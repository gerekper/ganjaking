<?php
/**
 * Extra Product Options Autoloader
 *
 * Autoload classes on demand.
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Autoloader {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Take a class name and turn it into a file name
	 *
	 * @param  string $class Class name.
	 *
	 * @return string
	 */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file
	 *
	 * @param  string $path File path.
	 *
	 * @return bool Successful or not.
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption
	 *
	 * @param string $class Class name.
	 */
	public function autoload( $class ) {

		$path           = NULL;
		$original_class = $class;
		$class          = strtolower( $class );
		$class          = str_replace( 'themecomplete', 'tm', $class );
		$file           = $this->get_file_name_from_class( $class );

		if ( 0 === strpos( $class, 'tm_epo_fields' ) ) {
			$path = THEMECOMPLETE_EPO_PLUGIN_PATH . '/include/fields/';
		} elseif ( 0 === strpos( $class, 'tm_epo_admin_' ) ) {
			$path = THEMECOMPLETE_EPO_PLUGIN_PATH . '/admin/';
		} elseif ( 0 === strpos( $class, 'tm_extra_' ) ) {
			$path = THEMECOMPLETE_EPO_PLUGIN_PATH . '/include/';
		} elseif ( 0 === strpos( $class, 'tm_epo_' ) ) {
			if ( 0 === strpos( $class, 'tm_epo_compatibility_base' ) ) {
				$path = THEMECOMPLETE_EPO_PLUGIN_PATH . '/include/compatibility/';
			} elseif ( 0 === strpos( $class, 'tm_epo_cp' ) ) {
				$path = THEMECOMPLETE_EPO_PLUGIN_PATH . '/include/compatibility/classes/';
			} else {
				$path = THEMECOMPLETE_EPO_PLUGIN_PATH . '/include/classes/';
			}
		}

		$path = apply_filters( 'wc_epo_autoload_path', $path, $original_class );
		$file = apply_filters( 'wc_epo_autoload_file', $file, $original_class );

		if ( $path ) {
			$this->load_file( $path . $file );
		}

	}

}

new THEMECOMPLETE_EPO_Autoloader();

define( 'THEMECOMPLETE_EPO_AUTOLOADER_LOADER', TRUE );
