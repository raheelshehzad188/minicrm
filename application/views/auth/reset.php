<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mino-auth">
  <div class="mino-auth__visual">
    <div class="mino-auth__visual-brand">
      <img src="<?php echo base_url('assets/images/logo-light.svg'); ?>" alt="Mino CRM">
      <span>Mino CRM</span>
    </div>
    <div class="mino-auth__visual-content">
      <h1>Choose a new password.</h1>
      <p>Use at least 8 characters. You’ll be signed out of other remembered devices.</p>
    </div>
  </div>

  <div class="mino-auth__form-panel">
    <button type="button" class="mino-nav-btn mino-auth__theme-toggle" data-mino-theme-toggle aria-label="Toggle theme">
      <i class="fas fa-moon"></i>
    </button>

    <div class="mino-auth__form-wrap">
      <div class="mino-auth__welcome">
        <h2>Reset password</h2>
        <?php if (empty($valid)): ?>
          <p class="text-danger">This reset link is invalid or has expired.</p>
          <a href="<?php echo site_url('auth/forgot'); ?>" class="btn btn-primary">Request a new link</a>
        <?php else: ?>
          <p>Enter and confirm your new password below.</p>
        <?php endif; ?>
      </div>

      <?php if (!empty($valid)): ?>
      <form class="mino-auth__form" id="resetForm" action="<?php echo site_url('auth/do_reset'); ?>" method="post" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="token" value="<?php echo html_escape($token); ?>">

        <div class="form-group">
          <label class="form-label" for="newPassword">New password <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="newPassword" name="password" required minlength="8" autocomplete="new-password">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirmPassword">Confirm password <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="confirmPassword" name="password_confirm" required minlength="8" autocomplete="new-password">
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block mino-auth__submit">
          <i class="fas fa-key"></i> Update Password
        </button>
      </form>
      <?php endif; ?>

      <div class="mino-auth__footer mt-4">
        <a href="<?php echo site_url('auth/login'); ?>">Back to sign in</a>
      </div>
    </div>
  </div>
</div>
