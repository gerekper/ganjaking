<?php
/**
 * Icon Box widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Happy_Addons_Pro\Template_Query_Manager;

defined( 'ABSPATH' ) || die();

class Off_Canvas extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Off Canvas', 'happy-addons-pro' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/icon-box/';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-offcanvas-menu';
	}

	public function get_keywords() {
		return [ 'info', 'box', 'icon' ];
	}

	protected function register_content_controls() {
		$this->register_ha_content_offcanvas_controls();
		$this->register_ha_content_toggle_controls();
		$this->register_ha_content_close_bar_controls();
		$this->register_ha_content_settings_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
	protected function register_ha_content_offcanvas_controls(){
		/**
		 * Content Tab: Offcanvas Content
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_offcanvas_content',
			[
				'label'                 => __( 'Offcanvas Content', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'content_type',
			[
				'label'                 => __( 'Content Type', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT2,
				'multiple'              => false,
				'options'               => [
					'sidebar'   => __( 'Sidebar', 'happy-addons-pro' ),
					'custom'    => __( 'Custom Content', 'happy-addons-pro' ),
					'section'   => __( 'Saved Section', 'happy-addons-pro' ),
					'widget'    => __( 'Saved Widget', 'happy-addons-pro' ),
					'template'  => __( 'Saved Page Template', 'happy-addons-pro' ),
				],
				'default'               => 'custom',
			]
		);


		$this->add_control(
			'sidebar',
			[
				'label'                 => __( 'Choose Sidebar', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT2,
				'label_block'           => false,
				'multiple'              => false,
				'options'               => Template_Query_Manager::get_registered_sidebars(),
				'condition'             => [
					'content_type' 		=> 'sidebar',
				],
			]
		);


		$this->add_control(
			'saved_widget',
			[
                'label' 				=> __('Choose Widget', 'happy-addons-pro'),
                'type' 					=> Controls_Manager::SELECT2,
				'label_block'           => false,
				'multiple'              => false,
                'options' 				=> Template_Query_Manager::get_page_template_options('widget'),
                'condition' 			=> [
                    'content_type' 		=> 'widget',
                ],
            ]
		);

		$this->add_control(
			'saved_section',
			[
				'label'                 => __( 'Choose Section', 'happy-addons-pro' ),
				'type' 					=> Controls_Manager::SELECT2,
				'label_block'           => false,
				'multiple'              => false,
				'options' 				=> Template_Query_Manager::get_page_template_options('section'),
				'condition'             => [
					'content_type'    	=> 'section',
				],
			]
		);

		$this->add_control(
			'templates',
			[
				'label'                 => __( 'Choose Template', 'happy-addons-pro' ),
				'type' 					=> Controls_Manager::SELECT2,
				'label_block'           => false,
				'multiple'              => false,
				'options' 				=> Template_Query_Manager::get_page_template_options('page'),
				'condition'             => [
					'content_type'    	=> 'template',
				],
			]
		);

		$this->add_control(
			'custom_content',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'title'       => __( 'Box 1', 'happy-addons-pro' ),
						'description' => __( 'Text box description goes here', 'happy-addons-pro' ),
					],
					[
						'title'       => __( 'Box 2', 'happy-addons-pro' ),
						'description' => __( 'Text box description goes here', 'happy-addons-pro' ),
					],
				],
				'fields'                => [
					[
						'name'              => 'title',
						'label'             => __( 'Title', 'happy-addons-pro' ),
						'type'              => Controls_Manager::TEXT,
						'dynamic'           => [
							'active'   => true,
						],
						'default'           => __( 'Title', 'happy-addons-pro' ),
					],
					[
						'name'              => 'description',
						'label'             => __( 'Description', 'happy-addons-pro' ),
						'type'              => Controls_Manager::WYSIWYG,
						'dynamic'           => [
							'active'   => true,
						],
						'default'           => '',
					],
				],
				'title_field'           => '{{{ title }}}',
				'condition'             => [
					'content_type'  => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function register_ha_content_toggle_controls(){
		/**
		 * Content Tab: Toggle
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_button_settings',
			[
				'label'                 => __( 'Toggle', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'toggle_source',
			[
				'label'                 => __( 'Toggle Source', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'burger',
				'options'               => [
					'button'        => __( 'Button', 'happy-addons-pro' ),
					'burger'        => __( 'Burger Icon', 'happy-addons-pro' ),
					'element-class' => __( 'Element Class', 'happy-addons-pro' ),
					'element-id'    => __( 'Element ID', 'happy-addons-pro' ),
				],
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'toggle_position',
			[
				'label'                 => __( 'Position', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'inline',
				'options'               => [
					'inline'        => __( 'Inline', 'happy-addons-pro' ),
					'floating'      => __( 'Floating', 'happy-addons-pro' ),
				],
				'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => [ 'button', 'burger' ],
				],
			]
		);

		$this->add_control(
			'floating_toggle_placement',
			[
				'label'                 => __( 'Placement', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'middle-right',
				'options'               => [
					'top-left'      => __( 'Top Left', 'happy-addons-pro' ),
					'top-center'    => __( 'Top Center', 'happy-addons-pro' ),
					'top-right'     => __( 'Top Right', 'happy-addons-pro' ),
					'middle-left'   => __( 'Middle Left', 'happy-addons-pro' ),
					'middle-right'  => __( 'Middle Right', 'happy-addons-pro' ),
					'bottom-right'  => __( 'Bottom Right', 'happy-addons-pro' ),
					'bottom-center' => __( 'Bottom Center', 'happy-addons-pro' ),
					'bottom-left'   => __( 'Bottom Left', 'happy-addons-pro' ),
				],
				'prefix_class'          => 'ha-floating-element-align-',
				'condition'             => [
					'toggle_source'     => [ 'button', 'burger' ],
					'toggle_position'   => 'floating',
				],
			]
		);

		$this->add_control(
			'toggle_zindex',
			[
				'label'                 => __( 'Z-Index', 'happy-addons-pro' ),
				'description'           => __( 'Adjust the z-index of the floating toggle. Defaults to 999', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => '999',
				'min'                   => 0,
				'step'                  => 1,
				'selectors'             => [
					'{{WRAPPER}} .ha-floating-element' => 'z-index: {{SIZE}};',
				],
				'condition'             => [
					'toggle_source'     => [ 'button', 'burger' ],
					'toggle_position'   => 'floating',
				],
			]
		);

		$this->add_control(
			'toggle_class',
			[
				'label'                 => __( 'Toggle CSS Class', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => '',
				'frontend_available'    => true,
				'condition'             => [
					'toggle_source'     => 'element-class',
				],
			]
		);

		$this->add_control(
			'toggle_id',
			[
				'label'                 => __( 'Toggle CSS ID', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => '',
				'frontend_available'    => true,
				'condition'             => [
					'toggle_source'     => 'element-id',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'                 => __( 'Button Text', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Click Here', 'happy-addons-pro' ),
				'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => 'button',
				],
			]
		);

		$this->add_control(
			'select_button_icon',
			[
				'label'                 => __( 'Button Icon', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'button_icon',
				'condition'             => [
					'toggle_source'     => 'button',
				],
			]
		);

		$this->add_control(
			'button_icon_position',
			[
				'label'                 => __( 'Icon Position', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'before',
				'options'               => [
					'before'    => __( 'Before', 'happy-addons-pro' ),
					'after'     => __( 'After', 'happy-addons-pro' ),
				],
				'prefix_class'          => 'ha-offcanvas-icon-',
				'condition'             => [
					'toggle_source'     => 'button',
					'select_button_icon[value]!'    => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_spacing',
			[
				'label'                 => __( 'Icon Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => 5,
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 50,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}}.ha-offcanvas-icon-before .ha-offcanvas-toggle-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.ha-offcanvas-icon-after .ha-offcanvas-toggle-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'toggle_source'     => 'button',
					'select_button_icon[value]!'    => '',
				],
			]
		);

		$this->add_control(
			'toggle_effect',
			[
				'label'                 => __( 'Animation', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT2,
				'multiple'				=> false,
				'default'               => 'arrow',
				'options'               => [
					''              => __( 'None', 'happy-addons-pro' ),
					'arrow'         => __( 'Arrow Left', 'happy-addons-pro' ),
					'arrow-r'       => __( 'Arrow Right', 'happy-addons-pro' ),
					'arrowalt'      => __( 'Arrow Alt Left', 'happy-addons-pro' ),
					'arrowalt-r'    => __( 'Arrow Alt Right', 'happy-addons-pro' ),
					'arrowturn'     => __( 'Arrow Turn Left', 'happy-addons-pro' ),
					'arrowturn-r'   => __( 'Arrow Turn Right', 'happy-addons-pro' ),
					'boring'        => __( 'Boring', 'happy-addons-pro' ),
					'collapse'      => __( 'Collapse Left', 'happy-addons-pro' ),
					'collapse-r'    => __( 'Collapse Right', 'happy-addons-pro' ),
					'elastic'       => __( 'Elastic Left', 'happy-addons-pro' ),
					'elastic-r'     => __( 'Elastic Right', 'happy-addons-pro' ),
					'emphatic'      => __( 'Emphatic Left', 'happy-addons-pro' ),
					'emphatic-r'    => __( 'Emphatic Right', 'happy-addons-pro' ),
					'minus'         => __( 'Minus', 'happy-addons-pro' ),
					'slider'        => __( 'Slider Left', 'happy-addons-pro' ),
					'slider-r'      => __( 'Slider Right', 'happy-addons-pro' ),
					'spin'          => __( 'Spin Left', 'happy-addons-pro' ),
					'spin-r'        => __( 'Spin Right', 'happy-addons-pro' ),
					'spring'        => __( 'Spring Left', 'happy-addons-pro' ),
					'spring-r'      => __( 'Spring Right', 'happy-addons-pro' ),
					'squeeze'       => __( 'Squeeze', 'happy-addons-pro' ),
					'stand'         => __( 'Stand Left', 'happy-addons-pro' ),
					'stand-r'       => __( 'Stand Right', 'happy-addons-pro' ),
					'vortex'        => __( 'Vortex Left', 'happy-addons-pro' ),
					'vortex-r'      => __( 'Vortex Right', 'happy-addons-pro' ),
					'3dx'           => __( '3DX', 'happy-addons-pro' ),
					'3dy'           => __( '3DY', 'happy-addons-pro' ),
					'3dxy'          => __( '3DXY', 'happy-addons-pro' ),
				],
				'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => 'burger',
				],
			]
		);

		$this->add_control(
			'burger_label',
			[
				'label'                 => __( 'Label', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Menu', 'happy-addons-pro' ),
				'condition'             => [
					'toggle_source'     => 'burger',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_ha_content_close_bar_controls(){
		/**
		 * Content Tab: Close bar
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_close_bar',
			[
				'label'                 => __( 'Close Bar', 'happy-addons-pro' ),
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'close_bar_absolute',
			[
				'label'             => __( 'Overlapping Close Bar', 'happy-addons-pro' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_on'          => __( 'Yes', 'happy-addons-pro' ),
				'label_off'         => __( 'No', 'happy-addons-pro' ),
				'return_value'      => 'yes',
			]
		);

		$this->add_control(
			'close_button_align',
			[
				'label'  => __( 'Close Button Alignment', 'happy-addons-pro' ),
				'type'   => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					]
				],
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-header'   => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'close_bar_additional' => 'none',
				],
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'select_close_button_icon',
			[
				'label'                 => __( 'Close Icon', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::ICONS,
				'label_block'           => false,
				'fa4compatibility'      => 'close_button_icon',
				'default'               => [
					'value'     => 'fas fa-times',
					'library'   => 'fa-solid',
				],
				'recommended'           => [
					'fa-regular' => [
						'times-circle',
					],
					'fa-solid' => [
						'times',
						'times-circle',
					],
				],
				'skin'                  => 'inline',
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'select_close_button_title',
			[
				'label'                 => __( 'Close Icon Title', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'select_close_button_icon_position',
			[
				'label' => __( 'Close Title Position', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'after'  => __( 'Before', 'happy-addons-pro' ),
					'before' => __( 'After', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'close_bar_additional',
			[
				'label' => __( 'Additional Content', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'happy-addons-pro' ),
					'logo'  => __( 'Logo', 'happy-addons-pro' ),
					'button'  => __( 'Action Button', 'happy-addons-pro' ),
					'link'  => __( 'Text Link', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'close_bar_logo',
			[
				'label' 	=> __( 'Choose Logo Image', 'happy-addons-pro' ),
				'type' 		=> \Elementor\Controls_Manager::MEDIA,
				'default' 	=> [
					'url' 	=> \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'close_bar_additional' => 'logo',
				],
			]
		);

		$this->add_control(
			'close_bar_button',
			[
				'label' => __( 'Button Text', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default title', 'happy-addons-pro' ),
				'placeholder' => __( 'Type your title here', 'happy-addons-pro' ),
				'condition' => [
					'close_bar_additional' => 'button',
				],
			]
		);

		$this->add_control(
			'close_bar_text',
			[
				'label' => __( 'Link Text', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default title', 'happy-addons-pro' ),
				'placeholder' => __( 'Type your title here', 'happy-addons-pro' ),
				'condition' => [
					'close_bar_additional' => 'link',
				],
			]
		);

		$this->add_control(
			'close_bar_link',
			[
				'label' => __( 'Link for Logo/Button/Text', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'happy-addons-pro' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition' => [
					'close_bar_additional!' => 'none',
				],
			]
		);

		$this->add_control(
			'hr_2',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'close_bar_additional_alignment',
			[
				'label'                 => __( 'Additional Content Alignment', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					1      => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					3      => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'               => 1,
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional'   => 'order: {{VALUE}};',
				],
				'condition' => [
					'close_bar_additional!' => 'none',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_ha_content_settings_controls(){
		/**
		 * Content Tab: Settings
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_settings',
			[
				'label'                 => __( 'Settings', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'direction',
			[
				'label'                 => __( 'Direction', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'happy-addons-pro' ),
						'icon'      => 'eicon-h-align-left',
					],
					'right'         => [
						'title'     => __( 'Right', 'happy-addons-pro' ),
						'icon'      => 'eicon-h-align-right',
					],
					'top'         => [
						'title'     => __( 'Top', 'happy-addons-pro' ),
						'icon'      => 'eicon-v-align-top',
					],
					'bottom'         => [
						'title'     => __( 'Bottom', 'happy-addons-pro' ),
						'icon'      => 'eicon-v-align-bottom',
					],
				],
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'content_transition',
			[
				'label'                 => __( 'Content Transition', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'slide',
				'options'               => [
					'slide'                 => __( 'Slide', 'happy-addons-pro' ),
					'reveal'                => __( 'Reveal', 'happy-addons-pro' ),
					'push'                  => __( 'Push', 'happy-addons-pro' ),
					'slide-along'           => __( 'Slide Along', 'happy-addons-pro' ),
				],
				'frontend_available'    => true,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'close_button',
			[
				'label'             => __( 'Show Close Bar', 'happy-addons-pro' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __( 'Yes', 'happy-addons-pro' ),
				'label_off'         => __( 'No', 'happy-addons-pro' ),
				'return_value'      => 'yes',
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'esc_close',
			[
				'label'             => __( 'Esc to Close', 'happy-addons-pro' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __( 'Yes', 'happy-addons-pro' ),
				'label_off'         => __( 'No', 'happy-addons-pro' ),
				'return_value'      => 'yes',
			]
		);

		$this->add_control(
			'body_click_close',
			[
				'label'             => __( 'Click anywhere to Close', 'happy-addons-pro' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __( 'Yes', 'happy-addons-pro' ),
				'label_off'         => __( 'No', 'happy-addons-pro' ),
				'return_value'      => 'yes',
			]
		);

		$this->add_control(
			'links_click_close',
			[
				'label'             => __( 'Click links to Close', 'happy-addons-pro' ),
				'description'       => __( 'Click on links inside offcanvas body to close the offcanvas bar', 'happy-addons-pro' ),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => '',
				'label_on'          => __( 'Yes', 'happy-addons-pro' ),
				'label_off'         => __( 'No', 'happy-addons-pro' ),
				'return_value'      => 'yes',
			]
		);

		$this->end_controls_section();
	}


	protected function register_style_controls() {
		$this->register_ha_style_offcanvas_controls();
		$this->register_ha_style_content_controls();
		$this->register_ha_style_toggle_controls();
		$this->register_ha_style_close_button_controls();
		$this->register_ha_style_overlay_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Style TAB
	/*-----------------------------------------------------------------------------------*/
	protected function register_ha_style_offcanvas_controls(){

		/**
		 * Style Tab: Offcanvas Bar
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_offcanvas_bar_style',
			[
				'label'                 => __( 'Offcanvas Content', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'offcanvas_bar_width',
			[
				'label'                 => __( 'Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => 300,
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 100,
						'max'   => 1000,
						'step'  => 1,
					],
					'%'         => [
						'min'   => 1,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}}' => 'width: {{SIZE}}{{UNIT}}',
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}}.ha-offcanvas-content-top, .ha-offcanvas-content.ha-offcanvas-content-{{ID}}.ha-offcanvas-content-bottom' => 'width: 100%; height: {{SIZE}}{{UNIT}}',

					'.ha-offcanvas-content-reveal.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-left .ha-offcanvas-container,
                    .ha-offcanvas-content-push.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-left .ha-offcanvas-container,
                    .ha-offcanvas-content-slide-along.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-left .ha-offcanvas-container' => 'transform: translate3d({{SIZE}}{{UNIT}}, 0, 0)',

					'.ha-offcanvas-content-reveal.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-right .ha-offcanvas-container,
                    .ha-offcanvas-content-push.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-right .ha-offcanvas-container,
                    .ha-offcanvas-content-slide-along.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-right .ha-offcanvas-container' => 'transform: translate3d(-{{SIZE}}{{UNIT}}, 0, 0)',

					'.ha-offcanvas-content-reveal.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-top .ha-offcanvas-container,
                    .ha-offcanvas-content-push.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-top .ha-offcanvas-container,
                    .ha-offcanvas-content-slide-along.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-top .ha-offcanvas-container' => 'transform: translate3d(0, {{SIZE}}{{UNIT}}, 0)',

					'.ha-offcanvas-content-reveal.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-bottom .ha-offcanvas-container,
                    .ha-offcanvas-content-push.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-bottom .ha-offcanvas-container,
                    .ha-offcanvas-content-slide-along.ha-offcanvas-content-{{ID}}-open.ha-offcanvas-content-bottom .ha-offcanvas-container' => 'transform: translate3d(0, -{{SIZE}}{{UNIT}}, 0)',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'offcanvas_bar_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}}',
			]
		);

		$this->add_control(
			'offcanvas_bar_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'offcanvas_bar_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'offcanvas_bar_box_shadow',
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}}',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'offcanvas_bar_bg_tite',
			[
				'label' => __( 'Background', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'offcanvas_bar_bg',
				'label'                 => __( 'Background', 'happy-addons-pro' ),
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}}',
			]
		);

		$this->end_controls_section();
	}
	protected function register_ha_style_content_controls(){

		/**
		 * Style Tab: Content
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_popup_content_style',
			[
				'label'                 => __( 'Content', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'                 => __( 'Alignment', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify'   => [
						'title' => __( 'Justified', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-body'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'widget_heading',
			[
				'label'                 => __( 'Box', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'widgets_bg_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-custom-widget, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .widget' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'widgets_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-custom-widget, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .widget',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'widgets_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-custom-widget, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_responsive_control(
			'widgets_bottom_spacing',
			[
				'label'                 => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '20',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 60,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-custom-widget, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .widget' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_responsive_control(
			'widgets_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-custom-widget, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'                 => __( 'Title', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'content_title_color',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-widget-title' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'widget_title_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-widget-title',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);


		$this->add_control(
			'text_heading',
			[
				'label'                 => __( 'Description', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'content_text_color',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-widget-content, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-widget-content *:not(.fa):not(.eicon)' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'text_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-widget-content, .ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-widget-content *:not(.fa):not(.eicon)',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'links_heading',
			[
				'label'                 => __( 'Links', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->start_controls_tabs( 'tabs_links_style' );

		$this->start_controls_tab(
			'tab_links_normal',
			[
				'label'                 => __( 'Normal', 'happy-addons-pro' ),
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'content_links_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-body a' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'links_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-body a',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_links_hover',
			[
				'label'                 => __( 'Hover', 'happy-addons-pro' ),
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'content_links_color_hover',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-body a:hover' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	protected function register_ha_style_toggle_controls(){

		/**
		 * Style Tab: Toggle
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_toggle_button_style',
			[
				'label'                 => __( 'Toggle', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'                 => __( 'Alignment', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'happy-addons-pro' ),
						'icon'      => 'eicon-h-align-left',
					],
					'center'        => [
						'title'     => __( 'Center', 'happy-addons-pro' ),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __( 'Right', 'happy-addons-pro' ),
						'icon'      => 'eicon-h-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .ha-offcanvas-toggle-wrap'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_icon_heading',
			[
				'label'                 => __( 'Icon', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => 'burger',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_icon_size',
			[
				'label'                 => __( 'Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => 16,
				],
				'range'                 => [
					'pt'        => [
						'min'   => 6,
						'max'   => 300,
						'step'  => 1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .ha-hamburger-box' => 'font-size: {{SIZE}}pt',
				],
				'condition'             => [
					'toggle_source'     => 'burger',
				],
			]
		);

		$this->add_control(
			'toggle_label_heading',
			[
				'label'                 => __( 'Label', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => 'burger',
					'burger_label!'     => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'button_typography',
				'label'                 => __( 'Typography', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .ha-offcanvas-toggle',
				'condition'             => [
					'toggle_source'     => [ 'button', 'burger' ],
					'burger_label!'     => '',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'                 => __( 'Button Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'md',
				'options'               => [
					'xs' => __( 'Extra Small', 'happy-addons-pro' ),
					'sm' => __( 'Small', 'happy-addons-pro' ),
					'md' => __( 'Medium', 'happy-addons-pro' ),
					'lg' => __( 'Large', 'happy-addons-pro' ),
					'xl' => __( 'Extra Large', 'happy-addons-pro' ),
				],
				'condition'             => [
					'toggle_source'     => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .ha-offcanvas-toggle:hover',
			]
		);

		$this->add_control(
			'sep1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'                 => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ha-offcanvas-toggle' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-offcanvas-toggle svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-hamburger-inner, {{WRAPPER}} .ha-hamburger-inner::before, {{WRAPPER}} .ha-hamburger-inner::after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'button_border_normal',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .ha-offcanvas-toggle',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-offcanvas-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .ha-offcanvas-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .ha-offcanvas-toggle',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'button_bg_color_normal',
				'label'                 => __( 'Background', 'happy-addons-pro' ),
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .ha-offcanvas-toggle',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'                 => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'button_bg_color_hover',
				'label'                 => __( 'Background', 'happy-addons-pro' ),
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .ha-offcanvas-toggle:hover',
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ha-offcanvas-toggle:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-offcanvas-toggle:hover svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-offcanvas-toggle:hover .ha-hamburger-inner, {{WRAPPER}} .ha-offcanvas-toggle:hover .ha-hamburger-inner::before, {{WRAPPER}} .ha-offcanvas-toggle:hover .ha-hamburger-inner::after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ha-offcanvas-toggle:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_animation',
			[
				'label'                 => __( 'Animation', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'toggle_label_spacing',
			[
				'label'                 => __( 'Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 30,
						'step'  => 1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .ha-hamburger-label' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'toggle_source'     => 'burger',
					'burger_label!'     => '',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function register_ha_style_close_button_controls(){

		/**
		 * Style Tab: Close Button
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_close_button_style',
			[
				'label'                 => __( 'Close Bar', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'close_bar__styles',
			[
				'label' => __( 'Close Bar Styles', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'close_bar_original_padding',
			[
				'label' => __( 'Icon Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'close_bar_original_bg',
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-header',
			]
		);

		$this->add_control(
			'close_bar_icon_styles',
			[
				'label' => __( 'Close Icon Styles', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'close_button_text_color',
			[
				'label'                 => __( 'Icon & Text Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-close-{{ID}}' => 'color: {{VALUE}}',
					'.ha-offcanvas-close-{{ID}} svg' => 'fill: {{VALUE}}',
				],
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_size',
			[
				'label'                 => __( 'Icon Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '28',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}}' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'close_button_title_typography',
				'label' => __( 'Text Typography', 'happy-addons-pro' ),
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}} .ha-offcanvas-close-bar-close-title',
			]
		);

		$this->add_control(
			'close_button_icon_padding',
			[
				'label' => __( 'Icon Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'close_button_icon_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_title_size_before',
			[
				'label'                 => __( 'Close Icon Title Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '5',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}} .ha-offcanvas-close-bar-close-title-before' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'close_button' => 'yes',
					'select_close_button_icon_position' => 'before',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_title_size_after',
			[
				'label'                 => __( 'Close Icon Title Spacing', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '5',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}} .ha-offcanvas-close-bar-close-title-after' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'close_button' => 'yes',
					'select_close_button_icon_position' => 'after',
				],
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'close_bar_icon_bg',
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-{{ID}}',
			]
		);

		$this->add_control(
			'additional_styles',
			[
				'label' => __( 'Additional Content Styles', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'             => [
					'close_bar_additional!' => 'none',
				],
			]
		);


		$this->add_responsive_control(
			'close_bar_logo_height',
			[
				'label'                 => __( 'Logo Height', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '30',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 200,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px' ],
				'selectors'             => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-logo img' => 'height: {{SIZE}}{{UNIT}}; width: auto; display: block;',
				],
				'condition'             => [
					'close_bar_additional' => 'logo',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_title_stat',[
			'condition'             => [
				'close_bar_additional' => 'button',
			],
		]);

		$this->start_controls_tab(
			'_tab_title_button_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'close_bar_button_bg',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-button',
			]
		);

		$this->add_control(
			'close_bar_button_txt_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_title_button_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'close_bar_button_bg_hover',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-button:hover',
			]
		);

		$this->add_control(
			'close_bar_button_txt_color_hover',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->start_controls_tabs( '_tabs_title_link',[
			'condition'             => [
				'close_bar_additional' => 'link',
			],
		]);

		$this->start_controls_tab(
			'_tab_title_link_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'close_bar_link_txt_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-link' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'close_bar_link_txt_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_title_link_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'close_bar_link_txt_color_hover',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-link:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'close_bar_link_txt_typography_hover',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-offcanvas-content.ha-offcanvas-content-{{ID}} .ha-offcanvas-close-bar-additional .ha-offcanvas-close-bar-additional-link:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	protected function register_ha_style_overlay_controls(){

		/**
		 * Style Tab: Overlay
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_overlay_style',
			[
				'label'                 => __( 'Overlay', 'happy-addons-pro' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_bg_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.ha-offcanvas-content-{{ID}}-open .ha-offcanvas-container:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label'                 => __( 'Opacity', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 1,
						'step'  => 0.01,
					],
				],
				'selectors'             => [
					'.ha-offcanvas-content-{{ID}}-open .ha-offcanvas-container:after' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();
	}



	/**
	 * Render offcanvas content widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$settings_attr = array(
			'toggle_source'     => esc_attr( $settings['toggle_source'] ),
			'toggle_id'         => esc_attr( $settings['toggle_id'] ),
			'toggle_class'      => esc_attr( $settings['toggle_class'] ),
			'content_id'        => esc_attr( $this->get_id() ),
			'transition'        => esc_attr( $settings['content_transition'] ),
			'direction'         => esc_attr( $settings['direction'] ),
			'esc_close'         => esc_attr( $settings['esc_close'] ),
			'body_click_close'  => esc_attr( $settings['body_click_close'] ),
			'links_click_close' => esc_attr( $settings['links_click_close'] ),
		);

		$this->add_render_attribute( 'content-wrap', 'class', 'ha-offcanvas-content-wrap' );

		$this->add_render_attribute( 'content-wrap', 'data-settings', htmlspecialchars( json_encode( $settings_attr ) ) );

		$this->add_render_attribute( 'content', 'class', [
			'ha-offcanvas-content',
			'ha-offcanvas-content-' . $this->get_id(),
			'ha-offcanvas-' . $settings_attr['transition'],
			'elementor-element-' . $this->get_id(),
		] );

		$this->add_render_attribute( 'content', 'class', 'ha-offcanvas-content-' . $settings['direction'] );

		$this->add_render_attribute( 'toggle-button', 'class', [
			'ha-offcanvas-toggle',
			'ha-offcanvas-toggle-' . esc_attr( $this->get_id() ),
			'elementor-button',
			'elementor-size-' . $settings['button_size'],
		] );

		if ( $settings['button_animation'] ) {
			$this->add_render_attribute( 'toggle-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
		}

		$this->add_render_attribute( 'hamburger', 'class', [
			'ha-offcanvas-toggle',
			'ha-offcanvas-toggle-' . esc_attr( $this->get_id() ),
			'ha-button',
			'ha-hamburger',
			'ha-hamburger--' . $settings['toggle_effect'],
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'content-wrap' ); ?>>
			<?php
				$has_placeholder = true;
				$placeholder = '';

			if ( 'button' === $settings['toggle_source'] || 'burger' === $settings['toggle_source'] ) {
				if ( 'floating' === $settings['toggle_position'] ) {
					$has_placeholder = true;
					$placeholder .= __( 'You set Offcanvas toggle to float.', 'happy-addons-pro' );
				} else {
					$has_placeholder = false;
				}

				// Toggle
				$this->render_toggle();
			} else {
				$has_placeholder = true;
				$placeholder .= __( 'You have selected to open offcanvas bar using another element.', 'happy-addons-pro' );
			}

			if ( $has_placeholder ) {
				$placeholder .= ' ' . __( 'This is just a placeholder & will not be shown on the live page.', 'happy-addons-pro' );

				if (\Elementor\Plugin::instance()->editor->is_edit_mode() ) {

				$args = [];
				$defaults = [
					'title' => $this->get_title(),
					'body'  => $placeholder,
				];

				$args = wp_parse_args( $args, $defaults );

				$this->add_render_attribute([
					'placeholder' => [
						'class' => 'ha-editor-placeholder',
					],
					'placeholder-title' => [
						'class' => 'ha-editor-placeholder-title',
					],
					'placeholder-content' => [
						'class' => 'ha-editor-placeholder-content',
					],
				]);

				?><div <?php echo $this->get_render_attribute_string( 'placeholder' ); ?>>
					<h4 <?php echo $this->get_render_attribute_string( 'placeholder-title' ); ?>>
						<?php echo $args['title']; ?>
					</h4>
					<div <?php echo $this->get_render_attribute_string( 'placeholder-content' ); ?>>
						<?php echo $args['body']; ?>
					</div>
				</div><?php
				}
			}
			?>

			<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
				<?php echo $this->render_close_button(); ?>
				<div class="ha-offcanvas-body">
				<?php
				if ( 'sidebar' === $settings['content_type'] ) {

					$this->render_sidebar();

				} elseif ( 'custom' === $settings['content_type'] ) {

					$this->render_custom_content();

				}
				elseif ( 'section' === $settings['content_type'] && ! empty( $settings['saved_section'] ) ) {

					echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_section'] );

				} elseif ( 'template' === $settings['content_type'] && ! empty( $settings['templates'] ) ) {

					echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['templates'] );

				} elseif ( 'widget' === $settings['content_type'] && ! empty( $settings['saved_widget'] ) ) {

					echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_widget'] );

				}
				?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render toggle output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_toggle() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'toggle-wrap', 'class', 'ha-offcanvas-toggle-wrap' );

		if ( 'floating' === $settings['toggle_position'] ) {
			$this->add_render_attribute( 'toggle-wrap', 'class', 'ha-floating-element' );
		}

		if ( ! isset( $settings['button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['button_icon'] = '';
		}

		$has_icon = ! empty( $settings['button_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['button_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['select_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_button_icon'] );
		$is_new = ! isset( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( 'button' === $settings['toggle_source'] ) {
			if ( $settings['button_text'] || $has_icon ) { ?>
				<div <?php echo $this->get_render_attribute_string( 'toggle-wrap' ); ?>>
					<div <?php echo $this->get_render_attribute_string( 'toggle-button' ); ?>>
						<?php if ( $has_icon ) { ?>
							<span class="ha-offcanvas-toggle-icon ha-icon ha-no-trans">
								<?php
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
								} elseif ( ! empty( $settings['button_icon'] ) ) {
									?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
								}
								?>
							</span>
						<?php } ?>
						<span class="ha-offcanvas-toggle-text">
							<?php echo $settings['button_text']; ?>
						</span>
					</div>
				</div>
			<?php }
			} elseif ( 'burger' === $settings['toggle_source'] ) { ?>
			<div <?php echo $this->get_render_attribute_string( 'toggle-wrap' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'hamburger' ); ?>>
					<span class="ha-hamburger-box">
						<span class="ha-hamburger-inner"></span>
					</span>
						<?php if ( $settings['burger_label'] ) { ?>
						<span class="ha-hamburger-label">
							<?php echo $settings['burger_label']; ?>
						</span>
					<?php } ?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render sidebar content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_close_button() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' !== $settings['close_button'] ) {
			return;
		}

		if ( ! isset( $settings['close_button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['close_button_icon'] = '';
		}

		$has_icon = ! empty( $settings['close_button_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['close_button_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['select_close_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_close_button_icon'] );
		$is_new = ! isset( $settings['close_button_icon'] ) && Icons_Manager::is_migration_allowed();

		$this->add_render_attribute( 'close-button', 'class',
			[
				'ha-icon',
				'ha-offcanvas-close',
				'ha-offcanvas-close-' . $this->get_id(),
				'ha-flex-inline',
				'ha-flex-y-center'
			]
		);

		$this->add_render_attribute( 'close-button', 'role', 'button' );

		$link = isset($settings['close_bar_link'])? $settings['close_bar_link']['url']:'#';
		$target = isset($settings['close_bar_link']) && $settings['close_bar_link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = isset($settings['close_bar_link']) && $settings['close_bar_link']['nofollow'] ? ' rel="nofollow"' : '';
		$content_type = isset($settings['close_bar_additional'])?$settings['close_bar_additional']:'';

		$logo = isset($settings['close_bar_logo']) && $settings['close_bar_logo']['url'] ? $settings['close_bar_logo']['url'] : '';

		$wrap_class = '';
		if($content_type !== 'none'){
			$wrap_class = ' ha-flex ha-flex-x-between';
		}
		if($settings['close_bar_absolute'] == 'yes'){
			$wrap_class .= ' ha-p-absolute ha-w-100';
		}
		?>

		<div class="ha-offcanvas-header<?php echo $wrap_class; ?>">

			<?php
			if($content_type !== 'none'):
			?>
			<div class="ha-flex-inline ha-flex-y-center ha-offcanvas-close-bar-additional">
				<?php
				if($content_type == 'logo'){
					echo '<a href="',$link,'" class="ha-offcanvas-close-bar-additional-logo" ' . $target . $nofollow . '><img src="',$logo,'"></a>';
				}elseif($content_type == 'button'){
					echo '<a href="',$link,'" class="elementor-button-link elementor-button ha-offcanvas-close-bar-additional-button" role="button" ' . $target . $nofollow . '>',$settings['close_bar_button'],'</a>';
				}elseif($content_type == 'link'){
					echo '<a href="',$link,'" class="ha-offcanvas-close-bar-additional-link" ' . $target . $nofollow . '>',$settings['close_bar_text'],'</a>';
				}
				?>
			</div>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string( 'close-button' ); ?>>
				<?php
				if($settings['select_close_button_icon_position'] == 'after' && $settings['select_close_button_title']){
					echo '<span class="ha-offcanvas-close-bar-close-title ha-offcanvas-close-bar-close-title-after">',$settings['select_close_button_title'].'</span>';
				}
				echo '<span class="ha-offcanvas-close-bar-close-icon">';
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['select_close_button_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['close_button_icon'] ) ) {
					?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
				}
				echo '</span>';
				if($settings['select_close_button_icon_position'] == 'before' && $settings['select_close_button_title']){
					echo '<span class="ha-offcanvas-close-bar-close-title ha-offcanvas-close-bar-close-title-before">',$settings['select_close_button_title'].'</span>';
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render sidebar content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_sidebar() {
		$settings = $this->get_settings_for_display();

		$sidebar = $settings['sidebar'];

		if ( empty( $sidebar ) ) {
			return;
		}

		dynamic_sidebar( $sidebar );
	}

	/**
	 * Render saved template output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_custom_content() {
		$settings = $this->get_settings_for_display();

		foreach ( $settings['custom_content'] as $index => $item ) :
			?>
			<div class="ha-offcanvas-custom-widget">
				<h3 class="ha-offcanvas-widget-title">
					<?php echo $item['title']; ?>
				</h3>
				<div class="ha-offcanvas-widget-content">
					<?php echo $item['description']; ?>
				</div>
			</div>
			<?php
		endforeach;
	}

	/**
	 * Render saved template output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_saved_template() {
		$settings = $this->get_settings_for_display();

		if ( 'section' === $settings['content_type'] && ! empty( $settings['saved_section'] ) ) {

			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_section'] );

		} elseif ( 'template' === $settings['content_type'] && ! empty( $settings['templates'] ) ) {

			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['templates'] );

		} elseif ( 'widget' === $settings['content_type'] && ! empty( $settings['saved_widget'] ) ) {

			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_widget'] );

		}
	}
}
