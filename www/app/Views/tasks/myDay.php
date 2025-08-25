<?php
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Today's Tasks</h1>
<h2>Great to see you again, <?= esc($userName) ?>!</h2>
<h2><?= esc($dayOfWeek) ?>, let's start the Day.</h2>
<p>Completed: <?= esc($percentage) ?>%</p>


<!--Button to createTask-->
<?php if ($canCreateTask): ?> 
    <a href="<?= site_url('/Tasks/createTask') ?>" class="btn btn-success mb-3">Create Task</a>
<?php endif; ?>

<?php if (empty($tasks)): ?>
    <p>All quiet here...</p>
<?php else: ?>
    <div class="tasks-container">
        <?php foreach ($tasks as $task): ?>
            <?= view('partials/task_card', ['task' => $task]) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal for detailed task view -->
<div id="taskModal" class="modal" style="display:none;">
    <div class="modal-content">
        <button id="closeModalBtn" class="modal-close">&times;</button>
        <h2 id="modalTitle"></h2>
        <p id="modalDescription"></p>
        <p><strong>Duration:</strong> <span id="modalDuration"></span></p>
        <p><strong>Priority:</strong> <span id="modalPriority"></span></p>
        <p><strong>Limit date:</strong> <span id="modalLimitDate"></span></p>
        <p><strong>State:</strong> <span id="modalState"></span></p>
        <p><strong>Person of interest:</strong> <span id="modalPerson"></span></p>
        <p><strong>Origin:</strong> <span id="modalOrigin"></span></p>
    </div>
</div>

<style>
.tasks-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.task-card {
    background-color: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    padding: 12px;
    cursor: pointer;
}
.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.badge {
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #fff;
}
.badge-to-do { background-color: #95a5a6; }
.badge-in-progress { background-color: #f1c40f; }
.badge-done { background-color: #2ecc71; }

.badge-low    { background-color: #3498db; } /* Blue for Low */
.badge-medium { background-color: #9b59b6; } /* Purple for Medium */
.badge-high   { background-color: #e67e22; } /* Orange for High */
.badge-urgent { background-color: #e74c3c; } /* Red for Urgent */

.state-select {
    margin-top: 8px;
    width: 100%;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 0.9rem;
}
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 6px;
    width: 90%;
    max-width: 500px;
    position: relative;
}
.modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    border: none;
    background: transparent;
    font-size: 1.5rem;
    line-height: 1;
    cursor: pointer;
    color: #999;
}
</style>

<!-- JavaScript to handle modal and state changes -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('taskModal');
    const closeModal = () => { modal.style.display = 'none'; };
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target.id === 'taskModal') closeModal();
    });

    // Handle card click: populate modal
    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('click', function (e) {
            // Prevent opening when interacting with select
            if (e.target.tagName.toLowerCase() === 'select') return;
            const id = this.getAttribute('data-task-id');
            // Get task data from data attributes or fetch additional details via AJAX if needed
            const task = <?= json_encode($tasks) ?>.find(t => t.id_task == id);
            if (task) {
                document.getElementById('modalTitle').textContent = task.name;
                document.getElementById('modalDescription').textContent = task.description || '';
                document.getElementById('modalDuration').textContent = task.duration || '-';
                document.getElementById('modalPriority').textContent = task.priority || '-';
                document.getElementById('modalLimitDate').textContent = task.limit_date_display || '-';
                document.getElementById('modalState').textContent = task.state;
                document.getElementById('modalPerson').textContent = task.person_of_interest || '-';
                document.getElementById('modalOrigin').textContent = task.origin_of_task;
                modal.style.display = 'flex';
            }
        });
    });

    // Handle state change
    document.querySelectorAll('.state-select').forEach(select => {
        select.addEventListener('change', function (e) {
            const newState = e.target.value;
            const id = this.getAttribute('data-id');
            fetch(`/tasks/updateState/` + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ state: newState })
            }).then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            }).then(() => {
                // Update badge text and class
                const card = this.closest('.task-card');
                const badge = card.querySelector('.badge');
                badge.textContent = newState;
                badge.className = 'badge badge-' + newState.toLowerCase().replace(/\s+/g, '-');
            }).catch(() => {
                // revert selection on error
                this.value = this.dataset.state;
            });
        });
    });
});
</script>

<?= $this->endSection() ?>