<?php

namespace ElementPack\Modules\Stacker\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;



if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Stacker extends Module_Base {

    public function get_name()
    {
        return 'bdt-stacker';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('Stacker', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-stacker';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['stack', 'stacker', 'scroll', 'scroller'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-stacker'];
        }
    }

    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['gsap', 'scroll-trigger-js', 'ep-scripts'];
        } else {
            return ['gsap', 'scroll-trigger-js', 'ep-stacker'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/fZSTyJc5W7E?si=GkkUhdv9aXPTlVxS';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Stacker', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'stacker_section_id',
            [
                'label'       => esc_html__('ID', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'description' => esc_html__( "Just write that's section or Container ID here such 'my-section'. N.B: No need to add '#'.", 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'stacker_section_list',
            [
                'label'       => __('Section or Container IDs', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ stacker_section_id }}}',
                'frontend_available' => true,
                'render_type'        => 'none',
                'prevent_empty' => false,
            ]
        );
        $this->add_control(
            'stacker_scroller_start',
            [
                'label' => esc_html__('Scroller Start', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'separator' => 'before',
                'frontend_available' => true,
                'render_type'        => 'none',
                'default' => [
                    'unit' => '%',
                    'size' => 10,
                ],
            ]
        );
       
        $this->add_control(
            'stacker_stacking_space',
            [
                'label' => esc_html__('Staking Space', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'frontend_available' => true,
                'render_type'        => 'none',
            ]
        );

        $this->add_control(
            'stacker_spacing',
            [
                'label' => esc_html__('Bottom Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-stacker .elementor-top-section, {{WRAPPER}} .bdt-ep-stacker .e-con' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this-> add_control(
            'stacker_stacking_opacity',
            [
                'label'         => __( 'Transparent on Scroll', 'bdthemes-element-pack' ),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => __( 'Yes', 'bdthemes-element-pack' ),
                'label_off'     => __( 'No', 'bdthemes-element-pack' ),
                'separator'     => 'before',
                'return_value'  => 'yes',
                'default'       => 'no',
                'frontend_available' => true,
                'render_type'        => 'none',
            ]
        );

        $this->add_control(
            'ignore_element_notes',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => esc_html__('Note: This widget won\'t function at the editor mode at all. It will work just fine on frontend perspective.', 'bdthemes-element-pack'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'separator'       => 'before',
            ]
        );
        $this->end_controls_section();
       
    }

    public function render(){?>
        <div class="bdt-ep-stacker"></div>
<?php
    }
}
