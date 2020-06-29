<?php

namespace ACP\Sorting\Model;

use AC\Column;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;

/**
 * @deprecated 5.2
 */
class RawValue extends AbstractModel implements WarningAware {

	/**
	 * @var string
	 */
	protected $column;

	public function __construct( Column $column, DataType $data_type = null, $show_empty = null ) {
		parent::__construct( $data_type, $show_empty );

		$this->column = $column;
	}

	public function get_sorting_vars() {
		$ids = [];

		foreach ( $this->strategy->get_results() as $id ) {
			$ids[ $id ] = $this->format_value( $this->column->get_raw_value( $id ) );
		}

		return [
			'ids' => ( new Sorter() )->sort( $ids, $this->get_order(), $this->data_type, $this->show_empty ),
		];
	}

	protected function format_value( $value ) {
		return $value;
	}

}