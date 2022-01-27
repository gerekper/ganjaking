<?php namespace Premmerce\WooCommercePinterest\Admin\Product\PinAll;

use Premmerce\WooCommercePinterest\Pinterest\PinService;
use Premmerce\WooCommercePinterest\ServiceContainer;
use WP_Term;

class PinAllManager {

	const OPTION_KEY = 'woocommerce_pinterest_pin_all_data';
	const CRON_INTERVAL = 3;
	const CRON_HOOK = 'woocommerce_pinterest_pin_all_products';
	const AJAX_GET_UPDATE_ACTION = 'woocommerce_pinterest_ajax_get_update_action';
	const START_PIN_PROCESS_ACTION = 'woocommerce_pinterest_start_pin_process_action';

	/**
	 * ServiceContainer
	 *
	 * @var ServiceContainer
	 */
	private $container;

	/**
	 * PinAllManager constructor.
	 *
	 * @param ServiceContainer $container
	 */
	public function __construct( ServiceContainer $container ) {
		$this->hooks();
		$this->container = $container;
	}

	protected function hooks() {
		add_action( 'admin_post_' . self::START_PIN_PROCESS_ACTION, array( $this, 'startPinAllTask' ) );
		add_action( 'wp_ajax_' . self::AJAX_GET_UPDATE_ACTION, array( $this, 'ajaxGetUpdate' ) );

		add_action( 'init', function () {
			$this->handleSingleCronEvent();
		} );
	}

	public function ajaxGetUpdate() {
		$data = $_GET;

		if ( wp_verify_nonce( $data['nonce'], self::AJAX_GET_UPDATE_ACTION ) ) {
			wp_send_json( array(
				'success'        => true,
				'process_status' => $this->getStatus(),
			) );
		}

		wp_send_json( array(
			'error'         => true,
			'error_message' => 'Invalid nonce',
			'need_reload'   => true,
		) );
	}

	public function startPinAllTask() {

		$data = $_GET;

		if ( wp_verify_nonce( $data['nonce'], self::START_PIN_PROCESS_ACTION ) ) {

			$data = array(
				'status'                     => 'processing',
				'pin_all_gallery_images'     => (bool) $data['pin_all_gallery_images'],
				'processing_category'        => false,
				'pending_categories'         => $this->buildPendingCategoriesFromUserSetup(),
				'current_process_unique_key' => uniqid()
			);

			$this->setData( $data );
		}

		return wp_redirect( wp_get_referer() . '#woocommerce_pinterest_pinterest_pin_all_section' );
	}

	protected function buildPendingCategoriesFromUserSetup() {

		$categoriesRelations = $this->container->getBoardRelationModel()->getAllCategoriesRelations();
		$categories          = array();

		foreach ( $categoriesRelations as $categoriesRelation ) {

			$category = get_term( (int) $categoriesRelation['entity_id'] );

			if ( $category instanceof WP_Term ) {
				$categories[] = array(
					'products_total'     => $category->count,
					'products_processed' => 0,
					'name'               => $category->slug,
					'slug'               => $category->name,
					'id'                 => $category->term_id
				);
			}
		}

		return $categories;
	}

	/**
	 * Get pin all data
	 *
	 * @return array
	 */
	public function getData() {
		return get_option( self::OPTION_KEY, array() );
	}

	/**
	 * Set data
	 *
	 * @param array $data
	 */
	public function setData( $data ) {
		update_option( self::OPTION_KEY, $data );
	}

	protected function scheduleCron() {
		wp_schedule_single_event( time(), self::CRON_HOOK );
	}

	/**
	 * PinService
	 *
	 * @return PinService
	 */
	public function getPinService() {
		return $this->container->getPinService();
	}

	public function handleSingleCronEvent() {
		$status = $this->getProcessStatus();

		if ( 'processing' === $status ) {
			$category = $this->getCurrentCategoryToProcess();

			if ( $category ) {
				$pinAllCategory = new PinAllCategory( $category, $this );

				$pinAllCategory->process();
			} else {
				$pendingCategories = $this->getPendingToProcessCategories();

				if ( $pendingCategories ) {
					$this->setCategoryToProcess( array_pop( $pendingCategories ) );
					$this->setPendingCategories( $pendingCategories );
				} else {

					$this->finish();

					return;
				}
			}
		}
	}

	protected function finish() {
		$this->setStatus( 'pending' );
	}

	public function setCategoryToProcess( $category ) {
		$data                        = $this->getData();
		$data['processing_category'] = $category;
		$this->setData( $data );
	}

	public function setPendingCategories( $categories ) {
		$data                       = $this->getData();
		$data['pending_categories'] = $categories;
		$this->setData( $data );
	}

	public function setStatus( $status ) {
		$data           = $this->getData();
		$data['status'] = $status;
		$this->setData( $data );
	}

	public function getStatus() {
		return array(
			'status'                     => $this->getProcessStatus(),
			'pin_all_gallery_images'     => $this->isPinAllGalleryImages(),
			'processing_category'        => $this->getCurrentCategoryToProcess(),
			'pending_categories'         => $this->getPendingToProcessCategories(),
			'current_process_unique_key' => $this->getCurrentProcessUniqueKey()
		);
	}

	public function getCurrentProcessUniqueKey() {
		return isset( $this->getData()['current_process_unique_key'] ) ? $this->getData()['current_process_unique_key'] : '-';
	}

	public function isPinAllGalleryImages() {
		return isset( $this->getData()['pin_all_gallery_images'] ) ? (bool) $this->getData()['pin_all_gallery_images'] : false;
	}

	public function getProcessStatus() {
		$status = isset( $this->getData()['status'] ) ? $this->getData()['status'] : 'pending';

		return in_array( $status, array( 'pending', 'processing' ) ) ? $status : 'pending';
	}

	/**
	 * Get current category that is in progress status
	 *
	 * @return bool|array
	 */
	public function getCurrentCategoryToProcess() {
		return isset( $this->getData()['processing_category'] ) && is_array( $this->getData()['processing_category'] ) ? $this->getData()['processing_category'] : false;
	}

	public function getPendingToProcessCategories() {
		return isset( $this->getData()['pending_categories'] ) && is_array( $this->getData()['pending_categories'] ) ? $this->getData()['pending_categories'] : false;
	}

}
