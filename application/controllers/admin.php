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
		$this->load->model('user_model'); //Should this be done in the login process?
		
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
		$this->template->load('admin_template', 'admin/dashboard');
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
		$this->load->model('role_model');
		$this->load->model('user_model');
		
		if($this->uri->segment(3) === 'add'){
			$matrix = $this->input->post('matrix');
			$admin = $this->input->post('admin');
			$role = $this->input->post('role');
	
			$id = $this->user_model->add_user($matrix, $admin, $role);
			
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
	
			$id = $this->user_model->edit_user($user_id, $matrix, $admin, $role);
			
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The user has been updated</div>');
			$this->db->cache_delete_all();
			redirect('admin/users');
		}
		
		$data['users'] = $this->user_model->list_users();
		$data['roles'] = $this->role_model->list_roles();
		
		$this->template->load('admin_template', 'admin/users', $data);
	}
	
	function roles(){
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
	
	function rooms(){
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
			
			$id = $this->room_model->add_room($building, $room, $seats, $role, $active, $resources, $max_daily_hours);
			
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
			
			
			
			$id = $this->room_model->edit_room($room_id, $building, $room, $seats, $roles, $active, $resources, $max_daily_hours);

			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">The room has been updated</div>');
			$this->db->cache_delete_all();
			redirect('admin/rooms');
		}
		
		
		$data['rooms'] = $this->room_model->list_rooms();
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
		//Deny access if user is not super admin
		if(!$this->session->userdata('super_admin')){
			$this->template->load('admin_template', 'admin/denied');
		}
		else{
			$this->load->model('booking_model');
			$this->load->model('room_model');
			
			if($this->uri->segment(3) === 'add'){
				$reason = $this->input->post('reason');
				$start = $this->input->post('start');
				$end = $this->input->post('end');
				$rooms = $this->input->post('rooms');
				
				$status = $this->booking_model->add_block_booking($reason,$start,$end, $rooms);
				
				
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
				$data['rooms'] = $this->room_model->list_rooms();
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
					$data['rooms'] = $this->room_model->list_rooms();
				
					$data['current_bb'] = $this->booking_model->get_block_booking($this->uri->segment(4));
					
					
					
					if(count($data) == 0){
						$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Invalid resource ID</div>');
						$this->db->cache_delete_all(); //Delete all cache to take care of foreign keys
						redirect('admin/block_booking');
					}
				}
			}
			
			else if ($this->uri->segment(3) === 'update'){
				$reason = $this->input->post('reason');
				$start = $this->input->post('start');
				$end = $this->input->post('end');
				$rooms = $this->input->post('rooms');
				$id = $this->input->post('block_booking_id');
				
				$status = $this->booking_model->edit_block_booking($reason,$start,$end, $rooms, $id);
				
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
	
	function reports(){
		echo "Coming soon!";
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */