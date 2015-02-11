<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Booking extends CI_Controller {

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
	function Booking(){
		parent::__construct();
	
		//Check for existing login
		if(!strlen($this->session->userdata('username')) > 0){
			$this->session->set_flashdata('origin', current_url());
			redirect('login/login_user');
		}
	
		//Check to see if the user is an administrator
		$this->load->model('user_model'); //Should this be done in the login process?
		
		
	
		//If site constant is set to debug, enable the profiler (gives analytics for page load). 
		//DO NOT USE ON LIVE SITE
		if($this->input->get('debug') !== false) $this->output->enable_profiler(DEBUG_MODE);
		//$this->output->enable_profiler(DEBUG_MODE);
	}

	
	public function index(){
		$this->load->model('room_model');
		$this->load->model('role_model');
		$this->load->model('booking_model');
		$this->load->model('resource_model');
		$this->load->model('hours_model');
		$this->load->library('calendar'); 
		
			
		
		//Pull in all the rooms the current user is allowed to view!
		$data['roles'] = $this->role_model->list_roles();
		$data['rooms'] = array();
		
		$data['resources_filter'] = $this->resource_model->list_resources(true);
		$data['buildings'] = $this->room_model->list_buildings();
		
		//Load the room data for every role the user has
		foreach ($data['roles']->result() as $role){
			$rooms = $this->room_model->list_rooms_by_role($role->role_id, true);
			
			foreach ($rooms->result() as $room){
				$data['rooms'][$role->role_id][] = $room;
				
				//Load the resources for each room (Mildly database intensive, but cached permanently after first load)
				$data['resources'][$room->room_id] = $this->resource_model->list_room_resources($room->room_id);
			}
		}
		
		//Generate the calendar needed
		if($this->input->get('month') !== false){
			if($this->input->get('date') !== false){
				
				
				//Return all bookings for the day (as an associative array for easy retrieval) 
				$bookings = $this->booking_model->get_bookings($this->input->get('date', TRUE));
				
				foreach($bookings->result() as $booking){
					$data['bookings'][$booking->room_id][strtotime($booking->start)] = $booking; 
				}
				
				$current_month = date_parse_from_format('Ymd', $this->input->get('date', TRUE));
				$data['calendar'] = $this->calendar->drawCalendar( $current_month['month'], $current_month['year'], $current_month['day']);
				
				//Load the hours for the selected date
				$data['hours'] = $this->hours_model->getAllHours(mktime(0,0,0, $current_month['month'],$current_month['day'],$current_month['year']));
				
				$data['block_bookings'] = $this->booking_model->list_block_bookings(mktime(0,0,0, $current_month['month'],$current_month['day'],$current_month['year']));
			}
			else{
				$current_month = date_parse_from_format('Ym', $this->input->get('month', TRUE));
				$data['calendar'] = $this->calendar->drawCalendar( $current_month['month'], $current_month['year'] );
			}
		}
		else{
			$data['calendar'] = $this->calendar->drawCalendar();
		}
		
		$this->template->load('rula_template', 'booking/booking_main', $data);
	}
	
	function book_room(){
		$this->load->model('booking_model');
		
		//User is NOT allowed to make bookings in this room. Redirect to base url
		if(!$this->booking_model->is_allowed($this->input->get('room_id'))){
			redirect(base_url());
		}
		
		if($this->input->get('slot') === FALSE || !is_numeric($this->input->get('slot')) || $this->input->get('room_id') === FALSE || !is_numeric($this->input->get('room_id'))){
			//Bad data, do something
		}
		else{
			$this->load->model('room_model');
			
			$this->load->model('resource_model');
			
			$data['room'] = $this->room_model->load_room($this->input->get('room_id'));
			$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
			$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), $this->input->get('slot'));
			$data['next_booking'] = $this->booking_model->next_booking($this->input->get('slot'));
			
			
			//var_dump($data); die;
			$this->template->load('rula_template', 'booking/book_room_form', $data);
		}
	}
	
	function submit(){
		$this->load->model('booking_model');
		$this->load->model('room_model');
		
		$start_time = $this->input->post('slot');
		$finish_time = $this->input->post('finish_time');
		$room_id = $this->input->post('room_id');
		$comment = $this->input->post('comment');
		
		
		
		//TODO: make sure time isnt past midnight, or before opening hours
		
		//Validate all the date/times submitted 
		if(is_numeric($start_time) && ($start_time % 1800) == 0 && is_numeric($finish_time) && ($finish_time % 1800) == 0 && is_numeric($room_id)){
			//Was this user allowed to book this room?
			if($this->booking_model->is_allowed($room_id)){
				$room = $this->room_model->load_room($room_id)['room_data']->row();
				
				//Check the users remaining bookable hours
				$limits = $this->booking_model->remaining_hours($this->session->userdata('username'), $start_time);
				$requested_time = (($finish_time - $start_time) / 60 / 60);
				
				if(( $room->max_daily_hours - $limits['day_used'] ) < 0 || ($limits['week_remaining'] - $requested_time) < 0 ){
					$this->session->set_flashdata('danger', "You have exceeded your booking limits. The booking has not been made");
					redirect(base_url() . 'booking?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
				}
				else{
					//Try to make the booking
					$id = $this->booking_model->book_room($room_id, $start_time, $finish_time, $comment);
					
					if($id === FALSE){
						$this->session->set_flashdata('warning', "Another booking already exists for this time. Please choose a different room/time");
						redirect(base_url() . 'booking?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
					}
					else{
						
						$this->load->library('email');
						$this->load->model('room_model');
						$room = $this->room_model->load_room($room_id);
						
						//Send an email
						$data['name'] = $this->session->userdata('name');
						$data['start'] = $start_time;
						$data['end'] = $finish_time;
						$data['room'] = $room;
						
						$this->booking_model->generate_ics($id);
						
						$email_content = $this->load->view('email/booking_confirmation', $data, TRUE);
						$this->email->clear();
						$this->email->to($this->session->userdata('username').EMAIL_SUFFIX);
						$this->email->from('noreply'.EMAIL_SUFFIX);
						$this->email->subject('Booking Confirmation');
						$this->email->message($email_content);
						$this->email->attach('temp/'.$id.'.ics');
						$this->email->send();
						$this->booking_model->delete_ics($id);
						
						
						
						$this->session->set_flashdata('success', "Booking Successfully Made");
						redirect(base_url() . 'booking?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
					}
				}
			}
			else{
				$this->session->set_flashdata('danger', "You do not have permissions to book this room");
				redirect(base_url() . 'booking?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
			}
		}
		else{
			$this->session->set_flashdata('warning', "An error has occured. The booking has not been made");
			redirect(base_url());
		}
	}
	
	function edit_room(){
		$this->load->model('booking_model');
		
		
		
		if($this->input->get('booking_id') === FALSE || !is_numeric($this->input->get('booking_id'))){
			redirect(base_url());
		}
		
		$booking_data = $this->booking_model->get_booking($this->input->get('booking_id'));
		
		if($booking_data->num_rows == 0){
			redirect(base_url());
		}
		
		$data['booking'] = $booking_data->row();
		
		//Check for admin status
		if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
			//See if user made this booking
			if($this->session->userdata('username') !== $data['booking']->matrix_id){
				redirect(base_url());
			}

		}
		
		$this->load->model('room_model');
		$this->load->model('resource_model');
		
		$data['room'] = $this->room_model->load_room($data['booking']->room_id);
		$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
		$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), strtotime($data['booking']->start));
		
		$this->template->load('rula_template', 'booking/edit_book_room_form', $data);
	}

}