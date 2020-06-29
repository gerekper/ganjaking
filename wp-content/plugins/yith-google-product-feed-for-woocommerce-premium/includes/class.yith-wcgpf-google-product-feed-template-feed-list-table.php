<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Google Product Feed List Table
 *
 * @class   YITH_WCGPF_Google_Product_Feed_Template_List_Table
 * @package YITH Google Product Feed for WooCommerce
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class YITH_WCGPF_Google_Product_Feed_Template_List_Table extends WP_List_Table {


    /**
     * YITH_WCGPF_Google_Product_Feed_Template_List_Table constructor.
     *
     * @param array $args
     */
    public function __construct( $args = array() ) {
        parent::__construct( array(
            'singular' => esc_html__( 'List of feed configuration templates', 'yith-google-product-feed-for-woocommerce' ),
            'plural'   => esc_html__( 'List of feed configuration templates', 'yith-google-product-feed-for-woocommerce' ),
            'ajax'     => false
        ) );
    }

    /**
     * @return array
     */
    function get_columns() {

        $columns = array(
            'cb'         => '<input type="checkbox" />',
            'file_name' => esc_html__( 'File name', 'yith-google-product-feed-for-woocommerce' ),
            'merchant' => esc_html__( 'Merchant', 'yith-google-product-feed-for-woocommerce' ),
        );


        return apply_filters( 'yith_wcgpf_list_columns', $columns );
    }

    /**
     * @param object $item
     * @param string $column_name
     *
     * @return string|void
     */
    function column_default( $item, $column_name ) {

        switch( $column_name ) {

            case 'file_name':
                return ($item->post_title) ? '<span>'.$item->post_title.'</span>':'';
                break;

            case 'merchant':
                return '<span>Google</span>';
                break;

            default:
                return apply_filters('yith_wcgpf_column_default','',$item, $column_name);
        }
    }

    /**
     * get views for the table
     * @author YITHEMES
     * @since 1.0.0
     * @return array
     */
    protected function get_views()
    {
        $views = array( 'all' => esc_html__( 'All', 'yith-google-product-feed-for-woocommerce' ),
            'publish' => esc_html__( 'Published', 'yith-google-product-feed-for-woocommerce' ),
            'trash' => esc_html__( 'Trash', 'yith-google-product-feed-for-woocommerce' ) );

        $current_view = $this->get_current_view();

        foreach ( $views as $view_id => $view ) {

            $query_args = array(
                'posts_per_page' => -1,
                'post_type' => 'yith-wcgpf-template',
                'post_status' => 'publish',
                'suppress_filter' => false
            );
            $status = 'status';
            $id = $view_id;

            if ( 'all' !== $view_id ) {
                $query_args[ 'post_status' ] = $view_id;
            }

            $href = esc_url( add_query_arg( $status, $id ) );
            $total_items = count( get_posts( $query_args ) );
            $class = $view_id == $current_view ? 'current' : '';
            $views[ $view_id ] = sprintf( "<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>", $href, $class, $view, $total_items );
        }


        return $views;
    }

    /**
     * return current view
     * @author YITHEMES
     * @since 1.0.0
     * @return string
     */
    public function get_current_view()
    {

        return empty( $_GET[ 'status' ] ) ? 'all' : $_GET[ 'status' ];
    }

    /**
     * Prepares the list of items for displaying.
     *
     * @since 1.0.0
     */
    function prepare_items() {

        $current_view = $this->get_current_view();
        if($current_view == 'all'){
            $current_view = 'any';
        }

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $perpage = apply_filters( 'yith_wcgpf_per_page', 15 );

        $args = array(
            'post_type'           => 'yith-wcgpf-template',
            'post_status'         => $current_view,
            'posts_per_page'      => $perpage,
            'paged'               => absint( $this->get_pagenum() ),
            'orderby'             => 'ID',
            'order'               => 'DESC',
        );
        $query = new WP_Query( $args );
        $this->items = $query->posts;

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $query->found_posts,
            "per_page" => $perpage
        ) );
    }

    /**
     * @author YITHEMES
     * @since 1.0.0
     * @param object $item
     * @return string
     */
    public function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="wcgpf_ids[]" value="%s" />', $item->ID
        );
    }

    /**
     * return bulk actions
     * @author YITHEMES
     * @since 1.0.0
     * @return array|false|string
     */
    public function get_bulk_actions()
    {

        $actions = $this->current_action();

        if ( isset( $_REQUEST[ 'wcgpf_ids' ] ) ) {

            $rules = $_REQUEST[ 'wcgpf_ids' ];

            if ( $actions == 'delete' ) {
                foreach ( $rules as $rule_id ) {
                    wp_delete_post( $rule_id, true );
                }
            }

            $this->prepare_items();
        }

        $current_view = $this->get_current_view();
        if ($current_view == 'trash') {
            $actions = array(
                'delete' => esc_html__( 'Delete permanently', 'yith-google-product-feed-for-woocommerce' )
            );
        } else {
            $actions = array(
                'delete' => esc_html__( 'Delete', 'yith-google-product-feed-for-woocommerce' )
            );
        }
        return $actions;
    }
    /**
     * @return array
     */
    function get_sortable_columns() {

        $sortable_columns = array(
            'post_title'         => array( 'Rule name', false ),
            'rule_type'          => array( 'Rule type', false ),
            'priority'           => array( 'Priority', false ),
        );
        return $sortable_columns;
    }

    /**
     * Function to edit or delete rules
     * @return array
     */
    protected function handle_row_actions( $post, $column_name, $primary ) {
        if ( $primary !== $column_name ) {
            return '';
        }

        $post_type_object = get_post_type_object( $post->post_type );
        $can_edit_post = current_user_can( 'edit_post', $post->ID );
        $title = _draft_or_post_title();
        $actions = array();

        if ( $can_edit_post && 'trash' != $post->post_status ) {
            $actions['edit'] = sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                get_edit_post_link( $post->ID ),
                /* translators: %s: post title */
                esc_attr( sprintf( esc_html__( 'Edit &#8220;%s&#8221;' ), $title ) ),
                esc_html__( 'Edit' )
            );
        }

        if ( current_user_can( 'delete_post', $post->ID ) ) {
            if ( 'trash' === $post->post_status ) {
                $actions['untrash'] = sprintf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( esc_html__( 'Restore &#8220;%s&#8221; from the Trash' ), $title ) ),
                    esc_html__( 'Restore' )
                );
            } elseif ( EMPTY_TRASH_DAYS ) {
                $actions['trash'] = sprintf(
                    '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
                    get_delete_post_link( $post->ID ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( esc_html__( 'Move &#8220;%s&#8221; to the Trash' ), $title ) ),
                    esc_html_x( 'Trash', 'verb' )
                );
            }
            if ( 'trash' === $post->post_status || ! EMPTY_TRASH_DAYS ) {
                $actions['delete'] = sprintf(
                    '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
                    get_delete_post_link( $post->ID, '', true ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( esc_html__( 'Delete &#8220;%s&#8221; permanently' ), $title ) ),
                    esc_html__( 'Delete permanently' )
                );
            }
        }

        return $this->row_actions( $actions );
    }





}