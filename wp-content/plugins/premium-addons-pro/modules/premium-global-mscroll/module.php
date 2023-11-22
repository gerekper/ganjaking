<?php
/**
 * Class: Module
 * Name: Global Badge
 * Slug: premium-global-badge
 *
 * @since 2.7.0
 */

namespace PremiumAddonsPro\Modules\PremiumGlobalMScroll;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddonsPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Global Badge Addon.
 */
class Module extends Module_Base {

	/**
	 * Load Script
	 *
	 * @var $load_script
	 */
	private static $load_script = null;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		$modules = Admin_Helper::get_enabled_elements();

		$pa_mscroll = $modules['premium-mscroll'];

		if ( ! $pa_mscroll ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Creates Premium Global Badge tab at the end of layout/content tab.
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/documents/register_controls', array( $this, 'register_page_controls' ), 10 );

		// Editor Hooks.
		add_action( 'elementor/column/print_template', array( $this, 'print_template' ), 10, 2 );
		add_action( 'elementor/widget/print_template', array( $this, 'print_template' ), 10, 2 );

		// Frontend Hooks.
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ), 10, 1 );

		add_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public static function enqueue_scripts() {

		if ( ! wp_script_is( 'pa-tweenmax', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-tweenmax' );
		}

		if ( ! wp_script_is( 'pa-motionpath', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-motionpath' );
		}

		if ( ! wp_script_is( 'pa-scrolltrigger', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-scrolltrigger' );
		}

		if ( ! wp_script_is( 'pa-mscroll', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-mscroll' );
		}

	}

	/**
	 * Enqueue styles.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public static function enqueue_styles() {

		if ( ! wp_style_is( 'pa-global', 'enqueued' ) ) {
			wp_enqueue_style( 'pa-global' );
		}

	}

	/**
	 * Register Magic Scroll page controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_page_controls( $element ) {

		if ( Helper_Functions::check_post_type( get_the_ID() ) ) {
			return;
		}

		$element->start_controls_section(
			'refresh_page_section',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Refresh Preview Area', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$element->add_control(
			'premium_mscroll_refresh',
			array(
				'label'     => '<div class="elementor-update-preview editor-pa-preview-update"><div class="elementor-update-preview-title">Found something wrong? Click Refresh</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Refresh</button></div></div>',
				'type'      => Controls_Manager::RAW_HTML,
				'separator' => 'before',
			)
		);

		$element->add_control(
			'premium_mscroll_refresh_note',
			array(
				'raw'             => __( 'This button can be used to refresh the preview area if you are seeing something wrong while using Magic Scroll addon.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Register Magic Scroll Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_mscroll',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Premium Magic Scroll', 'premium-addons-pro' ) ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'premium_mscroll_switcher',
			array(
				'label'        => __( 'Enable Magic Scroll', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-mscroll-',
				'render_type'  => 'template',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'premium_mscroll_type',
			array(
				'label'       => __( 'Select Animation', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => array(
					'position' => array(
						'label'   => __( 'Position', 'premium-addons-pro' ),
						'options' => array(
							'translate' => __( 'Translate', 'premium-addons-pro' ),
							'rotate'    => __( 'Rotate', 'premium-addons-pro' ),
							'scale'     => __( 'Scale', 'premium-addons-pro' ),
							'skew'      => __( 'Skew', 'premium-addons-pro' ),
							'fadein'    => __( 'Fade In', 'premium-addons-pro' ),
							'fadeout'   => __( 'Fade Out', 'premium-addons-pro' ),
						),
					),

					'css'      => array(
						'label'   => __( 'CSS Properties', 'premium-addons-pro' ),
						'options' => array(
							'opacity' => __( 'Opacity', 'premium-addons-pro' ),
							'border'  => __( 'Border', 'premium-addons-pro' ),
							'padding' => __( 'Padding', 'premium-addons-pro' ),
						),
					),

					'filters'  => array(
						'label'   => __( 'CSS Filters', 'premium-addons-pro' ),
						'options' => array(
							'blur'   => __( 'Blur', 'premium-addons-pro' ),
							'gray'   => __( 'Grayscale', 'premium-addons-pro' ),
							'bright' => __( 'Brightness', 'premium-addons-pro' ),
						),
					),

					'magic'    => array(
						'label'   => __( 'More Magic', 'premium-addons-pro' ),
						'options' => array(
							'color'     => __( 'Color', 'premium-addons-pro' ),
							'backcolor' => __( 'Background Color', 'premium-addons-pro' ),
							'font'      => __( 'Font Size', 'premium-addons-pro' ),
							'spacing'   => __( 'Letter Spacing', 'premium-addons-pro' ),
							'sequence'  => __( 'Images Sequence', 'premium-addons-pro' ),
							'tshadow'   => __( 'Text Shadow', 'premium-addons-pro' ),
							'shadow'    => __( 'Box Shadow', 'premium-addons-pro' ),
							'video'     => __( 'Play Video', 'premium-addons-pro' ),
							'svg'       => __( 'Draw SVG', 'premium-addons-pro' ),
							'carousel'  => __( 'Carousel Scroll', 'premium-addons-pro' ),
							'progress'  => __( 'Progress Bar', 'premium-addons-pro' ),
							'compare'   => __( 'Image Compare', 'premium-addons-pro' ),
						),
					),

					'advanced' => array(
						'label'   => __( 'Advanced', 'premium-addons-pro' ),
						'options' => array(
							'custom' => __( 'Custom Path', 'premium-addons-pro' ),
							'class'  => __( 'CSS Class', 'premium-addons-pro' ),
						),
					),

				),
				'default'     => 'translate',
				'label_block' => true,
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_fade_dir',
			array(
				'label'     => __( 'Fade Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'up'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'right' => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-left',
					),
					'down'  => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
					'left'  => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'default'   => 'up',
				'condition' => array(
					'premium_mscroll_type' => array( 'fadein', 'fadeout' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_apply_on',
			array(
				'label'       => __( 'Apply Animation On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'scope'  => __( 'Element Container', 'premium-addons-pro' ),
					'custom' => __( 'Custom CSS Selector', 'premium-addons-pro' ),
				),
				'default'     => 'scope',
				'render_type' => 'template',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type!' => array( 'carousel', 'progress', 'compare', 'custom', 'video' ),
				),
			)
		);

		// TODO: Add a heading conditioned to SVG that user must add paths.
		$repeater->add_control(
			'premium_mscroll_selector',
			array(
				'label'       => __( 'CSS Selector', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'render_type' => 'template',
				'description' => __( 'Add the CSS selector to apply the effect on. By default, the effect is applied on the outer container for all effects except Play Video.', 'premium-addons-pro' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_mscroll_apply_on',
									'value' => 'custom',
								),
								array(
									'name'  => 'premium_mscroll_type',
									'value' => 'custom',
								),
								array(
									'name'  => 'premium_mscroll_type',
									'value' => 'video',
								),
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_custompath_note',
			array(
				'raw'             => __( 'You will need to add a CSS selector for this animation. For example, img, span, or .elementor-icon, etc.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_type' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_carousel_note',
			array(
				'raw'             => __( 'Carousel effect can be used with with <b>Premium Carousel widget</b> only.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_type' => 'carousel',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_compare_note',
			array(
				'raw'             => __( 'Image Compare effect can be used with with <b>Premium Image Comparison widget</b> only.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_type' => 'compare',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_progress_note',
			array(
				'raw'             => __( 'Progress Bar Scroll effect can be used with with <b>Premium Progressbar widget</b> only.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_type' => 'progress',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_tr_x',
			array(
				'label'       => __( 'TranslateX', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'description' => __( 'Set the x-axis position the element will be moved to.', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'     => array(
					'size' => 100,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'translate' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_carousel_x',
			array(
				'label'       => __( 'TranslateX', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -400,
						'max' => 0,
					),
					'%'  => array(
						'min' => -100,
						'max' => 0,
					),
				),
				'default'     => array(
					'size' => -200,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'carousel' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_carousel_neg_note',
			array(
				'raw'             => __( 'Make sure to use only <b>negative</b> values for TranslateX control.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'premium_mscroll_type' => 'carousel',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_rot_x',
			array(
				'label'       => __( 'RotateX', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'default'     => array(
					'size' => 0,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'rotate' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_sc_screen',
			array(
				'label'       => __( 'Scale to Section Width/Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this to scale the element until its width/height is equal to the parent section width/height.', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_mscroll_type' => array( 'scale' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_sc_xvalue',
			array(
				'label'       => __( 'Scale X', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'default'     => array(
					'size' => 1.2,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type'       => array( 'scale' ),
					'premium_mscroll_sc_screen!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_sk_xvalue',
			array(
				'label'       => __( 'Skew X', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'default'     => array(
					'size' => 0,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'skew' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_origx',
			array(
				'label'       => __( 'Transform Origin X', 'premium-addons-pro' ),
				'default'     => 'center',
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'left'   => __( 'Left', 'premium-addons-pro' ),
					'center' => __( 'Center', 'premium-addons-pro' ),
					'right'  => __( 'Right', 'premium-addons-pro' ),
					'custom' => __( 'Custom', 'premium-addons-pro' ),
				),
				'label_block' => false,
				'condition'   => array(
					'premium_mscroll_type'       => array( 'rotate', 'translate', 'scale', 'skew' ),
					'premium_mscroll_sc_screen!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_origx_custom',
			array(
				'label'       => __( 'Custom Origin', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type'       => array( 'rotate', 'translate', 'scale', 'skew' ),
					'premium_mscroll_origx'      => 'custom',
					'premium_mscroll_sc_screen!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_tr_y',
			array(
				'label'       => __( 'TranslateY (PX)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'description' => __( 'Set the y-axis position the element will be moved to.', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'separator'   => 'before',
				'default'     => array(
					'size' => 100,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'translate' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_rot_y',
			array(
				'label'       => __( 'RotateY', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'separator'   => 'before',
				'default'     => array(
					'size' => 0,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'rotate' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_sc_yvalue',
			array(
				'label'       => __( 'Scale Y', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'separator'   => 'before',
				'default'     => array(
					'size' => 1.2,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type'       => array( 'scale' ),
					'premium_mscroll_sc_screen!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_sk_yvalue',
			array(
				'label'       => __( 'Skew Y', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'default'     => array(
					'size' => 0,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'skew' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_origy',
			array(
				'label'       => __( 'Transform Origin Y', 'premium-addons-pro' ),
				'default'     => 'center',
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'center' => __( 'Center', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
					'custom' => __( 'Custom', 'premium-addons-pro' ),
				),
				'label_block' => false,
				'condition'   => array(
					'premium_mscroll_type'       => array( 'rotate', 'translate', 'scale', 'skew' ),
					'premium_mscroll_sc_screen!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_origy_custom',
			array(
				'label'       => __( 'Custom Origin', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type'       => array( 'rotate', 'translate', 'scale', 'skew' ),
					'premium_mscroll_origy'      => 'custom',
					'premium_mscroll_sc_screen!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_rot_z',
			array(
				'label'       => __( 'RotateZ', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => -360,
						'max' => 360,
					),
				),
				'default'     => array(
					'size' => 360,
					'unit' => 'px',
				),
				'separator'   => 'before',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'rotate' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_op_value',
			array(
				'label'       => __( 'Opacity', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'     => array(
					'size' => 0.5,
					'unit' => 'px',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => array( 'opacity' ),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'premium_mscroll_border',
				'selector'       => '',
				'fields_options' => array(
					'width' => array(
						'responsive' => false,
					),
				),
				'condition'      => array(
					'premium_mscroll_type' => array( 'border' ),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'premium_mscroll_sh',
				'selector'       => '',
				'condition'      => array(
					'premium_mscroll_type' => array( 'shadow' ),
				),
				'fields_options' => array(
					'box_shadow'          => array(
						'render_type' => 'template',
					),
					'box_shadow_position' => array(
						'render_type' => 'template',
					),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'           => 'premium_mscroll_tsh',
				'selector'       => '',
				'condition'      => array(
					'premium_mscroll_type' => array( 'tshadow' ),
				),
				'fields_options' => array(
					'text_shadow' => array(
						'render_type' => 'template',
					),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_border_r',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'condition'  => array(
					'premium_mscroll_type' => array( 'border' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_border_ra',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
				'condition'   => array(
					'premium_mscroll_type' => array( 'border' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_border_ra_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'premium_mscroll_border_ra' => 'yes',
					'premium_mscroll_type'      => array( 'border' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'unit'   => 'px',
					'top'    => 10,
					'right'  => 10,
					'bottom' => 10,
					'left'   => 10,
				),
				'condition'  => array(
					'premium_mscroll_type' => array( 'padding' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_blur',
			array(
				'label'     => __( 'Blur Value (px)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 15,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 1.2,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_mscroll_type' => array( 'blur' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_gscale',
			array(
				'label'     => __( 'Grayscale Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'   => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_mscroll_type' => array( 'gray' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_bright',
			array(
				'label'     => __( 'Brightness Value (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'default'   => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_mscroll_type' => array( 'bright' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_path_type',
			array(
				'label'       => __( 'Path Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'svg'    => __( 'SVG Path', 'premium-addons-pro' ),
					'points' => __( 'Custom Points', 'premium-addons-pro' ),
				),
				'default'     => 'svg',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_type' => 'custom',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_path_points',
			array(
				'label'       => __( 'Custom Points', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( 'Add multiple points. For example, {50,20},{100,-50},{200,200}', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'points',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_svg_path',
			array(
				'label'       => __( 'SVG Path', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( 'Put the SVG path code. You can create paths from ', 'premium-addons-pro' ) . '<a href="https://codepen.io/GreenSock/full/oNNEdRV" target="_blank">here</a>',
				'condition'   => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_stroke_width',
			array(
				'label'     => __( 'Stroke Thickness', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 2,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
				'selectors' => array(
					'{{CURRENT_ITEM}}.premium-mscroll-svg svg path' => 'stroke-width: {{SIZE}}',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_stroke_color',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'default'   => '#6EC1E4',
				'condition' => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
				'selectors' => array(
					'{{CURRENT_ITEM}}.premium-mscroll-svg svg path' => 'stroke: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_stroke_dashes',
			array(
				'label'     => __( 'Space Between Dashes', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 3,
					'unit' => 'px',
				),
				'condition' => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
				'selectors' => array(
					'{{CURRENT_ITEM}}.premium-mscroll-svg svg path' => 'stroke-dasharray: {{SIZE}}',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_st_x',
			array(
				'label'     => __( 'X Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					),
				),
				'condition' => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_st_y',
			array(
				'label'     => __( 'Y Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					),
				),
				'condition' => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_st_zoom',
			array(
				'label'     => __( 'Zoom', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'condition' => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_st_height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'render_type' => 'template',
				'condition'   => array(
					'premium_mscroll_type'      => 'custom',
					'premium_mscroll_path_type' => 'svg',
				),
				'selectors'   => array(
					'{{CURRENT_ITEM}}.premium-mscroll-svg svg' => 'height:  {{SIZE}}px',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_gallery',
			array(
				'label'     => __( 'Select Images', 'premium-addons-pro' ),
				'type'      => Controls_Manager::GALLERY,
				'condition' => array(
					'premium_mscroll_type' => array( 'sequence' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_color',
			array(
				'label'     => __( 'Select Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'classes'   => 'editor-pa-color-control',
				'condition' => array(
					'premium_mscroll_type' => array( 'color', 'backcolor' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_css_class',
			array(
				'label'       => __( 'CSS Class', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'test-class',
				'description' => __( 'Add a CSS class to be added to the element above. Note that the CSS class will not be removed on scroll back.', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_mscroll_type' => array( 'class' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_font',
			array(
				'label'     => __( 'Font Size (px)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'condition' => array(
					'premium_mscroll_type' => array( 'font' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_letter_spacing',
			array(
				'label'     => __( 'Letter Spacing (px)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'premium_mscroll_type' => array( 'spacing' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_video_note',
			array(
				'raw'             => __( 'Use this option to play self-hosted videos on scroll. It can be used with Premium Video Box widget -> Self Hosted option.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_type' => array( 'video' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_reverse',
			array(
				'label'     => __( 'Reverse Animation', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_mscroll_type!' => array( 'sequence', 'class', 'font', 'spacing', 'video', 'progress', 'compare', 'fadein', 'fadeout' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_draw_start',
			array(
				'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
				'default'     => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'   => array(
					'premium_mscroll_type'     => array( 'svg' ),
					'premium_mscroll_reverse!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_draw_end',
			array(
				'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
				'default'     => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'   => array(
					'premium_mscroll_type'    => array( 'svg' ),
					'premium_mscroll_reverse' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_draw_sync',
			array(
				'label'     => __( 'Draw all SVG paths together', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_mscroll_type' => array( 'svg' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_svg_fill',
			array(
				'label'     => __( 'Fill Color After Draw', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_mscroll_type' => array( 'svg' ),
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_fill_af_full',
			array(
				'label'     => __( 'Fill Color After Fully Drawn', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_mscroll_type'       => array( 'svg' ),
					'premium_mscroll_draw_sync!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_svg_color',
			array(
				'label'     => __( 'Select Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'premium_mscroll_type'     => 'svg',
					'premium_mscroll_svg_fill' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_sync',
			array(
				'label'     => __( 'Sync with the Previous Animation', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_mscroll_sync_lock!' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'premium_mscroll_delay',
			array(
				'label'       => __( 'Delay (sec)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Negative values means this animation will start before the previous animation ends', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => -2,
						'max'  => 5,
						'step' => 0.1,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_sync!'      => 'yes',
					'premium_mscroll_sync_lock!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_sync_note',
			array(
				'raw'             => __( 'Please note that this option will be overriden by Run All Effects Simultaneously option', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_sync' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_sync_lock',
			array(
				'label'     => __( 'Sync with the whole scene', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_mscroll_sync!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_ease',
			array(
				'label'       => __( 'Easing', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'separator'   => 'before',
				'options'     => array(
					'linear'    => __( 'Linear', 'premium-addons-pro' ),
					'easein'    => __( 'EaseIn', 'premium-addons-pro' ),
					'easeout'   => __( 'EaseOut', 'premium-addons-pro' ),
					'easeinout' => __( 'EaseInOut', 'premium-addons-pro' ),
					'custom'    => __( 'Custom', 'premium-addons-pro' ),
				),
				'default'     => 'easein',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'premium_mscroll_c_ease',
			array(
				'label'       => __( 'Easing Function', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => sprintf( 'Copy and paste the <b>ease</b> value from %s . For example, Bounce.easeOut, SteppedEase.config(12), etc.', ' <a href="https://greensock.com/docs/v2/Easing" target="_blank">here</a>' ),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_ease' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'premium_mscroll_disable_anim',
			array(
				'label'       => __( 'Disable Animation On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'before',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$should_apply = apply_filters( 'pa_display_conditions_values', true );

		$values = $repeater->get_controls();

		if ( $should_apply ) {
			// $values = array_values( $values );
		}

		$element->add_control(
			'premium_mscroll_repeater',
			array(
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $values,
				'title_field'   => '{{{ premium_mscroll_type }}}',
				'condition'     => array(
					'premium_mscroll_switcher' => 'yes',
				),
				'prevent_empty' => false,
			)
		);

		$element->add_control(
			'premium_mscroll_perspective',
			array(
				'label'     => __( 'Perspective (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'condition' => array(
					'premium_mscroll_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_perspective_notice',
			array(
				'raw'             => __( 'Perspective value is used with Translate, Rotate, Skew effects to create 3D effects.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_switcher'           => 'yes',
					'premium_mscroll_perspective[size]!' => '',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_all_sync',
			array(
				'label'        => __( 'Run All Effects Simultaneously', 'premium-addons-pro' ),
				'description'  => __( 'Enable this option to start/end all scroll effects above at the same moment.', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-mscroll-sync-',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_mscroll_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_loop',
			array(
				'label'     => __( 'Loop Count', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'separator' => 'after',
				'condition' => array(
					'premium_mscroll_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_anim_trigger',
			array(
				'label'       => __( 'How this animation should work?', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'lock'      => __( 'Lock Page Scroll Until Animation Ends', 'premium-addons-pro' ),
					'automatic' => __( 'Complete Animation On Viewport', 'premium-addons-pro' ),
					'play'      => __( 'Play Animation Only When Visible', 'premium-addons-pro' ),
					'sticky'    => __( 'Stick to another element', 'premium-addons-pro' ),
				),
				'default'     => 'play',
				'render_type' => 'template',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher' => 'yes',
				),
			)
		);

		$refresh_button = ' <b> IMPORTANT: You may need to click the Refresh button after changing this option.</b>';

		$element->add_control(
			'premium_mscroll_lock_note',
			array(
				'raw'             => __( 'This means that the page scroll will stop until the animation is completed.' . $refresh_button, 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'lock',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_auto_note',
			array(
				'raw'             => __( 'This means that the animation will start and complete once the viewport\'s top hits the top of the section.' . $refresh_button, 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'automatic',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_play_note',
			array(
				'raw'             => __( 'This means that the animation will work only when the element is visible on the viewport.' . $refresh_button, 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'play',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_anim_dur',
			array(
				'label'     => __( 'Animation Duration (sec)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 3,
				),
				'condition' => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'automatic',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_anim_del',
			array(
				'label'     => __( 'Animation Delay (sec)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'automatic',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_auto_trigger',
			array(
				'label'       => __( 'When the animation should start?', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'top'    => __( 'Top of Viewport Hits The Section', 'premium-addons-pro' ),
					'center' => __( 'Center of Viewport Hits The Section', 'premium-addons-pro' ),
					'custom' => __( 'Custom Offset', 'premium-addons-pro' ),
				),
				'default'     => 'top',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'automatic',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_auto_offset',
			array(
				'label'     => __( 'Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'automatic',
					'premium_mscroll_auto_trigger' => 'custom',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_anim_rev',
			array(
				'label'       => __( 'Reverse Animation on Scroll Up', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'automatic',
				),
			)
		);

		$element->add_responsive_control(
			'premium_mscroll_order',
			array(
				'label'       => __( 'Animation Order', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the animation order. For example, 1 means first animation, 2 means second animation, and so on.', 'premium-addons-pro' ),
				'default'     => 1,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'lock',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_lock_delay',
			array(
				'label'       => __( 'Delay (sec)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Negative values means this animation will start before the previous animation ends', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => -2,
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'lock',
					'premium_mscroll_order!'       => 1,
				),
			)
		);

		$element->add_control(
			'premium_mscroll_sticky_target',
			array(
				'label'     => __( 'Apply Sticky On', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'scope'  => __( 'Element Container', 'premium-addons-pro' ),
					'custom' => __( 'Custom CSS Selector', 'premium-addons-pro' ),
				),
				'default'   => 'scope',
				'condition' => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'sticky',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_sticky_target_css',
			array(
				'label'       => __( 'CSS Selector', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Set the CSS selector of the element to be sticky.', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_mscroll_switcher'      => 'yes',
					'premium_mscroll_anim_trigger'  => 'sticky',
					'premium_mscroll_sticky_target' => 'custom',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_sticky_element',
			array(
				'label'       => __( 'Stick this Element to', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Set the CSS selector of the element to be sticky with.', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'sticky',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_sticky_note',
			array(
				'raw'             => __( 'No elements on the page has this CSS selector', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control elementor-hidden-control elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'premium_mscroll_anim_trigger' => 'sticky',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_sticky_start',
			array(
				'label'       => __( 'Start Sticky From', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'absolute' => __( 'Top of the reference element', 'premium-addons-pro' ),
					'relative' => __( 'The current position', 'premium-addons-pro' ),
				),
				'default'     => 'absolute',
				'render_type' => 'template',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'sticky',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_play_ref',
			array(
				'label'       => __( 'Start Animation From', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'section' => __( 'Top of the parent section', 'premium-addons-pro' ),
					'element' => __( 'Top of the element', 'premium-addons-pro' ),
				),
				'default'     => 'section',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'play',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_play_offset',
			array(
				'label'       => __( 'Offset (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set offset between top of the viewport and when the animation starts.', 'premium-addons-pro' ),
				'default'     => array(
					'size' => 50,
					'unit' => '%',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => array( 'sticky', 'play' ),
				),
			)
		);

		$element->add_responsive_control(
			'premium_mscroll_scene_speed',
			array(
				'label'       => __( 'Decrease Animation Speed By', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'The larger the value you set the slower the scene will animate', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'     => array(
					'size' => 1,
					'unit' => 'px',
				),
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => array( 'lock', 'play' ),
				),
			)
		);

		$element->add_responsive_control(
			'premium_mscroll_scrub',
			array(
				'label'       => __( 'Scrub', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'This option controls the time delay after you scroll and before the animation happens. Leave empty if you want the animation to be synced with the page scroll.', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'condition'   => array(
					'premium_mscroll_switcher'      => 'yes',
					'premium_mscroll_anim_trigger!' => array( 'automatic', 'sticky' ),
				),
			)
		);

		$element->add_control(
			'premium_mscroll_sticky_end',
			array(
				'label'       => __( 'Stop Sticky', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'before' => __( 'Before the reference element ends', 'premium-addons-pro' ),
					'after'  => __( 'After the reference element ends', 'premium-addons-pro' ),
				),
				'default'     => 'before',
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'sticky',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_disable_sticky',
			array(
				'label'       => __( 'Disable Sticky On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'before',
				'multiple'    => true,
				'label_block' => true,
				'condition'   => array(
					'premium_mscroll_switcher'     => 'yes',
					'premium_mscroll_anim_trigger' => 'sticky',
				),
			)
		);

		$element->add_control(
			'premium_mscroll_update',
			array(
				'label'     => '<div class="elementor-update-preview editor-pa-preview-update"><div class="elementor-update-preview-title">Found something wrong? Click Refresh</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Refresh</button></div></div>',
				'type'      => Controls_Manager::RAW_HTML,
				'separator' => 'before',
			)
		);

		$this->add_helpful_information( $element );

		$element->end_controls_section();

		$this->register_responsive_controls( $element );
	}

	/**
	 * Register Magic Scroll Controls
	 *
	 * @since 2.8.15
	 * @access private
	 * @param object $element for current element.
	 */
	private function add_helpful_information( $element ) {

		$element->add_control(
			'premium_mscroll_info',
			array(
				'label'     => __( 'Helpful Information', 'premium-addons-pro' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'premium_mscroll_switcher' => 'yes',
				),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-magic-scroll-addon-tutorial/' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/create-images-layers-parallax-addon-and-elementor-magic-scroll/' => __( 'Change element opacity using Magic Scroll addon »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-change-background-color-using-elementor-magic-scroll-addon/' => __( 'Change element background color using Magic Scroll addon »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/create-scrollable-carousel-with-magic-scroll-elementor-addon/' => __( 'Animate Premium Carousel using Magic Scroll addon »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/change-text-color-using-elementor-magic-scroll-addon/' => __( 'Change text color using Magic Scroll addon »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/draw-svg-with-elementor-magic-scroll/' => __( 'Draw SVG using Magic Scroll addon »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/animate-progress-bar-with-elementor-magic-scroll-global-addon/' => __( 'Animate Progress Bar using Magic Scroll addon »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-create-image-sequence-animation-with-elementor-magic-scroll/' => __( 'Create Image Sequence using Magic Scroll addon »', 'premium-addons-pro' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$element->add_control(
				'premium_mscroll_doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
					'condition'       => array(
						'premium_mscroll_switcher' => 'yes',
					),
				)
			);

			$doc_index++;

		}

	}

	/**
	 * Register Magic Scroll Controls
	 *
	 * @since 1.0.0
	 * @access private
	 * @param object $element for current element.
	 */
	private function register_responsive_controls( $element ) {

		$element->start_controls_section(
			'section_mscroll_responsive',
			array(
				'label'     => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Magic Scroll Responsive Settings', 'premium-addons-pro' ) ),
				'tab'       => Controls_Manager::TAB_ADVANCED,
				'condition' => array(
					'premium_mscroll_switcher' => 'yes',
				),
			)
		);

		$element->start_controls_tabs( 'premium_mscroll_tabs' );

		// Desktop tab.
		$element->start_controls_tab(
			'mscroll_desktop_tab',
			array(
				'label' => __( 'Desktop', 'premium-addons-pro' ),
			)
		);

		$element->add_control(
			'mscroll_desktop_action',
			array(
				'label'       => __( 'Action', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'    => __( 'Default', 'premium-addons-pro' ),
					'disable' => __( 'Disable Magic Scroll Totally', 'premium-addons-pro' ),
					'height'  => __( 'Disable Lock Page Scroll', 'premium-addons-pro' ),
				),
				'default'     => 'none',
				'render_type' => 'template',
				'label_block' => true,
			)
		);

		$element->add_control(
			'mscroll_desktop_lock',
			array(
				'label'       => __( 'When to Lock Page Scroll', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'window' => __( 'Section Height Larger Than Window Height', 'premium-addons-pro' ),
					'custom' => __( 'Section Height Larger Than Specfic Height', 'premium-addons-pro' ),
				),
				'default'     => 'window',
				'label_block' => true,
				'condition'   => array(
					'mscroll_desktop_action' => 'height',
				),
			)
		);

		$element->add_control(
			'mscroll_desktop_lock_height',
			array(
				'label'     => __( 'Height (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'mscroll_desktop_action' => 'height',
					'mscroll_desktop_lock'   => 'custom',
				),
			)
		);

		$element->end_controls_tab();

		// Tablet tab.
		$element->start_controls_tab(
			'mscroll_tablet_tab',
			array(
				'label' => __( 'Tablet', 'premium-addons-pro' ),
			)
		);

		$element->add_control(
			'mscroll_tablet_action',
			array(
				'label'       => __( 'Action', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'    => __( 'Default', 'premium-addons-pro' ),
					'disable' => __( 'Disable Magic Scroll Totally', 'premium-addons-pro' ),
					'height'  => __( 'Disable Lock Page Scroll', 'premium-addons-pro' ),
				),
				'default'     => 'none',
				'render_type' => 'template',
				'label_block' => true,
			)
		);

		$element->add_control(
			'mscroll_tablet_lock',
			array(
				'label'       => __( 'When to Lock Page Scroll', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'window' => __( 'Section Height Larger Than Window Height', 'premium-addons-pro' ),
					'custom' => __( 'Section Height Larger Than Specfic Height', 'premium-addons-pro' ),
				),
				'default'     => 'window',
				'label_block' => true,
				'condition'   => array(
					'mscroll_tablet_action' => 'height',
				),
			)
		);

		$element->add_control(
			'mscroll_tablet_lock_height',
			array(
				'label'     => __( 'Height (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'mscroll_tablet_action' => 'height',
					'mscroll_tablet_lock'   => 'custom',
				),
			)
		);

		$element->end_controls_tab();

		// Mobile tab.
		$element->start_controls_tab(
			'mscroll_mobile_tab',
			array(
				'label' => __( 'Mobile', 'premium-addons-pro' ),
			)
		);

		$element->add_control(
			'mscroll_mobile_action',
			array(
				'label'       => __( 'Action', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'    => __( 'Default', 'premium-addons-pro' ),
					'disable' => __( 'Disable Magic Scroll Totally', 'premium-addons-pro' ),
					'height'  => __( 'Disable Lock Page Scroll', 'premium-addons-pro' ),
				),
				'default'     => 'none',
				'render_type' => 'template',
				'label_block' => true,
			)
		);

		$element->add_control(
			'mscroll_mobile_lock',
			array(
				'label'       => __( 'When to Lock Page Scroll', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'window' => __( 'Section Height Larger Than Window Height', 'premium-addons-pro' ),
					'custom' => __( 'Section Height Larger Than Specfic Height', 'premium-addons-pro' ),
				),
				'default'     => 'window',
				'label_block' => true,
				'condition'   => array(
					'mscroll_mobile_action' => 'height',
				),
			)
		);

		$element->add_control(
			'mscroll_mobile_lock_height',
			array(
				'label'     => __( 'Height (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'mscroll_mobile_action' => 'height',
					'mscroll_mobile_lock'   => 'custom',
				),
			)
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();

	}

	/**
	 * Render Global badge output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template for current template.
	 * @param object $element for current element.
	 */
	public function print_template( $template, $element ) {

		if ( ! $template && 'widget' === $element->get_type() ) {
			return;
		}

		$old_template = $template;
		ob_start();

		?>
		<#

			var mscrollSettings = {},
			scrollEffects = [];

			mscrollSettings.isEnabled = 'yes' === settings.premium_mscroll_switcher;

			view.addRenderAttribute( 'mscroll_data', {
				'data-mscroll-enabled': mscrollSettings.isEnabled
			});

			if ( mscrollSettings.isEnabled )  {

				_.each( settings.premium_mscroll_repeater, function( effect, index ) {
					scrollEffects.push( effect );
				});

				mscrollSettings.effects = scrollEffects;

				mscrollSettings.fullDuration = settings.premium_mscroll_scene_speed.size;
				mscrollSettings.fullDurationTablet = settings.premium_mscroll_scene_speed_tablet.size || '';
				mscrollSettings.fullDurationMobile = settings.premium_mscroll_scene_speed_mobile.size || '';


				mscrollSettings.repeat = settings.premium_mscroll_loop;
				mscrollSettings.perspective = settings.premium_mscroll_perspective.size;

				mscrollSettings.trigger = settings.premium_mscroll_anim_trigger;

				if( 'automatic' === mscrollSettings.trigger ) {

					mscrollSettings.autoTrigger = 'custom' !== settings.premium_mscroll_auto_trigger ? settings.premium_mscroll_auto_trigger : settings.premium_mscroll_auto_offset.size + "%";

					mscrollSettings.animRev = 'yes' === settings.premium_mscroll_anim_rev;

					mscrollSettings.autoDuration = settings.premium_mscroll_anim_dur.size;
					mscrollSettings.autoDel = settings.premium_mscroll_anim_del.size;

				} else {

					mscrollSettings.scrub = settings.premium_mscroll_scrub.size;
					mscrollSettings.scrubTablet = settings.premium_mscroll_scrub_tablet.size || '';
					mscrollSettings.scrubMobile = settings.premium_mscroll_scrub_mobile.size || '';

					if( 'lock' === mscrollSettings.trigger ) {
						mscrollSettings.lockDelay = settings.premium_mscroll_lock_delay.size;

						mscrollSettings.order = settings.premium_mscroll_order;
						mscrollSettings.orderTablet = settings.premium_mscroll_order_tablet ? settings.premium_mscroll_order_tablet : mscrollSettings.order;
						mscrollSettings.orderMobile = settings.premium_mscroll_order_mobile ? settings.premium_mscroll_order_mobile : mscrollSettings.order;

					} else {

						mscrollSettings.playOffset = settings.premium_mscroll_play_offset.size;

						if( 'sticky' === mscrollSettings.trigger ) {

							mscrollSettings.stickyRef = settings.premium_mscroll_sticky_element;

							mscrollSettings.stickyTarget = settings.premium_mscroll_sticky_target;
							mscrollSettings.stickyTargetSelector = settings.premium_mscroll_sticky_target_css;

							mscrollSettings.stickyStart = settings.premium_mscroll_sticky_start;
							mscrollSettings.stickyEnd = settings.premium_mscroll_sticky_end;

							mscrollSettings.stickyDisable = settings.premium_mscroll_disable_sticky;

						} else {

							mscrollSettings.playRef = settings.premium_mscroll_play_ref;

						}

					}
				}

				mscrollSettings.desktop = {
					action: settings.mscroll_desktop_action,
					lock: settings.mscroll_desktop_lock,
					height: settings.mscroll_desktop_lock_height
				};

				mscrollSettings.tablet = {
					action: settings.mscroll_tablet_action,
					lock: settings.mscroll_tablet_lock,
					height: settings.mscroll_tablet_lock_height
				};

				mscrollSettings.mobile = {
					action: settings.mscroll_mobile_action,
					lock: settings.mscroll_mobile_lock,
					height: settings.mscroll_mobile_lock_height
				};

				view.addRenderAttribute( 'mscroll_data', {
					'id': 'premium-mscroll-' + view.getID(),
					'class': 'premium-mscroll-wrapper',
					'data-mscroll': JSON.stringify( mscrollSettings )
				});

			}

		#>
		<div {{{ view.getRenderAttributeString( 'mscroll_data' ) }}}></div>
		<?php
			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Global badge output on the frontend.
	 *
	 * Written in PHP and used to collect badge settings and add it as an element attribute.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$type = $element->get_type();

		// echo $element->get_name();

		$id = $element->get_id();

		$settings = $element->get_settings_for_display();

		$mscroll_switcher = $settings['premium_mscroll_switcher'];

		if ( 'yes' === $mscroll_switcher && isset( $settings['premium_mscroll_repeater'] ) ) {

			$full_duration     = isset( $settings['premium_mscroll_scene_speed'] ) ? $settings['premium_mscroll_scene_speed']['size'] : 1;
			$full_duration_mob = ! empty( $settings['premium_mscroll_scene_speed_mobile']['size'] ) ? $settings['premium_mscroll_scene_speed_mobile']['size'] : '';
			$full_duration_tab = ! empty( $settings['premium_mscroll_scene_speed_tablet']['size'] ) ? $settings['premium_mscroll_scene_speed_tablet']['size'] : '';

			$trigger = $settings['premium_mscroll_anim_trigger'];

			$effects = array();

			foreach ( $settings['premium_mscroll_repeater'] as $effect ) {
				array_push( $effects, $effect );
			}

			$mscroll_settings = array(
				'effects'            => $effects,

				'fullDuration'       => $full_duration,
				'fullDurationTablet' => $full_duration_tab,
				'fullDurationMobile' => $full_duration_mob,

				'repeat'             => $settings['premium_mscroll_loop'],
				'perspective'        => $settings['premium_mscroll_perspective']['size'],
				'trigger'            => $trigger,
			);

			if ( 'automatic' === $trigger ) {

				$mscroll_settings['autoTrigger'] = 'custom' !== $settings['premium_mscroll_auto_trigger'] ? $settings['premium_mscroll_auto_trigger'] : $settings['premium_mscroll_auto_offset']['size'] . '%';

				$mscroll_settings['autoDuration'] = $settings['premium_mscroll_anim_dur']['size'];
				$mscroll_settings['autoDel']      = $settings['premium_mscroll_anim_del']['size'];

				$mscroll_settings['animRev'] = 'yes' === $settings['premium_mscroll_anim_rev'];
			} else {

				$scrub     = ! empty( $settings['premium_mscroll_scrub']['size'] ) ? $settings['premium_mscroll_scrub']['size'] : '';
				$scrub_mob = ! empty( $settings['premium_mscroll_scrub_mobile']['size'] ) ? $settings['premium_mscroll_scrub_mobile']['size'] : '';
				$scrub_tab = ! empty( $settings['premium_mscroll_scrub_tablet']['size'] ) ? $settings['premium_mscroll_scrub_tablet']['size'] : '';

				$mscroll_settings['scrub']       = $scrub;
				$mscroll_settings['scrubTablet'] = $scrub_tab;
				$mscroll_settings['scrubMobile'] = $scrub_mob;

				if ( 'lock' === $trigger ) {
					$mscroll_settings['lockDelay'] = isset( $settings['premium_mscroll_lock_delay'] ) ? $settings['premium_mscroll_lock_delay']['size'] : 0;

					$order     = ! empty( $settings['premium_mscroll_order'] ) ? $settings['premium_mscroll_order'] : 1;
					$order_mob = ! empty( $settings['premium_mscroll_order_mobile'] ) ? $settings['premium_mscroll_order_mobile'] : $order;
					$order_tab = ! empty( $settings['premium_mscroll_order_tablet'] ) ? $settings['premium_mscroll_order_tablet'] : $order;

					$mscroll_settings['order']       = $order;
					$mscroll_settings['orderTablet'] = $order_tab;
					$mscroll_settings['orderMobile'] = $order_mob;

				} else {

					$mscroll_settings['playOffset'] = $settings['premium_mscroll_play_offset']['size'];

					if ( 'sticky' === $trigger ) {

						$mscroll_settings['stickyRef'] = $settings['premium_mscroll_sticky_element'];

						$mscroll_settings['stickyTarget']         = $settings['premium_mscroll_sticky_target'];
						$mscroll_settings['stickyTargetSelector'] = $settings['premium_mscroll_sticky_target_css'];

						$mscroll_settings['stickyStart'] = $settings['premium_mscroll_sticky_start'];
						$mscroll_settings['stickyEnd']   = $settings['premium_mscroll_sticky_end'];

						$mscroll_settings['stickyDisable'] = $settings['premium_mscroll_disable_sticky'];

					} else {

						$mscroll_settings['playRef'] = $settings['premium_mscroll_play_ref'];

					}
				}
			}

			$mscroll_settings['desktop'] = array(
				'action' => $settings['mscroll_desktop_action'],
				'lock'   => $settings['mscroll_desktop_lock'],
				'height' => $settings['mscroll_desktop_lock_height'],
			);

			$mscroll_settings['tablet'] = array(
				'action' => $settings['mscroll_tablet_action'],
				'lock'   => $settings['mscroll_tablet_lock'],
				'height' => $settings['mscroll_tablet_lock_height'],
			);

			$mscroll_settings['mobile'] = array(
				'action' => $settings['mscroll_mobile_action'],
				'lock'   => $settings['mscroll_mobile_lock'],
				'height' => $settings['mscroll_mobile_lock_height'],
			);

			$element->add_render_attribute( '_wrapper', 'data-mscroll', wp_json_encode( $mscroll_settings ) );

			if ( 'sticky' !== $trigger || ( 'sticky' === $trigger && 'scope' === $settings['premium_mscroll_sticky_target'] ) ) {
				$element->add_render_attribute( '_wrapper', 'class', 'pa-invisible' );
			}

			if ( 'widget' === $type && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				?>
				<div id='premium-mscroll-<?php echo esc_html( $id ); ?>' data-mscroll='<?php echo wp_json_encode( $mscroll_settings ); ?>'></div>
				<?php
			}
		}
	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 2.6.3
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( self::$load_script ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'premium_mscroll_switcher' ) ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();

			self::$load_script = true;

			remove_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );
		}

	}

}
