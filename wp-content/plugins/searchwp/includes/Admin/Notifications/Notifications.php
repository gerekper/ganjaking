<?php

namespace SearchWP\Admin\Notifications;

use SearchWP\License;
use SearchWP\Settings;
use SearchWP\Utils;

/**
 * Class for managing in-plugin notifications.
 *
 * @since 4.2.8
 */
class Notifications {

	/**
	 * URL for fetching remote notifications.
	 *
	 * @since 4.2.8
	 */
	private const SOURCE_URL = 'https://plugin.searchwp.com/wp-content/notifications.json';

	/**
	 * Name of the WP option to save the notifications data to.
	 *
	 * @since 4.2.8
	 */
	private const OPTION_NAME = 'searchwp_admin_notifications';

	/**
	 * Internal constant to populate the Notifications panel bypassing all checks.
     * Change false to true to enable.
	 *
	 * @since 4.3.0
	 */
	private const TEST_MODE = false;

	/**
	 * Init.
	 *
	 * @since 4.2.8
	 */
	public static function init() {

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets_global' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );

        add_action( 'admin_bar_menu', [ __CLASS__, 'add_admin_bar_menu_item' ] );
		add_filter( 'searchwp\admin_bar\notifications_count', [ __CLASS__, 'increment_admin_bar_notifications_count' ] );

		add_filter( 'searchwp\options\submenu_pages', [ __CLASS__, 'add_submenu_page' ] );

		add_action( 'searchwp\settings\header\actions', [ __CLASS__, 'output_header_button' ] );
		add_action( 'searchwp\settings\header\after', [ __CLASS__, 'output_panel' ] );

		add_action( 'wp_ajax_searchwp_notification_dismiss', [ __CLASS__, 'dismiss' ] );

        add_action( 'searchwp\settings\update\license', [ __CLASS__, 'fetch_notifications_on_license_change' ] );
	}

	/**
	 * Check if user has access.
	 *
	 * @since 4.2.8
	 *
	 * @return bool
	 */
	public static function has_access() {

		return current_user_can( Settings::get_capability() ) && ! Settings::get( 'hide-announcements' );
	}

	/**
	 * Register global assets.
	 *
	 * @since 4.2.8
	 */
	public static function assets_global() {

		if ( ! self::has_access() ) {
			return;
		}

		if ( empty( self::get_count() ) ) {
            return;
        }

        wp_enqueue_style(
            Utils::$slug . '_admin_notifications_global',
            SEARCHWP_PLUGIN_URL . 'assets/styles/admin/notifications-global.css',
            [],
            SEARCHWP_VERSION
        );
	}

	/**
	 * Register assets.
	 *
	 * @since 4.2.8
	 */
	public static function assets() {

		if ( ! self::has_access() ) {
			return;
		}

		if ( ! Utils::is_swp_admin_page() ) {
			return;
		}

		wp_enqueue_style(
			Utils::$slug . '_admin_notifications',
			SEARCHWP_PLUGIN_URL . 'assets/styles/admin/notifications.css',
			[],
			SEARCHWP_VERSION
		);

		wp_enqueue_script(
			Utils::$slug . '_admin_notifications',
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/panels/notifications.js',
			[],
			SEARCHWP_VERSION,
			true
		);

        Utils::localize_script( Utils::$slug . '_admin_notifications' );
	}

	/**
	 * Add Notifications pseudo submenu item to the SearchWP admin bar menu.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance, passed by reference.
	 *
	 * @since 4.2.8
	 */
    public static function add_admin_bar_menu_item( $wp_admin_bar ) {

	    if ( ! is_admin_bar_showing() ) {
		    return;
	    }

	    if ( ! apply_filters( 'searchwp\admin_bar', current_user_can( Settings::get_capability() ) ) ) {
		    return;
	    }

	    if ( ! self::has_access() ) {
		    return;
	    }

	    $wp_admin_bar->add_menu(
		    [
			    'parent' => Utils::$slug,
			    'id'     => Utils::$slug . '_notifications',
			    'title'  => __( 'Notifications', 'searchwp' ) . '<span style="margin-top: 10px; line-height: 1;" class="searchwp-admin-menu-notification-indicator"></span>',
			    'href'   => esc_url( add_query_arg( [ 'page' => 'searchwp-settings#notifications' ], admin_url( 'admin.php' ) ) ),
		    ]
	    );
    }

	/**
	 * Increment admin bar notifications count if there are active notifications.
	 *
	 * @param int $count Current admin bar notifications count.
	 *
	 * @since 4.2.8
     *
     * @return int
	 */
    public static function increment_admin_bar_notifications_count( $count ) {

        $notifications_count = self::get_count();

        if ( $notifications_count ) {
            $count = (int) $count + $notifications_count;
        }

        return $count;
    }

	/**
	 * Add Notifications pseudo submenu item to the SearchWP admin menu.
	 *
	 * @param array $submenu_pages List of registered SearchWP submenu pages.
	 *
	 * @since 4.2.8
     *
     * @return array
	 */
	public static function add_submenu_page( $submenu_pages ) {

		if ( ! self::has_access() ) {
			return $submenu_pages;
		}

		if ( empty( self::get_count() ) ) {
			return $submenu_pages;
        }

		$submenu_pages['notifications'] = [
			'menu_title' => esc_html__( 'Notifications', 'searchwp' ) . '<span style="margin-top: 6px;" class="searchwp-admin-menu-notification-indicator"></span>',
			'menu_slug'  => 'searchwp-settings#notifications',
			'position'   => 0,
		];

		return $submenu_pages;
	}

	/**
	 * Output header action button.
	 *
	 * @since 4.2.8
	 */
	public static function output_header_button() {

		if ( ! self::has_access() ) {
			return;
		}

		$notifications = self::get();

		?>
        <div id="swp-notifications-page-header-button" class="swp-header-menu--item swp-relative" title="<?php esc_html_e( 'Notifications', 'searchwp' ); ?>">
            <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.3333 0.5H1.66667C0.75 0.5 0 1.25 0 2.16667V13.8333C0 14.75 0.741667 15.5 1.66667 15.5H13.3333C14.25 15.5 15 14.75 15 13.8333V2.16667C15 1.25 14.25 0.5 13.3333 0.5ZM13.3333 13.8333H1.66667V11.3333H4.63333C5.20833 12.325 6.275 13 7.50833 13C8.74167 13 9.8 12.325 10.3833 11.3333H13.3333V13.8333ZM9.175 9.66667H13.3333V2.16667H1.66667V9.66667H5.84167C5.84167 10.5833 6.59167 11.3333 7.50833 11.3333C8.425 11.3333 9.175 10.5833 9.175 9.66667Z" fill="#0E2121" fill-opacity="0.6"/>
            </svg>

            <?php if ( ! empty( $notifications ) ) : ?>
                <div class="swp-badge">
                    <span><?php echo count( $notifications ); ?></span>
                </div>
            <?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Output main notifications panel.
	 *
	 * @since 4.2.8
	 */
	public static function output_panel() {

		$notifications = self::get();

		?>
        <div class="swp-notifications-panel--wrapper" style="display: none;">

            <div class="swp-notifications-panel components-animate__slide-in is-from-left">

                <div class="swp-notifications-panel--header">

                    <span>
						<span>
							<?php echo count( $notifications ); ?>
						</span>
						Unread Notifications
					</span>
                    
					<button type="button" class="swp-notifications-panel--close"
                            aria-label="Close notifications">
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             aria-hidden="true" focusable="false">
                            <path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z" fill="#ffffff"></path>
                        </svg>
                    </button>
                </div>

                <div class="swp-notifications-panel--notifications">
					<?php
                        foreach ( $notifications as $notification ) {
                            self::output_panel_notification_single( $notification );
                        }
                    ?>
                </div>

            </div>
            <div class="swp-notifications-backdrop"></div>
        </div>
		<?php
	}

	/**
	 * Output single notification in the main notifications panel.
	 *
	 * @since 4.2.8
     *
     * @param array $notification Single notification data.
	 */
    private static function output_panel_notification_single( $notification ) {

        ?>
        <div class="swp-notifications--notification swp-flex--row swp-flex--gap12">

            <div class="swp-notifications--notification-icon">

				<svg width="17" height="13" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.80001 12.1929L3.95357 8.34645L3.60001 7.9929L3.24646 8.34645L1.84646 9.74645L1.49291 10.1L1.84646 10.4536L7.44646 16.0536L7.80001 16.4071L8.15357 16.0536L20.1536 4.05356L20.5071 3.7L20.1536 3.34645L18.7536 1.94645L18.4 1.5929L18.0465 1.94645L7.80001 12.1929Z" fill="#437E47" stroke="#437E47"/>
				</svg>

            </div>

            <div class="swp-flex--item swp-flex--grow1">

                <div class="swp-flex--row swp-justify-between swp-flex--gap25">

                    <h2 class="swp-h2 swp-font-size16"><?php echo esc_html( $notification['title'] ); ?></h2>
                    
					<div class="swp-notifications--notification-date"><?php echo esc_html( human_time_diff( strtotime( $notification['start'] ), strtotime( current_time( 'mysql' ) ) ) ); ?>
                        ago
                    </div>

                </div>
				
				<p class="swp-p swp-margin-t15"><?php echo wp_kses_post( $notification['content'] ); ?></p>
                
				<div class="swp-flex--row swp-flex--wrap swp-flex--gap12 swp-flex--align-c swp-margin-t15">
				    
					<?php foreach ( $notification['actions'] as $notification_action ) : ?>
                        
						<a href="<?php echo esc_url( $notification_action['url'] ); ?>" target="_blank" class="swp-button is-<?php echo esc_attr( $notification_action['type'] ); ?>">
                            <?php echo esc_html( $notification_action['text'] ); ?>
                        </a>

				    <?php endforeach; ?>

                    <button type="button" class="swp-notification--dismiss" data-id="<?php echo absint( $notification['remote_id'] ); ?>">
                        <?php esc_html_e( 'Dismiss', 'searchwp' ); ?>
                    </button>

                </div>

            </div>

        </div>
        <?php
    }

	/**
	 * Get available notifications.
     *
     * @since 4.2.8
	 *
	 * @return array
	 */
	public static function get() {

		if ( ! self::has_access() ) {
			return [];
		}

		$option = self::get_option();

		// Fetch remote notifications every 12 hours.
		if ( self::TEST_MODE || empty( $option['updated_at'] ) || time() > $option['updated_at'] + ( 12 * HOUR_IN_SECONDS ) ) {
			self::save( self::fetch() );
			$option = self::get_option( [ 'cache' => false ] ); // Make sure the notifications are available right away.
		}

		return ! empty( $option['notifications'] ) ? self::filter_active( $option['notifications'] ) : [];
	}

	/**
	 * Get available notifications count.
     *
     * @since 4.2.8
	 *
	 * @return int
	 */
	public static function get_count() {

		if ( ! self::has_access() ) {
			return 0;
		}

		return count( self::get() );
	}

	/**
	 * Fetch notifications from the remote server.
	 *
	 * @since 4.2.8
	 *
	 * @return array
	 */
	private static function fetch() {

		$request = wp_remote_get(
			self::SOURCE_URL,
			[ 'sslverify' => false ]
		);

		if ( is_wp_error( $request ) ) {
			return [];
		}

		$response      = wp_remote_retrieve_body( $request );
		$notifications = ! empty( $response ) ? json_decode( $response, true ) : [];

		if ( ! is_array( $notifications ) ) {
			return [];
		}

		return self::filter_fetched( $notifications );
	}

	/**
	 * Parse single notification data.
	 *
	 * @since 4.2.8
	 *
	 * @param array $notification Raw data to parse.
	 *
	 * @return array
	 */
	private static function parse_notification( $notification ) {

		$remote_id  = ! empty( $notification['id'] ) ? $notification['id'] : '0';
		$type       = ! empty( $notification['notification_type'] ) ? $notification['notification_type'] : 'info';
		$title      = ! empty( $notification['title'] ) ? $notification['title'] : '';
		$slug       = ! empty( $notification['slug'] ) ? $notification['slug'] : $title;
		$content    = ! empty( $notification['content'] ) ? $notification['content'] : '';
		$buttons    = ! empty( $notification['btns'] ) && is_array( $notification['btns'] ) ? $notification['btns'] : [];
		$conditions = ! empty( $notification['type'] ) && is_array( $notification['type'] ) ? $notification['type'] : [];
		$start      = ! empty( $notification['start'] ) ? $notification['start'] : date( 'Y-m-d H:i:s' );
		$end        = ! empty( $notification['end'] ) ? $notification['end'] : date( 'Y-m-d H:i:s', time() + ( YEAR_IN_SECONDS * 1 ) );

		return [
			'remote_id'  => sanitize_text_field( $remote_id ),
			'type'       => sanitize_text_field( $type ),
			'title'      => esc_html( $title ),
			'slug'       => sanitize_title( $slug ),
			'content'    => wp_kses_post( $content ),
			'actions'    => self::parse_notification_actions( $buttons ),
			'conditions' => array_map( 'sanitize_text_field', $conditions ),
			'start'      => sanitize_text_field( $start ),
			'end'        => sanitize_text_field( $end ),
		];
	}

	/**
	 * Parse single notification actions data.
	 *
	 * @since 4.2.8
	 *
	 * @param array $buttons Raw data to parse.
	 *
	 * @return array
	 */
    private static function parse_notification_actions( $buttons ) {

	    $actions = [];

	    foreach ( $buttons as $type => $btn ) {

		    $button_type = $type === 'main' ? 'primary' : 'secondary';

		    $actions[] = [
			    'type' => sanitize_text_field( $button_type ),
			    'url'  => esc_url_raw( $btn['url'] ),
			    'text' => esc_html( $btn['text'] ),
		    ];
	    }

        return $actions;
    }

	/**
	 * Filter fetched notifications before saving.
	 *
	 * @since 4.2.8
     *
     * @param array $notifications Array of notifications items to verify.
	 *
     * @return array
	 */
	private static function filter_fetched( $notifications ) {

		$data = [];

		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return $data;
		}

		foreach ( $notifications as $notification ) {
			if ( self::verify_single( $notification ) ) {
				$data[] = self::parse_notification( $notification );
			}
		}

		return $data;
	}

	/**
	 * Filter active notifications and remove outdated ones.
	 *
	 * @since 4.2.8
     *
     * @param array $notifications Array of notifications items to filter.
	 *
     * @return array
	 */
	private static function filter_active( $notifications ) {

		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return [];
		}

        if ( self::TEST_MODE ) {
            return $notifications;
        }

		// Remove notifications that are not active.
		foreach ( $notifications as $key => $notification ) {
			if (
				( ! empty( $notification['start'] ) && time() < strtotime( $notification['start'] ) ) ||
				( ! empty( $notification['end'] ) && time() > strtotime( $notification['end'] ) )
			) {
				unset( $notifications[ $key ] );
			}
		}

		return $notifications;
	}

	/**
	 * Verify a single notification data.
	 *
	 * @since 4.2.8
	 *
	 * @param array $notification Notification data to verify.
	 *
	 * @return bool
	 */
	private static function verify_single( $notification ) {

        if ( self::TEST_MODE ) {
            return true;
        }

		// The message and license should never be empty, if they are, ignore.
		if ( empty( $notification['content'] ) || empty( $notification['type'] ) ) {
			return false;
		}

		$license_type = License::get_type();

        if ( empty( $license_type ) ) {
            $license_type = 'inactive';
        }

		// Ignore if license type does not match.
		if ( ! in_array( $license_type, $notification['type'], true ) ) {
			return false;
		}

		// Ignore if expired.
		if ( ! empty( $notification['end'] ) && time() > strtotime( $notification['end'] ) ) {
			return false;
		}

		$option = self::get_option();

		// Ignore if notification has already been dismissed.
		if ( ! empty( $option['dismissed_ids'] ) && in_array( $notification['id'], $option['dismissed_ids'] ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return false;
		}

		return true;
	}

	/**
	 * Get option value.
	 *
	 * @since 4.2.8
	 *
	 * @param array $args Method arguments.
	 *
	 * @return array
	 */
	private static function get_option( $args = [] ) {

		static $option_cache;

		if ( ! isset( $args['cache'] ) ) {
			$args['cache'] = true;
		}

		if ( $option_cache && ! empty( $args['cache'] ) ) {
			return $option_cache;
		}

		$option = get_option( self::OPTION_NAME, [] );

		if ( empty( $args['cache'] ) ) {
			return $option;
		}

		$option_cache = [
			'updated_at'    => ! empty( $option['updated_at'] ) ? $option['updated_at'] : 0,
			'dismissed_ids' => ! empty( $option['dismissed_ids'] ) ? $option['dismissed_ids'] : [],
			'notifications' => ! empty( $option['notifications'] ) ? $option['notifications'] : [],
		];

		return $option_cache;
	}

	/**
	 * Save notifications data in the database.
	 *
	 * @param array $notifications Array of notifications data to save.
	 *
	 * @since 4.2.8
	 */
	private static function save( $notifications ) {

		$option = self::get_option();

		update_option(
			self::OPTION_NAME,
			[
				'updated_at'    => time(),
				'dismissed_ids' => $option['dismissed_ids'],
				'notifications' => $notifications,
			]
		);
	}

	/**
	 * Dismiss notification via AJAX.
	 *
	 * @since 4.2.8
	 */
	public static function dismiss() {

		Utils::check_ajax_permissions();

        if ( Settings::get( 'hide-announcements' ) ) {
	        wp_send_json_error();
        }

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$option = self::get_option();

		$option['dismissed_ids'][] = $id;
		$option['dismissed_ids']   = array_unique( $option['dismissed_ids'] );

		// Remove notification.
		if ( is_array( $option['notifications'] ) && ! empty( $option['notifications'] ) ) {
			foreach ( $option['notifications'] as $key => $notification ) {
				if ( (int) $notification['remote_id'] === (int) $id ) {
					unset( $option['notifications'][ $key ] );
					break;
				}
			}
		}

		update_option( self::OPTION_NAME, $option );

		wp_send_json_success();
	}

	/**
	 * Force fetch remote notifications on license change.
	 *
	 * @since 4.2.8
	 */
    public static function fetch_notifications_on_license_change() {

	    $option = self::get_option();

        // Reset the last updated date to force the remote notifications fetch.
	    $option['updated_at'] = 0;

	    update_option( self::OPTION_NAME, $option );
    }
}
