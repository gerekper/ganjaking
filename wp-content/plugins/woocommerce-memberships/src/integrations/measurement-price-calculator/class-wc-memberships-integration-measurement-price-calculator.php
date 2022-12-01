<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handle Products with Measurement Price Calculator settings and member discounts.
 *
 * @since 1.8.8
 */
class WC_Memberships_Integration_Measurement_Price_Calculator {


	/**
	 * Filters Measurement Price Calculator products to apply member discounts.
	 *
	 * @since 1.8.8
	 */
	public function __construct() {

		// init discounts
		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Initializes Measurement Price Calculator product discounts.
	 *
	 * @see \WC_Memberships_Member_Discounts::init()
	 *
	 * @since 1.8.8
	 */
	public function init() {

		if ( wc_memberships()->get_member_discounts_instance()->applying_discounts() ) {

			// adjust rules table pricing
			add_filter( 'wc_measurement_price_calculator_settings_rule',  array( $this, 'apply_discounts_to_settings_rule_prices' ), 999, 2 );
			// handle the HTML price to display before/after discount price information
			add_filter( 'wc_measurement_price_calculator_get_price_html', array( $this, 'get_measurements_price_html' ), 999, 5 );
			// handle the correct cart and checkout product subtotal
			add_filter( 'woocommerce_product_get_price',                  array( $this, 'handle_product_cart_item_subtotal_price' ), 999, 2 );
			add_filter( 'woocommerce_cart_item_price',                    array( $this, 'handle_product_cart_item_subtotal_price' ), 999, 2 );
		}
	}


	/**
	 * Filters Measurement Price Calculator rules to filter prices for products with member discounts.
	 *
	 * @internal
	 *
	 * @since 1.8.8
	 *
	 * @param array $mpc_rule rule data (associative array with ranges, prices)
	 * @param \WC_Product $product the product the rule applies to, which may have member discounts
	 * @return array associative array with rule data
	 */
	public function apply_discounts_to_settings_rule_prices( $mpc_rule, $product ) {

		if ( $product instanceof \WC_Product ) {

			$member_discounts = wc_memberships()->get_member_discounts_instance();

			if (    $member_discounts
			     && isset( $mpc_rule['range_start'], $mpc_rule['regular_price'] )
			     && $member_discounts->user_has_member_discount( $product ) ) {

				if ( ! empty( $mpc_rule['regular_price'] ) ) {
					$regular_price             = $member_discounts->get_discounted_price( $mpc_rule['regular_price'], $product );
					$mpc_rule['regular_price'] = is_numeric( $regular_price ) ? $regular_price : $mpc_rule['regular_price'];
					$mpc_rule['price']         = $mpc_rule['regular_price'];
				}

				if ( ! empty( $mpc_rule['sale_price'] ) ) {
					$sale_price             = $member_discounts->get_discounted_price( $mpc_rule['sale_price'], $product );
					$mpc_rule['sale_price'] = is_numeric( $sale_price ) ? $sale_price : $mpc_rule['sale_price'];
					$mpc_rule['price']      = $mpc_rule['sale_price'];
				}
			}
		}

		return $mpc_rule;
	}


	/**
	 * Filters the product price once it lands to cart / checkout.
	 *
	 * Measurement Price Calculator prices do not follow WC core prices.
	 * Memberships filters the pricing table, but after the product is added to cart, it may end up double discounted.
	 * Therefore we reverse the original price on the calculated price added to cart.
	 *
	 * @internal
	 *
	 * @since 1.8.8
	 *
	 * @param float|string $price
	 * @param array|\WC_Product $product product or cart item, depending on current filter
	 * @return float|string
	 */
	public function handle_product_cart_item_subtotal_price( $price, $product ) {

		$filter    = current_filter();
		$discounts = wc_memberships()->get_member_discounts_instance();

		// different handling according to filter and WC version before/after WC 3.0+
		if ( 'woocommerce_cart_item_price' === $filter && is_array( $product ) && isset( $product['data'] ) ) {
			// stuff we need in WC <= 2.6
			$raw_price = isset( $product['pricing_item_meta_data']['_price'] ) && is_numeric( $product['pricing_item_meta_data']['_price'] ) ? (float) $product['pricing_item_meta_data']['_price'] : $price;
			$product   = $product['data'];
			$settings  = new \WC_Price_Calculator_Settings( $product );
		} else {
			$raw_price = $price;
			$settings  = new \WC_Price_Calculator_Settings( $product );
		}

		if (    $settings->is_calculator_enabled()
		     && $settings->is_pricing_calculator_enabled()
		     && $discounts->user_has_member_discount( $product ) ) {

			if ( 'woocommerce_cart_item_price' === $filter && 'dimensions' !== $settings->get_calculator_type() ) {
				$price = $settings->pricing_rules_enabled() ? $raw_price : $discounts->get_discounted_price( $raw_price, $product );
			} elseif ( $settings->pricing_rules_enabled() ) {
				$price = $discounts->get_original_price( $raw_price, $product );
			}

			// when filtering cart item price we need to return a string
			if ( 'woocommerce_cart_item_price' === $filter ) {
				$price = wc_price( $price );
			}
		}

		return $price;
	}


	/**
	 * Filters the HTML price when the product has MPC settings.
	 *
	 * @see \WC_Price_Calculator_Product::get_pricing_rules_price_html()
	 *
	 * @internal
	 *
	 * @since 1.8.8
	 *
	 * @param string $price_html the HTML price
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Variation $product
	 * @param string $pricing_label the measurement quantity unit the price refers to
	 * @param bool $quantity_calculator_enabled whether the calculator is in quantity based mode
	 * @param bool $pricing_rules_enabled if pricing table is being used
	 * @return string
	 */
	public function get_measurements_price_html( $price_html, $product , $pricing_label, $quantity_calculator_enabled, $pricing_rules_enabled ) {

		$discounts = wc_memberships()->get_member_discounts_instance();

		if (    $discounts
		     && $discounts->applying_discounts()
		     && $discounts->user_has_member_discount( $product ) ) {

			if ( true === $pricing_rules_enabled ) {

				// remove our own filter to avoid double discounting
				remove_filter( 'wc_measurement_price_calculator_settings_rule', array( $this, 'apply_discounts_to_settings_rule_prices' ), 999 );

				$mpc_settings = new \WC_Price_Calculator_Settings( $product );

				$price = $min_price = $discounts->get_discounted_price( $mpc_settings->get_pricing_rules_minimum_price(),         $product );
				$min_regular_price  = $discounts->get_discounted_price( $mpc_settings->get_pricing_rules_minimum_regular_price(), $product );
				$max_price          = $discounts->get_discounted_price( $mpc_settings->get_pricing_rules_maximum_price(),         $product );

				if ( $price > 0 ) {

					if ( $min_regular_price !== $price && $mpc_settings->pricing_rules_is_on_sale() ) {

						if ( ! $min_price || $min_price !== $max_price ) {
							$from       = wc_price( $mpc_settings->get_pricing_rules_minimum_regular_price() ) . ' - ' . wc_price( $mpc_settings->get_pricing_rules_maximum_regular_price() ) . ' ' . $pricing_label;
							$to         = wc_price( $min_price ) . ' - ' . wc_price( $max_price ) . ' ' . $pricing_label;
							$price_html = $this->get_price_html_from_to( $from, $to, '' ) . $product->get_price_suffix();
						} else {
							$price_html = $this->get_price_html_from_to( wc_price( $mpc_settings->get_pricing_rules_minimum_regular_price() ), $price, $pricing_label ) . $product->get_price_suffix();
						}

					} else {

						$discounted_price = wc_price( $price );

						if ( $min_price !== $max_price ) {
							$discounted_price  .= ' - ' . wc_price( $max_price );
						}

						$price_html = $this->get_price_html_from_to( $price_html, $discounted_price . $pricing_label, '' ) . ' ' . $product->get_price_suffix();
					}

				} elseif ( '' !== $price ) {

					if ( $min_regular_price !== $price && $mpc_settings->pricing_rules_is_on_sale() ) {

						if ( $min_price !== $max_price ) {
							$from       = wc_price( $mpc_settings->get_pricing_rules_minimum_regular_price() ) . ' - ' . wc_price( $mpc_settings->get_pricing_rules_maximum_regular_price() ) . ' ' . $pricing_label;
							$to         = wc_price( 0 ) . ' - ' . wc_price( $max_price ) . ' ' . $pricing_label;
							$price_html = $this->get_price_html_from_to( $from, $to, '' ) . $product->get_price_suffix();
						} else {
							$price_html = $this->get_price_html_from_to( wc_price( $mpc_settings->get_pricing_rules_minimum_regular_price() ), wc_price( 0 ), $pricing_label ) . ' ' . $pricing_label . $product->get_price_suffix();
						}

					} else {

						$discounted_price = wc_price( 0 );

						if ( $min_price !== $max_price ) {
							$discounted_price .= ' - ' . wc_price( $max_price );
						}

						$price_html = $this->get_price_html_from_to( $price_html, $discounted_price . $pricing_label, '' ) . ' ' . $product->get_price_suffix();
					}
				}
			}
		}

		return $price_html;
	}


	/**
	 * Returns the HTML price range for sale and discount prices.
	 *
	 * @since 1.8.8
	 *
	 * @param int|float|string $from price from
	 * @param int|float|string $to price to
	 * @param string $pricing_label the measurement unit label
	 * @return string HTML
	 */
	private function get_price_html_from_to( $from, $to, $pricing_label ) {
		return '<del>' . ( is_numeric( $from ) ? wc_price( $from ) . ' ' . $pricing_label : $from ) . '</del> <ins>' . ( ( is_numeric( $to ) ) ? wc_price( $to ) . ' ' . $pricing_label : $to ) . '</ins>';
	}


}
