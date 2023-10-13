<?php
/**
 * Class YITH_WCBK_Booking_External
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Booking_External' ) ) {
	/**
	 * Class YITH_WCBK_Booking_External
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Booking_External extends YITH_WCBK_Booking_Abstract {

		/**
		 * Object data.
		 *
		 * @var array
		 */
		protected $data = array();

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'booking_external';

		/**
		 * Instances.
		 *
		 * @var array
		 */
		private static $instances = array();

		/**
		 * Current instance.
		 *
		 * @var int
		 */
		private $current_instance = 0;

		/**
		 * __get function.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			if ( isset( $this->data[ $key ] ) ) {
				return $this->data[ $key ];
			}

			return '';
		}

		/**
		 * __isset function.
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 */
		public function __isset( $key ) {
			return isset( $this->data[ $key ] );
		}

		/**
		 * The constructor.
		 *
		 * @param array $args Arguments.
		 */
		public function __construct( $args = array() ) {
			$this->data = array_merge( $this->data, self::get_defaults() );
			$this->data = wp_parse_args( $args, $this->data );

			if ( isset( self::$instances[ $this->get_product_id() ] ) ) {
				$this->current_instance = ++ self::$instances[ $this->get_product_id() ];
			} else {
				self::$instances[ $this->get_product_id() ] = 1;
				$this->current_instance                     = 1;
			}
		}

		/**
		 * Return the hook prefix.
		 *
		 * @return string
		 */
		public function get_hook_prefix() {
			return 'yith_wcbk_booking_external_';
		}

		/**
		 * Check if the booking is external
		 *
		 * @return bool
		 */
		public function is_external() {
			return true;
		}

		/**
		 * Set function.
		 *
		 * @param string $prop  The prop.
		 * @param mixed  $value The value.
		 *
		 * @return bool
		 */
		public function set( $prop, $value ) {
			$this->data[ $prop ] = $value;

			return true;
		}

		/**
		 * Return the Booking ID
		 *
		 * @return int
		 */
		public function get_id() {
			return $this->get_product_id() . '-' . $this->data['id'];
		}

		/**
		 * Return the "from" timestamp
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_from( $context = 'view' ) {
			return $this->data['from'];
		}

		/**
		 * Return the "to" timestamp
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_to( $context = 'view' ) {
			return $this->data['to'];
		}

		/**
		 * Return the description
		 *
		 * @return string
		 */
		public function get_description() {
			return $this->data['description'];
		}

		/**
		 * Return the summary
		 *
		 * @return string
		 */
		public function get_summary() {
			return $this->data['summary'];
		}

		/**
		 * Return the location
		 *
		 * @return string
		 */
		public function get_location() {
			return $this->data['location'];
		}

		/**
		 * Return the uid
		 *
		 * @return string
		 */
		public function get_uid() {
			return $this->data['uid'];
		}

		/**
		 * Return the calendar_name
		 *
		 * @return string
		 */
		public function get_calendar_name() {
			return $this->data['calendar_name'];
		}

		/**
		 * Return the date
		 *
		 * @return string
		 */
		public function get_date() {
			return $this->data['date'];
		}

		/**
		 * Return the source
		 *
		 * @return string
		 */
		public function get_source() {
			return $this->data['source'];
		}

		/**
		 * Return the source slug
		 *
		 * @return string
		 */
		public function get_source_slug() {
			return yith_wcbk_booking_external_sources()->get_slug_from_string( $this->get_source() );
		}

		/**
		 * Return the product ID
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_product_id( $context = 'view' ) {
			return absint( $this->data['product_id'] );
		}

		/**
		 * Return the product
		 *
		 * @return WC_Product_Booking|false
		 */
		public function get_product() {
			$product = wc_get_product( $this->get_product_id() );

			return yith_wcbk_is_booking_product( $product ) ? $product : false;
		}

		/**
		 * Retrieve a formatted name by a "format" parameter.
		 *
		 * @param string $format The format.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_formatted_name( $format ) {
			// Here we don't know specific information to allow formatting, so we can show the title that contains the product title.
			return $this->get_title();
		}

		/**
		 * Get the title
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_title( $context = 'view' ) {
			switch ( $this->get_source_slug() ) {
				default:
					$product_id = $this->get_product_id();
					$product    = $this->get_product();
					// translators: %s is the Product ID.
					$title = ! ! $product ? $product->get_title() : sprintf( __( 'External of #%s product', 'yith-booking-for-woocommerce' ), $product_id );
			}

			return $title;
		}

		/**
		 * Get the duration of booking including duration unit
		 */
		public function get_duration_html() {
			return '';
		}

		/**
		 * Get the edit link
		 *
		 * @return string
		 */
		public function get_edit_link() {
			return '';
		}

		/**
		 * Return true if the booking has time
		 *
		 * @return bool
		 */
		public function has_time() {
			return $this->get_to() - $this->get_from() < DAY_IN_SECONDS;
		}

		/**
		 * Check if the booking is valid
		 *
		 * @return bool
		 */
		public function is_valid() {
			return ! ! $this->get_product_id() && ! ! $this->get_id();
		}

		/**
		 * Check if the booking is valid
		 *
		 * @return bool
		 */
		public function is_completed() {
			$now = strtotime( 'now midnight' );

			return $this->get_from() < $now && $this->get_to() < $now;
		}


		/**
		 * Return the status
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_status( $context = 'view' ) {
			return 'external';
		}

		/**
		 * Return string for status
		 *
		 * @return string
		 */
		public function get_status_text() {
			return __( 'External', 'yith-booking-for-woocommerce' );
		}

		/**
		 * Check if the booking can change status to $status
		 *
		 * @param string $status The status.
		 *
		 * @return bool
		 */
		public function can_be( $status ) {
			return false;
		}

		/**
		 * Checks the booking status against a passed in status.
		 *
		 * @param string $status The status.
		 *
		 * @return bool
		 */
		public function has_status( $status ) {
			return 'external' === $status;
		}

		/**
		 * Get default data.
		 *
		 * @return string[]
		 */
		public static function get_defaults() {
			return array(
				'id'            => '',
				'product_id'    => '',
				'from'          => '',
				'to'            => '',
				'description'   => '',
				'summary'       => '',
				'location'      => '',
				'uid'           => '',
				'calendar_name' => '',
				'source'        => '',
				'date'          => '',
			);
		}

		/**
		 * Get booking data to be displayed.
		 * Useful in booking-details on frontend, emails, admin calendar.
		 *
		 * @param string $context The context (frontend or admin).
		 * @param array  $args    Props to override.
		 *
		 * @return array
		 */
		public function get_booking_data_to_display( string $context = 'frontend', array $args = array() ): array {
			$data = parent::get_booking_data_to_display( $context, $args );

			$external_extra_data = array(
				'summary'       => __( 'Summary', 'yith-booking-for-woocommerce' ),
				'description'   => __( 'Description', 'yith-booking-for-woocommerce' ),
				'location'      => __( 'Location', 'yith-booking-for-woocommerce' ),
				'uid'           => __( 'UID', 'yith-booking-for-woocommerce' ),
				'calendar_name' => __( 'Calendar Name', 'yith-booking-for-woocommerce' ),
				'source'        => __( 'Source', 'yith-booking-for-woocommerce' ),
			);

			foreach ( $external_extra_data as $key => $label ) {
				$getter = "get_{$key}";
				if ( is_callable( array( $this, $getter ) ) ) {
					$value = $this->$getter();

					switch ( $key ) {
						case 'description':
							$value = nl2br( $value );
							break;
						case 'source':
							$value = yith_wcbk_booking_external_sources()->get_name_from_string( $value );
							break;
					}

					if ( ! ! $value ) {
						$data[ 'external-' . $key ] = array(
							'label'    => $label,
							'display'  => $value,
							'priority' => 100,
						);
					}
				}
			}

			yith_wcbk_array_sort( $data );

			return $data;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_booking_external' ) ) {
	/**
	 * Retrieve a booking external.
	 *
	 * @param array $args Arguments.
	 *
	 * @return YITH_WCBK_Booking_External
	 */
	function yith_wcbk_booking_external( $args ) {
		return $args instanceof YITH_WCBK_Booking_External ? $args : new YITH_WCBK_Booking_External( $args );
	}
}
