<?php ob_start();?>

<script src="<?php echo base_url(); ?>assets/js/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.datetimepicker.css"/>

<style>
#building_time_table td, #building_time_table th{
	padding: 0.8em;
	border: 1px solid black;
	background-color: #ddd;
}

#building_time_table td{
	background-color: #f5f5f5;
}

#building_time_table{
	margin-bottom: 2em;
}

</style>

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

<form role="form" method="post" action="<?= base_url() ?>admin/building_hours/edit/<?php echo $building->building_id; ?>/new_hours/submit ">
	<div class="form-group">
		<table id="building_time_table">
			<tr>
				<th></th>
				<th><label for="sunday">Sunday</label></th>
				<th><label for="Monday">Monday</label></th>
				<th><label for="Monday">Tuesday</label></th>
				<th><label for="Monday">Wednesday</label></th>
				<th><label for="Monday">Thursday</label></th>
				<th><label for="Monday">Friday</label></th>
				<th><label for="Monday">Saturday</label></th>
			</tr>
			
			<tr>
				<th style="min-width: 120px;">Opening Time</th>
				
				<?php for($i=0; $i < 7; $i++): ?>
				<td style="min-width: 120px;">
					 
					<?php $day_of_week = strtolower(date('D', strtotime("Sunday +{$i} days"))); ?>
					<select name="<?php echo $day_of_week; ?>_start">
						<?php 
							$start = mktime(0,0,0); 
							$end = $start + 24*60*60;
							
							$default_start = $start + 8*60*60; //8AM
							$default_end = $start + 22*60*60; //10PM
						?>
						
						<?php while($start <= $end): ?>
							<?php if($start == $end): ?><option value="24:00">12:00AM</option>
							<?php else: ?><option value="<?php echo date('H:i', $start); ?>" <?php if($start == $default_start) echo 'selected="selected"';?>><?php echo date('g:iA', $start); ?></option>
							<?php endif; ?>
						<?php $start += 60*30; ?>
						<?php endwhile; ?>
					</select> 
				
				</td>
				<?php endfor; ?>
			</tr>
			
			<tr>
				<th style="min-width: 120px;">Closing Time</th>
				<?php for($i=0; $i < 7; $i++): ?>
				<td style="min-width: 120px;">
					 
					<?php $day_of_week = strtolower(date('D', strtotime("Sunday +{$i} days"))); ?>
					<select name="<?php echo $day_of_week; ?>_end">
						<?php 
							$start = mktime(0,0,0); 
							$end = $start + 24*60*60;
						?>
						
						<?php while($start <= $end): ?>
							<?php if($start == $end): ?><option value="24:00">12:00AM</option>
							<?php else: ?><option value="<?php echo date('H:i', $start); ?>" <?php if($start == $default_end) echo 'selected="selected"';?>><?php echo date('g:iA', $start); ?></option>
							<?php endif; ?>
						<?php $start += 60*30; ?>
						<?php endwhile; ?>
					</select> 
				
				</td>
				<?php endfor; ?>
				
			</tr>
		</table>
		
		<table >
			<tr>
				<th><label for="start_date">Start Date</label></th>
				<th><label for="end_date">End Date</label></th>
			</tr>
			
			<tr>
				<td style="min-width: 300px;"><input  class="form-control date_time" type="text" id="start_date" name="start_date" value="" /></td>
				<td style="min-width: 300px;"><input class="form-control date_time" type="text" id="end_date" name="end_date" value="" /></td>
			</tr>
		</table>
		
		<br /><br />
		
		<input type="hidden" id="building_id" name="building_id" value="<?php echo $building->building_id; ?>" />
	</div>

	<button type="submit" class="btn btn-default">Submit</button>
	<a href="<?= base_url() ?>admin/building_hours/edit/<?php echo $building->building_id; ?>/"><button type="button" class="btn btn-default">Cancel</button></a>
</form>

<?php endif; ?>

<?php if($this->uri->segment(5) === 'new_closure'): ?>

<form role="form" method="post" action="<?= base_url() ?>admin/building_hours/edit/<?php echo $building->building_id; ?>/new_closure/submit">
	<div class="form-group">
		<label for="closure_date">Closure Date</label><br>
		<input class="form-control date_time" type="text" id="closure_date" name="closure_date" value="" />
		<input type="hidden" id="building_id" name="building_id" value="<?php echo $building->building_id; ?>" />
	</div>

	<button type="submit" class="btn btn-default">Submit</button>
	<a href="<?= base_url() ?>admin/building_hours/edit/<?php echo $building->building_id; ?>/"><button type="button" class="btn btn-default">Cancel</button></a>
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
			<?php foreach($hours->result() as $hour): ?>
			<tr>
				<td><?= date('l F d, Y', strtotime($hour->start_date)); ?></td>
				<td><?= date('l F d, Y', strtotime($hour->end_date)); ?></td>
				
				<td>
					<?php $daily_hours = json_decode($hour->hours_data);  ?>
					
					<strong style="display:inline-block; min-width: 100px;">Sunday:</strong> <?php echo $daily_hours->sun_start . " - " . $daily_hours->sun_end ?><br />
					<strong style="display:inline-block; min-width: 100px;">Monday:</strong> <?php echo $daily_hours->mon_start . " - " . $daily_hours->mon_end ?><br />
					<strong style="display:inline-block; min-width: 100px;">Tuesday:</strong> <?php echo $daily_hours->tue_start . " - " . $daily_hours->tue_end ?><br />
					<strong style="display:inline-block; min-width: 100px;">Wednesday:</strong> <?php echo $daily_hours->wed_start . " - " . $daily_hours->wed_end ?><br />
					<strong style="display:inline-block; min-width: 100px;">Thursday:</strong> <?php echo $daily_hours->thu_start . " - " . $daily_hours->thu_end ?><br />
					<strong style="display:inline-block; min-width: 100px;">Friday:</strong> <?php echo $daily_hours->fri_start . " - " . $daily_hours->fri_end ?><br />
					<strong style="display:inline-block; min-width: 100px;">Saturday:</strong> <?php echo $daily_hours->sat_start . " - " . $daily_hours->sat_end ?><br />
					
					
				</td>
				
				<td>options.....</td>
				
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