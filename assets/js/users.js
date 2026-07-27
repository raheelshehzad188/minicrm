/**
 * Mino CRM — Users module (AJAX + DataTables)
 */
(function (window, $) {
  'use strict';

  var cfg = window.MINO_USERS;
  if (!cfg) return;

  var table = null;

  function csrfData(extra) {
    var name = $('meta[name="mino-csrf-name"]').attr('content') || $('input[name^="mino_csrf"], input[name="mino_csrf"]').first().attr('name');
    var hash = $('meta[name="mino-csrf-hash"]').attr('content') || $('input[name="' + name + '"]').val();
    // Prefer form field from page
    var $token = $('#userForm input[type="hidden"]').filter(function () {
      return $(this).attr('name') && $(this).attr('name').indexOf('csrf') !== -1 || $(this).attr('name') === 'mino_csrf';
    }).first();
    if (!$token.length) $token = $('input[name="mino_csrf"]').first();
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
      Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 3000 });
    }
  }

  function statusBadge(status) {
    var map = { active: 'success', inactive: 'secondary', suspended: 'danger' };
    return '<span class="mino-badge mino-badge-' + (map[status] || 'secondary') + ' mino-badge-dot">' + status + '</span>';
  }

  function loadUsers() {
    $.getJSON(cfg.urls.list, {
      search: $('#userSearch').val(),
      status: $('#userStatusFilter').val(),
      role_id: $('#userRoleFilter').val()
    }).done(function (resp) {
      updateCsrf(resp);
      var rows = (resp.data && resp.data.rows) ? resp.data.rows : [];
      if (table) {
        table.clear();
        rows.forEach(function (u) { table.row.add(u); });
        table.draw();
      }
    });
  }

  function initTable() {
    table = $('#usersTable').DataTable({
      data: [],
      columns: [
        {
          data: null,
          render: function (u) {
            var img = u.profile_image
              ? '<img src="' + u.profile_image + '" class="mino-avatar mino-avatar-sm" alt="">'
              : '<span class="mino-avatar mino-avatar-sm">' + u.initials + '</span>';
            return '<div class="d-flex align-items-center gap-2">' + img +
              '<div><div class="fw-semibold">' + $('<div>').text(u.name).html() + '</div>' +
              '<div class="mino-text-xs mino-text-muted">' + $('<div>').text(u.email).html() + '</div></div></div>';
          }
        },
        { data: 'role_name' },
        { data: 'status', render: statusBadge },
        { data: 'last_login', render: function (v) { return v || '—'; } },
        {
          data: null,
          orderable: false,
          className: 'text-end',
          render: function (u) {
            var html = '<div class="table-actions">';
            if (cfg.canEdit) {
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon btn-edit-user" data-id="' + u.id + '" title="Edit"><i class="fas fa-pen"></i></button>';
              if (u.status === 'active') {
                html += '<button type="button" class="btn btn-sm btn-ghost btn-icon btn-suspend-user" data-id="' + u.id + '" title="Suspend"><i class="fas fa-ban"></i></button>';
              } else {
                html += '<button type="button" class="btn btn-sm btn-ghost btn-icon btn-activate-user" data-id="' + u.id + '" title="Activate"><i class="fas fa-circle-check"></i></button>';
              }
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon btn-reset-user" data-id="' + u.id + '" title="Reset password"><i class="fas fa-key"></i></button>';
            }
            if (cfg.canDelete && !u.is_self) {
              html += '<button type="button" class="btn btn-sm btn-ghost btn-icon text-danger btn-delete-user" data-id="' + u.id + '" title="Delete"><i class="fas fa-trash"></i></button>';
            }
            html += '</div>';
            return html;
          }
        }
      ],
      pageLength: 10,
      order: [],
      language: {
        search: '',
        searchPlaceholder: 'Filter table...',
        emptyTable: 'No users found'
      }
    });
    loadUsers();
  }

  function openCreate() {
    $('#userModalTitle').text('Add User');
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#userPassword').prop('required', true);
    $('#userPasswordLabel .required').show();
    $('#userPasswordHint').text('Min 8 characters');
    new bootstrap.Modal('#userModal').show();
  }

  function openEdit(id) {
    $.getJSON(cfg.urls.get + '/' + id).done(function (resp) {
      updateCsrf(resp);
      if (!resp.success) return toast('error', resp.message);
      var u = resp.data.user;
      $('#userModalTitle').text('Edit User');
      $('#userId').val(u.id);
      $('#userName').val(u.name);
      $('#userEmail').val(u.email);
      $('#userPhone').val(u.phone);
      $('#userRole').val(u.role_id);
      $('#userStatus').val(u.status);
      $('#userPassword').val('').prop('required', false);
      $('#userPasswordHint').text('Leave blank to keep current password');
      new bootstrap.Modal('#userModal').show();
    });
  }

  function postAction(url, data, confirmOpts) {
    var run = function () {
      $.ajax({
        url: url,
        method: 'POST',
        data: csrfData(data),
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        toast(resp.success ? 'success' : 'error', resp.message);
        if (resp.success) loadUsers();
        if (resp.data && resp.data.temp_password) {
          Swal.fire({ icon: 'info', title: 'Temp password (dev)', text: resp.data.temp_password });
        }
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Request failed');
      });
    };

    if (confirmOpts && window.MinoComponents) {
      MinoComponents.confirm(confirmOpts).then(function (r) { if (r.isConfirmed) run(); });
    } else if (confirmOpts && typeof Swal !== 'undefined') {
      Swal.fire($.extend({ showCancelButton: true, icon: 'warning' }, confirmOpts)).then(function (r) { if (r.isConfirmed) run(); });
    } else {
      run();
    }
  }

  $(function () {
    if (!$('#usersTable').length) return;
    initTable();

    var searchTimer;
    $('#userSearch, #userStatusFilter, #userRoleFilter').on('input change', function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(loadUsers, 250);
    });

    $('#btnAddUser').on('click', openCreate);

    $(document).on('click', '.btn-edit-user', function () { openEdit($(this).data('id')); });
    $(document).on('click', '.btn-delete-user', function () {
      var id = $(this).data('id');
      postAction(cfg.urls.destroy + '/' + id, {}, { title: 'Delete user?', text: 'This soft-deletes the user from your organization.', confirmText: 'Yes, delete' });
    });
    $(document).on('click', '.btn-suspend-user', function () {
      postAction(cfg.urls.status + '/' + $(this).data('id'), { status: 'suspended' }, { title: 'Suspend user?', text: 'They will not be able to sign in.', confirmText: 'Suspend' });
    });
    $(document).on('click', '.btn-activate-user', function () {
      postAction(cfg.urls.status + '/' + $(this).data('id'), { status: 'active' }, { title: 'Activate user?', text: 'They will regain access.', confirmText: 'Activate' });
    });
    $(document).on('click', '.btn-reset-user', function () {
      postAction(cfg.urls.reset + '/' + $(this).data('id'), {}, { title: 'Reset password?', text: 'A temporary password will be generated.', confirmText: 'Reset' });
    });

    $('#userForm').on('submit', function (e) {
      e.preventDefault();
      var id = $('#userId').val();
      var url = id ? (cfg.urls.update + '/' + id) : cfg.urls.store;
      $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        toast(resp.success ? 'success' : 'error', resp.message);
        if (resp.success) {
          bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
          loadUsers();
        }
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Request failed');
      });
    });
  });
})(window, jQuery);
