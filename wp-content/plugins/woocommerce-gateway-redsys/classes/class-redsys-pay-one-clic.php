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
		return $shippingg_address;
	}
	/**
	 * Create Order
	 *
	 * @param int $user_id Userd ID.
	 * @param int $product_id Product ID.
	 * @param int $qty Quantity.
	 */
	public function create_order( $user_id, $product_id, $qty ) {

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
		$order->set_payment_method( $gateways['redsys'] );
		$order->calculate_totals();
		// Update the status and add a note.
		$order->update_status( 'pending', 'Redsys Pay with 1clic!', true );
		// Save.
		$order->save();

		return $order->get_id();
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
