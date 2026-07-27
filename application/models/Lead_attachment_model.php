<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_attachment_model extends MY_Model {

	protected $table = 'lead_attachments';
	protected $tenant_scoped = TRUE;

	public function for_lead($lead_id)
	{
		$this->apply_tenant_scope();
		$this->db->select('lead_attachments.*, users.name AS user_name');
		$this->db->from($this->table);
		$this->db->join('users', 'users.id = lead_attachments.user_id', 'left');
		$this->db->where('lead_attachments.lead_id', (int) $lead_id);
		$this->db->where('lead_attachments.deleted_at IS NULL', NULL, FALSE);
		$this->db->order_by('lead_attachments.id', 'DESC');
		return $this->db->get()->result();
	}

	public function soft_delete($id)
	{
		$this->apply_tenant_scope();
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, array(
			'deleted_at' => date('Y-m-d H:i:s'),
		));
	}
}
