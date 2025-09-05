<div class="project-card"
    data-project-id="<?= esc($project['id_project']) ?>"
    data-name="<?= esc($project['name']) ?>"
    data-description="<?= esc($project['description']) ?>"
    data-start-date="<?= esc($project['start_date'] ?? ($project['start_date'] ?? '-')) ?>"
    data-end-date="<?= esc($project['end_date'] ?? ($project['end_date'] ?? '-')) ?>"
    data-state="<?= esc($project['state']) ?>"
    >
    <div class="project-card-header">
        <strong class="project-name"><?= esc($project['name']) ?></strong>
        <span 
            class="badge badge-<?= esc(strtolower(str_replace(' ', '-', $project['state']))) ?>">
            <?= esc($project['state']) ?>
        </span>
    </div>
    <p class="project-description"><?= esc($project['description']) ?></p>
    <div class="project-dates">
        <p><strong>Start Date:</strong> <?= esc($project['start_date']) ?></p>
        <p><strong>End Date:</strong> <?= esc($project['end_date']) ?></p>
    </div>
    <?php 
    if($canChangeState): ?>
        <select class="state-select" data-id="<?= esc($project['id_project']) ?>" data-state="<?= esc($project['state']) ?>">
            <option value="To Begin" <?= $project['state'] === 'To Begin' ? 'selected' : '' ?>>To Begin</option>
            <option value="Active" <?= $project['state'] === 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="On Pause" <?= $project['state'] === 'On Pause' ? 'selected' : '' ?>>On Pause</option>
            <option value="Finished" <?= $project['state'] === 'Finished' ? 'selected' : '' ?>>Finished</option>
        </select>
    <?php endif; ?>
</div>