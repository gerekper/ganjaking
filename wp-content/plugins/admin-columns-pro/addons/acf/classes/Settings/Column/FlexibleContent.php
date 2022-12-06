<?php

namespace ACA\ACF\Settings\Column;

use AC;
use AC\View;
use ACA\ACF\Column;

/**
 * @property Column $column
 */
class FlexibleContent extends AC\Settings\Column {

	const NAME = 'flex_display';

	/**
	 * @var string
	 */
	private $flex_display;

	protected function define_options() {
		return [ self::NAME => 'count' ];
	}

	public function create_view() {
		$setting = $this->create_element( 'select' );

		$setting->set_options( [
			'count'     => __( 'Layout Type Count', 'codepress-admin-columns' ),
			'structure' => __( 'Layout Structure', 'codepress-admin-columns' ),
		] );

		$view = new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );

		return $view;
	}

	public function get_flex_display() {
		return $this->flex_display;
	}

	public function set_flex_display( $flex_display ) {
		$this->flex_display = $flex_display;

		return $this;
	}

}