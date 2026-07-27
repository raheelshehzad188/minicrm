<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password_reset_model extends CI_Model {

	protected $table = 'password_resets';

	public function create_token($email, $hours = 1)
	{
		$token = bin2hex(random_bytes(32));
		$this->db->insert($this->table, array(
			'email'      => $email,
			'token'      => $token,
			'expires_at' => date('Y-m-d H:i:s', time() + ((int) $hours * 3600)),
			'created_at' => date('Y-m-d H:i:s'),
		));
		return $token;
	}

	public function find_valid($token)
	{
		$this->db->where('token', $token);
		$this->db->where('used_at IS NULL', NULL, FALSE);
		$this->db->where('expires_at >=', date('Y-m-d H:i:s'));
		return $this->db->get($this->table)->row();
	}

	public function mark_used($id)
	{
		return $this->db->where('id', (int) $id)->update($this->table, array(
			'used_at' => date('Y-m-d H:i:s'),
		));
	}

	public function invalidate_email($email)
	{
		return $this->db->where('email', $email)
			->where('used_at IS NULL', NULL, FALSE)
			->update($this->table, array('used_at' => date('Y-m-d H:i:s')));
	}
}
