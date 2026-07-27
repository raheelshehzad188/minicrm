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
		return $this->CI->Lead_timeline_model->add(array(
			'organization_id' => (int) current_org_id(),
			'lead_id'         => (int) $lead_id,
			'user_id'         => (int) current_user_id(),
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
