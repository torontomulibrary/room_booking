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

	<form class="form-inline" method="POST" action="<?php echo base_url(); ?>admin/check_for_bookings">
		<h2 style="font-size: 1.7em">Search by Room</h2>
		
		<div class="form-group">
			<label for="room" class="">Room</label><br>
			
				<select multiple="multiple" size=" 12" name="room[]" class="form-control">
					<option value=""></option>				
					<?php foreach($rooms->result() as $room):?>
						<option value="<?php echo $room->room_id; ?>" <?php if(isset($searched_rooms) && in_array($room->room_id, $searched_rooms)) echo 'selected="SELECTED"'; ?>><?php echo $room->name; ?></option>				
					<?php endforeach; ?>
				</select>
		</div>
		
		<div class="form-group">
			<label for="start">Start Date</label><br>
			<input class="form-control date_time" type="text"  name="start" id="start" <?php if(isset($date_mode) && $date_mode && isset($searched_start_date)):?>value="<?php echo $searched_start_date; ?>" <?php endif; ?> />
		</div>
		
		<div class="form-group">
			<label for="end">End Date</label><br>
			<input class="form-control date_time" type="text" id="end" name="end" <?php if(isset($date_mode) && $date_mode && isset($searched_end_date)):?>value="<?php echo $searched_end_date; ?>" <?php endif; ?> />
		</div>
				
		<button type="submit" class="btn btn-default">Submit</button>
		
	</form>  
	
	<hr />
	
	<h2 style="font-size: 1.7em">Search by Full Name</h2>
	
	<form class="form-inline" method="POST" action="<?php echo base_url(); ?>admin/check_for_bookings">
		<div class="form-group">
			<label for="fullname">Name</label>
			<input type="text" class="form-control" id="fullname" name="fullname" placeholder="John Smith" <?php if(isset($fullname_mode) && $fullname_mode && isset($searched)):?>value="<?php echo $searched; ?>" <?php endif; ?>>
		</div>
		
		<button type="submit" class="btn btn-default">Submit</button>
	</form>
	<hr />
	
	<h2 style="font-size: 1.7em">Search by Username</h2>
	
	<form class="form-inline" method="POST" action="<?php echo base_url(); ?>admin/check_for_bookings">
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" class="form-control" id="username" name="username" placeholder="john.smith" <?php if(isset($username_mode) && $username_mode && isset($searched)):?>value="<?php echo $searched; ?>" <?php endif; ?>>
		</div>
		
		<button type="submit" class="btn btn-default">Submit</button>
	</form>
	


	<hr />

	<?php if(isset($searched)): ?>
	<div class="table-responsive">
		
		<h3 style="font-size: 1.3em">All Bookings</h3>
	
	
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Full Name</th>
					<th>Username</th>
					<th>Room</th>
					<th>Booking Date</th>
					<th>Booking Time</th>
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				<?php if(isset($upcoming_user_bookings)): ?>
				<?php foreach($upcoming_user_bookings->result() as $upcoming_user_booking): ?>
				<tr>
					<td><?php echo $upcoming_user_booking->booker_name ?></td>
					<td><?php echo $upcoming_user_booking->matrix_id ?></td>
					<td><?php echo $upcoming_user_booking->name ?></td>
					<td><?php echo date('M j, Y ',strtotime($upcoming_user_booking->start)); ?></td>
					<td><?php echo date('g:iA',strtotime($upcoming_user_booking->start)); ?> - <?php echo date('g:iA',strtotime($upcoming_user_booking->end)); ?></td>
					<td>
						<?php if(strtotime($upcoming_user_booking->start) > time()): ?>
						<a href="<?= base_url() ?>booking/edit_booking?booking_id=<?= $upcoming_user_booking->booking_id ?>">
							<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Modify Booking </button>
						</a>
						<?php endif; ?>
					
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				
				<?php if(isset($current_user_bookings)): ?>
				<?php foreach($current_user_bookings->result() as $current_user_booking): ?>
				<tr>
					<td><?php echo $current_user_booking->booker_name ?></td>
					<td><?php echo $current_user_booking->matrix_id ?></td>
					<td><?php echo $current_user_booking->name ?></td>
					<td><?php echo date('M j, Y ',strtotime($current_user_booking->start)); ?></td>
					<td><?php echo date('g:iA',strtotime($current_user_booking->start)); ?> - <?php echo date('g:iA',strtotime($current_user_booking->end)); ?></td>
					
					<td>
						<?php if(strtotime($current_user_booking->start) > time()): ?>
						<a href="<?= base_url() ?>booking/edit_booking?booking_id=<?= $current_user_booking->booking_id ?>">
							<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Modify Booking</button>
						</a>
						<?php endif; ?>
						
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>				
				
				<?php if(isset($past_user_bookings)): ?>
				<?php foreach($past_user_bookings->result() as $past_user_booking): ?>
				<tr>
					<td><?php echo $past_user_booking->booker_name ?></td>
					<td><?php echo $past_user_booking->matrix_id ?></td>
					<td><?php echo $past_user_booking->name ?></td>
					<td><?php echo date('M j, Y ',strtotime($past_user_booking->start)); ?></td>
					<td><?php echo date('g:iA',strtotime($past_user_booking->start)); ?> - <?php echo date('g:iA',strtotime($past_user_booking->end)); ?></td>
					
					<td>
						<?php if(strtotime($past_user_booking->start) > time()): ?>
						<a href="<?= base_url() ?>booking/edit_booking?booking_id=<?= $past_user_booking->booking_id ?>">
							<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Modify Booking</button>
						</a>
						<?php endif; ?>			
					</td>
				
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				
				<?php if(isset($fullname_bookings)): ?>
				<?php foreach($fullname_bookings->result() as $fullname_booking): ?>
				<tr>
					<td><?php echo $fullname_booking->booker_name ?></td>
					<td><?php echo $fullname_booking->matrix_id ?></td>
					<td><?php echo $fullname_booking->name ?></td>
					<td><?php echo date('M j, Y ',strtotime($fullname_booking->start)); ?></td>
					<td><?php echo date('g:iA',strtotime($fullname_booking->start)); ?> - <?php echo date('g:iA',strtotime($fullname_booking->end)); ?></td>
				
					<td>
						<?php if(strtotime($fullname_booking->start) > time()): ?>
						<a href="<?= base_url() ?>booking/edit_booking?booking_id=<?= $fullname_booking->booking_id ?>">
							<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Modify Booking</button>
						</a>
						<?php endif; ?>			
					
					</td>
				
				
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				
				<?php if(isset($selected_bookings)): ?>
				<?php foreach($selected_bookings->result() as $selected_booking): ?>
				<tr>
					<td><?php echo $selected_booking->booker_name ?></td>
					<td><?php echo $selected_booking->matrix_id ?></td>
					<td><?php echo $selected_booking->name ?></td>
					<td><?php echo date('M j, Y ',strtotime($selected_booking->start)); ?></td>
					<td><?php echo date('g:iA',strtotime($selected_booking->start)); ?> - <?php echo date('g:iA',strtotime($selected_booking->end)); ?></td>
				
					<td>
						<?php if(strtotime($selected_booking->start) > time()): ?>
						<a href="<?= base_url() ?>booking/edit_booking?booking_id=<?= $selected_booking->booking_id ?>">
							<button class="btn btn-default btn-sm" type="button"><span aria-hidden="true" class="glyphicon glyphicon-edit"></span> Modify Booking</button>
						</a>
						<?php endif; ?>
					</td>
				
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				
				
				
			</tbody>
		</table>
		
	
	</div>
	
	<?php endif; ?>
	
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