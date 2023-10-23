<?php
/**
 * FAQ Shortcode Post Type class
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Shortcode_Post_Type' ) ) {

	/**
	 * Main class
	 *
	 * @class   YITH_FAQ_Shortcode_Post_Type
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress
	 */
	class YITH_FAQ_Shortcode_Post_Type {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_faq_post_type' ) );
		}

		/**
		 * Add video post type
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function add_faq_post_type() {

			$args = array(
				'labels'              => array(),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => false,
				'show_in_menu'        => false,
				'menu_position'       => 0,
				'show_in_nav_menus'   => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'menu_icon'           => '',
				'capability_type'     => 'post',
				'rewrite'             => false,
				'publicly_queryable'  => false,
				'query_var'           => false,
			);

			register_post_type( YITH_FWP_SHORTCODE_POST_TYPE, $args );

		}

	}

	new YITH_FAQ_Shortcode_Post_Type();

}
