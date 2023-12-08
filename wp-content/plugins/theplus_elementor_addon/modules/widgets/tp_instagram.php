<?php 
/*
Widget Name: Instagram
Description: Instagram
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Instagram extends Widget_Base {
		
	public function get_name() {
		return 'tp-instagram';
	}

    public function get_title() {
        return esc_html__('Instagram', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-instagram theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-depreciated');
    }
	public function get_keywords() {
		return [ 'instagram', 'feed', 'gallery', 'photos', 'images' ];
	}
	protected function register_controls() {
		/*layout start*/
		$this->start_controls_section(
			'section_insta_style',
			[
				'label' => esc_html__( 'Instagram Layout', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'deprecated_notice_insta',
            [
                'type' => \Elementor\Controls_Manager::DEPRECATED_NOTICE,
				'widget' => 'Instagram',
				'since' => '5.0.4',
				'last' => '5.0.8',
				'plugin' => 'in',
				'replacement' => 'Social Feed',
            ]
        );
		$this->add_control(
			'insta_layout_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->end_controls_section();
		/*layout end*/

  		$this->start_controls_section(
  			'theplus_section_instafeed_settings_account',
  			[
  				'label' => esc_html__( 'Instagram Account Settings', 'theplus' )
  			]
  		);
		$this->add_control(
			'instafeed_source',
			[
				'label' => esc_html__( 'Instagram Feed Source', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'username',
				'options' => [
					'username' => esc_html__( 'By Username', 'theplus' ),
					'access_token' => esc_html__( 'By Access Token', 'theplus' ),
				],
			]
		);

		$this->add_control(
			'instafeed_username',
			[
				'label' => esc_html__( 'Enter Username', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter Instagram Username', 'theplus' ),
				'condition' => [
					'instafeed_source' => 'username',
				],
			]
		);
		$this->add_control(
			'theplus_instafeed_access_token_note',
			[
				'label' => esc_html__( 'Note : Due to API Updates on Instagram Our Access Token Based option is not working. We are working closely on that and patch will be released soon. You need to use "Username" based feed for now. Sorry for the inconvenience.', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'instafeed_source' => 'access_token',
				],
			]
		);		
		$this->add_control(
			'theplus_instafeed_access_token',
			[
				'label' => esc_html__( 'Access Token', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
				'description' => 'How to <a href="http://www.jetseotools.com/instagram-access-token/" class="theplus-btn" target="_blank">Get Access Token?</a>',
				'condition' => [
					'instafeed_source' => 'access_token',
				],
			]
		);

		$this->add_control(
			'theplus_instafeed_user_id',
			[
				'label' => esc_html__( 'User ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' =>'',
				'description' => 'How to <a href="https://codeofaninja.com/tools/find-instagram-user-id" class="theplus-btn" target="_blank">Get User ID?</a>',
				'condition' => [
					'instafeed_source' => 'access_token',
				],
			]
		);
		$this->add_control(
			'theplus_instafeed_client_id',
			[
				'label' => esc_html__( 'Client ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
				'description' => 'How to <a href="https://www.instagram.com/developer/clients/manage/" class="theplus-btn" target="_blank">Get Client ID?</a>',
				'condition' => [
					'instafeed_source' => 'access_token',
				],
			]
		);



		$this->end_controls_section();

  		$this->start_controls_section(
  			'theplus_section_instafeed_settings_content',
  			[
  				'label' => esc_html__( 'Feed Settings', 'theplus' )
  			]
  		);

		

		$this->add_control(
			'theplus_instafeed_sort_by',
			[
				'label' => esc_html__( 'Sort By', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'theplus' ),
					'most-recent' => esc_html__( 'Most Recent',   'theplus' ),
					'least-recent' => esc_html__( 'Least Recent', 'theplus' ),
					'most-liked' => esc_html__( 'Most Likes', 'theplus' ),
					'least-liked' => esc_html__( 'Least Likes', 'theplus' ),
					'most-commented' => esc_html__( 'Most Commented', 'theplus' ),
					'least-commented' => esc_html__( 'Least Commented', 'theplus' ),
					'random' => esc_html__( 'Random', 'theplus' ),
				],
				'condition' => [
					'instafeed_source' => 'access_token',
				],
			]
		);

		$this->add_control(
			'theplus_instafeed_image_count',
			[
				'label' => esc_html__( 'Max Visible Images', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 12,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 12,
					],
				],
			]
		);

		$this->add_control(
			'theplus_instafeed_image_resolution',
			[
				'label' => esc_html__( 'Image Resolution', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard_resolution',
				'options' => [
					'standard_resolution' => esc_html__( 'Original Size', 'theplus' ),
					'thumbnail' => esc_html__( 'Thumbnail (150x150)', 'theplus' ),
					'low_resolution' => esc_html__( 'Low Res (306x306)',   'theplus' ),					
				],
				'condition' => [
					'instafeed_source' => 'access_token',
				],
			]
		);

		

		$this->end_controls_section();


  		$this->start_controls_section(
  			'theplus_section_instafeed_settings_general',
  			[
  				'label' => esc_html__( 'General Settings', 'theplus' )
  			]
  		);		
		$this->add_control(
			'desktop_column',
			[
				'label' => esc_html__( 'Desktop Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'theplus_instafeed_carousels!' => ['yes']
				],
			]
		);
		$this->add_control(
			'tablet_column',
			[
				'label' => esc_html__( 'Tablet Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'theplus_instafeed_carousels!' => ['yes']
				],
			]
		);
		$this->add_control(
			'mobile_column',
			[
				'label' => esc_html__( 'Mobile Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'theplus_instafeed_carousels!' => ['yes']
				],
			]
		);
		
		$this->add_control(
			'theplus_instafeed_grid_title',
			[
				'label' => esc_html__( 'Grid Layout Image', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'theplus_instafeed_force_square',
			[
				'label' => esc_html__( 'Force Square Image?', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'theplus_instafeed_sq_image_size',
			[
				'label' => esc_html__( 'Image Dimension (px)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 300,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-instafeed-square-img .theplus-insta-img-wrap img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; object-fit: cover;',
				],
 				'condition' => [
					'theplus_instafeed_force_square' => 'yes',
				],
			]
		);
		$this->add_control(
			'theplus_instafeed_masonry_heading',
			[
				'label' => esc_html__( 'Masonry Layout', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'theplus_instafeed_masonry',
			[
				'label' => esc_html__( 'Enable Masonry?', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'theplus_instafeed_carousels_heading',
			[
				'label' => esc_html__( 'Carousels', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'theplus_instafeed_carousels',
			[
				'label' => esc_html__( 'Enable Carousels?', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'theplus_instafeed_pagination_heading',
			[
				'label' => esc_html__( 'Pagination', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'theplus_instafeed_masonry!' => 'yes',
					'theplus_instafeed_carousels!' => 'yes',
					'instafeed_source' => 'access_token',
				],
			]
		);

		$this->add_control(
			'theplus_instafeed_pagination',
			[
				'label' => esc_html__( 'Enable Load More?', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'theplus_instafeed_masonry!' => 'yes',
					'theplus_instafeed_carousels!' => 'yes',
					'instafeed_source' => 'access_token',
				],
			]
		);
		$this->add_control(
			'tp_if_p_text',
			[
				'label' => esc_html__( 'Load More Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Load More', 'theplus' ),
				'placeholder' => esc_html__( 'Load More Text', 'theplus' ),
				'condition' => [
					'theplus_instafeed_pagination' => 'yes',
					'theplus_instafeed_masonry!' => 'yes',
					'theplus_instafeed_carousels!' => 'yes',
					'instafeed_source' => 'access_token',
				],
			]
		);
		$this->add_control(
			'tp_if_p_loading_text',
			[
				'label' => esc_html__( 'Loading Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Loading...', 'theplus' ),
				'placeholder' => esc_html__( 'Loading Text', 'theplus' ),
				'condition' => [
					'theplus_instafeed_pagination' => 'yes',
					'theplus_instafeed_masonry!' => 'yes',
					'theplus_instafeed_carousels!' => 'yes',
					'instafeed_source' => 'access_token',
				],
			]
		);		
		$this->add_control(
			'theplus_instafeed_caption_heading',
			[
				'label' => esc_html__( 'Link & Content', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'theplus_instafeed_img_icn',
			[
				'label' => esc_html__( 'Display Popup', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'show-instaimage',
				'default' => '',
			]
		);
		/*popup image icon start*/
		$this->add_control(
			'loop_select_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'theplus_instafeed_img_icn' => 'show-instaimage',
				],
			]
		);
		/*popup image icon end*/
		
		$this->add_control(
			'theplus_instafeed_caption',
			[
				'label' => esc_html__( 'Display Caption', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'show-caption',
				'default' => '',
			]
		);
		$this->add_control(
			'caption_count',
			[
				'label' => esc_html__( 'Caption Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 500,
				'step' => 1,
				'default' => 50,
				'condition'   => [
					'theplus_instafeed_caption'    => 'show-caption',
					//'instafeed_source'    => 'username',
				],
			]
			
		);
		$this->add_control(
			'theplus_instafeed_likes',
			[
				'label' => esc_html__( 'Display Like', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'theplus_instafeed_comments',
			[
				'label' => esc_html__( 'Display Comments', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'theplus_instafeed_link',
			[
				'label' => esc_html__( 'Enable Link', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'theplus_instafeed_link_target',
			[
				'label' => esc_html__( 'Open in new window?', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'theplus_instafeed_link' => 'yes',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'theplus_section_instafeed_styles_general',
			[
				'label' => esc_html__( 'Instagram Feed Styles', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_responsive_control(
			'theplus_instafeed_overlay_color_spacing',
			[
				'label' => esc_html__( 'Padding Gap', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-1 .theplus-insta-feed.grid-item.theplus-insta-box,{{WRAPPER}}  .theplus-instagram-feed.style-2 .theplus-insta-feed.grid-item.theplus-insta-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'theplus_instafeed_margin_st2',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-2 .theplus-insta-feed' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'theplus_instafeed_box_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-insta-feed-wrap',	
				'condition'    => [					
					'insta_layout_style' => 'style-1',					
				],
			]
		);

		$this->add_responsive_control(
			'theplus_instafeed_box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .theplus-insta-feed-wrap' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-1',					
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'theplus_instafeed_box_border_st2',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-instagram-feed.style-2 .theplus-insta-feed .theplus-insta-feed-inner',
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);

		$this->add_responsive_control(
			'theplus_instafeed_box_border_radius_st2',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-2 .theplus-insta-feed .theplus-insta-feed-inner' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);
		$this->start_controls_tabs( 'tabs_tpis_style' );
		$this->start_controls_tab(
			'tab_tpis_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tpis_normal_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-insta-feed-wrap',
				'condition'    => [					
					'insta_layout_style' => 'style-1',					
				],
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tpis_normal_box_shadow_st2',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-instagram-feed.style-2 .theplus-insta-feed-inner',
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tpis_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tpis_hover_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-insta-feed-wrap:hover',
				'condition'    => [					
					'insta_layout_style' => 'style-1',					
				],
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tpis_hover_box_shadow_st2',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-instagram-feed.style-2 .theplus-insta-feed:hover .theplus-insta-feed-inner',
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*like & comment start*/
		$this->start_controls_section(
			'theplus_section_like_comment',
			[
				'label' => esc_html__( 'Like & Comments Styles', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'theplus_instafeed_like_comments_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY
				],
				'selector' => '{{WRAPPER}} .theplus-insta-likes-comments > span',
			]
		);
		$this->add_control(
			'theplus_instafeed_like_comments_color',
			[
				'label' => esc_html__( 'Like & Comments Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f4ff33',
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed .theplus-insta-likes-comments > span' => 'color: {{VALUE}};',
				],
			]
		);		
		$this->add_responsive_control(
			'theplus_like_comments_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
	 					'{{WRAPPER}} .theplus-instagram-feed.style-2 .insta-like-comment-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	 			],
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'theplus_like_comments_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-2 .insta-like-comment-img' => 'background: {{VALUE}};',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'theplus_like_comments_border_st2',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-instagram-feed.style-2 .insta-like-comment-img',
				'separator' => 'before',
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);
		$this->add_responsive_control(
			'theplus_like_comments_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-2 .insta-like-comment-img' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);
		$this->end_controls_section();
		/*like & comment end*/
		/*image icon start*/
		$this->start_controls_section(
			'theplus_section_img_icn',
			[
				'label' => esc_html__( 'Popup Image Icon', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_responsive_control(
			'img_icn_width',
			[
				'label' => esc_html__( 'Image Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed .insta-image img ,{{WRAPPER}} .theplus-instagram-feed .insta-image' => 'max-width: {{SIZE}}{{UNIT}};height: auto;',
				],
			]
		);
		$this->add_responsive_control(
				'img_icn_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .insta-image img' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					],
				]
			);
		$this->add_control(
            'theplus_instafeed_img_icn_top',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Top Offset', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-1 .theplus-insta-feed-wrap .insta-image > a' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [					
					'theplus_instafeed_img_icn' => 'show-instaimage',	
					'theplus_instafeed_caption!' => 'yes',	
					'theplus_instafeed_likes!' => 'yes',	
					'theplus_instafeed_comments!' => 'yes',	
					'theplus_instafeed_link!' => 'yes',	
				],
            ]
        );
		$this->end_controls_section();
		/*image icon start*/
		/*caption start*/
		$this->start_controls_section(
			'theplus_section_caption',
			[
				'label' => esc_html__( 'Caption Styles', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
             	'name' => 'theplus_instafeed_caption_typography',
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY
                ],
				'selector' => '{{WRAPPER}} .theplus-insta-info-wrap .insta-caption',
			]
		);
		$this->add_control(
			'theplus_instafeed_caption_color',
			[
				'label' => esc_html__( 'Caption Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .theplus-insta-info-wrap .insta-caption' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*caption end*/
		/*load more button start*/
		$this->start_controls_section(
            'theplus_section_load_more_btn',
            [
                'label' => esc_html__( 'Load More Button Style', 'theplus' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'theplus_instafeed_load_more_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
	 					'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	 			],
			]
		);

		$this->add_responsive_control(
			'theplus_instafeed_load_more_btn_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
	 					'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	 			],
			]
		);
		
		$this->add_responsive_control(
			'load_more_btn_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
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
				'default' => 'center',
				'selectors' => [
	 					'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button-wrap' => 'text-align: {{VALUE}};',
	 			],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
	         'name' => 'theplus_instafeed_load_more_btn_typography',
				'selector' => '{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button',
			]
		);

		$this->start_controls_tabs( 'theplus_instafeed_load_more_btn_tabs' );

			// Normal State Tab
			$this->start_controls_tab( 'theplus_instafeed_load_more_btn_normal', [ 'label' => esc_html__( 'Normal', 'theplus' ) ] );

			$this->add_control(
				'theplus_instafeed_load_more_btn_normal_text_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'theplus_cta_btn_normal_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button' => 'background: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'theplus_instafeed_load_more_btn_normal_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button',
				]
			);

			$this->add_responsive_control(
				'theplus_instafeed_load_more_btn_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'theplus_instafeed_load_more_btn_shadow',
					'selector' => '{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button',
					'separator' => 'before'
				]
			);

			$this->end_controls_tab();

			// Hover State Tab
			$this->start_controls_tab( 'theplus_instafeed_load_more_btn_hover', [ 'label' => esc_html__( 'Hover', 'theplus' ) ] );

			$this->add_control(
				'theplus_instafeed_load_more_btn_hover_text_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'theplus_instafeed_load_more_btn_hover_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button:hover' => 'background: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'theplus_instafeed_load_more_btn_hover_border_color',
				[
					'label' => esc_html__( 'Border Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button:hover' => 'border-color: {{VALUE}};',
					],
				]

			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'theplus_instafeed_load_more_btn_hover_shadow',
					'selector' => '{{WRAPPER}} .theplus-instagram-feed .theplus-load-more-button:hover',
					'separator' => 'before'
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();
		/*load more button end*/		
		/*background overlay start*/
		$this->start_controls_section(
			'theplus_section_instafeed_styles_content',
			[
				'label' => esc_html__( 'Background Overlay', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		/*bg color gradiant start*/
		$this->add_control(
			'theplus_insta_overlay_color',
			[
				'label' => esc_html__( 'Hover Overlay Color', 'theplus' ),
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
			'title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0, .75)',
				'selectors' => [
					'{{WRAPPER}} .theplus-insta-feed .theplus-insta-feed-wrap .theplus-insta-img-wrap::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'theplus_insta_overlay_color' => 'solid',
				],
			]
		);
		$this->add_control(
            'overlay_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'theplus_insta_overlay_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'overlay_gradient_color1_control',
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
					'theplus_insta_overlay_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'overlay_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'theplus_insta_overlay_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'overlay_gradient_color2_control',
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
					'theplus_insta_overlay_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'overlay_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'theplus_insta_overlay_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_angle', [
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
					'{{WRAPPER}} .theplus-insta-feed .theplus-insta-feed-wrap::after' => 'background-image: linear-gradient({{SIZE}}{{UNIT}}, {{overlay_gradient_color1.VALUE}} {{overlay_gradient_color1_control.SIZE}}{{overlay_gradient_color1_control.UNIT}}, {{overlay_gradient_color2.VALUE}} {{overlay_gradient_color2_control.SIZE}}{{overlay_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'theplus_insta_overlay_color' => 'gradient',
					'overlay_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'title_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .theplus-insta-feed .theplus-insta-feed-wrap::after' => 'background-image: radial-gradient(at {{VALUE}}, {{overlay_gradient_color1.VALUE}} {{overlay_gradient_color1_control.SIZE}}{{overlay_gradient_color1_control.UNIT}}, {{overlay_gradient_color2.VALUE}} {{overlay_gradient_color2_control.SIZE}}{{overlay_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'theplus_insta_overlay_color' => 'gradient',
					'overlay_gradient_style' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		
		$this->add_responsive_control(
			'theplus_instafeed_spacing',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .theplus-insta-feed-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-1',					
				],
			]
		);
		$this->add_responsive_control(
			'theplus_instafeed_spacing_st2',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .theplus-instagram-feed.style-2 .theplus-insta-feed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [					
					'insta_layout_style' => 'style-2',					
				],
			]
		);
		
		$this->end_controls_section();
		/*background overlay end*/
		/*carousel option start*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'theplus_instafeed_carousels' => 'yes',
				],
            ]
        );		
		$this->add_control(
			'slider_direction',
			[
				'label'   => esc_html__( 'Slider Mode', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
				],
			]
		);		
		$this->add_control(
            'slide_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
            ]
        );
		
		$this->start_controls_tabs( 'tabs_carousel_style' );
		$this->start_controls_tab(
			'tab_carousel_desktop',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_desktop_column',
			[
				'label'   => esc_html__( 'Desktop Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_carousel_desktop_columns(),
			]
		);
		$this->add_control(
			'steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
            'slider_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Padding', 'theplus'),
				'size_units' => [ 'px' ],
				
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick:not(.multi-row) .slick-initialized .slick-slide' => 'margin: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .list-carousel-slick.multi-row .slick-initialized .slick-slide' => 'margin: 0 {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .list-carousel-slick.multi-row .slick-initialized .slick-slide > div' => 'margin: {{SIZE}}{{UNIT}} 0',
				],
            ]
        );		
		$this->add_control(
			'slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_pause_hover',
			[
				'label'   => esc_html__( 'Pause On Hover', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_adaptive_height',
			[
				'label'   => esc_html__( 'Adaptive Height', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
            'autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3000,
				],
				'condition' => [
					'slider_autoplay' => 'yes',
				],
            ]
        );
		
		$this->add_control(
			'slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_dots_style',
			[
				'label'   => esc_html__( 'Dots Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
					'style-7' => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition'    => [
					'slider_dots' => ['yes'],
				],
			]
		);
		$this->add_control(
			'dots_border_color',
			[
				'label' => esc_html__( 'Dots Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 li button' => '-webkit-box-shadow:inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li.slick-active button' => '-webkit-box-shadow:inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button' => 'border-color:{{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-3 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-4 li button' => '-webkit-box-shadow: inset 0 0 0 0px {{VALUE}};-moz-box-shadow: inset 0 0 0 0px {{VALUE}};box-shadow: inset 0 0 0 0px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-1','style-2','style-3','style-5'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_bg_color',
			[
				'label' => esc_html__( 'Dots Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button,{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 button' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-3','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_border_color',
			[
				'label' => esc_html__( 'Dots Active Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 .slick-active button:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-6'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_bg_color',
			[
				'label' => esc_html__( 'Dots Active Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 .slick-active button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 .slick-active button' => 'background: {{VALUE}};',					
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
            'dots_top_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Dots Top Padding', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slider.slick-dotted' => 'padding-bottom: {{SIZE}}{{UNIT}};',					
				],				
				'condition'    => [
					'slider_dots' => 'yes',
				],
            ]
        );
		$this->add_control(
			'hover_show_dots',
			[
				'label'   => esc_html__( 'On Hover Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_arrows_style',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'condition'    => [
					'slider_arrows' => ['yes'],
				],
			]
		);
		$this->add_control(
			'arrows_position',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right' => esc_html__( 'Top-Right', 'theplus' ),
					'bottm-left' => esc_html__( 'Bottom-Left', 'theplus' ),
					'bottom-center' => esc_html__( 'Bottom-Center', 'theplus' ),
					'bottom-right' => esc_html__( 'Bottom-Right', 'theplus' ),
				],				
				'condition'    => [
					'slider_arrows' => ['yes'],
					'slider_arrows_style' => ['style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'arrow_bg_color',
			[
				'label' => esc_html__( 'Arrow Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-6:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-6:before' => 'background: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-3','style-4','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_icon_color',
			[
				'label' => esc_html__( 'Arrow Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6 .icon-wrap' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => esc_html__( 'Arrow Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_icon_color',
			[
				'label' => esc_html__( 'Arrow Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6:hover .icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_section_arrow',
			[
				'label'   => esc_html__( 'Outer Content Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
					'slider_arrows_style' => ['style-1','style-2','style-5','style-6'],
				],
			]
		);
		$this->add_control(
			'hover_show_arrow',
			[
				'label'   => esc_html__( 'On Hover Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
            ]
        );
		$this->add_control(
			'slider_center_effects',
			[
				'label'   => esc_html__( 'Center Slide Effects', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_carousel_center_effects(),
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
			]
		);
		$this->add_control(
            'scale_center_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center,
					{{WRAPPER}} .list-carousel-slick .slick-slide.scc-animate' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});opacity:1;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_control(
            'scale_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'shadow_active_slide',
				'selector' => '{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center .info-box-bg-box',
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'shadow',
				],
			]
		);
		$this->add_control(
            'opacity_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Opacity', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.7,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => 'opacity:{{SIZE}}',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects!' => 'none',
				],
            ]
        );
		$this->add_control(
			'slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'slide_row_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Row Top Space', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick[data-slider_rows="2"] .slick-slide > div:last-child,{{WRAPPER}} .list-carousel-slick[data-slider_rows="3"] .slick-slide > div:nth-last-child(-n+2)' => 'padding-top:{{SIZE}}px',
				],
				'condition'    => [
					'slider_rows' => ['2','3'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_tablet_column',
			[
				'label'   => esc_html__( 'Tablet Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_tablet_columns(),
			]
		);
		$this->add_control(
			'tablet_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		
		$this->add_control(
			'slider_responsive_tablet',
			[
				'label'   => esc_html__( 'Responsive Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tablet_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_tablet' => 'yes',
					'tablet_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tablet_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
					'tablet_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_mobile_column',
			[
				'label'   => esc_html__( 'Mobile Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_carousel_mobile_columns(),
			]
		);
		$this->add_control(
			'mobile_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		
		$this->add_control(
			'slider_responsive_mobile',
			[
				'label'   => esc_html__( 'Responsive Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'mobile_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_mobile' => 'yes',
					'mobile_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'mobile_slider_dots',
			[
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
					'mobile_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*carousel option end*/
		/*Extra options*/
		$this->start_controls_section(
            'section_extra_options_styling',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'messy_column',
			[
				'label' => esc_html__( 'Messy Columns', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->start_controls_tabs( 'tabs_extra_option_style' );
		$this->start_controls_tab(
			'tab_column_1',
			[
				'label' => esc_html__( '1', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_1',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 1', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_2',
			[
				'label' => esc_html__( '2', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_2',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 2', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_3',
			[
				'label' => esc_html__( '3', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_3',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 3', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_4',
			[
				'label' => esc_html__( '4', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_4',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 4', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_5',
			[
				'label' => esc_html__( '5', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_5',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 5', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_column_6',
			[
				'label' => esc_html__( '6', 'theplus' ),
				'condition'    => [
					'messy_column' => ['yes'],
				],
			]
		);
		$this->add_responsive_control(
            'desktop_column_6',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Column 6', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 2,
					],
					'%' => [
						'min' => 70,
						'max' => 70,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'messy_column' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->end_controls_section();
		/*Extra options*/
		/*On Scroll View Animation*/
		$this->start_controls_section(
            'section_animation_styling',
            [
                'label' => esc_html__('On Scroll View Animation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_control(
			'animation_effects',
			[
				'label'   => esc_html__( 'Choose Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_animation_options(),
			]
		);		
		$this->add_control(
            'animation_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
			'animated_column_list',
			[
				'label'   => esc_html__( 'List Load Animation', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Content Animation Block', 'theplus' ),
					'stagger' => esc_html__( 'Stagger Based Animation', 'theplus' ),
					'columns' => esc_html__( 'Columns Based Animation', 'theplus' ),
				],
				'condition'    => [
					'animation_effects!' => [ 'no-animation' ],
				],
			]
		);
		$this->add_control(
            'animation_stagger',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Stagger', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 150,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 6000,
						'step' => 10,
					],
				],				
				'condition' => [
					'animation_effects!' => [ 'no-animation' ],
					'animated_column_list' => 'stagger',
				],
            ]
        );
		$this->add_control(
            'animation_duration_default',
            [
				'label'   => esc_html__( 'Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition'    => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animate_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_duration_default' => 'yes',
				],
            ]
        );
		$this->add_control(
			'animation_out_effects',
			[
				'label'   => esc_html__( 'Out Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_out_animation_options(),
				'separator' => 'before',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Out Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
            'animation_out_duration_default',
            [
				'label'   => esc_html__( 'Out Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
					'animation_out_duration_default' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		/*On Scroll View Animation*/
		
	}


	protected function render( ) {

		$settings 	= $this->get_settings();
		$image_limit 	= ($settings['theplus_instafeed_image_count']['size']) ? $settings['theplus_instafeed_image_count']['size'] : 12;
		$force_square = ($settings['theplus_instafeed_force_square'] == 'yes') ? "theplus-instafeed-square-img" : '';	 
		$desktop_class=$tablet_class=$mobile_class='';
		if($settings['theplus_instafeed_carousels']!='yes'){
			$desktop_class='tp-col-lg-'.esc_attr($settings['desktop_column']);
			$tablet_class='tp-col-md-'.esc_attr($settings['tablet_column']);
			$mobile_class='tp-col-sm-'.esc_attr($settings['mobile_column']).' tp-col-'.esc_attr($settings['mobile_column']);
		}
		$classes 		= $force_square;
		$data_attr= $desktop_class.'  ' . $tablet_class.' '.$mobile_class;
		$insta_layout_style = $settings['insta_layout_style'];
	 
	  //animation load
		$animation_effects=$settings["animation_effects"];
		$animation_delay= (!empty($settings["animation_delay"]["size"])) ? $settings["animation_delay"]["size"] : 50;
		$animation_stagger=$settings["animation_stagger"]["size"];
		$animated_columns='';
		if($animation_effects=='no-animation'){
			$animated_class='';
			$animation_attr='';
		}else{
			$animate_offset = theplus_scroll_animation();
			$animated_class = 'animate-general';
			$animation_attr = ' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay).'"';
			$animation_attr .= ' data-animate-offset="'.esc_attr($animate_offset).'"';
			if($settings["animated_column_list"]=='stagger'){
				$animated_columns='animated-columns';
				$animation_attr .=' data-animate-columns="stagger"';
				$animation_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
			}else if($settings["animated_column_list"]=='columns'){
				$animated_columns='animated-columns';
				$animation_attr .=' data-animate-columns="columns"';
			}
			if($settings["animation_duration_default"]=='yes'){
				$animate_duration=$settings["animate_duration"]["size"];
				$animation_attr .= ' data-animate-duration="'.esc_attr($animate_duration).'"';
			}
			if(!empty($settings["animation_out_effects"]) && $settings["animation_out_effects"]!='no-animation'){
				$animation_attr .= ' data-animate-out-type="'.esc_attr($settings["animation_out_effects"]).'" data-animate-out-delay="'.esc_attr($settings["animation_out_delay"]["size"]).'"';					
				if($settings["animation_out_duration_default"]=='yes'){						
					$animation_attr .= ' data-animate-out-duration="'.esc_attr($settings["animation_out_duration"]["size"]).'"';
				}
			}
		}
	  
	  //carousel option
		$isotope =$data_slider =$arrow_class=$data_carousel='';
		if($settings['theplus_instafeed_carousels']=='yes'){
		
			$slider_direction = ($settings['slider_direction']=='vertical') ? 'true' : 'false';
			$data_slider .=' data-slider_direction="'.esc_attr($slider_direction).'"';
			$data_slider .=' data-slide_speed="'.esc_attr($settings["slide_speed"]["size"]).'"';
			
			$data_slider .=' data-slider_desktop_column="'.esc_attr($settings['slider_desktop_column']).'"';
			$data_slider .=' data-steps_slide="'.esc_attr($settings['steps_slide']).'"';
			
			$slider_draggable= ($settings["slider_draggable"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
			$slider_infinite= ($settings["slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
			$slider_pause_hover= ($settings["slider_pause_hover"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
			$slider_adaptive_height= ($settings["slider_adaptive_height"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';
			$slider_autoplay= ($settings["slider_autoplay"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_autoplay="'.esc_attr($slider_autoplay).'"';
			$data_slider .=' data-autoplay_speed="'.esc_attr($settings["autoplay_speed"]["size"]).'"';
			
			//tablet
			$data_slider .=' data-slider_tablet_column="'.esc_attr($settings['slider_tablet_column']).'"';
			$data_slider .=' data-tablet_steps_slide="'.esc_attr($settings['tablet_steps_slide']).'"';
			$slider_responsive_tablet=$settings['slider_responsive_tablet'];
			$data_slider .=' data-slider_responsive_tablet="'.esc_attr($slider_responsive_tablet).'"';
			if(!empty($settings['slider_responsive_tablet']) && $settings['slider_responsive_tablet']=='yes'){				
				$tablet_slider_draggable= ($settings["tablet_slider_draggable"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_draggable="'.esc_attr($tablet_slider_draggable).'"';
				$tablet_slider_infinite= ($settings["tablet_slider_infinite"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_infinite="'.esc_attr($tablet_slider_infinite).'"';
				$tablet_slider_autoplay= ($settings["tablet_slider_autoplay"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_autoplay="'.esc_attr($tablet_slider_autoplay).'"';
				$data_slider .=' data-tablet_autoplay_speed="'.esc_attr($settings["tablet_autoplay_speed"]["size"]).'"';
				$tablet_slider_dots= ($settings["tablet_slider_dots"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_dots="'.esc_attr($tablet_slider_dots).'"';
				$tablet_slider_arrows= ($settings["tablet_slider_arrows"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_slider_arrows="'.esc_attr($tablet_slider_arrows).'"';
				$data_slider .=' data-tablet_slider_rows="'.esc_attr($settings["tablet_slider_rows"]).'"';
				$tablet_center_mode= ($settings["tablet_center_mode"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-tablet_center_mode="'.esc_attr($tablet_center_mode).'" ';
				$data_slider .=' data-tablet_center_padding="'.esc_attr(!empty($settings["tablet_center_padding"]["size"]) ? $settings["tablet_center_padding"]["size"] : 0).'" ';
			}
			
			//mobile 
			$data_slider .=' data-slider_mobile_column="'.esc_attr($settings['slider_mobile_column']).'"';
			$data_slider .=' data-mobile_steps_slide="'.esc_attr($settings['mobile_steps_slide']).'"';
			$slider_responsive_mobile=$settings['slider_responsive_mobile'];			
			$data_slider .=' data-slider_responsive_mobile="'.esc_attr($slider_responsive_mobile).'"';
			if(!empty($settings['slider_responsive_mobile']) && $settings['slider_responsive_mobile']=='yes'){
				$mobile_slider_draggable= ($settings["mobile_slider_draggable"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_draggable="'.esc_attr($mobile_slider_draggable).'"';
				$mobile_slider_infinite= ($settings["mobile_slider_infinite"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_infinite="'.esc_attr($mobile_slider_infinite).'"';
				$mobile_slider_autoplay= ($settings["mobile_slider_autoplay"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_autoplay="'.esc_attr($mobile_slider_autoplay).'"';
				$data_slider .=' data-mobile_autoplay_speed="'.esc_attr($settings["mobile_autoplay_speed"]["size"]).'"';
				$mobile_slider_dots= ($settings["mobile_slider_dots"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_dots="'.esc_attr($mobile_slider_dots).'"';
				$mobile_slider_arrows= ($settings["mobile_slider_arrows"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_slider_arrows="'.esc_attr($mobile_slider_arrows).'"';
				$data_slider .=' data-mobile_slider_rows="'.esc_attr($settings["mobile_slider_rows"]).'"';
				$mobile_center_mode= ($settings["mobile_center_mode"]=='yes') ? 'true' : 'false';
				$data_slider .=' data-mobile_center_mode="'.esc_attr($mobile_center_mode).'" ';
				$data_slider .=' data-mobile_center_padding="'.esc_attr($settings["mobile_center_padding"]["size"]).'" ';
			}
			
			$slider_dots= ($settings["slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_dots="'.esc_attr($slider_dots).'"';
			$data_slider .=' data-slider_dots_style="slick-dots '.esc_attr($settings["slider_dots_style"]).'"';
			
			
			$slider_arrows= ($settings["slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_arrows="'.esc_attr($slider_arrows).'"';
			$data_slider .=' data-slider_arrows_style="'.esc_attr($settings["slider_arrows_style"]).'" ';
			$data_slider .=' data-arrows_position="'.esc_attr($settings["arrows_position"]).'" ';
			$data_slider .=' data-arrow_bg_color="'.esc_attr($settings["arrow_bg_color"]).'" ';
			$data_slider .=' data-arrow_icon_color="'.esc_attr($settings["arrow_icon_color"]).'" ';
			$data_slider .=' data-arrow_hover_bg_color="'.esc_attr($settings["arrow_hover_bg_color"]).'" ';
			$data_slider .=' data-arrow_hover_icon_color="'.esc_attr($settings["arrow_hover_icon_color"]).'" ';
			
			$slider_center_mode= ($settings["slider_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-slider_center_mode="'.esc_attr($slider_center_mode).'" ';
			$data_slider .=' data-center_padding="'.esc_attr($settings["center_padding"]["size"]).'" ';
			$data_slider .=' data-scale_center_slide="'.esc_attr($settings["scale_center_slide"]["size"]).'" ';
			$data_slider .=' data-scale_normal_slide="'.esc_attr($settings["scale_normal_slide"]["size"]).'" ';
			$data_slider .=' data-opacity_normal_slide="'.esc_attr($settings["opacity_normal_slide"]["size"]).'" ';
			
			$data_slider .=' data-slider_rows="'.esc_attr($settings["slider_rows"]).'" ';
			
			$isotope = 'list-carousel-slick';
			
			
			
			if($settings["slider_arrows_style"]=='style-3' || $settings["slider_arrows_style"]=='style-4'){
				$arrow_class=$settings["arrows_position"];
			}
			if(($settings["slider_rows"] > 1) || ($settings["tablet_slider_rows"] > 1) || ($settings["mobile_slider_rows"] > 1)){
				$arrow_class .= ' multi-row';
			}
			if(!empty($settings["hover_show_dots"]) && $settings["hover_show_dots"]=='yes'){
				$data_carousel .=' hover-slider-dots';
			}
			if(!empty($settings["hover_show_arrow"]) && $settings["hover_show_arrow"]=='yes'){
				$data_carousel .=' hover-slider-arrow';
			}
			if(!empty($settings["outer_section_arrow"]) && $settings["outer_section_arrow"]=='yes' && ($settings["slider_arrows_style"]=='style-1' || $settings["slider_arrows_style"]=='style-2' || $settings["slider_arrows_style"]=='style-5' || $settings["slider_arrows_style"]=='style-6')){
				$data_carousel .=' outer-slider-arrow';
			}
			
		}
	$list_img='';
		if(!empty($settings['theplus_instafeed_img_icn']) && $settings['theplus_instafeed_img_icn'] == 'show-instaimage'){
			$image_alt='';
			if(!empty($settings["loop_select_image"]["url"])){
				$loop_imgSrc= $settings["loop_select_image"]["url"];
				$image_id=$settings["loop_select_image"]["id"];
				$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
				if(!$image_alt){
					$image_alt = get_the_title($image_id);
				}else if(!$image_alt){
					$image_alt = 'Plus service icon';
				}
			}else{
				$loop_imgSrc='';
			}
				$list_img ='<img src='.esc_url($loop_imgSrc).' />';
		}
	
			$popupimage_attr= $list_img;
	
	$uid=uniqid('insta');
	if($settings['loop_select_image']['url']!=''){
		$icn_image=$settings['loop_select_image']['url'];
	}else{
		$icn_image = THEPLUS_ASSETS_URL .'images/insta_img_icon.png';			
	}
	
	$insta_masonry='';
	if(!empty($settings["theplus_instafeed_masonry"]) && $settings["theplus_instafeed_masonry"]=='yes'){
		$insta_masonry = 'insta-masonry-layout';
	}
	
	$insta_feed_by='';
	if(!empty($settings['instafeed_source']) && $settings['instafeed_source']=='username'){
		$insta_feed_by .= 'data-instafeed_by="username"';
		$insta_feed_by .= 'data-username="'.esc_attr($settings['instafeed_username']).'"';		
	}else{
		$insta_feed_by .= ' data-instafeed_by="access_token"';
		$insta_feed_by .= ' data-user-id="'.esc_attr($settings['theplus_instafeed_user_id']).'"';
		$insta_feed_by .= ' data-client-id="'.esc_attr($settings['theplus_instafeed_client_id']).'"';
		$insta_feed_by .= ' data-access-token="'.esc_attr($settings['theplus_instafeed_access_token']).'"';
		$insta_feed_by .= ' data-resolution="'.esc_attr($settings['theplus_instafeed_image_resolution']).'"';
		$insta_feed_by .= ' data-sort-by="'.esc_attr($settings['theplus_instafeed_sort_by']).'"';
	}
	
	$insta_feed_by .= ' data-limit="'.$image_limit.'"';
	$insta_feed_by .= ' data-target="theplus-instagram-feed-'.esc_attr($this->get_id()).'"';
	$insta_feed_by .= ' data-link="'.esc_attr($settings['theplus_instafeed_link'] ).'"';
	$insta_feed_by .= ' data-link-target="'.esc_attr($settings['theplus_instafeed_link_target'] ).'"';
	$insta_feed_by .= ' data-caption="'.esc_attr($settings['theplus_instafeed_caption'] ).'"';
	$insta_feed_by .= ' data-caption-count="'.esc_attr($settings['caption_count'] ).'"';
	$insta_feed_by .= ' data-instaimage="'.esc_attr($settings['theplus_instafeed_img_icn'] ).'"';
	$insta_feed_by .= ' data-likes="'.esc_attr($settings['theplus_instafeed_likes'] ).'"';
	$insta_feed_by .= ' data-comments="'.esc_attr($settings['theplus_instafeed_comments'] ).'"';
	
	$insta_feed_by .= ' data-carousels="'.esc_attr($settings['theplus_instafeed_carousels'] ).'"';
	$insta_feed_by .= ' data-layoutstyle="'.esc_attr($settings['insta_layout_style'] ).'"';
	$insta_feed_by .= ' data-column="'.$data_attr.'"';
	$insta_feed_by .= ' data-popup-image="'.$icn_image.'"';
	$insta_feed_by .= ' data-loadmore-text="'.$settings['tp_if_p_text'].'"';
	$insta_feed_by .= ' data-loading-text="'.$settings['tp_if_p_loading_text'].'"';
	
	?>	
	
	<div class="theplus-instagram-feed <?php echo $insta_layout_style.' '.$classes; ?>">
		<?php if(((!empty($settings['theplus_instafeed_access_token']) || !empty($settings['theplus_instafeed_client_id'])) && !empty($settings['theplus_instafeed_user_id']) && $settings['instafeed_source']=='access_token') || ($settings['instafeed_source']=='username' && !empty($settings['instafeed_username']))){ ?>
		
			<div  class="theplus-insta-grid <?Php echo $isotope. ' '.$insta_masonry.' ' .$arrow_class. ' ' .$data_carousel. ' ' .$uid. ' ' .$animated_class; ?>" data-id="<?Php echo $uid; ?>" <?php echo $insta_feed_by; ?> <?Php echo $data_slider; ?> <?php echo $animation_attr; ?> data-strager="<?php echo $animated_columns; ?>" >
				<div class="post-inner-loop tp-row" id="theplus-instagram-feed-<?php echo esc_attr($this->get_id()); ?>"></div>
				<div class="plus-insta-loading"><img src="<?php echo THEPLUS_ASSETS_URL.'images/ajax-loader.gif'; ?>" alt="<?php echo esc_attr__('Loading','theplus'); ?>" /></div>
			</div>
			
			<?php if ( $settings['theplus_instafeed_pagination'] == 'yes' && $settings['theplus_instafeed_masonry']!='yes' && $settings["theplus_instafeed_carousels"]!='yes') { ?>
				<div class="theplus-load-more-button-wrap">
					<button class="theplus-load-more-button" id="theplus-load-more-btn-<?php echo $this->get_id(); ?>">
						<div class="theplus-btn-loader"></div>
						<span><?php echo $settings['tp_if_p_text'];?></span>
					</button>
				</div>
			<?php } ?>
			
		<?php }else{
			echo '<div class="plus-insta-private"><strong>'.esc_html__("Please Enter (Access Token Or Client ID) AND User ID","theplus").'</strong></div>';
		} ?>
	</div>
	<?php
		$css_rule =$css_messy='';
		if($settings['messy_column']=='yes'){
			if($settings['theplus_instafeed_carousels']!='yes'){
			$layout='grid';
				$desktop_column=$settings['desktop_column'];
				$tablet_column=$settings['tablet_column'];
				$mobile_column=$settings['mobile_column'];
			}else if($settings['theplus_instafeed_carousels']=='yes'){
				$layout='carousel';
				$desktop_column=$settings['slider_desktop_column'];
				$tablet_column=$settings['slider_tablet_column'];
				$mobile_column=$settings['slider_mobile_column'];
			}
			for($x = 1; $x <= 6; $x++){
				if(!empty($settings["desktop_column_".$x])){
					$desktop=!empty($settings["desktop_column_".$x]["size"]) ? $settings["desktop_column_".$x]["size"].$settings["desktop_column_".$x]["unit"] : '';
					$tablet=!empty($settings["desktop_column_".$x."_tablet"]["size"]) ? $settings["desktop_column_".$x."_tablet"]["size"].$settings["desktop_column_".$x."_tablet"]["unit"] : '';
					$mobile=!empty($settings["desktop_column_".$x."_mobile"]["size"]) ? $settings["desktop_column_".$x."_mobile"]["size"].$settings["desktop_column_".$x."_mobile"]["unit"] : '';
					$css_messy .= theplus_messy_columns($x,$layout,$uid,$desktop,$tablet,$mobile,$desktop_column,$tablet_column,$mobile_column);
				}
			}
			$css_rule ='<style>'.$css_messy.'</style>';
		}
		echo $css_rule;
	}

	protected function content_template() {

	}
}