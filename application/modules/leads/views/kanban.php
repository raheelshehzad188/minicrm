<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="mino-leads" class="leads-page" data-view="kanban">
  <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
    <input type="search" class="form-control form-control-sm" id="leadSearch" placeholder="Search leads…" style="max-width:220px">
    <select class="form-select form-select-sm" id="filterAssignee" style="max-width:180px">
      <option value="">All assignees</option>
      <?php foreach ($users as $u): ?>
        <option value="<?php echo (int) $u->id; ?>"><?php echo html_escape($u->name); ?></option>
      <?php endforeach; ?>
    </select>
    <button type="button" class="btn btn-sm btn-ghost" id="btnRefreshKanban"><i class="fas fa-rotate"></i></button>
  </div>

  <div class="lead-kanban" id="leadKanban">
    <div class="mino-skeleton" style="height:320px;border-radius:var(--mino-radius)"></div>
  </div>
</div>

<?php $this->load->view('partials/lead_form_modal'); ?>

<script>
window.MINO_LEADS = {
  canCreate: <?php echo !empty($can_create) ? 'true' : 'false'; ?>,
  canEdit: <?php echo !empty($can_edit) ? 'true' : 'false'; ?>,
  canDelete: false,
  canExport: false,
  canImport: false,
  isOwner: false,
  urls: <?php echo json_encode($urls); ?>,
  lookups: {
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
