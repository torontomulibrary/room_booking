<html>
<head></head>
<body>

Hi <?php echo $name; ?>, <br><br>

This e-mail is a notice letting you know that your request for <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',strtotime($start)) . '-'. date('h:ia',strtotime($end)); ?> on <?php echo date('F d, Y',strtotime($start)); ?> has been declined. <strong>You will not have access to the room for your requested time slot</strong><br><br>

</body>
</html>