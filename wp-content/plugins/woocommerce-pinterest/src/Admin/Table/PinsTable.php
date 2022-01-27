<?php namespace Premmerce\WooCommercePinterest\Admin\Table;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\Pins;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use WP_List_Table;

/**
 * Class PinsTable
 * Responsible for displaying pins table
 *
 * @package Premmerce\WooCommercePinterest\Admin\Table
 */
class PinsTable extends WP_List_Table {

	const PINS_IDS_INPUT_NAME = 'woocommerce-pinterest-pins-ids';

	const FILTER_BY_BOARD_INPUT_NAME = 'woocommerce-pinterest-filter-by-board';

	/**
	 * PinModel instance
	 *
	 * @var PinModel $model
	 */
	private $model;

	/**
	 * Items per page number
	 *
	 * @var int
	 */
	private $perPage;

	/**
	 * Actions array
	 *
	 * @var array
	 */
	private $actions;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $pinterestIntegration;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PriceTypesTable constructor.
	 *
	 * @param FileManager $fileManager
	 * @param PinModel $model
	 * @param PinterestIntegration $pinterestIntegration
	 */
	public function __construct( FileManager $fileManager, PinModel $model, PinterestIntegration $pinterestIntegration) {
		$this->model                = $model;
		$this->perPage              = get_user_meta(get_current_user_id(), Pins::PINS_PER_PAGE_USER_META_KEY, true);
		$this->perPage              = $this->perPage ? $this->perPage : 10;
		$this->pinterestIntegration = $pinterestIntegration;

		parent::__construct(array(
			'singular' => 'pin',
			'plural' => 'pins',
			'ajax' => false,
		));
		$this->fileManager = $fileManager;
	}

	/**
	 * Set table actions
	 *
	 * @param array $actions
	 */
	public function setActions( array $actions) {
		$this->actions = $actions;
	}

	/**
	 * Render data for cell checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_cb( $item) {
		return '<input type="checkbox" name="' . self::PINS_IDS_INPUT_NAME . '[]" value="' . esc_attr($item['id']) . '">';
	}

	/**
	 * Render data for product name
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_product_name( $item ) {
    $isCarousel = '';
    if ( intval( $item['attachment_id'] ) === 0 && intval( $item['carousel_ids'] ) !== 0 ) {
      $isCarousel = '<br>' . __( 'Carousel', 'woocommerce-pinterest' );
    }

		if ($item['product_name']) {
			return '<a href="' . get_edit_post_link($item['post_id']) . '">' . $item['product_name'] . '</a>' . $isCarousel;
		} else {
			return __('Product not found', 'woocommerce-pinterest');
		}
	}

	/**
	 * Render image column
	 *
	 * @param array $item
	 *
	 * @return string
	 */
  protected function column_image( $item )
  {
    if ( ! $item['attachment_id'] ) {
      $attachmentId = explode( ',', $item['carousel_ids'] );
      $url = wp_get_attachment_image_url( $attachmentId[0] );
    } elseif ( wp_get_attachment_image_src( $item['attachment_id'] ) ) {
      $url = wp_get_attachment_image_url( $item['attachment_id'] );
    } else {
      $url = wc_placeholder_img_src();
    }

    return '<img src="' . esc_attr( $url ) . '" width="50px;">';
  }

	/**
	 * Render date_created field
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_created_at( $item) {
		return date_i18n(get_option('date_format'), strtotime($item['created_at']));
	}

	/**
	 * Render date_created field
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_updated_at( $item) {
		return date_i18n(get_option('date_format'), strtotime($item['updated_at']));
	}

	/**
	 * Render pinterest link
	 *
	 * @param array $item
	 *
	 * @return string|null
	 */
	protected function column_link( $item) {
		if (!empty($item['pin_id'])) {
			return '<a target="_blank" href="https://www.pinterest.com/pin/' . esc_attr($item['pin_id']) . '/"><span class="dashicons dashicons-external"></span></a>';
		}
	}


	/**
	 * Render date_created field
	 *
	 * @param array $pin
	 *
	 * @return string
	 */
	protected function column_status( $pin) {
		$status = '';
		$action = '';
		switch ($pin['action']) {
			case PinModel::ACTION_DELETE:
				$action = __('Removing', 'woocommerce-pinterest');
				break;
			case PinModel::ACTION_EMPTY:
				$action = __('Ready', 'woocommerce-pinterest');
				break;
			case PinModel::ACTION_CREATE:
				$action = __('Creating', 'woocommerce-pinterest');
				break;
			case PinModel::ACTION_UPDATE:
				$action = __('Updating', 'woocommerce-pinterest');
				break;
		}

		switch ($pin['status']) {
			case PinModel::STATUS_FAILED:
				$status = 'failed';
				break;
			case PinModel::STATUS_PENDING:
				$status = 'processing';
				break;
			case PinModel::STATUS_SYNCHRONIZED:
				$status = 'ready';
				break;
			case PinModel::STATUS_WAITING:
				$status = 'processing';
				$action = $action . ' <span class="dashicons dashicons-clock"></span>';
				break;
		}

		if (!$this->isPinValid($pin)) {
			$status = 'failed';
		}

		$tooltip = $this->preparePinStatusTooltip($pin);

		$statusView = sprintf('<span class="pin-status pin-status--%s" title="%s">%s</span>', $status,
			esc_attr($tooltip), $action);

		if (PinModel::STATUS_FAILED === $pin['status']) {
			$error = !empty($pin['error']) ? json_decode($pin['error'], true) : null;
			if (!empty($error['message'])) {
				$statusView .= wc_help_tip($error['message']);
			}
		}

		return $statusView;
	}

	/**
	 * Prepare pin status tooltip
	 *
	 * @param array $pin
	 *
	 * @return string
	 */
	private function preparePinStatusTooltip( array $pin) {
		$tooltip = '';

		if ($pin['error']) {

			$error = json_decode(stripslashes($pin['error']));

			if (is_object($error)) {
				$code    = isset($error->code) ? $error->code : '';
				$message = isset($error->message) ? $error->message : '';

				/* translators: %d is replaced with error code, '%s is replaced with error message from response'*/
				$tooltip = sprintf(__('Response code %d' . PHP_EOL . 'Response message: %s', 'woocommerce-pinterest'),
					$code, $message);
			}
		}

		if (null !== $pin['produce_at']) {
			try {
				$produceAt = new \DateTime($pin['produce_at']);

				$tooltip = sprintf('In %s', human_time_diff(time(), $produceAt->getTimestamp()));

			} catch (\Exception $e) {
				wc_get_logger()->warning($e->getMessage(), array('source' => 'Pinterest for Woocommerce'));
			}

		}

		return $tooltip;
	}

	/**
	 * Render 'board' column cell
	 *
	 * @param array $item
	 */
	public function column_board( $item) {
		$boards    = $this->pinterestIntegration->get_option('boards', array());
		$names     = array_column($boards, 'name', 'id');
		$boardName = isset($names[$item['board']]) ? $names[$item['board']] : '&mdash;';
		echo esc_html($boardName);
	}

	public function isPinValid( $pin ) {
		$product = wc_get_product($pin['post_id']);

    if( ! $pin['attachment_id'] ) {
      $attachmentId = explode( ',', $pin['carousel_ids'] );
      return $product && wp_get_attachment_image_url( $attachmentId[0] );
    }
		
		return $product && wp_get_attachment_image_url($pin['attachment_id']);
	}

	/**
	 * Return array with columns titles
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox">',
			'image' => '<span class="dashicons dashicons-format-image"></span>',
			'product_name' => __('Name', 'woocommerce-pinterest'),
			'created_at' => __('Created', 'woocommerce-pinterest'),
			'updated_at' => __('Updated', 'woocommerce-pinterest'),
			'status' => __('Status', 'woocommerce-pinterest'),
			'link' => __('Link', 'woocommerce-pinterest'),
			'board' => __('Board', 'woocommerce-pinterest')
		);
	}

	public function get_sortable_columns() {
		return array(
			'product_name' => array('product_name', false),
			'created_at' => array('created_at', false),
			'updated_at' => array('updated_at', false),
			'status' => array('status', false),
			'board' => array('board', false)
		);
	}

	/**
	 * Generate row actions
	 *
	 * @param array $pin
	 * @param string $column_name
	 * @param string $primary
	 *
	 * @return string
	 */
	protected function handle_row_actions( $pin, $column_name, $primary) {
		if ('product_name' !== $column_name) {
			return '';
		}

		$actions = array();

		if (PinModel::STATUS_FAILED === $pin['status']) {
			$actions['retry']  = vsprintf('<a href="%s">%s</a>', array(
				$this->createActionUrl('woocommerce_pinterest_pin_retry', $pin['id']),
				__('Retry', 'woocommerce-pinterest'),
			));
			$actions['cancel'] = vsprintf('<a href="%s">%s</a>', array(
				$this->createActionUrl('woocommerce_pinterest_pin_cancel', $pin['id']),
				__('Cancel', 'woocommerce-pinterest'),
			));
		} elseif (!empty($pin['pin_id']) && $this->isPinValid($pin)) {
			$actions['update'] = vsprintf('<a href="%s">%s</a>', array(
				$this->createActionUrl('woocommerce_pinterest_pin_update', $pin['id']),
				__('Update', 'woocommerce-pinterest'),
			));
		}

		$actions['delete'] = vsprintf(
			'<a href="%s" data-action-delete data-confirmation-massage="%s">%s</a>',
			array(
				$this->createActionUrl('woocommerce_pinterest_pin_delete', $pin['id']),
				__('Are you sure you want to delete this pin?', 'woocommerce-pinterest'),
				__('Delete', 'woocommerce-pinterest'),
			)
		);

		return $this->row_actions($actions);
	}

	/**
	 * Create nonce url for action
	 *
	 * @param string $action
	 * @param int $itemId
	 *
	 * @return string
	 */
	protected function createActionUrl( $action, $itemId) {
		$url = admin_url('admin-post.php') . "?action={$action}&pin_id={$itemId}";

		return wp_nonce_url($url, $action . '_' . $itemId);
	}

	/**
	 * Set actions list for bulk
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return $this->actions;
	}

	/**
	 * Set items data in table
	 *
	 * @throws PinterestModelException
	 */
	public function prepare_items() {
		/**
		 * Init column headers.
		 */
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());


		$perPage     = $this->perPage;
		$currentPage = $this->get_pagenum();

		$offset = ( ( $currentPage - 1 ) * $perPage );

		$data = $this->getPinsData($offset, $perPage);

		$data = $this->addProductsNames($data);

		$this->items = apply_filters('woocommerce_pinterest_found_pins_for_table', $data, $this, $this->model);

		$this->setUpPagination($perPage);
	}

	/**
	 * Return pins data
	 *
	 * @param int $offset
	 * @param int $perPage
	 *
	 * @return array
	 *
	 * @throws PinterestModelException
	 */
	private function getPinsData( $offset, $perPage) {
		$this->preparePinsFilters();

		return $this->model
			->limit($perPage)
			->offset($offset)
			->orderBy($this->getOrderBy(), $this->getOrder())
			->get();
	}

	/**
	 * Prepare get pins filters
	 *
	 * @throws PinterestModelException
	 */
	private function preparePinsFilters() {
		$this->model->filterByCurrentUser();

		$boardToFilterBy = $this->getFilterByBoard();

		if ($boardToFilterBy) {
			$this->model->filterByBoard($boardToFilterBy);
		}

		$search = $this->getSearch();

		if ($search) {
			$this->model->filterByPostTitleLike($search);
		}

	}

	/**
	 * Return board id to filter pins by. Return null if not filtering now.
	 *
	 * @return string|null
	 */
	private function getFilterByBoard() {
		$board = filter_input(INPUT_GET, self::FILTER_BY_BOARD_INPUT_NAME, FILTER_SANITIZE_STRING);

		return $board ? $board : '';
	}

	/**
	 * Get search term if it is
	 *
	 * @return string
	 */
	private function getSearch() {
		$search = filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING);
		return $search ? $search : '';
	}

	/**
	 * Set product_name field to pin data array
	 *
	 * @param array $data
	 * @return array
	 */
	private function addProductsNames( array $data) {

		foreach ($data as &$pinData) {
			$pinData['product_name'] = $this->getProductName($pinData['post_id']);
		}

		return $data;
	}

	/**
	 * Get product name for Name column
	 *
	 * @param int $productId
	 * @return string
	 */
	private function getProductName( $productId) {
		$product = wc_get_product($productId);

		return $product ? $product->get_name() : '';
	}

	/**
	 * Setup pagination
	 *
	 * @param $perPage
	 *
	 * @throws PinterestModelException
	 */
	public function setUpPagination( $perPage) {
		$this->preparePinsFilters();

		$this->set_pagination_args(array(
			'total_items' => $this->model->count(),
			'per_page' => $perPage,
		));
	}

	/**
	 * Return array column to sort pins by
	 *
	 * @return string
	 */
	private function getOrderBy() {
		static $orderby;

		if (! $orderby) {
			$orderby      = filter_input(INPUT_GET, 'orderby', FILTER_SANITIZE_STRING);
			$columnsNames = array_keys($this->get_sortable_columns());

			$orderby = in_array($orderby, $columnsNames, true) ? $orderby : 'updated_at';
		}

		return $orderby;
	}

	/**
	 * Return order to sort pins
	 *
	 * @return string
	 */
	private function getOrder() {
		static $order;

		if (! $order) {
			$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
			$order = in_array($order, array('asc', 'desc'), true) ? $order : 'desc';
		}

		return strtoupper($order);
	}

	/**
	 * Check if given column contains time value
	 *
	 * @param string $columnName
	 *
	 * @return bool
	 */
	private function isTimeColumn( $columnName) {
		return in_array($columnName, array('created_at', 'updated_at'), true);
	}

	/**
	 * Render if no items
	 */
	public function no_items() {
		esc_html_e('No pins found', 'woocommerce-pinterest');
	}

	public function extra_tablenav( $which) {
		if ('top' === $which) {
			$this->renderCategoriesFilter();
		}
	}

	private function renderCategoriesFilter() {
		$boardsListFromSettings = (array) $this->pinterestIntegration->get_option('boards', array());
		$boardsFromSettings     = array_column($boardsListFromSettings, 'name', 'id');

		$this->fileManager->includeTemplate('admin/woocommerce/pins-table-categories-filter.php', array('boards' => $boardsFromSettings, 'selectedBoard' => $this->getFilterByBoard()));
	}
}
