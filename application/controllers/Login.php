<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/admin
	 *	- or -  
	 * 		http://example.com/index.php/admin/index
	 *	- or -
	**/
	 
	//$this->template->load(template, view, vars) 
	function __construct(){
		parent::__construct();
		
		//If site constant is set to debug, enable the profiler (gives analytics for page load). 
		//$this->output->enable_profiler(DEBUG_MODE);
		
		$this->session->keep_flashdata('origin'); //No idea why this is neccessary, but it does its job

	}

	
	public function index(){
		$this->load->view(DEFAULT_TEMPLATE);
	}
	
	//Login the current user. Redirect back to $url if set
	function login_user(){
		$this->load->library('cas');
		$this->load->model('user_model');
		$this->load->model('role_model');
		$this->load->model('log_model');
		
	
		
		$roles = array();
		
		//Force the user to log in
		try{
			@$this->cas->force_auth();
		}
		catch (Exception $e) {
			$this->session->sess_destroy();
			$this->cas->logout();
			log_message('error', $e->getMessage().' : '. $e->getFile(). '('.$e->getLine().')');
		}
		
		$user_data = $this->cas->user();
		
		
		$this->session->set_userdata('username', $user_data->userlogin);
		$this->session->set_userdata('cas_active_classes', $this->cas->getAttribute('activeclasses'));
		
		//------------Load Users from existing groups--------------------
		$cas_roles = $this->cas->getAttribute('activeclasses');
		
		//Apparently its possible for it to be a string. Thanks CAS library!
		if(!is_array($cas_roles) && is_string($cas_roles) && strlen($cas_roles > 0)){
			$cas_roles[] = $cas_roles; 
		}
		else if(!is_array($cas_roles) && is_string($cas_roles) && strlen($cas_roles == 0)){
			$cas_roles = array();
		}
		else if(!is_array($cas_roles) && !is_string($cas_roles)){
			$cas_roles = array();
		}
		
		//Load all roles from the database
		$all_roles = $this->role_model->get_roles();
		
		foreach($all_roles->result() as $role){
			
			$role_attributes = explode(",",$role->login_attributes);
			
			foreach($cas_roles as $cas_role){
				
				if(in_array($cas_role, $role_attributes)){
					$object = new stdClass();
					$object->role_id = $role->role_id;
					$object->name = $role->name;
					$roles[] = $object;
					
					break;
				}
			}
			
			//Access centre special role
			if(USE_ACCESS_CENTRE_LIST){
				if(in_array("access_centre", $role_attributes)){
					if(!strstr($_SERVER['HTTP_HOST'], 'localhost') && $this->user_model->is_access_center($user_data->userlogin)){
						$object = new stdClass();
						$object->role_id = $role->role_id;
						$object->name = $role->name;
						$roles[] = $object;
					}					
				}				
			}
			
			//Libstaff special role
			if(USE_LIBSTAFF_LIST){
				if(in_array("libstaff", $role_attributes)){
					if( $this->user_model->is_libstaff($user_data->userlogin)){
						$object = new stdClass();
						$object->role_id = $role->role_id;
						$object->name = $role->name;
						$roles[] = $object;
					}					
				}				
			}
			
			if(USE_STUDENT_FACULTY_SERVICE){
				//$studentNumber = '500898862';
				$studentNumber = $this->cas->getAttribute('studentnumber');
				
				if(strlen($studentNumber) > 0){
					if(in_array("fcs_member", $role_attributes)){
						if( $this->user_model->is_fcs_member($studentNumber)){
							$object = new stdClass();
							$object->role_id = $role->role_id;
							$object->name = $role->name;
							$roles[] = $object;
						}					
					}
				}
			}
		
		}			
		
		$this->session->set_userdata('name', $this->cas->getAttribute('firstname') . ' ' . $this->cas->getAttribute('lastname'));
		
		//---------------------------------------------------------------
		
		//Check for super admin rights
		if($this->user_model->is_super_admin($user_data->userlogin)){
			$this->session->set_userdata('super_admin', TRUE);
		}
		
		//Check for reg admin rights
		$this->session->set_userdata('admin', $this->user_model->is_admin($user_data->userlogin));
		
		//Check for local roles rights
		$local_user = $this->user_model->get_user_by_matrix($user_data->userlogin);
		
		//Assign local roles to the session
		if($local_user->num_rows() === 1){
			$local_roles = $this->role_model->get_user_roles($local_user->first_row()->user_id);
			
			if($local_roles->num_rows() > 0){
				foreach($local_roles->result() as $role){
					//Make sure user doesn't already have this role
					$found = false;
					
					foreach($roles as $existing_role){
						if($existing_role->role_id == $role->role_id) $found = true;
					}
					if(!$found) $roles[] = $role;
				}
			}
		}
		
		$this->session->set_userdata('roles', $roles);
		
		//Does the user have any roles in the system? No roles = no access
		if(count($roles) === 0){
			$this->log_model->log_event('login', $this->session->userdata('username'), "Login Denied", null, implode(',', $cas_roles));
			$this->template->load(DEFAULT_TEMPLATE, 'denied');
			$this->session->sess_destroy();	
			return;
			
		}
		else if($this->user_model->is_banned($user_data->userlogin)){
			$this->template->load($this->role_model->get_theme(), 'banned');
			$this->session->sess_destroy();	
			return;
		}
		//Successful login
		else{

			$this->load->library('user_agent');
			
			if($this->agent->is_mobile()){
				$this->log_model->log_event('mobile', $this->session->userdata('username'), "Login");
			}
			else{
				$this->log_model->log_event('desktop', $this->session->userdata('username'), "Login");
			}
			
			$redirect_url = $this->session->flashdata('origin');
			
			//If they are going to root of application, see if they are a mobile user and redirect if they are
			if($redirect_url == base_url() && $this->agent->is_mobile()){
				redirect(base_url(). 'mobile');
			}
			
			//If the origin url is set, redirect to it, else go to the landing page
			if(strlen($redirect_url) > 0){
				redirect($redirect_url);
			}
			else{
				redirect(base_url());
			}
		}
		
	}
	
	function logout(){
		$this->load->model('log_model');
		
		//If the session has timed out, prevent an error if they click the logout button
		if($this->session->userdata('username') !== NULL)
			$this->log_model->log_event('desktop', $this->session->userdata('username'), "Logout");
		
		$this->session->sess_destroy();
		
		$this->load->library('cas');
		$this->cas->logout(base_url());
		
		$this->template->load(DEFAULT_TEMPLATE, 'booking/book_room_form');
		
		
	}
}