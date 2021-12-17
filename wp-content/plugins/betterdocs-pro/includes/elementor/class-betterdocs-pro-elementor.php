<?php
use Elementor\Controls_Manager;
use ElementorPro\Modules\GlobalWidget\Documents\Widget;
use ElementorPro\Plugin;
use ElementorPro\Modules\ThemeBuilder as ThemeBuilder;

/**
 * Working with elementor plugin
 *
 *
 * @since      1.4.2
 * @package    BetterDocs_Pro
 * @subpackage BetterDocs_Pro/elementor
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class BetterDocs_Pro_Elementor
{
    public static $pro_active;

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.4.2
     */
    public static function init()
    {
        add_action('elementor/widgets/widgets_registered', [__CLASS__, 'register_widgets']);
        if (is_plugin_active('elementor-pro/elementor-pro.php')) {
            add_action('elementor/init', [__CLASS__, 'load_widget_file']);
        }
    }

    /**
     *
     * Mange all widget for single docs
     *
     * @return string[]
     * @since  1.4.2
     */
    public static function get_widget_list()
    {
        $widget_arr['betterdocs-elementor-reactions']     = 'BetterDocs_Elementor_Reactions';
        $widget_arr['betterdocs-elementor-multiple-kb']   = 'BetterDocs_Elementor_Multiple_Kb';
        $widget_arr['betterdocs-elementor-popular-view']  = 'Betterdocs_Elementor_Popular_View';
        $widget_arr['betterdocs-elementor-tab-view-list'] = 'BetterDocs_Elementor_Tab_View';
        
        return $widget_arr;
    }

    public static function load_widget_file()
    {
        //load widget file
        foreach (self::get_widget_list() as $key => $value) {
            require_once BETTERDOCS_PRO_ROOT_DIR_PATH . "includes/elementor/widgets/$key.php";
        }
    }

    public static function register_widgets($widgets_manager)
    {
        foreach (self::get_widget_list() as $value) {
            if (class_exists($value)) {
                $widgets_manager->register_widget_type(new $value);
            }
        }
    }
}

BetterDocs_Pro_Elementor::init();
