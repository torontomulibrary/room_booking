<html>
<head></head>
<body>

Hi <?php echo $name; ?>, <br><br>

This e-mail is your receipt for booking <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',$start) . '-'. date('h:ia',$end); ?> on <?php echo date('F d, Y',$start); ?>.<br><br>

To view/cancel your booking, please click the following <a href="<?php echo base_url() . 'booking/edit_booking?booking_id='.$booking_id; ?>">link</a><br><br>

Please note, you must claim your room within the first 15 minutes of the booking, or your room may be taken by other eligible users. <br><br>

In order to access the room, please pick up keys from the DCC Operations Office located on the 8th floor, DCC815.<br><br>

Please have your one card ready and be prepared to sign the key loan agreement form with one of the DCC Specialists.<br><br>


<b>Be sure to lock the door when you exit the room.</b>

<br><br>Thank you,<br>
DCC-FCS Room Booking Administrative Team

</body>
</html>