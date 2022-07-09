<?php
/* * class
 * Description of A2W_OrderDataTabController
 *
 * @author Mikhail
 *
 * @autoload: a2w_admin_init
 *
 * @ajax: true
 */
if (!class_exists('A2W_OrderDataTabController')) {

    class A2W_OrderDataTabController
    {
        public function __construct()
        {

            //the following 4 methods are shared with tracking plugin
            add_action('admin_enqueue_scripts', array($this, 'assets'));
            add_action('woocommerce_admin_order_data_after_order_details', array($this, 'woocommerce_admin_order_data_after_order_details'));
            add_action('woocommerce_process_shop_order_meta', array($this, 'woocommerce_process_shop_order_meta'));
            add_action('wp_ajax_a2w_reset_order_data', array($this, 'ajax_reset_order_data'));

            //Modify order item data
            add_filter('woocommerce_order_item_display_meta_key', array($this, 'woocommerce_order_item_display_meta_key'), 99, 3);
            add_filter('woocommerce_order_item_display_meta_value', array($this, 'woocommerce_order_item_display_meta_value'), 10, 3);

            //Filter order meta in emails
            add_filter('woocommerce_email_order_items_args', array($this, 'woocommerce_email_order_items_args'), 10);

        }

        public function assets()
        {
            if (isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] === 'edit') {
                wp_enqueue_script('a2w-wc-order-edit-script', A2W()->plugin_url() . '/assets/js/wc_order_edit.js', array(), A2W()->version);
                wp_enqueue_style('a2w-wc-order-edit-style', A2W()->plugin_url() . '/assets/css/wc_order_edit.css', array(), A2W()->version);
            }
        }

        public function ajax_reset_order_data()
        {

            $result = A2W_ResultBuilder::buildOk();

            try {
                $order_id = (int) $_POST['id'];

                $order = wc_get_order($order_id);

                if ($order) {

                    $order_items = $order->get_items();

                    foreach ($order_items as $item_id => $item) {
                        $item->delete_meta_data(A2W_Constants::order_item_external_order_meta());
                        $item->delete_meta_data(A2W_Constants::order_item_tracking_data_meta());
                        $item->save();
                    }

                    $order->add_order_note(__('The order external ID(s) and tracking numbers have been reset.', 'ali2woo'), false, true);
                } else {
                    $result = A2W_ResultBuilder::buildError('did not find the order id: №' . $order_id);
                }

                restore_error_handler();
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            }

            echo json_encode($result);
            wp_die();

        }

        public function woocommerce_admin_order_data_after_order_details($order)
        {?>

            <br class="clear" />
            <h3><?php _e('A2W Order Data', 'ali2woo')?><a href="#" class="edit_address"><?php _e('Edit')?></a></h3>

            <div class="address order_items">

            <?php

            if (!isset($_GET['post'])) {
                return;
            }

            $order_id = (int) $_GET['post'];
            $order = wc_get_order($order_id);

            if (!$order) {
                return;
            }

            $order_items = $order->get_items();

            ?>

            <?php if (count($order_items)): ?>
            <p>
            <?php foreach ($order_items as $item_id => $item): ?>


            <?php

            $a2w_order_item = new A2W_WooCommerceOrderItem($item);

            $external_order_id = $a2w_order_item->get_external_order_id();

            $ali_order_link = $a2w_order_item->get_ali_order_item_link();

            $order_item_tracking_codes = $a2w_order_item->get_formated_tracking_codes();

            //$carrier_link = $a2w_order_item->get_formated_carrier_link();

            $carrier_name = $a2w_order_item->get_carrier_name();

            ?>

            <strong><?php _e('Item', 'ali2woo')?>:</strong> <a target="_blank" href="<?php echo get_edit_post_link($item['product_id']); ?>"><?php echo $this->shorten_display_value($item->get_name()); ?></a>
            <a class="product_view" target="_blank" title="<?php _e('View product', 'ali2woo')?>" href="<?php echo get_permalink($item['product_id']); ?>"></a><br/>
            <strong><?php _e('AliExpress order ID', 'ali2woo')?>:</strong> <?php echo $ali_order_link; ?><br/>
            <strong><?php _e('Tracking numbers', 'ali2woo')?>:</strong> <?php echo $order_item_tracking_codes; ?><br/>
            <?php if ($carrier_name): ?><strong><?php _e('Carrier', 'ali2woo')?>:</strong> <?php echo $carrier_name; ?><br/><?php endif;?>
            <br/>
            <?php endforeach;?>
            </p>
            <?php endif;?>

            <a role="button" class="a2w_reset_order_data" href="#"><?php _e('Reset A2W Order Data', 'ali2woo')?></a>

            <?php

            /** todo: In previous plugin version the Tracking numbers and external order id are save in other metas
             * in the order data intead of order item data
             * The plugin still shows this data in read-only format, it will be removed completely  in the future plugin version
             **/
            $order_external_id_array = get_post_meta($order->get_id(), A2W_Constants::old_order_external_order_id());

            $order_tracking_codes = get_post_meta($order->get_id(), A2W_Constants::old_order_tracking_code());

            ?>

            <?php if ($order_external_id_array || $order_tracking_codes): ?>
            <hr/>
            <p><?php _e('Old order data goes below. It will be  removed completely in a future versions of the Ali2Woo plugin.', 'ali2woo')?></p>
            <?php endif;?>

            <?php if ($order_external_id_array): ?>
            <?php _e('AliExpress order ID(s)', 'ali2woo')?>:
            <div class="a2w_external_order_id">
            <?php foreach ($order_external_id_array as $k => $order_external_id): ?>
            <?php echo htmlentities($order_external_id) ?><?php if ($k < count($order_external_id_array) - 1): ?>,<?php endif;?>
            <?php endforeach;?>
            </div>

            <?php endif;?>


            <?php if ($order_tracking_codes): ?>
            <?php _e('AliExpress tracking numbers', 'ali2woo')?>:
            <div class="a2w_tracking_code_data">
            <?php foreach ($order_tracking_codes as $k => $tracking_code): ?>
            <?php echo htmlentities($tracking_code) ?><?php if ($k < count($order_tracking_codes) - 1): ?>,<?php endif;?>
            <?php endforeach;?>
            </div>

            <?php endif;?>

            </div>
            <div class="edit_address">

            <?php if (count($order_items)): ?>
            <?php foreach ($order_items as $item_id => $item): ?>
            <div class="clear"></div>
            <h3><?php _e('Item', 'ali2woo')?>: <span><a target="_blank" href="<?php echo get_edit_post_link($item['product_id']); ?>"><?php echo $this->shorten_display_value($item->get_name()); ?></a> <a class="product_view" target="_blank" title="<?php _e('View product', 'ali2woo')?>" href="<?php echo get_permalink($item['product_id']); ?>"></a></span></h3>

            <?php

            $a2w_order_item = new A2W_WooCommerceOrderItem($item);

            $external_order_id = $a2w_order_item->get_external_order_id();

            $order_item_tracking_codes = $a2w_order_item->get_formated_tracking_codes(true);
            ?>

            <?php woocommerce_wp_text_input(array(
                'id' => 'a2w_external_order_id_' . $item_id,
                'name' => 'a2w_external_order_id_' . $item_id,
                'label' => __('AliExpress Order ID', 'ali2woo'),
                'value' => $external_order_id,
                'wrapper_class' => 'form-field-wide',
            ));
            ?>

            <?php woocommerce_wp_text_input(array(
                'id' => 'a2w_tracking_code_' . $item_id,
                'name' => 'a2w_tracking_code_' . $item_id,
                'label' => __('Tracking numbers', 'ali2woo'),
                /* Here can be array of Tracking numbers, because sometimes AliExpress replace old tracking ocde with new one for some orders */
                'value' => $order_item_tracking_codes,
                'wrapper_class' => 'form-field-wide',
            ));
            ?>

            <?php endforeach;?>
            <?php endif;?>
            </div>
       <?php }

        public function woocommerce_process_shop_order_meta($order_id)
        {

            $order = wc_get_order($order_id);
            $order_items = $order->get_items();

            foreach ($order_items as $item_id => $item) {

                $a2w_order_item = new A2W_WooCommerceOrderItem($item);

                if (isset($_POST['a2w_external_order_id_' . $item_id]) && !empty($_POST['a2w_external_order_id_' . $item_id])) {

                    $external_order_id = intval($_POST['a2w_external_order_id_' . $item_id]);

                    $a2w_order_item->update_external_order($external_order_id);

                }

                if (isset($_POST['a2w_tracking_code_' . $item_id]) && !empty($_POST['a2w_tracking_code_' . $item_id])) {
                    $tracking_codes = explode(',', $_POST['a2w_tracking_code_' . $item_id]);
                    $tracking_codes = array_map('trim', $tracking_codes);

                    $a2w_order_item->update_tracking_codes($tracking_codes);

                }

                if ($a2w_order_item->save()) {
                    $order->add_order_note(__('The order external ID(s) and/or tracking numbers have been added to the order manually.', 'ali2woo'), false, true);
                }

            }

        }

        public function woocommerce_order_item_display_meta_key($display_key, $meta, $item)
        {

            //todo: maybe remove this code? THese metas are not displayed on th order edit page

            if ($meta->key === A2W_Shipping::get_order_item_shipping_meta_key()) {
                $display_key = esc_html__('Shipping Info', 'ali2woo');
            }

            if ($meta->key == A2W_Constants::order_item_external_order_meta()) {
                $display_key = esc_html__('AliExpress Order ID', 'ali2woo');
            }

            if ($meta->key == A2W_Constants::order_item_tracking_data_meta()) {
                $display_key = esc_html__('Tracking numbers', 'ali2woo');
            }

            return $display_key;
        }

        public function woocommerce_order_item_display_meta_value($display_value, $meta, $item)
        {
            if ($meta->key === A2W_Shipping::get_order_item_shipping_meta_key()) {
                $value = $meta->value;
                if ($value) {
                    $order_id = $item->get_order_id();
                    $display_value = A2W_Shipping::get_formated_shipping_info_meta($order_id, $value);
                }
            }

            if ($meta->key == A2W_Constants::order_item_tracking_data_meta()) {
                $display_value = $this->get_formated_order_item_tracking_data_meta($value);
            }

            return $display_value;

        }

        public function woocommerce_email_order_items_args($args)
        {

            if ($args['sent_to_admin']) {
                return $args;
            }

            //a little hack, save in global that we are in the email template
            $_POST['a2w_email_template_check'] = true;

            return $args;

        }

        private function get_formated_order_item_tracking_data_meta($value)
        {

            $tracking_codes = "";

            if ($value && isset($value['tracking_codes'])) {
                $tracking_codes = implode(",", $value['tracking_codes']);
            }

            return $tracking_codes;
        }

        private function shorten_display_value($string)
        {
            if (strlen($string) >= 40) {
                return substr($string, 0, 30) . " ... " . substr($string, -10);
            } else {
                return $string;
            }
        }

    }

}
