<?php
/*
 * Plugin Name: EventON - Action User
 * Plugin URI: http://www.myeventon.com/
 * Description: Powerful eventON user control and event submission manager
 * Author: Ashan Jay
 * Version: 2.4.1
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 6.0
 * Tested up to: 6.2
 */

class eventon_au{
	
	public $version='2.4.1';
	public $eventon_version = '4.3';
	public $name = 'ActionUser';

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
				add_action( 'init', array( $this, 'init' ), 0 );
				add_action( 'eventon_register_taxonomy', array( $this, 'create_user_tax' ) ,10);		
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
	        $this->addon_data['ID'] = 'EVOAU';

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';
	        
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE action user
		function init(){

			include_once( 'templates/class-templates.php' );			
			include_once( 'includes/class-functions.php' );			
			include_once( 'includes/form/class-form.php' );			
			include_once( 'includes/class-event_manager.php' );			
			include_once( 'includes/class-frontend.php' );			
			include_once( 'includes/shortcode.php' );
			
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/ajax.php' );
			}

			$this->frontend = new evoau_frontend();
			$this->manager = new EVOAU_Event_Manager();
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				include_once( 'includes/admin/class-settings.php' );
				include_once( 'includes/admin/class-admin.php' );
				include_once( 'includes/admin/class-admin-ajax.php' );
				$this->admin = new evoau_admin();	

				// settings link in plugins page
				add_filter("plugin_action_links_".$this->plugin_slug, array($this,'eventon_plugin_links' ));
			}			
		}
	
	// TAXONOMY 
	// event_users
		function create_user_tax(){
			register_taxonomy( 'event_users', 
				apply_filters( 'eventon_taxonomy_objects_event_users', array('ajde_events') ),
				apply_filters( 'eventon_taxonomy_args_event_users', array(
					'hierarchical' => true, 
					'label' => 'EvenON Users', 
					'show_ui' => false,
					'query_var' => true,
					'capabilities'			=> array(
						'manage_terms' 		=> 'manage_eventon_terms',
						'edit_terms' 		=> 'edit_eventon_terms',
						'delete_terms' 		=> 'delete_eventon_terms',
						'assign_terms' 		=> 'assign_eventon_terms',
					),
					'rewrite' => array( 'slug' => 'event-user' ) 
				)) 
			);
		}
	
	// SUPPORTIVE
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=action_user">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}	
		public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - '), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
}


function EVOAU(){	return eventon_au::instance(); }

// Initiate this addon within the plugin
$GLOBALS['eventon_au'] = EVOAU();


?>