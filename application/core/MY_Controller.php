<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller — shared bootstrap for all Mino CRM controllers
 */
class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->library(array('session', 'form_validation', 'Auth_lib', 'Permission_lib', 'Organization_lib'));
		$this->load->helper(array('url', 'html', 'form', 'security', 'auth', 'organization'));
		$this->config->load('auth', TRUE);
	}

	/**
	 * JSON response helper for AJAX endpoints
	 */
	protected function json_response($success, $message = '', $data = array(), $http_code = 200)
	{
		$this->output
			->set_status_header($http_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode(array(
				'success'    => (bool) $success,
				'message'    => $message,
				'data'       => $data,
				'csrf_name'  => $this->security->get_csrf_token_name(),
				'csrf_hash'  => $this->security->get_csrf_hash(),
			)));
	}

	/**
	 * Detect AJAX request
	 */
	protected function is_ajax()
	{
		return $this->input->is_ajax_request();
	}
}

/**
 * Public Controller — guests only (login, forgot, reset)
 */
class Public_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		// Auto-login via remember cookie
		$this->auth_lib->attempt_remember_login();

		if ($this->auth_lib->logged_in())
		{
			$redir = $this->config->item('login_redirect', 'auth') ?: 'dashboard';
			redirect($redir);
		}
	}
}

/**
 * Auth Controller — requires authenticated session + optional permission
 */
class Auth_Controller extends MY_Controller {

	/** @var bool Require login (disable only for rare cases) */
	protected $require_auth = TRUE;

	/** @var string|array|null Optional permission slug(s) */
	protected $required_permission = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->auth_lib->attempt_remember_login();

		if ($this->require_auth && ! $this->auth_lib->logged_in())
		{
			$this->session->set_userdata('redirect_after_login', current_url());
			if ($this->is_ajax())
			{
				$this->json_response(FALSE, 'Unauthenticated.', array(), 401);
				$this->output->_display();
				exit;
			}
			redirect('auth/login');
		}

		if ($this->required_permission)
		{
			$ok = is_array($this->required_permission)
				? $this->permission_lib->has_any($this->required_permission)
				: $this->permission_lib->can($this->required_permission);

			if ( ! $ok)
			{
				if ($this->is_ajax())
				{
					$this->json_response(FALSE, 'Permission denied.', array(), 403);
					$this->output->_display();
					exit;
				}
				show_error('You do not have permission to access this page.', 403);
			}
		}

		// Bind current organization for tenant-scoped models
		$this->organization_lib->boot();
	}

	/**
	 * Render page using the existing master layout (UI foundation)
	 */
	protected function render($content_view, $data = array())
	{
		$data['content_view'] = $content_view;
		$this->load->view('layouts/master', $data);
	}

	/**
	 * Render auth layout pages
	 */
	protected function render_auth($content_view, $data = array())
	{
		$data['content_view'] = $content_view;
		$this->load->view('layouts/auth_master', $data);
	}
}
