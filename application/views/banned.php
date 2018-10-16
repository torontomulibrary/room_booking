<?php ob_start();?>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<h3 style="text-align: center; font-weight: bold; margin-bottom: 1em;"><?php echo SITE_TITLE; ?></h3>
<span style="text-align: center">
	<p>Your access to the room booking system has been suspended</p>

	<p><a href="<?php echo base_url(); ?>logout">Logout</a></p>
</span>
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>