<?php
/**
 * Description of A2W_WpmlController
 *
 * @author Andrey
 * 
 * @autoload: a2w_admin_init
 */

if (!class_exists('A2W_WpmlController')) {

    class A2W_WpmlController {

        public function __construct() {
            add_action('wcml_after_sync_product_data', array($this, 'sync_product_data'), 100, 3);
        }

        /* This function will be called after the product has been transferred.
         *
         * In this place need to build attributes values and update swatches IDs, and do other cool stuff
        */
        public function sync_product_data($or_product_id, $tr_product_id, $language){
            global $wpdb;

            // Build attributes values and update swatches IDs

            $or_product_attributes = get_post_meta($tr_product_id, '_product_attributes',true);
            $var_attrs = array();
            if($or_product_attributes){
                foreach($or_product_attributes as $key=>$val){
                    if($val['is_variation']){
                        $var_attrs[$key] = array(); 
                    }
                }
            }

            if(!empty($var_attrs)){
                // Collect attributes value from original product variations
                $arrts_str = implode(",", array_map(function($v) {return "'attribute_$v'";}, array_keys($var_attrs)));
                $results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT pm.meta_key, pm.meta_value FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id) WHERE post_parent=%d and meta_key in ($arrts_str)", $or_product_id), ARRAY_A);
                foreach($results as $res) {
                    $attr_name = substr($res['meta_key'], strlen('attribute_'));
                    $var_attrs[$attr_name][] = $res['meta_value'];
                }

                $tr_swatch = get_post_meta($tr_product_id, '_swatch_type_options',true);

                // Replace default lang swatches ID to translated swatches ID
                foreach($var_attrs as $attr_slug=>$values){
                    $attr_id = md5($attr_slug);
                    foreach($values as $val){
                        $val_id = md5($val);
                        $new_val_id = md5($val.'-'.$language);
                        if(isset($tr_swatch[$attr_id]['attributes'][$val_id])){
                            $tr_swatch[$attr_id]['attributes'][$new_val_id] = $tr_swatch[$attr_id]['attributes'][$val_id];
                            unset($tr_swatch[$attr_id]['attributes'][$val_id]);
                        }
                    }
                }

                update_post_meta($tr_product_id, '_swatch_type_options', $tr_swatch);
            }
            
        }

    }

}
