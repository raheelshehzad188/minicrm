<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {

	protected $table = 'notifications';

	public function create($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function unread_count($user_id)
	{
		return (int) $this->db->where(array(
			'user_id' => (int) $user_id,
			'is_read' => 0,
		))->count_all_results($this->table);
	}

	public function latest_for_user($user_id, $limit = 10)
	{
		$this->db->where('user_id', (int) $user_id);
		$this->db->order_by('id', 'DESC');
		$this->db->limit($limit);
		return $this->db->get($this->table)->result();
	}

	public function mark_read($id, $user_id)
	{
		return $this->db->where(array(
			'id'      => (int) $id,
			'user_id' => (int) $user_id,
		))->update($this->table, array('is_read' => 1));
	}

	public function mark_all_read($user_id)
	{
		return $this->db->where(array(
			'user_id' => (int) $user_id,
			'is_read' => 0,
		))->update($this->table, array('is_read' => 1));
	}
}
