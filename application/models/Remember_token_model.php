<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Remember_token_model extends CI_Model {

	protected $table = 'remember_tokens';

	public function create($user_id, $days = 30)
	{
		$selector = bin2hex(random_bytes(8));
		$validator = bin2hex(random_bytes(32));

		$this->db->insert($this->table, array(
			'user_id'    => (int) $user_id,
			'selector'   => $selector,
			'token_hash' => hash('sha256', $validator),
			'expires_at' => date('Y-m-d H:i:s', time() + ((int) $days * 86400)),
			'created_at' => date('Y-m-d H:i:s'),
		));

		return $selector . ':' . $validator;
	}

	public function find_valid_by_selector($selector)
	{
		$this->db->where('selector', $selector);
		$this->db->where('expires_at >=', date('Y-m-d H:i:s'));
		return $this->db->get($this->table)->row();
	}

	public function delete_by_selector($selector)
	{
		return $this->db->where('selector', $selector)->delete($this->table);
	}

	public function delete_by_user($user_id)
	{
		return $this->db->where('user_id', (int) $user_id)->delete($this->table);
	}
}
