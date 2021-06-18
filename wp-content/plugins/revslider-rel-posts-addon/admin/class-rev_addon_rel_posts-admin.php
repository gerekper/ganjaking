<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_rel_posts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rev_addon_rel_posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_rel_posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-rel-posts-addon-admin.js', array( 'jquery','revbuilder-admin'), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_rel_posts_addon', $this->get_var() );
		}
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-rel-posts-addon') {
		if($slug == 'revslider-rel-posts-addon'){
			return array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'enabled' => get_option('revslider_rel_posts_enabled'),
				'bricks' => array(
					'active'  =>  __('Active','revslider-rel-posts-addon'),
					'rel-posts'  =>  __('rel-posts','revslider-rel-posts-addon'),
					'settings' =>  __('Settings','revslider-rel-posts-addon'),					
					'configuration' =>  __('Configuration','revslider-rel-posts-addon'),
					'rel-postscontent' =>  __('Content from','revslider-rel-posts-addon'),
					'slider' => __('Choose Slider','revslider-rel-posts-addon'),
					'page' => __('Page','revslider-rel-posts-addon'),
					'pagetitle' => __('Page Title','revslider-rel-posts-addon'),
					'save' => __('Save Configration','revslider-rel-posts-addon'),
					'entersometitle' => __('Enter Some Title','revslider-rel-posts-addon'),
					'loadvalues' => __('Loading rel-posts Add-On Configration','revslider-rel-posts-addon'),
					'savevalues' => __('Saving rel-posts Add-On Configration','revslider-rel-posts-addon'),
					'category' => __('Category Only','revslider-rel-posts-addon'),
					'taxonomyonly' => __('Taxon. Only','revslider-rel-posts-addon'),
					'taxonomy' => __('Taxonomy','revslider-rel-posts-addon'),
					'autoadd' => __('Default Slider','revslider-rel-posts-addon'),					
					'position' => __('Slider Position','revslider-rel-posts-addon'),
					'above' => __('Above','revslider-rel-posts-addon'),
					'below' => __('Below','revslider-rel-posts-addon'),								
					'caching' => __('Caching','revslider-rel-posts-addon'),								
					'searchin' => __('Start Search','revslider-rel-posts-addon'),								
					'fillwith' => __('Fill with','revslider-rel-posts-addon'),						
					'numofposts' => __('Nr. of Posts','revslider-rel-posts-addon'),
					'nothing' => __('Nothing','revslider-rel-posts-addon'),
					'categories' => __('Categories','revslider-rel-posts-addon'),
					'tags' => __('Tags','revslider-rel-posts-addon'),
					'format' => __('Format','revslider-rel-posts-addon'),
					'randomposts' => __('Random Posts','revslider-rel-posts-addon'),
					'recentposts' => __('Recent Posts','revslider-rel-posts-addon'),
					'mostcommentedposts' => __('Most commented Posts','revslider-rel-posts-addon')
				)
			);
		}
		else{
			return $var;
		}
	}

	/**
	 * Saves Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	public function save_rel_posts() {
		// Verify that the incoming request is coming with the security nonce
		if(isset($_REQUEST['data']['revslider_rel_posts_form'])){
			update_option( "revslider_rel_posts_addon", $_REQUEST['data']['revslider_rel_posts_form'] );
			return 1;
		}
		else{
			return 0;
		}
		
	}


	/**
	 * Load Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	public function values_rel_posts() {
		$revslider_rel_posts_addon_values = array();
		parse_str(get_option('revslider_rel_posts_addon'), $revslider_rel_posts_addon_values);
		$return = json_encode($revslider_rel_posts_addon_values);
		return array("message" => "Data found", "data"=>$return);
	}

	/**
	 * Change Enable Status of this Add-On
	 *
	 * @since    1.0.0
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_rel_posts_enabled", $enabled );	
	}


	/**
	 * Perform Ajax Calls from RevSlider Core
	 *
	 * @since    1.0.0
	 */
	public function do_ajax($return,$action) {
		switch ($action) {
			case 'wp_ajax_enable_revslider-rel-posts-addon':
				$this->change_addon_status( 1 );
				return  __('rel-posts AddOn enabled', 'revslider-rel-posts-addon');
				break;
			
			case 'wp_ajax_disable_revslider-rel-posts-addon':
				$this->change_addon_status( 0 );
				return  __('rel-posts AddOn disabled', 'revslider-rel-posts-addon');
				break;

			case 'wp_ajax_get_values_revslider-rel-posts-addon':
				$return = $this->values_rel_posts();
				if(empty($return)) $return = true;
				return $return;
				break;
			case 'wp_ajax_save_values_revslider-rel-posts-addon':
				$return = $this->save_rel_posts();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-rel-posts-addon');
				} 
				else {
					return  __('rel-posts Configuration saved', 'revslider-rel-posts-addon');	
				}
				break;
			default:
				return $return;
				break;
		}
	}

	/**
	 * Select Taxonomy Values Dropdown
	 *
	 * @since    1.0.0
	 */
	public function select_taxonomy ($taxonomy,$taxonomy_default = ""){
		if (is_array($taxonomy_default)) {
	          foreach ($taxonomy_default as $key => $post_term) {
	              $taxonomy = str_replace(' value="' . $post_term . '"', ' value="' . $post_term . '" selected="selected"', $taxonomy);
	          }
	    } else {
	          $taxonomy = str_replace(' value="' . $taxonomy_default . '"', ' value="' . $taxonomy_default . '" selected="selected"', $taxonomy);
	    }
	    return $taxonomy;
	}
}
