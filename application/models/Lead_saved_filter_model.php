<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_saved_filter_model extends MY_Model {

	protected $table = 'lead_saved_filters';
	protected $tenant_scoped = TRUE;

	public function for_user($user_id)
	{
		$this->apply_tenant_scope();
		$this->db->group_start();
		$this->db->where('user_id', (int) $user_id);
		$this->db->or_where('is_shared', 1);
		$this->db->group_end();
		$this->db->order_by('name', 'ASC');
		return $this->db->get($this->table)->result();
	}
}
