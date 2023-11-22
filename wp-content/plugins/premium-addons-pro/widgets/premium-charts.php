<?php
/**
 * Class: Premium_Charts
 * Name: Charts
 * Slug: premium-chart
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Charts
 */
class Premium_Charts extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-chart';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Charts', 'premium-addons-pro' );
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
		return 'pa-pro-charts';
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
		return array( 'pa', 'premium', 'data', 'graph', 'bar', 'circle', 'dynamic', 'statistic' );
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
			'pa-charts',
			'elementor-waypoints',
			'premium-pro',
		);
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
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://www.youtube.com/watch?v=lZZvslQ2UYU&list=PLLpZVOYpMtTArB4hrlpSnDJB36D2sdoTv';
	}

	/**
	 * Register Charts controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'general_settings',
			array(
				'label' => __( 'Premium Charts', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'data_source',
			array(
				'label'              => __( 'Data Source', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'custom',
				'options'            => array(
					'custom' => __( 'Custom', 'premium-addons-pro' ),
					'csv'    => 'CSV' . __( ' File', 'premium-addons-pro' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'type',
			array(
				'label'              => __( 'Layout', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'line'          => __( 'Line', 'premium-addons-pro' ),
					'bar'           => __( 'Bar', 'premium-addons-pro' ),
					'horizontalBar' => __( 'Horizontal Bar', 'premium-addons-pro' ),
					'pie'           => __( 'Pie', 'premium-addons-pro' ),
					'radar'         => __( 'Radar', 'premium-addons-pro' ),
					'doughnut'      => __( 'Doughnut', 'premium-addons-pro' ),
					'polarArea'     => __( 'Polar Area', 'premium-addons-pro' ),

				),
				'default'            => 'bar',
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'csv_type',
			array(
				'label'     => __( 'File Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'file' => __( 'Upload FIle', 'premium-addons-pro' ),
					'url'  => __( 'Remote File', 'premium-addons-pro' ),
				),
				'condition' => array(
					'data_source' => 'csv',
				),
				'default'   => 'file',
			)
		);

		$this->add_control(
			'premium_chart_separator',
			array(
				'label'       => __( 'Data Separator', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Separator between cells data', 'premium-addons-pro' ),
				'label_block' => true,
				'default'     => ',',
				'condition'   => array(
					'data_source' => 'csv',
				),
			)
		);

		$this->add_control(
			'csv_file',
			array(
				'label'      => __( 'Upload CSV File', 'premium-addons-pro' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array( 'active' => true ),
				'media_type' => array(),
				'condition'  => array(
					'data_source' => 'csv',
					'csv_type'    => 'file',
				),
			)
		);

		$this->add_control(
			'csv_url',
			array(
				'label'       => __( 'File URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'data_source' => 'csv',
					'csv_type'    => 'url',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'x_axis',
			array(
				'label' => __( 'X-Axis', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'x_axis_label_switch',
			array(
				'label'              => __( 'Show Axis Label', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Show or Hide X-Axis Label', 'premium-addons-pro' ),
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_label',
			array(
				'label'              => __( 'Label', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => array( 'active' => true ),
				'default'            => 'X-Axis',
				'label_block'        => true,
				'condition'          => array(
					'x_axis_label_switch' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_labels',
			array(
				'label'              => __( 'Data Labels', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => array( 'active' => true ),
				'default'            => 'Jan,Feb,Mar,Apr,May',
				'description'        => __( 'Enter labels for X-Axis separated with \' , \' ', 'premium-addons-pro' ),
				'label_block'        => true,
				'condition'          => array(
					'data_source' => 'custom',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_grid',
			array(
				'label'              => __( 'Show Grid Lines', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'default'            => 'true',
				'description'        => __( 'Show or Hide X-Axis Grid Lines', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_begin',
			array(
				'label'              => __( 'Begin at Zero', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'true',
				'description'        => __( 'Start X-Axis Labels at zero', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_label_rotation',
			array(
				'label'              => __( 'Labels\' Rotation ', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 360,
				'default'            => 0,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_column_width',
			array(
				'label'              => __( 'Column Width', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'condition'          => array(
					'type' => 'bar',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'y_axis',
			array(
				'label' => __( 'Y-Axis', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'y_axis_label_switch',
			array(
				'label'              => __( 'Show Axis Label', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Show or Hide Y-Axis Label', 'premium-addons-pro' ),
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_label',
			array(
				'label'              => __( 'Label', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => array( 'active' => true ),
				'default'            => 'Y-Axis',
				'label_block'        => true,
				'condition'          => array(
					'y_axis_label_switch' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$data_repeater = new REPEATER();

		$data_repeater->add_control(
			'y_axis_column_title',
			array(
				'label'   => __( 'Title', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$data_repeater->add_control(
			'y_axis_column_data',
			array(
				'label'       => __( 'Data', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter Data Numbers for Y-Axis separated with \' , \' ', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),

			)
		);

		$data_repeater->add_control(
			'y_axis_urls',
			array(
				'label'       => __( 'URLs', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter URLs for each Dataset separated with \' , \' ', 'premium-addons-pro' ),
				'label_block' => true,
			)
		);

		$data_repeater->add_control(
			'fill_colors_notice',
			array(
				'raw'             => '<strong>' . __( 'Please note!', 'premium-addons-pro' ) . '</strong> ' . __( 'First/Second Fill Color options used together to add a gradient for all charts except Pie, Dounut and Polar Area, Fill Colors option used to add multiple colors, but please make sure First/Second Color options are cleared.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			)
		);

		$data_repeater->add_control(
			'y_axis_column_color',
			array(
				'label' => __( 'First Fill Color', 'premium-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$data_repeater->add_control(
			'y_axis_column_second_color',
			array(
				'label' => __( 'Second Fill Color', 'premium-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$data_repeater->add_control(
			'y_axis_circle_color',
			array(
				'label'       => __( 'Fill Colors', 'premium-addons-pro' ),
				'description' => __( 'Enter Colors separated with \' , \', this will work only for pie, doughnut and polar area charts ', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '#ff6384,#4bc0c0,#ffcd56,#c9cbcf,#36a2eb',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$data_repeater->add_control(
			'y_axis_column_border_width',
			array(
				'label'   => __( 'Border Width', 'premium-addons-pro' ),
				'default' => 1,
				'type'    => Controls_Manager::NUMBER,
			)
		);

		$data_repeater->add_control(
			'y_axis_column_border_color',
			array(
				'label'   => __( 'Border Color', 'premium-addons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#fff',
			)
		);

		$this->add_control(
			'y_axis_data',
			array(
				'label'     => __( 'Data', 'premium-addons-pro' ),
				'type'      => Controls_Manager::REPEATER,
				'default'   => array(
					array(
						'y_axis_column_title' => __( 'Dataset #1', 'premium-addons-pro' ),
						'y_axis_column_data'  => '1,5,2,3,7',
						'y_axis_column_color' => '#6ec1e4',
					),
					array(
						'y_axis_column_title' => __( 'Dataset #2', 'premium-addons-pro' ),
						'y_axis_column_data'  => '2,10,1,5,4',
						'y_axis_column_color' => '#54595F',

					),
				),
				'fields'    => $data_repeater->get_controls(),
				'condition' => array(
					'data_source' => 'custom',
				),
			)
		);

		$csv_repeater = new REPEATER();

		$csv_repeater->add_control(
			'dataset_title',
			array(
				'label'   => __( 'Title', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$csv_repeater->add_control(
			'dataset_color',
			array(
				'label' => __( 'Fill Color', 'premium-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$csv_repeater->add_control(
			'circle_color',
			array(
				'label'       => __( 'Fill Colors', 'premium-addons-pro' ),
				'description' => __( 'Enter Colors separated with \' , \', this will work only for pie, doughnut and polar area charts ', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '#ff6384,#4bc0c0,#ffcd56,#c9cbcf,#36a2eb',
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$csv_repeater->add_control(
			'border_width',
			array(
				'label'   => __( 'Border Width', 'premium-addons-pro' ),
				'default' => 1,
				'type'    => Controls_Manager::NUMBER,
			)
		);

		$csv_repeater->add_control(
			'dataset_border_color',
			array(
				'label'   => __( 'Border Color', 'premium-addons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#fff',
			)
		);

		$this->add_control(
			'dataset_repeater',
			array(
				'label'     => __( 'Data', 'premium-addons-pro' ),
				'type'      => Controls_Manager::REPEATER,
				'default'   => array(
					array(
						'dataset_title' => __( 'Dataset #1', 'premium-addons-pro' ),
						'dataset_color' => '#6ec1e4',
					),
					array(
						'dataset_title' => __( 'Dataset #2', 'premium-addons-pro' ),
						'dataset_color' => '#54595F',

					),
				),
				'fields'    => $csv_repeater->get_controls(),
				'condition' => array(
					'data_source' => 'csv',
				),
			)
		);

		$this->add_control(
			'data_type',
			array(
				'label'              => __( 'Data Type', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'linear'      => __( 'Linear', 'premium-addons-pro' ),
					'logarithmic' => __( 'Logarithmic', 'premium-addons-pro' ),
				),
				'default'            => 'linear',
				'condition'          => array(
					'type!' => 'horizontalBar',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_grid',
			array(
				'label'              => __( 'Show Grid Lines', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'default'            => 'true',
				'description'        => __( 'Show or Hide Y-Axis Grid Lines', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_begin',
			array(
				'label'              => __( 'Begin at Zero', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'true',
				'return_value'       => 'true',
				'description'        => __( 'Start Y-Axis Data at zero', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_urls_target',
			array(
				'label'              => __( 'Open Links in new tab', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'default'            => 'true',
				'condition'          => array(
					'data_source' => 'custom',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_content',
			array(
				'label' => __( 'Title', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'title_switcher',
			array(
				'label'        => __( 'Title', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter a Title for the Chart', 'premium-addons-pro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'title_switcher' => 'true',
				),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'label_block' => true,
				'condition'   => array(
					'title_switcher' => 'true',
				),
			)
		);

		$this->add_control(
			'title_position',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
				),
				'default'   => 'top',
				'condition' => array(
					'title_switcher' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'title_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
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
				'selectors' => array(
					'{{WRAPPER}} .premium-chart-title' => 'text-align: {{VALUE}}',
				),
				'default'   => 'center',
				'condition' => array(
					'title_switcher' => 'true',
				),
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
			'y_axis_min',
			array(
				'label'              => __( 'Minimum Value', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'Set Y-axis minimum value, this will be overriden if data has a smaller value or Begin At Zero option is enabled', 'premium-addons-pro' ),
				'condition'          => array(
					'type!' => array( 'pie', 'doughnut', 'radar', 'polarArea' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_max',
			array(
				'label'              => __( 'Maximum Value', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'Set Y-axis maximum value, this will be overriden if data has a larger value', 'premium-addons-pro' ),
				'min'                => 0,
				'default'            => 1,
				'condition'          => array(
					'type!' => array( 'pie', 'doughnut' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'step_size',
			array(
				'label'              => __( 'Step Size', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'condition'          => array(
					'type!' => array( 'pie', 'doughnut' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'legend_display',
			array(
				'label'              => __( 'Show Legend', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Show or Hide chart legend', 'premium-addons-pro' ),
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'legend_circle',
			array(
				'label'              => __( 'Change Legend to Circles', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'true',
				'condition'          => array(
					'legend_display' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'legend_hide',
			array(
				'label'              => __( 'Hide Legend on Tablet/Mobile Devices', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Show or Hide chart legend', 'premium-addons-pro' ),
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'condition'          => array(
					'legend_display' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'legend_position',
			array(
				'label'              => __( 'Legend Position', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'right'  => __( 'Right', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
					'left'   => __( 'Left', 'premium-addons-pro' ),
				),
				'default'            => 'top',
				'tablet_default'     => 'top',
				'mobile_default'     => 'top',
				'condition'          => array(
					'legend_display' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'legend_reverse',
			array(
				'label'              => __( 'Reverse', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enable or Disable legend data reverse', 'premium-addons-pro' ),
				'return_value'       => 'true',
				'condition'          => array(
					'legend_display' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tool_tips',
			array(
				'label'              => __( 'Show Values on Hover', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tool_tips_percent',
			array(
				'label'              => __( 'Convert Values to percent', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'true',
				'condition'          => array(
					'tool_tips' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tool_tips_mode',
			array(
				'label'              => __( 'Mode', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'point'   => __( 'Point', 'premium-addons-pro' ),
					'nearest' => __( 'Nearest', 'premium-addons-pro' ),
					'dataset' => __( 'Dataset', 'premium-addons-pro' ),
					'x'       => __( 'X', 'premium-addons-pro' ),
					'y'       => __( 'Y', 'premium-addons-pro' ),
				),
				'default'            => 'nearest',
				'condition'          => array(
					'tool_tips' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'value_on_chart',
			array(
				'label'              => __( 'Show Values on Chart', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'This option works only with Pie and Douhnut Charts', 'premium-addons-pro' ),
				'label_on'           => 'Show',
				'label_off'          => 'Hide',
				'return_value'       => 'true',
				'condition'          => array(
					'type'       => array( 'pie', 'doughnut' ),
					'tool_tips!' => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'              => __( 'Animation Duration (msec)', 'premium-addons-pro' ),
				'description'        => __( 'Set the animation duration in milliseconds', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'start_animation',
			array(
				'label'              => __( 'Animation', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'linear'           => __( 'Linear', 'premium-addons-pro' ),
					'easeInQuad'       => __( 'Ease in Quad', 'premium-addons-pro' ),
					'easeOutQuad'      => __( 'Ease out Quad', 'premium-addons-pro' ),
					'easeInOutQuad'    => __( 'Ease in out Quad', 'premium-addons-pro' ),
					'easeInCubic'      => __( 'Ease in Cubic', 'premium-addons-pro' ),
					'easeOutCubic'     => __( 'Ease out Cubic', 'premium-addons-pro' ),
					'easeInOutCubic'   => __( 'Ease in out Cubic', 'premium-addons-pro' ),
					'easeInQuart'      => __( 'Ease in Quart', 'premium-addons-pro' ),
					'easeOutQuart'     => __( 'Ease out Quart', 'premium-addons-pro' ),
					'easeInOutQuart'   => __( 'Ease in out Quart', 'premium-addons-pro' ),
					'easeInQuint'      => __( 'Ease in Quint', 'premium-addons-pro' ),
					'easeOutQuint'     => __( 'Ease out Quint', 'premium-addons-pro' ),
					'easeInOutQuint'   => __( 'Ease in out Quint', 'premium-addons-pro' ),
					'easeInSine'       => __( 'Ease in Sine', 'premium-addons-pro' ),
					'easeOutSine'      => __( 'Ease out Sine', 'premium-addons-pro' ),
					'easeInOutSine'    => __( 'Ease in out Sine', 'premium-addons-pro' ),
					'easeInExpo'       => __( 'Ease in Expo', 'premium-addons-pro' ),
					'easeOutExpo'      => __( 'Ease out Expo', 'premium-addons-pro' ),
					'easeInOutExpo'    => __( 'Ease in out Cubic', 'premium-addons-pro' ),
					'easeInCirc'       => __( 'Ease in Circle', 'premium-addons-pro' ),
					'easeOutCirc'      => __( 'Ease out Circle', 'premium-addons-pro' ),
					'easeInOutCirc'    => __( 'Ease in out Circle', 'premium-addons-pro' ),
					'easeInElastic'    => __( 'Ease in Elastic', 'premium-addons-pro' ),
					'easeOutElastic'   => __( 'Ease out Elastic', 'premium-addons-pro' ),
					'easeInOutElastic' => __( 'Ease in out Elastic', 'premium-addons-pro' ),
					'easeInBack'       => __( 'Ease in Back', 'premium-addons-pro' ),
					'easeOutBack'      => __( 'Ease out Back', 'premium-addons-pro' ),
					'easeInOutBack'    => __( 'Ease in Out Back', 'premium-addons-pro' ),
					'easeInBounce'     => __( 'Ease in Bounce', 'premium-addons-pro' ),
					'easeOutBounce'    => __( 'Ease out Bounce', 'premium-addons-pro' ),
					'easeInOutBounce'  => __( 'Ease in out Bounce', 'premium-addons-pro' ),
				),
				'default'            => 'easeInQuad',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'render_event',
			array(
				'label'              => __( 'Load Chart On', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'scroll' => __( 'Scroll', 'premium-addons-pro' ),
					'load'   => __( 'Page Load', 'premium-addons-pro' ),
				),
				'default'            => 'scroll',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'format_locale',
			array(
				'label'              => __( 'Data Format Locale', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'description'        => __( 'Use this to format strings into specific locale format. For example, use de to format numbers according to German formatting.', 'premium-addons-pro' ),
				'default'            => '',
				'options'            => array(
					''   => __( 'Default', 'premium-addons-pro' ),
					'en' => __( 'English', 'premium-addons-pro' ),
					'fr' => __( 'French', 'premium-addons-pro' ),
					'da' => __( 'Danish', 'premium-addons-pro' ),
					'de' => __( 'German', 'premium-addons-pro' ),
					'ar' => __( 'Arabic', 'premium-addons-pro' ),
				),
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'general_style',
			array(
				'label' => __( 'General', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'       => __( 'Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the height of the graph in pixels', 'premium-addons-pro' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-chart-canvas-container'   => 'height: {{VALUE}}px',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'general_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-chart-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'general_border',
				'selector' => '{{WRAPPER}} .premium-chart-container',
			)
		);

		$this->add_control(
			'general_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-chart-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'general_box_shadow',
				'selector' => '{{WRAPPER}} .premium-chart-container',
			)
		);

		$this->add_responsive_control(
			'general_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-chart-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'general_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-chart-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'title_switcher' => 'true',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-chart-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-chart-title',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'title_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-chart-title-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'title_border',
				'selector' => '{{WRAPPER}} .premium-chart-title-container',
			)
		);

		$this->add_control(
			'title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-chart-title-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_box_shadow',
				'selector' => '{{WRAPPER}} .premium-chart-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-chart-title-container .premium-chart-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-chart-title-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'x_axis_style',
			array(
				'label' => __( 'X-Axis', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'x_axis_label_pop',
			array(
				'label'     => __( 'Axis Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'x_axis_label_switch' => 'true',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'x_axis_label_color',
			array(
				'label'              => __( 'Color', 'premium-addons-pro' ),
				'type'               => Controls_Manager::COLOR,
				'global'             => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_label_size',
			array(
				'label'              => __( 'Size', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 50,
				'default'            => 12,
				'frontend_available' => true,
			)
		);

		$this->end_popover();

		$this->add_control(
			'x_axis_labels_pop',
			array(
				'label' => __( 'Data Labels', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'x_axis_labels_color',
			array(
				'label'              => __( 'Color', 'premium-addons-pro' ),
				'type'               => Controls_Manager::COLOR,
				'global'             => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_labels_size',
			array(
				'label'              => __( 'Size', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 50,
				'default'            => 12,
				'frontend_available' => true,
			)
		);

		$this->end_popover();

		$this->add_control(
			'x_axis_grid_pop',
			array(
				'label' => __( 'Grid', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'x_axis_grid_color',
			array(
				'label'              => __( 'Color', 'premium-addons-pro' ),
				'type'               => Controls_Manager::COLOR,
				'default'            => '#6ec1e4',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'x_axis_grid_width',
			array(
				'label'              => __( 'Width', 'premium-charts' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 1,
				),
				'frontend_available' => true,
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'y_axis_style',
			array(
				'label' => __( 'Y-Axis', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'y_axis_label_pop',
			array(
				'label'     => __( 'Axis Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'y_axis_label_switch' => 'true',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'y_axis_label_color',
			array(
				'label'              => __( 'Color', 'premium-addons-pro' ),
				'type'               => Controls_Manager::COLOR,
				'global'             => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_label_size',
			array(
				'label'              => __( 'Size', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 50,
				'default'            => 12,
				'frontend_available' => true,
			)
		);

		$this->end_popover();

		$this->add_control(
			'y_axis_data_pop',
			array(
				'label' => __( 'Data', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'y_axis_labels_color',
			array(
				'label'              => __( 'Color', 'premium-addons-pro' ),
				'type'               => Controls_Manager::COLOR,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_labels_size',
			array(
				'label'              => __( 'Size', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 50,
				'default'            => 12,
				'frontend_available' => true,
			)
		);

		$this->end_popover();

		$this->add_control(
			'y_axis_grid_pop',
			array(
				'label' => __( 'Grid', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'y_axis_grid_color',
			array(
				'label'              => __( 'Color', 'premium-addons-pro' ),
				'type'               => Controls_Manager::COLOR,
				'default'            => '#54595f',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'y_axis_grid_width',
			array(
				'label'              => __( 'Width', 'premium-charts' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 1,
				),
				'frontend_available' => true,
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'legend_style',
			array(
				'label'     => __( 'Legend', 'premium-charts' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'legend_display' => 'true',
				),
			)
		);

		$this->add_control(
			'legend_text_color',
			array(
				'label'              => __( 'Color', 'premium-charts' ),
				'type'               => Controls_Manager::COLOR,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'legend_text_size',
			array(
				'label'              => __( 'Size', 'premium-charts' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 0,
				'max'                => 50,
				'default'            => 12,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'legend_item_width',
			array(
				'label'              => __( 'Item Width', 'premium-charts' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'default'            => 40,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Charts widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$title_tag = PAPRO_Helper::validate_html_tag( $settings['title_tag'] );
		if ( ! empty( $settings['title'] ) && $settings['title_switcher'] ) {
			$title = '<' . $title_tag . ' class="premium-chart-title">' . $settings['title'] . '</' . $title_tag . '>';
		}

		$data_source   = $settings['data_source'];
		$columns_array = array();

		if ( 'csv' === $data_source ) {
			$props = array();
			foreach ( $settings['dataset_repeater'] as $repeater_ele ) {
				if ( 'pie' === $settings['type'] || 'doughnut' === $settings['type'] || 'polarArea' === $settings['type'] ) {
					$bg_color = explode( ',', $repeater_ele['circle_color'] );
				} else {
					$bg_color = $repeater_ele['dataset_color'];
				}

				$prop = array(
					'backgroundColor' => $bg_color,
					'borderColor'     => $repeater_ele['dataset_border_color'],
					'borderWidth'     => $repeater_ele['border_width'],
					'title'           => $repeater_ele['dataset_title'],
				);

				array_push( $props, $prop );
			}

			$col_settings = array(
				'separator' => $settings['premium_chart_separator'],
				'url'       => 'file' === $settings['csv_type'] ? $settings['csv_file']['url'] : $settings['csv_url'],
				'props'     => $props,
			);

			$columns_array = array_merge( $columns_array, $col_settings );
		} else {

			foreach ( $settings['y_axis_data'] as $column_data ) {

				if ( 'pie' !== $settings['type'] && 'doughnut' !== $settings['type'] && 'polarArea' !== $settings['type'] ) {
					if ( empty( $column_data['y_axis_column_color'] ) && empty( $column_data['y_axis_column_second_color'] ) ) {
						$background = explode( ',', $column_data['y_axis_circle_color'] );
						array_push( $background, 'empty' );
					} elseif ( ! empty( $column_data['y_axis_column_second_color'] ) ) {
						$background = array( $column_data['y_axis_column_color'], $column_data['y_axis_column_second_color'] );
					} else {
						$background = $column_data['y_axis_column_color'];
					}
				} else {
					$background = explode( ',', $column_data['y_axis_circle_color'] );
				}

				$col_settings = array(
					'label'           => $column_data['y_axis_column_title'],
					'data'            => explode( ',', $column_data['y_axis_column_data'] ),
					'links'           => explode( ',', $column_data['y_axis_urls'] ),
					'backgroundColor' => $background,
					'borderColor'     => $column_data['y_axis_column_border_color'],
					'borderWidth'     => $column_data['y_axis_column_border_width'],
				);

				array_push( $columns_array, $col_settings );
			}
		}

		$chart_id = 'premium-chart-canvas-' . $id;

		$chart_settings = array(
			'chartId' => $chart_id,
			'height'  => ! empty( $settings['height'] ) ? $settings['height'] : 400,
		);

		$this->add_render_attribute(
			'charts',
			array(
				'id'            => 'premium-chart-container-' . $id,
				'class'         => 'premium-chart-container',
				'data-chart'    => wp_json_encode( $columns_array ),
				'data-settings' => wp_json_encode( $chart_settings ),
			)
		);

		$this->add_render_attribute(
			'canvas',
			array(
				'id'     => 'premium-chart-canvas-' . $id,
				'class'  => 'premium-chart-canvas',
				'width'  => 400,
				'height' => 400,
			)
		);

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'charts' ) ); ?>>
		<?php if ( ! empty( $settings['title'] ) && $settings['title_switcher'] && 'top' === $settings['title_position'] ) : ?>
			<div class="premium-chart-title-container"><?php echo wp_kses_post( $title ); ?></div>
		<?php endif; ?>
		<div class="premium-chart-canvas-container">
			<canvas <?php echo wp_kses_post( $this->get_render_attribute_string( 'canvas' ) ); ?>></canvas>
		</div>
		<?php if ( ! empty( $settings['title'] ) && $settings['title_switcher'] && 'bottom' === $settings['title_position'] ) : ?>
			<div class="premium-chart-title-container"><?php echo wp_kses_post( $title ); ?></div>
		<?php endif; ?>
	</div>

		<?php
	}

}
