<?php
namespace Premmerce\WooCommercePinterest\Installer;

use ParseCsv\Csv;
use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\PinterestException;

/**
 * Class GoogleCategoriesImporter
 *
 * @package Premmerce\WooCommercePinterest\Catalog
 *
 * This class is responsible for import Google Product Category Taxonomy from the csv file to the DB table.
 */
class GoogleCategoriesImporter {


	/**
	 * Categories array
	 *
	 * @var array
	 */
	private $categories;

	/**
	 * CSV instance
	 *
	 * @var Csv
	 */
	private $csv;

	/**
	 * FileManager
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * GoogleCategoriesModel instance
	 *
	 * @var GoogleCategoriesModel
	 */
	private $categoriesModel;

	/**
	 * GoogleCategoriesImporter constructor.
	 *
	 * @param Csv $csv
	 * @param FileManager $fileManager
	 * @param GoogleCategoriesModel $categoriesModel
	 */
	public function __construct( Csv $csv, FileManager $fileManager, GoogleCategoriesModel $categoriesModel) {
		$this->csv             = $csv;
		$this->categories      = array();
		$this->fileManager     = $fileManager;
		$this->categoriesModel = $categoriesModel;
	}

	/**
	 * Import GoogleCategories to database
	 *
	 * @throws PinterestException
	 */
	public function import() {
		$categoriesFilePath = $this->getCategoriesFilePath();
		$categories         = $this->getCategoriesFromFile($categoriesFilePath);
		$this->insertCategoriesToDataBase($categories);
	}

	/**
	 * Return path to Google categories file
	 *
	 * @return string
	 */
	private function getCategoriesFilePath() {
		return $this->fileManager->getPluginDirectory() . 'assets/csv/google_catalog.csv';
	}

	/**
	 * Return Google categories from file
	 *
	 * @param $fileName
	 *
	 * @return array[]
	 */
	private function getCategoriesFromFile( $fileName) {
		$this->csv->auto($fileName);
		$categoriesMap = $this->buildCategoriesMap($this->csv->data);
		$categoriesMap = $this->replaceParentNameWithParentCode($categoriesMap);

		return $categoriesMap;
	}

	/**
	 * Return Google categories hierarchy
	 *
	 * @param array $categories
	 *
	 * @return array[]
	 */
	private function buildCategoriesMap( array $categories) {
		$categoriesMap = array();

		foreach ($categories as $categoryRow) {

			$categoryData = $this->fetchCategoryDataFromRow($categoryRow);
			$name         = $categoryData['categoryName'];

			$categoriesMap[$name] = array(
				'code' => (int) $categoryData['categoryCode'],
				'parentName' => $categoryData['parentName']
			);
		}

		return $categoriesMap;
	}

	/**
	 * Parse single row from Google categories file
	 *
	 * @param array $categoryRow
	 *
	 * @return string[]
	 */
	private function fetchCategoryDataFromRow( array $categoryRow) {
		$filteredCategoryRow = array_filter($categoryRow);

		$categoryData['categoryCode'] = reset($categoryRow);
		$categoryData['categoryName'] = end($filteredCategoryRow);
		$categoryData['parentName']   = count($filteredCategoryRow) > 2 ? prev($filteredCategoryRow) : '';

		return $categoryData;
	}

	/**
	 * Replace category parent name with it's code
	 *
	 * @param array $categoriesMap
	 *
	 * @return array[]
	 */
	private function replaceParentNameWithParentCode( array $categoriesMap) {
		foreach ($categoriesMap as $categoryTitle => $categoryData) {
			$parentName                                  = $categoryData['parentName'];
			$categoriesMap[$categoryTitle]['parentCode'] = $parentName ? (int) $categoriesMap[$parentName]['code'] : 0;
			unset($categoriesMap[$categoryTitle]['parentName']);
		}

		return $categoriesMap;
	}

	/**
	 * Insert parsed Google categories to DB
	 *
	 * @param array $categories
	 *
	 * @throws PinterestException
	 */
	private function insertCategoriesToDatabase( array $categories) {
		$fields              = $this->getCategoriesTableFields();
		$categoriesTableData = $this->prepareCategoriesDataToInsert($categories);

		try {
			$this->categoriesModel->replaceMultiple($fields, $categoriesTableData);
		} catch (PinterestModelException $e) {
			throw new PinterestException('Caught exception when trying to import Google Categories.', $e->getCode(), $e);
		}
	}

	/**
	 * Return Google categories DB table columns names and types
	 *
	 * @return array
	 */
	private function getCategoriesTableFields() {
		return array(
			'id' => '%d',
			'parent_id' => '%d',
			'name' => '%s'
		);
	}

	/**
	 * Format Google categories data so it can be inserted into DB
	 *
	 * @param array $categories
	 *
	 * @return array
	 */
	private function prepareCategoriesDataToInsert( array $categories) {
		$preparedCategories = array();

		foreach ($categories as $categoryName => $categoryData) {
			$preparedCategories[] = array(
				'id' => $categoryData['code'],
				'parent_id' => $categoryData['parentCode'],
				'name' => $categoryName
			);
		}

		return $preparedCategories;
	}

}
