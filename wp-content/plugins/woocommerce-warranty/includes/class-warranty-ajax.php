<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Ajax {

    /**
     * Hook in the AJAX events
     */
    public static function init() {
        // warranty_EVENT => nopriv
        $events = array(
            'user_search'               => false,
            'search_for_email'          => false,
            'update_request_fragment'   => false,
            'update_request_status'     => false,
            'delete_request'            => false,
            'add_note'                  => false,
            'delete_note'               => false,
            'request_tracking'          => false,
            'set_tracking'              => false,
            'update_inline'             => false,
            'return_inventory'          => false,
            'refund_item'               => false,
            'send_coupon'               => false,
            'product_warranty_update'   => false,
            'update_category_defaults'  => false,
            'migrate_products'          => false,
        );

        foreach ( $events as $event => $nopriv ) {
            add_action( 'wp_ajax_warranty_' . $event, array( __CLASS__, $event ) );

            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_warranty_' . $event, array( __CLASS__, $event ) );
            }
        }
    }

    /**
     * AJAX handler for searching for users
     *
     * This method looks for partial user_email and/or user ID matches,
     * formatted as an array of unique customer keys with values being formed as:
     *
     *     first_name last_name <user_email>
     *
     * The resulting array is then JSON-encoded before it is sent back
     *
     */
    public static function user_search() {
        global $wpdb;
        $term       = stripslashes($_GET['term']);
        $results    = array();
        $all_users  = array();

        if ( is_numeric( $term ) ) {
            $results = $wpdb->get_results( $wpdb->prepare("
                SELECT *
                FROM {$wpdb->users}
                WHERE ID LIKE %s",
                $term .'%'
            ) );
        } else {
            $results = $wpdb->get_results( $wpdb->prepare("
                SELECT *
                FROM {$wpdb->users}
                WHERE user_email LIKE %s
                OR user_login LIKE %s
                OR display_name LIKE %s",
                '%'. $term .'%',
                '%'. $term .'%',
                '%'. $term .'%'
            ) );
        }

        if ( $results ) {
            foreach ( $results as $result ) {
                $all_users[ $result->ID ] = $result->display_name .' (#'. $result->ID .')';
            }
        }

        // Suppress errors as this table may not exist if they don't use the follow-up-emails extension.
        $wpdb->suppress_errors();

        // guest email search
        $results2 = $wpdb->get_results( $wpdb->prepare("
            SELECT id, email_address
            FROM {$wpdb->prefix}followup_customers
            WHERE user_id = 0
            AND email_address LIKE %s",
            '%'. $term .'%'
        ));

        $wpdb->suppress_errors( false );

        if ( $results2 ) {
            foreach ( $results2 as $result ) {
                $all_users[ $result->email_address ] = $result->email_address .' (Guest #'. $result->id .')';
            }
        }

        $all_users = apply_filters( 'warranty_user_search', $all_users, $term );

        wp_send_json( $all_users );
    }

    /**
     * AJAX handler for searching for existing email addresses
     *
     * This method looks for partial user_email and/or display_name matches,
     * as well as fuzzy first_name and last_name matches. The results are
     * formatted as an array of unique customer keys with values being formed as:
     *
     *     first_name last_name <user_email>
     *
     * The resulting array is then JSON-encoded before it is sent back
     *
     */
    public static function search_for_email() {
        global $wpdb;
        $term       = stripslashes( $_GET['term'] );
        $results    = array();
        $all_emails = array();

        // Registered users
        $email_term = $term . '%';
        $name_term  = '%' . $term . '%';

        $email_results = $wpdb->get_results( $wpdb->prepare(
            "SELECT DISTINCT u.ID, u.display_name, u.user_email
			FROM {$wpdb->prefix}users u
			WHERE (
				user_email LIKE %s OR display_name LIKE %s
			)",
            $email_term,
            $name_term
        ) );

        if ( $email_results ) {
            foreach ( $email_results as $result ) {
                $all_emails[] = $result->user_email;

                $first_name = get_user_meta( $result->ID, 'billing_first_name', true );
                $last_name  = get_user_meta( $result->ID, 'billing_last_name', true );

                if ( empty( $first_name ) && empty( $last_name ) ) {
                    $first_name = $result->display_name;
                }

                $results[ $result->user_email ] = $first_name . ' ' . $last_name . ' &lt;' . $result->user_email . '&gt;';
            }
        }

        // Full name (First Last format)
        $name_results = $wpdb->get_results(
            "SELECT DISTINCT m1.user_id, u.user_email, m1.meta_value AS first_name, m2.meta_value AS last_name
			FROM {$wpdb->prefix}users u, {$wpdb->prefix}usermeta m1, {$wpdb->prefix}usermeta m2
			WHERE u.ID = m1.user_id
			AND m1.user_id = m2.user_id
			AND m1.meta_key =  'first_name'
			AND m2.meta_key =  'last_name'
			AND CONCAT_WS(  ' ', m1.meta_value, m2.meta_value ) LIKE  '%{$term}%'"
        );

        if ( $name_results ) {
            foreach ( $name_results as $result ) {
                if ( in_array( $result->user_email, $all_emails ) ) {
                    continue;
                }

                $all_emails[]   = $result->user_email;
                $results[ $result->user_email ] = $result->first_name . ' ' . $result->last_name . ' &lt;' . $result->user_email . '&gt;';
            }
        }

        $results = apply_filters( 'warranty_email_query', $results, $term, $all_emails );

        wp_send_json( $results );
    }

    /**
     * Update a fragment of a warranty request
     */
    public static function update_request_fragment() {
        $post       = array_map( 'stripslashes_deep', $_REQUEST );
        $type       = $post['type'];
        $message    = '';

        if ( $type == 'change_status' ) {
            $new_status = $post['status'];
            $request_id = $post['request_id'];

            warranty_update_request( $request_id, array('status' => $new_status) );

            $message = __('Request status updated', 'wc_warranty');
        } elseif ( $type == 'generate_rma' ) {
            // using GET
            $request_id = $post['request_id'];
            $code       = warranty_generate_rma_code();

            warranty_update_request( $request_id, array('code' => $code) );

            $message = __('RMA Code generated successfully', 'wc_warranty');
        } elseif ( $type == 'request_code' ) {
            $request_id = $post['request_id'];

            warranty_update_request( $request_id, array('request_tracking_code' => 'y') );

            warranty_send_emails( $request_id, 'request_tracking' );

            $message = __('Tracking code requested', 'wc_warranty');
        } elseif ( $type == 'set_return_tracking' ) {
            $request_id = $post['request_id'];
            $provider   = isset($post['return_tracking_provider']) ? $post['return_tracking_provider'] : false;
            $code       = $post['return_tracking_code'];

            $data['return_tracking_code'] = $code;

            if ( false !== $provider ) {
                $data['return_tracking_provider'] = $provider;
            }

            warranty_update_request( $request_id, $data );

            $message = __('Return tracking code updated', 'wc_warranty');
        }

        if ( $message ) {
            $return = 'admin.php?page=warranties&updated='. urlencode( $message );
            die( $return );
        }
    }

    /**
     * Update a request's status and return the available actions
     * based on the new status
     */
    public static function update_request_status() {
        check_admin_referer( 'warranty_update_status' );

        $new_status = $_POST['status'];
        $request_id = absint($_POST['id']);

        warranty_update_request( $request_id, array('status' => $new_status) );

        wp_send_json(array(
            'status'    => 'OK',
            'message'   => __('Status updated', 'wc_warranty'),
            'actions'   => Warranty_Admin::get_warranty_actions( $request_id, true )
        ));
    }

    /**
     * Handle delete requests
     */
    public static function delete_request() {
        check_admin_referer( 'warranty_delete' );

        $id = absint($_REQUEST['id']);

        wp_delete_post( $id, true );

        die(1);
    }

    /**
     * Add a comment to a request
     */
    public static function add_note() {
        $request_id = absint( $_REQUEST['request'] );
        $user       = wp_get_current_user();
        $note       = stripslashes( $_REQUEST['note'] );

        $data = array(
            'comment_post_ID' => $request_id,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'comment_author_url'    => '',
            'comment_content' => $note,
            'comment_type' => 'wc_warranty_note',
            'comment_parent' => 0,
            'user_id' => $user->ID,
            'comment_date' => current_time('mysql'),
            'comment_approved' => 1,
        );

        wp_new_comment($data);

        ob_start();
        include WooCommerce_Warranty::$base_path .'templates/list-item-notes.php';
        $list = ob_get_clean();

        die( $list );
    }

    /**
     * Delete a request note
     */
    public static function delete_note() {
        $request_id = absint( $_REQUEST['request'] );
        $note       = absint( $_REQUEST['note_id'] );

        wp_delete_comment( $note, true );

        ob_start();
        include WooCommerce_Warranty::$base_path .'templates/list-item-notes.php';
        $list = ob_get_clean();

        die( $list );
    }

    /**
     * Send a tracking request to the customer
     */
    public static function request_tracking() {
        ob_start();

        $request_id = absint( $_POST['id'] );
        warranty_send_tracking_request( $request_id );

        ob_end_clean();

        wp_send_json(array('status' => 'OK'));
    }

    /**
     * Set the return shipping details
     */
    public static function set_tracking() {
        ob_start();

        $request_id = absint( $_POST['id'] );
        $provider   = !empty($_POST['return_tracking_provider']) ? $_POST['return_tracking_provider'] : false;
        $code       = $_POST['tracking'];

        $data['return_tracking_code'] = $code;

        if ( false !== $provider ) {
            $data['return_tracking_provider'] = $provider;
        }

        warranty_update_request( $request_id, $data );

        ob_end_clean();

        wp_send_json(array(
            'status' => 'OK',
            'message' => __('Shipping tracking details saved', 'wc_warranty')
        ));
    }

    /**
     * Update an RMA request from the shop order screen via AJAX
     */
    public static function update_inline() {
        $post   = stripslashes_deep( $_POST );
        $id     = absint( $post['id'] );
        $rma    = warranty_load( $id );
        $data   = array();

        if ( !wp_verify_nonce( $post['_wpnonce'], 'warranty_update' ) ) {
            wp_send_json(array(
                'status'    => 'ERROR',
                'message'   => 'Invalid referrer. Please reload the page and try again'
            ));
        }

        if ( !empty( $post['status'] ) && $post['status'] != $rma['status'] ) {
            $data['status'] = $post['status'];
        }

        if ( !empty( $post['shipping_label_image_id'] ) ) {
            $data['warranty_shipping_label'] = $post['shipping_label_image_id'];
        }

        if ( isset( $post['return_tracking_code'] ) ) {
            $data['return_tracking_code'] = $post['return_tracking_code'];
        }

        if ( !empty( $post['return_tracking_provider'] ) ) {
            $data['return_tracking_provider'] = $post['return_tracking_provider'];
        }

        if ( !empty( $data ) ) {
            warranty_update_request( $id, $data );
        }

        if ( !empty( $post['request_tracking'] ) ) {
            warranty_send_tracking_request( $id );
        }

        wp_send_json(array(
            'status'    => 'OK',
            'message'   => __('RMA request updated', 'wc_warranty'),
            'actions'   => Warranty_Admin::get_warranty_actions( $id, true )
        ));
    }

    /**
     * Return the stock if stock management is enabled
     */
    public static function return_inventory() {
        check_admin_referer( 'warranty_return_inventory' );

        $request_id    = absint( $_REQUEST['id'] );

        warranty_return_product_stock( $request_id );

        warranty_update_request( $request_id, array( 'returned' => 'yes' ) );

        wp_send_json(array(
            'status'    => 'OK',
            'message'   => __('Product stock returned', 'wc_warranty')
        ));
    }

    /**
     * Process refund requests
     */
    public static function refund_item() {
        check_admin_referer( 'warranty_update' );

        $request_id = absint( $_REQUEST['id'] );
        $amount     = !empty( $_REQUEST['amount'] ) ? $_REQUEST['amount'] : null;

        $refund = warranty_refund_item( $request_id, $amount );

        if ( is_wp_error( $refund ) ) {
            wp_send_json( array(
                'status'    => 'ERROR',
                'message'   => $refund->get_error_message()
            ) );
        } else {
            wp_send_json( array(
                'status'    => 'OK',
                'message'   => __('Item marked as Refunded', 'wc_warranty')
            ) );
        }
    }

    /**
     * Send coupon as a refund
     */
    public static function send_coupon() {
        Warranty_Coupons::send_coupon();
    }

    /**
     * Update a product's warranty details and return the new warranty string/description
     */
    public static function product_warranty_update() {
        $post   = stripslashes_deep( $_POST );
        $id     = $post['id'];
        $type   = (isset($post['warranty_type'])) ? $post['warranty_type'] : array();
        $label  = (isset($post['warranty_label'])) ? $post['warranty_label'] : array();
        $default= (isset($post['warranty_default'])) ? $post['warranty_default'] : array();

        $warranty = array();

        if ( !empty( $default[ $id ] ) && $default[ $id ] == 'yes' ) {
            // skip
            delete_post_meta( $id, '_warranty' );
            $string = warranty_get_warranty_string( $id );

            wp_send_json(array(
                'ack' => 'OK',
                'string' => $string
            ));
        }

        if ( $type[$id] == 'no_warranty' ) {
            $warranty = array('type' => 'no_warranty');
            update_post_meta( $id, '_warranty', $warranty );
        } elseif ( $type[$id] == 'included_warranty' ) {
            $warranty = array(
                'type'      => 'included_warranty',
                'length'    => $post['included_warranty_length'][$id],
                'value'     => $post['limited_warranty_length_value'][$id],
                'duration'  => $post['limited_warranty_length_duration'][$id]
            );
            update_post_meta( $id, '_warranty', $warranty );
        } elseif ( $type[$id] == 'addon_warranty' ) {
            $no_warranty= (isset($post['addon_no_warranty'][$id])) ? $post['addon_no_warranty'][$id] : 'no';
            $amounts    = (isset($post['addon_warranty_amount'][$id])) ? $post['addon_warranty_amount'][$id] : array();
            $values     = (isset($post['addon_warranty_length_value'][$id])) ? $post['addon_warranty_length_value'][$id] : array();
            $durations  = (isset($post['addon_warranty_length_duration'][$id])) ? $post['addon_warranty_length_duration'][$id] : array();
            $addons     = array();

            for ($x = 0; $x < count($amounts); $x++) {
                if (!isset($amounts[$x]) || !isset($values[$x]) || !isset($durations[$x])) continue;

                $addons[] = array(
                    'amount'    => $amounts[$x],
                    'value'     => $values[$x],
                    'duration'  => $durations[$x]
                );
            }

            $warranty = array(
                'type'                  => 'addon_warranty',
                'addons'                => $addons,
                'no_warranty_option'    => $no_warranty
            );
            update_post_meta( $id, '_warranty', $warranty );
        }

        if ( isset($post['warranty_label'][$id]) ) {
            update_post_meta( $id, '_warranty_label', $post['warranty_label'][$id] );
        }

        $string = warranty_get_warranty_string( $id );
        wp_send_json(array(
            'ack' => 'OK',
            'string' => $string
        ));
    }

    public static function update_category_defaults() {
        $warranties = Warranty_Settings::get_category_warranties_from_post();
        update_option( 'wc_warranty_categories', $warranties );

        $default_warranty   = warranty_get_default_warranty();
        $categories         = get_terms( 'product_cat', array( 'hide_empty' => false ) );
        $strings            = array();

        foreach ( $categories as $category ) {
            $category_id = $category->term_id;
            $warranty    = isset( $warranties[ $category_id ] ) ? $warranties[ $category_id ] : array();

            if ( empty( $warranty ) ) {
                $warranty = $default_warranty;
            }

            $default = isset( $warranty['default'] ) ? $warranty['default'] : false;

            $strings[ $category_id ] = ($default) ? '<em>Default warranty</em>' : warranty_get_warranty_string( 0, $warranty );
        }

        wp_send_json($strings);
    }

    /**
     * Move RMA products into the new wc_warranty_products table
     */
    public static function migrate_products() {
        global $wpdb;

        set_time_limit(0);

        // We need to turn off the object cache temporarily while we deal with transients,
        // as a workaround to a W3 Total Cache object caching bug
        global $_wp_using_ext_object_cache;

        $_wp_using_ext_object_cache_previous = $_wp_using_ext_object_cache;
        $_wp_using_ext_object_cache = false;

        if ( empty( $_POST['cmd'] ) ) {
            wp_send_json( array( 'error' => 'CMD is missing' ) );
        }

        $cmd        = $_POST['cmd'];
        $session    = !empty($_POST['update_session']) ? $_POST['update_session'] : '';

        if ( $cmd == 'start' ) {
            // count the total number of RMA to scan

            // generate a new session id
            $session = time();

            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'warranty_request'");

            set_transient( 'warranty_migrate_products_page', 1 );

            // re-enable caching if it was previously enabled
            $_wp_using_ext_object_cache = $_wp_using_ext_object_cache_previous;

            wp_send_json( array(
                'update_session'=> $session,
                'total_items'   => $count
            ) );
        } else {
            ob_start();

            $page       = get_transient( 'warranty_migrate_products_page' );
            $limit      = 10;
            $results    = array();

            if ( !$page ) {
                $page = 1;
            }

            $items = get_posts(array(
                'post_type'         => 'warranty_request',
                'paged'             => $page,
                'posts_per_page'    => $limit,
                'fields'            => 'ids'
            ));

            if ( empty( $items ) ) {
                $status = 'completed';
            } else {
                foreach ( $items as $request_id ) {
                    $item = array(
                        'request_id'    => $request_id,
                        'product_id'    => get_post_meta( $request_id, '_product_id', true ),
                        'order_item_index'  => get_post_meta( $request_id, '_index', true ),
                        'quantity'      => get_post_meta( $request_id, '_qty', true )
                    );

                    $wpdb->insert( $wpdb->prefix .'wc_warranty_products', $item );

                    $results[] = array(
                        'id'        => $request_id,
                        'status'    => 'success'
                    );
                }

                $page++;
                set_transient( 'warranty_migrate_products_page', $page );
                $status = 'partial';
            }

            ob_clean();

            // re-enable caching if it was previously enabled
            $_wp_using_ext_object_cache = $_wp_using_ext_object_cache_previous;

            wp_send_json( array(
                'status'            => $status,
                'update_data'       => $results,
                'session'           => $session
            ) );
        }
    }

}

Warranty_Ajax::init();
