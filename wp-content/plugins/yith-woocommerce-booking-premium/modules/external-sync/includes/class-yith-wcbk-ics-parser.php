<?php
/**
 * Class YITH_WCBK_ICS_Parser
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_ICS_Parser' ) ) {
	/**
	 * Class YITH_WCBK_ICS_Parser
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_ICS_Parser {
		/**
		 * The ICS content.
		 *
		 * @var string
		 */
		private $ics = '';

		/**
		 * Extra props.
		 *
		 * @var array
		 */
		private $extra_props = array();

		/**
		 * Lines.
		 *
		 * @var array
		 */
		private $lines = array();

		/**
		 * Product ID.
		 *
		 * @var  string
		 */
		private $prod_id;

		/**
		 * External events.
		 *
		 * @var YITH_WCBK_Booking_External[]
		 */
		private $events = array();

		/**
		 * Current event.
		 *
		 * @var YITH_WCBK_Booking_External
		 */
		private $current_event;

		/**
		 * Counter.
		 *
		 * @var array
		 */
		private $counter_begin_end = array();

		/**
		 * YITH_WCBK_ICS_Parser constructor.
		 *
		 * @param string $ics         ICS content.
		 * @param array  $extra_props Extra props.
		 *
		 * @throws Exception The Exception.
		 */
		public function __construct( $ics, $extra_props = array() ) {
			$this->ics         = $ics;
			$this->extra_props = $extra_props;

			$this->unfold();

			if ( ! $this->lines ) {
				$this->error( 101 );
			}

			if ( rtrim( current( $this->lines ) ) !== 'BEGIN:VCALENDAR' ) {
				$this->error( 102 );
			}

			$this->parse();

			$this->check_errors();
		}

		/**
		 * Unfolds an iCal file in lines before parsing
		 */
		private function unfold() {
			// Add slashes to escape "textual" new lines.
			$string = str_replace( '\n', '\\n', $this->ics );

			// Allow support for all line separators.
			$string = str_replace( array( "\r\n", "\n\r", "\r" ), "\n", $string );

			// Allow folding on new lines by using a single space or a TAB char.
			$string = preg_replace( '/\n[\s\t]/', '', $string );

			$this->lines = explode( "\n", $string );
		}


		/**
		 * Throw a new exception for error
		 *
		 * @param int $err_no Error number.
		 *
		 * @throws Exception The Exception.
		 */
		private function error( $err_no ) {
			$err_no  = absint( $err_no );
			$errors  = array(
				100 => _x( 'Generic Error', 'ICS Parser Error', 'yith-booking-for-woocommerce' ),
				101 => _x( 'ICS file seems to be empty', 'ICS Parser Error', 'yith-booking-for-woocommerce' ),
				102 => _x( 'Malformed ICS', 'ICS Parser Error', 'yith-booking-for-woocommerce' ),
			);
			$err_msg = array_key_exists( $err_no, $errors ) ? $errors[ $err_no ] : $errors[100];

			// translators: 1. error number; 2. error message.
			$error = sprintf( _x( 'Error %1$s: %2$s', 'ICS Parser Error', 'yith-booking-for-woocommerce' ), $err_no, $err_msg );

			throw new Exception( $error );
		}

		/**
		 * Check for errors
		 *
		 * @throws Exception The exception.
		 */
		private function check_errors() {
			if ( $this->counter_begin_end ) {
				$unique_values = array_unique( array_values( $this->counter_begin_end ) );
				if ( array( 0 ) !== $unique_values ) {
					$this->error( 102 );
				}
			}
		}

		/**
		 * Set property for the current event
		 *
		 * @param string $key   Key.
		 * @param string $value Value.
		 */
		private function set_prop_to_current_event( $key, $value ) {
			if ( ! is_null( $this->current_event ) ) {
				$this->current_event->set( $key, $value );
			}
		}

		/**
		 * Set default props for the current event
		 */
		private function set_default_props_in_current_event() {
			$this->set_prop_to_current_event( 'source', $this->get_source() );
			$this->set_prop_to_current_event( 'date', time() );

			$this->set_extra_props_in_current_event();
		}

		/**
		 * Set extra props for the current event
		 */
		private function set_extra_props_in_current_event() {
			foreach ( $this->extra_props as $key => $value ) {
				$this->set_prop_to_current_event( $key, $value );
			}
		}

		/**
		 * Retrieve key value from string
		 * example if text = "BEGIN:VCALENDAR" it will return array( 'BEGIN', 'VCALENDAR')
		 *
		 * @param string $text The text.
		 *
		 * @return array|bool
		 */
		private function key_value_from_string( $text ) {
			preg_match( '/([^:]+)[:]([\w\W]*)/', $text, $matches );
			if ( ! count( $matches ) ) {
				return false;
			}
			$matches = array_splice( $matches, 1, 2 );

			return $matches;
		}

		/**
		 * Retrieve key-params from a key
		 * example if key = "DTEND;VALUE=DATE" it will return array( 'DTEND', array( 'VALUE' => 'DATE' ))
		 *
		 * @param string $key The key.
		 *
		 * @return array|bool
		 */
		private function key_params_from_key( $key ) {
			$params = array();
			if ( strpos( $key, ';' ) !== false ) {
				list( $key, $string_params ) = explode( ';', $key, 2 );

				if ( strpos( $string_params, '=' ) !== false ) {
					$temp_params = explode( '=', $string_params, 2 );
					$params      = array( $temp_params[0] => $temp_params[1] );
				}
			}

			return array( $key, $params );
		}


		/**
		 * Retrieve the timestamp from an iCal date
		 *
		 * @param string $ical_date iCal date.
		 *
		 * @return bool|false|int
		 */
		public function ical_date_to_timestamp( $ical_date ) {
			$is_utc_time = 'Z' === substr( $ical_date, - 1 );
			$ical_date   = str_replace( 'T', '', $ical_date );
			$ical_date   = str_replace( 'Z', '', $ical_date );
			$pattern     = '/([0-9]{4})';      // 1 YYYY.

			$pattern .= '([0-9]{2})';      // 2 MM.
			$pattern .= '([0-9]{2})';      // 3 DD.
			$pattern .= '([0-9]{0,2})';    // 4 HH.
			$pattern .= '([0-9]{0,2})';    // 5 MM.
			$pattern .= '([0-9]{0,2})/';   // 6 SS.
			preg_match( $pattern, $ical_date, $date );

			// Unix timestamp can't represent dates before 1970.
			if ( $date[1] < 1970 ) {
				return false;
			}

			// Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow if 32 bit integers are used.
			$timestamp = mktime(
				(int) $date[4],
				(int) $date[5],
				(int) $date[6],
				(int) $date[2],
				(int) $date[3],
				(int) $date[1]
			);
			if ( $is_utc_time ) {
				$timezone_offset = get_option( 'gmt_offset' );

				$timestamp += $timezone_offset * HOUR_IN_SECONDS;
			}

			return $timestamp;
		}

		/**
		 * Let's start the parsing
		 *
		 * @throws Exception The exception.
		 */
		public function parse() {
			foreach ( $this->lines as $line_number => $line ) {
				$line      = trim( $line );
				$key_value = $this->key_value_from_string( $line );
				if ( $key_value ) {
					list( $complete_key, $value ) = $key_value;

					list( $key, $params ) = $this->key_params_from_key( $complete_key );

					switch ( $key ) {
						case 'BEGIN':
							if ( isset( $this->counter_begin_end[ $value ] ) ) {
								$this->counter_begin_end[ $value ] ++;
							} else {
								$this->counter_begin_end[ $value ] = 1;
							}

							if ( 'VEVENT' === $value ) {
								if ( is_null( $this->current_event ) ) {
									$this->current_event = new YITH_WCBK_Booking_External();
									$this->set_default_props_in_current_event();
								} else {
									$this->error( 102 );
								}
							}

							break;

						case 'END':
							if ( isset( $this->counter_begin_end[ $value ] ) ) {
								$this->counter_begin_end[ $value ] --;
							} else {
								$this->counter_begin_end[ $value ] = - 1;
							}

							if ( 'VEVENT' === $value ) {
								if ( is_null( $this->current_event ) ) {
									$this->error( 102 );
								} else {
									$this->events[]      = $this->current_event;
									$this->current_event = null;
								}
							}
							break;

						case 'PRODID':
							$this->set_prod_id( $value );
							break;

						case 'DTSTART':
							$this->set_prop_to_current_event( 'from', $this->ical_date_to_timestamp( $value ) );
							break;

						case 'DTEND':
							$this->set_prop_to_current_event( 'to', $this->ical_date_to_timestamp( $value ) );
							break;

						case 'DESCRIPTION':
						case 'UID':
						case 'SUMMARY':
						case 'LOCATION':
							$this->set_prop_to_current_event( strtolower( $key ), $value );
							break;

					}
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get events
		 *
		 * @return array
		 */
		public function get_events() {
			return $this->events;
		}

		/**
		 * Get the source from prod_id
		 *
		 * @return string
		 */
		public function get_source() {
			$prod_id = $this->get_prod_id();
			$source  = $prod_id;
			preg_match( '/([^-\/\/]+)[\/\/]*/', $prod_id, $matches );
			if ( count( $matches ) >= 2 && ! empty( $matches[1] ) ) {
				$source = $matches[1];
			}

			return $source;
		}

		/**
		 * Get lines
		 *
		 * @return array
		 */
		public function get_lines() {
			return $this->lines;
		}

		/**
		 * Get the prod_id
		 *
		 * @return string
		 */
		public function get_prod_id() {
			return $this->prod_id;
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Set the prod_id if not set
		 *
		 * @param int $prod_id Product ID.
		 */
		public function set_prod_id( $prod_id ) {
			if ( is_null( $this->prod_id ) ) {
				$this->prod_id = $prod_id;
			}
		}
	}
}
