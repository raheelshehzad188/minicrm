<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mino-auth">
  <div class="mino-auth__visual">
    <div class="mino-auth__visual-brand">
      <img src="<?php echo base_url('assets/images/logo-light.svg'); ?>" alt="Mino CRM">
      <span>Mino CRM</span>
    </div>
    <div class="mino-auth__visual-content">
      <h1>Reset access securely.</h1>
      <p>Enter your email and we’ll generate a secure password reset link for your account.</p>
    </div>
    <div class="mino-auth__visual-stats">
      <div><strong>Secure</strong><span>Tokenized resets</span></div>
      <div><strong>Fast</strong><span>1-hour expiry</span></div>
    </div>
  </div>

  <div class="mino-auth__form-panel">
    <button type="button" class="mino-nav-btn mino-auth__theme-toggle" data-mino-theme-toggle aria-label="Toggle theme">
      <i class="fas fa-moon"></i>
    </button>

    <div class="mino-auth__form-wrap">
      <div class="mino-auth__logo-mobile">
        <img src="<?php echo base_url('assets/images/logo.svg'); ?>" alt="Mino CRM">
        <span>Mino <em>CRM</em></span>
      </div>

      <div class="mino-auth__welcome">
        <h2>Forgot password</h2>
        <p>We’ll send a reset link if your email is registered.</p>
      </div>

      <form class="mino-auth__form" id="forgotForm" action="<?php echo site_url('auth/do_forgot'); ?>" method="post" novalidate>
        <?php echo csrf_field(); ?>
        <div class="form-group">
          <label class="form-label" for="forgotEmail">Email address <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="forgotEmail" name="email" placeholder="you@company.com" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block mino-auth__submit">
          <i class="fas fa-paper-plane"></i> Send Reset Link
        </button>
      </form>

      <div class="mino-auth__footer mt-4">
        <a href="<?php echo site_url('auth/login'); ?>">Back to sign in</a>
      </div>
    </div>
  </div>
</div>
