<?php

namespace ACP;

use AC;

abstract class Model {

	/**
	 * @var AC\Column
	 */
	protected $column;

	/**
	 * @var string
	 */
	private $data_type = 'string';

	/**
	 * @return bool
	 */
	abstract public function is_active();

	public function __construct( AC\Column $column ) {
		$this->column = $column;
	}

	/**
	 * @return AC\Column
	 */
	public function get_column() {
		return $this->column;
	}

	/**
	 * @param string $data_type
	 *
	 * @return $this
	 */
	public function set_data_type( $data_type ) {
		$data_type = strtolower( $data_type );

		if ( in_array( $data_type, [ 'string', 'numeric', 'date' ] ) ) {
			$this->data_type = $data_type;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_data_type() {
		return $this->data_type;
	}

}