
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!--
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
-->

<h2>Authentication Denied Log</h2>

<?php if($start - 50 >= 0):?>
<a href="<?php echo base_url(); ?>admin/auth_denied?start=<?php echo $start-50; ?>&end=<?php echo $start; ?>">&lt; Previous 50 </a>&nbsp;&nbsp;&nbsp;
<?php endif; ?>


<a href="<?php echo base_url(); ?>admin/auth_denied?start=<?php echo $start+50; ?>&end=<?php echo $end+50; ?>">Next 50 &gt;</a>

<div class="table-responsive">

	<table class="table table-striped">
		<thead>
			<tr>
				<th>Date</th>
				<th>Username</th>
				<th>CAS Roles</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($events->result() as $event): ?>
			<tr>
				<td><?= $event->date ?></td>
				<td><?= $event->username ?></td>
				<td><?= $event->data ?></td>
				
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>






<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>