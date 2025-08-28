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
        <label for="role-actions">Assign Actions to Role</label>
        <select id="role-actions" name="role-actions" class="form-control">
            <?php foreach ($actions as $action): ?>
                <option value="<?= esc($action['id_action']) ?>">
                    <?= esc($action['name']) ?> - <?= esc($action['description']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">
            If left blank, the task will be treated as an individual task unless you assign it explicitly below.
        </small>
    </div>

    <button type="submit" class="btn btn-primary">Create Role</button>
</form>

<?= $this->endSection() ?>