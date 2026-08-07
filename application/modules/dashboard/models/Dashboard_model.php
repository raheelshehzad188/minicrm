<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Model — org-scoped metrics with CRM placeholder providers
 *
 * TODO: Replace placeholder_* methods with real module queries when Leads,
 * Contacts, Deals, and Tasks modules are implemented.
 */
class Dashboard_model extends CI_Model {

	protected $context = array();

	public function set_context(array $context)
	{
		$this->context = $context;
		return $this;
	}

	public function get_welcome()
	{
		$hour = (int) date('G');
		if ($hour < 12)
		{
			$greeting = 'Good morning';
		}
		elseif ($hour < 17)
		{
			$greeting = 'Good afternoon';
		}
		else
		{
			$greeting = 'Good evening';
		}

		$scope_label = 'Organization overview';
		if ( ! empty($this->context['is_sales']))
		{
			$scope_label = 'Your personal pipeline';
		}
		elseif ( ! empty($this->context['is_manager']))
		{
			$scope_label = 'Team performance overview';
		}

		return array(
			'greeting'    => $greeting,
			'user_name'   => $this->context['user_name'],
			'org_name'    => current_org_name(),
			'role_name'   => current_role_name(),
			'scope_label' => $scope_label,
			'range_label' => $this->context['range_label'],
		);
	}

	public function get_kpis(array $keys)
	{
		$definitions = $this->kpi_definitions();
		$out         = array();

		foreach ($keys as $key)
		{
			if ( ! isset($definitions[$key]))
			{
				continue;
			}
			$def = $definitions[$key];
			$out[] = array(
				'key'   => $key,
				'label' => $def['label'],
				'value' => $this->format_kpi_value($key, $def['value']()),
				'meta'  => $def['meta'](),
				'icon'  => $def['icon'],
				'tone'  => $def['tone'],
				'trend' => $def['trend'](),
			);
		}

		return $out;
	}

	protected function kpi_definitions()
	{
		$ctx = $this->context;

		return array(
			'total_leads' => array(
				'label' => 'Total Leads',
				'icon'  => 'fa-bullseye',
				'tone'  => 'primary',
				'value' => function () { return $this->count_leads(NULL); },
				'meta'  => function () use ($ctx) { return $ctx['own_only'] ? 'Assigned to you' : 'In selected period'; },
				'trend' => function () { return array('direction' => 'neutral', 'label' => 'Live count'); },
			),
			'clinic_leads' => array(
				'label' => 'Clinic Leads',
				'icon'  => 'fa-spa',
				'tone'  => 'success',
				'value' => function () { return $this->count_leads('clinic'); },
				'meta'  => function () { return 'Patient leads'; },
				'trend' => function () { return array('direction' => 'neutral', 'label' => 'Clinic'); },
			),
			'academy_leads' => array(
				'label' => 'Academy Leads',
				'icon'  => 'fa-graduation-cap',
				'tone'  => 'info',
				'value' => function () { return $this->count_leads('academy'); },
				'meta'  => function () { return 'Student leads'; },
				'trend' => function () { return array('direction' => 'neutral', 'label' => 'Academy'); },
			),
			'new_leads' => array(
				'label' => 'New Leads',
				'icon'  => 'fa-user-plus',
				'tone'  => 'info',
				'value' => function () { return $this->placeholder_count('new_leads', 8, 220); },
				'meta'  => function () { return 'Created in range'; },
				'trend' => function () { return array('direction' => 'up', 'label' => '8.2% this week'); },
			),
			'qualified_leads' => array(
				'label' => 'Qualified Leads',
				'icon'  => 'fa-star',
				'tone'  => 'success',
				'value' => function () { return $this->placeholder_count('qualified_leads', 5, 180); },
				'meta'  => function () { return 'Ready for pipeline'; },
				'trend' => function () { return array('direction' => 'up', 'label' => '4.1%'); },
			),
			'won_deals' => array(
				'label' => 'Won Deals',
				'icon'  => 'fa-trophy',
				'tone'  => 'success',
				'value' => function () { return $this->placeholder_count('won_deals', 2, 85); },
				'meta'  => function () { return 'Closed won'; },
				'trend' => function () { return array('direction' => 'up', 'label' => '6.3%'); },
			),
			'lost_deals' => array(
				'label' => 'Lost Deals',
				'icon'  => 'fa-circle-xmark',
				'tone'  => 'danger',
				'value' => function () { return $this->placeholder_count('lost_deals', 1, 40); },
				'meta'  => function () { return 'Closed lost'; },
				'trend' => function () { return array('direction' => 'down', 'label' => '1.2%'); },
			),
			'total_contacts' => array(
				'label' => 'Total Contacts',
				'icon'  => 'fa-address-book',
				'tone'  => 'info',
				'value' => function () { return $this->placeholder_count('total_contacts', 40, 1400); },
				'meta'  => function () { return 'Active contacts'; },
				'trend' => function () { return array('direction' => 'up', 'label' => '3.1%'); },
			),
			'todays_followups' => array(
				'label' => "Today's Follow Ups",
				'icon'  => 'fa-phone',
				'tone'  => 'primary',
				'value' => function () { return $this->placeholder_count('todays_followups', 0, 18); },
				'meta'  => function () { return 'Due today'; },
				'trend' => function () { return array('direction' => 'neutral', 'label' => 'On schedule'); },
			),
			'pending_tasks' => array(
				'label' => 'Pending Tasks',
				'icon'  => 'fa-clock',
				'tone'  => 'warning',
				'value' => function () { return $this->placeholder_count('pending_tasks', 3, 65); },
				'meta'  => function () { return 'Open tasks'; },
				'trend' => function () { return array('direction' => 'neutral', 'label' => 'Needs attention'); },
			),
			'completed_tasks' => array(
				'label' => 'Completed Tasks',
				'icon'  => 'fa-check-double',
				'tone'  => 'success',
				'value' => function () { return $this->placeholder_count('completed_tasks', 5, 120); },
				'meta'  => function () { return 'Done in range'; },
				'trend' => function () { return array('direction' => 'up', 'label' => '9.4%'); },
			),
			'active_users' => array(
				'label' => 'Active Users',
				'icon'  => 'fa-users',
				'tone'  => 'info',
				'value' => function () { return $this->count_active_users(); },
				'meta'  => function () { return 'In your organization'; },
				'trend' => function () { return array('direction' => 'neutral', 'label' => 'Team members'); },
			),
			'monthly_revenue' => array(
				'label' => 'Monthly Revenue',
				'icon'  => 'fa-dollar-sign',
				'tone'  => 'success',
				'value' => function () { return $this->placeholder_count('monthly_revenue', 12000, 185000); },
				'meta'  => function () { return 'Placeholder until deals module'; },
				'trend' => function () { return array('direction' => 'up', 'label' => '18.7%'); },
			),
			'conversion_rate' => array(
				'label' => 'Conversion Rate',
				'icon'  => 'fa-chart-line',
				'tone'  => 'warning',
				'value' => function () {
					$base = 18 + ($this->seed_hash('conversion') % 22);
					return ! empty($this->context['is_sales']) ? max(8, $base - 4) : $base;
				},
				'meta'  => function () { return 'Lead to qualified'; },
				'trend' => function () { return array('direction' => 'up', 'label' => 'On track'); },
			),
		);
	}

	protected function format_kpi_value($key, $value)
	{
		if ($key === 'monthly_revenue')
		{
			if ($value >= 1000)
			{
				return '$' . number_format($value / 1000, 0) . 'k';
			}
			return '$' . number_format($value);
		}
		if ($key === 'conversion_rate')
		{
			return (int) $value . '%';
		}
		return number_format((int) $value);
	}

	public function get_charts(array $keys)
	{
		$out = array();
		foreach ($keys as $key)
		{
			$method = 'chart_' . $key;
			if (method_exists($this, $method))
			{
				$out[$key] = $this->$method();
			}
		}
		return $out;
	}

	protected function chart_lead_status()
	{
		return array(
			'type'   => 'donut',
			'title'  => 'Lead Status',
			'series' => array(
				$this->placeholder_count('ls_new', 20, 80),
				$this->placeholder_count('ls_contacted', 15, 60),
				$this->placeholder_count('ls_qualified', 10, 45),
				$this->placeholder_count('ls_lost', 5, 25),
			),
			'labels' => array('New', 'Contacted', 'Qualified', 'Lost'),
		);
	}

	protected function chart_monthly_leads()
	{
		$months = array();
		$data   = array();
		for ($i = 11; $i >= 0; $i--)
		{
			$months[] = date('M', strtotime("-{$i} months"));
			$data[]   = $this->placeholder_count('ml_' . $i, 8, 95);
		}
		return array(
			'type'       => 'area',
			'title'      => 'Monthly Leads',
			'categories' => $months,
			'series'     => array(
				array('name' => 'Leads', 'data' => $data),
			),
		);
	}

	protected function chart_sales_pipeline()
	{
		return array(
			'type'   => 'bar',
			'title'  => 'Sales Pipeline',
			'series' => array(
				array(
					'name' => 'Deals',
					'data' => array(
						$this->placeholder_count('sp_prospect', 5, 30),
						$this->placeholder_count('sp_qualified', 4, 25),
						$this->placeholder_count('sp_proposal', 3, 18),
						$this->placeholder_count('sp_negotiation', 2, 12),
						$this->placeholder_count('sp_won', 1, 10),
					),
				),
			),
			'categories' => array('Prospect', 'Qualified', 'Proposal', 'Negotiation', 'Won'),
		);
	}

	protected function chart_lead_sources()
	{
		return array(
			'type'   => 'donut',
			'title'  => 'Lead Source Distribution',
			'series' => array(38, 24, 18, 12, 8),
			'labels' => array('Website', 'Referral', 'LinkedIn', 'Cold Call', 'Events'),
		);
	}

	protected function chart_tasks_completion()
	{
		$completed = $this->placeholder_count('tc_done', 20, 90);
		$pending   = $this->placeholder_count('tc_pending', 5, 40);
		return array(
			'type'   => 'donut',
			'title'  => 'Tasks Completion',
			'series' => array($completed, $pending),
			'labels' => array('Completed', 'Pending'),
		);
	}

	protected function chart_revenue_overview()
	{
		$months = array();
		$rev    = array();
		$deals  = array();
		for ($i = 11; $i >= 0; $i--)
		{
			$months[] = date('M', strtotime("-{$i} months"));
			$rev[]    = $this->placeholder_count('rev_' . $i, 15, 95);
			$deals[]  = $this->placeholder_count('deals_' . $i, 5, 45);
		}
		return array(
			'type'       => 'area',
			'title'      => 'Revenue Overview',
			'categories' => $months,
			'series'     => array(
				array('name' => 'Revenue ($k)', 'data' => $rev),
				array('name' => 'Deals', 'data' => $deals),
			),
		);
	}

	public function get_recent_activities($limit = 15)
	{
		$this->load->model('Activity_log_model');

		$filters = array(
			'date_start' => $this->context['date_start'] . ' 00:00:00',
			'date_end'   => $this->context['date_end'] . ' 23:59:59',
		);

		if ( ! empty($this->context['own_only']))
		{
			$filters['user_id'] = $this->context['user_id'];
		}

		$rows = $this->Activity_log_model->recent_filtered(
			$this->context['org_id'],
			$filters,
			$limit
		);

		if (empty($rows))
		{
			return $this->placeholder_activities();
		}

		$out = array();
		foreach ($rows as $row)
		{
			$out[] = array(
				'id'          => (int) $row->id,
				'title'       => $this->activity_title($row),
				'description' => $row->description,
				'user_name'   => $row->user_name ?: 'System',
				'time_ago'    => $this->time_ago($row->created_at),
				'dot'         => $this->activity_dot($row->action),
			);
		}

		return $out;
	}

	protected function placeholder_activities()
	{
		$items = array(
			array('title' => 'User Logged In', 'description' => $this->context['user_name'] . ' signed in', 'dot' => 'info'),
			array('title' => 'Lead Created', 'description' => 'New lead from BrightSoft', 'dot' => 'primary'),
			array('title' => 'Task Completed', 'description' => 'Proposal sent to Horizon Ltd', 'dot' => 'success'),
			array('title' => 'Deal Won', 'description' => 'Acme Industries — $24,000', 'dot' => 'success'),
			array('title' => 'Password Changed', 'description' => 'Security update completed', 'dot' => 'warning'),
		);

		if ($this->context['own_only'])
		{
			$items = array_slice($items, 0, 3);
		}

		$out = array();
		$mins = array(10, 45, 120, 240, 1440);
		foreach ($items as $i => $item)
		{
			$out[] = array(
				'id'          => $i + 1,
				'title'       => $item['title'],
				'description' => $item['description'],
				'user_name'   => $this->context['user_name'],
				'time_ago'    => $this->minutes_ago_label($mins[$i]),
				'dot'         => $item['dot'],
				'placeholder' => TRUE,
			);
		}
		return $out;
	}

	public function get_upcoming_followups($limit = 5)
	{
		return $this->placeholder_upcoming('followups', $limit);
	}

	public function get_upcoming_tasks($limit = 5)
	{
		return $this->placeholder_upcoming('tasks', $limit);
	}

	public function get_upcoming_meetings($limit = 4)
	{
		return $this->placeholder_upcoming('meetings', $limit);
	}

	public function get_birthdays($limit = 4)
	{
		if ($this->context['own_only'])
		{
			return array();
		}
		return $this->placeholder_upcoming('birthdays', $limit);
	}

	protected function placeholder_upcoming($type, $limit)
	{
		$pools = array(
			'followups' => array(
				array('time' => '10:00 AM', 'title' => 'Call with Sarah Mitchell', 'meta' => 'CloudSync · Discovery', 'badge' => 'Call', 'tone' => 'primary'),
				array('time' => '01:30 PM', 'title' => 'Demo walkthrough', 'meta' => 'Nexus Labs · Virtual', 'badge' => 'Demo', 'tone' => 'info'),
				array('time' => '04:00 PM', 'title' => 'Send revised proposal', 'meta' => 'Peak Retail · Email', 'badge' => 'Email', 'tone' => 'warning'),
			),
			'tasks' => array(
				array('time' => 'Today', 'title' => 'Prepare Q3 forecast', 'meta' => 'High priority', 'badge' => 'Task', 'tone' => 'warning'),
				array('time' => 'Tomorrow', 'title' => 'Update CRM notes', 'meta' => 'Horizon Ltd', 'badge' => 'Task', 'tone' => 'primary'),
				array('time' => 'Fri', 'title' => 'Contract review', 'meta' => 'Verde Corp', 'badge' => 'Task', 'tone' => 'info'),
			),
			'meetings' => array(
				array('time' => '11:00 AM', 'title' => 'Quarterly review', 'meta' => 'Team · Conference room', 'badge' => 'Meet', 'tone' => 'success'),
				array('time' => '03:00 PM', 'title' => 'Client onboarding', 'meta' => 'BrightSoft', 'badge' => 'Meet', 'tone' => 'primary'),
			),
			'birthdays' => array(
				array('time' => 'Today', 'title' => 'Alex Davis', 'meta' => 'Sales · Send wishes', 'badge' => 'Birthday', 'tone' => 'success'),
				array('time' => 'Fri', 'title' => 'Sarah Kim', 'meta' => 'Marketing', 'badge' => 'Birthday', 'tone' => 'info'),
			),
		);

		$items = isset($pools[$type]) ? $pools[$type] : array();
		if ($this->context['own_only'])
		{
			$items = array_slice($items, 0, max(1, $limit - 1));
		}

		return array_slice($items, 0, $limit);
	}

	public function get_table_leads($limit = 8)
	{
		$this->load->model('Lead_model');
		$this->load->library('Lead_lib');

		$filters = array(
			'date_from' => $this->context['date_start'],
			'date_to'   => $this->context['date_end'],
			'limit'     => $limit,
			'offset'    => 0,
		);
		if ( ! empty($this->context['lead_type']))
		{
			$filters['lead_type'] = $this->context['lead_type'];
		}
		if ( ! empty($this->context['own_only']))
		{
			$filters['assigned_to'] = $this->context['user_id'];
		}

		$rows = $this->Lead_model->datatable($filters);
		$out = array();
		foreach ($rows as $i => $row)
		{
			$name = trim(($row->first_name ?: '') . ' ' . ($row->last_name ?: ''));
			if ($name === '')
			{
				$name = $row->title;
			}
			$out[] = array(
				'id'          => (int) $row->id,
				'name'        => $name,
				'email'       => $row->email,
				'company'     => $row->company_name ?: ($row->branch ?: '—'),
				'status'      => $row->status_name ?: '—',
				'status_tone' => 'primary',
				'initials'    => user_initials($name),
				'col_a'       => isset($row->lead_type) ? ucfirst($row->lead_type) : '—',
				'col_b'       => $row->assignee_name ?: 'Unassigned',
				'col_c'       => $row->source_name ?: '—',
				'placeholder' => FALSE,
			);
		}
		return $out;
	}

	public function get_table_contacts($limit = 8)
	{
		return $this->placeholder_table_rows('contacts', $limit);
	}

	public function get_table_deals($limit = 8)
	{
		return $this->placeholder_table_rows('deals', $limit);
	}

	public function get_table_tasks($limit = 8)
	{
		return $this->placeholder_table_rows('tasks', $limit);
	}

	protected function placeholder_table_rows($type, $limit)
	{
		$owner = $this->context['user_name'];
		$rows  = array(
			array('name' => 'Emma Watson', 'email' => 'emma@brightsoft.io', 'company' => 'BrightSoft', 'status' => 'Qualified', 'status_tone' => 'success', 'extra' => 'Website', 'owner' => 'Alex Davis'),
			array('name' => 'James Lee', 'email' => 'james@nexus.dev', 'company' => 'Nexus Labs', 'status' => 'New', 'status_tone' => 'primary', 'extra' => 'Referral', 'owner' => 'Sarah Kim'),
			array('name' => 'Olivia Park', 'email' => 'olivia@peak.co', 'company' => 'Peak Retail', 'status' => 'Contacted', 'status_tone' => 'warning', 'extra' => 'LinkedIn', 'owner' => $owner),
			array('name' => 'Daniel Ruiz', 'email' => 'daniel@verde.com', 'company' => 'Verde Corp', 'status' => 'Cold', 'status_tone' => 'danger', 'extra' => 'Cold Call', 'owner' => 'Alex Davis'),
			array('name' => 'Ava Chen', 'email' => 'ava@horizon.app', 'company' => 'Horizon Ltd', 'status' => 'Proposal', 'status_tone' => 'info', 'extra' => 'Webinar', 'owner' => $owner),
		);

		if ($this->context['own_only'])
		{
			$rows = array_values(array_filter($rows, function ($r) use ($owner) {
				return $r['owner'] === $owner;
			}));
		}

		$out = array();
		foreach (array_slice($rows, 0, $limit) as $i => $row)
		{
			$out[] = array(
				'id'          => $i + 1,
				'name'        => $row['name'],
				'email'       => $row['email'],
				'company'     => $row['company'],
				'status'      => $row['status'],
				'status_tone' => $row['status_tone'],
				'initials'    => user_initials($row['name']),
				'col_a'       => $type === 'deals' ? '$' . number_format(12000 + ($i * 3400)) : $row['extra'],
				'col_b'       => $row['owner'],
				'col_c'       => $type === 'tasks' ? date('M j', strtotime('+' . $i . ' days')) : ($type === 'deals' ? 'Pipeline' : $row['extra']),
				'placeholder' => TRUE,
			);
		}

		return $out;
	}

	/**
	 * Real lead counts by type (respects date range, visibility, optional type filter).
	 */
	protected function count_leads($lead_type = NULL)
	{
		$this->load->model('Lead_model');
		$this->load->library('Lead_lib');

		// When dashboard is filtered to a type, hide the other type widget count
		if ($lead_type && ! empty($this->context['lead_type']) && $this->context['lead_type'] !== $lead_type)
		{
			return 0;
		}

		$type = $lead_type;
		if ( ! $type && ! empty($this->context['lead_type']))
		{
			$type = $this->context['lead_type'];
		}

		$filters = array(
			'date_from' => $this->context['date_start'],
			'date_to'   => $this->context['date_end'],
		);
		if ( ! empty($this->context['own_only']))
		{
			$filters['assigned_to'] = $this->context['user_id'];
		}

		return $this->Lead_model->count_by_type($type, $filters);
	}

	protected function count_active_users()
	{
		$this->db->from('users');
		$this->db->where('organization_id', (int) $this->context['org_id']);
		$this->db->where('status', 'active');
		$this->db->where('deleted_at IS NULL', NULL, FALSE);
		return (int) $this->db->count_all_results();
	}

	protected function scope_multiplier()
	{
		if ( ! empty($this->context['own_only']))
		{
			return 0.35;
		}
		if ( ! empty($this->context['is_manager']))
		{
			return 0.75;
		}
		return 1;
	}

	protected function placeholder_count($key, $min, $max)
	{
		$span  = max(1, $max - $min + 1);
		$value = $min + ($this->seed_hash($key) % $span);
		$mul   = $this->scope_multiplier();

		return max(0, (int) round($value * $mul));
	}

	protected function seed_hash($key)
	{
		$seed = $this->context['org_id'] . ':' . $this->context['user_id'] . ':' . $key . ':' . $this->context['range_key'];
		return abs(crc32($seed));
	}

	protected function activity_title($row)
	{
		$map = array(
			'login'            => 'User Logged In',
			'logout'           => 'User Logged Out',
			'create'           => 'Record Created',
			'update'           => 'Record Updated',
			'delete'           => 'Record Deleted',
			'password_change'  => 'Password Changed',
			'status_change'    => 'Status Changed',
		);

		if (isset($map[$row->action]))
		{
			return $map[$row->action];
		}

		if ($row->module)
		{
			return ucfirst($row->module) . ' — ' . ucfirst(str_replace('_', ' ', $row->action));
		}

		return ucfirst(str_replace('_', ' ', $row->action));
	}

	protected function activity_dot($action)
	{
		$map = array(
			'login'           => 'info',
			'logout'          => 'muted',
			'create'          => 'primary',
			'update'          => 'warning',
			'delete'          => 'danger',
			'password_change' => 'warning',
			'status_change'   => 'info',
		);
		return isset($map[$action]) ? $map[$action] : 'primary';
	}

	protected function time_ago($datetime)
	{
		$ts = strtotime($datetime);
		if ( ! $ts)
		{
			return '';
		}
		$diff = time() - $ts;
		if ($diff < 60)
		{
			return 'Just now';
		}
		if ($diff < 3600)
		{
			return floor($diff / 60) . ' minutes ago';
		}
		if ($diff < 86400)
		{
			return floor($diff / 3600) . ' hours ago';
		}
		if ($diff < 172800)
		{
			return 'Yesterday';
		}
		return date('M j, Y', $ts);
	}

	protected function minutes_ago_label($minutes)
	{
		if ($minutes < 60)
		{
			return $minutes . ' minutes ago';
		}
		if ($minutes < 1440)
		{
			return floor($minutes / 60) . ' hours ago';
		}
		return 'Yesterday';
	}
}
