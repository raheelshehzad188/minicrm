<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mino-auth">
  <div class="mino-auth__visual">
    <div class="mino-auth__visual-brand">
      <img src="<?php echo base_url('assets/images/logo-light.svg'); ?>" alt="Mino CRM">
      <span>Mino CRM</span>
    </div>

    <div class="mino-auth__visual-content">
      <h1>Close more deals with a calm, focused CRM.</h1>
      <p>Track leads, nurture relationships, and grow revenue — all from one premium workspace built for modern sales teams.</p>
    </div>

    <div class="mino-auth__visual-stats">
      <div>
        <strong>12k+</strong>
        <span>Active teams</span>
      </div>
      <div>
        <strong>98%</strong>
        <span>Satisfaction</span>
      </div>
      <div>
        <strong>4.9</strong>
        <span>Avg. rating</span>
      </div>
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
        <h2>Welcome back</h2>
        <p>Sign in to your Mino CRM workspace to continue.</p>
      </div>

      <form class="mino-auth__form" id="loginForm" action="<?php echo site_url('auth/do_login'); ?>" method="post" novalidate>
        <?php echo csrf_field(); ?>
        <div class="form-group">
          <label class="form-label" for="loginEmail">Email address <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="you@company.com" required autocomplete="username">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="loginPassword">Password <span class="required">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required autocomplete="current-password">
          </div>
        </div>

        <div class="mino-auth__meta">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" value="1">
            <label class="form-check-label" for="rememberMe">Remember me</label>
          </div>
          <a href="<?php echo site_url('auth/forgot'); ?>" class="mino-auth__forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block mino-auth__submit" id="loginBtn">
          <i class="fas fa-right-to-bracket"></i> Sign In
        </button>
      </form>

      <div class="mino-auth__demo">
        <div class="mino-auth__demo-title">Demo Login</div>
        <p class="mino-auth__demo-hint">Click a user to fill the form, then Sign In.</p>
        <div class="table-responsive">
          <table class="mino-auth__demo-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Org</th>
              </tr>
            </thead>
            <tbody>
              <tr class="demo-login-row" role="button" tabindex="0" data-email="owner@acme.com" data-password="Password123!">
                <td>
                  <span class="fw-semibold">Acme Owner</span>
                  <span class="demo-email">owner@acme.com</span>
                </td>
                <td><span class="mino-badge mino-badge-primary">Owner</span></td>
                <td>Acme</td>
              </tr>
              <tr class="demo-login-row" role="button" tabindex="0" data-email="admin@acme.com" data-password="Password123!">
                <td>
                  <span class="fw-semibold">Acme Admin</span>
                  <span class="demo-email">admin@acme.com</span>
                </td>
                <td><span class="mino-badge mino-badge-info">Admin</span></td>
                <td>Acme</td>
              </tr>
              <tr class="demo-login-row" role="button" tabindex="0" data-email="manager@acme.com" data-password="Password123!">
                <td>
                  <span class="fw-semibold">Acme Manager</span>
                  <span class="demo-email">manager@acme.com</span>
                </td>
                <td><span class="mino-badge mino-badge-warning">Manager</span></td>
                <td>Acme</td>
              </tr>
              <tr class="demo-login-row" role="button" tabindex="0" data-email="sales@acme.com" data-password="Password123!">
                <td>
                  <span class="fw-semibold">Acme Sales</span>
                  <span class="demo-email">sales@acme.com</span>
                </td>
                <td><span class="mino-badge mino-badge-success">Sales</span></td>
                <td>Acme</td>
              </tr>
              <tr class="demo-login-row" role="button" tabindex="0" data-email="owner@beta.com" data-password="Password123!">
                <td>
                  <span class="fw-semibold">Beta Owner</span>
                  <span class="demo-email">owner@beta.com</span>
                </td>
                <td><span class="mino-badge mino-badge-primary">Owner</span></td>
                <td>Beta</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mino-auth__demo-pass">Password for all: <strong>Password123!</strong></div>
      </div>
    </div>
  </div>
</div>
