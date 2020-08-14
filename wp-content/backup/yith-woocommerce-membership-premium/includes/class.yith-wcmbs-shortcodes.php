<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Shortcodes Class
 *
 * @class   YITH_WCMBS_Shortcodes
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Shortcodes {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Shortcodes
     * @since 1.0.0
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Manager
     * @since 1.0.0
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct() {
        if ( is_admin() ) {
            add_filter( 'yith_wcmbs_settings_admin_tabs', array( $this, 'add_shortcodes_tab' ) );
            add_action( 'yith_wcmbs_render_admin_shortcodes_tab', array( $this, 'render_shortcodes_tab' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

            return;
        }

        /* Print WooCommerce Login form*/
        add_shortcode( 'loginform', array( $this, 'render_login_form' ) );

        /* Print link for protected media by ID of the media*/
        add_shortcode( 'protected_media', array( $this, 'render_protected_media_link' ) );

        /* Print membership protected links */
        add_shortcode( 'membership_protected_links', array( $this, 'render_protected_links' ) );

        /* Print content in base of membership */
        add_shortcode( 'membership_protected_content', array( $this, 'render_protected_content' ) );

        /* Print the list of items in a membership plan */
        add_shortcode( 'membership_items', array( $this, 'render_list_items_in_plan' ) );

        /* Print link for product download files */
        add_shortcode( 'membership_download_product_links', array( $this, 'render_membership_download_product_links' ) );

        /* Print membership history */
        add_shortcode( 'membership_history', array( $this, 'print_membership_history' ) );

        /* Print link for just-downloaded product files */
        add_shortcode( 'membership_downloaded_product_links', array( $this, 'render_membership_downloaded_product_links' ) );

    }

    /**
     * Add shortcode tab in admin tabs
     *
     * @param array $admin_tabs
     *
     * @return array
     */
    public function add_shortcodes_tab( $admin_tabs ) {
        $admin_tabs[ 'shortcodes' ] = __( 'Shortcodes', 'yith-woocommerce-membership' );

        return $admin_tabs;
    }

    /**
     * Render "Shortcodes" Tab
     */
    public function render_shortcodes_tab() {
        wc_get_template( '/tabs/shortcodes.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'yith-wcmbs-admin-shortcodes-tab', YITH_WCMBS_ASSETS_URL . '/css/shortcodes-tab.css' );
    }

    /**
     * Render Login Form
     *
     * EXAMPLE:
     * <code>
     *  [loginform]
     * </code>
     * this code displays the WooCommerce Login Form
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function render_login_form( $atts, $content = null ) {
        ob_start();
        if ( !is_user_logged_in() ) {
            echo '<div class="woocommerce">';
            wc_get_template( 'myaccount/form-login.php' );
            echo '</div>';
        }

        return ob_get_clean();
    }


    /**
     * Render Protected Media Link for downloading
     *
     * EXAMPLE:
     * <code>
     *  [protected_media id=237]Link Text[/protected_media]
     * </code>
     * this code displays a link for protected media download
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function render_protected_media_link( $atts, $content = null ) {
        if ( !empty( $atts[ 'id' ] ) && !empty( $content ) ) {
            $user_id = get_current_user_id();
            $post_id = $atts[ 'id' ];

            $manager = YITH_WCMBS_Manager();
            if ( $manager->user_has_access_to_post( $user_id, $post_id ) ) {

                $link = add_query_arg( array( 'protected_media' => $post_id ), home_url( '/' ) );

                $html = "<a href='{$link}'>";
                $html .= $content;
                $html .= "</a>";

                return $html;
            }
        }

        return '';
    }


    /**
     * Render Protected Links for downloading
     *
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function render_protected_links( $atts, $content = null ) {

        $default_atts = array(
            'post_id'    => 0,
            'link_class' => 'yith-wcmbs-download-button unlocked',
        );
        $atts         = wp_parse_args( $atts, $default_atts );
        $link_class   = $atts[ 'link_class' ];
        $post_id      = $atts[ 'post_id' ];

        if ( !$post_id ) {
            global $post;
            if ( !$post )
                return '';

            $post_id = $post->ID;
        }

        $protected_links = get_post_meta( $post_id, '_yith_wcmbs_protected_links', true );
        if ( $protected_links && is_array( $protected_links ) ) {
            $user_id = get_current_user_id();

            $html = '';

            $has_global_access = user_can( $user_id, 'create_users' );

            foreach ( $protected_links as $index => $protected_link ) {
                $name       = $protected_link[ 'name' ];
                $membership = $protected_link[ 'membership' ];
                $has_access = $has_global_access;

                if ( !$has_access ) {
                    if ( !!$membership && is_array( $membership ) ) {
                        $has_access = yith_wcmbs_user_has_membership( $user_id, $membership );
                    } else {
                        $has_access = yith_wcmbs_user_has_membership( $user_id );
                    }
                }

                if ( $has_access ) {
                    $link = add_query_arg( array( 'protected_link' => $index, 'of_post' => $post_id ), home_url( '/' ) );

                    $html .= "<a class='{$link_class}' href='{$link}'>";
                    $html .= $name;
                    $html .= "</a>";
                }
            }

            return $html;
        }

        return '';
    }


    /**
     * Render Protected Links for downloading
     *
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function render_protected_content( $atts, $content = null ) {
        if ( !$content ) {
            return '';
        }

        $default_atts = array(
            'plan_id' => 0,
            'user'    => 'member'
        );
        $atts         = wp_parse_args( $atts, $default_atts );
        $plan_id      = $atts[ 'plan_id' ];
        $user_type    = $atts[ 'user' ];

        switch ( $user_type ) {
            case 'guest':
                $has_access = !is_user_logged_in();
                break;
            case 'logged':
                $has_access = is_user_logged_in();
                break;
            default:
                $user_id    = get_current_user_id();
                $has_access = user_can( $user_id, 'create_users' );

                if ( !$has_access ) {
                    if ( !!$plan_id ) {
                        $ids        = explode( ',', $plan_id );
                        $has_access = yith_wcmbs_user_has_membership( $user_id, $ids );
                    } else {
                        $has_access = yith_wcmbs_user_has_membership( $user_id );
                    }
                }
                if ( 'non-member' === $user_type ) {
                    $has_access = !$has_access;
                }
        }

        if ( $has_access ) {
            return do_shortcode( $content );
        }

        return '';
    }

    /**
     * Render Product Link for downloading
     *
     * EXAMPLE:
     * <code>
     *  [membership_download_product_links link_class="btn btn-class"] Download [/membership_download_product_links]
     * </code>
     * this code displays a link for protected product download files
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param array  $atts the attributes of shortcode
     * @param string $content
     *
     * @return string
     */
    public function render_membership_download_product_links( $atts, $content = null ) {
        $r            = '';
        $default_atts = array(
            'link_class' => 'yith-wcmbs-download-button',
            'tooltip'    => 'no',
            'id'         => false,
        );

        $atts         = wp_parse_args( $atts, $default_atts );
        $link_class   = $atts[ 'link_class' ];
        $return       = !empty( $content ) ? 'links' : 'links_names';
        $id           = !empty( $atts[ 'id' ] ) ? $atts[ 'id' ] : false;
        $show_tooltip = $atts[ 'tooltip' ] == 'yes';
        $show_title   = $atts[ 'tooltip' ] == 'title';

        $links = YITH_WCMBS_Products_Manager()->get_download_links( array( 'return' => 'links_names', 'id' => $id ) );

        $credits             = yith_wcmbs_get_product_credits( $id );
        $locked_download_tip = sprintf( _n( 'Locked Download: unlock it using one credit!', 'Locked Download: unlock it using %s credits!', $credits, 'yith-woocommerce-membership' ), $credits );

        do_action( 'yith_wcmbs_before_links_list' );

        if ( !empty( $links ) ) {
            foreach ( $links as $link ) {
                switch ( $return ) {
                    case 'links':
                        $my_link         = $link[ 'link' ];
                        $my_name         = $link[ 'name' ];
                        $just_downloaded = $link[ 'unlocked' ];
                        $locked          = $just_downloaded ? 'unlocked' : 'locked';
                        $link_class      .= ' ' . $locked;
                        $link_class      .= $show_tooltip ? ' yith-wcmbs-tooltip' : '';
                        $data            = '';
                        if ( $locked == 'locked' ) {
                            $data .= 'data-locked="' . $locked_download_tip . '"';
                            $data .= 'data-credits-needed="' . $credits . '"';
                            if ( $show_title ) {
                                $my_name .= ' | ' . $locked_download_tip;
                            }
                        }

                        $my_name = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_name', $my_name, $link, $atts, $content );
                        $my_link = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_link', $my_link, $link, $atts, $content );
                        $data    = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_data', $data, $link, $atts, $content );

                        $r .= "<a class='{$link_class}' {$data} href='{$my_link}' title='{$my_name}'>{$content}</a>";
                        break;
                    case 'links_names':
                        $my_link         = $link[ 'link' ];
                        $my_name         = $link[ 'name' ];
                        $just_downloaded = $link[ 'unlocked' ];
                        $locked          = $just_downloaded ? 'unlocked' : 'locked';
                        $link_class      .= ' ' . $locked;
                        $link_class      .= $show_tooltip ? ' yith-wcmbs-tooltip' : '';

                        $data = '';
                        if ( $locked == 'locked' ) {
                            $data = 'title="' . $locked_download_tip . '"';
                            $data .= 'data-credits-needed="' . $credits . '"';
                            if ( $show_title ) {
                                $my_name .= ' | ' . $locked_download_tip;
                            }
                        }

                        $my_name = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_name', $my_name, $link, $atts, $content );
                        $my_link = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_link', $my_link, $link, $atts, $content );
                        $data    = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_data', $data, $link, $atts, $content );

                        $r .= "<a class='{$link_class}' {$data} href='{$my_link}'>{$my_name}</a>";
                        break;
                }
            }
        }

        return $r;
    }


    /**
     * Print the list of items in a membership plan
     *
     * EXAMPLE:
     * <code>
     *  [membership_items plan=237]
     * </code>
     * this code displays the list of items in the membership plan with ID = 237
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function render_list_items_in_plan( $atts, $content = null ) {
        if ( !empty( $atts[ 'plan' ] ) ) {
            $user_id          = get_current_user_id();
            $plan_id          = $atts[ 'plan' ];
            $is_default_style = isset( $atts[ 'style' ] ) && $atts[ 'style' ] == 'default';

            $member = YITH_WCMBS_Members()->get_member( $user_id );
            if ( current_user_can( 'edit_users' ) || ( !empty( $member ) && $member->has_active_plan( $plan_id ) ) ) {
                $allowed_in_plan = YITH_WCMBS_Manager()->get_allowed_posts_in_plan( $plan_id, true );
                $sorted_items    = get_post_meta( $plan_id, '_yith_wcmbs_plan_items', true );
                $sorted_items    = apply_filters( 'yith_wcmbs_sorted_plan_items', $sorted_items, $plan_id );
                $sorted_items    = !empty( $sorted_items ) ? $sorted_items : array();

                foreach ( $sorted_items as $key => $item ) {
                    if ( is_numeric( $item ) ) {
                        if ( !in_array( $item, $allowed_in_plan ) ) {
                            unset( $sorted_items[ $key ] );
                        }
                    }
                }

                $plan_list_styles = get_post_meta( $plan_id, '_yith_wcmbs_plan_list_styles', true );
                $show_icons       = isset( $plan_list_styles[ 'show_icons' ] ) ? ( $plan_list_styles[ 'show_icons' ] == 'yes' ) : true;

                if ( !empty( $allowed_in_plan ) ) {
                    foreach ( $allowed_in_plan as $item_id ) {
                        if ( !in_array( $item_id, $sorted_items ) )
                            $sorted_items[] = $item_id;
                    }
                }

                if ( isset( $atts[ 'orderby' ] ) ) {
                    $sorted_items = get_posts(
                        array(
                            'posts_per_page' => -1,
                            'post_type'      => YITH_WCMBS_Manager()->post_types,
                            'post__in'       => $sorted_items,
                            'fields'         => 'ids',
                            'orderby'        => $atts[ 'orderby' ],
                            'order'          => isset( $atts[ 'order' ] ) ? $atts[ 'order' ] : 'ASC',
                        )
                    );
                }

                $active_plan = $member->get_oldest_active_plan( $plan_id );

                $t_args = array(
                    'posts'       => $sorted_items,
                    'plan_id'     => $plan_id,
                    'plan'        => $active_plan,
                    'show_icons'  => $show_icons,
                    'active_plan' => $active_plan,

                );

                $template = $is_default_style ? '/frontend/my_account_plan_list_items.php' : '/frontend/plan_list_items.php';

                ob_start();
                wc_get_template( $template, $t_args, '', YITH_WCMBS_TEMPLATE_PATH );
                $html = ob_get_clean();

                return $html;
            }
        }

        return '';
    }

    /**
     * Print the list of items in a membership plan
     *
     * EXAMPLE:
     * <code>
     *  [membership_history]
     * </code>
     * this code displays the history for all user memberships
     *
     * EXAMPLE 2:
     * <code>
     *  [membership_history id="123" title="Title"]
     * </code>
     * this code displays the history user membership with id 123
     *
     * @access   public
     * @since    1.0.0
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     *
     * @param      $atts array the attributes of shortcode
     * @param null $content
     *
     * @return string
     */
    public function print_membership_history( $atts, $content = null ) {
        $user_plans = array();
        $title      = isset( $atts[ 'title' ] ) ? $atts[ 'title' ] : '';

        $no_membership_message = '';

        if ( empty( $atts[ 'id' ] ) ) {
            // ALL MEMBERSHIPS
            $user_id = isset( $atts[ 'user_id' ] ) ? $atts[ 'user_id' ] : get_current_user_id();

            $member                            = new YITH_WCMBS_Member_Premium( $user_id );
            $membership_plans_status           = apply_filters( 'yith_wcmbs_membership_history_shortcode_membership_plans_status', 'any', $atts );
            $membership_plans_args             = array( 'status' => $membership_plans_status );
            $membership_plans_args             = apply_filters( 'yith_wcmbs_membership_history_shortcode_membership_plans_args', $membership_plans_args, $member );
            $membership_plans_args[ 'return' ] = 'complete';
            $user_plans                        = $member->get_membership_plans( $membership_plans_args );

            // filter all user membership in base of type (only memberships, only subscriptions)
            $type = isset( $atts[ 'type' ] ) ? $atts[ 'type' ] : '';
            switch ( $type ) {
                case 'membership':
                    foreach ( $user_plans as $key => $membership ) {
                        if ( $membership->has_subscription() )
                            unset( $user_plans[ $key ] );
                    }
                    $no_membership_message = __( 'You don\'t have any membership without a subscription plan yet.', 'yith-woocommerce-membership' );
                    break;
                case 'subscription':
                    foreach ( $user_plans as $key => $membership ) {
                        if ( !$membership->has_subscription() )
                            unset( $user_plans[ $key ] );
                    }
                    $no_membership_message = __( 'You don\'t have any membership with a subscription plan yet.', 'yith-woocommerce-membership' );
                    break;
                default:
                    $no_membership_message = __( 'You don\'t have any membership yet.', 'yith-woocommerce-membership' );
                    break;
            }
        } else {
            $membership_id = $atts[ 'id' ];
            $membership    = new YITH_WCMBS_Membership( $membership_id );
            $user_plans    = ( $membership->is_valid() && $membership->user_id == get_current_user_id() ) ? array( $membership ) : array();

            if ( empty( $user_plans ) )
                return '';
        }

        $no_membership_message = apply_filters( 'yith_wcmbs_membership_history_shortcode_no_membership_message', $no_membership_message, $atts );

        $args = array(
            'user_plans'            => $user_plans,
            'title'                 => $title,
            'no_membership_message' => $no_membership_message,
        );
        ob_start();
        wc_get_template( '/frontend/my_account_membership_plans.php', $args, '', YITH_WCMBS_TEMPLATE_PATH );

        return ob_get_clean();
    }


    /**
     *
     */
    public function render_membership_downloaded_product_links() {
        ob_start();
        wc_get_template( '/frontend/downloaded-product-links.php', array(), '', YITH_WCMBS_TEMPLATE_PATH );

        return ob_get_clean();
    }

}

/**
 * Unique access to instance of YITH_WCMBS_Shortcodes class
 *
 * @return YITH_WCMBS_Shortcodes
 * @since 1.0.0
 */
function YITH_WCMBS_Shortcodes() {
    return YITH_WCMBS_Shortcodes::get_instance();
}