<?php

namespace ACP\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\Storage\UserColumnOrder;
use AC\Type\ListScreenId;
use ACP\RequestAjaxHandler;
use LogicException;

class ColumnOrderUser implements RequestAjaxHandler {

	/**
	 * @var UserColumnOrder
	 */
	private $user_storage;

	public function __construct( UserColumnOrder $user_storage ) {
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

		$this->user_storage->save(
			$id,
			(array) $request->get( 'column_names' )
		);

		wp_send_json_success();
	}

}