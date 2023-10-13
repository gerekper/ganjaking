<?php
/*
 * Plugin Name: EventON - Event Tickets
 * Plugin URI: http://www.myeventon.com/
 * Description: Sell Event Tickets using Woocommerce
 * Author: Ashan Jay
 * Version: 2.2.3
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 6.0
 * Tested up to: 6.2.2
 * WC tested up to: 7.9
 * WC requires at least: 7.0
 *
 * Text Domain: evotx
 * Domain Path: /lang/
 *
 * @package event ticket
 * @Author AJDE
 */


if ( ! defined( 'ABSPATH' ) ) exit;

//Event tickets main class
if ( ! class_exists( 'evotx' ) ):
class evotx{	
	public $version='2.2.3';
	public $eventon_version = '4.4.2';
	public $wc_version = '7.9';
	public $wc_max_version = '7.0';
	public $name = 'Tickets';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path, $assets_path;
	public $functions, $email, $frontend, $evotx_tix;
	private $urls, $addon;
	public $template_url ;
	public $good = false;

	public $evotx_opt;
	public $opt2;
	
	public $evotx_args;
	
	// Construct
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		public function __construct(){
			$this->super_init();
			add_action('plugins_loaded', array($this, 'plugin_init'), 10);
		}

		public function plugin_init(){			
			// check if eventon exists with addon class
			if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
				add_action('admin_notices', array($this, 'notice'));
				return false;			
			}			
			
			$this->addon = new evo_addons($this->addon_data);

			if($this->addon->evo_version_check()){
				// check if woocommerce exist
				if(!class_exists('WooCommerce')){
					add_action('admin_notices', array($this, '_wc_eventon_warning'));
				}else{

					// check with compatibility WC version requirement
					if( version_compare(WC()->version, $this->wc_version  ) >= 0 ){

						add_action( 'init', array( $this, 'init' ), 0 );
						$this->good = true;
						
						$this->load_plugin_textdomain();
						// settings link in plugins page
						add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
					}else{
						add_action('admin_notices', array($this, '_wc_version_warning'));
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
	        $this->assets_path = str_replace(array('http:','https:'), '', $this->addon_data['plugin_url']) . '/assets/';
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE please
		function init(){		
			
			$this->evotx_opt = get_option('evcal_options_evcal_tx');
			$this->opt2 = get_option('evcal_options_evcal_2');
			EVO()->cal->load_more('evcal_tx');
			
			$this->includes();

			// Deactivation
			register_activation_hook( __FILE__, array($this,'activate'));
			register_deactivation_hook( __FILE__, array($this,'deactivate'));
		}
	
	/** Include required core files. */
		function includes(){

			//return false;

			// both front and admin
			include_once( $this->plugin_path . '/includes/class-templates.php' );
			include_once( $this->plugin_path . '/includes/class-helper.php' );
			include_once( $this->plugin_path . '/includes/class-event_ticket.php' );
			include_once( $this->plugin_path . '/includes/class-post-types.php' );
			include_once( $this->plugin_path . '/includes/class-email.php' );
			include_once( $this->plugin_path . '/includes/class-evo-tix.php' );		
			include_once( $this->plugin_path . '/includes/class-evo-tix-cpt.php' );		
			include_once( $this->plugin_path . '/includes/class-attendees.php' );	
					
					
			include_once( $this->plugin_path . '/includes/class-integration-general.php' );
			include_once( $this->plugin_path . '/includes/class-integration-actionuser.php' );
			include_once( $this->plugin_path . '/includes/class-integration-countdown.php' );
			include_once( $this->plugin_path . '/includes/class-integration-virtualevents.php' );
			include_once( $this->plugin_path . '/includes/class-integration-webhooks.php' );
			include_once( $this->plugin_path . '/includes/class-appearance.php' );

			include_once( $this->plugin_path . '/includes/class-ajax.php' );
			
			include_once($this->plugin_path . '/includes/class-functions.php');
			$this->functions = new evotx_functions();
			$this->email = new evotx_email();

			if ( is_admin() ){				
				include_once( $this->plugin_path . '/includes/admin/class-admin-ajax.php' );
				include_once( $this->plugin_path . '/includes/admin/class-lang.php' );				
				include_once( $this->plugin_path . '/includes/admin/class-admin.php' );				
			}

			include_once( $this->plugin_path . '/includes/class-frontend.php' );
			$this->frontend = new evotx_front();

			if ( ! is_admin() || defined('DOING_AJAX') ){
				
			}
			if ( defined('DOING_AJAX') ){
				
			}

			include_once( $this->plugin_path . '/includes/class-integration-woocommerce.php' );

		}

		function evotx_tix(){
			include_once( $this->plugin_path . '/includes/class-evo-tix.php' );	
			$this->evotx_tix = new evotx_tix();
			return $this->evotx_tix;
		}	
	
	// Load localisation files
		function load_plugin_textdomain(){		
			
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'evotx' );
			
			if ( is_admin() ) {
				load_textdomain( 'evotx', WP_LANG_DIR . "/eventon-tickets/lang/evotx-".$locale.".mo" );	
				load_plugin_textdomain( 'evotx', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
			}		
		}

	// SECONDARY FUNCTIONS			
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_tx">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		function _wc_eventon_warning(){
	        ?>
	        <div class="message error"><p><?php _e('Eventon Tickets needs WooCommerce plugin to function properly. Please install WooCommerce', 'evotx'); ?></p></div>
	        <?php
	    }
	    function _wc_version_warning(){
	        ?>
	        <div class="message error"><p><?php printf(__('Tickets addon require WooCommerce version %s or above to fully function! Until a compatible WooCommerce version is installed tickets addon will be inactive.','evotx'), $this->wc_version); ?></p></div>
	        <?php
	    }	
	    public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}   
	
		// activate and deactive addon
		public function activate(){
			do_action('evotx_activate');
		}
		function deactivate(){
			do_action('evotx_deactivate');
			$this->addon->remove_addon();
		}	

		function check_tx_prop($field){				
			return (!empty($this->evotx_opt[$field]) && $this->evotx_opt[$field]=='yes')? 
				true: false;
		}
		function get_tx_prop($field){
			return (!empty($this->evotx_opt[$field]))? 
				$this->evotx_opt[$field]: false;
		}
}

endif;


// Initiate this addon within the plugin
function EVOTX(){ return evotx::instance(); }
$GLOBALS['evotx'] = EVOTX();

?>