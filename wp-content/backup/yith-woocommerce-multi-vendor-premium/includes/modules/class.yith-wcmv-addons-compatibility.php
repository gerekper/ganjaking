<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Multivendor Compatibility Class
 *
 * @class   YITH_WCMV_Addons_Compatibility
 * @package Yithemes
 * @since   1.7.4
 * @author  Yithemes
 *
 */
class YITH_WCMV_Addons_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMV_Addons_Compatibility
     */
    protected static $instance;

    /**
     * @var string The vendor taxonomy name
     */
    protected $_vendor_taxonomy_name = '';

    public $plugin_with_post_types    = array();
    public $vendor_allowed_post_types = array();
    public $plugin_with_capabilities  = array();

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMV_Addons_Compatibility
     * @since 1.0.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct() {
        $plugins                     = YITH_Vendors()->addons->plugins;
        $this->_vendor_taxonomy_name = YITH_Vendors()->get_taxonomy_name();

        foreach ( $plugins as $plugin_name => $options ) {
            /*if ( !$this->is_enabled_management_for_vendors( $plugin_name ) )
                continue;*/

            if ( isset( $options[ 'post_types' ] ) ) {
                $this->plugin_with_post_types[ $plugin_name ] = $options;
                $this->vendor_allowed_post_types              = array_merge( $this->vendor_allowed_post_types, (array) $options[ 'post_types' ] );
            }

            if ( isset( $options[ 'capabilities' ] ) ) {
                $this->plugin_with_capabilities[ $plugin_name ] = $options;
            }

        }

        /* Add/Remove capabilities to vendors */
        add_filter( 'yith_wcmv_premium_caps', array( $this, 'add_addons_caps_to_premium_caps' ) );

        $this->add_post_type_management_to_vendors();
    }

    /**
     * filter Multivendor premium caps and add the add-ons caps
     *
     * @param $premium_caps
     *
     * @return array
     */
    public function add_addons_caps_to_premium_caps( $premium_caps ) {
        $addons_caps = array();

        foreach ( $this->plugin_with_capabilities as $plugin_name => $plugin_options ) {
            $slug                 = $this->get_slug( $plugin_name );
            $plugin_caps          = (array) $plugin_options[ 'capabilities' ];
            $addons_caps[ $slug ] = $plugin_caps;
        }

        return array_merge( $premium_caps, $addons_caps );
    }

    /**
     * Add Allowed Post Types for Vendors
     *
     * @param array $allowed_post_types the allowed post types for Vendors; default are 'product' and 'shop_coupon'
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0.0
     * @return array
     */
    public function add_allowed_post_types_for_vendors( $allowed_post_types ) {

        return array_unique( array_merge( $allowed_post_types, $this->vendor_allowed_post_types ) );
    }

    /**
     * Add Menus to Admin Menu of Vendors
     *
     * @param array $menu_items array of menu items allowed for Vendors
     *
     * @return array
     *
     * @access public
     * @since  1.0.0
     */
    public function add_menus_to_vendors_admin_menu( $menu_items ) {
        $addons_menus = array();

        foreach ( $this->vendor_allowed_post_types as $post_type ) {
            $addons_menus = array_merge( $addons_menus, array( 'edit.php?post_type=' . $post_type ) );
        }

        return array_unique( array_merge( $menu_items, $addons_menus ) );
    }

    /**
     * Add post type management to Verdors for Add-on Plugins
     */
    public function add_post_type_management_to_vendors() {
        if ( is_admin() ) {
            /* add post types to vendor's allowed post types */
            add_filter( 'yith_wpv_vendors_allowed_post_types', array( $this, 'add_allowed_post_types_for_vendors' ) );

            /* Add Post types in Vendors Admin */
            add_filter( 'yith_wpv_vendor_menu_items', array( $this, 'add_menus_to_vendors_admin_menu' ) );

            /* Add Vendor taxonomy to post types */
            add_filter( 'yith_wcmv_register_taxonomy_object_type', array( $this, 'add_taxonomy_object_types' ) );

            /* Filter Vendors Post types */
            add_action( 'pre_get_posts', array( $this, 'filter_vendor_post_types' ) );
            add_filter( 'wp_count_posts', array( $this, 'vendor_count_posts' ), 10, 3 );
            add_action( 'save_post', array( $this, 'add_vendor_taxonomy_to_post_types' ), 10, 2 );

            /* Disable manage other vendors posts */
            add_action( 'current_screen', array( $this, 'disabled_manage_other_vendors_posts' ) );
        }
    }

    /**
     * Add Vendor taxonomy to post types
     *
     * @param  array $types array of object types associated to vendor taxonomy. Default: 'product'
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @return array
     */
    public function add_taxonomy_object_types( $types ) {
        $types = array_merge( $types, $this->vendor_allowed_post_types );

        return array_unique( $types );
    }

    /**
     * Add vendor taxonomy to post types
     *
     * @param       int $post_id Product ID
     *
     * @author      Andrea Grillo <andrea.grillo@yithemes.com>
     * @return      void
     * @since       1.0
     * @use         save_post action
     */
    public function add_vendor_taxonomy_to_post_types( $post_id, $post ) {
        $vendor = yith_get_vendor( 'current', 'user' );

        if ( $vendor->is_valid() && in_array( $post->post_type, $this->vendor_allowed_post_types ) && current_user_can( 'edit_post', $post_id ) && $vendor->has_limited_access() ) {
            wp_set_object_terms( $post_id, $vendor->term->slug, $vendor->term->taxonomy, false );
        }
    }

    /**
     * Filter Vendors post types
     *
     * @param WP_Query $query object The query object
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.0.0
     */
    public function filter_vendor_post_types( $query ) {
        if ( empty( $query->query[ 'yith_wcmv_addons_suppress_filter' ] ) &&
             isset( $query->query[ 'post_type' ] ) &&
             !empty( $this->vendor_allowed_post_types ) &&
             in_array( $query->query[ 'post_type' ], $this->vendor_allowed_post_types ) &&
             !current_user_can( 'edit_users' ) &&
             ( $vendor = yith_get_vendor( 'current', 'user' ) ) &&
             $vendor->is_valid() &&
             $vendor->has_limited_access()
        ) {
            $tax_query = $this->get_vendor_query_posts_args( $vendor );
            $query->set( 'tax_query', $tax_query );
        }
    }

    /**
     * get the slug from plugin name
     *
     * @param $plugin_name
     *
     * @return mixed
     */
    public function get_slug( $plugin_name ) {
        return str_replace( '-', '_', $plugin_name );
    }

    /**
     * Get query results of this vendor
     *
     * @param YITH_Vendor $vendor the vendor
     * @param string      $post_type the post type to get
     * @param array       $extra More arguments to append
     *
     * @return array
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function get_vendor_posts( $vendor, $post_type, $extra = array() ) {
        $args = wp_parse_args( $extra, array(
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'fields'         => 'ids'
        ) );

        $args = $this->get_vendor_query_posts_args( $vendor, $args );

        return get_posts( $args );
    }

    /**
     * Return the arguments to make a query for the posts of this vendor
     *
     * @param YITH_Vendor $vendor the vendor
     * @param array       $extra More arguments to append
     *
     * @return array
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function get_vendor_query_posts_args( $vendor, $extra = array() ) {
        return wp_parse_args( $extra, array(
            'tax_query' => array(
                array(
                    'taxonomy' => $vendor::$taxonomy,
                    'field'    => 'id',
                    'terms'    => $vendor->id
                )
            )
        ) );
    }

    /**
     * check if vendors can manage Plugin
     *
     * @return   bool
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0
     */
    public function is_enabled_management_for_vendors( $plugin_name ) {
        $slug = $this->get_slug( $plugin_name );
        if ( !YITH_Vendors()->addons->has_plugin( $plugin_name ) )
            return false;

        return 'yes' == get_option( 'yith_wpv_vendors_option_' . $slug . '_management', 'no' );
    }

    /**
     * Filter the post count for vendor
     *
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     *
     * @param $counts   The post count
     * @param $type     Post type
     * @param $perm     The read permission
     *
     * @return arr  Modified request
     * @since    1.0
     * @use      wp_post_count action
     */
    public function vendor_count_posts( $counts, $type, $perm ) {
        $vendor = yith_get_vendor( 'current', 'user' );

        if ( !$vendor || !in_array( $type, $this->vendor_allowed_post_types ) || $vendor->is_super_user() || !$vendor->is_user_admin() ) {
            return $counts;
        }

        /**
         * Get a list of post statuses.
         */
        $stati = get_post_stati();

        // Update count object
        foreach ( $stati as $status ) {
            $posts           = $this->get_vendor_posts( $vendor, $type, "post_status=$status" );
            $counts->$status = count( $posts );
        }

        return $counts;
    }

    /**
     * Restrict vendors from editing other vendors' posts
     *
     * @author      Leanza Francesco <leanzafrancesco@gmail.com>
     * @author      Andrea Grillo <andrea.grillo@yithemes.com>
     * @return      void
     * @since       1.11.4
     * @use         current_screen filter
     */
	public function disabled_manage_other_vendors_posts() {
		$vendor    = yith_get_vendor( 'current', 'user' );
		$is_seller = $vendor->is_valid() && $vendor->has_limited_access();

		if ( ! $is_seller || isset( $_POST['post_ID'] ) || ! isset( $_GET['post'] ) ) {
			return;
		}

		/* WPML Support */
		if ( $post = get_post( $_GET['post'] ) ) {
			$default_language = function_exists( 'wpml_get_default_language' ) ? wpml_get_default_language() : null;
			$post_id          = yit_wpml_object_id( $_GET['post'], $post->post_type, true, $default_language );
			$cpt_vendor       = yith_get_vendor( $post_id, 'product' ); // If false, the CPT hasn't any vendor set

			if ( apply_filters( 'yith_wcmv_vendor_disabled_manage_other_vendors_posts', true ) && ( in_array( $post->post_type, $this->vendor_allowed_post_types ) && false !== $cpt_vendor && $vendor->id != $cpt_vendor->id ) ) {
				wp_die( __( 'You do not have permission to edit this post', 'yith-woocommerce-product-vendors' ) );
			}
		}
	}
}