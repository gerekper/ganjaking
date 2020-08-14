<?php
/**
 * Class YWPAR_Tests_Review_Extra_Points
 *
 * @package YITH
 */

/**
 * Calculate points per product
 */
class YWPAR_Tests_Num_Order_Extra_Points extends YWPAR_Unit_Test_Case_With_Store {
	function test_order_num_1(){
		$ywpar_exp = array(
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
		YITH_WC_Points_Rewards()->set_option('num_order_exp', $ywpar_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_num_order_exp', 'yes');


		$product = parent::create_and_store_points_product( array('_price'=> 10 ));
		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);
		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 14 );

	}

	function test_order_num_2(){
		$ywpar_exp = array(
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
		YITH_WC_Points_Rewards()->set_option('num_order_exp', $ywpar_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_num_order_exp', 'yes');


		$product = parent::create_and_store_points_product( array('_price'=> 10 ));
		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);
		$order2 = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed'); //earn 4 point
		$order2->set_status('completed'); //earn 4 point

		$order->save();
		$order2->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 18 );
	}


	function test_order_num_3(){
		$ywpar_exp = array(
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
		$user = wp_get_current_user();
		YITH_WC_Points_Rewards()->set_option('num_order_exp', $ywpar_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_num_order_exp', 'yes');


		$product = parent::create_and_store_points_product( array('_price'=> 10 ));

		for ( $i = 1; $i < 7; $i ++ ) {
			$order = WC_Helper_Order::create_order($user->ID, $product);
			$order->set_status('completed'); //earn 4 point
			$order->save();
		}
		// 24 points for 6 order
		// 10 points at first
		// 60 points double second rule
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 94 );
	}
}