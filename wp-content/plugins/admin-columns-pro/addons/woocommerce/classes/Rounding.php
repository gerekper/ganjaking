<?php

namespace ACA\WC;

use AC;
use AC\Ajax;

final class Rounding implements AC\Registerable {

	public function register() {
		$this->get_ajax_handler()->register();
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler->set_action( 'acp-rounding' )
		        ->set_callback( [ $this, 'ajax_send_feedback' ] );

		return $handler;
	}

	public function ajax_send_feedback() {
		$price = filter_input( INPUT_GET, 'price' );
		$decimals = filter_input( INPUT_GET, 'decimals' );

		$rounding = new Helper\Price\Rounding();

		switch ( filter_input( INPUT_GET, 'type' ) ) {

			case 'roundup':
				echo $rounding->up( $price, $decimals );
				exit;

			case 'rounddown':
				echo $rounding->down( $price, $decimals );
				exit;

			default :
				echo $price;
				exit;
		}
	}
}