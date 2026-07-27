<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Leads HMVC Module — complete lead management
 */
class Leads extends Auth_Controller {

	protected $_lead_validation_error = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('Lead_lib', 'Activity_lib', 'Notification_lib', 'Mino_upload'));
		$this->load->model(array(
			'Lead_model',
			'Crm_lookup_model',
			'Lead_note_model',
			'Lead_attachment_model',
			'Lead_timeline_model',
			'Lead_saved_filter_model',
		));
		$this->load->helper('text');
	}

	/* -----------------------------------------------------------------
	 * Pages
	 * ----------------------------------------------------------------- */

	public function index()
	{
		$this->permission_lib->require('leads.view');
		$lookups = $this->_lookups_payload();

		$data = array(
			'page_title'    => 'Leads',
			'page_subtitle' => 'Manage and track your sales pipeline',
			'active_menu'   => 'leads',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Leads', 'url' => ''),
			),
			'page_actions'  => $this->_page_actions_html(),
			'content_view'  => 'index',
			'content_data'  => array_merge($lookups, array(
				'can_create' => $this->permission_lib->can('leads.create'),
				'can_edit'   => $this->permission_lib->can('leads.edit'),
				'can_delete' => $this->permission_lib->can('leads.delete'),
				'can_export' => $this->permission_lib->can('leads.export'),
				'can_import' => $this->permission_lib->can('leads.import'),
				'is_owner'   => $this->permission_lib->is_owner(),
				'urls'       => $this->_urls(),
			)),
		);
		$this->load->view('layouts/master', $data);
	}

	public function kanban()
	{
		$this->permission_lib->require('leads.view');
		$lookups = $this->_lookups_payload();

		$data = array(
			'page_title'    => 'Leads Kanban',
			'page_subtitle' => 'Drag leads across statuses',
			'active_menu'   => 'leads',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Leads', 'url' => site_url('leads')),
				array('label' => 'Kanban', 'url' => ''),
			),
			'page_actions'  => '
				<a href="' . site_url('leads') . '" class="btn btn-secondary btn-sm"><i class="fas fa-table"></i> List</a>
				' . ($this->permission_lib->can('leads.create')
					? '<button type="button" class="btn btn-primary btn-sm" id="btnAddLead"><i class="fas fa-plus"></i> Add Lead</button>'
					: ''),
			'content_view'  => 'kanban',
			'content_data'  => array_merge($lookups, array(
				'can_create' => $this->permission_lib->can('leads.create'),
				'can_edit'   => $this->permission_lib->can('leads.edit'),
				'urls'       => $this->_urls(),
			)),
		);
		$this->load->view('layouts/master', $data);
	}

	public function profile($id = 0)
	{
		$this->permission_lib->require('leads.view');
		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			show_error('Lead not found.', 404);
		}

		$lookups = $this->_lookups_payload();
		$data = array(
			'page_title'    => $lead->title,
			'page_subtitle' => trim($lead->first_name . ' ' . $lead->last_name) ?: ($lead->company_name ?: 'Lead profile'),
			'active_menu'   => 'leads',
			'breadcrumbs'   => array(
				array('label' => 'Home', 'url' => site_url('dashboard')),
				array('label' => 'Leads', 'url' => site_url('leads')),
				array('label' => $lead->title, 'url' => ''),
			),
			'page_actions'  => $this->_profile_actions_html($lead),
			'content_view'  => 'profile',
			'content_data'  => array_merge($lookups, array(
				'lead'       => $lead,
				'tag_ids'    => $this->Lead_model->get_tag_ids($lead->id),
				'can_edit'   => $this->permission_lib->can('leads.edit') && empty($lead->deleted_at),
				'can_delete' => $this->permission_lib->can('leads.delete'),
				'is_owner'   => $this->permission_lib->is_owner(),
				'urls'       => $this->_urls(),
			)),
		);
		$this->load->view('layouts/master', $data);
	}

	/* -----------------------------------------------------------------
	 * AJAX — list / meta
	 * ----------------------------------------------------------------- */

	public function datatable()
	{
		$this->permission_lib->require('leads.view');
		$filters = $this->_filters_from_input();
		$rows = $this->Lead_model->datatable($filters);
		$ids = array();
		foreach ($rows as $r) { $ids[] = (int) $r->id; }
		$tags = $this->Lead_model->get_tags_for_leads($ids);

		$data = array();
		foreach ($rows as $r)
		{
			$data[] = $this->_serialize_lead($r, isset($tags[(int) $r->id]) ? $tags[(int) $r->id] : array());
		}

		return $this->json_response(TRUE, 'OK', array(
			'rows'  => $data,
			'total' => $this->Lead_model->count_filtered($filters),
		));
	}

	public function kanban_data()
	{
		$this->permission_lib->require('leads.view');
		$filters = $this->_filters_from_input();
		$grouped = $this->Lead_model->kanban_by_status($filters);
		$statuses = $this->Crm_lookup_model->statuses();
		$columns = array();
		foreach ($statuses as $st)
		{
			$items = isset($grouped[(int) $st->id]) ? $grouped[(int) $st->id] : array();
			$cards = array();
			foreach ($items as $r)
			{
				$cards[] = $this->_serialize_lead($r);
			}
			$columns[] = array(
				'id'    => (int) $st->id,
				'name'  => $st->name,
				'slug'  => $st->slug,
				'color' => $st->color,
				'cards' => $cards,
			);
		}
		return $this->json_response(TRUE, 'OK', array('columns' => $columns));
	}

	public function meta()
	{
		$this->permission_lib->require('leads.view');
		return $this->json_response(TRUE, 'OK', $this->_lookups_payload(TRUE));
	}

	public function get($id = 0)
	{
		$this->permission_lib->require('leads.view');
		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$tags = $this->Lead_model->get_tags_for_leads(array((int) $lead->id));
		$payload = $this->_serialize_lead($lead, isset($tags[(int) $lead->id]) ? $tags[(int) $lead->id] : array());
		$payload['custom_values'] = $this->Lead_model->get_custom_values($lead->id);
		$payload['tag_ids'] = $this->Lead_model->get_tag_ids($lead->id);
		return $this->json_response(TRUE, 'OK', array('lead' => $payload));
	}

	public function check_duplicate()
	{
		$this->permission_lib->require('leads.create');
		$email  = $this->input->post('email', TRUE);
		$phone  = $this->input->post('phone', TRUE);
		$mobile = $this->input->post('mobile', TRUE);
		$exclude = (int) $this->input->post('exclude_id');
		$dupes = $this->lead_lib->find_duplicates($email, $phone, $mobile, $exclude ?: NULL);
		$list = array();
		foreach ($dupes as $d)
		{
			$list[] = array(
				'id'    => (int) $d->id,
				'title' => $d->title,
				'email' => $d->email,
				'phone' => $d->phone,
				'mobile'=> $d->mobile,
			);
		}
		return $this->json_response(TRUE, 'OK', array(
			'has_duplicates' => ! empty($list),
			'duplicates'     => $list,
		));
	}

	/* -----------------------------------------------------------------
	 * CRUD
	 * ----------------------------------------------------------------- */

	public function store()
	{
		$this->permission_lib->require('leads.create');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$force = (bool) $this->input->post('force_duplicate');
		$this->_lead_validation_error = '';
		$payload = $this->_validated_payload();
		if ($payload === FALSE)
		{
			$msg = $this->_lead_validation_error ?: strip_tags(validation_errors(' ', ' '));
			return $this->json_response(FALSE, $msg);
		}

		if ( ! $force)
		{
			$dupes = $this->lead_lib->find_duplicates($payload['email'], $payload['phone'], $payload['mobile']);
			if ($dupes)
			{
				$list = array();
				foreach ($dupes as $d)
				{
					$list[] = array('id' => (int) $d->id, 'title' => $d->title, 'email' => $d->email);
				}
				return $this->json_response(FALSE, 'Possible duplicate leads found.', array(
					'has_duplicates' => TRUE,
					'duplicates'     => $list,
				), 409);
			}
		}

		$payload['organization_id'] = (int) current_org_id();
		$payload['created_by'] = (int) current_user_id();
		$payload['updated_by'] = (int) current_user_id();
		$payload['created_at'] = date('Y-m-d H:i:s');

		$tag_ids = $this->_parse_tag_ids();
		$custom  = $this->input->post('custom_fields');

		$id = $this->Lead_model->insert($payload);
		if ( ! $id)
		{
			return $this->json_response(FALSE, 'Failed to create lead.');
		}

		$this->Lead_model->sync_tags($id, $tag_ids);
		if (is_array($custom))
		{
			$this->Lead_model->save_custom_values($id, $custom);
		}

		$this->lead_lib->timeline($id, 'created', 'Lead Created', $payload['title'] . ' was added');
		$this->activity_lib->log('create', 'Created lead ' . $payload['title'], 'leads', $id);

		if ( ! empty($payload['assigned_to']))
		{
			$this->lead_lib->notify_assignee($payload['assigned_to'], 'Lead assigned', 'You were assigned to ' . $payload['title'], $id);
			$this->lead_lib->timeline($id, 'assigned', 'Lead Assigned', 'Assigned to user #' . $payload['assigned_to']);
		}

		return $this->json_response(TRUE, 'Lead created successfully.', array('id' => (int) $id));
	}

	public function update($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}

		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! empty($lead->deleted_at) || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}

		$force = (bool) $this->input->post('force_duplicate');
		$this->_lead_validation_error = '';
		$payload = $this->_validated_payload();
		if ($payload === FALSE)
		{
			$msg = $this->_lead_validation_error ?: strip_tags(validation_errors(' ', ' '));
			return $this->json_response(FALSE, $msg);
		}

		if ( ! $force)
		{
			$dupes = $this->lead_lib->find_duplicates($payload['email'], $payload['phone'], $payload['mobile'], (int) $id);
			if ($dupes)
			{
				$list = array();
				foreach ($dupes as $d)
				{
					$list[] = array('id' => (int) $d->id, 'title' => $d->title, 'email' => $d->email);
				}
				return $this->json_response(FALSE, 'Possible duplicate leads found.', array(
					'has_duplicates' => TRUE,
					'duplicates'     => $list,
				), 409);
			}
		}

		$payload['updated_by'] = (int) current_user_id();
		$payload['updated_at'] = date('Y-m-d H:i:s');
		$tag_ids = $this->_parse_tag_ids();
		$custom  = $this->input->post('custom_fields');

		$old_status = (int) $lead->lead_status_id;
		$old_assignee = (int) $lead->assigned_to;

		$this->Lead_model->update((int) $id, $payload);
		$this->Lead_model->sync_tags((int) $id, $tag_ids);
		if (is_array($custom))
		{
			$this->Lead_model->save_custom_values((int) $id, $custom);
		}

		$this->lead_lib->timeline($id, 'updated', 'Lead Updated', $payload['title'] . ' was updated');
		$this->activity_lib->log('update', 'Updated lead ' . $payload['title'], 'leads', (int) $id);

		if ($old_status !== (int) $payload['lead_status_id'])
		{
			$this->lead_lib->timeline($id, 'status_changed', 'Status Changed', 'Status updated');
			if ($old_assignee)
			{
				$this->lead_lib->notify_assignee($old_assignee, 'Lead status changed', $payload['title'] . ' status was updated', $id);
			}
		}

		if ($old_assignee !== (int) $payload['assigned_to'])
		{
			$this->lead_lib->timeline($id, 'assigned', 'Assigned User Changed', 'Lead reassigned');
			if ( ! empty($payload['assigned_to']))
			{
				$this->lead_lib->notify_assignee($payload['assigned_to'], 'Lead assigned', 'You were assigned to ' . $payload['title'], $id);
			}
		}
		else if ($old_assignee)
		{
			$this->lead_lib->notify_assignee($old_assignee, 'Lead updated', $payload['title'] . ' was updated', $id);
		}

		return $this->json_response(TRUE, 'Lead updated successfully.');
	}

	public function delete($id = 0)
	{
		$this->permission_lib->require('leads.delete');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$this->Lead_model->soft_delete((int) $id);
		$this->lead_lib->timeline($id, 'deleted', 'Lead Deleted', 'Moved to trash');
		$this->activity_lib->log('delete', 'Soft-deleted lead ' . $lead->title, 'leads', (int) $id);
		return $this->json_response(TRUE, 'Lead moved to trash.');
	}

	public function restore($id = 0)
	{
		$this->permission_lib->require('leads.delete');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$lead = $this->Lead_model->with_trashed()->get_full((int) $id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$this->Lead_model->restore((int) $id);
		$this->lead_lib->timeline($id, 'restored', 'Lead Restored', 'Restored from trash');
		$this->activity_lib->log('update', 'Restored lead ' . $lead->title, 'leads', (int) $id);
		return $this->json_response(TRUE, 'Lead restored.');
	}

	public function force_delete($id = 0)
	{
		if ( ! $this->permission_lib->is_owner())
		{
			return $this->json_response(FALSE, 'Only the owner can permanently delete leads.', array(), 403);
		}
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$lead = $this->Lead_model->with_trashed()->get_full((int) $id);
		if ( ! $lead || (int) $lead->organization_id !== (int) current_org_id())
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}

		$this->db->where('lead_id', (int) $id)->delete('lead_tag_map');
		$this->db->where('lead_id', (int) $id)->delete('lead_notes');
		$this->db->where('lead_id', (int) $id)->delete('lead_attachments');
		$this->db->where('lead_id', (int) $id)->delete('lead_timeline');
		$this->db->where('lead_id', (int) $id)->delete('lead_custom_values');
		$this->Lead_model->force_delete((int) $id);

		$this->activity_lib->log('delete', 'Permanently deleted lead ' . $lead->title, 'leads', (int) $id);
		return $this->json_response(TRUE, 'Lead permanently deleted.');
	}

	/* -----------------------------------------------------------------
	 * Quick updates / bulk
	 * ----------------------------------------------------------------- */

	public function change_status($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! empty($lead->deleted_at) || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$status_id = (int) $this->input->post('status_id');
		if ( ! $status_id)
		{
			return $this->json_response(FALSE, 'Status is required.');
		}
		$this->Lead_model->update((int) $id, array(
			'lead_status_id' => $status_id,
			'updated_by'     => (int) current_user_id(),
		));
		$this->lead_lib->timeline($id, 'status_changed', 'Status Changed', 'Kanban / quick status update');
		if ($lead->assigned_to)
		{
			$this->lead_lib->notify_assignee($lead->assigned_to, 'Lead status changed', $lead->title . ' status was updated', $id);
		}
		return $this->json_response(TRUE, 'Status updated.');
	}

	public function change_stage($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! empty($lead->deleted_at) || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$stage_id = (int) $this->input->post('stage_id');
		$pipeline_id = (int) $this->input->post('pipeline_id');
		$data = array(
			'stage_id'   => $stage_id ?: NULL,
			'updated_by' => (int) current_user_id(),
		);
		if ($pipeline_id)
		{
			$data['pipeline_id'] = $pipeline_id;
		}
		$this->Lead_model->update((int) $id, $data);
		$this->lead_lib->timeline($id, 'stage_changed', 'Stage Changed', 'Pipeline stage updated');
		return $this->json_response(TRUE, 'Stage updated.');
	}

	public function assign($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$lead = $this->Lead_model->get_full((int) $id);
		if ( ! $lead || ! empty($lead->deleted_at) || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$user_id = (int) $this->input->post('assigned_to');
		if ($user_id && ! $this->_user_in_org($user_id))
		{
			return $this->json_response(FALSE, 'Assignee must belong to your organization.');
		}
		$this->Lead_model->update((int) $id, array(
			'assigned_to' => $user_id ?: NULL,
			'updated_by'  => (int) current_user_id(),
		));
		$this->lead_lib->timeline($id, 'assigned', 'Lead Assigned', $user_id ? 'Assigned to user #' . $user_id : 'Unassigned');
		if ($user_id)
		{
			$this->lead_lib->notify_assignee($user_id, 'Lead assigned', 'You were assigned to ' . $lead->title, $id);
		}
		return $this->json_response(TRUE, 'Assignment updated.');
	}

	public function bulk()
	{
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$action = $this->input->post('action', TRUE);
		$ids = $this->input->post('ids');
		if ( ! is_array($ids) || empty($ids))
		{
			return $this->json_response(FALSE, 'No leads selected.');
		}
		$ids = array_map('intval', $ids);
		$count = 0;

		foreach ($ids as $id)
		{
			$lead = $this->Lead_model->get_full($id);
			if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
			{
				continue;
			}

			switch ($action)
			{
				case 'delete':
					if ( ! $this->permission_lib->can('leads.delete') || ! empty($lead->deleted_at)) break;
					$this->Lead_model->soft_delete($id);
					$count++;
					break;
				case 'assign':
					if ( ! $this->permission_lib->can('leads.edit') || ! empty($lead->deleted_at)) break;
					$uid = (int) $this->input->post('assigned_to');
					if ($uid && ! $this->_user_in_org($uid)) break;
					$this->Lead_model->update($id, array('assigned_to' => $uid ?: NULL, 'updated_by' => (int) current_user_id()));
					if ($uid) $this->lead_lib->notify_assignee($uid, 'Lead assigned', 'You were assigned to ' . $lead->title, $id);
					$count++;
					break;
				case 'status':
					if ( ! $this->permission_lib->can('leads.edit') || ! empty($lead->deleted_at)) break;
					$sid = (int) $this->input->post('status_id');
					if ( ! $sid) break;
					$this->Lead_model->update($id, array('lead_status_id' => $sid, 'updated_by' => (int) current_user_id()));
					$count++;
					break;
				case 'pipeline':
					if ( ! $this->permission_lib->can('leads.edit') || ! empty($lead->deleted_at)) break;
					$pid = (int) $this->input->post('pipeline_id');
					$st  = (int) $this->input->post('stage_id');
					if ( ! $pid) break;
					$data = array('pipeline_id' => $pid, 'updated_by' => (int) current_user_id());
					if ($st) $data['stage_id'] = $st;
					elseif ($pid) $data['stage_id'] = $this->Crm_lookup_model->first_stage_id($pid);
					$this->Lead_model->update($id, $data);
					$count++;
					break;
				case 'stage':
					if ( ! $this->permission_lib->can('leads.edit') || ! empty($lead->deleted_at)) break;
					$st = (int) $this->input->post('stage_id');
					if ( ! $st) break;
					$this->Lead_model->update($id, array('stage_id' => $st, 'updated_by' => (int) current_user_id()));
					$count++;
					break;
			}
		}

		$this->activity_lib->log('update', 'Bulk ' . $action . ' on ' . $count . ' leads', 'leads');
		return $this->json_response(TRUE, 'Bulk action applied to ' . $count . ' lead(s).', array('count' => $count));
	}

	/* -----------------------------------------------------------------
	 * Notes / attachments / timeline
	 * ----------------------------------------------------------------- */

	public function notes($lead_id = 0)
	{
		$this->permission_lib->require('leads.view');
		$lead = $this->Lead_model->get_full((int) $lead_id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$rows = $this->Lead_note_model->for_lead((int) $lead_id);
		$out = array();
		foreach ($rows as $n)
		{
			$out[] = array(
				'id'         => (int) $n->id,
				'body'       => $n->body,
				'is_pinned'  => (int) $n->is_pinned,
				'user_name'  => $n->user_name,
				'user_id'    => (int) $n->user_id,
				'created_at' => $n->created_at,
				'updated_at' => $n->updated_at,
			);
		}
		return $this->json_response(TRUE, 'OK', array('notes' => $out));
	}

	public function note_store($lead_id = 0)
	{
		$this->permission_lib->require('leads.edit');
		$lead = $this->Lead_model->get_full((int) $lead_id);
		if ( ! $lead || ! empty($lead->deleted_at) || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$body = $this->input->post('body', FALSE);
		if ( ! trim(strip_tags((string) $body)))
		{
			return $this->json_response(FALSE, 'Note cannot be empty.');
		}
		$id = $this->Lead_note_model->insert(array(
			'organization_id' => (int) current_org_id(),
			'lead_id'         => (int) $lead_id,
			'user_id'         => (int) current_user_id(),
			'body'            => $body,
			'is_pinned'       => (int) $this->input->post('is_pinned') ? 1 : 0,
			'created_at'      => date('Y-m-d H:i:s'),
		));
		$this->lead_lib->timeline($lead_id, 'note_added', 'Note Added', 'A note was added');
		return $this->json_response(TRUE, 'Note added.', array('id' => (int) $id));
	}

	public function note_update($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		$note = $this->Lead_note_model->get((int) $id);
		if ( ! $note || ! empty($note->deleted_at))
		{
			return $this->json_response(FALSE, 'Note not found.', array(), 404);
		}
		$lead = $this->Lead_model->get_full((int) $note->lead_id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$body = $this->input->post('body', FALSE);
		$data = array('updated_at' => date('Y-m-d H:i:s'));
		if ($body !== NULL)
		{
			$data['body'] = $body;
		}
		if ($this->input->post('is_pinned') !== NULL)
		{
			$data['is_pinned'] = (int) $this->input->post('is_pinned') ? 1 : 0;
		}
		$this->Lead_note_model->update((int) $id, $data);
		return $this->json_response(TRUE, 'Note updated.');
	}

	public function note_delete($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		$note = $this->Lead_note_model->get((int) $id);
		if ( ! $note)
		{
			return $this->json_response(FALSE, 'Note not found.', array(), 404);
		}
		$lead = $this->Lead_model->get_full((int) $note->lead_id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$this->Lead_note_model->soft_delete((int) $id);
		return $this->json_response(TRUE, 'Note deleted.');
	}

	public function attachments($lead_id = 0)
	{
		$this->permission_lib->require('leads.view');
		$lead = $this->Lead_model->get_full((int) $lead_id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$rows = $this->Lead_attachment_model->for_lead((int) $lead_id);
		$out = array();
		foreach ($rows as $a)
		{
			$out[] = array(
				'id'            => (int) $a->id,
				'original_name' => $a->original_name,
				'file_path'     => base_url($a->file_path),
				'file_type'     => $a->file_type,
				'file_size'     => (int) $a->file_size,
				'user_name'     => $a->user_name,
				'created_at'    => $a->created_at,
				'previewable'   => $this->_is_previewable($a->file_type, $a->original_name),
			);
		}
		return $this->json_response(TRUE, 'OK', array('attachments' => $out));
	}

	public function attachment_upload($lead_id = 0)
	{
		$this->permission_lib->require('leads.edit');
		$lead = $this->Lead_model->get_full((int) $lead_id);
		if ( ! $lead || ! empty($lead->deleted_at) || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}

		$result = $this->mino_upload->image('file', 'leads/' . (int) current_org_id(), array(
			'allowed_types' => 'jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|csv|zip|txt',
			'max_size'      => 10240,
		));
		if ( ! $result['success'])
		{
			return $this->json_response(FALSE, $result['message']);
		}

		$full = $result['full'];
		$id = $this->Lead_attachment_model->insert(array(
			'organization_id' => (int) current_org_id(),
			'lead_id'         => (int) $lead_id,
			'user_id'         => (int) current_user_id(),
			'original_name'   => isset($_FILES['file']['name']) ? $_FILES['file']['name'] : $result['filename'],
			'file_path'       => $result['path'],
			'file_type'       => is_file($full) ? mime_content_type($full) : '',
			'file_size'       => is_file($full) ? filesize($full) : 0,
			'created_at'      => date('Y-m-d H:i:s'),
		));
		$this->lead_lib->timeline($lead_id, 'attachment', 'Attachment Uploaded', basename($result['path']));
		return $this->json_response(TRUE, 'File uploaded.', array('id' => (int) $id));
	}

	public function attachment_delete($id = 0)
	{
		$this->permission_lib->require('leads.edit');
		$att = $this->Lead_attachment_model->get((int) $id);
		if ( ! $att)
		{
			return $this->json_response(FALSE, 'Attachment not found.', array(), 404);
		}
		$lead = $this->Lead_model->get_full((int) $att->lead_id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$this->Lead_attachment_model->soft_delete((int) $id);
		return $this->json_response(TRUE, 'Attachment deleted.');
	}

	public function timeline($lead_id = 0)
	{
		$this->permission_lib->require('leads.view');
		$lead = $this->Lead_model->get_full((int) $lead_id);
		if ( ! $lead || ! $this->lead_lib->can_access_lead($lead))
		{
			return $this->json_response(FALSE, 'Lead not found.', array(), 404);
		}
		$rows = $this->Lead_timeline_model->for_lead((int) $lead_id);
		$out = array();
		foreach ($rows as $t)
		{
			$out[] = array(
				'id'          => (int) $t->id,
				'event_type'  => $t->event_type,
				'title'       => $t->title,
				'description' => $t->description,
				'user_name'   => $t->user_name ?: 'System',
				'created_at'  => $t->created_at,
			);
		}
		return $this->json_response(TRUE, 'OK', array('timeline' => $out));
	}

	/* -----------------------------------------------------------------
	 * Saved filters / import / export
	 * ----------------------------------------------------------------- */

	public function saved_filters()
	{
		$this->permission_lib->require('leads.view');
		$rows = $this->Lead_saved_filter_model->for_user((int) current_user_id());
		$out = array();
		foreach ($rows as $r)
		{
			$out[] = array(
				'id'      => (int) $r->id,
				'name'    => $r->name,
				'filters' => json_decode($r->filters_json, TRUE),
				'is_shared' => (int) $r->is_shared,
			);
		}
		return $this->json_response(TRUE, 'OK', array('filters' => $out));
	}

	public function save_filter()
	{
		$this->permission_lib->require('leads.view');
		$name = trim((string) $this->input->post('name', TRUE));
		$filters = $this->input->post('filters');
		if ($name === '')
		{
			return $this->json_response(FALSE, 'Filter name is required.');
		}
		if (is_string($filters))
		{
			$filters = json_decode($filters, TRUE);
		}
		$id = $this->Lead_saved_filter_model->insert(array(
			'organization_id' => (int) current_org_id(),
			'user_id'         => (int) current_user_id(),
			'name'            => $name,
			'filters_json'    => json_encode($filters ?: array()),
			'is_shared'       => (int) $this->input->post('is_shared') ? 1 : 0,
			'created_at'      => date('Y-m-d H:i:s'),
		));
		return $this->json_response(TRUE, 'Filter saved.', array('id' => (int) $id));
	}

	public function delete_filter($id = 0)
	{
		$this->permission_lib->require('leads.view');
		$f = $this->Lead_saved_filter_model->get((int) $id);
		if ( ! $f || ((int) $f->user_id !== (int) current_user_id() && ! $this->permission_lib->is_owner()))
		{
			return $this->json_response(FALSE, 'Filter not found.', array(), 404);
		}
		$this->Lead_saved_filter_model->delete((int) $id);
		return $this->json_response(TRUE, 'Filter deleted.');
	}

	public function export()
	{
		$this->permission_lib->require('leads.export');
		$filters = $this->_filters_from_input();
		$rows = $this->Lead_model->datatable($filters);
		$format = $this->input->get('format') ?: 'csv';

		$filename = 'leads_export_' . date('Ymd_His');
		$header = array('ID','Title','First Name','Last Name','Company','Email','Phone','Mobile','Website','City','State','Country','Status','Source','Pipeline','Stage','Assignee','Priority','Estimated Value','Expected Close','Created At');

		$lines = array();
		$lines[] = $header;
		foreach ($rows as $r)
		{
			$lines[] = array(
				$r->id, $r->title, $r->first_name, $r->last_name, $r->company_name, $r->email, $r->phone, $r->mobile,
				$r->website, $r->city, $r->state, $r->country, $r->status_name, $r->source_name, $r->pipeline_name,
				$r->stage_name, $r->assignee_name, $r->priority_name, $r->estimated_value, $r->expected_close_date, $r->created_at,
			);
		}

		$this->activity_lib->log('export', 'Exported ' . count($rows) . ' leads', 'leads');

		if ($format === 'excel')
		{
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
			header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
			echo "<table border='1'>";
			foreach ($lines as $i => $line)
			{
				echo '<tr>';
				foreach ($line as $cell)
				{
					$tag = $i === 0 ? 'th' : 'td';
					echo '<' . $tag . '>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . '</' . $tag . '>';
				}
				echo '</tr>';
			}
			echo '</table>';
			exit;
		}

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
		$out = fopen('php://output', 'w');
		fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
		foreach ($lines as $line)
		{
			fputcsv($out, $line);
		}
		fclose($out);
		exit;
	}

	public function import_preview()
	{
		$this->permission_lib->require('leads.import');
		if (empty($_FILES['file']['tmp_name']))
		{
			return $this->json_response(FALSE, 'Please upload a CSV file.');
		}
		$path = $_FILES['file']['tmp_name'];
		$handle = fopen($path, 'r');
		if ( ! $handle)
		{
			return $this->json_response(FALSE, 'Unable to read file.');
		}
		$header = fgetcsv($handle);
		if ( ! $header)
		{
			fclose($handle);
			return $this->json_response(FALSE, 'Empty file.');
		}
		$header = array_map(function ($h) { return trim($h); }, $header);
		$rows = array();
		$i = 0;
		while (($row = fgetcsv($handle)) !== FALSE && $i < 50)
		{
			$assoc = array();
			foreach ($header as $idx => $col)
			{
				$assoc[$col] = isset($row[$idx]) ? trim($row[$idx]) : '';
			}
			$rows[] = $assoc;
			$i++;
		}
		fclose($handle);

		$mapping = array(
			'Title' => 'title', 'First Name' => 'first_name', 'Last Name' => 'last_name',
			'Company' => 'company_name', 'Email' => 'email', 'Phone' => 'phone', 'Mobile' => 'mobile',
			'Website' => 'website', 'City' => 'city', 'Status' => 'status', 'Source' => 'source',
		);

		return $this->json_response(TRUE, 'OK', array(
			'headers' => $header,
			'preview' => $rows,
			'mapping' => $mapping,
			'total_preview' => count($rows),
		));
	}

	public function import_run()
	{
		$this->permission_lib->require('leads.import');
		if ($this->input->method() !== 'post')
		{
			return $this->json_response(FALSE, 'Invalid method.', array(), 405);
		}
		$raw = $this->input->post('rows');
		$dup_mode = $this->input->post('duplicate_mode') ?: 'skip'; // skip|update|create
		if (is_string($raw))
		{
			$raw = json_decode($raw, TRUE);
		}
		if ( ! is_array($raw) || empty($raw))
		{
			return $this->json_response(FALSE, 'No rows to import.');
		}

		$created = 0;
		$skipped = 0;
		$updated = 0;
		$defaults = $this->_default_ids();

		foreach ($raw as $row)
		{
			$title = isset($row['title']) ? trim($row['title']) : '';
			if ($title === '' && ! empty($row['company_name']))
			{
				$title = $row['company_name'];
			}
			if ($title === '')
			{
				$skipped++;
				continue;
			}

			$email  = isset($row['email']) ? trim($row['email']) : '';
			$phone  = isset($row['phone']) ? trim($row['phone']) : '';
			$mobile = isset($row['mobile']) ? trim($row['mobile']) : '';
			$dupes = $this->lead_lib->find_duplicates($email, $phone, $mobile);

			$status_id = $defaults['status_id'];
			if ( ! empty($row['status']))
			{
				$st = $this->Crm_lookup_model->status_by_name($row['status']);
				if ($st) $status_id = (int) $st->id;
			}
			$source_id = NULL;
			if ( ! empty($row['source']))
			{
				$src = $this->Crm_lookup_model->source_by_name($row['source']);
				if ($src) $source_id = (int) $src->id;
			}

			$payload = array(
				'title'         => $title,
				'first_name'    => isset($row['first_name']) ? $row['first_name'] : NULL,
				'last_name'     => isset($row['last_name']) ? $row['last_name'] : NULL,
				'company_name'  => isset($row['company_name']) ? $row['company_name'] : NULL,
				'email'         => $email ?: NULL,
				'phone'         => $phone ?: NULL,
				'mobile'        => $mobile ?: NULL,
				'website'       => isset($row['website']) ? $row['website'] : NULL,
				'city'          => isset($row['city']) ? $row['city'] : NULL,
				'lead_status_id'=> $status_id,
				'lead_source_id'=> $source_id,
				'pipeline_id'   => $defaults['pipeline_id'],
				'stage_id'      => $defaults['stage_id'],
				'updated_by'    => (int) current_user_id(),
			);

			if ($dupes)
			{
				if ($dup_mode === 'skip')
				{
					$skipped++;
					continue;
				}
				if ($dup_mode === 'update')
				{
					$this->Lead_model->update((int) $dupes[0]->id, $payload);
					$updated++;
					continue;
				}
			}

			$payload['organization_id'] = (int) current_org_id();
			$payload['created_by'] = (int) current_user_id();
			$payload['created_at'] = date('Y-m-d H:i:s');
			$payload['assigned_to'] = (int) current_user_id();
			$id = $this->Lead_model->insert($payload);
			if ($id)
			{
				$this->lead_lib->timeline($id, 'created', 'Lead Created', 'Imported via CSV');
				$created++;
			}
			else
			{
				$skipped++;
			}
		}

		$this->activity_lib->log('import', "Imported leads: {$created} created, {$updated} updated, {$skipped} skipped", 'leads');
		return $this->json_response(TRUE, 'Import complete.', array(
			'created' => $created,
			'updated' => $updated,
			'skipped' => $skipped,
		));
	}

	public function stages_by_pipeline($pipeline_id = 0)
	{
		$this->permission_lib->require('leads.view');
		$stages = $this->Crm_lookup_model->stages((int) $pipeline_id);
		$out = array();
		foreach ($stages as $s)
		{
			$out[] = array('id' => (int) $s->id, 'name' => $s->name, 'color' => $s->color);
		}
		return $this->json_response(TRUE, 'OK', array('stages' => $out));
	}

	/* -----------------------------------------------------------------
	 * Internals
	 * ----------------------------------------------------------------- */

	protected function _urls()
	{
		return array(
			'list'           => site_url('leads/datatable'),
			'kanban'         => site_url('leads/kanban_data'),
			'get'            => site_url('leads/get'),
			'store'          => site_url('leads/store'),
			'update'         => site_url('leads/update'),
			'delete'         => site_url('leads/delete'),
			'restore'        => site_url('leads/restore'),
			'force_delete'   => site_url('leads/force_delete'),
			'change_status'  => site_url('leads/change_status'),
			'change_stage'   => site_url('leads/change_stage'),
			'assign'         => site_url('leads/assign'),
			'bulk'           => site_url('leads/bulk'),
			'check_dup'      => site_url('leads/check_duplicate'),
			'notes'          => site_url('leads/notes'),
			'note_store'     => site_url('leads/note_store'),
			'note_update'    => site_url('leads/note_update'),
			'note_delete'    => site_url('leads/note_delete'),
			'attachments'    => site_url('leads/attachments'),
			'attach_upload'  => site_url('leads/attachment_upload'),
			'attach_delete'  => site_url('leads/attachment_delete'),
			'timeline'       => site_url('leads/timeline'),
			'export'         => site_url('leads/export'),
			'import_preview' => site_url('leads/import_preview'),
			'import_run'     => site_url('leads/import_run'),
			'saved_filters'  => site_url('leads/saved_filters'),
			'save_filter'    => site_url('leads/save_filter'),
			'delete_filter'  => site_url('leads/delete_filter'),
			'stages'         => site_url('leads/stages_by_pipeline'),
			'profile'        => site_url('leads/profile'),
			'list_page'      => site_url('leads'),
			'kanban_page'    => site_url('leads/kanban'),
		);
	}

	protected function _page_actions_html()
	{
		$html = '<a href="' . site_url('leads/kanban') . '" class="btn btn-secondary btn-sm"><i class="fas fa-columns"></i> Kanban</a>';
		if ($this->permission_lib->can('leads.import'))
		{
			$html .= ' <button type="button" class="btn btn-secondary btn-sm" id="btnImportLeads"><i class="fas fa-file-import"></i> Import</button>';
		}
		if ($this->permission_lib->can('leads.export'))
		{
			$html .= ' <div class="btn-group"><button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-file-export"></i> Export</button>
				<ul class="dropdown-menu dropdown-menu-end">
					<li><a class="dropdown-item" href="#" id="btnExportCsv">CSV</a></li>
					<li><a class="dropdown-item" href="#" id="btnExportExcel">Excel</a></li>
				</ul></div>';
		}
		if ($this->permission_lib->can('leads.create'))
		{
			$html .= ' <button type="button" class="btn btn-primary btn-sm" id="btnAddLead"><i class="fas fa-plus"></i> Add Lead</button>';
		}
		return $html;
	}

	protected function _profile_actions_html($lead)
	{
		$html = '<a href="' . site_url('leads') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>';
		if ($this->permission_lib->can('leads.edit') && empty($lead->deleted_at))
		{
			$html .= ' <button type="button" class="btn btn-primary btn-sm" id="btnEditLeadProfile" data-id="' . (int) $lead->id . '"><i class="fas fa-pen"></i> Edit</button>';
		}
		return $html;
	}

	protected function _lookups_payload($json = FALSE)
	{
		$map = function ($rows) {
			$out = array();
			foreach ($rows as $r)
			{
				$item = array(
					'id'    => (int) $r->id,
					'name'  => $r->name,
				);
				if (isset($r->slug)) $item['slug'] = $r->slug;
				if (isset($r->color)) $item['color'] = $r->color;
				if (isset($r->icon)) $item['icon'] = $r->icon;
				if (isset($r->pipeline_id)) $item['pipeline_id'] = (int) $r->pipeline_id;
				$out[] = $item;
			}
			return $out;
		};

		$data = array(
			'statuses'   => $json ? $map($this->Crm_lookup_model->statuses()) : $this->Crm_lookup_model->statuses(),
			'sources'    => $json ? $map($this->Crm_lookup_model->sources()) : $this->Crm_lookup_model->sources(),
			'tags'       => $json ? $map($this->Crm_lookup_model->tags()) : $this->Crm_lookup_model->tags(),
			'pipelines'  => $json ? $map($this->Crm_lookup_model->pipelines()) : $this->Crm_lookup_model->pipelines(),
			'stages'     => $json ? $map($this->Crm_lookup_model->stages()) : $this->Crm_lookup_model->stages(),
			'priorities' => $json ? $map($this->Crm_lookup_model->priorities()) : $this->Crm_lookup_model->priorities(),
			'users'      => $json ? $map($this->Crm_lookup_model->assignable_users()) : $this->Crm_lookup_model->assignable_users(),
			'custom_fields' => $json ? $map($this->Crm_lookup_model->custom_fields('leads')) : $this->Crm_lookup_model->custom_fields('leads'),
		);
		return $data;
	}

	protected function _filters_from_input()
	{
		return array(
			'search'      => $this->input->get_post('search', TRUE),
			'status_id'   => $this->input->get_post('status_id', TRUE),
			'source_id'   => $this->input->get_post('source_id', TRUE),
			'assigned_to' => $this->input->get_post('assigned_to', TRUE),
			'pipeline_id' => $this->input->get_post('pipeline_id', TRUE),
			'stage_id'    => $this->input->get_post('stage_id', TRUE),
			'priority_id' => $this->input->get_post('priority_id', TRUE),
			'tag_id'      => $this->input->get_post('tag_id', TRUE),
			'date_from'   => $this->input->get_post('date_from', TRUE),
			'date_to'     => $this->input->get_post('date_to', TRUE),
			'trashed'     => $this->input->get_post('trashed', TRUE),
		);
	}

	protected function _validated_payload()
	{
		$this->form_validation->set_rules('title', 'Lead Title', 'required|trim|max_length[200]');
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|max_length[100]');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|max_length[100]');
		$this->form_validation->set_rules('company_name', 'Company', 'trim|max_length[150]');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');
		$this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[50]');
		$this->form_validation->set_rules('website', 'Website', 'trim|max_length[255]');
		$this->form_validation->set_rules('address', 'Address', 'trim|max_length[255]');
		$this->form_validation->set_rules('city', 'City', 'trim|max_length[100]');
		$this->form_validation->set_rules('state', 'State', 'trim|max_length[100]');
		$this->form_validation->set_rules('country', 'Country', 'trim|max_length[100]');
		$this->form_validation->set_rules('postal_code', 'Postal Code', 'trim|max_length[30]');
		$this->form_validation->set_rules('lead_source_id', 'Source', 'integer');
		$this->form_validation->set_rules('lead_status_id', 'Status', 'integer');
		$this->form_validation->set_rules('pipeline_id', 'Pipeline', 'integer');
		$this->form_validation->set_rules('stage_id', 'Stage', 'integer');
		$this->form_validation->set_rules('assigned_to', 'Assignee', 'integer');
		$this->form_validation->set_rules('priority_id', 'Priority', 'integer');
		$this->form_validation->set_rules('estimated_value', 'Estimated Value', 'trim');
		$this->form_validation->set_rules('expected_close_date', 'Expected Close', 'trim');
		$this->form_validation->set_rules('description', 'Description', 'trim');

		if ($this->form_validation->run() === FALSE)
		{
			return FALSE;
		}

		$defaults = $this->_default_ids();
		$status_id = (int) $this->input->post('lead_status_id') ?: $defaults['status_id'];
		$pipeline_id = (int) $this->input->post('pipeline_id') ?: $defaults['pipeline_id'];
		$stage_id = (int) $this->input->post('stage_id') ?: $defaults['stage_id'];
		$assigned = (int) $this->input->post('assigned_to');
		if ($assigned && ! $this->_user_in_org($assigned))
		{
			$this->_lead_validation_error = 'Assignee must belong to your organization.';
			return FALSE;
		}

		$est = $this->input->post('estimated_value', TRUE);
		$close = $this->input->post('expected_close_date', TRUE);

		return array(
			'title'               => $this->input->post('title', TRUE),
			'first_name'          => $this->input->post('first_name', TRUE) ?: NULL,
			'last_name'           => $this->input->post('last_name', TRUE) ?: NULL,
			'company_name'        => $this->input->post('company_name', TRUE) ?: NULL,
			'email'               => $this->input->post('email', TRUE) ?: NULL,
			'phone'               => $this->input->post('phone', TRUE) ?: NULL,
			'mobile'              => $this->input->post('mobile', TRUE) ?: NULL,
			'website'             => $this->input->post('website', TRUE) ?: NULL,
			'address'             => $this->input->post('address', TRUE) ?: NULL,
			'city'                => $this->input->post('city', TRUE) ?: NULL,
			'state'               => $this->input->post('state', TRUE) ?: NULL,
			'country'             => $this->input->post('country', TRUE) ?: NULL,
			'postal_code'         => $this->input->post('postal_code', TRUE) ?: NULL,
			'lead_source_id'      => (int) $this->input->post('lead_source_id') ?: NULL,
			'lead_status_id'      => $status_id,
			'pipeline_id'         => $pipeline_id,
			'stage_id'            => $stage_id,
			'assigned_to'         => $assigned ?: NULL,
			'priority_id'         => (int) $this->input->post('priority_id') ?: NULL,
			'estimated_value'     => ($est !== '' && $est !== NULL) ? (float) $est : NULL,
			'expected_close_date' => $close ?: NULL,
			'description'         => $this->input->post('description', TRUE) ?: NULL,
		);
	}

	protected function _default_ids()
	{
		$status_id = $this->Crm_lookup_model->default_status_id();
		$pipe = $this->Crm_lookup_model->default_pipeline();
		$pipeline_id = $pipe ? (int) $pipe->id : NULL;
		$stage_id = $pipeline_id ? $this->Crm_lookup_model->first_stage_id($pipeline_id) : NULL;
		return compact('status_id', 'pipeline_id', 'stage_id');
	}

	protected function _parse_tag_ids()
	{
		$tags = $this->input->post('tag_ids');
		if (is_string($tags))
		{
			$tags = array_filter(array_map('trim', explode(',', $tags)));
		}
		if ( ! is_array($tags))
		{
			return array();
		}
		return array_map('intval', $tags);
	}

	protected function _user_in_org($user_id)
	{
		$row = $this->db->select('id')->from('users')
			->where('id', (int) $user_id)
			->where('organization_id', (int) current_org_id())
			->where('deleted_at IS NULL', NULL, FALSE)
			->where('status', 'active')
			->get()->row();
		return (bool) $row;
	}

	protected function _serialize_lead($r, $tags = array())
	{
		$name = trim(($r->first_name ?: '') . ' ' . ($r->last_name ?: ''));
		return array(
			'id'                  => (int) $r->id,
			'title'               => $r->title,
			'first_name'          => $r->first_name,
			'last_name'           => $r->last_name,
			'full_name'           => $name,
			'company_name'        => $r->company_name,
			'email'               => $r->email,
			'phone'               => $r->phone,
			'mobile'              => $r->mobile,
			'website'             => $r->website,
			'address'             => $r->address,
			'city'                => $r->city,
			'state'               => $r->state,
			'country'             => $r->country,
			'postal_code'         => $r->postal_code,
			'lead_source_id'      => (int) $r->lead_source_id,
			'lead_status_id'      => (int) $r->lead_status_id,
			'pipeline_id'         => (int) $r->pipeline_id,
			'stage_id'            => (int) $r->stage_id,
			'assigned_to'         => (int) $r->assigned_to,
			'priority_id'         => (int) $r->priority_id,
			'estimated_value'     => $r->estimated_value,
			'expected_close_date' => $r->expected_close_date,
			'description'         => $r->description,
			'status_name'         => $r->status_name,
			'status_color'        => $r->status_color,
			'source_name'         => $r->source_name,
			'source_color'        => $r->source_color,
			'pipeline_name'       => $r->pipeline_name,
			'stage_name'          => $r->stage_name,
			'stage_color'         => $r->stage_color,
			'priority_name'       => $r->priority_name,
			'priority_color'      => $r->priority_color,
			'assignee_name'       => $r->assignee_name,
			'assignee_email'      => isset($r->assignee_email) ? $r->assignee_email : NULL,
			'creator_name'        => isset($r->creator_name) ? $r->creator_name : NULL,
			'initials'            => user_initials($name ?: $r->title),
			'tags'                => $tags,
			'deleted_at'          => $r->deleted_at,
			'created_at'          => $r->created_at,
			'updated_at'          => $r->updated_at,
			'profile_url'         => site_url('leads/profile/' . (int) $r->id),
		);
	}

	protected function _is_previewable($mime, $name)
	{
		$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'), TRUE))
		{
			return TRUE;
		}
		return $mime && (strpos($mime, 'image/') === 0 || $mime === 'application/pdf');
	}
}
