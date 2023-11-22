<?php
/**
 * Plugin Name: EventON - Event Photos Plus
 * Plugin URI: http://www.myeventon.com/
 * Description: Extend the event additional images capabilities
 * Author: Ashan Jay 
 * Version: 1.0.4
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.0
 * Tested up to: 5.5.1
 */

class eventon_photos{
	
	public $version='1.0.4';
	public $eventon_version = '2.9';
	public $name = 'Event Photos Plus';
	public $id = 'EVOEP';
			
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
	
	// construct
	public function __construct(){

		$this->super_init();
		add_action('plugins_loaded',array($this, 'plugin_init'));

	}

	public function plugin_init(){

		// check if eventon exists with addon class
		if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
			add_action('admin_notices', array($this, 'notice'));
			return false;			
		}
		
		$this->addon = new evo_addons($this->addon_data);
		$this->helper = new evo_helper();

		$this->opt = get_option('evcal_options_evcal_ep');
		$this->opt2 = get_option('evcal_options_evcal_2');

		add_action( 'init', array( $this, 'init' ), 0 );

		// settings link in plugins page
		add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
					
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
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];

	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	
		}

	// INITIATE please
		function init(){				
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			//include_once( 'includes/class-shortcode.php' );
			//$this->shortcodes = new evoep_shortcode();

			include_once( 'includes/class-frontend.php' );
			$this->frontend = new evoep_front();
			
			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
				include_once( 'includes/admin/class-meta_boxes.php' );
				new evoep_metaboxes();
			}
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}			
			
			
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_ep">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - ','eventon'), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
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
function EVOEP(){ return eventon_photos::instance();}
$GLOBALS['eventon_photos'] = EVOEP();
?>