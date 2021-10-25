<?php

namespace MasterAddons\Modules\MegaMenu;

defined('ABSPATH') || exit;

class JLTMA_Megamenu_Assets
{

    private static $_instance = null;

    public function __construct()
    {

        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_js']);
        add_action('admin_print_scripts', [$this, 'admin_js']);
    }

    public function common_js()
    {
        ob_start();
?>
        var masteraddons = {
        resturl: '<?php echo get_rest_url() . 'masteraddons/v2/'; ?>',
        }
<?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }



    // Frontend Scripts
    public function frontend_js()
    {
        $add_inline_script = $this->common_js();
        wp_add_inline_script('mega-menu-nav-menu', $add_inline_script);
    }


    public function admin_enqueue_scripts()
    {

        $screen = get_current_screen();

        if ($screen->base == 'nav-menus') {

            // Stylesheets
            wp_enqueue_style('wp-color-picker');

            wp_enqueue_style('jltma-bootstrap', MELA_PLUGIN_URL . '/assets/css/bootstrap.min.css');
            wp_enqueue_style('mega-menu-style', MELA_PLUGIN_URL . '/assets/megamenu/css/megamenu.css');

            // Scripts
            wp_enqueue_script('jltma-bootstrap', MELA_PLUGIN_URL . '/assets/js/bootstrap.min.js', array('jquery'), MELA_VERSION, true);
            wp_enqueue_script('icon-picker', MELA_PLUGIN_URL . '/assets/megamenu/js/icon-picker.js', array('jquery'), MELA_VERSION, true);
            wp_enqueue_script('mega-menu-admin', MELA_PLUGIN_URL . '/assets/megamenu/js/mega-script.js', array('jquery', 'wp-color-picker'), MELA_VERSION, true);


            // Localize Scripts
            $localize_menu_data = array(
                'resturl'       => get_rest_url() . 'masteraddons/v2/'
            );
            wp_localize_script('mega-menu-admin', 'masteraddons', $localize_menu_data);
        }
    }


    // Admin Rest API Variable
    public function admin_js()
    {
        echo "<script type='text/javascript'>\n";
        echo $this->common_js();
        echo "\n</script>";
    }


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}




// Mega Menu
if (!function_exists('jltma_megamenu_assets')) {
    function jltma_megamenu_assets()
    {
        return JLTMA_Megamenu_Assets::get_instance();
    }
}

jltma_megamenu_assets();
