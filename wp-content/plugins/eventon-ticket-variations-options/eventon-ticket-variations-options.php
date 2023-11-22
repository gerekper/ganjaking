<?php
/*
 * Plugin Name: EventON - Ticket Variations & Options
 * Plugin URI: http://www.myeventon.com/addons/ticket-variations-options
 * Description: Extend tickets with variations and options
 * Author: Ashan Jay
 * Version: 1.1.3
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 6.0
 * Tested up to: 6.4.1
 *  
 * Text Domain: evovo
 * Domain Path: /lang/
 *
 */

class evovo{
	
	public $version='1.1.3';
	public $eventon_version = '4.5';
	public $evotx_version = '2.2.2';
	public $name = 'Ticket Variations & Options';
	public $id = 'EVOVO';

	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path, $addon, $frontend, $assets_path;
	
	// Instanace
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

	public function __construct(){
		$this->super_init();
		add_action('plugins_loaded', array($this, 'plugin_init'), 12);
	}

	public function plugin_init(){		
		// check if eventon exists with addon class
		if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
			add_action('admin_notices', array($this, 'notice'));
			return false;			
		}			
		
		$this->addon = new evo_addons($this->addon_data);

		if($this->addon->evo_version_check()){
			if(!class_exists('WooCommerce')){
				add_action('admin_notices', array($this, '_wc_eventon_warning'));
			}elseif(!class_exists('evotx')){
				add_action('admin_notices', array($this, '_tx_eventon_warning'));
			}else{

				// if event tickets environment is not setup @since 1.1.2
				if( !EVOTX()->good ){
					add_action('admin_notices', function(){
						?><div class="message error"><p><?php printf(__('Eventon %s can not run, tickets addon is not fully initiated.', 'eventon'), $this->name); ?></p></div><?php
					});
					return;
				}

				if(version_compare(EVOTX()->version , $this->evotx_version)>=0){
					add_action( 'init', array( $this, 'init' ), 0 );
					//add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
				}else{
					add_action('admin_notices', array($this, '_tx_version_warning'));
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
			
			$this->textdomain();

			include_once( 'includes/class-functions.php' );	
			include_once( 'includes/class-frontend.php' );	
			include_once( 'includes/class-event-variations_options.php' );	
			include_once( 'includes/class-integration-seats.php' );	
			include_once( 'includes/class-integration-booking.php' );	
			include_once( 'includes/class-integration-tickets.php' );	
			include_once( 'includes/class-integration-qr.php' );	
			
			
			$this->frontend = new evovo_frontend();
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			if ( is_admin() ){
				include_once( 'includes/admin/class-lang.php' );
				include_once( 'includes/admin/class-admin.php' );
				$this->admin = new evovo_admin();	
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
				load_plugin_textdomain( 'evovo', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

	// Secondary
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_vo">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		function _wc_eventon_warning(){
	        ?><div class="message error"><p><?php printf(__('Eventon %s need Woocommerce plugin to function properly. Please install woocommerce', 'eventon'), $this->name); ?></p></div><?php
	    }	
	    public function _tx_eventon_warning(){
			?><div class="message error"><p><?php printf(__('Eventon %s require Event Tickets addon to function properly. Please install Event Tickets addon!', 'eventon'), $this->name); ?></p></div><?php
		}
		public function _tx_version_warning(){
			?><div class="message error"><p><?php printf(__('Eventon %s require Event Tickets addon version %s or higher to fully function please update tickets addon!', 'eventon'), $this->name, $this->evotx_version); ?></p></div><?php
		}
	    public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - ','eventon'), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
	   
}

function EVOVO(){
	return evovo::instance();
}

// Initiate this addon within the plugin
$GLOBALS['evovo'] = EVOVO();

?>