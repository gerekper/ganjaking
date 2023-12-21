<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || exit;

class Cpt{

    public function __construct() {
        $this->post_type();
    }

    public function post_type() {

        $labels = array(
            'name'                  => _x( 'HA Nav items', 'Post Type General Name', 'happy-addons-pro' ),
            'singular_name'         => _x( 'HA Nav item', 'Post Type Singular Name', 'happy-addons-pro' ),
            'menu_name'             => esc_html__( 'HA Nav item', 'happy-addons-pro' ),
            'name_admin_bar'        => esc_html__( 'HA Nav item', 'happy-addons-pro' ),
            'archives'              => esc_html__( 'Item Archives', 'happy-addons-pro' ),
            'attributes'            => esc_html__( 'Item Attributes', 'happy-addons-pro' ),
            'parent_item_colon'     => esc_html__( 'Parent Item:', 'happy-addons-pro' ),
            'all_items'             => esc_html__( 'All Items', 'happy-addons-pro' ),
            'add_new_item'          => esc_html__( 'Add New Item', 'happy-addons-pro' ),
            'add_new'               => esc_html__( 'Add New', 'happy-addons-pro' ),
            'new_item'              => esc_html__( 'New Item', 'happy-addons-pro' ),
            'edit_item'             => esc_html__( 'Edit Item', 'happy-addons-pro' ),
            'update_item'           => esc_html__( 'Update Item', 'happy-addons-pro' ),
            'view_item'             => esc_html__( 'View Item', 'happy-addons-pro' ),
            'view_items'            => esc_html__( 'View Items', 'happy-addons-pro' ),
            'search_items'          => esc_html__( 'Search Item', 'happy-addons-pro' ),
            'not_found'             => esc_html__( 'Not found', 'happy-addons-pro' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'happy-addons-pro' ),
            'featured_image'        => esc_html__( 'Featured Image', 'happy-addons-pro' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'happy-addons-pro' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'happy-addons-pro' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'happy-addons-pro' ),
            'insert_into_item'      => esc_html__( 'Insert into item', 'happy-addons-pro' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', 'happy-addons-pro' ),
            'items_list'            => esc_html__( 'Items list', 'happy-addons-pro' ),
            'items_list_navigation' => esc_html__( 'Items list navigation', 'happy-addons-pro' ),
            'filter_items_list'     => esc_html__( 'Filter items list', 'happy-addons-pro' ),
        );
        $rewrite = array(
            'slug'                  => 'ha-nav-content',
            'with_front'            => true,
            'pages'                 => false,
            'feeds'                 => false,
        );
        $args = array(
            'label'                 => esc_html__( 'HA Nav item', 'happy-addons-pro' ),
            'description'           => esc_html__( 'ha_nav_content', 'happy-addons-pro' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'elementor', 'permalink' ),
            'hierarchical'          => true,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'rewrite'               => $rewrite,
            'query_var'             => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => false,
            'rest_base'             => 'ha-nav-content',
        );
        register_post_type( 'ha_nav_content', $args );
    }

    public static function flush_rewrites() {
        flush_rewrite_rules();
    }
}

new Cpt();
