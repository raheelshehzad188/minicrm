<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_note_model extends MY_Model {

	protected $table = 'lead_notes';
	protected $tenant_scoped = TRUE;

	public function for_lead($lead_id)
	{
		$this->apply_tenant_scope();
		$this->db->select('lead_notes.*, users.name AS user_name, users.profile_image');
		$this->db->from($this->table);
		$this->db->join('users', 'users.id = lead_notes.user_id', 'left');
		$this->db->where('lead_notes.lead_id', (int) $lead_id);
		$this->db->where('lead_notes.deleted_at IS NULL', NULL, FALSE);
		$this->db->order_by('lead_notes.is_pinned', 'DESC');
		$this->db->order_by('lead_notes.id', 'DESC');
		return $this->db->get()->result();
	}

	public function soft_delete($id)
	{
		$this->apply_tenant_scope();
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, array(
			'deleted_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}
}
