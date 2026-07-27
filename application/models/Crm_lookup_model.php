<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Org-scoped CRM lookup readers used by Leads
 */
class Crm_lookup_model extends MY_Model {

	protected $tenant_scoped = TRUE;

	public function statuses($active_only = TRUE)
	{
		$this->table = 'lead_statuses';
		$this->apply_tenant_scope();
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function sources($active_only = TRUE)
	{
		$this->table = 'lead_sources';
		$this->apply_tenant_scope();
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function tags($active_only = TRUE)
	{
		$this->table = 'lead_tags';
		$this->apply_tenant_scope();
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('name', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function pipelines($active_only = TRUE)
	{
		$this->table = 'pipelines';
		$this->apply_tenant_scope();
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function stages($pipeline_id = NULL, $active_only = TRUE)
	{
		$this->table = 'deal_stages';
		$this->apply_tenant_scope();
		if ($pipeline_id) $this->db->where('pipeline_id', (int) $pipeline_id);
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function priorities($active_only = TRUE)
	{
		$this->table = 'task_priorities';
		$this->apply_tenant_scope();
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function custom_fields($module = 'leads', $active_only = TRUE)
	{
		$this->table = 'custom_fields';
		$this->apply_tenant_scope();
		$this->db->where('module', $module);
		if ($active_only) $this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		return $this->db->get($this->table)->result();
	}

	public function default_status_id()
	{
		$this->table = 'lead_statuses';
		$this->apply_tenant_scope();
		$this->db->where('is_default', 1);
		$this->db->where('is_active', 1);
		$row = $this->db->get($this->table)->row();
		if ($row) return (int) $row->id;
		$this->apply_tenant_scope();
		$this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		$row = $this->db->get($this->table)->row();
		return $row ? (int) $row->id : NULL;
	}

	public function default_pipeline()
	{
		$this->table = 'pipelines';
		$this->apply_tenant_scope();
		$this->db->where('is_default', 1);
		$this->db->where('is_active', 1);
		$row = $this->db->get($this->table)->row();
		if ( ! $row)
		{
			$this->apply_tenant_scope();
			$this->db->where('is_active', 1);
			$this->db->order_by('sort_order', 'ASC');
			$row = $this->db->get($this->table)->row();
		}
		return $row;
	}

	public function first_stage_id($pipeline_id)
	{
		$this->table = 'deal_stages';
		$this->apply_tenant_scope();
		$this->db->where('pipeline_id', (int) $pipeline_id);
		$this->db->where('is_active', 1);
		$this->db->order_by('sort_order', 'ASC');
		$row = $this->db->get($this->table)->row();
		return $row ? (int) $row->id : NULL;
	}

	public function status_by_name($name)
	{
		$this->table = 'lead_statuses';
		$this->apply_tenant_scope();
		$this->db->group_start();
		$this->db->where('name', $name);
		$this->db->or_where('slug', strtolower(str_replace(' ', '_', $name)));
		$this->db->group_end();
		return $this->db->get($this->table)->row();
	}

	public function source_by_name($name)
	{
		$this->table = 'lead_sources';
		$this->apply_tenant_scope();
		$this->db->group_start();
		$this->db->where('name', $name);
		$this->db->or_where('slug', strtolower(str_replace(' ', '_', $name)));
		$this->db->group_end();
		return $this->db->get($this->table)->row();
	}

	public function assignable_users()
	{
		$this->db->select('users.id, users.name, users.email, users.profile_image, roles.name AS role_name');
		$this->db->from('users');
		$this->db->join('roles', 'roles.id = users.role_id', 'left');
		$this->db->where('users.organization_id', (int) current_org_id());
		$this->db->where('users.deleted_at IS NULL', NULL, FALSE);
		$this->db->where('users.status', 'active');
		$this->db->order_by('users.name', 'ASC');
		return $this->db->get()->result();
	}
}
