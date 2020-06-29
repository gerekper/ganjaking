<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers Premium
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_Frontend_Premium' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0.0
     */
    class YITH_WCBSL_Frontend_Premium extends YITH_WCBSL_Frontend {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCBSL_Frontend_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct() {
            parent::__construct();

            $show_position_in_bestsellers = get_option( 'yith-wcbsl-show-position-in-bestsellers', 'yes' ) == 'yes';
            if ( $show_position_in_bestsellers ) {
                add_action( 'woocommerce_product_meta_start', array( $this, 'print_position_in_bestsellers' ) );
            }

            $show_link_bestseller_category_in_prod = get_option( 'yith-wcbsl-show-link-bestseller-category-in-prod', 'no' ) == 'yes';
            if ( $show_link_bestseller_category_in_prod )
                add_action( 'woocommerce_product_meta_start', array( $this, 'print_link_bestseller_category_in_prod' ) );

            $show_link_bestseller_category_in_cat = get_option( 'yith-wcbsl-show-link-bestseller-category-in-cat', 'no' ) == 'yes';
            if ( $show_link_bestseller_category_in_cat )
                add_action( 'woocommerce_archive_description', array( $this, 'print_link_bestseller_category_in_cat' ) );


            remove_action( 'do_feed_rss2', 'do_feed_rss2' );
            add_action( 'do_feed_rss2', array( $this, 'print_feed_rss2' ), 10, 1 );

            $show_rss_link = get_option( 'yith-wcbsl-show-rss-link-for-bestsellers', 'yes' ) == 'yes';
            if ( $show_rss_link )
                add_action( 'yith_wcbsl_rss_link', array( $this, 'add_rss_link' ), 10, 2 );

            $show_icon = get_option( 'yith-wcbsl-show-icon-in-bestsellers', 'no' ) == 'yes';
            if ( $show_icon )
                $this->add_icon_in_position();

            // Add Shortcode for Best Sellers SLIDER
            add_shortcode( 'bestsellers_slider', array( $this, 'slider_shortcode_handler' ) );

            // Edit title
            add_filter( 'wp_title', array( $this, 'modify_title' ), 10, 2 );

            // Compatibility with YITH themes Best Sellers in products_slider shortcode
            add_filter( 'yit_theme_products_in_slider', array( $this, 'yit_theme_products_in_slider' ), 10, 2 );

        }


        /**
         * Filter Best Sellers for themes [products_slider shortcode]
         *
         * @param WP_Query $query
         * @param array    $args array of arguments: query_args | featured | best_sellers | on_sale | cat | posts_per_page
         * @return WP_Query the wp query that contain products
         */
        public function yit_theme_products_in_slider( $query, $args ) {
            $best_sellers = isset( $args[ 'best_sellers' ] ) ? $args[ 'best_sellers' ] : '';
            if ( $best_sellers != 'yes' )
                return $query;

            $reports = new YITH_WCBSL_Reports_Premium();

            $selected_categories = !empty( $args[ 'cat' ] ) ? explode( ',', $args[ 'cat' ] ) : array();
            $range               = get_option( 'yith-wcpsc-update-time', '7day' );
            $range_args          = array();
            $per_page            = !empty( $args[ 'per_page' ] ) ? $args[ 'per_page' ] : YITH_WCBSL()->get_limit();

            $best_sellers_array = array();
            if ( !empty( $selected_categories ) ) {
                $best_sellers_array = $reports->get_best_sellers_in_category( $selected_categories, $range, $range_args, array( 'limit' => $per_page ) );
            } else {
                $best_sellers_array = $reports->get_best_sellers( $range, array( 'range_args' => $range_args, 'limit' => $per_page ) );
            }

            $bestseller_products = array();
            foreach ( $best_sellers_array as $bs ) {
                $product = get_post( $bs->product_id );
                if ( $product ) {
                    $bestseller_products[] = $product;
                }
            }

            $query->posts = $bestseller_products;

            return $query;
        }


        /**
         * Add Best Seller Icon in position set in Settings Tab
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_icon_in_position() {
            $button_position = get_option( 'yith-wcbsl-bestseller-icon-position', 'before_summary' );
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
            add_action( $bp_array[ 'action' ], array( $this, 'print_icon' ), $bp_array[ 'priority' ] );
        }

        /**
         * Print Best Sellers Icon
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_icon() {
            global $product;
            $range = get_option( 'yith-wcpsc-update-time', '7day' );

            $base_product_id = yit_get_base_product_id( $product );

            $bestseller_info = $this->reports->check_is_bestseller( $base_product_id, $range );
            if ( !$bestseller_info )
                return;

            $icon_only_for_top = get_option( 'yith-wcbsl-bestseller-icon-only-for-top', 'no' ) === 'yes';
            $show_icon         = true;

            if ( $icon_only_for_top ) {
                if ( is_array( $bestseller_info ) ) {
                    $current_bestseller = current( $bestseller_info );
                    $show_icon          = isset( $current_bestseller[ 'title' ] ) && 'yith_wcbsl_all' === $current_bestseller[ 'title' ];
                }
            }

            if ( !$show_icon ) {
                return;
            }

            $icon_url = get_option( 'yith-wcbsl-bestseller-icon', YITH_WCBSL_ASSETS_URL . '/images/best-seller.png' );
            $align    = get_option( 'yith-wcbsl-bestseller-icon-align', 'right' );
            echo "<div class='yith-wcbsl-bestseller-icon-container' style='text-align:{$align}'><img class='yith-wcbsl-bestseller-icon' src='{$icon_url}' /></div>";
        }

        /**
         * Add rss link
         *
         * @access public
         * @since  1.0.0
         */
        public function add_rss_link( $cat_id, $show_newest_bestsellers = false ) {
            $bestsellers_page_id = get_option( 'yith-wcbsl-bestsellers-page-id' );
            $link                = get_permalink( $bestsellers_page_id ) . '?feed=rss2';
            if ( $show_newest_bestsellers ) {
                $link .= '&newest=1';
            } elseif ( $cat_id > 0 ) {
                $cat  = get_term( $cat_id, 'product_cat' );
                $link .= '&bs_cat=' . $cat_id;
            }

            $args = array(
                'link' => $link
            );

            wc_get_template( '/link-feed.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
        }

        /**
         * print Custom Feeds for bestsellers
         *
         * @access public
         * @since  1.0.0
         */
        public function print_feed_rss2( $for_comments ) {
            $bestsellers_page_id = get_option( 'yith-wcbsl-bestsellers-page-id' );
            if ( is_page( $bestsellers_page_id ) ) {
                wc_get_template( '/bestsellers-feed.php', array(), YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
            } else {
                do_feed_rss2( $for_comments ); // Call default function
            }
        }

        /**
         * Print position in Best Sellers
         *
         * @access public
         * @since  1.0.0
         */
        public function print_position_in_bestsellers() {
            global $product;
            $range = get_option( 'yith-wcpsc-update-time', '7day' );

            $base_product_id = yit_get_base_product_id( $product );

            $args = array(
                'id'            => $base_product_id,
                'bestseller_in' => $this->reports->check_is_bestseller( $base_product_id, $range )
            );

            wc_get_template( '/single-product/position-in-bestsellers.php', $args, '', YITH_WCBSL_TEMPLATE_PATH );
        }

        /**
         * Print link for BestSellers in first category of product
         *
         * @access public
         * @since  1.0.0
         */
        public function print_link_bestseller_category_in_prod() {
            global $product;
            $range = get_option( 'yith-wcpsc-update-time', '7day' );

            $base_product_id = yit_get_base_product_id( $product );

            $cats     = get_the_terms( $base_product_id, 'product_cat' );
            $cat_name = '';
            $cat_id   = '';

            if ( !empty( $cats ) ) {
                $first_cat = $cats[ 0 ];
                $cat_name  = $first_cat->name;
                $cat_id    = $first_cat->term_id;
            }

            $args = array(
                'id'       => $base_product_id,
                'cat_name' => $cat_name,
                'cat_id'   => $cat_id,
            );

            if ( $this->reports->get_best_sellers_in_category( $cat_id, $range ) )
                wc_get_template( '/single-product/link-bestseller-category.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
        }

        /**
         * Print link for BestSellers category pages
         *
         * @access public
         * @since  1.0.0
         */
        public function print_link_bestseller_category_in_cat() {
            global $wp_query;
            $cat = $wp_query->get_queried_object();


            if ( !empty( $cat ) ) {
                if ( isset( $cat->name ) && isset( $cat->term_id ) ) {
                    $cat_name = $cat->name;
                    $cat_id   = $cat->term_id;

                    $args = array(
                        'cat_name' => $cat_name,
                        'cat_id'   => $cat_id,
                    );

                    wc_get_template( '/single-product/link-bestseller-category.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
                }
            }
        }

        /**
         * Add Shortcode for Bestsellers
         * EXAMPLE:
         * <code>
         *  [bestsellers cats="56,57"]
         * </code>
         * this code displays all bestsellers of categories with ids 56 and 57.
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @param      $atts array the attributes of shortcode
         * @param null $content
         * @return string
         */
        public function shortcode_handler( $atts, $content = null ) {
            ob_start();
            wc_get_template( 'bestsellers.php', $atts, '', YITH_WCBSL_TEMPLATE_PATH . '/' );

            return ob_get_clean();
        }

        /**
         * Add Shortcode for Bestsellers SLIDER
         * EXAMPLE:
         * <code>
         *  [bestsellers_slider]
         * </code>
         * this code displays slider for bestsellers.
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @param      $atts array the attributes of shortcode
         * @param null $content
         * @return string
         */
        public function slider_shortcode_handler( $atts, $content = null ) {
            $defaults = array(
                'animate' => 'yes',
                'delay'     => '6000',
                'category'  => '',
                'range'     => get_option( 'yith-wcpsc-update-time', '7day' ),
                'title'     => get_option( 'yith-wcbsl-slider-title', _x( 'Best Sellers', 'Text of "Bestsellers" Slider', 'yith-woocommerce-best-sellers' ) )
            );

            $atts = shortcode_atts( $defaults, $atts, 'bestsellers_slider' );
            /**
             * @var string $animate
             * @var string $delay
             * @var string $category
             * @var string $range
             * @var string $title
             */
            extract( $atts );

            $reports      = new YITH_WCBSL_Reports_Premium();
            $category     = absint( $category );
            $delay        = absint( $delay );
            $best_sellers = !$category ? $reports->get_best_sellers( $range ) : $reports->get_best_sellers_in_category( $category, $range );

            ob_start();
            wc_get_template( 'slider/bestsellers.php', compact( 'best_sellers', 'animate', 'delay', 'category', 'range', 'title' ), '', YITH_WCBSL_TEMPLATE_PATH . '/' );

            return ob_get_clean();
        }


        /**
         * Display Bestsellers Badge in Shop Page
         *
         * @access public
         * @since  1.1.4
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function print_badge() {
            global $product;

            $base_product_id = yit_get_base_product_id( $product );

            if ( !$this->display_badge_on_bestseller( $base_product_id ) )
                return;

            $args[ 'class' ] = 'yith-wcbsl-mini-badge';
            wc_get_template( '/bestseller-badge.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
        }

        public function display_badge_on_bestseller( $product_id ) {
            $range              = get_option( 'yith-wcpsc-update-time', '7day' );
            $bestseller_info    = $this->reports->check_is_bestseller( $product_id, $range );
            $is_bestseller      = !!$bestseller_info;
            $badge_only_for_top = get_option( 'yith-wcbsl-bestsellers-badge-only-for-top', 'no' ) === 'yes';
            $show_badge         = false;

            if ( $badge_only_for_top ) {
                if ( is_array( $bestseller_info ) ) {
                    $current_bestseller = current( $bestseller_info );
                    $show_badge         = isset( $current_bestseller[ 'title' ] ) && 'yith_wcbsl_all' === $current_bestseller[ 'title' ];
                }
            } else {
                $show_badge = $is_bestseller;
            }

            return !!$show_badge;
        }

        /**
         * Show Bestseller Badge in products
         *
         * @access public
         * @return string
         * @param $val        string product image
         * @param $product_id int product id
         * @param $args       array
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function show_bestseller_badge( $val, $product_id, $args = array() ) {

            if ( !$this->display_badge_on_bestseller( $product_id ) )
                return $val;

            $default_args = array(
                'class' => '',
            );

            $args = wp_parse_args( $args, $default_args );

            ob_start();
            wc_get_template( '/bestseller-badge.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
            $val = $val . ob_get_clean();

            return $val;
        }


        /**
         * Modify title in header for Categories Bestsellers Page
         *
         * @access public
         * @return string
         * @param $title        string the title
         * @param $sep          string the separator
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function modify_title( $title, $sep ) {
            $is_cat = isset( $_GET[ 'bs_cat' ] ) ? true : false;
            $cat_id = 0;
            // Controllo Categoria Esistente
            if ( $is_cat ) {
                $cat_id = absint( $_GET[ 'bs_cat' ] );
                $cat    = get_term( $cat_id, 'product_cat' );
                if ( !empty( $cat ) ) {
                    $bestsellers_page_id = get_option( 'yith-wcbsl-bestsellers-page-id' );
                    if ( $bestsellers_page_id == get_the_ID() ) {
                        return $cat->name . ' ' . $title;
                    }
                }
            }

            return $title;
        }


        public function enqueue_scripts() {
            parent::enqueue_scripts();
            wp_add_inline_style( 'yith_wcbsl_frontend_style', $this->get_inline_css() );

            wp_enqueue_style( 'dashicons' );

            wp_enqueue_script( 'yith_fl_slider_js', YITH_WCBSL_ASSETS_URL . '/js/yith_fl_slider.js', array( 'jquery' ), YITH_WCBSL_VERSION, true );
            wp_enqueue_script( 'yith_wcbsl_frontend_js', YITH_WCBSL_ASSETS_URL . '/js/frontend.js', array( 'jquery', 'yith_fl_slider_js' ), YITH_WCBSL_VERSION, true );
        }

        /**
         * Get the css to add inline for custom styling
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_inline_css() {
            $badge_bg      = get_option( 'yith-wcbsl-badge-bg-color', '#A00000' );
            $badge_text    = get_option( 'yith-wcbsl-badge-text-color', '#FFFFFF' );
            $link_bg       = get_option( 'yith-wcbsl-link-bg-color', '#A00000' );
            $link_text     = get_option( 'yith-wcbsl-link-text-color', '#FFFFFF' );
            $link_bg_dark  = wc_hex_darker( $link_bg, 20 );
            $link_bg_light = wc_hex_lighter( $link_bg, 20 );

            $css = '';

            $css
                .= ".yith-wcbsl-badge-content{
                background: {$badge_bg} !important;
                color: {$badge_text} !important;
            }";


            $css
                .= ".yith-wcbsl-bestseller-positioning-in-product-wrapper a {
                background: {$link_bg} !important;
                color: {$link_text} !important;
            }";
            $css
                .= ".yith-wcbsl-bestseller-positioning-in-product-wrapper a:hover {
                background: {$link_bg_light} !important;
                color: {$link_text} !important;
            }";
            $css
                .= ".yith-wcbsl-bestseller-positioning-in-product-wrapper a:focus {
                background: {$link_bg_dark} !important;
                color: {$link_text} !important;
            }";

            return $css;
        }
    }
}
/**
 * Unique access to instance of YITH_WCBSL_Frontend_Premium class
 *
 * @deprecated since 1.1.0 use YITH_WCBSL_Frontend() instead
 * @return \YITH_WCBSL_Frontend_Premium
 * @since      1.0.0
 */
function YITH_WCBSL_Frontend_Premium() {
    return YITH_WCBSL_Frontend();
}