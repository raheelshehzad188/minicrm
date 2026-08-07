<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organization extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Organization_model');
		$this->load->library(array('Activity_lib', 'Mino_upload', 'Notification_lib'));
	}

	public function index()
	{
		$this->permission_lib->require('organization.view');

		$org = $this->organization_lib->boot();
		$data = array(
			'page_title'    => 'Organization Settings',
			'page_subtitle' => 'Manage your company profile and branding',
			'active_menu'   => 'organization',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Organization', 'url' => ''),
			),
			'content_view'  => 'settings',
			'content_data'  => array(
				'org'          => $org,
				'can_edit'     => $this->permission_lib->can('organization.edit'),
				'timezones'    => DateTimeZone::listIdentifiers(),
				'currencies'   => array('USD','EUR','GBP','CAD','AUD','PKR','INR','AED'),
			),
		);
		$this->load->view('layouts/master', $data);
	}

	public function update()
	{
		$this->permission_lib->require('organization.edit');

		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$this->form_validation->set_rules('name', 'Company Name', 'required|trim|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');
		$this->form_validation->set_rules('country', 'Country', 'trim|max_length[100]');
		$this->form_validation->set_rules('timezone', 'Timezone', 'required|trim|max_length[64]');
		$this->form_validation->set_rules('currency', 'Currency', 'required|trim|max_length[10]');
		$this->form_validation->set_rules('website', 'Website', 'trim|max_length[255]');
		$this->form_validation->set_rules('registration_number', 'Registration Number', 'trim|max_length[100]');
		$this->form_validation->set_rules('tax_number', 'Tax Number', 'trim|max_length[100]');

		if ($this->form_validation->run() === FALSE)
		{
			return $this->json_response(FALSE, strip_tags(validation_errors(' ', ' ')));
		}

		$org_id = $this->organization_lib->id();
		$payload = array(
			'name'                 => $this->input->post('name', TRUE),
			'email'                => $this->input->post('email', TRUE),
			'phone'                => $this->input->post('phone', TRUE),
			'address'              => $this->input->post('address', TRUE),
			'country'              => $this->input->post('country', TRUE),
			'timezone'             => $this->input->post('timezone', TRUE),
			'currency'             => $this->input->post('currency', TRUE),
			'website'              => $this->input->post('website', TRUE),
			'registration_number'  => $this->input->post('registration_number', TRUE),
			'tax_number'           => $this->input->post('tax_number', TRUE),
		);

		$ok = $this->Organization_model->update_settings($org_id, $payload);
		if ($ok)
		{
			$this->session->set_userdata('org_name', $payload['name']);
			$this->activity_lib->log('update', 'Updated organization settings', 'organization', $org_id);
			$this->notification_lib->push(
				$this->auth_lib->user_id(),
				'Organization updated',
				'Your organization settings were saved successfully.',
				'success',
				site_url('organization')
			);
		}

		return $this->json_response((bool) $ok, $ok ? 'Organization updated successfully.' : 'Unable to update.');
	}

	public function upload_logo()
	{
		$this->permission_lib->require('organization.edit');

		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		if (empty($_FILES['logo']['name']))
		{
			return $this->json_response(FALSE, 'Please choose a logo image.');
		}

		$result = $this->mino_upload->image('logo', 'logos', array('max_size' => 2048));
		if ( ! $result['success'])
		{
			return $this->json_response(FALSE, $result['message']);
		}

		$org = $this->organization_lib->current();
		if ($org && ! empty($org->logo))
		{
			$this->mino_upload->delete_file($org->logo);
		}

		$org_id = $this->organization_lib->id();
		$this->Organization_model->update_settings($org_id, array('logo' => $result['path']));
		$this->activity_lib->log('update', 'Uploaded organization logo', 'organization', $org_id);

		return $this->json_response(TRUE, 'Logo uploaded.', array(
			'logo_url' => base_url($result['path']),
			'logo'     => $result['path'],
		));
	}

	public function regenerate_api_key()
	{
		$this->permission_lib->require('organization.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$org_id = $this->organization_lib->id();
		$key = $this->Organization_model->regenerate_api_key($org_id);
		$this->activity_lib->log('update', 'Regenerated organization API key', 'organization', $org_id);
		$this->organization_lib->boot();
		return $this->json_response(TRUE, 'API key regenerated.', array('api_key' => $key));
	}
}
