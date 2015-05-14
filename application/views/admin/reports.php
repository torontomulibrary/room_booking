<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/reports.css" type="text/css" media="screen" />
<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.datetimepicker.css"/>

<style>
.spacer{
	margin: 4em;
	clear: both;
}

.chart_holder{
	margin-right: 1em;
	width: 600px; 
	height: 400px; 
	float: left;
	
}

.text-chart{
	width: 600px; 
	height: 400px; 
	float: left; 
	border: 2px solid #4572A7;
	line-height: 150px;
	padding: 1em;
	font-size: 30px;
	font-family:"Lucida Grande", "Lucida Sans Unicode", Arial, Helvetica, sans-serif;
	text-align: center;
}

</style>

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

	<h1 class="page-header">Reports for <?php echo $date_str; ?></h1>

	<form class="form-horizontal" method="GET" action="">
		<div class="form-group">
			<label for="start_date" class="col-sm-1 ">Start Date</label>
			<div class="col-sm-2">
				<input type="text" class="form-control" id="start_date" name="start_date" <?php if($this->input->get('start_date') !== false) echo 'value="'.$this->input->get('start_date').'" '; ?> placeholder="">
			</div>
		</div>
		<div class="form-group">
			<label for="end_date" class="col-sm-1 ">End Date</label>
			<div class="col-sm-2">
				<input type="text" class="form-control" id="end_date" name="end_date" <?php if($this->input->get('end_date') !== false) echo 'value="'.$this->input->get('end_date').'" '; ?> placeholder="">
			</div>
		</div>
		
		<div class="form-group">
			<label for="room" class="col-sm-1 ">Role</label>
			<div class="col-sm-2">
				<select name="role" class="form-control">
					<option value=""></option>				
					<?php foreach($roles->result() as $role):?>
						<option value="<?php echo $role->role_id; ?>" <?php if($role->role_id == $this->input->get('role')) echo 'selected="SELECTED"'; ?>><?php echo $role->name; ?></option>				
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="building" class="col-sm-1 ">Building</label>
			<div class="col-sm-2">
				<select name="building" class="form-control">
				<option value=""></option>				
				<?php foreach($buildings->result() as $building):?>
					<option value="<?php echo $building->building_id; ?>" <?php if($building->building_id == $this->input->get('building')) echo 'selected="SELECTED"'; ?>><?php echo $building->name; ?></option>				
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<label for="room" class="col-sm-1 ">Room</label>
			<div class="col-sm-2">
				<select name="room" class="form-control">
					<option value=""></option>				
					<?php foreach($rooms->result() as $room):?>
						<option value="<?php echo $room->room_id; ?>" <?php if($room->room_id == $this->input->get('room')) echo 'selected="SELECTED"'; ?>><?php echo $room->name; ?></option>				
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-1 col-sm-2">
			<button type="submit" class="btn btn-default">Submit</button>
			<!--<button type="reset" value="Reset" class="btn btn-default">Reset</button>-->
			</div>
		</div>
	</form>  
	
	
		
	<div id="hourly_usage" class="chart_holder" ></div>
	
	<div id="usage_by_type" class="chart_holder" ></div>
	
	<div class="spacer">&nbsp;</div>
	
	
	<div id="usage_by_seats" class="chart_holder" ></div>
	<div id="ratio_by_seats" class="chart_holder" ></div>
	
		<div class="spacer">&nbsp;</div>
	
	<div id="days_booked_ahead" class="chart_holder" ></div>
	
	<?php $bookings = $total_bookings->row(); ?>
	<?php $checkouts = $total_checkouts->row(); ?>
	<div class="text-chart" style="">Total Bookings: <?php echo $bookings->total; ?><br>Total Checkouts: <?php echo $checkouts->total; ?></div>
	
	<script>
		<?php 
			$hour_array = array();
			
			//Prepopulate every column with zero
			for($i=7; $i <= 23; $i++){
				$hour_array[$i] = 0;
			}
		
			foreach($usage_by_hour->result() as $hour){
				$hour_array[$hour->hour_slot] = $hour->num_bookings;
			}
		?>
		
		$(function () {
			$('#hourly_usage').highcharts({
				chart: {
					borderWidth: 2,
					type: 'column'
				},
				title: {
					text: 'Booking Usage by Hour'
				},
				
				xAxis: {
					categories: [
						'7AM',
						'8AM',
						'9AM',
						'10AM',
						'11AM',
						'12PM',
						'1PM',
						'2PM',
						'3PM',
						'4PM',
						'5PM',
						'6PM',
						'7PM',
						'8PM',
						'9PM',
						'10PM',
						'11PM'
					],
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Number of bookings'
					}
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.0f} bookings</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [{
					showInLegend: false,               
					data: [<?php echo implode(',', $hour_array); ?>]

				}]
			});
		});
		
		$(function () {
			$('#days_booked_ahead').highcharts({
				<?php 
					$days_ahead_array = array();
					
					//Prepopulate every column with zero
					for($i=0; $i <= 21; $i++){
						$days_ahead_array[$i] = 0;
					}
				
					foreach($days_booked_ahead->result() as $days){
						$days_ahead_array[$days->days_ahead] = $days->bookings;
					}
				?>
				
				
				
				chart: {
					type: 'column',
					borderWidth: 2,
					height: 400
				},
				title: {
					text: 'Number of days booking was made ahead'
				},
				
				xAxis: {
					categories: [
						<?php
							for($i=0; $i <= 21; $i++){
								echo '"'.$i .' days",';
							}
						?>
					],
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Number of bookings'
					}
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.0f} bookings</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [{
					showInLegend: false,               
					data: [<?php echo implode(',', $days_ahead_array); ?>]

				}]
			});
		});
		
		
		
		
		
		<?php $type_data = $usage_by_type->row(); ?>
		
		$(function () {
			$('#usage_by_type').highcharts({
				chart: {
					borderWidth: 2,
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					height: 400
				},
				title: {
					text: 'Mobile vs. Desktop'
				},
				 subtitle: {
				text: 'Based on number of logins'
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Number of logins',
					data: [
						['Desktop',  <?php echo $type_data->desktop; ?>],
						['Mobile', <?php echo $type_data->mobile; ?>]
						
					]
				}]
			});
		});
		
		$(function () {
			$('#usage_by_seats').highcharts({
				chart: {
					borderWidth: 2,
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					height: 400
				},
				title: {
					text: 'Usage by Room Capacity'
				},
				
				tooltip: {
					pointFormat: '{series.name}: <b>{point.y} bookings</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							}
						},
						startAngle: -90,
						endAngle: 90,
						center: ['50%', '75%']
					}
				},
				series: [{
					type: 'pie',
					innerSize: '50%',
					name: 'Number of bookings',
					data: [
						
						<?php 
							foreach($usage_by_seats->result() as $usage){
								echo "['".$usage->seats." seats', ".$usage->total."],";
							}
						?>
						
					]
				}]
			});
		});
		
		$(function () {
			$('#ratio_by_seats').highcharts({
				chart: {
					borderWidth: 2,
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					height: 400
				},
				title: {
					text: 'Usage by Room Capacity (Adjusted)'
				},
				 subtitle: {
				text: 'Corrected to adjust for variable number of rooms'
				},
				
				tooltip: {
					pointFormat: '{series.name}: <b>{point.y:.2f} average bookings per room </b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							}
						},
						startAngle: -90,
						endAngle: 90,
						center: ['50%', '75%']
					}
				},
				series: [{
					type: 'pie',
					innerSize: '50%',
					name: 'Number of bookings',
					data: [
						
						<?php 
							foreach($ratio_by_seats->result() as $usage){
								echo "['".$usage->seats." seats', ".$usage->total."],";
							}
						?>
						
					]
				}]
			});
		});
		
	</script>
	
	<script>
		$('#end_date, #start_date').datetimepicker({
			dayOfWeekStart : 0,
			lang:'en',
			step: 30,
			format: 'Y-m-d',
			timepicker:false,
		});
	</script>
	
	
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>