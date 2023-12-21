<?php
/**
 * shipping bar widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Group_Control_Background;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;

include_once HAPPY_ADDONS_PRO_DIR_PATH . "widgets/shipping-bar/classes/shippingbar-uitls.php";

use Happy_Addons_Pro\Elementor\Widget\Shipping_Bar\Shippingbar_Uitls;

defined( 'ABSPATH' ) || die();

class Shipping_Bar extends Base {

    /**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Shipping Bar', 'happy-addons-pro' );
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
		return 'hm hm-shipping-address';
	}

	public function get_keywords() {
		return [ 'shipping-bar', 'shipping bar', 'free shipping bar', 'shipping progress bar', 'ha shipping bar' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__shipping_bar_content_controls();
		$this->__shipping_bar_settings_controls();
		$this->__shipping_bar_sticky_controls();
	}

	// define shipping bar content controls
	public function __shipping_bar_content_controls(){
		$this->start_controls_section(
			'_section_shipping_bar',
			[
				'label' => __( 'Shipping Bar', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ha_fsb_layout',
			[
				'label' => __( 'Select Layout', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				//'separator' => 'after',
				'options' => [
					'ha-fsb-style1' => 'Layout - 1',
					'ha-fsb-style2' => 'Layout - 2',
					'ha-fsb-style3' => 'Layout - 3',
				],
				'default' => 'ha-fsb-style1',

			]
		);

		$this->add_responsive_control(
			'shipping_bar_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
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
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container .ha-fsb-inner' => 'text-align: {{VALUE}};'
				],
				'description' => __('Adjust Announcement Aligment', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'ha_fsb_announcement',
			[
				'label' => __( 'Announcement', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Free shipping for order above', 'happy-addons-pro' ),
				'placeholder' => __( 'Enter Shipping Bar announcement message for free shipping', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);
		$this->add_control(
			'ha_fsb_target_amount_position',
			[
				'label' => __( 'Target Amount Position', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'before' => __( 'Before', 'happy-addons-pro' ),
					'after' => __( 'After', 'happy-addons-pro' ),
				],
				'description' => __( 'Set where the amount will be displayed', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'ha_fsb_continue_shopping_text',
			[
				'label' => __( 'Link Text', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __('Continue Shopping', 'happy-addons-pro'),
				'placeholder' => __( 'Enter Continue Shopping Text', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);
		$this->add_control(
			'ha_fsb_continue_shopping_link',
			[
				'label' => __( 'Link', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::URL,
				'placeholder' => 'http://your-domain.com',
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->end_controls_section();
	}

	//define shipping bar settings controls
	protected function __shipping_bar_settings_controls() {
		$this->start_controls_section(
			'_section_shipping_bar_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hide_shipping_bar',
			[
				'label' => __( 'Show', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => __( 'Turning this off will allow hide the shipping bar from frontend', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'ha_fsb_shipping_zone',
			[
				'label' => esc_html__( 'Shipping Method', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => 'free_shipping',
				'options' => Shippingbar_Uitls::ha_get_default_shipping_zone(),
			]
		);

		$this->add_control(
			'ha_fsb_progress_type',
			[
				'label' => esc_html__( 'Progress Type', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => 'percent',
				'options' => [
					'figure_amount' => __( 'Figure Amount' ),
					'percent' => __( 'Percentage' )
				],
			]
		);

		$this->add_control(
			'ha_fsb_success_message',
			[
				'label' => __( 'Success Message', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('Congratulations for unlocking free shipping.', 'happy-addons-pro'),
				'placeholder' => __( 'Enter Shipping Bar sucess message for free shipping', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'ha_fsb_animation_speed',
			[
				'label' => __( 'Animation Speed', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'max' => 5000,
				'step' => 100,
				'default' => '1000',
				'description' => __( 'Set animation speed for loading progress. Higher is Slower(Ex:1000ms)', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'shipping_bar_is_dismissable',
			[
				'label' => __( 'Dismissable', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => __( 'Turning this on will allow users to dismiss the shipping bar from frontend', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'shipping_bar_dismissable_icon',
			[
				'label' => __( 'Dismissable Icon', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg','gif'],
				'frontend_available' => true,
				'default' => [
					'value' => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'description' => __( 'Select Dismissable Icon', 'happy-addons-pro' ),
				'condition' => [ 'shipping_bar_is_dismissable' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	//define shipping bar sticky controls
	protected function __shipping_bar_sticky_controls() {
		$this->start_controls_section(
			'_section_shipping_bar_sticky',
			[
				'label' => __( 'Sticky', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'shipping_bar_is_sticky',
			[
				'label' => __( 'Sticky', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'shipping_bar_sticky_type',
			[
				'label' => esc_html__( 'Sticky Type', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'sticky_in_place' => __('Sticky In Place', 'happy-addons-pro'),
					'custom_sticky_position' => __('Custom Position', 'happy-addons-pro'),
				],
				'default' => 'sticky_in_place',
				'condition' => [ 'shipping_bar_is_sticky' => 'yes' ],
			]
		);

		$this->add_control(
            'fsb_horizontal_align',
            [
                'label' => __('Horizontal Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'ha-fsb-left-side' => [
                        'title' => __('Left', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'ha-fsb-middle-side' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'ha-fsb-right-side' => [
                        'title' => __('Right', 'happy-addons-pro'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'ha-fsb-left-side',
                'toggle' => false,
				'condition' => [ 'shipping_bar_is_sticky' => 'yes', 'shipping_bar_sticky_type' => 'custom_sticky_position' ]
            ]
        );

		$this->add_control(
            'fsb_vertical_align',
            [
                'label' => __('Vertical Align', 'happy-addons-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'ha-fsb-position-top' => [
                        'title' => __('Top', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'ha-fsb-position-middle' => [
                        'title' => __('Center', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'ha-fsb-position-bottom' => [
                        'title' => __('Bottom', 'happy-addons-pro'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'ha-fsb-position-top',
                'toggle' => false,
				'condition' => [ 'shipping_bar_is_sticky' => 'yes', 'shipping_bar_sticky_type' => 'custom_sticky_position' ]
            ]
        );

		//for left & top
		$this->add_responsive_control(
			'shipping_bar_horizontal_offset_left_top',
			[
				'label' => esc_html__( 'Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-left-side.ha-fsb-container' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-left-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-top',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_bar_vertical_offset_top_left',
			[
				'label' => esc_html__( 'Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-position-top' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-left-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-top',
				],
			]
		);

		//for middel & top
		$this->add_responsive_control(
			'shipping_bar_vertical_offset_middel_top',
			[
				'label' => esc_html__( 'Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-position-top' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-middle-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-top',
				],
			]
		);

		//for right & top
		$this->add_responsive_control(
			'shipping_bar_vertical_offset_right_top',
			[
				'label' => esc_html__( 'Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-right-side' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-right-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-top',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_bar_horizantal_offset_right_top',
			[
				'label' => esc_html__( 'Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-position-top' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-right-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-top',
				],
			]
		);

		// for left & middle
		$this->add_responsive_control(
			'shipping_bar_horizantal_offset_left_middle',
			[
				'label' => esc_html__( 'Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-left-side' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-left-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-middle',
				],
			]
		);

		// for left & bottom
		$this->add_responsive_control(
			'shipping_bar_horizantal_offset_left_bottom',
			[
				'label' => esc_html__( 'Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-left-side' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-left-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-bottom',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_bar_vartical_offset_left_bottom',
			[
				'label' => esc_html__( 'Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-position-bottom' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-left-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-bottom',
				],
			]
		);

		//for middle & middle
		$this->add_responsive_control(
			'shipping_bar_horizaltal_offset_middle_middle',
			[
				'label' => esc_html__( 'Left/Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-middle-side' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-middle-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-middle',
				],
			]
		);

		// for right & middle
		$this->add_responsive_control(
			'shipping_bar_horizaltal_offset_right_middle',
			[
				'label' => esc_html__( 'Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-right-side' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-right-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-middle',
				],
			]
		);

		// for middle & bottom
		$this->add_responsive_control(
			'shipping_bar_horizaltal_offset_middle_bottom',
			[
				'label' => esc_html__( 'Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-position-bottom' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-middle-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-bottom',
				],
			]
		);

		// for right & bottom
		$this->add_responsive_control(
			'shipping_bar_horizantal_offset_right_bottom',
			[
				'label' => esc_html__( 'Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-right-side' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-right-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-bottom',
				],
			]
		);

		$this->add_responsive_control(
			'shipping_bar_vartical_offset_right_bottom',
			[
				'label' => esc_html__( 'Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-position-bottom' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
					'shipping_bar_sticky_type' 	=> 'custom_sticky_position',
					'fsb_horizontal_align' 		=> 'ha-fsb-right-side',
					'fsb_vertical_align' 		=> 'ha-fsb-position-bottom',
				],
			]
		);

		// for z-index
		$this->add_responsive_control(
			'shipping_bar_sticky_zindex',
			[
				'label' => esc_html__( 'Z-Index', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '999',
				'step' => '1',
				'condition' => [
					'shipping_bar_is_sticky' 	=> 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__shipping_bar_style_controls();
		$this->__progres_bar_style_controls();
		$this->__dismissable_style_controls();
	}

	//define shipping bar style controll
	public function __shipping_bar_style_controls() {
		$this->start_controls_section(
			'_section_fsb_style',
			[
				'label' => __( 'Shipping Bar', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'shipping_bar_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-style1' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-style2' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-container.ha-fsb-style3' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ha_fsb_content_background',
				'selector' => '{{WRAPPER}} .ha-fsb-style1, {{WRAPPER}} .ha-fsb-style2, {{WRAPPER}} .ha-fsb-style3',
				'fields_options' => [
					'background' => [
						'label' => 'Background'
					],
					'color' => [ 'default' => '#0A9663' ]
				],
				'exclude' => [
					'image'
				]
			]
		);

		$this->add_responsive_control(
			'ha_fsb_content_paddong',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1, {{WRAPPER}} .ha-fsb-style2, {{WRAPPER}} .ha-fsb-style3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ha_fsb_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1, {{WRAPPER}} .ha-fsb-style2, {{WRAPPER}} .ha-fsb-style3' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//inner bg for style2 & style3
		$this->add_control(
			'ha_fsb_inner_content_bg',
			[
				'label' => __( 'Inner Content Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style2 .ha-fsb-inner' => 'background: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style3 .ha-fsb-inner' => 'background: {{VALUE}}',
				],
				'condition' => [
					'ha_fsb_layout' => ['ha-fsb-style2', 'ha-fsb-style3']
				],
				'description' => __('Set Shipping bar inner content(Announcement container) background', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'ha_announcement_text_color',
			[
				'label' => __( 'Announcement Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .ha-fsb-inner-content' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style2 .ha-fsb-inner-content' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style3 .ha-fsb-inner-content' => 'color: {{VALUE}}',
				],
				'default' => '#fff585'
			]
		);

		$this->add_control(
			'ha_shopping_link_color',
			[
				'label' => __( 'Link Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style2 a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style3 a' => 'color: {{VALUE}}',
				],
				'default' => '#fff585',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ha_fsb_font_style',
				'label' => __( 'Text Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-fsb-style1 .ha-fsb-inner, {{WRAPPER}} .ha-fsb-style2 .ha-fsb-inner, {{WRAPPER}} .ha-fsb-style3 .ha-fsb-inner',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);


		$this->end_controls_section();
	}

	// define progress bar style control
	public function __progres_bar_style_controls() {
		$this->start_controls_section(
			'_section_psb_style',
			[
				'label' => __( 'Progress Bar', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ha_psb_text_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .ha-fsb-size' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style2 .ha-fsb-size' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style3 .ha-fsb-size' => 'color: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ha_fsb_size_font',
				'selector' => '{{WRAPPER}} .ha-fsb-progress-bar .ha-fsb-size',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ha_psb_background',
				'selector' => '{{WRAPPER}} .ha-fsb-style1 .ha-fsb-bar, .ha-fsb-style2 .ha-fsb-bar, .ha-fsb-style3 .ha-fsb-bar',
				'type' => ['classic', 'gradient'],
				'exclude' => [
					'image'
				],
				'fields_options' => [
					'color' => [ 'default' => '#B9B9B9' ],
				],
			]
		);

		$this->add_responsive_control(
			'ha_psb_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .ha-fsb-bar, {{WRAPPER}} .ha-fsb-style2 .ha-fsb-bar, {{WRAPPER}} .ha-fsb-style3 .ha-fsb-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ha_psb_margin',
			[
				'label' => __( 'Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 500,
					],
					'%' => [
						'min' => -100,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .ha-fsb-bar, {{WRAPPER}} .ha-fsb-style2 .ha-fsb-bar, {{WRAPPER}} .ha-fsb-style3 .ha-fsb-bar' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ha_progress',
			[
				'label' => esc_html__( 'Progress Options', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ha_psb_progress_background',
				'selector' => '{{WRAPPER}} .ha-fsb-style1 .ha-fsb-size, .ha-fsb-style2 .ha-fsb-size, .ha-fsb-style3 .ha-fsb-size',
				'exclude' => [
					'image'
				],
				'fields_options' => [
					'background' => [
						'label' => 'Background'
					],
					'color' => [ 'default' => '#333' ]
				],
			]
		);

		$this->add_responsive_control(
			'ha_pb_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .ha-fsb-size, {{WRAPPER}} .ha-fsb-style2 .ha-fsb-size, {{WRAPPER}} .ha-fsb-style3 .ha-fsb-size' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	//define shipping bar dismissable icon style
	public function __dismissable_style_controls() {
		$this->start_controls_section(
			'_section_dismissable_icon_style',
			[
				'label' => __( 'Dismissable Icon', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ha_icon_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
					],
					'%' => [
						'min' => 2,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .fsb-close-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-style2 .fsb-close-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-style3 .fsb-close-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dismissable_icon_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .fsb-close-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style2 .fsb-close-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-fsb-style3 .fsb-close-icon' => 'color: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);

		$this->add_responsive_control(
			'dismissable_icon_position_x',
			[
				'label' => esc_html__( 'Y Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .fsb-close_button' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-style2 .fsb-close_button' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-style3 .fsb-close_button' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_dismissable' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'dismissable_icon_position_y',
			[
				'label' => esc_html__( 'Y Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'label_block' => true,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
					'%' => [
						'min' => -500,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-fsb-style1 .fsb-close_button' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-style2 .fsb-close_button' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-fsb-style3 .fsb-close_button' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'shipping_bar_is_dismissable' => 'yes'
				],
			]
		);

		$this->end_controls_section();
	}

	public static function show_wc_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'WooCommerce is missing! Please install and activate WooCommerce.', 'happy-addons-pro' )
				);
		}
	}

	//load/render preview
	protected function render() {

		// Show Alart
		if ( ! function_exists( 'WC' ) ) {
			$this->show_wc_missing_alert();
			return;
		}

		$settings = $this->get_settings_for_display();
		extract( $settings );

		if( !ha_elementor()->editor->is_edit_mode() && ( 'yes' !== $hide_shipping_bar ) ) {
			return;
		}

		$wrapper_classes = ' ' . $ha_fsb_layout;
		$wrapper_classes .= ' ' . $fsb_horizontal_align;
		$wrapper_classes .= ' ' . $fsb_vertical_align;
		$wrapper_classes .= ('yes' == $shipping_bar_is_sticky) ? ' ha-fsb-position-fixed' : '';

		$minimum_order_amount = Shippingbar_Uitls::ha_get_minimum_order_amount($ha_fsb_shipping_zone);

		$cartTotal 		= Shippingbar_Uitls::ha_get_cart_subtotal();
		$targetAmount 	= (class_exists( 'WooCommerce' ) ) ? wc_price($minimum_order_amount) : '$' . number_format($minimum_order_amount, 2);
		$announcement 	= ($ha_fsb_target_amount_position == 'after') ? ha_kses_basic( $ha_fsb_announcement ) .' <span class="ha-target-amount">'. $targetAmount . '</span>' : '<span class="ha-target-amount">' . $targetAmount .'</span> ' . ha_kses_basic( $ha_fsb_announcement );

		$link_item_tag = 'a';
		$id = 'ha_fsb_continue_shopping_link';

		$this->add_render_attribute( $id, 'class', 'ha-fsb-inner-shopping' );

		if ( ! empty( $ha_fsb_continue_shopping_link['url'] ) ) {
			$this->add_link_attributes( $id, $ha_fsb_continue_shopping_link );
		}

		$achive_percent = Shippingbar_Uitls::ha_cal_progress( $minimum_order_amount, $cartTotal, $ha_fsb_progress_type );
		$totalPercent = $achive_percent >= 100 ? 100 : $achive_percent;

		$currencySymbol = (class_exists( 'WooCommerce' ) ) ? get_woocommerce_currency_symbol() : '';
		$message 		= Shippingbar_Uitls::ha_get_message($ha_fsb_shipping_zone, $minimum_order_amount, $cartTotal, $announcement, $ha_fsb_success_message);

		$data = [
			'progress_type' => $ha_fsb_progress_type,
			'currencySymbol' => $currencySymbol,
			'target_amount' => $minimum_order_amount,
			'achive_percent' => $achive_percent,
			'cart_total' => $cartTotal,
			'ha_fsb_animation_speed' => $ha_fsb_animation_speed,
			'announcement' => $announcement,
			'fsb_success_message' => $ha_fsb_success_message,
			'totalPercent' => $totalPercent,
		];

		$wrapperStyle = ( ('ha-fsb-middle-side' == $fsb_horizontal_align) && ('ha-fsb-position-middle' == $fsb_vertical_align) ) ? "transform: translate(-50%, -50%);z-index:".$shipping_bar_sticky_zindex.';' : 'z-index:'.$shipping_bar_sticky_zindex.';';

		if( !empty( $ha_fsb_shipping_zone ) && $minimum_order_amount > 0 ) {
	?>

		<div id="ha_fsb_container" class="ha-fsb-container <?php echo esc_attr( $wrapper_classes ); ?>"
			data-fsb_settings='<?php echo json_encode( $data, true );?>' style="<?php echo esc_attr($wrapperStyle); ?>">
			<div class="ha-fsb-inner">
				<span class="ha-fsb-inner-content"><?php echo $message; ?></span>
				<?php if('yes' === $shipping_bar_is_dismissable){ ?>
					<div class="fsb-close_button">
						<span class="fsb-close-icon" data-user_ip="<?php echo gethostbyaddr($_SERVER["REMOTE_ADDR"]);?>">
							<?php Icons_Manager::render_icon( $settings['shipping_bar_dismissable_icon'], ['aria-hidden' => 'true', 'class' => 'ha-fsb-icon-cancel'] ); ?>
						</span>
					</div>
				<?php }?>


				<<?php echo $link_item_tag; ?> <?php $this->print_render_attribute_string( $id ); ?>>
					<?php echo $ha_fsb_continue_shopping_text; ?>
				</<?php echo $link_item_tag; ?>>
			</div>
			<div class="ha-fsb-progress-bar ha-fsb-bar">
				<div class="ha-fsb-size" style="width: <?php ($totalPercent > 0) ? $totalPercent : '5%'; ?>;">
					0<?php echo ($ha_fsb_progress_type == 'figure_amount') ? $currencySymbol : '%';?> </div>
			</div>
		</div>

<?php } else {

		if( current_user_can ('manage_options') ) { ?>

			<div id="ha_fsb_container" class="ha-fsb-container <?php echo esc_attr( $wrapper_classes ); ?>"
				style="<?php echo esc_attr($wrapperStyle); ?>">
				<div class="ha-fsb-inner">
					<span class="ha-fsb-inner-content ha-fsb-message-disabled">No shipping zone selected, You must set your free shipping zone from WooCommerce Settings->Shipping Zone, otherwise click</span>
					<a class="ha-fsb-warning" href="<?php echo admin_url('admin.php?page=wc-settings&tab=shipping'); ?>" target="_blank">Here</a>.
				</div>
			</div>

<?php 		}
		}
	}

}
