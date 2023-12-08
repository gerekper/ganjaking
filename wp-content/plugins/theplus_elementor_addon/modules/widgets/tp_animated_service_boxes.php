<?php 
/*
Widget Name: Animated Service Boxes
Description: Listing and carousel of unique style animated services box
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class ThePlus_Animated_Service_Boxes extends Widget_Base {
		
	public function get_name() {
		return 'tp-animated-service-boxes';
	}

    public function get_title() {
        return esc_html__('Animated Service Boxes', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-th theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	public function get_keywords() {
		return ['image accordion','sliding boxes','article box','info banner','hover section','fancy box','services element','portfolio','verticle slider', 'horizontal slider'];
	}

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Animated Service Boxes', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'main_style',
			[
				'label' => esc_html__( 'Main Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image-accordion',
				'options' => [
					'image-accordion'  => esc_html__( 'Image Accordion', 'theplus' ),
					'sliding-boxes'  => esc_html__( 'Sliding Boxes', 'theplus' ),
					'article-box'  => esc_html__( 'Article Box', 'theplus' ),
					'info-banner'  => esc_html__( 'Info Banner', 'theplus' ),
					'hover-section'  => esc_html__( 'Hover Section', 'theplus' ),
					'fancy-box'  => esc_html__( 'Fancy Box', 'theplus' ),
					'services-element'  => esc_html__( 'Services Element', 'theplus' ),
					'portfolio'  => esc_html__( 'Portfolio', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'image_accordion_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'accordion-style-1',
				'options' => [
					'accordion-style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'accordion-style-2'  => esc_html__( 'Style 2', 'theplus' ),					
				],
				'condition'    => [
				'main_style' => 'image-accordion',
				],
			]
		);
		$this->add_control(
			'orientation_type',
			[
				'label' => esc_html__( 'Orientation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'accordion-vertical',
				'options' => [
					'accordion-vertical'  => esc_html__( 'Vertical', 'theplus' ),
					'accordion-horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
				],
				'condition'    => [
					'main_style' => 'image-accordion',
				],
			]
		);		
		$this->add_control(
			'sliding_boxes_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sliding-style-1',
				'options' => [
					'sliding-style-1'  => esc_html__( 'Style 1', 'theplus' ),			
				],
				'condition'    => [
					'main_style' => 'sliding-boxes',
				],
			]
		);
		$this->add_control(
			'article_box_style',
			[
				'label' => esc_html__( 'Article Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'article-box-style-1',
				'options' => [
					'article-box-style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'article-box-style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'    => [
					'main_style' => 'article-box',
				],
			]
		);
		$this->add_control(
			'active_slide',
			[
				'label' => esc_html__( 'Active Slide', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => theplus_get_active_slide(),
				'condition'    => [
					'main_style' => ['image-accordion','sliding-boxes'],
				],
			]
		);
		$this->add_control(
			'image_accordion_flex_grow',
			[
				'label' => esc_html__( 'Active Slide Width(0-15)', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 15,
				'step' => 0.5,
				'default' => 7.5,
				'condition'    => [
					'main_style' => ['image-accordion'],
				],
			]
		);
		$this->add_control(
			'info_banner_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'info-banner-style-1',
				'options' => [
					'info-banner-style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'info-banner-style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'    => [
				'main_style' => 'info-banner',
				],
			]
		);		
		$this->add_control(
			'hover_orientation',
			[
				'label' => esc_html__( 'Hover Orientation', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'info-banner-left',
				'options' => [
					'info-banner-top'  => esc_html__( 'Top', 'theplus' ),
					'info-banner-bottom'  => esc_html__( 'Bottom', 'theplus' ),
					'info-banner-left'  => esc_html__( 'Left', 'theplus' ),
					'info-banner-right'  => esc_html__( 'Right', 'theplus' ),
				],
				'condition'    => [
					'main_style' => 'info-banner',
					'info_banner_style' => 'info-banner-style-1',
				],
			]
		);
		$this->add_control(
			'hover_section_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover-section-style-1',
				'options' => [
					'hover-section-style-1'  => esc_html__( 'Style 1', 'theplus' ),			
				],
				'condition'    => [
					'main_style' => 'hover-section',
				],
			]
		);
		$this->add_control(
			'hover_section_image_preload',
			[
				'label' => esc_html__( 'Image Preload', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'main_style' => 'hover-section',
				],
			]
		);
		$this->add_control(
			'fancy_box_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fancy-box-style-1',
				'options' => [
					'fancy-box-style-1'  => esc_html__( 'Style 1', 'theplus' ),			
				],
				'condition'    => [
				'main_style' => 'fancy-box',
				],
			]
		);
		$this->add_control(
			'services_element_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'services-element-style-1',
				'options' => [
					'services-element-style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'services-element-style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'    => [
					'main_style' => 'services-element',
				],
			]
		);
		$this->add_control(
			'portfolio_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'portfolio-style-1',
				'options' => [
					'portfolio-style-1'  => esc_html__( 'Style 1', 'theplus' ),		
					'portfolio-style-2'  => esc_html__( 'Style 2', 'theplus' ),		
				],
				'condition'    => [
				'main_style' => 'portfolio',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'exclude' => [ 'custom' ],				
				'separator' => 'after',	
				'exclude' => [ 'custom' ],
			]
		);
		$this->add_control(
			'loop_display_button',
			[
				'label' => esc_html__( 'Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'main_style!' => 'portfolio',
				],
			]
		);
		$this->add_control(
            'loop_button_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'style-7',
                'options' => [
                    'style-7' => esc_html__('Style 1', 'theplus'),
                    'style-8' => esc_html__('Style 2', 'theplus'),
                    'style-9' => esc_html__('Style 3', 'theplus'),                    
                ],
				'separator' => 'after',
				'condition' => [
					'main_style!' => 'portfolio',
					'loop_display_button' => 'yes',
				],
            ]
        );
		$this->add_control(
			'loop_display_icon_image',
			[
				'label' => esc_html__( 'Image/Icon', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [					
					'main_style!' => ['image-accordion','sliding-boxes'],
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [								
								[
									'name'     => 'main_style','operator' => '==','value'    => 'services-element',
								],
								[
									'name'     => 'main_style','operator' => '==','value'    => 'info-banner',
								],	
								[
									'name'     => 'main_style','operator' => '==','value'    => 'hover-section',
								],	
								[
									'name'     => 'main_style','operator' => '==','value'    => 'article-box',
									'name'     => 'article_box_style','operator' => '==','value'    => 'article-box-style-2',
								],	
							],
						],
					],
				],
			]
		);	
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'loop_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'loop_image_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image using this option.','theplus'),
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],				
			]
		);
		$repeater->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => ['loop_image_icon' => 'lottie'],
			]
		);
		$repeater->add_control(
			'lottieNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: You will only be able to use this <b>Lottie</b> icon when you select the <b>info banner, hover section, and services element</b> from the <b>Main Style</b> menu and enable <b>Image/Icon</b> switcher.',
				'content_classes' => 'tp-widget-description',
				'condition' => ['loop_image_icon' => 'lottie'],
			]
		);
		$repeater->add_control(
			'loop_select_image',
			[
				'label' => esc_html__( 'Use Image As Icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'loop_image_icon' => 'image',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'loop_select_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'loop_image_icon' => 'image',
				],
			]
		);
		$repeater->add_control(
			'loop_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'loop_image_icon' => 'icon',
				],
			]
		);
		$repeater->add_control(
			'loop_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-bank',
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome',
				],	
			]
		);
		$repeater->add_control(
			'loop_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-university',
					'library' => 'solid',
				],
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome_5',
				],	
			]
		);
		$repeater->add_control(
			'loop_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'loop_sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'loop_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => ['active'   => true,],
			]
		);		
		$repeater->add_control(
			'featured_image',
			[
				'label' => esc_html__( 'Featured Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => ['active'   => true,],
			]
		);		
		$repeater->add_control(
			'loop_button_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => ['active'   => true,],
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Read More', 'theplus' ),
			]
		);
		$repeater->add_control(
			'loop_button_link',
			[
				'label' => esc_html__( 'Button Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => ['active'   => true,],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
			]
		);
		$repeater->add_control(
			'loop_content_list_heading',
			[
				'label' => 'List content display in Services Element style only.',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);		
		$repeater->add_control(
			'loop_content_list',
			[
				'label' => esc_html__( 'List Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__('Faucibus nisl tincidunt eget nullam | Volutpat est velit egestas dui | Tincidunt ornare massa eget egestas purus | Congue nisi vitae suscipit tellus mauris', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by "|" ', 'theplus' ),
				'description' => esc_html__( 'Display multiple listing use separator e.g. Small | Medium | Large ', 'theplus' ),
			]
		);
		$this->add_control(
            'loop_content',
            [
				'label' => esc_html__( 'Animated Service Boxes', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'loop_title' => 'The Plus',                       
                    ],
					[
                        'loop_title' => 'The Plus 2',
                    ],
					[
                        'loop_title' => 'The Plus 3',
                    ],					
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ loop_title }}}',				
            ]
        );
		$this->end_controls_section();
		/*layout*/		
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,				
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style','operator' => '==','value'    => 'image-accordion',
								],
								[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],
								[
									'name'     => 'main_style','operator' => '==','value'    => 'portfolio',
								],
								[
									'name'     => 'main_style','operator' => '==','value'    => 'info-banner',
								],	
								[
									'name'     => 'main_style','operator' => '==','value'    => 'hover-section',
								],	
								[
									'name'     => 'main_style','operator' => '==','value'    => 'article-box',
								],
								[		
									'name'     => 'main_style','operator' => '==','value'    => 'sliding-boxes',		
								],
							],
						],
					],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_layout_style' );
		$this->start_controls_tab(
			'tab_layout_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],								
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'transform_normal_css',
			[
				'label' => esc_html__( 'Transform CSS', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper .fancybox-image-background' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],								
							],
						],
					],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_layout_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],								
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'transform_hover_css',
			[
				'label' => esc_html__( 'Transform CSS', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper:hover .fancybox-image-background' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],								
							],
						],
					],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(	
		'text_align',
			[
				'label' => esc_html__( 'Text Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion .asb-content,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-content-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content,{{WRAPPER}} .pt_plus_asb_wrapper.hover-section .asb_wrap_list.tp-row.hover-section-extra,
					{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .asb_wrap_list' => 'text-align:{{VALUE}};',
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'image-accordion',
								],
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'info-banner',
								],
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'hover-section',
								],
								[
									'terms' => [
										[
											'name'  => 'main_style',
											'value' => 'portfolio',
										],
										[
											'name'  => 'portfolio_style',
											'value' => 'portfolio-style-1',
										],
									],
								],								
							],
						],
					],
				],				
				'toggle' => true,
			]
		);
		$this->add_responsive_control(	
		'text_align_port',
			[
				'label' => esc_html__( 'Text Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-2 .portfolio-wrapper' => 'align-items:{{VALUE}};',
				],
				'condition' => [
					'portfolio_style' => ['portfolio-style-2'],
				],				
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'align_offset',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
					],
					'space-between' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],					
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion .asb-content,{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-2 .portfolio-wrapper' => 'justify-content:{{VALUE}};',
					'{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content' => 'align-items:{{VALUE}};justify-content: center;',
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'image-accordion',
								],								
								[
									'terms' => [
										[
											'name'  => 'main_style',
											'value' => 'info-banner',
										],
										[
											'name'  => 'info_banner_style',
											'value' => 'info-banner-style-2',
										],
									],
								],
								[
									'terms' => [
										[
											'name'  => 'main_style',
											'value' => 'portfolio',
										],
										[
											'name'  => 'portfolio_style',
											'value' => 'portfolio-style-2',
										],
									],
								],
							],
						],
					],
				],
				'toggle' => true,
			]
		);
		$this->add_control(
			'align_offset_port1',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
					],										
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .asb_wrap_list.tp-row' => 'align-items:{{VALUE}};',
				],
				'condition' => [
					'main_style' => 'portfolio',
					'portfolio_style' => 'portfolio-style-1',
				],
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'align_offset_slidingbox',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],	
				],				
				'selectors' => [
					'{{WRAPPER}} .sliding-boxes .service-item-loop .asb-content' => 'justify-content:{{VALUE}};',					
				],
				'condition' => [
					'main_style' => 'sliding-boxes',
				],
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
            'layout_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px', 'em', '%' ,'vh'],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
						'step' => 5,
					],
					'em' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'separator' => 'before',				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion .asb_wrap_list,
					{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-wrapper,{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-image' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-2 .portfolio-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.article-box-style-2 .article-box-front-wrapper,{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-content-wrapper,{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-2 .info-front-content,{{WRAPPER}} .pt_plus_asb_wrapper.hover-section' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'image-accordion',
								],
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'hover-section',
								],
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'info-banner',
								],
								[
									'name'     => 'main_style',
									'operator' => '==',
									'value'    => 'portfolio',
								],
								[
									'terms' => [
										[
											'name'  => 'main_style',
											'value' => 'article-box',
										],
										[
											'name'  => 'article_box_style',
											'value' => 'article-box-style-2',
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
		/*layout*/
		/*portfoilio title text mobile*/
		$this->start_controls_section(
			'port_mobile_section',
			[
				'label' => esc_html__( 'Title On Click Text', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,	
				'condition' => [
					'main_style' => 'portfolio',
				],
			]
		);
		$this->add_control(
			'port_mobile_heading',
			[
				'label' => esc_html__( 'Set onclick Title Text,Work Only < 1024 screen', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'port_mobile_text',
			[
				'label' => esc_html__( 'Title On Click Text', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Click Here', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'placeholder' => esc_html__( 'Your Text Here', 'theplus' ),
			]
		);		
		$this->end_controls_section();
		/*portfoilio title text mobile*/
		/*sliding box columns*/
		$this->start_controls_section(
			'sliding_columns_section',
			[
				'label' => esc_html__( 'Columns Manage', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'main_style' => ['sliding-boxes'],
				],
			]
		);
		$this->add_control(
			'sb_tablet_column',
			[
				'label' => esc_html__( 'Tablet Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sb_t_2',
				'options' => [
					'sb_t_1'  => esc_html__( 'Column 1', 'theplus' ),
					'sb_t_2' => esc_html__( 'Column 2', 'theplus' ),
					'sb_t_3' => esc_html__( 'Column 3', 'theplus' ),
				],				
			]
		);
		$this->add_control(
			'sb_mobile_column',
			[
				'label' => esc_html__( 'Mobile Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sb_m_1',
				'options' => [
					'sb_m_1'  => esc_html__( 'Column 1', 'theplus' ),
					'sb_m_2' => esc_html__( 'Column 2', 'theplus' ),
					'sb_m_3' => esc_html__( 'Column 3', 'theplus' ),
				],				
			]
		);
		$this->add_responsive_control(
			'slide_columns_gap',
			[
				'label' => esc_html__( 'Columns Gap/Space Between', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],				
				'separator' => 'before',				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.sliding-boxes .service-item-loop' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*sliding box columns*/
		/*columns*/
		$this->start_controls_section(
			'columns_section',
			[
				'label' => esc_html__( 'Columns Manage', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'main_style' => ['article-box','info-banner','hover-section','fancy-box','services-element'],
				],
			]
		);
		$this->add_control(
			'desktop_column',
			[
				'label' => esc_html__( 'Desktop Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_get_columns_list_desk(),				
			]
		);
		$this->add_control(
			'tablet_column',
			[
				'label' => esc_html__( 'Tablet Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),				
			]
		);
		$this->add_control(
			'mobile_column',
			[
				'label' => esc_html__( 'Mobile Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => theplus_get_columns_list(),				
			]
		);		
		$this->add_responsive_control(
			'columns_gap',
			[
				'label' => esc_html__( 'Columns Gap/Space Between', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],				
				'separator' => 'before',				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .service-item-loop,
					{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style!' => ['image-accordion','sliding-boxes','portfolio'],
				],
			]
		);
		$this->end_controls_section();
		/*columns*/
		/*title style*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .asb-title',
			]
		);
		$this->start_controls_tabs( 'tabs_title_style' );
		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'main_style' => ['hover-section','portfolio'],
				],
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .asb-title',
			]
		);
		$this->add_control(
			'title_stroke_color',
			[
				'label' => esc_html__('Stroke Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
			]
		);
		$this->add_control(
			'title_stroke_size',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Stroke Size', 'theplus'),
				'size_units' => [ 'px' ],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-title' => '-webkit-text-stroke: {{SIZE}}{{UNIT}} {{title_stroke_color.VALUE}};text-stroke: {{SIZE}}{{UNIT}} {{title_stroke_color.VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
				'condition' => [
					'main_style' => ['hover-section','portfolio'],
				],
			]
		);
		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.hover-section .service-item-loop.active-hover .asb-title,{{WRAPPER}} .pt_plus_asb_wrapper.portfolio .service-item-loop.active-port .asb-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'main_style' => ['hover-section','portfolio'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow_hover',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.hover-section .service-item-loop.active-hover .asb-title,{{WRAPPER}} .pt_plus_asb_wrapper.portfolio .service-item-loop.active-port .asb-title',
			]
		);
		$this->add_control(
			'title_stroke_color_hover',
			[
				'label' => esc_html__('Stroke Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
			]
		);
		$this->add_control(
			'title_stroke_size_hover',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Stroke Size', 'theplus'),
				'size_units' => [ 'px' ],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.hover-section .service-item-loop.active-hover .asb-title,{{WRAPPER}} .pt_plus_asb_wrapper.portfolio .service-item-loop.active-port .asb-title' => '-webkit-text-stroke: {{SIZE}}{{UNIT}} {{title_stroke_color_hover.VALUE}};text-stroke: {{SIZE}}{{UNIT}} {{title_stroke_color_hover.VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'title_link_heading',
			[
				'label' => esc_html__( 'Set onclick Title Link Text,Work Only < 1024 screen', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'main_style' => ['portfolio'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_link_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.portfolio .pf_a_click',
				'condition' => [
					'main_style' => ['portfolio'],
				],
			]
		);
		$this->add_control(
			'title_link_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio .pf_a_click' => 'color: {{VALUE}};',
				],
				'condition' => [
					'main_style' => ['portfolio'],
				],
			]
		);
		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h6',
				'options' => theplus_get_tags_options(),
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*title style*/
		/*sub title style*/
		$this->start_controls_section(
            'section_sub_title_styling',
            [
                'label' => esc_html__('Sub Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'main_style!' => ['portfolio'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sub_title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .asb-sub-title',
			]
		);
		$this->add_control(
			'sub_title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-sub-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sub_title_tag',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h6',
				'options' => theplus_get_tags_options(),
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*sub title style*/
		/*description style*/
		$this->start_controls_section(
            'section_desc_styling',
            [
                'label' => esc_html__('Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'main_style!' => ['portfolio'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .asb-desc,{{WRAPPER}} .pt_plus_asb_wrapper .asb-desc p',
			]
		);		
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-desc,{{WRAPPER}} .pt_plus_asb_wrapper .asb-desc p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*description style*/
		/*icon style*/
		$this->start_controls_section(
            'section_icon_styling',
            [
                'label' => esc_html__('Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'loop_display_icon_image' => 'yes',	
					'main_style!' => ['services-element','portfolio'],	
				],	
            ]
        );
		$this->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon Styles', 'theplus' ),
				'type' => Controls_Manager::SELECT,				
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'square' => esc_html__( 'Square', 'theplus' ),
					'rounded' => esc_html__( 'Rounded', 'theplus' ),
					'hexagon' => esc_html__( 'Hexagon', 'theplus' ),
					'pentagon' => esc_html__( 'Pentagon', 'theplus' ),
					'square-rotate' => esc_html__( 'Square Rotate', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
            'icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon/Image Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_asb_wrapper img.asb-icon-image,{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_responsive_control(
            'icon_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image' => 'width: {{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;line-height: {{SIZE}}{{UNIT}} !important;text-align: center;',					
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_icon_style' );
		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color_option',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false, 
				'default' => 'solid',
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image:before,{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image i:before' => 'color: {{VALUE}};background: transparent;-webkit-background-clip: unset;-webkit-text-fill-color: initial;',
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_color_option' => 'solid',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
            'icon_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'icon_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'icon_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image:before,{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image i:before' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}});-webkit-transition: all 0.3s linear;-moz-transition: all 0.3s linear;-o-transition: all 0.3s linear;-ms-transition: all 0.3s linear;transition: all 0.3s linear;',
				],
				'condition'    => [
					'icon_color_option' => 'gradient',
					'icon_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
				'separator' => 'after',
			]
        );
		$this->add_control(
            'icon_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image:before,{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image i:before' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{icon_gradient_color1.VALUE}} {{icon_gradient_color1_control.SIZE}}{{icon_gradient_color1_control.UNIT}}, {{icon_gradient_color2.VALUE}} {{icon_gradient_color2_control.SIZE}}{{icon_gradient_color2_control.UNIT}});-webkit-transition: all 0.3s linear;-moz-transition: all 0.3s linear;-o-transition: all 0.3s linear;-ms-transition: all 0.3s linear;transition: all 0.3s linear;',
				],
				'condition' => [
					'icon_color_option' => 'gradient',
					'icon_gradient_style' => 'radial',
				],
				'of_type' => 'gradient',
				'separator' => 'after',	
			]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image,{{WRAPPER}} .pt_plus_asb_wrapper img.asb-icon-image' => 'border:1px solid {{VALUE}}',					
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image,{{WRAPPER}} .pt_plus_asb_wrapper img.asb-icon-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .asb-icon-image,{{WRAPPER}} .pt_plus_asb_wrapper img.asb-icon-image',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*icon style*/
		/*icon service element style*/
		$this->start_controls_section(
            'section_icon_se_styling',
            [
                'label' => esc_html__('Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'loop_display_icon_image' => 'yes',	
					'main_style' => ['services-element'],	
				],
            ]
        );
		$this->add_responsive_control(
            'icon_se_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon/Image Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 400,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper .asb-icon-image' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper img.asb-icon-image.asb-image,{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper .asb-icon-image svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_control(
			'icon_se_bg_border_color',
			[
				'label' => esc_html__( 'Icon Inset Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .services-element.services-element-style-2 .se-icon' => 'box-shadow:inset 0 0 0 2px {{VALUE}};',
					'{{WRAPPER}} .services-element.services-element-style-2 .se-wrapper:hover .se-icon' => 'box-shadow:inset 0 0 0 40px {{VALUE}};',
				],
				'condition' => [					
					'services_element_style' => ['services-element-style-2'],	
				],
			]
		);
		$this->start_controls_tabs( 'tabs_icon_se_style' );
		$this->start_controls_tab(
			'tab_icon_se_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'icon_se_normal_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-icon .asb-icon-image' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-icon .asb-icon-image svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .services-element.services-element-style-2 .se-wrapper-inner:after' => 'background-color: {{VALUE}}18;',					
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_icon_se_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'icon_se_hover_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper:hover .asb-icon-image' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper:hover .asb-icon-image svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*icon service element style*/
		/*lottie style start*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
            'lottiedisplay', 
			[
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Display', 'theplus'),
                'default' => 'inline-block',
                'options' => [
					'block'  => esc_html__( 'Block', 'theplus' ),
					'inline-block'  => esc_html__( 'Inline Block', 'theplus' ),
					'flex'  => esc_html__( 'Flex', 'theplus' ),
					'inline-flex'  => esc_html__( 'Inline Flex', 'theplus' ),
					'initial'  => esc_html__( 'Initial', 'theplus' ),
					'inherit'  => esc_html__( 'Inherit', 'theplus' ),
				],
            ]
        );
		$this->add_responsive_control(
			'lottieWidth',
			[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
			]
		);
		$this->add_responsive_control(
			'lottieHeight',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
			]
		);
		$this->add_responsive_control(
			'lottieSpeed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'lottieLoop',
			[
				'label' => esc_html__( 'Loop Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lottiehover',
			[
				'label' => esc_html__( 'Hover Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*lottie style end*/
		/*service element listing style*/
		$this->start_controls_section(
            'section_se_listing_styling',
            [
                'label' => esc_html__('Listing', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'main_style' => ['services-element'],	
				],				
            ]
        );
		$this->add_control(
            'content_se_margin_st1',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin Bottom', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .services-element.services-element-style-1 .se-liting-ul' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],				
				'condition' => [					
					'services_element_style' => ['services-element-style-1'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'se_listing_typography',
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-listing',
			]
		);
		$this->add_control(
			'se_listing_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-listing' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'se_listing_dot_color',
			[
				'label' => esc_html__( 'Dot Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .services-element.services-element-style-2 .se-listing:before' => 'box-shadow:0 0 0 2px {{VALUE}};',
					'{{WRAPPER}} .services-element.services-element-style-2 .se-listing:hover:before' => 'box-shadow:0 0 0 3px {{VALUE}};',
				],
				'condition' => [					
					'services_element_style' => ['services-element-style-2'],	
				],	
			]
		);
		$this->end_controls_section();
		/*service element listing style*/
		/*button style*/
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'loop_display_button' => 'yes',
				],
            ]
        );
		$this->add_control(
            'button_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Button Above Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt-plus-button-wrapper' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'loop_display_button' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button .button-link-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button .button-link-wrap',				
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-7 .button-link-wrap:after' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap',
				'separator' => 'after',
				'condition' => [
					'loop_button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'loop_button_style' => ['style-8'],
				],
			]
		);
		$this->add_responsive_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'loop_button_style' => ['style-8'],
					'button_border_style!' => 'none',
				]
			]
		);
		$this->add_control(
		'button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'loop_button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'loop_button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '
							   {{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap',
				'condition' => [
					'loop_button_style' => ['style-8'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'btn_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button .button-link-wrap:hover,{{WRAPPER}} .pt_plus_asb_wrapper:hover .pt_plus_button .hover_box_button' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_asb_wrapper:hover .pt_plus_button .hover_box_button',
				'separator' => 'after',
				'condition' => [
					'loop_button_style!' => ['style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_asb_wrapper:hover .pt_plus_button .hover_box_button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'loop_button_style' => ['style-8'],
					'button_border_style!' => 'none'
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'button_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_asb_wrapper:hover .pt_plus_button .hover_box_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'loop_button_style' => ['style-8'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_asb_wrapper:hover .pt_plus_button .hover_box_button',
				'condition' => [
					'loop_button_style' => ['style-8'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*button style*/
		/*feature image style*/
		$this->start_controls_section(
            'section_feature_image_styling',
            [
                'label' => esc_html__('Featured Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
					
				],
            ]
        );
		$this->add_responsive_control(
			'feature_image_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
				],			
			]
		);
		$this->add_responsive_control(
			'feature_image_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
            'featured_img_height_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
						'step' => 5,
					],
				],
				'render_type' => 'ui',
				'separator' => 'before',				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-image' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
				],	
            ]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'feature_image_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-image',
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'feature_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],				
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
				],	
			]
		);
		$this->add_control(
			'feature_image_n_heading',
			[
				'label' => 'Normal',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'fi_n_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-hover-wrapper .portfolio-hover-image',
				'condition' => [
					'main_style' => ['portfolio'],
					'portfolio_style' => ['portfolio-style-1'],
				],
			]
		);
		$this->end_controls_section();
		/*content style*/
		$this->start_controls_section(
            'section_content_styling',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion .asb-content,{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-content,
					{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-content-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-content-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-back-content-inner,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content,{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'main_style','operator' => '==','value'    => 'image-accordion',
								],
								[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],
								[
									'name'     => 'main_style','operator' => '==','value'    => 'info-banner',
								],
								[
									'terms' => [
										[
											'name'  => 'main_style','value' => 'services-element',
										],
										[
											'name'  => 'services_element_style','value' => 'services-element-style-2',
										],
									],
								],
								[
									'terms' => [
										[
											'name'  => 'main_style','value' => 'portfolio',
										],
										[
											'name'  => 'portfolio_style','value' => 'portfolio-style-1',
										],
									],
								],
							],
						],
					],
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'content_se_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Padding', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .services-element.services-element-style-1 .se-wrapper' => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .services-element.services-element-style-1 .se-listing-section' => 'padding: 0 {{SIZE}}{{UNIT}};',
				],				
				'condition' => [
					'main_style' => ['services-element'],
					'services_element_style' => ['services-element-style-1'],
				],
            ]
        );
		$this->add_responsive_control(
            'content_margin',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion.accordion-style-2 .service-item-loop' => 'margin: calc({{SIZE}}{{UNIT}}  / 2)',
				],				
				'condition' => [
					'main_style' => ['image-accordion'],
					'image_accordion_style' => ['accordion-style-2'],
				],
            ]
        );
		$this->add_responsive_control(
			'content_article_box_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.article-box.article-box-style-1 .article-overlay,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-1 .service-item-loop,
					{{WRAPPER}} .pt_plus_asb_wrapper.portfolio.portfolio-style-1 .portfolio-content-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main,
					{{WRAPPER}} .pt_plus_asb_wrapper.services-element.services-element-style-2 .service-item-loop' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
		'conditions'   => [
			'terms' => [
				[
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'main_style',
							'operator' => '==',
							'value'    => 'portfolio',
						],
						[
							'terms' => [
								[
									'name'  => 'main_style',
									'value' => 'info-banner',
								],
								[
									'name'  => 'info_banner_style',
									'value' => 'info-banner-style-2',
								],
							],
						],
						[
							'terms' => [
								[
									'name'  => 'main_style',
									'value' => 'services-element',
								],
								[
									'name'  => 'services_element_style',
									'value' => 'services-element-style-2',
								],
							],
						],
						[
							'terms' => [
								[
									'name'  => 'main_style',
									'value' => 'article-box',
								],
								[
									'name'  => 'article_box_style',
									'value' => 'article-box-style-1',
								],
							],
						],	
					],
				],
			],
		],
				'separator' => 'after',
			]
		);	
		$this->add_control(
			'hover_sec_bg_overlay',
			[
				'label' => esc_html__( 'Background Overlay', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'main_style' => ['hover-section'],
				],
			]
		);
		$this->add_control(
			'hover_sec_bg_overlay_opacity',
			[
				'label' => esc_html__( 'Opacity (0-100)', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 20,
				'condition'    => [
					'main_style' => ['hover-section'],
				],
			]
		);
		$this->add_control(
			'content_hover_background_head',
			[
				'label' => esc_html__( 'Hover Overlay Background Color', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'main_style' => ['image-accordion'],
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'article_content_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_asb_wrapper.article-box.article-box-style-1 .article-overlay',
				'condition'   => [
					'main_style' => 'article-box',
					'article_box_style' => 'article-box-style-1',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion .service-item-loop.active_accrodian .asb-content,{{WRAPPER}} .pt_plus_asb_wrapper.sliding-boxes .service-item-loop .asb-content,{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-content-wrapper,{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content',
				'condition'   => [
					'main_style' => ['image-accordion','sliding-boxes','info-banner'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.sliding-boxes .service-item-loop,{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper,
				{{WRAPPER}} .pt_plus_asb_wrapper.article-box.article-box-style-1 .article-overlay,
				{{WRAPPER}} .pt_plus_asb_wrapper.article-box-style-2 .article-box-main-wrapper,
				{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-content-wrapper,
				{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content',
				'condition' => [
					'main_style' => ['sliding-boxes','fancy-box','article-box','info-banner'],					
				],
			]
		);
		$this->add_responsive_control(
			'content_button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],				
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.sliding-boxes .service-item-loop,{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.article-box.article-box-style-1 .article-overlay,
					{{WRAPPER}} .pt_plus_asb_wrapper.article-box-style-2 .article-box-main-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner.info-banner-style-1 .info-banner-content-wrapper,
					{{WRAPPER}} .pt_plus_asb_wrapper.info-banner-style-2 .info-front-content,{{WRAPPER}} .pt_plus_asb_wrapper.image-accordion .service-item-loop' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['image-accordion','sliding-boxes','fancy-box','article-box','info-banner'],					
				],
				
			]
		);
		$this->add_control(
			'fb_hover_overlay_color',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'main_style' => ['fancy-box'],
				],
			]
		);
		$this->add_control(
			'fb_hover_overlay_color_n',
			[
				'label' => esc_html__( 'Normal Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper .fancybox-image-background' => 'box-shadow: {{VALUE}} 0 0 0 2000px inset;',
				],
				'condition' => [
					'main_style' => ['fancy-box'],
				],
			]
		);
		$this->add_control(
			'fb_hover_overlay_color_h',
			[
				'label' => esc_html__( 'Hover Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper:hover .fancybox-image-background' => 'box-shadow: {{VALUE}} 0 0 0 2000px inset;',
				],
				'condition' => [
					'main_style' => ['fancy-box'],
				],
			]
		);
		$this->add_control(
			'fb_hover_underline',
			[
				'label' => esc_html__( 'Hover Bottom Underline', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'main_style' => ['fancy-box'],
				],
			]
		);
		$this->add_control(
			'fb_hover_underline_color',
			[
				'label' => esc_html__( 'Bottom Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'main_style' => ['fancy-box'],
				],
			]
		);
		$this->add_responsive_control(
            'fb_hover_underline_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Line Height', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.fancy-box .fancybox-inner-wrapper:after' => 'height: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'main_style' => ['fancy-box'],
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_content_style' );
		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								
								[
									'name'     => 'main_style','operator' => '==','value'    => 'services-element',
								],								
							],
						],
					],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'se_background_n',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.services-element.services-element-style-1 .se-wrapper,
				{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main',
				'condition'    => [
					'main_style' => ['services-element'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_service_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main',
				'condition' => [
					'main_style' => ['services-element'],
					'services_element_style' => ['services-element-style-2'],
				],
			]
		);
		$this->add_responsive_control(
			'content_service_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],				
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'main_style' => ['services-element'],
					'services_element_style' => ['services-element-style-2'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								/*[
									'name'     => 'main_style','operator' => '==','value'    => 'info-banner',
								],*/
								[
									'name'     => 'main_style','operator' => '==','value'    => 'services-element',
								],
								/*[
									'name'     => 'main_style','operator' => '==','value'    => 'fancy-box',
								],
								[
									'terms' => [
										[
											'name'  => 'main_style','value' => 'article-box',
										],
										[
											'name'  => 'article_box_style','value' => 'article-box-style-2',
										],
									],
								],*/
							],
						],
					],
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'se_background_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt_plus_asb_wrapper.services-element.services-element-style-1 .se-listing-section,
				{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main:hover',
				'condition'    => [
					'main_style' => ['services-element'],
				],
			]
		);
		$this->add_control(
			'content_service_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_asb_wrapper.services-element .se-wrapper-main:hover' => 'border-color: {{VALUE}};',
				],
				'condition'    => [
					'main_style' => ['services-element'],
					'services_element_style' => ['services-element-style-2'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_section();
		/*content style*/
		/*content background back*/
		$this->start_controls_section(
            'ib_cb_bg_back',
            [
                'label' => esc_html__('Content Background Back', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'main_style' => ['info-banner'],
					'info_banner_style' => ['info-banner-style-1'],
				],
            ]
        );
		$this->add_control(
			'overlay_color_bg',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .info-banner.info-banner-style-1 .info-banner-content-wrapper .info-banner-back-content' => 'box-shadow: {{VALUE}} 0 0 0 2000px inset;',
				],
				'condition'    => [
					'main_style' => ['info-banner'],
					'info_banner_style' => ['info-banner-style-1'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ib_back_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .info-banner.info-banner-style-1 .info-banner-content-wrapper .info-banner-back-content',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*content background back*/
		/*outer content start*/
		/*content style*/
		$this->start_controls_section(
            'section_outer_content_styling',
            [
                'label' => esc_html__('Outer Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'main_style' => 'article-box',
					'article_box_style' => 'article-box-style-1',
				],
            ]
        );
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'oc_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .article-box.article-box-style-1 .article-box-inner-content',
					'separator' => 'before',
				]
			);
			$this->add_responsive_control(
				'oc_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .article-box.article-box-style-1 .article-box-inner-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'oc_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .article-box.article-box-style-1 .article-box-inner-content',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*outer content end*/
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$main_style = $settings["main_style"];
		$image_accordion_style = $settings["image_accordion_style"];
		$sliding_boxes_style = $settings["sliding_boxes_style"];
		$article_box_style = $settings["article_box_style"];
		$info_banner_style = $settings["info_banner_style"];
		$hover_section_style = $settings["hover_section_style"];
		$hspi = $settings["hover_section_image_preload"];
		$fancy_box_style = $settings["fancy_box_style"];
		$services_element_style = $settings["services_element_style"];
		$portfolio_style = $settings["portfolio_style"];
		$orientation_type = $settings["orientation_type"];	
		$hover_orientation = $settings["hover_orientation"];
        $sb_tablet_column = $settings["sb_tablet_column"];	
		$sb_mobile_column = $settings["sb_mobile_column"];	
		$desktop_class=$tablet_class=$mobile_class='';
		if($main_style=='article-box' || $main_style=='info-banner' || $main_style=='hover-section' || $main_style=='fancy-box' || $main_style=='services-element'){
			if($settings['desktop_column']=='5'){
				$desktop_class='theplus-col-5';
			}else{
				$desktop_class='tp-col-lg-'.esc_attr($settings['desktop_column']);
			}
			$tablet_class='tp-col-md-'.esc_attr($settings['tablet_column']);
			$mobile_class='tp-col-sm-'.esc_attr($settings['mobile_column']).' tp-col-'.esc_attr($settings['mobile_column']);
		}
		
		$data_attr= $desktop_class.'  ' . $tablet_class.' '.$mobile_class;
		$sb_c=$style=$orientation='';
		if($main_style=='image-accordion' && !empty($image_accordion_style)){
			$style=$image_accordion_style;
			$orientation = $orientation_type;
		}else if($main_style=='sliding-boxes' && !empty($sliding_boxes_style)){
			$style=$sliding_boxes_style;
			$sb_c = $sb_tablet_column .' '.$sb_mobile_column;
		}else if($main_style=='article-box' && !empty($article_box_style)){
			$style=$article_box_style;
		}else if($main_style=='info-banner' && !empty($info_banner_style)){
			$style=$info_banner_style;
			$hover_orientation = $hover_orientation;
		}else if($main_style=='hover-section' && !empty($hover_section_style)){
			$style=$hover_section_style;
		}else if($main_style=='fancy-box' && !empty($fancy_box_style)){
			$style=$fancy_box_style;
		}else if($main_style=='services-element' && !empty($services_element_style)){
			$style=$services_element_style;
		}else if($main_style=='portfolio' && !empty($portfolio_style)){
			$style=$portfolio_style;
		}
		
		$flex_grow_value='';
		if($main_style=='image-accordion'){
			$flex_grow_value='data-flexgrow="'.esc_attr($settings['image_accordion_flex_grow']).'"';
		}
		
		$port_hover_color=$port_click_text='';
		$port_mobile_text = (!empty($settings['port_mobile_text'])) ? $settings['port_mobile_text'] : 'click here';
		if($main_style=='portfolio'){
			$port_hover_color='data-phcolor="'.esc_attr($settings['title_hover_color']).'"';
            $port_click_text='data-clicktext="'.esc_attr($port_mobile_text).'"';
		}
		
		$hover_sec_ovly='';
		if($main_style=='hover-section'){
			$hover_sec_ovly='data-hsboc="'.esc_attr($settings['hover_sec_bg_overlay']).esc_attr($settings['hover_sec_bg_overlay_opacity']).'"';
		}
		
		$loop_item=$first_port_img='';
		if(!empty($settings["loop_content"])) {
				$index=1;
			if($main_style=='portfolio' && $style == 'portfolio-style-1'){
				$loop_item .='<div class="portfolio-content-wrapper tp-col-md-6 tp-col-lg-6 tp-col-sm-12 tp-col-12">';
			}
			foreach($settings["loop_content"] as $item) {
				$featured_image = '';
			
			if ( ! empty( $item['featured_image']['id'] ) ) {				
				$id = $item['featured_image']['id'];
				$size = $settings['thumbnail_size'];
				$featured_image = wp_get_attachment_image_src($id, $size, true );
				$featured_image= $featured_image[0];

				$image_alt = get_post_meta($id, '_wp_attachment_image_alt', TRUE);
				if(!$image_alt){
					$image_alt = get_the_title($id);
				}else if(!$image_alt){
					$image_alt = 'Image';
				}
			}else{				
				$featured_image = $item['featured_image']['url'];
			}
				$title_tag = !empty($settings['title_tag']) ? $settings['title_tag'] : 'h6';
				$sub_title_tag = !empty($settings['sub_title_tag']) ? $settings['sub_title_tag'] : 'h6';

				$list=$description=$loop_title=$list_sub_title=$list_title=$list_img=$loop_button='';
				//title		
				if(!empty($item['loop_title'])){
					$loop_title= $item['loop_title'];
					if (!empty($item['loop_button_link']['url'])){
						$list_title = '<a class="asb-title-link" href="'.esc_url($item['loop_button_link']['url']).'"><'.esc_attr(theplus_validate_html_tag($title_tag)).' class="asb-title ">'.esc_html($loop_title).'</'.esc_attr(theplus_validate_html_tag($title_tag)).'></a>';						
					}else{
						$list_title = '<'.esc_attr(theplus_validate_html_tag($title_tag)).' class="asb-title">'.esc_html($loop_title).'</'.esc_attr(theplus_validate_html_tag($title_tag)).'>';
					}
				}
				//sub title
				$loop_sub_title = $item['loop_sub_title'];
				if($loop_sub_title !=''){
					 $list_sub_title='<'.esc_attr(theplus_validate_html_tag($sub_title_tag)).' class="asb-sub-title"> '.esc_html($loop_sub_title).' </'.esc_attr(theplus_validate_html_tag($sub_title_tag)).'>';
				}
				//description
				$loop_content_desc = $item['loop_content_desc'];
				if($loop_content_desc !=''){
					 $description='<div class="asb-desc"> '.wp_kses_post($loop_content_desc).' </div>';
				}
				
				//listing				
				$loop_content_list = $item['loop_content_list'];
				$se_listing='';
				if(!empty($loop_content_list) ){
				$array=explode("|",$loop_content_list);
					if(!empty($array[1])){
						$se_listing .='<ul class="se-liting-ul">';
						foreach($array as $value){							
							$se_listing .='<li class="se-listing" >'.wp_kses_post($value).'</li>';							
						}
						$se_listing .='</ul>';
					}else{
						$se_listing ='<ul class="se-liting-ul"><li class="se-listing" >'.wp_kses_post($loop_content_list).'</li></ul>';
					}
				}
				
				//list content
				$loop_content_list = $item['loop_content_list'];
				if($loop_content_list !=''){
					 $list='<div class="asb-list"> '.wp_kses_post($loop_content_list).' </div>';
				}
				
				//Icon style
				$asb_icon_style='';
				$icon_style=$settings["icon_style"];
				if($icon_style == 'square'){
					$asb_icon_style = 'icon-squre';
				} 
				if($icon_style == 'rounded'){
					$asb_icon_style = 'icon-rounded';
				} 	
				if($icon_style == 'hexagon'){
					$asb_icon_style = 'icon-hexagon';
				} 	
				if($icon_style == 'pentagon'){
					$asb_icon_style = 'icon-pentagon';
				}  	
				if($icon_style == 'square-rotate'){
					$asb_icon_style = 'icon-square-rotate';
				}
		
				if($settings['loop_display_icon_image'] == 'yes'){
					//Icon Image
					if(!empty($item['loop_image_icon']) && $item['loop_image_icon'] == 'image'){
						$image_alt='';
						if(!empty($item["loop_select_image"]["url"])){
							$loop_select_image=$item['loop_select_image']['id'];
							$img = wp_get_attachment_image_src($loop_select_image,$item['loop_select_image_thumbnail_size']);
							$loop_imgSrc = $img[0];
							
							$image_id=$item["loop_select_image"]["id"];
							$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
							if(!$image_alt){
								$image_alt = get_the_title($image_id);
							}else if(!$image_alt){
								$image_alt = 'Plus ASB icon';
							}
						}else{
							$loop_imgSrc='';
						}
												
						$list_img ='<img class="asb-icon-image asb-image '.esc_attr($asb_icon_style).'" src='.esc_url($loop_imgSrc).' alt="'.esc_attr($image_alt).'" />';
						
					//icon font
					}else if(!empty($item['loop_image_icon']) && $item['loop_image_icon'] == 'icon'){		
						if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome'){
							$icons=$item["loop_icon_fontawesome"];
						}else if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome_5'){
							ob_start();
							\Elementor\Icons_Manager::render_icon( $item['loop_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
							$icons = ob_get_contents();
							ob_end_clean();
						}else if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='icon_mind'){
							$icons=$item["loop_icons_mind"];
						}else{
							$icons='';
						}
						
						if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome_5'){
							$list_img = '<span class="asb-icon-image asb-icon '.esc_attr($asb_icon_style).'" >'.$icons.'</span>';
						}else{
							$list_img = '<i class=" '.esc_attr($icons).' asb-icon-image asb-icon '.esc_attr($asb_icon_style).'" ></i>';
						}
						
					}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'lottie'){
						$ext = pathinfo($item['lottieUrl']['url'], PATHINFO_EXTENSION);			
						if($ext!='json'){
							$list_img .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
						}else{
							$lottiedisplay = isset($settings['lottiedisplay']) ? $settings['lottiedisplay'] : 'inline-block';
							$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 50;
							$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 50;
							$lottieSpeed = isset($settings['lottieSpeed']['size']) ? $settings['lottieSpeed']['size'] : 1;
							$lottieLoop = isset($settings['lottieLoop']) ? $settings['lottieLoop'] : 'no';
							$lottiehover = isset($settings['lottiehover']) ? $settings['lottiehover'] : 'no';
							$lottieLoopValue='';
							if(!empty($settings['lottieLoop']) && $settings['lottieLoop']=='yes'){
								$lottieLoopValue ='loop'; 
							}
							$lottieAnim='autoplay';
							if(!empty($settings['lottiehover']) && $settings['lottiehover']=='yes'){
								$lottieAnim ='hover'; 
							}
							$list_img .= '<lottie-player src="'.esc_url($item['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
						}
					}	
				}
						
				//button
				$loop_button='';
				if($settings['loop_display_button'] == 'yes'){
					$link_key = 'link_' . $index;
					if ( ! empty( $item['loop_button_link']['url'] ) ) {
						$this->add_render_attribute( $link_key, 'href', $item['loop_button_link']['url'] );
						if ( $item['loop_button_link']['is_external'] ) {
							$this->add_render_attribute( $link_key, 'target', '_blank' );
						}
						if ( $item['loop_button_link']['nofollow'] ) {
							$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
						}
					}
					$this->add_render_attribute( $link_key, 'class', 'button-link-wrap' );
					$this->add_render_attribute( $link_key, 'role', 'button' );
					
					$button_style = $settings['loop_button_style'];
					$button_text = $item['loop_button_text'];
						$btn_uid=uniqid('btn');
						$data_class= $btn_uid;
						$data_class .=' button-'.$button_style.' ';
						
						if($button_style=='style-7'){
							$button_text =esc_html($button_text).'<span class="btn-arrow"></span>';
						}
						if($button_style=='style-8'){
							$button_text =esc_html($button_text);
						}
						if($button_style=='style-9'){
							$button_text =esc_html($button_text).'<span class="btn-arrow"><i class="fa-show fa fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fa fa-chevron-right" aria-hidden="true"></i></span>';
						}
						
					$loop_button ='<div class="pt-plus-button-wrapper">';
					$loop_button .='<div class="pt_plus_button '.esc_attr($data_class).'">';
						$loop_button .= '<div class="animted-content-inner">';
								$loop_button .='<a '.$this->get_render_attribute_string( $link_key ).'>';
									$loop_button .= $button_text;
								$loop_button .='</a>';
							$loop_button .='</div>';
						$loop_button .='</div>';
					$loop_button .='</div>';
				}
					if($settings['main_style']=='image-accordion'){
						if($index== $settings['active_slide']){
							$style_loop='style="flex-grow: '.esc_attr($settings['image_accordion_flex_grow']).';"';
							$active='active_accrodian';							
						}else{
							$style_loop='style="flex-grow: 1;"';
							$active='';							
						}
					}else{
						$style_loop='';
						$active='';
					}
					$active_class='';
					if($settings['main_style']=='sliding-boxes' && $index== $settings['active_slide']){
						$active_class="active-slide";
					}else if($settings['main_style']=='hover-section' && $index== 1){
						$active_class="active-hover";
					}
					if($index==1){
						$first_port_img=$featured_image;
						if($main_style=='portfolio' && ($style == 'portfolio-style-1' || $style == 'portfolio-style-2')){
							$active_class='active-port';
						}
					}
					
					$image_url=$click_url='';
					if($main_style=='portfolio' && ($style == 'portfolio-style-1' || $style == 'portfolio-style-2')){
						$image_url='data-url="'.esc_url($featured_image).'"';
						$click_url='data-clickurl="'.esc_url($item['loop_button_link']['url']).'"';
					}

					$loop_item .= '<div class="service-item-loop '.$data_attr.' '.esc_attr($active).' '.esc_attr($active_class).' '.esc_attr($sb_c).'" '.$style_loop.' '.$image_url.' '.$click_url.' '.$flex_grow_value.' '.$port_hover_color.' '.$hover_sec_ovly.' '.$port_click_text.'>';				
						if(!empty($style)){
							ob_start();
								include THEPLUS_PATH. 'includes/animated-service/' . sanitize_file_name($style) . '.php'; 
								$loop_item .= ob_get_contents();
							ob_end_clean();
						}
					$loop_item .='</div>';
				$index++;	
			}
			if($main_style=='portfolio' && $style == 'portfolio-style-1'){
				$loop_item .= '</div>';
				$loop_item .= '<div class="portfolio-hover-wrapper tp-col-md-6 tp-col-lg-6 tp-col-sm-12 tp-col-12"><div class="portfolio-hover-image" style="background:url('.$first_port_img.') center/cover"></div></div>';
			}
		}
		
		$data_attr ='';
		if($settings['active_slide']==0 && $settings['main_style']=='image-accordion'){
			$data_attr .= 'data-accordion-hover="yes"';
		}

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$uid=uniqid('ani_ser_box');			
		$ani_ser_box ='<div id='.esc_attr($uid).'" class="pt_plus_asb_wrapper '.esc_attr($main_style).' '.esc_attr($style).' '.esc_attr($orientation).' '.esc_attr($hover_orientation).' '.esc_attr($animated_class).'" '.$data_attr.' '.$animation_attr.'>';	
			if($settings['main_style']=='hover-section'){
				$ani_ser_box .='<div class="asb_wrap_list tp-row hover-section-extra">';
			}else if($settings['main_style']=='portfolio' && $style=='portfolio-style-2'){
				$ani_ser_box .='<div class="asb_wrap_list tp-row"><div class="portfolio-wrapper tp-col-md-12" style="background:url('.$first_port_img.') center/cover">';
			}else{
				$ani_ser_box .='<div class="asb_wrap_list tp-row">';
			}
			$ani_ser_box .=$loop_item;
			if($settings['main_style']=='portfolio' && $style=='portfolio-style-2'){
				$ani_ser_box .='</div>';
				$ani_ser_box .='</div>';
			}else{
				$ani_ser_box .='</div>';
			}
		$ani_ser_box .='</div>';
			
		
		echo $ani_ser_box;
		
	}
}