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
 * Product Data Panel - General Tab
 *
 * Functions to modify the Product Data Panel - General Tab to add the
 * measurement price calculator fields
 */

add_action( 'woocommerce_product_options_dimensions', 'wc_price_calculator_product_options_dimensions' );

/**
 * Display our custom product Area/Volume meta fields in the product edit page
 */
function wc_price_calculator_product_options_dimensions() {

	woocommerce_wp_text_input( array(
		'id'          => '_area',
		'class'       => 'wc_input_decimal',
		'label'       => __( 'Area', 'woocommerce-measurement-price-calculator' ) . ' (' . get_option( 'woocommerce_area_unit' ) . ')',
		'description' => '<br />' . __( 'Overrides the area calculated from the width/length dimensions for the Measurements Price Calculator.', 'woocommerce-measurement-price-calculator' ) . '</br>',
	 ) );

	woocommerce_wp_text_input( array(
		'id'          => '_volume',
		'class'       => 'wc_input_decimal',
		'label'       => __( 'Volume', 'woocommerce-measurement-price-calculator' ) . ' (' . get_option( 'woocommerce_volume_unit' ) . ')',
		'description' => '<br />' .  __( 'Overrides the volume calculated from the width/length/height dimensions for the Measurements Price Calculator.', 'woocommerce-measurement-price-calculator' ) . '</br>',
	) );
}


add_action( 'woocommerce_process_product_meta', 'wc_measurement_price_calculator_process_product_meta', 10, 2 );

/**
 * Save our custom product meta fields
 *
 * @param int $post_id The post ID.
 * @param \WP_Post $post The post being saved.
 */
function wc_measurement_price_calculator_process_product_meta( $post_id, $post ) {

	$is_virtual = isset( $_POST['_virtual'] ) ? 'yes' : 'no';

	// Dimensions
	if ( 'no' === $is_virtual ) {
		update_post_meta( $post_id, '_area',   $_POST['_area'] );
		update_post_meta( $post_id, '_volume', $_POST['_volume'] );
	} else {
		update_post_meta( $post_id, '_area',   '' );
		update_post_meta( $post_id, '_volume', '' );
	}

	// compensate for non-integral stock quantities enforced by WC core
	$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

	if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

		if ( ! empty( $_POST['_manage_stock'] ) && 'grouped' !== $product_type && 'external' !== $product_type ) {

			// Manage stock
			update_post_meta( $post_id, '_stock', is_numeric( $_POST['_stock'] ) ? $_POST['_stock'] : (int) $_POST['_stock'] );

			// Check stock level (allowing stock quantities between 0 and 1 to be accepted, ie 0.5
			if ( 'variable' !== $product_type && 'no' === $_POST['_backorders'] && $_POST['_stock'] < 1 && $_POST['_stock'] > 0 ) {

				update_post_meta( $post_id, '_stock_status', $_POST['_stock_status'] );
			}
		}
	}
}


// add a minimum price field to simple products under the 'General' tab
add_action( 'woocommerce_product_options_pricing', 'wc_measurement_price_calculator_product_minimum_price' );

/**
 * Display the minimum price field for simple pricing calculator products
 *
 * @since 3.1
 */
function wc_measurement_price_calculator_product_minimum_price() {

	woocommerce_wp_text_input(
		array(
			'id'                => '_wc_measurement_price_calculator_min_price',
			'wrapper_class'     => 'show_if_pricing_calculator',
			'class'             => 'wc_input_price short',
			/* translators: Placeholders: %s - currency symbol */
			'label'             => sprintf( __( 'Minimum Price (%s)', 'woocommerce-measurement-price-calculator' ), get_woocommerce_currency_symbol() ),
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => 'any',
				'min'  => '0',
			),
		)
	);
}


// save the minimum price field for simple products
add_action( 'woocommerce_process_product_meta', 'wc_measurement_price_calculator_product_minimum_price_save' );

/**
 * save the minimum price field for simple products
 *
 * @since 3.1
 * @param int $post_id The post ID of the product being saved.
 */
function wc_measurement_price_calculator_product_minimum_price_save( $post_id ) {

	if ( isset( $_POST['_wc_measurement_price_calculator_min_price'] ) ) {

		update_post_meta( $post_id, '_wc_measurement_price_calculator_min_price', $_POST['_wc_measurement_price_calculator_min_price'] );
	}
}
