<html>
<head></head>
<body>

Hi <?php echo $name; ?>, <br><br>

This e-mail is your receipt for booking <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',$start) . '-'. date('h:ia',$end); ?> on <?php echo date('F d, Y',$start); ?>.<br><br>

To view/cancel your booking, please click the following <a href="<?php echo base_url() . 'booking/edit_booking?booking_id='.$booking_id; ?>">link</a><br><br>

Please note, you must claim your technology resource within the first 15 minutes of the booking, or it may be taken by other eligible users.
</body>
</html>