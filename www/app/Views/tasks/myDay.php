<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="header-area">
  <div class="header-content">
    <div class="header-info">
      <div>
        <h1>Today's Tasks</h1>
        <h2>Great to see you again, <?= esc($userName) ?>!</h2>
        <h2><?= esc($dayOfWeek) ?>, Let's start the Day.</h2>
        <p>Completed: <span id="completionPct"><?= esc($percentage) ?></span>%</p>
      </div>
    </div>
    <?php if ($canCreateTask): ?> 
      <a href="<?= site_url('/Tasks/createTask') ?>" class="btn-create-task">Create Task</a>
    <?php endif; ?>
  </div>
</div>

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

<style>

.tasks-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 12px;
  margin-top: 18px;
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
.header-area p {
  font-size: 1.1rem;
  color: #3bbe6bff;
  font-weight: bold;
  margin-bottom: 0;
}
#completionPct {
  font-weight: bold;
  color: #3bbe6bff;
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