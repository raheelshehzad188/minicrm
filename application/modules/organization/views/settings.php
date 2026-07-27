<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="mino-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Company profile</h3>
        <?php if ($can_edit): ?>
          <span class="mino-badge mino-badge-success">Editable</span>
        <?php else: ?>
          <span class="mino-badge mino-badge-secondary">View only</span>
        <?php endif; ?>
      </div>
      <div class="mino-card-body">
        <form id="orgSettingsForm" action="<?php echo site_url('organization/update'); ?>" method="post" <?php echo $can_edit ? '' : 'onsubmit="return false;"'; ?>>
          <?php echo csrf_field(); ?>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company name <span class="required">*</span></label>
              <input type="text" class="form-control" name="name" value="<?php echo html_escape($org->name); ?>" <?php echo $can_edit ? 'required' : 'disabled'; ?>>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" value="<?php echo html_escape($org->email); ?>" <?php echo $can_edit ? '' : 'disabled'; ?>>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone" value="<?php echo html_escape($org->phone); ?>" <?php echo $can_edit ? '' : 'disabled'; ?>>
            </div>
            <div class="col-md-6">
              <label class="form-label">Website</label>
              <input type="text" class="form-control" name="website" value="<?php echo html_escape(isset($org->website) ? $org->website : ''); ?>" placeholder="https://" <?php echo $can_edit ? '' : 'disabled'; ?>>
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2" <?php echo $can_edit ? '' : 'disabled'; ?>><?php echo html_escape(isset($org->address) ? $org->address : ''); ?></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label">Country</label>
              <input type="text" class="form-control" name="country" value="<?php echo html_escape($org->country); ?>" <?php echo $can_edit ? '' : 'disabled'; ?>>
            </div>
            <div class="col-md-4">
              <label class="form-label">Timezone <span class="required">*</span></label>
              <select class="form-select mino-select2" name="timezone" <?php echo $can_edit ? '' : 'disabled'; ?>>
                <?php foreach ($timezones as $tz): ?>
                  <option value="<?php echo html_escape($tz); ?>" <?php echo $org->timezone === $tz ? 'selected' : ''; ?>><?php echo html_escape($tz); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Currency <span class="required">*</span></label>
              <select class="form-select" name="currency" <?php echo $can_edit ? '' : 'disabled'; ?>>
                <?php foreach ($currencies as $c): ?>
                  <option value="<?php echo $c; ?>" <?php echo $org->currency === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Business registration number</label>
              <input type="text" class="form-control" name="registration_number" value="<?php echo html_escape(isset($org->registration_number) ? $org->registration_number : ''); ?>" <?php echo $can_edit ? '' : 'disabled'; ?>>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tax number</label>
              <input type="text" class="form-control" name="tax_number" value="<?php echo html_escape(isset($org->tax_number) ? $org->tax_number : ''); ?>" <?php echo $can_edit ? '' : 'disabled'; ?>>
            </div>
            <?php if ($can_edit): ?>
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Save changes</button>
            </div>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="mino-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Company logo</h3>
      </div>
      <div class="mino-card-body text-center">
        <div class="mb-3">
          <img id="orgLogoPreview" src="<?php echo !empty($org->logo) ? base_url($org->logo) : base_url('assets/images/logo.svg'); ?>" alt="Logo" style="max-width:140px;max-height:140px;border-radius:12px;border:1px solid var(--mino-border);padding:8px;background:var(--mino-surface-2);">
        </div>
        <?php if ($can_edit): ?>
        <form id="orgLogoForm" action="<?php echo site_url('organization/upload_logo'); ?>" method="post" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <input type="file" class="form-control mb-3" name="logo" id="orgLogoInput" accept="image/*" required>
          <button type="submit" class="btn btn-soft-primary btn-block"><i class="fas fa-upload"></i> Upload logo</button>
        </form>
        <?php endif; ?>
        <p class="mino-text-xs mino-text-muted mt-3 mb-0">JPG, PNG or WEBP. Max 2MB.</p>
      </div>
    </div>
  </div>
</div>
