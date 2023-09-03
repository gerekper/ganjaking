<?php
/**
 * WCS_PB_Abstract_Module class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract class used as the foundation for PB modules.
 *
 * @version  5.8.0
 */
abstract class WCS_PB_Abstract_Module {

	/**
	 * Sub-modules to instantiate.
	 * @var array
	 */
	protected $submodules = array();

	/**
	 * Handles module initialization.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->load_component( 'core' );
		$this->register_submodules();
		$this->initialize_submodules();
	}

	/**
	 * Include submodules.
	 *
	 * @return void
	 */
	protected function register_submodules() {}

	/**
	 * Initialize submodules.
	 *
	 * @return void
	 */
	protected function initialize_submodules() {

		$submodules = array();

		foreach ( $this->submodules as $submodule ) {
			$submodules[] = new $submodule();
		}

		$this->submodules = $submodules;
	}

	/**
	 * Load sub-module components.
	 *
	 * @param  string  $component
	 * @return void
	 */
	protected function load_submodule_components( $component ) {
		foreach ( $this->submodules as $submodule ) {
			$submodule->load_component( $component );
		}
	}

	/**
	 * Load module component.
	 *
	 * @param  string  $component
	 * @return void
	 */
	public function load_component( $component ) {

		if ( 'component' === $component ) {
			return;
		}

		$fn_name = 'load_' . $component;

		if ( is_callable( array( $this, $fn_name ) ) ) {
			$this->$fn_name();
		}

		if ( 'core' !== $component ) {
			$this->load_submodule_components( $component );
		}
	}
}
