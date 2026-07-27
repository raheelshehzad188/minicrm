<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Organization Library — multi-tenant context
 */
class Organization_lib {

	protected $CI;
	protected $organization = NULL;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Organization_model');
	}

	/**
	 * Boot current org from session
	 */
	public function boot()
	{
		$org_id = (int) $this->CI->session->userdata('organization_id');
		if ($org_id)
		{
			$this->organization = $this->CI->Organization_model->get($org_id);
		}
		return $this->organization;
	}

	public function id()
	{
		return (int) $this->CI->session->userdata('organization_id');
	}

	public function current()
	{
		if ($this->organization === NULL)
		{
			$this->boot();
		}
		return $this->organization;
	}

	public function name()
	{
		$org = $this->current();
		return $org ? $org->name : (string) $this->CI->session->userdata('org_name');
	}

	public function slug()
	{
		$org = $this->current();
		return $org ? $org->slug : (string) $this->CI->session->userdata('org_slug');
	}

	/**
	 * Scope a CI DB query builder to current organization
	 */
	public function scope($table_alias = NULL)
	{
		$col = $table_alias ? $table_alias . '.organization_id' : 'organization_id';
		$this->CI->db->where($col, $this->id());
		return $this->CI->db;
	}

	/**
	 * Ensure a row belongs to current organization
	 */
	public function owns($row, $column = 'organization_id')
	{
		if ( ! $row)
		{
			return FALSE;
		}
		if (is_array($row))
		{
			return isset($row[$column]) && (int) $row[$column] === $this->id();
		}
		return isset($row->{$column}) && (int) $row->{$column} === $this->id();
	}

	public function assert_owns($row, $column = 'organization_id')
	{
		if ( ! $this->owns($row, $column))
		{
			show_error('Cross-organization access denied.', 403);
		}
	}
}
