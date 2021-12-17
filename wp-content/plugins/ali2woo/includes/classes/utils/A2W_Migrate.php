<?php

/**
 * Description of A2W_Migrate
 *
 * @author Andrey
 * 
 * @autoload: a2w_admin_init
 */

if (!class_exists('A2W_Migrate')) {

    class A2W_Migrate {
        public function __construct() {
            $this->migrate();
        }
        
        public function migrate(){
            $cur_version = get_option('a2w_db_version', '');
            if(version_compare($cur_version, "1.2.0", '<')) {
                $this->migrate_to_120();
            }
            
            if(version_compare($cur_version, "1.2.1", '<')) {
                $this->migrate_to_121();
            }
            if(version_compare($cur_version, "1.3.5", '<')) {
                $this->migrate_to_135();
            }
            
            if(version_compare($cur_version, "1.3.7", '<')) {
                $this->migrate_to_137();
            }
            
            if(version_compare($cur_version, "1.4.5", '<')) {
                $this->migrate_to_145();
            }
            
            if(version_compare($cur_version, "1.5.9", '<')) {
                $this->migrate_to_159();
            }

            if(version_compare($cur_version, "1.6.14", '<')) {
                $this->migrate_to_1614();
            }

            if(version_compare($cur_version, "1.7.0", '<')) {
                $this->migrate_to_170();
            }

            if(version_compare($cur_version, "1.8.14", '<')) {
                $this->migrate_to_1814();
            }

            if(version_compare($cur_version, "1.11.00", '<')) {
                $this->migrate_to_11100();
            }

            if(version_compare($cur_version, "1.11.9", '<')) {
                $this->migrate_to_1_11_9();
            }

            if(version_compare($cur_version, "1.15.6", '<')) {
                $this->migrate_to_1_15_6();
            }

            if(version_compare($cur_version, "1.16.8", '<')) {
                $this->migrate_to_1_16_8();
            }

            if(version_compare($cur_version, "1.18.5", '<')) {
                $this->migrate_to_1_18_5();
            }

            if(version_compare($cur_version, "1.18.15", '<')) {
                $this->migrate_to_1_18_15();
            }

            if(is_a($this, 'A2WL_Migrate') && version_compare($cur_version, "2.0.5", '<')) {
                $this->migrate_to_2_0_5();
            }
            
            if(version_compare($cur_version, A2W()->version, '<')) {
                update_option('a2w_db_version', A2W()->version, 'no');
            }
        }
        
        private function migrate_to_120(){
            a2w_error_log('migrate to 1.2.0');
            
            global $wpdb;
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_external_id' WHERE pm2.post_id=src.post_id and pm2.meta_key='external_id'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_original_product_url' WHERE pm2.post_id=src.post_id and pm2.meta_key='original_product_url'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_seller_url' WHERE pm2.post_id=src.post_id and pm2.meta_key='seller_url'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_last_update' WHERE pm2.post_id=src.post_id and pm2.meta_key='a2w_last_update'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_skip_meta' WHERE pm2.post_id=src.post_id and pm2.meta_key='a2w_skip_meta'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_disable_var_price_change' WHERE pm2.post_id=src.post_id and pm2.meta_key='a2w_disable_var_price_change'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_reviews_last_update' WHERE pm2.post_id=src.post_id and pm2.meta_key='a2w_reviews_last_update'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_review_page' WHERE pm2.post_id=src.post_id and pm2.meta_key='a2w_review_page'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_shipping_data' WHERE pm2.post_id=src.post_id and pm2.meta_key='a2w_shipping_data'");
            
            $wpdb->query("DELETE pm2 FROM {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE (pm1.meta_key='import_type' or pm1.meta_key='_a2w_import_type') and pm1.meta_value='a2w') src WHERE pm2.post_id=src.post_id and pm2.meta_key='product_url'");
            
            $wpdb->query("UPDATE {$wpdb->postmeta} pm2, (SELECT p.id as post_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 ON(p.ID=pm1.post_id) WHERE pm1.meta_key='import_type' and pm1.meta_value='a2w') src SET pm2.meta_key='_a2w_import_type' WHERE pm2.post_id=src.post_id and pm2.meta_key='import_type'");
        }
        
        private function migrate_to_121(){
            a2w_error_log('migrate to 1.2.1');
            
            $formula_list = a2w_get_transient('a2w_formula_list');
            if($formula_list){
                update_option('a2w_formula_list', $formula_list, 'no');
            }
            a2w_delete_transient('a2w_formula_list');
            
            $formula = a2w_get_transient('a2w_default_formula');
            if($formula){
                update_option('a2w_default_formula', $formula, 'no');
            }
            a2w_delete_transient('a2w_default_formula');
        }
        
        private function migrate_to_135(){
            a2w_error_log('migrate to 1.3.5');
            a2w_settings()->auto_commit(false);
            a2w_set_setting('item_purchase_code',  get_option('a2w_item_purchase_code', ''));
            a2w_set_setting('envato_personal_token',  get_option('a2w_envato_personal_token', ''));
            a2w_set_setting('use_custom_account',  get_option('a2w_use_custom_account', false));
            a2w_set_setting('accounts',  get_option('a2w_accounts', array()));
            
            a2w_set_setting('import_language',  get_option('a2w_import_language', 'en'));
            a2w_set_setting('local_currency',  get_option('a2w_local_currency', 'USD'));
            a2w_set_setting('default_product_type',  get_option('a2w_default_product_type', 'simple'));
            a2w_set_setting('default_product_status',  get_option('a2w_default_product_status', 'publish'));
            a2w_set_setting('not_import_attributes',  get_option('a2w_not_import_attributes', false));
            a2w_set_setting('not_import_description',  get_option('a2w_not_import_description', false));
            a2w_set_setting('not_import_description_images',  get_option('a2w_not_import_description_images', false));
            a2w_set_setting('import_extended_attribute',  get_option('a2w_import_extended_attribute', false));
            a2w_set_setting('import_product_images_limit',  get_option('a2w_import_product_images_limit', 0));
            a2w_set_setting('use_external_image_urls',  get_option('a2w_use_external_image_urls', true));
            a2w_set_setting('use_random_stock',  get_option('a2w_use_random_stock', false));
            a2w_set_setting('use_random_stock_min',  get_option('a2w_use_random_stock_min', 5));
            a2w_set_setting('use_random_stock_max',  get_option('a2w_use_random_stock_max', 15));
            a2w_set_setting('split_attribute_values',  get_option('a2w_split_attribute_values', true));
            a2w_set_setting('attribute_values_separator',  get_option('a2w_attribute_values_separator', ','));
            a2w_set_setting('currency_conversion_factor',  get_option('a2w_currency_conversion_factor', 1));
            a2w_set_setting('auto_update',  get_option('a2w_auto_update', false));
            a2w_set_setting('not_available_product_status',  get_option('a2w_not_available_product_status', 'trash'));
            a2w_set_setting('sync_type',  get_option('a2w_sync_type', 'price_and_stock'));
            a2w_set_setting('fulfillment_prefship',  get_option('a2w_fulfillment_prefship', 'EMS_ZX_ZX_US'));
            a2w_set_setting('fulfillment_phone_code',  get_option('a2w_fulfillment_phone_code', ''));
            a2w_set_setting('fulfillment_phone_number',  get_option('a2w_fulfillment_phone_number', ''));
            a2w_set_setting('fulfillment_custom_note',  get_option('a2w_fulfillment_custom_note', ''));

            a2w_set_setting('use_extended_price_markup',  get_option('a2w_use_extended_price_markup', false));
            a2w_set_setting('use_compared_price_markup',  get_option('a2w_use_compared_price_markup', false));
            a2w_set_setting('price_cents',  get_option('a2w_price_cents', -1));
            a2w_set_setting('price_compared_cents',  get_option('a2w_price_compared_cents', -1));
            a2w_set_setting('default_formula',  get_option('a2w_default_formula', false));
            a2w_set_setting('formula_list',  get_option('a2w_formula_list', array()));

            a2w_set_setting('phrase_list',  get_option('a2w_phrase_list', array()));

            a2w_set_setting('review_status',  get_option('a2w_review_status', false));
            a2w_set_setting('review_translated',  get_option('a2w_review_translated', false));
            a2w_set_setting('review_avatar_import',  get_option('a2w_review_avatar_import', false));
            a2w_set_setting('review_max_per_product',  get_option('a2w_review_max_per_product', 20));
            a2w_set_setting('review_raiting_from',  get_option('a2w_review_raiting_from', 1));
            a2w_set_setting('review_raiting_to',  get_option('a2w_review_raiting_to', 5));
            a2w_set_setting('review_noavatar_photo',  get_option('a2w_review_noavatar_photo', (A2W()->plugin_url() . '/assets/img/noavatar.png')));
            a2w_set_setting('review_load_attributes',  get_option('a2w_review_load_attributes', false));
            a2w_set_setting('review_show_image_list',  get_option('a2w_review_show_image_list', false));
            a2w_set_setting('review_allow_country',  get_option('a2w_review_allow_country', ''));

            a2w_set_setting('aliship_frontend',  get_option('a2w_aliship_frontend', false));
            a2w_set_setting('aliship_shipto',  get_option('a2w_aliship_shipto', 'US'));
            
            a2w_set_setting('a2w_json_api_base',  get_option('a2w_json_api_base', 'a2w_api'));
            a2w_set_setting('a2w_json_api_controllers',  get_option('a2w_json_api_controllers', 'core,auth'));
            
            a2w_settings()->commit();
            a2w_settings()->auto_commit(true);
            
            delete_option('a2w_item_purchase_code');
            delete_option('a2w_envato_personal_token');
            delete_option('a2w_use_custom_account');
            delete_option('a2w_accounts');
            
            delete_option('a2w_import_language');
            delete_option('a2w_local_currency');
            delete_option('a2w_default_product_type');
            delete_option('a2w_default_product_status');
            delete_option('a2w_not_import_attributes');
            delete_option('a2w_not_import_description');
            delete_option('a2w_not_import_description_images');
            delete_option('a2w_import_extended_attribute');
            delete_option('a2w_import_product_images_limit');
            delete_option('a2w_use_external_image_urls');
            delete_option('a2w_use_random_stock');
            delete_option('a2w_use_random_stock_min');
            delete_option('a2w_use_random_stock_max');
            delete_option('a2w_split_attribute_values');
            delete_option('a2w_attribute_values_separator');
            delete_option('a2w_currency_conversion_factor');
            delete_option('a2w_auto_update');
            delete_option('a2w_not_available_product_status');
            delete_option('a2w_sync_type');
            delete_option('a2w_fulfillment_prefship');
            delete_option('a2w_fulfillment_phone_code');
            delete_option('a2w_fulfillment_phone_number');
            delete_option('a2w_fulfillment_custom_note');
            
            delete_option('a2w_use_extended_price_markup');
            delete_option('a2w_use_compared_price_markup');
            delete_option('a2w_price_cents');
            delete_option('a2w_price_compared_cents');
            delete_option('a2w_default_formula');
            delete_option('a2w_formula_list');
            
            delete_option('a2w_phrase_list');
            
            delete_option('a2w_review_status');
            delete_option('a2w_review_translated');
            delete_option('a2w_review_avatar_import');
            delete_option('a2w_review_max_per_product');
            delete_option('a2w_review_raiting_from');
            delete_option('a2w_review_raiting_to');
            delete_option('a2w_review_noavatar_photo');
            delete_option('a2w_review_load_attributes');
            delete_option('a2w_review_show_image_list');
            delete_option('a2w_review_allow_country');
            
            delete_option('a2w_aliship_frontend');
            delete_option('a2w_aliship_shipto');
            
            delete_option('a2w_json_api_base');
            delete_option('a2w_json_api_controllers');
            
            delete_option('a2w_disable_var_quantity_change');
            delete_option('a2w_disable_var_price_change');
            delete_option('a2w_use_custom_stock');
            delete_option('a2w_use_custom_stock_max');
            delete_option('a2w_use_custom_stock_min');
            delete_option('a2w_update_per_schedule');
            delete_option('a2w_auto_update_period');
            delete_option('a2w_review_update_per_schedule');
            delete_option('a2w_review_schedule_load_period');
            delete_option('a2w_remember_categories');
            
        }
        private function migrate_to_137(){
            a2w_error_log('migrate_to_137');
            
            $account_data = a2w_get_setting('account_data');
            if(empty($account_data)){
                $account_data = array('aliexpress'=>array('appkey'=>'', 'trackingid'=>''), 'admitad'=>array('cashback_url'=>''));
            }
            $accounts = a2w_get_setting('accounts');
            if (is_array($accounts) && $accounts) {
                if (isset($accounts[0]['appkey'])) {
                    $account_data['aliexpress']['appkey']=$accounts[0]['appkey'];
                }
                if (isset($accounts[0]['trackingid'])) {
                    $account_data['aliexpress']['trackingid']=$accounts[0]['trackingid'];
                }
            }
            a2w_set_setting('account_data', $account_data);
        }
        
        private function migrate_to_145(){
            a2w_error_log('migrate to 1.4.5');
            
            global $wpdb;
            
            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_a2w_product_url'");
            $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) SELECT post_id, '_a2w_product_url', meta_value FROM {$wpdb->postmeta} WHERE meta_key='_product_url'");
        }
        
        private function migrate_to_159(){
            a2w_error_log('migrate to 1.5.9');
            
            $srickers = a2w_get_setting('image_editor_srickers',array());
            foreach($srickers as $key=>$sricker){
                $srickers[$key] = str_replace("https://ali2woo.com/static/stickers/", "/assets/img/stickers/", $sricker);
            }
            a2w_set_setting('image_editor_srickers', $srickers);
            
        }

        private function migrate_to_1614(){
            a2w_error_log('migrate to 1.6.14');

            $on_not_available_product = 'trash';
            $on_not_available_variation = 'trash';
            $on_new_variation_appearance = 'add';
            $on_price_changes = 'update';
            $on_stock_changes = 'update';

            $not_available_product_status = a2w_get_setting('not_available_product_status');
            if($not_available_product_status == "trash"){
                $on_not_available_product = 'trash';
                $on_not_available_variation = 'trash';
            }else if($not_available_product_status == "outofstock"){
                $on_not_available_product = 'zero';
                $on_not_available_variation = 'zero';
            }else if($not_available_product_status == "instock"){
                $on_not_available_product = 'nothing';
                $on_not_available_variation = 'nothing';
            }

            $sync_type = a2w_get_setting('sync_type');
            if($sync_type='price_and_stock'){
                $on_price_changes = 'update';
                $on_stock_changes = 'update';
            }else if($sync_type='price'){
                $on_price_changes = 'update';
                $on_stock_changes = 'nothing';
            }else if($sync_type='stock'){
                $on_price_changes = 'nothing';
                $on_stock_changes = 'update';
            }else if($sync_type='no'){
                $on_price_changes = 'nothing';
                $on_stock_changes = 'nothing';
            }

            a2w_set_setting('on_not_available_product', $on_not_available_product);
            a2w_set_setting('on_not_available_variation', $on_not_available_variation);
            a2w_set_setting('on_new_variation_appearance', $on_new_variation_appearance);
            a2w_set_setting('on_price_changes', $on_price_changes);
            a2w_set_setting('on_stock_changes', $on_stock_changes);
        }

        private function migrate_to_170(){
            a2w_error_log('migrate to 1.7.0');

            a2w_set_setting('api_endpoint', 'https://api.ali2woo.com/v1/');
        }

        private function migrate_to_1814(){
            a2w_error_log('migrate to 1.8.14');

            a2w_set_setting('pricing_rules_type', 'sale_price_and_discount');
        }

        private function migrate_to_11100(){
            a2w_error_log('migrate to 1.11.00');

            a2w_set_setting('chrome_ext_import', false);
        }

        private function migrate_to_1_11_9(){
            a2w_error_log('migrate to 1.11.9');

            global $wpdb;

            $wpdb->query("UPDATE {$wpdb->comments} c INNER JOIN {$wpdb->commentmeta} cm ON (cm.comment_id=c.comment_ID) SET c.comment_type='review' WHERE (c.comment_type='' OR c.comment_type IS NULL OR c.comment_type = 'comment') AND cm.meta_key='a2w_cash'");

            $wpdb->query("DELETE pm1 FROM {$wpdb->postmeta} pm1 INNER JOIN {$wpdb->postmeta} pm2 ON (pm1.post_id=pm2.post_id and pm2.meta_key='_a2w_external_id') WHERE pm1.meta_key='_wc_review_count'");

            $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id, meta_key,meta_value) SELECT p.ID, '_wc_review_count', count(c.comment_ID) FROM {$wpdb->posts} p INNER JOIN {$wpdb->comments} c ON (p.ID=c.comment_post_ID) INNER JOIN {$wpdb->postmeta} pm ON (p.ID=pm.post_id AND pm.meta_key='_a2w_external_id') GROUP BY p.ID");

            delete_transient( 'wc_count_comments' );
        }

        private function migrate_to_1_15_6(){
            a2w_error_log('migrate to 1.15.6');

            wp_clear_scheduled_hook('a2w_auto_synch_event');
        }

        private function migrate_to_1_16_8(){
            a2w_error_log('migrate to 1.16.8');

            a2w_gen_pk();
        }

        private function migrate_to_1_18_5(){
            a2w_error_log('migrate to 1.18.5');

            global $wpdb;

            $wpdb->query("UPDATE {$wpdb->prefix}woocommerce_order_itemmeta SET meta_key='_a2w_customer_chosen_shipping' WHERE meta_key='a2w_customer_chosen_shipping'");
        }

        private function migrate_to_1_18_15() {
            a2w_error_log('migrate to 1.18.15');

            global $wpdb;

            $wpdb->query("UPDATE {$wpdb->options} SET autoload='no' WHERE option_name like 'a2w\_%' OR option_name like '_transient_a2w\_%'");
        }


        private function migrate_to_2_0_5(){
            a2w_error_log('migrate to 2.0.5');

            global $wpdb;

            $wpdb->query("UPDATE {$wpdb->postmeta} SET meta_key='_wp_a2w_attached_file' WHERE meta_key='_wp_a2wl_attached_file'");
        }
    }
}
