<?php
if( !defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'WC_Payment_Gateway' ) ) {

    require_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-payment-gateway.php' );
}

if( !class_exists( 'WC_Gateway_YITH_Paypal_Adaptive_Payments' ) ) {

    class WC_Gateway_YITH_Paypal_Adaptive_Payments extends WC_Payment_Gateway
    {

        protected static $instance;

        public function __construct()
        {

            $this->init_plugin();

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            add_action( 'woocommerce_api_wc_gateway_yith_paypal_adaptive_payments', array( $this, 'check_ipn_response' ) );

            add_action( 'yith_paypal_adaptive_payments_process_ipn', array( $this, 'process_ipn' ) );

            add_action( 'yith_paypal_adaptive_payments_cron', array( $this, 'process_incomplete_orders' ) );

            add_action( 'yith_paypal_adaptive_payments_pay_secondary_receivers', array( $this, 'pay_secondary_receiver' ) );
            add_action( 'admin_notices', array( $this, 'show_gateway_notices' ) );

        }

        public static function get_instance()
        {

            if( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * init all plugin fields
         * @author Salvatore Strano
         * @since 1.1.0
         */
        public function init_plugin(){
            $this->id = 'yith_paypal_adaptive_payments';
            $this->method_title = __( 'PayPal Adaptive Payments', 'yith-paypal-adaptive-payments-for-woocommerce' );
            $this->method_description = __( 'You can use PayPal for parallel or chained payments', 'yith-paypal-adaptive-payments-for-woocommerce' );
            $this->has_fields = false;
            $this->order_button_text = __( 'Pay with PayPal', 'yith-paypal-adaptive-payments-for-woocommerce' );

            $this->init_form_fields();
            $this->init_settings();


            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            //User details
            $this->username = $this->get_option( 'app_username' );
            $this->password = $this->get_option( 'app_password' );
            $this->signature = $this->get_option( 'app_signature' );
            $this->app_id = $this->get_option( 'app_id' );
            $this->primary_receiver = $this->get_option( 'primary_receiver_email' );
            $this->pay_method = $this->get_option( 'pay_method' );
            $this->is_sandbox = $this->get_option( 'enable_sandbox_mode' );
            $this->chained_delay = $this->get_option( 'payment_delay' );
            $this->invoice_prefix = $this->get_option( 'invoice_prefix', 'YITH-' );
            $this->is_debug_log = $this->get_option( 'enable_debuggin_mode' );

            $this->api_production_url = 'https://svcs.paypal.com/AdaptivePayments/';
            $this->api_sandbox_url = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
            $this->payment_production_url = 'https://www.paypal.com/cgi-bin/webscr';
            $this->payment_sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            $this->notify_url = WC()->api_request_url( 'WC_Gateway_YITH_Paypal_Adaptive_Payments' );
            $this->refund_enabled = $this->get_option( 'pay_enable_refund', 'no' );

            if( 'yes' == $this->refund_enabled ) {
                $this->supports = array(
                    'refunds'
                );
            }

            $this->log = false;

            if( 'yes' == $this->is_debug_log ) {
                $this->log = new WC_Logger();
            }
        }

        public function get_id(){
            return $this->id;
        }

        public function is_enabled(){
            return apply_filters( 'yith_padp_is_gateway_enabled', $this->enabled );
        }

	    /**
         * return payment option
         * @author YITHEMES
         * @since 1.0.0
         * @override
         * @return array
         */
        public function init_form_fields()
        {
            $this->form_fields = apply_filters( 'yith_padp_gateway_settings', array(

                    'enabled' => array(
                        'title' => __( 'Enable/Disable', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'checkbox',
                        'label' => __( 'Enable adaptive payments', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'default' => 'yes'
                    ),
                    'title' => array(
                        'title' => __( 'Title', 'woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'This controls the title that users see during checkout.', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'default' => _x( 'PayPal Adaptive Payments', 'Selectable payment method in checkout', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'desc_tip' => true ),
                    'description' => array(
                        'title' => __( 'Description', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'textarea',
                        'description' => __( 'This controls the description that users see during checkout.', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'default' => __( 'Pay with PayPal; if you don\'t have a PayPal account, you can pay using your credit card.',
                            'yith-paypal-adaptive-payments-for-woocommerce' ),
                    ),
                    'app_settings' => array(
                        'title' => __( 'API Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'title'
                    ),
                    'app_username' => array(
                        'title' => __( 'PayPal API username', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Enter a valid PayPal application username', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'desc_tip' => true
                    ),
                    'app_password' => array(
                        'title' => __( 'PayPal API password', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Enter a valid PayPal application password', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'desc_tip' => true
                    ),
                    'app_signature' => array(
                        'title' => __( ' PayPal API signature', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Enter a valid PayPal application signature', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'desc_tip' => true
                    ),

                    'app_settings_info' => array(
                        'type' => 'custom_info',
                        'description' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s <a href="%s" target="_blank">%s</a> %s',
                            __( 'You can find your API Signature in', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
                            __( 'Sandbox Account', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            __( 'if you use a sandbox account or in ', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            'https://www.paypal.com/businessprofile/mytools/apiaccess',
                            __( 'PayPal Account', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            __( 'if you use an real business account', 'yith-paypal-adaptive-payments-for-woocommerce' )
                        ),
                    ),
                    'app_id' => array(
                        'title' => __( 'Application ID', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Enter a valid application ID', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'desc_tip' => true
                    ),
                    'app_id_info' => array(
                        'type' => 'custom_info',
                        'description' => sprintf( '%s <code>%s</code> %s <a href="%s" target="_blank">%s</a>',
                            __( 'If you need an AppID for development and testing purposes please use this test APPId',
                                'yith-paypal-adaptive-payments-for-woocommerce' ),
                            'APP-80W284485P519543T',
                            __( 'Alternatively, you can find your real APP ID within your', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            'https://www.paypal-apps.com/user/my-account/applications',
                            __( 'PayPal Apps', 'yith-paypal-adaptive-payments-for-woocommerce' )
                        ),
                    ),
                    'payment_settings' => array(
                        'title' => __( 'Payment Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'title'
                    ),
                    /* 'pay_enable_refund' => array(
                         'title' => __( 'Enable/Disable Refunds', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                         'type' => 'checkbox',
                         'label' => __( 'Enable refunds for adaptive payments', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                         'default' => 'no',
                         'description' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
                             __( 'If you enabled the refund system, please be sure that all your receivers grant you third-party access (to make a
                    refund) by logging in to PayPal, choosing API Access on the Profile page, clicking the link to', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                             'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-list-auths',
                             __( 'Grant API permission', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                             __( 'and selecting Refund after clicking Configure a custom API authorization.', 'yith-paypal-adaptive-payments-for-woocommerce' )
                         )
                     ),*/
                    'pay_method' => array(
                        'title' => __( 'Payment Method', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'select',
                        'options' => array( 'parallel' => __( 'Parallel Payments', 'yith-paypal-adaptive-payments-for-woocommerce' ), 'chained' => __(
                            'Chained Payments', 'yith-paypal-adaptive-payments-for-woocommerce' ) ),
                        'description' => sprintf( '%s: %s, %s.<br/>%s <a href="%s" target="_blank">%s</a> ',
                            __( 'Select a payment method', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            __( 'Parallel method, payment from a sender is split among 2 to 6 receivers', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            __( 'Chained method, payment from a sender is indirectly split among 1 to 9 secondary receivers', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            __( 'To learn more about Parallel and Chained Payments, please,', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                            'https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/',
                            __( 'visit this page', 'yith-paypal-adaptive-payments-for-woocommerce' )
                        ),
                        'default' => 'parallel'
                    ),
                    'payment_delay' => array(
                        'title' => __( 'Delay', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'number',
                        'custom_attributes' => array(
                            'min' => 0,
                            'max' => 90,
                        ),
                        'description' => __( 'Set a delay (days) for the payments; enter 0 for instant payments (this works only with chained
                    payments - max value 90 )', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'default' => 0,
                    ),
                    'primary_receiver_email' => array(
                        'title' => __( 'Primary Receiver', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'email',
                        'description' => __( 'Enter a valid PayPal email address', 'yith-paypal-adaptive-payments-for-woocommerce' )
                    ),
                    'invoice_prefix' => array(
                        'title' => __( 'Invoice Prefix', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'text',
                        'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores,
                    ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'default' => 'YITH-',
                        'desc_tip' => true,
                    ),

                    'enable_sandbox_mode' => array(
                        'title' => __( 'Enable/Disable Sandbox mode', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'checkbox',
                        'label' => __( 'Enable PayPal sandbox', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'default' => 'no',
                        'description' => sprintf( __( 'PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', 'yith-paypal-adaptive-payments-for-woocommerce' ), 'https://developer.paypal.com/' ),
                    ),
                    'debug_section' => array(
                        'title' => __( 'Debug Settings', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'title'
                    ),
                    'enable_debuggin_mode' => array(
                        'title' => __( 'Debugging mode', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'label' => __( 'Enable debugging mode', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'type' => 'checkbox',
                        'default' => 'no',
                        'description' => sprintf( __( 'Log PayPal events, such as IPN requests, inside <code>woocommerce/logs/' . esc_attr( $this->id ) . '-%s.txt</code>', 'yith-paypal-adaptive-payments-for-woocommerce' ), sanitize_file_name( wp_hash( $this->id ) ) ),

                    )
                )

            );

        }

        /**
         * process the payment
         * @author YITHEMES
         * @since 1.0.0
         * @param int $order_id
         * @return array
         */
        public function process_payment( $order_id )
        {
            $order = wc_get_order( $order_id );
            $response = $this->process_paypal_request( $order );
	        WC()->cart->empty_cart();

            if( $response['success'] ) {

                $pay_url = $this->get_pay_url();
                $query_args = array(
                    'cmd' => '_ap-payment',
                    'paykey' => $response['pay_key']
                );
                $pay_url = esc_url_raw( add_query_arg( $query_args, $pay_url ) );

                $this->add_log( $this->id, 'PAY URL ' . $pay_url );

                return array(
                    'result' => 'success',
                    'redirect' => $pay_url
                );
            }
            else {

                wc_add_notice( $response['message'], 'error' );
                return array(
                    'result' => 'fail',
                    'redirect' => ''
                );
            }

        }

        /**
         * process a paypal request, try to generate a valid pay key
         * @author YITHEMES
         * @since 1.0.0
         * @param WC_Order $order
         * @return array
         */
        public function process_paypal_request( $order )
        {

            $paypal_order_data = $this->build_payment_args( $order );
            $params = array(
                'body' => json_encode( $paypal_order_data ),
                'timeout' => 60,
                'httpversion' => '1.1',
                'headers' => $this->get_paypal_headers()
            );

            $api_url = $this->get_api_url();

            $this->add_log( $this->id, 'Pay key request for order ' . $order->get_order_number() );

            $response = wp_safe_remote_post( $api_url . 'Pay', $params );
            $result = array(
                'success' => false,
                'pay_key' => '',
                'message' => __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'yith-paypal-adaptive-payments-for-woocommerce' )
            );


            if( is_wp_error( $response ) ) {
                $this->add_log( $this->id, 'WP_ERROR appeared when the pay key request was done: ' .
                    $response->get_error_message
                    () );
            }
            else if( $response['response']['code'] == 200 && $response['response']['message'] == 'OK' ) {

                $paypal_response = json_decode( $response['body'] );

                if( isset( $paypal_response->payKey ) ) {

                    $pay_key = esc_attr( $paypal_response->payKey );

                    $this->add_log( $this->id, 'The pay key has been successfully created: PayKey = ' . $pay_key );


                    $this->add_payment_options( $pay_key );
                    $result['success'] = true;
                    $result['pay_key'] = $pay_key;
                    $result['message'] = '';

                    yit_save_prop( $order, 'yith_pay_key', $pay_key );
                }

                if( isset( $paypal_response->error ) ) {

                    $errors = $paypal_response->error;
                    $this->add_log( $this->id, 'Pay key creation failed ' . print_r( $paypal_response, true
                        ) );

                    foreach ( $errors as $error ) {

                        $order->add_order_note( sprintf( '%s: ID:%s, %s', __( 'The following error appeared',
                            'yith-paypal-adaptive-payments-for-woocommerce' ), $error->errorId, $error->message ) );
                    }
                }


            }

            return $result;


        }


        /**
         * build all payment args for a paypal request
         * @author YITHEMES
         * @since 1.0.0
         * @param WC_Order $order
         * @return array
         */
        private function build_payment_args( $order )
        {


            //if chained method with delay
            if( 'chained' == $this->pay_method && $this->chained_delay>0 ) {
                $action_type = 'PAY_PRIMARY';
            }
            else {
                $action_type = 'CREATE';
            }

            $return_url = esc_url_raw( add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) );
            $cancel_url = esc_url_raw( $order->get_cancel_order_url_raw() );
            $order_id = yit_get_prop( $order, 'id' );
            $params = array(
                'actionType' => $action_type,
                'currencyCode' => get_woocommerce_currency(),
                'trackingId' => $this->invoice_prefix . $order_id,
                'returnUrl' => $return_url,
                'cancelUrl' => $cancel_url,
                'ipnNotificationUrl' => $this->notify_url,
                'requestEnvelope' => array(
                    'errorLanguage' => 'en_US',
                    'detailLevel' => 'ReturnAll'

                ),
                'receiverList' => $this->build_receivers( $order )

            );

            $this->add_log( $this->id, 'Build Payments args ' . print_r( $params, true ) );
            return $params;
        }

        /**
         * this method calculate the right commission for each receiver
         * @author YITHEMES
         * @since 1.0.0
         * @param  WC_Order $order
         * @return array
         */
        protected function build_receivers( $order )
        {
            $receiver_list  =   apply_filters( 'yith_paypal_adaptive_payment_custom_build_receivers',
                                        $this->gateway_build_receivers( $order ),
                                        $order,
                                        $this->pay_method,
                                        $this->primary_receiver
            );

            extract( $receiver_list );
            $order_total = $order->get_total();
            $receiver_data = array();
            //Add primary receiver
            if( $tot_commission>0 ) {

                if( 'parallel' == $this->pay_method ) {

                    if( $order_total == $tot_commission ) {
                        $receiver_data['receiver'] = array_values( $receivers );
                    }
                    else {

                        $primary_receiver = array(
                            'email' => $this->primary_receiver,
                            'amount' => number_format( $order_total-$tot_commission, 2, '.', '' )
                        );

                        array_unshift( $receivers, $primary_receiver );
                        $receiver_data['receiver'] = array_values( $receivers );
                    }
                }
                else {
                    $primary_receiver = array(
                        'email' => $this->primary_receiver,
                        'amount' => number_format( $order_total, 2, '.', '' ),
                        'primary' => true
                    );

                    array_unshift( $receivers, $primary_receiver );
                    $receiver_data['receiver'] = array_values( $receivers );
                }

            }
            else {
                $receiver_data['receiver'] = array(
                    'email' => $this->primary_receiver,
                    'amount' => number_format( $order_total, 2, '.', '' )
                );
            }

            return $receiver_data;
        }


        /**
         * @param WC_Order $order
         * @return array
         */
        protected function gateway_build_receivers( $order )
        {
            $receivers = array();
            $tot_commission = 0;

            $order_items = $order->get_items();

            foreach ( $order_items as $item ) {

                $product_id = $item['product_id'];
                $current_receivers = $this->get_receiver_item( $product_id );
                $line_total = $order->get_line_total( $item, true );
                $qty = isset( $item['qty'] ) ? $item['qty'] : 1;

                $tot_sales = get_post_meta( $product_id, 'yith_padp_product_total_sales', true );
                $tot_sales = empty( $tot_sales ) ? 0 : $tot_sales;


                if( $current_receivers ) {

                    foreach ( $current_receivers as $receiver ) {

                        if( empty( $receiver['split_after'] ) || ( $receiver['split_after']<=$tot_sales ) ) {
                            $user_id = $receiver['receiver_id'];
                            $user = get_user_by( 'id', $user_id );
                            $email = $user->yith_paypal_email;
                            $receiver_commission = $receiver['commission'];

                            $receiver_total = round( $line_total * $receiver_commission / 100, 2 );

                            if( !isset( $receivers[$email] ) ) {

                                $receivers[$email] = array(
                                    'email' => $email,
                                    'amount' => number_format( $receiver_total, 2, '.', '' )
                                );

                                if( 'chained' == $this->pay_method ) {

                                    $receivers[$email]['primary'] = false;
                                }
                            }
                            else {

                                $receivers[$email]['amount'] = number_format( $receivers[$email]['amount']+$receiver_total, 2, '.', '' );
                            }

                            $tot_commission += $receiver_total;

                        }
                    }
                }
            }

            return array( 'tot_commission' => $tot_commission, 'receivers' => $receivers );
        }

        /**
         * add payment options to paypal request
         * @author YITHEMES
         * @since 1.0.0
         * @param string $pay_key
         */
        protected function add_payment_options( $pay_key )
        {

            $blogname = get_option( 'blogname' );
            $body_params = array(
                'payKey' => $pay_key,
                'requestEnvelope' => array(
                    'detailLevel' => 'ReturnAll',
                    'error_language' => 'en_US'
                ),
                'displayOptions' => array(
                    'businessName' => trim( substr( $blogname, 0, 128 ) )
                ),
                'senderOptions' => array(
                    'referrerCode' => 'YITH_CART'
                )
            );

            $payment_options = array(
                'body' => json_encode( $body_params ),
                'timeout' => 60,
                'httpversion' => '1.1',
                'headers' => $this->get_paypal_headers()
            );

            $api_url = $this->get_api_url();


            $this->add_log( $this->id, 'Setting payment options with the following data: ' . print_r( $body_params, true ) );
            $response = wp_safe_remote_post( $api_url . 'SetPaymentOptions', $payment_options );

            if( !is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {

                $this->add_log( $this->id, 'Payment options configured successfully!' );

            }
            else {
                $this->add_log( $this->id, 'Failed to configure payment options: ' . print_r( $response, true ) );

            }

        }




        /**
         * return paypal headers
         * @author YITHEMES
         * @since 1.0.0
         * @return array
         */
        private function get_paypal_headers()
        {

            return array(
                'X-PAYPAL-SECURITY-USERID' => $this->username,
                'X-PAYPAL-SECURITY-PASSWORD' => $this->password,
                'X-PAYPAL-SECURITY-SIGNATURE' => $this->signature,
                'X-PAYPAL-REQUEST-DATA-FORMAT' => 'JSON',
                'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON',
                'X-PAYPAL-APPLICATION-ID' => $this->app_id,
            );
        }

        /**
         * get the right api url
         * @author YITHEMES
         * @since 1.0.0
         * @return string
         */
        private function get_api_url()
        {

            if( 'yes' == $this->is_sandbox ) {
                $url = $this->api_sandbox_url;
            }
            else {
                $url = $this->api_production_url;
            }
            return $url;
        }

        /**
         * get the right pay url
         * @author YITHEMES
         * @since 1.0.0
         * @return string
         */
        private function get_pay_url()
        {
            if( 'yes' == $this->is_sandbox ) {

                $url = $this->payment_sandbox_url;
            }
            else {
                $url = $this->payment_production_url;
            }
            return $url;

        }

        /**
         * if log mode is enabled,add the message in log file
         * @author YITHEMES
         * @since 1.0.0
         * @param string $id
         * @param string $message
         */
        private function add_log( $id, $message )
        {

            if( $this->log ) {

                $this->log->add( $id, $message );

            }
        }

        /**
         *
         * return the receivers information
         * @author YITHEMES
         * @since 1.0.0
         * @param int $product_id
         * @return bool|array
         */
        protected function get_receiver_item( $product_id )
        {

            $global_receiver = get_option( 'yith_receiver' );
            $product = wc_get_product( $product_id );
            $product_receiver = yit_get_prop( $product, '_yit_paypal_adp_product_receivers' );

            if( !empty( $product_receiver ) ) {
                return $product_receiver;
            }
            else if( !empty( $global_receiver ) ) {
                return $global_receiver;
            }

            return false;
        }

        /**
         * check ipn response
         * @author YITHEMES
         * @since 1.0.0
         */
        public function check_ipn_response()
        {

            $ipn_response = !empty( $_POST ) ? $_POST : false;

            $this->add_log( $this->id, 'Check IPN response ' . print_r( $ipn_response, true ) );
            if( $ipn_response ) {
                header( 'HTTP/1.1 200 OK' );
                do_action( 'yith_paypal_adaptive_payments_process_ipn', $ipn_response );
            }
            else {
                $this->add_log( $this->id, 'Error IPN Response '.print_r( $ipn_response, true ) );
                wp_die( 'PayPal IPN Request Failure', 'PayPal IPN', array( 'response' => 200 ) );

            }

        }

        /**
         * process the paypal ipn response
         * @author YITHEMES
         * @since 1.0.0
         * @param array $ipn_response
         */
        public function process_ipn( $ipn_response )
        {

            $ipn_response = stripslashes_deep( $ipn_response );

            if( isset( $ipn_response['tracking_id'] ) ) {

                $order_id = str_replace( $this->invoice_prefix, '', $ipn_response['tracking_id'] );
                /**
                 * @var WC_Order $order
                 */
                $order = wc_get_order( $order_id );
                $this->add_log( $this->id, 'Processing IPN for order #' . $order_id );

                $status = $ipn_response['status'];

                $this->add_log( $this->id, 'The payment status is ' . $status );

                yit_save_prop( $order, 'yith_payment_status', strtolower( $status ) );

                $payment_method = yit_get_prop( $order, 'yith_payment_method' );

                $payment_details = $this->get_payment_details( $ipn_response['pay_key'] );

                if( empty( $payment_method ) ) {
                    yit_save_prop( $order, 'yith_payment_method', $this->pay_method );
                }
                switch ( $status ) {

                    case 'CANCELED':
                        $order->update_status( 'cancelled', __( 'Payment has been cancelled by IPN', 'yith-paypal-adaptive-payments-for-woocommerce'
                        ) );
                        break;
                    case 'CREATED':
                        $order->update_status( 'on-hold', __( 'The payment request was received; funds will be transferred once the payment is approved', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                        break;
                    case 'COMPLETED':
                        $this->set_order_as_complete( $order, $ipn_response, __( 'The payment was successful', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                        break;
                    case 'INCOMPLETE':

                        //if is delayed chained payment
                        if( 'chained' == $this->pay_method && 0<$this->chained_delay ) {

                                $this->perform_incomplete_payment_status_for_chained_method( $order, $ipn_response );
                        }
                        else {
                            yit_save_prop( $order, 'yith_pay_key', $ipn_response['pay_key'] );
                            yit_save_prop( $order, 'yith_sender_email', $ipn_response['sender_email'] );
                            $message = __( 'Some transfers succeeded and some failed for a parallel payment', 'yith-paypal-adaptive-payments-for-woocommerce' );
                            $order->update_status( 'on-hold', $message );
                        }
                        break;
                    case 'ERROR':
                        $order->update_status( 'failed', __( 'The payment failed and all attempted transfers failed or all completed transfers were successfully reversed', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                        break;
                    case 'REVERSALERROR':
                        $order->update_status( 'failed', __( 'One or more transfers failed when attempting to reverse a payment', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                        break;
                    case 'PROCESSING':
                        $order->update_status( 'on-hold', __( 'The payment is in progress', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                        break;
                    case 'PENDING';
                        $order->update_status( 'pending', __( 'The payment is awaiting processing', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                        break;
                }

                $payment_info =  $payment_details['paymentInfoList']['paymentInfo'];
                $this->save_user_commission_status( $order_id, $payment_info );

                do_action( 'yith_paypal_adaptive_payments_after_process_ipn', $order, $payment_details, $this->primary_receiver );
            }
            else {

                $this->add_log( $this->id, 'Invalid IPN request ' . print_r( $ipn_response, true ) );
            }
        }

        /**
         * check ipn response for chained payments
         * @author Salvatore Strano
         * @since 1.1.0
         * @param $order
         * @param $ipn_response
         */
        public function perform_incomplete_payment_status_for_chained_method( $order, $ipn_response ){

            $chained_delay = yit_get_prop( $order, 'yith_pay_after', true );

            if( empty( $chained_delay ) ) {

                yit_save_prop( $order, 'yith_pay_after', $this->chained_delay );
            }
            $primary_transaction_id = !empty( $payment_details['paymentInfoList']['paymentInfo'][0]['transactionId'] ) ? $payment_details['paymentInfoList']['paymentInfo'][0]['transactionId'] : '';
            $primary_transaction_status = !empty( $payment_details['paymentInfoList']['paymentInfo'][0]['transactionStatus'] ) ? $payment_details['paymentInfoList']['paymentInfo'][0]['transactionStatus'] : '';
            $is_primary = !empty( $payment_details['paymentInfoList']['paymentInfo'][0]['receiver']['primary'] ) ? $payment_details['paymentInfoList']['paymentInfo'][0]['receiver']['primary'] : false;
            $message = __( 'There is a delayed chained payment for this order, secondary receivers have not been paid', 'yith-paypal-adaptive-payments-for-woocommerce' );
            //if the payment to primary receive is completed
            if( $is_primary && '' !== $primary_transaction_id && 'COMPLETED' === $primary_transaction_status ) {

                $message = sprintf( '%s %s', __( 'The payment was successful', 'yith-paypal-adaptive-payments-for-woocommerce' ), $message );
                $this->set_order_as_complete( $order, $ipn_response, $message );
            }
        }
        /**
         * update total sales
         * @author YITHEMES
         * @since 1.0.0
         * @param WC_Order $order
         */
        protected function update_product_sales( $order )
        {

            $items = $order->get_items();
            foreach ( $items as $item ) {

                $product_id = $item['product_id'];
                $product = wc_get_product( $product_id );
                $qty = !empty( $item['qty'] ) ? $item['qty'] : 1;
                $tot_product_sales = yit_get_prop( $product, 'yith_padp_product_total_sales' );
                $tot_product_sales = empty( $tot_product_sales ) ? $qty : $tot_product_sales+$qty;

                yit_save_prop( $product, 'yith_padp_product_total_sales', $tot_product_sales );
            }
        }

        /**
         * Check if this gateway is enabled and available in the user's country.
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function is_valid_for_use()
        {
            return in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paypal_supported_currencies', array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB' ) ) );
        }

        /**
         * Check if all required fields have been filled
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function is_options_valid_for_use()
        {
            return ( $this->username != '' &&
                $this->password != '' &&
                $this->signature != '' &&
                $this->app_id != '' &&
                $this->primary_receiver != ''
            );
        }

        /**
         * check if the gateway is available
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function is_available()
        {


            return apply_filters( 'yith_paypal_adaptive_payments_is_available', parent::is_available() &&
            																	$this->is_valid_for_use() &&
            																	$this->is_options_valid_for_use() &&
            																	$this->order_has_receivers() &&
            																	$this->products_allowed()
            		);
        }

        /**
         * check if the products in the cart are supported by gateway
         * @author YITHEMES
         * @since 1.0.1
         * @return boolean
         */
        public function products_allowed(){
        	
        	$allowed = true;
        	
        	$product_not_allowed = apply_filters( 'yith_paypal_adaptive_payments_unsupported_products', array( 'subscription','subscription_variation' ) );
        	
        	if( !empty( WC()->cart ) ) {
        	
        		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
        			/**
        			 * @var WC_Product $product ;
        			 */
        			$product = $values['data'];
        			$product_type = strtolower( $product->get_type() );
                    $product_id = yit_get_product_id( $product ) ;
        	        	
        			if( in_array( $product_type , $product_not_allowed ) ) {
        				$allowed = false;
        				break;
        			}else {

        				if( function_exists( 'YITH_WC_Subscription' ) && YITH_WC_Subscription()->is_subscription( $product_id ) ){
        					$allowed = false;
        					break;
        				}
        			}
        	
        		}
        	}
        	
        	return $allowed;
        	}
        /**
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function order_has_receivers()
        {

            $has_receivers = false;
            if( !empty( WC()->cart ) ) {

                foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                    /**
                     * @var WC_Product $product ;
                     */
                    $product = $values['data'];
                    $product_id = yit_get_base_product_id( $product );
                    $receivers = $this->get_receiver_item( $product_id );

                    if( $receivers ) {
                        $has_receivers = true;
                        break;
                    }

                }
            }

            return apply_filters( 'yith_paypal_adaptive_payments_has_receivers', $has_receivers );
        }

        /**
         * @param WC_Order $order
         * @param array $ipn_response
         */
        protected function set_order_as_complete( $order, $ipn_response, $message )
        {


            if( $order->get_status() != 'completed' ) {

               yit_save_prop( $order, 'yith_pay_key', $ipn_response['pay_key'] );
               yit_save_prop( $order, 'yith_sender_email', $ipn_response['sender_email'] );

                /**
                 * TODO mettere pure la lista delle transaction_id ?
                 */
                //update product sales
                $this->update_product_sales( $order );

                $order->add_order_note( $message );
                $order->payment_complete();
            }
        }

        /**
         * get all incomplete order for pay the secondary receivers
         * @author YITHEMES
         * @since 1.0.0
         */
        public function process_incomplete_orders()
        {

            $now = current_time( 'timestamp', 1 );

            $args = array(

                'post_type' => 'shop_order',
                'posts_per_page' => -1,
                'post_parent' => 0,
                'post_status' => array( 'wc-processing', 'wc-completed' ),
                'meta_query' => array(
                    array(
                        'key' => 'yith_payment_method',
                        'value' => 'chained',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'yith_payment_status',
                        'value' => 'incomplete',
                        'compare' => '='
                    ),

                ),
                'meta_key' => 'yith_pay_after',
                'meta_value_num' => 0,
                'meta_compare' => '>',

            );


            $orders = get_posts( $args );

            foreach ( $orders as $order ) {
                /**
                 * @var WP_Post $order
                 */
                $order_date_gmt = strtotime( $order->post_date_gmt );
                $order_id = yit_get_order_id( $order );
                $day_after = yit_get_prop( $order, 'yith_pay_after' );
                $how_time = ( $now-$order_date_gmt ) / DAY_IN_SECONDS;

                $this->add_log( $this->id, 'Processing this order ' . $order_id . ' Days since order creation ' . $how_time . ' Pay after ' .
                    $day_after );

                if( $how_time>=$day_after ) {

                    do_action( 'yith_paypal_adaptive_payments_pay_secondary_receivers', $order_id );
                }
            }
        }

        /**
         * @author Salvatore Strano
         * @since 1.0.0
         * @param int $order_id
         */
        public function pay_secondary_receiver( $order_id )
        {
            $order = wc_get_order( $order_id );
            $payKey = yit_get_prop( $order, 'yith_pay_key' );
          
            $body_params = array(
                'payKey' => $payKey,
                'requestEnvelope' => array(
                    'errorLanguage' => 'en_US',
                    'detailLevel' => 'ReturnAll'
                )
            );
            $params = array(
                'body' => json_encode( $body_params ),
                'timeout' => 60,
                'httpversion' => '1.1',
                'headers' => $this->get_paypal_headers()
            );

            $api_url = $this->get_api_url();

            $this->add_log( $this->id, 'Payment request for secondary receivers for order ' . $order->get_order_number() );

            $response = wp_safe_remote_post( $api_url . 'ExecutePayment', $params );

            if( is_wp_error( $response ) ) {
                $this->add_log( $this->id, 'Error during payment issuing for secondary receivers' );
            }
            else if( $response['response']['code'] == 200 && $response['response']['message'] == 'OK' ) {

                $this->add_log( $this->id, 'Response of ExecutePayment action ' . print_r( $response['body'], true ) );
                $payment_response = json_decode( $response['body'], true );

                if( $payment_response['responseEnvelope']['ack'] == 'Success' ) {

                    $payment_status = $payment_response['paymentExecStatus'];

                    yit_save_prop( $order, 'yith_payment_status', strtolower( $payment_status ) );

                    switch ( $payment_status ) {

                        case 'CREATED':
                            $order->add_order_note( __( 'The payment request was received; funds will be transferred once the payment is approved', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                            break;
                        case 'COMPLETED':
                            //update product sales
                            if( $order->get_status() != 'completed' ) {
                                $this->update_product_sales( $order );

                                $order->add_order_note( __( 'All receivers have been paid', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                                $order->payment_complete();
                            }
                            break;
                        case 'INCOMPLETE':
                            $order->add_order_note( __( 'Some transfers succeeded and some failed for a parallel payment', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                            break;
                        case 'ERROR':
                            $order->update_status( 'failed', __( 'The payment failed and all attempted transfers failed or all completed transfers were successfully reversed', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                            break;
                        case 'REVERSALERROR':
                            $order->update_status( 'failed', __( 'One or more transfers failed when attempting to reverse a payment', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                            break;
                    }

                    $this->update_commission_status( $order, $payment_status );
                }
            }

        }

        /**
         * get payment details by payKey
         * @author YITHEMES
         * @since 1.0.0
         * @param string $payKey
         * @return bool|array
         */
        public function get_payment_details( $payKey )
        {

            $body_params = array(
                'payKey' => $payKey,
                'requestEnvelope' => array(
                    'errorLanguage' => 'en_US',
                    'detailLevel' => 'ReturnAll'
                )
            );
            $params = array(
                'body' => json_encode( $body_params ),
                'timeout' => 60,
                'httpversion' => '1.1',
                'headers' => $this->get_paypal_headers()
            );

            $api_url = $this->get_api_url();

            $this->add_log( $this->id, 'Request for Pay Key payment details ' . $payKey );

            $response = wp_safe_remote_post( $api_url . 'PaymentDetails', $params );

            if( is_wp_error( $response ) ) {
                $this->add_log( $this->id, 'Your request for payment details failed' );
                $response = false;
            }
            else if( $response['response']['code'] == 200 && $response['response']['message'] == 'OK' ) {

                $this->add_log( $this->id, 'Payment Details ' . print_r( $response['body'], true ) );
                $response = json_decode( $response['body'], true );

            }
            return $response;
        }

        /**
         * save the user commission into table
         * @author YITHEMES
         * @since 1.0.0
         * @param $order_id
         * @param $payment_details
         */
        public function save_user_commission_status( $order_id, $payment_details )
        {
            if( !YITH_PayPal_Adaptive_Payments_Integrations::is_multivendor_active() ) {


                foreach ( $payment_details as $detail ) {


                    $receiver_email = trim( $detail['receiver']['email'] );
                    $receiver_commission = $detail['receiver']['amount'];
                    $receiver_transaction_id = isset( $detail['transactionId'] ) ? $detail['transactionId'] : '';
                    $receiver_status = isset( $detail['transactionStatus'] ) ? strtolower( $detail['transactionStatus'] ) : 'incomplete';

                    $receiver = yith_get_user_by_meta( 'yith_paypal_email', $receiver_email );

                    if( $receiver_email == $this->primary_receiver ){
                        continue;
                    }
                    if( $receiver ) {
                        $user_id = $receiver->ID;

                        if ( YITH_PADP_Receiver_Commission()->user_transaction_exist( $user_id, $order_id ) ){
                            YITH_PADP_Receiver_Commission()->update( $user_id, $order_id,$receiver_commission, $receiver_status,$receiver_transaction_id );
                        }else{
                            YITH_PADP_Receiver_Commission()->add_transaction($user_id, $order_id, $receiver_commission, $receiver_status, $receiver_transaction_id);
                        }
                    }
                    else {

                        $this->add_log( $this->id, 'User with PayPal email ' . $receiver_email . ' not found' );
                    }
                }
            }
        }

        /**
         * @param WC_Order $order
         * @param $payment_status
         */
        public function update_commission_status( $order, $payment_status )
        {

            if( !YITH_PayPal_Adaptive_Payments_Integrations::is_multivendor_active() ) {
                
                $order_id = yit_get_prop( $order, 'id' );
                YITH_PADP_Receiver_Commission()->update_by_order( $order_id, strtolower( $payment_status ) );
            }
        }

        /**
         * show the gateway notices
         * @author YITHEMES
         * @since 1.0.0
         */
        public function show_gateway_notices()
        {
        	$show_admin_notices = apply_filters( 'yith_paypal_adaptive_payments_show_gateway_notices', true );
        	
        	if( 'yes' == $this->enabled  && $show_admin_notices ) {
         
                $error = array();


                if( empty( $this->username ) ) {
                    $error[] = __( 'API Username', 'yith-paypal-adaptive-payments-for-woocommerce' );
                }
                if( empty( $this->password ) ) {
                    $error[] = __( 'API Password', 'yith-paypal-adaptive-payments-for-woocommerce' );
                }

                if( empty( $this->signature ) ) {
                    $error[] = __( 'API SIGNATURE', 'yith-paypal-adaptive-payments-for-woocommerce' );
                }

                if( empty( $this->app_id ) ) {
                    $error[] = __( 'APPLICATION ID', 'yith-paypal-adaptive-payments-for-woocommerce' );
                }

                if( empty( $this->primary_receiver ) ) {
                    $error[] = __( 'Primary Receiver Email', 'yith-paypal-adaptive-payments-for-woocommerce' );
                }


                if( count( $error )>0 ) {

                    $message = implode( ', ', $error );
                    $options_message = _n( 'The follow option is empty', 'The follow options are empty', count( $error ), 'yith-paypal-adaptive-payments-for-woocommerce' );
                    $error_message = sprintf( '<div class="notice notice-error"><p><strong>%s</strong>, %s: %s</p></div>',
                        __( 'YITH PayPal Adaptive Payments for WooCommerce is disabled', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        $options_message,
                        $message
                    );

                    echo $error_message;
                }

                if( !$this->is_valid_for_use() ) {

                    $error_message = sprintf( '<div class="notice notice-error"><p><strong>%s</strong>, %s <a href="%s" target="_blank">%s</a> %s</p></div>',
                        __( 'YITH PayPal Adaptive Payments for WooCommerce is disabled', 'yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x( 'PayPal doesn\'t support your currency', 'As in the sentence "PayPal doesn\'t support your currency, see here for a complete list of supported currencies"',
                            'yith-paypal-adaptive-payments-for-woocommerce' ),
                        'https://developer.paypal.com/docs/classic/api/currency_codes/',
                        _x( 'see here', 'PayPal doesn\'t support your currency, see here for a complete list of supported currencies',
                            'yith-paypal-adaptive-payments-for-woocommerce' ),
                        _x( 'for a complete supported currency list', 'As in the sentence "PayPal doesn\'t support your currency, see here for a complete list of supported currencies"',
                            'yith-paypal-adaptive-payments-for-woocommerce' )
                    );

                    echo $error_message;
                }

            }
            do_action( 'yith_paypal_adaptive_payments_show_other_notices', $this->enabled );
        }

        /**
         * Get gateway icon.
         * @return string
         */
        public function get_icon()
        {
            $icon_html = '';
            $icon = (array)$this->get_icon_image( WC()->countries->get_base_country() );

            foreach ( $icon as $i ) {
                $icon_html .= '<img src="' . esc_attr( $i ) . '" alt="' . esc_attr__( 'PayPal Acceptance Mark', 'woocommerce' ) . '" />';
            }

            $icon_html .= sprintf( '<a href="%1$s" class="about_paypal" onclick="javascript:window.open(\'%1$s\',\'WIPaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700\'); return false;" title="' . esc_attr__( 'What is PayPal?', 'woocommerce' ) . '">' . esc_attr__( 'What is PayPal?', 'woocommerce' ) . '</a>', esc_url( $this->get_icon_url( WC()->countries->get_base_country() ) ) );

            return apply_filters( 'yith_paypal_adaptive_payments_for_woocommerce_gateway_icon', $icon_html, $this->id );
        }

        /**
         * Get the link for an icon based on country.
         * @param  string $country
         * @return string
         */
        protected function get_icon_url( $country )
        {
            $url = 'https://www.paypal.com/' . strtolower( $country );
            $home_counties = array( 'BE', 'CZ', 'DK', 'HU', 'IT', 'JP', 'NL', 'NO', 'ES', 'SE', 'TR' );
            $countries = array( 'DZ', 'AU', 'BH', 'BQ', 'BW', 'CA', 'CN', 'CW', 'FI', 'FR', 'DE', 'GR', 'HK', 'IN', 'ID', 'JO', 'KE', 'KW', 'LU', 'MY', 'MA', 'OM', 'PH', 'PL', 'PT', 'QA', 'IE', 'RU', 'BL', 'SX', 'MF', 'SA', 'SG', 'SK', 'KR', 'SS', 'TW', 'TH', 'AE', 'GB', 'US', 'VN' );

            if( in_array( $country, $home_counties ) ) {
                return $url . '/webapps/mpp/home';
            }
            else if( in_array( $country, $countries ) ) {
                return $url . '/webapps/mpp/paypal-popup';
            }
            else {
                return $url . '/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside';
            }
        }

        /**
         * Get PayPal images for a country.
         * @param  string $country
         * @return array of image URLs
         */
        protected function get_icon_image( $country )
        {
            switch ( $country ) {
                case 'US' :
                case 'NZ' :
                case 'CZ' :
                case 'HU' :
                case 'MY' :
                    $icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'TR' :
                    $icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_odeme_secenekleri.jpg';
                    break;
                case 'GB' :
                    $icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_mc_vs_ms_ae_UK.png';
                    break;
                case 'MX' :
                    $icon = array(
                        'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_visa_mastercard_amex.png',
                        'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_debit_card_275x60.gif'
                    );
                    break;
                case 'FR' :
                    $icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg';
                    break;
                case 'AU' :
                    $icon = 'https://www.paypalobjects.com/webstatic/en_AU/mktg/logo/Solutions-graphics-1-184x80.jpg';
                    break;
                case 'DK' :
                    $icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_PayPal_betalingsmuligheder_dk.jpg';
                    break;
                case 'RU' :
                    $icon = 'https://www.paypalobjects.com/webstatic/ru_RU/mktg/business/pages/logo-center/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'NO' :
                    $icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/banner_pl_just_pp_319x110.jpg';
                    break;
                case 'CA' :
                    $icon = 'https://www.paypalobjects.com/webstatic/en_CA/mktg/logo-image/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'HK' :
                    $icon = 'https://www.paypalobjects.com/webstatic/en_HK/mktg/logo/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'SG' :
                    $icon = 'https://www.paypalobjects.com/webstatic/en_SG/mktg/Logos/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'TW' :
                    $icon = 'https://www.paypalobjects.com/webstatic/en_TW/mktg/logos/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'TH' :
                    $icon = 'https://www.paypalobjects.com/webstatic/en_TH/mktg/Logos/AM_mc_vs_dc_ae.jpg';
                    break;
                case 'JP' :
                    $icon = 'https://www.paypal.com/ja_JP/JP/i/bnr/horizontal_solution_4_jcb.gif';
                    break;
                default :
                    $icon = WC_HTTPS::force_https_url( WC()->plugin_url() . '/includes/gateways/paypal/assets/images/paypal.png' );
                    break;
            }
            return apply_filters( 'yith_paypal_adaptive_payments_for_woocommerce_paypal_icon', $icon );


        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @param $key
         * @param $data
         * @return string
         */
        public function generate_custom_info_html( $key, $data )
        {
            $field_key = $this->get_field_key( $key );
            $defaults = array(
                'title' => '',
                'disabled' => false,
                'class' => '',
                'css' => '',
                'placeholder' => '',
                'type' => 'text',
                'desc_tip' => false,
                'description' => '',
                'custom_attributes' => array(),
            );

            $data = wp_parse_args( $data, $defaults );

            ob_start();
            ?>
            <tr valign="top">

                <td class="forminp custom_info" colspan="2" style="padding: 10px 27px;">
                    <span class="description"><?php echo( $data['description'] ); ?></span>
                </td>
            </tr>
            <?php

            return ob_get_clean();
        }

        /* public function process_refund( $order_id, $amount = null, $reason = '' )
         {
             $order = wc_get_order( $order_id );

             $order_pay_status = get_post_meta( $order_id, 'yith_payment_status', true );

             if( 'completed' == $order->get_status() && 'completed' == $order_pay_status && $amount == $order->get_total() ){

                 $payKey = get_post_meta( $order_id, 'yith_pay_key', true );
                 $body_params = array(
                     'payKey' => $payKey,
                     'requestEnvelope' => array(
                         'errorLanguage' => 'en_US',
                         'detailLevel' => 'ReturnAll'
                     )
                 );
                 $params = array(
                     'body' => json_encode( $body_params ),
                     'timeout' => 60,
                     'httpversion' => '1.1',
                     'headers' => $this->get_paypal_headers()
                 );

                 $api_url = $this->get_api_url();

                 $this->add_log( $this->id, 'Refund request for order ' . $order->get_order_number().' '.print_r( $params, true ) );

                 $response = wp_safe_remote_post( $api_url . 'Refund', $params );

                 if( is_wp_error( $response ) ){

                     $this->add_log( $this->id, 'Error during refund request '.print_r( $response->get_error_messages(),
        true ) );
                     new WP_Error( 'error', $response->get_error_message() );
                 }


                 $this->add_log( $this->id, 'Refund Details ' . print_r( $response['body'], true ) );
                 $response = json_decode( $response['body'], true );
                 $ack = strtolower( $response['responseEnvelope']['ack'] );

                 if( $ack == 'success' || $ack == 'successwithwarning' ){
                     $order->add_order_note( __('Order refunded', 'yith-paypal-adaptive-payments-for-woocommerce' ) );
                     update_post_meta( $order_id, 'yith_payment_status', 'refunded' );
                     YITH_PADP_Receiver_Commission()->update_by_order( $order_id, 'refunded' );
                     return true;
                 }

                 return false;
             }else{
                 return false;
             }
         }*/

    }
}

function YITH_Paypal_Adaptive_Payments_Gateway()
{
    return WC_Gateway_YITH_Paypal_Adaptive_Payments::get_instance();
}