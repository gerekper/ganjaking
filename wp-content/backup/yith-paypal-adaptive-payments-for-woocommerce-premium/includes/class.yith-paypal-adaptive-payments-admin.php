<?php
if( !defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'YITH_Paypal_Adaptive_Payments_Admin' ) ) {

    class YITH_Paypal_Adaptive_Payments_Admin
    {

        protected static $instance;

        public function __construct()
        {

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );

            //if YITH Multivendor isn't activated
            if( !YITH_PayPal_Adaptive_Payments_Integrations::is_multivendor_active()  ) {
                //ADMIN PRODUCT
                add_filter( 'woocommerce_product_data_tabs', array( $this, 'write_paypal_adaptive_payments_tabs' ), 98 );
                add_action( 'woocommerce_product_data_panels', array( $this, 'write_product_paypal_adptive_payments_data' ) );
                add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_meta' ), 10, 2 );


                //ADD CUSTOM SECTION IN ADMIN USER
                add_action( 'show_user_profile', array( $this, 'add_customer_meta_paypal' ), 15 );
                add_action( 'edit_user_profile', array( $this, 'add_customer_meta_paypal' ), 15 );
                add_action( 'personal_options_update', array( $this, 'save_customer_meta_paypal' ), 15 );
                add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_paypal' ), 15 );

                add_action( 'update_option_yith_receiver', array( $this, 'update_user_meta' ), 10, 3 );
            }

            //ADD complete payment action into ORDERS
            add_filter( 'woocommerce_admin_order_actions', array( $this, 'admin_order_complete_payment_action' ), 10, 2 );

            //Show an admin notice if YITH Customize My Account Page is activated
            add_action('admin_notices', array( $this, 'show_admin_notices' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_scripts') );

        }

        /**
         * @return YITH_Paypal_Adaptive_Payments_Admin
         */
        public static function get_instance()
        {

            if( is_null( self::$instance ) ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public function enqueue_admin_script()
        {
            $is_wc_3 = version_compare( WC()->version, '3.0.0', '>=' );
            $params = array(
                'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
                'search_customers_nonce' => wp_create_nonce( 'search-paypal-email-customers' ),
                'is_wc_3' => $is_wc_3

            );
            $script_name = yit_load_js_file( 'yith_enhanced_select.js' );

            wp_register_script( 'yith_enhanced_select', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'js/' . $script_name, array( 'jquery', 'select2' ), YITH_PAYPAL_ADAPTIVE_VERSION, true );
            wp_localize_script( 'yith_enhanced_select', 'ywpadp_select2_param', $params );
            wp_register_style( 'yith_paypal_adp_css_admin', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'css/yith_paypal_adp_admin.css', array(), YITH_PAYPAL_ADAPTIVE_VERSION );

            if( ( isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) || ( isset( $_GET['post'] ) && 'product' == get_post_type( $_GET['post'] ) ) ) {

                $script_name = yit_load_js_file( 'yith_paypal_adp_product.js' );
                wp_enqueue_script( 'yith_enhanced_select' );
                wp_enqueue_script( 'yith_paypal_adp_product_js_admin', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'js/' . $script_name, array( 'jquery', 'jquery-ui-dialog' ,'yith_enhanced_select'), YITH_PAYPAL_ADAPTIVE_VERSION, true );
                wp_enqueue_style( 'yith_paypal_adp_css_admin' );
            }

            if( (isset( $_GET['page'] ) && 'yith_paypal_adaptive_payments_panel'  == $_GET['page'] ) ){
                $script_name = yit_load_js_file( 'yith_paypal_adp_admin.js' );
                wp_enqueue_script( 'yith_enhanced_select' );
                wp_enqueue_script( 'yith_paypal_adp_js_admin', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'js/' . $script_name, array( 'jquery', 'jquery-ui-dialog' ), YITH_PAYPAL_ADAPTIVE_VERSION, true );
                wp_enqueue_style( 'yith_paypal_adp_css_admin' );
                wp_localize_script( 'yith_paypal_adp_js_admin', 'yith_padp_param', array(  'is_customize_active' => defined( 'YITH_WCMAP_PREMIUM' ) && YITH_WCMAP_PREMIUM  ) );
            }

            if( isset( $_GET['post_type'] ) == 'shop_order' ){
                wp_enqueue_style( 'yith_paypal_adp_css_admin' );
            }

            if( ( ( isset( $_GET['page'] ) && 'wc-settings' == $_GET['page'] ) && ( isset( $_GET['section'] ) && 'yith_paypal_adaptive_payments' == $_GET['section'] ) ) || ( ( isset( $_GET['page'] ) && 'yith_paypal_adaptive_payments_panel' == $_GET['page'] ) ) ){

                $script_name = yit_load_js_file( 'yith_paypal_adp_gateway.js' );

                wp_enqueue_script( 'yith_paypal_adp_product_js_admin_gateway', YITH_PAYPAL_ADAPTIVE_ASSETS_URL . 'js/' . $script_name, array( 'jquery'), YITH_PAYPAL_ADAPTIVE_VERSION, true );

            }
        }

        /**
         * add yith adaptive payment tab into product data
         * @author YITHEMES
         * @since 1.0.0
         * @param array $product_data_tabs
         * @return array
         */
        public function write_paypal_adaptive_payments_tabs( $product_data_tabs )
        {

            $product_data_tabs['yith_adaptive_payments'] = array(
                'label' => __( 'PayPal Adaptive Payments', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                'target' => 'yith_adaptive_payments',
                'class' => array()

            );
            return $product_data_tabs;
        }

        /**
         *
         */
        public function write_product_paypal_adptive_payments_data()
        {

            wc_get_template( '/admin/paypal-adaptive-payments-product-data.php', array(), YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH, YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH );
        }

        /**
         * save the product receiver meta
         * @param int $post_id
         * @param $post
         */
        public function save_product_meta( $post_id, $post )
        {

            $product_receivers = isset( $_REQUEST['yith_product_receiver'] ) ? $_REQUEST['yith_product_receiver'] : array();
            $product = wc_get_product( $post_id );
            if( count( $product_receivers )>0 ) {

                yit_save_prop( $product, '_yit_paypal_adp_product_receivers', $product_receivers );

                $this->save_user_meta( $product_receivers );
            }
            else {

                yit_delete_prop( $product, '_yit_paypal_adp_product_receivers' );
            }
        }

        /**
         * add a section in user admin
         * @param WP_User $user
         */
        public function add_customer_meta_paypal( $user )
        {

            $paypal_email = get_user_meta( $user->ID, 'yith_paypal_email', true );
            ?>
            <h2><?php _e( 'PayPal Adaptive Payments info', 'yith-paypal-adaptive-payments-for-woocommerce' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="yith_user_paypal_email"><?php _e( 'Email', 'yith-paypal-adaptive-payments-for-woocommerce' ); ?></label></th>
                    <td>
                        <input id="yith_user_paypal_email" name="yith_paypal_email" type="email" value="<?php esc_attr_e( $paypal_email ); ?>"/>
                        <span class="description"><?php _e( 'Insert the email address you want to associate to your PayPal account',
                                'yith-paypal-adaptive-payments-for-woocommerce' );?></span>
                    </td>
                </tr>
            </table>
            <?php
        }

        /**
         * save the paypal field for user
         * @author YITHEMES
         * @since 1.0.0
         * @param int $user_id
         */
        public function save_customer_meta_paypal( $user_id )
        {

            if( isset( $_POST['yith_paypal_email'] ) ) {

                update_user_meta( $user_id, 'yith_paypal_email', $_POST['yith_paypal_email'] );
            }
        }

        /**
         * @param array $actions
         * @param WC_Order $order
         */
        public function admin_order_complete_payment_action( $actions, $order ){


            $order_id = yit_get_prop( $order, 'id' );
            $payment_method =   yit_get_prop( $order, 'yith_payment_method' );
            $pay_after      =   yit_get_prop( $order, 'yith_pay_after' );
            $payment_status =   yit_get_prop( $order, 'yith_payment_status' );

            if( 'chained' == $payment_method && 0<$pay_after && 'incomplete' == $payment_status && in_array( $order->get_status(), array('completed','processing' ) ) ){

                $actions['pay_secondary_receiver'] = array(
                    'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=yith_paypal_adaptive_payments_complete_payment&order_id=' . $order_id ), 'yith-padp-complete-payment' ),
                    'name'      => __( 'Pay Secondary Receivers', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                    'action'    => "pay_secondary_receiver"
                )            ;
            }

            return $actions;
        }

        public function update_user_meta( $old_value, $value, $option ){

            $this->save_user_meta( $value );
        }

        public function save_user_meta( $receivers ){

            foreach( $receivers as $receiver ){

                $user_id = $receiver['receiver_id'];
                $email = $receiver['email'];

                $yith_mail = get_user_meta( $user_id, 'yith_paypal_email', true );

                if( $email!== $yith_mail ){
                    update_user_meta( $user_id, 'yith_paypal_email', $email );
                }


            }
        }


        public function show_admin_notices(){

            $is_customize_active = defined('YITH_WCMAP_PREMIUM') &&  YITH_WCMAP_PREMIUM;
            if( ( isset( $_GET['page'] ) && 'yith_paypal_adaptive_payments_panel' == $_GET['page'] ) && ( isset( $_GET['tab'] ) && 'receiver-endpoint-settings' == $_GET['tab'] ) && $is_customize_active ){

                $message= __('Customize My Account Page is activated, you can change the YITH PayPal Adaptive Payments Endpoint here', 'yith-paypal-adaptive-payments-for-woocommerce');
                $admin_url = admin_url('admin.php');
                $args = array(
                    'page' => 'yith_wcmap_panel',
                    'tab' => 'endpoints'
                );
                $page_url = esc_url( add_query_arg( $args, $admin_url ) );
                $message = sprintf('%1$s <a href="%2$s">%2$s</a>',$message, $page_url);
                ?>

                <div class="notice notice-info" style="padding-right: 38px;position: relative;">
                    <p><?php echo $message;?></p>
                </div>

                <?php
            }
        }

        public function include_admin_scripts(){

            if( !wp_script_is( 'js-cookie' ) ){
                wp_enqueue_script( 'js-cookie', WC()->plugin_url().'/assets/js/js-cookie/'.yit_load_js_file('js.cookie.js' ),array('jquery'), '2.1.4', true  );
            }
            wp_enqueue_script( 'yith_padp_dismiss_notice', YITH_PAYPAL_ADAPTIVE_ASSETS_URL.'js/'.yit_load_js_file('yith_paypal_dismiss_notice.js' ), array('jquery', 'js-cookie' ), YITH_PAYPAL_ADAPTIVE_VERSION, true );
        }



    }
}

if( !function_exists( 'YITH_Paypal_Adaptive_Payments_Admin' ) ) {
    /**
     * @return YITH_Paypal_Adaptive_Payments_Admin
     */
    function YITH_Paypal_Adaptive_Payments_Admin()
    {

        return YITH_Paypal_Adaptive_Payments_Admin::get_instance();
    }
}