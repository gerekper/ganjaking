<?php

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Capabilities;
use AC\Request;
use ACP\RequestAjaxHandler;

class ListScreenOrder implements RequestAjaxHandler {

	/**
	 * @var AC\Storage\ListScreenOrder
	 */
	private $list_screen_order;

	public function __construct( AC\Storage\ListScreenOrder $order ) {
		$this->list_screen_order = $order;
	}

	public function handle() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		$request = new Request();

		if ( ! ( new AC\Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		$list_screen_key = $request->get( 'list_screen' );
		$order = $request->filter( 'order', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $order || ! $list_screen_key ) {
			wp_send_json_error();
		}

		$this->list_screen_order->set( $list_screen_key, $order );

		wp_send_json_success();
	}
}