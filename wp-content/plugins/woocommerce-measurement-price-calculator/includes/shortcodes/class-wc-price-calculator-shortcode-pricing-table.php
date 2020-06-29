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

if ( ! class_exists( 'WC_Price_Calculator_Shortcode_Pricing_Table' ) ) :

/**
 * Pricing Table Shortcode
 *
 * Displays a pricing table
 *
 * @since 3.0
 */
class WC_Price_Calculator_Shortcode_Pricing_Table {

	/**
	 * Get the shortcode content
	 *
	 * @since 3.0
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public static function get( $atts ) {

		return \WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts, array( 'class' => 'wc-measurement-price-calculator' ) );
	}


	/**
	 * Output a pricing table
	 *
	 * * product_id/product_sku - id or sku of product.  Defaults to current product, if any
	 *
	 * Usage:
	 * [wc_measurement_price_calculator_pricing_table]
	 *
	 * @param array $atts associative array of shortcode parameters
	 */
	public static function output( $atts ) {
		global $product, $wpdb;

		extract( shortcode_atts( array(
			'product_id'  => '',
			'product_sku' => '',
		), $atts ) );

		// product by sku?
		if ( $product_sku ) {
			$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value=%s LIMIT 1", $product_sku ) );
		}

		// product by id?
		if ( $product_id ) {
			$product = wc_get_product( $product_id );
		}

		// product ?
		if ( ! $product ) {
			return;
		}

		// pricing rules?
		$settings = new \WC_Price_Calculator_Settings( $product );
		if ( ! $settings->pricing_rules_enabled() || ! $settings->has_pricing_rules() ) {
			return;
		}

		// the countdown element with a unique identifier to allow multiple countdowns on the same page, and common class for ease of styling
		echo self::get_pricing_rules_table( $settings->get_pricing_rules( $settings->get_pricing_unit() ), $settings );
	}


	/**
	 * Returns a pricing rules HTML table
	 *
	 * @since 3.0
	 * @param array $rules array of pricing rules
	 * @param \WC_Price_Calculator_Settings $settings the calculator settings object
	 * @return string pricing rules HTML table
	 */
	public static function get_pricing_rules_table( $rules, $settings ) {

		$html = '<table class="wc-measurement-price-calculator-pricing-table">';
		/* translators: Placeholders: %s - pricing unit */
		$html .= '<thead><tr><th>' . sprintf( __( 'Range (%s)', 'woocommerce-measurement-price-calculator' ), '<span class="units">' . __( $settings->get_pricing_label(), 'woocommerce-measurement-price-calculator' ) . '</span>' ) . '</th>';
		/* translators: Placeholders: %s - currency symbol */
		$html .= '<th>' . sprintf( __( 'Price (%s)', 'woocommerce-measurement-price-calculator' ),  '<span class="units">' . get_woocommerce_currency_symbol() . '/' . __( $settings->get_pricing_label(), 'woocommerce-measurement-price-calculator' ) . '</span>' ) . '</th></tr></thead>';
		$html .= '<tbody>';
		foreach ( $rules as $rule ) {

			// format the range as "1 ft", "1 - 3 ft" or "1+ ft"
			$range = $rule['range_start'];

			if ( '' === $rule['range_end'] ) {
				$range .= '+';
			} elseif ( $rule['range_end'] != $rule['range_start'] ) {
				$range .= ' - ' . $rule['range_end'];
			}

			$range .= ' ' . __( $settings->get_pricing_label(), 'woocommerce-measurement-price-calculator' );

			$html .= sprintf( '<tr><td>%s</td><td>%s</td></tr>', $range, $settings->get_pricing_rule_price_html( $rule ) );
		}

		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}
}

endif; // class_exists check
