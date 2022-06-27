<?php
/**
 * Settings: Lockout
 *
 * @package WC_OD/Traits
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait for including the lockout fields in settings forms.
 */
trait WC_OD_Settings_Lockout {

	/**
	 * Gets the lockout-related setting fields.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	protected function get_lockout_fields() {
		return array(
			'number_of_orders' => array(
				'title'             => __( 'Number of orders', 'woocommerce-order-delivery' ),
				'description'       => __( '0 means that there is no limit of orders.', 'woocommerce-order-delivery' ),
				'desc_tip'          => __( 'Maximum number of orders that can be delivered on the day.', 'woocommerce-order-delivery' ),
				'type'              => 'number',
				'default'           => 0,
				'css'               => 'width: 50px;',
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),
		);
	}
}
