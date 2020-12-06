<?php


namespace Premmerce\WooCommercePinterest\Model;

/**
 * Class BoardRelationsModel
 *
 * @package Premmerce\WooCommercePinterest\Model
 *
 * This class is responsible for category board relations table queries
 */
class BoardRelationsModel extends AbstractModel {

	const ENTITY_TYPE_CATEGORY = 'category';

	const ENTITY_TYPE_PRODUCT = 'product';

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'woocommerce_pinterest_boards_mapping';

	/**
	 * Pinterest user id
	 *
	 * @var string
	 */
	private $pinterestUserId;

	/**
	 * BoardRelationsModel constructor.
	 *
	 * @param string $pinterestUserId
	 */
	public function __construct( $pinterestUserId ) {
		parent::__construct();

		$this->pinterestUserId = $pinterestUserId;
	}

	/**
	 * Get all categories relations for current user
	 *
	 * @return mixed
	 */
	public function getAllCategoriesRelations() {
		return $this->filterByEntityType( self::ENTITY_TYPE_CATEGORY )
			->filterByCurrentUser()
			->get();
	}

	/**
	 * Get relations by category
	 *
	 * @param $categoryId
	 *
	 * @return mixed
	 */
	public function getByCategory( $categoryId ) {
		return $this->filterByCategory( $categoryId )
					->filterByCurrentUser()
					->get();
	}

	/**
	 * Filter relations by category id
	 *
	 * @param $categoryId
	 *
	 * @return BoardRelationsModel
	 */
	private function filterByCategory( $categoryId ) {
		$this->where( array( 'entity_id' => $categoryId, 'entity_type' => self::ENTITY_TYPE_CATEGORY ) );

		return $this;
	}

	/**
	 * Filter relations by current user
	 *
	 * @return BoardRelationsModel
	 */
	private function filterByCurrentUser() {
		$this->where( array( 'pin_user_id' => $this->pinterestUserId ) );

		return $this;
	}

	/**
	 * Get boards ids by categories array
	 *
	 * @param array $categoriesArray
	 *
	 * @return array
	 *
	 * @throws PinterestModelException
	 */
	public function getBoardsIdsByCategoriesArray( array $categoriesArray ) {
		return $this->filterByEntityType( self::ENTITY_TYPE_CATEGORY )
					->filterByEntitiesIds( $categoriesArray )
					->filterByCurrentUser()
					->get( 'board_id', self::TYPE_COLUMN );
	}

	/**
	 * Filter by entities ids
	 *
	 * @param array $entitiesIds
	 *
	 * @return BoardRelationsModel
	 *
	 * @throws PinterestModelException
	 */
	private function filterByEntitiesIds( array $entitiesIds ) {
		$this->in( 'entity_id', $entitiesIds );

		return $this;
	}

	/**
	 * Filter by entity type
	 *
	 * @param $entityType
	 *
	 * @return $this
	 */
	private function filterByEntityType( $entityType ) {
		$this->where( array( 'entity_type' => $entityType ) );

		return $this;
	}

	/**
	 * Return boards ids filtered by product id
	 *
	 * @param $productId
	 *
	 * @return string[]
	 *
	 * @throws PinterestModelException
	 */
	public function getBoardsIdsByProductId( $productId ) {
		return (array) $this->filterByEntityType( self::ENTITY_TYPE_PRODUCT )
							->filterByEntitiesIds( array( $productId ) )
							->filterByCurrentUser()
							->get( 'board_id', self::TYPE_COLUMN );
	}

	/**
	 * Update category boards relations
	 *
	 * @param $relations
	 *
	 * @throws PinterestModelException
	 *
	 * todo: Try to improve updating logic to don't use relation_id. This would be simpler and clearer.
	 */
	public function updateCategoryBoardsRelations( $relations ) {

		$sortedRelations = $this->sortCategoryRelationsToUpdateOrDelete( $relations );

		$dataToInsert = $sortedRelations['toUpdate'];

		if ( $dataToInsert ) {
			$fields = $this->getFieldsWithPlaceholders();
			$this->replaceMultiple( $fields, $dataToInsert );
		}

		$idsToDelete = array_filter( $sortedRelations['toDelete'] );

		if ( $idsToDelete ) {
			$this->filterByCurrentUser()
				 ->filterByEntityType( self::ENTITY_TYPE_CATEGORY )
				 ->in( 'id', $idsToDelete )
				 ->deleteFiltered();
		}
	}

	/**
	 * Add product boards
	 *
	 * @param int $productId
	 * @param array $boardsIds
	 *
	 * @throws PinterestModelException
	 */
	public function addProductBoards( $productId, array $boardsIds ) {
		$preparedProductBoardRelations = $this->prepareProductRelationsToInsert( $productId, $boardsIds );

		if ( $preparedProductBoardRelations ) {
			$fields = $this->getFieldsWithPlaceholders();
			$this->replaceMultiple( $fields, $preparedProductBoardRelations );
		}
	}

	/**
	 * Update product boards relations
	 *
	 * @param $productId
	 * @param array $boardsIds
	 *
	 * @throws PinterestModelException
	 */
	public function updateProductBoardsRelations( $productId, array $boardsIds ) {
		$this->deleteNotSelectedProductBoardRelations( $productId, $boardsIds );
		$this->addProductBoards( $productId, $boardsIds );
	}


	/**
	 * Prepare product relations to insert
	 *
	 * @param $productId
	 * @param array $boardsIds
	 *
	 * @return array
	 */
	private function prepareProductRelationsToInsert( $productId, array $boardsIds ) {
		$preparedRelationsData = array();

		foreach ( $boardsIds as $boardId ) {
			$preparedRelationsData[] = array(
				'id'          => '',
				'pin_user_id' => $this->pinterestUserId,
				'entity_id'   => $productId,
				'entity_type' => self::ENTITY_TYPE_PRODUCT,
				'board_id'    => $boardId
			);
		}

		return $preparedRelationsData;
	}

	/**
	 * Delete not selected product board relations
	 *
	 * @param $productId
	 * @param int[] $boardsIds
	 *
	 * @throws PinterestModelException
	 */
	private function deleteNotSelectedProductBoardRelations( $productId, array $boardsIds ) {
		$this->filterByEntityType( self::ENTITY_TYPE_PRODUCT )
			 ->filterByCurrentUser()
			 ->filterByEntitiesIds( array( $productId ) )
			 ->notIn( 'board_id', $boardsIds )
			 ->deleteFiltered();
	}

	/**
	 * Prepare category relations to update
	 *
	 * @param array $relations
	 *
	 * @return array[]
	 */
	private function sortCategoryRelationsToUpdateOrDelete( array $relations ) {
		$preparedRelationsData = array(
			'toUpdate' => array(),
			'toDelete' => array()
		);

		foreach ( $relations as $categoryId => $categoryBoardsData ) {
			foreach ( $categoryBoardsData as $categoryBoardData ) {

				if ( empty( $categoryBoardData['board_id'] ) ) {
					$preparedRelationsData['toDelete'][] = $categoryBoardData['relation_id'];
				} else {
					$boardId = $categoryBoardData['board_id'];

					$data = array(
						'id'          => isset( $categoryBoardData['relation_id'] ) ? $categoryBoardData['relation_id'] : '',
						'entity_id'   => $categoryId,
						'entity_type' => self::ENTITY_TYPE_CATEGORY,
						'board_id'    => $boardId,
						'pin_user_id' => $this->pinterestUserId
					);

					$preparedRelationsData['toUpdate'][] = $data;
				}
			}
		}

		return $preparedRelationsData;
	}

	/**
	 * Get table fields names with placeholders
	 *
	 * @return array
	 */
	private function getFieldsWithPlaceholders() {
		return array(
			'id'          => '%d',
			'pin_user_id' => '%s',
			'entity_id'   => '%d',
			'entity_type' => '%s',
			'board_id'    => '%s'
		);
	}

	/**
	 * Sanitize single field
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 *
	 * @return int|string
	 */
	protected function sanitizeField( $fieldName, $value ) {
		switch ( $fieldName ) {
			case 'id':
			case 'entity_id':
				return intval( $value );
			case 'pin_user_id':
			case 'entity_type':
			case 'board_id':
				return sanitize_key( $value );
			default:
				return sanitize_text_field( $value );
		}
	}
}
