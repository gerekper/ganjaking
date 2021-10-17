<?php
/*
 Plugin Name: EventON - API Events
 Plugin URI: http://www.myeventon.com/
 Description: Display eventON events on external sites
 Author: Ashan Jay
 Version: 1.0.2
 Author URI: http://www.ashanjay.com/
 Requires at least: 4.0
 Tested up to: 4.7
 */

class EVOAP{
	
	public $version='1.0.2';
	public $eventon_version = '2.6';
	public $name = 'API Calendar';
	public $id = 'EVOAP';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
	// Construct
		public function __construct(){
			$this->super_init();
			add_action('plugins_loaded', array($this, 'plugin_init'));
		}

		public function plugin_init(){			
			// check if eventon exists with addon class
			if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
				add_action('admin_notices', array($this, 'notice'));
				return false;			
			}			
			
			$this->addon = new evo_addons($this->addon_data);

			if($this->addon->evo_version_check()){
				//$this->helper = new evo_helper();
				add_action( 'init', array( $this, 'init' ), 0 );
				add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
			}	
		}
		// Eventon missing
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
	
		// SUPER init
			function super_init(){
				// PLUGIN SLUGS			
				$this->addon_data['plugin_url'] = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
				$this->addon_data['plugin_slug'] = plugin_basename(__FILE__);
				list ($t1, $t2) = explode('/', $this->addon_data['plugin_slug'] );
		        $this->addon_data['slug'] = $t1;
		        $this->addon_data['plugin_path'] = dirname( __FILE__ );
		        $this->addon_data['evo_version'] = $this->eventon_version;
		        $this->addon_data['version'] = $this->version;
		        $this->addon_data['name'] = $this->name;

		        $this->plugin_url = $this->addon_data['plugin_url'];
		        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';
		        $this->plugin_slug = $this->addon_data['plugin_slug'];
		        $this->slug = $this->addon_data['slug'];
		        $this->plugin_path = $this->addon_data['plugin_path'];
			}

		// INITIATE please
			function init(){				
				// Deactivation
				register_deactivation_hook( __FILE__, array($this,'deactivate'));

				include_once( 'includes/class-styles.php' );
				
				if ( is_admin() ){
					include_once( 'includes/admin/admin-init.php' );
				}

				$this->styles = new evosy_styles();
				$this->opt = get_option('evcal_options_evcal_ap'); 

				add_action( 'rest_api_init', array($this, 'custom_rest_api'));			
			}

	// Set up custom endpoint for API
			function custom_rest_api(){
				global $EVOAP;

				// for showing event calendar on external sites
					register_rest_route( 
						'eventon','/calendar', 
						array(
							'methods' => 'GET',
							'callback' => array($this,'api_general_calendar'),					
							'permission_callback' => function (WP_REST_Request $request) {
			                	return true;
			            	}
						) 
					);
				// Show one event only by ID
					$api_namespace = 'eventon';
					register_rest_route( $api_namespace, '/oneevent', array(
						'methods' => 'GET',
						'callback' => array($this,'one_event_calendar'),
						'permission_callback' => function (WP_REST_Request $request) {
		                	return true;
		            	}
					) );

				// for getting event data as json
					$api_namespace = 'eventon';
					register_rest_route( $api_namespace, '/events', array(
						'methods' => 'GET',
						'callback' => array($this,'json_event_data'),
						'permission_callback' => function (WP_REST_Request $request) {
		                	return true;
		            	}
					) );

				// for getting custom event data as json
					$api_namespace = 'eventon/v1';
					register_rest_route( $api_namespace, '/custom-events/(?P<id>\d+)', array(
						'methods' => 'GET',
						'callback' => array($this,'custom_eventon_response'),
					) );

				// support for deprecated API urls
					$api_namespace = 'eventon/v1';
					register_rest_route( $api_namespace, '/calendar/(?P<id>\d+)', array(
						'methods' => 'GET',
						'callback' => array($this,'api_general_calendar')	
					) );
					$api_namespace = 'eventon/v1';
					register_rest_route( $api_namespace, '/oneevent/(?P<id>\d+)', array(
						'methods' => 'GET',
						'callback' => array($this,'one_event_calendar'),
					) );
					$api_namespace = 'eventon/v1';
					register_rest_route( $api_namespace, '/events/(?P<id>\d+)', array(
						'methods' => 'GET',
						'callback' => array($this,'json_event_data'),
					) );
			}

		// events response data hook
			function api_general_calendar($request){
				$parameters = $request->get_query_params();

				// parameters passed
				if(!empty($parameters) && sizeof($parameters)>0){
					$html = $this->get_events_html($parameters);
				}else{
					$html = $this->get_events_html();
				}				

				$response = array(
					'html'=>$html,
					'styles'=> $this->styles->get_styles(),  
				);				
				return $response;
			}
			function one_event_calendar($request){
				$parameters = $request->get_query_params();

				// parameters passed
				if(!empty($parameters) && sizeof($parameters)>0){
					$html = $this->get_oneevent_html($parameters);
				}else{
					$html = $this->get_oneevent_html();
				}

				$response = array(
					'html'=>$html,
					'styles'=> $this->styles->get_styles(),  
				);				
				return $response;
			}

		// Create the response for API
			function json_event_data( $request){
				$parameters = $request->get_query_params();

				// parameters passed
				if(!empty($parameters) && sizeof($parameters)>0){
					$events = $this->get_events($parameters);
				}else{
					$events = $this->get_events();
				}
		
				$response = array(
					'events'=>$events, 
				);				
				return $response;
			}
			function get_events($args=''){
				global $eventon;
				$args = !empty($args)? $args: array();
				$evo_opt = $eventon->frontend->evo_options;

				global $eventon;


				// pass event id
				$data_args = array();
				if(!empty($args['event_id'])){
					$data_args = array('wp_args'=>array('p'=> $args['event_id']));
				}

				$events_data = $eventon->evo_generator->get_all_event_data($data_args);

				if(!empty($events_data)):

					$evoapi_opt = get_evoOPT_array('ap');

					// create filters
					$filters = array();
					for($x=1; $x<=evo_get_ett_count($evo_opt); $x++){
						$_ett_name = ($x==1)? 'event_type': 'event_type_'.$x;

						if(empty($args[$_ett_name]) || $args[$_ett_name]=='all') continue;

						$filter = str_replace(' ', '', $args[$_ett_name]);
						$filter = explode(',', $filter);

						$filters[$_ett_name] = $filter;
					}


					$event_data_ =array();
					$events = 0;
					foreach($events_data as $event_id => $event_data){
						
						// filtering
						$filtering_stop = false;							
						if(count($filters)>0 ){

							// this loop check for all passed filter valiues
							foreach($filters as $eventtype => $filter){
								// if the event doesnt have event types
								if(empty($event_data[$eventtype]) ){
									$filtering_stop = true;
									continue;
								}

								$event_type_ids = array_keys($event_data[$eventtype]);
								
								// if the event doesnt have set filter values
								$intersect = array_intersect($filter, $event_type_ids);
								if(!$intersect || count($intersect) != count($filter) ){
									$filtering_stop = true;
								}
							}
						}

						if($filtering_stop) continue;

						$event_pmv = $event_data['pmv']; // store event PMV temporarily
						unset($event_data['pmv']);

						// pluggable for other fields
						$event_data_[$event_id] = apply_filters('evoapi_event_data',$event_data, $event_id, $event_pmv);
						$events++;
					}
					return sizeof($event_data_)>0? $event_data_: array('No Events');
				else:
					return array('No Events');
				endif;

							
			}
		
		// HTML for calendar view
			function get_events_html($args=''){
				
				global $eventon;
				add_filter( 'eventon_cal_class', array($this, 'api_cal_class'), 10, 1);
				
				$args = !empty($args)? $args: array();

				$evcal_opt = get_option('evcal_options_evcal_ap'); 

				// CUT OFF time calculation
					$current_timestamp = current_time('timestamp');
					// reset arguments
					$args['fixed_date']= $args['fixed_month']= $args['fixed_year']='';
					
					// passed values
						$args['event_count'] = $this->get_arg_value($args, 'event_count','evoAP_event_limit',0);
						$args['sep_month'] = $this->get_arg_value($args, 'sep_month','evoSL_sep_month','no');
						$number_of_months = (int)($this->get_arg_value($args, 'number_of_months','evoAP_months',1));
						$args['number_of_months'] = $number_of_months;
				
				// restrained time unix					
					$month_dif = '+';
					$unix_dif = strtotime($month_dif.($number_of_months).' months', $current_timestamp);

					$restrain_monthN = ($number_of_months>0)?				
						date('n',  $unix_dif):
						date('n',$current_timestamp);

					$restrain_year = ($number_of_months>0)?				
						date('Y', $unix_dif):
						date('Y',$current_timestamp);			

				// upcoming events list 
					$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
					$__focus_start_date_range = $current_timestamp;
					$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
				
				
				// Add extra arguments to shortcode arguments
				$new_arguments = array(
					'focus_start_date_range'=>$__focus_start_date_range,
					'focus_end_date_range'=>$__focus_end_date_range,
				);

				$args = (!empty($args) && is_array($args))? 
					wp_parse_args($args, $new_arguments): $new_arguments;


				// unchangeable argument values
					$args['ux_val'] = 4;


							
				// PROCESS variables
				$processed_args = $eventon->evo_generator->process_arguments($args);
				$this->shortcode_args = $processed_args;

				$content = '';

				$content .= $eventon->evo_generator->calendar_shell_header(
					array(
						'month'=>$restrain_monthN,'year'=>$restrain_year, 
						'date_header'=>false,
						'sort_bar'=>false,
						'date_range_start'=>$__focus_start_date_range,
						'date_range_end'=>$__focus_end_date_range,
						'title'=>'',
						'external'=>true,
						'send_unix'=>true
					)
				);
				
				$content .=$eventon->evo_generator->eventon_generate_events($processed_args);
				$content .=$eventon->evo_generator->calendar_shell_footer();

				remove_filter('eventon_cal_class',array($this, 'api_cal_class_remove'), 10, 1);

				return apply_filters('evosy_calendar_html',$content);
			}
			function get_oneevent_html($args=''){

				$evcal_opt = get_option('evcal_options_evcal_ap'); 

				$default_args = array(
					'lang'=>'',
					'event_id'=>'',
					'repeat_interval'=>''
				);

				$args = !empty($args)? wp_parse_args($args, $default_args): $default_args;

				$args['event_id'] = $this->get_arg_value($args, 'event_id','evoAP_event_id',false);				
				if(!$args['event_id']) return 'No event ID provided!';

				global $eventon;
				add_filter( 'eventon_cal_class', array($this, 'api_cal_class'), 10, 1);

				
				$eventon->evo_generator->is_eventcard_hide_forcer= true;
	
				// PROCESS variables
				$processed_args = $eventon->evo_generator->process_arguments($args);
				$args = wp_parse_args($args, $processed_args);
				$this->shortcode_args=$args;

				// unchangeable argument values
					$args['ux_val'] = 4;

				$content = '';

				$content .= $eventon->evo_generator->calendar_shell_header(
					array(
						'date_header'=>false,
						'sort_bar'=>false,
						'title'=>'',
						'external'=>true,
						'send_unix'=>true
					)
				);
				$event = $eventon->evo_generator->get_single_event_data(
					$args['event_id'] ,
					$args['lang'],
					$args['repeat_interval'],
					$args	
				);

				$content .= $event[0]['content'];
				$content .=$eventon->evo_generator->calendar_shell_footer();

				remove_filter('eventon_cal_class',array($this, 'api_cal_class_remove'), 10, 1);
				return apply_filters('evosy_calendar_html',$content);
			}

			function api_cal_class($array){
				$array[]= 'evoapi';
				return $array;
			}
			function api_cal_class_remove($array){
				if(in_array($array['evoapi']))
					$array = array_diff($array, array('evoapi'));

				return $array;
			}

			// check arg values first and if empty check options value and if empty return default
			function get_arg_value($args, $arg_fieldname, $options_fieldname, $default_value){
				$options = $this->opt;
				return (!empty($args[$arg_fieldname])? $args[$arg_fieldname]:
							(!empty($options[$options_fieldname])? $options[$options_fieldname]:$default_value ));
			}

		// custom event data creation
		// deprecating
			function custom_eventon_response( $data){
				$response = array(
					'events'=>$this->get_customevents(), 
				);				
				return $response;
			}

			function get_customevents(){
				global $eventon;

				$events_data = $eventon->evo_generator->get_all_event_data();

				if(!empty($events_data)):

					$evoapi_opt = get_evoOPT_array('ap');

					$filters = array();
					for($x=1; $x<=3; $x++){
						$_ett_name = ($x==1)? 'event_type': 'event_type_'.$x;
						if(!empty($evoapi_opt['evoSL_json_'.$_ett_name])){
							if($evoapi_opt['evoSL_json_'.$_ett_name]=='all') continue;

							$filter = str_replace(' ', '', $evoapi_opt['evoSL_json_'.$_ett_name]);
							$filter = explode(',', $filter);

							$filters[$_ett_name] = $filter;
						}
					}


					$event_data_ =array();
					foreach($events_data as $event_id => $event_data){
						
						// filtering
						$filtering_stop = false;							
						if(count($filters)>0 ){
							foreach($filters as $eventtype => $filter){
								if(empty($event_data[$eventtype]) ){
									$filtering_stop = true;
									continue;
								}

								$event_type_ids = array_keys($event_data[$eventtype]);

								if(!array_intersect($filter, $event_type_ids))
									$filtering_stop = true;
							}
						}

						if($filtering_stop) continue;

						$event_pmv = $event_data['pmv']; // store event PMV temporarily
						unset($event_data['pmv']);

						// pluggable for other fields
						$event_data_[$event_id] = apply_filters('evoapi_event_data',$event_data, $event_id, $event_pmv);
					}
					return $event_data_;
				else:
					return array('No Events');
				endif;
			}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_ap">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		// ACTIVATION			
			// Deactivate addon
			function deactivate(){
				$this->addon->remove_addon();
			}
		// duplicate language function to make it easy on the eye
			function lang($variable, $default_text, $lang=''){
				return eventon_get_custom_language($this->opt2, $variable, $default_text, $lang);
			}
}
// Initiate this addon within the plugin
$GLOBALS['EVOAP'] = new EVOAP();
?>