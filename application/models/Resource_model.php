<?php

class resource_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

	function load_resources($resources){
		if(!is_array($resources)) return FALSE;
		
	
		if(count($resources) > 0){
			$sql = "SELECT * FROM resources WHERE resource_id IN (". implode($resources,',').")";
		}
		else{
			$sql = "SELECT * FROM resources WHERE 0=1"; 	//It's just easier this way. Always a query result with the empty set
		}
		return $this->db->query($sql);
	}
	
    function list_resources($can_filter = false){
		if($can_filter){
			$this->db->where('can_filter', true);
		}
		
		$this->db->order_by('name', 'asc');
		return $this->db->get('resources');
	}
	
	function list_room_resources($room_id){
		if(!is_numeric($room_id)) return false;
		
		$sql = "SELECT 
					r.resource_id, r.name, r.desc, r.can_filter
				FROM
					resources r,
					room_resource rr
				WHERE
					r.resource_id = rr.resource_id
					AND rr.room_id = $room_id";
		
		return $this->db->query($sql);
	}	
	
	function get_resource($resource_id){
		$this->db->where('resource_id', $resource_id);
		return $this->db->get('resources');
	}
	
	function edit_resource($id, $name, $desc, $filter){
		if($filter === 'on'){
			$filter = TRUE;
		}
		else{
			$filter = FALSE;
		}
		
		$data = array(
			'name' => $name,
			'desc' => $desc,
			'can_filter' => $filter	
		);
		
		$this->db->where('resource_id', $id); 
		$this->db->update('resources', $data); 
		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function add_room_resource($name, $desc, $filter){
		if($filter === 'on'){
			$filter = TRUE;
		}
		else{
			$filter = FALSE;
		}
		
		$data = array(
			'name' => $name,
			'desc' => $desc,
			'can_filter' => $filter	
		);
		
		$this->db->cache_delete_all();
		
		$this->db->insert('resources', $data); 
		
		return $this->db->insert_id();
	}
	
	function delete_resource($resource_id){
		$this->db->cache_delete_all();
		$this->db->where('resource_id', $resource_id);
		return $this->db->delete('resources');
	}

}
