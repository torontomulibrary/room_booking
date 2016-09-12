<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_role)): ?>
<?php if(!isset($new)): ?>

<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->

<h2>Current Roles</h2>

<a href="<?=base_url()?>admin/roles/new">Add a new Role</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Role ID</th>
				<th>Role Name</th>
				<th>Bookings per Day</th>
			
				<th>Hours per Week</th>
				<th>Booking Window</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($roles->result() as $role): ?>
			<tr>
				<td><?= $role->role_id ?></td>
				<td><?= $role->name ?></td>
				<td><?= $role->bookings_per_day ?></td>
			
				<td><?= ltrim($role->hours_per_week, '0'); ?></td>
				<td><?= ltrim($role->booking_window, '0'); ?></td>
				
				<td>
					<a href="<?= base_url() ?>admin/roles/edit/<?= $role->role_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit </button>
					</a> 
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/roles/delete/<?= $role->role_id ?>" href="#">
					<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span> Remove</button>
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
	<?php $current = $current_role->row(); ?>
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_role)): ?>

<?= (isset($new))? '<h2> Create a new role</h2>' : '<h2> Edit role</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/roles">Back to all roles</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/roles/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">
	<div class="form-group">
		<label for="role_name">Role Name</label>
		<input class="form-control" type="text" id="role_name" name="role_name" value="<?php if(isset($current)) echo $current->name ?>" />
	</div>
	
	<div class="form-group">
		<label for="bookings_day">Bookings per Day</label>
		<input class="form-control" type="text" id="bookings_day" name="bookings_day" value="<?php if(isset($current)) echo ltrim($current->bookings_per_day, '0') ?>" />
	</div>
	
	<div class="form-group">
		<label for="hours_week">Hours per Week</label>
		<input class="form-control" type="text" id="hours_week" name="hours_week" value="<?php if(isset($current)) echo ltrim($current->hours_per_week, '0') ?>" />
	</div>
	
	<div class="form-group">
		<label for="booking_window">Booking Window (Number of days in the future that members can make a booking)</label>
		<input class="form-control" type="text" id="booking_window" name="booking_window" value="<?php if(isset($current)) echo ltrim($current->booking_window, '0') ?>" />
	</div>
	
	

  
	<?php if(isset($current)): ?><input type="hidden" name="role_id" value="<?= $current->role_id ?>" /><?php endif; ?>
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
				<p>You are about to delete a role! This procedure is irreversible, and all users will be stripped of this role!</p>
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