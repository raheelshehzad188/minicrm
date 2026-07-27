<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_module_model extends CI_Model {

	protected $table = 'permission_modules';

	public function all_active()
	{
		$this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}
}
