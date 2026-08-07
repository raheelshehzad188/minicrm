<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="mino-leads" class="leads-page"
  data-view="list"
  data-csrf-name="<?php echo html_escape($this->security->get_csrf_token_name()); ?>">

  <div class="mino-alert mino-alert-info mb-3">
    <i class="fas fa-building"></i>
    <div>Leads belong to <strong><?php echo html_escape(current_org_name()); ?></strong> only. Visibility follows your role.</div>
  </div>

  <!-- Filters -->
  <div class="mino-card mb-3">
    <div class="mino-card-body">
      <div class="row g-2 align-items-end" id="leadFilters">
        <div class="col-md-3">
          <label class="form-label mino-text-xs">Search</label>
          <input type="search" class="form-control form-control-sm" id="leadSearch" placeholder="Name, company, email, phone…">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Lead type</label>
          <select class="form-select form-select-sm" id="filterLeadType">
            <option value="">All</option>
            <?php
            $lead_types = isset($lead_types) ? $lead_types : array('clinic' => 'Clinic', 'academy' => 'Academy');
            foreach ($lead_types as $slug => $label):
            ?>
              <option value="<?php echo html_escape($slug); ?>"><?php echo html_escape($label); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Status</label>
          <select class="form-select form-select-sm" id="filterStatus">
            <option value="">All</option>
            <?php foreach ($statuses as $st): ?>
              <option value="<?php echo (int) $st->id; ?>"><?php echo html_escape($st->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Source</label>
          <select class="form-select form-select-sm" id="filterSource">
            <option value="">All</option>
            <?php foreach ($sources as $src): ?>
              <option value="<?php echo (int) $src->id; ?>"><?php echo html_escape($src->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Assignee</label>
          <select class="form-select form-select-sm" id="filterAssignee">
            <option value="">All</option>
            <?php foreach ($users as $u): ?>
              <option value="<?php echo (int) $u->id; ?>"><?php echo html_escape($u->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Priority</label>
          <select class="form-select form-select-sm" id="filterPriority">
            <option value="">All</option>
            <?php foreach ($priorities as $p): ?>
              <option value="<?php echo (int) $p->id; ?>"><?php echo html_escape($p->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-1">
          <button type="button" class="btn btn-sm btn-ghost w-100" id="btnToggleAdvanced" title="More filters"><i class="fas fa-sliders"></i></button>
        </div>
      </div>

      <div class="row g-2 mt-2 d-none" id="leadAdvancedFilters">
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Pipeline</label>
          <select class="form-select form-select-sm" id="filterPipeline">
            <option value="">All</option>
            <?php foreach ($pipelines as $p): ?>
              <option value="<?php echo (int) $p->id; ?>"><?php echo html_escape($p->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Stage</label>
          <select class="form-select form-select-sm" id="filterStage">
            <option value="">All</option>
            <?php foreach ($stages as $s): ?>
              <option value="<?php echo (int) $s->id; ?>" data-pipeline="<?php echo (int) $s->pipeline_id; ?>"><?php echo html_escape($s->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Tag</label>
          <select class="form-select form-select-sm" id="filterTag">
            <option value="">All</option>
            <?php foreach ($tags as $t): ?>
              <option value="<?php echo (int) $t->id; ?>"><?php echo html_escape($t->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">From</label>
          <input type="date" class="form-control form-control-sm" id="filterDateFrom">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">To</label>
          <input type="date" class="form-control form-control-sm" id="filterDateTo">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">Trash</label>
          <select class="form-select form-select-sm" id="filterTrashed">
            <option value="">Active</option>
            <option value="1">Trash only</option>
          </select>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2 mt-1">
          <button type="button" class="btn btn-sm btn-soft-primary" id="btnSaveFilter"><i class="fas fa-bookmark"></i> Save filter</button>
          <select class="form-select form-select-sm" id="savedFilters" style="max-width:220px">
            <option value="">Saved filters…</option>
          </select>
          <button type="button" class="btn btn-sm btn-ghost" id="btnClearFilters">Clear</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bulk bar -->
  <div class="lead-bulk-bar d-none mb-3" id="leadBulkBar">
    <span class="fw-semibold"><span id="bulkCount">0</span> selected</span>
    <div class="d-flex flex-wrap gap-2">
      <?php if (!empty($can_edit)): ?>
      <button type="button" class="btn btn-sm btn-secondary" data-bulk="assign">Assign</button>
      <button type="button" class="btn btn-sm btn-secondary" data-bulk="status">Status</button>
      <button type="button" class="btn btn-sm btn-secondary" data-bulk="pipeline">Pipeline</button>
      <button type="button" class="btn btn-sm btn-secondary" data-bulk="stage">Stage</button>
      <?php endif; ?>
      <?php if (!empty($can_delete)): ?>
      <button type="button" class="btn btn-sm btn-danger" data-bulk="delete">Delete</button>
      <?php endif; ?>
    </div>
  </div>

  <div class="mino-table-wrap">
    <div class="mino-table-toolbar">
      <div>
        <h3 class="mino-card-title mb-0">All Leads</h3>
        <span class="mino-text-xs mino-text-muted" id="leadCountLabel">Loading…</span>
      </div>
      <div class="d-flex gap-2">
        <div class="dropdown">
          <button class="btn btn-sm btn-ghost" data-bs-toggle="dropdown"><i class="fas fa-columns"></i> Columns</button>
          <div class="dropdown-menu dropdown-menu-end p-2" id="leadColumnToggles" style="min-width:180px"></div>
        </div>
      </div>
    </div>
    <div class="table-responsive p-3 pt-0">
      <table class="mino-table w-100" id="leadsTable">
        <thead>
          <tr>
            <th style="width:36px"><input type="checkbox" class="form-check-input" id="leadCheckAll"></th>
            <th>Lead</th>
            <th>Type</th>
            <th>Company</th>
            <th>Status</th>
            <th>Source</th>
            <th>Assignee</th>
            <th>Priority</th>
            <th>Value</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<?php $this->load->view('partials/lead_form_modal'); ?>
<?php $this->load->view('partials/lead_import_modal'); ?>

<script>
window.MINO_LEADS = {
  canCreate: <?php echo !empty($can_create) ? 'true' : 'false'; ?>,
  canEdit: <?php echo !empty($can_edit) ? 'true' : 'false'; ?>,
  canDelete: <?php echo !empty($can_delete) ? 'true' : 'false'; ?>,
  canExport: <?php echo !empty($can_export) ? 'true' : 'false'; ?>,
  canImport: <?php echo !empty($can_import) ? 'true' : 'false'; ?>,
  isOwner: <?php echo !empty($is_owner) ? 'true' : 'false'; ?>,
  urls: <?php echo json_encode($urls); ?>,
  lookups: {
    lead_types: <?php echo json_encode(isset($lead_types) ? $lead_types : array('clinic'=>'Clinic','academy'=>'Academy')); ?>,
    statuses: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name,'color'=>$r->color); }, $statuses)); ?>,
    sources: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name); }, $sources)); ?>,
    pipelines: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name); }, $pipelines)); ?>,
    stages: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name,'pipeline_id'=>(int)$r->pipeline_id); }, $stages)); ?>,
    priorities: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name); }, $priorities)); ?>,
    tags: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name,'color'=>$r->color); }, $tags)); ?>,
    users: <?php echo json_encode(array_map(function($r){ return array('id'=>(int)$r->id,'name'=>$r->name); }, $users)); ?>
  }
};
</script>
