<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Image_Size;

class Morphing_Blob extends Widget_Base
{

    public function get_name()
    {
        return "jltma-morphing-blob";
    }

    public function get_title()
    {
        return esc_html__('MA Morphing & Blob', MELA_TD);
    }

    public function get_icon()
    {
        return 'ma-el-icon eicon-youtube';
    }

    public function get_categories()
    {
        return ['master-addons'];
    }

    // public function get_script_depends()
    // {
    //     return ['jltma_mrbl_elparticle'];
    // }

    public function get_keywords()
    {
        return [
            'morphing',
            'animation',
            'svg animation',
            'blob',
            'blob animation',
            'morphing animation',
        ];
    }

    public function get_help_url()
    {
        return '';
    }

    protected function _register_controls()
    {
        $this->start_controls_section('general_section', [
            'label' => esc_html__('General', MELA_TD),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        // $this->add_control('jltma_mrbl_type', [
        //     'type'          => Controls_Manager::SELECT,
        //     'label'         => esc_html__('Enable Type of canvas', MELA_TD),
        //     'default'       => 'particles',
        //     'options'       => [
        //         'particles'     =>  esc_html__('Particles', MELA_TD),
        //         'video'         =>  esc_html__('Video', MELA_TD),
        //         'masksvg'       =>  esc_html__('Animated SVG', MELA_TD),
        //     ],
        // ]);
        $this->add_control(
            'jltma_mrbl_particle_json',
            array(
                'label'   => esc_html__('Particle json content', MELA_TD),
                'description' => 'Configure it on <a href="https://vincentgarreau.com/particles.js/" target="_blank">Particle site</a>, download json file and copy content of file to this area',
                'type'    => Controls_Manager::TEXTAREA,
                'condition' => array(
                    'jltma_mrbl_type' => 'particles',
                ),
            )
        );
        $this->add_control('jltma_mrbl_vid_mp4', [
            'label' => esc_html__('Mp4 video link', MELA_TD),
            'label_block'  => true,
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
        ]);
        $this->add_control('jltma_mrbl_vid_webm', [
            'label' => esc_html__('Webm video link', MELA_TD),
            'label_block'  => true,
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
        ]);
        $this->add_control('jltma_mrbl_vid_ogv', [
            'label' => esc_html__('Ogv video link', MELA_TD),
            'label_block'  => true,
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
        ]);
        $this->add_control('jltma_mrbl_vid_poster', [
            'label' => esc_html__('Upload poster', MELA_TD),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
            'label_block'  => true,
        ]);
        $this->add_control(
            'jltma_mrbl_vid_breakpoint',
            array(
                'label'   => esc_html__('Breakpoint', MELA_TD),
                'description' => esc_html__('Video will be replaced by Fallback image after if window width less than this breakpoint', MELA_TD),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 300,
                'max'     => 2500,
                'step'    => 1,
                'default' => 1200,
                'condition' => array(
                    'jltma_mrbl_type' => 'video',
                ),
            )
        );
        $this->add_responsive_control('jltma_mrbl_vid_fallback', [
            'label' => esc_html__('Upload fallback image', MELA_TD),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
            'label_block'  => true,
        ]);
        $this->add_control(
            'tensionPoints',
            [
                'label' => __('Curve Tension', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 2,
                ],
                'label_block' => true,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_control(
            'numPoints',
            [
                'label' => __('Num Points', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'label_block' => true,
                'range' => [
                    'px' => [
                        'min' => 3,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_control(
            'minmaxRadius',
            [
                'label' => __('Min Max Radius', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'sizes' => [
                        'start' => 140,
                        'end' => 160,
                    ],
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 600,
                        'step' => 1,
                    ],
                ],
                'labels' => [
                    __('Min', MELA_TD),
                    __('Max', MELA_TD),
                ],
                'scales' => 0,
                'handles' => 'range',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_control(
            'minmaxDuration',
            [
                'label' => __('Min Max Duration', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'sizes' => [
                        'start' => 5,
                        'end' => 6,
                    ],
                    'unit' => 's',
                ],
                'range' => [
                    's' => [
                        'min' => 0.1,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'labels' => [
                    __('Min', MELA_TD),
                    __('Max', MELA_TD),
                ],
                'scales' => 0,
                'handles' => 'range',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_responsive_control(
            'svgarea_size',
            [
                'label' => __('Svg Size', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 2000,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rh-svg-blob' => 'width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->add_responsive_control(
            'svg_size',
            [
                'label' => __('Image Size', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 2000,
                    ],
                ],
                'condition' => [
                    'svg_image[id]!' => '',
                    'jltma_mrbl_type' => 'masksvg',
                ],

            ]
        );
        $this->add_control(
            'svgfilltype',
            [
                'label' => __('Fill with', MELA_TD),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'color' => __('Color', MELA_TD),
                    'image' => __('Image', MELA_TD),
                    'gradient' => __('Gradient', MELA_TD),
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'fill_color',
            [
                'label' => __('Default Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'default' => '#FF0000',
                'alpha' => false,
                'condition' => [
                    'svgfilltype' => 'color',
                    'jltma_mrbl_type' => 'masksvg',
                ],

            ]
        );
        $this->add_control(
            'svg_image',
            [
                'label' => __('Image', MELA_TD),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],

                'show_label' => false,
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'image'
                ],
            ]
        );
        $this->add_control(
            'svgimage_x',
            [
                'label' => __('Translate X', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '0',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                //'render_type' => 'ui',
                'label_block' => false,
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'image'
                ],
            ]
        );
        $this->add_control(
            'svgimage_y',
            [
                'label' => __('Translate Y', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '0',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                //'render_type' => 'ui',
                'label_block' => false,
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'image'
                ],
            ]
        );
        $this->add_control(
            'gradientx1',
            [
                'label' => __('X1 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => '%',
                ],
                'label_block' => true,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradientx2',
            [
                'label' => __('X2 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'label_block' => true,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradienty1',
            [
                'label' => __('Y1 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => '%',
                ],
                'label_block' => true,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradienty2',
            [
                'label' => __('Y2 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'label_block' => true,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradientcolor1',
            [
                'label' => __('Color 1', MELA_TD),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff0000',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradientcolor2',
            [
                'label' => __('Color 2', MELA_TD),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0000ff',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );

        $this->add_responsive_control(
            'rhandwidth',
            [
                'label' => __('Area width', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 100,
                        'max' => 2500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma_mrbl_and_canvas' => 'width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->add_responsive_control(
            'rhandheight',
            [
                'label' => __('Area height', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 100,
                        'max' => 2500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma_mrbl_and_canvas' => 'height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->add_control('jltma_mrbl_vid_mp4', [
            'label' => esc_html__('Mp4 video link', MELA_TD),
            'label_block'  => true,
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
        ]);
        $this->add_control('jltma_mrbl_vid_webm', [
            'label' => esc_html__('Webm video link', MELA_TD),
            'label_block'  => true,
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
        ]);
        $this->add_control('jltma_mrbl_vid_ogv', [
            'label' => esc_html__('Ogv video link', MELA_TD),
            'label_block'  => true,
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
        ]);
        $this->add_control('jltma_mrbl_vid_poster', [
            'label' => esc_html__('Upload poster', MELA_TD),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
            'label_block'  => true,
        ]);
        $this->add_control(
            'jltma_mrbl_vid_breakpoint',
            array(
                'label'   => esc_html__('Breakpoint', MELA_TD),
                'description' => esc_html__('Video will be replaced by Fallback image after if window width less than this breakpoint', MELA_TD),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 300,
                'max'     => 2500,
                'step'    => 1,
                'default' => 1200,
                'condition' => array(
                    'jltma_mrbl_type' => 'video',
                ),
            )
        );
        $this->add_responsive_control('jltma_mrbl_vid_fallback', [
            'label' => esc_html__('Upload fallback image', MELA_TD),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'condition' => array(
                'jltma_mrbl_type' => 'video',
            ),
            'label_block'  => true,
        ]);
        $this->add_control(
            'tensionPoints',
            [
                'label' => __('Curve Tension', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 2,
                ],
                'label_block' => true,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_control(
            'numPoints',
            [
                'label' => __('Num Points', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'label_block' => true,
                'range' => [
                    'px' => [
                        'min' => 3,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_control(
            'minmaxRadius',
            [
                'label' => __('Min Max Radius', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'sizes' => [
                        'start' => 140,
                        'end' => 160,
                    ],
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 600,
                        'step' => 1,
                    ],
                ],
                'labels' => [
                    __('Min', MELA_TD),
                    __('Max', MELA_TD),
                ],
                'scales' => 0,
                'handles' => 'range',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_control(
            'minmaxDuration',
            [
                'label' => __('Min Max Duration', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'sizes' => [
                        'start' => 5,
                        'end' => 6,
                    ],
                    'unit' => 's',
                ],
                'range' => [
                    's' => [
                        'min' => 0.1,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'labels' => [
                    __('Min', MELA_TD),
                    __('Max', MELA_TD),
                ],
                'scales' => 0,
                'handles' => 'range',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
            ]
        );
        $this->add_responsive_control(
            'svgarea_size',
            [
                'label' => __('Svg Size', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 2000,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .rh-svg-blob' => 'width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->add_responsive_control(
            'svg_size',
            [
                'label' => __('Image Size', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 2000,
                    ],
                ],
                'condition' => [
                    'svg_image[id]!' => '',
                    'jltma_mrbl_type' => 'masksvg',
                ],

            ]
        );
        $this->add_control(
            'svgfilltype',
            [
                'label' => __('Fill with', MELA_TD),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'color' => __('Color', MELA_TD),
                    'image' => __('Image', MELA_TD),
                    'gradient' => __('Gradient', MELA_TD),
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                ],
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'fill_color',
            [
                'label' => __('Default Color', MELA_TD),
                'type' => Controls_Manager::COLOR,
                'default' => '#FF0000',
                'alpha' => false,
                'condition' => [
                    'svgfilltype' => 'color',
                    'jltma_mrbl_type' => 'masksvg',
                ],

            ]
        );
        $this->add_control(
            'svg_image',
            [
                'label' => __('Image', MELA_TD),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],

                'show_label' => false,
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'image'
                ],
            ]
        );
        $this->add_control(
            'svgimage_x',
            [
                'label' => __('Translate X', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '0',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                //'render_type' => 'ui',
                'label_block' => false,
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'image'
                ],
            ]
        );
        $this->add_control(
            'svgimage_y',
            [
                'label' => __('Translate Y', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '0',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                //'render_type' => 'ui',
                'label_block' => false,
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'image'
                ],
            ]
        );
        $this->add_control(
            'gradientx1',
            [
                'label' => __('X1 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => '%',
                ],
                'label_block' => true,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradientx2',
            [
                'label' => __('X2 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'label_block' => true,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradienty1',
            [
                'label' => __('Y1 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => '%',
                ],
                'label_block' => true,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradienty2',
            [
                'label' => __('Y2 position', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'label_block' => true,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradientcolor1',
            [
                'label' => __('Color 1', MELA_TD),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff0000',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );
        $this->add_control(
            'gradientcolor2',
            [
                'label' => __('Color 2', MELA_TD),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0000ff',
                'condition' => [
                    'jltma_mrbl_type' => 'masksvg',
                    'svgfilltype' => 'gradient'
                ],
            ]
        );

        $this->add_responsive_control(
            'rhandwidth',
            [
                'label' => __('Area width', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 100,
                        'max' => 2500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma_mrbl_and_canvas' => 'width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->add_responsive_control(
            'rhandheight',
            [
                'label' => __('Area height', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 100,
                        'max' => 2500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma_mrbl_and_canvas' => 'height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        if (!empty($settings['jltma_mrbl_particle_json'])) {
            $uniqueid = 'jltma_mrbl_particle_' . uniqid() . 'hash';
            wp_enqueue_script('jltma_mrbl_elparticle');
            $particlejson = sanitize_text_field($settings['jltma_mrbl_particle_json']);
            $particlecode = 'particlesJS("' . $uniqueid . '", ' . $particlejson . ', function() {console.log("callback - particles.js config loaded");});';
            wp_add_inline_script('jltma_mrbl_elparticle', $particlecode);
            if (Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="jltma_mrbl_and_canvas"><div id="' . $uniqueid . '" class="rh-particle-canvas-true" data-particlejson=\'' . $particlejson . '\'> </div></div>';
            } else {
                echo '<div id="' . $uniqueid . '" class="rh-particle-canvas-true"></div>';
            }
        } else if (!empty($settings['jltma_mrbl_canvas_type']) && $settings['jltma_mrbl_canvas_type'] == 'video') {
            if (!empty($settings['jltma_mrbl_vid_mp4'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-mp4', $settings['jltma_mrbl_vid_mp4']);
            }
            if (!empty($settings['jltma_mrbl_vid_webm'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-webm', $settings['jltma_mrbl_vid_webm']);
            }
            if (!empty($settings['jltma_mrbl_vid_ogv'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-ogv', $settings['jltma_mrbl_vid_ogv']);
            }
            if (!empty($settings['jltma_mrbl_vid_poster'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-poster', $settings['jltma_mrbl_vid_poster']['url']);
            }
            if (!empty($settings['jltma_mrbl_vid_breakpoint'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-breakpoint', $settings['jltma_mrbl_vid_breakpoint']);
            }
            if (!empty($settings['jltma_mrbl_vid_fallback'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-fallback', $settings['jltma_mrbl_vid_fallback']['url']);
            }
            if (!empty($settings['jltma_mrbl_vid_fallback_tablet'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-fallback-tablet', $settings['jltma_mrbl_vid_fallback_tablet']['url']);
            }
            if (!empty($settings['jltma_mrbl_vid_fallback_mobile'])) {
                $this->add_render_attribute('jltma_mrbl_vid_data', 'data-fallback-mobile', $settings['jltma_mrbl_vid_fallback_mobile']['url']);
            }
            echo '<video autoplay loop muted class="rh-video-canvas jltma_mrbl_and_canvas" ' . $this->get_render_attribute_string('jltma_mrbl_vid_data') . '></video>';
        } else if (!empty($settings['jltma_mrbl_canvas_type']) && $settings['jltma_mrbl_canvas_type'] == 'masksvg') {
            $widgetId = $this->get_id();

            if (!empty($settings['svg_image']['id'])) {
                $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['svg_image']['id'], 'image', $settings);
                $imageData = wp_get_attachment_image_src($settings['svg_image']['id'], 'full');
                $h = $imageData[2];
                $w = $imageData[1];
                $imageProportion = $h / $w;
                $realHeight = $settings['svg_size']['size'] * $imageProportion;
                $this->add_render_attribute('_svgrapper', 'data-resize', $realHeight);
            }
            $this->add_render_attribute('_svgrapper', 'data-numpoints', $settings['numPoints']['size']);
            $this->add_render_attribute('_svgrapper', 'data-minradius', $settings['minmaxRadius']['sizes']['start']);
            $this->add_render_attribute('_svgrapper', 'data-maxradius', $settings['minmaxRadius']['sizes']['end']);
            $this->add_render_attribute('_svgrapper', 'data-minduration', $settings['minmaxDuration']['sizes']['start']);
            $this->add_render_attribute('_svgrapper', 'data-maxduration', $settings['minmaxDuration']['sizes']['end']);
            $this->add_render_attribute('_svgrapper', 'data-tensionpoints', $settings['tensionPoints']['size']);

            if (empty($settings['svgimage_x']['size'])) {
                $posX = 0;
            } else {
                $posX = $settings['svgimage_x']['size'];
            }
            if (empty($settings['svgimage_y']['size'])) {
                $posY = 0;
            } else {
                $posY = $settings['svgimage_y']['size'];
            }
?>
            <div data-id="<?php echo esc_attr($widgetId); ?>" class="rh-svgblob-wrapper jltma_mrbl_and_canvas" <?php echo '' . $this->get_render_attribute_string('_svgrapper') ?>>
                <svg class="rh-svg-blob" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" preserveAspectRatio="xMidYMid meet" xml:space="preserve">

                    <?php if (!empty($settings['svg_image']['id']) && $settings['svgfilltype'] == 'image') { ?>
                        <defs>
                            <pattern id="pattern-<?php echo esc_attr($widgetId); ?>" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse" width="<?php echo '' . $settings['svg_size']['size'] . $settings['svg_size']['unit']; ?>" height="<?php echo '' . $realHeight . $settings['svg_size']['unit']; ?>" x="<?php echo '' . $posX . $settings['svgimage_x']['unit']; ?>" y="<?php echo '' . $posY . $settings['svgimage_y']['unit']; ?>">

                                <image id="img-pattern" xlink:href="<?php echo '' . $image_url; ?>" width="<?php echo '' . $settings['svg_size']['size'] . $settings['svg_size']['unit']; ?>" height="<?php echo '' . $realHeight . $settings['svg_size']['unit']; ?>"> </image>
                            </pattern>
                        </defs>
                    <?php } ?>
                    <?php if ($settings['svgfilltype'] == 'gradient') { ?>
                        <defs>
                            <linearGradient id="pattern-<?php echo esc_attr($widgetId); ?>" x1="<?php echo '' . $settings['gradientx1']['size'] . $settings['gradientx1']['unit']; ?>" x2="<?php echo '' . $settings['gradientx2']['size'] . $settings['gradientx2']['unit']; ?>" y1="<?php echo '' . $settings['gradienty1']['size'] . $settings['gradienty1']['unit']; ?>" y2="<?php echo '' . $settings['gradienty2']['size'] . $settings['gradienty2']['unit']; ?>">
                                <stop style="stop-color: <?php echo '' . $settings['gradientcolor1']; ?>" offset="0" />
                                <stop style="stop-color: <?php echo '' . $settings['gradientcolor2']; ?>" offset="1" />
                            </linearGradient>
                        </defs>
                    <?php } ?>


                    <path id="rhblobpath-<?php echo esc_attr($widgetId); ?>"></path>

                    <?php if (!empty($settings['svg_image']['id']) || $settings['gradientcolor1'] != '') : ?>
                        <style>
                            #rhblobpath-<?php echo esc_attr($widgetId); ?> {
                                fill: url(#pattern-<?php echo '' . $this->get_id(); ?>);
                            }
                        </style>
                    <?php else : ?>
                        <style>
                            #rhblobpath-<?php echo esc_attr($widgetId); ?> {
                                fill: <?php echo '' . $settings['fill_color']; ?>;
                            }
                        </style>
                    <?php endif; ?>


                </svg>
            </div>
<?php
            wp_enqueue_script('gsap');
        }
        wp_enqueue_script('jltma_mrbl_elcanvas');
    }
}
