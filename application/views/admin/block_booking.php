<?php ob_start();?>

<script src="<?php echo base_url(); ?>assets/js/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.datetimepicker.css"/>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_bb)): ?>
<?php if(!isset($new)): ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<h2>Block Booking</h2>

<a href="<?=base_url()?>admin/block_booking/new">Create new block booking</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Room(s)</th>
				<th>Reason</th>
				<th>Booked By</th>
				<th>Start</th>
				<th>End</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($block_bookings as $block_booking): ?>
			<tr>
				<td>
					<?php foreach($block_booking['room'] as $room):?>
						<?= $room['room_name'] ?><br />
					<?php endforeach; ?>
				</td>
				
				<td><?= $block_booking['reason'] ?></td>
				<td><?= $block_booking['booked_by'] ?></td>
				<td><?= $block_booking['start'] ?></td>
				<td><?= $block_booking['end'] ?></td>
				
				
				<td>
					<a href="<?= base_url() ?>admin/block_booking/edit/<?= $block_booking['block_booking_id']; ?>">
						<span title="Edit" class="glyphicon glyphicon-edit"></span>
					</a> &nbsp; 
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/block_booking/delete/<?= $block_booking['block_booking_id']; ?>" href="#">
						<span title="Remove" class="glyphicon glyphicon-remove"></span>
					</a>
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php endif; ?>

<!-- Back Button for edit page --->
<?php else: ?>
	<?php $current = $current_bb['block_booking_id']; ?>
	
	
	
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_bb)): ?>

<?= (isset($new))? '<h2>Create new block booking</h2>' : '<h2> Edit block booking</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/block_booking">Back to all block bookings</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/block_booking/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">
	<div class="form-group">
		<label for="reason">Reason</label>
		<input class="form-control" type="text" id="reason" name="reason" value="<?php if(isset($current)) echo $current_bb['reason'] ?>" />
	</div>
	
	<div class="form-group">
		<label for="start">Start</label>
		<input class="form-control date_time" type="text"  name="start" id="start" value="<?php if(isset($current)) echo date('Y-m-d H:i',strtotime($current_bb['start'])) ?>" />
	</div>
	
	<div class="form-group">
		<label for="end">End</label>
		<input class="form-control date_time" type="text" id="end" name="end" value="<?php if(isset($current)) echo date('Y-m-d H:i',strtotime($current_bb['end'])) ?>" />
	</div>
	
	<div class="form-group">
		<label for="rooms">Rooms</label>
		<select multiple id="rooms" class="form-control" name="rooms[]" size="10" style="height: 200px">
		<?php foreach($rooms->result() as $room): ?>
		<option value="<?= $room->room_id ?>" <?php if(isset($current) && array_key_exists($room->room_id, $current_bb['room'])):?>selected="selected"<?php endif; ?>><?= $room->name ?></option>
		<?php endforeach; ?>
		</select>
	</div>
	
	<?php //var_dump($permissions); ?>
	
	<div class="form-group">
		<label for="rooms">Permissions - Who can view / edit / delete this block booking</label>
		


		<select multiple id="permissions" class="form-control" name="permissions[]" size="10" style="height: 200px">
		<?php foreach($roles->result() as $role): ?>
		<option value="<?= $role->role_id ?>" <?php if(isset($current) && in_array($role->role_id, $permissions)): ?>selected="selected"<?php endif; ?>><?= $role->name ?></option>
		<?php endforeach; ?>
		</select>
	</div>

  
	<?php if(isset($current)): ?><input type="hidden" name="block_booking_id" value="<?= $current_bb['block_booking_id'] ?>" /><?php endif; ?>
	<button type="submit" class="btn btn-default">Submit</button>
</form>

<?php endif; ?>

<!--- Modal Dialog for delete option --->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
			</div>
		
			<div class="modal-body">
				<p>You are about to delete an block booking</p>
				<p>Do you want to proceed?</p>
				<p class="debug-url"></p>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a href="#" class="btn btn-danger danger">Delete</a>
			</div>
		</div>
	</div>
</div>
<!--- End Modal --->


	
<script>
	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
	});
	
	$('.date_time').datetimepicker({
		dayOfWeekStart : 0,
		lang:'en',
		step: 30,
		format: 'Y-m-d H:i'
	});
</script>



<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>