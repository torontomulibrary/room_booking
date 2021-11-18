
<?php ob_start();?>


<style>

</style>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>



	<?php 
		$room_desc = $room['room_data'];
		$room_desc = $room_desc->row();
			
	?>
	<div class="ui-corner-all custom-corners" >
		<div class="ui-bar ui-bar-a">
			<h3>Create Booking</h3>
		</div>
		<div class="ui-body ui-body-a">
		
			<span class="detail_label">Date</span>
			<span class="detail"><?php echo date('l F j, Y', ($this->input->get('slot'))); ?></span>
			<div style="clear: both"></div>
			
			
			
			<span class="detail_label">Location</span>
			<span class="detail"><?php echo $room_desc->name; ?>  (<?php echo $room_desc->seats; ?> seats)</span>
			<div style="clear: both"></div>
			
			
			
			<?php if($resources->num_rows() > 0): ?>
					<?php //var_dump($resources); die; ?>
				<span class="detail_label">Room Features</span>
				<span style="display: inline-block; float: left">
					<?php foreach($resources as $resource): ?>
						<?php //echo $resource->name; ?><br />
					<?php endforeach; ?>
					
					
				</span>
				<div style="clear: both"></div>
			
				
			<?php endif; ?>
			
			<span style="margin-top: 0.6em" class="detail_label">Start Time</span>
			<span style="margin-top: 0.6em" class="detail"><?php echo date('g:iA', ($this->input->get('slot'))); ?></span>
			<div style="clear: both"></div>
		
			<span class="detail_label select_label">Finish Time</span>
											
			<form action="submit" method="POST">
				<select name="end_time" class="combo_box">
					<?php
						$room_data = $room['room_data']->row(); 
						
						$max_per_day = $room_data->max_daily_hours - $limits['day_used'];
						$max_per_week = $limits['week_remaining'];
						$start_time = $this->input->get('slot') + ($room_data->minimum_slot * 60);
						
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
						if($end_time > mktime(0,0,0,date('n',$this->input->get('slot')), date('d',$this->input->get('slot'))+1)){
							$end_time = mktime(0,0,0,date('n',$this->input->get('slot')), date('d',$this->input->get('slot'))+1);
						}
						
						
						$slot = $start_time;
						while($slot <= $end_time){
							if($max_per_day <= 0 || $max_per_week <= 0) break;
							
							//Check for block bookings
							if($this->booking_model->is_block_booked($slot, $slot, $this->input->get('room_id'))){
								echo '<option value="'.$slot.'">'.date('g:ia', $slot).' (EST)</option>';
								break;
							}
							
							echo '<option value="'.$slot.'">'.date('g:iA', $slot).'</option>';
							
							$slot += $room_data->minimum_slot*60;
							$max_per_day -= 0.5;
							$max_per_week -= 0.5;
						}
					?>
				</select>
				
				<div style="clear: both"></div>
				
				<?php
					foreach($interface->result() as $form_element){
						if($form_element->field_type === "select"){
							echo '	<div class="form-group" id="field_type_container">
										<label style="font-weight: bold;" class="form_label" for="fc_'.$form_element->fc_id.'">'.$form_element->field_name.'</label>
										<select name="fc_'.$form_element->fc_id.'" id="field_type" class="form-control">';
											foreach(json_decode($form_element->data) as $field_dropdown_option){
												echo '<option value="'.$field_dropdown_option.'">'.$field_dropdown_option.'</option>';
											}
							echo '		</select> 
									</div>';
						}
	
						else if($form_element->field_type === "text"){
							echo '	<div style="clear:both"></div>
									
										<label style="font-weight: bold;" for="fc_'.$form_element->fc_id.'">'.$form_element->field_name.'</label>
										<input class="form-control" name="fc_'.$form_element->fc_id.'"  type="text">';
									
							
							
						}
						else if($form_element->field_type === "textarea"){
							echo '	<div style="clear:both"></div>
									
									<span class="detail_label select_label">'.$form_element->field_name.'</span>
									<textarea rows="5" name="fc_'.$form_element->fc_id.'" class="text_area_height"></textarea>';
						}
						else if($form_element->field_type === "check") {
							switch($form_element->field_name) {
								case "cov19-1":
									'<div style="margin-top: 1em" class="form_left">'
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
								/*
								case "cov19-3":
									echo '<p>I accept that:</p>';
								break;
								case "cov19-11":
									echo '<p>In consideration of the University permitting me to use the Facilities I agree: </p>';
								break;
								*/
							}
							echo '<div class="form-group form-check-group">
									<input aria-label="Consent item" class="form-check-input form-checkbox" required data-msg="You must agree before continuing." id="fc_'.$form_element->fc_id.'" name="fc_'.$form_element->fc_id.'" type="checkbox">
									<label for="fc_'.$form_element->fc_id.'" class="form-check-label">'.$form_element->field_desc.'</label>
									<div style="clear:both"></div>
								</div>';
						}
						
					}
				?>

				<input type="hidden" name="slot" value="<?php echo $this->input->get('slot'); ?>">
				<input type="hidden" name="room_id" value="<?php echo $this->input->get('room_id'); ?>">
				
				<input type="submit" style="margin-top: 2em" value="Create Booking" />
			</form>
		</div>
	</div>


<div class="back_img" style="margin-top: 5em">
	
	<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
</div>		
		
		

		
		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>