<?php
/*
 * Plugin Name: EventON - Sync Events
 * Plugin URI: http://www.myeventon.com/
 * Description: Sync facebook and google calendar events
 * Author: Ashan Jay
 * Version: 1.2.3
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 4.0
 * Tested up to: 4.9.4
 *
 * Text Domain: evosy
 * Domain Path: /lang/
 *
 * @package Sync Events
 * @Author AJDE
 */

 class eventon_sy{
 	public $version='1.2.3';
	public $eventon_version = '2.6.15';
	public $name = 'Sync';
	public $id = 'EVOSY';

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
				$this->helper = new evo_helper();
				add_action( 'init', array( $this, 'init' ), 0 );
				add_filter("plugin_action_links_".$this->plugin_slug, array($this,'plugin_links' ));
			}
		}

		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
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
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			$this->load_plugin_textdomain();

			$this->options = get_option('evcal_options_evosy_1');

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){				
				include_once($this->plugin_path.'/includes/admin/class-admin-init.php');
				$this->admin = new evosy_admin();
			}
			
			include_once($this->plugin_path.'/includes/admin/class-log.php');
			include_once($this->plugin_path.'/includes/admin/class-googlecal.php');
			include_once($this->plugin_path.'/includes/class-ajax.php');
			include_once($this->plugin_path.'/includes/class-functions.php');
			include_once($this->plugin_path.'/includes/class-cron.php');
			$this->functions = new evosy_functions();	
			$this->cron = new evosy_cron();

			// Cron jobs
			$this->cron->schedule_jobs();
		}

	// Deactivate addon
		function deactivate(){	
			$this->addon->remove_addon();	
			wp_clear_scheduled_hook('evosy_schedule_action_gg');
			wp_clear_scheduled_hook('evosy_schedule_action_fb');
		}

	// plugin link in settings
		function plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_sy">'.__('Settings','evosy').'</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}		
	
	// Load localisation files
		function load_plugin_textdomain(){		
			if ( is_admin() ) {
				$locale = apply_filters( 'plugin_locale', get_locale(), 'evosy' );
				
				load_plugin_textdomain( 'evosy', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
			}
		}
 }


function EVOSY(){
	return eventon_sy::instance();
}

// Initiate this addon within the plugin
$GLOBALS['eventon_sy'] = EVOSY();
