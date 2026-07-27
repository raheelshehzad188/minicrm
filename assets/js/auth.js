/**
 * Mino CRM — Auth AJAX forms
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
      return;
    }
    if (typeof Swal !== 'undefined') {
      Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 3200 });
      return;
    }
    alert(title);
  }

  function bindAjaxForm(selector, options) {
    $(document).on('submit', selector, function (e) {
      e.preventDefault();
      var $form = $(this);
      var $btn = $form.find('[type="submit"]');
      var url = $form.attr('action');
      $btn.prop('disabled', true);

      $.ajax({
        url: url,
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json'
      }).done(function (resp) {
        updateCsrf(resp);
        if (resp.success) {
          toast('success', resp.message || 'Success');
          if (resp.data && resp.data.reset_url) {
            // Dev hint
            console.info('Reset URL:', resp.data.reset_url);
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'info',
                title: 'Dev reset link',
                html: '<a href="' + resp.data.reset_url + '">' + resp.data.reset_url + '</a>',
                confirmButtonText: 'Open link'
              }).then(function (r) {
                if (r.isConfirmed) window.location = resp.data.reset_url;
              });
              return;
            }
          }
          if (resp.data && resp.data.redirect) {
            setTimeout(function () { window.location = resp.data.redirect; }, 400);
          } else if (options && options.onSuccess) {
            options.onSuccess(resp);
          }
        } else {
          toast('error', resp.message || 'Something went wrong');
        }
      }).fail(function (xhr) {
        var resp = xhr.responseJSON;
        if (resp) updateCsrf(resp);
        toast('error', (resp && resp.message) ? resp.message : 'Request failed');
      }).always(function () {
        $btn.prop('disabled', false);
      });
    });
  }

  $(function () {
    bindAjaxForm('#loginForm');
    bindAjaxForm('#forgotForm');
    bindAjaxForm('#resetForm');
    bindAjaxForm('#profileForm');
    bindAjaxForm('#changePasswordForm');

    function fillDemoLogin($row) {
      var email = $row.data('email');
      var password = $row.data('password');
      if (!email) return;
      $('#loginEmail').val(email).trigger('change');
      $('#loginPassword').val(password).trigger('change');
      $('.demo-login-row').removeClass('is-selected');
      $row.addClass('is-selected');
      $('#loginEmail').trigger('focus');
    }

    $(document).on('click', '.demo-login-row', function () {
      fillDemoLogin($(this));
    });

    $(document).on('keydown', '.demo-login-row', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        fillDemoLogin($(this));
      }
    });
  });
})(window, jQuery);
