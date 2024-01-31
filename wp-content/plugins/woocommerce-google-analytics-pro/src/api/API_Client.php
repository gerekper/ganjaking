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
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API;

use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Universal_Analytics\Management_API;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Universal_Analytics\Measurement_Protocol_API as Measurement_Protocol_UA_API;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Lightweight Client for Google APIs.
 *
 * @since 2.0.0
 */
class API_Client
{

	/** @var Auth|null the auth handler class instance */
	protected ?Auth $auth = null;

	/** @var Admin_API|null Admin API handler */
	private ?Admin_API $admin_api = null;

	/** @var Management_API|null Management API handler */
	private ?Management_API $management_api = null;

	/** @var Measurement_Protocol_API|null Measurement Protocol API handler for GA4 */
	private ?Measurement_Protocol_API $measurement_protocol_api = null;

	/** @var Measurement_Protocol_UA_API|null Measurement Protocol API handler for Universal Analytics */
	private ?Measurement_Protocol_UA_API $measurement_protocol_ua_api = null;


	/**
	 * Class constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->auth = new Auth();
	}


	/**
	 * Gets the API auth handler instance
	 *
	 * @since 2.0.0
	 *
	 * @return Auth
	 */
	public function get_auth_instance() : Auth {

		return $this->auth;
	}


	/**
	 * Gets the Admin API handler.
	 *
	 * @since 2.0.0
	 *
	 * @return Admin_API
	 */
	public function get_admin_api(): Admin_API {

		if ( $this->admin_api instanceof Admin_API ) {
			return $this->admin_api;
		}

		return $this->admin_api = new Admin_API( $this->get_auth_instance()->get_fresh_access_token() );
	}


	/**
	 * Gets the Management API handler.
	 *
	 * @since 2.0.0
	 *
	 * @deprecated since 2.0.0 will be removed when Universal Analytics is retired
	 *
	 * @return Management_API
	 */
	public function get_management_api(): Management_API {

		if ( $this->management_api instanceof Management_API ) {
			return $this->management_api;
		}

		return $this->management_api = new Management_API( $this->get_auth_instance()->get_fresh_access_token() );
	}


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
			wc_google_analytics_pro()->get_api_client_instance()->get_auth_instance()->get_mp_api_secret()
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
	public function get_measurement_protocol_for_ua_api(): Measurement_Protocol_UA_API {

		if ( $this->measurement_protocol_ua_api instanceof Measurement_Protocol_UA_API) {
			return $this->measurement_protocol_ua_api;
		}

		return $this->measurement_protocol_ua_api = new Measurement_Protocol_UA_API(Tracking::get_tracking_id());
	}


}
