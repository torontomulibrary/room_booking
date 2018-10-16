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
				
				<th>Role Name</th>
				<th>Hours per Week</th>
				<th>Booking Window</th>
				<th>Login Attributes</th>
				<th>Hide User Names for Booked Rooms</th>
				<th>Site Theme</th>
				<th>Priority</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($roles->result() as $role): ?>
			<tr>
				<td><?= $role->name ?></td>
				<td><?= ltrim($role->hours_per_week, '0'); ?></td>
				<td><?= ltrim($role->booking_window, '0'); ?></td>
				<td><?= $role->login_attributes ?></td>
				<td><?= ($role->is_private)? '<span class="glyphicon glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>' ?></td>
				<td><?= substr($role->site_theme, 0, -4); ?></td>
				<td><?= $role->priority ?></td>
				
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
		<label for="hours_week">Maximum bookable hours per week</label>
		<input class="form-control" type="text" id="hours_week" name="hours_week" value="<?php if(isset($current)) echo ltrim($current->hours_per_week, '0') ?>" />
	</div>
	
	<div class="form-group">
		<label for="booking_window">How far in the future a booking can be made (days)</label>
		<input class="form-control" type="text" id="booking_window" name="booking_window" value="<?php if(isset($current)) echo ltrim($current->booking_window, '0') ?>" />
	</div>
	
	<div class="form-group">
		<label for="booking_window">Login Attributes (Comma Seperated)</label>
		<input class="form-control" type="text" id="login_attributes" name="login_attributes" value="<?php if(isset($current)) echo ltrim($current->login_attributes, '0') ?>" />
		
		<?php if(USE_ACCESS_CENTRE_LIST === TRUE): ?>
			<span style="display: block; margin-top: 0.5em; padding-left: 2em; font-size: 0.9em; ">For special Access Centre role, please use the attribute: <span style="font-weight: bold; font-style: italic;">access_centre</span></span>
		<?php endif; ?>
		
		<?php if(USE_LIBSTAFF_LIST === TRUE): ?>
			<span style="display: block; margin-top: 0.5em; padding-left: 2em; font-size: 0.9em">For special Library staff role, please use the attribute: <span style="font-weight: bold; font-style: italic;">libstaff</span></span>
		<?php endif; ?>
	</div>
	
	<hr />
	
	<h3>Interface Settings</h3>
	
	<?php if(isset($current)) $interface_settings = json_decode($current->interface_settings); ?>
	
	
	
	<div class="form-group">
		<label for="priority">Priority (If a user has more then one role, which settings should be used) [Bigger number = higher priority]</label>
		 <select id="priority" class="form-control" name="priority">
			<?php for($i=0; $i <= 10; $i++): ?>
				<option value="<?php echo $i; ?>" <?php if(isset($current) && $current->priority == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="site_theme">Site Theme</label>
		 <select id="site_theme" class="form-control" name="site_theme">
			<?php foreach($site_themes as $site_theme): ?>
				<option value="<?php echo $site_theme; ?>" <?php if(isset($current) && $current->site_theme === $site_theme) echo 'selected="selected"'; ?>><?php echo $site_theme; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="is_private">Hide User names for booked rooms?</label>
		 <select id="is_private" class="form-control" name="is_private">
			<option value="1" <?php if(isset($current) && $current->is_private == "1") echo 'selected="selected"'; ?>>Yes</option>
			<option value="0" <?php if(isset($current) && $current->is_private == "0") echo 'selected="selected"'; ?>>No</option>
			
		</select>
	</div>
	
	<div class="form-group">
		<label for="conf_email">Confirmation Email Template</label>
		 <select id="conf_email" class="form-control" name="conf_email">
			<?php foreach($email_templates as $email_template): ?>
				<option value="<?php echo $email_template; ?>" <?php if(isset($interface_settings) && $interface_settings->conf_email === $email_template) echo 'selected="selected"'; ?>><?php echo $email_template; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="policy_url">URL to Booking Policy</label>
		<input class="form-control" type="text" id="policy_url" name="policy_url" value="<?php if(isset($interface_settings)) echo $interface_settings->policy_url; ?>" />
		<span style="display: block; margin-top: 0.5em; padding-left: 2em; font-size: 0.9em; ">Please include <i>http://</i> or <i>https://</i> in your URL</i></span>
	</div>

	<div class="form-group">
		<label for="sidebar_text">Booking Form - Sidebar Text (Supports HTML)</label>
		<textarea rows="5" class="form-control" name="sidebar_text"><?php if(isset($interface_settings)) echo $interface_settings->sidebar_text; ?></textarea>
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