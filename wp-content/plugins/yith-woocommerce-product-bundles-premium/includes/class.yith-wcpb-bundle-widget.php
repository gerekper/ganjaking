<?php
/**
 * Widget
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPB' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPB_Bundle_Widget' ) ) {
    /**
     * YITH_WCPB_Bundle_Widget
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPB_Bundle_Widget extends WC_Widget {
        /**
         * Constructor
         */
        public function __construct() {
            $this->widget_cssclass    = 'yith_wcpb_bundle_widget';
            $this->widget_description = __( 'Display a list of your product bundles on your site.', 'yith-woocommerce-product-bundles' );
            $this->widget_id          = 'yith_wcpb_bundle_widget';
            $this->widget_name        = __( 'YITH WooCommerce Product Bundle', 'yith-woocommerce-product-bundles' );

            $this->settings = array(
                'title'                   => array(
                    'type'  => 'text',
                    'std'   => __( 'Product Bundles', 'yith-woocommerce-product-bundles' ),
                    'label' => __( 'Title', 'yith-woocommerce-product-bundles' )
                ),
                'number'                  => array(
                    'type'  => 'number',
                    'step'  => 1,
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 5,
                    'label' => __( 'Number of products to show', 'woocommerce' )
                ),
                'show'                    => array(
                    'type'    => 'select',
                    'std'     => '',
                    'label'   => __( 'Show', 'woocommerce' ),
                    'options' => array(
                        ''         => __( 'All Products', 'woocommerce' ),
                        'featured' => __( 'Featured Products', 'woocommerce' ),
                        'onsale'   => __( 'On Sale Products', 'woocommerce' ),
                    )
                ),
                'orderby'                 => array(
                    'type'    => 'select',
                    'std'     => 'date',
                    'label'   => __( 'Order by', 'woocommerce' ),
                    'options' => array(
                        'date'  => __( 'Date', 'woocommerce' ),
                        'price' => __( 'Price', 'woocommerce' ),
                        'rand'  => __( 'Random', 'woocommerce' ),
                        'sales' => __( 'Sales', 'woocommerce' ),
                    )
                ),
                'order'                   => array(
                    'type'    => 'select',
                    'std'     => 'desc',
                    'label'   => _x( 'Order', 'Sorting order', 'woocommerce' ),
                    'options' => array(
                        'asc'  => __( 'ASC', 'woocommerce' ),
                        'desc' => __( 'DESC', 'woocommerce' ),
                    )
                ),
                'hide_free'               => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __( 'Hide free bundles', 'woocommerce' )
                ),
                'show_hidden'             => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __( 'Show hidden bundles', 'woocommerce' )
                ),
                'show_bundled'            => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __( 'Show products in bundle', 'yith-woocommerce-product-bundles' )
                ),
                'show_bundled_thumb'      => array(
                    'type'  => 'checkbox',
                    'std'   => 1,
                    'label' => __( 'Show thumbnails for products in bundle', 'yith-woocommerce-product-bundles' )
                ),
                'show_only_parent_bundle' => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __( 'Show only bundles including the currently viewed product', 'yith-woocommerce-product-bundles' )
                ),
            );

            parent::__construct();
        }

        /**
         * Query the products and return them
         *
         * @param  array $args
         * @param  array $instance
         *
         * @return WP_Query
         */
        public function get_products( $args, $instance ) {
            $number      = !empty( $instance[ 'number' ] ) ? absint( $instance[ 'number' ] ) : $this->settings[ 'number' ][ 'std' ];
            $show        = !empty( $instance[ 'show' ] ) ? sanitize_title( $instance[ 'show' ] ) : $this->settings[ 'show' ][ 'std' ];
            $orderby     = !empty( $instance[ 'orderby' ] ) ? sanitize_title( $instance[ 'orderby' ] ) : $this->settings[ 'orderby' ][ 'std' ];
            $order       = !empty( $instance[ 'order' ] ) ? sanitize_title( $instance[ 'order' ] ) : $this->settings[ 'order' ][ 'std' ];
            $only_parent = !empty( $instance[ 'show_only_parent_bundle' ] ) ? sanitize_title( $instance[ 'show_only_parent_bundle' ] ) : $this->settings[ 'show_only_parent_bundle' ][ 'std' ];

            $query_args = array(
                'posts_per_page' => $number,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'no_found_rows'  => 1,
                'order'          => $order,
                'meta_query'     => array(),
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => 'yith_bundle'
                    )
                )
            );

            if ( $only_parent ) {
                $product = wc_get_product();
                if ( !$product )
                    return false;

                $product_id           = $product->get_id();
                $product_id_strlen    = strlen( (string) $product_id );
                $meta_value_of_parent = '"product_id";s:' . $product_id_strlen . ':"' . $product_id . '"';

                $query_args[ 'meta_query' ][] = array(
                    'key'     => '_yith_wcpb_bundle_data',
                    'value'   => $meta_value_of_parent,
                    'compare' => 'LIKE'
                );

            }


            if ( empty( $instance[ 'show_hidden' ] ) ) {
                //$query_args[ 'meta_query' ][] = WC()->query->visibility_meta_query();
                $query_args[ 'post_parent' ]  = 0;
            }

            if ( !empty( $instance[ 'hide_free' ] ) ) {
                $query_args[ 'meta_query' ][] = array(
                    'key'     => '_price',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'DECIMAL',
                );
            }

            //$query_args[ 'meta_query' ][] = WC()->query->stock_status_meta_query();
            $query_args[ 'meta_query' ]   = array_filter( $query_args[ 'meta_query' ] );

            switch ( $show ) {
                case 'featured' :
                    $query_args[ 'meta_query' ][] = array(
                        'key'   => '_featured',
                        'value' => 'yes'
                    );
                    break;
                case 'onsale' :
                    $product_ids_on_sale      = wc_get_product_ids_on_sale();
                    $product_ids_on_sale[]    = 0;
                    $query_args[ 'post__in' ] = $product_ids_on_sale;
                    break;
            }

            switch ( $orderby ) {
                case 'price' :
                    $query_args[ 'meta_key' ] = '_price';
                    $query_args[ 'orderby' ]  = 'meta_value_num';
                    break;
                case 'rand' :
                    $query_args[ 'orderby' ] = 'rand';
                    break;
                case 'sales' :
                    $query_args[ 'meta_key' ] = 'total_sales';
                    $query_args[ 'orderby' ]  = 'meta_value_num';
                    break;
                default :
                    $query_args[ 'orderby' ] = 'date';
            }

            return new WP_Query( $query_args );
        }

        public function widget( $args, $instance ) {
            $show_bundled       = !empty( $instance[ 'show_bundled' ] );
            $show_bundled_thumb = !empty( $instance[ 'show_bundled_thumb' ] ) ? $instance[ 'show_bundled_thumb' ] : $this->settings[ 'show_bundled_thumb' ][ 'std' ];

            if ( $this->get_cached_widget( $args ) ) {
                return;
            }

            ob_start();

            if ( ( $products = $this->get_products( $args, $instance ) ) && $products->have_posts() ) {

                $this->widget_start( $args, $instance );

                echo apply_filters( 'yith_wcpb_before_widget_product_list', '<ul class="yith_wcpb_bundle_list_widget">' );

                $template_args = array(
                    'show_rating'        => false,
                    'show_bundled'       => $show_bundled,
                    'show_bundled_thumb' => $show_bundled_thumb
                );

                while ( $products->have_posts() ) {
                    $products->the_post();
                    global $product;

                    // to override the template the admin can put a file called content-widget-product.php in wp-content/themes/THEME_FOLDER/yith-woocommerce-product-bundles/
                    $theme_template_path = 'yith-woocommerce-product-bundles/';
                    wc_get_template( 'content-widget-product.php', $template_args, $theme_template_path, YITH_WCPB_TEMPLATE_PATH . '/premium/' );
                }

                echo apply_filters( 'yith_wcpb_after_widget_product_list', '</ul>' );

                $this->widget_end( $args );
            }
            wp_reset_postdata();
            echo $this->cache_widget( $args, ob_get_clean() );
        }
    }
}