<?php namespace Premmerce\WooCommercePinterest\Admin\Product;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\Admin;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\Pinterest\DescriptionPlaceholders;
use Premmerce\WooCommercePinterest\Pinterest\PinService;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsTaxonomy;
use WC_Product_Variation;
use WP_Post;

/**
 * Class ProductMetabox
 *
 * Responsible for displaying product metabox and handling product updates
 *
 * @package Premmerce\WooCommercePinterest\Admin
 */
class ProductHandler {

	const PRODUCT_BOARD_META_FIELD = 'woocommerce_pinterest_product_board';

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PinService instance
	 *
	 * @var PinService
	 */
	private $pinService;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinModel
	 */
	private $pinModel;

	/**
	 * PinterestTagsController instance
	 *
	 * @var PinterestTagsController
	 */
	private $pinterestTagsController;

	/**
	 * BoardRelationsModel instance
	 *
	 * @var BoardRelationsModel
	 */
	private $boardRelationsModel;

	/**
	 * DescriptionPlaceholders instance
	 *
	 * @var DescriptionPlaceholders
	 */
	private $descriptionPlaceholders;

	/**
	 * ProductHandler constructor.
	 *
	 * @param FileManager $fileManager
	 * @param PinService $service
	 * @param PinterestIntegration $integration
	 * @param PinModel $pinModel
	 * @param BoardRelationsModel $boardRelationsModel
	 * @param PinterestTagsController $pinterestTagsController
	 *
	 * @param DescriptionPlaceholders $descriptionPlaceholders
	 * @todo: this should be refactored
	 */
	public function __construct(
		FileManager $fileManager,
		PinService $service,
		PinterestIntegration $integration,
		PinModel $pinModel,
		BoardRelationsModel $boardRelationsModel,
		PinterestTagsController $pinterestTagsController,
		DescriptionPlaceholders $descriptionPlaceholders
	) {
		$this->fileManager             = $fileManager;
		$this->pinService              = $service;
		$this->integration             = $integration;
		$this->pinModel                = $pinModel;
		$this->pinterestTagsController = $pinterestTagsController;
		$this->boardRelationsModel     = $boardRelationsModel;
		$this->descriptionPlaceholders = $descriptionPlaceholders;
	}

	/**
	 * Set actions and filters related to this class
	 */
	public function init() {
		add_action('add_meta_boxes', array($this, 'addMetaBox'));

		// Update pin if product data updated
		add_action('updated_post_meta', array($this, 'updateIfPriceChanged'), 999, 4);
		add_action('post_updated', array($this, 'updateIfPostChange'), 999, 3);

		add_action('woocommerce_save_product_variation', array($this, 'saveVariationData'), 10, 2);
		add_action('save_post_product', array($this, 'updateProductBoards'), 9);
		add_action('save_post_product', array($this, 'synchronizePinsOnPostSaving'), 10, 2);

		add_action('deleted_post', array($this, 'deleteAllPinsFromPost'));
		add_action('trashed_post', array($this, 'deleteAllPinsFromPost'));
		add_action('delete_attachment', array($this, 'deleteAllPinsFromAttachment'));


		//Custom products table column
		add_filter('manage_edit-product_columns', array($this, 'addPinnedColumnToProductsTable') );
		add_filter('manage_edit-product_sortable_columns', array($this, 'addPinnedColumnToSortableColumns'));
		add_action('manage_product_posts_custom_column', array($this, 'pinnedColumnContent'), 10, 2);

		// Variations
		add_action('woocommerce_variation_options', array($this, 'addPinDescriptionOption'), 10, 3);
		add_action('woocommerce_product_after_variable_attributes', array($this, 'renderPinDescriptionField'), 10, 3);
	}

	/**
	 * Save pinterest variation data
	 *
	 * @param int $variationId
	 * @param int $i
	 */
	public function saveVariationData( $variationId, $i) {
		$sanitizedHasDescriptionFields = filter_input(INPUT_POST, 'variable_is_pin_description', FILTER_VALIDATE_BOOLEAN, FILTER_FORCE_ARRAY);

		$hasDescription = ! empty($sanitizedHasDescriptionFields[$i]);

		$this->pinService->setVariationHasPinDescription($variationId, $hasDescription);

		$sanitizedPinDescriptionTemplateFields = filter_input(INPUT_POST, 'pin_description_template', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
		$sanitizedPinDescriptionTemplate       = isset($sanitizedPinDescriptionTemplateFields[$i]) ?  sanitize_text_field($sanitizedPinDescriptionTemplateFields[$i]) : '';

		$this->pinService->updatePinDescriptionTemplate($variationId, $sanitizedPinDescriptionTemplate);
	}

	/**
	 * Render variation pin description field
	 *
	 * @param int $loop
	 * @param array $variation_data
	 * @param WP_Post $variation
	 */
	public function renderPinDescriptionField( $loop, $variation_data, $variation) {
		$this->fileManager->includeTemplate('admin/woocommerce/variation-pin-description.php', array(
			'hidden' => ! $this->pinService->isVariationHasPinDescription($variation->ID),
			'loop' => $loop,
			'pinDescription' => $this->pinService->getProductPinDescriptionTemplate($variation->ID),
			'descriptionFieldDescription' => $this->getPinDescriptionFieldDescription()
		));
	}

	/**
	 * Render new option for variations. Does variation have a pin description
	 *
	 * @param int $loop
	 * @param array $variation_data
	 * @param WP_Post $variation
	 */
	public function addPinDescriptionOption( $loop, $variation_data, $variation) {
		$this->fileManager->includeTemplate('admin/woocommerce/variation-description-option.php', array(
				'loop' => $loop,
				'isVariableHasPinDescription' => $this->pinService->isVariationHasPinDescription($variation->ID)
		));
	}

	/**
	 * Add meta box to product create\update page
	 */
	public function addMetaBox() {
		add_meta_box(
			'woocommerce-pinterest-metabox',
			'Pinterest data',
			array($this, 'renderMetabox'),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * Get Pin description field description
	 *
	 * @return string
	 */
	protected function getPinDescriptionFieldDescription() {
		$placeholders = array_keys($this->descriptionPlaceholders->getPlaceholders());

		/* translators: '%s' is replaced with supported placeholders like '{price}', '{title}' etc. */
		return sprintf(__('The following placeholders are supported: %s', 'woocommerce-pinterest'), implode(', ', $placeholders));
	}

	/**
	 * Render meta box at product edit\create page
	 *
	 * @todo move this and all related code to separate class
	 */
	public function renderMetaBox() {
		global $post;

		$dbPins = $this->pinService->getPostPinnedAttachments($post->ID);
		$dbPins = array_map('intval', $dbPins);

		$images = $this->getProductImages($post->ID);

		$images = array_unique(array_merge($images, $dbPins));
		$images = array_map('intval', $images);

		$boards = array();

		foreach ((array) $this->integration->get_option('boards') as $board) {
			$boards[$board['id']] = $board;
		}


		$boardsFromProductCategories = array_unique($this->pinService->getBoardsIdsFromProductCategories($post->ID));

		$productBoards = $this->pinService->getBoardsFromProductSettings($post->ID);

		$pinDescriptionVariables = $this->descriptionPlaceholders->getPlaceholders();



		try {
			$productPinterestTags = $this->pinterestTagsController->getTagsForProduct($post->ID);
			$description          = $this->pinService->getProductPinDescriptionTemplate($post->ID);

			$this->fileManager->includeTemplate(
				'admin/product/metabox.php',
				array(
					'images' => $images,
					'dbPins' => $dbPins,
					'boards' => $boards,
					'productBoards' => $productBoards,
					'boardsFromProductCategories' => $boardsFromProductCategories,
					'productPinterestTags' => $productPinterestTags,
					'description' => $description,
					'descriptionFieldTip' => $this->getPinDescriptionFieldDescription(),
					'post' => $post,
					'tagsBoxRenderingArgs' => $this->getTagsBoxRenderingArgs(),
					'tagsSettingsUrl' => $this->integration->getSettingsPageUrl('woocommerce_pinterest_pinterest_tags_section'),
					'boardsSettingsUrl' => $this->integration->getSettingsPageUrl('woocommerce_pinterest_pinterest_boards_section'),
					'fileManager' => $this->fileManager,
					'pinDescriptionVariables' => $pinDescriptionVariables
				)
			);
		} catch (PinterestException $e) {
			ServiceContainer::getInstance()->getLogger()->logPinterestException($e);
			Admin::printTagsQueryFailedMessage();
		}
	}

	/**
	 * Return arguments for rendering tags box
	 *
	 * @return array
	 */
	private function getTagsBoxRenderingArgs() {
		return array(
			'id' => 'tagsdiv-' . PinterestTagsTaxonomy::PINTEREST_TAGS_TAXONOMY_SLUG,
			'title' => 'Pinterest tags',
			'callback' => 'post_tags_meta_box',
			'args' => array(
				'taxonomy' => PinterestTagsTaxonomy::PINTEREST_TAGS_TAXONOMY_SLUG
			)
		);
	}


	/**
	 * Set update task for this post if price was changed
	 *
	 * @param int $meta_id
	 * @param int $object_id
	 * @param string $meta_key
	 */
	public function updateIfPriceChanged( $meta_id, $object_id, $meta_key) {
		if ('_price' !== $meta_key) {
			return;
		}

		$product = wc_get_product($object_id);

		if ($product instanceof WC_Product_Variation) {
			$object_id = $product->get_parent_id();
		}

		$this->pinService->updateByPost($object_id);
	}

	/**
	 * Set update task for this post if title or content was changed
	 *
	 * @param int $post_id
	 * @param WP_Post $post_after
	 * @param WP_Post $post_before
	 */
	public function updateIfPostChange( $post_id, $post_after, $post_before) {
		if ($post_before->post_title !== $post_after->post_title ||
			$post_before->post_content !== $post_after->post_content) {
			$this->pinService->updateByPost($post_id);
		}
	}

	/**
	 * Call on 'save_post' hook. Sync pinned post with DB. Create task for create and remove pins
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @throws PinterestModelException
	 */
	public function synchronizePinsOnPostSaving( $post_id, $post) {
		if ('publish' !== $post->post_status) {
			return;
		}

		if (! $this->isProductPageFormSaving()) {
			return;
		}

		$pinDescription = filter_input(INPUT_POST, 'woocommerce_pinterest_pin_description_template', FILTER_SANITIZE_STRING);
		$pinDescription = $pinDescription ? $pinDescription : '';
		$pinDescription = sanitize_text_field($pinDescription);

		$selectedAttachments = filter_input(INPUT_POST, 'woocommerce_pinterest_images', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
		$selectedAttachments = $selectedAttachments ? $selectedAttachments : array();

		$this->pinService->synchronize($post_id, $selectedAttachments);

		$this->pinService->updatePinDescriptionTemplate($post_id, $pinDescription);
	}

	/**
	 * Check if we are saving product edit form
	 *
	 * @return bool
	 */
	private function isProductPageFormSaving() {
		$metabox = filter_input(INPUT_POST, 'woocommerce_pinterest_metabox', FILTER_SANITIZE_STRING);

		return ! in_array($metabox, array(false, null), true);
	}

	/**
	 * Save board to pin product
	 *
	 * @param $postId
	 */
	public function updateProductBoards( $postId) {
		try {
			$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

			if ('editpost' === $action) {
				$boardsIds = filter_input(INPUT_POST, self::PRODUCT_BOARD_META_FIELD, FILTER_SANITIZE_STRING,
					FILTER_REQUIRE_ARRAY);

				$boardsIds = $boardsIds ? $boardsIds : array();

				$this->boardRelationsModel->updateProductBoardsRelations($postId, $boardsIds);
			}
		} catch (PinterestModelException $e) {
			$container = ServiceContainer::getInstance();
			$container->getLogger()->logPinterestException($e);
			$container->getNotifier()->flash(__('Saving Pinterest boards failed', 'woocommerce-pinterest'), AdminNotifier::ERROR);
		}
	}

	/**
	 * Delete pins by post id
	 *
	 * @param $postId
	 */
	public function deleteAllPinsFromPost( $postId) {
		$this->pinService->deletePinsByPostId($postId);
	}

	/**
	 * Delete pins by attachment id
	 *
	 * @param $attachmentId
	 */
	public function deleteAllPinsFromAttachment( $attachmentId) {
		$this->pinService->deletePinsByAttachmentId($attachmentId);
	}

	/**
	 * Add Pinned column to Woocommerce products table
	 *
	 * @param array $columns
	 * @return array
	 */
	public function addPinnedColumnToProductsTable( $columns) {
		$dataTip           = __('Pinned', 'woocommerce-pinterest');
		$columns['pinned'] = '<span class="dashicons dashicons-sticky parent-tips woocommerce-pinterest-pin-status-column-dashicon " data-tip="' . $dataTip . '"></span></span>';

		return $columns;
	}

	/**
	 * Render content of Pinned column
	 *
	 * @param $column
	 * @param $productId
	 */
	public function pinnedColumnContent( $column, $productId ) {
		if ('pinned' === $column) {
			if ($this->pinModel->getPinsByPost($productId)) {
				$class   = 'pinned';
				$dataTip = __('Yes', 'woocommerce-pinterest');
			} else {
				$class   = 'not-pinned';
				$dataTip = __('No', 'woocommerce-pinterest');
			}

			echo '<span class="dashicons dashicons-sticky tips woocommerce-pinterest-pin-status-cell-dashicon ' . esc_attr($class) . '" data-tip="' . esc_attr($dataTip) . '"></span>';
		}
	}

	/**
	 * Make Pinned column sortable
	 *
	 * @param $sortableColumns
	 *
	 * @return mixed
	 */
	public function addPinnedColumnToSortableColumns( $sortableColumns) {
		$sortableColumns['pinned'] = array('pinned', false);

		return $sortableColumns;
	}

	/**
	 * All product images (Featured, Gallery, Variation).
	 *
	 * @param $postId
	 *
	 * @return array
	 */
	private function getProductImages( $postId) {
		$images   = array();
		$product  = wc_get_product($postId);
		$featured = (int) get_post_thumbnail_id($product->get_id());

		if ($featured) {
			$images[] = $featured;
		}

		// Add gallery images
		$images = array_merge($product->get_gallery_image_ids(), $images);

		// Show variation featured images
		if ($product->is_type('variable')) {
			foreach ($product->get_children() as $variationId) {
				$image = get_post_thumbnail_id($variationId);
				if ($image) {
					$images[] = $image;
				}
			}
		}

		return $images;
	}
}
