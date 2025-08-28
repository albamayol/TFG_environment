<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <h1>My Projects</h1>

    <div class="projects-container">
        <!-- Example project cards. Replace with PHP loop for dynamic content. -->
        <div class="project-card">
            <div class="project-name">Project Alpha</div>
            <div class="project-description">A web application for managing tasks efficiently.</div>
            <div class="project-end-date">End Date: 2024-09-30</div>
        </div>
        <div class="project-card">
            <div class="project-name">Project Beta</div>
            <div class="project-description">Mobile app for tracking fitness activities.</div>
            <div class="project-end-date">End Date: 2024-12-15</div>
        </div>
        <div class="project-card">
            <div class="project-name">Project Beta</div>
            <div class="project-description">Mobile app for tracking fitness activities.</div>
            <div class="project-end-date">End Date: 2024-12-15</div>
        </div>
        <div class="project-card">
            <div class="project-name">Project Beta</div>
            <div class="project-description">Mobile app for tracking fitness activities.</div>
            <div class="project-end-date">End Date: 2024-12-15</div>
        </div>
        <div class="project-card">
            <div class="project-name">Project Beta</div>
            <div class="project-description">Mobile app for tracking fitness activities.</div>
            <div class="project-end-date">End Date: 2024-12-15</div>
        </div>
        <!-- End example cards -->
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