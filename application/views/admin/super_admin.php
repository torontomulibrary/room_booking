<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_user)): ?>
<?php if(!isset($new)): ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<div class="alert alert-warning" role="alert">
<p>Warning! Adding a user here will grant them full access to the application, and the power to edit any setting. With great power comes great responsibility!</p>
</div>

<h2>Current Administrators</h2>

<a href="<?=base_url()?>admin/super_admin/new">Add a new Super Admin</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Admin ID</th>
				<th>Matrix ID</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($admins->result() as $admin): ?>
			<tr>
				<td><?= $admin->admin_id ?></td>
				<td><?= $admin->matrix_id ?></td>
				<td>
					<a href="<?= base_url() ?>admin/super_admin/edit/<?= $admin->admin_id ?>">
						<span title="Edit" class="glyphicon glyphicon-edit"></span>
					</a> &nbsp; 
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/super_admin/delete/<?= $admin->admin_id ?>" href="#">
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
	<?php $current = $current_user->row(); ?>
	
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_user)): ?>

<?= (isset($new))? '<h2> Add new Super Admin</h2>' : '<h2> Edit Admin</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/super_admin">Back to all super admins</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/super_admin/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">
	<div class="form-group">
		<label for="super_admin">Matrix ID</label>
		<input class="form-control" type="text" id="super_admin" name="super_admin" value="<?php if(isset($current)) echo $current->matrix_id ?>" />
	</div>

  
	<?php if(isset($current)): ?><input type="hidden" name="super_admin_id" value="<?= $current->admin_id ?>" /><?php endif; ?>
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
				<p>You are about to delete an administrator!</p>
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