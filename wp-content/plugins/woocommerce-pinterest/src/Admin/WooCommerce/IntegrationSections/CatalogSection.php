<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

use \DateTime;
/**
 * Class CatalogSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Catalog section fields on settings page
 */
class CatalogSection implements IntegrationSectionInterface {


	public function getTitle() {
		return __('Pinterest Сatalog settings');
	}

	public function getSlug() {
		return 'pinterest_catalog_section';
	}

	public function getFields() {
		return array(
			'pinterest_catalog_table' => array(
				'type' => 'catalog_table',
				'desc_tip' => __(
					'This table represents the relation between your product categories and Google Merchants categories',
					'woocommerce-pinterest'
				)
			),

			'pinterest_generate_catalog_fields' => array(
				'title' => __('Generate catalog', 'woocommerce-pinterest'),
				'type' => 'pinterest_generate_catalog_fields',
			),

			'enable_catalog_auto_updating' => array(
				'title' => __('Catalog updating', 'woocommerce-pinterest'),
				'type' => 'checkbox',
				'label' => 'Regenerate catalog automatically'
			),

			'pinterest_catalog_updating_frequency' => array(
				'title' => __('Scheduling of catalog updates', 'woocommerce-pinterest'),
				'type' => 'pinterest_catalog_updating_frequency',
				'desc_tip' => __('How often do you want the catalog to update automatically?', 'woocommerce-pinterest'),
				'sanitize_callback' => array($this, 'sanitizeCatalogUpdatingFrequency')
			)
		);
	}

	/**
	 * Sanitize catalog updating frequency
	 *
	 * @param $postedValue
	 *
	 * @return int
	 *
	 */
	public function sanitizeCatalogUpdatingFrequency( $postedValue) {
		$days = filter_var($postedValue['days'], FILTER_SANITIZE_NUMBER_INT);
		$time = filter_var($postedValue['time'], FILTER_SANITIZE_STRING);

		$valueToSave['days'] = $days;
		$valueToSave['time'] = DateTime::createFromFormat('H:i', $time) ? $time : '00:00';

		return $valueToSave;
	}
}
