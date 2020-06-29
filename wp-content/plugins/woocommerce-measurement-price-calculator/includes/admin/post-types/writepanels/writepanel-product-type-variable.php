<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Data Panel - Product Variations
 *
 * Functions to modify the Product Data Panel - Variations panels to add the
 * measurement price calculator area/volume and minimum price fields
 */

add_action( 'woocommerce_variation_options_dimensions', 'wc_price_calculator_product_after_variable_attributes', 10, 3 );

/**
 * Display our custom product Area/Volume meta fields in the product
 * variation form
 *
 * @param int $loop the loop index
 * @param array $variation_data the variation data
 * @param \WP_Post $variation_post the variation post object
 */
function wc_price_calculator_product_after_variable_attributes( $loop, $variation_data, $variation_post ) {
	global $post;

	$parent_product = wc_get_product( $post );

	// add meta data to $variation_data array
	$variation_product = wc_get_product( $variation_post );
	$variation_data    = $variation_product ? array_merge( $variation_product->get_meta_data(), $variation_data ) : $variation_data;

	// will use the parent area/volume (if set) as the placeholder
	$parent_data = array(
		'area'   => $parent_product ? $parent_product->get_meta( '_area' )   : null,
		'volume' => $parent_product ? $parent_product->get_meta( '_volume' ) : null,
	);

	// default placeholders
	if ( ! $parent_data['area'] )   $parent_data['area']   = '0.00';
	if ( ! $parent_data['volume'] ) $parent_data['volume'] = '0';

	?>
		<p class="form-row form-row-first hide_if_variation_virtual">
			<label>
				<?php echo esc_html( 'Area', 'woocommerce-measurement-price-calculator' ) . ' (' . esc_html( get_option( 'woocommerce_area_unit' ) ) . '):'; ?>
				<?php echo wc_help_tip( __( 'Overrides the area calculated from the width/length dimensions for the Measurements Price Calculator.', 'woocommerce-measurement-price-calculator' ) ); ?>
			</label>
			<input type="number" size="5" name="variable_area[<?php echo $loop; ?>]" value="<?php if ( isset( $variation_data['_area'][0] ) ) echo esc_attr( $variation_data['_area'][0] ); ?>" placeholder="<?php echo $parent_data['area']; ?>" step="any" min="0" />
		</p>
		<p class="form-row form-row-last hide_if_variation_virtual">
			<label>
				<?php echo esc_html( 'Volume', 'woocommerce-measurement-price-calculator' ) . ' (' . esc_html( get_option( 'woocommerce_volume_unit' ) ) . '):'; ?>
				<?php echo wc_help_tip( __( 'Overrides the volume calculated from the width/length/height dimensions for the Measurements Price Calculator.', 'woocommerce-measurement-price-calculator' ) ); ?>
			</label>
			<input type="number" size="5" name="variable_volume[<?php echo $loop; ?>]" value="<?php if ( isset( $variation_data['_volume'][0] ) ) echo esc_attr( $variation_data['_volume'][0] ); ?>" placeholder="<?php echo $parent_data['volume']; ?>" step="any" min="0" />
		</p>
	<?php
}


add_action( 'woocommerce_variation_options_pricing', 'wc_price_calculator_product_variable_pricing', 10, 3 );


/**
 * Displays our custom minimum price field in the product variation form.
 *
 * @internal
 *
 * @since 3.16.0
 *
 * @param int $loop the loop index
 * @param array $variation_data the variation data
 * @param \WP_Post $variation_post the variation post object
 */
function wc_price_calculator_product_variable_pricing( $loop, $variation_data, $variation_post ) {

	// add meta data to $variation_data array
	$variation_product = wc_get_product( $variation_post );
	$variation_data    = $variation_product ? array_merge( $variation_product->get_meta_data(), $variation_data ) : $variation_data;

	$default_value = isset( $variation_data['_wc_measurement_price_calculator_min_price'] ) ? current( $variation_data['_wc_measurement_price_calculator_min_price'] ) : null;

	woocommerce_wp_text_input(
		[
			'id'                => 'variable_min_price[' . $loop . ']',
			'name'              => 'variable_min_price[' . $loop . ']',
			'wrapper_class'     => 'show_if_pricing_calculator',
			'class'             => 'wc_input_price short',
			/* translators: Placeholder: %s - currency symbol */
			'label'             => sprintf( __( 'Minimum Price (%s)', 'woocommerce-measurement-price-calculator' ), get_woocommerce_currency_symbol() ),
			'type'              => 'number',
			'custom_attributes' => [
				'step' => 'any',
				'min'  => '0',
			],
			'value'             => $default_value,
		]
	);
}


add_action( 'woocommerce_process_product_meta_variable', 'wc_measurement_price_calculator_process_product_meta_variable' );
add_action( 'woocommerce_ajax_save_product_variations',  'wc_measurement_price_calculator_process_product_meta_variable' );

/**
 * Save the variable product options.
 *
 * @param mixed $post_id the post identifier
 */
function wc_measurement_price_calculator_process_product_meta_variable( $post_id ) {

	if ( isset( $_POST['variable_sku'] ) ) {

		$variable_post_id   = isset( $_POST['variable_post_id'] ) ? $_POST['variable_post_id'] : [];
		$variable_area      = isset( $_POST['variable_area'] ) ? $_POST['variable_area'] : '';
		$variable_volume    = isset( $_POST['variable_volume'] ) ? $_POST['variable_volume'] : '';
		$variable_min_price = isset( $_POST['variable_min_price'] ) ? $_POST['variable_min_price'] : '';

		// bail if $variable_post_id is not as expected
		if ( ! is_array( $variable_post_id ) ) {
			return;
		}

		$max_loop = max( array_keys( $variable_post_id ) );

		for ( $i = 0; $i <= $max_loop; $i++ ) {

			if ( ! isset( $variable_post_id[ $i ] ) ) continue;

			$variation_id = (int) $variable_post_id[ $i ];
			$variation    = wc_get_product( $variation_id );

			// Update area post meta
			if ( empty( $variable_area[ $i ] ) ) {
				$variation->delete_meta_data( '_area' );
			} else {
				$variation->update_meta_data( '_area', $variable_area[ $i ] );
			}

			// Update volume post meta
			if ( empty( $variable_volume[ $i ] ) ) {
				$variation->delete_meta_data( '_volume' );
			} else {
				$variation->update_meta_data( '_volume', $variable_volume[ $i ] );
			}

			// Update minimum price post meta
			if ( empty( $variable_min_price[ $i ] ) ) {
				$variation->delete_meta_data( '_wc_measurement_price_calculator_min_price' );
			} else {
				$variation->update_meta_data( '_wc_measurement_price_calculator_min_price', $variable_min_price[ $i ] );
			}

			$variation->save_meta_data();
		}
	}
}
