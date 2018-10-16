BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//<?php echo SITE_TITLE; ?>//NONSGML v1.0//EN
BEGIN:VEVENT
UID:<?php echo $this->session->userdata('username').EMAIL_SUFFIX. ":". $booking_id. "\n"; ?>
DTSTAMP:<?php echo date('Ymd\THis\ZO', $start). "\n"; ?>
DTSTART:<?php echo date('Ymd\THis\ZO', $start). "\n"; ?>
DTEND:<?php echo date('Ymd\THis\ZO', $end). "\n"; ?>
SUMMARY: Library Room Booking
LOCATION: <?php echo $room. "\n"; ?>
END:VEVENT
END:VCALENDAR