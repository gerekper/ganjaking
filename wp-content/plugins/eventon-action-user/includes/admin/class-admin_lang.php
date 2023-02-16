<?php
/* Lang */

class EVOAU_Lang{
	public function __construct(){
		add_filter('eventon_settings_lang_tab_content', array($this,'langs'), 10, 1);
	}	


	function langs($_existen){
		$evcal_opt = get_option('evcal_options_evcal_1');
		$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Action User'),
				array('label'=>'Event Name','name'=>'evoAUL_evn','legend'=>''),
				array('label'=>'Event Sub Title','name'=>'evoAUL_est','legend'=>''),
				array('label'=>'Event Start Date/Time','name'=>'evoAUL_esdt','legend'=>''),
				array('label'=>'Event End Date/Time','name'=>'evoAUL_eedt','legend'=>''),
				array('label'=>'Event Details','name'=>'evcal_evcard_details_au','legend'=>''),
				array('label'=>'Event Color','name'=>'evoAUL_ec','legend'=>''),

				array('label'=>'Event Location Fields','name'=>'evoAU_pseld'),
				array('label'=>'Select Saved Location','name'=>'evoAUL_ssl'),
				array('label'=>'Select from List','name'=>'evoAUL_sfl'),
				array('label'=>'Hide Create New Form','var'=>1),
				array('label'=>'Event Location Name','name'=>'evoAUL_lca'),
				array('label'=>'Event Location Address','name'=>'evoAUL_ln'),
				array('label'=>'Event Location Coordinates (lat,lon Seperated by comma)','name'=>'evoAUL_lcor'),
				array('label'=>'Event Location Link','name'=>'evoAUL_llink'),

				array('label'=>'Select Saved Organizer','name'=>'evoAUL_sso'),
				array('label'=>'Event Organizer Fields','name'=>'evoAU_pseod'),
				array('label'=>'Event Organizer','name'=>'evoAUL_eo'),
				array('label'=>'Event Organizer Contact Information','name'=>'evoAUL_eoc'),
				array('label'=>'Event Organizer Link','name'=>'evoAUL_eol'),
				array('label'=>'Event Organizer Address','name'=>'evoAUL_eoa'),

				array('label'=>'Learn More Link','name'=>'evoAUL_lml'),
				array('label'=>'Create New','name'=>'evoAUL_cn'),
				array('label'=>'Edit','name'=>'evoAUL_edit'),
			);

		// event taxnomies upto 5 all active ones only
		$ett_verify = evo_get_ett_count($evcal_opt );
		$_tax_names_array = evo_get_ettNames($evcal_opt);
		$new_ar_1 = array();
		for($x=1; $x< ($ett_verify+1); $x++){
			$ab = ($x==1)? '':'_'.$x;
			$__tax_name = $_tax_names_array[$x];
			$new_ar_1[]= array('label'=>'Select the '.$__tax_name.'','name'=>'evoAUL_stet'.$x,'legend'=>'');
		}

		$new_ar_2 = array(
			array('label'=>'Edit Submitted Event','name'=>'evoAUL_ese','legend'=>''),
			array('label'=>'Event Post Status','var'=>1),
			array('label'=>'Event Image','name'=>'evoAUL_ei','legend'=>''),				
			array('label'=>'Remove Image','var'=>'1'),				
			array('label'=>'All Day Event','name'=>'evoAUL_001','legend'=>''),
			array('label'=>'No End time','name'=>'evoAUL_002','legend'=>''),
			array('label'=>'This is a repeating event','name'=>'evoAUL_ere1'),
			array('label'=>'Daily','name'=>'evoAUL_ere2'),
			array('label'=>'Weekly','name'=>'evoAUL_ere3'),
			array('label'=>'Monthly','name'=>'evoAUL_ere4'),
			array('label'=>'Yearly','name'=>'evoAUL_ere4y'),
			array('label'=>'Custom','var'=>1),
			array('label'=>'Days','var'=>1),
			array('label'=>'Weeks','var'=>1),
			array('label'=>'Months','var'=>1),
			array('label'=>'Years','var'=>1),
			array('label'=>'Event Repeat Type','name'=>'evoAUL_ere5'),
			array('label'=>'Gap Between Repeats','name'=>'evoAUL_ere6'),
			array('label'=>'Number of Repeats','name'=>'evoAUL_ere7'),
			array('label'=>'Custom Repeat Times','var'=>1),
			array('label'=>'Add New Repeat Interval','var'=>1),
			array('label'=>'All fields are required','var'=>1),
			array('label'=>'Start Date/Time','var'=>1),
			array('label'=>'End Date/Time','var'=>1),
			array('label'=>'Event Date & Time','var'=>1),
			array('label'=>'Edit','var'=>1),
			array('label'=>'other repeat intervals exists','var'=>1),

			// virtual 
			array('label'=>'This is a virtual (online) event','var'=>1),
			array('label'=>'Virtual Event URL','var'=>1),
			array('label'=>'Event access Pass Information','var'=>1),
			array('label'=>'Optional Embed Event Video Code','var'=>1),
			array('label'=>'When to show the above virtual event information on event card','var'=>1),
			array('label'=>'This will set when to show the virtual event link and access information on the event card','var'=>1),
			array('label'=>'Always','var'=>1),
			array('label'=>'3 Hours before the event start','var'=>1),
			array('label'=>'2 Hours before the event start','var'=>1),
			array('label'=>'1 Hour before the event start','var'=>1),
			array('label'=>'30 minutes before the event start','var'=>1),
			array('label'=>'Hide above access information when the event is live','var'=>1),
			array('label'=>'Disable redirecting and hiding virtual event link','var'=>1),
			array('label'=>'Enabling this will show virtual event link without hiding it behind a redirect url','var'=>1),
			array('label'=>'Content to show after event has taken place','var'=>1),
			array('label'=>'Optional After Event Information','var'=>1),
			array('label'=>'When to show the above content on eventcard','var'=>1),
			array('label'=>'After event end time is passed','var'=>1),
			array('label'=>'1 Hour after the event has ended','var'=>1),
			array('label'=>'1 Day after the event has ended','var'=>1),

			// health
			array('label'=>'Add Health Guidelines for this Event','var'=>1),
			array('label'=>'Face masks required','var'=>1),
			array('label'=>'Temperate will be checked at entrance','var'=>1),
			array('label'=>'Physical distance maintained event','var'=>1),
			array('label'=>'Event area sanitized before event','var'=>1),
			array('label'=>'Event is held outside','var'=>1),
			array('label'=>'Other additional health guidelines','var'=>1),
			
			array('label'=>'Your Full Name','name'=>'evoAUL_fn','legend'=>''),
			array('label'=>'Your Email Address','name'=>'evoAUL_ea','legend'=>''),
			array('label'=>'Form Human Submission Validation','name'=>'evoAUL_cap','legend'=>''),
			array('label'=>'Select an Image','name'=>'evoAUL_img002','legend'=>''),
			array('label'=>'Image Chosen','name'=>'evoAUL_img001','legend'=>''),
			array('label'=>'Additional Private Notes','name'=>'evoAU_add','legend'=>''),
			array('label'=>'Open in new window','name'=>'evoAUL_lm1'),			
			array('label'=>'or create New (type other categories seperated by commas)','name'=>'evoAUL_ocn'),
			array('label'=>'(Text)','var'=>'1'),
			array('label'=>'(Link)','var'=>'1'),		
			array('label'=>'** Special Event Edit Fields (exclude, feature and cancel event)','var'=>1),
			array('label'=>'Event Access Password','var'=>1),
			array('label'=>'Exclude this event from calendar','var'=>1),
			array('label'=>'Feature this event','var'=>1),
			array('label'=>'Cancel this event','var'=>1),		
			array('label'=>'Event Status','var'=>1),		
			array('label'=>'Submit Event','name'=>'evoAUL_se','legend'=>''),
			array('label'=>'Submit another event','var'=>'1'),
			array('label'=>'Update Event','var'=>'1'),
			array('label'=>'Login','name'=>'evoAUL_00l1'),
			array('label'=>'Register','name'=>'evoAUL_00l2'),

			array('label'=>'Date Picker','type'=>'subheader'),
				array('label'=>'Prev','var'=>1),				
				array('label'=>'Next','var'=>1),				
			array('type'=>'togend'),

			array('label'=>'User Interaction values','type'=>'subheader'),
				array('label'=>'User Interaction','var'=>1),
				array('label'=>'Slide Down EventCard','name'=>'evoAUL_ux1','legend'=>''),
				array('label'=>'External Link','name'=>'evoAUL_ux2','legend'=>''),
				array('label'=>'Lightbox popup window','name'=>'evoAUL_ux3'),
				array('label'=>'Type the External Url','name'=>'evoAUL_ux4'),
				array('label'=>'Open as Single Event Page','name'=>'evoAUL_ux4a'),
			array('type'=>'togend'),

			array('label'=>'Form Notification Messages','type'=>'subheader'),
				array('label'=>'You do not have permission to submit events!','var'=>1),
				array('label'=>'You must login to submit events.','name'=>'evoAUL_ymlse','legend'=>''),
				array('label'=>'Required Fields Missing','name'=>'evoAUL_nof1','legend'=>''),
				array('label'=>'Invalid validation code please try again','name'=>'evoAUL_nof2','legend'=>''),
				array('label'=>'Thank you for submitting your event!','name'=>'evoAUL_nof3','legend'=>''),
				array('label'=>'Could not create event post, try again later!','name'=>'evoAUL_nof4','legend'=>''),
				array('label'=>'Bad nonce form verification, try again!','name'=>'evoAUL_nof5','legend'=>''),
				array('label'=>'You can only submit one event!','name'=>'evoAUL_nof6','legend'=>''),
				array('label'=>'Image upload failed!','name'=>'evoAUL_nof7','legend'=>''),
				array('label'=>'Thank you for updating your event!','var'=>1),
			array('type'=>'togend'),
			array('label'=>'Event Manager Section','type'=>'subheader'),
				array('label'=>'My Eventon Events Manager','var'=>'1'),
				array('label'=>'Login required to manage your submitted events','var'=>'1'),
				array('label'=>'Login Now','var'=>'1'),
				array('label'=>'Hello','var'=>'1'),
				array('label'=>'From your event manager dashboard you can view your submitted events and manage them in here','var'=>'1'),
				array('label'=>'Back to my events','var'=>'1'),
				array('label'=>'Event Publish Status','var'=>1),
				array('label'=>'Status','var'=>1),
				array('label'=>'publish','var'=>1),
				array('label'=>'draft','var'=>1),
				array('label'=>'Date','var'=>'1'),
				array('label'=>'Events','var'=>1),
				array('label'=>'My Submitted Events','var'=>'1'),
				array('label'=>'You do not have submitted events','var'=>'1'),
				array('label'=>'Featured Event','var'=>'1'),
				array('label'=>'Are you sure you want to delete this event','var'=>1),
				array('label'=>'Yes','var'=>1),
				array('label'=>'No','var'=>1),
				array('label'=>'Previous Events','var'=>1),
				array('label'=>'Next Events','var'=>1),
			array('type'=>'togend'),
		);

		$endAr = array(array('type'=>'togend'));

		// hook for addons
		$new_ar = apply_filters('eventonau_language_fields', array_merge($new_ar, $new_ar_1, $new_ar_2));

		return (is_array($_existen))? array_merge($_existen, $new_ar, $endAr): $_existen;
	}

}
new EVOAU_Lang();