<?php


namespace Premmerce\WooCommercePinterest\Pinterest;

use ParseCsv\Csv;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesRelationsModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use Premmerce\WooCommercePinterest\Task\CatalogGenerationTaskManager;
use \WC_Product;

class CatalogGenerator {

	const CATALOG_FILE_BASENAME = 'catalog.csv';

	/**
	 * Csv instance
	 *
	 * @var Csv
	 */
	private $csv;

	/**
	 * GoogleCategoriesRelationsModel instance
	 *
	 * @var GoogleCategoriesRelationsModel
	 */
	private $categoriesRelationsModel;

	/**
	 * CatalogGenerator constructor.
	 *
	 * @param Csv $csv
	 * @param GoogleCategoriesRelationsModel $categoriesRelationsModel
	 */
	public function __construct( Csv $csv, GoogleCategoriesRelationsModel $categoriesRelationsModel) {
		$this->csv                      = $csv;
		$this->categoriesRelationsModel = $categoriesRelationsModel;
	}

	/**
	 * Export product data to catalog
	 *
	 * @param $productId
	 *
	 * @throws PinterestException
	 * @throws PinterestModelException
	 */
	public function exportProductDataToCatalog( $productId) {
		$catalogContent      = $this->prepareCsvRow($productId);
		$emptyRequiredFields = $this->getEmptyRequiredFields($catalogContent[0]);

		if ($emptyRequiredFields) {
			$this->updateSkippedProductsList($productId);
			$this->logNoRequiredRowData($productId, $emptyRequiredFields);
		} else {
			$this->writeProductDataToCsv($catalogContent);
		}
	}

	private function logNoRequiredRowData( $productId, array $missedFields) {
		$message  = "Product {$productId} wasn't added to catalog because some of required fields was missed." . PHP_EOL;
		$message .= 'Missed fields is ' . implode(', ', $missedFields);

		ServiceContainer::getInstance()->getLogger()->log($message);
	}

	/**
	 * Get empty Required fields
	 *
	 * @param array $rowData
	 *
	 * @return array
	 */
	private function getEmptyRequiredFields( array $rowData) {
		$missedFields = array();

		foreach ($this->getRequiredFields() as $field) {
			if (empty($rowData[$field])) {
				$missedFields[] = $field;
			}
		}

		return $missedFields;
	}

	/**
	 * Write product data to csv file
	 *
	 * @param array $productData
	 * @throws PinterestException
	 */
	private function writeProductDataToCsv( array $productData) {
		$this->csv->titles = array_keys($productData[0]);

		$filePath = $this->getCatalogFilePath();

		$append = file_exists($filePath);// No append on first line, otherwise no heading will be created.

		$success = $this->csv->save($filePath, $productData, $append);

		if (! $success) {
			throw new PinterestException("Can't write data to catalog file {$filePath}");
		}
	}

	/**
	 * Return required catalog fields
	 *
	 * @return array
	 */
	private function getRequiredFields() {
		return array(
			'id',
			'title',
			'description',
			'link',
			'image_link',
			'availability',
			'condition',
			'google_product_category',
			'price'
		);
	}

	/**
	 * Return optional catalog fields
	 *
	 * @return array
	 *
	 * @todo: add other optional fields
	 */
	private function getOptionalFields() {
		return array(
			'sale_price',
			'item_group_id'
		);
	}


	/**
	 * Prepare single scv row
	 *
	 * @param $productId
	 *
	 * @return array
	 *
	 * @throws PinterestModelException
	 */
	private function prepareCsvRow( $productId) {
		$product = wc_get_product($productId);

		$row = $this->prepareRowFromProduct($product);

		return array($row);
	}

	/**
	 * Prepare single row from WC product
	 *
	 * @param WC_Product $product
	 *
	 * @return array
	 *
	 * @throws PinterestModelException
	 */
	private function prepareRowFromProduct( WC_Product $product) {
		$fields  = array_merge($this->getRequiredFields(), $this->getOptionalFields());
		$rowData = array();

		foreach ($fields as $field) {
			switch ($field) {
				case 'id':
					$value = $product->get_id();
					break;
				case 'title':
					$value = strip_tags($product->get_title());
					break;
				case 'description':
					$shortDescription = $product->get_short_description();
					$value            = $shortDescription ? $shortDescription : $product->get_description();
					$value            = strip_tags($value);
					break;
				case 'link':
					$value = $product->get_permalink();
					break;
				case 'image_link':
					$value = wp_get_attachment_image_url($product->get_image_id(), 'full');
					break;
				case 'availability':
					$value = $this->wcStockStatusToPinterestStockStatus($product->get_stock_status());
					break;
				case 'condition':
					$value = 'new'; //todo: implement this
					break;
				case 'google_product_category':
					$value = $this->categoriesRelationsModel->getGoogleCategoryIdByProduct($product);
					break;
				case 'price':
					/**
					 * Grouped products hasn't 'regular_price', so we must get just 'price' if no 'regular_price'
					 * we need to try to get regular price first because we have 'sale_price' field which will be used for pin price, if filled
					 */
					$regularPrice = $this->formatPrice($product->get_regular_price());
					$value        =  $regularPrice ? $regularPrice : $product->get_price();
					break;
				case 'sale_price':
					$value = $this->formatPrice($product->get_sale_price());
					break;
				case 'item_group_id':
					$value = $product->get_parent_id() ? $product->get_parent_id() : '';
					break;
				default:
					$value = '';
			}

      if( $product->get_type() == 'variation' && empty( $value ) ) {
        $value = $this->getRowFromParentProduct( $field, $product );
      }

			$rowData[$field] = $value;
		}

		return $rowData;
	}

  /**
	 * Get single row from WC product parent
	 *
	 * @param string $field
	 * @param WC_Product $product
	 *
	 * @return string
   *
   * @throws PinterestModelException
	 */
  private function getRowFromParentProduct( $field, WC_Product $product )
  {
    $parent = wc_get_product( $product->get_parent_id() );

    if( ! $parent ) {
      return '';
    }

    if( ! $parent instanceof WC_Product ) {
      return '';
    }

    switch ( $field ) {
      case 'description':
        $shortDescription = $parent->get_short_description();
        $value = $shortDescription ? $shortDescription : $parent->get_description();
        $value = strip_tags( $value );
        break;
      case 'image_link':
        $value = wp_get_attachment_image_url( $parent->get_image_id(), 'full' );
        break;
      case 'availability':
        $value = $this->wcStockStatusToPinterestStockStatus( $parent->get_stock_status() );
        break;
      case 'google_product_category':
        $value = $this->categoriesRelationsModel->getGoogleCategoryIdByProduct( $parent );
        break;
      case 'price':
        $regularPrice = $this->formatPrice( $parent->get_regular_price() );
        $value = $regularPrice ? $regularPrice : $parent->get_price();
        break;
      default:
        $value = '';
    }

    return $value;
  }

	/**
	 * Format product price
	 *
	 * @param $productPrice
	 *
	 * @return string
	 */
	private function formatPrice( $productPrice) {
		$formattedPrice = '';

		if ($productPrice) {
			$formattedPrice = sprintf("{$productPrice}%s", get_woocommerce_currency());
		}
		return $formattedPrice;
	}


	/**
	 * Convert Woocommerce stock status string to Pinterest catalog stock status string
	 *
	 * @param $wcProductStatus
	 *
	 * @return string
	 */
	private function wcStockStatusToPinterestStockStatus( $wcProductStatus) {
		$statusesMap = array(
			'instock' => 'in stock',
			'outofstock' => 'out of stock',
			'onbackorder' => 'preorder'
		);

		return $statusesMap[$wcProductStatus];
	}

	/**
	 * Return catalog file path
	 *
	 * @return string
	 */
	public function getCatalogFilePath() {

		$uploadsBasedir      = wp_upload_dir()['basedir'];
		$pinterestCatalogDir = trailingslashit($uploadsBasedir) . 'pinterest-catalog';

		if (! file_exists($pinterestCatalogDir)) {
			mkdir($pinterestCatalogDir);
		}

		return trailingslashit($pinterestCatalogDir) . self::CATALOG_FILE_BASENAME;
	}

	/**
	 * Get rows number of catalog
	 *
	 * @return int
	 */
	public function getCatalogRowsNumber() {
		$count = 0;
		if (file_exists($this->getCatalogFilePath())) {
			$this->csv->auto($this->getCatalogFilePath());
			$count = count($this->csv->data);
		}

		return $count > 0 ? $count : 0;
	}

	/**
	 * Update skipped products list
	 *
	 * @param $skippedProductId
	 */
	public function updateSkippedProductsList( $skippedProductId) {
		$skipped   = get_option(CatalogGenerationTaskManager::CATALOG_GENERATION_SKIPPED_PRODUCTS_OPTION, array());
		$skipped[] = $skippedProductId;
		update_option(CatalogGenerationTaskManager::CATALOG_GENERATION_SKIPPED_PRODUCTS_OPTION, $skipped);
	}
}
