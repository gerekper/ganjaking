<?php

namespace ACP\Sorting;

use AC\Column;
use ACP;
use ACP\Sorting\Type\DataType;

/**
 * @deprecated 5.2
 */
class Model extends AbstractModel {

	/**
	 * @var Column
	 */
	protected $column;

	/**
	 * @var string
	 */
	protected $orderby;

	public function __construct( Column $column, DataType $data_type = null, $show_empty = null ) {
		parent::__construct( $data_type, $show_empty );

		$this->column = $column;
	}

	/**
	 * @param string $data_type_value
	 */
	public function set_data_type( $data_type_value ) {
		$this->data_type = new DataType( $data_type_value );
	}

	/**
	 * Get the sorting vars
	 * @return array
	 * @since 4.0
	 */
	public function get_sorting_vars() {
		if ( $this->orderby ) {
			return [
				'orderby' => $this->orderby,
			];
		}

		return [
			'ids' => ( new Sorter() )->sort( $this->get_raw_values(), $this->get_order(), $this->data_type, $this->show_empty ),
		];
	}

	/**
	 * @return array
	 */
	public function get_raw_values() {
		$values = [];

		foreach ( $this->strategy->get_results() as $id ) {
			$values[ $id ] = $this->column->get_raw_value( $id );
		}

		return $values;
	}

	/**
	 * @param string $orderby
	 *
	 * @return $this
	 * @deprecated 5.2
	 */
	public function set_orderby( $orderby ) {
		_deprecated_function( __METHOD__, '5.2', 'ACP\Sorting\Model\OrderBy' );

		$this->orderby = $orderby;

		return $this;
	}

}