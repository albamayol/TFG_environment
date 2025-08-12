<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Iniciar sesión</h1>

<?php if (session()->getFlashdata('error')): ?>
    <p style="color:red"><?= session()->getFlashdata('error') ?></p>
<?php endif; ?>

<form method="post" action="/login">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Contraseña</label>
    <input type="password" name="password" required>

    <button type="submit">Entrar</button>
</form>
<?= $this->endSection() ?>
