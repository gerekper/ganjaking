<?php

namespace ACA\WC\Editing\ShopOrder;

use ACP;
use ACP\Editing\Value\UpdateData;
use ACP\Editing\View;
use WC_Payment_Gateway;

class PaymentMethod implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		$payment_gateways = WC()->payment_gateways()->payment_gateways();
		$options = [];
		/**
		 * @var WC_Payment_Gateway $gateway
		 */
		foreach ( $payment_gateways as $key => $gateway ) {
			$options[ $key ] = $gateway->get_title();
		}

		return new ACP\Editing\View\Select( $options );
	}

	public function get_value( int $id ) {
		return wc_get_order( $id )->get_payment_method();
	}

	public function update( int $id, $data ): void {
		$order = wc_get_order( $id );
		$order->set_payment_method( $data );

		$order->save();
	}

}
