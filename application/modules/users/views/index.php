<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mino-alert mino-alert-info">
  <i class="fas fa-building"></i>
  <div>Users below belong to <strong><?php echo html_escape(current_org_name()); ?></strong> only. Other organizations cannot see these records.</div>
</div>

<div class="mino-table-wrap">
  <div class="mino-table-toolbar">
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <input type="search" class="form-control" id="userSearch" placeholder="Search name, email..." style="max-width:220px;height:34px;">
      <select class="form-select" id="userStatusFilter" style="max-width:140px;height:34px;">
        <option value="">All statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="suspended">Suspended</option>
      </select>
      <select class="form-select" id="userRoleFilter" style="max-width:160px;height:34px;">
        <option value="">All roles</option>
        <?php foreach ($roles as $role): ?>
          <option value="<?php echo (int) $role->id; ?>"><?php echo html_escape($role->name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="table-responsive p-3 pt-0">
    <table class="mino-table w-100" id="usersTable">
      <thead>
        <tr>
          <th>User</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last login</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="userForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" id="userId" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full name <span class="required">*</span></label>
              <input type="text" class="form-control" name="name" id="userName" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="required">*</span></label>
              <input type="email" class="form-control" name="email" id="userEmail" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone" id="userPhone">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role <span class="required">*</span></label>
              <select class="form-select" name="role_id" id="userRole" required>
                <?php foreach ($roles as $role): ?>
                  <?php if ($role->slug === 'owner' && !is_owner()) continue; ?>
                  <option value="<?php echo (int) $role->id; ?>"><?php echo html_escape($role->name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status <span class="required">*</span></label>
              <select class="form-select" name="status" id="userStatus" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" id="userPasswordLabel">Password <span class="required">*</span></label>
              <input type="password" class="form-control" name="password" id="userPassword" minlength="8" autocomplete="new-password">
              <div class="mino-text-xs mino-text-muted mt-1" id="userPasswordHint">Min 8 characters</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.MINO_USERS = {
  canEdit: <?php echo !empty($can_edit) ? 'true' : 'false'; ?>,
  canDelete: <?php echo !empty($can_delete) ? 'true' : 'false'; ?>,
  canCreate: <?php echo !empty($can_create) ? 'true' : 'false'; ?>,
  urls: {
    list: <?php echo json_encode(site_url('users/datatable')); ?>,
    store: <?php echo json_encode(site_url('users/store')); ?>,
    update: <?php echo json_encode(site_url('users/update')); ?>,
    destroy: <?php echo json_encode(site_url('users/delete')); ?>,
    status: <?php echo json_encode(site_url('users/set_status')); ?>,
    reset: <?php echo json_encode(site_url('users/reset_password')); ?>,
    get: <?php echo json_encode(site_url('users/get')); ?>
  }
};
</script>
