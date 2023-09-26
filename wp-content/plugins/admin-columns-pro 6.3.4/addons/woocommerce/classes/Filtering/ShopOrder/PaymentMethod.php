<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACP;
use WC_Payment_Gateway;

class PaymentMethod extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$options = [];

		/* @var WC_Payment_Gateway[] $gateways */
		$gateways = WC()->payment_gateways()->payment_gateways();

		foreach ( $gateways as $gateway ) {
			if ( 'yes' === $gateway->enabled ) {
				$options[ $gateway->get_title() ] = $gateway->get_title();
			}
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}