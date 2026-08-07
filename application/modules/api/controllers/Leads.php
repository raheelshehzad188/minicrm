<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/Api_Controller.php';

/**
 * REST API — POST /api/v1/leads (webhook-ready)
 */
class Leads extends Api_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('Lead_model', 'Crm_lookup_model'));
	}

	/**
	 * Create lead — POST /api/v1/leads
	 */
	public function create()
	{
		if (strtoupper($this->input->method(TRUE)) !== 'POST')
		{
			return $this->api_error('Method not allowed. Use POST.', 405);
		}

		if ($this->require_api_key() !== TRUE)
		{
			return;
		}

		$input = $this->json_input();
		if (empty($input))
		{
			// Fallback to form-encoded
			$input = $this->input->post(NULL, TRUE);
			if ( ! is_array($input))
			{
				$input = array();
			}
		}

		$errors = array();
		$type_raw = isset($input['type']) ? $input['type'] : (isset($input['lead_type']) ? $input['lead_type'] : '');
		$lead_type = $this->lead_lib->normalize_lead_type($type_raw);
		if ( ! $lead_type)
		{
			$errors['type'] = 'Type is required and must be a valid lead type (clinic, academy).';
		}

		$name = isset($input['name']) ? trim((string) $input['name']) : '';
		if ($name === '')
		{
			$errors['name'] = 'Name is required.';
		}

		$phone = isset($input['phone']) ? trim((string) $input['phone']) : '';
		if ($phone === '')
		{
			$errors['phone'] = 'Phone is required.';
		}

		$email = isset($input['email']) ? trim((string) $input['email']) : '';
		if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$errors['email'] = 'Email is invalid.';
		}

		if ( ! empty($errors))
		{
			return $this->api_error('Validation failed.', 422, array('errors' => $errors));
		}

		$dupes = $this->lead_lib->find_duplicates($email ?: NULL, $phone, NULL);
		if ($dupes)
		{
			$dup_errors = array();
			foreach ($dupes as $d)
			{
				if ($phone && ($d->phone === $phone || $d->mobile === $phone))
				{
					$dup_errors['phone'] = 'Duplicate phone number.';
				}
				if ($email && strcasecmp((string) $d->email, $email) === 0)
				{
					$dup_errors['email'] = 'Duplicate email address.';
				}
			}
			if (empty($dup_errors))
			{
				$dup_errors['phone'] = 'Duplicate phone or email.';
			}
			return $this->api_error('Duplicate lead detected.', 409, array(
				'errors'     => $dup_errors,
				'duplicates' => array_map(function ($d) {
					return array('id' => (int) $d->id, 'title' => $d->title, 'email' => $d->email, 'phone' => $d->phone);
				}, $dupes),
			));
		}

		$source_id = NULL;
		if ( ! empty($input['source']))
		{
			$src = $this->Crm_lookup_model->source_by_name($input['source']);
			if ($src)
			{
				$source_id = (int) $src->id;
			}
			else
			{
				$source_id = $this->_ensure_source((string) $input['source']);
			}
		}

		$status_id = $this->Crm_lookup_model->default_status_id();
		$pipe = $this->Crm_lookup_model->default_pipeline();
		$pipeline_id = $pipe ? (int) $pipe->id : NULL;
		$stage_id = $pipeline_id ? $this->Crm_lookup_model->first_stage_id($pipeline_id) : NULL;

		$payload = array(
			'organization_id'  => (int) $this->api_org->id,
			'lead_type'        => $lead_type,
			'title'            => $name,
			'first_name'       => $name,
			'last_name'        => NULL,
			'email'            => $email !== '' ? $email : NULL,
			'phone'            => $phone,
			'description'      => ! empty($input['notes']) ? trim((string) $input['notes']) : NULL,
			'branch'           => ! empty($input['branch']) ? trim((string) $input['branch']) : NULL,
			'treatment'        => NULL,
			'course'           => NULL,
			'preferred_batch'  => NULL,
			'appointment_date' => NULL,
			'appointment_time' => NULL,
			'lead_source_id'   => $source_id,
			'lead_status_id'   => $status_id,
			'pipeline_id'      => $pipeline_id,
			'stage_id'         => $stage_id,
			'created_by'       => NULL,
			'updated_by'       => NULL,
			'created_at'       => date('Y-m-d H:i:s'),
		);

		if ($lead_type === 'clinic')
		{
			$service = isset($input['service']) ? $input['service'] : (isset($input['treatment']) ? $input['treatment'] : '');
			$payload['treatment'] = $service !== '' ? trim((string) $service) : NULL;
			$payload['appointment_date'] = ! empty($input['appointment_date']) ? $input['appointment_date'] : NULL;
			$payload['appointment_time'] = ! empty($input['appointment_time']) ? $input['appointment_time'] : NULL;
		}
		elseif ($lead_type === 'academy')
		{
			$payload['course'] = ! empty($input['course']) ? trim((string) $input['course']) : NULL;
			$payload['preferred_batch'] = ! empty($input['preferred_batch']) ? trim((string) $input['preferred_batch']) : NULL;
		}

		try
		{
			$id = $this->Lead_model->insert($payload);
			if ( ! $id)
			{
				return $this->api_error('Failed to create lead.', 500);
			}

			$this->lead_lib->timeline($id, 'created', 'Lead Created', $name . ' was added via API');
			$this->activity_lib->log('create', 'API created lead ' . $name, 'leads', $id);

			return $this->api_success('Lead created successfully.', array(
				'id'        => (int) $id,
				'lead_type' => $lead_type,
				'title'     => $name,
			), 201);
		}
		catch (Exception $e)
		{
			return $this->api_error('Server error: ' . $e->getMessage(), 500);
		}
	}

	protected function _ensure_source($name)
	{
		$name = trim($name);
		if ($name === '')
		{
			return NULL;
		}
		$slug = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $name));
		$slug = trim($slug, '_');
		$row = array(
			'organization_id' => (int) current_org_id(),
			'name'            => $name,
			'slug'            => $slug ?: 'source',
			'color'           => '#0284C7',
			'icon'            => 'fa-globe',
			'sort_order'      => 99,
			'is_active'       => 1,
			'created_at'      => date('Y-m-d H:i:s'),
		);
		$this->db->insert('lead_sources', $row);
		return (int) $this->db->insert_id();
	}
}
