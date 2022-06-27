<?php

namespace ACP\Editing\RequestHandler;

use AC\Column;
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
use ACP\Editing\Strategy;
use Exception;
use RuntimeException;

class BulkSave implements RequestHandler {

	const SAVE_FAILED = 'failed';
	const SAVE_SUCCESS = 'success';
	const SAVE_NOT_EDITABLE = 'not_editable';

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function handle( Request $request ) {
		$response = new Response\Json();

		$ids = $request->filter( 'ids', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
		$value = $request->get( 'value', false );

		if ( $ids === false || $value === false ) {
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

		$column = $list_screen->get_column_by_name( $request->get( 'column' ) );

		if ( ! $column instanceof Editable ) {
			$response->error();
		}

		$service = $column->editing();

		if ( ! $service ) {
			$response->error();
		}

		$results = [];

		foreach ( $ids as $id ) {
			$error = null;

			$request = new Request();
			$request->get_parameters()->set( 'id', $id );
			$request->get_parameters()->set( 'value', $value );

			try {
				$request->add_middleware( new SaveValue( new ApplyFilter\SaveValue( $id, $column ) ) );

				$status = $this->save_single_value( $request, $strategy, $service, $column );
			} catch ( Exception $e ) {
				$error = $e->getMessage();
				$status = self::SAVE_FAILED;
			}

			$results[] = [
				'id'     => $id,
				'error'  => $error,
				'status' => $status,
			];
		}

		$response
			->set_parameter( 'results', $results )
			->set_parameter( 'total', count( $results ) )
			->success();
	}

	/**
	 * @param Request  $request
	 * @param Strategy $strategy
	 * @param Service  $service
	 * @param Column   $column
	 *
	 * @return string
	 */
	private function save_single_value( Request $request, Strategy $strategy, Service $service, Column $column ) {
		$id = (int) $request->get( 'id' );

		if ( ! $strategy->user_has_write_permission( $id ) ) {
			return self::SAVE_NOT_EDITABLE;
		}

		$edit_value = ( new ApplyFilter\EditValue( $id, $column ) )->apply_filters( $service->get_value( $id ) );

		if ( null === $edit_value ) {
			return self::SAVE_NOT_EDITABLE;
		}

		do_action( 'acp/editing/before_save', $column, $id, $request );

		$result = $service->update( $request );

		// Legacy..
		if ( $service instanceof Model && true !== $result && $service->has_error() ) {
			throw new RuntimeException( $service->get_error()->get_error_message() );
		}

		do_action( 'acp/editing/saved', $column, $id, $request->get( 'value' ) );

		return self::SAVE_SUCCESS;
	}

}