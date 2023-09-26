<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Email_Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Event_Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Frontend_Handler;

defined( 'ABSPATH' ) or exit;

/**
 * The tracking class.
 *
 * @since 2.0.0
 */
class Tracking {


	/** @var array cache for user tracking status **/
	private static array $user_tracking_enabled = [];

	/** @var Event_Tracking instance **/
	protected Event_Tracking $event_tracking;

	/** @var Email_Tracking instance **/
	protected Email_Tracking $email_tracking;

	/** @var Frontend_Handler the frontend handler instance */
	protected Frontend_Handler $frontend;


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->event_tracking = new Event_Tracking();
		$this->email_tracking = new Email_Tracking();
		$this->frontend       = new Frontend_Handler();

		add_filter( 'woocommerce_get_return_url', [ $this, 'google_ads_referrer_remove_gateways' ] );
	}


	/**
	 * Gets the event tracking instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Event_Tracking
	 */
	public function get_event_tracking_instance() : Event_Tracking {
		return $this->event_tracking;
	}


	/**
	 * Gets the email tracking instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Email_Tracking
	 */
	public function get_email_tracking_instance() : Email_Tracking {
		return $this->email_tracking;
	}


	/**
	 * Gets the Frontend handler instance
	 *
	 * @return Frontend_Handler
	 *@since 2.0.0
	 *
	 */
	public function get_frontend_handler_instance() : Frontend_Handler {
		return $this->frontend;
	}


	/**
	 * Determines if tracking is disabled.
	 *
	 * TODO: consider refactoring this method, perhaps into the inverse (ie, `should_track()`), as it's currently quite confusing {@itambek 2023-03-09}
	 *
	 * @since 2.0.0
	 *
	 * @param  bool $admin_event  (optional) Whether this is an admin event that should be tracked. Defaults to false.
	 * @param  int|null  $user_id (optional) User ID to check roles for
	 * @return bool
	 */
	public static function do_not_track( bool $admin_event = false, int $user_id = null ): bool {

		// do not track activity in the admin area, unless specified
		if ( ! $admin_event && ! wp_doing_ajax() && is_admin() ) {
			$do_not_track = true;
		} else {
			$do_not_track = ! self::is_tracking_enabled_for_user_role( $user_id );
		}

		/**
		 * Filters whether tracking should be disabled.
		 *
		 * @since 1.5.0
		 *
		 * @param bool $do_not_track
		 * @param bool $admin_event
		 * @param int  $user_id
		 */
		return (bool) apply_filters( 'wc_google_analytics_pro_do_not_track', $do_not_track, $admin_event, $user_id );
	}


	/**
	 * Determines if tracking should be performed for the provided user, by the role.
	 *
	 * @since 2.0.0
	 *
	 * @param ?int $user_id (optional) user id to check, defaults to current user id
	 * @return bool
	 */
	public static function is_tracking_enabled_for_user_role( ?int $user_id = null ): bool {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! wc_google_analytics_pro()->get_integration()->is_enabled() ) {

			self::$user_tracking_enabled[ $user_id ] = false;

		} elseif ( ! isset( self::$user_tracking_enabled[ $user_id ] ) ) {

			// enable tracking by default for all users and visitors
			$enabled = true;

			// get user's info
			$user = get_user_by( 'id', $user_id );

			if ( $user && user_can( $user_id, 'manage_woocommerce' ) ) {

				// Enable tracking of admins and shop managers only if checked in settings.
				$enabled = wc_string_to_bool( wc_google_analytics_pro()->get_integration()->get_option( 'admin_tracking_enabled' ) );

			}

			self::$user_tracking_enabled[ $user_id ] = $enabled;
		}

		return self::$user_tracking_enabled[ $user_id ];
	}


	/**
	 * Determines if user ID tracking is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function is_user_id_tracking_enabled(): bool {

		return wc_string_to_bool( wc_google_analytics_pro()->get_integration()->get_option( 'track_user_id' ) );
	}


	/**
	 * Determines whether cart & order value (revenue) should be tracked with tax and shipping included.
	 *
	 * @since 2.0.10
	 *
	 * @return bool
	 */
	public static function revenue_should_include_tax_and_shipping() : bool {

		return wc_string_to_bool( wc_google_analytics_pro()->get_integration()->get_option( 'include_tax_and_shipping_in_revenue' ) );
	}


	/**
	 * Determines if a request was not a page reload.
	 *
	 * TODO: consider refactoring this method, perhaps into the inverse (ie, `is_page_reload()`), as it's currently somewhat confusing {@itambek 2023-03-09}
	 *
	 * Prevents duplication of tracking events when user submits
	 * a form, e.g. applying a coupon on the cart page.
	 *
	 * This is not intended to prevent pageview events on a manual page refresh.
	 * Those are valid user interactions and should still be tracked.
	 *
	 * @since 2.0.0
	 *
	 * @return bool true if not a page reload, false if page reload
	 */
	public static function not_page_reload(): bool {

		// no referer... consider it's not a reload.
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			return true;
		}

		// compare paths
		return ( parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_PATH ) !== parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
	}


	/**
	 * Gets the configured Google Analytics measurement ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null the measurement ID
	 */
	public static function get_measurement_id(): ?string {

		return wc_google_analytics_pro()->get_integration()->get_measurement_id();
	}


	/**
	 * Gets the configured Google Analytics tracking ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string the tracking ID
	 */
	public static function get_tracking_id(): string {

		return wc_google_analytics_pro()->get_integration()->get_tracking_id();
	}


	/**
	 * Ensures Google Ads doesn't mistake the offsite gateway as the
	 * referrer by adding the `utm_nooverride` parameter.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $return_url WooCommerce return URL
	 * @return string the return URL
	 */
	public function google_ads_referrer_remove_gateways(string $return_url ): string {

		return add_query_arg( 'utm_nooverride', '1', remove_query_arg( 'utm_nooverride', $return_url ) );
	}


	/**
	 * Gets event params for debug mode.
	 *
	 * Only returns the debug mode property if debug mode is enabled. Note that setting `{ debug_mode: false }` does not
	 * disable debug mode - the property must not exist.
	 *
	 * @link https://support.google.com/analytics/answer/7201382?hl=en
	 *
	 * @since 2.0.9
	 *
	 * @return array { debug_mode: true } if debug mode is enabled, otherwise an empty array
	 */
	public static function get_debug_mode_params(): array {

		return wc_google_analytics_pro()->get_integration()->debug_mode_on() ? [ 'debug_mode' => true ] : [];
	}


}
