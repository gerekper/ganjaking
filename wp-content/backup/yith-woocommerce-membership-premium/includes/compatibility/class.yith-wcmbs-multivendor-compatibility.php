<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Multivendor Compatibility Class
 *
 * @class   YITH_WCMBS_Multivendor_Compatibility
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Multivendor_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Multivendor_Compatibility
     * @since 1.0.0
     */
    protected static $_instance;


    private $_options_to_override = array( 'yith_wpv_enable_product_amount', 'yith_wpv_vendors_product_limit' );


    /**
     * @var string The vendor taxonomy name
     */
    protected $_vendor_taxonomy_name = '';

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
    protected function __construct() {

        add_action( 'add_meta_boxes', array( $this, 'manage_metaboxes' ), 11 );

        add_filter( 'yith_wcmbs_create_membership', array( $this, 'set_create_membership_value' ), 10, 4 );

        if ( is_admin() ) {
            /* Remove Messages from admin menu for vendors */
            add_action( 'admin_menu', array( $this, 'remove_menu_pages' ) );
            add_action( 'admin_init', array( $this, 'disable_vendor_access_to_messages_page' ) );


            add_filter( 'yith_wcmbs_plan_tabs_metabox_settings', array( $this, 'multivendor_settings_in_plan' ) );
        }
        $this->override_options();

        if ( 'yes' !== get_option( 'yith_wpv_vendors_option_membership_management', 'no' ) )
            return;

        require_once( 'multivendor-utils/class.yith-wcmbs-members-list-table.php' );

        $this->_vendor_taxonomy_name = YITH_Vendors()->get_taxonomy_name();

        if ( is_admin() ) {

            add_filter( 'yith_wcmbs_plan_tabs_metabox_settings', array( $this, 'remove_options_from_plan' ) );
            add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'custom_types_for_yit_metabox_multivendor' ) );

            /* Vendor Membership Plans management */
            add_filter( 'request', array( $this, 'filter_plans_and_messages_list' ) );

            /* Edit Vendor Metabox in Plans */
            add_action( 'add_meta_boxes', array( $this, 'single_value_taxonomy' ) );

            /* Add Members for Vendors */
            add_action( 'admin_menu', array( $this, 'add_members_page_in_menu' ) );
        }

        /* Filter not_allowed, members_only, non_members_only post ids for Vendors */
        add_filter( 'yith_wcmbs_not_allowed_post_ids', array( $this, 'filter_not_allowed_ids' ), 10, 2 );
        add_filter( 'yith_wcmbs_filter_allowed_by_vendor_plans', array( $this, 'filter_allowed_by_vendor_plans' ), 10, 2 );

        /* Filter only multivendor plan ids for messages widget visibility */
        add_filter( 'yith_wcmbs_filter_only_multivendor_plan_ids', array( $this, 'filter_only_multivendor_plan_ids' ) );

        add_filter( 'yith_wcmbs_user_has_access_to_product', array( $this, 'vendor_is_owner_of_product' ), 10, 3 );

        add_filter( 'yith_wcmbs_add_products_in_plan_cat_tag_args', array( $this, 'filter_products_in_vendor_plan' ), 10, 3 );
        add_filter( 'yith_wcmbs_product_is_in_plans', array( $this, 'filter_product_plans_for_vendor_plan' ), 10, 3 );
    }

    /**
     * add Multi Vendor settings in each plan
     *
     * @since 1.3.10
     * @param $tabs
     * @return mixed
     */
    public function multivendor_settings_in_plan( $tabs ) {
        if ( current_user_can( 'manage_woocommerce' ) ) {
            $deps                   = array(
                'ids'    => '_mv-override-multi-vendor-settings',
                'values' => 'yes',
                'type'   => 'disable'
            );
            $tabs[ 'multi-vendor' ] = array(
                'label'  => __( 'Multi Vendor', 'yith-woocommerce-membership' ),
                'fields' => array(
                    'mv-override-multi-vendor-settings' => array(
                        'label' => __( 'Override Multi Vendor settings', 'yith-woocommerce-membership' ),
                        'type'  => 'onoff',
                        'std'   => 'no'
                    ),

                    'mv-vendors_product_amount_limit' => array(
                        'label' => __( 'Enable product amount limit', 'yith-woocommerce-membership' ),
                        'desc'  => __( 'Limit product amount for each vendor', 'yith-woocommerce-membership' ),
                        'type'  => 'checkbox',
                        'std'   => 'no',
                        'deps'  => $deps
                    ),

                    'mv-vendors_product_amount' => array(
                        'label'             => __( 'Product amount limit', 'yith-woocommerce-membership' ),
                        'type'              => 'number',
                        'std'               => 25,
                        'desc'              => __( 'Set a maximum number of products that each vendor can publish', 'yith-woocommerce-membership' ),
                        'custom_attributes' => "min='1' step='1'",
                        'deps'              => $deps

                    ),
                )
            );
        }

        return $tabs;
    }

    /**
     * override options
     *
     * @since 1.3.10
     */
    public function override_options() {
        foreach ( $this->_options_to_override as $option ) {
            add_filter( "pre_option_{$option}", array( $this, 'override_option_value' ), 10, 2 );
        }
    }

    /**
     * override the option value
     *
     * @param $value
     * @param $option
     * @return mixed
     * @since 1.3.10
     */
    public function override_option_value( $value, $option ) {
        switch ( $option ) {
            case 'yith_wpv_enable_product_amount':

                $member      = YITH_WCMBS_Members()->get_member( get_current_user_id() );
                $memberships = $member->get_membership_plans( array( 'return' => 'complete' ) );
                if ( $memberships ) {
                    foreach ( $memberships as $membership ) {
                        if ( yith_plugin_fw_is_true( get_post_meta( $membership->plan_id, '_mv-override-multi-vendor-settings', true ) ) ) {
                            $value = yith_plugin_fw_is_true( get_post_meta( $membership->plan_id, '_mv-vendors_product_amount_limit', true ) ) ? 'yes' : 'no';
                            if ( 'no' === $value ) {
                                break;
                            }
                        }
                    }
                }
                break;
            case 'yith_wpv_vendors_product_limit':
                $member      = YITH_WCMBS_Members()->get_member( get_current_user_id() );
                $memberships = $member->get_membership_plans( array( 'return' => 'complete' ) );
                if ( $memberships ) {
                    $values = array();
                    foreach ( $memberships as $membership ) {
                        if ( yith_plugin_fw_is_true( get_post_meta( $membership->plan_id, '_mv-override-multi-vendor-settings', true ) ) ) {
                            if ( yith_plugin_fw_is_true( get_post_meta( $membership->plan_id, '_mv-vendors_product_amount_limit', true ) ) ) {
                                $values[] = absint( get_post_meta( $membership->plan_id, '_mv-vendors_product_amount', true ) );
                            }
                        }
                    }
                    if ( $values ) {
                        $value = max( $values );
                    }
                }
                break;

        }
        return $value;
    }

    public function filter_product_plans_for_vendor_plan( $plan_ids, $product_id ) {
        if ( $plan_ids ) {
            $product_vendor    = yith_get_vendor( $product_id, 'product' );
            $product_vendor_id = $product_vendor->is_valid() && $product_vendor->has_limited_access( $product_vendor->get_owner() ) ? $product_vendor->id : false;
            foreach ( $plan_ids as $key => $plan_id ) {
                $plan_vendor = yith_get_vendor( $plan_id, 'product' );
                if ( $plan_vendor->is_valid() && $plan_vendor->has_limited_access( $plan_vendor->get_owner() ) ) {
                    if ( !$product_vendor_id || $plan_vendor->id !== $product_vendor_id ) {
                        unset( $plan_ids[ $key ] );
                    }

                }
            }
        }

        return $plan_ids;
    }

    public function filter_products_in_vendor_plan( $cat_tag_args, $plan_id ) {
        $vendor = yith_get_vendor( $plan_id, 'product' );
        if ( $vendor->is_valid() && $vendor->has_limited_access( $vendor->get_owner() ) ) {
            $tax_query = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => YITH_Vendors()->get_taxonomy_name(),
                    'field'    => 'id',
                    'terms'    => $vendor->id,
                    'operator' => 'IN'
                ),
            );

            $cat_tag_args[ 'tax_query' ] = array_merge( $tax_query, array( $cat_tag_args[ 'tax_query' ] ) );
        }

        return $cat_tag_args;
    }

    /**
     * @param bool     $create_membership
     * @param int      $id               the product id
     * @param WC_Order $order            the order
     * @param array    $plan_product_ids the plan product ids
     * @return bool
     */
    public function set_create_membership_value( $create_membership, $id, $order, $plan_product_ids ) {
        $product           = wc_get_product( $id );
        $vendor            = yith_get_vendor( $product, 'product' );
        $is_vendor_product = $vendor->is_valid();
        $order_id          = $order instanceof WC_Data ? $order->get_id() : $order->id;
        $is_parent_order   = !empty( YITH_Vendors()->orders ) && !!YITH_Vendors()->orders->get_suborder( $order_id );

        if ( $is_parent_order ) {
            return !$is_vendor_product;
        }

        return $create_membership;
    }

    public function manage_metaboxes() {
        if ( 'yes' != get_option( 'yith_wpv_vendors_option_membership_management', 'no' ) ) {
            $vendor = yith_get_vendor( 'current', 'user' );

            if ( $vendor->is_valid() && $vendor->has_limited_access() && !current_user_can( 'manage_users' ) ) {
                remove_meta_box( 'yith-wcmbs-restrict-access-metabox', null, 'side' );
                remove_meta_box( 'yith-wcmbs-alternative-content-metabox', null, 'normal' );
            }
        }
    }

    public function remove_options_from_plan( $tab ) {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( $vendor->is_valid() && $vendor->has_limited_access() && !current_user_can( 'manage_users' ) ) {
            unset( $tab[ 'items-in-plan' ][ 'fields' ][ 'post-cats' ] );
            unset( $tab[ 'items-in-plan' ][ 'fields' ][ 'post-tags' ] );
            unset( $tab[ 'items-in-plan' ][ 'fields' ][ 'add-posts' ] );
            unset( $tab[ 'items-in-plan' ][ 'fields' ][ 'add-pages' ] );

            $tab[ 'items-in-plan' ][ 'fields' ][ 'add-products' ][ 'type' ] = 'flmv-products';
            $tab[ 'settings' ][ 'fields' ][ 'linked-plans' ][ 'type' ]      = 'flmv-membership-plans';
        }

        return $tab;
    }

    /*
         * parse custom types for YIT Metabox
         *
         * @param array $args
         *
         * @return array
         * @since 1.0.0
         */
    public function custom_types_for_yit_metabox_multivendor( $args ) {
        if ( isset( $args[ 'type' ] ) ) {
            $items_array = array();
            switch ( $args[ 'type' ] ) {
                case 'flmv-products':
                    $vendor    = yith_get_vendor( 'current', 'user' );
                    $tax_query = array();
                    if ( $vendor->is_valid() && $vendor->has_limited_access() && !current_user_can( 'manage_users' ) ) {
                        $tax_query = array(
                            array(
                                'taxonomy' => YITH_Vendors()->get_taxonomy_name(),
                                'field'    => 'id',
                                'terms'    => $vendor->id,
                                'operator' => 'IN'
                            )
                        );
                    }
                    $items = get_posts( array( 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish', 'tax_query' => $tax_query ) );
                    if ( !empty( $items ) ) {
                        foreach ( $items as $item ) {
                            $title              = $item->post_title;
                            $id                 = $item->ID;
                            $items_array[ $id ] = $title;
                        }
                    }
                    $args[ 'args' ][ 'args' ][ 'value' ] = array();
                    break;
                case 'flmv-membership-plans':
                    $items_array = array();
                    $plans       = YITH_WCMBS_Manager()->get_plans();
                    if ( !empty( $plans ) ) {
                        foreach ( $plans as $p ) {
                            $id                 = $p->ID;
                            $name               = $p->post_title;
                            $items_array[ $id ] = $name;
                        }
                    }
                    break;
                default:
                    return $args;
            }
            $args[ 'type' ]              = 'chosen';
            $args[ 'args' ][ 'options' ] = $items_array;
        }

        return $args;
    }

    /**
     * return true if vendor is owner of the product
     *
     * @param bool $return
     * @param int  $user_id
     * @param int  $product_id
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0.0
     * @return array
     */
    public function vendor_is_owner_of_product( $return, $user_id, $product_id ) {
        $vendor = yith_get_vendor( $user_id, 'user' );
        if ( $vendor->is_valid() ) {
            $products = $vendor->get_products();
            if ( !empty( $products ) && in_array( $product_id, $products ) ) {
                return true;
            } else {
                return false;
            }
        }

        return $return;
    }

    /**
     * Add Members page in Admin menu for Vendors
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0.0
     * @return void
     */
    public function add_members_page_in_menu() {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
            add_submenu_page( 'edit.php?post_type=yith-wcmbs-plan',     //parent_slug
                              __( 'Members', 'yith-woocommerce-membership' ),                          //page_title
                              __( 'Members', 'yith-woocommerce-membership' ),                          //menu_title
                              'edit_plans',                                           // capability
                              'yith-wcmbs-vendor-members',                            // menu_slug
                              array( $this, 'render_vendor_members' )                 // callback function
            );
        }
    }

    public function render_vendor_members() {
        echo '<div class="wrap">';
        echo '<h2>' . __( 'Members', 'yith-woocommerce-membership' ) . '</h2>';

        $table = new YITH_WCMBS_Members_List_Table();
        $table->prepare_items();
        $table->display();

        echo '</div>';
    }


    /**
     * Remove the WooCommerce taxonomy Metabox and add a new Metabox for single taxonomy management in Membership Plans
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0.0
     * @return void
     */
    public function single_value_taxonomy() {

        $id              = 'tagsdiv-' . $this->_vendor_taxonomy_name;
        $taxonomy        = get_taxonomies( array( 'show_ui' => true ), 'object' );
        $product_vendors = $taxonomy[ $this->_vendor_taxonomy_name ];
        $page            = 'yith-wcmbs-plan';
        $context         = 'side';
        $callback        = array( YITH_Vendors()->admin, 'single_taxonomy_meta_box' );
        $callback_args   = array( 'taxonomy' => $this->_vendor_taxonomy_name );
        $priority        = 'default';

        remove_meta_box( $id, $page, $context );
        add_meta_box( $id, $product_vendors->labels->name, $callback, $page, $context, $priority, $callback_args );
    }


    /**
     * Disable access to messages page for Vendors
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0.0
     * @return void
     */
    public function disable_vendor_access_to_messages_page() {
        global $typenow;
        if ( $typenow == 'yith-wcmbs-thread' ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                wp_die( __( 'Are you trying to cheat, uh?' ), 403 );
            }
        }
    }


    /**
     * Remove Messages from admin menu for vendors
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @return void
     * @since  1.0.0
     */
    public function remove_menu_pages() {
        $vendor = yith_get_vendor( 'current', 'user' );

        if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
            remove_submenu_page( 'edit.php?post_type=yith-wcmbs-plan', 'edit.php?post_type=yith-wcmbs-thread' );
            remove_submenu_page( 'edit.php?post_type=yith-wcmbs-plan', 'edit.php?post_type=ywcmbs-membership' );
        }
    }

    /**
     * Filter passed plan ids and return only plans created by MultiVendor Admin
     *
     * @param  array $plans_ids array of plan ids
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @return array
     */
    public function filter_only_multivendor_plan_ids( $plans_ids ) {
        $multivendor_plans_ids = array();

        if ( !empty( $plans_ids ) ) {
            foreach ( $plans_ids as $plan_id ) {
                $post_vendor_term = wp_get_post_terms( $plan_id, $this->_vendor_taxonomy_name, array( "fields" => "ids" ) );
                if ( !$post_vendor_term ) {
                    $multivendor_plans_ids[] = $plan_id;
                }
            }
        }

        return $multivendor_plans_ids;
    }

    /**
     * Filter not allowed ids for vendors
     * Allow vendors to see their products in frontend
     *
     * @param  array $not_allowed array of not allowed post ids
     * @param  int   $user_id     the id of the vendor
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @return array
     */
    public function filter_not_allowed_ids( $not_allowed, $user_id ) {
        $vendor = yith_get_vendor( $user_id, 'user' );

        if ( $vendor->is_valid() ) {
            $vendor_product_ids = $vendor->get_products( array( 'yith_wcmbs_suppress_filter' => true ) );
            $not_allowed        = array_diff( $not_allowed, $vendor_product_ids );
        }

        return $not_allowed;
    }

    /**
     * Filter members only ids for vendors' members
     *
     * @param  array $allowed_post_ids      array of members only post ids
     * @param  array $user_membership_plans array of membership plans ids
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @return array
     */
    public function filter_allowed_by_vendor_plans( $allowed_post_ids, $user_membership_plans ) {
        $allowed_post_ids_for_vendor = array();
        $new_allowed_post_ids        = array();

        if ( !empty( $user_membership_plans ) && !empty( $allowed_post_ids ) ) {

            foreach ( $allowed_post_ids as $post_id ) {
                $post_vendor_term = wp_get_post_terms( $post_id, $this->_vendor_taxonomy_name, array( "fields" => "ids" ) );
                if ( !empty( $post_vendor_term ) ) {
                    foreach ( $post_vendor_term as $term_id ) {
                        $allowed_post_ids_for_vendor[ $term_id ][] = $post_id;
                    }
                } else {
                    $allowed_post_ids_for_vendor[ 0 ][] = $post_id;
                }
            }

            foreach ( $user_membership_plans as $membership_id ) {
                $plan_vendor_term = wp_get_post_terms( $membership_id, $this->_vendor_taxonomy_name, array( "fields" => "ids" ) );
                if ( !empty( $plan_vendor_term ) ) {
                    foreach ( $plan_vendor_term as $term_id ) {
                        if ( isset( $allowed_post_ids_for_vendor[ $term_id ] ) ) {
                            $new_allowed_post_ids = array_merge( $new_allowed_post_ids, $allowed_post_ids_for_vendor[ $term_id ] );
                        }
                    }
                } else {
                    if ( isset( $allowed_post_ids_for_vendor[ 0 ] ) ) {
                        $new_allowed_post_ids = array_merge( $new_allowed_post_ids, $allowed_post_ids_for_vendor[ 0 ] );
                    }
                }
            }
        }

        return array_unique( $new_allowed_post_ids );
    }

    /**
     * Only show vendor's plans
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @param  arr $request Current request
     * @return arr          Modified request
     * @since  1.0
     */
    public function filter_plans_and_messages_list( $request ) {
        global $typenow;

        $vendor = yith_get_vendor( 'current', 'user' );

        if ( is_admin() && !$vendor->is_super_user() && $vendor->is_user_admin() && in_array( $typenow, array( 'yith-wcmbs-plan', 'yith-wcmbs-thread' ) ) ) {
            $request[ $vendor->term->taxonomy ] = $vendor->slug;

            return apply_filters( "yith_wcmv_{$typenow}_request", $request );
        }

        return $request;
    }

}

/**
 * Unique access to instance of YITH_WCMBS_Multivendor_Compatibility class
 *
 * @return YITH_WCMBS_Multivendor_Compatibility
 * @since 1.0.0
 */
function YITH_WCMBS_Multivendor_Compatibility() {
    return YITH_WCMBS_Multivendor_Compatibility::get_instance();
}