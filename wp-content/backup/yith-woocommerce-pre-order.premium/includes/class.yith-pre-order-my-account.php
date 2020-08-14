<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_My_Account
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_My_Account' ) ) {
    /**
     * Class YITH_Pre_Order_My_Account
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Pre_Order_My_Account {

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        public function __construct() {
            add_action( 'woocommerce_my_account_my_orders_column_order-status', array( $this, 'add_pre_order_button_on_orders_page' ) );
            add_action( 'woocommerce_order_item_meta_start', array( $this, 'add_pre_order_info_on_single_order_page' ), 10, 3 );
            add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
            add_action( 'woocommerce_account_my-pre-orders_endpoint', array( $this, 'endpoint_content' ) );
            add_filter( 'the_title', array( $this, 'endpoint_title' ) );

            if ( version_compare( WC()->version, '2.6', '>=' ) ) {
                add_action( 'template_redirect', array( $this, 'load_account_template' ) );
            } else {
                add_action( 'woocommerce_before_my_account', array( $this, 'show_pre_order_section' ) );
            }
        }

        public function show_pre_order_section(  ) {
            if ( $this->the_user_has_pre_orders() ) {
                echo '<h2>' . esc_html__( 'My Pre-Orders', 'yith-pre-order-for-woocommerce' ) . '</h2>';
                $this->endpoint_content();
            }
        }

        public function load_account_template() {
            global $wp, $post;

            if( ! is_page( wc_get_page_id( 'myaccount' ) ) || ! isset( $wp->query_vars['my-pre-orders'] ) ) {
                return;
            }

	        // extract from post content the my-account shortcode
	        preg_match( '/\[woocommerce_my_account[^\]]*\]/', $post->post_content, $shortcode );
	        // get content
	        $shortcode = isset( $shortcode[0] ) ? $shortcode[0] : false;

	        if( ! $shortcode ) {
		        return;
	        }

            ob_start();

            echo '<div class="woocommerce">';

            wc_print_notices();

            do_action( 'woocommerce_account_navigation' );
            echo '<div class="woocommerce-MyAccount-content">';
            $this->endpoint_content();
            echo '</div>';

            echo '</div>';

            $content = ob_get_clean();

            $post->post_title = esc_html__( 'My Pre-order List', 'yith-pre-order-for-woocommerce' );
            $post->post_content = $content;
        }


        public function add_pre_order_button_on_orders_page( $order ) {
            $order = wc_get_order( $order );
            $order_has_preorder = yit_get_prop( $order, '_order_has_preorder', true );
            if ( 'yes' == $order_has_preorder ) {
                echo wc_get_order_status_name( $order->get_status() );
                echo '<br><mark>' . esc_html( esc_html__( 'Has Pre-Orders', 'yith-pre-order-for-woocommerce' ) ) . '</mark>';
            } else {
                echo wc_get_order_status_name( $order->get_status() );
            }
        }

        public function add_pre_order_info_on_single_order_page( $item_id, $item, $order ) {
        	$is_pre_order = ! empty( $item['ywpo_item_preorder'] ) ? $item['ywpo_item_preorder'] : '';
            if ( 'yes' == $is_pre_order ) {
                echo '<div>' . apply_filters( 'yith_ywpo_pre_order_product_label_single_order_page', esc_html__( 'Pre-Order product', 'yith-pre-order-for-woocommerce' ), $item, $item_id, $order ) . '</div>';
            }

        }

        public static function all_order_ids_from_current_user() {
	        $all_customer_order_ids = false;
        	if ( get_current_user_id() ) {
		        $args = array(
			        'post_type'   => wc_get_order_types(),
			        'post_status' => array_keys( wc_get_order_statuses() ),
			        'numberposts' => - 1,
			        'fields'      => 'ids',
			        'meta_key'    => '_customer_user',
			        'meta_value'  => get_current_user_id()
		        );
		        $all_customer_order_ids = get_posts( $args );
	        }
	        return $all_customer_order_ids;
        }


        /**
         * Checks if the user has any Pre-Order purchased.
         *
         * @return bool
         */
        public function the_user_has_pre_orders() {
	        $the_user_has_pre_orders = false;
	        $all_customer_order_ids = self::all_order_ids_from_current_user();
            if ( $all_customer_order_ids ) {
                foreach ( $all_customer_order_ids as $order_id ) {
                    $order = wc_get_order( $order_id );
                    foreach ( $order->get_items() as $item_id => $item ) {
	                    $item_is_pre_order = ! empty( $item['ywpo_item_preorder'] ) ? $item['ywpo_item_preorder'] : '';
                        $timestamp = ! empty( $item['ywpo_item_for_sale_date'] ) ? $item['ywpo_item_for_sale_date'] : '';
                        if ( 'yes' == $item_is_pre_order && ( $timestamp > time() || empty( $timestamp ) ) ) {
                            $the_user_has_pre_orders = true;
                        }
                    }
                }
            }
            return $the_user_has_pre_orders;
        }

        public function endpoint_content() {
            if ( $this->the_user_has_pre_orders() ) {
	            $all_customer_order_ids = self::all_order_ids_from_current_user();

                wc_get_template( 'my-account-my-pre-orders.php',
                    array( 'all_customer_order_ids' => $all_customer_order_ids ),
                    '',
                    YITH_WCPO_WC_TEMPLATE_PATH . 'frontend/' );
            }
        }

        public function new_menu_items( $items ) {
            if ( $this->the_user_has_pre_orders() ) {

                // Remove the logout menu item.
                $logout = $items['customer-logout'];
                unset( $items['customer-logout'] );

                // Insert your custom endpoint.
                $items['my-pre-orders'] =__( 'My Pre-Orders', 'yith-pre-order-for-woocommerce' );

                // Insert back the logout item.
                $items['customer-logout'] = $logout;
            }
            return $items;
        }

        /**
         * Set endpoint title.
         *
         * @param string $title
         * @return string
         */
        public function endpoint_title( $title ) {
            global $wp_query;

            $is_endpoint = isset( $wp_query->query_vars['my-pre-orders'] );

            if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
                // New page title.
                $title = esc_html__( 'My Pre-Orders', 'yith-pre-order-for-woocommerce' );

                remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
            }

            return $title;
        }

        /**
         * Plugin install action.
         * Flush rewrite rules to make our custom endpoint available.
         */
        public static function install() {
            flush_rewrite_rules();
        }


    }

}