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

if ( ! class_exists( 'YITH_Product_Slider_Type_Premium' ) ) {

	/**
	 * YITH_Product_Slider_Type_Premium
	 */
	class YITH_Product_Slider_Type_Premium extends YITH_Product_Slider_Type {

		/**
		 * __construct function
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'admin_init', array( $this, 'add_tab_metabox' ) );
			add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'edit_columns' ) );
			add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
			// Custom Tab Message!
			add_filter( 'post_updated_messages', array( $this, 'custom_tab_messages' ) );
			// Register metabox to tab manager!
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_product_slider_metaboxes' ) );
		}

		/**
		 * Get_instance
		 *
		 * @return YITH_Product_Slider_Type_Premium
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add_tab_metabox
		 * Register metabox for product slider
		 *
		 * @package YITH
		 * @since 1.0.0
		 */
		public function add_tab_metabox() {

			$args = include_once YWCPS_INC . '/metaboxes/product_slider-metabox.php';

			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once YWCPS_DIR . 'plugin-fw/yit-plugin.php';
			}
			$metabox = YIT_Metabox( 'yit-product-slider-setting' );
			$metabox->init( $args );

		}

		/**
		 * Edit Columns Table
		 *
		 * @param mixed $columns columns.
		 *
		 * @return mixed
		 */
		public function edit_columns( $columns ) {

			$columns = apply_filters(
				'yith_add_column_prod_slider',
				array(
					'cb'        => '<input type="checkbox" />',
					'title'     => __( 'Title', 'yith-woocommerce-product-slider-carousel' ),
					'shortcode' => __( 'Shortcode', 'yith-woocommerce-product-slider-carousel' ),
					'date'      => __( 'Date', 'yith-woocommerce-product-slider-carousel' ),
				)
			);

			return $columns;
		}

		/**
		 * Print the content columns
		 *
		 * @param mixed $column column.
		 * @param mixed $post_id post id.
		 */
		public function custom_columns( $column, $post_id ) {

			switch ( $column ) {
				case 'shortcode':
					$shortcode = '[yith_wc_productslider id=' . esc_html( $post_id ) . ']';
					echo yith_plugin_fw_get_field(
						array(
							'type' => 'copy-to-clipboard',
							'value' => $shortcode,
							'id' => 'ywcps_shortcode_copy',
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					break;
			}
		}

		/**
		 * Add_custom_product_slider_metaboxes
		 *
		 * @param mixed $args args.
		 *
		 * @return $args
		 */
		public function add_custom_product_slider_metaboxes( $args ) {

			$custom_types = array(
				'custom_checkbox',
				'select-group',
			);
			if ( in_array( $args['type'], $custom_types, true ) ) {
				$args['basename'] = YWCPS_DIR;
				$args['path']     = 'metaboxes/types/';
			}

			return $args;
		}

		/**
		 * Customize the messages for Sliders
		 *
		 * @param array $messages messages.
		 *
		 * @return array
		 * @fire post_updated_messages filter
		 * @package YITH
		 *
		 */
		public function custom_tab_messages( $messages ) {

			$singular_name                     = $this->get_tab_taxonomy_label( 'singular_name' );
			$messages[ $this->post_type_name ] = array(

				0  => '',
				/* translators: % is the singular name */
				1  => sprintf( __( '%s updated', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
				2  => __( 'Custom field updated', 'yith-woocommerce-product-slider-carousel' ),
				3  => __( 'Custom field deleted', 'yith-woocommerce-product-slider-carousel' ),
				/* translators: % is the singular name */
				4  => sprintf( __( '%s updated', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
				/* translators: % is the version restored of Product Slider Carousel name */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Product Slider Carousel restored to version %s', 'yith-woocommerce-product-slider-carousel' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, //phpcs:ignore WordPress.Security.NonceVerification
				/* translators: % is the singular name */
				6  => sprintf( __( '%s published', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
				/* translators: % is the singular name */
				7  => sprintf( __( '%s saved', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
				/* translators: % is the singular name */
				8  => sprintf( __( '%s submitted', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
				// * translators: % is the singular name */
				9  => sprintf( __( '%s', 'yith-woocommerce-product-slider-carousel' ), $singular_name ), //phpcs:ignore
				/* translators: % is the singular name */
				10 => sprintf( __( '%s draft updated', 'yith-woocommerce-product-slider-carousel' ), $singular_name ),
			);

			return $messages;
		}
	}
}
