<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard widget registry
 * enabled: global on/off — future per-org settings can override
 */
$config['dashboard_widgets'] = array(
	'welcome'             => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'filters'             => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'kpis'                => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'charts'              => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'recent_activity'     => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'upcoming_followups'  => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'upcoming_tasks'      => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'upcoming_meetings'   => array('enabled' => TRUE,  'roles' => array('owner','admin','manager')),
	'birthdays'           => array('enabled' => TRUE,  'roles' => array('owner','admin','manager')),
	'quick_actions'       => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'table_leads'         => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'table_contacts'      => array('enabled' => TRUE,  'roles' => array('owner','admin','manager')),
	'table_deals'         => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
	'table_tasks'         => array('enabled' => TRUE,  'roles' => array('owner','admin','manager','sales_person')),
);

$config['dashboard_kpi_keys'] = array(
	'total_leads',
	'new_leads',
	'qualified_leads',
	'won_deals',
	'lost_deals',
	'total_contacts',
	'todays_followups',
	'pending_tasks',
	'completed_tasks',
	'active_users',
	'monthly_revenue',
	'conversion_rate',
);

$config['dashboard_chart_keys'] = array(
	'lead_status',
	'monthly_leads',
	'sales_pipeline',
	'lead_sources',
	'tasks_completion',
	'revenue_overview',
);

$config['dashboard_default_range'] = 'last_30_days';
