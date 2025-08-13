<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Mis Tareas</h1>

<?php if(empty($tasks)): ?>
    <p>No tienes tareas asignadas.</p>
<?php else: ?>
    <ul>
    <?php foreach($tasks as $task): ?>
        <li><a href="/Tasks/<?= esc($task['id_task']) ?>"><?= esc($task['name']) ?></a> - <?= esc($task['state']) ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>
<?= $this->endSection() ?>