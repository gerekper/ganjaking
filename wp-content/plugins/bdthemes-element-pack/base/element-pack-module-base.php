<?php

namespace ElementPack\Base;

use Elementor\Core\Base\Module;
use Elementor\Plugin;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Element_Pack_Module_Base extends Module {

    public function get_widgets() {
        return [];
    }

    public function __construct() {
        add_action('elementor/widgets/register', [$this, 'init_widgets']);
    }


    public function init_widgets() {

        $widget_manager = Element_Pack_Loader::elementor()->widgets_manager;

        foreach ($this->get_widgets() as $widget) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Widgets\\' . $widget;

            //var_dump($class_name);

            $widget_manager->register(new $class_name());
        }
    }

    private function find_element_recursive($elements, $form_id) {

        foreach ($elements as $element) {
            if ($form_id === $element['id']) {
                return $element;
            }

            if (!empty($element['elements'])) {
                $element = $this->find_element_recursive($element['elements'], $form_id);

                if ($element) {
                    return $element;
                }
            }
        }

        return false;
    }

    /**
     * @param $post_id | page id or post id
     * @param $widget_id | elementor widget ids
     */
    public function get_widget_settings($post_id, $widget_id) {
        if (!$post_id || !$widget_id) {
            return "Invalid request";
        }

        $elementor = Plugin::$instance;
        $pageMeta  = $elementor->documents->get($post_id);

        if (!$pageMeta) {
            return "Invalid Post or Page ID";
        }
        $metaData = $pageMeta->get_elements_data();
        if (!$metaData) {
            return "Page page is not under elementor";
        }

        $widget_data = $this->find_element_recursive($metaData, $widget_id);
        $settings    = [];


        if (is_array($widget_data)) {
            $widget   = $elementor->elements_manager->create_element_instance($widget_data);
            $settings = $widget->get_settings();
        }

        return $settings;
    }
}
