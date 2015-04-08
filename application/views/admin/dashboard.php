<?php ob_start();?>

<script src="<?php echo base_url(); ?>assets/js/highcharts.js"></script>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

	  <h1 class="page-header">Usage at a Glance for <?php echo date('F Y') ?></h1>
<!--	  <div class="row placeholders">
		<div class="col-xs-6 col-sm-3 placeholder">
		  <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		  <h4>Label</h4>
		  <span class="text-muted">Something else</span>
		</div>
		<div class="col-xs-6 col-sm-3 placeholder">
		  <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
		  <h4>Label</h4>
		  <span class="text-muted">Something else</span>
		</div>
		<div class="col-xs-6 col-sm-3 placeholder">
		  <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		  <h4>Label</h4>
		  <span class="text-muted">Something else</span>
		</div>
		<div class="col-xs-6 col-sm-3 placeholder">
		  <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
		  <h4>Label</h4>
		  <span class="text-muted">Something else</span>
		</div>
	  </div>

	  <h2 class="sub-header">Section title</h2>
	  <div class="table-responsive">
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th>#</th>
			  <th>Header</th>
			  <th>Header</th>
			  <th>Header</th>
			  <th>Header</th>
			</tr>
		  </thead>
		  <tbody>
			<tr>
			  <td>1,001</td>
			  <td>Lorem</td>
			  <td>ipsum</td>
			  <td>dolor</td>
			  <td>sit</td>
			</tr>
			
		  </tbody>
		</table>
	  </div>
	  -->
	  
	  <div id="hourly_usage" style="max-width: 600px; margin: 0 auto 3em auto" ></div>
	
	
	<div id="usage_by_type" style="max-width: 600px; margin: 0 auto 3em auto;"></div>
	
	
	
	
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
	
		//var_dump($hour_array);
	
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
	
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>