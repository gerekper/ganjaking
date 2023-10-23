<?php
/**
 * Taxonomy class
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Taxonomy' ) ) {

	/**
	 * Taxonomy class
	 *
	 * @class   YITH_FAQ_Taxonomy
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress
	 */
	class YITH_FAQ_Taxonomy {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_faq_taxonomy' ) );
		}

		/**
		 * Add faq taxonomy
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_faq_taxonomy() {

			$labels = array(
				'name'                       => esc_html__( 'Categories', 'yith-faq-plugin-for-wordpress' ),
				'singular_name'              => esc_html__( 'Category', 'yith-faq-plugin-for-wordpress' ),
				'menu_name'                  => esc_html__( 'Categories', 'yith-faq-plugin-for-wordpress' ),
				'all_items'                  => esc_html__( 'All categories', 'yith-faq-plugin-for-wordpress' ),
				'parent_item'                => esc_html__( 'Parent category', 'yith-faq-plugin-for-wordpress' ),
				'parent_item_colon'          => esc_html__( 'Parent category:', 'yith-faq-plugin-for-wordpress' ),
				'new_item_name'              => esc_html__( 'New category', 'yith-faq-plugin-for-wordpress' ),
				'add_new_item'               => esc_html__( 'Add new category', 'yith-faq-plugin-for-wordpress' ),
				'edit_item'                  => esc_html__( 'Edit category', 'yith-faq-plugin-for-wordpress' ),
				'update_item'                => esc_html__( 'Update category', 'yith-faq-plugin-for-wordpress' ),
				'view_item'                  => esc_html__( 'View category', 'yith-faq-plugin-for-wordpress' ),
				'separate_items_with_commas' => esc_html__( 'Separate items with commas', 'yith-faq-plugin-for-wordpress' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove categories', 'yith-faq-plugin-for-wordpress' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used categories', 'yith-faq-plugin-for-wordpress' ),
				'popular_items'              => esc_html__( 'Popular categories', 'yith-faq-plugin-for-wordpress' ),
				'search_items'               => esc_html__( 'Search categories', 'yith-faq-plugin-for-wordpress' ),
				'not_found'                  => esc_html__( 'Not found', 'yith-faq-plugin-for-wordpress' ),
				'no_terms'                   => esc_html__( 'No categories', 'yith-faq-plugin-for-wordpress' ),
				'items_list'                 => esc_html__( 'Categories list', 'yith-faq-plugin-for-wordpress' ),
				'items_list_navigation'      => esc_html__( 'Categories list navigation', 'yith-faq-plugin-for-wordpress' ),
			);

			$args = array(
				'labels'             => $labels,
				'hierarchical'       => true,
				'public'             => false,
				'show_ui'            => true,
				'show_admin_column'  => true,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'show_in_rest'       => true,
				'publicly_queryable' => false,
			);

			register_taxonomy( YITH_FWP_FAQ_TAXONOMY, array( YITH_FWP_FAQ_POST_TYPE ), $args );

		}

	}

	new YITH_FAQ_Taxonomy();

}
