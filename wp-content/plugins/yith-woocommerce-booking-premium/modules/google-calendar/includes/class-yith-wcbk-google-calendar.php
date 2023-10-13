<?php
/**
 * Google Calendar Class.
 * Handle Google Calendar connection and panel.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Calendar' ) ) {
	/**
	 * Class YITH_WCBK_Google_Calendar
	 *
	 * @author YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Google_Calendar {
		/**
		 * The panel page url.
		 *
		 * @var string
		 */
		private $panel_url = '';

		/**
		 * The oAuth redirect URI.
		 *
		 * @var string
		 */
		private $redirect_uri = '';

		/**
		 * API Scopes.
		 *
		 * @var string
		 */
		private $scopes = 'https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/userinfo.profile';

		/**
		 * OAuth URL.
		 *
		 * @var string
		 */
		private $oauth_url = 'https://accounts.google.com/o/oauth2/';

		/**
		 * The options.
		 *
		 * @var array
		 */
		private $options = array();

		/**
		 * Single instance of the class.
		 *
		 * @var YITH_WCBK_Google_Calendar
		 */
		private static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Google_Calendar
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Google_Calendar constructor.
		 */
		private function __construct() {
			$this->panel_url = add_query_arg(
				array(
					'page'    => 'yith_wcbk_panel',
					'tab'     => 'settings',
					'sub_tab' => 'settings-calendars',
				),
				admin_url( 'admin.php' )
			);

			$this->redirect_uri = add_query_arg( array( 'yith-wcbk-google-auth' => '1' ), $this->panel_url );

			add_action( 'admin_init', array( $this, 'handle_actions' ) );
		}

		/**
		 * Display the Views for options and access
		 */
		public function display() {
			$actions = array();
			$html    = '';
			if ( ! $this->get_client_id() || ! $this->get_client_secret() ) {
				$html .= $this->get_client_secret_form_view();
			} else {
				$this->oauth_access();

				if ( $this->is_connected() ) {
					$html .= $this->get_profile_details_view();

					$html .= $this->get_timezone_info_view();

					$html .= $this->get_options_form_view();

					$actions[] = 'logout';
				} else {
					$html .= $this->get_access_form_view();

					$actions[] = 'delete-secret';
				}
			}

			if ( ! ! $actions ) {
				$args = array(
					'actions'         => $actions,
					'google_calendar' => $this,
				);

				$html .= $this->get_view_html( 'actions.php', $args );
			}

			echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Handle the actions of Google Calendar panel
		 */
		public function handle_actions() {
			if (
				isset( $_REQUEST['yith-wcbk-gcal-action'], $_REQUEST['yith-wcbk-gcal-nonce'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-gcal-nonce'] ) ), 'yith-wcbk-gcal-action' )
			) {
				$action          = sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-gcal-action'] ) );
				$redirect        = false;
				$default_options = array(
					'booking-events-to-synchronize' => array(),
					'debug'                         => 'no',
					'add-note-on-sync'              => 'no',
				);
				switch ( $action ) {
					case 'save-options':
						$options = ! empty( $_REQUEST['yith-wcbk-gcal-options'] ) ? wp_unslash( $_REQUEST['yith-wcbk-gcal-options'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$options = wp_parse_args( $options, $default_options );

						foreach ( $options as $option => $value ) {
							$value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
							$this->set_option( $option, $value );
						}
						break;
					case 'delete-client-secret':
						$this->set_option( 'client-id', '' );
						$this->set_option( 'client-secret', '' );
						$redirect = true;
						break;
					case 'logout':
						$this->set_option( 'access-token', '' );
						$this->set_option( 'refresh-token', '' );
						delete_transient( 'yith-wcbk-gcal-access-token' );
						$redirect = true;
						break;
				}
				if ( $redirect ) {
					wp_safe_redirect( $this->panel_url );
				}
			}
		}

		/**
		 * OAuth Access on Google response.
		 */
		protected function oauth_access() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_GET['yith-wcbk-google-auth'] ) && ! empty( $_GET['code'] ) ) {
				$code = sanitize_text_field( wp_unslash( $_GET['code'] ) );
				$data = array(
					'code'          => $code,
					'client_id'     => $this->get_client_id(),
					'client_secret' => $this->get_client_secret(),
					'redirect_uri'  => $this->redirect_uri,
					'grant_type'    => 'authorization_code',
				);

				$params = array(
					'body'      => http_build_query( $data ),
					'sslverify' => false,
					'timeout'   => 60,
					'headers'   => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
				);

				$response = wp_remote_post( $this->oauth_url . 'token', $params );

				if ( 200 === absint( wp_remote_retrieve_response_code( $response ) ) && 'OK' === wp_remote_retrieve_response_message( $response ) ) {
					$body         = json_decode( wp_remote_retrieve_body( $response ) );
					$access_token = sanitize_text_field( $body->access_token );
					$expires_in   = isset( $body->expires_in ) ? absint( $body->expires_in ) : HOUR_IN_SECONDS;

					$expires_in -= 100;

					$this->set_option( 'refresh-token', $body->refresh_token );

					set_transient( 'yith-wcbk-gcal-access-token', $access_token, $expires_in );

					$this->debug( 'Access Token generated successfully', compact( 'access_token', 'expires_in', 'body' ) );

					wp_safe_redirect( $this->panel_url );
					exit();
				} else {
					$this->error( 'Error while generating Access Token: ', $response );
				}
			}

			// phpcs:enable
		}

		/*
		|--------------------------------------------------------------------------
		| Getters - Setters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get an option
		 *
		 * @param string $key     Option key.
		 * @param mixed  $default Default value.
		 *
		 * @return mixed
		 */
		public function get_option( $key, $default = false ) {
			if ( ! array_key_exists( $key, $this->options ) ) {
				$this->options[ $key ] = get_option( 'yith-wcbk-google-calendar-option-' . $key, $default );
			}

			return $this->options[ $key ];
		}

		/**
		 * Set an option
		 *
		 * @param string $key   Option key.
		 * @param mixed  $value Value to be set.
		 */
		public function set_option( $key, $value ) {
			$this->options[ $key ] = $value;
			update_option( 'yith-wcbk-google-calendar-option-' . $key, $value );
		}

		/**
		 * Get the Google API Access Token
		 *
		 * @return mixed|string
		 */
		public function get_access_token() {
			$access_token = get_transient( 'yith-wcbk-gcal-access-token' );
			if ( ! $access_token ) {
				$refresh_token = $this->get_option( 'refresh-token' );
				if ( $refresh_token ) {
					$data = array(
						'client_id'     => $this->get_client_id(),
						'client_secret' => $this->get_client_secret(),
						'refresh_token' => $refresh_token,
						'grant_type'    => 'refresh_token',
					);

					$params = array(
						'body'      => http_build_query( $data ),
						'sslverify' => false,
						'timeout'   => 60,
						'headers'   => array(
							'Content-Type' => 'application/x-www-form-urlencoded',
						),
					);

					$response = wp_remote_post( $this->oauth_url . 'token', $params );

					if ( 200 === absint( wp_remote_retrieve_response_code( $response ) ) && 'OK' === wp_remote_retrieve_response_message( $response ) ) {
						$body         = json_decode( wp_remote_retrieve_body( $response ) );
						$access_token = sanitize_text_field( $body->access_token );
						$expires_in   = isset( $body->expires_in ) ? absint( $body->expires_in ) : HOUR_IN_SECONDS;

						$expires_in -= 100;

						set_transient( 'yith-wcbk-gcal-access-token', $access_token, $expires_in );

						$this->debug( 'Access Token refreshed successfully', compact( 'access_token', 'expires_in', 'body' ) );
					} else {
						$this->error( 'Error while refreshing Access Token: ', $response );
					}
				}
			}

			return $access_token;
		}

		/**
		 * Tetrieve the calendar id.
		 *
		 * @return string
		 */
		public function get_calendar_id() {
			return $this->get_option( 'calendar-id', '' );
		}

		/**
		 * Tetrieve the client id.
		 *
		 * @return string
		 */
		public function get_client_id() {
			return $this->get_option( 'client-id', '' );
		}

		/**
		 * Retrieve the calendar secret.
		 *
		 * @return string
		 */
		public function get_client_secret() {
			return $this->get_option( 'client-secret', '' );
		}

		/**
		 * Retrieve events when synchronize bookings into Google Calendar.
		 *
		 * @return array
		 */
		public function get_booking_events_to_synchronize() {
			$events = $this->get_option( 'booking-events-to-synchronize', array( 'creation', 'update', 'status-update', 'deletion' ) );
			if ( ! $events ) {
				$events = array();
			}

			return $events;
		}

		/**
		 * Is adding note on sync enabled?
		 *
		 * @return bool
		 * @since 3.0
		 */
		public function get_event_name_format() {
			return $this->get_option( 'event-name-format', '#{id} {product_name} ({user_name})' );
		}

		/**
		 * Retrieve the Calendar list from Google Calendar
		 *
		 * @return array
		 */
		public function get_calendar_list() {
			$calendars = array();
			$uri       = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
			$params    = array(
				'sslverify' => false,
				'timeout'   => 60,
				'headers'   => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->get_access_token(),
				),
			);

			$response = wp_remote_get( $uri, $params );

			if ( 200 === absint( wp_remote_retrieve_response_code( $response ) ) && 'OK' === wp_remote_retrieve_response_message( $response ) ) {
				$body      = json_decode( wp_remote_retrieve_body( $response ) );
				$calendars = $body->items;

				$this->debug( 'Calendar List retrieved successfully', $body );
			} else {
				$this->error( 'Error while retrieving Calendar List: ', $response );
			}

			return $calendars;
		}

		/**
		 * Retrieve the timezone info of the Google Calendar set.
		 *
		 * @return string
		 */
		public function get_timezone_info() {
			$info = '';
			if ( $this->is_calendar_sync_enabled() ) {
				$uri    = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->get_calendar_id();
				$params = array(
					'sslverify' => false,
					'timeout'   => 60,
					'headers'   => array(
						'Content-Type'  => 'application/json',
						'Authorization' => 'Bearer ' . $this->get_access_token(),
					),
				);

				$response = wp_remote_get( $uri, $params );

				if ( 200 === absint( wp_remote_retrieve_response_code( $response ) ) && 'OK' === wp_remote_retrieve_response_message( $response ) ) {
					$body = json_decode( wp_remote_retrieve_body( $response ) );
					$info = $body->timeZone; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					$this->debug( 'TimeZone info retrieved successfully', $body );
				} else {
					$this->error( 'Error while retrieving timezone information: ', $response );
				}
			}

			return $info;
		}


		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		*/

		/**
		 * Return true if is connected
		 *
		 * @return bool
		 */
		public function is_connected() {
			return ! ! $this->get_client_id() && ! ! $this->get_client_secret() && ! ! $this->get_access_token();
		}

		/**
		 * Return true is the calendar sync is enabled
		 *
		 * @return bool
		 */
		public function is_calendar_sync_enabled() {
			return $this->is_connected() && $this->get_calendar_id();
		}

		/**
		 * Is debug enabled?
		 *
		 * @return bool
		 */
		public function is_debug() {
			return 'yes' === $this->get_option( 'debug', 'no' );
		}

		/**
		 * Is adding note on sync enabled?
		 *
		 * @return bool
		 */
		public function is_add_note_on_sync_enabled() {
			return 'yes' === $this->get_option( 'add-note-on-sync', 'yes' );
		}

		/**
		 * Is the synchronization enabled on booking creation?
		 *
		 * @return bool
		 */
		public function is_synchronize_on_creation_enabled() {
			return in_array( 'creation', $this->get_booking_events_to_synchronize(), true );
		}

		/**
		 * Is the synchronization enabled on booking updating?
		 *
		 * @return bool
		 */
		public function is_synchronize_on_update_enabled() {
			return in_array( 'update', $this->get_booking_events_to_synchronize(), true );
		}

		/**
		 * Is the synchronization enabled on booking status update?
		 *
		 * @return bool
		 */
		public function is_synchronize_on_status_update_enabled() {
			return in_array( 'status-update', $this->get_booking_events_to_synchronize(), true );
		}

		/**
		 * Is the synchronization enabled on booking deletion?
		 *
		 * @return bool
		 */
		public function is_synchronize_on_deletion_enabled() {
			return in_array( 'deletion', $this->get_booking_events_to_synchronize(), true );
		}

		/*
		|--------------------------------------------------------------------------
		| Views
		|--------------------------------------------------------------------------
		*/

		/**
		 * Print a view
		 *
		 * @param string $view The view.
		 * @param array  $args The arguments.
		 */
		public function get_view( $view, $args = array() ) {
			$path = 'panel/settings/' . $view;
			yith_wcbk_get_module_view( 'google-calendar', $path, $args );
		}

		/**
		 * Return the view.
		 *
		 * @param string $view The view.
		 * @param array  $args The arguments.
		 *
		 * @return string
		 */
		public function get_view_html( $view, $args = array() ) {
			ob_start();
			$this->get_view( $view, $args );

			return ob_get_clean();
		}

		/**
		 * Get the Client Secret Form view
		 *
		 * @return string
		 */
		public function get_client_secret_form_view() {
			$args = array(
				'client_id'     => $this->get_client_id(),
				'client_secret' => $this->get_client_secret(),
				'redirect_uri'  => $this->redirect_uri,
			);

			return $this->get_view_html( 'client-secret-form.php', $args );
		}

		/**
		 * Get the Access Form view
		 *
		 * @return string
		 */
		public function get_access_form_view() {
			$auth_url = add_query_arg(
				array(
					'scope'           => $this->scopes,
					'redirect_uri'    => rawurlencode( $this->redirect_uri ),
					'response_type'   => 'code',
					'client_id'       => $this->get_client_id(),
					'approval_prompt' => 'force',
					'access_type'     => 'offline',
				),
				$this->oauth_url . 'auth'
			);

			return $this->get_view_html(
				'access-form.php',
				array(
					'auth_url'     => $auth_url,
					'redirect_uri' => $this->redirect_uri,
				)
			);
		}

		/**
		 * Get the Options Form view
		 *
		 * @return string
		 */
		public function get_options_form_view() {
			$options = array(
				'calendar-id' => '',
			);

			foreach ( $options as $option => $default ) {
				$options[ $option ] = $this->get_option( $option, $default );
			}

			$args = array(
				'options'   => $options,
				'calendars' => $this->get_calendar_list(),
			);

			return $this->get_view_html( 'options-form.php', $args );
		}

		/**
		 * Get the Timezone Info Form view
		 *
		 * @return string
		 */
		public function get_timezone_info_view() {
			$google_calendar_timezone = $this->get_timezone_info();

			$args = array(
				'is_calendar_sync_enabled' => $this->is_calendar_sync_enabled(),
				'google_calendar_timezone' => $google_calendar_timezone,
				'current_timezone'         => yith_wcbk_get_timezone( 'human' ),
			);

			return $this->get_view_html( 'timezone-info.php', $args );
		}

		/**
		 * Get the Profile Details view
		 *
		 * @return string
		 */
		public function get_profile_details_view() {
			$html = '';
			if ( $this->get_access_token() ) {
				$uri    = 'https://www.googleapis.com/oauth2/v2/userinfo';
				$params = array(
					'sslverify' => false,
					'timeout'   => 60,
					'headers'   => array(
						'Content-Type'  => 'application/json',
						'Authorization' => 'Bearer ' . $this->get_access_token(),
					),
				);

				$response = wp_remote_get( $uri, $params );

				if ( 200 === absint( wp_remote_retrieve_response_code( $response ) ) && 'OK' === wp_remote_retrieve_response_message( $response ) ) {
					$body = json_decode( wp_remote_retrieve_body( $response ) );
					$html = $this->get_view_html(
						'profile-details.php',
						array(
							'name'    => $body->name,
							'picture' => $body->picture,
						)
					);

					$this->debug( 'Profile Details retrieved successfully', $body );
				} else {
					$this->error( 'Error while retrieving user information: ', $response );
				}
			}

			return $html;
		}


		/*
		|--------------------------------------------------------------------------
		| Sync Booking
		|--------------------------------------------------------------------------
		*/

		/**
		 * Sync the booking product by updating/creating the event in Google Calendar
		 *
		 * @param int|YITH_WCBK_Booking $booking The booking.
		 *
		 * @return bool|string
		 */
		public function sync_booking_event( $booking ) {
			$sync_result = false;
			if ( $this->is_calendar_sync_enabled() ) {
				$booking = yith_get_booking( $booking );
				if ( $booking && $booking->is_valid() ) {
					$booking_id  = $booking->get_id();
					$calendar_id = $this->get_calendar_id();
					$event_id    = $this->create_booking_event_id( $booking_id );

					$time_debug_key = __FUNCTION__ . '_' . $booking_id;
					yith_wcbk_time_debug_start( $time_debug_key );

					$date_format      = 'Y-m-d';
					$date_time_format = 'Y-m-d\TH:i:s';

					$event_args = array(
						'id'                      => $event_id,
						'source'                  => array(
							'title' => __( 'View Booking', 'yith-booking-for-woocommerce' ),
							'url'   => admin_url( 'post.php?post=' . $booking_id . '&action=edit' ),
						),
						'summary'                 => $booking->get_formatted_name( $this->get_event_name_format() ),
						'guestsCanInviteOthers'   => false,
						'guestsCanModify'         => false,
						'guestsCanSeeOtherGuests' => false,
					);

					if ( $booking->has_time() ) {
						$timezone = yith_wcbk_get_timezone();

						$event_args['start'] = array(
							'dateTime' => gmdate( $date_time_format, $booking->get_from() ),
							'timeZone' => $timezone,
						);
						$event_args['end']   = array(
							'dateTime' => gmdate( $date_time_format, $booking->get_to() ),
							'timeZone' => $timezone,
						);

					} else {
						$to = $booking->get_to();
						if ( $booking->is_all_day() ) {
							$to = yith_wcbk_date_helper()->get_time_sum( $to, 1, 'day' );
						}
						$event_args['start'] = array(
							'date' => gmdate( $date_format, $booking->get_from() ),
						);
						$event_args['end']   = array(
							'date' => gmdate( $date_format, $to ),
						);
					}

					if ( ! empty( $booking->get_location() ) ) {
						$event_args['location'] = $booking->get_location();
					}

					$data = $booking->get_booking_data_to_display();
					unset( $data['product'], $data['order'], $data['user'], $data['duration'] );

					$description_rows = array();
					foreach ( $data as $key => $item ) {
						$label = $item['label'] ?? '';
						$value = $item['display'] ?? '';
						if ( $value ) {
							$description_rows[ $key ] = array(
								'label' => $label,
								'value' => $value,
							);
						}
					}

					$event_args['description'] = '';

					foreach ( $description_rows as $key => $description_row ) {
						$event_args['description'] .= '<p>';
						$event_args['description'] .= '<strong>' . esc_html( $description_row['label'] ) . '</strong>: ';
						$event_args['description'] .= esc_html( $description_row['value'] );
						$event_args['description'] .= '</p>';
					}

					$email = $booking->get_user_email();
					if ( $email ) {
						$event_args['attendees'] = array(
							array(
								'responseStatus' => 'accepted',
								'email'          => $email,
							),
						);
					}

					$uri              = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';
					$event_uri        = $uri . '/' . $event_id;
					$get_event_params = array(
						'sslverify' => false,
						'timeout'   => 60,
						'headers'   => array(
							'Content-Type'  => 'application/json',
							'Authorization' => 'Bearer ' . $this->get_access_token(),
						),
					);

					$get_event_response = wp_remote_get( $event_uri, $get_event_params );

					$event_args = apply_filters( 'yith_wcbk_google_calendar_sync_event_args', $event_args, $booking );

					$params = array(
						'method'    => 'POST',
						'body'      => wp_json_encode( $event_args ),
						'sslverify' => false,
						'timeout'   => 60,
						'headers'   => array(
							'Content-Type'  => 'application/json',
							'Authorization' => 'Bearer ' . $this->get_access_token(),
						),
					);

					$sync_result = 'created';
					if ( 200 === absint( wp_remote_retrieve_response_code( $get_event_response ) ) && 'OK' === wp_remote_retrieve_response_message( $get_event_response ) ) {
						$uri              = $event_uri;
						$params['method'] = 'PUT';
						$sync_result      = 'updated';

						$this->debug( 'Booking event already exists', compact( 'booking_id', 'event_id' ) );
					}

					$response = wp_remote_post( $uri, $params );

					if ( 200 === absint( wp_remote_retrieve_response_code( $response ) ) && 'OK' === wp_remote_retrieve_response_message( $response ) ) {
						$seconds = yith_wcbk_time_debug_end( $time_debug_key );
						$this->debug( sprintf( 'Booking event sync success (%s seconds taken)', $seconds ), compact( 'booking_id', 'sync_result', 'event_args' ) );
					} else {
						$sync_result = false;
						$this->error( "Error while synchronizing Booking #$booking_id: ", $response );
					}
				}
			}

			return $sync_result;
		}


		/**
		 * Delete a booking event
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return bool|string
		 * @since 2.1.4
		 */
		public function delete_booking_event( $booking ) {
			$sync_result = false;
			if ( $this->is_calendar_sync_enabled() ) {
				$booking = yith_get_booking( $booking );
				if ( $booking && $booking->is_valid() ) {
					$booking_id  = $booking->get_id();
					$calendar_id = $this->get_calendar_id();
					$event_id    = $this->create_booking_event_id( $booking_id );

					$uri       = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';
					$event_uri = $uri . '/' . $event_id;

					$params = array(
						'method'    => 'DELETE',
						'sslverify' => false,
						'timeout'   => 60,
						'headers'   => array(
							'Content-Type'  => 'application/json',
							'Authorization' => 'Bearer ' . $this->get_access_token(),
						),
					);

					$response = wp_remote_post( $event_uri, $params );
					if ( ! is_wp_error( $response ) && empty( $response['body'] ) ) {
						$sync_result = 'deleted';
						$this->debug( 'Booking event deleted success', compact( 'booking_id', 'sync_result' ) );
					} else {
						$sync_result = false;
						$this->error( "Error while deleting Booking #{$booking_id}: ", $response );
					}
				}
			}

			return $sync_result;
		}


		/*
		|--------------------------------------------------------------------------
		| Utils
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get the home url
		 *
		 * @return string
		 */
		private function get_home_url() {
			$home_url = home_url();
			$schemes  = apply_filters( 'yith_wcbk_google_calendar_home_url_schemes', array( 'https://', 'http://', 'www.' ) );

			foreach ( $schemes as $scheme ) {
				$home_url = str_replace( $scheme, '', $home_url );
			}

			if ( strpos( $home_url, '?' ) !== false ) {
				list( $base, $query ) = explode( '?', $home_url, 2 );

				$home_url = $base;
			}

			return apply_filters( 'yith_wcbk_google_calendar_get_home_url', $home_url );
		}

		/**
		 * Retrieve an unique booking event id based on booking ID
		 *
		 * @param int $booking_id the Booking ID.
		 *
		 * @return string
		 */
		public function create_booking_event_id( $booking_id ) {
			$home_url = $this->get_home_url();

			return md5( 'booking' . absint( $booking_id ) . $home_url );
		}

		/**
		 * Add a Log as Debug message if Debug is active
		 *
		 * @param string $message The message.
		 * @param mixed  $obj     A debug object.
		 */
		public function debug( $message = '', $obj = null ) {
			if ( $this->is_debug() ) {
				if ( ! is_null( $obj ) ) {
					$message .= ! ! $message ? ' - ' : '';
					$message .= print_r( $obj, true ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				}
				yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::DEBUG, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
			}
		}

		/**
		 * Add a Log as an Error message
		 *
		 * @param string $message The message.
		 * @param mixed  $obj     A debug object.
		 */
		public function error( $message = '', $obj = null ) {
			if ( $this->is_debug() ) {
				if ( ! is_null( $obj ) ) {
					$message .= ! ! $message ? ' - ' : '';
					$message .= print_r( $obj, true ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				}
				yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
			}
		}

		/**
		 * Get the logout URL.
		 *
		 * @return string
		 */
		public function get_logout_url() {
			return add_query_arg(
				array(
					'yith-wcbk-gcal-action' => 'logout',
					'yith-wcbk-gcal-nonce'  => wp_create_nonce( 'yith-wcbk-gcal-action' ),
				),
				$this->panel_url
			);
		}

		/**
		 * Get the logout URL.
		 *
		 * @return string
		 */
		public function get_delete_client_secret_url() {
			return add_query_arg(
				array(
					'yith-wcbk-gcal-action' => 'delete-client-secret',
					'yith-wcbk-gcal-nonce'  => wp_create_nonce( 'yith-wcbk-gcal-action' ),
				),
				$this->panel_url
			);
		}
	}
}
