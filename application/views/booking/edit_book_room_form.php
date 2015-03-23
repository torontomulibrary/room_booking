<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/booking_form.css" type="text/css" media="screen" />



<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->

<h3 style="text-align: center; font-weight: bold">Ryerson University Libray Room Booking</h3>

<?php
//Verify the required fields are present (and the time is a half hour increment (don't let people mess with the URL)
//Also make sure the user is allowed to book this room, and that the room is not closed
if($this->input->get('booking_id') === FALSE || !is_numeric($this->input->get('booking_id'))): ?>
	<div class="alert alert-danger" role="alert">An Error has occured. </div>
<?php else: ?>	
		<?php $room_data = $room['room_data']->row(); ?>

		<?php
			//Check to see if booking is upcoming, current, or in the past
			if(strtotime($booking->end) < time()){
				$past_booking = true;
			}
			else if(time() > strtotime($booking->start) && time() < strtotime($booking->end)){
				$current_booking = true; 
			}
			else{
				$future_booking = true;
			}
			
		?>
		<div class="row">
			<div class="col-xs-9">
				<div class="row">
					<div class="col-xs-3 " style="background-color: #F0F0F0; border: 2px solid #c3c3c3; height:800px">
						
						
						<img style="margin: 3em auto;" src="<?php echo base_url() ?>assets/img/Book-Room-Icon3.png" alt="calendar">
						
					
						
						
						
						<span id="month_left"><?php echo date('F',strtotime($booking->start)) ?></span>
						<span id="date_left"><?php echo date('d', strtotime($booking->start)) ?></span>
					</div>
					<div class="col-xs-9" style="border: 2px solid #c3c3c3;  height:800px">
						
						
						<h3 id="page_title">Edit Reservation</h3>
						
						<!--<?php if(isset($past_booking) && $past_booking):?><h2>This booking is in the past</h2><?php endif; ?>-->
						
						<?php if($room_data->max_daily_hours - $limits['day_used'] + ((strtotime($booking->end) - strtotime($booking->start)) /60 / 60) <= 0): ?>
							<div class="alert alert-danger" role="alert">You have already booked the maximum allowable time for today</div>
						<?php elseif(isset($current_booking) && $current_booking === TRUE): ?>
							<div class="alert alert-info" role="alert">This booking is currently underway! You are able to checkout early from this booking, reclaiming any unused time (rounded up to the nearest half hour). 
							
							<br><br>Checking out will make <strong><?php echo $room_data->name;?></strong> available to other eligible users.</div>
						
							<div class="form_buttons_container">
								<form method="post" action="<?php echo base_url()?>booking/checkout">
									<input id="submit_button" type="submit" value="Checkout" />
									<input type="button" id="cancel_button" value="Cancel" />
									<input type="hidden" name="booking_id" value="<?php echo $this->input->get('booking_id'); ?>" />
								</form>
							
							</div>

						<?php else: ?>
						
							<form action="<?php echo base_url()?>booking/submit" method="post" autocomplete="off">
							
								<div class="form_left">When</div>
								<div class="form_right"><?php echo date('l M d, Y', strtotime($booking->start)); ?></div>
								
								<div class="form_left">Where</div>
								<div class="form_right"><?php echo $room_data->name;?> (<?php echo $room_data->seats; echo ($room_data->seats>1)? ' seats': ' seat'?>)</div>

								<div class="form_left">Reserved By</div>
								<div class="form_right"><?php echo $booking->booker_name;?></div>
								
								<div class="form_left">Start Time</div>
								<div class="form_right"><?php echo date('g:ia', strtotime($booking->start));?></div>
								
								<div class="form_left">Finish Time</div><div style="clear:both"></div>
								<div class="">
									<select name="finish_time">
										<?php
											//Figure out the allowance for the daily max (not counting the current booking)
											$max_per_day = $room_data->max_daily_hours - $limits['day_used'] + ((strtotime($booking->end) - strtotime($booking->start)) /60 / 60);
											
											$max_per_week = $limits['week_remaining'] + ((strtotime($booking->end) - strtotime($booking->start)) /60 / 60);
											$start_time = strtotime($booking->start) + (30*60); //Start at the starting time + 30 minutes as the first slot to book
											
											//Figure out the end time. It's either the users max allowed booking time, or midnight
											//$end_time = $start_time + ($limits['booking_limit'] * 60 * 60) - (30*60);
											
											$end_time = $start_time + $max_per_day * 60*60;
										
											
											//If there is another booking ahead of this, do not allow for overlap
											if($next_booking->num_rows > 0 && $next_booking->row()->start != null && $end_time > strtotime($next_booking->row()->start)){
											
													$end_time = strtotime($next_booking->row()->start);
													
													
											}
											
											//If greater then midnight, set the end time to midnight
											if($end_time > mktime(0,0,0,date('n',strtotime($booking->start)), date('d',strtotime($booking->start))+1)){
												$end_time = mktime(0,0,0,date('n',strtotime($booking->start)), date('d',strtotime($booking->start))+1);
											}
											
											//If greater then closing time
											if($end_time > (mktime(0,0,0, date('n',strtotime($booking->start)),date('j',strtotime($booking->start)),date('Y',strtotime($booking->start))) + round(($hours[$building['building_data']->row()->external_id]->ENDTIME *24*60*60)))){
												$end_time = mktime(0,0,0, date('n',strtotime($booking->start)),date('j',strtotime($booking->start)),date('Y',strtotime($booking->start))) + round(($hours[$building['building_data']->row()->external_id]->ENDTIME *24*60*60));
											}
											
											
											
											$slot = $start_time;
											while($slot <= $end_time){
												
												
												if($max_per_day <= 0 || $max_per_week <= 0) break;
												
												if($slot == strtotime($booking->end)){
													echo '<option value="'.$slot.'" selected="selected">'.date('g:ia', $slot).' (EST)</option>';
												}
												else{
													echo '<option value="'.$slot.'">'.date('g:ia', $slot).' (EST)</option>';
												}
												
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
								<div style="clear:both"></div>
								<br><br>
								
								<div class="form_left">Additional Info</div><div style="clear:both"></div>
								<div><textarea name="comment" rows="6" cols="75"><?php echo $booking->comment; ?></textarea></div>
								
								<div class="form_buttons_container">
									<input id="delete_button" type="button" value="Delete Booking" data-toggle="modal" data-target="#confirm-delete" /><input id="submit_button" type="submit" value="Edit Booking" /><input type="button" id="cancel_button" value="Cancel" />
								</div>
							
								<input type="hidden" name="slot" value="<?php echo strtotime($booking->start); ?>" />
								<input type="hidden" name="room_id" value="<?php echo $booking->room_id; ?>" />
								<input type="hidden" name="booking_id" value="<?php echo $this->input->get('booking_id'); ?>" />
								
							</form>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		  
		  <div class="col-xs-3" style="background-color: #F0F0F0; border: 2px solid #c3c3c3; height:800px">
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
		
		<!--- Modal Dialog for delete option --->
		<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Confirm Booking Cancellation</h4>
					</div>
				
					<div class="modal-body">
						<p>You are about to cancel your booking! The room will be made available to other eligible people.</p>
						<p>Do you want to proceed?</p>
						<p class="debug-url"></p>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<a href="#" class="btn btn-danger danger">Delete</a>
					</div>
				</div>
			</div>
		</div>
		<!--- End Modal --->


			
		<script>
			$('#confirm-delete').on('show.bs.modal', function(e) {
				$(this).find('.danger').attr('href', "<?php echo base_url() . 'booking/delete_booking?booking_id='.$this->input->get('booking_id');?>");
			})
		</script>

		
		<script type="text/javascript">
			$('#cancel_button').on('click',function(){
				window.location = "<?php echo base_url() . 'booking/booking_main?month='. date('Ym',strtotime($booking->start)) . '&date='.date('Ymd',strtotime($booking->start)); ?>";
			});
		</script>
	<?php endif; ?>
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>