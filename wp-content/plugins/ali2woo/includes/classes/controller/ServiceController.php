<?php

/**
 * Description of ServiceController
 *
 * @author Ali2Woo Team
 * 
 * @autoload: a2w_admin_init
 */

namespace Ali2Woo;

class ServiceController {

    private $system_message_update_period = 3600;

    public function __construct() {
        $system_message_last_update = intval(get_setting('plugin_data_last_update'));
        if (!$system_message_last_update || $system_message_last_update < time()) {
            set_setting('plugin_data_last_update', time() + $this->system_message_update_period);
            $sync_model = new Synchronize();
            $request_url = RequestHelper::build_request('sync_plugin_data', array('pc' => $sync_model->get_product_cnt()));
            $request = a2w_remote_get($request_url);
            if (!is_wp_error($request) && intval($request['response']['code']) == 200) {
                $plugin_data = json_decode($request['body'], true);
                $categories = isset($plugin_data['categories']) && is_array($plugin_data['categories']) ? $plugin_data['categories'] : array();
                if (isset($plugin_data['messages'])) {
                    set_setting('system_message', $plugin_data['messages']);
                }
                update_option('a2w_all_categories', $categories, 'no');
            }
        }
    }
}
