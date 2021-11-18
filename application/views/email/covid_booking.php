<html>
<head></head>
<body>

Hi <?php echo $name; ?>, <br><br>

This e-mail is your receipt for booking <?php echo $room['room_data']->row()->name; ?> between <?php echo date('h:ia',$start) . '-'. date('h:ia',$end); ?> on <?php echo date('F d, Y',$start); ?>.<br><br>

If you are feeling unwell, please stay home. To view/cancel your booking, please click the following <a href="<?php echo base_url() . 'booking/edit_booking?booking_id='.$booking_id; ?>">link</a><br><br>


To access campus, including the Library, you are required to: <br>
<ul>
	<li>Submit <a href="https://www.ryerson.ca/covid-19/health-safety-on-campus/vaccinations/">proof of vaccination status</a> or apply for an exemption by September 20th.</li>
	<li>Complete a <a href="https://www.ryerson.ca/covid-19/health-screening-reporting-cases/health-screening/">health screening</a> prior to your arrival on campus each day.</li>
	<li>Wear a mask when indoors that covers your mouth, nose and chin in accordance with our <a href="https://www.ryerson.ca/policies/policy-list/face-mask-policy/">mask policy</a>.</li>
	<li>Observe two meters physical distance in the Library, common spaces and corridors.</li>
</ul>
<br>

During the fall 2021 semester, campus buildings will be locked and only accessible by OneCard.  Upon arrival, please have your <a href="https://www.ryerson.ca/university-business-services/onecard/">OneCard</a> with you to confirm your booking. <br><br>
 
<b>Library Building Access:</b><br><br>

The University Library is located at 350 Victoria Street. Enter through the Library Building entrance at the Lower Ground level.  <br><br>

 

When you arrive, please follow these steps:<br>
<ul>
    <li style="margin-bottom:15px">STEP 1: Tap your OneCard against the card reader to gain entry.</li>
<li style="margin-bottom:15px">STEP 2: Proceed directly to the 2nd floor Library entrance</li>
   <li style="margin-bottom:15px">STEP 3: <u>Check in</u> with staff and confirm your booking time. You will be required to show your OneCard, your badge/proof of completion of the <a href="https://www.ryerson.ca/covid-19/health-screening-reporting-cases/health-screening/student-health-screening/">Student Health Screening</a> and your <a href="https://www.ryerson.ca/covid-19/updates/2021/09/process-to-submit-your-proof-of-vaccination-now-live/">Vaccination Status</a> within the RyersonSafe app. </li>
    <li style="margin-bottom:15px">STEP 4: A staff member will then direct you to your designated study seat. While in the Library, please adhere to health and safety protocols outlined below.</li>
    <li style="margin-bottom:15px">STEP 5: To exit, please follow directional arrows, proceed to the marked exit, return your booking ticket, and <u>check out</u> with Library staff.</li>
</ul>
       <br>

Please note: <br>
<ul>
   <li style="margin-bottom:15px">Seating capacity has been reduced to encourage students to practice physical distancing while moving throughout the Library.</li>


    <li style="margin-bottom:15px">You will have a designated seat for the duration of your booking. For health and safety reasons please do not change your seat, move locations or furniture.</li>


    <li style="margin-bottom:15px">Disinfectant wipes and hand sanitizer is available for your use and safety.</li>


    <li style="margin-bottom:15px">Printing is available soon.</li>


    <li style="margin-bottom:15px">Photocopying is not available.</li>


    <li style="margin-bottom:15px">There is limited access to collections during our renovations. If you require print items that are not available electronically, please see Contactless Print Pick Up.</li>
</ul>

The Library continues to provide academic supports, services and resources - from research appointments, to live chat reference and a host of workshops and specialized digital learning resources. For the latest information please visit <a href="https://library.ryerson.ca">https://library.ryerson.ca</a> <br><br>

If you have questions or need additional assistance, please contact access@ryerson.ca.
</body>
</html>