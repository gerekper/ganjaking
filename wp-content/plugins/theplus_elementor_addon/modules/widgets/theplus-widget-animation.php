<?php
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$this->start_controls_section(
    'section_animation_styling',
    [
        'label'=>esc_html__('On Scroll View Animation', 'theplus'),
        'tab'=>Controls_Manager::TAB_STYLE,
    ]
);
$this->add_control(
    'animation_effects',
    [
        'label'=>esc_html__('In Animation Effect', 'theplus'),
        'type'=>Controls_Manager::SELECT,
        'default'=>'no-animation',
        'options'=>theplus_get_animation_options(),
    ]
);
$this->add_control(
    'animation_delay',
    [
        'type'=>Controls_Manager::SLIDER,
        'label'=>esc_html__('Animation Delay', 'theplus'),
        'default'=>[
            'unit'=>'',
            'size'=>50,
        ],
        'range'=>[
            ''=>[
                'min'=>0,
                'max'=>4000,
                'step'=>15,
            ],
        ],
        'condition'=>[
            'animation_effects!'=>'no-animation',
        ],
    ]
);

if(!empty($Plus_Listing_block) && $Plus_Listing_block == "Plus_Listing_block"){
    $this->add_control(
        'animated_column_list',
        [
            'label'=>esc_html__('List Load Animation','theplus'),
            'type'=>Controls_Manager::SELECT,
            'default'=>'',
            'options'=>[
                ''=> esc_html__('Content Animation Block','theplus'),
                'stagger'=>esc_html__('Stagger Based Animation','theplus'),
                'columns'=>esc_html__('Columns Based Animation','theplus'),
            ],
            'condition'    => [
                'animation_effects!' => [ 'no-animation' ],
            ],
        ]
    );
    $this->add_control(
        'animation_stagger',
        [
            'type'=>Controls_Manager::SLIDER,
            'label'=>esc_html__('Animation Stagger', 'theplus'),
            'default'=>[
                'unit'=>'',
                'size'=>150,
            ],
            'range'=>[
                '' => [
                    'min'=>0,
                    'max'=>6000,
                    'step'=>10,
                ],
            ],				
            'condition'=>[
                'animation_effects!'=>['no-animation'],
                'animated_column_list'=>'stagger',
            ],
        ]
    );
}

$this->add_control(
    'animation_duration_default',
    [
        'label' => esc_html__( 'Animation Duration', 'theplus' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => 'no',
        'condition' => [
            'animation_effects!' => 'no-animation',
        ],
    ]
);
$this->add_control(
    'animate_duration',
    [
        'type'=>Controls_Manager::SLIDER,
        'label'=>esc_html__('Duration Speed', 'theplus'),
        'default'=>[
            'unit'=>'px',
            'size'=>50,
        ],
        'range'=>[
            'px'=>[
                'min'=>100,
                'max'=>10000,
                'step'=>100,
            ],
        ],
        'condition' => [
            'animation_effects!'=>'no-animation',
            'animation_duration_default'=>'yes',
        ],
    ]
);
$this->add_control(
    'animation_out_effects',
    [
        'label'=>esc_html__( 'Out Animation Effect', 'theplus' ),
        'type'=>Controls_Manager::SELECT,
        'default'=>'no-animation',
        'options'=>theplus_get_out_animation_options(),
        'separator'=>'before',
        'condition'=>[
            'animation_effects!'=>'no-animation',
        ],
    ]
);
$this->add_control(
    'animation_out_delay',
    [
        'type'=>Controls_Manager::SLIDER,
        'label'=>esc_html__('Out Animation Delay', 'theplus'),
        'default'=>[
            'unit'=>'',
            'size'=>50,
        ],
        'range'=>[
            ''=>[
                'min'=>0,
                'max'=>4000,
                'step'=>15,
            ],
        ],
        'condition' => [
            'animation_effects!'=>'no-animation',
            'animation_out_effects!'=>'no-animation',
        ],
    ]
);
$this->add_control(
    'animation_out_duration_default',
    [
        'label'=>esc_html__('Out Animation Duration','theplus'),
        'type'=>Controls_Manager::SWITCHER,
        'default'=>'no',
        'condition'=>[
            'animation_effects!'=>'no-animation',
            'animation_out_effects!'=>'no-animation',
        ],
    ]
);
$this->add_control(
    'animation_out_duration',
    [
        'type'=>Controls_Manager::SLIDER,
        'label'=>esc_html__('Duration Speed', 'theplus'),
        'default'=>[
            'unit'=>'px',
            'size'=>50,
        ],
        'range'=>[
            'px'=>[
                'min'=>100,
                'max'=>10000,
                'step'=>100,
            ],
        ],
        'condition'=>[
            'animation_effects!'=>'no-animation',
            'animation_out_effects!'=>'no-animation',
            'animation_out_duration_default'=>'yes',
        ],
    ]
);
$this->end_controls_section();
