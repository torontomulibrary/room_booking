<?php

class building_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

    function list_buildings(){
		return $this->db->get('buildings');
	}
	
	function get_by_external_id($external_id){
		$this->db->where('external_id', $external_id);
		return $this->db->get('buildings');
	}
	
	function load_building($building_id){
		if(!is_numeric($building_id)) return false;
		
		$this->db->where('building_id', $building_id);
		$data['building_data'] = $this->db->get('buildings');
		
		if($data['building_data']->num_rows() > 0){
			return $data;
		}
		else{
			return false;
		}
		
	
	}	
	
	function edit_building($building_id, $name, $ext_id){
		
		$data = array(
			'building_id' => $building_id,
			'name' => $name,
			'external_id' => $ext_id
		);
		
		$this->db->where('building_id', $building_id); 
		$this->db->update('buildings', $data); 
		
		$this->db->cache_delete_all();
		
		return TRUE;
	}
	
	function add_building($building_name, $ext_id = 0){
		
		$data = array(
			'name' => $building_name,
			'external_id' => $ext_id,
		);
		
		$this->db->insert('buildings', $data); 
		$id = $this->db->insert_id();

		$this->db->cache_delete_all();
		
		return $id;
	}
	
	function delete_building($building_id){
		$this->db->where('building_id', $building_id);
		$this->db->delete('buildings');
		
		$this->db->cache_delete_all();
	}

}
