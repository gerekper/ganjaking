<?php

namespace ElementPack\Includes;

use ElementPack\Admin\ModuleService;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Pro_Widget_Map {

    public function get_pro_widget_map() {

        $arr_obj = ModuleService::get_widget_settings(
            function ($settings) {
                $core_widgets        = $settings['settings_fields']['element_pack_active_modules'];

                $arr = [];

                foreach ($core_widgets as $key => $widget) {

                    if ('pro' == $widget['widget_type']) {

                        $ar = [
                            'categories' => ['element-pack-pro'],
                            'name'       => $widget['name'],
                            'title'      => $widget['label'],
                            'icon'       => 'bdt-wi-' . $widget['name'] . ' bdt-pro-unlock-icon',
                            'action_button' => [
                                'classes'   => ['elementor-button', 'elementor-button-success'],
                                'text'      => esc_html__('See it in Action', 'bdthemes-element-pack'),
                                'url'       => esc_url($widget['demo_url'])
                            ]
                        ];

                        array_push($arr, $ar);
                    }
                }

                return $arr;
            }
        );

        return $arr_obj;
    }
}
