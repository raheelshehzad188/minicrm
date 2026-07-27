/**
 * Mino CRM — App Bootstrap
 * theme.js, layout.js, components.js, dashboard.js load separately.
 * This file holds shared helpers.
 */
(function (window, $) {
  'use strict';

  window.MinoCRM = {
    version: '1.0.0',

    asset: function (path) {
      var base = $('meta[name="mino-base"]').attr('content') || '';
      return base.replace(/\/$/, '') + '/' + String(path).replace(/^\//, '');
    },

    init: function () {
      // Placeholder for future module hooks
    }
  };

  $(function () {
    window.MinoCRM.init();
  });
})(window, jQuery);
