<?php
/**
 * Settings: Shipping Methods.
 *
 * @package WC_OD/Traits
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait for including the shipping methods' fields in settings forms.
 */
trait WC_OD_Settings_Shipping_Methods {

	/**
	 * Gets the shipping-methods settings fields.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	protected function get_shipping_methods_fields() {
		return array(
			'shipping_methods_option'     => array(
				'title'    => __( 'Shipping methods', 'woocommerce-order-delivery' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'width: 400px;',
				'desc_tip' => true,
				'options'  => array(
					''           => __( 'All shipping methods', 'woocommerce-order-delivery' ),
					'all_except' => __( 'All shipping methods, except&hellip;', 'woocommerce-order-delivery' ),
					'specific'   => __( 'Only specific shipping methods', 'woocommerce-order-delivery' ),
				),
			),
			'all_except_shipping_methods' => array(
				'title' => __( 'All shipping methods, except&hellip;', 'woocommerce-order-delivery' ),
				'type'  => 'shipping_methods',
			),
			'specific_shipping_methods'   => array(
				'title' => __( 'Only specific shipping methods', 'woocommerce-order-delivery' ),
				'type'  => 'shipping_methods',
			),
		);
	}

	/**
	 * Generates the HTML for a 'shipping_methods' field.
	 *
	 * @since 1.6.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 * @return string
	 */
	public function generate_shipping_methods_html( $key, $data ) {
		$defaults = array(
			'type'              => 'multiselect',
			'class'             => 'wc-enhanced-select-nostd',
			'css'               => 'width: 400px;',
			'desc_tip'          => true,
			'options'           => wc_od_get_shipping_methods_choices(),
			'custom_attributes' => array(
				'data-placeholder' => __( 'Select shipping methods', 'woocommerce-order-delivery' ),
			),
		);

		$data = wp_parse_args( $data, $defaults );

		return $this->generate_multiselect_html( $key, $data );
	}

	/**
	 * Validates a 'shipping_methods' field.
	 *
	 * @since 1.6.0
	 *
	 * @param string $key   Field key.
	 * @param mixed  $value Posted Value.
	 * @return array An array with the shipping methods.
	 */
	public function validate_shipping_methods_field( $key, $value ) {
		return $this->validate_array_field( $key, $value );
	}

	/**
	 * Sanitizes the 'shipping methods' fields.
	 *
	 * @since 1.6.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	protected function sanitize_shipping_methods_fields( $settings ) {
		if ( ! isset( $settings['shipping_methods_option'] ) ) {
			return $settings;
		}

		$settings['shipping_methods'] = array();

		if ( ! empty( $settings['shipping_methods_option'] ) ) {
			$setting_key = "{$settings['shipping_methods_option']}_shipping_methods";

			$settings['shipping_methods'] = ( ! empty( $settings[ $setting_key ] ) ? $settings[ $setting_key ] : array() );
		}

		unset( $settings['specific_shipping_methods'], $settings['all_except_shipping_methods'] );

		return $settings;
	}
}
