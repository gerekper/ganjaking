<?php

namespace ACP\Controller;

use AC;
use AC\Registrable;
use AC\Storage\ListScreenOrder;

class AjaxRequestListScreenOrder implements Registrable {

	/**
	 * @var ListScreenOrder
	 */
	private $list_screen_order;

	public function __construct( ListScreenOrder $order ) {
		$this->list_screen_order = $order;
	}

	public function register() {
		$this->get_ajax_handler()->register();
	}

	private function get_ajax_handler() {
		$handler = new AC\Ajax\Handler();
		$handler->set_action( 'acp_update_layout_order' )
		        ->set_callback( [ $this, 'ajax_update_list_screen_order' ] );

		return $handler;
	}

	public function ajax_update_list_screen_order() {
		$this->get_ajax_handler()->verify_request();

		$list_screen_key = filter_input( INPUT_POST, 'list_screen' );
		$order = filter_input( INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $order ) {
			wp_die();
		}

		$this->list_screen_order->set( $list_screen_key, $order );

		wp_send_json_success();
	}
}