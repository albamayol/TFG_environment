<?php
/**
 * My Tasks view.
 *
 * Shows all outstanding tasks for the current user grouped into columns:
 * today (due today), week (due this week), later (due after this week or no
 * limit date), and expired (past the deadline). Each column lists tasks
 * sorted by priority and due date. Users can change task state via a
 * drop‑down and click a card to see detailed information in a modal.
 *
 * Variables provided by the controller:
 * - $tasksToday (array)
 * - $tasksWeek (array)
 * - $tasksLater (array)
 * - $tasksExpired (array)
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Ensure task arrays are defined
$tasksToday   = isset($tasksToday)   ? $tasksToday   : [];
$tasksWeek    = isset($tasksWeek)    ? $tasksWeek    : [];
$tasksLater   = isset($tasksLater)   ? $tasksLater   : [];
$tasksExpired = isset($tasksExpired) ? $tasksExpired : [];

// Combine all tasks into a single array for JSON encoding in JavaScript
$allTasksForJs = array_merge($tasksToday, $tasksWeek, $tasksLater, $tasksExpired);
?>

<h1>My Tasks</h1>

<!--Button to createTask-->
<?php if ($canCreateTask): ?> 
    <a href="<?= site_url('/Tasks/createTask') ?>" class="btn btn-success mb-3">Create Task</a>
<?php endif; ?>

<div class="board">
    <div class="column" id="col-today">
        <h2>To‑Do Today</h2>
        <?php if (empty($tasksToday)): ?>
            <p>No tasks for today.</p>
        <?php else: ?>
            <?php foreach ($tasksToday as $task): ?>
                <?= view('partials/task_card', ['task' => $task]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="column" id="col-week">
        <h2>This Week</h2>
        <?php if (empty($tasksWeek)): ?>
            <p>No tasks for this week.</p>
        <?php else: ?>
            <?php foreach ($tasksWeek as $task): ?>
                <?= view('partials/task_card', ['task' => $task]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="column" id="col-later">
        <h2>Pending Later</h2>
        <?php if (empty($tasksLater)): ?>
            <p>No tasks pending later.</p>
        <?php else: ?>
            <?php foreach ($tasksLater as $task): ?>
                <?= view('partials/task_card', ['task' => $task]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="column" id="col-expired">
        <h2>Passed Deadline</h2>
        <?php if (empty($tasksExpired)): ?>
            <p>No overdue tasks.</p>
        <?php else: ?>
            <?php foreach ($tasksExpired as $task): ?>
                <?= view('partials/task_card', ['task' => $task]) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal reused from My Day view for task details -->
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

<!-- Minimal styles for board and cards; ideally move to external CSS -->
<style>
.board {
    display: flex;
    gap: 16px;
}
.column {
    flex: 1;
    background-color: #fff;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 10px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}
.column h2 {
    margin-top: 0;
    font-size: 1.2rem;
    color: #34495e;
    border-bottom: 1px solid #e0e4e8;
    padding-bottom: 8px;
}
/* Reuse card styles from My Day */
.task-card {
    background-color: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    padding: 12px;
    margin-bottom: 10px;
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
</style>

<!-- JavaScript for state update and modal (same as My Day) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('taskModal');
    const closeModal = () => { modal.style.display = 'none'; };
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target.id === 'taskModal') closeModal();
    });
    // Flatten all tasks arrays into one list for modal lookup
    /*const allTasks = [].concat(
        <?= json_encode($tasksToday) ?>,
        <?= json_encode($tasksWeek) ?>,
        <?= json_encode($tasksLater) ?>,
        <?= json_encode($tasksExpired) ?>
    );*/

    // Single JSON encode for all tasks
    const allTasks = <?= json_encode($allTasksForJs) ?>;

    // Click handler for task cards
    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.tagName.toLowerCase() === 'select') return;
            const id = this.getAttribute('data-task-id');
            const task = allTasks.find(t => t.id_task == id);
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
                const card = this.closest('.task-card');
                const badge = card.querySelector('.badge');
                badge.textContent = newState;
                badge.className = 'badge badge-' + newState.toLowerCase().replace(/\s+/g, '-');
            }).catch(() => {
                this.value = this.dataset.state;
            });
        });
    });
});
</script>

<?= $this->endSection() ?>