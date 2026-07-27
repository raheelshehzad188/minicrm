<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * UI Kit showcase — authenticated (design system preview)
 */
class Ui extends Auth_Controller {

	public function components()
	{
		$data = array(
			'page_title'    => 'Components',
			'page_subtitle' => 'Reusable UI building blocks for Mino CRM modules',
			'active_menu'   => 'components',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'UI Kit', 'url' => ''),
				array('label' => 'Components', 'url' => ''),
			),
			'content_view'  => 'ui/components',
		);
		$this->load->view('layouts/master', $data);
	}

	public function forms()
	{
		$data = array(
			'page_title'    => 'Forms',
			'page_subtitle' => 'Professional form system with validation states and input patterns',
			'active_menu'   => 'forms',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'UI Kit', 'url' => ''),
				array('label' => 'Forms', 'url' => ''),
			),
			'content_view'  => 'ui/forms',
		);
		$this->load->view('layouts/master', $data);
	}

	public function tables()
	{
		$data = array(
			'page_title'    => 'Tables',
			'page_subtitle' => 'DataTable-ready list patterns for CRM modules',
			'active_menu'   => 'tables',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'UI Kit', 'url' => ''),
				array('label' => 'Tables', 'url' => ''),
			),
			'page_actions'  => '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeadModal"><i class="fas fa-plus"></i> Add Lead</button>',
			'content_view'  => 'ui/tables',
		);
		$this->load->view('layouts/master', $data);
	}

	public function index()
	{
		redirect('ui/components');
	}
}
