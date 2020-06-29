<?php
/**
 * Class YWPAR_Tests_Expiration_Points
 *
 * @package YITH
 */

/**
 * Calculate points per product
 */
class YWPAR_Tests_Expiration_Points extends YWPAR_Unit_Test_Case_With_Store {

	/**
	 * the customer does:
	 * two orders
	 * an order with redeem coupon
	 * order refund of the last order
	 * expiration points
	 */
	function test_start(){
		YITH_WC_Points_Rewards()->reset_points();
		$args = array( parent::$currency => array(
			'money' => 1,
			'points' => 1
		));
		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		YITH_WC_Points_Rewards()->set_option('days_before_expiration', 5 );

		$user = wp_get_current_user();
		$product = parent::create_and_store_points_product( array('_price'=> 120 ));

		//First order
		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 480 );

		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 10);

		//Second order
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 960 );
		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 8);


		//REWARDED POINTS
		remove_action( 'woocommerce_order_status_changed', array( YITH_WC_Points_Rewards_Redemption(), 'clear_ywpar_coupon_after_create_order' ), 10 );
		update_option( 'woocommerce_default_customer_address', 'base' );
		update_option( 'woocommerce_tax_based_on', 'base' );

		$product = WC_Helper_Product::create_simple_product();
		$product->set_regular_price( 1000 );
		$product->save();
		$product = wc_get_product( $product->get_id() );

		$coupon = new WC_Coupon();
		$coupon->set_code( 'test-coupon-1' );
		$coupon->set_amount( 3 );
		$coupon->set_discount_type( 'fixed_cart' );
		yit_save_prop($coupon,'ywpar_coupon',1);
		$coupon->save();

		$order = wc_create_order( array(
			'status'        => 'pending',
			'customer_id'   => $user->ID,
			'customer_note' => '',
			'total'         => '',
		) );

		// Add order products
		$product_item  = new WC_Order_Item_Product();
		$coupon_item_1 = new WC_Order_Item_Coupon();

		$product_item->set_props( array(
			'product'  => $product,
			'quantity' => 1,
			'subtotal' => 1000, // Ex tax.
			'total'    => 1000,
		) );
		$coupon_item_1->set_props( array(
			'code'         => 'test-coupon-1',
			'discount'     => 3,
			'discount_tax' => 0,
		) );
		$product_item->save();
		$coupon_item_1->save();

		$order->add_item( $product_item );
		$order->apply_coupon( 'test-coupon-1' );

		$order->calculate_totals( true );

		yit_set_prop( $order, '_ywpar_coupon_points', 300 );
		yit_set_prop( $order, '_ywpar_coupon_amount', 3 );
		$order->set_status('completed');
		$order->save();
	 	YITH_WC_Points_Rewards_Redemption()->deduce_order_points( $order );
		$redeemed = get_user_meta( $user->ID, '_ywpar_rewarded_points', true );
		$this->assertEquals( $redeemed, 300 );
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 1657 );
		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 3, 2);

		$order->set_status('refunded');
		$order->save();
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 960 );

		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 2, 2);

		YITH_WC_Points_Rewards()->set_expired_points();

		$history = YITH_WC_Points_Rewards()->get_history( get_current_user_id());
		print_r( $history );

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 0 );

	}



	/**
	 * the customer does:
	 * two orders
	 * an order with redeem coupon
	 * expiration points
	 */
	function test_expiration_2(){
		YITH_WC_Points_Rewards()->reset_points();
		$args = array( parent::$currency => array(
			'money' => 1,
			'points' => 1
		));
		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		YITH_WC_Points_Rewards()->set_option('days_before_expiration', 5 );

		$user = wp_get_current_user();
		$product = parent::create_and_store_points_product( array('_price'=> 120 ));

		//First order
		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 480 );

		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 10);

		//Second order
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 960 );
		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 8);


		//REWARDED POINTS
		remove_action( 'woocommerce_order_status_changed', array( YITH_WC_Points_Rewards_Redemption(), 'clear_ywpar_coupon_after_create_order' ), 10 );
		update_option( 'woocommerce_default_customer_address', 'base' );
		update_option( 'woocommerce_tax_based_on', 'base' );

		$product = WC_Helper_Product::create_simple_product();
		$product->set_regular_price( 1000 );
		$product->save();
		$product = wc_get_product( $product->get_id() );

		$coupon = new WC_Coupon();
		$coupon->set_code( 'test-coupon-1' );
		$coupon->set_amount( 3 );
		$coupon->set_discount_type( 'fixed_cart' );
		yit_save_prop($coupon,'ywpar_coupon',1);
		$coupon->save();

		$order = wc_create_order( array(
			'status'        => 'pending',
			'customer_id'   => $user->ID,
			'customer_note' => '',
			'total'         => '',
		) );

		// Add order products
		$product_item  = new WC_Order_Item_Product();
		$coupon_item_1 = new WC_Order_Item_Coupon();

		$product_item->set_props( array(
			'product'  => $product,
			'quantity' => 1,
			'subtotal' => 1000, // Ex tax.
			'total'    => 1000,
		) );
		$coupon_item_1->set_props( array(
			'code'         => 'test-coupon-1',
			'discount'     => 3,
			'discount_tax' => 0,
		) );
		$product_item->save();
		$coupon_item_1->save();

		$order->add_item( $product_item );
		$order->apply_coupon( 'test-coupon-1' );

		$order->calculate_totals( true );

		yit_set_prop( $order, '_ywpar_coupon_points', 300 );
		yit_set_prop( $order, '_ywpar_coupon_amount', 3 );
		$order->set_status('completed');
		$order->save();
		YITH_WC_Points_Rewards_Redemption()->deduce_order_points( $order );
		$redeemed = get_user_meta( $user->ID, '_ywpar_rewarded_points', true );
		$this->assertEquals( $redeemed, 300 );
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 1657 );
		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 3, 2);

		YITH_WC_Points_Rewards()->set_expired_points();

		$history = YITH_WC_Points_Rewards()->get_history( get_current_user_id());
		print_r( $history );

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 997 );

	}


	/**
	 * the customer does:
	 * two orders
	 * an order with redeem coupon
	 * partial refund
	 * expiration points
	 */
	function test_expiration_3(){
		YITH_WC_Points_Rewards()->reset_points();
		$args = array( parent::$currency => array(
			'money' => 1,
			'points' => 1
		));
		YITH_WC_Points_Rewards()->set_option('earn_points_conversion_rate', $args );
		YITH_WC_Points_Rewards()->set_option('days_before_expiration', 5 );

		$user = wp_get_current_user();
		$product = parent::create_and_store_points_product( array('_price'=> 120 ));

		//First order
		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 480 );

		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 10);

		//Second order
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 960 );
		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 8);


		//REWARDED POINTS
		remove_action( 'woocommerce_order_status_changed', array( YITH_WC_Points_Rewards_Redemption(), 'clear_ywpar_coupon_after_create_order' ), 10 );
		update_option( 'woocommerce_default_customer_address', 'base' );
		update_option( 'woocommerce_tax_based_on', 'base' );

		$product = WC_Helper_Product::create_simple_product();
		$product->set_regular_price( 1000 );
		$product->save();
		$product = wc_get_product( $product->get_id() );

		$coupon = new WC_Coupon();
		$coupon->set_code( 'test-coupon-1' );
		$coupon->set_amount( 3 );
		$coupon->set_discount_type( 'fixed_cart' );
		yit_save_prop($coupon,'ywpar_coupon',1);
		$coupon->save();

		$order = wc_create_order( array(
			'status'        => 'pending',
			'customer_id'   => $user->ID,
			'customer_note' => '',
			'total'         => '',
		) );

		// Add order products
		$product_item  = new WC_Order_Item_Product();
		$coupon_item_1 = new WC_Order_Item_Coupon();

		$product_item->set_props( array(
			'product'  => $product,
			'quantity' => 1,
			'subtotal' => 1000, // Ex tax.
			'total'    => 1000,
		) );
		$coupon_item_1->set_props( array(
			'code'         => 'test-coupon-1',
			'discount'     => 3,
			'discount_tax' => 0,
		) );
		$product_item->save();
		$coupon_item_1->save();

		$order->add_item( $product_item );
		$order->apply_coupon( 'test-coupon-1' );

		$order->calculate_totals( true );

		yit_set_prop( $order, '_ywpar_coupon_points', 300 );
		yit_set_prop( $order, '_ywpar_coupon_amount', 3 );
		$order->set_status('completed');
		$order->save();
		YITH_WC_Points_Rewards_Redemption()->deduce_order_points( $order );
		$redeemed = get_user_meta( $user->ID, '_ywpar_rewarded_points', true );
		$this->assertEquals( $redeemed, 300 );

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 1657 );
		YWSBS_Helper_Points_table::update_date_to_the_last( $user->ID, 3, 2);


		wc_create_refund(
			array(
				'order_id'   => $order->get_id(),
				'amount'     => '100',
				'line_items' => array(),
			)
		);

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );




		//the rewarded points are now added because the option 'reassing_redeemed_points_refund_order' is yes
		$this->assertEquals( $total_point, 1857 );

		YITH_WC_Points_Rewards()->set_expired_points();

		$history = YITH_WC_Points_Rewards()->get_history( get_current_user_id());
		print_r( $history );

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );

		$this->assertEquals( $total_point, 897 );

	}
}