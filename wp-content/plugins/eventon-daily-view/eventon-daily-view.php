<?php
/*
 Plugin Name: EventON - Daily View 
 Plugin URI: http://www.myeventon.com/addons/daily-view/
 Description: Adds the capabilities to create a calendar with horizontally scrollable list of days of the month right below month title and sort bar. Read the guide for more information on how to use this addon.
 Author: Ashan Jay
 Version: 1.0.12
 Author URI: http://www.ashanjay.com/
 Requires at least: 4.0
 Tested up to: 5.4
 */
 
class EventON_daily_view{
	
	public $version='1.0.12';
	public $eventon_version = '2.8';
	public $name = 'DailyView';
		
	public $is_running_dv =false;
	public $load_script =false;
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	public $template_url ;	
	private $urls;
	
	public $shortcode_args;

	// Instanace
	protected static $_instance = null;
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

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
			// PLUGIN SLUGS			
			$this->addon_data['plugin_url'] = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
			$this->addon_data['plugin_url_'] = plugins_url('/'.basename(dirname(__FILE__)),dirname(__FILE__));
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
						
			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
			}

			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-shortcode.php' );
			
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class_ajax.php' );
			}

			$this->shortcodes = new evo_dv_shortcode();
			$this->frontend = new evodv_frontend();
		}		
	
	// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}
}


// Initiate this addon within the plugin
function EVODV(){
	return EventON_daily_view::instance();
}
$GLOBALS['eventon_dv'] = EVODV();

// php tag
function add_eventon_dv($args=''){	
	EVODV()->frontend->getCAL( $args);
}
?>