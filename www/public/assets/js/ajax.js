$(function () {
  const $form = $('#login-form');
  const $resp = $('#response');

  function showError(msg) {
    $resp.html('<div class="alert alert-danger" role="alert">' + msg + '</div>');
  }

  // Get current CSRF from meta
  function getCsrfName() {
    return $('meta[name="csrf-token-name"]').attr('content');
  }
  function getCsrfHash() {
    return $('meta[name="csrf-token"]').attr('content');
  }

  // Refresh CSRF in meta + hidden field (CI rotates per request)
  function refreshCsrf(csrf) {
    if (!csrf || !csrf.name || !csrf.hash) return;
    $('meta[name="csrf-token"]').attr('content', csrf.hash);
    $('meta[name="csrf-token-name"]').attr('content', csrf.name);
    const $hidden = $form.find('input[name="'+ csrf.name +'"]');
    if ($hidden.length) $hidden.val(csrf.hash);
  }

  $form.on('submit', function (e) {
    e.preventDefault();

    const payload = {
      email:    $form.find('input[name="email"]').val(),
      password: $form.find('input[name="password"]').val()
    };

    // Basic client check (optional)
    if (!payload.email || !payload.password) {
      showError('Email y contraseña son obligatorios');
      return;
    }

    const $submit = $form.find('input[type="submit"]');
    $submit.prop('disabled', true);

    $.ajax({
      type: 'POST',
      url:  $form.attr('action'), // "/login"
      contentType: 'application/json;charset=utf-8',
      dataType: 'json',
      data: JSON.stringify(payload),
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfHash()
      }
    })
    .done(function (data) {
      if (data && data.csrf) refreshCsrf(data.csrf);

      if (!data || !data.success) {
        showError((data && data.error) || 'Authentication error');
        return;
      }
      // success → redirect
      window.location.href = data.redirect || '/Tasks/MyDay';
    })
    .fail(function (jqXHR) {
      const data = jqXHR.responseJSON || {};
      if (data && data.csrf) refreshCsrf(data.csrf);
      showError(data.error || 'An error occurred. Please try again.');
    })
    .always(function () {
      $submit.prop('disabled', false);
    });
  });
});
