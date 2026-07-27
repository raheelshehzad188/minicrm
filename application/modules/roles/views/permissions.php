<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="mino-alert mino-alert-primary">
  <i class="fas fa-shield-halved"></i>
  <div>
    Permission matrix for <strong><?php echo html_escape($role->name); ?></strong>.
    Future modules registered in the system appear here automatically.
  </div>
</div>

<form id="permissionMatrixForm" action="<?php echo site_url('roles/save_permissions/' . $role->id); ?>" method="post">
  <?php echo csrf_field(); ?>

  <div class="mino-table-wrap mb-4">
    <div class="mino-table-toolbar">
      <h3 class="mino-card-title mb-0">Module permissions</h3>
      <?php if ($can_edit): ?>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-ghost" id="permSelectAll">Select all</button>
        <button type="button" class="btn btn-sm btn-ghost" id="permClearAll">Clear</button>
      </div>
      <?php endif; ?>
    </div>
    <div class="table-responsive">
      <table class="mino-table w-100" id="permMatrixTable">
        <thead>
          <tr>
            <th>Module</th>
            <?php foreach ($actions as $action): ?>
              <th class="text-center text-capitalize"><?php echo html_escape($action); ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($matrix as $row): ?>
          <tr>
            <td>
              <div class="fw-semibold"><?php echo html_escape($row['module']->name); ?></div>
              <div class="mino-text-xs mino-text-muted"><?php echo html_escape($row['module']->description); ?></div>
            </td>
            <?php foreach ($actions as $action): ?>
              <?php $perm = $row['perms'][$action]; ?>
              <td class="text-center">
                <?php if ($perm): ?>
                  <input class="form-check-input perm-check" type="checkbox" name="permission_ids[]" value="<?php echo (int) $perm->id; ?>"
                    <?php echo in_array((int) $perm->id, $assigned, TRUE) ? 'checked' : ''; ?>
                    <?php echo $can_edit ? '' : 'disabled'; ?>
                    title="<?php echo html_escape($perm->slug); ?>">
                <?php else: ?>
                  <span class="mino-text-muted">—</span>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (!empty($profile_perms)): ?>
  <div class="mino-card mb-4">
    <div class="mino-card-header"><h3 class="mino-card-title">Profile permissions</h3></div>
    <div class="mino-card-body d-flex flex-wrap gap-4">
      <?php foreach ($profile_perms as $p): ?>
        <div class="form-check">
          <input class="form-check-input perm-check" type="checkbox" name="permission_ids[]" value="<?php echo (int) $p->id; ?>"
            id="perm_<?php echo (int) $p->id; ?>"
            <?php echo in_array((int) $p->id, $assigned, TRUE) ? 'checked' : ''; ?>
            <?php echo $can_edit ? '' : 'disabled'; ?>>
          <label class="form-check-label" for="perm_<?php echo (int) $p->id; ?>"><?php echo html_escape($p->name); ?></label>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($can_edit): ?>
  <div class="d-flex justify-content-end gap-2">
    <a href="<?php echo site_url('roles'); ?>" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Save permissions</button>
  </div>
  <?php endif; ?>
</form>
