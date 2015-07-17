
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_room)): ?>
<?php if(!isset($new)): ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<h2>Modify Rooms</h2>


<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Building</th>
				<th>Seats</th>
				<th>Bookable by</th>

				<th>Active</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rooms->result() as $room): ?>
			<tr>
				<td><?= $room->name ?></td>
				<td><?= $room->building ?></td>
				<td><?= $room->seats ?></td>
				
				<td>
					<?php
						$roles = $this->role_model->get_room_roles($room->room_id);
						
						foreach($roles->result() as $role){
							echo $role->name ."<br />";
						}
					
					?>
				</td>
				
				
				
				<td><?= ($room->is_active)? '<span class="glyphicon glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>' ?></td>
				<td><a href="<?= base_url() ?>admin/modify_rooms/edit/<?= $room->room_id ?>"><span title="Edit" class="glyphicon glyphicon-edit"></span></a></td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php endif; ?>

<!-- Back Button for edit page --->
<?php else: ?>
	<?php $current = $current_room['room_data']->row(); ?>
	
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_room)): ?>

<?= (isset($new))? '<h2> Create a new room</h2>' : '<h2> Edit room</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/modify_rooms">Back to all rooms</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/modify_rooms/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">

  <div class="form-group">
    <label for="roomNumber">Room Number </label>
    <input type="text" class="form-control" id="roomNumber" placeholder="Enter room number" name="room" <?php if(isset($current)): ?>value="<?= $current->name ?>" readonly <?php endif; ?>>
  </div>

  <div class="form-group">
		<label for="notes">Notes</label>
		<textarea class="form-control" name="notes" id="notes" rows="3"><?php if(isset($current)) echo $current->notes ?></textarea>
	</div>
  
  
  <?php if(isset($current)): ?><input type="hidden" name="room_id" value="<?= $current->room_id ?>" /><?php endif; ?>
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
				<p>You are about to delete a room! This procedure is irreversible.</p>
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
	})
</script>



<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>