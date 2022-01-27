<?php namespace Premmerce\WooCommercePinterest\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\Product\PinAll\PinAllManager;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;
use WP_Term;

/**
 * Class Admin
 * Responsible for handling admin requests
 *
 * @package Premmerce\Pinterest\Admin
 */
class Admin {

	const CATEGORY_TAGS_FIELD_KEY = 'woocommerce-pinterest-category-tags';

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * ServiceContainer instance
	 *
	 * @var ServiceContainer
	 */
	private $container;

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $pluginName;

	/**
	 * PluginMessageTitle
	 *
	 * @var string
	 */
	private $pluginMessageTitle;

	/**
	 * Pins instance
	 *
	 * @var Pins
	 */
	private $pins;

	/**
	 * PinterestTagsController instance
	 *
	 * @var PinterestTagsController
	 */
	private $pinterestTagsController;

	/**
	 * PinterestIntegration instance
	 *
	 * @var WooCommerce\PinterestIntegration
	 */
	private $pinterestIntegration;

	/**
	 * Admin constructor.
	 *
	 * Register menu items and handlers
	 *
	 * @param ServiceContainer $container
	 */
	public function __construct( ServiceContainer $container ) {
		$this->container               = $container;
		$this->pluginName              = __( 'Pinterest for WooCommerce', 'woocommerce-pinterest' );
		$this->pluginMessageTitle      = '<b>' . esc_html( $this->pluginName ) . '</b><br>';
		$this->fileManager             = $container->getFileManager();
		$this->pins                    = $this->container->getPins();
		$this->pinterestTagsController = $container->getPinterestTagsController();
		$this->pinterestIntegration    = $container->getPinterestIntegration();
		$this->registerHooks();
	}

	/**
	 * Register admin side hooks
	 */
	public function registerHooks() {
		/**
		 * Todo: Init this only when it really needed
		 */
		add_action( 'admin_init', array( $this, 'initProductHandler' ) );

		add_action( 'admin_menu', array( $this, 'registerMenu' ), 999 );

		add_action( 'admin_init', array( $this, 'activationMessage' ) );

		add_action( 'admin_init', array( $this, 'firstConnection' ) );

		add_action( 'admin_init', function () {
			( new WCPinterestPrivacy() );
		}, 9 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAssets' ), 100 );

		add_filter( 'woocommerce_screen_ids', function ( $screen ) {
			$screen[] = 'woocommerce_page_woocommerce-pinterest-page';

			return $screen;
		} );

		add_action( 'load-edit.php', function () {

			if ( $this->container->getApiState()->isConnected( 'v3' ) ) {
				$bulkActions = $this->container->getBulkActions();

				add_action( 'woocommerce_product_bulk_edit_end', array( $bulkActions, 'renderProductsBoardField' ) );
				add_action( 'woocommerce_product_bulk_and_quick_edit', array(
					$bulkActions,
					'updateProductsBoardsBulkEdit'
				), 10, 2 );

				add_filter( 'bulk_actions-edit-product', array( $bulkActions, 'registerBulkPinAction' ) );
				add_filter( 'handle_bulk_actions-edit-product', array( $bulkActions, 'bulkPinActionHandler' ), 10, 3 );
			}

		} );

		$pluginBaseName = plugin_basename( $this->fileManager->getMainFile() );

		add_filter( 'plugin_action_links_' . $pluginBaseName, array( $this, 'addSettingsLink' ) );

		//pinterest tags
		add_action( 'product_cat_edit_form_fields', array( $this, 'renderProductCategoryEditTagForm' ) );
		add_action( 'product_cat_add_form_fields', array( $this, 'renderProductCategoryNewTagForm' ) );

		add_action( 'edit_product_cat', array( $this, 'updateProductCategoryPinterestTags' ) );
		add_action( 'create_product_cat', array( $this, 'updateProductCategoryPinterestTags' ) );

		add_action( 'wp_loaded', array( $this, 'initAJAX' ) );

		add_action( 'admin_notices', array( $this, 'pushAuthFailedMessage' ), 9 );
		add_action( 'admin_notices', array( $this, 'pushNoDefaultBoardMessage' ), 9 );
	}

	/**
	 * Handle product metabox and bulk actions
	 */
	public function initProductHandler() {
		if ( $this->container->getApiState()->isConnected( 'v3' ) ) {
			$this->container->getProductHandler()->init();
		}
	}

	/**
	 * Register admin menu
	 */
	public function registerMenu() {
		add_submenu_page(
			'woocommerce',
			'WooCommerce Pinterest',
			'Pinterest',
			'manage_options',
			'woocommerce-pinterest-page',
			array( $this->pins, 'render' )
		);
	}

	/**
	 * Enqueue plugin assets
	 *
	 * @param string $screen
	 *
	 */
	public function enqueueAssets( $screen ) {
		$this->container->getAdminAssets()->enqueueAssets( $screen );
	}

	/**
	 * Add plugin settings link
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function addSettingsLink( $links ) {
		$link = array(
			'settings' => '<a href="' . esc_url( $this->pinterestIntegration->getSettingsPageUrl() ) . '">'
						  . esc_html__( 'Settings', 'woocommerce-pinterest' ) . '</a>'
		);

		return array_merge( $link, $links );
	}

	/**
	 * Render product category edit tag form
	 *
	 * @param WP_Term $productCategory
	 */
	public function renderProductCategoryEditTagForm( WP_Term $productCategory ) {
		try {
			$allPinterestTags = $this->pinterestTagsController->getAllPinterestTags();
			$selectedTags     = $this->pinterestTagsController->getTagsObjectsByCategory( $productCategory );
			$this->fileManager->includeTemplate( 'admin/categories-tags/edit-category-tags.php', array(
				'allPinterestTags' => $allPinterestTags,
				'selectedTagsIds'  => wp_list_pluck( $selectedTags, 'term_id' )
			) );
		} catch ( PinterestException $e ) {

			$this->container->getLogger()->logPinterestException( $e );
			self::printTagsQueryFailedMessage();
		}
	}

	/**
	 * Render product category new tag form
	 *
	 * @param string $taxonomySlug
	 */
	public function renderProductCategoryNewTagForm( $taxonomySlug ) {
		try {
			$allPinterestTags = $this->pinterestTagsController->getAllPinterestTags();
			$this->fileManager->includeTemplate( 'admin/categories-tags/new-category-tags.php', array( 'allPinterestTags' => $allPinterestTags ) );
		} catch ( PinterestException $e ) {
			$this->container->getLogger()->logPinterestException( $e );

			self::printTagsQueryFailedMessage();
		}

	}

	public static function printTagsQueryFailedMessage() {
		echo '<span style="color: red">' . esc_html( __( 'Tags query failed. See details on the WooCommerce logs page.', 'woocommerce-pinterest' ) ) . '</span>';
	}

	/**
	 * Update product category pinterest tags
	 *
	 * @param int $termId
	 */
	public function updateProductCategoryPinterestTags( $termId ) {

		$tagsIds = filter_input( INPUT_POST, self::CATEGORY_TAGS_FIELD_KEY, FILTER_SANITIZE_NUMBER_INT,
			FILTER_FORCE_ARRAY );

		$tagsIds = $tagsIds ? $tagsIds : array();

		$this->pinterestTagsController->updateProductCategoryPinterestTags( $termId, $tagsIds );
	}

	/**
	 * Show activation message
	 */
	public function activationMessage() {
		if ( get_transient( 'woocommerce_pinterest_show_message' ) ) {
			delete_transient( 'woocommerce_pinterest_show_message' );

			$account = '<a href="' . esc_url( $this->pinterestIntegration->getSettingsPageUrl() ) . '">'
					   . esc_html__( 'connect your account', 'woocommerce-pinterest' ) . '</a>';


			$this->container->getNotifier()->push(
				sprintf(
				/* translators: '%1$s' is replaced with this plugin name, '%2$s is replaced with <a> tag' */
					esc_html__( '%1$s is ready. To get started, %2$s', 'woocommerce-pinterest' ),
					'<b>' . esc_html( $this->pluginName ) . '</b>',
					$account

				),
				AdminNotifier::SUCCESS,
				true
			);
		}
	}

	public function initAJAX() {
		$this->container->getAjax()->init();
	}

	/**
	 * Check api connection and set user data
	 *
	 * @todo Think about moving this somewhere out of there
	 */
	public function firstConnection() {
		$api   = $this->container->getApi();
		$state = $api->getState();

		$token  = $state->getToken( 'v3' );
		$userId = $state->getUserId();


		if ( ! $token ) {
			return;
		}

		if ( ! $userId ) {
			$notifier = $this->container->getNotifier();
			try {
				$user = $api->getUser()->getData();
				update_option( 'woocommerce_pinterest_user', $user );
				$notifier->flash( $this->pluginMessageTitle . esc_html__( 'Your account has successfully connected',
						'woocommerce-pinterest' ) );

				$this->container->getPinterestIntegration()->updateBoards();

				$state->deleteApiAuthFailed();

				$notifier->push( $this->pluginMessageTitle . esc_html__( 'Your account has successfully connected',
						'woocommerce-pinterest' ) );
			} catch ( PinterestApiException $e ) {
				$logger = $this->container->getLogger();
				$logger->logPinterestException( $e );

				$api->getState()->setApiAuthFailed();

				$notifier->push( $this->pluginMessageTitle . esc_html( $e->getMessage() ), AdminNotifier::ERROR );
			}
		} else {
			$state->deleteApiAuthFailed();
		}
	}

	public function pushAuthFailedMessage() {
		if ( $this->container->getApiState()->isApiAuthFailed() ) {
			$authFailedMessage = $this->prepareAuthFailedMessage();
			$this->container->getNotifier()->push( $authFailedMessage, AdminNotifier::ERROR, false );
		}
	}

	public function pushNoDefaultBoardMessage() {
		if ( $this->container->getApiState()->isConnected( 'v3' ) ) {
			if ( ! $this->container->getPinterestIntegration()->get_option( 'board' ) ) {
				$noDefaultBoardMessage = $this->prepareNoDefaultBoardMessage();
				$this->container->getNotifier()->push( $noDefaultBoardMessage, AdminNotifier::WARNING, false );
			}
		}
	}

	/**
	 * Prepare auth failed message
	 *
	 * @return string
	 */
	private function prepareAuthFailedMessage() {
		/* translators: %s is replaced with settings page url*/
		$authFailedMessage = sprintf( __(
			'Pinterest authorization failed. Please, try to reconnect again on <a href="%s">this</a> page.', 'woocommerce-pinterest' ),
			esc_url( $this->pinterestIntegration->getSettingsPageUrl() )
		);

		return $authFailedMessage;
	}

	/**
	 * Prepare No default board message
	 *
	 * @return string
	 */
	private function prepareNoDefaultBoardMessage() {

		/* translators: %s is replaced with settings page url */
		$noDefaultBoardMessage = sprintf( __(
			'You haven\'t selected a default Pinterest board yet. Pins without selected boards will be automatically skipped. Please, select your default board <a href="%s">here</a>.', 'woocommerce-pinterest' ),
			esc_url( $this->pinterestIntegration->getSettingsPageUrl() )
		);

		return $noDefaultBoardMessage;
	}
}
