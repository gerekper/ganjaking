<?php
/**
 * Plugin Name: EventON - Include Anything
 * Plugin URI: http://www.myeventon.com/
 * Description: Include any posts inside eventON calendar seamlessly
 * Author: Ashan Jay 
 * Version: 0.5
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 6.0
 * Tested up to: 6.2.2
 */

class EVO_Include_Anything{
	
	public $version='0.5';
	public $eventon_version = '4.3';
	public $name = 'Include Anything';
	public $id = 'EVOIA';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path, $assets_path;
	private $urls;
	public $template_url, $addon , $frontend, $styles;

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
			add_action('plugins_loaded', array($this, 'plugin_init'));
		}
		function plugin_init(){
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
		// Eventon missing
			public function notice(){
				?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
		        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
			}
		
	// INITIATE please
		function init(){
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-styles.php' );

			$this->frontend = new EVOIA_Frontend();
			$this->styles = new EVOIA_Styles();
			
			if ( is_admin() ){
				include_once( 'includes/class-admin.php' );
				//include_once( 'includes/class-admin-ajax.php' );
				new EVOIA_Admin();
			}
		}

	// SECONDARY FUNCTIONS			
		/// Deactivate addon
			function deactivate(){	$this->addon->remove_addon();	}
		
}


// Initiate this addon within the plugin
function EVOIA(){ return EVO_Include_Anything::instance();}
$GLOBALS['EVO_Include_Anything'] = EVOIA();
?>