<?php

namespace ElementPack\Includes\SmoothScroller;

use Elementor\Plugin;
use Elementor\Core\Kits\Documents\Kit;
use ElementPack\Includes\SmoothScroller\Settings_Contorls;

if (!defined('ABSPATH')) exit;
class SmoothScroller_Loader {

    function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'element_pack_smooth_scroller']);
        add_action('elementor/init', [$this, 'smooth_scroller_tab_settings_init']);
    }



    public function smooth_scroller_tab_settings_init() {
        add_action('elementor/kit/register_tabs', function (Kit $kit) {
            $kit->register_tab('ep-smooth-scroller', Settings_Contorls::class);
        }, 1, 40);
    }



    public function ep_smooth_scroller_settings($setting_id) {
        global $smooth_scroller_settings;
        $return = '';
        if (!isset($smooth_scroller_settings['kit_settings'])) {
            $kit = Plugin::$instance->documents->get(Plugin::$instance->kits_manager->get_active_id(), false);
            $smooth_scroller_settings['kit_settings'] = $kit->get_settings();
        }

        if (isset($smooth_scroller_settings['kit_settings'][$setting_id])) {
            $return = $smooth_scroller_settings['kit_settings'][$setting_id];
        }

        return apply_filters('smooth_scroller_settings' . $setting_id, $return);
    }


    public function element_pack_smooth_scroller() {
        wp_enqueue_script('bdt-smooth-scroller', BDTEP_URL . 'includes/smooth-scroller/assets/ep-smooth-scroller.js', [], BDTEP_VER, true);
        wp_localize_script(
            'bdt-smooth-scroller',
            'SmoothScroller_Settings',
            [
                'speed'          => $this->ep_smooth_scroller_settings('smooth_scroller_speed'),
                'smoothness'    => $this->ep_smooth_scroller_settings('smooth_scroller_smoothness'),
            ]
        );
    }
}

if (class_exists('ElementPack\Includes\SmoothScroller\SmoothScroller_Loader')) {
    new SmoothScroller_Loader();
}
