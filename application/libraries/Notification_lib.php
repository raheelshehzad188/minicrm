<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notification Library — in-app notifications
 */
class Notification_lib {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Notification_model');
	}

	public function push($user_id, $title, $message, $type = 'info', $link = NULL, $organization_id = NULL)
	{
		$org_id = $organization_id ?: (int) $this->CI->session->userdata('organization_id');
		return $this->CI->Notification_model->create(array(
			'organization_id' => (int) $org_id,
			'user_id'         => (int) $user_id,
			'title'           => $title,
			'message'         => $message,
			'type'            => $type,
			'link'            => $link,
			'is_read'         => 0,
			'created_at'      => date('Y-m-d H:i:s'),
		));
	}

	public function notify_org_admins($title, $message, $type = 'info', $link = NULL)
	{
		$this->CI->load->model('User_model');
		$admins = $this->CI->User_model->get_org_admins();
		foreach ($admins as $admin)
		{
			$this->push($admin->id, $title, $message, $type, $link);
		}
	}

	public function unread_count($user_id = NULL)
	{
		$uid = $user_id ?: (int) $this->CI->session->userdata('user_id');
		return $this->CI->Notification_model->unread_count($uid);
	}

	public function latest($limit = 10, $user_id = NULL)
	{
		$uid = $user_id ?: (int) $this->CI->session->userdata('user_id');
		return $this->CI->Notification_model->latest_for_user($uid, $limit);
	}

	public function mark_read($id, $user_id = NULL)
	{
		$uid = $user_id ?: (int) $this->CI->session->userdata('user_id');
		return $this->CI->Notification_model->mark_read($id, $uid);
	}

	public function mark_all_read($user_id = NULL)
	{
		$uid = $user_id ?: (int) $this->CI->session->userdata('user_id');
		return $this->CI->Notification_model->mark_all_read($uid);
	}
}
