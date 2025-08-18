<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Pasmo') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Expose CSRF to JS (CI4 helpers) -->
    <meta name="csrf-token-name" content="<?= esc(csrf_token()) ?>">
    <meta name="csrf-token" content="<?= esc(csrf_hash()) ?>">
</head>
<body>
    <?php if (session()->get('logged_in')): ?>
        <nav>
            <?php if (session()->get('role_name') === 'Profile_Admin'): ?>
                <a href="/IAM/Users">Usuarios</a>
                <a href="/IAM/Roles">Roles</a>
                <a href="/IAM/Actions">Acciones</a>
            <?php endif; ?>
            <a href="/Tasks/MyDay">Mis Tareas</a>
            <a href="/Projects/MyProjects">Mis Proyectos</a>
            <a href="/logout">Cerrar sesión</a>
        </nav>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- jQuery-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <!-- Per-page scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
