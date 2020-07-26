<?php
/**
 * Class to handle the plugin settings.
 *
 * @package WC_OD/Classes
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Settings Class.
 */
class WC_OD_Settings extends WC_OD_Singleton {

	/**
	 * Stores the default settings values.
	 *
	 * @since 1.6.0
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Stores the settings values.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		parent::__construct();

		add_action( 'added_option', array( $this, 'updated_setting' ), 10, 2 );
		add_action( 'updated_option', array( $this, 'updated_setting' ), 10, 3 );
	}

	/**
	 * Gets the setting key.
	 *
	 * @since 1.6.0
	 *
	 * @param string $name The setting name.
	 * @return string
	 */
	protected function get_setting_key( $name ) {
		$key = wc_od_no_prefix( $name );

		// Rename the setting key for backward compatibility.
		if ( 'delivery_date_field' === $key ) {
			$key = 'delivery_fields_option';
		}

		return $key;
	}

	/**
	 * Gets the setting option name.
	 *
	 * @since 1.6.0
	 *
	 * @param string $name The setting name.
	 * @return string
	 */
	protected function get_setting_option( $name ) {
		$key = $this->get_setting_key( $name );

		return wc_od_maybe_prefix( $key );
	}

	/**
	 * Gets the default values for the plugin settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array The default settings.
	 */
	public function get_defaults() {
		if ( empty( $this->defaults ) ) {
			$defaults = array(
				'min_working_days'         => 0,
				'shipping_days'            => array(
					array( // Sunday.
						'enabled' => 'no',
						'time'    => '',
					),
					array( // Monday.
						'enabled' => 'yes',
						'time'    => '',
					),
					array( // Tuesday.
						'enabled' => 'yes',
						'time'    => '',
					),
					array( // Wednesday.
						'enabled' => 'yes',
						'time'    => '',
					),
					array( // Thursday.
						'enabled' => 'yes',
						'time'    => '',
					),
					array( // Friday.
						'enabled' => 'yes',
						'time'    => '',
					),
					array( // Saturday.
						'enabled' => 'no',
						'time'    => '',
					),
				),
				'delivery_range'           => array(
					'min' => 1,
					'max' => 10,
				),
				'delivery_ranges'          => array(),
				'checkout_delivery_option' => 'calendar',
				'delivery_days'            => array(
					array( // Sunday.
						'enabled'          => 'no',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
					array( // Monday.
						'enabled'          => 'yes',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
					array( // Tuesday.
						'enabled'          => 'yes',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
					array( // Wednesday.
						'enabled'          => 'yes',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
					array( // Thursday.
						'enabled'          => 'yes',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
					array( // Friday.
						'enabled'          => 'yes',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
					array( // Saturday.
						'enabled'          => 'yes',
						'time_frames'      => array(),
						'shipping_methods' => array(),
					),
				),
				'max_delivery_days'        => 90,
				'enable_local_pickup'      => 'no',
				'shipping_events_index'    => 1,
				'shipping_events'          => array(),
				'delivery_events_index'    => 1,
				'delivery_events'          => array(),
				'delivery_fields_option'   => 'optional',
			);

			/**
			 * Filters the default values for the settings.
			 *
			 * @since 1.0.0
			 *
			 * @param array $defaults The default settings.
			 */
			$this->defaults = apply_filters( 'wc_od_defaults', $defaults );
		}

		return $this->defaults;
	}

	/**
	 * Gets the default value for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The setting name.
	 * @return mixed The default setting value. Null otherwise.
	 */
	public function get_default( $name ) {
		$defaults    = $this->get_defaults();
		$setting_key = $this->get_setting_key( $name );

		return ( isset( $defaults[ $setting_key ] ) ? $defaults[ $setting_key ] : null );
	}

	/**
	 * Gets if the specified setting exists or not.
	 *
	 * @since 1.6.0
	 *
	 * @param string $name The setting name.
	 * @return bool
	 */
	public function has_setting( $name ) {
		return array_key_exists( $this->get_setting_key( $name ), $this->get_defaults() );
	}

	/**
	 * Gets a setting value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name    The setting name.
	 * @param mixed  $default Optional. Override the default value.
	 * @return mixed The setting value.
	 */
	public function get_setting( $name, $default = null ) {
		$setting_key = $this->get_setting_key( $name );

		if ( ! $this->has_setting( $setting_key ) ) {
			return $default;
		}

		if ( ! array_key_exists( $setting_key, $this->settings ) ) {
			// Use the default value defined by the plugin.
			if ( is_null( $default ) ) {
				$default = $this->get_default( $setting_key );
			}

			$value = get_option( $this->get_setting_option( $setting_key ), $default );

			/**
			 * Filters the setting value.
			 *
			 * @since 1.1.0
			 *
			 * @param mixed $value The setting value.
			 */
			$this->settings[ $setting_key ] = apply_filters( "wc_od_get_setting_{$setting_key}", $value );
		}

		return $this->settings[ $setting_key ];
	}

	/**
	 * Updates a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $name  The setting name.
	 * @param mixed $value The setting value.
	 * @return boolean Gets if the setting was updated or not.
	 */
	public function update_setting( $name, $value ) {
		if ( $this->has_setting( $name ) ) {
			return update_option( $this->get_setting_option( $name ), $value );
		}

		return false;
	}

	/**
	 * Fires after an option has been successfully added or updated.
	 *
	 * We use this method to update the $this->settings property with the new value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option    Name of the updated setting.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $new_value Optional. The new option value. Only on updated_option hook.
	 */
	public function updated_setting( $option, $old_value, $new_value = null ) {
		if ( $this->has_setting( $option ) ) {
			$value       = ( 'updated_option' === current_filter() ? $new_value : $old_value );
			$setting_key = $this->get_setting_key( $option );

			// Updates the setting value.
			$this->settings[ $setting_key ] = $value;
		}
	}
}
