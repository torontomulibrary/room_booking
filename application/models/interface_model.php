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
	
	function get_field_roles($fc_id){
		if(!is_numeric($fc_id)) return FALSE;
		
		$sql = "SELECT fcr.fc_id, fcr.role_id, r.name FROM form_customization_role fcr, roles r
				WHERE fc_id = $fc_id
				and fcr.role_id = r.role_id";
		
		
		return $this->db->query($sql);
	}
	
	function add_field($name, $type, $data, $roles){
		if(!is_array($data)) return FALSE;
		if(!is_array($roles)) return FALSE;
		
		$db_data = array(
			'field_name'	=>	$name,
			'field_type'	=>	$type,
			'data'			=>	json_encode($data)
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
	
	function delete_field($fc_id){
		if(!is_numeric($fc_id)) return FALSE;
		
		$this->db->where('fc_id', $fc_id);
		$this->db->delete('form_customization');
	}
}
