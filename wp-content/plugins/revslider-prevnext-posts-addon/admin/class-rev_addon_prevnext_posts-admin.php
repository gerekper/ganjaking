<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_prevnext_posts
 * @subpackage Rev_addon_prevnext_posts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rev_addon_prevnext_posts
 * @subpackage Rev_addon_prevnext_posts/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_prevnext_posts_Admin {

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
		 * defined in Rev_addon_prevnext_posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_prevnext_posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if(isset($_GET["page"]) && $_GET["page"]=="revslider"){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-prevnext-posts-addon-admin.js', array( 'jquery','revbuilder-admin' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_prevnext_posts_addon', $this->get_var() );
		}
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-prevnext-posts-addon') {
		if($slug == 'revslider-prevnext-posts-addon'){
			return array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'enabled' => get_option('revslider_prevnext_posts_enabled'),
				'bricks' => array(
					'active'  =>  __('Active','revslider-prevnext-posts-addon'),
					'prevnext-posts'  =>  __('prevnext-posts','revslider-prevnext-posts-addon'),
					'settings' =>  __('Settings','revslider-prevnext-posts-addon'),					
					'configuration' =>  __('Configuration','revslider-prevnext-posts-addon'),
					'prevnext-postscontent' =>  __('Content from','revslider-prevnext-posts-addon'),
					'slider' => __('Slider','revslider-prevnext-posts-addon'),
					'page' => __('Page','revslider-prevnext-posts-addon'),
					'pagetitle' => __('Page Title','revslider-prevnext-posts-addon'),
					'save' => __('Save Configration','revslider-prevnext-posts-addon'),
					'entersometitle' => __('Enter Some Title','revslider-prevnext-posts-addon'),
					'loadvalues' => __('Loading prevnext-posts Add-On Configration','revslider-prevnext-posts-addon'),
					'savevalues' => __('Saving prevnext-posts Add-On Configration','revslider-prevnext-posts-addon'),
					'category' => __('Category Only','revslider-prevnext-posts-addon'),
					'taxonomyonly' => __('Taxon. Only','revslider-prevnext-posts-addon'),
					'taxonomy' => __('Taxonomy','revslider-prevnext-posts-addon'),
					'autoadd' => __('Default Slider','revslider-prevnext-posts-addon'),					
					'position' => __('Slider Position','revslider-prevnext-posts-addon'),
					'above' => __('Above','revslider-prevnext-posts-addon'),
					'below' => __('Below','revslider-prevnext-posts-addon'),
					'none' => __('None','revslider-prevnext-posts-addon'),
					'dontaddslider' => __('Do not add Slider','revslider-prevnext-posts-addon'),
					'prevpost' => __('Prev Post','revslider-prevnext-posts-addon'),
					'nextpost' => __('Next Post','revslider-prevnext-posts-addon'),
					'infotaxonomy' => __('Only display posts from current post\'s taxonomy','revslider-prevnext-posts-addon'),
					'infocategory' => __('Only display posts from current post\'s category','revslider-prevnext-posts-addon'),
					'infoslidertype' => __('Requires "Current Post"<br>Post Content Sliders','revslider-prevnext-posts-addon'),
				
					'prev_title'=>__('Post Title','revslider-prevnext-posts-addon'),
					'prev_excerpt'=>__('Post Excerpt','revslider-prevnext-posts-addon'),
					'prev_content'=>__('Post content','revslider-prevnext-posts-addon'),
					'prev_content_words'=>__('Post content limit by words','revslider-prevnext-posts-addon'),
					'prev_content_chars'=>__('Post content limit by chars','revslider-prevnext-posts-addon'),
					'prev_link'=>__('The link to the post','revslider-prevnext-posts-addon'),
					'prev_date'=>__('Date created','revslider-prevnext-posts-addon'),
					'prev_date_modified'=>__('Date modified','revslider-prevnext-posts-addon'),
					'prev_author_name'=>__('Author name','revslider-prevnext-posts-addon'),
					'prev_num_comments'=>__('Number of comments','revslider-prevnext-posts-addon'),
					'prev_catlist'=>__('List of categories with links','revslider-prevnext-posts-addon'),
					'prev_catlist_raw'=>__('List of categories without links','revslider-prevnext-posts-addon'),
					'prev_taglist'=>__('List of tags with links','revslider-prevnext-posts-addon'),
					'prev_id'=>__('Post ID','revslider-prevnext-posts-addon'),

					'next_title'=>__('Post Title','revslider-prevnext-posts-addon'),
					'next_excerpt'=>__('Post Excerpt','revslider-prevnext-posts-addon'),
					'next_content'=>__('Post content','revslider-prevnext-posts-addon'),
					'next_content_words'=>__('Post content limit by words','revslider-prevnext-posts-addon'),
					'next_content_chars'=>__('Post content limit by chars','revslider-prevnext-posts-addon'),
					'next_link'=>__('The link to the post','revslider-prevnext-posts-addon'),
					'next_date'=>__('Date created','revslider-prevnext-posts-addon'),
					'next_date_modified'=>__('Date modified','revslider-prevnext-posts-addon'),
					'next_author_name'=>__('Author name','revslider-prevnext-posts-addon'),
					'next_num_comments'=>__('Number of comments','revslider-prevnext-posts-addon'),
					'next_catlist'=>__('List of categories with links','revslider-prevnext-posts-addon'),
					'next_catlist_raw'=>__('List of categories without links','revslider-prevnext-posts-addon'),
					'next_taglist'=>__('List of tags with links','revslider-prevnext-posts-addon'),
					'next_id'=>__('Post ID','revslider-prevnext-posts-addon')	
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
	public function save_prevnext_posts() {
		// Verify that the incoming request is coming with the security nonce
		if(isset($_REQUEST['data']['revslider_prevnext_posts_form'])){
			update_option( "revslider_prevnext_posts_addon", $_REQUEST['data']['revslider_prevnext_posts_form'] );
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
	public function values_prevnext_posts() {
		$revslider_prevnext_posts_addon_values = array();
		parse_str(get_option('revslider_prevnext_posts_addon'), $revslider_prevnext_posts_addon_values);
		$return = json_encode($revslider_prevnext_posts_addon_values);
		return array("message" => "Data found", "data"=>$return);
	}

	/**
	 * Change Enable Status of this Add-On
	 *
	 * @since    1.0.0
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_prevnext_posts_enabled", $enabled );	
	}


	/**
	 * Perform Ajax Call from RevSlider core
	 *
	 * @since    1.0.0
	 */
	public function do_ajax($return,$action) {
		switch ($action) {
			case 'wp_ajax_enable_revslider-prevnext-posts-addon':
				$this->change_addon_status( 1 );
				return  __('prevnext-posts AddOn enabled', 'revslider-prevnext-posts-addon');
				break;
			
			case 'wp_ajax_disable_revslider-prevnext-posts-addon':
				$this->change_addon_status( 0 );
				return  __('prevnext-posts AddOn disabled', 'revslider-prevnext-posts-addon');
				break;

			case 'wp_ajax_get_values_revslider-prevnext-posts-addon':
				$return = $this->values_prevnext_posts();
				if(empty($return)) $return = true;
				return $return;
				break;
			case 'wp_ajax_save_values_revslider-prevnext-posts-addon':
				$return = $this->save_prevnext_posts();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-prevnext-posts-addon');
				} 
				else {
					return  __('prevnext-posts Configuration saved', 'revslider-prevnext-posts-addon');	
				}
				break;
			default:
				return $return;
				break;
		}
	}

}
