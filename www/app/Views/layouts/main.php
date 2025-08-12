<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Gestor') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
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
    <main>
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
