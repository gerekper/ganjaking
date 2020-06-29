<?php
/**
 * Widget
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Product_Size_Charts_Widget' ) ) {
    /**
     * YITH_WCPSC_Bundle_Widget
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPSC_Product_Size_Charts_Widget extends WC_Widget {
        /**
         * Constructor
         */
        public function __construct() {
            $this->widget_cssclass    = 'yith_wcpsc_widget';
            $this->widget_description = __( 'Display a list with your Product Size Charts.', 'yith-product-size-charts-for-woocommerce' );
            $this->widget_id          = 'yith_wcpsc_widget';
            $this->widget_name        = __( 'YITH Product Size Charts', 'yith-product-size-charts-for-woocommerce' );

            $this->settings = array(
                'title'  => array(
                    'type'  => 'text',
                    'std'   => _x( 'Product Size Charts', 'default title for plugin widget', 'yith-product-size-charts-for-woocommerce' ),
                    'label' => __( 'Title', 'yith-product-size-charts-for-woocommerce' )
                ),
                'number' => array(
                    'type'  => 'number',
                    'step'  => 1,
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 5,
                    'label' => __( 'Number of charts to show', 'woocommerce' )
                ),
            );

            parent::__construct();
        }

        /**
         * Query the charts and return them
         *
         * @param  array $args
         * @param  array $instance
         *
         * @return WP_Query
         */
        public function get_charts( $args, $instance ) {
            $number     = !empty( $instance[ 'number' ] ) ? absint( $instance[ 'number' ] ) : $this->settings[ 'number' ][ 'std' ];
            $query_args = array(
                'posts_per_page' => $number,
                'post_status'    => 'publish',
                'post_type'      => 'yith-wcpsc-wc-chart',
                'no_found_rows'  => 1,
                'order'          => 'ASC',
                'orderby'        => 'title',
                'meta_key'       => 'show_in_widget',
                'meta_value'     => 1,
                'fields'         => 'ids'
            );

            return get_posts( $query_args );
        }

        public function widget( $args, $instance ) {
            if ( $this->get_cached_widget( $args ) ) {
                return;
            }

            if ( ( $charts = $this->get_charts( $args, $instance ) ) && count( $charts ) > 0 ) {
                $frontend = YITH_WCPSC_Frontend();
                $this->widget_start( $args, $instance );

                echo apply_filters( 'yith_wcpsc_before_widget_charts_list', '<ul class="yith_wcpsc_list_widget">' );

                foreach ( $charts as $chart_id ) {
                    echo $frontend->print_button_by_chart_id( $chart_id, true, 'list' );
                    echo $frontend->print_popup_chart_by_id( $chart_id, true );
                }

                echo apply_filters( 'yith_wcpsc_after_widget_product_list', '</ul>' );

                $this->widget_end( $args );
            }
            wp_reset_postdata();
            echo $this->cache_widget( $args, ob_get_clean() );
        }
    }
}