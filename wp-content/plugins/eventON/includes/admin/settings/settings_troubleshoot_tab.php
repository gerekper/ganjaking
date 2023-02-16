<?php
	// EventON Settings tab - Troubleshoot/support
	// version: 0.4
?>
<div id="evcal_5" class="postbox evcal_admin_meta curve">	
	
	<div class="inside eventon_settings_page">
		<div class='evotrouble_left'>
			<div class='evotrouble_left_in' style='padding-right:10px; padding-top:20px'>
				<h3>Common Issues and Solutions</h3>
				<p>Are you experiencing issues with EventON? Please look through our common questions/issues below and the solutions to them before contacting us. You <b>do NOT</b> need to purchase support from codecanyon. If you purchase our software and have a valid license, we will support you :)</p>
				<div class="evotrouble_qas">
				<?php

					$qas = apply_filters('eventon_troubleshooter',array(
						'Frequently Asked Questions'=> array(
							'Why doesn’t my shortcode work?'=> 'One reason why your short code might not work is if there are commas in the short code. Your short code should look like this without any commas separating the variables. Also when you enter the shortcode make sure to switch back to “Text” mode in wordpress text editor to check that the shortcode is cleanly typed without any HTML tags inside.',
							'How can I change the fonts on the Calendar?'=> 'In the WordPress backend under Event Calendar Setting >Appearance you can find the below input field “Primary Calendar Font Family” where you can write the name of font that you would like to use.	NOTE: make sure this font is either supported via webfonts on @font-face in your website. Also if the font name is something like “Times New Roman” make sure to type that inside quotation marks.',
							'How do I change the time to 24 hour format instead or AM/PM?'=>'Go to Settings> General on the lower part on this page you should find “Date Format” and “Time Format” settings for your website. Simply making changes in here to reflect 24 hour time format will change the time on Event Calendar to 24 hour time format.',
							'Some data fields (like RSVP box, Event Tickets etc.) are not showing in the eventcard, why is this?'=>'Once you activate these new data field boxes make sure to go to <b>myeventon> settings> eventCard</b> and under Re-arrange the order of eventCard event data boxes – make sure the new box is showing up and placed correct. Then click Save Changes',
							'How do I show more fields on event top?'=>'Go to  <b>myEventon> Settings> EventTop</b>  and select other fields you want to show on eventtop.',
							'Add to calendar time is incorrect'=>'Go to  <b>Settings > General> Timezone</b> and make sure the timezone set is correct timezone for your location. Add to calendar ICS and google calendar will adjust time based on this timezone value set.',
							'How to get the tile view'=>'Inside the eventon shortcode add tiles variable so it would look like this <code>[add_eventon tiles="yes"]</code>',
							'How can I optimize the JS files>'=>'EventON does not offer JS file optimization, however you can use https://wordpress.org/plugins/autoptimize/ WP plugin to easily optimize.',

						),
						'Common Issues'=>array(
							'Why is google maps showing blank box?'=>'One common solution is: <br/>Go to <b>myEventon> Settings> Google Maps API</b> and click disable google maps API and select google maps javascript file only.
								<br/><br/> Another solution is to inspect your website on front end to see what issues you are seeing. <a href="http://www.myeventon.com/documentation/why-are-my-events-are-not-sliding-down-or-months-not-switching/" target="_blank">Follow these guidelines to perform front-end inspection</a>',
							'Calendar does not switch months or load same events'=>'This happen when there is a javascript error on your website. Solution is to inspect your website on front-end to see what issues you are seeing. <a href="http://www.myeventon.com/documentation/why-are-my-events-are-not-sliding-down-or-months-not-switching/" target="_blank">Follow these guidelines to perform front-end inspection</a>',
							'All my events are not showing in the calendar'=>'EventON should show all your events in the calendar. <a href="http://www.myeventon.com/documentation/all-the-events-are-not-showing-in-calendar/" target="_blank">See the common reasons why this happens and solutions to it</a>',
							'How to find if the issue is indeed coming from EventON?'=>'When you use multiple plugins and themes, it is possible eventON does not play nice with those due to something different they do than standard procedure. <a href="http://www.myeventon.com/documentation/how-to-find-if-an-issue-is-coming-from-eventon/" target="_blank">Follow these guidelines to see if it is EventON that is causing the error.</a>'
						)
					));

					foreach($qas as $section=>$questions){
						echo '<h4>'.$section.'</h4>';
						foreach($questions as $question=>$answer){
							echo '<h5>'.$question.'</h5><p style="display:none">'.$answer.'</p>';
						}
					}

				?>
				</div>
			</div><!-- .evotrouble_left_in-->
		</div>
		<div class='evotrouble_right' style='text-align:center'>
			
			<div class='evotrouble_documentation'>
				<h2 class='heading tac' style='text-align:center; padding-top:60px;'>EventON Documentation</h2>			
				<div class='eventon_searchbox'>
					<form role="search" action="https://docs.myeventon.com/" method="get" id="searchform">
						<input type="text" name="s" placeholder="Search Documentation"/>
						<input type="hidden" name="post_type" value="document" /> <!-- // hidden 'products' value -->
						<input type="submit" alt="Search" value="Search" />
					</form>
				</div>
				<p style=' margin-bottom:25px; text-align:center'><i>NOTE: Please feel free to type in your question and search our documentation library for related answeres</i></p>
			</div>
			<!-- video tutorials -->
				<a id='troubleshoot_videos' class='evo_admin_btn btn_prime'>Video Tutorials on using EventON</a>
				<div class='troubleshoot_videos' style='display:none'>
					<p>
						<iframe width="100%" height="" src="https://www.youtube.com/embed/ijgMkKrdZfc" frameborder="0" allowfullscreen></iframe>
					</p><p>
						<iframe width="100%" height="auto" src="https://www.youtube.com/embed/TTbBHZzlQew" frameborder="0" allowfullscreen></iframe>
					</p><p>
						<iframe width="100%" height="auto" src="https://www.youtube.com/embed/0dod0m3Flvg" frameborder="0" allowfullscreen></iframe>
					</p>
					<p>
						<a href="http://www.myeventon.com/support/video-tutorials/" class='evo_admin_btn btn_prime' target="_blank">Rest of Video Tutorials</a>
					</p>
				</div>

			<div class='evotrouble_bottom' style='padding-top:60px; text-align:center'>
				<div class='evo_support_box evo_troubleshoot'>
					<h2>Having issues with EventON? <br/><a style='margin-top:8px; display:inline-block' class='btn' href='http://docs.myeventon.com/documentations/check-eventon-working/' target='_blank'>Troubleshoot Guide to Eventon</a></h2>
					<p>Read our <a href='http://docs.myeventon.com/documentations/check-eventon-working/' target='_blank'>troubleshooting guide</a> and identify your issue and apply common solutions to solve the issues before contacting us.</p>
				</div>

				<div class='evo_support_box special' >
					<h2>Official Support Helpdesk<br/><a style='margin-top:8px; display:inline-block' class='btn' href='http://helpdesk.ashanjay.com' target='_blank'>EventON HelpDesk</a></h2>
					<p>This is our official support system. Please feel free to search for already asked questions and ask your support questions for our help. EventON <b>Purchase code</b> is required to access helpdesk.</p>
				</div>				

				<a class='evo_support_box twitter' href='http://www.twitter.com/myeventon' target='_blank'>
					<h3>Follow us on twitter @myeventon</h3>
					<p>You can get the latest updates, other news, tips and tricks for eventON via our twitter stream.</p>
				</a>
			</div>
		</div>
		<div class="clear"></div>

		

	</div>

</div>