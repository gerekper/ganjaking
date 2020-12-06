<?php

namespace Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable;

use Premmerce\SDK\V2\FileManager\FileManager;
use \WP_List_Table;
use WP_Term;

abstract class AbstractTermRelationsTable extends WP_List_Table {


	/**
	 * Items per page
	 *
	 * @var int
	 */
	protected $perPage = 20;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;


	/**
	 * TermsCollection instance
	 *
	 * @var TermsCollection|null;
	 */
	public $items;

	/**
	 * AbstractTermRelationsTable constructor.
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager) {
		$this->fileManager = $fileManager;

		parent::__construct(array(
			'singular' => 'relation',
			'plural' => 'relations'
		));
	}

	/**
	 * Set how many items display per page, but no less than 0
	 *
	 * @param int $perPage
	 */
	public function setPerPage( $perPage) {
		$this->perPage = absint($perPage) > 0 ? $perPage : $this->perPage;
	}

	/**
	 * Render 'category' column for TermRelations tables.
	 * This method name shouldn't been changed because WP looking for it.
	 *
	 * @see WP_List_Table
	 *
	 * @param WP_Term $item
	 * @return string
	 */
	public function column_category( WP_Term $item) {
		$includeDataCategoryId = $this instanceof WcCategoryPinterestBoardRelationsTable;
		$level                 = $this->items->getTermLevel($item->term_id);

		return $this->fileManager->renderTemplate('admin/woocommerce/term-relations-table/category-name.php', array(
			'term' => $item,
			'includeDataCategoryId' => $includeDataCategoryId,
			'level' => $level
		));
	}

	public function preparePagination() {
		$this->set_pagination_args(array(
			'total_items' => wp_count_terms('product_cat'),
			'per_page' => $this->perPage,
			'total_pages' => ceil($this->items->count() / $this->perPage)
		));
	}

	public function prepare_items() {
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

		$woocommerceCategories = get_terms(array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false
		));

		$this->items = new TermsCollection($woocommerceCategories, $this->perPage, $this->get_pagenum());


		$this->preparePagination();
	}
}
