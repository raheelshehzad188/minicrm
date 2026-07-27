<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends MY_Model {

	protected $table = 'permissions';
	protected $tenant_scoped = FALSE;

	public function get_slugs_by_role($role_id)
	{
		$this->db->select('permissions.slug');
		$this->db->from('role_permissions');
		$this->db->join('permissions', 'permissions.id = role_permissions.permission_id');
		$this->db->where('role_permissions.role_id', (int) $role_id);
		$rows = $this->db->get()->result();

		$slugs = array();
		foreach ($rows as $row)
		{
			$slugs[] = $row->slug;
		}
		return $slugs;
	}

	public function get_ids_by_role($role_id)
	{
		$this->db->select('permission_id');
		$this->db->from('role_permissions');
		$this->db->where('role_id', (int) $role_id);
		$rows = $this->db->get()->result();
		$ids = array();
		foreach ($rows as $row)
		{
			$ids[] = (int) $row->permission_id;
		}
		return $ids;
	}

	public function all_grouped_by_module()
	{
		$this->db->order_by('module', 'ASC');
		$this->db->order_by('id', 'ASC');
		$rows = $this->db->get($this->table)->result();
		$grouped = array();
		foreach ($rows as $row)
		{
			$grouped[$row->module][] = $row;
		}
		return $grouped;
	}

	public function sync_role_permissions($role_id, array $permission_ids)
	{
		$this->db->where('role_id', (int) $role_id)->delete('role_permissions');
		$batch = array();
		foreach (array_unique($permission_ids) as $pid)
		{
			$pid = (int) $pid;
			if ($pid > 0)
			{
				$batch[] = array(
					'role_id'       => (int) $role_id,
					'permission_id' => $pid,
				);
			}
		}
		if ($batch)
		{
			$this->db->insert_batch('role_permissions', $batch);
		}
		return TRUE;
	}
}
