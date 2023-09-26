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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Identity_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Deferred_Event;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0\SV_WC_Helper;

defined( 'ABSPATH' ) or exit;

/**
 * The base GA4 Event class.
 *
 * @since 2.0.0
 */
abstract class GA4_Event extends Event {


	/** @var bool whether this is a GA4 recommended event */
	protected bool $recommended_event = false;


	/**
	 * Checks whether this event is a recommended GA4 event.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_recommended_event() : bool {

		return $this->recommended_event;
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field() : array {

		return array_merge( parent::get_form_field(), [ 'custom_attributes' => [
			'recommended_event' => $this->is_recommended_event() ? 'yes' : 'no',
			'default_name'      => $this->get_default_name(),
		]] );
	}


	/**
	 * Records the event via API.
	 *
	 * @since 2.0.0
	 *
	 * @param array $properties event properties
	 * @param array $identities visitor identities
	 * @param array $user_properties user properties
	 * @return bool whether the event was successfully tracked or not
	 */
	protected function record_via_api( array $properties = [], array $identities = [], array $user_properties = [] ): bool {

		$user_id = $identities['uid'] ?? Identity_Helper::get_uid();

		if ( Tracking::do_not_track( $this->is_admin_event(), $user_id ) ) {
			return false;
		}

		$data = [
			'client_id'  => (string) ( $identities['cid'] ?? Identity_Helper::get_cid() ),
			'events'     => [
				/**
				 * Filters the event item to be sent to the API.
				 *
				 * @since 2.0.5
				 * @param array{ name: string, params: array<string, mixed> } $event an associative array of event item data
				 */
				apply_filters( 'wc_google_analytics_pro_api_event_item', [
					'name'   => $this->get_name(),
					'params' => array_merge(
						[
							'page_location' => preg_replace( '/(_wpnonce=)[^&]+/', '$1***', home_url() . ( $_SERVER['REQUEST_URI'] ?? '' ) ),
							'page_referrer' => $_SERVER['HTTP_REFERER'] ?? '',
						],
						/** @link https://www.simoahava.com/analytics/session-attribution-with-ga4-measurement-protocol/ */
						Identity_Helper::get_session_params(),
						Tracking::get_debug_mode_params(),
						// add event properties last, so that they can override any of the above
						$properties
					),
				] ),
			]
		];

		// only add user properties if not empty to avoid invalid JSON
		if ( ! empty( $user_properties ) ) {
			$data = SV_WC_Helper::array_insert_after( $data, 'client_id', ['user_properties' => $user_properties ] );
		}

		if ( $user_id ) {
			if ( Tracking::is_user_id_tracking_enabled() ) {
				$data = SV_WC_Helper::array_insert_after( $data, 'client_id', ['user_id' => (string) $user_id] );
			}

			$data['user_properties']['role']['value'] = implode( ', ', get_userdata( $user_id )->roles );
		}

		return wc_google_analytics_pro()->get_api_client_instance()->get_measurement_protocol_api()->collect( $data );
	}


	/**
	 * Records the event via JS.
	 *
	 * Frontend does not require identities, as these are provided by the JS tracking code (gtag setup).
	 *
	 * @since 2.0.0
	 *
	 * @param array $properties event properties
	 *
	 * @return bool
	 */
	protected function record_via_js( array $properties = [] ): bool {

		if ( Tracking::do_not_track() ) {
			return false;
		}

		$event_name   = esc_js( $this->get_name() );

		// We need to double-escape the double quotes in the JSON string - first by `json_encode` and then by `wp_slash`.
		// This is because the escape characters are processed twice - first by string interpolation and then by JSON.parse.
		// Escaping only once would result in the escape characters being removed by string interpolation, resulting in
		// an invalid JSON string.
		$event_params = ! empty( $properties ) ? wp_slash( json_encode( $properties ) ) : '{}';

		// Using a template literal and then parsing the result as JSON allows us to use JS variables inside event parameters.
		// This is helpful in cases where some parameter value cannot be determined in backend and must be provided by the frontend.
		$track_event = <<<JS
		gtag('event', '{$event_name}', JSON.parse(`{$event_params}`))
		JS;

		if ( $this instanceof Deferred_Event && $trigger = $this->get_trigger_js() ) {
			$track_event = str_replace( '"__INSERT_TRACKING_CALL_HERE__"', $track_event, $trigger );
		}

		wc_google_analytics_pro()->get_tracking_instance()->get_frontend_handler_instance()->enqueue_tracking_call( $track_event );

		return true;
	}


}
