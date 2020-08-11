<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * various themes 
 * https://wordpress.org/plugins/atum-stock-manager-for-woocommerce/
 * 
 * @package Extra Product Options/Compatibility
 * @version 5.0.12.4
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_themes {

	/**
	 * The single instance of the class
	 *
	 * @since 5.0.12.4
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 5.0.12.4
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0.12.4
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'add_compatibility' ) );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 5.0.12.4
	 */
	public function add_compatibility() {
		if (defined('WOODMART_SLUG')){
			add_action( 'woodmart_after_footer', array($this, 'woodmart_after_footer'), 998 );
			add_action( 'woodmart_after_footer', array($this, 'woodmart_after_footer2'), 1000 );
			add_action( 'wp_enqueue_scripts', array( $this, 'woodmart_wp_enqueue_scripts' ), 4 );
		}

	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @since 5.0.12.4
	 */
	public function woodmart_after_footer() {
		THEMECOMPLETE_EPO_DISPLAY()->block_epo = TRUE;

	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @since 5.0.12.4
	 */
	public function woodmart_after_footer2() {
		THEMECOMPLETE_EPO_DISPLAY()->block_epo = FALSE;

	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @since 5.0.12.4
	 */
	public function woodmart_wp_enqueue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-woodmart', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-woodmart.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
		}

	}

}
