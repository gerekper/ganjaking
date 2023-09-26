<?php

namespace ACA\WC\Settings\Product;

use AC;
use AC\View;

class UseIcon extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var bool
	 */
	private $use_icon;

	protected function define_options() {
		return [ 'use_icon' => '' ];
	}

	public function get_dependent_settings() {
		$setting = [];

		if ( ! $this->get_use_icon() ) {
			$setting[] = new AC\Settings\Column\StringLimit( $this->column );
		}

		return $setting;
	}

	public function create_view() {

		$setting = $this->create_element( 'radio' )
		                ->set_attribute( 'data-refresh', 'column' )
		                ->set_options( [
			                '1' => __( 'Yes' ),
			                ''  => __( 'No' ),
		                ] );

		return new View( [
			'label'   => __( 'Use an icon?', 'codepress-admin-columns' ),
			'tooltip' => __( 'Use an icon instead of text for displaying the note.', 'codepress-admin-columns' ),
			'setting' => $setting,
		] );
	}

	/**
	 * @return bool
	 */
	public function get_use_icon() {
		return $this->use_icon;
	}

	/**
	 * @param bool $use_icon
	 *
	 * @return true
	 */
	public function set_use_icon( $use_icon ) {
		$this->use_icon = $use_icon;

		return true;
	}

	/**
	 * @param string $value
	 * @param int    $post_id
	 *
	 * @return string
	 */
	public function format( $value, $post_id ) {
		if ( $this->get_use_icon() && $value ) {
			$icon = ac_helper()->icon->dashicon( [ 'icon' => 'media-text', 'class' => 'gray' ] );
			$value = ac_helper()->html->tooltip( $icon, $value );
		}

		return $value;
	}

}