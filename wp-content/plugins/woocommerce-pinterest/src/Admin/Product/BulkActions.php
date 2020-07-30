<?php namespace Premmerce\WooCommercePinterest\Admin\Product;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\Pinterest\PinService;
use \WP_Post;

class BulkActions {

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * PinService instance
	 *
	 * @var PinService
	 */
	private $pinService;

	/**
	 * AdminNotifier instance
	 *
	 * @var AdminNotifier
	 */
	private $notifier;

	/**
	 * BoardRelationsModel instance
	 *
	 * @var BoardRelationsModel
	 */
	private $boardRelationsModel;

	/**
	 * PinModel instance
	 *
	 * @var PinModel
	 */
	private $pinModel;

	/**
	 * BulkActions constructor.
	 *
	 * @param FileManager $fileManager
	 * @param PinterestIntegration $integration
	 * @param PinService $pinService
	 * @param AdminNotifier $notifier
	 * @param BoardRelationsModel $boardRelationsModel
	 * @param PinModel $pinModel
	 */
	public function __construct(
		FileManager $fileManager,
		PinterestIntegration $integration,
		PinService $pinService,
		AdminNotifier $notifier,
		BoardRelationsModel $boardRelationsModel,
		PinModel $pinModel
	) {
		$this->fileManager         = $fileManager;
		$this->integration         = $integration;
		$this->pinService          = $pinService;
		$this->notifier            = $notifier;
		$this->boardRelationsModel = $boardRelationsModel;
		$this->pinModel            = $pinModel;
	}

	/**
	 * Output Pinterest Board field in products bulk edit form
	 */
	public function renderProductsBoardField() {

		$boards  = $this->integration->get_option('boards', array());
		$options = array_column($boards, 'name', 'id');
		$this->fileManager->includeTemplate('admin/bulk-edit.php', array('options' => $options));
	}

	/**
	 * Save product data after bulk edit
	 *
	 * @param int $postId
	 *
	 * @throws PinterestModelException
	 */
	public function updateProductsBoardsBulkEdit( $postId ) {

		$isBulkEdit = null !== filter_input(INPUT_GET, 'woocommerce_bulk_edit', FILTER_SANITIZE_STRING);
		$boards     = filter_input(INPUT_GET, 'pinterest_board', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

		if ($isBulkEdit
			&& 'product' === filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING)
			&& $boards
		) {
			$this->boardRelationsModel->addProductBoards($postId, $boards);
			$this->pinService->addProductPinsToNewBoards($postId, $boards);
		}

	}

	/**
	 * Add own action to product bulk action list
	 *
	 * @param array $bulk_actions
	 *
	 * @return mixed
	 */
	public function registerBulkPinAction( $bulk_actions) {
		$bulk_actions['woocommerce_pinterest_bulk_pin'] = _x('Pin', 'Action', 'woocommerce-pinterest');

		return $bulk_actions;
	}

	/**
	 * Handle pin bulk action
	 *
	 * @param string $redirect_to
	 * @param string $action
	 * @param array $post_ids
	 *
	 * @return string
	 *
	 * @throws PinterestModelException
	 */
	public function bulkPinActionHandler( $redirect_to, $action, $post_ids) {
		if ('woocommerce_pinterest_bulk_pin' !== $action) {
			return $redirect_to;
		}

		foreach ($post_ids as $product_id) {
			$this->pinService->pinFeaturedImage($product_id);
		}

		$this->notifier->flash(__('Pins are added to the queue', 'woocommerce-pinterest'));

		return $redirect_to;
	}

}
