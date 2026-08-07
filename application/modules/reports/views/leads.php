<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="mino-reports" class="reports-page"
  data-url="<?php echo html_escape($data_url); ?>"
  data-export="<?php echo html_escape($export_url); ?>">

  <div class="mino-card mb-3">
    <div class="mino-card-body">
      <div class="row g-2 align-items-end" id="reportFilters">
        <div class="col-md-2">
          <label class="form-label mino-text-xs">Lead type</label>
          <select class="form-select form-select-sm" id="reportLeadType">
            <option value="">All</option>
            <?php foreach ($lead_types as $slug => $label): ?>
              <option value="<?php echo html_escape($slug); ?>"><?php echo html_escape($label); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mino-text-xs">Status</label>
          <select class="form-select form-select-sm" id="reportStatus">
            <option value="">All</option>
            <?php foreach ($statuses as $st): ?>
              <option value="<?php echo (int) $st->id; ?>"><?php echo html_escape($st->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mino-text-xs">Source</label>
          <select class="form-select form-select-sm" id="reportSource">
            <option value="">All</option>
            <?php foreach ($sources as $src): ?>
              <option value="<?php echo (int) $src->id; ?>"><?php echo html_escape($src->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">From</label>
          <input type="date" class="form-control form-control-sm" id="reportDateFrom">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mino-text-xs">To</label>
          <input type="date" class="form-control form-control-sm" id="reportDateTo">
        </div>
        <div class="col-md-2">
          <label class="form-label mino-text-xs">Search</label>
          <input type="search" class="form-control form-control-sm" id="reportSearch" placeholder="Name, email, phone…">
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3">
        <button type="button" class="btn btn-sm btn-primary" id="btnRunReport"><i class="fas fa-play"></i> Run report</button>
        <button type="button" class="btn btn-sm btn-ghost" id="btnClearReport">Clear</button>
        <?php if (!empty($can_export)): ?>
        <a href="#" class="btn btn-sm btn-secondary" id="btnExportReport"><i class="fas fa-download"></i> Export CSV</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="stat-grid mb-3" id="reportSummary">
    <div class="stat-card">
      <div class="stat-card__label">Total</div>
      <div class="stat-card__value" id="sumTotal">—</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__label">Clinic</div>
      <div class="stat-card__value" id="sumClinic">—</div>
    </div>
    <div class="stat-card">
      <div class="stat-card__label">Academy</div>
      <div class="stat-card__value" id="sumAcademy">—</div>
    </div>
  </div>

  <div class="mino-table-wrap">
    <div class="mino-table-toolbar">
      <h3 class="mino-card-title mb-0">Lead report</h3>
      <span class="mino-text-xs mino-text-muted" id="reportCountLabel">Run a report to see results</span>
    </div>
    <div class="table-responsive p-3 pt-0">
      <table class="mino-table w-100" id="reportTable">
        <thead>
          <tr>
            <th>Lead</th>
            <th>Type</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Branch</th>
            <th>Service / Course</th>
            <th>Status</th>
            <th>Source</th>
            <th>Assignee</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<script>
(function ($, window) {
  'use strict';
  var $root = $('#mino-reports');
  if (!$root.length) return;
  var table = null;

  function filters() {
    return {
      lead_type: $('#reportLeadType').val() || '',
      status_id: $('#reportStatus').val() || '',
      source_id: $('#reportSource').val() || '',
      date_from: $('#reportDateFrom').val() || '',
      date_to: $('#reportDateTo').val() || '',
      search: $('#reportSearch').val() || ''
    };
  }

  function escapeHtml(str) {
    return $('<div>').text(str == null ? '' : str).html();
  }

  function run() {
    $.getJSON($root.data('url'), filters()).done(function (resp) {
      if (!resp.success) return;
      var rows = (resp.data && resp.data.rows) ? resp.data.rows : [];
      var sum = (resp.data && resp.data.summary) ? resp.data.summary : {};
      $('#sumTotal').text(sum.total != null ? sum.total : '—');
      $('#sumClinic').text(sum.clinic != null ? sum.clinic : '—');
      $('#sumAcademy').text(sum.academy != null ? sum.academy : '—');
      $('#reportCountLabel').text(rows.length + ' result' + (rows.length === 1 ? '' : 's'));

      if (table) {
        table.clear();
        rows.forEach(function (r) { table.row.add(r); });
        table.draw();
      }
    });
  }

  $(function () {
    table = $('#reportTable').DataTable({
      data: [],
      columns: [
        {
          data: null,
          render: function (r) {
            return '<a href="' + escapeHtml(r.profile_url) + '" class="fw-semibold text-decoration-none">' + escapeHtml(r.title) + '</a>';
          }
        },
        {
          data: null,
          render: function (r) {
            var tone = r.lead_type === 'academy' ? 'info' : 'primary';
            return '<span class="mino-badge mino-badge-' + tone + '">' + escapeHtml(r.lead_type_label || r.lead_type) + '</span>';
          }
        },
        { data: 'phone', defaultContent: '—' },
        { data: 'email', defaultContent: '—' },
        { data: 'branch', defaultContent: '—' },
        {
          data: null,
          render: function (r) {
            return escapeHtml(r.treatment || r.course || '—');
          }
        },
        { data: 'status_name', defaultContent: '—' },
        { data: 'source_name', defaultContent: '—' },
        { data: 'assignee_name', defaultContent: '—' },
        {
          data: 'created_at',
          render: function (v) { return v ? String(v).substring(0, 10) : '—'; }
        }
      ],
      pageLength: 25,
      order: [[9, 'desc']],
      language: { emptyTable: 'No leads match these filters' }
    });

    $('#btnRunReport').on('click', run);
    $('#btnClearReport').on('click', function () {
      $('#reportFilters select, #reportFilters input').val('');
      run();
    });
    $('#btnExportReport').on('click', function (e) {
      e.preventDefault();
      var q = $.param($.extend({}, filters(), { format: 'csv' }));
      window.location = $root.data('export') + '?' + q;
    });
    run();
  });
})(jQuery, window);
</script>
