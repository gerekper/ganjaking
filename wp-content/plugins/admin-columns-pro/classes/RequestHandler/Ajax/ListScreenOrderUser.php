<?php

namespace ACP\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use ACP\Preference;
use ACP\RequestAjaxHandler;

class ListScreenOrderUser implements RequestAjaxHandler {

	/**
	 * @var Preference\User\TableListOrder
	 */
	private $preference_user;

	public function __construct( Preference\User\TableListOrder $preference_user ) {
		$this->preference_user = $preference_user;
	}

	public function handle(): void {
		$request = new Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		$list_screen_key = $request->get( 'list_screen' );
		$order = $request->filter( 'order', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $order || ! $list_screen_key ) {
			wp_send_json_error();
		}

		$this->preference_user->set( $list_screen_key, $order );

		wp_send_json_success();
	}

}