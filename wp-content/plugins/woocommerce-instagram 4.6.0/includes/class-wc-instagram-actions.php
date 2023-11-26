<?php
/**
 * Scheduled Actions
 *
 * This class handles the actions that are executed periodically.
 *
 * @package WC_Instagram
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Actions.
 */
class WC_Instagram_Actions {

	/**
	 * The scheduled actions instances.
	 *
	 * @var array
	 */
	protected static $actions = array();

	/**
	 * Init.
	 *
	 * @since 4.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_actions' ), 5 );
	}

	/**
	 * Initializes actions.
	 *
	 * @since 4.0.0
	 */
	public static function init_actions() {
		if ( ! wc_instagram_is_connected() ) {
			return;
		}

		include_once WC_INSTAGRAM_PATH . 'includes/abstracts/abstract-wc-instagram-action.php';
		include_once WC_INSTAGRAM_PATH . 'includes/actions/class-wc-instagram-action-generate-catalogs.php';

		self::set( 'generate_catalogs', new WC_Instagram_Action_Generate_Catalogs() );
	}

	/**
	 * Gets the instance of a scheduled action.
	 *
	 * @since 4.0.0
	 *
	 * @param string $name The action name.
	 * @return WC_Instagram_Action|null
	 */
	public static function get( $name ) {
		return ( isset( self::$actions[ $name ] ) ? self::$actions[ $name ] : null );
	}

	/**
	 * Sets an instance of a scheduled action.
	 *
	 * @since 4.0.0
	 *
	 * @param string              $name     The action name.
	 * @param WC_Instagram_Action $instance The action instance.
	 */
	public static function set( $name, $instance ) {
		self::$actions[ $name ] = $instance;
	}

	/**
	 * Un-schedules all events attached to the action.
	 *
	 * @since 4.0.0
	 *
	 * @param string $name The action name.
	 */
	public static function clear( $name ) {
		$action = self::get( $name );

		if ( $action instanceof WC_Instagram_Action ) {
			$action->cancel();
		}
	}
}

WC_Instagram_Actions::init();
