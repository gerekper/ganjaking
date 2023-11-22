<?php

namespace DynamicContentForElementor\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Custom outline group control
 *
 */
class Group_Control_Outline extends Group_Control_Base
{
    protected static $fields;
    public static function get_type()
    {
        return 'outline';
    }
    protected function init_fields()
    {
        $fields = [];
        $fields['outline'] = ['label' => _x('Outline Type', 'Outline Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'solid' => _x('Solid', 'Outline Control', 'dynamic-content-for-elementor'), 'double' => _x('Double', 'Outline Control', 'dynamic-content-for-elementor'), 'dotted' => _x('Dotted', 'Outline Control', 'dynamic-content-for-elementor'), 'dashed' => _x('Dashed', 'Outline Control', 'dynamic-content-for-elementor')], 'selectors' => ['{{SELECTOR}}' => 'outline-style: {{VALUE}};']];
        $fields['width'] = ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px'], 'range' => ['px' => ['min' => 0, 'max' => 30, 'step' => 1]], 'size_units' => ['px'], 'selectors' => ['{{SELECTOR}}' => 'outline-width: {{SIZE}}{{UNIT}};'], 'condition' => ['outline!' => '']];
        $fields['offset'] = ['label' => _x('Offset', 'Outline Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px'], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'size_units' => ['px'], 'selectors' => ['{{SELECTOR}}' => 'outline-offset: {{SIZE}}{{UNIT}};'], 'condition' => ['outline!' => '']];
        $fields['color'] = ['label' => _x('Color', 'Outline Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{SELECTOR}}' => 'outline-color: {{VALUE}};'], 'condition' => ['outline!' => '']];
        return $fields;
    }
    protected function get_default_options()
    {
        return ['popover' => ['starter_title' => _x('Outline', 'Outline Control', 'dynamic-content-for-elementor'), 'starter_name' => 'outline_wgt']];
    }
}
