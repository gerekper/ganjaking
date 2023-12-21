<?php
/**
 * Dynamic select control class.
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Controls;

use Elementor\Control_Select2;

defined( 'ABSPATH' ) || die();

class Select2 extends Control_Select2 {

	/**
	 * Control identifier
	 */
	const TYPE = 'ha_advanced_select2';

	/**
	 * Set control type.
	 */
	public function get_type() {
		return self::TYPE;
	}

	/**
	 * Get select2 control default settings.
	 *
	 * Retrieve the default settings of the select2 control. Used to return the
	 * default settings while initializing the select2 control.
	 *
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options'        => [],
			'multiple'       => false,
			'sortable'       => false,
			'dynamic_params' => [],
			'select2options' => [],
		];
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {
		if ( $this->get_settings( 'sortable' ) ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
	}
}
