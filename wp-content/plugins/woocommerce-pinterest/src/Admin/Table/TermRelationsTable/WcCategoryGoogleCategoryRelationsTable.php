<?php


namespace Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesRelationsModel;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use \WP_Term;

/**
 * Class CatalogTable
 *
 * @package Premmerce\WooCommercePinterest\Admin\Table
 *
 * This class is responsible for Woocommerce categories to Google Merchant categories mapping table rendering
 */
class WcCategoryGoogleCategoryRelationsTable extends AbstractTermRelationsTable {

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * GoogleCategoriesRelationsModel instance
	 *
	 * @var GoogleCategoriesRelationsModel
	 */
	private $googleCategoriesRelationsModel;

	/**
	 * GoogleCategoriesModel instance
	 *
	 * @var GoogleCategoriesModel
	 */
	private $categoriesModel;

	/**
	 * TermsCollection instance
	 *
	 * @var TermsCollection|null
	 */
	public $items;

	/**
	 * CatalogTable constructor.
	 *
	 * @param FileManager $fileManager
	 * @param GoogleCategoriesRelationsModel $googleCategoriesRelationsModel
	 * @param GoogleCategoriesModel $categoriesModel
	 */
	public function __construct(
		FileManager $fileManager,
		GoogleCategoriesRelationsModel $googleCategoriesRelationsModel,
		GoogleCategoriesModel $categoriesModel
	) {
		parent::__construct($fileManager);
		$this->fileManager                    = $fileManager;
		$this->googleCategoriesRelationsModel = $googleCategoriesRelationsModel;
		$this->categoriesModel                = $categoriesModel;
	}


	/**
	 * Return columns ids and names
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
		   'category' => __('WooCommerce category', 'woocommerce-pinterest'),
		   'google_category' => __('Google category', 'woocommerce-pinterest'),
		   'products_number' => __('Category product number', 'woocommerce-pinterest')
		);
	}

	/**
	 * Render 'google_category' column cell
	 *
	 * @param WP_Term $item
	 * @return string
	 */
	public function column_google_category( WP_Term $item) {
		$formattedCategories = $this->prepareDataForGoogleCategoryCell($item->term_id);

		return $this->renderGoogleCategoryCell($item->term_id, $formattedCategories);
	}

	/**
	 * Render products_number column cell
	 *
	 * @param WP_Term $item
	 */
	public function column_products_number( WP_Term $item) {
		$productsCount           = $item->count;
		$productsNumberFormatted = number_format_i18n((float) $productsCount);

		$args = array(
			'taxonomy' => $item->taxonomy,
			'term' => $item->slug,
			'post_type' => 'product'
		);

		$url = add_query_arg($args, 'edit.php');

		echo '<a href="' . esc_url($url) . '">' . esc_html($productsNumberFormatted) . '</a>';
	}


	/**
	 * Prepare data for 'google_category' cell
	 *
	 * @param int $wcCategoryId
	 *
	 * @return array
	 */
	private function prepareDataForGoogleCategoryCell( $wcCategoryId) {
		$categoryWithParents = $this->googleCategoriesRelationsModel->getCategoriesChainByWoocommerceCategory($wcCategoryId);

		return $this->formatCategories($categoryWithParents);
	}

	/**
	 * Format categories for 'google_category' cell
	 *
	 * @param array $categoryWithParents
	 *
	 * @return array
	 */
	private function formatCategories( array $categoryWithParents) {
		$categoryWithParentsFormatted = array();

		$categoryWithParents = array_reverse($categoryWithParents);

		$categoryWithParentsChunked = array_chunk($categoryWithParents, 2);
		$parent                     = 0;

		foreach ($categoryWithParentsChunked as $categoryData) {
			$categoryName                              = $categoryData[0];
			$categoryId                                = $categoryData[1];
			$categoryWithParentsFormatted[$categoryId] = array('name' => $categoryName, 'parent_id' => $parent);
			$parent                                    = $categoryId;
		}

		return $categoryWithParentsFormatted;
	}

	/**
	 * Render 'google_category' cell
	 *
	 * @param $wcCategoryId
	 * @param array $selectedCategoryWithParents
	 *
	 * @todo: too long method, it should be refactored
	 *
	 * @return string
	 */
	private function renderGoogleCategoryCell( $wcCategoryId, array $selectedCategoryWithParents) {
		$html = '<fieldset class="woocommerce-pinterest-google-category-selectors">';


		if (! $selectedCategoryWithParents) {
			$rootLevelCategories = $this->categoriesModel->getChildren(0);
			$html               .=$this->renderGoogleCategorySelect($rootLevelCategories, 0);
		}

		$lastGoogleCategoryId = '';
		foreach ($selectedCategoryWithParents as $googleCategoryId => $categoryData) {
			$categories = $this->categoriesModel->getChildren($categoryData['parent_id']);
			$html      .= $this->renderGoogleCategorySelect($categories, $googleCategoryId);

			$lastGoogleCategoryId = $googleCategoryId;
		}

		if ($lastGoogleCategoryId) {
				$lastSelectorCategories = $this->categoriesModel->getChildren($lastGoogleCategoryId);

			if ($lastSelectorCategories) {
				$html .= $this->renderGoogleCategorySelect($lastSelectorCategories);
			}
		}

		$html .= '<input 
                        type="hidden"
                        class="woocommerce-pinterest-google-categories-mapping"
                        name="' . PinterestIntegration::GOOGLE_CATEGORIES_MAPPING_INPUT_NAME . '[' . esc_attr($wcCategoryId) . ']"
                        value="' . esc_attr($lastGoogleCategoryId) . '"
                        data-wc-category-id="' . esc_attr($wcCategoryId) . '"
                  />';

		$html .= '</fieldset>';

		return $html;
	}

	/**
	 * Render select for 'google_category' cell
	 *
	 * @param string[][]   $googleCategories
	 * @param int $selectedCategoryId
	 *
	 * @return string
	 */
	private function renderGoogleCategorySelect( array $googleCategories, $selectedCategoryId = null) {
		return $this->fileManager->renderTemplate('admin/woocommerce/term-relations-table/google-category-table/google-category-select.php',
			array(
				'categories' => $googleCategories,
				'selectedCategoryId' => $selectedCategoryId
			)
		);
	}
}
