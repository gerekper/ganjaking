<?php
/*
 Plugin Name: EventON - Lists and Items
 Plugin URI: http://www.myeventon.com/addons/event-lists-items
 Description: Create custom eventON category lists and item boxes
 Author: Ashan Jay
 Version: 0.12
 Author URI: http://www.ashanjay.com/
 Requires at least: 5.0
 Tested up to: 5.6
 */

class EVO_lists{
	
	public $version='0.12';
	public $eventon_version = '3.0.7';
	public $name = 'Event Lists & Items';
	public $id = 'EVOLI';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	public $load_scripts = false;
	
	// construct
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		public function __construct(){
			$this->super_init();
			add_action('plugins_loaded', array($this, 'plugin_init'), 15);
		}
		public function plugin_init(){
			// check if eventon exists with addon class
			if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
				add_action('admin_notices', array($this, 'notice'));
				return false;			
			}	

			$this->addon = new evo_addons($this->addon_data);
			if($this->addon->evo_version_check())
				add_action( 'init', array( $this, 'init' ), 0 );		
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
			$this->helper = new evo_helper();
			$this->opt2 = EVO()->calendar->evopt2;

			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-shortcode.php' );
			$this->shortcodes = new evoli_shortcode();

			include_once( 'includes/class-frontend.php' );
			$this->frontend = new evoli_front();
			
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}
			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
			}
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_li">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		// ACTIVATION		
			function deactivate(){
				$this->addon->remove_addon();
			}
		// duplicate language function to make it easy on the eye
			function lang($variable, $default_text, $lang=''){
				return eventon_get_custom_language($this->opt2, $variable, $default_text, $lang);
			}
}
// Initiate this addon within the plugin
function EVOLI(){ return EVO_lists::instance(); }
$GLOBALS['eventon_li'] = EVOLI();
?>