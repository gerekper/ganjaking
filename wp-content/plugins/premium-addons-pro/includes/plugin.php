<?php

namespace PremiumAddonsPro\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Plugin
 */
class Plugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function _includes() {
		require PREMIUM_PRO_ADDONS_PATH . 'includes/class-modules-manager.php';
	}

	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$filename = strtolower(
			preg_replace(
				array( '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
				array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
				$class
			)
		);
		$filename = PREMIUM_PRO_ADDONS_PATH . $filename . '.php';

		if ( is_readable( $filename ) ) {
			include $filename;
		}
	}

	public function elementor_controls_init() {

		$this->_modules_manager = new Manager();

	}

	private function setup_hooks() {

		add_action( 'elementor/init', array( $this, 'elementor_controls_init' ) );

	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->_includes();

		$this->setup_hooks();

	}
}
