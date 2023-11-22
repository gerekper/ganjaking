<?php

namespace WCML\PaymentGateways;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use WPML\LIB\WP\Hooks;

use function WPML\FP\spreadArgs;

class BlockHooks implements \IWPML_Action {

	/**
	 * @var \woocommerce_wpml
	 */
	private $woocommerce_wpml;

	public function __construct( \woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		Hooks::onAction( 'woocommerce_blocks_payment_method_type_registration', PHP_INT_MAX )
			->then( spreadArgs( [ $this, 'translateSettings' ] ) );
	}

	/**
	 * @param PaymentMethodRegistry $registry
	 */
	public function translateSettings( $registry ) {
		if ( $this->woocommerce_wpml->gateways ) {
			foreach ( $registry->get_all_registered() as $gatewayId => $gateway ) {
				Hooks::onFilter( 'option_woocommerce_' . $gatewayId . '_settings' )
					->then( spreadArgs( function( $settings ) use ( $gatewayId ) {
						foreach ( [ 'title', 'description' ] as $name ) {
							if ( isset( $settings[ $name ] ) ) {
								$settings[ $name ] = $this->woocommerce_wpml->gateways->get_translated_gateway_string( $settings[ $name ], $gatewayId, $name );
							}
						}

						return $settings;
					} ) );
			}
		}
	}

}
