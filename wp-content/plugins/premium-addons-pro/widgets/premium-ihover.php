<?php
/**
 * Class: Premium_Ihover
 * Name: iHover
 * Slug: premium-ihover
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Ihover
 */
class Premium_Ihover extends Widget_Base {

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		$this->template_instance = Premium_Template_Tags::getInstance();
		return $this->template_instance;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-ihover';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'iHover', 'premium-addons-pro' ) );
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
		return 'pa-pro-ihover';
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
		return array( 'cta', 'action', 'link', 'image', 'animation' );
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
			'lottie-js',
			'premium-pro',
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register iHover controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_ihover_image_content_section',
			array(
				'label' => __( 'Image', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_front_image',
			array(
				'label'       => __( 'Choose Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'em' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-item.style20 .premium-ihover-spinner, {{WRAPPER}} .premium-ihover-item, {{WRAPPER}} .premium-ihover-img-wrap, {{WRAPPER}} .premium-ihover-img-wrap .premium-ihover-img, {{WRAPPER}} .premium-ihover-info-wrap' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_ihover_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-item-wrap, {{WRAPPER}} .premium-ihover-img, {{WRAPPER}} .premium-ihover-info-back, {{WRAPPER}} .premium-ihover-spinner' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_hover_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'style18',
				'options'     => array(
					'style18'  => 'Advertising',
					'style19'  => 'Book Cover',
					'style10'  => 'Backward',
					'style15'  => 'Faded In Background',
					'style17'  => 'Flash Rotation',
					'style4'   => 'Flip Background',
					'style16'  => 'Flip Door',
					'style9'   => 'Heroes Flying-Top',
					'style9-1' => 'Heroes Flying-Bottom',
					'style9-2' => 'Heroes Flying-Right',
					'style14'  => 'Magic Door',
					'style2'   => 'Reduced Image-Top',
					'style2-2' => 'Reduced Image-Right',
					'style6'   => 'Reduced Image-Bottom',
					'style2-1' => 'Reduced Image-Left',
					'style7'   => 'Rotated Image-Left',
					'style7-1' => 'Rotated Image-Right',
					'style13'  => 'Rotated Wheel Image-Left',
					'style8'   => 'Rotating Wheel-Left',
					'style8-1' => 'Rotating Wheel-Top',
					'style8-2' => 'Rotating Wheel-Bottom',
					'style8-3' => 'Rotating Wheel-Right',
					'style1'   => 'Rotor Cube',
					'style11'  => 'Slided Out Image',
					'style12'  => 'Slided In Image',
					'style20'  => 'Spinner',
					'style5'   => 'Zoom In ',
					'style5-1' => 'Zoom Out',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_link_switcher',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Add a custom link or select an existing page link', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_link_type',
			array(
				'label'       => __( 'Link/URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'condition'   => array(
					'premium_ihover_thumbnail_link_switcher'  => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_existing_page',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_ihover_thumbnail_link_switcher' => 'yes',
					'premium_ihover_thumbnail_link_type' => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_url',
			array(
				'label'       => __( 'URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => 'https://premiumaddons.com/',
				'default'     => array(
					'url' => '#',
				),
				'condition'   => array(
					'premium_ihover_thumbnail_link_switcher' => 'yes',
					'premium_ihover_thumbnail_link_type' => 'url',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_link_text',
			array(
				'label'       => __( 'Link Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'premium_ihover_thumbnail_link_switcher' => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_link_whole',
			array(
				'label'     => __( 'Whole Box Link', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_ihover_thumbnail_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_button_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click Here', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'premium_ihover_thumbnail_link_switcher' => 'yes',
					'premium_ihover_link_whole!' => 'yes',
				),
				'label_block' => false,
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
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
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-list' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_ihover_css_classes',
			array(
				'label'       => __( 'CSS Classes', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'title'       => __( 'Add your custom class without the dot. e.g: my-class', 'premium-addons-pro' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_description_content_section',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_ihover_icon_fa_switcher',
			array(
				'label'   => __( 'Icon', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_ihover_icon_selection',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-pro' ),
				'description' => __( 'Select type for the icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'icon',
				'options'     => array(
					'icon'      => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'image'     => __( 'Image', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_icon_fa_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'description'      => __( 'Choose an Icon for Front Side', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_ihover_icon_fa',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
					'premium_ihover_icon_selection'   => 'icon',
				),
			)
		);

		$this->add_control(
			'premium_ihover_icon_image',
			array(
				'label'       => __( 'Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'description' => __( 'Choose the icon image', 'premium-addons-pro' ),
				'label_block' => true,
				'condition'   => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
					'premium_ihover_icon_selection'   => 'image',
				),
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
					'premium_ihover_icon_selection'   => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
					'premium_ihover_icon_selection'   => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
					'premium_ihover_icon_selection'   => 'animation',
				),
			)
		);

		$this->add_control(
			'premium_ihover_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'size' => 50,
				),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-title-wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_back_title_switcher',
			array(
				'label'       => __( 'Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable/Disable Title', 'premium-addons-pro' ),
				'default'     => 'yes',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_back_title',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'placeholder' => __( 'Awesome Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Awesome Title', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_ihover_thumbnail_back_title_switcher'  => 'yes',
				),
				'label_block' => false,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_back_title_tag',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-pro' ),
				'description' => __( 'Select a heading tag for the title. Headings are defined with H1 to H6 tags', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h4',
				'options'     => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'condition'   => array(
					'premium_ihover_thumbnail_back_title_switcher'  => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_back_separator_switcher',
			array(
				'label'       => __( 'Separator', 'premium-addons-pro' ),
				'description' => __( 'Enable/Disable Separator', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_back_description_switcher',
			array(
				'label'       => __( 'Description', 'premium-addons-pro' ),
				'description' => __( 'Enable/Disable Description', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_back_description',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Cool Description', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_ihover_thumbnail_back_description_switcher'  => 'yes',
				),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_ihover_description_alignment_content',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
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
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-content-wrap' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/premium-ihover-widget/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_front_image',
			array(
				'label' => __( 'Front Image', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-ihover-img',
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'elementor' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-img' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_icon_style_section',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_ihover_icon_fa_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_fa_color_selection',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-ihover-title-wrap svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_ihover_icon_selection' => 'icon',
				),
			)
		);

		$this->add_control(
			'premium_ihover_icon_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-icon, {{WRAPPER}} .premium-ihover-title-wrap svg, {{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-lottie'    => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_ihover_icon_border',
				'selector' => '{{WRAPPER}} .premium-ihover-icon, {{WRAPPER}} .premium-ihover-title-wrap svg, {{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-lottie',
			)
		);

		$this->add_control(
			'premium_ihover_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-icon, {{WRAPPER}} .premium-ihover-title-wrap svg, {{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-lottie'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_ihover_icon_shadow',
				'selector'  => '{{WRAPPER}} .premium-ihover-icon, {{WRAPPER}} .premium-ihover-title-wrap svg',
				'condition' => array(
					'premium_ihover_icon_selection' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_ihover_image_shadow',
				'selector'  => '{{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-lottie',
				'condition' => array(
					'premium_ihover_icon_selection!' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-icon, {{WRAPPER}} .premium-ihover-title-wrap svg, {{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-lottie' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-icon, {{WRAPPER}} .premium-ihover-title-wrap svg, {{WRAPPER}} .premium-ihover-icon-image, {{WRAPPER}} .premium-ihover-lottie' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_title_style_section',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_ihover_thumbnail_back_title_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_title_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_ihover_thumbnail_title_typhography',
				'selector' => '{{WRAPPER}} .premium-ihover-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_ihover_thumbnail_title_text_shadow',
				'selector' => '{{WRAPPER}} .premium-ihover-title',
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_thumbnail_divider_style_tab',
			array(
				'label'     => __( 'Separator', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_ihover_thumbnail_back_separator_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_divider_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-divider .premium-ihover-divider-line' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_divider_type',
			array(
				'label'     => __( 'Style', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'none'   => __( 'None', 'premium-addons-pro' ),
					'solid'  => __( 'Solid', 'premium-addons-pro' ),
					'double' => __( 'Double', 'premium-addons-pro' ),
					'dotted' => __( 'Dotted', 'premium-addons-pro' ),
				),
				'default'   => 'solid',
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-divider .premium-ihover-divider-line' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_divider_width',
			array(
				'label'       => __( 'Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Enter Separator width in (PX, EM, %), default is 100%', 'premium-addons-pro' ),
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 450,
					),
					'em' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-ihover-divider .premium-ihover-divider-line' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_divider_height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-ihover-divider' => 'height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_ihover_thumbnail_divider_box_shadow',
				'selector' => '{{WRAPPER}} .premium-ihover-divider',
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_divider_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-divider' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_thumbnail_description_style_tab',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_ihover_thumbnail_back_description_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_description_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_ihover_thumbnail_description_typhography',
				'selector' => '{{WRAPPER}} .premium-ihover-description',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_ihover_thumbnail_description_text_shadow',
				'selector' => '{{WRAPPER}} .premium-ihover-description',
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_description_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_thumbnail_description_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_btn_style',
			array(
				'label'     => __( 'Button', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_ihover_thumbnail_link_switcher' => 'yes',
					'premium_ihover_link_whole!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_ihover_btn_hover_scale',
			array(
				'label'        => __( 'Hover Grow Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-ihover-btn-scale-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typo',
				'selector' => '{{WRAPPER}} .premium-ihover-link',
			)
		);

		$this->add_control(
			'premium_ihover_btn_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_btn_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-link:hover'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_btn_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_btn_hover_background',
			array(
				'label'     => __( 'Background Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-link:hover'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_hover_btn_border',
				'selector' => '{{WRAPPER}} .premium-ihover-link',
			)
		);

		$this->add_control(
			'premium_ihover_btn_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_ihover_btn_shadow',
				'selector' => '{{WRAPPER}} .premium-ihover-link',
			)
		);

		$this->add_responsive_control(
			'premium_ihover_btn_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-btn-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_btn_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_thumbnail_spinner_style_section',
			array(
				'label'     => __( 'Spinner', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_ihover_thumbnail_hover_effect' => 'style20',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_spinner_type',
			array(
				'label'     => __( 'Style', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'none'   => __( 'None', 'premium-addons-pro' ),
					'solid'  => __( 'Solid', 'premium-addons-pro' ),
					'double' => __( 'Double', 'premium-addons-pro' ),
					'dotted' => __( 'Dotted', 'premium-addons-pro' ),
					'dashed' => __( 'Dashed', 'premium-addons-pro' ),
					'groove' => __( 'Groove', 'premium-addons-pro' ),
				),
				'default'   => 'solid',
				'condition' => array(
					'premium_ihover_thumbnail_hover_effect' => 'style20',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-item.style20 .premium-ihover-spinner' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_spinner_border_width',
			array(
				'label'      => __( 'Border Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'premium_ihover_thumbnail_hover_effect' => 'style20',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-item.style20 .premium-ihover-spinner' => 'border-width:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_spinner_border_left_color',
			array(
				'label'     => __( 'First Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'condition' => array(
					'premium_ihover_thumbnail_hover_effect' => 'style20',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-spinner' => 'border-top-color: {{VALUE}}; border-left-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_spinner_border_right_color',
			array(
				'label'     => __( 'Second Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'condition' => array(
					'premium_ihover_thumbnail_hover_effect' => 'style20',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-spinner' => 'border-bottom-color: {{VALUE}};border-right-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_ihover_container_style_section',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_ihover_thumbnail_background_color',
			array(
				'label'     => __( 'Hover Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-info-back' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_ihover_container_background',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-ihover-item-wrap' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_ihover_container_border',
				'selector'  => '{{WRAPPER}} .premium-ihover-item-wrap',
				'condition' => array(
					'premium_ihover_thumbnail_hover_effect!'  => 'style20',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_ihover_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-ihover-img',
			)
		);

		$this->add_responsive_control(
			'premium_ihover_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-item-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_ihover_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-ihover-item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Get Link
	 *
	 * Renders the link HTML markup.
	 *
	 * @return void
	 */
	protected function get_link() {

		$settings = $this->get_settings_for_display();

		$ihover_link_type = $settings['premium_ihover_thumbnail_link_type'];

		if ( 'url' === $ihover_link_type ) {
			$link_url = $settings['premium_ihover_thumbnail_url']['url'];
		} elseif ( 'link' === $ihover_link_type ) {
			$link_url = get_permalink( $settings['premium_ihover_thumbnail_existing_page'] );
		}

		$class = 'premium-ihover-link';

		if ( 'yes' === $settings['premium_ihover_link_whole'] ) {
			$class = 'premium-ihover-full-link';
		}

		$this->add_render_attribute(
			'link',
			array(
				'class' => $class,
				'href'  => $link_url,
				'title' => $settings['premium_ihover_thumbnail_link_text'],
			)
		);

		if ( ! empty( $settings['premium_ihover_thumbnail_url']['is_external'] ) ) {
			$this->add_render_attribute( 'link', 'target', '_blank' );
		}

		if ( ! empty( $settings['premium_ihover_thumbnail_url']['nofollow'] ) ) {
			$this->add_render_attribute( 'link', 'rel', 'nofollow' );
		}

		?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>

		<?php
		if ( 'yes' !== $settings['premium_ihover_link_whole'] ) {
			echo esc_html( $settings['premium_ihover_button_text'] ) . '</a>';
		}
	}


	/**
	 * Render iHover widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'container', 'class', array( 'premium-ihover-container', $settings['premium_ihover_css_classes'] ) );

		$ihover_title_tag = PAPRO_Helper::validate_html_tag( $settings['premium_ihover_thumbnail_back_title_tag'] );

		$this->add_inline_editing_attributes( 'title', 'basic' );

		$this->add_render_attribute( 'title', 'class', 'premium-ihover-title' );

		$this->add_inline_editing_attributes( 'description', 'advanced' );

		$this->add_render_attribute( 'description', 'class', 'premium-ihover-description' );

		$this->add_render_attribute( 'item', 'class', array( 'premium-ihover-item', $settings['premium_ihover_thumbnail_hover_effect'] ) );

		$this->add_render_attribute( 'img', 'class', 'premium-ihover-img' );

		$this->add_render_attribute( 'img', 'src', $settings['premium_ihover_thumbnail_front_image']['url'] );
		$this->add_render_attribute( 'img', 'alt', Control_Media::get_image_alt( $settings['premium_ihover_thumbnail_front_image'] ) );

		if ( 'yes' === $settings['premium_ihover_icon_fa_switcher'] ) {

			$icon_type = $settings['premium_ihover_icon_selection'];

			if ( 'icon' === $icon_type ) {

				if ( ! empty( $settings['premium_ihover_icon_fa'] ) ) {
					$this->add_render_attribute(
						'icon',
						'class',
						array(
							'premium-ihover-icon',
							$settings['premium_ihover_icon_fa'],
						)
					);
					$this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
				}

				$migrated = isset( $settings['__fa4_migrated']['premium_ihover_icon_fa_updated'] );
				$is_new   = empty( $settings['premium_ihover_icon_fa'] ) && Icons_Manager::is_migration_allowed();

			} elseif ( 'image' === $icon_type ) {

				$alt = Control_Media::get_image_alt( $settings['premium_ihover_icon_image'] );

				$this->add_render_attribute(
					'content_img',
					array(
						'class' => 'premium-ihover-icon-image',
						'src'   => $settings['premium_ihover_icon_image']['url'],
						'alt'   => $alt,
					)
				);

			} else {

				$this->add_render_attribute(
					'lottie_icon',
					array(
						'class'               => array(
							'premium-ihover-lottie',
							'premium-lottie-animation',
						),
						'data-lottie-url'     => $settings['lottie_url'],
						'data-lottie-loop'    => $settings['lottie_loop'],
						'data-lottie-reverse' => $settings['lottie_reverse'],
					)
				);

			}
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div class="premium-ihover-list">
				<div class="premium-ihover-item-wrap">
					<?php
					if ( 'yes' === $settings['premium_ihover_link_whole'] && 'yes' === $settings['premium_ihover_thumbnail_link_switcher'] ) :
							echo $this->get_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'item' ) ); ?>>
						<?php if ( 'style20' === $settings['premium_ihover_thumbnail_hover_effect'] ) : ?>
							<div class="premium-ihover-spinner"></div>
						<?php endif; ?>
						<div class="premium-ihover-img-wrap">
							<div class="premium-ihover-img-front">
								<div class="premium-ihover-img-inner-wrap"></div>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'img' ) ); ?>>
							</div>
						</div>
						<div class="premium-ihover-info-wrap">
							<div class="premium-ihover-info-back">
								<div class="premium-ihover-content">
									<div class="premium-ihover-content-wrap">
										<div class="premium-ihover-title-wrap">
											<?php if ( 'yes' === $settings['premium_ihover_icon_fa_switcher'] ) : ?>
												<?php
												if ( 'icon' === $icon_type ) :
													if ( $is_new || $migrated ) :
														Icons_Manager::render_icon(
															$settings['premium_ihover_icon_fa_updated'],
															array(
																'class' => 'premium-ihover-icon',
																'aria-hidden' => 'true',
															)
														);
													else :
														?>
														<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
														<?php
													endif;
												elseif ( 'image' === $icon_type ) :
													?>
													<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'content_img' ) ); ?>>
												<?php else : ?>
													<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie_icon' ) ); ?>></div>
												<?php endif; ?>
											<?php endif; ?>

											<?php if ( 'yes' === $settings['premium_ihover_thumbnail_back_title_switcher'] ) : ?>
												<<?php echo wp_kses_post( $ihover_title_tag . ' ' . $this->get_render_attribute_string( 'title' ) ); ?>>
													<?php echo wp_kses_post( $settings['premium_ihover_thumbnail_back_title'] ); ?>
												</<?php echo wp_kses_post( $ihover_title_tag ); ?>>
											<?php endif; ?>

										</div>

										<?php if ( 'yes' === $settings['premium_ihover_thumbnail_back_separator_switcher'] ) : ?>
											<div class="premium-ihover-divider">
												<span class="premium-ihover-divider-line"></span>
											</div>
										<?php endif; ?>

										<?php if ( 'yes' === $settings['premium_ihover_thumbnail_back_description_switcher'] ) : ?>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'description' ) ); ?>>
												<?php echo $this->parse_text_editor( $settings['premium_ihover_thumbnail_back_description'] ); ?>
											</div>
										<?php endif; ?>

										<?php if ( 'yes' !== $settings['premium_ihover_link_whole'] && 'yes' === $settings['premium_ihover_thumbnail_link_switcher'] ) : ?>
											<div class="premium-ihover-btn-container">
												<?php echo $this->get_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
										<?php endif; ?>

									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
					if ( 'yes' === $settings['premium_ihover_link_whole'] && 'yes' === $settings['premium_ihover_thumbnail_link_switcher'] ) :
						echo '</a>';
						endif;
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render iHover widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			view.addRenderAttribute('container', 'class', [ 'premium-ihover-container', settings.premium_ihover_css_classes ] );

			var titleTag = elementor.helpers.validateHTMLTag( settings.premium_ihover_thumbnail_back_title_tag ),

				linkUrl = 'url' == settings.premium_ihover_thumbnail_link_type ? settings.premium_ihover_thumbnail_url.url : settings.premium_ihover_thumbnail_existing_page,

				hoverEffect = settings.premium_ihover_thumbnail_hover_effect;

			view.addInlineEditingAttributes('title', 'basic');

			view.addRenderAttribute('title', 'class', 'premium-ihover-title');

			view.addInlineEditingAttributes('description', 'advanced');

			view.addRenderAttribute('description', 'class', 'premium-ihover-description');

			view.addRenderAttribute('premium_ihover_item', 'class', [ 'premium-ihover-item', hoverEffect ] );

			if( 'yes' === settings.premium_ihover_icon_fa_switcher ) {

				var iconType = settings.premium_ihover_icon_selection;

				if( 'icon' === iconType ) {

					var iconHTML = elementor.helpers.renderIcon( view, settings.premium_ihover_icon_fa_updated, {'class': 'premium-ihover-icon', 'aria-hidden': true }, 'i' , 'object' ),
						migrated = elementor.helpers.isIconMigrated( settings, 'premium_ihover_icon_fa_updated' );

				} else if ( 'image' === iconType ) {

					view.addRenderAttribute( 'content_img', 'class', 'premium-ihover-icon-image' );
					view.addRenderAttribute( 'content_img', 'src', settings.premium_ihover_icon_image.url );

				} else {

					view.addRenderAttribute( 'lottie_icon', {
						'class': [
							'premium-ihover-lottie',
							'premium-lottie-animation'
						],
						'data-lottie-url': settings.lottie_url,
						'data-lottie-loop': settings.lottie_loop,
						'data-lottie-reverse': settings.lottie_reverse
					});

				}

			}

		#>

		<div {{{ view.getRenderAttributeString('container') }}}>
			<div class="premium-ihover-list">
				<div class="premium-ihover-item-wrap">
					<# if( 'yes' === settings.premium_ihover_link_whole && 'yes' === settings.premium_ihover_thumbnail_link_switcher ) { #>
						<a class="premium-ihover-item-full-link" href="{{ linkUrl }}" title="{{ settings.premium_ihover_thumbnail_link_text }}">
					<# } #>
					<div {{{ view.getRenderAttributeString('premium_ihover_item') }}}>
						<# if( 'style20' == hoverEffect ){ #>
							<div class="premium-ihover-spinner"></div>
						<# } #>
						<div class="premium-ihover-img-wrap">
							<div class="premium-ihover-img-front">
								<div class="premium-ihover-img-inner-wrap"></div>
								<img class="premium-ihover-img" src="{{ settings.premium_ihover_thumbnail_front_image.url }}">
							</div>
						</div>

						<div class="premium-ihover-info-wrap">
							<div class="premium-ihover-info-back">
								<div class="premium-ihover-content">
									<div class="premium-ihover-content-wrap">
										<div class="premium-ihover-title-wrap">
											<# if( 'yes' == settings.premium_ihover_icon_fa_switcher ) { #>
												<# if( 'icon' == settings.premium_ihover_icon_selection ) {
													if ( iconHTML && iconHTML.rendered && ( ! settings.premium_ihover_icon_fa || migrated ) ) { #>
														{{{ iconHTML.value }}}
													<# } else { #>
														<i class="premium-ihover-icon {{ settings.premium_ihover_icon_fa }}"></i>
													<# } #>
												<# } else if( 'image' === iconType ) { #>
													<img {{{ view.getRenderAttributeString('content_img') }}}>
												<# } else { #>
													<div {{{ view.getRenderAttributeString('lottie_icon') }}}></div>
												<# } #>
											<# } #>

											<# if( 'yes' == settings.premium_ihover_thumbnail_back_title_switcher ) { #>
												<{{{titleTag}}} {{{ view.getRenderAttributeString('title') }}} >{{{ settings.premium_ihover_thumbnail_back_title }}}</{{{titleTag}}}>
											<# } #>

										</div>
										<# if( 'yes' == settings.premium_ihover_thumbnail_back_separator_switcher ) { #>
											<div class="premium-ihover-divider"><span class="premium-ihover-divider-line"></span></div>
										<# } #>

										<# if( 'yes' == settings.premium_ihover_thumbnail_back_description_switcher ) { #>
											<div {{{ view.getRenderAttributeString('description') }}}>
												{{{ settings.premium_ihover_thumbnail_back_description }}}
											</div>
										<# } #>

										<# if ( 'yes' !== settings.premium_ihover_link_whole && 'yes' === settings.premium_ihover_thumbnail_link_switcher ) { #>
											<div class="premium-ihover-btn-container">
												<a class="premium-ihover-link" href="{{ linkUrl }}" title="{{ settings.premium_ihover_thumbnail_link_text }}">{{{settings.premium_ihover_button_text}}}</a>
											</div>
										<# } #>

									</div>
								</div>
							</div>
						</div>
					</div>
					<# if( 'yes' === settings.premium_ihover_link_whole && 'yes' === settings.premium_ihover_thumbnail_link_switcher ) { #>
						</a>
					<# } #>
				</div>
			</div>
		</div>

		<?php
	}
}
