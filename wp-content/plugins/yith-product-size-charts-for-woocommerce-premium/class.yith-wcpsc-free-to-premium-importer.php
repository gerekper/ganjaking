<?php
!defined( 'YITH_WCPSC' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Free_To_Premium_Importer' ) ) {
    /**
     * Free to premium importer class
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPSC_Free_To_Premium_Importer {

        /** @var YITH_WCPSC_Free_To_Premium_Importer */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCPSC_Free_To_Premium_Importer
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self;
        }

        const PROCESS_PER_TIME = 50;

        /**
         * YITH_WCPSC_Free_To_Premium_Importer constructor.
         */
        private function __construct() {
            if ( isset( $_GET[ 'page' ] ) && 'yith_wcpsc_panel' === $_GET[ 'page' ] ) {
                add_filter( 'yith_wcpsc_panel_settings_options', array( $this, 'free_to_premium_options' ), 10, 1 );
            }

            add_action( 'wp_ajax_yith_wcpsc_free_to_premium_update', array( $this, 'free_to_premium_update' ) );
            add_action( 'wp_ajax_nopriv_yith_wcpsc_free_to_premium_update', array( $this, 'free_to_premium_update' ) );

            add_action( 'wp_ajax_yith_wcpsc_free_to_premium_skip', array( $this, 'free_to_premium_skip' ) );
            add_action( 'wp_ajax_nopriv_yith_wcpsc_free_to_premium_skip', array( $this, 'free_to_premium_skip' ) );
        }

        public function free_to_premium_update() {
            $chart_ids      = $this->get_free_charts_to_sync();
            $ids_to_process = array_slice( $chart_ids, 0, self::PROCESS_PER_TIME );

            foreach ( $ids_to_process as $id ) {
                $product_id = absint( get_post_meta( $id, 'product', true ) );
                if ( $product_id ) {
                    $charts_in_product = get_post_meta( $product_id, 'yith_wcpsc_product_charts', true );
                    $charts_in_product = !!$charts_in_product && is_array( $charts_in_product ) ? $charts_in_product : array();
                    $charts_in_product = array_unique( array_merge( array( $id ), $charts_in_product ) );

                    update_post_meta( $product_id, 'yith_wcpsc_product_charts', $charts_in_product );
                    delete_post_meta( $id, 'product' );
                }
            }

            if ( count( $chart_ids ) > self::PROCESS_PER_TIME ) {
                wp_send_json( array( 'action' => 'next', 'toUpdate' => count( $chart_ids ) - count( $ids_to_process ) ) );
            } else {
                wp_send_json(
                    array(
                        'action'  => 'complete',
                        'message' => '<span class="success">' . __( 'All size charts updated correctly!', 'yith-product-size-charts-for-woocommerce' ) . '</span>'
                    ) );
            }

        }

        public function free_to_premium_skip() {
            update_option( 'yith_wcpsc_free_to_premium_update_skipped', true );
        }

        public function free_to_premium_options( $settings ) {
            if ( $this->has_free_charts_to_sync() ) {
                $to_update               = count( $this->get_free_charts_to_sync() );
                $free_to_premium_options = array(
                    'free-to-premium'        => array(
                        'type' => 'title',
                        'desc' => '',
                        'id'   => 'yith-wcpsc-free-to-premium'
                    ),
                    'free-to-premium-update' => array(
                        'type'      => 'yith-field',
                        'yith-type' => 'html',
                        'html'      => "<div id='yith-wcpsc-free-to-premium-update__container'>" .
                                       "<h2>" . __( 'Free to Premium Update', 'yith-product-size-charts-for-woocommerce' ) . "</h2>" .
                                       "<span id='yith-wcpsc-free-to-premium-update' class='button button-primary' data-to-update='{$to_update}'>" . __( 'Update', 'yith-product-size-charts-for-woocommerce' ) . "</span>" .
                                       "<span id='yith-wcpsc-free-to-premium-skip' class='button button-secondary'>" . __( 'Skip', 'yith-product-size-charts-for-woocommerce' ) . "</span>" .
                                       "<div id='yith-wcpsc-free-to-premium-update__messages'></div>" .
                                       "<div id='yith-wcpsc-free-to-premium-update__progress'><div id='yith-wcpsc-free-to-premium-update__progress__bar'></div><div id='yith-wcpsc-free-to-premium-update__progress__percentage'>0 %</div></div>" .
                                       "<span class='description'>" .
                                       implode( '<br />', array(
                                           __( 'There some size charts assigned to your products from the free version.', 'yith-product-size-charts-for-woocommerce' ),
                                           __( 'To keep them assigned to your product you should simply click on update.', 'yith-product-size-charts-for-woocommerce' ),
                                           __( 'You can skip this process if you want to assign them manually.', 'yith-product-size-charts-for-woocommerce' ),
                                       ) ) .
                                       "</span>" .
                                       "</div>",

                    ),
                    'free-to-premium-end'    => array(
                        'type' => 'sectionend',
                        'id'   => 'yith-wcpsc-free-to-premium'
                    ),
                );
                $settings[ 'settings' ]  = $free_to_premium_options + $settings[ 'settings' ];

            }

            return $settings;
        }

        public function has_free_charts_to_sync() {
            $had_free_version = version_compare( get_option( 'yith_wcpsc_has_free_version', '1.0.0' ), '1.1.10', '>' );
            $skipped          = !!get_option( 'yith_wcpsc_free_to_premium_update_skipped', false );
            $has              = ( $had_free_version && !$skipped ) || isset( $_GET[ 'yith_wcpsc_free_to_premium_update_force' ] );

            if ( $has ) {
                $args = array(
                    'posts_per_page' => 1,
                    'post_type'      => 'yith-wcpsc-wc-chart',
                    'post_status'    => 'publish',
                    'meta_key'       => 'product',
                    'meta_value'     => '',
                    'meta_compare'   => '!=',
                    'fields'         => 'ids'
                );
                $has  = !!get_posts( $args );
            }

            return $has;
        }

        public function get_free_charts_to_sync() {
            $args = array(
                'posts_per_page' => -1,
                'post_type'      => 'yith-wcpsc-wc-chart',
                'post_status'    => 'publish',
                'meta_key'       => 'product',
                'meta_value'     => '',
                'meta_compare'   => '!=',
                'fields'         => 'ids'
            );
            return get_posts( $args );
        }

    }
}