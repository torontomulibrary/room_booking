<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<h2>Global settings</h2>

<form role="form" method="post" action="<?= base_url() ?>admin/global_settings/update">
	
	<h3>Appearance</h3>
	<div class="form-group">
		<label for="site_title">Site title</label>
		<input class="form-control" type="text" id="site_title" name="site_title" value="<?php echo $settings['site_title']; ?>" />
	</div>
	
	<div class="form-group">
		<label for="site_logo">Site logo (must be absolute URL)</label>
		<input class="form-control" type="text" id="site_logo" name="site_logo" value="<?php echo $settings['site_logo']; ?>" />
	</div>
	
	<div class="form-group">
		<label for="global_message">Site Message (shown to all users)</label>
		<textarea class="form-control" id="global_message" name="global_message" rows="3"><?php echo $settings['global_message']; ?></textarea>
	</div>

	<div class="form-check">
		<input type="checkbox" class="form-check-input" id="debug_mode" name="debug_mode" <?php echo ($settings['debug_mode'] == true ? 'checked' :  '') ?>>
		<label class="form-check-label" for="debug_mode">Enable Debug Mode?</label>
	</div>
	<hr>

	<h3>Booking Settings</h3>
	
	<div class="form-check">
		<input type="checkbox" class="form-check-input" id="advance_start" name="advance_start" <?php echo ($settings['advance_start'] == true ? 'checked' :  '') ?>>
		<label class="form-check-label" for="advance_start">Unbooked slots will advance their start time to the current time</label>
	</div>
	
	<div class="form-check">
		<input type="checkbox" class="form-check-input" id="allow_booking_overlap" name="allow_booking_overlap" <?php echo ($settings['allow_booking_overlap'] == true ? 'checked' :  '') ?>>
		<label class="form-check-label" for="allow_booking_overlap">Allow a person to make overlapping bookings</label>
	</div>
  
	<?php if(isset($current)): ?><input type="hidden" name="super_admin_id" value="<?= $current->admin_id ?>" /><?php endif; ?>
	<button type="submit" class="btn btn-default">Submit</button>
</form>



<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>