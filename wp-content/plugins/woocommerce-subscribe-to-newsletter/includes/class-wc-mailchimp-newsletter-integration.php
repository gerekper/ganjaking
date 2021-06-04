<?php
/**
 * WC_Mailchimp_Integration class.
 *
 * http://apidocs.mailchimp.com/api/rtfm/campaignecommorderadd.func.php#campaignecommorderadd-v13
 */
if ( ! class_exists( 'WC_Mailchimp_Newsletter_Integration' ) ) {
	/**
	 * Class WC_Mailchimp_Newsletter_Integration
	 *
	 * @deprecated 3.0.0
	 */
	class WC_Mailchimp_Newsletter_Integration {

		/**
		 * Provider's id
		 *
		 * @var string
		 */
		private $id = 'mailchimp';

		private $api_key;
		private $api_endpoint = 'https://<dc>.api.mailchimp.com/3.0';
		private $list;

		/**
		 * Constructor
		 */
		public function __construct( $api_key, $list = false ) {
			wc_deprecated_function( 'WC_Mailchimp_Newsletter_Integration', '3.0.0', 'WC_Newsletter_Subscription_Provider_Mailchimp' );

			$this->api_key = $api_key;
			$this->list    = $list;
			$datacentre    = '';

			if ( $this->api_key ) {
				if ( strstr( $this->api_key, '-' ) ) {
					list( , $datacentre ) = explode( '-', $this->api_key );
				}
				if ( ! $datacentre ) {
					$datacentre = 'us2';
				}
				$this->api_endpoint   = str_replace( '<dc>', $datacentre, $this->api_endpoint );
			}
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
		 * Performs the underlying HTTP request. Not very exciting
		 *
		 * @since 2.7.0 Added parameter $http_method
		 * @since 2.9.1 Added parameter $query_params
		 *
		 * @param  string $method The API method to be called.
		 * @param  string $http_method Accepts 'GET', 'POST', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', or 'PATCH'.
		 * @param  array  $args   Assoc array of parameters to be passed.
		 * @param  array  $query_params Assoc array of query params to be added to the URL.
		 * @return array          Assoc array of decoded result.
		 */
		private function api_request( $method, $http_method = 'GET', $args = array(), $query_params = array() ) {

			if ( ! empty( $args ) ) {
				$args = wp_json_encode( $args );
			}

			$url = $this->api_endpoint . '/' . $method;
			if ( ! empty( $query_params ) ) {
				$url .= '?' . http_build_query( $query_params );
			}

			$result = wp_remote_request(
				$url,
				apply_filters(
					'woocommerce_newsletter_mailchimp_api_request',
					array(
						'method'      => $http_method,
						'body'        => $args,
						'sslverify'   => false,
						'timeout'     => 60,
						'httpversion' => '1.1',
						'headers'     => array(
							'Authorization' => 'Basic ' . base64_encode( 'apikey:' . $this->api_key ),
							'Content-Type'  => 'application/json',
						),
					),
					$method
				)
			);

			return ! is_wp_error( $result ) && isset( $result['body'] ) ? json_decode( $result['body'] ) : false;
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
		 * @return array
		 */
		public function get_lists() {
			$mailchimp_lists = get_transient( 'wc_mc_list_' . md5( $this->api_key ) );

			if ( ! $mailchimp_lists ) {

				$lists = $this->api_request( 'lists', 'GET', array(), array( 'count' => 1000 ) );

				if ( $lists ) {

					if ( isset( $lists->status ) && isset( $lists->instance ) ) {
						/* translators: 1: list code 2: list error */
						echo '<div class="error"><p>' . sprintf( esc_html__( 'Unable to load lists() from MailChimp: (%1$s) %2$s', 'woocommerce-subscribe-to-newsletter' ), $lists->status, $lists->detail ) . '</p></div>';

						return array();

					} else {
						foreach ( $lists->lists as $list ) {
							$mailchimp_lists[ $list->id ] = $list->name;
						}

						if ( sizeof( $mailchimp_lists ) > 0 ) {
							set_transient( 'wc_mc_list_' . md5( $this->api_key ), $mailchimp_lists, 60 * 60 * 1 );
						}
					}
				} else {
					$mailchimp_lists = array();
				}
			}

			return $mailchimp_lists;
		}

		/**
		 * Show_stats function.
		 *
		 * @return void
		 */
		public function show_stats() {
			$stats = get_transient( 'woocommerce_mailchimp_stats' );

			if ( ! $stats ) {

				$list = $this->api_request( 'lists/' . $this->list, 'GET' );

				if ( isset( $list->status ) && isset( $list->instance ) ) {

					echo '<div class="error inline"><p>' . esc_html__( 'Unable to load stats from MailChimp', 'woocommerce-subscribe-to-newsletter' ) . '</p></div>';

				} else {

					$stats  = '<ul class="woocommerce_stats" style="word-wrap:break-word;">';
					$stats .= '<li><strong style="font-size:3em;">' . esc_html( $list->stats->member_count ) . '</strong> ' . esc_html__( 'Total subscribers', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
					$stats .= '<li><strong style="font-size:3em;">' . esc_html( $list->stats->unsubscribe_count ) . '</strong> ' . esc_html__( 'Unsubscribes', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
					$stats .= '<li><strong style="font-size:3em;">' . esc_html( $list->stats->member_count_since_send ) . '</strong> ' . esc_html__( 'Subscribers since last newsletter', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
					$stats .= '<li><strong style="font-size:3em;">' . esc_html( $list->stats->unsubscribe_count_since_send ) . '</strong> ' . esc_html__( 'Unsubscribes since last newsletter', 'woocommerce-subscribe-to-newsletter' ) . '</li>';
					$stats .= '</ul>';

					set_transient( 'woocommerce_mailchimp_stats', $stats, 60 * 60 * 1 );
				}
			}

			echo $stats;
		}

		/**
		 * Subscribe function.
		 *
		 * @param mixed  $first_name firstname.
		 * @param mixed  $last_name lastname.
		 * @param mixed  $email email.
		 * @param string $listid  listid (default: 'false').
		 * @return void
		 */
		public function subscribe( $first_name, $last_name, $email, $listid = 'false' ) {
			if ( ! $email ) {
				return; // Email is required.
			}

			if ( 'false' == $listid ) {
				$listid = $this->list;
			}

			// Get email's md5 identifier.
			$subscriber_hash = md5( strtolower( $email ) );

			$result = $this->api_request(
				'lists/' . $listid . '/members/' . $subscriber_hash,
				'PUT',
				array(
					'email_address' => $email,
					'merge_fields'  => apply_filters(
						'wc_mailchimp_subscribe_vars',
						array(
							'FNAME' => $first_name,
							'LNAME' => $last_name,
						)
					),
					'status'        => ( get_option( 'woocommerce_mailchimp_double_opt_in' ) === 'yes' ) ? 'pending' : 'subscribed',
				)
			);

			if ( isset( $result->errors ) && ! empty( $result->status ) ) {
				// Email admin.
				wp_mail( get_option( 'admin_email' ), esc_html__( 'Email subscription failed (Mailchimp)', 'woocommerce-subscribe-to-newsletter' ), '(' . esc_html( $result->status ) . ') ' . print_r( $result->errors, true ) );
			} else {
				do_action( 'wc_subscribed_to_newsletter', $email );
			}
		}
	}
}
