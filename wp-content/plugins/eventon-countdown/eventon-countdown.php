<?php
/*
 Plugin Name: EventON - Countdown timer
 Plugin URI: http://www.myeventon.com/
 Description: Count down till the event ends
 Author: Ashan Jay
 Version: 0.16
 Author URI: http://www.ashanjay.com/
 Requires at least: 5.0
 Tested up to: 5.7

 */

 class eventon_cd{
 	public $version='0.16';
	public $eventon_version = '3.1.1';
	public $name = 'Countdown';
	public $id = 'EVOCD';

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
	function notice(){
		?><div class="message error"><p><?php _e('EventON is required for this addon to work properly.', 'eventon'); ?></p></div><?php
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
			$this->helper = new evo_helper();
			add_filter("plugin_action_links_".$this->plugin_slug, array($this,'plugin_links' ));

			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once($this->plugin_path.'/includes/class-shortcodes.php');
			include_once($this->plugin_path.'/includes/class-frontend.php');

			include_once($this->plugin_path.'/includes/class-integration-virtualevents.php');

			$this->frontend = new evocd_front();

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				include_once($this->plugin_path.'/includes/admin/class-admin-init.php');
				$this->admin = new evocd_admin();
			}

			//AJAX includes
			if ( defined('DOING_AJAX') ){
				//include_once($this->plugin_path.'/includes/class-ajax.php');
			}
		}
	
	
 	// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}

	// plugin link in settings
		function plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_cd">'.__('Settings','eventon').'</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
	// language
		public function lang($variable, $default_text, $lang=''){
			return eventon_get_custom_language($this->frontend->evoOpt2, $variable, $default_text, $lang);
		}
		
 }

// Initiate this addon within the plugin
function EVOCD(){return eventon_cd::instance();}
$GLOBALS['eventon_cd'] = EVOCD();