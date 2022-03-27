<?php

namespace ACP\RequestHandler\Ajax;

use AC\ColumnSize;
use AC\Nonce;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\RequestAjaxHandler;
use LogicException;

class ColumnWidthUserReset implements RequestAjaxHandler {

	/**
	 * @var ColumnSize\UserStorage
	 */
	private $user_storage;

	public function __construct( ColumnSize\UserStorage $user_storage ) {
		$this->user_storage = $user_storage;
	}

	public function handle() {
		$request = new Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		try {
			$id = new ListScreenId( $request->get( 'list_id' ) );
		} catch ( LogicException $e ) {
			wp_send_json_error();
		}

		$this->user_storage->delete(
			$id,
			(string) $request->get( 'column_name' )
		);

		wp_send_json_success();
	}

}