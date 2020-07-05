<?php


namespace Premmerce\WooCommercePinterest\Task;

use Premmerce\WooCommercePinterest\Pinterest\CatalogGenerator;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \DateTime;
use \Exception;
use \WC_Log_Levels;

/**
 * Class CatalogGenerationManager
 *
 * @package Premmerce\WooCommercePinterest\Task
 *
 * This class is responsible for scheduling catalog generation in background.
 */
class CatalogGenerationTaskManager extends AbstractTaskManager {

	const CATALOG_GENERATION_SKIPPED_PRODUCTS_OPTION = 'woocommerce-pinterest-skipped-products';

	protected $taskStatusOptionName = 'woocommerce_pinterest_generation_scheduled';

	protected $catalogUpdatingIntervalName = 'woocommerce_pinterest_catalog_updating_interval';

	protected $catalogUpdatingHook = 'woocommerce_pinterest_update_catalog';

	/**
	 * CatalogGenerationProcess instance
	 *
	 * @var PinCreationBackgroundProcess
	 */
	private $catalogGenerationProcess;

	/**
	 * CatalogGenerator instance
	 *
	 * @var CatalogGenerator
	 */
	private $catalogGenerator;

	/**
	 * CatalogGenerationManager constructor.
	 *
	 * @param CatalogGenerationProcess $catalogGenerationProcess
	 * @param CatalogGenerator $catalogGenerator
	 */
	public function __construct( CatalogGenerationProcess $catalogGenerationProcess, CatalogGenerator $catalogGenerator) {
		$this->catalogGenerationProcess = $catalogGenerationProcess;
		$this->catalogGenerator         = $catalogGenerator;

		add_filter('cron_schedules', array($this, 'registerCatalogRegenerationInterval'));
		add_action($this->catalogUpdatingHook, array($this, 'reGenerateCatalog'));
	}

	public function schedule() {
		if ( $this->taskStarted()) {
			return;
		}

		$logger = ServiceContainer::getInstance()->getLogger();
		$logger->log('Catalog generation started.', WC_Log_Levels::INFO);


		$this->deleteExistingCatalogFile();
		$this->clearSkippedProductsList();

		$productsIds = $this->getProductsIdsForCatalog();

		foreach ($productsIds as $id) {
			$this->catalogGenerationProcess->push_to_queue($id);
		}

		$this->setTaskStarted();

		$this->catalogGenerationProcess->save()->dispatch();
	}

	public function reGenerateCatalog() {
		$this->unsetTaskStarted();
		$this->schedule();
	}

	private function deleteExistingCatalogFile() {
		$catalogFilePath = $this->catalogGenerator->getCatalogFilePath();
		if (file_exists($catalogFilePath)) {
			unlink($catalogFilePath);
		}
	}


	public function clearSkippedProductsList() {
		delete_option(self::CATALOG_GENERATION_SKIPPED_PRODUCTS_OPTION);
	}

	/**
	 * Get list of skipped products
	 *
	 * @return array
	 */
	public function getSkipperProductsList() {
		return get_option(self::CATALOG_GENERATION_SKIPPED_PRODUCTS_OPTION, array());
	}

	/**
	 * Get product ids for catalog
	 *
	 * @return int[]
	 */
	private function getProductsIdsForCatalog() {

		return wc_get_products(array(
			'limit' => -1,
			'status' => 'publish',
			'has_password' => false,
			'return' => 'ids',
			'type' => $this->getProductTypesForCatalog(),
			'parent_exclude' => $this->getNotPublishVariableProductsIds()
		));
	}

	/**
	 * Get not publish variable product ids
	 *
	 * @return int[]
	 */
	private function getNotPublishVariableProductsIds() {
		$postStatusesExcludingPublish = array_diff(array_keys(get_post_statuses()), array('publish'));

		return wc_get_products(array(
			'limit' => '-1',
			'status' => $postStatusesExcludingPublish,
			'has_password' => false,
			'return' => 'ids'
		));
	}

	/**
	 * Get product types will be added to catalog
	 *
	 * @return string[]
	 */
	private function getProductTypesForCatalog() {
		$productTypes = array_keys(wc_get_product_types());
		array_push($productTypes, 'variation');

		return apply_filters('woocommerce_pinterest_catalog_product_types', $productTypes);
	}

	public function scheduleAutoUpdating( $time) {
		try {
			$newScheduleStartTimestamp = $this->getAutoUpdatingStartTime($time)->getTimestamp();
			$oldScheduleTimestamp      = $this->getNextScheduledCatalogGenerationTimestamp();
			if ($oldScheduleTimestamp) {
				wp_unschedule_event($oldScheduleTimestamp, $this->catalogUpdatingHook);
			}

			wp_schedule_event($newScheduleStartTimestamp, $this->catalogUpdatingIntervalName, $this->catalogUpdatingHook);

		} catch (PinterestException $e) {
			$container = ServiceContainer::getInstance();
			$container->getLogger()->logPinterestException($e);
			$container->getNotifier()->flash(__('Failed to schedule catalog regeneration', 'woocommerce-pinterest'));
		}
	}

	/**
	 * Get auto updating start time
	 *
	 * @param $time
	 *
	 * @return DateTime
	 *
	 * @throws PinterestException
	 */
	private function getAutoUpdatingStartTime( $time) {
		try {
			$startTime = new DateTime($time);
			$startTime->modify($time);
			if ($startTime->getTimestamp() < $time) {
				$startTime->modify('+ 1 day');
			}

			return $startTime;
		} catch (Exception $e) {
			throw new PinterestException('Caught Exception when trying to detect next catalog regeneration date', 0, $e);
		}
	}

	/**
	 * Get next scheduled catalog generation timestamp
	 *
	 * @return int|null
	 */
	public function getNextScheduledCatalogGenerationTimestamp() {
		$nextScheduled = wp_next_scheduled($this->catalogUpdatingHook);
		return  $nextScheduled ? $nextScheduled : null;
	}

	public function unscheduleAutoUpdating() {
		$nextScheduledTimestamp = wp_next_scheduled($this->catalogUpdatingHook);
		wp_unschedule_event($nextScheduledTimestamp, $this->catalogUpdatingHook);
	}

	/**
	 * Register catalog generation interval
	 *
	 * @param array $cronSchedules
	 *
	 * @return array
	 */
	public function registerCatalogRegenerationInterval( array $cronSchedules) {

		$container = ServiceContainer::getInstance();
		$frequency = $container->getPinterestIntegration()->get_option('pinterest_catalog_updating_frequency');


		if (isset($frequency['days'])) {
			$days = $frequency['days'];
			$cronSchedules[$this->catalogUpdatingIntervalName] = array(
				'interval' => $days * DAY_IN_SECONDS,
				/* translators: %d is replaced with days number */
				'display' => sprintf(_n('Every %d day', 'Every %d days', $days, 'woocommerce-pinterest'), $days)
			);
		}

		return $cronSchedules;
	}

	/**
	 * Return path to catalog file
	 *
	 * @return string
	 */
	public function getCatalogFilePath() {
		return $this->catalogGenerator->getCatalogFilePath();
	}

	/**
	 * Check if catalog was generated
	 *
	 * @return bool
	 */
	public function catalogGenerated() {
		return file_exists($this->getCatalogFilePath()) && ! $this->taskIsFinished();
	}

	/**
	 * Check if task is finished
	 *
	 * @return bool
	 */
	private function taskIsFinished() {
		return ! $this->catalogGenerationProcess->isFinished();
	}

	/**
	 * Get web path to catalog file
	 *
	 * @return string
	 */
	public function getCatalogFileUrl() {
		$baseUploadsUrl = trailingslashit(wp_upload_dir()['baseurl']) . 'pinterest-catalog';
		return trailingslashit($baseUploadsUrl) . CatalogGenerator::CATALOG_FILE_BASENAME;
	}

	/**
	 * Get number of products in catalog
	 *
	 * @return int
	 */
	public function getCatalogRowsNumber() {
		return $this->catalogGenerator->getCatalogRowsNumber();
	}
}
