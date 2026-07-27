/**
 * Mino CRM — Role-Based Dashboard (AJAX + ApexCharts + DataTables)
 */
(function (window, $) {
  'use strict';

  var STORAGE_KEY = 'mino_dashboard_range';

  function chartColors() {
    var s = getComputedStyle(document.documentElement);
    return {
      primary: s.getPropertyValue('--mino-primary').trim() || '#0F766E',
      success: s.getPropertyValue('--mino-success').trim() || '#059669',
      warning: s.getPropertyValue('--mino-warning').trim() || '#D97706',
      info: s.getPropertyValue('--mino-info').trim() || '#0284C7',
      danger: s.getPropertyValue('--mino-danger').trim() || '#DC2626',
      muted: s.getPropertyValue('--mino-text-muted').trim() || '#64748B',
      border: s.getPropertyValue('--mino-border').trim() || '#E2E8F0',
      text: s.getPropertyValue('--mino-text-secondary').trim() || '#475569'
    };
  }

  function escapeHtml(str) {
    return $('<div>').text(str || '').html();
  }

  var Dashboard = {
    $root: null,
    dataUrl: '',
    charts: {},
    dataTables: {},
    state: {
      range: 'last_30_days',
      startDate: '',
      endDate: '',
      loading: false
    },

    init: function () {
      this.$root = $('#mino-dashboard');
      if (!this.$root.length) return;

      this.dataUrl = this.$root.data('url');
      this.state.range = localStorage.getItem(STORAGE_KEY) || this.$root.data('default-range') || 'last_30_days';
      this.syncRangeButtons();
      this.bindEvents();
      this.loadData();

      $(document).on('mino:themechange', function () {
        Dashboard.rerenderCharts();
      });
    },

    bindEvents: function () {
      var self = this;

      $(document).on('click', '.dashboard-range-btn', function () {
        var range = $(this).data('range');
        if (range === 'custom') return;
        self.setRange(range);
      });

      $('#btnCustomRange').on('click', function () {
        $('#dashboardCustomRange').toggleClass('d-none');
      });

      $('#btnApplyCustomRange').on('click', function () {
        var start = $('#dashStartDate').val();
        var end = $('#dashEndDate').val();
        if (!start || !end || start > end) {
          if (window.MinoComponents) {
            MinoComponents.toast('warning', 'Select a valid date range.');
          }
          return;
        }
        self.state.startDate = start;
        self.state.endDate = end;
        self.setRange('custom');
      });

      $('#btnRefreshDashboard').on('click', function () {
        self.loadData(true);
      });
    },

    setRange: function (range) {
      this.state.range = range;
      localStorage.setItem(STORAGE_KEY, range);
      this.syncRangeButtons();
      this.loadData();
    },

    syncRangeButtons: function () {
      $('.dashboard-range-btn').removeClass('active btn-soft-primary').addClass('btn-ghost');
      $('.dashboard-range-btn[data-range="' + this.state.range + '"]')
        .removeClass('btn-ghost')
        .addClass('active btn-soft-primary');
    },

    loadData: function (force) {
      if (this.state.loading && !force) return;
      this.state.loading = true;
      this.showLoaders();

      var params = { range: this.state.range };
      if (this.state.range === 'custom') {
        params.start_date = this.state.startDate;
        params.end_date = this.state.endDate;
      }

      var self = this;
      $.ajax({
        url: this.dataUrl,
        method: 'GET',
        data: params,
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .done(function (res) {
          if (!res.success) {
            self.onError(res.message || 'Failed to load dashboard.');
            return;
          }
          if (res.csrf_hash) {
            self.$root.attr('data-csrf-hash', res.csrf_hash);
          }
          self.render(res.data || {});
        })
        .fail(function (xhr) {
          var msg = 'Unable to load dashboard data.';
          if (xhr.status === 401) msg = 'Session expired. Please sign in again.';
          if (xhr.status === 403) msg = 'You do not have permission to view this dashboard.';
          self.onError(msg);
        })
        .always(function () {
          self.state.loading = false;
        });
    },

    onError: function (message) {
      if (window.MinoComponents) {
        MinoComponents.toast('error', message);
      }
    },

    showLoaders: function () {
      $('.dashboard-widget-loading').removeClass('d-none');
      $('#kpi-skeleton').removeClass('d-none');
      $('#kpi-grid').addClass('d-none');
    },

    render: function (data) {
      if (data.welcome) this.renderWelcome(data.welcome);
      if (data.kpis) this.renderKpis(data.kpis);
      if (data.charts) this.renderCharts(data.charts);
      if (data.activities) this.renderActivities(data.activities);
      if (data.upcoming_followups) this.renderUpcomingList('#followups-list', '#followups-empty', '#followups-loading', data.upcoming_followups);
      if (data.upcoming_tasks) this.renderUpcomingList('#tasks-upcoming-list', '#tasks-upcoming-empty', '#tasks-upcoming-loading', data.upcoming_tasks);
      if (data.upcoming_meetings) this.renderUpcomingList('#meetings-list', '#meetings-empty', '#meetings-loading', data.upcoming_meetings);
      if (data.birthdays) this.renderBirthdays(data.birthdays);
      if (data.quick_actions) this.renderQuickActions(data.quick_actions);
      if (data.tables) this.renderTables(data.tables, data.context);
    },

    renderWelcome: function (w) {
      $('#welcome-skeleton-title, #welcome-skeleton-sub').addClass('d-none');
      $('#welcome-content, #welcomeIconWrap').removeClass('d-none');
      $('#welcomeTitle').text(w.greeting + ', ' + w.user_name);
      $('#welcomeSubtitle').text(w.scope_label + ' · ' + w.range_label);
      $('#welcomeOrg').text(w.org_name);
      $('#welcomeRole').text(w.role_name);
      $('#welcomeRange').text(w.range_label);
    },

    renderKpis: function (kpis) {
      var html = '';
      kpis.forEach(function (k) {
        var trendClass = k.trend && k.trend.direction === 'down' ? 'down' : (k.trend && k.trend.direction === 'up' ? 'up' : '');
        var trendIcon = k.trend && k.trend.direction === 'down' ? 'fa-arrow-down' : (k.trend && k.trend.direction === 'up' ? 'fa-arrow-up' : 'fa-minus');
        var trendHtml = k.trend && k.trend.label
          ? '<span class="' + trendClass + '"><i class="fas ' + trendIcon + '"></i> ' + escapeHtml(k.trend.label) + '</span>'
          : '';

        html += '<div class="stat-card stat-card--' + escapeHtml(k.tone) + '">' +
          '<div class="d-flex justify-content-between align-items-start">' +
          '<div>' +
          '<div class="stat-card__label">' + escapeHtml(k.label) + '</div>' +
          '<p class="stat-card__value">' + escapeHtml(k.value) + '</p>' +
          '<div class="stat-card__meta">' + escapeHtml(k.meta) + (trendHtml ? ' · ' + trendHtml : '') + '</div>' +
          '</div>' +
          '<div class="stat-card__icon"><i class="fas ' + escapeHtml(k.icon) + '"></i></div>' +
          '</div></div>';
      });

      $('#kpi-skeleton').addClass('d-none');
      $('#kpi-grid').removeClass('d-none').html(html);
    },

    renderCharts: function (charts) {
      this._lastCharts = charts;
      var map = {
        revenue_overview: { el: '#chartRevenueOverview', loader: '#chart-revenue-loading' },
        lead_status: { el: '#chartLeadStatus', loader: '#chart-lead-status-loading' },
        monthly_leads: { el: '#chartMonthlyLeads', loader: '#chart-monthly-leads-loading' },
        sales_pipeline: { el: '#chartSalesPipeline', loader: '#chart-pipeline-loading' },
        lead_sources: { el: '#chartLeadSources', loader: '#chart-sources-loading' },
        tasks_completion: { el: '#chartTasksCompletion', loader: '#chart-tasks-loading' }
      };

      var self = this;
      Object.keys(charts).forEach(function (key) {
        var cfg = map[key];
        if (!cfg || !charts[key]) return;
        $(cfg.loader).addClass('d-none');
        $(cfg.el).removeClass('d-none');
        self.renderChart(key, cfg.el, charts[key]);
      });
    },

    renderChart: function (key, selector, config) {
      if (typeof ApexCharts === 'undefined') return;
      var el = document.querySelector(selector);
      if (!el) return;

      if (this.charts[key]) {
        this.charts[key].destroy();
        this.charts[key] = null;
      }

      var c = chartColors();
      var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      var base = {
        chart: {
          fontFamily: 'Plus Jakarta Sans, sans-serif',
          background: 'transparent',
          toolbar: { show: false }
        },
        colors: [c.primary, c.info, c.success, c.warning, c.danger],
        legend: { labels: { colors: c.text } },
        grid: { borderColor: c.border, strokeDashArray: 4 },
        tooltip: { theme: isDark ? 'dark' : 'light' },
        dataLabels: { enabled: false }
      };

      var options = {};

      if (config.type === 'donut') {
        options = $.extend(true, {}, base, {
          chart: $.extend({}, base.chart, { type: 'donut', height: 260 }),
          series: config.series,
          labels: config.labels,
          legend: { position: 'bottom', labels: { colors: c.text } },
          plotOptions: {
            pie: {
              donut: {
                size: '68%',
                labels: {
                  show: true,
                  total: { show: true, label: 'Total', color: c.muted }
                }
              }
            }
          },
          stroke: { width: 0 }
        });
      } else if (config.type === 'bar') {
        options = $.extend(true, {}, base, {
          chart: $.extend({}, base.chart, { type: 'bar', height: 280 }),
          series: config.series,
          xaxis: {
            categories: config.categories,
            labels: { style: { colors: c.muted, fontSize: '12px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
          },
          yaxis: { labels: { style: { colors: c.muted, fontSize: '12px' } } },
          plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } }
        });
      } else {
        options = $.extend(true, {}, base, {
          chart: $.extend({}, base.chart, { type: 'area', height: 300 }),
          series: config.series,
          stroke: { curve: 'smooth', width: 2.5 },
          fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
          },
          xaxis: {
            categories: config.categories,
            labels: { style: { colors: c.muted, fontSize: '12px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
          },
          yaxis: { labels: { style: { colors: c.muted, fontSize: '12px' } } },
          legend: { position: 'top', horizontalAlign: 'right', labels: { colors: c.text } }
        });
      }

      this.charts[key] = new ApexCharts(el, options);
      this.charts[key].render();
    },

    rerenderCharts: function () {
      var last = this._lastCharts;
      if (!last) return;
      this.renderCharts(last);
    },

    renderActivities: function (items) {
      $('#activity-loading').addClass('d-none');
      if (!items.length) {
        $('#activity-empty').removeClass('d-none');
        return;
      }
      var html = '';
      items.forEach(function (a) {
        html += '<li class="mino-timeline-item">' +
          '<span class="mino-timeline-dot ' + escapeHtml(a.dot) + '"></span>' +
          '<div class="mino-timeline-content">' +
          '<div class="mino-timeline-title">' + escapeHtml(a.title) + '</div>' +
          '<div>' + escapeHtml(a.description) + '</div>' +
          '<div class="mino-timeline-time">' + escapeHtml(a.time_ago) + '</div>' +
          '</div></li>';
      });
      $('#activity-list').removeClass('d-none').html(html);
    },

    renderUpcomingList: function (listSel, emptySel, loaderSel, items) {
      $(loaderSel).addClass('d-none');
      if (!items.length) {
        $(emptySel).removeClass('d-none');
        return;
      }
      var html = '';
      items.forEach(function (item) {
        html += '<div class="followup-item">' +
          '<div class="followup-item__time">' + escapeHtml(item.time).replace(' ', '<br>') + '</div>' +
          '<div class="followup-item__body">' +
          '<div class="followup-item__title">' + escapeHtml(item.title) + '</div>' +
          '<div class="followup-item__meta">' + escapeHtml(item.meta) + '</div>' +
          '</div>' +
          '<span class="mino-badge mino-badge-' + escapeHtml(item.tone) + '">' + escapeHtml(item.badge) + '</span>' +
          '</div>';
      });
      $(listSel).removeClass('d-none').html(html);
    },

    renderBirthdays: function (items) {
      if (!items.length) {
        $('#birthdays-empty').removeClass('d-none');
        return;
      }
      var html = '';
      items.forEach(function (b) {
        html += '<div class="col-sm-6 col-md-3"><div class="mino-card p-3 h-100">' +
          '<div class="fw-semibold">' + escapeHtml(b.title) + '</div>' +
          '<div class="mino-text-xs mino-text-muted">' + escapeHtml(b.meta) + '</div>' +
          '<span class="mino-badge mino-badge-' + escapeHtml(b.tone) + ' mt-2">' + escapeHtml(b.badge) + '</span>' +
          '</div></div>';
      });
      $('#birthdays-list').removeClass('d-none').html(html);
    },

    renderQuickActions: function (actions) {
      $('#quick-actions-loading').addClass('d-none');
      if (!actions.length) return;
      var html = '';
      actions.forEach(function (a) {
        html += '<a href="' + escapeHtml(a.url) + '" class="quick-action" data-action="' + escapeHtml(a.key) + '">' +
          '<span class="quick-action__icon"><i class="fas ' + escapeHtml(a.icon) + '"></i></span>' +
          '<span class="quick-action__label">' + escapeHtml(a.label) + '</span></a>';
      });
      $('#quick-actions-grid').removeClass('d-none').html(html);

      $('#quick-actions-grid').off('click', '[data-action]').on('click', '[data-action]', function (e) {
        var key = $(this).data('action');
        if (key !== 'invite_user' && $(this).attr('href') === '#') {
          e.preventDefault();
          if (window.MinoComponents) {
            MinoComponents.toast('info', 'This module will be available soon.');
          }
        }
      });
    },

    renderTables: function (tables, context) {
      var self = this;
      var scopeNote = context && context.scope === 'own' ? ' · Your records' : '';

      if (tables.leads) {
        self.initDataTable('leads', '#tableLeads', '#table-leads-loading', '#table-leads-empty', '#table-leads-sub',
          tables.leads, scopeNote, function (row) {
            return '<tr>' +
              '<td><div class="d-flex align-items-center gap-2">' +
              '<span class="mino-avatar mino-avatar-sm">' + escapeHtml(row.initials) + '</span>' +
              '<div><div class="fw-semibold">' + escapeHtml(row.name) + '</div>' +
              '<div class="mino-text-xs mino-text-muted">' + escapeHtml(row.email) + '</div></div></div></td>' +
              '<td>' + escapeHtml(row.company) + '</td>' +
              '<td><span class="mino-badge mino-badge-' + escapeHtml(row.status_tone) + ' mino-badge-dot">' + escapeHtml(row.status) + '</span></td>' +
              '<td>' + escapeHtml(row.col_a) + '</td>' +
              '<td>' + escapeHtml(row.col_b) + '</td></tr>';
          });
      }

      if (tables.contacts) {
        self.initDataTable('contacts', '#tableContacts', '#table-contacts-loading', '#table-contacts-empty', '#table-contacts-sub',
          tables.contacts, scopeNote, function (row) {
            return self.personRow(row);
          });
      }

      if (tables.deals) {
        self.initDataTable('deals', '#tableDeals', '#table-deals-loading', '#table-deals-empty', '#table-deals-sub',
          tables.deals, scopeNote, function (row) {
            return self.personRow(row, true);
          });
      }

      if (tables.tasks) {
        self.initDataTable('tasks', '#tableTasks', '#table-tasks-loading', '#table-tasks-empty', '#table-tasks-sub',
          tables.tasks, scopeNote, function (row) {
            return '<tr>' +
              '<td><div class="fw-semibold">' + escapeHtml(row.name) + '</div></td>' +
              '<td>' + escapeHtml(row.company) + '</td>' +
              '<td><span class="mino-badge mino-badge-' + escapeHtml(row.status_tone) + '">' + escapeHtml(row.status) + '</span></td>' +
              '<td>' + escapeHtml(row.col_c) + '</td>' +
              '<td>' + escapeHtml(row.col_b) + '</td></tr>';
          });
      }
    },

    personRow: function (row, isDeal) {
      return '<tr>' +
        '<td><div class="d-flex align-items-center gap-2">' +
        '<span class="mino-avatar mino-avatar-sm">' + escapeHtml(row.initials) + '</span>' +
        '<div><div class="fw-semibold">' + escapeHtml(row.name) + '</div>' +
        '<div class="mino-text-xs mino-text-muted">' + escapeHtml(row.email) + '</div></div></div></td>' +
        '<td>' + escapeHtml(row.company) + '</td>' +
        '<td><span class="mino-badge mino-badge-' + escapeHtml(row.status_tone) + ' mino-badge-dot">' + escapeHtml(row.status) + '</span></td>' +
        '<td>' + escapeHtml(isDeal ? row.col_a : row.col_a) + '</td>' +
        '<td>' + escapeHtml(row.col_b) + '</td></tr>';
    },

    initDataTable: function (key, tableSel, loaderSel, emptySel, subSel, rows, scopeNote, rowFn) {
      $(loaderSel).addClass('d-none');
      $(subSel).text(rows.length + ' record' + (rows.length === 1 ? '' : 's') + scopeNote + (rows[0] && rows[0].placeholder ? ' · Sample data' : ''));

      if (!rows.length) {
        $(emptySel).removeClass('d-none');
        return;
      }

      var $table = $(tableSel);
      var html = '';
      rows.forEach(function (row) { html += rowFn(row); });
      $table.find('tbody').html(html);
      $table.removeClass('d-none');

      if (this.dataTables[key]) {
        this.dataTables[key].destroy();
        this.dataTables[key] = null;
      }

      if ($.fn.DataTable) {
        this.dataTables[key] = $table.DataTable({
          pageLength: 5,
          lengthChange: false,
          searching: true,
          ordering: true,
          info: true,
          dom: 'ftip',
          language: { search: '', searchPlaceholder: 'Search…' }
        });
      }
    },

    destroyAll: function () {
      var self = this;
      Object.keys(this.charts).forEach(function (key) {
        if (self.charts[key]) {
          self.charts[key].destroy();
          self.charts[key] = null;
        }
      });
      Object.keys(this.dataTables).forEach(function (key) {
        if (self.dataTables[key]) {
          self.dataTables[key].destroy();
          self.dataTables[key] = null;
        }
      });
    }
  };

  window.MinoDashboard = Dashboard;

  $(function () {
    if ($('#mino-dashboard').length) {
      Dashboard.init();
    }
  });
})(window, jQuery);
