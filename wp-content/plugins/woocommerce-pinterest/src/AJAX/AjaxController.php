<?php

namespace Premmerce\WooCommercePinterest\AJAX;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesModel;
use Premmerce\WooCommercePinterest\Model\GoogleCategoriesRelationsModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;

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

	const SAVE_CATEGORY_BOARDS_RELATIONS_ACTION = self::ACTION_PREFIX . 'save-category-boards-mapping';

	const UPDATE_SETTINGS_BOX_STATE_ACTION = self::ACTION_PREFIX . 'update-settings-box-state';

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
	}


	public function init() {
		add_action('wp_ajax_' . self::GET_TAGS_FOR_CATEGORY_ACTION, array($this, 'sendTagsForCategory'));
		add_action('wp_ajax_' . self::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION, array($this, 'sendGoogleCategoriesForNewSelector'));
		add_action('wp_ajax_' . self::SAVE_GOOGLE_CATEGORIES_ACTION, array($this, 'saveGoogleCategoriesMapping'));
		add_action('wp_ajax_' . self::SAVE_CATEGORY_BOARDS_RELATIONS_ACTION, array($this, 'saveCategoryBoardsRelations'));
		add_action('wp_ajax_' . self::UPDATE_SETTINGS_BOX_STATE_ACTION, array($this, 'updateSettingsBoxState'));
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

	public function saveGoogleCategoriesMapping() {
		check_ajax_referer(self::SAVE_GOOGLE_CATEGORIES_ACTION);


		$categories = filter_input(INPUT_POST, 'categories', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);

		try {
			$this->googleCategoriesRelationsModel->updateCategoriesRelations($categories);
		} catch (PinterestModelException $e) {
			ServiceContainer::getInstance()->getLogger()->logPinterestException($e);
			wp_send_json(array('success' => false));
		}

		wp_send_json(array('success' => true));
	}

	public function saveCategoryBoardsRelations() {
		check_ajax_referer(self::SAVE_CATEGORY_BOARDS_RELATIONS_ACTION);

		$relations = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY );

		try {
			$this->boardRelationsModel->updateCategoryBoardsRelationsFromAjax($relations);
		} catch (PinterestModelException $e) {
			wp_send_json(array(
				'success' => false,
				'message' => "Looks like something goes wrong. Settings wasn't saved."
			));
			ServiceContainer::getInstance()->getLogger()->logPinterestException($e);
		}

		wp_send_json(array('success' => true));
	}

	public function updateSettingsBoxState() {
		check_ajax_referer(self::UPDATE_SETTINGS_BOX_STATE_ACTION);

		$settingsBoxStateData = filter_input_array(INPUT_POST, array('sectionId' => FILTER_SANITIZE_STRING, 'closed' => FILTER_VALIDATE_BOOLEAN));

		if (is_array($settingsBoxStateData)) {
			$this->pinterestIntegration->updateClosedSettingsBoxesIds($settingsBoxStateData);
		}

	}
}
