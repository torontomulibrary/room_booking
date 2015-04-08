<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/reports.css" type="text/css" media="screen" />
<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>
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
	
	
	<div id="hourly_usage" style="max-width: 600px; float: left" ></div>
	
	<div id="usage_by_type" style="max-width: 600px; mfloat: right"></div>
	
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
		
		<?php $type_data = $usage_by_type->row(); ?>
		
		$(function () {
			$('#usage_by_type').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
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