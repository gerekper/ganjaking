<?php

/**
 * Class: Premium_Trustpilot_Reviews
 * Name: Trustpilot Reviews
 * Slug: premium-trustpilot-reviews
 */

namespace PremiumAddonsPro\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Trustpilot_Reviews
 */
class Premium_Trustpilot_Reviews extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-trustpilot-reviews';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Trustpilot Reviews', 'premium-addons-pro' ) );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-trust-reviews';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'font-awesome-5-all',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-slick',
			'isotope-js',
			'premium-pro',
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'business', 'rating', 'testimonials', 'place', 'rate', 'recommendation', 'social' );
	}

	/**
	 * Register Premium Trustpilot Reviews controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'general',
			array(
				'label' => __( 'Business Info', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'business_name',
			array(
				'label'       => __( 'Business Name', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'leap13.com',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'description' => 'Click <a href="#" target="_blank">here</a> to get Business Name',
			)
		);

		$this->add_control(
			'clear_cache',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="clearReviewsCache(this);" action="javascript:void(0);" data-target="business_name"><input type="submit" value="Clear Cached Data" class="elementor-button" style="background-color: #3b5998; color: #fff;"></form>',
				'label_block' => true,
				'condition'   => array(
					'business_name!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'skin_type',
			array(
				'label'        => __( 'Skin', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'default',
				'options'      => array(
					'default' => __( 'Classic', 'premium-addons-pro' ),
					'card'    => __( 'Cards', 'premium-addons-pro' ),
					'bubble'  => __( 'Bubble', 'premium-addons-pro' ),
				),
				'render_type'  => 'template',
				'prefix_class' => 'premium-social-reviews-',
			)
		);

		$this->add_control(
			'source_icon',
			array(
				'label' => __( 'Trustpilot Icon', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$left_direction = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'iconh_position',
			array(
				'label'       => __( 'Horizontal Position (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => '%',
				'default'     => array(
					'size' => 0,
					'unit' => '%',
				),
				'min'         => 0,
				'max'         => 100,
				'condition'   => array(
					'source_icon' => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-fb-rev-icon' => $left_direction . ': {{SIZE}}{{UNIT}}; --translate-x: -{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'iconv_position',
			array(
				'label'       => __( 'Vertical Position (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units'  => '%',
				'default'     => array(
					'size' => 0,
					'unit' => '%',
				),
				'condition'   => array(
					'source_icon' => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-fb-rev-icon' => 'top: {{SIZE}}{{UNIT}}; --translate-y: -{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'display_tabs' );

		$this->start_controls_tab(
			'business_tab',
			array(
				'label'     => __( 'Business', 'premium-addons-pro' ),
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'place_reviews_position',
			array(
				'label'        => __( 'Place Info Next to Reviews', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-fb-page-next-',
				'condition'    => array(
					'business_info' => 'yes',
				),
				'render_type'  => 'template',
			)
		);

		$this->add_responsive_control(
			'reviews_width',
			array(
				'label'       => __( 'Reviews Width (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'condition'   => array(
					'business_info'          => 'yes',
					'place_reviews_position' => 'yes',
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-fb-rev-content, {{WRAPPER}} .slick-dots' => 'width: {{SIZE}}%',
				),
			)
		);

		$this->add_responsive_control(
			'business_info_valign',
			array(
				'label'        => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'prefix_class' => 'premium-reviews-place-v-',
				'separator'    => 'after',
				'default'      => 'top',
				'toggle'       => false,
				'condition'    => array(
					'business_info'          => 'yes',
					'place_reviews_position' => 'yes',
				),
			)
		);

		$this->add_control(
			'business_custom_image_switch',
			array(
				'label'     => __( 'Replace Business Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'business_info' => 'yes',
					'source_image'  => 'true',
				),
			)
		);

		$this->add_control(
			'business_custom_image',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'business_info'                => 'yes',
					'business_custom_image_switch' => 'yes',
					'source_image'                 => 'true',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'condition' => array(
					'business_info'                => 'yes',
					'business_custom_image_switch' => 'yes',
					'source_image'                 => 'true',
				),
			)
		);

		$this->add_control(
			'business_display',
			array(
				'label'       => __( 'Display', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'inline' => __( 'Inline', 'premium-addons-pro' ),
					'block'  => __( 'Block', 'premium-addons-pro' ),
				),
				'default'     => 'inline',
				'render_type' => 'ui',
				'condition'   => array(
					'business_info' => 'yes',
					'source_image'  => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'business_image_align',
			array(
				'label'     => __( 'Image Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-left' => 'align-self: {{VALUE}};',
				),
				'condition' => array(
					'business_display' => 'inline',
					'source_image'     => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'business_text_halign',
			array(
				'label'     => __( 'Text Horizontal Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-right' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'business_display' => 'inline',
					'source_image'     => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'business_text_align',
			array(
				'label'     => __( 'Text Vertical Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-right' => 'align-self: {{VALUE}};',
				),
				'condition' => array(
					'business_display' => 'inline',
					'source_image'     => 'true',
				),
			)
		);

		$this->add_control(
			'business_dir',
			array(
				'label'              => __( 'Direction', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'rtl' => 'RTL',
					'ltr' => 'LTR',
				),
				'default'            => 'ltr',
				'prefix_class'       => 'premium-reviews-src-',
				'frontend_available' => true,
				'condition'          => array(
					'business_display' => 'inline',
					'source_image'     => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'business_align',
			array(
				'label'     => __( 'Business Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .premium-fb-rev-page' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'reviews_tab',
			array(
				'label' => __( 'Reviews', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'reviews_columns',
			array(
				'label'          => __( 'Reviews/Row', 'premium-addons-pro' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'100%'   => __( '1 Column', 'premium-addons-pro' ),
					'50%'    => __( '2 Columns', 'premium-addons-pro' ),
					'33.33%' => __( '3 Columns', 'premium-addons-pro' ),
					'25%'    => __( '4 Columns', 'premium-addons-pro' ),
					'20%'    => __( '5 Columns', 'premium-addons-pro' ),
				),
				'default'        => '33.33%',
				'tablet_default' => '100%',
				'mobile_default' => '100%',
				'render_type'    => 'template',
				'selectors'      => array(
					'{{WRAPPER}} .premium-fb-rev-review-wrap' => 'width: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'reviews_display',
			array(
				'label'        => __( 'Image Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'block'  => __( 'Above Name', 'premium-addons-pro' ),
					'left'   => __( 'Left of Name', 'premium-addons-pro' ),
					'inline' => __( 'Left of all content', 'premium-addons-pro' ),

				),
				'default'      => 'block',
				'render_type'  => 'template',
				'condition'    => array(
					'reviewer_image' => 'yes',
					'skin_type!'     => 'bubble',
				),
				'prefix_class' => 'premium-reviewer-image-pos-',
			)
		);

		$this->add_responsive_control(
			'reviews_image_align',
			array(
				'label'     => __( 'Image Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-review-inner .premium-fb-rev-content-left' => 'align-self: {{VALUE}};',
				),
				'condition' => array(
					'reviews_display' => 'inline',
					'reviewer_image'  => 'yes',
					'skin_type'       => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'reviews_text_align',
			array(
				'label'     => __( 'Text Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-long-arrow-down',
					),
				),
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-reviewer-header' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'reviews_display' => 'left',
					'skin_type!'      => 'bubble',
				),
			)
		);

		$this->add_control(
			'reviews_style',
			array(
				'label'     => __( 'Layout', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'even'    => __( 'Even', 'premium-addons-pro' ),
					'masonry' => __( 'Masonry', 'premium-addons-pro' ),
				),
				'default'   => 'even',
				'condition' => array(
					'reviews_columns!' => '100%',
				),
			)
		);

		$this->add_control(
			'reviews_dir',
			array(
				'label'              => __( 'Direction', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'rtl' => 'RTL',
					'ltr' => 'LTR',
				),
				'default'            => 'ltr',
				'prefix_class'       => 'premium-reviews-',
				'frontend_available' => true,
				'condition'          => array(
					'reviews_display!' => 'block',
					'skin_type'        => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'     => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'default'   => 'center',
				'condition' => array(
					'reviews_display' => 'block',
					'skin_type!'      => 'bubble',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .premium-fb-rev-content' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bubble_arrow',
			array(
				'label'        => __( 'Bubble Arrow', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'true',
				'return_value' => 'true',
				'condition'    => array(
					'skin_type' => 'bubble',
				),
			)
		);

		$this->add_control(
			'reviews_carousel',
			array(
				'label' => __( 'Carousel', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'infinite_autoplay',
			array(
				'label'     => __( 'Infinite Autoplay', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'reviews_carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'rows',
			array(
				'label'     => __( 'Rows/Column', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'min'       => 1,
				'max'       => 3,
				'condition' => array(
					'reviews_carousel'  => 'yes',
					'infinite_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_play',
			array(
				'label'     => __( 'Autoplay', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'reviews_carousel'   => 'yes',
					'infinite_autoplay!' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_autoplay_speed',
			array(
				'label'      => __( 'Autoplay Speed', 'premium-addons-pro' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 5000,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'reviews_carousel',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'carousel_play',
									'value' => 'yes',
								),
								array(
									'name'  => 'infinite_autoplay',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'carousel_navigation',
			array(
				'label'     => __( 'Navigation', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'none'   => __( 'None', 'premium-addons-pro' ),
					'arrows' => __( 'Arrows', 'premium-addons-pro' ),
					'dots'   => __( 'Dots', 'premium-addons-pro' ),
					'all'    => __( 'Dots & Arrows', 'premium-addons-pro' ),
				),
				'default'   => 'arrows',
				'condition' => array(
					'reviews_carousel'   => 'yes',
					'infinite_autoplay!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_arrows_pos',
			array(
				'label'      => __( 'Arrows Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'condition'  => array(
					'reviews_carousel'    => 'yes',
					'carousel_navigation' => array( 'arrows', 'all' ),
					'infinite_autoplay!'  => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-reviews a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-fb-rev-reviews a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_dots_pos',
			array(
				'label'      => __( 'Dots Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'reviews_carousel'    => 'yes',
					'carousel_navigation' => array( 'dots', 'all' ),
					'infinite_autoplay!'  => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-reviews ul.slick-dots,{{WRAPPER}} .premium-fb-dots-container ul.slick-dots' => 'bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'carousel_rtl',
			array(
				'label'       => __( 'RTL Mode', 'premium-addons-pro' ),
				'description' => __( 'Recommended for RTL Sites', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'reviews_carousel' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'adv',
			array(
				'label' => __( 'Business Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'business_info',
			array(
				'label'   => __( 'Business Info', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'source_image',
			array(
				'label'        => __( 'Business Image', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'true',
				'return_value' => 'true',
				'condition'    => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'source_name',
			array(
				'label'        => __( 'Business Name', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'business_rate_label',
			array(
				'label'     => __( 'Rate Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'business_rate',
			array(
				'label'     => __( 'Business Rate', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_number',
			array(
				'label'     => __( 'Number of Reviews', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'source_stars',
			array(
				'label'        => __( 'Rating Stars', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'true',
				'return_value' => 'true',
				'condition'    => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'     => __( 'Show Trustpilot Badge', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_theme',
			array(
				'label'     => __( 'Badge Theme', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'black' => __( 'Dark', 'premium-addons-pro' ),
					'white' => __( 'Light', 'premium-addons-pro' ),
				),
				'default'   => 'black',
				'condition' => array(
					'business_info' => 'yes',
					'badge'         => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_size',
			array(
				'label'      => __( 'Badge Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-trustpilot-badge img' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'business_info' => 'yes',
					'badge'         => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'review_settings',
			array(
				'label' => __( 'Reviews Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'reviewer_image',
			array(
				'label'        => __( 'Show Reviewer Image', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'render_type'  => 'template',
				'return_value' => 'yes',
				'prefix_class' => 'premium-reviewer-image-',

			)
		);

		$this->add_control(
			'stars',
			array(
				'label'   => __( 'Show Stars', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'stars_pos_bubble',
			array(
				'label'        => __( 'Stars Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'above' => __( 'Above Name', 'premium-addons-pro' ),
					'below' => __( 'Below Name', 'premium-addons-pro' ),
				),
				'default'      => 'below',
				'condition'    => array(
					'stars'     => 'yes',
					'skin_type' => 'bubble',
				),
				'prefix_class' => 'premium-review-stars-',
			)
		);

		$this->add_control(
			'date',
			array(
				'label'   => __( 'Show Date', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'date_position',
			array(
				'label'     => __( 'Date Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'column'         => __( 'Above Stars', 'premium-addons-pro' ),
					'column-reverse' => __( 'Below Stars', 'premium-addons-pro' ),
				),
				'default'   => 'column-reverse',
				'condition' => array(
					'stars' => 'yes',
					'date'  => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-info' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'     => __( 'Date Format', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'd/m/Y'  => 'DD/MM/YYYY',
					'm/d/Y'  => 'MM/DD/YYYY',
					'j F, Y' => __( 'Full Date', 'premium-addons-pro' ),
				),
				'default'   => 'j F, Y',
				'condition' => array(
					'date' => 'yes',
				),
			)
		);

		$this->add_control(
			'text',
			array(
				'label'     => __( 'Show Review Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label'     => __( 'Hide Reviews With Empty Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'text' => 'yes',
				),
			)
		);

		$this->add_control(
			'words_num',
			array(
				'label'     => __( 'Review Words Length', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 10,
				'condition' => array(
					'text' => 'yes',
				),
			)
		);

		$this->add_control(
			'readmore',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-for-elementor' ),
				'condition' => array(
					'text'       => 'yes',
					'words_num!' => '',
				),
			)
		);

		$this->add_control(
			'filter',
			array(
				'label'     => __( 'Filter by Rate', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'filter_min',
			array(
				'label'     => __( 'Min Stars', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 5,
				'condition' => array(
					'filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_max',
			array(
				'label'     => __( 'Max Stars', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 5,
				'condition' => array(
					'filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label' => __( 'Reviews Limit', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'limit_num',
			array(
				'label'       => __( 'Number of Reviews', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 10,
				'description' => __( 'You can only pull 10 reviews from Trustpilot', 'premium-addons-pro' ),
				'condition'   => array(
					'limit' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema',
			array(
				'label' => __( 'Rating Schema', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'schema_type',
			array(
				'label'     => __( 'Schema Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'Place'        => __( 'Place', 'premium-addons-pro' ),
					'Organization' => __( 'Organization', 'premium-addons-pro' ),
					'Service'      => __( 'Service', 'premium-addons-pro' ),
				),
				'default'   => 'Place',
				'condition' => array(
					'schema' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_doc',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Enabling schema improves SEO as it helps to list star ratings in search engine results.', 'premium-addons-pro' ),
				'content_classes' => 'editor-pa-doc',
				'condition'       => array(
					'schema' => 'yes',
				),
			)
		);

		$this->add_control(
			'reload',
			array(
				'label'   => __( 'Reload Reviews Once Every', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'hour'  => __( 'Hour', 'premium-addons-pro' ),
					'day'   => __( 'Day', 'premium-addons-pro' ),
					'week'  => __( 'Week', 'premium-addons-pro' ),
					'month' => __( 'Month', 'premium-addons-pro' ),
					'year'  => __( 'Year', 'premium-addons-pro' ),
				),
				'default' => 'day',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-trustpilot-reviews-widget-tutorial/' => __( 'Getting started »', 'premium-addons-pro' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

		$this->start_controls_section(
			'images',
			array(
				'label' => __( 'Images', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'images_tabs' );

		$this->start_controls_tab(
			'business_img_tab',
			array(
				'label'     => __( 'Business', 'premium-addons-pro' ),
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'business_image_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 100,
				),
				'condition'  => array(
					'business_custom_image_switch!' => 'yes',
					'source_image'                  => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'business_image_border',
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img',
				'condition' => array(
					'source_image' => 'true',
				),
			)
		);

		$this->add_control(
			'business_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'source_image' => 'true',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'business_image_shadow',
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img',
				'condition' => array(
					'source_image' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'business_image_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-left' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'source_image' => 'true',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'img_tab',
			array(
				'label'     => __( 'Review', 'premium-addons-pro' ),
				'condition' => array(
					'reviewer_image' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'image_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-review-inner .premium-fb-rev-img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-rev-arrow-bubble' => 'left: calc( {{SIZE}}{{UNIT}} - 24px );',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'reviewer_image_border',
				'selector' => '{{WRAPPER}} .premium-fb-rev-review-inner .premium-fb-rev-img',
			)
		);

		$this->add_control(
			'reviewer_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-review-inner .premium-fb-rev-img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .premium-fb-rev-review-inner .premium-fb-rev-img',
			)
		);

		$this->add_responsive_control(
			'image_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-review-inner .premium-fb-rev-content-left' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'page',
			array(
				'label'     => __( 'Business Info', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'business_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'revs_number_label',
			array(
				'label'     => __( 'Reviews Number', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'reviews_number' => 'yes',
				),
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'condition' => array(
					'reviews_number' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-rating-count span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'number_typo',
				'condition' => array(
					'reviews_number' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-fb-rev-rating-count span',
			)
		);

		$this->start_controls_tabs( 'business_info_tabs' );

		$this->start_controls_tab(
			'page_container',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'page_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-fb-rev-page',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'page_container_border',
				'selector' => '{{WRAPPER}} .premium-fb-rev-page',
			)
		);

		$this->add_control(
			'page_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'page_container_shadow',
				'selector' => '{{WRAPPER}} .premium-fb-rev-page',
			)
		);

		$this->add_responsive_control(
			'page_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'page_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'page_link',
			array(
				'label'     => __( 'Name', 'premium-addons-pro' ),
				'condition' => array(
					'source_name' => 'true',
				),
			)
		);

		$this->add_control(
			'page_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'page_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'page_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-fb-rev-page-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'business_shadow',
				'selector' => '{{WRAPPER}} .premium-fb-rev-page-link',
			)
		);

		$this->add_responsive_control(
			'page_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-link-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'page_rate_link',
			array(
				'label' => __( 'Rate', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'rating_label',
			array(
				'label'     => __( 'Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'business_rate_label' => 'yes',
				),
			)
		);

		$this->add_control(
			'rate_label_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'condition' => array(
					'business_rate_label' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-rating-label span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'rate_label_type',
				'condition' => array(
					'business_rate_label' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-fb-rev-rating-label span',
			)
		);

		$this->add_control(
			'stars_label',
			array(
				'label'     => __( 'Stars', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'source_stars' => 'true',
				),
			)
		);

		$this->add_control(
			'default_business_star',
			array(
				'label'     => __( 'Default Trustpilot Stars', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'source_stars' => 'true',
				),
			)
		);

		$this->add_control(
			'business_star_img_size',
			array(
				'label'      => __( 'Stars Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-stars img' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'source_stars'          => 'true',
					'default_business_star' => 'yes',
				),
			)
		);

		$this->add_control(
			'business_star_size',
			array(
				'label'     => __( 'Star Size', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 50,
				'default'   => 20,
				'condition' => array(
					'source_stars'           => 'true',
					'default_business_star!' => 'yes',
				),
			)
		);

		$this->add_control(
			'business_fill',
			array(
				'label'     => __( 'Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffab40',
				'global'    => false,
				'condition' => array(
					'source_stars'           => 'true',
					'default_business_star!' => 'yes',
				),
			)
		);

		$this->add_control(
			'business_empty',
			array(
				'label'     => __( 'Empty Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'source_stars'           => 'true',
					'default_business_star!' => 'yes',
				),
			)
		);

		$this->add_control(
			'number_label',
			array(
				'label'     => __( 'Number', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'business_rate' => 'true',
				),
			)
		);

		$this->add_control(
			'page_rate_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'condition' => array(
					'business_rate' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-rating' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'page_rate_typo',
				'condition' => array(
					'business_rate' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-rating',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'business_rate_shadow',
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-rating',
				'condition' => array(
					'business_rate' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'page_rate_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-rating-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'review_container',
			array(
				'label' => __( 'Review', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'default_reviews_star',
			array(
				'label'   => __( 'Default Trustpilot Stars', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'reviews_star_img_size',
			array(
				'label'      => __( 'Stars Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-stars-container img' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'stars'                => 'yes',
					'default_reviews_star' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_star_size',
			array(
				'label'     => __( 'Star Size', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 50,
				'default'   => 15,
				'condition' => array(
					'stars'                 => 'yes',
					'default_reviews_star!' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_fill',
			array(
				'label'     => __( 'Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffab40',
				'global'    => false,
				'condition' => array(
					'stars'                 => 'yes',
					'default_reviews_star!' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_empty',
			array(
				'label'     => __( 'Empty Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'stars'                 => 'yes',
					'default_reviews_star!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'review_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-fb-rev-review',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'review_container_border',
				'selector' => '{{WRAPPER}} .premium-fb-rev-review',
			)
		);

		$this->add_control(
			'review_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-review' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'review_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-fb-rev-review',
			)
		);

		$this->add_responsive_control(
			'reviews_gap',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 10,
					'bottom' => 30,
					'left'   => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-review-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-fb-rev-reviews a.carousel-arrow , {{WRAPPER}}.premium-fb-page-next-yes.premium-reviews-place-v-center .premium-fb-rev-page-inner ' => 'transform: translateY(calc(-50% + {{TOP}}{{UNIT}}/2 - {{BOTTOM}}{{UNIT}}/2 )) !important',
					'{{WRAPPER}}.premium-fb-page-next-yes.premium-reviews-place-v-bottom .premium-fb-rev-page-inner' => 'transform: translateY(calc(-100% - {{BOTTOM}}{{UNIT}} )) !important',
					'{{WRAPPER}}.premium-fb-page-next-yes.premium-reviews-place-v-top .premium-fb-rev-page-inner' => 'transform: translateY({{TOP}}{{UNIT}}) !important',
				),
			)
		);

		$this->add_responsive_control(
			'review_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'reviewer',
			array(
				'label' => __( 'Reviewer Name', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'reviewer_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-reviewer-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reviewer_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-reviewer-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reviewer_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-fb-rev-reviewer-link',
			)
		);

		$this->add_responsive_control(
			'reviewer_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-reviewer-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'date_style',
			array(
				'label'     => __( 'Date', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'date' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-time .premium-fb-rev-time-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reviewer_date_color_hover',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-time .premium-fb-rev-time-text:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typo',
				'selector' => '{{WRAPPER}} .premium-fb-rev-time .premium-fb-rev-time-text',
			)
		);

		$this->add_responsive_control(
			'date_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'bubble_style',
			array(
				'label'     => __( 'Review Container', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'text'      => 'yes',
					'skin_type' => 'bubble',
				),
			)
		);

		$this->add_control(
			'bubble_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-rating' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .premium-rev-arrow'     => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bubble_border_type',
			array(
				'label'       => __( 'Border Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'solid',
				'label_block' => false,
				'options'     => array(
					'none'   => __( 'None', 'premium-addons-pro' ),
					'solid'  => __( 'Solid', 'premium-addons-pro' ),
					'double' => __( 'Double', 'premium-addons-pro' ),
					'dotted' => __( 'Dotted', 'premium-addons-pro' ),
					'dashed' => __( 'Dashed', 'premium-addons-pro' ),
					'groove' => __( 'Groove', 'premium-addons-pro' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-fb-rev-rating' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bubble_border_width',
			array(
				'label'     => __( 'Border Width', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'condition' => array(
					'bubble_border_type!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-rating' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-rev-arrow'     => 'top: -{{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'bubble_border_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'bubble_border_type!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-rating' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .premium-rev-arrow-bubble-border' => 'border-top-color: {{VALUE}};',
				),
				'selector'  => '{{WRAPPER}} .premium-fb-rev-rating',
			)
		);

		$this->add_control(
			'bubble_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-rating' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'bubble_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bubble_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-text-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'reviewer_txt',
			array(
				'label'     => __( 'Review Text', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'text' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviewer_txt_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reviewer_txt_typo',
				'selector' => '{{WRAPPER}} .premium-fb-rev-text',
			)
		);

		$this->add_responsive_control(
			'reviewer_txt_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 10,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-text-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'readmore_style',
			array(
				'label'     => __( 'Readmore Text', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'text'       => 'yes',
					'words_num!' => '',
				),
			)
		);

		$this->add_control(
			'readmore_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-readmore' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'readmore_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-readmore:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'readmore_typo',
				'selector' => '{{WRAPPER}} .premium-fb-rev-readmore',
			)
		);

		$this->add_responsive_control(
			'readmore_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'container',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'container_width',
			array(
				'label'      => __( 'Max Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-widget-container' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-fb-rev-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .premium-fb-rev-container',
			)
		);

		$this->add_control(
			'container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-fb-rev-container',
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_arrows_style',
			array(
				'label'     => __( 'Carousel Arrows', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'reviews_carousel'    => 'yes',
					'carousel_navigation' => array( 'arrows', 'all' ),
					'infinite_autoplay!'  => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_dots_style',
			array(
				'label'     => __( 'Carousel Dots', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'reviews_carousel'    => 'yes',
					'carousel_navigation' => array( 'dots', 'all' ),
					'infinite_autoplay!'  => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_dot_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'carousel_dot_active_color',
			array(
				'label'     => __( 'Active Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li.slick-active' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Trustpilot Reviews widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$business_name = $settings['business_name'];

		$id = $this->get_id();

		$transient = $settings['reload'];

		if ( empty( $business_name ) ) { ?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please Enter a Valid Business Name', 'premium-addons-pro' ) ); ?>
			</div>
			<?php
			return;
		}

		$reviews_transient = sprintf( 'papro_reviews_%s_%s', $business_name, $id );

		do_action( 'papro_reviews_transient', $reviews_transient, $settings );

		$response = get_transient( $reviews_transient );

		if ( false === $response ) {

			sleep( 2 );

			$response = premium_trustpilot_data( $business_name );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				?>
				<div class="premium-error-notice">
					<?php /* translators: %s: Error term */ ?>
					<?php echo wp_kses_post( sprintf( esc_html( __( 'Something went wrong: %s', 'premium-addons-pro' ) ), $error_message ) ); ?>
				</div>
				<?php
				return;
			}

			$expire_time = Helper_Functions::transient_expire( $transient );

			set_transient( $reviews_transient, $response, $expire_time );

		}

		$response_data = $response['data'];

		$response_results = rplg_json_decode( $response_data );

		if ( ! isset( $response_results->reviews ) ) {
			?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Something went wrong. It seems like the busniess/company you selected does not have any reviews', 'premium-addons-pro' ) ); ?>
			</div>
			<?php
			delete_transient( $reviews_transient );
			return false;
		}

		$reviews = $response_results->reviews;

		$business = $response_results->business;

		if ( 'yes' === $settings['business_info'] && 'yes' === $settings['business_custom_image_switch'] ) {

			$image_src = $settings['business_custom_image'];

			$image_src_size = Group_Control_Image_Size::get_attachment_image_src( $image_src['id'], 'thumbnail', $settings );

			if ( empty( $image_src_size ) ) :
				$image_src_size = $image_src['url'];
else :
	$image_src_size = $image_src_size;
endif;

			$custom_image = ! empty( $image_src_size ) ? $image_src_size : '';

		} else {
			$custom_image = '';
		}

		$show_stars = 'yes' === $settings['stars'] ? true : false;

		$show_date = 'yes' === $settings['date'] ? true : false;

		$date_format = $settings['date_format'];

		$this->add_render_attribute( 'business', 'class', 'premium-fb-rev-page' );

		$this->add_render_attribute( 'reviews', 'class', 'premium-fb-rev-content' );

		$business_rate = ( 'yes' === $settings['business_info'] && 'yes' === $settings['business_rate'] ) ? true : false;

		$rev_text = 'yes' === $settings['text'] ? true : false;

		$rev_length = $settings['words_num'];

		$business_star_size   = ! empty( $settings['business_star_size'] ) ? $settings['business_star_size'] : 16;
		$business_fill_color  = ! empty( $settings['business_fill'] ) ? $settings['business_fill'] : '#ffab40';
		$business_empty_color = ! empty( $settings['business_empty'] ) ? $settings['business_empty'] : '#ccc';

		$rev_star_size   = ! empty( $settings['reviews_star_size'] ) ? $settings['reviews_star_size'] : 16;
		$rev_fill_color  = ! empty( $settings['reviews_fill'] ) ? $settings['reviews_fill'] : '#6ec1e4';
		$rev_empty_color = ! empty( $settings['reviews_empty'] ) ? $settings['reviews_empty'] : '#ccc';

		if ( 'yes' === $settings['limit'] ) {
            // phpcs:ignore WordPress.PHP.StrictComparisons
			if ( '0' == $settings['limit_num'] ) {
				$limit = 0;
			} else {
				$limit = ! empty( $settings['limit_num'] ) ? $settings['limit_num'] : 10;
			}
		} else {
			$limit = 3;
		}

		if ( 'yes' === $settings['filter'] ) {
			$min_filter = ! empty( $settings['filter_min'] ) ? $settings['filter_min'] : 1;
			$max_filter = ! empty( $settings['filter_max'] ) ? $settings['filter_max'] : 5;
		} else {
			$min_filter = 1;
			$max_filter = 5;
		}

		$rating = 0;
		if ( ! empty( $reviews ) ) {
			foreach ( $reviews as $review ) {
				$review_rating = isset( $review->rating ) ? $review->rating : 5;
				$rating        = $rating + $review_rating;
			}
			$rating = round( $rating / count( $reviews ), 1 );
			$rating = number_format( (float) $rating, 1, '.', '' );
		}

		$carousel = 'yes' === $settings['reviews_carousel'] ? true : false;

		$reviews_number        = intval( 100 / substr( $settings['reviews_columns'], 0, strpos( $settings['reviews_columns'], '%' ) ) );
		$reviews_number_tab    = intval( 100 / substr( $settings['reviews_columns_tablet'], 0, strpos( $settings['reviews_columns_tablet'], '%' ) ) );
		$reviews_number_mobile = intval( 100 / substr( $settings['reviews_columns_mobile'], 0, strpos( $settings['reviews_columns_mobile'], '%' ) ) );

		$business_settings = array(
			'image'         => $custom_image,
			'show_name'     => $settings['source_name'],
			'show_image'    => $settings['source_image'],
			'rating'        => $rating,
			'color'         => $business_fill_color,
			'empty_color'   => $business_empty_color,
			'stars'         => $settings['source_stars'],
			'stars_size'    => $business_star_size,
			'business_rate' => $business_rate,
			'rate_label'    => $settings['business_rate_label'],
			'rev_number'    => $settings['reviews_number'],
			'badge'         => $settings['badge'],
			'theme'         => $settings['badge_theme'],
			'id'            => $this->get_id(),
			'default_stars' => $settings['default_business_star'],
		);

		$reviews_settings = array(
			'fill_color'    => $rev_fill_color,
			'empty_color'   => $rev_empty_color,
			'stars'         => $show_stars,
			'stars_size'    => $rev_star_size,
			'filter_min'    => $min_filter,
			'filter_max'    => $max_filter,
			'date'          => $show_date,
			'format'        => $date_format,
			'limit'         => $limit,
			'text'          => $rev_text,
			'hide_empty'    => $settings['hide_empty'],
			'rev_length'    => $rev_length,
			'readmore'      => $settings['readmore'],
			'skin_type'     => $settings['skin_type'],
			'bubble_arrow'  => 'bubble' === $settings['skin_type'] ? $settings['bubble_arrow'] : false,
			'show_image'    => $settings['reviewer_image'],
			'show_icon'     => $settings['source_icon'],
			'image_display' => $settings['reviews_display'],
			'default_stars' => $settings['default_reviews_star'],
		);

		$container_settings = array(
			'class'           => array(
				'premium-fb-rev-container',
				'trustpilot-reviews',
				'premium-reviews-' . $settings['reviews_style'],
			),
			'data-col'        => $reviews_number,
			'data-col-tab'    => $reviews_number_tab,
			'data-col-mobile' => $reviews_number_mobile,
			'data-style'      => $settings['reviews_style'],
		);

		$this->add_render_attribute( 'container', $container_settings );

		if ( $carousel ) {

			$play   = 'yes' === $settings['carousel_play'] ? true : false;
			$speed  = ! empty( $settings['carousel_autoplay_speed'] ) ? $settings['carousel_autoplay_speed'] : 5000;
			$rtl    = 'yes' === $settings['carousel_rtl'] ? true : false;
			$dots   = ( 'dots' === $settings['carousel_navigation'] || 'all' === $settings['carousel_navigation'] ) ? 'true' : 'false';
			$arrows = ( 'arrows' === $settings['carousel_navigation'] || 'all' === $settings['carousel_navigation'] ) ? 'true' : 'false';

			$infinite = 'yes' === $settings['infinite_autoplay'];

			$rows = 'yes' === $settings['infinite_autoplay'] ? $settings['rows'] : 0;

			$container_settings = array(
				'data-carousel' => $carousel,
				'data-play'     => $play,
				'data-speed'    => $speed,
				'data-rtl'      => $rtl,
				'data-dots'     => $dots,
				'data-arrows'   => $arrows,
				'data-infinite' => $infinite,
				'data-rows'     => $rows,
			);

			$this->add_render_attribute( 'container', $container_settings );

		}

		if ( 'yes' === $settings['schema'] ) {

			$ratings = $business->reviews->count;

			$this->add_render_attribute(
				'container',
				array(
					'itemscope' => '',
					'itemtype'  => 'http://schema.org/' . $settings['schema_type'],
				)
			);
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div class="premium-fb-rev-list">
				<?php if ( 'yes' === $settings['business_info'] ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'business' ) ); ?>>
						<div class="premium-fb-rev-page-inner">
							<?php premium_trustpilot_reviews_business( $business, $business_settings ); ?>
						</div>
						<?php if ( '0' == $limit && 'yes' === $settings['source_icon'] ) : ?>
							<div class="premium-fb-rev-icon">
									<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#00b67a" d="M17.227 16.67l2.19 6.742-7.413-5.388 5.223-1.354zM24 9.31h-9.165L12.005.589l-2.84 8.723L0 9.3l7.422 5.397-2.84 8.714 7.422-5.388 4.583-3.326L24 9.311z"></path></svg>
								</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $reviews ) ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'reviews' ) ); ?>>

						<?php premium_trustpilot_rev_reviews( $reviews, $reviews_settings ); ?>

					</div>
				<?php endif; ?>
			</div>
			<?php if ( 'yes' === $settings['schema'] ) { ?>
				<div class="elementor-screen-only" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					<span itemprop="ratingValue" class="elementor-screen-only"><?php echo esc_attr( $rating ); ?></span><span itemprop="reviewCount" class="elementor-screen-only"><?php echo esc_attr( $ratings ); ?></span>
				</div>
			<?php } ?>
		</div>

		<?php
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {

			if ( 'masonry' === $settings['reviews_style'] && 'yes' !== $settings['reviews_carousel'] ) {
				$this->render_editor_script();
			}
		}
	}

	/**
	 * Render Editor Masonry Script.
	 *
	 * @since 1.9.4
	 * @access protected
	 */
	protected function render_editor_script() {

		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {

				$( '.premium-fb-rev-reviews' ).each( function() {

					var $node_id 	= '<?php echo esc_attr( $this->get_id() ); ?>',
						scope 		= $( '[data-id="' + $node_id + '"]' ),
						selector 	= $(this);
					if ( selector.closest( scope ).length < 1 ) {
						return;
					}
					var masonryArgs = {
						itemSelector	: '.premium-fb-rev-review-wrap',
						percentPosition : true,
						layoutMode		: 'masonry',
					};

					var $isotopeObj = {};

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope( masonryArgs );

						selector.find('.premium-fb-rev-review-wrap').resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});

				});
			});
		</script>
		<?php
	}

}
