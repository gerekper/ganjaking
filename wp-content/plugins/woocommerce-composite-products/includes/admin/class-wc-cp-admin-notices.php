<?php
/**
 * WC_CP_Admin_Notices class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Products admin notices handling.
 *
 * @class    WC_CP_Admin_Notices
 * @version  7.0.3
 */
class WC_CP_Admin_Notices {

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
		'update'   => 'update_notice',
		'welcome'  => 'welcome_notice',
		'loopback' => 'loopback_notice'
	);

	/**
	 * Constructor.
	 */
	public static function init() {

		if ( ! class_exists( 'WC_CP_Notices' ) ) {
			require_once  WC_CP_ABSPATH . 'includes/class-wc-cp-notices.php' ;
		}

		// Avoid duplicates for some notice types that are meant to be unique.
		if ( ! isset( $GLOBALS[ 'sw_store' ][ 'notices_unique' ] ) ) {
			$GLOBALS[ 'sw_store' ][ 'notices_unique' ] = array();
		}

		self::$maintenance_notices = get_option( 'wc_cp_maintenance_notices', array() );
		self::$dismissed_notices   = get_user_meta( get_current_user_id(), 'wc_cp_dismissed_notices', true );
		self::$dismissed_notices   = empty( self::$dismissed_notices ) ? array() : self::$dismissed_notices;

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
	 * @since  7.0.3
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
	 * @since  7.0.3
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function get_notice_option( $notice_name, $key, $default = null ) {
		return WC_CP_Notices::get_notice_option( $notice_name, $key, $default );
	}

	/**
	 * Set a setting for a notice type.
	 *
	 * @since  7.0.3
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array
	 */
	public static function set_notice_option( $notice_name, $key, $value ) {
		return WC_CP_Notices::set_notice_option( $notice_name, $key, $value );
	}

	/**
	 * Checks if a maintenance notice is visible.
	 *
	 * @since  3.14.0
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
	 * @since  3.14.0
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
		update_option( 'wc_cp_meta_box_notices', self::$meta_box_notices );
		update_option( 'wc_cp_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = get_option( 'wc_cp_meta_box_notices', array() );
		$notices       = $saved_notices + self::$admin_notices;

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$notice_classes = array( 'wc_cp_notice', 'notice', 'notice-' . $notice[ 'type' ] );
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
						jQuery( '.wc_cp_notice .notice-dismiss' ).on( 'click', function() {

							var data = {
								action: 'woocommerce_dismiss_composite_notice',
								notice: jQuery( this ).parent().data( 'dismiss_class' ),
								security: '" . wp_create_nonce( 'wc_cp_dismiss_notice_nonce' ) . "'
							};

							jQuery.post( '" . WC()->ajax_url() . "', data );
						} );
					} );
				" );
			}

			// Clear.
			delete_option( 'wc_cp_meta_box_notices' );
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
	 * @since  3.14.0
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
	 * @since  3.14.0
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_dismissible_notice( $notice_name ) {

		// Remove if not already removed.
		if ( ! self::is_dismissible_notice_dismissed( $notice_name ) ) {
			self::$dismissed_notices = array_merge( self::$dismissed_notices, array( $notice_name ) );
			update_user_meta( get_current_user_id(), 'wc_cp_dismissed_notices', self::$dismissed_notices );
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

		if ( ! class_exists( 'WC_CP_Install' ) ) {
			return;
		}

		if ( WC_CP_Install::is_update_pending() ) {

			$status = '';

			// Show notice to indicate that an update is in progress.
			if ( WC_CP_Install::is_update_process_running() || WC_CP_Install::is_update_queued() ) {

				$prompt = '';

				// Check if the update process is running.
				if ( false === WC_CP_Install::is_update_process_running() ) {
					$prompt = self::get_force_update_prompt();
				}

				$status = sprintf( __( '<strong>WooCommerce Composite Products</strong> is updating your database.%s', 'woocommerce-composite-products' ), $prompt );

			// Show a prompt to update.
			} elseif ( false === WC_CP_Install::auto_update_enabled() && false === WC_CP_Install::is_update_incomplete() ) {

				$status  = __( '<strong>WooCommerce Composite Products</strong> has been updated! To keep things running smoothly, your database needs to be updated, as well.', 'woocommerce-composite-products' );
				$status .= '<br/>' . sprintf( __( 'Before you proceed, please take a few minutes to <a href="%s" target="_blank">learn more</a> about best practices when updating.', 'woocommerce-composite-products' ), WC_CP()->get_resource_url( 'updating' ) );
				$status .= self::get_trigger_update_prompt();

			} elseif ( WC_CP_Install::is_update_incomplete() ) {

				$status = sprintf( __( '<strong>WooCommerce Composite Products</strong> has not finished updating your database.%s', 'woocommerce-composite-products' ), self::get_failed_update_prompt() );
			}

			if ( $status ) {
				self::add_notice( $status, 'info' );
			}

		// Show persistent notice to indicate that the update process is complete.
		} else {
			$notice = __( '<strong>WooCommerce Composite Products</strong> database update complete. Thank you for updating to the latest version!', 'woocommerce-composite-products' );
			self::add_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'update' ) );
		}
	}

	/**
	 * Add 'welcome' notice.
	 *
	 * @since  3.15.0
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
		<div class="sw-welcome-icon"></div>
		<h2 class="sw-welcome-title"><?php esc_attr_e( 'Ready to create your first product kit?', 'woocommerce-composite-products' ); ?></h2>
		<p class="sw-welcome-text"><?php esc_attr_e( 'Thank you for installing WooCommerce Composite Products.', 'woocommerce-composite-products' ); ?><br/><?php esc_attr_e( 'Let\'s get started by creating your first composite product!', 'woocommerce-composite-products' ); ?></p>
		<a href="<?php echo admin_url( 'post-new.php?post_type=product&wc_cp_first_composite=1' ); ?>" class="sw-welcome-button button-primary"><?php esc_attr_e( 'Let\'s go!', 'woocommerce-composite-products' ); ?></a>
		<?php

		$notice = ob_get_clean();

		self::add_dismissible_notice( $notice, array( 'type' => 'native', 'dismiss_class' => 'welcome' ) );
	}

	/**
	 * Add 'loopback' notice.
	 *
	 * @since  7.0.3
	 */
	public static function loopback_notice() {

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins'
		);

		// Onboarding notices should only show on the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		// Health check class exists?
		if ( ! file_exists( ABSPATH . 'wp-admin/includes/class-wp-site-health.php' ) ) {
			return;
		}

		$last_tested   = self::get_notice_option( 'loopback', 'last_tested', 0 );
		$last_result   = self::get_notice_option( 'loopback', 'last_result', '' );
		$auto_run_test = gmdate( 'U' ) - $last_tested > DAY_IN_SECONDS;
		$show_notice   = 'fail' === $last_result;

		if ( ! function_exists( 'wc_enqueue_js' ) ) {
			return;
		}

		wc_enqueue_js( "
			jQuery( function( $ ) {

				var auto_run_test  = " . ( $auto_run_test ? 'true' : 'false' ) . ",
					notice         = jQuery( '.wc_cp_notice.loopback' ),
					notice_exists  = notice.length > 0;

				var do_loopback_test = function() {

					if ( notice_exists && ! auto_run_test ) {
						notice.find( 'a.wc-cp-run-again' ).addClass( 'disabled' );
						notice.find( 'span.spinner' ).addClass( 'is-active' );
					}

					var data = {
						action: 'woocommerce_composite_loopback_test',
						security: '" . wp_create_nonce( 'wc_cp_loopback_notice_nonce' ) . "'
					};

					jQuery.post( '" . WC()->ajax_url() . "', data, function( response ) {

						if ( ! notice_exists || auto_run_test ) {
							return;
						}

						if ( 'success' === response.result ) {
							notice.html( '" . '<p>' . __( 'Loopback test passed!', 'woocommerce-composite-products' ) . '</p>' . "' ).removeClass( 'notice-warning' ).addClass( 'notice-success' );
						} else {
							notice.html( '" . '<p>' . __( 'Loopback test failed!', 'woocommerce-composite-products' ) . '</p>' . "' ).removeClass( 'notice-warning' ).addClass( 'notice-error' );
						}
					} );
				};

				if ( auto_run_test ) {
					do_loopback_test();
				}

				if ( notice_exists ) {

					notice.find( 'a.wc-cp-run-again' ).on( 'click', function() {

						auto_run_test = false;

						do_loopback_test();

						return false;
					} );
				}
			} );
		" );

		if ( $show_notice ) {

			$notice       = __( 'Composite Products ran a quick check-up on your site, and found that loopback requests might be failing to complete. Loopback requests are used by WooCommerce to run scheduled events, such as database upgrades. To keep your site in top shape, please ask the host or administrator of your server to look into this for you.', 'woocommerce-composite-products' );
			$rerun_prompt = '<p><a href="#trigger_loopback_test" class="button wc-cp-run-again">' . __( 'Repeat test', 'woocommerce-composite-products' ) . '</a><span class="spinner" style="float:none;vertical-align:top"></span></p>';

			$notice .= $rerun_prompt;

			self::add_dismissible_notice( $notice, array( 'type' => 'warning', 'unique_context' => 'loopback', 'dismiss_class' => 'loopback' ) );
		}
	}

	/**
	 * Returns a "trigger update" notice component.
	 *
	 * @since  3.12.0
	 *
	 * @return string
	 */
	private static function get_trigger_update_prompt() {
		$update_url    = esc_url( wp_nonce_url( add_query_arg( 'trigger_wc_cp_db_update', true, admin_url() ), 'wc_cp_trigger_db_update_nonce', '_wc_cp_admin_nonce' ) );
		$update_prompt = '<p><a href="' . $update_url . '" class="wc-cp-update-now button">' . __( 'Update database', 'woocommerce-composite-products' ) . '</a></p>';
		return $update_prompt;
	}

	/**
	 * Returns a "force update" notice component.
	 *
	 * @since  3.12.0
	 *
	 * @return string
	 */
	private static function get_force_update_prompt() {

		$fallback_prompt = '';
		$update_runtime  = get_option( 'wc_cp_update_init', 0 );

		// Wait for at least 5 seconds.
		if ( gmdate( 'U' ) - $update_runtime > 5 ) {
			// Perhaps the upgrade process failed to start?
			$fallback_url    = esc_url( wp_nonce_url( add_query_arg( 'force_wc_cp_db_update', true, admin_url() ), 'wc_cp_force_db_update_nonce', '_wc_cp_admin_nonce' ) );
			$fallback_link   = '<a href="' . $fallback_url . '">' . __( 'run it manually', 'woocommerce-composite-products' ) . '</a>';
			$fallback_prompt = sprintf( __( ' The process seems to be taking a little longer than usual, so let\'s try to %s.', 'woocommerce-composite-products' ), $fallback_link );
		}

		return $fallback_prompt;
	}

	/**
	 * Returns a "failed update" notice component.
	 *
	 * @since  3.12.0
	 *
	 * @return string
	 */
	private static function get_failed_update_prompt() {

		$support_url    = WC_CP()->get_resource_url( 'ticket-form' );
		$support_link   = '<a href="' . $support_url . '">' . __( 'get in touch with us', 'woocommerce-composite-products' ) . '</a>';
		$support_prompt = sprintf( __( ' If this message persists, please restore your database from a backup, or %s.', 'woocommerce-composite-products' ), $support_link );

		return $support_prompt;
	}

	/**
	 * Dismisses a notice. Dismissible maintenance notices cannot be dismissed forever.
	 *
	 * @since  3.14.0
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
		if ( isset( $_GET[ 'dismiss_wc_cp_notice' ] ) && isset( $_GET[ '_wc_cp_admin_nonce' ] ) ) {
			if ( ! wp_verify_nonce( wc_clean( $_GET[ '_wc_cp_admin_nonce' ] ), 'wc_cp_dismiss_notice_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'woocommerce' ) );
			}

			$notice = sanitize_text_field( $_GET[ 'dismiss_wc_cp_notice' ] );

			self::dismiss_notice( $notice );
		}
	}
}

WC_CP_Admin_Notices::init();
