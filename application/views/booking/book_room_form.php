<?php $role_data = json_decode($role->row()->interface_settings); ?>

<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/template/<?php echo $theme; ?>/css/booking_form.css" type="text/css" media="screen" />
<script src="<?php echo base_url(); ?>assets/js/jquery.validate-1.19.2.min.js"></script>

<style>
	.form-checkbox {
		width: 1.25em;
		height: 1.25em;
	}
	.form-check-group {
		border: 1px solid white;
		border-radius: 4px;
		padding: 4px;
	}
	.form-check-group.error {
		border-color: darkred;
	}
	.form-check-group .error {
		color: darkred;
		font-weight: 400;
		font-size: 0.95em;
	}
</style>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->
<h3 style="text-align: center; font-weight: bold"><?php echo phrase("Book a Room"); ?></h3>

<?php 
//Verify the required fields are present (and the time is a half hour increment (don't let people mess with the URL)
//Also make sure the user is allowed to book this room, and that the room is not closed
if($this->input->get('slot') === NULL || !is_numeric($this->input->get('slot')) || $this->input->get('room_id') === NULL || !is_numeric($this->input->get('room_id')) || ($this->input->get('slot') % 1800) !== 0): ?>
	<div class="alert alert-danger" role="alert">An Error has occurred. </div>
<?php else: ?>	
		<?php $room_data = $room['room_data']->row(); ?>

		<div class="row"  style="background-color: #F0F0F0; border: 2px solid #c3c3c3;  ">
			<div class="col-md-2" >
				<img style="margin: 3em auto;" src="<?php echo base_url() ?>assets/img/Book-Room-Icon3.png" alt="calendar">
				<span id="month_left"><?php echo date('F', $this->input->get('slot')) ?></span>
				<span id="date_left"><?php echo date('d', $this->input->get('slot')) ?></span>
			</div>	

			<div class="col-md-7" style="min-height:720px; background-color: #fff; border-left: 2px solid #c3c3c3; border-right: 2px solid #c3c3c3">
					
						
				<h3 id="page_title">Make a Reservation</h3>
				
				<?php if($limits['day_used'] >= $room_data->max_daily_hours): ?>
					<div class="alert alert-danger" role="alert">You have already booked the maximum allowable time for today</div>
				<?php else: ?>
				
					<form id="bookingForm" action="<?php echo base_url()?>booking/submit" method="post" autocomplete="off">
					
						<div class="form_left">When</div>
						<div class="form_right"><?php echo date('l M d, Y', $this->input->get('slot'));?></div><br>
						
						<div class="form_left">Where</div>
						<div class="form_right"><?php echo $room_data->name;?> <span id="res_num_seats">(<?php echo $room_data->seats; echo ($room_data->seats>1)? ' seats': ' seat'?>)<span></div>
					<br>
						
						
						<div class="form_left">Start Time</div>
						<div class="form_right"><?php echo date('g:ia', $this->input->get('slot'));?></div><br>
						
						<div class="form_left">Finish Time</div>
						<div class="form_right">
							<select name="finish_time">
								<?php
								
									$max_per_day = $room_data->max_daily_hours - $limits['day_used'];
									$max_per_week = $limits['week_remaining'];
									
									//We need to check if the booking start has been advanced (or not)
									if($settings['advance_start']){
										//Get the opening hours
										foreach ($hours as $building_open_time){
											if($building_open_time->LOCATION_ID == $room_data->building_id){
												$open_time = $building_open_time->STARTTIME * 24;
												break;
											}
											
										}
										$booking_date_ts = mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot')));
										$slot_offset = ($this->input->get('slot') - ($booking_date_ts + $open_time*60*60)) % ($room_data->minimum_slot*60);
										

										if($slot_offset == 0){
											$start_time = $this->input->get('slot') + ($room_data->minimum_slot*60); //Start at the starting time + the rooms interval minutes as the first slot to book
										}
										else{
											$start_time = $this->input->get('slot') + ($room_data->minimum_slot*60) - $slot_offset;
										}
									}
									else{
										$start_time = $this->input->get('slot') + ($room_data->minimum_slot*60); //Start at the starting time + the rooms interval minutes as the first slot to book
									}
									
									
									//Figure out the end time. It's either the users max allowed booking time, or midnight
									$end_time = $start_time + (($room_data->max_daily_hours - $limits['day_used'])*60*60 ); 
									
									//If there is another booking ahead of this, do not allow for overlap
									if($next_booking->num_rows() > 0 && $next_booking->row()->start != null && $end_time > strtotime($next_booking->row()->start)){
									
											$end_time = strtotime($next_booking->row()->start);
											
									}
									
									//If greater then closing time
									if($end_time > (mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))) + round(($hours[$building['building_data']->row()->building_id]->ENDTIME *24*60*60)))){
										$end_time = mktime(0,0,0, date('n',$this->input->get('slot')),date('j',$this->input->get('slot')),date('Y',$this->input->get('slot'))) + round(($hours[$building['building_data']->row()->building_id]->ENDTIME *24*60*60));
									}
									
									//If greater then midnight, set the end time to midnight
									if($end_time > mktime(0,0,0,date('n',$this->input->get('slot')), date('d',$this->input->get('slot'))+1, date('Y', $this->input->get('slot')))){
										$end_time = mktime(0,0,0,date('n',$this->input->get('slot')), date('d',$this->input->get('slot'))+1, date('Y', $this->input->get('slot')));
									}
									
									$slot = $start_time;
									
									if($slot > $end_time){
										echo '<option value="'.$end_time.'">'.date('g:ia', $end_time).' (EST)</option>';
									}
									else{
										while($slot <= $end_time){
											if($max_per_day <= 0 || $max_per_week <= 0) break;
											
											//Check for block bookings
											if($this->booking_model->is_block_booked($slot, $slot, $this->input->get('room_id'))){
												echo '<option value="'.$slot.'">'.date('g:ia', $slot).' (EST)</option>';
												break;
											}
											
											echo '<option value="'.$slot.'">'.date('g:ia', $slot).' (EST)</option>';
											
											$slot += $room_data->minimum_slot*60;
											$max_per_day -= 0.5;
											$max_per_week -= 0.5;
										}
									}
								?>
							</select> 
						</div>
					<br>
					
						<div style="clear:both"></div>
						
						<br><br>
						
						<div class="form_left"><?php echo phrase("Room Features");?></div><div style="clear:both"></div>
						
						<div class="">
							<ul>
								<?php 
									echo '<li>'.$room_data->seats; echo ($room_data->seats>1)? ' people': ' person'; echo ' max</li>';
									
									foreach($resources->result() as $resource){
										echo '<li>'.$resource->name.'</li>';
										
										if($resource->image != ''){
											echo '<img src="'.base_url(IMAGE_DIR.$resource->image).'">';
										}
									}
								?>
							</ul>
						</div>
						
						<?php if(strlen($room_data->notes) > 0): ?>
							<div style="clear:both"></div>
							<br>
							<div class="form_left">Note</div><div style="clear:both"><strong><?php echo $room_data->notes; ?></strong></div>
						<?php endif; ?>
						
						<!--
						<div style="clear:both"></div>
						<br><br>
						<div class="form_left">Additional Info</div><div style="clear:both"></div>
						<div><textarea name="comment" rows="6" cols="75" style="max-width: 490px"></textarea></div>
						-->

						<?php
							foreach($interface->result() as $form_element){
								if($form_element->field_type === "select"){
									echo '	<div class="form-group" id="field_type_container">
												<label class="form_label" for="fc_'.$form_element->fc_id.'">'.$form_element->field_name.'</label>
												<select name="fc_'.$form_element->fc_id.'" id="field_type" class="form-control">';
													foreach(json_decode($form_element->data) as $field_dropdown_option){
														echo '<option value="'.$field_dropdown_option.'">'.$field_dropdown_option.'</option>';
													}
									echo '		</select> 
											</div>';
								}
								else if($form_element->field_type === "text"){
									echo '	<div style="clear:both"></div>
											<div class="form-group">
												<label class="form_label" for="fc_'.$form_element->fc_id.'">'.$form_element->field_name.'</label>
												<input class="form-control" id="field_title" name="fc_'.$form_element->fc_id.'"  type="text">
											</div>';
									
									
								}
								else if($form_element->field_type === "textarea"){
									echo '	<div style="clear:both"></div>
											
											<div class="form_label">'.$form_element->field_name.'</div><div style="clear:both"></div>
											<div><textarea name="fc_'.$form_element->fc_id.'" rows="6" cols="75" style="max-width: 490px"></textarea></div>';
									
									
								}

								elseif($form_element->field_type === "check") {
									switch($form_element->field_name) {
										case "cov19-1":
											echo '<div style="margin-top: 1em" class="form_left">'
													.phrase("Informed Consent").'</div><div style="clear:both">
												</div>
												<p>
													While Ryerson University (the “University”) has put in place reasonable measures to reduce the spread of COVID-19, the University cannot guarantee that any individual 
													using the University’s facilities, or participating in activities organized by the University, whether on-campus or off-campus (including student internships and placements)
													(collectively, the “Activities”) will not become infected with COVID-19. Prior to participation, all participants in the Activities are required to complete the COVID-19
													<a href="https://www.ryerson.ca/covid-19/health-screening-reporting-cases/health-screening/student-health-screening/">Health Screening</a> prior to coming to campus each day. 
													Health Screening can be accessed via the <a href="https://www.ryerson.ca/community-safety-security/ryersonsafe/ryersonsafe-mobile-app/">RyersonSafe</a> mobile app or via 
													<a href="https://ryerson.apparmor.com/WebApp/default.aspx?menu=Start%20Health%20Screening">web browser</a> if you do not have the RyersonSafe app on your mobile phone.
												</p>
												<p>
													If I am unable to confirm or agree with all the statements below I understand I will not be able to make a Library study space booking at this time.
												</p>
												<p>	
													PLEASE NOTE: Completion of form is for a single (one day only) student booking time.  Student bookings are only available up to one week in advance. 
													Please contact <a href="mailto:access@ryerson.ca">access@ryerson.ca</a> for any cancellation required.
												</p>';
										break;
										/*case "cov19-3":
											echo '<p>I certify that:</p>';
										break;
										case "cov19-11":
											echo '<p>In consideration of the University permitting me to use the Facilities I agree: </p>';
										break;
										*/
									}
									echo '<div class="form-group form-check-group">
											<input aria-label="Consent item" class="form-check-input form-checkbox" required data-msg="You must agree before continuing." id="fc_'.$form_element->fc_id.'" name="fc_'.$form_element->fc_id.'" type="checkbox">
											<span class="form-check-label">'.$form_element->field_desc.'</span>
											<div style="clear:both"></div>
										</div>';
								}
								
							}
						?>
						
						
						<div class="form_buttons_container">
							<input id="submit_button" type="submit" value="<?php echo phrase("Book Room"); ?>" /><input type="button" id="cancel_button" value="Cancel" />
						</div>
					
						<input type="hidden" name="slot" value="<?php echo $this->input->get('slot'); ?>" />
						<input type="hidden" name="room_id" value="<?php echo $this->input->get('room_id'); ?>" />
						<div style="clear:both"></div>
					</form>
				<?php endif; ?>
			
			</div>
					
			<div class="col-md-3">
				<?php echo $role_data->sidebar_text; ?>

				<h4>You are able to</h4>
				<ul>
					<li>You have booked <strong><?php echo $limits['day_used']; ?> hours</strong> today</li>
					<li>Book <strong><?php echo $limits['week_remaining']; ?> hours</strong> <?php echo phrase("in the study rooms this week");?></li>  
				</ul>

			</div>
				
		</div>
		  
		 
		<script type="text/javascript">
			jQuery("#bookingForm").validate({
				submitHandler: function(form) {
					form.submit();
				},
				errorPlacement: function(error, el) {
					el.parent().append(error);
				},
				highlight: function(element, errorClass, validClass) {
					jQuery(element).addClass(errorClass).removeClass(validClass);
					jQuery(element).parent().addClass(errorClass).removeClass(validClass);
				},
				unhighlight: function(element, errorClass, validClass) {
					jQuery(element).removeClass(errorClass).addClass(validClass);
					jQuery(element).parent().removeClass(errorClass).addClass(validClass);
				}
			});
		</script>
		
		<script type="text/javascript">
			jQuery('#cancel_button').on('click',function(){
				window.location = "<?php echo base_url() . 'booking/booking_main?month='. date('Ym',$this->input->get('slot')) . '&date='.date('Ymd',$this->input->get('slot')); ?>";
			});
		</script>
	<?php endif; ?>
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>