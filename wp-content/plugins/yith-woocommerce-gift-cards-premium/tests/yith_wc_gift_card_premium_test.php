<?php
/**
 * Class SampleTest
 *
 * @package Yith_Woocommerce_Gift_Cards.premium
 */

/**
 * Sample test case.
 */
class Test_One_Physical_Gift_Card extends WP_UnitTestCase {

    /**
     * test2
     */
	function test(){

      $gift_card_products_args =
          array(
              'gift1' => array(
                  'type' => 'digital',
                  // gift this product
                  'as_present' => 0,
                  'instances' => array(
                      'instance1' => array(
                          'quantity' => 1,
                          'ywgc_amount' => '40',
                          //digital data
                          '_ywgc_recipients' => array( 'daniel.sanchez1@yithemes.com' ),
                          'ywgc_recipient_name' => 'yith receiver name instance1',
                          'ywgc_sender_name' => 'yith sender name instance1',
                          'ywgc_message' => 'message for this gift card instance1',
                          'ywgc_postdated' => 0,
                          'ywgc_delivery_date' => '',
                          'ywgc_is_manual_amount' => 0,
                          // design
                          'ywgc_design_type' => 0,
                          'ywgc_has_custom_design' => 0,
                          'ywgc_design' => 0,
                          // test options
                          'ywgc_test_apply' => 1,
                          'ywgc_test_remove' => 0,
                      ),
                      'instance2' => array(
                          'quantity' => 3,
                          'ywgc_amount' => '35',
                          //digital data
                          '_ywgc_recipients' => array( 'daniel.sanchez2@yithemes.com' ),
                          'ywgc_recipient_name' => 'yith receiver name instance2',
                          'ywgc_sender_name' => 'yith sender name instance2',
                          'ywgc_message' => 'message for this gift card instance2',
                          'ywgc_postdated' => 0,
                          'ywgc_delivery_date' => '',
                          'ywgc_is_manual_amount' => 0,
                          // design
                          'ywgc_design_type' => 0,
                          'ywgc_has_custom_design' => 0,
                          'ywgc_design' => 0,
                          // test options
                          'ywgc_test_apply' => 0,
                          'ywgc_test_remove' => 0,
                      ),
                  ),
              ),
              'gift2' => array(
                  'type' => 'physical',
                  // gift this product
                  'as_present' => 0,
                  'instances' => array(
                      'instance1' => array(
                          'quantity' => 2,
                          'ywgc_amount' => '20',
                          //digital data
                          '_ywgc_recipients' => array( 'daniel.sanchez1@yithemes.com' ),
                          'ywgc_recipient_name' => 'yith receiver name instance1',
                          'ywgc_sender_name' => 'yith sender name instance1',
                          'ywgc_message' => 'message for this gift card instance1',
                          'ywgc_postdated' => 0,
                          'ywgc_delivery_date' => '',
                          'ywgc_is_manual_amount' => 0,
                          // design
                          'ywgc_design_type' => 0,
                          'ywgc_has_custom_design' => 0,
                          'ywgc_design' => 0,
                          // test options
                          'ywgc_test_apply' => 1,
                          'ywgc_test_remove' => 1,
                      ),
                      'instance2' => array(
                          'quantity' => 1,
                          'ywgc_amount' => '75',
                          //digital data
                          '_ywgc_recipients' => array( 'daniel.sanchez2@yithemes.com' ),
                          'ywgc_recipient_name' => 'yith receiver name instance2',
                          'ywgc_sender_name' => 'yith sender name instance2',
                          'ywgc_message' => 'message for this gift card instance2',
                          'ywgc_postdated' => 0,
                          'ywgc_delivery_date' => '',
                          'ywgc_is_manual_amount' => 0,
                          // design
                          'ywgc_design_type' => 0,
                          'ywgc_has_custom_design' => 0,
                          'ywgc_design' => 0,
                          // test options
                          'ywgc_test_apply' => 1,
                          'ywgc_test_remove' => 1,
                      ),
                  ),

              ),
              'gift3' => array(
                  'type' => 'digital',
                  // gift this product
                  'as_present' => 1,
                  'price' => '50',
                  'product_type' => 'simple',
                  'instances' => array(
                      'instance1' => array(
                          'quantity' => 2,
                          //digital data
                          '_ywgc_recipients' => array( 'daniel.sanchez1@yithemes.com' ),
                          'ywgc_recipient_name' => 'yith receiver name instance1',
                          'ywgc_sender_name' => 'yith sender name instance1',
                          'ywgc_message' => 'message for this gift card instance1',
                          'ywgc_postdated' => 0,
                          'ywgc_delivery_date' => '',
                          'ywgc_is_manual_amount' => 0,
                          // design
                          'ywgc_design_type' => 0,
                          'ywgc_has_custom_design' => 0,
                          'ywgc_design' => 0,
                          // test options
                          'ywgc_test_apply' => 1,
                          'ywgc_test_remove' => 1,
                      ),
                      'instance2' => array(
                          'quantity' => 3,
                          //digital data
                          '_ywgc_recipients' => array( 'daniel.sanchez1@yithemes.com' ),
                          'ywgc_recipient_name' => 'yith receiver name instance1',
                          'ywgc_sender_name' => 'yith sender name instance1',
                          'ywgc_message' => 'message for this gift card instance1',
                          'ywgc_postdated' => 0,
                          'ywgc_delivery_date' => '',
                          'ywgc_is_manual_amount' => 0,
                          // design
                          'ywgc_design_type' => 0,
                          'ywgc_has_custom_design' => 0,
                          'ywgc_design' => 0,
                          // test options
                          'ywgc_test_apply' => 1,
                          'ywgc_test_remove' => 1,
                      ),
                  ),

              ),
      );

      // Setting the prices of the products to add
      $products_args = array( '30', '70' );

	  $ywgc_minimal_car_total = 50;

      $final_total = 60;

      $gift_card_test = new YITH_WC_Gift_Card_helper( $gift_card_products_args, $products_args, $ywgc_minimal_car_total, $final_total );

      $gift_card_test->yith_wc_ut_gift_card_run_test();

    }

}
