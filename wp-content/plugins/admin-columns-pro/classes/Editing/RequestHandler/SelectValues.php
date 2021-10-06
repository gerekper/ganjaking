<?php

namespace ACP\Editing\RequestHandler;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Editing\ListScreen;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\RemoteOptions;
use ACP\Editing\RequestHandler;
use ACP\Editing\Service;
use ACP\Editing\ServiceFactory;

class SelectValues implements RequestHandler {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function handle( Request $request ) {
		$response = new Response\Json();

		$service = $this->get_service_from_request( $request );

		if ( ! $service ) {
			$response->error();
		}

		switch ( true ) {
			case $service instanceof RemoteOptions:
				$options = $service->get_remote_options(
					$request->filter( 'item_id', null, FILTER_SANITIZE_NUMBER_INT )
				);

				$select = new AC\Helper\Select\Response( $options, false );
				break;
			case $service instanceof PaginatedOptions:
				$options = $service->get_paginated_options(
					$request->filter( 'searchterm' ),
					$request->filter( 'page', 1, FILTER_SANITIZE_NUMBER_INT ),
					$request->filter( 'item_id', null, FILTER_SANITIZE_NUMBER_INT )
				);
				$has_more = ! $options->is_last_page();

				$select = new AC\Helper\Select\Response( $options, $has_more );
				break;
			default:
				$response->error();
		}

		$response
			->set_parameters( $select() )
			->success();
	}

	/**
	 * @param Request $request
	 *
	 * @return Service|null
	 */
	private function get_service_from_request( Request $request ) {
		$list_id = $request->get( 'layout' );

		if ( ! ListScreenId::is_valid_id( $list_id ) ) {
			return null;
		}

		$list_screen = $this->storage->find( new ListScreenId( $list_id ) );

		if ( ! $list_screen instanceof ListScreen ) {
			return null;
		}

		$strategy = $list_screen->editing();

		if ( ! $strategy ) {
			return null;
		}

		$column = $list_screen->get_column_by_name( $request->get( 'column' ) );

		if ( ! $column ) {
			return null;
		}

		return ServiceFactory::create( $column );
	}

}