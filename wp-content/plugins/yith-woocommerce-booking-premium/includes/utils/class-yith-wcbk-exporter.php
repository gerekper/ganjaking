<?php
/**
 * Class YITH_WCBK_Exporter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Exporter' ) ) {
	/**
	 * Class YITH_WCBK_Exporter
	 * manages exporting to csv, pdf, ics
	 */
	class YITH_WCBK_Exporter {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Exporter constructor.
		 */
		protected function __construct() {
			add_action( 'init', array( $this, 'export_action_handler' ) );
		}

		/**
		 * Handle the export actions
		 *
		 * @since 2.0.0
		 */
		public function export_action_handler() {
			if ( ! empty( $_REQUEST['yith_wcbk_exporter_action'] ) ) {
				switch ( $_REQUEST['yith_wcbk_exporter_action'] ) {
					case 'export_ics':
						if (
							! empty( $_REQUEST['yith_wcbk_exporter_nonce'] ) &&
							wp_verify_nonce( wc_clean( wp_unslash( $_REQUEST['yith_wcbk_exporter_nonce'] ) ), 'export' ) &&
							! empty( $_REQUEST['product_id'] )
						) {
							$booking_ids = yith_wcbk_booking_helper()->get_bookings_by_product( absint( $_REQUEST['product_id'] ), 'ids' );
							$this->download_ics( $booking_ids, 'yith-booking-' . absint( $_REQUEST['product_id'] ) . '-' . gmdate( 'Y-m-d_H-i-s' ) . '.ics' );
						}
						break;
					case 'export_future_ics':
						if ( ! empty( $_REQUEST['product_id'] ) ) {
							$product = yith_wcbk_get_booking_product( absint( $_REQUEST['product_id'] ) );
							if ( $product ) {
								$key      = sanitize_text_field( wp_unslash( $_REQUEST['key'] ?? '' ) );
								$security = sanitize_text_field( wp_unslash( $_REQUEST['security'] ?? '' ) );

								$allowed = ( ! ! $key && $product->is_valid_external_calendars_key( $key ) ) || ( ! ! $security && wp_verify_nonce( $security, 'export-future-ics' ) );

								if ( $allowed ) {
									$booking_ids = yith_wcbk_booking_helper()->get_future_bookings_by_product( absint( $_REQUEST['product_id'] ), 'ids' );
									$this->download_ics( $booking_ids, 'yith-booking-' . absint( $_REQUEST['product_id'] ) . '-' . gmdate( 'Y-m-d_H-i-s' ) . '.ics' );
								}
							}
						}
						wp_die( esc_html__( 'Something went wrong.', 'yith-booking-for-woocommerce' ) );
						break;
					default:
						break;
				}
			}
		}

		/**
		 * Transform an array to CSV file.
		 *
		 * @param array  $array     The array.
		 * @param string $filename  File name.
		 * @param string $delimiter Delimiter.
		 */
		private function array_to_csv_download( $array, $filename = 'export.csv', $delimiter = ',' ) {
			self::download_headers( $filename );
			$f = fopen( 'php://output', 'w' );

			foreach ( $array as $line ) {
				fputcsv( $f, $line, $delimiter );
			}
			exit;
		}

		/**
		 * Download headers.
		 *
		 * @param string $filename File name.
		 */
		private static function download_headers( $filename ) {
			$filename_array = explode( '.', $filename );
			$content_type   = strpos( $filename, '.' ) > 0 ? end( $filename_array ) : 'txt';

			if ( 'ics' === $content_type ) {
				$content_type = 'text/calendar';
			}

			header( 'X-Robots-Tag: noindex, nofollow', true );
			header( 'Content-Type: ' . $content_type . '; charset=' . get_option( 'blog_charset' ), true );
			header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
		}

		/**
		 * Generate the template
		 *
		 * @param int  $booking_id Booking ID.
		 * @param bool $is_admin   Is admin flag.
		 */
		public function generate_pdf( $booking_id, $is_admin = true ) {
			// disable error reporting to prevent issues when generating PDF.
			error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting

			$filename = apply_filters( 'yith_wcbk_pdf_file_name', "booking_$booking_id.pdf", $booking_id, $is_admin );
			$booking  = yith_get_booking( $booking_id );

			ob_start();
			wc_get_template(
				'booking/pdf/booking.php',
				array(
					'booking'    => $booking,
					'booking_id' => $booking_id,
					'is_admin'   => $is_admin,
				),
				'',
				YITH_WCBK_TEMPLATE_PATH
			);
			$html = ob_get_clean();
			require_once YITH_WCBK_DOMPDF_DIR . 'autoload.inc.php';

			$pdf     = new Dompdf\Dompdf();
			$options = $pdf->getOptions();
			$options->set( 'defaultFont', 'dejavu sans' );
			$pdf->setOptions( $options );
			$pdf->setPaper( 'A4' );

			$pdf->loadHtml( $html );
			$pdf->render();

			$pdf_options = array( 'Attachment' => 0 ); // 0 -> open | 1 -> download

			$pdf->stream( $filename, $pdf_options );
			die();
		}

		/**
		 * Download bookings in a file
		 *
		 * @param array $post_ids Booking ids.
		 */
		public function download_csv( $post_ids ) {
			$post_ids = ! ! $post_ids ? (array) $post_ids : array();

			$booking_fields = apply_filters(
				'yith_wcbk_csv_fields',
				array(
					'booking_id',
					'product_id',
					'product_name',
					'date',
					'status',
					'order_id',
					'user_id',
					'username',
					'duration',
					'from',
					'to',
				)
			);

			$csv_array = array( $booking_fields );

			foreach ( $post_ids as $post_id ) {
				$booking = yith_get_booking( $post_id );
				if ( $booking->is_valid() ) {
					$current_booking = array();
					foreach ( $booking_fields as $booking_field ) {
						$val = '';
						switch ( $booking_field ) {
							case 'booking_id':
								$val = $booking->get_id();
								break;
							case 'product_id':
								$product_id = $booking->get_product_id();
								$val        = ! ! $product_id ? $product_id : '';
								break;
							case 'product_name':
								$val = get_the_title( $booking->get_product_id() );
								break;
							case 'date':
								$booking_post = get_post( $booking->get_id() );
								$val          = yith_wcbk_datetime( strtotime( $booking_post->post_date ) );
								break;
							case 'status':
								$val = $booking->get_status_text();
								break;
							case 'order_id':
								$val = ! ! $booking->get_order_id() ? $booking->get_order_id() : '';
								break;
							case 'user_id':
								$val = ! ! $booking->get_user_id() ? $booking->get_user_id() : '';
								break;
							case 'username':
								$user = $booking->get_user();
								$val  = ! ! $user ? $user->nickname : '';
								break;
							case 'duration':
								$val = $booking->get_duration_html();
								break;
							case 'from':
								$val = $booking->get_formatted_from();
								break;
							case 'to':
								$val = $booking->get_formatted_to();
								break;
						}

						$hook              = 'yith_wcbk_csv_field_' . $booking_field;
						$field_value       = apply_filters( $hook, $val, $booking );
						$current_booking[] = apply_filters( 'yith_wcbk_csv_field_value', $field_value, $booking_field, $booking );
					}

					$csv_array[] = $current_booking;
				}
			}

			$delimiter = apply_filters( 'yith_wcbk_csv_delimiter', ',' );
			$filename  = apply_filters( 'yith_wcbk_csv_file_name', 'yith-bookings-' . gmdate( 'Y-m-d' ) . '.csv' );

			$this->array_to_csv_download( $csv_array, $filename, $delimiter );
		}

		/**
		 * Download ICS file.
		 *
		 * @param array       $post_ids Booking ids.
		 * @param bool|string $filename File name.
		 */
		public function download_ics( $post_ids, $filename = false ) {
			$filename = ! ! $filename ? $filename : 'yith-bookings-' . gmdate( 'Y-m-d_H-i-s' ) . '.ics';
			$filename = apply_filters( 'yith_wcbk_ics_file_name', $filename );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			empty( $_GET['yith_wcbk_exporter_debug'] ) && self::download_headers( $filename );
			echo $this->get_ics( $post_ids, current_user_can( 'yith_manage_bookings' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			exit;
		}

		/**
		 * Download bookings in ICS file
		 *
		 * @param int|array $post_ids   Booking ids.
		 * @param bool      $is_admin   Is admin flag.
		 * @param bool      $force_time Force time flag.
		 *
		 * @return string
		 */
		public function get_ics( $post_ids, $is_admin = false, $force_time = false ) {
			$post_ids         = ! ! $post_ids ? (array) $post_ids : array();
			$blog_name        = get_bloginfo( 'name' );
			$date_format      = 'Ymd';
			$date_time_format = 'Ymd\THis';
			$home_url         = $this->get_home_url();
			$home_url         = str_replace( '/', '_', $home_url );
			$timezone_offset  = get_option( 'gmt_offset' );
			$timezone_string  = get_option( 'timezone_string' );

			$rows = array(
				'BEGIN:VCALENDAR',
				'VERSION:2.0',
				"PRODID:-//$blog_name, by YITH Booking and Appointment for WooCommerce//NONSGML v1.0",
				'METHOD:REQUEST',
				'CALSCALE:GREGORIAN',
			);
			if ( $timezone_string ) {
				$rows[] = "X-WR-TIMEZONE:$timezone_string";
			}

			foreach ( $post_ids as $id ) {
				$booking = yith_get_booking( $id );
				if ( $booking->is_valid() ) {
					$rows[] = 'BEGIN:VEVENT';
					$rows[] = 'DTSTAMP:' . gmdate( $date_time_format, strtotime( 'now' ) );

					if ( ! $booking->has_time() ) {
						$to = $booking->get_to();
						if ( $booking->is_all_day() ) {
							$to = yith_wcbk_date_helper()->get_time_sum( $to, 1, 'day' );
						}

						$from_row = 'DTSTART;VALUE=DATE:' . gmdate( $date_format, $booking->get_from() );
						$to_row   = 'DTEND;VALUE=DATE:' . gmdate( $date_format, $to );
						if ( $force_time ) {
							$product = $booking->get_product();
							if ( $product && $product->get_check_in() && $product->get_check_out() ) {
								$check_in  = str_replace( ':', '', yith_wcbk_string_to_time_slot( $product->get_check_in() ) );
								$check_out = str_replace( ':', '', yith_wcbk_string_to_time_slot( $product->get_check_out() ) );
								if ( $check_in && $check_out ) {
									$from_row = 'DTSTART:' . gmdate( $date_format, $booking->get_from() ) . "T{$check_in}00";
									$to_row   = 'DTEND:' . gmdate( $date_format, $to ) . "T{$check_out}00";
								}
							}
						}
					} else {
						$from_row = 'DTSTART:' . gmdate( $date_time_format, $booking->get_from() );
						$to_row   = 'DTEND:' . gmdate( $date_time_format, $booking->get_to() );
					}

					$rows[] = $from_row;
					$rows[] = $to_row;

					$timezone_offset_sign  = $timezone_offset >= 0 ? '+' : '-';
					$timezone_offset_value = absint( $timezone_offset );
					$timezone_offset_value = $timezone_offset_value < 10 ? ( '0' . $timezone_offset_value ) : $timezone_offset_value;

					$rows[] = 'TZOFFSETTO:' . sprintf( '%s%s00', $timezone_offset_sign, $timezone_offset_value );

					if ( ! empty( $booking->get_location() ) ) {
						$rows[] = 'LOCATION:' . $booking->get_location();
					}

					$rows[] = 'UID:booking_' . $id . '@' . $home_url;

					$summary = $booking->get_title();
					if ( $is_admin && $booking->get_user() ) {
						$user      = $booking->get_user();
						$user_info = '';
						if ( $user ) {
							$user_info = ' - ' . esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')';
						}
						$summary .= $user_info;
					}
					$summary = apply_filters( 'yith_wcbk_ics_event_summary', $summary, $booking, $is_admin );
					$rows[]  = 'SUMMARY:' . $summary;

					$data = $booking->get_booking_data_to_display();
					unset( $data['product'], $data['order'], $data['user'], $data['duration'] );

					$description_data = array();
					foreach ( $data as $key => $item ) {
						$label = strtoupper( $item['label'] ?? '' );
						$value = $item['display'] ?? '';
						if ( $value ) {
							$description_data[ $label ] = wp_strip_all_tags( $value );
						}
					}

					$description_data = apply_filters( 'yith_wcbk_ics_event_description_data', $description_data, $booking, $is_admin );
					$description_rows = array();
					foreach ( $description_data as $label => $data ) {
						$description_rows[] .= "{$label}: $data";
					}
					$description = implode( '\n', $description_rows );
					$description = apply_filters( 'yith_wcbk_ics_event_description', $description, $booking, $is_admin );

					$rows[] = 'DESCRIPTION:' . $description;

					$rows = apply_filters( 'yith_wcbk_ics_event_rows', $rows, $booking );

					$rows[] = 'END:VEVENT';
				}
			}

			$rows[] = 'END:VCALENDAR';
			$ics    = implode( "\r\n", $rows );

			return $ics;
		}

		/**
		 * Get the home url
		 *
		 * @return string
		 */
		private function get_home_url() {
			$home_url = home_url();
			$schemes  = apply_filters( 'yith_wcbk_exporter_home_url_schemes', array( 'https://', 'http://', 'www.' ) );

			foreach ( $schemes as $scheme ) {
				$home_url = str_replace( $scheme, '', $home_url );
			}

			if ( strpos( $home_url, '?' ) !== false ) {
				list( $base, $query ) = explode( '?', $home_url, 2 );

				$home_url = $base;
			}

			$home_url = untrailingslashit( $home_url );

			return apply_filters( 'yith_wcbk_exporter_get_home_url', $home_url );
		}
	}
}
