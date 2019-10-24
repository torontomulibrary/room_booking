<html>
<head></head>
<body>

Hi <?php echo $name; ?>, <br><br>

This e-mail is your receipt for booking <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',$start) . '-'. date('h:ia',$end); ?> on <?php echo date('F d, Y',$start); ?>.<br><br>

To view/cancel your booking, please click the following <a href="<?php echo base_url() . 'booking/edit_booking?booking_id='.$booking_id; ?>">link</a><br><br>

Please note, you must claim your room within the first 15 minutes of the booking, or your room may be taken by other eligible users. <br><br>

In order to access the room, please pick up the room key from the Faculty of Community Services Dean's Office, located in SHE-697.<br><br>

Please have your one card ready, and check in at the front desk with the Administrative Assistant. Please note that you will need to leave your one card to sign out the room key.<br><br>


<b>Be sure to lock the door when you exit the room.</b>

<br><br>Thank you,<br>
DCC-FCS Room Booking Administrative Team

</body>
</html>