<?php

namespace ACP\Editing\Controller;

use AC;
use AC\Request;
use AC\Response;
use ACP\Editing\Editable;
use ACP\Editing\Preference;

class Single extends Column {

	/**
	 * @var Preference\EditState
	 */
	private $edit_state;

	public function __construct( AC\ListScreenRepository\Storage $storage, Request $request, Preference\EditState $edit_state ) {
		parent::__construct( $storage, $request );

		$this->edit_state = $edit_state;
	}

	public function save_action() {
		$id = $this->request->filter( 'id', null, FILTER_SANITIZE_NUMBER_INT );
		$list_screen = $this->get_list_screen_from_request();
		$column = $this->get_column_from_request();

		$response = new Response\Json();

		if ( ! $id || ! $list_screen || ! $column ) {
			$response->error();
		}

		$model = $this->get_model_from_request();

		if ( ! $list_screen->editing()->user_has_write_permission( $id ) ) {
			$response->error();
		}

		// Can contain strings and array's
		$value = $this->request->get( 'value', '' );

		$model->update( $id, $value );

		if ( $model->has_error() ) {
			$response->set_message( $model->get_error()->get_error_message() )
			         ->error();
		}

		$response
			->set_parameters( [
				'id'            => $id,
				'value'         => $model->get_value( $id ),
				'display_value' => $list_screen->get_display_value_by_column_name( $column->get_name(), $id ),
			] )
			->success();
	}

	public function editability_state_action() {
		$value = $this->request->get( 'value' ) ? 1 : 0;
		$list_screen_key = $this->request->filter( 'list_screen', '', FILTER_SANITIZE_STRING );

		$response = new Response\Json();

		if ( ! $list_screen_key ) {
			$response->error();
		}

		$result = $this->edit_state->set( $list_screen_key, $value );

		if ( ! $result ) {
			$response->error();
		}

		$response->success();
	}

	public function get_editable_values_action() {
		$ids = $this->request->filter( 'ids', [], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
		$list_screen = $this->get_list_screen_from_request();
		$column_name = $this->request->get( 'column' );

		$response = new Response\Json();

		if ( empty( $ids ) || ! $list_screen ) {
			$response->error();
		}

		$strategy = $list_screen->editing();

		foreach ( $ids as $k => $id ) {
			if ( ! $strategy->user_has_write_permission( $id ) ) {
				unset( $ids[ $k ] );
			}
		}

		$values = [];

		foreach ( $list_screen->get_columns() as $column ) {
			if ( ! $column instanceof Editable ) {
				continue;
			}

			// Check if the request is for all columns or a specific one
			if ( $column_name && $column->get_name() !== $column_name ) {
				continue;
			}

			$model = $column->editing();

			if ( ! $model || ! $model->is_active() ) {
				continue;
			}

			foreach ( $ids as $id ) {
				$value = $model->get_value( $id );

				// Not editable
				if ( null === $value ) {
					continue;
				}

				// Some non-existing values van be set to false
				if ( false === $value ) {
					$value = '';
				}

				$values[] = [
					'id'          => $id,
					'column_name' => $column->get_name(),
					'value'       => $value,
				];
			}
		}

		$response
			->set_parameter( 'editable_values', $values )
			->success();
	}

}