<?php

/**
 * Class WC_Helper_Product.
 *
 * This helper class should ONLY be used for unit tests!.
 *
 */
class YITH_WC_Gift_Card_helper extends WP_UnitTestCase {

    /**
     * arguments of the gift card test
     *
     * @param array
     * @since 2.0.0
     */
    public $gift_card_products_args;

    /**
     * arguments of the products to create with prices to apply the gift cards created
     *
     * @param array
     * @since 2.0.0
     */
    public $products_args;

    /**
     * This is the final total after apllying the gift cards
     *
     * @param array
     * @since 2.0.0
     */
    public $final_total;

    /**
     * gift cards product created
     *
     * @param array
     * @since 2.0.0
     */
    public $gift_cards_instances = array();

    /**
     * orders created
     *
     * @param array
     * @since 2.0.0
     */
    public $orders_created = array();

    /**
     * products created
     *
     * @param array
     * @since 2.0.0
     */
    public $products_created = array();

    /**
     * email contents sent
     *
     * @param array
     * @since 2.0.0
     */
    public $email_contents_sent = array();

    /**
     * gift cards to apply
     *
     * @param array
     * @since 2.0.0
     */
    public $gift_cards_to_apply = array();

    /**
     * gift cards to remove
     *
     * @param array
     * @since 2.0.0
     */
    public $gift_cards_to_remove = array();

    /**
     * default_gift_product
     *
     * @param array
     * @since 2.0.0
     */
    public $default_gift_product = null;

    /**
     * Constructor
     *
     * Initialize test
     *
     * @since 2.0.0
     * @author Daniel Sanchez
     */
    public function __construct( $args, $products_args, $ywgc_minimal_car_total, $final_total ) {

        $this->gift_card_products_args = $args;
        $this->products_args = $products_args;
        $this->final_total = $final_total;
        if ( $ywgc_minimal_car_total )
            update_option( 'ywgc_minimal_car_total', $ywgc_minimal_car_total );

        $this->default_gift_product = WC_Helper_Product::create_gift_card_product( 'digital' );

        $this->products_created[ 'gift_this_product' ] = array();
        $this->products_created[ 'gift_gards' ] = array();
        $this->products_created[ 'apllying_gift_cards' ] = array();

        $this->products_created[ 'gift_gards' ][] = $this->default_gift_product->get_id();

    }

    /**
     * Testing gift cards.
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_gift_card_run_test() {

        echo "\r\n";
        echo "\r\n";

        $this->yith_wc_ut_create_gift_products_and_add_to_cart();

        $this->orders_created[ 'creating' ] = $this->yith_wc_ut_create_order_from_cart();

        WC()->cart->empty_cart();

        $this->yith_wc_ut_get_email_files_sent();

        $this->yith_wc_ut_checking_order();

        $this->yith_wc_ut_checking_gift_cards_to_apply();

        WC()->cart->empty_cart();

        $this->yith_wc_ut_cleaning_db();

        echo "\r\n";
        echo "\r\n";

    }

    /**
     * Building cart items
     *
     * @since 2.0.0
     *
     * @return array
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_gift_card_build_cart_item_data( $args, $type, $gift_card_product_id ) {

        $cart_item_data[ 'ywgc_test_apply' ]        = $args[ 'ywgc_test_apply' ];
        $cart_item_data[ 'ywgc_test_remove' ]       = $args[ 'ywgc_test_remove' ];

        $cart_item_data[ 'ywgc_amount' ]            = $args[ 'ywgc_amount' ];
        $cart_item_data[ 'ywgc_is_manual_amount' ]  = $args[ 'ywgc_is_manual_amount' ];

        if ( $type ==  'digital' ){

            $cart_item_data[ 'ywgc_is_digital' ]  = 1;
            $cart_item_data[ 'ywgc_is_physical' ] = 0;

        }
        else{

            $cart_item_data[ 'ywgc_is_physical' ] = 1;
            $cart_item_data[ 'ywgc_is_digital' ]  = 0;

        }

        if ( $args[ 'ywgc_product_as_present' ] ) {

            $cart_item_data[ 'ywgc_product_id' ] = YITH_YWGC()->default_gift_card_id;

            $cart_item_data[ 'ywgc_product_as_present' ]    = $args[ 'ywgc_product_as_present' ];
            $cart_item_data[ 'ywgc_present_product_id' ]    = $args[ 'ywgc_present_product_id' ];
            $cart_item_data[ 'ywgc_present_variation_id' ]  = $args[ 'ywgc_present_variation_id' ];

        }
        else{
            $cart_item_data[ 'ywgc_product_id' ] = $gift_card_product_id;
        }


        if ( $cart_item_data[ 'ywgc_is_digital' ] ) {

            $cart_item_data[ 'ywgc_recipients' ]     = $args[ '_ywgc_recipients' ];
            $cart_item_data[ 'ywgc_sender_name' ]    = $args[ 'ywgc_sender_name' ];
            $cart_item_data[ 'ywgc_recipient_name' ] = $args[ 'ywgc_recipient_name' ];
            $cart_item_data[ 'ywgc_message' ]        = $args[ 'ywgc_message' ];
            $cart_item_data[ 'ywgc_postdated' ]      = $args[ 'ywgc_postdated' ];

            if ( $args[ 'ywgc_postdated' ] ) {
              $cart_item_data[ 'ywgc_delivery_date' ] = $args[ 'ywgc_delivery_date' ];
            }

            $cart_item_data[ 'ywgc_has_custom_design' ] = $args[ 'ywgc_has_custom_design' ];

            if ( $args[ 'ywgc_has_custom_design' ] ) {
              $cart_item_data[ 'ywgc_design' ]          = $args[ 'ywgc_design' ];
              $cart_item_data[ 'ywgc_design_type' ]     = $args[ 'ywgc_design_type' ];
            }

          }

        if ( $cart_item_data[ 'ywgc_is_physical' ] ) {

            $cart_item_data[ 'ywgc_recipient_name' ] = $args[ 'ywgc_recipient_name' ];
            $cart_item_data[ 'ywgc_sender_name' ]    = $args[ 'ywgc_sender_name' ];
            $cart_item_data[ 'ywgc_message' ]        = $args[ 'ywgc_message' ];

          }

      return $cart_item_data;

    }

    /**
     * Creating gift card products and add them to cart
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_create_gift_products_and_add_to_cart() {

        echo "\r\n* Creating gift cards product ( " . count( $this->gift_card_products_args ) . " )\r\n";

        foreach ( $this->gift_card_products_args as $gift_card ){

            $gift_this_product = $gift_card[ 'as_present' ];

            if ( ! $gift_this_product ){

                $gift_card_product = WC_Helper_Product::create_gift_card_product( $gift_card[ 'type' ] );

                $this->products_created[ 'gift_gards' ][] = $gift_card_product->get_id();

                $this->assertTrue( $gift_card_product instanceof WC_Product_Gift_Card );
                echo "\r\n   A-> Gift card " . $gift_card[ 'type' ] . " product with id " . $gift_card_product->get_id() . " created correctly\r\n";

                $gift_card_product_id = $gift_card_product->get_id();

            }else{

                echo "\r\n* Creating simple product with price -> " . $gift_card[ 'price' ] . "\r\n";

                $product = WC_Helper_Product::create_simple_product( $gift_card[ 'price' ] );

                $this->products_created[ 'gift_this_product' ][] = $product->get_id();

                $gift_card_product_id = $this->default_gift_product->get_id();

            }



            foreach ( $gift_card[ 'instances' ] as $key => $instance ) {

                echo "\r\n      Adding instance '" . $key . "' to the cart. Quantity -> : " . $instance[ 'quantity' ] . "\r\n";

                if ( $gift_this_product ){

                    $instance[ 'ywgc_amount' ] = $product->get_price();
                    $instance[ 'ywgc_present_product_id' ] = $product->get_id();
                    $instance[ 'ywgc_present_variation_id' ] = 0;

                }

                $item_data = $this->yith_wc_ut_gift_card_build_cart_item_data( $instance, $gift_card[ 'type' ], $gift_card_product_id );
                WC()->cart->add_to_cart( $gift_card_product_id, $instance[ 'quantity' ], 0, array(), $item_data );

            }

            $this->gift_cards_instances[] = array( 'gift_card_product' => $gift_card_product );

        }

        $items = WC()->cart->get_cart();

        foreach( $items as $item => $values ){

            $values[ 'data' ]->set_price( $values[ 'ywgc_amount' ] );

        }

        //WC()->cart->calculate_totals();

    }

    /**
     * Create an order and add items from the cart
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_create_order_from_cart() {

        $items = WC()->cart->get_cart();

        echo "\r\n* Creating an order and adding the items form the card to the order. Items: ( " . count( $items ) . " )\r\n";

        $order = wc_create_order();

        foreach ( $items as $cart_item_key => $values ) {

            $item                       = new WC_Order_Item_Product();
            $product                    = $values['data'];
            $item->legacy_values        = $values; // @deprecated For legacy actions.
            $item->legacy_cart_item_key = $cart_item_key; // @deprecated For legacy actions.
            $item->set_props( array(
                'quantity'     => $values['quantity'],
                'variation'    => $values['variation'],
                'subtotal'     => $values['line_subtotal'],
                'total'        => $values['line_total'],
                'subtotal_tax' => $values['line_subtotal_tax'],
                'total_tax'    => $values['line_tax'],
                'taxes'        => $values['line_tax_data'],
            ) );

            if ( $product ) {
                $item->set_props( array(
                    'name'         => $product->get_name(),
                    'tax_class'    => $product->get_tax_class(),
                    'product_id'   => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
                    'variation_id' => $product->is_type( 'variation' ) ? $product->get_id() : 0,
                ) );
            }
            $item->set_backorder_meta();

            $item->save();

            $order->add_item( $item );

        }

        //$order->calculate_totals();
        $order->save();

        $order->calculate_totals();

        $this->assertTrue( $order instanceof WC_Order );

        echo "\r\n   A-> Order with id " . $order->get_id() . " created correctly\r\n";

        $order->update_status( 'completed' );

        return $order;

    }

    /**
     * Retrieve all the email files sent
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_get_email_files_sent() {

        $files = array_diff( scandir( UNIT_TEST_GIFT_CARD_EMAIL_FOLDER ), array( '.', '..' ) );

        foreach ( $files as $file ){

            $content = file_get_contents( UNIT_TEST_GIFT_CARD_EMAIL_FOLDER . $file, true );
            $this->email_contents_sent[] = $content;
        }

    }

    /**
     * Testing gift cards.
     *
     * @since 2.0.0
     *
     * @return boolean
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_email_sent( $gc ) {

        $found = false;
        foreach ( $this->email_contents_sent as $content ){

            if ( strpos( $content, $gc->gift_card_number ) !== false) {
                $found = true;
            }

        }

        return $found;

    }

    /**
     * checking order
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_checking_order() {

        echo "\r\n* FOREACH ORDER: Check all the order items to find the gift card product associated to the order \r\n";
        foreach ( $this->orders_created[ 'creating' ]->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

            $product_id = $order_item_data->get_product_id();

            echo "\r\n   Checking item " . $product_id . " on the order:\r\n";

            $product = wc_get_product( $product_id );

            if ( ! $product instanceof WC_Product_Gift_Card ) {
                echo "\r\n   -> Item " . $product_id . " is a NOT gift card\r\n";
                continue;
            }

            echo "\r\n      -> Item " . $product_id . " is a gift card\r\n";

            $Array_Codes = is_array( wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_CODE ) ) ? wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_CODE ) : array();

            $this->assertTrue( count( $Array_Codes ) > 0 );
            echo "\r\n      A-> the gift card code or codes created correctly: \r\n ";

            $test_apply    = wc_get_order_item_meta ( $order_item_id, '_ywgc_test_apply' );
            $test_remove    = wc_get_order_item_meta ( $order_item_id, '_ywgc_test_remove' );

            foreach ( $Array_Codes as $code ){

                echo "\r\n            ". $code . "\r\n ";

                if ( $test_apply ){
                    echo "\r\n            This gift card code is going to be APPLIED **************\r\n ";
                    $this->gift_cards_to_apply[ $code ] = $code;
                }

                if ( $test_remove ){
                    echo "\r\n            This gift card code is going to be REMOVED **************\r\n ";
                    $this->gift_cards_to_remove[ $code ] = $code;
                }

            }

            $is_digital       = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_digital' );

            if ( $is_digital ){

                $this->assertTrue( $is_digital == 1 );
                echo "\r\n      A-> the gift card item is DIGITAL\r\n";

            }
            else{

                $this->assertTrue( $is_digital != 1 );
                echo "\r\n      A-> the gift card item is NOT DIGITAL\r\n";

            }

            echo "\r\n   Checking YWGC_Gift_Card_Premium from item " . $product_id . " :\r\n";

            $gift_ids = ywgc_get_order_item_giftcards( $order_item_id );

            $this->assertTrue( count( $gift_ids ) > 0 );
            echo "\r\n      A-> Getting YWGC_Gift_Card_Premium's ( " . count( $gift_ids ) . " ): \r\n";

            foreach ( $gift_ids as $gift_id ){

                $gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

                $this->assertTrue( $gc instanceof YWGC_Gift_Card_Premium );

                echo "\r\n            A-> Gift card ID: " . $gift_id . " - is an instance of WGC_Gift_Card_Premium \r\n";

                $this->assertFalse( empty( $gc->gift_card_number ) );

                echo "\r\n                  A-> code created: " . $gc->gift_card_number . " \r\n";

                echo "\r\n                  -> total_amount: " . $gc->total_amount . " \r\n";
                echo "\r\n                  -> total_balance: " . $gc->get_balance() . " \r\n";

                if ( $gc->is_digital ){

                    echo "\r\n                  -> is digital: \r\n";

                    if ( $gc->has_been_sent() ){

                        echo "\r\n                      -> gift card set as SENT. Let's check if it is true \r\n";

                        $this->assertTrue( $this->yith_wc_ut_email_sent( $gc) );

                        echo "\r\n                          -> OK. Email sent is being FOUND  \r\n";

                    }
                    else
                        echo "\r\n                  -> gift card with pospone delivery \r\n";

                }
                else
                    echo "\r\n                  -> is physical \r\n";

            }

        }

        echo "\r\n* END OF FOREACH ORDER\r\n";

        echo "\r\n*********************************************************\r\n";
        echo "*                                                       *\r\n";
        echo "*   ORDER TOTAL OF GIFT CARDS -> " . $this->orders_created[ 'creating' ]->get_total() . "\r\n";
        echo "*                                                       *\r\n";
        echo "*********************************************************\r\n";

    }


    /**
     * Testing gift cards.
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_checking_gift_cards_to_apply() {

        echo "\r\n";
        echo "\r\n";
        echo "\r\n**** APPLYING GIFT CARDS ****\r\n";

        $YITH_YWGC_Cart_Checkout_Ajax_Call = new YITH_YWGC_Cart_Checkout_Ajax_Calls();

        foreach ( $this->products_args as $price ){

            echo "\r\n* Creating simple product with price -> " . $price . "\r\n";

            $product = WC_Helper_Product::create_simple_product( $price );

            $this->products_created[ 'apllying_gift_cards' ][] = $product->get_id();

            echo "\r\n        Adding to the cart \r\n";

            WC()->cart->add_to_cart( $product->get_id(), 1 );

            echo "\r\n        Cart total -> " . WC()->cart->total . "\r\n";

        }

        echo "\r\n* Applying gift card codes: \r\n";

        foreach ( $this->gift_cards_to_apply as $code ){

            $gift = YITH_YWGC()->get_gift_card_by_code( $code );

            if ( YITH_YWGC()->check_gift_card( $gift, true ) ) {

                echo "\r\n     Applying code " . $code . " to the cart with balance -> " . $gift->get_balance() . " \r\n";

                $YITH_YWGC_Cart_Checkout_Ajax_Call->apply_gift_card_code_callback_ajax_call( $code );
            }

            WC()->cart->calculate_totals();

            $cart_total = WC()->cart->get_total('edit');

            echo "\r\n     Cart total -> " . $cart_total . "\r\n";

        }

        echo "\r\n* Removing gift card codes: \r\n";

        foreach ( $this->gift_cards_to_remove as $code ){

            $gift = YITH_YWGC()->get_gift_card_by_code( $code );

            if ( YITH_YWGC()->check_gift_card( $gift, true ) ) {

                echo "\r\n     Removing code " . $code . " to the cart with balance -> " . $gift->get_balance() . " \r\n";

                $YITH_YWGC_Cart_Checkout_Ajax_Call->remove_gift_card_code_callback_ajax_call( $code );
            }

            WC()->cart->calculate_totals();

            $cart_total = WC()->cart->get_total('edit');

            echo "\r\n     Cart total -> " . $cart_total . "\r\n";

        }

        $items = WC()->cart->get_cart();

        $order = $this->yith_wc_ut_create_order_from_cart();

        do_action( 'woocommerce_saved_order_items', $order->get_id(), $items );

        /*$order = wc_create_order();

        wc_save_order_items( $order->get_id(), $items );*/

        $this->orders_created[ 'applying' ] = wc_get_order( $order->get_id() );

        foreach ( $this->gift_cards_to_apply as $code ) {

            $gift = YITH_YWGC()->get_gift_card_by_code( $code );

            echo "\r\n     Gift card code " . $code . " with balance after apllying -> " . $gift->get_balance() . " \r\n";

        }

        echo "\r\n*********************************************************\r\n";
        echo "*                                                       *\r\n";
        echo "*   ORDET TOTAL WITH GIFT CARD CODES APPLIED -> " . $this->orders_created[ 'applying' ]->get_total() . "\r\n";
        echo "*                                                       *\r\n";
        echo "*********************************************************\r\n";

        $this->assertTrue( $this->final_total == $this->orders_created[ 'applying' ]->get_total() );

    }

    /**
     * Testing gift cards.
     *
     * @since 2.0.0
     *
     * @return output
     * @author Daniel Sanchez
     *
     */
    public function yith_wc_ut_cleaning_db() {

        foreach ( $this->gift_cards_instances as $gift_cards_instance )
            WC_Helper_Product::delete_product( $gift_cards_instance[ 'gift_card_product' ]->get_id() );

        foreach ( $this->products_created[ 'gift_this_product' ] as $product_id )
            WC_Helper_Product::delete_product( $product_id );

        foreach ( $this->products_created[ 'apllying_gift_cards' ] as $product_id )
            WC_Helper_Product::delete_product( $product_id );

        foreach ( $this->products_created[ 'gift_gards' ] as $product_id )
            WC_Helper_Product::delete_product( $product_id );

        foreach ( $this->orders_created as $order )
            WC_Helper_Order::delete_order( $order->get_id() );

        foreach ( $this->gift_cards_to_apply as $code ) {

            $gift = YITH_YWGC()->get_gift_card_by_code( $code );

            wp_delete_post( $gift->ID );

        }

        foreach ( $this->gift_cards_to_remove as $code ) {

            $gift = YITH_YWGC()->get_gift_card_by_code( $code );

            wp_delete_post( $gift->ID );

        }

    }

}
