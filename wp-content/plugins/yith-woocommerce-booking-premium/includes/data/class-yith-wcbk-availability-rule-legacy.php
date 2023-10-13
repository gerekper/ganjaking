<?php
/**
 * Availability Rule Legacy class
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Availability_Rule_Legacy' ) ) {
	/**
	 * Class YITH_WCBK_Availability_Rule_Legacy
	 *
	 * @version 2.1.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Availability_Rule_Legacy extends YITH_WCBK_Simple_Object {
		/**
		 * The object type.
		 *
		 * @var string
		 */
		protected $object_type = 'availability_rule';

		/**
		 * Data array.
		 *
		 * @var array
		 */
		protected $data = array(
			'name'          => '',
			'enabled'       => 'yes',
			'type'          => 'month',
			'from'          => '',
			'to'            => '',
			'bookable'      => 'yes',
			'days_enabled'  => 'no',
			'days'          => array(
				'1' => 'yes',
				'2' => 'yes',
				'3' => 'yes',
				'4' => 'yes',
				'5' => 'yes',
				'6' => 'yes',
				'7' => 'yes',
			),
			'times_enabled' => 'no',
			'day_time_from' => array(),
			'day_time_to'   => array(),
		);

		/**
		 * Magic Method __get
		 * for backward compatibility
		 *
		 * @param string $key The key.
		 *
		 * @return mixed|null
		 */
		public function __get( $key ) {
			$getter = 'get_' . $key;
			$value  = is_callable( array( $this, $getter ) ) ? $this->$getter : $this->get_prop( $key );
			if ( null !== $value ) {
				$this->$key = $value;
			}

			return $value;
		}

		/**
		 * Get the from value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_from( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_from', '3.0.0', 'YITH_WCBK_Availability_Rule::get_date_ranges' );

			if ( is_callable( array( $this, 'get_date_ranges' ) ) ) {
				$date_ranges = $this->get_date_ranges();
				if ( ! ! $date_ranges ) {
					$date_range = current( $date_ranges );

					return $date_range['from'] ?? '';
				}
			}

			return '';
		}

		/**
		 * Get the to value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_to( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_to', '3.0.0', 'YITH_WCBK_Availability_Rule::get_date_ranges' );

			if ( is_callable( array( $this, 'get_date_ranges' ) ) ) {
				$date_ranges = $this->get_date_ranges();
				if ( ! ! $date_ranges ) {
					$date_range = current( $date_ranges );

					return $date_range['to'] ?? '';
				}
			}

			return '';
		}

		/**
		 * Get the bookable value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_bookable( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_bookable', '3.0.0' );

			return 'yes';
		}

		/**
		 * Get the days enabled value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_days_enabled( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_days_enabled', '3.0.0' );

			return 'no';
		}

		/**
		 * Get the days value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public function get_days( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_days', '3.0.0' );

			return array(
				'1' => 'yes',
				'2' => 'yes',
				'3' => 'yes',
				'4' => 'yes',
				'5' => 'yes',
				'6' => 'yes',
				'7' => 'yes',
			);
		}

		/**
		 * Get the times_enabled value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_times_enabled( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_times_enabled', '3.0.0' );

			return 'no';
		}

		/**
		 * Get the day_time_from value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public function get_day_time_from( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_day_time_from', '3.0.0' );

			return array();
		}

		/**
		 * Get the day_time_to value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public function get_day_time_to( $context = 'view' ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::get_day_time_to', '3.0.0' );

			return array();
		}

		/**
		 * Set from
		 *
		 * @param string $from The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_from( $from ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}

		/**
		 * Set to
		 *
		 * @param string $to The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_to( $to ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}

		/**
		 * Set bookable
		 *
		 * @param string|bool $bookable The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_bookable( $bookable ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}

		/**
		 * Set days_enabled
		 *
		 * @param string|bool $days_enabled The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_days_enabled( $days_enabled ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}

		/**
		 * Set days
		 *
		 * @param array $days The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_days( $days ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}


		/**
		 * Set times_enabled
		 *
		 * @param string|bool $times_enabled The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_times_enabled( $times_enabled ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}

		/**
		 * Set day_time_from
		 *
		 * @param array $day_time_from The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_day_time_from( $day_time_from ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}

		/**
		 * Set day_time_to
		 *
		 * @param array $day_time_to The value to be set.
		 *
		 * @deprecated 3.0.0
		 */
		public function set_day_time_to( $day_time_to ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
		}


		/**
		 * Return true if has days enabled
		 *
		 * @return bool
		 * @deprecated 3.0.0
		 */
		public function has_days_enabled() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );

			return 'yes' === $this->get_days_enabled();
		}

		/**
		 * Return true if has days enabled
		 *
		 * @return bool
		 * @deprecated 3.0.0
		 */
		public function has_times_enabled() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );

			return 'yes' === $this->get_times_enabled();
		}


		/*
		|--------------------------------------------------------------------------
		| Non-crud Getters
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Return day time from
		 *
		 * @param int $day_number The day number.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_day_time_from_by_day( $day_number ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
			$values = $this->get_day_time_from();

			return $values[ $day_number ] ?? '00:00';
		}

		/**
		 * Return day time to
		 *
		 * @param int $day_number The day number.
		 *
		 * @return string
		 * @deprecated 3.0.0
		 */
		public function get_day_time_to_by_day( $day_number ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Availability_Rule::' . __FUNCTION__, '3.0.0' );
			$values = $this->get_day_time_to();

			return $values[ $day_number ] ?? '00:00';
		}
	}
}

if ( ! function_exists( 'yith_wcbk_availability_range' ) ) {
	/**
	 * Return an availability rule object.
	 *
	 * @param array $args Arguments.
	 *
	 * @return YITH_WCBK_Availability_Rule
	 * @deprecated since 2.1 | use yith_wcbk_availability_rule instead
	 */
	function yith_wcbk_availability_range( $args ) {
		yith_wcbk_deprecated_function( 'yith_wcbk_availability_range', '2.1', 'yith_wcbk_availability_rule' );

		return yith_wcbk_availability_rule( $args );
	}
}
