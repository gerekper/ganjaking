<?php

namespace Premmerce\WooCommercePinterest\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\AJAX\AjaxController;
use Premmerce\WooCommercePinterest\PinterestPlugin;

/**
 * Class AdminAssets
 *
 * @package Premmerce\WooCommercePinterest\Admin
 *
 * This class is responsible for including admin css and js files
 */
class AdminAssets {

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * Version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * File suffix
	 *
	 * @var string
	 */
	private $fileSuffix;

	/**
	 * Handle suffix
	 *
	 * @var string
	 */
	private $handlePrefix;

	/**
	 * LocalizeArray field name
	 *
	 * @var string
	 */
	private $localizeArrayField = 'localizeScriptArray';

	/**
	 * AdminAssets constructor.
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager) {
		$this->fileManager  = $fileManager;
		$this->version      = PinterestPlugin::$version;
		$this->handlePrefix = 'woocommerce-pinterest-';
		$this->fileSuffix   = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $screen
	 *
	 * @return void
	 */
	public function enqueueAssets( $screen) {
		$assetsDataForScreen = $this->getAssetsDataForScreen($screen);

		if (!$assetsDataForScreen) {
			return;
		}

		$styleBaseName  = $assetsDataForScreen['styleBaseName'];
		$scriptBaseName = $assetsDataForScreen['scriptBaseName'];

		if ($styleBaseName) {
			$this->enqueueStyle($styleBaseName);
		}

		if ($scriptBaseName) {
			$this->enqueueScript($scriptBaseName);

			if ($assetsDataForScreen[$this->localizeArrayField]) {
				$this->localizeScript($scriptBaseName, $assetsDataForScreen[$this->localizeArrayField]);
			}
		}
	}

	/**
	 * Enuque style
	 *
	 * @param string $styleBaseName
	 *
	 * @return void
	 */
	private function enqueueStyle( $styleBaseName) {
		$assetType = 'css';
		wp_enqueue_style(
			$this->getAssetHandle($styleBaseName, $assetType),
			$this->getAssetPath($styleBaseName, $assetType),
			array(),
			$this->version
		);
	}

	/**
	 * Enqueue style
	 *
	 * @param string $scriptBaseName
	 *
	 * @return void
	 */
	private function enqueueScript( $scriptBaseName) {
		$assetType = 'js';
		wp_enqueue_script(
			$this->getAssetHandle($scriptBaseName, $assetType),
			$this->getAssetPath($scriptBaseName, $assetType),
			$this->getScriptDependencies($scriptBaseName),
			$this->version,
			false
		);
	}

	/**
	 * Localize script
	 *
	 * @param string $scriptBaseName
	 * @param array $localizeData
	 *
	 * @return void
	 */
	private function localizeScript( $scriptBaseName, array $localizeData) {
		wp_localize_script(
			$this->getAssetHandle($scriptBaseName, 'js'),
			'premmerceSettings',
			$localizeData
		);
	}

	/**
	 * Get asset handle
	 *
	 * @param string $assetBaseName
	 * @param string $assetType css or js
	 *
	 * @return string
	 */
	private function getAssetHandle( $assetBaseName, $assetType) {
		$type = 'js' === $assetType ? '-script' : '-style';
		return $this->handlePrefix . $assetBaseName . $type;
	}

	/**
	 * Get asset path
	 *
	 * @param string $assetBaseName
	 * @param string $assetType css or js
	 *
	 * @return string
	 */
	private function getAssetPath( $assetBaseName, $assetType) {
		$file = trailingslashit('admin') . $assetType . '/' . $assetBaseName . $this->fileSuffix . '.' . $assetType;
		return $this->fileManager->locateAsset($file);
	}

	/**
	 * Get assets data for screen
	 *
	 * @param string $screen
	 * @return array
	 */
	private function getAssetsDataForScreen( $screen) {
		$assetsData = array(
			'post.php' => $this->getPostsAssetsData(),
			'post-new.php' => $this->getPostsAssetsData(),
			'woocommerce_page_woocommerce-pinterest-page' => $this->getPinsPageAssetsData(),
			'woocommerce_page_wc-settings' => $this->isPinterestIntegrationSectionPage($screen) ? $this->getSettingsPageAssetsData() : array(),
			'edit.php' => $this->isProductsListPage() ? $this->getProductsTablePageAssetsData() : array(),
			'edit-tags.php' => $this->getCategoriesScreensAssetsData(),
			'term.php' => $this->getCategoriesScreensAssetsData()
		);

		return isset($assetsData[$screen]) ? $assetsData[$screen] : array();
	}

    /**
     * @param $scriptBaseName
     *
     * @return array
     */
	private function getScriptDependencies($scriptBaseName) {
	    $dependencies = array(
            'product' => array('jquery', 'selectWoo'),
            'products-table' => array('jquery', 'selectWoo'),
            'pins' => array('jquery', 'selectWoo'),
            'terms-pages' => array('jquery', 'selectWoo'),
            'settings' => array('jquery')
        );

	    return array_key_exists($scriptBaseName, $dependencies) ? $dependencies[$scriptBaseName] : array();
    }

    /**
     * @return bool
     */
	private function isProductsListPage(){
	    $screen = get_current_screen();
        return $screen && isset($screen->post_type) && 'product' === $screen->post_type;
    }

	/**
	 * Get categories sceens assets data
	 *
	 * @return array
	 */
	private function getCategoriesScreensAssetsData() {
		return array(
			'scriptBaseName' => 'terms-pages',
			'styleBaseName' => '',
			$this->localizeArrayField => array(
				'get_terms_action' => AjaxController::GET_TAGS_FOR_CATEGORY_ACTION,
				'get_terms_nonce' => wp_create_nonce(AjaxController::GET_TAGS_FOR_CATEGORY_ACTION),
				'searchTagsTranslation' => __('Search hashtags', 'woocommerce-pinterest')
			),
		);
	}

	/**
	 * Get posts assets data
	 *
	 * @return array
	 */
	private function getPostsAssetsData() {
		return array(
			'styleBaseName' => 'product',
			'scriptBaseName' => 'product',
			$this->localizeArrayField => array(),
		);
	}

	/**
	 * Get pins page assets data
	 *
	 * @return array
	 */
	private function getPinsPageAssetsData() {
		return array(
			'styleBaseName' => 'pins',
			'scriptBaseName' => 'pins',
			$this->localizeArrayField => array()
		);
	}

	/**
	 * Get settings page assets data
	 *
	 * @return array
	 */
	private function getSettingsPageAssetsData() {
		return array(
			'styleBaseName' => 'pins',
			'scriptBaseName' => 'settings',
			$this->localizeArrayField => array(
				'get_google_categories_nonce' => wp_create_nonce(AjaxController::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION),
				'get_google_categories_action' => AjaxController::GET_GOOGLE_CATEGORIES_BY_PARENT_ACTION,
				'save_google_categories_mapping_nonce' => wp_create_nonce(AjaxController::SAVE_GOOGLE_CATEGORIES_ACTION),
				'save_google_categories_mapping_action' => AjaxController::SAVE_GOOGLE_CATEGORIES_ACTION,
				'save_category_boards_relations_nonce' => wp_create_nonce(AjaxController::SAVE_CATEGORY_BOARDS_RELATIONS_ACTION),
				'save_category_boards_relations_action' => AjaxController::SAVE_CATEGORY_BOARDS_RELATIONS_ACTION,
				'update_settings_page_boxes_states_nonce' => wp_create_nonce(AjaxController::UPDATE_SETTINGS_BOX_STATE_ACTION),
				'update_settings_page_boxes_states_action' => AjaxController::UPDATE_SETTINGS_BOX_STATE_ACTION,
				'not_selected_option_name' => __('Not selected', 'woocommerce-pinterest'),
			)
		);
	}

	/**
	 * Get products table page assets data
	 *
	 * @return array
	 */
	private function getProductsTablePageAssetsData() {
		return array(
			'scriptBaseName' => 'products-table',
			'styleBaseName' => 'products-table',
			$this->localizeArrayField => array()
		);
	}

	/**
	 * Check if pinterest integration section page
	 *
	 * @param $screenId
	 *
	 * @return bool
	 */
	private function isPinterestIntegrationSectionPage( $screenId) {
		if ('woocommerce_page_wc-settings' === $screenId) {
			$tab     = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
			$section = filter_input(INPUT_GET, 'section', FILTER_SANITIZE_STRING);

			return 'integration' === $tab && ( !$section || 'pinterest' === $section );
		}

		return false;
	}
}
