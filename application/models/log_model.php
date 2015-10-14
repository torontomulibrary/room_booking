<?php

class log_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

	function log_event($interface, $username, $action, $booking_id = null, $data = null){
		$data = array(
			'date' => date('Y-m-d H:i:s'),
			'username' => $username,
			'interface' => $interface,
			'action' => $action	,
			'booking_id' => $booking_id,
			'data' => $data,
		);
		
		$this->db->insert('log', $data);
	}
	
	function report_by_device($start, $end){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "select 
					a.mobile, b.desktop
				from
					(select 
						count(*) as mobile
					from
						log
					where
						action = 'Login'
						and interface = 'mobile'
						and date > '".date('Y-m-d H:i:s', $start)."'
						and date < '".date('Y-m-d H:i:s', $end)."') a,
					(select 
						count(*) as desktop
					from
						log
					where
						action = 'Login'
						and interface = 'desktop'
						and date >= '".date('Y-m-d H:i:s', $start)."'
						and date <= '".date('Y-m-d H:i:s', $end)."') b
				";
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function usage_by_hour($start, $end, $building_id = null, $room_id = null, $role_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "select hour(b.start) as hour_slot, count(*) as num_bookings from bookings b, (SELECT building_id FROM buildings"; 
		
				if($building_id !== null && is_numeric($building_id)) $sql .= " WHERE building_id = ".$building_id;
				
				$sql .= ") bldg, rooms r "; if($role_id !== null) $sql.= ", room_roles rr ";
				$sql .= "WHERE "; if($role_id !== null) $sql .= "r.room_id = rr.room_id AND"; $sql .= " r.building_id = bldg.building_id AND b.room_id = r.room_id 
				AND b.start > '".date('Y-m-d H:i:s', $start)."'
				and b.start < '".date('Y-m-d H:i:s', $end)."' ";
				
				if($role_id !== null && is_numeric($role_id)) $sql .= " AND rr.role_id = ".$role_id;
				
				if($room_id !== null && is_numeric($room_id)){
					$sql .= " and b.room_id = ". $room_id;
				}
				
				$sql .= " group by hour(b.start)";
				
		
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function days_booked_ahead($start, $end, $building_id = null, $room_id = null, $role_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "
			SELECT count(*) as bookings, TIMESTAMPDIFF(DAY, l.date,b.start) as days_ahead from log l, bookings b, rooms r, buildings bu "; if($role_id !== null) $sql.= ", room_roles rr ";
			$sql .= "WHERE b.booking_id = l.booking_id 
			AND b.room_id = r.room_id
			AND r.building_id = bu.building_id ";
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND r.room_id = rr.room_id ";
	
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND rr.role_id = ".$role_id;
	
			if($building_id !== null && is_numeric($building_id)) $sql .= " AND bu.building_id = ".$building_id;
			
			$sql .= " AND l.booking_id is not null AND action='Create Booking'
			
			AND b.start > '".date('Y-m-d H:i:s', $start)."'
			and b.start < '".date('Y-m-d H:i:s', $end)."' ";
			
			if($room_id !== null && is_numeric($room_id)){
				$sql .= "and b.room_id = ". $room_id;
			}
			
			$sql .= " group by days_ahead
					 order by days_ahead asc";
			
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	
	function usage_by_seats ($start, $end, $building_id = null, $room_id = null, $role_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "
			SELECT count(*) as total, r.seats from bookings b, rooms r"; if($role_id !== null) $sql.= ", room_roles rr ";
			$sql .= " WHERE r.room_id = b.room_id ";
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND r.room_id = rr.room_id ";
			
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND rr.role_id = ".$role_id;
			
			$sql .= "
			
			AND b.start > '".date('Y-m-d H:i:s', $start)."'
			and b.start < '".date('Y-m-d H:i:s', $end)."' ";
			
			if($building_id !== null && is_numeric($building_id)) $sql .= "AND r.building_id = ".$building_id;
			
			if($room_id !== null && is_numeric($room_id)){
					$sql .= " and b.room_id = ". $room_id;
			}
				
			$sql.="
			GROUP BY r.seats
			ORDER BY seats asc
			";
			
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function ratio_by_seats ($start, $end, $building_id = null, $room_id = null, $role_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "
			SELECT count(*)/rc.num_rooms as total, r.seats from bookings b, rooms r "; if($role_id !== null) $sql.= ", room_roles rr "; $sql .= ", (SELECT count(*) as num_rooms, seats from rooms group by seats) rc
			WHERE 
            rc.seats = r.seats
            AND r.room_id = b.room_id ";
            
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND r.room_id = rr.room_id ";
			
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND rr.role_id = ".$role_id;
			
			$sql .= "
			
			AND b.start > '".date('Y-m-d H:i:s', $start)."'
			and b.start < '".date('Y-m-d H:i:s', $end)."' ";
			
			if($building_id !== null && is_numeric($building_id)) $sql .= "AND r.building_id = ".$building_id;
			
			if($room_id !== null && is_numeric($room_id)){
					$sql .= " and b.room_id = ". $room_id;
			}
				
			$sql.="
			GROUP BY r.seats
			ORDER BY seats asc
			";
			
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function total_bookings($start, $end, $building_id = null, $room_id = null, $role_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "
			select count(*) as total from (
				select distinct booking_id from bookings b, rooms r, room_roles rr
				where b.room_id = r.room_id
				and r.room_id = rr.room_id
				
				AND b.start > '".date('Y-m-d H:i:s', $start)."'
				and b.start < '".date('Y-m-d H:i:s', $end)."' ";
				
				
				if($role_id !== null && is_numeric($role_id)) $sql .= " AND rr.role_id = ".$role_id;
				
				if($room_id !== null && is_numeric($room_id)){
					$sql .= " and b.room_id = ". $room_id;
				}
			
			
			
				if($building_id !== null && is_numeric($building_id)) $sql .= " AND r.building_id = ".$building_id;
			
			
			$sql .= ") summary ";
			
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function total_checkouts($start, $end, $building_id = null, $room_id = null, $role_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "
			select count(*) as total from log l, bookings b, rooms r, room_roles rr where l.action='Checkout'
			and l.booking_id = b.booking_id
			and b.room_id = r.room_id
			and r.room_id = rr.room_id
			
			AND b.start > '".date('Y-m-d H:i:s', $start)."'
			and b.start < '".date('Y-m-d H:i:s', $end)."' ";
			
			
			if($role_id !== null && is_numeric($role_id)) $sql .= " AND rr.role_id = ".$role_id;
			
			if($room_id !== null && is_numeric($room_id)){
				$sql .= " and b.room_id = ". $room_id;
			}
			
			if($building_id !== null && is_numeric($building_id)) $sql .= " AND r.building_id = ".$building_id;
			
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function login_denied_events($start = 0, $end = 50){
		if (!is_numeric($end)) return FALSE;
		
		$sql = "
			SELECT * FROM log
			WHERE action = 'Login Denied'
			ORDER BY date DESC
			LIMIT " . $start . ", ". $end;
			
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}

}
