<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_timeline_model extends MY_Model {

	protected $table = 'lead_timeline';
	protected $tenant_scoped = TRUE;

	public function add($data)
	{
		if ( ! isset($data['organization_id']))
		{
			$data['organization_id'] = (int) current_org_id();
		}
		if ( ! isset($data['created_at']))
		{
			$data['created_at'] = date('Y-m-d H:i:s');
		}
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function for_lead($lead_id, $limit = 100)
	{
		$this->apply_tenant_scope();
		$this->db->select('lead_timeline.*, users.name AS user_name, users.profile_image');
		$this->db->from($this->table);
		$this->db->join('users', 'users.id = lead_timeline.user_id', 'left');
		$this->db->where('lead_timeline.lead_id', (int) $lead_id);
		$this->db->order_by('lead_timeline.id', 'DESC');
		$this->db->limit((int) $limit);
		return $this->db->get()->result();
	}
}
