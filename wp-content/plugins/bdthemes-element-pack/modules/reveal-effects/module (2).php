<?php

namespace ElementPack\Modules\RevealEffects;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
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
        return 'bdt-reveal-effects';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'section_element_pack_reveal_controls',
            [
                'tab'   => Controls_Manager::TAB_ADVANCED,
                'label' => BDTEP_CP . esc_html__('Reveal Effects', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );
        $element->end_controls_section();
    }


    public function register_controls($widget, $args) {

        $widget->add_control(
            'element_pack_reveal_effects_enable',
            [
                'label'              => esc_html__('Use Reveal Effects?', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SWITCHER,
                'render_type'        => 'none',
                'frontend_available' => true,
                'prefix_class'       => 'bdt-reveal-preload bdt-reveal-effects-',
            ]
        );
        $widget->add_control(
            'element_pack_reveal_effects_direction',
            [
                'label'              => __('Direction', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'lr',
                'options'            => [
                    'lr' => __('Left to Right', 'bdthemes-element-pack'),
                    'rl' => __('Right to Left', 'bdthemes-element-pack'),
                    'c'  => __('Center', 'bdthemes-element-pack'),
                    'tb' => __('Top to Bottom', 'bdthemes-element-pack'),
                    'bt' => __('Bottom to top', 'bdthemes-element-pack')
                ],
                'frontend_available' => true,
                'render_type'        => 'template',
                'condition'          => [
                    'element_pack_reveal_effects_enable' => 'yes'
                ]
            ]
        );
        $widget->add_control(
            'element_pack_reveal_effects_easing',
            [
                'label'              => __('Easing', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'easeOutQuint',
                'render_type'        => 'template',
                'options'            => [
                    'easeOutQuad'     => esc_html__('Ease Out Quad', 'bdthemes-element-pack'),
                    'easeOutCubic'    => esc_html__('Ease Out Cubic', 'bdthemes-element-pack'),
                    'easeOutQuart'    => esc_html__('Ease Out Quart', 'bdthemes-element-pack'),
                    'easeOutQuint'    => esc_html__('Ease Out Quint', 'bdthemes-element-pack'),
                    'easeOutSine'     => esc_html__('Ease Out Sine', 'bdthemes-element-pack'),
                    'easeOutExpo'     => esc_html__('Ease Out Expo', 'bdthemes-element-pack'),
                    'easeOutCirc'     => esc_html__('Ease Out Circ', 'bdthemes-element-pack'),
                    'easeOutBack'     => esc_html__('Ease Out Back', 'bdthemes-element-pack'),
                    'easeOutBounce'   => esc_html__('Ease Out Bounce', 'bdthemes-element-pack'),
                    'easeOutInQuad'   => esc_html__('Ease Out In Quad', 'bdthemes-element-pack'),
                    'easeOutInCubic'  => esc_html__('Ease Out In Cubic', 'bdthemes-element-pack'),
                    'easeOutInQuart'  => esc_html__('Ease Out In Quart', 'bdthemes-element-pack'),
                    'easeOutInQuint'  => esc_html__('Ease Out In Quint', 'bdthemes-element-pack'),
                    'easeOutInSine'   => esc_html__('Ease Out In Sine', 'bdthemes-element-pack'),
                    'easeOutInExpo'   => esc_html__('Ease Out In Expo', 'bdthemes-element-pack'),
                    'easeOutInCirc'   => esc_html__('Ease Out In Circ', 'bdthemes-element-pack'),
                    'easeOutInBack'   => esc_html__('Ease Out In Back', 'bdthemes-element-pack'),
                    'easeOutInBounce' => esc_html__('Ease Out In Bounce', 'bdthemes-element-pack'),
                ],
                'frontend_available' => true,
                'condition'          => [
                    'element_pack_reveal_effects_enable' => 'yes'
                ]
            ]
        );
        $widget->add_control(
            'element_pack_reveal_effects_speed',
            [
                'label'              => __('Speed', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SLIDER,
                'size_units'         => ['px'],
                'range'              => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 10,
                        'step' => 0.1,
                    ],

                ],
                'default'            => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'frontend_available' => true,
                'render_type'        => 'template',
                'condition'          => [
                    'element_pack_reveal_effects_enable' => 'yes'
                ]
            ]
        );
        $widget->add_control(
            'element_pack_reveal_effects_color',
            [
                'label'              => __('Background', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::COLOR,
                'frontend_available' => true,
                'render_type'        => 'template',
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'condition'          => [
                    'element_pack_reveal_effects_enable' => 'yes'
                ]
            ]
        );
    }

    public function reveal_effects_render($widget) {
        if ('yes' === $widget->get_settings_for_display('element_pack_reveal_effects_enable')) {
            $widget->add_render_attribute('_wrapper', 'style', '--ep-reveal-effects-init: 0;');
            $widget->add_render_attribute('_wrapper', 'data-nnn', $widget->get_settings_for_display('element_pack_reveal_effects_color'));
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script('anime', BDTEP_ASSETS_URL . 'vendor/js/anime.min.js', [], '3.2.1', true);
        wp_enqueue_script('revealFx', BDTEP_ASSETS_URL . 'vendor/js/RevealFx.min.js', ['anime'], '0.0.2', true);
    }

    public function should_script_enqueue($widget) {
        if ('yes' === $widget->get_settings_for_display('element_pack_reveal_effects_enable')) {
            $this->enqueue_scripts();
            wp_enqueue_style('ep-reveal-effects');
            wp_enqueue_script('ep-reveal-effects');
        }
    }

    protected function add_actions() {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/common/section_element_pack_reveal_controls/before_section_end', [$this, 'register_controls'], 10, 2);

        add_action('elementor/element/after_add_attributes', [$this, 'reveal_effects_render'], 10, 1);
        add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
    }
}
