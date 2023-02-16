<?php
/**
 * Plugin Name: EventON - Dynamic Pricing
 * Plugin URI: http://www.myeventon.com/addons/dynamic-pricing
 * Description: Dynamic prices for event tickets
 * Author: Ashan Jay
 * Version: 0.6
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.0
 * Tested up to: 5.5
 * @package EventON
 * @category Core
 * @author AJDE
 */

class eventon_dp{
	
	public $version='0.6';
	public $eventon_version = '2.9';
	public $evotx_version = '1.8';
	public $name = 'dynamic pricing';

	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	
	// Instance
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

					global $evotx;

					if(version_compare($evotx->version , $this->evotx_version)>=0){
						add_action( 'init', array( $this, 'init' ), 0 );
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

	// INITIATE 
		function init(){

			include_once( 'includes/class-functions.php' );	
			include_once( 'includes/class-frontend.php' );	
			
			$this->front = new evodp_frontend();
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			if ( is_admin() ){
				include_once( 'includes/admin/class-admin.php' );
				$this->admin = new evodp_admin();	
			}			
		}
			
	// ACTIVATION
		// Deactivate addon
		function deactivate(){ $this->addon->remove_addon();}

	// Secondary
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

// Initiate this addon within the plugin
function EVODP() { return eventon_dp::instance(); }
$GLOBALS['evodp'] = EVODP();

?>