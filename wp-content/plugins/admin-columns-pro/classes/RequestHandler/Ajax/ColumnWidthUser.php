<?php

namespace ACP\RequestHandler\Ajax;

use AC\ColumnSize;
use AC\Nonce;
use AC\Request;
use AC\Type\ColumnWidth;
use AC\Type\ListScreenId;
use ACP\RequestAjaxHandler;
use InvalidArgumentException;
use LogicException;

class ColumnWidthUser implements RequestAjaxHandler {

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

		try {
			$width = new ColumnWidth( 'px', (int) $request->filter( 'width', 0, FILTER_VALIDATE_INT ) );
		} catch ( InvalidArgumentException $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		$this->user_storage->save(
			$id,
			(string) $request->get( 'column_name' ),
			$width
		);

		wp_send_json_success();
	}

}