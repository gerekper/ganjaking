<?php
/** 
 * front end 
 * @version 1.0
 */
class evowi_frontend{

	public $wishlist_manager = false;
	public function __construct(){

		$this->fnc = new evowi_fnc();

		// wishlisted events
		$this->user_wishlist = $this->fnc->user_wishlist();

		$wishlist_events = get_option('_evo_wishlist');
		//print_r($wishlist_events);
		//delete_option('_evo_wishlist');


		add_action( 'init', array( $this, 'register_styles_scripts' ) , 15);
		add_action('evo_addon_styles', array($this, 'styles') );

		// shortcode additions
		add_filter('eventon_shortcode_defaults', array($this,'add_shortcode_defaults'), 10, 1);
		add_filter('eventon_shortcode_popup',array($this,'shortcode_options'), 10, 1);		
		add_filter('eventon_calhead_shortcode_args', array($this,'cal_head_args'), 10, 2);

		// event AJAX
		add_filter('eventon_wp_query_args',array($this, 'wp_query_arg'), 10, 3);
		add_filter('eventon_wp_queried_events_list',array($this, 'queries_events'), 10, 2);

		add_action('evo_ajax_cal_before', array($this, 'evo_init_ajax_before'), 10, 1);
		add_filter('evo_init_ajax_wparg_additions', array($this, 'evo_init_ajax_wparg_additions'), 10, 1);
		add_filter('evo_generate_events_before_process', array($this, 'evo_generate_events_before_process'), 10, 1);

		// eventtop inclusion
		add_filter('eventon_eventtop_one', array($this, 'eventop'), 10, 3);
		add_filter('evo_eventtop_adds', array($this, 'eventtop_adds'), 10, 1);
		add_filter('eventon_eventtop_evowi', array($this, 'eventtop_content'), 10, 3);

		add_filter('evo_frontend_lightbox', array($this, 'lightbox'),10,1);
	}

	// STYLES
		function styles(){
			ob_start();
			include_once(EVOWI()->plugin_path.'/assets/evowi_style.css');
			echo ob_get_clean();
		}
		public function register_styles_scripts(){
			if(is_admin()) return false;
			
			// Load dailyview styles conditionally
			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$evOpt, true))
				wp_register_style( 'evowi_styles',EVOWI()->assets_path.'evowi_style.css');

			wp_register_script('evowi_script',EVOWI()->assets_path.'evowi_script.js', array('jquery'), EVOWI()->version, true );				
			wp_localize_script( 
				'evowi_script', 
				'evowi_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evowi_nonce' )
				)
			);

			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));
		}			
		function print_styles(){
			wp_enqueue_style( 'evowi_styles');	
			wp_enqueue_script('evowi_script');
		}

	// Show add to wish list element
		// event top inclusion
		public function eventop($array, $pmv, $vals){
			$array['evowi'] = array(
				'vals'=>$vals,
				'pmv'=>$pmv,
			);
			return $array;
		}
		public function eventtop_content($object, $helpers, $EVENT){
			
			// if wish list is not enabled for calednar
			if(!evo_settings_check_yn(EVO()->evo_generator->shortcode_args, 'wishlist')) return;

			$this->print_styles();

			$output = '';

			//print_r($object);
			$data = "data-ei='{$object->vals['eventid']}' data-ri='{$object->vals['ri']}' data-pl='". get_page_link() ."'";
			$evOpt = evo_get_options('1');
			$event_ri = $EVENT->ri;		

			if( $this->fnc->is_event_wishlisted($object->vals['eventid'], $event_ri, $this->user_wishlist) ){
				$output .= "<span class='evowi wishlisted' $data>
					<span class='evowi_wi_area'>
						<i class='fa ".get_eventON_icon('evcal_evowi_001', 'fa-heart',$evOpt )."'></i>
						<em>".$this->fnc->get_wishlist_count($object->vals['eventid'], $event_ri)."</em>
					</span>".evo_lang('In your wishlist')."</span>";
			}else{
				$output .= "<span class='evowi notlisted' $data>
					<span class='evowi_wi_area'>
						<i class='fa ".get_eventON_icon('evcal_evowi_002', 'fa-heart-o',$evOpt )."'></i>
						<em>".$this->fnc->get_wishlist_count($object->vals['eventid'], $event_ri)."</em>
					</span>".evo_lang('Add to wishlist')."</span>";
			}

			return $output;
		}

		// event card inclusion functions		
			function eventtop_adds($array){
				$array[] = 'evowi';
				return $array;
			}

		// lightbox
			function lightbox($array){
				$array['evowl_lightbox']= array(
					'id'=>'evowl_lightbox',
					'CLclosebtn'=> 'evowl_lightbox',
					'CLin'=> 'evowl_lightbox_body',
					'content'=>'<p class="evoloading loading_content"></p>'
				);
				return $array;
			}

	

	// AJAX
		function wp_query_arg($wp_arguments, $filters, $ecv){
						
			if(!isset($ecv['wishlist_filter'])) return $wp_arguments;
			if($ecv['wishlist_filter'] != 'yes') return $wp_arguments;

			$wp_arguments['post__in'] = $this->fnc->get_user_wishlist_events_array( $this->user_wishlist );

			return $wp_arguments;
		}

		function queries_events($event_list_array, $ecv){
			if(!isset($ecv['wishlist_filter'])) return $event_list_array;
			if($ecv['wishlist_filter'] != 'yes') return $event_list_array;

			foreach($event_list_array as $key=>$event){
				$ri = !empty($event['event_repeat_interval'])? $event['event_repeat_interval']: '0';
				if(!in_array($event['event_id'].'-'.$ri, $this->user_wishlist)){
					unset($event_list_array[$key]);
				}
			}

			return $event_list_array;
		}

	// SHORTCODE
		function shortcode_content($atts){
			// add el scripts to footer
			add_action('wp_footer', array($this, 'print_styles'));

			// Set number of months for the wish list manager if not passed
			if( !isset($atts['number_of_months']) ) $atts['number_of_months']= 12;
					
			if($this->user_wishlist){
				$this->only_wi_actions();

				$atts['calendar_type'] = 'wishlist';
				$atts['wishlist'] = 'yes';

				echo EVO()->calendar->_get_initial_calendar( $atts , array(
					'date_header'=>false,
				));	

				$this->remove_only_wi_actions();
			}else{
				echo '<p class="no_events">'.evo_lang('You do not have any wish list events')."</p>";
			}	
		}

		// before initial ajax call is run
		function evo_init_ajax_before(){
			$SC = EVO()->calendar->shortcode_args;
			if($SC['calendar_type'] != 'wishlist') return;

			// restrained time unix
			$number_of_months = (!empty($SC['number_of_months']))? (int)($SC['number_of_months']):12;
			$event_past_future = !empty($SC['event_past_future']) ? $SC['event_past_future']: 'all';
			

			$DD = EVO()->calendar->DD;
			$DD->setTimestamp( EVO()->calendar->current_time );


			// START range
			if( $event_past_future == 'all'){
				$months = (int)($number_of_months/2);
				$DD->modify('- '. $months .' months');
				$DD->modify('first day of this month');
			}
			if( $event_past_future == 'past'){
				$DD->modify('- '. $number_of_months .' months');
				$DD->modify('first day of this month');
			}

			$SC['focus_start_date_range'] = $DD->format('U');			


			// END RANGE
			if( $event_past_future == 'past'){
				$DD->setTimestamp( EVO()->calendar->current_time );
			}else{
				$DD->modify('+ '. $number_of_months .' months');
				$DD->modify('last day of this month');
				$DD->setTime(23,59,59);
			}

			$SC['focus_end_date_range'] = $DD->format('U');	

			$SC['wishlist'] = 'yes';	
			$SC['sep_month'] = 'yes';	
			$SC['hide_empty_months'] = 'yes';	

			EVO()->calendar->update_shortcode_arguments($SC);		

		}
		// before quering events for initial ajax call
		function evo_init_ajax_wparg_additions($A){
			$SC = EVO()->calendar->shortcode_args;
			if($SC['calendar_type'] != 'wishlist') return;

			return array('post__in'=> $this->fnc->get_user_wishlist_events_array( $this->user_wishlist ));
		}

		// before queried events list convert to HTML
		function evo_generate_events_before_process($EL){
			$SC = EVO()->calendar->shortcode_args;
			if($SC['calendar_type'] != 'wishlist') return $EL;

			// filter for only wishlisted events between repeat instances
				foreach($EL as $key=>$event){
					$ri = !empty($event['event_repeat_interval'])? $event['event_repeat_interval']: '0';
					if(!in_array($event['event_id'].'-'.$ri, $this->user_wishlist)){
						unset($EL[$key]);
					}
				}
			return $EL;
		}

	// SUPPORTIVE
		public function only_wi_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only_wi_actions(){
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));				
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoWI';
			return $name;
		}
		function cal_head_args($array, $arg=''){
			if( !empty($arg['wishlist'])){
				$array['wishlist'] = $arg['wishlist'];
				//if( $arg['wishlist'] == 'yes') $array['wishlist_filter'] = 'yes';
				
				if($this->wishlist_manager)
					$array['wishlist_filter'] = 'yes';
			}
			return $array;
		}
		function add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				'wishlist'=>'no',
				'wishlist_filter'=>'no',
				'title'=>'',
			));			
		}
		function shortcode_options($shortcode_array){
			$shortcode_array[0]['variables'][] = array(
				'name'=> 'Allow loggedin visitors to add events to wishlist',
				'type'=>'YN',
				'default'=>'no',
				'var'=>'wishlist',
				'guide'=>'This will allow loggedin visitors to add events to wishlist '
			);

			$shortcode_array[1]['variables'][] = array(
				'name'=> 'Allow loggedin visitors to add events to wishlist',
				'type'=>'YN',
				'default'=>'no',
				'var'=>'wishlist',
				'guide'=>'This will allow loggedin visitors to add events to wishlist '
			);


			// wish list events only
			$new_shortcode_array = array(
				array(
					'id'=>'s_WI',
					'name'=>'Wishlist Events Manager',
					'code'=>'add_eventon_wishlist_manager',
					'variables'=>array(
						EVO()->shortcode_gen->shortcode_default_field('event_past_future'),
						EVO()->shortcode_gen->shortcode_default_field('number_of_months'),
						EVO()->shortcode_gen->shortcode_default_field('lang'),
						EVO()->shortcode_gen->shortcode_default_field('etc_override'),
					)
				)
			);
			return array_merge($shortcode_array, $new_shortcode_array);
		}

}