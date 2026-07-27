/**
 * Mino CRM — UI Components (Select2, DataTables, Toasts, etc.)
 */
(function (window, $) {
  'use strict';

  var Components = {
    init: function () {
      this.initSelect2();
      this.initDataTables();
      this.initTooltips();
      this.bindDemoActions();
    },

    initSelect2: function () {
      if (!$.fn.select2) return;
      $('.mino-select2').each(function () {
        var $el = $(this);
        if ($el.hasClass('select2-hidden-accessible')) return;
        $el.select2({
          width: '100%',
          placeholder: $el.data('placeholder') || 'Select an option',
          allowClear: !!$el.data('allow-clear'),
          dropdownParent: $el.closest('.modal, .offcanvas, body')
        });
      });
    },

    initDataTables: function () {
      if (!$.fn.DataTable) return;
      $('.mino-datatable').each(function () {
        var $table = $(this);
        if ($.fn.DataTable.isDataTable($table)) return;
        $table.DataTable({
          pageLength: $table.data('page-length') || 10,
          order: [],
          language: {
            search: '',
            searchPlaceholder: 'Search...',
            lengthMenu: 'Show _MENU_',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '<i class="fas fa-chevron-left"></i>', next: '<i class="fas fa-chevron-right"></i>' }
          },
          dom: '<"mino-table-toolbar"<"d-flex gap-2 align-items-center"l f>>t<"mino-table-footer"ip>',
          drawCallback: function () {
            // Theme-friendly redraw
          }
        });
      });
    },

    initTooltips: function () {
      if (typeof bootstrap === 'undefined') return;
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
      });
    },

    toast: function (options) {
      options = options || {};
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          toast: true,
          position: options.position || 'top-end',
          icon: options.icon || 'success',
          title: options.title || 'Done',
          text: options.text || '',
          showConfirmButton: false,
          timer: options.timer || 3000,
          timerProgressBar: true
        });
        return;
      }
      alert(options.title || 'Done');
    },

    confirm: function (options) {
      options = options || {};
      if (typeof Swal === 'undefined') {
        return Promise.resolve({ isConfirmed: window.confirm(options.text || 'Are you sure?') });
      }
      return Swal.fire({
        title: options.title || 'Are you sure?',
        text: options.text || '',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--mino-primary').trim() || '#0F766E',
        cancelButtonColor: '#64748B',
        confirmButtonText: options.confirmText || 'Yes, continue',
        cancelButtonText: options.cancelText || 'Cancel'
      });
    },

    bindDemoActions: function () {
      $(document).on('click', '[data-mino-toast]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        Components.toast({
          icon: $btn.data('icon') || 'success',
          title: $btn.data('title') || 'Notification',
          text: $btn.data('text') || ''
        });
      });

      $(document).on('click', '[data-mino-confirm]', function (e) {
        e.preventDefault();
        Components.confirm({
          title: $(this).data('title') || 'Confirm action',
          text: $(this).data('text') || 'This is a UI demo confirmation.'
        });
      });

      // Bulk select
      $(document).on('change', '[data-mino-check-all]', function () {
        var checked = $(this).prop('checked');
        var target = $(this).data('mino-check-all');
        $(target).prop('checked', checked);
      });
    }
  };

  window.MinoComponents = Components;

  $(function () {
    Components.init();
  });
})(window, jQuery);
