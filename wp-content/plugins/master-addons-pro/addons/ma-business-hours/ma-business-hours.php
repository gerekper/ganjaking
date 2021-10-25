<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Business Hours Widget
 */
class Business_Hours extends Widget_Base
{

	public function get_name()
	{
		return 'ma-business-hours';
	}
	public function get_title()
	{
		return esc_html__('Business Hours', MELA_TD);
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-clock-o';
	}

	public function get_keywords()
	{
		return ['office', 'business', 'hours', 'time', 'duty', 'schedule', 'clock', 'alarm'];
	}

	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/business-hours/';
	}


	protected function _register_controls()
	{

		/*
    	 * Content Tab: Business Hours
    	 */
		$this->start_controls_section(
			'ma_el_business_hours_schedule_section',
			[
				'label'             => __('MA Business Hours', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_business_hours_style',
			[
				'label'                => __('Design Preset', MELA_TD),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'style-default'                 => __('Default', MELA_TD),
					'solid-bg-color'                => __('Solid Background', MELA_TD),
					'content-bg-image'              => __('Short Details', MELA_TD),
					'content-corner-btn'            => __('Booking Reservation One', MELA_TD),
					'table-reservation'             => __('Booking Reservation Two', MELA_TD)
				],
				'default'              => 'style-default',
				'frontend_available'   => true,
			]
		);

		$this->add_control(
			'ma_el_business_timings',
			[
				'label'                => __('Business Timings', MELA_TD),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'default'               => __('Default', MELA_TD),
					'custom'                => __('Custom Table', MELA_TD)
				],
				'default'              => 'default',
				'frontend_available'   => true,
			]
		);



		$this->add_control(
			'ma_el_bh_show_title',
			[
				'label'             => __('Show Title?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'yes',
			]
		);

		$this->add_control(
			'ma_el_bh_table_title',
			[
				'label'             => __('Table Title', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'placeholder'       => __('Opening hours', MELA_TD),
				'default'           => __('Opening hours', MELA_TD),
				'label_block'       => true,
				'condition'        => [
					'ma_el_bh_show_title'           => 'yes'
				],
			]
		);

		$this->add_control(
			'ma_el_bh_show_subtitle',
			[
				'label'             => __('Show Sub Title?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'yes',
				'condition'         => [
					'ma_el_business_hours_style'    =>  ['content-bg-image', 'content-corner-btn', 'table-reservation']
				]
			]
		);


		$this->add_control(
			'ma_el_bh_table_subtitle',
			[
				'label'             => __('Sub Title', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'placeholder'       => __('Book your Table', MELA_TD),
				'default'           => __('Book your Table', MELA_TD),
				'label_block'       => true,
				'condition'        => [
					'ma_el_bh_show_subtitle'           => 'yes'
				],
			]
		);

		$this->add_control(
			'ma_el_business_bg_image',
			[
				'label' => __('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'        => [
					'ma_el_business_hours_style'   =>  ['content-bg-image', 'content-corner-btn', 'table-reservation']
				],

			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'ma_el_business_bg_img',
				'default' => 'full',
				'exclude'    => array('custom'),
				'condition' => [
					'ma_el_business_bg_image[url]!' => '',
					'ma_el_business_hours_style'   =>  ['content-bg-image', 'content-corner-btn', 'table-reservation']
				],
			]
		);


		$this->add_control(
			'ma_el_bh_show_day_icon',
			[
				'label'             => __('Show Day Icon?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value' => 'yes'
			]
		);


		$this->add_control(
			'ma_el_bh_day_icon',
			[
				'label'         	=> esc_html__('Day Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'far fa-clock',
					'library'   => 'regular',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_bh_show_day_icon'   =>  'yes'
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'ma_el_bh_day',
			[
				'label'             => __('Day', MELA_TD),
				'type'              => Controls_Manager::SELECT,
				'default'           => 'Sunday',
				'options'           => [
					'Sunday'    => __('Sunday', MELA_TD),
					'Monday'    => __('Monday', MELA_TD),
					'Tuesday'   => __('Tuesday', MELA_TD),
					'Wednesday' => __('Wednesday', MELA_TD),
					'Thursday'  => __('Thursday', MELA_TD),
					'Friday'    => __('Friday', MELA_TD),
					'Saturday'  => __('Saturday', MELA_TD),
				],
			]
		);

		$repeater->add_control(
			'ma_el_bh_closed',
			[
				'label'             => __('Closed?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'no',
			]
		);

		$repeater->add_control(
			'ma_el_opening_hours',
			[
				'label'             => __('Opening Hours', MELA_TD),
				'type'              => Controls_Manager::SELECT,
				'default'           => '08:30',
				'options'           => [
					'00:00' => '12:00 AM',
					'00:30' => '12:30 AM',
					'01:00' => '1:00 AM',
					'01:30' => '1:30 AM',
					'02:00' => '2:00 AM',
					'02:30' => '2:30 AM',
					'03:00' => '3:00 AM',
					'03:30' => '3:30 AM',
					'04:00' => '4:00 AM',
					'04:30' => '4:30 AM',
					'05:00' => '5:00 AM',
					'05:30' => '5:30 AM',
					'06:00' => '6:00 AM',
					'06:30' => '6:30 AM',
					'07:00' => '7:00 AM',
					'07:30' => '7:30 AM',
					'08:00' => '8:00 AM',
					'08:30' => '8:30 AM',
					'09:00' => '9:00 AM',
					'09:30' => '9:30 AM',
					'10:00' => '10:00 AM',
					'10:30' => '10:30 AM',
					'11:00' => '11:00 AM',
					'11:30' => '11:30 AM',
					'12:00' => '12:00 PM',
					'12:30' => '12:30 PM',
					'13:00' => '1:00 PM',
					'13:30' => '1:30 PM',
					'14:00' => '2:00 PM',
					'14:30' => '2:30 PM',
					'15:00' => '3:00 PM',
					'15:30' => '3:30 PM',
					'16:00' => '4:00 PM',
					'16:30' => '4:30 PM',
					'17:00' => '5:00 PM',
					'17:30' => '5:30 PM',
					'18:00' => '6:00 PM',
					'18:30' => '6:30 PM',
					'19:00' => '7:00 PM',
					'19:30' => '7:30 PM',
					'20:00' => '8:00 PM',
					'20:30' => '8:30 PM',
					'21:00' => '9:00 PM',
					'21:30' => '9:30 PM',
					'22:00' => '10:00 PM',
					'22:30' => '10:30 PM',
					'23:00' => '11:00 PM',
					'23:30' => '11:30 PM',
					'24:00' => '12:00 PM',
					'24:30' => '12:30 PM',
				],
				'condition'         => [
					'ma_el_bh_closed' => 'no',
				],
			]
		);

		$repeater->add_control(
			'ma_el_closing_hours',
			[
				'label'             => __('Closing Hours', MELA_TD),
				'type'              => Controls_Manager::SELECT,
				'default'           => '08:30',
				'options'           => [
					'00:00' => '12:00 AM',
					'00:30' => '12:30 AM',
					'01:00' => '1:00 AM',
					'01:30' => '1:30 AM',
					'02:00' => '2:00 AM',
					'02:30' => '2:30 AM',
					'03:00' => '3:00 AM',
					'03:30' => '3:30 AM',
					'04:00' => '4:00 AM',
					'04:30' => '4:30 AM',
					'05:00' => '5:00 AM',
					'05:30' => '5:30 AM',
					'06:00' => '6:00 AM',
					'06:30' => '6:30 AM',
					'07:00' => '7:00 AM',
					'07:30' => '7:30 AM',
					'08:00' => '8:00 AM',
					'08:30' => '8:30 AM',
					'09:00' => '9:00 AM',
					'09:30' => '9:30 AM',
					'10:00' => '10:00 AM',
					'10:30' => '10:30 AM',
					'11:00' => '11:00 AM',
					'11:30' => '11:30 AM',
					'12:00' => '12:00 PM',
					'12:30' => '12:30 PM',
					'13:00' => '1:00 PM',
					'13:30' => '1:30 PM',
					'14:00' => '2:00 PM',
					'14:30' => '2:30 PM',
					'15:00' => '3:00 PM',
					'15:30' => '3:30 PM',
					'16:00' => '4:00 PM',
					'16:30' => '4:30 PM',
					'17:00' => '5:00 PM',
					'17:30' => '5:30 PM',
					'18:00' => '6:00 PM',
					'18:30' => '6:30 PM',
					'19:00' => '7:00 PM',
					'19:30' => '7:30 PM',
					'20:00' => '8:00 PM',
					'20:30' => '8:30 PM',
					'21:00' => '9:00 PM',
					'21:30' => '9:30 PM',
					'22:00' => '10:00 PM',
					'22:30' => '10:30 PM',
					'23:00' => '11:00 PM',
					'23:30' => '11:30 PM',
					'24:00' => '12:00 PM',
					'24:30' => '12:30 PM',
				],
				'condition'         => [
					'ma_el_bh_closed' => 'no',
				],
			]
		);

		$repeater->add_control(
			'ma_el_bh_closed_text',
			[
				'label'             => esc_html__('Closed Text', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'placeholder'       => __('Closed', MELA_TD),
				'default'           => __('Closed', MELA_TD),
				'label_block'       => true,
				'condition'        => [
					'ma_el_bh_closed'   =>  'no'
				],
			]
		);

		$repeater->add_control(
			'ma_el_highlight_this',
			[
				'label'             => __('Highlight', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'yes'
			]
		);


		$repeater->add_control(
			'ma_el_highlighted_text_color',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour li{{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				],
				'condition'         => [
					'ma_el_highlight_this' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'ma_el_highlighted_bg_color',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour li{{CURRENT_ITEM}}' => 'background: {{VALUE}};'
				],
				'condition'         => [
					'ma_el_highlight_this' => 'yes',
				],
			]
		);

		$this->add_control(
			'ma_el_business_hours',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'ma_el_bh_day'      => esc_html__('Sunday', MELA_TD),
						'ma_el_bh_closed'   => 'yes',
						'ma_el_highlight_this' => 'yes',
						'ma_el_highlighted_text_color' => '#4b00e7',
					],
					[
						'ma_el_bh_day'  => esc_html__('Monday', MELA_TD),
					],
					[
						'ma_el_bh_day'  => esc_html__('Tuesday', MELA_TD),
					],
					[
						'ma_el_bh_day'  => esc_html__('Wednesday', MELA_TD),
					],
					[
						'ma_el_bh_day'  => esc_html__('Thursday', MELA_TD),
					],
					[
						'ma_el_bh_day'  => esc_html__('Friday', MELA_TD),
					],
					[
						'ma_el_bh_day'      => esc_html__('Saturday', MELA_TD),
					],
				],
				'fields' 				=> $repeater->get_controls(),
				'title_field'           => '{{{ ma_el_bh_day }}}',
				'condition'         => [
					'ma_el_business_timings' => 'default',
				],
			]
		);



		/*
	     * Custom Business Hour Starts
	     */

		$repeater = new Repeater();


		$repeater->add_control(
			'ma_el_bh_custom_day',
			[
				'label'             => __('Day', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'default'           => 'Sunday'
			]
		);

		$repeater->add_control(
			'ma_el_bh_custom_closed',
			[
				'label'             => __('Closed?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'no',
			]
		);

		$repeater->add_control(
			'ma_el_bh_custom_time',
			[
				'label'             => __('Custom Time', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'default'           => '10:00 AM - 06:00 PM',
				'condition'         => [
					'ma_el_bh_custom_closed' => 'no',
				],
			]
		);


		$repeater->add_control(
			'ma_el_bh_custom_closed_text',
			[
				'label'             => esc_html__('Closed Text', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'placeholder'       => __('Closed', MELA_TD),
				'default'           => __('Closed', MELA_TD),
				'label_block'       => true,
				'condition'        => [
					'ma_el_bh_custom_closed'   =>  'no'
				],
			]
		);

		$repeater->add_control(
			'ma_el_custom_highlight_this',
			[
				'label'             => __('Highlight', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'yes'
			]
		);


		$repeater->add_control(
			'ma_el_custom_highlighted_text_color',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					//				    '{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item{{CURRENT_ITEM}}' => 'background-color:
					// {{VALUE}}',
				],
				'condition'         => [
					'ma_el_custom_highlight_this' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'ma_el_custom_highlighted_bg_color',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					//				    '{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item{{CURRENT_ITEM}}' => 'background-color:
					// {{VALUE}}',
				],
				'condition'         => [
					'ma_el_custom_highlight_this' => 'yes',
				],
			]
		);

		$this->add_control(
			'ma_el_business_custom_hours',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'ma_el_bh_custom_day'      => __('Sunday', MELA_TD),
						'ma_el_bh_custom_closed'   => 'yes',
						'ma_el_custom_highlight_this' => 'yes',
						//					    'ma_el_highlighted_text_color' => '#4b00e7',
					],
					[
						'ma_el_bh_custom_day'  => __('Monday', MELA_TD),
					],
					[
						'ma_el_bh_custom_day'  => __('Tuesday', MELA_TD),
					],
					[
						'ma_el_bh_custom_day'  => __('Wednesday', MELA_TD),
					],
					[
						'ma_el_bh_custom_day'  => __('Thursday', MELA_TD),
					],
					[
						'ma_el_bh_custom_day'  => __('Friday', MELA_TD),
					],
					[
						'ma_el_bh_custom_day'  => __('Saturday', MELA_TD),
					],
				],
				'fields' 				=> $repeater->get_controls(),
				//			    'title_field'           => '{{tab_title}}',
				'title_field'           => '{{{ ma_el_bh_custom_day }}}',
				'condition'         => [
					'ma_el_business_timings' => 'custom',
				],
			]
		);

		$this->add_control(
			'ma_el_bh_hours_format',
			[
				'label'             => __('24 Hours Format?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_on'          => __('Yes', MELA_TD),
				'label_off'         => __('No', MELA_TD),
				'return_value'      => 'yes',
				'condition'         => [
					'ma_el_business_timings' => 'default',
				],
			]
		);

		$this->add_control(
			'ma_el_days_format',
			[
				'label'                => __('Days Format', MELA_TD),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'full',
				'options'              => [
					'full'      => __('Full', MELA_TD),
					'short'     => __('Short', MELA_TD),
				],
				'condition'         => [
					'ma_el_business_timings' => 'default',
				],
			]
		);



		$this->add_control(
			'ma_el_bh_show_button',
			[
				'label'             => __('Show Button?', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_off'         => __('Yes', MELA_TD),
				'label_on'          => __('No', MELA_TD),
				'return_value'      => 'yes',
				//			    'condition'         => [
				//				    'ma_el_business_hours_style!' => ['content-corner-btn', 'table-reservation']
				//			    ],
			]
		);



		$this->end_controls_section();



		/*
	     * Button Reservation Details
	     */
		$this->start_controls_section(
			'ma_el_bf_button_section',
			[
				'label'             => __('Button Details', MELA_TD),
				'condition'        => [
					'ma_el_bh_show_button'          =>  'yes'
				],
			]
		);


		$this->add_control(
			'ma_el_bh_table_btn_text',
			[
				'label'             => esc_html__('Button Text', MELA_TD),
				'type'              => Controls_Manager::TEXT,
				'placeholder'       => __('Book Now', MELA_TD),
				'default'           => __('Book Now', MELA_TD),
				'label_block'       => true
			]
		);

		$this->add_control(
			'ma_el_bh_table_btn_link',
			[
				'label'       => esc_html__('Link URL', MELA_TD),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url'         => '#',
					'is_external' => '',
				],
				'show_external' => true,
			]
		);

		$this->add_responsive_control(
			'button_alignment',
			[
				'label'                 => __('Alignment', MELA_TD),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center'    => [
						'title' => __('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right'     => [
						'title' => __('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-content-bottom' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


		/**
		 * Style Tab: Row Style
		 */

		$this->start_controls_section(
			'ma_el_section_rows_style',
			[
				'label'             => __('Rows Style', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_rows_style');

		$this->start_controls_tab(
			'ma_el_tab_row_normal',
			[
				'label'                 => __('Normal', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_row_bg_color_normal',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-business-hour li' => 'background: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_tab_row_hover',
			[
				'label'                 => __('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_row_bg_color_hover',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ma-el-business-hour li:hover' => 'background: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_control(
			'stripes',
			[
				'label'             => __('Striped Rows', MELA_TD),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'no',
				'label_on'          => __('Yes', MELA_TD),
				'label_off'         => __('No', MELA_TD),
				'return_value'      => 'yes',
				'separator'         => 'before',
			]
		);

		$this->start_controls_tabs('tabs_alternate_style');

		$this->start_controls_tab(
			'tab_even',
			[
				'label'                 => __('Even Row', MELA_TD),
				'condition'             => [
					'stripes' => 'yes',
				],
			]
		);

		$this->add_control(
			'row_even_bg_color',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '#f5f5f5',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:nth-child(even)' => 'background-color: {{VALUE}}',
				],
				'condition'         => [
					'stripes' => 'yes',
				],
			]
		);

		$this->add_control(
			'row_even_text_color',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:nth-child(even)' => 'color: {{VALUE}}',
				],
				'condition'         => [
					'stripes' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_odd',
			[
				'label'                 => __('Odd Row', MELA_TD),
				'condition'             => [
					'stripes' => 'yes',
				],
			]
		);

		$this->add_control(
			'row_odd_bg_color',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '#ffffff',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:nth-child(odd)' => 'background-color: {{VALUE}}',
				],
				'condition'         => [
					'stripes' => 'yes',
				],
			]
		);

		$this->add_control(
			'row_odd_text_color',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:nth-child(odd)' => 'color: {{VALUE}}',
				],
				'condition'         => [
					'stripes' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();



		$this->add_responsive_control(
			'rows_padding',
			[
				'label'             => __('Padding', MELA_TD),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', '%'],
				'default'           => [
					'top'       => '8',
					'right'     => '10',
					'bottom'    => '8',
					'left'      => '10',
					'unit'      => 'px',
					'isLinked'  => false,
				],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .ma-el-business-hour'
			]
		);


		$this->add_responsive_control(
			'rows_margin',
			[
				'label'             => __('Margin Bottom', MELA_TD),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'        => ['px'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'closed_row_heading',
			[
				'label'             => __('Closed Row', MELA_TD),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'closed_row_bg_color',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item.ma-el-bh-closed' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'closed_row_day_color',
			[
				'label'             => __('Day Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item.ma-el-bh-closed .ma-el-business-day-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'closed_row_tex_color',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item.ma-el-bh-closed .closed' =>
					'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_heading',
			[
				'label'             => __('Rows Divider', MELA_TD),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __('Divider', MELA_TD),
				'selector' => '{{WRAPPER}} .ma-el-business-hour li:before',
			]
		);


		$this->end_controls_section();

		/**
		 * Style Tab: Business Hours
		 */
		$this->start_controls_section(
			'ma_el_business_hours_section_style',
			[
				'label'             => __('Business Hours', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_hours_style');

		$this->start_controls_tab(
			'tab_hours_normal',
			[
				'label'                 => __('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'             => __('Day', MELA_TD),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
			]
		);


		$this->add_control(
			'day_color',
			[
				'label'             => __('Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-day-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'title_typography',
				'label'             => __('Typography', MELA_TD),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .ma-el-business-hour .ma-el-business-day-name',
			]
		);

		$this->add_control(
			'hours_heading',
			[
				'label'             => __('Hours', MELA_TD),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'hours_color',
			[
				'label'             => __('Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-duration' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'hours_typography',
				'label'             => __('Typography', MELA_TD),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .ma-el-business-hour .ma-el-business-duration',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hours_hover',
			[
				'label'                 => __('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'day_color_hover',
			[
				'label'             => __('Day Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:hover .ma-el-business-day-name' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hours_color_hover',
			[
				'label'             => __('Hours Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-item:hover .ma-el-business-duration' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		/*
	     * Button Style
	     */
		$this->start_controls_section(
			'ma_el_business_hours_button_style',
			[
				'label'             => __('Button Style', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'        => [
					'ma_el_bh_show_button'          =>  'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __('Background', MELA_TD),
				'types' => ['classic', 'gradient'],
				'default'	=> 'gradient',
				'selector' => '{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-btn'
			]
		);

		$this->add_control(
			'ma_el_business_hours_button_color',
			[
				'label'             => __('Button Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-business-hour .ma-el-business-hour-btn' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ma-el-business-hour.table-reservation .ma-el-business-hour-btn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();



		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/business-hours/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/business-hours-elementor/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=x0_HY9uYgog" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		//Upgrade to Pro
		
	}


	public function ma_el_business_hours()
	{
		$settings = $this->get_settings();
		$i = 1; ?>

		<?php if ($settings['ma_el_bh_show_title'] == 'yes') { ?>
			<?php if ($settings['ma_el_business_hours_style'] == 'table-reservation') { ?>
				<h2 class="ma-el-business-reservation-title">
				<?php } else { ?>
					<h2 class="ma-el-business-hour-title">
					<?php } ?>

					<?php echo esc_attr($settings['ma_el_bh_table_title']); ?>
					</h2>
				<?php } ?>


				<?php if ($settings['ma_el_bh_show_subtitle'] == 'yes') { ?>
					<?php if ($settings['ma_el_business_hours_style'] == 'table-reservation') { ?>
						<h2 class="ma-el-business-hour-title">
							<?php echo esc_attr($settings['ma_el_bh_table_subtitle']); ?>
						</h2>
					<?php } ?>
				<?php } ?>



				<?php foreach ($settings['ma_el_business_hours'] as $index => $item) : ?>
					<?php
					$this->add_render_attribute(
						'jltma-row' . $i,
						'class',
						'ma-el-business-hour-item clearfix elementor-repeater-item-' . esc_attr($item['_id'])
					);
					if ($item['ma_el_bh_closed'] != 'no') {
						$this->add_render_attribute('jltma-row' . $i, 'class', 'ma-el-bh-closed');
					}
					?>
					<li <?php echo $this->get_render_attribute_string('jltma-row' . $i); ?>>
						<span class="ma-el-business-day-name float-left">
							<?php if ($settings['ma_el_bh_show_day_icon'] == "yes") {
								Master_Addons_Helper::jltma_fa_icon_picker('far fa-clock', 'icon', $settings['ma_el_bh_day_icon'], 'ma_el_bh_day_icon');
							} ?>

							<?php
							if ($settings['ma_el_days_format'] == 'full') {
								echo ucwords(esc_attr($item['ma_el_bh_day']));
							} else {
								echo ucwords(esc_attr(substr($item['ma_el_bh_day'], 0, 3)));
							}
							?>
						</span>


						<span class="ma-el-business-duration float-right">
							<?php if ($item['ma_el_bh_closed'] == 'no') { ?>
								<time class="ma-el-opening-hours">
									<?php
									if ($settings['ma_el_bh_hours_format'] == 'yes') {
										echo esc_attr($item['ma_el_opening_hours']);
									} else {
										echo esc_attr(date("g:i A", strtotime($item['ma_el_opening_hours'])));
									}
									?>
								</time>
								<time class="ma-el-closing-hours">
									<?php
									if ($settings['ma_el_bh_hours_format'] == 'yes') {
										echo esc_attr($item['ma_el_closing_hours']);
									} else {
										echo esc_attr(date("g:i A", strtotime($item['ma_el_closing_hours'])));
									}
									?>
								</time>
							<?php
							} else {
								if ($item['ma_el_bh_closed_text']) {
									echo '<span class="closed">' . esc_attr($item['ma_el_bh_closed_text']) . '</span>';
								} else {
									echo '<span class="closed">' . esc_attr_e('Closed', MELA_TD) . '</span>';
								}
							} ?>
						</span>
					</li>
				<?php $i++;
				endforeach; ?>
				<?php
			}

			public function ma_el_minimal_business_hours()
			{

				$settings = $this->get_settings();

				foreach ($settings['ma_el_business_hours'] as $index => $item) :

					$this->add_render_attribute('jltma-row', 'class', 'ma-el-business-hour-item clearfix elementor-repeater-item-' . esc_attr($item['_id']));
					//		    if ( $item['ma_el_bh_closed'] != 'no' ) {
					//			    $this->add_render_attribute( 'jltma-row', 'class', 'ma-el-bh-closed' );
					//		    }
				?>


					<li <?php echo $this->get_render_attribute_string('jltma-row' . $i); ?>>
						<span class="ma-el-business-day-name float-left">
							<?php if ($settings['ma_el_bh_show_day_icon'] == "yes") {
								// echo '<i class="' . $settings['ma_el_bh_day_icon'] .'"></i>';
								Master_Addons_Helper::jltma_fa_icon_picker('far fa-clock', 'icon', $settings['ma_el_bh_day_icon'], 'ma_el_bh_day_icon');
							} ?>

							<?php
							if ($settings['ma_el_days_format'] == 'full') {
								echo ucwords(esc_attr($item['ma_el_bh_day']));
							} else {
								echo ucwords(esc_attr(substr($item['ma_el_bh_day'], 0, 3)));
							}
							?>
						</span>


						<span class="ma-el-business-duration float-right">
							<?php if ($item['ma_el_bh_closed'] == 'no') { ?>
								<time class="ma-el-opening-hours">
									<?php
									if ($settings['ma_el_bh_hours_format'] == 'yes') {
										echo esc_attr($item['ma_el_opening_hours']);
									} else {
										echo esc_attr(date("g:i A", strtotime($item['ma_el_opening_hours'])));
									}
									?>
								</time>
								<time class="ma-el-closing-hours">
									<?php
									if ($settings['ma_el_bh_hours_format'] == 'yes') {
										echo esc_attr($item['ma_el_closing_hours']);
									} else {
										echo esc_attr(date("g:i A", strtotime($item['ma_el_closing_hours'])));
									}
									?>
								</time>
							<?php
							} else {
								if ($item['ma_el_bh_closed_text']) {
									echo '<span class="closed">' . esc_attr($item['ma_el_bh_closed_text']) . '</span>';
								} else {
									echo '<span class="closed">' . esc_attr_e('Closed', MELA_TD) . '</span>';
								}
							} ?>
						</span>
					</li>
					<?php $i++;
				endforeach;
			}


			private function render_image($image_id, $settings)
			{
				$ma_el_image_gallery_image = $settings['ma_el_business_bg_img_size'];

				if ('custom' === $ma_el_image_gallery_image) {
					$image_src = Group_Control_Image_Size::get_attachment_image_src($image_id, 'ma_el_business_bg_image', $settings);
				} else {
					$image_src = wp_get_attachment_image_src($image_id, $ma_el_image_gallery_image);
					$image_src = $image_src[0];
				}

				return sprintf('<img src="%s" alt="%s" />', esc_url($image_src), esc_html(get_post_meta($image_id, '_wp_attachment_image_alt', true)));
			}


			public function ma_el_custom_business_hours()
			{
				$settings = $this->get_settings();
				$i = 1;

				if ($settings['ma_el_bh_show_title'] == "yes") {
					if ($settings['ma_el_business_hours_style'] == 'table-reservation') { ?>
						<h2 class="ma-el-business-reservation-title">
						<?php } else { ?>
							<h2 class="ma-el-business-hour-title">
							<?php }
						echo esc_attr($settings['ma_el_bh_table_title']); ?>
							</h2>
						<?php } ?>


						<?php if ($settings['ma_el_bh_show_subtitle'] == 'yes') { ?>
							<?php if ($settings['ma_el_business_hours_style'] == 'table-reservation') { ?>
								<h2 class="ma-el-business-hour-title">
									<?php echo esc_attr($settings['ma_el_bh_table_subtitle']); ?>
								</h2>
							<?php } ?>
						<?php } ?>


						<?php foreach ($settings['ma_el_business_custom_hours'] as $index => $item) : ?>
							<?php
							$this->add_render_attribute('jltma-row' . $i, 'class', 'ma-el-business-hour-item clearfix elementor-repeater-item-' . esc_attr($item['_id']));
							if ($item['ma_el_bh_custom_closed'] != 'no') {
								$this->add_render_attribute('jltma-row' . $i, 'class', 'ma-el-bh-closed');
							}
							?>
							<li <?php echo $this->get_render_attribute_string('jltma-row' . $i); ?>>

								<?php if ($item['ma_el_bh_custom_day'] != '') { ?>
									<span class="ma-el-business-day-name float-left">
										<?php
										if ($settings['ma_el_bh_show_day_icon'] == "yes") {
											// echo '<i class="' . $settings['ma_el_bh_day_icon'] .'"></i>';
											Master_Addons_Helper::jltma_fa_icon_picker('far fa-clock', 'icon', $settings['ma_el_bh_day_icon'], 'ma_el_bh_day_icon');
										} ?>
										<?php
										echo esc_attr($item['ma_el_bh_custom_day']);
										?>
									</span>
								<?php } ?>

								<span class="ma-el-business-duration float-right">
									<?php
									if ($item['ma_el_bh_custom_closed'] == 'no' && $item['ma_el_bh_custom_time'] != '') {
										echo esc_attr($item['ma_el_bh_custom_time']);
									} else {
										if ($item['ma_el_bh_custom_closed_text']) {
											echo '<span class="closed">' . esc_attr($item['ma_el_bh_custom_closed_text']) .
												'</span>';
										} else {
											echo '<span class="closed">' . esc_attr_e('Closed', MELA_TD) . '</span>';
										}
									}
									?>
								</span>
							</li>
						<?php $i++;
						endforeach; ?>
					<?php
				}


				protected function render()
				{
					$settings = $this->get_settings();
					$this->add_render_attribute(
						'ma_el_business_hours',
						'class',
						[
							'ma-el-business-hour',
							$settings['ma_el_business_hours_style']
						]
					);

					$ma_el_business_bg_image = $this->get_settings_for_display('ma_el_business_bg_image');

					if (!empty($ma_el_business_bg_image)) {
						$ma_el_business_bg_image_url_src = Group_Control_Image_Size::get_attachment_image_src(
							$ma_el_business_bg_image['id'],
							'ma_el_business_bg_img',
							$settings
						);
					}

					// Booking Button
					$this->add_render_attribute('ma_el_bh_btn_link', [
						'class'	=> ['ma-el-business-hour-btn', 'float-right'],
						'href'	=> esc_url($settings['ma_el_bh_table_btn_link']['url']),
					]);

					$this->add_render_attribute('ma_el_bh_normal_btn', [
						'class'	=> ['ma-el-business-hour-btn'],
						'href'	=> esc_url($settings['ma_el_bh_table_btn_link']['url']),
					]);

					if ($settings['ma_el_bh_table_btn_link']['is_external']) {
						$this->add_render_attribute('ma_el_bh_btn_link', 'target', '_blank');
					}

					if ($settings['ma_el_bh_table_btn_link']['nofollow']) {
						$this->add_render_attribute('ma_el_bh_btn_link', 'rel', 'nofollow');
					}

					?>
						<div <?php echo $this->get_render_attribute_string('ma_el_business_hours'); ?>>

							<?php if (($settings['ma_el_business_hours_style'] == 'content-corner-btn') || ($settings['ma_el_business_hours_style'] == 'table-reservation')) { ?>
								<div class="jltma-row">
									<div class="<?php echo ($settings['ma_el_business_hours_style'] == 'table-reservation') ? "jltma-col-lg-8" : "jltma-col-lg-6"; ?>">
										<?php echo $this->render_image($settings['ma_el_business_bg_image']['id'], $settings); ?>

									</div>
								<?php } ?>

								<?php if (($settings['ma_el_business_hours_style'] == 'content-corner-btn') || ($settings['ma_el_business_hours_style'] == 'table-reservation')) { ?>
									<div class="<?php echo ($settings['ma_el_business_hours_style'] == 'table-reservation') ? "jltma-col-lg-4" : "jltma-col-lg-6"; ?>">
									<?php } ?>

									<div class="ma-el-business-hour-content" <?php if ($settings['ma_el_business_hours_style'] == 'content-bg-image') {
																					echo 'style="background: url(' . ($ma_el_business_bg_image_url_src) ? $ma_el_business_bg_image_url_src : '' . ') no-repeat center; background-size: cover;';
																				} ?>>
										<div class="ma-el-business-hour-content-details">
											<ul class="ma-el-business-hour-list">
												<?php
												if ($settings['ma_el_business_timings'] == 'default') {
													$this->ma_el_business_hours();
												} elseif ($settings['ma_el_business_timings'] == 'custom') {
													$this->ma_el_custom_business_hours();
												} elseif ($settings['ma_el_business_hours_style'] == 'content-bg-image') {
													$this->ma_el_minimal_business_hours();
												}
												?>
											</ul>
										</div>

										<?php if ($settings['ma_el_bh_show_button'] == 'yes' && $settings['ma_el_business_hours_style'] == 'content-corner-btn') { ?>
											<div class="ma-el-business-hour-content-bottom">

												<?php if ($settings['ma_el_bh_show_subtitle'] == 'yes') { ?>
													<span class="float-left">
														<?php echo esc_attr($settings['ma_el_bh_table_subtitle']); ?>
													</span>
												<?php } ?>

												<a <?php echo $this->get_render_attribute_string('ma_el_bh_btn_link'); ?>>
													<?php echo  $settings['ma_el_bh_table_btn_text']; ?>
													<i class="fa fa-arrow-right"></i>
												</a>
											</div>
										<?php } ?>


										<?php if (
											$settings['ma_el_bh_show_button'] == 'yes' && $settings['ma_el_business_hours_style'] ==
											'table-reservation'
										) { ?>
											<div class="ma-el-business-hour-content-bottom">
												<a <?php echo $this->get_render_attribute_string('ma_el_bh_btn_link'); ?>>
													<?php echo  $settings['ma_el_bh_table_btn_text']; ?>
												</a>
											</div>
										<?php } ?>


										<?php if (($settings['ma_el_bh_show_button'] == 'yes') &&
											($settings['ma_el_business_hours_style'] !== 'content-corner-btn') &&
											($settings['ma_el_business_hours_style'] !== 'table-reservation')
										) { ?>
											<div class="ma-el-business-hour-content-bottom">
												<a <?php echo $this->get_render_attribute_string('ma_el_bh_normal_btn'); ?>>
													<?php echo  $settings['ma_el_bh_table_btn_text']; ?>
													<i class="fa fa-arrow-right"></i>
												</a>
											</div><!-- /.ma-el-business-hour-content-bottom -->
										<?php } ?>


									</div>

									<?php if (($settings['ma_el_business_hours_style'] == 'content-corner-btn') || ($settings['ma_el_business_hours_style'] == 'table-reservation')) { ?>
									</div>
								</div>
							<?php } ?>

						</div>
				<?php
				}
			}
