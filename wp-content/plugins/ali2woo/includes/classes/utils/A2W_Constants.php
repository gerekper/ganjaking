<?php

/**
 * Description of A2W_Constants
 *
 * @author Mikhail
 */
if (!class_exists('A2W_Constants')) {

    class A2W_Constants {

        public static function order_item_external_order_meta(){
            return "_a2w_external_order_item_id";
        }

        public static function order_item_tracking_data_meta(){
            return "_a2w_tracking_data";
        }

        public static function product_external_meta(){
            return "_a2w_external_id";
        }

        public static function product_reviews_max_number_meta(){
            return "_a2w_reviews_max_number";
        }





        //old meta keys, they will be deleted in a future
        public static function old_order_external_order_id(){
            return "_a2w_external_order_id";      
        }

        public static function old_order_tracking_code(){
            return "_a2w_tracking_code";      
        }

    }

}
