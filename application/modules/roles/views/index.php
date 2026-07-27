<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row g-4">
  <?php foreach ($roles as $role): ?>
  <div class="col-md-6 col-xl-3">
    <div class="mino-card h-100">
      <div class="mino-card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h4 class="mb-1"><?php echo html_escape($role->name); ?></h4>
            <p class="mino-text-sm mino-text-muted mb-0"><?php echo html_escape($role->description); ?></p>
          </div>
          <?php if ($role->is_system): ?>
            <span class="mino-badge mino-badge-secondary">System</span>
          <?php endif; ?>
        </div>
        <div class="stat-card__value" style="font-size:1.5rem;"><?php echo (int) $role->user_count; ?></div>
        <div class="mino-text-xs mino-text-muted mb-4">Users in this org</div>
        <a href="<?php echo site_url('roles/permissions/' . $role->id); ?>" class="btn btn-soft-primary btn-sm btn-block">
          <i class="fas fa-shield-halved"></i> Permissions
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="mino-card mt-4">
  <div class="mino-card-header">
    <h3 class="mino-card-title">Roles overview</h3>
  </div>
  <div class="table-responsive">
    <table class="mino-table w-100">
      <thead>
        <tr>
          <th>Role</th>
          <th>Slug</th>
          <th>Users</th>
          <th>Type</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($roles as $role): ?>
        <tr>
          <td class="fw-semibold"><?php echo html_escape($role->name); ?></td>
          <td><code class="mono"><?php echo html_escape($role->slug); ?></code></td>
          <td><?php echo (int) $role->user_count; ?></td>
          <td><?php echo $role->is_system ? '<span class="mino-badge mino-badge-secondary">System</span>' : '<span class="mino-badge mino-badge-info">Custom</span>'; ?></td>
          <td class="text-end">
            <a href="<?php echo site_url('roles/permissions/' . $role->id); ?>" class="btn btn-sm btn-ghost"><i class="fas fa-pen"></i> Manage</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
