<?php // phpcs:ignore WordPress.NamingConventions

/**
 * Created by PhpStorm.
 * User: lorenzo
 * Date: 24/05/16
 * Time: 16.51
 *
 * @package       YITH\yit-woocommerce-advanced-reviews\includes
 */
class YWAR_Review {

	/**
	 * Id
	 *
	 * @var id int the review id
	 */
	public $id = 0;

	/**
	 * Content
	 *
	 * @var content string the review content
	 */
	public $content = '';

	/**
	 * Title
	 *
	 * @var string the review title
	 */
	public $title = '';

	/**
	 * Rating
	 *
	 * @var int the review rating
	 */
	public $rating = 0;

	/**
	 * Parent_id
	 *
	 * @var int the parent review, if exist
	 */
	public $parent_id = 0;


	/**
	 * Product_id
	 *
	 * @var int the product id the review belong to
	 */
	public $product_id = 0;


	/**
	 * Comment_id
	 *
	 * @var int the comment id of the original comment object
	 */
	public $comment_id = 0;

	/**
	 * Status
	 *
	 * @var string the review status
	 */
	public $status = '';


	/**
	 * Review_date
	 *
	 * @var string the review date
	 */
	public $review_date = '';

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {

	}



	/**
	 * Get
	 * Retrieve a review by its id
	 *
	 * @param id mixed $id id.
	 * @return review void YWAR_Review;
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


	/**
	 * Is_approved
	 *
	 * @return bool void
	 */
	public function is_approved() {
		return $this->id && ( 1 === get_post_meta( $this->id, YITH_YWAR_META_APPROVED, true ) );
	}

}
