<?php
/**
 * Class Redsys Pay One Clic
 *
 * @package WooCommerce Redsys Gateway
 * @since 24.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
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
		$this->log = new WC_Logger();
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
	 * Create Subscription For Order
	 *
	 * @param int $order_id Order ID.
	 * @param int $product_id Product ID.
	 * @param int $user_id User ID.
	 */
	public function create_subscription_for_order( $order_id, $product_id, $user_id, $initial_status = 'on-hold' ) {
		// Verificar si WooCommerce Subscriptions está activo.
		if ( ! function_exists( 'wcs_create_subscription' ) ) {
			return false;
		}

		$product = wc_get_product( $product_id );

		// Verificar si el producto es una suscripción.
		if ( ! $product || ! $product->is_type( array( 'subscription', 'variable-subscription' ) ) ) {
			return false;
		}

		$subscription_args = array(
			'order_id'          => $order_id,
			'customer_id'       => $user_id,
			'billing_period'    => WC_Subscriptions_Product::get_period( $product ),
			'billing_interval'  => WC_Subscriptions_Product::get_interval( $product ),
			'start_date'        => current_time( 'mysql' ),
			'next_payment_date' => WC_Subscriptions_Product::get_first_renewal_payment_date( $product, current_time( 'mysql' ) ),
		);

		if ( empty( $initial_status ) ) {
			$initial_status = 'on-hold';
		}

		// Crear la suscripción y establecer el estado inicial.
		$subscription = wcs_create_subscription( $subscription_args );
		if ( is_wp_error( $subscription ) ) {
			return false;
		}

		// Añadir el producto a la suscripción.
		$subscription->add_product( $product );
		$subscription->calculate_totals();

		// Establecer la suscripción como relacionada con el pedido.
		$subscription->set_parent_id( $order_id );

		// Añadimos el método redsys para los cobros automáticos.
		$subscription->set_payment_method( 'redsys' );

		// Guardar y activar la suscripción.
		$subscription->update_status( $initial_status );
		$subscription->save();

		return $subscription->get_id();
	}
	/**
	 * Update Subscription Status
	 *
	 * @param int    $order_id Order ID.
	 * @param string $new_status New Status.
	 */
	public function update_subscription_status( $order_id, $new_status ) {
		if ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			return;
		}
		if ( wcs_order_contains_subscription( $order_id ) ) {
			$subscriptions = wcs_get_subscriptions_for_order( $order_id );
			foreach ( $subscriptions as $subscription ) {
				$subscription->update_status( $new_status );
			}
		}
	}
	/**
	 * Create Order
	 *
	 * @param int    $user_id Userd ID.
	 * @param int    $product_id Product ID.
	 * @param int    $qty Quantity.
	 * @param string $shipping_method Shipping Method.
	 * @param string $gateway_id Gateway ID.
	 */
	public function create_order( $user_id, $product_id, $qty, $shipping_method = '', $gateway_id = 'redsys' ) {

		$this->log->add( 'redsys-pay', 'create_order' );
		$this->log->add( 'redsys-pay', 'user_id: ' . $user_id );
		$this->log->add( 'redsys-pay', 'product_id: ' . $product_id );
		$this->log->add( 'redsys-pay', 'qty: ' . $qty );
		$this->log->add( 'redsys-pay', 'gateway_id: ' . $gateway_id );
		$this->log->add( 'redsys-pay', 'shipping_method: ' . $shipping_method );

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

		$this->log->add( 'redsys-pay', 'shipping_method: ' . $shipping_method );

		if ( ! empty( $shipping_method ) ) {
			list($method_id, $encoded_label, $cost_cents) = explode( '-', $shipping_method );

			$label = base64_decode( $encoded_label ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			$cost  = floatval( $cost_cents ) / 100; // Convierte los centavos a formato decimal.

			$this->log->add( 'redsys-pay', 'method_id: ' . $method_id );
			$this->log->add( 'redsys-pay', 'label: ' . $label );
			$this->log->add( 'redsys-pay', 'cost: ' . $cost );

			// Se asume que $method_id tiene el formato 'method:instance_id'.
			list($method, $instance_id) = explode( ':', $method_id );

			// Recuperar la instancia del método de envío.
			$shipping_method_instance = WC_Shipping_Zones::get_shipping_method( $instance_id );

			if ( $shipping_method_instance ) {
				$item = new WC_Order_Item_Shipping();
				$item->set_method_title( $label );
				$item->set_method_id( $method_id ); // Aquí usamos 'method:instance_id'.
				$item->set_total( $cost );
				$order->add_item( $item );
			}
		}

		$order->calculate_totals();
		// Update the status and add a note.
		$order->update_status( 'pending', 'Redsys Pay with 1clic Button!', true );
		// Save.
		$order->save();

		$order_id        = $order->get_id();
		$subscription_id = $this->create_subscription_for_order( $order_id, $product_id, $user_id, 'on-hold' );

		return $order_id;
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
				$this->update_subscription_status( $order_id, 'active' );
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
