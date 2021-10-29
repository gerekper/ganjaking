<?php
/*
 Plugin Name: EventON - Speakers & Schedule
 Plugin URI: http://www.myeventon.com/addons/event-lists-items
 Description: Set up speakers for event with schedule within events
 Author: Ashan Jay
 Version: 1.0.3
 Author URI: http://www.ashanjay.com/
 Requires at least: 4.0
 Tested up to: 5.5.1
 */

class EVO_speak{
	
	public $version='1.0.3';
	public $eventon_version = '3.0';
	public $name = 'Event Speakers & Schedule';
	public $id = 'EVOSS';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
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
				$this->helper = new evo_helper();
				$this->opt2 = get_option('evcal_options_evcal_2');
				add_action( 'init', array( $this, 'init' ), 0 );
			}	
		}	
	
	// Eventon missing
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

	        // guide file
	        	$this->addon_data['guide_file'] = ( file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
								$this->addon_data['plugin_url'].'/guide.php':null;

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	        
		}

	// INITIATE please
		function init(){				
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-functions.php' );
			include_once( 'includes/class-integration_lists_items.php' );
			$this->frontend = new evoss_front();
			$this->functions = new evoss_functions();
			
			if ( defined('DOING_AJAX') ){
				include_once( 'includes/class-ajax.php' );
			}
			if ( is_admin() ){
				include_once( 'includes/admin/admin-init.php' );
			}

			// register tax and post types
			add_action('eventon_register_taxonomy', array($this, 'register_tax'), 5);

			//add_filter( 'template_include', array( $this, 'template_loader' ) , 99);
			add_filter( 'evo_template_loader_file', array( $this, 'file' ) , 10,1);
			add_filter( 'eventon_template_paths', array( $this, 'path' ) , 10,1);
		}

	// create new post type
		function register_tax(){
			$__capabilities = array(
				'manage_terms' 		=> 'manage_eventon_terms',
				'edit_terms' 		=> 'edit_eventon_terms',
				'delete_terms' 		=> 'delete_eventon_terms',
				'assign_terms' 		=> 'assign_eventon_terms',
			);
			$labels = array(
				'name'              => _x( 'Event Speakers', 'eventon' ),
				'singular_name'     => _x( 'Event Speaker', 'eventon' ),
				'search_items'      => __( 'Search Event Speakers', 'eventon' ),
				'all_items'         => __( 'All Event Speakers', 'eventon' ),
				'parent_item'       => __( 'Parent Event Speaker', 'eventon' ),
				'parent_item_colon' => __( 'Parent Event Speaker:', 'eventon' ),
				'edit_item'         => __( 'Edit Event Speaker', 'eventon' ),
				'update_item'       => __( 'Update Event Speaker', 'eventon' ),
				'add_new_item'      => __( 'Add New Event Speaker', 'eventon' ),
				'new_item_name'     => __( 'New Event Speaker Name', 'eventon' ),
				'menu_name'         => __( 'Event Speaker', 'eventon' ),
			);
			register_taxonomy( 'event_speaker', 
				apply_filters( 'eventon_taxonomy_objects_event_speaker', array('ajde_events') ),
				apply_filters( 'eventon_taxonomy_args_event_speaker', array(
					'hierarchical' => false, 
					//'label' => __('Event Speaker','eventon'), 
					'labels' => $labels, 
					'show_ui' => true,
					'show_in_menu'=>true,
					'show_in_nav_menu'=>true,
					'show_tagcloud'=>false,
					'show_admin_column'=>false,
					'show_in_quick_edit'         => false,
    				'meta_box_cb'                => false,
					'query_var' => true,
					'capabilities'	=> $__capabilities,
					'rewrite' => apply_filters('evotax_slug_spe', array( 'slug' => 'event-speaker' ) )
				)) 
			);
		}
	// template loading
		function file($file){
			if( is_tax(array('event_speaker'))){
				$file 	= 'taxonomy-event_speaker.php';
			}
			return $file;
		}
		function path($paths){
			if( is_tax(array('event_speaker'))){
				$paths[] 	= $this->addon_data['plugin_path'] . '/templates/';
			}
			return $paths;
		}

	// SECONDARY FUNCTIONS	
		function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_ss">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
		// Deactivate addon
			function deactivate(){
				$this->addon->remove_addon();
			}
		// duplicate language function to make it easy on the eye
			function lang($variable, $default_text, $lang=''){
				return eventon_get_custom_language($this->opt2, $variable, $default_text, $lang);
			}
}
// Initiate this addon within the plugin
function EVOSS(){ return EVO_speak::instance(); }
$GLOBALS['evo_speak'] = EVOSS();
$GLOBALS['evo_speak'] = new EVO_speak();
?>