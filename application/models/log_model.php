<?php

class log_Model  extends CI_Model  {

	
	function __construct() {
		parent::__construct();
	}

	function log_event($interface, $username, $action, $booking_id = null){
		$data = array(
			'date' => date('Y-m-d H:i:s'),
			'username' => $username,
			'interface' => $interface,
			'action' => $action	,
			'booking_id' => $booking_id
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
	
	function usage_by_hour($start, $end, $building_id = null, $room_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
		
		$sql = "select hour(b.start) as hour_slot, count(*) as num_bookings from bookings b, (SELECT building_id FROM buildings"; 
		
				if($building_id !== null && is_numeric($building_id)) $sql .= " WHERE building_id = ".$building_id;
				
				$sql .= ") bldg, rooms r WHERE r.building_id = bldg.building_id AND b.room_id = r.room_id 
				AND b.start > '".date('Y-m-d H:i:s', $start)."'
				and b.start < '".date('Y-m-d H:i:s', $end)."' ";
				
				if($room_id !== null && is_numeric($room_id)){
					$sql .= "and b.room_id = ". $room_id;
				}
				
				$sql .= " group by hour(b.start)";
				
		
		
		$this->db->cache_off();
		$query = $this->db->query($sql);
		$this->db->cache_on();
		
		return $query;
	}
	
	function checkouts_by_day($start, $end, $building_id = null, $room_id = null){
		if(!is_numeric($start) || !is_numeric($end)) return false;
	}

}
