<?php namespace Premmerce\WooCommercePinterest\Pinterest;

use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Logger\Logger;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\ServiceContainer;

/**
 * Class PinService
 * Responsible for pins management
 *
 * @package Premmerce\WooCommercePinterest\Pinterest
 */
class PinService {


	/**
	 * PinModel instance
	 *
	 * @var PinModel
	 */
	private $pinModel;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

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
	private $categoryBoardRelationModel;

	/**
	 * PinService constructor.
	 *
	 * @param PinModel $pinModel
	 * @param BoardRelationsModel $categoryBoardRelationModel
	 * @param Logger $logger
	 * @param AdminNotifier $notifier
	 */
	public function __construct( PinModel $pinModel, BoardRelationsModel $categoryBoardRelationModel, Logger $logger, AdminNotifier $notifier) {
		$this->pinModel                   = $pinModel;
		$this->categoryBoardRelationModel = $categoryBoardRelationModel;
		$this->logger                     = $logger;
		$this->notifier                   = $notifier;
	}

	/**
	 * Add image to pins table, with pending status
	 *
	 * @param int $postId
	 * @param int $attachmentId
	 *
	 * @throws PinterestModelException
	 */
	public function create( $postId, $attachmentId) {
		$boardsToPin = $this->getBoardsIdsToPinTo($postId);

		$pinsCreationFailed = false;

		foreach ($boardsToPin as $board) {
			$pin = $this->pinModel->getPinByPostAttachmentAndBoard($postId, $attachmentId, $board);

			if (empty($pin)) {
				try {
					$id = $this->pinModel->createPin($postId, $attachmentId, $board);
					$this->queueAction($id, PinModel::ACTION_CREATE);
				} catch (PinterestModelException $e) {
					$this->logger->logPinterestException($e);
					$pinsCreationFailed = true;
				}

			}

			if (PinModel::ACTION_DELETE === $pin['action']) {
				if (empty($pin['pin_id'])) {
					$this->pinModel->setAction($pin['pin_id'], PinModel::ACTION_CREATE);
				} else {
					$this->pinModel->setPinSynchronized($pin['pin_id'], false);
				}
			}
		}

		if ($pinsCreationFailed) {
			$this->notifier->flash(
				__('One or more Pins weren\'t created because of errors.', 'woocommerce-pinterest'),
				AdminNotifier::ERROR
			);
		}

	}

	/**
	 * Get boards ids to pin to
	 *
	 * @param $postId
	 *
	 * @return string[]
	 * 
	 * @throws PinterestModelException
	 */
	public function getBoardsIdsToPinTo( $postId) {
		$boardsByProduct           = $this->getBoardsFromProductSettings($postId);
		$boardsByProductCategories = $this->getBoardsIdsFromProductCategories($postId);

		$boards = array_unique(array_merge($boardsByProduct, $boardsByProductCategories));


		$container    = ServiceContainer::getInstance();
		$defaultBoard = $container->getPinterestIntegration()->get_option('board');

		$boards = $boards ? $boards : array($defaultBoard);
		$boards = $this->filterOutNotAvailableBoards($boards);

		return $boards;
	}

	/**
	 * Get boards from product settings
	 *
	 * @param $productId
	 *
	 * @return string[]
	 *
	 * @throws PinterestModelException
	 */
	public function getBoardsFromProductSettings( $productId) {

		return $this->categoryBoardRelationModel->getBoardsIdsByProductId($productId);
	}

	/**
	 * Get boards Ids from Product categories
	 *
	 * @param $productId
	 *
	 * @return string[]
	 * @throws PinterestModelException
	 */
	public function getBoardsIdsFromProductCategories( $productId) {
		$productCategoriesIds = wc_get_product_term_ids($productId, 'product_cat');

		return $this->categoryBoardRelationModel->getBoardsIdsByCategoriesArray($productCategoriesIds);
	}

	/**
	 * Filter out not available boards from settings
	 *
	 * @param array $boardsIds
	 *
	 * @return array
	 */
	public function filterOutNotAvailableBoards( array $boardsIds) {
		$availableBoards = (array) ServiceContainer::getInstance()
			->getPinterestIntegration()
			->get_option('boards');

		$availableBoardsIds = array_column($availableBoards, 'id');

		return array_intersect($availableBoardsIds, $boardsIds);
	}

	/**
	 * Cancel pin action, and change status to synchronized
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function cancel( $id) {
		$pin = $this->pinModel->find($id);

		if (empty($pin['pin_id'])) {
			$this->pinModel->deleteSingleById($id);
		} else {
			return $this->pinModel->setPinSynchronized($id);
		}
	}


	/**
	 * Set pin action delete, and change status to pending
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete( $id) {
		return $this->queueAction($id, PinModel::ACTION_DELETE);
	}

	/**
	 * Set pin action update, and change status to pending
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function update( $id) {
		return $this->queueAction($id, PinModel::ACTION_UPDATE);
	}

	/**
	 * Retry failed pin, and change status to pending
	 *
	 * @param int $id
	 *
	 * @return false|int
	 */
	public function retry( $id) {
		$pin = $this->pinModel->find($id);

		if ($pin && PinModel::STATUS_FAILED === $pin['status']) {
			return $this->queueAction($id, $pin['action']);
		}
	}

	/**
	 * Update all pins by post id
	 *
	 * @param int $postId
	 *
	 * @return bool
	 */
	public function updateByPost( $postId) {
		return $this->queuePostAction($postId, PinModel::ACTION_UPDATE);
	}


	/**
	 * Add new post pins and remove old
	 *
	 * @param int $postId
	 * @param array $newAttachmentsIds
	 *
	 * @throws PinterestModelException
	 */
	public function synchronize( $postId, array $newAttachmentsIds) {
		foreach ($newAttachmentsIds as $attachmentId) {
			$this->create($postId, $attachmentId);
		}

		$pinsIdsToRemove = $this->getPinsIdsToDelete($postId, $newAttachmentsIds);
		foreach ($pinsIdsToRemove as $pinToRemove) {
			$this->delete($pinToRemove);
		}
	}

	/**
	 * Return pins ids to delete
	 *
	 * @param int $postId
	 * @param array $newAttachmentsIds
	 *
	 * @return array
	 * @throws PinterestModelException
	 */
	private function getPinsIdsToDelete( $postId, array $newAttachmentsIds) {
		$toRemoveByAttachment =  $this->getPinsIdsToRemoveByAttachmentIds($postId, $newAttachmentsIds);
		$toRemoveByBoard      = $this->getPinsIdsToRemoveByBoardsIds($postId);

		$pinsIdsToRemove = array_unique(array_merge($toRemoveByAttachment, $toRemoveByBoard));
		return $pinsIdsToRemove;
	}

	/**
	 * Get pins ids from not selected attachments
	 *
	 * @param $postId
	 * @param $newAttachmentsIds
	 *
	 * @return mixed
	 * @throws PinterestModelException
	 */
	private function getPinsIdsToRemoveByAttachmentIds( $postId, $newAttachmentsIds) {
		return $this->pinModel->filterByCurrentUser()
			->filterByPost($postId)
			->notIn('attachment_id', $newAttachmentsIds)
			->get('id', PinModel::TYPE_COLUMN);
	}

	/**
	 * Return pins ids from not selected boards
	 *
	 * @param $postId
	 * @return PinModel
	 *
	 * @throws PinterestModelException
	 */
	private function getPinsIdsToRemoveByBoardsIds( $postId ) {
		$productBoardsIds = $this->getBoardsIdsToPinTo($postId);

		return $this->pinModel->filterByCurrentUser()
			->filterByPost($postId)
			->notIn('board', $productBoardsIds)
			->get('id', PinModel::TYPE_COLUMN);
	}

	/**
	 * Add post featured image to pins table
	 *
	 * @param int $postId
	 * @throws PinterestModelException
	 */
	public function pinFeaturedImage( $postId) {
		$attachmentId = (int) get_post_thumbnail_id($postId);

		if (  $attachmentId ) {
			$this->create($postId, $attachmentId);
		} else {
			$postTitle = get_post_field('title', $postId);
			$this->logger->log("Tried to pin product without image. Product id: {$postId}, product title: {$postTitle}");
		}
	}

	/**
	 * Get all post attachments from pins table
	 *
	 * @param int $postId
	 *
	 * @return string[]
	 */
	public function getPostPinnedAttachments( $postId) {
		$attachments = $this->pinModel->filterActual()
								   ->filterByCurrentUser()
								   ->filterByPost($postId)
								   ->get('attachment_id', PinModel::TYPE_COLUMN);

		return $attachments;
	}

	/**
	 * Get product pin description template
	 *
	 * @param int $postId
	 *
	 * @return string
	 */
	public function getProductPinDescriptionTemplate( $postId) {

		$description = get_post_meta($postId, '_pin_description_template', true);

		$description = $description ? (string) $description : '';

		return apply_filters('woocommerce_pinterest_product_pin_description_template', $description);
	}

	/**
	 * Check if variation has enabled "pin description" option
	 *
	 * @param int $postId
	 *
	 * @return bool
	 */
	public function isVariationHasPinDescription( $postId) {
		return get_post_meta($postId, '_has_pin_description_template', true) === 'yes';
	}

	/**
	 * Update status of having variation pin description
	 *
	 * @param int $variationId
	 * @param bool $state
	 */
	public function setVariationHasPinDescription( $variationId, $state) {
		$state = $state ? 'yes' : '';

		update_post_meta($variationId, '_has_pin_description_template', $state);
	}

	/**
	 * Update product pin description template
	 *
	 * @param int $postId
	 * @param string $description
	 */
	public function updatePinDescriptionTemplate( $postId, $description) {
		update_post_meta($postId, '_pin_description_template', $description);
	}

	/**
	 * Update action for all pins by post id
	 *
	 * @param int $postId
	 * @param string $action
	 *
	 * @return bool
	 */
	protected function queuePostAction( $postId, $action) {
		$pins = $this->pinModel->getPinsByPost($postId);

		$result = true;
		foreach ($pins as $pin) {
			$result = $this->queueAction($pin['id'], $action) && $result;
		}

		return $result;
	}

	/**
	 * Update action pins by id
	 *
	 * @param int $id
	 * @param string $action
	 *
	 * @return bool
	 */
	protected function queueAction( $id, $action) {
		$this->pinModel->setAction($id, $action);

		return update_option('woocommerce_pinterest_start_bg', true);
	}

	/**
	 * Add pinned product images to new product boards
	 *
	 * @param int $productId
	 * @param array $boards
	 * @throws PinterestModelException
	 */
	public function addProductPinsToNewBoards( $productId, array $boards) {
		$attachmentsIds = $this->getPostPinnedAttachments($productId);

		foreach ($attachmentsIds as $attachmentId) {
			$this->create($productId, $attachmentId);
		}
	}

	/**
	 * Delete all pins created from post
	 *
	 * @param $postId
	 */
	public function deletePinsByPostId( $postId) {
		$pins = $this->pinModel->filterByPost($postId)->get('id', PinModel::TYPE_COLUMN);

		foreach ($pins as $pinId) {
			$this->delete($pinId);
		}
	}

	/**
	 * Delete all pins created from attachment
	 *
	 * @param int $attachmentId
	 */
	public function deletePinsByAttachmentId( $attachmentId) {
		$pins = $this->pinModel->filterByAttachment($attachmentId)->get('id', PinModel::TYPE_COLUMN);

		foreach ($pins as $pinId) {
			$this->delete($pinId);
		}
	}
}
