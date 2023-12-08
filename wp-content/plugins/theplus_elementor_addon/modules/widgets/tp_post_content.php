<?php 
/*
Widget Name: Post Content
Description: Post Content
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

class ThePlus_Post_Content extends Widget_Base {
		
	public function get_name() {
		return 'tp-post-content';
	}

    public function get_title() {
        return esc_html__('Post Content', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-file-text-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-builder');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Post Content', 'theplus' ),
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
            'postContentType', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Content Type', 'theplus'),
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Full Content', 'theplus'),
                    'excerpt' => esc_html__('Excerpt', 'theplus'),                    
                ],
				'condition' => [					
					'posttype' => 'singlepage',
				],
            ]
        );
		$this->add_control(
            'postContentEditorType', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Content', 'theplus'),
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Elementor', 'theplus'),
                    'wordpress' => esc_html__('Wordpress', 'theplus'),                    
                ],
				'condition' => [					
					'posttype' => 'singlepage',
				],
            ]
        );
		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
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
					'{{WRAPPER}} .elementor-widget-container' => 'justify-content: {{VALUE}};',
				],		
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Content Section End*/
		/*Post Excerpts Style*/
		$this->start_controls_section(
            'section_excerpts_style',
            [
                'label' => esc_html__('Excerpts', 'theplus'),
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
					'{{WRAPPER}} > .elementor-widget-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerptstypography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} > .elementor-widget-container',			
			]
		);
		$this->start_controls_tabs( 'tabs_excerpts_style' );
		$this->start_controls_tab(
			'tab_excerpts_normal',
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
					'{{WRAPPER}} > .elementor-widget-container' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'boxBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
			]
		);
		$this->add_responsive_control(
			'boxBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} > .elementor-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxBoxShadow',
				'selector' => '{{WRAPPER}} > .elementor-widget-container',
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
					'{{WRAPPER}} > .elementor-widget-container:hover:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'boxBgHover',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} > .elementor-widget-container:hover:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} > .elementor-widget-container:hover:hover',
			]
		);
		$this->add_responsive_control(
			'boxBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} > .elementor-widget-container:hover:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxBoxShadowHover',
				'selector' => '{{WRAPPER}} > .elementor-widget-container:hover:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Excerpts Style*/
	}
	protected function render($wrapper = false) {
		$settings = $this->get_settings_for_display();
		$posttype = !empty($settings['posttype']) ? $settings['posttype'] : 'singlepage';
		$postContentType = !empty($settings['postContentType']) ? $settings['postContentType'] : 'default';

		$postContentEditorType = !empty($settings['postContentEditorType']) ? $settings['postContentEditorType'] : 'default';

		if(!empty($posttype) && $posttype=='singlepage'){
			if($postContentType == 'default'){
				if(!empty($postContentEditorType) && $postContentEditorType=='wordpress'){
					static $views_ids = array();
					$post_id = get_the_ID();
					if ( ! isset( $post_id ) ) {
						return '';
					}
					if ( isset( $views_ids[ $post_id ] ) ) {
						$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
							defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

						return $is_debug ?
							esc_html__( 'Block Re-rendering halted', 'theplus' ) :
							'';
					}

					$views_ids[ $post_id ] = true;

					global $current_screen;
					if ( isset($current_screen) && method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ) {
						$content = wp_strip_all_tags(get_the_content( '',true, $post));
					} else {
						$post = get_post($post_id);
						if ( ! $post || 'nxt_builder' == $post->post_type) {
							return '';
						}
						
						if ( ('publish' !== $post->post_status && 'draft' !== $post->post_status && 'private' !== $post->post_status) || ! empty( $post->post_password ) ) {
							return '';
						}
						
						$content = apply_filters( 'the_content', $post->post_content );
						echo '<div class="tp-post-content tp-wp-content">' . balanceTags( $content, true ) . '</div>';
					}
					unset( $views_ids[ $post_id ] );
				}else{
					static $posts = [];
					$post = get_post();

					if ( post_password_required( $post->ID ) ) {
						echo get_the_password_form( $post->ID );
						return;
					}
					if ( isset( $posts[ $post->ID ] ) ) {
						return;
					}

					$posts[ $post->ID ] = true;
					$editor = Theplus_Element_Load::elementor()->editor;
					$editmode = $editor->is_edit_mode();

					if ( Theplus_Element_Load::elementor()->preview->is_preview_mode( $post->ID ) ) {
						$content = Theplus_Element_Load::elementor()->preview->builder_wrapper( '' );
					} else {
						$document = Theplus_Element_Load::elementor()->documents->get( $post->ID );
						if ( $document ) {
							$previewType = $document->get_settings( 'preview_type' );
							$previewId = $document->get_settings( 'preview_id' );
	
							if ( 0 === strpos( $previewType, 'single' ) && ! empty( $previewId ) ) {
								$post = get_post( $previewId );
	
								if ( ! $post ) {
									return;
								}
							}
						}
						$editor->set_edit_mode( false );
						$content = Theplus_Element_Load::elementor()->frontend->get_builder_content( $post->ID, true );
	
						if (empty($content)) {
							Theplus_Element_Load::elementor()->frontend->remove_content_filter();
							setup_postdata( $post );
							
							$content = apply_filters( 'the_content', get_the_content() );
	
							wp_link_pages( [
								'before' => '<div class="page-links elementor-page-links"><span class="page-links-title elementor-page-links-title">' . __( 'Pages:', 'theplus' ) . '</span>','after' => '</div>','link_before' => '<span>','link_after' => '</span>','pagelink' => '<span class="screen-reader-text">' . __( 'Page', 'theplus' ) . ' </span>%','separator' => '<span class="screen-reader-text">, </span>',
							] );
	
							Theplus_Element_Load::elementor()->frontend->add_content_filter();
	
							return;
						}else{
							$content = apply_filters( 'the_content', $content );
						}
					} 
					Theplus_Element_Load::elementor()->editor->set_edit_mode( $editmode );
					
					if ( $wrapper ) {
						echo '<div class="tp-post-content">' . balanceTags( $content, true ) . '</div>';
					} else {
						echo $content;
					}
				}
				
			}else if($postContentType == 'excerpt'){
				
				the_excerpt( get_the_ID() );
			}
		}else if(!empty($posttype) && $posttype=='archivepage'){
			if ( is_category() || is_tag() || is_tax() ) {
				echo term_description();
			}
		}
	}
    protected function content_template() {
	
    }
}