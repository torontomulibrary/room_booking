
<?php ob_start();?>




<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>

<div id="search_results">
	
	<?php if($bookings->num_rows() > 0): ?>
	<h3>You have <?php echo $bookings->num_rows(); ?> upcoming booking<?php if($bookings->num_rows > 1) echo 's'; ?></h3>

	<ul data-role="listview" data-inset="true">
		
		<?php foreach ($bookings->result() as $booking): ?>
		<li>
			<a href="<?php echo base_url(); ?>mobile/edit_booking?booking_id=<?php echo $booking->booking_id; ?>">
				<h3><?php echo $booking->name; ?> (<?php echo $booking->seats; ?> seats)</h3>
				<p><?php echo date('d-M-Y g:iA', strtotime($booking->start)); ?></p>
			</a>
		</li>
		<?php endforeach; ?>

	</ul>
	<?php else: ?>
		<?php print_message('Notice', 'You do not have any upcoming bookings'); ?>
	<?php endif; ?>
</div>

<div class="back_img">
	<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
</div>
		
	
		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>