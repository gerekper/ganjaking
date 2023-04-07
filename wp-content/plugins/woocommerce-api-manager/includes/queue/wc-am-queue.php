<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Queue
 *
 * @since       2.6.2
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Queue class
 */
class WC_AM_Queue {

	/**
	 * The single instance of the queue.
	 *
	 * @var WCAM_Queue_Interface|null
	 */
	protected static $instance = null;

	/**
	 * The default queue class to initialize
	 *
	 * @var string
	 */
	protected static $default_cass = 'WCAM_Action_Queue';

	/**
	 * Single instance of WCAM_Queue_Interface
	 *
	 * @return WCAM_Queue_Interface|null
	 */
	final public static function instance() {

		if ( is_null( self::$instance ) ) {
			$class          = self::get_class();
			self::$instance = new $class();
			self::$instance = self::validate_instance( self::$instance );
		}

		return self::$instance;
	}

	/**
	 * Get class to instantiate. Make sure 3rd party code has the chance to attach a custom queue class.
	 *
	 * @return string
	 */
	protected static function get_class() {
		if ( ! did_action( 'plugins_loaded' ) ) {
			wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before plugins_loaded.', 'woocommerce-api-manager' ), '2.6.2' );
		}

		return apply_filters( 'wc_am_queue_class', self::$default_cass );
	}

	/**
	 * Enforce the WCAM_Queue_Interface.
	 *
	 * @param WCAM_Queue_Interface $instance Instance class.
	 *
	 * @return WCAM_Queue_Interface
	 */
	protected static function validate_instance( $instance ) {
		if ( false === ( $instance instanceof WCAM_Queue_Interface ) ) {
			$default_class = self::$default_cass;
			/* translators: %s: Default class name */
			wc_doing_it_wrong( __FUNCTION__, sprintf( __( 'The class attached to the "wc_am_queue_class" does not implement the WCAM_Queue_Interface interface. The default %s class will be used instead.', 'woocommerce-api-manager' ), $default_class ), '2.6.2' );
			$instance = new $default_class();
		}

		return $instance;
	}
}