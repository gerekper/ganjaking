<?php
/*
Plugin Name: Tags add-on for UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description:Allow users to add tags to their profiles easily 
Version: 1.2
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/
if(!defined('ABSPATH')) {exit;}

define('userpro_tags_url',plugin_dir_url(__FILE__ ));
define('userpro_tags_path',plugin_dir_path(__FILE__ ));

if(!class_exists('userpro_tags_setup') ) :

class userpro_tags_setup {
private static $_instance;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
public function __construct() {
$this->includes_file();
global $userpro;


add_action('init',array($this,'userpro_tags_init'));
add_action('wp_enqueue_scripts',array($this,'userpro_tags_scripts'));

}
	/* init */

//hook into the init action and call create_topics_nonhierarchical_taxonomy when it fires


	function userpro_tags_init() {
		load_plugin_textdomain('userpro-tags', false, dirname(plugin_basename(__FILE__)) . '/languages');
		
	}
	function userpro_tags_scripts(){
		wp_enqueue_script( 'tags-scripts', userpro_tags_url. 'scripts/tags-scripts.js', array('userpro_min'),'', true );
		$userpro_limit_tags = userpro_tags_get_option('limit_tags' );
		wp_localize_script('tags-scripts', 'userpro_tags_script_data', array( 'userpro_limit_tags' => $userpro_limit_tags ) );
	}
	public function includes_file() {

	/* functions */
	foreach (glob(userpro_tags_path . 'functions/*.php') as $filename) { require_once $filename; }
	
	/* administration */
	if (is_admin()){
	foreach (glob(userpro_tags_path . 'admin/*.php') as $filename) { include $filename; }
	global $userpro;
	if(isset($userpro)){
		foreach (glob(userpro_tags_path . 'updates/*.php') as $filename) { include $filename; }	
	}else{
		add_action('admin_notices',array($this ,'UPt_userpro_activation_notices'));
		return 0;
	}
	
	}
}
	

function UPt_userpro_activation_notices(){
		echo '<div class="error" role="alert"><p>Attention: User-Pro users Tag requires User-Pro to be installed and activated.</p></div>';
		deactivate_plugins( plugin_basename( __FILE__ ) );
		return 0;
	}
}
endif;

$UPt = userpro_tags_setup::instance();
?>