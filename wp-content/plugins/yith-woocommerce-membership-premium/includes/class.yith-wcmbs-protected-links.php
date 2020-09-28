<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Protected_Links
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Protected_Links {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Protected_Links
     * @since 1.0.0
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Protected_Links
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
    private function __construct() {

        if ( is_admin() ) {
            add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
            add_action( 'save_post', array( $this, 'save_links' ) );
        }
        if ( isset( $_REQUEST[ 'protected_link' ] ) ) {
            add_action( 'get_header', array( $this, 'download' ), 999 );
        }

    }

    public function add_metabox() {
        $protected_link_post_types = apply_filters( 'yith_wcmbs_protected_link_post_types', array( 'post', 'page', 'product' ) );

        add_meta_box( 'yith-wcmbs-protected-links',
                      __( 'Membership Protected Links', 'yith-woocommerce-membership' ),
                      array( $this, 'show_protected_links_metabox' ),
                      $protected_link_post_types,
                      'normal',
                      'default' );
    }

    public function show_protected_links_metabox() {
        include YITH_WCMBS_TEMPLATE_PATH . '/metaboxes/protected-links.php';
    }

    public function save_links( $post_id ) {
        if ( isset( $_REQUEST[ '_yith_wcmbs_protected_links' ] ) ) {
            $protected_links = $_REQUEST[ '_yith_wcmbs_protected_links' ];

            $protected_links_to_save = array();

            if ( !!$protected_links && is_array( $protected_links ) ) {
                foreach ( $protected_links as $key => $value ) {
                    $name       = isset( $value[ 'name' ] ) ? $value[ 'name' ] : '';
                    $link       = isset( $value[ 'link' ] ) ? $value[ 'link' ] : '';
                    $membership = isset( $value[ 'membership' ] ) ? $value[ 'membership' ] : array();

                    if ( !!$link ) {
                        $protected_links_to_save[] = array(
                            'name'       => $name,
                            'link'       => $link,
                            'membership' => $membership,
                        );
                    }
                }
            }
            update_post_meta( $post_id, '_yith_wcmbs_protected_links', $protected_links_to_save );
        }
    }

    /**
     * Check if user has access to media. If user have access forces the file download
     *
     * @since 1.0.0
     */
    public function download() {
        if ( !isset( $_REQUEST[ 'protected_link' ] ) || empty( $_REQUEST[ 'of_post' ] ) )
            return;

        $protected_id = $_REQUEST[ 'protected_link' ];
        $post_id      = $_REQUEST[ 'of_post' ];
        $user_id      = get_current_user_id();

        $protected_links = get_post_meta( absint( $post_id ), '_yith_wcmbs_protected_links', true );

        if ( !!$protected_links && is_array( $protected_links ) && isset( $protected_links[ $protected_id ] ) ) {
            $the_protected_link = $protected_links[ $protected_id ];
            $has_access         = user_can( $user_id, 'create_users' );
            $membership         = $the_protected_link[ 'membership' ];


            if ( !$has_access ) {
                if ( !!$membership && is_array( $membership ) ) {
                    $has_access = yith_wcmbs_user_has_membership( $user_id, $membership );
                } else {
                    $has_access = yith_wcmbs_user_has_membership( $user_id );
                }
            }

            if ( $has_access ) {
                $file_path = $the_protected_link[ 'link' ];
                $filename  = basename( $file_path );
                do_action( 'woocommerce_download_file_force', $file_path, $filename );
            } else {
                wp_die( __( 'You can\'t access to this content.', 'yith-woocommerce-membership' ), __( 'Restricted Access.', 'yith-woocommerce-membership' ) );
            }

        }
    }
}