
<?php ob_start();?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/my_bookings.css" type="text/css" media="screen" />

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>



<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->



<!--- Show warnings or notices --->
<?php if($this->session->flashdata('notice') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('notice'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('warning') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('warning'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('success') !== FALSE): ?><div class="alert alert-success" role="alert"><?php echo $this->session->flashdata('success'); ?></div><?php endif; ?>
<?php if($this->session->flashdata('danger') !== FALSE): ?><div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('danger'); ?></div><?php endif; ?>

<div id="top_content">
	
	<div id="top_left">
		
		
		<div id="app_links">
			<ul>
				<?php if($this->session->userdata('admin') || $this->session->userdata('super_admin')): ?>
				<li><a href="<?php echo base_url(); ?>admin">Administrator View</a></li>
				<?php endif; ?>
				<li><a  href="<?php echo base_url(); ?>booking/booking_main">Dashboard</a></li>
				<li><a href="<?php echo base_url(); ?>booking/">MAIN PAGE</a></li>
				<li><a class="selected" href="<?php echo base_url(); ?>booking/my_bookings/">MY BOOKINGS</a></li>
				<li><a href="<?php echo base_url(); ?>logout">LOG OUT</a></li>
			</ul>
		</div>
		
		
		
		
	</div>
	
	
	
	
	
</div>




<div style="clear:both"></div>


<div class="booking_container">

	<div class="role_title">Upcoming Bookings</div>
	<div class="table-wrapper">
		<?php if($upcoming->num_rows > 0): ?>
		<table class="booking_table" style="width: 100%; border-collapse: initial;" cellspacing="0">
			<thead></thead>
			<tbody>
				<?php $count = 0 ?>
				<?php foreach ($upcoming->result() as $upcoming): ?>
				<tr class="upcoming <?php if($count %2 === 0) echo 'odd_row'; else echo 'even_row';?>">
					<td><?php echo date('M d, Y', strtotime($upcoming->start)); ?></td>
					<td><?php echo date('g:iA', strtotime($upcoming->start)) . ' - '. date('g:iA', strtotime($upcoming->end)); ?></td>
					<td><?php echo $upcoming->name; ?></td>
					<td><a href="<?php echo base_url(); ?>booking/edit_booking?booking_id=<?php echo $upcoming->booking_id; ?>">Edit Booking</a></td>
				
				</tr>
				<?php $count += 1; ?>
				<?php endforeach; ?>
				
				
			</tbody>
		</table>
		<?php else: ?>
			You do not have any upcoming bookings
		<?php endif; ?>
	</div>
	
		
	<div class="role_title">Previous Bookings</div>
	<div class="table-wrapper">
		<?php if($previous->num_rows > 0): ?>
		<table class="booking_table" style="width: 100%; border-collapse: initial;" cellspacing="0">
			<thead></thead>
			<tbody>
				<?php $count = 0 ?>
				<?php foreach ($previous->result() as $previous): ?>
				
				<tr class="previous <?php if($count %2 === 0) echo 'odd_row'; else echo 'even_row';?>">
					<td><?php echo date('M d, Y', strtotime($previous->start)); ?></td>
					<td><?php echo date('g:iA', strtotime($previous->start)) . ' - '. date('g:iA', strtotime($previous->end)); ?></td>
					<td><?php echo $previous->name; ?></td>
					<td>&nbsp;</td>
				
				</tr>
				<?php $count += 1; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else: ?>
			You do not have any previous bookings
		<?php endif; ?>
	</div>
	

</div>	



<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>