<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('User_model', 'Role_model'));
		$this->load->library(array('Activity_lib', 'Notification_lib', 'Mino_upload', 'Mino_mail', 'Auth_lib'));
	}

	public function index()
	{
		$this->permission_lib->require('users.view');

		$data = array(
			'page_title'    => 'Users',
			'page_subtitle' => 'Manage team members in your organization',
			'active_menu'   => 'users',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Users', 'url' => ''),
			),
			'page_actions'  => $this->permission_lib->can('users.create')
				? '<button type="button" class="btn btn-primary btn-sm" id="btnAddUser"><i class="fas fa-plus"></i> Add User</button>'
				: '',
			'content_view'  => 'index',
			'content_data'  => array(
				'roles'     => $this->Role_model->all_roles(),
				'can_create'=> $this->permission_lib->can('users.create'),
				'can_edit'  => $this->permission_lib->can('users.edit'),
				'can_delete'=> $this->permission_lib->can('users.delete'),
			),
		);
		$this->load->view('layouts/master', $data);
	}

	public function datatable()
	{
		$this->permission_lib->require('users.view');

		$filters = array(
			'search'  => $this->input->get('search', TRUE),
			'status'  => $this->input->get('status', TRUE),
			'role_id' => $this->input->get('role_id', TRUE),
		);
		$rows = $this->User_model->datatable($filters);
		$data = array();
		foreach ($rows as $u)
		{
			$data[] = array(
				'id'            => (int) $u->id,
				'name'          => $u->name,
				'email'         => $u->email,
				'phone'         => $u->phone,
				'role_id'       => (int) $u->role_id,
				'role_name'     => $u->role_name,
				'role_slug'     => $u->role_slug,
				'status'        => $u->status,
				'profile_image' => $u->profile_image ? base_url($u->profile_image) : '',
				'initials'      => user_initials($u->name),
				'last_login'    => $u->last_login,
				'created_at'    => $u->created_at,
				'is_self'       => ((int) $u->id === $this->auth_lib->user_id()),
			);
		}
		return $this->json_response(TRUE, 'OK', array('rows' => $data));
	}

	public function get($id = 0)
	{
		$this->permission_lib->require('users.view');
		$user = $this->User_model->get_with_role((int) $id);
		if ( ! $user)
		{
			return $this->json_response(FALSE, 'User not found.', array(), 404);
		}
		return $this->json_response(TRUE, 'OK', array('user' => $user));
	}

	public function store()
	{
		$this->permission_lib->require('users.create');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');
		$this->form_validation->set_rules('role_id', 'Role', 'required|integer');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,suspended]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$email = strtolower(trim($this->input->post('email', TRUE)));
		if ($this->User_model->email_exists($email))
		{
			return $this->json_response(FALSE, 'Email already exists.');
		}

		$role_id = (int) $this->input->post('role_id');
		if ( ! $this->_can_assign_role($role_id))
		{
			return $this->json_response(FALSE, 'You cannot assign this role.');
		}

		$password = $this->input->post('password', TRUE);
		$id = $this->User_model->insert(array(
			'organization_id' => $this->organization_lib->id(),
			'role_id'         => $role_id,
			'name'            => $this->input->post('name', TRUE),
			'email'           => $email,
			'phone'           => $this->input->post('phone', TRUE),
			'password'        => $this->auth_lib->hash_password($password),
			'status'          => $this->input->post('status', TRUE),
			'created_at'      => date('Y-m-d H:i:s'),
		));

		if ( ! $id)
		{
			return $this->json_response(FALSE, 'Failed to create user.');
		}

		$this->activity_lib->log('create', 'Created user ' . $email, 'users', $id);
		$this->notification_lib->notify_org_admins(
			'New user created',
			$this->input->post('name', TRUE) . ' was added to your organization.',
			'info',
			site_url('users')
		);

		$this->mino_mail->send_template($email, 'Welcome to Mino CRM', 'welcome', array(
			'name'     => $this->input->post('name', TRUE),
			'email'    => $email,
			'password' => $password,
			'org_name' => current_org_name(),
			'login_url'=> site_url('auth/login'),
		));

		return $this->json_response(TRUE, 'User created successfully.', array('id' => $id));
	}

	public function update($id = 0)
	{
		$this->permission_lib->require('users.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$user = $this->User_model->get((int) $id);
		if ( ! $user)
		{
			return $this->json_response(FALSE, 'User not found.', array(), 404);
		}

		$this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');
		$this->form_validation->set_rules('role_id', 'Role', 'required|integer');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,suspended]');
		$this->form_validation->set_rules('password', 'Password', 'trim|min_length[8]|max_length[255]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$email = strtolower(trim($this->input->post('email', TRUE)));
		if ($this->User_model->email_exists($email, (int) $id))
		{
			return $this->json_response(FALSE, 'Email already exists.');
		}

		$role_id = (int) $this->input->post('role_id');
		$role = $this->Role_model->get($user->role_id);
		$new_role = $this->Role_model->get($role_id);

		// Prevent demoting last owner
		if ($role && $role->slug === 'owner' && $new_role && $new_role->slug !== 'owner')
		{
			if ($this->User_model->count_owners_in_org() <= 1)
			{
				return $this->json_response(FALSE, 'Cannot change role of the last Owner.');
			}
		}

		if ( ! $this->_can_assign_role($role_id))
		{
			return $this->json_response(FALSE, 'You cannot assign this role.');
		}

		$payload = array(
			'name'    => $this->input->post('name', TRUE),
			'email'   => $email,
			'phone'   => $this->input->post('phone', TRUE),
			'role_id' => $role_id,
			'status'  => $this->input->post('status', TRUE),
		);
		$password = $this->input->post('password', TRUE);
		if ($password)
		{
			$payload['password'] = $this->auth_lib->hash_password($password);
		}

		$ok = $this->User_model->update((int) $id, $payload);
		if ($ok)
		{
			$this->activity_lib->log('update', 'Updated user ' . $email, 'users', (int) $id);
			if ((int) $id === $this->auth_lib->user_id())
			{
				$this->session->set_userdata('user_name', $payload['name']);
				$this->session->set_userdata('user_email', $payload['email']);
			}
		}

		return $this->json_response((bool) $ok, $ok ? 'User updated.' : 'Unable to update user.');
	}

	public function delete($id = 0)
	{
		$this->permission_lib->require('users.delete');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$id = (int) $id;
		if ($id === $this->auth_lib->user_id())
		{
			return $this->json_response(FALSE, 'You cannot delete your own account.');
		}

		$user = $this->User_model->get_with_role($id);
		if ( ! $user)
		{
			return $this->json_response(FALSE, 'User not found.', array(), 404);
		}

		if ($user->role_slug === 'owner' && $this->User_model->count_owners_in_org() <= 1)
		{
			return $this->json_response(FALSE, 'Cannot delete the last Owner.');
		}

		$ok = $this->User_model->soft_delete($id);
		if ($ok)
		{
			$this->activity_lib->log('delete', 'Soft-deleted user ' . $user->email, 'users', $id);
		}
		return $this->json_response((bool) $ok, $ok ? 'User deleted.' : 'Unable to delete.');
	}

	public function set_status($id = 0)
	{
		$this->permission_lib->require('users.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$id = (int) $id;
		$status = $this->input->post('status', TRUE);
		if ($id === $this->auth_lib->user_id() && $status !== 'active')
		{
			return $this->json_response(FALSE, 'You cannot suspend your own account.');
		}

		$user = $this->User_model->get_with_role($id);
		if ( ! $user)
		{
			return $this->json_response(FALSE, 'User not found.', array(), 404);
		}

		$ok = $this->User_model->set_status($id, $status);
		if ($ok)
		{
			$this->activity_lib->log('update', 'Changed status of ' . $user->email . ' to ' . $status, 'users', $id);
			if ($status === 'active')
			{
				$this->mino_mail->send_template($user->email, 'Account Activated — Mino CRM', 'account_activated', array(
					'name'     => $user->name,
					'org_name' => current_org_name(),
					'login_url'=> site_url('auth/login'),
				));
				$this->notification_lib->push($id, 'Account activated', 'Your account has been activated.', 'success', site_url('dashboard'));
			}
		}
		return $this->json_response((bool) $ok, $ok ? 'Status updated.' : 'Unable to update status.');
	}

	public function reset_password($id = 0)
	{
		$this->permission_lib->require('users.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$user = $this->User_model->get((int) $id);
		if ( ! $user)
		{
			return $this->json_response(FALSE, 'User not found.', array(), 404);
		}

		$temp = substr(bin2hex(random_bytes(8)), 0, 10) . 'A1!';
		$ok = $this->User_model->update_password($user->id, $this->auth_lib->hash_password($temp));
		if ($ok)
		{
			$this->activity_lib->log('password_change', 'Admin reset password for ' . $user->email, 'users', $user->id);
			$this->mino_mail->send_template($user->email, 'Your password was reset', 'password_reset_admin', array(
				'name'     => $user->name,
				'password' => $temp,
				'login_url'=> site_url('auth/login'),
			));
			$this->notification_lib->push($user->id, 'Password reset', 'An administrator reset your password.', 'warning', site_url('auth/password'));
		}

		$payload = array();
		if (ENVIRONMENT !== 'production')
		{
			$payload['temp_password'] = $temp;
		}
		return $this->json_response((bool) $ok, $ok ? 'Password reset. User will receive the new credentials.' : 'Failed.', $payload);
	}

	protected function _can_assign_role($role_id)
	{
		$role = $this->Role_model->get((int) $role_id);
		if ( ! $role)
		{
			return FALSE;
		}
		// Only Owner can assign Owner role
		if ($role->slug === 'owner' && ! $this->permission_lib->is_owner())
		{
			return FALSE;
		}
		return TRUE;
	}
}
