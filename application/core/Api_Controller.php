<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API base controller — JSON + API key auth (no session login)
 */
class Api_Controller extends MY_Controller {

	/** @var object|null Authenticated organization */
	protected $api_org = NULL;

	/** @var int|null webhook_logs.id for this request */
	protected $webhook_log_id = NULL;

	/** @var string */
	protected $webhook_endpoint = '';

	/** @var string */
	protected $raw_body = '';

	/** @var array */
	protected $json_body = array();

	/** @var string */
	protected $request_time = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('Organization_model', 'Webhook_log_model'));
		$this->load->library(array('Lead_lib', 'Activity_lib'));
		$this->config->load('leads', TRUE);

		$this->request_time = date('Y-m-d H:i:s');
		$this->raw_body = file_get_contents('php://input');
		$decoded = json_decode($this->raw_body, TRUE);
		$this->json_body = is_array($decoded) ? $decoded : array();

		$this->webhook_endpoint = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'api';
		$this->webhook_log_id = $this->Webhook_log_model->log_request(array(
			'endpoint'        => $this->webhook_endpoint,
			'method'          => $this->input->method(TRUE),
			'request_payload' => $this->raw_body !== '' ? $this->raw_body : json_encode($this->input->post(NULL, TRUE)),
			'ip_address'      => $this->input->ip_address(),
			'user_agent'      => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL,
			'request_time'    => $this->request_time,
		));
	}

	/**
	 * Authenticate via X-API-Key or Authorization: Bearer
	 */
	protected function require_api_key()
	{
		$key = $this->input->get_request_header('X-API-Key', TRUE);
		if ( ! $key)
		{
			$auth = $this->input->get_request_header('Authorization', TRUE);
			if ($auth && preg_match('/Bearer\s+(.+)/i', $auth, $m))
			{
				$key = trim($m[1]);
			}
		}
		if ( ! $key)
		{
			$key = $this->input->get_post('api_key', TRUE);
		}

		if ( ! $key)
		{
			return $this->api_error('API key required. Send X-API-Key header.', 401);
		}

		$org = $this->Organization_model->get_by_api_key($key);
		if ( ! $org || $org->status !== 'active')
		{
			return $this->api_error('Invalid or inactive API key.', 401);
		}

		$this->api_org = $org;
		$this->session->set_userdata(array(
			'organization_id' => (int) $org->id,
			'org_name'        => $org->name,
			'org_slug'        => $org->slug,
		));
		$this->organization_lib->boot();

		if ($this->webhook_log_id)
		{
			$this->Webhook_log_model->update_log($this->webhook_log_id, array(
				'organization_id' => (int) $org->id,
			));
		}

		return TRUE;
	}

	protected function api_success($message, $data = array(), $http_code = 200)
	{
		$payload = array(
			'success' => TRUE,
			'message' => $message,
			'data'    => $data,
		);
		$this->finalize_webhook_log($payload, $http_code, NULL);
		$this->output
			->set_status_header($http_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload));
		return TRUE;
	}

	protected function api_error($message, $http_code = 400, $data = array())
	{
		$payload = array(
			'success' => FALSE,
			'message' => $message,
			'data'    => $data,
			'errors'  => isset($data['errors']) ? $data['errors'] : array(),
		);
		$this->finalize_webhook_log($payload, $http_code, $message);
		log_message('error', 'API [' . $this->webhook_endpoint . ']: ' . $message);
		$this->output
			->set_status_header($http_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($payload));
		return FALSE;
	}

	protected function finalize_webhook_log($payload, $http_code, $error_message = NULL)
	{
		if ( ! $this->webhook_log_id)
		{
			return;
		}
		$this->Webhook_log_model->update_log($this->webhook_log_id, array(
			'response_payload' => json_encode($payload),
			'response_code'    => (int) $http_code,
			'error_message'    => $error_message,
			'organization_id'  => $this->api_org ? (int) $this->api_org->id : NULL,
		));
	}

	protected function json_input($key = NULL, $default = NULL)
	{
		if ($key === NULL)
		{
			return $this->json_body;
		}
		return array_key_exists($key, $this->json_body) ? $this->json_body[$key] : $default;
	}
}
