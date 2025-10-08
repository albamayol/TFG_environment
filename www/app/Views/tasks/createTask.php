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

<div class="create-task-page">
  <div class="create-task-card">
    <div class="create-task-header">
      <div>
        <h1 class="create-task-title">Create Task</h1>
      </div>
      <a href="<?= site_url('/Tasks/MyDay') ?>" class="btn btn-back create-task-back">Back</a>
    </div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger" style="margin-bottom:18px;">
    <ul style="margin:0 0 0 18px;">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
        <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('/Tasks/store') ?>" method="post" id="createTaskForm">
    <?= csrf_field() ?>

    <div class="form-row">
        <div class="form-group">
          <label for="name" class="label">Task Name</label>
          <input type="text" id="name" name="name" class="input" required placeholder="Task title">
        </div>
        <div class="form-group">
          <label for="priority" class="label">Priority</label>
          <select id="priority" name="priority" class="input">
            <option value="Low" class="badge badge-low">Low</option>
            <option value="Medium" class="badge badge-medium" selected>Medium</option>
            <option value="High" class="badge badge-high">High</option>
            <option value="Urgent" class="badge badge-urgent">Urgent</option>
          </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
          <label for="description" class="label">Description</label>
          <textarea id="description" name="description" class="input" rows="4" placeholder="Describe the task..."></textarea>
        </div>
        <div class="form-group">
          <label for="limit_date" class="label">Limit Date</label>
          <input type="datetime-local" id="limit_date" name="limit_date" class="input">
        </div>
        <div class="form-group">
          <label for="duration" class="label">Duration (HH:MM:SS)</label>
          <input type="time" id="duration" name="duration" class="input" step="1">
        </div>  
    </div>

    <div class="form-row">
        <div class="form-group" style="flex:1;">
            <label for="id_project" class="label">Assign to Project</label>
            <select id="id_project" name="id_project" class="input" data-users-url="<?= site_url('Tasks/usersForProject') ?>">
                <option value="">(no project)</option>
                <?php foreach ($projects as $project): ?>
                <option value="<?= esc($project['id_project']) ?>">
                    <?= esc($project['name']) ?> - <?= esc($project['description']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <div class="hint">If left blank, the task will be treated as an individual task unless you assign it explicitly below.</div>
        </div>
        <div class="form-group" style="flex:1;">
            <label for="assigned_user" class="label">Assign to Worker</label>
            <select id="assigned_user" name="assigned_user" class="input">
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                <option value="<?= esc($user['id_user']) ?>">
                    <?= esc($user['email']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <div class="hint">If left blank and do not choose “Individual Task”, the task will be assigned to yourself.</div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-check">
            <input type="checkbox" id="individual" name="individual" value="1" class="form-check-input">
            <label for="individual" class="form-check-label">Individual Task (assign to me)</label>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" id="simulated" name="simulated" value="1" class="form-check-input">
            <label for="simulated" class="form-check-label">Simulated?</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Create Task</button>
</form>
</div>
</div>

<style>
.create-task-page {
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 36px 10px;
  background: none;
}
.create-task-card {
  width: 100%;
  max-width: 680px;
  background: var(--card);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 36px 32px 28px 32px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.create-task-header {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-bottom: 18px;
  justify-content: space-between;
}
.header-icon {
  background: #e7edf7;
  border-radius: 50%;
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(79,140,255,0.07);
  margin-right: 10px;
}
.create-task-title {
  margin: 0 0 2px 0;
  font-size: 2rem;
  color: var(--primary);
  font-weight: 700;
  letter-spacing: .5px;
}
.create-task-back {
  margin-left: auto;
  margin-right: 0;
  min-width: 90px;
  padding: 10px 18px;
}
.form-row {
  display: flex;
  gap: 18px;
  margin-bottom: 0;
  flex-wrap: wrap;
}
.form-group {
  /*display: flex;*/
  flex-direction: column;
  flex: 1 1 240px;
  min-width: 240px;
  margin-bottom: 18px;
}
.form-check {
  flex-direction: row;
  align-items: center;
  margin-bottom: 0;
  gap: 8px;
}
.form-check-input {
  margin-right: 8px;
}
@media (max-width: 900px) {
  .create-task-card { padding: 22px 10px; }
  .form-row { flex-direction: column; gap: 0; }
  .form-group { min-width: 0; }
  .create-task-header { flex-direction: column; align-items: flex-start; gap: 10px; }
}
</style>

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