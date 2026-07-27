<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form id="leadForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" id="leadId" value="">
        <input type="hidden" name="force_duplicate" id="forceDuplicate" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="leadModalTitle">Add Lead</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item"><button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabLeadInfo">Lead</button></li>
            <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLeadCompany">Company</button></li>
            <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLeadPipeline">Pipeline</button></li>
            <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLeadMore">More</button></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="tabLeadInfo">
              <div class="row g-3">
                <div class="col-md-12">
                  <label class="form-label">Lead title <span class="required">*</span></label>
                  <input type="text" class="form-control" name="title" id="leadTitle" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">First name</label>
                  <input type="text" class="form-control" name="first_name" id="leadFirstName">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last name</label>
                  <input type="text" class="form-control" name="last_name" id="leadLastName">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" id="leadEmail">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Phone</label>
                  <input type="text" class="form-control" name="phone" id="leadPhone">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Mobile</label>
                  <input type="text" class="form-control" name="mobile" id="leadMobile">
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="tabLeadCompany">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Company name</label>
                  <input type="text" class="form-control" name="company_name" id="leadCompany">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Website</label>
                  <input type="text" class="form-control" name="website" id="leadWebsite" placeholder="https://">
                </div>
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <input type="text" class="form-control" name="address" id="leadAddress">
                </div>
                <div class="col-md-3">
                  <label class="form-label">City</label>
                  <input type="text" class="form-control" name="city" id="leadCity">
                </div>
                <div class="col-md-3">
                  <label class="form-label">State</label>
                  <input type="text" class="form-control" name="state" id="leadState">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Country</label>
                  <input type="text" class="form-control" name="country" id="leadCountry">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Postal code</label>
                  <input type="text" class="form-control" name="postal_code" id="leadPostal">
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="tabLeadPipeline">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Status</label>
                  <select class="form-select" name="lead_status_id" id="leadStatus">
                    <?php foreach ($statuses as $st): ?>
                      <option value="<?php echo (int) $st->id; ?>"><?php echo html_escape($st->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Source</label>
                  <select class="form-select" name="lead_source_id" id="leadSource">
                    <option value="">—</option>
                    <?php foreach ($sources as $src): ?>
                      <option value="<?php echo (int) $src->id; ?>"><?php echo html_escape($src->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Priority</label>
                  <select class="form-select" name="priority_id" id="leadPriority">
                    <option value="">—</option>
                    <?php foreach ($priorities as $p): ?>
                      <option value="<?php echo (int) $p->id; ?>"><?php echo html_escape($p->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Pipeline</label>
                  <select class="form-select" name="pipeline_id" id="leadPipeline">
                    <?php foreach ($pipelines as $p): ?>
                      <option value="<?php echo (int) $p->id; ?>"><?php echo html_escape($p->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Stage</label>
                  <select class="form-select" name="stage_id" id="leadStage">
                    <?php foreach ($stages as $s): ?>
                      <option value="<?php echo (int) $s->id; ?>" data-pipeline="<?php echo (int) $s->pipeline_id; ?>"><?php echo html_escape($s->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Assigned salesperson</label>
                  <select class="form-select" name="assigned_to" id="leadAssignee">
                    <option value="">Unassigned</option>
                    <?php foreach ($users as $u): ?>
                      <option value="<?php echo (int) $u->id; ?>"><?php echo html_escape($u->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Estimated value</label>
                  <input type="number" step="0.01" class="form-control" name="estimated_value" id="leadValue">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Expected closing date</label>
                  <input type="date" class="form-control" name="expected_close_date" id="leadCloseDate">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tags</label>
                  <select class="form-select mino-select2" name="tag_ids[]" id="leadTags" multiple>
                    <?php foreach ($tags as $t): ?>
                      <option value="<?php echo (int) $t->id; ?>"><?php echo html_escape($t->name); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="tabLeadMore">
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" id="leadDescription" rows="5"></textarea>
              </div>
              <?php if (!empty($custom_fields)): ?>
              <div class="row g-3" id="leadCustomFields">
                <?php foreach ($custom_fields as $cf): ?>
                <div class="col-md-6">
                  <label class="form-label"><?php echo html_escape($cf->name); ?></label>
                  <input type="text" class="form-control" name="custom_fields[<?php echo (int) $cf->id; ?>]" data-cf="<?php echo (int) $cf->id; ?>">
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btnSaveLead">Save Lead</button>
        </div>
      </form>
    </div>
  </div>
</div>
