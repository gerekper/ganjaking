<?php
/*
 Plugin Name: EventON - Event Search
 Plugin URI: http://www.myeventon.com/
 Description: Search eventON calendar events at ease
 Author: Ashan Jay
 Version: 0.7
 Author URI: http://www.ashanjay.com/
 Requires at least: 4.0
 Tested up to: 4.5.3
 */

class eventon_sr{
	
	public $version='0.7';
	public $eventon_version = '2.4.4';
	public $name = 'Search Events';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
	// Construct
		public function __construct(){
			$this->super_init();
			add_action('plugins_loaded', array($this, 'plugin_init'));
		}

	// check for secondary required addons
		function plugin_init(){
			include_once( 'includes/admin/class-admin_check.php' );
			$this->check = new addon_check($this->addon_data);
			$check = $this->check->initial_check();
			
			if($check){
				$this->addon = new evo_addon($this->addon_data);

				add_action( 'init', array( $this, 'init' ), 0 );
				// settings link in plugins page
				add_filter("plugin_action_links_".$this->plugin_slug, array($this,'plugin_links' ));	
			}
		}
		function _eventon_warning(){
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
			// Activation
			$this->addon->activate();	
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-shortcode.php' );		
			include_once( 'includes/class-ajax.php' );		
			include_once( 'includes/class-frontend.php' );
			$this->frontend = new evosr_front();

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				$this->addon->updater();
				include_once( 'includes/admin/class-admin-init.php' );	
			}
		}

	// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}	
	// plugin link in settings
		function plugin_links($links){
			$settings_link = '<a href="admin.php?page=eventon&tab=evcal_1#eventon_search">'.__('Settings','eventon').'</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
}

// Initiate this addon within the plugin
$GLOBALS['eventon_sr'] = new eventon_sr();	