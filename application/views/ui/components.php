<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row g-4">
  <!-- Buttons -->
  <div class="col-12">
    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Buttons</h3></div>
      <div class="mino-card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
          <button type="button" class="btn btn-primary">Primary</button>
          <button type="button" class="btn btn-secondary">Secondary</button>
          <button type="button" class="btn btn-success">Success</button>
          <button type="button" class="btn btn-danger">Danger</button>
          <button type="button" class="btn btn-warning">Warning</button>
          <button type="button" class="btn btn-info">Info</button>
          <button type="button" class="btn btn-outline-primary">Outline</button>
          <button type="button" class="btn btn-soft-primary">Soft</button>
          <button type="button" class="btn btn-ghost">Ghost</button>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-primary btn-sm">Small</button>
          <button type="button" class="btn btn-primary">Default</button>
          <button type="button" class="btn btn-primary btn-lg">Large</button>
          <button type="button" class="btn btn-primary btn-icon"><i class="fas fa-plus"></i></button>
          <button type="button" class="btn btn-primary" disabled>Disabled</button>
          <button type="button" class="btn btn-primary" data-mino-toast data-title="Toast sent" data-icon="success"><i class="fas fa-bell"></i> Toast</button>
          <button type="button" class="btn btn-danger" data-mino-confirm data-title="Delete item?" data-text="This is a UI confirmation demo."><i class="fas fa-trash"></i> Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Alerts & Badges -->
  <div class="col-lg-6">
    <div class="mino-card h-100">
      <div class="mino-card-header"><h3 class="mino-card-title">Alerts</h3></div>
      <div class="mino-card-body">
        <div class="mino-alert mino-alert-primary"><i class="fas fa-circle-info"></i><div>Primary informational alert message.</div></div>
        <div class="mino-alert mino-alert-success"><i class="fas fa-circle-check"></i><div>Success — action completed successfully.</div></div>
        <div class="mino-alert mino-alert-warning"><i class="fas fa-triangle-exclamation"></i><div>Warning — please review before continuing.</div></div>
        <div class="mino-alert mino-alert-danger"><i class="fas fa-circle-xmark"></i><div>Danger — something went wrong.</div></div>
        <div class="mino-alert mino-alert-info mb-0"><i class="fas fa-lightbulb"></i><div>Info — tip for better conversion rates.</div></div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="mino-card h-100">
      <div class="mino-card-header"><h3 class="mino-card-title">Badges & Avatars</h3></div>
      <div class="mino-card-body">
        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="mino-badge mino-badge-primary">Primary</span>
          <span class="mino-badge mino-badge-success mino-badge-dot">Active</span>
          <span class="mino-badge mino-badge-danger mino-badge-dot">Lost</span>
          <span class="mino-badge mino-badge-warning mino-badge-dot">Pending</span>
          <span class="mino-badge mino-badge-info">Info</span>
          <span class="mino-badge mino-badge-secondary">Neutral</span>
        </div>
        <div class="d-flex align-items-center gap-3 mb-4">
          <span class="mino-avatar mino-avatar-sm">SM</span>
          <span class="mino-avatar">AD</span>
          <span class="mino-avatar mino-avatar-lg bg-success-soft text-success">JD</span>
          <span class="mino-avatar mino-avatar-xl bg-info-soft text-info">KL</span>
        </div>
        <div class="mino-avatar-stack">
          <span class="mino-avatar mino-avatar-sm">A</span>
          <span class="mino-avatar mino-avatar-sm bg-success-soft text-success">B</span>
          <span class="mino-avatar mino-avatar-sm bg-warning-soft text-warning">C</span>
          <span class="mino-avatar mino-avatar-sm bg-info-soft text-info">+5</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="col-lg-6">
    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Tabs</h3></div>
      <div class="mino-card-body mino-tabs">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOverview" type="button">Overview</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabActivity" type="button">Activity</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabNotes" type="button">Notes</button></li>
        </ul>
        <div class="tab-content pt-4">
          <div class="tab-pane fade show active" id="tabOverview">Account overview content for the selected record.</div>
          <div class="tab-pane fade" id="tabActivity">Recent activity timeline would appear here.</div>
          <div class="tab-pane fade" id="tabNotes">Internal notes and collaboration comments.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Accordion -->
  <div class="col-lg-6">
    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Accordion</h3></div>
      <div class="mino-card-body">
        <div class="accordion mino-accordion" id="demoAccordion">
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#acc1">Getting started</button></h2>
            <div id="acc1" class="accordion-collapse collapse show" data-bs-parent="#demoAccordion">
              <div class="accordion-body">Configure your pipeline stages and invite your team to Mino CRM.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc2">Import data</button></h2>
            <div id="acc2" class="accordion-collapse collapse" data-bs-parent="#demoAccordion">
              <div class="accordion-body">Import leads and contacts from CSV or popular CRMs.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc3">Automations</button></h2>
            <div id="acc3" class="accordion-collapse collapse" data-bs-parent="#demoAccordion">
              <div class="accordion-body">Set follow-up reminders and assignment rules.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal / Offcanvas / Dropdown -->
  <div class="col-lg-6">
    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Overlays</h3></div>
      <div class="mino-card-body">
        <div class="d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#demoModal"><i class="fas fa-window-maximize"></i> Modal</button>
          <button type="button" class="btn btn-secondary" data-bs-toggle="offcanvas" data-bs-target="#demoOffcanvas"><i class="fas fa-panel-right"></i> Offcanvas</button>
          <div class="dropdown">
            <button class="btn btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">Dropdown</button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#"><i class="fas fa-eye"></i> View</a></li>
              <li><a class="dropdown-item" href="#"><i class="fas fa-pen"></i> Edit</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash"></i> Delete</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Spinner / Skeleton / Empty -->
  <div class="col-lg-6">
    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Loading & Empty States</h3></div>
      <div class="mino-card-body">
        <div class="d-flex align-items-center gap-4 mb-4">
          <div class="mino-spinner"></div>
          <div class="mino-spinner mino-spinner-sm"></div>
          <div class="flex-grow-1">
            <div class="mino-skeleton mino-skeleton-title"></div>
            <div class="mino-skeleton mino-skeleton-text"></div>
            <div class="mino-skeleton mino-skeleton-text" style="width:80%"></div>
          </div>
        </div>
        <div class="mino-empty py-4">
          <div class="mino-empty__icon"><i class="fas fa-inbox"></i></div>
          <div class="mino-empty__title">No records yet</div>
          <p class="mino-empty__text">When you add leads or contacts, they will appear here.</p>
          <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add first lead</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Timeline -->
  <div class="col-12">
    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Timeline</h3></div>
      <div class="mino-card-body">
        <ul class="mino-timeline">
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">Lead qualified</div>
              <div>Moved to Qualified stage by Alex Davis</div>
              <div class="mino-timeline-time">Today, 9:42 AM</div>
            </div>
          </li>
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot success"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">Email opened</div>
              <div>Prospect opened pricing proposal</div>
              <div class="mino-timeline-time">Yesterday, 4:18 PM</div>
            </div>
          </li>
          <li class="mino-timeline-item">
            <span class="mino-timeline-dot info"></span>
            <div class="mino-timeline-content">
              <div class="mino-timeline-title">Lead created</div>
              <div>Imported from website form</div>
              <div class="mino-timeline-time">Mar 12, 2026</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Demo Modal -->
<div class="modal fade" id="demoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Full name <span class="required">*</span></label>
          <input type="text" class="form-control" placeholder="Jane Doe">
        </div>
        <div class="form-group mb-0">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" placeholder="jane@company.com">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-mino-toast data-title="Lead created">Save Lead</button>
      </div>
    </div>
  </div>
</div>

<!-- Demo Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="demoOffcanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Lead Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <p class="mino-text-secondary">Side panel for quick preview of a lead or deal without leaving the list view.</p>
    <div class="d-flex align-items-center gap-3 mb-4">
      <span class="mino-avatar mino-avatar-lg">EW</span>
      <div>
        <div class="fw-semibold">Emma Watson</div>
        <div class="mino-text-sm mino-text-muted">BrightSoft</div>
      </div>
    </div>
    <span class="mino-badge mino-badge-success mino-badge-dot">Qualified</span>
  </div>
</div>
