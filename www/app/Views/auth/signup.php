<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Registro</h1>

<?php if(session()->getFlashdata('errors')): ?>
    <ul>
    <?php foreach(session()->getFlashdata('errors') as $error): ?>
        <li><?= esc($error) ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>

<form method="post" action="/signup">
    <label>Nombre</label>
    <input type="text" name="name" value="<?= old('name') ?>" required>

    <label>Apellidos</label>
    <input type="text" name="surnames" value="<?= old('surnames') ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= old('email') ?>" required>

    <label>Contraseña</label>
    <input type="password" name="password" required>

    <button type="submit">Registrarse</button>
</form>
<?= $this->endSection() ?>