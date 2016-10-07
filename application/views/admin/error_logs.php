
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!--
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
-->

<?php if(!isset($log_data)): ?>
<h2>Error Logs</h2>

<?php foreach($error_files as $file): ?>
	<div><a href="<?php echo base_url(); ?>admin/error_logs/<?php echo $file; ?>"><?php echo $file; ?></a> ( <a href="<?php echo base_url(); ?>admin/error_logs/delete/<?php echo $file; ?>">X</a> )</div>
<?php endforeach; ?>

<?php else: ?>
	<?php echo nl2br($log_data); ?>
<?php endif; ?>



<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>