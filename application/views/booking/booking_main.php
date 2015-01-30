
<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/booking_main.css" type="text/css" media="screen" />



<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->

<h3 style="text-align: center; font-weight: bold">Ryerson University Library Room Booking</h3>

<!--- Show warnings or notices --->
<?php if($this->session->flashdata('notice') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('notice'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('warning') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('warning'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('success') !== FALSE): ?><div class="alert alert-success" role="alert"><?php echo $this->session->flashdata('success'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('danger') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('danger'); ?></div><?php endif; ?>

<div class="calendar_container">
	<?php 
		echo $calendar;
	?>
</div>
<div class="center"><a href="<?php echo base_url() . 'booking?month=' . date('Ym') . '&date=' . date('Ymd')?>" id="showtoday">SHOW TODAY</a></div>

<?php if($this->input->get('date') !== FALSE): ?>

<div class="center"><a href="#" id="filter_link">Filter</a>
	<div id="filter_container">
		<!-- Disable autocomplete to prevent the browser from leaving boxes checked/unchecked on refresh -->
		<form autocomplete="off">		
		<?php foreach($resources_filter->result() as $resource): ?>
		<div class="checkbox">
		  <label>
			<input type="checkbox" class="resource_checkbox filter_checkbox" value="<?php echo $resource->resource_id ?>">
			<?php echo $resource->name; ?>
		  </label>
		</div>
		<?php endforeach; ?>
		
		<?php foreach($buildings->result() as $building): ?>
		<div class="checkbox">
		  <label>
			<input type="checkbox" class="building_checkbox filter_checkbox" value="<?php echo $building->building_id ?>">
			<?php echo $building->name; ?>
		  </label>
		</div>
		<?php endforeach; ?>
		</form>
	
	</div>
</div>
<div style="clear:both"></div>


<div class="booking_container">
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
		
		<h4><?php echo $role->name; ?></h4>
		
		<?php if($hours['min'] == 2 || $hours['max'] == -1): ?>
				<div class="alert alert-warning" role="alert">All rooms are closed!</div>
		<?php else: ?>
		
			<div class="table-wrapper">
			
				
			
					<table class="" style="width: 100%;">
						<thead>
						<?php
							//Create the top row listing the times as table headers
							echo '<tr><th>&nbsp;</th>';
							
							$tStart = mktime(0,0,0) + (($hours['min'] * 24) * 60 * 60); 
							
							//Avoid going past midnight
							if($hours['max'] > (23.5/24)){ $hours['max'] = (23.5/24); }
							
							$tEnd = mktime(0,0,0) + (($hours['max'] * 24) * 60 * 60);

							$tNow = $tStart;

							while($tNow <= $tEnd){
							  echo '<th>'.date("g:iA",$tNow).'</th>';
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
							echo 	'<tr data-buildingid="'.$room->building_id.'" data-seats="'.$room->seats.'" class="room_row"><th class="room_name">'.$room->name .' ('.$seats.') <div class="room_resources">';
							
							foreach($resources[$room->room_id]->result() as $resource){
								echo '<span class="resource_element" data-resourceid="'.$resource->resource_id.'">'.$resource->name . '</span>';
							}
							
							echo 	'</div></th>';
							
							//Check to see if opening hours are the same as min for the day. Placeholders may be needed otherwise.
							if($hours[$room->external_id]->STARTTIME == $hours[$room->external_id]->ENDTIME || $hours[$room->external_id]->HASCLOSURE == true || $hours[$room->external_id]->ISOPEN == false){
								//Room is closed for the day
								$numSlots = ceil(((($hours['max'] - $hours['min']) * 24) * 60) / 30) + 1;
								
								//Output the placeholder
								echo '<td colspan="'. $numSlots .'" class="closed booking_cell">Closed</td>';
								
								$tStart = mktime(0,0,0, $date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours['min'] * 24) * 60 * 60); //Start the "closed" slot at the earliest time
							}
							else if($hours[$room->external_id]->STARTTIME > $hours['min']){
								//How big is the placeholder
								$numSlots = ceil(((($hours[$room->external_id]->STARTTIME - $hours['min']) * 24) * 60) / 30);
								
								//Output the placeholder
								echo '<td colspan="'. $numSlots .'" class="closed booking_cell">Closed</td>';
								
								//Adjust the starting time to be offset
								$tStart = mktime(0,0,0,$date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours[$room->external_id]->STARTTIME * 24) * 60 * 60); 
							}
							else {
								$tStart = mktime(0,0,0,$date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours[$room->external_id]->STARTTIME * 24) * 60 * 60); 
							}
							
							if($hours[$room->external_id]->ENDTIME > (23.5/24)){
								$hours[$room->external_id]->ENDTIME = (23.5/24);
							}
							$tEnd =  mktime(0,0,0,$date_raw['month'], $date_raw['day'], $date_raw['year']) + (($hours[$room->external_id]->ENDTIME * 24) * 60 * 60); 
							$tNow = $tStart;

							while($tNow <= $tEnd){
								$end_row = false;
								
								//Check for block bookings! (Nested loops, YUCK!)
								foreach($block_bookings as $block_booking){
									if(strtotime($block_booking['start']) < $tStart){
										$block_booking['start'] = date('Y-m-d H:i:s', $tStart);
									}
									
									//Since we bumped the start time forward, make sure it didn't pass the end time. If it did, ignore the block booking (since the booking started/ended during closed hours)
									if($block_booking['end'] > $block_booking['start']){
										if(array_key_exists($room->room_id, $block_booking['room']) && strtotime($block_booking['start']) == $tNow){
											$bbStart = strtotime($block_booking['start']);
											$bbEnd = strtotime($block_booking['end']);
											
											if($bbEnd > $tEnd){
												$bbEnd = $tEnd;	
											}
											
											$length = ($bbEnd - $bbStart);
											$colspan = ($bbEnd - $bbStart) / 60 / 30;
											
											$tNow += $length; 
											
											//If the block booking goes past the end time, set it to the end time
											if($tNow >= $tEnd){
												$tNow = $tEnd + (60*30);
												$colspan += 1; //Need for the edge case, not sure why
												$end_row = true;
											}
											
											echo '<td class="closed booking_cell" colspan="'.$colspan.'">'.$block_booking['reason'].'</td>';
											
											break;
										}
									}
								}
								
								if($end_row) break;
								
								//End block bookings
								
								//Check for bookings
								if(isset($bookings[$room->room_id][$tNow])){
									//Calculate how long this booking is for, and offset counter by that much
									$diff = round(abs(strtotime($bookings[$room->room_id][$tNow]->end) - strtotime($bookings[$room->room_id][$tNow]->start)) / 60,2);
									
									$booker_username = $bookings[$room->room_id][$tNow]->matrix_id;
									$booker_name = $bookings[$room->room_id][$tNow]->booker_name;
									
									//If this is your booking, or you are admin, show who booked it
									if($booker_username == $this->session->userdata('username') || $this->session->userdata('super_admin') == TRUE){
										echo '<td colspan="'.($diff/30) .'" class="booked_cell booking_cell">'.$booker_name.'</td>';
									}
									else{
										echo '<td colspan="'.($diff/30) .'" class="booked_cell booking_cell">Booked</td>';
									}
									$tNow += 60 * $diff ; //Add "diff" minutes
									
								}
								else{
									$uri = array(
									'slot='.mktime(date('H',$tNow), date('i', $tNow),0, $date_raw['month'], $date_raw['day'], $date_raw['year']), //From the timeslot as the given time & date
									'room_id='. $room->room_id,
									);
									$uri = implode($uri, '&amp;');
								
									//Check to see if the date is in the past, or too far in the future
									if(time() > $tNow){
										echo '<td class="not_avail booking_cell">'.date("g:iA",$tNow).'</td>';
									}
									else{
										echo '<td class="room_free booking_cell"><a class="" href="'. base_url() . 'booking/book_room?' . $uri . '">'.date("g:iA",$tNow).'</a></td>';
									}
									
									$tNow += 60 * 30; //Add 30 minutes
									
								}
							}
							
							//Add placeholders to the end if it closes earlier then other rooms (ignore doing this if the room has a closure)
							if($hours[$room->external_id]->ENDTIME < $hours['max'] && !$hours[$room->external_id]->ISOPEN == false && $hours[$room->external_id]->HASCLOSURE == false && ($hours[$room->external_id]->STARTTIME != $hours[$room->external_id]->ENDTIME)){
								//How big is the placeholder
								$numSlots = ceil(((($hours['max'] - $hours[$room->external_id]->ENDTIME) * 24) * 60) / 30);
								
								//Output the placeholder
								echo '<td colspan="'. $numSlots .'" class="closed booking_cell">Closed</td>';
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

<?php endif;?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/booking_main.js" /></script>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>