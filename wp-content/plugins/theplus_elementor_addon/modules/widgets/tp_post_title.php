<?php 
/*
Widget Name: Post Title
Description: Post Title
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

class ThePlus_Post_Title extends Widget_Base {
		
	public function get_name() {
		return 'tp-post-title';
	}

    public function get_title() {
        return esc_html__('Post Title', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-underline theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-builder');
    }

    protected function register_controls() {
		/*Post Title*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Post Title', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'posttype',
			[
				'label' => esc_html__( 'Post Types', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'singlepage',
				'options' => [
					'singlepage' => esc_html__('Single Page', 'theplus'),
					'archivepage' => esc_html__('Archive Page', 'theplus'),
				],
			]
		);
		$this->add_control(
			'titleprefix',
			[
				'label' => esc_html__( 'Prefix Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$this->add_control(
			'titlepostfix',
			[
				'label' => esc_html__( 'Postfix Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$this->add_control(
			'titleTag',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => esc_html__('H1', 'theplus'),
					'h2' => esc_html__('H2', 'theplus'),
					'h3' => esc_html__('H3', 'theplus'),
					'h4' => esc_html__('H4', 'theplus'),
					'h5' => esc_html__('H5', 'theplus'),
					'h6' => esc_html__('H6', 'theplus'),
					'div' => esc_html__('Div', 'theplus'),
					'span' => esc_html__('Span', 'theplus'),
					'p' => esc_html__('P', 'theplus'),
				],
			]
		);
		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Box Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'flex-start',		
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
					'{{WRAPPER}} .tp-post-title' => 'justify-content: {{VALUE}};',
				],		
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'textAlignment',
			[
				'label' => esc_html__( 'Text Alignment', 'theplus' ),
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
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .tp-post-title' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*Post Title*/
		/*Extra Options*/
		$this->start_controls_section(
			'extra_opt_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'titleLink',[
				'label'   => esc_html__( 'Link', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
			]
		);
		$this->add_control(
			'limitCountType',
			[
				'label' => esc_html__( 'Length Limit', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('No Limit', 'theplus'),
					'limitByLetter' => esc_html__('Based on Letters', 'theplus'),
					'limitByWord' => esc_html__('Based on Words', 'theplus'),
				],
			]
		);		
		$this->add_control(
			'titleLimit',
			[
				'label' => esc_html__( 'Limit of Words/Character', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 5000,
				'step' => 1,
				'default' => 10,
				'condition' => [					
					'limitCountType!' => 'default',
				],
			]
		);
		$this->end_controls_section();
		/*Extra Options*/
		/*Post Title Style*/
		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title',
			]
		);
		$this->start_controls_tabs( 'tabs_title_style' );
		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'NormalColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'boxBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title',
			]
		);
		$this->add_responsive_control(
			'boxBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-title a,{{WRAPPER}} .tp-post-title .tp-entry-title',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'HoverColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-title a:hover,{{WRAPPER}} .tp-post-title .tp-entry-title:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'boxBgHover',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-post-title a:hover,{{WRAPPER}} .tp-post-title .tp-entry-title:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-title a:hover,{{WRAPPER}} .tp-post-title .tp-entry-title:hover',
			]
		);
		$this->add_responsive_control(
			'boxBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-title a:hover,{{WRAPPER}} .tp-post-title .tp-entry-title:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-title a:hover,{{WRAPPER}} .tp-post-title .tp-entry-title:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Title Style*/
		/*Post prefix postfix*/
		$this->start_controls_section(
            'section_prepost_style',
            [
                'label' => esc_html__('Prefix/Postfix', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
				    'relation' => 'or',
				    'terms' => [
				    	[
				        	'name' => 'titleprefix','operator' => '!=','value' => '',
				        ],
						[   							
							'name' => 'titlepostfix','operator' => '!=','value' => '',
						],
				    ],
				],
            ]
        );
		$this->add_responsive_control(
			'prepostpadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-title .tp-post-title-prepost' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'prepostmargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-title .tp-post-title-prepost' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
		$this->add_responsive_control(
            'preoffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Prefix Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-post-title .tp-post-title-prepost.tp-prefix' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'postoffset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Postfix Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-post-title .tp-post-title-prepost.tp-postfix' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'preposttypography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-post-title .tp-post-title-prepost',
				'separator' => 'after',	
			]
		);
		$this->add_control(
			'prepostColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-title .tp-post-title-prepost' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'prepostboxBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-title .tp-post-title-prepost',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'prepostboxBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-title .tp-post-title-prepost',
			]
		);
		$this->add_responsive_control(
			'prepostboxBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-title .tp-post-title-prepost' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'prepostboxBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-title .tp-post-title-prepost',
			]
		);
		$this->end_controls_section();
		/*Post Prefix postfix*/
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id = get_the_ID();
		
	    $titletag = (!empty($settings['titleTag'])) ? $settings['titleTag'] : 'h3';
	    $titleLink = (!empty($settings['titleLink'])) ? $settings['titleLink'] : 'no';
        $limitCountType = (!empty($settings['limitCountType'])) ? $settings['limitCountType'] : '';
        $text_limit = (!empty($settings['titleLimit'])) ? $settings['titleLimit'] : 10;
        $titleprefix = (!empty($settings['titleprefix'])) ? $settings['titleprefix'] : '';
        $titlepostfix = (!empty($settings['titlepostfix'])) ? $settings['titlepostfix'] : '';
		
		if($settings['posttype']=='archivepage'){
			add_filter( 'get_the_archive_title', function ($title) {    
				if ( is_category() ) {    
					$title = single_cat_title( '', false );    
				} else if ( is_tag() ) {    
					$title = single_tag_title( '', false );    
				} else if ( is_author() ) {    
					$title = '<span class="vcard">' . get_the_author() . '</span>' ;    
				} else if ( is_tax() ) {
					$title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
				} else if (is_post_type_archive()) {
					$title = post_type_archive_title( '', false );
				} else if (is_search()) {
					$title = get_search_query();
				}
				return $title;    
			});
		}
					
		if(!empty($settings['posttype'])){
			if( $limitCountType == 'limitByWord' ){
				if($settings['posttype']=='singlepage'){
					$title = wp_trim_words(get_the_title($post_id),$text_limit);
				}else if($settings['posttype']=='archivepage'){
					$title = wp_trim_words(get_the_archive_title(),$text_limit);
				}			
			} else if( $limitCountType == 'limitByLetter' ){
				if($settings['posttype']=='singlepage'){
					$title = substr(wp_trim_words(get_the_title($post_id)),0, $text_limit) . '...';
				}else if($settings['posttype']=='archivepage'){
					$title = substr(wp_trim_words(get_the_archive_title()),0, $text_limit) . '...';
				}				
			} else {
				if($settings['posttype']=='singlepage'){
					$title = get_the_title($post_id);
				}else if($settings['posttype']=='archivepage'){					
					$title = get_the_archive_title();
				}				
			}
		}
		
		$output = '<div class="tp-post-title">';
			$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['boxBg_image'],$settings['boxBgHover_image']) : '';
			if(!empty($titleLink) && $titleLink=='yes'){
				$output .= '<a class="'.esc_attr($lz1).'" href="'.get_the_permalink().'" >';
			}
			$output .= '<'.theplus_validate_html_tag($titletag).' class="tp-entry-title '.esc_attr($lz1).'">';	
						$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['prepostboxBg_image']) : '';
						if(!empty($titleprefix)){
							$output .='<span class="tp-post-title-prepost tp-prefix '.esc_attr($lz2).'">'.esc_html($titleprefix).'</span>';
						}
						$output .= $title;
						if(!empty($titlepostfix)){
							$output .='<span class="tp-post-title-prepost tp-postfix '.esc_attr($lz2).'">'.esc_html($titlepostfix).'</span>';
						}
			$output .= '</'.theplus_validate_html_tag($titletag).'>';
			if(!empty($titleLink) && $titleLink=='yes'){
				$output .= "</a>";
			}			
		$output .= "</div>";
		
		 echo $output; 		
	}
	
    protected function content_template() {
	
    }

}