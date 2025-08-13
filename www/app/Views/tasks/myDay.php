<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Tareas de hoy (<?= esc($today) ?>)</h1>
<p>Completadas: <?= esc($percent) ?>%</p>

<?php if(empty($tasks)): ?>
    <p>No tienes tareas para hoy.</p>
<?php else: ?>
    <ul>
    <?php foreach($tasks as $task): ?>
        <li><?= esc($task['name']) ?> - <?= esc($task['state']) ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>