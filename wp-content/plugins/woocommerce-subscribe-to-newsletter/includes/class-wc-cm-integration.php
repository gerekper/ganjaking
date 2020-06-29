<?php

/**
 * WC_Cm_Integration class.
 * Campaign Monitor
 */
class WC_CM_Integration {

	/**
	 * Provider's id
	 *
	 * @var string
	 */
	private $id = 'cmonitor';

	private $api_key;
	private $list;

	/**
	 * Constructor
	 */
	public function __construct( $api_key, $list = false ) {
		$this->api_key = $api_key;
		$this->list    = $list;
	}

	/**
	 * Return provider's id
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * has_list function.
	 *
	 * @access public
	 * @return void
	 */
	public function has_list() {
		if ( $this->list ) {
			return true;
		}
	}

	/**
	 * has_api_key function.
	 *
	 * @access public
	 * @return void
	 */
	public function has_api_key() {
		if ( $this->api_key ) {
			return true;
		}
	}

	/**
	 * get_lists function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_lists() {
		$cmonitor_lists = get_transient( 'wc_cm_list_' . md5( $this->api_key ) );

		if ( ! $cmonitor_lists ) {
			include_once 'api/campaignmonitor/csrest_general.php';
			include_once 'api/campaignmonitor/csrest_clients.php';

			$cmonitor_lists = array();

			// Get clients.
			$wrap   = new CS_REST_General( $this->api_key );
			$result = $wrap->get_clients();

			if ( $result->was_successful() ) {
				if ( is_array( $result->response ) ) {
					foreach ( $result->response as $client ) {

						$cmonitor = new CS_REST_Clients( $client->ClientID, $this->api_key);
						$list_result = $cmonitor->get_lists();
						if ( $list_result->was_successful() ) {
						    if ( is_array( $list_result->response ) ) {
						    	foreach ( $list_result->response as $list )
						    		$cmonitor_lists[ $list->ListID ] = $list->Name . ' (' . $client->Name . ')';
						    }
						}
					}

					if ( count( $cmonitor_lists ) ) {
						set_transient( 'wc_cm_list_' . md5( $this->api_key ), $cmonitor_lists, HOUR_IN_SECONDS );
					}
				}
			} else {
				echo '<div class="error"><p>' . esc_html__( 'Unable to load data from Campaign Monitor - check your API key.', 'woocommerce-subscribe-to-newsletter' ) . '</p></div>';
			}
		}

		return $cmonitor_lists;
	}

	/**
	 * show_stats function.
	 *
	 * @access public
	 * @return void
	 */
	public function show_stats() {
		$stats = get_transient( 'woocommerce_cmonitor_stats' );

		if ( ! $stats ) {
			include_once 'api/campaignmonitor/csrest_lists.php';

			$api = new CS_REST_Lists( $this->list, $this->api_key );

			$result = $api->get_stats();

			if ( $result->was_successful() ) {

				$stats  = '<ul class="woocommerce_stats" style="word-wrap:break-word;">';
				$stats .= '<li><strong style="font-size:3em;">' . esc_html( $result->response->TotalActiveSubscribers ) . '</strong> ' . esc_html__( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
				$stats .= '<li><strong style="font-size:3em;">' . esc_html( $result->response->NewActiveSubscribersToday ) . '</strong> ' . esc_html__( 'Subscribers today', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
				$stats .= '<li><strong style="font-size:3em;">' . esc_html( $result->response->NewActiveSubscribersThisMonth ) . '</strong> ' . esc_html__( 'Subscribers this month', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
				$stats .= '<li><strong style="font-size:3em;">' . esc_html( $result->response->UnsubscribesThisMonth ) . '</strong> ' . esc_html__( 'Unsubscribes this month', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
				$stats .= '</ul>';

				set_transient( 'woocommerce_cmonitor_stats', $stats, HOUR_IN_SECONDS );

			} else {
				echo '<div class="error inline"><p>' . esc_html__('Unable to load stats from Campaign Monitor', 'woocommerce-subscribe-to-newsletter' ) . '</p></div>';
			}
		}

		echo $stats;
	}

	/**
	 * subscribe function.
	 *
	 * @access public
	 * @param mixed $first_name
	 * @param mixed $last_name
	 * @param mixed $email
	 * @param string $listid (default: 'false')
	 * @return void
	 */
	public function subscribe( $first_name, $last_name, $email, $listid = 'false' ) {

		if ( ! $email )
			return; // Email is required

		if ( $listid == 'false' )
			$listid = $this->list;

		include_once 'api/campaignmonitor/csrest_subscribers.php';

		$api = new CS_REST_Subscribers( $listid, $this->api_key );

		$name = '';

		if ( $first_name && $last_name )
			$name = $first_name . ' ' . $last_name;

		// Only cURL transport throws Exception, other varies (could be fatal error or die).
		// :sadpanda:. We need to use WP HTTP API in the future :).
		$succeed = true;
		$result  = null;
		try {
			$result = $api->add( array(
				'EmailAddress' 	 => $email,
				'Name'		 => $name,
				'ConsentToTrack' => 'yes',
				'Resubscribe' 	 => true
			) );
		} catch ( Exception $e ) {
			$succeed = false;
		}

		if ( ! $succeed || ( is_callable( array( $result, 'was_successful' ) ) && ! $result->was_successful() ) ) {
			$message = __( 'Unexpected response from Campaign Monitor API.', 'woocommerce-subscribe-to-newsletter' );
			if ( is_a( $result, 'CS_REST_Wrapper_Result ' ) ) {
				$message .= ' ' . sprintf( __( 'HTTP status code %s with following response: %s.', 'woocommerce-subscribe-to-newsletter' ), esc_html( $result->http_status_code, print_r( $result->response, true ) ) );
			}

			// Email admin.
			wp_mail( get_option( 'admin_email' ), esc_html__( 'Email subscription failed (Campaign Monitor)', 'woocommerce-subscribe-to-newsletter' ), $message );
		} else {
			do_action( 'wc_subscribed_to_newsletter', $email );
		}
	}

}
