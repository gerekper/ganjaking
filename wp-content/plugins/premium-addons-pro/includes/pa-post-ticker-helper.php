<?php
/**
 * Pa_Post_Ticker_Helper.
 */

namespace PremiumAddonsPro\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Pa_Post_Ticker_Helper Class.
 */
class Pa_Post_Ticker_Helper {

	/**
	 * Api Settings.
	 *
	 * @var settings
	 */
	private static $settings = null;

	/**
	 * Alpha vintage Api URL.
	 * Used to fetch stock and currencies data.
	 *
	 * @see https://www.alphavantage.co
	 *
	 * @var alpha_vintage_api
	 */
	private static $alpha_vintage_api = 'https://www.alphavantage.co/query?function=';

	/**
	 * Class Constructor.
	 *
	 * @param array $settings  API settings.
	 */
	public function __construct( $settings = array() ) {
		self::$settings = $settings;
	}

	/**
	 * Get Stock Data.
	 *
	 * @access public
	 * @since 2.8.22
	 *
	 * @return array
	 */
	public static function get_req_data() {

		$settings        = self::$settings;
		$api_key         = $settings['api_key'];
		$function        = $settings['function'];
		$function_index  = 'GLOBAL_QUOTE' === $function ? 'Global Quote' : 'Realtime Currency Exchange Rate';
		$symbols         = $settings['symbols'];
		$to_currencies   = $settings['to_currency'];
		$res_arr         = array();
		$add_curr_change = isset( $settings['old_data_key'] ) ? $settings['old_data_key'] : false;

		foreach ( $symbols as $index => $symbol ) {

			$req_url = 'https://www.alphavantage.co/query?function=' . $function . '&apikey=' . $api_key . '&datatype=json';

			if ( 'GLOBAL_QUOTE' === $function ) {
				$req_url .= '&symbol=' . strtoupper( $symbol );

			} else {
				// check if the it's a physical to crypto exchange.
				$to_curr     = $to_currencies[ $index ];
				$is_reversed = '/' !== $symbol[0] && '/' === $to_curr[0] ? true : false;
				$symbol      = str_replace( '/', '', $symbol );
				$to_curr     = str_replace( '/', '', $to_curr );

				if ( $is_reversed ) {
					$req_url .= '&from_currency=' . strtoupper( $to_curr ) . '&to_currency=' . strtoupper( $symbol );

				} else {
					$req_url .= '&from_currency=' . strtoupper( $symbol ) . '&to_currency=' . strtoupper( $to_curr );
				}
			}

			$response = wp_remote_get(
				$req_url,
				array(
					'timeout'   => 60,
					'sslverify' => false,
				)
			);

			if ( is_wp_error( $response ) || empty( $response ) ) {

				$notice = sprintf( 'Something went wrong: %s', $response->get_error_message() );
				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return;
			}

			$response = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $response[ $function_index ] ) && ! empty( $response[ $function_index ] ) ) {

				$symbol_data = $response[ $function_index ];

				if ( 'GLOBAL_QUOTE' === $function ) {
					$data = array(
						'symbol'         => $symbol,
						'price'          => $symbol_data['05. price'],
						'change'         => $symbol_data['09. change'],
						'percent_change' => $symbol_data['10. change percent'],
					);
				} else {

					$exchange_rate = $is_reversed ? 1 / $symbol_data['5. Exchange Rate'] : $symbol_data['5. Exchange Rate'];
					$symbol_name   = $symbol . '/' . $to_currencies[ $index ];

					$data = array(
						'symbol' => $symbol_name,
						'price'  => $exchange_rate,
						'name'   => $symbol_data['2. From_Currency Name'] . ' to ' . $symbol_data['4. To_Currency Name'],
					);

					if ( false !== $add_curr_change ) {
						$old_price = get_option( $add_curr_change, false );

						$change                 = ! $old_price ? 0 : floatval( $symbol_data['price'] ) - $old_price[ $symbol_name ];
						$data['change']         = $change;
						$data['percent_change'] = ! $old_price ? 0 : ( ( $change / floatval( $symbol_data['price'] ) ) * 100 ) . '%';
					}
				}

				array_push( $res_arr, $data );
			} else {

				$error  = isset( $response['Note'] ) ? $response['Note'] : 'please make sure your query data are valid.';
				$notice = sprintf( __( 'Something went wrong, %s', 'premium-addons-for-elementor' ), $error );
				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return false;
			}
		}

		return empty( $res_arr ) ? false : $res_arr;
	}

	/**
	 * Get Gold Data.
	 *
	 * @access public
	 * @since 2.8.22
	 *
	 * @return array
	 */
	public static function get_gold_data( $api_data, $alter ) {

		$api_key       = $api_data['api_key'];
		$to_currencies = $api_data['to_currency'];
		$res_arr       = array();

		if ( false !== strpos( $api_key, 'goldapi' ) ) {

			$to_currencies = explode( ',', $to_currencies );

			foreach ( $to_currencies as $index => $currency ) {

				$currency = strtoupper( $currency );

				$req_url = 'https://www.goldapi.io/api/XAU/' . $currency;

				$response = wp_remote_get(
					$req_url,
					array(
						'headers' => array(
							'x-access-token' => $api_key,
							'Content-Type'   => 'application/json',
						),
					)
				);

				$response = self::handle_response( $response, $alter );

				if ( isset( $response['is_error_msg'] ) ) {
					return $response;
				}

				array_push(
					$res_arr,
					array(
						'symbol'         => 'XAU/' . $currency,
						'price'          => isset( $response['price'] ) ? $response['price'] : false,
						'change'         => isset( $response['ch'] ) ? $response['ch'] : false,
						'percent_change' => isset( $response['chp'] ) ? $response['chp'] : false,
					)
				);
			}
		} else {

			$req_url = sprintf( 'https://api.metalpriceapi.com/v1/latest?api_key=%s&base=XAU&currencies=%s', $api_key, $to_currencies );

			$response = wp_remote_get(
				$req_url,
				array(
					'timeout'   => 60,
					'sslverify' => false,
				)
			);

			$response = self::handle_response( $response, $alter );

			if ( isset( $response['is_error_msg'] ) ) {
				return;
			}

			$to_currencies = $response['rates'];

			foreach ( $to_currencies as $currency => $price ) {

				$currency = strtoupper( $currency );

				array_push(
					$res_arr,
					array(
						'symbol'         => 'XAU/' . $currency,
						'price'          => $price,
						'change'         => isset( $response['ch'] ) ? $response['ch'] : false,
						'percent_change' => isset( $response['chp'] ) ? $response['chp'] : false,
					)
				);
			}
		}

		return empty( $res_arr ) ? false : $res_arr;
	}

	/**
	 * Handle Response
	 *
	 * @since 2.9.2
	 * @access public
	 *
	 * @return object $response response object.
	 */
	public static function handle_response( $response, $alter ) {

		if ( is_wp_error( $response ) || empty( $response ) ) {

			if ( $alter ) {
				$notice = sprintf( 'Something went wrong: %s', $response->get_error_message() );
				?>
				<div class="premium-error-notice">
					<?php echo wp_kses_post( $notice ); ?>
				</div>
				<?php
			}

			return array(
				'is_error_msg' => true,
				'error_msg'    => $notice,
			);
		}

		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $response['error'] ) ) {

			$error_msg = is_array( $response['error'] ) ? $response['error']['message'] : $response['error'];

			if ( $alter ) {

				$notice = sprintf( 'Something went wrong: %s', $error_msg );
				?>
				<div class="premium-error-notice">
					<?php echo wp_kses_post( $notice ); ?>
				</div>
				<?php
			}

			return array(
				'is_error_msg' => true,
				'error_msg'    => $error_msg,
			);
		}

		return $response;

	}

	/**
	 * Delet Existing Transient.
	 *
	 * @access public
	 * @since 2.8.23
	 */
	public static function delete_existing_transient() {
		global $wpdb;

		$id      = self::$settings['id'];
		$api_key = self::$settings['api_key'];

		$query = $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE '%papro_" . $id . '_' . $api_key . "%'" );

		$wpdb->query( $query );
	}
}


