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

if ( class_exists( 'WC_OD_Settings_Delivery_Days_Time_Frame', false ) ) {
	return;
}

/**
 * WC_OD_Settings_Delivery_Days_Time_Frame class.
 */
class WC_OD_Settings_Delivery_Days_Time_Frame extends WC_OD_Settings_Time_Frame {

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {
		$this->id = 'delivery_days';

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
	 * Gets the form title.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_title() {
		return _x( 'Delivery days > Add Time Frame', 'time frame settings page title', 'woocommerce-order-delivery' );
	}

	/**
	 * Gets the form description.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_form_description() {
		return _x( 'Add time frames to multiple delivery days at once.', 'settings page description', 'woocommerce-order-delivery' );
	}

	/**
	 * Outputs the backlink in the heading.
	 *
	 * @since 1.7.0
	 */
	public function output_heading_backlink() {
		wc_back_link( _x( 'Return to the shipping options', 'settings back link label', 'woocommerce-order-delivery' ), wc_od_get_settings_url() );
	}

	/**
	 * Redirects to a different page after saving the settings if necessary.
	 *
	 * @since 1.5.0
	 */
	public function maybe_redirect() {
		if ( $this->has_errors() ) {
			return;
		}

		wp_safe_redirect( wc_od_get_settings_url() );
		exit;
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

	/**
	 * Saves the settings.
	 *
	 * @since 1.7.0
	 *
	 * @return bool was anything saved?
	 */
	public function save() {
		$settings = $this->sanitized_fields( $this->settings );

		// Add the time frame to all the days included in the 'delivery_days' setting.
		$delivery_days = wc_od_get_delivery_days();
		$chosen_days   = $settings['delivery_days'];

		unset( $settings['delivery_days'] );

		foreach ( $chosen_days as $weekday ) {
			$delivery_day = $delivery_days->get( $weekday );
			$time_frames  = $delivery_day->get_time_frames();

			$time_frames->add( $settings );
		}

		return update_option( $this->get_option_key(), $delivery_days->to_array() );
	}
}
