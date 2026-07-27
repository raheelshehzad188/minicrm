<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="offcanvas offcanvas-end mino-right-panel" tabindex="-1" id="minoRightPanel" aria-labelledby="minoRightPanelLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="minoRightPanelLabel">Quick Settings</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <h6 class="mb-3">Appearance</h6>

    <div class="theme-option active" data-mino-theme="light">
      <span class="theme-option__swatch light"></span>
      <div>
        <div class="fw-semibold">Light Mode</div>
        <div class="mino-text-xs mino-text-muted">Clean daylight interface</div>
      </div>
    </div>

    <div class="theme-option" data-mino-theme="dark">
      <span class="theme-option__swatch dark"></span>
      <div>
        <div class="fw-semibold">Dark Mode</div>
        <div class="mino-text-xs mino-text-muted">Reduce eye strain at night</div>
      </div>
    </div>

    <hr class="my-4" style="border-color: var(--mino-border);">

    <h6 class="mb-3">Layout</h6>
    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" id="settingCompact" data-mino-sidebar-toggle>
      <label class="form-check-label" for="settingCompact">Collapsed sidebar</label>
    </div>
    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" id="settingDense" checked disabled>
      <label class="form-check-label" for="settingDense">Comfortable density</label>
    </div>

    <hr class="my-4" style="border-color: var(--mino-border);">

    <h6 class="mb-3">Quick Links</h6>
    <div class="d-flex flex-column gap-2">
      <a href="<?php echo site_url('ui/components'); ?>" class="btn btn-ghost justify-content-start"><i class="fas fa-puzzle-piece"></i> Components</a>
      <a href="<?php echo site_url('ui/forms'); ?>" class="btn btn-ghost justify-content-start"><i class="fas fa-wpforms"></i> Forms</a>
      <a href="<?php echo site_url('ui/tables'); ?>" class="btn btn-ghost justify-content-start"><i class="fas fa-table"></i> Tables</a>
    </div>
  </div>
</div>
