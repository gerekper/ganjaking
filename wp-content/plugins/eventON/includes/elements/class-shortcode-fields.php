<?php
/**
 * Shortcode Field Data Array
 * @3.0
 */

class Evo_Shortcode_Fields{
	// common used field arrays
	public function get_fields($key){
		$options_1 = EVO()->cal->get_op('evcal_1');

		// Additional Event Type taxonomies 
			$event_types_sc = array();
			for( $x=1; $x <= (apply_filters('evo_event_type_count',5)); $x++){
				if($x <=2 ) continue;
				if(!empty($options_1['evcal_ett_'.$x]) && $options_1['evcal_ett_'.$x]=='yes' && !empty($options_1['evcal_eventt'.$x])){
				 	$event_types_sc['event_type_'.$x] = array(
						'name'=>'Event Type '.$x,
						'type'=>'taxonomy',
						'guide'=>'Event Type '.$x.' category IDs - seperate by commas (eg. 3,12)',
						'placeholder'=>'eg. 3, 12',
						'var'=>'event_type_'.$x,
						'possible_values'=>'yes',
						'default'=>'0'
					);
				}else{ $event_types_sc['event_type_'.$x] = array(); }
			}

		
		$SC_defaults = array(
			'cal_id'=>array(
				'name'=>__('Calendar ID (optional)','eventon'),
				'type'=>'text',
				'var'=>'cal_id',
				'default'=>'0',
				'placeholder'=>'eg. 1'
			),
			'number_of_months'=>array(
				'name'=>__('Number of Months','eventon'),
				'type'=>'text',
				'var'=>'number_of_months',
				'default'=>'0',
				'placeholder'=>'eg. 5'
			),		
			'cal_init_nonajax'=>array(
				'name'=>__('Load Initial Calendar without AJAX','eventon'),
				'type'=>'YN',
				'var'=>'cal_init_nonajax',
				'guide'=>__('Events to be loaded to calendar initiall without ajax.','eventon'),
				'default'=>'no'
			),
			'show_et_ft_img'=>array(
				'name'=>__('Show Featured Image','eventon'),
				'type'=>'YN',
				'var'=>'show_et_ft_img',
				'default'=>'no'
			),'hide_ft_img'=>array(
				'name'=>__('Hide Event Featured Image','eventon'),
				'type'=>'YN',
				'var'=>'hide_ft_img',
				'default'=>'no'
			),
			'event_past_future'=>array(
				'name'=>__('Past/Future Events Filter','eventon'),
				'type'=>'select',
				'var'=>'event_past_future',
				'default'=>'all',
				'options'=>array( 
					'all'=>'All',
					'past'=>'Only Past Events',
					'future'=>'Only Future Events',
				)
			),
			'event_virtual'=>array(
				'name'=>__('Virtual Events Filter','eventon'),
				'type'=>'select',
				'var'=>'event_virtual',
				'default'=>'all',
				'options'=>array( 
					'all'=>'All',
					'vir'=>'Virtual Events',
					'nvir'=>'Non Virtual Events',
				)
			),
			'hide_past'=>array(
				'name'=>__('Hide Past Events','eventon'),
				'type'=>'YN',
				'var'=>'hide_past',
				'default'=>'no'
			),'hide_past_by'=>array(
				'name'=>__('Classify Past Events Based on','eventon'),
				'guide'=>__('You can choose which date (start or end) to use to decide when to clasify them as past events.','eventon'),
				'type'=>'select',
				'var'=>'hide_past_by',
				'default'=>'ee',
				'options'=>array( 
					'ss'=>'Event Start Date/time',
					'ee'=>'Event End Date/Time',
				)
			),
			'ft_event_priority'=>array(
				'name'=>__('Feature event priority','eventon'),
				'type'=>'YN',
				'guide'=>__('Move featured events above others','eventon'),
				'var'=>'ft_event_priority',
				'default'=>'no',
			),
			'event_count'=>array(
				'name'=>__('Event count limit','eventon'),
				'placeholder'=>'eg. 3',
				'type'=>'text',
				'guide'=>__('Limit number of events for each month eg. 3','eventon'),
				'var'=>'event_count',
				'default'=>'0'
			),
			'month_incre'=>array(
				'name'=>__('Month Increment','eventon'),
				'type'=>'text',
				'placeholder'=>'eg. +1',
				'guide'=>__('Change starting month (eg. +1)','eventon'),
				'var'=>'month_incre',
				'default'=>'0'
			),
			'event_type'=>array(
				'name'=>__('Event Type','eventon'),
				'type'=>'taxonomy',
				'guide'=>__('Event Type category IDs - seperate by commas (eg. 3,12)','eventon'),
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_type',
				'possible_values'=>'yes',
				'default'=>'0'
			),'event_type_2'=>array(
				'name'=>__('Event Type 2','eventon'),
				'type'=>'taxonomy',
				'guide'=>__('Event Type 2 category IDs - seperate by commas (eg. 3,12)','eventon'),
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_type_2',
				'possible_values'=>'yes',
				'default'=>'0'
			),
			'event_type_3'=>$event_types_sc['event_type_3'],
			'event_type_4'=>$event_types_sc['event_type_4'],
			'event_type_5'=>$event_types_sc['event_type_5'],
			'event_location'=>array(
				'name'=>__('Event Location','eventon'),
				'type'=>'taxonomy',
				'guide'=>__('Event Loction term ID(s) - seperate by commas (eg. 3,12)','eventon'),
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_location',
				'possible_values'=>'yes',
				'default'=>'0'
			),
			'event_organizer'=>array(
				'name'=>__('Event Organizer','eventon'),
				'type'=>'taxonomy',
				'guide'=>__('Event Organizer term ID(s) - seperate by commas (eg. 3,12)','eventon'),
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_organizer',
				'possible_values'=>'yes',
				'default'=>'0'
			),
			'event_tag'=>array(
				'name'=>__('Event Tag','eventon'),
				'type'=>'taxonomy',
				'guide'=>__('Event Tag IDs - seperate by commas (eg. 3,12)','eventon'),
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_tag',
				'possible_values'=>'no',
				'default'=>'0'
			),
			'fixed_month'=>array(
				'name'=>__('Fixed Month','eventon'),
				'type'=>'text',
				'guide'=>__('Set fixed month for calendar start (integer)','eventon'),
				'var'=>'fixed_month',
				'default'=>'0',
				'placeholder'=>'eg. 10'
			),
			'fixed_year'=>array(
				'name'=>__('Fixed Year','eventon'),
				'type'=>'text',
				'guide'=>__('Set fixed year for calendar start (integer)','eventon'),
				'var'=>'fixed_year',
				'default'=>'0',
				'placeholder'=>'eg. 2013'
			),
			'event_order'=>array(
				'name'=>__('Event Order','eventon'),
				'type'=>'select',
				'guide'=>__('Select ascending or descending order for events within a month. By default it will be Ascending order.','eventon'),
				'var'=>'event_order',
				'default'=>'ASC',
				'options'=>array('ASC'=>'ASC','DESC'=>'DESC')
			),
			'pec'=>array(
				'name'=>__('Event Cut-off','eventon'),
				'type'=>'select',
				'guide'=>__('Past or upcoming events cut-off time. This will allow you to override past event cut-off settings for calendar events. Current date = today at 12:00am','eventon'),
				'var'=>'pec',
				'default'=>'Current Time',
				'options'=>array( 
					'ct'=>'Current Time: '.date('m/j/Y g:i a', current_time('timestamp')),
					'cd'=>'Current Date: '.date('m/j/Y', current_time('timestamp')),
				)
			),
			'lang'=>array(
				'name'=>'Language Variation (<a href="'.get_admin_url().'admin.php?page=eventon&tab=evcal_2">'.__('Update Language Text','eventon').'</a>)',
				'type'=>'select',
				'guide'=>__('Select which language variation text to use','eventon'),
				'var'=>'lang',
				'default'=>'L1',
				'options'=>array('L1'=>'L1','L2'=>'L2','L3'=>'L3')
			),
			'hide_mult_occur'=>array(
				'name'=>__('Hide multiple occurence (HMO)','eventon'),
				'type'=>'YN',
				'guide'=>__('Hide events from showing more than once in between months','eventon'),
				'var'=>'hide_mult_occur',
				'default'=>'no',
			),
			'show_repeats' => array(
				'name'=>'Show all repeating events while HMO',
				'type'=>'YN',
				'guide'=>'If you are hiding multiple occurence of event but want to show all repeating events set this to yes',
				'var'=>'show_repeats',
				'default'=>'no',
			),
			'hide_empty_months'=>array(
				'name'=>'Hide empty months (Use ONLY w/ event list)',
				'type'=>'YN',
				'guide'=>'Hide months without any events on the events list',
				'var'=>'hide_empty_months',
				'default'=>'no',
			),
			'show_year'=>array(
				'name'=>'Show year',
				'type'=>'YN',
				'guide'=>'Show year next to month name on the events list',
				'var'=>'show_year',
				'default'=>'no',
			),
			'hide_month_headers'=>array(
				'name'=>__('Hide month headers','eventon'),
				'type'=>'YN',
				'guide'=>__('Remove the month headers that seprate each months events in the list.','eventon'),
				'var'=>'hide_month_headers',
				'default'=>'no',
			),
			'show_repeats'=>array(
				'name'=>__('Show all repeating events while HMO','eventon'),
				'type'=>'YN',
				'guide'=>__('If you are hiding multiple occurence of event but want to show all repeating events set this to yes','eventon'),
				'var'=>'show_repeats',
				'default'=>'no',
			),
			'fixed_mo_yr'=>array(
				'name'=>__('Fixed Month/Year','eventon'),
				'type'=>'fmy',
				'guide'=>__('Set fixed month and year value (Both values required)(integer)','eventon'),
				'var'=>'fixed_my',
			),
			'fixed_d_m_y'=>array(
				'name'=>__('Fixed Date/Month/Year','eventon'),
				'type'=>'fdmy',
				'guide'=>__('Set fixed date, month and year value (All values required)(integer)','eventon'),
				'var'=>'fixed_my',
			),
			'evc_open'=>array(
				'name'=>__('Open eventCards on load','eventon'),
				'type'=>'YN',
				'guide'=>__('Open eventCards when the calendar first load on the page by default. This will override the settings saved for default calendar.','eventon'),
				'var'=>'evc_open',
				'default'=>'no',
			),
			'UIX'=>array(
				'name'=>__('User Interaction','eventon'),
				'type'=>'select',
				'guide'=>__('Select the user interaction option to override individual event user interactions','eventon'),
				'var'=>'ux_val',
				'default'=>'0',
				'options'=>apply_filters('eventon_uix_shortcode_opts', array(
					'0'=>'None',
					'X'=>__('Do not interact','eventon'),
					'1'=>__('Slide Down EventCard','eventon'),
					'3'=>__('Lightbox popup window','eventon'),
					'3a'=>__('Lightbox popup window with AJAX','eventon'),
					'4'=>__('Open in single event page','eventon'),
					'4a'=>__('Open in single event page in new window','eventon')
				))
			),'etc_override'=>array(
				'name'=>__('Event type color override','eventon'),
				'type'=>'YN',
				'guide'=>__('Select this option to override event colors with event type colors, if they exists','eventon'),
				'var'=>'etc_override',
				'default'=>'no',
			),
			'only_ft'=>array(
				'name'=>__('Show only featured events','eventon'),
				'type'=>'YN',
				'guide'=>__('Display only featured events in the calendar','eventon'),
				'var'=>'only_ft',
				'default'=>'no',
			),
			'hide_ft'=>array(
				'name'=>__('Hide featured events','eventon'),
				'type'=>'YN',
				'guide'=>__('Hide all the featured events from showing in the calendar','eventon'),
				'var'=>'hide_ft',
				'default'=>'no',
			),
			'jumper'=>array(
				'name'=>__('Show jump months option','eventon'),
				'type'=>'YN',
				'guide'=>__('Display month jumper on the calendar','eventon'),
				'var'=>'jumper',
				'default'=>'no',
				),
				'exp_jumper'=>array(
					'name'=>__('Expand jump month on load','eventon'),
					'type'=>'YN',
					'guide'=>__('Show expand jump month section when calendar load','eventon'),
					'var'=>'exp_jumper',
					'default'=>'no',
				),
			'accord'=>array(
				'name'=>__('Accordion effect on eventcards','eventon'),'type'=>'YN',
				'guide'=>__('This will close open events when new one clicked','eventon'),'var'=>'accord','default'=>'no',
			),'sort_by'=>array(
				'name'=>__('Default Sort by','eventon'),
				'type'=>'select',
				'guide'=>__('Sort calendar events by on load','eventon'),
				'var'=>'sort_by',
				'default'=>'sort_date',
				'options'=>array( 
					'sort_date'=>__('Date','eventon'),
					'sort_title'=>__('Title','eventon'),
					'sort_posted'=>__('Posted Date','eventon'),
					'sort_rand'=>__('Random Order','eventon'),
				)
			),'hide_sortO'=>array(
				'name'=>__('Disable calendar sort/filter ability','eventon'),
				'type'=>'YN',
				'guide'=>__('This will hide sort options section on the calendar.','eventon'),
				'var'=>'hide_so',
				'default'=>'no',
			),'expand_sortO'=>array(
				'name'=>__('Expand sort/filter section by default','eventon'),
				'type'=>'YN',
				'guide'=>__('This will expand sort options section on load for calendar.','eventon'),
				'var'=>'exp_so',
				'default'=>'no',
			),'rtl'=>array(
				'name'=>__('* RTL can now be changed from eventON settings','eventon'),
				'type'=>'note',
				'var'=>'rtl',
				'default'=>'no',
			),

			'show_limit'=>array(
				'name'=>__('Show load more events button','eventon'),
				'type'=>'YN',
				'guide'=>__('Require "event count limit" to work, then this will add a button to show rest of the events for calendar in increments','eventon'),
				'var'=>'show_limit',
				'default'=>'no',
				'afterstatement'=>'show_limit'
			),
				'show_limit_redir'=>array(
					'name'=>__('Redirect load more events button','eventon'),
					'type'=>'text',
					'guide'=>__('http:// URL the load more events button will redirect to instead of loading more events on the same calendar.','eventon'),
					'var'=>'show_limit_redir',
					'default'=>'no',
				),
				'show_limit_ajax'=>array(
					'name'=>__('Load more events via AJAX','eventon'),
					'type'=>'YN',
					'guide'=>__('This will load more events via AJAX as oppose to loading all events onLoad.','eventon'),
					'var'=>'show_limit_ajax',
					'default'=>'no',
				),
				'show_limit_close'=>array(
					'name'=>'Custom Code','type'=>'customcode', 'value'=>'',
					'closestatement'=>'show_limit'
				),

			'members_only'=>array(
				'name'=>__('Make this calendar only visible to loggedin user','eventon'),
				'type'=>'YN',
				'guide'=>__('This will make this calendar only visible to loggedin users','eventon'),
				'var'=>'members_only',
				'default'=>'no',
			),'layout_changer'=>array(
				'name'=>__('Show calendar layout changer','eventon'),
				'type'=>'YN',
				'guide'=>__('Show layout changer on calendar so users can choose between tiles or rows layout','eventon'),
				'var'=>'layout_changer',
				'default'=>'no',
			),'filter_type'=>array(
				'name'=>__('Calendar Filter Type','eventon'),
				'type'=>'select',
				'guide'=>__('If sorting/filter allowed for calendar, you can select between dropdown list or checkbox list for multiple filter selection.','eventon'),
				'var'=>'filter_type',
				'default'=>'default',
				'options'=>array( 
					'default'=>__('Dropdown Filter List','eventon'),
					'select'=>__('Multiple Checkbox Filter','eventon'),
				)
			),
			'filter_show_set_only'=>array(
				'name'=>__('Show only selected filters','eventon'),
				'type'=>'YN',
				'guide'=>__('This will show only the above set filter values for selection on the calendar, other filter values will not show for selection on calendar','eventon'),
				'var'=>'filter_show_set_only',
				'default'=>'no',
			),'filter_relationship'=>array(
				'name'=>__('Filter Relationship for Multiple Event Types','eventon'),
				'type'=>'select',
				'guide'=>__('For multiple event types, select the filter relationship that to be used for filtering events.','eventon'),
				'var'=>'filter_relationship',
				'default'=>'default',
				'options'=>array( 
					'AND'=>__('AND','eventon'),
					'OR'=>__('OR','eventon'),
				)
			),
			'hide_arrows'=>array(
				'name'=>'Hide Calendar Arrows',
				'type'=>'YN',
				'guide'=>'This will hide calendar arrow navigations',
				'var'=>'hide_arrows',
				'default'=>'no',
			),
			'bottom_nav'=>array(
				'name'=>'Show Month Navigation at The Bottom of The Calendar',
				'type'=>'YN',
				'guide'=>'Enabling this will show month name and navigation arrows at the bottom of the event calendar.',
				'var'=>'bottom_nav',
				'default'=>'no',
			),
			'ics'=>array(
				'name'=>'Allow ICS download of all events',
				'type'=>'YN',
				'guide'=>'This will allow visitors to download one ICS file with all the events',
				'var'=>'ics',
				'default'=>'no',
			),
			'hide_end_time'=>array(
				'name'=>'Hide the end time on all events',
				'type'=>'YN',
				'guide'=>'This will hide the end time on calendar eventTop and EventCard, this will not effect single event page',
				'var'=>'hide_end_time',
				'default'=>'no',
			),
			'ml_priority'=>array(
				'name'=>'Move month long events to top',
				'type'=>'YN',
				'guide'=>'Prioritize month long events and move them to the top of the events list, featured events if prioritized will be moved above these',
				'var'=>'ml_priority',
				'default'=>'no',
			),
			'yl_priority'=>array(
				'name'=>'Move year long events to top',
				'type'=>'YN',
				'guide'=>'Prioritize year long events and move them to the top of the events list, featured events if prioritized will be moved above these',
				'var'=>'yl_priority',
				'default'=>'no',
			),
			'eventtop_style'=> array(				
				'name'=>'EventTop Design Style',
				'type'=>'select',
				'options'=>array(
					'2'=>'Colorful with gap between events',					
					'1'=>'Colorful EventTop',					
					'3'=>'Colorful event date bubbles',
					'0'=>'Clear with left border colors',
					'4'=>'Clear with left border colors and gaps',
					'5'=>'Crystal clear',
					),
				'guide'=>'This does not effect when tiles view is enabled',
				'var'=>'eventtop_style','default'=>'2',							
			),
			'eventtop_date_style'=> array(				
				'name'=>'EventTop Date Design Style',
				'type'=>'select',
				'options'=>array(
					'0'=>'Clean on the eye',					
					'1'=>'White wash bubble',
					),
				'guide'=>'This does not effect when tiles view is enabled',
				'var'=>'eventtop_date_style','default'=>'0',							
			),
			'tiles'=> array(
				'name'=>'Activate Tile Design',
				'type'=>'YN',
				'guide'=>'This will activate the tile event design for calendar instead of rows of events.',
				'default'=>'no',
				'var'=>'tiles',
				'afterstatement'=>'tiles'
			),
			'tile_count'=> array(
				'name'=>'Number of Tiles in a Row',
				'type'=>'select',
				'options'=>array(
					'2'=>'2',
					'3'=>'3',
					'4'=>'4',
					),
				'guide'=>'Select the number of tiles to show on one row',
				'var'=>'tile_count','default'=>'0'
			),
			'tile_height'=> array(
				'name'=>'Tile Box Minimum Height (px)',
				'placeholder'=>'eg. 200',
				'type'=>'text',
				'guide'=>'Set the minimum height of event tile for the tiled calendar design',
				'var'=>'tile_height','default'=>'0'
			),
			'tile_bg'=> array(
				'name'=>'Tile Background Color',
				'type'=>'select',
				'options'=>array(
					'0'=>'Event Color',
					'1'=>'Featured Image',
					),
				'guide'=>'Select the type of background for the event tile design',
				'var'=>'tile_bg','default'=>'0'
			),
			'tile_bg_size'=> array(
				'name'=>__('Tile Background Image Size','eventon'),
				'type'=>'select',
				'options'=>array(
					'full'=> __('Full Size','eventon'),
					'med'=> __('Medium Size','eventon'),
					'thumb'=> __('Thumbnail Size','eventon'),
					),
				'guide'=>'The size of the event image you select will effect how clear or blurred the image appear in tile.',
				'var'=>'tile_bg_size','default'=>'full'
			),
			'tile_style'=> array(
				'name'=>'Tile Style',
				'type'=>'select',
				'options'=>array(
					'0'=>'Default',
					'1'=>'Details under colored tile box',
					'2'=>'Details under clean tile box',
					),
				'guide'=>'With this you can select different layout styles for tiles',
				'var'=>'tile_style','default'=>'0'
			),
			'close_tiles'=> array(
				'name'=>'Custom Code','type'=>'customcode', 'value'=>'','closestatement'=>'tiles'
			),
			'livenow_bar'=> array(
				'name'=>'Hide Live Now bar & Time',
				'type'=>'YN',
				'guide'=>'Setting this to yes will hide the live now bar and time for this calendar.',
				'default'=>'no',
				'var'=>'livenow_bar',
			),
			'hide_cancels'=> array(
				'name'=>'Hide all cancelled events',
				'type'=>'YN',
				'guide'=>'This will hide all the cancelled events from the calendar. Compatible with other views.',
				'default'=>'no',
				'var'=>'hide_cancels',
			),
			'view_switcher'=> array(
				'name'=>'Enable multi views switcher (Addons Required) Beta*',
				'type'=>'YN',
				'guide'=>'This will allow visitors to select multi views of calendar with view style addons eg. fullCal, dailyview..',
				'default'=>'no',
				'var'=>'view_switcher',
			),
			'hide_et_dn' => array(
				'name'=>'Hide EventTop Date Numbers',
				'type'=>'YN',
				'guide'=>'This will hide big date numbers from event top',
				'var'=>'hide_et_dn',
				'default'=>'no'
			),
			'hide_et_tags'=>array(
				'name'=>'Hide EventTop Above Title Tags',
				'type'=>'YN',
				'guide'=>'This will hide colored rags above title from event top',
				'var'=>'hide_et_tags',
				'default'=>'no'
			),
			'hide_et_tl'=>array(
				'name'=>'Hide EventTop Time and Locations',
				'type'=>'YN',
				'guide'=>'This will hide time and location fields from event top',
				'var'=>'hide_et_tl',
				'default'=>'no'
			),
			'hide_et_extra'=>array(
				'name'=>'Hide EventTop Extra Content',
				'type'=>'YN',
				'guide'=>'This will hide event organizer, type etc. from event top',
				'var'=>'hide_et_extra',
				'default'=>'no'
			)
			
		);
		
		return $SC_defaults[$key];
	
	}
}