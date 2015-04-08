
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>
	<?php if($this->session->flashdata('notice') !== FALSE): ?><div class="alert alert-notice" role="alert"><?php print_message('Notice', $this->session->flashdata('notice')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('warning') !== FALSE): ?><div class="alert alert-warning" role="alert"><?php print_message('Warning', $this->session->flashdata('warning')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('success') !== FALSE): ?><div class="alert alert-success" role="alert"><?php print_message('Success', $this->session->flashdata('success')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('danger') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php print_message('Error', $this->session->flashdata('danger')); ?></div><?php endif; ?>
	
	

	<ul data-role="listview" data-inset="true">

		<li><a rel="" href="<?php echo base_url(); ?>mobile/my_bookings">My Bookings</a></li>
		<li><a rel="" href="<?php echo base_url(); ?>mobile/book_room">Book a Study Room</a></li>
		<li><a rel="external" href="next_available_room.cfm">View Room Availability<br />(Within the next 3 hours)</a></li>
	</ul>


	<ul data-role="listview" data-inset="true">
		<li><a href="http://library.ryerson.ca/info/policies/study-room-booking-policy/" rel="external">View Booking Policy</a></li>
		<li><a href="https://library.cf.ryerson.ca/studentbooking/mobile/feedback.cfm">Give Us Feedback</a></li>
		<li><a href="https://library.cf.ryerson.ca/studentbooking/no-cas/study_space.cfm" rel="external">Additional Study Locations</a></li>

		<li><a href="<?php echo base_url();?>logout" rel="external">Log Out</a></li>

	</ul>
		
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>