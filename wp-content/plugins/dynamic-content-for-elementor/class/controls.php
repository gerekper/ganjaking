<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class Controls
{
    public $controls = [];
    public $group_controls = [];
    public static $namespace = '\\DynamicContentForElementor\\Controls\\';
    public function __construct()
    {
        $this->controls = $this->get_controls();
        $this->group_controls = $this->get_group_controls();
    }
    public function get_controls()
    {
        $controls['images_selector'] = 'Control_Images_Selector';
        $controls['ooo_query'] = 'Control_OOO_Query';
        $controls['transforms'] = 'Control_Transforms';
        $controls['xy_movement'] = 'Control_XY_Movement';
        $controls['xy_positions'] = 'Control_XY_Positions';
        return $controls;
    }
    public function get_group_controls()
    {
        $group_controls['ajax_page'] = 'Group_Control_Ajax_Page';
        $group_controls['animation_element'] = 'Group_Control_Animation_Element';
        $group_controls['filters_css'] = 'Group_Control_Filters_CSS';
        $group_controls['filters_hsb'] = 'Group_Control_Filters_HSB';
        $group_controls['outline'] = 'Group_Control_Outline';
        $group_controls['transform_element'] = 'Group_Control_Transform_Element';
        return $group_controls;
    }
    /**
     * On Controls Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function on_controls_registered()
    {
        $this->register_controls();
    }
    /**
     * On Controls Registered
     *
     * @since 1.0.4
     *
     * @access public
     */
    public function register_controls()
    {
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        foreach ($this->controls as $key => $name) {
            $class = self::$namespace . $name;
            /**
             * @var \Elementor\Base_Control $new_class
             */
            $new_class = new $class();
            $controls_manager->register($new_class);
        }
        foreach ($this->group_controls as $key => $name) {
            $class = self::$namespace . $name;
            $controls_manager->add_group_control($class::get_type(), new $class());
        }
    }
}
