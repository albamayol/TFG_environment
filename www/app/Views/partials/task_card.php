<?php
/**
 * Partial view: task card.
 *
 * Accepts a `$task` array and renders a card element. Used by both
 * MyDay and MyTasks views to avoid duplication.
 *
 * The parent view should define CSS for .task-card, .task-header,
 * .badge-* classes, and .state-select.
 */
?>
<div class="task-card" data-task-id="<?= esc($task['id_task']) ?>">
    <div class="task-header">
        <strong><?= esc($task['name']) ?></strong>
        <span class="badge badge-<?= esc(strtolower(str_replace(' ', '-', $task['state']))) ?>">
            <?= esc($task['state']) ?>
        </span>
    </div>
    <span class="badge badge-<?= esc(strtolower(str_replace(' ', '-', $task['priority']))) ?>">
        <?= esc($task['priority']) ?>
    </span>
    <p><?= esc($task['description']) ?></p>
    <p><strong>Duration:</strong> <?= esc($task['duration']) ?: '-' ?></p>
    <p><strong>Limit date:</strong> <?= esc($task['limit_date_display'] ?? ($task['limit_date'] ?? '-')) ?></p>
    <p><strong>Origin:</strong> <?= esc($task['origin_of_task']) ?></p>
    <select class="state-select" data-id="<?= esc($task['id_task']) ?>" data-state="<?= esc($task['state'])?>">
        <option value="To Do" <?= $task['state'] === 'To Do' ? 'selected' : '' ?>>To Do</option>
        <option value="In Progress" <?= $task['state'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="Done" <?= $task['state'] === 'Done' ? 'selected' : '' ?>>Done</option>
    </select>
</div>
