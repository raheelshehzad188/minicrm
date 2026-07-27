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
