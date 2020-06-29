<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of PREMIUM version of YWGC plugin
 *
 * @class   YITH_WooCommerce_Gift_Cards_Privacy
 * @package Yithemes
 * @since   1.8.5
 * @author  Daniel Sanchez Saez
 */
if ( ! class_exists( 'YITH_WooCommerce_Gift_Cards_Privacy' ) ) {

    class YITH_WooCommerce_Gift_Cards_Privacy extends YITH_Privacy_Plugin_Abstract
    {

        /**
         * Init - hook into events.
         */
        public function __construct()
        {

            /**
             * GDRP privacy policy content
             */
            parent::__construct( _x( 'YITH Gift Cards for WooCommerce premium', 'Privacy Policy Content', 'yith-woocommerce-gift-cards' ) );

            /**
             * GDRP WordPress hook to add exporters
             */

            add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'yith_wc_register_gift_cards_customer_data_exporter_received' ) );

            add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'yith_wc_register_gift_cards_customer_data_exporter_sent' ) );

            /**
             * GDRP WordPress hook to add erasers
             */

            add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'yith_wc_register_gift_cards_customer_data_eraser' ) );

            /**
             * GDRP to export order personal data
             */

            add_filter( 'woocommerce_privacy_export_order_personal_data_props', array( $this, 'woocommerce_privacy_export_order_personal_data_props_call_back' ), 10, 1 );

            add_filter( 'woocommerce_privacy_export_order_personal_data_prop', array( $this, 'woocommerce_privacy_export_order_personal_data_prop_call_back' ), 10, 3 );


            /**
             * GDRP to erase order personal data
             */

            add_filter( 'woocommerce_privacy_erase_order_personal_data', array( $this, 'woocommerce_privacy_erase_order_personal_data_call_back' ), 10, 2 );

        }

        /**
         * Add privacy policy content for the privacy policy page.
         *
         * @since 1.8.5
         */
        public function get_privacy_message( $section ) {

            $privacy_content_path = YITH_YWGC_VIEWS_PATH . '/privacy/html-policy-content-' . $section . '.php';

            if ( file_exists( $privacy_content_path ) ) {

                ob_start();

                include $privacy_content_path;

                return ob_get_clean();

            }

            return '';

        }

        /**
         * RECEIVED and SENT - Registers the personal data eraser for yith gift cards.
         *
         * @since 1.8.5
         *
         * @param  array $erasers An array of personal data erasers.
         * @return array $erasers An array of personal data erasers.
         */
        function yith_wc_register_gift_cards_customer_data_eraser( $erasers )
        {

            $erasers[ 'yith-wc-gift-cards-premium' ] = array(
                'eraser_friendly_name' => _x( 'YITH gift cards received and sent', 'GDPR privacy', 'yith-woocommerce-gift-cards' ),
                'callback' => array( $this, 'yith_wc_gift_cards_customer_data_eraser' ),
            );

            return $erasers;
        }

        /**
         * RECEIVED and SENT - Erases personal data associated with an email address from the posts table.
         *
         * @since 1.8.5
         *
         * @param  string $email_address The comment author email address.
         * @param  int $page Comment page.
         * @return array
         */
        function yith_wc_gift_cards_customer_data_eraser( $email_address, $page = 1 )
        {

            $user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

            $customer      = new WC_Customer( $user->ID );

            if ( ! $customer ) {
                return array();
            }

            $posts_per_page = 500;
            $page = (int) $page;
            $items_removed  = false;
            $items_retained = false;

            // ERASING GIFT CARDS RECEIVED

            $args = array(
                'post_type' => YWGC_CUSTOM_POST_TYPE_NAME,
                'paged' => $page,
                'posts_per_page' => $posts_per_page,
                'meta_query' => array(
                    array(
                        'key' => '_ywgc_recipient',
                        'value' => $email_address,
                        'compare' => '=',
                    ),
                ),
            );

            $gift_cards = get_posts( $args );

            $anon_author = _x( 'Anonymous', 'GDPR privacy', 'yith-woocommerce-gift-cards' );
            $messages    = array();

            foreach ( $gift_cards as $gift_card ) {

                /**
                 * Filters whether to anonymize the gift card.
                 *
                 * @since 1.8.5
                 *
                 * @param bool|string                    Whether to apply the gift cards anonymization (bool).
                 *                                       Custom prevention message (string). Default true.
                 * @param YITH_WooCommerce_Gift_Cards_Premium $gift_card             YITH_WooCommerce_Gift_Cards_Premium object.
                 */
                $anon_message = apply_filters( 'yith_wc_anonymize_gift_cards_received', true, $gift_card );

                if ( true !== $anon_message ) {
                    if ( $anon_message && is_string( $anon_message ) ) {
                        $messages[] = esc_html( $anon_message );
                    } else {
                        /* translators: %d: Comment ID */
                        $messages[] = sprintf( esc_html__( 'Gift card %d contains personal data but could not be anonymized.' ), $gift_card->ID );
                    }

                    $items_retained = true;

                    continue;
                }

                update_post_meta( $gift_card->ID, '_ywgc_customer_user', 0 );

                update_post_meta( $gift_card->ID, '_ywgc_recipient_name', $anon_author );

                update_post_meta( $gift_card->ID, '_ywgc_recipient', wp_privacy_anonymize_data( 'email', get_post_meta( $gift_card->ID, '_ywgc_recipient', true ) ) );

                /******* Posible future code in case to remove the recipient_name on the order ********

                $order = wc_get_order( $gift_card->order_id );

                foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

                    $gift_ids = wc_get_order_item_meta( $order_item_id, '_ywgc_gift_card_code' );

                    if ( ! in_array( $gift_card->get_title(), $gift_ids ) )
                        continue;

                    if ( wc_get_order_item_meta ( $order_item_id, '_ywgc_is_digital' ) ) {

                        wc_update_order_item_meta ( $order_item_id, '_ywgc_recipient_name', $anon_author );

                    }

                }
                 *************************************************************************************/

                $items_removed = true;

            }

            $done = count( $gift_cards ) < $posts_per_page;

            // ERASING GIFT CARDS SENT

            global $wpdb;

            $aux_page = $page - 1;
            $args = " SELECT ID, post_title, post_date
                    FROM $wpdb->posts
                    WHERE post_type = 'gift_card'
                        AND post_author = $user->ID LIMIT $posts_per_page OFFSET $aux_page";

            $gift_cards = $wpdb->get_results( $args );//LIMIT $posts_per_page OFFSET $page

            $anon_author = _x( 'Anonymous', 'GDPR privacy', 'yith-woocommerce-gift-cards' );
            $messages    = array();

            global $wpdb;

            foreach ( $gift_cards as $gift_card ) {

                /**
                 * Filters whether to anonymize the gift card.
                 *
                 * @since 1.8.5
                 *
                 * @param bool|string                    Whether to apply the gift cards anonymization (bool).
                 *                                       Custom prevention message (string). Default true.
                 * @param YITH_WooCommerce_Gift_Cards_Premium $gift_card             YITH_WooCommerce_Gift_Cards_Premium object.
                 */
                $anon_message = apply_filters( 'yith_wc_anonymize_gift_cards_sent', true, $gift_card );

                if ( true !== $anon_message ) {
                    if ( $anon_message && is_string( $anon_message ) ) {
                        $messages[] = esc_html( $anon_message );
                    } else {
                        /* translators: %d: Comment ID */
                        $messages[] = sprintf( esc_html__( 'Gift card %d contains personal data but could not be anonymized.' ), $gift_card->ID );
                    }

                    $items_retained = true;

                    continue;
                }

                $guid = wp_privacy_anonymize_data( 'url', $gift_card->guid );

                // Update post
                $result = $wpdb->query( $wpdb->prepare(
                    "
                    UPDATE $wpdb->posts
                    SET guid = '$guid', post_author = '0'
                    WHERE ID = %d
                    ", $gift_card->ID ) );

                if ( $result ) {

                    update_post_meta( $gift_card->ID, '_ywgc_customer_user', 0 );

                    update_post_meta( $gift_card->ID, '_ywgc_sender_name', $anon_author );

                    update_post_meta( $gift_card->ID, '_ywgc_recipient_name', $anon_author );

                    update_post_meta( $gift_card->ID, '_ywgc_recipient', wp_privacy_anonymize_data( 'email', get_post_meta( $gift_card->ID, '_ywgc_recipient', true ) ) );

                    $items_removed = true;

                } else {

                    $items_retained = true;
                }

            }

            if ( $done )
                $done = count( $gift_cards ) < $posts_per_page;

            return array(
                'items_removed'  => $items_removed,
                'items_retained' => $items_retained,
                'messages'       => $messages,
                'done'           => $done,
            );

        }

        /**
         * Registers the personal data exporter for yith gift cards. RECEIVED
         *
         * @since 1.8.5
         *
         * @param array $exporters An array of personal data exporters.
         * @return array $exporters An array of personal data exporters.
         */
        function yith_wc_register_gift_cards_customer_data_exporter_received( $exporters )
        {

            $exporters[ 'yith-wc-gift-cards-premium-received' ] = array(
                'exporter_friendly_name' => _x( 'YITH gift cards received', 'GDPR privacy', 'yith-woocommerce-gift-cards' ),
                'callback' => array( $this, 'yith_wc_gift_cards_customer_data_exporter_received' ),
            );

            return $exporters;
        }

        /**
         * Finds and exports personal data associated with an email address from the posts table. RECEIVED
         *
         * @since 1.8.5
         *
         * @param string $email_address The comment author email address.
         * @param int $page Comment page.
         * @return array $return An array of personal data.
         */
        function yith_wc_gift_cards_customer_data_exporter_received( $email_address, $page = 1 )
        {

            $user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

            $customer      = new WC_Customer( $user->ID );

            if ( ! $customer ) {
                return array();
            }

            $data_to_export = array();

            $posts_per_page = 500;
            $page = (int) $page;

            $args = array(
                'post_type' => YWGC_CUSTOM_POST_TYPE_NAME,
                'paged' => $page,
                'posts_per_page' => $posts_per_page,
                'meta_query' => array(
                    array(
                        'key' => '_ywgc_recipient',
                        'value' => $email_address,
                        'compare' => '=',
                    ),
                ),
            );

            $gift_cards = get_posts( $args );

            foreach ( $gift_cards as $gift_card ) {

                $comment_data_to_export = $this->yith_wc_gift_cards_create_comment_data_to_export( $gift_card );

                $data_to_export[] = array(
                    'group_id' => 'yith_wc_gift_cards_received',
                    'group_label' => _x( 'Gift cards received', 'GDPR privacy', 'yith-woocommerce-gift-cards' ),
                    'item_id' => "yith_gift_card_sent-" . $gift_card->ID,
                    'data' => $comment_data_to_export,
                );

            }

            $done = count( $gift_cards ) < $posts_per_page;

            return array(
                'data' => $data_to_export,
                'done' => $done,
            );

        }

        /**
         * Registers the personal data exporter for yith gift cards. RECEIVED
         *
         * @since 1.8.5
         *
         * @param array $exporters An array of personal data exporters.
         * @return array $exporters An array of personal data exporters.
         */
        function yith_wc_register_gift_cards_customer_data_exporter_sent( $exporters )
        {

            $exporters[ 'yith-wc-gift-cards-sent-premium' ] = array(
                'exporter_friendly_name' => _x( 'YITH gift cards sent', 'GDPR privacy', 'yith-woocommerce-gift-cards' ),
                'callback' => array( $this, 'yith_wc_gift_cards_customer_data_exporter_sent' ),
            );

            return $exporters;
        }

        /**
         * Finds and exports personal data associated with an email address from the posts table. RECEIVED
         *
         * @since 1.8.5
         *
         * @param string $email_address The comment author email address.
         * @param int $page Comment page.
         * @return array $return An array of personal data.
         */
        function yith_wc_gift_cards_customer_data_exporter_sent( $email_address, $page = 1 )
        {

            $user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

            $customer      = new WC_Customer( $user->ID );

            if ( ! $customer ) {
                return array();
            }

            $data_to_export = array();

            $posts_per_page = 500;
            $page = (int) $page;

            global $wpdb;

            $aux_page = $page - 1;
            $args = " SELECT ID, post_title, post_date
                    FROM $wpdb->posts
                    WHERE post_type = 'gift_card'
                        AND post_author = $user->ID LIMIT $posts_per_page OFFSET $aux_page";

            $gift_cards = $wpdb->get_results( $args );//LIMIT $posts_per_page OFFSET $page

            foreach ( $gift_cards as $gift_card ) {

                $comment_data_to_export = $this->yith_wc_gift_cards_create_comment_data_to_export( $gift_card );

                $data_to_export[] = array(
                    'group_id' => 'yith_wc_gift_cards_sent',
                    'group_label' => _x( 'Gift cards sent', 'GDPR privacy', 'yith-woocommerce-gift-cards' ),
                    'item_id' => "yith_gift_card_sent-" . $gift_card->ID,
                    'data' => $comment_data_to_export,
                );

            }

            $done = count( $gift_cards ) < $posts_per_page;

            return array(
                'data' => $data_to_export,
                'done' => $done,
            );

        }

        function yith_wc_gift_cards_create_comment_data_to_export( $gift_card )
        {

            $comment_data_to_export = array();

            $comment_data_to_export[] = array(
                'name' => 'Created',
                'value' => $gift_card->post_date,
            );

            $comment_data_to_export[] = array(
                'name' => 'Code',
                'value' => $gift_card->post_title,
            );

            $comment_data_to_export[] = array(
                'name' => 'Total balance',
                'value' => get_post_meta( $gift_card->ID, '_ywgc_balance_total', true ),
            );

            $comment_data_to_export[] = array(
                'name' => 'Total amount',
                'value' => get_post_meta( $gift_card->ID, '_ywgc_amount_total', true ),
            );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_sender_name', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Sender name',
                    'value' => $aux,
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_recipient_name', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Recipient name',
                    'value' => $aux,
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_recipient', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Recipient email',
                    'value' => $aux,
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_message', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Message',
                    'value' => $aux,
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_postdated', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Delivery date',
                    'value' => get_post_meta( $gift_card->ID, '_ywgc_delivery_date', true ),
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_expiration', true );
            if ( $aux != 0 )
                $comment_data_to_export[] = array(
                    'name' => 'Expiration date',
                    'value' => $aux,
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_is_digital', true );
            if ( $aux == 1 )
                $comment_data_to_export[] = array(
                    'name' => 'Type',
                    'value' => 'digital',
                );
            else
                $comment_data_to_export[] = array(
                    'name' => 'Type',
                    'value' => 'physical',
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_delivery_send_date', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Delivery sent',
                    'value' => $aux,
                );

            $aux = get_post_meta( $gift_card->ID, '_ywgc_internal_notes', true );
            if ( $aux != '' )
                $comment_data_to_export[] = array(
                    'name' => 'Internal_notes',
                    'value' => $aux,
                );

            return $comment_data_to_export;

        }

        /**
         * GDPR erase order_metas to the filter hook of WooCommerce to erase personal order data associated with an email address.
         *
         * @since 1.8.5
         *
         * @param  boolean $erasure_enabled.
         * @param  object $order.
         * @return boolean
         */
        function woocommerce_privacy_erase_order_personal_data_call_back( $erasure_enabled, $order )
        {

            if ( $erasure_enabled ){

                $anon_author = _x( 'Anonymous', 'GDPR privacy', 'yith-woocommerce-gift-cards' );

                foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

                    $gift_ids = wc_get_order_item_meta( $order_item_id, '_ywgc_gift_card_code' );

                    if ( !$gift_ids )
                        continue;

                    $is_digital       = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_digital' );

                    if ( $is_digital ) {

                        wc_update_order_item_meta ( $order_item_id, '_ywgc_recipient_name', $anon_author );

                        wc_update_order_item_meta ( $order_item_id, '_ywgc_sender_name', $anon_author );

                        wc_update_order_item_meta ( $order_item_id, '_ywgc_message', wp_privacy_anonymize_data( 'longtext', wc_get_order_item_meta ( $order_item_id, '_ywgc_message' ), true ) );


                    }

                }

            }

            return $erasure_enabled;

        }

        /**
         * GDPR add order_meta to the filter hook of WooCommerce to export personal order data associated with an email address.
         *
         * @since 1.8.5
         *
         * @param  array $array_meta_to_export meta_orders.
         * @return array
         */
        function woocommerce_privacy_export_order_personal_data_props_call_back( $array_meta_to_export )
        {

            $array_meta_to_export[ '_ywgc_recipients' ] = esc_html__( 'Gift Card', 'yith-woocommerce-gift-cards' );

            return $array_meta_to_export;

        }

        /**
         * GDPR retrieve the value order_meta to add to the filer hook of WooCommerce to export personal order data associated with an email address.
         *
         * @since 1.8.5
         *
         * @param  string $value value of meta_order.
         * @param  string $prop meta_order
         * @param  object $order
         * @return string
         */
        function woocommerce_privacy_export_order_personal_data_prop_call_back( $value, $prop, $order )
        {

            $array_props = array(
                '_ywgc_recipients',
            );

            if ( in_array( $prop, $array_props ) ){

                foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

                    $gift_ids      = wc_get_order_item_meta( $order_item_id, '_ywgc_gift_card_code' );

                    if ( ! $gift_ids )
                        continue;


                    $value .= "CODE: ";
                    foreach ( $gift_ids as $gift_id_code )
                        $value .= $gift_id_code . " ";
                    $value .= "<br>";

                    $is_digital       = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_digital' );

                    if ( $is_digital ) {

                        $value .= "Type: digital <br>";

                        $aux       = wc_get_order_item_meta ( $order_item_id, '_ywgc_recipients' );
                        $value .= "Recipient email: " . $aux[0] . "<br>";

                        $aux    = wc_get_order_item_meta ( $order_item_id, '_ywgc_recipient_name' );
                        $value .= "Recipient name: " . $aux . "<br>";

                        $aux            = wc_get_order_item_meta ( $order_item_id, '_ywgc_sender_name' );
                        $value .= "Sender name: " . $aux . "<br>";

                        $aux           = wc_get_order_item_meta ( $order_item_id, '_ywgc_message' );
                        $value .= "Message: " . $aux . "<br>";

                        if ( wc_get_order_item_meta ( $order_item_id, '_ywgc_postdated' ) )
                            $value .= "Delivery date -> " . wc_get_order_item_meta ( $order_item_id, '_ywgc_delivery_date' ) . "<br>";

                    }else
                        $value .= "Type: physical <br>";

                    $aux           = wc_get_order_item_meta ( $order_item_id, '_ywgc_amount' );
                    $value .= "Amount: " . $aux . "<br>";

                    $aux           = wc_get_order_item_meta ( $order_item_id, '_ywgc_subtotal' );
                    $value .= "Subtotal: " . $aux . "<br>";

                    $aux           = wc_get_order_item_meta ( $order_item_id, '_ywgc_subtotal_tax' );
                    $value .= "Subtotal: " . $aux . "<br>";

                    $value .= "<br>";

                }

            }

            return $value;

        }

    }

}

new YITH_WooCommerce_Gift_Cards_Privacy();
