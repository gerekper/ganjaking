<?php
/**
 * Class: Premium_Color_Transition
 * Name: Color Transition
 * Slug: premium-color-transition
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Color_Transition
 */
class Premium_Color_Transition extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-color-transition';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Background Transition', 'premium-addons-pro' );
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
		return 'pa-pro-color-transition';
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
			'elementor-waypoints',
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
		return array( 'pa', 'premium', 'color', 'scroll', 'background' );
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
	 * Register Background Transition controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'sections',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'offset_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Important: Please note that Offset option works only when Change Color As Gradient option is disabled.', 'premium-addons-pro' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$id_repeater = new REPEATER();

		$id_repeater->add_control(
			'section_id',
			array(
				'label'   => __( 'CSS ID', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$id_repeater->start_controls_tabs( 'colors' );

		$id_repeater->start_controls_tab(
			'scroll_down',
			array(
				'label' => sprintf( '<i class="eicon-arrow-down premium-editor-icon"></i>%s', __( 'Scroll Down', 'premium-addons-pro' ) ),
			)
		);

		$id_repeater->add_control(
			'scroll_down_type',
			array(
				'label'   => __( 'Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'color' => __( 'Color', 'premium-addons-pro' ),
					'image' => __( 'Image', 'premium-addons-pro' ),
				),
				'default' => 'color',
			)
		);

		$id_repeater->add_control(
			'scroll_down_doc',
			array(
				'raw'             => __( 'This color is applied while scrolling down', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'editor-pa-doc',
			)
		);

		$id_repeater->add_control(
			'down_color',
			array(
				'label'       => __( 'Select Color', 'premium-addons-pro' ),
				'type'        => Controls_Manager::COLOR,
				'redner_type' => 'template',
				'global'      => false,
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="down"], #premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background: {{VALUE}}',
				),
				'condition'   => array(
					'scroll_down_type' => 'color',
				),
			)
		);

		$id_repeater->add_control(
			'down_image',
			array(
				'label'       => __( 'Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="down"], #premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background: transparent; background-image: url("{{URL}}")',
				),
				'condition'   => array(
					'scroll_down_type' => 'image',
				),
			)
		);

		$id_repeater->add_responsive_control(
			'down_image_size',
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
				'condition'   => array(
					'scroll_down_type' => 'image',
				),
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="down"], #premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-size: {{VALUE}}',
				),
			)
		);

		$id_repeater->add_responsive_control(
			'down_image_size_custom',
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
					'scroll_down_type' => 'image',
					'down_image_size'  => 'custom',
				),
				'selectors'  => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="down"], #premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-size: {{SIZE}}{{UNIT}} auto',

				),
			)
		);

		$id_repeater->add_responsive_control(
			'down_image_position',
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
				'condition'   => array(
					'scroll_down_type' => 'image',
				),
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="down"], #premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-position: {{VALUE}}',
				),
			)
		);

		$id_repeater->add_responsive_control(
			'down_image_repeat',
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
				'condition'   => array(
					'scroll_down_type' => 'image',
				),
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="down"], #premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-repeat: {{VALUE}}',
				),
			)
		);

		$id_repeater->add_control(
			'scroll_down_offset',
			array(
				'label'              => __( 'Offset', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'top-in-view'    => __( 'Viewport Hits Top', 'premium-addons-pro' ),
					'bottom-in-view' => __( 'Viewport Hits Bottom', 'premium-addons-pro' ),
					'custom'         => __( 'Custom Offset', 'premium-addons-pro' ),
				),
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$id_repeater->add_control(
			'scroll_down_custom_offset',
			array(
				'label'              => __( 'Offset', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', '%' ),
				'frontend_available' => true,
				'condition'          => array(
					'scroll_down_offset' => 'custom',
				),
			)
		);

		$id_repeater->end_controls_tab();

		$id_repeater->start_controls_tab(
			'scroll_up',
			array(
				'label' => sprintf( '<i class="eicon-arrow-up premium-editor-icon"></i>%s', __( 'Scroll Up', 'premium-addons-pro' ) ),
			)
		);

		$id_repeater->add_control(
			'scroll_up_doc',
			array(
				'raw'             => __( 'This color is applied while scrolling up', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'editor-pa-doc',
			)
		);

		$id_repeater->add_control(
			'scroll_up_type',
			array(
				'label'   => __( 'Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'color' => __( 'Color', 'premium-addons-pro' ),
					'image' => __( 'Image', 'premium-addons-pro' ),
				),
				'default' => 'color',
			)
		);

		$id_repeater->add_control(
			'up_color',
			array(
				'label'     => __( 'Select Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'selectors' => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'scroll_up_type' => 'color',
				),
			)
		);

		$id_repeater->add_control(
			'up_image',
			array(
				'label'       => __( 'Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background: transparent; background-image: url("{{URL}}")',
				),
				'condition'   => array(
					'scroll_up_type' => 'image',
				),
			)
		);

		$id_repeater->add_responsive_control(
			'up_image_size',
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
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-size: {{VALUE}}',
				),
				'condition'   => array(
					'scroll_up_type' => 'image',
				),
			)
		);

		$id_repeater->add_responsive_control(
			'up_image_size_custom',
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
					'scroll_up_type' => 'image',
					'up_image_size'  => 'custom',
				),
				'selectors'  => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-size: {{SIZE}}{{UNIT}} auto',

				),
			)
		);

		$id_repeater->add_responsive_control(
			'up_image_position',
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
				'condition'   => array(
					'scroll_up_type' => 'image',
				),
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-position: {{VALUE}}',
				),
			)
		);

		$id_repeater->add_responsive_control(
			'up_image_repeat',
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
				'condition'   => array(
					'scroll_up_type' => 'image',
				),
				'selectors'   => array(
					'#premium-color-transition-{{ID}} {{CURRENT_ITEM}}[data-direction="up"]' => 'background-repeat: {{VALUE}}',
				),
			)
		);

		$id_repeater->add_control(
			'scroll_up_offset',
			array(
				'label'              => __( 'Offset', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'top-in-view'    => __( 'Viewport Hits Top', 'premium-addons-pro' ),
					'bottom-in-view' => __( 'Viewport Hits Bottom ', 'premium-addons-pro' ),
					'custom'         => __( 'Custom Offset', 'premium-addons-pro' ),
				),
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$id_repeater->add_control(
			'scroll_up_custom_offset',
			array(
				'label'              => __( 'Offset', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', '%' ),
				'frontend_available' => true,
				'condition'          => array(
					'scroll_up_offset' => 'custom',
				),
			)
		);

		$id_repeater->end_controls_tab();

		$id_repeater->end_controls_tabs();

		$this->add_control(
			'id_repeater',
			array(
				'label'              => __( 'Elements', 'premium-addons-pro' ),
				'type'               => Controls_Manager::REPEATER,
				'fields'             => $id_repeater->get_controls(),
				'title_field'        => '{{{ section_id }}}',
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'gradient',
			array(
				'label'              => __( 'Change Colors As Gradient', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'This option works if only solid colors are used', 'premium-addons-pro' ),
				'return_value'       => 'true',
				'default'            => 'true',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'duration',
			array(
				'label'     => __( 'Transition Duration (sec)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'gradient!' => 'true',
				),
				'selectors' => array(
					'#premium-color-transition-{{ID}} .premium-color-transition-layer'  => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->add_responsive_control(
			'offset',
			array(
				'label'              => __( 'Offset (PX)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'Distance between the top of viewport and top of the element, default: 30', 'premium-addons-pro' ),
				'default'            => 30,
				'condition'          => array(
					'gradient!' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/background-transition-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Getting started »', 'premium-addons-pro' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Background Transition output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.7.1
	 * @access protected
	 */
	protected function render() {

		$this->add_render_attribute( 'container', 'class', 'premium-scroll-background' );

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>></div>

		<?php
	}


}
