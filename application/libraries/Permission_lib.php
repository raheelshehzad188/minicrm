<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Permission Library — role-based access control
 * Owner always has full access.
 */
class Permission_lib {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function can($permission)
	{
		if ($this->is_owner())
		{
			return TRUE;
		}
		$perms = $this->CI->session->userdata('permissions');
		if ( ! is_array($perms))
		{
			return FALSE;
		}
		return in_array($permission, $perms, TRUE);
	}

	public function cannot($permission)
	{
		return ! $this->can($permission);
	}

	public function has_any(array $permissions)
	{
		if ($this->is_owner())
		{
			return TRUE;
		}
		foreach ($permissions as $p)
		{
			if ($this->can($p))
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	public function has_all(array $permissions)
	{
		if ($this->is_owner())
		{
			return TRUE;
		}
		foreach ($permissions as $p)
		{
			if ( ! $this->can($p))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	public function is_role($slug)
	{
		return $this->CI->session->userdata('role_slug') === $slug;
	}

	public function is_owner()
	{
		return $this->is_role('owner');
	}

	public function is_admin()
	{
		return $this->is_role('admin') || $this->is_owner();
	}

	public function require($permission)
	{
		if ( ! $this->can($permission))
		{
			show_error('Permission denied.', 403);
		}
	}

	public function all()
	{
		$perms = $this->CI->session->userdata('permissions');
		return is_array($perms) ? $perms : array();
	}

	/**
	 * Reload permissions into session (after role matrix update)
	 */
	public function refresh_session_permissions($role_id = NULL)
	{
		$this->CI->load->model('Permission_model');
		$rid = $role_id ?: (int) $this->CI->session->userdata('role_id');
		$slugs = $this->CI->Permission_model->get_slugs_by_role($rid);
		$this->CI->session->set_userdata('permissions', $slugs);
		return $slugs;
	}
}
