<?php

namespace ElementPack\Includes\SmoothScroller;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Tab_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Settings_Contorls extends Tab_Base {



    public function get_id() {
        return 'ep-smooth-scroller';
    }
    public function get_title() {
        return __('Smooth Scroller', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-smooth-scroller bdt-new';
    }
    protected function register_tab_controls() {
        $this->start_controls_section(
            'smooth_scroller_setting',
            [
                'label' => esc_html__('Smooth Scroller', 'bdthemes-element-pack'),
                'tab'   => 'ep-smooth-scroller',
            ]
        );
        $this->add_control(
            'smooth_scroller_speed',
            [
                'label'         => esc_html__('Speed', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::NUMBER,
                'min'           => 10,
                'max'           => 200,
                'step'          => 1,
                'default'       => 120,
                'dynamic'       => ['active' => true],
            ]
        );

        $this->add_control(
            'smooth_scroller_smoothness',
            [
                'label'         => esc_html__('Smoothness', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::NUMBER,
                'min'           => 0,
                'max'           => 100,
                'step'          => 1,
                'default'       => 12,
                'dynamic'       => ['active' => true],
            ]
        );
        $this->end_controls_section();
    }
}
