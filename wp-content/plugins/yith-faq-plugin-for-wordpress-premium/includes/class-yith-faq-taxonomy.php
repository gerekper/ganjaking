<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_FAQ_Taxonomy' ) ) {


	/**
	 * Taxonomy class
	 *
	 * @class   YITH_FAQ_Taxonomy
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 *
	 */
	class YITH_FAQ_Taxonomy {

		/**
		 * @var $post_type string post type name
		 */
		private $post_type = null;

		/**
		 * @var $taxonomy string taxonomy name
		 */
		private $taxonomy = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 *
		 * @param   $taxonomy  string
		 * @param   $post_type string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct( $post_type, $taxonomy ) {

			$this->post_type = $post_type;
			$this->taxonomy  = $taxonomy;

			add_action( 'init', array( $this, 'add_faq_taxonomy' ) );

		}

		/**
		 * Add faq taxonomy
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_faq_taxonomy() {

			$labels = array(
				'name'                       => esc_html_x( 'Categories', 'Taxonomy General Name', 'yith-faq-plugin-for-wordpress' ),
				'singular_name'              => esc_html_x( 'Category', 'Taxonomy Singular Name', 'yith-faq-plugin-for-wordpress' ),
				'menu_name'                  => esc_html__( 'Categories', 'yith-faq-plugin-for-wordpress' ),
				'all_items'                  => esc_html__( 'All Categories', 'yith-faq-plugin-for-wordpress' ),
				'parent_item'                => esc_html__( 'Parent Category', 'yith-faq-plugin-for-wordpress' ),
				'parent_item_colon'          => esc_html__( 'Parent Category:', 'yith-faq-plugin-for-wordpress' ),
				'new_item_name'              => esc_html__( 'New Category', 'yith-faq-plugin-for-wordpress' ),
				'add_new_item'               => esc_html__( 'Add New Category', 'yith-faq-plugin-for-wordpress' ),
				'edit_item'                  => esc_html__( 'Edit Category', 'yith-faq-plugin-for-wordpress' ),
				'update_item'                => esc_html__( 'Update Category', 'yith-faq-plugin-for-wordpress' ),
				'view_item'                  => esc_html__( 'View Category', 'yith-faq-plugin-for-wordpress' ),
				'separate_items_with_commas' => esc_html__( 'Separate items with commas', 'yith-faq-plugin-for-wordpress' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove categories', 'yith-faq-plugin-for-wordpress' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used categories', 'yith-faq-plugin-for-wordpress' ),
				'popular_items'              => esc_html__( 'Popular Categories', 'yith-faq-plugin-for-wordpress' ),
				'search_items'               => esc_html__( 'Search Categories', 'yith-faq-plugin-for-wordpress' ),
				'not_found'                  => esc_html__( 'Not Found', 'yith-faq-plugin-for-wordpress' ),
				'no_terms'                   => esc_html__( 'No Categories', 'yith-faq-plugin-for-wordpress' ),
				'items_list'                 => esc_html__( 'Categories list', 'yith-faq-plugin-for-wordpress' ),
				'items_list_navigation'      => esc_html__( 'Categories list navigation', 'yith-faq-plugin-for-wordpress' ),
			);

			$args = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => false,
			);

			register_taxonomy( $this->taxonomy, array( $this->post_type ), $args );

		}

	}

}




