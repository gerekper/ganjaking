<?php
namespace ElementPack\Includes\MegaMenu;

defined('ABSPATH') || exit;

class Mega_Menu_Options {
    protected $current_menu_id = null;

    public function __construct() {
        add_action('admin_footer', [$this, 'ep_get_mega_menu_options']);
        add_action('admin_head', [$this, 'ep_save_mega_menu_options']);
    }

    public function current_menu_id() {

        if ( null !== $this->current_menu_id ) {
            return $this->current_menu_id;
        }

        $nav_menus            = wp_get_nav_menus(['orderby' => 'name']);
        $menu_count           = count($nav_menus);
        $nav_menu_selected_id = isset($_REQUEST['menu']) ? sanitize_key($_REQUEST['menu']) : 0;
        $add_new_screen       = (isset($_GET['menu']) && 0 == $_GET['menu']) ? true : false;

        $this->current_menu_id = $nav_menu_selected_id;

        // If we have one theme location, and zero menus, we take them right into editing their first menu
        $page_count                  = wp_count_posts('page');
        $one_theme_location_no_menus = (1 == count(get_registered_nav_menus()) && !$add_new_screen && empty($nav_menus) && !empty($page_count->publish)) ? true : false;

        // Get recently edited nav menu
        $recently_edited = absint(get_user_option('nav_menu_recently_edited'));

        if ( empty($recently_edited) && is_nav_menu($this->current_menu_id) ) {
            $recently_edited = $this->current_menu_id;
        }

        // Use $recently_edited if none are selected
        if ( empty($this->current_menu_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited) ) {
            $this->current_menu_id = $recently_edited;
        }

        // On deletion of menu, if another menu exists, show it
        if ( !$add_new_screen && 0 < $menu_count && isset($_GET['action']) && 'delete' == $_GET['action'] ) {
            $this->current_menu_id = $nav_menus[0]->term_id;
        }

        // Set $this->current_menu_id to 0 if no menus
        if ( $one_theme_location_no_menus ) {
            $this->current_menu_id = 0;
        } elseif ( empty($this->current_menu_id) && !empty($nav_menus) && !$add_new_screen ) {
            // if we have no selection yet, and we have menus, set to the first one in the list
            $this->current_menu_id = $nav_menus[0]->term_id;
        }

        return $this->current_menu_id;
    }

    function ep_get_mega_menu_options() {
        $screen = get_current_screen();

        if ( $screen->base != 'nav-menus' ) {
            return;
        }

        $file = BDTEP_INC_PATH . 'mega-menu/templates/modal.php';

        if ( is_readable($file) ) {
            include $file;
        }

        $menu_id = $this->current_menu_id();
        $data    = $this->ep_get_option(Mega_Menu_Init::$megamenu_settings_key, []);
        $data    = (isset($data['menu_location_' . $menu_id])) ? $data['menu_location_' . $menu_id] : [];

        ?>
        <script>
            var ep_mega_menu_trigger_button = `
                <div class="ep-megamenu-switcher" id="ep-megamenu-switcher">
                    <div class="ep-checkbox-container">
                    <input id="ep-is-metabox-enabled" type="checkbox" class="ep-is-metabox-enabled bdt-switch" name="ep_megamenu_enabled" value="1"  <?php checked((isset($data['ep_megamenu_enabled']) ? $data['ep_megamenu_enabled'] : ''), '1'); ?> >
                    <label for="ep-is-metabox-enabled"><i class="bdt-wi-element-pack"></i> Mega Menu</label>
                    </div>`;
        </script>
        <?php
    }


    public function ep_save_mega_menu_options() {
        $screen = get_current_screen();
        if ( $screen->base != 'nav-menus' || !isset($_POST['update-nav-menu-nonce']) ) {
            return;
        }
        $menu_id             = isset($_POST['menu']) ? sanitize_key($_POST['menu']) : 0;
        $ep_megamenu_enabled = isset($_POST['ep_megamenu_enabled']) ? sanitize_key($_POST['ep_megamenu_enabled']) : 0;

        $data                              = $this->ep_get_option(Mega_Menu_Init::$megamenu_settings_key, []);
        $data['menu_location_' . $menu_id] = [
            'ep_megamenu_enabled' => $ep_megamenu_enabled,
        ];

        $this->ep_save_option(Mega_Menu_Init::$megamenu_settings_key, $data);
    }

    protected function ep_get_option($key, $default = '') {
        $data_all = get_option(Mega_Menu_Init::$megamenu_options_key);
        return (isset($data_all[$key]) && $data_all[$key] != '') ? $data_all[$key] : $default;
    }

    protected function ep_save_option($key, $value = '') {
        $data_all       = get_option(Mega_Menu_Init::$megamenu_options_key);
        $data_all[$key] = $value;
        update_option(Mega_Menu_Init::$megamenu_options_key, $data_all);
    }
}

new Mega_Menu_Options();
