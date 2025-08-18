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
    <!-- CSRF protection -->
    <?= csrf_field() ?>

    <label>Name</label>
    <input type="text" name="name" value="<?= set_value('name') ?>" required>
    <?= \Config\Services::validation()->showError('name') ?>
    
    <label>Surnames</label>
    <input type="text" name="surnames" value="<?= set_value('surnames') ?>" required>
    <?= \Config\Services::validation()->showError('surnames') ?>

    <label>Email</label>
    <input type="email" name="email" value="<?= set_value('email') ?>" required>
    <?= \Config\Services::validation()->showError('email') ?>
    
    <label>Password</label>
    <h3>Password must include:</h3>
    <ul>
        <li>At least 12 characters</li>
        <li>At least one lowercase letter</li>
        <li>At least one uppercase letter</li>
        <li>At least one number</li>
        <li>At least one special character</li>
        <li>No white spaces</li>
    </ul>
    <input type="password" name="password" required min_length="12" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{12,}$">
    <?= \Config\Services::validation()->showError('password') ?>

    <label>Repeat Password</label>
    <input type="password" name="repeated_password" required>
    <?= \Config\Services::validation()->showError('repeated_password') ?>

    <label>Birthdate</label>
    <input type="date" name="birthdate" required lang="es-ES">
    <?= \Config\Services::validation()->showError('birthdate') ?>

    <label>Address</label>
    <input type="text" name="address" value="<?= set_value('address') ?>" required>
    <?= \Config\Services::validation()->showError('address') ?>

    <label>DNI/NIE</label>
    <input type="text" name="dni_nie">
    <?= \Config\Services::validation()->showError('dni_nie') ?>

    <label>Telephone</label>
    <input type="tel" name="telephone" placeholder="e.g. +34123456789" required>
    <?= \Config\Services::validation()->showError('telephone') ?>

    <label>Soft Skills</label>
    <input type="text" name="soft_skills" value="<?= set_value('soft_skills') ?>">
    <?= \Config\Services::validation()->showError('soft_skills') ?>

    <label>Technical Skills</label>
    <input type="text" name="technical_skills" value="<?= set_value('technical_skills') ?>">
    <?= \Config\Services::validation()->showError('technical_skills') ?>
    
    <button type="submit">Registrarse</button>
</form>
<?= $this->endSection() ?>