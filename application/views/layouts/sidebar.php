<?php defined('BASEPATH') OR exit('No direct script access allowed');
$current = isset($active_menu) ? $active_menu : '';
?>
<aside class="mino-sidebar" id="minoSidebar" aria-label="Main navigation">
  <div class="mino-sidebar__brand">
    <img src="<?php echo base_url('assets/images/logo.svg'); ?>" alt="Mino CRM" class="mino-sidebar__logo">
    <span class="mino-sidebar__brand-text">Mino <span>CRM</span></span>
  </div>

  <nav class="mino-sidebar__nav">
    <div class="mino-nav-section">
      <div class="mino-nav-section__label">Main</div>
      <a href="<?php echo site_url('dashboard'); ?>" class="mino-nav-link <?php echo $current === 'dashboard' ? 'active' : ''; ?>">
        <i class="fas fa-gauge-high nav-icon"></i>
        <span class="nav-text">Dashboard</span>
      </a>
    </div>

    <div class="mino-nav-section">
      <div class="mino-nav-section__label">CRM</div>

      <button type="button" class="mino-nav-link" data-mino-submenu-toggle aria-expanded="<?php echo in_array($current, array('leads', 'contacts'), true) ? 'true' : 'false'; ?>">
        <i class="fas fa-user-group nav-icon"></i>
        <span class="nav-text">People</span>
        <i class="fas fa-chevron-right nav-arrow"></i>
      </button>
      <ul class="mino-nav-submenu <?php echo in_array($current, array('leads', 'contacts'), true) ? 'show' : ''; ?>">
        <li>
          <?php if (can('leads.view')): ?>
          <a href="<?php echo site_url('leads'); ?>" class="mino-nav-link <?php echo $current === 'leads' ? 'active' : ''; ?>">
            <span class="nav-text">Leads</span>
          </a>
          <?php else: ?>
          <a href="#" class="mino-nav-link"><span class="nav-text">Leads</span></a>
          <?php endif; ?>
        </li>
        <li>
          <a href="<?php echo site_url('ui/tables'); ?>" class="mino-nav-link <?php echo $current === 'contacts' ? 'active' : ''; ?>">
            <span class="nav-text">Contacts</span>
          </a>
        </li>
      </ul>

      <button type="button" class="mino-nav-link" data-mino-submenu-toggle aria-expanded="false">
        <i class="fas fa-handshake nav-icon"></i>
        <span class="nav-text">Deals</span>
        <i class="fas fa-chevron-right nav-arrow"></i>
      </button>
      <ul class="mino-nav-submenu">
        <li><a href="#" class="mino-nav-link"><span class="nav-text">Pipeline</span></a></li>
        <li><a href="#" class="mino-nav-link"><span class="nav-text">Won Deals</span></a></li>
        <li><a href="#" class="mino-nav-link"><span class="nav-text">Lost Deals</span></a></li>
      </ul>

      <a href="#" class="mino-nav-link">
        <i class="fas fa-calendar-check nav-icon"></i>
        <span class="nav-text">Follow-ups</span>
      </a>
      <a href="#" class="mino-nav-link">
        <i class="fas fa-list-check nav-icon"></i>
        <span class="nav-text">Tasks</span>
      </a>
    </div>

    <div class="mino-nav-section">
      <div class="mino-nav-section__label">Insights</div>
      <?php if (can('reports.view')): ?>
      <a href="<?php echo site_url('reports/leads'); ?>" class="mino-nav-link <?php echo $current === 'reports' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-text">Reports</span>
      </a>
      <?php else: ?>
      <a href="#" class="mino-nav-link">
        <i class="fas fa-chart-line nav-icon"></i>
        <span class="nav-text">Reports</span>
      </a>
      <?php endif; ?>
      <a href="#" class="mino-nav-link">
        <i class="fas fa-bullseye nav-icon"></i>
        <span class="nav-text">Goals</span>
      </a>
    </div>

    <div class="mino-nav-section">
      <div class="mino-nav-section__label">UI Kit</div>
      <a href="<?php echo site_url('ui/components'); ?>" class="mino-nav-link <?php echo $current === 'components' ? 'active' : ''; ?>">
        <i class="fas fa-puzzle-piece nav-icon"></i>
        <span class="nav-text">Components</span>
      </a>
      <a href="<?php echo site_url('ui/forms'); ?>" class="mino-nav-link <?php echo $current === 'forms' ? 'active' : ''; ?>">
        <i class="fas fa-wpforms nav-icon"></i>
        <span class="nav-text">Forms</span>
      </a>
      <a href="<?php echo site_url('ui/tables'); ?>" class="mino-nav-link <?php echo $current === 'tables' ? 'active' : ''; ?>">
        <i class="fas fa-table nav-icon"></i>
        <span class="nav-text">Tables</span>
      </a>
    </div>

    <div class="mino-nav-section">
      <div class="mino-nav-section__label">System</div>
      <?php if (can('users.view')): ?>
      <a href="<?php echo site_url('users'); ?>" class="mino-nav-link <?php echo $current === 'users' ? 'active' : ''; ?>">
        <i class="fas fa-users nav-icon"></i>
        <span class="nav-text">Users</span>
      </a>
      <?php endif; ?>
      <?php if (can('roles.view')): ?>
      <a href="<?php echo site_url('roles'); ?>" class="mino-nav-link <?php echo $current === 'roles' ? 'active' : ''; ?>">
        <i class="fas fa-shield-halved nav-icon"></i>
        <span class="nav-text">Roles</span>
      </a>
      <?php endif; ?>
      <?php if (can('organization.view')): ?>
      <a href="<?php echo site_url('organization'); ?>" class="mino-nav-link <?php echo $current === 'organization' ? 'active' : ''; ?>">
        <i class="fas fa-building nav-icon"></i>
        <span class="nav-text">Organization</span>
      </a>
      <?php endif; ?>
      <a href="<?php echo site_url('auth/profile'); ?>" class="mino-nav-link <?php echo $current === 'profile' ? 'active' : ''; ?>">
        <i class="fas fa-user nav-icon"></i>
        <span class="nav-text">My Profile</span>
      </a>
      <a href="<?php echo site_url('auth/logout'); ?>" class="mino-nav-link">
        <i class="fas fa-right-from-bracket nav-icon"></i>
        <span class="nav-text">Logout</span>
      </a>
    </div>
  </nav>

  <div class="mino-sidebar__footer">
    <button type="button" class="mino-nav-link" data-bs-toggle="offcanvas" data-bs-target="#minoRightPanel">
      <i class="fas fa-sliders nav-icon"></i>
      <span class="nav-text">Quick Settings</span>
    </button>
  </div>
</aside>

<div class="mino-sidebar-overlay" aria-hidden="true"></div>
