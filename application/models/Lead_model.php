<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_model extends MY_Model {

	protected $table = 'leads';
	protected $tenant_scoped = TRUE;
	protected $include_deleted = FALSE;

	public function with_trashed()
	{
		$this->include_deleted = TRUE;
		return $this;
	}

	public function only_trashed()
	{
		$this->include_deleted = 'only';
		return $this;
	}

	protected function apply_soft_delete_scope()
	{
		if ($this->include_deleted === 'only')
		{
			$this->db->where($this->table . '.deleted_at IS NOT NULL', NULL, FALSE);
		}
		elseif ($this->include_deleted !== TRUE)
		{
			$this->db->where($this->table . '.deleted_at IS NULL', NULL, FALSE);
		}
		$this->include_deleted = FALSE;
	}

	protected function base_select()
	{
		$this->db->select('leads.*,
			CONCAT(IFNULL(leads.first_name,""), " ", IFNULL(leads.last_name,"")) AS full_name,
			lead_statuses.name AS status_name, lead_statuses.color AS status_color, lead_statuses.slug AS status_slug,
			lead_statuses.icon AS status_icon, lead_statuses.is_won, lead_statuses.is_lost,
			lead_sources.name AS source_name, lead_sources.color AS source_color, lead_sources.icon AS source_icon,
			pipelines.name AS pipeline_name,
			deal_stages.name AS stage_name, deal_stages.color AS stage_color, deal_stages.slug AS stage_slug,
			task_priorities.name AS priority_name, task_priorities.color AS priority_color, task_priorities.slug AS priority_slug,
			assignee.name AS assignee_name, assignee.email AS assignee_email, assignee.profile_image AS assignee_image,
			creator.name AS creator_name');
		$this->db->from($this->table);
		$this->db->join('lead_statuses', 'lead_statuses.id = leads.lead_status_id', 'left');
		$this->db->join('lead_sources', 'lead_sources.id = leads.lead_source_id', 'left');
		$this->db->join('pipelines', 'pipelines.id = leads.pipeline_id', 'left');
		$this->db->join('deal_stages', 'deal_stages.id = leads.stage_id', 'left');
		$this->db->join('task_priorities', 'task_priorities.id = leads.priority_id', 'left');
		$this->db->join('users assignee', 'assignee.id = leads.assigned_to', 'left');
		$this->db->join('users creator', 'creator.id = leads.created_by', 'left');
	}

	public function get_full($id)
	{
		$this->apply_tenant_scope();
		$this->include_deleted = TRUE;
		$this->apply_soft_delete_scope();
		$this->base_select();
		$this->db->where('leads.id', (int) $id);
		return $this->db->get()->row();
	}

	public function datatable($filters = array())
	{
		$this->apply_tenant_scope();
		if ( ! empty($filters['trashed']))
		{
			$this->include_deleted = 'only';
		}
		$this->apply_soft_delete_scope();
		$this->base_select();
		$this->apply_filters($filters);

		$CI =& get_instance();
		if (isset($CI->lead_lib))
		{
			$CI->lead_lib->apply_visibility_scope('leads');
		}

		$this->db->order_by('leads.id', 'DESC');
		if ( ! empty($filters['limit']))
		{
			$this->db->limit((int) $filters['limit'], (int) (isset($filters['offset']) ? $filters['offset'] : 0));
		}
		return $this->db->get()->result();
	}

	public function count_filtered($filters = array())
	{
		$this->apply_tenant_scope();
		if ( ! empty($filters['trashed']))
		{
			$this->include_deleted = 'only';
		}
		$this->apply_soft_delete_scope();
		$this->db->from($this->table);
		$this->db->join('lead_statuses', 'lead_statuses.id = leads.lead_status_id', 'left');
		$this->db->join('lead_sources', 'lead_sources.id = leads.lead_source_id', 'left');
		$this->apply_filters($filters);
		$CI =& get_instance();
		if (isset($CI->lead_lib))
		{
			$CI->lead_lib->apply_visibility_scope('leads');
		}
		return (int) $this->db->count_all_results();
	}

	protected function apply_filters($filters)
	{
		if ( ! empty($filters['search']))
		{
			$s = $this->db->escape_like_str($filters['search']);
			$this->db->group_start();
			$this->db->like('leads.title', $s);
			$this->db->or_like('leads.first_name', $s);
			$this->db->or_like('leads.last_name', $s);
			$this->db->or_like('leads.company_name', $s);
			$this->db->or_like('leads.email', $s);
			$this->db->or_like('leads.phone', $s);
			$this->db->or_like('leads.mobile', $s);
			$this->db->group_end();
		}
		if ( ! empty($filters['status_id']))
		{
			$this->db->where('leads.lead_status_id', (int) $filters['status_id']);
		}
		if ( ! empty($filters['source_id']))
		{
			$this->db->where('leads.lead_source_id', (int) $filters['source_id']);
		}
		if ( ! empty($filters['assigned_to']))
		{
			$this->db->where('leads.assigned_to', (int) $filters['assigned_to']);
		}
		if ( ! empty($filters['pipeline_id']))
		{
			$this->db->where('leads.pipeline_id', (int) $filters['pipeline_id']);
		}
		if ( ! empty($filters['stage_id']))
		{
			$this->db->where('leads.stage_id', (int) $filters['stage_id']);
		}
		if ( ! empty($filters['priority_id']))
		{
			$this->db->where('leads.priority_id', (int) $filters['priority_id']);
		}
		if ( ! empty($filters['date_from']))
		{
			$this->db->where('leads.created_at >=', $filters['date_from'] . ' 00:00:00');
		}
		if ( ! empty($filters['date_to']))
		{
			$this->db->where('leads.created_at <=', $filters['date_to'] . ' 23:59:59');
		}
		if ( ! empty($filters['tag_id']))
		{
			$this->db->where('EXISTS (
				SELECT 1 FROM lead_tag_map ltm
				WHERE ltm.lead_id = leads.id AND ltm.tag_id = ' . (int) $filters['tag_id'] . '
			)', NULL, FALSE);
		}
	}

	public function kanban_by_status($filters = array())
	{
		$filters['trashed'] = 0;
		$rows = $this->datatable($filters);
		$grouped = array();
		foreach ($rows as $row)
		{
			$key = (int) $row->lead_status_id;
			if ( ! isset($grouped[$key]))
			{
				$grouped[$key] = array();
			}
			$grouped[$key][] = $row;
		}
		return $grouped;
	}

	public function find_duplicates($email, $phone, $mobile, $exclude_id = NULL)
	{
		$this->apply_tenant_scope();
		$this->db->where('deleted_at IS NULL', NULL, FALSE);
		$this->db->group_start();
		$has = FALSE;
		if ($email)
		{
			$this->db->or_where('email', $email);
			$has = TRUE;
		}
		if ($phone)
		{
			$this->db->or_where('phone', $phone);
			$this->db->or_where('mobile', $phone);
			$has = TRUE;
		}
		if ($mobile)
		{
			$this->db->or_where('mobile', $mobile);
			$this->db->or_where('phone', $mobile);
			$has = TRUE;
		}
		$this->db->group_end();
		if ( ! $has)
		{
			return array();
		}
		if ($exclude_id)
		{
			$this->db->where('id !=', (int) $exclude_id);
		}
		$this->db->limit(10);
		return $this->db->get($this->table)->result();
	}

	public function soft_delete($id, $user_id = NULL)
	{
		$this->apply_tenant_scope();
		$this->db->where('id', (int) $id);
		$this->db->where('deleted_at IS NULL', NULL, FALSE);
		return $this->db->update($this->table, array(
			'deleted_at' => date('Y-m-d H:i:s'),
			'deleted_by' => $user_id ?: (int) current_user_id(),
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => $user_id ?: (int) current_user_id(),
		));
	}

	public function restore($id)
	{
		$this->apply_tenant_scope();
		$this->db->where('id', (int) $id);
		$this->db->where('deleted_at IS NOT NULL', NULL, FALSE);
		return $this->db->update($this->table, array(
			'deleted_at' => NULL,
			'deleted_by' => NULL,
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => (int) current_user_id(),
		));
	}

	public function force_delete($id)
	{
		$this->apply_tenant_scope();
		$this->db->where('id', (int) $id);
		return $this->db->delete($this->table);
	}

	public function sync_tags($lead_id, array $tag_ids)
	{
		$this->db->where('lead_id', (int) $lead_id)->delete('lead_tag_map');
		foreach ($tag_ids as $tid)
		{
			$tid = (int) $tid;
			if ($tid > 0)
			{
				$this->db->insert('lead_tag_map', array('lead_id' => (int) $lead_id, 'tag_id' => $tid));
			}
		}
	}

	public function get_tag_ids($lead_id)
	{
		$rows = $this->db->select('tag_id')->where('lead_id', (int) $lead_id)->get('lead_tag_map')->result();
		return array_map(function ($r) { return (int) $r->tag_id; }, $rows);
	}

	public function get_tags_for_leads(array $lead_ids)
	{
		if (empty($lead_ids))
		{
			return array();
		}
		$this->db->select('lead_tag_map.lead_id, lead_tags.id, lead_tags.name, lead_tags.color');
		$this->db->from('lead_tag_map');
		$this->db->join('lead_tags', 'lead_tags.id = lead_tag_map.tag_id');
		$this->db->where_in('lead_tag_map.lead_id', $lead_ids);
		$rows = $this->db->get()->result();
		$out = array();
		foreach ($rows as $r)
		{
			$out[(int) $r->lead_id][] = array(
				'id'    => (int) $r->id,
				'name'  => $r->name,
				'color' => $r->color,
			);
		}
		return $out;
	}

	public function save_custom_values($lead_id, array $values)
	{
		$org = (int) current_org_id();
		foreach ($values as $field_id => $value)
		{
			$field_id = (int) $field_id;
			if ($field_id <= 0) continue;
			$existing = $this->db->get_where('lead_custom_values', array(
				'lead_id' => (int) $lead_id,
				'custom_field_id' => $field_id,
			))->row();
			$payload = array(
				'organization_id' => $org,
				'lead_id'         => (int) $lead_id,
				'custom_field_id' => $field_id,
				'value'           => is_array($value) ? json_encode($value) : (string) $value,
				'updated_at'      => date('Y-m-d H:i:s'),
			);
			if ($existing)
			{
				$this->db->where('id', $existing->id)->update('lead_custom_values', $payload);
			}
			else
			{
				$payload['created_at'] = date('Y-m-d H:i:s');
				$this->db->insert('lead_custom_values', $payload);
			}
		}
	}

	public function get_custom_values($lead_id)
	{
		$this->db->select('lead_custom_values.*, custom_fields.name, custom_fields.slug, custom_fields.field_type');
		$this->db->from('lead_custom_values');
		$this->db->join('custom_fields', 'custom_fields.id = lead_custom_values.custom_field_id');
		$this->db->where('lead_custom_values.lead_id', (int) $lead_id);
		$this->db->where('lead_custom_values.organization_id', (int) current_org_id());
		$rows = $this->db->get()->result();
		$out = array();
		foreach ($rows as $r)
		{
			$out[(int) $r->custom_field_id] = $r->value;
		}
		return $out;
	}
}
