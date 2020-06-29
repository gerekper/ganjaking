<?php namespace Premmerce\WooCommercePinterest\Model;

use DateTime;
use Premmerce\WooCommercePinterest\Admin\Product\ProductHandler;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections\PinTimeSection;
use Premmerce\WooCommercePinterest\ServiceContainer;
use WC_Product;

/**
 * Class PinModel
 * Responsible for pinterest plugin database queries
 *
 * @package Premmerce\WooCommercePinterest\Model
 */
class PinModel extends AbstractModel {

	/**
	 * Pending actions
	 */
	const ACTION_DELETE = 'delete';

	const ACTION_EMPTY = '';

	const ACTION_CREATE = 'create';

	const ACTION_UPDATE = 'update';

	/**
	 * Synchronization statuses
	 */
	const STATUS_FAILED = -1;

	const STATUS_PENDING = 0;

	const STATUS_SYNCHRONIZED = 1;

	const STATUS_WAITING = 2;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'woocommerce_pinterest';

	/**
	 * User id
	 *
	 * @var string
	 */
	private $userId;

	/**
	 * PinModel constructor.
	 *
	 * @param $userId
	 */
	public function __construct( $userId) {
		$this->userId = $userId;
		parent::__construct();
	}


	/**
	 * Create pin
	 *
	 * @param int $postId
	 * @param int $attachmentId
	 * @param string $board
	 *
	 * @return int|null
	 *
	 * @throws PinterestModelException
	 */
	public function createPin( $postId, $attachmentId, $board) {
		if (!$board) {
			throw new PinterestModelException('Can\'t create pin without selected board. Pin product id ' . $postId);
		}

		return $this->create(array(
			'post_id' => $postId,
			'action' => self::ACTION_CREATE,
			'created_at' => current_time('mysql'),
			'updated_at' => current_time('mysql'),
			'attachment_id' => $attachmentId,
			'pin_user_id' => $this->userId,
			'board' => $board,
			'produce_at' => null
		));
	}

	/**
	 * Set pin as synchronized as clear action and set pin id from pinterest
	 *
	 * @param int $id
	 * @param string $pinId
	 *
	 * @return false|int
	 */
	public function setPinCreated( $id, $pinId) {
		return $this->update($id, array(
			'action' => self::ACTION_EMPTY,
			'status' => self::STATUS_SYNCHRONIZED,
			'created_at' => current_time('mysql'),
			'updated_at' => current_time('mysql'),
			'pin_id' => $pinId
		));
	}

	/**
	 * Set pin as synchronized as clear action
	 *
	 * @param int $id
	 *
	 * @param bool $updateDate
	 *
	 * @return false|int
	 */
	public function setPinSynchronized( $id, $updateDate = true) {
		$update = array(
			'action' => self::ACTION_EMPTY,
			'status' => self::STATUS_SYNCHRONIZED,
			'error' => null,
		);

		if ($updateDate) {
			$update['updated_at'] = current_time('mysql');
		}

		return $this->update($id, $update);
	}

	/**
	 * Set pin as failed
	 *
	 * @param int $id
	 * @param $errorData
	 *
	 * @return false|int
	 */
	public function setPinFailed( $id, $errorData) {
		return $this->update($id, array(
			'status' => self::STATUS_FAILED,
			'error' => wp_json_encode($errorData)
		));
	}

	/**
	 * Return Pin by post attachment and board
	 *
	 * @param int $postId
	 * @param int $attachmentId
	 * @param string $board
	 *
	 * @return array|null
	 */
	public function getPinByPostAttachmentAndBoard( $postId, $attachmentId, $board) {
		return $this->filterByCurrentUser()
			->filterByPost($postId)
			->filterByAttachment($attachmentId)
			->filterByBoard($board)
			->get(null, self::TYPE_ROW);
	}

	/**
	 * Return pins by post id
	 *
	 * @param int $postId
	 *
	 * @return array|null
	 */
	public function getPinsByPost( $postId) {
		return $this->filterByCurrentUser()->where(array('post_id' => $postId))->get();
	}

	/**
	 * Set action
	 *
	 * @param int $id
	 * @param int $action
	 *
	 * @return false|int
	 */
	public function setAction( $id, $action) {
		$status    = self::STATUS_PENDING;
		$container = ServiceContainer::getInstance();
		$produceAt = null;

		// In future we can defer all actions.
		if (static::ACTION_CREATE === $action) {

			$integration = $container->getPinterestIntegration();
			$executeDate = $integration->getExecuteDate();

			if ($integration->isEnableDeferPinning() && $executeDate instanceof \DateTime) {
				$status    = self::STATUS_WAITING;
				$produceAt = $executeDate->format('Y-m-d H:i:s');
			}
		}

		return $this->update($id, array(
			'action' => $action,
			'status' => $status,
			'error' => null,
			'produce_at' => $produceAt,
		));
	}

	/**
	 * Filter pins by has defer pinning
	 *
	 * @return bool
	 */
	public function hasDeferPinning() {
		return count($this->filterByCurrentUser()->where(array('status' => self::STATUS_WAITING))->get('COUNT(id)')) > 0;
	}

	/**
	 * Add interval to pins
	 *
	 * @param $ids
	 * @param int $interval
	 * @param bool $notIn
	 * @return $this
	 */
	public function addIntervalToPins( $ids, $interval = PinTimeSection::DEFAULT_INTERVAL, $notIn = true) {
		try {
			$newDate = ( new DateTime() )->modify('+' . $interval . ' minutes')->format('Y-m-d H:i:s');

			$separator = $notIn ? 'NOT IN' : 'IN';

			$currentDate = ( new DateTime() )->format('Y-m-d H:i:s');

			$this->db->query($this->db->prepare('UPDATE ' . $this->table . ' SET produce_at = %s WHERE id ' . $separator . ' (' . implode(',',
					$ids) . ') AND pin_user_id = %d AND status = %s AND produce_at < %s',
				 $newDate, $this->userId, self::STATUS_WAITING, $currentDate));

		} catch (\Exception $exception) {
			wc_get_logger()->warning($exception->getMessage(), array('source' => 'Pinterest for Woocommerce'));
		}

		return $this;
	}

	/**
	 * Switch pins to pending
	 *
	 * @param array $ids
	 * @param string $status
	 * @param bool $notIn
	 */
	public function switchToPending( $ids, $status, $notIn = true ) {

		$separator = $notIn ? 'NOT IN' : 'IN';

		$this->db->query($this->db->prepare('UPDATE ' . $this->table . ' SET status = %s, produce_at = NULL WHERE id ' . $separator . ' (' . implode(',',
				$ids) . ')',
			$status));
	}

	/**
	 * Return number of pins
	 *
	 * @return int
	 */
	public function count() {
		return (int) $this->get('COUNT(id) as count', AbstractModel::TYPE_VAR);
	}

	public function filterByProduceNow() {
		$currentDate = ( new DateTime() )->format('Y-m-d H:i:s');

		return $this->where(array(
			'produce_at' => $currentDate
		), '<');
	}

	/**
	 * Filter pins by post id
	 *
	 * @param int $postId
	 *
	 * @return PinModel
	 */
	public function filterByPost( $postId) {
		return $this->where(array('post_id' => $postId));
	}

	/**
	 * Filter pins by attachment id
	 *
	 * @param int $attachmentId
	 *
	 * @return PinModel
	 */
	public function filterByAttachment( $attachmentId) {
		return $this->where(array('attachment_id' => $attachmentId));
	}

	/**
	 * Filter pins by actual
	 *
	 * @return PinModel
	 */
	public function filterActual() {
		return $this->where(array('action' => 'delete'), '!=');
	}

	/**
	 * Pins with pending status
	 *
	 * @return PinModel
	 */
	public function filterPending() {
		return $this->where(array('status' => self::STATUS_PENDING));
	}

	/**
	 * Pins with waiting status
	 *
	 * @return PinModel
	 */
	public function filterWaiting() {
		return $this->where(array('status' => self::STATUS_WAITING));
	}

	/**
	 * Filter pins by current user
	 *
	 * @return PinModel
	 */
	public function filterByCurrentUser() {
		return $this->where(array('pin_user_id' => $this->userId));
	}

	/**
	 * Filter pins by board
	 *
	 * @param $board
	 *
	 * @return PinModel
	 */
	public function filterByBoard( $board) {
		return $this->where(array('board' => $board));
	}

	/**
	 * Filter pins by multiple boards ids
	 *
	 * @param array $boardsIds
	 *
	 * @return PinModel
	 *
	 * @throws PinterestModelException
	 */
	public function filterByBoardIn( array $boardsIds) {
		return $this->in('board', $boardsIds);
	}

	/**
	 * Filter pins by pin product title like
	 *
	 * @param string $searchTerm
	 *
	 * @return PinModel
	 *
	 * @throws PinterestModelException
	 */
	public function filterByPostTitleLike( $searchTerm) {
		$foundPostsIds = $this->getProductsIdsWithTitleLike($searchTerm);
		return $this->filterByProductIn($foundPostsIds);
	}

	/**
	 * Return posts ids where name contains search term
	 *
	 * @param $searchTerm
	 *
	 * @return int[]
	 */
	private function getProductsIdsWithTitleLike( $searchTerm) {
		$wildcard = '%';
		$like     = $wildcard . $this->db->esc_like($searchTerm) . $wildcard;
		$sql      = $this->db->prepare("SELECT ID FROM {$this->db->posts} WHERE post_type IN (%s, %s) AND post_title LIKE %s", 'product', 'product_variation', $like);
		$result   = $this->db->get_col($sql);

		return array_map('intval', $result);
	}

	/**
	 * Filter pins by multiple products ids
	 *
	 * @param array $productsIds
	 *
	 * @return PinModel
	 *
	 * @throws PinterestModelException
	 */
	public function filterByProductIn( array $productsIds) {
		return $this->in('post_id', $productsIds);
	}

	/**
	 * Sanitize single field
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function sanitizeField( $fieldName, $value) {
		switch ($fieldName) {
			case 'id':
			case 'post_id':
			case 'attachment_id':
				return intval($value);
			case 'pin_id':
			case 'pin_user_id':
				return sanitize_key($value);
			case 'action':
			case 'status':
			case 'board':
				return sanitize_text_field($value);
			case 'produce_at':
				if (null === $value) {
					return $value;
				}
				return sanitize_text_field($value);
			default:
				return esc_sql($value);
		}
	}


}
