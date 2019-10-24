<?php

class role_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}
	
	function get_roles(){
		$this->db->order_by('name');
		return $this->db->get('roles');
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
	
	function get_priority_role($room_id){
		if(!is_numeric($room_id)) return false;
		
		$sql = "SELECT * FROM roles
				WHERE priority IN 

				(
					SELECT max(priority) FROM 
					(
						SELECT r.* FROM roles r, room_roles rr
						WHERE r.role_id = rr.role_id
						AND rr.room_id = ". $room_id ."  
					) rrr
				)
				LIMIT 1"; //If more then 1 result for this priority, only take the first one
		
		return $this->db->query($sql);
	}
	
	function get_email_template($room_id){
		$role_data = $this->get_priority_role($room_id);
		
		$role_json = json_decode($role_data->row()->interface_settings);
		
		//Trim the file extension from the template (eg, '.php')
		return substr($role_json->conf_email, 0, -4);
		
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
	
	function add_role($role_name, $hours_week, $booking_window, $login_attributes, $priority, $is_private, $interface_settings, $site_theme){
		$data = array(
			'name' => $role_name,
			'hours_per_week' => $hours_week,
			'booking_window' => $booking_window,
			'login_attributes' => $login_attributes,
			'login_attributes' => $login_attributes,
			'priority' => $priority,
			'is_private' => $is_private,
			'interface_settings' => $interface_settings,
			'site_theme' => $site_theme,
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
	
	function edit_role($role_id, $role_name, $hours_week, $booking_window, $login_attributes, $priority, $is_private, $interface_settings, $site_theme){
	
		$data = array(
			'name' => $role_name,
			'hours_per_week' => $hours_week,
			'booking_window' => $booking_window,
			'login_attributes' => $login_attributes,
			'login_attributes' => $login_attributes,
			'priority' => $priority,
			'is_private' => $is_private,
			'interface_settings' => $interface_settings,
			'site_theme' => $site_theme,
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
	
	function get_policy_url($room_id = false){
		if($room_id === false){
			//Load all of the user roles, and return the one with highest priority
			$sql = "	SELECT interface_settings FROM roles 
						WHERE priority IN (SELECT max(priority) FROM roles WHERE role_id IN ";
			
			
						//Gather roles from session rather then database (since students etc.. are not whitelisted)
						$roles = array();
						
						if(count($this->session->userdata('roles')) === 0 || $this->session->userdata('roles') === NULL) return DEFAULT_TEMPLATE;
						foreach($this->session->userdata('roles') as $role){
							if(is_numeric($role->role_id)) $roles[] = $role->role_id;
						}
						
						$sql .= "(".implode(",", $roles).")) 
						AND role_id IN ";
			
			
						//Gather roles from session rather then database (since students etc.. are not whitelisted)
						$roles = array();
						
						foreach($this->session->userdata('roles') as $role){
							if(is_numeric($role->role_id)) $roles[] = $role->role_id;
						}
						
						$sql .= "(".implode(",", $roles).")
						LIMIT 1";
			
			//Retrieve the url
			$result_json = $this->db->query($sql);
			$result_data = json_decode($result_json->row()->interface_settings);
			
			return $result_data->policy_url;
		}
		else if(is_numeric($room_id)){
			echo 'not implemented yet'; return;
		}
	}
	
	function load_email_templates(){
		$files = array();
		
		if ($handle = opendir(getcwd() . DIRECTORY_SEPARATOR .'application'. DIRECTORY_SEPARATOR . 'views'. DIRECTORY_SEPARATOR . 'email')) {
			while (false !== ($entry = readdir($handle))) {
				if(strstr($entry, '.php') && $entry !== "booking_ics.php"){
					$files[] = $entry;
				}
			}
			closedir($handle);
		}
		
		sort($files);
		
		return $files;
		
	}
	
	function load_themes(){
		$files = array();
		
		if ($handle = opendir(getcwd() . DIRECTORY_SEPARATOR .'application'. DIRECTORY_SEPARATOR . 'views')) {
			while (false !== ($entry = readdir($handle))) {
				if(strstr($entry, '_template.php') && $entry != "admin_template.php" && $entry != "mobile_template.php"){
					$files[] = $entry;
				}
			}
			closedir($handle);
		}
		
		sort($files);
		
		return $files;
		
	}

	function get_theme(){
		//Load all of the user roles, and return the one with highest priority
			$sql = "	SELECT site_theme FROM roles 
						WHERE priority IN (SELECT max(priority) FROM roles WHERE role_id IN ";
			
			
						//Gather roles from session rather then database (since students etc.. are not whitelisted)
						$roles = array();
						
						if(count($this->session->userdata('roles')) === 0 || $this->session->userdata('roles') === NULL) return DEFAULT_TEMPLATE;
						foreach($this->session->userdata('roles') as $role){
							if(is_numeric($role->role_id)) $roles[] = $role->role_id;
						}
						
						$sql .= "(".implode(",", $roles).")) 
						AND role_id IN ";
			
			
						//Gather roles from session rather then database (since students etc.. are not whitelisted)
						$roles = array();
						
						foreach($this->session->userdata('roles') as $role){
							if(is_numeric($role->role_id)) $roles[] = $role->role_id;
						}
						
						$sql .= "(".implode(",", $roles).")
						LIMIT 1";
			
			//Retrieve the url
			$result = $this->db->query($sql);
			$return_data = $result->row()->site_theme;
			
			return substr($return_data, 0, -4);
	}
}
