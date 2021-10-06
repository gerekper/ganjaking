<?php

namespace ACP\Editing\RequestHandler;

use AC;
use AC\Column;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Editing\ApplyFilter\EditValue;
use ACP\Editing\Editable;
use ACP\Editing\ListScreen;
use ACP\Editing\RequestHandler;
use ACP\Editing\Service;
use ACP\Editing\Settings;

class InlineValues implements RequestHandler {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function handle( Request $request ) {
		$response = new Response\Json();

		$ids = $request->filter( 'ids', [], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );

		if ( empty( $ids ) ) {
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

		foreach ( $ids as $k => $id ) {
			if ( ! $strategy->user_has_write_permission( $id ) ) {
				unset( $ids[ $k ] );
			}
		}

		$column = $list_screen->get_column_by_name( $request->get( 'column' ) );

		$values = $column
			? $this->get_values_by_column( $column, $ids )
			: $this->get_values_by_list_screen( $list_screen, $ids );

		$response
			->set_parameter( 'editable_values', $values )
			->success();
	}

	/**
	 * @param AC\ListScreen $list_screen
	 * @param array         $ids
	 *
	 * @return array
	 */
	private function get_values_by_list_screen( AC\ListScreen $list_screen, array $ids ) {
		$values = [];

		foreach ( $list_screen->get_columns() as $column ) {
			$values[] = $this->get_values_by_column( $column, $ids );
		}

		return array_merge( ...$values );
	}

	/**
	 * @param Column $column
	 * @param array  $ids
	 *
	 * @return array
	 */
	private function get_values_by_column( Column $column, array $ids ) {
		if ( ! $column instanceof Editable ) {
			return [];
		}

		$setting = $column->get_setting( Settings::NAME );

		if ( ! $setting instanceof Settings || ! $setting->is_active() ) {
			return [];
		}

		$service = $column->editing();

		if ( ! $service instanceof Service ) {
			return [];
		}

		$values = [];

		foreach ( $ids as $id ) {
			$value = $service->get_value( $id );

			// Apply Filters
			$value = ( new EditValue( $id, $column ) )->apply_filters( $value );

			// Not editable
			if ( null === $value ) {
				continue;
			}

			// Some non-existing values can be set to false
			if ( false === $value ) {
				$value = '';
			}

			$values[] = [
				'id'          => $id,
				'column_name' => $column->get_name(),
				'value'       => $value,
			];
		}

		return $values;
	}

}