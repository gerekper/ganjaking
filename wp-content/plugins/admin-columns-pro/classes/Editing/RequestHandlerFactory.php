<?php

namespace ACP\Editing;

use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\Editing;

class RequestHandlerFactory {

	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function create( Request $request ): ?RequestHandler {
		switch ( $request->get( 'method' ) ) {
			case 'bulk-deletable-rows' :
				$list_screen = $this->storage->find( new ListScreenId( $request->get( 'layout' ) ) );

				return $list_screen instanceof Editing\BulkDelete\ListScreen
					? $list_screen->deletable()->get_query_request_handler()
					: null;
			case 'bulk-editable-rows' :
				$list_screen = $this->storage->find( new ListScreenId( $request->get( 'layout' ) ) );

				return $list_screen instanceof Editing\ListScreen
					? $list_screen->editing()->get_query_request_handler()
					: null;
			default:
				return null;
		}
	}

}