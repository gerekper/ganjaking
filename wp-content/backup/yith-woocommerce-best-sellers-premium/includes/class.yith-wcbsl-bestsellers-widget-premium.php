<?php
/**
 * Widget
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_Bestsellers_Widget' ) ) {
    /**
     * YITH_WCBSL_Bestsellers_Widget
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBSL_Bestsellers_Widget extends WC_Widget {
        /**
         * Constructor
         */
        public function __construct() {
            $this->widget_cssclass    = 'yith_wcbsl_widget';
            $this->widget_description = __( 'Display best-seller products.', 'yith-woocommerce-best-sellers' );
            $this->widget_id          = 'yith_wcbsl_widget';
            $this->widget_name        = __( 'YITH WooCommerce Best Sellers', 'yith-woocommerce-best-sellers' );

            $this->settings = array(
                'title'       => array(
                    'type'  => 'text',
                    'std'   => __( 'Best Sellers', 'yith-woocommerce-best-sellers' ),
                    'label' => __( 'Title', 'yith-woocommerce-best-sellers' )
                ),
                'type'        => array(
                    'type'    => 'select',
                    'options' => array(
                        'bestsellers' => __( 'Best Sellers', 'yith-woocommerce-best-sellers' ),
                        'newest'      => __( 'Newest Best Sellers', 'yith-woocommerce-best-sellers' ),
                    ),
                    'std'     => 'bestsellers',
                    'label'   => _x( 'Type','type of widget', 'yith-woocommerce-best-sellers' )
                ),
                'number'      => array(
                    'type'  => 'number',
                    'step'  => 1,
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 3,
                    'label' => __( 'Number of products to show', 'woocommerce' )
                ),
                'show_thumb'  => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __( 'Show thumbnails for best-seller products', 'yith-woocommerce-best-sellers' )
                )
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
        public function get_best_sellers( $args, $instance ) {
            $number       = !empty( $instance[ 'number' ] ) ? absint( $instance[ 'number' ] ) : $this->settings[ 'number' ][ 'std' ];
            $show_newest  = !empty( $instance[ 'type' ] ) ? $instance[ 'type' ] == 'newest' : $this->settings[ 'show_newest' ][ 'std' ] == 'newest';
            $reports      = new YITH_WCBSL_Reports_Premium();
            $range        = get_option( 'yith-wcpsc-update-time', '7day' );
            $best_sellers = array();
            if ( $show_newest ) {
                $best_sellers = $reports->get_newest_bestsellers( $range, array(), $number );
            } else {
                $best_sellers = $reports->get_best_sellers( $range, array( 'limit' => $number ) );
            }

            return $best_sellers;
        }

        public function widget( $args, $instance ) {
            $number      = !empty( $instance[ 'number' ] ) ? absint( $instance[ 'number' ] ) : $this->settings[ 'number' ][ 'std' ];
            $show_newest  = !empty( $instance[ 'type' ] ) ? $instance[ 'type' ] == 'newest' : $this->settings[ 'type' ][ 'std' ] == 'newest';
            $show_thumb  = !empty( $instance[ 'show_thumb' ] ) ? $instance[ 'show_thumb' ] : $this->settings[ 'show_thumb' ][ 'std' ];
            if ( $this->get_cached_widget( $args ) ) {
                return;
            }

            ob_start();

            if ( ( $best_sellers = $this->get_best_sellers( $args, $instance ) ) && count( $best_sellers ) > 0 ) {
                $this->widget_start( $args, $instance );

                echo apply_filters( 'yith_wcbsl_before_widget_bestsellers_list', '<ul class="yith_wcbsl_bestsellers_list_widget">' );

                $best_sellers = array_slice( $best_sellers, 0, $number );

                $loop = 0;
                foreach ( $best_sellers as $product ) {
                    $loop++;
                    $bs_id         = absint( $product->product_id );
                    $template_args = array(
                        'id'         => $bs_id,
                        'loop'       => $loop,
                        'show_thumb' => $show_thumb
                    );
                    wc_get_template( '/widget/bestseller.php', $template_args, '', YITH_WCBSL_TEMPLATE_PATH );
                }

                echo apply_filters( 'yith_wcbsl_after_widget_bestsellers_list', '</ul>' );

                if ( $show_newest ) {
                    $bestsellers_page_id        = get_option( 'yith-wcbsl-bestsellers-page-id' );
                    $bestsellers_page_permalink = $bestsellers_page_id ? get_permalink( $bestsellers_page_id ) : '#';
                    $bestsellers_page_permalink .= '?newest=1';

                    $text_top_100 = sprintf( __( 'show Newest Top 100', 'yith-woocommerce-best-sellers' ) );

                    echo '<div class="yith-wcbsl-bestseller-positioning-in-product-wrapper">';
                    echo "<a href='{$bestsellers_page_permalink}'>{$text_top_100}</a>";
                    echo '</div>';

                }


                $this->widget_end( $args );
            }
            wp_reset_postdata();
            echo $this->cache_widget( $args, ob_get_clean() );
        }
    }
}