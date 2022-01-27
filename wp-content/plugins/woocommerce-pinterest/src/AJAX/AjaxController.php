<?php

namespace Premmerce\WooCommercePinterest\AJAX;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesRelationsModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;
use WC_Regenerate_Images;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\Task\BoardCreationBackgroundProcess;
use Premmerce\SDK\V2\Notifications\AdminNotifier;

/**
 * Class AJAX
 *
 * @package Premmerce\WooCommercePinterest\AJAX
 *
 * This class is responsible for AJAX
 */
class AjaxController {

	const ACTION_PREFIX = 'woocommerce-pinterest-';

	const GET_TAGS_FOR_CATEGORY_ACTION = self::ACTION_PREFIX . 'get-tags-for-category';

	const GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION = self::ACTION_PREFIX . 'get-google-categories-by-parent';

	const SAVE_GOOGLE_CATEGORIES_ACTION = self::ACTION_PREFIX . 'save-google-categories-mapping';

	const UPDATE_SETTINGS_BOX_STATE_ACTION = self::ACTION_PREFIX . 'update-settings-box-state';

	const REGENERATE_SHOP_THUMBNAILS_ACTION = self::ACTION_PREFIX . 'regenerate-shop-thumbnails';

	const CREATE_AND_SET_UP_BOARDS_ACTION = self::ACTION_PREFIX . 'create-and-set-up-boards';

	/**
	 * PinterestTagsController instance
	 *
	 * @var PinterestTagsController
	 */
	private $pinterestTagsController;

	/**
	 * GoogleCategoriesModel
	 *
	 * @var GoogleCategoriesModel
	 */
	private $googleCategoriesModel;

	/**
	 * GoogleCategoriesRelationsModel instance
	 *
	 * @var GoogleCategoriesRelationsModel
	 */
	private $googleCategoriesRelationsModel;

	/**
	 * BoardRelationsModel instance
	 *
	 * @var BoardRelationsModel
	 */
	private $boardRelationsModel;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $pinterestIntegration;

  /**
   * ApiState instance
   *
   * @var ApiState
   */
  private $apiState;

  /**
   * Background process
   *
   * @var BoardCreationBackgroundProcess
   */
  private $backgroundProcess;

  /**
   * ServiceContainer instance
   *
   * @var ServiceContainer
   */
  private $container;

	/**
	 * AJAX constructor.
	 *
	 * @param PinterestTagsController $pinterestTagsController
	 * @param GoogleCategoriesModel $googleCategoriesModel
	 * @param GoogleCategoriesRelationsModel $googleCategoriesRelationsModel
	 * @param BoardRelationsModel $boardRelationsModel
	 * @param PinterestIntegration $pinterestIntegration
	 */
	public function __construct(
		PinterestTagsController $pinterestTagsController,
		GoogleCategoriesModel $googleCategoriesModel,
		GoogleCategoriesRelationsModel $googleCategoriesRelationsModel,
		BoardRelationsModel $boardRelationsModel,
		PinterestIntegration $pinterestIntegration
	) {
		$this->pinterestTagsController        = $pinterestTagsController;
		$this->googleCategoriesModel          = $googleCategoriesModel;
		$this->googleCategoriesRelationsModel = $googleCategoriesRelationsModel;
		$this->boardRelationsModel            = $boardRelationsModel;
		$this->pinterestIntegration           = $pinterestIntegration;
    $this->container                      = ServiceContainer::getInstance();
		$this->apiState                       = new ApiState();
		$this->backgroundProcess              = new BoardCreationBackgroundProcess(
      $this->container->getApi(),
      $this->container->getPinModel(),
      $this->container->getPinDataGenerator(),
      $this->container->getLogger()
    );
	}


	public function init() {
		add_action('wp_ajax_' . self::GET_TAGS_FOR_CATEGORY_ACTION, array($this, 'sendTagsForCategory'));
		add_action('wp_ajax_' . self::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION, array($this, 'sendGoogleCategoriesForNewSelector'));
		add_action('wp_ajax_' . self::UPDATE_SETTINGS_BOX_STATE_ACTION, array($this, 'updateSettingsBoxState'));
		add_action('wp_ajax_' . self::REGENERATE_SHOP_THUMBNAILS_ACTION, array($this, 'regenerateShopThumbnails'));
		add_action('wp_ajax_' . self::CREATE_AND_SET_UP_BOARDS_ACTION, array($this, 'createAndSetupBoards'));
	}

	/**
	 * Send tags for category
	 *
	 * @return void

	 * @todo: send error message and alert it on front
	 */
	public function sendTagsForCategory() {
		check_ajax_referer(self::GET_TAGS_FOR_CATEGORY_ACTION);

		$searchWord = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);

		try {
			$tags = $this->pinterestTagsController->getTagsForAjaxSearch($searchWord);
		} catch (PinterestException $e) {
			ServiceContainer::getInstance()->getLogger()->logPinterestException($e);
			$tags = array();
		}

		wp_send_json($tags);
	}

	public function sendGoogleCategoriesForNewSelector() {
		check_ajax_referer(self::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION);

		$parentId = filter_input(INPUT_GET, 'parentId', FILTER_SANITIZE_NUMBER_INT);

		wp_send_json($this->googleCategoriesModel->getChildren($parentId));
	}

	public function updateSettingsBoxState() {
		check_ajax_referer(self::UPDATE_SETTINGS_BOX_STATE_ACTION);

		$settingsBoxStateData = filter_input_array(INPUT_POST, array('sectionId' => FILTER_SANITIZE_STRING, 'closed' => FILTER_VALIDATE_BOOLEAN));

		if (is_array($settingsBoxStateData)) {
			$this->pinterestIntegration->updateClosedSettingsBoxesIds($settingsBoxStateData);
		}

	}

  public function regenerateShopThumbnails()
  {
    check_ajax_referer( self::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION );

    WC_Regenerate_Images::queue_image_regeneration();

    wp_send_json( ['message' => __( 'Thumbnail regeneration has been scheduled to run in the background.', 'woocommerce-pinterest' )] );
  }

  public function createAndSetupBoards()
  {
    check_ajax_referer( self::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION );

    $container     = ServiceContainer::getInstance();
    $boardsRequest = $container->getApi()->getBoards();
    $boards        = $boardsRequest->getData();

    while ( $boardsRequest->getBookmark() ) {
      $boardsRequest = $container->getApi()->getBoards( $boardsRequest->getBookmark() );
      $boards = array_merge( $boardsRequest->getData(), $boards );
    }

    $boards = $this->backgroundProcess->filterBoards( $boards );

    $boardNames = [];
    if ( $boards ) {
      foreach ( $boards as $board ) {
        $boardNames[] = $board['name'];
      }
    }

    $categories = get_categories( [
      'taxonomy'   => 'product_cat',
      'hide_empty' => false,
    ] );

    $pushed = false;

    if ( $this->apiState->isReady() ) {
      if ( ! empty( $categories ) ) {
        foreach ( $categories as $categy ) {
          if ( ! in_array( $categy->name, $boardNames ) ) {
            $this->backgroundProcess->push_to_queue( [
              'name'        => $categy->name,
              'description' => $categy->description,
            ] );

            $pushed = true;
          }
        }

        if ( ! $pushed ) {
          // Update already existing boards mappin
          $integration = $container->getPinterestIntegration();
          $boardsOption = (array)$integration->get_option( 'boards' );
          $this->backgroundProcess->updateBoardsMapping( $boardsOption );
        }

        $this->backgroundProcess->save()->dispatch();
      }

      $response = [
        'class'   => 'success',
        'message' => __( 'Boards generation has been scheduled to run in the background.', 'woocommerce-pinterest' ),
      ];

    } else {
      $response = [
        'class'   => 'error',
        'message' => $this->apiState->getStateMessage(),
      ];
    }

    wp_send_json( $response );
  }
}
