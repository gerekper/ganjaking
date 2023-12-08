<?php 
/*
Widget Name: Chart
Description: Chart
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load; 

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Chart extends Widget_Base {
	
	public function get_name() {
		return 'tp-chart';
	}

    public function get_title() {
        return esc_html__('Chart', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-area-chart theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	
	public function get_keywords() {
		return ['chart','line','bar','vertical bar','horizontal bar','radar','pie','doughnut','polararea','bubble'];
	}
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'chart_tab_content',
			[
				'label' => esc_html__( 'Chart', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'select_chart',
			[
				'label' => esc_html__( 'Select Chart', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'line',
				'options' => [
					'line'  => esc_html__( 'Line', 'theplus' ),
					'bar' => esc_html__( 'Bar', 'theplus' ),
					'radar' => esc_html__( 'Radar', 'theplus' ),
					'pie' => esc_html__( 'Doughnut & Pie', 'theplus' ),
					'polarArea' => esc_html__( 'Polar Area', 'theplus' ),
					'bubble' => esc_html__( 'Bubble', 'theplus' ),
				], 
			]
		);
		$this->add_control(
			'bar_chart_type',
			[
				'label' => esc_html__( 'Orientation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'vertical_bar',
				'options' => [
					'vertical_bar'  => esc_html__( 'Vertical Bar', 'theplus' ),
					'horizontal_bar' => esc_html__( 'Horizontal Bar', 'theplus' ),
				],
				'condition' => [ 
					'select_chart' => 'bar',
				],  
			]
		); 
		$this->add_control(
			'doughnut_pie_chart_type',
			[
				'label' => esc_html__( 'Orientation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pie',
				'options' => [
					'pie' => esc_html__( 'Pie', 'theplus' ),
					'doughnut'  => esc_html__( 'Doughnut', 'theplus' ),
				],
				'condition' => [ 
					'select_chart' => 'pie',
				],  
			]
		); 		
		$this->add_control(
			'main_label',
			[
				'label' => esc_html__( 'label Values', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Jan | Feb | Mar', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ),  
				'dynamic' => ['active' => true,], 
			]
		);   
		$this->end_controls_section();  
		$this->start_controls_section(
            'chart_dataset',
            [
                'label' => esc_html__('Dataset', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'select_chart' => [ 'line', 'bar', 'radar']
				]
            ]
        ); 
		$repeater = new \Elementor\Repeater();  
		$repeater->add_control(
			'loop_label',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Label', 'theplus' ),
				'placeholder' => esc_html__( 'Enter label', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				 				
			]
		);
		$repeater->add_control(
			'loop_data',
			[
				'label' => esc_html__( 'Data', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '0 | 25 | 42', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'multi_dot_bg',
			[
				'label' => esc_html__( 'Multiple Dot Background', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'dot_bg',
			[
				'label' => esc_html__( 'Dot Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgb(0 0 0 / 50%)',
				'condition' => [
					'multi_dot_bg!' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'multi_dot_bg_multiple',
			[
				'label' => esc_html__( 'Dot Background', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'multi_dot_bg' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'multi_dot_border',
			[
				'label' => esc_html__( 'Multiple Border', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'dot_border',
			[
				'label' => esc_html__( 'Dot Border', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgb(0 0 0 / 50%)',
				'condition' => [
					'multi_dot_border!' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'multi_dot_border_multiple',
			[
				'label' => esc_html__( 'Dot Border', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'multi_dot_border' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'fill_opt',
			[
				'label' => esc_html__( 'Fill', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'description' => esc_html__( 'Note : Fill works in Line and Radar chart', 'theplus' ),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'line_styling_opt',
			[
				'label' => esc_html__( 'Border Dash', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'description' => esc_html__( 'Note : Border Dash works in Line and Radar chart', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'chart_content',
			[
				'label' => esc_html__( 'Chart Data Boxes', 'theplus' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
                    [  
						'loop_label' => esc_html__( 'Jan', 'theplus' ),
						'loop_data' => esc_html__( '25 | 15 | 30 ', 'theplus' ),						
						'dot_bg'  => '#f7d78299',
						'dot_border' => '#f7d78299',
                    ],
					[ 
						'loop_label' => esc_html__( 'Feb', 'theplus' ),
						'loop_data' => esc_html__( '12 | 18 | 28', 'theplus' ),
						'dot_bg'  => '#6fc78499',
						'dot_border' => '#6fc78499',
                    ],
					[ 
						'loop_label' => esc_html__( 'Mar', 'theplus' ),
						'loop_data' => esc_html__( '11 | 20 | 40', 'theplus' ),
						'dot_bg'  => '#8072fc99',
						'dot_border' => '#8072fc99',
                    ],					
                ],
				'title_field' => '{{{ loop_label }}}',
			]
		);
		$this->end_controls_section(); 
		
		$this->start_controls_section(
            'chart_dataset_alone',
            [
                'label' => esc_html__('Dataset', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'select_chart',
									'operator' => '==',
									'value'    => 'polarArea',
								],
								[
									'terms' => [
										[
											'name'  => 'select_chart',
											'value' => 'pie',
										],
										[
											'name'  => 'doughnut_pie_chart_type',
											'value' => 'pie',
										],
									],
								],								
							],
						],
					],
				],
            ]
        );		
		$this->add_control(
			'alone_data',
			[
				'label' => esc_html__( 'Data', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '10 | 15 | 20', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'alone_bg_colors',
			[
				'label' => esc_html__( 'Background Colors', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'alone_border_colors',
			[
				'label' => esc_html__( 'Border Colors', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'terms' => [
										[
											'name'  => 'select_chart',
											'value' => 'pie',
										],
										[
											'name'  => 'doughnut_pie_chart_type',
											'value' => 'pie',
										],
									],
								],								
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'alone_border_colors_polar',
			[
				'label' => esc_html__( 'Border Colors', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'terms' => [
										[
											'name'  => 'select_chart',
											'value' => 'polarArea',
										],
									],
								],								
							],
						],
					],
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
            'doughnut_chart_dataset',
            [
                'label' => esc_html__('Dataset', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'select_chart' => 'pie',
					'doughnut_pie_chart_type' => 'doughnut',
				],
            ]
        );
		$repeater2 = new \Elementor\Repeater(); 
		$repeater2->add_control(
			'doughnut_loop_label',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Label', 'theplus' ),
				'placeholder' => esc_html__( 'Enter label', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				 				
			]
		);
		$repeater2->add_control(
			'doughnut_loop_data',
			[
				'label' => esc_html__( 'Data', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '10 | 15 | 20', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$repeater2->add_control(
			'doughnut_loop_background',
			[
				'label' => esc_html__( 'Background Colors', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#ff5a6e99|#8072fc99|#6fc78499', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$repeater2->add_control(
			'doughnut_loop_border',
			[
				'label' => esc_html__( 'Border Colors', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#00000099|#00000099|#00000099', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'doughnut_chart_datasets',
			[
				'label' => esc_html__( 'Chart Data Boxes', 'theplus' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater2->get_controls(),
				'default' => [
                    [  
						'doughnut_loop_label' => esc_html__( 'Jan', 'theplus' ),
						'doughnut_loop_data' => esc_html__( '25 | 15 | 30 ', 'theplus' ),						
						'doughnut_loop_background'  => '#ff5a6e99|#8072fc99|#6fc78499',
						'doughnut_loop_border' => '#00000099|#00000099|#00000099',
                    ],
					[ 
						'doughnut_loop_label' => esc_html__( 'Feb', 'theplus' ),
						'doughnut_loop_data' => esc_html__( '12 | 18 | 28', 'theplus' ),
						'doughnut_loop_background'  => '#f7d78299|#6fc78499|#8072fc99',
						'doughnut_loop_border' => '#40e0d0|#40e0d0|#40e0d0',
                    ],
					[ 
						'doughnut_loop_label' => esc_html__( 'Mar', 'theplus' ),
						'doughnut_loop_data' => esc_html__( '11 | 20 | 40', 'theplus' ),
						'doughnut_loop_background'  => '#71d1dc99|#8072fc99|#ff5a6e99',
						'doughnut_loop_border' => '#00000099|#00000099|#00000099',
                    ],					
                ],
				'title_field' => '{{{ doughnut_loop_label }}}',
			]
		);
		$this->end_controls_section(); 
		
		$this->start_controls_section(
            'chart_dataset_bubble',
            [
                'label' => esc_html__('Datasets', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'select_chart' => 'bubble'
				]
            ]
        );
		$repeater1 = new \Elementor\Repeater();  
		$repeater1->add_control(
			'loop_label',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Label', 'theplus' ),
				'placeholder' => esc_html__( 'Enter label', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				 				
			]
		);
		$repeater1->add_control(
			'bubble_data',
			[
				'label' => esc_html__( 'Data', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '[5|15|15][10|18|12][7|14|14]', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ), 
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
			]
		);
		$repeater1->add_control(
			'multi_dot_bg',
			[
				'label' => esc_html__( 'Multiple Background Colors', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater1->add_control(
			'dot_bg',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgb(0 0 0 / 50%)',
				'condition' => [
					'multi_dot_bg!' => 'yes',
				],
			]
		);
		$repeater1->add_control(
			'multi_dot_bg_multiple',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'multi_dot_bg' => 'yes',
				],
			]
		);
		$repeater1->add_control(
			'multi_dot_border',
			[
				'label' => esc_html__( 'Multiple Border Colors', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater1->add_control(
			'dot_border',
			[
				'label' => esc_html__( 'Dot Border', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgb(0 0 0 / 50%)',
				'condition' => [
					'multi_dot_border!' => 'yes',
				],
			]
		);
		$repeater1->add_control(
			'multi_dot_border_multiple',
			[
				'label' => esc_html__( 'Dot Background Colors', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '#f7d78299|#6fc78499|#8072fc99', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by | ', 'theplus' ),  
				'dynamic' => ['active'   => true,],
				'condition'    => [
					'multi_dot_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'bubble_content',
			[
				'label' => esc_html__( 'Chart Data Boxes', 'theplus' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater1->get_controls(),
				'default' => [
                    [  
						'loop_label' => esc_html__( 'Jan', 'theplus' ),
						'bubble_data' => esc_html__( '[5|15|15][10|18|12][7|14|14]', 'theplus' ),						
						'dot_bg'  => '#f7d78299',
						'dot_border' => '#f7d78299',
                    ],
					[ 
						'loop_label' => esc_html__( 'Feb', 'theplus' ),
						'bubble_data' => esc_html__( '[7|10|16][15|14|18][15|17|12]', 'theplus' ),
						'dot_bg'  => '#6fc78499',
						'dot_border' => '#6fc78499',
                    ],
					[ 
						'loop_label' => esc_html__( 'Mar', 'theplus' ),
						'bubble_data' => esc_html__( '[9|20|12][8|16|16][14|24|20]', 'theplus' ),
						'dot_bg'  => '#8072fc99',
						'dot_border' => '#8072fc99',
                    ],					
                ],
				'title_field' => '{{{ loop_label }}}',
			]
		);
		$this->end_controls_section(); 
		
		$this->start_controls_section(
            'chart_extra_options',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT, 
            ]
        );
		$this->add_responsive_control(
            'maxbarthickness',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bar Size', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'condition' => [
					'select_chart' => 'bar',
				],
            ]
        );
		$this->add_responsive_control(
            'barthickness',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bar Space', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition' => [
					'select_chart' => 'bar',
				],
            ]
        );
		$this->add_control(
			'show_grid_lines',
			[
				'label' => esc_html__( 'Grid Lines', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition'    => [
					'select_chart!' => 'pie',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_axes_style', [
			'condition' => [
				'select_chart!' => 'pie',
				'show_grid_lines' => 'yes',
			],
		] );
		$this->start_controls_tab(
			'tab_axes_x',
			[
				'label' => esc_html__( 'X Axes', 'theplus' ),
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'grid_color',
			[
				'label' => esc_html__( 'Grid Color X Axes', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgb(0 0 0 / 50%)',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'zero_linecolor_x',
			[
				'label' => esc_html__( 'Zero Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgba(0, 0, 0, 0.25)',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'draw_border_x',
			[
				'label' => esc_html__( 'Draw Border', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'draw_on_chart_area_x',
			[
				'label' => esc_html__( 'Draw Border on Chart Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_axes_y',
			[
				'label' => esc_html__( 'Y Axes', 'theplus' ),
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'grid_color_xAxes',
			[
				'label' => esc_html__( 'Grid Color Y Axes', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgb(0 0 0 / 50%)',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'zero_linecolor_y',
			[
				'label' => esc_html__( 'Zero Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => 'rgba(0, 0, 0, 0.25)',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'draw_border_y',
			[
				'label' => esc_html__( 'Draw Border', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->add_control(
			'draw_on_chart_area_y',
			[
				'label' => esc_html__( 'Draw Border on Chart Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'select_chart!' => 'pie',
					'show_grid_lines' => 'yes',
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->add_control(
			'show_labels',
			[
				'label' => esc_html__( 'Labels', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'select_chart!' => 'pie',
				]
			]
		);
		$this->add_control(
			'show_labels_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#666',
				'condition' => [
					'select_chart!' => 'pie',
					'show_labels' => 'yes',
				]
			]
		);
		$this->add_responsive_control(
            'show_labels_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Label Size', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [					
					'size' => 12,
				],
				'condition' => [
					'select_chart!' => 'pie',
					'show_labels' => 'yes',
				],
            ]
        );
		$this->add_control(
			'show_legend',
			[
				'label' => esc_html__( 'Legend', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);	
		$this->add_responsive_control(
            'show_legend_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [					
					'size' => 12,
				],
				'condition' => [
					'show_legend' => 'yes',
				],
            ]
        );
		$this->add_control(
			'show_legend_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#666',
				'condition' => [
					'show_legend' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_legend_position',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'top' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'top',
				'condition' => [
					'show_legend' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_legend_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'End', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'condition' => [
					'show_legend' => 'yes',
				],
			]
		);		
		$this->add_control(
			'tension',
			[
				'label' => esc_html__( 'Smooth', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'select_chart' => 'line',
				]
			]
		);
		$this->add_control(
			'custom_point_styles',
			[
				'label' => esc_html__( 'Custom Point Styles', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'select_chart' => ['line','radar','bubble'],
				]
			]
		);
		$this->add_control(
			'point_styles',
			[
				'label' => esc_html__( 'Point Styles', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle'  => esc_html__( 'Circle', 'theplus' ),
					'cross' => esc_html__( 'Cross', 'theplus' ),
					'crossRot' => esc_html__( 'CrossRot', 'theplus' ),
					'dash' => esc_html__( 'Dash', 'theplus' ),
					'line' => esc_html__( 'Line', 'theplus' ),
					'rect' => esc_html__( 'Rect', 'theplus' ),
					'rectRounded' => esc_html__( 'RectRounded', 'theplus' ),
					'rectRot' => esc_html__( 'RectRot', 'theplus' ),
					'star' => esc_html__( 'Star', 'theplus' ),
					'triangle' => esc_html__( 'Triangle', 'theplus' ),
				], 
				'condition' => [
					'select_chart' => ['line','radar','bubble'],
					'custom_point_styles' => 'yes',
				]
			]
		);
		$this->add_control(
			'point_bg',
			[
				'label' => esc_html__( 'Point Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#ff5a6e99',
				'condition' => [
					'select_chart' => ['line','radar'],
					'custom_point_styles' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'point_n_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Point Normal Size', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [					
					'size' => 5,
				],
				'condition' => [
					'select_chart' => ['line','radar'],
					'custom_point_styles' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'point_h_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Point Hover Size', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [					
					'size' => 10,
				],
				'condition' => [
					'select_chart' => ['line','radar'],
					'custom_point_styles' => 'yes',
				],
            ]
        );
		$this->add_control(
			'point_border_color',
			[
				'label' => esc_html__( 'Point Border', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#00000099',
				'condition' => [
					'select_chart' => ['line','radar'],
					'custom_point_styles' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'point_border_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Point Border Width', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [					
					'size' => 1,
				],
				'condition' => [
					'select_chart' => ['line','radar'],
					'custom_point_styles' => 'yes',
				],
            ]
        );	
		$this->add_control(
			'show_tooltip',
			[
				'label' => esc_html__( 'Tooltip', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tooltip_event',
			[
				'label' => esc_html__( 'Event', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover'  => esc_html__( 'Hover', 'theplus' ),
					'click' => esc_html__( 'Click', 'theplus' ),
				],
				'condition' => [
					'show_tooltip' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'tooltip_font_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Font Size', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'default' => [					
					'size' => 12,
				],
				'condition' => [
					'show_tooltip' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tooltip_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#fff',
				'condition' => [
					'show_tooltip' => 'yes',
				],
			]
		);
		$this->add_control(
			'tooltip_body_color',
			[
				'label' => esc_html__( 'Body Font Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#fff',
				'condition' => [
					'show_tooltip' => 'yes',
				],
			]
		);
		$this->add_control(
			'tooltip_bg',
			[
				'label' => esc_html__( 'Tooltip Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'show_tooltip' => 'yes',
				],
			]
		);
		$this->add_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Aspect Ratio', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'maintain_aspect_ratio',
			[
				'label' => esc_html__( 'Maintain Aspect Ratio', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'c_animation',
			[
				'label' => esc_html__( 'Animation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'easeOutQuart',
				'options' => [
					'linear'  => esc_html__( 'linear', 'theplus' ),
					'easeInQuad' => esc_html__( 'easeInQuad', 'theplus' ),
					'easeOutQuad' => esc_html__( 'easeOutQuad', 'theplus' ),
					'easeInOutQuad' => esc_html__( 'easeInOutQuad', 'theplus' ),
					'easeInCubic' => esc_html__( 'easeInCubic', 'theplus' ),
					'easeOutCubic' => esc_html__( 'easeOutCubic', 'theplus' ),
					'easeInOutCubic' => esc_html__( 'easeInOutCubic', 'theplus' ),
					'easeInQuart' => esc_html__( 'easeInQuart', 'theplus' ),
					'easeOutQuart' => esc_html__( 'easeOutQuart', 'theplus' ),
					'easeInOutQuart' => esc_html__( 'easeInOutQuart', 'theplus' ),
					'easeInQuint'  => esc_html__( 'easeInQuint', 'theplus' ),
					'easeOutQuint' => esc_html__( 'easeOutQuint', 'theplus' ),
					'easeInOutQuint' => esc_html__( 'easeInOutQuint', 'theplus' ),
					'easeInSine' => esc_html__( 'easeInSine', 'theplus' ),
					'easeOutSine' => esc_html__( 'easeOutSine', 'theplus' ),
					'easeInOutSine' => esc_html__( 'easeInOutSine', 'theplus' ),
					'easeInExpo' => esc_html__( 'easeInExpo', 'theplus' ),
					'easeOutExpo' => esc_html__( 'easeOutExpo', 'theplus' ),
					'easeInOutExpo' => esc_html__( 'easeInOutExpo', 'theplus' ),
					'easeInCirc' => esc_html__( 'easeInCirc', 'theplus' ),
					'easeOutCirc'  => esc_html__( 'easeOutCirc', 'theplus' ),
					'easeInOutCirc' => esc_html__( 'easeInOutCirc', 'theplus' ),
					'easeInElastic' => esc_html__( 'easeInElastic', 'theplus' ),
					'easeOutElastic' => esc_html__( 'easeOutElastic', 'theplus' ),
					'easeInOutElastic' => esc_html__( 'easeInOutElastic', 'theplus' ),
					'easeInBack' => esc_html__( 'easeInBack', 'theplus' ),
					'easeOutBack' => esc_html__( 'easeOutBack', 'theplus' ),
					'easeInOutBack' => esc_html__( 'easeInOutBack', 'theplus' ),
					'easeInBounce' => esc_html__( 'easeInBounce', 'theplus' ),
					'easeOutBounce' => esc_html__( 'easeOutBounce', 'theplus' ),
					'easeInOutBounce'  => esc_html__( 'easeInOutBounce', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'c_animation_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration', 'theplus'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10000,
						'step' => 100,
					],
				],
				'default' => [					
					'size' => 1000,
				],
            ]
        );
		$this->end_controls_section();
	}
	public function bubble_array( $bubble_data ) {
		$bubble_data = trim( $bubble_data );		
		$split_value = preg_match_all( '#\[([^\]]+)\]#U', $bubble_data, $fetch_and_match );		
		if ( !$split_value ) {
			return [];
		}else {
			$data_value = $fetch_and_match[1];
			$bubble = [];
			foreach ( $data_value as $item_value ) {
				$item_value = trim( $item_value, '][ ' );
				$item_value = explode( '|', $item_value );
				
				if (count($item_value) != 3){
					continue;
				}					
				
				$x_y_r = new \stdClass();
				$x_y_r->x = floatval( trim( $item_value[0] ) );
				$x_y_r->y = floatval( trim( $item_value[1] ) );
				$x_y_r->r = floatval( trim( $item_value[2] ) );
				$bubble[] = $x_y_r;
			}
			return $bubble;
		}
	}
	protected function render() { 	
		$settings = $this->get_settings_for_display(); 
		$output = $label_data = $get_data = $charttype = '';
		$select_chart = $settings['select_chart'];
		$bar_chart_type = $settings['bar_chart_type'];
		$doughnut_pie_chart_type = $settings['doughnut_pie_chart_type'];	
		
	
		if((!empty($select_chart) && $select_chart=='bar') && (!empty($bar_chart_type) && $bar_chart_type=='horizontal_bar')){
			$charttype ='horizontalBar';
		}else if((!empty($select_chart) && $select_chart=='pie') && (!empty($doughnut_pie_chart_type) && $doughnut_pie_chart_type=='doughnut')){
			$charttype ='doughnut';
		}else{
			$charttype = $select_chart;
		} 
		
		$options=$datasets=$datasets1=$chart_datasets=[];

		if(!empty($select_chart) && ($select_chart=='pie' || $select_chart=='polarArea')){
			if($doughnut_pie_chart_type!='doughnut'){
				$alone_data = array_map('floatval', explode('|', $settings['alone_data']));
				if(!empty($alone_data)){
					$datasets[] = [ 
						'data' => $alone_data,
						'backgroundColor' => explode('|', $settings['alone_bg_colors'])
					];
				}
				
				if((!empty($doughnut_pie_chart_type) && $doughnut_pie_chart_type=='doughnut') && !empty($settings['alone_border_colors'])){
					$datasets[] = [
						'borderColor' => explode('|', $settings['alone_border_colors'])
					];
				}
				
				if($select_chart =='polarArea' && !empty($settings['alone_border_colors_polar'])){
					$datasets[] = [
						'borderColor' => explode('|', $settings['alone_border_colors_polar'])
					];
				}
			}else{
				foreach($settings['doughnut_chart_datasets'] as $item1){
					
					$datasets2['data']  =  array_map('floatval', explode('|', $item1['doughnut_loop_data']));
					
					$datasets2['backgroundColor'] = ($item1['doughnut_loop_background']) ? explode('|', $item1['doughnut_loop_background']) : [];
					
					$datasets2['borderColor'] = ($item1['doughnut_loop_border']) ? explode('|', $item1['doughnut_loop_border']) : [];
					
					$datasets[] = $datasets2;
				}
			}			
			
		}else{
			$chart_datasets = ($select_chart=='bubble') ?  $settings['bubble_content'] : $settings['chart_content'];			
			foreach($chart_datasets as $item){
				$datasets1['label'] = $item['loop_label'];

				if (!empty($select_chart) && $select_chart=='bubble') {
					$datasets1['data'] = $this->bubble_array($item['bubble_data']);
				} else {
					$datasets1['data']  =  array_map('floatval', explode('|', $item['loop_data']));				
				}

				if((!empty($item['multi_dot_bg']) && $item['multi_dot_bg']=='yes') && !empty($item['multi_dot_bg_multiple'])) {
					$datasets1['backgroundColor'] = explode('|', $item['multi_dot_bg_multiple']);
				} else {
					$datasets1['backgroundColor'] = $item['dot_bg'];
				}

				if((!empty($item['multi_dot_border']) && $item['multi_dot_border']=='yes') && !empty($item['multi_dot_border_multiple'])){
					$datasets1['borderColor'] = explode('|', $item['multi_dot_border_multiple']);
				} else {
					$datasets1['borderColor'] = $item['dot_border'];
				}
				
				$datasets1['borderDash']=[];
				if(!empty($select_chart) && ($select_chart=='line' || $select_chart=='radar') && (!empty($item['line_styling_opt']) && $item['line_styling_opt']=='yes')){
					$datasets1['borderDash'] = [5, 5];
				}
				
				if(!empty($item['fill_opt']) && $item['fill_opt']=='yes'){
					 $datasets1['fill'] =true;
				}else{
					 $datasets1['fill'] =false;
				}
				
				if (!empty($select_chart) && ($select_chart=='line' || $select_chart=='radar' || $select_chart=='bubble')){					
					if(!empty($settings['custom_point_styles']) && $settings['custom_point_styles']=='yes'){
						if(!empty($settings['point_styles'])){
							$datasets1['pointStyle'] =$settings['point_styles'];
						}
						if(!empty($settings['point_bg']) && $select_chart!='bubble'){
							$datasets1['pointBackgroundColor'] =$settings['point_bg'];
						}
						if(!empty($settings['point_n_size']['size']) && $select_chart!='bubble'){
							$datasets1['pointRadius'] =$settings['point_n_size']['size'];
						}
						if(!empty($settings['point_h_size']['size']) && $select_chart!='bubble'){
							$datasets1['pointHoverRadius'] =$settings['point_h_size']['size'];
						}
						if(!empty($settings['point_border_width']['size']) && $select_chart!='bubble'){
							$datasets1['borderWidth'] = $settings['point_border_width']['size'];
						}
						if(!empty($settings['point_border_color']) && $select_chart!='bubble'){
							$datasets1['pointBorderColor'] =$settings['point_border_color'];	
						}
					}
					
					if($select_chart=='line' && (!empty($settings['tension']) && $settings['tension']=='yes')){
						 $datasets1['tension']= 0.4;
					}else{
						 $datasets1['tension']= 0;
					}
				}
									
				if (!empty($select_chart) && $select_chart=='bar'){
					if(!empty($settings['barthickness']['size'])){
						$datasets1['barThickness']= $settings['barthickness']['size'];  /*space*/
					}
					if(!empty($settings['maxbarthickness']['size'])){
						$datasets1['maxBarThickness']= $settings['maxbarthickness']['size'];  /*size*/
					}	
					
			    }	

				$datasets[] = $datasets1;
			} 
		}

		if($select_chart=='pie' && (!empty($doughnut_pie_chart_type) && $doughnut_pie_chart_type=='pie')){
			$options['cutoutPercentage'] = 0;
		}else if($select_chart=='pie' && (!empty($doughnut_pie_chart_type) && $doughnut_pie_chart_type=='doughnut')){
			$options['cutoutPercentage'] = 50;
		}else{
			if(!empty($settings['show_grid_lines']) && $settings['show_grid_lines']=='yes'){
				$options['scales'] = [
					'yAxes' => [[
						'ticks' => [
							'display' => (!empty($settings['show_labels']) ) ? true : false,
							'fontColor' => $settings['show_labels_color'],
							'fontSize' => !empty($settings['show_labels_size']['size']) ? $settings['show_labels_size']['size'] : '',
						],
						'gridLines' => [							
							'color'      => $settings['grid_color'],
							'zeroLineColor' => $settings['zero_linecolor_y'],
							'drawBorder' => ( !empty($settings['draw_border_y'] )) ? true : false,
							'drawOnChartArea' => (!empty( $settings['draw_on_chart_area_y'] )) ? true : false,
							
						]
					]],
					'xAxes' => [[
						'ticks' => [
							'display' => ( !empty($settings['show_labels'] )) ? true : false,
							'fontColor' => $settings['show_labels_color'],
							'fontSize' => !empty($settings['show_labels_size']['size']) ? $settings['show_labels_size']['size'] : '',
						],
						'gridLines' => [							
							'color'      => $settings['grid_color_xAxes'],
							'zeroLineColor' => $settings['zero_linecolor_x'],
							'drawBorder' => ( !empty($settings['draw_border_x'] )) ? true : false,
							'drawOnChartArea' => (!empty( $settings['draw_on_chart_area_x'] )) ? true : false,							
						]
					]]
				];
			}else{
				$options['scales'] = [
					'yAxes' => [[
						'ticks' => [
							'display' => ( $settings['show_labels'] ) ? true : false,
							'fontColor' => $settings['show_labels_color'],
							'fontSize' => isset($settings['show_labels_size']['size']) ? $settings['show_labels_size']['size'] : '',
						],
						'gridLines' => [
							'display'    => false,
						]
					]],
					'xAxes' => [[
						'ticks' => [
							'display' => ( $settings['show_labels'] ) ? true : false,
							'fontColor' => $settings['show_labels_color'],
							'fontSize' => isset($settings['show_labels_size']['size']) ? $settings['show_labels_size']['size'] : '',
						],
						'gridLines' => [
							'display'    => false,
						]
					]]
				];
			}
		}
		
		if (!empty($settings['show_legend']) && $settings['show_legend']=='yes') {
			if (!empty($settings['show_legend_position'])){
				$options['legend'] = [ 
					'position' => $settings['show_legend_position'],
					'align' => $settings['show_legend_align'],
					'labels' => [
							'fontColor' => $settings['show_legend_color'],
							'fontSize' => $settings['show_legend_size']['size'],
					],
				];
			}
		}else{
			$options['legend'] = [ 'display' => false ];
		}
		
		if(!empty($settings['c_animation']) && !empty($settings['c_animation_duration']['size'])){
			$options['animation'] = [ 'duration' => $settings['c_animation_duration']['size'] , 'easing' => $settings['c_animation']];
		}
		
		if(!empty($settings['show_tooltip']) && $settings['show_tooltip']=='yes') {
			if (!empty($settings['tooltip_bg']) || !empty($settings['tooltip_color']) || !empty($settings['tooltip_body_color'])){
				$options['tooltips'] = [ 
					'backgroundColor' => $settings['tooltip_bg'],
					'titleFontColor' => $settings['tooltip_color'],
					'bodyFontColor' => $settings['tooltip_body_color'],
					'titleFontSize' => $settings['tooltip_font_size']['size'],
					'bodyFontSize' => $settings['tooltip_font_size']['size'],
				];
			}
			if(!empty($settings['tooltip_event']) && $settings['tooltip_event']=='click'){
				$options['events'] = ['click'];
			}
			
		}else{
			$options['tooltips'] = [ 'enabled' => false ];
		}
		
		if (!empty($settings['aspect_ratio']) && $settings['aspect_ratio']=='yes') {
			$options['aspectRatio'] = 1;
		}

		if (!empty($settings['maintain_aspect_ratio']) && $settings['maintain_aspect_ratio'] !='yes') {
			$options['maintainAspectRatio'] = false;
		}
		
		
		$this->add_render_attribute([
			'get_all_chart_values' => [
				'data-settings' => [ wp_json_encode(array_filter([
						"type"        => $charttype,
						"data"        => [
							"labels" => explode("|", $settings["main_label"]),
							"datasets" => $datasets,  
						],						
						"options" => $options,
					]))							
				]
			]
		]);
		
		$unique=uniqid('chart');
		$output .= '<div class="tp-chart-wrapper" data-id="'.esc_attr($unique).'" '.$this->get_render_attribute_string( 'get_all_chart_values' ).'>';
			$output .= '<canvas id="'.esc_attr($unique).'"></canvas>';		
		$output .= '</div>';
		
		echo $output;
	}
}
?>