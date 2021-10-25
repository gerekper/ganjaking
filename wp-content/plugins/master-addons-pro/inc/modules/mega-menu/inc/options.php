<?php

namespace MasterAddons\Modules\MegaMenu;

defined('ABSPATH') || exit;

class JLTMA_Megamenu_Options
{
    private $dir;
    private $url;

    protected $current_menu_id = null;

    private static $_instance = null;

    public static $key = 'masteraddons_options';
    public static $jltma_menuitem_settings_key = 'jltma_menu_settings';
    public static $jltma_menu_settings_key = 'jltma_megamenu_settings';


    public function __construct()
    {

        $this->dir = dirname(__FILE__) . '/';
        $this->url = Master_Menu::plugin_url();

        add_action('admin_footer', [$this, 'options_menu_item']);

        add_action('admin_head-nav-menus.php', array($this, 'jltma_nav_meta_box_register'), 9);

        add_action('wp_ajax_jltma_save_megamenu_options', [$this, 'jltma_save_megamenu_options']);
        add_action('wp_ajax_jltma_get_megamenu_options', [$this, 'get_jltma_get_megamenu_options']);

        // Exclude Mega Menu from Search Engine indexing
        add_action('wp_head', [$this, 'jltma_meta_add_exclude_search']);
    }

    function get_jltma_get_megamenu_options()
    {

        $menu_id = $this->current_menu_id();
        $data = $this->get_option(self::$jltma_menu_settings_key, []);
        $data = (isset($data['menu_location_' . $menu_id])) ? $data['menu_location_' . $menu_id] : [];

        $response = array(
            "menu_id" => $menu_id
        );
        $response = json_encode($response);

        echo $response;
        die();
    }


    public function jltma_meta_add_exclude_search()
    {
        if (get_post_type() == 'master_addons_template') {
            echo '<meta name="robots" content="noindex,nofollow" />', "\n";
        }
    }

    public static function get_icons()
    {
        return include 'icon-list.php';

        // return include \Master_Menu::plugin_url() .'/inc/icon_settings.php';
    }

    function options_menu_item()
    {
        $screen = get_current_screen();
        if ($screen->base != 'nav-menus') {
            return;
        }

        include 'view/modal-options.php';
    }


    /*
    * Register Page Nav Meta Box settings
    */
    public function jltma_nav_meta_box_register()
    {
        global $pagenow;

        if ('nav-menus.php' !== $pagenow) {
            return;
        }

        add_meta_box(
            'jltma-mega-menu-settings',
            esc_html__('Master Addons Mega Menu', MELA_TD),
            [$this, 'jltma_metabox_render'],
            'nav-menus',
            'side',
            'high'
        );
    }


    public function get_settings($menu_id)
    {
        return get_term_meta($menu_id, $this->meta_key, true);
    }


    public function update_settings($menu_id = 0, $settings = array())
    {
        update_term_meta($menu_id, $this->meta_key, $settings);
    }




    public function jltma_metabox_render()
    {
        $menu_id = $this->current_menu_id();
        $data = $this->get_option(self::$jltma_menu_settings_key, []);
        $data = (isset($data['menu_location_' . $menu_id])) ? $data['menu_location_' . $menu_id] : [];
?>
        <div class="master-mega-menu-accordion d-flex justify-content-between" id="jltma-megamenu-options">
            <div class="font-weight-bold">
                <?php _e("Enable Mega Menu", MELA_TD) ?>
            </div>
            <div class="jltma-checkbox-container">
                <input type='checkbox' id="jltma-menu-metabox-input-is-enabled" class='jltma-menu-metabox-input-is-enabled' name='is_enabled' value='1' <?php checked((isset($data['is_enabled']) ? $data['is_enabled'] : ''), '1'); ?> />
                <label for="jltma-menu-metabox-input-is-enabled">
                    <?php _e("Enable Mega Menu?", MELA_TD) ?>
                </label>
            </div>
        </div>
        <div class="jltma-notice p-2 mt-2 mb-1 bg-success text-white" style="display: none;"></div>

<?php
    }



    public function current_menu_id()
    {

        if (null !== $this->current_menu_id) {
            return $this->current_menu_id;
        }

        $nav_menus            = wp_get_nav_menus(array('orderby' => 'name'));
        $menu_count           = count($nav_menus);
        $nav_menu_selected_id = isset($_REQUEST['menu']) ? (int) sanitize_key($_REQUEST['menu']) : 0;
        $add_new_screen       = (isset($_GET['menu']) && 0 == $_GET['menu']) ? true : false;

        $this->current_menu_id = $nav_menu_selected_id;

        // If we have one theme location, and zero menus, we take them right into editing their first menu
        $page_count = wp_count_posts('page');
        $one_theme_location_no_menus = (1 == count(get_registered_nav_menus()) && !$add_new_screen && empty($nav_menus) && !empty($page_count->publish)) ? true : false;

        // Get recently edited nav menu
        $recently_edited = absint(get_user_option('nav_menu_recently_edited'));
        if (empty($recently_edited) && is_nav_menu($this->current_menu_id)) {
            $recently_edited = $this->current_menu_id;
        }

        // Use $recently_edited if none are selected
        if (empty($this->current_menu_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited)) {
            $this->current_menu_id = $recently_edited;
        }

        // On deletion of menu, if another menu exists, show it
        if (!$add_new_screen && 0 < $menu_count && isset($_GET['action']) && 'delete' == $_GET['action']) {
            $this->current_menu_id = $nav_menus[0]->term_id;
        }

        // Set $this->current_menu_id to 0 if no menus
        if ($one_theme_location_no_menus) {
            $this->current_menu_id = 0;
        } elseif (empty($this->current_menu_id) && !empty($nav_menus) && !$add_new_screen) {
            // if we have no selection yet, and we have menus, set to the first one in the list
            $this->current_menu_id = $nav_menus[0]->term_id;
        }

        return $this->current_menu_id;
    }


    public function jltma_save_megamenu_options()
    {

        $menu_id = $this->current_menu_id();
        $is_enabled = isset($_REQUEST['is_enabled']) ? sanitize_key($_REQUEST['is_enabled']) : 0;

        $data = $this->get_option(self::$jltma_menu_settings_key, []);
        $data['menu_location_' . $menu_id] = [
            'is_enabled' => $is_enabled,
        ];

        $this->save_sanitized(self::$jltma_menu_settings_key, $data);

        $response = array(
            "status" => "success",
            "message" => esc_html__("Mega Menu Saved", MELA_TD)
        );
        $response = json_encode($response);

        echo $response;

        die();
    }

    public function get_option($key, $default = '')
    {
        $data_all = get_option(self::$key);
        return (isset($data_all[$key]) && $data_all[$key] != '') ? $data_all[$key] : $default;
    }

    public function save_sanitized($key, $value = '', $senitize_func = 'sanitize_text_field')
    {
        $data_all = get_option(self::$key);

        $value = self::sanitize($value, $senitize_func);

        $data_all[$key] = $value;
        update_option('masteraddons_options', $data_all);
    }


    public static function sanitize($value, $senitize_func = 'sanitize_text_field')
    {
        $senitize_func = (in_array($senitize_func, [
            'sanitize_email',
            'sanitize_file_name',
            'sanitize_hex_color',
            'sanitize_hex_color_no_hash',
            'sanitize_html_class',
            'sanitize_key',
            'sanitize_meta',
            'sanitize_mime_type',
            'sanitize_sql_orderby',
            'sanitize_option',
            'sanitize_text_field',
            'sanitize_title',
            'sanitize_title_for_query',
            'sanitize_title_with_dashes',
            'sanitize_user',
            'esc_url_raw',
            'wp_filter_nohtml_kses',
        ])) ? $senitize_func : 'sanitize_text_field';

        if (!is_array($value)) {
            return $senitize_func($value);
        } else {
            return array_map(function ($inner_value) use ($senitize_func) {
                return self::sanitize($inner_value, $senitize_func);
            }, $value);
        }
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}



if (!function_exists('jltma_megamenu_options')) {
    function jltma_megamenu_options()
    {
        return JLTMA_Megamenu_Options::get_instance();
    }
}

jltma_megamenu_options();
