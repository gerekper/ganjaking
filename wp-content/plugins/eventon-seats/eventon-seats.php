<?php
/**
 * Plugin Name: EventON - Seats
 * Plugin URI: http://www.myeventon.com/addons/event-lists-items
 * Description: Seat selection feature
 * Author: Ashan Jay
 * Version: 1.1.1
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.5
 * Tested up to: 6.0.3
 * 
 * Text Domain: evost
 * Domain Path: /lang/
 *
 */

class EVO_seats{
	
	public $version='1.1.1';
	public $eventon_version = '4.0';
	public $evotx_version = '2.0';
	public $name = 'Event Seats';
	public $id = 'EVOST';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
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
				if(!class_exists('WooCommerce')){
					add_action('admin_notices', array($this, '_wc_eventon_warning'));
				}elseif(!class_exists('evotx')){
					add_action('admin_notices', array($this, '_tx_eventon_warning'));
				}else{
					add_action( 'init', array( $this, 'init' ), 0 );
					$this->helper = new evo_helper();
					// settings link in plugins page
					add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
				}
			}
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

	        // guide file
	        	$this->addon_data['guide_file'] = ( file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
								$this->addon_data['plugin_url'].'/guide.php':null;

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	        
		}

	// INITIATE please
		function init(){	

			$this->textdomain();

			$this->opt2 = get_option('evcal_options_evcal_2');
			$this->opt = get_option('evcal_options_evcal_tx');
			
			include_once( 'includes/class-integration-general.php' );
			include_once( 'includes/class-template_views.php' );
			include_once( 'includes/class-event_seats.php' );
			//include_once( 'includes/class-event_seats_session.php' );
			include_once( 'includes/class-seat_expirations.php' );
			include_once( 'includes/class-event_seats_seat.php' );
			include_once( 'includes/class-event_seats_section.php' );			
			include_once( 'includes/class-event_seats_json.php' );
			include_once( 'includes/class-frontend.php' );			
			include_once( 'includes/class-integration-tickets.php' );
			include_once( 'includes/class-integration-qrcode.php' );
			
			$this->frontend = new evost_front();
			
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}
			if ( is_admin() ){
				include_once( 'includes/admin/class-seat-map-editor.php' );
				include_once( 'includes/admin/class-admin.php' );
				$this->admin = new evost_admin();
			}

			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));
		}

	// Localization
		function textdomain(){
			if(is_admin())
				load_plugin_textdomain( 'evost', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_tx#evotxst">Settings</a>'; 
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
		// notices
		function _wc_eventon_warning(){
	        ?><div class="message error"><p><?php _e('Eventon Seats need Woocommerce plugin to function properly. Please install woocommerce', 'eventon'); ?></p></div><?php
	    }	
	    public function _tx_eventon_warning(){
			?><div class="message error"><p><?php _e('Eventon Seats require Event Tickets addon to function properly. Please install Event Tickets addon!', 'eventon'); ?></p></div><?php
		}
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}  
}

// initiate
function EVOST(){ return EVO_seats::instance();}

$GLOBALS['evost'] = EVOST();

?>