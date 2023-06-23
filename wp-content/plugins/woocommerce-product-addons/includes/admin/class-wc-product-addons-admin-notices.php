<?php
/**
 * WC_PAO_Admin_Notices class
 *
 * @package  WooCommerce Product Add-ons
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Add-Ons admin notices handling.
 *
 * @class    WC_PAO_Admin_Notices
 * @version  6.3.2
 */
class WC_PAO_Admin_Notices {

	/**
	 * Notices presisting on the next request.
	 * @var array
	 */
	public static $meta_box_notices = array();

	/**
	 * Notices displayed on the current request.
	 * @var array
	 */
	public static $admin_notices = array();

	/**
	 * Maintenance notices displayed on every request until cleared.
	 * @var array
	 */
	public static $maintenance_notices = array();

	/**
	 * Dismissible notices displayed on the current request.
	 * @var array
	 */
	public static $dismissed_notices = array();

	/**
	 * Constructor.
	 */
	public static function init() {

		if ( ! class_exists( 'WC_PAO_Notices' ) ) {
			require_once dirname( __FILE__ ) . '/../class-wc-product-addons-notices.php';
		}

		// Avoid duplicates for some notice types that are meant to be unique.
		if ( ! isset( $GLOBALS[ 'sw_store' ][ 'notices_unique' ] ) ) {
			$GLOBALS[ 'sw_store' ][ 'notices_unique' ] = array();
		}

		self::$dismissed_notices   = get_user_meta( get_current_user_id(), 'wc_pao_dismissed_notices', true );
		self::$dismissed_notices   = empty( self::$dismissed_notices ) ? array() : self::$dismissed_notices;

		// Show meta box notices.
		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
		// Save meta box notices.
		add_action( 'shutdown', array( __CLASS__, 'save_notices' ), 100 );
	}

	/**
	 * Add a notice/error.
	 *
	 * @param  string   $text
	 * @param  mixed    $args
	 * @param  boolean  $save_notice
	 */
	public static function add_notice( $text, $args, $save_notice = false ) {

		if ( is_array( $args ) ) {
			$type           = $args[ 'type' ];
			$dismiss_class  = isset( $args[ 'dismiss_class' ] ) ? $args[ 'dismiss_class' ] : false;
			$unique_context = isset( $args[ 'unique_context' ] ) ? $args[ 'unique_context' ] : false;
		} else {
			$type           = $args;
			$dismiss_class  = false;
			$unique_context = false;
		}

		if ( $unique_context ) {
			if ( self::unique_notice_exists( $unique_context ) ) {
				return;
			} else {
				$GLOBALS[ 'sw_store' ][ 'notices_unique' ][] = $unique_context;
			}
		}

		$notice = array(
			'type'          => $type,
			'content'       => $text,
			'dismiss_class' => $dismiss_class
		);

		if ( $save_notice ) {
			self::$meta_box_notices[] = $notice;
		} else {
			self::$admin_notices[] = $notice;
		}
	}

	/**
	 * Checks if a notice that belongs to a the specified uniqueness context already exists.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $context
	 * @return bool
	 */
	private static function unique_notice_exists( $context ) {
		return $context && in_array( $context, $GLOBALS[ 'sw_store' ][ 'notices_unique' ] );
	}

	/**
	 * Get a setting for a notice type.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function get_notice_option( $notice_name, $key, $default = null ) {
		return WC_PAO_Notices::get_notice_option( $notice_name, $key, $default );
	}

	/**
	 * Set a setting for a notice type.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array
	 */
	public static function set_notice_option( $notice_name, $key, $value ) {
		return WC_PAO_Notices::set_notice_option( $notice_name, $key, $value );
	}

	/**
	 * Checks if a maintenance notice is visible.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_maintenance_notice_visible( $notice_name ) {
		return in_array( $notice_name, self::$maintenance_notices );
	}

	/**
	 * Checks if a dismissible notice has been dismissed in the past.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_dismissible_notice_dismissed( $notice_name ) {
		return in_array( $notice_name, self::$dismissed_notices );
	}

	/**
	 * Save notices to the DB.
	 */
	public static function save_notices() {
		update_option( 'wc_pao_meta_box_notices', self::$meta_box_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = get_option( 'wc_pao_meta_box_notices', array() );
		$notices       = array_merge( self::$admin_notices, $saved_notices );

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$notice_classes = array( 'wc_pao_notice', 'notice', 'notice-' . $notice[ 'type' ] );
				$dismiss_attr   = $notice[ 'dismiss_class' ] ? ' data-dismiss_class="' . $notice[ 'dismiss_class' ] . '"' : '';

				if ( $notice[ 'dismiss_class' ] ) {
					$notice_classes[] = $notice[ 'dismiss_class' ];
					$notice_classes[] = 'is-dismissible';
				}

				$output = '<div class="' . esc_attr( implode( ' ', $notice_classes ) ) . '"' . $dismiss_attr . '>' . wpautop( $notice[ 'content' ] ) . '</div>';
				echo wp_kses_post( $output );
			}

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( "
					jQuery( function( $ ) {
						jQuery( '.wc_pao_notice .notice-dismiss' ).on( 'click', function() {

							var data = {
								action: 'woocommerce_dismiss_addons_notice',
								notice: jQuery( this ).parent().data( 'dismiss_class' ),
								security: '" . wp_create_nonce( 'wc_pao_dismiss_notice_nonce' ) . "'
							};

							jQuery.post( '" . WC()->ajax_url() . "', data );
						} );
					} );
				" );
			}

			// Clear.
			delete_option( 'wc_pao_meta_box_notices' );
		}
	}


	/**
	 * Add a dimissible notice/error.
	 *
	 * @since  6.0.0
	 *
	 * @param  string   $text
	 * @param  mixed    $args
	 */
	public static function add_dismissible_notice( $text, $args ) {
		if ( ! isset( $args[ 'dismiss_class' ] ) || ! self::is_dismissible_notice_dismissed( $args[ 'dismiss_class' ] ) ) {
			self::add_notice( $text, $args );
		}
	}

	/**
	 * Remove a dismissible notice.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_dismissible_notice( $notice_name ) {

		// Remove if not already removed.
		if ( ! self::is_dismissible_notice_dismissed( $notice_name ) ) {
			self::$dismissed_notices = array_merge( self::$dismissed_notices, array( $notice_name ) );
			update_user_meta( get_current_user_id(), 'wc_pao_dismissed_notices', self::$dismissed_notices );
			return true;
		}

		return false;
	}

	/**
	 * Dismisses a notice. Dismissible maintenance notices cannot be dismissed forever.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $notice
	 */
	public static function dismiss_notice( $notice ) {
		if ( isset( self::$maintenance_notice_types[ $notice ] ) ) {
			return self::remove_maintenance_notice( $notice );
		} else {
			return self::remove_dismissible_notice( $notice );
		}
	}
}

WC_PAO_Admin_Notices::init();
