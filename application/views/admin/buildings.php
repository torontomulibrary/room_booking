<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_building)): ?>
<?php if(!isset($new)): ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<h2>Current Buildings</h2>

<a href="<?=base_url()?>admin/buildings/new">Add a new Building</a>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Building ID</th>
				<th>External ID</th>
				<th>Building Name</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($buildings->result() as $building): ?>
			<tr>
				<td><?= $building->building_id ?></td>
				<td><?= $building->external_id ?></td>
				<td><?= $building->name ?></td>
				<td>
					<a href="<?= base_url() ?>admin/buildings/edit/<?= $building->building_id ?>">
						<span title="Edit" class="glyphicon glyphicon-edit"></span>
					</a> &nbsp; 
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/buildings/delete/<?= $building->building_id ?>" href="#">
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
	<?php $current = $current_building['building_data']->row(); ?>
	
<?php endif; ?>

<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<?php if(isset($new) || isset($current_building)): ?>

<?= (isset($new))? '<h2> Create a new building</h2>' : '<h2> Edit building</h2>'; ?>
<a style="display:block" href="<?= base_url() ?>admin/buildings">Back to all buildings</a><br />

<form role="form" method="post" action="<?= base_url() ?>admin/buildings/<?php if(isset($current)):?>update<?php else: ?>add<?php endif; ?>">
	<div class="form-group">
		<label for="building">Building Name</label>
		<input class="form-control" type="text" id="building" name="building" value="<?php if(isset($current)) echo $current->name ?>" />
	</div>
	
	<div class="form-group">
		<label for="building">External ID (used for hours of operation)</label>
		<input class="form-control" type="text" id="ext_id" name="ext_id" value="<?php if(isset($current)) echo $current->external_id ?>" />
	</div>

  
	<?php if(isset($current)): ?><input type="hidden" name="building_id" value="<?= $current->building_id ?>" /><?php endif; ?>
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
				<p>You are about to delete a building! This procedure is irreversible, and all of its rooms will also be deleted!</p>
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