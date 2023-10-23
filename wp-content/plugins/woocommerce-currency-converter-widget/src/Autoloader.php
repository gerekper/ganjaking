<?php
/**
 * Includes the composer Autoloader used for packages and classes in the src/ directory.
 *
 * @since 1.7.0
 */

namespace KoiLab\WC_Currency_Converter;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Initializes the autoloader.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public static function init() {
		$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';
		$loaded     = is_readable( $autoloader ) && ( require $autoloader );

		if ( $loaded ) {
			add_action( 'plugins_loaded', array( __CLASS__, 'register_class_aliases' ), 0 );
		}

		return $loaded;
	}

	/**
	 * Registers class aliases to make them backward compatible.
	 *
	 * @since 2.1.0
	 */
	public static function register_class_aliases() {
		// Aliases the 'Themesquad\WC_Currency_Converter\' namespace.
		$aliases = array(
			'Utilities\Currency_Utils',
			'Utilities\L10n_Utils',
			'Utilities\Plugin_Utils',
			'Utilities\String_Utils',
		);

		foreach ( $aliases as $class ) {
			class_alias( 'KoiLab\\WC_Currency_Converter\\' . $class, 'Themesquad\\WC_Currency_Converter\\' . $class );
		}

		// Re-located classes.
		$aliases = array(
			'KoiLab\WC_Currency_Converter\Exchange\Providers\Open_Exchange_Provider' => 'Themesquad\WC_Currency_Converter\Open_Exchange\API',
			'KoiLab\WC_Currency_Converter\Exchange\Rates' => 'Themesquad\WC_Currency_Converter\Open_Exchange\Rates',
		);

		foreach ( $aliases as $class => $alias ) {
			class_alias( $class, $alias );
		}
	}
}
