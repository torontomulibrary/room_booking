BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Ryerson University Library//NONSGML v1.0//EN
BEGIN:VEVENT
UID:<?php echo $this->session->userdata('username').EMAIL_SUFFIX; ?> 
DTSTAMP:<?php echo date('Ymd\THis\ZO', $start); ?> 
DTSTART:<?php echo date('Ymd\THis\ZO', $start); ?> 
DTEND:<?php echo date('Ymd\THis\ZO', $end); ?> 
SUMMARY: Library Room Booking 
LOCATION: TST100 
END:VEVENT
END:VCALENDAR