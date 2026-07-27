<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends MY_Model {

	protected $table = 'roles';
	protected $tenant_scoped = FALSE;

	public function get_by_slug($slug)
	{
		return $this->db->get_where($this->table, array('slug' => $slug))->row();
	}

	public function all_roles()
	{
		$this->db->order_by('id', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function with_user_counts($org_id)
	{
		$this->db->select('roles.*, COUNT(users.id) AS user_count');
		$this->db->from($this->table);
		$this->db->join('users', 'users.role_id = roles.id AND users.organization_id = ' . (int) $org_id . ' AND users.deleted_at IS NULL', 'left', FALSE);
		$this->db->group_by('roles.id');
		$this->db->order_by('roles.id', 'ASC');
		return $this->db->get()->result();
	}
}
