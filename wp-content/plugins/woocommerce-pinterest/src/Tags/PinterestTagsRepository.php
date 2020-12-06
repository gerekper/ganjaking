<?php

namespace Premmerce\WooCommercePinterest\Tags;

use Premmerce\WooCommercePinterest\PinterestException;
use \WP_Term;

/**
 * Class TagsRepository
 *
 * @package Premmerce\WooCommercePinterest\Tags
 *
 * This class is responsible for retrieving pinterest tags
 */
class PinterestTagsRepository {

	/**
	 * PinterestTagsToCategoriesMapper instance
	 *
	 * @var PinterestTagsToCategoriesMapper
	 */
	private $pinterestTagsToCategoriesMapper;

	/**
	 * WC product id
	 *
	 * @var int WC product id
	 */
	private $productId;

	/**
	 * Categories ids to get terms ids from
	 *
	 * @var int[] Categories to get terms ids from
	 */
	private $categoriesIds;

	/**
	 * WP term id
	 *
	 * @var int WP term id
	 */
	private $includeProductCategories;

	/**
	 * Name to use for LIKE
	 *
	 * @var string Name to search for
	 */
	private $nameLike;

	/**
	 * Flag including empty
	 *
	 * @var
	 */
	private $includingEmpty;

	/**
	 * Fields
	 *
	 * @var string
	 */
	private $fields;


	/**
	 * PinterestTagsRepository constructor.
	 *
	 * @param PinterestTagsToCategoriesMapper $pinterestTagsToCategoriesMapper
	 */
	public function __construct( PinterestTagsToCategoriesMapper $pinterestTagsToCategoriesMapper) {

		$this->pinterestTagsToCategoriesMapper = $pinterestTagsToCategoriesMapper;

		$this->setDefaults();
	}

	/**
	 * Set defaults to properties
	 *
	 * @return void
	 */
	private function setDefaults() {
		$this->includeProductCategories = false;
		$this->productId                = null;
		$this->categoriesIds            = array();
		$this->includingEmpty           = true;
		$this->fields                   = 'all';
		$this->nameLike                 = '';
	}

	/**
	 * Get All pinterest tags
	 *
	 * @return WP_Term[]
	 *
	 * @throws PinterestException
	 */
	public function getAll() {
		$result = get_terms($this->buildGetAllQueryArgs());

		if (is_wp_error($result)) {
			throw new PinterestException("Terms query failed with message {$result->get_error_message()}");
		}

		return $result;
	}

	/**
	 * Get filtered tags
	 *
	 * @return array
	 *
	 * @throws PinterestException
	 */
	public function get() {
		$terms = array();

		if ($this->productId || $this->nameLike) {
			$terms = $this->doMainTagsQuery();
		}

		if ($this->includingTagsFromCategories()) {
			$terms = array_merge($terms, $this->doTagsFromCategoriesQuery());
		}

		$this->setDefaults();

		return $terms;
	}

	/**
	 * Run main tags query
	 *
	 * @return array
	 *
	 * @throws PinterestException
	 */
	private function doMainTagsQuery() {
		$result = get_terms($this->buildMainTagsQueryArgs());

		if (is_wp_error($result)) {
			throw new PinterestException($result->get_error_message());
		}
		return $result;
	}

	/**
	 * Run query to get tags from categories
	 *
	 * @return array
	 *
	 * @throws PinterestException
	 */
	private function doTagsFromCategoriesQuery() {
		$args = $this->buildTagsByCategoriesQueryArgs();

		$result = $args['include'] ? get_terms ($args) : array();

		if (is_wp_error($args)) {
			throw new PinterestException($result->get_error_message());
		}

		return $result;
	}

	/**
	 * Including tags from categories
	 *
	 * @return bool
	 */
	private function includingTagsFromCategories() {
		return $this->includeProductCategories || $this->categoriesIds;
	}

	/**
	 * Build main tags query args
	 *
	 * @return array
	 */
	private function buildMainTagsQueryArgs() {
		$args               = $this->buildQueryArgsBase();
		$args['object_ids'] = $this->productId;
		$args['name__like'] = $this->nameLike;

		return $args;
	}

	/**
	 * Build get tags by categories query args
	 *
	 * @return array
	 */
	private function buildTagsByCategoriesQueryArgs() {
		$args            = $this->buildQueryArgsBase();
		$args['include'] = $this->getTagsIdsFromCategories($this->getCategoriesIdsForQuery());

		return $args;
	}

	/**
	 * Get categories ids for query
	 *
	 * @return array
	 */
	private function getCategoriesIdsForQuery() {
		$productCategoriesIds = array();

		if ($this->includeProductCategories && $this->productId) {
			$productCategoriesIds = wc_get_product_term_ids($this->productId, 'product_cat');
		}

		return array_unique(array_merge($this->categoriesIds, $productCategoriesIds));
	}

	/**
	 * Build get all tags query args
	 *
	 * @return array
	 */
	private function buildGetAllQueryArgs() {
		$args               = $this->buildQueryArgsBase();
		$args['hide_empty'] = false;

		return $args;
	}

	/**
	 * Build query args base
	 *
	 * @return array
	 */
	private function buildQueryArgsBase() {
		$args = array(
			'taxonomy' => PinterestTagsTaxonomy::PINTEREST_TAGS_TAXONOMY_SLUG,
			'fields' => $this->fields,
			'hide_empty' => ! $this->includingEmpty
		);

		return $args;
	}

	/** Get tags ids from categories
	 *
	 * @param int[] $categoriesIds
	 *
	 * @return int[]
	 */
	private function getTagsIdsFromCategories( array $categoriesIds) {

		$categoriesTagsIds = array();

		foreach ($categoriesIds as $categoryId) {

			$categoryTagsIds = $this->pinterestTagsToCategoriesMapper->getPinterestTagsIdsByCategoryId($categoryId);

			$categoriesTagsIds = array_merge($categoriesTagsIds, $categoryTagsIds);
		}

		return array_unique($categoriesTagsIds);
	}


	/**
	 * Set product id
	 *
	 * @param int Product id
	 *
	 * @return $this
	 */
	public function setProductId( $productId) {
		$this->productId = $productId;

		return $this;
	}

	/**
	 * Set categories ids
	 *
	 * @param array $categoriesIds
	 *
	 * @return $this
	 */
	public function setCategoriesIds( array $categoriesIds) {
		$this->categoriesIds = $categoriesIds;

		return $this;
	}

	/**
	 * Set categories ids from product
	 *
	 * @param $productId
	 * @return $this
	 */
	public function setCategoriesIdsFromProduct( $productId) {
		$this->categoriesIds = wc_get_product_terms($productId, 'product_cat', array('fields' => 'ids'));

		return $this;
	}

	/**
	 * Set include product categories
	 *
	 * @param bool $includeProductCategories
	 *
	 * @return $this
	 */
	public function setIncludeProductCategories( $includeProductCategories) {
		$this->includeProductCategories = $includeProductCategories;

		return $this;
	}

	/**
	 * Set name like parameter
	 *
	 * @param string $search
	 *
	 * @return $this
	 */
	public function setNameLike( $search) {
		$this->nameLike = $search;

		return $this;
	}

	/**
	 * Set including empty
	 *
	 * @param bool $includingEmpty
	 *
	 * @return $this
	 *
	 * @todo: check and remove this if not used
	 */
	public function setIncludingEmpty( $includingEmpty) {
		$this->includingEmpty = $includingEmpty;

		return $this;
	}

	/**
	 * Set names only
	 *
	 * @param bool $namesOnly
	 *
	 * @return $this
	 */
	public function setNamesOnly( $namesOnly) {
		$this->fields = $namesOnly ? 'names' : 'all';

		return $this;
	}
}
