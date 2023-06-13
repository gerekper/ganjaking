<?php
/*
 Plugin Name: EventON - Event Lists Ext
 Plugin URI: http://www.myeventon.com/
 Description: Create past and upcoming event lists for eventON
 Author: Ashan Jay
 Version: 1.0
 Author URI: http://www.ashanjay.com/
 Requires at least: 6.0
 Tested up to: 6.2
 Text Domain: evoel
 Domain Path: /lang/
 */
 
class eventon_event_lists{
	
	public $version='1.0';
	public $eventon_version = '4.3';
	public $name = 'EventLists';
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path, $assets_path, $addon ;	
	public $template_url, $frontend, $shortcodes;
	private $urls;
	
	public $is_running_el = false;
	
	// Instanace
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	/* Construct	 */
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

			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-shortcode.php' );
			$this->frontend = new evoel_frontend();
			$this->shortcodes = new evo_el_shortcode();	
					
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			// RUN addon updater only in dedicated pages
			if ( is_admin() )	$this->addon->updater();
		}

	// Load localisation files
		function load_plugin_textdomain(){		
			
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'evoel' );
			
			if ( is_admin() ) {
				load_textdomain( 'evoel', WP_LANG_DIR . "/eventon-event-lists/lang/evoel-".$locale.".mo" );	
				load_plugin_textdomain( 'evoel', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
			}		
		}

	// SECONDARY FUNCTIONS
		function print_scripts(){$this->frontend->print_scripts();}
		function deactivate(){
			$this->addon->remove_addon();
		}	
}

// Initiate this addon within the plugin
function EVOEL(){	return eventon_event_lists::instance();}
$GLOBALS['eventon_el'] = EVOEL();

// php tag
	function add_eventon_el($args='') {
		
		/*
		// connect to support arguments
		$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();		
		$args = shortcode_atts( $supported_defaults, $args ) ;
		*/
		
		$content = EVOEL()->frontend->getCAL($args, 'php');		
		echo $content;
	}