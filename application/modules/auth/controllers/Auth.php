<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Module — login, logout, forgot/reset password, profile
 */
class Auth extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
		$this->config->load('auth', TRUE);
	}

	public function index()
	{
		redirect('auth/login');
	}

	/* -----------------------------------------------------------------
	 * Login
	 * ----------------------------------------------------------------- */

	public function login()
	{
		$this->auth_lib->attempt_remember_login();
		if ($this->auth_lib->logged_in())
		{
			$redir = $this->config->item('login_redirect', 'auth') ?: 'dashboard';
			redirect($redir);
		}

		$data = array(
			'page_title'   => 'Sign In',
			'content_view' => 'login',
		);
		$this->load->view('layouts/auth_master', $data);
	}

	public function do_login()
	{
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid request method.', array(), 405);
		}

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|max_length[150]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[255]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$email = $this->input->post('email', TRUE);
		$password = $this->input->post('password', TRUE);
		$remember = (bool) $this->input->post('remember');

		$result = $this->auth_lib->login($email, $password, $remember);

		if ( ! $result['success'])
		{
			return $this->json_response(FALSE, $result['message'], array(), 401);
		}

		$redirect = $this->session->userdata('redirect_after_login');
		$this->session->unset_userdata('redirect_after_login');
		if ( ! $redirect)
		{
			$redir = $this->config->item('login_redirect', 'auth') ?: 'dashboard';
			$redirect = site_url($redir);
		}

		return $this->json_response(TRUE, $result['message'], array('redirect' => $redirect));
	}

	/* -----------------------------------------------------------------
	 * Logout
	 * ----------------------------------------------------------------- */

	public function logout()
	{
		$this->auth_lib->logout();
		$redir = $this->config->item('logout_redirect', 'auth') ?: 'auth/login';
		redirect($redir);
	}

	/* -----------------------------------------------------------------
	 * Forgot / Reset password
	 * ----------------------------------------------------------------- */

	public function forgot()
	{
		if ($this->auth_lib->logged_in())
		{
			redirect('dashboard');
		}

		$data = array(
			'page_title'   => 'Forgot Password',
			'content_view' => 'forgot',
		);
		$this->load->view('layouts/auth_master', $data);
	}

	public function do_forgot()
	{
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid request method.', array(), 405);
		}

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|max_length[150]');
		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$result = $this->auth_lib->request_password_reset($this->input->post('email', TRUE));
		$data = array();
		if ( ! empty($result['token']))
		{
			$data['reset_url'] = site_url('auth/reset/' . $result['token']);
			$data['dev_hint'] = 'Dev only: reset link included because ENVIRONMENT !== production';
		}

		return $this->json_response(TRUE, $result['message'], $data);
	}

	public function reset($token = '')
	{
		if ($this->auth_lib->logged_in())
		{
			redirect('dashboard');
		}

		$token = trim($token);
		$this->load->model('Password_reset_model');
		$row = $this->Password_reset_model->find_valid($token);

		$data = array(
			'page_title'   => 'Reset Password',
			'content_view' => 'reset',
			'content_data' => array(
				'token'   => $token,
				'valid'   => (bool) $row,
			),
		);
		$this->load->view('layouts/auth_master', $data);
	}

	public function do_reset()
	{
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid request method.', array(), 405);
		}

		$cfg = $this->config->item('auth');
		$min_len = 8;
		if (is_array($cfg) && isset($cfg['password_min_length']))
		{
			$min_len = (int) $cfg['password_min_length'];
		}
		elseif ($this->config->item('password_min_length', 'auth'))
		{
			$min_len = (int) $this->config->item('password_min_length', 'auth');
		}

		$this->form_validation->set_rules('token', 'Token', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $min_len . ']|max_length[255]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$result = $this->auth_lib->reset_password(
			$this->input->post('token', TRUE),
			$this->input->post('password', TRUE)
		);

		if ( ! $result['success'])
		{
			return $this->json_response(FALSE, $result['message']);
		}

		return $this->json_response(TRUE, $result['message'], array(
			'redirect' => site_url('auth/login'),
		));
	}

	/* -----------------------------------------------------------------
	 * Profile / Change password (authenticated)
	 * ----------------------------------------------------------------- */

	public function profile()
	{
		$this->_require_login();
		$this->permission_lib->require('profile.manage');

		$user = $this->auth_lib->current_user();

		$data = array(
			'page_title'    => 'My Profile',
			'page_subtitle' => 'Manage your account details',
			'active_menu'   => 'profile',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'My Profile', 'url' => ''),
			),
			'content_view'  => 'profile',
			'content_data'  => array('user' => $user),
		);
		$this->load->view('layouts/master', $data);
	}

	public function update_profile()
	{
		$this->_require_login();
		$this->permission_lib->require('profile.manage');

		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid request method.', array(), 405);
		}

		$this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$this->load->model('User_model');
		$ok = $this->User_model->update_profile($this->auth_lib->user_id(), array(
			'name'  => $this->input->post('name', TRUE),
			'phone' => $this->input->post('phone', TRUE),
		));

		if ($ok)
		{
			$this->session->set_userdata('user_name', $this->input->post('name', TRUE));
			$this->load->library(array('Activity_lib', 'Notification_lib'));
			$this->activity_lib->log('profile_update', 'Updated profile details', 'profile', $this->auth_lib->user_id());
			$this->notification_lib->push(
				$this->auth_lib->user_id(),
				'Profile updated',
				'Your profile details were saved.',
				'success',
				site_url('auth/profile')
			);
		}

		return $this->json_response((bool) $ok, $ok ? 'Profile updated.' : 'Unable to update profile.');
	}

	public function upload_avatar()
	{
		$this->_require_login();
		$this->permission_lib->require('profile.manage');

		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid request method.', array(), 405);
		}

		if (empty($_FILES['avatar']['name']))
		{
			return $this->json_response(FALSE, 'Please choose an image.');
		}

		$this->load->library(array('Mino_upload', 'Activity_lib', 'Notification_lib'));
		$this->load->model('User_model');

		$result = $this->mino_upload->image('avatar', 'profiles');
		if ( ! $result['success'])
		{
			return $this->json_response(FALSE, $result['message']);
		}

		$user = $this->auth_lib->current_user();
		if ($user && ! empty($user->profile_image))
		{
			$this->mino_upload->delete_file($user->profile_image);
		}

		$ok = $this->User_model->update_profile($this->auth_lib->user_id(), array(
			'profile_image' => $result['path'],
		));

		if ($ok)
		{
			$this->session->set_userdata('profile_image', $result['path']);
			$this->activity_lib->log('profile_update', 'Uploaded profile avatar', 'profile', $this->auth_lib->user_id());
			$this->notification_lib->push(
				$this->auth_lib->user_id(),
				'Avatar updated',
				'Your profile photo was updated.',
				'success',
				site_url('auth/profile')
			);
		}

		return $this->json_response((bool) $ok, $ok ? 'Avatar uploaded.' : 'Unable to save avatar.', array(
			'avatar_url' => base_url($result['path']),
		));
	}

	public function password()
	{
		$this->_require_login();
		$this->permission_lib->require('profile.password');

		$data = array(
			'page_title'    => 'Change Password',
			'page_subtitle' => 'Update your account password',
			'active_menu'   => 'password',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Change Password', 'url' => ''),
			),
			'content_view'  => 'change_password',
		);
		$this->load->view('layouts/master', $data);
	}

	public function do_change_password()
	{
		$this->_require_login();
		$this->permission_lib->require('profile.password');

		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid request method.', array(), 405);
		}

		$this->form_validation->set_rules('current_password', 'Current Password', 'required');
		$this->form_validation->set_rules('password', 'New Password', 'required|min_length[8]|max_length[255]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$result = $this->auth_lib->change_password(
			$this->auth_lib->user_id(),
			$this->input->post('current_password', TRUE),
			$this->input->post('password', TRUE)
		);

		if ($result['success'])
		{
			$this->load->library(array('Activity_lib', 'Notification_lib'));
			$this->activity_lib->log('password_change', 'Changed account password', 'profile', $this->auth_lib->user_id());
			$this->notification_lib->push(
				$this->auth_lib->user_id(),
				'Password changed',
				'Your password was changed successfully.',
				'success',
				site_url('auth/password')
			);
		}

		return $this->json_response($result['success'], $result['message']);
	}

	protected function _require_login()
	{
		$this->auth_lib->attempt_remember_login();
		if ( ! $this->auth_lib->logged_in())
		{
			if ($this->is_ajax())
			{
				$this->json_response(FALSE, 'Unauthenticated.', array(), 401);
				$this->output->_display();
				exit;
			}
			redirect('auth/login');
		}
		$this->organization_lib->boot();
	}
}
