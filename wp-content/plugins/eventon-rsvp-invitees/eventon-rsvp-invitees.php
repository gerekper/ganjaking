<?php
/*
 * Plugin Name: EventON - RSVP Events Invitees
 * Plugin URI: http://www.myeventon.com/addons/rsvp-invitees
 * Description: Send event RSVPs only to invitees
 * Author: Ashan Jay
 * Version: 0.5
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.0
 * Tested up to: 5.8
 *  
 * Text Domain: evorsi
 * Domain Path: /lang/
 *
 */

class evorsi{
	
	public $version='0.5';
	public $eventon_version = '3.1';
	public $evors_version = '2.7.3';
	public $name = 'RSVP Invitees';
	public $id = 'EVORSI';

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
	// Construct
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
				if(!class_exists('EventON_rsvp')){
					add_action('admin_notices', array($this, '_rs_eventon_warning'));
				}else{
					if(version_compare(EVORS()->version , $this->evors_version)>=0){
						add_action( 'init', array( $this, 'init' ), 10 );
					}else{
						add_action('admin_notices', array($this, '_rs_version_warning'));
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

	// INITIATE
		function init(){
			
			$this->textdomain();

			include_once( 'includes/class-frontend.php' );	
			include_once( 'includes/class-template_views.php' );	
			include_once( 'includes/class-event_invitees.php' );	
			include_once( 'includes/class-invitee.php' );	
			
			if ( defined('DOING_AJAX') ){
				//include_once( 'includes/class-ajax.php' );
			}

			$this->frontend = new evorsi_frontend();
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));
			
			if ( is_admin() ){
				include_once( 'includes/class-invitee_manager.php' );	
				include_once( 'includes/class-admin.php' );
				$this->admin = new evorsi_admin();	
				$this->manager = new EVORSI_Manager();	
			}			
		}


	// ACTIVATION
		// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}

	// Localization
		function textdomain(){
			if(is_admin())
				load_plugin_textdomain( 'evorsi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

	// Secondary
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_rs">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		  public function _rs_eventon_warning(){
			?><div class="message error"><p><?php printf(__('Eventon %s require Event RSVP addon to function properly. Please install Event RSVP addon!', 'eventon'), $this->name); ?></p></div><?php
		}
		public function _rs_version_warning(){
			?><div class="message error"><p><?php printf(__('Eventon %s require Event RSVP addon version %s or higher to fully function please update tickets addon!', 'eventon'), $this->name, $this->evotx_version); ?></p></div><?php
		}
	    public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - ','eventon'), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
	   
}

function EVORSI(){
	return evorsi::instance();
}

// Initiate this addon within the plugin
$GLOBALS['evorsi'] = EVORSI();

?>