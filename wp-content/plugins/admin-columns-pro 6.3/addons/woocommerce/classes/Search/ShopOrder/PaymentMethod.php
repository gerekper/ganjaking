<?php

namespace ACA\WC\Search\ShopOrder;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use WC_Payment_Gateway;

class PaymentMethod extends Comparison\Meta implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, '_payment_method_title', MetaType::POST );
	}

	public function get_values() {
		$enabled = [];
		$disabled = [];

		foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {
			/**
			 * @var WC_Payment_Gateway $gateway
			 */
			if ( 'yes' === $gateway->enabled ) {
				$enabled[ $gateway->get_title() ] = $gateway->get_title();
			} else {
				$disabled[ $gateway->get_title() ] = sprintf( '%s (%s)', $gateway->get_title(), __( 'disabled', 'codepress-admin-columns' ) );
			}
		}

		natcasesort( $enabled );
		natcasesort( $disabled );

		$options = array_merge( $enabled, $disabled );

		return AC\Helper\Select\Options::create_from_array( $options );
	}

}