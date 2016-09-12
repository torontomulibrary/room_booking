<html>
<head></head>
<body>

Hi <?php echo $name; ?>, <br><br>

This e-mail is a to confirm that you have requestion <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',$start) . '-'. date('h:ia',$end); ?> on <?php echo date('F d, Y',$start); ?>, and it is currently awaiting moderation.<br><br>

If you have any questions or need to add additional information, please contact <a href="mailto:performance.rooms@ryerson.ca">performance.rooms@ryerson.ca</a>

</body>
</html>