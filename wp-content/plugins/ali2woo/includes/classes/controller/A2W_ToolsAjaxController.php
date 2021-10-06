<?php

/**
 * Description of A2W_ToolsAjaxController
 *
 * @author andrey
 * 
 * @autoload: a2w_admin_init
 * 
 * @ajax: true
 */
if (!class_exists('A2W_ToolsAjaxController')) {

    class A2W_ToolsAjaxController {

        private $importClazz = "A2W_ImportShopmaster";

        public function __construct() {
            add_action('wp_ajax_a2w_convert_sm_product', array($this, 'ajax_convert_sm_product'));

            if (a2w_check_defined('A2W_SHOPMASTER_IMPORT_CLASS')) {
                $this->importClazz = A2W_SHOPMASTER_IMPORT_CLASS;
            }
        }

        public function ajax_convert_sm_product(){
            $result = A2W_ResultBuilder::buildOk();
            if (empty($_POST['id']) || empty($_POST['file'])) {
                $result = A2W_ResultBuilder::buildError("id and file is required");
            }            
            if($result['state'] != 'error'){
                $post_id = $_POST['id'];

                $dir  = wp_upload_dir();
                $import = new $this->importClazz($dir['basedir'].$_POST['file']);
                if(!$import->validate()){
                    $result = A2W_ResultBuilder::buildError('invalid csv file');
                }

                if($result['state'] != 'error'){
                    $result = $import->convert($post_id);

                    if($result['state']!='error' 
                        && ($external_id =  get_post_meta($post_id, '_a2w_external_id', true))
                        && get_post_meta($post_id, '_a2w_need_convert', true)
                    ){  
                        $woocommerce_model = new A2W_Woocommerce();
                        $aliexpress_model = new A2W_Aliexpress();

                        $sync_id = $external_id.';'.get_post_meta($post_id, '_a2w_import_lang', true);
                        $res = $aliexpress_model->sync_products($sync_id, array('manual_update'=>1));
                        if ($res['state'] === 'error') {
                            $result = $res;
                        } else {
                            foreach ($res['products'] as $product) {

                                $on_price_changes = a2w_get_setting('on_price_changes');
                                $on_stock_changes = a2w_get_setting('on_stock_changes');

                                $import->convert_product($post_id, $product);

                                $wc_product = $woocommerce_model->get_product_by_post_id($post_id, false);

                                if ($wc_product) {
                                    $wc_product['disable_var_price_change'] = $wc_product['disable_var_price_change'] || $on_price_changes !== "update";
                                    $wc_product['disable_var_quantity_change'] = $wc_product['disable_var_quantity_change'] || $on_stock_changes !== "update";
                                }

                                $product = array_replace_recursive($wc_product, $product);
                                $product = A2W_PriceFormula::apply_formula($product);
                                $woocommerce_model->upd_product($product['post_id'], $product, array('manual_update'=>1));

                                delete_post_meta($post_id, '_a2w_tmp_convert_data');
                                delete_post_meta($post_id, '_a2w_need_convert');

                                break;
                            }
                        }
                    }
                }
            }

            echo json_encode($result);
            wp_die();
        }
        
    }
}
