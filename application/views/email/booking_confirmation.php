Hi <?php echo $name; ?>, 

This e-mail is your receipt for booking <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',$start) . '-'. date('h:ia',$end); ?> on <?php echo date('F d, Y',$start); ?>.

Please retain this e-mail as proof of your booking and be prepared to show this e-mail if requested.

To view/cancel your booking, please click the following <?php echo 'code to make link'; ?>