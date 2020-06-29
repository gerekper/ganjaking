<?php

namespace Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable;

use Premmerce\SDK\V2\FileManager\FileManager;
use \WP_List_Table;
use WP_Term;

abstract class AbstractTermRelationsTable extends WP_List_Table {


	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

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
	 * TermsCollection instance
	 *
	 * @var TermsCollection|null;
	 */
	public $items;

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
		$level                 = $this->items->getTermLevel($item->term_taxonomy_id);

		return $this->fileManager->renderTemplate('admin/woocommerce/term-relations-table/category-name.php', array(
			'term' => $item,
			'includeDataCategoryId' => $includeDataCategoryId,
			'level' => $level
		));
	}
}
