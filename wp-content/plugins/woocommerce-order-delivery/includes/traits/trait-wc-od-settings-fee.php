<?php
/**
 * Settings: Fee
 *
 * @package WC_OD/Traits
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait for including the fee fields in settings forms.
 */
trait WC_OD_Settings_Fee {

	/**
	 * Gets the fee-related setting fields.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_fee_fields() {
		$fields = array(
			'fee_amount' => array(
				'title'       => __( 'Fee amount', 'woocommerce-order-delivery' ),
				'type'        => 'price',
				'description' => __( 'Enter a fixed amount to apply as a fee.', 'woocommerce-order-delivery' ),
				'desc_tip'    => true,
				'placeholder' => 0,
			),
			'fee_label'  => array(
				'title'       => __( 'Fee label', 'woocommerce-order-delivery' ),
				'type'        => 'text',
				'description' => __( 'The fee label to use in the order details.', 'woocommerce-order-delivery' ),
				'desc_tip'    => true,
				'placeholder' => __( 'Delivery fee', 'woocommerce-order-delivery' ),
			),
		);

		if ( wc_tax_enabled() ) {
			$fields = array_merge(
				$fields,
				array(
					'fee_tax_status' => array(
						'title'       => __( 'Fee tax status', 'woocommerce-order-delivery' ),
						'type'        => 'select',
						'description' => __( 'Define whether the fee is taxable.', 'woocommerce-order-delivery' ),
						'desc_tip'    => true,
						'default'     => 'none',
						'options'     => array(
							'none'    => _x( 'None', 'Tax status', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
							'taxable' => __( 'Taxable', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
						),
					),
					'fee_tax_class'  => array(
						'title'       => __( 'Fee tax class', 'woocommerce-order-delivery' ),
						'type'        => 'select',
						'description' => __( 'Choose a tax class for the fee. Tax classes are used to apply different tax rates.', 'woocommerce-order-delivery' ),
						'desc_tip'    => true,
						'options'     => wc_get_product_tax_class_options(),
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Sanitizes the fee-related setting fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	protected function sanitize_fee_fields( $settings ) {
		if ( isset( $settings['fee_tax_status'] ) && 'none' === $settings['fee_tax_status'] ) {
			$settings['fee_tax_class'] = '';
		}

		return $settings;
	}
}
