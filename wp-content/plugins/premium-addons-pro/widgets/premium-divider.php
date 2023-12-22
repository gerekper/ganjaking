<?php
/**
 * Class: Premium_Divider
 * Name: Divider
 * Slug: premium-divider
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Divider
 */
class Premium_Divider extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		if ( version_compare( PREMIUM_ADDONS_VERSION, '4.9.26', '<' ) ) {
			return false;
		}

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-divider' );
		return $is_enabled;
	}

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-divider';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Divider', 'premium-addons-pro' );
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
		return 'pa-pro-separator';
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
		return array( 'pa', 'premium', 'lottie', 'separator', 'svg', 'icon' );
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

		$draw_scripts = $this->check_icon_draw() ? array(
			'pa-fontawesome-all',
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'lottie-js',
				'premium-pro',
			)
		);
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
	 * Register Divider controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->add_divider_controls( $draw_icon );

		$this->add_element_controls( $draw_icon );

		$this->add_divider_style_controls();

		$this->add_element_style_controls( $draw_icon );

		$this->add_container_style();
	}

	/**
	 * Adds Divider Controls.
	 *
	 * @param Boolean $draw_icon  true if the svg draw feature is enabled for the widget.
	 */
	private function add_divider_controls( $draw_icon ) {

		$sep_conditions = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'left_sep_sw',
					'value' => 'yes',
				),
				array(
					'name'  => 'right_sep_sw',
					'value' => 'yes',
				),
			),
		);

		$this->start_controls_section(
			'separator_section',
			array(
				'label' => __( 'Divider', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'left_sep_sw',
			array(
				'label'     => __( 'Left Separator', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'right_sep_sw',
			array(
				'label'     => __( 'Right Separator', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$solid_shapes = array( 'solid', 'double', 'dotted', 'dashed', 'groove', 'shadow', 'gradient', 'curvedbot', 'curvedtop', 'custom', 'div-bg-curly', 'div-bg-curved', 'div-bg-slashed', 'div-bg-wavy', 'div-bg-zigzag', 'div-bg-diamond', 'div-bg-para', 'div-bg-rect', 'div-bg-r-dots', 'div-bg-r-ftree', 'div-bg-r-hround', 'div-bg-r-leaves', 'div-bg-r-strips', 'div-bg-r-square', 'div-bg-r-tree', 'div-bg-r-tribal', 'div-bg-r-x', 'div-bg-r-tzigzag' );

		$this->add_control(
			'left_and_right_separator_type',
			array(
				'label'      => __( 'Style', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SELECT,
				'groups'     => array(
					'line'       => array(
						'label'   => __( 'Line', 'premium-addons-for-elementor' ),
						'options' => array(
							'solid'            => __( 'Solid', 'premium-addons-pro' ),
							'double'           => __( 'Double', 'premium-addons-pro' ),
							'dotted'           => __( 'Dotted', 'premium-addons-pro' ),
							'dashed'           => __( 'Dashed', 'premium-addons-pro' ),
							'groove'           => __( 'Groove', 'premium-addons-pro' ),
							'shadow'           => __( 'Shadow', 'premium-addons-pro' ),
							'gradient'         => __( 'Gradient', 'premium-addons-pro' ),
							'curvedbot'        => __( 'Curved Bottom', 'premium-addons-pro' ),
							'curvedtop'        => __( 'Curved Top', 'premium-addons-pro' ),
							'div-bg-curly'     => __( 'Curly', 'premium-addons-pro' ),
							'div-bg-curved'    => __( 'Curved', 'premium-addons-pro' ),
							'div-bg-slashed'   => __( 'Slashed', 'premium-addons-pro' ),
							'div-bg-wavy'      => __( 'Wavy', 'premium-addons-pro' ),
							'div-bg-zigzag'    => __( 'Zigzag', 'premium-addons-pro' ),
							'div-bg-diamond'   => __( 'Diamond', 'premium-addons-pro' ),
							'div-bg-para'      => __( 'Parallelogram', 'premium-addons-pro' ),
							'div-bg-rect'      => __( 'Rectangles', 'premium-addons-pro' ),
							'div-bg-r-dots'    => __( 'Dots', 'premium-addons-pro' ),
							'div-bg-r-ftree'   => __( 'Fir Trees', 'premium-addons-pro' ),
							'div-bg-r-hround'  => __( 'Half Rounds', 'premium-addons-pro' ),
							'div-bg-r-leaves'  => __( 'Leaves', 'premium-addons-pro' ),
							'div-bg-r-strips'  => __( 'Strips', 'premium-addons-pro' ),
							'div-bg-r-square'  => __( 'Squares', 'premium-addons-pro' ),
							'div-bg-r-tree'    => __( 'Trees', 'premium-addons-pro' ),
							'div-bg-r-tribal'  => __( 'Tribal', 'premium-addons-pro' ),
							'div-bg-r-x'       => __( 'X', 'premium-addons-pro' ),
							'div-bg-r-tzigzag' => __( 'Bold Zigzag', 'premium-addons-pro' ),
						),
					),
					'pattern'    => array(
						'label'   => __( 'Pattern', 'premium-addons-for-elementor' ),
						'options' => array(
							'pattern-1'  => __( 'Pattern 1', 'premium-addons-for-elementor' ),
							'pattern-2'  => __( 'Pattern 2', 'premium-addons-for-elementor' ),
							'pattern-3'  => __( 'Pattern 3', 'premium-addons-for-elementor' ),
							'pattern-4'  => __( 'Pattern 4', 'premium-addons-for-elementor' ),
							'pattern-5'  => __( 'Pattern 5', 'premium-addons-for-elementor' ),
							'pattern-6'  => __( 'Pattern 6', 'premium-addons-for-elementor' ),
							'pattern-7'  => __( 'Pattern 7', 'premium-addons-for-elementor' ),
							'pattern-8'  => __( 'Pattern 8', 'premium-addons-for-elementor' ),
							'pattern-9'  => __( 'Pattern 9', 'premium-addons-for-elementor' ),
							'pattern-10' => __( 'Pattern 10', 'premium-addons-for-elementor' ),
							'pattern-11' => __( 'Pattern 11', 'premium-addons-for-elementor' ),
						),
					),
					'custom_sep' => array(
						'label'   => __( 'Custom', 'premium-addons-for-elementor' ),
						'options' => array(
							'custom'     => __( 'Custom Image', 'premium-addons-for-elementor' ),
							'custom_svg' => __( 'Custom SVG', 'premium-addons-for-elementor' ),
						),
					),
				),
				'default'    => 'solid',
				'conditions' => $sep_conditions,
			)
		);

		$this->add_control(
			'content_lines_Number',
			array(
				'label'      => __( 'Number of Lines', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SELECT,
				'options'    => array(
					'<hr>'                 => __( 'One', 'premium-addons-pro' ),
					'<hr><hr>'             => __( 'Two', 'premium-addons-pro' ),
					'<hr><hr><hr>'         => __( 'Three', 'premium-addons-pro' ),
					'<hr><hr><hr><hr>'     => __( 'Four', 'premium-addons-pro' ),
					'<hr><hr><hr><hr><hr>' => __( 'Five', 'premium-addons-pro' ),
				),
				'default'    => '<hr>',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'left_and_right_separator_type',
							'operator' => 'in',
							'value'    => $solid_shapes,
						),
						$sep_conditions,
					),
				),
			)
		);

		$this->add_control(
			'left_separator_image',
			array(
				'label'       => __( 'Left Line Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'left_and_right_separator_type' => 'custom',
					'left_sep_sw'                   => 'yes',
				),
			)
		);

		$this->add_control(
			'right_separator_image',
			array(
				'label'       => __( 'Right Line Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'left_and_right_separator_type' => 'custom',
					'right_sep_sw'                  => 'yes',
				),
			)
		);

		$this->add_control(
			'left_custom_svg',
			array(
				'label'                  => __( 'Left SVG', 'premium-addons-pro' ),
				'type'                   => Controls_Manager::ICONS,
				'skin'                   => 'inline',
				'label_block'            => false,
				'exclude_inline_options' => array( 'none', 'icon' ),
				'condition'              => array(
					'left_and_right_separator_type' => 'custom_svg',
					'left_sep_sw'                   => 'yes',
				),
			)
		);

		$this->add_control(
			'right_custom_svg',
			array(
				'label'                  => __( 'Right SVG', 'premium-addons-pro' ),
				'type'                   => Controls_Manager::ICONS,
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'none', 'icon' ),
				'condition'              => array(
					'left_and_right_separator_type' => 'custom_svg',
					'right_sep_sw'                  => 'yes',
				),
			)
		);

		$this->add_control(
			'sep_bg_stroke_width',
			array(
				'label'       => __( 'Thickness', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'label_block' => true,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 0.1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 1,
				),
				'conditions'  => array(
					'terms' => array(
						$sep_conditions,
						array(
							'name'     => 'left_and_right_separator_type',
							'operator' => 'in',
							'value'    => array( 'div-bg-curly', 'div-bg-curved', 'div-bg-slashed', 'div-bg-wavy', 'div-bg-zigzag' ),
						),
					),
				),
			)
		);

		$this->add_control(
			'content_link_switcher',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Add a custom link or select an existing page link', 'premium-addons-pro' ),
				'conditions'  => $sep_conditions,
			)
		);

		$this->add_control(
			'content_link_type',
			array(
				'label'       => __( 'Link/URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'conditions'  => array(
					'terms' => array(
						$sep_conditions,
						array(
							'name'  => 'content_link_switcher',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'content_existing_page',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'label_block' => true,
				'conditions'  => array(
					'terms' => array(
						$sep_conditions,
						array(
							'name'  => 'content_link_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'content_link_type',
							'value' => 'link',
						),
					),
				),
			)
		);

		$this->add_control(
			'content_url',
			array(
				'label'       => __( 'URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'conditions'  => array(
					'terms' => array(
						$sep_conditions,
						array(
							'name'  => 'content_link_switcher',
							'value' => 'yes',
						),
						array(
							'name'  => 'content_link_type',
							'value' => 'url',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'content_alignment',
			array(
				'label'      => __( 'Alignment', 'premium-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'default'    => 'center',
				'toggle'     => false,
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-wrapper-separator-divider' => 'justify-content: {{VALUE}}',
				),
				'conditions' => $sep_conditions,
			)
		);

		// separator svg draw
		$this->add_control(
			'sep_draw_svg',
			array(
				'label'      => __( 'Draw Pattern', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'classes'    => $draw_icon ? '' : 'editor-pa-control-disabled',
				// 'conditions' => $sep_conditions,
				'conditions' => array(
					'terms' => array(
						$sep_conditions,
						array(
							'name'     => 'left_and_right_separator_type',
							'operator' => '!in',
							'value'    => $solid_shapes,
						),
					),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'sep_path_width',
				array(
					'label'      => __( 'Path Thickness', 'premium-addons-pro' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 0.1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .premium-separator-divider-left svg *, {{WRAPPER}} .premium-separator-divider-right svg *' => 'stroke-width: {{SIZE}}',
					),
					'conditions' => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							$sep_conditions,
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
						),
					),
				)
			);

			$this->add_control(
				'sep_svg_sync',
				array(
					'label'      => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'       => Controls_Manager::SWITCHER,
					'conditions' => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
							$sep_conditions,
						),
					),
				)
			);

			$this->add_control(
				'sep_frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'conditions'  => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
							$sep_conditions,
						),
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'divider',
				 array(
					'terms' => array(
						$sep_conditions,
						array(
							'name'     => 'left_and_right_separator_type',
							'operator' => '!in',
							'value'    => $solid_shapes,
						),
					),
				),
				'01',
				'conditions'
			);
		}

		$this->add_control(
			'sep_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'sep_draw_svg',
							'value' => 'yes',
						),
						array(
							'name'     => 'left_and_right_separator_type',
							'operator' => '!in',
							'value'    => $solid_shapes,
						),
						$sep_conditions,
					),
				),
			)
		);

		$this->add_control(
			'sep_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'sep_draw_svg',
							'value' => 'yes',
						),
						array(
							'name'     => 'left_and_right_separator_type',
							'operator' => '!in',
							'value'    => $solid_shapes,
						),
						$sep_conditions,
					),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'sep_start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'conditions'  => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
							$sep_conditions,
						),
					),
				)
			);

			$this->add_control(
				'sep_end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'size' => 0,
						'unit' => '%',
					),
					'conditions'  => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
							$sep_conditions,
						),
					),
				)
			);

			$this->add_control(
				'sep_svg_yoyo',
				array(
					'label'      => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'       => Controls_Manager::SWITCHER,
					'conditions' => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							array(
								'name'  => 'sep_lottie_loop',
								'value' => 'true',
							),
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
							$sep_conditions,
						),
					),
				)
			);

			$this->add_control(
				'sep_animate_offset',
				array(
					'label'      => __( 'Offset (%)', 'premium-addons-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'conditions' => array(
						'terms' => array(
							array(
								'name'  => 'sep_draw_svg',
								'value' => 'yes',
							),
							array(
								'name'     => 'left_and_right_separator_type',
								'operator' => '!in',
								'value'    => $solid_shapes,
							),
							$sep_conditions,
						),
					),
				)
			);

		}

		$this->end_controls_section();
	}

	/**
	 * Adds Element Controls.
	 *
	 * @param Boolean $draw_icon  true if the svg draw feature is enabled for the widget.
	 */
	private function add_element_controls( $draw_icon ) {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Element', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'element_sw',
			array(
				'label'     => __( 'Element', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'content_text',
			array(
				'label'     => __( 'Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'element_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_text_tag',
			array(
				'label'     => __( 'HTML Tag', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'condition' => array(
					'element_sw'    => 'yes',
					'content_text!' => '',
				),
			)
		);

		$this->add_control(
			'content_inside_separator',
			array(
				'label'     => __( 'Element Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'none'              => array(
						'title' => __( 'None', 'premium-addons-pro' ),
						'icon'  => 'eicon-ban',
					),
					'font_awesome_icon' => array(
						'title' => __( 'Icon', 'premium-addons-pro' ),
						'icon'  => 'divider-type-icon',
					),
					'custom_image'      => array(
						'title' => __( 'Image', 'premium-addons-pro' ),
						'icon'  => 'divider-type-image',
					),
					'animation'         => array(
						'title' => __( 'Lottie Animation', 'premium-addons-pro' ),
						'icon'  => 'divider-type-lottie',
					),
					'svg'               => array(
						'title' => __( 'SVG Code', 'premium-addons-pro' ),
						'icon'  => 'divider-type-code',
					),
				),
				'toggle'    => false,
				'default'   => 'font_awesome_icon',
				'condition' => array(
					'element_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'divider_icon',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
                'exclude_inline_options' => array( 'svg' ),
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => array(
					'element_sw'               => 'yes',
					'content_inside_separator' => 'font_awesome_icon',
				),
			)
		);

		$this->add_control(
			'content_image',
			array(
				'label'       => __( 'Choose Image', 'premium-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'element_sw'               => 'yes',
					'content_inside_separator' => 'custom_image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'thumbnail',
				'condition' => array(
					'element_sw'               => 'yes',
					'content_inside_separator' => 'custom_image',
				),
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'element_sw'               => 'yes',
					'content_inside_separator' => 'svg',
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
					'element_sw'               => 'yes',
					'content_inside_separator' => 'animation',
				),
			)
		);

		$svg_fa_cond = array(
			array(
				'name'  => 'element_sw',
				'value' => 'yes',
			),
			array(
				'relation' => 'or',
				'terms'    => array(
					array(
						'name'  => 'content_inside_separator',
						'value' => 'svg',
					),
					array(
						'terms' => array(
							array(
								'name'  => 'content_inside_separator',
								'value' => 'font_awesome_icon',
							),
							array(
								'name'     => 'divider_icon[library]',
								'operator' => '!==',
								'value'    => 'svg',
							),
						),
					),
				),
			),
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'      => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SWITCHER,
				'classes'    => $draw_icon ? '' : 'editor-pa-control-disabled',
				'conditions' => array(
					'terms' => $svg_fa_cond,
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'element_sw',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'content_inside_separator',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'content_inside_separator',
											'value' => 'svg',
										),
										array(
											'terms' => array(
												array(
													'name' => 'content_inside_separator',
													'value' => 'font_awesome_icon',
												),
												array(
													'name' => 'divider_icon[library]',
													'operator' => '!==',
													'value' => 'svg',
												),
											),
										),
									),
								),
								array(
									'name'  => 'draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
		);

		if ( $draw_icon ) {
			$this->add_control(
				'path_width',
				array(
					'label'     => __( 'Path Thickness', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 0.1,
						),
					),
					'condition' => array(
						'element_sw'               => 'yes',
						'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-separator-icon-container svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'      => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'       => Controls_Manager::SWITCHER,
					'conditions' => array(
						'terms' => array_merge(
							array(
								array(
									'name'  => 'draw_svg',
									'value' => 'yes',
								),
							),
							$svg_fa_cond
						),
					),
				)
			);

			$this->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-pro' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-pro' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'conditions'  => array(
						'terms' => array_merge(
							array(
								array(
									'name'  => 'draw_svg',
									'value' => 'yes',
								),
							),
							$svg_fa_cond
						),
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {
			Helper_Functions::get_draw_svg_notice(
				$this,
				'divider',
				array(
					'element_sw'               => 'yes',
					'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
					'divider_icon[library]!'   => 'svg',
				),
				'11'
			);
		}

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $animation_conds,
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $animation_conds,
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-pro' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'element_sw'               => 'yes',
						'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
						'draw_svg'                 => 'yes',
						'lottie_reverse!'          => 'true',
					),
				)
			);

			$this->add_control(
				'end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-pro' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-pro' ),
					'default'     => array(
						'size' => 0,
						'unit' => '%',
					),
					'condition'   => array(
						'element_sw'               => 'yes',
						'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
						'draw_svg'                 => 'yes',
						'lottie_reverse'           => 'true',
					),

				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'element_sw'               => 'yes',
						'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
						'draw_svg'                 => 'yes',
						'lottie_loop'              => 'true',
					),
				)
			);

			$this->add_control(
				'animate_offset',
				array(
					'label'      => __( 'Offset (%)', 'premium-addons-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'condition'  => array(
						'element_sw'               => 'yes',
						'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
						'draw_svg'                 => 'yes',
					),
				)
			);

		}

		$this->add_responsive_control(
			'content_display',
			array(
				'label'     => __( 'Display', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'row'    => array(
						'title' => __( 'Inline', 'premium-addons-pro' ),
						'icon'  => 'eicon-navigation-horizontal',
					),
					'column' => array(
						'title' => __( 'Block', 'premium-addons-pro' ),
						'icon'  => 'eicon-navigation-vertical',
					),
				),
				'default'   => 'row',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'flex-direction: {{VALUE}}',
				),
				'condition' => array(
					'element_sw'                => 'yes',
					'content_text!'             => '',
					'content_inside_separator!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'div_content_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'align-items: {{VALUE}}',
				),
				'condition' => array(
					'element_sw'                => 'yes',
					'content_text!'             => '',
					'content_inside_separator!' => 'none',
					'content_display'           => 'column',
				),
			)
		);

		$this->add_responsive_control(
			'content_order',
			array(
				'label'     => __( 'Text Order', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'0' => array(
						'title' => __( 'Before Icon', 'premium-addons-pro' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'After Icon', 'premium-addons-pro' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '0',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-text-icon' => 'order: {{VALUE}}',
				),
				'condition' => array(
					'element_sw'                => 'yes',
					'content_text!'             => '',
					'content_inside_separator!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'content_spacing',
			array(
				'label'     => __( 'Spacing (px)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => 'px',
					'size' => 6,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'gap: {{SIZE}}px',
				),
				'condition' => array(
					'element_sw'                => 'yes',
					'content_text!'             => '',
					'content_inside_separator!' => 'none',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds Divider Style Controls.
	 */
	private function add_divider_style_controls() {

		$bg_shapes = array( 'div-bg-curly', 'div-bg-curved', 'div-bg-slashed', 'div-bg-wavy', 'div-bg-zigzag', 'div-bg-diamond', 'div-bg-para', 'div-bg-rect' );

		$this->start_controls_section(
			'separator_lines_tab',
			array(
				'label'      => __( 'Divider', 'premium-addons-pro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'left_sep_sw',
							'value' => 'yes',
						),
						array(
							'name'  => 'right_sep_sw',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'left_separator_width',
			array(
				'label'       => __( 'Left Width (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-divider-left' => 'width: {{SIZE}}%;',
				),
				'condition'   => array(
					'left_sep_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'right_separator_width',
			array(
				'label'       => __( 'Right Width (%)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-divider-right' => 'width: {{SIZE}}%;',
				),
				'condition'   => array(
					'right_sep_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'left_and_right_separator_height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'label_block' => true,
				'condition'   => array(
					'left_and_right_separator_type!' => array( 'curved', 'pattern-1', 'pattern-2', 'pattern-3', 'pattern-4', 'pattern-5' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-divider-left hr,
                    {{WRAPPER}} .premium-separator-divider-right hr' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-separator-curvedtop .premium-separator-left-side hr,
                     {{WRAPPER}} .premium-separator-curvedtop .premium-separator-right-side hr' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-separator-shadow .premium-separator-left-side hr,
                     {{WRAPPER}} .premium-separator-shadow .premium-separator-right-side hr,
                    {{WRAPPER}} .premium-separator-gradient .premium-separator-left-side hr,
                    {{WRAPPER}} .premium-separator-gradient .premium-separator-right-side hr,
                    {{WRAPPER}} .premium-div-svg .premium-separator-divider-right,
                    {{WRAPPER}} .premium-div-svg .premium-separator-divider-left,
                    {{WRAPPER}} .premium-separator-divider-bg hr' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'left_and_right_separator_top_space',
			array(
				'label'      => __( 'Space Between Lines', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-divider-left hr,{{WRAPPER}} .premium-separator-divider-right hr' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sep_amount',
			array(
				'label'       => __( 'Amount', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 0.1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => '20',
				),
				'label_block' => true,
				'condition'   => array(
					'left_and_right_separator_type' => $bg_shapes,
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-divider-bg hr' => 'mask-size: {{SIZE}}px 100%; -webkit-mask-size: {{SIZE}}px 100%',
				),
			)
		);

		$this->add_control(
			'left_separator_heading',
			array(
				'label'     => __( 'Left', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'left_sep_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'left_separator_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'left_and_right_separator_type!' => array( 'custom', 'gradient' ),
					'left_sep_sw'                    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-divider-left hr' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-custom_svg .premium-separator-divider-left svg,
                     {{WRAPPER}} .premium-separator-custom_svg .premium-separator-divider-left svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-divider-left .premium-no-fill, {{WRAPPER}} .premium-separator-divider-left .premium-no-fill *' => 'stroke: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-curvedtop .premium-separator-left-side hr' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .premium-separator-shadow .premium-separator-left-side hr,
                     {{WRAPPER}} .premium-separator-divider-bg .premium-separator-left-side hr' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_separator_slices',
			array(
				'label'       => __( 'Number of Slices', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'left_and_right_separator_type' => array( 'custom' ),
					'left_sep_sw'                   => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-custom .premium-separator-left-side hr' => 'border-image-slice: {{SIZE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'left_shadow',
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'condition' => array(
					'left_and_right_separator_type' => array( 'shadow' ),
					'left_sep_sw'                   => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-separator-shadow .premium-separator-left-side hr',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'left_background',
				'types'     => array( 'gradient' ),
				'condition' => array(
					'left_and_right_separator_type' => array( 'gradient' ),
					'left_sep_sw'                   => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-separator-gradient .premium-separator-left-side hr',
			)
		);

        $this->add_responsive_control(
			'left_rotate',
			array(
				'label'      => __( 'Rotate (degrees)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
                'condition' => array(
					'left_sep_sw'                  => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-left-side' => 'transform-origin: center; transform: rotate({{SIZE}}{{UNIT}})',
				),
			)
		);

		$this->add_control(
			'right_separator_heading',
			array(
				'label'     => __( 'Right', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'right_sep_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'right_separator_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'left_and_right_separator_type!' => array( 'custom', 'gradient' ),
					'right_sep_sw'                   => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-divider-right hr' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-custom_svg .premium-separator-divider-right svg,
                    {{WRAPPER}} .premium-separator-custom_svg .premium-separator-divider-right svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-divider-right .premium-no-fill,
                     {{WRAPPER}} .premium-separator-divider-right .premium-no-fill *' => 'stroke: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-curvedtop .premium-separator-right-side hr' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .premium-separator-shadow .premium-separator-right-side hr, {{WRAPPER}} .premium-separator-divider-bg .premium-separator-right-side hr' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'right_separator_slices',
			array(
				'label'       => __( 'Number of Slices', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'label_block' => true,
				'condition'   => array(
					'left_and_right_separator_type' => array( 'custom' ),
					'right_sep_sw'                  => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-custom .premium-separator-right-side hr' => 'border-image-slice: {{SIZE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'right_shadow',
				'label'     => __( 'Gradient', 'premium-addons-pro' ),
				'condition' => array(
					'left_and_right_separator_type' => array( 'shadow' ),
					'right_sep_sw'                  => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-separator-shadow .premium-separator-right-side hr',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'right_background',
				'types'     => array( 'gradient' ),
				'condition' => array(
					'left_and_right_separator_type' => array( 'gradient' ),
					'right_sep_sw'                  => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-separator-gradient .premium-separator-right-side hr',
			)
		);

        $this->add_responsive_control(
			'right_rotate',
			array(
				'label'      => __( 'Rotate (degrees)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
                'condition' => array(
					'right_sep_sw'                  => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-right-side' => 'transform-origin: center; transform: rotate({{SIZE}}{{UNIT}})',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds Element Style Controls.
	 *
	 * @param Boolean $draw_icon  true if the svg draw feature is enabled for the widget.
	 */
	private function add_element_style_controls( $draw_icon ) {

		$this->start_controls_section(
			'separator_content_tab',
			array(
				'label'     => __( 'Element', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'element_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_txt_heading',
			array(
				'label'     => __( 'Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'content_text!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'text_typhography',
				'condition' => array(
					'content_text!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-separator-icon-text',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'text_tshadow',
				'selector'  => '',
				'condition' => array(
					'content_text!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-separator-icon-text',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'content_text!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'content_text!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container:hover .premium-separator-icon-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_size',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'range'       => array(
					'em' => array(
						'min' => 0,
						'max' => 25,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-icon-wrap i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-separator-icon-wrap svg'  => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
				'condition'   => array(
					'content_inside_separator' => 'font_awesome_icon',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-icon-container svg, {{WRAPPER}} .premium-separator-icon-container img, {{WRAPPER}} .premium-separator-icon-container .premium-lottie-animation' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'content_inside_separator' => array( 'animation', 'svg', 'custom_image' ),
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-icon-container svg, {{WRAPPER}} .premium-separator-icon-container img,{{WRAPPER}} .premium-separator-icon-container .premium-lottie-animation' => 'height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'content_inside_separator' => array( 'animation', 'svg', 'custom_image' ),
				),
			)
		);

		$this->add_responsive_control(
			'pa_divider_img_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''        => __( 'Default', 'premium-addons-pro' ),
					'cover'   => __( 'Cover', 'premium-addons-pro' ),
					'fill'    => __( 'Fill', 'premium-addons-pro' ),
					'contain' => __( 'Contain', 'premium-addons-pro' ),
				),
				'default'   => '',
				'condition' => array(
					'content_inside_separator' => 'custom_image',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container img' => 'object-fit: {{VALUE}};',
				),

			)
		);

		$this->add_control(
			'separator_content_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-wrap i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-icon-wrap .premium-drawable-icon *,
					{{WRAPPER}} .premium-separator-icon-container .premium-separator-icon-wrap svg:not([class*="premium-"]),
					{{WRAPPER}} .premium-separator-icon-wrap:not(.premium-lottie-animation) svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'content_inside_separator!' => array( 'custom_image', 'animation' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-separator-icon-container .premium-drawable-icon *,
                         {{WRAPPER}} .premium-separator-icon-wrap svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'content_inside_separator' => array( 'font_awesome_icon', 'svg' ),
					'draw_svg'                 => 'yes',
				),
			)
		);

		$this->add_control(
			'separator_content_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'separator' => 'before',
				'condition' => array(
					'content_inside_separator' => array( 'font_awesome_icon', 'text' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-container:hover .premium-separator-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-separator-container:hover .premium-separator-icon-wrap .premium-drawable-icon *,
					{{WRAPPER}} .premium-separator-container:hover .premium-separator-icon-wrap svg:not([class*="premium-"]),
					{{WRAPPER}} .premium-separator-container:hover .premium-separator-icon-wrap:not(.premium-lottie-animation) svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_cont_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'separator_content_background',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'separator_content_box_shadow',
				'selector' => '{{WRAPPER}} .premium-separator-icon-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'separator_content_border',
				'selector' => '{{WRAPPER}} .premium-separator-icon-container',
			)
		);

		$this->add_control(
			'separator_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'separator_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'separator_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'separator_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'separator_adv_radius' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'separator_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'separator_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-icon-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds Container Controls.
	 */
	private function add_container_style() {

		$this->start_controls_section(
			'container_style',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_and_separator_size',
			array(
				'label'       => __( 'Container Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%', 'custom' ),
				'label_block' => true,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
					'em' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'     => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-separator-inner' => 'width: {{SIZE}}{{UNIT}};',
				),
				// 'conditions'  => $sep_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-separator-container',
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-separator-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Divider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$left_sep  = 'yes' === $settings['left_sep_sw'];
		$right_sep = 'yes' === $settings['right_sep_sw'];
		$divider   = 'yes' === $settings['element_sw'];

		if ( $left_sep || $right_sep ) {
			$separator_list = $this->get_divider_pattern();
			$separator      = $settings['left_and_right_separator_type'];
			$has_patterns   = ( false !== strpos( $separator, 'pattern-', 0 ) ) || ( 'custom_svg' === $separator );
			$custom_sep     = 'custom' === $separator;
			$bg_sep         = false !== strpos( $separator, 'div-bg-', 0 );
			$sep_draw       = false;
			$is_bordered    = in_array( $separator, array( 'solid', 'double', 'dotted', 'dashed', 'groove', 'shadow', 'gradient', 'custom' ), true );

			if ( $bg_sep ) {

				$this->add_render_attribute( 'container', 'class', 'premium-separator-divider-bg' );

				$repeated_bg_sep = false !== strpos( $separator, 'div-bg-r-', 0 );
				$sep_bg          = $separator_list[ $separator ];

				if ( $repeated_bg_sep ) {
					$this->add_render_attribute( 'container', 'class', 'premium-bg-repeat-x' );
				} else {

					if ( ! in_array( $separator, array( 'div-bg-diamond', 'div-bg-para', 'div-bg-rect' ), true ) ) {
						$sep_bg = $this->get_sep_svg( $sep_bg, $settings['sep_bg_stroke_width'] );
					}
				}

				$this->add_render_attribute( 'container', 'style', '--pa-divider-bg: url("' . $sep_bg . '")' );
			}

			if ( $has_patterns ) {
				$this->add_render_attribute( 'container', 'class', 'premium-div-svg' );

				if ( 'custom_svg' !== $separator ) {
					$separator = $separator_list[ $separator ];
				}

				$sep_draw = 'yes' === $settings['sep_draw_svg'];

				if ( $sep_draw ) {
					$class_arr = array(
						'elementor-invisible',
						'premium-svg-drawer',
						'premium-drawable-icon',
					);

					$svg_arr = array(
						'data-svg-reverse'     => $settings['sep_lottie_reverse'],
						'data-svg-loop'        => $settings['sep_lottie_loop'],
						'data-svg-sync'        => $settings['sep_svg_sync'],
						'data-svg-frames'      => $settings['sep_frames'],
						'data-svg-anim-offset' => $settings['sep_animate_offset']['size'] . $settings['sep_animate_offset']['unit'],
						'data-svg-yoyo'        => $settings['sep_svg_yoyo'],
						'data-svg-point'       => $settings['sep_lottie_reverse'] ? $settings['sep_end_point']['size'] : $settings['sep_start_point']['size'],
					);
				}
			} else {
				$no_of_lines = $settings['content_lines_Number'];
			}

			if ( $left_sep ) {
				$this->add_render_attribute( 'left_sep', 'class', array( 'premium-separator-divider-left', 'premium-separator-left-side' ) );

				if ( $sep_draw ) {

					$this->add_render_attribute(
						'left_sep',
						array_merge(
							array( 'class' => $class_arr ),
							$svg_arr
						)
					);
				} else {
					$this->add_render_attribute( 'left_sep', 'class', 'premium-svg-nodraw' );
				}

				if ( $custom_sep ) {
					$this->add_render_attribute( 'left_sep', 'data-background', $settings['left_separator_image']['url'] );
				}
			}

			if ( $right_sep ) {

				$this->add_render_attribute( 'right_sep', 'class', array( 'premium-separator-divider-right', 'premium-separator-right-side' ) );

				if ( $sep_draw ) {
					$this->add_render_attribute(
						'right_sep',
						array_merge(
							array( 'class' => $class_arr ),
							$svg_arr
						)
					);
				} else {
					$this->add_render_attribute( 'right_sep', 'class', 'premium-svg-nodraw' );
				}

				if ( $custom_sep ) {
					$this->add_render_attribute( 'right_sep', 'data-background', $settings['right_separator_image']['url'] );
				}
			}
		}

		if ( $divider ) {
			$icon_type = $settings['content_inside_separator'];
			$text      = $settings['content_text'];

			if ( ! empty( $text ) ) {
				$text_tag = PAPRO_Helper::validate_html_tag( $settings['content_text_tag'] );

				$this->add_inline_editing_attributes( 'content_text', 'basic' );

				$this->add_render_attribute( 'content_text', 'class', 'premium-separator-icon-text' );
			}

			if ( $icon_type ) {

				if ( 'font_awesome_icon' === $icon_type || 'svg' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

					if ( 'svg' === $icon_type ) {
						$this->add_render_attribute( 'icon', 'class', 'premium-separator-icon-wrap' );
					}

					if ( 'yes' === $settings['draw_svg'] ) {

						$this->add_render_attribute(
							'container',
							'class',
							array(
								'elementor-invisible',
							)
						);

						if ( 'font_awesome_icon' === $icon_type ) {
							$this->add_render_attribute( 'icon', 'class', $settings['divider_icon']['value'] );
						}

						$this->add_render_attribute(
							'icon',
							array(
								'class'                => 'premium-svg-drawer',
								'data-svg-reverse'     => $settings['lottie_reverse'],
								'data-svg-loop'        => $settings['lottie_loop'],
								'data-svg-sync'        => $settings['svg_sync'],
								'data-svg-anim-offset' => $settings['animate_offset']['size'] . $settings['animate_offset']['unit'],
								'data-svg-fill'        => $settings['svg_color'],
								'data-svg-frames'      => $settings['frames'],
								'data-svg-yoyo'        => $settings['svg_yoyo'],
								'data-svg-point'       => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
							)
						);

					} else {

						$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );

					}
				} elseif ( 'animation' === $icon_type ) {

					$this->add_render_attribute(
						'separator_lottie',
						array(
							'class'               => array(
								'premium-separator-icon-wrap',
								'premium-lottie-animation',
							),
							'data-lottie-url'     => $settings['lottie_url'],
							'data-lottie-loop'    => $settings['lottie_loop'],
							'data-lottie-reverse' => $settings['lottie_reverse'],
						)
					);

				}
			}
		}

		$separator_link_type = $settings['content_link_type'];

		if ( 'url' === $separator_link_type ) {

			$this->add_link_attributes( 'link', $settings['content_url'] );

		} elseif ( 'link' === $separator_link_type ) {

			$this->add_render_attribute( 'link', 'href', get_permalink( $settings['content_existing_page'] ) );

		}

		$this->add_render_attribute( 'link', 'class', 'premium-separator-item-link' );

		if ( $is_bordered ) {
			$this->add_render_attribute( 'container', 'class', 'premium-bordered-top' );
		}

		$this->add_render_attribute(
			'container',
			array(
				'class'         => array(
					'premium-separator-container',
					'premium-separator-' . $settings['left_and_right_separator_type'],
				),
				'data-settings' => $settings['left_and_right_separator_type'],
			)
		);

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div class="premium-separator-wrapper">
				<div class="premium-separator-wrapper-separator">
					<div class="premium-separator-wrapper-separator-divider">
						<div class="premium-separator-inner">
							<?php if ( 'yes' === $settings['content_link_switcher'] ) : ?>
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
							<?php endif; ?>

								<div class="premium-separator-content-wrapper">
									<?php if ( $left_sep ) : ?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'left_sep' ) ); ?>>
											<?php
											if ( $has_patterns ) {

												if ( 'custom_svg' === $separator ) {

													Icons_Manager::render_icon(
														$settings['left_custom_svg'],
														array(
															'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
															'aria-hidden' => 'true',
														)
													);

												} else {
													echo $separator;
												}
											} else {
												echo $no_of_lines;
											}
											?>
										</div>
									<?php endif; ?>

									<?php if ( $divider ) : ?>
										<div class="premium-separator-icon-container">

											<?php if ( ! empty( $text ) ) : ?>
												<div class="premium-separator-icon-wrap premium-separator-text-icon">
													<<?php echo wp_kses_post( $text_tag . ' ' . $this->get_render_attribute_string( 'content_text' ) ); ?>>
														<?php echo wp_kses_post( $settings['content_text'] ); ?>
													</<?php echo wp_kses_post( $text_tag ); ?>>

												</div>
											<?php endif; ?>

											<?php if ( 'font_awesome_icon' === $icon_type ) : ?>
												<div class="premium-separator-icon-wrap premium-separator-icon">
													<?php
													if ( 'yes' !== $settings['draw_svg'] ) :
														Icons_Manager::render_icon(
															$settings['divider_icon'],
															array(
																'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
																'aria-hidden' => 'true',
															)
														);
													else :
														?>
														<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
													<?php endif; ?>
												</div>
											<?php elseif ( 'svg' === $icon_type ) : ?>
												<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
													<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
												</div>
											<?php elseif ( 'custom_image' === $icon_type ) : ?>
												<div class="premium-separator-icon-wrap premium-separator-img-icon">
													<?php PAPRO_Helper::get_attachment_image_html( $settings, 'thumbnail', 'content_image' ); ?>
												</div>
											<?php else : ?>
												<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'separator_lottie' ) ); ?>></div>
											<?php endif; ?>
										</div>
									<?php endif; ?>

									<?php if ( $right_sep ) : ?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'right_sep' ) ); ?>>
											<?php
											if ( $has_patterns ) {
												if ( 'custom_svg' === $separator ) {

													Icons_Manager::render_icon(
														$settings['right_custom_svg'],
														array(
															'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
															'aria-hidden' => 'true',
														)
													);

												} else {
													echo $separator;
												}
											} else {
												echo $no_of_lines;
											}
											?>
										</div>
									<?php endif; ?>
								</div>

							<?php if ( 'yes' === $settings['content_link_switcher'] ) : ?>
								</a>
							<?php endif; ?>

							</div>

						</div>

				</div>

				<div class="premium-clearfix"></div>

			</div>

		</div>

		<?php

	}

	private function get_divider_pattern() {
		return array(
			'pattern-1'        => '<svg xmlns="http://www.w3.org/2000/svg" class="premium-no-fill" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 1200.2 60.9" width="1200.2" height="60.9"><path fill="none" stroke="#798184" stroke-miterlimit="10" stroke-width="2" d="M629,31c0,0,111.8-7.5,150.6-5c0,0,25.2,2.4,22.5-21.7c0,0-24.1-5-21.2,21.7c0,0-3.7,24.3,21.2,21.7c0,0,5-22.7-21.2-21.7c0,0,13.8,20,29.8,0.5c0,0-13.3-19.9-29.8-0.5l29.8,0.5c0,0,100.6,3.6,196.6,15.9c96.8,12.4,154.8-0.9,190.6-9.9"></path><path fill="none" stroke="#798184" stroke-miterlimit="10" stroke-width="2" d="M599.1,30.4c0,0-8.3-4-8.6-14.9s8.6-14.9,8.6-14.9s8.7,4.4,8.6,14.9S595.3,39,584.2,39c-11.1,0.1-14.9-8.6-14.9-8.6s4.5-8.7,14.9-8.6c10.4,0.1,23.4,12.6,23.5,23.5s-8.6,14.9-8.6,14.9s-8.5-4.4-8.6-14.9s12.1-23.4,23.5-23.5C625.5,21.6,629,31,629,31s-5.5,8.4-14.9,8c-9.4-0.3-23.2-8.9-10.5-25.3c0,0,5.8-7.1,16.7-4.5c0,0,2.8,9.8-4.5,16.7c-7.3,6.9-18.3,5.6-18.3,5.6s-10.1,0-15-5.6s-5.8-11.7-4.5-16.7c0,0,10.3-2.9,16.7,4.5c6.4,7.4,9.2,22.3,0,33.3c0,0-4.7,6.4-16.7,4.5c0,0-2.4-11,4.5-16.7s21.5-9.2,33.3,0c0,0,6.4,4.2,4.5,16.7c0,0-9.2,2.9-16.7-4.5c0,0-8.4-8.9-3.5-17"></path><path fill="none" stroke="#798184" stroke-miterlimit="10" stroke-width="2" d="M569.3,30.3c-86.1,2.1-151.9-4.4-151.9-4.4s-23.5,2-21.2-21.7c0,0,24.1-4.7,21.2,21.7c0,0,3.5,24.4-21.3,21.8c0,0-4.5-23.4,21.3-21.8c0,0-13.7,20-29.9,0.5c0,0,13.4-19.7,29.9-0.5c0,0-96.2,0-176.2,12c-10.1,1.5-92.9,11.5-135.6,9.4c-7.7-0.4-58-0.2-105.4-15"></path></svg>',
			'pattern-2'        => '<svg xmlns="http://www.w3.org/2000/svg" class="premium-no-fill" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 689.5 115.3" width="689.5" height="115.3"><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M21.1,53.9c6.4,0,19.1-6.6,20.4-25c1.3-17.4-18.9-15-19.9-4.3c0,0-10-15.3-19.1-3.5c-5.4,7,4.5,29.9,21.1,33.5c33.6,7.1,40,16.2,42.6,24c5.5,16.3-11.8,23.6-19,13c0,0-10.4,14-19,1.3C23.8,86.5,24.9,76,34,66c8.2-8.9,19.8-13.8,32-13.8H113c12.1,0,23.8-4.9,31.9-13.8c9.1-9.9,10.2-20.5,5.8-26.9c-8.6-12.7-19,1.3-19,1.3c-7.2-10.6-24.5-3.3-19,13c2.6,7.8,9,16.9,42.6,24c16.7,3.5,26.6,26.5,21.1,33.5c-9.1,11.8-19.1-3.5-19.1-3.5c-1,10.7-22.8,12.9-19.9-4.3c3.6-20.6,19.8-23.3,26.2-23.3c6.4,0,525.8,0,525.8,0"></path><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="105.9" cy="40.4" r="4.1"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="47.3" cy="112.5" r="2.3"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="181.5" cy="98.3" r="2.8"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="75.2" cy="4.1" r="3.6"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="178.6" cy="35.2" r="2.9"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="70.1" cy="64.4" r="2.5"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="226.2" cy="67" r="2.5"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="254.8" cy="32.3" r="2.5"></circle><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M120.2,78.6c0,0,1.4-1.7,2.4,0.1c0.9,1.6-2.4,3.5-2.4,3.5s-2.8-1.5-2.8-2.9C117.2,77.8,118.8,77.5,120.2,78.6z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M298.3,70.8c0,0,2.2-2.7,3.9,0.1c1.5,2.6-3.9,5.6-3.9,5.6s-4.4-2.5-4.6-4.6C293.6,69.5,296.1,68.9,298.3,70.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M47.5,43c0,0,1.4-1.7,2.4,0.1c0.9,1.6-2.4,3.5-2.4,3.5s-2.8-1.5-2.8-2.9C44.5,42.2,46.1,41.9,47.5,43z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M211.2,8.9c0,0,2.2-2.7,3.9,0.1c1.5,2.6-3.9,5.6-3.9,5.6s-4.4-2.5-4.6-4.6C206.5,7.5,209.1,7,211.2,8.9z"></path><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="8.5" cy="69.9" r="3.3"></circle></svg>',
			'pattern-3'        => '<svg xmlns="http://www.w3.org/2000/svg" class="premium-no-fill" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 1012.7 69.2" width="1012.7" height="69.2"><g><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M552.4,34.7c0,0,163.4,0,164.4,0c1,0,11.7-3.7,14.2-13.2c2.5-9.5-13.1,2-14,12s11.7,18.4,14.3,19.6c2.6,1.2,4.3-10.6-14.4-18.3c0,0,60.8,0.1,62.6,0c1.8-0.1,10.9-5.6,13-11.7c2.1-6.1-11.3,2.8-12.9,11.1s11.6,15.6,13.2,15.6s-1-9.2-13.2-15c0,0,56.9,0.4,60.6,0c3.3-0.3,13.9-8.6,13.4-13.2C853,17,840.6,31.1,840,34.7c-0.5,3.6,8.7,14,13.4,14.6c4.7,0.6-6-12.3-13.4-14.6c0,0,56.5,0,59.4,0c2.1,0,10.1-7.7,8.6-12.5s-10.2,6.9-8.6,12.5s2.6,9.9,7.2,12c5.5,2.4,1.3-8.6-7.2-12h78.2c0,0,14.6,13.5,34.2,0c0,0-18.4-13.9-34.2,0"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M458.6,34.7c0,0-163.4,0-164.4,0S282.5,31,280,21.5s13.1,2,14,12c1,9.9-11.7,18.4-14.3,19.6c-2.6,1.2-4.3-10.6,14.4-18.3c0,0-60.8,0.1-62.6,0c-1.8-0.1-10.9-5.6-13-11.7s11.3,2.8,12.9,11.1c1.6,8.2-11.6,15.6-13.2,15.6c-1.6,0,1-9.2,13.2-15c0,0-56.9,0.4-60.6,0c-3.3-0.3-13.9-8.6-13.4-13.2s12.9,9.5,13.4,13.2c0.5,3.6-8.7,14-13.4,14.6c-4.7,0.6,6-12.3,13.4-14.6c0,0-56.5,0-59.4,0c-2.1,0-10.1-7.7-8.6-12.5c1.4-4.7,10.2,6.9,8.6,12.5c-1.5,5.6-2.6,9.9-7.2,12c-5.5,2.4-1.3-8.6,7.2-12H33.3c0,0-15.5,11.5-32.4,0c0,0,17.7-13.7,32.4,0"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M506.3,34.8L488.7,17c0,0-7.1-7.8,1.3-14.5c8.4-6.7,16.4,5.3,16.4,5.3s8.9-13,17.7-4.2c6.4,6.3,0,13.4,0,13.4l-18.2,18.2l-17.2,17.2c0,0-6.7,6.8-13.4,0c-6.8-6.8,0.4-14.7,4.2-17.7c0,0-12.3-7.9-4.2-17.7c0,0,6.7-7.2,15,1.6c8.4,8.9,33.8,33.8,33.8,33.8s6.7,6.5,0,13.4c-6.6,6.9-15-0.6-17.7-4.2c0,0-9.6,12.7-17.7,4.2c-7-7.4,1.7-15.1,1.7-15.1l34.9-34.8c0,0,6.9-4.3,12.2,1.1c5.3,5.4,2.5,13.5-4.2,17.7c0,0,12.5,8.5,4.2,17.7c-6.5,7.3-15-1.5-15-1.5L506.3,34.8"></path>/g></svg>',
			'pattern-4'        => '<svg xmlns="http://www.w3.org/2000/svg" class="premium-no-fill" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 818.8 45.3" width="818.8" height="45.3"><g><g><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M320.4,24.1c0,0-1.3-6.5-13.3-8.7C307.1,15.4,307.4,23.3,320.4,24.1z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M291.5,39.3c0,0,10.4-0.8,13.9-8.3C305.5,31,292.9,31.1,291.5,39.3z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M255.2,40.3c0,0,10.4-0.8,13.9-8.3C269.1,31.9,256.5,32.1,255.2,40.3z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M215.6,38.2c0,0,10.4-0.8,13.9-8.3C229.6,29.8,216.9,30,215.6,38.2z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M174.7,34c0,0,10.4-0.8,13.9-8.3C188.6,25.7,176,25.8,174.7,34z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M134.7,28.8c0,0,10.4-0.8,13.9-8.3C148.6,20.5,136,20.7,134.7,28.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M92.4,27.2c0,0,10.4-0.8,13.9-8.3C106.4,18.8,93.8,19,92.4,27.2z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M52.4,28.3c0,0,10.4-0.8,13.9-8.3C66.4,19.9,53.8,20.1,52.4,28.3z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M0.5,29.7c0,0,10.5,0.3,16-6.7C16.5,23,4.1,21.8,0.5,29.7z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M288.4,25.9c0,0-1.3-6.5-13.3-8.7C275,17.2,275.3,25.1,288.4,25.9z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M251.5,25.9c0,0-1.3-6.5-13.3-8.7C238.1,17.2,238.4,25.1,251.5,25.9z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M213.9,22.2c0,0-1.3-6.5-13.3-8.7C200.6,13.5,200.8,21.3,213.9,22.2z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M173.8,17.5c0,0-1.3-6.5-13.3-8.7C160.5,8.8,160.7,16.7,173.8,17.5z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M129.4,14.1c0,0-1.3-6.5-13.3-8.7C116,5.4,116.3,13.3,129.4,14.1z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M87.8,13.5c0,0-1.3-6.5-13.3-8.7C74.4,4.7,74.7,12.6,87.8,13.5z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M49,15.7c0,0-1.3-6.5-13.3-8.7C35.6,7,35.9,14.8,49,15.7z"></path></g><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="410.7" cy="22.2" r="7.2"></circle><circle fill="none" stroke="#798184" stroke-width="2" stroke-dasharray="3.862,2.8965" cx="410.7" cy="22.2" r="10.8"></circle><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M428.7,4.1c6.7,6.3,6.8,11.5-0.5,16.7c-1.3,0.9-1.4,2.7-0.3,3.8c3,3,6.9,8.7,0.7,14.9c-6.1,6.1-11.9,2.4-14.9-0.4c-1.1-1-2.8-0.9-3.8,0.3c-2.8,3.7-8.9,9.2-16.6,1.5c-5.8-5.8-4.9-11.7,1.3-16.5c1.3-1,1.3-2.8,0.1-3.9c-3.5-3.1-8.4-9.4-1.6-16.1c7.3-7.3,13.5-2.7,16.5,0.6c1,1.1,2.8,1.1,3.8,0C416.2,1.8,421.7-2.5,428.7,4.1z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M389,22.5c-2.4,10.2-11,11-11,11c0.2-3.8,1.9-6.3,3.9-7.9c0.1-0.1,0-0.3-0.1-0.3c-2.6,0.2-5.6-0.3-8.4-2.8c0,0,3.6-2.9,8.9-2.4c0.2,0,0.3-0.2,0.1-0.3c-1.4-1-4.7-4-4.5-8.4C378,11.4,386.6,12.3,389,22.5z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M435,21.5c2.4-10.2,11-11,11-11c-0.2,3.8-1.9,6.3-3.9,7.9c-0.1,0.1,0,0.3,0.1,0.3c2.6-0.2,5.6,0.3,8.4,2.8c0,0-3.6,2.9-8.9,2.4c-0.2,0-0.3,0.2-0.1,0.3c1.4,1,4.7,4,4.5,8.4C446.1,32.6,437.5,31.7,435,21.5z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M373.4,22.5c0,0-18.1-1.1-39.5,2.2c-20.8,3.5-70.1,8.7-146.2-1.8C85.3,8.7,16.1,23.2,16.1,23.2"></path><g><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M502.5,22.8c0,0,1.3-6.5,13.2-8.7C515.7,14.1,515.4,22,502.5,22.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M531,38c0,0-10.3-0.8-13.7-8.3C517.3,29.6,529.7,29.8,531,38z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M566.9,38.9c0,0-10.3-0.8-13.7-8.3C553.2,30.6,565.6,30.8,566.9,38.9z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M605.9,36.8c0,0-10.3-0.8-13.7-8.3C592.2,28.5,604.7,28.7,605.9,36.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M646.3,32.6c0,0-10.3-0.8-13.7-8.3C632.6,24.3,645.1,24.5,646.3,32.6z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M685.8,27.5c0,0-10.3-0.8-13.7-8.3C672.1,19.1,684.6,19.3,685.8,27.5z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M727.5,25.8c0,0-10.3-0.8-13.7-8.3C713.8,17.5,726.2,17.6,727.5,25.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M767,26.9c0,0-10.3-0.8-13.7-8.3C753.3,18.6,765.7,18.8,767,26.9z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M818.3,28.4c0,0-10.4,0.3-15.8-6.7C802.5,21.7,814.8,20.5,818.3,28.4z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M534.1,24.6c0,0,1.3-6.5,13.2-8.7C547.3,15.9,547,23.7,534.1,24.6z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M570.6,24.6c0,0,1.3-6.5,13.2-8.7C583.7,15.9,583.4,23.7,570.6,24.6z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M607.7,20.8c0,0,1.3-6.5,13.2-8.7C620.8,12.1,620.5,20,607.7,20.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M647.2,16.1c0,0,1.3-6.5,13.2-8.7C660.4,7.4,660.1,15.3,647.2,16.1z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M691.1,12.8c0,0,1.3-6.5,13.2-8.7C704.2,4.1,704,11.9,691.1,12.8z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M732.1,12.1c0,0,1.3-6.5,13.2-8.7C745.3,3.4,745,11.3,732.1,12.1z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M770.4,14.3c0,0,1.3-6.5,13.2-8.7C783.6,5.6,783.3,13.5,770.4,14.3z"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M450.7,21.5c0,0,16.4-0.2,49,3.7c43.8,5.3,111.4-0.3,144.9-5c53.1-7.4,132.5-5.6,157.9,1.5"></path></g></g></svg>',
			'pattern-5'        => '<svg xmlns="http://www.w3.org/2000/svg" class="premium-no-fill" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 1214 62.1" width="1214" height="62.1"><g><g><circle fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" cx="606.9" cy="29" r="8.4"></circle><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M600.2,24c0,0-7.5-12.5-7.3-16c0.2-3.5,18.3-5.3,21.8-1.7c3,3-1.4,12.3-2.8,15c-0.1,0.2,0.1,0.3,0.2,0.2c2.3-2,10.5-8.4,14.9-6.7c4.3,1.6,6.3,15.5,4.7,17.4c-1.6,1.9-11.2,2.3-16.5-0.4c0,0,13.2,7.6,11.5,13.2c-1.7,5.6-10.9,10.6-12.7,9.3c-2.5-1.6-8.6-9.6-7.1-16.9c0,0-5.9,20.3-14,17.2c-8.1-3.1-11.1-12-11-13.2c0.1-1.2,4.3-8.3,17.2-9.4c0,0-9.1,1.1-16.1-2.4s-1.3-11.8,1-14.1C587,12.5,595.7,18.9,600.2,24z"></path></g><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M615.2,31.8c0,0,26.7,27.3,196.6,5.1l6.7-1.1c0,0,13.7-6.1,18.4-13c0,0-8.4-0.4-6.9-8.8c1.5-8.2,13.7-6.8,18.7-13c0,0,3.6,13.6-2.3,19.9c-4,4.3-8.4,2.5-8.4,2.5s-2.7,5.3-15.2,11.9c0,0,42.1-2.5,54.2,0c0,0,2.5,0.7,4.4,4c5.4,10.3,9.8,12.5,9.8,12.5s-1.4,3.6,2.1,6.9c5.2,4.9,16.4,1.9,16.4,1.9c-5.1-4.1-3.9-14.1-10.7-15.4c-6.9-1.3-7.2,5.6-7.2,5.6c-5.7-3.9-10.7-15.2-10.7-15.2s30.4,2.5,41.5,0c0,0,5.7-1.6,8.5-3c0,0,11.2-5,15.2-10.7c0,0-6.9-0.3-5.6-7.2c1.3-6.8,11.3-5.6,15.4-10.7c0,0,3,11.2-1.9,16.4c-3.3,3.5-6.9,2.1-6.9,2.1s-2.2,4.4-12.5,9.8c0,0,25.9-2.7,36.8,0c2.5,0.6,12.7,3.8,12.7,3.8s2,3.4,3.6,5.9c4.4,6.9,7.8,8.8,7.8,8.8s-1.1,2.8,1.6,5.3c4,3.7,12.6,1.4,12.6,1.4c-3.9-3.2-3-10.9-8.2-11.8c-5.3-1-5.5,4.3-5.5,4.3c-4.3-3-9.8-13.9-9.8-13.9s17.8,2.4,32.3,1.8c7.1-0.3,23.3-2.3,23.3-2.3s9-4,12.1-8.5c0,0-5.5-0.3-4.5-5.8c1-5.4,9-4.4,12.3-8.5c0,0,2.4,8.9-1.5,13.1c-2.6,2.8-5.5,1.7-5.5,1.7s-1.8,3.5-10,7.8c0,0,34.7-5.4,45.7,5.1c3.7,6.9,6.6,8.4,6.6,8.4s-1,2.4,1.4,4.7c3.5,3.3,11.1,1.3,11.1,1.3c-3.5-2.8-2.7-9.6-7.2-10.4c-4.7-0.9-4.9,3.8-4.9,3.8c-3.8-2.7-7.2-10.2-7.2-10.2s31.6,2.4,42.9-2.3c0,0,7.7-2,10.7-5.2c0,0-4.3-0.9-2.8-5.2c1.5-4.2,7.7-2.3,10.9-5.2c0,0,0.7,7.4-2.9,10.2c-2.5,1.9-4.6,0.6-4.6,0.6s-1.9,2.5-8.9,4.9c0,0,10.7,8.4,31.7,6.8s23.6-1.4,23.6-1.4s-4.8-7.7,2.4-10s15.8,6.9,20,5.8c0,0-6.9,13.5-19,9.1c0,0-1.5-0.8-3.4-4.9"></path><path fill="none" stroke="#798184" stroke-width="2" stroke-miterlimit="10" d="M598.8,31.8c0,0-46.5,19.1-196.6,5.1l-6.7-1.1c0,0-13.7-6.1-18.4-13c0,0,8.4-0.4,6.9-8.8c-1.5-8.2-13.7-6.8-18.7-13c0,0-3.6,13.6,2.3,19.9c4,4.3,8.4,2.5,8.4,2.5s2.7,5.3,15.2,11.9c0,0-42.1-2.5-54.2,0c0,0-2.5,0.7-4.4,4c-5.4,10.3-9.8,12.5-9.8,12.5s1.4,3.6-2.1,6.9c-5.2,4.9-16.4,1.9-16.4,1.9c5.1-4.1,3.9-14.1,10.7-15.4c6.9-1.3,7.2,5.6,7.2,5.6c5.7-3.9,10.7-15.2,10.7-15.2s-30.4,2.5-41.5,0c0,0-5.7-1.6-8.5-3c0,0-11.2-5-15.2-10.7c0,0,6.9-0.3,5.6-7.2c-1.3-6.8-11.3-5.6-15.4-10.7c0,0-3,11.2,1.9,16.4c3.3,3.5,6.9,2.1,6.9,2.1s2.2,4.4,12.5,9.8c0,0-25.9-2.7-36.8,0c-2.5,0.6-12.7,3.8-12.7,3.8s-2,3.4-3.6,5.9c-4.4,6.9-7.8,8.8-7.8,8.8s1.1,2.8-1.6,5.3c-4,3.7-12.6,1.4-12.6,1.4c3.9-3.2,3-10.9,8.2-11.8c5.3-1,5.5,4.3,5.5,4.3c4.3-3,9.8-13.9,9.8-13.9s-17.8,2.4-32.3,1.8c-7.1-0.3-23.3-2.3-23.3-2.3s-9-4-12.1-8.5c0,0,5.5-0.3,4.5-5.8c-1-5.4-9-4.4-12.3-8.5c0,0-2.4,8.9,1.5,13.1c2.6,2.8,5.5,1.7,5.5,1.7s1.8,3.5,10,7.8c0,0-34.7-5.4-45.7,5.1C120,47.5,117,49,117,49s1,2.4-1.4,4.7c-3.5,3.3-11.1,1.3-11.1,1.3c3.5-2.8,2.7-9.6,7.2-10.4c4.7-0.9,4.9,3.8,4.9,3.8c3.8-2.7,7.2-10.2,7.2-10.2S92.3,40.5,81,35.7c0,0-7.7-2-10.7-5.2c0,0,4.3-0.9,2.8-5.2c-1.5-4.2-7.7-2.3-10.9-5.2c0,0-0.7,7.4,2.9,10.2c2.5,1.9,4.6,0.6,4.6,0.6s1.9,2.5,8.9,4.9c0,0-10.7,8.4-31.7,6.8s-23.6-1.4-23.6-1.4s4.8-7.7-2.4-10s-15.8,6.9-20,5.8c0,0,6.9,13.5,19,9.1c0,0,1.5-0.8,3.4-4.9"></path></g></svg>',
			'pattern-6'        => '<svg preserveAspectRatio="none" class="premium-no-fill" xmlns="http://www.w3.org/2000/svg" width="960" height="22" viewBox="0 0 960 22"><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="50.052" cy="11" r="1"></circle><circle cx="11.011" cy="11" r="1"></circle><circle cx="89.093" cy="11" r="1"></circle><circle cx="50.052" cy="11" r="1"></circle><circle cx="128.134" cy="11" r="1"></circle><circle cx="89.093" cy="11" r="1"></circle><circle cx="167.175" cy="11" r="1"></circle><circle cx="128.134" cy="11" r="1"></circle><circle cx="206.216" cy="11" r="1"></circle><circle cx="167.175" cy="11" r="1"></circle><circle cx="245.257" cy="11" r="1"></circle><circle cx="206.216" cy="11" r="1"></circle><circle cx="284.298" cy="11" r="1"></circle><circle cx="245.257" cy="11" r="1"></circle><circle cx="323.339" cy="11" r="1"></circle><circle cx="284.298" cy="11" r="1"></circle><circle cx="362.38" cy="11" r="1"></circle><circle cx="323.339" cy="11" r="1"></circle><circle cx="401.421" cy="11" r="1"></circle><circle cx="362.38" cy="11" r="1"></circle><circle cx="440.462" cy="11" r="1"></circle><circle cx="401.421" cy="11" r="1"></circle><circle cx="479.503" cy="11" r="1"></circle><circle cx="440.462" cy="11" r="1"></circle><circle cx="518.544" cy="11" r="1"></circle><circle cx="479.503" cy="11" r="1"></circle><circle cx="557.585" cy="11" r="1"></circle><circle cx="518.544" cy="11" r="1"></circle><circle cx="596.626" cy="11" r="1"></circle><circle cx="557.585" cy="11" r="1"></circle><circle cx="635.667" cy="11" r="1"></circle><circle cx="596.626" cy="11" r="1"></circle><circle cx="674.708" cy="11" r="1"></circle><circle cx="635.667" cy="11" r="1"></circle><circle cx="713.749" cy="11" r="1"></circle><circle cx="674.708" cy="11" r="1"></circle><circle cx="752.79" cy="11" r="1"></circle><circle cx="713.749" cy="11" r="1"></circle><circle cx="791.831" cy="11" r="1"></circle><circle cx="752.79" cy="11" r="1"></circle><circle cx="830.872" cy="11" r="1"></circle><circle cx="791.831" cy="11" r="1"></circle><circle cx="869.913" cy="11" r="1"></circle><circle cx="830.872" cy="11" r="1"></circle><circle cx="908.954" cy="11" r="1"></circle><circle cx="869.913" cy="11" r="1"></circle><circle cx="947.995" cy="11" r="1"></circle><circle cx="908.954" cy="11" r="1"></circle><path stroke-dasharray="0 6" d="M937.974 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M898.933 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M859.892 11a9.505 9.505 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path stroke-dasharray="0 6" d="M820.851 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M781.81 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M742.769 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M703.728 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M664.687 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M625.646 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M586.605 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M547.564 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M508.523 11a9.505 9.505 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path stroke-dasharray="0 6" d="M469.482 11a9.505 9.505 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path stroke-dasharray="0 6" d="M430.441 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M391.4 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M352.359 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M313.318 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M274.277 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M235.236 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M196.2 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M157.154 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M118.113 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path stroke-dasharray="0 6" d="M79.072 11a9.506 9.506 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path stroke-dasharray="0 6" d="M40.031 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path></g><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"><path d="M957.5 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M933.374 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M918.459 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M894.333 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M879.418 11a9.506 9.506 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M855.292 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M840.377 11a9.506 9.506 0 11-9.506-9.5 9.5 9.5 0 019.506 9.5z"></path><path d="M816.251 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M801.335 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M777.209 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M762.294 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M738.168 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M723.253 11a9.506 9.506 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M699.127 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M684.211 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M660.086 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M645.17 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M621.044 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M606.129 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M582 11a4.9 4.9 0 11-4.9-4.9A4.9 4.9 0 01582 11z"></path><path d="M567.087 11a9.5 9.5 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M542.962 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M528.046 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M503.921 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M489.005 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M464.879 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M449.964 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M425.838 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M410.923 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M386.8 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M371.881 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M347.756 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M332.84 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M308.714 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M293.8 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M269.673 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M254.758 11a9.505 9.505 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path d="M230.632 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M215.716 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M191.591 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M176.675 11a9.505 9.505 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path d="M152.549 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M137.634 11a9.505 9.505 0 11-9.5-9.5 9.5 9.5 0 019.5 9.5z"></path><path d="M113.508 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M98.593 11a9.505 9.505 0 11-9.506-9.5 9.5 9.5 0 019.506 9.5z"></path><path d="M74.467 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M59.551 11a9.505 9.505 0 11-9.505-9.5 9.5 9.5 0 019.505 9.5z"></path><path d="M35.426 11a4.9 4.9 0 11-4.9-4.9 4.9 4.9 0 014.9 4.9z"></path><path d="M20.51 11a9.505 9.505 0 11-9.505-9.5A9.5 9.5 0 0120.51 11z"></path></g></svg>',
			'pattern-7'        => '<svg preserveAspectRatio="none" class="premium-no-fill" xmlns="http://www.w3.org/2000/svg" width="960" height="23" viewBox="0 0 960 23"><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"><path d="M951.73 13.991a4.3 4.3 0 00-3.052-5.261 5.375 5.375 0 00-6.576 3.816 6.721 6.721 0 004.769 8.219 8.4 8.4 0 0010.274-5.965 10.5 10.5 0 00-7.451-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.375 4.2-2.488 6.136a13.13 13.13 0 01-14.746 6.132 10.5 10.5 0 01-7.451-12.843 8.4 8.4 0 0110.274-5.96 6.718 6.718 0 014.768 8.22 5.375 5.375 0 01-6.576 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M894.96 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.72 6.72 0 004.768 8.219 8.4 8.4 0 0010.274-5.965 10.5 10.5 0 00-7.451-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.375 4.2-2.489 6.136a13.128 13.128 0 01-14.745 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.273-5.96 6.717 6.717 0 014.768 8.22 5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M838.189 13.991a4.3 4.3 0 00-3.053-5.261 5.375 5.375 0 00-6.576 3.816 6.721 6.721 0 004.769 8.219A8.4 8.4 0 00843.6 14.8a10.5 10.5 0 00-7.45-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.376 4.2-2.489 6.136a13.13 13.13 0 01-14.746 6.132A10.5 10.5 0 01796.6 7.744a8.4 8.4 0 0110.275-5.96A6.718 6.718 0 01811.637 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.053-5.261"></path><path d="M781.423 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.72 6.72 0 004.768 8.219 8.4 8.4 0 0010.274-5.965 10.5 10.5 0 00-7.451-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.375 4.2-2.488 6.136a13.13 13.13 0 01-14.746 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.27-5.956A6.719 6.719 0 01754.872 10a5.376 5.376 0 01-6.576 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M724.652 13.991A4.3 4.3 0 00721.6 8.73a5.374 5.374 0 00-6.576 3.816 6.719 6.719 0 004.769 8.219 8.4 8.4 0 0010.273-5.965 10.5 10.5 0 00-7.45-12.842 13.127 13.127 0 00-14.872 6.357c-1.071 1.951-1.376 4.2-2.489 6.136a13.129 13.129 0 01-14.746 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96A6.717 6.717 0 01698.1 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M667.9 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.721 6.721 0 004.769 8.219 8.4 8.4 0 0010.276-5.965 10.5 10.5 0 00-7.45-12.842A13.127 13.127 0 00651 8.319c-1.071 1.951-1.375 4.2-2.489 6.136a13.128 13.128 0 01-14.746 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96A6.717 6.717 0 01641.351 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M611.132 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.72 6.72 0 004.768 8.219 8.4 8.4 0 0010.275-5.965A10.5 10.5 0 00609.1 1.962a13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.376 4.2-2.489 6.136a13.13 13.13 0 01-14.746 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96A6.719 6.719 0 01584.581 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.053-5.261"></path><path d="M554.366 13.991a4.3 4.3 0 00-3.052-5.261 5.375 5.375 0 00-6.576 3.816 6.721 6.721 0 004.768 8.219A8.4 8.4 0 00559.78 14.8a10.5 10.5 0 00-7.45-12.842 13.127 13.127 0 00-14.872 6.357c-1.071 1.951-1.375 4.2-2.489 6.136a13.128 13.128 0 01-14.746 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96 6.719 6.719 0 014.768 8.22 5.376 5.376 0 01-6.576 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M497.6 13.991a4.3 4.3 0 00-3.051-5.261 5.375 5.375 0 00-6.577 3.816 6.719 6.719 0 004.769 8.219 8.4 8.4 0 0010.268-5.965 10.5 10.5 0 00-7.45-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.376 4.2-2.489 6.136a13.128 13.128 0 01-14.745 6.132A10.5 10.5 0 01456 7.744a8.4 8.4 0 0110.273-5.96A6.717 6.717 0 01471.044 10a5.375 5.375 0 01-6.576 3.816 4.3 4.3 0 01-3.051-5.261"></path><path d="M440.812 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.721 6.721 0 004.769 8.219 8.4 8.4 0 0010.273-5.965 10.5 10.5 0 00-7.45-12.842A13.127 13.127 0 00423.9 8.319c-1.07 1.951-1.375 4.2-2.489 6.136a13.128 13.128 0 01-14.745 6.132 10.5 10.5 0 01-7.451-12.843 8.4 8.4 0 0110.274-5.96A6.717 6.717 0 01414.26 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M384.042 13.991a4.3 4.3 0 00-3.053-5.261 5.374 5.374 0 00-6.576 3.816 6.72 6.72 0 004.768 8.219 8.4 8.4 0 0010.275-5.965 10.5 10.5 0 00-7.45-12.842 13.128 13.128 0 00-14.873 6.357c-1.07 1.951-1.376 4.2-2.489 6.136a13.13 13.13 0 01-14.744 6.136 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96A6.719 6.719 0 01357.49 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.053-5.261"></path><path d="M327.275 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.721 6.721 0 004.769 8.219 8.4 8.4 0 0010.273-5.965 10.5 10.5 0 00-7.45-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.375 4.2-2.488 6.136a13.13 13.13 0 01-14.747 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96 6.718 6.718 0 014.768 8.22 5.375 5.375 0 01-6.576 3.816 4.3 4.3 0 01-3.048-5.258"></path><path d="M270.505 13.991a4.3 4.3 0 00-3.052-5.261 5.374 5.374 0 00-6.576 3.816 6.718 6.718 0 004.768 8.219 8.4 8.4 0 0010.274-5.965 10.5 10.5 0 00-7.451-12.842A13.127 13.127 0 00253.6 8.319c-1.07 1.951-1.376 4.2-2.489 6.136a13.128 13.128 0 01-14.745 6.132 10.5 10.5 0 01-7.451-12.843 8.4 8.4 0 0110.274-5.96A6.717 6.717 0 01243.953 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M213.756 13.991A4.3 4.3 0 00210.7 8.73a5.374 5.374 0 00-6.576 3.816 6.72 6.72 0 004.768 8.219A8.4 8.4 0 00219.17 14.8a10.5 10.5 0 00-7.451-12.842 13.127 13.127 0 00-14.872 6.357c-1.07 1.951-1.375 4.2-2.489 6.136a13.128 13.128 0 01-14.745 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.273-5.96A6.717 6.717 0 01187.2 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.052-5.261"></path><path d="M156.985 13.991a4.3 4.3 0 00-3.053-5.261 5.375 5.375 0 00-6.576 3.816 6.721 6.721 0 004.769 8.219A8.4 8.4 0 00162.4 14.8a10.5 10.5 0 00-7.45-12.842 13.127 13.127 0 00-14.872 6.357c-1.071 1.951-1.376 4.2-2.489 6.136a13.13 13.13 0 01-14.747 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.275-5.96 6.718 6.718 0 014.766 8.22 5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.053-5.261"></path><path d="M100.218 13.991a4.3 4.3 0 00-3.051-5.261 5.376 5.376 0 00-6.577 3.816 6.721 6.721 0 004.769 8.219 8.4 8.4 0 0010.273-5.965 10.5 10.5 0 00-7.45-12.842A13.127 13.127 0 0083.31 8.319c-1.07 1.951-1.375 4.2-2.488 6.136a13.13 13.13 0 01-14.746 6.132 10.5 10.5 0 01-7.45-12.843A8.4 8.4 0 0168.9 1.784 6.719 6.719 0 0173.668 10a5.376 5.376 0 01-6.577 3.816 4.3 4.3 0 01-3.051-5.258"></path><path d="M43.448 13.991A4.3 4.3 0 0040.4 8.73a5.374 5.374 0 00-6.576 3.816 6.719 6.719 0 004.769 8.219A8.4 8.4 0 0048.862 14.8a10.5 10.5 0 00-7.45-12.842A13.127 13.127 0 0026.54 8.319c-1.071 1.951-1.376 4.2-2.49 6.136a13.127 13.127 0 01-14.745 6.132 10.5 10.5 0 01-7.45-12.843 8.4 8.4 0 0110.274-5.96A6.717 6.717 0 0116.9 10a5.374 5.374 0 01-6.575 3.816 4.3 4.3 0 01-3.056-5.258"></path></g><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="11.884" cy="7.898" r="1"></circle><circle cx="39.084" cy="14.651" r="1"></circle><circle cx="28.572" cy="20.546" r="1"></circle><circle cx="22.396" cy="2.004" r="1"></circle><circle cx="68.644" cy="7.898" r="1"></circle><circle cx="95.843" cy="14.651" r="1"></circle><circle cx="85.332" cy="20.546" r="1"></circle><circle cx="79.156" cy="2.004" r="1"></circle><circle cx="125.404" cy="7.898" r="1"></circle><circle cx="152.603" cy="14.651" r="1"></circle><circle cx="142.091" cy="20.546" r="1"></circle><circle cx="135.916" cy="2.004" r="1"></circle><circle cx="182.164" cy="7.898" r="1"></circle><circle cx="209.363" cy="14.651" r="1"></circle><circle cx="198.851" cy="20.546" r="1"></circle><circle cx="192.675" cy="2.004" r="1"></circle><circle cx="238.923" cy="7.898" r="1"></circle><circle cx="266.123" cy="14.651" r="1"></circle><circle cx="255.611" cy="20.546" r="1"></circle><circle cx="249.435" cy="2.004" r="1"></circle><circle cx="295.683" cy="7.898" r="1"></circle><circle cx="322.882" cy="14.651" r="1"></circle><circle cx="312.371" cy="20.546" r="1"></circle><circle cx="306.195" cy="2.004" r="1"></circle><circle cx="352.443" cy="7.898" r="1"></circle><circle cx="379.642" cy="14.651" r="1"></circle><circle cx="369.131" cy="20.546" r="1"></circle><circle cx="362.955" cy="2.004" r="1"></circle><circle cx="409.203" cy="7.898" r="1"></circle><circle cx="436.402" cy="14.651" r="1"></circle><circle cx="425.89" cy="20.546" r="1"></circle><circle cx="419.714" cy="2.004" r="1"></circle><circle cx="465.963" cy="7.898" r="1"></circle><circle cx="493.162" cy="14.651" r="1"></circle><circle cx="482.65" cy="20.546" r="1"></circle><circle cx="476.474" cy="2.004" r="1"></circle><circle cx="522.722" cy="7.898" r="1"></circle><circle cx="549.922" cy="14.651" r="1"></circle><circle cx="539.41" cy="20.546" r="1"></circle><circle cx="533.234" cy="2.004" r="1"></circle><circle cx="579.482" cy="7.898" r="1"></circle><circle cx="606.681" cy="14.651" r="1"></circle><circle cx="596.17" cy="20.546" r="1"></circle><circle cx="589.994" cy="2.004" r="1"></circle><circle cx="636.242" cy="7.898" r="1"></circle><circle cx="663.441" cy="14.651" r="1"></circle><circle cx="652.929" cy="20.546" r="1"></circle><circle cx="646.754" cy="2.004" r="1"></circle><circle cx="693.002" cy="7.898" r="1"></circle><circle cx="720.201" cy="14.651" r="1"></circle><circle cx="709.689" cy="20.546" r="1"></circle><circle cx="703.513" cy="2.004" r="1"></circle><circle cx="749.761" cy="7.898" r="1"></circle><circle cx="776.961" cy="14.651" r="1"></circle><circle cx="766.449" cy="20.546" r="1"></circle><circle cx="760.273" cy="2.004" r="1"></circle><circle cx="806.521" cy="7.898" r="1"></circle><circle cx="833.721" cy="14.651" r="1"></circle><path d="M822.209 20.546a1 1 0 111 1 1 1 0 01-1-1z"></path><circle cx="817.033" cy="2.004" r="1"></circle><circle cx="863.281" cy="7.898" r="1"></circle><circle cx="890.481" cy="14.651" r="1"></circle><circle cx="879.969" cy="20.546" r="1"></circle><circle cx="873.793" cy="2.004" r="1"></circle><circle cx="920.041" cy="7.898" r="1"></circle><circle cx="947.24" cy="14.651" r="1"></circle><circle cx="936.729" cy="20.546" r="1"></circle><circle cx="930.553" cy="2.004" r="1"></circle></g></svg>',
			'pattern-8'        => '<svg preserveAspectRatio="none"  class="premium-no-fill" xmlns="http://www.w3.org/2000/svg" width="960" height="22" viewBox="0 0 960 22"><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"><path d="M957.5 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M922.791 1.5a13.322 13.322 0 000 18.842 13.325 13.325 0 00-18.843 0 13.323 13.323 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M935.683 10.921a4.86 4.86 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M888.081 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.324 13.324 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M900.973 10.921a4.86 4.86 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M853.372 1.5a13.322 13.322 0 000 18.842 13.325 13.325 0 00-18.843 0 13.323 13.323 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M866.264 10.921a4.86 4.86 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M818.662 1.5a13.323 13.323 0 000 18.842 13.325 13.325 0 00-18.843 0 13.322 13.322 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M831.554 10.921a4.86 4.86 0 11-4.854-4.857 4.858 4.858 0 014.854 4.857z"></path><path d="M783.952 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M796.844 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path><path d="M749.243 1.5a13.323 13.323 0 000 18.842 13.325 13.325 0 00-18.843 0 13.322 13.322 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M762.135 10.921a4.859 4.859 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M714.533 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M727.425 10.921a4.859 4.859 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M679.824 1.5a13.323 13.323 0 000 18.842 13.325 13.325 0 00-18.843 0 13.322 13.322 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M692.716 10.921a4.86 4.86 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M645.114 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M658.006 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path><path d="M610.405 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.324 13.324 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M623.3 10.921a4.86 4.86 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M575.7 1.5a13.323 13.323 0 000 18.842 13.325 13.325 0 00-18.843 0 13.322 13.322 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M588.587 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path><path d="M540.985 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M553.877 10.921a4.859 4.859 0 11-4.858-4.857 4.857 4.857 0 014.858 4.857z"></path><path d="M506.276 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M519.168 10.921a4.859 4.859 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M471.566 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M484.458 10.921a4.859 4.859 0 11-4.858-4.857 4.857 4.857 0 014.858 4.857z"></path><path d="M436.857 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M449.749 10.921a4.86 4.86 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M402.148 1.5a13.322 13.322 0 000 18.842 13.325 13.325 0 00-18.843 0 13.323 13.323 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M415.04 10.921a4.86 4.86 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M367.438 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.324 13.324 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M380.33 10.921a4.86 4.86 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M332.728 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M345.62 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path><path d="M298.019 1.5a13.322 13.322 0 000 18.842 13.325 13.325 0 00-18.843 0 13.323 13.323 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M310.911 10.921a4.86 4.86 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M263.309 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M276.2 10.921a4.859 4.859 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M228.6 1.5a13.322 13.322 0 000 18.842 13.325 13.325 0 00-18.843 0 13.323 13.323 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M241.492 10.921a4.86 4.86 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M193.89 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M206.782 10.921a4.859 4.859 0 11-4.859-4.857 4.858 4.858 0 014.859 4.857z"></path><path d="M159.181 1.5a13.322 13.322 0 000 18.842 13.325 13.325 0 00-18.843 0 13.323 13.323 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M172.073 10.921a4.86 4.86 0 11-4.86-4.857 4.858 4.858 0 014.86 4.857z"></path><path d="M124.471 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M137.363 10.921a4.859 4.859 0 11-4.863-4.857 4.857 4.857 0 014.863 4.857z"></path><path d="M89.761 1.5a13.324 13.324 0 000 18.842 13.323 13.323 0 00-18.842 0 13.323 13.323 0 000-18.842 13.325 13.325 0 0018.842 0z"></path><path d="M102.653 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path><path d="M55.052 1.5a13.323 13.323 0 000 18.842 13.325 13.325 0 00-18.843 0 13.322 13.322 0 000-18.842 13.326 13.326 0 0018.843 0z"></path><path d="M67.944 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path><path d="M20.342 1.5a13.323 13.323 0 000 18.842 13.323 13.323 0 00-18.842 0A13.323 13.323 0 001.5 1.5a13.325 13.325 0 0018.842 0z"></path><path d="M33.234 10.921a4.859 4.859 0 11-4.859-4.857 4.857 4.857 0 014.859 4.857z"></path></g><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M10.928 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><path d="M45.637 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="28.392" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M80.347 9.922a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="63.101" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M115.056 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="97.81" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M149.765 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="132.52" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M184.475 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="167.229" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M219.184 9.922a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="201.938" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M253.893 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="236.648" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M288.6 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="271.357" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M323.312 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="306.067" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M358.022 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="340.776" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M392.731 9.922a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="375.485" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M427.44 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="410.195" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M462.15 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="444.904" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M496.859 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="479.614" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M531.568 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="514.323" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M566.278 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="549.031" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M600.987 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="583.74" cy="10.925" r="8.499" stroke-dasharray="0 6"></circle><path d="M635.7 9.922a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="618.449" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M670.406 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="653.158" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M705.115 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="687.867" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M739.823 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="722.577" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M774.532 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="757.286" cy="10.925" r="8.499" stroke-dasharray="0 6"></circle><path d="M809.241 9.922a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="791.996" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M843.951 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="826.705" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M878.66 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="861.417" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M913.37 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="896.126" cy="10.925" r="8.5" stroke-dasharray="0 6"></circle><path d="M948.079 9.921a1 1 0 11-1 1 1 1 0 011-1z"></path><circle cx="930.835" cy="10.925" r="8.499" stroke-dasharray="0 6"></circle></g></svg>',
			'pattern-9'        => '<svg preserveAspectRatio="none"  class="premium-no-fill" xmlns="http://www.w3.org/2000/svg" width="960" height="42" viewBox="0 0 960 42"><g><path fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M1.5 28.658c96.2-3.176 192.23-6.925 288.5-3.432 27.655 1 55.292 2.551 82.872 4.811 22.788 1.867 41.616-1.57 59.417-16.555C445.106 2.693 447.273-.929 455.168 3.1c17.883 9.126 32.182 30.812 54.917 22.778 11.376-4.02 21.53-12.136 33.746-13.732 14.045-1.835 27.568 4.446 40.854 7.837 30.077 7.677 63.929 7.312 94.841 8.571 34.5 1.405 69.045 1.277 103.551.285 19.533-.562 39.059-1.409 58.572-2.4 38.575-1.971 77.228-2.9 115.852-3.212"></path></g><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="551.999" cy="28.971" r="1"></circle><path d="M453 38.971a1 1 0 11-1-1 1 1 0 011 1z"></path><path d="M413 7.641a3.5 3.5 0 11-3.5-3.5 3.5 3.5 0 013.5 3.5z"></path></g></svg>',
			'pattern-10'       => '<svg preserveAspectRatio="none" class="premium-no-fill" xmlns="http://www.w3.org/2000/svg" width="960" height="58" viewBox="0 0 960 58"><g ><path fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M1.5 34.254c144.315 6.061 293.324-12.344 437.111 4.493 9.423 1.1 18.887 2.3 28.4 2.062 5.671-.141 11.762-.746 16.9-2.969a18.806 18.806 0 009.635-8.687C499.018 18.6 493.661.3 479.511 1.562c-10.764.96-20.732 17.29-13.056 26.73 7.228 8.886 19.752 3.66 27.2-2.006 3.3-2.511 8.7-8.216 8.615-12.614-.162-7.736-10.982-5.291-15.6-4.37-8.807 1.757-17.934 5.531-22.828 13.459-2.225 3.6-2.439 9.07.365 12.572 6.147 7.667 19.455-1.821 24.145-12.433 2.458-5.564 1.669-12.479-3.274-16.324-4.376-3.4-12.394-3.522-17-.864-5.492 3.168-8.828 9.481-9.091 15.721-.119 2.835.063 7.317 1.43 9.876 6.331 11.837 22.337 11.573 33.873 10.958 28.61-1.521 56.976-6.234 85.66-7.738 29.64-1.553 59.322-2.16 89-2.211q25.186-.044 50.373.434c62.805 1.083 125.3 3.363 188.119 1.437 16.685-.512 33.369-1.088 50.059-1.425"></path></g><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M513.651 4.924l.975 1.976 2.18.317-1.577 1.537.372 2.17-1.95-1.024-1.949 1.024.372-2.17-1.577-1.537 2.18-.317.974-1.976z"></path><path d="M438.727 47.387l.974 1.976 2.18.317-1.577 1.537.372 2.17-1.949-1.024-1.95 1.024.372-2.17-1.577-1.537 2.18-.317.975-1.976z"></path><path d="M520.127 53.387a3.5 3.5 0 11-3.5-3.5 3.5 3.5 0 013.5 3.5z"></path><circle cx="405.905" cy="21.924" r="1"></circle><circle cx="553.095" cy="22.924" r="1"></circle></g></svg>',
			'pattern-11'       => '<svg preserveAspectRatio="none"  class="premium-no-fill" xmlns="http://www.w3.org/2000/svg" width="960" height="60" viewBox="0 0 960 60"><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"><path d="M479.5 1.5C472.881 6.931 469 13.507 469 20.593s3.88 13.657 10.5 19.092c6.622-5.435 10.5-12.008 10.5-19.1S486.121 6.931 479.5 1.5z"></path><path d="M495.54 24.8c-6.568 2.653-11.209 8.714-13.769 16.886 7.52 4.1 15.067 5.238 21.639 2.584s11.211-8.713 13.767-16.885c-7.516-4.105-15.067-5.24-21.637-2.585z"></path><path d="M463.46 24.8c-6.57-2.654-14.121-1.519-21.637 2.585 2.556 8.172 7.2 14.232 13.767 16.885s14.119 1.519 21.639-2.584c-2.56-8.173-7.201-14.234-13.769-16.886z"></path><path d="M479.5 39.685c9.6 9.487 25.238 12.963 38.721 10.635 7.344-1.266 15.086-3.892 21.285-8.106 6.074-4.131 11.029-11.223 5.709-18.543a9.12 9.12 0 00-12.74-2.018 7.3 7.3 0 00-1.613 10.194 5.839 5.839 0 008.153 1.289 4.671 4.671 0 001.033-6.524"></path><path d="M534.852 44.936a116.272 116.272 0 0128.391-9.913c9.6-2 30.911-7.989 32.581 7.367a9.121 9.121 0 01-8.081 10.054 7.3 7.3 0 01-8.043-6.466 5.836 5.836 0 015.173-6.432 4.668 4.668 0 015.147 4.137"></path><path d="M957.5 22.075c-46.765 0-89.963 18.293-136.324 20.127-54.814 2.17-105.267-22.246-159.842-22.553-22.915-.128-46.078 1.583-68.115 7.455"></path><path d="M479.5 39.685c-9.6 9.487-25.238 12.963-38.721 10.635-7.344-1.266-15.086-3.892-21.285-8.106-6.074-4.131-11.029-11.223-5.709-18.543a9.12 9.12 0 0112.74-2.018 7.3 7.3 0 011.613 10.194 5.839 5.839 0 01-8.153 1.289 4.671 4.671 0 01-1.033-6.524"></path><path d="M424.148 44.936a116.272 116.272 0 00-28.391-9.913c-9.6-2-30.911-7.989-32.581 7.367a9.121 9.121 0 008.081 10.054 7.3 7.3 0 008.043-6.466 5.836 5.836 0 00-5.173-6.432 4.668 4.668 0 00-5.147 4.137"></path><path d="M1.5 22.075c46.765 0 89.963 18.293 136.324 20.127 54.814 2.17 105.267-22.246 159.842-22.553 22.915-.128 46.078 1.583 68.115 7.455"></path></g><g fill="none" stroke="#6e7881" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="0 6" d="M137.511 35.123c54.815 2.169 105.268-22.247 159.843-22.554a313.238 313.238 0 0139.858 1.885 260.953 260.953 0 0147.27 9.942c7.891 2.5 15.641 5.422 23.268 8.638"></path><path stroke-dasharray="0 6" d="M821.489 35.124c-54.815 2.169-105.268-22.247-159.843-22.554a313.421 313.421 0 00-39.858 1.884 261.04 261.04 0 00-47.27 9.943c-7.891 2.5-15.641 5.422-23.268 8.637"></path><path d="M476.5 48.684l3-3 3 3-3 3z"></path><path stroke-dasharray="0 6" d="M957.5 29.075c-46.765 0-89.963 18.293-136.324 20.127-54.814 2.17-105.267-22.246-159.842-22.553"></path><path stroke-dasharray="0 6" d="M1.5 29.075c46.765 0 89.963 18.293 136.324 20.127 54.814 2.17 105.267-22.246 159.842-22.553"></path><path d="M606.839 37.245a3.5 3.5 0 103.5-3.5 3.5 3.5 0 00-3.5 3.5z"></path><path d="M562 48.245a3.5 3.5 0 103.5-3.5 3.5 3.5 0 00-3.5 3.5z"></path><circle cx="490.474" cy="57.038" r="1"></circle><path stroke-dasharray="0 6" d="M490.809 38.034l17.33-7"></path><path stroke-dasharray="0 6" d="M479.499 30.093l.003-19"></path><path stroke-dasharray="0 6" d="M468.191 38.034l-17.33-7"></path><path d="M352.161 37.245a3.5 3.5 0 11-3.5-3.5 3.5 3.5 0 013.5 3.5z"></path><path d="M397 48.245a3.5 3.5 0 11-3.5-3.5 3.5 3.5 0 013.5 3.5z"></path><circle cx="468.526" cy="57.038" r="1"></circle></g></svg>',

			'div-bg-curly'     => array( "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' fill='none' stroke='black' stroke-linecap='square' stroke-miterlimit='10'", "viewBox='0 0 24 24'%3E%3Cpath d='M0,21c3.3,0,8.3-0.9,15.7-7.1c6.6-5.4,4.4-9.3,2.4-10.3c-3.4-1.8-7.7,1.3-7.3,8.8C11.2,20,17.1,21,24,21'/%3E%3C/svg%3E" ),
			'div-bg-curved'    => array( "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' fill='none' stroke='black' stroke-linecap='square' stroke-miterlimit='10'", "viewBox='0 0 24 24'%3E%3Cpath d='M0,6c6,0,6,13,12,13S18,6,24,6'/%3E%3C/svg%3E" ),
			'div-bg-slashed'   => array( "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' fill='none' stroke='black' stroke-linecap='square' stroke-miterlimit='10'", "viewBox='0 0 20 16'%3E%3Cg transform='translate(-12.000000, 0)'%3E%3Cpath d='M28,0L10,18'/%3E%3Cpath d='M18,0L0,18'/%3E%3Cpath d='M48,0L30,18'/%3E%3Cpath d='M38,0L20,18'/%3E%3C/g%3E%3C/svg%3E" ),
			'div-bg-wavy'      => array( "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' fill='none' stroke='black' stroke-linecap='square' stroke-miterlimit='10'", "viewBox='0 0 24 24'%3E%3Cpath d='M0,6c6,0,0.9,11.1,6.9,11.1S18,6,24,6'/%3E%3C/svg%3E" ),
			'div-bg-zigzag'    => array( "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' fill='none' stroke='black' stroke-linecap='square' stroke-miterlimit='10'", "viewBox='0 0 24 24'%3E%3Cpolyline points='0,18 12,6 24,18 '/%3E%3C/svg%3E" ),

			'div-bg-diamond'   => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' viewBox='0 0 24 24' fill='black' stroke='none'%3E%3Cpath d='M12.7,2.3c-0.4-0.4-1.1-0.4-1.5,0l-8,9.1c-0.3,0.4-0.3,0.9,0,1.2l8,9.1c0.4,0.4,1.1,0.4,1.5,0l8-9.1c0.3-0.4,0.3-0.9,0-1.2L12.7,2.3z'/%3E%3C/svg%3E",
			'div-bg-para'      => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' viewBox='0 0 24 24' fill='black' stroke='none'%3E%3Cpolygon points='9.4,2 24,2 14.6,21.6 0,21.6'/%3E%3C/svg%3E",
			'div-bg-rect'      => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' overflow='visible' height='100%' viewBox='0 0 60 30' fill='black' stroke='none'%3E%3Crect x='15' y='0' width='30' height='30'/%3E%3C/svg%3E",

			'div-bg-r-dots'    => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 126 26' fill='black' stroke='none'%3E%3Cpath d='M3,10.2c2.6,0,2.6,2,2.6,3.2S4.4,16.5,3,16.5s-3-1.4-3-3.2S0.4,10.2,3,10.2z M18.8,10.2c1.7,0,3.2,1.4,3.2,3.2s-1.4,3.2-3.2,3.2c-1.7,0-3.2-1.4-3.2-3.2S17,10.2,18.8,10.2z M34.6,10.2c1.5,0,2.6,1.4,2.6,3.2s-0.5,3.2-1.9,3.2c-1.5,0-3.4-1.4-3.4-3.2S33.1,10.2,34.6,10.2z M50.5,10.2c1.7,0,3.2,1.4,3.2,3.2s-1.4,3.2-3.2,3.2c-1.7,0-3.3-0.9-3.3-2.6S48.7,10.2,50.5,10.2z M66.2,10.2c1.5,0,3.4,1.4,3.4,3.2s-1.9,3.2-3.4,3.2c-1.5,0-2.6-0.4-2.6-2.1S64.8,10.2,66.2,10.2z M82.2,10.2c1.7,0.8,2.6,1.4,2.6,3.2s-0.1,3.2-1.6,3.2c-1.5,0-3.7-1.4-3.7-3.2S80.5,9.4,82.2,10.2zM98.6,10.2c1.5,0,2.6,0.4,2.6,2.1s-1.2,4.2-2.6,4.2c-1.5,0-3.7-0.4-3.7-2.1S97.1,10.2,98.6,10.2z M113.4,10.2c1.2,0,2.2,0.9,2.2,3.2s-0.1,3.2-1.3,3.2s-3.1-1.4-3.1-3.2S112.2,10.2,113.4,10.2z'/%3E%3C/svg%3E",
			'div-bg-r-ftree'   => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 126 26' fill='black' stroke='none'%3E%3Cpath d='M111.9,18.3v3.4H109v-3.4H111.9z M90.8,18.3v3.4H88v-3.4H90.8z M69.8,18.3v3.4h-2.9v-3.4H69.8z M48.8,18.3v3.4h-2.9v-3.4H48.8z M27.7,18.3v3.4h-2.9v-3.4H27.7z M6.7,18.3v3.4H3.8v-3.4H6.7z M46.4,4l4.3,4.8l-1.8,0l3.5,4.4l-2.2-0.1l3,3.3l-11,0.4l3.6-3.8l-2.9-0.1l3.1-4.2l-1.9,0L46.4,4z M111.4,4l2.4,4.8l-1.8,0l3.5,4.4l-2.5-0.1l3.3,3.3h-11l3.1-3.4l-2.5-0.1l3.1-4.2l-1.9,0L111.4,4z M89.9,4l2.9,4.8l-1.9,0l3.2,4.2l-2.5,0l3.5,3.5l-11-0.4l3-3.1l-2.4,0L88,8.8l-1.9,0L89.9,4z M68.6,4l3,4.4l-1.9,0.1l3.4,4.1l-2.7,0.1l3.8,3.7H63.8l2.9-3.6l-2.9,0.1L67,8.7l-2,0.1L68.6,4z M26.5,4l3,4.4l-1.9,0.1l3.7,4.7l-2.5-0.1l3.3,3.3H21l3.1-3.4l-2.5-0.1l3.2-4.3l-2,0.1L26.5,4z M4.9,4l3.7,4.8l-1.5,0l3.1,4.2L7.6,13l3.4,3.4H0l3-3.3l-2.3,0.1l3.5-4.4l-2.3,0L4.9,4z'/%3E%3C/svg%3E",
			'div-bg-r-hround'  => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 120 26' fill='black' stroke='none'%3E%3Cpath d='M11.9,15.9L11.9,15.9L0,16c-0.2-3.7,1.5-5.7,4.9-6C10,9.6,12.4,14.2,11.9,15.9zM26.9,15.9L26.9,15.9L15,16c0.5-3.7,2.5-5.7,5.9-6C26,9.6,27.4,14.2,26.9,15.9z M37.1,10c3.4,0.3,5.1,2.3,4.9,6H30.1C29.5,14.4,31.9,9.6,37.1,10z M57,15.9L57,15.9L45,16c0-3.4,1.6-5.4,4.9-5.9C54.8,9.3,57.4,14.2,57,15.9z M71.9,15.9L71.9,15.9L60,16c-0.2-3.7,1.5-5.7,4.9-6C70,9.6,72.4,14.2,71.9,15.9z M82.2,10c3.4,0.3,5,2.3,4.8,6H75.3C74,13,77.1,9.6,82.2,10zM101.9,15.9L101.9,15.9L90,16c-0.2-3.7,1.5-5.7,4.9-6C100,9.6,102.4,14.2,101.9,15.9z M112.1,10.1c2.7,0.5,4.3,2.5,4.9,5.9h-11.9l0,0C104.5,14.4,108,9.3,112.1,10.1z'/%3E%3C/svg%3E",
			'div-bg-r-leaves'  => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 117 26' fill='black' stroke='none'%3E%3Cpath d='M3,1.5C5,4.9,6,8.8,6,13s-1.7,8.1-5,11.5C0.3,21.1,0,17.2,0,13S1,4.9,3,1.5z M16,1.5c2,3.4,3,7.3,3,11.5s-1,8.1-3,11.5c-2-4.1-3-8.3-3-12.5S14,4.3,16,1.5z M29,1.5c2,4.8,3,9.3,3,13.5s-1,7.4-3,9.5c-2-3.4-3-7.3-3-11.5S27,4.9,29,1.5z M41.1,1.5C43.7,4.9,45,8.8,45,13s-1,8.1-3,11.5c-2-3.4-3-7.3-3-11.5S39.7,4.9,41.1,1.5zM55,1.5c2,2.8,3,6.3,3,10.5s-1.3,8.4-4,12.5c-1.3-3.4-2-7.3-2-11.5S53,4.9,55,1.5z M68,1.5c2,3.4,3,7.3,3,11.5s-0.7,8.1-2,11.5c-2.7-4.8-4-9.3-4-13.5S66,3.6,68,1.5z M82,1.5c1.3,4.8,2,9.3,2,13.5s-1,7.4-3,9.5c-2-3.4-3-7.3-3-11.5S79.3,4.9,82,1.5z M94,1.5c2,3.4,3,7.3,3,11.5s-1.3,8.1-4,11.5c-1.3-1.4-2-4.3-2-8.5S92,6.9,94,1.5z M107,1.5c2,2.1,3,5.3,3,9.5s-0.7,8.7-2,13.5c-2.7-3.4-4-7.3-4-11.5S105,4.9,107,1.5z'/%3E%3C/svg%3E",
			'div-bg-r-strips'  => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 120 26' fill='black' stroke='none'%3E%3Cpath d='M54,1.6V26h-9V2.5L54,1.6z M69,1.6v23.3L60,26V1.6H69z M24,1.6v23.5l-9-0.6V1.6H24z M30,0l9,0.7v24.5h-9V0z M9,2.5v22H0V3.7L9,2.5z M75,1.6l9,0.9v22h-9V1.6z M99,2.7v21.7h-9V3.8L99,2.7z M114,3.8v20.7l-9-0.5V3.8L114,3.8z'/%3E%3C/svg%3E",
			'div-bg-r-square'  => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 126 26' fill='black' stroke='none'%3E%3Cpath d='M46.8,7.8v11.5L36,18.6V7.8H46.8z M82.4,7.8L84,18.6l-12,0.7L70.4,7.8H82.4z M0,7.8l12,0.9v9.9H1.3L0,7.8z M30,7.8v10.8H19L18,7.8H30z M63.7,7.8L66,18.6H54V9.5L63.7,7.8z M89.8,7L102,7.8v10.8H91.2L89.8,7zM108,7.8l12,0.9v8.9l-12,1V7.8z'/%3E%3C/svg%3E",
			'div-bg-r-tree'    => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 123 26' fill='black' stroke='none'%3E%3Cpath d='M6.4,2l4.2,5.7H7.7v2.7l3.8,5.2l-3.8,0v7.8H4.8v-7.8H0l4.8-5.2V7.7H1.1L6.4,2z M25.6,2L31,7.7h-3.7v2.7l4.8,5.2h-4.8v7.8h-2.8v-7.8l-3.8,0l3.8-5.2V7.7h-2.9L25.6,2z M47.5,2l4.2,5.7h-3.3v2.7l3.8,5.2l-3.8,0l0.4,7.8h-2.8v-7.8H41l4.8-5.2V7.7h-3.7L47.5,2z M66.2,2l5.4,5.7h-3.7v2.7l4.8,5.2h-4.8v7.8H65v-7.8l-3.8,0l3.8-5.2V7.7h-2.9L66.2,2zM87.4,2l4.8,5.7h-2.9v3.1l3.8,4.8l-3.8,0v7.8h-2.8v-7.8h-4.8l4.8-4.8V7.7h-3.7L87.4,2z M107.3,2l5.4,5.7h-3.7v2.7l4.8,5.2h-4.8v7.8H106v-7.8l-3.8,0l3.8-5.2V7.7h-2.9L107.3,2z'/%3E%3C/svg%3E",
			'div-bg-r-tribal'  => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 121 26' fill='black' stroke='none'%3E%3Cpath d='M29.6,10.3l2.1,2.2l-3.6,3.3h7v2.9h-7l3.6,3.5l-2.1,1.7l-5.2-5.2h-5.8v-2.9h5.8L29.6,10.3z M70.9,9.6l2.1,1.7l-3.6,3.5h7v2.9h-7l3.6,3.3l-2.1,2.2l-5.2-5.5h-5.8v-2.9h5.8L70.9,9.6z M111.5,9.6l2.1,1.7l-3.6,3.5h7v2.9h-7l3.6,3.3l-2.1,2.2l-5.2-5.5h-5.8v-2.9h5.8L111.5,9.6z M50.2,2.7l2.1,1.7l-3.6,3.5h7v2.9h-7l3.6,3.3l-2.1,2.2L45,10.7h-5.8V7.9H45L50.2,2.7z M11,2l2.1,1.7L9.6,7.2h7V10h-7l3.6,3.3L11,15.5L5.8,10H0V7.2h5.8L11,2z M91.5,2l2.1,2.2l-3.6,3.3h7v2.9h-7l3.6,3.5l-2.1,1.7l-5.2-5.2h-5.8V7.5h5.8L91.5,2z'/%3E%3C/svg%3E",
			'div-bg-r-x'       => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 126 26' fill='black' stroke='none'%3E%3Cpath d='M10.7,6l2.5,2.6l-4,4.3l4,5.4l-2.5,1.9l-4.5-5.2l-3.9,4.2L0.7,17L4,13.1L0,8.6l2.3-1.3l3.9,3.9L10.7,6z M23.9,6.6l4.2,4.5L32,7.2l2.3,1.3l-4,4.5l3.2,3.9L32,19.1l-3.9-3.3l-4.5,4.3l-2.5-1.9l4.4-5.1l-4.2-3.9L23.9,6.6zM73.5,6L76,8.6l-4,4.3l4,5.4l-2.5,1.9l-4.5-5.2l-3.9,4.2L63.5,17l4.1-4.7L63.5,8l2.3-1.3l4.1,3.6L73.5,6z M94,6l2.5,2.6l-4,4.3l4,5.4L94,20.1l-3.9-5l-3.9,4.2L84,17l3.2-3.9L84,8.6l2.3-1.3l3.2,3.9L94,6z M106.9,6l4.5,5.1l3.9-3.9l2.3,1.3l-4,4.5l3.2,3.9l-1.6,2.1l-3.9-4.2l-4.5,5.2l-2.5-1.9l4-5.4l-4-4.3L106.9,6z M53.1,6l2.5,2.6l-4,4.3l4,4.6l-2.5,1.9l-4.5-4.5l-3.5,4.5L43.1,17l3.2-3.9l-4-4.5l2.3-1.3l3.9,3.9L53.1,6z'/%3E%3C/svg%3E",
			'div-bg-r-tzigzag' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='xMidYMid meet' overflow='visible' height='100%' viewBox='0 0 120 26' fill='black' stroke='none'%3E%3Cpolygon points='0,14.4 0,21 11.5,12.4 21.3,20 30.4,11.1 40.3,20 51,12.4 60.6,20 69.6,11.1 79.3,20 90.1,12.4 99.6,20 109.7,11.1 120,21 120,14.4 109.7,5 99.6,13 90.1,5 79.3,14.5 71,5.7 60.6,12.4 51,5 40.3,14.5 31.1,5 21.3,13 11.5,5 	'/%3E%3C/svg%3E",
		);
	}

	/**
	 * Get Seprator Svg.
	 * Rebuild the separator svg mask.
	 *
	 * @access private
	 * @since 2.9.8
	 *
	 * @param array  $separator svg parts.
	 * @param string $stroke stroke width.
	 */
	private function get_sep_svg( $separator, $stroke ) {

		$stroke_width = empty( $stroke['size'] ) ? 1 : $stroke['size'];

		return $separator[0] . " stroke-width='" . $stroke_width . "' " . $separator[1];
	}
}
