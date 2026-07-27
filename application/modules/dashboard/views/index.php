<?php defined('BASEPATH') OR exit('No direct script access allowed');
$widgets = isset($widgets) ? $widgets : array();
$has = function ($key) use ($widgets) { return in_array($key, $widgets, TRUE); };
?>

<div id="mino-dashboard"
  class="dashboard-page"
  data-url="<?php echo html_escape($data_url); ?>"
  data-default-range="<?php echo html_escape($default_range); ?>"
  data-csrf-name="<?php echo html_escape($csrf_name); ?>"
  data-csrf-hash="<?php echo html_escape($csrf_hash); ?>">

  <?php if ($has('welcome')): ?>
  <div class="dashboard-welcome mino-card mb-4" data-widget="welcome" id="widget-welcome">
    <div class="dashboard-welcome__inner">
      <div class="dashboard-welcome__content">
        <div class="mino-skeleton mino-skeleton-title" id="welcome-skeleton-title"></div>
        <div class="mino-skeleton mino-skeleton-text" id="welcome-skeleton-sub" style="width:45%"></div>
        <div class="dashboard-welcome__meta d-none" id="welcome-content">
          <h2 class="dashboard-welcome__title" id="welcomeTitle"></h2>
          <p class="dashboard-welcome__subtitle" id="welcomeSubtitle"></p>
          <div class="dashboard-welcome__chips">
            <span class="mino-badge mino-badge-primary" id="welcomeOrg"></span>
            <span class="mino-badge mino-badge-info" id="welcomeRole"></span>
            <span class="mino-badge mino-badge-secondary" id="welcomeRange"></span>
          </div>
        </div>
      </div>
      <div class="dashboard-welcome__icon d-none d-md-flex" id="welcomeIconWrap">
        <i class="fas fa-chart-pie"></i>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($has('kpis')): ?>
  <div data-widget="kpis" id="widget-kpis" class="mb-4">
    <div class="stat-grid" id="kpi-skeleton">
      <?php for ($i = 0; $i < 8; $i++): ?>
      <div class="stat-card">
        <div class="mino-skeleton mino-skeleton-card"></div>
      </div>
      <?php endfor; ?>
    </div>
    <div class="stat-grid stagger-children d-none" id="kpi-grid"></div>
  </div>
  <?php endif; ?>

  <?php if ($has('charts')): ?>
  <div class="row g-4 mb-4" data-widget="charts" id="widget-charts">
    <div class="col-lg-8">
      <div class="chart-card h-100">
        <div class="mino-card-header">
          <h3 class="mino-card-title">Revenue Overview</h3>
        </div>
        <div class="chart-placeholder position-relative">
          <div class="dashboard-widget-loading" id="chart-revenue-loading">
            <div class="mino-skeleton" style="height:280px;border-radius:var(--mino-radius)"></div>
          </div>
          <div id="chartRevenueOverview" class="d-none"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card h-100">
        <div class="mino-card-header">
          <h3 class="mino-card-title">Lead Status</h3>
        </div>
        <div class="chart-placeholder position-relative" style="min-height:260px;">
          <div class="dashboard-widget-loading" id="chart-lead-status-loading">
            <div class="mino-skeleton" style="height:240px;border-radius:var(--mino-radius)"></div>
          </div>
          <div id="chartLeadStatus" class="d-none"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-card h-100">
        <div class="mino-card-header"><h3 class="mino-card-title">Monthly Leads</h3></div>
        <div class="chart-placeholder position-relative">
          <div class="dashboard-widget-loading" id="chart-monthly-leads-loading">
            <div class="mino-skeleton" style="height:260px;border-radius:var(--mino-radius)"></div>
          </div>
          <div id="chartMonthlyLeads" class="d-none"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-card h-100">
        <div class="mino-card-header"><h3 class="mino-card-title">Sales Pipeline</h3></div>
        <div class="chart-placeholder position-relative">
          <div class="dashboard-widget-loading" id="chart-pipeline-loading">
            <div class="mino-skeleton" style="height:260px;border-radius:var(--mino-radius)"></div>
          </div>
          <div id="chartSalesPipeline" class="d-none"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card h-100">
        <div class="mino-card-header"><h3 class="mino-card-title">Lead Sources</h3></div>
        <div class="chart-placeholder position-relative" style="min-height:240px;">
          <div class="dashboard-widget-loading" id="chart-sources-loading">
            <div class="mino-skeleton" style="height:220px;border-radius:var(--mino-radius)"></div>
          </div>
          <div id="chartLeadSources" class="d-none"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card h-100">
        <div class="mino-card-header"><h3 class="mino-card-title">Tasks Completion</h3></div>
        <div class="chart-placeholder position-relative" style="min-height:240px;">
          <div class="dashboard-widget-loading" id="chart-tasks-loading">
            <div class="mino-skeleton" style="height:220px;border-radius:var(--mino-radius)"></div>
          </div>
          <div id="chartTasksCompletion" class="d-none"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card h-100 d-none" id="chart-extra-wrap">
        <div class="mino-card-header"><h3 class="mino-card-title">Performance</h3></div>
        <div class="mino-card-body" id="chartPerfBars"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-4 mb-4">
    <?php if ($has('recent_activity')): ?>
    <div class="col-lg-4" data-widget="recent_activity" id="widget-activity">
      <div class="mino-card h-100">
        <div class="mino-card-header">
          <h3 class="mino-card-title">Recent Activity</h3>
        </div>
        <div class="mino-card-body position-relative" style="min-height:280px;">
          <div class="dashboard-widget-loading" id="activity-loading">
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="mino-skeleton mino-skeleton-text mb-3" style="width:<?php echo 70 + ($i * 5); ?>%"></div>
            <?php endfor; ?>
          </div>
          <ul class="mino-timeline d-none" id="activity-list"></ul>
          <div class="mino-empty d-none" id="activity-empty">
            <div class="mino-empty__icon"><i class="fas fa-clock-rotate-left"></i></div>
            <div class="mino-empty__title">No activity yet</div>
            <div class="mino-empty__text">Actions in your CRM will appear here.</div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($has('upcoming_followups')): ?>
    <div class="col-lg-4" data-widget="upcoming_followups" id="widget-followups">
      <div class="mino-card h-100">
        <div class="mino-card-header">
          <h3 class="mino-card-title">Upcoming Follow Ups</h3>
        </div>
        <div class="mino-card-body position-relative" style="min-height:280px;">
          <div class="dashboard-widget-loading" id="followups-loading">
            <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="mino-skeleton mino-skeleton-text mb-3"></div>
            <?php endfor; ?>
          </div>
          <div class="d-none" id="followups-list"></div>
          <div class="mino-empty d-none" id="followups-empty">
            <div class="mino-empty__icon"><i class="fas fa-phone"></i></div>
            <div class="mino-empty__title">No follow ups scheduled</div>
            <div class="mino-empty__text">Your upcoming calls and reminders will show here.</div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($has('quick_actions') || $has('upcoming_tasks') || $has('upcoming_meetings')): ?>
    <div class="col-lg-4">
      <?php if ($has('quick_actions')): ?>
      <div class="mino-card mb-4" data-widget="quick_actions" id="widget-quick-actions">
        <div class="mino-card-header"><h3 class="mino-card-title">Quick Actions</h3></div>
        <div class="mino-card-body position-relative" style="min-height:120px;">
          <div class="dashboard-widget-loading" id="quick-actions-loading">
            <div class="mino-skeleton" style="height:100px;border-radius:var(--mino-radius)"></div>
          </div>
          <div class="quick-actions-grid d-none" id="quick-actions-grid"></div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($has('upcoming_tasks')): ?>
      <div class="mino-card mb-4" data-widget="upcoming_tasks" id="widget-upcoming-tasks">
        <div class="mino-card-header"><h3 class="mino-card-title">Upcoming Tasks</h3></div>
        <div class="mino-card-body position-relative">
          <div class="dashboard-widget-loading" id="tasks-upcoming-loading">
            <div class="mino-skeleton mino-skeleton-text mb-2"></div>
            <div class="mino-skeleton mino-skeleton-text mb-2"></div>
          </div>
          <div class="d-none" id="tasks-upcoming-list"></div>
          <div class="mino-empty d-none py-4" id="tasks-upcoming-empty">
            <div class="mino-empty__title" style="font-size:var(--mino-fs-sm)">No upcoming tasks</div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($has('upcoming_meetings')): ?>
      <div class="mino-card" data-widget="upcoming_meetings" id="widget-meetings">
        <div class="mino-card-header"><h3 class="mino-card-title">Upcoming Meetings</h3></div>
        <div class="mino-card-body position-relative">
          <div class="dashboard-widget-loading" id="meetings-loading">
            <div class="mino-skeleton mino-skeleton-text mb-2"></div>
          </div>
          <div class="d-none" id="meetings-list"></div>
          <div class="mino-empty d-none py-4" id="meetings-empty">
            <div class="mino-empty__title" style="font-size:var(--mino-fs-sm)">No meetings scheduled</div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($has('birthdays')): ?>
  <div class="row g-4 mb-4" data-widget="birthdays" id="widget-birthdays">
    <div class="col-12">
      <div class="mino-card">
        <div class="mino-card-header"><h3 class="mino-card-title">Birthdays</h3></div>
        <div class="mino-card-body">
          <div class="row g-3 d-none" id="birthdays-list"></div>
          <div class="mino-empty d-none py-4" id="birthdays-empty">
            <div class="mino-empty__title" style="font-size:var(--mino-fs-sm)">No birthdays this week</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($has('table_leads')): ?>
  <div class="mino-table-wrap mb-4" data-widget="table_leads" id="widget-table-leads">
    <div class="mino-table-toolbar">
      <div>
        <h3 class="mino-card-title mb-0">Latest Leads</h3>
        <span class="mino-text-xs mino-text-muted" id="table-leads-sub">Loading…</span>
      </div>
    </div>
    <div class="table-responsive position-relative" style="min-height:200px;">
      <div class="dashboard-widget-loading p-4" id="table-leads-loading">
        <div class="mino-skeleton mino-skeleton-text mb-2"></div>
        <div class="mino-skeleton" style="height:160px;border-radius:var(--mino-radius)"></div>
      </div>
      <table class="mino-table w-100 d-none" id="tableLeads">
        <thead>
          <tr>
            <th>Lead</th>
            <th>Company</th>
            <th>Status</th>
            <th>Source</th>
            <th>Owner</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div class="mino-empty d-none p-4" id="table-leads-empty">
        <div class="mino-empty__icon"><i class="fas fa-bullseye"></i></div>
        <div class="mino-empty__title">No leads yet</div>
        <div class="mino-empty__text">Create your first lead to see it here.</div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($has('table_contacts')): ?>
  <div class="mino-table-wrap mb-4" data-widget="table_contacts" id="widget-table-contacts">
    <div class="mino-table-toolbar">
      <div>
        <h3 class="mino-card-title mb-0">Latest Contacts</h3>
        <span class="mino-text-xs mino-text-muted" id="table-contacts-sub">Loading…</span>
      </div>
    </div>
    <div class="table-responsive position-relative" style="min-height:200px;">
      <div class="dashboard-widget-loading p-4" id="table-contacts-loading">
        <div class="mino-skeleton" style="height:160px;border-radius:var(--mino-radius)"></div>
      </div>
      <table class="mino-table w-100 d-none" id="tableContacts">
        <thead>
          <tr>
            <th>Contact</th>
            <th>Company</th>
            <th>Status</th>
            <th>Source</th>
            <th>Owner</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div class="mino-empty d-none p-4" id="table-contacts-empty">
        <div class="mino-empty__icon"><i class="fas fa-address-book"></i></div>
        <div class="mino-empty__title">No contacts yet</div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($has('table_deals')): ?>
  <div class="mino-table-wrap mb-4" data-widget="table_deals" id="widget-table-deals">
    <div class="mino-table-toolbar">
      <div>
        <h3 class="mino-card-title mb-0">Latest Deals</h3>
        <span class="mino-text-xs mino-text-muted" id="table-deals-sub">Loading…</span>
      </div>
    </div>
    <div class="table-responsive position-relative" style="min-height:200px;">
      <div class="dashboard-widget-loading p-4" id="table-deals-loading">
        <div class="mino-skeleton" style="height:160px;border-radius:var(--mino-radius)"></div>
      </div>
      <table class="mino-table w-100 d-none" id="tableDeals">
        <thead>
          <tr>
            <th>Deal</th>
            <th>Company</th>
            <th>Status</th>
            <th>Value</th>
            <th>Owner</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div class="mino-empty d-none p-4" id="table-deals-empty">
        <div class="mino-empty__icon"><i class="fas fa-handshake"></i></div>
        <div class="mino-empty__title">No deals yet</div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($has('table_tasks')): ?>
  <div class="mino-table-wrap mb-4" data-widget="table_tasks" id="widget-table-tasks">
    <div class="mino-table-toolbar">
      <div>
        <h3 class="mino-card-title mb-0">Latest Tasks</h3>
        <span class="mino-text-xs mino-text-muted" id="table-tasks-sub">Loading…</span>
      </div>
    </div>
    <div class="table-responsive position-relative" style="min-height:200px;">
      <div class="dashboard-widget-loading p-4" id="table-tasks-loading">
        <div class="mino-skeleton" style="height:160px;border-radius:var(--mino-radius)"></div>
      </div>
      <table class="mino-table w-100 d-none" id="tableTasks">
        <thead>
          <tr>
            <th>Task</th>
            <th>Company</th>
            <th>Status</th>
            <th>Due</th>
            <th>Owner</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div class="mino-empty d-none p-4" id="table-tasks-empty">
        <div class="mino-empty__icon"><i class="fas fa-list-check"></i></div>
        <div class="mino-empty__title">No tasks yet</div>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>
