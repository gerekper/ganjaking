<?php
/**
 * @version 2.0
 */

echo "
<div class='evopad20'>
<p>.ICS file is a file containing event data from popular organizers/calendars like iCal, outlook etc.</p>

		<h4>Acceptable ICS file fields</h4>
		<div class='evoics_guide_popup' style='padding-left:20px'>
			<p><b>UID</b> - Unique ID for the event</p>
			<p><b>DTSTART</b> - Timestamp of event start date and time</p>
			<p><b>DTEND</b> - Timestamp of event end date and time</p>
			<p><b>SUMMARY</b> - Event Name</p>
			<p><b>DESCRIPTION</b> - Event description</p>
			<p><b>LOCATION</b> - Event location name or address</p>
		</div>
<p>General Guide: Choose Import method from settings. Then you can upload a ICS file or import from a URL. You can set via settings, which timezone to use. After events are processed from ICS file, via more options button (...) you can edit event times to fix any incorrections.</p>
<p><b>Requirements:</b> EventON version ". EVOICS()->eventon_version." or higher</p>
<p>Guide version 2.0</p>
</div>
";
?>