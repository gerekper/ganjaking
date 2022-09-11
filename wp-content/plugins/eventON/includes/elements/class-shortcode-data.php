<?php 
/**
 * EventON shortcode generator data
 * @version 2.9
 */

class EVO_Shortcode_Data{

	public $fields;
	public function __construct(){
		include_once 'class-shortcode-fields.php';
		$this->fields = new Evo_Shortcode_Fields();
	}

	// array of shortcode variables
		public function get_shortcode_field_array(){
			$_current_year = date('Y');
			$shortcode_guide_array = apply_filters('eventon_shortcode_popup', array(
				array(
					'id'=>'s1',
					'name'=> __('Main Calendar','eventon'),
					'code'=>'add_eventon',
					'variables'=>apply_filters('eventon_basiccal_shortcodebox', array(
						$this->shortcode_default_field('cal_id')
						,$this->shortcode_default_field('event_order')
						,$this->shortcode_default_field('event_count')
						,$this->shortcode_default_field('show_limit')
						,$this->shortcode_default_field('show_limit_redir')
						,$this->shortcode_default_field('show_limit_ajax')
						,$this->shortcode_default_field('show_limit_close')
						,$this->shortcode_default_field('month_incre')
						,$this->shortcode_default_field('fixed_mo_yr')						
						,$this->shortcode_default_field('lang')
						,$this->shortcode_default_field('UIX')

						,array('name'=> __('General Options','eventon'),'type'=>'collapsable','closed'=>true)	
							,$this->shortcode_default_field('cal_init_nonajax')
							,$this->shortcode_default_field('ft_event_priority')
							,$this->shortcode_default_field('ml_priority')
							,$this->shortcode_default_field('yl_priority')
							,$this->shortcode_default_field('only_ft')
							,$this->shortcode_default_field('hide_ft')
							,$this->shortcode_default_field('evc_open')				
							,array(
									'name'=>'Show jump months option',
									'type'=>'YN',
									'guide'=>'Display month jumper on the calendar',
									'var'=>'jumper',
									'default'=>'no',
									'afterstatement'=>'jumper_offset'
								),
								$this->shortcode_default_field('exp_jumper'),							
								array(
									'name'=>' Jumper Start Year',
									'type'=>'select',
									'options'=>array(
										'0'=>$_current_year-2,
										'1'=>$_current_year-1,
										'2'=>$_current_year,
										'3'=>$_current_year+1,
										),
									'guide'=>'Select which year you want to set to start jumper options at relative to current year',
									'var'=>'jumper_offset','default'=>'0',
								),array(
									'name'=>' Jumper Years Count',
									'type'=>'select',
									'options'=>array(1=>'1','2','3','4','5','6','7','8','9'),
									'guide'=>'Set how many years you want to show after the selected year above',
									'var'=>'jumper_count','default'=>'5',
									'closestatement'=>'jumper_offset'
								)
							,$this->shortcode_default_field('accord')
							,$this->shortcode_default_field('hide_arrows')
							,$this->shortcode_default_field('bottom_nav')
							,$this->shortcode_default_field('livenow_bar')
							,$this->shortcode_default_field('hide_cancels')

						,array('type'=>'close_div')	

						,array('name'=>'Sorting & Filtering Options','type'=>'collapsable','closed'=>true)
							,$this->shortcode_default_field('sort_by')
							,$this->shortcode_default_field('event_past_future')
							,$this->shortcode_default_field('hide_past_by')
							,array('name'=>'You can also use NOT-, NOT-all for event type filter values','type'=>'note')
							,$this->shortcode_default_field('event_type')
							,$this->shortcode_default_field('event_type_2')
							,$this->shortcode_default_field('event_type_3')
							,$this->shortcode_default_field('event_type_4')
							,$this->shortcode_default_field('event_type_5')
							,$this->shortcode_default_field('event_location')
							,$this->shortcode_default_field('event_organizer')	
							,$this->shortcode_default_field('event_tag')	
							,$this->shortcode_default_field('hide_sortO')						
							,$this->shortcode_default_field('expand_sortO')
							,$this->shortcode_default_field('filter_show_set_only')	
							,$this->shortcode_default_field('filter_type')	
							,$this->shortcode_default_field('filter_relationship')	
							,array('type'=>'close_div')	

						,array('name'=>'Display Design Options','type'=>'collapsable','closed'=>true)
							,$this->shortcode_default_field('show_et_ft_img')
							,$this->shortcode_default_field('etc_override')
							,$this->shortcode_default_field('eventtop_style')
							,$this->shortcode_default_field('eventtop_date_style'),

							$this->shortcode_default_field('tiles'),
								$this->shortcode_default_field('tile_count'),
								$this->shortcode_default_field('tile_height'),
								$this->shortcode_default_field('tile_style'),
								$this->shortcode_default_field('tile_bg'),
								$this->shortcode_default_field('tile_bg_size'),								
							$this->shortcode_default_field('close_tiles'),
							$this->shortcode_default_field('hide_et_dn'),
							$this->shortcode_default_field('hide_et_tags'),
							$this->shortcode_default_field('hide_et_tl'),
							$this->shortcode_default_field('hide_et_extra'),
							$this->shortcode_default_field('view_switcher'),
							
							array('type'=>'close_div'),	
						array('name'=>'Other Additional Options','type'=>'subheader'),
							$this->shortcode_default_field('members_only'),
							$this->shortcode_default_field('ics'),
							$this->shortcode_default_field('hide_end_time'),
						
					))
				),
				array(
					'id'=>'s2',
					'name'=> __('Event Lists','eventon'),
					'code'=>'add_eventon_list',
					'variables'=> apply_filters('eventon_basiclist_shortcodebox',array(
						
						$this->shortcode_default_field('number_of_months')
						,array(
							'name'=>'Event count limit',
							'placeholder'=>'eg. 3',
							'type'=>'text',
							'guide'=>'Limit number of events per month (integer)',
							'var'=>'event_count',
							'default'=>'0'
						),
						array(
							'name'=>__('Show load more events button','eventon'),
							'type'=>'YN',
							'guide'=>__('Require "event count limit" to work, then this will add a button to show rest of the events for calendar in increments','eventon'),
							'var'=>'show_limit',
							'default'=>'no',
							'afterstatement'=>'show_limit'
						),
							array(
								'name'=>__('Redirect load more events button','eventon'),
								'type'=>'text',
								'guide'=>__('http:// URL the load more events button will redirect to instead of loading more events on the same calendar.','eventon'),
								'var'=>'show_limit_redir',
								'default'=>'no',
							),
							array('name'=>'Load more events via AJAX is not supported on basic list','type'=>'note')
							,array(
								'name'=>'Custom Code','type'=>'customcode', 'value'=>'',
								'closestatement'=>'show_limit'
							)
						,$this->shortcode_default_field('month_incre')
						,$this->shortcode_default_field('fixed_mo_yr')
						,$this->shortcode_default_field('cal_id')
						,$this->shortcode_default_field('event_order')
						,$this->shortcode_default_field('UIX')

						,array('name'=>'Sorting & Filtering Options','type'=>'collapsable','closed'=>true)
							,$this->shortcode_default_field('event_past_future')
							,$this->shortcode_default_field('hide_past_by')
							,array('name'=>'You can also use NOT-, NOT-all for event type filter values','type'=>'note')
							,$this->shortcode_default_field('event_type')
							,$this->shortcode_default_field('event_type_2')
							,$this->shortcode_default_field('event_type_3')
							,$this->shortcode_default_field('event_type_4')
							,$this->shortcode_default_field('event_type_5')
							,$this->shortcode_default_field('event_location')
							,$this->shortcode_default_field('event_organizer')	
							,$this->shortcode_default_field('event_tag')	
							,$this->shortcode_default_field('hide_sortO')						
							,$this->shortcode_default_field('expand_sortO')
							,$this->shortcode_default_field('filter_show_set_only')	
							,$this->shortcode_default_field('filter_type')	
							,$this->shortcode_default_field('filter_relationship')	
							,array('type'=>'close_div'),

						array('name'=>'Display Design Options','type'=>'collapsable','closed'=>true),
							$this->shortcode_default_field('show_et_ft_img'),
							$this->shortcode_default_field('etc_override'),
							
							// tiles
							$this->shortcode_default_field('tiles'),
								$this->shortcode_default_field('tile_count'),
								$this->shortcode_default_field('tile_height'),
								$this->shortcode_default_field('tile_bg'),
								$this->shortcode_default_field('tile_bg_size'),
								$this->shortcode_default_field('tile_style'),
							$this->shortcode_default_field('close_tiles'),	

							array('type'=>'close_div')

						,array('name'=>'Other Additional Options','type'=>'subheader')
						,$this->shortcode_default_field('hide_month_headers')
						,$this->shortcode_default_field('hide_mult_occur')
						,$this->shortcode_default_field('show_repeats')						
						,$this->shortcode_default_field('hide_empty_months')						
						,$this->shortcode_default_field('show_year')

						,$this->shortcode_default_field('ft_event_priority'),
						$this->shortcode_default_field('only_ft'),						
						$this->shortcode_default_field('accord'),

						
						
						$this->shortcode_default_field('hide_end_time'),
					))
				),
				array(
					'id'=>'s_SE',
					'name'=>__('Single Event','eventon'),
					'code'=>'add_single_eventon',
					'variables'=>array(
						array(
							'name'=>'Event ID',
							'type'=>'select','var'=>'id',
							'placeholder'=>'eg. 234',	
							'options'=>	$this->get_event_ids()		
						),array(
							'name'=>'Repeat Interval ID',
							'type'=>'text','var'=>'repeat_interval',
							'guide'=>'Enter the repeat interval instance ID of the event you want to show from the repeating events series (the number at the end of the single event URL)  eg. 3. This is only for repeating events',
							'placeholder'=>'eg. 4',							
						),
						array(
							'name'=>__('Show only parts of the event','eventon'),
							'type'=>'YN',
							'guide'=>__('This will allow you to show only certain parts of the event anywhere you want using this shortcode. eg. Only event location map.','eventon'),
							'var'=>'event_parts',
							'default'=>'no',
							'afterstatement'=>'event_parts'
						),
							array(
								'name'=>__('Select fields to show','eventon'),
								'type'=>'select_in_lightbox',
								'var'=>'ep_fields',
								'options'=> $this->get_event_card_fields(),
								'placeholder'=>'eg. time',
							)
							
							,array(
								'name'=>'Custom Code','type'=>'customcode', 'value'=>'',
								'closestatement'=>'event_parts'
							),
						array(
							'name'=>'Show Event Excerpt',
							'type'=>'YN',
							'guide'=>'Show event excerpt under the single event box',
							'var'=>'show_excerpt',
							'default'=>'no'
						),array(
							'name'=>'Show expanded eventCard',
							'type'=>'YN',
							'guide'=>'Show single event eventCard expanded on load',
							'var'=>'show_exp_evc',
							'default'=>'no'
						),array(
							'name'=>'User click on Event Box',
							'type'=>'select',
							'guide'=>'What to do when user click on event box. NOTE: Show expended eventCard will be overridden if opening lightbox',
							'var'=>'ev_uxval',
							'options'=>array(
								'4'=>'Go to Event Page',
								'3'=>'Open event as Lightbox',
								'2'=>'External Link',
								'1'=>'SlideDown EventCard',
								'X'=>'Do nothing'
							),
							'default'=>'4'
						),
						array(
							'name'=>'External Link URL',
							'type'=>'text',
							'guide'=>'If user click on event box is set to external link this field is required with a complete url',
							'var'=>'ext_url',
							'placeholder'=>'http://'
						),
						$this->shortcode_default_field('lang')
						,

						

						array('name'=>'Display Design Options','type'=>'collapsable','closed'=>true),
							$this->shortcode_default_field('show_et_ft_img'),
							$this->shortcode_default_field('etc_override'),
							$this->shortcode_default_field('hide_et_dn'),
							$this->shortcode_default_field('hide_et_tags'),
							$this->shortcode_default_field('hide_et_tl'),
							$this->shortcode_default_field('hide_et_extra'),

							$this->shortcode_default_field('tiles'),
								$this->shortcode_default_field('tile_height'),
								$this->shortcode_default_field('tile_bg'),
								$this->shortcode_default_field('tile_bg_size'),
								$this->shortcode_default_field('tile_style'),
							$this->shortcode_default_field('close_tiles'),
							
							array('type'=>'close_div'),	
						
					)
				),

				// event from anywhere
				array(
					'id'=>'eventon_anywhere',
					'name'=>__('Single Event from Anywhere [Beta]','eventon'),
					'code'=>'eventon_anywhere',
					'variables'=>array(						
						array(
							'name'=>__('Call to action text','eventon'),
							'type'=>'text','var'=>'cta_text',
							'guide'=>__('The text that will call to load event details when clicked on','eventon'),
							'placeholder'=>__('eg. click to see event details','eventon'),							
						),array(
							'name'=>'Event ID',
							'type'=>'select','var'=>'id',
							'placeholder'=>'eg. 234',	
							'options'=>	$this->get_event_ids()		
						),array(
							'name'=>'Repeat Interval ID',
							'type'=>'text','var'=>'repeat_interval',
							'guide'=>'Enter the repeat interval instance ID of the event you want to show from the repeating events series (the number at the end of the single event URL)  eg. 3. This is only for repeating events',
							'placeholder'=>'eg. 4',							
						),array(
							'name'=>'User click on Event Box',
							'type'=>'select',
							'guide'=>'What to do when user click on event box. NOTE: Show expended eventCard will be overridden if opening lightbox',
							'var'=>'ev_uxval',
							'options'=>array(
								'4'=>'Go to Event Page',
								'3'=>'Open event as Lightbox',
							),
							'default'=>'4'
						)						
					)
				),

				array(
					'id'=>'s_NOW',
					'name'=>__('Live Now Calendar View','eventon'),
					'code'=>'add_eventon_now',
					'variables'=>array(						
						$this->shortcode_default_field('lang'),
						$this->shortcode_default_field('show_et_ft_img'),
						$this->shortcode_default_field('etc_override'),
						//$this->shortcode_default_field('hide_ft_img'),
						$this->shortcode_default_field('UIX'),
						array(
							'name'=>'Hide happening now events',
							'type'=>'YN',
							'guide'=>'This will hide the happening now events, but will show coming up next.',
							'var'=>'hide_now',
							'default'=>'no'
						),array(
							'name'=>'Hide coming up next section',
							'type'=>'YN',
							'guide'=>'This will hide the coming up next section.',
							'var'=>'hide_next',
							'default'=>'no'
						)
						
					)
				),

				

				array(
					'id'=>'evosv',
					'name'=>__('Schedule View','eventon'),
					'code'=>'add_eventon_sv',
					'variables'=>array(						
						$this->shortcode_default_field('lang'),
						$this->shortcode_default_field('etc_override'),
						$this->shortcode_default_field('UIX'),	
					)
				),array(
					'id'=>'evotv',
					'name'=>__('Tabbed View [Beta]','eventon'),
					'code'=>'add_eventon_tabs',
					'variables'=>$this->tab_array_content()
				)
			));
			
			return $shortcode_guide_array;
		}

	public function tab_array_content(){

		$return = array();
		$return[] = array('name'=>__('IMPORTANT: Make sure tab shortcode does not include [ or ]. Eg. add_eventon','eventon'),
							'placeholder'=>'eg. Main Calendar','type'=>'note');

		for($x=1; $x<=5; $x++){
			$return[] = array(
				'name'=>__('Tab Name','eventon').' #'.$x,
				'placeholder'=>'eg. Main','type'=>'text',
				'var'=>'tab'.$x,'default'=>'Tab Name'
			);
			$return[] = array(
				'name'=>__('Tab Shortcode','eventon').' #'.$x,
				'placeholder'=>'eg. add_eventon','type'=>'text',
				'var'=>'tab'.$x.'shortcode','default'=>'add_eventon'
			);
		}
		return $return;
		
	}
	public function shortcode_default_field($A){
		return $this->fields->get_fields($A);
	}

	public function get_event_card_fields(){
		$HH = new evo_cal_help();
		return $HH->get_eventcard_fields( );
	}

	// Supportive
		// get event ids
		function get_event_ids(){
			global $post, $wpdb;
			$backup_post = $post;

			$events = $wpdb->get_results("
				SELECT $wpdb->posts.post_title, $wpdb->posts.ID 
				FROM $wpdb->posts 
				WHERE $wpdb->posts.post_type ='ajde_events' 
				AND $wpdb->posts.post_status = 'publish'
				ORDER BY $wpdb->posts.post_date DESC
			");
						
			$ids = array();
			$ids['na'] = '--';
			if($events){
				foreach($events as $event){
					$ids[$event->ID] = $event->post_title.' ('.$event->ID.')';
				}			
			}
			
			$post = $backup_post;
			return $ids;
		}
}