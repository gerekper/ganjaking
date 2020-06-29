<?php
/**
 * Class YWPAR_Tests_Renewal
 *
 * @package YITH
 */

/**
 * Calculate points per product
 */
class YWPAR_Tests_Renewal extends YWPAR_Unit_Test_Case_With_Store {

	/**
	 * @var
	 */
	public $product;

	/**
	 * Test is default calculation point
	 */
	function test_is_point_product() {
		$product = parent::create_and_store_points_product( array('_price'=> 136));
		parent::add_points_to_user(1000);
		$max_discount = YITH_WC_Points_Rewards_Redemption()->calculate_product_max_discounts( $product->get_id(), $product->get_price());
		$this->assertEquals( $max_discount, 136 );
		WC()->cart->add_to_cart( $product->get_id(), 2 );
		$rewards_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		$this->assertEquals( $rewards_discount, 10 );
		WC()->cart->empty_cart();
	}


	function test_is_point_product2() {
		parent::add_points_to_user(1000);

		$product = parent::create_and_store_points_product( array('_price'=> 136,'_ywpar_max_point_discount'=> 5 ));
		WC()->cart->add_to_cart( $product->get_id(), 1 );
		$rewards_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		$this->assertEquals( $rewards_discount, 5 );

		$product2 = parent::create_and_store_points_product( array('_price'=> 136,'_ywpar_max_point_discount'=> 5 ));
		WC()->cart->add_to_cart( $product2->get_id(), 1 );
		$rewards_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		//	$this->assertEquals( $rewards_discount, 10 );

		// Create coupon
		$coupon = WC_Helper_Coupon::create_coupon( 'dummycoupon', array(
			'discount_type'          => 'fixed',
			'coupon_amount'          => '10',
			'limit_usage_to_x_items' => 1,
		) );

		update_post_meta( $coupon->get_id(), 'ywpar_coupon', 1 );

		// We need this to have the calculate_totals() method calculate totals.
		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		// Add 2 products and coupon to cart.
		WC()->cart->add_discount( $coupon->get_code() );
		WC()->cart->calculate_totals();

		//	$this->assertEquals( 262, WC()->cart->total );
		//	$this->assertEquals( 272, WC()->cart->get_subtotal() );
		WC()->cart->empty_cart();
	}

	/**
	 * calculate max rewards discount and points for fixed rewards
	 * @throws Exception
	 */
	function test_fixed_discount() {
		parent::add_points_to_user( 1000 );
		$product = parent::create_and_store_points_product( array( '_price' => 10 ) );
		WC()->cart->add_to_cart( $product->get_id(), 1 );
		$rewards_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		$this->assertEquals( $rewards_discount, 10 );

		$product2 = parent::create_and_store_points_product( array( '_price' => 20 ) );
		WC()->cart->add_to_cart( $product2->get_id(), 2 );

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		WC()->cart->calculate_totals();

		$this->print_discount_points_asserts( 10, 1000 );

		parent::add_points_to_user( 0 );
		$rewards_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		$this->assertEquals( $rewards_discount, 0 );

		WC()->cart->empty_cart();
	}


	function test_percentual_discount_with_limits(){
		YITH_WC_Points_Rewards()->set_option('conversion_rate_method', 'percentage');
		$args = array( parent::$currency => array(
			'points' => 20,
			'discount' => 5
		));
		YITH_WC_Points_Rewards()->set_option('rewards_percentual_conversion_rate', $args );

		$product = parent::create_and_store_points_product( array('_price'=> 10 ));
		WC()->cart->add_to_cart( $product->get_id(), 1 );
		$product2 = parent::create_and_store_points_product( array('_price'=> 20 ));
		WC()->cart->add_to_cart( $product2->get_id(), 2 );

		YITH_WC_Points_Rewards()->set_option('max_percentual_discount', 20 );

		parent::add_points_to_user(110);
		$this->print_discount_points_asserts(10, 80 );

		parent::add_points_to_user(83);
		$this->print_discount_points_asserts(10, 80 );

		parent::add_points_to_user(15);
		$this->print_discount_points_asserts(0, 0 );

		parent::add_points_to_user(35);
		$this->print_discount_points_asserts(2.5, 20 );
		WC()->cart->empty_cart();
	}


	/**
	 * calculate max rewards discount and points for percentage rewards method
	 * @throws Exception
	 */
	function test_percentual_discount(){

		YITH_WC_Points_Rewards()->set_option('conversion_rate_method', 'percentage');
		$args = array( parent::$currency => array(
			'points' => 20,
			'discount' => 5
		));
		YITH_WC_Points_Rewards()->set_option('rewards_percentual_conversion_rate', $args );

		$product = parent::create_and_store_points_product( array('_price'=> 10 ));
		WC()->cart->add_to_cart( $product->get_id(), 1 );
		$product2 = parent::create_and_store_points_product( array('_price'=> 20 ));
		WC()->cart->add_to_cart( $product2->get_id(), 2 );

		parent::add_points_to_user(110);
		$this->print_discount_points_asserts(12.5, 100 );

		parent::add_points_to_user(83);
		$this->print_discount_points_asserts(10, 80 );

		parent::add_points_to_user(15);
		$this->print_discount_points_asserts(0, 0 );

		parent::add_points_to_user(1000);
		$this->print_discount_points_asserts(50, 400 );

		$args = array( parent::$currency => array(
			'points' => 100,
			'discount' => 5
		));
		YITH_WC_Points_Rewards()->set_option('rewards_percentual_conversion_rate', $args );

		parent::add_points_to_user(110);
		$this->print_discount_points_asserts(2.5, 100 );

		parent::add_points_to_user(83);
		$this->print_discount_points_asserts(0, 0 );

		parent::add_points_to_user(1000);
		$this->print_discount_points_asserts(25, 1000 );

		WC()->cart->empty_cart();
	}


	function print_discount_points_asserts( $discount, $points ){
		$rewards_discount = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		$this->assertEquals( $rewards_discount, $discount );
		$max_points = YITH_WC_Points_Rewards_Redemption()->get_max_points();
		$this->assertEquals( $points, $max_points );
	}

}
