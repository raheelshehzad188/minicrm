<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook_log_model extends MY_Model {

	protected $table = 'webhook_logs';
	protected $tenant_scoped = FALSE;

	public function log_request(array $data)
	{
		$row = array(
			'organization_id'  => isset($data['organization_id']) ? (int) $data['organization_id'] : NULL,
			'endpoint'         => isset($data['endpoint']) ? substr($data['endpoint'], 0, 255) : '',
			'method'           => isset($data['method']) ? substr(strtoupper($data['method']), 0, 10) : 'POST',
			'request_payload'  => isset($data['request_payload']) ? $data['request_payload'] : NULL,
			'response_payload' => isset($data['response_payload']) ? $data['response_payload'] : NULL,
			'response_code'    => isset($data['response_code']) ? (int) $data['response_code'] : NULL,
			'ip_address'       => isset($data['ip_address']) ? substr($data['ip_address'], 0, 45) : NULL,
			'user_agent'       => isset($data['user_agent']) ? substr($data['user_agent'], 0, 500) : NULL,
			'error_message'    => isset($data['error_message']) ? $data['error_message'] : NULL,
			'request_time'     => isset($data['request_time']) ? $data['request_time'] : date('Y-m-d H:i:s'),
			'created_at'       => date('Y-m-d H:i:s'),
		);
		$this->db->insert($this->table, $row);
		return (int) $this->db->insert_id();
	}

	public function update_log($id, array $data)
	{
		$allowed = array('response_payload', 'response_code', 'error_message', 'organization_id');
		$payload = array_intersect_key($data, array_flip($allowed));
		if (empty($payload))
		{
			return FALSE;
		}
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, $payload);
	}
}
