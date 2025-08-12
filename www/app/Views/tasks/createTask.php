<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Nueva tarea</h1>

<?php if(session()->getFlashdata('errors')): ?>
    <ul>
        <?php foreach(session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="/Tasks/store">
    <label>Nombre</label>
    <input type="text" name="name" value="<?= old('name') ?>" required>

    <label>Descripción</label>
    <textarea name="description"><?= old('description') ?></textarea>

    <label>Fecha límite</label>
    <input type="datetime-local" name="limit_date" value="<?= old('limit_date') ?>">

    <label>Estado</label>
    <select name="state">
        <option>To Do</option>
        <option>In Progress</option>
        <option>Done</option>
    </select>

    <label>Prioridad</label>
    <select name="priority">
        <option>Low</option>
        <option selected>Medium</option>
        <option>High</option>
        <option>Urgent</option>
    </select>

    <button type="submit">Guardar</button>
</form>
<?= $this->endSection() ?>
