/**
 * Mino CRM — Organization & Roles AJAX
 */
(function (window, $) {
  'use strict';

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

  $(function () {
    // Organization settings
    $('#orgSettingsForm').on('submit', function (e) {
      e.preventDefault();
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        toast(resp.success ? 'success' : 'error', resp.message);
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Request failed');
      });
    });

    $('#orgLogoForm').on('submit', function (e) {
      e.preventDefault();
      var $form = $(this);
      var fd = new FormData(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        toast(resp.success ? 'success' : 'error', resp.message);
        if (resp.success && resp.data && resp.data.logo_url) {
          $('#orgLogoPreview').attr('src', resp.data.logo_url);
        }
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Upload failed');
      });
    });

    $('#orgLogoInput').on('change', function () {
      var file = this.files && this.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function (ev) { $('#orgLogoPreview').attr('src', ev.target.result); };
      reader.readAsDataURL(file);
    });

    // Permission matrix
    $('#permSelectAll').on('click', function () {
      $('#permissionMatrixForm .perm-check:not(:disabled)').prop('checked', true);
    });
    $('#permClearAll').on('click', function () {
      $('#permissionMatrixForm .perm-check:not(:disabled)').prop('checked', false);
    });

    $('#permissionMatrixForm').on('submit', function (e) {
      e.preventDefault();
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        toast(resp.success ? 'success' : 'error', resp.message);
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Request failed');
      });
    });

    // Profile avatar
    $('#profileAvatarForm').on('submit', function (e) {
      e.preventDefault();
      var $form = $(this);
      $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        toast(resp.success ? 'success' : 'error', resp.message);
        if (resp.success && resp.data && resp.data.avatar_url) {
          $('.profile-avatar-img').attr('src', resp.data.avatar_url).show();
          $('.profile-avatar-initials').hide();
        }
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) || 'Upload failed');
      });
    });
  });
})(window, jQuery);
