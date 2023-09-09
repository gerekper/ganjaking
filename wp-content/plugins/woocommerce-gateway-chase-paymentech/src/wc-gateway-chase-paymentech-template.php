<?php
/**
 * WooCommerce Chase Paymentech
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Template Function Overrides
 *
 * @since 1.0
 * @version 1.0
 */

defined( 'ABSPATH' ) or exit;


if ( ! function_exists( 'woocommerce_chase_paymentech_payment_fields' ) ) {

	/**
	 * Pluggable function to render the checkout page iframe
	 *
	 * @since 1.0
	 * @param WC_Gateway_Chase_Paymentech $gateway gateway object
	 * @param string $url the iframe url
	 * @param array $params the iframe url parameters
	 */
	function woocommerce_chase_paymentech_payment_fields( $gateway, $url = '', $params = array() ) {

		// tokenization is allowed if tokenization is enabled on the gateway and the orbital connection settings are configured
		$tokenization_allowed = $gateway->tokenization_enabled();

		// on the pay page there is no way of creating an account, so disallow tokenization for guest customers
		if ( $tokenization_allowed && is_checkout_pay_page() && ! is_user_logged_in() ) {
			$tokenization_allowed = false;
		}

		$tokens = array();
		$default_new_card = true;

		$tokenized_payment_method_selected = $gateway->get_payment_tokens_handler()->tokenized_payment_method_selected();

		if ( $tokenization_allowed && is_user_logged_in() ) {

			$tokens = $gateway->get_payment_tokens_handler()->get_tokens( get_current_user_id() );

			foreach ( $tokens as $token ) {
				if ( 0 !== $tokenized_payment_method_selected && ( ! isset( $_REQUEST['should_tokenize'] ) || 'no' === $_REQUEST['should_tokenize'] ) && $token->is_default() ) {
					// if should tokenize payment method, then we want the 'new card' option to be selected
					$default_new_card = false;
					break;
				}
			}
		}

		if ( 0 < $tokenized_payment_method_selected ) {
			$default_new_card = false;
		}

		$order_id = ! empty( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : null;

		// can't call tokenization_forced() in the AJAX context because it is not recognized as the order pay page
		if ( $order_id && wp_doing_ajax() ) {

			$tokenization_forced = false;

			// Subscriptions
			if ( wc_chase_paymentech()->get_gateway()->supports_subscriptions() ) {
				$tokenization_forced = function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order_id );
			}

			// Pre-Orders
			if ( ! $tokenization_forced && wc_chase_paymentech()->get_gateway()->supports_pre_orders() ) {

				$order_contains_pre_order = is_callable( 'WC_Pre_Orders_Order::order_contains_pre_order' ) && \WC_Pre_Orders_Order::order_contains_pre_order( $order_id );
				$pre_order_product        = $order_contains_pre_order && is_callable( 'WC_Pre_Orders_Order::get_pre_order_product' ) ? \WC_Pre_Orders_Order::get_pre_order_product( $order_id ) : null;

				if ( $order_contains_pre_order && ! empty( $pre_order_product ) ) {
					$tokenization_forced = is_callable( 'WC_Pre_Orders_Product::product_is_charged_upon_release' ) && \WC_Pre_Orders_Product::product_is_charged_upon_release( $pre_order_product );
				}
			}

		} else {

			$tokenization_forced = $gateway->get_payment_tokens_handler()->tokenization_forced();
		}

		// load the payment fields template file
		wc_get_template(
			'checkout/chase-paymentech-payment-fields.php',
			array(
				'iframe_src'                        => $url && ! empty( $params ) ? add_query_arg( urlencode_deep( $params ), $url ) : '',
				'order_id'                          => isset( $GLOBALS['wp']->query_vars['order-pay'] ) ? absint( $GLOBALS['wp']->query_vars['order-pay'] ) : $_REQUEST['order_id'],
				'test_mode'                         => $gateway->is_test_environment(),
				'gateway_description'               => $gateway->get_description(),
				'tokens'                            => $tokens,
				'tokenization_allowed'              => $tokenization_allowed,
				'tokenization_forced'               => $tokenization_forced,
				'default_new_card'                  => $default_new_card,
				'should_tokenize_payment_method'    => $gateway->get_payment_tokens_handler()->should_tokenize(),
				'certification_mode'                => $gateway->is_certification_mode(),
				'tokenized_payment_method_selected' => $tokenized_payment_method_selected,
			),
			'',
			$gateway->get_plugin()->get_plugin_path() . '/templates/'
		);

	}

}
