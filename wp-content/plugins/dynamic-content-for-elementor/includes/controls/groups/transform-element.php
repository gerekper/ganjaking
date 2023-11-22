<?php

namespace DynamicContentForElementor\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Custom transform-element group control
 *
 */
class Group_Control_Transform_Element extends Group_Control_Base
{
    protected static $fields;
    public static function get_type()
    {
        return 'transform-element';
    }
    protected function init_fields()
    {
        $controls = [];
        $controls['transform_type'] = ['type' => Controls_Manager::HIDDEN, 'default' => 'custom'];
        $controls['transform'] = ['label' => _x('Transformations', 'Transform Control', 'dynamic-content-for-elementor'), 'type' => 'transforms', 'responsive' => \true, 'render_type' => 'ui', 'default' => ['angle' => 0, 'rotate_x' => 0, 'rotate_y' => 0, 'translate_x' => 0, 'translate_y' => 0, 'translate_z' => 0, 'scale' => 1], 'condition' => ['transform_type' => 'custom'], 'selectors' => ['{{SELECTOR}} > *:first-child' => 'transform: rotateZ({{ANGLE}}deg) rotateX({{ROTATE_X}}deg) rotateY({{ROTATE_Y}}deg) scale({{SCALE}}) translateX({{TRANSLATE_X}}px) translateY({{TRANSLATE_Y}}px) translateZ({{TRANSLATE_Z}}px);']];
        $controls['transform-origin'] = ['label' => _x('Transform origin', 'Transform-origin x/y Control', 'dynamic-content-for-elementor'), 'type' => 'xy_positions', 'responsive' => \true, 'render_type' => 'ui', 'condition' => ['transform_type' => 'custom'], 'selectors' => ['{{SELECTOR}} > *:first-child' => 'transform-origin: {{X}}% {{Y}}%; -webkit-transform-origin: {{X}}% {{Y}}%;']];
        $controls['perspective_hr'] = ['type' => Controls_Manager::DIVIDER, 'style' => 'thick'];
        $controls['perspective'] = ['label' => _x('Perspective', 'Perspective Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'responsive' => \true, 'render_type' => 'ui', 'default' => ['size' => ''], 'size_units' => ['px'], 'range' => ['px' => ['max' => 1200, 'min' => 0, 'step' => 1]], 'selectors' => ['{{SELECTOR}}' => 'perspective: {{SIZE}}{{UNIT}}; -webkit-perspective: {{SIZE}}{{UNIT}};']];
        $controls['perspective-origin'] = ['label' => _x('Perspective origin', 'Perspective-origin x/y Control', 'dynamic-content-for-elementor'), 'type' => 'xy_positions', 'responsive' => \true, 'condition' => ['transform_type' => 'custom'], 'selectors' => ['{{SELECTOR}} > *:first-child' => 'perspective-origin: {{X}}% {{Y}}%; -webkit-perspective-origin:: {{X}}% {{Y}}%;']];
        return $controls;
    }
    protected function prepare_fields($fields)
    {
        \array_walk($fields, function (&$field, $field_name) {
            if (\in_array($field_name, ['transform_element', 'popover_toggle'])) {
                return;
            }
        });
        return parent::prepare_fields($fields);
    }
    protected function get_default_options()
    {
        return ['popover' => \false];
    }
}
