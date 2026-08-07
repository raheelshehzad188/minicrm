<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Library — widget registry, date ranges, role scoping
 */
class Dashboard_lib {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load('dashboard');
	}

	/**
	 * Build dashboard context for model queries
	 */
	public function build_context($range = NULL, $start_date = NULL, $end_date = NULL, $lead_type = NULL)
	{
		$range = $range ?: $this->CI->config->item('dashboard_default_range');
		$dates = $this->parse_date_range($range, $start_date, $end_date);

		$role_slug = (string) $this->CI->session->userdata('role_slug');

		$this->CI->load->library('Lead_lib');
		$lead_type = $this->CI->lead_lib->normalize_lead_type($lead_type);

		return array(
			'org_id'       => (int) current_org_id(),
			'user_id'      => (int) current_user_id(),
			'user_name'    => current_user_name(),
			'role_slug'    => $role_slug,
			'is_owner'     => $this->CI->permission_lib->is_owner(),
			'is_admin'     => $this->CI->permission_lib->is_admin(),
			'is_manager'   => $role_slug === 'manager',
			'is_sales'     => $role_slug === 'sales_person',
			'own_only'     => $role_slug === 'sales_person',
			'team_scope'   => in_array($role_slug, array('manager', 'admin', 'owner'), TRUE),
			'range_key'    => $dates['key'],
			'range_label'  => $dates['label'],
			'date_start'   => $dates['start'],
			'date_end'     => $dates['end'],
			'lead_type'    => $lead_type ?: '',
			'permissions'  => $this->CI->session->userdata('permissions') ?: array(),
		);
	}

	/**
	 * Parse filter preset or custom date range
	 */
	public function parse_date_range($range, $start_date = NULL, $end_date = NULL)
	{
		$today = date('Y-m-d');

		$presets = array(
			'today'         => array($today, $today, 'Today'),
			'yesterday'     => array(date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('-1 day')), 'Yesterday'),
			'last_7_days'   => array(date('Y-m-d', strtotime('-6 days')), $today, 'Last 7 Days'),
			'last_30_days'  => array(date('Y-m-d', strtotime('-29 days')), $today, 'Last 30 Days'),
			'this_month'    => array(date('Y-m-01'), $today, 'This Month'),
			'last_month'    => array(
				date('Y-m-01', strtotime('first day of last month')),
				date('Y-m-t', strtotime('last day of last month')),
				'Last Month',
			),
		);

		if ($range === 'custom' && $start_date && $end_date)
		{
			$start = date('Y-m-d', strtotime($start_date));
			$end   = date('Y-m-d', strtotime($end_date));
			if ($start && $end && $start <= $end)
			{
				return array(
					'key'   => 'custom',
					'start' => $start,
					'end'   => $end,
					'label' => date('M j', strtotime($start)) . ' – ' . date('M j, Y', strtotime($end)),
				);
			}
		}

		if (isset($presets[$range]))
		{
			return array(
				'key'   => $range,
				'start' => $presets[$range][0],
				'end'   => $presets[$range][1],
				'label' => $presets[$range][2],
			);
		}

		$fallback = $presets['last_30_days'];
		return array(
			'key'   => 'last_30_days',
			'start' => $fallback[0],
			'end'   => $fallback[1],
			'label' => $fallback[2],
		);
	}

	/**
	 * Enabled widgets for current user
	 */
	public function visible_widgets($context = NULL)
	{
		$context = $context ?: $this->build_context();
		$registry = $this->CI->config->item('dashboard_widgets') ?: array();
		$visible  = array();

		foreach ($registry as $key => $widget)
		{
			if (empty($widget['enabled']))
			{
				continue;
			}
			if ( ! $this->widget_allowed_for_role($key, $widget, $context))
			{
				continue;
			}
			if ( ! $this->widget_allowed_by_permission($key, $context))
			{
				continue;
			}
			$visible[] = $key;
		}

		return $visible;
	}

	protected function widget_allowed_for_role($key, $widget, $context)
	{
		if ($context['is_owner'])
		{
			return TRUE;
		}
		$roles = isset($widget['roles']) ? $widget['roles'] : array();
		return in_array($context['role_slug'], $roles, TRUE);
	}

	protected function widget_allowed_by_permission($key, $context)
	{
		if ($context['is_owner'])
		{
			return TRUE;
		}

		$map = array(
			'table_leads'    => 'leads.view',
			'table_contacts' => 'contacts.view',
			'table_deals'    => 'deals.view',
			'table_tasks'    => 'tasks.view',
		);

		if (isset($map[$key]) && ! $this->CI->permission_lib->can($map[$key]))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * KPI keys visible for current role/permissions
	 */
	public function visible_kpis($context = NULL)
	{
		$context = $context ?: $this->build_context();
		$all     = $this->CI->config->item('dashboard_kpi_keys') ?: array();
		$visible = array();

		foreach ($all as $key)
		{
			if ($this->kpi_allowed($key, $context))
			{
				$visible[] = $key;
			}
		}

		return $visible;
	}

	protected function kpi_allowed($key, $context)
	{
		if ($context['is_owner'])
		{
			return TRUE;
		}

		$perm_map = array(
			'total_leads'      => 'leads.view',
			'clinic_leads'     => 'leads.view',
			'academy_leads'    => 'leads.view',
			'new_leads'        => 'leads.view',
			'qualified_leads'  => 'leads.view',
			'won_deals'        => 'deals.view',
			'lost_deals'       => 'deals.view',
			'total_contacts'   => 'contacts.view',
			'todays_followups' => 'leads.view',
			'pending_tasks'    => 'tasks.view',
			'completed_tasks'  => 'tasks.view',
			'active_users'     => 'users.view',
			'monthly_revenue'  => 'deals.view',
			'conversion_rate'  => 'leads.view',
		);

		if (isset($perm_map[$key]) && ! $this->CI->permission_lib->can($perm_map[$key]))
		{
			return FALSE;
		}

		// Sales person does not see org-wide active users
		if ($key === 'active_users' && $context['is_sales'])
		{
			return $this->CI->permission_lib->can('users.view');
		}

		return TRUE;
	}

	/**
	 * Chart keys visible for current role/permissions
	 */
	public function visible_charts($context = NULL)
	{
		$context = $context ?: $this->build_context();
		$all     = $this->CI->config->item('dashboard_chart_keys') ?: array();
		$visible = array();

		foreach ($all as $key)
		{
			if ($this->chart_allowed($key, $context))
			{
				$visible[] = $key;
			}
		}

		return $visible;
	}

	protected function chart_allowed($key, $context)
	{
		if ($context['is_owner'])
		{
			return TRUE;
		}

		$perm_map = array(
			'lead_status'       => 'leads.view',
			'monthly_leads'     => 'leads.view',
			'sales_pipeline'    => 'deals.view',
			'lead_sources'      => 'leads.view',
			'tasks_completion'  => 'tasks.view',
			'revenue_overview'  => 'deals.view',
		);

		return ! isset($perm_map[$key]) || $this->CI->permission_lib->can($perm_map[$key]);
	}

	/**
	 * Quick actions with permission gates
	 */
	public function quick_actions($context = NULL)
	{
		$context = $context ?: $this->build_context();

		$actions = array(
			array(
				'key'   => 'create_lead',
				'label' => 'Create Lead',
				'icon'  => 'fa-bullseye',
				'url'   => site_url('leads'),
				'perm'  => 'leads.create',
			),
			array(
				'key'   => 'create_contact',
				'label' => 'Create Contact',
				'icon'  => 'fa-address-book',
				'url'   => '#',
				'perm'  => 'contacts.create',
			),
			array(
				'key'   => 'create_task',
				'label' => 'Create Task',
				'icon'  => 'fa-list-check',
				'url'   => '#',
				'perm'  => 'tasks.create',
			),
			array(
				'key'   => 'create_deal',
				'label' => 'Create Deal',
				'icon'  => 'fa-handshake',
				'url'   => '#',
				'perm'  => 'deals.create',
			),
			array(
				'key'   => 'invite_user',
				'label' => 'Invite User',
				'icon'  => 'fa-user-plus',
				'url'   => site_url('users'),
				'perm'  => 'users.create',
			),
		);

		$out = array();
		foreach ($actions as $action)
		{
			if ($context['is_owner'] || $this->CI->permission_lib->can($action['perm']))
			{
				unset($action['perm']);
				$out[] = $action;
			}
		}

		return $out;
	}

	public function filter_options()
	{
		return array(
			array('key' => 'today', 'label' => 'Today'),
			array('key' => 'yesterday', 'label' => 'Yesterday'),
			array('key' => 'last_7_days', 'label' => 'Last 7 Days'),
			array('key' => 'last_30_days', 'label' => 'Last 30 Days'),
			array('key' => 'this_month', 'label' => 'This Month'),
			array('key' => 'last_month', 'label' => 'Last Month'),
			array('key' => 'custom', 'label' => 'Custom Range'),
		);
	}
}
