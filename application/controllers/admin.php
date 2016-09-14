<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
	function Admin(){
		parent::__construct();
		
		//Check for existing login
		if(!strlen($this->session->userdata('username')) > 0){
			$this->session->set_flashdata('origin', current_url());
			redirect('login/login_user');
		}
		
		
		//Check to see if the user is an administrator
		$this->load->model('user_model'); 
		$this->load->model('role_model'); 
		
		if(!$this->user_model->is_admin($this->session->userdata('username'))){
		//Dont do this. Use flashdata instead, and redirect to non-admin area
			$this->session->set_flashdata('warning', 'You an not an administrator');
			redirect('/');
		}
		
	
		//If site constant is set to debug, enable the profiler (gives analytics for page load). 
		//DO NOT USE ON LIVE SITE
		if($this->input->get('debug') !== false) $this->output->enable_profiler(DEBUG_MODE);
	}

	
	public function index(){
		$this->load->model('log_model');
		
		$data['usage_by_type'] = $this->log_model->report_by_device(mktime(0,0,0,date('n'),1), mktime(23,59,59,date('n')+1,0)); //Mobile vs Desktop for current month
		$data['usage_by_hour'] = $this->log_model->usage_by_hour(mktime(0,0,0,date('n'),1), mktime(23,59,59,date('n')+1,0)); //Mobile vs Desktop for current month
		
		$this->template->load('admin_template', 'admin/dashboard', $data);
	}
	
	public function clear_cache(){
		  $this->load->library('user_agent');
		
		
		foreach (glob(FCPATH.'temp'.DIRECTORY_SEPARATOR.'*') as $filename) {
			if (is_file($filename)) {
				if(!strstr($filename, 'README.txt')) unlink($filename);
			}
		}
		
		$this->db->cache_delete_all();
		
		if ($this->agent->is_referral()){
			redirect($this->agent->referrer());
		}
		else{
			redirect('/admin');
		}
	}
	
	function users(){
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
			$this->load->model('role_model');
			$this->load->model('user_model');
			
			if($this->uri->segment(3) === 'add'){
				$matrix = $this->input->post('matrix');
				$admin = $this->input->post('admin');
				$role = $this->input->post('role');
				$name = $this->input->post('name');
		
				$id = $this->user_model->add_user($matrix, $name, $admin, $role);
				
				if(is_numeric($id)){
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">User added successfully</div>');
					$this->db->cache_delete_all();
					redirect('admin/users');
				}
				else{
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
					$this->db->cache_delete_all();
					redirect('admin/users');
				}
			}
			
			//Set variable so the view loads the form, rather then list out existing users
			else if ($this->uri->segment(3) === 'new'){
				$data['new'] = true;
				$data['user_roles'] = $this->role_model->list_roles();
			}
			
			else if ($this->uri->segment(3) === 'delete'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The user was not deleted</div>');
					
					redirect('admin/users');
				}
				
				$this->user_model->delete_user($this->uri->segment(4));
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">User deleted successfully</div>');
				$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
				redirect('admin/users');
			}
			else if ($this->uri->segment(3) === 'edit'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
					redirect('admin/users');
				}
				else{
					$data['current_user'] = $this->user_model->get_user($this->uri->segment(4));
					$data['user_roles'] = $this->role_model->get_user_roles($this->uri->segment(4));
					
					if($data['current_user']->num_rows() === 0){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid User ID</div>');
						$this->db->cache_delete_all();
						redirect('admin/users');
					}
				}
			}
			
			else if ($this->uri->segment(3) === 'update'){
				$user_id = $this->input->post('user_id');
				$matrix = $this->input->post('matrix');
				$admin = $this->input->post('admin');
				$role = $this->input->post('role');
				$name = $this->input->post('name');
		
				$id = $this->user_model->edit_user($user_id, $matrix, $name, $admin, $role);
				
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The user has been updated</div>');
				$this->db->cache_delete_all();
				redirect('admin/users');
			}
			
			$data['users'] = $this->user_model->list_users();
			$data['roles'] = $this->role_model->list_roles();
			
			$this->template->load('admin_template', 'admin/users', $data);
		}
	}
	
	function roles(){
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
		
			$this->load->model('role_model');
			
			if($this->uri->segment(3) === 'add'){
				$role_name = $this->input->post('role_name');
				$bookings_day = $this->input->post('bookings_day');
				$hours_week = $this->input->post('hours_week');
				$booking_window = $this->input->post('booking_window');
				
				$id = $this->role_model->add_role($role_name, $bookings_day,  $hours_week, $booking_window);
				
				if(is_numeric($id)){
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Building added successfully</div>');
					$this->db->cache_delete_all();
					redirect('admin/roles');
				}
				else{
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
					$this->db->cache_delete_all();
					redirect('admin/roles');
				}
			}
			
			//Set variable so the view loads the form, rather then list out existing roles
			else if ($this->uri->segment(3) === 'new'){
				$data['new'] = true;
			}
			
			else if ($this->uri->segment(3) === 'delete'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The role was not deleted</div>');
					
					redirect('admin/roles');
				}
				
				$this->role_model->delete_role($this->uri->segment(4));
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Role deleted successfully</div>');
				$this->db->cache_delete_all();
				redirect('admin/roles');
			}
			else if ($this->uri->segment(3) === 'edit'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
					redirect('admin/roles');
				}
				else{
					$data['current_role'] = $this->role_model->get_role($this->uri->segment(4));
					
					if($data['current_role']->num_rows() === 0){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid role ID</div>');
						$this->db->cache_delete_all();
						redirect('admin/roles');
					}
				}
			}
			
			else if ($this->uri->segment(3) === 'update'){
				$role_id = $this->input->post('role_id');
				$role_name = $this->input->post('role_name');
				$bookings_day = $this->input->post('bookings_day');
				$hours_day = $this->input->post('hours_day');
				$hours_week = $this->input->post('hours_week');
				$booking_window = $this->input->post('booking_window');
		
				$id = $this->role_model->edit_role($role_id, $role_name, $bookings_day, $hours_week, $booking_window);
				
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The role has been updated</div>');
				$this->db->cache_delete_all();
				redirect('admin/roles');
			}
			
			$data['roles'] = $this->role_model->list_roles();
			
			$this->template->load('admin_template', 'admin/roles', $data);
		}
	}
	
	function ban_users(){
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
			$this->load->model('user_model');
			
			if($this->uri->segment(3) === 'add'){
				$matrix_id = $this->input->post('matrix_id');
				$reason = $this->input->post('reason');
				$reporter = $this->session->userdata('name');
				$date = date('Y-m-d H:i:s');
				
				$this->user_model->ban_user($matrix_id, $reason, $date, $reporter);
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">User successfully banned</div>');
				redirect('admin/ban_users');
			}
			
			//Set variable so the view loads the form, rather then list out existing roles
			else if ($this->uri->segment(3) === 'new'){
				$data['new'] = true;
			}
			
			else if ($this->uri->segment(3) === 'delete'){
				
				$this->user_model->delete_banned_user($this->uri->segment(4));
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">User ban removed</div>');
				$this->db->cache_delete_all();
				redirect('admin/ban_users');
			}
			
			$data['banned_users'] = $this->user_model->load_banned_users();
			
			$this->template->load('admin_template', 'admin/ban_users', $data);
		}
	}
	
	//This function is only available to admins. Super admins get a more extensive list of fields
	function modify_rooms(){
		$this->load->model('room_model');
		$this->load->model('resource_model');
		$this->load->model('building_model');
		$this->load->model('role_model');
		

		if ($this->uri->segment(3) === 'edit'){
			if(!is_numeric($this->uri->segment(4))){
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
				redirect('admin/modify_rooms');
			}
			else{
				$data['current_room'] = $this->room_model->load_room($this->uri->segment(4));
				$data['room_roles'] = $this->role_model->get_room_roles($this->uri->segment(4));
				
				if($data['current_room']['room_data']->num_rows() === 0){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid Room ID</div>');
					$this->db->cache_delete_all();
					redirect('admin/modify_rooms');
				}
			}
		}
		
		else if ($this->uri->segment(3) === 'update'){
			$room_id = $this->input->post('room_id');
			$notes = $this->input->post('notes');
			
			
			
			$id = $this->room_model->edit_notes($room_id, $notes);

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The room has been updated</div>');
			$this->db->cache_delete_all();
			redirect('admin/modify_rooms');
		}
		
		
		$data['rooms'] = $this->room_model->list_admin_rooms();
		$data['roles'] = $this->role_model->list_roles();
		
		$data['resources'] = $this->resource_model->list_resources();
		$data['buildings'] = $this->building_model->list_buildings();
		
		$this->template->load('admin_template', 'admin/modify_rooms', $data);
	}
	
	
	function rooms(){
		//Make sure non-super admins cannot access these methods
		if(!$this->session->userdata('super_admin')){
			redirect('admin/modify_rooms');
		}
		
		$this->load->model('room_model');
		$this->load->model('resource_model');
		$this->load->model('building_model');
		$this->load->model('role_model');
		
		if($this->uri->segment(3) === 'add'){
			$building = $this->input->post('building');
			$role = $this->input->post('role');
			$room = $this->input->post('room');
			$resources = $this->input->post('resources');
			$seats = $this->input->post('seats');
			$active = $this->input->post('active');
			$max_daily_hours = $this->input->post('max_daily_hours');
			$notes = $this->input->post('notes');
			$requires_moderation = $this->input->post('requires_moderation');
			
			
			
			$id = $this->room_model->add_room($building, $room, $seats, $role, $active, $resources, $max_daily_hours, $notes, $requires_moderation);
			
			if(is_numeric($id)){
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Room added successfully</div>');
				$this->db->cache_delete_all();
				redirect('admin/rooms');
			}
			else{
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
				redirect('admin/rooms');
			}
		}
		
		//Set variable so the view loads the form, rather then list out existing rooms
		else if ($this->uri->segment(3) === 'new'){
			//$data['room_roles'] = $this->role_model->list_roles();
			$data['new'] = true;
		}
		
		else if ($this->uri->segment(3) === 'delete'){
			if(!is_numeric($this->uri->segment(4))){
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The room was not deleted</div>');
				
				redirect('admin/rooms');
			}
			
			$this->room_model->delete_room($this->uri->segment(4));
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Room deleted successfully</div>');
			$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
			redirect('admin/rooms');
		}
		else if ($this->uri->segment(3) === 'edit'){
			if(!is_numeric($this->uri->segment(4))){
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
				redirect('admin/rooms');
			}
			else{
				$data['current_room'] = $this->room_model->load_room($this->uri->segment(4));
				$data['room_roles'] = $this->role_model->get_room_roles($this->uri->segment(4));
				
				if($data['current_room']['room_data']->num_rows() === 0){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid Room ID</div>');
					$this->db->cache_delete_all();
					redirect('admin/rooms');
				}
			}
		}
		
		else if ($this->uri->segment(3) === 'update'){
			$building = $this->input->post('building');
			$roles = $this->input->post('role');
			$room = $this->input->post('room');
			$resources = $this->input->post('resources');
			$seats = $this->input->post('seats');
			$active = $this->input->post('active');
			$room_id = $this->input->post('room_id');
			$max_daily_hours = $this->input->post('max_daily_hours');
			$notes = $this->input->post('notes');
			$requires_moderation = $this->input->post('requires_moderation');
			
			//If no resources are selected, create an empty array
			if($resources === false) $resources = array();
			
			
			$id = $this->room_model->edit_room($room_id, $building, $room, $seats, $roles, $active, $resources, $max_daily_hours, $notes, $requires_moderation);

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The room has been updated</div>');
			$this->db->cache_delete_all();
			redirect('admin/rooms');
		}
		
		
		$data['rooms'] = $this->room_model->list_admin_rooms();
		$data['roles'] = $this->role_model->list_roles();
		
		$data['resources'] = $this->resource_model->list_resources();
		$data['buildings'] = $this->building_model->list_buildings();
		
		$this->template->load('admin_template', 'admin/rooms', $data);
	}
	
	function buildings(){
		//Deny access if user is not super admin
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
		
			$this->load->model('building_model');
			$this->load->model('role_model');
			
			if($this->uri->segment(3) === 'add'){
				$building = $this->input->post('building');
				$ext_id = $this->input->post('ext_id');
				
				$id = $this->building_model->add_building($building);
				
				if(is_numeric($id)){
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Building added successfully</div>');
					$this->db->cache_delete_all();
					redirect('admin/buildings');
				}
				else{
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
					$this->db->cache_delete_all();
					redirect('admin/buildings');
				}
			}
			
			//Set variable so the view loads the form, rather then list out existing buildings
			else if ($this->uri->segment(3) === 'new'){
				$data['new'] = true;
			}
			
			else if ($this->uri->segment(3) === 'delete'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The building was not deleted</div>');
					
					redirect('admin/buildings');
				}
				
				$this->building_model->delete_building($this->uri->segment(4));
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Building deleted successfully</div>');
				$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
				redirect('admin/buildings');
			}
			else if ($this->uri->segment(3) === 'edit'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
					redirect('admin/buildings');
				}
				else{
					$data['current_building'] = $this->building_model->load_building($this->uri->segment(4));
					
					if($data['current_building']['building_data']->num_rows() === 0){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid building ID</div>');
						$this->db->cache_delete_all();
						redirect('admin/buildings');
					}
				}
			}
			
			else if ($this->uri->segment(3) === 'update'){
				$building_name = $this->input->post('building');
				$building_id = $this->input->post('building_id');
				$ext_id = $this->input->post('ext_id');
				
				$id = $this->building_model->edit_building($building_id, $building_name, $ext_id);
				
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The building has been updated</div>');
				
				redirect('admin/buildings');
			}
			
			$data['buildings'] = $this->building_model->list_buildings();
			
			$this->template->load('admin_template', 'admin/buildings', $data);
		}
	}
	
	function building_hours(){
		//Deny access if user is not super admin
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
			$this->load->model('building_model');
			$this->load->model('role_model');
			
			//If a building has been selected
			if($this->uri->segment(3) === 'edit'){
				$this->load->model('hours_model');
				
				//Basic validation on the building ID
				if(!is_numeric($this->uri->segment(4))) return false;
				
				$data['current_building'] = $this->building_model->load_building($this->uri->segment(4));
				
				//Catch the case of an invalid building id
				if($data['current_building'] === false) return;
				
				
				//Deleting a closure
				if($this->uri->segment(5) === 'remove_closure' && is_numeric($this->uri->segment(6))){
					$this->hours_model->delete_closure($this->uri->segment(6));
					
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Closure deleted successfully</div>');
					redirect('admin/building_hours/edit/'.$this->uri->segment(4));
				}
				
				//Creating a new closure
				if($this->uri->segment(5) === 'new_closure' && $this->uri->segment(6) === 'submit'){
					$result = $this->hours_model->add_closure($this->uri->segment(4), $this->input->post('closure_date'));
					
					if(is_numeric($result)){
						$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Closure added successfully</div>');
						redirect('admin/building_hours/edit/'.$this->uri->segment(4));
					}
					else{
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Closure may not have been added</div>');
						redirect('admin/building_hours/edit/'.$this->uri->segment(4));
					}
				}
				
				//Creating new hours
				if($this->uri->segment(5) === 'new_hours' && $this->uri->segment(6) === 'submit'){
					$hours_data = array(
						'sun_start' 	=>		$this->input->post('sun_start'),
						'sun_end' 		=>		$this->input->post('sun_end'),
						'mon_start' 	=>		$this->input->post('mon_start'),
						'mon_end' 		=>		$this->input->post('mon_end'),
						'tue_start' 	=>		$this->input->post('tue_start'),
						'tue_end' 		=>		$this->input->post('tue_end'),
						'wed_start' 	=>		$this->input->post('wed_start'),
						'wed_end' 		=>		$this->input->post('wed_end'),
						'thu_start' 	=>		$this->input->post('thu_start'),
						'thu_end' 		=>		$this->input->post('thu_end'),
						'fri_start' 	=>		$this->input->post('fri_start'),
						'fri_end' 		=>		$this->input->post('fri_end'),
						'sat_start' 	=>		$this->input->post('sat_start'),
						'sat_end'		=>		$this->input->post('sat_end'),
					);

					$result = $this->hours_model->add_hours($this->uri->segment(4), $this->input->post('start_date'), $this->input->post('end_date'), $hours_data);
					
					if(!is_numeric($result)){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. '.$result.'. Hours may not have been added</div>');
						redirect('admin/building_hours/edit/'.$this->uri->segment(4));
					}
					else{
						$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Hours added successfully</div>');
						redirect('admin/building_hours/edit/'.$this->uri->segment(4));
					}
				}
				
				
				$data['closures'] = $this->hours_model->get_closures($this->uri->segment(4));
				$data['hours'] = $this->hours_model->get_hours($this->uri->segment(4));
			}
			else{
				$data['buildings'] = $this->building_model->list_buildings();
			}
			
		
			$this->template->load('admin_template', 'admin/building_hours', $data);
					
		}
	}

	function super_admin(){
		//Deny access if user is not super admin
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
			$this->load->model('user_model');
			
			if($this->uri->segment(3) === 'add'){
				$admin_id = $this->input->post('super_admin');
				
				$id = $this->user_model->add_super_user($admin_id);
				
				if(is_numeric($id)){
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">User added successfully</div>');
					$this->db->cache_delete_all();
					redirect('admin/super_admin');
				}
				else{
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
					$this->db->cache_delete_all();
					redirect('admin/super_admin');
				}
			}
			
			//Set variable so the view loads the form, rather then list out existing super_admin
			else if ($this->uri->segment(3) === 'new'){
				$data['new'] = true;
			}
			
			else if ($this->uri->segment(3) === 'delete'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The user was not deleted</div>');
					
					redirect('admin/super_admin');
				}
				
				$this->user_model->delete_super_admin($this->uri->segment(4));
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Admin deleted successfully</div>');
				$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
				redirect('admin/super_admin');
			}
			else if ($this->uri->segment(3) === 'edit'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
					redirect('admin/super_admin');
				}
				else{
					$data['current_user'] = $this->user_model->get_admin_user($this->uri->segment(4));
					
					if($data['current_user']->num_rows() === 0){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid admin ID</div>');
						$this->db->cache_delete_all();
						redirect('admin/super_admin');
					}
				}
			}
			
			else if ($this->uri->segment(3) === 'update'){
				$super_admin = $this->input->post('super_admin');
				$super_admin_id = $this->input->post('super_admin_id');
				
				$id = $this->user_model->edit_super_admin($super_admin_id, $super_admin);
				
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The super admin has been updated</div>');
				$this->db->cache_delete_all();
				redirect('admin/super_admin');
			}
			
			
			$data['admins'] = $this->user_model->list_super_users();
			
			$this->template->load('admin_template', 'admin/super_admin', $data);
		}
	}
	
	function room_resources(){
		//Deny access if user is not super admin
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
			$this->load->model('resource_model');
			
			if($this->uri->segment(3) === 'add'){
				$name = $this->input->post('room_resource_name');
				$desc = $this->input->post('resource_desc');
				$filter = $this->input->post('filter');
				
				$id = $this->resource_model->add_room_resource($name, $desc, $filter);
				
				
				if(is_numeric($id)){
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Resource added successfully</div>');
					$this->db->cache_delete_all();
					redirect('admin/room_resources');
				}
				else{
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
					$this->db->cache_delete_all();
					redirect('admin/room_resources');
				}
			}
			
			//Set variable so the view loads the form, rather then list out existing 
			else if ($this->uri->segment(3) === 'new'){
				$data['new'] = true;
			}
			
			else if ($this->uri->segment(3) === 'delete'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The resource was not deleted</div>');
					
					redirect('admin/room_resources');
				}
				
				$this->resource_model->delete_resource($this->uri->segment(4));
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Resource deleted successfully</div>');
				$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
				redirect('admin/room_resources');
			}
			else if ($this->uri->segment(3) === 'edit'){
				if(!is_numeric($this->uri->segment(4))){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
					redirect('admin/room_resources');
				}
				else{
					$data['current_resource'] = $this->resource_model->get_resource($this->uri->segment(4));
					
					if($data['current_resource']->num_rows() === 0){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid resource ID</div>');
						$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
						redirect('admin/room_resources');
					}
				}
			}
			
			else if ($this->uri->segment(3) === 'update'){
				$id = $this->input->post('room_resource_id');
				$name = $this->input->post('room_resource_name');
				$desc = $this->input->post('resource_desc');
				$filter = $this->input->post('filter');
				
				$id = $this->resource_model->edit_resource($id, $name, $desc, $filter);
				
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The resource has been updated</div>');
				$this->db->cache_delete_all();
				redirect('admin/room_resources');
			}
			
			
			$data['resources'] = $this->resource_model->list_resources();
			
			$this->template->load('admin_template', 'admin/room_resources', $data);
		}
	}
	
	
	
	
	function block_booking(){

			$this->load->model('booking_model');
			$this->load->model('room_model');
			$this->load->model('role_model');
			
			$permissions = $this->role_model->load_permissions($this->session->userdata('username'));
			if($permissions['can_block_book'] == false){
				$this->template->load('admin_template', 'admin/denied');
			}
			else{
				if($this->uri->segment(3) === 'add'){
					$reason = $this->input->post('reason');
					$start = $this->input->post('start');
					$end = $this->input->post('end');
					$rooms = $this->input->post('rooms');
					$permissions = $this->input->post('permissions'); 
					
					if($permissions === false) $permissions = array();
					
					$status = $this->booking_model->add_block_booking($reason,$start,$end, $rooms, $permissions);
					
					
					if($status){
						$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Booking added successfully</div>');
						redirect('admin/block_booking');
					}
					else{
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Data may not have been added</div>');
						redirect('admin/block_booking');
					}
				}
				
				//Set variable so the view loads the form, rather then list out existing 
				else if ($this->uri->segment(3) === 'new'){
					$data['new'] = true;
					$data['rooms'] = $this->room_model->list_admin_rooms();
					$data['roles'] = $this->role_model->list_admin_roles();
				}
				
				else if ($this->uri->segment(3) === 'delete'){
					if(!is_numeric($this->uri->segment(4))){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. The block booking was not deleted</div>');
						
						redirect('admin/block_booking');
					}
					
					$result = $this->booking_model->delete_block_booking($this->uri->segment(4));
					if($result !== FALSE){
						$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Block Booking deleted successfully</div>');
					}
					else{
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error has occured. The block booking may not have been deleted</div>');
					}
					$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
					redirect('admin/block_booking');
				}
				else if ($this->uri->segment(3) === 'edit'){
					if(!is_numeric($this->uri->segment(4))){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">An error occurred. Unable to edit</div>');
						redirect('admin/block_booking');
					}
					else{
						$data['rooms'] = $this->room_model->list_admin_rooms();
						$data['roles'] = $this->role_model->list_admin_roles();
						$data['permissions'] = $this->booking_model->get_block_booking_permissions($this->uri->segment(4));
						$data['current_bb'] = $this->booking_model->get_block_booking($this->uri->segment(4));
					}
				}
				
				else if ($this->uri->segment(3) === 'update'){
					$reason = $this->input->post('reason');
					$start = $this->input->post('start');
					$end = $this->input->post('end');
					$rooms = $this->input->post('rooms');
					$id = $this->input->post('block_booking_id');
					$permissions = $this->input->post('permissions'); 
					
					if($permissions === false) $permissions = array();
					
					$status = $this->booking_model->edit_block_booking($reason,$start,$end, $rooms, $permissions, $id);
					
					if($status !== FALSE){
						$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The block booking has been updated</div>');
					}
					else{
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Errors</div>');
					}
					
					$this->db->cache_delete_all();
					redirect('admin/block_booking');
				}
				
				//Load all UPCOMING block bookings. We don't care about past ones
				$data['block_bookings'] = $this->booking_model->list_block_bookings();
				
				$this->template->load('admin_template', 'admin/block_booking', $data);
			}
		
	}
	
	function permissions(){
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
		
			$this->load->model('role_model');
			
			$data = array();
			$data['roles'] = $this->role_model->list_roles();
			$data['permissions'] = $this->role_model->get_permissions();
			
			if ($this->uri->segment(3) === 'edit'){
				$data['role'] = $this->role_model->get_role($this->uri->segment(4));
				$data['current_permission'] = $this->role_model->get_permission($this->uri->segment(4));
			}
			else if ($this->uri->segment(3) === 'update'){
				$role_id = $this->input->post('role_id');
				$can_block_book = $this->input->post('can_bb');
				
				$this->role_model->set_permissions($role_id, $can_block_book);
				
				$this->db->cache_delete_all();
				redirect('admin/permissions');
				
			}
			
			$this->template->load('admin_template', 'admin/permissions', $data);
		}
		
		
	}
	
	function moderate(){
		$this->load->model('booking_model');
		
		if($this->uri->segment(3) === 'approve'){
			if($this->uri->segment(4) !== FALSE && is_numeric($this->uri->segment(4))){
				$ret_val = $this->booking_model->moderator_approve($this->uri->segment(4));
				
				if($ret_val === FALSE){
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This booking could not be approved. Another booking may exist at this time</div>');
					
					redirect('admin/moderate');
				}
				else{
					if(SEND_MODERATION_ACTION_EMAIL){
						$this->load->library('email');
						$this->load->model('room_model');
						
						$booking = $this->booking_model->get_booking($ret_val)->row();
						
						$room_data = $this->room_model->load_room($booking->room_id);
						

						
						//Send an email
						$data['name'] = $booking->booker_name;
						$data['start'] = $booking->start;
						$data['end'] = $booking->end;
						$data['room'] = $room_data;
						
						$email_content = $this->load->view('email/moderation_approved', $data, TRUE);
						$this->email->clear();
						$this->email->set_mailtype('html');
						$this->email->to($this->session->userdata('username').EMAIL_SUFFIX);
						$this->email->from(CONTACT_EMAIL);
						$this->email->subject('Your request is awaiting moderation');
						$this->email->message($email_content);
						$this->email->send();
					}
					
					
					
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">This booking has been approved!</div>');					
					redirect('admin/moderate');
				}
			}
			
		}
		else if($this->uri->segment(3) === 'deny'){
			if($this->uri->segment(4) !== FALSE && is_numeric($this->uri->segment(4))){
				//Save the data first, in order to be able to populate the email
				$mod_data = $this->booking_model->load_moderation_entry($this->uri->segment(4))->row();
				
				
				
				$result = $this->booking_model->moderator_deny($this->uri->segment(4));
				
				if($result !== FALSE){
					
					if(SEND_MODERATION_ACTION_EMAIL){
						$this->load->library('email');
						$this->load->model('room_model');
						
						$booking = $this->booking_model->get_booking($ret_val)->row();
						
						$room_data = $this->room_model->load_room($mod_data->room_id);
						

						
						//Send an email
						$data['name'] = $mod_data->booker_name;
						$data['start'] = $mod_data->start;
						$data['end'] = $mod_data->end;
						$data['room'] = $room_data;
						
						$email_content = $this->load->view('email/moderation_denied', $data, TRUE);
						$this->email->clear();
						$this->email->set_mailtype('html');
						$this->email->to($this->session->userdata('username').EMAIL_SUFFIX);
						$this->email->from(CONTACT_EMAIL);
						$this->email->subject('Your request is awaiting moderation');
						$this->email->message($email_content);
						$this->email->send();
					}
					
					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">This booking has been declined!</div>');	
				}
				else{
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This booking could not be denied.</div>');
				}
				
				
				redirect('admin/moderate');
			}
		}
		else{
			$data = array();
			$data['queue'] = $this->booking_model->get_moderation_queue(false);
		
			$this->template->load('admin_template', 'admin/moderation', $data);
		
		}

	}	
	
	function reports(){
		$this->load->model('log_model');
		$this->load->model('building_model');
		$this->load->model('room_model');
		$this->load->model('role_model');
		
		$data['buildings'] = $this->building_model->list_buildings();
		$data['rooms'] = $this->room_model->list_rooms();
		$data['roles'] = $this->role_model->list_roles();
		
		//Refine by buildings
		if($this->input->get('building') !== FALSE && is_numeric($this->input->get('building'))){
			$building_id = $this->input->get('building');
		}
		else{
			$building_id = null;
		}
		
		if($this->input->get('room') !== FALSE && is_numeric($this->input->get('room'))){
			$room_id = $this->input->get('room');
		}
		else{
			$room_id = null;
		}
		
		if($this->input->get('role') !== FALSE && is_numeric($this->input->get('role'))){
			$role_id = $this->input->get('role');
		}
		else{
			$role_id = null;
		}
		
		
		//Refine by start/end times
		if($this->input->get('start_date') !== FALSE && strlen($this->input->get('start_date')) > 0 && $this->input->get('end_date') !== FALSE && strlen($this->input->get('end_date')) > 0){
			$start_time = strtotime($this->input->get('start_date'));
			$end_time = strtotime($this->input->get('end_date'))+ ((24*60*60)-1); //Add a day (minus a second) to get as late as possible
		}
		else{
			$start_time = mktime(0,0,0,date('n'),1);
			$end_time = mktime(23,59,59,date('n')+1,0);
		}
		
		$data['total_bookings'] = $this->log_model->total_bookings($start_time, $end_time, $building_id, $room_id, $role_id);
		$data['total_checkouts'] = $this->log_model->total_checkouts($start_time, $end_time, $building_id, $room_id, $role_id);
		$data['usage_by_hour'] = $this->log_model->usage_by_hour($start_time, $end_time, $building_id, $room_id, $role_id);
		$data['usage_by_type'] = $this->log_model->report_by_device($start_time, $end_time); 
		$data['days_booked_ahead'] = $this->log_model->days_booked_ahead($start_time, $end_time, $building_id, $room_id, $role_id);
		$data['usage_by_seats'] = $this->log_model->usage_by_seats($start_time, $end_time, $building_id, $room_id, $role_id);
		$data['ratio_by_seats'] = $this->log_model->ratio_by_seats($start_time, $end_time, $building_id, $room_id, $role_id);
		
	
	
		
		
		
		$this->template->load('admin_template', 'admin/reports', $data);
	}
	
	function auth_denied(){
		$this->load->model('log_model');
		
		$data = array();
		
		
		if(is_numeric($this->input->get('start')) && $this->input->get('start') >= 0){
			$data['start'] = $this->input->get('start');
			
			if(is_numeric($this->input->get('end')) && $this->input->get('end') > $data['start']){
				$data['end'] = $this->input->get('end');
			}
			else{
				$data['end'] = $data['start'] + 50;
			}
		}
		else{
			$data['start'] = 0;
			$data['end'] = 50;
		}
		
		
		
		
		
		$data['events'] = $this->log_model->login_denied_events($data['start'], $data['end']);
		
		$this->template->load('admin_template', 'admin/auth_denied', $data);
	}
	
	function error_logs(){
		$this->load->model('log_model');
		
		if($this->uri->segment(3) !== false){
			$data['log_data'] = $this->log_model->get_log($this->uri->segment(3));
		}
		else{
			$data = array();
			$data['error_files'] = $this->log_model->get_error_files();
		
			
		}
		
		$this->template->load('admin_template', 'admin/error_logs', $data);
	}
	
	function filter_stats(){
		$data = $this->db->query('select data, count(*) as c from room_booking.log where action="Filter" group by data order by 2 desc');
		
		$arr = array();
		
		foreach($data->result() as $row){
			$json = json_decode($row->data);
			
			
			
			
			if(is_object($json)){
				foreach($json as $key => $value){
					if(is_array($value)){
						foreach($value as $sub_key => $sub_value){
							 if(!isset($arr[$key])){
								$arr[$key][$sub_value] = $row->c;
							}
							else if(isset($arr[$key]) && !isset($arr[$key][$sub_value])){
								$arr[$key][$sub_value] = $row->c;
							}
							else{
								$arr[$key][$sub_value] += $row->c;
							}
						}
					}
					
					else if(!isset($arr[$key])){
						
						$arr[$key][$value] = $row->c;
					}
					else if(isset($arr[$key]) && !isset($arr[$key][$value])){
						$arr[$key][$value] = $row->c;
					}
					else{
						$arr[$key][$value] += $row->c;
					}
				}
			}
			
			
		}
		
		var_dump($arr);
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */