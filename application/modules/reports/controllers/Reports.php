<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reports module — lead reports with lead type filtering
 */
class Reports extends Auth_Controller {

	protected $required_permission = 'reports.view';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Lead_lib');
		$this->load->model(array('Lead_model', 'Crm_lookup_model'));
	}

	public function index()
	{
		redirect('reports/leads');
	}

	public function leads()
	{
		$lookups = array(
			'lead_types' => $this->lead_lib->lead_types(),
			'statuses'   => $this->Crm_lookup_model->statuses(),
			'sources'    => $this->Crm_lookup_model->sources(),
		);

		$data = array(
			'page_title'    => 'Lead Reports',
			'page_subtitle' => 'Filter and export leads by type',
			'active_menu'   => 'reports',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Reports', 'url' => site_url('reports')),
				array('label' => 'Leads', 'url' => ''),
			),
			'page_actions'  => '',
			'content_view'  => 'leads',
			'content_data'  => array_merge($lookups, array(
				'data_url'   => site_url('reports/leads_data'),
				'export_url' => site_url('leads/export'),
				'can_export' => $this->permission_lib->can('reports.export') || $this->permission_lib->can('leads.export'),
			)),
		);
		$this->load->view('layouts/master', $data);
	}

	public function leads_data()
	{
		$this->permission_lib->require('reports.view');
		$lead_type = $this->lead_lib->normalize_lead_type($this->input->get('lead_type', TRUE));
		$filters = array(
			'lead_type'   => $lead_type ?: '',
			'status_id'   => $this->input->get('status_id', TRUE),
			'source_id'   => $this->input->get('source_id', TRUE),
			'date_from'   => $this->input->get('date_from', TRUE),
			'date_to'     => $this->input->get('date_to', TRUE),
			'search'      => $this->input->get('search', TRUE),
		);

		$rows = $this->Lead_model->datatable($filters);
		$data = array();
		foreach ($rows as $r)
		{
			$type = isset($r->lead_type) ? $r->lead_type : 'clinic';
			$data[] = array(
				'id'               => (int) $r->id,
				'title'            => $r->title,
				'lead_type'        => $type,
				'lead_type_label'  => $this->lead_lib->lead_type_label($type),
				'email'            => $r->email,
				'phone'            => $r->phone,
				'branch'           => isset($r->branch) ? $r->branch : NULL,
				'treatment'        => isset($r->treatment) ? $r->treatment : NULL,
				'course'           => isset($r->course) ? $r->course : NULL,
				'status_name'      => $r->status_name,
				'source_name'      => $r->source_name,
				'assignee_name'    => $r->assignee_name,
				'created_at'       => $r->created_at,
				'profile_url'      => site_url('leads/profile/' . (int) $r->id),
			);
		}

		$summary_filters = $filters;
		unset($summary_filters['lead_type']);
		$summary = array(
			'total'   => $this->Lead_model->count_by_type($lead_type ?: NULL, $filters),
			'clinic'  => $this->Lead_model->count_by_type('clinic', $summary_filters),
			'academy' => $this->Lead_model->count_by_type('academy', $summary_filters),
		);

		return $this->json_response(TRUE, 'OK', array(
			'rows'    => $data,
			'summary' => $summary,
		));
	}
}
