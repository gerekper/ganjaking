<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;


/**
 * Class TagsSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Tags section fields on settings page
 */
class TagsSection implements IntegrationSectionInterface {

	/**
	 * PinterestTagsController instance
	 *
	 * @var PinterestTagsController
	 */
	private $tagsController;

	/**
	 * TagsSection constructor.
	 *
	 * @param PinterestTagsController $tagsController
	 */
	public function __construct( PinterestTagsController $tagsController) {

		$this->tagsController = $tagsController;
	}

	public function getTitle() {
		return __('Pinterest hashtags settings', 'woocommerce-pinterest');
	}

	public function getSlug() {
		return 'pinterest_tags_section';
	}

	public function getFields() {
		return array(
			'tags_fetching_strategy' => array(
				'title' => __('Pinterest hashtags sources', 'woocommerce-pinterest'),
				'type' => 'multiselect',
				'options' => $this->tagsController->getTagsSourcesList(),
				'desc_tip' => __('Select sources for Pinterest hashtags. The order of hashtags does matter. Sources without hashtags will be skipped.',
					'woocommerce-pinterst')
			),

			'main_pinterest_tag' => array(
				'title' => __('Main hashtag', 'woocommerce-pinterest'),
				'type' => 'text'
			)
		);
	}
}
