<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/landing_page.css" type="text/css" media="screen" />



<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>

<div id="container">
	<div id="left_container">
		<div class="nav_link orange" id="book_room"><a href="<?php echo base_url(); ?>booking/booking_main">Book a Room</a></div>
		<div id="calendar_icon" class="nav_link grey "><a href="<?php echo base_url(); ?>booking/booking_main"><img src="<?php echo base_url();?>assets/img/Book-Room-Icon2.png" alt="Calendar" /></a></div>
		<div class="nav_link aqua"><a href="<?php echo base_url(); ?>booking/my_bookings/">My Bookings</a></div>
		<div class="nav_link dark"><a href="<?php echo $policy_url; ?>">Booking Policy</a></div>
		<div class="nav_link blue"><a href="<?php echo base_url();?>logout">Logout</a></div>
	</div>

	<div id="right_container">
		<div id="usage_title" class="dark">Usage</div>
		<div id="usage_graphic" class="grey">
			<span id="text_wrapper">Used <span id="used_hours"><?php echo $limits['day_used']; ?></span>HOURS</span>
			<div id="remaining">
				Used Today<br /><span class="remaining_number"><?php echo $limits['day_used']; ?> hours</span><br style="margin-bottom: 0.4em;"/>
				Remaining this Week<br /><span class="remaining_number"><?php echo $limits['week_remaining']; ?> hours</span>
			</div>
		</div>
	</div>
</div>

<div style="clear:both"></div>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>