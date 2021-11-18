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
					b.needs_moderation = 0
					AND b.start BETWEEN '".date('Y-m-d H:i:s', $date_start)."' AND '".date('Y-m-d H:i:s', $date_end)."'
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
	
	function get_selected_bookings($start, $end, $rooms){
		if(!is_array($rooms)){
			return;
		}
		
		foreach($rooms as $room){
			if(!is_numeric($room)){
				return;
			}
		}
		
		$sql = "SELECT distinct booking_id, r.room_id, r.name,  matrix_id, start, end, comment, booker_name FROM bookings b, rooms r, room_roles rr
					WHERE
					b.start BETWEEN '".$start." 00:00:00' AND '".$end." 23:59:59'
					AND b.room_id = r.room_id
					AND r.room_id = rr.room_id 
					AND r.room_id IN (". implode(',', $rooms) .") ";
			
		if($this->session->userdata('super_admin') !== true){
			$sql .= "AND rr.role_id IN ";
			
			//Gather roles from session rather then database (since students etc.. are not whitelisted)
			$roles = array();
		
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$sql .= "(".implode(",", $roles).")";
		}
		
			$sql .= " ORDER BY start DESC";
		
		//Run query, but prevent caching as this data changes frequently
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function next_booking($datetime, $room_id){
		if(!is_numeric($room_id)) return;
		$sql = "SELECT MIN(start) AS start FROM bookings WHERE needs_moderation = 0 AND room_id = ".$room_id."     AND start > ".$this->db->escape(date('Y-m-d H:i:s', $datetime));
		
		$this->db->cache_off();
		$result = $this->db->query($sql);
		$this->db->cache_on();
		
		return $result;
		
	}
	
	function remaining_hours($matrix, $date){
		//Pull down the hours limit (the maximum a users group of roles allows for. Eg, if user is library staff & undergrad, they can book longer then a normal undergrad for all rooms
		$sql = "SELECT MAX(r.hours_per_week) AS hours_per_week FROM roles r WHERE r.role_id IN ";
		
		$roles = array();
		
		foreach($this->session->userdata('roles') as $role){
			if(is_numeric($role->role_id)) $roles[] = $role->role_id;
		}
		
		$sql .= "(".implode(",", $roles).")";
		
		$limits = $this->db->query($sql)->row();
		
		
		//Pull down their existing bookings for that week (don't cache this)
		$this->db->cache_off();
		$weekly_bookings_query = $this->db->query("SELECT IFNULL(sum(TIMESTAMPDIFF(minute,start,end)),0) as weekly_minutes FROM bookings where needs_moderation = 0 AND matrix_id = ". $this->db->escape($this->session->userdata('username')). " AND  yearweek(start,6) = yearweek('" . date('Y-m-d',$date) ."',6)");
		$this->db->cache_on();
		$weekly_bookings = $weekly_bookings_query->row();
		
		//Pull down existing bookings for that day
		$this->db->cache_off();
		$daily_bookings_query = $this->db->query("SELECT IFNULL(sum(TIMESTAMPDIFF(minute,start,end)),0) as daily_minutes FROM bookings where needs_moderation = 0 AND matrix_id = ". $this->db->escape($this->session->userdata('username')). " AND  year(start) = '".date('Y',$date)."' AND dayofyear(start) = " . (date('z', $date) + 1));
		$this->db->cache_on();
		$daily_bookings = $daily_bookings_query->row();
		
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
	
	function book_room($room_id, $start, $end, $custom_fields, $booker_name = '', $matrix_id = ''){
		if($booker_name === '') $booker_name = $this->session->userdata('name');
		if($matrix_id === '') $matrix_id = $this->session->userdata('username');
		
		//Make sure the slot is not already booked!
		$this->db->cache_off();
		
		$sql = "SELECT * FROM bookings WHERE 
				((start <= '". date('Y-m-d H:i:s', $start)."'
				and end > '". date('Y-m-d H:i:s', $start)."')
				
				OR
				
				(start >= '". date('Y-m-d H:i:s', $start)."'  
				and start < '". date('Y-m-d H:i:s', $end)."'))
				
				
				and needs_moderation = 0 
				and room_id = $room_id";
		
		$existing_bookings = $this->db->query($sql);
		
		$this->db->cache_on();
		
		if($this->is_block_booked($start, $end, $room_id)) return FALSE;
		
		if($existing_bookings->num_rows() == 0){
			$data = array(
						'room_id' => $room_id,
						'start' => date('Y-m-d H:i:s', $start),
						'end' => date('Y-m-d H:i:s', $end),
						'booker_name' => $booker_name,
						'matrix_id' => $matrix_id,
						'needs_moderation' => FALSE
					);
			
			$this->db->insert('bookings', $data);
			
			$booking_id = $this->db->insert_id();
			
			if(is_array($custom_fields)){
				foreach($custom_fields as $field_data){
					$data = array(
						'booking_id'	=> $booking_id,
						'fc_id'			=> $field_data[0],
						'data'			=> $field_data[1]
					);
					
					$this->db->insert('form_customization_data', $data);
				}
			}
			
			return $booking_id;
		}
		else{
			return FALSE;
		}
	}
	
	//Turn off caching for this in case of edits
	function get_custom_fields_data($booking_id){
		$this->db->where('booking_id', $booking_id);
		
		$this->db->cache_off();
		$query = $this->db->get('form_customization_data');
		$this->db->cache_on();
		
		return $query;

	}
	
	function edit_booking($room_id, $start, $end, $user_data, $booking_id, $matrix_id, $booker_name){
		if(!is_numeric($booking_id)) return FALSE;
		
		if($this->is_block_booked($start, $end, $room_id)) return FALSE;
		
		//Make sure the slot is not already booked by someone else (prevent changing start time)!
		$this->db->cache_off();
		
		$sql = "SELECT * FROM bookings WHERE 
				((start <= '". date('Y-m-d H:i:s', $start)."'
				and end > '". date('Y-m-d H:i:s', $start)."')
				OR
				(start >= '". date('Y-m-d H:i:s', $start)."'
				and start < '". date('Y-m-d H:i:s', $end)."'))
				
				and room_id = $room_id
				and booking_id <> " . $booking_id;
		
		$existing_bookings = $this->db->query($sql);
		
		$this->db->cache_on();
	
		if($existing_bookings->num_rows() > 0) return FALSE;
	
	
		$data = array(
					'room_id' => $room_id,
					'start' => date('Y-m-d H:i:s', $start),
					'end' => date('Y-m-d H:i:s', $end),
					'booker_name' => $this->session->userdata('name'),
					'matrix_id' => $matrix_id,
					'booker_name' => $booker_name
				);
			
		$this->db->where('booking_id', $booking_id);
		$this->db->update('bookings', $data);
		
		if(is_array($user_data)){
			$this->db->where('booking_id', $booking_id);
			$this->db->delete('form_customization_data');
			
			foreach($user_data as $field_data){
				$data = array(
					'fc_id'			=> $field_data[0],
					'data'			=> $field_data[1],
					'booking_id'	=> $booking_id
				);
				
				$this->db->insert('form_customization_data', $data);
			}
		}
		
		return true;
		
		
	}
	
	function delete_booking($booking_id){
		$this->db->where('booking_id', $booking_id);
		$this->db->delete('bookings');
	}
	
	function checkout($booking_id){
		if(date('i') >= 30){
			$minute = 0;
			$hour = date('H') + 1;
		}
		else{
			$minute = 30;
			$hour = date('H');
		}
		
		$data = array(
				'end' => date('Y-m-d H:i:s', mktime($hour, $minute,0)),
		);
		
		
		$this->db->where('booking_id', $booking_id);
		$this->db->update('bookings', $data);
	}
	
	function is_checked_out($booking_id){
		$this->db->cache_off();
		$this->db->where('booking_id', $booking_id);
		$this->db->where('action', 'Checkout');
		$result = $this->db->get('log');
		$this->db->cache_on();
		
		if($result->num_rows() > 0){
			return TRUE;
		}
		else{
			return FALSE;
		}		
	}
	
	function get_bookings_by_name($fullname, $limit = 30){
			if(!is_numeric($limit)) return FALSE;
			
			$sql = "SELECT b.booking_id, b.room_id, r.name, b.matrix_id, b.booker_name, b.start, b.end, r.seats from bookings b, rooms r, room_roles rr
					WHERE 
					b.needs_moderation = 0 AND
					b.room_id = r.room_id
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
					
			$sql .= " AND booker_name like '%".$this->db->escape_like_str($fullname)."%'
					ORDER BY start DESC
					LIMIT 0, $limit";
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function get_upcoming_bookings($matrix_id){
		$sql = "SELECT distinct b.booking_id, b.room_id, r.name, b.matrix_id, b.booker_name, b.start, b.end, r.seats from bookings b, rooms r, room_roles rr
				WHERE 
				b.needs_moderation = 0 AND
				b.room_id = r.room_id
				
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
					
		$sql .= " AND start > ". $this->db->escape(date('Y-m-d H:i:s'))."
				AND matrix_id = '".$matrix_id."'";
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function get_current_bookings($matrix_id){
		$sql = "SELECT distinct b.booking_id, b.room_id, r.name, b.matrix_id, b.booker_name, b.start, b.end, r.seats from bookings b, rooms r, room_roles rr
				WHERE 
				b.needs_moderation = 0 AND
				b.room_id = r.room_id
				
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
					
			$sql .= " AND start <= ". $this->db->escape(date('Y-m-d H:i:s'))."
				AND end > ". $this->db->escape(date('Y-m-d H:i:s'))."
				AND matrix_id = '".$matrix_id."'";
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function get_previous_bookings($matrix_id, $limit = 5){
		if(!is_numeric($limit)) return false;
		
		$sql = "select distinct b.booking_id, b.room_id, r.name, b.matrix_id, b.booker_name, b.start, b.end, r.seats from bookings b, rooms r, room_roles rr
				where 
				b.needs_moderation = 0 AND
				b.room_id = r.room_id
				
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
					
		$sql .= " and end < ". $this->db->escape(date('Y-m-d H:i:s'))."
				AND matrix_id = '".$matrix_id."'
				ORDER BY end DESC
				LIMIT 0,".$limit;
				
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	//Return a random-generated list of free rooms for a given time
	function get_random_free_bookings($datetime, $min_seats, $max_seats, $limit, $data){
		$hours = $data['hours'];
		$roles = $data['roles'];
		$limits = $data['limits'];
		$block_bookings = $data['block_bookings'];
		$recurring_bookings = $data['recurring_bookings'];
		$bookings = $data['bookings'];
		$rooms = $data['r'];
		
			
		$good_rooms = array();
		
		foreach($roles->result() as $role){
			if(isset($rooms[$role->role_id])){
				foreach($rooms[$role->role_id] as $room){
					$skip = false;
					
					if($room->seats > $max_seats || $room->seats < $min_seats){
						$skip = true;
					
					}
					
					//Do you have any hours remaining to book
					if($room->max_daily_hours <= $limits['day_used'] || $limits['week_remaining'] <= 0){
						$skip = true;
					}
					
					//Is this time during the building hours?
					$current_time = round(date('G', $datetime) + (date('i', $datetime)/60),1);
					if($current_time < round(($hours[$room->building_id]->STARTTIME) * 24,1) || $current_time > round(($hours[$room->building_id]->ENDTIME) * 24,1)){
						$skip = true;
					}
					
					//Does an earlier booking overlap this time?
					if(!$skip){
						if(isset($bookings[$room->room_id])){
							foreach($bookings[$room->room_id] as $booking){
							
								if((strtotime($booking->start) <= $datetime) && (strtotime($booking->end) > $datetime)){
									$skip = true;
									break;
								}
							}
						}
					}
					
					//No need to check if room is already skipped
					if(!$skip){
						//Is this time slot "block booked"?
						foreach($block_bookings as $block_booking){
							if(array_key_exists($room->room_id, $block_booking['room']) && strtotime($block_booking['start']) <= $datetime && strtotime($block_booking['end']) > $datetime){
								$skip = true;
								break;
							}
						}
						
						//Is there a recurring booking at this time?
						foreach($recurring_bookings as $recurring_booking){
							//Does this booking apply to todays date? If not, skip it
							//If Days since reccuring booking start MOD interval == 0
							if(!(round(($datetime - strtotime($recurring_booking['start']))/(60*60*24)) % $recurring_booking['repeat_interval'] === 0)){
								continue;
							}
							//The recurruing booking applies to todays date. Change the start/end dates to "today"
							else{
								//Make sure the recurring booking has started (and isn't just upcoming)
								if($recurring_booking['start'] > date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, date('n',$datetime), date('j',$datetime), date('Y',$datetime)))){
									continue;
								}
									
								$recurring_booking['start'] = date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, date('n',$datetime), date('j',$datetime), date('Y',$datetime)));
								$recurring_booking['end'] =  date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['end'])),date("i", strtotime($recurring_booking['end'])),0, date('n',$datetime), date('j',$datetime), date('Y',$datetime)));
							}
							
							if($datetime >= strtotime($recurring_booking['start']) && $datetime < strtotime($recurring_booking['end'])){
								
								if(array_key_exists($room->room_id, $recurring_booking['room'])){
									
									$skip = true;
									break;
								}
							}
							
						}
					}
					
					if(!$skip){
						$good_rooms[] = $room;
						
					}	
				}
			}
		}
		
		//Now that we have a list of rooms, pick $limit amount randomly
		$results = array();
		
		$room_pool = count($good_rooms);
		if($limit > $room_pool) $limit = $room_pool;
				
		for($i=0; $i < $limit; $i++){
			
			if(count($good_rooms) == 0) break; //If pool of good rooms is used up
			
			$random_number = rand(0,count($good_rooms)-1);
			
			$next_booking = $this->next_booking($datetime, $good_rooms[$random_number]->room_id);
			$next_booking = $next_booking->row();
			
			$good_rooms[$random_number]->next_booking = strtotime($next_booking->start); //Kick in the next booking
			
			$results[] = $good_rooms[$random_number];
			
			//Remove the 'good room' from the pool
			unset($good_rooms[$random_number]);
			$good_rooms = array_values($good_rooms);
			
		}
		
		return $results;
	}
	
	//Lists upcoming block bookings (unless optional parameter is true, where past block bookings are shown)
	function list_block_bookings($date = 0, $include_past = false, $skip_permissions = false, $recurring = false){
		if($date == 0) $date = time();
		if($recurring === false) $use_recurring = 0;
		else $use_recurring = 1;
		
		$sql = "SELECT DISTINCT bb.*, bbr.room_id, r.name FROM block_booking bb, block_booking_room bbr, rooms r ";
		
		if($this->session->userdata('super_admin') !== true && !$skip_permissions){
			$sql.= ", block_booking_permissions bbp WHERE
					(bb.matrix_id=".$this->db->escape($this->session->userdata('username')) ." OR (bbp.block_booking_id = bb.block_booking_id "; //Add permissions table to query if not a super admin
			
			$sql .= "AND bbp.role_id IN ";
				
			//Gather roles from session rather then database (since students etc.. are not whitelisted)
			$roles = array();
		
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$sql .= "(".implode(",", $roles)."))) ";
		}
		else{
			$sql.= " WHERE 1=1 "; //Added the dreaded 1=1 to deal with avoid extra code related to placing the "AND" in the query
		}
		
		$sql.= "AND bb.block_booking_id = bbr.block_booking_id AND bbr.room_id = r.room_id ";
		
		if(!$include_past){
			$sql .= " AND (start >= '".date('Y-m-d', $date)."' OR end  > '".date('Y-m-d', $date)."')";
		}
		
		$sql .= "AND bb.is_recurring = ". $use_recurring ." ORDER BY start ASC, r.name asc";
		
		$result = $this->db->query($sql);
		
		
		//Yay post-processing
		$data = array();
		
		foreach($result->result() as $row){
			$data[$row->block_booking_id]['block_booking_id'] = $row->block_booking_id;
			$data[$row->block_booking_id]['booked_by'] = $row->booked_by;
			$data[$row->block_booking_id]['start'] = $row->start;
			$data[$row->block_booking_id]['end'] = $row->end;
			$data[$row->block_booking_id]['reason'] = $row->reason;
			$data[$row->block_booking_id]['repeat_interval'] = $row->repeat_interval;
			$data[$row->block_booking_id]['room'][$row->room_id] = 	array('room_id' => $row->room_id, 'room_name' => $row->name);
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
			$data['room'][$row->room_id] = 	array('room_id' => $row->room_id, 'room_name' => $row->name);
			$data['repeat_interval'] = $row->repeat_interval;
		}
		
		return $data;
	}
	
	function add_block_booking($reason, $start, $end, $rooms, $permissions){
		$this->load->library('bookingcalendar');
		
		//Check for valid input formats
		if(!is_array($rooms)) return FALSE;
		if(!$this->bookingcalendar->isValidDateTimeString($start, 'Y-m-d G:i') || !$this->bookingcalendar->isValidDateTimeString($end, 'Y-m-d G:i'))return FALSE;		
		
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
						'booked_by' => $this->session->userdata('name'),
						'matrix_id' => $this->session->userdata('username'),
						'is_recurring' => 0,
					);
			
		$this->db->insert('block_booking', $data);
		$this->db->cache_delete_all();
		$id = $this->db->insert_id();
		
		$this->set_block_booking_rooms($rooms, $id);
		$this->set_block_booking_permissions($permissions, $id);

		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function add_recurring_booking($reason, $start, $end, $start_time, $end_time, $rooms, $permissions, $repeat_interval){
		$this->load->library('bookingcalendar');
		
		//Check for valid input formats
		if(!is_array($rooms)) return FALSE;
		if(!is_numeric($repeat_interval)) return FALSE;
		if(!$this->bookingcalendar->isValidDateTimeString($start, 'Y-m-d') || !$this->bookingcalendar->isValidDateTimeString($end, 'Y-m-d')) return FALSE;		
		
		//Make sure the end is always after the start
		$dt_start = date_create($start);
		$dt_end = date_create($end);
		
		if($dt_start > $dt_end){
			return FALSE;
		}
		
		$start_datetime = date('Y-m-d G:i',strtotime($start_time, strtotime($start)));
		$end_datetime = date('Y-m-d G:i',strtotime($end_time, strtotime($end)));
		
		if(strtotime($start_datetime) > strtotime($end_datetime)){
			return FALSE;
		}
		
		$data = array(
						'reason' => $reason,
						'start' => $start_datetime,
						'end' => $end_datetime,
						'booked_by' => $this->session->userdata('name'),
						'matrix_id' => $this->session->userdata('username'),
						'is_recurring' => 1,
						'repeat_interval' => $repeat_interval,
					);
			
		$this->db->insert('block_booking', $data);
		$this->db->cache_delete_all();
		$id = $this->db->insert_id();
		
		$this->set_block_booking_rooms($rooms, $id);
		$this->set_block_booking_permissions($permissions, $id);

		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function edit_block_booking($reason, $start, $end, $rooms, $permissions, $id){
		$this->load->library('bookingcalendar');
		
	
		if(!is_array($rooms)) return FALSE;
		if(!$this->bookingcalendar->isValidDateTimeString($start, 'Y-m-d G:i') || !$this->bookingcalendar->isValidDateTimeString($end, 'Y-m-d G:i'))return FALSE;		
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
						'booked_by' => $this->session->userdata('name'),
						'matrix_id' => $this->session->userdata('username'),
						'is_recurring' => 0,
					);
		
		
		$this->db->where('block_booking_id', $id);
		$this->db->update('block_booking', $data);
		
		$this->set_block_booking_permissions($permissions, $id);
		
		$this->db->cache_delete_all();
		
		
		$this->set_block_booking_rooms($rooms, $id);

		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function edit_recurring_booking($reason, $start, $end, $start_time, $end_time, $rooms, $permissions, $repeat_interval, $id){
	
		
		$this->load->library('bookingcalendar');
		
	
		if(!is_array($rooms)) return FALSE;
		if(!$this->bookingcalendar->isValidDateTimeString($start, 'Y-m-d') || !$this->bookingcalendar->isValidDateTimeString($end, 'Y-m-d'))return FALSE;		
		//Make sure the end is always after the start
		$dt_start = date_create($start);
		$dt_end = date_create($end);
		if($dt_start > $dt_end){
			
			return FALSE;
		}
		
		$start_datetime = date('Y-m-d G:i',strtotime($start_time, strtotime($start)));
		$end_datetime = date('Y-m-d G:i',strtotime($end_time, strtotime($end)));
		
		if(strtotime($start_datetime) > strtotime($end_datetime)){
			return FALSE;
		}
		
		$data = array(
						'reason' => $reason,
						'start' => $start_datetime,
						'end' => $end_datetime,
						'booked_by' => $this->session->userdata('name'),
						'matrix_id' => $this->session->userdata('username'),
						'is_recurring' => 1,
						'repeat_interval' => $repeat_interval,
					);
		
		
		
		
		$this->db->where('block_booking_id', $id);
		$this->db->update('block_booking', $data);
		
		$this->set_block_booking_permissions($permissions, $id);
		
		$this->db->cache_delete_all();
		
		
		$this->set_block_booking_rooms($rooms, $id);

		$this->db->cache_delete_all();
		return TRUE;
	}
	
	function get_block_booking_permissions($id){
		$this->db->select('role_id');
		$this->db->where('block_booking_id', $id);
		$result = $this->db->get('block_booking_permissions');
		
		$ret_val = array();
		
		foreach($result->result() as $row){
			$ret_val[] = $row->role_id;
		}
		
		return $ret_val;
	}
	
	function set_block_booking_permissions($permissions, $id){
		if(!is_array($permissions)) return false;
		
		$this->db->where('block_booking_id', $id);
		$this->db->delete('block_booking_permissions');
		
		foreach($permissions as $perm){
			$data = array(
						'block_booking_id' => $id,
						'role_id' => $perm
					);
			
			$this->db->insert('block_booking_permissions', $data);
		}
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
	
	function count_free_rooms($role_id, $time = 0){
		if(!is_numeric($role_id)) return;
		
		if($time == 0){
			//If time not set, assume the next half hour slot
			
			$hour = date('H');
			
			if(date('i') < 30){
				$minute = 30;
				
			}
			else{
				$minute = 0;
				$hour += 1;
			}
			
			$time = mktime($hour, $minute,0);
		}
		
		$sql = "SELECT 
					(COUNT(*) - (SELECT 
							COUNT(*)
						FROM
							bookings b,
							room_roles rr
						WHERE
							b.needs_moderation = 0 AND
							b.room_id = rr.room_id
								AND rr.role_id = ".$role_id." 
								AND (start = '".date('Y-m-d H:i:s', $time)."'
								OR (start < '".date('Y-m-d H:i:s', $time)."'
								AND end > '".date('Y-m-d H:i:s', $time)."'))) - (SELECT 
							COUNT(*)
						FROM
							block_booking_room bbr,
							block_booking bb,
							rooms r,
							room_roles rr
						WHERE
							bbr.room_id = r.room_id
								AND r.room_id = rr.room_id
								AND rr.role_id = ".$role_id." 
								AND bb.block_booking_id = bbr.block_booking_id
								AND (bb.start = '".date('Y-m-d H:i:s', $time)."'
								OR (bb.start < '".date('Y-m-d H:i:s', $time)."'
								AND bb.end > '".date('Y-m-d H:i:s', $time)."'))
								AND r.is_active = 1)) as free_rooms
				FROM
					rooms r,
					room_roles rr
				WHERE
					r.room_id = rr.room_id
						AND rr.role_id = ".$role_id." 
						AND r.is_active = 1";
		
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
		
	}
	
	function get_moderation_queue(){
		
		$sql = "SELECT b.*, r.name FROM bookings b, rooms r
				WHERE b.room_id = r.room_id
				AND needs_moderation = 1 ";
				
		if($this->session->userdata('super_admin') !== true){
			$sql.= " AND r.room_id IN (SELECT room_id FROM room_roles rr WHERE rr.role_id IN ";
			
			//Gather roles from session rather then database 
			$roles = array();
		
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$sql .= "(".implode(",", $roles)."))";
		}
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function add_to_moderation_queue($room_id, $start, $end, $user_data){
		
		if($this->is_block_booked($start, $end, $room_id)) return FALSE;
		
		$data = array(
			'room_id' => $room_id,
			'start' => date('Y-m-d H:i:s', $start),
			'end' => date('Y-m-d H:i:s', $end),
			'booker_name' => $this->session->userdata('name'),
			'matrix_id' => $this->session->userdata('username'),
			'needs_moderation' => TRUE
		);

		$this->db->insert('bookings', $data);
		$insert_id =  $this->db->insert_id();
		
		if(is_array($user_data)){
			foreach($user_data as $field_data){
				$data = array(
					'fc_id'			=> $field_data[0],
					'data'			=> $field_data[1],
					'booking_id'	=> $insert_id
				);
				
				$this->db->insert('form_customization_data', $data);
			}
		}
		
		
		return $insert_id;
		
	}
	
	function moderator_approve($booking_id){
		//see if the slot is free, then "book"
		
		$this->db->where('booking_id', $booking_id);
		$data = $this->db->get('bookings')->row();
		
		//Make sure the admin is allowed to moderate this entry!
		if($this->session->userdata('super_admin') !== true){
			$sql = "SELECT room_id FROM room_roles WHERE role_id IN ";
			
			//Gather roles from session rather then database 
			$roles = array();
		
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$sql .= "(".implode(",", $roles).")";
			
			$this->db->cache_off();
			$query = $this->db->query($sql);
			$this->db->cache_on();
			
			$is_allowed = false;
			
			foreach($query->result() as $row){
				if($data->room_id == $row->room_id){
					$is_allowed = true;
					break;
				}
				
			}
			
			//User was not allowed to moderate this room. Return false without taking action
			if(!$is_allowed){
				return FALSE;
			}
		}
		
		//Get the custom fields
		$customization_data = $this->get_custom_fields_data($booking_id);
		
		$user_data = array();
		foreach($customization_data->result() as $field){
			$user_data[] = array($field->fc_id, $field->data);
		}
		
		
		//Remove it from the moderator queue
		$this->db->where('booking_id', $booking_id);
		$this->db->delete('bookings');
		
		//Book the room, making it appear as booked to all users
		$ret_val = $this->book_room($data->room_id, strtotime($data->start), strtotime($data->end), $user_data, $data->booker_name, $data->matrix_id);
		
		
		if($ret_val !== FALSE){
			$log_data = json_encode(array(
				"room_id" => $data->room_id,
				"matrix_id" => $data->matrix_id,
				"booker_name" => $data->booker_name,
				"start" => $data->start,
				"end" => $data->end
			));
		
			$this->load->model('log_model');
			$this->log_model->log_event('desktop', $this->session->userdata('username'), "Moderator Approve", null, $log_data);
				
			return $ret_val;
		}
		//Approval failed, likely because of an overlapping booking. Re-add to the moderation queue
		else{
			$mod_data = array(
				'room_id'			=> $data->room_id, 
				'start'				=> $data->start, 
				'end'				=> $data->end, 
				'comment'			=> $data->comment,
				'booker_name' 		=> $data->booker_name, 
				'matrix_id'			=> $data->matrix_id,
				'needs_moderation'	=> TRUE
			);
			
			$this->db->insert('bookings', $data);
			return FALSE;
		}
		
		
	}
	
	function moderator_deny($booking_id){
		$this->load->model('log_model');
		//delete from moderation table
		
		$this->db->where('booking_id', $booking_id);
		$data = $this->db->get('bookings')->row();
		
		//Make sure the admin is allowed to moderate this entry!
		if($this->session->userdata('super_admin') !== true){
			$sql = "SELECT room_id FROM room_roles WHERE role_id IN ";
			
			//Gather roles from session rather then database 
			$roles = array();
		
			foreach($this->session->userdata('roles') as $role){
				if(is_numeric($role->role_id)) $roles[] = $role->role_id;
			}
			
			$sql .= "(".implode(",", $roles).")";
			
			$this->db->cache_off();
			$query = $this->db->query($sql);
			$this->db->cache_on();
			
			$is_allowed = false;
			
			foreach($query->result() as $row){
				if($data->room_id == $row->room_id){
					$is_allowed = true;
					break;
				}
				
			}
			
			//User was not allowed to moderate this room. Return false without taking action
			if(!$is_allowed){
				return FALSE;
			}

		}
		
		$log_data = json_encode(array(
			"room_id" => $data->room_id,
			"matrix_id" => $data->matrix_id,
			"booker_name" => $data->booker_name,
			"start" => $data->start,
			"end" => $data->end
		));
		
		
		$this->load->model('log_model');
		$this->log_model->log_event('desktop', $this->session->userdata('username'), "Moderator Deny", null, $log_data);
		
		$this->db->where('booking_id', $booking_id);
		$this->db->delete('bookings');
		
		
		
	}
	
	function load_moderation_entry($booking_id){
		$this->db->where('booking_id', $booking_id);
		return $this->db->get('bookings');
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
	
	function is_block_booked($start, $end, $room_id){
		//Check for block/recurring bookings
		$block_bookings = $this->list_block_bookings(strtotime(date("Y-m-d",$start)), false, false);
		$recurring_bookings = $this->list_block_bookings(strtotime(date("Y-m-d",$start)), false, false, true);
		
		foreach($block_bookings as $block_booking){
			if(array_key_exists($room_id, $block_booking['room'])){
				if(strtotime($block_booking['start']) <= $start && strtotime($block_booking['end']) > $start){
					return TRUE;
				}
				if($start < strtotime($block_booking['start']) && $end > strtotime($block_booking['start'])){
					return TRUE;
				}
			}
		}
		
		
		foreach($recurring_bookings as $recurring_booking){
			//Does this booking apply to todays date? If not, skip it
			//If Days since reccuring booking start MOD interval == 0
			if(!(round(($start - strtotime($recurring_booking['start']))/(60*60*24)) % $recurring_booking['repeat_interval'] === 0)){
				continue;
			}
			//The recurruing booking applies to todays date. Change the start/end dates to "today"
			else{
				//Make sure the recurring booking has started (and isn't just upcoming)
				if($recurring_booking['start'] > date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, date('n',$start), date('j',$start), date('Y',$start)))){
					continue;
				}
				
				$recurring_booking['start'] = date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, date('n',$start), date('j',$start), date('Y',$start)));
				$recurring_booking['end'] =  date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['end'])),date("i", strtotime($recurring_booking['end'])),0, date('n',$start), date('j',$start), date('Y',$start)));
			}
			
			if(array_key_exists($room_id, $recurring_booking['room'])){
				if($start >= strtotime($recurring_booking['start']) && $start < strtotime($recurring_booking['end'])){
					return TRUE;
				}
				if($start < strtotime($recurring_booking['start']) && $end > strtotime($recurring_booking['start'])){
					return TRUE;
				}
			}
			
		}
		
		return FALSE;
	}
	
	function has_overlap($matrix_id, $new_booking_start, $new_booking_end){
		//Get all of the users bookings from today
		$sql = "SELECT * from bookings where matrix_id = ". $this->db->escape($matrix_id) ." and start > ".$this->db->escape(date('Y-m-d'));
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		
		foreach($query->result() as $booking){
			//started before this, ends after this start
			if((strtotime($booking->start) < $new_booking_start) && (strtotime($booking->end) > $new_booking_start)) return true;
			
			//started same as this
			if(strtotime($booking->start) == $new_booking_start) return true;
						
			//started after this, but before this ends
			if((strtotime($booking->start) > $new_booking_start) && ($new_booking_end >= strtotime($booking->end))) return true;
		}
		
		return false;
		
		
		
	}
}