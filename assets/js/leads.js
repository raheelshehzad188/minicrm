/**
 * Mino CRM — Leads module (list, kanban, profile, import/export)
 */
(function (window, $) {
  'use strict';

  var cfg = window.MINO_LEADS;
  if (!cfg) return;

  var table = null;
  var selected = {};
  var importRows = [];
  var columnVisibility = {
    company: true, status: true, source: true, assignee: true, priority: true, value: true, created: true
  };

  function csrfData(extra) {
    var $token = $('#leadForm input[name="mino_csrf"], input[name="mino_csrf"]').first();
    var data = $.extend({}, extra || {});
    if ($token.length) data[$token.attr('name')] = $token.val();
    return data;
  }

  function updateCsrf(resp) {
    if (!resp || !resp.csrf_name || !resp.csrf_hash) return;
    $('input[name="' + resp.csrf_name + '"]').val(resp.csrf_hash);
  }

  function toast(icon, title) {
    if (window.MinoComponents && MinoComponents.toast) {
      MinoComponents.toast({ icon: icon, title: title });
    } else if (typeof Swal !== 'undefined') {
      Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 3200 });
    }
  }

  function escapeHtml(str) {
    return $('<div>').text(str == null ? '' : str).html();
  }

  function filters() {
    return {
      search: $('#leadSearch').val() || '',
      status_id: $('#filterStatus').val() || '',
      source_id: $('#filterSource').val() || '',
      assigned_to: $('#filterAssignee').val() || '',
      priority_id: $('#filterPriority').val() || '',
      pipeline_id: $('#filterPipeline').val() || '',
      stage_id: $('#filterStage').val() || '',
      tag_id: $('#filterTag').val() || '',
      date_from: $('#filterDateFrom').val() || '',
      date_to: $('#filterDateTo').val() || '',
      trashed: $('#filterTrashed').val() || ''
    };
  }

  function statusBadge(name, color) {
    if (!name) return '—';
    color = color || '#64748B';
    return '<span class="mino-badge" style="background:' + escapeHtml(color) + '22;color:' + escapeHtml(color) + '">' + escapeHtml(name) + '</span>';
  }

  function tagChips(tags) {
    if (!tags || !tags.length) return '';
    return tags.map(function (t) {
      return '<span class="mino-badge me-1" style="background:' + escapeHtml(t.color) + '22;color:' + escapeHtml(t.color) + '">' + escapeHtml(t.name) + '</span>';
    }).join('');
  }

  /* ========== List ========== */
  function loadLeads() {
    if (!$('#leadsTable').length) return;
    $.getJSON(cfg.urls.list, filters()).done(function (resp) {
      updateCsrf(resp);
      var rows = (resp.data && resp.data.rows) ? resp.data.rows : [];
      $('#leadCountLabel').text(rows.length + ' lead' + (rows.length === 1 ? '' : 's'));
      if (table) {
        table.clear();
        rows.forEach(function (r) { table.row.add(r); });
        table.draw();
      }
      selected = {};
      updateBulkBar();
      $('#leadCheckAll').prop('checked', false);
    });
  }

  function initTable() {
    table = $('#leadsTable').DataTable({
      data: [],
      columns: [
        {
          data: null,
          orderable: false,
          render: function (r) {
            return '<input type="checkbox" class="form-check-input lead-row-check" data-id="' + r.id + '">';
          }
        },
        {
          data: null,
          render: function (r) {
            return '<div class="d-flex align-items-center gap-2">' +
              '<span class="mino-avatar mino-avatar-sm">' + escapeHtml(r.initials) + '</span>' +
              '<div><a href="' + escapeHtml(r.profile_url) + '" class="fw-semibold text-decoration-none">' + escapeHtml(r.title) + '</a>' +
              '<div class="mino-text-xs mino-text-muted">' + escapeHtml(r.full_name || r.email || '') + '</div>' +
              '<div>' + tagChips(r.tags) + '</div></div></div>';
          }
        },
        { data: 'company_name', defaultContent: '—', visible: columnVisibility.company },
        {
          data: null,
          visible: columnVisibility.status,
          render: function (r) { return statusBadge(r.status_name, r.status_color); }
        },
        { data: 'source_name', defaultContent: '—', visible: columnVisibility.source },
        { data: 'assignee_name', defaultContent: '—', visible: columnVisibility.assignee },
        {
          data: null,
          visible: columnVisibility.priority,
          render: function (r) { return statusBadge(r.priority_name, r.priority_color); }
        },
        {
          data: 'estimated_value',
          visible: columnVisibility.value,
          render: function (v) { return v != null && v !== '' ? '$' + Number(v).toLocaleString() : '—'; }
        },
        {
          data: 'created_at',
          visible: columnVisibility.created,
          render: function (v) { return v ? String(v).substring(0, 10) : '—'; }
        },
        {
          data: null,
          orderable: false,
          className: 'text-end',
          render: function (r) {
            var html = '<div class="table-actions">';
            html += '<a href="' + escapeHtml(r.profile_url) + '" class="btn btn-sm btn-ghost btn-icon" title="View"><i class="fas fa-eye"></i></a>';
            if (cfg.canEdit && !r.deleted_at) {
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon btn-edit-lead" data-id="' + r.id + '" title="Edit"><i class="fas fa-pen"></i></button>';
            }
            if (cfg.canDelete && !r.deleted_at) {
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon text-danger btn-delete-lead" data-id="' + r.id + '" title="Delete"><i class="fas fa-trash"></i></button>';
            }
            if (cfg.canDelete && r.deleted_at) {
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon btn-restore-lead" data-id="' + r.id + '" title="Restore"><i class="fas fa-rotate-left"></i></button>';
            }
            if (cfg.isOwner && r.deleted_at) {
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon text-danger btn-force-lead" data-id="' + r.id + '" title="Permanent delete"><i class="fas fa-skull-crossbones"></i></button>';
            }
            html += '</div>';
            return html;
          }
        }
      ],
      pageLength: 10,
      order: [[8, 'desc']],
      language: { search: '', searchPlaceholder: 'Filter table…', emptyTable: 'No leads found' }
    });

    var colMap = [
      { key: 'company', label: 'Company', idx: 2 },
      { key: 'status', label: 'Status', idx: 3 },
      { key: 'source', label: 'Source', idx: 4 },
      { key: 'assignee', label: 'Assignee', idx: 5 },
      { key: 'priority', label: 'Priority', idx: 6 },
      { key: 'value', label: 'Value', idx: 7 },
      { key: 'created', label: 'Created', idx: 8 }
    ];
    var html = '';
    colMap.forEach(function (c) {
      html += '<label class="dropdown-item d-flex align-items-center gap-2"><input type="checkbox" class="form-check-input lead-col-toggle" data-idx="' + c.idx + '" data-key="' + c.key + '"' + (columnVisibility[c.key] ? ' checked' : '') + '> ' + c.label + '</label>';
    });
    $('#leadColumnToggles').html(html);

    loadLeads();
  }

  function updateBulkBar() {
    var n = Object.keys(selected).length;
    $('#bulkCount').text(n);
    $('#leadBulkBar').toggleClass('d-none', n === 0);
  }

  /* ========== Form ========== */
  function filterStagesByPipeline() {
    var pid = String($('#leadPipeline').val());
    $('#leadStage option').each(function () {
      var op = $(this).data('pipeline');
      if (!op) return;
      $(this).toggle(String(op) === pid);
    });
    var $vis = $('#leadStage option').filter(function () { return $(this).css('display') !== 'none' && $(this).val(); }).first();
    if ($vis.length && $('#leadStage option:selected').css('display') === 'none') {
      $('#leadStage').val($vis.val());
    }
  }

  function openCreate() {
    $('#leadModalTitle').text('Add Lead');
    $('#leadForm')[0].reset();
    $('#leadId').val('');
    $('#forceDuplicate').val('0');
    if ($('#leadTags').hasClass('select2-hidden-accessible')) {
      $('#leadTags').val(null).trigger('change');
    }
    filterStagesByPipeline();
    new bootstrap.Modal('#leadModal').show();
  }

  function openEdit(id) {
    $.getJSON(cfg.urls.get + '/' + id).done(function (resp) {
      updateCsrf(resp);
      if (!resp.success) return toast('error', resp.message);
      var l = resp.data.lead;
      $('#leadModalTitle').text('Edit Lead');
      $('#leadId').val(l.id);
      $('#forceDuplicate').val('0');
      $('#leadTitle').val(l.title);
      $('#leadFirstName').val(l.first_name);
      $('#leadLastName').val(l.last_name);
      $('#leadEmail').val(l.email);
      $('#leadPhone').val(l.phone);
      $('#leadMobile').val(l.mobile);
      $('#leadCompany').val(l.company_name);
      $('#leadWebsite').val(l.website);
      $('#leadAddress').val(l.address);
      $('#leadCity').val(l.city);
      $('#leadState').val(l.state);
      $('#leadCountry').val(l.country);
      $('#leadPostal').val(l.postal_code);
      $('#leadStatus').val(l.lead_status_id || '');
      $('#leadSource').val(l.lead_source_id || '');
      $('#leadPriority').val(l.priority_id || '');
      $('#leadPipeline').val(l.pipeline_id || '');
      filterStagesByPipeline();
      $('#leadStage').val(l.stage_id || '');
      $('#leadAssignee').val(l.assigned_to || '');
      $('#leadValue').val(l.estimated_value || '');
      $('#leadCloseDate').val(l.expected_close_date || '');
      $('#leadDescription').val(l.description || '');
      var tags = l.tag_ids || [];
      $('#leadTags').val(tags.map(String)).trigger('change');
      if (l.custom_values) {
        Object.keys(l.custom_values).forEach(function (fid) {
          $('[data-cf="' + fid + '"]').val(l.custom_values[fid]);
        });
      }
      new bootstrap.Modal('#leadModal').show();
    });
  }

  function saveLead(force) {
    if (force) $('#forceDuplicate').val('1');
    var id = $('#leadId').val();
    var url = id ? (cfg.urls.update + '/' + id) : cfg.urls.store;
    var data = $('#leadForm').serialize();
    $.ajax({
      url: url,
      method: 'POST',
      data: data,
      dataType: 'json'
    }).done(function (resp) {
      updateCsrf(resp);
      if (resp.success) {
        toast('success', resp.message);
        bootstrap.Modal.getInstance(document.getElementById('leadModal')).hide();
        loadLeads();
        if (cfg.leadId) location.reload();
        return;
      }
      if (resp.data && resp.data.has_duplicates) {
        var names = (resp.data.duplicates || []).map(function (d) { return d.title; }).join(', ');
        Swal.fire({
          icon: 'warning',
          title: 'Possible duplicates',
          html: 'Matching leads: <strong>' + escapeHtml(names) + '</strong><br>Save anyway?',
          showCancelButton: true,
          confirmButtonText: 'Save anyway'
        }).then(function (r) {
          if (r.isConfirmed) saveLead(true);
        });
        return;
      }
      toast('error', resp.message || 'Save failed');
    }).fail(function (xhr) {
      var resp = xhr.responseJSON;
      if (resp) updateCsrf(resp);
      if (resp && resp.data && resp.data.has_duplicates) {
        var names = (resp.data.duplicates || []).map(function (d) { return d.title; }).join(', ');
        Swal.fire({
          icon: 'warning',
          title: 'Possible duplicates',
          html: 'Matching leads: <strong>' + escapeHtml(names) + '</strong><br>Save anyway?',
          showCancelButton: true,
          confirmButtonText: 'Save anyway'
        }).then(function (r) {
          if (r.isConfirmed) saveLead(true);
        });
        return;
      }
      toast('error', (resp && resp.message) || 'Request failed');
    });
  }

  function postAction(url, data, confirmOpts, onOk) {
    var run = function () {
      $.ajax({
        url: url,
        method: 'POST',
        data: csrfData(data),
        dataType: 'json',
        traditional: true
      })
        .done(function (resp) {
          updateCsrf(resp);
          toast(resp.success ? 'success' : 'error', resp.message);
          if (resp.success) {
            if (onOk) onOk(resp);
            else loadLeads();
          }
        })
        .fail(function (xhr) {
          var resp = xhr.responseJSON;
          if (resp) updateCsrf(resp);
          toast('error', (resp && resp.message) || 'Request failed');
        });
    };
    if (confirmOpts) {
      if (window.MinoComponents && MinoComponents.confirm) {
        MinoComponents.confirm(confirmOpts).then(function (r) { if (r.isConfirmed) run(); });
      } else {
        Swal.fire($.extend({ showCancelButton: true, icon: 'warning' }, confirmOpts)).then(function (r) { if (r.isConfirmed) run(); });
      }
    } else run();
  }

  /* ========== Kanban ========== */
  function loadKanban() {
    if (!$('#leadKanban').length) return;
    $.getJSON(cfg.urls.kanban, {
      search: $('#leadSearch').val() || '',
      assigned_to: $('#filterAssignee').val() || ''
    }).done(function (resp) {
      updateCsrf(resp);
      var cols = (resp.data && resp.data.columns) ? resp.data.columns : [];
      var html = '';
      cols.forEach(function (col) {
        html += '<div class="lead-kanban-col" data-status-id="' + col.id + '">' +
          '<div class="lead-kanban-col__head" style="border-color:' + escapeHtml(col.color) + '">' +
          '<span>' + escapeHtml(col.name) + '</span>' +
          '<span class="mino-badge mino-badge-secondary">' + col.cards.length + '</span></div>' +
          '<div class="lead-kanban-col__body" data-status-id="' + col.id + '">';
        col.cards.forEach(function (c) {
          html += '<div class="lead-kanban-card" data-id="' + c.id + '">' +
            '<a href="' + escapeHtml(c.profile_url) + '" class="fw-semibold text-decoration-none">' + escapeHtml(c.title) + '</a>' +
            '<div class="mino-text-xs mino-text-muted">' + escapeHtml(c.company_name || c.full_name || '') + '</div>' +
            '<div class="d-flex justify-content-between mt-2">' +
            '<span class="mino-text-xs">' + escapeHtml(c.assignee_name || 'Unassigned') + '</span>' +
            '<span class="mino-text-xs fw-semibold">' + (c.estimated_value ? '$' + Number(c.estimated_value).toLocaleString() : '') + '</span>' +
            '</div></div>';
        });
        html += '</div></div>';
      });
      $('#leadKanban').html(html || '<div class="mino-empty"><div class="mino-empty__title">No leads</div></div>');

      if (cfg.canEdit && typeof Sortable !== 'undefined') {
        $('#leadKanban .lead-kanban-col__body').each(function () {
          Sortable.create(this, {
            group: 'leads',
            animation: 150,
            ghostClass: 'lead-kanban-ghost',
            onAdd: function (evt) {
              var leadId = $(evt.item).data('id');
              var statusId = $(evt.to).data('status-id');
              postAction(cfg.urls.change_status + '/' + leadId, { status_id: statusId }, null, function () {
                loadKanban();
              });
            }
          });
        });
      }
    });
  }

  /* ========== Profile ========== */
  function loadTimeline() {
    if (!cfg.leadId) return;
    $.getJSON(cfg.urls.timeline + '/' + cfg.leadId).done(function (resp) {
      updateCsrf(resp);
      var rows = (resp.data && resp.data.timeline) || [];
      if (!rows.length) {
        $('#leadTimelineList').html('<li class="mino-text-muted">No activity yet.</li>');
        return;
      }
      var html = '';
      rows.forEach(function (t) {
        html += '<li class="mino-timeline-item"><span class="mino-timeline-dot"></span><div class="mino-timeline-content">' +
          '<div class="mino-timeline-title">' + escapeHtml(t.title) + '</div>' +
          '<div>' + escapeHtml(t.description || '') + '</div>' +
          '<div class="mino-timeline-time">' + escapeHtml(t.user_name) + ' · ' + escapeHtml(t.created_at) + '</div></div></li>';
      });
      $('#leadTimelineList').html(html);
    });
  }

  function loadNotes() {
    if (!cfg.leadId) return;
    $.getJSON(cfg.urls.notes + '/' + cfg.leadId).done(function (resp) {
      updateCsrf(resp);
      var rows = (resp.data && resp.data.notes) || [];
      if (!rows.length) {
        $('#leadNotesList').html('<div class="mino-empty py-4"><div class="mino-empty__title" style="font-size:var(--mino-fs-sm)">No notes yet</div></div>');
        return;
      }
      var html = '';
      rows.forEach(function (n) {
        html += '<div class="mino-card mb-2"><div class="mino-card-body">' +
          (n.is_pinned ? '<span class="mino-badge mino-badge-warning mb-2">Pinned</span>' : '') +
          '<div class="lead-note-body">' + n.body + '</div>' +
          '<div class="d-flex justify-content-between align-items-center mt-2">' +
          '<span class="mino-text-xs mino-text-muted">' + escapeHtml(n.user_name) + ' · ' + escapeHtml(n.created_at) + '</span>';
        if (cfg.canEdit) {
          html += '<div><button type="button" class="btn btn-sm btn-ghost btn-pin-note" data-id="' + n.id + '" data-pinned="' + n.is_pinned + '"><i class="fas fa-thumbtack"></i></button>' +
            '<button type="button" class="btn btn-sm btn-ghost text-danger btn-del-note" data-id="' + n.id + '"><i class="fas fa-trash"></i></button></div>';
        }
        html += '</div></div></div>';
      });
      $('#leadNotesList').html(html);
    });
  }

  function loadAttachments() {
    if (!cfg.leadId) return;
    $.getJSON(cfg.urls.attachments + '/' + cfg.leadId).done(function (resp) {
      updateCsrf(resp);
      var rows = (resp.data && resp.data.attachments) || [];
      if (!rows.length) {
        $('#leadAttachmentsList').html('<div class="mino-empty py-4"><div class="mino-empty__title" style="font-size:var(--mino-fs-sm)">No files yet</div></div>');
        return;
      }
      var html = '<div class="list-group list-group-flush">';
      rows.forEach(function (a) {
        html += '<div class="list-group-item d-flex justify-content-between align-items-center">' +
          '<div><div class="fw-semibold">' + escapeHtml(a.original_name) + '</div>' +
          '<div class="mino-text-xs mino-text-muted">' + escapeHtml(a.user_name) + ' · ' + Math.round((a.file_size || 0) / 1024) + ' KB</div></div><div class="d-flex gap-1">';
        if (a.previewable) {
          html += '<a class="btn btn-sm btn-ghost" href="' + escapeHtml(a.file_path) + '" target="_blank"><i class="fas fa-eye"></i></a>';
        }
        html += '<a class="btn btn-sm btn-ghost" href="' + escapeHtml(a.file_path) + '" download><i class="fas fa-download"></i></a>';
        if (cfg.canEdit) {
          html += '<button type="button" class="btn btn-sm btn-ghost text-danger btn-del-attach" data-id="' + a.id + '"><i class="fas fa-trash"></i></button>';
        }
        html += '</div></div>';
      });
      html += '</div>';
      $('#leadAttachmentsList').html(html);
    });
  }

  function loadSavedFilters() {
    if (!$('#savedFilters').length) return;
    $.getJSON(cfg.urls.saved_filters).done(function (resp) {
      updateCsrf(resp);
      var rows = (resp.data && resp.data.filters) || [];
      var html = '<option value="">Saved filters…</option>';
      rows.forEach(function (f) {
        html += '<option value="' + f.id + '" data-filters=\'' + escapeHtml(JSON.stringify(f.filters || {})) + '\'>' + escapeHtml(f.name) + '</option>';
      });
      $('#savedFilters').html(html);
    });
  }

  function exportUrl(format) {
    var q = $.param($.extend({}, filters(), { format: format }));
    window.location = cfg.urls.export + '?' + q;
  }

  function runBulk(action) {
    var ids = Object.keys(selected);
    if (!ids.length) return;

    var payload = { action: action, ids: ids };

    var ask = function (fields, then) {
      Swal.fire($.extend({
        title: 'Bulk ' + action,
        showCancelButton: true,
        confirmButtonText: 'Apply'
      }, fields)).then(function (r) {
        if (r.isConfirmed) then(r);
      });
    };

    var send = function () {
      postAction(cfg.urls.bulk, payload, null, function () {
        selected = {};
        loadLeads();
      });
    };

    if (action === 'delete') {
      postAction(cfg.urls.bulk, payload, { title: 'Delete selected?', text: 'Leads will be moved to trash.', confirmText: 'Delete' }, function () {
        selected = {};
        loadLeads();
      });
      return;
    }

    if (action === 'assign') {
      var opts = (cfg.lookups.users || []).map(function (u) {
        return '<option value="' + u.id + '">' + escapeHtml(u.name) + '</option>';
      }).join('');
      ask({
        html: '<select id="swalAssign" class="form-select"><option value="">Unassigned</option>' + opts + '</select>'
      }, function () {
        payload.assigned_to = $('#swalAssign').val();
        send();
      });
      return;
    }

    if (action === 'status') {
      var sopts = (cfg.lookups.statuses || []).map(function (s) {
        return '<option value="' + s.id + '">' + escapeHtml(s.name) + '</option>';
      }).join('');
      ask({ html: '<select id="swalStatus" class="form-select">' + sopts + '</select>' }, function () {
        payload.status_id = $('#swalStatus').val();
        send();
      });
      return;
    }

    if (action === 'pipeline') {
      var popts = (cfg.lookups.pipelines || []).map(function (p) {
        return '<option value="' + p.id + '">' + escapeHtml(p.name) + '</option>';
      }).join('');
      ask({ html: '<select id="swalPipe" class="form-select">' + popts + '</select>' }, function () {
        payload.pipeline_id = $('#swalPipe').val();
        send();
      });
      return;
    }

    if (action === 'stage') {
      var stopts = (cfg.lookups.stages || []).map(function (s) {
        return '<option value="' + s.id + '">' + escapeHtml(s.name) + '</option>';
      }).join('');
      ask({ html: '<select id="swalStage" class="form-select">' + stopts + '</select>' }, function () {
        payload.stage_id = $('#swalStage').val();
        send();
      });
    }
  }

  /* ========== Boot ========== */
  $(function () {
    var view = $('#mino-leads').data('view');

    if ($('#leadTags').length && $.fn.select2) {
      $('#leadTags').select2({ dropdownParent: $('#leadModal'), width: '100%', placeholder: 'Select tags' });
    }

    $('#leadPipeline').on('change', filterStagesByPipeline);

    $(document).on('click', '#btnAddLead', openCreate);
    $(document).on('click', '.btn-edit-lead, #btnEditLeadProfile', function () {
      openEdit($(this).data('id') || cfg.leadId);
    });

    $('#leadForm').on('submit', function (e) {
      e.preventDefault();
      saveLead(false);
    });

    $(document).on('click', '.btn-delete-lead', function () {
      postAction(cfg.urls.delete + '/' + $(this).data('id'), {}, { title: 'Move to trash?', text: 'You can restore later.', confirmText: 'Delete' });
    });
    $(document).on('click', '.btn-restore-lead, #btnRestoreLead', function () {
      var id = $(this).data('id') || cfg.leadId;
      postAction(cfg.urls.restore + '/' + id, {}, null, function () {
        if (cfg.leadId) location.reload();
        else loadLeads();
      });
    });
    $(document).on('click', '.btn-force-lead, #btnForceDeleteLead', function () {
      var id = $(this).data('id') || cfg.leadId;
      postAction(cfg.urls.force_delete + '/' + id, {}, { title: 'Permanent delete?', text: 'This cannot be undone.', confirmText: 'Delete forever', icon: 'error' }, function () {
        window.location = cfg.urls.list_page;
      });
    });

    var timer;
    $('#leadSearch, #filterStatus, #filterSource, #filterAssignee, #filterPriority, #filterPipeline, #filterStage, #filterTag, #filterDateFrom, #filterDateTo, #filterTrashed')
      .on('input change', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
          if (view === 'kanban') loadKanban();
          else loadLeads();
        }, 250);
      });

    $('#btnToggleAdvanced').on('click', function () {
      $('#leadAdvancedFilters').toggleClass('d-none');
    });

    $('#btnClearFilters').on('click', function () {
      $('#leadFilters select, #leadFilters input, #leadAdvancedFilters select, #leadAdvancedFilters input').val('');
      loadLeads();
    });

    $('#btnSaveFilter').on('click', function () {
      Swal.fire({
        title: 'Save filter',
        input: 'text',
        inputPlaceholder: 'Filter name',
        showCancelButton: true
      }).then(function (r) {
        if (!r.isConfirmed || !r.value) return;
        postAction(cfg.urls.save_filter, { name: r.value, filters: JSON.stringify(filters()) }, null, loadSavedFilters);
      });
    });

    $('#savedFilters').on('change', function () {
      var raw = $(this).find(':selected').attr('data-filters');
      if (!raw) return;
      try {
        var f = JSON.parse(raw);
        $('#leadSearch').val(f.search || '');
        $('#filterStatus').val(f.status_id || '');
        $('#filterSource').val(f.source_id || '');
        $('#filterAssignee').val(f.assigned_to || '');
        $('#filterPriority').val(f.priority_id || '');
        $('#filterPipeline').val(f.pipeline_id || '');
        $('#filterStage').val(f.stage_id || '');
        $('#filterTag').val(f.tag_id || '');
        $('#filterDateFrom').val(f.date_from || '');
        $('#filterDateTo').val(f.date_to || '');
        $('#leadAdvancedFilters').removeClass('d-none');
        loadLeads();
      } catch (e) {}
    });

    $(document).on('change', '.lead-col-toggle', function () {
      var idx = $(this).data('idx');
      var key = $(this).data('key');
      var on = $(this).is(':checked');
      columnVisibility[key] = on;
      table.column(idx).visible(on);
    });

    $('#leadCheckAll').on('change', function () {
      var on = $(this).is(':checked');
      $('.lead-row-check').prop('checked', on).each(function () {
        var id = $(this).data('id');
        if (on) selected[id] = true; else delete selected[id];
      });
      updateBulkBar();
    });

    $(document).on('change', '.lead-row-check', function () {
      var id = $(this).data('id');
      if ($(this).is(':checked')) selected[id] = true;
      else delete selected[id];
      updateBulkBar();
    });

    $(document).on('click', '[data-bulk]', function () {
      runBulk($(this).data('bulk'));
    });

    $('#btnExportCsv').on('click', function (e) { e.preventDefault(); exportUrl('csv'); });
    $('#btnExportExcel').on('click', function (e) { e.preventDefault(); exportUrl('excel'); });

    $('#btnImportLeads').on('click', function () {
      importRows = [];
      $('#importPreviewWrap').addClass('d-none');
      $('#btnImportRun').addClass('d-none');
      $('#importFile').val('');
      new bootstrap.Modal('#leadImportModal').show();
    });

    $('#btnImportPreview').on('click', function () {
      var file = $('#importFile')[0].files[0];
      if (!file) return toast('warning', 'Choose a CSV file');
      var fd = new FormData();
      fd.append('file', file);
      var $token = $('input[name="mino_csrf"]').first();
      if ($token.length) fd.append($token.attr('name'), $token.val());
      $.ajax({
        url: cfg.urls.import_preview,
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        if (!resp.success) return toast('error', resp.message);
        var headers = resp.data.headers || [];
        var preview = resp.data.preview || [];
        var mapping = resp.data.mapping || {};
        importRows = preview.map(function (row) {
          var out = {};
          Object.keys(mapping).forEach(function (csvCol) {
            var field = mapping[csvCol];
            out[field] = row[csvCol] || '';
          });
          // also allow direct header names matching fields
          headers.forEach(function (h) {
            var key = String(h).toLowerCase().replace(/\s+/g, '_');
            if (row[h] != null && out[key] == null) out[key] = row[h];
            if (mapping[h]) out[mapping[h]] = row[h];
          });
          return out;
        });
        var thead = '<tr>' + headers.map(function (h) { return '<th>' + escapeHtml(h) + '</th>'; }).join('') + '</tr>';
        var tbody = preview.map(function (row) {
          return '<tr>' + headers.map(function (h) { return '<td>' + escapeHtml(row[h] || '') + '</td>'; }).join('') + '</tr>';
        }).join('');
        $('#importPreviewTable thead').html(thead);
        $('#importPreviewTable tbody').html(tbody);
        $('#importPreviewWrap').removeClass('d-none');
        $('#btnImportRun').removeClass('d-none');
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Preview failed');
      });
    });

    $('#btnImportRun').on('click', function () {
      postAction(cfg.urls.import_run, {
        rows: JSON.stringify(importRows),
        duplicate_mode: $('#importDupMode').val()
      }, null, function (resp) {
        bootstrap.Modal.getInstance(document.getElementById('leadImportModal')).hide();
        loadLeads();
        if (resp.data) {
          toast('success', 'Created ' + resp.data.created + ', updated ' + resp.data.updated + ', skipped ' + resp.data.skipped);
        }
      });
    });

    if (view === 'list') {
      initTable();
      loadSavedFilters();
    } else if (view === 'kanban') {
      loadKanban();
      $('#btnRefreshKanban').on('click', loadKanban);
    } else if (view === 'profile') {
      loadTimeline();
      loadNotes();
      loadAttachments();

      $('#profileAssignee').on('change', function () {
        postAction(cfg.urls.assign + '/' + cfg.leadId, { assigned_to: $(this).val() }, null, loadTimeline);
      });

      $('#btnAddNote').on('click', function () {
        var body = $('#noteEditor').html();
        if (!$.trim($('#noteEditor').text())) return toast('warning', 'Write a note first');
        postAction(cfg.urls.note_store + '/' + cfg.leadId, {
          body: body,
          is_pinned: $('#notePinned').is(':checked') ? 1 : 0
        }, null, function () {
          $('#noteEditor').html('');
          $('#notePinned').prop('checked', false);
          loadNotes();
          loadTimeline();
        });
      });

      $(document).on('click', '.btn-del-note', function () {
        postAction(cfg.urls.note_delete + '/' + $(this).data('id'), {}, { title: 'Delete note?', confirmText: 'Delete' }, function () {
          loadNotes();
        });
      });

      $(document).on('click', '.btn-pin-note', function () {
        var pinned = $(this).data('pinned') ? 0 : 1;
        postAction(cfg.urls.note_update + '/' + $(this).data('id'), { is_pinned: pinned }, null, loadNotes);
      });

      $('#leadAttachInput').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var fd = new FormData();
        fd.append('file', file);
        var $token = $('input[name="mino_csrf"]').first();
        if ($token.length) fd.append($token.attr('name'), $token.val());
        $.ajax({
          url: cfg.urls.attach_upload + '/' + cfg.leadId,
          method: 'POST',
          data: fd,
          processData: false,
          contentType: false,
          dataType: 'json'
        }).done(function (resp) {
          updateCsrf(resp);
          toast(resp.success ? 'success' : 'error', resp.message);
          if (resp.success) {
            loadAttachments();
            loadTimeline();
          }
        });
        $(this).val('');
      });

      $(document).on('click', '.btn-del-attach', function () {
        postAction(cfg.urls.attach_delete + '/' + $(this).data('id'), {}, { title: 'Delete file?', confirmText: 'Delete' }, loadAttachments);
      });
    }
  });
})(window, jQuery);
