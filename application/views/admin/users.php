
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_user)): ?>
<?php if(!isset($new)): ?>

<!--
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
-->

<h2>Current Users</h2>

<a href="<?=base_url()?>admin/users/new">Add new User</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>User ID</th>
				<th>Matrix ID</th>
				<th>Name</th>
				<th>Roles</th>
				<th>Is Admin</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($users->result() as $user): ?>
			<tr>
				<td><?= $user->user_id ?></td>
				<td><?= $user->matrix_id ?></td>
				<td><?= $user->name ?></td>
				
				
				<td>
					<?php
						$roles = $this->role_model->get_user_roles($user->user_id);
						
						foreach($roles->result() as $role){
							echo $role->name ."<br />";
						}
					
					?>
				</td>
				
				<td><?= ($user->is_admin)? '<span class="glyphicon glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>' ?></td>
				<td><a href="<?= base_url() ?>admin/users/edit/<?= $user->user_id ?>"><span title="Edit" class="glyphicon glyphicon-edit"></span></a> &nbsp; <a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/users/delete/<?= $user->user_id ?>" href="#"><span title="Remove" class="glyphicon glyphicon-remove"></span></a></td>
				
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
<?php if(isset($new) || isset($current)): ?>

<?= (isset($new))? '<h2> Add new User</h2>' : '<h2> Edit User</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/users">Back to all users</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/users/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">

  <div class="form-group">
    <label for="matrix">Matrix ID</label>
    <input type="text" class="form-control" id="matrix" placeholder="Enter matrix username" name="matrix" <?php if(isset($current)): ?>value="<?= $current->matrix_id ?>" <?php endif; ?>>
  </div>
  
    <div class="form-group">
    <label for="matrix">Name</label>
    <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" <?php if(isset($current)): ?>value="<?= $current->name ?>" <?php endif; ?>>
  </div>

  <div class="form-group">
    <label for="role">Roles:</label>
    <select id="role" class="form-control" name="role[]" multiple size="7">
		<?php foreach($roles->result() as $role): ?>
			<option value="<?= $role->role_id ?>" <?php foreach ($user_roles->result() as $user_role): if($role->role_id === $user_role->role_id):?>selected="selected"<?php endif; endforeach;?>><?= $role->name ?></option>
		<?php endforeach; ?>
	</select>
  </div>
  

  
  <div class="checkbox">
    <label>
      <input type="checkbox" name="admin" <?php if(isset($current) && $current->is_admin != 0) echo 'checked' ?>> Make administrator
    </label>
  </div>
  
  <?php if(isset($current)): ?><input type="hidden" name="user_id" value="<?= $current->user_id ?>" /><?php endif; ?>
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