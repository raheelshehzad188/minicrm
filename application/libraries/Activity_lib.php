<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Activity Log Library
 */
class Activity_lib {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Activity_log_model');
	}

	public function log($action, $description, $module = NULL, $record_id = NULL, $meta = NULL)
	{
		$data = array(
			'organization_id' => $this->CI->session->userdata('organization_id') ?: NULL,
			'user_id'         => $this->CI->session->userdata('user_id') ?: NULL,
			'action'          => $action,
			'module'          => $module,
			'record_id'       => $record_id,
			'description'     => $description,
			'ip_address'      => $this->CI->input->ip_address(),
			'user_agent'      => substr((string) $this->CI->input->user_agent(), 0, 255),
			'meta'            => $meta ? json_encode($meta) : NULL,
			'created_at'      => date('Y-m-d H:i:s'),
		);
		return $this->CI->Activity_log_model->insert_log($data);
	}
}
