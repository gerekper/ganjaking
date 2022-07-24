<?php
/**
 * Handle API requests.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SP_API' ) ) {

	/**
	 * API request helper.
	 */
	class SP_API {

		/**
		 * Uri to make AWS requests
		 *
		 * @const SP_API_REQUEST_ENDPOINT
		 */
		const SP_API_REQUEST_ENDPOINT = 'https://mcf.atouchpoint.com/api/do-request';

		/**
		 * Singleton instance of NS_FBA
		 *
		 * @var NS_FBA $ns_fba
		 */
		private $ns_fba;

		/**
		 * Contain the Seller Partner host
		 *
		 * @var $host
		 */
		private $host;

		/**
		 * Contain the Seller Partner refresh token
		 *
		 * @var $token
		 */
		private $token;

		/**
		 * Contain the plugin version
		 *
		 * @var $version
		 */
		private $version;

		/**
		 * Contain the Customer MCF Status
		 *
		 * @var $customer_status
		 */
		private $customer_status;

		/**
		 * Contain the Merchant Id
		 *
		 * @var $merchant_id
		 */
		private $merchant_id;

		/**
		 * Constructor.
		 *
		 * @param   array $options  The plugin integration options.
		 */
		public function __construct( array $options ) {
			$this->ns_fba = NS_FBA::get_instance();

			$this->host            = $options['api_host'];
			$this->token           = $options['token'];
			$this->version         = $options['version'];
			$this->customer_status = $options['customer_mcf_status'];
			$this->merchant_id     = $options['merchant_id'];
		}

		/**
		 * Used to make all requests to FBASIG/AMZ endpoints
		 *
		 * @param   string $path               URL to be called.
		 * @param   string $type               Amazon request type / end-point / function.
		 * @param   int    $qty                Total fulfillment Qty (if applicable).
		 * @param   string $method             Request method.
		 * @param   string $json_encoded_body  The json body.
		 *
		 * @return WP_Error|array can be WP_Error or array
		 */
		public function make_request(
			string $path = '',
			string $type = 'Unspecified',
			int $qty = 0,
			string $method = 'GET',
			string $json_encoded_body = ''
		) {

			// TODO: Provide a better single validation point for all input through the API to check for and prevent issues.
			// This is UGLY, but for now the quickest way to fix + symbols in SKUs AAARRRRGH!
			// Solution in progress - ticket open with Amazon. Suspect issue on their end.
			// phpcs:ignore
			$path = str_ireplace( '+', '%2b', $path );

			// This is needed for the same fix in createFulfillmentOrder since it POSTs with a json body.
			$json_encoded_body = str_ireplace( '+', '%2b', $json_encoded_body );

			$url = $this->host . $path;

			/** TODO: JUST FOR TESTING.
			if ( $this->ns_fba->is_debug ) {
				error_log( "\nSP-API make_request var url passed in: \n" . print_r( $url, true ) );              // phpcs:ignore
			}
			*/

			$args = array(
				'refreshToken'   => $this->token,
				'requestUrl'     => rawurlencode( $url ),
				'requestMethod'  => $method,
				'requestType'    => $type,
				'requestQty'     => $qty,
				'requestBody'    => $json_encoded_body,
				'clientVersion'  => $this->version,
				'merchantId'     => $this->merchant_id,
				'customerStatus' => $this->customer_status,
				'hostName'       => rawurlencode( get_site_url() ),
			);

			$url = add_query_arg( $args, self::SP_API_REQUEST_ENDPOINT );

			// This is UGLY, but for now the quickest way to test + symbols in SKUs AAARRRRGH!
			// Solution in progress - ticket open with Amazon. Suspect issue on their end.
			// phpcs:ignore
			//$url = str_ireplace( '%2B', '+', $url );

			// Grabbing the response here gives us a single logging point on all request / response pairs.
			$response = wp_remote_post( $url ); // phpcs:ignore

			/** TODO: JUST FOR TESTING.
			if ( $this->ns_fba->is_debug ) {
				error_log( "\nSP-API make_request var args: \n" . print_r( $args, true ) );            // phpcs:ignore
				error_log( "\nSP-API make_request var url: \n" . print_r( $url, true ) );              // phpcs:ignore
				error_log( "\nSP-API make_request var response:  \n" . print_r( $response, true ) );   // phpcs:ignore
			}
			*/
			return $response;
		}

		/**
		 * Analyse response to detect and log any errors.
		 *
		 * @param   WP_Error|array $response  The http response.
		 *
		 * @return bool
		 */
		public static function is_error_in( $response ): bool {
			$json = json_decode( $response['body'], true );
			// TODO: Remove false success condition after verifying it is no longer applicable.
			// TODO: Consolidate all possible conditions here throughout codebase into 1 single consistent bool.
			// TODO: This will require scrubbing the MCF ns-fba-sig code as well.
			if ( is_wp_error( $response ) || ! is_array( $response ) ||
				! empty( $json['error'] ) || ! empty( $json['errors'] ) || ( isset( $json['success'] ) && false === $json['success'] ) ) {
				/** Keep for logging use later
				if ( $this->ns_fba->is_debug ) {
					// phpcs:ignore
					error_log( "SP-API is_error_in Response (true):\n" . print_r( $response, true ) );
				}
				*/
				return true;
			}
			return false;
		}

		/**
		 * Used to test sandbox.
		 *
		 * @param   string $path               URL to be called.
		 * @param   string $method             Request method.
		 * @param   string $json_encoded_body  The json body.
		 *
		 * @return array|WP_Error
		 */
		public function make_sandbox_requests( $path = '', $method = 'GET', $json_encoded_body = '' ) {

			// TODO: Update this function to match current production code once Sandbox is viable for testing.
			$url = 'https://sandbox.sellingpartnerapi-na.amazon.com' . $path;

			$args = array(
				'refreshToken'   => $this->token,
				'requestUrl'     => rawurlencode( $url ),
				'requestMethod'  => $method,
				'clientVersion'  => $this->version,
				'merchantId'     => $this->merchant_id,
				'requestBody'    => $json_encoded_body,
				'customerStatus' => $this->customer_status,
			);

			$url = add_query_arg( $args, self::SP_API_REQUEST_ENDPOINT );

			return wp_remote_post( $url );
		}

	} // End Class.
}
