<?php
/* * class
 * Description of A2W_WooCommerceOrderListController
 *
 * @author MA_GROUP
 *
 * @autoload: a2w_admin_init
 *
 * @ajax: true
 */
if (!class_exists('A2W_WooCommerceOrderListController')) {

    class A2W_WooCommerceOrderListController
    {

        private $bulk_actions = array();
        private $bulk_actions_text = array();

        public function __construct()
        {
            add_filter("bulk_actions-edit-shop_order", array($this, 'bulk_actions'));

            add_action('admin_enqueue_scripts', array($this, 'assets'));

            add_action('a2w_install', array($this, 'install'));

            add_filter('woocommerce_admin_order_actions', array($this, 'admin_order_actions'), 2, 100);

            add_action('manage_posts_extra_tablenav', array($this, 'add_bulk_order_sunc_button'));

            add_action('wp_ajax_a2w_order_info', array($this, 'ajax_order_info'));

            add_action('wp_ajax_a2w_get_fulfilled_orders', array($this, 'ajax_get_fulfilled_orders'));

            add_action('wp_ajax_a2w_save_tracking_code', array($this, 'ajax_save_tracking_code'));

            //two methods below are shared with the tracking plugin
            add_action('manage_shop_order_posts_custom_column', array($this, 'manage_columns_data'));
            add_filter('manage_edit-shop_order_columns', array($this, 'manage_columns_headers'));

            list($this->bulk_actions, $this->bulk_actions_text) = apply_filters('a2w_wcol_bulk_actions_init', array($this->bulk_actions, $this->bulk_actions_text));
        }

        public function bulk_actions($actions)
        {
            foreach ($this->bulk_actions as $action) {
                $actions[$action] = $this->bulk_actions_text[$action];
            }

            return $actions;
        }

        public function install()
        {
            $user = wp_get_current_user();
            $page = "edit-shop_order";
            $hidden = array("billing_address");
            update_user_option($user->ID, "manage{$page}columnshidden", $hidden, true);
        }

        public function add_bulk_order_sunc_button()
        {
            if (isset($_GET['post_type']) && $_GET['post_type'] == "shop_order") {
                $woocommerce_model = new A2W_Woocommerce();
                $fulfilled_order_count = $woocommerce_model->get_fulfilled_orders_count();
                if ($fulfilled_order_count > 0) {
                ?>
                    <div class="alignleft actions">
                        <?php submit_button(__("SYNC ALL with AliExpress", 'ali2woo'), 'primary', 'a2w_bulk_order_sync_manual', false);?>
                        <h2 style="display: inline-block; margin-top: 3px;"><?php echo wc_help_tip("It allows you to get and update tracking data for all orders via the Ali2Woo Chrome extension."); ?></h2>
                    </div>
                <?php
                }
            }
        }

        public function ajax_get_fulfilled_orders()
        {

            $woocommerce_model = new A2W_Woocommerce();
            $result = A2W_ResultBuilder::buildOk(array('data' => $woocommerce_model->get_fulfilled_orders_data()));

            echo json_encode($result);
            wp_die();
        }

        public function ajax_save_tracking_code()
        {

            $order_id = intval($_POST['id']);
            $ext_id = intval($_POST['ext_id']);
            $tracking_codes = isset($_POST['tracking_codes']) ? $_POST['tracking_codes'] : array();
            $carrier_name = isset($_POST['carrier_name']) ? $_POST['carrier_name'] : '';
            $carrier_url = isset($_POST['carrier_url']) ? $_POST['carrier_url'] : '';
            $tracking_status = isset($_POST['tracking_status']) ? $_POST['tracking_status'] : '';

            $woocommerce_model = new A2W_Woocommerce();
            $result = $woocommerce_model->save_tracking_code($order_id, $ext_id, $tracking_codes, $carrier_name, $carrier_url, $tracking_status);

            if (!$result) {
                $result = A2W_ResultBuilder::buildError(_x('Didn`t find the Woocommerce order №', 'Error text', 'ali2woo') . $order_id);
            } else {
                $result = A2W_ResultBuilder::buildOk();
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_order_info()
        {

            $result = array("state" => "ok", "data" => "");

            $order_id = isset($_POST['id']) ? $_POST['id'] : false;

            if (!$order_id) {
                $result['state'] = 'error';
                echo json_encode($result);
                wp_die();
            }

            $content = array();

            $order = wc_get_order($order_id);

            $items = $order->get_items();

            // $order_external_id_array = get_post_meta($order->get_id(), A2W_Constants::old_order_external_order_id());
            // $order_tracking_codes = get_post_meta($order->get_id(), A2W_Constants::old_order_tracking_code());

            $k = 1;

            foreach ($items as $item) {

                $a2w_order_item = new A2W_WooCommerceOrderItem($item);

                $product_name = $a2w_order_item->get_name();
                $product_id = $a2w_order_item->get_product_id();

                $tmp = '';

                if ($product_id > 0) {
                    $product_url = get_post_meta($product_id, '_a2w_product_url', true);
                    $seller_url = get_post_meta($product_id, '_a2w_seller_url', true);

                    $shipping_info = $item->get_meta(A2W_Shipping::get_order_item_shipping_meta_key());
                    $shipping_code = $item->get_meta(A2W_Shipping::get_order_item_legacy_shipping_meta_key());

                    if ($product_url) {
                        $tmp = $k . '). <a title="' . $product_name . '" href="' . $product_url . '" target="_blank" class="link_to_source product_url">' . _x('Product page', 'hint', 'ali2woo') . '</a>';
                    }

                    if ($seller_url) {
                        $tmp .= "<span class='seller_url_block'> | <a href='" . $seller_url . "' target='_blank' class='seller_url'>" . _x('Seller', 'hint', 'ali2woo') . "</a></span>";
                    }

                    if ($shipping_info) {
                        $display_value = A2W_Shipping::get_formated_shipping_info_meta($order->get_id(), $shipping_info);
                        $tmp .= '<span class="seller_url_block"> | ' . $display_value . '</span>';
                    } else if ($shipping_code) {
                        $tmp .= '<span class="seller_url_block"> | ' . $shipping_code . '</span>';
                    }

                    // $external_order_id = $a2w_order_item->get_external_order_id();

                    $ali_order_link = $a2w_order_item->get_ali_order_item_link();

                    if ($ali_order_link) {
                        $tmp .= " | " . __('AliExpress order ID', 'ali2woo') . ": <span class='seller_url_block'>" . $ali_order_link . "</span>";

                        $order_item_tracking_codes = $a2w_order_item->get_formated_tracking_codes();

                        if ($order_item_tracking_codes !== "") {
                            $tmp .= " | " . __('Tracking numbers', 'ali2woo') . ": <span class='seller_url_block'>" . $order_item_tracking_codes . "</span>";
                        }
                    }

                } else {
                    $tmp .= $k . '). <span style="color:red;">' . _x('The product has been deleted', 'hint', 'ali2woo') . '</span>';
                }

                $content[] = $tmp;
                $k++;
            }

            /*  if (!empty($order_external_id_array) && isset($order_external_id_array[0]) && !empty($order_external_id_array[0]) ){
            $content[] = "AliExpress order ID(s): <span class='seller_url_block'>" . implode(", ", $order_external_id_array). "</span>";
            }

            if (!empty($order_tracking_codes)) {
            $content[] = "Tracking number(s): <span class='seller_url_block'>" . (is_array($order_tracking_codes) ? implode(", ", $order_tracking_codes) : strval($order_tracking_codes)) . "</span>";
            }*/

            $content = apply_filters('a2w_get_order_content', $content, $order_id);
            $result['data'] = array('content' => $content, 'id' => $order_id);

            echo json_encode($result);
            wp_die();
        }

        public function assets()
        {
            wp_enqueue_style('a2w-wc-ol-style', A2W()->plugin_url() . '/assets/css/wc_ol_style.css', array(), A2W()->version);
            wp_enqueue_script('a2w-wc-ol-script', A2W()->plugin_url() . '/assets/js/wc_ol_script.js', ['jquery-ui-core', 'jquery-ui-dialog'], A2W()->version);

            $lang_data = array(
                'aliexpress_info' => _x('AliExpress Info', 'Dialog title', 'ali2woo'),
                'please_wait_data_loads' => _x('Please wait, data loads...', 'Status', 'ali2woo'),
                'please_wait' => _x('Please wait...', 'Status', 'ali2woo'),
                'sync_process' => _x('Sync process', 'Status', 'ali2woo'),
                'sync_done' => _x('Sync done! Click "Refresh page" to see updated tracking data in your orders.', 'Button text', 'ali2woo'),
                'error' => _x('Error!', 'Button text', 'ali2woo'),
                'error_please_install_new_extension' => _x('Error! Please install the latest Chrome extension.', 'Error text', 'ali2woo'),
                'error_cant_do_tracking_sync' => _x('Can`t get tracking data. Unknown error in the Chrome extension. Please contact our support.', 'Error text', 'ali2woo'),
                'try_again' => _x('Try again?', 'Button text', 'ali2woo'),
                'error_didnt_do_find_alix_order_num' => _x('Didn`t find the AliExpress order № ', 'Error text', 'ali2woo'),
                'error_cant_do_tracking_sync_login_to_account' => _x('Can`t get tracking data. Please log-in to your AliExpress account first.', 'Error text', 'ali2woo'),
                'no_tracking_codes_for_order' => _x('No tracking numbers for the AliExpress order № ', 'Status', 'ali2woo'),
                'tracking_sync' => _x('SYNC ALL with AliExpress', 'Button text', 'ali2woo'),
                'error_403_code' => _x('Please log in your AliExpress account to get tracking data for the AliExpress order № ', 'Error text', 'ali2woo'),
                'tracking_sync_done' => _x('Tracking data is updated for the AliExpress order № ', 'Status', 'ali2woo'),
                'no_orders_for_synch' => _x('At the moment there are no orders available for synchronization. Please note: the plugin updates ONLY not delivered orders with filled AliExpress Order ID.', 'Status', 'ali2woo'),
                'bulk_order_sync' => _x('Bulk Order Sync', 'Popup title', 'ali2woo'),
                'order_sync' => _x('Order Sync', 'Popup title', 'ali2woo'),
            );

            wp_localize_script('a2w-wc-ol-script', 'a2w_script_data', array('lang' => $lang_data));
        }

        public function manage_columns_data($name)
        {
            global $post;

            switch ($name) {
                case 'tracking_code':

                    $order = wc_get_order($post->ID);
                    $order_items = $order->get_items();

                    $result = array();

                    foreach ($order_items as $item) {
                        $a2w_order_item = new A2W_WooCommerceOrderItem($item);

                        $tracking_number = $a2w_order_item->get_formated_tracking_codes();

                        if ($tracking_number) {
                            $result[] = $tracking_number;
                        }
                    }

                    if (!empty($result)) {
                        echo implode(",", $result);
                    } else {
                        _e('Not available yet', 'ali2woo');
                    }

                    break;

                case 'aliexpress_order':

                    $order = wc_get_order($post->ID);
                    $order_items = $order->get_items();

                    $result = array();

                    foreach ($order_items as $item) {
                        $a2w_order_item = new A2W_WooCommerceOrderItem($item);

                        //  $ali_order_id = $a2w_order_item->get_external_order_id();

                        $ali_order_link = $a2w_order_item->get_ali_order_item_link();

                        if ($ali_order_link) {
                            $result[] = $ali_order_link;
                        }
                    }

                    if (!empty($result)) {
                        echo implode(",", $result);
                    } else {
                        _e('Not available yet', 'ali2woo');
                    }

                    break;

            }
        }

        public function manage_columns_headers($columns)
        {
            $new_columns = array();

            foreach ($columns as $column_name => $column_info) {

                $new_columns[$column_name] = $column_info;

                if ('order_total' === $column_name) {
                    $new_columns['tracking_code'] = __('Tracking numbers', 'ali2woo');
                    $new_columns['aliexpress_order'] = __('AliExpress Order ID', 'ali2woo');
                }
            }

            return $new_columns;
        }

        public function admin_order_actions($actions, $order)
        {

            $actions['a2w_order_fulfillment'] = array(
                'url' => '#' . $order->get_id(),
                'name' => __('Order fulfillment', 'ali2woo'),
                'action' => 'a2w_aliexpress_order_fulfillment',
            );

            $actions['a2w-order-info'] = array(
                'url' => '#' . $order->get_id(),
                'name' => __('AliExpress Info', 'ali2woo'),
                'action' => 'a2w-order-info',
            );

            $order_items = $order->get_items();

            $all_external_id = array();

            foreach ($order_items as $item_id => $item) {
                $a2w_order_item = new A2W_WooCommerceOrderItem($item);

                $external_order_id = $a2w_order_item->get_external_order_id();

                if ($external_order_id) {
                    $all_external_id[] = $external_order_id;
                }

            }

            if (!empty($all_external_id)) {
                $order_ids_url = implode("-", $all_external_id);

                $actions['a2w-aliexpress-sync'] = array(
                    'url' => '#' . $order_ids_url,
                    'name' => __('SYNC with AliExpress', 'ali2woo'),
                    'action' => 'a2w-aliexpress-sync',
                );
            }

            return $actions;
        }
    }

}
