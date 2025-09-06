<?php
/**
 * View: Create Project
 *
 * Only users with roles Profile_Admin, Manager
 *
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Create Project</h1>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('/Projects/store') ?>" method="post" id="createProjectForm">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name">Project Name</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
    </div>
    
    <div class="form-group">
        <label for="start_date">Starting Date</label>
        <input type="datetime-local" id="start_date" name="start_date" class="form-control">
    </div>

    <div class="form-group">
        <label for="end_date">Ending Date</label>
        <input type="datetime-local" id="end_date" name="end_date" class="form-control">
    </div>
    
    <!--Required -->
    <div class="form-group">
        <label>Assign Head of Team for the Project</label>
        <div class="hot-box">
            <?php if(!empty($hots)): ?>
                <?php foreach ($hots as $HOT): ?>
                    <?php $checked = (($HOT['id_user'] ?? $HOT['id_head_of_team']) === old('head-of-team', '')) ? 'checked' : ''; ?>
                    <div class="form-check hot-row">
                        <label for="hot-<?= esc($HOT['id_user']) ?>" class="form-check-label">
                            <?= esc($HOT['email'] ?? trim(($HOT['name'] ?? '') . ' ' . ($HOT['surnames'] ?? ''))) ?>
                        </label>
                        <input
                            type="radio"
                            id="hot-<?= esc($HOT['id_user']) ?>"
                            name="head-of-team"
                            value="<?= esc($HOT['id_user']) ?>"
                            class="form-check-input"
                            required
                            <?= $checked ?>
                        >
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No Heads of Team available. Please create one first.</p>
            <?php endif; ?>

        </div>
    </div>

    <!--Required -->
    <div class="form-group">
        <label>Assign Workers to the Project with their Roles</label>
        <div class="workers_and_roles-box">
            <?php if (!empty($workers)): ?>
                <?php $oldRoles = old('roles') ?? []; ?>
                <?php $oldWorkers = array_map('strval', (array) old('workers', [])); ?> <!-- helper-->
                <?php foreach ($workers as $worker): 
                    $isChecked = in_array($worker['id_user'], $oldWorkers, true) ? 'checked' : '';
                    $selectedRole = (string) ($oldRoles[$worker['id_user']] ?? '');
                    ?>
                    <div class="form-check worker-row">
                        <label for="worker-<?= esc($worker['id_user']) ?>" class="form-check-label">
                            <?= esc($worker['email']) ?? trim(($worker['name'] ?? '') . ' ' . ($worker['surnames'] ?? '')) ?>
                        </label>
                        <input
                            type="checkbox"
                            id="worker-<?= esc($worker['id_user']) ?>"
                            name="workers[]"
                            value="<?= esc($worker['id_user']) ?>"
                            class="form-check-input worker-cb"
                            required
                            <?= $isChecked ?>
                        >
                        <select
                            name="roles[<?= esc($worker['id_user']) ?>]"
                            id="role-<?= esc($worker['id_user']) ?>"
                            class="form-control role-select"
                            <?= $isChecked ? '' : 'disabled' ?>
                        >
                            <option value="">-- Select role --</option>
                            <?php foreach ($roles as $role): ?>
                                <?php
                                    $ridRaw = $role['id_role'];
                                    $rname  = $role['name'] ?? $role['role_name'] ?? ('Role #'.$ridRaw);
                                    $sel    = ((string)$ridRaw === (string)$selectedRole) ? 'selected' : '';
                                ?>
                                <option value="<?= esc($ridRaw) ?>" <?= $sel ?>><?= esc($rname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No workers available.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" id="simulated" name="simulated" value="1" class="form-check-input">
        <label for="simulated" class="form-check-label">Simulated?</label>
    </div>

    <button type="button" class="btn btn-back" onclick="window.location.href='<?= site_url('/Projects/MyProjects') ?>'">
        Back
    </button>
    <button type="submit" class="btn btn-primary">Create Project</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.worker-row').forEach(function (row) {
        const cb = row.querySelector('.worker-cb');
        const sel = row.querySelector('.role-select');

        function sync() {
            if (cb.checked) {
                sel.disabled = false;
                sel.setAttribute('required', 'required'); //require a role if worker is selected
            } else {
                sel.disabled = true;
                sel.removeAttribute('required');
                sel.value = ''; //reset value for role
            }
        }

        cb.addEventListener('change', sync);
        sync(); //init state
    });
});
</script>

<?= $this->endSection() ?>