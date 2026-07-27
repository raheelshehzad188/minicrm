<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model {

	protected $table = 'activity_logs';

	public function insert_log($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function recent_for_org($org_id, $limit = 50)
	{
		$this->db->select('activity_logs.*, users.name AS user_name');
		$this->db->from($this->table);
		$this->db->join('users', 'users.id = activity_logs.user_id', 'left');
		$this->db->where('activity_logs.organization_id', (int) $org_id);
		$this->db->order_by('activity_logs.id', 'DESC');
		$this->db->limit($limit);
		return $this->db->get()->result();
	}

	/**
	 * Recent activities with optional user and date filters (dashboard)
	 */
	public function recent_filtered($org_id, $filters = array(), $limit = 20)
	{
		$this->db->select('activity_logs.*, users.name AS user_name');
		$this->db->from($this->table);
		$this->db->join('users', 'users.id = activity_logs.user_id', 'left');
		$this->db->where('activity_logs.organization_id', (int) $org_id);

		if ( ! empty($filters['user_id']))
		{
			$this->db->where('activity_logs.user_id', (int) $filters['user_id']);
		}
		if ( ! empty($filters['date_start']))
		{
			$this->db->where('activity_logs.created_at >=', $filters['date_start']);
		}
		if ( ! empty($filters['date_end']))
		{
			$this->db->where('activity_logs.created_at <=', $filters['date_end']);
		}

		$this->db->order_by('activity_logs.id', 'DESC');
		$this->db->limit((int) $limit);
		return $this->db->get()->result();
	}
}
