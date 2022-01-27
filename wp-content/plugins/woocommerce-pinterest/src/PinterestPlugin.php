<?php namespace Premmerce\WooCommercePinterest;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\Admin;
use Premmerce\WooCommercePinterest\Admin\Product\PinAll\PinAllManager;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Frontend\Frontend;
use Premmerce\PrimaryCategory\ServiceContainer as PrimaryCategoryServiceContainer;
use Premmerce\WooCommercePinterest\Task\BoardCreationBackgroundProcess;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;

/**
 * Class PinterestPlugin
 *
 * Responsible for handling plugin actions (activate, uninstall),
 * and plugin initialization.
 *
 * @package Premmerce\Pinterest
 */
class PinterestPlugin {


	/**
	 * ServiceContainer instance
	 *
	 * @var ServiceContainer
	 */
	private $container;

	/**
	 * Main plugin file path
	 *
	 * @var string
	 */
	private $mainFile;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public static $version = '';

	/**
	 * PinterestPlugin constructor.
	 *
	 * @param string $mainFile
	 */
	public function __construct( $mainFile) {
		$this->mainFile  = $mainFile;
		self::$version   = $this->getPluginVersionFromMainFileDocBlock();
		$this->container = ServiceContainer::getInstance();
		$this->container->addService(FileManager::class, new FileManager($mainFile));
		$this->container->addService( PinAllManager::class, new PinAllManager( $this->container ) );

		add_action('plugins_loaded', array($this, 'loadTextDomain'));
		add_action('plugins_loaded', array($this, 'checkForWooCommerce'));
		add_action('plugins_loaded', array($this, 'initPrimaryCategory'));

		add_filter( 'woocommerce_integrations', array( $this, 'addIntegration' ) );

		add_action( 'woocommerce_pinterest_set_timeout_to_get_boards',  array( $this, 'getBoardsAfterTimeout' ) );
	}

  public function getBoardsAfterTimeout()
  {
    BoardCreationBackgroundProcess::getBoardsAfterTimeout();
  }

	/**
	 * Run plugin part
	 */
	public function run() {

		$this->container->getInstaller()->update();

		$this->container->getAuthHandler()->init();
		$this->container->getPinCreationTaskManager()->init();
		$this->container->getCatalogGenerationTaskManager()->init();

		$fileManager = $this->container->getFileManager();
		$integration = $this->container->getPinterestIntegration();

		if ( is_admin() ) {

			new Admin( $this->container );

		} else {
			$saveButton = $this->container->getSaveButton();
			$analytics  = $this->container->getAnalytics();

			new Frontend( $fileManager, $integration, $saveButton, $analytics );
		}
	}

	/**
	 * Add WooCommerce Integration
	 *
	 * @param $integrations
	 *
	 * @return array
	 */
	public function addIntegration( $integrations ) {
		$integrations[] = PinterestIntegration::class;

		return $integrations;
	}

	/**
	 * Load plugin translations
	 */
	public function loadTextDomain() {
		$name = $this->container->getFileManager()->getPluginName();
		load_plugin_textdomain( 'woocommerce-pinterest', false, $name . '/languages/' );
	}

	public function initPrimaryCategory() {
		if ( ! PinterestPluginUtils::isYoastActive() ) {
			$primaryCategoryContainer = PrimaryCategoryServiceContainer::getInstance();
			$primaryCategoryContainer->initPrimaryCategory( $this->mainFile );
		}
	}

	/**
	 * Fired when the plugin is activated
	 */
	public function activate() {
		$this->container->getInstaller()->install();
		set_transient( 'woocommerce_pinterest_show_message', 1, MINUTE_IN_SECONDS );
	}

	/**
	 * Check woocommerce and push notifications
	 */
	public function checkForWooCommerce() {
		if ( ! $this->isWooCommerceActive() ) {
			/* translators: '%1$s' <a> tag, '%2$s' is replaced with is replaced with plugin name */
			$message = esc_html__( 'The %1$s plugin requires %2$s plugin to be active!', 'woocommerce-pinterest' );
			$woo     = '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>';
			$error   = sprintf( $message, 'WooCommerce Pinterest', $woo );
			$this->container->getNotifier()->push( $error, AdminNotifier::ERROR, false );
		}
	}

	/**
	 * Check WooCommerce plugin
	 *
	 * @return bool
	 */
	public function isWooCommerceActive() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Return plugin version from main file docblock
	 *
	 * @return string
	 */
	private function getPluginVersionFromMainFileDocBlock() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$pluginData = get_plugin_data( $this->mainFile, false, false );

		return isset( $pluginData['Version'] ) ? $pluginData['Version'] : '';
	}
}
