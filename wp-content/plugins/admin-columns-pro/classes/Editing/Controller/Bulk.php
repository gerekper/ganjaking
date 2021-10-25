<?php

namespace ACP\Editing\Controller;

use AC\Response;
use ACP\Editing\Model;
use ACP\Editing\Strategy;

class Bulk extends Column {

	const SAVE_FAILED = 'failed';
	const SAVE_SUCCESS = 'success';
	const SAVE_NOT_EDITABLE = 'not_editable';

	/**
	 * @param int      $id
	 * @param mixed    $value
	 * @param Strategy $strategy
	 * @param Model    $model
	 *
	 * @return string
	 */
	private function save_single_value( $id, $value, Strategy $strategy, Model $model ) {
		if ( ! $strategy->user_has_write_permission( $id ) ) {
			return self::SAVE_NOT_EDITABLE;
		}

		$edit_value = $model->get_value( $id );

		if ( null === $edit_value ) {
			return self::SAVE_NOT_EDITABLE;
		}

		if ( true !== $model->update( $id, $value ) && $model->has_error() ) {
			return self::SAVE_FAILED;
		}

		return self::SAVE_SUCCESS;
	}

	public function save_action() {
		$ids = $this->request->filter( 'ids', false, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
		$value = $this->request->get( 'value', false );

		$response = new Response\Json();

		if ( $ids === false || $value === false ) {
			$response->error();
		}

		$list_screen = $this->get_list_screen_from_request();

		if ( ! $list_screen ) {
			$response->error();
		}

		$model = $this->get_model_from_request();

		if ( ! $model ) {
			$response->error();
		}

		$strategy = $list_screen->editing();
		$results = [];
		$statuses = [];

		foreach ( $ids as $id ) {
			$result = $this->save_single_value( $id, $value, $strategy, $model );

			$data = [
				'id'     => $id,
				'result' => $result,
			];

			if ( ! isset( $statuses[ $result ] ) ) {
				$statuses[ $result ] = 0;
			}

			$statuses[ $result ]++;

			if ( $result === self::SAVE_FAILED ) {
				$data['error'] = $model->get_error()->get_error_message();
			}

			$results[] = $data;
		}

		$response
			->set_parameters( array_count_values( $statuses ) )
			->set_parameter( 'results', $results )
			->set_parameter( 'total', count( $results ) )
			->success();
	}

}