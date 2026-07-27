<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('Role_model', 'Permission_model', 'Permission_module_model'));
		$this->load->library(array('Activity_lib', 'Notification_lib'));
	}

	public function index()
	{
		$this->permission_lib->require('roles.view');

		$roles = $this->Role_model->with_user_counts($this->organization_lib->id());
		$data = array(
			'page_title'    => 'Roles & Permissions',
			'page_subtitle' => 'Control access across your organization',
			'active_menu'   => 'roles',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Roles', 'url' => ''),
			),
			'content_view'  => 'index',
			'content_data'  => array(
				'roles'    => $roles,
				'can_edit' => $this->permission_lib->can('roles.edit'),
			),
		);
		$this->load->view('layouts/master', $data);
	}

	public function permissions($role_id = 0)
	{
		$this->permission_lib->require('roles.view');

		$role = $this->Role_model->get((int) $role_id);
		if ( ! $role)
		{
			show_404();
		}

		$modules = $this->Permission_module_model->all_active();
		$grouped = $this->Permission_model->all_grouped_by_module();
		$assigned = $this->Permission_model->get_ids_by_role($role->id);

		// Build matrix actions per module
		$actions = array('view', 'create', 'edit', 'delete', 'export', 'import');
		$matrix = array();
		foreach ($modules as $mod)
		{
			$row = array(
				'module' => $mod,
				'perms'  => array(),
			);
			foreach ($actions as $action)
			{
				$slug = $mod->slug . '.' . $action;
				$perm = NULL;
				if (isset($grouped[$mod->slug]))
				{
					foreach ($grouped[$mod->slug] as $p)
					{
						if ($p->slug === $slug)
						{
							$perm = $p;
							break;
						}
					}
				}
				$row['perms'][$action] = $perm;
			}
			// Also collect profile-like extra permissions under module if any
			$matrix[] = $row;
		}

		// Profile permissions (not full CRUD matrix)
		$profile_perms = isset($grouped['profile']) ? $grouped['profile'] : array();

		$data = array(
			'page_title'    => 'Permission Matrix — ' . $role->name,
			'page_subtitle' => 'Toggle what this role can access',
			'active_menu'   => 'roles',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Roles', 'url' => site_url('roles')),
				array('label' => $role->name, 'url' => ''),
			),
			'page_actions'  => '<a href="' . site_url('roles') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>',
			'content_view'  => 'permissions',
			'content_data'  => array(
				'role'          => $role,
				'matrix'        => $matrix,
				'actions'       => $actions,
				'assigned'      => $assigned,
				'profile_perms' => $profile_perms,
				'can_edit'      => $this->permission_lib->can('roles.edit') && ! ($role->slug === 'owner' && ! $this->permission_lib->is_owner()),
			),
		);
		$this->load->view('layouts/master', $data);
	}

	public function save_permissions($role_id = 0)
	{
		$this->permission_lib->require('roles.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$role = $this->Role_model->get((int) $role_id);
		if ( ! $role)
		{
			return $this->json_response(FALSE, 'Role not found.', array(), 404);
		}

		// Only owner can edit owner permissions
		if ($role->slug === 'owner' && ! $this->permission_lib->is_owner())
		{
			return $this->json_response(FALSE, 'Only an Owner can modify Owner permissions.');
		}

		$ids = $this->input->post('permission_ids');
		if ( ! is_array($ids))
		{
			$ids = array();
		}
		$ids = array_map('intval', $ids);

		$this->Permission_model->sync_role_permissions($role->id, $ids);
		$this->activity_lib->log('update', 'Updated permissions for role ' . $role->name, 'roles', $role->id);

		// If current user's role was updated, refresh session
		if ((int) $role->id === (int) $this->auth_lib->role_id())
		{
			$this->permission_lib->refresh_session_permissions($role->id);
		}

		$this->notification_lib->push(
			$this->auth_lib->user_id(),
			'Permissions saved',
			'Permission matrix for ' . $role->name . ' was updated.',
			'success',
			site_url('roles/permissions/' . $role->id)
		);

		return $this->json_response(TRUE, 'Permissions saved successfully.');
	}
}
