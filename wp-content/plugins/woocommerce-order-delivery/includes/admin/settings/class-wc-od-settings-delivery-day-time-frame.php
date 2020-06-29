<?php
/**
 * Settings: Delivery Day Time Frame.
 *
 * @package WC_OD/Admin/Settings
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Settings_Time_Frame', false ) ) {
	include_once 'class-wc-od-settings-time-frame.php';
}

if ( ! class_exists( 'WC_OD_Settings_Delivery_Day_Time_Frame', false ) ) {
	/**
	 * WC_OD_Settings_Delivery_Day_Time_Frame class.
	 */
	class WC_OD_Settings_Delivery_Day_Time_Frame extends WC_OD_Settings_Time_Frame {

		/**
		 * Settings Form ID.
		 *
		 * @var String
		 */
		public $id = 'delivery_days';

		/**
		 * The delivery day ID.
		 *
		 * @var mixed A weekday index (0-6). False otherwise.
		 */
		protected $day_id;

		/**
		 * The delivery day object.
		 *
		 * @var WC_OD_Delivery_Day
		 */
		protected $delivery_day;

		/**
		 * Constructor.
		 *
		 * @since 1.5.0
		 *
		 * @param mixed  $day_id   The delivery day ID.
		 * @param string $frame_id The time frame ID.
		 */
		public function __construct( $day_id, $frame_id ) {
			$this->day_id = $day_id;

			parent::__construct( $frame_id );
		}

		/**
		 * Gets the delivery day object.
		 *
		 * @since 1.6.0
		 *
		 * @return WC_OD_Delivery_Day
		 */
		public function get_delivery_day() {
			if ( is_null( $this->delivery_day ) ) {
				$this->delivery_day = wc_od_get_delivery_day( $this->day_id );
			}

			return $this->delivery_day;
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.5.0
		 */
		public function init_settings() {
			$settings     = $this->get_form_fields_defaults();
			$delivery_day = $this->get_delivery_day();

			if ( $this->is_new() ) {
				// Copy the shipping methods fields from the delivery day settings.
				$settings['shipping_methods_option'] = $delivery_day->get_shipping_methods_option();
				$settings['shipping_methods']        = $delivery_day->get_shipping_methods();

				// Backward compatibility.
				$settings['delivery_days'] = array( (string) $this->day_id );
			} else {
				$time_frame = $delivery_day->get_time_frames()->get( $this->frame_id );

				if ( $time_frame ) {
					$settings = array_merge( $settings, $time_frame->to_array() );
				}
			}

			if ( $settings['shipping_methods_option'] ) {
				$setting_key = "{$settings['shipping_methods_option']}_shipping_methods";

				$settings[ $setting_key ] = $settings['shipping_methods'];
			}

			unset( $settings['shipping_methods'] );

			$this->settings = $settings;
		}

		/**
		 * Outputs the settings screen heading.
		 *
		 * @since 1.6.0
		 */
		public function output_heading() {
			$weekdays = wc_od_get_week_days();
			$label    = ( $this->is_new() ? __( 'Add Time Frame', 'woocommerce-order-delivery' ) : $this->settings['title'] );

			echo '<h2>';

			echo str_replace(
				array(
					'[delivery_day]',
					'[time_frame]',
				),
				array(
					$weekdays[ $this->day_id ],
					$label,
				),
				esc_html_x( 'Delivery days > [delivery_day] > [time_frame]', 'time frame settings page title', 'woocommerce-order-delivery' )
			); // WPCS: XSS ok.

			wc_back_link(
				__( 'Return to delivery day settings', 'woocommerce-order-delivery' ),
				wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $this->day_id ) )
			);

			echo '</h2>';
		}

		/**
		 * After saving the form.
		 *
		 * @since 1.6.0
		 *
		 * @param bool $saved Was anything saved?.
		 */
		public function after_save( $saved ) {
			// Reset the delivery day.
			$this->delivery_day = null;

			if ( ! $this->has_errors() ) {
				$this->maybe_redirect();
			}
		}

		/**
		 * Redirect to the current delivery day screen after save a new time frame.
		 *
		 * @since 1.5.0
		 */
		public function maybe_redirect() {
			if ( $this->is_new() ) {
				wp_safe_redirect( wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $this->day_id ) ) );
				exit;
			}
		}

		/**
		 * Sanitize the settings before save the option.
		 *
		 * @since 1.5.0
		 *
		 * @param array $settings The settings to sanitize.
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			$settings = parent::sanitized_fields( $settings );

			// Backward compatibility.
			unset( $settings['delivery_days'] );

			// Insert the time frame settings into the 'delivery_days' setting.
			$delivery_days = wc_od_get_delivery_days();
			$time_frames   = $delivery_days->get( $this->day_id )->get_time_frames();

			if ( $this->is_new() ) {
				$time_frames->add( $settings );
			} else {
				$time_frames->set( $this->frame_id, $settings );
			}

			return $delivery_days->to_array();
		}
	}
}
