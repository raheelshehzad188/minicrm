<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Module — role-based CRM overview
 */
class Dashboard extends Auth_Controller {

	protected $required_permission = 'dashboard.view';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Dashboard_lib');
		$this->load->model('dashboard/Dashboard_model');
		$this->config->load('dashboard');
	}

	public function index()
	{
		$context = $this->dashboard_lib->build_context();

		$data = array(
			'page_title'    => 'Dashboard',
			'page_subtitle' => 'Overview of your sales pipeline and team activity',
			'active_menu'   => 'dashboard',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Dashboard', 'url' => ''),
			),
			'page_actions'  => $this->_filter_actions_html(),
			'content_view'  => 'index',
			'content_data'  => array(
				'widgets'        => $this->dashboard_lib->visible_widgets($context),
				'filter_options' => $this->dashboard_lib->filter_options(),
				'default_range'  => $this->config->item('dashboard_default_range'),
				'data_url'       => site_url('dashboard/data'),
				'csrf_name'      => $this->security->get_csrf_token_name(),
				'csrf_hash'      => $this->security->get_csrf_hash(),
			),
		);
		$this->load->view('layouts/master', $data);
	}

	/**
	 * AJAX — full dashboard payload for widgets
	 */
	public function data()
	{
		if ( ! $this->is_ajax())
		{
			show_404();
		}

		$range      = $this->input->get('range', TRUE);
		$start_date = $this->input->get('start_date', TRUE);
		$end_date   = $this->input->get('end_date', TRUE);

		$context = $this->dashboard_lib->build_context($range, $start_date, $end_date);
		$this->Dashboard_model->set_context($context);

		$widgets = $this->dashboard_lib->visible_widgets($context);
		$payload = array(
			'context' => array(
				'range_key'   => $context['range_key'],
				'range_label' => $context['range_label'],
				'date_start'  => $context['date_start'],
				'date_end'    => $context['date_end'],
				'role_slug'   => $context['role_slug'],
				'scope'       => $context['own_only'] ? 'own' : ($context['is_manager'] ? 'team' : 'org'),
			),
			'widgets' => $widgets,
		);

		if (in_array('welcome', $widgets, TRUE))
		{
			$payload['welcome'] = $this->Dashboard_model->get_welcome();
		}

		if (in_array('kpis', $widgets, TRUE))
		{
			$kpi_keys = $this->dashboard_lib->visible_kpis($context);
			$payload['kpis'] = $this->Dashboard_model->get_kpis($kpi_keys);
		}

		if (in_array('charts', $widgets, TRUE))
		{
			$chart_keys = $this->dashboard_lib->visible_charts($context);
			$payload['charts'] = $this->Dashboard_model->get_charts($chart_keys);
		}

		if (in_array('recent_activity', $widgets, TRUE))
		{
			$payload['activities'] = $this->Dashboard_model->get_recent_activities(12);
		}

		if (in_array('upcoming_followups', $widgets, TRUE))
		{
			$payload['upcoming_followups'] = $this->Dashboard_model->get_upcoming_followups();
		}

		if (in_array('upcoming_tasks', $widgets, TRUE))
		{
			$payload['upcoming_tasks'] = $this->Dashboard_model->get_upcoming_tasks();
		}

		if (in_array('upcoming_meetings', $widgets, TRUE))
		{
			$payload['upcoming_meetings'] = $this->Dashboard_model->get_upcoming_meetings();
		}

		if (in_array('birthdays', $widgets, TRUE))
		{
			$payload['birthdays'] = $this->Dashboard_model->get_birthdays();
		}

		if (in_array('quick_actions', $widgets, TRUE))
		{
			$payload['quick_actions'] = $this->dashboard_lib->quick_actions($context);
		}

		$tables = array();
		if (in_array('table_leads', $widgets, TRUE))
		{
			$tables['leads'] = $this->Dashboard_model->get_table_leads();
		}
		if (in_array('table_contacts', $widgets, TRUE))
		{
			$tables['contacts'] = $this->Dashboard_model->get_table_contacts();
		}
		if (in_array('table_deals', $widgets, TRUE))
		{
			$tables['deals'] = $this->Dashboard_model->get_table_deals();
		}
		if (in_array('table_tasks', $widgets, TRUE))
		{
			$tables['tasks'] = $this->Dashboard_model->get_table_tasks();
		}
		if ( ! empty($tables))
		{
			$payload['tables'] = $tables;
		}

		return $this->json_response(TRUE, 'OK', $payload);
	}

	protected function _filter_actions_html()
	{
		return '
			<div class="dashboard-toolbar" id="dashboardToolbar">
				<div class="btn-group dashboard-range-group" role="group" aria-label="Date range">
					<button type="button" class="btn btn-sm btn-ghost dashboard-range-btn" data-range="today">Today</button>
					<button type="button" class="btn btn-sm btn-ghost dashboard-range-btn" data-range="yesterday">Yesterday</button>
					<button type="button" class="btn btn-sm btn-ghost dashboard-range-btn" data-range="last_7_days">7 Days</button>
					<button type="button" class="btn btn-sm btn-soft-primary dashboard-range-btn active" data-range="last_30_days">30 Days</button>
					<button type="button" class="btn btn-sm btn-ghost dashboard-range-btn" data-range="this_month">This Month</button>
					<button type="button" class="btn btn-sm btn-ghost dashboard-range-btn" data-range="last_month">Last Month</button>
					<button type="button" class="btn btn-sm btn-ghost" id="btnCustomRange" data-range="custom"><i class="fas fa-calendar"></i> Custom</button>
				</div>
				<button type="button" class="btn btn-sm btn-secondary" id="btnRefreshDashboard" title="Refresh">
					<i class="fas fa-rotate-right"></i>
				</button>
			</div>
			<div class="dashboard-custom-range d-none" id="dashboardCustomRange">
				<input type="date" class="form-control form-control-sm" id="dashStartDate" aria-label="Start date">
				<span class="mino-text-sm mino-text-muted">to</span>
				<input type="date" class="form-control form-control-sm" id="dashEndDate" aria-label="End date">
				<button type="button" class="btn btn-sm btn-primary" id="btnApplyCustomRange">Apply</button>
			</div>
		';
	}
}
