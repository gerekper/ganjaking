<?php
/**
 * Plugin Name: EventON - RSVP Events
 * Plugin URI: http://www.myeventon.com/
 * Description: Allow visitors to RSVP to your event.
 *  Author: Ashan Jay
 * Version: 2.8.2
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.0
 * Tested up to: 6.1
 *
 * Text Domain: evors
 * Domain Path: /lang/
 */

class EventON_rsvp{
	
	public $version='2.8.2';
	public $eventon_version = '4.2.2';
	public $name = 'RSVP Events';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path, $assets_path ;
	private $urls;
	public $template_url ;

	public $evors_opt;
	
	public $rsvp_array = array('y'=>'yes','m'=>'maybe','n'=>'no');
	public $rsvp_array_ = array('y'=>'Yes','m'=>'Maybe','n'=>'No');

	public $evors_args;
	public $l = 'L1';

	// Instanace
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	
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
				add_action( 'init', array( $this, 'init' ), 0 );
			}	
		}	
	
	// Eventon missing
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
	
	// SUPER init
		function super_init(){

			$this->load_plugin_textdomain();

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
			
			$this->helper = new evo_helper();
			
			EVO()->cal->load_more('evcal_rs');
			$this->opt2 = EVO()->cal->get_op('evcal_2'); 
			$this->evors_opt = EVO()->cal->get_op('evcal_rs'); 

			// settings link in plugins page
			add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));

			// /register_activation_hook( __FILE__, array($this,'evors_daily_schedule') );
			add_action( 'wp', array($this,'evors_daily_schedule') );	
			add_action('evors_daily_action', array($this, 'schedule_digest_email'));		
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-event_rsvp.php' );
			include_once( 'includes/class-rsvp.php' );
			include_once( 'includes/class-shortcode.php' );
			include_once( 'includes/class-emailing.php' );
			include_once( 'includes/class-event-manager.php' );
			include_once( 'includes/class-functions.php' );
			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-intergration-actionuser.php' );
			include_once( 'includes/class-intergration-qrcode.php' );
			include_once( 'includes/class-intergration-webhooks.php' );

			$this->frontend = new evors_front();
			$this->functions = new evorsvp_functions();
			$this->email = new evors_email();
			$this->webhooks = new EVORS_Webhooks();
			
			if ( is_admin() ){
				include_once( 'includes/admin/class-lang.php' );
				include_once( 'includes/admin/class-settings.php' );
				include_once( 'includes/admin/class-admin-ajax.php' );
				include_once( 'includes/admin/admin-init.php' );
			}else{ // only for frontend

			}
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
				include_once( 'includes/class-form.php' );
				$this->rsvpform = new evors_form();
			}			

			$this->register_rsvp_post_type();
			
			$this->shortcodes = new evo_rs_shortcode();
		}

	// create new post type
		function register_rsvp_post_type(){
			$labels = eventon_get_proper_labels( __('Event RSVP','eventon'),__('Event RSVPs','eventon'));
			register_post_type('evo-rsvp', 
				apply_filters( 'eventon_register_post_type_rsvp',
					array(
						'labels' => $labels,
						'description'	=> 'RSVP for eventon events',
						'public' 				=> true,
						'show_ui' 				=> true,
						'capability_type' 		=> 'eventon',
						'map_meta_cap'			=> true,
						'publicly_queryable' 	=> false,
						'hierarchical' 			=> false,
						'query_var'		 		=> true,
						'supports' 				=> array('title','custom-fields'),					
						'menu_position' 		=> 5, 
						'show_in_menu'			=>'edit.php?post_type=ajde_events',
						'has_archive' 			=> true,
						'exclude_from_search'	=> true
					)
				)
			);
		}		

	// Load localisation files
		function load_plugin_textdomain(){		
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'evors' );
							
			if ( is_admin() ) {
				load_textdomain( 'evors', WP_LANG_DIR . "/eventon-rsvp/lang/evors-".$locale.".mo" );	
				load_plugin_textdomain( 'evors', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
			}		
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_rs">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		// schedule tasks
			function evors_daily_schedule(){
				if( !wp_next_scheduled( 'evors_daily_action' ) ) {
				   	wp_schedule_event( time(), 'daily', 'evors_daily_action' );
				}
			}
			function schedule_digest_email(){
				$this->email->schedule_digest_email();
			}

		// Deactivate addon
			function deactivate(){
				$this->addon->remove_addon();
			}
		// duplicate language function to make it easy on the eye
			function lang($variable, $default_text, $lang=''){
				$lang = !empty($lang)? $lang: $this->l;

				return eventon_get_custom_language($this->opt2, $variable, $default_text, $lang);
			}
			function lang_e($text, $lang=''){
				evo_lang_e($text, $lang);
			}

			function check_rsvp_prop($field){				
				return (!empty($this->evors_opt[$field]) && $this->evors_opt[$field]=='yes')? 
					true: false;
			}
			function get_rsvp_prop($field){
				return (!empty($this->evors_opt[$field]))? 
					$this->evors_opt[$field]: false;
			}
}

// Initiate this addon within the plugin
function EVORS(){	return EventON_rsvp::instance();}
$GLOBALS['eventon_rs'] = EVORS();


?>