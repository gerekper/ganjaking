<?php
/**
 * Manage the subscriptions settings.
 *
 * @package WC_OD/Subscriptions
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Subscriptions_Settings class.
 */
class WC_OD_Subscriptions_Settings {

	/**
	 * Constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_filter( 'wc_od_defaults', array( $this, 'get_defaults' ) );

		if ( is_admin() ) {
			add_filter( 'wc_od_shipping_settings', array( $this, 'register_settings' ) );
			add_filter( 'wc_od_system_status_report_settings', array( $this, 'system_status_report_settings' ) );
		}
	}

	/**
	 * Gets the default values for the subscriptions settings.
	 *
	 * @since 1.4.0
	 *
	 * @param array $defaults The default settings values.
	 * @return array The default settings.
	 */
	public function get_defaults( $defaults ) {
		return array_merge(
			$defaults,
			array(
				'subscriptions_limit_to_billing_interval' => 'yes',
				'subscriptions_renewal_delivery_option'   => 'first',
				'subscriptions_renewal_same_weekday'      => 'no',
			)
		);
	}

	/**
	 * Register the subscription settings.
	 *
	 * @since 1.4.0
	 *
	 * @param array $settings The settings.
	 * @return array
	 */
	public function register_settings( $settings ) {
		$instance = WC_OD()->settings();

		/**
		 * Filters the subscription settings.
		 *
		 * @since 1.4.0
		 *
		 * @param array $subscription_settings An array with the subscription settings.
		 */
		$subscription_settings = apply_filters(
			'wc_od_subscription_settings',
			array(
				array(
					'id'    => 'subscription_options',
					'title' => __( 'Subscription Options', 'woocommerce-order-delivery' ),
					'desc'  => __( 'Set up the delivery of the subscriptions and their renewals.', 'woocommerce-order-delivery' ),
					'type'  => 'title',
				),
				array(
					'id'       => 'wc_od_subscriptions_limit_to_billing_interval',
					'title'    => __( 'Limit to billing interval', 'woocommerce-order-delivery' ),
					'desc'     => __( "Limit the delivery dates according to the subscription's billing interval", 'woocommerce-order-delivery' ),
					'desc_tip' => __( 'It will prevent the customer from moving the order delivery to the next renewal.', 'woocommerce-order-delivery' ),
					'type'     => 'checkbox',
					'default'  => $instance->get_default( 'subscriptions_limit_to_billing_interval' ),
				),
				array(
					'id'       => 'wc_od_subscriptions_renewal_delivery_option',
					'title'    => __( 'On order renewal', 'woocommerce-order-delivery' ),
					'desc_tip' => __( 'Choose the option to assign the delivery details on order renewal.', 'woocommerce-order-delivery' ),
					'type'     => 'radio',
					'default'  => $instance->get_default( 'subscriptions_renewal_delivery_option' ),
					'options'  => array(
						'first'    => __( 'Assign the first available delivery date', 'woocommerce-order-delivery' ),
						'interval' => __( "Keep the subscription's billing interval between delivery dates", 'woocommerce-order-delivery' ),
					),
				),
				array(
					'id'      => 'wc_od_subscriptions_renewal_same_weekday',
					'title'   => __( 'Same weekday', 'woocommerce-order-delivery' ),
					'desc'    => __( 'Assign the same weekday as the previous order delivery date', 'woocommerce-order-delivery' ),
					'type'    => 'checkbox',
					'default' => $instance->get_default( 'subscriptions_renewal_same_weekday' ),
				),
				array(
					'id'   => 'subscription_options',
					'type' => 'sectionend',
				),
			)
		);

		return array_merge( $settings, $subscription_settings );
	}

	/**
	 * Filters the settings to include in the System Status Report.
	 *
	 * @since 2.3.0
	 *
	 * @param array $settings An array with the settings data.
	 * @return array
	 */
	public function system_status_report_settings( $settings ) {
		$instance = WC_OD()->settings();

		return array_merge(
			$settings,
			array(
				'subscriptions_limit_to_billing_interval' => array(
					'key'   => 'Limit to billing interval',
					'label' => __( 'Limit to billing interval', 'woocommerce-order-delivery' ),
					'value' => $instance->get_setting( 'subscriptions_limit_to_billing_interval' ),
					'type'  => 'bool',
				),
				'subscriptions_renewal_delivery_option'   => array(
					'key'   => 'On order renewal',
					'label' => __( 'On order renewal', 'woocommerce-order-delivery' ),
					'value' => $instance->get_setting( 'subscriptions_renewal_delivery_option' ),
				),
				'subscriptions_renewal_same_weekday'      => array(
					'key'   => 'Same weekday',
					'label' => __( 'Same weekday', 'woocommerce-order-delivery' ),
					'value' => $instance->get_setting( 'subscriptions_renewal_same_weekday' ),
					'type'  => 'bool',
				),
			)
		);
	}
}

return new WC_OD_Subscriptions_Settings();
