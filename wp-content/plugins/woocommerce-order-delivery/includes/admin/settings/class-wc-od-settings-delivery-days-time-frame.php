<?php
/**
 * Settings: Delivery Days Time Frame.
 *
 * @package WC_OD/Admin/Settings
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Settings_Time_Frame', false ) ) {
	include_once 'class-wc-od-settings-time-frame.php';
}

if ( ! class_exists( 'WC_OD_Settings_Delivery_Days_Time_Frame', false ) ) {
	/**
	 * WC_OD_Settings_Delivery_Days_Time_Frame class.
	 */
	class WC_OD_Settings_Delivery_Days_Time_Frame extends WC_OD_Settings_Time_Frame {

		/**
		 * Settings Form ID.
		 *
		 * @var String
		 */
		public $id = 'delivery_days';

		/**
		 * Constructor.
		 *
		 * @since 1.6.0
		 */
		public function __construct() {
			parent::__construct( 'new' );
		}

		/**
		 * Initialise form fields.
		 *
		 * @since 1.6.0
		 */
		public function init_form_fields() {
			parent::init_form_fields();

			$delivery_days = wc_od_get_delivery_days();
			$default       = array_map( 'strval', $delivery_days->where( 'enabled', 'yes' )->keys() );

			$this->form_fields['delivery_days'] = array(
				'title'             => __( 'Delivery days', 'woocommerce-order-delivery' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'css'               => 'width: 400px;',
				'description'       => __( 'Choose the delivery days in which this time frame is available.', 'woocommerce-order-delivery' ),
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => wc_od_get_week_days(),
				'default'           => $default,
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select delivery days', 'woocommerce-order-delivery' ),
				),
			);
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.6.0
		 */
		public function init_settings() {
			$this->settings = $this->get_form_fields_defaults();
		}

		/**
		 * Outputs the settings screen heading.
		 *
		 * @since 1.6.0
		 */
		public function output_heading() {
			echo '<h2>';
			echo esc_html_x( 'Delivery days > Add Time Frame', 'time frame settings page title', 'woocommerce-order-delivery' );
			wc_back_link( __( 'Return to delivery settings', 'woocommerce-order-delivery' ), wc_od_get_settings_url() );
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
			if ( $this->has_errors() ) {
				return;
			}

			wp_safe_redirect( wc_od_get_settings_url() );
			exit;
		}

		/**
		 * Sanitize the settings before save the option.
		 *
		 * @since 1.6.0
		 *
		 * @param array $settings The settings to sanitize.
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			$settings = parent::sanitized_fields( $settings );

			// Insert the time frame settings into the 'delivery_days' setting.
			$delivery_days = wc_od_get_delivery_days();

			$chosen_delivery_days = $settings['delivery_days'];

			unset( $settings['delivery_days'] );

			foreach ( $chosen_delivery_days as $weekday ) {
				$delivery_day = $delivery_days->get( $weekday );
				$time_frames  = $delivery_day->get_time_frames();

				$time_frames->add( $settings );
			}

			return $delivery_days->to_array();
		}

		/**
		 * Validates the 'delivery_days' field.
		 *
		 * @since 1.6.0
		 *
		 * @param string $key   Field key.
		 * @param mixed  $value Posted Value.
		 * @return array An array with the delivery days.
		 */
		public function validate_delivery_days_field( $key, $value ) {
			$delivery_days = $this->validate_array_field( $key, $value );

			return array_map( 'intval', $delivery_days );
		}
	}
}
