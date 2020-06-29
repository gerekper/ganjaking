<?php
/**
 * Class YWPAR_Tests_Amount_Spent_Extra_Points
 *
 * @package YITH
 */

/**
 * Calculate points per product
 */
class YWPAR_Tests_Amount_Spent_Extra_Points extends YWPAR_Unit_Test_Case_With_Store {
	function test_amount_1(){
		$ywpar_exp = array(
			'list' => array(
				array(
					'number' => 50,
					'points' => 10,
					'repeat' => 0
				),
				array(
					'number' => 200,
					'points' => 30,
					'repeat' => 0
				),
			),
		);
		$user = wp_get_current_user();
		YITH_WC_Points_Rewards()->set_option('amount_spent_exp', $ywpar_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_amount_spent_exp', 'yes');

		$product = parent::create_and_store_points_product( array('_price'=> 20 ));

		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);

		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 18 );

		$product2 = parent::create_and_store_points_product( array( '_price' => 50 ) );
		$order    = WC_Helper_Order::create_order( $user->ID, $product2 );
		$order->set_status( 'completed' );
		$order->save();
		//200 + 30 => amount
		//20  point + 18 point + 30
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 68 );

	}

	function test_amount_2(){
		$ywpar_exp = array(
			'list' => array(
				array(
					'number' => 50,
					'points' => 10,
					'repeat' => 0
				),
				array(
					'number' => 200,
					'points' => 30,
					'repeat' => 1
				),
			),
		);
		$user = wp_get_current_user();
		YITH_WC_Points_Rewards()->set_option('amount_spent_exp', $ywpar_exp );
		YITH_WC_Points_Rewards()->set_option( 'enable_amount_spent_exp', 'yes');

		$product = parent::create_and_store_points_product( array('_price'=> 20 ));
		//qty = 4
		$order = WC_Helper_Order::create_order($user->ID, $product);
		$order->set_status('completed');
		$order->save();

		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 18 );

		$product2 = parent::create_and_store_points_product( array( '_price' => 50 ) );
		$order    = WC_Helper_Order::create_order( $user->ID, $product2 );
		$order->set_status( 'completed' );
		$order->save();
		//200 + 30 => amount
		//20  point + 18 point + 30
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 68 );

		$product3 = parent::create_and_store_points_product( array( '_price' => 50 ) );
		$order    = WC_Helper_Order::create_order( $user->ID, $product2 );
		$order->set_status( 'completed' );
		$order->save();

		//68 + 20 + 30
		$total_point = get_user_meta( $user->ID, '_ywpar_user_total_points', true );
		$this->assertEquals( $total_point, 118 );

	}

}