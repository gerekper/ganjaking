<?php

namespace ElementPack\Admin\AssetMinifier;

if ( !defined('ABSPATH') ) {
    exit;
} // Exit if accessed directly

if ( !class_exists('MatthiasMullie\Minify\Minify') ) {
    require_once 'autoload.php';
}

use ElementPack\Admin\ModuleService;
use MatthiasMullie\Minify;

class Asset_Minifier {

    public function getWidgetIds() {

        return ModuleService::get_widget_settings(function ($settings) {
            $active_widgets     = $settings['settings_fields']['element_pack_active_modules'];
            $elementor_extend   = $settings['settings_fields']['element_pack_elementor_extend'];
            $third_party_widget = $settings['settings_fields']['element_pack_third_party_widget'];

            /**
             * Core Widget
             */
            $options        = get_option('element_pack_active_modules', []);
            $active_widgets = array_reduce($active_widgets, function ($results, $item) use ($options) {
                if(element_pack_is_widget_enabled($item['name'], $options)){
                    $results[] = $item['name'];
                }
                return $results;
            }, []);


            /**
             * Extension
             */
            $options        = get_option('element_pack_elementor_extend', []);
            $elementor_extend = array_reduce($elementor_extend, function ($results, $item) use ($options) {
                if(element_pack_is_extend_enabled($item['name'], $options)){
                    $results[] = $item['name'];
                };
                return $results;
            }, []);


            /**
             * Third Party Widget
             */
            $options        = get_option('element_pack_third_party_widget', []);
            $third_party_widget = array_reduce($third_party_widget, function ($results, $item) use ($options) {
                if(element_pack_is_third_party_enabled($item['name'], $options)){
                    if ( isset($item['plugin_path']) ) {
                        if(ModuleService::is_plugin_active($item['plugin_path'])){
                            $results[] = $item['name'];
                        }
                    }
                };
                return $results;
            }, []);


            return array_merge_recursive($active_widgets, $elementor_extend, $third_party_widget);

        });
    }

    public function getAssets() {
        $widgets = $this->getWidgetIds();

        $scripts   = [];
        $direction = is_rtl() ? '.rtl' : '';

        foreach ( $widgets as $widget ) {
            $jsPath  = BDTEP_PATH . 'assets/js/modules/ep-' . $widget . '.js';
            $cssPath = BDTEP_PATH . 'assets/css/ep-' . $widget . $direction . '.css';

            $script = [];
            if ( file_exists($jsPath) ) {
                $script['js'] = $jsPath;
            }

            if ( file_exists($cssPath) ) {
                $script['css'] = $cssPath;
            }

            if ( $script ) {
                $scripts[] = $script;
            }

        }

        return $scripts;
    }

    protected function getJsPaths() {
        return array_reduce($this->getAssets(), function ($results, $item) {
            if ( isset($item['js']) ) {
                $results[] = $item['js'];
            }
            return $results;
        }, []);
    }

    public function getCssPaths() {
        return array_reduce($this->getAssets(), function ($results, $item) {
            if ( isset($item['css']) ) {
                $results[] = $item['css'];
            }
            return $results;
        }, []);
    }

    public function minifyJs() {
        $scripts = array_merge([
            // global js path goes there
            BDTEP_ASSETS_PATH . 'js/common/helper.js'
        ], $this->getJsPaths());

        $scripts  = apply_filters('elementpack/optimization/assets/scripts', $scripts);
        $minifier = new Minify\JS();

        foreach ( $scripts as $item ) {
            $minifier->add($item);
        }

        $uploads_dir = trailingslashit(wp_upload_dir()['basedir']) . 'element-pack/minified/js';
        wp_mkdir_p($uploads_dir);
        $minifiedPath = "$uploads_dir" . "/ep-scripts.js";
        $minifier->minify($minifiedPath);
    }

    public function minifyCss() {
        $styles = array_merge($this->getCssPaths(), [
            // global js path goes there
            BDTEP_ASSETS_PATH . 'css/ep-font.css'
        ]);

        $styles   = apply_filters('elementpack/optimization/assets/styles', $styles);
        $minifier = new Minify\CSS();

        foreach ( $styles as $item ) {
            $minifier->add($item);
        }

        $uploads_dir = trailingslashit(wp_upload_dir()['basedir']) . 'element-pack/minified/css';
        wp_mkdir_p($uploads_dir);
        $minifiedPath = "$uploads_dir" . "/ep-styles.css";
        $minifier->minify($minifiedPath);
    }

}