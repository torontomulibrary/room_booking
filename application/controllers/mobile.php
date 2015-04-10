<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile extends CI_Controller {

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
	function Mobile(){
		parent::__construct();
		
		$this->load->helper('mobile_message');
		
		//Check for existing login
		if(!strlen($this->session->userdata('username')) > 0){
			$this->session->set_flashdata('origin', current_url());
			redirect('login/login_user');
		}
		
		
		//If site constant is set to debug, enable the profiler (gives analytics for page load). 
		//DO NOT USE ON LIVE SITE
		if($this->input->get('debug') !== false) $this->output->enable_profiler(DEBUG_MODE);
	}

	
	public function index(){
		
		$this->template->load('mobile_template', 'mobile/mobile_main');
	}
	
	function my_bookings(){
		$this->load->model('booking_model');
		
		$data['bookings'] = $this->booking_model->get_upcoming_bookings($this->session->userdata('username'));
		
		$this->template->load('mobile_template', 'mobile/my_bookings', $data);
	}
	
	function book_room(){
		$this->load->model('hours_model');
		$this->load->model('role_model');
		$this->load->model('room_model');
		$this->load->model('booking_model');
			
		
		$data = array();
		
		if($this->input->get('selected_date') !== FALSE && strtotime($this->input->get('selected_date')) !== FALSE){
			$data['hours'] = $this->hours_model->getAllHours(strtotime($this->input->get('selected_date')));
		}
		
		
		
		if($this->input->get('selected_date') !== FALSE && $this->input->get('set_time') !== FALSE){
			$data['roles'] = $this->role_model->list_roles();
			
			//Load the room data for every role the user has
			foreach ($data['roles']->result() as $role){
				$rooms = $this->room_model->list_rooms_by_role($role->role_id, true);
				
				foreach ($rooms->result() as $room){
					$data['rooms'][$role->role_id][] = $room;
				}
			}
			
			//Get all block bookings
			$data['block_bookings'] = $this->booking_model->list_block_bookings(strtotime($this->input->get('selected_date')));
			
			
			//Return all bookings for the day (as an associative array for easy retrieval) 
			$bookings = $this->booking_model->get_bookings(date('Ymd',strtotime($this->input->get('selected_date'))));
			
			$data['bookings'] = array();
			
			foreach($bookings->result() as $booking){
				$data['bookings'][$booking->room_id][strtotime($booking->start)] = $booking; 
			}
		}
		
		$this->template->load('mobile_template', 'mobile/book_room', $data);
	}
	
	function create_booking(){
		$data['date'] = $this->input->get('selected_date');
		
		$this->template->load('mobile_template', 'mobile/find_slot', $data);
	}
	
	function edit_booking(){
		$this->load->model('booking_model');
		
		
		
		if($this->input->get('booking_id') === FALSE || !is_numeric($this->input->get('booking_id'))){
			$this->session->set_flashdata('warning', "An error has occured. ");
			redirect(base_url().'mobile');
		}
		
		$booking_data = $this->booking_model->get_booking($this->input->get('booking_id'));
		
		if($booking_data->num_rows == 0){
			$this->session->set_flashdata('warning', "An error has occured.");
			redirect(base_url().'mobile');
		}
		
		$data['booking'] = $booking_data->row();
		
		if(strtotime($data['booking']->end) < time()){			
			$this->session->set_flashdata('warning', "Cannot edit bookings in the past");
			 redirect(base_url().'mobile');
		}
		
		//Check for admin status
		if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
			//See if user made this booking
			if($this->session->userdata('username') !== $data['booking']->matrix_id){
				redirect(base_url().'mobile');
			}

		}
		
		$this->load->model('room_model');
		$this->load->model('resource_model');
		$this->load->model('building_model');
		
		$data['room'] = $this->room_model->load_room($data['booking']->room_id);
		$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
		$data['building'] = $this->building_model->load_building($data['room']['room_data']->row()->building_id);
		
		$data['vars'] = $data;
		
		$this->template->load('mobile_template', 'mobile/edit_booking', $data);
		
		
	}
	
	function cancel_booking(){
		$this->load->model('booking_model');
		
		if(!is_numeric($this->input->get('booking_id'))){
			$this->session->set_flashdata('warning', "An error has occured. The booking has not been deleted");
			redirect(base_url().'booking/booking_main');
		}
		
		//====Make sure user has permission to delete this booking======
		$booking_data = $this->booking_model->get_booking($this->input->get('booking_id'));
		$data['booking'] = $booking_data->row();
		
		if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
			if($this->session->userdata('username') !== $data['booking']->matrix_id){
				$this->session->set_flashdata('warning', "An error has occured. The booking has not been deleted");
				redirect(base_url().'booking/booking_main');
			}

		}
		//======END PERMISSION CHECK======================================
		
		//====Make sure that the booking is not in the past, or currently underway===
		
		
		if(strtotime($data['booking']->start) < time()){
			$this->session->set_flashdata('warning', "Cannot delete bookings in the past");
			 redirect(base_url().'booking/booking_main');
		}
		//=====END TIME CHECK===========================================
		
		$this->booking_model->delete_booking($this->input->get('booking_id')); 
		
		$this->session->set_flashdata('success', "Booking Deleted");
		redirect(base_url().'mobile');
	}
	
	
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */