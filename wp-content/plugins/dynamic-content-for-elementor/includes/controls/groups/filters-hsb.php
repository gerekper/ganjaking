<?php

namespace DynamicContentForElementor\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Custom Filters-HSB group control
 *
 */
class Group_Control_Filters_HSB extends Group_Control_Base
{
    protected static $fields;
    public static function get_type()
    {
        return 'filters-hsb';
    }
    protected function init_fields()
    {
        $controls = [];
        $controls['filter_type'] = ['type' => Controls_Manager::HIDDEN, 'default' => 'custom'];
        $controls['hue'] = ['label' => _x('Hue', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 360]], 'separator' => 'none', 'selectors' => ['{{SELECTOR}}' => 'filter: hue-rotate( {{hue.SIZE}}deg) saturate( {{saturate.SIZE}}% ) brightness( {{brightness.SIZE}}% );'], 'condition' => ['filter_type' => 'custom']];
        $controls['saturate'] = ['label' => _x('Saturation', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 100], 'range' => ['px' => ['min' => 0, 'max' => 200]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        $controls['brightness'] = ['label' => _x('Brightness', 'Filter Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'render_type' => 'ui', 'required' => 'true', 'default' => ['size' => 100], 'range' => ['px' => ['min' => 0, 'max' => 200]], 'separator' => 'none', 'condition' => ['filter_type' => 'custom']];
        return $controls;
    }
    protected function prepare_fields($fields)
    {
        \array_walk($fields, function (&$field, $field_name) {
            if (\in_array($field_name, ['filter_hsb', 'popover_toggle'])) {
                return;
            }
            $field['condition'] = ['filter_hsb' => 'custom'];
        });
        return parent::prepare_fields($fields);
    }
    /**
     * @since 0.5.0
     * @access protected
     */
    protected function get_default_options()
    {
        return ['popover' => ['starter_title' => _x('Filters HSB', 'Filters HSB Control', 'dynamic-content-for-elementor'), 'starter_name' => 'filter_hsb']];
    }
}
