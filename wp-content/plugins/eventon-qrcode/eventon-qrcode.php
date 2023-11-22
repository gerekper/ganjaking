<?php
/*
 Plugin Name: EventON - QR Code
 Plugin URI: http://www.myeventon.com/
 Description: Checkin customers with QR Code
 Author: Ashan Jay
 Version: 2.0
 Author URI: http://www.ashanjay.com/
 Requires at least: 5.0
 Tested up to: 5.8.2
 */

class EventON_qr{
	
	public $version='2.0';
	public $eventon_version = '4.0';
	public $evotx_version = '2.0';
	public $evors_version = '2.6';
	public $name = 'QR Code';
			
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

			include_once( 'includes/class-checkin.php' );			
			include_once( 'includes/class-shortcode.php' );
			$this->shortcode = new evo_qr_shortcode();			
			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
			}

			$this->checkin = new evoqr_checkin();			
		}
		

	// SUPPORTING
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon#eventon_qr">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}

	// ACTIVATION			
		// Deactivate addon
		function deactivate(){	$this->addon->remove_addon();	}
		

}

// Initiate this addon within the plugin
$GLOBALS['eventon_qr'] = new EventON_qr();

function EVOQR(){
	global $eventon_qr;	return $eventon_qr;
}