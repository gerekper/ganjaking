<?php
/*
 Plugin Name: EventON - RSS Feed
 Plugin URI: http://www.myeventon.com/
 Description: Create RSS feed of all events
 Author: Ashan Jay
 Version: 0.3
 Author URI: http://www.ashanjay.com/
 Requires at least: 3.8
 Tested up to: 4.2.2

 */

 class eventon_rss{
 	public $version='0.3';
	public $eventon_version = '2.3';
	public $name = 'RSS Feed';

	public $rss_slug;
	public $print_scripts_on = false;

 	// Construct
	public function __construct(){
		$this->super_init();
		add_action('plugins_loaded', array($this, 'plugin_init'));
	}

	function plugin_init(){
		if(class_exists('EventON')){
			include_once( 'includes/admin/class-admin_check.php' );
			$this->check = new addon_check($this->addon_data);
			$check = $this->check->initial_check();
			
			if($check){
				// initiate eventon addon
				$this->addon = new evo_addon($this->addon_data);				
				add_action( 'init', array( $this, 'init' ), 0 );
			}
		}else{
			add_action('admin_notices', array($this, '_eventon_warning'));
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
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE please
		function init(){				
			// Activation
			$this->activate();		
			
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once($this->plugin_path.'/includes/class-shortcodes.php');
			include_once($this->plugin_path.'/includes/class-frontend.php');

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				$this->addon->updater();
				include_once($this->plugin_path.'/includes/admin/admin-init.php');
			}

			//frontend includes
			if ( ! is_admin() || defined('DOING_AJAX') ){}	
	
		}
	
	
 	// ACTIVATION			
		function activate(){
			// add actionUser addon to eventon addons list
			$this->addon->activate();
		}		
	
		// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}
		
 }

// Initiate this addon within the plugin
$GLOBALS['eventon_rss'] = new eventon_rss();