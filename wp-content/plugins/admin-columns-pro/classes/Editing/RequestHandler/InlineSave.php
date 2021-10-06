<?php

namespace ACP\Editing\RequestHandler;

use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Editing\ApplyFilter;
use ACP\Editing\Editable;
use ACP\Editing\ListScreen;
use ACP\Editing\Middleware\SaveValue;
use ACP\Editing\Model;
use ACP\Editing\RequestHandler;
use ACP\Editing\Service;
use Exception;

class InlineSave implements RequestHandler {

	/**
	 * @var Storage;
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function handle( Request $request ) {

		$response = new Response\Json();

		$id = $request->filter( 'id', null, FILTER_SANITIZE_NUMBER_INT );

		if ( ! $id ) {
			$response->error();
		}

		$list_id = $request->get( 'layout' );

		if ( ! ListScreenId::is_valid_id( $list_id ) ) {
			$response->error();
		}

		$list_screen = $this->storage->find( new ListScreenId( $list_id ) );

		if ( ! $list_screen instanceof ListScreen ) {
			$response->error();
		}

		$strategy = $list_screen->editing();

		if ( ! $strategy ) {
			$response->error();
		}

		if ( ! $strategy->user_has_write_permission( $id ) ) {
			$response->error();
		}

		$column = $list_screen->get_column_by_name( $request->get( 'column' ) );

		if ( ! $column instanceof Editable ) {
			$response->error();
		}

		$service = $column->editing();

		if ( ! $service instanceof Service ) {
			$response->error();
		}

		try {
			$request->add_middleware( new SaveValue( new ApplyFilter\SaveValue( $id, $column ) ) );

			$service->update( $request );

			do_action( 'acp/editing/saved', $column, $id, $request->get( 'value' ) );
		} catch ( Exception $e ) {
			$response->set_message( $e->getMessage() )
			         ->error();
		}

		// Legacy error handling..
		if ( $service instanceof Model && $service->has_error() ) {
			$response->set_message( $service->get_error()->get_error_message() )
			         ->error();
		}

		$edit_value = ( new ApplyFilter\EditValue( $id, $column ) )->apply_filters( $service->get_value( $id ) );

		$response
			->set_parameters( [
				'id'            => $id,
				'value'         => $edit_value,
				'display_value' => $list_screen->get_display_value_by_column_name( $column->get_name(), $id ),
			] )
			->success();
	}

}