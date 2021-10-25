<?php

namespace MasterAddons\Modules\MegaMenu;

defined('ABSPATH') || exit;

class Master_Menu
{

    public $dir;

    public $url;

    private static $plugin_path;

    private static $plugin_url;

    private static $_instance = null;

    public function __construct()
    {

        // Current Path
        $this->dir = dirname(__FILE__) . '/';

        $this->url = self::plugin_url() . '/mega-menu/';

        // Include Files
        $this->jltma_include_files();

        jltma_megamenu_assets();
        jltma_megamenu_options();

        jltma_megamenu_api()->init();
        jltma_megamenu_cpt_api()->init();

        if (is_admin()) {
            jltma_megamenu_cpt();
        }
    }


    public function jltma_include_files()
    {
        include $this->dir . '/inc/cpt.php';
        include $this->dir . '/inc/megamenu-assets.php';
        include $this->dir . '/inc/rest-api.php';
        include $this->dir . '/inc/api.php';
        include $this->dir . '/inc/options.php';
        include $this->dir . '/inc/walker-nav-menu.php';
        include $this->dir . '/inc/cpt-api.php';
    }

    public static function plugin_url()
    {
        if (self::$plugin_url) {
            return self::$plugin_url;
        }
        return self::$plugin_url = untrailingslashit(plugins_url('/', __FILE__));
    }

    public static function plugin_path()
    {
        if (self::$plugin_path) {
            return self::$plugin_path;
        }
        return self::$plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

/*
* Returns Instanse of the Master Mega Menu
*/
if (!function_exists('jltma_megamenu')) {
    function jltma_megamenu()
    {
        return  Master_Menu::get_instance();
    }
}

jltma_megamenu();

/* Re-write flus */
register_activation_hook(__FILE__, 'jltma_flush_rewrites');
register_deactivation_hook(__FILE__, 'jltma_flush_rewrites');
if (!function_exists('jltma_flush_rewrites')) {
    function jltma_flush_rewrites()
    {
        flush_rewrite_rules();
    }
}
