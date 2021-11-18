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
	function __construct(){
		parent::__construct();
		
		$this->load->library('cas');
		if(!$this->cas->is_authenticated()){
			$this->session->unset_userdata('username');
			$this->session->set_flashdata('origin', current_url());
			redirect('login/login_user');
		}
		
		$this->load->helper('mobile_message');
		
		//Check for existing login
		if(!strlen($this->session->userdata('username')) > 0){
			$this->session->set_flashdata('origin', current_url());
			redirect('login/login_user');
		}
		
		
		//If site constant is set to debug, enable the profiler (gives analytics for page load). 
		if($this->input->get('debug') !== NULL) $this->output->enable_profiler($this->settings_model->check_debug());	
	}

	
	public function index(){
		$this->load->model('role_model');
		
		$data['theme'] =  str_replace("_template", "", $this->role_model->get_theme());
		
		$data['policy_url'] = $this->role_model->get_policy_url(); 
		$data['settings'] = $this->settings_model->get_all_settings_array();

		$this->template->load('mobile_template', 'mobile/mobile_main', $data);
	}
	
	function my_bookings(){
		$this->load->model('booking_model');
		$this->load->model('role_model');
		
		$data['theme'] =  str_replace("_template", "", $this->role_model->get_theme());
		
		$data['bookings'] = $this->booking_model->get_upcoming_bookings($this->session->userdata('username'));
		$data['settings'] = $this->settings_model->get_all_settings_array();

		$this->template->load('mobile_template', 'mobile/my_bookings', $data);
	}
	
	function next_available(){
		$this->load->model('hours_model');
		$this->load->model('role_model');
		$this->load->model('room_model');
		$this->load->model('booking_model');
			
		$data = array();
		$data['roles'] = $this->role_model->list_roles();
		$data['theme'] =  str_replace("_template", "", $this->role_model->get_theme());
		
		$booking_data = array();
		
		//Get the next half hour slot
		$time = (time() - (time() % 1800)) + 1800;

		$num_slots = 6; //6 half hours for 3 hours in future
		
		//Get all the room data for each role
		foreach ($data['roles']->result() as $role){
				$raw_rooms = $this->room_model->list_rooms_by_role($role->role_id, true);
				
				foreach ($raw_rooms->result() as $room){
					$data['r'][$role->role_id][] = $room;
				}
		}
		
		for($i=$num_slots; $i > 0; $i--){
			//Prepare the data here, rather then call these functions in triplicate
			$data['hours'] = $this->hours_model->getAllHours($time);
			$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), $time);
			$data['block_bookings'] = $this->booking_model->list_block_bookings($time, false, true);
			$data['recurring_bookings'] = $this->booking_model->list_block_bookings($time, false, true, true);
			$data['bookings'] = $this->booking_model->get_bookings(date('Ymd',$time));
			
			foreach($data['bookings']->result() as $booking){
				$booking_data[$booking->room_id][strtotime($booking->start)] = $booking; 
			}
			
			$data['bookings'] = $booking_data; 
			
			$data['rooms'][$time] = $this->booking_model->get_random_free_bookings($time, 1, 4, 2, $data); //1-4 seats
			$data['rooms'][$time] = array_merge($data['rooms'][$time], $this->booking_model->get_random_free_bookings($time, 5, 8, 2, $data)); //5-8 seats
			$data['rooms'][$time] = array_merge($data['rooms'][$time], $this->booking_model->get_random_free_bookings($time, 9, 16, 2, $data));//9-16 seats
			
			$time += 1800; //Add 30mins to the time;
		}
		$data['settings'] = $this->settings_model->get_all_settings_array();
		$this->template->load('mobile_template', 'mobile/next_booking', $data);
	}
	
	function book_room(){
		$this->load->model('hours_model');
		$this->load->model('role_model');
		$this->load->model('room_model');
		$this->load->model('booking_model');
			
		
		$data = array();
		
		$data['roles'] = $this->role_model->list_roles();
		$data['theme'] =  str_replace("_template", "", $this->role_model->get_theme());
		
		if($this->input->get('selected_date') !== NULL && strtotime($this->input->get('selected_date')) !== NULL){
			$data['hours'] = $this->hours_model->getAllHours(strtotime($this->input->get('selected_date')));
		}		
		
		
		
		if($this->input->get('selected_date') !== NULL && $this->input->get('set_time') !== NULL){
			
			$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), $this->input->get('set_time'));
			
			//Load the room data for every role the user has
			foreach ($data['roles']->result() as $role){
				$rooms = $this->room_model->list_rooms_by_role($role->role_id, true);
				
				foreach ($rooms->result() as $room){
					$data['rooms'][$role->role_id][] = $room;
				}
			}
			
			//Get all block bookings
			$data['block_bookings'] = $this->booking_model->list_block_bookings(strtotime($this->input->get('selected_date')), false, true);
			$data['recurring_bookings'] = $this->booking_model->list_block_bookings(strtotime($this->input->get('selected_date')), false, true, true);
			
			
			//Return all bookings for the day (as an associative array for easy retrieval) 
			$bookings = $this->booking_model->get_bookings(date('Ymd',strtotime($this->input->get('selected_date'))));
			
			$data['bookings'] = array();
			
			foreach($bookings->result() as $booking){
				$data['bookings'][$booking->room_id][strtotime($booking->start)] = $booking; 
			}
		}
		$data['settings'] = $this->settings_model->get_all_settings_array();
		$this->template->load('mobile_template', 'mobile/book_room', $data);
	}
	
	function create_booking(){
		if(!is_numeric($this->input->get('room_id'))) return false;
		
		$this->load->model('room_model');
		$this->load->model('resource_model');
		$this->load->model('building_model');
		$this->load->model('booking_model');
		$this->load->model('hours_model');
		$this->load->model('interface_model');
		
		
		$data['interface'] = $this->interface_model->get_fields($this->input->get('room_id'));
		$data['date'] = $this->input->get('selected_date');
		$data['limits'] = $this->booking_model->remaining_hours($this->session->userdata('username'), $this->input->get('slot'));
		$data['room'] = $this->room_model->load_room($this->input->get('room_id'));
		$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
		$data['building'] = $this->building_model->load_building($data['room']['room_data']->row()->building_id);
		$data['next_booking'] = $this->booking_model->next_booking($this->input->get('slot'), $this->input->get('room_id'));
		$data['hours'] = $this->hours_model->getAllHours(mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))));
		$data['settings'] = $this->settings_model->get_all_settings_array();
		
		$this->template->load('mobile_template', 'mobile/booking_form', $data);
	}
	
	function edit_booking(){
		$this->load->model('booking_model');
		$this->load->model('interface_model');
		
		
		if($this->input->get('booking_id') === NULL || !is_numeric($this->input->get('booking_id'))){
			$this->session->set_flashdata('warning', "An error has occured. ");
			redirect(base_url().'mobile');
		}
		
		$booking_data = $this->booking_model->get_booking($this->input->get('booking_id'));
		
		if($booking_data->num_rows() == 0){
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
		$data['interface'] = $this->interface_model->get_fields($data['booking']->room_id);
		$data['custom_data'] = $this->booking_model->get_custom_fields_data($this->input->get('booking_id'));
		$data['vars'] = $data;
		
		$data['settings'] = $this->settings_model->get_all_settings_array();
		$this->template->load('mobile_template', 'mobile/edit_booking', $data);
		
		
	}
	
	function submit(){
		$this->load->model('booking_model');
		$this->load->model('room_model');
		$this->load->model('interface_model');
		
		$start_time = $this->input->post('slot');
		$finish_time = $this->input->post('end_time');
		$room_id = $this->input->post('room_id');
		$comment = $this->input->post('add_info');
		
		
		//Get customized fields
		if(!is_numeric($room_id)) return false;
		$fields = $this->interface_model->get_fields($room_id);
		
		$user_data = array();
		foreach($fields->result() as $field){
			$user_data[] = array($field->fc_id, $this->input->post('fc_'.$field->fc_id));
		}
		
		//Correct for the offset of times
		if(is_numeric($room_id)){
			$room = $this->room_model->load_room($room_id);
			$room = $room['room_data']->row(); 
			
			if($room->requires_moderation){
				$time = time() + MODERATION_TIME_DELAY;
			}
			else{
				$time = time() + TIME_DELAY;
			}
		}
		
		//Validate all the date/times submitted 
		if(is_numeric($start_time) && ($start_time % 1800) == 0 && is_numeric($finish_time) && ($finish_time % 1800) == 0 && is_numeric($room_id) && $start_time > time()){
			//Was this user allowed to book this room?
			if($this->booking_model->is_allowed($room_id)){
				//Check the users remaining bookable hours
				$limits = $this->booking_model->remaining_hours($this->session->userdata('username'), $start_time);
				$requested_time = (($finish_time - $start_time) / 60 / 60);
				
				if(( $room->max_daily_hours - $limits['day_used'] ) < 0 || ($limits['week_remaining'] - $requested_time) < 0 ){
					$this->session->set_flashdata('danger', "You have exceeded your booking limits. The booking has not been made");
					redirect(base_url() . 'mobile');
				}
				else{
					//Try to make the booking
					if($room->requires_moderation == TRUE){
							$id = $this->booking_model->add_to_moderation_queue($room_id, $start_time, $finish_time, $user_data);
							
							if(is_numeric($id)){
								$this->session->set_flashdata('success', "Your request is awaiting approval!");
								
								if(SEND_MODERATION_REQUEST_CONFIRMATION_EMAIL){
									$this->load->library('email');
									$this->load->model('room_model');
									$room = $this->room_model->load_room($room_id);
									
									//Send an email
									$data['name'] = $this->session->userdata('name');
									$data['start'] = $start_time;
									$data['end'] = $finish_time;
									$data['room'] = $room;
									
									$email_content = $this->load->view('email/awaiting_moderation', $data, TRUE);
									$this->email->clear();
									$this->email->set_mailtype('html');
									$this->email->to($this->session->userdata('username').EMAIL_SUFFIX);
									$this->email->from(REPLY_EMAIL);
									$this->email->subject('Your request is awaiting moderation');
									$this->email->message($email_content);
									$this->email->send();
								}
							}
							else{
								$this->session->set_flashdata('warning', "This booking cannot be added. Conflicting booking");
							}
							
							redirect(base_url() . 'mobile');
						}
						else{
							//Get the ID of the new booking. Returns false if the booking slot was not free
							$id = $this->booking_model->book_room($room_id, $start_time, $finish_time, $user_data); 
							
							$log_data = json_encode(array(
									"booking_id" => $id,
									"room_id" => $room_id,
									"matrix_id" => $this->session->userdata('username'),
									"booker_name" => $this->session->userdata('name'),
									"start" =>date('Y-m-d H:i:s', $start_time),
									"end" =>date('Y-m-d H:i:s', $finish_time),
								));
							
							$this->load->model('log_model');
							$this->log_model->log_event('mobile', $this->session->userdata('username'), "Create Booking", $id, $log_data);
						}
					if($id === FALSE){
						$this->session->set_flashdata('warning', "Another booking already exists for this time. Please choose a different room/time");
						redirect(base_url() . 'mobile');
					}
					else{
						
						$this->load->library('email');
						$this->load->model('room_model');
						$this->load->model('role_model');
						$room = $this->room_model->load_room($room_id);
						
						//Send an email
						$data['name'] = $this->session->userdata('name');
						$data['start'] = $start_time;
						$data['end'] = $finish_time;
						$data['room'] = $room;
						$data['settings'] = $this->settings_model->get_all_settings_array();
						$this->booking_model->generate_ics($id);
						
						//Load in the email template
						$email_template = $this->role_model->get_email_template($room_id);
							
						$email_content = $this->load->view('email/'.$email_template, $data, TRUE);
						$this->email->clear();
						$this->email->set_mailtype('html');
						$this->email->to($this->session->userdata('username').EMAIL_SUFFIX);
						$this->email->from('noreply'.EMAIL_SUFFIX);
						$this->email->subject('Booking Confirmation');
						$this->email->message($email_content);
						$this->email->attach('temp/'.$id.'.ics');
						$this->email->send();
						$this->booking_model->delete_ics($id);
						
						
						
						$this->session->set_flashdata('success', "Booking Successfully Made");
						redirect(base_url() . 'mobile');
					}
				}
			}
			else{
				$this->session->set_flashdata('danger', "You do not have permissions to book this room");
				redirect(base_url() . 'mobile');
			}
		}
		else{
			$this->session->set_flashdata('warning', "An error has occured. The booking has not been made");
			redirect(base_url() . 'mobile');
		}
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
		
		if($data['booking'] === FALSE || $booking_data->num_rows() == 0){
			$this->session->set_flashdata('warning', "An error has occured. The booking has not been deleted");
			redirect(base_url() . 'mobile');
		}
		
		if(!$this->session->userdata('super_admin') || !$this->session->userdata('admin')){
			if($this->session->userdata('username') !== $data['booking']->matrix_id){
				$this->session->set_flashdata('warning', "An error has occured. The booking has not been deleted");
				redirect(base_url() . 'mobile');
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
		
		//Prepare log string
		$log_data = json_encode($data['booking']);
		
		
		$this->load->model('log_model');
		$this->log_model->log_event('mobile', $this->session->userdata('username'), "Delete Booking", $this->input->get('booking_id'), $log_data);
		
		$this->session->set_flashdata('success', "Booking Deleted");
		redirect(base_url().'mobile');
	}
	
	
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */