<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configurate clearfy plugins
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @since         1.0.0
 * @package       clearfy
 * @copyright (c) 2018, Webcraftic Ltd
 *
 */
abstract class Wbcr_FactoryClearfy228_Configurate {

	/**
	 * @param Wbcr_Factory437_Plugin $plugin
	 */
	public function __construct( Wbcr_Factory437_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->registerActionsAndFilters();
	}

	/**
	 * Registers filters and actions
	 *
	 * @return mixed
	 */
	abstract protected function registerActionsAndFilters();

	/**
	 * Get options with namespace
	 *
	 * @param      $option_name
	 * @param bool $default
	 *
	 * @return mixed|void
	 */
	public function getPopulateOption( $option_name, $default = false ) {
		return $this->plugin->getPopulateOption( $option_name, $default );
	}

	/**
	 * Get options with namespace
	 *
	 * @param      $option_name
	 * @param bool $default
	 *
	 * @return mixed|void
	 */
	public function getOption( $option_name, $default = false ) {
		return $this->plugin->getOption( $option_name, $default );
	}

	/**
	 * Get network options with namespace
	 *
	 * @param      $option_name
	 * @param bool $default
	 *
	 * @return mixed|void
	 */
	public function getNetworkOption( $option_name, $default = false ) {
		return $this->plugin->getNetworkOption( $option_name, $default );
	}

	/**
	 * @param $option_name
	 * @param $value
	 *
	 * @return bool
	 */
	public function updatePopulateOption( $option_name, $value ) {
		$this->plugin->updatePopulateOption( $option_name, $value );
	}

	/**
	 * @param $option_name
	 * @param $value
	 *
	 * @return bool
	 */
	public function updateNetworkOption( $option_name, $value ) {
		$this->plugin->updateNetworkOption( $option_name, $value );
	}

	/**
	 * @param $option_name
	 * @param $value
	 *
	 * @return bool
	 */
	public function updateOption( $option_name, $value ) {
		$this->plugin->updateOption( $option_name, $value );
	}

	/**
	 * @param $option_name
	 *
	 * @return bool
	 */
	public function deletePopulateOption( $option_name ) {
		$this->plugin->deletePopulateOption( $option_name );
	}

	/**
	 * @param $option_name
	 *
	 * @return bool
	 */
	public function deleteOption( $option_name ) {
		$this->plugin->deleteOption( $option_name );
	}
}
