<?php
/**
 * View: Create Task
 *
 * Only users with roles Profile_Admin, Manager, or Head_of_Team can access this page.
 * 
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Create Task</h1>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<button type="button" class="btn btn-back" onclick="window.location.href='<?= site_url('/Tasks/MyDay') ?>'">
    Back
</button>

<form action="<?= site_url('/Tasks/store') ?>" method="post" id="createTaskForm">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name">Task Name</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
    </div>

    <div class="form-group">
        <label for="priority">Priority</label>
        <select id="priority" name="priority" class="form-control">
            <option value="Low">Low</option>
            <option value="Medium" selected>Medium</option>
            <option value="High">High</option>
            <option value="Urgent">Urgent</option>
        </select>
    </div>

    <div class="form-group">
        <label for="limit_date">Limit Date</label>
        <input type="datetime-local" id="limit_date" name="limit_date" class="form-control">
    </div>

    <div class="form-group">
        <label for="duration">Duration (HH:MM:SS)</label>
        <input type="time" id="duration" name="duration" class="form-control" step="1">
    </div>

    <div class="form-group">
        <label for="id_project">Assign Task to Project</label>
        <select id="id_project" name="id_project" class="form-control" data-users-url="<?= site_url('Tasks/usersForProject') ?>">
            <option value="">(no project)</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?= esc($project['id_project']) ?>">
                    <?= esc($project['name']) ?> - <?= esc($project['description']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">
            If left blank, the task will be treated as an individual task unless you assign it explicitly below.
        </small>
    </div>

    <div class="form-group">
        <label for="assigned_user">Assign Task to Worker</label>
        <select id="assigned_user" name="assigned_user" class="form-control">
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= esc($user['id_user']) ?>">
                    <?= esc($user['email']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">
            If left blank and do not choose “Individual Task”, the task will be assigned to yourself.
        </small>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="individual" name="individual" value="1" class="form-check-input">
        <label for="individual" class="form-check-label">Individual Task (assign to me)</label>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="simulated" name="simulated" value="1" class="form-check-input">
        <label for="simulated" class="form-check-label">Simulated?</label>
    </div>

    <button type="submit" class="btn btn-primary">Create Task</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const projectSelect  = document.getElementById('id_project');
    const userSelect     = document.getElementById('assigned_user');
    const individualCb   = document.getElementById('individual');
    const selfUserId     = '<?= esc($selfUserId) ?>';
    const usersBaseUrl   = projectSelect.dataset.usersUrl;

    function loadUsers(projectId) {
        const url = usersBaseUrl + (projectId ? '/' + projectId : '');
        // Fetch users via AJAX
        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })  .then(res => res.json())
            .then(data => {
                userSelect.innerHTML = '<option value="">-- Select User --</option>';
                data.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id_user;
                    opt.textContent = u.email || '';
                    userSelect.appendChild(opt);
                });
                if (![...userSelect.options].some(o => o.value === userSelect.value)) {
                    userSelect.value = '';
                }
            })
            .catch(() => {
            });
    }

    projectSelect.addEventListener('change', function () {
        if (!individualCb.checked) loadUsers(this.value);
    });

    individualCb.addEventListener('change', function () {
        userSelect.disabled = this.checked;
        if (this.checked) userSelect.value = '';
        else loadUsers(projectSelect.value);
    });

    // Init
    userSelect.disabled = individualCb.checked;
    if (!individualCb.checked) loadUsers(projectSelect.value);
});
</script>

<?= $this->endSection() ?>