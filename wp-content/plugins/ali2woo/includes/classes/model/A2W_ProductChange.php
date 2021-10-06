<?php

/**
 * Description of A2W_ProductChange
 *
 * @author mikhail
 */
if (!class_exists('A2W_ProductChange')) {
    class A2W_ProductChange {

        private $transient_key = 'a2w_product_changes';

        /**
         * Save the product is not available. It overwites other changes for the product!
         */
        public function saveProductNotAvailable($product_id){
            if ($product_id){
                $changes = $this->get_all();  
               
                $changes[$product_id] = array('not_available_product' => true, 
                                              'is_price_changed' => false, 
                                              'is_stock_changed' => false, 
                                              'has_new_variants' => false);

                a2w_set_transient($this->transient_key, $changes);
            }
        }

        /**
         * Save some product changes.
         * $data - array that describes the changes 
         */
        public function save($product_id, $is_price_changed, $is_stock_changed, $has_new_variants){

            if ($product_id && ($is_price_changed || $is_stock_changed || $has_new_variants)){

                $changes = $this->get_all();

                if ( !isset($changes[$product_id]) ){
                    $changes[$product_id] = array('not_available_product' => false );
                }

                $changes[$product_id]['is_price_changed'] = $is_price_changed;
                $changes[$product_id]['is_stock_changed'] = $is_stock_changed;
                $changes[$product_id]['has_new_variants'] = $has_new_variants;
               
                a2w_set_transient($this->transient_key, $changes);
            }

        }

        public function get_all(){
            $changes = array();
            $changes = a2w_get_transient($this->transient_key);  
            
            return $changes;
        }

        public function clear_all(){
            a2w_delete_transient($this->transient_key);
        }
    }
}
    