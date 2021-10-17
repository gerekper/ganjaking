<?php
/**
 * @version 0.1
 */

echo "
<p>.ICS file is a file containing event data from popular organizers/calendars like iCal, outlook etc. All events are saved as <b>published</b> events after processed and imported into this website.</p>

		<h4>Acceptable ICS file fields</h4>
		<div class='evoics_guide_popup' style='padding-left:20px'>
			<p><b>UID</b> - Unique ID for the event</p>
			<p><b>DTSTART</b> - Timestamp of event start date and time</p>
			<p><b>DTEND</b> - Timestamp of event end date and time</p>
			<p><b>SUMMARY</b> - Event Name</p>
			<p><b>DESCRIPTION</b> - Event description</p>
			<p><b>LOCATION</b> - Event location name or address</p>
		</div>

<p><b>Requirements:</b> EventON version 2.4.4 or higher</p>
";
?>