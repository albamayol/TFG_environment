<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="auth-page">
  <div class="auth-card">
    <h1 class="auth-title">Log In</h1>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <!-- AJAX response area -->
    <div id="response" class="alert-space" aria-live="polite"></div>

    <form id="login-form" action="<?= site_url('/login') ?>" method="POST" novalidate>
      <?= csrf_field() ?>

      <div class="field">
        <label for="email" class="label">Email</label>
        <input
          id="email"
          class="input"
          type="email"
          name="email"
          inputmode="email"
          autocomplete="username"
          required
          autofocus
          placeholder="yourmail@example.com"
        >
      </div>

      <div class="field">
        <label for="password" class="label">Password</label>
        <div class="password-row">
          <input
            id="password"
            class="input"
            type="password"
            name="password"
            required
          >
          <button type="button" class="btn btn-ghost btn-eye" aria-label="Show Password" title="Show Password" id="togglePwd">
            👁
          </button>
        </div>
      </div>

      <input type="submit" class="btn btn-primary" id="loginBtn" value="Enter">
        <span class="spinner" aria-hidden="true"></span>

      <p class="muted small">
        ¿Don't have an account yet? 
        <a class="link" href="/signup">Create an account</a>
      </p>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/ajax.js') ?>"></script>
<script>
// small, safe UI sugar that won't interfere with ajax.js
(() => {
  const pwd = document.getElementById('password');
  const btn = document.getElementById('togglePwd');
  if (pwd && btn) {
    btn.addEventListener('click', () => {
      const isPwd = pwd.type === 'password';
      pwd.type = isPwd ? 'text' : 'password';
      btn.setAttribute('aria-label', isPwd ? 'Hide Password' : 'Show Password');
      btn.title = isPwd ? 'Hide Password' : 'Show Password';
    });
  }
})();
</script>
<?= $this->endSection() ?>
