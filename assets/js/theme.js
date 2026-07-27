/**
 * Mino CRM — Theme Manager
 * Light / Dark mode with localStorage persistence
 */
(function (window, $) {
  'use strict';

  var STORAGE_KEY = 'mino-theme';

  var Theme = {
    init: function () {
      var saved = localStorage.getItem(STORAGE_KEY);
      var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      var theme = saved || (prefersDark ? 'dark' : 'light');
      this.set(theme, false);

      $(document).on('click', '[data-mino-theme-toggle]', function (e) {
        e.preventDefault();
        Theme.toggle();
      });

      $(document).on('click', '[data-mino-theme]', function (e) {
        e.preventDefault();
        Theme.set($(this).data('mino-theme'));
      });
    },

    get: function () {
      return document.documentElement.getAttribute('data-theme') || 'light';
    },

    set: function (theme, persist) {
      if (persist === undefined) persist = true;
      theme = theme === 'dark' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', theme);
      if (persist) localStorage.setItem(STORAGE_KEY, theme);
      this._syncUi(theme);
      $(document).trigger('mino:themechange', [theme]);
    },

    toggle: function () {
      this.set(this.get() === 'dark' ? 'light' : 'dark');
    },

    _syncUi: function (theme) {
      var isDark = theme === 'dark';
      $('[data-mino-theme-toggle] i').each(function () {
        $(this).removeClass('fa-moon fa-sun').addClass(isDark ? 'fa-sun' : 'fa-moon');
      });
      $('[data-mino-theme]').removeClass('active');
      $('[data-mino-theme="' + theme + '"]').addClass('active');
    }
  };

  window.MinoTheme = Theme;

  $(function () {
    Theme.init();
  });
})(window, jQuery);
