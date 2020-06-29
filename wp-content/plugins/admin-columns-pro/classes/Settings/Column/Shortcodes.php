<?php

namespace ACP\Settings\Column;

use AC;
use AC\View;

class Shortcodes extends AC\Settings\Column {

	/**
	 * @var bool
	 */
	private $shortcode;

	protected function define_options() {
		return [ 'shortcode' ];
	}

	public function create_view() {

		$setting = $this->create_element( 'select' )
		                ->set_options( $this->get_shortcode_options() );

		$view = new View( [
			'label'   => __( 'Shortcode', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );

		return $view;
	}

	/**
	 * @return array
	 */
	private function get_shortcode_options() {
		global $shortcode_tags;

		$shortcode_keys = array_keys( $shortcode_tags );

		$options = array_combine( $shortcode_keys, $shortcode_keys );
		asort( $options );

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_shortcode() {
		return $this->shortcode;
	}

	/**
	 * @param string $shortcode
	 */
	public function set_shortcode( $shortcode ) {
		$this->shortcode = $shortcode;
	}

}