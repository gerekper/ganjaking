<?php
/**
 * Class: Premium_Google_Reviews
 * Name: Google Reviews
 * Slug: premium-google-reviews
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
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
 * Class Premium_Google_Reviews
 */
class Premium_Google_Reviews extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-google-reviews';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Google Reviews', 'premium-addons-pro' );
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
		return 'pa-pro-google-reviews';
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
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'business', 'rating', 'testimonials', 'place', 'rate', 'recommendation', 'social' );
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
			'pa-slick',
			'premium-addons',
			'premium-pro',
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
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Google Reviews controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'general',
			array(
				'label' => __( 'Access Credentials', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'api_key',
			array(
				'label'       => __( 'API Key', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'description' => 'Click <a href="https://developers.google.com/places/web-service/get-api-key" target="_blank">here</a> to get your Google Places API key',
			)
		);

		$this->add_control(
			'place_id',
			array(
				'label'       => __( 'Place ID', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'ChIJ7abYXwhAxokRFGoJWSMHR7c',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Click <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank">here</a> to get your place ID',
			)
		);

		$this->add_control(
			'clear_cache',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="clearReviewsCache(this);" action="javascript:void(0);" data-target="place_id"><input type="submit" value="Clear Cached Data" class="elementor-button" style="background-color: #3b5998; color: #fff;"></form>',
				'label_block' => true,
				'condition'   => array(
					'api_key!' => '',
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
				'label' => __( 'Google Icon', 'premium-addons-pro' ),
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
					'{{WRAPPER}} .premium-fb-rev-icon' => $left_direction . ': {{SIZE}}%;--translate-x: -{{SIZE}}%',
				),
			)
		);

		$this->add_responsive_control(
			'iconv_position',
			array(
				'label'       => __( 'Vertical Position (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'default'     => array(
					'size' => 0,
					'unit' => '%',
				),
				'condition'   => array(
					'source_icon' => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-fb-rev-icon' => 'top: {{SIZE}}%; --translate-y: -{{SIZE}}%',
				),
			)
		);

		$this->start_controls_tabs( 'display_tabs' );

		$this->start_controls_tab(
			'place_tab',
			array(
				'label'     => __( 'Place', 'premium-addons-pro' ),
				'condition' => array(
					'place_info' => 'yes',
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
					'place_info' => 'yes',
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
					'place_info'             => 'yes',
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
						'icon'  => 'eicon-arrow-up',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'prefix_class' => 'premium-reviews-place-v-',
				'separator'    => 'after',
				'default'      => 'top',
				'toggle'       => false,
				'condition'    => array(
					'place_info'             => 'yes',
					'place_reviews_position' => 'yes',
				),
			)
		);

		$this->add_control(
			'place_custom_image_switch',
			array(
				'label'     => __( 'Replace Place Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
			)
		);

		$this->add_control(
			'place_custom_image',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'place_info'                => 'yes',
					'place_custom_image_switch' => 'yes',
					'source_image'              => 'true',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'condition' => array(
					'place_info'                => 'yes',
					'place_custom_image_switch' => 'yes',
					'source_image'              => 'true',
				),
			)
		);

		$this->add_control(
			'place_display',
			array(
				'label'       => __( 'Display', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'inline' => __( 'Inline', 'premium-addons-pro' ),
					'block'  => __( 'Block', 'premium-addons-pro' ),
				),
				'default'     => 'block',
				'render_type' => 'ui',
				'condition'   => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'place_image_align',
			array(
				'label'     => __( 'Image Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-left' => 'align-self: {{VALUE}};',
				),
				'condition' => array(
					'place_display' => 'inline',
					'source_image'  => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'place_text_halign',
			array(
				'label'     => __( 'Text Horizontal Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-right' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'place_display' => 'inline',
					'source_image'  => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'place_text_align',
			array(
				'label'     => __( 'Text Vertical Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-right' => 'align-self: {{VALUE}};',
				),
				'condition' => array(
					'place_display' => 'inline',
					'source_image'  => 'true',
				),
			)
		);

		$this->add_control(
			'place_dir',
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
					'place_display' => 'inline',
					'source_image'  => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'place_align',
			array(
				'label'     => __( 'Place Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .premium-fb-rev-page' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'place_info' => 'yes',
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
				'label'              => __( 'Reviews/Row', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'100%'    => __( '1 Column', 'premium-addons-pro' ),
					'50%'     => __( '2 Columns', 'premium-addons-pro' ),
					'33.33%'  => __( '3 Columns', 'premium-addons-pro' ),
					'25%'     => __( '4 Columns', 'premium-addons-pro' ),
					'20%'     => __( '5 Columns', 'premium-addons-pro' ),
					'16.667%' => __( '6 Columns', 'premium-addons-pro' ),
				),
				'default'            => '33.33%',
				'tablet_default'     => '100%',
				'mobile_default'     => '100%',
				'render_type'        => 'template',
				'selectors'          => array(
					'{{WRAPPER}} .premium-fb-rev-review-wrap' => 'width: {{VALUE}}',
				),
				'frontend_available' => true,
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
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
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
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
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
				'label'              => __( 'Layout', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'even'    => __( 'Even', 'premium-addons-pro' ),
					'masonry' => __( 'Masonry', 'premium-addons-pro' ),
				),
				'default'            => 'masonry',
				'condition'          => array(
					'reviews_columns!' => '100%',
				),
				'frontend_available' => true,
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
				'conditions'         => array(
					'terms' => array(
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'reviews_display',
									'operator' => '!==',
									'value'    => 'block',
								),
								array(
									'name'  => 'skin_type',
									'value' => 'bubble',
								),
							),
						),
					),
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
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
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
				'label'              => __( 'Carousel', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'infinite_autoplay',
			array(
				'label'              => __( 'Infinite Autoplay', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'reviews_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'rows',
			array(
				'label'              => __( 'Rows/Column', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 2,
				'min'                => 1,
				'max'                => 3,
				'condition'          => array(
					'reviews_carousel'  => 'yes',
					'infinite_autoplay' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_play',
			array(
				'label'              => __( 'Autoplay', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'reviews_carousel'   => 'yes',
					'infinite_autoplay!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'conditions'         => array(
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
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_navigation',
			array(
				'label'              => __( 'Navigation', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'none'   => __( 'None', 'premium-addons-pro' ),
					'arrows' => __( 'Arrows', 'premium-addons-pro' ),
					'dots'   => __( 'Dots', 'premium-addons-pro' ),
					'all'    => __( 'Dots & Arrows', 'premium-addons-pro' ),
				),
				'default'            => 'arrows',
				'condition'          => array(
					'reviews_carousel'   => 'yes',
					'infinite_autoplay!' => 'yes',
				),
				'frontend_available' => true,
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

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'source_settings',
			array(
				'label' => __( 'Place Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'place_info',
			array(
				'label'   => __( 'Place Info', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'source_image',
			array(
				'label'        => __( 'Place Image', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'true',
				'return_value' => 'true',
				'condition'    => array(
					'place_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'source_name',
			array(
				'label'        => __( 'Place Name', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'true',
				'return_value' => 'true',
				'condition'    => array(
					'place_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'place_rate',
			array(
				'label'     => __( 'Place Rate', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'place_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_number',
			array(
				'label'     => __( 'Number of Reviews', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'place_info' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_number_text',
			array(
				'label'       => __( 'Reviews Number Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Based on {{number}} reviews',
				'description' => __( 'This helps to control number of reviews string. {{number}} will be repalced with the number of reviews', 'premium-addons-pro' ),
				'condition'   => array(
					'place_info'     => 'yes',
					'reviews_number' => 'yes',
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
					'place_info' => 'yes',
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
				'label' => __( 'Show Date', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
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
				'default'   => 'column',
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
				'default'   => 'd/m/Y',
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
				'condition' => array(
					'text' => 'yes',
				),
			)
		);

		$this->add_control(
			'readmore',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-pro' ),
				'condition' => array(
					'text'       => 'yes',
					'words_num!' => '',
				),
			)
		);

		$this->add_control(
			'language_prefix',
			array(
				'label'       => __( 'Get Reviews By Language', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter language prefix, eg. en for English, ja for Japanese, if you don\'t know your language prefix, please check <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank">here</a>' ),
			)
		);

		$this->add_control(
			'sort',
			array(
				'label'   => __( 'Sort By', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'most_relevant' => __( 'Relevance', 'premium-addons-pro' ),
					'newest'        => __( 'Newest', 'premium-addons-pro' ),
				),
				'default' => 'most_relevant',
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
				'max'         => 5,
				'description' => __( 'You can only pull 5 reviews from Google', 'premium-addons-pro' ),
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
			'https://premiumaddons.com/docs/google-reviews-widget-tutorial/' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/getting-your-api-key-for-google-reviews/' => __( 'How to get an API key for Google Reviews »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-get-google-place-id' => __( 'How to get your place ID »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-enable-places-api-for-premium-google-reviews-widget' => __( 'Error: This APi project is not authorized to use this API »', 'premium-addons-pro' ),
			'https://www.youtube.com/watch?v=Z0EeGyD34Zk' => __( 'Check the video tutorial »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-clear-cached-data-in-social-reviews-widgets/' => __( 'How to clear cached data manually »', 'premium-addons-pro' ),
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
			'place_img_tab',
			array(
				'label'     => __( 'Place', 'premium-addons-pro' ),
				'condition' => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'place_image_size',
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
					'place_custom_image_switch!' => 'yes',
					'place_info'                 => 'yes',
					'source_image'               => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'place_image_border',
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img',
				'condition' => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
			)
		);

		$this->add_control(
			'place_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'condition'  => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'place_image_shadow',
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-img',
				'condition' => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'place_image_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'place_info'   => 'yes',
					'source_image' => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-fb-rev-page-inner .premium-fb-rev-page-left' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}}.premium-reviews-ltr .premium-rev-arrow-bubble' => 'left: calc( {{SIZE}}{{UNIT}} - 24px );',
					'{{WRAPPER}}.premium-reviews-rtl .premium-rev-arrow-bubble' => 'right: calc( {{SIZE}}{{UNIT}} - 24px );',
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
				'label'     => __( 'Place Info', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'place_info' => 'yes',
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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

		$this->start_controls_tabs( 'place_info_tabs' );

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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-fb-rev-page-link',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'place_shadow',
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
			'place_star_size',
			array(
				'label'     => __( 'Star Size', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 50,
				'default'   => 20,
				'condition' => array(
					'source_stars' => 'true',
				),
			)
		);

		$this->add_control(
			'place_fill',
			array(
				'label'     => __( 'Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffab40',
				'global'    => false,
				'condition' => array(
					'source_stars' => 'true',
				),
			)
		);

		$this->add_control(
			'place_empty',
			array(
				'label'     => __( 'Empty Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'source_stars' => 'true',
				),
			)
		);

		$this->add_control(
			'page_rate_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'place_rate' => 'yes',
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
					'place_rate' => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-rating',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'place_rate_shadow',
				'selector'  => '{{WRAPPER}} .premium-fb-rev-page-rating',
				'condition' => array(
					'place_rate' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'page_rate_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'place_rate' => 'yes',
				),
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
			'reviews_star_size',
			array(
				'label'     => __( 'Star Size', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 50,
				'default'   => 15,
				'condition' => array(
					'stars' => 'yes',
				),
			)
		);

		$this->add_control(
			'reviews_fill',
			array(
				'label'     => __( 'Star Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'default'   => '#ffab40',
				'condition' => array(
					'stars' => 'yes',
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
					'stars' => 'yes',
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
				'condition'  => array(
					'review_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'review_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'review_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-review' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'review_adv_radius' => 'yes',
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
					'{{WRAPPER}}.premium-fb-page-next-yes.premium-reviews-place-v-bottom .premium-fb-rev-page-inner ' => 'transform: translateY(calc(-100% - {{BOTTOM}}{{UNIT}} )) !important',
					'{{WRAPPER}}.premium-fb-page-next-yes.premium-reviews-place-v-top .premium-fb-rev-page-inner ' => 'transform: translateY({{TOP}}{{UNIT}}) !important',
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'condition'   => array(
					'skin_type' => 'bubble',
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
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
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
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
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
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow' => 'color: {{VALUE}};',
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
			'arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-fb-rev-container .slick-arrow' => 'background-color: {{VALUE}};',
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
				'label'     => __( 'Carousel Dots', 'premium-addons-pro' ),
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
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'carousel_dot_active_color',
			array(
				'label'     => __( 'Active Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li.slick-active' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Google Reviews widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$api_key = $settings['api_key'];

		$place_id = $settings['place_id'];

		$transient = $settings['reload'];

		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( empty( $api_key ) || empty( $place_id ) ) { ?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please Enter a Valid API Key & Place ID', 'premium-addons-pro' ) ); ?>
			</div>
			<?php
			return;
		}

		$transient_name = sprintf( 'papro_reviews_%s_%s', $place_id, $id );

		$stored_revs = get_option( $transient_name, array() );

		do_action( 'papro_reviews_transient', $transient_name, $settings );

		if ( $is_edit_mode ) {
			$response = false;
		} else {
			$response = get_transient( $transient_name );
		}

		$new_revs = array();

		if ( false === $response ) {

			sleep( 2 );

			$lang_prefix = $settings['language_prefix'];

			$sort = $settings['sort'];

			$response = premium_google_rev_api_rating( $api_key, $place_id, $lang_prefix, $sort );

			$response_data = $response['data'];

			$response_json = rplg_json_decode( $response_data );

			$response = $response_json;

			if ( isset( $response->error_message ) ) {
				$error_message = $response->error_message;
				?>
				<div class="premium-error-notice">
					<?php
						echo wp_kses_post( sprintf( 'Something went wrong: %s', $error_message ) );
					?>
				</div>
				<?php
				return;
			}

			$expire_time = Helper_Functions::transient_expire( $transient );

			set_transient( $transient_name, $response, $expire_time );

			if ( isset( $response->result->reviews ) ) {
				$stored_revs = array_merge( $response->result->reviews, $stored_revs );

				update_option( $transient_name, $stored_revs );
			}
		}

		$response_results = $response->result;

		$result_status = $response->status;

		if ( 'OK' !== $result_status || ! property_exists( $response_results, 'reviews' ) ) {
			?>
			<div class="premium-error-notice">
				<?php
					echo wp_kses_post( sprintf( 'Something went wrong, error: %s', $result_status ) );
				?>
			</div>
			<?php
			delete_transient( $transient_name );
			return false;
		}

		$place = $response_results;

		// Using $stored_reviews directly will cause issues if the reviews were not cached using update_option, and get_transient is still not false.
		$reviews = count( $stored_revs ) < 1 ? $place->reviews : $stored_revs;

		$rating = 0;

		if ( isset( $place->rating ) ) {

			if ( $place->rating > $rating ) {

				$rating = $place->rating;

			}
		} elseif ( ! empty( $reviews ) ) {

			if ( count( $reviews ) > 0 ) {

				foreach ( $reviews as $review ) {

					$rating = $rating + $review->rating;

				}

				$rating = round( $rating / count( $reviews ), 1 );
				$rating = number_format( (float) $rating, 1, '.', '' );
			}
		}

		if ( 'yes' === $settings['place_info'] && 'yes' === $settings['place_custom_image_switch'] ) {

			$image_src = $settings['place_custom_image'];

			$image_src_size = Group_Control_Image_Size::get_attachment_image_src( $image_src['id'], 'thumbnail', $settings );

			if ( empty( $image_src_size ) ) {
				$image_src_size = $image_src['url'];
			} else {
				$image_src_size = $image_src_size;
			}

			$custom_image = ! empty( $image_src_size ) ? $image_src_size : '';

		} else {
			$custom_image = '';
		}

		$show_stars = 'yes' === $settings['stars'] ? true : false;

		$show_date = 'yes' === $settings['date'] ? true : false;

		$date_format = $settings['date_format'];

		$this->add_render_attribute( 'place', 'class', 'premium-fb-rev-page' );

		$this->add_render_attribute( 'reviews', 'class', 'premium-fb-rev-content' );

		$place_rate = ( 'yes' === $settings['place_info'] && 'yes' === $settings['place_rate'] ) ? true : false;

		$rev_text = 'yes' === $settings['text'] ? true : false;

		$rev_length = $settings['words_num'];

		$place_star_size   = ! empty( $settings['place_star_size'] ) ? $settings['place_star_size'] : 16;
		$place_fill_color  = ! empty( $settings['place_fill'] ) ? $settings['place_fill'] : '#ffab40';
		$place_empty_color = ! empty( $settings['place_empty'] ) ? $settings['place_empty'] : '#ccc';

		$rev_star_size   = ! empty( $settings['reviews_star_size'] ) ? $settings['reviews_star_size'] : 16;
		$rev_fill_color  = ! empty( $settings['reviews_fill'] ) ? $settings['reviews_fill'] : '#6ec1e4';
		$rev_empty_color = ! empty( $settings['reviews_empty'] ) ? $settings['reviews_empty'] : '#ccc';

		if ( 'yes' === $settings['limit'] ) {
			// phpcs:ignore WordPress.PHP.StrictComparisons
			if ( '0' == $settings['limit_num'] ) {
				$limit = 0;
			} else {
				$limit = ! empty( $settings['limit_num'] ) ? $settings['limit_num'] : 5;
			}
		} else {
			$limit = 5;
		}

		if ( 'yes' === $settings['filter'] ) {
			$min_filter = ! empty( $settings['filter_min'] ) ? $settings['filter_min'] : 1;
			$max_filter = ! empty( $settings['filter_max'] ) ? $settings['filter_max'] : 5;
		} else {
			$min_filter = 1;
			$max_filter = 5;
		}

		$carousel = 'yes' === $settings['reviews_carousel'] ? true : false;

		$place_settings = array(
			'image'       => $custom_image,
			'show_name'   => $settings['source_name'],
			'show_image'  => $settings['source_image'],
			'rating'      => $rating,
			'color'       => $place_fill_color,
			'empty_color' => $place_empty_color,
			'stars'       => $settings['source_stars'],
			'stars_size'  => $place_star_size,
			'place_rate'  => $place_rate,
			'rev_number'  => $settings['reviews_number'],
			'number_text' => str_replace( '{{number}}', '%s', $settings['reviews_number_text'] ),
			'key'         => $api_key,
			'id'          => $id,
		);

		$reviews_settings = array(
			'show_image'    => $settings['reviewer_image'],
			'id'            => $place_id,
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
			'show_icon'     => $settings['source_icon'],
			'image_display' => $settings['reviews_display'],
		);

		$this->add_render_attribute(
			'container',
			'class',
			array(
				'premium-fb-rev-container',
				'google-reviews',
				'premium-reviews-' . $settings['reviews_style'],
			)
		);

		if ( 'yes' === $settings['schema'] ) {

			$ratings = $response_results->user_ratings_total;

			$address = $response_results->adr_address;

			$name = $response_results->name;

			$this->add_render_attribute(
				'container',
				array(
					'itemscope' => 'Organization',
					'itemtype'  => 'http://schema.org/AggregateRating',
				)
			);
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div class="premium-fb-rev-list">
				<?php if ( 'yes' === $settings['place_info'] ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'place' ) ); ?>>
						<div class="premium-fb-rev-page-inner">
							<?php premium_reviews_place( $place, $place_settings ); ?>
						</div>
						<?php if ( '0' == $limit && 'yes' === $settings['source_icon'] ) : ?>
							<div class="premium-fb-rev-icon">
								<svg version="1.1" id="fi_281764" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <path style="fill:#FBBB00;" d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256 c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456 C103.821,274.792,107.225,292.797,113.47,309.408z"></path> <path style="fill:#518EF8;" d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451 c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535 c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z"></path> <path style="fill:#28B446;" d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512 c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771 c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z"></path> <path style="fill:#F14336;" d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012 c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0 C318.115,0,375.068,22.126,419.404,58.936z"></path> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $reviews ) ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'reviews' ) ); ?>>
						<?php premium_google_rev_reviews( $reviews, $reviews_settings ); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( 'yes' === $settings['schema'] ) { ?>
				<span itemprop="ratingValue" class="elementor-screen-only"><?php echo wp_kses_post( $rating ); ?></span>
				<span itemprop="reviewCount" class="elementor-screen-only"><?php echo wp_kses_post( $ratings ); ?></span>
				<div class="elementor-screen-only" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Organization">
					<span itemprop="name" class="elementor-screen-only"><?php echo wp_kses_post( $name ); ?></span>
					<div class="elementor-screen-only" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
						<span itemprop="streetAddress" class="elementor-screen-only"><?php echo wp_kses_post( $address ); ?></span>
					</div>
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
