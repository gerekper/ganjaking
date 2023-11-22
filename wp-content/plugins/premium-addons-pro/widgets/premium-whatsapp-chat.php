<?php
/**
 * Class: Premium_Whatsapp_Chat
 * Name: Whatsapp Chat
 * Slug: premium-whatsapp-chat
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Whatsapp_Chat
 */
class Premium_Whatsapp_Chat extends Widget_Base {

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

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-whatsapp-chat' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-whatsapp-chat';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'WhatsApp Chat', 'premium-addons-pro' );
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
		return 'pa-pro-whatsapp';
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
		return array( 'pa', 'premium', 'message', 'client', 'send', 'customer' );
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
			'premium-addons',
			'premium-pro',
			'tooltipster',
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
				'tooltipster-bundle',
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
	 * Register Whatsapp Chat controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'chat',
			array(
				'label' => __( 'Chat', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'chat_type',
			array(
				'label'       => __( 'Chat', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'private' => __( 'Private', 'premium-addons-pro' ),
					'group'   => __( 'Group', 'premium-addons-pro' ),
				),
				'default'     => 'private',
				'label_block' => true,
			)
		);

		$this->add_control(
			'number',
			array(
				'label'       => __( 'Phone Number', 'premium-addons-pro' ),
				'description' => 'Example: +1123456789',
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'chat_type' => 'private',
				),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'group_id',
			array(
				'label'       => __( 'Group ID', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'click <a href="https://www.youtube.com/watch?time_continue=13&v=Vx53spbt_qk" target="_blank"> here</a> to know how to get the group id',
				'dynamic'     => array( 'active' => true ),
				'default'     => '9EHLsEsOeJk6AVtE8AvXiA',
				'condition'   => array(
					'chat_type' => 'group',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'settings',
			array(
				'label' => __( 'Button', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'button_float',
			array(
				'label' => __( 'Float', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_responsive_control(
			'horizontal_position',
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
					'button_float' => 'yes',
					'position'     => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_position_left',
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
					'button_float' => 'yes',
					'position'     => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'vertical_position',
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
					'button_float' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Contact us', 'premium-addons-pro' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'icon_switcher',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable button icon', 'premium-addons-pro' ),
				'default'     => 'yes',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon'      => __( 'Icon', 'premium-addons-pro' ),
					'image'     => __( 'Image', 'premium-addons-pro' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'       => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'default'   => 'icon',
				'condition' => array(
					'icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_selection_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_selection',
				'default'          => array(
					'value'   => 'fab fa-whatsapp',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array(
					'icon_switcher' => 'yes',
					'icon_type'     => 'icon',
				),
			)
		);

		$this->add_control(
			'image_upload',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'icon_switcher' => 'yes',
					'icon_type'     => 'image',
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
					'icon_switcher' => 'yes',
					'icon_type'     => 'svg',
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
					'icon_switcher' => 'yes',
					'icon_type'     => 'animation',
				),
			)
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'icon_switcher'                    => 'yes',
					'icon_type'                        => array( 'icon', 'svg' ),
					'icon_selection_updated[library]!' => 'svg',
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'icon_switcher',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'icon_type',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'icon_type',
											'value' => 'icon',
										),
										array(
											'name'  => 'icon_type',
											'value' => 'svg',
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
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-whatsapp-icon-wrap svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
						'draw_svg'      => 'yes',
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
					'condition'   => array(
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
						'draw_svg'      => 'yes',
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {
			Helper_Functions::get_draw_svg_notice(
				$this,
				'whatsapp',
				array(
					'icon_switcher'                    => 'yes',
					'icon_type'                        => array( 'icon', 'svg' ),
					'icon_selection_updated[library]!' => 'svg',
				)
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
						'icon_switcher'   => 'yes',
						'icon_type'       => array( 'icon', 'svg' ),
						'draw_svg'        => 'yes',
						'lottie_reverse!' => 'true',
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
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'icon_switcher'  => 'yes',
						'icon_type'      => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),

				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
						'draw_svg'      => 'yes',
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
						'draw_svg'      => 'yes',
						'lottie_loop'   => 'true',
					),
				)
			);
		}

		$this->add_control(
			'icon_position',
			array(
				'label'        => __( 'Icon Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'before' => __( 'Before', 'premium-addons-pro' ),
					'after'  => __( 'After', 'premium-addons-pro' ),
				),
				'default'      => 'after',
				'label_block'  => true,
				'prefix_class' => 'premium-whatsapp-icon-',
				'condition'    => array(
					'icon_switcher' => 'yes',
					'button_text!'  => '',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
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
					'{{WRAPPER}} .premium-whatsapp-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-whatsapp-image, {{WRAPPER}} .premium-whatsapp-link svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
				'condition'  => array(
					'icon_switcher'           => 'yes',
					'icon_selection_updated!' => '',
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
				'condition'  => array(
					'icon_switcher' => 'yes',
					'icon_type'     => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link svg' => 'width: {{SIZE}}{{UNIT}};',
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
				'condition'  => array(
					'icon_switcher' => 'yes',
					'icon_type'     => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$icon_spacing_after = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-whatsapp-icon-after .premium-whatsapp-icon-wrap' => 'margin-' . $icon_spacing_after . ': {{SIZE}}px',
					'{{WRAPPER}}.premium-whatsapp-icon-before .premium-whatsapp-icon-wrap' => 'margin-' . $icon_spacing . ': {{SIZE}}px',
				),
				'separator' => 'after',
				'condition' => array(
					'icon_switcher' => 'yes',
					'button_text!'  => '',
				),
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'       => __( 'Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'lg',
				'options'     => array(
					'sm'    => __( 'Small', 'premium-addons-pro' ),
					'md'    => __( 'Medium', 'premium-addons-pro' ),
					'lg'    => __( 'Large', 'premium-addons-pro' ),
					'block' => __( 'Block', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_responsive_control(
			'button_alignment',
			array(
				'label'     => __( 'Button Alignment', 'premium-addons-pro' ),
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
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link-wrap' => 'text-align: {{VALUE}}',
				),
				'condition' => array(
					'button_float!' => 'yes',
					'button_size!'  => 'block',
				),
			)
		);

		$this->add_responsive_control(
			'text_alignment',
			array(
				'label'     => __( 'Text Alignment', 'premium-addons-pro' ),
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
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link-wrap .premium-whatsapp-link' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'button_float!' => 'yes',
					'icon_position' => 'row',
					'button_size'   => 'block',
				),
			)
		);

		$this->add_responsive_control(
			'text_alignment_after',
			array(
				'label'     => __( 'Text Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right'  => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'left'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link-wrap .premium-whatsapp-link' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'button_float!' => 'yes',
					'icon_position' => 'row-reverse',
					'button_size'   => 'block',
				),
			)
		);

		$this->add_control(
			'position',
			array(
				'label'       => __( 'Button Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'right' => __( 'Right', 'premium-addons-pro' ),
					'left'  => __( 'Left', 'premium-addons-pro' ),
				),
				'toggle'      => false,
				'default'     => 'right',
				'label_block' => true,
				'condition'   => array(
					'button_float' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->add_control(
			'link_new_tab',
			array(
				'label'   => __( 'Open Link in New Tab', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			'hide_tabs',
			array(
				'label'       => __( 'Hide on Tabs', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This will hide the chat button on tablets', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'hide_mobiles',
			array(
				'label'       => __( 'Hide on Mobiles', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This will hide the chat button on mobile phones', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tooltips',
			array(
				'label'       => __( 'Tooltips', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This will show a tooltip next to the button when hovered', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tooltips_msg',
			array(
				'label'     => __( 'Tooltip Message', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( 'Message us', 'premium-addons-pro' ),
				'condition' => array(
					'tooltips' => 'yes',
				),
			)
		);

		$this->add_control(
			'tooltips_anim',
			array(
				'label'       => __( 'Animation', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'fade'  => __( 'Fade', 'premium-addons-pro' ),
					'grow'  => __( 'Grow', 'premium-addons-pro' ),
					'swing' => __( 'Swing', 'premium-addons-pro' ),
					'slide' => __( 'Slide', 'premium-addons-pro' ),
					'fall'  => __( 'Fall', 'premium-addons-pro' ),
				),
				'condition'   => array(
					'tooltips' => 'yes',
				),
				'default'     => 'fade',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_button_style_section',
			array(
				'label' => __( 'Button', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'separator' => 'after',
				'condition' => array(
					'icon_switcher' => 'yes',
					'icon_type'     => array( 'icon', 'svg' ),
					'draw_svg'      => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'condition' => array(
					'button_text!' => '',
				),
				'selector'  => '{{WRAPPER}} .premium-whatsapp-link .premium-whatsapp-text',
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'text_color_normal',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link .premium-whatsapp-text'   => 'color: {{VALUE}};',
				),
				'condition' => array(
					'button_text!' => '',
				),
			)
		);

		$this->add_control(
			'button_icon_color_normal',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-whatsapp-icon-wrap svg, {{WRAPPER}} .premium-whatsapp-icon-wrap svg *' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'icon_switcher'           => 'yes',
					'icon_type'               => array( 'icon', 'svg' ),
					'icon_selection_updated!' => '',
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
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-whatsapp-icon-wrap svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'button_background_normal',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border_normal',
				'selector' => '{{WRAPPER}} .premium-whatsapp-link',
			)
		);

		$this->add_control(
			'button_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'button_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'button_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'button_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-pro' ),
				'name'      => 'button_icon_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-whatsapp-icon',
				'condition' => array(
					'icon_switcher'           => 'yes',
					'icon_type'               => 'icon',
					'icon_selection_updated!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Text Shadow', 'premium-addons-pro' ),
				'name'      => 'button_text_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-whatsapp-link .premium-whatsapp-text',
				'condition' => array(
					'button_text!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Button Shadow', 'premium-addons-pro' ),
				'name'     => 'button_box_shadow_normal',
				'selector' => '{{WRAPPER}} .premium-whatsapp-link',
			)
		);

		$this->add_responsive_control(
			'button_margin_normal',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding_normal',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tooltips_background',
			array(
				'label'     => __( 'Tooltips Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.tooltipster-sidetip div.tooltipster-box-{{ID}} .tooltipster-content'  => 'background-color:{{VALUE}};',
				),
				'condition' => array(
					'tooltips' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_color_hover',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover .premium-whatsapp-text'   => 'color: {{VALUE}};',
				),
				'condition' => array(
					'button_text!' => '',
				),
			)
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover .premium-whatsapp-icon'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-whatsapp-link:hover svg, {{WRAPPER}} .premium-whatsapp-link:hover svg *' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'icon_switcher'           => 'yes',
					'icon_type'               => array( 'icon', 'svg' ),
					'icon_selection_updated!' => '',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color_hover',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'icon_switcher' => 'yes',
						'icon_type'     => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-whatsapp-link:hover svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'button_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border_hover',
				'selector' => '{{WRAPPER}} .premium-whatsapp-link:hover',
			)
		);

		$this->add_control(
			'button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'button_hover_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'button_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'button_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-pro' ),
				'name'      => 'button_icon_shadow_hover',
				'selector'  => '{{WRAPPER}} .premium-whatsapp-link:hover i',
				'condition' => array(
					'icon_switcher'           => 'yes',
					'icon_type'               => 'icon',
					'icon_selection_updated!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Text Shadow', 'premium-addons-pro' ),
				'name'      => 'button_text_shadow_hover',
				'selector'  => '{{WRAPPER}} .premium-button:hover .premium-whatsapp-text',
				'condition' => array(
					'button_text!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Button Shadow', 'premium-addons-pro' ),
				'name'     => 'button_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-whatsapp-link:hover',
			)
		);

		$this->add_responsive_control(
			'button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-whatsapp-link:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render Whatsapp Chat widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$pa_whats_chat_settings = array(
			'tooltips'   => $settings['tooltips'],
			'anim'       => $settings['tooltips_anim'],
			'hideMobile' => 'yes' === $settings['hide_mobiles'] ? true : false,
			'hideTab'    => 'yes' === $settings['hide_tabs'] ? true : false,
			'id'         => $this->get_id(),
		);

		$target = 'yes' === $settings['link_new_tab'] ? '_blank' : '_self';

		$id = ( 'private' === $settings['chat_type'] ) ? $settings['number'] : $settings['group_id'];

		$is_mobile = wp_is_mobile();

		if ( ( 'private' === $settings['chat_type'] && ! $is_mobile ) || 'group' === $settings['chat_type'] ) {

			$browser = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'Firefox';

			$is_firefox = ( false !== strpos( $browser, 'Firefox' ) ) ? 'web' : 'chat';

			$prefix = ( 'private' === $settings['chat_type'] ) ? 'web' : $is_firefox;

			$suffix = ( 'private' === $settings['chat_type'] ) ? 'send?phone=' : '';

			$href = sprintf( 'https://%s.whatsapp.com/%s%s', $prefix, $suffix, $id );

		} else {

			$id = str_replace( '+', '', $id );

			$href = sprintf( 'https://wa.me/%s', $id );

		}

		$pos = 'yes' === $settings['button_float'] ? 'premium-button-float' : '';

		$button_size = 'premium-btn-' . $settings['button_size'];

		$this->add_render_attribute(
			'whatsapp',
			array(
				'class'         => 'premium-whatsapp-container',
				'data-settings' => wp_json_encode( $pa_whats_chat_settings ),
			)
		);

		$this->add_render_attribute(
			'button_link',
			array(
				'class'                => array(
					'premium-whatsapp-link',
					$button_size,
					$pos,
					$settings['position'],
					'elementor-animation-' . $settings['button_hover_animation'],
				),
				'data-tooltip-content' => '#tooltip_content',
				'href'                 => esc_url( $href ),
				'target'               => $target,
			)
		);

		if ( 'yes' === $settings['icon_switcher'] ) {

			$icon_type = $settings['icon_type'];

			if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

				if ( 'icon' === $icon_type ) {

					if ( ! empty( $settings['icon_selection'] ) ) {
						$this->add_render_attribute(
							'icon',
							array(
								'class'       => array(
									'premium-whatsapp-icon',
									$settings['icon_selection'],
								),
								'aria-hidden' => 'true',
							)
						);

					}

					$migrated = isset( $settings['__fa4_migrated']['icon_selection_updated'] );
					$is_new   = empty( $settings['icon_selection'] ) && Icons_Manager::is_migration_allowed();

				}

				if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $icon_type ) || 'svg' === $icon_type ) {
					$this->add_render_attribute( 'icon', 'class', 'premium-whatsapp-icon' );
				}

				if ( 'yes' === $settings['draw_svg'] ) {

					$this->add_render_attribute(
						'container',
						'class',
						array(
							'elementor-invisible',
							'premium-drawer-hover',
						)
					);

					if ( 'icon' === $icon_type ) {

						$this->add_render_attribute( 'icon', 'class', $settings['icon_selection_updated']['value'] );

					}

					$this->add_render_attribute(
						'icon',
						array(
							'class'            => 'premium-svg-drawer',
							'data-svg-reverse' => $settings['lottie_reverse'],
							'data-svg-loop'    => $settings['lottie_loop'],
							'data-svg-sync'    => $settings['svg_sync'],
							'data-svg-hover'   => $settings['svg_hover'],
							'data-svg-fill'    => $settings['svg_color'],
							'data-svg-frames'  => $settings['frames'],
							'data-svg-yoyo'    => $settings['svg_yoyo'],
							'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
						)
					);

				} else {
					$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );
				}
			} elseif ( 'image' === $icon_type ) {
				$src = $settings['image_upload']['url'];

				$alt = Control_Media::get_image_alt( $settings['image_upload'] );

				$this->add_render_attribute(
					'image',
					array(
						'class' => 'premium-whatsapp-image',
						'src'   => $src,
						'alt'   => $alt,
					)
				);
			} else {
				$this->add_render_attribute(
					'lottie',
					array(
						'class'               => array(
							'premium-whatsapp-lottie',
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

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'whatsapp' ) ); ?>>
		<div class="premium-whatsapp-link-wrap">
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button_link' ) ); ?>>
				<?php if ( ! empty( $settings['button_text'] ) ) : ?>
					<span class="premium-whatsapp-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['icon_switcher'] ) : ?>
					<span class="premium-whatsapp-icon-wrap">
						<?php
						if ( 'icon' === $icon_type ) :
							if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
								Icons_Manager::render_icon(
									$settings['icon_selection_updated'],
									array(
										'class'       => array( 'premium-whatsapp-icon', 'premium-svg-nodraw' ),
										'aria-hidden' => 'true',
									)
								);
							else :
								?>
								<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
							<?php endif; ?>
						<?php elseif ( 'svg' === $icon_type ) : ?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
								<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
							</div>
						<?php elseif ( 'image' === $icon_type ) : ?>
							<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'image' ) ); ?>>
						<?php else : ?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie' ) ); ?>></div>
						<?php endif; ?>
					</span>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['tooltips'] ) : ?>
					<div id="tooltip_content">
						<span><?php echo esc_html( $settings['tooltips_msg'] ); ?></span>
					</div>
				<?php endif; ?>
			</a>

		</div>

	</div>

		<?php
	}
}
