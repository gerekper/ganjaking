<?php
/**
 * Class: Premium_Image_Accordion
 * Name: Image Accordion
 * Slug: premium-image-accordion
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
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
 * Class Premium_Image_Accordion
 */
class Premium_Image_Accordion extends Widget_Base {

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
		return 'premium-image-accordion';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Image Accordion', 'premium-addons-pro' ) );
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
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-image-accordion';
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
		return array(
			'premium-elements',
		);
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
		return array( 'image', 'photo', 'visual', 'slide' );
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
	 * Register Image Accordion controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Accordion', 'premium-addons-pro' ),
			)
		);

		$accordion_repeater = new Repeater();

		$accordion_repeater->add_control(
			'image',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}::before, {{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-background' => 'background-image: url("{{URL}}");',
				),
			)
		);

		$accordion_repeater->add_responsive_control(
			'image_size',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'auto'    => __( 'Auto', 'premium-addons-pro' ),
					'contain' => __( 'Contain', 'premium-addons-pro' ),
					'cover'   => __( 'Cover', 'premium-addons-pro' ),
					'custom'  => __( 'Custom', 'premium-addons-pro' ),
				),
				'default'     => 'auto',
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}::before, {{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-background' => 'background-size: {{VALUE}}',
				),
			)
		);

		$accordion_repeater->add_responsive_control(
			'image_size_custom',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'vw' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'condition'  => array(
					'image_size' => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}::before, {{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-background' => 'background-size: {{SIZE}}{{UNIT}} auto',

				),
			)
		);

		$accordion_repeater->add_responsive_control(
			'image_position',
			array(
				'label'       => __( 'Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'center center' => __( 'Center Center', 'premium-addons-pro' ),
					'center left'   => __( 'Center Left', 'premium-addons-pro' ),
					'center right'  => __( 'Center Right', 'premium-addons-pro' ),
					'top center'    => __( 'Top Center', 'premium-addons-pro' ),
					'top left'      => __( 'Top Left', 'premium-addons-pro' ),
					'top right'     => __( 'Top Right', 'premium-addons-pro' ),
					'bottom center' => __( 'Bottom Center', 'premium-addons-pro' ),
					'bottom left'   => __( 'Bottom Left', 'premium-addons-pro' ),
					'bottom right'  => __( 'Bottom Right', 'premium-addons-pro' ),
				),
				'default'     => 'center center',
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}::before, {{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-background' => 'background-position: {{VALUE}}',
				),
			)
		);

		$accordion_repeater->add_responsive_control(
			'image_repeat',
			array(
				'label'       => __( 'Repeat', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'repeat'    => __( 'Repeat', 'premium-addons-pro' ),
					'no-repeat' => __( 'No-repeat', 'premium-addons-pro' ),
					'repeat-x'  => __( 'Repeat-x', 'premium-addons-pro' ),
					'repeat-y'  => __( 'Repeat-y', 'premium-addons-pro' ),
				),
				'default'     => 'repeat',
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}::before, {{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-background' => 'background-repeat: {{VALUE}}',
				),
			)
		);

		$accordion_repeater->add_control(
			'content_switcher',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$condition = array( 'content_switcher' => 'yes' );

		$accordion_repeater->add_control(
			'content_type',
			array(
				'label'     => __( 'Content Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'custom'   => __( 'Custom Content', 'premium-addons-pro' ),
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'   => 'custom',
				'condition' => array(
					'content_switcher' => 'yes',
				),
			)
		);

		$accordion_repeater->add_control(
			'temp_content',
			array(
				'label'       => __( 'Elementor Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'label_block' => true,
				'condition'   => array(
					'content_switcher' => 'yes',
					'content_type'     => 'template',
				),
			)
		);

		$accordion_repeater->add_control(
			'icon_switcher',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array_merge(
					array(
						'content_type' => 'custom',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon'      => __( 'Icon', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
				),
				'default'   => 'icon',
				'condition' => array(
					'content_type'     => 'custom',
					'content_switcher' => 'yes',
					'icon_switcher'    => 'yes',
				),
			)
		);

		$accordion_repeater->add_control(
			'icon_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'label_block'      => true,
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array_merge(
					array(
						'content_type'  => 'custom',
						'icon_switcher' => 'yes',
						'icon_type'     => 'icon',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array_merge(
					array(
						'icon_switcher' => 'yes',
						'icon_type'     => 'animation',
						'content_type'  => 'custom',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array_merge(
					array(
						'content_type'  => 'custom',
						'icon_switcher' => 'yes',
						'icon_type'     => 'animation',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array_merge(
					array(
						'content_type'  => 'custom',
						'icon_switcher' => 'yes',
						'icon_type'     => 'animation',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'image_title',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'condition' => array_merge(
					array(
						'content_type' => 'custom',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'image_desc',
			array(
				'label'     => __( 'Description', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'dynamic'   => array( 'active' => true ),
				'condition' => array_merge(
					array(
						'content_type' => 'custom',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'custom_position',
			array(
				'label'     => __( 'Custom Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array_merge( array(), $condition ),
			)
		);

		$accordion_repeater->add_responsive_control(
			'hor_offset',
			array(
				'label'       => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-content' => 'position: absolute; left: {{SIZE}}{{UNIT}}',
				),
				'condition'   => array(
					'custom_position' => 'yes',
				),
			)
		);

		$accordion_repeater->add_responsive_control(
			'ver_offset',
			array(
				'label'       => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-accordion-content' => 'position: absolute; top: {{SIZE}}{{UNIT}}',
				),
				'condition'   => array(
					'custom_position' => 'yes',
				),
			)
		);

		$accordion_repeater->add_control(
			'link_switcher',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Add a custom link or select an existing page link', 'premium-addons-pro' ),
				'condition'   => array_merge( array(), $condition ),
			)
		);

		$accordion_repeater->add_control(
			'link_type',
			array(
				'label'       => __( 'Link Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array_merge(
					array(
						'link_switcher' => 'yes',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://premiumaddons.com/',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array_merge(
					array(
						'link_switcher' => 'yes',
						'link_type'     => 'url',
					),
					$condition
				),
			)
		);

		$accordion_repeater->add_control(
			'existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array_merge(
					array(
						'link_switcher' => 'yes',
						'link_type'     => 'link',
					),
					$condition
				),
				'label_block' => true,
			)
		);

		$accordion_repeater->add_control(
			'link_title',
			array(
				'label'       => __( 'Link Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array_merge(
					array(
						'link_switcher' => 'yes',
					),
					$condition
				),
				'label_block' => true,
			)
		);

		$accordion_repeater->add_control(
			'link_whole',
			array(
				'label'     => __( 'Whole Image Link', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array_merge(
					array(
						'link_switcher' => 'yes',
					),
					$condition
				),
			)
		);

		$this->add_control(
			'image_content',
			array(
				'label'       => __( 'Images', 'premium-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'image_title' => 'Image #1',
					),
					array(
						'image_title' => 'Image #2',
					),
				),
				'fields'      => $accordion_repeater->get_controls(),
				'title_field' => '{{{ image_title }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'display_settings',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'default_active',
			array(
				'label'       => __( 'Hovered By Default Index', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the index for the image to be hovered by default on page load, index starts from 1', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'active_img_size',
			array(
				'label'       => __( 'Hovered Image Size (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-accordion-horizontal .premium-accordion-li-active' => 'width: {{SIZE}}% !important',
				),

			)
		);

		$this->add_control(
			'direction_type',
			array(
				'label'       => __( 'Direction', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'horizontal',
				'options'     => array(
					'horizontal' => __( 'Horizontal', 'premium-addons-pro' ),
					'vertical'   => __( 'Vertical', 'premium-addons-pro' ),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'skew',
			array(
				'label'        => __( 'Skew Images', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'direction_type' => 'horizontal',
				),
			)
		);

		$this->add_control(
			'skew_direction',
			array(
				'label'       => __( 'Skew Direction', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'right',
				'options'     => array(
					'right' => __( 'Right', 'premium-addons-pro' ),
					'left'  => __( 'Left', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'condition'   => array(
					'direction_type' => 'horizontal',
					'skew'           => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'       => __( 'Image Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', 'vh' ),
				'render_type' => 'template',
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-li' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'content_position',
			array(
				'label'     => __( 'Content Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-circle-up',
					),
					'center'     => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-circle-down',
					),
				),
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-overlay-wrap' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'                => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
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
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'default'              => 'center',
				'toggle'               => false,
				'render_type'          => 'template',
				'selectors'            => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-overlay-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hide_description_thresold',
			array(
				'label'       => __( 'Hide Description Below Width (PX)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set screen width below which the description will be hidden', 'premium-addons-pro' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$title = __( 'Getting started »', 'premium-addons-for-elementor' );

		$doc_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/image-accordion-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'image_style',
			array(
				'label' => __( 'Images', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_background',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-overlay-wrap'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_hover_background',
			array(
				'label'     => __( 'Overlay Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-li:hover .premium-accordion-overlay-wrap'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$padding = is_rtl() ? 'left' : 'right';

		$this->add_responsive_control(
			'image_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-horizontal:not(.premium-accordion-skew) .premium-accordion-li:not(:last-child)' => 'padding-' . $padding . ': {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-accordion-horizontal:not(.premium-accordion-skew) .premium-accordion-li:not(:last-child) .premium-accordion-overlay-wrap ' => 'width: calc(100% - {{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .premium-accordion-skew .premium-accordion-ul' => 'border-spacing: {{SIZE}}{{UNIT}} 0;',
					'{{WRAPPER}} .premium-accordion-vertical .premium-accordion-li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_spacing_color',
			array(
				'label'     => __( 'Spacer Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-ul' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs( 'images_tabs' );

		$this->start_controls_tab(
			'image_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_normal',
				'selector' => '{{WRAPPER}} .premium-accordion-ul li.premium-accordion-li',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_tab_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .premium-accordion-ul:hover li.premium-accordion-li:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'style_settings',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'icons_active_tabs' );

		$this->start_controls_tab(
			'icons_style_section',
			array(
				'label' => __( 'Icon', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
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
					'{{WRAPPER}} i.premium-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-accordion-content > svg, {{WRAPPER}} .premium-lottie-animation' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-accordion-content > svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-icon:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-accordion-content > svg:hover' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-icon, {{WRAPPER}} .premium-accordion-content > svg' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_hover_color',
			array(
				'label'     => __( 'Background Hover Color ', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-icon:hover, {{WRAPPER}} .premium-accordion-content > svg:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .premium-accordion-icon, {{WRAPPER}} .premium-accordion-content > svg',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .premium-accordion-section .premium-accordion-icon, {{WRAPPER}} .premium-accordion-content > svg',
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-icon, {{WRAPPER}} .premium-accordion-content > svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-icon, {{WRAPPER}} .premium-accordion-content > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-icon, {{WRAPPER}} .premium-accordion-content > svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'titles_style_section',
			array(
				'label' => __( 'Title', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-accordion-section .premium-accordion-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'selector' => '{{WRAPPER}} .premium-accordion-section .premium-accordion-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'descriptions_style_section',
			array(
				'label' => __( 'Description', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-accordion-section .premium-accordion-description',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'description_shadow',
				'selector' => '{{WRAPPER}} .premium-accordion-section .premium-accordion-description',
			)
		);

		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'description_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_section();

		$this->start_controls_section(
			'Link_style',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-item-link-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-accordion-section .premium-accordion-item-link-title:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-accordion-section .premium-accordion-item-link-title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'container_style',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .premium-accordion-section',
			)
		);

		$this->add_control(
			'container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_shadow',
				'selector' => '{{WRAPPER}} .premium-accordion-section',
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-accordion-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Image Accordion widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$accordion_settings = array(
			'hide_desc'     => $settings['hide_description_thresold'],
			'dir'           => $settings['direction_type'],
			'imgSize'       => array(
				'desktop' => $settings['active_img_size']['size'],
				'tablet'  => $settings['active_img_size_tablet']['size'],
				'mobile'  => $settings['active_img_size_mobile']['size'],
			),
			'initialHeight' => array(
				'desktop' => $settings['height']['size'],
				'tablet'  => $settings['height_tablet']['size'],
				'mobile'  => $settings['height_mobile']['size'],
			),
		);

		$direction = 'premium-accordion-' . $settings['direction_type'];

		$this->add_render_attribute( 'accordion', 'class', 'premium-accordion-section' );

		if ( $settings['skew'] && 'horizontal' === $settings['direction_type'] ) {
			$this->add_render_attribute(
				'accordion',
				array(
					'class'     => 'premium-accordion-skew',
					'data-skew' => $settings['skew_direction'],
				)
			);

		}

		$this->add_render_attribute(
			'accordion',
			array(
				'id'            => 'premium-accordion-section-' . $id,
				'class'         => $direction,
				'data-settings' => wp_json_encode( $accordion_settings ),
			)
		);

		$this->add_render_attribute(
			'accordion_list',
			'class',
			array(
				'premium-accordion-ul',
				'premium-accordion-' . $settings['content_position'],
			)
		);

		$this->add_render_attribute( 'content', 'class', array( 'premium-accordion-content', 'premium-accordion-' . $settings['content_align'] ) );

		?>
			<div class="premium-accordion-container">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'accordion' ) ); ?>>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'accordion_wrap' ) ); ?>>
						<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'accordion_list' ) ); ?>>
							<?php
							foreach ( $settings['image_content'] as $index => $item ) :

								if ( 'custom' === $item['content_type'] ) {

									$title = $this->get_repeater_setting_key( 'image_title', 'image_content', $index );

									$description = $this->get_repeater_setting_key( 'image_desc', 'image_content', $index );

									$this->add_render_attribute( $title, 'class', 'premium-accordion-title' );

									$this->add_inline_editing_attributes( $title, 'none' );

									$this->add_render_attribute( $description, 'class', 'premium-accordion-description' );

									$this->add_inline_editing_attributes( $description, 'basic' );

									if ( 'yes' === $item['content_switcher'] && 'yes' === $item['icon_switcher'] ) {
										if ( 'animation' === $item['icon_type'] ) {

											$lottie_key = 'icon_lottie_' . $index;

											$this->add_render_attribute(
												$lottie_key,
												array(
													'class' => array(
														'premium-accordion-icon',
														'premium-lottie-animation',
													),
													'data-lottie-url' => $item['lottie_url'],
													'data-lottie-loop' => $item['lottie_loop'],
													'data-lottie-reverse' => $item['lottie_reverse'],
												)
											);
										}
									}
								}

								$list_item_key = 'img_index_' . $index;

								$this->add_render_attribute(
									$list_item_key,
									'class',
									array(
										'premium-accordion-li',
										'elementor-repeater-item-' . $item['_id'],
									)
								);

								if ( ! empty( $settings['default_active'] ) && ( $settings['default_active'] - 1 ) === $index ) {

									$this->add_render_attribute( $list_item_key, 'class', 'premium-accordion-li-active' );

								}

								$item_link = 'link_' . $index;

								$link_type = $item['link_type'];

								$link_url = ( 'url' === $link_type ) ? $item['link']['url'] : get_permalink( $item['existing_link'] );

								if ( 'yes' === $item['link_switcher'] ) {

									$this->add_render_attribute( $item_link, 'class', 'premium-accordion-item-link' );

									if ( ! empty( $item['link']['is_external'] ) ) {
										$this->add_render_attribute( $item_link, 'target', '_blank' );
									}

									if ( ! empty( $item['link']['nofollow'] ) ) {
										$this->add_render_attribute( $item_link, 'rel', 'nofollow' );
									}

									if ( ! empty( $item['link_title'] ) ) {
										$this->add_render_attribute( $item_link, 'title', $item['link_title'] );
									}

									if ( ! empty( $item['link']['url'] ) || ! empty( $item['existing_link'] ) ) {
										$this->add_render_attribute( $item_link, 'href', $link_url );
									}
								}

								?>

								<li <?php echo wp_kses_post( $this->get_render_attribute_string( $list_item_key ) ); ?>>
									<?php if ( ! $settings['skew'] || 'vertical' === $settings['direction_type'] ) : ?>
										<div class="premium-accordion-background"></div>
									<?php endif; ?>

									<?php if ( 'yes' === $item['link_switcher'] && 'yes' === $item['link_whole'] ) : ?>
										<a <?php echo wp_kses_post( $this->get_render_attribute_string( $item_link ) ); ?>></a>
									<?php endif ?>

									<div class="premium-accordion-overlay-wrap">
										<?php if ( 'yes' === $item['content_switcher'] ) : ?>
											<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content' ) ); ?>>
												<?php if ( 'template' === $item['content_type'] ) : ?>
													<?php echo $this->getTemplateInstance()->get_template_content( $item['temp_content'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												<?php else : ?>
													<?php
													if ( 'yes' === $item['icon_switcher'] ) :
														if ( 'icon' === $item['icon_type'] ) :

															$icon_migrated = isset( $item['__fa4_migrated']['icon_updated'] );
															$icon_new      = empty( $item['icon'] ) && Icons_Manager::is_migration_allowed();

															if ( $icon_new || $icon_migrated ) :
																Icons_Manager::render_icon(
																	$item['icon_updated'],
																	array(
																		'class'       => 'premium-accordion-icon',
																		'aria-hidden' => 'true',
																	)
																);
															else :
																?>
															<i class="<?php echo wp_kses_post( $item['icon'] ); ?>"></i>
															<?php endif; ?>
														<?php else : ?>
														<div <?php echo wp_kses_post( $this->get_render_attribute_string( $lottie_key ) ); ?>></div>
													<?php endif ?>
													<?php endif; ?>

													<?php if ( ! empty( $item['image_title'] ) ) : ?>
														<h3 <?php echo wp_kses_post( $this->get_render_attribute_string( $title ) ); ?>>
															<?php echo wp_kses_post( $item['image_title'] ); ?>
														</h3>
													<?php endif ?>

													<?php if ( ! empty( $item['image_desc'] ) ) : ?>
														<div <?php echo wp_kses_post( $this->get_render_attribute_string( $description ) ); ?>>
															<?php echo wp_kses_post( $item['image_desc'] ); ?>
														</div>
													<?php endif; ?>

												<?php endif; ?>

												<?php if ( 'yes' === $item['link_switcher'] && 'yes' !== $item['link_whole'] ) : ?>
													<a class="premium-accordion-item-link-title" <?php echo wp_kses_post( $this->get_render_attribute_string( $item_link ) ); ?>>
														<?php echo wp_kses_post( $item['link_title'] ); ?>
													</a>
												<?php endif; ?>

											</div>
										<?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Render Image Accordion widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {

		?>
		<#

			var accordionSetting = {},
				imgSize = {
					'desktop' : settings.active_img_size.size,
					'tablet'  : settings.active_img_size_tablet.size,
					'mobile'  : settings.active_img_size_mobile.size
				};

				initialHeight = {
					'desktop' : settings.height.size,
					'tablet'  : settings.height_tablet.size,
					'mobile'  : settings.height_mobile.size
				};

				accordionSetting.hide_desc = settings.hide_description_thresold;
				accordionSetting.dir = settings.direction_type;
				accordionSetting.imgSize = imgSize;
				accordionSetting.initialHeight = initialHeight;

			var direction = 'premium-accordion-' + settings.direction_type;

			view.addRenderAttribute('accordion', {
				'id': 'premium-accordion-section-'+ view.getIDInt(),
				'class': 'premium-accordion-section',
				'data-settings': JSON.stringify( accordionSetting )
			});

			if( settings.skew && 'horizontal' === settings.direction_type ) {
				view.addRenderAttribute('accordion', {
					'class': 'premium-accordion-skew',
					'data-skew': settings.skew_direction
				});
			}

			view.addRenderAttribute('accordion_wrap', 'class', direction );

			view.addRenderAttribute('accordion_list', 'class', [
				'premium-accordion-ul',
				'premium-accordion-' + settings.content_position
			] );

			view.addRenderAttribute('content', 'class', [ 'premium-accordion-content', 'premium-accordion-' + settings.content_align ] );

		#>
			<div class="premium-accordion-container">
				<div {{{ view.getRenderAttributeString( 'accordion' ) }}}>
					<div {{{ view.getRenderAttributeString( 'accordion_wrap' ) }}}>
						<ul {{{ view.getRenderAttributeString( 'accordion_list' ) }}}>
							<#
							_.each( settings.image_content, function( item , index ) {

								if ( 'custom' === item.content_type) {
									var title       = view.getRepeaterSettingKey( 'image_title', 'image_content', index );
									var description = view.getRepeaterSettingKey( 'image_desc', 'image_content', index );

									view.addRenderAttribute( title, 'class', 'premium-accordion-title' );
									view.addInlineEditingAttributes( title,'none' );

									view.addRenderAttribute( description, 'class', 'premium-accordion-description' );
									view.addInlineEditingAttributes( description, 'basic' );

									if ( item.content_switcher === 'yes' && item.icon_switcher === 'yes' ) {
										if( item.icon_type === 'animation' ) {

											var lottieKey = 'icon_lottie_' + index;

											view.addRenderAttribute( lottieKey, {
												'class': [ 'premium-accordion-icon', 'premium-lottie-animation' ],
												'data-lottie-url': item.lottie_url,
												'data-lottie-loop': item.lottie_loop,
												'data-lottie-reverse': item.lottie_reverse
											});

										}
									}
								}

								var itemLink = 'link_' + index;
								var linkType, linkUrl, linkTitle;

								linkType = item.link_type;
								linkTitle = item.link_title;
								linkUrl= 'url' ===  linkType  ? item.link.url : item.existing_link;

								if( 'yes' === item.link_switcher ) {
									view.addRenderAttribute(itemLink, 'class', 'premium-accordion-item-link');
									if( '' != item.link.is_external ) {
										view.addRenderAttribute(itemLink, 'target', '_blank');
									}
									if( '' != item.link.nofollow ) {
										view.addRenderAttribute(itemLink, 'rel', 'nofollow');
									}
									if( '' != item.link_title ) {
										view.addRenderAttribute(itemLink, 'title', linkTitle);
									}
									if( ('' != item.link.url) || ('' != item.existing_link) ) {
										view.addRenderAttribute(itemLink, 'href', linkUrl);
									}
								}

								var listItemKey = 'img_index_' + index;

								view.addRenderAttribute( listItemKey, 'class',
									[
										'premium-accordion-li' ,
										'elementor-repeater-item-' + item._id
									]
								);

								if ( '' !== settings.default_active && ( settings.default_active - 1 ) === index ) {

									view.addRenderAttribute( listItemKey, 'class', 'premium-accordion-li-active' );

								}

							#>
							<li {{{ view.getRenderAttributeString( listItemKey ) }}}>
								<# if( ! settings.skew || 'vertical' === settings.direction_type ) { #>
									<div class="premium-accordion-background"></div>
								<# } #>

								<# if( item.link_switcher === 'yes' && item.link_whole === 'yes' ) { #>
									<a {{{ view.getRenderAttributeString( itemLink ) }}}></a>
								<# } #>

								<div class="premium-accordion-overlay-wrap">
									<# if ( item.content_switcher === 'yes' ) { #>
										<div {{{ view.getRenderAttributeString( 'content' ) }}}>
											<# if ( 'template' === item.content_type ) { #>
												<div class="premium-accord-temp" data-template="{{item.temp_content}}"></div>
											<# } else { #>
												<# if( item.icon_switcher === 'yes' ) {
													if( item.icon_type === 'icon' ) {

														var listIconHTML = elementor.helpers.renderIcon( view, item.icon_updated, { 'class': 'premium-accordion-icon', 'aria-hidden': true }, 'i' , 'object' ),
															listIconMigrated = elementor.helpers.isIconMigrated( item, 'icon_updated' );

														if ( listIconHTML && listIconHTML.rendered && ( ! item.icon || listIconMigrated ) ) { #>
															{{{ listIconHTML.value }}}
														<# } else { #>
															<i class="premium-accordion-icon {{ item.icon }}" aria-hidden="true"></i>
														<# } #>
													<# } else { #>
														<div {{{ view.getRenderAttributeString( lottieKey ) }}}></div>
													<# } #>
												<# } #>

												<# if( '' != item.image_title ) { #>
													<h3 {{{ view.getRenderAttributeString( title ) }}} >{{{item.image_title}}}</h3>
												<# } #>

												<# if( '' != item.image_desc ) { #>
													<div  {{{ view.getRenderAttributeString( description ) }}}>{{{item.image_desc}}}</div>
												<# } #>

											<# } #>

											<# if( 'yes' === item.link_switcher && 'yes' !== item.link_whole ) { #>
												<a class="premium-accordion-item-link-title" {{{ view.getRenderAttributeString( itemLink ) }}}>
													{{{item.link_title}}}
												</a>
											<# } #>

										</div>
									<# } #>
								</div>

							</li>
							<# }) #>
						</ul>
					</div>
				</div>
			</div>
		<?php
	}
}
