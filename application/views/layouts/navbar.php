<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<header class="mino-navbar">
  <button type="button" class="mino-navbar__toggle" data-mino-sidebar-toggle aria-label="Toggle sidebar">
    <i class="fas fa-bars"></i>
  </button>

  <div class="mino-search">
    <i class="fas fa-magnifying-glass mino-search__icon"></i>
    <input type="search" class="mino-search__input" placeholder="Search leads, contacts, deals..." aria-label="Global search">
    <span class="mino-search__kbd">/</span>
  </div>

  <div class="mino-navbar__actions">
    <div class="dropdown">
      <button type="button" class="mino-nav-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
        <i class="fas fa-bell"></i>
        <span class="mino-nav-btn__badge">3</span>
      </button>
      <div class="dropdown-menu dropdown-menu-end mino-notif-dropdown">
        <div class="dropdown-header">
          <strong>Notifications</strong>
          <a href="#" class="mino-text-xs">Mark all read</a>
        </div>
        <div class="mino-notif-list">
          <a href="#" class="mino-notif-item unread">
            <span class="mino-avatar mino-avatar-sm">JD</span>
            <div>
              <div class="mino-notif-item__text"><strong>Jane Doe</strong> assigned you a new lead</div>
              <div class="mino-notif-item__time">2 minutes ago</div>
            </div>
          </a>
          <a href="#" class="mino-notif-item unread">
            <span class="mino-avatar mino-avatar-sm bg-success-soft text-success">DK</span>
            <div>
              <div class="mino-notif-item__text">Deal <strong>Acme Corp</strong> moved to Negotiation</div>
              <div class="mino-notif-item__time">1 hour ago</div>
            </div>
          </a>
          <a href="#" class="mino-notif-item">
            <span class="mino-avatar mino-avatar-sm bg-warning-soft text-warning">TM</span>
            <div>
              <div class="mino-notif-item__text">Follow-up due for <strong>TechNova</strong></div>
              <div class="mino-notif-item__time">Yesterday</div>
            </div>
          </a>
        </div>
        <div class="mino-notif-footer">
          <a href="#">View all notifications</a>
        </div>
      </div>
    </div>

    <div class="dropdown">
      <button type="button" class="mino-nav-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Messages">
        <i class="fas fa-envelope"></i>
        <span class="mino-nav-btn__badge">2</span>
      </button>
      <div class="dropdown-menu dropdown-menu-end mino-notif-dropdown">
        <div class="dropdown-header">
          <strong>Messages</strong>
        </div>
        <div class="mino-notif-list">
          <a href="#" class="mino-notif-item unread">
            <span class="mino-avatar mino-avatar-sm">SK</span>
            <div>
              <div class="mino-notif-item__text"><strong>Sarah Kim</strong> — Can we reschedule?</div>
              <div class="mino-notif-item__time">15 min ago</div>
            </div>
          </a>
          <a href="#" class="mino-notif-item">
            <span class="mino-avatar mino-avatar-sm bg-info-soft text-info">MR</span>
            <div>
              <div class="mino-notif-item__text"><strong>Mike Ross</strong> — Proposal looks great</div>
              <div class="mino-notif-item__time">3 hours ago</div>
            </div>
          </a>
        </div>
        <div class="mino-notif-footer">
          <a href="#">Open inbox</a>
        </div>
      </div>
    </div>

    <button type="button" class="mino-nav-btn" data-mino-theme-toggle aria-label="Toggle dark mode">
      <i class="fas fa-moon"></i>
    </button>

    <button type="button" class="mino-nav-btn d-none d-md-flex" data-bs-toggle="offcanvas" data-bs-target="#minoRightPanel" aria-label="Quick settings">
      <i class="fas fa-sliders"></i>
    </button>

    <div class="dropdown">
      <button type="button" class="mino-user-btn" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
          $avatar = $this->session->userdata('profile_image');
          if ($avatar):
        ?>
          <img src="<?php echo base_url($avatar); ?>" alt="" class="mino-avatar mino-avatar-sm" style="object-fit:cover;">
        <?php else: ?>
          <span class="mino-avatar mino-avatar-sm"><?php echo html_escape(user_initials()); ?></span>
        <?php endif; ?>
        <span class="mino-user-btn__info">
          <span class="mino-user-btn__name"><?php echo html_escape(current_user_name()); ?></span>
          <span class="mino-user-btn__role"><?php echo html_escape(current_role_name()); ?></span>
        </span>
        <i class="fas fa-chevron-down mino-text-xs mino-text-muted"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><h6 class="dropdown-header"><?php echo html_escape(current_org_name()); ?></h6></li>
        <li><a class="dropdown-item" href="<?php echo site_url('auth/profile'); ?>"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a class="dropdown-item" href="<?php echo site_url('auth/password'); ?>"><i class="fas fa-key"></i> Change Password</a></li>
        <li><a class="dropdown-item" href="#"><i class="fas fa-circle-question"></i> Help Center</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?php echo site_url('auth/logout'); ?>"><i class="fas fa-right-from-bracket"></i> Sign Out</a></li>
      </ul>
    </div>
  </div>
</header>
