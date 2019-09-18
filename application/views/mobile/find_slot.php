
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>
	<?php if($this->session->flashdata('notice') !== NULL): ?><div class="alert alert-notice" role="alert"><?php print_message('Notice', $this->session->flashdata('notice')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('warning') !== NULL): ?><div class="alert alert-warning" role="alert"><?php print_message('Warning', $this->session->flashdata('warning')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('success') !== NULL): ?><div class="alert alert-success" role="alert"><?php print_message('Success', $this->session->flashdata('success')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('danger') !== NULL): ?><div class="alert alert-danger" role="alert"><?php print_message('Error', $this->session->flashdata('danger')); ?></div><?php endif; ?>
		
	
	<p>You pressed <?php echo $date; ?></p>

	
	<div class="back_img">
		<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
	</div>	


<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>