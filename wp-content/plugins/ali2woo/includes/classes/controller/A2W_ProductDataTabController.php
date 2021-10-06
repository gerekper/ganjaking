<?php
/**
 * Description of A2W_ProductDataTabController
 *
 * @author Andrey
 * 
 * @autoload: a2w_admin_init
 * 
 * @ajax: true
 */
if (!class_exists('A2W_ProductDataTabController')) {

    class A2W_ProductDataTabController extends A2W_AbstractController {

        public $tab_class = '';
        public $tab_id = '';
        public $tab_title = '';
        public $tab_icon = '';

        public function __construct() {
            parent::__construct();
            $this->tab_class = 'a2w_product_data';
            $this->tab_id = 'a2w_product_data';
            $this->tab_title = 'A2W Data';

            add_action('admin_head', array(&$this, 'on_admin_head'));

            add_action('woocommerce_product_write_panel_tabs', array(&$this, 'product_write_panel_tabs'), 99);
            add_action('woocommerce_product_data_panels', array(&$this, 'product_data_panel_wrap'), 99);
            add_action('woocommerce_process_product_meta', array(&$this, 'process_meta_box'), 1, 2);
            add_action('woocommerce_variation_options_pricing', array(&$this, 'variation_options_pricing'), 20, 3);

            add_action('wp_ajax_a2w_data_remove_deleted_attribute', array($this, 'ajax_remove_deleted_attribute'));
            add_action('wp_ajax_a2w_data_remove_deleted_variation', array($this, 'ajax_remove_deleted_variation'));
            add_action('wp_ajax_a2w_data_last_update_clean', array($this, 'ajax_last_update_clean'));
            add_action('wp_ajax_a2w_update_product_shipping_info', array($this, 'ajax_update_product_shipping_info'));
            add_action('wp_ajax_a2w_remove_product_shipping_info', array($this, 'ajax_remove_product_shipping_info'));
        }

        public function on_admin_head() {
            echo '<style type="text/css">#woocommerce-product-data ul.wc-tabs li.' . $this->tab_class . ' a::before {content: \'\f163\';}</style>';
        }

        public function product_write_panel_tabs() {
            ?>
            <li class="<?php echo $this->tab_class; ?>"><a href="#<?php echo $this->tab_id; ?>"><span><?php echo $this->tab_title; ?></span></a></li>
            <?php
        }

        public function product_data_panel_wrap() {
            ?>
            <div id="<?php echo $this->tab_id; ?>" class="panel <?php echo $this->tab_class; ?> woocommerce_options_panel wc-metaboxes-wrapper" style="display:none">
                <?php $this->render_product_tab_content(); ?>
            </div>
            <?php
        }

        public function render_product_tab_content() {
            global $post;

            $post_id = isset($_REQUEST['post'])?$_REQUEST['post']:"";

            $country_model = new A2W_Country();

            $this->model_put('post_id', $post_id);
            $this->model_put('countries', $country_model->get_countries());
            
            $this->include_view("product_data_tab.php");

        }

        public function process_meta_box($post_id, $post) {
            if (isset($_POST['_a2w_external_id'])) {
                update_post_meta($post_id, '_a2w_external_id', $_POST['_a2w_external_id']);
            } else {
                delete_post_meta($post_id, '_a2w_external_id');
            }

            if (isset($_POST['_a2w_orders_count'])) {
                update_post_meta($post_id, '_a2w_orders_count', $_POST['_a2w_orders_count']);
            } else {
                delete_post_meta($post_id, '_a2w_orders_count');
            }

            update_post_meta($post_id, '_a2w_disable_sync', !empty($_POST['_a2w_disable_sync']) ? 1 : 0);

            update_post_meta($post_id, '_a2w_disable_var_price_change', !empty($_POST['_a2w_disable_var_price_change']) ? 1 : 0);

            update_post_meta($post_id, '_a2w_disable_var_quantity_change', !empty($_POST['_a2w_disable_var_quantity_change']) ? 1 : 0);

            update_post_meta($post_id, '_a2w_disable_add_new_variants', !empty($_POST['_a2w_disable_add_new_variants']) ? 1 : 0);

            if (!empty($_POST['_a2w_last_update'])) {
                update_post_meta($post_id, '_a2w_last_update', $_POST['_a2w_last_update']);
            } else {
                delete_post_meta($post_id, '_a2w_last_update');
            }
            if (!empty($_POST['_a2w_reviews_last_update'])) {
                update_post_meta($post_id, '_a2w_reviews_last_update', $_POST['_a2w_reviews_last_update']);
            } else {
                delete_post_meta($post_id, '_a2w_reviews_last_update');
            }
            if (!empty($_POST['_a2w_review_page'])) {
                update_post_meta($post_id, '_a2w_review_page', $_POST['_a2w_review_page']);
            } else {
                delete_post_meta($post_id, '_a2w_review_page');
            }
        }

        public function variation_options_pricing($loop, $variation_data, $variation) {
            if (!empty($variation_data['_aliexpress_regular_price']) || !empty($variation_data['_aliexpress_price'])) {
                echo '<p class="form-field form-row form-row-first">';
                if (!empty($variation_data['_aliexpress_regular_price'])) {
                    $label = sprintf(__('Aliexpress Regular price (%s)', 'ali2woo'), get_woocommerce_currency_symbol());

                    echo '<label style="cursor: inherit;">' . $label . ':</label>&nbsp;&nbsp;<label style="cursor: inherit;">' . wc_format_localized_price(is_array($variation_data['_aliexpress_regular_price']) ? $variation_data['_aliexpress_regular_price'][0] : $variation_data['_aliexpress_regular_price']) . '</label>';
                }
                echo '&nbsp;</p>';
                echo '<p class="form-field form-row form-row-last">';
                if (!empty($variation_data['_aliexpress_price'])) {
                    $label = sprintf(__('Aliexpress Sale price (%s)', 'ali2woo'), get_woocommerce_currency_symbol());
                    echo '<label style="cursor: inherit;">' . $label . ':</label>&nbsp;&nbsp;<label style="cursor: inherit;">' . wc_format_localized_price(is_array($variation_data['_aliexpress_price']) ? $variation_data['_aliexpress_price'][0] : $variation_data['_aliexpress_price']) . '</label>';
                }
                echo '&nbsp;</p>';
            }
        }

        public function ajax_remove_deleted_attribute() {
            if (!empty($_POST['post_id']) && !empty($_POST['id'])) {
                $deleted_variations_attributes = get_post_meta($_POST['post_id'], '_a2w_deleted_variations_attributes', true);
                if ($deleted_variations_attributes) {
                    foreach ($deleted_variations_attributes as $k => $a) {
                        if ($_POST['id'] == 'all' || $k == sanitize_title($_POST['id'])) {
                            unset($deleted_variations_attributes[$k]);
                        }
                    }
                }
                update_post_meta($_POST['post_id'], '_a2w_deleted_variations_attributes', $deleted_variations_attributes);
            }
            echo json_encode(A2W_ResultBuilder::buildOk());
            wp_die();
        }

        public function ajax_remove_deleted_variation() {
            if (!empty($_POST['post_id'])) {
                $a2w_skip_meta = get_post_meta($_POST['post_id'], "_a2w_skip_meta", true);
                $a2w_skip_meta = $a2w_skip_meta?$a2w_skip_meta:array('skip_vars' => array(), 'skip_images' => array());
                if($_POST['id']=='all'){
                    $a2w_skip_meta['skip_vars'] = array();
                }else{
                    $a2w_skip_meta['skip_vars'] = array_filter(array_diff($a2w_skip_meta['skip_vars'], array($_POST['id'])));
                }
                update_post_meta($_POST['post_id'], "_a2w_skip_meta", $a2w_skip_meta);
            }
            echo json_encode(A2W_ResultBuilder::buildOk());
            wp_die();
        }
        
        public function ajax_last_update_clean() {
            if (!empty($_POST['post_id']) && !empty($_POST['type'])) {
                if($_POST['type'] === 'product'){
                    delete_post_meta($_POST['post_id'], '_a2w_last_update');
                }else if($_POST['type'] === 'review'){
                    delete_post_meta($_POST['post_id'], '_a2w_reviews_last_update');
                }
            }
            echo json_encode(A2W_ResultBuilder::buildOk());
            wp_die();
        }

        public function ajax_update_product_shipping_info() {
            if (!empty($_POST['id']) && isset($_POST['cost']) && !empty($_POST['country_to']) && !empty($_POST['method']) && !empty($_POST['items'])) {
                $shipping_meta = new A2W_ProductShippingMeta($_POST['id']);
                $shipping_meta->save_cost($_POST['cost'], false);
                $shipping_meta->save_method($_POST['method'], false);

                $shipping_meta->save_country_to($_POST['country_to'], false);
                if(isset($_POST['country_from'])){
                    $shipping_meta->save_country_from($_POST['country_from'], false);
                    $shipping_meta->save_items(1, $_POST['country_from'], $_POST['country_to'], $_POST['items'], false);
                }else{
                    $shipping_meta->save_items(1, '', $_POST['country_to'], $_POST['items'], false);
                }
                
                $shipping_meta->save();
                echo json_encode(A2W_ResultBuilder::buildOk());
            }else{
                echo json_encode(A2W_ResultBuilder::buildError('wrong params'));
            }
            wp_die();
        }

        public function ajax_remove_product_shipping_info() {
            if (!empty($_POST['id'])) {
                $shipping_meta = new A2W_ProductShippingMeta($_POST['id']);
                $shipping_meta->save_cost('', false);
                $shipping_meta->save_country_to('', false);
                $shipping_meta->save_method('', false);
                $shipping_meta->save();
                echo json_encode(A2W_ResultBuilder::buildOk());
            }else{
                echo json_encode(A2W_ResultBuilder::buildError('wrong params'));
            }
            wp_die();
        }
    }

}
