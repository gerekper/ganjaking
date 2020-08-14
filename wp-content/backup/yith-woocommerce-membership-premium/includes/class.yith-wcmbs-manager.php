<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Manager
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Manager {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Manager
     * @since 1.0.0
     */
    protected static $_instance;

    public $post_types = array( 'post', 'product', 'page' );

    public $restricted_items_transient_name = 'yith-wcmbs-restr-items';
    public $restricted_users_transient_name = 'yith-wcmbs-restr-users';

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Manager
     * @since 1.0.0
     */
    public static function get_instance() {
        $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

        return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    protected function __construct() {

    }


    /**
     * Get the not allowed posts for a user
     *
     * @param int $user_id the user id
     *
     * @access public
     * @since  1.0.0
     * @return array
     */
    public function get_non_allowed_post_ids_for_user( $user_id ) {
        // FULL ACCESS TO ADMIN
        if ( user_can( $user_id, 'create_users' ) )
            return array();

        $member = YITH_WCMBS_Members()->get_member( $user_id );

        $restricted_items = $this->get_restricted_items();

        if ( !$member->is_member() ) {
            return $restricted_items;
        }

        return array();
    }

    /**
     * Get the ids of post that have restricted access
     *
     * @access public
     * @since  1.0.0
     */
    public function get_restricted_items() {
        $posts = array();

        foreach ( $this->post_types as $post_type ) {
            $args       = array(
                'post_type'                  => $post_type,
                'posts_per_page'             => -1,
                'post_status'                => 'publish',
                'yith_wcmbs_suppress_filter' => true,
                'meta_key'                   => '_yith_wcmbs_restrict_access',
                'meta_value'                 => 'all_members',
                'fields'                     => 'ids'
            );
            $this_posts = get_posts( $args );
            if ( $this_posts )
                $posts = array_merge( $posts, $this_posts );
        }

        return array_unique( $posts );
    }

    public function user_has_access_to_post( $user_id, $post_id ) {
        $not_allowed_for_this_user = $this->get_non_allowed_post_ids_for_user( $user_id );

        return !in_array( $post_id, $not_allowed_for_this_user );
    }

    /**
     * delete the transient for Restricted Items
     */
    public function delete_transients() {
        delete_transient( $this->restricted_items_transient_name );
        delete_transient( $this->restricted_users_transient_name );
    }
}

/**
 * Unique access to instance of YITH_WCMBS_Manager class
 *
 * @return YITH_WCMBS_Manager|YITH_WCMBS_Manager_Premium
 * @since 1.0.0
 */
function YITH_WCMBS_Manager() {
    return YITH_WCMBS_Manager::get_instance();
}