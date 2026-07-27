<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Library — login, logout, remember me, password reset, session
 */
class Auth_lib {

	protected $CI;
	protected $cfg;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model(array(
			'User_model',
			'Login_attempt_model',
			'Password_reset_model',
			'Remember_token_model',
			'Organization_model',
			'Permission_model',
		));
		$this->CI->config->load('auth', TRUE);
		$this->cfg = $this->CI->config->item('auth');
		if ( ! is_array($this->cfg))
		{
			// Flatten from section if needed
			$this->cfg = array(
				'max_login_attempts'      => $this->CI->config->item('max_login_attempts', 'auth'),
				'lockout_minutes'         => $this->CI->config->item('lockout_minutes', 'auth'),
				'attempt_window_minutes'  => $this->CI->config->item('attempt_window_minutes', 'auth'),
				'remember_days'           => $this->CI->config->item('remember_days', 'auth'),
				'remember_cookie'         => $this->CI->config->item('remember_cookie', 'auth'),
				'reset_token_hours'       => $this->CI->config->item('reset_token_hours', 'auth'),
				'password_min_length'     => $this->CI->config->item('password_min_length', 'auth'),
				'login_redirect'          => $this->CI->config->item('login_redirect', 'auth'),
				'logout_redirect'         => $this->CI->config->item('logout_redirect', 'auth'),
			);
		}
	}

	public function logged_in()
	{
		return (bool) $this->CI->session->userdata('user_id');
	}

	public function user_id()
	{
		return (int) $this->CI->session->userdata('user_id');
	}

	public function organization_id()
	{
		return (int) $this->CI->session->userdata('organization_id');
	}

	public function role_id()
	{
		return (int) $this->CI->session->userdata('role_id');
	}

	public function user_name()
	{
		return (string) $this->CI->session->userdata('user_name');
	}

	/**
	 * Attempt login with brute-force and status checks
	 *
	 * @return array {success, message, user?}
	 */
	public function login($email, $password, $remember = FALSE)
	{
		$email = strtolower(trim($email));
		$ip = $this->CI->input->ip_address();

		$max = (int) (isset($this->cfg['max_login_attempts']) ? $this->cfg['max_login_attempts'] : 5);
		$window = (int) (isset($this->cfg['attempt_window_minutes']) ? $this->cfg['attempt_window_minutes'] : 15);
		$lockout = (int) (isset($this->cfg['lockout_minutes']) ? $this->cfg['lockout_minutes'] : 15);

		$failures = $this->CI->Login_attempt_model->count_recent_failures($email, $ip, $window);
		if ($failures >= $max)
		{
			return array(
				'success' => FALSE,
				'message' => 'Too many failed attempts. Please try again in ' . $lockout . ' minutes.',
			);
		}

		$user = $this->CI->User_model->find_by_email($email);
		if ( ! $user || ! password_verify($password, $user->password))
		{
			$this->CI->Login_attempt_model->record($email, $ip, FALSE);
			$remaining = max(0, $max - $failures - 1);
			$msg = 'Invalid email or password.';
			if ($remaining <= 2)
			{
				$msg .= ' ' . $remaining . ' attempt(s) remaining.';
			}
			return array('success' => FALSE, 'message' => $msg);
		}

		if ($user->status !== 'active')
		{
			$this->CI->Login_attempt_model->record($email, $ip, FALSE);
			return array('success' => FALSE, 'message' => 'Your account is ' . $user->status . '. Contact your administrator.');
		}

		if (empty($user->org_status) || $user->org_status !== 'active')
		{
			$this->CI->Login_attempt_model->record($email, $ip, FALSE);
			return array('success' => FALSE, 'message' => 'Your organization is not active.');
		}

		$this->CI->Login_attempt_model->record($email, $ip, TRUE);
		$this->CI->Login_attempt_model->clear_failures($email, $ip);

		$this->establish_session($user);

		if ($remember)
		{
			$this->set_remember_cookie($user->id);
		}

		$this->CI->User_model->update_last_login($user->id);

		$this->CI->load->library('Activity_lib');
		$this->CI->activity_lib->log('login', 'User logged in: ' . $user->email, 'auth', $user->id);

		return array('success' => TRUE, 'message' => 'Login successful.', 'user' => $user);
	}

	public function establish_session($user)
	{
		$this->CI->session->sess_regenerate(TRUE);

		$permissions = $this->CI->Permission_model->get_slugs_by_role($user->role_id);

		$this->CI->session->set_userdata(array(
			'user_id'          => (int) $user->id,
			'organization_id'  => (int) $user->organization_id,
			'role_id'          => (int) $user->role_id,
			'user_name'        => $user->name,
			'user_email'       => $user->email,
			'role_slug'        => isset($user->role_slug) ? $user->role_slug : '',
			'role_name'        => isset($user->role_name) ? $user->role_name : '',
			'org_name'         => isset($user->org_name) ? $user->org_name : '',
			'org_slug'         => isset($user->org_slug) ? $user->org_slug : '',
			'profile_image'    => isset($user->profile_image) ? $user->profile_image : '',
			'permissions'      => $permissions,
			'logged_in_at'     => time(),
		));
	}

	public function logout()
	{
		$cookie = isset($this->cfg['remember_cookie']) ? $this->cfg['remember_cookie'] : 'mino_remember';
		$raw = $this->CI->input->cookie($cookie, TRUE);
		if ($raw)
		{
			$parts = explode(':', $raw, 2);
			if ( ! empty($parts[0]))
			{
				$this->CI->Remember_token_model->delete_by_selector($parts[0]);
			}
			delete_cookie($cookie);
		}

		if ($uid = $this->user_id())
		{
			$this->CI->load->library('Activity_lib');
			$this->CI->activity_lib->log('logout', 'User logged out', 'auth', $uid);
			$this->CI->Remember_token_model->delete_by_user($uid);
		}

		$this->CI->session->sess_destroy();
	}

	public function set_remember_cookie($user_id)
	{
		$days = (int) (isset($this->cfg['remember_days']) ? $this->cfg['remember_days'] : 30);
		$cookie = isset($this->cfg['remember_cookie']) ? $this->cfg['remember_cookie'] : 'mino_remember';
		$value = $this->CI->Remember_token_model->create($user_id, $days);

		$params = array(
			'name'     => $cookie,
			'value'    => $value,
			'expire'   => $days * 86400,
			'path'     => '/',
			'secure'   => FALSE,
			'httponly' => TRUE,
			'samesite' => 'Lax',
		);
		$this->CI->input->set_cookie($params);
	}

	public function attempt_remember_login()
	{
		if ($this->logged_in())
		{
			return TRUE;
		}

		$cookie = isset($this->cfg['remember_cookie']) ? $this->cfg['remember_cookie'] : 'mino_remember';
		$raw = $this->CI->input->cookie($cookie, TRUE);
		if ( ! $raw || strpos($raw, ':') === FALSE)
		{
			return FALSE;
		}

		list($selector, $validator) = explode(':', $raw, 2);
		$row = $this->CI->Remember_token_model->find_valid_by_selector($selector);
		if ( ! $row || ! hash_equals($row->token_hash, hash('sha256', $validator)))
		{
			delete_cookie($cookie);
			return FALSE;
		}

		$user = $this->CI->User_model->without_tenant()->get($row->user_id);
		// get via find with joins for session
		$user = $this->CI->User_model->find_by_email($user ? $user->email : '');
		if ( ! $user || $user->status !== 'active' || $user->org_status !== 'active')
		{
			$this->CI->Remember_token_model->delete_by_selector($selector);
			delete_cookie($cookie);
			return FALSE;
		}

		// Rotate remember token
		$this->CI->Remember_token_model->delete_by_selector($selector);
		$this->set_remember_cookie($user->id);
		$this->establish_session($user);
		$this->CI->User_model->update_last_login($user->id);

		return TRUE;
	}

	public function hash_password($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	public function verify_password($password, $hash)
	{
		return password_verify($password, $hash);
	}

	/**
	 * Request password reset — always returns success message (no email enumeration)
	 * Returns token for local/dev logging when APPPATH/logs used
	 */
	public function request_password_reset($email)
	{
		$email = strtolower(trim($email));
		$user = $this->CI->User_model->find_by_email($email);
		$token = NULL;

		if ($user && $user->status === 'active')
		{
			$this->CI->Password_reset_model->invalidate_email($email);
			$hours = (int) (isset($this->cfg['reset_token_hours']) ? $this->cfg['reset_token_hours'] : 1);
			$token = $this->CI->Password_reset_model->create_token($email, $hours);
			log_message('info', 'Password reset token for ' . $email . ': ' . $token);

			$this->CI->load->library('Mino_mail');
			$this->CI->mino_mail->send_template($email, 'Reset your Mino CRM password', 'password_reset', array(
				'reset_url' => site_url('auth/reset/' . $token),
			));
		}

		return array(
			'success' => TRUE,
			'message' => 'If that email exists, a reset link has been generated.',
			'token'   => (ENVIRONMENT !== 'production') ? $token : NULL,
		);
	}

	public function reset_password($token, $password)
	{
		$row = $this->CI->Password_reset_model->find_valid($token);
		if ( ! $row)
		{
			return array('success' => FALSE, 'message' => 'Invalid or expired reset link.');
		}

		$user = $this->CI->User_model->find_by_email($row->email);
		if ( ! $user)
		{
			return array('success' => FALSE, 'message' => 'User not found.');
		}

		$this->CI->User_model->update_password($user->id, $this->hash_password($password));
		$this->CI->Password_reset_model->mark_used($row->id);
		$this->CI->Remember_token_model->delete_by_user($user->id);

		return array('success' => TRUE, 'message' => 'Password updated. You can sign in now.');
	}

	public function change_password($user_id, $current, $new)
	{
		$user = $this->CI->User_model->without_tenant()->get($user_id);
		if ( ! $user)
		{
			return array('success' => FALSE, 'message' => 'User not found.');
		}
		// Tenant check
		if ((int) $user->organization_id !== $this->organization_id())
		{
			return array('success' => FALSE, 'message' => 'Unauthorized.');
		}
		if ( ! password_verify($current, $user->password))
		{
			return array('success' => FALSE, 'message' => 'Current password is incorrect.');
		}

		$this->CI->User_model->update_password($user_id, $this->hash_password($new));
		$this->CI->Remember_token_model->delete_by_user($user_id);

		return array('success' => TRUE, 'message' => 'Password changed successfully.');
	}

	public function current_user()
	{
		if ( ! $this->logged_in())
		{
			return NULL;
		}
		return $this->CI->User_model->get_with_role($this->user_id());
	}
}
