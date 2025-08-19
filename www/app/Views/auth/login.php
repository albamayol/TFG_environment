<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Iniciar sesión</h1>

<?php if (session()->getFlashdata('error')): ?>
    <p style="color:red"><?= session()->getFlashdata('error') ?></p>
<?php endif; ?>

<form id="login-form" action="<?= site_url('/login') ?>" method="POST">
    <?= csrf_field() ?> <!-- keep for non-AJAX posts -->
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Contraseña</label>
    <input type="password" name="password" required>

    <input type="submit" value="Entrar">
</form>
<!-- Where we show AJAX responses -->
<div id="response" style="margin-top:1rem;"></div>

<p><a href="/signup">Crear cuenta</a></p>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/ajax.js') ?>"></script>
<?= $this->endSection() ?>
