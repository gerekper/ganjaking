<?php
/*
 * Plugin Name: EventON - Event Map  
 * Plugin URI: http://www.myeventon.com/event-map
 * Description: Create a map of all events
 * Version: 1.4.8
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 5.0
 * Tested up to: 6.0
 */ 
class evoemap{	
	
	// Versions
	public $version='1.4.8';
	public $eventon_version = '4.1';
	public $name = 'EventsMap';
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls, $template_url, $evOpt ;

	private $localtion_value ='';	
	
	public $is_running_em = false;
	public $load_script = false;
	
	public $_allevent_map = false;	
	private $current_locations = array();

	public $shortcode_args, $events_list, $this_cal;
	
	// Instanace
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {	self::$_instance = new self();	}
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
				$this->includes();	
			}
		}	
	
	// SUPER init
		function super_init(){
			// PLUGIN SLUGS			
			$this->addon_data['plugin_url'] = path_join(plugins_url(), basename(dirname(__FILE__)));
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

			// get plugin slug
			$this->plugin_url = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
			$this->plugin_path = dirname( __FILE__ );
			$this->plugin_slug = plugin_basename(__FILE__);
			list ($t1, $t2) = explode('/', $this->plugin_slug);
	        $this->slug = $t1;
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				$this->addon->updater();			
			}			
		}	
	
	//	INCLDUES
		function includes(){
			include_once( 'includes/shortcode.php' );
			include_once( 'includes/class-frontend.php' );

			$this->shortcodes = new evo_em_shortcode();
			$this->frontend = new evoem_frontend();
			
			if ( is_admin() ){
				include_once( 'includes/admin/admin_init.php' );
				$this->admin = new evoem_admin();
			}

			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}
		}	

	// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}

	// Eventon missing
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}

}

function EVOEM(){	return evoemap::instance();}
$GLOBALS['evoemap'] = EVOEM();

// php tag
function add_eventon_geocal($args=''){	
	echo EVOEVM()->shortcodes->generate_calendar($args);	
}