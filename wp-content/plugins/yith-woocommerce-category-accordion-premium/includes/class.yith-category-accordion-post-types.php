<?php //phpcs:ignore
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'YITH_Category_Accordion_Post_Types' ) ) {

	/**
	 * The post type class
	 */
	class YITH_Category_Accordion_Post_Types {

		/**
		 * The static instance of class
		 *
		 * @var YITH_Category_Accordion_Post_Types $instance
		 */
		private static $instance;

		/**
		 * Post type name
		 *
		 * @var string
		 */
		public static $post_type = 'yith_cacc';

		/**
		 * Get_instance
		 *
		 * @return $instance
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * __construct
		 *
		 * @return void
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'setup_post_type' ), 5 );

		}

		/**
		 * Setup_post_type
		 *
		 * @return void
		 */
		public static function setup_post_type() {

			$labels = array(
				'name'          => __( 'Accordion Styles', 'yith-woocommerce-category-accordion' ),
				'singular_name' => __( 'Accordion', 'yith-woocommerce-category-accordion' ),
				'menu_name'     => __( 'Accordion', 'yith-woocommerce-category-accordion' ),
				'add_new_item'  => __( 'Add new accordion style', 'yith-woocommerce-category-accordion' ),
				'add_new'       => __( '+ Create accordion', 'yith-woocommerce-category-accordion' ),
				'edit_item'     => __( 'Edit accordion', 'yith-woocommerce-category-accordion' ),
				'view_item'     => __( 'View accordion', 'yith-woocommerce-category-accordion' ),
				'update_item'   => __( 'Update accordion', 'yith-woocommerce-category-accordion' ),
				'search_items'  => __( 'Search accordion', 'yith-woocommerce-category-accordion' ),
				'not_found'     => __( 'No accordions', 'yith-woocommerce-category-accordion' ),

			);

			$args = array(
				'label'           => __( 'Accordions', 'yith-woocommerce-category-accordion' ),
				'labels'          => $labels,
				'description'     => 'Create, edit and show accordions',
				'public'          => false,
				'show_in_menu'    => false,
				'show_ui'         => true,
				'capability_type' => 'post',
				'supports'        => array( 'title' ),

			);

			register_post_type( self::$post_type, $args );

		}

	}

}

YITH_Category_Accordion_Post_Types::get_instance();
