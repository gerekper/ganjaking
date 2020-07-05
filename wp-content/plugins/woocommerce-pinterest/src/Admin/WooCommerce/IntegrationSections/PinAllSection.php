<?php namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

/**
 * Class PinAll
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 */
class PinAllSection implements IntegrationSectionInterface {


	public function getTitle() {
		return __( 'Bulk pinning', 'woocommerce-pinterest' );
	}

	public function getSlug() {
		return 'pinterest_pin_all_section';
	}

	public function getFields() {
		return array(
			'pin_all_images' => array(
				'title'             => __( 'Pin full product gallery', 'woocommerce-pinterest' ),
				'type'              => 'checkbox',
				'label'             => __( 'Pin full product gallery', 'woocommerce-pinterest' ),
				'desc_tip'          => __( 'Non-boarded products will be skipped', 'woocommerce-pinterest' ),
				'custom_attributes' => array(
					'data-pin-full-product-gallery-checkbox' => true
				)
			),
			'pin_all_button' => array(
				'type'  => 'pin_all_button',
				'title' => __( 'Pin products categories', 'woocommerce-pinterest' ),
			),
		);

	}
}
