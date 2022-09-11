<?php
/*
 Plugin Name: EventON - Full cal
 Plugin URI: http://www.myeventon.com/
 Description: Create a full grid calendar with a month view of eventON events.
 Author: Ashan Jay
 Version: 2.0.3
 Author URI: http://www.ashanjay.com/
 Requires at least: 5.0
 Tested up to: 5.9
 */
 
class EventON_full_cal{
	
	public $version='2.0.3';
	public $eventon_version = '4.0.2';
	public $name = 'FullCal';
		
	public $is_running_fc =false;
	public $load_script =false;
		
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

	function plugin_init(){
		// check if eventon exists with addon class
		if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
			add_action('admin_notices', array($this, '_eventon_warning'));
			return false;			
		}
		
		$this->addon = new evo_addons($this->addon_data);
		if($this->addon->evo_version_check()){
			add_action( 'init', array( $this, 'init' ), 0 );
		}
	}
	function _eventon_warning(){
		?><div class="message error"><p><?php _e('EventON is required for FullCal to work properly.', 'eventon'); ?></p></div><?php
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
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE please
		function init(){	
			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-shortcode.php' );
			
			if ( is_admin() )
				include_once( 'includes/admin/admin-init.php' );

			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}

			$this->shortcodes = new evo_fc_shortcode();
			$this->frontend = new evofc_frontend();
				
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));
		}

	// SECONDARY FUNCTIONS	
		function deactivate(){
			$this->addon->remove_addon();
		}	
		function print_scripts(){
			$this->frontend->print_scripts_();
		}
}

// Initiate this addon within the plugin
function EVOFC(){	return EventON_full_cal::instance();}
$GLOBALS['eventon_fc'] = EVOFC();

/*** Only for PHP call to fullCal  */
	function add_eventon_fc($args='') {
		echo EVOFC()->shortcodes->fullcal_calendar($args, 'php');
	}
?>