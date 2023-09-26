<?php
/**
 * Settings: Delivery Day Time Frame.
 *
 * @package WC_OD/Admin/Settings
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Settings_Delivery_Day_Time_Frame', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Settings_Time_Frame', false ) ) {
	include_once 'class-wc-od-settings-time-frame.php';
}

if ( ! trait_exists( 'WC_OD_Settings_Fee' ) ) {
	require_once WC_OD_PATH . 'includes/traits/trait-wc-od-settings-fee.php';
}

/**
 * WC_OD_Settings_Delivery_Day_Time_Frame class.
 */
class WC_OD_Settings_Delivery_Day_Time_Frame extends WC_OD_Settings_Time_Frame {

	use WC_OD_Settings_Fee;

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
	 * Time frame object.
	 *
	 * @var WC_OD_Time_Frame
	 */
	protected $time_frame;

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

		$this->time_frame = wc_od_get_time_frame( 'new' === $frame_id ? 0 : $frame_id );

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
	 * Initialise form fields.
	 *
	 * @since 2.0.0
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$this->form_fields = array_merge(
			$this->form_fields,
			$this->get_fee_fields()
		);
	}

	/**
	 * Initialise Settings.
	 *
	 * @since 1.5.0
	 */
	public function init_settings() {
		$settings = $this->get_form_fields_defaults();

		if ( $this->is_new() ) {
			$delivery_day = $this->get_delivery_day();

			// Inherit the settings from the delivery day.
			$inherit_settings   = array_keys( $settings );
			$inherit_settings[] = 'shipping_methods';

			$settings = array_merge( $settings, $delivery_day->get_props( $inherit_settings ) );
		} else {
			$settings = array_merge( $settings, $this->time_frame->get_data_without( array( 'id', 'meta_data' ) ) );
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
				( $this->is_new() ? __( 'Add time frame', 'woocommerce-order-delivery' ) : $this->settings['title'] ),
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
	 * Sanitizes the settings.
	 *
	 * @since 1.5.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public function sanitized_fields( $settings ) {
		$settings = $this->sanitize_fee_fields( $settings );

		return parent::sanitized_fields( $settings );
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

		$this->time_frame->set_props( $settings );

		$delivery_day = $this->get_delivery_day();
		$delivery_day->add_time_frame( $this->time_frame );
		$delivery_day->save();

		return true;
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
}
