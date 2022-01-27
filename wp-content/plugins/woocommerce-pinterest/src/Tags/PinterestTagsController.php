<?php

namespace Premmerce\WooCommercePinterest\Tags;

use Premmerce\PrimaryCategory\Model\Model;
use Premmerce\WooCommercePinterest\Logger\Logger;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \WP_Term;

/**
 * Class PinterestTagsController
 *
 * @package Premmerce\WooCommercePinterest\Tags
 *
 * This class is responsible for managing Pinterest Tags.
 * This is the higher abstraction level created for useful Pinterest Tags getting, setting and mapping to products and categories.
 *
 */
class PinterestTagsController {

	/**
	 * PinterestTagsRepository instance
	 *
	 * @var PinterestTagsRepository
	 */
	private $pinterestTagsRepository;

	/**
	 * PinterestTagsToCategoriesMapper instance
	 *
	 * @var PinterestTagsToCategoriesMapper
	 */
	private $pinterestTagsToCategoriesMapper;

	/**
	 * PrimaryCategory Model instance
	 *
	 * @var Model
	 */
	private $primaryCategoryModel;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * PinterestTagsController constructor.
	 *
	 * @param PinterestTagsRepository $pinterestTagsRepository
	 * @param PinterestTagsToCategoriesMapper $pinterestTagsToCategoriesMapper
	 * @param Model $primaryCategoryModel
	 * @param Logger $logger
	 */
	public function __construct( PinterestTagsRepository $pinterestTagsRepository, PinterestTagsToCategoriesMapper $pinterestTagsToCategoriesMapper, Model $primaryCategoryModel, Logger $logger) {
		$this->pinterestTagsToCategoriesMapper = $pinterestTagsToCategoriesMapper;
		$this->pinterestTagsRepository         = $pinterestTagsRepository;
		$this->primaryCategoryModel            = $primaryCategoryModel;
		$this->logger                          = $logger;
	}

	/**
	 * Get tags for product
	 *
	 * @param int $productId
	 *
	 * @return array
	 *
	 * @throws PinterestException
	 */
	public function getTagsForProduct( $productId) {
		$pinterestIntegration = ServiceContainer::getInstance()->getPinterestIntegration();
		$tagsSources          = (array) $pinterestIntegration->get_option('tags_fetching_strategy');
		$tagsNames            = array();

		foreach ($tagsSources as $source) {
			switch ($source) {
				case 'product':
					$tagsPortion = $this->getTagsNamesByProductWithoutCategories($productId);
					break;

				case 'all_product_categories':
					$tagsPortion = $this->getTagsNamesByProductFromCategoriesOnly($productId);
					break;

				case 'main_tag':
					$tagsPortion = (array) $pinterestIntegration->get_option('main_pinterest_tag');
					break;

				case 'primary_category':
					$categoryId  = $this->primaryCategoryModel->getPrimaryCategoryId($productId);
					$tagsPortion = $this->getTagsNamesByCategory($categoryId);
					break;

				default:
					$tagsPortion = array();
			}

			$tagsNames = array_merge($tagsNames, $tagsPortion);
		}

		return array_unique($tagsNames);
	}

	/**
	 * Get tags names by product without categories
	 *
	 * @param int
	 *
	 * @return string[]
	 *
	 * @throws PinterestException
	 */
	public function getTagsNamesByProductWithoutCategories( $productId) {
		$tagsNames = $this->pinterestTagsRepository
			->setProductId($productId)
			->setIncludeProductCategories(false)
			->setNamesOnly(true)
			->get();

		return $tagsNames;
	}

	/**
	 * Get tags names by product from product categories only
	 *
	 * @param int $productId
	 *
	 * @return string[]
	 *
	 * @throws PinterestException
	 */
	public function getTagsNamesByProductFromCategoriesOnly( $productId) {
		$tagsNames = $this->pinterestTagsRepository
			->setCategoriesIdsFromProduct($productId)
			->setNamesOnly(true)
			->get();

		return $tagsNames;
	}

	/**
	 * Get tags objects by category object
	 *
	 * @param WP_Term $category
	 *
	 * @return WP_Term[]
	 *
	 * @throws PinterestException
	 */
	public function getTagsObjectsByCategory( WP_Term $category) {
		return $this->pinterestTagsRepository
			->setCategoriesIds(array($category->term_id))
			->get();
	}

	/**
	 * Get tags names by category id
	 *
	 * @param $categoryId
	 * @return string[]
	 *
	 * @throws PinterestException
	 */
	public function getTagsNamesByCategory( $categoryId) {
		$tags = array();

		if ( $categoryId) {
			$tags = $this->pinterestTagsRepository
				->setCategoriesIds(array($categoryId))
				->setNamesOnly(true)
				->get();
		}

		return $tags;
	}

	/**
	 * Get tags for ajax search
	 *
	 * @param string $search
	 *
	 * @return array
	 *
	 * @throws PinterestException
	 */
	public function getTagsForAjaxSearch( $search) {
		return $this->pinterestTagsRepository
			->setNameLike($search)
			->get();
	}

	/**
	 * Get all Pinterest tags
	 *
	 * @return WP_Term[]
	 *
	 * @todo check how many terms can be fetched ok
	 *
	 * @throws PinterestException
	 */
	public function getAllPinterestTags() {
		return $this->pinterestTagsRepository->getAll();
	}

	/**
	 * Update product category pinterest tags
	 *
	 * @param int $termId
	 * @param array $tagsIds
	 */
	public function updateProductCategoryPinterestTags( $termId, array $tagsIds) {
		$this->pinterestTagsToCategoriesMapper->setCategoryPinterestTagsIds($termId, $tagsIds);
	}

	/**
	 * Get tags sources list
	 *
	 * @return array
	 */
	public function getTagsSourcesList() {
		$strategies = array(
			'product' => __('Product', 'woocommerce-pinterest'),
			'primary_category' => __('Primary category', 'woocommerce-pinterest'),
			'all_product_categories' => __('All product categories', 'woocommerce-pinterest'),
			'main_tag' => __('Main hashtag', 'woocommerce-pinterest')
		);

		return $strategies;
	}
}
