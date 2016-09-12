
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<?php if(!isset($current_permission)): ?>

<!--
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
-->

<h2>Admin Permissions</h2>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Role Name</th>
				<th>Can make block bookings</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($roles->result() as $role): ?>
			<tr>
				<td><?= $role->name ?></td>
				<td>
					<?php
						foreach($permissions->result() as $perm){
							if($role->role_id == $perm->role_id && $perm->can_block_book == 1){
								echo '<span class="glyphicon glyphicon glyphicon-ok"></span>';
								break;
							}
							else if ($role->role_id == $perm->role_id && $perm->can_block_book == 0){
								echo '<span class="glyphicon glyphicon-remove"></span>' ;
								break;
							}
						}
					?>
				</td>
				
				<td>
					<a href="<?= base_url() ?>admin/permissions/edit/<?= $role->role_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit</button>
					</a> 
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>




<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php else: ?>
<?php $current = $current_permission->row(); ?>
<?php $current_role = $role->row(); ?>


<?= '<h2> Edit Permissions</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/permissions">Back to all permissions</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/permissions/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">

  <div class="form-group">
    <label for="role_name">Role Name</label>
    <input readonly="readonly" type="text" class="form-control" id="role_name" value="<?= $current_role->name ?>">
  </div>
  


  
  <div class="checkbox">
    <label>
      <input type="checkbox" name="can_bb" <?php if(isset($current) && $current->can_block_book != 0) echo 'checked' ?>> Can block book?
    </label>
  </div>
  
  <?php if(isset($current)): ?><input type="hidden" name="role_id" value="<?= $current->role_id ?>" /><?php endif; ?>
  <button type="submit" class="btn btn-default">Submit</button>
</form>

<?php endif; ?>





<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>