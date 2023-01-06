<?php

use WPML\FP\Fns;

class WCML_Payment_Method_Filter {
	/** @var array  */
	private $payment_gateway_cache = [];

	public function add_hooks() {
		add_filter(
			'woocommerce_order_get_payment_method_title',
			Fns::withoutRecursion( Fns::identity(), [ $this, 'payment_method_string' ] ),
			10,
			2
		);
	}

	public function payment_method_string( $title, $object ) {

		if ( ! empty( $title ) && $object->get_id() ) {
			$payment_gateway = $this->get_payment_gateway( $object->get_id() );

			if ( isset( $_POST['payment_method'] ) && $payment_gateway->id !== $_POST['payment_method'] && WC()->payment_gateways() ) {
				$payment_gateways = WC()->payment_gateways()->payment_gateways();
				if ( isset( $payment_gateways[ $_POST['payment_method'] ] ) ) {
					$payment_gateway = $payment_gateways[ $_POST['payment_method'] ];
				}
			}

			if ( $payment_gateway ) {
				$settings = maybe_unserialize( get_option( 'woocommerce_' . $payment_gateway->id . '_settings' ) );

				$title = apply_filters(
					'wpml_translate_single_string',
					! empty( $settings['title'] ) ? $settings['title'] : $payment_gateway->title,
					'admin_texts_woocommerce_gateways',
					$payment_gateway->id . '_gateway_title'
				);

				if ( $title === $payment_gateway->title ) {
					if ( 'cheque' === $payment_gateway->id && $title === $payment_gateway->title ) {
						$title = _x( $payment_gateway->title, 'Check payment method', 'woocommerce' );
					} else {
						$title = __( $payment_gateway->title, 'woocommerce' );
					}
				}
			}
		}

		return $title;
	}

	/**
	 * @param int $object_id
	 *
	 * @return bool|WC_Payment_Gateway
	 */
	private function get_payment_gateway( $object_id ) {
		if ( ! array_key_exists( $object_id, $this->payment_gateway_cache ) ) {
			$payment_gateway = wc_get_payment_gateway_by_order( $object_id );
			$this->payment_gateway_cache[ $object_id ] = $payment_gateway;
		}

		return $this->payment_gateway_cache[ $object_id ];
	}
}
