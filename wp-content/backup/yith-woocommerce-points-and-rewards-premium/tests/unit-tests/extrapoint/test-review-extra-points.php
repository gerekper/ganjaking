<?php
/**
 * Class YWPAR_Tests_Review_Extra_Points
 *
 * @package YITH
 */

/**
 * Calculate points per product
 */
class YWPAR_Tests_Review_Extra_Points extends YWPAR_Unit_Test_Case_With_Store {

	function test_review_1(){
		$ywpar_review_exp = array(
			'list' => array(
				array(
					'number' => 1,
					'points' => 10,
					'repeat' => 0
				),
				array(
					'number' => 2,
					'points' => 30,
					'repeat' => 0
				),
			),
		);
		$user = wp_get_current_user();
		YITH_WC_Points_Rewards()->set_option('review_exp', $ywpar_review_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_review_exp', 'yes');

		$product = parent::create_and_store_points_product( array('_price'=> 10 ));
		$review_id = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio 1' );
		//sleep( 50);
		$review_id2 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio' );

		wp_set_comment_status($review_id, 'approve');
		wp_set_comment_status($review_id2, 'approve');
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 10 );
		wp_delete_comment($review_id);
		wp_delete_comment($review_id2);

	}

	function test_review_2() {
		$ywpar_review_exp = array(
			'list' => array(
				array(
					'number' => 1,
					'points' => 10,
					'repeat' => 0
				),
				array(
					'number' => 2,
					'points' => 30,
					'repeat' => 0
				),
			),
		);
		$user             = wp_get_current_user();
		YITH_WC_Points_Rewards()->set_option( 'review_exp', $ywpar_review_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_review_exp', 'yes' );

		$product   = parent::create_and_store_points_product( array( '_price' => 10 ) );
		$review_id = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio 1' );
		$review_id2 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio' );
		$review_id3 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio sdf sdfksjbd fskdbf' );

		wp_set_comment_status( $review_id, 'approve' );
		wp_set_comment_status( $review_id2, 'approve' );
		wp_set_comment_status( $review_id3, 'approve' );
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 40 );
		wp_delete_comment($review_id);
		wp_delete_comment($review_id2);
		wp_delete_comment($review_id3);
	}

	/**
	 *
	 */
	function test_review_3() {
		$ywpar_review_exp = array(
			'list' => array(
				array(
					'number' => 1,
					'points' => 10,
					'repeat' => 0
				),
				array(
					'number' => 2,
					'points' => 30,
					'repeat' => 1
				),
			),
		);
		$user             = wp_get_current_user();
		YITH_WC_Points_Rewards()->set_option( 'review_exp', $ywpar_review_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_review_exp', 'yes' );

		$product   = parent::create_and_store_points_product( array( '_price' => 10 ) );
		$review_id = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio 1' );
		$review_id2 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio' );
		$review_id3 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio sdf sdfksjbd fskdbf' );
		$review_id4 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio sf s sdfsd fsdfsdfs' );
		$review_id5 = self::create_product_review( $user->ID, $product->get_id(), 'ciaoo ciccio sdfs dfsd fsdfsdf sdfksjbd fss sdfs dfsdfkdbf' );

		wp_set_comment_status( $review_id, 'approve' );
		wp_set_comment_status( $review_id2, 'approve' );
		wp_set_comment_status( $review_id3, 'approve' );
		wp_set_comment_status( $review_id4, 'approve' );
		wp_set_comment_status( $review_id5, 'approve' );

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 70 );

		wp_delete_comment($review_id);
		wp_delete_comment($review_id2);
		wp_delete_comment($review_id3);
		wp_delete_comment($review_id4);
		wp_delete_comment($review_id5);
	}
	/**
	 * Creates a new product review on a specific product.
	 *
	 * @since 3.0
	 * @param $product_id integer Product ID that the review is for
	 * @param $revieww_content string Content to use for the product review
	 * @return integer Product Review ID
	 */
	public static function create_product_review( $user_id, $product_id, $review_content = 'Review content here' ) {

		$data = array(
			'comment_post_ID'      => $product_id,
			'comment_author'       => 'admin',
			'comment_author_email' => 'woo@woo.local',
			'comment_author_url'   => '',
			//'comment_date'         => '2016-01-01T11:11:11',
			'comment_content'      => $review_content,
			'comment_approved'     => 0,
			'comment_type'         => 'review',
			'user_id'              => $user_id
		);
		return wp_insert_comment( $data );
	}
}
