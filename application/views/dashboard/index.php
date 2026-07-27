<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Stat Cards -->
<div class="stat-grid stagger-children">
  <div class="stat-card stat-card--primary">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Total Leads</div>
        <p class="stat-card__value">2,847</p>
        <div class="stat-card__meta"><span class="up"><i class="fas fa-arrow-up"></i> 12.5%</span> vs last month</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-bullseye"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--info">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">New Leads</div>
        <p class="stat-card__value">186</p>
        <div class="stat-card__meta"><span class="up"><i class="fas fa-arrow-up"></i> 8.2%</span> this week</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-user-plus"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--success">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Contacts</div>
        <p class="stat-card__value">1,254</p>
        <div class="stat-card__meta"><span class="up"><i class="fas fa-arrow-up"></i> 3.1%</span> vs last month</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-address-book"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--warning">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Deals</div>
        <p class="stat-card__value">342</p>
        <div class="stat-card__meta"><span class="down"><i class="fas fa-arrow-down"></i> 2.4%</span> vs last month</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-handshake"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--success">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Revenue</div>
        <p class="stat-card__value">$128k</p>
        <div class="stat-card__meta"><span class="up"><i class="fas fa-arrow-up"></i> 18.7%</span> this quarter</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-dollar-sign"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--primary">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Follow Ups</div>
        <p class="stat-card__value">47</p>
        <div class="stat-card__meta">12 due today</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-phone"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--info">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Tasks</div>
        <p class="stat-card__value">89</p>
        <div class="stat-card__meta">23 open</div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-list-check"></i></div>
    </div>
  </div>

  <div class="stat-card stat-card--warning">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="stat-card__label">Performance</div>
        <p class="stat-card__value">94%</p>
        <div class="stat-card__meta"><span class="up"><i class="fas fa-arrow-up"></i> On track</span></div>
      </div>
      <div class="stat-card__icon"><i class="fas fa-chart-line"></i></div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Sales Chart -->
  <div class="col-lg-8">
    <div class="chart-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Sales Overview</h3>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-ghost">Monthly</button>
          <button type="button" class="btn btn-sm btn-soft-primary">Yearly</button>
        </div>
      </div>
      <div class="chart-placeholder">
        <div id="salesChart"></div>
      </div>
    </div>
  </div>

  <!-- Performance Donut -->
  <div class="col-lg-4">
    <div class="chart-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Deal Pipeline</h3>
      </div>
      <div class="chart-placeholder" style="min-height: 260px;">
        <div id="performanceChart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Recent Activity -->
  <div class="col-lg-4">
    <div class="mino-card h-100">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Recent Activity</h3>
        <a href="#" class="mino-text-sm">View all</a>
      </div>
      <div class="mino-card-body">
        <ul class="mino-timeline">
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">New lead created</div>
              <div>Emma Watson from BrightSoft</div>
              <div class="mino-timeline-time">10 minutes ago</div>
            </div>
          </li>
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot success"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">Deal won — $24,000</div>
              <div>Acme Industries closed by Alex</div>
              <div class="mino-timeline-time">1 hour ago</div>
            </div>
          </li>
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot warning"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">Follow-up reminder</div>
              <div>Call scheduled with TechNova</div>
              <div class="mino-timeline-time">3 hours ago</div>
            </div>
          </li>
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot info"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">Task completed</div>
              <div>Proposal sent to Horizon Ltd</div>
              <div class="mino-timeline-time">Yesterday</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Upcoming Follow-ups -->
  <div class="col-lg-4">
    <div class="mino-card h-100">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Upcoming Follow-ups</h3>
        <a href="#" class="mino-text-sm">Calendar</a>
      </div>
      <div class="mino-card-body">
        <div class="followup-item">
          <div class="followup-item__time">10:00<br>AM</div>
          <div class="followup-item__body">
            <div class="followup-item__title">Call with Sarah Mitchell</div>
            <div class="followup-item__meta">CloudSync · Discovery call</div>
          </div>
          <span class="mino-badge mino-badge-primary">Call</span>
        </div>
        <div class="followup-item">
          <div class="followup-item__time">01:30<br>PM</div>
          <div class="followup-item__body">
            <div class="followup-item__title">Demo — Product walkthrough</div>
            <div class="followup-item__meta">Nexus Labs · Virtual</div>
          </div>
          <span class="mino-badge mino-badge-info">Demo</span>
        </div>
        <div class="followup-item">
          <div class="followup-item__time">04:00<br>PM</div>
          <div class="followup-item__body">
            <div class="followup-item__title">Send revised proposal</div>
            <div class="followup-item__meta">Peak Retail · Email</div>
          </div>
          <span class="mino-badge mino-badge-warning">Task</span>
        </div>
        <div class="followup-item">
          <div class="followup-item__time">Tomorrow</div>
          <div class="followup-item__body">
            <div class="followup-item__title">Contract review meeting</div>
            <div class="followup-item__meta">Verde Corp · On-site</div>
          </div>
          <span class="mino-badge mino-badge-success">Meet</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions + Performance -->
  <div class="col-lg-4">
    <div class="mino-card mb-4">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Quick Actions</h3>
      </div>
      <div class="mino-card-body">
        <div class="quick-actions-grid">
          <a href="#" class="quick-action">
            <span class="quick-action__icon"><i class="fas fa-user-plus"></i></span>
            <span class="quick-action__label">Add Lead</span>
          </a>
          <a href="#" class="quick-action">
            <span class="quick-action__icon"><i class="fas fa-handshake"></i></span>
            <span class="quick-action__label">New Deal</span>
          </a>
          <a href="#" class="quick-action">
            <span class="quick-action__icon"><i class="fas fa-envelope"></i></span>
            <span class="quick-action__label">Send Email</span>
          </a>
          <a href="#" class="quick-action">
            <span class="quick-action__icon"><i class="fas fa-calendar-plus"></i></span>
            <span class="quick-action__label">Schedule</span>
          </a>
        </div>
      </div>
    </div>

    <div class="mino-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Team Performance</h3>
      </div>
      <div class="mino-card-body">
        <div class="perf-bar">
          <div class="perf-bar__label"><span>Lead Conversion</span><span>78%</span></div>
          <div class="perf-bar__track"><div class="perf-bar__fill" style="width:78%"></div></div>
        </div>
        <div class="perf-bar">
          <div class="perf-bar__label"><span>Deal Win Rate</span><span>64%</span></div>
          <div class="perf-bar__track"><div class="perf-bar__fill success" style="width:64%"></div></div>
        </div>
        <div class="perf-bar">
          <div class="perf-bar__label"><span>Follow-up SLA</span><span>91%</span></div>
          <div class="perf-bar__track"><div class="perf-bar__fill info" style="width:91%"></div></div>
        </div>
        <div class="perf-bar">
          <div class="perf-bar__label"><span>Task Completion</span><span>55%</span></div>
          <div class="perf-bar__track"><div class="perf-bar__fill warning" style="width:55%"></div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Latest Leads Table -->
<div class="mino-table-wrap">
  <div class="mino-table-toolbar">
    <div>
      <h3 class="mino-card-title mb-0">Latest Leads</h3>
      <span class="mino-text-xs mino-text-muted">Static sample data for UI preview</span>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-sm btn-secondary"><i class="fas fa-filter"></i> Filter</button>
      <button type="button" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Lead</button>
    </div>
  </div>
  <div class="table-responsive">
    <table class="mino-table w-100">
      <thead>
        <tr>
          <th style="width:40px"><input type="checkbox" class="form-check-input" data-mino-check-all=".lead-check"></th>
          <th>Lead</th>
          <th>Company</th>
          <th>Status</th>
          <th>Source</th>
          <th>Owner</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" class="form-check-input lead-check"></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span class="mino-avatar mino-avatar-sm">EW</span>
              <div>
                <div class="fw-semibold">Emma Watson</div>
                <div class="mino-text-xs mino-text-muted">emma@brightsoft.io</div>
              </div>
            </div>
          </td>
          <td>BrightSoft</td>
          <td><span class="mino-badge mino-badge-success mino-badge-dot">Qualified</span></td>
          <td>Website</td>
          <td>Alex Davis</td>
          <td>
            <div class="table-actions">
              <button type="button" class="btn btn-sm btn-ghost btn-icon" title="View"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon" title="Edit"><i class="fas fa-pen"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon text-danger" title="Delete"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" class="form-check-input lead-check"></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span class="mino-avatar mino-avatar-sm bg-info-soft text-info">JL</span>
              <div>
                <div class="fw-semibold">James Lee</div>
                <div class="mino-text-xs mino-text-muted">james@nexus.dev</div>
              </div>
            </div>
          </td>
          <td>Nexus Labs</td>
          <td><span class="mino-badge mino-badge-primary mino-badge-dot">New</span></td>
          <td>Referral</td>
          <td>Sarah Kim</td>
          <td>
            <div class="table-actions">
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-pen"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon text-danger"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" class="form-check-input lead-check"></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span class="mino-avatar mino-avatar-sm bg-warning-soft text-warning">OP</span>
              <div>
                <div class="fw-semibold">Olivia Park</div>
                <div class="mino-text-xs mino-text-muted">olivia@peak.co</div>
              </div>
            </div>
          </td>
          <td>Peak Retail</td>
          <td><span class="mino-badge mino-badge-warning mino-badge-dot">Contacted</span></td>
          <td>LinkedIn</td>
          <td>Mike Ross</td>
          <td>
            <div class="table-actions">
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-pen"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon text-danger"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" class="form-check-input lead-check"></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span class="mino-avatar mino-avatar-sm bg-danger-soft text-danger">DR</span>
              <div>
                <div class="fw-semibold">Daniel Ruiz</div>
                <div class="mino-text-xs mino-text-muted">daniel@verde.com</div>
              </div>
            </div>
          </td>
          <td>Verde Corp</td>
          <td><span class="mino-badge mino-badge-danger mino-badge-dot">Cold</span></td>
          <td>Cold Call</td>
          <td>Alex Davis</td>
          <td>
            <div class="table-actions">
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-pen"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon text-danger"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" class="form-check-input lead-check"></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span class="mino-avatar mino-avatar-sm bg-success-soft text-success">AC</span>
              <div>
                <div class="fw-semibold">Ava Chen</div>
                <div class="mino-text-xs mino-text-muted">ava@horizon.app</div>
              </div>
            </div>
          </td>
          <td>Horizon Ltd</td>
          <td><span class="mino-badge mino-badge-info mino-badge-dot">Proposal</span></td>
          <td>Webinar</td>
          <td>Sarah Kim</td>
          <td>
            <div class="table-actions">
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-eye"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon"><i class="fas fa-pen"></i></button>
              <button type="button" class="btn btn-sm btn-ghost btn-icon text-danger"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="mino-table-footer">
    <span class="mino-text-sm mino-text-muted">Showing 1–5 of 186 leads</span>
    <ul class="mino-pagination">
      <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
    </ul>
  </div>
</div>
