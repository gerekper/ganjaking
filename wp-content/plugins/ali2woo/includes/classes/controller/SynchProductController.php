<?php

/**
 * Description of SynchProductController
 *
 * @author Ali2Woo Team
 *
 * @autoload: a2w_init
 *
 * @cron: true
 */

 namespace Ali2Woo;

class SynchProductController extends AbstractController
{
    private $update_per_schedule = 100;
    private $update_per_request = 5;
    private $update_period_delay = 60 * 60 * 24;

    public function __construct()
    {
        parent::__construct();

        add_action('a2w_install', array($this, 'install'));

        add_action('a2w_uninstall', array($this, 'uninstall'));

        add_filter('cron_schedules', array($this, 'init_reccurences'));

        add_action('admin_init', array($this, 'init'));

        add_action('a2w_synch_event_check', array($this, 'synch_event_check'));

        if (get_setting('auto_update')) {
            add_action('a2w_update_products_event', array($this, 'update_products_event'));

            if (get_setting('email_alerts')) {
                add_action('a2w_email_alerts_event', array($this, 'email_alerts_event'));
            }
        }

        if (get_setting('load_review') && get_setting('review_status')) {
            add_action('a2w_update_reviews_event', array($this, 'update_reviews_event'));
        }
    }

    public function init_reccurences($schedules)
    {
        $schedules['a2w_5_mins'] = array('interval' => 5 * 60, 'display' => __('Every 5 Minutes', 'ali2woo'));
        $schedules['a2w_15_mins'] = array('interval' => 15 * 60, 'display' => __('Every 15 Minutes', 'ali2woo'));
        return $schedules;
    }

    public function init()
    {
        add_action('a2w_set_setting_email_alerts', array($this, 'toggle_email_alerts'), 10, 3);

        add_action('a2w_set_setting_auto_update', array($this, 'togle_auto_update'), 10, 3);

        add_action('a2w_set_setting_review_status', array($this, 'togle_update_reviews'), 10, 3);

        if (!wp_next_scheduled('a2w_synch_event_check')) {
            wp_schedule_event(time(), 'a2w_5_mins', 'a2w_synch_event_check');
        }
    }

    public function synch_event_check()
    {
        // check: is a2w_update_products_event, update_reviews_event exist. if no, create it.

        if (!wp_next_scheduled('a2w_update_products_event') && get_setting('auto_update')) {
            $this->schedule_event();
        }

        if (!wp_next_scheduled('a2w_update_reviews_event') && get_setting('load_review') && get_setting('review_status')) {
            $this->schedule_reviews_event();
        }

        if (!wp_next_scheduled('a2w_email_alerts_event') && get_setting('auto_update') && get_setting('email_alerts')) {
            $this->schedule_email_alerts_event();
        }
    }

    public function install()
    {

        $this->unschedule_event();
        if (get_setting('auto_update')) {
            $this->schedule_event();
        }

        $this->unschedule_email_alerts_event();
        if (get_setting('auto_update') && get_setting('email_alerts')) {
            $this->schedule_email_alerts_event();
        }

        $this->unschedule_reviews_event();
        if (get_setting('load_review') && get_setting('review_status')) {
            $this->schedule_reviews_event();
        }

        // reset a2w_synch_event_check
        wp_clear_scheduled_hook('a2w_synch_event_check');
    }

    public function uninstall()
    {
        $this->unschedule_event();
        $this->schedule_email_alerts_event();
        $this->unschedule_reviews_event();

        // reset a2w_synch_event_check
        wp_clear_scheduled_hook('a2w_synch_event_check');
    }

    public function toggle_email_alerts($old_value, $value, $option)
    {
        if ($old_value !== $value) {
            $this->unschedule_email_alerts_event();
            if ($value) {
                $this->schedule_email_alerts_event();
            }
        }
    }

    public function togle_auto_update($old_value, $value, $option)
    {
        if ($old_value !== $value) {
            $this->unschedule_event();
            if ($value) {
                $this->schedule_event();
            }
        }
    }

    public function togle_update_reviews($old_value, $value, $option)
    {
        if ($old_value !== $value) {
            $this->unschedule_reviews_event();
            if ($value) {
                $this->schedule_reviews_event();
            }
        }

    }

    // Cron auto update event
    public function update_products_event()
    {
        if (!get_setting('auto_update') || $this->is_process_running('a2w_update_products_event')) {
            return;
        }

        $this->lock_process('a2w_update_products_event');

        a2w_init_error_handler();
        try {
            /** @var $woocommerce_model  Woocommerce */ 
 $woocommerce_model = A2W()->getDI()->get('Ali2Woo\Woocommerce');
            $loader = new Aliexpress();
            $sync_model = new Synchronize();

            $update_per_schedule = apply_filters('a2w_update_per_schedule',
                a2w_check_defined('A2W_UPDATE_PER_SCHEDULE') ? intval(A2W_UPDATE_PER_SCHEDULE) : $this->update_per_schedule
            );

            $update_per_request = apply_filters('a2w_update_per_request',
                a2w_check_defined('A2W_UPDATE_PER_REQUEST') ? intval(A2W_UPDATE_PER_REQUEST) : $this->update_per_request
            );

            $update_period_delay = apply_filters('a2w_update_period_delay',
                a2w_check_defined('A2W_UPDATE_PERIOD_DELAY') ? intval(A2W_UPDATE_PERIOD_DELAY) : $this->update_period_delay
            );

            $product_ids = $woocommerce_model->get_sorted_products_ids("_a2w_last_update", $update_per_schedule, array('value' => time() - $update_period_delay, 'compare' => '<'), get_setting('untrash_product'));

            $on_price_changes = get_setting('on_price_changes');
            $on_stock_changes = get_setting('on_stock_changes');

            $product_map = array();
            foreach ($product_ids as $product_id) {
                $product = $woocommerce_model->get_product_by_post_id($product_id, false);
                if ($product) {
                    if (!$product['disable_sync']) {
                        $product['disable_var_price_change'] = $product['disable_var_price_change'] || $on_price_changes !== "update";
                        $product['disable_var_quantity_change'] = $product['disable_var_quantity_change'] || $on_stock_changes !== "update";
                        $product_map[strval($product['id'])] = $product;
                    } else {
                        // update meta for skiped products
                        update_post_meta($product['post_id'], '_a2w_last_update', time());
                    }
                }
                unset($product);
            }
            $pc = $sync_model->get_product_cnt();
            while ($product_map) {
                $tmp_product_map = array_slice($product_map, 0, $update_per_request, true);
                $product_map = array_diff_key($product_map, $tmp_product_map);

                $product_ids = array_map(function ($p) {
                    $complex_id = $p['id'] . ';' . $p['import_lang'];

                    $shipping_meta = new ProductShippingMeta($p['post_id']);

                    $country_to = $shipping_meta->get_country_to();
                    if (!empty($country_to)) {
                        $complex_id .= ';' . $country_to;
                    }

                    $method = $shipping_meta->get_method();
                    if (!empty($method)) {
                        $complex_id .= ';' . $method;
                    }

                    return $complex_id;
                }, $tmp_product_map);

                $result = $loader->sync_products($product_ids, array('pc' => $pc));
                if ($result['state'] !== 'error') {
                    foreach ($result['products'] as $product) {
                        if (!empty($tmp_product_map[$product['id']])) {
                            try {
                                $product = array_replace_recursive($tmp_product_map[strval($product['id'])], $product);
                                $product = PriceFormula::apply_formula($product);
                                $woocommerce_model->upd_product($product['post_id'], $product);
                                if ($result['state'] !== 'ok') {
                                    a2w_error_log("update_products_event: " . $result['message']);
                                }
                                unset($tmp_product_map[$product['id']]);
                            } catch (\Throwable $e) {
                                a2w_print_throwable($e);
                            } catch (\Exception $e) {
                                a2w_print_throwable($e);
                            }
                        }
                    }

                    delete_transient('_a2w_daily_limits_warning');
                } else {
                    // update dayly limit warning
                    if ($result['error_code'] == 5001 && isset($result['time_left'])) {
                        set_transient('_a2w_daily_limits_warning', array('limit' => $result['call_limit'], 'until' => time() + $result['time_left']), time() + $result['time_left']);
                    } else {
                        a2w_error_log($result['message']);
                    }
                }

                // update meta for skiped products
                // foreach($tmp_product_map as $product){
                //     update_post_meta($product['post_id'], '_a2w_last_update', time());
                // }
                unset($result);
            }
        } catch (\Throwable $e) {
            a2w_print_throwable($e);
        } catch (\Exception $e) {
            a2w_print_throwable($e);
        }

        $this->unlock_process('a2w_update_products_event');

        if (get_setting('auto_update')) {
            $this->schedule_event();
        } else {
            $this->unschedule_event();
        }
    }

    public function email_alerts_event()
    {

        if (!get_setting('auto_update') || !get_setting('email_alerts') || $this->is_process_running('a2w_email_alerts_event')) {
            return;
        }

        $this->lock_process('a2w_email_alerts_event');

        a2w_init_error_handler();
        try {
            $product_change_model = new ProductChange();

            $to = get_setting('email_alerts_email');

            if ($to) {

                $items = $product_change_model->get_all();
                //we update data of product_change_model in the add_variation method in Ali2Woo\Woocommerce
                //the collision of these two events is almost unreal, therefore we neglect it
                $product_change_model->clear_all();

                if ($items) {

                    $items_per_email = 100;
                    $chunks = array_chunk($items, $items_per_email, true);

                    a2w_info_log('Total changes: ' . count($items) . ', total emails: ' . count($chunks));

                    foreach ($chunks as $chunk_items) {
                        $result = $this->send_email_alert($to, $chunk_items);
                    }

                }

            }

        } catch (\Throwable $e) {
            a2w_print_throwable($e);
        } catch (\Exception $e) {
            a2w_print_throwable($e);
        }

        $this->unlock_process('a2w_email_alerts_event');

        if (get_setting('auto_update') && get_setting('email_alerts')) {
            $this->schedule_email_alerts_event();
        } else {
            $this->unschedule_email_alerts_event();
        }

    }

    private function send_email_alert($to, $items)
    {

        $items = $this->format_items_for_email_alert($items);

        $this->model_put("email_heading", __('Ali2Woo report: Changes in your products', 'ali2woo'));
        $this->model_put("email_subheading", __('Changes occurred in the last half-hour in your store', 'ali2woo'));
        $this->model_put("items", $items);
        $this->model_put("email_footer_text", __('The report is generated by the Email alerts module in Ali2Woo at', 'ali2woo') . ' ' . date("F j, Y, g:i a"));

        ob_start();
        $this->include_view(
            array("emails/email-header.php", "emails/product-changes.php", "emails/email-footer.php"));

        $message = ob_get_clean();

        $result = wc_mail($to, __('Ali2Woo report: Changes in your products', 'ali2woo'), $message);
        a2w_info_log('Email with product changes: ' . ($result ? ' is sent' : ' isn`t sent'));

        return $result;

    }

    private function format_items_for_email_alert($items)
    {

        $formatted_items = array();

        foreach ($items as $product_id => $item) {

            $product = wc_get_product($product_id);

            $formatted_items[$product_id] = $item;

            $formatted_items[$product_id]['image-src'] = $product->get_image();
            $formatted_items[$product_id]['title'] = $product->get_formatted_name();
            $formatted_items[$product_id]['url'] = get_permalink($product_id);
            $formatted_items[$product_id]['original_url'] = get_post_meta($product_id, '_a2w_product_url', true);

        }

        return $formatted_items;

    }

    public function update_reviews_event()
    {
        if (!get_setting('load_review') || !get_setting('review_status') || $this->is_process_running('a2w_update_reviews_event')) {
            return;
        }

        $this->lock_process('a2w_update_reviews_event');

        a2w_init_error_handler();
        try {
            /** @var $woocommerce_model  Woocommerce */ 
            $woocommerce_model = A2W()->getDI()->get('Ali2Woo\Woocommerce');
            $reviews_model = new Review();

            $posts_by_time = $woocommerce_model->get_sorted_products_ids("_a2w_reviews_last_update", 20);
            foreach ($posts_by_time as $post_id) {
                $reviews_model->load($post_id);
            }
        } catch (\Throwable $e) {
            a2w_print_throwable($e);
        } catch (\Exception $e) {
            a2w_print_throwable($e);
        }

        $this->unlock_process('a2w_update_reviews_event');

        if (get_setting('load_review') && get_setting('review_status')) {
            $this->schedule_reviews_event();
        } else {
            $this->unschedule_reviews_event();
        }
    }

    private function schedule_event()
    {
        if (!($timestamp = wp_next_scheduled('a2w_update_products_event'))) {
            wp_schedule_single_event(time() + MINUTE_IN_SECONDS * 5, 'a2w_update_products_event');
        }
    }

    private function unschedule_event()
    {
        wp_clear_scheduled_hook('a2w_update_products_event');
    }

    private function schedule_email_alerts_event()
    {
        if (!($timestamp = wp_next_scheduled('a2w_email_alerts_event'))) {
            wp_schedule_single_event(time() + MINUTE_IN_SECONDS * 30, 'a2w_email_alerts_event');
        }
    }

    private function unschedule_email_alerts_event()
    {
        wp_clear_scheduled_hook('a2w_email_alerts_event');
    }

    private function schedule_reviews_event()
    {
        if (!($timestamp = wp_next_scheduled('a2w_update_reviews_event'))) {
            wp_schedule_single_event(time() + MINUTE_IN_SECONDS * 30, 'a2w_update_reviews_event');
        }
    }

    private function unschedule_reviews_event()
    {
        wp_clear_scheduled_hook('a2w_update_reviews_event');
    }

    protected function is_process_running($process)
    {
        if (get_site_transient($process . '_process_lock')) {
            return true;
        }

        return false;
    }

    protected function lock_process($process)
    {
        set_site_transient($process . '_process_lock', microtime(), MINUTE_IN_SECONDS * 2);
    }

    protected function unlock_process($process)
    {
        delete_site_transient($process . '_process_lock');
    }

}
