
<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_field)): ?>
<?php if(!isset($new)): ?>

<!--
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
-->

<h2>Form Customization</h2>

<a href="<?=base_url()?>admin/form_customization/new">Add a new form component</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Field Title</th>
				<th>Field Type</th>
				<th>Roles</th>
				<th>Data</th>
				<th>Show results when Moderating</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($form_components->result() as $component): ?>
			<tr>
				<td><?= $component->field_name ?></td>
				<td><?= $component->field_type ?></td>
				
				
				
				
				<td>
					<?php 
						
						$roles = $this->interface_model->get_field_roles($component->fc_id);
						
						foreach($roles->result() as $role){
							echo $role->name ."<br />";
						}
						
					?>
				</td>
				
				<td><?= $component->data ?></td>
				
				<td><?= ($component->show_moderator)? '<span class="glyphicon glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>' ?></td>
				
				<td>
					<a href="<?= base_url() ?>admin/form_customization/edit/<?= $component->fc_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit</button>
					</a>
					
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/form_customization/delete/<?= $component->fc_id ?>" href="#">
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
	<?php $current = $current_field->row(); ?>
	
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current)): ?>

<?= (isset($new))? '<h2> Add a Form Component</h2>' : '<h2> Edit Field</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/form_customization">Back to all form components</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/form_customization/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">

	<div class="form-group">
		<label for="field_title">Field Title</label>
		<input type="text" class="form-control" id="field_title" placeholder="Enter the title for this field" name="field_title" <?php if(isset($current)): ?>value="<?= $current->field_name ?>" <?php endif; ?>>
	</div>
	
	<div class="form-group" id="field_type_container">
		<label for="field_type">Field Type</label>
		<select name="field_type" id="field_type" class="form-control">
			<option value="text" <?php if(isset($current) && $current->field_type === "text") echo 'selected="selected"'; ?>>Text Field</option>
			<option value="textarea" <?php if(isset($current) && $current->field_type === "textarea") echo 'selected="selected"'; ?>>Large Text Field</option>
			<option value="select" <?php if(isset($current) && $current->field_type === "select") echo 'selected="selected"'; ?>>Dropdown</option>
		</select> 
	</div>
  


	<div class="form-group">
		<label for="role">Roles:</label>
		<select id="role" class="form-control" name="role[]" multiple size="7">
			<?php foreach($user_roles->result() as $role): ?>
				<option value="<?= $role->role_id ?>" <?php if(isset($field_roles)){foreach ($field_roles->result() as $field_role): if($role->role_id === $field_role->role_id):?>selected="selected"<?php endif; endforeach;}?>><?= $role->name ?></option>
			<?php endforeach; ?>
		</select>
	</div>
  
    
  <div class="checkbox">
    <label>
      <input type="checkbox" name="show_moderator" <?php if(isset($current) && $current->show_moderator != 0) echo 'checked' ?>> Show results when Moderating
    </label>
  </div>
  
  <?php if(isset($current)): ?><input type="hidden" name="fc_id" value="<?= $current->fc_id ?>" /><?php endif; ?>
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
	});
	
	if($("#field_type").val() == "select"){
		
		
		<?php
			if(isset($current->data)){
				echo "create_select_form(true);";
				
				foreach(json_decode($current->data) as $row){
					echo 'add_select_value("'.$row.'");';
				}
				
			}
			else{
				echo "create_select_form(false);";
			}
		?>
	}
	else{
		delete_select_form();
	}
	
	$("#field_type" ).change(function() {
		if($("#field_type").val() == "select"){
			create_select_form(false);
		}
		else{
			delete_select_form();
		}
	});
	
	var select_items;
	
	function create_select_form(prepopulate){
		select_items = 0;
		delete_select_form();
		
		$("#field_type_container").after('<div class="form-group" id="select_values_container"><fieldset style="padding: 2em; margin-left: 2em; border: 1px solid #000000" id="select_values_fieldset"></fieldset></div>');
		
		if(prepopulate == false){
			add_select_value("");
		}
		
	}
	
	function add_select_value(value){
			
			$('#add_more_button').remove();
			
			$('#select_values_fieldset').append(
				'<label for="select_field_title">Dropdown Item #' + (select_items+1) + '</label>'+
				'<input style="margin-bottom: 1em" type="text" class="form-control" id="select_field_title" placeholder="" name="select_field_title[]" value="'+value+'">' +
				'<button id="add_more_button" type="button" class="btn btn-default" aria-label="Left Align" >Add Another Field</button>'
			);
			
			$('#add_more_button').on('click', function(){
				add_select_value("");
			});
			
			select_items++;
	}
	
	
	
	function delete_select_form(){
		$('#select_values_container').remove();
	}
	
</script>



<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>