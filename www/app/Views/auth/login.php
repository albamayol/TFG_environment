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
<!--<script>
$(function () {
  const $form = $('#login-form');
  const $resp = $('#response');

  function setMessage(html) { $resp.html(html); }

  // Read CSRF token from <meta>
  const csrfName = $('meta[name="csrf-token-name"]').attr('content');
  function getCsrfHash() { return $('meta[name="csrf-token"]').attr('content'); }

  // Keep hidden input (csrf_field) in sync if server sends a new token
  function refreshCsrf(csrf) {
    if (!csrf || !csrf.name || !csrf.hash) return;
    // meta (for next AJAX)
    $('meta[name="csrf-token"]').attr('content', csrf.hash);
    // hidden input (for non-AJAX fallback)
    const $hidden = $form.find('input[name="'+ csrf.name +'"]');
    if ($hidden.length) $hidden.val(csrf.hash);
  }

  $form.on('submit', function (e) {
    e.preventDefault(); // hijack submit

    const payload = {
      email:    $form.find('input[name=email]').val(),
      password: $form.find('input[name=password]').val()
    };

    // Disable button to avoid double click (optional)
    const $btn = $form.find('button[type=submit]').prop('disabled', true);

    $.ajax({
      type: 'POST',
      url:  $form.attr('action'),
      contentType: 'application/json;charset=utf-8',
      dataType: 'json',
      data: JSON.stringify(payload),
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfHash() // CI4 will validate from header
      }
    })
    .done(function (data, textStatus, jqXHR) {
      // If server rotates CSRF, refresh it
      if (data && data.csrf) refreshCsrf(data.csrf);

      if (!data || !data.success) {
        setMessage('<p style="color:red">' + (data?.error || 'Error de autenticación') + '</p>');
        return;
      }
      // Success → redirect (server can send where)
      window.location.href = data.redirect || '/Tasks/MyDay';
    })
    .fail(function (jqXHR) {
      const data = jqXHR.responseJSON || {};
      if (data && data.csrf) refreshCsrf(data.csrf);

      const msg = data.error || 'Ha ocurrido un error. Inténtalo de nuevo.';
      setMessage('<p style="color:red">'+ msg +'</p>');
    })
    .always(function () {
      $btn.prop('disabled', false);
    });
  });
});
</script>-->

<?= $this->endSection() ?>
