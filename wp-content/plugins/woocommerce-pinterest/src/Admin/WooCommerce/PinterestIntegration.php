<?php namespace Premmerce\WooCommercePinterest\Admin\WooCommerce;

use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\BoardsSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\CatalogSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\DomainVerificationSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\GeneralSettingsSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\IntegrationSectionInterface;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\PinterestAccountSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\PinTimeSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\SaveButtonSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\TagsSection;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\TrackConversionSection;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \WC_Integration;
use \DateTime;

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

	/**
	 * ServiceContainer instance
	 *
	 * @var ServiceContainer
	 */
	private $container;

	/**
	 * IntegrationSectionInterface instances array
	 *
	 * @var IntegrationSectionInterface[]
	 */
	private $sections;

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
		$this->method_title                 = __('Pinterest for WooCommerce', 'woocommerce-integration-demo');
		$this->userDefinedTagsFetchingOrder = (array) $this->get_option('tags_fetching_strategy');
		$this->loadSections();

		// Load the settings.
		$this->init_settings();
		$this->init_form_fields();

		$this->registerImageSize();

		// Actions.
		add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
		add_action('admin_post_woocommerce_pinterest_verify_domain', array($this, 'handleDomainVerification'));
		add_action('admin_post_woocommerce_pinterest_update_boards', array($this, 'handleUpdateBoards'));
		add_action('admin_post_woocommerce_pinterest_generate_catalog', array($this, 'handleCatalogGenerationButton'));

		add_filter('woocommerce_settings_api_form_fields_pinterest', array($this, 'sortPinterestTagsSources'));

		add_action('admin_init', array($this, 'pushSkippedProductsMessage'));

		add_filter('woocommerce_regenerate_images_intermediate_image_sizes', array($this, 'addPinterestImageToWcRegeneration'));

	}

	private function loadSections() {
		$this->sections = array(
			PinterestAccountSection::class => new PinterestAccountSection(),
			DomainVerificationSection::class => new DomainVerificationSection($this->container->getApiState(),
				$this->container->getFileManager()),
			PinTimeSection::class => new PinTimeSection($this),
			GeneralSettingsSection::class => new GeneralSettingsSection($this,
				$this->container->getFileManager(),
				$this->container->getApiState(),
				$this->container->getDescriptionPlaceholders()
			),
			BoardsSection::class => new BoardsSection(),
			CatalogSection::class => new CatalogSection(),
			SaveButtonSection::class => new SaveButtonSection(),
			TagsSection::class => new TagsSection($this->container->getPinterestTagsController()),
			TrackConversionSection::class => new TrackConversionSection()
		);
	}

	/**
	 * Register new size for pinterest images.
	 */
	protected function registerImageSize() {
		$imageSizeOptions = $this->get_option('pinterest_image_size');

		add_image_size('pinterest_image', $imageSizeOptions['w'], $imageSizeOptions['h'], $imageSizeOptions['crop']);
	}

	/**
	 * Initialize integration settings form fields.
	 *
	 */
	public function init_form_fields() {
		foreach ($this->sections as $section) {
			$this->form_fields[$section->getSlug()] = array(
				'title' => $section->getTitle(),
				'type' => 'title',
			);

			foreach ($section->getFields() as $slug => $field) {
				$this->form_fields[$slug] = $field;
			}
		}
	}

	/**
	 * Print html for v1 connection button field
	 *
	 * @param string $fieldKey
	 * @param array $params
	 *
	 * @return string
	 */
	public function generate_v1_connection_button_html( $fieldKey, $params) {
		return $this->renderConnectionButton('v1', $fieldKey, $params);
	}

	/**
	 * Print html for v3 connection button field
	 *
	 * @param string $fieldKey
	 * @param array $params
	 *
	 * @return string
	 */
	public function generate_v3_connection_button_html( $fieldKey, $params) {
		return $this->renderConnectionButton('v3', $fieldKey, $params);
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
	public function renderConnectionButton( $type, $fieldKey, $params = array()) {
		$state = $this->container->getApiState();


		$v1User          = $state->isConnected('v1') ? $state->getUser() : null;
		$v1UserFirstName = $v1User ? $v1User['first_name'] : '';

		return $this->container->getFileManager()->renderTemplate('admin/woocommerce/connection-button.php', array(
			'field_key' => $fieldKey,
			'state' => $state,
			'stateIcon' => $this->container->getFileManager()->renderTemplate('admin/state.php', array('stateMessage' => $state->getStateMessage())),
			'type' => $type,
			'data' => $params,
			'userName' => $v1UserFirstName
		));
	}

	/**
	 * Print html for domain_verification field
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_domain_verification_html( $key, $data) {
		return $this->container->getFileManager()->renderTemplate('admin/woocommerce/domain-verification.php', array(
			'state' => $this->container->getApiState(),
			'domain' => $this->getDomain(),
			'key' => $this->get_field_key($key),
			'data' => $data,
		));
	}

	/**
	 * Print html for category board table
	 *
	 * @param $key
	 * @param $data
	 * @return string
	 */
	public function generate_category_board_table_html( $key, $data) {
		$categoryBoardTable = $this->container->getCategoryBoardTable();
		$categoryBoardTable->prepare_items();

		if (! $this->container->getApiState()->isConnected('v1')) {
			$connectionNeededMessage = __('This settings are only available when connected to Pinterest API Base level', 'woocommerce-pinterest');
			return '<span class="woo-pinterest-orange">' . $connectionNeededMessage . '</span>';
		}

		return $this->container
		->getFileManager()
		->renderTemplate('admin/woocommerce/term-relations-table/category-board-table/category-board-table.php', array(
			'table' => $categoryBoardTable
		)
		);
	}

	public function generate_catalog_table_html( $key, $data) {
		$catalogTable = $this->container->getCatalogTable();
		$catalogTable->prepare_items();

		return $this->container
			->getFileManager()
			->renderTemplate('admin/woocommerce/term-relations-table/google-category-table/google-category-table.php', array(
			   'table' => $catalogTable
			));
	}

	public function generate_pinterest_generate_catalog_fields_html( $key, $data) {
		$catalogGenerationTaskManager = $this->container->getCatalogGenerationTaskManager();

		return $this->container->getFileManager()
			->renderTemplate('admin/woocommerce/catalog-generation-fields.php', array(
				'data' => $data,
				'catalogGenerated' => $catalogGenerationTaskManager->catalogGenerated(),
				'catalogFileExists' => file_exists($catalogGenerationTaskManager->getCatalogFilePath()),
				'catalogFilePath' => $catalogGenerationTaskManager->getCatalogFileUrl(),
				'generatedRowsNumber' => $catalogGenerationTaskManager->getCatalogRowsNumber()
			));
	}

	public function generate_pinterest_catalog_updating_frequency_html( $key, $data) {
		$currentFrequency = $this->get_option($data['type'], array('days' => '1', 'time' => '00:00'));

		return $this->container->getFileManager()
			->renderTemplate('admin/woocommerce/catalog-updating-frequency.php',
				array(
					'title' => $data['title'],
					'days' => $currentFrequency['days'],
					'time' => $currentFrequency['time'],
					'nextScheduledTime' => ServiceContainer::getInstance()->getCatalogGenerationTaskManager()->getNextScheduledCatalogGenerationTimestamp()
				)
			);
	}


	public function handleCatalogGenerationButton() {
		$this->container->getCatalogGenerationTaskManager()->reGenerateCatalog();

		wp_safe_redirect($this->getSettingsPageUrl());
	}

	/**
	 * Domain verification handler
	 */
	public function handleDomainVerification() {
		$api = $this->container->getApi();

		try {
			$api->setDomain($this->getDomain());

			$this->verifyDomain();
		} catch (PinterestApiException $e) {
			if ($e->getCode() === 400) {
				//Website already exists
				$this->verifyDomain();
			} else {
				$logger = $this->container->getLogger();
				$logger->logPinterestException($e);
				$this->container->getNotifier()->flash($e->getMessage(), AdminNotifier::ERROR);
			}
		}

		wp_safe_redirect($this->getSettingsPageUrl());
	}


	public function process_admin_options() {
		try {
			$categoryBoardRelations = filter_input(INPUT_POST,
				'woocommerce-pinterest-category-board-relations',
				FILTER_SANITIZE_STRING,
				FILTER_FORCE_ARRAY);

			if(is_array($categoryBoardRelations)){
				$this->container->getBoardRelationModel()->updateCategoryBoardsRelations($categoryBoardRelations);
			}

			$wcCategoryGoogleCategoryRelations = filter_input(INPUT_POST,
				self::GOOGLE_CATEGORIES_MAPPING_INPUT_NAME,
				FILTER_SANITIZE_NUMBER_INT,
				FILTER_FORCE_ARRAY);


			$this->container->getGoogleCategoriesRelationsModel()->updateCategoriesRelations($wcCategoryGoogleCategoryRelations);


			$catalogAutoUpdatingEnabled = null !== filter_input(INPUT_POST, 'woocommerce_pinterest_enable_catalog_auto_updating', FILTER_SANITIZE_STRING);

			$taskManager = $this->container->getCatalogGenerationTaskManager();

			if ($catalogAutoUpdatingEnabled) {
				$postedCatalogUpdatedFrequency = filter_input(INPUT_POST, 'woocommerce_pinterest_pinterest_catalog_updating_frequency', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
				$catalogUpdatingFrequency      = $this->sections[CatalogSection::class]->sanitizeCatalogUpdatingFrequency($postedCatalogUpdatedFrequency);
				$taskManager->scheduleAutoUpdating($catalogUpdatingFrequency['time']);
			} else {
				$taskManager->unscheduleAutoUpdating();
			}

			parent::process_admin_options();
		} catch (PinterestModelException $e) {
			$container = ServiceContainer::getInstance();

			$container->getLogger()->logPinterestException($e);
			$container->getNotifier()->flash(__('Pinterest for WooCommerce options processing failed.',
				'woocommerce-pinterest'),
				AdminNotifier::ERROR);
		}

	}

	/**
	 * Process verification
	 */
	public function verifyDomain() {
		$api      = $this->container->getApi();
		$notifier = $this->container->getNotifier();

		try {
			$response = $api->getVerificationCode($this->getDomain());
			$data     = $response->getData();
			if (isset($data['verification_code'])) {
				$code = $data['verification_code'];
				$this->update_option('verification_code', $code);
				$response = $api->verifyDomain($this->getDomain());

				if ($response->getCode() === 200) {
					$message = __('Your domain has been submitted for verification', 'woocommerce-pinterest');
					$notifier->flash($message, AdminNotifier::SUCCESS);
				}
			} else {
				$message = __('Failed to fetch the verification code', 'woocommerce-pinterest');

				$notifier->flash($message, AdminNotifier::ERROR);
			}
		} catch (PinterestApiException $e) {
			$logger = $this->container->getLogger();
			$logger->logPinterestException($e);
			$notifier->flash($e->getMessage(), AdminNotifier::ERROR);
		}
	}

	/**
	 * Handle update boards admin action
	 */
	public function handleUpdateBoards() {
		$this->updateBoards();

		wp_safe_redirect($this->getSettingsPageUrl());
	}

	/**
	 * Refresh boards list
	 *
	 * @return bool
	 */
	public function updateBoards() {
		$notifier = $this->container->getNotifier();

		try {
			$boards = $this->container->getApi()->getBoards()->getData();

			$boards = $this->filterBoards($boards);

			$result = $this->update_option('boards', $boards);
			$notifier->flash(__('Boards list updated', 'woocommerce-pinterest'));

		} catch (PinterestApiException $e) {
			$logger = $this->container->getLogger();
			$logger->logPinterestException($e);
			$notifier->flash($e->getMessage(), AdminNotifier::ERROR);
			$result = false;
		}

		return $result;
	}

	/**
	 * Sort Pinterest tags sources
	 *
	 * @param array $formFields
	 * @return array
	 */
	public function sortPinterestTagsSources( array $formFields) {
		$userDefinedOrder = $this->userDefinedTagsFetchingOrder;

		if ($userDefinedOrder) {
			$userDefinedOrder = array_reverse($userDefinedOrder);
			$options          = array_flip($formFields['tags_fetching_strategy']['options']);

			foreach ($userDefinedOrder as $optionFromUser) {
				$key = array_search($optionFromUser, $options, true);
				if ($key) {
					unset($options[$key]);
					$options = array($key => $optionFromUser) + $options;
				}
			}

			$formFields['tags_fetching_strategy']['options'] = array_flip($options);
		}

		return $formFields;
	}

	/**
	 * Current domain for verification
	 *
	 * @return string
	 */
	protected function getDomain() {
		$domain = isset($_SERVER['SERVER_NAME']) ? sanitize_text_field($_SERVER['SERVER_NAME']) : wp_parse_url(home_url())['host'];
		return $domain;
	}

	/**
	 * Filter Pinterest boards list to remove hidden board _products.
	 *
	 * @param array $boards
	 *
	 * @return array
	 */
	private function filterBoards( array $boards) {

		foreach ($boards as $key => $board) {
			$boardUrlParts = array_filter(explode('/', $board['url']));
			if ('_products' === end($boardUrlParts)) {
				unset($boards[$key]);
				break;
			}
		}

		return array_values($boards);
	}

	/**
	 * Print html for Pinterest Size field
	 *
	 * @param $key
	 * @param $field
	 *
	 * @return string
	 */
	protected function generate_pinterest_size_html( $key, $field) {
		$pinterestSize = $this->get_option($key);

		return $this->container->getFileManager()->renderTemplate('admin/woocommerce/pinterest-image-size.php',
			array(
					'key' => $key,
					'field' => $field,
					'fieldKey' => $this->get_field_key($key),
					'pinterestSize' => $pinterestSize,

			));
	}

	/**
	 * Print html for sections titles
	 *
	 * @param string $key
	 * @param array $data
	 *
	 * @return string
	 */
	public function generate_title_html( $key, $data) {
		$closedBoxes = $this->getClosedSettingsBoxesIds();

		$data['class'] = in_array($key, $closedBoxes, true ) ? 'closed' : '';

		return parent::generate_title_html($key, $data);
	}

	/**
	 * Check if defer pinning enabled
	 *
	 * @return bool
	 */
	public function isEnableDeferPinning() {
		return $this->get_option('pin_time') === 'defer';
	}

	/**
	 * Return task running date
	 *
	 * @return DateTime|null
	 */
	public function getExecuteDate() {
		return $this->sections[PinTimeSection::class]->getExecuteDate();
	}

	/**
	 * Get defer params
	 *
	 * @return array
	 */
	public function getDeferParams() {
		return $this->sections[PinTimeSection::class]->getDeferParams();
	}


	public function pushSkippedProductsMessage() {
		$catalogGenerationTaskManager = ServiceContainer::getInstance()->getCatalogGenerationTaskManager();
		$skippedList                  = $catalogGenerationTaskManager->getSkipperProductsList();

		if ($skippedList) {
			$catalogGenerationTaskManager->clearSkippedProductsList();

			$logsLink = admin_url('admin.php?page=wc-status&tab=logs');
			$catalogsDocumentationLink = 'https://help.pinterest.com/en/business/article/before-you-get-started-with-catalogs';

			/* translators: '%d' is replaced with skipped products number*/
			$message = _n('During the last catalog generation, %d product was skipped. ',
                    'During the last catalog generation, %d products were skipped. ',
                    count($skippedList),
                    'woocommerce-pinterest' ) .
                __(sprintf("The missing fields can be viewed in the logs <a href='%s' target='_blank'>here</a>", $logsLink) . '. '.
                sprintf("Also, check <a href='%s' target='_blank'>Pinterest documentation about Catalogs</a>", $catalogsDocumentationLink) . '. ' .
				"It mostly happens because required fields weren't filled in. " .
				"If you don't know what fields to fill, please read the corresponding documentation pages.",
			'woocommerce-pinterest'
			);

			$formattedMessage = sprintf($message, count($skippedList));

			ServiceContainer::getInstance()->getNotifier()->flash($formattedMessage, AdminNotifier::WARNING, true);
		}
	}

	/**
	 * Return unescaped plugin settings page url
	 *
	 * @param string $anchor
	 *
	 * @return string
	 */
	public function getSettingsPageUrl( $anchor = '') {
		$url = admin_url(self::SETTINGS_PAGE_URL);

		if ($anchor) {
			$url .= "#{$anchor}";
		}

		return $url;
	}

	/**
	 * Update closed settings boxes ids
	 *
	 * @param array $changedBoxData
	 */
	public function updateClosedSettingsBoxesIds( array $changedBoxData) {
		$changedSettingsBoxId = $changedBoxData['sectionId'];
		$closedBoxes          = $this->getClosedSettingsBoxesIds();

		if ($changedBoxData['closed']) {
			array_push($closedBoxes, $changedSettingsBoxId);
			$closedBoxes = array_unique($closedBoxes);
		} else {
			$index = array_search($changedSettingsBoxId, $closedBoxes, true);

			if (false !== $index) {
				unset($closedBoxes[$index]);
			}
		}

		$this->update_option($this->settingsPageOpenedBoxesOptionName, $closedBoxes);
	}

	/**
	 * Return closed settings boxes ids
	 *
	 * @return string[]
	 */
	private function getClosedSettingsBoxesIds() {
		$settingsBoxesIds = (array) $this->get_option($this->settingsPageOpenedBoxesOptionName, array());

		return array_map('sanitize_key', $settingsBoxesIds);
	}

	/**
	 * Make WooCommerce to regenerate images for Pinterest
	 *
	 * @param array $sizes
	 *
	 * @return array
	 */
	public function addPinterestImageToWcRegeneration( array $sizes) {
		array_push($sizes, 'pinterest_image');
		return $sizes;
	}


}
