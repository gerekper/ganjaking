<?php

namespace ACA\ACF\Settings\Column;

use AC;
use AC\View;
use ACA\ACF\Column;

/**
 * @property Column $column
 */
class RepeaterDisplay extends AC\Settings\Column {

	const KEY = 'repeater_display';
	const DISPLAY_SUBFIELD = 'subfield';
	const DISPLAY_COUNT = 'count';

	/**
	 * @var string
	 */
	private $repeater_display;

	public function __construct( Column $column ) {
		parent::__construct( $column );
	}

	protected function define_options() {
		return [ self::KEY => self::DISPLAY_SUBFIELD ];
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$setting = $this->create_element( 'select', self::KEY )
		                ->set_attribute( 'data-refresh', 'column' )
		                ->set_options( [
			                self::DISPLAY_SUBFIELD => __( 'Subfield', 'codepress-admin-columns' ),
			                self::DISPLAY_COUNT    => __( 'Number of Rows', 'codepress-admin-columns' ),
		                ] );

		$view = new View( [
			'label'   => __( 'Option', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );

		return $view;
	}

	public function get_dependent_settings() {
		$settings = [];

		switch ( $this->get_repeater_display() ) {
			case self::DISPLAY_SUBFIELD:
				$settings[] = new RepeaterSubField( $this->column );

		}

		return $settings;
	}

	public function get_repeater_display() {
		return $this->repeater_display;
	}

	public function set_repeater_display( $repeater_display ) {
		$this->repeater_display = $repeater_display;

		return $this;
	}

}