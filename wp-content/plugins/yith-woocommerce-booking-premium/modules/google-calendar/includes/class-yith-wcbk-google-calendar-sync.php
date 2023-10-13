<?php
/**
 * Google Calendar Sync Class
 * handle Google Calendar synchronization.
 *
 * @package YITH\Booking\Modules\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Google_Calendar_Sync' ) ) {
	/**
	 * Class YITH_WCBK_Google_Calendar_Sync
	 * Todo: needs refactoring.
	 *
	 * @author YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Google_Calendar_Sync {
		use YITH_WCBK_Singleton_Trait;

		const BG_PROCESS_GROUP = 'yith_wcbk_google_calendar_background_sync';

		/**
		 * Magic __get method for backwards compatibility. Maps legacy vars to new getters.
		 *
		 * @param string $key Key name.
		 *
		 * @return mixed
		 */
		public function __get( string $key ) {
			$value = null;

			switch ( $key ) {
				case 'google_calendar':
					$value = $this->google_calendar();
					break;

				default:
					return null;
			}

			yith_wcbk_doing_it_wrong( __CLASS__ . '::' . $key, 'This property should not be accessed directly.', '4.0.0' );

			return $value;
		}

		/**
		 * The constructor.
		 */
		protected function __construct() {
			$this->google_calendar();

			if ( $this->is_enabled() ) {
				if ( $this->google_calendar()->is_synchronize_on_creation_enabled() ) {
					add_action( 'yith_wcbk_booking_created', array( $this, 'sync_booking' ), 10, 1 );
					add_action( 'yith_wcbk_google_calendar_booking_sync_on_creation', array( $this, 'sync_booking' ), 10, 1 );
				}

				if ( $this->google_calendar()->is_synchronize_on_status_update_enabled() ) {
					add_action( 'yith_wcbk_booking_status_changed', array( $this, 'sync_booking' ), 10, 1 );
					add_action( 'yith_wcbk_google_calendar_booking_sync_on_status_update', array( $this, 'sync_booking' ), 10, 1 );
				}

				if ( $this->google_calendar()->is_synchronize_on_update_enabled() ) {
					add_action( 'yith_wcbk_google_calendar_booking_sync_on_update', array( $this, 'sync_booking' ), 10, 1 );
				}

				if ( $this->google_calendar()->is_synchronize_on_deletion_enabled() ) {
					add_action( 'delete_post', array( $this, 'sync_booking_on_deletion' ), 10, 1 );
					add_action( 'trashed_post', array( $this, 'sync_booking_on_deletion' ), 10, 1 );
					add_action( 'untrashed_post', array( $this, 'sync_booking' ), 10, 1 );
				}

				add_action( 'yith_wcbk_google_calendar_booking_sync', array( $this, 'sync_booking' ), 10, 1 );

				if ( yith_wcbk()->is_request( 'admin' ) ) {
					add_action( 'wp_loaded', array( $this, 'handle_actions' ), 90 );

					add_filter( 'yith_wcbk_booking_custom_columns', array( $this, 'add_google_sync_column' ) );
					add_action( 'yith_wcbk_booking_render_custom_columns', array( $this, 'render_google_sync_column' ), 10, 3 );
					add_action( 'yith_wcbk_booking_actions_meta_box_after', array( $this, 'print_action_in_booking_meta_box' ), 10, 1 );
				}
			}

			if ( yith_wcbk()->is_request( 'admin' ) ) {
				add_action( 'yith_wcbk_print_google_calendar_tab', array( $this, 'print_google_calendar_tab' ) );
			}
		}

		/**
		 * Get the Google Calendar instance.
		 *
		 * @return YITH_WCBK_Google_Calendar
		 */
		public function google_calendar(): YITH_WCBK_Google_Calendar {
			return YITH_WCBK_Google_Calendar::get_instance();
		}

		/**
		 * Add Google Sync column in Booking WP List
		 *
		 * @param array $columns The columns.
		 *
		 * @return array
		 */
		public function add_google_sync_column( $columns ) {
			$text                            = esc_html__( 'Google Calendar Sync', 'yith-booking-for-woocommerce' );
			$columns['google-calendar-sync'] = "<span class='yith-wcbk-google-calendar-sync-head tips' data-tip='{$text}'>{$text}</span>";

			return $columns;
		}

		/**
		 * Print Google Sync column in Booking WP List
		 *
		 * @param string            $column  The column.
		 * @param int               $post_id The post ID.
		 * @param YITH_WCBK_Booking $booking The Booking.
		 */
		public function render_google_sync_column( $column, $post_id, $booking ) {
			if ( 'google-calendar-sync' === $column ) {
				$sync_status = 'not-sync';
				$date        = '';
				$label       = __( 'not synchronized', 'yith-booking-for-woocommerce' );
				if ( $booking->get_google_calendar_last_update() ) {
					$sync_status = 'sync';
					$date        = date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->get_google_calendar_last_update() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
					$label       = __( 'synchronized', 'yith-booking-for-woocommerce' );
				}

				$tip = $label;

				if ( $date ) {
					$tip .= '<br />' . $date;
				}

				echo '<span class="yith-wcbk-google-calendar-sync-status ' . esc_attr( $sync_status ) . ' yith-icon yith-icon-update tips" data-tip="' . esc_attr( $tip ) . '"></span>';
			}
		}

		/**
		 * Print Google Sync action in booking meta-box.
		 *
		 * @param YITH_WCBK_Booking $booking The Booking.
		 */
		public function print_action_in_booking_meta_box( YITH_WCBK_Booking $booking ) {
			$force_sync_url = $this->get_action_url( 'sync-booking', array( 'booking_id' => $booking->get_id() ) );

			yith_wcbk_get_module_view( 'google-calendar', 'booking/actions.php', compact( 'booking', 'force_sync_url' ) );
		}

		/**
		 * Get the action url
		 *
		 * @param string $action The action.
		 * @param array  $args   Arguments.
		 * @param string $url    The URL.
		 *
		 * @return string
		 */
		public function get_action_url( $action, $args = array(), $url = '' ) {
			$args         = ! ! $args && is_array( $args ) ? $args : array();
			$default_args = array(
				'yith-wcbk-google-calendar-sync-action' => $action,
				'_wpnonce'                              => wp_create_nonce( 'yith-wcbk-google-calendar-sync' ),
				'_wp_http_referer'                      => isset( $_SERVER['REQUEST_URI'] ) ? rawurlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			);

			$args = wp_parse_args( $args, $default_args );
			$url  = ! ! $url ? $url : admin_url();

			return add_query_arg( $args, $url );
		}


		/**
		 * Handle actions.
		 */
		public function handle_actions() {
			if (
				! empty( $_REQUEST['yith-wcbk-google-calendar-sync-action'] ) &&
				isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'yith-wcbk-google-calendar-sync' )
			) {
				$redirect_url = wp_get_referer();
				$action       = sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-google-calendar-sync-action'] ) );
				switch ( $action ) {
					case 'sync-booking':
						if ( ! empty( $_REQUEST['booking_id'] ) ) {
							$booking_id = absint( $_REQUEST['booking_id'] );
							/**
							 * DO_ACTION: yith_wcbk_google_calendar_booking_sync
							 * Used to synchronize the Google Calendar event.
							 *
							 * @param int $booking_id The booking ID.
							 */
							do_action( 'yith_wcbk_google_calendar_booking_sync', $booking_id );
						}
						break;

					case 'force-sync-all-bookings':
					case 'sync-new-bookings':
						if ( ! yith_wcbk()->background_processes->is_group_running( self::BG_PROCESS_GROUP ) ) {
							if ( 'force-sync-all-bookings' === $action ) {
								yith_wcbk()->background_processes->schedule_single( array( __CLASS__, 'delete_last_update_meta_for_all_products' ), array(), self::BG_PROCESS_GROUP );
							}
							yith_wcbk()->background_processes->schedule_single( array( __CLASS__, 'sync_new_bookings_batch' ), array(), self::BG_PROCESS_GROUP );

							WC_Admin_Notices::add_custom_notice(
								'yith_wcbk_google_calendar_sync',
								sprintf(
									'<strong>%s</strong> <a href="%s">%s &rarr;</a>',
									esc_html__( 'Google Calendar Sync - your bookings are being synchronized in the background.', 'yith-booking-for-woocommerce' ),
									esc_url(
										add_query_arg(
											array(
												'page'   => 'wc-status',
												'tab'    => 'action-scheduler',
												's'      => self::BG_PROCESS_GROUP,
												'status' => 'pending',
											),
											admin_url( 'admin.php' )
										)
									),
									esc_html__( 'View progress', 'yith-booking-for-woocommerce' )
								)
							);
						}

						$redirect_url = add_query_arg(
							array(
								'page'    => YITH_WCBK_Admin::PANEL_PAGE,
								'tab'     => 'settings',
								'sub_tab' => 'settings-calendars',
							),
							admin_url( 'admin.php' )
						);

						admin_url( 'admin.php?page=yith_wcbk_panel&tab=google-calendar' );
						break;
				}

				wp_safe_redirect( $redirect_url );
			}
		}

		/**
		 * Delete last update meta for all products.
		 */
		public static function delete_last_update_meta_for_all_products() {
			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"DELETE pm
    					FROM $wpdb->postmeta AS pm
    					INNER JOIN $wpdb->posts AS post ON post.ID = pm.post_id
    					WHERE pm.meta_key = '_google_calendar_last_update' AND post.post_type = %s",
					YITH_WCBK_Post_Types::BOOKING
				)
			);
		}

		/**
		 * Synchronize new bookings in batch.
		 */
		public static function sync_new_bookings_batch() {
			$args = array(
				'posts_per_page' => 10, // Sync 10 events per time, to avoid too many consecutive requests to Google Calendar API.
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => '_google_calendar_last_update',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_google_calendar_last_update',
						'value'   => 1,
						'compare' => '<',
					),
				),
			);

			$bookings = yith_wcbk_get_booking_post_ids( $args );

			if ( ! $bookings ) {
				$notice = sprintf(
					'<strong>%s</strong>',
					esc_html__( 'Google Calendar Sync - synchronization completed!', 'yith-booking-for-woocommerce' )
				);
				WC_Admin_Notices::add_custom_notice( 'yith_wcbk_google_calendar_sync', $notice );

				// Stop the execution, since there are no more bookings to update.
				return false;
			}

			foreach ( $bookings as $booking_id ) {
				/**
				 * DO_ACTION: yith_wcbk_google_calendar_booking_sync
				 * Used to synchronize the Google Calendar event.
				 *
				 * @param int $booking_id The booking ID.
				 */
				do_action( 'yith_wcbk_google_calendar_booking_sync', $booking_id );
			}

			return true;
		}

		/**
		 * Synchronize new bookings
		 *
		 * @deprecated 4.1.0
		 */
		public function sync_new_bookings() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Google_Calendar_Sync::sync_new_bookings', '4.1.0' );
			yith_wcbk()->background_processes->schedule_single( array( __CLASS__, 'sync_new_bookings_batch' ), array(), self::BG_PROCESS_GROUP );
		}

		/**
		 * Is enabled?
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return ! ! $this->google_calendar() && $this->google_calendar()->is_calendar_sync_enabled();
		}

		/**
		 * Sync booking
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function sync_booking( $booking ) {
			$booking = yith_get_booking( $booking );
			$allowed = 'yith_wcbk_booking_status_changed' !== current_action() || ! doing_action( 'save_post' );

			if ( $booking && apply_filters( 'yith_wcbk_google_calendar_allow_booking_sync', $allowed, $booking ) ) {
				$sync_result = $this->google_calendar()->sync_booking_event( $booking );
				if ( $sync_result ) {
					$note = '';
					switch ( $sync_result ) {
						case 'created':
							$note = __( 'Google Calendar: event successfully created', 'yith-booking-for-woocommerce' );
							break;

						case 'updated':
							$note = __( 'Google Calendar: event successfully updated', 'yith-booking-for-woocommerce' );
							break;
					}

					if ( $note && apply_filters( 'yith_wcbk_google_calendar_add_note_in_booking_on_sync', $this->google_calendar()->is_add_note_on_sync_enabled(), $booking ) ) {
						$booking->add_note( 'google-calendar', $note );
					}

					$booking->set_google_calendar_last_update( time() );
					$booking->save();
				} else {
					yith_wcbk_add_log( sprintf( 'Sync Booking #%s failed!', $booking->get_id() ), YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
				}
			}
		}

		/**
		 * Sync booking on deletion
		 *
		 * @param int $post_id The post ID.
		 */
		public function sync_booking_on_deletion( $post_id ) {
			if ( YITH_WCBK_Post_Types::BOOKING === get_post_type( $post_id ) ) {
				$booking = yith_get_booking( $post_id );
				if ( $booking ) {
					$sync_result = $this->google_calendar()->delete_booking_event( $booking );
					if ( $sync_result ) {
						if ( apply_filters( 'yith_wcbk_google_calendar_add_note_in_booking_on_sync', $this->google_calendar()->is_add_note_on_sync_enabled(), $booking ) ) {
							$note = __( 'Google Calendar: event successfully deleted', 'yith-booking-for-woocommerce' );
							$booking->add_note( 'google-calendar', $note );
						}
					} else {
						yith_wcbk_add_log( sprintf( 'Deleting Booking #%s failed!', $booking->get_id() ), YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
					}
				}
			}
		}

		/**
		 * Print the Google Calendar tab
		 */
		public function print_google_calendar_tab() {
			yith_wcbk_get_module_view( 'google-calendar', 'panel/settings.php' );
		}
	}
}
