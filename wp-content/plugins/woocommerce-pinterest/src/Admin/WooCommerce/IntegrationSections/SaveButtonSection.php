<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

/**
 * Class SaveButtonSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Save Button settings section fields on settings page
 */
class SaveButtonSection implements IntegrationSectionInterface {


	public function getTitle() {
		return __('Save button settings', 'woocommerce-pinterest');


	}

	public function getSlug() {
		return 'save_button_section';
	}

	public function getFields() {
		return array(
			'save_button_product' => array(
				'type' => 'checkbox',
				'title' => __('Product page', 'woocommerce-pinterest'),
				'label' => __('Enable on the product page', 'woocommerce-pinterest'),
				'description' => __(
					'This option displays a Save button on your products’ images and your customers can save them to their Pinterest boards.',
					'woocommerce-pinterest'
				),
			),

			'save_button_all' => array(
				'type' => 'checkbox',
				'title' => __('All pages', 'woocommerce-pinterest'),
				'label' => __('Enable on all pages', 'woocommerce-pinterest')
			)
		);
	}
}
