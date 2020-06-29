<?php
/**
 * Manage the subscriptions settings.
 *
 * @package WC_OD/Subscriptions
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Subscriptions_Settings' ) ) {
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
			$defaults['subscriptions_limit_to_billing_interval'] = 'yes';

			return $defaults;
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
			return array_merge(
				array_slice( $settings, 0, -3 ),
				apply_filters( 'wc_od_subscription_settings', array(
					array(
						'id'       => wc_od_maybe_prefix( 'subscriptions_limit_to_billing_interval' ),
						'title'    => __( 'Limit subscription orders to the billing interval', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Limit the available dates in the calendar according to the periodicity of the subscription', 'woocommerce-order-delivery' ),
						'desc_tip' => __( 'It will avoid the customer postponing a subscription order to the next billing interval (weekly, monthly… ).', 'woocommerce-order-delivery' ),
						'type'     => 'checkbox',
						'default'  => WC_OD()->settings()->get_default( 'subscriptions_limit_to_billing_interval' ),
					),
				) ),
				array_slice( $settings, -3)
			);
		}

	}
}

return new WC_OD_Subscriptions_Settings();
