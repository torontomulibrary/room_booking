
<?php ob_start();?>
		
<style>
.ui-input-text{
	border: 0px;
}
</style>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>
	<?php if($this->session->flashdata('notice') !== FALSE): ?><div class="alert alert-notice" role="alert"><?php print_message('Notice', $this->session->flashdata('notice')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('warning') !== FALSE): ?><div class="alert alert-warning" role="alert"><?php print_message('Warning', $this->session->flashdata('warning')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('success') !== FALSE): ?><div class="alert alert-success" role="alert"><?php print_message('Success', $this->session->flashdata('success')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('danger') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php print_message('Error', $this->session->flashdata('danger')); ?></div><?php endif; ?>
		
	<form method="GET" id="date_form"  action="book_room">
	<?php if($this->input->get('selected_date') === FALSE): ?>
			<input id="date" type="hidden" name="selected_date" value="">
			<div id="cal_container"></div>
	<?php else: ?>
			<input id="date" type="hidden" name="selected_date" value="<?php echo $this->input->get('selected_date'); ?>">
	<?php endif; ?>
	
		
		
	<?php if($this->input->get('selected_date') !== FALSE): ?>
			
		
		
		<?php if($hours['min'] == 2 || $hours['max'] == -1): ?>
				<div class="alert alert-warning" role="alert">All rooms are closed!</div>
		<?php else: ?>
			<select name="set_time">
			<?php
				$date = strtotime($this->input->get('selected_date'));
				$tStart = mktime(0,0,0,date('n', $date), date('j', $date), date('Y', $date)) + round((($hours['min'] * 24) * 60 * 60)); 
				
				//Avoid going past midnight
				if($hours['max'] > 1){ $hours['max'] = 1; }
				
				$tEnd = mktime(0,0,0,date('n', $date), date('j', $date), date('Y', $date)) + round((($hours['max'] * 24) * 60 * 60)) - 1800;

				$tNow = $tStart;

				while($tNow <= $tEnd){
					if($this->input->get('set_time') !== FALSE && $tNow == $this->input->get('set_time')){
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

	<?php if($this->input->get('selected_date') !== FALSE && $this->input->get('set_time') !== FALSE): ?>
	<?php //var_Dump($hours); ?>
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
							
							//Is this time during the building hours?
							$current_time = round(date('G', $this->input->get('set_time')) + (date('i', $this->input->get('set_time'))/60),1);
							if($current_time < round(($hours[$room->external_id]->STARTTIME) * 24,1) || $current_time > round(($hours[$room->external_id]->ENDTIME) * 24,1)){
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
							}
							
							if(!$skip){
								$count++;								
								echo '	<li>
											<a href="#">'.$room->name .'(<strong>'.$room->seats .' seats</strong>)<br />
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
	<?php endif; ?>

	
	<div class="back_img" style="margin-top: 5em">
		<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
	</div>	
	

	<script src="<?php echo base_url(); ?>assets/datepicker/external/jquery-ui/jquery-ui.min.js"></script>
	
	<?php if($this->input->get('selected_date') === FALSE): ?>
	<script>
	$(function() {
		$( "#cal_container" ).datepicker({
			inline: true,
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