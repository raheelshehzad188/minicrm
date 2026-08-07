<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organization_model extends MY_Model {

	protected $table = 'organizations';
	protected $tenant_scoped = FALSE;

	public function get_by_slug($slug)
	{
		return $this->db->get_where($this->table, array('slug' => $slug))->row();
	}

	public function get_active($id)
	{
		return $this->db->get_where($this->table, array(
			'id'     => (int) $id,
			'status' => 'active',
		))->row();
	}

	public function get_by_api_key($api_key)
	{
		if ( ! $api_key)
		{
			return NULL;
		}
		return $this->db->get_where($this->table, array('api_key' => $api_key))->row();
	}

	public function regenerate_api_key($id)
	{
		$key = bin2hex(random_bytes(32));
		$this->db->where('id', (int) $id);
		$this->db->update($this->table, array(
			'api_key'    => $key,
			'updated_at' => date('Y-m-d H:i:s'),
		));
		return $key;
	}

	public function update_settings($id, $data)
	{
		$allowed = array(
			'name', 'logo', 'email', 'phone', 'address', 'country',
			'timezone', 'currency', 'website', 'registration_number', 'tax_number', 'updated_at',
		);
		$payload = array_intersect_key($data, array_flip($allowed));
		$payload['updated_at'] = date('Y-m-d H:i:s');
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, $payload);
	}
}
