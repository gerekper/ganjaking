<?php

namespace ACP\Filtering;

use ACP;

abstract class Strategy extends ACP\Strategy {

	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * @param Model $model
	 */
	public function __construct( Model $model ) {
		$this->model = $model;
	}

	/**
	 * @return Model
	 */
	public function get_model() {
		return $this->model;
	}

	/**
	 * @param string $field
	 *
	 * @return array|false
	 */
	abstract public function get_values_by_db_field( $field );

	/**
	 * Used to add a callback for handling filter request
	 * @return void
	 */
	abstract public function handle_request();

}