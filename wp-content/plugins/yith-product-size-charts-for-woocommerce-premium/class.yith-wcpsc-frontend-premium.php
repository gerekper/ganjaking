<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Frontend_Premium' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPSC_Frontend_Premium extends YITH_WCPSC_Frontend {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC_Frontend_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {
            parent::__construct();

            // Add all popup on single page product
            add_action( 'woocommerce_before_single_product', array( $this, 'print_popup_charts' ) );

            // Add button in position setted in Settings Tab
            $this->add_button_in_position();

            // Add Shortcode for Product Size Charts
            add_shortcode( 'sizecharts', array( $this, 'shortcode_handler' ) );
            add_shortcode( 'product_size_charts', array( $this, 'product_size_charts_shortcode_handler' ) );

            // Add widget for Product Size Charts
            add_action( 'widgets_init', array( $this, 'register_widgets' ) );

            /* Actions to add a product size chart in php code by users
             *
             * HOW TO USE:
             * int      $id             the id
             * bool     $also_tabs      if true print also the PSC with display mode Tabs
             * string   $type           'button' or 'list'
             *
             * do_action('yith_wcpsc_print_product_size_chart_by_id', $id, $also_tabs); // print the PSC by id
             * do_action('yith_wcpsc_print_product_size_charts', $also_tabs, $type); // print all PSCs
            */
            add_action( 'yith_wcpsc_print_product_size_charts', array( $this, 'print_all_charts' ), 10, 2 );
            add_action( 'yith_wcpsc_print_product_size_chart_by_id', array(
                $this,
                'print_button_by_chart_id'
            ), 10, 2 );
            add_action( 'yith_wcpsc_print_product_size_chart_by_id', array( $this, 'print_popup_chart_by_id' ), 11, 2 );
        }

        /**
         * Add button in position set in Settings Tab
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_button_in_position() {
            $button_position = get_option( 'yith-wcpsc-popup-button-position', 'after_summary' );
            $bp_array        = array( 'action' => 'woocommerce_after_single_product_summary', 'priority' => 9 );
            switch ( $button_position ) {
                case 'before_summary':
                    $bp_array = array( 'action' => 'woocommerce_before_single_product_summary', 'priority' => 25 );
                    break;
                case 'before_description':
                    $bp_array = array( 'action' => 'woocommerce_single_product_summary', 'priority' => 15 );
                    break;
                case 'after_description':
                    $bp_array = array( 'action' => 'woocommerce_single_product_summary', 'priority' => 25 );
                    break;
                case 'after_add_to_cart':
                    $bp_array = array( 'action' => 'woocommerce_single_product_summary', 'priority' => 35 );
                    break;
                case 'after_summary':
                    $bp_array = array( 'action' => 'woocommerce_after_single_product_summary', 'priority' => 9 );
                    break;
            }
            add_action( $bp_array[ 'action' ], array( $this, 'print_button' ), $bp_array[ 'priority' ] );


            add_action( 'yith_wcpsc_size_chart_buttons', array( $this, 'print_button' ) );
        }


        /**
         * add tabs to product [override free method]
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function product_tabs( $tabs ) {
            global $post;

            $charts = $this->get_charts_from_product_id( $post->ID );

            if ( count( $charts ) > 0 ) {
                foreach ( $charts as $chart_id ) {
                    $c = get_post( $chart_id );
                    if ( $c == false )
                        continue;
                    $display_as = get_post_meta( $chart_id, 'display_as', true );

                    $is_tab = !$display_as || 'tab' === $display_as;
                    if ( !$is_tab )
                        continue;

                    $tab_priority = get_post_meta( $chart_id, 'tab_priority', true );
                    $tab_priority = !empty( $tab_priority ) ? $tab_priority : 99;

                    $tab_title = get_post_meta( $chart_id, 'tab_title', true );
                    $tab_title = !!$tab_title ? $tab_title : $c->post_title;

                    $tabs[ 'yith-wcpsc-tab-' . $c->ID ] = array(
                        'title'         => apply_filters( 'yith_wcpsc_tab_title',$tab_title,$post,$chart_id),
                        'priority'      => $tab_priority,
                        'callback'      => array( $this, 'create_tab_content' ),
                        'yith_wcpsc_id' => $c->ID
                    );
                }
            }

            return $tabs;
        }

        /**
         * create the content of table in product page [override free method]
         *
         * @access   public
         * @since    1.0.0
         * @param string $key the key of the tab
         * @param array  $tab array that contains info of tab (title, priority, callback, yith_wcpsc_id)
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function create_tab_content( $key, $tab ) {
            if ( !isset( $tab[ 'yith_wcpsc_id' ] ) )
                return;
            $c_id       = $tab[ 'yith_wcpsc_id' ];

            $table_meta = get_post_meta( $c_id, '_table_meta', true );
            $args       = array(
                'c_id'       => $c_id,
                'table_meta' => $table_meta
            );
            wc_get_template( 'product/table.php', $args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/premium/' );
        }

        /**
         * get charts from product id
         *
         * @access   public
         * @since    1.0.0
         * @param   int $id the id of the product
         * @return array    array of ids of charts
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_charts_from_product_id( $id ) {
            $charts = get_post_meta( $id, 'yith_wcpsc_product_charts', true );
            $charts = !empty( $charts ) ? $charts : array();

            // CATEGORIES CHARTS
            $prod_cats = get_the_terms( $id, 'product_cat' );
            if ( !empty( $prod_cats ) ) {
                foreach ( $prod_cats as $prod_cat ) {
                    $cat_id     = $prod_cat->term_id;
                    $cat_charts = get_option( 'yith-wcpsc-category-charts-' . $cat_id );

                    if ( !empty( $cat_charts ) ) {
                        $c_charts = array();
                        foreach ( $cat_charts as $cat_chart ) {
                            $c_charts[] = $cat_chart;
                        }
                        $charts = array_merge( $charts, array_diff( $c_charts, $charts ) );
                    }
                }
            }

            // CHARTS for ALL PRODUCTS
            $all_products_charts = get_option( 'yith-wcpsc-category-charts-all-products', array() );
            $charts              = array_unique( array_merge( $charts, $all_products_charts ) );

            // WPML
            $charts = array_map( 'yith_wcpsc_wpml_get_current_language_id', $charts );

            return $charts;
        }

        /**
         * Print charts in single product page for popup
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_popup_charts( $also_tabs = false ) {
            global $post;

            $charts = $this->get_charts_from_product_id( $post->ID );

            if ( count( $charts ) > 0 ) {
                foreach ( $charts as $chart_id ) {
                    $this->print_popup_chart_by_id( $chart_id, $also_tabs );
                }
            }
        }

        /**
         * Print product size chart button
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_button( $also_tabs = false ) {
            global $post;

            $charts = $this->get_charts_from_product_id( $post->ID );

            if ( count( $charts ) > 0 ) {
                foreach ( $charts as $chart_id ) {
                    $this->print_button_by_chart_id( $chart_id, $also_tabs );
                }
            }
        }


        /**
         * Print button by chart id
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_button_by_chart_id( $c_id, $also_tabs = false, $type = 'button' ) {
            $post_type = get_post_type( $c_id );
            if ( 'yith-wcpsc-wc-chart' === $post_type ) {
                $is_tab = ( null == get_post_meta( $c_id, 'display_as', true ) ) || get_post_meta( $c_id, 'display_as', true ) == 'tab';
                if ( $is_tab && !$also_tabs )
                    return;

                $table_meta = get_post_meta( $c_id, '_table_meta', true );
                $args       = array(
                    'c_id' => $c_id,
                );

                switch ( $type ) {
                    case 'list':
                        wc_get_template( 'product/list.php', $args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/premium/' );
                        break;
                    case 'button':
                    default:
                        wc_get_template( 'product/button.php', $args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/premium/' );
                }
            }
        }

        /**
         * Print chart for popup by chart id
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_popup_chart_by_id( $c_id, $also_tabs = false ) {
            $post_type = get_post_type( $c_id );
            if ( 'yith-wcpsc-wc-chart' === $post_type ) {
                $is_tab = ( null == get_post_meta( $c_id, 'display_as', true ) ) || get_post_meta( $c_id, 'display_as', true ) == 'tab';
                if ( $is_tab && !$also_tabs )
                    return;

                $table_meta = get_post_meta( $c_id, '_table_meta', true );
                $args       = array(
                    'c_id'       => $c_id,
                    'table_meta' => $table_meta,
                    'is_popup'   => true
                );
                wc_get_template( 'product/table.php', $args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/premium/' );
            }
        }

        /**
         * Print all charts [popup]
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_all_charts( $also_tabs = false, $type = 'button', $args = array(), $get_posts_args = array() ) {
            $c_args = array(
                'post_per_pages' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_type'      => 'yith-wcpsc-wc-chart',
                'post_status'    => 'publish',
                'fields'         => 'ids'
            );
            $c_args = wp_parse_args( $get_posts_args, $c_args );
            $charts = apply_filters( 'yith_wcpsc_all_size_charts', get_posts( $c_args ) );
            if ( !!$charts ) {
                switch ( $type ) {
                    case 'list':
                        echo '<ul class="yith-wcpsc-charts-list">';
                        foreach ( $charts as $chart_id ) {
                            $this->print_button_by_chart_id( $chart_id, $also_tabs, $type );
                            $this->print_popup_chart_by_id( $chart_id, $also_tabs );
                        }

                        echo '</ul>';
                        break;
                    case 'charts':
                        foreach ( $charts as $chart_id ) {
                            $table_meta = get_post_meta( $chart_id, '_table_meta', true );
                            $chart_args = array(
                                'c_id'       => $chart_id,
                                'table_meta' => $table_meta,
                                'is_popup'   => false
                            );
                            if ( isset( $args[ 'show_title' ] ) && $args[ 'show_title' ] === 'yes' ) {
                                echo '<h3>' . get_the_title( $chart_id ) . '</h3>';
                            }
                            wc_get_template( 'product/table.php', $chart_args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/premium/' );
                        }
                        break;
                    case 'button':
                    default:
                        foreach ( $charts as $chart_id ) {
                            $this->print_button_by_chart_id( $chart_id, $also_tabs, $type );
                            $this->print_popup_chart_by_id( $chart_id, $also_tabs );
                        }

                }
            }
        }

        /**
         * Add Shortcode for Product Size Charts
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @param      $atts array the attributes of shortcode
         * @param null $content
         * @return string
         */
        public function shortcode_handler( $atts ) {
            ob_start();
            if ( isset( $atts[ 'id' ] ) ) {
                $id      = $atts[ 'id' ];
                $display = isset( $atts[ 'display' ] ) ? $atts[ 'display' ] : 'popup';
                switch ( $display ) {
                    case 'full':
                        $table_meta = get_post_meta( $id, '_table_meta', true );
                        $chart_args = array(
                            'c_id'       => $id,
                            'table_meta' => $table_meta,
                            'is_popup'   => false
                        );
                        if ( isset( $atts[ 'show_title' ] ) && $atts[ 'show_title' ] === 'yes' ) {
                            echo '<h3>' . get_the_title( $id ) . '</h3>';
                        }
                        wc_get_template( 'product/table.php', $chart_args, YITH_WCPSC_SLUG . '/', YITH_WCPSC_TEMPLATE_PATH . '/premium/' );
                        break;
                    default:
                        $this->print_button_by_chart_id( $id, true );
                        $this->print_popup_chart_by_id( $id, true );
                }
            } else {
                $type = isset( $atts[ 'type' ] ) ? $atts[ 'type' ] : 'button';
                $this->print_all_charts( true, $type, $atts );
            }

            return ob_get_clean();
        }

        /**
         * product_size_charts shortcode: print all size charts of the current product
         *
         * @param array $atts
         * @since 1.1.12
         */
        public function product_size_charts_shortcode_handler( $atts = array() ) {
            global $product;
            if ( $product instanceof WC_Product ) {
                $type      = isset( $atts[ 'type' ] ) ? $atts[ 'type' ] : 'button';
                $chart_ids = $this->get_charts_from_product_id( $product->get_id() );
                if ( $chart_ids ) {
                    $this->print_all_charts( true, $type, $atts, array( 'post__in' => $chart_ids ) );
                }
            }
        }

        public function enqueue_scripts() {
            parent::enqueue_scripts();

            wp_enqueue_style( 'dashicons' );

            wp_enqueue_style( 'jquery-ui-style-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css' );
            wp_enqueue_script( 'yith_wcpsc_popup_js', YITH_WCPSC_ASSETS_URL . '/js/yith_wcpsc_popup.js', array( 'jquery' ), '1.0.0', true );
            wp_enqueue_script( 'yith_wcpsc_frontend_js', YITH_WCPSC_ASSETS_URL . '/js/frontend_premium.js', array( 'jquery', 'jquery-ui-tabs' ), '1.0.0', true );
            wp_localize_script( 'yith_wcpsc_frontend_js', 'ajax_object', array(
                'popup_position' => get_option( 'yith-wcpsc-popup-position', 'center' ),
                'popup_effect'   => get_option( 'yith-wcpsc-popup-effect', 'fade' ),
            ) );

            wp_add_inline_style( 'yith-wcpsc-frontent-styles', $this->get_inline_css() );
        }

        /**
         * Get the css to add inline for custom styling
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_inline_css() {
            $overlay_bg = get_option( 'yith-wcpsc-popup-overlay-color', '#000000' );
            $overlay_op = get_option( 'yith-wcpsc-popup-overlay-opacity', '0.8' );

            // Overlay
            $css
                = ".yith-wcpsc-overlay{
                background: {$overlay_bg};
                opacity: {$overlay_op};
            }";

            $button_padding_opt = get_option( 'yith-wcpsc-popup-button-padding', array( 10, 20, 10, 20 ) );
            $button_padding     = implode( 'px ', $button_padding_opt ) . 'px';

            $button_color         = get_option( 'yith-wcpsc-popup-button-color', '#b369a5' );
            $button_text_color    = get_option( 'yith-wcpsc-popup-button-text-color', '#ffffff' );
            $button_border_radius = get_option( 'yith-wcpsc-popup-button-border-radius', '3' );
            $button_shadow_color  = get_option( 'yith-wcpsc-popup-button-shadow-color', '#dddddd' );
            $button_color_light   = wc_hex_lighter( $button_color, 20 );
            $css
                                  .= ".yith-wcpsc-product-size-chart-button{
                background: {$button_color};
                border: none;
                border-radius: {$button_border_radius}px;
                color: {$button_text_color};
                display: inline-block;
                padding: {$button_padding};
                text-decoration: none;
                margin: 5px 3px;
                cursor: pointer;
                box-shadow: 0px 2px 7px -2px {$button_shadow_color};
            }
            .yith-wcpsc-product-size-chart-button:hover{
                 background: {$button_color_light};
            }
            ";

            $table_style            = get_option( 'yith-wcpsc-table-style', 'default' );
            $base_table_color       = get_option( 'yith-wcpsc-table-base-color', '#f9f9f9' );
            $base_table_color_dark  = wc_hex_darker( $base_table_color, 10 );
            $base_table_color_light = wc_hex_lighter( $base_table_color );

            switch ( $table_style ) {
                case 'default':
                    $css
                        .= ".yith-wcpsc-product-table-default td, .yith-wcpsc-product-table-default th {
                        border     : 1px solid {$base_table_color_dark} !important;
                    }
                    .yith-wcpsc-product-table-default tr > td:first-child, .yith-wcpsc-product-table-default th {
                        background  : {$base_table_color} !important;
                    }";
                    break;
                case 'informal':
                    $css
                        .= ".yith-wcpsc-product-table-informal tr > td:first-child .yith-wcpsc-product-table-td-content {
                        background : {$base_table_color} !important;
                    }";
                    break;
                case 'elegant':
                    $css
                        .= ".yith-wcpsc-product-table-elegant th {
                        border-top-color: {$base_table_color} !important;
                        border-bottom-color: {$base_table_color} !important;
                    }
                    .yith-wcpsc-product-table-elegant tr td {
                        border-bottom-color: {$base_table_color_light} !important;
                    }";
                    break;
                case 'casual':
                    $css
                        .= ".yith-wcpsc-product-table-casual th {
                            color: {$base_table_color} !important;
                        }
                        .yith-wcpsc-product-table-casual tr td {
                            background  : #f9f9f9 !important;
                        }

                        .yith-wcpsc-product-table-casual tr > td:first-child {
                            background : {$base_table_color_light} !important;
                        }";
                    break;
            }

            $popup_style               = get_option( 'yith-wcpsc-popup-style', 'default' );
            $base_popup_color          = get_option( 'yith-wcpsc-popup-base-color', '#f9f9f9' );
            $base_popup_color_dark     = wc_hex_darker( $base_popup_color, 10 );
            $base_popup_color_light    = wc_hex_lighter( $base_popup_color );
            $base_popup_color_light_70 = wc_hex_lighter( $base_popup_color, 70 );

            switch ( $popup_style ) {
                case 'default':
                    $css
                        .= ".yith-wcpsc-product-size-charts-popup{
                                box-shadow: 2px 2px 10px 1px {$base_popup_color_dark} !important;
                                background: {$base_popup_color} !important;
                                border: 1px solid {$base_popup_color_dark} !important;
                            }
                            ul.yith-wcpsc-tabbed-popup-list li.ui-state-active {
                                background : {$base_popup_color} !important;
                            }";
                    break;
                case 'informal':
                    $css
                        .= ".yith-wcpsc-product-size-charts-popup-informal{
                                background: {$base_popup_color} !important;
                            }
                            .yith-wcpsc-product-size-charts-popup-informal span.yith-wcpsc-product-size-charts-popup-close{
                                color: {$base_popup_color_light} !important;
                            }";
                    break;
                case 'elegant':
                    $css
                        .= ".yith-wcpsc-product-size-charts-popup-elegant span.yith-wcpsc-product-size-charts-popup-close{
                                color: {$base_popup_color} !important;
                                background: {$base_popup_color_light_70} !important;
                            }";
                    break;
                case 'casual':
                    $css
                        .= ".yith-wcpsc-product-size-charts-popup-casual{
                                background: {$base_popup_color} !important;
                            }
                            .yith-wcpsc-product-size-charts-popup-casual span.yith-wcpsc-product-size-charts-popup-close{
                                background: {$base_popup_color} !important;
                            }";
                    break;
            }

            return $css;
        }

        /*
         * Check if current device is mobile device
         */
        public function is_mobile() {
            return preg_match( "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER[ "HTTP_USER_AGENT" ] );
        }


        /**
         * register Widget for Charts
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_widgets() {
            register_widget( 'YITH_WCPSC_Product_Size_Charts_Widget' );
        }
    }
}
/**
 * Unique access to instance of YITH_WCPSC_Frontend class
 *
 * @deprecated since 1.1.0 use YITH_WCPSC_Frontend() instead
 * @return YITH_WCPSC_Frontend_Premium
 * @since      1.0.0
 */
function YITH_WCPSC_Frontend_Premium() {
    return YITH_WCPSC_Frontend();
}