<?php

/**
 * Created by PhpStorm.
 * User: lorenzo
 * Date: 24/05/16
 * Time: 16.51
 */
class YWAR_Review {

	/**
	 * @var int the review id
	 */
	public $id = 0;

	/**
	 * @var string the review content
	 */
	public $content = '';

	/**
	 * @var string the review title
	 */
	public $title = '';

	/**
	 * @var int the review rating
	 */
	public $rating = 0;

	/**
	 * @var int the parent review, if exist
	 */
	public $parent_id = 0;

	/**
	 * @var int the product id the review belong to
	 */
	public $product_id = 0;

	/**
	 * @var int the comment id of the original comment object
	 */
	public $comment_id = 0;
	/**
	 * @var string the review status
	 */
	public $status = '';

	/**
	 * @var string the review date
	 */
	public $review_date = '';

	private function __construct() {

	}


	/**
	 * Retrieve a review by its id
	 *
	 * @param $id
	 *
	 * @return YWAR_Review
	 */
	public static function get( $id ) {
		$review = new YWAR_Review();

		$review->id = $id;
		if ( $id ) {
			$review->product_id = get_post_meta( $id, YITH_YWAR_META_KEY_PRODUCT_ID, true );
			$review->comment_id = get_post_meta( $id, YITH_YWAR_META_COMMENT_ID, true );
		}

		return $review;
	}


	public function is_approved() {
		return $this->id && ( 1 == get_post_meta( $this->id, YITH_YWAR_META_APPROVED, true ) );
	}

}