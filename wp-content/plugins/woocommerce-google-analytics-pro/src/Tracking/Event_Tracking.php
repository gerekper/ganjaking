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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Identity_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integration;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Plugin;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4\Custom_Event;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;
use function Sodium\add;

defined( 'ABSPATH' ) or exit;

/**
 * The event tracking class.
 *
 * @since 2.0.0
 */
class Event_Tracking {


	/**
	 * The order of event classes is significant - it determines their order in integration form fields.
	 *
	 * @var string[] event classes to register
	 */
	protected array $event_classes = [
		// GA4 events
		Tracking\Events\GA4\Login_Event::class,
		Tracking\Events\GA4\Logout_Event::class,
		Tracking\Events\GA4\View_Sign_Up_Event::class,
		Tracking\Events\GA4\Sign_Up_Event::class,
		Tracking\Events\GA4\View_Homepage_Event::class,
		Tracking\Events\GA4\View_Item_List_Event::class,
		Tracking\Events\GA4\View_Item_Event::class,
		Tracking\Events\GA4\Select_Item_Event::class,
		Tracking\Events\GA4\Add_To_Cart_Event::class,
		Tracking\Events\GA4\Remove_From_Cart_Event::class,
		Tracking\Events\GA4\Change_Cart_Quantity_Event::class,
		Tracking\Events\GA4\View_Cart_Event::class,
		Tracking\Events\GA4\Apply_Coupon_Event::class,
		Tracking\Events\GA4\Remove_Coupon_Event::class,
		Tracking\Events\GA4\Begin_Checkout_Event::class,
		Tracking\Events\GA4\Provide_Billing_Email_Event::class,
		Tracking\Events\GA4\Add_Shipping_Info_Event::class,
		Tracking\Events\GA4\Add_Payment_Info_Event::class,
		Tracking\Events\GA4\Place_Order_Event::class,
		Tracking\Events\GA4\Start_Payment_Event::class,
		Tracking\Events\GA4\Purchase_Event::class,
		Tracking\Events\GA4\Review_Event::class,
		Tracking\Events\GA4\Comment_Event::class,
		Tracking\Events\GA4\View_Account_Event::class,
		Tracking\Events\GA4\View_Order_Event::class,
		Tracking\Events\GA4\Update_Address_Event::class,
		Tracking\Events\GA4\Change_Password_Event::class,
		Tracking\Events\GA4\Estimate_Shipping_Event::class,
		Tracking\Events\GA4\Track_Order_Event::class,
		Tracking\Events\GA4\Cancel_Order_Event::class,
		Tracking\Events\GA4\Refund_Event::class,
		Tracking\Events\GA4\Reorder_Event::class,
		// UA events
		Tracking\Events\Universal_Analytics\Signed_In_Event::class,
		Tracking\Events\Universal_Analytics\Signed_Out_Event::class,
		Tracking\Events\Universal_Analytics\Viewed_Signup_Event::class,
		Tracking\Events\Universal_Analytics\Signed_Up_Event::class,
		Tracking\Events\Universal_Analytics\Viewed_Homepage_Event::class,
		Tracking\Events\Universal_Analytics\Viewed_Product_Event::class,
		Tracking\Events\Universal_Analytics\Clicked_Product_Event::class,
		Tracking\Events\Universal_Analytics\Added_To_Cart_Event::class,
		Tracking\Events\Universal_Analytics\Removed_From_Cart_Event::class,
		Tracking\Events\Universal_Analytics\Changed_Cart_Quantity_Event::class,
		Tracking\Events\Universal_Analytics\Viewed_Cart_Event::class,
		Tracking\Events\Universal_Analytics\Applied_Coupon_Event::class,
		Tracking\Events\Universal_Analytics\Removed_Coupon_Event::class,
		Tracking\Events\Universal_Analytics\Started_Checkout_Event::class,
		Tracking\Events\Universal_Analytics\Provided_Billing_Email_Event::class,
		Tracking\Events\Universal_Analytics\Selected_Payment_Method_Event::class,
		Tracking\Events\Universal_Analytics\Placed_Order_Event::class,
		Tracking\Events\Universal_Analytics\Started_Payment_Event::class,
		Tracking\Events\Universal_Analytics\Completed_Purchase_Event::class,
		Tracking\Events\Universal_Analytics\Wrote_Review_Event::class,
		Tracking\Events\Universal_Analytics\Commented_Event::class,
		Tracking\Events\Universal_Analytics\Viewed_Account_Event::class,
		Tracking\Events\Universal_Analytics\Viewed_Order_Event::class,
		Tracking\Events\Universal_Analytics\Updated_Address_Event::class,
		Tracking\Events\Universal_Analytics\Changed_Password_Event::class,
		Tracking\Events\Universal_Analytics\Estimated_Shipping_Event::class,
		Tracking\Events\Universal_Analytics\Tracked_Order_Event::class,
		Tracking\Events\Universal_Analytics\Cancelled_Order_Event::class,
		Tracking\Events\Universal_Analytics\Order_Refunded_Event::class,
		Tracking\Events\Universal_Analytics\Reordered_Event::class,
		Tracking\Events\Universal_Analytics\Not_Found_Error_Event::class,
	];

	/** @var Event[] registered event instances */
	protected array $events = [];


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->load_events();

		// load & set event names every time integration settings are loaded
		add_action( 'wc_google_analytics_pro_after_settings_loaded', [ $this, 'load_event_names' ] );

		add_action( 'init', [ $this, 'register_event_hooks' ] );

		// track page views (for UA)
		add_action( 'wp_head', [ $this, 'pageview' ] );
	}


	/**
	 * Loads events.
	 *
	 * Instantiates event classes, but does not register any callback hooks. This is because event names are not available
	 * until the Integration class is fully loaded. The Integration class is loaded on `init` hook, but it also requires
	 * this class to be available in order to set up form fields for each event.
	 *
	 * This 3-step process of loading and registering events ensures that we have a single source of truth for events while
	 * also preventing loading dependencies in an endless circle.
	 *
	 * @since 2.0.0
	 *
	 * @see self::register_event_hooks()
	 * @see self::load_event_names()
	 */
	public function load_events(): void {

		foreach ( $this->get_event_classes_to_load() as $event_class ) {

			// sanity check: because the event classes are filterable, make sure we actually have a valid class name
			if ( ! $event_class || ! is_subclass_of( $event_class, Event::class ) ) {
				continue;
			}

			$this->events[ $event_class::ID ] = new $event_class();
		}
	}


	/**
	 * Loads and sets event names.
	 *
	 * This method is a callback to when integration settings are loaded (or reloaded). It ensures that event names are
	 * always up-to-date if settings change between the initial page load and before event hooks are registered.
	 *
	 * @internal
	 *
	 * @see Integration::init_settings()
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings
	 * @return void
	 */
	public function load_event_names( array $settings ): void {

		foreach ( $this->get_events() as $event ) {

			$event->set_name( $settings[ $event::ID . '_event_name' ] ?? '' );
		}
	}


	/**
	 * Registers event hooks.
	 *
	 * This method will register event hook callbacks for each event that is enabled (that is, if the event has a name set).
	 * Event names can only be loaded after the Integration class has loaded, which is why we register event hooks separately
	 * from initializing the event classes.
	 *
	 * @since 2.0.0
	 *
	 * @see self::load_events()
	 * @see self::load_event_names()
	 *
	 * @return void
	 */
	public function register_event_hooks(): void {

		foreach( $this->get_events() as $event ) {

			if ( $event->is_enabled() ) {

				$event->register_hooks();
			}
		}
	}


	/**
	 * Gets a list of event classes to load.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_event_classes_to_load() : array {

		/**
		 * Filters the event classes to load
		 *
		 * @since 2.0.0
		 *
		 * @param Event[] $event_classes a list of event classes to load
		 */
		return apply_filters( 'wc_google_analytics_pro_event_classes_to_load', $this->event_classes );
	}


	/**
	 * Gets all registered event instances.
	 *
	 * @since 2.0.0
	 *
	 * @return Event[]
	 */
	public function get_events() : array {

		return $this->events;
	}


	/**
	 * Gets a registered event instance.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id the event ID
	 * @return Event|null
	 */
	public function get_event(string $id ) : ?Event {

		return $this->events[ $id ] ?? null;
	}


	/**
	 * Sanitizes a custom event string.
	 *
	 * Contains excess checks to account for any kind of user input.
	 *
	 * @since 2.0.0
	 *
	 * @param string $str
	 * @return string|bool the sanitized string or false on failure
	 */
	private function sanitize_event_string( $str = false ) {

		if (isset( $str )) {

			// remove excess spaces
			$str = trim( $str );

			return $str;
		}

		return false;
	}


	/** Event tracking methods ******************************/


	/**
	 * Tracks a pageview.
	 *
	 * @since 1.0.0
	 */
	public function pageview() {

		if (Tracking::do_not_track()) {
			return;
		}

		$frontend = $this->get_plugin()->get_tracking_instance()->get_frontend_handler_instance();

		$frontend->enqueue_js( 'pageview', $frontend->get_ga_function_name() . "( 'send', 'pageview' );" );
	}


	/**
	 * Tracks a custom event.
	 *
	 * Contains excess checks to account for any kind of user input.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_name the event name
	 * @param array $properties Optional. The event properties
	 * @param bool $track_in_admin Optional. Whether the event should be tracked when it occurs in site admin
	 */
	public function custom_event( string $event_name, array $properties = [], bool $track_in_admin = false ): void {

		$event_name = trim( $event_name );

		if ( ! $event_name ) {
			return;
		}

		// Universal Analytics
		if ( Tracking::get_tracking_id() ) {

			// sanitize property names and values
			$prop_array = false;
			$props      = false;

			if ( count( $properties ) > 0 ) {

				foreach ( $properties as $k => $v ) {

					$key   = $this->sanitize_event_string( $k );
					$value = $this->sanitize_event_string( $v );

					if ( $key && $value ) {
						$prop_array[ $key ] = $value;
					}
				}

				if ( $prop_array && is_array( $prop_array ) && count( $prop_array ) > 0 ) {
					$props = $prop_array;
				}
			}

			// if everything checks out then trigger event
			$this->api_record_event( $event_name, $props, [], [], $track_in_admin );
		}

		// GA4
		$event = new Custom_Event();
		$event->track( $event_name, $properties, $track_in_admin );
	}


	/** Tracking methods ***/


	/**
	 * Records an event via the Measurement Protocol API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_name the name of the event to be set
	 * @param string[] $properties the properties to be set with event
	 * @param string[] $ec additional enhanced ecommerce data to be sent with the event
	 * @param string[] $identities (optional) identities to use when tracking the event - if not provided, auto-detects from GA cookie and current user
	 * @param bool $admin_event whether the event is an admin one
	 * @return bool whether event was recorded
	 */
	public function api_record_event( string $event_name, array $properties = [], array $ec = [], ?array $identities = null, bool $admin_event = false ): bool {

		$record  = false;
		$user_id = is_array( $identities ) && isset( $identities['uid'] ) ? $identities['uid'] : null;

		// verify tracking status
		if ( ! Tracking::do_not_track( $admin_event, $user_id ) ) {

			// remove blank properties/ec properties
			unset( $properties[''], $ec[''] );

			// auto-detect identities, if not provided
			if ( ! is_array( $identities ) || empty( $identities ) || empty( $identities['cid'] ) ) {
				$identities = Identity_Helper::get_all();
			}

			// proceed if CID is not null
			if ( ! empty( $identities['cid'] ) ) {

				// remove user ID, unless user ID tracking is enabled,
				if ( isset( $identities['uid'] ) && 'yes' !== $this->get_integration()->get_option( 'track_user_id' )) {
					unset( $identities['uid'] );
				}

				// set IP and user-agent overrides, unless already provided
				if ( empty( $identities['uip'] ) ) {
					$identities['uip'] = $this->get_client_ip();
				}

				if ( empty( $identities['ua'] ) ) {
					$identities['ua'] = wc_get_user_agent();
				}

				// track the event via Measurement Protocol
				$this->get_plugin()->get_api_client_instance()->get_measurement_protocol_for_ua_api()->track_event( $event_name, $identities, $properties, $ec );

				$record = true;
			}
		}

		return $record;
	}


	/**
	 * Returns the visitor's IP.
	 *
	 * @since 2.0.0
	 *
	 * @return string client IP
	 */
	private function get_client_ip(): string {

		return \WC_Geolocation::get_ip_address();
	}


	/** Generic helper methods ******************************/


	/**
	 * Gets the Measurement Protocol API handler for GA4.
	 *
	 * @since 2.0.0
	 *
	 * @return Measurement_Protocol_API
	 */
	public function get_measurement_protocol_api(): Measurement_Protocol_API {

		if ( $this->measurement_protocol_api instanceof Measurement_Protocol_API) {
			return $this->measurement_protocol_api;
		}

		return $this->measurement_protocol_api = new Measurement_Protocol_API(
			Tracking::get_measurement_id(),
			wc_google_analytics_pro()->get_auth_instance()->get_mp_api_secret()
		);
	}


	/**
	 * Gets the Measurement Protocol API handler for Universal Analytics.
	 *
	 * @since 2.0.0
	 *
	 * @deprecated since 2.0.0 will be removed when Universal Analytics is retired
	 *
	 * @return Measurement_Protocol_UA_API
	 */
	public function get_measurement_protocol_ua_api(): Measurement_Protocol_UA_API {

		if ( $this->measurement_protocol_ua_api instanceof Measurement_Protocol_UA_API) {
			return $this->measurement_protocol_ua_api;
		}

		return $this->measurement_protocol_ua_api = new Measurement_Protocol_UA_API( Tracking::get_tracking_id() );
	}


	/**
	 * Gets the integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Integration
	 */
	public function get_integration(): Integration {

		return wc_google_analytics_pro()->get_integration();
	}


	/**
	 * Gets the plugin instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Plugin
	 */
	protected function get_plugin(): Plugin {

		return wc_google_analytics_pro();
	}


}
