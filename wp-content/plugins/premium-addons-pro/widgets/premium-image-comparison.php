<?php
/**
 * Class: Premium_Image_Comparison
 * Name: Image Comparison
 * Slug: premium-addon-image-comparison
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Premium_Image_Comparison
 */
class Premium_Image_Comparison extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-image-comparison';
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
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Image Comparison', 'premium-addons-pro' );
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
		return 'pa-pro-image-comparison';
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
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
			'event-move',
			'pa-imgcompare',
			'imagesloaded',
			'premium-pro',
		);
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
		return array( 'pa', 'premium', 'compare', 'before', 'after', 'slider' );
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
	 * Register Image Comparison controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_img_compare_original_image_section',
			array(
				'label' => __( 'Original Image', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_image_comparison_original_image',
			array(
				'label'       => __( 'Choose Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'It\'s recommended to use two images that have the same size', 'premium-addons-pro' ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_img_compare_original_img_label_switcher',
			array(
				'label'   => __( 'Label', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_img_compare_original_img_label',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Before', 'premium-addons-pro' ),
				'placeholder' => 'Before',
				'condition'   => array(
					'premium_img_compare_original_img_label_switcher'  => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_img_compare_original_hor_label_position',
			array(
				'label'     => __( 'Horizontal Position', 'premium-addons-pro' ),
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
				'condition' => array(
					'premium_img_compare_original_img_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'vertical',
				),
				'default'   => 'center',
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_original_label_horizontal_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'condition'  => array(
					'premium_img_compare_original_img_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'horizontal',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-img-compare-horizontal .premium-twentytwenty-before-label' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_img_compare_original_label_position',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'condition' => array(
					'premium_img_compare_original_img_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'horizontal',
				),
				'default'   => 'middle',
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_original_label_vertical_offset',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'condition'  => array(
					'premium_img_compare_original_img_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'vertical',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-img-compare-vertical .premium-twentytwenty-before-label' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_image_comparison_modified_image_section',
			array(
				'label' => __( 'Modified Image', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_image_comparison_modified_image',
			array(
				'label'       => __( 'Choose Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'It\'s recommended to use two images that have the same size', 'premium-addons-pro' ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_image_comparison_modified_image_label_switcher',
			array(
				'label'   => __( 'Label', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_image_comparison_modified_image_label',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'After',
				'default'     => __( 'After', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_image_comparison_modified_image_label_switcher'  => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_img_compare_modified_hor_label_position',
			array(
				'label'     => __( 'Horizontal Position', 'premium-addons-pro' ),
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
				'condition' => array(
					'premium_image_comparison_modified_image_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'vertical',
				),
				'default'   => 'center',
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_modified_label_horizontal_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'condition'  => array(
					'premium_image_comparison_modified_image_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'horizontal',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-img-compare-horizontal .premium-twentytwenty-after-label' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_img_compare_modified_label_position',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'condition' => array(
					'premium_image_comparison_modified_image_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'horizontal',
				),
				'default'   => 'middle',
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_modified_label_vertical_offset',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'condition'  => array(
					'premium_image_comparison_modified_image_label_switcher' => 'yes',
					'premium_image_comparison_orientation' => 'vertical',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-img-compare-vertical .premium-twentytwenty-after-label' => 'bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_img_compare_display_options',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'prmium_img_compare_images_size',
				'default' => 'full',
			)
		);

		$this->add_control(
			'premium_image_comparison_orientation',
			array(
				'label'        => __( 'Orientation', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'horizontal' => __( 'Vertical', 'premium-addons-pro' ),
					'vertical'   => __( 'Horizontal', 'premium-addons-pro' ),
				),
				'prefix_class' => 'premium-img-compare-',
				'default'      => 'horizontal',
				'label_block'  => true,
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'premium_img_compare_visible_ratio',
			array(
				'label'       => __( 'Visible Ratio', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'This option specifies the area of the original image that is visible by default.', 'premium-addons-pro' ),
				'default'     => 0.5,
				'min'         => 0,
				'step'        => 0.1,
				'max'         => 1,
			)
		);

		$this->add_control(
			'premium_image_comparison_add_drag_handle',
			array(
				'label'       => __( 'Show Drag Handle', 'premium-addons-pro' ),
				'description' => __( 'Show drag handle between the images', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'label_on'    => 'Show',
				'label_off'   => 'Hide',
				'condition'   => array(
					'magic_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_add_separator',
			array(
				'label'       => __( 'Show Separator', 'premium-addons-pro' ),
				'description' => __( 'Show separator between the images', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => 'Show',
				'label_off'   => 'Hide',
				'condition'   => array(
					'premium_image_comparison_add_drag_handle' => 'yes',
					'magic_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_interaction_mode',
			array(
				'label'       => __( 'Interaction Mode', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'mousemove' => __( 'Mouse Move', 'premium-addons-pro' ),
					'drag'      => __( 'Mouse Drag', 'premium-addons-pro' ),
					'click'     => __( 'Mouse Click', 'premium-addons-pro' ),
				),
				'default'     => 'mousemove',
				'label_block' => true,
				'condition'   => array(
					'magic_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_overlay',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',

			)
		);

		$this->add_control(
			'magic_scroll',
			array(
				'label' => __( 'Use With Magic Scroll', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'magic_scroll_rev',
			array(
				'label'     => __( 'Reverse Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'magic_scroll' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/premium-image-comparison-widget/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-pro' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_img_compare_original_img_label_style_tab',
			array(
				'label'     => __( 'First Label', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_img_compare_original_img_label_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_original_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-before-label span'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_image_comparison_original_label_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-twentytwenty-before-label span',
			)
		);

		$this->add_control(
			'premium_image_comparison_original_label_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-before-label span'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_image_comparison_original_label_border',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-before-label span',
			)
		);

		$this->add_control(
			'premium_image_comparison_original_label_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twentytwenty-before-label span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_image_comparison_original_label_box_shadow',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-before-label span',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_image_comparison_original_label_text_shadow',
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twentytwenty-before-label span',
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_original_label_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twentytwenty-before-label span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_image_comparison_modified_image_label_style_tab',
			array(
				'label'     => __( 'Second Label', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_image_comparison_modified_image_label_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_modified_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-after-label span'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_image_comparison_modified_label_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-twentytwenty-after-label span',
			)
		);

		$this->add_control(
			'premium_image_comparison_modified_label_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-after-label span'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_image_comparison_modified_label_border',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-after-label span',
			)
		);

		$this->add_control(
			'premium_image_comparison_modified_label_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twentytwenty-after-label span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_image_comparison_modified_label_box_shadow',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-after-label span',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_image_comparison_modified_label_text_shadow',
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-twentytwenty-after-label span',
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_modified_label_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twentytwenty-after-label span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_image_comparison_drag_style_settings',
			array(
				'label'     => __( 'Drag', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_image_comparison_add_drag_handle'  => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_drag_width',
			array(
				'label'       => __( 'Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Enter Drag width in (PX), default is 50px', 'premium-addons-pro' ),
				'size_units'  => array( 'px', 'em' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-twentytwenty-handle' => 'width:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_drag_height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'description' => __( 'Enter Drag height in (PX), default is 50px', 'premium-addons-pro' ),
				'size_units'  => array( 'px', 'em' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-twentytwenty-handle' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_drag_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-handle'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_image_comparison_drag_border',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-handle',
			)
		);

		$this->add_control(
			'premium_image_comparison_drag_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twentytwenty-handle' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_image_comparison_drag_box_shadow',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-handle',
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_drag_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-twentytwenty-handle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_image_comparison_arrow_style',
			array(
				'label'     => __( 'Arrows', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_image_comparison_add_drag_handle'  => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_arrows_size',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-twentytwenty-left-arrow' => 'border: {{SIZE}}px inset transparent; border-right: {{SIZE}}px solid; margin-top: -{{size}}px',
					'{{WRAPPER}} .premium-twentytwenty-right-arrow' => 'border: {{SIZE}}px inset transparent; border-left: {{SIZE}}px solid; margin-top: -{{size}}px',
					'{{WRAPPER}} .premium-twentytwenty-down-arrow' => 'border: {{SIZE}}px inset transparent; border-top: {{SIZE}}px solid; margin-left: -{{size}}px',
					'{{WRAPPER}} .premium-twentytwenty-up-arrow' => 'border: {{SIZE}}px inset transparent; border-bottom: {{SIZE}}px solid; margin-left: -{{size}}px',
				),
			)
		);

		$this->add_control(
			'premium_image_comparison_arrows_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .premium-twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .premium-twentytwenty-down-arrow' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .premium-twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_img_compare_separator_style_settings',
			array(
				'label'     => __( 'Separator', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_image_comparison_add_drag_handle'  => 'yes',
					'premium_image_comparison_add_separator'    => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_img_compare_separator_background_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-handle:after, {{WRAPPER}} .premium-twentytwenty-handle:before'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_separator_spacing',
			array(
				'label'       => __( 'Spacing', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.premium-img-compare-horizontal .premium-twentytwenty-handle:after' => 'top: {{SIZE}}%;',
					'{{WRAPPER}}.premium-img-compare-horizontal .premium-twentytwenty-handle:before' => 'bottom: {{SIZE}}%;',
					'{{WRAPPER}}.premium-img-compare-vertical .premium-twentytwenty-handle:after' => 'right: {{SIZE}}%;',
					'{{WRAPPER}}.premium-img-compare-vertical .premium-twentytwenty-handle:before' => 'left: {{SIZE}}%;',
				),
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_separator_width',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.premium-img-compare-vertical .premium-twentytwenty-handle:before,{{WRAPPER}}.premium-img-compare-vertical .premium-twentytwenty-handle:after' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'premium_image_comparison_add_drag_handle' => 'yes',
					'premium_image_comparison_add_separator' => 'yes',
					'premium_image_comparison_orientation' => 'vertical',
				),
			)
		);

		$this->add_responsive_control(
			'premium_img_compare_separator_height',
			array(
				'label'       => __( 'Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.premium-img-compare-horizontal .premium-twentytwenty-handle:after,{{WRAPPER}}.premium-img-compare-horizontal .premium-twentytwenty-handle:before' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'premium_image_comparison_add_drag_handle' => 'yes',
					'premium_image_comparison_add_separator' => 'yes',
					'premium_image_comparison_orientation' => 'horizontal',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_img_compare_separator_shadow',
				'selector' => '{{WRAPPER}} .premium-twentytwenty-handle:after,{{WRAPPER}} .premium-twentytwenty-handle:before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_image_comparison_contents_wrapper_style_settings',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_image_comparison_overlay_background',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-twentytwenty-overlay.premium-twentytwenty-show:hover'  => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_image_comparison_overlay' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_image_comparison_contents_wrapper_border',
				'selector' => '{{WRAPPER}} .premium-images-compare-container',
			)
		);

		$this->add_control(
			'premium_image_comparison_contents_wrapper_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-images-compare-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_image_comparison_contents_wrapper_box_shadow',
				'selector' => '{{WRAPPER}} .premium-images-compare-container',
			)
		);

		$this->add_responsive_control(
			'premium_image_comparison_contents_wrapper_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-images-compare-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Image Comparison widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$original_image = $settings['premium_image_comparison_original_image'];

		$modified_image = $settings['premium_image_comparison_modified_image'];

		$original_image_src = Group_Control_Image_Size::get_attachment_image_src( $original_image['id'], 'prmium_img_compare_images_size', $settings );

		$original_image_src = empty( $original_image_src ) ? $original_image['url'] : $original_image_src;

		$modified_image_src = Group_Control_Image_Size::get_attachment_image_src( $modified_image['id'], 'prmium_img_compare_images_size', $settings );

		$modified_image_src = empty( $modified_image_src ) ? $modified_image['url'] : $modified_image_src;

		$img_compare_setttings = array(
			'orientation'  => $settings['premium_image_comparison_orientation'],
			'visibleRatio' => ! empty( $settings['premium_img_compare_visible_ratio'] ) || '0' == $settings['premium_img_compare_visible_ratio'] ? $settings['premium_img_compare_visible_ratio'] : 0.1,
			'switchBefore' => ( 'yes' === $settings['premium_img_compare_original_img_label_switcher'] ) ? true : false,
			'beforeLabel'  => ( 'yes' === $settings['premium_img_compare_original_img_label_switcher'] && ! empty( $settings['premium_img_compare_original_img_label'] ) ) ? $settings['premium_img_compare_original_img_label'] : '',
			'switchAfter'  => ( 'yes' === $settings['premium_image_comparison_modified_image_label_switcher'] ) ? true : false,
			'afterLabel'   => ( 'yes' === $settings['premium_image_comparison_modified_image_label_switcher'] && ! empty( $settings['premium_image_comparison_modified_image_label'] ) ) ? $settings['premium_image_comparison_modified_image_label'] : '',
			'mouseMove'    => ( 'mousemove' === $settings['premium_image_comparison_interaction_mode'] ) ? true : false,
			'clickMove'    => ( 'click' === $settings['premium_image_comparison_interaction_mode'] ) ? true : false,
			'showDrag'     => ( 'yes' === $settings['premium_image_comparison_add_drag_handle'] ) ? true : false,
			'showSep'      => ( 'yes' === $settings['premium_image_comparison_add_separator'] ) ? true : false,
			'overlay'      => ( 'yes' === $settings['premium_image_comparison_overlay'] ) ? false : true,
			'beforePos'    => $settings['premium_img_compare_original_label_position'],
			'afterPos'     => $settings['premium_img_compare_modified_label_position'],
			'verbeforePos' => $settings['premium_img_compare_original_hor_label_position'],
			'verafterPos'  => $settings['premium_img_compare_modified_hor_label_position'],
		);

		if ( 'yes' === $settings['magic_scroll'] ) {

			$img_compare_setttings['reverse'] = $settings['magic_scroll_rev'];

			$this->add_render_attribute( 'image-compare', 'class', 'premium-compare-mscroll' );

		}

		$this->add_render_attribute(
			'image-compare',
			array(
				'class'         => array( 'premium-images-compare-container', 'premium-twentytwenty-container' ),
				'data-settings' => wp_json_encode( $img_compare_setttings ),
			)
		);

		$this->add_render_attribute(
			'first-image',
			array(
				'src' => $original_image_src,
				'alt' => $settings['premium_img_compare_original_img_label'],
			)
		);

		$this->add_render_attribute(
			'second-image',
			array(
				'src' => $modified_image_src,
				'alt' => $settings['premium_image_comparison_modified_image_label'],
			)
		);

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'image-compare' ) ); ?>>
			<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'first-image' ) ); ?>>
			<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'second-image' ) ); ?>>
		</div>

		<?php

	}
}
