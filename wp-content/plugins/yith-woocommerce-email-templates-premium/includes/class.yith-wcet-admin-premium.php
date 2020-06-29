<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCET_PREMIUM' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Email Templates
 *
 * @class   YITH_WCET_Admin_Premium
 * @package YITH WooCommerce Badge Management
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !class_exists( 'YITH_WCET_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCET_Admin_Premium extends YITH_WCET_Admin {
        /**
         * Single instance of the class
         *
         * @var YITH_WCET_Admin_Premium
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

            add_filter( 'yith_wcet_settings_admin_tabs', array( $this, 'settings_premium_tabs' ) );

            /* Action to Duplicate Email Templates post type */
            add_action( 'admin_action_duplicate_email_template', array( $this, 'admin_action_duplicate_email_template' ) );
            add_filter( 'post_row_actions', array( $this, 'add_duplicate_action_on_email_templates' ), 10, 2 );

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            add_action( 'wp_ajax_yith_wcet_email_preview', array( $this, 'ajax_preview_email' ) );
        }

        public function ajax_preview_email() {
            YITH_WCET_Email_Template_Helper()->preview_emails();
        }

        /**
         * Do actions duplicate_email_template
         *
         * @since       1.2.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function admin_action_duplicate_email_template() {
            if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] = 'duplicate_email_template' ) {
                if ( isset( $_REQUEST[ 'template_id' ] ) ) {
                    $id = absint( $_REQUEST[ 'template_id' ] );
                    $this->duplicate_email_template( $id );

                    $admin_edit_url = admin_url( 'edit.php?post_type=yith-wcet-etemplate' );
                    wp_redirect( $admin_edit_url );
                }
            }
        }

        /**
         * Add Duplicate action link in Email Template LIST
         *
         * @param array   $actions An array of row action links. Defaults are
         *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
         *                         'Delete Permanently', 'Preview', and 'View'.
         * @param WP_Post $post    The post object.
         *
         * @since       1.2.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function add_duplicate_action_on_email_templates( $actions, $post ) {
            if ( $post->post_type == 'yith-wcet-etemplate' && $post->post_status == 'publish' ) {
                $admin_edit_url                        = admin_url();
                $link                                  = add_query_arg( array(
                                                                            'action'      => 'duplicate_email_template',
                                                                            'template_id' => $post->ID
                                                                        ), $admin_edit_url );
                $action_name                           = __( 'Duplicate', 'yith-woocommerce-email-templates' );
                $actions[ 'duplicate_email_template' ] = "<a href='{$link}'>{$action_name}</a>";
            }

            return $actions;
        }

        /**
         * Duplicate Email Template
         *
         * @since       1.2.0
         *
         * @param       int $post_id the id of the membership plan
         *
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function duplicate_email_template( $post_id ) {
            $post = get_post( $post_id );

            if ( !$post || $post->post_type != 'yith-wcet-etemplate' )
                return;

            $new_post = array(
                'post_status' => $post->post_status,
                'post_type'   => 'yith-wcet-etemplate',
                'post_title'  => $post->post_title . ' - ' . __( 'Copy', 'yith-woocommerce-email-templates' )
            );

            $meta_to_save = array(
                '_template_meta',
            );

            $new_post_id = wp_insert_post( $new_post );

            foreach ( $meta_to_save as $key ) {
                $value = get_post_meta( $post_id, $key, true );
                update_post_meta( $new_post_id, $key, $value );
            }
        }


        /**
         * Add Email extra settings in woocommerce email settings
         *
         * @param array $settings
         *
         * @return array
         *
         * @access public
         * @since  1.0.0
         */
        public function email_extra_settings( $settings ) {
            $templates_array = array(
                'default' => __( 'Default', 'yith-woocommerce-email-templates' )
            );

            $args      = array(
                'posts_per_page' => -1,
                'post_type'      => 'yith-wcet-etemplate',
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_status'    => 'publish',
                'fields'         => 'ids'
            );
            $templates = get_posts( $args );
            foreach ( $templates as $template_id ) {
                $templates_array[ $template_id ] = get_the_title( $template_id );
            }

            $settings = apply_filters( 'yith_wcet_premium_email_extra_settings', $settings, $templates_array );

            return $settings;
        }

        /**
         * Add socials tabs and remove premium tab
         *
         * @access public
         * @since  1.0.0
         */
        public function settings_premium_tabs( $tabs ) {
            if ( isset( $tabs[ 'premium' ] ) )
                unset( $tabs[ 'premium' ] );

            $tabs[ 'socials' ] = __( 'Social Network', 'yith-woocommerce-email-templates' );

            return $tabs;
        }

        /**
         * render Email Template metabox
         *
         * @param $post WP_Post
         *
         * @return void
         */
        public function metabox_render( $post ) {

            $meta = get_post_meta( $post->ID, '_template_meta', true );

            $default = array(
                'txt_color_default'            => '#000000',
                'txt_color'                    => '#000000',
                'bg_color_default'             => '#F5F5F5',
                'bg_color'                     => '#F5F5F5',
                'base_color_default'           => '#2470FF',
                'base_color'                   => '#2470FF',
                'body_color_default'           => '#FFFFFF',
                'body_color'                   => '#FFFFFF',
                'logo_url'                     => '',
                'custom_logo_url'              => get_option( 'yith-wcet-custom-default-header-logo' ),
                'page_width'                   => '800',
                // - - - - -      P R E M I U M      - - - - -
                'logo_height'                  => '100',
                'header_padding'               => array( 36, 48, 36, 48 ),
                'premium_mail_style'           => 'default',
                'footer_logo_url'              => '',
                'header_position'              => 'center',
                'header_color'                 => 'transparent',
                'header_color_default'         => 'transparent',
                'link_color'                   => '#333333',
                'link_color_default'           => '#333333',
                'footer_text_color'            => '#555555',
                'footer_text_color_default'    => '#555555',
                'page_border_radius'           => '3',
                'h1_size'                      => '30',
                'h2_size'                      => '18',
                'h3_size'                      => '16',
                'body_size'                    => '14',
                'body_line_height'             => '20',
                'table_border_width'           => '1',
                'table_border_color'           => '#555555',
                'table_border_color_default'   => '#555555',
                'table_bg_color'               => '#FFFFFF',
                'table_bg_color_default'       => '#FFFFFF',
                'price_title_bg_color'         => '#FFFFFF',
                'price_title_bg_color_default' => '#FFFFFF',
                'show_prod_thumb'              => '',
                'footer_text'                  => '',
                'socials_on_header'            => 0,
                'socials_on_footer'            => 0,
                'socials_color'                => 'black',
                'custom_links'                 => array(),
                'additional_css'               => ''
            );

            $args = wp_parse_args( $meta, $default );

            $args = apply_filters( 'yith_wcet_metabox_options_content_args', $args );

            yith_wcet_metabox_options_content_premium( $args );

        }

        /**
         * metabox save
         *
         * @param $post_id
         */
        public function metabox_save( $post_id ) {
            if ( !empty( $_POST[ '_template_meta' ] ) ) {
                $meta = $_POST[ '_template_meta' ];
                //checkbox
                $meta[ 'show_prod_thumb' ]   = ( !isset( $_POST[ '_template_meta' ][ 'show_prod_thumb' ] ) ) ? 0 : 1;
                $meta[ 'socials_on_header' ] = ( !isset( $_POST[ '_template_meta' ][ 'socials_on_header' ] ) ) ? 0 : 1;
                $meta[ 'socials_on_footer' ] = ( !isset( $_POST[ '_template_meta' ][ 'socials_on_footer' ] ) ) ? 0 : 1;

                //custom links
                $meta[ 'custom_links' ] = ( !empty( $_POST[ '_template_meta' ][ 'custom_links' ] ) ) ? $_POST[ '_template_meta' ][ 'custom_links' ] : array();

                $empty_custom_links = 1;
                foreach ( $meta[ 'custom_links' ] as $key => $m ) {
                    if ( ( $m[ 'text' ] == '' ) && ( $m[ 'url' ] == '' ) ) {
                        unset( $meta[ 'custom_links' ][ $key ] );
                    } else {
                        $empty_custom_links = 0;
                    }
                }
                if ( $empty_custom_links ) {
                    $meta[ 'custom_links' ] = array();
                }

                update_post_meta( $post_id, '_template_meta', $meta );
            }
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 1.1.0
         */
        public function register_plugin_for_activation() {
            if ( function_exists( 'YIT_Plugin_Licence' ) ) {
                YIT_Plugin_Licence()->register( YITH_WCET_INIT, YITH_WCET_SECRET_KEY, YITH_WCET_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 1.1.0
         */
        public function register_plugin_for_updates() {
            if ( function_exists( 'YIT_Upgrade' ) ) {
                YIT_Upgrade()->register( YITH_WCET_SLUG, YITH_WCET_INIT );
            }
        }
    }
}