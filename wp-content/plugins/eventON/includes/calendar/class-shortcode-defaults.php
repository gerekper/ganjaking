<?php
/**
 * Calendar shortcode defaults
 # @version 2.9
 */

class EVO_Calendar_Shortcode_Defaults extends evo_cal_shell{

	/**
	 * Shortcode variables that are available for calednar
	 * @return array array of all processed variables with values
	 */
		public function get_supported_shortcode_atts(){
			$args = array(
				'cal_id'=>'',
				'cal_init_nonajax'=>'no',
				'calendar_type'=>'default',
				'event_count'=>0, // restrict the event count show ing on the calednar
				'month_incre'=>0,
				'focus_start_date_range'=>'',
				'focus_end_date_range'=>'',
				'sort_by'=>'sort_date',		// sort_rand
					'exp_so'=>'no',		// expand sort options by default
				'filters'=>'no', // filters on yes or no
					'filter_type'=>'default',	// dropdown or select
					'filter_show_set_only'=>'no', // show only set filter values
					'filter_relationship'=>'AND', // filter logic relationship for multiple filters

				'view_switcher'=>'no', // calendar view switcher
				// MAP values
					'mapscroll'=> true,
					'mapformat'=>'roadmap',
					'mapzoom'=>18,
					'mapiconurl'=>'',
					'maps_load'=>'no',
				
				'fixed_month'=>0,
				'fixed_year'=>0,
					'hide_past'=>'no',
					'hide_past_by'=>'ee',	// ss | ee
				'event_virtual'=>'all',
				'event_status'=>'all',

				'show_et_ft_img'=>'no',
				'hide_ft_img'=>'no',
				'event_order'=>'ASC',
				'ft_event_priority'=>'no',
				'number_of_months'=>1,
				'hide_mult_occur'=>'no',
				'hide_empty_months'=>'no',
				'sep_month'=>'no',
				'hide_month_headers'=>'no',
				'show_repeats'=>'no', // show repeating events while hide multiple occurance
				'show_upcoming'=>0,
				'show_year'=>'no',
				
				'show_limit'=>'no',		// show only event count but add view more
					'show_limit_redir'=>'',		// url to redirect show more button
					'show_limit_ajax'=>'no',
					'show_limit_paged'=>1,
				
				'tiles'=>'no',		// tile box style cal
					'tile_height'=>0,		// tile height
					'tile_bg'=>0,		// tile background
					'tile_count'=>2,		// tile count in a row
					'tile_style'=>0,		// tile style
					'layout_changer'=>'no',	// show option to change layout between tile and rows	
					'tile_bg_size'=>'full',	// background image size

				'lang'=>'L1',
				'pec'=>'',				// past event cut-off
				'evc_open'=>'no',		// open eventCard by default
				'ux_val'=>'0', 			// user interaction to override default user interaction values
				'etc_override'=>'no',	// even type color override the event colors
				'jumper'=>'no'	,		// month jumper
					'jumper_offset'=>'0', 	// jumper start year offset
					'exp_jumper'=>'no', 	// expand jumper
					'jumper_count'=>5, 		// jumper years count
				'accord'=>'no',			// accordion
				'only_ft'=> 'no',		// only featured events				
				'hide_ft'=> 'no',		// hide all feaured events				
				'hide_so'=>'no',	// hide sort options
				'filters'=>'yes',	// enable filtering events def. yes
				'wpml_l1'=>'',		// WPML lanuage L1 = en
				'wpml_l2'=>'',		// WPML lanuage L2 = nl
				'wpml_l3'=>'',		// WPML lanuage L3 = es
				's'=>'',		// keywords to search
				'hide_arrows'=>'no',	// hide calendar arrows
				'bottom_nav'=>'no',		// calendar bottom arrows
				'members_only'=>'no',	// only visible for loggedin user
				'ics'=>'no'	,		// download all events as ICS
				'hide_end_time'=>'no'	,		// hide end time of the event
				'event_tag'=>'all',

				'ml_priority'=>'no',
				'yl_priority'=>'no',

				'eventtop_style' => 2, // event top style
				'eventtop_date_style' => 0, // event top date style
				'livenow_bar'=>'yes', // live now bar and time remaining
				'hide_cancels'=>'no', // hide all cancel events

				// hide certain parts from event top 
				'hide_et_dn'=> 'no',
				'hide_et_tags'=> 'no',
				'hide_et_tl'=> 'no',
				'hide_et_extra'=> 'no',

				// event parts
				'event_parts'=>'no',
				'ep_fields'=>'',

				'social_share'=>'no', // social share the calendar

				'search'=>'',
				'search_all'=>'no',
			);

			// each event type category
			foreach($this->get_all_event_tax() as $ety=>$ett){
				if(empty($ett)) continue;
				$args[$ett] ='all';
			}	


			// override eventtop_style with default set via settings
			if( $S = EVO()->cal->get_prop('evo_eventtop_style_def','evcal_1')){
				$args['eventtop_style'] = str_replace('_', '', $S);				
			}	

			// append certain site option values to SC
			$values = array(
				'_cal_evo_rtl'=>'evo_rtl',
				'mapzoom'=> 'evcal_gmap_zoomlevel',
			);	
			foreach($values as $L=>$F){
				if(!empty($this->cal->evopt1[$F]) ) $args[ $L ] = $this->cal->evopt1[$F];
			}

			$A = apply_filters('eventon_shortcode_defaults', $args);
			ksort($A);
			return $A;
		}
}
