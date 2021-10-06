<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Featured_Addon
 * @subpackage Revslider_Featured_Addon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_Featured_Addon
 * @subpackage Revslider_Featured_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Featured_Addon_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode( 'featured_revslider', array($this,'featured_revslider_shortcode') );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_Featured_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Featured_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-featured-addon-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_Featured_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Featured_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-featured-addon-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Action to display the featured slider set in the options instead the featured image
	 * if featured image is included by the_thumbnail into the theme templates
	 *
	 * @since    1.0.0
	 */
	public function post_thumbnail_replace( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		$html = $this->create_slider( $html, $post_id, $post_thumbnail_id);
		return $html;
	}

	/**
	 * Shortcode to display the featured slider anywhere
	 *
	 * @since    1.0.0
	 */
	public function featured_revslider_shortcode($atts , $content = '') {
		global $post;
		$post_id = $post->ID;
		$post_thumbnail_id = get_post_thumbnail_id($post->ID);
		$html = $this->create_slider( $content, $post_id, $post_thumbnail_id );
		return $html;
	}

	/**
	 * Build the return value for either shortcode or WP filter
	 *
	 * @since    1.0.0
	 * @changed  2.0.1 removed inside gallery with one item
	 */
	private function create_slider( $html, $post_id , $post_thumbnail_id=0 ) {
		//saved values
		$revslider_featured_addon_values = array();
		parse_str(get_option('revslider_featured_addon'), $revslider_featured_addon_values);

		$post_revslider_id = get_post_meta( $post_id, 'revslider_featured_slider_id', true );

		//defaults in case no saved values
		$revslider_featured_addon_values['revslider-featured-addon-type'] = isset($revslider_featured_addon_values['revslider-featured-addon-type']) ? $revslider_featured_addon_values['revslider-featured-addon-type'] : 'single';
		$revslider_featured_addon_values['revslider-featured-addon-slider'] = isset($revslider_featured_addon_values['revslider-featured-addon-slider']) ? $revslider_featured_addon_values['revslider-featured-addon-slider'] : '';

		//defaults for display logic
		$revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-image'] = isset($revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-image']) ? $revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-image'] : 'off';
		$revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-slider'] = isset($revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-slider']) ? $revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-slider'] : 'off';
		$revslider_featured_addon_values['revslider-featured-addon-write-when-no-featured-image'] = isset($revslider_featured_addon_values['revslider-featured-addon-write-when-no-featured-image']) ? $revslider_featured_addon_values['revslider-featured-addon-write-when-no-featured-image'] : 'off';

		//auto display
		if ( $revslider_featured_addon_values['revslider-featured-addon-type']=='auto' && !empty($revslider_featured_addon_values['revslider-featured-addon-slider']) ){
			//Option: Overwrite featured image off
			if($revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-image'] == "off" && $post_thumbnail_id>0) return $html;
			//Option: Overwrite featured slider off
			if($revslider_featured_addon_values['revslider-featured-addon-overwrite-featured-slider'] == "off" && !empty($post_revslider_id)){
				//Set Slider to local post saved slider
				$revslider_featured_addon_values['revslider-featured-addon-slider'] = $post_revslider_id;
			}else{
				//Option: No featured Image = No slider also
				if($revslider_featured_addon_values['revslider-featured-addon-write-when-no-featured-image'] == "off" && $post_thumbnail_id<0) return $html;
			};
			//Add RevSlider Shortcode with current post as only content
			$html = do_shortcode('[rev_slider alias="'.$revslider_featured_addon_values['revslider-featured-addon-slider'].'"][/rev_slider]');
		}
		else {
			if (!empty($post_revslider_id)){
				$html = do_shortcode('[rev_slider alias="'.$post_revslider_id.'"][/rev_slider]');
			}
		}

		return $html;
	}

	/**
	 * Mostly the same as `get_metadata()` makes sure any postthumbnail function gets checked at
	 * the deepest level possible.
	 *
	 */
	function set_revslider_addon_meta_key( $null = null, $object_id = '', $meta_key = '', $single = '') {
		// only affect thumbnails on the frontend, do allow ajax calls
		
		if ( ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) || '_thumbnail_id' != $meta_key )
			return $null;

		$meta_type = 'post';
		$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');

		if ( !$meta_cache ) {
			$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
			$meta_cache = $meta_cache[$object_id];
		}

		if ( !$meta_key )
			return $meta_cache;

		if ( isset($meta_cache[$meta_key]) ) {
			if ( $single )
				return maybe_unserialize( $meta_cache[$meta_key][0] );
			else
				return array_map('maybe_unserialize', $meta_cache[$meta_key]);
		}

		if ($single)
			// allow to set an other ID
			return apply_filters( 'revslider_featured_image_thumbnail_id', -3 , $object_id ); // set the default featured img ID
		else
			return array();


	}

	public function filter_get_posts($query, $slider_id){
	//	var_dump($query);
	//	var_dump($slider_id);
	}


}
