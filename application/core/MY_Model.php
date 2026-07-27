<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Model with multi-tenant organization scoping
 *
 * When $tenant_scoped = TRUE, all finds/updates automatically filter by
 * session organization_id unless explicitly bypassed.
 */
class MY_Model extends CI_Model {

	protected $table = '';
	protected $primary_key = 'id';
	protected $tenant_scoped = FALSE;
	protected $tenant_column = 'organization_id';
	protected $soft_bypass_tenant = FALSE;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Temporarily disable tenant scope (Owner/system use carefully)
	 */
	public function without_tenant()
	{
		$this->soft_bypass_tenant = TRUE;
		return $this;
	}

	protected function apply_tenant_scope()
	{
		if ($this->tenant_scoped && ! $this->soft_bypass_tenant)
		{
			$org_id = $this->session->userdata('organization_id');
			if ($org_id)
			{
				$this->db->where($this->table . '.' . $this->tenant_column, (int) $org_id);
			}
			else
			{
				// Fail closed — no org in session means no rows
				$this->db->where('1 =', 0, FALSE);
			}
		}
		$this->soft_bypass_tenant = FALSE;
	}

	public function get($id)
	{
		$this->apply_tenant_scope();
		return $this->db->get_where($this->table, array($this->primary_key => (int) $id))->row();
	}

	public function get_all($where = array(), $order_by = NULL, $limit = NULL, $offset = NULL)
	{
		$this->apply_tenant_scope();
		if ( ! empty($where))
		{
			$this->db->where($where);
		}
		if ($order_by)
		{
			$this->db->order_by($order_by);
		}
		if ($limit !== NULL)
		{
			$this->db->limit($limit, $offset);
		}
		return $this->db->get($this->table)->result();
	}

	public function insert($data)
	{
		if ($this->tenant_scoped && ! isset($data[$this->tenant_column]))
		{
			$data[$this->tenant_column] = (int) $this->session->userdata('organization_id');
		}
		if ( ! isset($data['created_at']))
		{
			$data['created_at'] = date('Y-m-d H:i:s');
		}
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($id, $data)
	{
		if ( ! isset($data['updated_at']))
		{
			$data['updated_at'] = date('Y-m-d H:i:s');
		}
		$this->apply_tenant_scope();
		$this->db->where($this->primary_key, (int) $id);
		return $this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->apply_tenant_scope();
		$this->db->where($this->primary_key, (int) $id);
		return $this->db->delete($this->table);
	}

	public function count($where = array())
	{
		$this->apply_tenant_scope();
		if ( ! empty($where))
		{
			$this->db->where($where);
		}
		return (int) $this->db->count_all_results($this->table);
	}
}
