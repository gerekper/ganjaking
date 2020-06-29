<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_YWGC_Gift_Cards_Table' ) ) {

    /**
     *
     * @class   YITH_YWGC_Gift_Cards_Table
     *
     * @since   1.0.0
     * @author  Lorenzo Giuffrida
     */
    class YITH_YWGC_Gift_Cards_Table {

        /* Gift card table columns */
        const COLUMN_ID_ORDER           = 'purchase_order';
        const COLUMN_ID_INFORMATION     = 'information';
        const COLUMN_ID_BALANCE         = 'balance';
        const COLUMN_ID_DEST_ORDERS     = 'dest_orders';
        const COLUMN_ID_ACTIONS         = 'gift_card_actions';
        const COLUMN_ID_INTERNAL_NOTES  = 'internal_notes';
        const COLUMN_ID_EXPIRATION_DATE = 'expiration_date';
        const COLUMN_ID_GIFT_CARD_LINK  = 'gift_card_link';
        const COLUMN_ID_ENABLE_DISABLE  = 'gift_card_enable_disable';

        /**
         * Single instance of the class
         *
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null ( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        protected function __construct() {


            // Add to admin_init function
            add_filter ( 'manage_edit-gift_card_columns', array( $this, 'add_custom_columns_title' ) );

            // Add to admin_init function
            add_action ( 'manage_gift_card_posts_custom_column', array( $this, 'show_custom_column_content' ), 10, 2 );


            add_filter( 'manage_posts_columns' , array( $this, 'remove_default_columns' ), 10, 2 );


            add_filter( 'yith_plugin_fw_wc_panel_screen_ids_for_assets' , array( $this, 'ywgc_include_framework_js_in_table_screen' ), 10, 1 );

        }

	    function ywgc_include_framework_js_in_table_screen( $screen_ids ) {

		    $screen_ids[] = 'edit-gift_card';

		    return $screen_ids;
	    }


        /**
         * remove default columns to custom post type table
         *
         * @param array $defaults current columns
         *
         * @return array new columns
         */
        function remove_default_columns( $columns, $post_type ) {

            if( $post_type == YWGC_CUSTOM_POST_TYPE_NAME ) {
	            unset( $columns['date'] );
	            unset( $columns['shortcode'] );
            }

            return $columns;
        }


        /**
         * Add custom columns to custom post type table
         *
         * @param array $defaults current columns
         *
         * @return array new columns
         */
        function add_custom_columns_title( $defaults ) {
            $columns = array_slice ( $defaults, 0, 2 );

            $columns[ self::COLUMN_ID_ORDER ]           = esc_html__( "Order", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_BALANCE ]         = esc_html__( "Balance", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_DEST_ORDERS ]     = esc_html__( "Orders", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_EXPIRATION_DATE ] = esc_html__( "Expiration date", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_INFORMATION ]     = esc_html__( "Information", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_ACTIONS ]         = '';
            $columns[ self::COLUMN_ID_INTERNAL_NOTES ]  = esc_html__( "Notes", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_GIFT_CARD_LINK ]  = esc_html__( "Direct link", 'yith-woocommerce-gift-cards' );
            $columns[ self::COLUMN_ID_ENABLE_DISABLE ]  = esc_html__( "Enable/Disable", 'yith-woocommerce-gift-cards' );

            $columns = apply_filters('yith_wcgc_custom_columns_title',$columns);
            return array_merge ( $columns, array_slice ( $defaults, 1 ) );
        }

        /**
         * @param WC_Order|int $order
         *
         * @return int
         */
        public function get_order_number_and_details( $order ) {

            if ( is_numeric ( $order ) ) {
                $order = wc_get_order ( $order );
            }

            if ( ! $order instanceof WC_Order ) {
                return '';
            }
            $order_id = yit_get_order_id ( $order );
            $customer = $order->get_user ();
            if ( $customer ) {
                $username = '<a href="user-edit.php?user_id=' . absint ( $customer->ID ) . '">';

                if ( $customer->first_name || $customer->last_name ) {
                    $username .= esc_html ( ucfirst ( $customer->first_name ) . ' ' . ucfirst ( $customer->last_name ) );
                } else {
                    $username .= esc_html ( ucfirst ( $customer->display_name ) );
                }

                $username .= '</a>';

            } else {
                $billing_first_name = $order->get_billing_first_name ();
                $billing_last_name  = $order->get_billing_last_name ();

                if ( $billing_first_name || $billing_last_name ) {
                    $username = trim ( $billing_first_name . ' ' . $billing_last_name );
                } else {
                    $username = esc_html__( 'Guest', 'yith-woocommerce-gift-cards' );
                }
            }

            return sprintf ( _x ( '%s by %s', 'Order number by X', 'yith-woocommerce-gift-cards' ),
                '<a href="' . admin_url ( 'post.php?post=' . absint ( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' .
                esc_attr ( $order->get_order_number () ) . '</strong></a>',
                $username );
        }


        /**
         * show content for custom columns
         *
         * @param $column_name string column shown
         * @param $post_ID     int     post to use
         */
        public function show_custom_column_content( $column_name, $post_ID ) {

            $gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $post_ID ) );

            if ( ! $gift_card->exists () ) {
                return;
            }

            switch ( $column_name ) {
                case self::COLUMN_ID_ORDER :

                    if ( $gift_card->order_id ) {
                        echo $this->get_order_number_and_details ( $gift_card->order_id );
                    } else {
                        echo apply_filters( 'yith_wcgc_table_created_manually_message', esc_html__( "Created manually", 'yith-woocommerce-gift-cards' ) );
                    }

                    break;

                case self::COLUMN_ID_BALANCE:

                    echo wc_price ( $gift_card->get_balance () );

                    break;

                case self::COLUMN_ID_DEST_ORDERS:
                    $orders = $gift_card->get_registered_orders ();
                    if ( $orders ) {
                        foreach ( $orders as $order_id ) {
                            echo $this->get_order_number_and_details ( $order_id );
                            echo "<br>";
                        }
                    }
                    if ( $gift_card->get_balance() == $gift_card->total_amount  ){
                        echo apply_filters( 'yith_wcgc_table_code_no_used_message', esc_html__( "The code has not been used yet", 'yith-woocommerce-gift-cards' ) );
                    }

                    break;

                case self::COLUMN_ID_INFORMATION:

                    $this->show_details_on_gift_cards_table ( $post_ID, $gift_card );

                    break;

                case self::COLUMN_ID_EXPIRATION_DATE:
                    $date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

                    $expiration_date = !is_numeric($gift_card->expiration) ? strtotime( $gift_card->expiration ) : $gift_card->expiration ;

                    if( $expiration_date ){
                        echo date_i18n ( $date_format, $expiration_date );
                    }else{
                        _e('Unlimited','yith-woocommerce-gift-cards');
                    }

                    break;

                case self::COLUMN_ID_ACTIONS:

                    $this->show_send_email_button ( $post_ID, $gift_card );
                    $this->show_download_pdf_button ( $post_ID, $gift_card );

                    break;

                case self::COLUMN_ID_INTERNAL_NOTES:

                    echo $gift_card->internal_notes;

                    break;

                case self::COLUMN_ID_GIFT_CARD_LINK:

                    $shop_page_url = apply_filters( 'yith_ywgc_shop_page_url', get_permalink ( wc_get_page_id ( 'shop' ) ) ? get_permalink ( wc_get_page_id ( 'shop' ) ) : site_url () );

                    $args = array(
                        YWGC_ACTION_ADD_DISCOUNT_TO_CART => $gift_card->gift_card_number,
                        YWGC_ACTION_VERIFY_CODE          => YITH_YWGC ()->hash_gift_card ( $gift_card ),
                    );

                    $direct_link = esc_url ( add_query_arg ( $args, $shop_page_url ) );
                    $copy_text   = esc_html__( "Copy link", 'yith-woocommerce-gift-cards' );
                    $copied_text   = esc_html__( "Link copied!", 'yith-woocommerce-gift-cards' );

                    echo '<p id="ywgc_direct_link" style="display: none">' . $direct_link . '</p>
						  <button type="button" id="ywgc_direct_link_button" class="button">' . $copy_text . '</button>
						  <p id="ywgc_copied_to_clipboard" style="display: none">' . $copied_text . '</p>
						  ';
                    break;


                case self::COLUMN_ID_ENABLE_DISABLE:

		                echo "<div class='yith-plugin-ui'>";
		                echo yith_plugin_fw_get_field( array(
			                'type'  => 'onoff',
			                'id'    => 'ywgc-toggle-enabled-' . $gift_card->ID,
			                'class' => 'ywgc-toggle-enabled',
			                'value' => $gift_card->is_enabled() ? 'yes' : 'no',
			                'data'  => array(
				                'gift-card-id' => $gift_card->ID,
			                )
		                ) );
		                echo "</div>";

	                break;

                    break;

                default:
                    echo apply_filters('yith_wcgc_column_default','',$post_ID, $column_name);
            }
        }

        /**
         * Download pdf
         *
         * @param $post_ID
         * @param $gift_card    object
         *
         * @return pdf download link
         * @author Daniel Sanchez <daniel.sanchez@yithemes.com>
         * @since 2.0.3
         */
        public function show_download_pdf_button( $post_ID, $gift_card ) {
            if ( $gift_card->is_enabled () && apply_filters('ywgc_show_download_pdf_button', true )) {

                $recipient = $gift_card->recipient;

                if ( ! empty( $recipient ) ) {

                    $download_pdf_link =sprintf ( '<a class="ywgc-actions %s" href="%s" title="%s"></a>',
                        'gift-cards download_pdf',
                        esc_url_raw ( add_query_arg ( array(
                            YWGC_ACTION_DOWNLOAD_PDF => 1,
                            'id'                      => $post_ID
                        ) ) ),
                        esc_html__( "Download pdf", 'yith-woocommerce-gift-cards' ) );

                    echo $download_pdf_link;
                }
            }
        }

        /**
         * @param                        $post_ID
         * @param YWGC_Gift_Card_Premium $gift_card
         */
        public function show_send_email_button( $post_ID, $gift_card ) {
            if ( $gift_card->is_enabled () ) {

                $recipient = $gift_card->recipient;

                if ( ! empty( $recipient ) ) {

                    $send_now_link = sprintf ( '<a class="ywgc-actions %s" href="%s" title="%s"></a>',
                        'gift-cards send-now',
                        esc_url_raw ( add_query_arg ( array(
                            YWGC_ACTION_RETRY_SENDING => 1,
                            'id'                      => $post_ID
                        ) ) ),
                        esc_html__( "Send now", 'yith-woocommerce-gift-cards' ) );

                    echo $send_now_link;
                }
            }
        }

        /**
         * @param int                    $post_ID
         * @param YWGC_Gift_Card_Premium $gift_card
         */
        public function show_details_on_gift_cards_table( $post_ID, $gift_card ) {

            if ( $gift_card->is_dismissed () ) {
                ?>
                <span
                        class="ywgc-dismissed-text"><?php _e ( "This card is dismissed.", 'yith-woocommerce-gift-cards' ); ?></span>
                <?php
            }

            if ( ! $gift_card->is_digital ) {
                ?>
                <div>
                    <span><?php echo esc_html__( "Physical product", 'yith-woocommerce-gift-cards' ); ?></span>
                </div>
                <?php
            } else {
                if ( $gift_card->delivery_send_date ){
                    $status_class = "sent";
                    $message      = sprintf ( esc_html__( "Sent on %s", 'yith-woocommerce-gift-cards' ), $gift_card->get_formatted_date( $gift_card->delivery_send_date ) );
                } else if ( $gift_card->delivery_date >= current_time ( 'timestamp' ) ) {
                    $status_class = "scheduled";
                    $message      = esc_html__( "Scheduled", 'yith-woocommerce-gift-cards' );
                } else if ( $gift_card->has_been_sent() == '' ) {
                    $status_class = "not-sent";
                    $message      = esc_html__( "Not yet sent", 'yith-woocommerce-gift-cards' );
                }else{
                    $status_class = "failed";
                    $message      = esc_html__( "Failed", 'yith-woocommerce-gift-cards' );
                }
                ?>

                <div>
                    <span><?php echo sprintf ( esc_html__( "Recipient: %s", 'yith-woocommerce-gift-cards' ), $gift_card->recipient ); ?></span>
                </div>

                <div>
                    <?php

                    if( $gift_card->delivery_date ): ?>
                        <span><?php echo sprintf ( esc_html__( "Delivery date: %s", 'yith-woocommerce-gift-cards' ), $gift_card->get_formatted_date( $gift_card->delivery_date ) ); ?></span>
                        <br>
                    <?php endif; ?>
                    <span
                            class="ywgc-delivery-status <?php echo $status_class; ?>"><?php echo $message; ?></span>

                </div>

                <?php
            }
        }
    }
}

YITH_YWGC_Gift_Cards_Table::get_instance ();
