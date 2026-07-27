<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mino-alert mino-alert-info">
  <i class="fas fa-circle-info"></i>
  <div>Reusable form patterns for all CRM modules. No submit handlers — UI foundation only.</div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="mino-card">
      <div class="mino-card-header">
        <h3 class="mino-card-title">Lead Form</h3>
        <span class="mino-badge mino-badge-secondary">Example</span>
      </div>
      <div class="mino-card-body">
        <form class="row g-3" onsubmit="return false;">
          <div class="col-md-6">
            <label class="form-label" for="firstName">First name <span class="required">*</span></label>
            <input type="text" class="form-control" id="firstName" placeholder="Jane" required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="lastName">Last name <span class="required">*</span></label>
            <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="email">Email <span class="required">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              <input type="email" class="form-control is-valid" id="email" value="jane@company.com">
            </div>
            <div class="valid-feedback d-block">Looks good.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="phone">Phone</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-phone"></i></span>
              <input type="tel" class="form-control is-invalid" id="phone" placeholder="+1 555 000 0000">
            </div>
            <div class="invalid-feedback d-block">Please enter a valid phone number.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="company">Company</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-building"></i></span>
              <input type="text" class="form-control" id="company" placeholder="Acme Inc.">
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="source">Lead source</label>
            <select class="form-select mino-select2" id="source" data-placeholder="Select source">
              <option value=""></option>
              <option value="website">Website</option>
              <option value="referral">Referral</option>
              <option value="linkedin">LinkedIn</option>
              <option value="webinar">Webinar</option>
              <option value="cold">Cold Call</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status">
              <option>New</option>
              <option selected>Contacted</option>
              <option>Qualified</option>
              <option>Proposal</option>
              <option>Cold</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="owner">Owner</label>
            <select class="form-select mino-select2" id="owner" data-placeholder="Assign owner" data-allow-clear="true">
              <option value=""></option>
              <option value="1">Alex Davis</option>
              <option value="2">Sarah Kim</option>
              <option value="3">Mike Ross</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label" for="notes">Notes</label>
            <textarea class="form-control" id="notes" rows="4" placeholder="Add context about this lead..."></textarea>
          </div>

          <div class="col-12">
            <label class="form-label d-block mb-2">Interest</label>
            <div class="d-flex flex-wrap gap-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="intProduct" checked>
                <label class="form-check-label" for="intProduct">Product</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="intService">
                <label class="form-check-label" for="intService">Service</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="intPartner">
                <label class="form-check-label" for="intPartner">Partnership</label>
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label d-block mb-2">Priority</label>
            <div class="d-flex flex-wrap gap-4">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="priority" id="priHigh">
                <label class="form-check-label" for="priHigh">High</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="priority" id="priMed" checked>
                <label class="form-check-label" for="priMed">Medium</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="priority" id="priLow">
                <label class="form-check-label" for="priLow">Low</label>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 justify-content-end pt-2">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary" data-mino-toast data-title="Form is UI-only" data-icon="info"><i class="fas fa-floppy-disk"></i> Save Lead</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="mino-card mb-4">
      <div class="mino-card-header"><h3 class="mino-card-title">Floating Labels</h3></div>
      <div class="mino-card-body">
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="floatName" placeholder="Name">
          <label for="floatName">Full name</label>
        </div>
        <div class="form-floating mb-3">
          <input type="email" class="form-control" id="floatEmail" placeholder="Email">
          <label for="floatEmail">Email address</label>
        </div>
        <div class="form-floating">
          <textarea class="form-control" placeholder="Message" id="floatMsg" style="height:100px"></textarea>
          <label for="floatMsg">Message</label>
        </div>
      </div>
    </div>

    <div class="mino-card">
      <div class="mino-card-header"><h3 class="mino-card-title">Validation Legend</h3></div>
      <div class="mino-card-body">
        <div class="form-group">
          <label class="form-label">Valid field</label>
          <input type="text" class="form-control is-valid" value="Accepted">
        </div>
        <div class="form-group mb-0">
          <label class="form-label">Invalid field</label>
          <input type="text" class="form-control is-invalid" value="Missing @">
          <div class="invalid-feedback d-block">Email format is required.</div>
        </div>
      </div>
    </div>
  </div>
</div>
