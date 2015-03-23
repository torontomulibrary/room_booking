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

	
	public function booking_main(){
		//Default to today if no date is selected
		if($this->input->get('month') === FALSE && $this->input->get('date') === FALSE){
			
			//Keep flashdata if redirecting to "today"
			foreach($this->session->all_userdata() as $key => $val){
			  if(strpos($key,'flash:old:') > -1){ // key is flashdata
				$item = substr($key , strlen('flash:old:'));
				$this->session->keep_flashdata($item);
			  }
			}
			
			redirect(base_url() . 'booking/booking_main?month='. date('Ym') .'&date='. date('Ymd'));
		}
		
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
				
				$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), strtotime($this->input->get('date')));
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
	
	function index(){
		$this->load->model('booking_model');
		
		$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), time());
		
		$this->template->load('rula_template', 'booking/landing_page', $data);
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
			$this->load->model('hours_model');
			$this->load->model('resource_model');
			$this->load->model('building_model');
			
			$data['hours'] = $this->hours_model->getAllHours(mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))));
			
			$data['room'] = $this->room_model->load_room($this->input->get('room_id'));
			
			$data['building'] = $this->building_model->load_building($data['room']['room_data']->row()->building_id);
			$data['hours'] = $this->hours_model->getAllHours(mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))));
			
			$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
			$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), $this->input->get('slot'));
			$data['next_booking'] = $this->booking_model->next_booking($this->input->get('slot'), $this->input->get('room_id'));
			
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
				$room = $this->room_model->load_room($room_id);
				$room = $room['room_data']->row(); 
				
				//Check the users remaining bookable hours
				$limits = $this->booking_model->remaining_hours($this->session->userdata('username'), $start_time);
				$requested_time = (($finish_time - $start_time) / 60 / 60);
				
				if(( $room->max_daily_hours - $limits['day_used'] ) < 0 || ($limits['week_remaining'] - $requested_time) < 0 ){
					$this->session->set_flashdata('danger', "You have exceeded your booking limits. The booking has not been made");
					redirect(base_url() . 'booking/booking_main?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
				}
				else{
					//Try to make the booking
					
					//Was this a new booking, or an edit?
					if($this->input->post('booking_id') !== FALSE && is_numeric($this->input->post('booking_id'))){
						
						//Check if user was allowed to make this booking
						$data['booking'] = $this->booking_model->get_booking($this->input->post('booking_id'));
						
						$booking = $data['booking']->row();
						
						if(strtotime($data['booking']->start) < time()){
							$this->session->set_flashdata('warning', "Cannot edit bookings in the past");
							 redirect(base_url().'booking/booking_main');
						}
						
						if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
							//See if user made this booking, if not, redirect them to to the homepage
							if($this->session->userdata('username') !== $data['booking']->row()->matrix_id){
								redirect(base_url().'booking/booking_main');
							}
						}
						$id = $this->booking_model->edit_booking($room_id, $start_time, $finish_time, $comment, $this->input->post('booking_id'), $booking->matrix_id, $booking->booker_name);
						
						if($id !== FALSE) $id = $this->input->post('booking_id'); 
					}
						
					
					else{
						//Get the ID of the new booking. Returns false if the booking slot was not free
						$id = $this->booking_model->book_room($room_id, $start_time, $finish_time, $comment); 
					}
					
					
					if($id === FALSE){
						$this->session->set_flashdata('warning', "Another booking already exists for this time. Please choose a different room/time");
						redirect(base_url() . 'booking/booking_main?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
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
						redirect(base_url() . 'booking/booking_main?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
					}
				}
			}
			else{
				$this->session->set_flashdata('danger', "You do not have permissions to book this room");
				redirect(base_url() . 'booking/booking_main?month='.date('Ym', $start_time).'&date='.date('Ymd',$start_time));
			}
		}
		else{
			$this->session->set_flashdata('warning', "An error has occured. The booking has not been made");
			redirect(base_url().'booking/booking_main');
		}
	}
	
	function edit_booking(){
		$this->load->model('booking_model');
		
		
		
		if($this->input->get('booking_id') === FALSE || !is_numeric($this->input->get('booking_id'))){
			$this->session->set_flashdata('warning', "An error has occured. ");
			redirect(base_url().'booking/booking_main');
		}
		
		$booking_data = $this->booking_model->get_booking($this->input->get('booking_id'));
		
		if($booking_data->num_rows == 0){
			$this->session->set_flashdata('warning', "An error has occured.");
			redirect(base_url().'booking/booking_main');
		}
		
		$data['booking'] = $booking_data->row();
		
		if(strtotime($data['booking']->end) < time()){			
			$this->session->set_flashdata('warning', "Cannot edit bookings in the past");
			 redirect(base_url().'booking/booking_main');
		}
		
		//Check for admin status
		if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
			//See if user made this booking
			if($this->session->userdata('username') !== $data['booking']->matrix_id){
				redirect(base_url().'booking/booking_main');
			}

		}
		
		$this->load->model('room_model');
		$this->load->model('hours_model');
		$this->load->model('resource_model');
		$this->load->model('building_model');
		
		$data['room'] = $this->room_model->load_room($data['booking']->room_id);
		$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
		$data['limits'] = $this->booking_model->remaining_hours($data['booking']->matrix_id, strtotime($data['booking']->start));
		$data['next_booking'] = $this->booking_model->next_booking(strtotime($data['booking']->start), $data['booking']->room_id);
		
		$data['building'] = $this->building_model->load_building($data['room']['room_data']->row()->building_id);
		$data['hours'] = $this->hours_model->getAllHours(mktime(0,0,0, date('n',strtotime($data['booking']->start)),date('j',strtotime($data['booking']->start)),date('Y',strtotime($data['booking']->start))));
		
		$this->template->load('rula_template', 'booking/edit_book_room_form', $data);
	}
	
	function checkout(){
		$this->load->model('booking_model');
		
		//Check if user was allowed to make this booking
		$data['booking'] = $this->booking_model->get_booking($this->input->post('booking_id'));
		
		$booking = $data['booking']->row();
		
		if(time() > strtotime($booking->start) && time() < strtotime($booking->end)){
			if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
				//See if user made this booking, if not, redirect them to to the homepage
				if($this->session->userdata('username') !== $booking->matrix_id){
					$this->session->set_flashdata('warning', "You do not have permissions to edit this booking");
					redirect(base_url().'booking/booking_main');
				}
			}
			$this->booking_model->checkout($this->input->post('booking_id'));
			$this->session->set_flashdata('success', "You have checked out!");
			redirect(base_url().'booking/booking_main');
			
		}
		else{
			$this->session->set_flashdata('warning', "Cannot checkout. Booking has already ended");
			 redirect(base_url().'booking/booking_main');
		}
		
		
	}
	
	function delete_booking(){
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
		redirect(base_url().'booking/booking_main');
		
	}

}