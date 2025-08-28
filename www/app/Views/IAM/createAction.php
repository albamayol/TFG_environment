<?php
/**
 * View: Create Action
 *
 * Only users with role Profile_Admin or Manager can access this page.
 * 
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Create Action</h1>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('/IAM/Actions/store') ?>" method="post" id="createActionForm">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="action-name">Action Name</label>
        <input type="text" id="action-name" name="action-name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="action-description">Action's Description</label>
        <textarea id="action-description" name="action-description" class="form-control" rows="4"></textarea>
    </div>

    <div class="form-group form-check">
        <label for="simulated" class="form-check-label">Simulated?</label>
        <input type="checkbox" id="simulated" name="simulated" value="1" class="form-check-input">
    </div>
    
    <button type="submit" class="btn btn-primary">Create Action</button>
</form>

<?= $this->endSection() ?>