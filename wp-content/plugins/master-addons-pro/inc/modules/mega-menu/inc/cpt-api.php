<?php

namespace MasterAddons\Modules\MegaMenu;

class JLTMA_Megamenu_Cpt_Api
{

    use JLTMA_Mega_Menu_Rest_API;

    private static $_instance = null;

    public function __construct()
    {
        $this->config("mastermega-content", "/(?P<type>\w+)/(?P<key>\w+(|[-]\w+))/");
        $this->init();
    }


    public function get_jltma_content_editor()
    {
        $content_key = $this->request['key'];
        $content_type = $this->request['type'];

        $builder_post_title = 'mastermega-content-' . $content_type . '-' . $content_key;

        $builder_post_id = get_page_by_title($builder_post_title, OBJECT, 'mastermega_content');

        if (is_null($builder_post_id)) {
            $defaults = array(
                'post_content' => '',
                'post_title' => $builder_post_title,
                'post_status' => 'publish',
                'post_type' => 'mastermega_content',
            );
            $builder_post_id = wp_insert_post($defaults);

            update_post_meta($builder_post_id, '_wp_page_template', 'elementor_canvas');
        } else {
            $builder_post_id = $builder_post_id->ID;
        }

        $url = get_admin_url() . '/post.php?post=' . $builder_post_id . '&action=elementor';
        wp_redirect($url);
        exit;
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

// Returns Instanse of the Master Mega Menu Custom Post Type
if (!function_exists('jltma_megamenu_cpt_api')) {
    function jltma_megamenu_cpt_api()
    {
        return JLTMA_Megamenu_Cpt_Api::get_instance();
    }
}
jltma_megamenu_cpt_api();
