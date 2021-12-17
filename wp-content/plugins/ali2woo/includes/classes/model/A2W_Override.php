<?php

/**
 * Description of A2W_Override
 *
 * @author Andrey
 */

if (!class_exists('A2W_Override')) {
    class A2W_Override
    {

        public function __construct()
        {}

        public function has_override($product_id)
        {
            global $wpdb;

            if ($product_id) {
                return !!$wpdb->get_var("SELECT 1 FROM $wpdb->options WHERE option_name like '%a2w_product%' AND option_value like '%\"override_product_id\";i:" . intval($product_id) . "%'");
            } else {
                return false;
            }
        }

        public function override($product_id, $external_id, $change_supplier = false, $override_images = false, $override_title_description = false, $variations = array())
        {
            global $wpdb;

            $result = array("state" => "error", "message" => "Product not found.");

            if ($this->has_override($product_id)) {
                $result = array(
                    "state" => "error",
                    "message" => __("You've already selected to override this product. Check your import list and confirm the override to continue.", "ali2woo"),
                );
            } else {
                $override_product = $wpdb->get_row($wpdb->prepare("SELECT p.ID, p.post_title FROM $wpdb->posts p WHERE p.ID=%d", $product_id), ARRAY_A);
                if ($override_product) {
                    $product_import_model = new A2W_ProductImport();
                    $product = $product_import_model->get_product($external_id);
                    if ($product) {
                        $product['override_product_id'] = intval($product_id);
                        $product['override_product_title'] = $override_product['post_title'];
                        $product['override_supplier'] = $change_supplier;
                        $product['override_images'] = $override_images;
                        $product['override_title_description'] = $override_title_description;
                        $product['override_variations'] = $variations;

                        $product_import_model->upd_product($product);

                        $result = array(
                            'state' => 'ok',
                            'product_id' => $product_id, 'external_id' => $external_id,
                            'html' => $this->override_message($product_id, $product['override_product_title']),
                        );
                    }
                }
            }

            return $result;
        }

        public function find_orders($product_id)
        {
            global $wpdb;
            $query = "SELECT variation_id, max(variation_attributes) as variation_attributes, max(thumbnail) as thumbnail, count(order_id) as cnt FROM (SELECT wi.order_id as order_id, wim2.meta_value as variation_id, group_concat(t1.name SEPARATOR '#') as variation_attributes, max(p2.guid) as thumbnail FROM {$wpdb->prefix}woocommerce_order_items wi INNER JOIN {$wpdb->posts} p1 ON (p1.ID=wi.order_id and not p1.post_status in ('wc-completed', 'wc-cancelled', 'wc-refunded')) INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta wim1 on (wi.order_item_id=wim1.order_item_id AND wim1.meta_key='_product_id' AND wim1.meta_value=%d) INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta wim2 on (wi.order_item_id=wim2.order_item_id AND wim2.meta_key='_variation_id') INNER JOIN {$wpdb->postmeta} pm1 on (pm1.post_id=wim2.meta_value AND pm1.meta_key like'attribute_%') LEFT JOIN {$wpdb->postmeta} pm2 on (pm2.post_id=wim2.meta_value AND pm2.meta_key like'_thumbnail_id') LEFT JOIN {$wpdb->posts} p2 on (pm2.meta_value=p2.ID) INNER JOIN {$wpdb->term_taxonomy} tt1 on (tt1.taxonomy=substring(pm1.meta_key, 11)) INNER JOIN {$wpdb->terms} t1 on (t1.term_id=tt1.term_id and t1.slug=pm1.meta_value) GROUP BY order_id, variation_id) as q GROUP BY variation_id";
            return $wpdb->get_results($wpdb->prepare($query, $product_id), ARRAY_A);
        }

        public function find_variations($product_id)
        {
            global $wpdb;
            $query = "SELECT p.id as variation_id, group_concat(t1.name SEPARATOR '#') as variation_attributes, max(p2.guid) thumbnail FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm1 on (pm1.post_id=p.ID AND pm1.meta_key like'attribute_%') LEFT JOIN {$wpdb->postmeta} pm2 on (pm2.post_id=p.ID AND pm2.meta_key like'_thumbnail_id') LEFT JOIN {$wpdb->posts} p2 on (pm2.meta_value=p2.ID) INNER JOIN {$wpdb->term_taxonomy} tt1 on (tt1.taxonomy=substring(pm1.meta_key, 11)) INNER JOIN {$wpdb->terms} t1 on (t1.term_id=tt1.term_id and t1.slug=pm1.meta_value) WHERE p.post_type='product_variation' AND p.post_parent=%d GROUP BY variation_id";
            return $wpdb->get_results($wpdb->prepare($query, $product_id), ARRAY_A);
        }

        public function cancel_override($external_id)
        {
            global $wpdb;

            $result = array("state" => "error", "message" => "Product not found.");

            $product_import_model = new A2W_ProductImport();

            $product = $product_import_model->get_product($external_id);

            if ($product) {
                unset($product['override_product_id']);
                unset($product['override_product_title']);
                unset($product['override_supplier']);
                unset($product['override_images']);
                unset($product['override_title_description']);
                unset($product['override_variations']);

                $product_import_model->upd_product($product, false);

                $result = array('state' => 'ok');
            }

            return $result;
        }

        public function override_message($product_id, $product_title)
        {
            $msg = sprintf(__('This product will override <a href="%s">%s</a> Click "Override" to proceed.', 'ali2woo'),
                admin_url('post.php?post=' . intval($product_id) . '&action=edit'),
                $product_title
            );
            $btn = '<button class="btn btn-default cancel-override" type="button">' . __('Cancel Override', 'ali2woo') . '</button>';
            return '<div><div style="padding-bottom:8px;">' . $msg . '</div>' . $btn . '</div>';
        }
    }

}
