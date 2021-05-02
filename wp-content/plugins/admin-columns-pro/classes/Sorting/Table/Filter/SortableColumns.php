<?php

namespace ACP\Sorting\Table\Filter;

use AC\Column;
use AC\ColumnRepository;
use ACP\Sorting;
use ACP\Sorting\ModelFactory;

class SortableColumns implements ColumnRepository\Filter {

	/**
	 * @var ModelFactory
	 */
	private $model_factory;

	public function __construct( ModelFactory $model_factory ) {
		$this->model_factory = $model_factory;
	}

	public function filter( $columns ) {
		return array_filter( $columns, [ $this, 'is_active' ] );
	}

	private function is_active( Column $column ) {
		$setting = $column->get_setting( 'sort' );

		if ( ! $setting instanceof Sorting\Settings ) {
			return false;
		}

		if ( ! $this->model_factory->create( $column ) ) {
			return false;
		}

		return $setting->is_active();
	}

}