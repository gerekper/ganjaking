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
 * Do not edit or add to this file if you wish to upgrade WooCommerce PayPal Express to newer
 * versions in the future. If you wish to customize WooCommerce PayPal Express for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-PayPal Express/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API\Measurement_Protocol_API;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The Measurement Protocol for GA4 request class.
 *
 * @link https://developers.google.com/analytics/devguides/collection/protocol/ga4/reference?client_type=gtag
 *
 * @since 2.0.0
 */
class Request extends Framework\SV_WC_API_JSON_Request
{

	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 *
	 * @param string $measurement_id the Google Analytics tracking ID
	 * @param string $api_secret Measurement Protocol API secret
	 * @param array $data the request payload
	 */
	public function __construct( string $measurement_id, string $api_secret, array $data = [] ) {

		$this->params = [
			'measurement_id' => $measurement_id,
			'api_secret' => $api_secret,
		];

		$this->data = $data;
	}


	/**
	 * Sets request data.
	 *
	 * @since 2.0.0
	 *
	 * @param array $data
	 * @return $this
	 */
	public function set_data( array $data )
	{
		$this->data = $data;

		return $this;
	}


}
