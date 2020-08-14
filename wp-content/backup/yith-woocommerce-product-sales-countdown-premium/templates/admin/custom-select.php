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

/**
 * Implements a custom select in YWPC plugin admin tab
 *
 * @class   YWPC_Custom_Select
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWPC_Custom_Select {

	/**
	 * Outputs a custom select template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public static function output( $option ) {

		$option_value   = WC_Admin_Settings::get_option( $option['id'], $option['default'] );
		$formatted_name = '';

		if ( $option_value != '' ) {
			$product = wc_get_product( $option_value );
			if ( $product ) {
				$formatted_name = wp_kses_post( $product->get_formatted_name() );
			}

			if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {
				$formatted_name = array( $option_value => $formatted_name );
			}

		}

		$args = array(
			'class'            => esc_attr( $option['class'] ),
			'id'               => esc_attr( $option['id'] ),
			'name'             => esc_attr( $option['id'] ),
			'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'yith-woocommerce-product-countdown' ),
			'data-allow_clear' => false,
			'data-selected'    => $formatted_name,
			'data-multiple'    => false,
			'data-action'      => 'woocommerce_json_search_products',
			'value'            => $option_value,
			'style'            => esc_attr( $option['css'] )
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