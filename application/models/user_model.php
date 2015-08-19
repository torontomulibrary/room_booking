<?php

class user_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

	function is_admin($matrix_id){
		$this->db->where('matrix_id', $matrix_id);
		$this->db->where('is_admin', 1);
		$result = $this->db->get('users');
		
		if($result->num_rows() > 0){
			return TRUE;
		}
		else{
			$this->db->where('matrix_id');
			$result = $this->db->get('admin');
			
			if($result->num_rows() > 0){
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
	}
	
    function list_users(){
		if(!$this->session->userdata('super_admin')){
			return $this->db->query("
				SELECT distinct u.* FROM users u, user_roles ur
				WHERE u.user_id = ur.user_id
				and ur.role_id IN (
					SELECT role_id from user_roles ur, users u
					where u.user_id = ur.user_id
					and u.matrix_id = 'swilson'
				)			
			");
		}
		else{
			return $this->db->get('users');
		}
	}
	
	function list_super_users(){
		return $this->db->get('admin');
	}
	
	function get_admin_user($admin_id){
		$this->db->where('admin_id',$admin_id);
		return $this->db->get('admin');
	}
	
	function get_user($user_id){
		$this->db->where('user_id', $user_id);
		return $this->db->get('users');
	}

	function get_user_by_matrix($matrix_id){
		$this->db->where('matrix_id', $matrix_id);
		return $this->db->get('users');
	}
	
	function add_user($matrix, $name, $admin, $roles){
		if($admin === 'on'){
			$admin = TRUE;
		}
		else{
			$admin = FALSE;
		}
		
		$data = array(
			'matrix_id' => $matrix,
			'name' => $name,
			'is_admin' => $admin	
		);
		
		$this->db->insert('users', $data); 
		$id = $this->db->insert_id();
		
		$this->set_roles($id, $roles);
		
		return $id;
	}
	
	function add_super_user($matrix){
		$data = array(
			'matrix_id' => $matrix,
		);
		
		$this->db->insert('admin', $data); 
		$id = $this->db->insert_id();
		
		return $id;
	}
	
	function is_super_admin($username){
		$this->db->where('matrix_id',$username);
		$result = $this->db->get('admin');
		
		if($result->num_rows() > 0){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	function edit_user($user_id, $matrix, $name, $admin, $roles){
		if($admin === 'on'){
			$admin = TRUE;
		}
		else{
			$admin = FALSE;
		}
		
		$data = array(
			'user_id' => $user_id,
			'matrix_id' => $matrix,
			'name' => $name,
			'is_admin' => $admin	
		);
		
		$this->db->where('user_id', $user_id); 
		$this->db->update('users', $data); 
		
		$this->set_roles($user_id, $roles);
		
		return TRUE;
	}
	
	function edit_super_admin($admin_id, $matrix_id){
		$data = array(
			'admin_id' => $user_id,
			'matrix_id' => $matrix_id
		);
		
		$this->db->where('admin_id', $admin_id); 
		$this->db->update('admin', $data); 
		
		return TRUE;
	}
	
	function set_roles($user_id, $roles){
		if(!is_array($roles)) return FALSE;
		
		$this->db->where('user_id', $user_id);
		$this->db->delete('user_roles');
		
		foreach($roles as $role){
			$data = array(
				'user_id' => $user_id,
				'role_id' => $role
			);
			
			$this->db->insert('user_roles', $data);
		}
	}
	
	function delete_user($user_id){
		$this->db->where('user_id', $user_id);
		$this->db->delete('users');
	}
	
	function delete_super_admin($admin_id){
		$this->db->where('admin_id', $admin_id);
		$this->db->delete('admin');
	}
	
	function load_banned_users(){
		return $this->db->get('banned_users');
	}
	
	function is_banned($matrix_id){
		$this->db->where('matrix_id', $matrix_id);
		$result = $this->db->get('banned_users');
		
		if($result->num_rows() > 0) return TRUE;
		else return FALSE;
	}
	
	function ban_user($matrix_id, $reason, $date, $reporter){
		
		$data = array(
			'matrix_id' => $matrix_id,
			'reason' => $reason,
			'date' => $date,
			'reporter' => $reporter,
		);
		
		$this->db->insert('banned_users', $data); 
		$this->db->cache_delete_all();
		
		return TRUE;
	}
	
	function delete_banned_user($matrix_id){
		$this->db->where('matrix_id', $matrix_id);
		$this->db->delete('banned_users');
	}
	
	function is_libstaff($username){
		// Create a stream
		$opts = array(
		  'http'=>array(
			'method'=>	"GET",
			'User-agent' => "LibraryBooking/1.0",
			'content'=>	'',
			'timeout'=>	60
		  )
		);

		$context = stream_context_create($opts);

		// Block the user using the HTTP headers set above
		$url = LIBSTAFF_URL;
		
		$file = @file_get_contents($url, false, $context);
		
		//Make sure the server gave a 200 response, else return the user an error
		if($file !== FALSE && $http_response_header[0] === "HTTP/1.1 200 OK"){
			$data = json_decode($file);
			
			$found = false;
			
			foreach ($data as $matrix){
				if($username == $matrix) return TRUE;
			}
			
			if($found == false) return FALSE;
		}
		else{
			return FALSE;
		}
	}
	
	function is_access_center($username){
		$response = array();
		
		
		// Create a stream
		$opts = array(
		  'http'=>array(
			'method'=>	"GET",
			'User-agent' => USER_AGENT,
			'header'=> 	"Authorization: Basic ".base64_encode(RMS_USERNAME.":".RMS_PASSWORD)."\r\n",
			'content'=>	'',
			'timeout'=>	60
		  )
		);

		$context = stream_context_create($opts);

		// Block the user using the HTTP headers set above
		$url = str_replace('{username}', urlencode($username), RMS_SERVICE);
		
		$file = @file_get_contents($url, false, $context);
		
		//Make sure the server gave a 200 response, else return the user an error
		if($file !== FALSE && $http_response_header[0] === "HTTP/1.1 200 OK"){
			$data = json_decode($file);
			
			if($data->hasOwnerResource == true){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return FALSE;
		}
	}

}
