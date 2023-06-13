<?php
/*
 Plugin Name: EventON - Event Slider
 Plugin URI: http://www.myeventon.com/addons/event-slider
 Description: Display your events in a neatly crafted slider of events
 Author: Ashan Jay
 Version: 2.0.6
 Author URI: http://www.ashanjay.com/
 Requires at least: 6.0
 Tested up to: 6.1
 */

class evo_slider{
	
	public $version='2.0.6';
	public $eventon_version = '4.2';
	public $name = 'Event Slider';
	public $id = 'EVOSL';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
	// Construct
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
				$this->helper = new evo_helper();
				add_action( 'init', array( $this, 'init' ), 0 );
				add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));	
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

			include_once( 'includes/class-shortcode.php' );
			$this->shortcodes = new evosl_shortcode();

			include_once( 'includes/class-frontend.php' );
			$this->frontend = new evosl_front();
			
			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
			}
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_4">Guide</a>'; 
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
		// Eventon missing
			public function notice(){
				?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
		        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
			}
}
// Initiate this addon within the plugin
function EVOSL(){ return evo_slider::instance(); }
$GLOBALS['eventon_sl'] = EVOSL();
?>