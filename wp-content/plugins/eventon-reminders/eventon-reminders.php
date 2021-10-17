<?php
/*
 * Plugin Name: EventON - Reminders
 * Plugin URI: http://www.myeventon.com/addons/reminders
 * Description: Send automated event reminders to RSVP guests or Ticket customers
 * Author: Ashan Jay
 * Version: 0.4
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 4.0
 * Tested up to: 4.9.5
 */

class eventon_reminders{
	
	public $version='0.4';
	public $eventon_version = '2.6.9';
	public $EVORS_version = '2.5.15';
	public $EVOTX_version = '1.6.7';
	public $name = 'Reminders';
	public $addon_id = 'EVORM';
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	
	// Instanace
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

	// Constructor
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
				// neight rsvp nor tickets exists
				if(!class_exists('EventON_rsvp') && !class_exists('evotx')){
					add_action('admin_notices', array($this, '_eventon_warning'));
				}else{
					$initiate_addon = false;
					// if rsvp exists check versions
					if(class_exists('EventON_rsvp') ){
						if( version_compare(EVORS()->version , $this->EVORS_version)>=0){
							$initiate_addon = true;
						}else{
							add_action('admin_notices', array($this, '_version_warning_rs'));
						}
					}	

					// if tickets exists check versions
					if(class_exists('evotx') ){
						if( version_compare(EVOTX()->version , $this->EVOTX_version)>=0){
							$initiate_addon = true;
						}else{
							add_action('admin_notices', array($this, '_version_warning_tx'));
						}
					}

					if( $initiate_addon ){
						add_action( 'init', array( $this, 'init' ), 0 );
						add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
					}
				}
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

	// INITIATE action user
		function init(){
				
			include_once( 'includes/class-functions.php' );	
			include_once( 'includes/class-data_log.php' );		
			include_once( 'includes/class-cron.php' );		
			
			$this->cron = new EVORM_Cron();
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			if ( is_admin() ){
				include_once( 'includes/admin/class-admin.php' );
				$this->admin = new evorm_admin();	
			}			
		}

	// all reminders
		function get_reminders(){
			return apply_filters('evorm_reminders', array(
				'pre_1'=>array(
					'var'=>'evorm_pre_1',
					'label'=>__('Enable before event start reminder email #1','evorm'),
				),'pre_2'=>array(
					'var'=>'evorm_pre_2',
					'label'=>__('Enable before event start reminder email #2','evorm'),
				),'post_1'=>array(
					'var'=>'evorm_post_1',
					'label'=>__('Enable after event end reminder email #1','evorm'),
				),'post_2'=>array(
					'var'=>'evorm_post_2',
					'label'=>__('Enable after event end reminder email #2','evorm'),
				)
			));
		}
			
	// ACTIVATION
		// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}

	// Secondary
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_rs#evors_reminders">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		public function _eventon_warning(){
			?><div class="message error"><p><?php printf(__('EventON %s require either EventON RSVP or EventON Tickets addon to function properly. Please install either of those addons!', 'evorm'), $this->name); ?></p></div><?php
		}
		public function _version_warning_rs(){
			?><div class="message error"><p><?php printf(__('EventON %s require EventON RSVP addon version %s or higher to fully function please update RSVP addon!', 'evorm'), $this->name, $this->EVORS_version); ?></p></div><?php
		}
		public function _version_warning_tx(){
			?><div class="message error"><p><?php printf(__('EventON %s require EventON Tickets addon version %s or higher to fully function please update Tickets addon!', 'evorm'), $this->name, $this->EVOTX_version); ?></p></div><?php
		}
	    public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - ','evorm'), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
	   
}

function EVORM(){
	return eventon_reminders::instance();
}

// Initiate this addon within the plugin
$GLOBALS['eventon_reminders'] = EVORM();


?>