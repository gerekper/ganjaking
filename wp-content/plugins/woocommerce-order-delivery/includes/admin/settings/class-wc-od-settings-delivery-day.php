<?php
/**
 * Settings: Delivery Day.
 *
 * @package WC_OD/Admin/Settings
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Settings_API', false ) ) {
	include_once 'abstract-class-wc-od-settings-api.php';
}

if ( ! class_exists( 'WC_OD_Settings_Delivery_Day', false ) ) {
	/**
	 * WC_OD_Settings_Delivery_Day class.
	 */
	class WC_OD_Settings_Delivery_Day extends WC_OD_Settings_API {

		/**
		 * Settings Form ID.
		 *
		 * @var String
		 */
		public $id = 'delivery_days';

		/**
		 * The day ID.
		 *
		 * @var int
		 */
		public $day_id;

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
		 * @param int $day_id The delivery day ID.
		 */
		public function __construct( $day_id ) {
			$this->day_id = $day_id;

			parent::__construct();
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
		 * Initialise form fields.
		 *
		 * @since 1.5.0
		 */
		public function init_form_fields() {
			$delivery_day = $this->get_delivery_day();

			$this->form_fields = array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'woocommerce-order-delivery' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this day for delivery', 'woocommerce-order-delivery' ),
					'default' => $delivery_day['enabled'],
				),
			);

			if ( ! $delivery_day->has_time_frames() ) {
				$this->form_fields = array_merge( $this->form_fields, $this->get_shipping_methods_fields() );

				$this->form_fields['shipping_methods_option']['description'] = __( 'Choose the available shipping methods for this delivery day.', 'woocommerce-order-delivery' );
			}

			$this->form_fields['time_frames'] = array(
				'title'    => __( 'Time frames', 'woocommerce-order-delivery' ),
				'type'     => 'wc_od_table',
				'desc'     => __( 'Define the time frames for this delivery day.', 'woocommerce-order-delivery' ),
				'desc_tip' => true,
			);
		}

		/**
		 * Enqueue the settings scripts.
		 *
		 * @since 1.5.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'wc-od-admin-settings-delivery-day', WC_OD_URL . 'assets/js/admin/settings-delivery-day.js', array( 'jquery' ), WC_OD_VERSION, true );
		}

		/**
		 * Initialise Settings.
		 *
		 * @since 1.5.0
		 */
		public function init_settings() {
			$delivery_day = $this->get_delivery_day();
			$settings     = array_merge( $this->get_form_fields_defaults(), $delivery_day->to_array() );

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

			echo '<h2>';
			/* translators: %s: week day name */
			printf( esc_html_x( 'Delivery days > %s', 'delivery day settings page title', 'woocommerce-order-delivery' ), $weekdays[ $this->day_id ] ); // WPCS: XSS ok.
			wc_back_link( __( 'Return to shipping options', 'woocommerce-order-delivery' ), wc_od_get_settings_url() );
			echo '</h2>';

			echo wpautop( wp_kses_post( __( 'Edit the delivery day settings.', 'woocommerce-order-delivery' ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
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
		}

		/**
		 * Merge the day settings with the rest of days before save the option.
		 *
		 * @since 1.5.0
		 *
		 * @param array $settings The sanitized settings.
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			$settings = parent::sanitized_fields( $settings );

			$delivery_days = wc_od_get_delivery_days();

			$delivery_days->set( $this->day_id, $settings );

			return $delivery_days->to_array();
		}
	}
}
