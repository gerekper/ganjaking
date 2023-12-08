<?php 
/*
Widget Name: Table Of Content
Description: Table Of Content
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Table_Content extends Widget_Base {
		
	public function get_name() {
		return 'tp-table-content';
	}

    public function get_title() {
        return esc_html__('Table Of Content', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-table theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
    	 /* Layout Tab */
		$this->start_controls_section(
			'table_content_option_section',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'Style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'typeList',
			[
				'label' => esc_html__( 'List Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'OL',
				'options' => [
					'UL'  => esc_html__( 'UL', 'theplus' ),
					'OL' => esc_html__( 'OL', 'theplus' ),
				],
				'condition' => [
					'Style' => 'none',
				],
			]
		);		
		$this->add_control(
			'selectorHeading',
			[
				'label' => __( 'Select Tags', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'h1'  => __( 'H1', 'theplus' ),
					'h2' => __( 'H2', 'theplus' ),
					'h3' => __( 'H3', 'theplus' ),
					'h4' => __( 'H4', 'theplus' ),
					'h5' => __( 'H5', 'theplus' ),
					'h6' => __( 'H6', 'theplus' ),
				],
				'default' => [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ],
				'separator' => 'before',
				'label_block' => true,
			]
		);		
		$this->add_control(
			'ChildToggle',
			[
				'label' => esc_html__( 'Child Collapsed', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);		
		$this->end_controls_section();		
	    /* Layout Tab */
		/* Content Tab */
		$this->start_controls_section(
			'table_content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'showText',
            [
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',				
			]
        );
		$this->add_control(
			'contentText',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Table Of Content', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Title', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
        $this->add_control(
            'TableDescText',
            [   
            	'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Description', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
				],
				'separator' => 'before',
            ]
        );
        $this->add_control(
            'showIcon',
            [
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
				],	
			]
        );
        $this->add_control(
			'PrefixIcon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'fa fa-exclamation-circle',
					'library' => 'solid',
				],
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],
			]
		);		
		$this->end_controls_section();
	   /* Content Tab */
	   /* Extra Options Tab */
		$this->start_controls_section(
			'table_extra_option_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'ToggleIcon',
            [
				'label' => esc_html__( 'Toggle', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
				],	
			]
        );
		$this->add_responsive_control(
            'DefaultToggle',
            [
				'label' => esc_html__( 'Default On', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],	
			]
        );
        $this->start_controls_tabs( 'toggle_open_close' );
		$this->start_controls_tab(
			'Ticon_opn',
			[
				'label' => esc_html__( 'Open', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);	
		$this->add_control(
			'openIcon',
			[
				'label' => esc_html__( 'Select Open Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'fas fa-angle-up',
					'library' => 'solid',
				],
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Ticon_close',
			[
				'label' => esc_html__( 'Close', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);
		$this->add_control(
			'closeIcon',
			[
				'label' => esc_html__( 'Select Close Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'fas fa-angle-down',
					'library' => 'solid',
				],
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);		
	    $this->end_controls_tab();
	    $this->end_controls_tabs();        
		$this->add_control(
			'smoothScroll',
			[
				'label' => esc_html__( 'Smooth Scroll', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'smoothDuration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Smooth Duration', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 420,
				],
				'render_type' => 'ui',
				'condition' => [
					'smoothScroll' => 'yes',
				],	
            ]
        );
        $this->add_responsive_control(
            'scrollOffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Scroll Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'smoothScroll' => 'yes',
				],
            ]
        );
        $this->add_control(
			'fixedPosition',
			[
				'label' => esc_html__( 'Fixed', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'fixedOffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Fixed Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'fixedPosition' => 'yes',
				],
            ]
        );
		$this->add_control(
            'hashtag',
            [
				'label' => esc_html__( 'Hash Tag', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
        );
		$this->add_control(
			'hashtagtext',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '#',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'hashtag' => 'yes',
				],	
			]
		);
		$this->add_control('copyText',
            [
                'label' => esc_html__( 'Copy Text', 'theplus' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'On', 'theplus' ),
                'label_off' => esc_html__( 'Off', 'theplus' ),
                'default' => 'yes',
                'condition' => [
                    'hashtag' => 'yes',
                ],
            ]
        );
		$this->add_control(
            'hashtaghover',
            [
				'label' => esc_html__( 'Hash Tag On Hover', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'hashtag' => 'yes',
				],	
			]
        );
		$this->add_responsive_control(
            'headingsOffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Heading Active Offset ', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'description' => esc_html__( 'Note : Value to make Heading of TOC active by reaching to It\'s page location.', 'theplus' ),
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'separator' => 'before',
				'render_type' => 'ui',
            ]
        );
		$this->add_control(
			'contentSelector',
			[
				'label' => esc_html__( 'Restricted Container Area', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'description' => esc_html__( 'Note : You can add class name of container to restrict TOC rendering. ', 'theplus' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'excludecontentSelector',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'How to exclude any title?', 'theplus' ),
				'content_classes' => 'tp-widget-description-toc',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'excludecontentSelector1',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Use class ".tp-toc-ignore" in heading to exclude that heading from TOC.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->end_controls_section();
	    /* Extra Options Tab */
	    /* Content TextBG Style */
        $this->start_controls_section(
            'table_heading_textbg_styling',
            [
                'label' => esc_html__('Heading', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'showText' => 'yes',
				],
            ]
        );
        $this->add_responsive_control(
			'TextMargin',
			[
				'label'      => esc_html__( 'Margin', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em'],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
				'condition' => [
				    'showText' => 'yes',
			    ],			
			]
		);
		$this->add_responsive_control(
			'TextPadding',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em'],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
				'condition' => [
				    'showText' => 'yes',
			    ],			
			]
		);
		$this->add_control(
			'tct_HeadOPt',
			[
				'label' => esc_html__( 'Heading Option ', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'TextTypo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .tp-toc-wrap .tp-toc-heading',
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
        $this->start_controls_tabs( 'table_textbg_color' );
		$this->start_controls_tab(
			'Nml_Textbg_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
		$this->add_control(
			'TextNormalColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc-heading' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-toc-wrap .tp-toc-heading svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_Textbg_color',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
		$this->add_control(
			'TextHoverColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tct_DescOPt',
			[
				'label' => esc_html__( 'Description Option ', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
					'TableDescText!' => '',
				],
			]
		);	
		 $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'DescTextTypo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .tp-toc-wrap .tp-toc-heading .tp-table-desc',
				'condition' => [
					'showText' => 'yes',
					'TableDescText!' => '',
				],	
			]
		);
        $this->start_controls_tabs( 'table_desctext_color' );
		$this->start_controls_tab(
			'Nml_Desctext_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'TableDescText!' => '',
				],
			]
		);
		$this->add_control(
			'DescTextNormalColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc-heading .tp-table-desc' => 'color: {{VALUE}};',
				],
				'condition' => [
					'showText' => 'yes',
					'TableDescText!' => '',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_Desctext_color',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'TableDescText!' => '',
				],
			]
		);
		$this->add_control(
			'DescTextHoverColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'showText' => 'yes',
					'TableDescText!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading .tp-table-desc' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'tct_IcnOPt',
			[
				'label' => esc_html__( 'Icon Option ', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'IconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-toc-heading .table-prefix-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-toc-heading .table-prefix-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],
            ]
        );
        $this->start_controls_tabs( 'table_icon_color' );
		$this->start_controls_tab(
			'Nml_Icon_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],
			]
		);
		$this->add_control(
			'IconNormalColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc-heading .table-prefix-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-toc-heading .table-prefix-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_Icon_color',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],
			]
		);
		$this->add_control(
			'IconHoverColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'showText' => 'yes',
					'showIcon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading .table-prefix-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading .table-prefix-icon svg' => 'fill: {{VALUE}};',
				],	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'tct_TgIcnOPt',
			[
				'label' => esc_html__( 'Toggle Icon Option ', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'ToggleIconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .table-toggle-wrap .table-toggle-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .table-toggle-wrap .table-toggle-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
            ]
        );
        $this->start_controls_tabs( 'table_toggleicon_color' );
		$this->start_controls_tab(
			'Nml_TglIcon_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);
		$this->add_control(
			'ToggleIconNormalColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .table-toggle-wrap .table-toggle-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .table-toggle-wrap .table-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_TglIcon_color',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],
			]
		);
		$this->add_control(
			'ToggleIconHoverColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .table-toggle-wrap.tp-toc-wrap:hover .table-toggle-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .table-toggle-wrap.tp-toc-wrap:hover .table-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'showText' => 'yes',
					'ToggleIcon' => 'yes',
				],	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control(
			'tct_BgOPt',
			[
				'label' => esc_html__( 'Background Option ', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'showText' => 'yes',
			    ],
			]
		);
		$this->start_controls_tabs( 'Nml_hvr_border' );
		$this->start_controls_tab(
			'Nml_Border',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'TextBg',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-toc-wrap .tp-toc-heading',
			   'condition' => [
				   'showText' => 'yes',
			    ],	
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'TextBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap .tp-toc-heading',
				'condition' => [
				    'showText' => 'yes',
			    ],		
			]
	    );
		$this->add_responsive_control(
			'TextBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				    'showText' => 'yes',
			    ],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'TextBoxShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap .tp-toc-heading',
				'condition' => [
					'showText' => 'yes',
				],	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_Border',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'showText' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'TextBgHover',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading',
			   'condition' => [
				    'showText' => 'yes',
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'TextBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading',
				'condition' => [
				    'showText' => 'yes',
			    ],	
			]
	    );
		$this->add_responsive_control(
			'TextBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				    'showText' => 'yes',
			    ],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'TextBoxShadowHover',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap:hover .tp-toc-heading',
				'condition' => [
					'showText' => 'yes',
				],
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();	
        $this->end_controls_section();
        /* Content TextBG Style */
        /*Heading Area Style*/
		$this->start_controls_section(
            'table_content_heading_styling',
            [
                'label' => esc_html__('Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,					
            ]
        );
        $this->add_responsive_control(
            'leftOffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Left Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .toc-list,
					{{WRAPPER}} .table-style-2 .toc-list li,
					{{WRAPPER}} .table-style-3 .tp-toc .toc-list .toc-list li,
					{{WRAPPER}} .table-style-4 .tp-toc .toc-list .toc-list li' => 'padding-left: {{SIZE}}{{UNIT}}',					
				],
            ]
        );
        $this->add_responsive_control(
            'bottomOffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-table-content .toc-list li,{{WRAPPER}} .tp-table-content .toc-list li.is-active-li a' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list,{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list.is-collapsible' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list .toc-list-item:last-child,
					{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list.is-collapsible .toc-list-item:last-child' => 'margin-bottom: 0 !important;',
					'{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list .toc-list,{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list.is-collapsible .toc-list,{{WRAPPER}} .tp-toc-wrap .toc-list-item .toc-list.is-collapsible.is-collapsed' => 'margin-top: 0 !important;',
				],
				'condition' => [
					'Style' => ['style-2','style-3','style-4']
				],
            ]
        );
        $this->add_responsive_control(
			'contentPadding',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em'],	
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],			
			]
		);
		$this->add_responsive_control(
			'outerMargin',
			[
				'label'      => esc_html__( 'Outer Margin', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em' ],
				'condition' => [
					'showText' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],		
			]
		);
        $this->add_responsive_control(
            'Style4Padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Child Padding', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'render_type' => 'ui',
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'condition' => [
					'Style' => 'style-4',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc > .toc-list > li .toc-list' => 'padding-left: {{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->add_control(
			'TableSetMinHeight',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'TableMinHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Minimum Height', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'render_type' => 'ui',				
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'condition' => [
					'TableSetMinHeight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc' => 'min-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'TableMaxHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Height', 'theplus'),
				'size_units' => [ 'px','em','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'render_type' => 'ui',				
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'condition' => [
					'TableSetMinHeight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc' => 'max-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'ScrollBarWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('ScrollBar Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'render_type' => 'ui',
				
				'condition' => [
					'TableSetMinHeight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->add_control(
			'ScrollBarThumb',
			[
				'label' => esc_html__( 'ScrollBar Thumb Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'TableSetMinHeight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ScrollBarTrack',
			[
				'label' => esc_html__( 'ScrollBar Track Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'TableSetMinHeight' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-toc-wrap .tp-toc::-webkit-scrollbar-track' => 'background-color: {{VALUE}};',
				],	
			]
		); 
        $this->end_controls_section();
        /* Heading Area Style */
        /* Content Line Style */
        $this->start_controls_section(
            'table_content_styling_section',
            [
                'label' => esc_html__('Content Line', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                	'Style!' => 'none',
                	'typeList' => ['UL','OL'],					
				],
            ]
        );
        $this->add_responsive_control(
            'LineWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Line Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .table-style-1 .toc-link::before,
					{{WRAPPER}} .table-style-3 .tp-toc > .toc-list .toc-list li:before,
					{{WRAPPER}} .table-style-4 .tp-toc > .toc-list .toc-list li:before' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .table-style-2 .toc-list li' => 'border-left-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .table-style-4 .tp-toc > .toc-list .toc-list li.is-active-li:before' => 'left: calc({{SIZE}} / 2 * 1px)',
				],	
            ]
        );
        $this->add_responsive_control(
            'Line2Width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Active Line Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .table-style-2 .toc-list li.is-active-li' => 'border-left-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .table-style-3 .tp-toc > .toc-list .toc-list li.is-active-li:before,
					{{WRAPPER}} .table-style-4 .tp-toc > .toc-list .toc-list li.is-active-li:before' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'Style' => ['style-2','style-3','style-4']
				],	
            ]
        );
        $this->start_controls_tabs( 'Nml_Act_color' );
		$this->start_controls_tab(
			'Nml_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
        $this->add_control(
            'LineColor',
            [
                'label' => esc_html__('Line Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .table-style-1 .toc-link::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .table-style-2 .toc-list li' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}} .table-style-3 .tp-toc > .toc-list .toc-list li:before,
					{{WRAPPER}} .table-style-4 .tp-toc > .toc-list .toc-list li:before' => 'background: {{VALUE}};',
				],  
            ]
        );
        $this->end_controls_tab();
		$this->start_controls_tab(
			'Act_color',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
        $this->add_control(
            'LineActiveColor',
            [
                'label' => esc_html__('Line Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                 'selectors' => [
					'{{WRAPPER}} .table-style-1 .toc-link.is-active-link::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .table-style-2 .toc-list li.is-active-li' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}} .table-style-3 .tp-toc > .toc-list .toc-list li.is-active-li:before,
					{{WRAPPER}} .table-style-4 .tp-toc > .toc-list .toc-list li.is-active-li:before' => 'background: {{VALUE}};',
				],
            ]
        );
        $this->end_controls_tab();
		$this->end_controls_tabs();	
        $this->end_controls_section();
        /* Content Line Style */
        /* Content Level1 Style */
        $this->start_controls_section(
            'table_content_L1section_styling',
            [
                'label' => esc_html__('Level 1 Typography', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'Level1Typo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .tp-toc .toc-list > li > a',
			]
		);
        $this->start_controls_tabs( 'table_L1_color' );
		$this->start_controls_tab(
			'Nml_L1_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'Level1NormalColor',
			[
				'label' => esc_html__( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc .toc-list > li > a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Act_L1_color',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'Level1ActiveColor',
			[
				'label' => esc_html__( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc .toc-list > li:hover > a, {{WRAPPER}} .tp-toc > .toc-list > li.is-active-li > a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
        $this->end_controls_section();
        /* Content Level1 Style */
        /* Content SubLevel Style */
        $this->start_controls_section(
            'table_content_sublevel_styling',
            [
                'label' => esc_html__('Sub-Level Typography', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'LevelSubTypo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .tp-toc .toc-list .toc-list > li > a,
				{{WRAPPER}} .tp-toc .toc-list .toc-listis-collapsible > li > a',
			]
		);
        $this->start_controls_tabs( 'table_sl_color' );
		$this->start_controls_tab(
			'Nml_Sl_color',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'LevelSubNormalColor',
			[
				'label' => esc_html__( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc .toc-list .toc-list > li > a,
				{{WRAPPER}} .tp-toc .toc-list .toc-listis-collapsible > li > a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Act_Sl_color',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'LevelSubActiveColor',
			[
				'label' => esc_html__( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-toc .toc-list .toc-list > li:hover > a, {{WRAPPER}} .tp-toc .toc-list .toc-list > li.is-active-li > a,{{WRAPPER}} .tp-toc .toc-list .toc-listis-collapsible > li:hover > a, {{WRAPPER}} .tp-toc .toc-list .toc-listis-collapsible > li.is-active-li > a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
        $this->end_controls_section();
        /* Content SubLevel Style */
		
		 /* hash tag Style */
        $this->start_controls_section(
            'table_hashtag_styling',
            [
                'label' => esc_html__('Hash Tag', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hashtag' => 'yes',
				],
            ]
        );
		 $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hashTypo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '.tp-toc-hash-tag',
			]
		);
        $this->start_controls_tabs( 'table_hash' );
		$this->start_controls_tab(
			'hashn',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'hashcolor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.tp-toc-hash-tag' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'hashh',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'hashcolorh',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'h1:hover .tp-toc-hash-tag,h2:hover .tp-toc-hash-tag,h3:hover .tp-toc-hash-tag,
					h4:hover .tp-toc-hash-tag,h5:hover .tp-toc-hash-tag,h6:hover .tp-toc-hash-tag' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tct_Hashcopyhead',
			[
				'label' => esc_html__( 'Copied Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'HashcopyTypo',
                'label' => esc_html__('Typography', 'theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '.tp-copy-hash',
			]
		);
		$this->add_control(
			'Hashcopycolor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.tp-copy-hash' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->end_controls_section();
        /* hash tag Style */
		
        /* Content Box BG Style */
        $this->start_controls_section(
            'table_content_boxbg_styling',
            [
                'label' => esc_html__('Box', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
			'boxPadding',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em'],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'Nml_hvr_Boxborder' );
		$this->start_controls_tab(
			'Nml_BoxBorder',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'boxBg',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-toc-wrap',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap',
			]
	    );
		$this->add_responsive_control(
			'boxBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'boxBoxShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Hvr_BoxBorder',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'boxBgHover',
			   'label' => esc_html__( 'Background', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .tp-toc-wrap:hover',
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap:hover',
			]
	    );
		$this->add_responsive_control(
			'boxBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-toc-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'boxBoxShadowHover',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-toc-wrap:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();
        /* Content Box BG Style */
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$uid_tblcontent=uniqid("tp-tbl");
		$Style = (!empty($settings["Style"])) ? $settings["Style"] : 'none';
		$ToggleIcon = (!empty($settings["ToggleIcon"])) ? $settings["ToggleIcon"] : false;
		$TableDescText = (!empty($settings["TableDescText"])) ? $settings["TableDescText"] : '';
		$PrefixIcon = (!empty($settings["PrefixIcon"])) ? $settings["PrefixIcon"] : '';
		$DefaultToggle['md'] = (!empty($settings["DefaultToggle"]) && $settings["DefaultToggle"]=='yes') ? true : false;
	    $DefaultToggle['sm'] = (!empty($settings["DefaultToggle_tablet"]) && $settings["DefaultToggle_tablet"]=='yes') ? true : false;
		$DefaultToggle['xs'] = (!empty($settings["DefaultToggle_mobile"]) && $settings["DefaultToggle_mobile"]=='yes') ? true : false;
				
		$option = [];
		$option['tocSelector'] = '.tp-toc';
		$option['contentSelector'] = (!empty($settings["contentSelector"])) ? $settings["contentSelector"] : '.elementor-page';
		$option['headingSelector']= (is_array($settings["selectorHeading"])) ? implode(',', $settings["selectorHeading"]) : $settings["selectorHeading"];
		
		if(!empty($settings['hashtag']) && $settings['hashtag']=='yes'){
			$option['hashtagtext']= !empty($settings["hashtagtext"]) ? $settings["hashtagtext"] : '#';
			$option['copyText']= !empty($settings["copyText"]) ? 1 : 0;
		}
		
		$option['isCollapsedClass'] ='';
		if(!empty($settings['ChildToggle']) && $settings['ChildToggle']=='yes'){
			$option['isCollapsedClass'] = 'is-collapsed';
		}	
		$option['headingsOffset'] = (!empty($settings['headingsOffset']['size'])) ? $settings['headingsOffset']['size'] : 1;
		$option['scrollSmooth'] = (!empty($settings['smoothScroll'])) ? true : false;
		$option['scrollSmoothDuration'] = (!empty($settings['smoothDuration']['size'])) ? (int)$settings['smoothDuration']['size'] : 420;
		$option['scrollSmoothOffset'] = (!empty($settings['scrollOffset']['size'])) ? (int)$settings['scrollOffset']['size'] : 0;
		$option['orderedList'] = (!empty($settings['typeList']) && $settings['typeList']==='OL') ? true : false;
		$option['positionFixedSelector'] = null;
		if(!empty($settings['fixedPosition']) && $settings['fixedPosition']=='yes'){
			$option['positionFixedSelector'] = '.tp-table-content';
		}
		$option['fixedSidebarOffset'] = (!empty($settings['fixedPosition']) && !empty($settings['fixedOffset']['size'])) ? (int)$settings['fixedOffset']['size'] : 'auto';	
		$option['hasInnerContainers'] = true;
		$openIcon=$closeIcon='';
		if(!empty($settings["openIcon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon($settings["openIcon"], [ 'aria-hidden' => 'true' ]);
			$openIcon = ob_get_contents();
			ob_end_clean();						
		}
		if(!empty($settings["closeIcon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon($settings["closeIcon"], [ 'aria-hidden' => 'true' ]);
			$closeIcon = ob_get_contents();
			ob_end_clean();						
		}
		$toggleClass=$toggleAttr='';
		if(!empty($ToggleIcon) && $ToggleIcon=='yes'){
			$toggleClass = 'table-toggle-wrap';	
			  $toggleAttr .=' data-open="'.esc_html($openIcon).'"';
			  $toggleAttr .=' data-close="'.esc_html($closeIcon).'"'; 
			$toggleAttr .=' data-default-toggle="'.htmlspecialchars(json_encode($DefaultToggle), ENT_QUOTES, 'UTF-8').'"';
		}
		$toggleActive=' active';
		if(!empty($settings["PrefixIcon"])){
			ob_start();
			\Elementor\Icons_Manager::render_icon($settings["PrefixIcon"], [ 'aria-hidden' => 'true' ]);
			 $PrefixIcon = ob_get_contents();
			ob_end_clean();						
		}
		$hashtag = isset($settings['hashtag']) ? $settings['hashtag'] : 'no';
		$hashtaghover = isset($settings['hashtaghover']) ? $settings['hashtaghover'] : 'yes';
		$hashtagclass=$hashtaghoverclass='';
		if(isset($hashtag) && $hashtag=='yes'){
			$hashtagclass='tp-toc-hash-tag';
			if(isset($hashtaghover) && $hashtaghover=='yes'){
				$hashtaghoverclass='tp-toc-hash-tag-hover';
			}
		}
	    $output = '<div class="tp-table-content '.esc_attr($hashtagclass).' '.esc_attr($hashtaghoverclass).' tp-widget-'.esc_attr($uid_tblcontent).' table-'.esc_attr($Style).'" data-settings="'.htmlspecialchars(json_encode($option), ENT_QUOTES, 'UTF-8').'" >';
			$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['boxBg_image'],$settings['boxBgHover_image']) : '';
			$output .= '<div class="tp-toc-wrap '.esc_attr($toggleClass).$toggleActive.' '.esc_attr($lz2).'" '.$toggleAttr.' >';
	          if((!empty($settings['showText']) && $settings['showText']=='yes') && !empty($settings['contentText']) ) {
					$table_desc='';
					if(!empty($TableDescText)){
						$table_desc= '<div class="tp-table-desc">'.$TableDescText.'</div>';
					}	
					$Icon = ((!empty($settings['showIcon']) && $settings['showIcon']=='yes') && !empty($PrefixIcon)) ? $PrefixIcon : '';
					
					$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['TextBg_image'],$settings['TextBgHover_image']) : '';
					$output .= '<div class="tp-toc-heading '.esc_attr($lz1).'"><span class="table-prefix-icon">'. $Icon .'<span>'. $settings['contentText'] .$table_desc.'</span></span>';
						if(!empty($ToggleIcon) && $ToggleIcon=='yes'){
							if((!empty($settings["DefaultToggle"]) && $settings["DefaultToggle"]=='yes')){
							$output .= '<span class="table-toggle-icon">'.$openIcon.'</span>';
							}else{
								$output .= '<span class="table-toggle-icon">'.$closeIcon.'</span>';
							}
						}
					$output .= '</div>';
		    	}
		    	$output .= '<div class="tp-toc toc"></div>';
			$output .= '</div>';
        $output .= '</div>';
        echo $output; 
    }
	protected function content_template() {
		
	}
}