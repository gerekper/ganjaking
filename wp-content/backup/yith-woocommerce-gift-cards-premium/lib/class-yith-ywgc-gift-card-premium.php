<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_Gift_Card_Premium' ) ) {
    /**
     *
     * @class   YWGC_Gift_Card_Premium
     *
     * @since   1.0.0
     * @author  Lorenzo Giuffrida
     */
    class YWGC_Gift_Card_Premium extends YITH_YWGC_Gift_Card {

        const META_SENDER_NAME = '_ywgc_sender_name';
        const META_RECIPIENT_NAME = '_ywgc_recipient_name';
        const META_RECIPIENT_EMAIL = '_ywgc_recipient';
        const META_MESSAGE = '_ywgc_message';
        const META_CURRENCY = '_ywgc_currency';
        const META_VERSION = '_ywgc_version';
        const META_IS_POSTDATED = '_ywgc_postdated';
        const META_DELIVERY_DATE = '_ywgc_delivery_date';
        const META_SEND_DATE = '_ywgc_delivery_send_date';
        const META_AS_PRESENT = '_ywgc_product_as_present';
        const META_AS_PRESENT_VARIATION_ID = '_ywgc_present_variation_id';
        const META_AS_PRESENT_PRODUCT_ID = '_ywgc_present_product_id';
        const META_MANUAL_AMOUNT = '_ywgc_is_manual_amount';
        const META_IS_DIGITAL = '_ywgc_is_digital';
        const META_HAS_CUSTOM_DESIGN = '_ywgc_has_custom_design';
        const META_DESIGN_TYPE = '_ywgc_design_type';
        const META_DESIGN = '_ywgc_design';
        const META_EXPIRATION = '_ywgc_expiration';
        const META_INTERNAL_NOTES = '_ywgc_internal_notes';

        const STATUS_PRE_PRINTED = 'ywgc-pre-printed';
        const STATUS_DISABLED = 'ywgc-disabled';
        const STATUS_CODE_NOT_VALID = 'ywgc-code-not-valid';

        /**
         * @var bool the gift card has a postdated delivery date
         */
        public $postdated_delivery = false;

        /**
         * @var string the expected delivery date
         */
        public $delivery_date = '';

        /**
         * @var string the real delivery date
         */
        public $delivery_send_date = '';


        /**
         * @var string the sender for digital gift cards
         */
        public $sender_name = '';

        /**
         * @var string the sender for digital gift cards
         */
        public $recipient_name = '';

        /**
         * @var string the message for digital gift cards
         */
        public $message = '';

        /**
         * @var bool the digital gift cards use the default image
         */
        public $has_custom_design = true;

        /**
         * @var string the type of design chosen by the user. Could be :
         *             'default' for standard image
         *             'custom' for image uploaded by the user
         *             'template' for template chosen from the desing list
         */
        public $design_type = 'default';

        /**
         * @var string the custom image for digital gift cards
         */
        public $design = null;

        /**
         * @var bool the product is set as a present
         */
        public $product_as_present = false;

        /**
         * @var int the product variation id when the product is used as a present
         */
        public $present_variation_id = 0;

        /**
         * @var int the product id used as a present
         */
        public $present_product_id = 0;

        /**
         * @var string the currency used when the gift card is created
         */
        public $currency = '';

        /**
         * Plugin version that created the gift card
         */
        public $version = '';

        /**
         * @var bool the gift card is digital
         */
        public $is_digital = false;

        /**
         * @var bool the gift card amount was entered manually
         */
        public $is_manual_amount = false;

        /**
         * @var int the timestamp for gift card valid use
         */
        public $expiration = 0;

        /**
         * @var string internal note
         */
        public $internal_notes = '';

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @param $args int|array|WP_Post
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        public function __construct( $args = array() ) {

            parent::__construct( $args );

            //  If $args is related to an existent gift card, load their data
            if ( $this->ID ) {
                $this->sender_name          = get_post_meta( $this->ID, self::META_SENDER_NAME, true );
                $this->recipient_name       = get_post_meta( $this->ID, self::META_RECIPIENT_NAME, true );
                $this->recipient            = get_post_meta( $this->ID, self::META_RECIPIENT_EMAIL, true );
                $this->message              = get_post_meta( $this->ID, self::META_MESSAGE, true );
                $this->currency             = get_post_meta( $this->ID, self::META_CURRENCY, true );
                $this->version              = get_post_meta( $this->ID, self::META_VERSION, true );
                $this->postdated_delivery   = get_post_meta( $this->ID, self::META_IS_POSTDATED, true );
                $this->delivery_date        = get_post_meta( $this->ID, self::META_DELIVERY_DATE, true );
                $this->delivery_send_date   = get_post_meta( $this->ID, self::META_SEND_DATE, true );
                $this->product_as_present   = get_post_meta( $this->ID, self::META_AS_PRESENT, true );
                $this->present_variation_id = get_post_meta( $this->ID, self::META_AS_PRESENT_VARIATION_ID, true );
                $this->present_product_id   = get_post_meta( $this->ID, self::META_AS_PRESENT_PRODUCT_ID, true );
                $this->is_manual_amount     = get_post_meta( $this->ID, self::META_MANUAL_AMOUNT, true );
                $this->is_digital           = get_post_meta( $this->ID, self::META_IS_DIGITAL, true );
                $this->has_custom_design    = get_post_meta( $this->ID, self::META_HAS_CUSTOM_DESIGN, true );
                $this->design_type          = get_post_meta( $this->ID, self::META_DESIGN_TYPE, true );
                $this->design               = get_post_meta( $this->ID, self::META_DESIGN, true );
                $this->expiration           = get_post_meta( $this->ID, self::META_EXPIRATION, true );
                $this->internal_notes       = get_post_meta( $this->ID, self::META_INTERNAL_NOTES, true );
            }

        }

        /**
         * The gift card product is virtual
         */
        public function is_virtual() {

            return $this->is_digital;
        }


        public function get_gift_card_error( $err_code ) {
            $err = '';

            switch ( $err_code ) {
                case self::E_GIFT_CARD_NOT_EXIST:
					$err = sprintf( esc_html__( 'The gift card code %s does not exist!', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::E_GIFT_CARD_NOT_YOURS:
                    $err = sprintf( esc_html__( 'Sorry, it seems that the gift card code "%s" is not yours and cannot be used for this order.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::E_GIFT_CARD_ALREADY_APPLIED:
                    $err = sprintf( esc_html__( 'The gift card code %s has already been applied!', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::E_GIFT_CARD_EXPIRED:
                    $err = sprintf( esc_html__( 'Sorry, the gift card code %s is expired and cannot be used.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::E_GIFT_CARD_DISABLED:
                    $err = sprintf( esc_html__( 'Sorry, the gift card code %s is currently disabled and cannot be used.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::E_GIFT_CARD_DISMISSED:
                    $err = sprintf( esc_html__( 'Sorry, the gift card code %s is no longer valid!', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::E_GIFT_CARD_INVALID_REMOVED:
                    $err = sprintf( esc_html__( 'Sorry, it seems that the gift card code %s is invalid - it has been removed from your cart.', 'yith-woocommerce-gift-cards' ), $this->gift_card_number );
                    break;
                case self::GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD:
                    $err = esc_html__( 'Gift card codes cannot be used to purchase other gift cards', 'yith-woocommerce-gift-cards' );
                    break;

            }

            return apply_filters( 'yith_ywgc_get_gift_card_error', $err, $err_code, $this );
        }

        /**
         * Retrieve a message for a successful gift card status
         *
         * @param string $err_code
         *
         * @return string
         */
        public function get_gift_card_message( $err_code ) {

            $err = '';

            switch ( $err_code ) {

                case self::GIFT_CARD_SUCCESS:
                    $err = esc_html__( 'Gift card code successfully applied.', 'yith-woocommerce-gift-cards' );
                    break;
                case self::GIFT_CARD_REMOVED:
                    $err = esc_html__( 'Gift card code successfully removed.', 'yith-woocommerce-gift-cards' );
                    break;

                case self::GIFT_CARD_NOT_ALLOWED_FOR_PURCHASING_GIFT_CARD:
                    $err = esc_html__( 'Gift card codes cannot be used to purchase other gift cards', 'yith-woocommerce-gift-cards' );
                    break;
            }

            return apply_filters( 'yith_ywgc_get_gift_card_message', $err, $err_code, $this );
        }

        /**
         * Check if the gift card has been sent
         */
        public function has_been_sent() {
            return $this->delivery_send_date;
        }

        /**
         * Set the gift card as sent
         */
        public function set_as_sent() {
            $this->delivery_send_date = current_time( 'timestamp' );
            update_post_meta( $this->ID, self::META_SEND_DATE, $this->delivery_send_date );
        }

        public function set_as_code_not_valid() {
            $this->gift_card_number = 'NOT VALID';
            $this->set_status( self::STATUS_CODE_NOT_VALID );

        }

        /**
         * Set the gift card as pre-printed i.e. the code is manually entered instead of being auto generated
         */
        public function set_as_pre_printed() {
            $this->set_status( self::STATUS_PRE_PRINTED );
        }

        /**
         * Check if the gift card is pre-printed
         */
        public function is_pre_printed() {

            return self::STATUS_PRE_PRINTED == $this->status;
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
         * Check if the gift card is expired
         */
        public function is_expired() {

            if ( ! $this->expiration ) {
                return false;
            }

            return time() > $this->expiration;
        }

        /**
         * Retrieve if a gift card is disabled
         *
         * @return bool
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function is_disabled() {

            return self::STATUS_DISABLED == $this->status;
        }

        /**
         * Check if the gift card can be used
         * @return bool
         */
        public function can_be_used() {
            $can_use = $this->exists() &&
                $this->is_enabled() &&
                ! $this->is_expired();


            return apply_filters( 'yith_ywgc_gift_card_can_be_used', $can_use, $this );
        }

        /**
         * Set the gift card enabled status
         *
         * @param bool|false $enabled
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function set_enabled_status( $enabled = false ) {

            $current_status = $this->is_enabled();

            if ( $current_status == $enabled ) {
                return;
            }

            //  If the gift card is dismissed, stop now
            if ( $this->is_dismissed() ) {
                return;
            }

            $this->set_status( $enabled ? 'publish' : self::STATUS_DISABLED );
        }

        /**
         * Set the gift card status
         *
         * @param string $status
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function set_status( $status ) {

            $this->status = $status;

            if ( $this->ID ) {
                $args = array(
                    'ID'          => $this->ID,
                    'post_status' => $status,
                );

                wp_update_post( $args );
            }
        }

        /**
         * Retrieve all scheduled gift cards to be sent on a specific day or up to the specific day if $include_old is true
         *
         * @param string $send_date the gift card scheduled day
         * @param string $relation  the conditional relation for gift cards date specified
         *
         * @return array
         */
        public static function get_postdated_gift_cards( $send_date, $relation = '<=' ) {

            $args = array(
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => self::META_DELIVERY_DATE,
                        'value'   => $send_date,
                        'compare' => $relation,
                    ),
	                array(
		                'key'     => self::META_DELIVERY_DATE,
		                'value'   => '',
		                'compare' => '!=',
	                ),
                    array(
                        'key'   => self::META_SEND_DATE,
                        'value' => '',
                    ),
	                array(
		                'key'   => self::META_IS_DIGITAL,
		                'value' => '1',
		                'compare' => '=',
	                ),
	                array(
		                'key'     => self::META_RECIPIENT_EMAIL,
		                'value'   => '',
		                'compare' => '!=',
	        ),
                ),

                'post_type'      => YWGC_CUSTOM_POST_TYPE_NAME,
                'fields'         => 'ids',
                'post_status'    => 'publish',
                'posts_per_page' => - 1,
            );

            return get_posts( $args );
        }

        /**
         * Save the current object
         */
        public function save() {
            parent::save();

            $date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

            update_post_meta( $this->ID, self::META_SENDER_NAME, $this->sender_name );
            update_post_meta( $this->ID, self::META_RECIPIENT_NAME, $this->recipient_name );
            update_post_meta( $this->ID, self::META_RECIPIENT_EMAIL, $this->recipient );
            update_post_meta( $this->ID, self::META_MESSAGE, str_replace( '\\','',$this->message ) );
            update_post_meta( $this->ID, self::META_CURRENCY, $this->currency );
            update_post_meta( $this->ID, self::META_VERSION, $this->version );
            update_post_meta( $this->ID, self::META_IS_POSTDATED, $this->postdated_delivery );

            if ( $this->postdated_delivery ) {

                update_post_meta( $this->ID, self::META_DELIVERY_DATE, $this->delivery_date );

                //Update also the delivery date with format
                $delivery_date_format = date_i18n ( $date_format, $this->delivery_date );
                update_post_meta( $this->ID, '_ywgc_delivery_date_formatted', $delivery_date_format );

                update_post_meta( $this->ID, self::META_SEND_DATE, $this->delivery_send_date );
            }
            else{
                $delivery_date_format = date_i18n ( $date_format, time() );
                update_post_meta( $this->ID, '_ywgc_delivery_date_formatted', $delivery_date_format );
            }

            update_post_meta( $this->ID, self::META_HAS_CUSTOM_DESIGN, $this->has_custom_design );

            $expiration_in_timestamp = $this->expiration;

            $expiration_date_format = $this->expiration != '0' ? date_i18n ( $date_format, $this->expiration ) : '';

            update_post_meta( $this->ID, self::META_EXPIRATION,  $expiration_in_timestamp );
            update_post_meta( $this->ID, '_ywgc_expiration_date_formatted', $expiration_date_format );


            update_post_meta( $this->ID, self::META_DESIGN_TYPE, $this->design_type );
            update_post_meta( $this->ID, self::META_DESIGN, $this->design );

            update_post_meta( $this->ID, self::META_AS_PRESENT, $this->product_as_present );
            if ( $this->product_as_present ) {
                update_post_meta( $this->ID, self::META_AS_PRESENT_PRODUCT_ID, $this->present_product_id );
                update_post_meta( $this->ID, self::META_AS_PRESENT_VARIATION_ID, $this->present_variation_id );
            }

            update_post_meta( $this->ID, self::META_MANUAL_AMOUNT, $this->is_manual_amount );
            update_post_meta( $this->ID, self::META_IS_DIGITAL, $this->is_digital );
            update_post_meta( $this->ID, self::META_INTERNAL_NOTES, $this->internal_notes );

        }

        /**
         * The gift card is nulled and no more usable
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function set_dismissed_status() {
            $this->set_status( self::STATUS_DISMISSED );
        }

        /**
         * The gift card code is duplicate and the gift card is not usable until a new, valid, code is set
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function set_duplicated_status() {
            $this->set_status( self::STATUS_DISMISSED );
        }

        /**
         * Check if the gift card is dismissed
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function is_dismissed() {

            return self::STATUS_DISMISSED == $this->status;
        }


        /**
         * Retrieve the status label for every gift card status
         *
         * @return string
         */
        public function get_status_label() {
            $label = '';

            switch ( $this->status ) {
                case self::STATUS_DISABLED:
                    $label = esc_html__( "The gift card has been disabled", 'yith-woocommerce-gift-cards' );
                    break;
                case self::STATUS_ENABLED:
                    $label = esc_html__( "Valid", 'yith-woocommerce-gift-cards' );
                    break;
                case self::STATUS_DISMISSED:
                    $label = esc_html__( "No longer valid, replaced by another code", 'yith-woocommerce-gift-cards' );
                    break;
            }

            return $label;
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

            $new_gift = new YWGC_Gift_Card_Premium();

            $new_gift->product_id           = $this->product_id;
            $new_gift->order_id             = $this->order_id;
            $new_gift->sender_name          = $this->sender_name;
            $new_gift->recipient_name       = $this->recipient_name;
            $new_gift->recipient            = $this->recipient;
            $new_gift->message              = $this->message;
            $new_gift->postdated_delivery   = $this->postdated_delivery;
            $new_gift->delivery_date        = $this->delivery_date;
            $new_gift->delivery_send_date   = $this->delivery_send_date;
            $new_gift->has_custom_design    = $this->has_custom_design;
            $new_gift->expiration           = $this->expiration;
            $new_gift->design_type          = $this->design_type;
            $new_gift->design               = $this->design;
            $new_gift->product_as_present   = $this->product_as_present;
            $new_gift->present_variation_id = $this->present_variation_id;
            $new_gift->present_product_id   = $this->present_product_id;
            $new_gift->currency             = $this->currency;
            $new_gift->status               = $this->status;

            $new_gift->gift_card_number = $new_code;

            //  Set the amount of the cloned gift card equal to the balance of the old one
            $new_gift->total_amount = $this->get_balance();
            $new_gift->update_balance( $new_gift->total_amount );

            return $new_gift;
        }


        public function get_formatted_date( $date ){

            $date_format = apply_filters( 'yith_wcgc_date_format','Y-m-d' );

            $date = !is_numeric($date) ? strtotime( $date ) : $date ;

            $formatted_date = date_i18n( $date_format, $date );
            return $formatted_date;
        }
    }
}
