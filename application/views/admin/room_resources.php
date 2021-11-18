<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>

<?php if($action === 'Add' || $action === 'Edit'): ?>
	
<!--- 
	Display new/edit form.
	Depending on which feature was selected, the form may be pre-populated
--->
<div class="container">
	<h2><?php echo $title; ?></h2>
	<hr>
	
	<!-- Display status message -->
	<?php if(!empty($error_msg)){ ?>
	<div class="col-xs-12">
		<div class="alert alert-danger"><?php echo $error_msg; ?></div>
	</div>
	<?php } ?>
	
	<div class="row">
		<div class="col-md-6">
			<form method="post" action="" enctype="multipart/form-data">
				<div class="form-group">
					<label>Name:</label>
					<input type="text" name="name" class="form-control" placeholder="Enter name" value="<?php echo !empty($resource['name'])?$resource['name']:''; ?>" >
					<?php echo form_error('name','<p class="help-block text-danger">','</p>'); ?>
				</div>
				<div class="form-group">
					<label>Description:</label>
					<input type="text" name="desc" class="form-control" placeholder="Enter description" value="<?php echo !empty($resource['desc'])?$resource['desc']:''; ?>" >
					<?php echo form_error('desc','<p class="help-block text-danger">','</p>'); ?>
				</div>
				<div class="form-group">
					<label>Image:</label>
					<input type="file" name="image" class="form-control-file">
					<?php echo form_error('image','<p class="help-block text-danger">','</p>'); ?>
					<?php if(!empty($resource['image'])){ ?>
						<div class="img-box">
							<img src="<?php echo base_url(IMAGE_DIR.$resource['image']); ?>">
						</div>
					<?php } ?>
				</div>
				<div class="form-check">
					<input type="checkbox" name="filter" class="form-check-input" <?php if(array_key_exists('can_filter', $resource) && $resource['can_filter'] == 'on') echo 'checked'; ?>>
					<label class="form-check-label">Allow Filtering</label>
					<?php echo form_error('desc','<p class="help-block text-danger">','</p>'); ?>
				</div>
				
				<a href="<?php echo base_url('admin/room_resources'); ?>" class="btn btn-secondary">Back</a>
				<input type="hidden" name="id" value="<?php echo !empty($resource['id'])?$resource['id']:''; ?>">
				<input type="submit" name="resSubmit" class="btn btn-success" value="SUBMIT">
			</form>
		</div>
	</div>
</div>

<?php else: ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<div class="container">
	<h2><?php echo $title; ?></h2>

	<!-- Display status message -->
	<?php if(!empty($success_msg)){ ?>
	<div class="col-xs-12">
		<div class="alert alert-success"><?php echo $success_msg; ?></div>
	</div>
	<?php }elseif(!empty($error_msg)){ ?>
	<div class="col-xs-12">
		<div class="alert alert-danger"><?php echo $error_msg; ?></div>
	</div>
	<?php } ?>

	<div class="row">
		<div class="col-md-12 head">
				<!-- Add link -->
				<a href="<?=base_url()?>admin/room_resources/add">Add a new room resource</a>
		</div>
			
		<!-- Data list table --> 
		<table class="table table-striped table-bordered">
			<thead class="thead-dark">
				<tr>
					<th>#</th>
					<th></th>
					<th>Name</th>
					<th>Description</th>
					<th>Allow Filtering</th>
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($resources)){
					$i=0;  
					foreach($resources as $r){
						$i++; 
						$image = !empty($r['image'])?'<img width="100px" src="'.base_url().IMAGE_DIR.$r['image'].'" alt="" />':'Empty';
						$filter = '<span class="glyphicon glyphicon-'.($r['can_filter']?'ok':'remove').'"></span>';
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $image; ?></td>
					<td><?php echo $r['name']; ?></td>
					<td><?php echo $r['desc']; ?></td>
					<td><?php echo $filter; ?></td>
					<td>
						<a href="<?= base_url() ?>admin/room_resources/edit/<?= $r['resource_id'] ?>">
							<button class="btn btn-default btn-sm" type="button">
								<span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit
							</button>
						</a> 
						<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/room_resources/delete/<?= $r['resource_id'] ?>" href="#">
							<button class="btn btn-default btn-sm" type="button">
								<span aria-hidden="true" class="glyphicon glyphicon-remove"></span> Remove
							</button>
						</a>
					</td>
				</tr>
				<?php } }else{ ?>
				<tr><td colspan="6">No resource(s) found...</td></tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

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