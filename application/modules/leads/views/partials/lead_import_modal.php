<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal fade" id="leadImportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Leads</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mino-text-sm mino-text-muted">Upload a CSV (Excel-compatible). Preview up to 50 rows, then confirm.</p>
        <div class="mb-3">
          <input type="file" class="form-control" id="importFile" accept=".csv,text/csv">
        </div>
        <div class="mb-3">
          <label class="form-label">Duplicate handling</label>
          <select class="form-select" id="importDupMode">
            <option value="skip">Skip duplicates</option>
            <option value="update">Update existing</option>
            <option value="create">Create anyway</option>
          </select>
        </div>
        <div id="importPreviewWrap" class="d-none">
          <div class="table-responsive" style="max-height:280px">
            <table class="mino-table w-100" id="importPreviewTable">
              <thead></thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-soft-primary" id="btnImportPreview">Preview</button>
        <button type="button" class="btn btn-primary d-none" id="btnImportRun">Import</button>
      </div>
    </div>
  </div>
</div>
