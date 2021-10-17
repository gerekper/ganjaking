<?php
/**
 * @version 0.3
 */

echo "
<p>CSV is a tabular format that consists of rows and columns. Each row in
a CSV file represents an event post; each column identifies a piece of information
that comprises an event data. It is <b>VERY Important</b> to built your CSV file correctly to successfully import events.</p>

		<h4>Acceptable CSV file fields</h4>
		<div class='evocsv_guide_popup' style='padding-left:20px'>
			<p><b>publish_status</b> - Publish status of the event: <i class='highl'>publish | draft</i></p>
			<p><b>event_id (int)</b> - Event post ID, if passed, event with this id will be updated with meta data</p>
			<p><b>featured</b> - Feature an event or no eg <b class='highl'>yes | no</b></p>
			<p><b>color</b> - Hex color for the event without # sign</p>
			<p><b>event_name</b> - Name of the event</p>
			<p><b>event_description</b> - Event main description</p>
			<p><b>event_start_date & event_end_date</b> - Start/End date in format <i class='highl'>mm/dd/YYYY</i></p>
			<p><b>event_start_time & event_end_time</b> - Start/End time in format <i class='highl'>h:mm:AM/PM</i></p>

			<p><i>Location Fields:</i> 
				<span class='inden'><b>location_name</b> => Event location Name</span>
				<span class='inden'><b>event_location</b> => Event location complete address</span>
				<span class='inden'><b>evcal_location_link</b> => Location Link (http://)</span>
				<span class='inden'><b>evo_location_id</b> => Location taxonomy term ID</span></p>

			<p><i>Organizer Fields:</i> 
				<span class='inden'><b>event_organizer</b> => Organizer Name</span>
				<span class='inden'><b>evcal_org_contact</b> => Organizer contact information eg. email</span>
				<span class='inden'><b>evcal_org_address</b> => Organizer location address</span>
				<span class='inden'><b>evcal_org_exlink</b> => Organizer external link (http://)</span>
				<span class='inden'><b>evo_organizer_id</b> => Organizer taxonomy term ID</span></p>

			<p><b>event_gmap</b> - Generate google maps from address. eg <b class='highl'>yes | no</b></p>
			<p><b>evcal_subtitle</b> - Event Subtitle</p>
			<p><b>all_day</b> - All day event eg. <b class='highl'>yes | no</b></p>
			<p><b>hide end time</b> -Hide end time for event eg. <b class='highl'>yes | no</b></p>
			<p><b>event_type categories</b> - Event type category term IDs seperated by commas eg. 4,19. You can add upto 5 event type categories in seperate columns with column headers in format <b class='highl'>event_type, event_type_2 etc.</b></p>
			
			<p><i>Custom Meta Fields:</i> 
				<span class='inden'><b>cmd_{x} </b> => Custom meta data values. You can add upto 10 custom meta field values with sepeate columns with column headers in <b class='highl'>format cmd_{x}</b></span>
				<span class='inden'><b>cmd_{x}L</b> => can be used for custom field button url data</span></p>

			<p><b>image_url </b> - Complete http:// url to an image from external source</p>
			<p><b>image_id </b> - (integer) existing attachment ID from wordpress site you are importing events to</p>
			<p><b>evcal_lmlink </b> - Learn more link (http)</p>
			<p><b>evcal_lmlink_target </b> - Learn more link open in new window <b class='highl'>yes | no</b></p>

			<p><i>User Interaction Fields:</i> 
				<span class='inden'><b>_evcal_exlink_option</b> => UX_val from 1-4 (integer)</span> 
				<span class='inden'><b>evcal_exlink</b> => UX_VAL link to event URL (http://)</span> 
				<span class='inden'><b>_evcal_exlink_target</b> => UX_val link open in new window <b class='highl'>yes | no</b></span></p>

			<p><i>ActionUser Compatible Fields:<i> <b>evoau_assignu</b> => User IDs seperated by comma to assign users to events</p>
		</div>
		<br/>
		<p><b>NOTE</b> <i>Please check the <b class='highl'>\"sample.csv\"</b> file that came with the addon for more instruction guidance. If you are importing a large CSV file, make sure to set <code>max_input_vars=10000</code> and <code>max_execution_time=0</code> in php.ini file in your webhost.</i></p>

		<p>You can also add additional field support using <a href='http://www.myeventon.com/documentation/import-additional-fields-using-hooks/' target='_blank'>this guide</a></p>

<p><b>Requirements:</b> EventON version 2.6 or higher</p>
";
?>