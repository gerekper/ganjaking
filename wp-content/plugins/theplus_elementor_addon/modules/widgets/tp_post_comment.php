<?php 
/*
Widget Name: Post Comment
Description: Post Comment
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Post_Comment extends Widget_Base {
		
	public function get_name() {
		return 'tp-post-comment';
	}

    public function get_title() {
        return esc_html__('Post Comment', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-commenting theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-builder');
    }

    protected function register_controls() {	
		/*Tab Content*/
		$this->start_controls_section(
			'contentt_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				 'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control( 'PostTitle', 
			[
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Title', 'theplus'),
                'default' => esc_html__('Leave Your Comment', 'theplus'),
            ]
        );
		$this->add_control( 'BtnTitle', 
			[
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__('Button Title', 'theplus'),
				'default' => esc_html__('Submit Now', 'theplus'),
			]
		);
		$this->add_control( 'PlaceholderTxt', 
			[
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__('Placerholder Text', 'theplus'),
				'default' => esc_html__('Comment', 'theplus'),
			]
		);
		$this->add_control( 'LeaveTxt', 
			[
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__('Leave Reply Text', 'theplus'),
				'default' => esc_html__('Leave A Reply To', 'theplus'),
			]
		);
		$this->add_control( 'CancelTxt', 
			[
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__('Cancel Reply Text', 'theplus'),
				'default' => esc_html__('Cancel Reply', 'theplus'),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section('content_extraoption',
			[
				'label' => esc_html__( 'Extra Option', 'theplus' ),
				 'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control('cmtcount',
			[
				'label' => esc_html__( 'Comment Count', 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
			]
		);
		$this->end_controls_section();

		/*Tab Style*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Heading', 'theplus' ),
				 'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'headingTypo',
				'label' => esc_html__( 'Text', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .comment-section-title,.tp-post-comment #respond.comment-respond h3#reply-title',
			]
		);
		$this->add_control(
			'headingColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list .comment-section-title,.tp-post-comment #respond.comment-respond h3#reply-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*Comment Text Style*/
		/*Comment List Style*/
		$this->start_controls_section(
            'section_cmnt_list_style',
            [
                'label' => esc_html__('Comment List', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
            'imagesize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_cmnt_list_img_style' );
		$this->start_controls_tab(
			'tab_cmnt_list_img_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'imgNmlBorder',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar',
				]
	    );
		$this->add_responsive_control(
				'imgNmlBRadius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_Shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cmnt_list_imgh_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'imgHvrBorder',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar:hover,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar:hover',
				]
	    );
		$this->add_responsive_control(
				'imgHvrBRadius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar:hover,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'imgH_Shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list li.comment>.comment-body img.avatar:hover,{{WRAPPER}} .tp-post-comment .comment-list li.pingback>.comment-body img.avatar:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'userTypo',
				'label' => esc_html__( 'Username Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-author.vcard .fn .url,{{WRAPPER}} .tp-post-comment .comment-author.vcard .fn',	
			]
		);
		$this->start_controls_tabs( 'tabs_cmnt_list_style' );
		$this->start_controls_tab(
			'tab_cmnt_list_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'userColor',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-author.vcard .fn .url,{{WRAPPER}} .tp-post-comment .comment-author.vcard .fn' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cmnt_list_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'userHoverColor',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-author.vcard .fn .url:hover,{{WRAPPER}} .tp-post-comment .comment-author.vcard .fn:hover' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		 $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'metaTypo',
				'label' => esc_html__( 'Meta Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-meta .comment-metadata a',
				
			]
		);
		$this->start_controls_tabs( 'tabs_cmnt_meta_style' );
		$this->start_controls_tab(
			'tab_cmnt_meta_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'metaColor',
			[
				'label' => esc_html__( 'Meta Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-meta .comment-metadata a' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cmnt_meta_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'metaHoverColor',
			[
				'label' => esc_html__( 'Meta Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-meta .comment-metadata a:hover' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		 $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'replyTypo',
				'label' => esc_html__( 'Reply Message Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .comment-content,{{WRAPPER}} .tp-post-comment .comment-list .comment-content p',
				'separator' => 'before',
			]
		);
		 $this->start_controls_tabs( 'tabs_cmnt_reply_style' );
		$this->start_controls_tab(
			'tab_cmnt_reply_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'replyColor',
			[
				'label' => esc_html__( 'Reply Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list .comment-content,{{WRAPPER}} .tp-post-comment .comment-list .comment-content p' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cmnt_reply_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'replyHoverColor',
			[
				'label' => esc_html__( 'Reply Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list .comment-content:hover,{{WRAPPER}} .tp-post-comment .comment-list .comment-content:hover p' => 'color: {{VALUE}};border-color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'sep_comment_styling',
			[
				'label' => esc_html__( 'Separate Comment', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
            'sep_comment_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Padding', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list>.comment:not(.parent),
					{{WRAPPER}} .tp-post-comment .comment-list ul.children li,
					{{WRAPPER}} .tp-post-comment .comment-list>.comment:nth-child(2),
					{{WRAPPER}} .tp-post-comment .comment-list>.comment.parent > .comment-body' => 'padding: {{SIZE}}px;',
					'{{WRAPPER}} .tp-post-comment .comment-list>.comment.parent > .comment-body' => 'padding-left: calc(95px + {{SIZE}}px) !important;',
					'{{WRAPPER}} .tp-post-comment .comment-list li.comment.parent > .comment-body  img.avatar' => 'left: {{SIZE}}px !important;top: {{SIZE}}px !important;',
					'{{WRAPPER}} .tp-post-comment .comment-list li.comment.parent > .comment-body .reply' => 'right: {{SIZE}}px !important;',
					'{{WRAPPER}} .tp-post-comment #comments .comment-list li.comment> .children' => 'padding-top: {{SIZE}}px !important;',
				],
            ]
        );
		$this->add_control(
            'sep_comment_bottom_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Bottom Space', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list>.comment:not(.parent),
					{{WRAPPER}} .tp-post-comment .comment-list ul.children li,
					{{WRAPPER}} .tp-post-comment .comment-list>.comment:nth-child(2),
					{{WRAPPER}} .tp-post-comment .comment-list>.comment.parent > .comment-body' => 'margin-bottom: {{SIZE}}px;',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'sepcommentborder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list>.comment:not(.parent),
					{{WRAPPER}} .tp-post-comment .comment-list ul.children li,
					{{WRAPPER}} .tp-post-comment .comment-list>.comment:nth-child(2),
					{{WRAPPER}} .tp-post-comment .comment-list>.comment.parent > .comment-body',	
			]
		);
		$this->add_responsive_control(
			'sepcommentborderradius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment .comment-list>.comment:not(.parent),
					{{WRAPPER}} .tp-post-comment .comment-list ul.children li,
					{{WRAPPER}} .tp-post-comment .comment-list>.comment:nth-child(2),
					{{WRAPPER}} .tp-post-comment .comment-list>.comment.parent > .comment-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->end_controls_section();
		/*Comment List Style*/
		 /*reply Button Style*/
		$this->start_controls_section(
            'section_rep_button_style',
            [
                'label' => esc_html__('Reply Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'repbtnpadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list .reply a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'repbtnTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a',
			]
		);
		$this->start_controls_tabs( 'tabs_rep_btn_color_style' );
		$this->start_controls_tab(
			'tab_rep_btn_color_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'repbtnColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list .reply a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'repbtnBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'repbtnBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a',	
			]
		);
		$this->add_responsive_control(
			'repbtnBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment .comment-list .reply a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'repbtnBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_rep_btn_color_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'repbtnHoverColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-list .reply a:hover' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'repbtnBgHover',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'repbtnBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a:hover',	
			]
		);
		$this->add_responsive_control(
			'repbtnBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment .comment-list .reply a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'repbtnBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-comment .comment-list .reply a:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*reply Button Style*/
		 /*cancel reply Button Style*/
		$this->start_controls_section(
            'section_repcan_button_style',
            [
                'label' => esc_html__('Cancel Reply', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'repCanbtnTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment #respond.comment-respond #cancel-comment-reply-link',
			]
		);
		$this->start_controls_tabs( 'tabs_repcancel_btn_style' );
		$this->start_controls_tab(
			'tab_repcancel_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'repcanbtnColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #respond.comment-respond #cancel-comment-reply-link' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_repcancel_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'repcanbtnColorHover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #respond.comment-respond #cancel-comment-reply-link:hover' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*cancel reply Button Style*/
        /*Comment Field Style*/
		$this->start_controls_section(
            'section_cmnt_field_style',
            [
                'label' => esc_html__('Comment Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'fieldLabelColor',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #respond.comment-respond h3#reply-title' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'fieldLabelTypo',
				'label' => esc_html__( 'Label Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment #respond.comment-respond h3#reply-title',
				'separator' => 'after',	
			]
		);	
		$this->add_responsive_control(
			'fieldpadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
		$this->add_control('textColor',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #comments .logged-in-as , #comments .logged-in-as, #comments .logged-in-as a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name' => 'textTypo',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment #comments .logged-in-as , #comments .logged-in-as, #comments .logged-in-as a',
				'separator' => 'after',	
			]
		);	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'fieldTypo',
				'label' => esc_html__( 'Fields Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment',
			]
		);		
		$this->start_controls_tabs( 'tabs_cmnt_field_style' );
		$this->start_controls_tab(
			'tab_cmnt_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'fieldColor',
			[
				'label' => esc_html__( 'Fields Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment' => 'color: {{VALUE}}',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'fieldBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fieldBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment',	
			]
		);
		$this->add_responsive_control(
			'fieldBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'fieldBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author,
				{{WRAPPER}} .tp-post-comment #commentform #email,
				{{WRAPPER}} .tp-post-comment #commentform #url,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_cmnt_field_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'fieldHoverColor',
			[
				'label' => esc_html__( 'Fields Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #author:focus,
				{{WRAPPER}} .tp-post-comment #commentform #email:focus,
				{{WRAPPER}} .tp-post-comment #commentform #url:focus,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment:focus' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'fieldBgHover',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author:focus,
				{{WRAPPER}} .tp-post-comment #commentform #email:focus,
				{{WRAPPER}} .tp-post-comment #commentform #url:focus,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fieldBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author:focus,
				{{WRAPPER}} .tp-post-comment #commentform #email:focus,
				{{WRAPPER}} .tp-post-comment #commentform #url:focus,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment:focus',	
			]
		);
		$this->add_responsive_control(
			'fieldBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment #commentform #author:focus,
				{{WRAPPER}} .tp-post-comment #commentform #email:focus,
				{{WRAPPER}} .tp-post-comment #commentform #url:focus,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'fieldBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #author:focus,
				{{WRAPPER}} .tp-post-comment #commentform #email:focus,
				{{WRAPPER}} .tp-post-comment #commentform #url:focus,
				{{WRAPPER}} .tp-post-comment form.comment-form textarea#comment:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		/*custom width*/
		$this->add_control(
			'customwidth',
			[
				'label' => esc_html__( 'Custom Width', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'customwidthcomment',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Comment', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment form.comment-form .tp-row' => 'width: {{SIZE}}%;float: left;margin-right: 15px;',
				],
				'condition' => [
					'customwidth' => 'yes',
				],
            ]
        );
		$this->add_control(
            'customwidthauthor',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Author', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment form.comment-form .comment-form-author' => 'width: {{SIZE}}%;float: left;margin-right: 15px;',
				],
				'condition' => [
					'customwidth' => 'yes',
				],
            ]
        );
		$this->add_control(
            'customwidthemail',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Email', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment form.comment-form .comment-form-email' => 'width: {{SIZE}}%;float: left;margin-right: 15px;',
				],
				'condition' => [
					'customwidth' => 'yes',
				],
            ]
        );
		$this->add_control(
            'customwidthwebsite',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Website', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment form.comment-form .comment-form-url' => 'width: {{SIZE}}%;float: left;margin-right: 15px;',
				],
				'condition' => [
					'customwidth' => 'yes',
				],
            ]
        );
		$this->add_control(
            'customwidthcc',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Cookies Consent', 'theplus'),				
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment .comment-form-cookies-consent input#wp-comment-cookies-consent' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .tp-post-comment .comment-form-cookies-consent label' => 'text-align: center;',
				],
				'condition' => [
					'customwidth' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		 /*Comment Field Style*/
		 /*Submit Button Style*/
		$this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__('Submit Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'btnpadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'btnmargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
		$this->add_responsive_control(
			'btnalign',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
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
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #comments .form-submit' => 'text-align: {{VALUE}};',			
				],
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btnTypo',
				'label' => esc_html__( 'Button Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit',
			]
		);
		$this->start_controls_tabs( 'tabs_btn_color_style' );
		$this->start_controls_tab(
			'tab_btn_color_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'btnColor',
			[
				'label' => esc_html__( 'Button Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #submit' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'btnBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btnBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit',	
			]
		);
		$this->add_responsive_control(
			'btnBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment #commentform #submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btnBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_btn_color_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'btnHoverColor',
			[
				'label' => esc_html__( 'Button Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-comment #commentform #submit:hover' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'btnBgHover',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btnBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit:hover',	
			]
		);
		$this->add_responsive_control(
			'btnBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-comment #commentform #submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btnBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-comment #commentform #submit:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Submit Button Style*/
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id = get_queried_object_id();
		$post = get_queried_object();
		$uid_pscmnt=uniqid('tp-comment');
		$comment_args = $this->tp_comment_args();
		$comment = get_comments($post);
		$list_args = array('style' => 'ul', 'short_ping' => true, 'avatar_size' => 100, 'page' => $post_id);	

		$count = !empty($settings['cmtcount']) ? $settings['cmtcount'] : '';
		$cmtcount = 0;
		if ( !empty($count) ) {
			$cmtcount = get_comments_number($post_id);
		}

		ob_start();
			echo '<div class="tp-post-comment tp-widget-'.esc_attr($uid_pscmnt).'">';
				echo '<div id="comments" class="comments-area">';
					if (get_comments_number($post_id) > 0) {
						echo '<ul class="comment-list">';
							echo '<li>';
								if ( !empty($cmtcount) ) {
									echo '<div class="comment-section-title">'.esc_html__('Comment', 'theplus').' ('. $cmtcount.')</div>';
								} else {
									echo '<div class="comment-section-title">'.esc_html__('Comment', 'theplus').'</div>';
								}
							echo '</li>';
							wp_list_comments($list_args, $comment);
						echo '</ul>';
					}
					comment_form($comment_args, $post_id);
				echo '</div>';
			echo '</div>';
		$output = ob_get_clean();

		echo $output;
	}
	
	public function tp_comment_args(){
		$settings = $this->get_settings_for_display();

		$PostTitle = !empty($settings['PostTitle']) ? $settings['PostTitle'] : '';
		$BtnTitle = !empty($settings['BtnTitle']) ? $settings['BtnTitle'] : '';
		$PlaceholderTxt = !empty($settings['PlaceholderTxt']) ? $settings['PlaceholderTxt'] : '';
		$LeaveTxt = !empty($settings['LeaveTxt']) ? $settings['LeaveTxt'] : '';
		$CancelTxt = !empty($settings['CancelTxt']) ? $settings['CancelTxt'] : '';

		$user = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		$args = array(
			'id_form' => 'commentform',
			'class_form'  => 'comment-form',
			'id_submit' => 'submit',
			'title_reply' => esc_html($PostTitle),
			'title_reply_to' => esc_html($LeaveTxt) . esc_html__(' %s', 'theplus' ),
			'cancel_reply_link' => esc_html($CancelTxt),
			'label_submit'      => esc_html($BtnTitle),
			'comment_field' =>  '<div class="tp-row"><div class="tp-col-md-12 tp-col"><label><textarea id="comment" name="comment" cols="45" rows="6" placeholder="'.esc_html($PlaceholderTxt).'" aria-required="true"></textarea></label></div></div>',
			'must_log_in' => '<p class="must-log-in">' .
				sprintf(
				esc_html__( 'You must be %1$slogged in%2$s to post a comment.', 'theplus' ),
				'<a href="'.wp_login_url( apply_filters( "the_permalink", get_permalink() ) ).'">',
				'</a>'
				) . '</p>',
			'logged_in_as' => '<p class="logged-in-as">' .
				sprintf(
				esc_html__( 'Logged in as %1$s%2$s. %3$sLog out?%4$s', 'theplus' ),
				'<a href="'.admin_url( "profile.php" ).'">'.$user_identity,
				'</a>',
				'<a href="'.wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ).'" title="'.esc_attr__("Log out of this account","theplus").'">',
				'</a>'
				) . '</p>',
			'comment_notes_before' => '',
			'comment_notes_after' => '',
		);

		return $args;
	}
    protected function content_template() {
	
    }
}