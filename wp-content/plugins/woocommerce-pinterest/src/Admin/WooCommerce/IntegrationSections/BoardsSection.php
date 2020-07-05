<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

/**
 * Class BoardsSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Boards section fields on settings page
 */
class BoardsSection implements IntegrationSectionInterface {


	public function getTitle() {
		return __('Pinterest boards settings');
	}

	public function getSlug() {
		return 'pinterest_boards_section';
	}

	public function getFields() {
		return array(
			'category_board_table' => array(
				'type' => 'category_board_table',
				'desc_tip' => __(
					'This table represents the relation between your product categories and Pinterest boards.',
					'woocommerce-pinterest'
				),
			),
		);

	}
}
