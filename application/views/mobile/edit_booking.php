
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
			<h3>Reservation Details</h3>
		</div>
		<div class="ui-body ui-body-a">
		
			<span class="detail_label">Date</span>
			<span class="detail"><?php echo date('l F j, Y', strtotime($booking->start)); ?></span>
			<div style="clear: both"></div>
			
			<span class="detail_label">Time</span>
			<span class="detail"><?php echo date('g:iA', strtotime($booking->start)); ?> - <?php echo date('g:iA', strtotime($booking->end)); ?></span>
			<div style="clear: both"></div>
			
			<span class="detail_label">Location</span>
			<span class="detail"><?php echo $room_desc->name; ?></strong>  (<?php echo $room_desc->seats; ?> seats)</span>
			<div style="clear: both"></div>
			
			<span class="detail_label">Reserved By</span>
			<span class="detail"><?php echo $booking->booker_name; ?></span>
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
			
			
		
		
		</div>
	</div>

<h2>Reservation Options:</h2>

<ul data-role="listview" data-inset="true">
	
	<li><a href="<?php echo base_url(); ?>mobile/cancel_booking?booking_id=<?php echo $booking->booking_id; ?>">Cancel reservation?</a></li>
	<li><a href="view_br.cfm?b=399545&del=y">Make another booking</a></li>
</ul>

<div class="back_img">
	<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
</div>		
		
		

		
		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>