<?php

/**
 * Description of SettingPageAjaxController
 *
 * @author Ali2Woo Team
 *
 * @autoload: a2w_admin_init
 *
 * @ajax: true
 */

namespace Ali2Woo;

class SettingPageAjaxController
{
    public const FREE_TARIFF_CODE = 'free';

    public function __construct()
    {

        add_action('wp_ajax_a2w_update_price_rules', array($this, 'ajax_update_price_rules'));

        add_action('wp_ajax_a2w_apply_pricing_rules', array($this, 'ajax_apply_pricing_rules'));

        add_action('wp_ajax_a2w_update_phrase_rules', array($this, 'ajax_update_phrase_rules'));

        add_action('wp_ajax_a2w_apply_phrase_rules', array($this, 'ajax_apply_phrase_rules'));

        add_action('wp_ajax_a2w_get_status_apply_phrase_rules', array($this, 'ajax_get_status_apply_phrase_rules'));

        add_action('wp_ajax_a2w_reset_shipping_meta', array($this, 'ajax_reset_shipping_meta'));

        add_action('wp_ajax_a2w_calc_external_images_count', array($this, 'ajax_calc_external_images_count'));
        add_action('wp_ajax_a2w_calc_external_images', array($this, 'ajax_calc_external_images'));
        add_action('wp_ajax_a2w_load_external_image', array($this, 'ajax_load_external_image'));

        add_action('wp_ajax_a2w_purchase_code_info', array($this, 'ajax_purchase_code_info'));

        add_action('wp_ajax_a2w_build_aliexpress_api_auth_url', array($this, 'ajax_build_aliexpress_api_auth_url'));
        add_action('wp_ajax_a2w_save_access_token', array($this, 'ajax_save_access_token'));
        add_action('wp_ajax_a2w_delete_access_token', array($this, 'ajax_delete_access_token'));

    }

    public function ajax_update_phrase_rules()
    {
        a2w_init_error_handler();

        $result = ResultBuilder::buildOk();
        try {

            PhraseFilter::deleteAll();

            if (isset($_POST['phrases'])) {
                foreach ($_POST['phrases'] as $phrase) {
                    $filter = new PhraseFilter($phrase);
                    $filter->save();
                }
            }

            $result = ResultBuilder::buildOk(array('phrases' => PhraseFilter::load_phrases()));

            restore_error_handler();
        } catch (Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        } catch (Exception $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        echo json_encode($result);

        wp_die();
    }

    public function ajax_apply_phrase_rules()
    {
        a2w_init_error_handler();

        $result = ResultBuilder::buildOk();
        try {
            $product_import_model = new ProductImport();

            $type = isset($_POST['type']) ? $_POST['type'] : false;
            $scope = isset($_POST['scope']) ? $_POST['scope'] : false;

            if ($type === 'products' || $type === 'all_types') {
                if ($scope === 'all' || $scope === 'import') {
                    $products = $product_import_model->get_product_list(false);

                    foreach ($products as $product) {

                        $product = PhraseFilter::apply_filter_to_product($product);
                        $product_import_model->upd_product($product);
                    }
                }

                if ($scope === 'all' || $scope === 'shop') {
                    //todo: update attributes as well
                    PhraseFilter::apply_filter_to_products();
                }
            }

            if ($type === 'all_types' || $type === 'reviews') {

                PhraseFilter::apply_filter_to_reviews();
            }

            if ($type === 'all_types' || $type === 'shippings') {

            }
            restore_error_handler();
        } catch (Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        } catch (Exception $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        echo json_encode($result);

        wp_die();
    }

    public function ajax_update_price_rules()
    {
        a2w_init_error_handler();

        $result = ResultBuilder::buildOk();
        try {
            settings()->auto_commit(false);

            $pricing_rules_types = array_keys(PriceFormula::pricing_rules_types());
            set_setting('pricing_rules_type', $_POST['pricing_rules_type'] && in_array($_POST['pricing_rules_type'], $pricing_rules_types) ? $_POST['pricing_rules_type'] : $pricing_rules_types[0]);

            $use_extended_price_markup = isset($_POST['use_extended_price_markup']) ? filter_var($_POST['use_extended_price_markup'], FILTER_VALIDATE_BOOLEAN) : false;
            $use_compared_price_markup = isset($_POST['use_compared_price_markup']) ? filter_var($_POST['use_compared_price_markup'], FILTER_VALIDATE_BOOLEAN) : false;

            set_setting('price_cents', isset($_POST['cents']) && intval($_POST['cents']) > -1 && intval($_POST['cents']) <= 99 ? intval(wp_unslash($_POST['cents'])) : -1);
            if ($use_compared_price_markup) {
                set_setting('price_compared_cents', isset($_POST['compared_cents']) && intval($_POST['compared_cents']) > -1 && intval($_POST['compared_cents']) <= 99 ? intval(wp_unslash($_POST['compared_cents'])) : -1);
            } else {
                set_setting('price_compared_cents', -1);
            }

            set_setting('use_extended_price_markup', $use_extended_price_markup);
            set_setting('use_compared_price_markup', $use_compared_price_markup);

            set_setting('add_shipping_to_price', !empty($_POST['add_shipping_to_price']) ? filter_var($_POST['add_shipping_to_price'], FILTER_VALIDATE_BOOLEAN) : false);
            set_setting('apply_price_rules_after_shipping_cost', !empty($_POST['apply_price_rules_after_shipping_cost']) ? filter_var($_POST['apply_price_rules_after_shipping_cost'], FILTER_VALIDATE_BOOLEAN) : false);

            settings()->commit();
            settings()->auto_commit(true);

            if (isset($_POST['rules'])) {
                PriceFormula::deleteAll();
                foreach ($_POST['rules'] as $rule) {
                    $formula = new PriceFormula($rule);
                    $formula->save();
                }
            }

            if (isset($_POST['default_rule'])) {
                PriceFormula::set_default_formula(new PriceFormula($_POST['default_rule']));
            }

            $result = ResultBuilder::buildOk(array('rules' => PriceFormula::load_formulas(), 'default_rule' => PriceFormula::get_default_formula(), 'use_extended_price_markup' => $use_extended_price_markup, 'use_compared_price_markup' => $use_compared_price_markup));

            restore_error_handler();
        } catch (Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        } catch (Exception $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        echo json_encode($result);

        wp_die();
    }

    public function ajax_apply_pricing_rules()
    {
        a2w_init_error_handler();

        $result = ResultBuilder::buildOk(array('done' => 1));
        try {
            $product_import_model = new ProductImport();
            /** @var $woocommerce_model  Woocommerce */ 
            $woocommerce_model = A2W()->getDI()->get('Ali2Woo\Woocommerce');

            $type = isset($_POST['type']) ? $_POST['type'] : false;
            $scope = isset($_POST['scope']) ? $_POST['scope'] : false;
            $page = isset($_POST['page']) ? $_POST['page'] : 0;
            $import_page = isset($_POST['import_page']) ? $_POST['import_page'] : 0;

            if ($page == 0 && ($scope === 'all' || $scope === 'import')) {
                $products_count = $product_import_model->get_products_count();

                $update_per_request = a2w_check_defined('A2W_UPDATE_PRODUCT_IN_IMPORTLIST_PER_REQUEST');
                $update_per_request = $update_per_request ? A2W_UPDATE_PRODUCT_IN_IMPORTLIST_PER_REQUEST : 50;

                $products_id_list = $product_import_model->get_product_id_list($update_per_request, $update_per_request * $import_page);
                foreach ($products_id_list as $product_id) {
                    $product = $product_import_model->get_product($product_id);
                    if (!isset($product['disable_var_price_change']) || !$product['disable_var_price_change']) {
                        $product = PriceFormula::apply_formula($product, 2, $type);
                        $product_import_model->upd_product($product);
                    }
                    unset($product);
                }
                unset($products_id_list);

                if (($import_page * $update_per_request + $update_per_request) >= $products_count) {
                    $result = ResultBuilder::buildOk(array('done' => 1, 'info' => 'Import: 100%'));
                } else {
                    $result = ResultBuilder::buildOk(array('done' => 0, 'info' => 'Import: ' . round(100 * ($import_page * $update_per_request + $update_per_request) / $products_count, 2) . '%'));
                }
            }
            if ($result['done'] == 1 && ($scope === 'all' || $scope === 'shop')) {

                $update_per_request = a2w_check_defined('A2W_UPDATE_PRODUCT_PER_REQUEST');
                $update_per_request = $update_per_request ? A2W_UPDATE_PRODUCT_PER_REQUEST : 30;

                $products_count = $woocommerce_model->get_products_count();
                if (($page * $update_per_request + $update_per_request) >= $products_count) {
                    $result = ResultBuilder::buildOk(array('done' => 1, 'info' => 'Shop: 100%'));
                } else {
                    $result = ResultBuilder::buildOk(array('done' => 0, 'info' => 'Shop: ' . round(100 * ($page * $update_per_request + $update_per_request) / $products_count, 2) . '%'));
                }

                $product_ids = $woocommerce_model->get_products_ids($page, $update_per_request);
                foreach ($product_ids as $product_id) {
                    $product = $woocommerce_model->get_product_by_post_id($product_id);
                    if (!isset($product['disable_var_price_change']) || !$product['disable_var_price_change']) {
                        $product = PriceFormula::apply_formula($product, 2, $type);
                        if (isset($product['sku_products']['variations']) && count($product['sku_products']['variations']) > 0) {
                            $woocommerce_model->update_price($product_id, $product['sku_products']['variations'][0]);
                            foreach ($product['sku_products']['variations'] as $var) {
                                $variation_id = get_posts(array('post_type' => 'product_variation', 'fields' => 'ids', 'numberposts' => 100, 'post_parent' => $product_id, 'meta_query' => array(array('key' => 'external_variation_id', 'value' => $var['id']))));
                                $variation_id = $variation_id ? $variation_id[0] : false;
                                if ($variation_id) {
                                    $woocommerce_model->update_price($variation_id, $var);
                                }
                            }
                            wc_delete_product_transients($product_id);
                        }
                    }
                    unset($product);
                }
                unset($product_ids);
            }

            restore_error_handler();
        } catch (Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        } catch (Exception $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        echo json_encode($result);

        wp_die();
    }

    public function ajax_calc_external_images_count()
    {
        echo json_encode(ResultBuilder::buildOk(array('total_images' => Attachment::calc_total_external_images())));
        wp_die();
    }

    public function ajax_calc_external_images()
    {
        $page_size = isset($_POST['page_size']) && intval($_POST['page_size']) > 0 ? intval($_POST['page_size']) : 1000;
        $result = ResultBuilder::buildOk(array('ids' => Attachment::find_external_images($page_size)));
        echo json_encode($result);
        wp_die();
    }

    public function ajax_load_external_image()
    {
        global $wpdb;

        a2w_init_error_handler();

        $attachment_model = new Attachment('local');

        $image_id = isset($_POST['id']) && intval($_POST['id']) > 0 ? intval($_POST['id']) : 0;

        if ($image_id) {
            try {
                $attachment_model->load_external_image($image_id);

                $result = ResultBuilder::buildOk();
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                a2w_print_throwable($e);
                $result = ResultBuilder::buildError($e->getMessage());
            }
        } else {
            $result = ResultBuilder::buildError("load_external_image: waiting for ID...");
        }

        echo json_encode($result);
        wp_die();
    }

    public function ajax_reset_shipping_meta()
    {
        $result = ResultBuilder::buildOk();
        //remove saved shipping meta
        ProductShippingMeta::clear_in_all_product();
        echo json_encode($result);
        wp_die();
    }

    public function ajax_purchase_code_info()
    {
        $result = SystemInfo::server_ping();
        if ($result['state'] !== 'error') {
            $isFreeTariff = empty($result['tariff_code']) || $result['tariff_code'] === self::FREE_TARIFF_CODE;
            $result['tariff_name'] = $isFreeTariff ? 'Free' : ucfirst($result['tariff_code']);

            if ($isFreeTariff){
                //fix how we display limits in lite version
                $result['limits']['reviews'] = 0;
                $result['limits']['shipping'] = 0;
            }

            $valid_to = !empty($result['valid_to']) ? strtotime($result['valid_to']) : false;
            $tariff_to = !empty($result['tariff_to']) ? strtotime($result['tariff_to']) : false;

            $supported_until = ($valid_to && $tariff_to && $tariff_to > $valid_to) ? $tariff_to : $valid_to;

            if ($supported_until && $supported_until < time()) {
                $result['supported_until'] = "Support expired on " . date("F j, Y", $supported_until);
            } else if ($supported_until) {
                $result['supported_until'] = date("F j, Y", $supported_until);
            } else {
                $result['supported_until'] = "";
            }
        }
        echo json_encode($result);
        wp_die();
    }

    public function ajax_build_aliexpress_api_auth_url(): void
    {
        $state = urlencode(trailingslashit(get_bloginfo('wpurl')));

        $result = [
            'state' => 'ok',
            'url' => $this->buildAuthEndpointUrl($state)
        ];
    
        
        $pc = get_setting('item_purchase_code');
        if ($pc) {
            // $pc = md5($pc);
            $state = $state . ";" . $pc;
            $result = [
                'state' => 'ok',
                'url' => $this->buildAuthEndpointUrl($state)
            ];

        } else {
            $result = [
                'state' => 'error',
                'message' => 'Input your purchase code in the plugin settings'
            ];
        }
        

        echo json_encode($result);
        wp_die();
    }

    private function buildAuthEndpointUrl(string $state): string
    {
        $authEndpoint = 'https://api-sg.aliexpress.com/oauth/authorize';
        $redirectUri = get_setting('api_endpoint').'auth.php&state=' . $state;
        $clientId = get_setting('client_id');

        return sprintf(
            '%s?response_type=code&force_auth=true&redirect_uri=%s&client_id=%s',
            $authEndpoint,
            $redirectUri,
            $clientId
        );
    }

    public function ajax_save_access_token()
    {
        $result = array('state' => 'error', 'message' => 'Wrong params');
        if (isset($_POST['token'])) {
            $token = AliexpressToken::getInstance();
            $token->add($_POST['token']);
			//todo: have to think about this method, perhaps it should be refactored
            Utils::clear_system_error_messages();

            $tokens = $token->tokens();
            $data = '';
            foreach ($tokens as $t) {
                $data .= '<tr>';
                $data .= '<td>' . esc_attr($t['user_nick']) . '</td>';
                $data .= '<td>' . esc_attr(date("F j, Y, H:i:s", round($t['expire_time'] / 1000))) . '</td>';
                $data .= '<td><input type="checkbox" class="default" value="yes" ' . (isset($t['default']) && $t['default'] ? " checked" : "") . '/></td>';
                $data .= '<td><a href="#" data-token-id="' . $t['user_id'] . '">Delete</a></td>';
                $data .= '</tr>';
            }
            $result = array('state' => 'ok', 'data' => $data);
        }

        echo json_encode($result);
        wp_die();
    }

    public function ajax_delete_access_token()
    {
        $result = array('state' => 'error', 'message' => 'Wrong params');
        if (isset($_POST['id'])) {
            $token = AliexpressToken::getInstance();
            $token->del($_POST['id']);
            $result = array('state' => 'ok');
        }
        echo json_encode($result);
        wp_die();
    }
}
