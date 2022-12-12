<?php

namespace ACP\Editing;

use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\Editing;

class RequestHandlerFactory {

	const METHOD_BULK_DELETABLE_ROWS = 'bulk-deletable-rows';
	const METHOD_BULK_EDITABLE_ROWS = 'bulk-editable-rows';

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	/**
	 * @param Request $request
	 *
	 * @return RequestHandler|null
	 */
	public function create( Request $request ) {
		switch ( $request->get( 'method' ) ) {
			case self::METHOD_BULK_DELETABLE_ROWS :
				$list_screen = $this->storage->find( new ListScreenId( $request->get( 'layout' ) ) );

				return $list_screen instanceof Editing\BulkDelete\ListScreen
					? $list_screen->deletable()->get_query_request_handler()
					: null;
			case self::METHOD_BULK_EDITABLE_ROWS :
				$list_screen = $this->storage->find( new ListScreenId( $request->get( 'layout' ) ) );

				return $list_screen instanceof Editing\ListScreen
					? $list_screen->editing()->get_query_request_handler()
					: null;
			default:
				return null;
		}
	}

}