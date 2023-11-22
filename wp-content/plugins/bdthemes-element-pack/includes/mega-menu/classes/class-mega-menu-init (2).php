<?php

namespace ElementPack\Includes\MegaMenu;

defined('ABSPATH') || exit;

class Mega_Menu_Init {
    public $dir;
    public $url;

    public static $megamenu_options_key  = '_ep_mega_menu_options';
    public static $megamenu_settings_key  = '_ep_mega_menu_settings';
    public static $menu_item_settings_key = '_ep_mega_menu_item_settings';

    public function __construct() {

        add_action('admin_enqueue_scripts', [$this, 'ep_megamenu_load_assets']);
        add_action('wp_ajax_ep_get_menu_item_settings', [$this, 'ep_get_menu_item_settings']);
        add_action('wp_ajax_ep_save_menu_item_settings', [$this, 'ep_save_menu_item_settings']);
        add_action('wp_ajax_ep_get_content_editor', [$this, 'ep_get_content_editor']);

        $this->ep_megamenu_includes_files();
    }


    public function ep_megamenu_includes_files() {
        include $this->dir . 'class-mega-menu-options.php';
        include $this->dir . 'class-mega-menu-cpt.php';
        include $this->dir . 'class-mega-menu-walker.php';
    }

    /**
     * !enqueue assets
     */
    public function ep_megamenu_load_assets() {
        $current_screen = get_current_screen();
        if ($current_screen->base == 'nav-menus') {
            $this->enqueue_styles();
            $this->enqueue_scripts();
        }
    }


    public function enqueue_styles() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('aesthetic-icon-picker', BDTEP_ADMIN_URL . 'assets/vendor/aesthetic-icon-picker/css/style.css', false, BDTEP_VER);
        wp_enqueue_style('ep-megamenu-admin', BDTEP_ADMIN_URL . 'assets/css/ep-megamenu-admin.css', false, BDTEP_VER);
        wp_enqueue_style('font-awesome', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css', false, '5.15.3');
    }



    public function enqueue_scripts() {
        wp_enqueue_script('aesthetic-icon-picker', BDTEP_ADMIN_URL . 'assets/vendor/aesthetic-icon-picker/js/aesthetic-icon-picker.js',    array('jquery'), BDTEP_VER, true);
        wp_enqueue_script('ep-megamenu-admin', BDTEP_ADMIN_URL . 'assets/js/ep-megamenu-admin.js', ['jquery', 'wp-color-picker'], BDTEP_VER, true);
        wp_localize_script('ep-megamenu-admin', 'megaMenuBuilder', ['items' => $this->mega_menu_items()]);
    }



    private function mega_menu_items() {
        $args = [
            'post_type'      => 'nav_menu_item',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'nopaging'       => true,
            'fields'         => 'ids',
        ];
        $items = new \WP_Query($args);

        $menuItems = [];

        foreach ($items->posts as $item) {
            $data = get_post_meta($item, Mega_Menu_Init::$menu_item_settings_key, true);
            $data = (array) json_decode($data);

            if (isset($data['menu_enable']) && $data['menu_enable'] == 1) {
                $menuItems[] = "#menu-item-" . $item;
            }
        }

        return $menuItems;
    }

    public function ep_save_menu_item_settings() {

        if (!current_user_can('manage_options')) {
            return;
        }

        $menu_item_id       = sanitize_key($_REQUEST['settings']['menu_id']);
        $menu_item_settings = json_encode($_REQUEST['settings'], JSON_UNESCAPED_UNICODE);
        update_post_meta($menu_item_id, Mega_Menu_Init::$menu_item_settings_key, $menu_item_settings);

        $data = [
            'menu_id'        => $menu_item_id,
            'saved' => 1,
            'message' => esc_html__('Successfully Saved', 'bdthemes-element-pack')
        ];

        wp_send_json($data);
    }

    public function ep_get_menu_item_settings() {

        if (!current_user_can('manage_options')) {
            return;
        }

        $menu_item_id = sanitize_key($_REQUEST['menu_id']);
        $data         = get_post_meta($menu_item_id, Mega_Menu_Init::$menu_item_settings_key, true);
        if (empty($data)) {
            $data = [
                'menu_id'        => $menu_item_id,
                'menu_has_child' => '',
                'menu_enable'    => '',
            ];
            $data = json_encode($data);
        }

        echo sanitize_text_field($data);
        wp_die();
    }

    public function ep_get_content_editor() {

        $content_key = sanitize_key($_REQUEST['key']);

        $builder_post_title = 'bdt-ep-megamenu-content-' . $content_key;
        $builder_post_id    = get_page_by_title($builder_post_title, OBJECT, 'ep_megamenu_content');

        if (is_null($builder_post_id)) {
            $defaults = [
                'post_type'    => 'ep_megamenu_content',
                'post_status'  => 'publish',
                'post_title'   => $builder_post_title,
                'post_content' => '',
            ];
            $builder_post_id = wp_insert_post($defaults);

            update_post_meta($builder_post_id, '_wp_page_template', 'elementor_canvas');
        } else {
            $builder_post_id = $builder_post_id->ID;
        }

        $url = get_admin_url() . 'post.php?post=' . $builder_post_id . '&action=elementor';
        echo esc_url_raw($url);
        wp_die();
    }

    public static function init() {
        new Mega_Menu_Init;
    }
}

/**
 * Initialize Element Pack Mega Menu
 */
Mega_Menu_Init::init();
