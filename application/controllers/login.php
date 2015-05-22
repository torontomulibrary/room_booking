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
	function Login(){
		parent::__construct();
		
		//If site constant is set to debug, enable the profiler (gives analytics for page load). 
		//$this->output->enable_profiler(DEBUG_MODE);
		
		$this->session->keep_flashdata('origin'); //No idea why this is neccessary, but it does its job
		
		//TEMP CODE - TO BE DELETED ONCE LOGIN SYSTEM IS BUILT
		//$this->session->set_userdata('username', 'swilson');
	}

	
	public function index(){
		$this->load->view('rula_template');
	}
	
	//Login the current user. Redirect back to $url if set
	function login_user(){
		$this->load->helper('rms_helper');
		$this->load->library('cas');
		$this->load->model('user_model');
		$this->load->model('role_model');
		$roles = array();
		
		//Force the user to log in
		$this->cas->force_auth();
		$user_data = $this->cas->user();
		
		
		$this->session->set_userdata('username', $user_data->userlogin);
		$this->session->set_userdata('cas_active_classes', $this->cas->getAttribute('activeclasses'));
		
		
		
		//------------Load Users from existing groups--------------------
		$cas_roles = $this->cas->getAttribute('activeclasses');
		
		//Grad Rooms
		if(is_array($cas_roles) && in_array('graduate', $cas_roles)){
			$object = new stdClass();
			$object->role_id = 5; //Hardcoded ID. Yuck!
			$object->name = "Graduate";
			$roles[] = $object;
		}
		
		
		//Undergrad/Grad/CE are all allowed to book these rooms
		if(is_array($cas_roles) && (in_array('undergrad', $cas_roles) || in_array('cned', $cas_roles) || in_array('graduate', $cas_roles))){
			$object = new stdClass();
			$object->role_id = 4; //Hardcoded ID. Yuck!
			$object->name = "Undergraduate";
			$roles[] = $object;
		}
		
		//Access centre rooms
		if( !strstr($_SERVER['HTTP_HOST'], 'localhost') && is_access_center($user_data->userlogin)){ //Disable on localhost
			$object = new stdClass();
			$object->role_id = 6; //Hardcoded ID. Yuck!
			$object->name = "Adaptive";
			$roles[] = $object;
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
		
		//Assign local rows to the session
		if($local_user->num_rows() === 1){
			$local_roles = $this->role_model->get_user_roles($local_user->first_row()->user_id);
			
			if($local_roles->num_rows() > 0){
				foreach($local_roles->result() as $role){
					$roles[] = $role;
				}
			}
		}
		
		$this->session->set_userdata('roles', $roles);
		
		//Does the user have any roles in the system? No roles = no access
		if(count($roles) === 0){
			$this->template->load('rula_template', 'denied');
			$this->session->sess_destroy();	
			
		}
		else if($this->user_model->is_banned($user_data->userlogin)){
			$this->template->load('rula_template', 'banned');
			$this->session->sess_destroy();	
		}
		//Successful login
		else{
			$this->load->model('log_model');
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
		$this->log_model->log_event('desktop', $this->session->userdata('username'), "Logout");
		
		$this->session->sess_destroy();
		
		$this->load->library('cas');
		$this->cas->logout('http://library.ryerson.ca');
		
		$this->template->load('rula_template', 'booking/book_room_form');
		
		
	}
}