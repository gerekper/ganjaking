<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Frontend_Premium' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Frontend_Premium extends YITH_WCMBS_Frontend {

        /**
         * Single instance of the class
         *
         * @var YITH_WCMBS_Frontend_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        private $_woocommerce_product_actions                        = array();
        private $_woocommerce_product_actions_priority_uptodate      = false;
        private $_woocommerce_product_actions_removed                = false;
        private $_woocommerce_product_shop_actions                   = array();
        private $_woocommerce_product_shop_actions_priority_uptodate = false;
        private $_woocommerce_product_shop_actions_removed           = false;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {
            $this->_init_woocommerce_hooks();

            // add frontend css
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );


            $hide_contents_option = get_option( 'yith-wcmbs-hide-contents', 'all' );

            if ( $hide_contents_option == 'alternative_content' ) {
                add_filter( 'the_content', array( $this, 'filter_content_for_membership' ), 999 );
                add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'control_product_access_in_shop' ) );
                add_action( 'woocommerce_before_main_content', array( $this, 'control_product_access_in_product_page' ) );
            }

            if ( $hide_contents_option == 'redirect' ) {
                add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'control_product_access_in_shop' ) );
                add_action( 'get_header', array( $this, 'redirect_if_not_have_access' ) );
            }

            if ( $hide_contents_option != 'redirect' ) {
                // Filter Post, Pages and product
                add_action( 'pre_get_posts', array( $this, 'hide_not_allowed_posts' ) );
                add_filter( 'the_posts', array( $this, 'filter_posts' ) );
                add_filter( 'get_pages', array( $this, 'filter_posts' ) );

                // Filter nav menu
                add_filter( 'wp_nav_menu_objects', array( $this, 'filter_nav_menu_pages' ), 10, 2 );
                // Filter next and previous post link
                add_filter( 'get_next_post_where', array( $this, 'filter_adiacent_post_where' ), 10, 3 );
                add_filter( 'get_previous_post_where', array( $this, 'filter_adiacent_post_where' ), 10, 3 );

            }

            // SHOP PAGE RESTRICTIONS
            if ( $hide_contents_option === 'all' ) {
                // Hide Shop for non-members if shop has restricted access
                add_filter( 'template_include', array( $this, 'hide_shop_for_non_members' ), 99 );
            } else if ( $hide_contents_option === 'alternative_content' ) {
                add_action( 'woocommerce_before_main_content', array( $this, 'alternative_content_for_shop' ), 0 );
                add_action( 'woocommerce_after_main_content', array( $this, 'alternative_content_for_shop' ), 999 );
            }

            /* Validate product before add to cart */
            add_action( 'woocommerce_add_to_cart_validation', array( $this, 'validate_product_add_to_cart' ), 10, 2 );

            /* Print Membership History in My Account */
            add_action( 'woocommerce_after_my_account', array( $this, 'print_membership_history' ) );

            /* check for Checkout registration required */
            add_action( 'woocommerce_checkout_registration_required', array( $this, 'checkout_registration_required' ) );

            /* Messages in Frontend */
            YITH_WCMBS_Messages_Manager_Frontend();

            add_filter( 'body_class', array( $this, 'add_membership_class_to_body' ) );
        }

        /**
         * Add membership CSS classes to body
         *
         * @param array $classes
         * @return array
         * @since 1.3.17
         */
        public function add_membership_class_to_body( $classes ) {
            $member             = YITH_WCMBS_Members()->get_member( get_current_user_id() );
            $membership_classes = array();
            if ( $member->is_member() ) {
                $membership_classes[] = 'yith-wcmbs-member';
            }

            $plan_ids = $member->get_membership_plans();
            foreach ( $plan_ids as $plan_id ) {
                $membership_classes[] = "yith-wcmbs-member-{$plan_id}";
            }

            return array_merge( $classes, $membership_classes );
        }

        public function alternative_content_for_shop() {
            $shop_page_id                    = wc_get_page_id( 'shop' );
            if ( !YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), $shop_page_id ) ) {
                switch ( current_action() ) {
                    case 'woocommerce_before_main_content':
                        ob_start();
                        break;
                    case 'woocommerce_after_main_content':
                        $shop_content = ob_get_clean();
                        $alternative_content = yith_wcmbs_get_alternative_content( $shop_page_id );
                        woocommerce_output_content_wrapper();
                        if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
                            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
                        <?php endif;
                        echo yith_wcmbs_stylize_content( $alternative_content );
                        woocommerce_output_content_wrapper_end();
                        break;
                }
            }
        }

        /**
         * Hide Shop for non-members
         * if shop has restricted access
         * Note: hide the shop also if alternative_content is set!
         *
         * @param $template
         * @return string
         * @since 1.2.10
         */
        public function hide_shop_for_non_members( $template ) {
            if ( is_post_type_archive( 'product' ) &&
                 !YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), wc_get_page_id( 'shop' ) ) &&
                 $template_404 = get_404_template()
            ) {
                $template = $template_404;
            }

            return $template;
        }

        /**
         * check for Checkout registration required for membership products
         *
         * @param bool $required
         * @return bool
         */
        public function checkout_registration_required( $required ) {
            if ( get_option( 'yith-wcmbs-enable-guest-checkout', 'no' ) == 'yes' || $required || is_user_logged_in() )
                return $required;

            foreach ( WC()->cart->cart_contents as $key => $item ) {
                $prod_id = isset( $item[ 'product_id' ] ) ? $item[ 'product_id' ] : 0;
                if ( YITH_WCMBS_Manager()->get_plan_by_membership_product( $prod_id ) ) {
                    $required = true;
                    break;
                }
            }

            return $required;
        }


        /**
         * Unset posts with alternative content, if the option "hide-content" = 'alternative_content'
         *
         * @param array $post_ids
         * @access public
         * @return array
         * @since  1.0.0
         */
        public function unset_posts_with_alternative_content( $post_ids ) {
            $hide_contents_option = get_option( 'yith-wcmbs-hide-contents', 'all' );

            $new_post_ids = array();

            if ( !empty( $post_ids ) && $hide_contents_option == 'alternative_content' ) {
                foreach ( $post_ids as $id ) {
                    $alternative_content = yith_wcmbs_get_alternative_content( $id );
                    if ( empty( $alternative_content ) ) {
                        $new_post_ids[] = $id;
                    }
                }
            } else {
                return $post_ids;
            }

            return $new_post_ids;
        }


        /**
         * Filter Adiacent Posts (next and previous)
         *
         * @param string $where
         * @param bool   $in_same_term
         * @param array  $excluded_terms
         * @access public
         * @return string
         * @since  1.0.0
         */
        public function filter_adiacent_post_where( $where, $in_same_term, $excluded_terms ) {
            $current_user_id      = get_current_user_id();
            $non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );

            $non_allowed_post_ids = $this->unset_posts_with_alternative_content( $non_allowed_post_ids );

            if ( !empty( $non_allowed_post_ids ) )
                $where .= " AND p.ID NOT IN (" . implode( $non_allowed_post_ids, ',' ) . ')';

            return $where;
        }

        /**
         * Filter Nav Menu Pages
         *
         * @param $items array
         * @param $args  array
         * @access public
         * @return array
         * @since  1.0.0
         */
        public function filter_nav_menu_pages( $items, $args ) {
            $current_user_id      = get_current_user_id();
            $non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );

            $non_allowed_post_ids = $this->unset_posts_with_alternative_content( $non_allowed_post_ids );

            foreach ( $items as $key => $post ) {
                if ( is_object( $post ) && isset( $post->object_id ) && in_array( absint( $post->object_id ), $non_allowed_post_ids ) ) {
                    unset( $items[ $key ] );
                }
            }

            return $items;
        }

        /**
         * Filter pre get posts Query
         *
         * @param $query WP_Query
         * @access public
         * @since  1.0.0
         */
        public function hide_not_allowed_posts( $query ) {
            $suppress_filter = isset( $query->query[ 'yith_wcmbs_suppress_filter' ] ) ? $query->query[ 'yith_wcmbs_suppress_filter' ] : false;

            $restricted_post_types   = apply_filters( 'yith_wcmbs_restricted_post_types', YITH_WCMBS_Manager()->post_types );
            $is_restricted_post_type = isset( $query->query[ 'post_type' ] ) ? in_array( $query->query[ 'post_type' ], $restricted_post_types ) : true;

            if ( $is_restricted_post_type && !$suppress_filter ) {

                $current_user_id      = get_current_user_id();
                $non_allowed_post_ids = YITH_WCMBS_Manager()->get_non_allowed_post_ids_for_user( $current_user_id );

                $non_allowed_post_ids = $this->unset_posts_with_alternative_content( $non_allowed_post_ids );

                $query->set( 'post__not_in', (array) $non_allowed_post_ids );
            }
        }

        /**
         * Filter posts
         *
         * @param array $posts
         * @return array
         * @access public
         * @since  1.0.0
         */
        public function filter_posts( $posts ) {
            $current_user_id = get_current_user_id();

            $hide_contents_option = get_option( 'yith-wcmbs-hide-contents', 'all' );

            if ( is_array( $posts ) ) {
                foreach ( $posts as $post_key => $post ) {
                    if ( !YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID ) ) {
                        if ( $hide_contents_option == 'alternative_content' ) {
                            $alternative_content = yith_wcmbs_get_alternative_content( $post->ID );
                            if ( empty( $alternative_content ) ) {
                                unset( $posts[ $post_key ] );
                            }
                        } else {
                            unset( $posts[ $post_key ] );
                        }
                    }
                }
            }


            return $posts;
        }


        /**
         * If user doesn't have access to content, redirect to the link setted by admin
         *
         * @access public
         * @since  1.0.0
         */
        public function redirect_if_not_have_access() {
            global $post;
            $current_user_id = get_current_user_id();

            $user_has_no_access = ( is_single() || is_page() ) && !YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID );
            $user_has_no_access = $user_has_no_access || ( is_post_type_archive( 'product' ) && !YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), wc_get_page_id( 'shop' ) ) );

            if ( $user_has_no_access ) {
                $redirect_link = get_option( 'yith-wcmbs-redirect-link', '' );
                if ( !empty( $redirect_link ) ) {
                    if ( strpos( $redirect_link, 'http' ) != 0 )
                        $redirect_link = 'http://' . str_replace( 'http://', '', $redirect_link );
                }
                wp_redirect( $redirect_link );
            }
        }

        /**
         * Before add to cart a product check if user can buy it
         * If user cannot buy the product, show a Error message
         *
         * @param        $passed_validation
         * @param        $product_id
         * @return bool
         */
        public function validate_product_add_to_cart( $passed_validation, $product_id ) {
            if ( $passed_validation && !YITH_WCMBS_Manager()->user_has_access_to_post( get_current_user_id(), $product_id ) ) {
                $product       = wc_get_product( $product_id );
                $product_title = $product->get_title();

                $error_message     = sprintf( __( 'You cannot purchase "%s". To do it, you need a membership plan', 'yith-woocommerce-membership' ), $product_title );
                $error_message     = apply_filters( 'yith_wcmbs_validate_product_add_to_cart_needs_membership_error_message', $error_message, $product_title, $product_id );
                $passed_validation = false;
                wc_add_notice( $error_message, 'error' );
            }

            return $passed_validation;
        }


        /**
         * Control the allowed access for products in shop
         * If the user don't have access remove all WooCommerce actions that show contents in shop
         *
         * @access public
         * @since  1.0.0
         */
        public function control_product_access_in_shop() {
            global $post;
            $current_user_id = get_current_user_id();

            if ( YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID ) ) {
                $this->restore_woocommerce_product_shop_actions();
            } else {
                $this->remove_woocommerce_product_shop_actions();
            }
        }

        private function _init_woocommerce_hooks() {
            $this->_woocommerce_product_actions = apply_filters( 'yith_wcmbs_frontend_woocommerce_product_actions', array(
                'woocommerce_single_product_summary'       => array(
                    'woocommerce_template_single_rating'      => 10,
                    'woocommerce_template_single_price'       => 10,
                    'woocommerce_template_single_excerpt'     => 20,
                    'woocommerce_template_single_meta'        => 40,
                    'woocommerce_template_single_sharing'     => 50,
                    'woocommerce_template_single_add_to_cart' => 30,
                ),
                'woocommerce_after_single_product_summary' => array(
                    'woocommerce_output_product_data_tabs' => 10
                ),
                'woocommerce_simple_add_to_cart'           => array(
                    'woocommerce_simple_add_to_cart' => 30
                ),
                'woocommerce_grouped_add_to_cart'          => array(
                    'woocommerce_grouped_add_to_cart' => 30
                ),
                'woocommerce_variable_add_to_cart'         => array(
                    'woocommerce_variable_add_to_cart' => 30
                ),
                'woocommerce_external_add_to_cart'         => array(
                    'woocommerce_external_add_to_cart' => 30
                ),
                'woocommerce_single_variation'             => array(
                    'woocommerce_single_variation'                    => 10,
                    'woocommerce_single_variation_add_to_cart_button' => 20,
                ),
            ) );

            $this->_woocommerce_product_shop_actions = apply_filters( 'yith_wcmbs_frontend_woocommerce_product_shop_actions', array(
                'woocommerce_before_shop_loop_item_title'   => array(
                    'woocommerce_show_product_loop_sale_flash' => 10,
                ),
                'woocommerce_after_shop_loop_item_title'    => array(
                    'woocommerce_template_loop_price'  => 10,
                    'woocommerce_template_loop_rating' => 5,
                ),
                'woocommerce_before_single_product_summary' => array(
                    'woocommerce_show_product_sale_flash' => 10
                ),
                'woocommerce_after_shop_loop_item'          => array(
                    'woocommerce_template_loop_add_to_cart' => 10,
                )
            ) );
        }

        /**
         * Control the allowed access for products in single product page
         * If the user don't have access remove all WooCommerce actions that show contents in single product page
         *
         * @access public
         * @since  1.0.0
         */
        public function control_product_access_in_product_page() {
            global $post;
            $current_user_id = get_current_user_id();

            if ( is_single() ) {
                if ( YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post->ID ) ) {
                    $this->restore_woocommerce_product_actions();
                } else {
                    $this->remove_woocommerce_product_actions();
                }
            }
        }


        /**
         * Remove WooCommerce actions in Shop loop
         *
         * @access public
         * @since  1.0.0
         */
        public function remove_woocommerce_product_shop_actions() {
            $actions_to_remove = $this->_woocommerce_product_shop_actions;

            foreach ( $actions_to_remove as $hook => $functions ) {
                foreach ( $functions as $function => $default_priority ) {
                    if ( $priority = has_action( $hook, $function ) ) {
                        if ( !$this->_woocommerce_product_shop_actions_priority_uptodate ) {
                            $this->_woocommerce_product_shop_actions[ $hook ][ $function ] = $priority;
                        }
                        remove_action( $hook, $function, $priority );
                    } else {
                        if ( !$this->_woocommerce_product_shop_actions_priority_uptodate ) {
                            unset( $this->_woocommerce_product_shop_actions[ $hook ][ $function ] );
                        }
                    }
                }
            }

            $this->_woocommerce_product_shop_actions_priority_uptodate = true;

            do_action( 'yith_wcbms_remove_woocommerce_product_shop_actions' );

            $this->_woocommerce_product_shop_actions_removed = true;
        }

        /**
         * Restore WooCommerce actions in Shop loop
         *
         * @access public
         * @since  1.0.0
         */
        public function restore_woocommerce_product_shop_actions() {
            if ( !$this->_woocommerce_product_shop_actions_removed )
                return;

            $actions_to_restore = $this->_woocommerce_product_shop_actions;

            foreach ( $actions_to_restore as $hook => $functions ) {
                foreach ( $functions as $function => $priority ) {
                    if ( !has_action( $hook, $function ) ) {
                        add_action( $hook, $function, $priority );
                    }
                }
            }

            do_action( 'yith_wcbms_restore_woocommerce_product_shop_actions' );

            $this->_woocommerce_product_shop_actions_removed = false;
        }

        /**
         * Remove WooCommerce actions in Single Product Page
         * and add alternative content
         *
         * @access public
         * @since  1.0.0
         */
        public function remove_woocommerce_product_actions() {
            $actions_to_remove = $this->_woocommerce_product_actions;

            foreach ( $actions_to_remove as $hook => $functions ) {
                foreach ( $functions as $function => $default_priority ) {
                    if ( $priority = has_action( $hook, $function ) ) {
                        if ( !$this->_woocommerce_product_actions_priority_uptodate ) {
                            $this->_woocommerce_product_actions[ $hook ][ $function ] = $priority;
                        }
                        remove_action( $hook, $function, $priority );
                    } else {
                        if ( !$this->_woocommerce_product_actions_priority_uptodate ) {
                            unset( $this->_woocommerce_product_actions[ $hook ][ $function ] );
                        }
                    }
                }
            }
            $this->_woocommerce_product_actions_priority_uptodate = true;

            add_action( 'woocommerce_single_product_summary', array( $this, 'get_the_alternative_content' ) );

            do_action( 'yith_wcbms_remove_woocommerce_product_actions' );

            $this->_woocommerce_product_actions_removed = true;

        }

        /**
         * Restore WooCommerce actions in Single Product Page
         *
         * @access public
         * @since  1.0.0
         */
        public function restore_woocommerce_product_actions() {
            if ( !$this->_woocommerce_product_actions_removed )
                return;

            $actions_to_restore = $this->_woocommerce_product_actions;

            foreach ( $actions_to_restore as $hook => $functions ) {
                foreach ( $functions as $function => $priority ) {
                    if ( !has_action( $hook, $function ) ) {
                        add_action( $hook, $function, $priority );
                    }
                }
            }

            remove_action( 'woocommerce_single_product_summary', array( $this, 'get_the_alternative_content' ) );

            do_action( 'yith_wcbms_restore_woocommerce_product_actions' );

            $this->_woocommerce_product_actions_removed = false;
        }


        /**
         * Print the alternative content for products
         *
         * @access public
         * @since  1.0.0
         */
        public function get_the_alternative_content() {
            global $post;
            $alternative_content = yith_wcmbs_get_alternative_content( $post->ID );

            echo yith_wcmbs_stylize_content( $alternative_content );
        }

        /**
         * Filter the content in base of membership
         * if the user don't have access, show the alternative content
         *
         * @param string $content the content of post, page
         * @return string
         * @access public
         * @since  1.0.0
         */
        public function filter_content_for_membership( $content ) {
            $post_id         = get_the_ID();
            $current_user_id = get_current_user_id();

            if ( !$post_id || YITH_WCMBS_Manager()->user_has_access_to_post( $current_user_id, $post_id ) ) {
                return $content;
            }

            $alternative_content = yith_wcmbs_get_alternative_content( $post_id );
            return yith_wcmbs_stylize_content( $alternative_content );
        }

        /**
         * Print Membership History in MyAccount
         *
         * @access public
         * @since  1.0.0
         */
        public function print_membership_history() {
            $show = get_option( 'yith-wcmbs-show-history-in-my-account', 'yes' ) == 'yes';

            if ( $show ) {
                $title     = __( 'Membership Plans:', 'yith-woocommerce-membership' );
                $shortcode = '[membership_history title="' . $title . '"]';
                $shortcode = apply_filters( 'yith_wcmbs_membership_history_shortcode_in_my_account', $shortcode, $title );

                echo do_shortcode( $shortcode );
            }
        }

        public function enqueue_scripts() {
            parent::enqueue_scripts();
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            wp_enqueue_style( 'yith_wcmbs_frontend_opensans', "//fonts.googleapis.com/css?family=Open+Sans:100,200,300,400,600,700,800" );

            wp_enqueue_style( 'yith_wcmbs_membership_icons', YITH_WCMBS_ASSETS_URL . '/fonts/membership-icons/style.css' );

            wp_enqueue_style( 'dashicons' );

            $frontend_js_deps = apply_filters( 'yith_wcmbs_frontend_js_deps', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs', 'jquery-ui-tooltip' ) );
            wp_enqueue_script( 'yith_wcmbs_frontend_js', YITH_WCMBS_ASSETS_URL . '/js/frontend_premium' . $suffix . '.js', $frontend_js_deps, YITH_WCMBS_VERSION, true );
            wp_localize_script( 'yith_wcmbs_frontend_js', 'my_ajax_obj', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'user_id'  => get_current_user_id()
            ) );

            if ( apply_filters( 'yith_wcmbs_inline_style', true ) ) {
                wp_add_inline_style( 'yith-wcmbs-frontent-styles', $this->get_inline_css_for_plans() );
            }
        }


        /**
         * get the custom css for plans
         *
         * @access public
         * @since  1.0.0
         */
        public function get_inline_css_for_plans() {
            $plans = YITH_WCMBS_Manager()->plans;

            $css = '';
            if ( !empty ( $plans ) ) {
                foreach ( $plans as $plan ) {
                    $plan_list_styles = get_post_meta( $plan->ID, '_yith_wcmbs_plan_list_styles', true );
                    $plan_id          = $plan->ID;

                    /**
                     * @var string $list_style
                     * @var string $title_color
                     * @var string $title_background
                     * @var string $title_font_size
                     * @var string $title_margin_top
                     * @var string $title_margin_right
                     * @var string $title_margin_bottom
                     * @var string $title_margin_left
                     * @var string $title_padding_top
                     * @var string $title_padding_right
                     * @var string $title_padding_bottom
                     * @var string $title_padding_left
                     * @var string $item_background
                     * @var string $item_color
                     * @var string $item_font_size
                     * @var string $item_margin_top
                     * @var string $item_margin_right
                     * @var string $item_margin_bottom
                     * @var string $item_margin_left
                     * @var string $item_padding_top
                     * @var string $item_padding_right
                     * @var string $item_padding_bottom
                     * @var string $item_padding_left
                     * @var string $show_icons
                     */

                    $default_plan_list_styles = array(
                        'list_style'           => 'none',
                        'title_color'          => '#333333',
                        'title_background'     => 'transparent',
                        'title_font_size'      => '15',
                        'title_margin_top'     => '0',
                        'title_margin_right'   => '0',
                        'title_margin_bottom'  => '0',
                        'title_margin_left'    => '0',
                        'title_padding_top'    => '0',
                        'title_padding_right'  => '0',
                        'title_padding_bottom' => '0',
                        'title_padding_left'   => '0',
                        'item_background'      => 'transparent',
                        'item_color'           => '#333333',
                        'item_font_size'       => '15',
                        'item_margin_top'      => '0',
                        'item_margin_right'    => '0',
                        'item_margin_bottom'   => '0',
                        'item_margin_left'     => '20',
                        'item_padding_top'     => '0',
                        'item_padding_right'   => '0',
                        'item_padding_bottom'  => '0',
                        'item_padding_left'    => '0',
                        'show_icons'           => 'yes'
                    );

                    $plan_list_styles = wp_parse_args( $plan_list_styles, $default_plan_list_styles );

                    extract( $plan_list_styles );

                    $dark_item_color = wc_hex_lighter( $item_color );

                    $css
                        .= ".yith-wcmbs-plan-list-container-{$plan_id} ul.child{
                        list-style: $list_style;
                        margin-left: 0px;
                    }

                    .yith-wcmbs-plan-list-container-{$plan_id} ul.child li{
                        margin-top:     {$item_margin_top}px;
                        margin-right:   {$item_margin_right}px;
                        margin-bottom:  {$item_margin_bottom}px;
                        margin-left:    {$item_margin_left}px;
                        padding-top:    {$item_padding_top}px;
                        padding-right:  {$item_padding_right}px;
                        padding-bottom: {$item_padding_bottom}px;
                        padding-left:   {$item_padding_left}px;
                        background:     {$item_background};
                    }

                    .yith-wcmbs-plan-list-container-{$plan_id} p{
                        color:          $title_color;
                        font-size:      {$title_font_size}px ;
                        margin-top:     {$title_margin_top}px;
                        margin-right:   {$title_margin_right}px;
                        margin-bottom:  {$title_margin_bottom}px;
                        margin-left:    {$title_margin_left}px;
                        padding-top:    {$title_padding_top}px;
                        padding-right:  {$title_padding_right}px;
                        padding-bottom: {$title_padding_bottom}px;
                        padding-left:   {$title_padding_left}px;
                        background:     {$title_background};
                    }
                    .yith-wcmbs-plan-list-container-{$plan_id} a, .yith-wcmbs-plan-list-container-{$plan_id} li{
                        color: $item_color;
                        font-size: {$item_font_size}px;
                    }
                    .yith-wcmbs-plan-list-container-{$plan_id} a:hover{
                        color: $dark_item_color;
                    }
                    ";
                }
            }

            return $css;
        }
    }
}