<?php
/**
 * View: Create Role
 *
 * Only users with role Profile_Admin or Manager can access this page.
 * 
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Create Role</h1>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('/IAM/Roles/store') ?>" method="post" id="createRoleForm">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="role-name">Role Name</label>
        <input type="text" id="role-name" name="role-name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="role-description">Description</label>
        <textarea id="role-description" name="role-description" class="form-control" rows="4"></textarea>
    </div>

    <div class="form-group">
        <label for="role-skills">Skills for this Role</label>
        <input type="text" id="role-skills" name="role-skills" class="form-control">
    </div>

    <div class="form-group">
        <label>Assign Actions to Role</label>
        <div class="actions-box">
            <?php foreach ($actions as $action): ?>
                <div class="form-check action-row">
                    <label for="action-<?= esc($action['id_actions']) ?>" class="form-check-label">
                        <?= esc($action['name']) ?> - <?= esc($action['description']) ?>
                    </label>
                    <input
                        type="checkbox"
                        id="action-<?= esc($action['id_actions']) ?>"
                        name="role-actions[]"
                        value="<?= esc($action['id_actions']) ?>"
                        class="form-check-input"
                    >
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group form-check">
        <label for="simulated" class="form-check-label">Simulated?</label>
        <input type="checkbox" id="simulated" name="simulated" value="1" class="form-check-input">
    </div>

    <button type="submit" class="btn btn-primary">Create Role</button>
</form>

<style>
.actions-box {
    background: #f7f9fc;
    border: 1px solid #dbe2ea;
    border-radius: 8px;
    padding: 16px 18px;
    margin-bottom: 18px;
    color: #11306aff;
}

.action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #eaeaea;
}

.action-row:last-child {
    border-bottom: none;
}

.form-check-label {
    margin: 0;
    flex: 1;
}

.action-row .form-check-label {
    color: #11306aff; 
}

.form-check-input {
    margin-left: 18px;
}
</style>

<?= $this->endSection() ?>