<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Frontend {

    /**
     * Setup the class
     */
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Register the frontend hooks
     */
    public function register_hooks() {
        add_filter( 'body_class', array( $this, 'output_body_class' ) );
        add_action( 'wp_enqueue_scripts', array($this, 'frontend_styles') );

        // My Account
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'show_request_button' ), 10, 1 );
        add_filter( 'woocommerce_my_account_my_orders_actions', array($this, 'my_orders_request_button'), 10, 2 );

        add_filter( 'woocommerce_available_variation', array($this, 'add_variation_data'), 10, 3 );

        // Frontend form processing
        add_action( 'template_redirect', array($this, 'process_form_submission') );

        // Hide warranty notes from Recent Comments widget.
        add_filter( 'widget_comments_args', array( $this, 'hide_warranty_comments' ), 10, 1 );
    }

    /**
     * Add woocommerce CSS classes to the <body> element of the Warranty pages
     *
     * @param array $classes
     * @return array
     */
    public function output_body_class( $classes ) {
        if ( is_page( wc_get_page_id( 'warranty' ) ) ) {
            $classes[] = 'woocommerce';
            $classes[] = 'woocommerce-page';
        }

        return $classes;
    }

    /**
     * Register JS and CSS files
     */
    public function frontend_styles() {
        global $post;

        wp_enqueue_style( 'wc-form-builder', plugins_url('assets/css/form-builder.css', WooCommerce_Warranty::$plugin_file) );

        if ( $post ) {
            $product = wc_get_product( $post->ID );

            if ( $product && $product->is_type( 'variable' ) ) {
                wp_enqueue_script( 'wc-warranty-variables', plugins_url('assets/js/variables.js', WooCommerce_Warranty::$plugin_file), array('jquery') );
                wp_localize_script( 'wc-warranty-variables', 'WC_Warranty', array(
                    'currency_symbol'   => get_woocommerce_currency_symbol(),
                    'lifetime'          => __('Lifetime', 'wc_warranty'),
                    'no_warranty'       => __('No Warranty', 'wc_warranty'),
                    'free'              => __('Free', 'wc_warranty'),
                    'durations'         => array(
                        'day'      => __('Day', 'wc_warranty'),
                        'days'     => __('Days', 'wc_warranty'),
                        'week'     => __('Week', 'wc_warranty'),
                        'weeks'    => __('Weeks', 'wc_warranty'),
                        'month'    => __('Month', 'wc_warranty'),
                        'months'   => __('Months', 'wc_warranty'),
                        'year'     => __('Year', 'wc_warranty'),
                        'years'    => __('Years', 'wc_warranty')
                    )
                ) );
            }
        }

    }

    /**
     * Display the 'Request Warranty' button on the order view page if
     * an order contains a product with a valid warranty
     * @param $order WC_Order object
     */
    function show_request_button( $order ) {
        global $wpdb;

        if ( 'no' == get_option( 'warranty_show_rma_button', 'yes' ) ) {
            return;
        }

        if ( 'completed' === WC_Warranty_Compatibility::get_order_prop( $order, 'status' ) && Warranty_Order::order_has_warranty( $order ) ) {
            // If there is an existing warranty request, show a different text
            $requests = get_posts(array(
                'post_type' => 'warranty_request',
                'meta_query'    => array(
                    array(
                        'key'   => '_order_id',
                        'value' => WC_Warranty_Compatibility::get_order_prop( $order, 'id' ),
                    )
                )
            ));

            if (! $requests ) {
                $requests = array();
            }

            if ( count($requests) > 0 ) {
                $title = get_option( 'view_warranty_button_text', __('View Warranty Request', 'wc_warranty') );
            } else {
                $title = get_option( 'warranty_button_text', __('Request Warranty', 'wc_warranty') );
            }

            $page_id = get_option("woocommerce_warranty_page_id");
            $permalink = esc_url( add_query_arg( 'order', WC_Warranty_Compatibility::get_order_prop( $order, 'id' ), get_permalink( $page_id ) ) );
            echo '<a class="warranty-button button" href="'.$permalink.'">'.$title.'</a>';
        }
    }

    /**
     * Display the 'Request Warranty' button on the My Account page
     * @param  array $actions
     * @param  WC_Order $order
     * @return array $actions
     */
    function my_orders_request_button( $actions, $order ) {
        global $wpdb;

        if ( 'no' == get_option( 'warranty_show_rma_button', 'yes' ) ) {
            return $actions;
        }

        if ( 'completed' === WC_Warranty_Compatibility::get_order_prop( $order, 'status' ) && Warranty_Order::order_has_warranty( $order ) ) {
            // If there is an existing warranty request, show a different text
            $requests = get_posts(array(
                'post_type' => 'warranty_request',
                'meta_query'    => array(
                    array(
                        'key'   => '_order_id',
                        'value' => WC_Warranty_Compatibility::get_order_prop( $order, 'id' ),
                    )
                )
            ));

            if (! $requests ) {
                $requests = array();
            }

            if ( count($requests) > 0 ) {
                $title = get_option( 'view_warranty_button_text', __('View Warranty Status', 'wc_warranty'));
            } else {
                $title = get_option( 'warranty_button_text', __('Request Warranty', 'wc_warranty') );
            }

            $page_id = get_option("woocommerce_warranty_page_id");
            $permalink = esc_url( add_query_arg( 'order', WC_Warranty_Compatibility::get_order_prop( $order, 'id' ), get_permalink( $page_id ) ) );

            $actions['request_warranty'] = array('url' => $permalink, 'name' => $title);
        }

        return $actions;
    }

    /**
     * Add warranty data to all variations
     *
     * @param $data
     * @param $product
     * @param $variation
     *
     * @return mixed
     */
    function add_variation_data( $data, $product, $variation ) {
        $variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $variation->variation_id ) ) ? $variation->variation_id : $variation->get_id();
        $warranty   = warranty_get_product_warranty( $variation_id );

        $data['_warranty']          = $warranty;
        $data['_warranty_label']    = $warranty['label'];

        return $data;
    }

    /**
     * Capture and process frontend form submissions
     * @todo Split into methods
     */
    public function process_form_submission() {
        global $wpdb, $woocommerce;

        if ( isset($_REQUEST['req']) ) {
            $request = $_REQUEST['req'];

            if ( $request == 'new_warranty' ) {
                $order_id       = isset($_GET['order']) ? intval($_GET['order']) : false;
                $idxs           = $_GET['idx'];
                $_POST          = array_map('stripslashes_deep', $_POST);
                $request_type   = $_POST['warranty_request_type'];



                if ( $order_id && $idxs ) {
	                if ( ! is_user_logged_in() || ! current_user_can( 'view_order', intval( $order_id ) ) ) {
		                wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
	                }

                    $warranty_data  = array();
                    $order          = wc_get_order( $order_id );
                    $quantities     = $_POST['warranty_qty'];
                    $items          = $order->get_items();
                    $errors         = array();

                    if ( 'completed' === WC_Warranty_Compatibility::get_order_prop( $order, 'status' ) ) {
                        $products = array();
                        foreach ( $idxs as $i => $idx ) {
                            $products[] = !empty( $items[ $idx ]['variation_id'] )
                                ? $items[ $idx ]['variation_id']
                                : $items[ $idx ]['product_id'];
                        }

                        $request_id = warranty_create_request( array(
                            'type'          => $request_type,
                            'order_id'      => $order_id,
                            'product_id'    => $products,
                            'index'         => $idxs,
                            'qty'           => $quantities
                        ) );

	                    if ( 'yes' == get_option( 'warranty_show_tracking_field', 'no' ) ) {
		                    update_post_meta( $request_id, '_request_tracking_code', 'y' );

		                    if ( ! empty( $_POST['tracking_provider'] ) ) {
			                    update_post_meta( $request_id, '_tracking_provider', $_POST['tracking_provider'] );
		                    }

		                    if ( ! empty( $_POST['tracking_code'] ) ) {
			                    update_post_meta( $request_id, '_tracking_code', $_POST['tracking_code'] );
		                    }
	                    }

                        // save the custom forms
                        $result = WooCommerce_Warranty::process_warranty_form( $request_id );

                        if ( is_wp_error($result) ) {
                            wp_delete_post( $request_id, true );

                            $errors = $result->get_error_messages();
                        } else {
                            // set the initial status and send the emails
                            warranty_update_status( $request_id, 'new' );
                        }

                        if ( empty( $errors ) ) {
                            $back   = get_permalink( get_option('woocommerce_warranty_page_id') );
                            $back   = add_query_arg( 'order', $order_id, $back );
                            $back   = add_query_arg( 'updated', urlencode(__('Request(s) sent', 'wc_warranty')), $back );

                            wp_redirect( $back );
                            exit;
                        } else {
                            $back   = get_permalink( wc_get_page_id('warranty') );
                            $back   = add_query_arg( array(
                                'order'         => $order_id,
                                'request_id'    => $request_id,
                                'errors'        => urlencode( wp_json_encode( $errors ) )
                            ), $back );

                            if (! empty($idxs) ) {
                                foreach ( $idxs as $idx ) {
                                    $back = add_query_arg( 'idx[]', $idx, $back );
                                }
                            }

                            wp_redirect( $back );
                            exit;
                        }
                    } else {
                        $request_id = new WP_Error( 'wc_warranty', __('Order does not have a valid warranty', 'wc_warranty') );

                        $result = $request_id;
                        $error  = $result->get_error_message( 'wc_warranty' );
                        $back   = get_permalink( wc_get_page_id('warranty') );
                        $back   = add_query_arg( array(
                            'order'         => $order_id,
                            'error'         => urlencode( $error )
                        ), $back );

                        wp_redirect( $back );
                        exit;
                    }
                }
            } elseif ( $request == 'new_return' ) {
                $_POST = array_map('stripslashes_deep', $_POST);

                $return_id      = $_POST['return'];
                $order_id       = $_POST['order_id'];
                $product_name   = $_POST['product_name'];
                $first_name     = $_POST['first_name'];
                $last_name      = $_POST['last_name'];
                $email          = $_POST['email'];

                $warranty = array(
                    'post_content'  => '',
                    'post_name'     => __('Return Request for Order #', 'wc_warranty') . $order_id,
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                    'post_type'     => 'warranty_request'
                );
                $request_id = wp_insert_post( $warranty );

                $metas = array(
                    'order_id'      => $order_id,
                    'product_id'    => 0,
                    'product_name'  => $product_name,
                    'answer'        => '',
                    'attachment'    => '',
                    'code'          => warranty_generate_rma_code(),
                    'first_name'    => $first_name,
                    'last_name'     => $last_name,
                    'email'         => $email
                );

                foreach ( $metas as $key => $value ) {
                    add_post_meta( $request_id, '_'.$key, $value, true );
                }

                $status = WooCommerce_Warranty::process_warranty_form( $request_id );

                warranty_update_status( $request_id, 'new' );

                if ( is_wp_error( $status ) ) {
                    wp_delete_post( $request_id, true );

                    foreach ( $status->get_error_messages() as $error ) {
                        wc_add_notice( $error, 'error' );
                    }
                } else {
                    if ( function_exists('wc_add_notice') ) {
                        wc_add_notice(__('Return request submitted successfully', 'wc_warranty'));
                    } else {
                        $woocommerce->add_message(__('Return request submitted successfully', 'wc_warranty'));
                    }

                    wp_redirect( get_permalink($return_id) );
                    exit;
                }
            }
        }

        if ( isset($_REQUEST['action']) ) {
            if ( $_REQUEST['action'] == 'set_tracking_code' ) {
                $request_id = $_REQUEST['request_id'];
                $code       = $_REQUEST['tracking_code'];
                $provider   = isset($_REQUEST['tracking_provider']) ? $_REQUEST['tracking_provider'] : '';

                update_post_meta( $request_id, '_tracking_code', $code );

                if (! empty($provider) ) {
                    update_post_meta( $request_id, '_tracking_provider', $provider );
                }

                $request = warranty_load($request_id);

                $back   = get_permalink( get_option('woocommerce_warranty_page_id') );
                $back   = add_query_arg( 'order', $request['order_id'], $back );
                $back   = add_query_arg( 'updated', urlencode(__('Tracking codes updated', 'wc_warranty')), $back );

                wp_redirect( $back );
                exit;
            }
        }
    }

    /**
     * Adds to Recent Comments widget query to filter out warranty notes.
     *
     * @since 1.8.13
     *
     * @param array $comment_query_args An array of arguments used to retrieve the recent comments.
     *
     * @return array Modified array of arguments.
     */
    public function hide_warranty_comments( $comment_query_args ) {
        if ( isset( $comment_query_args['type__not_in'] ) ) {
            $comment_query_args['type__not_in'][] = 'wc_warranty_note';
        } else {
            $comment_query_args['type__not_in'] = array( 'wc_warranty_note' );
        }
        return $comment_query_args;
    }

}

new Warranty_Frontend();
