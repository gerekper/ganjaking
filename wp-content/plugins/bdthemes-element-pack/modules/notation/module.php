<?php

namespace ElementPack\Modules\Notation;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-notation';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'section_element_pack_notation_controls',
            [
                'label' => BDTEP_CP . esc_html__('Notation', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $element->end_controls_section();
    }


    public function register_controls($widget, $args) {

        $widget->add_control(
            'ep_notation_active',
            [
                'label'              => esc_html__('Notation Effects', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SWITCHER,
                'render_type'        => 'template',
                'frontend_available' => true,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'ep_notation_select_type',
            [
                'label'   => esc_html__('Element Type', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'widget',
                'options' => [
                    'widget' => esc_html__('Widget', 'bdthemes-element-pack'),
                    'custom' => esc_html__('Widget > Custom Selector', 'bdthemes-element-pack'),
                ],
            ]
        );

        $repeater->add_control(
            'ep_notation_custom_selector',
            [
                'label'       => esc_html__('Custom Selector', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__('Please use ID or Class to select your element/elements. ( Example - #select-id, .select-class)', 'bdthemes-element-pack'),
                'condition'   => [
                    'ep_notation_select_type' => 'custom',
                ],
            ]
        );
        $repeater->add_control(
            'ep_notation_type',
            [
                'label'   => esc_html__('Select Style', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'underline',
                'options' => [
                    'underline'      => esc_html__('Underline', 'bdthemes-element-pack'),
                    'box'            => esc_html__('Box', 'bdthemes-element-pack'),
                    'circle'         => esc_html__('Circle', 'bdthemes-element-pack'),
                    'highlight'      => esc_html__('Highlight', 'bdthemes-element-pack'),
                    'strike-through' => esc_html__('Strike-through', 'bdthemes-element-pack'),
                    'crossed-off'    => esc_html__('Crossed-off', 'bdthemes-element-pack'),
                    'bracket'        => esc_html__('Bracket', 'bdthemes-element-pack'),
                ],
            ]
        );
        $repeater->add_control(
            'ep_notation_bracket_on',
            [
                'label'       => esc_html__('Bracket On', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__('Value could be a string. Each string being one of these values: left, right, top, bottom. When drawing a bracket, this configures which side(s) of the element to bracket. Default value is left,right', 'bdthemes-element-pack'),
                'default'     => 'left,right',
                'condition'   => [
                    'ep_notation_type' => 'bracket',
                ],
            ]
        );
        $repeater->add_control(
            'ep_notation_color',
            [
                'label' => esc_html__('Color', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::COLOR,
            ]
        );
        $repeater->add_control(
            'ep_notation_anim_duration',
            [
                'label'   => esc_html__('Animation Duration', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 5000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 800,
                ],
            ]
        );

        $repeater->add_control(
            'ep_notation_stroke_width',
            [
                'label'   => esc_html__('Stroke Width', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
            ]
        );

        $repeater->add_control(
            'ep_notation_waypoint_offset',
            [
                'label'       => esc_html__('Waypoint Offset', 'bdthemes-element-pack') . BDTEP_NC,
                'description' => esc_html__('Example: bottom-in-view, 90%', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'bottom-in-view',
                'separator'   => 'before'
            ]
        );

        $widget->add_control(
            'ep_notation_list',
            [
                'label'              => esc_html__('Notation Items', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::REPEATER,
                'fields'             => $repeater->get_controls(),
                'prevent_empty'      => false,
                'title_field'        => '{{{ ep_notation_select_type }}}',
                'frontend_available' => true,
                'default'            => [
                    [
                        'ep_notation_select_type' => 'widget',
                    ],
                ],
                'condition'          => [
                    'ep_notation_active' => 'yes',
                ],
                'render_type' => 'template',
            ]
        );
    }


    public function enqueue_scripts() {
        wp_enqueue_script('ep-notation');
    }

    public function should_script_enqueue($widget) {
        if ('yes' === $widget->get_settings_for_display('ep_notation_active')) {
            $this->enqueue_scripts();
        }
    }

    protected function add_actions() {

        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/common/section_element_pack_notation_controls/before_section_end', [$this, 'register_controls'], 10, 2);
        // render scripts
        add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
    }
}
