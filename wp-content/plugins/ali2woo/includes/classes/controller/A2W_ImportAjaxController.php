<?php

/**
 * Description of A2W_ImportAjaxController
 *
 * @author Andrey
 * 
 * @autoload: a2w_admin_init
 * 
 * @ajax: true
 */
if (!class_exists('A2W_ImportAjaxController')) {

    class A2W_ImportAjaxController {
        public function __construct() {
            add_filter('a2w_woocommerce_after_add_product', array($this, 'woocommerce_after_add_product'), 30, 4);

            add_action('wp_ajax_a2w_push_product', array($this, 'ajax_push_product'));
            add_action('wp_ajax_a2w_delete_import_products', array($this, 'ajax_delete_import_products'));
            add_action('wp_ajax_a2w_update_product_info', array($this, 'ajax_update_product_info'));
            add_action('wp_ajax_a2w_link_to_category', array($this, 'ajax_link_to_category'));
            add_action('wp_ajax_a2w_get_all_products_to_import', array($this, 'ajax_get_all_products_to_import'));
            add_action('wp_ajax_a2w_get_product', array($this, 'ajax_get_product'));
            add_action('wp_ajax_a2w_split_product', array($this, 'ajax_split_product'));
            add_action('wp_ajax_a2w_import_images_action', array($this, 'ajax_import_images_action'));
            add_action('wp_ajax_a2w_import_cancel_images_action', array($this, 'ajax_import_cancel_images_action'));
            add_action('wp_ajax_a2w_search_tags', array($this, 'ajax_search_tags'));
            add_action('wp_ajax_a2w_search_products', array($this, 'ajax_search_products'));
            add_action('wp_ajax_a2w_override_product', array($this, 'ajax_override_product'));
            add_action('wp_ajax_a2w_override_order_variations', array($this, 'ajax_override_order_variations'));
            add_action('wp_ajax_a2w_cancel_override_product', array($this, 'ajax_cancel_override_product'));
            add_action('wp_ajax_a2w_add_to_import', array($this, 'ajax_add_to_import'));
            add_action('wp_ajax_a2w_remove_from_import', array($this, 'ajax_remove_from_import'));
            add_action('wp_ajax_a2w_load_shipping_info', array($this, 'ajax_load_shipping_info'));
            add_action('wp_ajax_a2w_set_shipping_info', array($this, 'ajax_set_shipping_info'));
            add_action('wp_ajax_a2w_update_shipping_list', array($this, 'ajax_update_shipping_list'));
        }

        public function woocommerce_after_add_product($result, $product_id, $product, $params){
            $product_import_model = new A2W_ProductImport();
            // remove product from process list and from import list
            $product_import_model->del_product($product['import_id'], true);
            $product_import_model->del_product($product['import_id']);
            return $result;
        }

        public function ajax_push_product() {
            a2w_init_error_handler();

            $result = A2W_ResultBuilder::buildOk();

            $product_import_model = new A2W_ProductImport();
            $woocommerce_model = new A2W_Woocommerce();
            $reviews_model = new A2W_Review();

            $background_import = a2w_get_setting('background_import', true);

            if($background_import){
                // NEW import method (in background)
                if (isset($_POST['id']) && $_POST['id']) {
                    $product = $product_import_model->get_product($_POST['id']);
                    if ($product) {
                        try {
                            $ts = microtime(true);

                            $steps = $woocommerce_model->build_steps($product);
                            
                            // process first step
                            $result = $woocommerce_model->add_product($product, array('step'=>'init'));
                            unset($steps[array_search('init', $steps)]);    
                            
                            if ($result['state'] !== 'error') {
                                // write firt step log
                                a2w_info_log("IMPORT[time: ".(microtime(true)-$ts).", id:".$result['product_id'].", extId: ".$_POST['id'].", step: init]");

                                // move product to pricessing list
                                $product_import_model->move_to_processing($_POST['id']);

                                // process all other steps
                                $product_queue = A2W_ImportProcess::create_new_queue($result['product_id'], $_POST['id'], $steps, false);
                                if(a2w_get_setting('load_review')){
                                    A2W_ImportProcess::create_new_queue($result['product_id'], $_POST['id'], array('reviews'), false);
                                }
                                $product_queue->dispatch();
                            }
                        } catch (Throwable $e) {
                            a2w_print_throwable($e);
                            $result = A2W_ResultBuilder::buildError($e->getMessage());
                        } catch (Exception $e) {
                            a2w_print_throwable($e);
                            $result = A2W_ResultBuilder::buildError($e->getMessage());
                        }
                    } else {
                        $result = A2W_ResultBuilder::buildError("Product " . $_POST['id'] . " not find.");
                    }
                } else {
                    $result = A2W_ResultBuilder::buildError("import_product: waiting for ID...");
                }
            }else{
                // Old import method 
                ini_set("memory_limit",-1);
                set_time_limit(0);
                ignore_user_abort(true);

                if(!a2w_check_defined('A2W_DO_NOT_USE_TRANSACTION')){
                    global $wpdb;

                    wp_defer_term_counting(true);
                    wp_defer_comment_counting(true );
                    $wpdb->query('SET autocommit = 0;');

                    register_shutdown_function(function(){
                        global $wpdb;
                        $wpdb->query('COMMIT;');
                        wp_defer_term_counting(false);
                        wp_defer_comment_counting(false);
                    });
                }

                try {
                    if (isset($_POST['id']) && $_POST['id']) {
                        $product = $product_import_model->get_product($_POST['id']);

                        if ($product) {
                            $import_wc_product_id = $woocommerce_model->get_product_id_by_import_id($product['import_id']);
                            if(!a2w_check_defined('A2W_ALLOW_PRODUCT_DUPLICATION') && $import_wc_product_id){
                                $result = $woocommerce_model->upd_product($import_wc_product_id, $product);
                            }else{
                                $result = $woocommerce_model->add_product($product);
                            }

                            $product_id = false;
                            if ($result['state'] !== 'error') {
                                $product_id = $result['product_id'];
                                $product_import_model->del_product($_POST['id']);
                                $result = A2W_ResultBuilder::buildOk();
                            } else {
                                $result = A2W_ResultBuilder::buildError($result['message']);
                            }

                            if ($result['state'] !== 'error' && a2w_get_setting('load_review')) {
                                $reviews_model->load($product_id, true);
                                //make sure that post comment status is 'open'
                                wp_update_post(array('ID' => $product_id,'comment_status'=>'open'));
                            }
                            if ($result['state'] === 'error') {
                                $result = A2W_ResultBuilder::buildError($result['message']);
                            }

                        } else {
                            $result = A2W_ResultBuilder::buildError("Product " . $_POST['id'] . " not find.");
                        }
                    } else {
                        $result = A2W_ResultBuilder::buildError("import_product: waiting for ID...");
                    }

                    restore_error_handler();
                } catch (Throwable $e) {
                    a2w_print_throwable($e);
                    $result = A2W_ResultBuilder::buildError($e->getMessage());
                } catch (Exception $e) {
                    a2w_print_throwable($e);
                    $result = A2W_ResultBuilder::buildError($e->getMessage());
                }
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_delete_import_products() {
            a2w_init_error_handler();
            try {
                if (isset($_POST['ids']) && $_POST['ids']) {
                    $product_import_model = new A2W_ProductImport();
                    $product_import_model->del_product($_POST['ids']);
                }
                $result = A2W_ResultBuilder::buildOk();
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

        public function ajax_update_product_info() {
            a2w_init_error_handler();
            try {
                $product_import_model = new A2W_ProductImport();
                $out_data = array();
                if (isset($_POST['id']) && $_POST['id'] && ($product = $product_import_model->get_product($_POST['id']))) {
                    if (isset($_POST['title']) && $_POST['title']) {
                        if(!isset($product['original_title'])) {
                            $product['original_title'] = $product['title'];
                        }
                        // $product['title'] = stripslashes($_POST['title']);
                        $product['title'] = sanitize_text_field($_POST['title']);
                    }

                    if (!empty($_POST['sku'])) {
                        $product['sku'] = stripslashes($_POST['sku']);
                    }

                    if (isset($_POST['type']) && $_POST['type'] && in_array($_POST['type'], array('simple', 'external'))) {
                        $product['product_type'] = $_POST['type'];
                    }
                    
                    if (isset($_POST['status']) && $_POST['status'] && in_array($_POST['status'], array('publish', 'draft'))) {
                        $product['product_status'] = $_POST['status'];
                    }

                    if (isset($_POST['tags']) && $_POST['tags']) {
                        $product['tags'] = $_POST['tags']?array_map('sanitize_text_field', $_POST['tags']):array();
                    }
                    
                    if (!empty($_POST['attr_names'])) {
                        foreach($_POST['attr_names'] as $attr){
                            foreach($product['sku_products']['attributes'] as &$product_attr){
                                if($product_attr['id'] == $attr['id']){
                                    if(!isset($product_attr['original_name'])){
                                        $product_attr['original_name'] = $product_attr['name'];
                                    }
                                    $product_attr['name'] = $attr['value'];
                                    break;
                                }
                            }
                        }
                    }

                    if (isset($_POST['categories'])) {
                        $product['categories'] = array();
                        if($_POST['categories']){
                            foreach ($_POST['categories'] as $cat_id) {
                                if (intval($cat_id)) {
                                    $product['categories'][] = intval($cat_id);
                                }
                            }
                        }
                        
                    }

                    if (isset($_POST['description'])) {
                        $product['description'] = stripslashes(trim(urldecode($_POST['description'])));
                    }

                    if (isset($_POST['skip_vars']) && $_POST['skip_vars']) {
                        $product['skip_vars'] = $_POST['skip_vars'];
                    }

                    if (isset($_POST['reset_skip_vars']) && $_POST['reset_skip_vars']) {
                        $product['skip_vars'] = array();
                    }

                    if (isset($_POST['skip_images']) && $_POST['skip_images']) {
                        $product['skip_images'] = $_POST['skip_images'];
                    }

                    if (!empty($_POST['no_skip'])) {
                        $product['skip_images'] = array();
                    }

                    if (isset($_POST['thumb'])) {
                        $product['thumb_id'] = $_POST['thumb'];
                    }

                    if (isset($_POST['specs'])) {
                        $product['attribute'] = array();
                        $split_attribute_values = a2w_get_setting('split_attribute_values');
                        $attribute_values_separator = a2w_get_setting('attribute_values_separator');
                        foreach($_POST['specs'] as $attr){
                            $name = trim($attr['name']);
                            if(!empty($name)){
                                $el = array('name'=>$name);
                                if ($split_attribute_values) {
                                    $el['value'] = array_map('trim', explode($attribute_values_separator, $attr['value']));
                                } else {
                                    $el['value'] = array($attr['value']);
                                }
                                $product['attribute'][] = $el;
                            }
                        }
                    }else if (!empty($_POST['cleanSpecs'])){
                        $product['attribute'] = array();
                    }

                    if (isset($_POST['disable_var_price_change'])) {
                        if (intval($_POST['disable_var_price_change'])) {
                            $product['disable_var_price_change'] = true;
                        } else {
                            $product['disable_var_price_change'] = false;
                        }
                    }
                    
                    if (isset($_POST['disable_var_quantity_change'])) {
                        if (intval($_POST['disable_var_quantity_change'])) {
                            $product['disable_var_quantity_change'] = true;
                        } else {
                            $product['disable_var_quantity_change'] = false;
                        }
                    }

                    if (!empty($_POST['variations'])) {
                        $out_data['new_attr_mapping'] = array();
                        foreach ($_POST['variations'] as $variation) {
                            foreach ($product['sku_products']['variations'] as &$v) {
                                if ($v['id'] == $variation['variation_id']) {
                                    if (isset($variation['regular_price'])) {
                                        $v['calc_regular_price'] = floatval($variation['regular_price']);
                                    }
                                    if (isset($variation['price'])) {
                                        $v['calc_price'] = floatval($variation['price']);
                                    }
                                    if (isset($variation['quantity'])) {
                                        $v['quantity'] = intval($variation['quantity']);
                                    }

                                    if (isset($variation['sku']) && $variation['sku']) {
                                        $v['sku'] = sanitize_text_field($variation['sku']);
                                    }

                                    if (isset($variation['attributes']) && is_array($variation['attributes'])) {
                                        foreach ($variation['attributes'] as $a) {
                                            foreach ($v['attributes'] as $i => $av) {
                                                $_attr_val = false;
                                                foreach ($product['sku_products']['attributes'] as $tmp_attr) {
                                                    if(isset($tmp_attr["value"][$av])){
                                                        $_attr_val = $tmp_attr["value"][$av];
                                                        break;
                                                    }
                                                }
                                                $old_name = sanitize_text_field($_attr_val['name']);
                                                $new_name = sanitize_text_field($a['value']);
                                                if ($old_name!==$new_name && $_attr_val['id'] == $a['id']) {
                                                    $_attr_id = explode(':', $av);
                                                    $attr_id = $_attr_id[0];
                                                    $new_attr_id = $attr_id . ':' . md5($variation['variation_id'].$new_name);
                                                    if ($av !== $new_attr_id) {
                                                        $out_data['new_attr_mapping'][] = array('variation_id' => $variation['variation_id'], 'old_attr_id' => $av, 'new_attr_id' => $new_attr_id);
                                                    }
                                                    foreach ($product['sku_products']['attributes'] as $ind => $orig_attr) {
                                                        if ($orig_attr['id'] == $attr_id) {
                                                            if (!isset($orig_attr['value'][$new_attr_id])) {
                                                                $product['sku_products']['attributes'][$ind]['value'][$new_attr_id] = $product['sku_products']['attributes'][$ind]['value'][$av];
                                                                if(!isset($product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['original_id'])){
                                                                    $product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['original_id'] = $product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['id'];
                                                                }
                                                                $product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['id'] = $new_attr_id;
                                                                $product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['name'] = $new_name;
                                                                if (!isset($product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['src_id'])) {
                                                                    $product['sku_products']['attributes'][$ind]['value'][$new_attr_id]['src_id'] = $av;
                                                                }
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    $v['attributes'][$i] = $new_attr_id;
                                                    $v['attributes_names'][$i] = sanitize_text_field($a['value']);
                                                }
                                            }
                                        }
                                    }

                                    break;
                                }
                            }
                        }
                    }

                    $product_import_model->upd_product($product);
                    $result = A2W_ResultBuilder::buildOk($out_data);
                } else {
                    $result = A2W_ResultBuilder::buildError("update_product_info: waiting for ID...");
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

        public function ajax_link_to_category() {
            $product_import_model = new A2W_ProductImport();
            if (!empty($_POST['categories']) && !empty($_POST['ids'])) {
                $new_categories = is_array($_POST['categories']) ? array_map('intval', $_POST['categories']) : array(intval($_POST['categories']));
                $ids = (is_string($_POST['ids']) && $_POST['ids']==='all')?$product_import_model->get_product_id_list():(is_array($_POST['ids']) ? $_POST['ids'] : array($_POST['ids']));
                foreach ($ids as $id) {
                    if ($product = $product_import_model->get_product($id)) {
                        $product['categories'] = $new_categories;
                        $product_import_model->upd_product($product);
                    }
                }
                a2w_set_setting('remember_categories', $new_categories);
            } else if (empty($_POST['categories'])) {
                a2w_del_setting('remember_categories');
            }
            echo json_encode(A2W_ResultBuilder::buildOk());
            wp_die();
        }
        
        public function ajax_get_all_products_to_import() {
            $product_import_model = new A2W_ProductImport();
            echo json_encode(A2W_ResultBuilder::buildOk(array('ids' => $product_import_model->get_product_id_list())));
            wp_die();
        }

        public function ajax_get_product() {
            $product_import_model = new A2W_ProductImport();
            if (!empty($_POST['id'])) {
                if ($product = $product_import_model->get_product($_POST['id'])) {
                    $result = A2W_ResultBuilder::buildOk(array('product'=>$product));
                }else{
                    $result = A2W_ResultBuilder::buildError("product not found");
                }
            } else {
                $result = A2W_ResultBuilder::buildError("get_product: waiting for ID...");
            }
            echo json_encode($result);
            wp_die();
        }

        public function ajax_split_product(){
            $product_import_model = new A2W_ProductImport();
            if (!empty($_POST['id']) && !empty($_POST['attr'])) {
                if ($product = $product_import_model->get_product($_POST['id'])) {
                    $new_products = array();
                    $attr_index = 0;
                    $split_attr = array();
                    foreach($product['sku_products']['attributes'] as $k=>$a){
                        if($a['id'] == $_POST['attr']){
                            $split_attr = $a;
                            $attr_index = $k;
                        }
                    }
                    
                    foreach($product['sku_products']['attributes'][$attr_index]['value'] as $aid=>$av){
                        // if this original attr (not generated by update)
                        if(!isset($av['original_id'])){
                            $new_product = $product;
                            $new_product['disable_add_new_variants'] = true;
                            $new_product['skip_vars'] = array();
                            $new_product['skip_attr'] = array($split_attr['name']);
                            foreach($new_product['sku_products']['variations'] as $v){

                                $skip = true;
                                foreach($v['attributes'] as $vva){
                                    $var_atr_val = isset($product['sku_products']['attributes'][$attr_index]['value'][$vva])
                                        ?$product['sku_products']['attributes'][$attr_index]['value'][$vva]:false;
                                    if($var_atr_val && ($var_atr_val['id'] === $av['id'] || (isset($var_atr_val['original_id']) && $var_atr_val['original_id']=== $av['id']))){
                                        $skip = false;
                                    }
                                }

                                if($skip){
                                    $new_product['skip_vars'][] = $v['id'];
                                }else if(!empty($v['image'])){
                                    $new_product['thumb'] = $v['image'];
                                    $new_product['thumb_id'] = md5($v['image']);
                                }
                            }
                                
                            $new_products[$av['id']] = $new_product;
                        }
                        
                    }

                    $i = 0;
                    foreach($new_products as $k=>&$new_product){
                        if($i === 0){
                            $product_import_model->upd_product($new_product);
                        }else{
                            $new_product['import_id'] = $new_product['id']."-".md5($k.microtime(true));
                            $product_import_model->add_product($new_product);
                        }
                        $i++;
                    }


                    $result = A2W_ResultBuilder::buildOk();
                }else{
                    $result = A2W_ResultBuilder::buildError("product not found");
                }
                
            } else if (!empty($_POST['id']) && !empty($_POST['vars'])) {
                if ($product = $product_import_model->get_product($_POST['id'])) {

                    if(count($_POST['vars']) == count($product['sku_products']['variations'])){
                        $result = A2W_ResultBuilder::buildOk();
                    }else{
                        $selected_vars = $_POST['vars'];
                        $rest_vars =  $foo = array_values(array_filter(array_map(function($v){return $v['id'];}, $product['sku_products']['variations']), 
                            function($v) use ($selected_vars) {
                                return !in_array($v, $selected_vars);
                            }
                        ));

                        $product_thumb = false;
                        $new_product_thumb = false;
                        foreach($product['sku_products']['variations'] as $v){
                            if(!$product_thumb && !empty($v['image']) && in_array($v['id'], $selected_vars)){
                                $product_thumb = $v['image'];
                            }

                            if(!$new_product_thumb && !empty($v['image']) && in_array($v['id'], $rest_vars)){
                                $new_product_thumb = $v['image'];
                            }
                        }

                        $new_product = $product;


                        $product['disable_add_new_variants'] = true;
                        $product['skip_vars'] = $rest_vars;
                        if($product_thumb){
                            $product['thumb'] = $product_thumb;
                            $product['thumb_id'] = md5($product_thumb);
                        }
                    
                        $new_product = $product;
                        $new_product['import_id'] = $new_product['id']."-".md5('new_product'.microtime(true));
                        $new_product['disable_add_new_variants'] = true;
                        $new_product['skip_vars'] = $selected_vars;
                        if($new_product_thumb){
                            $new_product['thumb'] = $new_product_thumb;
                            $new_product['thumb_id'] = md5($new_product_thumb);
                        }

                        $count_attributes = function ($p){
                            $used_attribute_values = array();
                            $var_count = 0;
                            foreach($p['sku_products']['variations'] as $var){
                                if(in_array($var['id'], $p['skip_vars'])){
                                    continue;
                                }
                                $var_count ++;
                                foreach($var['attributes'] as $var_attr_id){
                                    foreach($p['sku_products']['attributes'] as $attr){
                                        if(isset($attr['value'][$var_attr_id])){
                                            if(!isset($used_attribute_values[$attr['id']])){
                                                $used_attribute_values[$attr['id']] = array('name'=>$attr['name'], 'values'=>array());
                                            }
                                            $used_attribute_values[$attr['id']]['values'][$var_attr_id] = $var_attr_id;
                                        }
                                    }
                                }
                            }

                            if($var_count > 1){
                                return array_unique(array_values(
                                    array_map(function ($a){return $a['name'];}, 
                                        array_filter($used_attribute_values, function ($a){ return count($a['values'])<2; })
                                )));
                            } else {
                                return array();
                            }
                        };

                        $new_product['skip_attr'] = $count_attributes($new_product);
                        
                        $product['skip_attr'] = $count_attributes($product);

                        $product_import_model->upd_product($product);
                        $product_import_model->add_product($new_product);

                        $result = A2W_ResultBuilder::buildOk();
                    }

                    
                }else{
                    $result = A2W_ResultBuilder::buildError("product not found");
                }
            } else {
                $result = A2W_ResultBuilder::buildError("split_product: wrong parameters...");
            }

            echo json_encode($result);
            wp_die();
        }
        
        public function ajax_import_images_action() {
            a2w_init_error_handler();
            try {
                $product_import_model = new A2W_ProductImport();
                if (isset($_POST['id']) && $_POST['id'] && ($product = $product_import_model->get_product($_POST['id'])) && !empty($_POST['source']) && !empty($_POST['type']) && in_array($_POST['source'], array("description", "variant")) && in_array($_POST['type'], array("copy", "move"))) {
                    if(!empty($_POST['images'])){
                        foreach($_POST['images'] as $image){
                            if($_POST['type'] == 'copy'){
                                $product['tmp_copy_images'][$image] = $_POST['source'];
                            } else if($_POST['type'] == 'move') {
                                $product['tmp_move_images'][$image] = $_POST['source'];
                            }
                        }
                        
                        $product_import_model->upd_product($product);    
                    }
                    
                    $result = A2W_ResultBuilder::buildOk();
                } else {
                    $result = A2W_ResultBuilder::buildError("Error in params");
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
        
        public function ajax_import_cancel_images_action() {
            a2w_init_error_handler();
            try {
                $product_import_model = new A2W_ProductImport();
                if (isset($_POST['id']) && $_POST['id'] && ($product = $product_import_model->get_product($_POST['id'])) && !empty($_POST['image']) && !empty($_POST['source']) && !empty($_POST['type']) && in_array($_POST['source'], array("description", "variant")) && in_array($_POST['type'], array("copy", "move"))) {
                    if($_POST['type'] == 'copy'){
                        unset($product['tmp_copy_images'][$_POST['image']]);
                    } else if($_POST['type'] == 'move') {
                        unset($product['tmp_move_images'][$_POST['image']]);
                    }
                    
                    $product_import_model->upd_product($product);    
                    
                    $result = A2W_ResultBuilder::buildOk();
                } else {
                    $result = A2W_ResultBuilder::buildError("Error in params");
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

        public function ajax_search_tags() {
            a2w_init_error_handler();
            try {
                $woocommerce_model = new A2W_Woocommerce();

                $num_in_page = 50;
                $page = !empty($_REQUEST['page'])?intval($_REQUEST['page']):1;
                $search = !empty($_REQUEST['search'])?$_REQUEST['search']:'';
                $result = $woocommerce_model->get_product_tags($search);
                $total_count = count($result);
                $result = array_slice($result, $num_in_page*($page-1), $num_in_page);
                
                $result = array(
                    'results'=>array_map(function($o) {return array('id'=>$o, 'text'=>$o);}, $result),
                    'pagination'=>array('more'=>$num_in_page*($page-1)+$num_in_page<$total_count)
                );
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

        public function ajax_search_products() {
            a2w_init_error_handler();
            try {
                $num_in_page = 20;
                $page = !empty($_REQUEST['page'])?intval($_REQUEST['page']):1;
                $search = !empty($_REQUEST['search'])?$_REQUEST['search']:'';

                global $wpdb;

                $products = $wpdb->get_results($wpdb->prepare("SELECT p.ID, p.post_title, pimg.guid as thumb FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON (p.ID=pm.post_id AND pm.meta_key='_thumbnail_id') LEFT JOIN $wpdb->posts pimg ON (pimg.ID=pm.meta_value) WHERE p.post_type='product' AND p.post_title like '%%%s%%' LIMIT %d, %d", $search, ($page-1) * $num_in_page, $num_in_page),ARRAY_A);
                $products = $products && is_array($products)?$products:array();
                $total_count = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM $wpdb->posts WHERE post_type='product' AND post_title like '%%%s%%'", $search));
                $result = array(
                    'results'=>array_map(function($o) {return array('id'=>$o['ID'], 'text'=>$o['post_title'], 'thumb'=>$o['thumb']);}, $products),
                    'pagination'=>array('more'=>$num_in_page*($page-1)+$num_in_page<$total_count)
                );

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

        public function ajax_override_order_variations(){
            $product_id = $_REQUEST['product_id'];

            $result = array("state"=>"ok");

            if(!$product_id){
                $result = array("state"=>"error", "message"=>"Wrong params.");
            }

            if($result['state']!='error') {
                $override_model = new A2W_Override();
                $result['order_variations'] = $override_model->find_orders($product_id);
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_override_product() {
            $result = array('state'=>'ok');

            $product_id = $_REQUEST['product_id'];
            $external_id = $_REQUEST['external_id'];
            $override_images = !empty($_REQUEST['override_images'])?filter_var($_REQUEST['override_images'], FILTER_VALIDATE_BOOLEAN):false;
            $override_title_description =  !empty($_REQUEST['override_title_description'])?filter_var($_REQUEST['override_title_description'], FILTER_VALIDATE_BOOLEAN):false;
            $order_variations = !empty($_REQUEST['order_variations']) && is_array($_REQUEST['order_variations'])?$_REQUEST['order_variations']:array();

            if(!$product_id || !$external_id){
                $result = array("state"=>"error", "message"=>"Wrong params.");
            }

            if($result['state']!='error') {
                $override_model = new A2W_Override();
                $result = $override_model->override($product_id, $external_id, $override_images, $override_title_description, $order_variations);
            }  

            if($result['state']!='error') {
                $result['button'] = __('Override', 'ali2woo');
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_cancel_override_product() {
            $external_id = $_REQUEST['external_id'];

            if($external_id){
                $override_model = new A2W_Override();
                $result = $override_model->cancel_override($external_id);
            } else {
                $result = array("state"=>"error", "message"=>"Wrong params.");
            }

            if($result['state']!='error') {
                $result['button'] = __('Push to Shop', 'ali2woo');
                $result['override_action'] = '<li><a href="#" class="product-card-override-product">'.__('Select Product to Override', 'ali2woo').'</a></li>';               
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_add_to_import() {
            if (isset($_POST['id'])) {
                $product = array();
                $products = a2w_get_transient('a2w_search_result');

                $product_import_model = new A2W_ProductImport();
                $loader = new A2W_Aliexpress();

                if ($products && is_array($products)) {
                    foreach ($products as $p) {
                        if ($p['id'] == $_POST['id']) {
                            $product = $p;
                            break;
                        }
                    }
                }

                global $wpdb;
                $post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_a2w_external_id' AND meta_value='%s' LIMIT 1", $_POST['id']));
                if (a2w_check_defined('A2W_ALLOW_PRODUCT_DUPLICATION') || !$post_id) {
                    $params = empty($_POST['apd'])?array():array('data'=>array('apd'=>json_decode(stripslashes($_POST['apd']))));
                    $res = $loader->load_product($_POST['id'], $params);
                    if ($res['state'] !== 'error') {
                        $product = array_replace_recursive($product, $res['product']);

                        if ($product) {
                            $product = A2W_PriceFormula::apply_formula($product);

                            $product_import_model->add_product($product);

                            echo json_encode(A2W_ResultBuilder::buildOk());
                        } else {
                            echo json_encode(A2W_ResultBuilder::buildError("Product not found in serach result"));
                        }
                    } else {
                        echo json_encode($res);
                    }
                } else {
                    echo json_encode(A2W_ResultBuilder::buildError("Product already imported."));
                }
            } else {
                echo json_encode(A2W_ResultBuilder::buildError("add_to_import: waiting for ID..."));
            }
            wp_die();
        }

        public function ajax_remove_from_import() {
            if (isset($_POST['id'])) {
                $product = false;
                $products = a2w_get_transient('a2w_search_result');

                $product_import_model = new A2W_ProductImport();

                foreach ($products as $p) {
                    if ($p['id'] == $_POST['id']) {
                        $product = $p;
                        break;
                    }
                }
                if ($product) {
                    $product_import_model->del_product($product['id']);
                    echo json_encode(A2W_ResultBuilder::buildOk());
                } else {
                    echo json_encode(A2W_ResultBuilder::buildError("Product not found in serach result"));
                }
            } else {
                echo json_encode(A2W_ResultBuilder::buildError("remove_from_import: waiting for ID..."));
            }
            wp_die();
        }

        public function ajax_load_shipping_info() {
            if (isset($_POST['id'])) {
                $ids = is_array($_POST['id']) ? $_POST['id'] : array($_POST['id']);
                $page = isset($_POST['page']) ? $_POST['page'] : 'search';

                $woocommerce_model = new A2W_Woocommerce();
                $product_import_model = new A2W_ProductImport();
                $loader = new A2W_Aliexpress();

                $default_ff_method = a2w_get_setting('fulfillment_prefship');

                $product = false;
                $products = $page === 'search' ? a2w_get_transient('a2w_search_result') : array();
                $result = array();
                foreach ($ids as $id) {
                    if($page == 'product'){
                        $product_id = $woocommerce_model->get_product_id_by_external_id($id);
                        if($tmp_product = $woocommerce_model->get_product_by_post_id($product_id)){
                            $products = array($tmp_product);
                        }
                    } else if($page == 'import' && $tmp_product = $product_import_model->get_product($id)){
                        $products = array($tmp_product);
                    }
                    foreach ($products as &$product) {
                        if ($product['id'] == $id || $product['import_id'] == $id) {
                            $product_country_to = isset($product['shipping_to_country']) && $product['shipping_to_country'] ? $product['shipping_to_country'] : '';
                            $product_country_from = isset($product['shipping_from_country']) && $product['shipping_from_country'] ? $product['shipping_from_country'] : '';
                            $country_to = isset($_POST['country_to']) ? $_POST['country_to'] : $product_country_to;
                            $country_from = isset($_POST['country_from']) ? $_POST['country_from'] : $product_country_from;

                            $country = $country_from.$country_to;

                            if ($country && empty($product['shipping_info'][$country])) {
                                $product['shipping_to_country'] = $country_to;
                                $product['shipping_from_country'] = $country_from;
                                $res = $loader->load_shipping_info($product['id'], 1, $country_to, $country_from, $page == 'import'?$product['price_min']:$product['price'], $page == 'import'?$product['price_max']:$product['price']);
                                if ($res['state'] !== 'error') {
                                    $product['shipping_info'][$country] = $res['items'];
                                } else {
                                    $product['shipping_info'][$country] = array();
                                }
                            }

                            $items = isset($product['shipping_info'][$country]) ? $product['shipping_info'][$country] : array();


                            if(empty($product['shipping_default_method'])){
                                $product['shipping_default_method'] = $default_ff_method;
                            }

                            $has_shipping_method = false;
                            foreach($items as $item){
                                if($item['serviceName'] == $product['shipping_default_method']){
                                    $has_shipping_method = true;
                                    break;
                                }
                            }

                            if(!$has_shipping_method){
                                $default_method = "";
                                $tmp_p = -1;
                                foreach($items as $k=>$item){
                                    $price = isset($item['previewFreightAmount']['value']) ? $item['previewFreightAmount']['value'] : $item['freightAmount']['value'];
                                    if ($tmp_p < 0 || $price < $tmp_p || $item['serviceName'] == $default_ff_method) {
                                        $tmp_p = $price;
                                        $default_method = $item['serviceName'];
                                        if($default_method == $default_ff_method){
                                            break;
                                        }
                                    }
                                }
                                $product['shipping_default_method'] = $default_method;
                            }

                            $product['shipping_cost'] = 0;
                            foreach($items as $item){
                                if($item['serviceName'] == $product['shipping_default_method']){
                                    $product['shipping_cost'] = isset($item['previewFreightAmount']['value']) ? $item['previewFreightAmount']['value'] : $item['freightAmount']['value'];
                                }
                            }

                            $product = A2W_PriceFormula::apply_formula($product);

                            $variations = array();
                            if(isset($product['sku_products']['variations'])){
                                foreach($product['sku_products']['variations'] as $v){
                                    $variations[] = array('id'=>$v['id'], 'calc_price'=>$v['calc_price'], 'calc_regular_price'=>$v['calc_regular_price']);
                                }
                            }

                            $result[] = array('product_id' => $id, 'default_method'=>isset($product['shipping_default_method']) ? $product['shipping_default_method'] : '', 'items' => $items, 'shipping_cost' => $product['shipping_cost'], 'variations'=>$variations);

                            if($page !== 'search'){
                                $product_import_model->upd_product($product);
                            }
                            break;
                        }
                    }
                }
                
                if($page === 'search'){
                    a2w_set_transient('a2w_search_result', $products);
                }

                echo json_encode(A2W_ResultBuilder::buildOk(array('products' => $result)));
            } else {
                echo json_encode(A2W_ResultBuilder::buildError("load_shipping_info: waiting for ID..."));
            }
            wp_die();
        }

        public function ajax_set_shipping_info() {  
            if (isset($_POST['id'])) {
                $product_import_model = new A2W_ProductImport();
                $product = $product_import_model->get_product($_POST['id']);

                if($product) {
                    $product_country_to = isset($product['shipping_to_country']) && $product['shipping_to_country'] ? $product['shipping_to_country'] : '';
                    $product_country_from = isset($product['shipping_from_country']) && $product['shipping_from_country'] ? $product['shipping_from_country'] : '';
                    $country_to = isset($_POST['country_to']) ? $_POST['country_to'] : $product_country_to;
                    $country_from = isset($_POST['country_from']) ? $_POST['country_from'] : $product_country_from;

                    $country = $country_from.$country_to;
                    $method = isset($_POST['method']) ? $_POST['method'] : '';

                    if ($country && $method) {
                        $product['shipping_default_method'] = $method;
                        $product['shipping_to_country'] = $country_to;
                        $product['shipping_from_country'] = $country_from;

                        $product['shipping_cost'] = 0;
                        $items = isset($product['shipping_info'][$country]) ? $product['shipping_info'][$country] : array();
                        foreach($items as $s){
                            if($s['serviceName'] == $product['shipping_default_method']) {
                                $product['shipping_cost'] = isset($s['previewFreightAmount']['value']) ? $s['previewFreightAmount']['value'] : $s['freightAmount']['value'];
                                break;
                            }
                        }

                        $product = A2W_PriceFormula::apply_formula($product);

                        $product_import_model->upd_product($product);
                    }

                    $variations = array();
                    foreach($product['sku_products']['variations'] as $v){
                        $variations[] = array('id'=>$v['id'], 'calc_price'=>$v['calc_price'], 'calc_regular_price'=>$v['calc_regular_price']);
                    }
                    
                    $shipping_cost = 0;
                    if(isset($product['shipping_cost'])){
                        $shipping_cost = $product['shipping_cost'];
                    }

                    echo json_encode(A2W_ResultBuilder::buildOk(array('default_method'=>$product['shipping_default_method'], 'shipping_cost'=>$shipping_cost, 'variations'=>$variations)));
                }else{
                    echo json_encode(A2W_ResultBuilder::buildError("Product not found."));    
                }
            } else {
                echo json_encode(A2W_ResultBuilder::buildError("load_shipping_info: waiting for ID..."));
            }
            wp_die();
        }

        public function ajax_update_shipping_list() {  
            if (isset($_POST['items'])) {
                foreach ($_POST['items'] as $ship_way){
                    if(empty($ship_way['company']) || empty($ship_way['serviceName'])) {
                        continue;
                    }

                    $item = A2W_ShippingPostType::get_item($ship_way['company']);
                    
                    // skip disabled items
                    if ( $item === false) continue;
                    
                    // if no such item yet, let`s add it and then get it
                    if (!$item) {
                        A2W_ShippingPostType::add_item($ship_way['company'], $ship_way['serviceName']);
                    }
                }
            }
            echo json_encode(A2W_ResultBuilder::buildOk());
            wp_die();
        }
    }

}
