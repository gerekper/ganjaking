<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_gal_Public {

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

		//exchange gallery shortcode
		remove_shortcode('gallery', 'gallery_shortcode');
		add_shortcode('gallery', array($this,'rev_addon_gallery'),10,2);

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
		 * defined in Rev_addon_gal_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_gal_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rev_addon_gal-public.css', array(), $this->version, 'all' );

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
		 * defined in Rev_addon_gal_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_gal_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rev_addon_gal-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Shortcode to wrap around the original gallery shortcode
	 *
	 * @since    2.0.0
	 */
	public function gutenberg_block_content_fitler( $block_content, $block ) {
	
		if(class_exists( 'RevSliderFunctions' )) {
		
			$f = new RevSliderFunctions();
			$blockName = $f->get_val($block, 'blockName', '');
			
			if($blockName === 'core/gallery') {
			
				$ids = $f->get_val($block, array('attrs', 'ids'), array());
				$class = $f->get_val($block, array('attrs', 'className'), '');
				
				if(is_array($ids) && !empty($ids) && is_string($class) && strpos($class, 'revslider-gallery-') !== false) {
				
					$alias = str_replace('revslider-gallery-', '', $class);
					$ids = implode($ids, ',');
					return '[gallery rev_addon_gal_slider="' . $alias . '" ids="' . $ids . '"]';
				
				}
			
			}
			
		}
		
		return $block_content;

	}

	/**
	 * Shortcode to wrap around the original gallery shortcode
	 *
	 * @since    1.0.0
	 * @version  1.0.1 : Exits when other revslider_function
	 */
	public function rev_addon_gallery($output, $attr){
		$return = array();
		foreach($output as $attr_key => $attr_value){
			$return[] = $attr_key.'="'.$attr_value.'"';
		}
		$return = implode(" ", $return);

		//exits if other RevSlider functionality captures the gallery functionality
		if(isset($output["revslider_function"]) && $output["revslider_function"]!='gallery') return false;

		$slider = isset( $output["rev_addon_gal_slider"] ) ? $output["rev_addon_gal_slider"] : get_option("revslider_gallery_addon");

		if(!empty($slider)){
			return do_shortcode('[rev_slider alias="'.$slider.'"][gallery '.$return.'][/rev_slider]');
		}
		else return false;
	}

	/**
	 * Filters the custom meta placeholders and replaces them
	 *
	 * @since    1.0.0
	 */
	public function rev_addon_insert_meta($text,$post_id){
		$text = str_replace(array('%caption%', '{{caption}}'), '{{excerpt}}', $text);
		$text = str_replace(array('%description%', '{{description}}'), '{{content}}', $text);
		$text = str_replace(array('%uploaded%', '{{uploaded}}'), '{{date}}', $text);
		
		$avail_image_sizes = get_intermediate_image_sizes();
		$avail_image_sizes[] = 'full';

		foreach($avail_image_sizes as $image_size){
			$image_array = wp_get_attachment_image_src($post_id,$image_size);
			$text = str_replace(array('%image_'.$image_size.'_url%', '{{image_'.$image_size.'_url}}'), $image_array[0], $text);
			$text = str_replace(array('%image_'.$image_size.'_html%', '{{image_'.$image_size.'_html}}'), wp_get_attachment_image($post_id,$image_size), $text);
		}
		return $text;
	}

}// END CLASS
