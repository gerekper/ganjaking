<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Widget_Base;

use \Essential_Addons_Elementor\Pro\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Fancy_Chart extends Widget_Base {

	public function get_name() {
		return 'eael-fancy-chart';
	}

	public function get_title() {
		return __( 'Fancy Chart', 'essential-addons-elementor' );
	}

	public function get_icon() {
		return 'eaicon-fancy-chart';
	}

	public function get_categories() {
		return [ 'essential-addons-elementor' ];
	}

	public function get_keywords() {
		return [
			'ea fancy chart',
			'fancy chart',
			'ea chart',
			'chart',
			'bar chart',
			'area chart',
			'line chart',
			'radar chart',
			'pie chart',
			'donut chart',
			'polar chart',
			'polar area chart',
			'ea',
		];
	}

	public function get_custom_help_url() {
		return 'https://essential-addons.com/elementor/docs/ea-fancy-chart/';
	}

	protected function register_controls() {
		/**
		 * Chart besic setting
		 */
		$this->start_controls_section(
			'eael_fancy_chart_setting',
			[
				'label' => esc_html__( 'General', 'essential-addons-elementor' )
			]
		);

		$this->add_control(
			'eael_fancy_chart_title',
			[
				'label'       => esc_html__( 'Chart Title', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your chart title', 'essential-addons-elementor' ),
				'default'     => esc_html__( 'Sample Chart Title', 'essential-addons-elementor' ),
				'dynamic'     => [ 'active' => true ],
				'ai'          => [ 'active' => false, ],
			]
		);

		$this->add_control(
			'eael_fancy_chart_title_tag',
			[
				'label'   => esc_html__( 'Title Tag', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h1'   => esc_html__( 'H1', 'essential-addons-elementor' ),
					'h2'   => esc_html__( 'H2', 'essential-addons-elementor' ),
					'h3'   => esc_html__( 'H3', 'essential-addons-elementor' ),
					'h4'   => esc_html__( 'H4', 'essential-addons-elementor' ),
					'h5'   => esc_html__( 'H5', 'essential-addons-elementor' ),
					'h6'   => esc_html__( 'H6', 'essential-addons-elementor' ),
					'span' => esc_html__( 'Span', 'essential-addons-elementor' ),
					'p'    => esc_html__( 'P', 'essential-addons-elementor' ),
					'div'  => esc_html__( 'Div', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_des',
			[
				'label'   => esc_html__( 'Description', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => esc_html__( 'Sample chart description', 'essential-addons-elementor' ),
				'ai'      => [ 'active' => false, ],
			]
		);

		$this->add_control(
			'eael_fancy_chart_heading_20',
			[
				'label'     => esc_html__( 'Settings', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_fancy_chart_chart_style',
			[
				'label'   => esc_html__( 'Chart Style', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'bar',
				'options' => [
					'bar'       => esc_html__( 'Bar', 'essential-addons-elementor' ),
					'area'      => esc_html__( 'Area', 'essential-addons-elementor' ),
					'line'      => esc_html__( 'Line', 'essential-addons-elementor' ),
					'radar'     => esc_html__( 'Radar', 'essential-addons-elementor' ),
					'pie'       => esc_html__( 'Pie', 'essential-addons-elementor' ),
					'donut'     => esc_html__( 'Donut', 'essential-addons-elementor' ),
					'polarArea' => esc_html__( 'Polar Area', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_chart_type',
			[
				'label'     => esc_html__( 'Chart Orientation', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'vertical',
				'options'   => [
					'vertical'   => esc_html__( 'Vertical', 'essential-addons-elementor' ),
					'horizontal' => esc_html__( 'Horizontal', 'essential-addons-elementor' ),
				],
				'condition' => [ 'eael_fancy_chart_chart_style' => 'bar' ]
			]
		);

		$this->add_control(
			'eael_fancy_chart_stroke',
			[
				'label'     => esc_html__( 'Stroke Style', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'smooth',
				'options'   => [
					'smooth'   => esc_html__( 'Smooth', 'essential-addons-elementor' ),
					'straight' => esc_html__( 'Straight', 'essential-addons-elementor' ),
					'stepline' => esc_html__( 'Step line', 'essential-addons-elementor' ),
				],
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'area', 'line' ]
				]
			]
		);

		$this->add_control(
			'eael_fancy_chart_fill_type',
			[
				'label'   => esc_html__( 'Fill Type', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'gradient' => esc_html__( 'Gradient', 'essential-addons-elementor' ),
					'solid'    => esc_html__( 'Solid', 'essential-addons-elementor' ),
					'pattern'  => esc_html__( 'Pattern', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_stacked',
			[
				'label'        => esc_html__( 'Stacked', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Off', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'eael_fancy_chart_chart_style' => [ 'bar', 'area' ]
				]
			]
		);

		$this->add_control(
			'eael_show_donut_central_labels',
			[
				'label'        => esc_html__( 'Show Central Labels', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_fancy_chart_chart_style' => 'donut'
				]
			]
		);

		$this->add_control(
			'eael_show_donut_total',
			[
				'label'        => esc_html__( 'Show Total', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_fancy_chart_chart_style'   => 'donut',
					'eael_show_donut_central_labels' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_donut_total_always',
			[
				'label'        => esc_html__( 'Show Always Total', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'eael_fancy_chart_chart_style'   => 'donut',
					'eael_show_donut_central_labels' => 'yes'
				]
			]
		);

		//Advanched features
		$this->add_control(
			'eael_fancy_chart_heading_af',
			[
				'label'     => esc_html__( 'Advanced Settings', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_fancy_chart_toolbar_show',
			[
				'label'        => esc_html__( 'Toolbar', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'eael_fancy_chart_tooltip_enable',
			[
				'label'        => esc_html__( 'Tooltip Enable', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_label_enable',
			[
				'label'        => esc_html__( 'Data Label', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_position',
			[
				'label'   => esc_html__( 'Data Position', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'top'    => esc_html__( 'Top', 'essential-addons-elementor' ),
					'center' => esc_html__( 'Center', 'essential-addons-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_legend_show',
			[
				'label'        => esc_html__( 'Show Legend', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'eael_fancy_chart_legend_position',
			[
				'label'   => esc_html__( 'Legend Position', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'top',
				'options' => [
					'top'    => esc_html__( 'Top', 'essential-addons-elementor' ),
					'right'  => esc_html__( 'Right', 'essential-addons-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'essential-addons-elementor' ),
					'left'   => esc_html__( 'Left', 'essential-addons-elementor' ),
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Chart data controll
		 */
		$this->start_controls_section(
			'eael_fancy_chart_data_setting_panel',
			[
				'label' => esc_html__( 'Data', 'essential-addons-elementor' )
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_option_type',
			[
				'label'   => esc_html__( 'Data Source', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual'       => esc_html__( 'Static', 'essential-addons-elementor' ),
					'json'         => esc_html__( 'JSON', 'essential-addons-elementor' ),
					'csv'          => esc_html__( 'CSV', 'essential-addons-elementor' ),
					'google_sheet' => esc_html__( 'Google Sheets', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_csv_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'You will have to <strong>copy/paste</strong> the content from your <strong>.CSV</strong> file to show your data.', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'eael_fancy_chart_data_option_type' => 'csv',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_json_note',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => __( 'You will have to <strong>copy/paste</strong> the content from your <strong>.JSON</strong> file to show your data.', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'eael_fancy_chart_data_option_type' => 'json',
				],
			]
		);

		/**
		 * Category List
		 */

		$this->add_control(
			'eael_fancy_chart_data_option_json',
			[
				'label'       => esc_html__( 'JSON Datasets', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 15,
				'default'     => '{
"category": ["Category 1", "Category 2", "Category 3", "Category 4"],
"dataset": [
{
"name": "Dataset 1", "data": ["44", "75", "35", "13"], "color": "#7385FF" 
},
{
"name": "Dataset 2", "data": ["55", "85", "41", "101"], "color": "#A88FF7" 
},
{
"name": "Dataset 3", "data": ["57", "90", "36", "72"], "color": "#FC9DD9" 
},
{
"name": "Dataset 4", "data": ["45", "26", "12", "60"], "color": "#75C5B1" 
}
]
}',
				'placeholder' => esc_html__( 'Insert JSON data here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_chart_style'      => [ 'bar', 'area', 'line', 'radar' ],
					'eael_fancy_chart_data_option_type' => [ 'json' ],
				],
				'ai'          => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_demo_json',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => '<strong>Dataset Format: </strong> <br>
                {<br>
                    "category": ["Category 1", "Category 2"], <br>
                    "dataset": [ <br>
                    { <br>
                    "name": "Dataset 1",
                    "data": ["44", "75", "35", "13"],
                    "color": "#7385FF"
                    <br> }, <br>
                    { <br>
                    "name": "Dataset 2",
                    "data": ["55", "85", "41", "101"],
                    "color": "#A88FF7"
                    <br> }<br>
                    ]<br>
                    }',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'eael_fancy_chart_chart_style'      => [ 'bar', 'area', 'line', 'radar' ],
					'eael_fancy_chart_data_option_type' => 'json',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_single_json',
			[
				'label'       => esc_html__( 'JSON Dataset', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 10,
				'default'     => '{
"category": ["Category 1", " Category 2", "Category 3", "Category 4"],
"dataset": [
{"data": "44", "color": "#7385FF"},
{"data": "76", "color": "#A88FF7"},
{"data": "35", "color": "#FC9DD9"},
{"data": "13", "color": "#75C5B1"}
]
}',
				'placeholder' => esc_html__( 'Insert JSON data here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_data_option_type' => [ 'json' ],
					'eael_fancy_chart_chart_style'      => [ 'pie', 'donut', 'polarArea' ],
				],
				'ai'          => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_demo_single_json',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => '<strong>Dataset Format: </strong> <br>
                {<br>
                    "category": ["Category 1", "Category 2", "Category 3", "Category 4"], <br>
                    "dataset": [ <br>
                    {
                    "data": "44",
                    "color": "#7385FF"
                    }, <br>
                    {
                    "data": "76",
                    "color": "#A88FF7"
                    }, <br>
                    {
                    "data": "35",
                    "color": "#FC9DD9"
                    }, <br>
                    {
                    "data": "13",
                    "color": "#75C5B1"
                    } <br>
                    ]<br>
                    }',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'eael_fancy_chart_data_option_type' => 'json',
					'eael_fancy_chart_chart_style'      => [ 'pie', 'donut', 'polarArea' ],
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_option_csv',
			[
				'label'       => esc_html__( 'CSV Datasets', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => 'Category,Category 1,Category 2,Category 3, Category 4
Dataset 1, 44, 75, 35, 13
Dataset 2, 55, 85, 41, 101
Dataset 3, 57, 90, 36, 72
Dataset 4, 45, 26, 12, 60
#color,#7385FF,#A88FF7,#FC9DD9,#75C5B1',
				'rows'        => 10,
				'placeholder' => esc_html__( 'Insert CSV data here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_chart_style'      => [ 'bar', 'area', 'line', 'radar' ],
					'eael_fancy_chart_data_option_type' => 'csv',
				],
				'ai'          => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_demo_csv',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<strong>Dataset Format:</strong> <br>
                Category,Category 1,Category 2,Category 3,Category 4 <br>
                Dataset 1, 44, 75, 35, 13 <br>
                Dataset 2, 55, 85, 41, 101 <br>
                Dataset 3, 57, 90, 36, 72 <br>
                Dataset 4, 45, 26, 12, 60 <br>
                #color,#7385FF,#A88FF7,#FC9DD9,#75C5B1',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'eael_fancy_chart_chart_style'      => [ 'bar', 'area', 'line', 'radar' ],
					'eael_fancy_chart_data_option_type' => 'csv',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_single_csv',
			[
				'label'       => esc_html__( 'CSV Dataset', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => 'Category,Category 1, Category 2, Category 3, Category 4
44, #7385FF
76, #A88FF7
55, #FC9DD9
35, #75C5B1',
				'rows'        => 10,
				'placeholder' => esc_html__( 'Insert CSV data here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_chart_style'      => [ 'pie', 'donut', 'polarArea' ],
					'eael_fancy_chart_data_option_type' => 'csv',
				],
				'ai'          => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_demo_single_csv',
			[
				'type'            => Controls_Manager  :: RAW_HTML,
				'raw'             => '<strong>Dataset Format: </strong> <br>
                Category,Category 1, Category 2, Category 3, Category 4 <br>
                44, #7385FF <br>
                76, #A88FF7 <br>
                55, #FC9DD9 <br>
                35, #75C5B1',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'eael_fancy_chart_chart_style'      => [ 'pie', 'donut', 'polarArea' ],
					'eael_fancy_chart_data_option_type' => 'csv',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_api_key',
			[
				'label'       => esc_html__( 'API Key', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Insert API Key Here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_data_option_type' => 'google_sheet',
				],
				'ai'          => [ 'active' => false, ],
			]
		);

		$this->add_control(
			'eael_fancy_chart_sheet_id',
			[
				'label'       => esc_html__( 'Sheet ID', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Insert Sheet ID', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_data_option_type' => 'google_sheet',
				],
				'ai'          => [ 'active' => false, ],
			]
		);

		$this->add_control(
			'eael_fancy_chart_table_range',
			[
				'label'       => esc_html__( 'Table Range', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type Range Name', 'essential-addons-elementor' ),
				'default'     => esc_html__( 'Sheet1', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_data_option_type' => 'google_sheet',
				],
				'ai'          => [ 'active' => false, ],
			]
		);

		$this->add_control(
			'eael_fancy_chart_sheet_cache_limit',
			[
				'label'       => __( 'Data Cache Time', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 60,
				'description' => esc_html__( 'Cache expiration time (Minutes)', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_fancy_chart_data_option_type' => 'google_sheet',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'eael_fancy_chart_category',
			[
				'label'       => esc_html__( 'Category Title', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Category Title', 'essential-addons-elementor' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'ai'          => [ 'active' => false, ],
			]
		);

		$this->add_control(
			'eael_fancy_chart_heading_21',
			[
				'label'     => esc_html__( 'Categories', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_fancy_chart_data_option_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_category_list',
			[
				'label'       => esc_html__( '', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'eael_fancy_chart_category' => esc_html__( 'Category 1', 'essential-addons-elementor' ) ],
					[ 'eael_fancy_chart_category' => esc_html__( 'Category 2', 'essential-addons-elementor' ) ],
					[ 'eael_fancy_chart_category' => esc_html__( 'Category 3', 'essential-addons-elementor' ) ],
					[ 'eael_fancy_chart_category' => esc_html__( 'Category 4', 'essential-addons-elementor' ) ],
				],
				'title_field' => '{{{ eael_fancy_chart_category }}}',
				'condition'   => [
					'eael_fancy_chart_data_option_type' => 'manual',
				],
			]
		);

		/**
		 * Data List
		 */
		$data_repeater = new \Elementor\Repeater();

		$data_repeater->add_control(
			'eael_data_chart_list',
			[
				'label'       => esc_html__( 'Value', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Value', 'essential-addons-elementor' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'ai'          => [ 'active' => false, ],
			]
		);

		$data_repeater->add_control(
			'eael_chart_dat_color',
			[
				'label' => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'eael_fancy_chart_heading_22',
			[
				'label'     => esc_html__( 'Datasets', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_fancy_chart_chart_style'      => [ 'pie', 'donut', 'polarArea' ],
					'eael_fancy_chart_data_option_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_list',
			[
				'label'       => esc_html__( '', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $data_repeater->get_controls(),
				'default'     => [
					[
						'eael_data_chart_list' => esc_html__( '44', 'essential-addons-elementor' ),
						'eael_chart_dat_color' => '#7385FF',
					],
					[
						'eael_data_chart_list' => esc_html__( '76', 'essential-addons-elementor' ),
						'eael_chart_dat_color' => '#A88FF7',
					],
					[
						'eael_data_chart_list' => esc_html__( '35', 'essential-addons-elementor' ),
						'eael_chart_dat_color' => '#FC9DD9',
					],
					[
						'eael_data_chart_list' => esc_html__( '13', 'essential-addons-elementor' ),
						'eael_chart_dat_color' => '#75C5B1',
					],
				],
				'condition'   => [
					'eael_fancy_chart_chart_style'      => [ 'pie', 'donut', 'polarArea' ],
					'eael_fancy_chart_data_option_type' => 'manual',
				],
				'title_field' => '{{{ eael_data_chart_list }}}',
			]
		);

		/**
		 * Group Data List
		 */
		$group_data_repeater = new \Elementor\Repeater();

		$group_data_repeater->add_control(
			'eael_group_data_chart_title',
			[
				'label'       => esc_html__( 'Data Label', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Data Label', 'essential-addons-elementor' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'ai'          => [ 'active' => false, ],
			]
		);

		$group_data_repeater->add_control(
			'eael_group_data_lists',
			[
				'label'       => esc_html__( 'Value', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Value', 'essential-addons-elementor' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'ai'          => [ 'active' => false, ],
			]
		);

		$group_data_repeater->add_control(
			'eael_fancy_chart_global_warning_text',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( '
Group data values by Comma ( , )
Example: 14, 25, 35, 9, 55', 'essential-addons-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$group_data_repeater->add_control(
			'eael_group_data_list_color',
			[
				'label' => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'eael_fancy_chart_heading_23',
			[
				'label'     => esc_html__( 'Datasets', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_fancy_chart_chart_style'      => [ 'bar', 'area', 'line', 'radar' ],
					'eael_fancy_chart_data_option_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_group_data_list',
			[
				'label'       => esc_html__( '', 'essential-addons-elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $group_data_repeater->get_controls(),
				'default'     => [
					[
						'eael_group_data_chart_title' => esc_html__( 'Dataset 1', 'essential-addons-elementor' ),
						'eael_group_data_lists'       => esc_html__( '44, 75, 35, 13', 'essential-addons-elementor' ),
						'eael_group_data_list_color'  => '#7385FF',
					],
					[
						'eael_group_data_chart_title' => esc_html__( 'Dataset 2', 'essential-addons-elementor' ),
						'eael_group_data_lists'       => esc_html__( '55, 85, 41, 101', 'essential-addons-elementor' ),
						'eael_group_data_list_color'  => '#A88FF7',
					],
					[
						'eael_group_data_chart_title' => esc_html__( 'Dataset 3', 'essential-addons-elementor' ),
						'eael_group_data_lists'       => esc_html__( '57, 90, 36, 72', 'essential-addons-elementor' ),
						'eael_group_data_list_color'  => '#FC9DD9',
					],
					[
						'eael_group_data_chart_title' => esc_html__( 'Dataset 4', 'essential-addons-elementor' ),
						'eael_group_data_lists'       => esc_html__( '45, 26, 12, 60', 'essential-addons-elementor' ),
						'eael_group_data_list_color'  => '#75C5B1',
					],
				],
				'condition'   => [
					'eael_fancy_chart_chart_style'      => [ 'bar', 'area', 'line', 'radar' ],
					'eael_fancy_chart_data_option_type' => [ 'manual' ],
				],
				'title_field' => '{{{ eael_group_data_chart_title }}}',
			]
		);

		$this->end_controls_section();

		/**
		 * Start style section
		 */
		$this->start_controls_section(
			'eael_header_section',
			[
				'label' => esc_html__( 'Header Style', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_fancy_chart_title_align',
			[
				'label'     => esc_html__( 'Title Alignment', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .eael_fancy_chart_title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael_fancy_chart_title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_fancy_chart_header_typography',
				'selector' => '{{WRAPPER}} .eael_fancy_chart_title',
			]
		);

		$this->add_control(
			'eael_fancy_chart_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael_fancy_chart_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//Descriptions
		$this->add_control(
			'eael_fancy_chart_desc_style',
			[
				'label'     => esc_html__( 'Description', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_fancy_chart_desc_align',
			[
				'label'     => esc_html__( 'Alignment', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .eael_fancy_chart_header p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_desc_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael_fancy_chart_header p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_fancy_chart_desc_typography',
				'selector' => '{{WRAPPER}} .eael_fancy_chart_header p',
			]
		);

		$this->add_control(
			'eael_fancy_chart_desc_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael_fancy_chart_header p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_chart_section',
			[
				'label' => esc_html__( 'Chart Style', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_chart_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'eael_fancy_chart_border_radious',
			[
				'label'     => esc_html__( 'Border Radious', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'step'      => 1,
				'default'   => 0,
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'bar' ]
				]
			]
		);

		$this->add_control(
			'eael_fancy_chart_height',
			[
				'label'      => esc_html__( 'Height', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 10,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 450,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_width',
			[
				'label'      => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 10,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_offsetx',
			[
				'label'   => esc_html__( 'OffsetX', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_offsety',
			[
				'label'   => esc_html__( 'OffsetY', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->end_controls_section();


		//Data labels style settings
		$this->start_controls_section(
			'eael_fancy_chart_data_labels_styles_section',
			[
				'label'     => esc_html__( 'Data Labels', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_data_label_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_label_color',
			[
				'label'   => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff'
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_label_font_size',
			[
				'label'      => esc_html__( 'Font Size (PX)', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 0.5,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
			]
		);

//		$this->add_control(
//			'eael_fancy_chart_data_label_font_family',
//			[
//				'label' => esc_html__( 'Font Family', 'essential-addons-elementor' ),
//				'type'  => \Elementor\Controls_Manager::FONT,
//				'selectors' => [
//					'{{WRAPPER}} .eael_fancy_chart' => 'font-family: {{VALUE}}',
//				],
//			]
//		);


		$this->end_controls_section();


		//Bar Setting
		$this->start_controls_section(
			'eael_fancy_chart_bar_section',
			[
				'label'     => esc_html__( 'Bar Style', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'bar', 'radar' ],
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_bar_width',
			[
				'label'   => esc_html__( 'Bar Width', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 25,
				],
			]
		);

		$this->end_controls_section();

		//Animation Setting
		$this->start_controls_section(
			'eael_fancy_chart_animation_section',
			[
				'label' => esc_html__( 'Animation', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_fancy_chart_animation_show',
			[
				'label'        => esc_html__( 'Animation', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'eael_fancy_chart_animation_speed',
			[
				'label'     => esc_html__( 'Speed', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 10,
					],
				],
				'default'   => [
					'size' => 1100,
				],
				'condition' => [
					'eael_fancy_chart_animation_show' => 'true',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_animation_delay',
			[
				'label'     => esc_html__( 'Delay', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 10,
					],
				],
				'default'   => [
					'size' => 250,
				],
				'condition' => [
					'eael_fancy_chart_animation_show' => 'true',
				],
			]
		);

		$this->end_controls_section();

		//Grid Setting
		$this->start_controls_section(
			'eael_fancy_chart_grid_section',
			[
				'label'     => esc_html__( 'Grid Style', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'bar', 'area', 'line', 'radar' ],
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_show_grid',
			[
				'label'        => esc_html__( 'Show Grid', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'eael_fancy_chart_grid_border_color',
			[
				'label'   => esc_html__( 'Border Color', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#ddd',
			]
		);

		$this->add_control(
			'eael_fancy_chart_grid_dash_stroke',
			[
				'label'   => esc_html__( 'Dash Stroke', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
				'default' => 0,
			]
		);

		$this->add_control(
			'eael_fancy_chart_y_axis_line',
			[
				'label'        => esc_html__( 'Y-Axis Line', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'eael_fancy_chart_x_axis_line',
			[
				'label'        => esc_html__( 'X-Axis Line', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'false',
				// 'default'      => 'false',
			]
		);

		$this->end_controls_section();

		//Grid Setting
		$this->start_controls_section(
			'eael_fancy_chart_stroke_section',
			[
				'label'     => esc_html__( 'Stroke Style', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'bar', 'area', 'radar', 'pie', 'donut', 'polarArea' ],
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_stroke_show',
			[
				'label'        => esc_html__( 'Stroke Show', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$this->add_control(
			'eael_fancy_chart_stroke_color',
			[
				'label'   => esc_html__( 'Stroke Color', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#FFF',
			]
		);

		$this->add_control(
			'eael_fancy_chart_stroke_width',
			[
				'label'   => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 2,
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_dash_stroke',
			[
				'label'   => esc_html__( 'Dash Stroke', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
				'default' => 0,
			]
		);

		$this->end_controls_section();

		//Tooltip
		$this->start_controls_section(
			'eael_fancy_chart_tooltip_style',
			[
				'label'     => esc_html__( 'Tooltip Style', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_tooltip_enable' => 'yes',
					'eael_fancy_chart_chart_style'    => [ 'bar', 'area', 'line', 'radar' ],
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_tooltip_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apexcharts-tooltip.apexcharts-theme-light'                           => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_tooltip_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apexcharts-tooltip.apexcharts-theme-light' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_tooltip_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apexcharts-tooltip.apexcharts-theme-light'                           => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		//X-Axis
		$this->start_controls_section(
			'eael_fancy_chart_x_axis_setting',
			[
				'label'     => esc_html__( 'X-Axis Settings', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'bar', 'area', 'line', 'radar' ]
				]
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_x_label',
			[
				'label'        => esc_html__( 'Labels', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_x_position',
			[
				'label'   => esc_html__( 'Position', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'essential-addons-elementor' ),
						'icon'  => 'eicon-arrow-down',
					],
					'top'    => [
						'title' => esc_html__( 'Top', 'essential-addons-elementor' ),
						'icon'  => 'eicon-arrow-up',
					],
				],
				'default' => 'bottom',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'eael_chart_front_color_x_axis',
			[
				'label' => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_fancy_cahrt_xaxis_typography',
				'selector' => '{{WRAPPER}} .eael_fancy_cahrt_xaxis tspan',
			]
		);

		$this->end_controls_section();

		//Y-Axis
		$this->start_controls_section(
			'eael_fancy_chart_y_axis_setting',
			[
				'label'     => esc_html__( 'Y-Axis Settings', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_fancy_chart_chart_style' => [ 'bar', 'area', 'line', 'radar' ]
				],
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_y_label',
			[
				'label'        => esc_html__( 'Labels', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_fancy_chart_data_y_position',
			[
				'label'   => esc_html__( 'Position', 'essential-addons-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					true  => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-arrow-right',
					],
					false => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-arrow-left',
					],
				],
				'default' => false,
				'toggle'  => false,
			]
		);

		$this->add_control(
			'eael_chart_front_color_y_axis',
			[
				'label' => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_fancy_cahrt_yaxis_typography',
				'selector' => '{{WRAPPER}} .eael_fancy_cahrt_yaxis tspan',
			]
		);

		$this->end_controls_section();
	}

	protected function eael_replace_color( $default, $replace ) {
		foreach ( $replace as $k => $v ) {
			$default[ $k ] = empty( $v ) ? $default[ $k ] ?? '#000' : $v;
		}

		return $default;
	}

	//Method for bar options
	protected function get_chart_style_bar_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$horizontal_true      = 'horizontal' === $fancy_chart_settings['eael_fancy_chart_type'];
		$stacked              = $settings['eael_fancy_chart_stacked'] === 'yes';

		$data_options = [
			'chart'       => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
				'stacked'    => $stacked
			],
			'series'      => $eael_dafault_data_attrs['eael_chart_data_set'],
			'xaxis'       => [
				'categories' => $eael_dafault_data_attrs['eael_chart_data_cat_set'],
				'position'   => $fancy_chart_settings['eael_fancy_chart_data_x_position'],
				'labels'     => [
					'show'  => $fancy_chart_settings['eael_fancy_chart_data_x_label'],
					'style' => [
						'cssClass' => 'eael_fancy_cahrt_xaxis',
						'colors'   => $settings['eael_chart_front_color_x_axis'],
					],
				]
			],
			'yaxis'       => [
				'show'     => true,
				'opposite' => ( $fancy_chart_settings['eael_fancy_chart_data_y_position'] == 0 ) ? '' : $fancy_chart_settings['eael_fancy_chart_data_y_position'],
				'labels'   => [
					'show'  => $fancy_chart_settings['eael_fancy_chart_data_y_label'],
					'style' => [
						'cssClass' => 'eael_fancy_cahrt_yaxis',
						'colors'   => $settings['eael_chart_front_color_y_axis'],
					],
				],
			],
			'legend'      => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'tooltip'     => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'grid'        => [
				'show'            => $settings['eael_fancy_chart_show_grid'],
				'borderColor'     => $settings['eael_fancy_chart_grid_border_color'],
				'strokeDashArray' => $settings['eael_fancy_chart_grid_dash_stroke'],
				'xaxis'           => [
					'lines' => [
						'show' => $settings['eael_fancy_chart_x_axis_line']
					]
				],
				'yaxis'           => [
					'lines' => [
						'show' => $settings['eael_fancy_chart_y_axis_line']
					]
				],
			],
			'plotOptions' => [
				'bar' => [
					'borderRadius' => $fancy_chart_settings['eael_fancy_chart_border_radious'],
					'dataLabels'   => [
						'position' => $fancy_chart_settings['eael_fancy_chart_data_position'],
					],
					'horizontal'   => $horizontal_true,
					'columnWidth'  => $fancy_chart_settings['eael_fancy_chart_bar_width'],
					'barHeight'    => $fancy_chart_settings['eael_fancy_chart_bar_width'],
				],
			],
			'stroke'      => [
				'show'      => $settings['eael_fancy_chart_stroke_show'],
				'lineCap'   => 'butt',
				'colors'    => [ $settings['eael_fancy_chart_stroke_color'] ],
				'width'     => $settings['eael_fancy_chart_stroke_width']['size'],
				'dashArray' => $settings['eael_fancy_chart_dash_stroke'],
			],
			'dataLabels'  => [
				'enabled' => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'   => [
					'colors'   => [ $fancy_chart_settings['data_label_color'] ],
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				],
			]
		];

		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];

			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}

		return $data_options;
	}

	//Method for area options
	protected function get_chart_style_area_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$stacked              = $settings['eael_fancy_chart_stacked'] === 'yes';

		$data_options = [
			'chart'      => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
				'stacked'    => $stacked
			],
			'series'     => $eael_dafault_data_attrs['eael_chart_data_set'],
			'xaxis'      => [
				'categories' => $eael_dafault_data_attrs['eael_chart_data_cat_set'],
				'position'   => $fancy_chart_settings['eael_fancy_chart_data_x_position'],
				'labels'     => [
					'show'  => $fancy_chart_settings['eael_fancy_chart_data_x_label'],
					'style' => [
						'cssClass' => 'eael_fancy_cahrt_xaxis',
						'colors'   => $settings['eael_chart_front_color_x_axis'],
					],
				]
			],
			'yaxis'      => [
				'show'     => true,
				'opposite' => ( $fancy_chart_settings['eael_fancy_chart_data_y_position'] == 0 ) ? '' : $fancy_chart_settings['eael_fancy_chart_data_y_position'],
				'labels'   => [
					'show'  => $fancy_chart_settings['eael_fancy_chart_data_y_label'],
					'style' => [
						'cssClass' => 'eael_fancy_cahrt_yaxis',
						'colors'   => $settings['eael_chart_front_color_y_axis'],
					],
				],
			],
			'legend'     => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'tooltip'    => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'grid'       => [
				'show'            => $settings['eael_fancy_chart_show_grid'],
				'borderColor'     => $settings['eael_fancy_chart_grid_border_color'],
				'strokeDashArray' => $settings['eael_fancy_chart_grid_dash_stroke'],
				'xaxis'           => [
					'lines' => [
						'show' => $settings['eael_fancy_chart_x_axis_line']
					]
				],
				'yaxis'           => [
					'lines' => [
						'show' => $settings['eael_fancy_chart_y_axis_line']
					]
				],
			],
			'stroke'     => [
				'show'      => $settings['eael_fancy_chart_stroke_show'],
				'curve'     => $settings['eael_fancy_chart_stroke'],
				'lineCap'   => 'butt',
				'colors'    => [ $settings['eael_fancy_chart_stroke_color'] ],
				'width'     => $settings['eael_fancy_chart_stroke_width']['size'],
				'dashArray' => $settings['eael_fancy_chart_dash_stroke'],
			],
			'dataLabels' => [
				'enabled'    => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'      => [
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				],
				'background' => [
					'enabled'   => true,
					'foreColor' => $fancy_chart_settings['data_label_color'],
				]
			]
		];
		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];
			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}

		return $data_options;
	}

	//Method for line data options
	protected function get_chart_style_line_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$data_options         = [
			'chart'      => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
			],
			'series'     => $eael_dafault_data_attrs['eael_chart_data_set'],
			'xaxis'      => [
				'categories' => $eael_dafault_data_attrs['eael_chart_data_cat_set'],
				'position'   => $fancy_chart_settings['eael_fancy_chart_data_x_position'],
				'labels'     => [
					'show'  => $fancy_chart_settings['eael_fancy_chart_data_x_label'],
					'style' => [
						'cssClass' => 'eael_fancy_cahrt_xaxis',
						'colors'   => $settings['eael_chart_front_color_x_axis'],
					],
				]
			],
			'yaxis'      => [
				'show'     => true,
				'opposite' => ( $fancy_chart_settings['eael_fancy_chart_data_y_position'] == 0 ) ? '' : $fancy_chart_settings['eael_fancy_chart_data_y_position'],
				'labels'   => [
					'show'  => $fancy_chart_settings['eael_fancy_chart_data_y_label'],
					'style' => [
						'cssClass' => 'eael_fancy_cahrt_yaxis',
						'colors'   => $settings['eael_chart_front_color_y_axis'],
					],
				],
			],
			'legend'     => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'tooltip'    => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'grid'       => [
				'show'            => $settings['eael_fancy_chart_show_grid'],
				'borderColor'     => $settings['eael_fancy_chart_grid_border_color'],
				'strokeDashArray' => $settings['eael_fancy_chart_grid_dash_stroke'],
				'xaxis'           => [
					'lines' => [
						'show' => $settings['eael_fancy_chart_x_axis_line']
					]
				],
				'yaxis'           => [
					'lines' => [
						'show' => $settings['eael_fancy_chart_y_axis_line']
					]
				],
			],
			'dataLabels' => [
				'enabled'    => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'      => [
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				],
				'background' => [
					'enabled'   => true,
					'foreColor' => $fancy_chart_settings['data_label_color'],
				]
			]
		];
		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];
			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}
		$data_options['stroke']['curve'] = $settings['eael_fancy_chart_stroke'];

		return $data_options;
	}

	//Method for rader options
	protected function get_chart_style_radar_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$data_options         = [
			'chart'       => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
			],
			'series'      => $eael_dafault_data_attrs['eael_chart_data_set'],
			'legend'      => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'xaxis'       => [
				'categories' => $eael_dafault_data_attrs['eael_chart_data_cat_set'],
				'labels'     => [
					'show' => $fancy_chart_settings['eael_fancy_chart_data_x_label'],
				]
			],
			'tooltip'     => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'stroke'      => [
				'show'      => $settings['eael_fancy_chart_stroke_show'],
				'curve'     => 'smooth',
				'lineCap'   => 'butt',
				'colors'    => [ $settings['eael_fancy_chart_stroke_color'] ],
				'width'     => $settings['eael_fancy_chart_stroke_width']['size'],
				'dashArray' => $settings['eael_fancy_chart_dash_stroke'],
			],
			'plotOptions' => [
				'radar' => [
					'polygons' => [
						'strokeColors' => $settings['eael_fancy_chart_grid_border_color'],
						'strokeWidth'  => 1,
					],
				],
			],
			'dataLabels'  => [
				'enabled'    => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'      => [
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				],
				'background' => [
					'enabled'   => true,
					'foreColor' => $fancy_chart_settings['data_label_color'],
				]
			]
		];
		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];
			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}

		return $data_options;
	}

	//Mehtod for pie options
	protected function get_chart_style_pie_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$data_options         = [
			'chart'      => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
			],
			'series'     => $eael_dafault_data_attrs['new_group_data_lists'],
			'labels'     => $eael_dafault_data_attrs['eael_chart_category_lists'],
			'legend'     => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'tooltip'    => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'stroke'     => [
				'show'      => $settings['eael_fancy_chart_stroke_show'],
				'curve'     => 'smooth',
				'lineCap'   => 'butt',
				'colors'    => [ $settings['eael_fancy_chart_stroke_color'] ],
				'width'     => $settings['eael_fancy_chart_stroke_width']['size'],
				'dashArray' => $settings['eael_fancy_chart_dash_stroke'],
			],
			'dataLabels' => [
				'enabled' => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'   => [
					'colors'   => [ $fancy_chart_settings['data_label_color'] ],
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				]
			]
		];
		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];
			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}

		return $data_options;
	}

	//Methos for donut options
	protected function get_chart_style_donut_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$data_options         = [
			'chart'       => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
			],
			'series'      => $eael_dafault_data_attrs['new_group_data_lists'],
			'labels'      => $eael_dafault_data_attrs['eael_chart_category_lists'],
			'legend'      => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'tooltip'     => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'plotOptions' => [
				'pie' => [
					'donut' => [
						'labels' => [
							'show'  => isset( $settings['eael_show_donut_central_labels'] ) && $settings['eael_show_donut_central_labels'] === 'yes',
							'name'  => [
								'show' => isset( $settings['eael_show_donut_total'] ) && $settings['eael_show_donut_total'] === 'yes'
							],
							'value' => [
								'show' => isset( $settings['eael_show_donut_total'] ) && $settings['eael_show_donut_total'] === 'yes'
							],
							'total' => [
								'showAlways' => isset( $settings['eael_show_donut_total_always'] ) && $settings['eael_show_donut_total_always'] === 'yes',
								'show'       => isset( $settings['eael_show_donut_total'] ) && $settings['eael_show_donut_total'] === 'yes'
							]
						]
					]
				],
			],
			'stroke'      => [
				'show'    => $settings['eael_fancy_chart_stroke_show'],
				'curve'   => 'smooth',
				'lineCap' => 'butt',
				'colors'  => [ $settings['eael_fancy_chart_stroke_color'] ],
				'width'   => $settings['eael_fancy_chart_stroke_width']['size'],
			],
			'dataLabels'  => [
				'enabled' => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'   => [
					'colors'   => [ $fancy_chart_settings['data_label_color'] ],
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				]
			]
		];
		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];
			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}

		return $data_options;
	}

	//Method for polararea options
	protected function get_chart_style_polararea_options( $settings, $eael_dafault_data_attrs ) {
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );
		$data_options         = [
			'chart'       => [
				'toolbar'    => [
					'show' => $settings['eael_fancy_chart_toolbar_show']
				],
				'type'       => $fancy_chart_settings['eael_chart_style'],
				'background' => $settings['eael_chart_bg_color'],
				'height'     => $fancy_chart_settings['eael_chart_height'] . $fancy_chart_settings['eael_chart_height_unit'],
				'width'      => $fancy_chart_settings['eael_chart_width'] . $fancy_chart_settings['eael_chart_width_unit'],
				'animations' => [
					'enabled'          => $settings['eael_fancy_chart_animation_show'],
					'easing'           => 'easeinout',
					'speed'            => $fancy_chart_settings['eael_fancy_chart_animation_speed'],
					'animateGradually' => [
						'delay' => $fancy_chart_settings['eael_fancy_chart_animation_delay'],
					],
				],
				'offsetX'    => $settings['eael_fancy_chart_offsetx']['size'],
				'offsetY'    => $settings['eael_fancy_chart_offsety']['size'],
			],
			'series'      => $eael_dafault_data_attrs['new_group_data_lists'],
			'labels'      => $eael_dafault_data_attrs['eael_chart_category_lists'],
			'legend'      => [
				'show'     => $fancy_chart_settings['eael_fancy_chart_legend_show'],
				'position' => $fancy_chart_settings['eael_fancy_chart_legend_position'],
			],
			'tooltip'     => [
				'enabled' => $fancy_chart_settings['eael_chart_style_tooltip_enabel'],
			],
			'stroke'      => [
				'show'    => $settings['eael_fancy_chart_stroke_show'],
				'curve'   => 'smooth',
				'lineCap' => 'butt',
				'colors'  => [ $settings['eael_fancy_chart_stroke_color'] ],
				'width'   => $settings['eael_fancy_chart_stroke_width']['size'],
			],
			'plotOptions' => [
				'polarArea' => [
					'rings'  => [
						'strokeWidth' => 1,
						'strokeColor' => $settings['eael_fancy_chart_grid_border_color'],
					],
					'spokes' => [
						'strokeWidth'     => 1,
						'connectorColors' => $eael_dafault_data_attrs['eael_group_color_array'],
					],
				],
			],
			'dataLabels'  => [
				'enabled'    => $settings['eael_fancy_chart_data_label_enable'] === 'yes',
				'style'      => [
//					'fontFamily' => $settings['eael_fancy_chart_data_label_font_family'],
					'fontSize' => $fancy_chart_settings['data_label_font_size'],
				],
				'background' => [
					'enabled'   => true,
					'foreColor' => $fancy_chart_settings['data_label_color'],
				]
			]
		];
		if ( ! empty( $eael_dafault_data_attrs['eael_group_color_array'] ) ) {
			$data_options['colors'] = $eael_dafault_data_attrs['eael_group_color_array'];
			$data_options['fill']   = [
				'colors'  => $eael_dafault_data_attrs['eael_group_color_array'],
				'opacity' => 0.9,
				'type'    => $settings['eael_fancy_chart_fill_type'],
			];
			if ( $settings['eael_fancy_chart_fill_type'] === 'gradient' ) {
				$data_options['fill']['gradient'] = [
					'shadeIntensity' => 0,
					'opacityFrom'    => 0.5,
					'opacityTo'      => 0.9,
					'stops'          => [ 0, 90, 100 ]
				];
			}
		}

		return $data_options;
	}

	/**
	 * Get setting from elementor control
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	protected function get_fancy_chart_settings( $settings ) {
		$fancy_chart_settings                                     = [];
		$fancy_chart_settings['category_lists']                   = ! empty( $settings['eael_fancy_chart_category_list'] ) ? $settings['eael_fancy_chart_category_list'] : '';
		$fancy_chart_settings['data_lists']                       = ! empty( $settings['eael_fancy_chart_data_list'] ) ? $settings['eael_fancy_chart_data_list'] : '';
		$fancy_chart_settings['group_data_lists']                 = ! empty( $settings['eael_fancy_chart_group_data_list'] ) ? $settings['eael_fancy_chart_group_data_list'] : '';
		$fancy_chart_settings['eael_chart_style']                 = ! empty( $settings['eael_fancy_chart_chart_style'] ) ? $settings['eael_fancy_chart_chart_style'] : '';
		$fancy_chart_settings['eael_chart_style_tooltip_enabel']  = ! empty( $settings['eael_fancy_chart_tooltip_enable'] ) ? $settings['eael_fancy_chart_tooltip_enable'] : '';
		$fancy_chart_settings['eael_fancy_chart_border_radious']  = ! empty( $settings['eael_fancy_chart_border_radious'] ) ? $settings['eael_fancy_chart_border_radious'] : '';
		$fancy_chart_settings['eael_fancy_chart_data_position']   = ! empty( $settings['eael_fancy_chart_data_position'] ) ? $settings['eael_fancy_chart_data_position'] : '';
		$fancy_chart_settings['eael_fancy_chart_legend_position'] = ! empty( $settings['eael_fancy_chart_legend_position'] ) ? $settings['eael_fancy_chart_legend_position'] : '';
		$fancy_chart_settings['eael_fancy_chart_legend_show']     = ! empty( $settings['eael_fancy_chart_legend_show'] ) ? $settings['eael_fancy_chart_legend_show'] : '';
		$fancy_chart_settings['eael_fancy_chart_data_y_label']    = ! empty( $settings['eael_fancy_chart_data_y_label'] ) ? $settings['eael_fancy_chart_data_y_label'] : '';
		$fancy_chart_settings['eael_fancy_chart_data_y_position'] = ! empty( $settings['eael_fancy_chart_data_y_position'] ) ? $settings['eael_fancy_chart_data_y_position'] : '';
		$fancy_chart_settings['eael_fancy_chart_data_x_label']    = ! empty( $settings['eael_fancy_chart_data_x_label'] ) ? $settings['eael_fancy_chart_data_x_label'] : '';
		$fancy_chart_settings['eael_fancy_chart_data_x_position'] = ! empty( $settings['eael_fancy_chart_data_x_position'] ) ? $settings['eael_fancy_chart_data_x_position'] : '';
		$fancy_chart_settings['eael_fancy_chart_type']            = ! empty( $settings['eael_fancy_chart_chart_type'] ) ? $settings['eael_fancy_chart_chart_type'] : '';
		$fancy_chart_settings['eael_chart_width_unit']            = ! empty( $settings['eael_fancy_chart_width']['unit'] ) ? $settings['eael_fancy_chart_width']['unit'] : '';
		$fancy_chart_settings['eael_chart_width']                 = ! empty( $settings['eael_fancy_chart_width']['size'] ) ? $settings['eael_fancy_chart_width']['size'] : '';
		$fancy_chart_settings['eael_chart_height_unit']           = ! empty( $settings['eael_fancy_chart_height']['unit'] ) ? $settings['eael_fancy_chart_height']['unit'] : '';
		$fancy_chart_settings['eael_chart_height']                = ! empty( $settings['eael_fancy_chart_height']['size'] ) ? $settings['eael_fancy_chart_height']['size'] : '';
		$fancy_chart_settings['eael_get_data_type']               = ! empty( $settings['eael_fancy_chart_data_option_type'] ) ? $settings['eael_fancy_chart_data_option_type'] : '';
		$fancy_chart_settings['eael_fancy_chart_animation_speed'] = ! empty( $settings['eael_fancy_chart_animation_speed']['size'] ) ? $settings['eael_fancy_chart_animation_speed']['size'] : '';
		$fancy_chart_settings['eael_fancy_chart_animation_delay'] = ! empty( $settings['eael_fancy_chart_animation_delay']['size'] ) ? $settings['eael_fancy_chart_animation_delay']['size'] : '';
		$fancy_chart_settings['data_label_font_size']             = ! empty( $settings['eael_fancy_chart_data_label_font_size']['size'] ) && ! empty( $settings['eael_fancy_chart_data_label_font_size']['unit'] ) ? $settings['eael_fancy_chart_data_label_font_size']['size'] . $settings['eael_fancy_chart_data_label_font_size']['unit'] : '12px';
		$fancy_chart_settings['data_label_color']                 = ! empty( $settings['eael_fancy_chart_data_label_color'] ) ? $settings['eael_fancy_chart_data_label_color'] : '#fff';
		$fancy_chart_settings['eael_fancy_chart_bar_width']       = ! empty( $settings['eael_fancy_chart_bar_width']['size'] ) ? $settings['eael_fancy_chart_bar_width']['size'] : '';

		return $fancy_chart_settings;
	}

	protected function eael_fancy_chart_new_array_set( $csv_slice_array ) {
		$new_array = [];
		for ( $i = 0; $i < count( (array) $csv_slice_array[0] ); $i ++ ) {
			$sub_array = [];
			for ( $j = 0; $j < count( $csv_slice_array ); $j ++ ) {
				$sub_array[] = $csv_slice_array[ $j ][ $i ];
			}
			$new_array[] = $sub_array;
		}

		return $new_array;
	}

	protected function eael_fancy_chart_dataset( $eael_sheet_category, $fancy_chart_data ) {
		$eael_sheet_data_set = [];
		for ( $i = 0; $i < count( $eael_sheet_category ); $i ++ ) {
			$eael_sheet_data_set[] = array(
				'name' => $eael_sheet_category[ $i ],
				'data' => $fancy_chart_data[ $i ],
			);
		}

		return $eael_sheet_data_set;
	}

	protected function render() {
		$settings             = $this->get_settings_for_display();
		$fancy_chart_settings = $this->get_fancy_chart_settings( $settings );

		//Get manual data
		//Create new category array list  category_data_group_name
		$eael_chart_category_lists = [];
		$new_group_data_lists      = [];
		$eael_group_data_array     = [];
		$eael_group_color_array    = [];
		// $eael_group_color_array    = ['#7385FF', '#A88FF7', '#FC9DD9', '#75C5B1', '#4FC0D0'];

		if ( is_array( $fancy_chart_settings['category_lists'] ) ) {
			foreach ( $fancy_chart_settings['category_lists'] as $value ) {
				$eael_chart_category_lists[] = $value['eael_fancy_chart_category'];
			}
		}

		//Create new data array list
		if ( is_array( $fancy_chart_settings['data_lists'] ) ) {
			foreach ( $fancy_chart_settings['data_lists'] as $value ) {
				$new_group_data_lists[]        = (int) $value['eael_data_chart_list'];
				$eael_get_static_color_array[] = $value['eael_chart_dat_color'];
			}
			$eael_group_color_array = array_values( array_filter( $eael_get_static_color_array, 'strlen' ) );
		}

		//
		if ( is_array( $fancy_chart_settings['group_data_lists'] ) ) {
			foreach ( $fancy_chart_settings['group_data_lists'] as $value ) {
				$eael_group_data_array[]  = [
					'name' => $value['eael_group_data_chart_title'],
					'data' => explode( ",", $value['eael_group_data_lists'] ),
				];
				$eael_group_color_array[] = $value['eael_group_data_list_color'];
			}
		}

		//Get JSON Data
		if ( 'json' === $fancy_chart_settings['eael_get_data_type'] ) {
			$eael_get_json_data        = $settings['eael_fancy_chart_data_option_json'];
			$eael_get_json_single_data = $settings['eael_fancy_chart_data_single_json'];
			//Get JSON group data
			$get_json_data_chunk = '';
			if ( ! empty( $eael_get_json_data ) ) {
				$get_json_data_convert = json_decode( $eael_get_json_data, true );
				$get_json_data_chunk   = array_chunk( $get_json_data_convert, 1 );
			}

			//Get JSON single data
			if ( ! empty( $eael_get_json_single_data ) ) {
				$json_single_data_convert  = json_decode( $eael_get_json_single_data, true );
				$eael_chart_category_lists = $json_single_data_convert['category'];
				foreach ( $json_single_data_convert['dataset'] as $item ) {
					$new_group_data_lists[]       = (int) $item['data'];
					$eael_get_json_single_color[] = $item['color'];
				}
				$eael_group_color_array = array_merge( $eael_get_json_single_color, $eael_group_color_array );
			}
		}

		//CSV Data Set
		if ( 'csv' === $fancy_chart_settings['eael_get_data_type'] ) {
			$eael_group_color_array = [ '#7385FF', '#A88FF7', '#FC9DD9', '#75C5B1', '#4FC0D0' ];
			$eael_get_csv_data      = $settings['eael_fancy_chart_data_option_csv'];

			$eael_csv_group_data   = [];
			$eael_csv_chart_legend = '';
			if ( $eael_get_csv_data !== null ) {
				$eael_csv_data_array   = explode( "\n", trim( $eael_get_csv_data ) );
				$eael_csv_chart_legend = str_getcsv( array_shift( $eael_csv_data_array ) );
				array_shift( $eael_csv_chart_legend );

				//Create new array according to comma separated value
				if ( is_array( $eael_csv_data_array ) ) {
					$result_array = [];
					foreach ( $eael_csv_data_array as $items ) {
						if ( strpos( $items, '#color' ) === 0 ) {
							$explode_color_array    = explode( ',', trim( $items ) );
							$eael_color_array_slice = array_slice( $explode_color_array, 1 );
							$eael_group_color_array = $this->eael_replace_color( $eael_group_color_array, $eael_color_array_slice );
						} else {
							$result_array[] = explode( ',', $items );
						}
					}

					$eael_csv_array_slice = [];
					foreach ( $result_array as $item ) {
						$eael_get_csv_cat[]     = $item[0];
						$eael_csv_array_slice[] = array_slice( $item, 1 );
					}

					//
					$eael_csv_create_new_array = $this->eael_fancy_chart_new_array_set( $eael_csv_array_slice );
					$eael_csv_group_data       = $this->eael_fancy_chart_dataset( $eael_csv_chart_legend, $eael_csv_create_new_array );
				}
			}

			//For single value like Pie, Donut, Polar
			$eael_get_csv_data_single = $settings['eael_fancy_chart_data_single_csv'];
			if ( $eael_get_csv_data_single !== null ) {
				$eael_csv_data_array_single = explode( "\n", trim( $eael_get_csv_data_single ) );
				$eael_get_csv_cat_single    = str_getcsv( array_shift( $eael_csv_data_array_single ) );
				array_shift( $eael_get_csv_cat_single );
				$eael_chart_category_lists = $eael_get_csv_cat_single;

				for ( $i = 0; $i < count( $eael_csv_data_array_single ); $i ++ ) {
					$create_color_array = strrchr( $eael_csv_data_array_single[ $i ], '#' );
					if ( ! empty( $create_color_array ) ) {
						$eael_group_color_array[ $i ] = $create_color_array;
					}
					$new_group_data_lists[] = (int) trim( $eael_csv_data_array_single[ $i ] );
				}
			}
		}

		//Get Google Sheet data
		if ( 'google_sheet' == $fancy_chart_settings['eael_get_data_type'] ) {
			$eael_group_color_array = [ '#7385FF', '#A88FF7', '#FC9DD9', '#75C5B1', '#4FC0D0' ];

			if ( empty( $settings['eael_fancy_chart_api_key'] ) || empty( $settings['eael_fancy_chart_sheet_id'] ) || empty( $settings['eael_fancy_chart_table_range'] ) ) {
				esc_html_e( 'Please insert correct API / Sheet ID', 'essential-addons-elementor' );

				return;
			}

			$arg = [
				'google_sheet_api_key' => $settings['eael_fancy_chart_api_key'],
				'google_sheet_id'      => $settings['eael_fancy_chart_sheet_id'],
				'table_range'          => $settings['eael_fancy_chart_table_range'],
				'cache_time'           => $settings['eael_fancy_chart_sheet_cache_limit'],
			];

			$transient_key = 'eael_fancy_chart_source_google_sheet_' . md5( implode( '', $arg ) );
			$results       = get_transient( $transient_key );
			if ( empty( $results ) || empty( $results['rowData'] ) ) {
				$connection = wp_remote_get( "https://sheets.googleapis.com/v4/spreadsheets/{$settings['eael_fancy_chart_sheet_id']}/?key={$settings['eael_fancy_chart_api_key']}&ranges={$settings['eael_fancy_chart_table_range']}&includeGridData=true", [ 'timeout' => 70 ] );
				if ( ! is_wp_error( $connection ) ) {
					$connection = json_decode( wp_remote_retrieve_body( $connection ), true );
					if ( isset( $connection['sheets'][0]['data'][0]['rowData'] ) ) {
						$results                = [];
						$results['rowData']     = $connection['sheets'][0]['data'][0]['rowData'];
						$results['startRow']    = empty( $connection['sheets'][0]['data'][0]['startRow'] ) ? 0 : $connection['sheets'][0]['data'][0]['startRow'];
						$results['startColumn'] = empty( $connection['sheets'][0]['data'][0]['startColumn'] ) ? 0 : $connection['sheets'][0]['data'][0]['startColumn'];

						set_transient( $transient_key, $results, $settings['eael_fancy_chart_sheet_cache_limit'] * MINUTE_IN_SECONDS );
					}
				}
			}

			$eael_sheet_category   = [];
			$eael_sheet_group_name = [];
			$eael_sheet_data       = [];
			$eael_sheet_group_bg   = [];

			if ( ! empty( $results['rowData'] ) ) {
				foreach ( $results['rowData'] as $key => $results_data ) {
					if ( isset( $results_data['values'] ) ) {
						$result = $results_data['values'];
					}

					if ( $key == 0 ) {
						foreach ( $result as $key => $th ) {
							if ( isset( $th['userEnteredFormat']['backgroundColor'] ) ) {
								$eael_sheet_group_bg[] = $th['userEnteredFormat']['backgroundColor'];
							}
							$catValue = empty( $th['formattedValue'] ) ? '' : $th['formattedValue'];
							if ( $key != 0 ) {
								$eael_sheet_group_name[] = $catValue;
							}
						}
					} else {
						foreach ( $result as $key => $value ) {
							$dataValue = empty( $value['formattedValue'] ) ? '' : $value['formattedValue'];
							if ( $key === 0 ) {
								$eael_sheet_category[] = $dataValue;
							} else {
								$eael_sheet_data[] = [
									'key' => $key,
									'val' => $dataValue,
								];
							}
						}
					}
				}
			}

			$results = [];
			$indinx  = - 1;
			foreach ( $eael_sheet_data as $item ) {
				if ( $item['key'] === 1 ) {
					$indinx ++;
				}
				$results[ $indinx ][] = $item['val'];
				//For pie,donut,polar
				if ( $item['key'] === 1 ) {
					$new_group_data_lists[ $indinx ] = (int) $item['val'];
				}
			}
			$eael_sheet_new_array = $this->eael_fancy_chart_new_array_set( $results );
			$eael_sheet_data_set  = $this->eael_fancy_chart_dataset( $eael_sheet_group_name, $eael_sheet_new_array );

			//Create color array
			if ( ! empty( $eael_sheet_group_bg ) ) {
				foreach ( $eael_sheet_group_bg as $colors ) {
					$red_value   = ! empty( $colors['red'] ) ? $colors['red'] : 0;
					$green_value = ! empty( $colors['green'] ) ? $colors['green'] : 0;
					$blue_value  = ! empty( $colors['blue'] ) ? $colors['blue'] : 0;

					$red        = $red_value * 255;
					$green      = $green_value * 255;
					$blue       = $blue_value * 255;
					$hexColor[] = sprintf( "#%02X%02X%02X", $red, $green, $blue );
				}
				$eael_group_color_array = array_merge( $hexColor, $eael_group_color_array );
			}

			//For pie,donut,polar
			$eael_sheet_single_style = [ 'pie', 'donut', 'polarArea' ];
			if ( in_array( $settings['eael_fancy_chart_chart_style'], $eael_sheet_single_style ) ) {
				$eael_chart_category_lists = $eael_sheet_category;
				$eael_group_color_array    = $eael_group_color_array;
			}
		}

		//
		$eael_chart_data_set     = [];
		$eael_chart_data_cat_set = [];
		switch ( $fancy_chart_settings['eael_get_data_type'] ) {
			case 'manual':
				$eael_chart_data_set     = $eael_group_data_array;
				$eael_chart_data_cat_set = $eael_chart_category_lists;
				break;
			case 'json':
				$eael_chart_data_set     = ( ! empty( $get_json_data_chunk ) ) ? $get_json_data_chunk[1][0] : '';
				$eael_chart_data_cat_set = ( ! empty( $get_json_data_chunk ) ) ? $get_json_data_chunk[0][0] : '';
				break;
			case 'csv':
				$eael_chart_data_set     = $eael_csv_group_data;
				$eael_chart_data_cat_set = $eael_get_csv_cat;
				break;
			case 'google_sheet':
				$eael_chart_data_set     = $eael_sheet_data_set;
				$eael_chart_data_cat_set = $eael_sheet_category;
				break;
		}

		//
		$eael_dafault_data_attrs = [
			'eael_chart_data_set'       => $eael_chart_data_set,
			'eael_chart_data_cat_set'   => $eael_chart_data_cat_set,
			'eael_group_color_array'    => $eael_group_color_array,
			'new_group_data_lists'      => $new_group_data_lists,
			'eael_chart_category_lists' => $eael_chart_category_lists,
		];
		switch ( $fancy_chart_settings['eael_chart_style'] ) {
			case 'bar':
				$data_options = $this->get_chart_style_bar_options( $settings, $eael_dafault_data_attrs );
				break;
			case 'area':
				$data_options = $this->get_chart_style_area_options( $settings, $eael_dafault_data_attrs );
				break;
			case 'line':
				$data_options = $this->get_chart_style_line_options( $settings, $eael_dafault_data_attrs );
				break;
			case 'radar':
				$data_options = $this->get_chart_style_radar_options( $settings, $eael_dafault_data_attrs );
				break;
			case 'pie':
				$data_options = $this->get_chart_style_pie_options( $settings, $eael_dafault_data_attrs );
				break;
			case 'donut':
				$data_options = $this->get_chart_style_donut_options( $settings, $eael_dafault_data_attrs );
				break;
			case 'polarArea':
				$data_options = $this->get_chart_style_polararea_options( $settings, $eael_dafault_data_attrs );
				break;
		}

		$json_data_options = json_encode( $data_options );

		//
		$this->add_render_attribute( 'eael-fancy-chart', [
			'data-options' => $json_data_options,
			'class'        => 'eael_fanct_chart_wrapper',
		] );

		//
		$widget_id = $this->get_id();
		?>
		<div <?php $this->print_render_attribute_string( 'eael-fancy-chart' ); ?>>
			<div class="eael_fancy_chart_header">
				<?php if ( ! empty( $settings['eael_fancy_chart_title'] ) ) { ?>
				<<?php echo Helper::eael_validate_html_tag( $settings['eael_fancy_chart_title_tag'] ); ?> class="eael_fancy_chart_title">
				<?php echo Helper::eael_wp_kses( $settings['eael_fancy_chart_title'] ); ?>
			</<?php echo Helper::eael_validate_html_tag( $settings['eael_fancy_chart_title_tag'] ); ?>>
			<?php
			}

			if ( ! empty( $settings['eael_fancy_chart_des'] ) ) { ?>
				<p>
					<?php echo Helper::eael_wp_kses( $settings['eael_fancy_chart_des'] ); ?>
				</p>
			<?php } ?>
		</div>
		<div class="eael_fancy_chart" id="eael_fancy_chart-<?php echo $widget_id; ?>"></div>
		</div>
		<?php
	}

}
