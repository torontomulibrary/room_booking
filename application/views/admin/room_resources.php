<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_resource)): ?>
<?php if(!isset($new)): ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<h2>Room Resources</h2>

<a href="<?=base_url()?>admin/room_resources/new">Add a new room resource</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Allow filtering?</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($resources->result() as $resource): ?>
			<tr>
				<td><?= $resource->name ?></td>
				<td><?= $resource->desc ?></td>
				<td><?= ($resource->can_filter)? '<span class="glyphicon glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>' ?></td>
				<td>
					<a href="<?= base_url() ?>admin/room_resources/edit/<?= $resource->resource_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit </button>
					</a> 
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/room_resources/delete/<?= $resource->resource_id ?>" href="#">
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
	<?php $current = $current_resource->row(); ?>
	
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_resource)): ?>

<?= (isset($new))? '<h2>Create new resource</h2>' : '<h2> Edit Resource</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/room_resources">Back to all resources</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/room_resources/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">
	<div class="form-group">
		<label for="room_resource_name">Name</label>
		<input class="form-control" type="text" id="room_resource_name" name="room_resource_name" value="<?php if(isset($current)) echo $current->name ?>" />
	</div>
	
	<div class="form-group">
		<label for="resource_desc">Description</label>
		<textarea class="form-control" name="resource_desc" id="resource_desc" rows="3"><?php if(isset($current)) echo $current->desc ?></textarea>
	</div>
	
	<div class="filter">
		<label>
			<input type="checkbox" name="filter" <?php if(isset($current) && $current->can_filter != 0) echo 'checked' ?>> Allow filtering
		</label>
	</div>

  
	<?php if(isset($current)): ?><input type="hidden" name="room_resource_id" value="<?= $current->resource_id ?>" /><?php endif; ?>
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
				<p>You are about to delete an resource! This resource will be removed from any rooms containing it</p>
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