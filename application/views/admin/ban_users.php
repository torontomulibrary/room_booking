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

<h2>Ban Users</h2>

<a href="<?=base_url()?>admin/ban_users/new">Ban a user</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Matrix ID</th>
				<th>Date</th>
				<th>Reason</th>
				<th>Banned By</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($banned_users->result() as $user): ?>
			<tr>
				<td><?= $user->matrix_id ?></td>
				<td><?= $user->date ?></td>
				<td><?= $user->reason ?></td>
				<td><?= $user->reporter ?></td>			
				
				<td>
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/ban_users/delete/<?= $user->matrix_id ?>" href="#">
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
	<?php $current = $current_role->row(); ?>
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_role)): ?>

<h2>Ban User</h2>
<a style="display:block" href="<?= base_url() ?>admin/ban_users">Back to all banned users</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/ban_users/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">
	<div class="form-group">
		<label for="role_name">Matrix ID</label>
		<input class="form-control" type="text" id="role_name" name="matrix_id" value="" />
	</div>
	
	<div class="form-group">
		<label for="bookings_day">Reason</label>
		<textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
	</div>
	
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
				<p>You are about to remove a banned user! The user account will be able to make bookings again!</p>
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