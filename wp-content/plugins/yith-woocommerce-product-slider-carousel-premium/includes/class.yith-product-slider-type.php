<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Product_Slider_Type' ) ) {

	/**
	 * YITH_Product_Slider_Type
	 */
	class YITH_Product_Slider_Type {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_Product_Slider_Type $instance
		 */
		protected static $instance;
		/**
		 * YITH WooCommerce Sequential Order Number Premium post type name
		 *
		 * @var YITH_Product_Slider_Type $post_type_name
		 */
		protected $post_type_name;
		/**
		 * __construct function
		 */
		public function __construct() {

			$this->post_type_name = 'yith_wcps_type';
			// Add action register post type!
			add_action( 'init', array( $this, 'register_product_slider_post_type' ), 10 );
			if ( ! defined( 'YWCPS_PREMIUM' ) ) {
				add_action( 'init', array( $this, 'save_free_product_slider' ), 15 );
			}

		}

		/**
		 * Get_instance
		 *
		 * @return instance
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}


		/**Create Product Slider custom post type
		 *
		 * @package YITH
		 * @since 1.0.0
		 */
		public function register_product_slider_post_type() {
			$args = apply_filters(
				'yith_wcps_post_type',
				array(
					'label'               => 'yith_wcps_type',
					'description'         => __( 'Yith WooCommerce Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'labels'              => defined( 'YWCPS_PREMIUM' ) ? $this->get_tab_taxonomy_label() : array(),
					'supports'            => array( 'title' ),
					'hierarchical'        => true,
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => false,
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => false,
					'menu_position'       => 11,
					'menu_icon'           => 'dashicons-format-gallery',
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => false,
					'capability_type'     => 'post',
				)
			);

			register_post_type( $this->post_type_name, $args );
		}

		/**
		 * Save_free_product_slider
		 *
		 * @return void
		 */
		public function save_free_product_slider() {

			global $wpdb;

			$slider_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = 'yith_wcps_type'" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery
			global $slider_free_id;

			if ( $slider_found ) {
				$slider_free_id = intval( $slider_found );
			} else {
				$my_post        = array(
					'post_title'     => 'Yith WooCommerce Product Slider Carousel Free',
					'post_content'   => '',
					'post_status'    => 'private',
					'post_type'      => 'yith_wcps_type',
					'comment_status' => 'closed',
				);
				$slider_free_id = wp_insert_post( $my_post );

			}
			update_post_meta( $slider_free_id, '_ywcps_free_slider_id', $slider_free_id );
		}
		/**
		 * Get the tab taxonomy label
		 *
		 * @param string $arg The string to return. Defaul empty. If is empty return all taxonomy labels.
		 *
		 * @package YITH
		 * @since  1.0.0
		 *
		 * @return Array taxonomy label
		 * @fire yith_tab_manager_taxonomy_label hooks
		 */
		protected function get_tab_taxonomy_label( $arg = '' ) {

			$label = apply_filters(
				'yith_product_slider_taxonomy_label',
				array(
					'name'               => _x( 'YITH WooCommerce Product Slider Carousel', 'Post Type General Name', 'yith-woocommerce-product-slider-carousel' ),
					'singular_name'      => _x( 'Product Slider Carousel', 'Post Type Singular Name', 'yith-woocommerce-product-slider-carousel' ),
					'menu_name'          => __( 'Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-product-slider-carousel' ),
					'all_items'          => __( 'All Product Slider Carousels', 'yith-woocommerce-product-slider-carousel' ),
					'view_item'          => __( 'View Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'add_new_item'       => __( 'Add New Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'add_new'            => __( 'Add New Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'edit_item'          => __( 'Edit Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'update_item'        => __( 'Update Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'search_items'       => __( 'Search Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
					'not_found'          => __( 'Not found', 'yith-woocommerce-product-slider-carousel' ),
					'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-product-slider-carousel' ),
				)
			);
			return ! empty( $arg ) ? $label[ $arg ] : $label;
		}
	}

}
