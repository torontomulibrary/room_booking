<?php

class room_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

    function list_rooms($exclude_inactive = false){
		//Only show roles you are a member of, unless user is a super admin 
		if( !$this->session->userdata('super_admin')){
			$query = "select distinct r.room_id, r.max_daily_hours, r.building_id, r.name, r.seats, r.is_active, b.name as building, b.external_id
				from rooms r, buildings b, room_roles rr, roles ro
				where r.building_id = b.building_id
				and rr.room_id = r.room_id  
				and rr.role_id IN (";
			
			//Gather roles from session rather then database (since students etc.. are not whitelisted)
			$roles = array();
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$query .= implode(",", $roles);
				
			$query .= ")
				order by building asc, r.name asc";
			
			return $this->db->query($query);
		}
		else{
			return $this->db->query("
				SELECT DISTINCT r.room_id, r.max_daily_hours, r.building_id, r.name, r.seats, r.is_active, b.name AS building, b.external_id
				FROM rooms r, buildings b
				WHERE r.building_id = b.building_id
			");
		}
	}
	
	function list_rooms_by_role($role, $exclude_inactive = false){
		if(!is_numeric($role)) return false;
		
		$sql =  "select distinct r.room_id, r.max_daily_hours, r.building_id, r.name, r.seats, r.is_active, b.name as building, b.external_id
                from rooms r, buildings b, room_roles rr, roles ro
                where r.building_id = b.building_id
                and rr.room_id = r.room_id  
                and rr.role_id = $role";
			
		if($exclude_inactive) $sql .= " and r.is_active = true ";
		
        $sql.= "order by building asc, r.name asc ";
		
		return $this->db->query($sql);
	}
	
	function list_buildings(){
		return $this->db->get('buildings');
	}
	
	function load_room($room_id){
		$this->db->where('room_id', $room_id);
		$data['room_data'] = $this->db->get('rooms');
		
		$this->db->where('room_id', $room_id);
		$resources = $this->db->get('room_resource');
		
		$data['room_resources'] = array();
		
		foreach($resources->result() as $resource){
			$data['room_resources'][] = $resource->resource_id;
		}
		
		return $data;
	}	
	
	function add_room($building_id, $name, $seats, $roles, $active, $max_daily_hours, $resources, $notes){
		if($active === 'on'){
			$active = TRUE;
		}
		else{
			$active = FALSE;
		}
		
		$data = array(
			'building_id' => $building_id,
			'name' => $name,
			'seats' => $seats,
			'is_active' => $active,	
			'max_daily_hours' => $max_daily_hours,
			'notes' => $notes,
		);
		
		$this->db->insert('rooms', $data); 
		$id = $this->db->insert_id();
		
		$this->set_resources($id, $resources);
		$this->set_roles($id, $roles);
		
		$this->db->cache_delete_all();
		
		return $id;
	}
	
	function edit_room($room_id, $building_id, $name, $seats, $roles, $active, $resources, $max_daily_hours, $notes){
		if($active === 'on'){
			$active = TRUE;
		}
		else{
			$active = FALSE;
		}
		
		$data = array(
			'building_id' => $building_id,
			'name' => $name,
			'seats' => $seats,
			'is_active' => $active,	
			'max_daily_hours' => $max_daily_hours,	
			'notes' => $notes,	
		);
		
		$this->db->where('room_id', $room_id); 
		$this->db->update('rooms', $data); 
		
		$this->set_resources($room_id, $resources);
		$this->set_roles($room_id, $roles);
		
		$this->db->cache_delete_all();
		
		return TRUE;
	}
	
	function set_resources($room_id, $resources){
		if(!is_array($resources)) return FALSE;
		
		$this->db->where('room_id', $room_id);
		$this->db->delete('room_resource');
		
		foreach($resources as $resouce){
			$data = array(
				'room_id' => $room_id,
				'resource_id' => $resouce
			);
			
			$this->db->insert('room_resource', $data);
		}
		
		$this->db->cache_delete_all();
		
	}
	
	function set_roles($room_id, $roles){
		if(!is_array($roles)) return FALSE;
		
		$this->db->where('room_id', $room_id);
		$this->db->delete('room_roles');
		
		foreach($roles as $role){
			$data = array(
				'room_id' => $room_id,
				'role_id' => $role
			);
			
			$this->db->insert('room_roles', $data);
		}
		
		$this->db->cache_delete_all();
		
	}
	
	function delete_room($room_id){
		$this->db->where('room_id', $room_id);
		$this->db->delete('rooms');
		
		$this->db->cache_delete_all();
		
	}

}
