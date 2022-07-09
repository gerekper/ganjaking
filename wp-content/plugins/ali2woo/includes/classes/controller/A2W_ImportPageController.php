<?php

/**
 * Description of A2W_ImportPageController
 *
 * @author Andrey
 *
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_ImportPageController')) {

    class A2W_ImportPageController extends A2W_AbstractAdminPage
    {
        public function __construct()
        {
            $products_cnt = 0;
            if (is_admin()) {
                $product_import_model = new A2W_ProductImport();
                $products_cnt = $product_import_model->get_products_count();
            }

            parent::__construct(__('Import List', 'ali2woo'), __('Import List', 'ali2woo') . ' ' . ($products_cnt ? ' <span class="update-plugins count-' . $products_cnt . '"><span class="plugin-count">' . $products_cnt . '</span></span>' : ''), 'import', 'a2w_import', 20);

            add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init'), 30);
        }

        public function before_admin_render()
        {
            $product_import_model = new A2W_ProductImport();
            if (isset($_REQUEST['delete_id']) && $_REQUEST['delete_id']) {
                if ($product = $product_import_model->get_product($_REQUEST['delete_id'])) {
                    foreach ($product['tmp_edit_images'] as $edit_image) {
                        if (isset($edit_image['attachment_id'])) {
                            A2W_Utils::delete_attachment($edit_image['attachment_id'], true);
                        }
                    }
                    $product_import_model->del_product($_REQUEST['delete_id']);
                }
                wp_redirect(admin_url('admin.php?page=a2w_import'));
            } else if ((isset($_REQUEST['action']) && $_REQUEST['action'] == "delete_all") || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == "delete_all")) {
                $product_ids = $product_import_model->get_product_id_list();

                foreach ($product_ids as $product_id) {
                    if ($product = $product_import_model->get_product($product_id)) {
                        foreach ($product['tmp_edit_images'] as $edit_image) {
                            if (isset($edit_image['attachment_id'])) {
                                A2W_Utils::delete_attachment($edit_image['attachment_id'], true);
                            }
                        }
                    }
                }

                $product_import_model->del_product($product_ids);

                wp_redirect(admin_url('admin.php?page=a2w_import'));
            } else if ((isset($_REQUEST['action']) && $_REQUEST['action'] == "push_all") || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == "push_all")) {
                // push all
                wp_redirect(admin_url('admin.php?page=a2w_import'));
            } else if (((isset($_REQUEST['action']) && $_REQUEST['action'] == "delete") || (isset($_REQUEST['action2']) && $_REQUEST['action2'] == "delete")) && isset($_REQUEST['gi']) && is_array($_REQUEST['gi']) && $_REQUEST['gi']) {
                $product_import_model->del_product($_REQUEST['gi']);

                wp_redirect(admin_url('admin.php?page=a2w_import'));
            }
        }

        public function render($params = array())
        {
            $product_import_model = new A2W_ProductImport();
            $woocommerce_model = new A2W_Woocommerce();
            $country_model = new A2W_Country();
            $override_model = new A2W_Override();

            $serach_query = !empty($_REQUEST['s']) ? $_REQUEST['s'] : '';
            $sort_query = !empty($_REQUEST['o']) ? $_REQUEST['o'] : $product_import_model->default_sort();

            $default_shipping_from_country = a2w_get_setting('aliship_shipfrom', 'CN');
            $default_shipping_to_country = a2w_get_setting('aliship_shipto', 'US');

            $products_cnt = $product_import_model->get_products_count();
            $paginator = A2W_Paginator::build($products_cnt);

            if (a2w_check_defined('A2W_SKIP_IMPORT_SORTING')) {
                $product_list = $product_import_model->get_product_list(true, $serach_query, $sort_query, $paginator['per_page'], ($paginator['cur_page'] - 1) * $paginator['per_page']);
            } else {
                $product_list_all = $product_import_model->get_product_list(true, $serach_query, $sort_query);
                $product_list = array_slice($product_list_all, $paginator['per_page'] * ($paginator['cur_page'] - 1), $paginator['per_page']);
                unset($product_list_all);
            }
            foreach ($product_list as &$product) {
                if (empty($product['sku_products'])) {
                    $product['sku_products'] = array('variations' => array(), 'attributes' => array());
                }

                $tmp_all_images = A2W_Utils::get_all_images_from_product($product);

                if (empty($product['description'])) {
                    $product['description'] = '';
                }

                $product['gallery_images'] = array();
                $product['variant_images'] = array();
                $product['description_images'] = array();

                foreach ($tmp_all_images as $img_id => $img) {
                    if ($img['type'] === 'gallery') {
                        $product['gallery_images'][$img_id] = $img['image'];
                    } else if ($img['type'] === 'variant') {
                        $product['variant_images'][$img_id] = $img['image'];
                    } else if ($img['type'] === 'description') {
                        $product['description_images'][$img_id] = $img['image'];
                    }
                }
                foreach ($product['tmp_copy_images'] as $img_id => $source) {
                    if (isset($tmp_all_images[$img_id])) {
                        $product['gallery_images'][$img_id] = $tmp_all_images[$img_id]['image'];
                    }
                }

                foreach ($product['tmp_move_images'] as $img_id => $source) {
                    if (isset($tmp_all_images[$img_id])) {
                        $product['gallery_images'][$img_id] = $tmp_all_images[$img_id]['image'];
                    }
                }

                if (!isset($product['thumb_id']) && $product['gallery_images']) {
                    $k = array_keys($product['gallery_images']);
                    $product['thumb_id'] = $k[0];
                }
            }

            $this->model_put("paginator", $paginator);
            $this->model_put("serach_query", $serach_query);
            $this->model_put("sort_query", $sort_query);
            $this->model_put("sort_list", $product_import_model->sort_list());
            $this->model_put("product_list", $product_list);
            $this->model_put("localizator", A2W_AliexpressLocalizator::getInstance());
            $this->model_put("categories", $woocommerce_model->get_categories());
            $this->model_put('countries', $country_model->get_countries());
            $this->model_put('override_model', $override_model);
            

            $this->include_view("import.php");
        }

        public function tiny_mce_before_init($initArray)
        {
            if ($this->is_current_page()) {
                $initArray['setup'] = 'function(ed) {ed.on("change", function(e) { a2w_update_product(e.target.id, { description:encodeURIComponent(e.target.getContent())}); });}';
            }
            return $initArray;
        }

    }

}
