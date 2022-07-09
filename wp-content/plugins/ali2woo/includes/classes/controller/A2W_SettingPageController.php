<?php

/**
 * Description of A2W_SettingPage
 *
 * @author Andrey
 *
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_SettingPageController')) {

    class A2W_SettingPageController extends A2W_AbstractAdminPage
    {
        public function __construct()
        {
            parent::__construct(__('Settings', 'ali2woo'), __('Settings', 'ali2woo'), 'import', 'a2w_setting', 30);

            add_filter('a2w_setting_view', array($this, 'setting_view'));
            add_filter('a2w_configure_lang_data', array($this, 'configure_lang_data'));
        }

        public function configure_lang_data($lang_data)
        {
            if ($this->is_current_page()) {
                $lang_data = array(
                    'process_loading_d_of_d_erros_d' => _x('Process loading %d of %d. Errors: %d.', 'Status', 'ali2woo'),
                    'load_button_text' => _x('Load %d images', 'Status', 'ali2woo'),
                    'all_images_loaded_text' => _x('All images loaded', 'Status', 'ali2woo'),
                    'leave_blank_to_allow_all_countries' => __('leave blank to allow all countries', 'ali2woo'),
                );
            }
            return $lang_data;
        }

        public function render($params = array())
        {

            $current_module = isset($_REQUEST['subpage']) ? $_REQUEST['subpage'] : 'common';

            $this->model_put("modules", $this->getModules());
            $this->model_put("current_module", $current_module);

            $this->include_view(array("settings/settings_head.php", apply_filters('a2w_setting_view', $current_module), "settings/settings_footer.php"));
        }

        public function getModules()
        {
            return apply_filters('a2w_setting_modules', array(
                array('id' => 'common', 'name' => __('Common settings', 'ali2woo')),
                array('id' => 'account', 'name' => __('Account settings', 'ali2woo')),
                array('id' => 'price_formula', 'name' => __('Pricing Rules', 'ali2woo')),
                array('id' => 'reviews', 'name' => __('Reviews settings', 'ali2woo')),
                array('id' => 'shipping', 'name' => __('Shipping settings', 'ali2woo')),
                array('id' => 'phrase_filter', 'name' => __('Phrase Filtering', 'ali2woo')),
                array('id' => 'chrome_api', 'name' => __('API Keys', 'ali2woo')),
                array('id' => 'system_info', 'name' => __('System Info', 'ali2woo')),
            ));
        }

        public function setting_view($current_module)
        {
            $view = "";
            switch ($current_module) {
                case 'common':
                    $view = $this->common_handle();
                    break;
                case 'account':
                    $view = $this->account_handle();
                    break;
                case 'price_formula':
                    $view = $this->price_formula();
                    break;
                case 'reviews':
                    $view = $this->reviews();
                    break;
                case 'shipping':
                    $view = $this->shipping();
                    break;
                case 'phrase_filter':
                    $view = $this->phrase_filter();
                    break;
                case 'chrome_api':
                    $view = $this->chrome_api();
                    break;
                case 'system_info':
                    $view = $this->system_info();
                    break;
            }
            return $view;
        }

        private function common_handle()
        {
            global $a2w_settings;
            if (isset($_POST['setting_form'])) {
                a2w_settings()->auto_commit(false);
                a2w_set_setting('item_purchase_code', isset($_POST['a2w_item_purchase_code']) ? wp_unslash($_POST['a2w_item_purchase_code']) : '');

                a2w_set_setting('import_language', isset($_POST['a2w_import_language']) ? wp_unslash($_POST['a2w_import_language']) : 'en');

                if (isset($_POST['a2w_local_currency'])) {
                    $currency = isset($_POST['a2w_local_currency']) ? wp_unslash($_POST['a2w_local_currency']) : 'USD';
                    a2w_set_setting('local_currency', $currency);
                    update_option('woocommerce_currency', $currency);
                }

                a2w_set_setting('default_product_type', isset($_POST['a2w_default_product_type']) ? wp_unslash($_POST['a2w_default_product_type']) : 'simple');
                a2w_set_setting('default_product_status', isset($_POST['a2w_default_product_status']) ? wp_unslash($_POST['a2w_default_product_status']) : 'publish');

                a2w_set_setting('delivered_order_status', isset($_POST['a2w_delivered_order_status']) ? wp_unslash($_POST['a2w_delivered_order_status']) : '');

                a2w_set_setting('tracking_code_order_status', isset($_POST['a2w_tracking_code_order_status']) ? wp_unslash($_POST['a2w_tracking_code_order_status']) : '');

                a2w_set_setting('placed_order_status', isset($_POST['a2w_placed_order_status']) ? wp_unslash($_POST['a2w_placed_order_status']) : '');

                a2w_set_setting('currency_conversion_factor', isset($_POST['a2w_currency_conversion_factor']) ? wp_unslash($_POST['a2w_currency_conversion_factor']) : '1');
                a2w_set_setting('import_product_images_limit', isset($_POST['a2w_import_product_images_limit']) && intval($_POST['a2w_import_product_images_limit']) ? intval($_POST['a2w_import_product_images_limit']) : '');
                a2w_set_setting('import_extended_attribute', isset($_POST['a2w_import_extended_attribute']) ? 1 : 0);

                a2w_set_setting('background_import', isset($_POST['a2w_background_import']) ? 1 : 0);
                a2w_set_setting('allow_product_duplication', isset($_POST['a2w_allow_product_duplication']) ? 1 : 0);
                a2w_set_setting('convert_attr_case', isset($_POST['a2w_convert_attr_case']) ? wp_unslash($_POST['a2w_convert_attr_case']) : 'original');

                a2w_set_setting('remove_ship_from', isset($_POST['a2w_remove_ship_from']) ? 1 : 0);
                a2w_set_setting('default_ship_from', isset($_POST['a2w_default_ship_from']) ? wp_unslash($_POST['a2w_default_ship_from']) : 'CN');

                a2w_set_setting('use_external_image_urls', isset($_POST['a2w_use_external_image_urls']));
                a2w_set_setting('not_import_attributes', isset($_POST['a2w_not_import_attributes']));
                a2w_set_setting('not_import_description', isset($_POST['a2w_not_import_description']));
                a2w_set_setting('not_import_description_images', isset($_POST['a2w_not_import_description_images']));

                a2w_set_setting('use_random_stock', isset($_POST['a2w_use_random_stock']));
                if (isset($_POST['a2w_use_random_stock'])) {
                    $min_stock = (!empty($_POST['a2w_use_random_stock_min']) && intval($_POST['a2w_use_random_stock_min']) > 0) ? intval($_POST['a2w_use_random_stock_min']) : 1;
                    $max_stock = (!empty($_POST['a2w_use_random_stock_max']) && intval($_POST['a2w_use_random_stock_max']) > 0) ? intval($_POST['a2w_use_random_stock_max']) : 1;

                    if ($min_stock > $max_stock) {
                        $min_stock = $min_stock + $max_stock;
                        $max_stock = $min_stock - $max_stock;
                        $min_stock = $min_stock - $max_stock;
                    }
                    a2w_set_setting('use_random_stock_min', $min_stock);
                    a2w_set_setting('use_random_stock_max', $max_stock);
                }

                a2w_set_setting('auto_update', isset($_POST['a2w_auto_update']));
                a2w_set_setting('on_not_available_product', isset($_POST['a2w_on_not_available_product']) ? wp_unslash($_POST['a2w_on_not_available_product']) : 'trash');
                a2w_set_setting('on_not_available_variation', isset($_POST['a2w_on_not_available_variation']) ? wp_unslash($_POST['a2w_on_not_available_variation']) : 'trash');
                a2w_set_setting('on_new_variation_appearance', isset($_POST['a2w_on_new_variation_appearance']) ? wp_unslash($_POST['a2w_on_new_variation_appearance']) : 'add');
                a2w_set_setting('on_price_changes', isset($_POST['a2w_on_price_changes']) ? wp_unslash($_POST['a2w_on_price_changes']) : 'update');
                a2w_set_setting('on_stock_changes', isset($_POST['a2w_on_stock_changes']) ? wp_unslash($_POST['a2w_on_stock_changes']) : 'update');
                a2w_set_setting('untrash_product', isset($_POST['a2w_untrash_product']));
                a2w_set_setting('email_alerts', isset($_POST['a2w_email_alerts']));
                a2w_set_setting('email_alerts_email', isset($_POST['a2w_email_alerts_email']) ? wp_unslash($_POST['a2w_email_alerts_email']) : '');

                a2w_set_setting('fulfillment_prefship', isset($_POST['a2w_fulfillment_prefship']) ? wp_unslash($_POST['a2w_fulfillment_prefship']) : 'ePacket');
                a2w_set_setting('fulfillment_phone_code', isset($_POST['a2w_fulfillment_phone_code']) ? wp_unslash($_POST['a2w_fulfillment_phone_code']) : '');
                a2w_set_setting('fulfillment_phone_number', isset($_POST['a2w_fulfillment_phone_number']) ? wp_unslash($_POST['a2w_fulfillment_phone_number']) : '');
                a2w_set_setting('fulfillment_custom_note', isset($_POST['a2w_fulfillment_custom_note']) ? wp_unslash($_POST['a2w_fulfillment_custom_note']) : '');
                a2w_set_setting('fulfillment_cpf_meta_key', isset($_POST['a2w_fulfillment_cpf_meta_key']) ? wp_unslash($_POST['a2w_fulfillment_cpf_meta_key']) : '');
                a2w_set_setting('fulfillment_rut_meta_key', isset($_POST['a2w_fulfillment_rut_meta_key']) ? wp_unslash($_POST['a2w_fulfillment_rut_meta_key']) : '');

                a2w_set_setting('order_translitirate', isset($_POST['a2w_order_translitirate']));
                a2w_set_setting('order_third_name', isset($_POST['a2w_order_third_name']));
                a2w_set_setting('order_autopay', $_POST['a2w_order_awaiting_payment'] === "no");
                a2w_set_setting('order_awaiting_payment', $_POST['a2w_order_awaiting_payment'] === "yes");

                a2w_settings()->commit();
                a2w_settings()->auto_commit(true);
            }

            $localizator = A2W_AliexpressLocalizator::getInstance();
            $countryModel = new A2W_Country();
            $language_model = new A2W_Language();
            $this->model_put("currencies", $localizator->getCurrencies(false));
            $this->model_put("custom_currencies", $localizator->getCurrencies(true));
            $this->model_put("order_statuses", function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : array());
            $this->model_put("shipping_countries", $countryModel->get_countries());
            $this->model_put("languages", $language_model->get_languages());

            return "settings/common.php";
        }

        private function account_handle()
        {
            $account = A2W_Account::getInstance();

            $token = A2W_AliexpressToken::getInstance();

            if (isset($_POST['setting_form'])) {
                $account->set_account_type(isset($_POST['a2w_account_type']) && in_array($_POST['a2w_account_type'], array('aliexpress', 'admitad', 'epn')) ? $_POST['a2w_account_type'] : 'aliexpress');
                $account->use_custom_account(isset($_POST['a2w_use_custom_account']));
                if ($account->custom_account && isset($_POST['a2w_account_type'])) {
                    if ($_POST['a2w_account_type'] == 'aliexpress') {
                        $account->save_aliexpress_account(isset($_POST['a2w_appkey']) ? $_POST['a2w_appkey'] : '', isset($_POST['a2w_secretkey']) ? $_POST['a2w_secretkey'] : '', isset($_POST['a2w_trackingid']) ? $_POST['a2w_trackingid'] : '');
                    } else if ($_POST['a2w_account_type'] == 'admitad') {
                        $account->save_admitad_account(isset($_POST['a2w_admitad_cashback_url']) ? $_POST['a2w_admitad_cashback_url'] : '');
                    } else if ($_POST['a2w_account_type'] == 'epn') {
                        $account->save_epn_account(isset($_POST['a2w_epn_cashback_url']) ? $_POST['a2w_epn_cashback_url'] : '');
                    }
                }
            }

            $this->model_put("account", $account);

            $this->model_put("tokens", $token->tokens());

            return "settings/account.php";
        }

        private function price_formula()
        {
            $formulas = A2W_PriceFormula::load_formulas();

            if ($formulas) {
                $add_formula = new A2W_PriceFormula();
                $add_formula->min_price = floatval($formulas[count($formulas) - 1]->max_price) + 0.01;
                $formulas[] = $add_formula;
                $this->model_put("formulas", $formulas);
            } else {
                $this->model_put("formulas", A2W_PriceFormula::get_default_formulas());
            }

            $this->model_put("pricing_rules_types", A2W_PriceFormula::pricing_rules_types());

            $this->model_put("default_formula", A2W_PriceFormula::get_default_formula());

            $this->model_put('cents', a2w_get_setting('price_cents'));
            $this->model_put('compared_cents', a2w_get_setting('price_compared_cents'));

            return "settings/price_formula.php";
        }

        private function reviews()
        {
            if (isset($_POST['setting_form'])) {
                a2w_settings()->auto_commit(false);
                a2w_set_setting('load_review', isset($_POST['a2w_load_review']));
                a2w_set_setting('review_status', isset($_POST['a2w_review_status']));
                a2w_set_setting('review_translated', isset($_POST['a2w_review_translated']));
                a2w_set_setting('review_avatar_import', isset($_POST['a2w_review_avatar_import']));

                a2w_set_setting('review_schedule_load_period', 'a2w_15_mins');

                //review number fields
                $review_number_from = 10;
                $review_number_to = 20;

                if (isset($_POST['a2w_review_min_per_product'])) {
                    $review_number_from = intval($_POST['a2w_review_min_per_product']);
                }

                if (isset($_POST['a2w_review_max_per_product'])) {
                    $review_number_to = intval($_POST['a2w_review_max_per_product']);
                }

                if ($review_number_to < 1) {
                    $review_number_to = 20;
                }

                if ($review_number_from < 1 || $review_number_from > $review_number_to) {
                    $review_number_from = $review_number_to;
                }

                a2w_set_setting('review_min_per_product', $review_number_from);
                a2w_set_setting('review_max_per_product', $review_number_to);

                //clear this meta in all products, it will be recalculated during reviews loading
                A2W_Review::clear_all_product_max_number_review_meta();

                //raiting fields
                $raiting_from = 1;
                $raiting_to = 5;
                if (isset($_POST['a2w_review_raiting_from'])) {
                    $raiting_from = intval($_POST['a2w_review_raiting_from']);
                }

                if (isset($_POST['a2w_review_raiting_to'])) {
                    $raiting_to = intval($_POST['a2w_review_raiting_to']);
                }

                if ($raiting_from >= 5) {
                    $raiting_from = 5;
                }

                if ($raiting_from < 1 || $raiting_from > $raiting_to) {
                    $raiting_from = 1;
                }

                if ($raiting_to >= 5) {
                    $raiting_to = 5;
                }

                if ($raiting_to < 1) {
                    $raiting_to = 1;
                }

                a2w_set_setting('review_raiting_from', $raiting_from);
                a2w_set_setting('review_raiting_to', $raiting_to);

                //update more field
                a2w_set_setting('review_load_attributes', isset($_POST['a2w_review_load_attributes']));

                $a2w_review_show_image_list = $_POST['a2w_review_show_image_list'];
                a2w_set_setting('review_show_image_list', isset($a2w_review_show_image_list));

                if (isset($a2w_review_show_image_list)) {
                    $a2w_review_thumb_width = intval($_POST['a2w_review_thumb_width']);

                    if ($a2w_review_thumb_width > 0) {
                        a2w_set_setting('review_thumb_width', $a2w_review_thumb_width);
                    } else {
                        a2w_set_setting('review_thumb_width', 30);
                    }
                }

                a2w_set_setting('review_skip_empty', isset($_POST['a2w_review_skip_empty']));
                a2w_set_setting('review_skip_keywords', isset($_POST['a2w_review_skip_keywords']) ? trim(wp_unslash($_POST['a2w_review_skip_keywords'])) : '');

                if (!isset($_POST['a2w_review_country']) || !is_array($_POST['a2w_review_country'])) {
                    a2w_set_setting('review_country', array());
                } else {
                    a2w_set_setting('review_country', $_POST['a2w_review_country']);
                }

                a2w_set_setting('moderation_reviews', isset($_POST['a2w_moderation_reviews']));

                if (isset($_FILES) && isset($_FILES['a2w_review_noavatar_photo']) && 0 === $_FILES['a2w_review_noavatar_photo']['error']) {

                    if (!function_exists('wp_handle_upload')) {
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                    }

                    $uploadedfile = $_FILES['a2w_review_noavatar_photo'];
                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile) {
                        a2w_set_setting('review_noavatar_photo', $movefile['url']);
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                } else {
                    a2w_del_setting('review_noavatar_photo');
                }

                a2w_settings()->commit();
                a2w_settings()->auto_commit(true);

            }

            $countryModel = new A2W_Country();
            $countries = $countryModel->get_countries();

            unset($countries[0]);

            $this->model_put("reviews_countries", $countries);

            return "settings/reviews.php";
        }

        private function shipping()
        {
            if (isset($_POST['setting_form'])) {

                a2w_set_setting('aliship_shipto', isset($_POST['a2w_aliship_shipto']) ? wp_unslash($_POST['a2w_aliship_shipto']) : 'US');
                a2w_set_setting('aliship_frontend', isset($_POST['a2w_aliship_frontend']));
                a2w_set_setting('default_shipping_class', !empty($_POST['a2w_default_shipping_class']) ? $_POST['a2w_default_shipping_class'] : false);

                if (isset($_POST['a2w_aliship_frontend'])) {

                    if (isset($_POST['default_rule'])) {
                        A2W_ShippingPriceFormula::set_default_formula(new A2W_ShippingPriceFormula($_POST['default_rule']));
                    }

                    a2w_set_setting('aliship_selection_type', isset($_POST['a2w_aliship_selection_type']) ? wp_unslash($_POST['a2w_aliship_selection_type']) : 'popup');

                    a2w_set_setting('aliship_shipping_type', isset($_POST['a2w_aliship_shipping_type']) ? wp_unslash($_POST['a2w_aliship_shipping_type']) : 'new');

                    a2w_set_setting('aliship_shipping_option_text',
                        (isset($_POST['a2w_aliship_shipping_option_text']) && !empty($_POST['a2w_aliship_shipping_option_text'])) ?
                        wp_unslash($_POST['a2w_aliship_shipping_option_text']) : '[{shipping_cost}] {shipping_company} ({delivery_time}) - {country}');

                    a2w_set_setting('aliship_shipping_label', isset($_POST['a2w_aliship_shipping_label']) ? wp_unslash($_POST['a2w_aliship_shipping_label']) : 'Shipping');
                    a2w_set_setting('aliship_free_shipping_label', isset($_POST['a2w_aliship_free_shipping_label']) ? wp_unslash($_POST['a2w_aliship_free_shipping_label']) : 'Free Shipping');

                    a2w_set_setting('aliship_product_enable', isset($_POST['a2w_aliship_product_enable']));

                    if (isset($_POST['a2w_aliship_product_enable'])) {
                        a2w_set_setting('aliship_product_position', isset($_POST['a2w_aliship_product_position']) ? wp_unslash($_POST['a2w_aliship_product_position']) : 'after_cart');

                        a2w_set_setting('aliship_product_not_available_message',
                            (isset($_POST['a2w_aliship_product_not_available_message']) && !empty($_POST['a2w_aliship_product_not_available_message'])) ?
                            wp_unslash($_POST['a2w_aliship_product_not_available_message']) : 'This product can not be delivered to {country}.');
                    }

                    a2w_set_setting('aliship_not_available_remove', isset($_POST['a2w_aliship_not_available_remove']));

                    a2w_set_setting('aliship_not_available_message',
                        (isset($_POST['a2w_aliship_not_available_message']) && !empty($_POST['a2w_aliship_not_available_message'])) ?
                        wp_unslash($_POST['a2w_aliship_not_available_message']) : '[{shipping_cost}] {delivery_time} - {country}');

                    $not_available_shipping_cost = (isset($_POST['a2w_aliship_not_available_cost']) && floatval($_POST['a2w_aliship_not_available_cost']) >= 0) ? floatval($_POST['a2w_aliship_not_available_cost']) : 10;

                    a2w_set_setting('aliship_not_available_cost', $not_available_shipping_cost);

                    $min_time = (isset($_POST['a2w_aliship_not_available_time_min']) && intval($_POST['a2w_aliship_not_available_time_min']) > 0) ? intval($_POST['a2w_aliship_not_available_time_min']) : 20;
                    $max_time = (isset($_POST['a2w_aliship_not_available_time_max']) && intval($_POST['a2w_aliship_not_available_time_max']) > 0) ? intval($_POST['a2w_aliship_not_available_time_max']) : 30;

                    a2w_set_setting('aliship_not_available_time_min', $min_time);
                    a2w_set_setting('aliship_not_available_time_max', $max_time);

                }

            }

            $countryModel = new A2W_Country();

            $this->model_put("shipping_countries", $countryModel->get_countries());

            $this->model_put("shipping_selection_types", A2W_Shipping::get_selection_types());

            $this->model_put("shipping_types", A2W_Shipping::get_shipping_types());

            $this->model_put("selection_position_types", A2W_Shipping::get_selection_position_types());

            $this->model_put("default_formula", A2W_ShippingPriceFormula::get_default_formula());

            $shipping_class = get_terms(array('taxonomy' => 'product_shipping_class', 'hide_empty' => false));
            $this->model_put("shipping_class", $shipping_class ? $shipping_class : array());

            return "settings/shipping.php";
        }

        private function phrase_filter()
        {
            $phrases = A2W_PhraseFilter::load_phrases();

            if ($phrases) {
                $this->model_put("phrases", $phrases);
            } else {
                $this->model_put("phrases", array());
            }

            return "settings/phrase_filter.php";
        }

        private function chrome_api()
        {
            $api_keys = a2w_get_setting('api_keys', array());

            if (!empty($_REQUEST['delete-key'])) {
                foreach ($api_keys as $k => $key) {
                    if ($key['id'] === $_REQUEST['delete-key']) {
                        unset($api_keys[$k]);
                        a2w_set_setting('api_keys', $api_keys);
                        break;
                    }
                }
                wp_redirect(admin_url('admin.php?page=a2w_setting&subpage=chrome_api'));
            } else if (!empty($_POST['a2w_api_key'])) {
                $key_id = $_POST['a2w_api_key'];
                $key_name = !empty($_POST['a2w_api_key_name']) ? $_POST['a2w_api_key_name'] : "New key";

                $is_new = true;
                foreach ($api_keys as &$key) {
                    if ($key['id'] === $key_id) {
                        $key['name'] = $key_name;
                        $is_new = false;
                        break;
                    }
                }

                if ($is_new) {
                    $api_keys[] = array('id' => $key_id, 'name' => $key_name);
                }

                a2w_set_setting('api_keys', $api_keys);

                wp_redirect(admin_url('admin.php?page=a2w_setting&subpage=chrome_api&edit-key=' . $key_id));
            } else if (isset($_REQUEST['edit-key'])) {
                $api_key = array('id' => md5("a2wkey" . rand() . microtime()), 'name' => "New key");
                $is_new = true;
                if (empty($_REQUEST['edit-key'])) {
                    $api_keys[] = $api_key;
                    a2w_set_setting('api_keys', $api_keys);

                    wp_redirect(admin_url('admin.php?page=a2w_setting&subpage=chrome_api&edit-key=' . $api_key['id']));
                } else if (!empty($_REQUEST['edit-key']) && $api_keys && is_array($api_keys)) {
                    foreach ($api_keys as $key) {
                        if ($key['id'] === $_REQUEST['edit-key']) {
                            $api_key = $key;
                            $is_new = false;
                        }
                    }
                }
                $this->model_put("api_key", $api_key);
                $this->model_put("is_new_api_key", $is_new);
            }

            $this->model_put("api_keys", $api_keys);

            return "settings/chrome.php";
        }

        private function system_info()
        {
            if (isset($_POST['setting_form'])) {
                a2w_set_setting('write_info_log', isset($_POST['a2w_write_info_log']));
            }

            $server_ip = '-';
            if (array_key_exists('SERVER_ADDR', $_SERVER)) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } elseif (array_key_exists('LOCAL_ADDR', $_SERVER)) {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            } elseif (array_key_exists('SERVER_NAME', $_SERVER)) {
                $server_ip = gethostbyname($_SERVER['SERVER_NAME']);
            } else {
                // Running CLI
                if (stristr(PHP_OS, 'WIN')) {
                    $server_ip = gethostbyname(php_uname("n"));
                } else {
                    $ifconfig = shell_exec('/sbin/ifconfig eth0');
                    preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
                    $server_ip = $match[1];
                }
            }

            $this->model_put("server_ip", $server_ip);

            return "settings/system_info.php";
        }
    }

}
