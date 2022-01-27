<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

/**
 * Class PinterestConnectionSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Pinterest Connection section fields on settings page
 */
class PinterestAccountSection implements IntegrationSectionInterface {


	public function getTitle() {
		return __('Account', 'woocommerce-pinterest');
	}

	public function getSlug() {
		return 'account_section';
	}

	public function getFields() {
		return array(
			'v3_connection_button' => array(
				'title' => __('API connection', 'woocommerce-pinterest'),
				'type' => 'v3_connection_button',
				'desc_tip' => __('Allows you to create, update or delete pins, verify the domain and connect Pinterest Track Conversions',
					'woocommerce-pinterest')
			)
		);
	}
}
