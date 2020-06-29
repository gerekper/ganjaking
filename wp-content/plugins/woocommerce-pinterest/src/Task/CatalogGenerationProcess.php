<?php


namespace Premmerce\WooCommercePinterest\Task;

use Premmerce\WooCommercePinterest\Pinterest\CatalogGenerator;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \WC_Background_Process;
use \WC_Log_Levels;

class CatalogGenerationProcess extends WC_Background_Process {

	/**
	 * Action
	 *
	 * @var string
	 */
	protected $action = 'woocommerce_pinterest_generate_catalog';

	/**
	 * CatalogGenerator instance
	 *
	 * @var CatalogGenerator
	 */
	private $catalogGenerator;

	/**
	 * CatalogGenerationProcess constructor.
	 *
	 * @param CatalogGenerator $catalogGenerator
	 */
	public function __construct( CatalogGenerator $catalogGenerator) {
		parent::__construct();

		$this->catalogGenerator = $catalogGenerator;
	}

	/**
	 * Export single product to catalog
	 *
	 * @param mixed $productId
	 *
	 * @return false
	 */
	public function task( $productId) {
		try {
			$this->catalogGenerator->exportProductDataToCatalog($productId);
		} catch (PinterestException $e) {
			ServiceContainer::getInstance()->getLogger()->logPinterestException($e);

			$this->kill_process();
		}

		return false;
	}

	/**
	 * Check is process finished
	 *
	 * @return bool
	 */
	public function isFinished() {
		return $this->is_queue_empty();
	}

	public function complete() {
		parent::complete();

		ServiceContainer::getInstance()->getLogger()->log('Catalog generation finished.', WC_Log_Levels::INFO);
	}
}
