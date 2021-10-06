<?php

/**
 * Description of A2W_ImportShopmaster
 *
 * @author Andrey
 */

if (!class_exists('A2W_ImportShopmaster')) {
    class A2W_ImportShopmaster {
        private $filepath;

        private $delimiter;

        private $fields = array(
            'productId'=>-1,
            'sourceId'=>-1,
            'sourceUrl'=>-1,
            'productVarSku'=>-1,
            'productVarId'=>-1,
            'productVarAttr'=>-1,
            'sourceVarId'=>-1,
            'sourceVarAttr'=>-1,
            'warehouse'=>-1
        );

        public function __construct($path, $delimiter = ";") { 
            $this->filepath = $path;
            $this->delimiter = $delimiter;
        }

        public function validate() {
            $res = $this->validate_by_delimiter(',');
            if(!$res){
                $res = $this->validate_by_delimiter(';');
            }
            return $res;
        }

        public function validate_by_delimiter($delimiter){
            $handle = fopen($this->filepath, "r");
            if ($handle) {
                $fields = $this->fields;
                $columns = fgetcsv($handle, 0, $delimiter);
                foreach($columns as $i=>$c){
                    if(isset($fields[$c])){
                        $fields[$c] = $i;
                    }
                }
                foreach($fields as $f){
                    if ($f == -1){
                        return false;
                    }
                }
                $this->delimiter = $delimiter;
                $this->fields = $fields;
                return true;
            } else {
                return false;
            }
        }

        public function fetch_product_ids(){
            $ids = array();
            $handle = fopen($this->filepath, "r");
            if ($handle) {
                $after_header = false;
                $cur_id = "";
                while (($columns = fgetcsv($handle, 0, $this->delimiter)) !== false) {
                    $index = $this->fields['productId'];
                    if(!empty(trim($columns[$index])) && trim($columns[$index]) !='productId' && trim($columns[$index]) != $cur_id){
                        $ids[] = $cur_id = trim($columns[$index]);
                    }
                }                
                fclose($handle);
            } 
            return $ids;
        }

        public function convert($id){
            global $wpdb;
            $handle = fopen($this->filepath, "r");
            if ($handle) {
                $found = false;

                $product = array();

                while (($columns = fgetcsv($handle, 0, $this->delimiter)) !== false) {
                    if(!$found && trim($columns[$this->fields['productId']]) == $id){
                        $found = true;
                    }

                    if ($found && trim($columns[$this->fields['productId']]) != "" && trim($columns[$this->fields['productId']]) != $id){
                        break;
                    } else if($found) {
                        // fill product data
                        $product['id'] = $id;
                        if(!isset($product['variations'])){
                            $product['variations'] = array();
                        }

                        if(!empty($columns[$this->fields['sourceId']])){
                            $product['sourceId'] = $columns[$this->fields['sourceId']];    
                        }
                        if(!empty($columns[$this->fields['sourceUrl']])){
                            $product['sourceUrl'] = $columns[$this->fields['sourceUrl']]; 
                        }

                        if(!empty($columns[$this->fields['warehouse']])){
                            $product['warehouse'] = $columns[$this->fields['warehouse']];    
                        }

                        $product['sourceLang'] = 'en';
                        if(!empty($product['sourceUrl'])){
                            preg_match('/\/\/(.{2,3})\..*/', $product['sourceUrl'], $output_array);
                            if(isset($output_array[1])){
                                $product['sourceLang'] = $output_array[1] == 'www'?'en':strtolower($output_array[1]);
                            }
                        }

                        if(!empty($columns[$this->fields['sourceVarId']]) && !empty($columns[$this->fields['sourceVarAttr']])){
                            $attrs = explode(";",$columns[$this->fields['sourceVarId']]);
                            foreach($attrs as $k=>$a){
                                $na = explode("#",$a);
                                $attrs[$k] = $na[0];
                            }
                            $var = array('sourceVarId'=>$columns[$this->fields['sourceVarId']], 
                                         'nSourceVarId'=>implode(";",$attrs), 
                                         'sourceVarAttr'=>$columns[$this->fields['sourceVarAttr']]);

                            
                            if(!empty($columns[$this->fields['productVarSku']])){
                                $var['productVarSku'] = $columns[$this->fields['productVarSku']];    
                            }
                            if(!empty($columns[$this->fields['productVarId']])){
                                $var['productVarId'] = $columns[$this->fields['productVarId']];    
                            }
                            if(!empty($columns[$this->fields['productVarAttr']])){
                                $var['productVarAttr'] = json_decode($columns[$this->fields['productVarAttr']],true);    
                            }
                            $product['variations'][] = $var;    
                        }
                    }
                }
                fclose($handle);


                if (empty($product) || !get_post($product['id'])){
                    return A2W_ResultBuilder::buildError("product(".$id.") not found");
                } else{
                    $external_id = get_post_meta($product['id'],'_a2w_external_id', true);
                    if(!$external_id){
                        update_post_meta($product['id'], '_a2w_external_id', $product['sourceId']);
                        update_post_meta($product['id'], '_a2w_import_id', $product['sourceId']);
                        update_post_meta($product['id'], '_a2w_product_url', $product['sourceUrl']);
                        update_post_meta($product['id'], '_a2w_original_product_url', $product['sourceUrl']);
                        update_post_meta($product['id'], '_a2w_disable_sync', 0);
                        update_post_meta($product['id'], '_a2w_disable_var_price_change', 0);
                        update_post_meta($product['id'], '_a2w_disable_var_quantity_change', 0);
                        update_post_meta($product['id'], '_a2w_disable_add_new_variants', 0);
                        update_post_meta($product['id'], '_a2w_import_lang', isset($product['sourceLang'])?$product['sourceLang']:'en');
                        if(isset($product['warehouse'])){
                            update_post_meta($product['id'], '_a2w_country_code', $product['warehouse']);
                        }
                        $var_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->posts} p WHERE post_parent = %s AND p.post_type='product_variation'", $product['id']));
                        foreach($var_ids as $vid) {
                            update_post_meta($vid, 'external_variation_id', 'delete');
                            if(isset($product['warehouse'])){
                                update_post_meta($vid, '_a2w_country_code', $product['warehouse']);
                            }
                        }

                        update_post_meta($product['id'], '_a2w_tmp_convert_data', $product);
                        update_post_meta($product['id'], '_a2w_need_convert', 1);
                    }
                }

                return A2W_ResultBuilder::buildOk();

            } else {
                return A2W_ResultBuilder::buildError("can't read file: ".$this->filepath);
            } 
        }

        public function convert_product($product_id, $product, $params = array()) {
            global $wpdb;

            $old_product_data = get_post_meta($product_id, '_a2w_need_convert', true);
            if(!$old_product_data) return;

            $convert_data = get_post_meta($product_id, '_a2w_tmp_convert_data', true);

            // 'external_variation_id', $product['sourceId'].'-'.implode('-',explode(';',$v['nSourceVarId']))

           
            $skip_meta = array('skip_vars'=>array(),'skip_images'=>array());
            $skip_vars = array();
            $existing_vars = array();
            foreach($convert_data['variations'] as $v){       
                $existing_vars[] = $product['id'].'-'.implode('-',explode(';',$v['nSourceVarId']));
            }

            $externalVariationId = $product['id'].'-'.implode('-',explode(';',$v['nSourceVarId']));
            foreach($product['sku_products']['variations'] as $var) {
                if(!in_array($var['id'], $existing_vars)){
                    $skip_vars[] = $var['id'];
                }
            }

            if(count($skip_vars) < count($product['sku_products']['variations'])){
                $skip_meta['skip_vars'] = $skip_vars;
            }

            $used_attr = array();
            $var_ids = array();
            foreach($convert_data['variations'] as $v){
                $var_ids[] = explode(";", $v['nSourceVarId']);
                foreach($v['productVarAttr'] as $a){
                    if(!in_array($a['name'], $used_attr)){
                        $used_attr[] = $a['name'];
                    }
                }    
            }
            $total_attr_cnt = count($product['sku_products']['attributes']);

            $skip_attr = array();
            foreach($product['sku_products']['attributes'] as $a){
                if(!in_array($a['name'], $used_attr)){
                    $values = array_keys($a['value']);

                    $val_cnt = 0;
                    foreach($values as $val){
                        $used = false;
                        foreach($var_ids as $vid){
                            if(in_array($val, $vid)){
                                $used=true;
                                break;      
                            }
                        }
                        if($used) $val_cnt++;  
                        
                        
                    }

                    if($val_cnt<2) {
                        $skip_attr[sanitize_title($a['name'])] = array('name'=>$a['name'], 'current_name'=>$a['name'], 'original_attribute_id'=>$a['id'], 'values'=>array());
                    }
                }
            }

            if($total_attr_cnt - count($used_attr) == count($skip_attr)){
                update_post_meta($product_id, '_a2w_deleted_variations_attributes', $skip_attr);
            }

            update_post_meta($product_id, '_a2w_skip_meta', $skip_meta);

            return A2W_ResultBuilder::buildOk();
        }

        // public function convert($id){
        //     global $wpdb;
        //     $handle = fopen($this->filepath, "r");
        //     if ($handle) {
        //         $found = false;

        //         $product = array();

        //         while (($columns = fgetcsv($handle, 0, $this->delimiter)) !== false) {
        //             if(!$found && trim($columns[2]) == $id){
        //                 $found = true;
        //             }

        //             if ($found && trim($columns[2]) != "" && trim($columns[2]) != $id){
        //                 break;
        //             } else if($found) {
        //                 // fill product data
        //                 $product['id'] = $id;
        //                 if(!isset($product['variations'])){
        //                     $product['variations'] = array();
        //                 }
        //                 if(!empty($columns[0])){
        //                     $product['name'] = $columns[0];    
        //                 }
        //                 if(!empty($columns[1])){
        //                     $product['sku'] = $columns[1];    
        //                 }
        //                 if(!empty($columns[3])){
        //                     $product['sourceId'] = $columns[3];    
        //                 }
        //                 $product['sourceLang'] = 'en';
        //                 if(!empty($columns[4])){
        //                     $product['sourceUrl'] = $columns[4];
        //                     preg_match('/\/\/(.{2,3})\..*/', $columns[4], $output_array);
        //                     if(isset($output_array[1])){
        //                         $product['sourceLang'] = $output_array[1] == 'www'?'en':strtolower($output_array[1]);
        //                     }
        //                 }
        //                 if(!empty($columns[8]) && !empty($columns[9])){
        //                     $attrs = explode(";",$columns[8]);
        //                     foreach($attrs as $k=>$a){
        //                         $na = explode("#",$a);
        //                         $attrs[$k] = $na[0];
        //                     }
        //                     $var = array('sourceVarId'=>$columns[8], 'nSourceVarId'=>implode(";",$attrs), 'sourceVarAttr'=>$columns[9]);
        //                     if(!empty($columns[5])){
        //                         $var['productVarSku'] = $columns[5];    
        //                     }
        //                     if(!empty($columns[6])){
        //                         $var['productVarId'] = $columns[6];    
        //                     }
        //                     if(!empty($columns[7])){
        //                         $var['productVarAttr'] =  json_decode($columns[7],true);
        //                     }
        //                     $product['variations'][] = $var;                                
        //                 }
        //             }
        //         }
        //         fclose($handle);


        //         if (empty($product) || !get_post($product['id'])){
        //             return A2W_ResultBuilder::buildError("product(".$id.") not found");
        //         } else{
        //             $external_id = get_post_meta($product['id'],'_a2w_external_id', true);
        //             if(!$external_id){
        //                 update_post_meta($product['id'], '_a2w_external_id', $product['sourceId']);
        //                 update_post_meta($product['id'], '_a2w_import_id', $product['sourceId']);
        //                 update_post_meta($product['id'], '_a2w_product_url', $product['sourceUrl']);
        //                 update_post_meta($product['id'], '_a2w_original_product_url', $product['sourceUrl']);
        //                 update_post_meta($product['id'], '_a2w_disable_sync', 0);
        //                 update_post_meta($product['id'], '_a2w_disable_var_price_change', 0);
        //                 update_post_meta($product['id'], '_a2w_disable_var_quantity_change', 0);
        //                 update_post_meta($product['id'], '_a2w_disable_add_new_variants', 0);
        //                 update_post_meta($product['id'], '_a2w_import_lang', isset($product['sourceLang'])?$product['sourceLang']:'en');

        //                 foreach($product['variations'] as $v) {
        //                     $external_var_id = $product['sourceId'].'-'.implode('-',explode(';',$v['nSourceVarId']));
        //                     $var_id = 0;
        //                     if(isset($v['productVarId'])){
        //                         $var_id = $v['productVarId'];
        //                     } else if(isset($v['productVarSku'])){
        //                         $var_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON(p.ID=pm.post_id and pm.meta_key='_sku')  WHERE post_parent = %s AND pm.meta_value=%s", $product['id'], $v['productVarSku']));
        //                     }
        //                     if($var_id){
        //                         update_post_meta($var_id, 'external_variation_id', $product['sourceId'].'-'.implode('-',explode(';',$v['nSourceVarId'])));
        //                         update_post_meta($var_id, '_aliexpress_sku_props', $v['nSourceVarId']);
        //                     }
        //                 }

        //                 update_post_meta($product['id'], '_a2w_tmp_convert_data', $product);
        //                 update_post_meta($product['id'], '_a2w_need_convert', 1);
        //             }
        //         }

        //         return A2W_ResultBuilder::buildOk();

        //     } else {
        //         return A2W_ResultBuilder::buildError("can't read file: ".$this->filepath);
        //     } 
        // }

        // public function convert_product($product_id, $product, $params = array()) {
        //     global $wpdb;

        //     $old_product_data = get_post_meta($product_id, '_a2w_need_convert', true);
        //     if(!$old_product_data) return;

        //     $skip_var = array();
        //     $skip_meta = array('skip_vars'=>array(),'skip_images'=>array());

        //     $vars_tmp = $wpdb->get_results($wpdb->prepare("SELECT ID as id, pm.meta_value as external_variation_id FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON(p.ID=pm.post_id and pm.meta_key='external_variation_id') WHERE post_parent = %s AND p.post_type='product_variation'", $product_id), ARRAY_A );
        //     $vars = array();
        //     foreach($vars_tmp as $v){
        //         $vars[$v['id']] = $v['external_variation_id'];
        //         update_post_meta($v['id'], 'external_variation_id', $v['external_variation_id']);
        //     }

        //     foreach($product['sku_products']['variations'] as $var) {
        //         if(!in_array($var['id'], array_values($vars))){
        //             $skip_meta['skip_vars'][] = $var['id']; 
        //         }else{
        //             foreach($vars as $vid=>$evid){
        //                 if($evid == $var['id']){
        //                     // build _aliexpress_sku_props
        //                     $aliexpress_sku_props_id_arr = array();
        //                     foreach ($var['attributes'] as $cur_var_attr) {
        //                         foreach ($product['sku_products']['attributes'] as $attr) {
        //                             if (isset($attr['value'][$cur_var_attr])) {
        //                                 $aliexpress_sku_props_id_arr[] = isset($attr['value'][$cur_var_attr]['original_id']) ? $attr['value'][$cur_var_attr]['original_id'] : $attr['value'][$cur_var_attr]['id'];
        //                                 break;
        //                             }
        //                         }
        //                     }
        //                     $aliexpress_sku_props_id = $aliexpress_sku_props_id_arr ? implode(";", $aliexpress_sku_props_id_arr) : "";
        //                     if ($aliexpress_sku_props_id) {
        //                         update_post_meta($product_id, '_aliexpress_sku_props', $aliexpress_sku_props_id);
        //                     }
        //                     break;
        //                 }
        //             }
        //         }
        //     }

        //     $used_attr = array();
        //     $var_ids = array();
        //     $convert_data = get_post_meta($product_id, '_a2w_tmp_convert_data', true);
        //     foreach($convert_data['variations'] as $v){
        //         $var_ids[] = explode(";", $v['nSourceVarId']);
        //         foreach($v['productVarAttr'] as $a){
        //             if(!in_array($a['name'], $used_attr)){
        //                 $used_attr[] = $a['name'];
        //             }
        //         }    
        //     }
        //     $total_attr_cnt = count($product['sku_products']['attributes']);

        //     $skip_attr = array();
        //     foreach($product['sku_products']['attributes'] as $a){
        //         if(!in_array($a['name'], $used_attr)){
        //             $values = array_keys($a['value']);

        //             $val_cnt = 0;
        //             foreach($values as $val){
        //                 $used = false;
        //                 foreach($var_ids as $vid){
        //                     if(in_array($val, $vid)){
        //                         $used=true;
        //                         break;      
        //                     }
        //                 }
        //                 if($used) $val_cnt++;  
                        
                        
        //             }

        //             if($val_cnt<2) {
        //                 $skip_attr[sanitize_title($a['name'])] = array('name'=>$a['name'], 'current_name'=>$a['name'], 'original_attribute_id'=>$a['id'], 'values'=>array());
        //             }
        //         }
        //     }

        //     if($total_attr_cnt - count($used_attr) == count($skip_attr)){
        //         update_post_meta($product_id, '_a2w_deleted_variations_attributes', $skip_attr);
        //     }


        //     update_post_meta($product_id, '_a2w_skip_meta', $skip_meta);

        //     return A2W_ResultBuilder::buildOk();
        // }
    }

}
