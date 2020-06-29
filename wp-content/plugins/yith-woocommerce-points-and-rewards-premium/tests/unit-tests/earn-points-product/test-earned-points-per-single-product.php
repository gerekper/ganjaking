<?php
/**
 * Class YWPAR_Tests_Point_Product
 *
 * @package YITH
 */

/**
 * Calculate points per product
 */
class YWPAR_Tests_Earned_Points_Single_Product extends YWPAR_Unit_Test_Case_With_Store {

	/**
	 * @var
	 */
	public $product;

	/**
	 * Test is default calculation point
	 */
	function test_is_point_product() {
		$product = parent::create_and_store_points_product();
		$product->set_price(136);
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);
		$this->assertEquals( $points, 14 );
	}

	/**
	 * Test is 1 - 1 calculation point
	 */
	function test_is_point_product_2() {
		$product = parent::create_and_store_points_product();
		$product->set_price(136);
		$args = array( parent::$currency => array(
			'money' => 1,
			'points' => 1
		));
		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);
		$this->assertEquals( $points, 136 );
	}

	/**
	 * Test 25 - 1 calculation points
	 */
	function test_is_point_product_3() {
		$product = parent::create_and_store_points_product();
		$product->set_price(136);
		$args = array( parent::$currency => array(
			'money' => 25,
			'points' => 1
		));
		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);
		$this->assertEquals( $points, 5 );
	}

	/**
	 * Test 25 - 1 + product override
	 */
	function test_is_point_product_4() {
		$args = array(
			'_ywpar_point_earned' => 25
		);
		$product = parent::create_and_store_points_product( $args );
		$product->set_price(136);

		$args = array( parent::$currency => array(
			'money' => 25,
			'points' => 1
		));
		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);
		$this->assertEquals( $points, 25 );
	}

	/**
	 * Test 25 - 1 + product override + data validation valid
	 */
	function test_is_point_product_5() {
		$args = array(
			'_ywpar_point_earned' => 25,
			'_ywpar_point_earned_dates_from' =>strtotime('2018-11-22'),
			'_ywpar_point_earned_dates_to' => time() + DAY_IN_SECONDS,
		);

		$product = parent::create_and_store_points_product( $args );
		$product->set_price(136);

		$args = array( parent::$currency => array(
			'money' => 25,
			'points' => 1
		));

		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);

		$this->assertEquals( $points, 25 );
	}

	/**
	 * Test 25 - 1 + product override + data validation expired
	 */
	function test_is_point_product_6() {
		$args = array(
			'_ywpar_point_earned' => 25,
			'_ywpar_point_earned_dates_from' =>strtotime('2018-11-20'),
			'_ywpar_point_earned_dates_to' =>strtotime('2018-11-21'),
		);

		$product = parent::create_and_store_points_product( $args );
		$product->set_price(136);

		$args = array( parent::$currency => array(
			'money' => 25,
			'points' => 1
		));

		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);

		$this->assertEquals( $points, 5 );
	}

	/**
	 * Test 25 - 1 + category override
	 */
	 function test_is_point_product_7(){
		$product = parent::create_and_store_create_points_single_product_with_category(array(), array('cat_1'));
		$categories = $product->get_category_ids();

		 foreach ( $categories as $category_id ) {
			 update_term_meta( $category_id, 'point_earned', 25 );
		 }


		 $product->set_price(136);

		 $args = array( parent::$currency => array(
			 'money' => 25,
			 'points' => 1
		 ));

		 YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		 $points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);

		 $this->assertEquals( $points, 25 );
	 }

	/**
	 * Test 25 - 1 + category override + data validation valid
	 */
	 function test_is_point_product_8(){
		$product = parent::create_and_store_create_points_single_product_with_category(array(), array('cat_1'));
		$categories = $product->get_category_ids();

		 foreach ( $categories as $category_id ) {
			 update_term_meta( $category_id, 'point_earned', 25 );
			 update_term_meta( $category_id, 'point_earned_dates_from', time() - DAY_IN_SECONDS );
			 update_term_meta( $category_id, 'point_earned_dates_to', time() + DAY_IN_SECONDS );
		 }


		 $product->set_price(136);

		 $args = array( parent::$currency => array(
			 'money' => 25,
			 'points' => 1
		 ));

		 YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		 $points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);

		 $this->assertEquals( $points, 25 );
	 }

	/**
	 * Test 25 - 1 + category override + data validation expired
	 */
	function test_is_point_product_9(){
		$product = parent::create_and_store_create_points_single_product_with_category(array(), array('cat_1'));
		$categories = $product->get_category_ids();

		foreach ( $categories as $category_id ) {
			update_term_meta( $category_id, 'point_earned', 25 );
			update_term_meta( $category_id, 'point_earned_dates_from', time() + DAY_IN_SECONDS );
			update_term_meta( $category_id, 'point_earned_dates_to', time() + 2 * DAY_IN_SECONDS );
		}
		$product->set_price(136);
		$args = array( parent::$currency => array(
			'money' => 25,
			'points' => 1
		));

		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		$points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product, 1, parent::$currency);

		$this->assertEquals( $points, 5 );
	}

}
