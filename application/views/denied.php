<?php ob_start();?>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<h3 style="text-align: center; font-weight: bold; margin-bottom: 1em;"><?php echo $settings['site_title']; ?></h3>
<span style="text-align: center">
	<p>You do not have access to the Room Booking System</p>

	<p>Please read the <a href="<?php echo DEFAULT_POLICY_URL; ?>">policy</a> to see if you should have access</p>
	
	<p>If you believe you have reached this page in error, please contact <a href="mailto:<?php echo SITE_ADMIN; ?>"><?php echo SITE_ADMIN; ?></a></p>

	<p><a href="<?php echo base_url(); ?>logout">Logout</a></p>
</span>
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>