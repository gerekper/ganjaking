<?php namespace Premmerce\WooCommercePinterest\Admin;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Admin\Table\PinsTable;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\Pinterest\PinService;
use Premmerce\WooCommercePinterest\ServiceContainer;

class Pins {

	const PINS_PER_PAGE_USER_META_KEY = 'woocommerce_page_woocommerce_pinterest_page_per_page';

	/**
	 * PinService instance
	 *
	 * @var PinService
	 */
	private $service;

	/**
	 * Actions
	 *
	 * @var array
	 */
	private $actions;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * ApiState instance
	 *
	 * @var ApiState
	 */
	private $apiState;

	/**
	 * AdminNotifier instance
	 *
	 * @var AdminNotifier
	 */
	private $notifier;

	/**
	 * Pins constructor.
	 *
	 * @param FileManager $fileManager
	 * @param ApiState $apiState
	 * @param PinService $service
	 */
	public function __construct( FileManager $fileManager, ApiState $apiState, PinService $service, AdminNotifier $notifier) {
		$this->fileManager = $fileManager;
		$this->apiState    = $apiState;
		$this->service     = $service;
		$this->notifier    = $notifier;

		$this->actions = array(
			'update' => __('Update', 'woocommerce-pinterest'),
			'delete' => __('Delete', 'woocommerce-pinterest'),
			'retry' => __('Retry', 'woocommerce-pinterest'),
			'cancel' => __('Cancel', 'woocommerce-pinterest'),
		);

		$this->init();

	}

	/**
	 * Init page
	 */
	public function init() {
		add_action('admin_post_woocommerce_pinterest_pin_update', array($this, 'handleUpdate'));
		add_action('admin_post_woocommerce_pinterest_pin_delete', array($this, 'handleDelete'));
		add_action('admin_post_woocommerce_pinterest_pin_retry', array($this, 'handleRetry'));
		add_action('admin_post_woocommerce_pinterest_pin_cancel', array($this, 'handleCancel'));

		//Screen options
		add_action('load-woocommerce_page_woocommerce-pinterest-page', array($this, 'addScreenOptions'));
		add_filter('set-screen-option', array($this, 'savePerPageOption'), 10, 3);
	}

	/**
	 * Show content
	 */
	public function render() {
		/**
		 * We can't inject PinsTable object because it needs some functions and classes which are not exist at __construct() call point.
		 * This looks better than include batch of WP core files in getPinsTable method
		 */
		$pinsTable = ServiceContainer::getInstance()->getPinsTable();
		$pinsTable->setActions($this->actions);

		if ($pinsTable->current_action()) {
			$this->handleBulkAction($pinsTable->current_action());
		}

		$this->fileManager->includeTemplate('admin/pins.php', array(
			'table' => $pinsTable,
			'fileManager' => $this->fileManager,
			'stateMessage' =>$this->apiState->getStateMessage()
		));
	}

	/**
	 * Handle table bulk actions
	 *
	 * @param string $action
	 */
	public function handleBulkAction( $action) {
		check_admin_referer('bulk-pins');

		$pinsIds = filter_input(INPUT_GET, PinsTable::PINS_IDS_INPUT_NAME, FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);

		if (array_key_exists($action, $this->actions)) {
			foreach ($pinsIds as $id) {
				if (is_callable(array($this->service, $action))) {
					call_user_func(array($this->service, $action), $id);
				}
			}

			/* translators: '%s' is replaced with action name */
			$message = sprintf(__('Pins are queued for %s', 'woocommerce-pinterest'), $this->actions[$action]);
			$this->notifier->flash(esc_html($message));
		}

		$this->redirectBack();
	}

	/**
	 * Handle update column and bulk actions
	 */
	public function handleCancel() {
		$this->handleSingleAction('cancel');
	}


	/**
	 * Handle update column and bulk actions
	 */
	public function handleUpdate() {
		$this->handleSingleAction('update');
	}

	/**
	 * Handle delete column and bulk actions
	 */
	public function handleDelete() {
		$this->handleSingleAction('delete');
	}

	/**
	 * Handle delete column
	 */
	public function handleRetry() {
		$this->handleSingleAction('retry');
	}

	/**
	 * Add Number of items per page option to Pins page
	 */
	public function addScreenOptions() {
		$screen = get_current_screen();
		$screen->add_option('per_page');
	}

	/**
	 * Save option per page
	 *
	 * @param bool $keep
	 * @param string $option
	 * @param mixed $value
	 * @return bool
	 */
	public function savePerPageOption( $keep, $option, $value) {
		if (self::PINS_PER_PAGE_USER_META_KEY === $option) {
			$keep = (int) $value;
		}

		return $keep;
	}

	/**
	 * Check admin referrer and handle action for single item
	 *
	 * @param $action
	 */
	protected function handleSingleAction( $action) {
		$id = filter_input(INPUT_GET, 'pin_id', FILTER_SANITIZE_STRING);
		if (! $id) {
			$id = filter_input(INPUT_POST, 'pin_id', FILTER_SANITIZE_STRING);
		}
		$id = $id ? $id : null;

		check_admin_referer("woocommerce_pinterest_pin_{$action}_{$id}");

		if (is_callable(array($this->service, $action))) {
			call_user_func(array($this->service, $action), $id);
		}

		/* translators: '%s' is replaced with action name */
		$message = __('Pin is queued for %s', 'woocommerce-pinterest');

		$message = sprintf($message, $this->actions[$action]);

		$this->notifier->flash(esc_html($message));

		$this->redirectBack();
	}


	/**
	 * Redirect to previous page
	 */
	protected function redirectBack() {
		wp_safe_redirect(wp_get_referer());
		die;
	}
}
