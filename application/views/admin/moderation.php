
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
				
				<?php
					foreach($custom_fields->result() as $field){
						echo '<th>'.$field->field_name.'</th>';						
					}
				?>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($queue->result() as $row): ?>
			
			<?php
				//Get the results for any custom fields for this booking
				$custom_data = $this->booking_model->get_custom_fields_data($row->booking_id);
			
			?>
			
			<tr>
				<td><?= $row->name ?></td>			
				<td><?= $row->start ?></td>
				<td><?= $row->end ?></td>
				<td><?= $row->booker_name ?></td>
				
				<?php
					foreach($custom_fields->result() as $field){
						$match = 0;
						 
						foreach($custom_data->result() as $entry){
							if($entry->fc_id == $field->fc_id){
								echo '<td>'.htmlspecialchars($entry->data).'</td>';
								$match = 1;
								break;
							}
						}
						if($match == 0) echo '<td>&nbsp;</td>';
					}
				?>

				<td>
					<a href="<?= base_url(); ?>admin/moderate/approve/<?= $row->booking_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-ok"></span>Approve</button>
					</a> 
					<a href="<?= base_url(); ?>admin/moderate/deny/<?= $row->booking_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span>Deny</button>
					</a>
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>