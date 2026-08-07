<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lead Library — visibility scope, duplicates, timeline, notifications
 */
class Lead_lib {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load('leads', TRUE);
	}

	/**
	 * Registered lead types (slug => label). Future types are added in config/leads.php.
	 */
	public function lead_types()
	{
		$types = $this->CI->config->item('lead_types', 'leads');
		return is_array($types) ? $types : array('clinic' => 'Clinic', 'academy' => 'Academy');
	}

	public function default_lead_type()
	{
		$default = $this->CI->config->item('lead_type_default', 'leads');
		return $default ?: 'clinic';
	}

	public function normalize_lead_type($type)
	{
		$type = strtolower(trim((string) $type));
		$types = $this->lead_types();
		if ($type === '' || ! isset($types[$type]))
		{
			return NULL;
		}
		return $type;
	}

	public function lead_type_label($type)
	{
		$types = $this->lead_types();
		$type = $this->normalize_lead_type($type) ?: $type;
		return isset($types[$type]) ? $types[$type] : ucfirst((string) $type);
	}

	/**
	 * Visibility mode: org | team | own
	 */
	public function visibility_mode()
	{
		$slug = (string) $this->CI->session->userdata('role_slug');
		if ($this->CI->permission_lib->is_owner() || $slug === 'admin')
		{
			return 'org';
		}
		if ($slug === 'manager')
		{
			return 'team';
		}
		return 'own';
	}

	public function can_access_lead($lead)
	{
		if ( ! $lead)
		{
			return FALSE;
		}
		if ((int) $lead->organization_id !== (int) current_org_id())
		{
			return FALSE;
		}

		$mode = $this->visibility_mode();
		if ($mode === 'org' || $mode === 'team')
		{
			return TRUE;
		}

		$uid = (int) current_user_id();
		return ((int) $lead->assigned_to === $uid) || ((int) $lead->created_by === $uid);
	}

	public function apply_visibility_scope($alias = 'leads')
	{
		$mode = $this->visibility_mode();
		if ($mode === 'own')
		{
			$uid = (int) current_user_id();
			$this->CI->db->group_start();
			$this->CI->db->where($alias . '.assigned_to', $uid);
			$this->CI->db->or_where($alias . '.created_by', $uid);
			$this->CI->db->group_end();
		}
	}

	public function timeline($lead_id, $event_type, $title, $description = NULL, $meta = NULL)
	{
		$this->CI->load->model('Lead_timeline_model');
		$user_id = (int) current_user_id();
		return $this->CI->Lead_timeline_model->add(array(
			'organization_id' => (int) current_org_id(),
			'lead_id'         => (int) $lead_id,
			'user_id'         => $user_id > 0 ? $user_id : NULL,
			'event_type'      => $event_type,
			'title'           => $title,
			'description'     => $description,
			'meta'            => $meta ? json_encode($meta) : NULL,
			'created_at'      => date('Y-m-d H:i:s'),
		));
	}

	public function notify_assignee($user_id, $title, $message, $lead_id)
	{
		if ( ! $user_id || (int) $user_id === (int) current_user_id())
		{
			return;
		}
		$this->CI->load->library('Notification_lib');
		$this->CI->notification_lib->push(
			(int) $user_id,
			$title,
			$message,
			'info',
			site_url('leads/profile/' . (int) $lead_id)
		);
	}

	/**
	 * Duplicate candidates by email/phone/mobile within org
	 */
	public function find_duplicates($email, $phone, $mobile, $exclude_id = NULL)
	{
		$this->CI->load->model('Lead_model');
		return $this->CI->Lead_model->find_duplicates($email, $phone, $mobile, $exclude_id);
	}
}
