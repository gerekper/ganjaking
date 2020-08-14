<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WCPOS_Cron' ) ){

    class YITH_WCPOS_Cron{

        public static $instance;

        public static function  get_instance(){
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action( 'wp_loaded', array( $this, 'set_cron' ) );
            add_filter( 'cron_schedules',  array( $this, 'cron_schedule')  );
        }

        /**
         * Destroy the schedule
         *
         * Called when ywrac_cron_time and ywrac_cron_time_type are update from settings panel
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function destroy_schedule() {
            wp_clear_scheduled_hook( 'ywcpos_cron' );
            $this->set_cron();
        }

        /**
         * Cron Schedule
         *
         * Add new schedules to wordpress
         *
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function cron_schedule( $schedules ){

            $schedules['ywcpos_gap'] = array(
                'interval' => 3600,
                'display' => __( 'YITH WooCommerce Pending Order Survey Cron', 'yith-woocommerce-pending-order-survey' )
            );

            return $schedules;
        }

        /**
         * Set Cron
         *
         * Set ywrac_cron action each ywrac_gap schedule
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function set_cron() {
            if ( !wp_next_scheduled( 'ywcpos_cron' ) ) {
                wp_schedule_event( current_time('timestamp'), 'ywcpos_gap', 'ywcpos_cron' );
            }
        }
    }
}

function YITH_WC_POS_Cron(){

    return YITH_WCPOS_Cron::get_instance();
}