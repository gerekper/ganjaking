<?php


namespace Premmerce\WooCommercePinterest\Model;

use Premmerce\SDK\V2\FileManager\FileManager;

/**
 * Class GoogleCategoriesRelationsModel
 *
 * @package Premmerce\WooCommercePinterest\Model
 *
 * This class is responsible for Google Product Categories table queries
 */
class GoogleCategoriesModel extends AbstractModel {

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'woocommerce_pinterest_google_product_categories';

	/**
	 * GoogleCategoriesRelationsModel constructor.
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager) {
		parent::__construct();
		$this->fileManager = $fileManager;
	}

	/**
	 * Filter rows by parent_id column value
	 *
	 * @param $parentId
	 *
	 * @return GoogleCategoriesModel
	 */
	private function filterByParentId( $parentId) {
		$this->where(array('parent_id' => $parentId));

		return $this;
	}

	/**
	 * Get category direct children
	 *
	 * @param $parentId
	 *
	 * @return mixed
	 */
	public function getChildren( $parentId) {
		return $this->filterByParentId($parentId)
			->get();
	}

	/**
	 * Return category full ancestors chain
	 *
	 * @param $childCategoryId
	 *
	 * @return array
	 */
	public function getCategoryAncestorsChain( $childCategoryId) {
		$sql    = $this->prepareQueryToGetCategoryAncestorsChain($childCategoryId);
		$result = $this->db->get_row($sql, ARRAY_A);
		return  $result ? $result : array();
	}

	/**
	 * Build query to get category ancestors
	 *
	 * @param $childCategoryId
	 * 
	 * @return string
	 */
	private function prepareQueryToGetCategoryAncestorsChain( $childCategoryId) {
		$select = $this->selectForGetCategoryAncestorsQuery();

		$from = "FROM {$this->table} as child";

		$join = $this->joinForGetCategoryAncestorsQuery();

		$where = 'WHERE child.id=%d';

		$sql = "{$select} {$from} {$join} {$where}";

		return $this->db->prepare($sql, $childCategoryId);
	}

	/**
	 * Build get category ancestors SELECT query part
	 *
	 * @return string
	 */
	private function selectForGetCategoryAncestorsQuery() {
		$select      = 'SELECT child.id, child.name, ';
		$selectParts = array();

		for ($i=1; $i<7; $i++) {
			$selectParts[] = "parent{$i}.id as parent{$i}, parent{$i}.name as parent{$i}name";
		}

		$select .= implode(', ', $selectParts);


		return $select;
	}

	/**
	 * Build get category ancestors JOIN query part
	 *
	 * @return string
	 */
	private function joinForGetCategoryAncestorsQuery() {
		$join = "LEFT JOIN {$this->table} as parent1 on child.parent_id = parent1.id ";

		for ($i=1; $i<6; $i++) {

			$aliasNumber = $i+1;
			$join       .= "LEFT JOIN {$this->table} as parent{$aliasNumber} on parent{$i}.parent_id = parent{$aliasNumber}.id ";

		}

		return $join;
	}
}
