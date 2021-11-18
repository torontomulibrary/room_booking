
<?php ob_start();?>
		
<style>
.ui-input-text{
	border: 0px;
}
</style>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>
	<?php if($this->session->flashdata('notice') !== NULL): ?><div class="alert alert-notice" role="alert"><?php print_message('Notice', $this->session->flashdata('notice')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('warning') !== NULL): ?><div class="alert alert-warning" role="alert"><?php print_message('Warning', $this->session->flashdata('warning')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('success') !== NULL): ?><div class="alert alert-success" role="alert"><?php print_message('Success', $this->session->flashdata('success')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('danger') !== NULL): ?><div class="alert alert-danger" role="alert"><?php print_message('Error', $this->session->flashdata('danger')); ?></div><?php endif; ?>
		
	<form method="GET" id="date_form"  action="book_room">
	<?php if($this->input->get('selected_date') === NULL): ?>
			<input id="date" type="hidden" name="selected_date" value="">
			<div id="cal_container"></div>
	<?php else: ?>
			<input id="date" type="hidden" name="selected_date" value="<?php echo $this->input->get('selected_date'); ?>">
	<?php endif; ?>
	
		
		
	<?php if($this->input->get('selected_date') !== NULL): ?>
			
		<?php
			//Make sure the user can book this far in the future
			$selected_date = strtotime($this->input->get('selected_date'));
			
			$window = 0; 
			
			foreach($roles->result() as $role){
				//Find the biggest booking window the user has
				if($role->booking_window > $window) $window = $role->booking_window;
			}
		
			
			
			
		
		?>
		
		<?php if(($selected_date - time()) > $window * 24 * 60 * 60): ?>
			<div class="alert alert-warning" role="alert">Bookings can only be made <?php echo $window ?> days in advance</div>		
		<?php elseif($hours['min'] == 2 || $hours['max'] == -1): ?>
				<div class="alert alert-warning" role="alert">All rooms are closed!</div>
		<?php else: ?>
			<select name="set_time">
			<?php
				$date = strtotime($this->input->get('selected_date'));
				$tStart = mktime(0,0,0,date('n', $date), date('j', $date), date('Y', $date)) + round((($hours['min'] * 24) * 60 * 60)); //Date + earliest time available
				
				//If same day, do not show past times
				if(date('Y-m-d', $tStart) === date('Y-m-d') && $tStart < time()){
					$hour = date("H");
					$minute =  date("i");
					
					if($minute >= 30){
						$minute = 0;
						$hour += 1;
					}
					else{
						$minute = 30;
					}
					
					
					$tStart = mktime($hour,$minute,0,date('n', $date), date('j', $date), date('Y', $date)); 
				}
				
				
				//Avoid going past midnight
				if($hours['max'] > 1){ $hours['max'] = 1; }
				
				$tEnd = mktime(0,0,0,date('n', $date), date('j', $date), date('Y', $date)) + round((($hours['max'] * 24) * 60 * 60)) - 1800;

				$tNow = $tStart;

				while($tNow <= $tEnd){
					if($this->input->get('set_time') !== NULL && $tNow == $this->input->get('set_time')){
						echo '<option value="'.$tNow.'" selected="selected">'.date("g:iA",$tNow).'</option>';
					}
					else{
						echo '<option value="'.$tNow.'">'.date("g:iA",$tNow).'</option>';
					}
				
				  
				  $tNow += 60 * 30; //30 MINUTES (60 seconds * 30)
				}
			?>
			</select>
			<input type="submit" value="Check Availability" />
		<?php endif; ?>
		
		
		
	<?php endif; ?>
	
	</form>


	<?php if($this->input->get('selected_date') !== NULL && $this->input->get('set_time') !== NULL): ?>
	
		<?php if($this->input->get('set_time') > time()): ?>
		
			<ul data-role="listview" data-inset="true">
				<?php foreach($roles->result() as $role): ?>
					<?php $count = 0; ?>
					<?php if(isset($rooms[$role->role_id])): ?>
				
						<li data-role="list-divider"><?php echo $role->name; ?>:</li>
						
						
						<?php foreach($rooms[$role->role_id] as $room): ?>
						
							<?php 
								//Does an existing booking start at this time? 
								if(!isset($bookings[$room->room_id][$this->input->get('set_time')])){
									
									
									$skip = false;
									
									//Has the time already passed?
									if($this->input->get('set_time') < time()){
											$skip = true;
									}
									
									//Do you have any hours remaining to book
									if($room->max_daily_hours <= $limits['day_used'] || $limits['week_remaining'] <= 0){
										$skip = true;
									}
									
									
									//Is this time during the building hours?
									$current_time = round(date('G', $this->input->get('set_time')) + (date('i', $this->input->get('set_time'))/60),1);
									if($current_time < round(($hours[$room->building_id]->STARTTIME) * 24,1) || $current_time > round(($hours[$room->building_id]->ENDTIME) * 24,1)){
										$skip = true;
									}
									
									//Does an earlier booking overlap this time?
									if(!$skip){
										if(isset($bookings[$room->room_id])){
											foreach($bookings[$room->room_id] as $booking){
												if((strtotime($booking->start) < $this->input->get('set_time')) && (strtotime($booking->end) > $this->input->get('set_time'))){
													$skip = true;
													break;
												}
											}
										}
									}
									
									//Is this time slot "block booked"? (also if we are already skipping the room, no need to check)
									if(!$skip){
										foreach($block_bookings as $block_booking){
											if(array_key_exists($room->room_id, $block_booking['room']) && strtotime($block_booking['start']) <= $this->input->get('set_time') && strtotime($block_booking['end']) > $this->input->get('set_time')){
												$skip = true;
												break;
											}
										}
										
										
										foreach($recurring_bookings as $recurring_booking){
											//Does this booking apply to todays date? If not, skip it
											//If Days since reccuring booking start MOD interval == 0
											if(!(round(($this->input->get('set_time') - strtotime($recurring_booking['start']))/(60*60*24)) % $recurring_booking['repeat_interval'] === 0)){
												continue;
											}
											//The recurruing booking applies to todays date. Change the start/end dates to "today"
											else{
												//Make sure the recurring booking has started (and isn't just upcoming)
												if($recurring_booking['start'] > date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, date('n',$this->input->get('set_time')), date('j',$this->input->get('set_time')), date('Y',$this->input->get('set_time'))))){
													continue;
												}
												
												$recurring_booking['start'] = date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['start'])),date("i", strtotime($recurring_booking['start'])),0, date('n',$this->input->get('set_time')), date('j',$this->input->get('set_time')), date('Y',$this->input->get('set_time'))));
												$recurring_booking['end'] =  date("Y-m-d G:i:s",mktime(date("G", strtotime($recurring_booking['end'])),date("i", strtotime($recurring_booking['end'])),0, date('n',$this->input->get('set_time')), date('j',$this->input->get('set_time')), date('Y',$this->input->get('set_time'))));
											}
											
											if($this->input->get('set_time') >= strtotime($recurring_booking['start']) && $this->input->get('set_time') < strtotime($recurring_booking['end'])){
												
												if(array_key_exists($room->room_id, $recurring_booking['room'])){
													
													$skip = true;
													break;
												}
											}
											
										}
									}
									
									//Does the time line up with the minimum interval
									if(!$skip){
										$open_time = round(($hours[$room->building_id]->STARTTIME) * 24,1); //8.5 = 8:30am
										
										$oTime = strtotime("today") + $open_time * 60 * 60;
										
										//diffTime is number of seconds between selected time & opening time
										$diffTime = $this->input->get('set_time') - $oTime;
										
										//Convert to minutes
										$diffTime = $diffTime / 60;

										if ($diffTime % $room->minimum_slot == 0){
										}
										else{
											$skip = true;
											break;
										}
								
										
										
									}
									
									
									
									if(!$skip){
										$count++;								
										echo '	<li>
													<a href="'.base_url().'mobile/create_booking?slot='.$this->input->get('set_time').'&room_id='.$room->room_id.'">'.$room->name .'(<strong>'.$room->seats .' seats</strong>)<br />
													<span id="font_pos">Available </span>
													<span class="showArrow secondaryWArrow">&nbsp;</span></a>
												</li>';
									}
									
								}
							?>
							
						<?php endforeach; ?>
						<?php if($count === 0): ?>
								<li>
									<span id="font_pos">No Available Rooms</span>
									
								</li>
							<?php endif; ?>
						
					<?php endif; ?>
				
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo "set time: ". $this->input->get('set_time'); echo "<br>Time: ".time(); ?>
		<?php endif; ?>
	<?php endif; ?>

	
	<div class="back_img" style="margin-top: 5em">
		<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
	</div>	
	

	<script src="<?php echo base_url(); ?>assets/datepicker/external/jquery-ui/jquery-ui.min.js"></script>
	
	<?php if($this->input->get('selected_date') === NULL): ?>
	<script>
	$(function() {
		$( "#cal_container" ).datepicker({
			inline: true,
			minDate: 0,
			dateFormat: 'dd-mm-yy',
			onSelect: function(date){
				$('#date').val(date);
				$('#date_form').submit(); 
			}
		});
	});
	</script>	
	<?php endif; ?>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>