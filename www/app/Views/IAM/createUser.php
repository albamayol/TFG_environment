<?= $this->extend('layouts/main') ?>
<?php helper('form'); ?>
<?= $this->section('content') ?>

<style>
  .auth-wrap { 
    display:flex; 
    justify-content:center; 
    padding:2rem; }
  .auth-card {
    width:100%; 
    max-width:860px; 
    background:#fff; 
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.08); 
    padding:2rem;
  }
  .auth-card h1 { 
    margin:0 0 1rem; 
    font-size:1.6rem; }
  .subtle { 
    color:#666; 
    margin-bottom:1.25rem; 
  }

  .alert-errors {
    background:#fff3f3; 
    border:1px solid #f5c2c7; 
    color:#b02a37;
    padding:.75rem 1rem; 
    border-radius:10px; 
    margin-bottom:1rem;
  }
  .alert-errors ul { 
    margin:.25rem 0 0 1rem; 
  }

  form .grid { 
    display:grid; 
    gap:1rem; 
  }
  @media (min-width: 720px) {
    form .grid.cols-2 { 
      grid-template-columns: 1fr 1fr; 
    }
  }

  label {  
    display:block; 
    font-weight:600; 
    margin-bottom:.35rem; 
  }
  .field { 
    display:flex; 
    flex-direction:column; 
  }
  .input, textarea, select {
    width:100%; 
    border:1px solid #e5e7eb; 
    border-radius:12px; 
    padding:.75rem .9rem;
    outline:0; 
    transition:border .15s, 
    box-shadow .15s; 
    background:#fff;
  }
  .input:focus, textarea:focus {
    border-color:#2563eb; 
    box-shadow:0 0 0 3px rgba(37,99,235,.15);
  }
  .hint { 
    color:#6b7280; 
    font-size:.85rem; 
    margin-top:.35rem; 
  }
  .error { 
    color:#b02a37; 
    font-size:.85rem; 
    margin-top:.35rem; 
  }

  .pwd-box { 
    position:relative; 
  }
  .pwd-toggle {
    position:absolute; 
    right:.5rem; 
    top:50%; 
    transform:translateY(-50%);
    background:transparent; 
    border:0; 
    cursor:pointer; 
    font-size:.9rem; 
    color:#2563eb;
  }
  .requirements { 
    margin:.4rem 0 .6rem 1.1rem; 
    color:#444; 
    font-size:.9rem; 
  }
  .btn {
    background:#111827; 
    color:#fff; 
    border:0; 
    border-radius:12px; 
    padding:.85rem 1.1rem;
    font-weight:600; 
    cursor:pointer; 
    transition:transform .05s ease,
    box-shadow .15s ease;
  }
  .btn:hover { 
    box-shadow:0 6px 18px rgba(0,0,0,.12); 
  }
  .btn:active { 
    transform:translateY(1px); 
  }

  /* Soft dark mode if the layout respects prefers-color-scheme */
  @media (prefers-color-scheme: dark) {
    .auth-card { 
      background:#111315; 
      color:#e5e7eb; 
      box-shadow:none; 
      border:1px solid #1f2937; 
    }
    .input, textarea, select { 
      background:#0b0d0f; 
      color:#e5e7eb; 
      border-color:#374151; 
    }
    .input:focus, textarea:focus { 
      box-shadow:0 0 0 3px rgba(37,99,235,.25); 
    }
    .subtle { 
      color:#9ca3af; 
    }
    .hint { 
      color:#9ca3af; 
    }
    .alert-errors { 
      background:#2a0f12; 
      border-color:#7a2d34; 
      color:#fca5a5; 
    }
  }
</style>

<div class="auth-wrap">
  <div class="auth-card">
    <h1>Create User</h1>
    <p class="subtle">Create a new user account.</p>

    <?php if(session()->getFlashdata('errors')): ?>
      <div class="alert-errors">
        <strong>Please fix the following fields:</strong>
        <ul>
          <?php foreach(session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
          <?php endforeach ?>
        </ul>
      </div>
    <?php endif ?>
    
    <button type="button" class="btn btn-back" onclick="window.location.href='<?= site_url('/IAM/Users') ?>'">
        Back
    </button>

    <form action="<?= site_url('IAM/Users/store') ?>" method="post" novalidate>
      <?= csrf_field() ?>

      <div class="grid cols-2">
        <div class="field">
          <label for="name">First name</label>
          <input class="input" id="name" type="text" name="name" value="<?= set_value('name') ?>" required placeholder="Jane">
          <?php if (service('validation')->hasError('name')): ?>
            <div class="error"><?= service('validation')->showError('name') ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="surnames">Last name(s)</label>
          <input class="input" id="surnames" type="text" name="surnames" value="<?= set_value('surnames') ?>" required placeholder="Doe">
          <?php if (service('validation')->hasError('surnames')): ?>
            <div class="error"><?= service('validation')->showError('surnames') ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input class="input" id="email" type="email" name="email" value="<?= set_value('email') ?>" required placeholder="jane@example.com" autocomplete="email">
        <?php if (service('validation')->hasError('email')): ?>
          <div class="error"><?= service('validation')->showError('email') ?></div>
        <?php endif; ?>
      </div>

      <div class="grid cols-2">
        <div class="field">
          <label for="password">Password</label>
          <div class="pwd-box">
            <input class="input" id="password" type="password" name="password" required
                   minlength="12"
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{12,}$"
                   title="At least 12 characters, incl. uppercase, lowercase, number and symbol. No spaces."
                   autocomplete="new-password" aria-describedby="pwd-help">
            <button class="pwd-toggle" type="button" data-target="password">Show</button>
          </div>
          <div id="pwd-help" class="hint">Must include: 12+ chars, uppercase, lowercase, number, symbol (no spaces).</div>
          <ul class="requirements">
            <li>At least 12 characters</li>
            <li>At least 1 lowercase, 1 uppercase, 1 number and 1 symbol</li>
            <li>No spaces</li>
          </ul>
          <?php if (service('validation')->hasError('password')): ?>
            <div class="error"><?= service('validation')->showError('password') ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="repeated_password">Confirm password</label>
          <div class="pwd-box">
            <input class="input" id="repeated_password" type="password" name="repeated_password" required autocomplete="new-password">
            <button class="pwd-toggle" type="button" data-target="repeated_password">Show</button>
          </div>
          <?php if (service('validation')->hasError('repeated_password')): ?>
            <div class="error"><?= service('validation')->showError('repeated_password') ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="grid cols-2">
        <div class="field">
          <label for="birthdate">Birthdate</label>
          <input class="input" id="birthdate" type="date" name="birthdate" required aria-describedby="birthdate-hint">
          <?php if (service('validation')->hasError('birthdate')): ?>
            <div class="error"><?= service('validation')->showError('birthdate') ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="telephone">Telephone</label>
          <input class="input" id="telephone" type="tel" name="telephone" required
                 placeholder="e.g., +34 600 123 456"
                 pattern="^\+?[0-9\s\-\(\)]{7,20}$">
          <?php if (service('validation')->hasError('telephone')): ?>
            <div class="error"><?= service('validation')->showError('telephone') ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="field">
        <label for="address">Address</label>
        <input class="input" id="address" type="text" name="address" value="<?= set_value('address') ?>" required placeholder="123 Example Street, Madrid" autocomplete="street-address">
        <?php if (service('validation')->hasError('address')): ?>
          <div class="error"><?= service('validation')->showError('address') ?></div>
        <?php endif; ?>
      </div>

      <div class="grid cols-2">
        <div class="field">
          <label for="dni_nie">National ID (DNI/NIE) <span class="hint">(optional)</span></label>
          <input class="input" id="dni_nie" type="text" name="dni_nie" value="<?= set_value('dni_nie') ?>" placeholder="12345678Z">
          <?php if (service('validation')->hasError('dni_nie')): ?>
            <div class="error"><?= service('validation')->showError('dni_nie') ?></div>
          <?php endif; ?>
        </div>
        <div></div>
      </div>

      <div class="grid cols-2">
        <div class="field">
          <label for="soft_skills">Soft skills <span class="hint">(optional)</span></label>
          <textarea class="input" id="soft_skills" name="soft_skills" rows="3" placeholder="Communication, leadership..."><?= set_value('soft_skills') ?></textarea>
          <?php if (service('validation')->hasError('soft_skills')): ?>
            <div class="error"><?= service('validation')->showError('soft_skills') ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="technical_skills">Technical skills <span class="hint">(optional)</span></label>
          <textarea class="input" id="technical_skills" name="technical_skills" rows="3" placeholder="PHP, MySQL, Docker..."><?= set_value('technical_skills') ?></textarea>
          <?php if (service('validation')->hasError('technical_skills')): ?>
            <div class="error"><?= service('validation')->showError('technical_skills') ?></div>
          <?php endif; ?>
        </div>
      </div>
        <div class="field">
            <label for="role">Role</label>
            <select class="input" id="role" name="role" required>
                <option value="" disabled <?= set_value('role') ? '' : 'selected' ?>>Select a role</option>
                <option value="Profile_Admin" <?= set_value('role') === 'Profile_Admin' ? 'selected' : '' ?>>Profile Admin</option>
                <option value="Manager" <?= set_value('role') === 'Manager' ? 'selected' : '' ?>>Manager</option>
                <option value="Head_Of_Team" <?= set_value('role') === 'Head_Of_Team' ? 'selected' : '' ?>>Head Of Team</option>
                <option value="Worker" <?= set_value('role') === 'Worker' ? 'selected' : '' ?>>Worker</option>
            </select>
            <?php if (service('validation')->hasError('role')): ?>
            <div class="error"><?= service('validation')->showError('role') ?></div>
            <?php endif; ?>
      <div class="form-group form-check">
        <input type="checkbox" id="simulated" name="simulated" value="1" class="form-check-input">
        <label for="simulated" class="form-check-label">Simulated?</label>
      </div>
      <div style="margin-top:1rem; display:flex; gap:.75rem; justify-content:flex-end;">
        <button type="submit" class="btn">Create account</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Toggle show/hide password
  document.querySelectorAll('.pwd-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const input = document.getElementById(id);
      const isPwd = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPwd ? 'text' : 'password');
      btn.textContent = isPwd ? 'Hide' : 'Show';
    });
  });
</script>

<?= $this->endSection() ?>