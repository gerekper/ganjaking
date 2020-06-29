<?php


namespace Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Model\BoardRelationsModel;
use Premmerce\WooCommercePinterest\ServiceContainer;
use \WP_Term;

/**
 * Class CategoryBoardTable
 *
 * @package Premmerce\WooCommercePinterest\Admin\Table
 *
 * This class is responsible for WooCommerce Category to Pinterest boards mapping table rendering
 */
class WcCategoryPinterestBoardRelationsTable extends AbstractTermRelationsTable {

	/**
	 * BoardRelationsModel instance
	 *
	 * @var BoardRelationsModel
	 */
	private $model;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * Field id
	 *
	 * @var int
	 */
	private $fieldId;

	/**
	 * Boards array
	 *
	 * @var array
	 */
	private $boards;

	/**
	 * TermsCollection instance
	 *
	 * @var TermsCollection|null
	 */
	public $items;

	/**
	 * CategoryBoardTable constructor.
	 *
	 * @param BoardRelationsModel $model
	 * @param FileManager $fileManager
	 */
	public function __construct( BoardRelationsModel $model, FileManager $fileManager) {
		$this->fileManager = $fileManager;
		$this->model       = $model;

		parent::__construct($this->fileManager);
	}

	/**
	 * Return table columns list
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'category' => __('WooCommerce Category', 'woocommerce-pinterest'),
			'board' => __('Board name', 'woocommerce-pinterest')
		);
	}

	/**
	 * Render column 'board' cell
	 *
	 * @param WP_Term $item
	 *
	 * @return string
	 */
	public function column_board( WP_Term $item) {
		return $this->renderBoardCell($item->term_taxonomy_id,
			$this->boards,
			$this->model->getByCategory($item->term_taxonomy_id));
	}

	/**
	 * Render column 'board' cell
	 *
	 * @param $categoryId
	 * @param array $boards
	 * @param array $boardsCategoriesRelations
	 *
	 * @todo: move this to template
	 *
	 * @return string
	 */
	public function renderBoardCell( $categoryId, array $boards, array $boardsCategoriesRelations) {
		$this->fieldId = 0;


		$html  = '<fieldset class="woocommerce-pinterest-category-board-selectors">';
		$html .= $boardsCategoriesRelations ? '' : $this->renderCategoryBoardSelect($categoryId, $boards);

		foreach ($boardsCategoriesRelations as $relation) {
			$html .= $this->renderCategoryBoardSelect($categoryId, $boards, $relation);
		}

		$html .= '</fieldset>';

		$html .= '<button class="woocommerce-pinterest-category-board-new-select">' . __('Add board', 'woocommerce-pinterest') . '</button>';

		return $html;
	}

	/**
	 * Render select for column 'board'
	 *
	 * @param $categoryId
	 * @param array $boards
	 * @param array $categoryBoardRelation
	 *
	 * @return string
	 *
	 */
	private function renderCategoryBoardSelect( $categoryId, array $boards, array $categoryBoardRelation = array()) {
		$this->fieldId++;

		return $this->fileManager->renderTemplate(
			'admin/woocommerce/term-relations-table/category-board-table/category-board-select.php',
			array(   'categoryId' => $categoryId,
				'categoryBoardRelation' => $categoryBoardRelation,
				'boards' => $boards,
				'fieldId' => $this->fieldId
			)
		);
	}


	public function prepare_items() {
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

		$integration  = ServiceContainer::getInstance()->getPinterestIntegration();
		$this->boards = (array) $integration->get_option('boards');

		$terms = get_terms(array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false
		));

		$this->items = new TermsCollection($terms);

	}


}
