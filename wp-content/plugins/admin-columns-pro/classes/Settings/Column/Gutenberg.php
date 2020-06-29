<?php

namespace ACP\Settings\Column;

use AC;
use AC\View;
use ACP;

class Gutenberg extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $gutenberg_display;

	protected function define_options() {
		return [ 'gutenberg_display' => 'count' ];
	}

	public function create_view() {
		$setting = $this->create_element( 'select' );

		$setting->set_attribute( 'data-refresh', 'column' )
		        ->set_options( [
			        'count'     => __( 'Block Count', 'codepress-admin-columns' ),
			        'structure' => __( 'Block Structure', 'codepress-admin-columns' ),
		        ] );

		$view = new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );

		return $view;
	}

	public function get_dependent_settings() {
		$settings = [];

		if ( 'structure' === $this->get_gutenberg_display() ) {
			$settings[] = new AC\Settings\Column\NumberOfItems( $this->column );
		}

		return $settings;
	}

	public function get_gutenberg_display() {
		return $this->gutenberg_display;
	}

	public function set_gutenberg_display( $flex_display ) {
		$this->gutenberg_display = $flex_display;

		return $this;
	}

}