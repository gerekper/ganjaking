<?php

namespace ACP\RequestHandler\Ajax;

use AC\ColumnSize;
use AC\Nonce;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\RequestAjaxHandler;
use LogicException;

class ColumnWidthList implements RequestAjaxHandler {

	/**
	 * @var ColumnSize\ListStorage
	 */
	private $list_storage;

	/**
	 * @var ColumnSize\UserStorage
	 */
	private $user_storage;

	public function __construct( ColumnSize\ListStorage $list_storage, ColumnSize\UserStorage $user_storage ) {
		$this->list_storage = $list_storage;
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

		foreach ( $this->user_storage->all( $id ) as $column_name => $width ) {
			$this->list_storage->save( $id, $column_name, $width );
		}

		$this->user_storage->delete_by_list_id( $id );

		wp_send_json_success();
	}

}