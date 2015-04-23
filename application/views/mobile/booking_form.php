
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
				
				<span class="detail_label">Room Features</span>
				<span style="display: inline-block; float: left">
					<?php foreach($resources->result() as $resource): ?>
						<?php echo $resource->name; ?><br />
					<?php endforeach; ?>
					
					
				</span>
				<div style="clear: both"></div>
			
				
			<?php endif; ?>
			
			<span class="detail_label">Start Time</span>
			<span class="detail"><?php echo date('g:iA', ($this->input->get('slot'))); ?></span>
			<div style="clear: both"></div>
		
			<span class="detail_label select_label">Finish Time</span>
											
			<form action="submit" method="POST">
				<select name="end_time" class="combo_box">
					<?php
						$room_data = $room['room_data']->row(); 
						
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
						
						
						$slot = $start_time;
						while($slot <= $end_time){
							if($max_per_day <= 0 || $max_per_week <= 0) break;
							
							echo '<option value="'.$slot.'">'.date('g:iA', $slot).'</option>';
							
							$slot += 30*60;
							$max_per_day -= 0.5;
							$max_per_week -= 0.5;
						}
					?>
				</select>
				
				<div style="clear: both"></div>
					
				<span class="detail_label select_label">Notes</span>
				<textarea name="add_info" id="notes_field" class="" rows="5"></textarea>

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