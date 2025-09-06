<?php
  $meEmail    = trim((string)($currentUserEmail ?? ''));
  $ownerEmail = trim((string)($task['person_of_interest'] ?? ''));
  $isOwner    = ($ownerEmail !== '') && (strcasecmp($ownerEmail, $meEmail) === 0);
  $isSim = (int)($task['simulated'] ?? 0) === 1;
?>

<div
    class="task-card <?= $isSim ? 'is-simulated' : '' ?>"
    data-task-id="<?= esc($task['id_task']) ?>"
    data-name="<?= esc($task['name']) ?>"
    data-description="<?= esc($task['description']) ?>"
    data-duration="<?= esc($task['duration']) ?: '-' ?>"
    data-priority="<?= esc($task['priority']) ?>"
    data-limit-date="<?= esc($task['limit_date_display'] ?? ($task['limit_date'] ?? '-')) ?>"
    data-state="<?= esc($task['state']) ?>"
    data-person="<?= esc($task['person'] ?? ($task['person_of_interest'] ?? '-')) ?>"
    data-origin="<?= esc($task['origin_of_task']) ?>"
    data-simulated="<?= $isSim ? '1' : '0' ?>"

    data-can-delete="<?= $isOwner ? '1' : '0' ?>"
    data-delete-url="<?= site_url('Tasks/delete/' . $task['id_task']) ?>"
>
    <div class="task-header">
        <strong><?= esc($task['name']) ?></strong>
        <span
            class="badge badge-<?= esc(strtolower(str_replace(' ', '-', $task['state']))) ?> state-badge">
            <?= esc($task['state']) ?>
        </span>
    </div>
    <span class="badge badge-<?= esc(strtolower(str_replace(' ', '-', $task['priority']))) ?>">
        <?= esc($task['priority']) ?>
    </span>
    <?php if (!empty($task['description'])): ?>
        <p><?= esc($task['description']) ?></p>
    <?php endif; ?>
    <p><strong>Duration:</strong> <?= esc($task['duration']) ?: '-' ?></p>
    <p><strong>Limit date:</strong> <?= esc($task['limit_date_display'] ?? ($task['limit_date'] ?? '-')) ?></p>
    <p><strong>Origin:</strong> <?= esc($task['origin_of_task']) ?></p>
    <select class="state-select" data-id="<?= esc($task['id_task']) ?>" data-state="<?= esc($task['state']) ?>">
        <option value="To Do" <?= $task['state'] === 'To Do' ? 'selected' : '' ?>>To Do</option>
        <option value="In Progress" <?= $task['state'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="Done" <?= $task['state'] === 'Done' ? 'selected' : '' ?>>Done</option>
    </select>
</div>