<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="mino-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Change password</h3>
      </div>
      <div class="mino-card-body">
        <form id="changePasswordForm" action="<?php echo site_url('auth/do_change_password'); ?>" method="post">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label class="form-label" for="currentPassword">Current password <span class="required">*</span></label>
            <input type="password" class="form-control" id="currentPassword" name="current_password" required autocomplete="current-password">
          </div>
          <div class="form-group">
            <label class="form-label" for="password">New password <span class="required">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required minlength="8" autocomplete="new-password">
          </div>
          <div class="form-group">
            <label class="form-label" for="passwordConfirm">Confirm new password <span class="required">*</span></label>
            <input type="password" class="form-control" id="passwordConfirm" name="password_confirm" required minlength="8" autocomplete="new-password">
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="<?php echo site_url('auth/profile'); ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
