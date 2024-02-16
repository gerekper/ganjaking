<?php
/**
 * Class: Premium_Notbar
 * Name: Alert Box
 * Slug: premium-notbar
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Responsive\Responsive;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Notbar
 */
class Premium_Notbar extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 2.8.4
	 * @access public
	 */
	public function check_icon_draw() {

		$is_enabled = Admin_Helper::check_svg_draw( 'premium-notbar' );
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
		return 'premium-notbar';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Alert Box', 'premium-addons-pro' );
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
		return 'pa-pro-notification-bar';
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
		return array( 'pa', 'premium', 'notification', 'bar', 'popup', 'modal', 'event' );
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
	 * Register Alert Box controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_notbar_general_section',
			array(
				'label' => __( 'General Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_type',
			array(
				'label'       => __( 'Alert Box Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'notification' => __( 'Notification Bar', 'premium-addons-pro' ),
					'alert'        => __( 'Alert Message', 'premium-addons-pro' ),
				),
				'default'     => 'notification',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_alert_type',
			array(
				'label'        => __( 'Message Type', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'info'    => __( 'Info', 'premium-addons-pro' ),
					'success' => __( 'Success', 'premium-addons-pro' ),
					'warning' => __( 'Warning', 'premium-addons-pro' ),
					'error'   => __( 'Error', 'premium-addons-pro' ),
				),
				'default'      => 'info',
				'prefix_class' => 'premium-alert-',
				'label_block'  => true,
				'condition'    => array(
					'premium_notbar_type' => 'alert',
				),
			)
		);

		$this->add_control(
			'alert_skin',
			array(
				'label'        => __( 'Skin', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'skin1' => __( 'Skin 1', 'premium-addons-pro' ),
					'skin2' => __( 'Skin 2', 'premium-addons-pro' ),
					'skin3' => __( 'Skin 3', 'premium-addons-pro' ),
				),
				'default'      => 'skin1',
				'prefix_class' => 'premium-alert-',
				'label_block'  => true,
				'condition'    => array(
					'premium_notbar_type' => 'alert',
				),
			)
		);

		$this->add_control(
			'custom_position',
			array(
				'label' => __( 'Custom Position', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_notbar_position',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'float'  => array(
						'title' => __( 'Custom', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'float',
				'toggle'    => false,
				'condition' => array(
					'custom_position' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_float_pos',
			array(
				'label'     => __( 'Vertical Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
					'unit' => '%',
				),
				'condition' => array(
					'custom_position'         => 'yes',
					'premium_notbar_position' => 'float',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-notbar' => 'top: {{SIZE}}%;',
				),
			)
		);

		$this->add_control(
			'premium_notbar_top_select',
			array(
				'label'       => __( 'Layout', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'fixed'    => __( 'Fixed', 'premium-addons-pro' ),
					'relative' => __( 'Relative', 'premium-addons-pro' ),
				),
				'default'     => 'relative',
				'condition'   => array(
					'custom_position'         => 'yes',
					'premium_notbar_type!'    => 'alert',
					'premium_notbar_position' => 'top',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_width',
			array(
				'label'       => __( 'Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'wide'  => __( 'Full Width', 'premium-addons-pro' ),
					'boxed' => __( 'Boxed', 'premium-addons-pro' ),
				),
				'default'     => 'boxed',
				'label_block' => true,
				'condition'   => array(
					'custom_position'     => 'yes',
					'premium_notbar_type' => 'notification',
				),
			)
		);

		// $this->add_control(
		// 'enable_background_overlay',
		// array(
		// 'label' => __( 'Overlay Background', 'premium-addons-pro' ),
		// 'type'  => Controls_Manager::SWITCHER,
		// )
		// );

		// $this->add_control(
		// 'background_overlay_notice',
		// array(
		// 'raw'             => __( 'Please note that Overlay Background works only on the frontend', 'premium-addons-pro' ),
		// 'type'            => Controls_Manager::RAW_HTML,
		// 'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		// 'condition'       => array(
		// 'enable_background_overlay' => 'yes',
		// ),
		// )
		// );

		$this->add_control(
			'entrance_animation',
			array(
				'label'              => __( 'Entrance Animation', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::ANIMATION,
				'default'            => 'fadeInUp',
				'separator'          => 'before',
				'label_block'        => true,
				'frontend_available' => true,

			)
		);

		$this->add_control(
			'premium_notbar_index',
			array(
				'label'       => __( 'Z-index', 'premium-addons-pro' ),
				'description' => __( 'Set a z-index for the notification bar, default is: 1', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'      => 1,
				'selectors'   => array(
					'#premium-notbar-{{ID}}' => 'z-index: {{VALUE}};',
				),
				'condition'   => array(
					'custom_position' => 'yes',
				),
			)
		);

		$this->add_control(
			'onclose_action',
			array(
				'label'       => __( 'On Click', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'hide'    => __( 'Hide', 'premium-addons-pro' ),
					'remove' => __( 'Remove', 'premium-addons-pro' ),
				),
				'default'     => 'hide',
				'label_block' => true,
			)
		);

        $this->add_control(
			'remove_element',
			array(
				'label'       => __( 'Element to Remove', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'widget'    => __( 'Widget', 'premium-addons-pro' ),
					'column' => __( 'Parent Column', 'premium-addons-pro' ),
                    'section' => __( 'Parent Container', 'premium-addons-pro' ),
				),
				'default'     => 'widget',
				'label_block' => true,
                'condition'   => array(
					'onclose_action' => 'remove',
				),
			)
		);

        $this->add_control(
            'remove_element_notice',
            array(
                'raw'             => __( 'This option works on the frontend only.', 'premium-addons-pro' ),
                'type'            => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'condition'       => array(
                    'onclose_action' => 'remove',
                ),
            )
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_content',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_content_type',
			array(
				'label'       => __( 'Content to Show', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'editor'   => __( 'Text Editor', 'premium-addons-pro' ),
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'     => 'editor',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_type' => 'notification',
				),
			)
		);

		$this->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_content_type' => 'template',
					'premium_notbar_type'         => 'notification',
				),
			)
		);

		$this->add_control(
			'premium_notbar_content_temp_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'premium_notbar_content_type' => 'template',
					'premium_notbar_type'         => 'notification',
				),
			)
		);

		$this->add_control(
			'premium_notbar_content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'premium_notbar_content_type' => 'template',
					'premium_notbar_type'         => 'notification',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_title',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'separator' => 'before',
				'default'   => 'Premium Alert Box ',
				'condition' => array(
					'premium_notbar_type' => 'alert',
				),
			)
		);

		$this->add_control(
			'premium_notbar_text',
			array(
				'label'      => __( 'Description', 'premium-addons-pro' ),
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => array( 'active' => true ),
				'default'    => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_notbar_type',
							'value' => 'alert',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'premium_notbar_type',
									'value' => 'notification',
								),
								array(
									'name'  => 'premium_notbar_content_type',
									'value' => 'editor',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_temp_width',
			array(
				'label'     => __( 'Content Width (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'condition' => array(
					// 'premium_notbar_content_type' => 'template',
					'premium_notbar_type' => 'notification',
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-text-container'   => 'width: {{SIZE}}%;',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_text_align',
			array(
				'label'       => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'flex-start'    => array(
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon'  => 'eicon-flex eicon-justify-start-h',
					),
					'center'        => array(
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon'  => 'eicon-flex eicon-justify-center-h',
					),
					'flex-end'      => array(
						'title' => esc_html__( 'End', 'elementor' ),
						'icon'  => 'eicon-flex eicon-justify-end-h',
					),
					'space-between' => array(
						'title' => esc_html__( 'Space Between', 'elementor' ),
						'icon'  => 'eicon-flex eicon-justify-space-between-h',
					),
					'space-around'  => array(
						'title' => esc_html__( 'Space Around', 'elementor' ),
						'icon'  => 'eicon-flex eicon-justify-space-around-h',
					),
					'space-evenly'  => array(
						'title' => esc_html__( 'Space Evenly', 'elementor' ),
						'icon'  => 'eicon-flex eicon-justify-space-evenly-h',
					),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_type' => 'notification',
				),
				'selectors'   => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper' => 'justify-content: {{VALUE}}; text-align: {{VALUE}};',
				),
				'separator'   => 'after',
				'default'     => 'left',
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label'     => __( 'Dismiss Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'eicon-close'         => array(
						'title' => __( 'close', 'premium-addons-pro' ),
						'icon'  => 'eicon-close',
					),
					'far fa-times-circle' => array(
						'title' => __( 'far-circle', 'premium-addons-pro' ),
						'icon'  => 'far fa-times-circle',
					),
					'fas fa-times-circle' => array(
						'title' => __( 'fas-circle', 'premium-addons-pro' ),
						'icon'  => 'fas fa-times-circle',
					),
					'eicon-ban'           => array(
						'title' => __( 'none', 'premium-addons-pro' ),
						'icon'  => 'eicon-ban',
					),
				),
				'separator' => 'before',
				'default'   => 'eicon-close',
			)
		);

		$this->add_responsive_control(
			'close_ver_align',
			array(
				'label'                => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'              => 'top',
				'selectors_dictionary' => array(
					'top'    => 'top: 10px',
					'middle' => 'top: 50%; transform: translateY(-50%)',
					'bottom' => 'bottom: 10px',
				),
				'toggle'               => false,
				'selectors'            => array(
					'#premium-notbar-{{ID}} .premium-notbar-button-wrap'    => '{{VALUE}};',
				),
				'condition'            => array(
					'close_icon!' => 'eicon-ban',
				),
			)
		);

		// $this->add_control(
		// 'premium_notbar_close_hor_position',
		// array(
		// 'label'       => __( 'Horizontal Position', 'premium-addons-pro' ),
		// 'type'        => Controls_Manager::SELECT,
		// 'options'     => array(
		// 'row'         => __( 'After', 'premium-addons-pro' ),
		// 'row-reverse' => __( 'Before', 'premium-addons-pro' ),
		// ),
		// 'selectors'   => array(
		// '{{WRAPPER}} .premium-notbar-content-wrapper'    => '-webkit-flex-direction: {{VALUE}}; flex-direction: {{VALUE}};',
		// ),
		// 'default'     => 'row',
		// 'label_block' => true,
		// 'condition'  => array(
		// 'premium_notbar_type' => 'notification',
		// ),
		// )
		// );

		$this->end_controls_section();

        $this->start_controls_section(
			'icon_section',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'conditions'=> [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'premium_notbar_content_type',
                            'value'=> 'editor'
                        ],
                        [
                            'name' => 'premium_notbar_type',
                            'value'=> 'alert'
                        ]
                    ]
                ]
			)
		);

        $this->add_control(
			'premium_notbar_icon_switcher',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			)
		);

		$common_conditions = array(
			'premium_notbar_icon_switcher' => 'yes',
			// 'premium_notbar_content_type'  => 'editor',
		);

		$this->add_control(
			'premium_notbar_icon_selector',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'font-awesome-icon',
				'options'   => array(
					'font-awesome-icon' => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'custom-image'      => __( 'Custom Image', 'premium-addons-pro' ),
					'animation'         => __( 'Lottie Animation', 'premium-addons-pro' ),
					'svg'               => __( 'SVG Code', 'premium-addons-pro' ),
				),
				'condition' => $common_conditions,
			)
		);

		$this->add_control(
			'premium_notbar_icon_updated',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-exclamation-circle',
					'library' => 'fa-solid',
				),
				'condition' => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => 'font-awesome-icon',
					)
				),
			)
		);

		$this->add_control(
			'premium_notbar_custom_image',
			array(
				'label'     => __( 'Custom Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => 'custom-image',
					)
				),
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => 'svg',
					)
				),
			)
		);

		$this->add_control(
			'lottie_source',
			array(
				'label'   => __( 'Source', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'url'  => __( 'External URL', 'premium-addons-for-elementor' ),
					'file' => __( 'Media File', 'premium-addons-for-elementor' ),
				),
				'default' => 'url',
                'condition'   => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => 'animation',
					)
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
				'condition'   => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => 'animation',
						'lottie_source'                => 'url',
					)
				),
			)
		);

		$this->add_control(
			'lottie_file',
			array(
				'label'      => __( 'Upload JSON File', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'condition'  => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => 'animation',
						'lottie_source'                => 'file',
					)
				),
			)
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
						'premium_notbar_icon_updated[library]!' => 'svg',
					)
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'premium_notbar_icon_switcher',
					'value' => 'yes',
				),
				array(
					'name'  => 'premium_notbar_content_type',
					'value' => 'editor',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_notbar_icon_selector',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_notbar_icon_selector',
											'value' => 'font-awesome-icon',
										),
										array(
											'name'  => 'premium_notbar_icon_selector',
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
					'condition' => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
						)
					),
					'selectors' => array(
						'#premium-notbar-{{ID}} .premium-notbar-icon-wrap svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
							'draw_svg'                     => 'yes',
						)
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
					'condition'   => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
							'draw_svg'                     => 'yes',
						)
					),
				)
			);
		} elseif ( method_exists( 'PremiumAddons\Includes\Helper_Functions', 'get_draw_svg_notice' ) ) {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'alert',
				array_merge(
					$common_conditions,
					array(
						'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
						'premium_notbar_icon_updated[library]!' => 'svg',
					)
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
					'condition'   => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
							'draw_svg'                     => 'yes',
							'lottie_reverse!'              => 'true',
						)
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
					'condition'   => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
							'draw_svg'                     => 'yes',
							'lottie_reverse'               => 'true',
						)
					),

				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-pro' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
							'draw_svg'                     => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-pro' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array_merge(
						$common_conditions,
						array(
							'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
							'draw_svg'                     => 'yes',
							'lottie_loop'                  => 'true',
						)
					),
				)
			);
		}

		$this->add_responsive_control(
			'icon_ver_align',
			array(
				'label'      => __( 'Vertical Alignment', 'premium-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'default'    => 'center',
				'toggle'     => false,
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-wrap'    => 'align-self: {{VALUE}};',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'premium_notbar_icon_switcher',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'premium_notbar_type',
									'value' => 'notification',
								),
								array(
									'terms' => array(
										array(
											'name'  => 'premium_notbar_type',
											'value' => 'alert',
										),
										array(
											'name'  => 'alert_skin',
											'value' => 'skin2',
										),
									),
								),
							),
						),
					),
				),
			)
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'button_section',
			array(
				'label'     => __( 'Button', 'premium-addons-pro' ),
				'condition' => array(
					'premium_notbar_type' => 'notification',
				),
			)
		);

		$this->add_control(
			'premium_notbar_button',
			array(
				'label'       => __( 'Show Button', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable button', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_button_text',
			array(
				'label'     => __( 'Button', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Premium Button',
				'condition' => array(
					'premium_notbar_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_button'         => 'yes',
					'premium_notbar_link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'premium_notbar_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'condition'   => array(
					'premium_notbar_button'         => 'yes',
					'premium_notbar_link_selection' => 'link',
				),
				'label_block' => true,
			)
		);

        $this->add_responsive_control(
			'button_width',
			array(
				'label'     => __( 'Button Container Width (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'premium_notbar_button'         => 'yes',
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-btn-wrap'   => 'width: {{SIZE}}%;',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_button_alignment',
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
				'default'   => 'right',
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-btn-wrap'    => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_button' => 'yes',
                    'button_width[size]!' => ''
				),
			)
		);

        if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
            Helper_Functions::add_btn_hover_controls( $this, array( 'premium_notbar_button' => 'yes' ) );
        }

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_advanced',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_cookies',
			array(
				'label'       => __( 'Use Cookies', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option will use cookies to remember user action', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'cookies_rule',
			array(
				'label'       => __( 'Cookies For Logged In Users', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable cookies also for logged in users', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_notbar_cookies' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_interval',
			array(
				'label'       => __( 'Expiration Time', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'How much time before removing cookie, set the value in hours, default is: 1 hour', 'premium-addons-pro' ),
				'default'     => 1,
				'min'         => 0,
				'condition'   => array(
					'premium_notbar_cookies' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_height',
			array(
				'label'      => __( 'Max Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh', 'custom' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-text-container' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_overflow',
			array(
				'label'       => __( 'Overflow', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'scroll'  => __( 'Scroll', 'premium-addons-pro' ),
					'visible' => __( 'Show', 'premium-addons-pro' ),
                    'hidden' => __( 'Hidden', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'default'     => 'visible',
				'selectors'   => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-text-container' => 'overflow-y: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_style',
			array(
				'label' => __( 'Bar', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// $this->add_control(
		// 'premium_notbar_background',
		// array(
		// 'label'     => __( 'Background Color', 'premium-addons-pro' ),
		// 'type'      => Controls_Manager::COLOR,
		// 'selectors' => array(
		// '#premium-notbar-{{ID}}' => 'background-color: {{VALUE}};',
		// '#premium-notbar-{{ID}} .premium-notbar-icon-text-container' => 'background-color: {{VALUE}};',
		// ),
		// )
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'alertbox_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '#premium-notbar-{{ID}}',
			)
		);

		// $this->add_control(
		// 'background_overlay',
		// array(
		// 'label'     => __( 'Overlay Background Color', 'premium-addons-pro' ),
		// 'type'      => Controls_Manager::COLOR,
		// 'condition' => array(
		// 'enable_background_overlay' => 'yes',
		// ),
		// 'selectors' => array(
		// '#premium-notbar-outer-container-{{ID}} .premium-notbar-background-overlay'   => 'background-color: {{VALUE}};',
		// ),
		// )
		// );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_border',
				'selector' => '#premium-notbar-{{ID}}',
			)
		);

		$this->add_control(
			'premium_notbar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}}' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_notbar_shadow',
				'selector' => '#premium-notbar-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_icon_style',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
                'conditions' => array(
                    'terms' => array(
                        array(
                            'name' => 'premium_notbar_icon_switcher',
                            'value' => 'yes'
                        ),
                        array(
                            'relation'=>'or',
                            'terms' => array(

                                [
                                    'name' => 'premium_notbar_content_type',
                                    'value'=> 'editor'
                                ],
                                [
                                    'name' => 'premium_notbar_type',
                                    'value'=> 'alert'
                                ]

                            )
                        )
                    )
                )
			)
		);

		$this->add_responsive_control(
			'premium_notbar_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'#premium-notbar-{{ID}} .premium-notbar-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
                    '#premium-notbar-{{ID}} .premium-notbar-custom-image' => 'width: {{SIZE}}{{UNIT}} !important;'
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon'   => 'color: {{VALUE}};',
					'#premium-notbar-{{ID}} .premium-notbar-icon-wrap svg, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg *'   => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					// 'global'    => array(
					// 'default' => Global_Colors::COLOR_ACCENT,
					// ),
					'condition' => array(
						'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
					),
					'selectors' => array(
						'#premium-notbar-{{ID}} .premium-notbar-icon-wrap svg *' => 'stroke: {{VALUE}};',
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
				'separator' => 'after',
				'condition' => array(
					'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
					'draw_svg'                     => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}}:hover .premium-notbar-icon'   => 'color: {{VALUE}};',
					'#premium-notbar-{{ID}}:hover .premium-notbar-icon-wrap svg, #premium-notbar-{{ID}}:hover .premium-notbar-icon-wrap svg *'   => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color_hover',
				array(
					'label'     => __( 'Hover Stroke Color', 'premium-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => array(
						'premium_notbar_icon_selector' => array( 'font-awesome-icon', 'svg' ),
					),
					'selectors' => array(
						'#premium-notbar-{{ID}}:hover .premium-notbar-icon-wrap svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_notbar_icon_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg'    => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'skin3_backcolor',
			array(
				'label'     => __( 'Container Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-wrap'    => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_type' => 'alert',
					'alert_skin'          => 'skin3',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_icon_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg',
			)
		);

		$this->add_control(
			'premium_notbar_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_notbar_icon_shadow',
				'selector'  => '#premium-notbar-{{ID}} .premium-notbar-icon',
				'condition' => array(
					'premium_notbar_icon_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'premium_notbar_img_shadow',
				'selector'  => '#premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie',
				'condition' => array(
					'premium_notbar_icon_selector!' => 'font-awesome-icon',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_text_style',
			array(
				'label'     => __( 'Text', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_notbar_content_type' => 'editor',
					'premium_notbar_type'         => 'notification',
				),
			)
		);

		$this->add_control(
			'premium_notbar_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-text'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_text_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-text',
			)
		);

		$this->add_control(
			'premium_notbar_text_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_text_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text',
			)
		);

		$this->add_control(
			'premium_notbar_text_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_text_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-text',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_notbar_text_box_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_text_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_notbar_type' => 'alert',
				),
			)
		);

		$this->add_control(
			'premium_notbar_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-title-wrap'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-title-wrap',
			)
		);

		$this->add_control(
			'premium_notbar_title_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-title-wrap'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_title_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-title-wrap',
			)
		);

		$this->add_control(
			'premium_notbar_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-title-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_title_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-title-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_notbar_title_box_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-title-wrap',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-title-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-title-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_content_style',
			array(
				'label'     => __( 'Content', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_notbar_type' => 'alert',
				),
			)
		);

		$this->add_control(
			'premium_notbar_content_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrap'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_content_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrap',
			)
		);

		$this->add_control(
			'premium_notbar_content_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-content-wrap'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_content_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-content-wrap',
			)
		);

		$this->add_control(
			'premium_notbar_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-content-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_content_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_notbar_content_box_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-content-wrap',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-content-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_button_style',
			array(
				'label'     => __( 'Button', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_notbar_type'   => 'notification',
					'premium_notbar_button' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_button_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button',
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'button_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_button_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-button'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_button_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-button, #premium-notbar-{{ID}} .premium-button-style2-shutinhor:before , #premium-notbar-{{ID}} .premium-button-style2-shutinver:before , #premium-notbar-{{ID}} .premium-button-style5-radialin:before , #premium-notbar-{{ID}} .premium-button-style5-rectin:before'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_button_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button',
			)
		);

		$this->add_control(
			'premium_notbar_button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_button_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_bshadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_button_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-button, #premium-notbar-{{ID}} .premium-button-line6::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_button_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-button:hover, {{WRAPPER}} .premium-button-line6::after'   => 'color: {{VALUE}};',
				),
			)
		);

        $this->add_control(
			'underline_color',
			array(
				'label'     => __( 'Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
                'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-btn-svg'   => 'stroke: {{VALUE}};',
                    '{{WRAPPER}} .premium-button-line2::before, {{WRAPPER}} .premium-button-line4::before, {{WRAPPER}} .premium-button-line5::before, {{WRAPPER}} .premium-button-line5::after, {{WRAPPER}} .premium-button-line6::before, {{WRAPPER}} .premium-button-line7::before'   => 'background-color: {{VALUE}};'
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style8',
				),
			)
		);

		$this->add_control(
			'premium_notbar_button_backcolor_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-button-none:hover, #premium-notbar-{{ID}} .premium-button-style8:hover, #premium-notbar-{{ID}} .premium-button-style1:before, #premium-notbar-{{ID}} .premium-button-style2-shutouthor:before, #premium-notbar-{{ID}} .premium-button-style2-shutoutver:before, #premium-notbar-{{ID}} .premium-button-style2-shutinhor, #premium-notbar-{{ID}} .premium-button-style2-shutinver, #premium-notbar-{{ID}} .premium-button-style2-dshutinhor:before, #premium-notbar-{{ID}} .premium-button-style2-dshutinver:before, #premium-notbar-{{ID}} .premium-button-style2-scshutouthor:before, #premium-notbar-{{ID}} .premium-button-style2-scshutoutver:before, #premium-notbar-{{ID}} .premium-button-style5-radialin, #premium-notbar-{{ID}} .premium-button-style5-radialout:before, #premium-notbar-{{ID}} .premium-button-style5-rectin, #premium-notbar-{{ID}} .premium-button-style5-rectout:before, #premium-notbar-{{ID}} .premium-button-style6-bg, #premium-notbar-{{ID}} .premium-button-style6:before'   => 'background-color: {{VALUE}};',
				),
                'condition' => array(
					'premium_button_hover_effect!' => 'style7',
				),
			)
		);

        $this->add_control(
			'first_layer_hover',
			array(
				'label'     => __( 'Layer #1 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:before' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style7',

				),
			)
		);

		$this->add_control(
			'second_layer_hover',
			array(
				'label'     => __( 'Layer #2 Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button-style7 .premium-button-text-icon-wrapper:after' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'premium_button_hover_effect' => 'style7',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_hover_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button:hover',
			)
		);

		$this->add_control(
			'button_hover_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'button_hover_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_bshadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-button:hover',
			)
		);

		$this->add_responsive_control(
			'button_hover_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-button:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'button_hover_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_close_style',
			array(
				'label'     => __( 'Dismiss Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'close_icon!' => 'eicon-ban',
				),
			)
		);

        $this->add_responsive_control(
			'dismiss_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_close_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_close_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close:hover'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_close_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-close',
			)
		);

		$this->add_control(
			'premium_notbar_close_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_close_backcolor_hover',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close:hover'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_close_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-close',
			)
		);

		$this->add_control(
			'premium_notbar_close_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_close_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-close',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_close_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_close_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Responsive Style
	 *
	 * Returns the responsive style based on Elementor's Breakpoints.
	 *
	 * @access protected
	 * @return string
	 */
	protected function get_responsive_style() {

		$breakpoints = Responsive::get_breakpoints();
		$style       = '<style>';
		$style      .= '@media ( max-width: ' . $breakpoints['md'] . 'px ) {';
		$style      .= '.premium-notbar-content-wrapper, .premium-notbar-icon-text-container {';
		$style      .= 'flex-direction: column !important; -moz-flex-direction: column !important; -webkit-flex-direction: column !important;';
		$style      .= '}';
		$style      .= '}';
		$style      .= '</style>';

		return $style;

	}

	/**
	 * Render Alert Box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$type = $settings['premium_notbar_type'];

		$icon_type = $settings['premium_notbar_icon_selector'];

		$content_type = $settings['premium_notbar_content_type'];

		if ( 'template' === $content_type ) {
			$template = empty( $settings['premium_notbar_content_temp'] ) ? $settings['live_temp_content'] : $settings['premium_notbar_content_temp'];
		}

		$this->add_render_attribute(
			'close_button',
			array(
				'type'  => 'button',
				'class' => 'premium-notbar-close',
			)
		);

		if ( 'yes' === $settings['premium_notbar_button'] ) {

            $effect_class = '';
            if ( version_compare( PREMIUM_ADDONS_VERSION, '4.10.17', '>' ) ) {
                $effect_class = Helper_Functions::get_button_class( $settings );
            }

            $this->add_render_attribute( 'button', array(
                'class'=> array(
                    'premium-notbar-button',
                    $effect_class
                ),
                'data-text' => $settings['premium_notbar_button_text']
            ));

			if ( 'url' === $settings['premium_notbar_link_selection'] ) {
				$this->add_link_attributes( 'button', $settings['premium_notbar_link'] );

			} else {

				$this->add_render_attribute( 'button', 'href', get_permalink( $settings['premium_notbar_existing_link'] ) );
			}


		}

		if ( 'yes' === $settings['premium_notbar_icon_switcher'] && ( 'editor' === $settings['premium_notbar_content_type'] || 'alert' === $type ) ) {

			if ( 'font-awesome-icon' === $icon_type || 'svg' === $icon_type ) {

				if ( 'font-awesome-icon' === $icon_type ) {

					if ( ! empty( $settings['premium_notbar_icon_updated'] ) ) {
						$this->add_render_attribute(
							'icon',
							array(
								'class'       => array(
									'premium-notbar-icon',
									$settings['premium_notbar_icon_updated']['value']
								),
								'aria-hidden' => 'true'
							)
						);

					}
				}

				if ( ( 'yes' === $settings['draw_svg'] && 'font-awesome-icon' === $icon_type ) || 'svg' === $icon_type ) {
					$this->add_render_attribute( 'icon', 'class', 'premium-notbar-icon' );
				}

				if ( 'yes' === $settings['draw_svg'] ) {

					$this->add_render_attribute(
						'alert',
						'class',
						array(
							'elementor-invisible',
							'premium-drawer-hover',
						)
					);

					if ( 'font-awesome-icon' === $icon_type ) {

						$this->add_render_attribute( 'icon', 'class', $settings['premium_notbar_icon_updated']['value'] );

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
			} elseif ( 'custom-image' === $icon_type ) {

				$src = $settings['premium_notbar_custom_image']['url'];

				$alt = Control_Media::get_image_alt( $settings['premium_notbar_custom_image'] );

				$this->add_render_attribute(
					'image',
					array(
						'class' => 'premium-notbar-custom-image',
						'src'   => $src,
						'alt'   => $alt,
					)
				);

			} else {

				$this->add_render_attribute(
					'alert_lottie',
					array(
						'class'               => array(
							'premium-notbar-icon-lottie',
							'premium-lottie-animation',
						),
						'data-lottie-url'     => 'url' === $settings['lottie_source'] ? $settings['lottie_url'] : $settings['lottie_file']['url'],
						'data-lottie-loop'    => $settings['lottie_loop'],
						'data-lottie-reverse' => $settings['lottie_reverse'],
					)
				);

			}
		}

		$bar_settings = array(
			'id'                => $id,
			'type'              => $type,
			'customPos'         => $settings['custom_position'],
			'location'          => 'yes' === $settings['custom_position'] ? $settings['premium_notbar_position'] : 'relative',
			'cookies'           => ( 'yes' === $settings['premium_notbar_cookies'] ) ? true : false,
			'logged'            => ( 'yes' === $settings['cookies_rule'] ) ? true : false,
			'interval'          => ! empty( $settings['premium_notbar_interval'] ) ? $settings['premium_notbar_interval'] : 1,
			'entranceAnimation' => $settings['entrance_animation'],
			'closeAction'       => $settings['onclose_action'],
		);

        if( 'remove' === $settings['onclose_action'] ) {
            $bar_settings['elementToRemove'] = $settings['remove_element'];
        }

		if ( 'yes' === $settings['custom_position'] ) {

			if ( 'alert' === $type ) {
				$settings['premium_notbar_top_select'] = 'fixed';
			}

			$bar_layout = 'premium-notbar-' . $settings['premium_notbar_top_select'];

			$bar_settings['layout']   = $settings['premium_notbar_width'];
			$bar_settings['position'] = $bar_layout;

			$this->add_render_attribute( 'wrap', 'class', 'premium-notbar-' . $settings['premium_notbar_width'] );

			$bar_position = $settings['premium_notbar_position'];

			if ( 'top' !== $bar_position ) {

				$this->add_render_attribute( 'wrap', 'class', 'premium-notbar-' . $bar_position );

			} elseif ( 'top' === $bar_position && is_user_logged_in() ) {

				$this->add_render_attribute( 'wrap', 'class', 'premium-notbar-edit-top ' . $bar_layout );

			} else {

				$this->add_render_attribute( 'wrap', 'class', array( 'premium-notbar-top', $bar_layout ) );

			}
		} else {

			$this->add_render_attribute(
				'wrap',
				array(
					'class' => array(
						'premium-notbar-position-empty',
					),
				)
			);

		}

		$this->add_render_attribute( 'text', 'class', 'premium-notbar-text' );

		$this->add_render_attribute(
			'alert',
			array(
				'id'            => 'premium-notbar-outer-container-' . $id,
				'class'         => array(
					'premium-notbar-outer-container',
					'premium-notbar-' . $settings['premium_notbar_content_type'],
					'premium-notbar-' . $type,
					'elementor-invisible',
				),
				'data-settings' => wp_json_encode( $bar_settings ),
			)
		);

		$this->add_render_attribute(
			'wrap',
			array(
				'id'    => 'premium-notbar-' . $id,
				'class' => 'premium-notbar',
			)
		);

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'alert' ) ); ?>>


		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrap' ) ); ?>>
			<div class="premium-notbar-content-wrapper">

				<div class="premium-notbar-icon-text-container">

					<?php if ( 'yes' === $settings['premium_notbar_icon_switcher'] && ( 'editor' === $settings['premium_notbar_content_type'] || 'alert' === $type ) ) : ?>
						<div class="premium-notbar-icon-wrap">
							<?php
							if ( 'font-awesome-icon' === $icon_type ) :
								if ( 'yes' !== $settings['draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['premium_notbar_icon_updated'],
										array(
											'class'       => 'premium-notbar-icon',
											'aria-hidden' => 'true',
										)
									);
								else : ?>
                                    <i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
                                <?php endif;
							elseif ( 'svg' === $icon_type ) : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
									<?php $this->print_unescaped_setting( 'custom_svg' ); ?>
								</div>
                            <?php elseif ( 'custom-image' === $icon_type ) : ?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'image' ) ); ?>>
							<?php else : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'alert_lottie' ) ); ?>></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( 'notification' === $type ) : ?>
						<?php if ( 'editor' === $content_type ) : ?>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'text' ) ); ?>>
								<?php Utils::print_unescaped_internal_string( $this->parse_text_editor( $settings['premium_notbar_text'] ) ); ?>
							</span>
							<?php
						else :
							echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;
						?>
					<?php else : ?>
						<div>
							<?php if ( ! empty( $settings['premium_notbar_title'] ) ) : ?>
								<span class="premium-notbar-title-wrap">
									<?php echo $this->parse_text_editor( $settings['premium_notbar_title'] ); ?>
								</span>
							<?php endif; ?>

							<?php if ( ! empty( $settings['premium_notbar_text'] ) ) : ?>
								<div class="premium-notbar-content-wrap">
									<?php echo wp_kses_post( $settings['premium_notbar_text'] ); ?>
								</div>
							<?php endif; ?>

							<?php if ( 'eicon-ban' !== $settings['close_icon'] ) : ?>
								<div class="premium-notbar-button-wrap">
									<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'close_button' ) ); ?>>
										<i class="<?php echo wp_kses_post( $settings['close_icon'] ); ?>"></i>
									</a>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( 'notification' === $type ) : ?>
					<?php if ( 'yes' === $settings['premium_notbar_button'] ) : ?>
						<div class="premium-notbar-btn-wrap">
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
								<div class="premium-button-text-icon-wrapper">
                                    <span><?php echo wp_kses_post( $settings['premium_notbar_button_text'] ); ?></span>
                                </div>
                                <?php if ( 'style6' === $settings['premium_button_hover_effect'] && 'yes' === $settings['mouse_detect'] ) : ?>
                                    <span class="premium-button-style6-bg"></span>
                                <?php endif; ?>

                                <?php if ( 'style8' === $settings['premium_button_hover_effect'] ) : ?>
                                    <?php echo Helper_Functions::get_btn_svgs( $settings['underline_style'] ); ?>
                                <?php endif; ?>

							</a>
						</div>
					<?php endif; ?>

					<?php if ( 'eicon-ban' !== $settings['close_icon'] ) : ?>
						<div class="premium-notbar-button-wrap">
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'close_button' ) ); ?>>
								<i class="<?php echo wp_kses_post( $settings['close_icon'] ); ?>"></i>
							</a>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

		<?php
		echo $this->get_responsive_style();
	}
}
