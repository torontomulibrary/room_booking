<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/admin
	 *	- or -  
	 * 		http://example.com/index.php/admin/index
	 *	- or -
	**/
	 
	
	function Api(){
		parent::__construct();
		
		if($this->input->get('debug') !== false) $this->output->enable_profiler(DEBUG_MODE);
	}

	
	public function count_free_rooms(){
		$this->load->model('booking_model');
		
		$this->output->cache(5); //Cache for 5 minutes
		
		$data['free_rooms'] = $this->booking_model->count_free_rooms(4); //$role_id "4" is undergrads
		$this->load->view('api/free_rooms', $data);
	}
	
	function room_data(){
		$this->load->model('room_model');
		$this->load->model('resource_model');
		
		$requested_room = $this->uri->segment(3); 
		
		if($requested_room === FALSE){
			echo '[]';
			return;
		}
		
		$data['room'] = $this->room_model->load_room_by_name($requested_room);
		
		if($data['room'] === FALSE){
			echo '[]';
			return;
		}
		
		$data['resources'] = $this->resource_model->load_resources($data['room']['room_resources']);
		
		//var_dump($data['resources']);
		
		//$this->output->cache(5); //Cache for 5 minutes
		
		//$data['free_rooms'] = $this->booking_model->count_free_rooms(4); //$role_id "4" is undergrads
		$this->load->view('api/room_data', $data);
	
	}
	
	
}