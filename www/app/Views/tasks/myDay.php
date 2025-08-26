<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Today's Tasks</h1>
<h2>Great to see you again, <?= esc($userName) ?>!</h2>
<h2><?= esc($dayOfWeek) ?>, Let's start the Day.</h2>
<p>Completed: <span id="completionPct"><?= esc($percentage) ?></span>%</p>

<?php if ($canCreateTask): ?> 
  <a href="<?= site_url('/Tasks/createTask') ?>" class="btn btn-success mb-3">Create Task</a>
<?php endif; ?>

<?php if (empty($tasks)): ?>
  <p>All quiet here...</p>
<?php else: ?>
  <div class="tasks-container">
    <?php foreach ($tasks as $task): ?>
      <?= view('partials/task_card', ['task' => $task]) ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Shared Modal (now minimal; JS injects the body) -->
<div id="taskModal" class="modal" style="display:none;">
  <div class="modal-content">
    <button id="closeModalBtn" class="modal-close" aria-label="Close">&times;</button>
    <div id="taskModalBody"></div>
  </div>
</div>

<!-- Load the centralized modal logic -->
<script src="<?= base_url('assets/js/task-modal.js') ?>" defer></script>

<?= $this->endSection() ?>