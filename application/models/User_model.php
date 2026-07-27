<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

	protected $table = 'users';
	protected $tenant_scoped = TRUE;
	protected $include_deleted = FALSE;

	public function with_trashed()
	{
		$this->include_deleted = TRUE;
		return $this;
	}

	protected function apply_soft_delete_scope()
	{
		if ( ! $this->include_deleted)
		{
			$this->db->where($this->table . '.deleted_at IS NULL', NULL, FALSE);
		}
		$this->include_deleted = FALSE;
	}

	public function find_by_email($email)
	{
		$this->db->select('users.*, roles.slug AS role_slug, roles.name AS role_name, organizations.status AS org_status, organizations.name AS org_name, organizations.slug AS org_slug');
		$this->db->from($this->table);
		$this->db->join('roles', 'roles.id = users.role_id', 'left');
		$this->db->join('organizations', 'organizations.id = users.organization_id', 'left');
		$this->db->where('users.email', $email);
		$this->db->where('users.deleted_at IS NULL', NULL, FALSE);
		return $this->db->get()->row();
	}

	public function get_with_role($id)
	{
		$this->apply_tenant_scope();
		$this->apply_soft_delete_scope();
		$this->db->select('users.*, roles.slug AS role_slug, roles.name AS role_name');
		$this->db->from($this->table);
		$this->db->join('roles', 'roles.id = users.role_id', 'left');
		$this->db->where('users.id', (int) $id);
		return $this->db->get()->row();
	}

	public function get($id)
	{
		$this->apply_tenant_scope();
		$this->apply_soft_delete_scope();
		return $this->db->get_where($this->table, array($this->primary_key => (int) $id))->row();
	}

	public function update_last_login($id)
	{
		return $this->db->where('id', (int) $id)->update($this->table, array(
			'last_login' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	public function update_password($id, $hash)
	{
		return $this->db->where('id', (int) $id)->update($this->table, array(
			'password'   => $hash,
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	public function update_profile($id, $data)
	{
		$allowed = array('name', 'phone', 'profile_image', 'updated_at');
		$payload = array_intersect_key($data, array_flip($allowed));
		$payload['updated_at'] = date('Y-m-d H:i:s');
		$this->apply_tenant_scope();
		$this->apply_soft_delete_scope();
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, $payload);
	}

	/**
	 * Datatable listing for current org
	 */
	public function datatable($filters = array())
	{
		$this->apply_tenant_scope();
		$this->apply_soft_delete_scope();

		$this->db->select('users.id, users.name, users.email, users.phone, users.status, users.profile_image, users.last_login, users.created_at, users.role_id, roles.name AS role_name, roles.slug AS role_slug');
		$this->db->from($this->table);
		$this->db->join('roles', 'roles.id = users.role_id', 'left');

		if ( ! empty($filters['status']))
		{
			$this->db->where('users.status', $filters['status']);
		}
		if ( ! empty($filters['role_id']))
		{
			$this->db->where('users.role_id', (int) $filters['role_id']);
		}
		if ( ! empty($filters['search']))
		{
			$s = $this->db->escape_like_str($filters['search']);
			$this->db->group_start();
			$this->db->like('users.name', $s);
			$this->db->or_like('users.email', $s);
			$this->db->or_like('users.phone', $s);
			$this->db->group_end();
		}

		$this->db->order_by('users.id', 'DESC');
		return $this->db->get()->result();
	}

	public function email_exists($email, $exclude_id = NULL)
	{
		$this->db->where('email', $email);
		$this->db->where('deleted_at IS NULL', NULL, FALSE);
		if ($exclude_id)
		{
			$this->db->where('id !=', (int) $exclude_id);
		}
		return $this->db->count_all_results($this->table) > 0;
	}

	public function soft_delete($id)
	{
		$this->apply_tenant_scope();
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, array(
			'deleted_at' => date('Y-m-d H:i:s'),
			'status'     => 'inactive',
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	public function set_status($id, $status)
	{
		$allowed = array('active', 'inactive', 'suspended');
		if ( ! in_array($status, $allowed, TRUE))
		{
			return FALSE;
		}
		$this->apply_tenant_scope();
		$this->apply_soft_delete_scope();
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, array(
			'status'     => $status,
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	public function get_org_admins()
	{
		$this->apply_tenant_scope();
		$this->apply_soft_delete_scope();
		$this->db->select('users.*');
		$this->db->from($this->table);
		$this->db->join('roles', 'roles.id = users.role_id');
		$this->db->where_in('roles.slug', array('owner', 'admin'));
		$this->db->where('users.status', 'active');
		return $this->db->get()->result();
	}

	public function count_owners_in_org($org_id = NULL)
	{
		$org = $org_id ?: (int) $this->session->userdata('organization_id');
		$this->db->from($this->table);
		$this->db->join('roles', 'roles.id = users.role_id');
		$this->db->where('users.organization_id', (int) $org);
		$this->db->where('roles.slug', 'owner');
		$this->db->where('users.deleted_at IS NULL', NULL, FALSE);
		return (int) $this->db->count_all_results();
	}
}
