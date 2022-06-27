<?php
/**
 * WC_PB_Admin_Notices class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WC_PB_Admin_Notices
 * @version  6.14.1
 */
class WC_PB_Admin_Notices {

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
		'update'                  => 'update_notice',
		'welcome'                 => 'welcome_notice',
		'loopback'                => 'loopback_notice',
		'update_order_item_stats' => 'update_order_item_stats_notice'

	);

	/**
	 * Constructor.
	 */
	public static function init() {

		if ( ! class_exists( 'WC_PB_Notices' ) ) {
			require_once  WC_PB_ABSPATH . 'includes/class-wc-pb-notices.php' ;
		}

		// Avoid duplicates for some notice types that are meant to be unique.
		if ( ! isset( $GLOBALS[ 'sw_store' ][ 'notices_unique' ] ) ) {
			$GLOBALS[ 'sw_store' ][ 'notices_unique' ] = array();
		}

		self::$maintenance_notices = get_option( 'wc_pb_maintenance_notices', array() );
		self::$dismissed_notices   = get_user_meta( get_current_user_id(), 'wc_pb_dismissed_notices', true );
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
			$save_notice    = isset( $args[ 'save_notice' ] ) ? $args[ 'save_notice' ] : $save_notice;
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
	 * @since  6.3.0
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
	 * @since  6.3.0
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function get_notice_option( $notice_name, $key, $default = null ) {
		return WC_PB_Notices::get_notice_option( $notice_name, $key, $default );
	}

	/**
	 * Set a setting for a notice type.
	 *
	 * @since  6.3.0
	 *
	 * @param  string  $notice_name
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return array
	 */
	public static function set_notice_option( $notice_name, $key, $value ) {
		return WC_PB_Notices::set_notice_option( $notice_name, $key, $value );
	}

	/**
	 * Checks if a maintenance notice is visible.
	 *
	 * @since  5.8.0
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
	 * @since  5.8.0
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
		update_option( 'wc_pb_meta_box_notices', self::$meta_box_notices );
		update_option( 'wc_pb_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = get_option( 'wc_pb_meta_box_notices', array() );
		$notices       = array_merge( self::$admin_notices, $saved_notices );

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$notice_classes = array( 'wc_pb_notice', 'notice', 'notice-' . $notice[ 'type' ] );
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
						jQuery( '.wc_pb_notice .notice-dismiss' ).on( 'click', function() {

							var data = {
								action: 'woocommerce_dismiss_bundle_notice',
								notice: jQuery( this ).parent().data( 'dismiss_class' ),
								security: '" . wp_create_nonce( 'wc_pb_dismiss_notice_nonce' ) . "'
							};

							jQuery.post( '" . WC()->ajax_url() . "', data );
						} );
					} );
				" );
			}

			// Clear.
			delete_option( 'wc_pb_meta_box_notices' );
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
	 * @since  5.8.0
	 *
	 * @param  string  $text
	 * @param  mixed   $args
	 */
	public static function add_dismissible_notice( $text, $args ) {
		if ( ! isset( $args[ 'dismiss_class' ] ) || ! self::is_dismissible_notice_dismissed( $args[ 'dismiss_class' ] ) ) {
			self::add_notice( $text, $args );
		}
	}

	/**
	 * Remove a dismissible notice.
	 *
	 * @since  5.8.0
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_dismissible_notice( $notice_name ) {

		// Remove if not already removed.
		if ( ! self::is_dismissible_notice_dismissed( $notice_name ) ) {
			self::$dismissed_notices = array_merge( self::$dismissed_notices, array( $notice_name ) );
			update_user_meta( get_current_user_id(), 'wc_pb_dismissed_notices', self::$dismissed_notices );
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

		if ( ! class_exists( 'WC_PB_Install' ) ) {
			return;
		}

		if ( WC_PB_Install::is_update_pending() ) {

			$status = '';

			// Show notice to indicate that an update is in progress.
			if ( WC_PB_Install::is_update_process_running() || WC_PB_Install::is_update_queued() ) {

				$prompt = '';

				// Check if the update process is running.
				if ( false === WC_PB_Install::is_update_process_running() ) {
					$prompt = self::get_force_update_prompt();
				}

				/* translators: Force update prompt */
				$status = sprintf( __( '<strong>WooCommerce Product Bundles</strong> is updating your database.%s', 'woocommerce-product-bundles' ), $prompt );

			// Show a prompt to update.
			} elseif ( false === WC_PB_Install::auto_update_enabled() && false === WC_PB_Install::is_update_incomplete() ) {

				$status  = __( '<strong>WooCommerce Product Bundles</strong> has been updated! To keep things running smoothly, your database needs to be updated, as well.', 'woocommerce-product-bundles' );
				/* translators: Learn more link */
				$status .= '<br/>' . sprintf( __( 'Before you proceed, please take a few minutes to <a href="%s" target="_blank">learn more</a> about best practices when updating.', 'woocommerce-product-bundles' ), WC_PB()->get_resource_url( 'updating' ) );
				$status .= self::get_trigger_update_prompt();

			} elseif ( WC_PB_Install::is_update_incomplete() ) {

				/* translators: Failed update prompt */
				$status = sprintf( __( '<strong>WooCommerce Product Bundles</strong> has not finished updating your database.%s', 'woocommerce-product-bundles' ), self::get_failed_update_prompt() );
			}

			if ( $status ) {
				self::add_notice( $status, 'info' );
			}

		// Show persistent notice to indicate that the update process is complete.
		} else {
			$notice = __( '<strong>WooCommerce Product Bundles</strong> database update complete. Thank you for updating to the latest version!', 'woocommerce-product-bundles' );
			self::add_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'update' ) );
		}
	}

	/**
	 * Add 'welcome' notice.
	 *
	 * @since  5.9.0
	 */
	public static function welcome_notice() {

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

		ob_start();

		?>
		<p class="sw-welcome-text">
			<?php
				/* translators: onboarding url */
				echo wp_kses_post( sprintf( __( 'Thank you for installing <strong>WooCommerce Product Bundles</strong>. Ready to get started? <a href="%s">Click here to create your first bundle</a>.', 'woocommerce-product-bundles' ), admin_url( 'post-new.php?post_type=product&wc_pb_first_bundle=1' ) ) );
			?>
		</p>
		<?php

		$notice = ob_get_clean();

		self::add_dismissible_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'welcome' ) );
	}

	/**
	 * Add 'loopback' test.
	 *
	 * In PB, the ability to do loopback requests is nice to have, but the plugin will do fine even without them, in most cases.
	 * For this reason, we have decided to remove the notice and only let the test run to have a result in the Status Report.
	 *
	 * @since  6.3.0
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

		if ( ! function_exists( 'wc_enqueue_js' ) ) {
			return;
		}

		$last_tested   = self::get_notice_option( 'loopback', 'last_tested', 0 );
		$auto_run_test = gmdate( 'U' ) - $last_tested > DAY_IN_SECONDS;

		if ( ! $auto_run_test ) {
			return;
		}

		wc_enqueue_js( "
			jQuery( function( $ ) {

				var do_loopback_test = function() {

					var data = {
						action: 'woocommerce_bundles_health-check-loopback_test',
						security: '" . wp_create_nonce( 'wc_pb_loopback_notice_nonce' ) . "'
					};

					jQuery.post( '" . WC()->ajax_url() . "', data, function( response ) {
						return true;
					} );
				};

				do_loopback_test();
			} );
		" );
	}

	/**
	 * Adds a notice to migrate order revenue analytics to account for GCs correctly.
	 *
	 * @since  6.9.0
	 */
	public static function update_order_item_stats_notice() {

		if ( ! method_exists( WC(), 'queue' ) || ! class_exists( 'WC_PB_Admin_Analytics_Sync' ) || ! WC_PB_Core_Compatibility::is_wc_admin_active() ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins'
		);

		// Notices should only show and get scheduled on the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		if ( WC_PB_Admin_Analytics_Sync::is_order_item_stats_update_queued() ) {

			$notice = __( '<strong>WooCommerce Product Bundles</strong> is updating your historical revenue Analytics data. This may take a while, so please be patient!', 'woocommerce-product-bundles' );
			self::add_notice( $notice, 'info' );

		} else {

			$notice = __( '<strong>WooCommerce Product Bundles</strong> has finished updating your revenue Analytics data!', 'woocommerce-product-bundles' );
			self::add_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'update_order_item_stats' ) );
		}
	}

	/**
	 * Returns a "trigger update" notice component.
	 *
	 * @since  5.5.0
	 *
	 * @return string
	 */
	private static function get_trigger_update_prompt() {
		$update_url    = esc_url( wp_nonce_url( add_query_arg( 'trigger_wc_pb_db_update', true, admin_url() ), 'wc_pb_trigger_db_update_nonce', '_wc_pb_admin_nonce' ) );
		$update_prompt = '<p><a href="' . $update_url . '" class="wc-pb-update-now button">' . __( 'Update database', 'woocommerce-product-bundles' ) . '</a></p>';
		return $update_prompt;
	}

	/**
	 * Returns a "force update" notice component.
	 *
	 * @since  5.5.0
	 *
	 * @return string
	 */
	private static function get_force_update_prompt() {

		$fallback_prompt = '';
		$update_runtime  = get_option( 'wc_pb_update_init', 0 );

		// Wait for at least 30 seconds.
		if ( gmdate( 'U' ) - $update_runtime > 30 ) {
			// Perhaps the upgrade process failed to start?
			$fallback_url    = esc_url( wp_nonce_url( add_query_arg( 'force_wc_pb_db_update', true, admin_url() ), 'wc_pb_force_db_update_nonce', '_wc_pb_admin_nonce' ) );
			$fallback_link   = '<a href="' . $fallback_url . '">' . __( 'run it manually', 'woocommerce-product-bundles' ) . '</a>';
			/* translators: Run manually link */
			$fallback_prompt = sprintf( __( ' The process seems to be taking a little longer than usual, so let\'s try to %s.', 'woocommerce-product-bundles' ), $fallback_link );
		}

		return $fallback_prompt;
	}

	/**
	 * Returns a "failed update" notice component.
	 *
	 * @since  5.5.0
	 *
	 * @return string
	 */
	private static function get_failed_update_prompt() {

		$support_url    = WC_PB()->get_resource_url( 'ticket-form' );
		$support_link   = '<a href="' . $support_url . '">' . __( 'get in touch with us', 'woocommerce-product-bundles' ) . '</a>';
		/* translators: Get in touch link */
		$support_prompt = sprintf( __( ' If this message persists, please restore your database from a backup, or %s.', 'woocommerce-product-bundles' ), $support_link );

		return $support_prompt;
	}

	/**
	 * Dismisses a notice. Dismissible maintenance notices cannot be dismissed forever.
	 *
	 * @since  5.8.0
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
	| Notes for the WC Admin Inbox.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add note.
	 *
	 * @since  6.3.0
	 *
	 * @param  array|string  $args
	 */
	public static function add_note( $args ) {

		if ( ! class_exists( 'WC_PB_Core_Compatibility' ) ) {
			require_once  WC_PB_ABSPATH . 'includes/compatibility/core/class-wc-pb-core-compatibility.php' ;
		}

		if ( ! WC_PB_Core_Compatibility::is_wc_admin_active() ) {
			return;
		}

		$note_class = false;

		if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Note' ) ) {
			$note_class = 'Automattic\WooCommerce\Admin\Notes\Note';
		} elseif ( class_exists( 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Note' ) ) {
			$note_class = 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Note';
		} else {
			return;
		}

		if ( ! is_array( $args ) ) {
			$args = self::get_note_args( $args );
		}

		if ( ! is_array( $args ) ) {
			return;
		}

		$default_args = array(
			'name'         => '',
			'title'        => '',
			'content'      => '',
			'type'         => $note_class::E_WC_ADMIN_NOTE_INFORMATIONAL,
			'source'       => '',
			'icon'         => '',
			'check_plugin' => '',
			'actions'      => array()
		);

		$args = wp_parse_args( $args, $default_args );

		if ( empty( $args[ 'name' ] ) || empty( $args[ 'title' ] ) || empty( $args[ 'content' ] ) || empty( $args[ 'type' ] ) || empty( $args[ 'icon' ] ) ) {
			return false;
		}

		// First, see if we've already created this note so we don't do it again.
		$data_store = WC_Data_Store::load( 'admin-note' );
		$note_ids   = $data_store->get_notes_with_name( $args[ 'name' ] );
		if ( ! empty( $note_ids ) ) {
			return;
		}

		// Otherwise, add the note.
		$note = new $note_class();

		$note->set_name( $args[ 'name' ] );
		$note->set_title( $args[ 'title' ] );
		$note->set_content( $args[ 'content' ] );
		$note->set_type( $args[ 'type' ] );

		if ( ! method_exists( $note, 'set_image' ) ) {
			$note->set_icon( $args[ 'icon' ] );
		}

		if ( $args[ 'source' ] ) {
			$note->set_source( $args[ 'source' ] );
		}

		if ( is_array( $args[ 'actions' ] ) ) {
			foreach ( $args[ 'actions' ] as $action ) {
				if ( empty( $action[ 'name' ] ) || empty( $action[ 'label' ] ) ) {
					continue;
				}
				$note->add_action( $action[ 'name' ], $action[ 'label' ], empty( $action[ 'url' ] ) ? false : $action[ 'url' ], empty( $action[ 'status' ] ) ? $note_class::E_WC_ADMIN_NOTE_UNACTIONED : $action[ 'status' ], empty( $action[ 'primary' ] ) ? false : $action[ 'primary' ] );
			}
		}

		// Check if plugin installed or activated.
		if ( ! empty( $args[ 'check_plugin' ] ) ) {
			if ( WC_PB_Notices::is_feature_plugin_installed( $args[ 'name' ] ) ) {
				$note->set_status( $note_class::E_WC_ADMIN_NOTE_ACTIONED );
			}
		}

		$note->save();
	}

	/**
	 * Get note data.
	 *
	 * @since 6.3.0
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function get_note_args( $name ) {

		$note_class = false;

		if ( class_exists( 'Automattic\WooCommerce\Admin\Notes\Note' ) ) {
			$note_class = 'Automattic\WooCommerce\Admin\Notes\Note';
		} elseif ( class_exists( 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Note' ) ) {
			$note_class = 'Automattic\WooCommerce\Admin\Notes\WC_Admin_Note';
		} else {
			return;
		}

		if ( 'bulk-discounts' === $name ) {

			ob_start();

			?>
			<p><?php _e( 'Did you know that you can use <strong>Product Bundles</strong> to offer bulk quantity discounts? ', 'woocommerce-product-bundles' ); ?></p>
			<p><?php _e( 'Grab the free <strong>Bulk Discounts</strong> add-on, and offer lower prices to those who purchase more!', 'woocommerce-product-bundles' ); ?></p>
			<?php

			$content = ob_get_clean();

			$args = array(
				'name'         => 'wc-pb-bulk-discounts',
				'title'        => __( 'Ready to start offering bulk discounts?', 'woocommerce-product-bundles' ),
				'content'      => $content,
				'type'         => $note_class::E_WC_ADMIN_NOTE_INFORMATIONAL,
				'source'       => 'woocommerce-product-bundles',
				'icon'         => 'plugins',
				'check_plugin' => true,
				'actions'      => array(
					array(
						'name'  => 'learn-more-bulk-discounts',
						'label' => __( 'Learn more', 'woocommerce-product-bundles' ),
						'url'   => WC_PB()->get_resource_url( 'bulk-discounts' )
					)
				)
			);

			return $args;
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
		if ( isset( $_GET[ 'dismiss_wc_pb_notice' ] ) && isset( $_GET[ '_wc_pb_admin_nonce' ] ) ) {
			if ( ! wp_verify_nonce( wc_clean( $_GET[ '_wc_pb_admin_nonce' ] ), 'wc_pb_dismiss_notice_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'woocommerce' ) );
			}

			$notice = sanitize_text_field( $_GET[ 'dismiss_wc_pb_notice' ] );

			self::dismiss_notice( $notice );
		}
	}
}

WC_PB_Admin_Notices::init();
