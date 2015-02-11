<?php

class role_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

    function list_roles(){
		//Only show roles you are a member of, unless user is a super admin 
		if(!$this->session->userdata('super_admin')){
			$query = 	"SELECT * FROM roles WHERE 	role_id IN ";
			
			//Gather roles from session rather then database (since students etc.. are not whitelisted)
			$roles = array();
			
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$query .= "(".implode(",", $roles).") ORDER BY name ASC";
				
			return $this->db->query($query);
		}
		else{
			return $this->db->get('roles');
		}
	}
	
	function get_role($role_id){
		$this->db->where('role_id',$role_id);
		return $this->db->get('roles');
		
		
	}
	
	function get_room_roles($room_id){

		$this->db->select('rr.role_id, r.name')
          ->from('room_roles rr, roles r')
          ->where('r.role_id = rr.role_id')
		  ->where('rr.room_id = '.$room_id);
		
		return $this->db->get();		
	}
	
	function get_user_roles($user_id){
		$sql = "select ur.role_id, r.name, u.is_admin
					from user_roles ur, roles r, users u
					where r.role_id = ur.role_id
					and ur.user_id = ".$this->db->escape($user_id)."
					and u.user_id = ur.user_id";
					
					
		
		return $this->db->query($sql);		
	}
	
	function add_role($role_name, $bookings_day, $hours_week, $booking_window){
		$data = array(
			'name' => $role_name,
			'bookings_per_day' => $bookings_day,
			
			'hours_per_week' => $hours_week,
			'booking_window' => $booking_window,
		);
		
		$this->db->insert('roles', $data);
		
		return $this->db->insert_id();
	}
	
	function delete_role($role_id){
		$this->db->where('role_id', $role_id);
		$this->db->delete('roles');
		
		return TRUE;
	}
	
	function edit_role($role_id, $role_name, $bookings_day, $hours_week, $booking_window){
	
		$data = array(
			'name' => $role_name,
			'bookings_per_day' => $bookings_day,
		
			'hours_per_week' => $hours_week,
			'booking_window' => $booking_window,
		);
		
		$this->db->where('role_id', $role_id); 
		$this->db->update('roles', $data); 
		
		return TRUE;
	}


}
