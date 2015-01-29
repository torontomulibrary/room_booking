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
		$this->output->enable_profiler(DEBUG_MODE);
		
		$this->session->keep_flashdata('origin'); //No idea why this is neccessary, but it does its job
		
		//TEMP CODE - TO BE DELETED ONCE LOGIN SYSTEM IS BUILT
		//$this->session->set_userdata('username', 'swilson');
	}

	
	public function index(){
		//$this->template->load('rula_template', 'admin/dashboard');
		$this->load->view('rula_template');
	}
	
	//Login the current user. Redirect back to $url if set
	function login_user(){
		$this->load->library('cas');
		$this->load->model('user_model');
		$this->load->model('role_model');
		$roles = array();
		
		//Force the user to log in
		$this->cas->force_auth();
		$user_data = $this->cas->user();
		
		
		$this->session->set_userdata('username', $user_data->userlogin);
		$this->session->set_userdata('cas_active_classes', $this->cas->getAttribute('activeclasses'));
		
		
		
		//---------------------------------------------------------------
		//TODO: Check for CAS roles that we care about (undergrad/grad)
		$cas_roles = $this->cas->getAttribute('activeclasses');
		if(in_array('graduate', $cas_roles)){
			$object = new stdClass();
			$object->role_id = 5; //Hardcoded ID. Yuck!
			$object->name = "Graduate";
			$roles[] = $object;
		}
		
		if(in_array('undergrad', $cas_roles)){
			$object = new stdClass();
			$object->role_id = 4; //Hardcoded ID. Yuck!
			$object->name = "Undergraduate";
			$roles[] = $object;
		}
		
		$this->session->set_userdata('name', $this->cas->getAttribute('firstname') . ' ' . $this->cas->getAttribute('lastname'));
		
		//Grad students contain the "graduate" role in activeclasses
		//UGrad  students contain the "undergrad" role in activeclasses
		
		
		//---------------------------------------------------------------
		
		//Check for super admin rights
		if($this->user_model->is_super_admin($user_data->userlogin)){
			$this->session->set_userdata('super_admin', TRUE);
		}
		
		//Check for local roles/admin rights
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
		
		//If the roles array is empty, display friendly message to user telling them
		//that they do not have access to the system (and possibly a contact email?)
		
		$redirect_url = $this->session->flashdata('origin');
		//print current_url(); die;
		//If the origin url is set, redirect to it, else go to the landing page
		if(strlen($redirect_url) > 0){
			redirect($redirect_url);
		}
		else{
			redirect(base_url());
		}
		
	}
	
	function logout(){
		$this->session->sess_destroy();
		
		$this->load->library('cas');
		$this->cas->logout('http://library.ryerson.ca');
		
		$this->template->load('rula_template', 'booking/book_room_form');
		
		
	}
}