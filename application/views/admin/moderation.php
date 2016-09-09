
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<!--
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
-->

<h2>Moderation Queue</h2>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Room</th>
				<th>Booking Start</th>
				<th>Booking End</th>
				<th>Booker Name</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($queue->result() as $row): ?>
			<tr>
				<td><?= $row->name ?></td>			
				<td><?= $row->start ?></td>
				<td><?= $row->end ?></td>
				<td><?= $row->booker_name ?></td>

				<td>
					<a href="<?= base_url(); ?>admin/moderate/approve/<?= $row->moderation_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-ok"></span>Approve</button>
					</a> 
					<a href="<?= base_url(); ?>admin/moderate/deny/<?= $row->moderation_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span>Deny</button>
					</a>
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>