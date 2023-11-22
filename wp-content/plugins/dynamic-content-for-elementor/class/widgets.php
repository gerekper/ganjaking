<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Widgets Class
 *
 * Register new elementor widget.
 *
 * @since 0.0.1
 */
class Widgets
{
    public static $widgets_info = [];
    public static $grouped_widgets = [];
    public static $groups;
    public static $namespace = '\\DynamicContentForElementor\\Widgets\\';
    public function __construct()
    {
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories'], 20);
    }
    /**
     * On Widgets Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function on_widgets_registered()
    {
        $this->register_widgets();
    }
    /**
     * Register Widgets
     *
     * @since 0.5.0
     *
     * @access private
     */
    public function register_widgets()
    {
        $widgets = \DynamicContentForElementor\Plugin::instance()->features->filter(['type' => 'widget', 'status' => 'active']);
        foreach ($widgets as $widget_info) {
            if (\DynamicContentForElementor\Helper::check_plugin_dependencies(\false, $widget_info['plugin_depends']) && (!isset($widget_info['minimum_php']) || isset($widget_info['minimum_php']) && \version_compare(\phpversion(), $widget_info['minimum_php'], '>='))) {
                $widget_class = '\\DynamicContentForElementor\\' . $widget_info['class'];
                /**
                 * @var \Elementor\Widget_Base $widget_object;
                 */
                $widget_object = new $widget_class();
                if (\method_exists($widget_object, 'run_once')) {
                    $widget_object->run_once();
                }
                \Elementor\Plugin::instance()->widgets_manager->register($widget_object);
            }
        }
    }
    /**
     * Add Elementor categories
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function add_elementor_widget_categories($elements)
    {
        // Default category for widgets without a category
        $elements->add_category('dynamic-content-for-elementor', array('title' => DCE_PRODUCT_NAME));
        $groups = \DynamicContentForElementor\Plugin::instance()->features->get_widgets_groups();
        // Add categories
        foreach ($groups as $group_key => $group_name) {
            $elements->add_category('dynamic-content-for-elementor-' . \strtolower($group_key), array('title' => DCE_PRODUCT_NAME . ' - ' . $group_name));
        }
    }
}
