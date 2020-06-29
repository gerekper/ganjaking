<?php

namespace ACP\Filtering\Model;

use AC;
use ACP\Filtering\Model;
use ACP\Filtering\Settings;

class Delegated extends Model {

	/**
	 * @var string Dropdown HTML attribute id
	 */
	private $dropdown_attr_id;

	/**
	 * @param AC\Column $column
	 * @param string    $dropdown_attr_id
	 */
	public function __construct( AC\Column $column, $dropdown_attr_id = null ) {
		parent::__construct( $column );

		$this->dropdown_attr_id = $dropdown_attr_id;
	}

	public function get_filtering_vars( $vars ) {
		return $vars;
	}

	public function get_filtering_data() {
		return [];
	}

	public function register_settings() {
		$this->column->add_setting( new Settings\Delegated( $this->column ) );
	}

	public function get_dropdown_attr_id() {
		return $this->dropdown_attr_id;
	}

	/**
	 * @param string $dropdown_attr_id
	 *
	 * @deprecated 4.2.3
	 */
	public function set_dropdown_attr_id( $dropdown_attr_id ) {
		_deprecated_function( __METHOD__, '4.2.3' );

		$this->dropdown_attr_id = $dropdown_attr_id;
	}

}