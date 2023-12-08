<?php 
/*
Widget Name: TP Custom Field
Description: Dynamic Content Custom Field Display
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography; 

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Custom_Field extends Widget_Base {
		
	public function get_name() {
		return 'tp-custom-field';
	}

    public function get_title() {
        return esc_html__('TP Custom Field', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-th theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-listing');
    }

	public function get_keywords() {
		return [ 'Custom Field', 'Dynamic Content', 'Dynamic Listing'];
	}

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Custom Field', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
				'custom-field-key',
				[
					'label' => __( 'Name', 'theplus' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( 'Enter Your Custom Field Name/Key', 'theplus' ),
					'default' => __( 'field_key', 'theplus' ),
				]
		);

		$this->add_control(
			'cfkey_type',
			[
				'label' => __( 'Field Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'text' => __( 'Default', 'theplus' ),					
					'image' => __( 'Image', 'theplus' ),
					'link' => __( 'Link', 'theplus' ),
					'video' => __( 'Video', 'theplus' ),
					'audio' => __( 'Audio', 'theplus' ),
					'date' => __( 'Date', 'theplus'),
					'html' => __( 'Html', 'theplus' ),
					'oembed' => __( 'oEmbed', 'theplus' ),
				],
				'default' => 'text'
			]
		);
		
		$this->add_control(
		        'key_link_type',
                [
                    'label' => __('Link Type', 'theplus'),
                    'type'  => Controls_Manager::SELECT,
                    'options' => [
                        'default' => __('Default Link', 'theplus'),
                        'email'   => __('Email', 'theplus'),
                        'tel'     => __('Telephone', 'theplus')
                    ],
                    'default'   => 'default',
                    'condition' => [
	                    'cfkey_type' => 'link',
                    ]
                ]
        );
		
		if(class_exists('acf')){
			$this->add_control(
					'field_acf_key',
					[
						'label'	=> __('ACF Field/Key','theplus'),
						'type'	=> Controls_Manager::SWITCHER,
						'label_on' => __( 'Yes', 'theplus' ),
						'label_off' => __( 'No', 'theplus' ),
						'condition' => [
							'cfkey_type' => ['text','link', 'audio', 'date']
						],
					]
			);
		}
		
		$this->add_control(
            'field_date_format',
            [
                'label' => __( 'Date format', 'theplus' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => theplus_date_format_list(),
                'default' => 'F j, Y',
                'condition' => [
                    'field_acf_key' => '',
                    'cfkey_type' => 'date'
                ],
                
            ]
        );
		
		$this->add_control(
            'date_custom_format',
            [
                'label' => __( 'Date Format', 'theplus' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter Date Format', 'theplus' ),
                'default' => 'y:m:d',
				'description' => 'Enter date and time formatting documentation : <a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"> Click here</a>',
                'condition' => [
                    'field_date_format' => 'custom'
                ]
            ]
        );
		
		//Link Options
		$this->add_control(
            'field_link_type',
            [
                'label' => __('Links To','theplus'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'static' => __('Static','theplus'),
                    'post' => __('Post', 'theplus'),
					'custom_field' => __('Custom Field','theplus'),
                ],
                'default'   => 'static',
                'condition' => [
                    'cfkey_type' => 'link',
                ]
            ]
        );

		$this->add_control(
			'field_link_text',
			[
				'label' => __( 'Static Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Link Text', 'theplus' ),
				'default' => __( 'Read More', 'theplus' ),
				'condition' => [
					'cfkey_type' => 'link',
                    'field_link_type' => 'static'
				]
			]
		);
        $this->add_control(
            'field_link_dynamic_text',
            [
                'label' => __( 'Enter Dynamic Field Key', 'theplus' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Enter Field Key', 'theplus' ),
                'default' => '',
                'condition' => [
                    'cfkey_type' => 'link',
                    'field_link_type' => 'custom_field'
                ],
                'description' => __( 'Custom field will be used for anchor text', 'theplus' )
            ]
        );

		$this->add_control(
			'field_link_target',
			[
				'label' => __( 'Open in new tab', 'theplus' ),
				'type'  => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'theplus' ),
				'label_off' => __( 'No', 'theplus' ),
				'condition' => [
					'cfkey_type' => ['link', 'image'],
				]
			]
		);

        $this->add_control(
            'field_link_download',
            [
                'label' => __( 'Download on Click', 'theplus' ),
                'type'  => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'theplus' ),
				'label_off' => __( 'No', 'theplus' ),
                'return_value' => 1,
                'default' => __('label_off', 'theplus'),
                'condition' => [
                    'cfkey_type' => ['link'],
                ]
            ]
        );
		
		$this->add_control(
			'field_label',
			[
				'label' => __( 'Label', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Label', 'theplus' ),
				'default' => '',
				'condition' => [
					'cfkey_type' => ['text','link', 'date'],
				]
			]
		);
		
		$this->add_control(
			'header_tag',
			[
				'label' => __( 'HTML Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => __( 'H1', 'theplus' ),
					'h2' => __( 'H2', 'theplus' ),
					'h3' => __( 'H3', 'theplus' ),
					'h4' => __( 'H4', 'theplus' ),
					'h5' => __( 'H5', 'theplus' ),
					'h6' => __( 'H6', 'theplus' ),
					'div' => __( 'div', 'theplus' ),
					'span' => __( 'span', 'theplus' ),
					'p' => __( 'p', 'theplus' ),
				],
				'default' => 'h3',
				'condition' => [
					'cfkey_type' => ['text', 'date'],
				]
			]
		);
		
		//alignment
		$this->add_responsive_control(
			'field_align',
			[
				'label' => __( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
				'condition'=> [
					'cfkey_type!'=>'video',
				]
			]
		);
		
		//Link Image Type
		$this->add_control(
            'field_links_to_image',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Image Link to', 'theplus'),
                'options' => [
                    ''  => __('None', 'theplus'),
                    'post' => __('Post Url', 'theplus'),
                    'media' => __('Full Image', 'theplus'),
                    'lightbox' => __('Light Box', 'theplus'),
                    'static' => __( 'Custom URL', 'theplus' ),
                    'custom_field' => __( 'Custom Field', 'theplus' ),
                ],
                'default' => '',
                'condition' => [
                    'cfkey_type' => 'image',
                ]
            ]
        );

		$this->add_control(
			'field_link_image',
			[
				'label' => __( 'Custom URL', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Custom URL', 'theplus' ),
				'default' => '',
				'condition' => [
					'cfkey_type' => 'image',
					'field_links_to_image' => 'static'
				]
			]
		);
		$this->add_control(
			'field_link_dynamic_image',
			[
				'label' => __( 'Enter Field Key', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Field Key', 'theplus' ),
				'default' => '',
				'condition' => [
					'cfkey_type' => 'image',
					'field_links_to_image' => 'custom_field'
				],
				'description' => __( 'Custom field will be used for image link', 'theplus')
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'field_image_size',
				'label' => __( 'Image Size', 'theplus' ),
				'default' => 'full',
				'exclude' => [ 'custom' ],
				'condition' => [
					'cfkey_type' => 'image',
				]
			]
		);
		
		//Video Type Options
		$this->add_control(
			'field_video_type',
			[
				'label' => __( 'Video Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'youtube' => __( 'Youtube Video', 'theplus' ),
					'vimeo' => __( 'Vimeo Video', 'theplus' ),
				],
				'default' => 'youtube',
				'condition' => [
					'cfkey_type' => 'video',
				]
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => __( 'Aspect Ratio', 'theplus' ),
				'type' => Controls_Manager::SELECT,				
				'options' => [
					'169' => '16:9',
					'43' => '4:3',
					'32' => '3:2',
				],
				'frontend_available' => true,
				'default' => '16-9',
				'condition' => [
					'cfkey_type' => 'video',
				]
			]
		);
		
		
		
		//youtube video options
		$this->add_control(
			'youtube_field_opt',
			[
				'label' => __( 'Youtube Video Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'field_yt_autoplay',
			[
				'label' => __( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,				
				'label_on' => __( 'Yes', 'theplus' ),
				'label_off' => __( 'No', 'theplus' ),
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'field_yt_controls',
			[
				'label' => __( 'Player Control', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'theplus' ),
				'label_on' => __( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'youtube',
				],
			]
		);
		
		$this->add_control(
			'field_yt_rel',
			[
				'label' => __( 'Youtube Suggested Videos', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,				
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'field_yt_showinfo',
			[
				'label' => __( 'Video Title & Actions', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'theplus' ),
				'label_on' => __( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'youtube',
				],
			]
		);
		
		//Vimeo Video Options
		$this->add_control(
			'vimeo_field_opt',
			[
				'label' => __( 'Vimeo Video Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
				],
			]
		);
		$this->add_control(
			'field_vimeo_bg',
			[
				'label' => __( 'Background Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'lablel_on' => __( 'Yes', 'theplus' ),
				'label_off' => __( 'No', 'theplus' ),
				'default' => '',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
				]
			]
		);
		$this->add_control(
			'field_vimeo_autoplay',
			[
				'label' => __( 'Vimeo Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'theplus' ),
				'label_on' => __( 'Yes', 'theplus' ),
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
					'field_vimeo_bg' => ''
				],
			]
		);
		$this->add_control(
			'field_vimeo_title',
			[
				'label' => __( 'Vimeo Intro Title', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
					'field_vimeo_bg' => ''
				],
			]
		);
		$this->add_control(
			'field_vimeo_loop',
			[
				'label' => __( 'Vimeo Loop', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'theplus' ),
				'label_on' => __( 'Yes', 'theplus' ),
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
					'field_vimeo_bg' => ''
				],
			]
		);
		$this->add_control(
			'field_vimeo_portrait',
			[
				'label' => __( 'Vimeo Intro Portrait', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
					'field_vimeo_bg' => ''
				],
			]
		);

		$this->add_control(
			'field_vimeo_byline',
			[
				'label' => __( 'Vimeo Intro Byline', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'theplus' ),
				'label_on' => __( 'Show', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
					'field_vimeo_bg' => ''
				],
			]
		);

		$this->add_control(
			'field_vimeo_color',
			[
				'label' => __( 'Controls Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT
                ],
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
					'field_vimeo_bg' => ''
				],
			]
		);

		$this->add_control(
			'field_vimeo_muted',
			[
				'label' => __( 'Muted', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'theplus' ),
				'lablel_on' => __( 'Yes', 'theplus' ),
				'default' => '',
				'condition' => [
					'cfkey_type' => 'video',
					'field_video_type' => 'vimeo',
				]
			]
        );
		
		$this->end_controls_section();
		
		/*text limit start*/
		$this->start_controls_section(
            'txt_fld_limit',
            [
                'label' => esc_html__('Text Field Limit', 'theplus'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'cfkey_type' => 'text',
				]
            ]
        );
		$this->add_control(
			'display_txt_limit',
			[
				'label' => esc_html__( 'Text Limit', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'display_text_by', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Limit on', 'theplus'),
                'default' => 'char',
                'options' => [
                    'char' => esc_html__('Character', 'theplus'),
                    'word' => esc_html__('Word', 'theplus'),                    
                ],
				'condition'   => [					
					'display_txt_limit'    => 'yes',
				],
            ]
        );
		$this->add_control(
			'display_txt_input',
			[
				'label' => esc_html__( 'Text Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,				
				'condition'   => [					
					'display_txt_limit'    => 'yes',
				],
			]
		);		
		$this->end_controls_section();
		/*text limit end*/
		
		/*style start*/
		
		/*Custom Field start*/
		$this->start_controls_section(
            'custom_field',
            [
                'label' => esc_html__('Custom Field Styling', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'ec_rtn_shop_heading',
			[
				'label' => esc_html__( 'Field Styling', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'cfkey_type' => ['text','html','link', 'date'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap',
				'condition' => [
					'cfkey_type' => ['text','html','link', 'date'],
				],
				
			]
		);
		$this->add_control(
			'field_color',
			[
				'label' => esc_html__( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap' => 'color: {{VALUE}}',
				],
				'condition' => [
					'cfkey_type' => ['text','html','link', 'date'],
				],
			]
		);
		$this->add_control(
			'field_h_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'cfkey_type' => ['text','link', 'date'],
				],
			]
		);
		$this->add_control(
			'cf_label_heading',
			[
				'label' => esc_html__( 'Label Styling', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'field_label!' => '',
					'cfkey_type' => ['text','link', 'date'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cf_label_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-field-wrapper .tp-field-label',
				'condition' => [
					'field_label!' => '',
					'cfkey_type' => ['text','link', 'date'],
				],
				
			]
		);
		$this->add_responsive_control(
			'cf_label_offset',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Right Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-field-wrapper .tp-field-label' => 'margin-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_label!' => '',
					'cfkey_type' => ['text','link', 'date'],
				],
			]
		);
		$this->add_control(
			'cf_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-field-wrapper .tp-field-label' => 'color: {{VALUE}}',
				],
				'condition' => [
					'field_label!' => '',
					'cfkey_type' => ['text','link', 'date'],
				],
			]
		);
		$this->add_responsive_control(
			'cf_img_size',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
							'min' => 1,
							'max' => 100,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap img' => 'width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					'cfkey_type' => ['image'],
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cf_img_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap img',
				'condition' => [					
					'cfkey_type' => ['image'],
				],
			]
		);
		$this->add_responsive_control(
			'cf_img_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'cfkey_type' => ['image'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cf_img_shadow',
				'selector' => '{{WRAPPER}} .tp-field-wrapper .plus-custom-field-wrap img',
				'condition' => [					
					'cfkey_type' => ['image'],
				],
			]
		);		
		$this->add_responsive_control(
			'cf_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-field-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'cf_bg',
				'types'     => [ 'classic', 'gradient' ],				
				'selector'  => '{{WRAPPER}} .tp-field-wrapper',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cf_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-field-wrapper',				
			]
		);
		$this->add_responsive_control(
			'cf_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-field-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cf_shadow',
				'selector' => '{{WRAPPER}} .tp-field-wrapper',				
			]
		);		
		$this->end_controls_section();
		/*Custom Field end*/
		
		/*style end*/
		
	}
	
	protected function limit_words($string, $word_limit){
		$words = explode(" ",$string);
		return implode(" ",array_splice($words,0,$word_limit));
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		
		if(!isset($settings['cfkey_type']) || $settings['cfkey_type'] == ''){
			$settings['cfkey_type'] = 'text';
		}

		$custom_field = $settings['custom-field-key'];
		
		$post_data = array();
	    if(!isset($GLOBALS['post'])){
		    return $post_data;
	    }
		
		$preview_id = $post_id='';
		if($GLOBALS['post']->post_type == 'elementor_library'){
			$post_id = $GLOBALS['post']->ID;
            $preview_id = get_post_meta($post_id,'tp_preview_post',true);
            if ($preview_id != '' && $preview_id != 0):
                $post_data = get_post($preview_id);
            else:
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 1
                );
                $get_data = get_posts( $args );
                $post_data = $get_data[0];
            endif;
		}else{
            $post_data = $GLOBALS['post'];
        }
		
		if(empty($post_data)){
            $post_data = get_post(0);
        }
		
		$post_id = $post_data->ID;
        $post_title = $post_data->post_title;
		
		//if(class_exists('acf') && in_array($settings['cfkey_type'],['text','link','audio', 'date']) && $settings['field_acf_key'] == 'yes'){
		if ( class_exists('acf') && (( in_array($settings['cfkey_type'], ['text','link','audio', 'date']) && $settings['field_acf_key'] == 'yes') || ($settings['cfkey_type'] === 'image') ) ) {
			$custom_field_val = get_field($custom_field,$post_id);			
		}else{
			$custom_field_val = get_post_meta($post_id,$custom_field,true);
		}
		
		$field_link_dynamic_text = '';
		if($settings['field_link_dynamic_text'] != '') {
			$field_link_dynamic_text = get_post_meta( $post_id, $settings['field_link_dynamic_text'], true );
		}

		$field_link_dynamic_image = '';
        if($settings['cfkey_type'] == 'image' && $settings['field_links_to_image'] == 'custom_field' && $settings['field_link_dynamic_image'] != ''){
	        $field_link_dynamic_image = get_post_meta( $post_id, $settings['field_link_dynamic_image'], true );
        }
		
		
		$repeater_field = theplus_acf_repeater_field_data();

		if($repeater_field['is_repeater_field']){
			if(isset($repeater_field['field'])){
				$field_data = get_field($repeater_field['field'], $post_id);
				if($field_data[0][$custom_field]){
					$custom_field_val = $field_data[0][$custom_field];
				}
				if($settings['cfkey_type'] == 'link' && $settings['field_link_dynamic_text'] != '') {
					$field_link_dynamic_text = $field_data[0][ $settings['field_link_dynamic_text'] ];
				}
				if($settings['cfkey_type'] == 'image' && $settings['field_links_to_image'] == 'custom_field' && $settings['field_link_dynamic_image'] != ''){
					$field_link_dynamic_image = $field_data[0][ $settings['field_link_dynamic_image'] ];
				}
			}else {
				$custom_field_val = get_sub_field($custom_field);
				if($settings['cfkey_type'] == 'link') {
					$field_link_dynamic_text = get_sub_field( $settings['field_link_dynamic_text'] );
				}
				if($settings['cfkey_type'] == 'image' && $settings['field_links_to_image'] == 'custom_field' && $settings['field_link_dynamic_image'] != ''){
					$field_link_dynamic_image = get_sub_field( $settings['field_link_dynamic_image'] );
				}
			}
		}
		
		if($settings['cfkey_type'] == 'text'){			
			if((!empty($settings['display_txt_limit']) && $settings['display_txt_limit']=='yes') && !empty($settings['display_txt_input'])){
					if(!empty($settings['display_text_by'])){				
						if($settings['display_text_by']=='char'){												
							$custom_field_val = substr($custom_field_val,0,$settings['display_txt_input']);								
						}else if($settings['display_text_by']=='word'){
							$custom_field_val = $this->limit_words($custom_field_val,$settings['display_txt_input']);					
						}
					}
				}else{				
					$custom_field_val =$custom_field_val;
				}
			}		
		$this->add_render_attribute( 'tp-field-class', 'class', 'tp-field-wrapper' );
		$this->add_render_attribute( 'tp-field-class', 'class', 'tp-field-'.$settings['cfkey_type'] );
		
		$this->add_render_attribute( 'tp-custom-field-class', 'class', 'plus-custom-field-wrap' );
		
		if(empty($custom_field_val)){
		    $this->add_render_attribute('tp-field-class','class','hide');
        }

		if($settings['field_link_download'] == '1'){
            $this->add_render_attribute( 'tp-custom-field-class', 'download', '' );
        }
		
		if($settings['field_link_target'] == 'yes'){
			$this->add_render_attribute( 'tp-custom-field-class', 'target', '_blank' );
		}
		
		$cfkey_type = $settings['cfkey_type'];
		$widget_id = $this->get_id();
		$field_html = '';
		switch ($cfkey_type) {

			case "image":	
				$post_image_size = $settings['field_image_size_size'];
				
				$image_type_select = $settings['field_links_to_image'];				
				if($image_type_select == 'post'){
					$post_permalink = get_permalink($post_id );
				}elseif($image_type_select == 'media'){
					$image_link = wp_get_attachment_image_src($custom_field_val ,'full');
					$post_permalink = $image_link[0];
				}else if($image_type_select == 'lightbox'){
					$image_link = wp_get_attachment_image_src($custom_field_val ,'full');
					$post_permalink = $image_link[0];
				}elseif($image_type_select == 'static'){
					$post_permalink = $settings['field_link_image'];
				}elseif($image_type_select == 'custom_field'){
					$post_permalink = $field_link_dynamic_image;
				}

				if(is_numeric($custom_field_val)){
					$field_html =  '<div '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'>';
					if($image_type_select != '') {
						$target = '';
						if(!empty($settings['field_link_target']) && $settings['field_link_target'] == 'yes'){
							$target = ' target="_blank"';
						}
						$field_html .= '<a href="' . esc_url($post_permalink) . '" title="' . $post_title . '"' . $target . '>';
					}
					$field_html .=  wp_get_attachment_image( $custom_field_val, $post_image_size );
					if($image_type_select != ''){
						$field_html .= '</a>';
					}
					$field_html .= '</div>';
				}else if(is_array($custom_field_val)){
				
					//if(!isset($custom_field_val[$post_image_size])){
					if(!isset($custom_field_val['sizes'][$post_image_size])){						
						if(!empty($custom_field_val['guid'])){
							$custom_field_val = $custom_field_val['guid'];
						}else{
							$custom_field_val = $custom_field_val['url'];
						}
						
					}else{
						//$custom_field_val = $custom_field_val[$post_image_size];
						$custom_field_val = $custom_field_val['sizes'][$post_image_size];
					}
					$target = '';
					if(!empty($settings['field_link_target']) && $settings['field_link_target'] == 'yes'){
						$target = ' target="_blank"';
					}
					$field_html =  '<div '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'>';
					if($image_type_select == 'lightbox') {
						$field_html .= '<a href="' . esc_url($custom_field_val) . '" '.$target.' title="' . $post_title . '">';
					}else if($image_type_select != '') {
						$field_html .= '<a href="' . esc_url($post_permalink) . '" '.$target.' title="' . $post_title . '">';
					}
					$field_html .= '<img src="'.$custom_field_val.'" />';
					if($image_type_select != '' || $image_type_select == 'lightbox'){
						$field_html .= '</a>';
					}
					$field_html .= '</div>';
				}else{
					$field_html =  '<div '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'>';
					if($image_type_select != '') {
						$target = '';
						if(!empty($settings['field_link_target']) && $settings['field_link_target'] == 'yes'){
							$target = ' target="_blank"';
						}
						if(!empty($post_permalink)){
							$field_html .= '<a href="' . esc_url($post_permalink) . '" '.$target.' title="' . $post_title . '">';
						}else{
							$field_html .= '<a href="' . esc_url($custom_field_val) . '" '.$target.' title="' . $post_title . '">';
						}
						
					}
					$field_html .= '<img src="'.$custom_field_val.'" />';
					if($image_type_select != ''){
						$field_html .= '</a>';
					}
					$field_html .= '</div>';
				}

				break;

			// case "link":	
			// 	if(is_array($custom_field_val)){
			// 		$custom_field_val = $custom_field_val['url'];
			// 	}
			// 	if($settings['key_link_type'] == 'email'){
			// 		$custom_field_val = 'mailto:'.$custom_field_val;
			// 	}elseif($settings['key_link_type'] == 'tel'){
			// 		$custom_field_val = 'tel:'.$custom_field_val;
			// 	}
				
			// 	if(!empty($settings['field_link_text']) && $settings['field_link_type'] == 'static'){
			// 		$static_text = ($settings['field_link_text']) ? $settings['field_link_text'] : __('Read More','theplus');
			// 		$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'  >'.$static_text.'</a>';
			// 	}else if(!empty($field_link_dynamic_text) && $settings['field_link_type'] == 'custom_field') {
			// 		$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).' >'.$field_link_dynamic_text.'</a>';
			// 	}else{
			// 		if($settings['key_link_type'] != 'default'){
			// 			$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'>'. get_post_meta($post_id,$custom_field,true) .'</a>';
			// 		}else{
			// 			$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).' >'.$custom_field_val.'</a>';
			// 		}
			// 	}

				case "link":
				$custom_field_val_array = false;
				if(is_array($custom_field_val)){
				$custom_field_val_array = $custom_field_val;
				$custom_field_val = $custom_field_val['url'];
				}
				if($settings['key_link_type'] == 'email'){
				$custom_field_val = 'mailto:'.$custom_field_val;
				}elseif($settings['key_link_type'] == 'tel'){
				$custom_field_val = 'tel:'.$custom_field_val;
				}
			
			
				if(!empty($settings['field_link_text']) && $settings['field_link_type'] == 'static'){
					$static_text = ($settings['field_link_text']) ? $settings['field_link_text'] : __('Read More','theplus');
					$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).
									'  >'.$static_text.'</a>';
				}else if(!empty($field_link_dynamic_text) && $settings['field_link_type'] == 'custom_field') {
					$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).' >'.$field_link_dynamic_text.'</a>';
				}else{
					if($settings['key_link_type'] != 'default'){
						$field_html = '<a href="'.$custom_field_val.'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'>'. get_post_meta($post_id,$custom_field,true) .'</a>';
					}else{
						if (is_array($custom_field_val_array)) {
							$field_html = '<a href="' . $custom_field_val . '" ' . $this->get_render_attribute_string('tp-custom-field-class') .(isset($custom_field_val_array['target']) ? ' target="'.$custom_field_val_array['target'].'" ': ' ') .' >' . (!empty($custom_field_val_array['title']) ? $custom_field_val_array['title'] : $custom_field_val) . '</a>';
						} else {
							$field_html = '<a href="' . $custom_field_val . '" ' . $this->get_render_attribute_string('tp-custom-field-class') .' >' . $custom_field_val . '</a>';
						}
					}
				}

				if($settings['field_link_type'] == 'post'){
					$field_html = '<a href="'. get_permalink($post_id) .'" '.$this->get_render_attribute_string( 'tp-custom-field-class' ).' >'.$custom_field_val.'</a>';
				}

				break;

			

			case "video":  
				add_filter( 'oembed_result', [ $this, 'theplus_video_embed_field' ], 55, 3 );
				
				$field_html = wp_oembed_get( $custom_field_val, wp_embed_defaults() );
				$argument = "['".$widget_id."','".$settings['aspect_ratio']."']";
				$field_html .= "<script type='text/javascript'>
					jQuery(document).ready(function(){
						jQuery(document).trigger('elementor/render/tp-field-video',".$argument.");
					});
					
					jQuery(window).on('resize',function() {
						jQuery(document).trigger('elementor/render/tp-field-video',".$argument.");
					});
					jQuery(document).trigger('elementor/render/tp-field-video',".$argument.");
					</script>";
				
				remove_filter( 'oembed_result', [ $this, 'theplus_video_embed_field' ], 55 );
				
				break;

			case "audio":
				$field_html = wp_audio_shortcode([ 'src' => $custom_field_val ]);
				break;
			
			case "date"  : 	
				if(empty($custom_field_val)){
					break;
				}

				if(!empty($settings['field_acf_key'])){
					$format = "g:i A";
					if($settings['field_date_format']=='custom') {
						$format = $settings['date_custom_format'];
					}elseif($settings['field_date_format'] == 'default'){
						$format = get_option( 'date_format' );
					}else{
						$format = $settings['field_date_format'];
					}
					
					$custom_field_val = str_replace('/', '-', $custom_field_val );
					$field_html = date( $format, strtotime( $custom_field_val ) );
				}else{
					$field_html = $custom_field_val;
				}
				$field_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', theplus_validate_html_tag($settings['header_tag']), $this->get_render_attribute_string( 'tp-custom-field-class' ), do_shortcode($field_html) );
				
				break;

			case "html": 	
				if(!empty($custom_field_val)){
					$field_html = '<div '.$this->get_render_attribute_string( 'tp-custom-field-class' ).'>'.wpautop(do_shortcode($custom_field_val)).'</div>';
				}
				break;
				
			case "oembed":
				if($repeater_field['is_repeater_field']){
				    $field_html = $custom_field_val;
                }else{
					$field_html = wp_oembed_get( $custom_field_val, wp_embed_defaults() );
                }
				break;
				
			default:
				$field_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', theplus_validate_html_tag($settings['header_tag']), $this->get_render_attribute_string( 'tp-custom-field-class' ), do_shortcode($custom_field_val) );
				break;
		}
		
		$output = '<div '.$this->get_render_attribute_string('tp-field-class').'>';
            if(($settings['cfkey_type']=='text') || ($settings['cfkey_type']=='link') || ($settings['cfkey_type']=='date')){
                if(!empty($settings['field_label']) && !empty($custom_field_val)){
                    $output .= '<span class="tp-field-label">'.$settings['field_label'].'</span>';
                }
            }
            $output .= $field_html;
        $output .= '</div>';
		
		echo $output;
	}
	
	public function theplus_video_embed_field($html){
		$settings = $this->get_settings();

		$data_params = [];

		if ( $settings['field_video_type'] === 'youtube'  ) {
			$yt_options = [ 'autoplay', 'controls', 'rel', 'showinfo' ];

			foreach ( $yt_options as $option ) {
				$value = ( 'yes' === $settings[ 'field_yt_' . $option ] ) ? '1' : '0';
				$data_params[ $option ] = $value;
			}

			$data_params['wmode'] = 'opaque';
		}

		if ( $settings['field_video_type'] === 'vimeo' ) {
			$vimeo_options = [ 'autoplay', 'loop', 'portrait', 'title', 'byline', 'muted', 'bg' ];

			foreach ( $vimeo_options as $option ) {
				
				$value = ( 'yes' === $settings[ 'field_vimeo_' . $option ] ) ? '1' : '0';
				$data_params[ $option ] = $value;
				if($settings['field_vimeo_bg' ] == 'yes'){					
					unset($data_params ['loop']);
					unset($data_params ['autoplay']);
					unset($data_params ['title']);
				}
			}

			$data_params['color'] = str_replace( '#', '', $settings['field_vimeo_color'] );
		}

		if ( ! empty( $data_params ) ) {
			preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $html, $matches );
			$embed_url = esc_url( add_query_arg( $data_params, $matches[1] ) );

			$html = str_replace( $matches[1], $embed_url, $html );
		}

		return $html;
	}
	
    protected function content_template() {
	
    }

}