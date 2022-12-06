<?php

namespace ACP\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\Storage\ListColumnOrder;
use AC\Storage\UserColumnOrder;
use AC\Type\ListScreenId;
use ACP\RequestAjaxHandler;
use LogicException;

class ColumnOrderList implements RequestAjaxHandler {

	/**
	 * @var ListColumnOrder
	 */
	private $storage;

	/**
	 * @var UserColumnOrder
	 */
	private $user_storage;

	public function __construct( ListColumnOrder $storage, UserColumnOrder $user_storage ) {
		$this->storage = $storage;
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

		$this->storage->save(
			$id,
			(array) $request->get( 'column_names' )
		);

		$this->user_storage->delete( $id );

		wp_send_json_success();
	}

}