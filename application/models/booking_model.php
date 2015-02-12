<?php

class booking_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}
	
	function get_bookings($date){
		//Parse the date_time first
		if(is_numeric($date)){
		
			//Create start and end dates. Return all bookings for this day
			$date_raw = date_parse_from_format('Ymd', $date);
			$date_start = mktime(0, 0, 0, $date_raw['month'], $date_raw['day'], $date_raw['year']);
			$date_end = mktime(23, 59, 59, $date_raw['month'], $date_raw['day'], $date_raw['year']);
			
			$sql = "SELECT distinct booking_id, r.room_id, matrix_id, start, end, comment, booker_name FROM bookings b, rooms r, room_roles rr
					WHERE
					b.start BETWEEN '".date('Y-m-d H:i:s', $date_start)."' AND '".date('Y-m-d H:i:s', $date_end)."'
					AND b.room_id = r.room_id
					AND r.room_id = rr.room_id ";
			
			if($this->session->userdata('super_admin') !== true){
				$sql .= "AND rr.role_id IN ";
				
				//Gather roles from session rather then database (since students etc.. are not whitelisted)
				$roles = array();
			
				foreach($this->session->userdata('roles') as $role){
					if(is_numeric($role->role_id)) $roles[] = $role->role_id;
				}
				
				$sql .= "(".implode(",", $roles).")";
			}
			
			//Run query, but prevent caching as this data changes frequently
			$this->db->cache_off();
			$query = $this->db->query($sql);
			$this->db->cache_on();
			
			return $query;
		}
		else{
			return FALSE;
		}
	}
	
	function get_booking($booking_id){
		$this->db->cache_off();
		$this->db->where('booking_id', $booking_id);
		$result = $this->db->get('bookings');
		$this->db->cache_on();
		
		return $result;
	}
	
	function next_booking($datetime){
		$sql = "SELECT MIN(start) AS start FROM bookings WHERE start > ".$this->db->escape(date('Y-m-d h:i:s', $datetime));
		
		$this->db->cache_off();
		$result = $this->db->query($sql);
		$this->db->cache_on();
		
		return $result;
		
	}
	
	function remaining_hours($matrix, $date){
		//Pull down the hours limit (the maximum a users group of roles allows for. Eg, if user is library staff & undergrad, they can book longer then a normal undergrad for all rooms
		$sql = "SELECT MAX(r.hours_per_week) AS hours_per_week, MAX(booking_limit) as booking_limit FROM roles r WHERE r.role_id IN ";
		
		foreach($this->session->userdata('roles') as $role){
			if(is_numeric($role->role_id)) $roles[] = $role->role_id;
		}
		
		$sql .= "(".implode(",", $roles).")";
		
		$limits = $this->db->query($sql)->row();
		
		//Pull down their existing bookings for that week (don't cache this)
		$this->db->cache_off();
		$weekly_bookings_query = $this->db->query("SELECT IFNULL(sum(TIMESTAMPDIFF(minute,start,end)),0) as weekly_minutes FROM bookings where matrix_id = ". $this->db->escape($this->session->userdata('username')). " AND  weekofyear(start) = " . date('W', $date));
		$this->db->cache_on();
		$weekly_bookings = $weekly_bookings_query->row();
		
		//Pull down existing bookings for that day
		$this->db->cache_off();
		$daily_bookings_query = $this->db->query("SELECT IFNULL(sum(TIMESTAMPDIFF(minute,start,end)),0) as daily_minutes FROM bookings where matrix_id = ". $this->db->escape($this->session->userdata('username')). " AND  dayofyear(start) = " . (date('z', $date) + 1));
		$this->db->cache_on();
		$daily_bookings = $daily_bookings_query->row();
		
		$data['booking_limit'] = (int)$limits->booking_limit;
		//$data['day_limit'] = (int)$limits->hours_per_day;
		$data['day_used'] = $daily_bookings->daily_minutes / 60;
		$data['week_limit'] = (int)$limits->hours_per_week;
		$data['week_remaining'] = $limits->hours_per_week - ($weekly_bookings->weekly_minutes / 60);
		
		return $data;
	}
	
	//Return TRUE or FALSE depending whether or not a user is allowed to book the given room_id
	function is_allowed($room_id){
		if(!is_numeric($room_id)) return FALSE;
		if($this->session->userdata('super_admin') == TRUE) return TRUE;
		
		$sql = "SELECT * FROM room_roles WHERE room_id = ".$this->db->escape($room_id)." AND role_id IN ";
		
		$roles = array();
		
		foreach($this->session->userdata('roles') as $role){
			if(is_numeric($role->role_id)) $roles[] = $role->role_id;
		}
		
		$sql .= "(".implode(",", $roles).")";
		
		$result = $this->db->query($sql);
		if($result->num_rows() > 0){
			return TRUE;
		}
		else{
			
			return FALSE;
		}
	}
	
	function book_room($room_id, $start, $end, $comment){
		//Make sure the slot is not already booked!
		$this->db->cache_off();
		
		$sql = "SELECT * FROM bookings WHERE 
				start >= '". date('Y-m-d H:i:s', $start)."'
				and end <= '". date('Y-m-d H:i:s', $end)."'
				and room_id = $room_id";
		
		$existing_bookings = $this->db->query($sql);
		
		$this->db->cache_on();
		
		if($existing_bookings->num_rows() == 0){
			$data = array(
						'room_id' => $room_id,
						'start' => date('Y-m-d H:i:s', $start),
						'end' => date('Y-m-d H:i:s', $end),
						'comment' => $comment,
						'booker_name' => $this->session->userdata('name'),
						'matrix_id' => $this->session->userdata('username')
					);
			
			$this->db->insert('bookings', $data);
			
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	
	function edit_booking($room_id, $start, $end, $comment, $booking_id){
	
		$data = array(
					'room_id' => $room_id,
					'start' => date('Y-m-d H:i:s', $start),
					'end' => date('Y-m-d H:i:s', $end),
					'comment' => $comment,
					'booker_name' => $this->session->userdata('name'),
					'matrix_id' => $this->session->userdata('username')
				);
			
			$this->db->where('booking_id', $booking_id);
			$this->db->update('bookings', $data);
			
			return true;
		
		
	}
	
	function delete_booking($booking_id){
		$this->db->where('booking_id', $booking_id);
		$this->db->delete('bookings');
	}
	
	//Lists upcoming block bookings (unless optional parameter is true, where past block bookings are shown)
	function list_block_bookings($date = 0, $include_past = false){
		if($date == 0) $date = time();
		
		$sql = "SELECT bb.*, bbr.room_id, r.name FROM block_booking bb, block_booking_room bbr, rooms r WHERE bb.block_booking_id = bbr.block_booking_id AND bbr.room_id = r.room_id ";
		
		if(!$include_past){
			$sql .= " AND (start >= '".date('Y-m-d', $date)."' OR end  > '".date('Y-m-d', $date)."')";
		}
		
		$sql .= " ORDER BY start ASC";
		$result = $this->db->query($sql);
		
		
		//Yay post-processing
		$data = array();
		
		foreach($result->result() as $row){
			$data[$row->block_booking_id]['block_booking_id'] = $row->block_booking_id;
			$data[$row->block_booking_id]['start'] = $row->start;
			$data[$row->block_booking_id]['end'] = $row->end;
			$data[$row->block_booking_id]['reason'] = $row->reason;
			$data[$row->block_booking_id]['room'][$row->room_id] = 	array(
															'room_id' 	=>	$row->room_id,
															'room_name' =>	$row->name
														);
			
		}
		
		return $data;
	}
	
	function get_block_booking($id){
		if(!is_numeric($id)) return FALSE;
		
		$sql = "SELECT bb.*, bbr.room_id, r.name FROM block_booking bb, block_booking_room bbr, rooms r WHERE bb.block_booking_id = bbr.block_booking_id AND bbr.room_id = r.room_id AND bb.block_booking_id = $id";
		
		$result = $this->db->query($sql);
		
		
		//Yay post-processing
		
		foreach($result->result() as $row){
			$data['block_booking_id'] = $row->block_booking_id;
			$data['start'] = $row->start;
			$data['end'] = $row->end;
			$data['reason'] = $row->reason;
			$data['room'][$row->room_id] = 	array(
									'room_id' 	=>	$row->room_id,
									'room_name' =>	$row->name
								);
			
		}
		
		return $data;
	}
	
	function add_block_booking($reason, $start, $end, $rooms){
		$this->load->library('calendar');
		
		//CHeck for valid input formats
		if(!is_array($rooms)) return FALSE;
		if(!$this->calendar->isValidDateTimeString($start, 'Y-m-d G:i') || !$this->calendar->isValidDateTimeString($end, 'Y-m-d G:i'))return FALSE;		
		
		//Make sure the end is always after the start
		$dt_start = date_create($start);
		$dt_end = date_create($end);
		if($dt_start > $dt_end){
			return FALSE;
		}
		
		$data = array(
						'reason' => $reason,
						'start' => $start,
						'end' => $end,
					);
			
		$this->db->insert('block_booking', $data);
		$this->db->cache_delete_all();
		$id = $this->db->insert_id();
		
		$this->set_block_booking_rooms($rooms, $id);

		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function edit_block_booking($reason, $start, $end, $rooms, $id){
		$this->load->library('calendar');
		
	
		if(!is_array($rooms)) return FALSE;
		if(!$this->calendar->isValidDateTimeString($start, 'Y-m-d G:i') || !$this->calendar->isValidDateTimeString($end, 'Y-m-d G:i'))return FALSE;		
		//Make sure the end is always after the start
		$dt_start = date_create($start);
		$dt_end = date_create($end);
		if($dt_start > $dt_end){
		
			return FALSE;
		}
		
		$data = array(
						'reason' => $reason,
						'start' => $start,
						'end' => $end,
					);
		
		
		$this->db->where('block_booking_id', $id);
		$this->db->update('block_booking', $data);
		$this->db->cache_delete_all();
		
		
		$this->set_block_booking_rooms($rooms, $id);

		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function delete_block_booking($id){
		if(!is_numeric($id)) return FALSE;
		
		$this->db->where('block_booking_id', $id);
		$this->db->delete('block_booking');
		
		$this->db->cache_delete_all();
	}
	
	function set_block_booking_rooms($rooms, $bb_id){
		$this->db->where('block_booking_id',$bb_id);
		$this->db->delete('block_booking_room');

		foreach($rooms as $room){
			$data = array(
				'room_id' => $room,
				'block_booking_id' => $bb_id
			);
			
			$this->db->insert('block_booking_room', $data);
		}
		
	}
	
	function generate_ics($booking_id){
		$this->load->model('room_model');
		
		$this->db->where('booking_id', $booking_id);
		$booking_data = $this->db->get('bookings')->row();
		
		$room_result = $this->room_model->load_room($booking_data->room_id);
		$room = $room_result['room_data']->row();
		
		$data = array(
			'start' => strtotime($booking_data->start),
			'end' => strtotime($booking_data->end),
			'room' => $room->name,
			'booking_id' => $booking_id,
		);
		
		$ics_content = $this->load->view('email/booking_ics',$data, TRUE);
		file_put_contents('temp/'.$booking_id.'.ics', $ics_content);
	}
	
	function delete_ics($booking_id){
		@unlink('temp/'.$booking_id.'.ics');
		return;
	}
}