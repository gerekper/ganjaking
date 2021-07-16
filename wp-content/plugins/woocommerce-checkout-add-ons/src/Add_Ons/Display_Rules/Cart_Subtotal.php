<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules;

defined( 'ABSPATH' ) or exit;

/**
 * Cart Subtotal Display Rule Class
 *
 * @since 2.1.0
 */
class Cart_Subtotal extends Display_Rule {


	/** @var string the rule type */
	protected $rule_type = 'cart_subtotal';


	/**
	 * Sets up the rule.
	 *
	 * @since 2.1.0
	 *
	 * @param array $data
	 *     @type array $values
	 */
	public function __construct( $data = [] ) {

		parent::__construct( [
			'property' => __( 'Cart subtotal', 'woocommerce-checkout-add-ons' ),
			'values'   => ! empty( $data['values'] ) ? $data['values'] : [],
			'tooltip'  => __( 'Cart subtotal required to display this add-on. You may enter both minimum and maximum values, or enter one value to use an open-ended rule.', 'woocommerce-checkout-add-ons' ),
			'add_on'   => ! empty( $data['add_on'] ) ? $data['add_on'] : null,
		] );
	}


	/**
	 * Gets property field.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_property_field() {

		return [];
	}


	/**
	 * Gets form fields.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_fields() {

		$minimum = '';
		$maximum = '';

		if ( ! empty( $this->get_values() ) ) {
			list( $minimum, $maximum ) = $this->get_values();
		}

		return [
			'cart_minimum' => [
				'id'          => 'cart_minimum',
				'name'        => 'rules[cart_subtotal][values][0]',
				'type'        => 'text',
				'class'       => 'rules-text rules-range rules-minimum wc_input_price',
				'label'       => __( 'Cart minimum', 'woocommerce-checkout-add-ons' ),
				'placeholder' => __( 'minimum', 'woocommerce-checkout-add-ons' ),
				'text_before' => __( 'is between ', 'woocommerce-checkout-add-ons' ),
				'text_after'  => '',
				'value'       => $minimum,
			],
			'cart_maximum' => [
				'id'          => 'cart_maximum',
				'name'        => 'rules[cart_subtotal][values][1]',
				'type'        => 'text',
				'class'       => 'rules-text rules-range rules-maximum wc_input_price',
				'label'       => __( 'Cart maximum', 'woocommerce-checkout-add-ons' ),
				'placeholder' => __( 'maximum', 'woocommerce-checkout-add-ons' ),
				'text_before' => __( ' and ', 'woocommerce-checkout-add-ons' ),
				'text_after'  => '',
				'value'       => $maximum,
			],
		];
	}


	/**
	 * Evaluates the rule, based on the cart contents.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function evaluate() {

		$should_display = true;

		if ( WC()->cart instanceof \WC_Cart ) {
			$subtotal = WC()->cart->get_subtotal();

			$minimum = '';
			$maximum = '';

			if ( ! empty( $this->get_values() ) ) {
				list( $minimum, $maximum ) = $this->get_values();
			}

			if ( '' !== $minimum && '' !== $maximum ) {
				// between
				$should_display = $minimum <= $subtotal && $subtotal <= $maximum;
			} elseif ( '' !== $minimum ) {
				// higher than
				$should_display = $minimum <= $subtotal;
			} elseif ( '' !== $maximum ) {
				// lower than
				$should_display = $subtotal <= $maximum;
			}
		}

		return $should_display;
	}


	/**
	 * Gets a human readable description.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_description() {

		$description = '';

		$minimum = '';
		$maximum = '';

		if ( ! empty( $this->get_values() ) ) {
			list( $minimum, $maximum ) = $this->get_values();
		}

		if ( '' !== $minimum && '' !== $maximum ) {
			// between
			$description = sprintf(
				/* translators: Placeholders: %1$s - minimum value, %2$s - maximum value, %3$s - <strong> tag, %4$s - </strong> tag */
				__( '%3$sCart subtotal %4$s%1$s - %2$s', 'woocommerce-checkout-add-ons' ),
				get_woocommerce_currency_symbol() . number_format( $minimum, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ),
				get_woocommerce_currency_symbol() . number_format( $maximum, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ),
				'<strong>',
				'</strong>'
			);

		} elseif ( '' !== $minimum ) {
			// higher than
			$description = sprintf(
				/* translators: Placeholders: %1$s - minimum value, %2$s - <strong> tag, %3$s - </strong> tag */
				__( '%2$sCart subtotal %3$s > %1$s', 'woocommerce-checkout-add-ons' ),
				get_woocommerce_currency_symbol() . number_format( $minimum, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ),
				'<strong>',
				'</strong>'
			);

		} elseif ( '' !== $maximum ) {
			// lower than
			$description = sprintf(
				/* translators: Placeholders: %1$s - maximum value, %2$s - <strong> tag, %3$s - </strong> tag */
				__( '%2$sCart subtotal %3$s < %1$s', 'woocommerce-checkout-add-ons' ),
				get_woocommerce_currency_symbol() . number_format( $maximum, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ),
				'<strong>',
				'</strong>'
			);

		}

		return $description;
	}


}
