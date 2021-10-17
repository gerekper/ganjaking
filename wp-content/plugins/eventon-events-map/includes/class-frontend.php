<?php
/** 
 * front end events map
 * @version 2.1.17
 */
class evoem_frontend{

	public $atts;

	public function __construct(){
		add_action( 'init', array( $this, 'register_styles_scripts' ) , 15);
		add_action( 'wp_footer', array( $this, 'print_page_scripts' ) ,15);
		add_action('evo_addon_styles', array($this, 'styles') );

		add_action('eventon_save_meta', array($this,'evmap_save_meta_values'),10,2);

		// calendar header button
		add_filter('evo_cal_above_header_btn', array($this, 'header_allmap_button'), 10, 2);
	}


	// include focus in header section
		function header_allmap_button($array, $args){
			if(!empty($args['focusmap']) && $args['focusmap']=='yes'){
				$new['evo-mapfocus']=evo_lang_get('evoEM_l2','All Map');
				$array = array_merge($new, $array);
			}
			return $array;
		}

	//	MAIN function to generate the calendar outter shell
		public function generate_evo_em(){

			$args = EVO()->evo_generator->process_arguments( $this->atts );
					
			$this->set_date_range();

			$this->only_em_actions();

			// LIGHTBOX
				if(!empty($args['lightbox']) && $args['lightbox']=='yes'){
					add_filter('evo_frontend_lightbox', array($this, 'ligthbox'),10,1);
				}	
			
			ob_start();

				// get events for the date range using shortcode values
				$_events = EVO()->calendar->_generate_events();
				$this->events_list = $_events['data'];


				if($args['map_type']=='upcoming'){
					echo EVO()->calendar->get_calendar_header(array(
						'date_header'=>false,
						'header_title'=> (!empty($args['map_title'])?$args['map_title']:'') ,
						'send_unix'=>true,
						'_html_evcal_list'=>false,
						'_html_sort_section'=>true
					));
				}else{
					echo EVO()->calendar->get_calendar_header(array(
						'_html_evcal_list'=>false,
						'_html_sort_section'=>true
					));
				}

				// map section
				echo $this->append_map_section($args);

				echo $_events['html']. '</div>';

				echo EVO()->calendar->body->get_calendar_footer();


				if($args['map_type']=='upcoming'){
					echo '<a class="evo-mapfocus evo_btn">'. evo_lang_get('evoEM_l2','All Map').'</a>';
				}

			$this->remove_only_em_actions();

			return ob_get_clean();
			
		}

		// GET calendar event date range
		function set_date_range(){
			$atts = $this->atts;
			$args = EVO()->calendar->shortcode_args;

			// upcoming events list
			if($args['map_type']=='upcoming'){
				$DD = EVO()->calendar->DD;
				$DD->setTimestamp( EVO()->calendar->current_time );

				EVO()->calendar->_update_sc_args('focus_start_date_range', $DD->format('U'));

				$this->this_cal['month'] = $DD->format('n');
				$this->this_cal['year'] =  $DD->format('Y');

				$number_of_months = !empty($atts['number_of_months'])? $atts['number_of_months']:12;

				$DD->modify( '+'. $number_of_months . 'months');
				
				EVO()->calendar->_update_sc_args('focus_end_date_range', $DD->format('U'));
				EVO()->calendar->_update_sc_args('event_past_future', 'future');
				EVO()->calendar->_update_sc_args('number_of_months', $number_of_months);

			}else{
				EVO()->calendar->_update_sc_args('number_of_months', '1');

				$this->this_cal['month'] =  date('n', EVO()->calendar->current_time);
				$this->this_cal['year'] =  date('Y', EVO()->calendar->current_time);
			}
		}
		
		
	//	Calendar with map of events
		public function append_map_section($args){			
			$evOpt = get_option('evcal_options_evcal_1');
			
			ob_start();

				$show_alle = (!empty($args['show_alle']) && $args['show_alle']=='yes')? 'yes':'no';
				$loc_page = (!empty($args['loc_page']) && $args['loc_page']=='yes')? 'yes':'no';
				$lightbox = (!empty($args['lightbox']) && $args['lightbox']=='yes')? 'yes':'no';
				
				// check default markers set if not get marker url
				$clusters = (!empty($evOpt['evomap_clusters']) && $evOpt['evomap_clusters']=='yes')? 'no':'yes';
				$mapzoomlevel = !empty($evOpt['evcal_gmap_zoomlevel'])? $evOpt['evcal_gmap_zoomlevel']:'8';

				// default lat long
					$latlon = !empty($evOpt['evomap_def_latlon'])? $evOpt['evomap_def_latlon']: '45.523062,-122.676482';
					$latlon = str_replace(' ', '', $latlon);
					$latlon = explode(',', $latlon);

				// passed on data
					$passed_data = array();
					$passed_data['txt'] = evo_lang_get('evoEM_l1','Events at this location');
					$passed_data['count'] = 0;
					$passed_data['locurl'] = site_url();
					$passed_data['loclink'] = $loc_page;
					$passed_data['filepath'] = EVOEM()->assets_path . 'images/m';
					$passed_data['dlat'] = $latlon[0];
					$passed_data['dlon'] = $latlon[1];					
					$passed_data['clusters'] = $clusters;					
					$passed_data['zoomlevel'] = $mapzoomlevel;					
					$passed_data['markertype'] = !empty($evOpt['evo_map_marker_type'])? $evOpt['evo_map_marker_type']:'dynamic';
					$passed_data['mapstyles'] = evo_settings_check_yn($evOpt,'evomap_map_style')?'true':'false';

					// marker URL
						if($passed_data['markertype']=='custom' && !empty($evOpt['evo_gmap_iconurl']))
							$passed_data['markerurl'] = urlencode($evOpt['evo_gmap_iconurl']);

						if($passed_data['markertype']=='dynamic' ) 
							$passed_data['markerurl'] = urlencode( EVOEM()->plugin_url);

					$map_data = json_encode( $passed_data );
				
				$mapID = rand(10,40);

				echo "<div class='evomap_section'>";

				echo "<div class='evomap_progress'><span></span></div>";

				echo "<div id='evoGEO_map_".$mapID."' class='evoGEO_map' ".( (!empty($args['map_height']))? 'style="height:'.$args['map_height'].'px;"':null )."></div>	
					<p class='evomap_noloc' style='display:none'>".evo_lang_get('evoEM_l3','No Events Available')."</p>
					<div class='evomap_debug' style='display:none'></div>
					<div class='evomap_data' data-d='". $map_data ."' data-filepath='". $passed_data['filepath'] ."'></div>
				</div>";
				
				echo "<div class='evoEM_list' data-showe='{$show_alle}' data-lightbox='{$lightbox}'>";
				echo "<div id='evcal_list' class='eventon_events_list evoEM'>";
				
			return ob_get_clean();
		}
			
		// get locations list
		// DEP 
			public function get_locations_list(){

				$events = $this->events_list;

				$locations = array();
					$count = 0;

					// go through all the event on hand
					foreach($events as $event){
						
						$pmv = $event['event_pmv'];
						$ri = !empty($event['event_repeat_interval']) ? $event['event_repeat_interval']:0;
						
						// location taxonomy 
						 	$evo_location_tax_id = (!empty($pmv['evo_location_tax_id']))? $pmv['evo_location_tax_id'][0]: false;

						if(!$evo_location_tax_id) continue; // skip is no location taxonomy ID

						// get location taxonomy data
							$LOCATIONterm = get_term_by('id',$evo_location_tax_id, 'event_location');

						if(!$LOCATIONterm) return false;

							//$LOCMETA = get_option( "taxonomy_$evo_location_tax_id" );
							$LOCMETA = evo_get_term_meta('event_location',$evo_location_tax_id, '', true);
					

						if(!empty($LOCMETA['location_lon']) && !empty($LOCMETA['location_lat']) ){
							$key = $LOCMETA['location_lat'].$LOCMETA['location_lon']; // array key

							// if there is a repeating instance
							if( isset($locations[$key]) && in_array( $event['event_id'], $locations[$key]['events']) ){
								$ri_addition = !empty($locations[$key]['ri'])? $locations[$key]['ri']+1: 1;
								$locations[$key]['ri']= $ri_addition;
							}	

							if( 
								(!empty($locations[$key]['events']) && !in_array( $event['event_id'], $locations[$key]['events']) )
								|| empty($locations[$key]) 
							){

								$eventids = !empty($locations[$key]['events'])? $locations[$key]['events']: array();
								$eventids[] = $event['event_id'];

								// location type
									//$location_type = (!empty($loc_lan) && !empty($loc_lon) )? 'lanlat':(!empty($loc_add)? 'address': false);
									$location_type = 'latlng';

								// location address
								$coordinates = $LOCMETA['location_lat'].','.$LOCMETA['location_lon'];
								$address = !empty($LOCMETA['location_address'])? $LOCMETA['location_address']: '';
								$name = $LOCATIONterm->name;

								$locations[$key] = array(
									'events'=>$eventids,
									'coordinates'=>$coordinates,
									'address'=>$address,
									'name'=>$name,
								);
								$count ++;
							}													
						}
					}// endforeach

					// /print_r($locations);
					$count = 0;
					foreach($locations as $ll){
						$locations_[$count] = $ll;
						$count++;
					}

					ob_start();

					if(!empty($locations_))
						echo json_encode($locations_);

					// go through all the locations
					/*foreach($locations as $location){
						// location type
						$location_type = $location['type'];
						$locationData = $location['locationData'];
						$ids = implode(',',$location['events']);
						echo "<p data-eventids='{$ids}' data-location_name='".($location['name'])."' data-locationData='{$locationData}' data-type='{$location_type}'></p>";
					}*/

				return array('content'=>ob_get_clean(), 'count'=>$count);

			}
		
	// Lightbox calling
		function ligthbox($array){
			$array['evoem_lightbox']= array(
				'id'=>'evoem_lightbox',
				'CLclosebtn'=> 'evolbclose_em',
				'CLin'=>'evoem_lightbox_body evo_pop_body eventon_events_list evcal_eventcard'
			);return $array;
		}

	//	Save the location slug when event data is saved
		public function evmap_save_meta_values($fields, $post_id){
			global $post;
			
			if(!empty($_POST[ 'evcal_location']) ){
				$location_slug = sanitize_title($_POST[ 'evcal_location' ]);
				update_post_meta( $post->ID, 'evcal_location_slug', $location_slug);
			}else{
				delete_post_meta( $post_id, 'evcal_location_slug');
			}
			
		}

	//	STYLES
		function styles(){
			ob_start();
			include_once(EVOEM()->plugin_path.'/assets/evmap_style.css');
			echo ob_get_clean();
		}
		public function register_styles_scripts(){
			if(is_admin()) return false;
			
			// Load dailyview styles conditionally
			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$evOpt, true))
				wp_register_style( 'eventon_em_styles',EVOEM()->assets_path.'evmap_style.css');

			wp_register_script('eventon_em_infobox',EVOEM()->assets_path.'infobox.js', array('jquery'), EVOEM()->version, true );
			wp_register_script('evoemap_cluster',EVOEM()->assets_path.'js/markerclusterer.js', array('jquery'), EVOEM()->version, true );
			wp_register_script('eventon_em_marker',EVOEM()->assets_path.'markerwithlabel_packed.js', array('jquery'), EVOEM()->version, true );
			wp_register_script('eventon_em_script',EVOEM()->assets_path.'evmap_script.js', array('jquery'), EVOEM()->version, true );				
			
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));
		}			
		//
		public function print_scripts(){
			wp_enqueue_script('eventon_em_infobox');
			wp_enqueue_script('evoemap_cluster');
			wp_enqueue_script('eventon_em_marker');
			wp_enqueue_script('eventon_em_script');
		}
		function print_styles(){
			wp_enqueue_style( 'eventon_em_styles');	
		}
		function print_page_scripts(){	
			if(EVOEM()->load_script)	$this->print_scripts();
		}

	// SUPPORT FUNCTIONS
		// ONLY for EM calendar actions 
		public function only_em_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only_em_actions(){
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));			
		}

		// add class name to calendar header for EM
		function eventon_cal_class($name){
			$name[]='eventmap';
			return $name;
		}

	// return fays in given month
		function days_in_month($month, $year) { 
			return date('t', mktime(0, 0, 0, $month+1, 0, $year)); 
		}
	// Append associative array elements
		function array_push_associative(&$arr) {
		   $ret='';
		   $args = func_get_args();
		   foreach ($args as $arg) {
			   if (is_array($arg)) {
				   foreach ($arg as $key => $value) {
					   $arr[$key] = $value;
					   $ret++;
				   }
			   }else{
				   $arr[$arg] = "";
			   }
		   }
		   return $ret;
		}

}