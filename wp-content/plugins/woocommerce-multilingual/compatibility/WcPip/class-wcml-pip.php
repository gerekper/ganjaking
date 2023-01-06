<?php

use WCML\Compatibility\WcPip\Helper;

class WCML_Pip implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wcml_send_email_order_id', [ $this, 'wcml_send_email_order_id' ] );
		add_action( 'wc_pip_print', [ $this, 'print_invoice_language' ], 10, 2 );
	}

	public function wcml_send_email_order_id( $order_id ) {
		$pip_order_id = Helper::getPipOrderId();

		if ( $pip_order_id ) {
			$order_id = $pip_order_id;
		}

		return $order_id;
	}

	public function print_invoice_language( $type, $order_id ) {
		$order_language = get_post_meta( $order_id, 'wpml_language', true );

		if ( $order_language ) {
			do_action( 'wpml_switch_language', $order_language );
		}
	}
}
