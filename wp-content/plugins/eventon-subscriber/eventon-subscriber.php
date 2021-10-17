<?php
/*
 Plugin Name: EventON - Subscriber
 Plugin URI: http://www.myeventon.com/
 Description: Subscribe to all calendar events
 Author: Ashan Jay
 Version: 1.3.4
 Author URI: http://www.ashanjay.com/
 Requires at least: 5.0
 Tested up to: 5.5
 */

 class eventon_sb{
 	public $version='1.3.4';
	public $eventon_version = '2.9';
	public $name = 'Subscriber';
	public $id = 'EVOSB';

	public $rss_slug;
	public $print_scripts_on = false;

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
			add_action('admin_notices', array($this, 'notice'));
			return false;			
		}			
		
		$this->addon = new evo_addons($this->addon_data);

		if($this->addon->evo_version_check()){
			add_action( 'init', array( $this, 'init' ), 0 );			
		}
	}

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
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	
		}

	// INITIATE please
		function init(){				
			add_filter("plugin_action_links_".$this->plugin_slug, array($this,'plugin_links' ));
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			$this->helper = new evo_helper();

			include_once($this->plugin_path.'/includes/class-shortcodes.php');
			include_once($this->plugin_path.'/includes/class-post-type.php');
			include_once($this->plugin_path.'/includes/class-frontend.php');
			include_once($this->plugin_path.'/includes/class-subscriber.php');

			$this->frontend = new evosb_front();

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				include_once($this->plugin_path.'/includes/admin/class-admin-init.php');
				$this->admin = new evosb_admin();
			}

			//AJAX includes
			if ( defined('DOING_AJAX') ){
				include_once($this->plugin_path.'/includes/class-ajax.php');
			}	
		}
	
 	// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}

	// plugin link in settings
		function plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_sb">'.__('Settings','eventon').'</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
	// language
		public function lang($variable, $default_text, $lang=''){
			return eventon_get_custom_language($this->frontend->evoOpt2, $variable, $default_text, $lang);
		}
		
}

// Initiate
function EVOSB(){	return eventon_sb::instance(); }
$GLOBALS['eventon_sb'] = EVOSB();