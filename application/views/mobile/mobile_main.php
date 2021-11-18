
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>
	<?php if($this->session->flashdata('notice') !== NULL): ?><div class="alert alert-notice" role="alert"><?php print_message('Notice', $this->session->flashdata('notice')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('warning') !== NULL): ?><div class="alert alert-warning" role="alert"><?php print_message('Warning', $this->session->flashdata('warning')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('success') !== NULL): ?><div class="alert alert-success" role="alert"><?php print_message('Success', $this->session->flashdata('success')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('danger') !== NULL): ?><div class="alert alert-danger" role="alert"><?php print_message('Error', $this->session->flashdata('danger')); ?></div><?php endif; ?>
	
	<?php 
		//Show the site message (if not empty
		if(trim($settings['global_message']) !== ''){
			echo '<div class="alert alert-danger" role="alert">'. trim($settings['global_message']) . '</div>';

		}
	?>
	

	<ul data-role="listview" data-inset="true">

		<li><a rel="" href="<?php echo base_url(); ?>mobile/my_bookings">My Bookings</a></li>
		<li><a rel="" href="<?php echo base_url(); ?>mobile/book_room"><?php echo phrase('Book a Study Room'); ?></a></li>
		<li id="view_room_avail"><a rel="" href="<?php echo base_url(); ?>mobile/next_available">View Room Availability<br />(Within the next 3 hours)</a></li>
	</ul>


	<ul data-role="listview" data-inset="true">
		<li><a href="<?php echo $policy_url; ?>" rel="external">View Booking Policy</a></li>
		<li><a href="<?php echo base_url();?>logout" rel="external">Log Out</a></li>

	</ul>
		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>