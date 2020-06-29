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

namespace SkyVerge\WooCommerce\Measurement_Price_Calculator;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Shortcodes handler.
 *
 * @since 3.14.0
 */
class Shortcodes {


	/**
	 * Initializes shortcodes.
	 *
	 * @since 3.14.0
	 */
	public function __construct() {

		add_shortcode( 'wc_measurement_price_calculator_pricing_table', array( $this, 'pricing_table_shortcode' ) );
	}


	/**
	 * Renders a table of product prices.
	 *
	 * @internal
	 *
	 * @since 3.14.0
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public function pricing_table_shortcode( $atts ) {

		require_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/shortcodes/class-wc-price-calculator-shortcode-pricing-table.php' );

		return \WC_Shortcodes::shortcode_wrapper( array( 'WC_Price_Calculator_Shortcode_Pricing_Table', 'output' ), $atts, array( 'class' => 'wc-measurement-price-calculator' ) );
	}


}
