<?php

namespace ElementPack\Includes\MegaMenu;

defined('ABSPATH') || exit;

/**
 * Mega menu custom post type class this class add Menu menu content as post format.
 * since
 */
class Mega_Menu_CPT {

    public function __construct() {
        add_action('init', [$this, 'ep_register_megamenu_cpts']);
        register_activation_hook(__FILE__, [$this, 'flush_rewrite_rules']);
    }

    public function ep_register_megamenu_cpts() {

        /**
         * Post Type: Mega Menu Items.
         */

        $labels = [
            "name"          => __("Mega Menu Items", "bdthemes-element-pack"),
            "singular_name" => __("Mega Menu Item", "bdthemes-element-pack"),
        ];

        $rewrite = [
            'slug'       => 'bdt-ep-megamenu-content',
            'with_front' => true,
            'pages'      => false,
            'feeds'      => false,
        ];

        $args = [
            "label"                 => __("Mega Menu Items", "bdthemes-element-pack"),
            "labels"                => $labels,
            "description"           => "",
            "public"                => true,
            "publicly_queryable"    => true,
            "show_ui"               => false,
            "show_in_rest"          => true,
            "rest_base"             => "bdt-ep-megamenu-content",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive"           => false,
            "show_in_menu"          => true,
            "show_in_nav_menus"     => false,
            "delete_with_user"      => false,
            "exclude_from_search"   => true,
            "capability_type"       => "page",
            "map_meta_cap"          => true,
            "hierarchical"          => true,
            "rewrite"               => $rewrite,
            "query_var"             => true,
            "supports"              => ["title", "editor", "elementor", "permalink"],
            "show_in_graphql"       => false,
            'can_export'            => true,

        ];

        register_post_type("ep_megamenu_content", $args);
    }

    public function flush_rewrite_rules() {
        $this->ep_register_megamenu_cpts();
        flush_rewrite_rules();
    }
}

new Mega_Menu_CPT();
