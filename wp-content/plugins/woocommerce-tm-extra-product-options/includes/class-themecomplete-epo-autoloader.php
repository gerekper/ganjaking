<?php
/**
 * Extra Product Options Autoloader
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Autoloader
 *
 * Autoload classes on demand.
 *
 * @package Extra Product Options/Classes
 * @author  ThemeComplete
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Autoloader {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );
	}

	/**
	 * Take a class name and turn it into a file name
	 *
	 * @param string  $class_name Class name.
	 * @param boolean $no_prepend Flag to indicate not to prepend the class name.
	 * @param boolean $no_replace Flag to indicate not to replace the class name.
	 *
	 * @return string
	 */
	private function get_file_name_from_class( $class_name, $no_prepend = false, $no_replace = false ) {

		if ( $no_prepend ) {
			$prepend = '';
		} else {
			$prepend = 'class-';
		}

		if ( $no_replace ) {
			$replace = $class_name;
		} else {
			$replace = str_replace( '_', '-', $class_name );
		}
		return $prepend . str_replace( '_', '-', $class_name ) . '.php';
	}

	/**
	 * Include a class file
	 *
	 * @param  string $path File path.
	 *
	 * @return boolean Successful or not.
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;

			return true;
		}

		return false;
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption
	 *
	 * @param string $class_name Class name.
	 * @return void
	 */
	public function autoload( $class_name ) {
		$path           = null;
		$original_class = $class_name;
		$class_name     = strtolower( $class_name );
		$file           = $this->get_file_name_from_class( $class_name );

		if ( 0 === mb_strpos( $class_name, 'themecomplete_epo_fields' ) ) {
			$path = THEMECOMPLETE_EPO_INCLUDES_PATH . 'fields/';
		} elseif ( 0 === mb_strpos( $class_name, 'themecomplete_epo_admin_' ) ) {
			$path = THEMECOMPLETE_EPO_ADMIN_PATH;
		} elseif ( 0 === mb_strpos( $class_name, 'themecomplete_extra_' ) ) {
			$path = THEMECOMPLETE_EPO_INCLUDES_PATH;
		} elseif ( 0 === mb_strpos( $class_name, 'themecomplete_epo_' ) ) {
			if ( 0 === mb_strpos( $class_name, 'themecomplete_epo_compatibility_base' ) ) {
				$path = THEMECOMPLETE_EPO_COMPATIBILITY_PATH;
			} elseif ( 0 === mb_strpos( $class_name, 'themecomplete_epo_cp' ) ) {
				$path = THEMECOMPLETE_EPO_COMPATIBILITY_PATH . 'classes/';
			} elseif ( 0 === mb_strpos( $class_name, 'themecomplete_epo_builder_element' ) ) {
				$path = THEMECOMPLETE_EPO_INCLUDES_PATH . 'classes/builder/';
			} else {
				$path = THEMECOMPLETE_EPO_INCLUDES_PATH . 'classes/';
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

define( 'THEMECOMPLETE_EPO_AUTOLOADER_LOADER', true );
