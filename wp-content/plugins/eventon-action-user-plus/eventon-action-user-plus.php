<?php
/*
 * Plugin Name: EventON - Action User Plus
 * Plugin URI: http://www.myeventon.com/addons/action-user-plus
 * Description: Extends actionUser addon even further
 * Author: Ashan Jay
 * Version: 1.1.2
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.0
 * Tested up to: 5.8
 */

class eventon_aup{
	
	public $version='1.1.2';
	public $eventon_version = '3.0';
	public $evoau_min_version = '2.2.7';
	public $name = 'Action User Plus';

	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	
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
				if(!class_exists('WooCommerce')){
					add_action('admin_notices', array($this, '_wc_eventon_warning'));
				}elseif(!class_exists('eventon_au')){
					add_action('admin_notices', array($this, '_au_eventon_warning'));
				}else{

					global $eventon_au;

					// check for actionUser version compatibility
					if(version_compare($eventon_au->version, $this->evoau_min_version ) >= 0){
						add_action( 'init', array( $this, 'init' ), 0 );
						add_action( 'eventon_register_taxonomy', array( $this, 'create_user_tax' ) ,10);	
						// settings link in plugins page
						add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
					}else{
						add_action('admin_notices', array($this, '_au_version_warning'));
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
	        $this->addon_data['name'] = 'Action User Plus';

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	        
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE 
		function init(){
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}

			include_once( 'includes/class-functions.php' );			
			include_once( 'includes/class-frontend.php' );			
			include_once( 'includes/class-shortcode.php' );
			
			$this->frontend = new evoaup_frontend();
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				include_once( 'includes/admin/class-admin.php' );
				$this->admin = new evoaup_admin();	
			}			
		}
	
	// TAXONOMY 
	// event_users
		function create_user_tax(){
			register_taxonomy( 'event_user_roles', 
				apply_filters( 'eventon_taxonomy_objects_event_user_roles', array('ajde_events') ),
				apply_filters( 'eventon_taxonomy_args_event_user_roles', array(
					'hierarchical' => true, 
					'label' => 'EvenON User Roles', 
					'show_ui' => false,
					'query_var' => true,
					'capabilities'			=> array(
						'manage_terms' 		=> 'manage_eventon_terms',
						'edit_terms' 		=> 'edit_eventon_terms',
						'delete_terms' 		=> 'delete_eventon_terms',
						'assign_terms' 		=> 'assign_eventon_terms',
					),
					'rewrite' => array( 'slug' => 'event-user-role' ) 
				)) 
			);
		}
	
	// ACTIVATION
		// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}

	// Secondary
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=action_user#evoAU6">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
		function _wc_eventon_warning(){
	        ?>
	        <div class="message error"><p><?php _e('Eventon ActionUser Plus require woocommerce plugin. Please install Woocommerce', 'eventon'); ?></p></div>
	        <?php
	    }function _au_eventon_warning(){
	        ?>
	        <div class="message error"><p><?php _e('Eventon ActionUser Plus require ActionUser plugin. Please install <a href="http://www.myeventon.com/addons/action-user/" target=_"blank">ActionUser</a>', 'eventon'); ?></p></div>
	        <?php
	    }function _au_version_warning(){
	        ?>
	        <div class="message error"><p><?php printf(__('EventON ActionUser Plus require ActionUser version %s or above to fully function! Until a compatible ActionUser version is installed ActionUser Plus addon will be inactive.','eventon'), $this->evoau_min_version); ?></p></div>
	        <?php
	    }	
}

// Initiate this addon within the plugin
$GLOBALS['eventon_aup'] = new eventon_aup();
function EVOAUP(){
	global $eventon_aup;
	return $eventon_aup;
}
?>