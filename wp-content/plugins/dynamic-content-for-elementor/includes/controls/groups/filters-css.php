<?php

namespace DynamicContentForElementor\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Custom Filters-CSS group control
 *
 */
class Group_Control_Filters_CSS extends Group_Control_Base
{
    protected static $fields;
    public static function get_type()
    {
        return 'filters-css';
    }
    protected function init_fields()
    {
        $controls = [];
        $controls['filter_type'] = ['type' => Controls_Manager::HIDDEN, 'default' => 'custom'];
        $controls['blur'] = ['label' => _x('Blur', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'default' => ['size' => 0], 'selectors' => ['{{SELECTOR}}' => 'filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) sepia( {{sepia.SIZE}} ) blur( {{blur.SIZE}}px ) invert( {{invert.SIZE}}%) hue-rotate( {{huerotate.SIZE}}deg) saturate( {{saturate.SIZE}}% )'], 'condition' => ['filter_type' => 'custom']];
        $controls['brightness'] = ['label' => _x('Brightness', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 100], 'range' => ['px' => ['min' => 0, 'max' => 200]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        $controls['contrast'] = ['label' => _x('Contrast', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 100], 'range' => ['px' => ['min' => 0, 'max' => 200]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        $controls['saturate'] = ['label' => _x('Saturation', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 100], 'range' => ['px' => ['min' => 0, 'max' => 200]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        $controls['huerotate'] = ['label' => _x('HueRotate', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 360]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        $controls['sepia'] = ['label' => _x('Sepia', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 0], 'range' => ['%' => ['min' => 0, 'max' => 1]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        $controls['invert'] = ['label' => _x('Invert', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 0], 'range' => ['%' => ['min' => 0, 'max' => 100]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        return $controls;
    }
    protected function prepare_fields($fields)
    {
        \array_walk($fields, function (&$field, $field_name) {
            if (\in_array($field_name, ['filter_css', 'popover_toggle'])) {
                return;
            }
            $field['condition'] = ['filter_css' => 'custom'];
        });
        return parent::prepare_fields($fields);
    }
    protected function get_default_options()
    {
        return ['popover' => ['starter_title' => _x('Filters CSS', 'Filters CSS Control', 'dynamic-content-for-elementor'), 'starter_name' => 'filter_css']];
    }
}
