<?php ob_start();?>

<script src="<?php echo base_url(); ?>assets/js/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.datetimepicker.css"/>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>


<?php if(!isset($current_building)): ?>

<!---
	Create a table listing all of the existing rooms, and options available
	for each room. This only appears when not editing/creating new rooms
--->

<h2>Manage Building Hours</h2>

<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>


				<th>Building Name</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($buildings->result() as $building): ?>
			<tr>


				<td><?= $building->name ?></td>
				<td>
					<a href="<?= base_url() ?>admin/building_hours/edit/<?= $building->building_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-time"></span> Manage Hours </button>
					</a> 
					
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php else: ?>

<?php $building = $current_building['building_data']->row() ?>

<h2>Manage <?php echo $building->name; ?> hours</h2>

<br />

<a href="<?=base_url()?>admin/building_hours/edit/<?php echo $building->building_id; ?>/new_hours"><button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> Add new building hours </button></a>
<a href="<?=base_url()?>admin/building_hours/edit/<?php echo $building->building_id; ?>/new_closure"><button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> Add a closure </button></a><br /><br />



<?php if($this->uri->segment(5) === 'new_hours'): ?>

you want to add some hours

<?php endif; ?>

<?php if($this->uri->segment(5) === 'new_closure'): ?>

<form role="form" method="post" action="<?= base_url() ?>admin/building_hours/edit/<?php echo $building->building_id; ?>/new_closure/submit">
	<div class="form-group">
		<label for="closure_date">Closure Date</label><br>
		<input class="form-control date_time" type="text" id="closure_date" name="closure_date" value="" />
		<input type="hidden" id="building_id" name="building_id" value="<?php echo $building->building_id; ?>" />
	</div>

	<button type="submit" class="btn btn-default">Submit</button>
</form>

<br><br>

<?php endif; ?>


<h3>Closures</h3>
<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Closure Date</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($closures->result() as $closure): ?>
			<tr>
				<td><?= date('l F d, Y', strtotime($closure->closure_date)); ?></td>
				<td>
					<a href="<?= base_url() ?>admin/building_hours/edit/<?php echo $building->building_id; ?>/remove_closure/<?php echo $closure->closure_id; ?>" >
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span> Remove Closure </button>
					</a>
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<br /><br />

<h3>Current Hours</h3>
<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Hours</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach(array() as $room): ?>
			<tr>
				<td><?= $room->name ?></td>
				<td><?= $room->building ?></td>
				
				<td>
					<a href="<?= base_url() ?>admin/rooms/edit/<?= $room->room_id ?>">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Edit </button>
					</a>
					
					<a data-toggle="modal" data-target="#confirm-delete" data-href="<?= base_url() ?>admin/rooms/delete/<?= $room->room_id ?>" href="#">
						<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span> Remove</button>
					</a>
				</td>
				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
	
<script>
	
	$('.date_time').datetimepicker({
		dayOfWeekStart : 0,
		timepicker:false,
		inline:true,
		lang:'en',
		step: 30,
		format: 'Y-m-d'
	});
</script>


<?php endif; ?>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>
