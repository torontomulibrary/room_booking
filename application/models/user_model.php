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
			'admin_id' => $admin_id,
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
		//Download staff list from remote source, and cache file for the day. 
		
		
		//Check to see if a cache file already exists
		if(file_exists('temp/'. date('Ymd').'.libstaff')){
			$jsonData = @file_get_contents('temp/'. date('Ymd').'.libstaff');
		}
		else{
			// Create a stream
			$opts = array(
			  'http'=>array(
				'method'=>	"GET",
				'User-agent' => USER_AGENT,
				'content'=>	'',
				'timeout'=>	60
			  )
			);

			$context = stream_context_create($opts);
			$url = LIBSTAFF_URL;
			
			$file = @file_get_contents($url, false, $context);
			
			//Make sure the server gave a 200 response, else return the user an error
			if($file !== FALSE && $http_response_header[0] === "HTTP/1.1 200 OK"){
				//Delete older versions of the cache file
				foreach(glob("temp/*.libstaff") as $f) {
					unlink($f);
				}
				
				//Write new cache file
				file_put_contents('temp/'. date('Ymd').'.libstaff', $file);
				
				$jsonData = $file;
			}
			else{
				return FALSE;
			}
			
		}
		
		$jsonData = json_decode($jsonData);
		
		$found = false;
		
		//Check for match
		foreach ($jsonData as $matrix){
			if($username == $matrix) return TRUE;
		}
		
		if($found == false) return FALSE;
	
		
	}
	
	function is_fcs_member($studentNumber){
		require_once(APPPATH.'libraries/PbeWithMd5AndDes.php');
		require_once(APPPATH.'libraries/PkcsKeyGenerator.php');
		require_once(APPPATH.'libraries/DesEncryptor.php');
		
		//Prepare the student ID
		$date = date('mdYHis');
		
		$raw_string = $date . $studentNumber;
	
		//Encrypt it!
		$crypt = PbeWithMd5AndDes::encrypt($raw_string, STUDENT_FACULTY_SERVICE_KEY, STUDENT_FACULTY_SERVICE_IV, 20, 1);
		
		//Prepare the request
		$opts = array(
		  'http'=>array(
			'ignore_errors' => true,
			'method'=>	"POST",
			'header'=> 	"Content-Type: application/xml\r\n".
						"Authorization: Basic ". base64_encode(STUDENT_FACULTY_SERVICE_USERNAME.":".STUDENT_FACULTY_SERVICE_PASSWORD)."\r\n",
			'content'=>	'<?xml version="1.0"?>'."\n".
						'<RU_STD_CAREER_REQUEST xmlns="http://xmlns.ryerson.ca/ps/sas/schemas/RU_STD_CAREER_REQUEST.V1">'."\n".
						'<STUDENT_ID>'.implode(unpack("H*", $crypt)).'</STUDENT_ID>'."\n".
						'</RU_STD_CAREER_REQUEST>',
			'timeout'=>	60,
			)
		);
		
		$context = stream_context_create($opts);
		$file = @file_get_contents(STUDENT_FACULTY_SERVICE_URL, false, $context);
		
		if($file !== FALSE && strstr($http_response_header[0],"HTTP/1.1 200")){
			$careers = new SimpleXMLElement($file);
			
			//Check for errors with a 200 status. These will be XML with containing "<RU_STD_CAREER_REQUEST_FAULT>"
			if(isset($careers->IS_FAULT)){
				//Error, write to log?
				return FALSE;
			}
			else{
				//Else good response. Iterate all CAREERS, as a person can have multiple. Stop once the ACADEMIC_GROUP "CS" is found
				foreach ($careers->CAREERS->CAREER as $career) {
				   if($career->ACADEMIC_GROUP == "CS") return TRUE;
				}
			}

			return FALSE;
		}
		else{
			//Server response was not a 200 status. A error occured
			//Error, write to log?
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
		if($file !== FALSE && strstr($http_response_header[0],"HTTP/1.1 200")){
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
