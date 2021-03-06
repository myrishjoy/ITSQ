<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sadu_model extends CI_Model
{
	function __construct()
	{
		parent:: __construct();
	}

	function add_organization($data)
	{
		$this->db->insert('organizations',$data);
	}

	function get_Activity_Proposal($selected)
    {

		$query = $this->db->query("
		SELECT activity_proposals.*, organizations.*,proposal_status.*
		FROM activity_proposals
		INNER JOIN organizations
		INNER JOIN proposal_status
		ON activity_proposals.sent_by = organizations.org_id
		AND activity_proposals.scc_approve = proposal_status.id_status
		WHERE activity_proposals.sent_by = $selected 
		AND activity_proposals.sadu_status = 2
		OR activity_proposals.sadu_status = 1
		");

        return $query->result_array();
    }

	function Save_Prop_Status($data, $proposal_id)
	{
		$this->db->where('prop_id', $proposal_id);
		if($this->db->update('activity_proposals', $data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function Save_Prop_Comment($data, $comment)
	{
		$this->db->select('*');
        $this->db->from('comments');
        $this->db->where('comment', $comment['comment']);
		$query = $this->db->get();
		if($query->num_rows() == 0)
		{
			if($data['sadu_status'] == 2)
			{
				$this->db->insert('comments', $comment);

				$this->db->where('prop_id', $comment['actprop_id']);
				$this->db->update('activity_proposals', $data);
				
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function get_All_Proposal($selected)
	{
        $this->db->select('activity_proposals.*, organizations.*');
		$this->db->join('organizations', 'activity_proposals.sent_by = organizations.org_id');
        $this->db->from('activity_proposals');
        $this->db->where('activity_proposals.sent_by', $selected );
        
        $query = $this->db->get();
        return $query->result_array();
	}

	function get_All_Proposal_Comment($id)
	{
		$this->db->select('comments.*, users.*');
		$this->db->join('users', 'comments.author = users.id');
        $this->db->from('comments');
        $this->db->where('actprop_id', $id );
        
        $query = $this->db->get();
        return $query->result_array();
	}
	
	function upload_template($file_data)
	{
		$this->db->insert('proposal_template', $file_data);
	}

	function get_templates()
	{
		$this->db->select('*');
        $this->db->from('proposal_template');
        
        $query = $this->db->get();
        return $query->result_array();
	}

	function delete_temp_selected($template_selected)
	{
		for($i = 0 ; $i < sizeof($template_selected) ; $i++)
		{
			$this->db->where('template_id', $template_selected[$i]);
			$this->db->delete('proposal_template');
		}
	}
}
?>