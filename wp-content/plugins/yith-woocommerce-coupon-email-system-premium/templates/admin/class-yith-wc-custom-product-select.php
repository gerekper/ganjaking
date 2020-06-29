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

if ( ! class_exists( 'YITH_WC_Custom_Product_Select' ) ) {

	/**
	 * Outputs a custom product select template in plugin options panel
	 *
	 * @class   YITH_WC_Custom_Product_Select
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YITH_WC_Custom_Product_Select {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Custom_Product_Select
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Custom_Product_Select
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self( $_REQUEST );

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'woocommerce_admin_field_yith-wc-product-select', array( $this, 'output' ) );

		}

		/**
		 * Outputs a custom product select template in plugin options panel
		 *
		 * @since   1.0.0
		 *
		 * @param   $option
		 *
		 * @author  Alberto Ruggiero
		 * @return  void
		 */
		public function output( $option ) {

			$custom_attributes = array();

			if ( ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {

				$custom_attributes = $option['custom_attributes'];

			}

			$option_value = WC_Admin_Settings::get_option( $option['id'], $option['default'] );
			$product_ids  = array_filter( array_map( 'absint', ( is_array( $option_value ) ? $option_value : explode( ',', $option_value ) ) ) );
			$json_ids     = array();

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( is_object( $product ) ) {
					$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
				}
			}

			$args = array(
				'class'             => esc_attr( $option['class'] ),
				'id'                => esc_attr( $option['id'] ),
				'name'              => esc_attr( $option['id'] ),
				'data-placeholder'  => esc_attr( $option['placeholder'] ),
				'data-allow_clear'  => false,
				'data-selected'     => $json_ids,
				'data-multiple'     => $option['multiple'],
				'data-action'       => ( ( $option['variations'] == 'true' ) ? 'woocommerce_json_search_products_and_variations' : 'woocommerce_json_search_products' ),
				'value'             => implode( ',', array_keys( $json_ids ) ),
				'style'             => esc_attr( $option['css'] ),
				'custom-attributes' => $custom_attributes
			)

			?>
			<tr valign="top" class="titledesc">
				<th scope="row">
					<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $option['type'] ) ?>">

					<?php yit_add_select2_fields( $args ) ?>

				</td>
			</tr>
			<?php
		}

	}

	/**
	 * Unique access to instance of YITH_WC_Custom_Product_Select class
	 *
	 * @return \YITH_WC_Custom_Product_Select
	 */
	function YITH_WC_Custom_Product_Select() {

		return YITH_WC_Custom_Product_Select::get_instance();

	}

	new YITH_WC_Custom_Product_Select();

}