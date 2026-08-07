<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_trashed = !empty($lead->deleted_at);
?>
<div id="mino-leads" class="leads-page lead-profile"
  data-view="profile"
  data-lead-id="<?php echo (int) $lead->id; ?>">

  <?php if ($is_trashed): ?>
  <div class="mino-alert mino-alert-warning mb-3">
    <i class="fas fa-trash"></i>
    <div>This lead is in the trash.
      <?php if (!empty($can_delete)): ?>
        <button type="button" class="btn btn-sm btn-secondary ms-2" id="btnRestoreLead">Restore</button>
      <?php endif; ?>
      <?php if (!empty($is_owner)): ?>
        <button type="button" class="btn btn-sm btn-danger ms-1" id="btnForceDeleteLead">Delete permanently</button>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="mino-card mb-4">
        <div class="mino-card-body text-center">
          <span class="mino-avatar mb-3" style="width:64px;height:64px;font-size:1.25rem"><?php echo html_escape(user_initials(trim($lead->first_name.' '.$lead->last_name) ?: $lead->title)); ?></span>
          <h2 class="h5 mb-1"><?php echo html_escape($lead->title); ?></h2>
          <div class="mino-text-sm mino-text-muted mb-2"><?php echo html_escape(trim($lead->first_name.' '.$lead->last_name)); ?></div>
          <?php if ($lead->status_name): ?>
            <span class="mino-badge" style="background:<?php echo html_escape($lead->status_color); ?>22;color:<?php echo html_escape($lead->status_color); ?>"><?php echo html_escape($lead->status_name); ?></span>
          <?php endif; ?>
        </div>
        <div class="mino-card-body border-top">
          <div class="lead-info-row"><span>Lead type</span><strong><?php
            $lt = isset($lead->lead_type) ? $lead->lead_type : 'clinic';
            echo html_escape(ucfirst($lt));
          ?></strong></div>
          <div class="lead-info-row"><span>Email</span><strong><?php echo html_escape($lead->email ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Phone</span><strong><?php echo html_escape($lead->phone ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Mobile</span><strong><?php echo html_escape($lead->mobile ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Branch</span><strong><?php echo html_escape(isset($lead->branch) ? ($lead->branch ?: '—') : '—'); ?></strong></div>
          <?php if (isset($lead->lead_type) && $lead->lead_type === 'clinic'): ?>
          <div class="lead-info-row"><span>Treatment</span><strong><?php echo html_escape($lead->treatment ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Appointment</span><strong><?php
            $appt = trim(($lead->appointment_date ?: '') . ' ' . ($lead->appointment_time ? substr($lead->appointment_time, 0, 5) : ''));
            echo html_escape($appt ?: '—');
          ?></strong></div>
          <?php elseif (isset($lead->lead_type) && $lead->lead_type === 'academy'): ?>
          <div class="lead-info-row"><span>Course</span><strong><?php echo html_escape($lead->course ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Preferred batch</span><strong><?php echo html_escape($lead->preferred_batch ?: '—'); ?></strong></div>
          <?php endif; ?>
          <div class="lead-info-row"><span>Source</span><strong><?php echo html_escape($lead->source_name ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Priority</span><strong><?php echo html_escape($lead->priority_name ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Value</span><strong><?php echo $lead->estimated_value !== null ? '$'.number_format((float)$lead->estimated_value, 2) : '—'; ?></strong></div>
          <div class="lead-info-row"><span>Close date</span><strong><?php echo html_escape($lead->expected_close_date ?: '—'); ?></strong></div>
        </div>
      </div>

      <div class="mino-card mb-4">
        <div class="mino-card-header"><h3 class="mino-card-title">Company</h3></div>
        <div class="mino-card-body">
          <div class="lead-info-row"><span>Name</span><strong><?php echo html_escape($lead->company_name ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Website</span><strong><?php echo $lead->website ? '<a href="'.html_escape($lead->website).'" target="_blank" rel="noopener">'.html_escape($lead->website).'</a>' : '—'; ?></strong></div>
          <div class="lead-info-row"><span>Address</span><strong><?php echo html_escape($lead->address ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>City</span><strong><?php echo html_escape(trim(implode(', ', array_filter(array($lead->city,$lead->state,$lead->country)))) ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Postal</span><strong><?php echo html_escape($lead->postal_code ?: '—'); ?></strong></div>
        </div>
      </div>

      <div class="mino-card mb-4">
        <div class="mino-card-header"><h3 class="mino-card-title">Assigned User</h3></div>
        <div class="mino-card-body d-flex align-items-center gap-3">
          <span class="mino-avatar mino-avatar-sm"><?php echo html_escape(user_initials($lead->assignee_name ?: '?')); ?></span>
          <div>
            <div class="fw-semibold"><?php echo html_escape($lead->assignee_name ?: 'Unassigned'); ?></div>
            <div class="mino-text-xs mino-text-muted"><?php echo html_escape($lead->assignee_email ?: ''); ?></div>
          </div>
        </div>
        <?php if (!empty($can_edit) && !$is_trashed): ?>
        <div class="mino-card-body border-top">
          <select class="form-select form-select-sm" id="profileAssignee">
            <option value="">Unassigned</option>
            <?php foreach ($users as $u): ?>
              <option value="<?php echo (int)$u->id; ?>" <?php echo ((int)$lead->assigned_to === (int)$u->id) ? 'selected' : ''; ?>><?php echo html_escape($u->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
      </div>

      <div class="mino-card mb-4">
        <div class="mino-card-header"><h3 class="mino-card-title">Pipeline</h3></div>
        <div class="mino-card-body">
          <div class="lead-info-row"><span>Pipeline</span><strong><?php echo html_escape($lead->pipeline_name ?: '—'); ?></strong></div>
          <div class="lead-info-row"><span>Stage</span><strong><?php echo html_escape($lead->stage_name ?: '—'); ?></strong></div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <ul class="nav nav-tabs mb-3" id="leadProfileTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabTimeline" type="button">Timeline</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabNotes" type="button">Notes</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAttachments" type="button">Attachments</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabTasks" type="button">Tasks</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFollowups" type="button">Follow Ups</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDeals" type="button">Deals</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAbout" type="button">Details</button></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="tabTimeline">
          <div class="mino-card">
            <div class="mino-card-header"><h3 class="mino-card-title">Activity Timeline</h3></div>
            <div class="mino-card-body">
              <ul class="mino-timeline" id="leadTimelineList">
                <li class="mino-text-muted">Loading…</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tabNotes">
          <div class="mino-card mb-3">
            <?php if (!empty($can_edit) && !$is_trashed): ?>
            <div class="mino-card-body">
              <div id="noteEditor" class="lead-note-editor form-control" contenteditable="true" data-placeholder="Write a note…"></div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <label class="form-check mb-0">
                  <input type="checkbox" class="form-check-input" id="notePinned"> Pin note
                </label>
                <button type="button" class="btn btn-sm btn-primary" id="btnAddNote">Add Note</button>
              </div>
            </div>
            <?php endif; ?>
          </div>
          <div id="leadNotesList"></div>
        </div>

        <div class="tab-pane fade" id="tabAttachments">
          <div class="mino-card">
            <div class="mino-card-header">
              <h3 class="mino-card-title">Attachments</h3>
              <?php if (!empty($can_edit) && !$is_trashed): ?>
              <label class="btn btn-sm btn-soft-primary mb-0">
                <i class="fas fa-upload"></i> Upload
                <input type="file" id="leadAttachInput" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.txt">
              </label>
              <?php endif; ?>
            </div>
            <div class="mino-card-body" id="leadAttachmentsList"></div>
          </div>
        </div>

        <div class="tab-pane fade" id="tabTasks">
          <div class="mino-empty">
            <div class="mino-empty__icon"><i class="fas fa-list-check"></i></div>
            <div class="mino-empty__title">Tasks coming soon</div>
            <div class="mino-empty__text">Tasks module will attach here when available.</div>
          </div>
        </div>

        <div class="tab-pane fade" id="tabFollowups">
          <div class="mino-empty">
            <div class="mino-empty__icon"><i class="fas fa-phone"></i></div>
            <div class="mino-empty__title">Follow ups coming soon</div>
            <div class="mino-empty__text">Follow-up scheduling will appear here.</div>
          </div>
        </div>

        <div class="tab-pane fade" id="tabDeals">
          <div class="mino-empty">
            <div class="mino-empty__icon"><i class="fas fa-handshake"></i></div>
            <div class="mino-empty__title">Deals coming soon</div>
            <div class="mino-empty__text">Related deals will link here when the Deals module is built.</div>
          </div>
        </div>

        <div class="tab-pane fade" id="tabAbout">
          <div class="mino-card">
            <div class="mino-card-header"><h3 class="mino-card-title">Lead Information</h3></div>
            <div class="mino-card-body">
              <p class="mb-0"><?php echo nl2br(html_escape($lead->description ?: 'No description.')); ?></p>
              <hr>
              <div class="lead-info-row"><span>Created</span><strong><?php echo html_escape($lead->created_at); ?> · <?php echo html_escape($lead->creator_name ?: ''); ?></strong></div>
              <div class="lead-info-row"><span>Updated</span><strong><?php echo html_escape($lead->updated_at ?: '—'); ?></strong></div>
              <div class="mt-3" id="profileTags">
                <?php
                $tag_map = array();
                foreach ($tags as $t) { $tag_map[(int)$t->id] = $t; }
                foreach ($tag_ids as $tid):
                  if (!isset($tag_map[$tid])) continue;
                  $t = $tag_map[$tid];
                ?>
                  <span class="mino-badge me-1" style="background:<?php echo html_escape($t->color); ?>22;color:<?php echo html_escape($t->color); ?>"><?php echo html_escape($t->name); ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('partials/lead_form_modal'); ?>

<script>
window.MINO_LEADS = {
  canCreate: false,
  canEdit: <?php echo !empty($can_edit) ? 'true' : 'false'; ?>,
  canDelete: <?php echo !empty($can_delete) ? 'true' : 'false'; ?>,
  canExport: false,
  canImport: false,
  isOwner: <?php echo !empty($is_owner) ? 'true' : 'false'; ?>,
  leadId: <?php echo (int) $lead->id; ?>,
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
