<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/booking_form.css" type="text/css" media="screen" />



<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->
<h3 style="text-align: center; font-weight: bold">Ryerson University Library Room Booking</h3>

<?php 
//Verify the required fields are present (and the time is a half hour increment (don't let people mess with the URL)
//Also make sure the user is allowed to book this room, and that the room is not closed
if($this->input->get('slot') === FALSE || !is_numeric($this->input->get('slot')) || $this->input->get('room_id') === FALSE || !is_numeric($this->input->get('room_id')) || ($this->input->get('slot') % 1800) !== 0): ?>
	<div class="alert alert-danger" role="alert">An Error has occurred. </div>
<?php else: ?>	
		<?php $room_data = $room['room_data']->row(); ?>

		<div class="row">
			<div class="col-xs-9">
				<div class="row">
					<div class="col-xs-3 " style="background-color: #F0F0F0; border: 2px solid #c3c3c3; height:720px">
							<img style="margin: 3em auto;" src="<?php echo base_url() ?>assets/img/Book-Room-Icon3.png" alt="calendar">
						
						
						
						<span id="month_left"><?php echo date('F', $this->input->get('slot')) ?></span>
						<span id="date_left"><?php echo date('d', $this->input->get('slot')) ?></span>
					</div>
					<div class="col-xs-9" style="border: 2px solid #c3c3c3;  height:720px">
						
						
						<h3 id="page_title">Make a Reservation</h3>
						
						<?php if($limits['day_used'] >= $room_data->max_daily_hours): ?>
							<div class="alert alert-danger" role="alert">You have already booked the maximum allowable time for today</div>
						<?php else: ?>
						
							<form action="<?php echo base_url()?>booking/submit" method="post" autocomplete="off">
							
								<div class="form_left">When</div>
								<div class="form_right"><?php echo date('l M d, Y', $this->input->get('slot'));?></div>
								
								<div class="form_left">Where</div>
								<div class="form_right"><?php echo $room_data->name;?> (<?php echo $room_data->seats; echo ($room_data->seats>1)? ' seats': ' seat'?>)</div>
							
								
								
								<div class="form_left">Start Time</div>
								<div class="form_right"><?php echo date('g:ia', $this->input->get('slot'));?></div>
								
								<div class="form_left">Finish Time</div>
								<div class="form_right">
									<select name="finish_time">
										<?php
										
											$max_per_day = $room_data->max_daily_hours - $limits['day_used'];
											$max_per_week = $limits['week_remaining'];
											$start_time = $this->input->get('slot') + (30*60); //Start at the starting time + 30 minutes as the first slot to book
											
											//Figure out the end time. It's either the users max allowed booking time, or midnight
											//$end_time = $start_time + ($limits['booking_limit'] * 60 * 60) - (30*60);
											
											$end_time = $start_time + (($room_data->max_daily_hours - $limits['day_used'])*60*60 ); 
											
											//If there is another booking ahead of this, do not allow for overlap
											if($next_booking->num_rows > 0 && $next_booking->row()->start != null && $end_time > strtotime($next_booking->row()->start)){
											
													$end_time = strtotime($next_booking->row()->start);
													
											}
											
											//If greater then closing time
											if($end_time > (mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))) + round(($hours[$building['building_data']->row()->external_id]->ENDTIME *24*60*60)))){
												$end_time = mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))) + round(($hours[$building['building_data']->row()->external_id]->ENDTIME *24*60*60));
											}
											
											//If greater then midnight, set the end time to midnight
											if($end_time > mktime(0,0,0,date('n',$this->input->get('slot')), date('d',$this->input->get('slot'))+1)){
												$end_time = mktime(0,0,0,date('n',$this->input->get('slot')), date('d',$this->input->get('slot'))+1);
											}
											
											
											
											
											
											
											//TODO: Compare this to the buildings closing time & block bookings
											
											$slot = $start_time;
											while($slot <= $end_time){
												if($max_per_day <= 0 || $max_per_week <= 0) break;
												
												echo '<option value="'.$slot.'">'.date('g:ia', $slot).' (EST)</option>';
												
												$slot += 30*60;
												$max_per_day -= 0.5;
												$max_per_week -= 0.5;
											}
										?>
									</select> 
								</div>
							
							
								<div style="clear:both"></div>
								
								<br><br>
								
								<div class="form_left">Room Features</div><div style="clear:both"></div>
								
								<div class="">
									<ul>
										<?php 
											echo '<li>'.$room_data->seats; echo ($room_data->seats>1)? ' people': ' person'; echo ' max</li>';
											
											foreach($resources->result() as $resource){
												echo '<li>'.$resource->name.'</li>';
											}
										?>
									</ul>
								</div>
								
								<?php if(strlen($room_data->notes) > 0): ?>
									<div style="clear:both"></div>
									<br>
									<div class="form_left">Note</div><div style="clear:both"><strong><?php echo $room_data->notes; ?></strong></div>
								<?php endif; ?>
								
								<div style="clear:both"></div>
								<br><br>
								<div class="form_left">Additional Info</div><div style="clear:both"></div>
								<div><textarea name="comment" rows="6" cols="75"></textarea></div>
								
								<div class="form_buttons_container">
									<input id="submit_button" type="submit" value="Book Room" /><input type="button" id="cancel_button" value="Cancel" />
								</div>
							
								<input type="hidden" name="slot" value="<?php echo $this->input->get('slot'); ?>" />
								<input type="hidden" name="room_id" value="<?php echo $this->input->get('room_id'); ?>" />
								
							</form>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		  
		  <div class="col-xs-3" style="background-color: #F0F0F0; border: 2px solid #c3c3c3; height:720px">
			<h4>Please Note</h4>
			<ul>
				<li>Study Rooms are for groups of 3 or more people</li>
				<li>You must claim your room within the <strong>first 15 minutes</strong> of the booking, or your room may be taken by other eligible users</li>
				<li>Please read the <a href="http://library.ryerson.ca/info/policies/study-room-booking-policy/">full policy</a></li>  
			</ul>
			
			<h4>You are able to</h4>
			<ul>
				<li style="display:none">Make 2 study room bookings per day (keep this?)</li>
				<li>You have booked <strong><?php echo $limits['day_used']; ?> hours</strong> today</li>
				<li>Book <strong><?php echo $limits['week_remaining']; ?> hours</strong> in the study rooms this week</li>  
			</ul>
		  
		  </div>
		</div>
		
		<script type="text/javascript">
			$('#cancel_button').on('click',function(){
				window.location = "<?php echo base_url() . 'booking/booking_main?month='. date('Ym',$this->input->get('slot')) . '&date='.date('Ymd',$this->input->get('slot')); ?>";
			});
		</script>
	<?php endif; ?>
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>