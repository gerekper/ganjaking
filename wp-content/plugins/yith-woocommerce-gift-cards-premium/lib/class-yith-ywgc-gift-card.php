<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_YWGC_Gift_Card' ) ) {

    /**
     *
     * @class   YITH_YWGC_Gift_Card
     *
     * @since   1.0.0
     * @author  Lorenzo Giuffrida
     */
    class YITH_YWGC_Gift_Card {

        const E_GIFT_CARD_NOT_EXIST = 100;
        const E_GIFT_CARD_NOT_YOURS = 101;
        const E_GIFT_CARD_ALREADY_APPLIED = 102;
        const E_GIFT_CARD_EXPIRED = 103;
        const E_GIFT_CARD_DISABLED = 104;
        const E_GIFT_CARD_DISMISSED = 105;
        const E_GIFT_CARD_INVALID_REMOVED = 106;

        const GIFT_CARD_SUCCESS = 200;
        const GIFT_CARD_REMOVED = 201;
        const GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD = 202;

        const META_ORDER_ID = '_ywgc_order_id';
        const META_AMOUNT_TOTAL = '_ywgc_amount_total';
        const META_BALANCE_TOTAL = '_ywgc_balance_total';

        const META_CUSTOMER_ID = '_ywgc_customer_id'; // Refers to id of the customer that purchase the gift card

        const STATUS_ENABLED = 'publish';
        const STATUS_DISMISSED = 'ywgc-dismissed';


        /**
         * @var int the gift card id
         */
        public $ID = 0;

        /**
         * @var int  the product id
         */
        public $product_id = 0;

        /**
         * @var int the order id
         */
        public $order_id = 0;

        /**
         * @var string the gift card code
         */
        public $gift_card_number = '';

        /**
         * @var float the gift card amount
         */
        public $total_amount = 0.00;

        /**
         * @var float the gift card current balance
         */
        protected $total_balance = 0.00;

        /**
         * @var string the gift card post status
         */
        public $status = 'publish';

        /**
         * @var string the recipient for digital gift cards
         */
        public $recipient = '';

        public $customer_id = 0;

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @param  array $args the arguments
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        public function __construct( $args = array() ) {

            /**
             *  if $args['ID'] is set, retrieve the post with the same ID
             *  if $args['gift_card_number'] is set, retrieve the post with the same post_title
             */
            if ( isset( $args['ID'] ) ) {
                $post = get_post ( $args['ID'] );
            } elseif ( isset( $args['gift_card_number'] ) ) {
                $this->gift_card_number = $args['gift_card_number'];

                $post = get_page_by_title ( $args['gift_card_number'], OBJECT, YWGC_CUSTOM_POST_TYPE_NAME );
            }

            //  Load post data, if exists
            if ( isset( $post ) ) {

                $this->ID               = $post->ID;
                $this->gift_card_number = $post->post_title;
                $this->product_id       = $post->post_parent;
                //  Backward compatibility check with gift cards created with free version
                $old_order_id = get_post_meta ( $post->ID, '_gift_card_order_id', true );
                if ( ! empty( $old_order_id ) ) {
                    $this->order_id = $old_order_id;
                } else {
                    $this->order_id = get_post_meta ( $post->ID, self::META_ORDER_ID, true );
                }

                $total_amount = get_post_meta ( $post->ID, self::META_AMOUNT_TOTAL, true );
                if ( ! empty( $total_amount ) ) {
                    $this->total_amount = $total_amount;
                } else {
                    $amount     = get_post_meta ( $post->ID, '_ywgc_amount', true );
                    $amount_tax = get_post_meta ( $post->ID, '_ywgc_amount_tax', true );
                    $this->update_amount ( (float)$amount + (float)$amount_tax );
                }

                $total_balance = get_post_meta ( $post->ID, self::META_BALANCE_TOTAL, true );

                if ( ! empty( $total_balance ) ) {
                    $this->total_balance = $total_balance;
                } else {
                    $balance     = get_post_meta ( $post->ID, '_ywgc_amount_balance', true );
                    $balance_tax = get_post_meta ( $post->ID, '_ywgc_amount_balance_tax', true );
                    $balance = empty( $balance ) ? 0 : $balance;
                    $balance_tax = empty( $balance_tax ) ? 0 : $balance_tax;
                    $this->update_balance ( $balance + $balance_tax );
                }

                $this->customer_id = get_post_meta ( $post->ID, self::META_CUSTOMER_ID, true );

                $this->status = $post->post_status;
            }
        }


        /**
         * Register the order in the list of orders where the gift card was used
         *
         * @param int $order_id
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function register_order( $order_id ) {
            if ( $this->ID ) {
                //  assign the order to this gift cards...
                $orders   = $this->get_registered_orders ();
                $orders[] = $order_id;
                update_post_meta ( $this->ID, YWGC_META_GIFT_CARD_ORDERS, $orders );

                //  assign the customer to this gift cards...
                $order = wc_get_order ( $order_id );
                $this->register_user ( yit_get_prop ( $order, 'customer_user' ) );
            }
        }

        /**
         * Check if the user is registered as the gift card owner
         *
         * @param int $user_id
         *
         * @return bool
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function is_registered_user( $user_id ) {
            $customer_users = get_post_meta ( $this->ID, YWGC_META_GIFT_CARD_CUSTOMER_USER );

            return in_array ( $user_id, $customer_users );
        }

        /**
         * Register an user as the gift card owner(may be one or more)
         *
         * @param int $user_id
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function register_user( $user_id ) {
            if ( $user_id == 0 ) {
                return;
            }

            if ( $this->is_registered_user ( $user_id ) ) {
                //  the user is a register user
                return;
            }

            add_post_meta ( $this->ID, YWGC_META_GIFT_CARD_CUSTOMER_USER, $user_id );
        }

        /**
         * Retrieve the list of orders where the gift cards was used
         *
         * @return array|mixed
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function get_registered_orders() {
            $orders = array();

            if ( $this->ID ) {
                $orders = get_post_meta ( $this->ID, YWGC_META_GIFT_CARD_ORDERS, true );
                if ( ! $orders ) {
                    $orders = array();
                }
            }

            return array_unique ( $orders );
        }

        /**
         * Check if the gift card has enough balance to cover the amount requested
         *
         * @param $amount int the amount to be deducted from current gift card balance
         *
         * @return bool the gift card has enough credit
         */
        public function has_sufficient_credit( $amount ) {
            return $this->total_balance >= $amount;
        }

        /**
         * retrieve the gift card code
         *
         * @return string
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function get_code() {
            return $this->gift_card_number;
        }

        /**
         * The gift card exists
         *
         * @return bool
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function exists() {
            return $this->ID > 0;
        }

        /**
         * Retrieve if a gift card is enabled
         *
         * @return bool
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function is_enabled() {

            return self::STATUS_ENABLED == $this->status;
        }

        /**
         * Check the gift card ownership
         *
         * @param int|string $user user id or user email
         *
         * @return bool
         */
        public function is_owner( $user ) {
            //todo perform a real check for gift card ownership
            return true;
        }

        /**
         * Check if the gift card can be used
         * @return bool
         */
        public function can_be_used() {
            $can_use = $this->exists ();

            return apply_filters ( 'yith_ywgc_gift_card_can_be_used', $can_use, $this );
        }

        /**
         * Save the current object
         */
        public function save() {

            // Create post object args
            $args = array(
                'post_title'  => $this->gift_card_number,
                'post_status' => $this->status,
                'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
                'post_parent' => $this->product_id,
            );

            if ( $this->ID == 0 ) {
                // Insert the post into the database
                $this->ID = wp_insert_post ( $args );

            } else {
                $args["ID"] = $this->ID;
                $this->ID   = wp_update_post ( $args );
            }

            $total_balance_rounded = round($this->total_balance, 2);
            $total_amount_rounded = round($this->total_amount, 2);

            //  Save Gift Card post_meta
            update_post_meta ( $this->ID, self::META_ORDER_ID, $this->order_id );
            update_post_meta ( $this->ID, self::META_CUSTOMER_ID, $this->customer_id );
            update_post_meta ( $this->ID, self::META_BALANCE_TOTAL, $total_balance_rounded );
            update_post_meta ( $this->ID, self::META_AMOUNT_TOTAL, $total_amount_rounded );


            $order_user_id = get_post_meta($this->order_id, '_customer_user', true);
            update_post_meta ( $this->ID, '_ywgc_sender_user_id', $order_user_id );


            return $this->ID;
        }



        /**
         * Update and store the new balance
         *
         * @param float $new_amount
         */
        public function update_balance( $new_amount ) {
            $this->total_balance = $new_amount;
            if ( $this->ID ) {
                update_post_meta ( $this->ID, self::META_BALANCE_TOTAL, $this->total_balance );
            }
        }

        /**
         * Update and store the new amount
         *
         * @param float $new_amount
         */
        public function update_amount( $new_amount ) {
            $this->total_amount = $new_amount;
            if ( $this->ID ) {
                update_post_meta ( $this->ID, self::META_AMOUNT_TOTAL, $this->total_amount );
            }
        }

        /**
         * Retrieve the current gift card balance
         * @return float|mixed
         */
        public function get_balance() {
            return round( $this->total_balance, wc_get_price_decimals() );
        }

        public function get_status_label() {
            return '';
        }

        /**
         * Clone the current gift card using the remaining balance as new amount
         *
         * @param string $new_code the code to be used for the new gift card
         *
         * @return YWGC_Gift_Card_Premium
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function clone_gift_card( $new_code = '' ) {

            $new_gift              = new YITH_YWGC_Gift_Card();
            $new_gift->product_id  = $this->product_id;
            $new_gift->customer_id = $this->customer_id;
            $new_gift->order_id    = $this->order_id;

            //  Set the amount of the cloned gift card equal to the balance of the old one
            $new_gift->total_amount     = $new_gift->total_balance;
            $new_gift->gift_card_number = $new_code;

            return $new_gift;
        }
    }
}
