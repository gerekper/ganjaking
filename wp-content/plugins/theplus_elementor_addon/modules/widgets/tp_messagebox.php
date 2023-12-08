<?php 
/*
Widget Name: Message Box
Description: Message Box
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

class ThePlus_MessageBox extends Widget_Base {
		
	public function get_name() {
		return 'tp-messagebox';
	}

    public function get_title() {
        return esc_html__('Message Box', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-calendar-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		/*Text Content */
		$this->start_controls_section(
			'message_box_content_section',
			[
				'label' => esc_html__( 'Text Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'Title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'This is alert need your attention', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Title', 'theplus' ),	
				'label_block' => true,			
			]
		);
		$this->add_control(
            'Description',
            [
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
        );
        $this->add_control(
            'descText',
            [   
            	'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I Am Text Block. Click Edit Button To Change This Text. Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Ut Elit Tellus, Luctus Nec Ullamcorper Mattis, Pulvinar Dapibus Leo.', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Description here', 'theplus' ),
				'condition' => [
					'Description' => 'yes',
				],
            ]
        );        
		$this->end_controls_section();
	    /* Text Content */
	    /* Icon/Button Content */
	   $this->start_controls_section(
			'message_icnbtn_section',
			[
				'label' => esc_html__( 'Icon & Button', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
	   $this->add_control(
            'icon',
            [
				'label' => esc_html__( 'Main Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',	
			]
        );
        $this->add_control(
			'IconName',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'fa fa-exclamation',
					'library' => 'solid',
				],
				'condition' => [
					'icon' => 'yes',
				],
			]
		);
		$this->add_control(
            'dismiss',
            [
				'label' => esc_html__( 'Close Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',	
			]
        );
        $this->add_control(
			'dismsIcon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,			
				'default' => [
					'value' => 'far fa-times-circle',
					'library' => 'solid',
				],
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Closing Animation Duration', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 5000,
						'step' => 50,
					],
				],			
				'render_type' => 'ui',
				'condition' => [
					'dismiss' => 'yes',
				],				
            ]
        );
	    $this->end_controls_section();
	    /* Icon/Button Content */
        /* Title Style*/
		$this->start_controls_section(
            'message_box_title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,					
            ]
        );
       $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'titleTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .msg-title',
				'condition' => [
					'Title!' => '',
				],
			]
		);
       $this->add_responsive_control(
			'titleAdjust',
			[
				'label'      => esc_html__( 'Title Adjust', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator' => 'after',
				'selectors'  => [
					'{{WRAPPER}} .msg-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
        $this->start_controls_tabs( 'mesg_title_color' );
		$this->start_controls_tab(
			'title_color_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'titleNmlColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .msg-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'Title!' => '',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'titleNmlBg',
			   'label' => esc_html__( 'Background Type', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .msg-title',
			   'condition' => [
					'Title!' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'titleNShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box .msg-title',				
				 'condition' => [
					'Title!' => '',
				],				
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'title_color_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'titleHvrColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'Title!' => '',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
			   'name' => 'titleHvrBg',
			   'label' => esc_html__( 'Background Type', 'theplus' ),
			   'types' => [ 'classic', 'gradient' ],
			   'selector' => '{{WRAPPER}} .messagebox-bg-box:hover .msg-title',
			   'condition' => [
					'Title!' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'titleHvrShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover .msg-title',
				 'condition' => [
					'Title!' => '',
				],				
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();	
        $this->end_controls_section();
        /* Title Style*/
        /*Description Style*/	
        $this->start_controls_section(
            'message_box_desc_styling',
            [
                'label' => esc_html__('Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'Description' => 'yes',
				],
            ]
        );
        $this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name' => 'descTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .msg-desc',
				'condition' => [
					'Description' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'descAdjust',
			[
				'label' => esc_html__( 'Description Adjust', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .msg-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'Description' => 'yes',
				],				
			]
		);
        $this->start_controls_tabs( 'mesg_desc_color' );
		$this->start_controls_tab(
			'desc_color_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'Description' => 'yes',
				],
			]
		);
		$this->add_control(
			'descNmlColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .msg-desc' => 'color: {{VALUE}};',
				],
				'condition' => [
					'Description' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'descNmlBG',
				'label' => esc_html__( 'Background Type', 'theplus' ),
			    'types' => [ 'classic', 'gradient' ],
			    'selector' => '{{WRAPPER}} .msg-desc',
			    'condition' => [
				    'Description' => 'yes',
			    ],
			]
		);
		$this->add_responsive_control(
			'descNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .msg-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				    'Description' => 'yes',
			    ],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'desc_color_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'Description' => 'yes',
				],	
			]
		);
		$this->add_control(
			'descHvrColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-desc' => 'color: {{VALUE}};',
				],
				'condition' => [
					'Description' => 'yes',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
				   'name' => 'descHvrBG',
				   'label' => esc_html__( 'Background Type', 'theplus' ),
				   'types' => [ 'classic', 'gradient' ],
				   'selector' => '{{WRAPPER}} .messagebox-bg-box:hover .msg-desc',
				   'condition' => [
					    'Description' => 'yes',
				    ],
				]
		);
		$this->add_responsive_control(
				'descHvrBRadius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .messagebox-bg-box:hover .msg-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
					    'Description' => 'yes',
				    ],
				]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();			
        $this->end_controls_section();
        /*Description Style*/
        /*Main Icon Style*/
        $this->start_controls_section(
            'message_box_icon_styling',
            [
                'label' => esc_html__('Main Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'icon' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'iconSize',
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
				'default' => [
					'unit' => 'px',
					'size' => 25,
				],				
				'render_type' => 'ui',
				'condition' => [
					'icon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->add_responsive_control(
            'iconWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
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
					'size' => 40,
				],			
				'render_type' => 'ui',
				'condition' => [
					'icon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],				
            ]
        );
        $this->add_control(
            'msgArrow',
            [
				'label' => esc_html__( 'Arrow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'after',
			]
        );
        $this->start_controls_tabs( 'mesg_icon_color' );
		$this->start_controls_tab(
			'icon_color_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'iconNormalColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'icon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'bgNormalColor',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'icon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'arrowNormalColor',
			[
				'label' => esc_html__( 'Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'msgArrow' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .msg-arrow::after' => 'border-left-color: {{VALUE}};',
				],
			]
		);
	    $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'iconNmlBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box .msg-icon-content',
				'condition' => [
				   'icon' => 'yes',
			    ],
			]
	    );
		$this->add_responsive_control(
			'iconBdrNmlRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box .msg-icon-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				   'icon' => 'yes',
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'nmlIconShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box .msg-icon-content',
				'condition' => [
					'icon' => 'yes',
				],
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'icon_color_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'iconHoverColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'icon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-icon-content i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-icon-content svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'bgHoverColor',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'icon' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-icon-content' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'arrowHoverColor',
			[
				'label' => esc_html__( 'Arrow Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'msgArrow' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-arrow::after' => 'border-left-color: {{VALUE}};',
				],
			]
		);
		 $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'iconHvrBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover .msg-icon-content',
				'condition' => [
				    'icon' => 'yes',
			    ],
			]
	    );
		$this->add_responsive_control(
			'iconBdrHvrRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-icon-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				    'icon' => 'yes',
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'hvrIconShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover .msg-icon-content',
				'condition' => [
					'icon' => 'yes',
				],
			]
		);	
	    $this->end_controls_tab();
	    $this->end_controls_tabs();	
        $this->end_controls_section();
        /* Main Icon Style */
        /* Close Button Style */
        $this->start_controls_section(
            'message_box_dismiss_styling',
            [
                'label' => esc_html__('Close Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
					'dismiss' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'dIconSize',
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
				'default' => [
					'unit' => 'px',
					'size' => 37,
				],					
				'render_type' => 'ui',
				'condition' => [
					'dismiss' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
            ]
        );
        $this->add_responsive_control(
            'dIconWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Width', 'theplus'),
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
					'size' => 38,
				],					
				'render_type' => 'ui',
				'condition' => [
					'dismiss' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}',			
				],
				'separator' => 'after',
            ]
        );
        $this->start_controls_tabs( 'mesg_dismiss_color' );
		$this->start_controls_tab(
			'dIcon_color_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);
		$this->add_control(
			'dIconNmlColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'dismiss' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'dIconNmlBG',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content' => 'background: {{VALUE}};',
				],
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);   
		$this->add_responsive_control(
			'dIconNmlBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				    'dismiss' => 'yes',
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'dIconNmlShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box .msg-dismiss-content',
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'dIcon_color_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);
		$this->add_control(
			'dIconHvrColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'dismiss' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-dismiss-content i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-dismiss-content svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'dIconHvrBG',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-dismiss-content' => 'background: {{VALUE}};',
				],
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);		
		$this->add_responsive_control(
			'dIconHvrBRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box:hover .msg-dismiss-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				    'dismiss' => 'yes',
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'dIconHvrShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover .msg-dismiss-content',
				'condition' => [
					'dismiss' => 'yes',
				],
			]
		);	
	    $this->end_controls_tab();
	    $this->end_controls_tabs();	
        $this->end_controls_section();
        /* Close Button Style */
        /*Background Style*/	
        $this->start_controls_section(
            'message_box_background_styling',
            [
                'label' => esc_html__('Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
			'bgPadding',
			[
				'label'      => esc_html__( 'Padding', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator' => 'after',
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'mesg_background' );
		$this->start_controls_tab(
			'bg_color_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'normalBG',
				'label' => esc_html__( 'Background Type', 'theplus' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .messagebox-bg-box',
			]
		);
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bgNmlBorder',
				'label' => esc_html__( 'Border Type', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box',
			]
		);
		$this->add_responsive_control(
			'boxBdrNmlRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'nmlboxShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box',
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bg_color_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'HoverBG',
				'label' => esc_html__( 'Background Type', 'theplus' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bgHvrBorder',
				'label' => esc_html__( 'Border Type', 'theplus'),
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover',
			]
		);
		$this->add_responsive_control(
			'boxBdrHvrRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .messagebox-bg-box:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'hvrboxShadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .messagebox-bg-box:hover',
			]
		);	
	    $this->end_controls_tab();
	    $this->end_controls_tabs();	
        $this->end_controls_section();
        /*Background Style*/	
	}

	protected function render() {
        $settings = $this->get_settings_for_display();
        $uid_msgbox = uniqid('tp-msg');
        $msgArrow = (!empty($settings['msgArrow'])) ? $settings['msgArrow'] : false;
		   
	    $getIcon = $arrow ='';
	    if((!empty($settings['icon']) && $settings['icon']=='yes')){
			if(!empty($msgArrow)){
				$arrow=' msg-arrow';
			}
			if(!empty($settings["IconName"])){
				$getIcon .='<div class="msg-icon-content '. esc_attr($arrow).'">';
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings["IconName"], [ 'aria-hidden' => 'true' ]);
					$getIcon .= ob_get_contents();
					ob_end_clean();
				$getIcon .='</div>';
			}	    	
        }
        $getDismiss = '';
        if((!empty($settings['dismiss']) && $settings['dismiss']=='yes')){
		    $getDismiss .='<div class="msg-dismiss-content">';
		    if(!empty($settings["dismsIcon"])){
		        ob_start();
			    \Elementor\Icons_Manager::render_icon( $settings["dismsIcon"], [ 'aria-hidden' => 'true' ]);
			    $getDismiss .= ob_get_contents();
			    ob_end_clean();	
			}
		   $getDismiss .='</div>';
        }
        $getTitle = '';
	    if(!empty($settings['Title'])){
		   $lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['titleNmlBg_image'],$settings['titleHvrBg_image']) : '';
		   $getTitle .='<div class="msg-title '.esc_attr($lz1).'" >'.wp_kses_post($settings['Title']).'</div>';
	    }
	    $getDesc = '';
	    if((!empty($settings['Description']) && $settings['Description']=='yes') && !empty($settings['descText'])){
			$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['descNmlBG_image'],$settings['descHvrBG_image']) : '';
	        $getDesc .='<div class="msg-desc '.esc_attr($lz2).'">'.wp_kses_post($settings['descText']).'</div>';
	    }
		$speed = '';
		if((!empty($settings['dismiss']) && $settings['dismiss']=='yes')){
		    $speed = !empty($settings['speed']['size']) ? $settings['speed']['size'] : '500';
		}	    
		$output = '<div class="tp-messagebox tp-widget-'.esc_attr($uid_msgbox).'" data-speed="'.$speed.'">';
				$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['normalBG_image'],$settings['HoverBG_image']) : '';
				$output .='<div class="messagebox-bg-box '.esc_attr($lz3).'">';
					$output .='<div class="message-media ">';
						$output .=$getIcon;
						$output .='<div class="msg-content">';
							$output .=$getTitle;
							$output .=$getDesc;
						$output .='</div>';
						$output .=$getDismiss;
					$output .='</div>';
				$output .= '</div>';
       $output .= '</div>';
       echo $output;    
    }
    protected function content_template() {
		
	}
}