<?php

namespace ACA\WC;

use AC;

/**
 * @since 3.0
 */
abstract class Field {

	/**
	 * @var AC\Column
	 */
	protected $column;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @return string
	 */
	abstract public function set_label();

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	abstract public function get_value( $id );

	/**
	 * @param AC\Column $column
	 */
	public function __construct( AC\Column $column ) {
		$this->column = $column;

		$this->set_label();
	}

	/**
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return get_class( $this );
	}

}