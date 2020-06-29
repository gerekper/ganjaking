<?php namespace Premmerce\WooCommercePinterest;

use ParseCsv\Csv;
use Premmerce\PrimaryCategory\PrimaryCategory;
use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\AdminAssets;
use Premmerce\WooCommercePinterest\Admin\Pins;
use Premmerce\WooCommercePinterest\Admin\Product\BulkActions;
use Premmerce\WooCommercePinterest\Admin\Product\ProductHandler;
use Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable\WcCategoryGoogleCategoryRelationsTable;
use Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable\WcCategoryPinterestBoardRelationsTable;
use Premmerce\WooCommercePinterest\Admin\Table\PinsTable;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\AJAX\AjaxController;
use Premmerce\WooCommercePinterest\Installer\GoogleCategoriesImporter;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Analytics;
use Premmerce\WooCommercePinterest\Frontend\SaveButton;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesRelationsModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesModel;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Pinterest\Api\Api;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\Pinterest\AuthHandler;
use Premmerce\WooCommercePinterest\Pinterest\CatalogGenerator;
use Premmerce\WooCommercePinterest\Pinterest\DescriptionPlaceholders;
use Premmerce\WooCommercePinterest\Pinterest\PinDataGenerator;
use Premmerce\WooCommercePinterest\Pinterest\PinService;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsRepository;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsToCategoriesMapper;
use Premmerce\WooCommercePinterest\Task\CatalogGenerationTaskManager;
use Premmerce\WooCommercePinterest\Task\PinCreationBackgroundProcess;
use Premmerce\WooCommercePinterest\Logger\Logger;
use Premmerce\WooCommercePinterest\Task\CatalogGenerationProcess;
use Premmerce\WooCommercePinterest\Task\PinCreationTaskManager;
use Premmerce\WooCommercePinterest\Installer\Installer;

/**
 * Class ServiceContainer
 * Responsible for plugin's services storage
 *
 * @package Premmerce\WooCommercePinterest
 *
 * @todo: think about using Dependency Injection package
 */
class ServiceContainer {


	/**
	 * Services
	 *
	 * @var array
	 */
	protected $services = array();

	/**
	 * ServiceContainer instance
	 *
	 * @var ServiceContainer
	 */
	protected static $instance;

	/**
	 * Return self
	 *
	 * @return ServiceContainer
	 */
	public static function getInstance() {
		return static::$instance ? static::$instance : static::$instance = new static();
	}

	/**
	 * Return FileManager instance
	 *
	 * @return FileManager
	 */
	public function getFileManager() {
		return $this->getService(FileManager::class);
	}

	/**
	 * Return AdminNotifier instance
	 *
	 * @return AdminNotifier
	 */
	public function getNotifier() {
		if (! $this->serviceExists(AdminNotifier::class)) {
			$this->addService(AdminNotifier::class, new AdminNotifier());
		}

		return $this->getService(AdminNotifier::class);
	}

	/**
	 * Return PinService instance
	 *
	 * @return PinService
	 */
	public function getPinService() {
		if (! $this->serviceExists(PinService::class)) {
			$pinModel                   = $this->getPinModel();
			$categoryBoardRelationModel = $this->getBoardRelationModel();
			$logger                     = $this->getLogger();
			$notifier                   = $this->getNotifier();

			$this->addService(PinService::class,
				new PinService($pinModel, $categoryBoardRelationModel, $logger, $notifier));
		}

		return $this->getService(PinService::class);
	}

	/**
	 * Return PinModel instance
	 *
	 * @return PinModel
	 */
	public function getPinModel() {
		if (! $this->serviceExists(PinModel::class)) {
			$this->addService(PinModel::class, new PinModel($this->getApiState()->getUserId()));
		}

		return $this->getService(PinModel::class);
	}

	/**
	 * Return PinDataGenerator
	 *
	 * @return PinDataGenerator
	 */
	public function getPinDataGenerator() {
		if (! $this->serviceExists(PinDataGenerator::class)) {
			$pinDataGenerator = new PinDataGenerator($this->getPinterestTagsController(), $this->getPinterestIntegration());

			$this->addService(PinDataGenerator::class, $pinDataGenerator);
		}

		return $this->getService(PinDataGenerator::class);
	}


	/**
	 * Return Api instance
	 *
	 * @return Api
	 */
	public function getApi() {
		if (! $this->serviceExists(Api::class)) {
			$state = $this->getApiState();
			$this->addService(Api::class, new Api($state));
		}

		return $this->getService(Api::class);
	}

	/**
	 * Return ApiState instance
	 *
	 * @return ApiState
	 */
	public function getApiState() {
		if (! $this->serviceExists(ApiState::class)) {
			$this->addService(ApiState::class, new ApiState());
		}

		return $this->getService(ApiState::class);
	}

	/**
	 * Return PinCreationBackgroundProcess instance
	 *
	 * @return PinCreationBackgroundProcess
	 */
	public function getPinCreationBackgroundProcess() {
		if (! $this->serviceExists(PinCreationBackgroundProcess::class)) {

			$this->includeWcBackgroundProcess();

			$this->addService(
				PinCreationBackgroundProcess::class,
				new PinCreationBackgroundProcess(
					$this->getApi(),
					$this->getPinModel(),
					$this->getPinDataGenerator(),
					$this->getLogger())
			);
		}

		return $this->getService(PinCreationBackgroundProcess::class);
	}

	/**
	 * Return CatalogGenerationProcess
	 *
	 * @return CatalogGenerationProcess
	 */
	public function getCatalogGenerationProcess() {
		if (! $this->serviceExists(CatalogGenerationProcess::class)) {

			$this->includeWcBackgroundProcess();

			$this->addService(CatalogGenerationProcess::class,
				new CatalogGenerationProcess($this->getCatalogGenerator()));
		}

		return $this->getService(CatalogGenerationProcess::class);
	}

	/**
	 * Return CatalogGenerator instance
	 *
	 * @return CatalogGenerator
	 */
	public function getCatalogGenerator() {
		if (! $this->serviceExists(CatalogGenerator::class)) {
			$this->addService(CatalogGenerator::class, new CatalogGenerator($this->getCsv(), $this->getGoogleCategoriesRelationsModel()));
		}

		return $this->getService(CatalogGenerator::class);
	}

	private function includeWcBackgroundProcess() {
		if (! class_exists('WC_Background_Process', false)) {
			include_once dirname(WC_PLUGIN_FILE) . '/includes/abstracts/class-wc-background-process.php';
		}
	}

	/**
	 * Return PinterestIntegration instance
	 *
	 * @return PinterestIntegration
	 */
	public function getPinterestIntegration() {
		if (! $this->serviceExists(PinterestIntegration::class)) {
			if ( function_exists('wc' ) && isset(wc()->integrations->get_integrations()['pinterest'])) {
				$pinterestIntegration = wc()->integrations->get_integrations()['pinterest'];
				$this->addService(PinterestIntegration::class, $pinterestIntegration );
			}
		}
		return $this->getService(PinterestIntegration::class);
	}

	/**
	 * Return BulkAction instance
	 *
	 * @return BulkActions
	 */
	public function getBulkActions() {
		if (! $this->serviceExists(BulkActions::class)) {
			$this->addService(BulkActions::class, new BulkActions(
				$this->getFileManager(),
				$this->getPinterestIntegration(),
				$this->getPinService(),
				$this->getNotifier(),
				$this->getBoardRelationModel(),
				$this->getPinModel())
			);
		}

		return $this->getService(BulkActions::class);
	}

	/**
	 * Return SaveButton instance
	 *
	 * @return SaveButton
	 */
	public function getSaveButton() {
		if (! $this->serviceExists(SaveButton::class)) {
			$this->addService(SaveButton::class, new SaveButton($this->getFileManager(), $this->getPinterestIntegration()));
		}

		return $this->getService(SaveButton::class);
	}

	/**
	 * Return Analytics instance
	 *
	 * @return Analytics
	 */
	public function getAnalytics() {
		if (! $this->serviceExists(Analytics::class)) {
			$primaryCategoryServiceContainer = \Premmerce\PrimaryCategory\ServiceContainer::getInstance();
			$primaryCategoryModel            = $primaryCategoryServiceContainer->getModel();
			$this->addService(Analytics::class, new Analytics($this->getFileManager(), $this->getPinterestIntegration(), $primaryCategoryModel));
		}

		return $this->getService(Analytics::class);
	}

	/**
	 * Return ProductHandler instance
	 *
	 * @return ProductHandler
	 */
	public function getProductHandler() {
		if (! $this->serviceExists(ProductHandler::class)) {
			$productHandler = new ProductHandler(
				$this->getFileManager(),
				$this->getPinService(),
				$this->getPinterestIntegration(),
				$this->getPinModel(),
				$this->getBoardRelationModel(),
				$this->getPinterestTagsController(),
				$this->getDescriptionPlaceholders()
			);
			$this->addService(ProductHandler::class, $productHandler);
		}

		return $this->getService(ProductHandler::class);
	}

	/**
	 * Return AuthHandler instance
	 *
	 * @return AuthHandler
	 */
	public function getAuthHandler() {
		if (! $this->serviceExists(AuthHandler::class)) {
			$this->addService(AuthHandler::class, new AuthHandler($this->getApiState(), $this->getPinterestIntegration()));
		}

		return $this->getService(AuthHandler::class);
	}

	/**
	 * Return PinCreationTaskManager instance
	 *
	 * @return PinCreationTaskManager
	 */
	public function getPinCreationTaskManager() {
		if (!$this->serviceExists(PinCreationTaskManager::class)) {
			$this->addService(PinCreationTaskManager::class, new PinCreationTaskManager($this->getApiState(), $this->getPinCreationBackgroundProcess(), $this->getPinModel()));
		}

		return $this->getService(PinCreationTaskManager::class);
	}

	/**
	 * Return CatalogGenerationTaskManager instance
	 *
	 * @return CatalogGenerationTaskManager
	 */
	public function getCatalogGenerationTaskManager() {
		if (! $this->serviceExists(CatalogGenerationTaskManager::class)) {
			$this->addService(CatalogGenerationTaskManager::class, new CatalogGenerationTaskManager($this->getCatalogGenerationProcess(), $this->getCatalogGenerator()));
		}

		return $this->getService(CatalogGenerationTaskManager::class);
	}

	/**
	 * Return Pins instance
	 *
	 * @return Pins
	 */
	public function getPins() {
		if (! $this->serviceExists(Pins::class)) {
			$this->addService(Pins::class, new Pins($this->getFileManager(), $this->getApiState(), $this->getPinService(), $this->getNotifier()));
		}

		return $this->getService(Pins::class);
	}

	/**
	 * Return PinterestTagsRepository instance
	 *
	 * @return PinterestTagsRepository
	 */
	public function getPinterestTagsRepository() {
		if (! $this->serviceExists(PinterestTagsRepository::class)) {

			$pinterestTagsRepository = new PinterestTagsRepository($this->getPinterestTagsToCategoriesMapper());
			$this->addService(PinterestTagsRepository::class, $pinterestTagsRepository);
		}

		return $this->getService(PinterestTagsRepository::class);
	}

	/**
	 * Return PinterestTagsToCategoriesMapper instance
	 *
	 * @return PinterestTagsToCategoriesMapper
	 */
	public function getPinterestTagsToCategoriesMapper() {
		if (! $this->serviceExists(PinterestTagsToCategoriesMapper::class)) {
			$this->addService(PinterestTagsToCategoriesMapper::class, new PinterestTagsToCategoriesMapper());
		}

		return $this->getService(PinterestTagsToCategoriesMapper::class);
	}

	/**
	 * Return PinterestTagsToCategoriesMapper instance
	 *
	 * @return PinterestTagsController
	 */
	public function getPinterestTagsController() {
		if (! $this->serviceExists(PinterestTagsController::class)) {
			$primaryCategoryServiceContainer = \Premmerce\PrimaryCategory\ServiceContainer::getInstance();

			$pinterestTagsController = new PinterestTagsController(
				$this->getPinterestTagsRepository(),
				$this->getPinterestTagsToCategoriesMapper(),
				$primaryCategoryServiceContainer->getModel(),
				$this->getLogger()
			);
			$this->addService(PinterestTagsController::class, $pinterestTagsController);
		}

		return $this->getService(PinterestTagsController::class);
	}

	/**
	 * Returns PinsTable object.
	 * Do not call this before WP_Screen is ready.
	 *
	 * @return PinsTable
	 */
	public function getPinsTable() {
		if (! $this->serviceExists(PinsTable::class)) {

			$this->includeWpListTable();

			$this->addService(PinsTable::class, new PinsTable($this->getFileManager(), $this->getPinModel(), $this->getPinterestIntegration()));
		}

		return $this->getService(PinsTable::class);
	}

	/**
	 * Return WcCategoryPinterestBoardRelationsTable instance
	 *
	 * @return WcCategoryPinterestBoardRelationsTable
	 */
	public function getCategoryBoardTable() {
		if (! $this->serviceExists(WcCategoryPinterestBoardRelationsTable::class)) {

			$this->includeWpListTable();

			$this->addService(WcCategoryPinterestBoardRelationsTable::class, new WcCategoryPinterestBoardRelationsTable(
				$this->getBoardRelationModel(),
				$this->getFileManager())
			);
		}

		return $this->getService(WcCategoryPinterestBoardRelationsTable::class);
	}

	private function includeWpListTable() {
		if ( ! class_exists( '\WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}
	}

	/**Return BoardRelationsModel instance
	 *
	 * @return BoardRelationsModel
	 */
	public function getBoardRelationModel() {
		if (! $this->serviceExists(BoardRelationsModel::class)) {
			$pinterestUserId = $this->getApiState()->getUserId();
			$this->addService(BoardRelationsModel::class, new BoardRelationsModel($pinterestUserId));
		}

		return $this->getService(BoardRelationsModel::class);
	}

	/**
	 * Return AdminAssets instance
	 *
	 * @return AdminAssets
	 */
	public function getAdminAssets() {
		if (! $this->serviceExists(AdminAssets::class)) {
			$this->addService(AdminAssets::class, new AdminAssets($this->getFileManager()));
		}

		return $this->getService(AdminAssets::class);
	}

	/**
	 * Return Logger instance
	 *
	 * @return Logger
	 */
	public function getLogger() {
		if (! $this->serviceExists(Logger::class)) {
			$this->addService(Logger::class, new Logger());
		}

		return $this->getService(Logger::class);
	}

	/**
	 * Return Installer instance
	 *
	 * @return Installer
	 */
	public function getInstaller() {
		if (! $this->serviceExists(Installer::class)) {
			global $wpdb;
			$this->addService(Installer::class, new Installer($wpdb, $this->getGoogleCategoriesImporter()));
		}

		return $this->getService(Installer::class);
	}

	/**
	 * Return GoogleCategoriesModel instance
	 *
	 * @return GoogleCategoriesModel
	 */
	public function getGoogleCategoriesModel() {
		if (! $this->serviceExists(GoogleCategoriesModel::class)) {
			$googleCategoriesRelationModel = new GoogleCategoriesModel($this->getFileManager());

			$this->addService(GoogleCategoriesModel::class, $googleCategoriesRelationModel);
		}

		return $this->getService(GoogleCategoriesModel::class);
	}

	/**
	 * Return Csv instance
	 *
	 * @return Csv;
	 */
	public function getCsv() {
		if (! $this->serviceExists(Csv::class)) {
			$this->addService(Csv::class, new Csv());
		}

		return $this->getService(Csv::class);
	}

	/**
	 * Return GoogleCategoriesImporter instance
	 *
	 * @return GoogleCategoriesImporter
	 */
	public function getGoogleCategoriesImporter() {
		if (! $this->serviceExists(GoogleCategoriesImporter::class)) {
			$this->addService(GoogleCategoriesImporter::class,
				new GoogleCategoriesImporter($this->getCsv(),
					$this->getFileManager(),
					$this->getGoogleCategoriesModel()));
		}

		return $this->getService(GoogleCategoriesImporter::class);
	}

	/**
	 * Return AjaxController instance
	 *
	 * @return AjaxController
	 */
	public function getAjax() {
		if (!$this->serviceExists(AjaxController::class)) {
			$this->addService(AjaxController::class, new AjaxController($this->getPinterestTagsController(),
				$this->getGoogleCategoriesModel(),
				$this->getGoogleCategoriesRelationsModel(),
				$this->getBoardRelationModel(),
				$this->getPinterestIntegration()
			));
		}

		return $this->getService(AjaxController::class);
	}

	/**
	 * Return DescriptionPlaceholders instance
	 *
	 * @return DescriptionPlaceholders
	 */
	public function getDescriptionPlaceholders() {
		if (! $this->serviceExists(DescriptionPlaceholders::class)) {
			$this->addService(DescriptionPlaceholders::class, new DescriptionPlaceholders());
		}

		return $this->getService(DescriptionPlaceholders::class);
	}

	/**
	 * Return WcCategoryGoogleCategoryRelationsTable
	 *
	 * @return WcCategoryGoogleCategoryRelationsTable
	 */
	public function getCatalogTable() {
		if (!$this->serviceExists(WcCategoryGoogleCategoryRelationsTable::class)) {

			$this->includeWpListTable();

			$this->addService(WcCategoryGoogleCategoryRelationsTable::class, new WcCategoryGoogleCategoryRelationsTable($this->getFileManager(),
				$this->getGoogleCategoriesRelationsModel(),
				$this->getGoogleCategoriesModel()
			));
		}

		return $this->getService(WcCategoryGoogleCategoryRelationsTable::class);
	}

	/**
	 * ReturnGoogleCategoriesRelationsModel
	 *
	 * @return GoogleCategoriesRelationsModel
	 */
	public function getGoogleCategoriesRelationsModel() {
		if (! $this->serviceExists(GoogleCategoriesRelationsModel::class)) {
			$this->addService(GoogleCategoriesRelationsModel::class, new GoogleCategoriesRelationsModel($this->getGoogleCategoriesModel()));
		}

		return $this->getService(GoogleCategoriesRelationsModel::class);
	}

	/**
	 * Check if service exists
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function serviceExists( $id) {
		return isset($this->services[$id]);
	}

	/**
	 * Add new service
	 *
	 * @param string $id
	 * @param $service
	 */
	public function addService( $id, $service) {
		$this->services[$id] = $service;
	}


	/**
	 * Return registered service
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function getService( $id) {
		if ($this->serviceExists($id)) {
			return $this->services[$id];
		}
	}
}
