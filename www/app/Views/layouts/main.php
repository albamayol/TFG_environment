<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Pasmo') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . time()) ?>">
    <!-- Expose CSRF to JS (CI4 helpers) -->
    <meta name="csrf-token-name" content="<?= esc(csrf_token()) ?>">
    <meta name="csrf-token" content="<?= esc(csrf_hash()) ?>">
    <!-- Assets base + direct URL to the trash icon (with cache-busting) -->
    <meta name="assets-url" content="<?= rtrim(base_url('assets'), '/') ?>">

</head>

<!-- Script to detect user timezone and send it to the server -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tzNow = Intl.DateTimeFormat().resolvedOptions().timeZone;
  const cookieMatch = document.cookie.match(/(?:^|;)\s*user_timezone=([^;]+)/);
  const tzCookie = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;

  // If first time OR timezone changed (travel/DST), (re)post it
  if (tzNow && tzNow !== tzCookie) {
    const tokenNameMeta = document.querySelector('meta[name="csrf-token-name"]');
    const tokenMeta     = document.querySelector('meta[name="csrf-token"]');
    const tokenName     = tokenNameMeta ? tokenNameMeta.content : null;
    const tokenValue    = tokenMeta ? tokenMeta.content : null;

    const body = { timezone: tzNow };
    if (tokenName && tokenValue) body[tokenName] = tokenValue; // CI4 expects token in payload

    fetch('/timezone/set-timezone', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(body)
    })
    .then(r => r.ok ? r.json().catch(()=> ({})) : Promise.reject())
    .then(json => {
      // Persist for a day to avoid spamming; update if it changes later
      document.cookie = 'user_timezone=' + encodeURIComponent(tzNow) + '; path=/; max-age=86400';
      // Optional: if your endpoint returns a refreshed CSRF token, update the meta
      if (json && json.csrf && json.csrf.name && json.csrf.hash) {
        if (tokenNameMeta) tokenNameMeta.content = json.csrf.name;
        if (tokenMeta)     tokenMeta.content     = json.csrf.hash;
      }
    })
    .catch(() => { /* non-fatal: app will just fallback to UTC */ });
  }
});
</script>

<body>
    <?php if (session()->get('logged_in')): ?>
        <nav>
            <a href="/Tasks/MyDay">My Day</a>
            <a href="/Tasks/MyTasks">My Tasks</a>
            <a href="/Projects/MyProjects">My Projects</a>
            <?php if (session()->get('role_name') === 'Profile_Admin' || session()->get('role_name') === 'Manager'): ?>
              <details class="iam-details">
                <summary class="iam-link">IAM</summary>
                  <a href="/IAM/Users">Users</a>
                  <a href="/IAM/Roles">Roles</a>
                  <a href="/IAM/Actions">Actions</a>
            </details>
            <?php endif; ?>
            <a href="/Profile">Profile</a>
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
    <?= $this->renderSection('scripts') ?>
</body>
<Footer>
    <p style="text-align: center; font-size: small; color: gray; margin-top: 20px;">
        &copy; <?= date('Y') ?> Pasmo. All rights reserved.
    </p>
    <small style="opacity:.6">tz: <?= esc(session('user_timezone') ?? 'UTC') ?></small>
</Footer>
</html>
