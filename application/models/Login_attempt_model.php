<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_attempt_model extends CI_Model {

	protected $table = 'login_attempts';

	public function record($email, $ip, $success = FALSE)
	{
		return $this->db->insert($this->table, array(
			'email'        => $email,
			'ip_address'   => $ip,
			'attempted_at' => date('Y-m-d H:i:s'),
			'success'      => $success ? 1 : 0,
		));
	}

	public function count_recent_failures($email, $ip, $window_minutes)
	{
		$since = date('Y-m-d H:i:s', time() - ((int) $window_minutes * 60));
		$this->db->from($this->table);
		$this->db->where('email', $email);
		$this->db->where('ip_address', $ip);
		$this->db->where('success', 0);
		$this->db->where('attempted_at >=', $since);
		return (int) $this->db->count_all_results();
	}

	public function clear_failures($email, $ip)
	{
		$this->db->where('email', $email);
		$this->db->where('ip_address', $ip);
		$this->db->where('success', 0);
		return $this->db->delete($this->table);
	}
}
