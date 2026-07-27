<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="mino-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Profile details</h3>
        <span class="mino-badge mino-badge-secondary"><?php echo html_escape(current_org_name()); ?></span>
      </div>
      <div class="mino-card-body">
        <form id="profileForm" action="<?php echo site_url('auth/update_profile'); ?>" method="post">
          <?php echo csrf_field(); ?>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="profileName">Full name <span class="required">*</span></label>
              <input type="text" class="form-control" id="profileName" name="name" value="<?php echo html_escape($user->name); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="profileEmail">Email</label>
              <input type="email" class="form-control" id="profileEmail" value="<?php echo html_escape($user->email); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="profilePhone">Phone</label>
              <input type="text" class="form-control" id="profilePhone" name="phone" value="<?php echo html_escape($user->phone); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <input type="text" class="form-control" value="<?php echo html_escape($user->role_name); ?>" disabled>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
              <a href="<?php echo site_url('auth/password'); ?>" class="btn btn-secondary">Change password</a>
              <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Save changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="mino-card">
      <div class="mino-card-body text-center">
        <?php if (!empty($user->profile_image)): ?>
          <img src="<?php echo base_url($user->profile_image); ?>" alt="" class="mino-avatar mino-avatar-xl mx-auto mb-3 profile-avatar-img" style="object-fit:cover;">
          <span class="mino-avatar mino-avatar-xl mx-auto mb-3 profile-avatar-initials" style="display:none;"><?php echo html_escape(user_initials($user->name)); ?></span>
        <?php else: ?>
          <span class="mino-avatar mino-avatar-xl mx-auto mb-3 profile-avatar-initials"><?php echo html_escape(user_initials($user->name)); ?></span>
          <img src="" alt="" class="mino-avatar mino-avatar-xl mx-auto mb-3 profile-avatar-img" style="display:none;object-fit:cover;">
        <?php endif; ?>
        <h4 class="mb-1"><?php echo html_escape($user->name); ?></h4>
        <p class="mino-text-muted mb-3"><?php echo html_escape($user->email); ?></p>
        <span class="mino-badge mino-badge-primary"><?php echo html_escape($user->role_name); ?></span>
        <?php if (!empty($user->last_login)): ?>
          <p class="mino-text-xs mino-text-muted mt-3 mb-3">Last login: <?php echo html_escape($user->last_login); ?></p>
        <?php endif; ?>

        <form id="profileAvatarForm" action="<?php echo site_url('auth/upload_avatar'); ?>" method="post" enctype="multipart/form-data" class="mt-3">
          <?php echo csrf_field(); ?>
          <input type="file" class="form-control mb-2" name="avatar" accept="image/*" required>
          <button type="submit" class="btn btn-soft-primary btn-sm btn-block"><i class="fas fa-camera"></i> Upload avatar</button>
        </form>
      </div>
    </div>
  </div>
</div>
