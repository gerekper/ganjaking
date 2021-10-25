<?php

namespace MasterAddons\Modules\MegaMenu;

defined('ABSPATH') || exit;

class JLTMA_Megamenu_Api
{

    use JLTMA_Mega_Menu_Rest_API;

    private static $_instance = null;

    public function __construct()
    {
        $this->config('megamenu', '');
        $this->init();
    }

    public function get_jltma_save_menuitem_settings()
    {
        $menu_item_id = $this->request['settings']['menu_id'];
        $menu_item_settings = json_encode($this->request['settings']);

        update_post_meta($menu_item_id, \JLTMA_Megamenu_Options::$jltma_menuitem_settings_key, $menu_item_settings);

        return [
            'saved' => 1,
            'message' => esc_html__('Saved', MELA_TD),
        ];
    }

    public function get_get_menuitem_settings()
    {
        $menu_item_id = $this->request['menu_id'];
        $data = get_post_meta($menu_item_id, \JLTMA_Megamenu_Options::$jltma_menuitem_settings_key, true);

        return (array) json_decode($data);
    }


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

if (!function_exists('jltma_megamenu_api')) {
    function jltma_megamenu_api()
    {
        return JLTMA_Megamenu_Api::get_instance();
    }
}

jltma_megamenu_api();
