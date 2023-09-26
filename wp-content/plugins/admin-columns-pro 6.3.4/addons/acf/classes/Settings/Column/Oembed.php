<?php

namespace ACA\ACF\Settings\Column;

use AC;
use AC\View;

class Oembed extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $oembed;

	protected function define_options() {
		return [ 'oembed' ];
	}

	public function format( $value, $original_value ) {
		if ( 'video' === $this->get_oembed() ) {
			$value = wp_oembed_get( $value, [
				'width'  => 200,
				'height' => 200,
			] );
		}

		return $value;
	}

	public function create_view() {
		$select = $this->create_element( 'select' );
		$select->set_options( [
			''      => __( 'Url' ), // default
			'video' => __( 'Video' ),
		] );

		$view = new View( [
			'label'   => __( 'Display format', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_oembed() {
		return $this->oembed;
	}

	/**
	 * @param string $display
	 *
	 * @return $this
	 */
	public function set_oembed( $display ) {
		$this->oembed = $display;

		return $this;
	}

}