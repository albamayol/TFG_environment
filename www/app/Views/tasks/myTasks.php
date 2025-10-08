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
<div class="header-area">
  <div class="header-content">
    <div class="header-info">
      <div>
        <h1>My Tasks</h1>
        <h2>Stay organized and productive!</h2>
      </div>
    </div>
    <?php if ($canCreateTask): ?> 
      <a href="<?= site_url('/Tasks/createTask') ?>" class="btn-create-task">Create Task</a>
    <?php endif; ?>
  </div>
</div>

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

  .column > *:not(:last-child) {
    margin-bottom: 18px;
  }
  .column h2 {
    margin-top: 0;
    font-size: 1.2rem;
    color: #34495e;
    border-bottom: 1px solid #e0e4e8;
    padding-bottom: 8px;
  }

  .header-area {
  background: linear-gradient(120deg, #f7faff 70%, #e7edf7 100%);
  border-radius: 18px;
  padding: 32px 32px 26px 32px;
  margin-bottom: 38px;
  box-shadow: 0 6px 24px rgba(79,140,255,0.09);
  display: flex;
  flex-direction: column;
  align-items: stretch;
  position: relative;
}
.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 18px;
}
.header-info {
  display: flex;
  align-items: center;
  gap: 18px;
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
.header-area h1 {
  margin-bottom: 0.3rem;
  font-size: 2.2rem;
  color: #4f8cff;
  letter-spacing: .5px;
  font-weight: 700;
}
.header-area h2 {
  margin: 0 0 0.3rem 0;
  font-size: 1.1rem;
  color: #11306aff;
  font-weight: 400;
}

.tasks-container {
  display: grid;
  grid-template-columns: repeat(4, minmax(220px, 1fr));
  gap: 22px;
  max-height: 68vh;
  overflow-y: auto;
  padding-bottom: 12px;
  margin-bottom: 18px;
  margin-top: 32px;
  background: rgba(255,255,255,0.01);
  border-radius: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.tasks-section-header {
  grid-column: 1 / -1;
  font-size: 1.1rem;
  font-weight: 700;
  color: #4f8cff;
  margin: 18px 0 6px 0;
  padding-left: 6px;
}
@media (max-width: 1200px) {
  .tasks-container { grid-template-columns: repeat(3, minmax(220px, 1fr)); }
}
@media (max-width: 900px) {
  .tasks-container { grid-template-columns: repeat(2, minmax(220px, 1fr)); }
}
@media (max-width: 600px) {
  .header-content { flex-direction: column; align-items: flex-start; }
  .tasks-container { grid-template-columns: 1fr; }
  .btn-create-task { width: 100%; text-align: center; }
}
</style>

<script src="<?= base_url('assets/js/task-modal.js') ?>" defer></script>

<?= $this->endSection() ?>