<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>My Projects</h1>

<div class="projects-container">
    <?php if (!empty($projects)): ?>
        <?php foreach ($projects as $project): ?>
            <?= view('partials/project_card', ['project' => $project]) ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div>All quiet here...</div>
    <?php endif; ?>
    <?php if ($canCreateProject): ?> 
        <a href="<?= site_url('/Projects/createProject') ?>" class="btn btn-success mb-3">Create Project</a>
    <?php endif; ?>

    <!-- Shared Modal (now minimal; JS injects the body) -->
    <div id="projectModal" class="modal" style="display:none;">
        <div class="modal-content">
            <button id="closeModalBtn" class="modal-close" aria-label="Close">&times;</button>
            <div id="projectModalBody"></div>
        </div>
    </div>
</div>

<style>
    .projects-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-width: 600px;
        margin: auto;
    }
</style>

<script src="<?= base_url('assets/js/project-modal.js') ?>" defer></script>

<?= $this->endSection() ?>