<?php
/**
 * Background processes
 *
 * This class handles the background processes.
 *
 * @package WC_Instagram
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Backgrounds.
 */
class WC_Instagram_Backgrounds {

	/**
	 * The background process instances.
	 *
	 * @var array
	 */
	protected static $backgrounds = array();

	/**
	 * Init.
	 *
	 * @since 4.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_backgrounds' ), 5 );
	}

	/**
	 * Initializes background processes.
	 *
	 * @since 4.0.0
	 */
	public static function init_backgrounds() {
		if ( ! wc_instagram_is_connected() ) {
			return;
		}

		include_once WC_INSTAGRAM_PATH . 'includes/abstracts/abstract-wc-instagram-background-process.php';
		include_once WC_INSTAGRAM_PATH . 'includes/backgrounds/class-wc-instagram-background-generate-catalog.php';

		self::set( 'generate_catalog', new WC_Instagram_Background_Generate_Catalog() );
	}

	/**
	 * Gets the instance of a background process.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key The background process key.
	 * @return WC_Instagram_Background_Process|null
	 */
	public static function get( $key ) {
		return ( isset( self::$backgrounds[ $key ] ) ? self::$backgrounds[ $key ] : null );
	}

	/**
	 * Sets an instance of a background process.
	 *
	 * @since 4.0.0
	 *
	 * @param string                          $key      The background process key.
	 * @param WC_Instagram_Background_Process $instance The action instance.
	 */
	public static function set( $key, $instance ) {
		self::$backgrounds[ $key ] = $instance;
	}
}

WC_Instagram_Backgrounds::init();
