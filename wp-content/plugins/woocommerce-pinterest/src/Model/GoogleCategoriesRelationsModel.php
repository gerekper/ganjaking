<?php


namespace Premmerce\WooCommercePinterest\Model;

use Premmerce\PrimaryCategory\Model\Model as PrimaryCategoryModel;
use Premmerce\WooCommercePinterest\PinterestPluginUtils;
use \WC_Product;

/**
 * Class GoogleCategoriesRelationsModel
 *
 * @package Premmerce\WooCommercePinterest\Model
 *
 * This class is responsible for relations between Google Merchant Categories Taxonomy and Woocommerce product categories.
 */
class GoogleCategoriesRelationsModel extends AbstractModel {

	protected $table = 'woocommerce_pinterest_google_categories_mapping';

	/**
	 * GoogleCategoriesModel instance
	 *
	 * @var GoogleCategoriesModel
	 */
	private $categoriesModel;

	/**
	 * GoogleCategoriesRelationsModel constructor.
	 *
	 * @param GoogleCategoriesModel $categoriesModel
	 */
	public function __construct( GoogleCategoriesModel $categoriesModel) {
		parent::__construct();

		$this->categoriesModel = $categoriesModel;
	}

	/**
	 * Return Google category id by product
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public function getGoogleCategoryIdByProduct( WC_Product $product) {
		$woocommerceProductCategory = $this->getProductCategoryIdToGetGoogleCategoryFrom($product);

		return $this->getGoogleCategoryIdByWcCategoryId($woocommerceProductCategory);

	}

	/**
	 * Get product category id which will be used to get Google category id
	 *
	 * @param WC_Product $product
	 * @return int
	 */
	public function getProductCategoryIdToGetGoogleCategoryFrom( WC_Product $product) {
		$productToGetCategoryFrom = $product->is_type('variation') ? wc_get_product($product->get_parent_id()) : $product;

		$categoryId = $productToGetCategoryFrom->get_meta(PrimaryCategoryModel::PRIMARY_CATEGORY_META_FIELD_KEY);

		if (! $categoryId && PinterestPluginUtils::isYoastActive()) {
			$yoastPrimaryCategory = new \WPSEO_Primary_Term('product_cat', $productToGetCategoryFrom->get_id() );
			$categoryId           = $yoastPrimaryCategory->get_primary_term();
		}

		if (! $categoryId) {
			$productCategories = wc_get_product_term_ids($productToGetCategoryFrom->get_id(), 'product_cat');
			$categoryId = reset($productCategories);
		}

		return (int) $categoryId;
	}

	/**
	 * Return Google categories chain by WC category
	 *
	 * @param int $wcCategoryId
	 *
	 * @return array
	 */
	public function getCategoriesChainByWoocommerceCategory( $wcCategoryId) {
		$googleCategoryId = $this->getGoogleCategoryIdByWcCategoryId($wcCategoryId);

		$categoryAncestorsChain = $this->categoriesModel->getCategoryAncestorsChain($googleCategoryId);

		return array_filter($categoryAncestorsChain);
	}

	/**
	 * Return Google category id by WC category id
	 *
	 * @param $wcCategoryId
	 *
	 * @return int
	 */
	private function getGoogleCategoryIdByWcCategoryId( $wcCategoryId) {
		return (int) $this->filterByWcCategoryId($wcCategoryId)
			->get('google_category', self::TYPE_VAR);
	}

	/**
	 * Filter rows by WC category id
	 *
	 * @param $wcCategoryId
	 *
	 * @return GoogleCategoriesRelationsModel
	 */
	private function filterByWcCategoryId( $wcCategoryId) {
		$this->where(array('woocommerce_category' => $wcCategoryId));

		return $this;
	}

	/**
	 * Filter rows by WC categories array
	 *
	 * @param array $wcCategoriesIds
	 *
	 * @return GoogleCategoriesRelationsModel
	 *
	 * @throws PinterestModelException
	 */
	private function filterByWcCategoriesArray( array $wcCategoriesIds) {
		$this->in('woocommerce_category', $wcCategoriesIds);

		return $this;
	}

	/**
	 * Update WC category-Google category relations
	 *
	 * @param array $categoriesRelations
	 *
	 * @throws PinterestModelException
	 */
	public function updateCategoriesRelations( array $categoriesRelations) {
		$fields = $this->getCategoriesRelationsFields();

		$relations = $this->sortRelationsToDeleteOrUpdate($categoriesRelations);

		if ($relations['update']) {
			$this->replaceMultiple($fields, $relations['update']);
		}

		$idsToDelete = array_map(function ( $relation) {
			return $relation['woocommerce_category'];
		}, $relations['delete']);
		
		$this->filterByWcCategoriesArray($idsToDelete)->deleteFiltered();
	}

	/**
	 * Get Wc categories-Google categories relations table fields names and types
	 *
	 * @return array
	 */
	private function getCategoriesRelationsFields() {
		return array(
			'id' => '%d',
			'google_category' => '%d',
			'woocommerce_category' => '%d'
		);
	}

	/**
	 * Sort categories relations from user form to update or delete
	 *
	 * @param array $categoriesRelations
	 *
	 * @return array
	 */
	private function sortRelationsToDeleteOrUpdate( array $categoriesRelations) {
		$relations = array(
			'update' => array(),
			'delete' => array()
		);

		foreach ($categoriesRelations as $wcCategory => $googleCategory) {
			$relationArray = array(
				'id' => '',
				'google_category' => $googleCategory,
				'woocommerce_category' => $wcCategory
			);

			$key = $googleCategory ? 'update' : 'delete';

			array_push($relations[$key], $relationArray);
		}

		return $relations;
	}
}
