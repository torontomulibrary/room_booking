<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



	<ul data-role="listview" data-inset="true">

	<?php foreach($rooms as $time => $rooms): ?>
		<li data-role="list-divider"><?php echo date('g:iA', $time); ?>:</li>
		
		<?php foreach ($rooms as $room): ?>
		<li>
			<a href="<?php echo base_url(); ?>mobile/create_booking?slot=<?php echo $time; ?>&room_id=<?php echo $room->room_id; ?>"><?php echo $room->name; ?>(<strong><?php echo $room->seats ?> seats</strong>)<br />
			<span id="font_pos">Available 
				<?php 
					if(($room->next_booking - $time) < ($room->max_daily_hours * 60 * 60) && date('Y-m-d') === date('Y-m-d',$room->next_booking)){
					echo " until ". date('g:ia', $room->next_booking); 
					}
				
				?>
			</span>
			<span class="showArrow secondaryWArrow">&nbsp;</span></a>
		</li>
		<?php endforeach; ?>
	<?php endforeach; ?>
	

	
	<div class="back_img" style="margin-top: 5em">
		<a data-role="button" class="black button" href="<?php echo base_url(); ?>mobile"><span>Menu</span></a>
	</div>	
	
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>