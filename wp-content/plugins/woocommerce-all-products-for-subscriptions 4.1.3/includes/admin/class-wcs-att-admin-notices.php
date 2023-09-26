<?php
/**
 * WCS_ATT_Admin_Notices class
 *
 * @package  WooCommerce All Products For Subscriptions
 * @since    2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WCS_ATT_Admin_Notices
 * @version  4.1.0
 */
class WCS_ATT_Admin_Notices {

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
	 * Array of maintenance notice types - name => callback.
	 * @var array
	 */
	private static $maintenance_notice_types = array(
		'welcome' => 'welcome_notice',
		'v4'      => 'v4_notice'
	);

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$maintenance_notices = get_option( 'wcsatt_maintenance_notices', array() );

		self::$dismissed_notices = get_user_meta( get_current_user_id(), 'wcsatt_dismissed_notices', true );
		self::$dismissed_notices = empty( self::$dismissed_notices ) ? array() : self::$dismissed_notices;

		// Show meta box notices.
		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
		// Save meta box notices.
		add_action( 'shutdown', array( __CLASS__, 'save_notices' ), 100 );

		if ( function_exists( 'WC' ) ) {
			// Show maintenance notices.
			add_action( 'admin_print_styles', array( __CLASS__, 'hook_maintenance_notices' ) );
		}
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
			$type          = $args[ 'type' ];
			$dismiss_class = isset( $args[ 'dismiss_class' ] ) ? $args[ 'dismiss_class' ] : false;
		} else {
			$type          = $args;
			$dismiss_class = false;
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
	 * Checks if a maintenance notice is visible.
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
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_dismissible_notice_dismissed( $notice_name ) {
		return in_array( $notice_name, self::$dismissed_notices );
	}

	/**
	 * Save errors to an option.
	 */
	public static function save_notices() {
		update_option( 'wcsatt_meta_box_notices', self::$meta_box_notices );
		update_option( 'wcsatt_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = get_option( 'wcsatt_meta_box_notices', array() );
		$notices       = array_merge( self::$admin_notices, $saved_notices );

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$notice_classes = array( 'wcsatt_notice', 'notice', 'notice-' . $notice[ 'type' ] );
				$dismiss_attr   = $notice[ 'dismiss_class' ] ? ' data-dismiss_class="' . esc_attr( $notice[ 'dismiss_class' ] ) . '"' : '';

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
						jQuery( '.wcsatt_notice .notice-dismiss' ).on( 'click', function() {

							var data = {
								action: 'woocommerce_dismiss_satt_notice',
								notice: jQuery( this ).parent().data( 'dismiss_class' ),
								security: '" . wp_create_nonce( 'wcsatt_dismiss_notice_nonce' ) . "'
							};

							jQuery.post( '" . WC()->ajax_url() . "', data );
						} );
					} );
				" );
			}

			// Clear.
			delete_option( 'wcsatt_meta_box_notices' );
		}
	}

	/**
	 * Show maintenance notices.
	 */
	public static function hook_maintenance_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) || ! class_exists( 'WCS_ATT_Admin' ) ) {
			return;
		}

		foreach ( self::$maintenance_notice_types as $notice_name => $callback ) {
			if ( self::is_maintenance_notice_visible( $notice_name ) ) {
				call_user_func( array( __CLASS__, $callback ) );
			}
		}
	}

	/**
	 * Add a dimissible notice/error.
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
	 * @param  string  $notice_name
	 */
	public static function remove_dismissible_notice( $notice_name ) {

		// Remove if not already removed.
		if ( ! self::is_dismissible_notice_dismissed( $notice_name ) ) {
			self::$dismissed_notices = array_merge( self::$dismissed_notices, array( $notice_name ) );
			update_user_meta( get_current_user_id(), 'wcsatt_dismissed_notices', self::$dismissed_notices );
			return true;
		}

		return false;
	}

	/**
	 * Add a maintenance notice to be displayed.
	 *
	 * @param  string  $notice_name
	 */
	public static function add_maintenance_notice( $notice_name ) {

		// Add if not already there.
		if ( ! self::is_maintenance_notice_visible( $notice_name ) ) {
			self::$maintenance_notices = array_merge( self::$maintenance_notices, array( $notice_name ) );
			return true;
		}

		return false;
	}

	/**
	 * Remove a maintenance notice.
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_maintenance_notice( $notice_name ) {

		// Remove if there.
		if ( self::is_maintenance_notice_visible( $notice_name ) ) {
			self::$maintenance_notices = array_diff( self::$maintenance_notices, array( $notice_name ) );
			return true;
		}

		return false;
	}

	/**
	 * Add 'welcome' notice.
	 */
	public static function welcome_notice() {

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Onboarding notices should only show on the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		ob_start();

		?>
		<p class="sw-welcome-text">
			<?php
				/* translators: onboarding url */
				echo wp_kses_post( sprintf( __( 'Thank you for installing <strong>All Products for WooCommerce Subscriptions</strong>. Ready to make your products available on subscription? <a href="%s">Add some global subscription plans to get started</a>.<br/>You can limit these plans to specific product categories &mdash; or, if you need more flexibility, it is possible to add custom subscription plans to individual products.', 'woocommerce-all-products-for-subscriptions' ), WCS_ATT()->get_resource_url( 'global-plan-settings' ) ) );
			?>
		</p>
		<?php

		$notice = ob_get_clean();

		self::add_dismissible_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'welcome' ) );
	}

	/**
	 * Add global plans onboarding notice.
	 */
	public static function add_global_plans_onboarding_notice() {

		$global_schemes_data = get_option( 'wcsatt_subscribe_to_cart_schemes', array() );

		if ( ! empty( $global_schemes_data ) ) {
			return;
		}

		ob_start();

		?>
		<p>
			<?php esc_html_e( 'Awesome &ndash; this product is now available on subscription!', 'woocommerce-all-products-for-subscriptions' ); ?>
			<?php echo wp_kses_post( sprintf( __( 'Did you know that you can also <a href="%s">add subscription plans to your products in bulk</a>?', 'woocommerce-all-products-for-subscriptions' ), WCS_ATT()->get_resource_url( 'global-plan-settings' ) ) ); ?>
		</p>
		<?php

		$notice = ob_get_clean();

		self::add_notice( $notice, array( 'type' => 'info' ), true );
	}

	/**
	 * Add v4 update notice.
	 */
	public static function v4_notice() {

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Maintenance notices should only show on the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		ob_start();

		?>
		<p>
			<?php
				/* translators: onboarding url */
				echo wp_kses_post( sprintf( __( '<strong>All Products for WooCommerce Subscriptions</strong> no longer supports Cart Subscription Plans. Your existing Cart Subscription Plans have been migrated to <a href="%s">Global Subscription Plans</a> &mdash; a quick, new way to add subscription plans to your existing products in bulk.', 'woocommerce-all-products-for-subscriptions' ), WCS_ATT()->get_resource_url( 'global-plan-settings' ) ) );
			?>
		</p>
		<?php

		$notice = ob_get_clean();

		self::add_dismissible_notice( $notice, array( 'type' => 'warning', 'dismiss_class' => 'v4_notice' ) );
	}

	/**
	 * Dismisses a notice.
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

WCS_ATT_Admin_Notices::init();
