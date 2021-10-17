<?php
/**
 * RSVP Language
 * @version 2.6.3
 */

class EVORS_Lang{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
	}

	function admin_init(){
		add_filter('eventon_settings_lang_tab_content', array($this, 'lang_additions'), 10, 1);
	}

	// language settings additinos
	function lang_additions($_existen){

		
		$A = array(
			array('type'=>'togheader','name'=>'ADDON: RSVP Events'),
			array('label'=>'Field Title','name'=>'evoRSL_001','legend'=>'','placeholder'=>'RSVP Now'),
			array('label'=>'Field Subtitle','name'=>'evoRSL_002','placeholder'=>'Make sure to RSVP to this amazing event!'),
			array('label'=>'Guests List','name'=>'evoRSL_002a'),
			array('label'=>'List of guests not attending to this event','var'=>1),
			array('label'=>'Not Attending','var'=>1),
			array('label'=>'Attending','name'=>'evoRSL_002a1'),
			array('label'=>'Event is happening for certain','var'=>'1'),
			array('label'=>'Needed for the event to happen','var'=>'1'),
			array('label'=>'Can not make it to this event?','name'=>'evoRSL_002a2'),
			array('label'=>'Spots remaining','name'=>'evoRSL_002b','placeholder'=>'Spots remaining'),
			array('label'=>'Spaces Still available','name'=>'evoRSL_002bb'),
			array('label'=>'No more spots left!','name'=>'evoRSL_002c','placeholder'=>'No more spots left!'),
			array('label'=>'RSVPing is closed at this time.','name'=>'evoRSL_002d','placeholder'=>'RSVPing is closed at this time.'),
			array('label'=>'Sorry to hear you can not make it to the event.','var'=>'1'),
			array('label'=>'We look forward to seeing you at the event!','var'=>'1'),
			array('label'=>'Please let us know if you can make it to the event.','var'=>'1'),
			array('label'=>'You have already RSVP-ed','var'=>'1'),
			array('label'=>'RSVP Status: Check-in','name'=>'evoRSL_003x',),
			array('label'=>'RSVP Status: Checked','name'=>'evoRSL_003y',),
			array('label'=>'You must login to RSVP for this event','var'=>'1',),
			array('label'=>'You do not have permission to RSVP to this event!','var'=>'1',),
			array('label'=>'Login Now','var'=>'1',),
			array('label'=>'Additional Information','var'=>'1'),
			array('label'=>'No more spaces left','var'=>'1'),
			array('label'=>'Event Over','var'=>'1'),
			array('label'=>'rsvps','var'=>'1'),
			array('label'=>'Filled','var'=>'1'),
			array('label'=>'Open','var'=>'1'),
			array('label'=>'Additional guest names','var'=>'1'),
			array('label'=>'You have RSVPed to this event','var'=>'1'),
			array('label'=>'Please RSVP to join this event','var'=>'1'),
			array('label'=>'RSVP for this event is closed now','var'=>'1'),
			
			array('label'=>'Form RSVP Selection','type'=>'subheader'),
				array('label'=>'RSVP options: Yes','name'=>'evoRSL_003','legend'=>'',),
				array('label'=>'RSVP options: Maybe','name'=>'evoRSL_004','legend'=>'',),
				array('label'=>'RSVP options: No','name'=>'evoRSL_005','legend'=>'',),
				array('label'=>'RSVP options: Change my RSVP','name'=>'evoRSL_005a','legend'=>'',),
			array('type'=>'togend'),
			
			array('label'=>'RSVP Form Text','type'=>'subheader'),
				array('label'=>'Form label: RSVP ID #','name'=>'evoRSL_007a',),
				array('label'=>'Form label: First Name','name'=>'evoRSL_007',),
				array('label'=>'Form label: Last Name','name'=>'evoRSL_008',),
				array('label'=>'Form label: Email Address','name'=>'evoRSL_009',),
				array('label'=>'Form label: Phone Number','name'=>'evoRSL_009a',),
				array('label'=>'Form label: How many people in your party?','name'=>'evoRSL_010',),
				array('label'=>'Form label: List Full Name of Other Guests','name'=>'evoRSL_010b',),
				array('label'=>'Form label: Additional Notes','name'=>'evoRSL_010a',),
				array('label'=>'Form label: Verify you are a human','name'=>'evoRSL_011a',),
				array('label'=>'Form label: Receive updates about event','name'=>'evoRSL_011',),
				array('label'=>'Form label: Terms & Conditions','name'=>'evoRSL_tnc',),
				array('label'=>'Form Text: We have to look up your RSVP in order to change it','name'=>'evoRSL_x1',),
				array('label'=>'Fill in the form below to RSVP!','var'=>'1'),
				array('label'=>'You have already RSVPed for this event!','var'=>'1')
		);
				
		// form additional fields
		$optRS = EVORS()->frontend->optRS;
		$B = array();	
		for($x=1; $x <= EVORS()->frontend->addFields; $x++){
			if(evo_settings_val('evors_addf'.$x, $optRS) && !empty($optRS['evors_addf'.$x.'_1'])){
				$F = html_entity_decode(stripslashes($optRS['evors_addf'.$x.'_1']));

				if(!empty($optRS['evors_addf'.$x.'_ph']) ){
					$B[] = array('label'=> $optRS['evors_addf'.$x.'_ph'], 'var'=>1 );
				}
				$B[] = array('label'=>$F,'var'=>1);
			}
		}

		$A = array_merge($A, $B);

		$A = array_merge($A, array(
			array('type'=>'togend'),

			array('label'=>'EventTop','type'=>'subheader'),
				array('label'=>'Be the first to RSVP','var'=>'1'),
				array('label'=>'Guest is attending','var'=>'1'),
				array('label'=>'Guests are attending','var'=>'1'),
				array('label'=>'Guest is not attending','var'=>'1'),
				array('label'=>'Guests are not attending','var'=>'1'),
			array('type'=>'togend'),
			
			array('label'=>'Form Text: **Make sure to include text part [ ] in your custom text','type'=>'subheader'),
				array('label'=>'Form Text: RSVP to','name'=>'evoRSL_x2','placeholder'=>'RSVP to [event-name]'),
				array('label'=>'Form Text: Change RSVP to','name'=>'evoRSL_x2a','placeholder'=>'Change RSVP to [event-name]'),
				array('label'=>'Form Text: Find my RSVP for',
					'name'=>'evoRSL_x3','placeholder'=>'Find my RSVP for [event-name]'),
				array('label'=>'Form Text: You have reserved',
					'name'=>'evoRSL_x6','placeholder'=>'You have reserved [spaces] space(s) for [event-name]'),

				array('label'=>'Form Text: Thank you',
					'name'=>'evoRSL_x7','placeholder'=>'Thank you'),
				array('label'=>'Form Text: We have email-ed you a confirmation to ',
					'name'=>'evoRSL_x8','placeholder'=>'We have email-ed you a confirmation to [email]'),
				array('label'=>'Sorry to hear you are not coming',
					'var'=>1,'placeholder'=>'Sorry to hear you are not coming'),
				
				array('label'=>'Form Text: Successfully RSVP-ed for','name'=>'evoRSL_x5','placeholder'=>'Successfully RSVP-ed for [event-name]'),
				array('label'=>'Form Text: Successfully updated RSVP for','name'=>'evoRSL_x4','placeholder'=>'Successfully updated RSVP for [event-name]'),
			array('type'=>'togend'),

			array('label'=>'Form Buttons','type'=>'subheader'),
				array('label'=>'Form Button: Submit','name'=>'evoRSL_012',),
				array('label'=>'Form Button: Change my RSVP','name'=>'evoRSL_012x',),
				array('label'=>'Form Button: Find my RSVP','name'=>'evoRSL_012y',),
			array('type'=>'togend'),

			array('type'=>'subheader','label'=>'EMAIL Body'),
				array('label'=>'RSVP Status','name'=>'evoRSLX_001','legend'=>'','placeholder'=>''),
				array('label'=>'Primary Contact on RSVP','name'=>'evoRSLX_002','legend'=>'','placeholder'=>''),
				array('label'=>'Spaces','name'=>'evoRSLX_003','legend'=>'','placeholder'=>''),
				array('label'=>'Receive Updates','name'=>'evoRSLX_003a'),
				array('label'=>'Location','name'=>'evoRSLX_003x',),
				array('label'=>'Thank you for RSVPing to our event','name'=>'evoRSLX_004','legend'=>'','placeholder'=>''),
				array('label'=>'We look forward to seeing you!','name'=>'evoRSLX_005','legend'=>'','placeholder'=>'We look forward to seeing you!'),
				array('label'=>'Contact us for questions and concerns.',
					'name'=>'evoRSLX_006','legend'=>'','placeholder'=>''),
				array('label'=>'New RSVP for event.',
					'name'=>'evoRSLX_007','legend'=>'','placeholder'=>''),
				array('label'=>'Event Time','name'=>'evoRSLX_008','legend'=>''),
				array('label'=>'Event Name','name'=>'evoRSLX_008a'),
				array('label'=>'Event Details','name'=>'evoRSLX_008b'),
				array('label'=>'Capacity','name'=>'evoRSLX_email_01'),
				array('label'=>'Remaining','name'=>'evoRSLX_email_02'),
				array('label'=>'Event RSVP Stats','name'=>'evoRSLX_email_03'),
				array('label'=>'View RSVP','var'=>'1'),
				array('label'=>'Edit Event','var'=>'1'),
				array('label'=>'Confirmation email subheader.','name'=>'evoRSLX_009','legend'=>'','placeholder'=>'You have RSVP-ed for'),		
				array('label'=>'RSVP Update Notice','var'=>1),	
				array('label'=>'View Event','var'=>1),	
			array('type'=>'togend'),
			array('label'=>'Other EMAIL Texts','type'=>'subheader'),
				array('label'=>'You have successfully changed the RSVP status','var'=>1),
				array('label'=>'RSVP Notification','var'=>1),
				array('label'=>'New RSVP','var'=>1),
				array('label'=>'Full Name','var'=>1),
				array('label'=>'You have received a new RSVP','var'=>1),
				array('label'=>'Virtual Event Access','var'=>1),
				array('label'=>'Link','var'=>1),
				array('label'=>'Pass','var'=>1),
				array('label'=>'Your temporary password','var'=>1),
			array('type'=>'togend'),

			array('label'=>'Form Messages','type'=>'subheader'),
				array('label'=>'Messages: Error 1','name'=>'evoRSL_013','placeholder'=>'Required fields missing'),
				array('label'=>'Messages: Error 2','name'=>'evoRSL_014','placeholder'=>'Invalid email address'),
				array('label'=>'Messages: Error 3','name'=>'evoRSL_015','placeholder'=>'Please select RSVP option'),
				array('label'=>'Messages: Error 4','name'=>'evoRSL_016','placeholder'=>'Could not update RSVP, please contact us.'),
				array('label'=>'Messages: Error 5','name'=>'evoRSL_017','placeholder'=>'Could not find RSVP, please try again.'),
				array('label'=>'Messages: Error 6','name'=>'evoRSL_017x','placeholder'=>'Invalid Captcha code.'),
				array('label'=>'Messages: Error 7','name'=>'evoRSL_017y','placeholder'=>'Could not create a RSVP please try later.'),
				array('label'=>'Messages: Error 8','name'=>'evoRSL_017z1','placeholder'=>'You can only RSVP once for this event.'),
				array('label'=>'Messages: Error 9','name'=>'evoRSL_017z2','placeholder'=>'Your party size exceed available space.'),
				array('label'=>'Messages: Error 9','name'=>'evoRSL_017z3','placeholder'=>'Your party size exceed allowed space per RSVP'),
				array('label'=>'Messages: Error 10','name'=>'evoRSL_017z4','placeholder'=>'There are no spaces available to RSVP'),
				array('label'=>'There are not enough space!','var'=>1),

				array('label'=>'Messages: Success 1','name'=>'evoRSL_018','placeholder'=>'Thank you for submitting your rsvp'),
				array('label'=>'Messages: Success 2','name'=>'evoRSL_019','placeholder'=>'Sorry to hear you are not going to make it to our event'),
				array('label'=>'Messages: Success 3','name'=>'evoRSL_020','placeholder'=>'Thank you for updating your rsvp'),
				array('label'=>'Messages: Success 4','name'=>'evoRSL_021','placeholder'=>'Great! we found your RSVP!'),
			array('type'=>'togend'),
			array('label'=>'Event Manager','type'=>'subheader'),
				array('label'=>'RSVP ID','var'=>1),
				array('label'=>'Events I have RSVPed to','name'=>'evoRSL_EM1'),
				array('label'=>'Login required to manage events you have RSVP-ed.','name'=>'evoRSL_EM2'),
				array('label'=>'Login Now','name'=>'evoRSL_EM3'),
				array('label'=>'Hello','name'=>'evoRSL_EM3a'),
				array('label'=>'Past Events','var'=>1),
				array('label'=>'Registered Attendees','var'=>1),
				array('label'=>'Type in guest email address or RSVP id','var'=>1),
				array('label'=>'Register a new guest on the spot','var'=>1),
				array('label'=>'Refresh data','var'=>1),
				array('label'=>'From your RSVP manager dashboard you can view events you have RSVP-ed to and update options.','name'=>'evoRSL_EM4'),
				array('label'=>'My Events','name'=>'evoRSL_EM5'),
				array('label'=>'You have not RSVP-ed to any events yet','name'=>'evoRSL_EM52'),
				array('label'=>'Time','var'=>'1'),
				array('label'=>'Update','var'=>'1'),
				array('label'=>'No Signed-in guests yet','var'=>'1'),
			array('type'=>'togend'),
		));

		$new_ar = apply_filters('evors_lang_ar', $A);

		$endAr = array(array('type'=>'togend'));

		return (is_array($_existen))? array_merge($_existen, $new_ar, $endAr): $_existen;
	}
}

new EVORS_Lang();