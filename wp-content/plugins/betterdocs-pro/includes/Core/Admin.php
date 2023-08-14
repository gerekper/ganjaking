<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Utils\Base;
use WPDeveloper\BetterDocs\Utils\Helper;
use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocsPro\Admin\Analytics;
use WPDeveloper\BetterDocs\Dependencies\DI\Container;

class Admin extends Base {

    private $settings;
    private $container;

    public function __construct( Settings $settings, Container $container ) {
        $this->settings  = $settings;
        $this->container = $container;

        if ( ! is_admin() ) {
            return;
        }

        add_filter( 'manage_docs_posts_columns', [$this, 'views_columns'] );
        add_filter( 'manage_docs_posts_custom_column', [$this, 'manage_views_columns'], 10, 2 );

        // add_filter( 'plugin_action_links_betterdocs/betterdocs.php', [$this, 'insert_plugin_links'] );
        add_filter( 'admin_body_class', [$this, 'admin_body_classes'] );

        add_action( 'admin_enqueue_scripts', [$this, 'enqueue'] );

        /**
         * doc_category extra meta fields
         */

        add_action( 'betterdocs_doc_category_add_form_after', [$this, 'handbook_layout_cover_image'] );
        add_action( 'betterdocs_doc_category_update_form_after', [$this, 'update_handbook_layout_cover_image'], 10, 1 );

        if ( $this->settings->get( 'multiple_kb' ) ) {
            /**
             * Add Multiple KB Menu on Dashboad Sidebar
             */
            add_filter( 'betterdocs_admin_menu', [$this, 'menu'] );

            if ( ! isset( $_GET['mode'] ) || trim( $_GET['mode'] ) != 'list' ) {
                add_action( 'betterdocs_admin_header_before_end', [$this, 'add_knowledge_base_filter'] );
            }
            if ( isset( $_GET['mode'] ) && trim( $_GET['mode'] ) == 'list' ) {
                add_action( 'betterdocs_admin_filter_after_category', [$this, 'filter_by_kb'] );
            }
        }

        if ( isset( $_GET['mode'] ) && trim( $_GET['mode'] ) == 'list' ) {
            add_action( 'betterdocs_admin_filter_before_submit', [$this, 'filter_by_view'] );
        }
    }

    public function views_columns( $columns ) {
        $new_columns = [];

        foreach ( $columns as $key => $value ) {
            if ( $key == 'date' ) {
                $new_columns['betterdocs_views'] = __( 'Views', 'betterdocs-pro' );
            }
            $new_columns[$key] = $value;
        }

        return $new_columns;
    }

    public function manage_views_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'betterdocs_views':
                $views         = (int) $this->container->get( Analytics::class )->get_views( $post_id );
                $analytics_url = admin_url( 'admin.php?page=betterdocs-analytics&betterdocs=' . $post_id . '&comparison_factor=views,feelings' );
                echo ! empty( $views ) ? '<a href="' . $analytics_url . '">' . $views . '</a>' : 0;
                break;
        }
    }

    public function insert_plugin_links( $links ) {
        if ( isset( $links['deactivate'] ) ) {
            $links['deactivate'] = sprintf( __( 'Required by %s', 'betterdocs-pro' ), 'BetterDocs Pro' );
        }

        return $links;
    }

    public function admin_body_classes( $classes ) {
        return $classes . ' betterdocs-pro ';
    }

    public function handbook_layout_cover_image() {
        betterdocs()->views->get( 'admin/taxonomy/handbook-layout-image-add' );
    }

    public function update_handbook_layout_cover_image( $term ) {
        $cat_thumb_id = get_term_meta( $term->term_id, 'doc_category_thumb-id', true );
        betterdocs()->views->get( 'admin/taxonomy/handbook-layout-image-update', [
            'cat_thumb_id' => $cat_thumb_id
        ] );
    }

    public function menu( $menus ) {
        $menus['multiple_kb'] = Helper::normalize_menu(
            __( 'Multiple KB', 'betterdocs-pro' ),
            'edit-tags.php?taxonomy=knowledge_base&post_type=docs',
            'manage_knowledge_base_terms'
        );

        return $menus;
    }

    public function enqueue( $hook ) {
        // @todo: check the hook condition.

        if ( $hook !== 'toplevel_page_betterdocs-admin' ) {
            return;
        }

        global $current_screen;

        $_params = [
            'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
            'doc_cat_order_nonce'        => wp_create_nonce( 'doc_cat_order_nonce' ),
            'knowledge_base_order_nonce' => wp_create_nonce( 'knowledge_base_order_nonce' ),
            'paged'                      => isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0,
            'menu_title'                 => __( 'Switch to BetterDocs UI', 'betterdocs-pro' )
        ];

        if ( isset( $current_screen->taxonomy ) ) {
            $_params['per_page_id'] = "edit_{$current_screen->taxonomy}_per_page";
        }

        betterdocs_pro()->assets->enqueue( 'betterdocs-pro-admin', 'admin/css/betterdocs-admin.css' );
        betterdocs_pro()->assets->enqueue( 'betterdocs-pro-admin', 'admin/js/betterdocs.js', ['jquery'] );
        betterdocs_pro()->assets->localize( 'betterdocs-pro-admin', 'betterdocs_pro_admin', $_params );
    }

    public function add_knowledge_base_filter() {
        betterdocs()->views->get( 'admin/header-parts/kb' );
    }

    public function filter_by_kb() {
        betterdocs()->views->get( 'admin/header-parts/filter-by-kb' );
    }

    public function filter_by_view() {
        betterdocs()->views->get( 'admin/header-parts/filter-by-view' );
    }
}
