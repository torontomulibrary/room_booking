<?php ob_start();?>

<script src="<?php echo base_url(); ?>assets/js/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.datetimepicker.css"/>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

	<?php 
		//Prepare a string to neatly display the report window
		if($this->input->get('start_date') !== FALSE && strlen($this->input->get('start_date')) > 0 && $this->input->get('end_date') !== FALSE && strlen($this->input->get('end_date')) > 0){
			$date_str = date('F j, Y', strtotime($this->input->get('start_date'))) . " - ". date('F j, Y', strtotime($this->input->get('end_date')));
		}
		else{
			$date_str = date('F Y');
		}
	
	?>

	<h1 class="page-header">Check for Bookings</h1>

	<form class="form-inline" method="GET" action="">
		<h2 style="font-size: 1.7em">Search by Room</h2>
		
		<div class="form-group">
			<label for="room" class="">Room</label><br>
			
				<select multiple="multiple" size=" 12" name="room" class="form-control">
					<option value=""></option>				
					<?php foreach($rooms->result() as $room):?>
						<option value="<?php echo $room->room_id; ?>" <?php if($room->room_id == $this->input->get('room')) echo 'selected="SELECTED"'; ?>><?php echo $room->name; ?></option>				
					<?php endforeach; ?>
				</select>
			
		</div>
		
		
		<div class="form-group">
			<label for="start">Start Date</label><br>
			<input class="form-control date_time" type="text"  name="start" id="start" value="" />
		</div>
		
		<div class="form-group">
			<label for="end">End Date</label><br>
				<input class="form-control date_time" type="text" id="end" name="end" value="" />
		</div>
				
		
		
		
			<button type="submit" class="btn btn-default">Submit</button>
		
		
		
	</form>  
	
	<hr />
	
	<h2 style="font-size: 1.7em">Search by Username</h2>
	
	<form class="form-inline">
		<div class="form-group">
			<label for="exampleInputName2">Username</label>
			<input type="text" class="form-control" id="exampleInputName2" placeholder="john.smith">
		</div>
		
		<button type="submit" class="btn btn-default">Submit</button>
	</form>

	<hr />

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
	
	
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>