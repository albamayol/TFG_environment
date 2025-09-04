<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <h1>My Projects</h1>

    <div class="projects-container">
        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <div class="project-name"><?= esc($project['name']) ?></div>
                    <div class="project-description"><?= esc($project['description']) ?></div>
                    <div class="project-state"><strong>State:</strong> <?= esc($project['state']) ?></div>
                    <div class="project-dates">
                        <span><strong>Start Date:</strong> <?= esc($project['start_date']) ?></span><br>
                        <span><strong>End Date:</strong> <?= esc($project['end_date']) ?></span>
                    </div>
                    <div class="project-simulated">
                        <strong>Simulated:</strong>
                        <?= !empty($project['simulated']) ? '<span style="color:#1a418c;">Yes</span>' : '<span style="color:#b02a37;">No</span>' ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div>All quiet here...</div>
        <?php endif; ?>
        <?php if ($canCreateProject): ?> 
            <a href="<?= site_url('/Projects/createProject') ?>" class="btn btn-success mb-3">Create Project</a>
        <?php endif; ?>
    </div>

    <style>
        .projects-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 600px;
            margin: auto;
        }
        .project-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 20px;
        }
        .project-name {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .project-description {
            margin-bottom: 8px;
            color: #555;
        }
        .project-end-date {
            font-size: 0.95em;
            color: #888;
        }
    </style>
<?= $this->endSection() ?>