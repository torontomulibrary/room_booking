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
			$this->db->order_by('name');
			return $this->db->get('roles');
		}
	}
	
	function list_admin_roles(){
		if(!$this->session->userdata('super_admin')){
			$sql = "SELECT * FROM
					roles
				WHERE
					role_id IN (
						SELECT role_id FROM user_roles ur, users u
						WHERE
							ur.user_id = u.user_id
							AND u.matrix_id = ".$this->db->escape($this->session->userdata('username'))." 
							AND is_admin = 1
					)";
			
			return $this->db->query($sql);
		}
		else{
			$this->db->order_by('name');
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
	
	function add_role($role_name, $hours_week, $booking_window){
		$data = array(
			'name' => $role_name,
			'hours_per_week' => $hours_week,
			'booking_window' => $booking_window,
		);
		
		$this->db->insert('roles', $data);
		
		$insert_id = $this->db->insert_id();
		
		$data = array(
					'role_id' => $insert_id,
					'can_block_book' => 0
				);
				
		$this->db->insert('permissions', $data);
		
		return $insert_id;
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
	
	function get_permissions(){
		return $this->db->get('permissions');
	}
	
	function get_permission($role_id){
		$this->db->where('role_id', $role_id);
		return $this->db->get('permissions');
	}
	
	function set_permissions($role_id, $can_block_book){
		
		if($can_block_book == 'on') $can_block_book = 1;
		else $can_block_book = 0;
		
		$data = array('can_block_book' => $can_block_book);
		
		$this->db->where('role_id', $role_id);
		
		$this->db->update('permissions', $data);
	}
	
	function load_permissions($matrix_id){
		$data = array();
		
		if($this->session->userdata('super_admin')){
			$data['can_block_book'] = true;
		}
		else{
		
			$sql = "SELECT u.matrix_id, u.user_id, u.is_admin, u.name, ur.role_id FROM users u, user_roles ur WHERE u.user_id = ur.user_id AND u.matrix_id = ". $this->db->escape($this->session->userdata('username'));
		
			$admin_roles = $this->db->query($sql);
			
			$data['can_block_book'] = false;
			
			foreach($admin_roles->result() as $role){
				$this->db->where('role_id', $role->role_id);
				$permissions = $this->db->get('permissions');
				
				foreach($permissions->result() as $perm){
					if($perm->can_block_book == 1){
						$data['can_block_book'] = true;
						break;
					}
				}
			}
		}
		
		return $data;
	}


}
