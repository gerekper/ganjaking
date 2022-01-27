<?php namespace Premmerce\WooCommercePinterest\Admin\WooCommerce;

use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\BoardsSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\CatalogSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\DebugSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\DomainVerificationSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\GeneralSettingsSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\IntegrationSectionInterface;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\PinAllSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\PinterestAccountSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\PinTimeSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\SaveButtonSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\TagsSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\TrackConversionSection;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \WC_Integration;
use \DateTime;
use WC_Settings_API;

/**
 * Class PinterestIntegration
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce
 *
 * This class is responsible for creating Woocommerce Integration. It keeps user settings and data from Pinterest.
 */
class PinterestIntegration extends WC_Integration {

	const SETTINGS_PAGE_URL = 'admin.php?page=wc-settings&tab=integration&section=pinterest';

	//@todo: check if this is really should be there
	const GOOGLE_CATEGORIES_MAPPING_INPUT_NAME = 'woocommerce-pinterest-google-categories-mapping';

	const CATEGORIES_PER_PAGE_BASE_USER_META_KEY = 'woocommerce_page_wc_settings_per_page';

	/**
	 * ServiceContainer instance
	 *
	 * @var ServiceContainer
	 */
	private $container;

	/**
	 * Sections which will be displayed
	 *
	 * @var IntegrationSectionInterface[]
	 */
	private $sections;

	/**
	 * All loaded sections
	 *
	 * @var IntegrationSectionInterface[]
	 *
	 *
	 */
	private $loadedSections;

	/**
	 * User defined tags fetching order array
	 *
	 * @var array
	 */
	private $userDefinedTagsFetchingOrder;

	/**
	 * Settings option name
	 *
	 * @var string
	 */
	private $settingsPageOpenedBoxesOptionName = 'settings-page-opened-boxes';

	/**
	 * PinterestIntegration constructor.
	 */
	public function __construct() {
		$this->container                    = ServiceContainer::getInstance();
		$this->id                           = 'pinterest';
		$this->method_title                 = __( 'Pinterest for WooCommerce', 'woocommerce-integration-demo' );
		$this->userDefinedTagsFetchingOrder = (array) $this->get_option( 'tags_fetching_strategy' );
		$this->loadSections();

		// Load the settings.
		$this->init_settings();
		$this->init_form_fields();

		$this->registerImageSize();

		// Actions.
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'admin_post_woocommerce_pinterest_verify_domain', array( $this, 'handleDomainVerification' ) );
		add_action( 'admin_post_woocommerce_pinterest_update_boards', array( $this, 'handleUpdateBoards' ) );
		add_action( 'admin_post_woocommerce_pinterest_generate_catalog', array(
			$this,
			'handleCatalogGenerationButton'
		) );

		add_filter( 'woocommerce_settings_api_form_fields_pinterest', array( $this, 'sortPinterestTagsSources' ) );

		add_action( 'admin_init', array( $this, 'pushSkippedProductsMessage' ) );

		add_filter( 'woocommerce_regenerate_images_intermediate_image_sizes', array(
			$this,
			'addPinterestImageToWcRegeneration'
		) );

		//Screen options
		add_action( 'load-woocommerce_page_wc-settings', array( $this, 'registerScreenOptions' ) );
		add_filter( 'set-screen-option', array( $this, 'savePerPageOption' ), 10, 3 );

	}

	/**
	 * Load settings sections
	 *
	 * @todo: check if we always need to load them all
	 */
	private function loadSections() {
		$this->sections = array();


		try {
			$mainSections = array(
				PinterestAccountSection::class   => new PinterestAccountSection(),
				DomainVerificationSection::class => new DomainVerificationSection( $this->container->getApiState(),
					$this->container->getFileManager() ),
				GeneralSettingsSection::class    => new GeneralSettingsSection( $this,
					$this->container->getFileManager(),
					$this->container->getApiState(),
					$this->container->getDescriptionPlaceholders()
				),
				PinTimeSection::class            => new PinTimeSection( $this ),
				SaveButtonSection::class         => new SaveButtonSection(),
				TagsSection::class               => new TagsSection( $this->container->getPinterestTagsController() ),
				TrackConversionSection::class    => new TrackConversionSection(),
				DebugSection::class              => new DebugSection()
			);

		} catch ( PinterestException $e ) {
			$this->container->getLogger()->logPinterestException( $e );
		}


		$boardsSections = array(
			BoardsSection::class => new BoardsSection(),
			PinAllSection::class => new PinAllSection()
		);

		$catalogSections = array( CatalogSection::class => new CatalogSection() );

		if ( 'main-settings' === $this->getPinterestTab() ) {

			$this->sections = $mainSections;

		} elseif ( 'boards-settings' === $this->getPinterestTab() ) {

			$this->sections = $boardsSections;

		} elseif ( 'catalog-settings' === $this->getPinterestTab() ) {

			$this->sections = $catalogSections;

		}

		$this->loadedSections = array_merge( $mainSections, $boardsSections, $catalogSections );
	}

	/**
	 * Get loaded settings section
	 *
	 * @param string $sectionName
	 *
	 * @return IntegrationSectionInterface|null
	 */
	public function getLoadedSection( $sectionName ) {
		return isset( $this->loadedSections[ $sectionName ] ) ? $this->loadedSections[ $sectionName ] : null;
	}

	private function getPinterestTab() {
		$tab = filter_input( INPUT_GET, 'pinterest-tab', FILTER_SANITIZE_STRING );

		return $tab ? $tab : 'main-settings';
	}

	/**
	 * Register new size for pinterest images.
	 */
	protected function registerImageSize() {
		$imageSizeOptions = $this->get_option( 'pinterest_image_size', array(
			'w'    => '238',
			'h'    => '284',
			'crop' => 'yes'
		) );

		add_image_size( 'pinterest_image', $imageSizeOptions['w'], $imageSizeOptions['h'], $imageSizeOptions['crop'] );
	}

	/**
	 * Initialize integration settings form fields.
	 *
	 */
	public function init_form_fields() {
		foreach ( $this->sections as $section ) {
			$this->form_fields[ $section->getSlug() ] = array(
				'title' => $section->getTitle(),
				'type'  => 'title',
			);

			foreach ( $section->getFields() as $slug => $field ) {
				$this->form_fields[ $slug ] = $field;
			}
		}
	}

	/**
	 * Print html for v3 connection button field
	 *
	 * @param string $fieldKey
	 * @param array $params
	 *
	 * @return string
	 */
	public function generate_v3_connection_button_html( $fieldKey, $params ) {
		return $this->renderConnectionButton( 'v3', $fieldKey, $params );
	}

	/**
	 * Render connection button
	 *
	 * @param string $type
	 * @param string $fieldKey
	 * @param array $params
	 *
	 * @return string
	 */
	public function renderConnectionButton( $type, $fieldKey, $params = array() ) {
		$state = $this->container->getApiState();

		$v3User          = $state->isConnected( 'v3' ) ? $state->getUser() : null;
		$v3UserFirstName = $v3User ? $v3User['first_name'] : '';

		return $this->container->getFileManager()->renderTemplate( 'admin/woocommerce/connection-button.php', array(
			'field_key' => $fieldKey,
			'state'     => $state,
			'stateIcon' => $this->container->getFileManager()->renderTemplate( 'admin/state.php', array( 'stateMessage' => $state->getStateMessage() ) ),
			'type'      => $type,
			'data'      => $params,
			'userName'  => $v3UserFirstName
		) );
	}

	/**
	 * Print html for domain_verification field
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_domain_verification_html( $key, $data ) {
		return $this->container->getFileManager()->renderTemplate( 'admin/woocommerce/domain-verification.php', array(
			'state'               => $this->container->getApiState(),
			'domain'              => $this->getDomain(),
			'key'                 => $this->get_field_key( $key ),
			'data'                => $data,
			'is_already_verified' => ! empty( $this->get_option( 'verification_code', '' ) )
		) );
	}

	/**
	 * Print html for category board table
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return string
	 */
	public function generate_category_board_table_html( $key, $data ) {
		$categoryBoardTable = $this->container->getCategoryBoardTable();
		$categoryBoardTable->setPerPage( $this->getPerPageOptionForCurrentTab() );
		$categoryBoardTable->prepare_items();

		if ( ! $this->container->getApiState()->isConnected( 'v3' ) ) {
			$connectionNeededMessage = __( 'This settings are only available when connected to Pinterest API Base level', 'woocommerce-pinterest' );

			return '<span class="woo-pinterest-orange">' . $connectionNeededMessage . '</span>';
		}

		return $this->container
			->getFileManager()
			->renderTemplate( 'admin/woocommerce/term-relations-table/category-board-table/category-board-table.php', array(
					'table' => $categoryBoardTable
				)
			);
	}

	/**
	 * Print html for pin all button
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return string
	 */
	public function generate_pin_all_button_html( $key, $data ) {
		return $this->container->getFileManager()->renderTemplate( 'admin/woocommerce/pin-all-button.php', array(
			'data' => $data,
		) );
	}

	/**
	 * Create and set up boards
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return string
	 */
	public function generate_create_boards_button_html( $key, $data ) {
		return $this->container->getFileManager()->renderTemplate( 'admin/woocommerce/create-boards-button.php', array(
			'data' => $data,
		) );
	}

	public function generate_catalog_table_html( $key, $data ) {
		$catalogTable = $this->container->getCatalogTable();
		$catalogTable->setPerPage( $this->getPerPageOptionForCurrentTab() );
		$catalogTable->prepare_items();

		return $this->container
			->getFileManager()
			->renderTemplate( 'admin/woocommerce/term-relations-table/google-category-table/google-category-table.php', array(
				'table' => $catalogTable
			) );
	}

	public function generate_pinterest_generate_catalog_fields_html( $key, $data ) {
		$catalogGenerationTaskManager = $this->container->getCatalogGenerationTaskManager();

		return $this->container->getFileManager()
							   ->renderTemplate( 'admin/woocommerce/catalog-generation-fields.php', array(
								   'data'                => $data,
								   'catalogGenerated'    => $catalogGenerationTaskManager->catalogGenerated(),
								   'catalogFileExists'   => file_exists( $catalogGenerationTaskManager->getCatalogFilePath() ),
								   'catalogFilePath'     => $catalogGenerationTaskManager->getCatalogFileUrl(),
								   'generatedRowsNumber' => $catalogGenerationTaskManager->getCatalogRowsNumber()
							   ) );
	}

	public function generate_pinterest_catalog_updating_frequency_html( $key, $data ) {
		$currentFrequency = $this->get_option( $data['type'], array( 'days' => '1', 'time' => '00:00' ) );

		return $this->container->getFileManager()
							   ->renderTemplate( 'admin/woocommerce/catalog-updating-frequency.php',
								   array(
									   'title'             => $data['title'],
									   'days'              => $currentFrequency['days'],
									   'time'              => $currentFrequency['time'],
									   'nextScheduledTime' => ServiceContainer::getInstance()->getCatalogGenerationTaskManager()->getNextScheduledCatalogGenerationTimestamp()
								   )
							   );
	}


	public function handleCatalogGenerationButton() {
		$this->container->getCatalogGenerationTaskManager()->reGenerateCatalog();

		wp_safe_redirect( wp_get_referer() );
	}

	/**
	 * Domain verification handler
	 */
	public function handleDomainVerification() {
		$api = $this->container->getApi();

		try {
			$api->setDomain( $this->getDomain() );

			$this->verifyDomain();
		} catch ( PinterestApiException $e ) {
			if ( $e->getCode() === 400 ) {
				//Website already exists
				$this->verifyDomain();
			} else {
				$logger = $this->container->getLogger();
				$logger->logPinterestException( $e );
				$this->container->getNotifier()->flash( $e->getMessage(), AdminNotifier::ERROR );
			}
		}

		wp_safe_redirect( $this->getSettingsPageUrl() );
	}


	public function process_admin_options() {
		$currentPinterestTab = $this->getPinterestTab();

		try {

			if ( 'boards-settings' === $currentPinterestTab ) {
				$this->savePinterestCategoryBoardsMappings();
			} elseif ( 'catalog-settings' === $currentPinterestTab ) {
				$this->saveCatalogSettings();
			}

		} catch ( PinterestModelException $e ) {
			$container = ServiceContainer::getInstance();

			$container->getLogger()->logPinterestException( $e );
			$container->getNotifier()->flash( __( 'Pinterest for WooCommerce options processing failed.',
				'woocommerce-pinterest' ),
				AdminNotifier::ERROR );
		}

		parent::process_admin_options();
	}

	/**
	 * Save Woocommerce category to Pinterest Boards relations
	 *
	 * @throws PinterestModelException
	 */
	private function savePinterestCategoryBoardsMappings() {
		$categoryBoardRelations = filter_input( INPUT_POST,
			'woocommerce-pinterest-category-board-relations',
			FILTER_SANITIZE_STRING,
			FILTER_FORCE_ARRAY );

		if ( is_array( $categoryBoardRelations ) ) {
			$this->container->getBoardRelationModel()->updateCategoryBoardsRelations( $categoryBoardRelations );
		}
	}

	/**
	 * Save Pinterest catalog settings
	 *
	 * @throws PinterestModelException
	 */
	private function saveCatalogSettings() {
		$wcCategoryGoogleCategoryRelations = filter_input( INPUT_POST,
			self::GOOGLE_CATEGORIES_MAPPING_INPUT_NAME,
			FILTER_SANITIZE_NUMBER_INT,
			FILTER_FORCE_ARRAY );


		$relations = $wcCategoryGoogleCategoryRelations ? $wcCategoryGoogleCategoryRelations : array();
		$this->container->getGoogleCategoriesRelationsModel()->updateCategoriesRelations( $relations );
		$catalogAutoUpdatingEnabled = null !== filter_input( INPUT_POST, 'woocommerce_pinterest_enable_catalog_auto_updating', FILTER_SANITIZE_STRING );
		$taskManager                = $this->container->getCatalogGenerationTaskManager();

		if ( $catalogAutoUpdatingEnabled ) {
			$postedCatalogUpdatedFrequency = filter_input( INPUT_POST, 'woocommerce_pinterest_pinterest_catalog_updating_frequency', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY );

			/**
			 * Catalog settings section
			 *
			 * @var CatalogSection $catalogSection
			 */
			$catalogSection = $this->getLoadedSection( CatalogSection::class );

			$catalogUpdatingFrequency = $catalogSection->sanitizeCatalogUpdatingFrequency( $postedCatalogUpdatedFrequency );
			$taskManager->scheduleAutoUpdating( $catalogUpdatingFrequency['time'] );
		} else {
			$taskManager->unscheduleAutoUpdating();
		}
	}

	/**
	 * Process verification
	 */
	public function verifyDomain() {
		$api      = $this->container->getApi();
		$notifier = $this->container->getNotifier();

		try {
			$response = $api->getVerificationCode( $this->getDomain() );
			$data     = $response->getData();
			if ( isset( $data['verification_code'] ) ) {
				$code = $data['verification_code'];
				$this->update_option( 'verification_code', $code );
				$response = $api->verifyDomain( $this->getDomain() );

				if ( $response->getCode() === 200 ) {
					$message = __( 'Your domain has been submitted for verification', 'woocommerce-pinterest' );
					$notifier->flash( $message, AdminNotifier::SUCCESS );
				}
			} else {
				$message = __( 'Failed to fetch the verification code', 'woocommerce-pinterest' );

				$notifier->flash( $message, AdminNotifier::ERROR );
			}
		} catch ( PinterestApiException $e ) {
			$logger = $this->container->getLogger();
			$logger->logPinterestException( $e );
			$notifier->flash( $e->getMessage(), AdminNotifier::ERROR );
		}
	}

	/**
	 * Handle update boards admin action
	 */
	public function handleUpdateBoards() {

		$this->updateBoards();

		wp_safe_redirect( $this->getSettingsPageUrl() );
	}

	/**
	 * Refresh boards list
	 *
	 * @return bool
	 */
	public function updateBoards() {

		$notifier = $this->container->getNotifier();

		try {
			$boardsRequest = $this->container->getApi()->getBoards();
			$boards        = $boardsRequest->getData();

			while ( $boardsRequest->getBookmark() ) {
				$boardsRequest = $this->container->getApi()->getBoards( $boardsRequest->getBookmark() );
				$boards        = array_merge( $boardsRequest->getData(), $boards );
			}

			$boards = $this->filterBoards( $boards );

			$result = $this->update_option( 'boards', $boards );
			$notifier->flash( __( 'Boards list updated', 'woocommerce-pinterest' ) );

		} catch ( PinterestApiException $e ) {
			$logger = $this->container->getLogger();
			$logger->logPinterestException( $e );
			$notifier->flash( $e->getMessage(), AdminNotifier::ERROR );
			$result = false;
		}

		return $result;
	}

	/**
	 * Sort Pinterest tags sources
	 *
	 * @param array $formFields
	 *
	 * @return array
	 */
	public function sortPinterestTagsSources( array $formFields ) {
		$userDefinedOrder = $this->userDefinedTagsFetchingOrder;

		if ( $userDefinedOrder && isset( $formFields['tags_fetching_strategy']['options'] ) ) {
			$userDefinedOrder = array_reverse( $userDefinedOrder );
			$options          = array_flip( $formFields['tags_fetching_strategy']['options'] );

			foreach ( $userDefinedOrder as $optionFromUser ) {
				$key = array_search( $optionFromUser, $options, true );
				if ( $key ) {
					unset( $options[ $key ] );
					$options = array( $key => $optionFromUser ) + $options;
				}
			}

			$formFields['tags_fetching_strategy']['options'] = array_flip( $options );
		}

		return $formFields;
	}

	/**
	 * Current domain for verification
	 *
	 * @return string
	 */
	protected function getDomain() {
		$domain = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( $_SERVER['SERVER_NAME'] ) : wp_parse_url( home_url() )['host'];

		return $domain;
	}

	/**
	 * Filter Pinterest boards list to remove hidden board _products.
	 *
	 * @param array $boards
	 *
	 * @return array
	 */
	private function filterBoards( array $boards ) {

		foreach ( $boards as $key => $board ) {
			$boardUrlParts = array_filter( explode( '/', $board['url'] ) );
			if ( '_products' === end( $boardUrlParts ) ) {
				unset( $boards[ $key ] );
				break;
			}
		}

		return array_values( $boards );
	}

	/**
	 * Print html for Pinterest Size field
	 *
	 * @param $key
	 * @param $field
	 *
	 * @return string
	 */
	protected function generate_pinterest_size_html( $key, $field ) {
		$pinterestSize = $this->get_option( $key );

		return $this->container->getFileManager()->renderTemplate( 'admin/woocommerce/pinterest-image-size.php',
			array(
				'key'           => $key,
				'field'         => $field,
				'fieldKey'      => $this->get_field_key( $key ),
				'pinterestSize' => $pinterestSize,
			) );
	}

	/**
	 * Print html for sections titles
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_title_html( $key, $data ) {
		$closedBoxes = $this->getClosedSettingsBoxesIds();

		$data['class'] = in_array( $key, $closedBoxes, true ) ? 'closed' : '';

		return parent::generate_title_html( $key, $data );
	}

	public function generate_richpins_advanced_html( $key, $data ) {

		$fields = array(
			'brand'                  => 'og:brand',
			'url'                    => 'og:url',
			'title'                  => 'og:title',
			'site_name'              => 'og:site_name',
			'description'            => 'og:description',
			'product_price_amount'   => 'og:price:product:amount',
			'price_standard_amount'  => 'og:price:standard_amount',
			'product_price_currency' => 'og:price:product:currency',
			'availability'           => 'og:availability',
			'type'                   => 'og:type',
		);

		return $this->container->getFileManager()->renderTemplate( 'admin/woocommerce/richpins-advanced.php', array(
			'fields'   => $fields,
			'fieldKey' => $this->get_field_key( $key ),
			'data'     => $this->get_option( $key ),
		) );
	}

	/**
	 * Check if defer pinning enabled
	 *
	 * @return bool
	 */
	public function isEnableDeferPinning() {
		return $this->get_option( 'pin_time' ) === 'defer';
	}

	/**
	 * Return task running date
	 *
	 * @return DateTime|null
	 */
	public function getExecuteDate( $limitPerDay = false ) {
		/**
		 * Pin time section
		 *
		 * @var PinTimeSection $pinTimeSection
		 */
		$pinTimeSection = $this->getLoadedSection( PinTimeSection::class );

		try {
			$date = $pinTimeSection->getExecuteDate( $limitPerDay );
		} catch ( PinterestException $e ) {
			$this->container->getLogger()->logPinterestException( $e );

			return null;
		}

		return $date;
	}

	/**
	 * Get defer params
	 *
	 * @return array
	 */
	public function getDeferParams() {
		/**
		 * Pin time section
		 *
		 * @var PinTimeSection $pinTimeSection
		 */
		$pinTimeSection = $this->getLoadedSection( PinTimeSection::class );

		return $pinTimeSection->getDeferParams();
	}


	public function pushSkippedProductsMessage() {
		$catalogGenerationTaskManager = ServiceContainer::getInstance()->getCatalogGenerationTaskManager();
		$skippedList                  = $catalogGenerationTaskManager->getSkipperProductsList();

		if ( $skippedList ) {
			$catalogGenerationTaskManager->clearSkippedProductsList();

			$logsLink = admin_url( 'admin.php?page=wc-status&tab=logs' );

			$catalogsDocumentationLink = 'https://help.pinterest.com/en/business/article/before-you-get-started-with-catalogs';

			/* translators: '%d' is replaced with skipped products number*/
			$message = _n( 'During the last catalog generation, %d product was skipped. ',
					'During the last catalog generation, %d products were skipped. ',
					count( $skippedList ),
					'woocommerce-pinterest' ) .
					   /* translators: %s is replaced with logs page link */
					   __( sprintf( "The missing fields can be viewed in the logs <a href='%s' target='_blank'>here</a>", $logsLink ) . '. ' .
						   sprintf( "Also, check <a href='%s' target='_blank'>Pinterest documentation about Catalogs</a>", $catalogsDocumentationLink ) . '. ' .
						   "It mostly happens because required fields weren't filled in. " .
						   "If you don't know what fields to fill, please read the corresponding documentation pages.",
						   'woocommerce-pinterest'
					   );

			$formattedMessage = sprintf( $message, count( $skippedList ) );

			ServiceContainer::getInstance()->getNotifier()->flash( $formattedMessage, AdminNotifier::WARNING, true );
		}
	}

	/**
	 * Return unescaped plugin settings page url
	 *
	 * @param string $anchor
	 *
	 * @return string
	 */
	public function getSettingsPageUrl( $anchor = '' ) {
		$url = admin_url( self::SETTINGS_PAGE_URL );

		if ( $anchor ) {
			$url .= "#{$anchor}";
		}

		return $url;
	}

	/**
	 * Update closed settings boxes ids
	 *
	 * @param array $changedBoxData
	 */
	public function updateClosedSettingsBoxesIds( array $changedBoxData ) {
		$changedSettingsBoxId = $changedBoxData['sectionId'];
		$closedBoxes          = $this->getClosedSettingsBoxesIds();

		if ( $changedBoxData['closed'] ) {
			array_push( $closedBoxes, $changedSettingsBoxId );
			$closedBoxes = array_unique( $closedBoxes );
		} else {
			$index = array_search( $changedSettingsBoxId, $closedBoxes, true );

			if ( false !== $index ) {
				unset( $closedBoxes[ $index ] );
			}
		}

		$this->update_option( $this->settingsPageOpenedBoxesOptionName, $closedBoxes );
	}

	/**
	 * Return closed settings boxes ids
	 *
	 * @return string[]
	 */
	private function getClosedSettingsBoxesIds() {
		$settingsBoxesIds = (array) $this->get_option( $this->settingsPageOpenedBoxesOptionName, array() );

		return array_map( 'sanitize_key', $settingsBoxesIds );
	}

	/**
	 * Make WooCommerce to regenerate images for Pinterest
	 *
	 * @param array $sizes
	 *
	 * @return array
	 */
	public function addPinterestImageToWcRegeneration( array $sizes ) {
		array_push( $sizes, 'pinterest_image' );

		return $sizes;
	}

	public function admin_options() {
		echo '<h2>' . esc_html( $this->get_method_title() ) . '</h2>';

		$this->renderTabs();

		echo wp_kses_post( wpautop( $this->get_method_description() ) );
		echo '<div><input type="hidden" name="section" value="' . esc_attr( $this->id ) . '" /></div>';

		WC_Settings_API::admin_options();
	}

	private function renderTabs() {
		$tabs = array(
			__( 'Main settings', 'woocommerce-pinterest' )              => 'main-settings',
			__( 'Pinterest boards settings', 'woocommerce-pinterest' )  => 'boards-settings',
			__( 'Pinterest catalog settings', 'woocommerce-pinterest' ) => 'catalog-settings'
		);

		$baseUrl     = $this->getSettingsPageUrl();
		$selectedTab = $this->getPinterestTab();

		$this->container->getFileManager()->includeTemplate( 'admin/woocommerce/settings-tabs.php',
			array(
				'tabs'        => $tabs,
				'baseUrl'     => $baseUrl,
				'selectedTab' => $selectedTab
			)
		);
	}

	public function registerScreenOptions() {
		$screensToShowOptions = array( 'catalog-settings', 'boards-settings' );

		if ( in_array( $this->getPinterestTab(), $screensToShowOptions, true ) ) {
			add_screen_option( 'per_page' );
			add_filter( 'woocommerce_page_wc_settings_per_page', array( $this, 'getPerPageOptionForCurrentTab' ) );
		}
	}

	public function getPerPageOptionForCurrentTab() {
		$savedPerPage = get_user_meta(
			get_current_user_id(),
			self::CATEGORIES_PER_PAGE_BASE_USER_META_KEY . '_' . $this->getPinterestTab(),
			true
		);

		return (int) $savedPerPage ? (int) $savedPerPage : 20;
	}

	/**
	 * Save per page option
	 *
	 * @param bool $keep
	 * @param string $option
	 * @param mixed $value
	 *
	 * @return bool|int
	 */
	public function savePerPageOption( $keep, $option, $value ) {
		if ( self::CATEGORIES_PER_PAGE_BASE_USER_META_KEY === $option && $value < 999 ) {
			$metaKey = self::CATEGORIES_PER_PAGE_BASE_USER_META_KEY . '_' . $this->getPinterestTab();
			update_user_meta( get_current_user_id(), $metaKey, (int) $value );
		}

		return $keep;
	}

}
