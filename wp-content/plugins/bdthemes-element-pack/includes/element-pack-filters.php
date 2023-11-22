<?php

/**
 * Element Pack widget filters
 * @since 5.7.4
 */

use ElementPack\Admin\ModuleService;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Settings Filters
if (!function_exists('ep_is_dashboard_enabled')) {
    function ep_is_dashboard_enabled() {
        return apply_filters('elementpack/settings/dashboard', true);
    }
}

if (!function_exists('element_pack_is_widget_enabled')) {
    function element_pack_is_widget_enabled($widget_id, $options = []) {

        if(!$options){
            $options = get_option('element_pack_active_modules', []);
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $widget_id = str_replace('-','_', $widget_id);
            return apply_filters("elementpack/widget/{$widget_id}", true);
        }
    }
}

if (!function_exists('element_pack_is_extend_enabled')) {
    function element_pack_is_extend_enabled($widget_id, $options = []) {

        if(!$options){
            $options = get_option('element_pack_elementor_extend', []);
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $widget_id = str_replace('-','_', $widget_id);
            return apply_filters("elementpack/extend/{$widget_id}", true);
        }
    }
}

if (!function_exists('element_pack_is_third_party_enabled')) {
    function element_pack_is_third_party_enabled($widget_id, $options = []) {

        if(!$options){
            $options = get_option('element_pack_third_party_widget', []);
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $widget_id = str_replace('-','_', $widget_id);
            return apply_filters("elementpack/widget/{$widget_id}", true);
        }
    }
}

if (!function_exists('element_pack_is_asset_optimization_enabled')) {
    function element_pack_is_asset_optimization_enabled() {
        $asset_manager = element_pack_option('asset-manager', 'element_pack_other_settings', 'off');
        if( $asset_manager == 'on'){
            return apply_filters("elementpack/optimization/asset_manager", true);
        }
    }
}


