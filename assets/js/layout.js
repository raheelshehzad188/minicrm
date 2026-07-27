/**
 * Mino CRM — Layout (Sidebar, Navbar, Right Panel)
 */
(function (window, $) {
  'use strict';

  var STORAGE_COLLAPSE = 'mino-sidebar-collapsed';

  var Layout = {
    init: function () {
      this.$app = $('.mino-app');
      if (!this.$app.length) return;

      if (localStorage.getItem(STORAGE_COLLAPSE) === '1' && window.innerWidth >= 992) {
        this.$app.addClass('sidebar-collapsed');
      }

      this._bind();
      this._highlightActive();
    },

    _bind: function () {
      var self = this;

      $(document).on('click', '[data-mino-sidebar-toggle]', function (e) {
        e.preventDefault();
        self.toggleSidebar();
      });

      $(document).on('click', '.mino-sidebar-overlay', function () {
        self.closeMobileSidebar();
      });

      $(document).on('click', '[data-mino-submenu-toggle]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $submenu = $btn.next('.mino-nav-submenu');
        var expanded = $btn.attr('aria-expanded') === 'true';

        $btn.attr('aria-expanded', !expanded);
        $submenu.toggleClass('show');
      });

      $(window).on('resize', function () {
        if (window.innerWidth >= 992) {
          self.closeMobileSidebar();
        }
      });

      // Focus search with /
      $(document).on('keydown', function (e) {
        if (e.key === '/' && !$(e.target).is('input, textarea, select, [contenteditable]')) {
          e.preventDefault();
          $('.mino-search__input').trigger('focus');
        }
      });
    },

    toggleSidebar: function () {
      if (window.innerWidth < 992) {
        this.$app.toggleClass('sidebar-open');
        $('.mino-sidebar-overlay').toggleClass('show', this.$app.hasClass('sidebar-open'));
        return;
      }
      this.$app.toggleClass('sidebar-collapsed');
      localStorage.setItem(STORAGE_COLLAPSE, this.$app.hasClass('sidebar-collapsed') ? '1' : '0');
    },

    closeMobileSidebar: function () {
      this.$app.removeClass('sidebar-open');
      $('.mino-sidebar-overlay').removeClass('show');
    },

    _highlightActive: function () {
      var path = window.location.pathname.replace(/\/+$/, '');
      $('.mino-nav-link[href]').each(function () {
        var href = $(this).attr('href');
        if (!href || href === '#' || href === 'javascript:void(0)') return;
        try {
          var linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '');
          if (linkPath && path.indexOf(linkPath) !== -1 && linkPath !== '/') {
            $(this).addClass('active');
            var $parentSub = $(this).closest('.mino-nav-submenu');
            if ($parentSub.length) {
              $parentSub.addClass('show');
              $parentSub.prev('[data-mino-submenu-toggle]').attr('aria-expanded', 'true');
            }
          }
        } catch (err) { /* ignore */ }
      });
    }
  };

  window.MinoLayout = Layout;

  $(function () {
    Layout.init();
  });
})(window, jQuery);
