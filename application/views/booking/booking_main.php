
<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/template/<?php echo $theme; ?>/css/booking_main.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.jscrollpane.css" type="text/css" media="screen" />
<script src="<?php echo base_url(); ?>assets/js/jquery.jscrollpane.min.js"></script>




<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->



<!--- Show warnings or notices --->
<?php if($this->session->flashdata('notice') !== NULL): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('notice'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('warning') !== NULL): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('warning'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('success') !== NULL): ?><div class="alert alert-success" role="alert"><?php echo $this->session->flashdata('success'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('danger') !== NULL): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('danger'); ?></div><?php endif; ?>

<div id="top_content">
	<div id="top_left">
		<div id="app_links">
			<ul>
				<?php if($this->session->userdata('admin') || $this->session->userdata('super_admin')): ?>
				<li><a href="<?php echo base_url(); ?>admin">Administrator View</a></li>
				<?php endif; ?>
				<li><a class="selected" href="<?php echo current_url(); ?>"><?php echo phrase("BOOK A ROOM");?></a></li>
				<li><a href="<?php echo base_url(); ?>booking/">MAIN PAGE</a></li>
				<li><a href="<?php echo base_url(); ?>booking/my_bookings/">MY BOOKINGS</a></li>
				<li><a href="<?php echo base_url(); ?>mobile/">MOBILE</a></li>
				<li><a href="<?php echo base_url(); ?>logout">LOG OUT</a></li>
			</ul>
		</div>
		
		
		<div class="center" id="filter_link_title">Narrow your Search<a href="#" id="filter_link">+</a>
			<div id="filter_container" class="">
				<?php $num_rows = 5; $count = 0; ?>
				
				<!-- Disable autocomplete to prevent the browser from leaving boxes checked/unchecked on refresh -->
				<form id="herp" autocomplete="off">		
				
					<div class="filter_row">
					
					<?php if($count > 0 && $count % $num_rows == 0):?></div><div class="filter_row"><?php endif; ?>
					<label>
						<input type="checkbox" class="seat_checkbox filter_checkbox" name="seats" value="1-4" data-minseats="1" data-maxseats="4">
						<span></span>2 - 4 Seats
					</label>
					<br>
					<?php $count++; ?>
					
					<?php if($count > 0 && $count % $num_rows == 0):?></div><div class="filter_row"><?php endif; ?>
					<label>
						<input type="checkbox" class="seat_checkbox filter_checkbox" name="seats" value="5-8" data-minseats="5" data-maxseats="8">
						<span></span>5 - 8 Seats
					</label>
					<br>
					<?php $count++; ?>
					
					<?php if($count > 0 && $count % $num_rows == 0):?></div><div class="filter_row"><?php endif; ?>
					<label>
						<input type="checkbox" class="seat_checkbox filter_checkbox" name="seats" value="9-16" data-minseats="9" data-maxseats="16">
						<span></span>9 - 16 Seats
					</label>
					<br>
					<?php $count++; ?>
					
					
					<?php foreach($buildings->result() as $building): ?>
						<?php if($count > 0 && $count % $num_rows == 0):?></div><div class="filter_row"><?php endif; ?>
						
						<label>
							<input type="checkbox" class="building_checkbox filter_checkbox" name="building[]" value="<?php echo $building->building_id ?>">
							<span></span><?php echo $building->name; ?>
						</label>
						<br>
						
						<?php $count++; ?>
					<?php endforeach; ?>

					<?php if(!empty($resources_filter)):
						foreach($resources_filter as $resource): ?>
						<?php if($count > 0 && $count % $num_rows == 0):?></div><div class="filter_row"><?php endif; ?>
						
						<label>
							<input type="checkbox" class="resource_checkbox filter_checkbox" name="resource[]" value="<?php echo $resource['resource_id'] ?>">
							<span></span><?php echo $resource['name']; ?> 
						</label>
						<br>
						 
						<?php $count++; ?>
					<?php endforeach; 
						endif;
					?>
					
					</div>
				</form>
			
			
			</div>
			<div style="clear:both"></div>
		</div>
		
	</div>
	
	<h3 id="calendar_header" style="text-align: center; font-weight: bold; margin-top: 2em; width: 450px; float: right;"><span><?php echo phrase("Book a Room"); ?> -<br> <?php echo $settings['site_title']; ?></span></h3>
	
	
	<div class="calendar_container">
		<?php 
			echo $calendar;
		?>
	</div>
</div>

<?php if($this->input->get('date') !== NULL): ?>


<div style="clear:both"></div>


<div class="booking_container" id="booking_container">

	<?php
		//Figure out of any limits were reached
		$disabled_rooms = false;		
		
		foreach ($roles->result() as $role){
			if(isset($rooms[$role->role_id])){
				foreach($rooms[$role->role_id] as $room){
					if($limits['day_used'] >= $room->max_daily_hours || $limits['week_remaining'] <= 0){
						$disabled_rooms = true;
						break;
					}
				}
			}
			if($disabled_rooms) break;
		}
		
		if($disabled_rooms){
			echo '<div class="alert alert-danger" role="alert">'. phrase('Some rooms may not be available because you have reached your maximum daily/weekly limits') . '</div>';
		}
	?>
	
	<?php 
		//Show the site message (if not empty
		if(trim($settings['global_message']) !== ''){
			echo '<div class="alert alert-danger" role="alert">'. trim($settings['global_message']) . '</div>';

		}
	?>
	
	<?php 
		
		//Display the date for the selected day
		$date_raw = date_parse_from_format('Ymd', $this->input->get('date', TRUE));
		$date = mktime(0, 0, 0, $date_raw['month'], $date_raw['day'], $date_raw['year']);
		echo '<h3 id="booking_container_date">'.date('l F d, Y', $date).'</h3>';
		
		
	?>
	
	<?php foreach ($roles->result() as $role): ?>
		<?php 
			//Only show role type/table if rooms exist for it
			if(isset($rooms[$role->role_id])): 
		?>
		
	
		
		<?php if($hours['min'] == 2 || $hours['max'] == -1): ?>
				<h4><?php echo $role->name; ?></h4>
				<div class="alert alert-warning" role="alert">All rooms are closed!</div>
		<?php else: ?>
		
			<div class="role_title"><?php echo $role->name; ?><span class="role_title_collapse"><a href="#">+</a></span></div>
			<div class="table-wrapper">
					<table class="booking_table" style="width: 100%; border-collapse: initial;" cellspacing="0">
						<thead>
							
						<?php
							//Create the top row listing the times as table headers
							echo '<tr><th><div class="table_cell_height">Name</div></th>';
							
							$tStart = mktime(0,0,0) + round((($hours['min'] * 24) * 60 * 60)); 
							
							//Avoid going past midnight
							if($hours['max'] > 1){ $hours['max'] = 1; }
							
							$tEnd = mktime(0,0,0) + round((($hours['max'] * 24) * 60 * 60)) - 1800;

							$tNow = $tStart;

							while($tNow <= $tEnd){
							  echo '<th><div class="table_cell_height">'.date("g:iA",$tNow).'</div></th>';
							  $tNow += 60 * 30; //30 MINUTES (60 seconds * 30)
							}
							
							echo '</tr>';
							
						?>
					</thead>
					<tbody>			
					<?php
						//Get all the rooms for this role
						
						foreach($rooms[$role->role_id] as $room){
							
							//Create left-hand column showing room name (and hidden room features used for filtering (seats, whiteboard, computer, etc..))
							if($room->seats > 1){
								$seats = $room->seats . " seats";
							}
							else{
								$seats = "1 seat";
							}
							echo 	'<tr data-buildingid="'.$room->building_id.'" data-seats="'.$room->seats.'" class="room_row">';
							echo	'<th class="room_name"><div class="table_cell_height"><span class="room_name_container">'.$room->name .'</span><span class="room_seat_container"> &bull; '.$seats.'</span></div><div class="room_resources">';
							
							foreach($resources[$room->room_id]->result() as $resource){
								echo '<span class="resource_element" data-resourceid="'.$resource->resource_id.'">'.$resource->name . '</span>';
							}
							
							echo 	'</div></th>';
							
							//If no hours are set (usually from invalid hours data)
							if(!isset($hours[$room->building_id])){
								$hours[$room->building_id] = $hours[0];
							}
							
							//Check to see if opening hours are the same as min for the day. Placeholders may be needed otherwise.
							if($hours[$room->building_id]->STARTTIME == $hours[$room->building_id]->ENDTIME || $hours[$room->building_id]->HASCLOSURE == true || $hours[$room->building_id]->ISOPEN == false){
								//Room is closed for the day
								$numSlots = ceil(((($hours['max'] - $hours['min']) * 24) * 60) / 30) + 1;
								
								//Output the placeholder
								echo '<td colspan="'. $numSlots .'" class="closed booking_cell"><div class="table_cell_height">Closed</div></td>';
								
								$tStart = mktime(0,0,0, $date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours['min'] * 24) * 60 * 60); //Start the "closed" slot at the earliest time
							}
							else if($hours[$room->building_id]->STARTTIME > $hours['min'] || $hours[$room->building_id]->ISOPEN == false){
								//How big is the placeholder
								$numSlots = ceil((((($hours[$room->building_id]->STARTTIME - $hours['min']) * 24) * 60) / 30)- 0.005); //Subtract 0.005 to eliminate any rounding errors
								
								//Output the placeholder
								echo '<td colspan="'. $numSlots .'" class="closed booking_cell"><div class="table_cell_height">Closed</div></td>';
								
								//Adjust the starting time to be offset
								$tStart = mktime(0,0,0,$date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours[$room->building_id]->STARTTIME * 24) * 60 * 60); 
							}
							else {
								$tStart = mktime(0,0,0,$date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours[$room->building_id]->STARTTIME * 24) * 60 * 60); 
							}
							
							if($hours[$room->building_id]->ENDTIME > 1){
								$hours[$room->building_id]->ENDTIME = 1;
							}
							$tEnd =  mktime(0,0,0,$date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours[$room->building_id]->ENDTIME * 24) * 60 * 60) - 1800; 
							$tNow = $tStart;


							while($tNow <= $tEnd && $hours[$room->building_id]->ISOPEN == true && $hours[$room->building_id]->HASCLOSURE == false){
								$end_row = false;
								
								//Since block/recurring bookings advance "tNow", recheck to see if any new block/recurring
								//bookings start at the new time
								$recheck = true;
								while($recheck){
									if($end_row) break;
									
									$recheck = false;
								
									//Check for block bookings!
									foreach($block_bookings as $block_booking){
										//If block booking starts before opening, bump it to the opening time
										if(strtotime($block_booking['start']) < $tStart){
											$block_booking['start'] = date('Y-m-d H:i:s', $tStart);
										}
										
										//Since we bumped the start time forward, make sure it didn't pass the block booking end time. 
										//If it did, ignore the block booking (since the booking started/ended during closed hours)
										if($block_booking['end'] > $block_booking['start']){
											if($tNow >= strtotime($block_booking['start']) && $tNow < strtotime($block_booking['end'])){
												$bbStart = $tNow;							
												$bbEnd = strtotime($block_booking['end']);		

												if(array_key_exists($room->room_id, $block_booking['room'])){
													if($bbEnd > $tEnd){
														$bbEnd = $tEnd;	
													}
													
													$length = ($bbEnd - $bbStart);
													$colspan = ($bbEnd - $bbStart) / 60 / 30;
													
													$tNow += $length; 
													
													//If the block booking goes past the end time, set it to the end time
													if($tNow >= $tEnd){
														$tNow = $tEnd + (60*30); //Keep adding half hour increments until the end of the booking is reached
														$colspan += 1; //Need for the edge case, not sure why
														$end_row = true; //Reached the end of the day. Set flag
													}
													
													echo '<td class="closed booking_cell" colspan="'.$colspan.'"><div class="table_cell_height">'.$block_booking['reason'].'</div></td>';
													
													//Since we moved $tNow, recheck for bookings
													$recheck = true;
												}
											}
										}
									}
									
									//Check for recurring bookings here!
									foreach($recurring_bookings as $recurring_booking){
										//Does this booking apply to todays date? If not, skip it
										//If Days since reccuring booking start MOD interval == 0
										if(!(round(($tNow - strtotime($recurring_booking['start']))/(60*60*24)) % $recurring_booking['repeat_interval'] === 0)){
											continue;
										}
										//The recurruing booking applies to todays date. Change the start/end dates to "today"
										else{
											//Make sure the recurring booking has started (and isn't just upcoming)
											if($recurring_booking['start'] > date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, $date_raw['month'], $date_raw['day'], $date_raw['year']))){
												continue;
											}
											
											$recurring_booking['start'] = date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, $date_raw['month'], $date_raw['day'], $date_raw['year']));
											$recurring_booking['end'] =  date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['end'])),date("i", strtotime($recurring_booking['end'])),0, $date_raw['month'], $date_raw['day'], $date_raw['year']));
											
										}
										
										//If recurring booking starts before opening, bump it to the opening time
										if(strtotime($recurring_booking['start']) < $tStart){
											$recurring_booking['start'] = date('Y-m-d H:i:s', $tStart);
										}
									
										//Since we bumped the start time forward, make sure it didn't pass the recurring booking end time. 
										//If it did, ignore the recurring booking (since the booking started/ended during closed hours)
										if(strtotime($recurring_booking['end']) > strtotime($recurring_booking['start'])){
											
											if($tNow >= strtotime($recurring_booking['start']) && $tNow < strtotime($recurring_booking['end'])){
												$bbStart = $tNow;							
												$bbEnd = strtotime($recurring_booking['end']);		

												if(array_key_exists($room->room_id, $recurring_booking['room'])){
													if($bbEnd > $tEnd){
														$bbEnd = $tEnd;	
													}
												
													$length = ($bbEnd - $bbStart);
													$colspan = ($bbEnd - $bbStart) / 60 / 30;
													
													$tNow += $length; 
													
													//If the recurring booking goes past the end time, set it to the end time
													if($tNow >= $tEnd){
														$tNow = $tEnd + (60*30); //Keep adding half hour increments until the end of the booking is reached
														$colspan += 1; //Need for the edge case, not sure why
														$end_row = true;
													}
													
													echo '<td class="closed booking_cell" colspan="'.$colspan.'"><div class="table_cell_height">'.$recurring_booking['reason'].'</div></td>';
													
													//Since we moved $tNow, recheck for bookings
													$recheck = true;
												}
											}
										}
									}
								}
								if($end_row) break;
								
								//End block bookings
								
								//Calculate how many 30-minute slots are needed for this room
								$free_slot_width = $room->minimum_slot / 30;
								
								$next_booking = null;
									
								
								//Check if bookings existing between tNow & tNow + minimum slot (this is possible with rolling start times)
								if(isset($bookings[$room->room_id])){
									foreach ($bookings[$room->room_id] as $booking){
										
										$booking_duration = strtotime($booking->end) - strtotime($booking->start);
										$booking_offset = strtotime($booking->start) - $tNow;
										
										if($tNow + 60*$room->minimum_slot > strtotime($booking->start) && strtotime($booking->start) >= $tNow && (strtotime($booking->end) <= ($tNow + $booking_duration + $booking_offset))){
											$next_booking = $booking;
											break;
										}
									}
									
									
								}

								//Check for bookings
								if(isset($next_booking) && $next_booking !== null){
									if($tNow == strtotime($next_booking->start)){
										//Calculate how long this booking is for, and offset counter by that much
										$diff = round(abs(strtotime($next_booking->end) - strtotime($next_booking->start)) / 60,2);
										
										$booker_username = $next_booking->matrix_id;
										$booker_name = $next_booking->booker_name;
										
										//If this is your booking, or you are admin, show who booked it
										if($booker_username == $this->session->userdata('username')){
											echo '<td colspan="'.($diff/30) .'" class="my_booked_cell booking_cell"><div class="table_cell_height"><a href="'.base_url().'booking/edit_booking?booking_id='.$next_booking->booking_id.'">'.$booker_name.'</a></div></td>';
										}
										else if($this->session->userdata('super_admin') == TRUE || $this->session->userdata('admin')){
											echo '<td colspan="'.($diff/30) .'" class="booked_cell booking_cell"><div class="table_cell_height"><a href="'.base_url().'booking/edit_booking?booking_id='.$bookings[$room->room_id][$tNow]->booking_id.'">'.$booker_name.'</a></div></td>';
										}
										else if($role->is_private == FALSE){
											echo '<td colspan="'.($diff/30) .'" class="booked_cell booking_cell"><div class="table_cell_height">'.$booker_name.'</div></td>';
										}
										else{
											echo '<td colspan="'.($diff/30) .'" class="booked_cell booking_cell"><div class="table_cell_height">Booked</div></td>';
										}
										
											$tNow += 60 * $diff ; //Add "diff" minutes
									}
									else{
										//Start time was rolled over
										
										
										//Calculate the difference between tNow and next_booking->start and create a placeholder
										$placeholder_slots = (strtotime($next_booking->start) - $tNow) / 60 /30;
										
										echo '<td colspan="'. $placeholder_slots .'" class="not_avail booking_cell bleh"><div class="table_cell_height">'. date('g:iA', $tNow). '</div></td>';
										$tNow += ($placeholder_slots) * 30 * 60; 
									}
									
									
									
								}
								else{
									$uri = array(
									'slot='.mktime(date('H',$tNow), date('i', $tNow),0, $date_raw['month'], $date_raw['day'], $date_raw['year']), //From the timeslot as the given time & date
									'room_id='. $room->room_id,
									);
									$uri = implode($uri, '&amp;');

									
								
									//Check to see if the date is in the past
									if($room->requires_moderation && (time()+ MODERATION_TIME_DELAY > $tNow)){ 
										echo '<td colspan="'. $free_slot_width.'" class="not_avail booking_cell"><div class="table_cell_height">'.date("g:iA",$tNow).'</div></td>';
									}
									else if(!$room->requires_moderation && (time()+ TIME_DELAY > $tNow)){ 
										if($settings['advance_start'] == true && date('Y-m-d', $tNow) == date('Y-m-d')){
											//Need to check to make sure it is in the middle of a slot. Otherwise do nothing
											if($tNow + $room->minimum_slot*60 > time()){

												//Calculate slots from $tNow until current time
												$past_slots = ceil((time() - $tNow) / 60 / 30);
												
												//Output disabled slot from tNow to current time
												echo '<td colspan="'. $past_slots.'" class="not_avail booking_cell"><div class="table_cell_height">' . date("g:iA",$tNow).'</div></td>';
												
												
												//Calculate avail slot from current time to remainder of free_slot_width
												$new_slot_width = $free_slot_width - $past_slots;
												$new_start_time = $tNow + (30*$past_slots * 60);
												
												//Create new URI
												$uri = array(
														'slot='.mktime(date('H',$new_start_time), date('i', $new_start_time),0, $date_raw['month'], $date_raw['day'], $date_raw['year']), //From the timeslot as the given time & date
														'room_id='. $room->room_id,
													);
												$uri = implode($uri, '&amp;');
												
												if($new_slot_width > 0){
													echo '<td colspan="'. $new_slot_width.'" class="room_free booking_cell"><div class="table_cell_height"><a class="" href="'. base_url() . 'booking/book_room?' . $uri . '">'.date("g:iA",$new_start_time).'</a></div></td>';
												}
											}
											else{
												echo '<td colspan="'. $free_slot_width.'" class="not_avail booking_cell"><div class="table_cell_height">'.date("g:iA",$tNow).'</div></td>';
											}
											
										}
										else{
											echo '<td colspan="'. $free_slot_width.'" class="not_avail booking_cell"><div class="table_cell_height">'.date("g:iA",$tNow).'</div></td>';
										}
										
									}
									
									//If too far in the future (past the roles booking window)
									else if($tNow > (mktime(0,0,0, date("n"), date("j")+1)+($role->booking_window*24*60*60)) ){
										//If the slot goes past the end of day (possible if building hours for other buildings are longer then current room hours)
										while(($tNow + 30*$free_slot_width) >= $tEnd){
											$free_slot_width -=1;										
										}
										
										echo '<td colspan="'. $free_slot_width.'" class="not_avail booking_cell"><div class="table_cell_height">'.date("g:iA",$tNow).'</div></td>';									
									}
									
									//If there are not enough hours for the day/week to make a booking
									else if($limits['day_used'] >= $room->max_daily_hours || $limits['week_remaining'] <= 0){
											echo '<td colspan="'. $free_slot_width.'" class="not_avail booking_cell"><div class="table_cell_height">'.date("g:iA",$tNow).'</div></td>';
									}
									else{
										$overlap_found = false;
										
										//Make sure it doesn't overlap an upcoming block booking or recurring booking
										foreach($block_bookings as $block_booking){
											//If block booking is in the future           AND     (now + slot) passes the block booking start
											
											//MAKE SURE THE BOOKING INCLUDES THIS ROOM!!!!!!!
											if(array_key_exists($room->room_id, $block_booking['room'])){
												if(strtotime($block_booking['start']) > $tNow && ($tNow + ($room->minimum_slot*60) > strtotime($block_booking['start']) )){
													echo '<td colspan="'. $free_slot_width.'" class="closed booking_cell"><div class="table_cell_height">Unavailable</div></td>';
													$overlap_found = true;
													break;
												}
											}
										}
										
										foreach($recurring_bookings as $recurring_booking){
											if(!(round(($tNow - strtotime($recurring_booking['start']))/(60*60*24)) % $recurring_booking['repeat_interval'] === 0)){
												continue;
											}
											else{
												//Make sure the recurring booking has started (and isn't just upcoming)
												if($recurring_booking['start'] > date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, $date_raw['month'], $date_raw['day'], $date_raw['year']))){
													continue;
												}
												
												$recurring_booking['start'] = date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, $date_raw['month'], $date_raw['day'], $date_raw['year']));
												$recurring_booking['end'] =  date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['end'])),date("i", strtotime($recurring_booking['end'])),0, $date_raw['month'], $date_raw['day'], $date_raw['year']));
												
											}
											
											//MAKE SURE THE BOOKING INCLUDES THIS ROOM!!!!!!!
											if(array_key_exists($room->room_id, $recurring_booking['room'])){
											
												if(strtotime($recurring_booking['start']) > $tNow && ($tNow + ($room->minimum_slot*60) > strtotime($recurring_booking['start']) )){
													echo '<td colspan="'. $free_slot_width.'" class="closed booking_cell"><div class="table_cell_height">Unavailable</div></td>';
													$overlap_found = true;
													break;
												}
											}
										}
										
										
										//Make sure there is enough space for this slot before the end of the day
										if($tNow + ($room->minimum_slot*60) > ($tEnd + (30*60))){
											
											//Calculute the number of slots until $tEnd
											$slots_until_end = ($tEnd - $tNow +(30*60))/(30*60);

											echo '<td colspan="'. $slots_until_end.'" class="room_free booking_cell"><div class="table_cell_height"><a class="" href="'. base_url() . 'booking/book_room?' . $uri . '">'.date("g:iA",$tNow).'</a></div></td>';
											$overlap_found = true;
											
										}
										
										
										if(!$overlap_found){
											echo '<td colspan="'. $free_slot_width.'" class="room_free booking_cell"><div class="table_cell_height"><a class="" href="'. base_url() . 'booking/book_room?' . $uri . '">'.date("g:iA",$tNow).'</a></div></td>';
										}
									}
									
									$tNow += 60 * $room->minimum_slot; //Add the length of the admin-defined timeslot
									
								}
							}
							
							//Add placeholders to the end if it closes earlier then other rooms (ignore doing this if the room has a closure)
							if($hours[$room->building_id]->ENDTIME < $hours['max'] && !$hours[$room->building_id]->ISOPEN == false && $hours[$room->building_id]->HASCLOSURE == false && ($hours[$room->building_id]->STARTTIME != $hours[$room->building_id]->ENDTIME)){
								//How big is the placeholder
								$numSlots = round(((($hours['max'] - $hours[$room->building_id]->ENDTIME) * 24) * 60) / 30);
								
								//Output the placeholder
								echo '<td colspan="'. $numSlots .'" class="closed booking_cell"><div class="table_cell_height">Closed</div></td>';
							}
							
							echo '</tr>';
						}
					
					?>
					</tbody>
					</table>
			
			
		
			</div>
		<?php endif; ?>
		<?php endif; ?>
		
	<?php endforeach; ?>
	

	
</div>

<div id="privacy">
	<strong>Privacy Statement:</strong>  <?php echo phrase("The Study Room Booking module collects only the minimum amount of information from users necessary to book a room - this includes name and email address. This information is stored on a secure site, and will not be used for any other purpose."); ?>
</div>

	<!-- bottom scrollbar -->
	<div id="footer_scrollbar" style="width: 920px; overflow-x: scroll; position: fixed; bottom: 0; z-index: 999" ><div id="test" style="width: 5000px">&nbsp;</div></div>

<?php endif;?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/booking_main.js" /></script>

<script>
	jQuery('#test').css('width', jQuery('.booking_table').first().width() + 180);
	
	jQuery('#footer_scrollbar').on('scroll', function(){
		var scroll_position = this;
		
		jQuery('.table-wrapper').each(function(){
			jQuery(this).scrollLeft(jQuery(scroll_position).scrollLeft());
		});
	});
	
	jQuery('.table-wrapper').on('scroll', function(){
		var scroll_position = this;
		
		jQuery('#footer_scrollbar').scrollLeft(jQuery(scroll_position).scrollLeft());
	});
	
	jQuery(document).ready(function () {
		jQuery('#filter_container').jScrollPane({
			horizontalDragMinWidth: 70,
			horizontalDragMaxWidth: 70
		});
		
		
	});
	
	<?php
	//If user is looking at today, scroll to the current time!
	if($this->input->get('date') === date('Ymd')):
	
		$scroll = floor((time() - (mktime(0,$hours['min']*24*60))) / 1800) * 126; //Calculate time past from start to now (in seconds), divide by number of half hour blocks, multiply by 126 (width of a block)
		if($scroll < 0) $scroll = 0;
	?>
	var scroll_amount = <?php echo $scroll; ?>;
	jQuery('#footer_scrollbar').scrollLeft(scroll_amount);
	
	<?php endif; ?>
	
</script>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>