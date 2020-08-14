
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Cart_Checkout_Ajax_Calls' ) ) {

    /**
     *
     * @class   YITH_YWGC_Cart_Checkout
     *
     * @since   1.0.0
     * @author  Lorenzo Giuffrida
     */
    class YITH_YWGC_Cart_Checkout_Ajax_Calls extends YITH_YWGC_Cart_Checkout
    {


        /**
         * Constructor
         *
         * Initialize test
         *
         * @since 1.8.6
         * @author Daniel Sanchez
         */
        public function __construct() {



        }

        /**
         * Check if the gift card code provided is valid and store the amount for
         * applying the discount to the cart
         */
        public function apply_gift_card_code_callback_ajax_call( $code ){

            if ( ! empty( $code )) {
                $gift = YITH_YWGC()->get_gift_card_by_code( $code );

                if ( YITH_YWGC()->check_gift_card( $gift ) ) {
                    $this->add_gift_card_code_to_session( $code );

                    //wc_add_notice( $gift->get_gift_card_message(YITH_YWGC_Gift_Card::GIFT_CARD_SUCCESS ) );
                }
                //wc_print_notices();
            }

            //die();
        }

        public function remove_gift_card_code_callback_ajax_call( $code ) {

            if ( ! empty( $code ) ) {

                $gift = YITH_YWGC()->get_gift_card_by_code( $code );
                if ( YITH_YWGC()->check_gift_card( $gift, true ) ) {
                    $this->remove_gift_card_code_from_session( $code );

                    //wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_REMOVED ) );
                }
                //wc_print_notices();
            }

            //die();
        }

    }

}


