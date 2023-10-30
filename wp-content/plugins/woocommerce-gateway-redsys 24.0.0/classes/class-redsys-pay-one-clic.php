<?php
/**
 * Class Redsys Pay One Clic
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Redsys Pay One Clic
 */
class Redsys_Pay_One_Clic {

	/**
	 * Contructor.
	 */
	public function __construct() {
		// construct.
	}
	/**
	 * Get Billing Address
	 *
	 * @param int $user_id Userd ID.
	 */
	public function get_billing_address( $user_id ) {
		$billing_address = array(
			'first_name' => get_user_meta( $user_id, 'first_name', true ),
			'last_name'  => get_user_meta( $user_id, 'last_name', true ),
			'company'    => get_user_meta( $user_id, 'billing_company', true ),
			'email'      => get_user_meta( $user_id, 'billing_email', true ),
			'phone'      => get_user_meta( $user_id, 'billing_phone', true ),
			'address_1'  => get_user_meta( $user_id, 'billing_address_1', true ),
			'address_2'  => get_user_meta( $user_id, 'billing_address_2', true ),
			'city'       => get_user_meta( $user_id, 'billing_city', true ),
			'state'      => get_user_meta( $user_id, 'billing_state', true ),
			'postcode'   => get_user_meta( $user_id, 'billing_postcode', true ),
			'country'    => get_user_meta( $user_id, 'billing_country', true ),
		);
		return $billing_address;
	}
	/**
	 * Get Shipping Address
	 *
	 * @param int $user_id Userd ID.
	 */
	public function get_shipping_address( $user_id ) {
		if ( get_user_meta( $user_id, 'first_name', true ) ) {
			$shipping_address = array(
				'first_name' => get_user_meta( $user_id, 'first_name', true ),
				'last_name'  => get_user_meta( $user_id, 'last_name', true ),
				'company'    => get_user_meta( $user_id, 'shipping_company', true ),
				'email'      => get_user_meta( $user_id, 'shipping_email', true ),
				'phone'      => get_user_meta( $user_id, 'shipping_phone', true ),
				'address_1'  => get_user_meta( $user_id, 'shipping_address_1', true ),
				'address_2'  => get_user_meta( $user_id, 'shippingg_address_2', true ),
				'city'       => get_user_meta( $user_id, 'shipping_city', true ),
				'state'      => get_user_meta( $user_id, 'shipping_state', true ),
				'postcode'   => get_user_meta( $user_id, 'shipping_postcode', true ),
				'country'    => get_user_meta( $user_id, 'shipping_country', true ),
			);
		} else {
			$shipping_address = array(
				'first_name' => get_user_meta( $user_id, 'first_name', true ),
				'last_name'  => get_user_meta( $user_id, 'last_name', true ),
				'company'    => get_user_meta( $user_id, 'billing_company', true ),
				'email'      => get_user_meta( $user_id, 'billing_email', true ),
				'phone'      => get_user_meta( $user_id, 'billing_phone', true ),
				'address_1'  => get_user_meta( $user_id, 'billing_address_1', true ),
				'address_2'  => get_user_meta( $user_id, 'billing_address_2', true ),
				'city'       => get_user_meta( $user_id, 'billing_city', true ),
				'state'      => get_user_meta( $user_id, 'billing_state', true ),
				'postcode'   => get_user_meta( $user_id, 'billing_postcode', true ),
				'country'    => get_user_meta( $user_id, 'billing_country', true ),
			);
		}
		return $shippingg_address;
	}
	/**
	 * Create Order
	 *
	 * @param int    $user_id Userd ID.
	 * @param int    $product_id Product ID.
	 * @param int    $qty Quantity.
	 * @param string $gateway_id Gateway ID.
	 */
	public function create_order( $user_id, $product_id, $qty, $gateway_id = 'redsys' ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id          = get_current_user_id();
		$args             = array(
			'customer_id' => $user_id,
		);
		$order            = wc_create_order( $args );
		$billing_address  = $this->get_billing_address( $user_id );
		$shipping_address = $this->get_shipping_address( $user_id );
		$order->add_product( wc_get_product( $product_id ), $qty );
		$order->set_address( $billing_address, 'billing' );
		$order->set_address( $shipping_address, 'shipping' );
		$gateways = WC()->payment_gateways->payment_gateways();
		$order->set_payment_method( $gateways[ $gateway_id ] );
		$order->calculate_totals();
		// Update the status and add a note.
		$order->update_status( 'pending', 'Redsys Pay with 1clic!', true );
		// Save.
		$order->save();

		return $order->get_id();
	}
	/**
	 * Process Payment
	 *
	 * @param int    $order_id Order ID.
	 * @param string $token Token.
	 */
	public function process_payment( $order_id, $token ) {

		$order      = wc_get_order( $order_id );
		$gateway    = new WC_Gateway_Redsys();
		$token_type = 'C';

		if ( 'yes' === $gateway->debug ) {
			$gateway->log->add( 'redsys', 'Order bigger 0' );
		}
		$token_type = get_transient( $order_id . '_redsys_token_type' );
		if ( 'R' === $token_type ) {
			$result = $gateway->pay_with_token_r( $order_id, $token );
			if ( $result ) {
				if ( 'yes' === $gateway->debug ) {
					$gateway->log->add( 'redsys', 'Pago mediante token CORRECTO' );
				}
				return array(
					'result'   => 'success',
					'redirect' => add_query_arg( 'utm_nooverride', '1', $gateway->get_return_url( $order ) ),
				);
			} else {
				if ( 'yes' === $gateway->debug ) {
					$gateway->log->add( 'redsys', 'Pago mediante token FALLIDO' );
				}
				$error = 'We are having trouble charging the card, please try another one';
				do_action( 'redsys_post_payment_error', $order->get_id(), $error );
				wc_add_notice( 'We are having trouble charging the card, please try another one. ', 'error' );
			}
		} else {
			$result = $gateway->pay_with_token_c( $order_id, $token );
			if ( 'success' === $result ) {
				if ( 'yes' === $gateway->debug ) {
					$gateway->log->add( 'redsys', '$result: success' );
				}
				return array(
					'result'   => 'success',
					'redirect' => add_query_arg( 'utm_nooverride', '1', $gateway->get_return_url( $order ) ),
				);
			} elseif ( 'ChallengeRequest' === $result ) {
				if ( 'yes' === $gateway->debug ) {
					$gateway->log->add( 'redsys', '$result: ChallengeRequest' );
				}
				return array(
					'result'   => 'success',
					'redirect' => $order->get_checkout_payment_url( true ) . '#3DSform',
				);
			} elseif ( 'threeDSMethodURL' === $result ) {
				if ( 'yes' === $gateway->debug ) {
					$gateway->log->add( 'redsys', '$result: threeDSMethodURL' );
					$gateway->log->add( 'redsys', '$gateway->notify_url: ' . $gateway->notify_url );
				}
				return array(
					'result'   => 'success',
					'redirect' => $gateway->notify_url . '&threeDSMethodURL=true&order=' . $order_id,
				);
			}
		}
	}
}
/**
 * WCRed_pay
 *
 * @return Redsys_Pay_One_Clic
 */
function WCRed_pay() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return new Redsys_Pay_One_Clic();
}
