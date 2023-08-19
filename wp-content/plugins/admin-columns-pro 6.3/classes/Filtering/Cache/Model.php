<?php

namespace ACP\Filtering\Cache;

use ACP\Filtering;

class Model extends Filtering\Cache {

	/**
	 * @var Filtering\Model
	 */
	protected $model;

	public function __construct( Filtering\Model $model ) {
		$this->model = $model;

		parent::__construct( $this->get_key() );
	}

	protected function get_key() {
		$column = $this->model->get_column();

		return $column->get_list_screen()->get_storage_key() . $column->get_name();
	}

	public function put_if_expired() {
		if ( $this->is_expired() ) {
			$seconds = apply_filters( 'acp/filtering/cache/seconds', 300, $this->model );

			$this->put( $this->model->get_filtering_data(), $seconds );
		}
	}

}