<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Generate_Voucher_Codes' ) ) {

    /**
     * RS_Generate_Voucher_Codes Class.
     */
    class RS_Generate_Voucher_Codes extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_voucher_code_generator' ;

        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param mixed $item Queue item to iterate over
         *
         * @return mixed
         */
        protected function task( $item ) {
            $this->generate_code( $item ) ;
            return false ;
        }

        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete() {
            parent::complete() ;
            $offset       = get_option( 'rs_updater_for_voucher_code' ) ;
            $VoucherCodes = get_option( 'rs_voucher_codes' ) ;
            $SlicedArray  = array_slice( array_unique( $VoucherCodes ) , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_generate_voucher_codes( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Voucher Code(s) generated Successfully' ) ;
                delete_option( 'rs_updater_for_voucher_code' ) ;
            }
        }

        public function generate_code( $VoucherId ) {
            if ( $VoucherId != 'no_vouchers' ) {
                global $wpdb ;
                $table_name  = $wpdb->prefix . "rsgiftvoucher" ;
                $VoucherData = get_option( 'rs_voucher_data' ) ;
                $wpdb->insert(
                        $table_name , array(
                    'points'                       => $VoucherData[ 'voucherpoint' ] ,
                    'vouchercode'                  => $VoucherId ,
                    'vouchercreated'               => $VoucherData[ 'vouchercreated' ] ,
                    'voucherexpiry'                => $VoucherData[ 'expiry_date' ] ,
                    'memberused'                   => '' ,
                    'voucher_code_usage'           => $VoucherData[ 'usertype' ] ,
                    'voucher_code_usage_limit'     => $VoucherData[ 'usagelimit' ] ,
                    'voucher_code_usage_limit_val' => $VoucherData[ 'usagelimitvalue' ] )
                ) ;
            }
        }

    }

}