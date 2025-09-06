<?php
/**
 * My Tasks view.
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$tasksToday   = isset($tasksToday)   ? $tasksToday   : [];
$tasksWeek    = isset($tasksWeek)    ? $tasksWeek    : [];
$tasksLater   = isset($tasksLater)   ? $tasksLater   : [];
$tasksExpired = isset($tasksExpired) ? $tasksExpired : [];
?>

<h1>My Tasks</h1>

<?php if ($canCreateTask): ?> 
  <a href="<?= site_url('/Tasks/createTask') ?>" class="btn btn-success mb-3">Create Task</a>
<?php endif; ?>

<div class="board">
  <div class="column" id="col-today">
    <h2>To-Do Today</h2>
    <?php if (empty($tasksToday)): ?>
      <p>No tasks for today.</p>
    <?php else: foreach ($tasksToday as $task): ?>
      <?= view('partials/task_card', ['task' => $task]) ?>
    <?php endforeach; endif; ?>
  </div>

  <div class="column" id="col-week">
    <h2>This Week</h2>
    <?php if (empty($tasksWeek)): ?>
      <p>No tasks for this week.</p>
    <?php else: foreach ($tasksWeek as $task): ?>
      <?= view('partials/task_card', ['task' => $task]) ?>
    <?php endforeach; endif; ?>
  </div>

  <div class="column" id="col-later">
    <h2>Pending Later</h2>
    <?php if (empty($tasksLater)): ?>
      <p>No tasks pending later.</p>
    <?php else: foreach ($tasksLater as $task): ?>
      <?= view('partials/task_card', ['task' => $task]) ?>
    <?php endforeach; endif; ?>
  </div>

  <div class="column" id="col-expired">
    <h2>Passed Deadline</h2>
    <?php if (empty($tasksExpired)): ?>
      <p>No overdue tasks.</p>
    <?php else: foreach ($tasksExpired as $task): ?>
      <?= view('partials/task_card', ['task' => $task]) ?>
    <?php endforeach; endif; ?>
  </div>
</div>

<!-- Shared Modal -->
<div id="taskModal" class="modal" style="display:none;">
  <div class="modal-content">
    <button id="closeModalBtn" class="modal-close" aria-label="Close">&times;</button>
    <div id="taskModalBody"></div>
  </div>
</div>

<style>
  .board {
    display: flex;
    gap: 16px;
  }
  .column {
    flex: 1;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 10px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
  }
  .column h2 {
    margin-top: 0;
    font-size: 1.2rem;
    color: #34495e;
    border-bottom: 1px solid #e0e4e8;
    padding-bottom: 8px;
  }
</style>

<script src="<?= base_url('assets/js/task-modal.js') ?>" defer></script>

<?= $this->endSection() ?>