<?php
/**
 * WC_CSP_Admin_Notices class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Conditional Shipping and Payments admin notices handling.
 *
 * @class    WC_CSP_Admin_Notices
 * @version  1.5.6
 */
class WC_CSP_Admin_Notices {

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
		'update'  => 'update_notice',
		'welcome' => 'welcome_notice'
	);

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$maintenance_notices = get_option( 'wc_csp_maintenance_notices', array() );

		self::$dismissed_notices = get_user_meta( get_current_user_id(), 'wc_csp_dismissed_notices', true );
		self::$dismissed_notices = empty( self::$dismissed_notices ) ? array() : self::$dismissed_notices;

		// Show meta box notices.
		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
		// Save meta box notices.
		add_action( 'shutdown', array( __CLASS__, 'save_notices' ), 100 );
		// Show maintenance notices.
		add_action( 'admin_print_styles', array( __CLASS__, 'hook_maintenance_notices' ) );
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
		update_option( 'wc_csp_meta_box_notices', self::$meta_box_notices );
		update_option( 'wc_csp_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = get_option( 'wc_csp_meta_box_notices', array() );
		$notices       = $saved_notices + self::$admin_notices;

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$notice_classes = array( 'wc_csp_notice', 'notice', 'notice-' . $notice[ 'type' ] );
				$dismiss_attr   = $notice[ 'dismiss_class' ] ? 'data-dismiss_class="' . $notice[ 'dismiss_class' ] . '"' : '';

				if ( $notice[ 'dismiss_class' ] ) {
					$notice_classes[] = $notice[ 'dismiss_class' ];
					$notice_classes[] = 'is-dismissible';
				}

				echo '<div class="' . implode( ' ', $notice_classes ) . '"' . $dismiss_attr . '>';
				echo wpautop( wp_kses_post( $notice[ 'content' ] ) );
				echo '</div>';
			}

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( "
					jQuery( function( $ ) {
						jQuery( '.wc_csp_notice .notice-dismiss' ).on( 'click', function() {

							var data = {
								action: 'woocommerce_dismiss_csp_notice',
								notice: jQuery( this ).parent().data( 'dismiss_class' ),
								security: '" . wp_create_nonce( 'wc_csp_dismiss_notice_nonce' ) . "'
							};

							jQuery.post( '" . WC()->ajax_url() . "', data );
						} );
					} );
				" );
			}

			// Clear.
			delete_option( 'wc_csp_meta_box_notices' );
		}
	}

	/**
	 * Show maintenance notices.
	 */
	public static function hook_maintenance_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
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
			update_user_meta( get_current_user_id(), 'wc_csp_dismissed_notices', self::$dismissed_notices );
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
	 * Add 'update' maintenance notice.
	 */
	public static function update_notice() {

		if ( ! class_exists( 'WC_CSP_Install' ) ) {
			return;
		}

		if ( WC_CSP_Install::is_update_pending() ) {

			$status = '';

			// Show notice to indicate that an update is in progress.
			if ( WC_CSP_Install::is_update_process_running() || WC_CSP_Install::is_update_queued() ) {

				$status = __( 'Your database is being updated in the background.', 'woocommerce-conditional-shipping-and-payments' );

				// Check if the update process is running.
				if ( false === WC_CSP_Install::is_update_process_running() ) {
					$status .= self::get_force_update_prompt();
				}

			// Show a prompt to update.
			} elseif ( false === WC_CSP_Install::auto_update_enabled() && false === WC_CSP_Install::is_update_incomplete() ) {

				$status  = __( 'Your database needs to be updated to the latest version.', 'woocommerce-conditional-shipping-and-payments' );
				$status .= self::get_trigger_update_prompt();

			} elseif ( WC_CSP_Install::is_update_incomplete() ) {

				$status  = __( 'Database update incomplete.', 'woocommerce-conditional-shipping-and-payments' );
				$status .= self::get_failed_update_prompt();
			}

			if ( $status ) {
				$notice = '<strong>' . __( 'WooCommerce Conditional Shipping and Payments Data Update', 'woocommerce-conditional-shipping-and-payments' ) . '</strong> &#8211; ' . $status;
				self::add_notice( $notice, 'native' );
			}

		// Show persistent notice to indicate that the update process is complete.
		} else {
			$notice = __( 'WooCommerce Conditional Shipping and Payments data update complete.', 'woocommerce-conditional-shipping-and-payments' );
			self::add_notice( $notice, array( 'type' => 'native', 'dismiss_class' => 'update' ) );
		}
	}

	/**
	 * Add 'welcome' notice.
	 *
	 * @since  1.5.0
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

		$restriction_data = get_option( 'wccsp_restrictions_global_settings', array() );

		if ( empty( $restriction_data ) ) {

			ob_start();

			?>
			<div class="sw-welcome-icon"></div>
			<h2 class="sw-welcome-title"><?php esc_attr_e( 'Ready to exclude some payment / shipping options?', 'woocommerce-conditional-shipping-and-payments' ); ?></h2>
			<p class="sw-welcome-text"><?php esc_attr_e( 'Thank you for installing WooCommerce Conditional Shipping and Payments.', 'woocommerce-conditional-shipping-and-payments' ); ?><br/><?php esc_attr_e( 'Let\'s get started by creating your first restriction!', 'woocommerce-conditional-shipping-and-payments' ); ?></p>
			<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=restrictions' ); ?>" class="sw-welcome-button button-primary"><?php esc_attr_e( 'Let\'s go!', 'woocommerce-conditional-shipping-and-payments' ); ?></a>
			<?php

			$notice = ob_get_clean();
			self::add_dismissible_notice( $notice, array( 'type' => 'native', 'dismiss_class' => 'welcome' ) );
		}
	}

	/**
	 * Returns a "trigger update" notice component.
	 *
	 * @return string
	 */
	private static function get_trigger_update_prompt() {
		$update_url    = esc_url( wp_nonce_url( add_query_arg( 'trigger_wc_csp_db_update', true, admin_url() ), 'wc_csp_trigger_db_update_nonce', '_wc_csp_admin_nonce' ) );
		$update_prompt = '<p><a href="' . $update_url . '" class="wc-cp-update-now button-primary">' . __( 'Run the updater', 'woocommerce' ) . '</a></p>';
		return $update_prompt;
	}

	/**
	 * Returns a "force update" notice component.
	 *
	 * @return string
	 */
	private static function get_force_update_prompt() {

		$fallback_prompt = '';
		$update_runtime  = get_option( 'wc_csp_update_init', 0 );

		// Wait for at least 5 seconds.
		if ( gmdate( 'U' ) - $update_runtime > 5 ) {
			// Perhaps the upgrade process failed to start?
			$fallback_url    = esc_url( wp_nonce_url( add_query_arg( 'force_wc_csp_db_update', true, admin_url() ), 'wc_csp_force_db_update_nonce', '_wc_csp_admin_nonce' ) );
			$fallback_link   = '<a href="' . $fallback_url . '">' . __( 'run the update process manually', 'woocommerce-conditional-shipping-and-payments' ) . '</a>';
			$fallback_prompt = '<br/><em>' . sprintf( __( '&hellip;Taking a while? You may need to %s.', 'woocommerce-conditional-shipping-and-payments' ), $fallback_link ) . '</em>';
		}

		return $fallback_prompt;
	}

	/**
	 * Returns a "failed update" notice component.
	 *
	 * @return string
	 */
	private static function get_failed_update_prompt() {

		$support_url    = esc_url( WC_CP_SUPPORT_URL );
		$support_link   = '<a href="' . $support_url . '">' . __( 'get in touch with us', 'woocommerce-conditional-shipping-and-payments' ) . '</a>';
		$support_prompt = '<br/><em>' . sprintf( __( 'If this message persists, please restore your database from a backup, or %s.', 'woocommerce-conditional-shipping-and-payments' ), $support_link ) . '</em>';

		return $support_prompt;
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

	/*
	|--------------------------------------------------------------------------
	| Deprecated.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Act upon clicking on a 'dismiss notice' link.
	 *
	 * @deprecated  3.14.0
	 */
	public static function dismiss_notice_handler() {
		if ( isset( $_GET[ 'dismiss_wc_csp_notice' ] ) && isset( $_GET[ '_wc_csp_admin_nonce' ] ) ) {
			if ( ! wp_verify_nonce( $_GET[ '_wc_csp_admin_nonce' ], 'wc_csp_dismiss_notice_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'woocommerce' ) );
			}

			$notice = sanitize_text_field( $_GET[ 'dismiss_wc_csp_notice' ] );

			self::dismiss_notice( $notice );
		}
	}
}

WC_CSP_Admin_Notices::init();
