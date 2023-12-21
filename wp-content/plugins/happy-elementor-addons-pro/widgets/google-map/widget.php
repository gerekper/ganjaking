<?php
/**
 * Gmap widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || die();
class Google_map extends Base {



	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */

	public function get_title() {
		return __( 'Advanced Google Map', 'happy-addons-pro' );
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
		return 'hm hm-map-marker';
	}

	public function get_keywords() {
		return ['map', 'google map', 'google', 'gmap'];
	}

	protected function register_content_controls() {
		$this->center_location();
		$this->coordinate_settings();
		$this->routes_settings();
		$this->map_settings();
		$this->marker_settings();
		$this->legend_settings();
	}

	protected function center_location() {
		$google_credentials = ha_get_credentials( 'google_map' );
		$gm_api_key         = is_array( $google_credentials ) ? $google_credentials['api_key'] : '';

		$this->start_controls_section(
			'section_center_location',
			[
				'label' => __( 'General Settings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		if ( empty( $gm_api_key ) ) {
			$this->add_control(
				'credentials_set_notice',
				[
					'raw'             => '<strong>' . esc_html__( 'Note!', 'happy-addons-pro' ) . '</strong> ' . esc_html__( 'Please set credentials in Happy Addons Dashboard - ', 'happy-addons-pro' ) . '<a style="border-bottom-color: inherit;" href="' . esc_url( admin_url( 'admin.php?page=happy-addons#credentials' ) ) . '" target="_blank" >' . esc_html__( 'Credentials', 'happy-addons-pro' ) . '</a>',
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type'     => 'ui',
				]
			);
		}

		$this->add_control(
			'google_map_type',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Google Map Type', 'happy-addons-pro' ),
				'default' => 'basic',
				'options' => [
					'basic'    => __( 'Basic', 'happy-addons-pro' ),
					'polyline' => __( 'Polyline', 'happy-addons-pro' ),
					'polygon'  => __( 'Polygon', 'happy-addons-pro' ),
					'routes'   => __( 'Routes', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'latitude',
			[
				'type'               => Controls_Manager::TEXT,
				'label'              => __( 'Latitude', 'happy-addons-pro' ),
				'default'            => '40.7306',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'longitude',
			[
				'type'               => Controls_Manager::TEXT,
				'label'              => __( 'Longitude', 'happy-addons-pro' ),
				'default'            => '-73.9352',
				'frontend_available' => false,
			]
		);

		$this->end_controls_section();
	}

	protected function map_settings() {
		$this->start_controls_section(
			'section_map_setting',
			[
				'label' => __( 'Map Settings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'map_type',
			[
				'type'               => Controls_Manager::SELECT,
				'label'              => __( 'Map type', 'happy-addons-pro' ),
				'default'            => 'roadmap',
				'options'            => [
					'roadmap'   => __( 'Road Map', 'happy-addons-pro' ),
					'satellite' => __( 'Satellite', 'happy-addons-pro' ),
					'terrain'   => __( 'Terrain', 'happy-addons-pro' ),
					'hybrid'    => __( 'Hybrid', 'happy-addons-pro' ),
				],
				'frontend_available' => false,
			]
		);

		$this->add_control(
			'map_type_set_notice',
			[
				'raw'             => __( '<strong>Satellite</strong> and <strong>Hybrid</strong> map type may not work on zoom level greater than 12 on all locations', 'happy-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type'     => 'ui',
				'condition'       => [
					'map_type' => ['satellite', 'hybrid'],
				],
			]
		);

		$this->add_responsive_control(
			'map_zoom',
			[
				'type'               => Controls_Manager::SLIDER,
				'label'              => __( 'Zoom', 'happy-addons-pro' ),
				'default'            => [
					'size' => 8,
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => [
					'size' => 8,
				],
				'desktop_default' => [
					'size' => 8,
				],
				'tablet_default' => [
					'size' => 10,
				],
				'mobile_default' => [
					'size' => 12,
				],
			]
		);

		$this->add_control(
			'scroll_wheel_zoom',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Scroll wheel zoom', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'info_open',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Info container always opened', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'info_open_hover',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Open info container on Hover', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'zoom_control',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Zoom controls', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'full_screen',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Full screen control', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'disable_map_drag',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Disable map drag', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'show_legend',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Show legend', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
				'separator'          => 'before',
			]
		);
		// $this->add_control(
		// 	'show_route',
		// 	[
		// 		'type'               => Controls_Manager::SWITCHER,
		// 		'label'              => __( 'Show Route', 'happy-addons-pro' ),
		// 		'return_value'       => 'yes',
		// 		'frontend_available' => false,
		// 		'separator'          => 'after',
		// 	]
		// );
		$this->add_control(
			'map_type_control',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Map type control', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'street_view',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Street view control', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);

		$this->add_control(
			'hide_logo',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Hide logo', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
				'separator'          => 'before',
			]
		);
		$this->add_control(
			'hide_copyright',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Hide copyright', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'hide_tou',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Hide terms of use', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'hide_report',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Hide report a map error', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);
		$this->add_control(
			'hide_keyboard',
			[
				'type'               => Controls_Manager::SWITCHER,
				'label'              => __( 'Hide keyboard shortcuts', 'happy-addons-pro' ),
				'return_value'       => 'yes',
				'frontend_available' => false,
			]
		);

		$this->end_controls_section();
	}

	protected function coordinate_settings() {
		$this->start_controls_section(
			'section_coordinate_settings',
			[
				'label'     => __( 'Coordinate Settings', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'google_map_type' => ['polyline', 'polygon'],
				],
			]
		);

		$polylines_repeater = new Repeater();

		$polylines_repeater->add_control(
			'ha_gmap_polyline_title',
			[
				'label'       => esc_html__( 'Title', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( '#', 'happy-addons-pro' ),
			]
		);

		$polylines_repeater->add_control(
			'ha_gmap_polyline_lat',
			[
				'label'       => esc_html__( 'Latitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$polylines_repeater->add_control(
			'ha_gmap_polyline_lng',
			[
				'label'       => esc_html__( 'Longitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'ha_gmap_polylines',
			[
				'type'        => Controls_Manager::REPEATER,
				'seperator'   => 'before',
				'default'     => [
					[
						'ha_gmap_polyline_title' => esc_html__( '#1', 'happy-addons-pro' ),
						'ha_gmap_polyline_lat'   => '-12.040397656836609',
						'ha_gmap_polyline_lng'   => '-77.03373871559225',
					],
					[
						'ha_gmap_polyline_title' => esc_html__( '#2', 'happy-addons-pro' ),
						'ha_gmap_polyline_lat'   => '-12.040248585302038',
						'ha_gmap_polyline_lng'   => '-77.03993927003302',
					],
					[
						'ha_gmap_polyline_title' => esc_html__( '#3', 'happy-addons-pro' ),
						'ha_gmap_polyline_lat'   => '-12.050047116528843',
						'ha_gmap_polyline_lng'   => '-77.02448169303511',
					],
					[
						'ha_gmap_polyline_title' => esc_html__( '#4', 'happy-addons-pro' ),
						'ha_gmap_polyline_lat'   => '-12.044804866577001',
						'ha_gmap_polyline_lng'   => '-77.02154422636042',
					],
				],
				'fields'      => $polylines_repeater->get_controls(),
				'title_field' => '{{ha_gmap_polyline_title}}',
			]
		);

		$this->end_controls_section();
	}

	protected function routes_settings() {
		$this->start_controls_section(
			'ha_google_map_routes_settings',
			[
				'label'     => esc_html__( 'Routes Coordinate Settings', 'happy-addons-pro' ),
				'condition' => [
					'google_map_type' => ['routes'],
				],
			]
		);
		$this->add_control(
			'ha_map_routes_origin',
			[
				'label'     => esc_html__( 'Origin', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_control(
			'ha_map_routes_origin_lat',
			[
				'label'       => esc_html__( 'Latitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => esc_html__( '-12.044012922866312', 'happy-addons-pro' ),
			]
		);
		$this->add_control(
			'ha_map_routes_origin_lng',
			[
				'label'       => esc_html__( 'Longitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => esc_html__( '-77.02470665341184', 'happy-addons-pro' ),
			]
		);
		$this->add_control(
			'ha_map_routes_dest',
			[
				'label'     => esc_html__( 'Destination', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_control(
			'ha_map_routes_dest_lat',
			[
				'label'       => esc_html__( 'Latitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => esc_html__( '-12.090814532191756', 'happy-addons-pro' ),
			]
		);
		$this->add_control(
			'ha_map_routes_dest_lng',
			[
				'label'       => esc_html__( 'Longitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => esc_html__( '-77.02271108990476', 'happy-addons-pro' ),
			]
		);
		$this->add_control(
			'ha_map_routes_travel_mode',
			[
				'label'       => esc_html__( 'Travel Mode', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'walking',
				'label_block' => false,
				'options'     => [
					'walking'   => esc_html__( 'Walking', 'happy-addons-pro' ),
					'bicycling' => esc_html__( 'Bicycling', 'happy-addons-pro' ),
					'driving'   => esc_html__( 'Driving', 'happy-addons-pro' ),
				],
			]
		);
		$this->end_controls_section();
	}

	protected function legend_settings() {
		$this->start_controls_section('section_legend_settings', [
			'label'     => esc_html__( 'Legend', 'happy-addons-pro' ),
			'tab'       => Controls_Manager::TAB_CONTENT,
			'condition' => [
				'show_legend' => 'yes',
			],
		]);

		$this->add_control(
			'legend_title',
			[
				'label'       => esc_html__( 'Title', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Legend', 'happy-addons-pro' ),
				'placeholder' => esc_html__( 'Type your title here', 'happy-addons-pro' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'legend_icon',
			[
				'label' => esc_html__( 'Choose Image', 'happy-addons-pro' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'legend_item_title',
			[
				'label'       => esc_html__( 'Title', 'happy-addons-pro' ),
				'label_block' => 'true',
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Title', 'happy-addons-pro' ),
				'placeholder' => esc_html__( 'Type your title here', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'list_legend',
			[
				'label'       => esc_html__( 'Legend items', 'happy-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'legend_item_title' => esc_html__( 'Title #1', 'happy-addons-pro' ),
					],
				],
				'title_field' => '{{{ legend_item_title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function marker_settings() {
		$this->start_controls_section(
			'section_marker',
			[
				'label' => __( 'Map Marker', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'pin_icon',
			[
				'label' => esc_html__( 'Choose Image', 'happy-addons-pro' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'rectangular_image',
			[
				'label'        => esc_html__( 'Rectangular image', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$repeater->add_control(
			'square_pin_size',
			[
				'label'      => esc_html__( 'Custom marker size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'condition'  => [
					'rectangular_image!' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'rectangular_pin_size_height',
			[
				'label'      => esc_html__( 'Custom marker height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 65,
				],
				'condition'  => [
					'rectangular_image' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'rectangular_pin_size_width',
			[
				'label'      => esc_html__( 'Custom marker width', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'condition'  => [
					'rectangular_image' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pin_latitude',
			[
				'label'       => esc_html__( 'Latitude', 'happy-addons-pro' ),
				'label_block' => 'true',
				'type'        => Controls_Manager::TEXT,
				'default'     => '40.730610',
			]
		);

		$repeater->add_control(
			'pin_longitude',
			[
				'label'       => esc_html__( 'Longitude', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => 'true',
				'default'     => '-73.935242',
			]
		);

		$repeater->add_control(
			'pin_item_title',
			[
				'label'       => esc_html__( 'Title', 'happy-addons-pro' ),
				'label_block' => 'true',
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => 'Title',
				'placeholder' => esc_html__( 'Type your title here', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'pin_item_description',
			[
				'label'       => esc_html__( 'Description', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Type description here', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'list_marker',
			[
				'label'              => esc_html__( 'Map pins', 'happy-addons-pro' ),
				'type'               => Controls_Manager::REPEATER,
				'fields'             => $repeater->get_controls(),
				'default'            => [
					[
						'pin_item_title' => esc_html__( 'Title', 'happy-addons-pro' ),
					],
				],
				'title_field'        => '{{{ pin_item_title }}}',
				'frontend_available' => false,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'panel-map-styles',
			[
				'label' => __( 'Map Style', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'map_style',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Map style', 'happy-addons-pro' ),
				'options' => [
					'standard'  => esc_html__( 'Standard', 'happy-addons-pro' ),
					'silver'    => esc_html__( 'Silver', 'happy-addons-pro' ),
					'retro'     => esc_html__( 'Retro', 'happy-addons-pro' ),
					'dark'      => esc_html__( 'Dark', 'happy-addons-pro' ),
					'night'     => esc_html__( 'Night', 'happy-addons-pro' ),
					'aubergine' => esc_html__( 'Aubergine', 'happy-addons-pro' ),
					'custom'    => esc_html__( 'Custom', 'happy-addons-pro' ),
				],
				'default' => 'standard',
			]
		);
		$this->add_control(
			'custom_style_set_notice',
			[
				'raw'             => '<strong>' . esc_html__( 'Note!', 'happy-addons-pro' ) . '</strong> ' . sprintf( esc_html__( 'To use custom style, Use a third party platform like %s and paste your custom style json here', 'happy-addons-pro' ), '<a href="https://snazzymaps.com/" target="_blank">snazzymaps</a>' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type'     => 'ui',
				'condition'       => [
					'map_style' => 'custom',
				],
			]
		);

		$this->add_control(
			'custom_map_style',
			[
				'label'       => esc_html__( 'JSON code', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 10,
				'placeholder' => esc_html__( 'Type your JSON code here', 'happy-addons-pro' ),
				'condition'   => [
					'map_style' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->section_style_map();
		$this->section_style_marker_title();
		$this->section_style_marker_description();
		$this->section_style_legend_title();
		$this->section_style_legend();
		$this->section_style_legend_items();
		$this->section_style_map_stroke();
	}

	protected function section_style_map() {
		$this->start_controls_section('section_style_map', [
			'label' => esc_html__( 'Map', 'happy-addons-pro' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control(
			'section_style_map_margin',
			[
				'label'      => esc_html__( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'devices'    => ['desktop', 'tablet', 'mobile'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__wrapper'              => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay' => 'margin-right: {{RIGHT}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'map_height',
			[
				'label'      => esc_html__( 'Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => '500',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__wrapper'              => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'map_width',
			[
				'label'      => esc_html__( 'Width', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__wrapper'              => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'section_styles_map_style_tabs' );

		$this->start_controls_tab( 'section_styles_map_normal_style_tab', ['label' => esc_html__( 'NORMAL', 'happy-addons-pro' )] );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'section_styles_map_border_normal',
				'label'    => esc_html__( 'Border Type', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-adv-google-map__wrapper',
			]
		);

		$this->add_responsive_control(
			'section_styles_map_border_radius_normal',
			[
				'label'      => esc_html__( 'Border radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__wrapper, {{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'section_styles_map_box_shadow_normal',
				'label'    => esc_html__( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-adv-google-map__wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters_normal',
				'selector' => '{{WRAPPER}} .ha-adv-google-map__wrapper',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'section_styles_map_hover_style_tab', ['label' => esc_html__( 'HOVER', 'happy-addons-pro' )] );

		$this->add_control(
			'section_styles_map_separate_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'section_styles_map_border_hover',
				'label'    => esc_html__( 'Border Type', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-adv-google-map__wrapper:hover, {{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay:hover + .ha-adv-google-map__wrapper',
			]
		);

		$this->add_responsive_control(
			'section_styles_map_border_radius_hover',
			[
				'label'      => esc_html__( 'Border radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__wrapper:hover, {{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay:hover + .ha-adv-google-map__wrapper, {{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'section_styles_map_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-adv-google-map__wrapper:hover, {{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay:hover + .ha-adv-google-map__wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .ha-adv-google-map__wrapper:hover, {{WRAPPER}} .mdp-gmaper-elementor-google-map-overlay:hover + .ha-adv-google-map__wrapper',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function generate_margin_padding_controls( $section_id, $html_class ) {
		$this->add_responsive_control(
			$section_id . '_margin',
			[
				'label'      => esc_html__( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'devices'    => ['desktop', 'tablet', 'mobile'],
				'selectors'  => [
					"{{WRAPPER}} .$html_class" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$section_id . '_padding',
			[
				'label'      => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'devices'    => ['desktop', 'tablet', 'mobile'],
				'selectors'  => [
					"{{WRAPPER}} .$html_class" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
	private function generate_typography_tabs_controls( $section_id, $html_class, $include_color = true, $include_bg = true, $include_css_filter = false, $include_typography = true ) {
		if ( $include_typography ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => $section_id . '_typography',
					'label'    => esc_html__( 'Typography', 'happy-addons-pro' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' => "{{WRAPPER}} .$html_class",
				]
			);
		}

		$this->start_controls_tabs( $section_id . '_style_tabs' );

		$this->start_controls_tab( $section_id . '_normal_style_tab', ['label' => esc_html__( 'NORMAL', 'happy-addons-pro' )] );

		if ( $include_color ) {
			$this->add_control(
				$section_id . '_normal_text_color',
				[
					'label'     => esc_html__( 'Color', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} .$html_class" => 'color: {{VALUE}}',
					],
				]
			);
		}

		if ( $include_bg ) {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $section_id . '_normal_background',
					'label'    => esc_html__( 'Background type', 'happy-addons-pro' ),
					'types'    => ['classic', 'gradient'],
					'selector' => "{{WRAPPER}} .$html_class",
				]
			);

			$this->add_control(
				$section_id . '_separate_normal',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);
		}

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $section_id . '_border_normal',
				'label'    => esc_html__( 'Border Type', 'happy-addons-pro' ),
				'selector' => "{{WRAPPER}} .$html_class",
			]
		);

		$this->add_responsive_control(
			$section_id . '_border_radius_normal',
			[
				'label'      => esc_html__( 'Border radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					"{{WRAPPER}} .$html_class" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $section_id . '_box_shadow_normal',
				'label'    => esc_html__( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => "{{WRAPPER}} .$html_class",
			]
		);

		if ( $include_css_filter ) {
			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name'     => 'css_filters_normal',
					'selector' => '{{WRAPPER}} .' . $html_class,
				]
			);
		}

		$this->end_controls_tab();

		$this->start_controls_tab( $section_id . '_hover_style_tab', ['label' => esc_html__( 'HOVER', 'happy-addons-pro' )] );

		if ( $include_color ) {
			$this->add_control(
				$section_id . '_hover_color',
				[
					'label'     => esc_html__( 'Color', 'happy-addons-pro' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} .$html_class:hover" => 'color: {{VALUE}}',
					],
				]
			);
		}

		if ( $include_bg ) {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $section_id . '_background_hover',
					'label'    => esc_html__( 'Background type', 'happy-addons-pro' ),
					'types'    => ['classic', 'gradient', 'video'],
					'selector' => "{{WRAPPER}} .$html_class:hover",
				]
			);
		}

		$this->add_control(
			$section_id . '_separate_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $section_id . '_border_hover',
				'label'    => esc_html__( 'Border Type', 'happy-addons-pro' ),
				'selector' => "{{WRAPPER}} .$html_class:hover",
			]
		);

		$this->add_responsive_control(
			$section_id . '_border_radius_hover',
			[
				'label'      => esc_html__( 'Border radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					"{{WRAPPER}} .$html_class:hover" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $section_id . '_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => "{{WRAPPER}} .$html_class:hover",
			]
		);

		if ( $include_css_filter ) {
			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name'     => 'css_filters_hover',
					'selector' => '{{WRAPPER}} .' . $html_class . ':hover',
				]
			);
		}

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	protected function section_style_marker_title() {
		$this->start_controls_section('section_style_marker_title', [
			'label' => esc_html__( 'Marker title', 'happy-addons-pro' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->generate_margin_padding_controls( 'section_style_marker_title', 'ha-adv-google-map__marker-title-wrapper' );

		$this->add_control(
			'marker_title_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'happy-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'happy-addons-pro' ),
						'icon'  => ' eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'happy-addons-pro' ),
						'icon'  => ' eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'happy-addons-pro' ),
						'icon'  => ' eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .ha-adv-google-map__marker-title-wrapper' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->generate_typography_tabs_controls( 'section_style_marker_title', 'ha-adv-google-map__marker-title' );

		$this->end_controls_section();
	}

	/**
	 * Add widget controls: Style -> Section Style Marker Description.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	protected function section_style_marker_description() {
		$this->start_controls_section('section_style_marker_description', [
			'label' => esc_html__( 'Marker description', 'happy-addons-pro' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->generate_margin_padding_controls( 'section_style_marker_description', 'ha-adv-google-map__marker-description-wrapper' );

		$this->add_control(
			'marker_description_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'happy-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'happy-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'happy-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					],
					'end'    => [
						'title' => esc_html__( 'Right', 'happy-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .ha-adv-google-map__marker-description-wrapper' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->generate_typography_tabs_controls( 'section_style_marker_description', 'ha-adv-google-map__marker-description-wrapper' );

		$this->end_controls_section();
	}

	/**
	 * Add widget controls: Style -> Section Style Legend.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	protected function section_style_legend() {
		$this->start_controls_section('section_style_legend', [
			'label'     => esc_html__( 'Legend', 'happy-addons-pro' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_legend' => 'yes',
			],
		]);

		$this->generate_margin_padding_controls( 'section_style_legend', 'ha-adv-google-map__legend' );

		$this->add_control(
			'legend_wrapper_height',
			[
				'label'      => esc_html__( 'Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					// '{{WRAPPER}} .ha-adv-google-map__legend' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adv-google-map__legend--items' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'legend_wrapper_width',
			[
				'label'      => esc_html__( 'Width', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__legend' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->generate_typography_tabs_controls( 'legend_icon_size', 'ha-adv-google-map__legend', false, true, false, false );

		$this->end_controls_section();
	}

	/**
	 * Add widget controls: Style -> Section Style Legend Title.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	protected function section_style_legend_title() {
		$this->start_controls_section('section_style_legend_title', [
			'label'     => esc_html__( 'Legend title', 'happy-addons-pro' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_legend' => 'yes',
			],
		]);

		$this->generate_margin_padding_controls( 'section_style_legend_title', 'ha-adv-google-map__legend-title-wrapper' );

		$this->add_control(
			'legend_title_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'happy-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'    => [
						'title' => esc_html__( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .ha-adv-google-map__legend-title-wrapper' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->generate_typography_tabs_controls( 'section_style_legend_title', 'ha-adv-google-map__legend-title-wrapper' );

		$this->end_controls_section();
	}

	/**
	 * Add widget controls: Style -> Section Style Legend Items.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 **/
	protected function section_style_legend_items() {
		$this->start_controls_section('section_style_legend_items', [
			'label'     => esc_html__( 'Legend items', 'happy-addons-pro' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_legend' => 'yes',
			],
		]);

		$this->add_control(
			'legend_icon_size',
			[
				'label'      => esc_html__( 'Icon size', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-adv-google-map__legend--item-image' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->generate_margin_padding_controls( 'section_style_legend_items', 'ha-adv-google-map__legend--item' );

		$this->generate_typography_tabs_controls( 'section_style_legend_items', 'ha-adv-google-map__legend--item' );

		$this->end_controls_section();
	}

	protected function section_style_map_stroke() {
		$this->start_controls_section(
			'section_map_stroke_style_settings',
			[
				'label'     => esc_html__( 'Stroke Style', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'google_map_type' => ['polyline', 'polygon', 'routes'],
				],
			]
		);
		$this->add_control(
			'ha_map_stroke_color',
			[
				'label'   => esc_html__( 'Color', 'happy-addons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#e23a47',
			]
		);
		$this->add_responsive_control(
			'ha_map_stroke_opacity',
			[
				'label'      => __( 'Opacity', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0.8,
				],
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0.2,
						'max'  => 1,
						'step' => 0.1,
					],
				],
			]
		);
		$this->add_responsive_control(
			'ha_map_stroke_weight',
			[
				'label'      => __( 'Weight', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 4,
				],
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
			]
		);
		$this->add_control(
			'ha_map_stroke_fill_color',
			[
				'label'     => esc_html__( 'Fill Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e23a47',
				'condition' => [
					'google_map_type' => ['polygon'],
				],
			]
		);
		$this->add_responsive_control(
			'ha_map_stroke_fill_opacity',
			[
				'label'      => __( 'Fill Opacity', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0.4,
				],
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0.2,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'condition'  => [
					'google_map_type' => ['polygon'],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function notice() {
		printf(
			'<div %s>%s</div>',
			'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
			'<strong>' . esc_html__( 'Note!', 'happy-addons-pro' ) . '</strong> ' . esc_html__( 'Please set credentials in Happy Addons Dashboard.', 'happy-addons-pro' )
		);
	}

	protected function render() {
		$google_credentials = ha_get_credentials( 'google_map' );
		$gm_api_key         = is_array( $google_credentials ) ? $google_credentials['api_key'] : '';

		if ( empty( $gm_api_key ) ) {
			$this->notice();
			return;
		}

		$settings          = $this->get_settings_for_display();
		$markers           = wp_json_encode( $settings['list_marker'] );
		$polylines_array   = $settings['ha_gmap_polylines'] ? $settings['ha_gmap_polylines'] : [];
		$polylines         = wp_json_encode( $polylines_array );
		$map_zoom          = isset( $settings['map_zoom'] ) ? $settings['map_zoom']['size'] : 8;
		$map_zoom_tablet   = isset( $settings['map_zoom_tablet'] ) ? $settings['map_zoom_tablet']['size'] : 10;
		$map_zoom_mobile   = isset( $settings['map_zoom_mobile'] ) ? $settings['map_zoom_mobile']['size'] : 12;
		$scroll_wheel_zoom = $settings['scroll_wheel_zoom'] ? $settings['scroll_wheel_zoom'] : 'false';
		$info_open         = $settings['info_open'] ? $settings['info_open'] : 'false';
		$info_open_hover   = $settings['info_open_hover'] ? $settings['info_open_hover'] : 'false';
		$zoom_control      = $settings['zoom_control'] ? $settings['zoom_control'] : 'false';
		$full_screen       = $settings['full_screen'] ? $settings['full_screen'] : 'false';
		$disable_map_drag  = $settings['disable_map_drag'] ? $settings['disable_map_drag'] : 'false';
		$show_legend       = $settings['show_legend'] ? $settings['show_legend'] : 'false';
		$street_view       = $settings['street_view'] ? $settings['street_view'] : 'false'; 
		$stroke_color 	   = $settings['ha_map_stroke_color'] ? $settings['ha_map_stroke_color'] : '';
		$stroke_opacity    = $settings['ha_map_stroke_opacity'] ? $settings['ha_map_stroke_opacity']['size'] : '';
		$stroke_fill_color = $settings['ha_map_stroke_fill_color'] ? $settings['ha_map_stroke_fill_color'] : '';
		$stroke_weight	   = $settings['ha_map_stroke_weight'] ? $settings['ha_map_stroke_weight']['size'] : '';
		$stroke_fill_opacity = $settings['ha_map_stroke_fill_opacity'] ? $settings['ha_map_stroke_fill_opacity']['size'] : '';

		?>
		<div class="ha-adv-google-map">
			<div class="ha-adv-google-map__wrapper
			<?php if ( $settings['hide_logo'] === 'yes' ) {
				echo esc_attr( ' ha-adv-google-map-disable-logo' );
			} ?>
			<?php if ( $settings['hide_copyright'] === 'yes' ) {
				echo esc_attr( 'ha-adv-google-map-disable-copyright' );
			} ?>
			<?php if ( $settings['hide_tou'] === 'yes' ) {
				echo esc_attr( 'ha-adv-google-map-disable-terms-of-use' );
			} ?>
			<?php if ( $settings['hide_report'] === 'yes' ) {
				echo esc_attr( 'ha-adv-google-map-disable-report-error' );
			} ?>
			<?php if ( $settings['hide_keyboard'] === 'yes' ) {
				echo esc_attr( 'ha-adv-google-map-disable-keyboard-shortcuts' );
			} ?>
			"
			data-latitude="<?php echo esc_attr( $settings['latitude'] ); ?>"
			data-longitude="<?php echo esc_attr( $settings['longitude'] ); ?>"
			data-map-type="<?php echo esc_attr( $settings['map_type'] ); ?>"
			data-map-zoom="<?php echo esc_attr( $map_zoom ); ?>"
			data-map-zoom-tablet="<?php echo esc_attr( $map_zoom_tablet ); ?>"
			data-map-zoom-mobile="<?php echo esc_attr( $map_zoom_mobile ); ?>"
			data-scroll-wheel-zoom="<?php echo esc_attr( $scroll_wheel_zoom ); ?>"
			data-info-open="<?php echo esc_attr( $info_open ); ?>"
			data-info-open-hover="<?php echo esc_attr( $info_open_hover ); ?>"
			data-zoom-control="<?php echo esc_attr( $zoom_control ); ?>"
			data-full-screen="<?php echo esc_attr( $full_screen ); ?>"
			data-disable-map-drag="<?php echo esc_attr( $disable_map_drag ); ?>"
			data-show-legend="<?php echo esc_attr( $show_legend ); ?>"
			data-map-type-control="<?php echo esc_attr( $settings['map_type_control'] ); ?>"
			data-street-view="<?php echo esc_attr( $street_view ); ?>"
			data-hide-logo="<?php echo esc_attr( $settings['hide_logo'] ); ?>"
			data-hide-copyright="<?php echo esc_attr( $settings['hide_copyright'] ); ?>"
			data-hide-tou="<?php echo esc_attr( $settings['hide_tou'] ); ?>"
			data-hide-report="<?php echo esc_attr( $settings['hide_report'] ); ?>"
			data-hide-keyboard="<?php echo esc_attr( $settings['hide_keyboard'] ); ?>"
			data-markers="<?php echo esc_attr( $markers ); ?>"
			data-google-map-type="<?php echo esc_attr( $settings['google_map_type'] ); ?>"
			data-polylines="<?php echo esc_attr( $polylines ); ?>"
			data-map-style="<?php echo esc_attr( $settings['map_style'] ); ?>"
			data-custom-map-style="<?php echo esc_attr( $settings['custom_map_style'] ); ?>"
			data-stroke-color="<?php echo esc_attr( $stroke_color ); ?>"
			data-stroke-opacity="<?php echo esc_attr( $stroke_opacity ); ?>"
			data-stroke-weight="<?php echo esc_attr( $stroke_weight ); ?>"
			data-stroke-fill="<?php echo esc_attr( $stroke_fill_color ); ?>"
			data-stroke-fill-opacity="<?php echo esc_attr( $stroke_fill_opacity ); ?>"
			data-origin-lat="<?php echo esc_attr( $settings['ha_map_routes_origin_lat'] ); ?>"
			data-origin-lng="<?php echo esc_attr( $settings['ha_map_routes_origin_lng'] ); ?>"
			data-dest-lat="<?php echo esc_attr( $settings['ha_map_routes_dest_lat'] ); ?>"
			data-dest-lng="<?php echo esc_attr( $settings['ha_map_routes_dest_lng'] ); ?>"
			data-travel-mode="<?php echo esc_attr( $settings['ha_map_routes_travel_mode'] ); ?>"
			></div>

			<?php if ( 'yes' === $settings['show_legend'] ) : ?>
				<div class="ha-adv-google-map__legend">
					<div class="ha-adv-google-map__legend-title-wrapper">
						<div class="ha-adv-google-map__legend--title"><?php echo esc_html( $settings['legend_title'] ); ?></div>
					</div>
					<div class="ha-adv-google-map__legend--items">
					<?php if ( is_array( $settings['list_legend'] ) ) :
						foreach ( $settings['list_legend'] as $item ) : ?>
							<div class="ha-adv-google-map__legend--item">
								<div class="ha-adv-google-map__legend--item-image" style="background-size: cover !important; background: url('<?php echo esc_url( $item['legend_icon']['url'] ); ?>') center center;"></div>
								<div class="ha-adv-google-map__legend--item-title"><?php echo esc_html( $item['legend_item_title'] ); ?></div>
							</div>
						<?php endforeach;
					endif; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
