
<?php ob_start();?>
		
<style>
.ui-input-text{
	border: 0px;
}
</style>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>
	<?php if($this->session->flashdata('notice') !== FALSE): ?><div class="alert alert-notice" role="alert"><?php print_message('Notice', $this->session->flashdata('notice')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('warning') !== FALSE): ?><div class="alert alert-warning" role="alert"><?php print_message('Warning', $this->session->flashdata('warning')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('success') !== FALSE): ?><div class="alert alert-success" role="alert"><?php print_message('Success', $this->session->flashdata('success')); ?></div><?php endif; ?>
	<?php if($this->session->flashdata('danger') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php print_message('Error', $this->session->flashdata('danger')); ?></div><?php endif; ?>
		
	<?php if($this->input->get('selected_date') === FALSE): ?>
		<form method="GET" id="date_form"  action="book_room">
			<input id="date" type="hidden" name="selected_date" value="">
			<div id="cal_container"></div>
	<?php else: ?>
		<form method="GET" id="date_form"  action="create_booking">
			<input id="date" type="hidden" name="selected_date" value="<?php echo $this->input->get('selected_date'); ?>">
	<?php endif; ?>
	
		
		
	<?php if($this->input->get('selected_date') !== FALSE): ?>
			
		
		
		<?php if($hours['min'] == 2 || $hours['max'] == -1): ?>
				<div class="alert alert-warning" role="alert">All rooms are closed!</div>
		<?php else: ?>
			<select name="set_time">
			<?php
				$tStart = mktime(0,0,0) + round((($hours['min'] * 24) * 60 * 60)); 
				
				//Avoid going past midnight
				if($hours['max'] > 1){ $hours['max'] = 1; }
				
				$tEnd = mktime(0,0,0) + round((($hours['max'] * 24) * 60 * 60)) - 1800;

				$tNow = $tStart;

				while($tNow <= $tEnd){
				  echo '<option value="'.$tNow.'">'.date("g:iA",$tNow).'</option>';
				  $tNow += 60 * 30; //30 MINUTES (60 seconds * 30)
				}
				
		
			?>
			</select>
			<input type="submit" value="Check Availability" />
		<?php endif; ?>
		
		
		
	<?php endif; ?>
	</form>

	
	<div class="back_img" style="margin-top: 5em">
		<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
	</div>	
	

	<script src="<?php echo base_url(); ?>assets/datepicker/external/jquery-ui/jquery-ui.min.js"></script>
	
	<script>
	$(function() {
		$( "#cal_container" ).datepicker({
			inline: true,
			dateFormat: 'dd-mm-yy',
			onSelect: function(date){
				$('#date').val(date);
				$('#date_form').submit(); 
			}
		});
	});
	
	
	</script>	

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>