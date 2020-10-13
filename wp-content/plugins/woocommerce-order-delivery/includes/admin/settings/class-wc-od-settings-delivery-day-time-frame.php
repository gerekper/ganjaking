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

if ( class_exists( 'WC_OD_Settings_Delivery_Day_Time_Frame', false ) ) {
	return;
}

/**
 * WC_OD_Settings_Delivery_Day_Time_Frame class.
 */
class WC_OD_Settings_Delivery_Day_Time_Frame extends WC_OD_Settings_Time_Frame {

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
		$this->id     = 'delivery_days';
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

			// Copy the value of number_of_orders.
			$settings['number_of_orders'] = $delivery_day->get_number_of_orders();

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
	 * Gets the form title.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return str_replace(
			array(
				'[delivery_day]',
				'[time_frame]',
			),
			array(
				wc_od_get_weekday( $this->day_id ),
				( $this->is_new() ? __( 'Add Time Frame', 'woocommerce-order-delivery' ) : $this->settings['title'] ),
			),
			esc_html_x( 'Delivery days > [delivery_day] > [time_frame]', 'time frame settings page title', 'woocommerce-order-delivery' )
		);
	}

	/**
	 * Gets the form description.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_description() {
		return _x( 'Edit the time frame settings.', 'settings page description', 'woocommerce-order-delivery' );
	}

	/**
	 * Outputs the backlink in the heading.
	 *
	 * @since 1.7.0
	 */
	public function output_heading_backlink() {
		wc_back_link(
			__( 'Return to the delivery day settings', 'woocommerce-order-delivery' ),
			wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $this->day_id ) )
		);
	}

	/**
	 * Saves the settings.
	 *
	 * @since 1.7.0
	 *
	 * @return bool was anything saved?
	 */
	public function save() {
		$settings = $this->sanitized_fields( $this->settings );

		// Insert the time frame settings into the 'delivery_days' setting.
		$delivery_days = wc_od_get_delivery_days();
		$time_frames   = $delivery_days->get( $this->day_id )->get_time_frames();

		if ( $this->is_new() ) {
			$time_frames->add( $settings );
		} else {
			$time_frames->set( $this->frame_id, $settings );
		}

		$saved = update_option( $this->get_option_key(), $delivery_days->to_array() );

		// Reset the delivery day.
		$this->delivery_day = null;

		/** @var WC_OD_Delivery_Cache $delivery_cache */
		$delivery_cache = WC_OD_Delivery_Cache::instance();
		$delivery_cache->remove_order_cache();

		return $saved;
	}

	/**
	 * Redirects to a different page after saving the settings if necessary.
	 *
	 * @since 1.5.0
	 */
	public function maybe_redirect() {
		if ( ! $this->is_new() || $this->has_errors() ) {
			return;
		}

		wp_safe_redirect( wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $this->day_id ) ) );
		exit;
	}

	/**
	 * Sanitizes the settings.
	 *
	 * @since 1.5.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public function sanitized_fields( $settings ) {
		// Backward compatibility.
		unset( $settings['delivery_days'] );

		return parent::sanitized_fields( $settings );
	}
}
