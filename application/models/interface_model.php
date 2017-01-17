<?php

class interface_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

	function get_all_form_components(){
		return $this->db->get('form_customization');
	}
	
	function get_field($fc_id){
		$this->db->where('fc_id', $fc_id);
		return $this->db->get('form_customization');
	}
	
	function get_fields($room_id){
		if(!is_numeric($room_id)) return FALSE;
		
		$sql = "SELECT DISTINCT fc.* FROM form_customization fc, form_customization_role fcr
				WHERE fc.fc_id = fcr.fc_id
				AND fcr.role_id IN (SELECT role_id FROM room_roles WHERE room_id = ".$room_id.")
				ORDER BY priority DESC";
				
		return $this->db->query($sql);
		
	}
	
	function get_field_roles($fc_id){
		if(!is_numeric($fc_id)) return FALSE;
		
		$sql = "SELECT fcr.fc_id, fcr.role_id, r.name FROM form_customization_role fcr, roles r
				WHERE fc_id = $fc_id
				and fcr.role_id = r.role_id";
		
		
		return $this->db->query($sql);
	}
	
	function add_field($name, $type, $data, $roles, $show_moderator, $priority){
		if(!is_array($data)) return FALSE;
		if(!is_array($roles)) return FALSE;
		
		$db_data = array(
			'field_name'	=>	$name,
			'field_type'	=>	$type,
			'data'			=>	json_encode($data),
			'show_moderator'=>	$show_moderator,
			'priority'		=>	$priority,
		);
		
		$this->db->insert('form_customization', $db_data);
		$insert_id = $this->db->insert_id();
		
		foreach($roles as $role){
			$role_data = array(
				'fc_id'		=> $insert_id,
				'role_id'	=> $role
			);
			
			$this->db->insert('form_customization_role', $role_data);
		}
		
		return $insert_id;
	}
	
	function edit_field($fc_id, $name, $type, $data, $roles, $show_moderator, $priority){
		if(!is_array($data)) return FALSE;
		if(!is_array($roles)) return FALSE;
		
		$db_data = array(
			'field_name'	=>	$name,
			'field_type'	=>	$type,
			'data'			=>	json_encode($data),
			'show_moderator'=>	$show_moderator,
			'priority'		=>	$priority,
		);
		
		$this->db->where('fc_id', $fc_id);
		$this->db->update('form_customization', $db_data);
		
		//Clear old roles first
		$this->db->where('fc_id', $fc_id);
		$this->db->delete('form_customization_role');
		
		//Insert updated roles
		foreach($roles as $role){
			$role_data = array(
				'fc_id'		=> $fc_id,
				'role_id'	=> $role
			);
			
			$this->db->insert('form_customization_role', $role_data);
		}
		
		return $fc_id;
	}
	
	function delete_field($fc_id){
		if(!is_numeric($fc_id)) return FALSE;
		
		$this->db->where('fc_id', $fc_id);
		$this->db->delete('form_customization');
	}
	
	function get_moderation_fields(){
		$this->load->model('role_model');
		
		$sql = "SELECT distinct fc.field_name, fcr.fc_id FROM form_customization_role fcr, form_customization fc WHERE 
				fcr.fc_id = fc.fc_id
				AND fc.show_moderator = 1		
				AND role_id IN ";
				
				//Gather roles from session rather then database (since students etc.. are not whitelisted)
				$roles = array();
				
				foreach($this->session->userdata('roles') as $role){
					if(is_numeric($role->role_id)) $roles[] = $role->role_id;
				}
				
				$sql .= "(".implode(",", $roles).")";
				
		return $this->db->query($sql);
	}
}
