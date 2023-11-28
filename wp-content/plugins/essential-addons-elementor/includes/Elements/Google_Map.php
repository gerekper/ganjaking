<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use Elementor\Repeater;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Google_Map extends Widget_Base
{

	public function get_name()
	{
		return 'eael-google-map';
	}

	public function get_title()
	{
		return esc_html__('Google Map', 'essential-addons-elementor');
	}

	public function get_icon()
	{
		return 'eaicon-advanced-google-maps';
	}

	public function get_categories()
	{
		return ['essential-addons-elementor'];
	}

	public function get_keywords()
	{
		return [
			'advanced google map',
			'ea advanced google map',
			'ea google map',
			'ea map',
			'ea advanced map',
			'map',
			'google map',
			'navigation map',
			'mutiple marker',
			'dynamic map',
			'ea',
			'essential addons'
		];
	}

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/advanced-google-map/';
	}

	protected function register_controls()
	{
		/**
		 * Google Map General Settings
		 */
		$this->start_controls_section(
			'eael_section_google_map_settings',
			[
				'label' => esc_html__('General Settings', 'essential-addons-elementor')
			]
		);
		$this->add_control(
			'eael_google_map_type',
			[
				'label'       	=> esc_html__('Google Map Type', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'basic',
				'label_block' 	=> false,
				'options' 		=> [
					'basic'  	=> esc_html__('Basic', 'essential-addons-elementor'),
					'marker'  	=> esc_html__('Multiple Marker', 'essential-addons-elementor'),
					'static'  	=> esc_html__('Static', 'essential-addons-elementor'),
					'polyline'  => esc_html__('Polyline', 'essential-addons-elementor'),
					'polygon'  	=> esc_html__('Polygon', 'essential-addons-elementor'),
					'overlay'  	=> esc_html__('Overlay', 'essential-addons-elementor'),
					'routes'  	=> esc_html__('With Routes', 'essential-addons-elementor'),
					'panorama'  => esc_html__('Panorama', 'essential-addons-elementor'),
				]
			]
		);
		$this->add_control(
			'eael_google_map_address_type',
			[
				'label' => __('Address Type', 'essential-addons-elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'address' => [
						'title' => __('Address', 'essential-addons-elementor'),
						'icon' => 'fa fa-map',
					],
					'coordinates' => [
						'title' => __('Coordinates', 'essential-addons-elementor'),
						'icon' => 'fa fa-map-marker',
					],
				],
				'default' => 'address',
				'condition' => [
					'eael_google_map_type' => ['basic']
				]
			]
		);
		$this->add_control(
			'eael_google_map_addr',
			[
				'label' => esc_html__('Geo Address', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
				'label_block' => true,
				'default' => esc_html__('Marina Bay, Singapore', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_address_type' => ['address'],
					'eael_google_map_type' => ['basic']
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('28.948790', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type!' => ['routes'],
					'eael_google_map_address_type' => ['coordinates']
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-81.298843', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type!' => ['routes'],
					'eael_google_map_address_type' => ['coordinates']
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		// Only for static
		$this->add_control(
			'eael_google_map_static_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('28.948790', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['static'],
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_static_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-81.298843', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['static'],
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_resolution_title',
			[
				'label' => __('Map Image Resolution', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_google_map_type' => 'static'
				]
			]
		);
		$this->add_control(
			'eael_google_map_static_width',
			[
				'label' => esc_html__('Static Image Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 610
				],
				'range' => [
					'px' => [
						'max' => 1400,
					],
				],
				'condition' => [
					'eael_google_map_type' => 'static'
				]
			]
		);
		$this->add_control(
			'eael_google_map_static_height',
			[
				'label' => esc_html__('Static Image Height', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 300
				],
				'range' => [
					'px' => [
						'max' => 700,
					],
				],
				'condition' => [
					'eael_google_map_type' => 'static'
				]
			]
		);
		// Only for Overlay
		$this->add_control(
			'eael_google_map_overlay_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('28.948790', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['overlay'],
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_overlay_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-81.298843', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['overlay'],
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		// Only for panorama
		$this->add_control(
			'eael_google_map_panorama_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('28.948790', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['panorama'],
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_panorama_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-81.298843', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['panorama'],
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_overlay_content',
			[
				'label' => esc_html__('Overlay Content', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => True,
				'default' => esc_html__('Add your content here', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => 'overlay'
				]
			]
		);
		$this->end_controls_section();
		/**
		 * Map Settings (With Marker only for Basic)
		 */
		$this->start_controls_section(
			'eael_section_google_map_basic_marker_settings',
			[
				'label' => esc_html__('Map Marker Settings', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['basic']
				]
			]
		);
		$this->add_control(
			'eael_google_map_basic_marker_title',
			[
				'label' => esc_html__('Title', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
				'default' => esc_html__('Google Map Title', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_basic_marker_content',
			[
				'label' => esc_html__('Content', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
				'default' => esc_html__('Google map content', 'essential-addons-elementor')
			]
		);
		$this->add_control(
			'eael_google_map_basic_marker_icon_enable',
			[
				'label' => __('Custom Marker Icon', 'essential-addons-elementor'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __('Yes', 'essential-addons-elementor'),
				'label_off' => __('No', 'essential-addons-elementor'),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'eael_google_map_basic_marker_icon',
			[
				'label' => esc_html__('Marker Icon', 'essential-addons-elementor'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					// 'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'eael_google_map_basic_marker_icon_enable' => 'yes'
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_basic_marker_icon_width',
			[
				'label' => esc_html__('Marker Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 32
				],
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'condition' => [
					'eael_google_map_basic_marker_icon_enable' => 'yes'
				]
			]
		);
		$this->add_control(
			'eael_google_map_basic_marker_icon_height',
			[
				'label' => esc_html__('Marker Height', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 32
				],
				'range' => [
					'px' => [
						'max' => 150,
					],
				],
				'condition' => [
					'eael_google_map_basic_marker_icon_enable' => 'yes'
				]
			]
		);
		$this->end_controls_section();
  		/**
  		 * Map Settings (With Marker)
  		 */
  		$this->start_controls_section(
  			'eael_section_google_map_marker_settings',
  			[
  				'label' => esc_html__( 'Map Marker Settings', 'essential-addons-elementor' ),
  				'condition' => [
  					'eael_google_map_type' => ['marker', 'polyline', 'routes', 'static']
  				]
  			]
  		);

        $repeater = new Repeater();

        $repeater->add_control(
            'eael_google_map_marker_lat',
            [
                'label' => esc_html__( 'Latitude', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__( '28.948790', 'essential-addons-elementor' ),
				'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_lng',
            [
                'label' => esc_html__( 'Longitude', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__( '-81.298843', 'essential-addons-elementor' ),
				'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_title',
            [
                'label' => esc_html__( 'Title', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__( 'Marker Title', 'essential-addons-elementor' ),
				'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_content',
            [
                'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'default' => esc_html__( 'Marker Content. You can put html here.', 'essential-addons-elementor' ),
				'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_icon_color',
            [
                'label' => esc_html__( 'Default Icon Color', 'essential-addons-elementor' ),
                'description' => esc_html__( '(Works only on Static mode)', 'essential-addons-elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#e23a47',
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_icon_enable',
            [
                'label' => __( 'Use Custom Icon', 'essential-addons-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __( 'Yes', 'essential-addons-elementor' ),
                'label_off' => __( 'No', 'essential-addons-elementor' ),
                'return_value' => 'yes',
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_icon',
            [
                'label' => esc_html__( 'Custom Icon', 'essential-addons-elementor' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    // 'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'eael_google_map_marker_icon_enable' => 'yes'
				],
				'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_icon_width',
            [
                'label' => esc_html__( 'Icon Width', 'essential-addons-elementor' ),
                'type' => Controls_Manager::NUMBER,
                'default' => esc_html__( '32', 'essential-addons-elementor' ),
                'condition' => [
                    'eael_google_map_marker_icon_enable' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'eael_google_map_marker_icon_height',
            [
                'label' => esc_html__( 'Icon Height', 'essential-addons-elementor' ),
                'type' => Controls_Manager::NUMBER,
                'default' => esc_html__( '32', 'essential-addons-elementor' ),
                'condition' => [
                    'eael_google_map_marker_icon_enable' => 'yes'
                ]
            ]
        );

		$this->add_control(
			'eael_google_map_markers',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					['eael_google_map_marker_title' => esc_html__('Map Marker 1', 'essential-addons-elementor')],
				],
				'fields' => $repeater->get_controls(),
				'title_field' => '{{eael_google_map_marker_title}}',
			]
		);
		$this->end_controls_section();


  		/**
  		 * Polyline Coordinates Settings (Polyline)
  		 */
  		$this->start_controls_section(
  			'eael_section_google_map_polyline_settings',
  			[
  				'label' => esc_html__( 'Coordinate Settings', 'essential-addons-elementor' ),
  				'condition' => [
  					'eael_google_map_type' => ['polyline', 'polygon']
  				]
  			]
  		);

        $polylines_repeater = new Repeater();

        $polylines_repeater->add_control(
            'eael_google_map_polyline_title',
            [
                'label' => esc_html__( 'Title', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__( '#', 'essential-addons-elementor' ),
				'ai' => [
					'active' => false,
				],
            ]
        );

        $polylines_repeater->add_control(
            'eael_google_map_polyline_lat',
            [
                'label' => esc_html__( 'Latitude', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
				'ai' => [
					'active' => false,
				],
            ]
        );

        $polylines_repeater->add_control(
            'eael_google_map_polyline_lng',
            [
                'label' => esc_html__( 'Longitude', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
				'ai' => [
					'active' => false,
				],
            ]
        );

		$this->add_control(
			'eael_google_map_polylines',
			[
				'type'        => Controls_Manager::REPEATER,
				'seperator'   => 'before',
				'default'     => [
					[
						'eael_google_map_polyline_title' => esc_html__( '#1', 'essential-addons-elementor' ),
						'eael_google_map_polyline_lat'   => '-12.040397656836609',
						'eael_google_map_polyline_lng'   => '-77.03373871559225'
					],
					[
						'eael_google_map_polyline_title' => esc_html__( '#2', 'essential-addons-elementor' ),
						'eael_google_map_polyline_lat'   => '-12.040248585302038',
						'eael_google_map_polyline_lng'   => '-77.03993927003302'
					],
					[
						'eael_google_map_polyline_title' => esc_html__( '#3', 'essential-addons-elementor' ),
						'eael_google_map_polyline_lat'   => '-12.050047116528843',
						'eael_google_map_polyline_lng'   => '-77.02448169303511'
					],
					[
						'eael_google_map_polyline_title' => esc_html__( '#4', 'essential-addons-elementor' ),
						'eael_google_map_polyline_lat'   => '-12.044804866577001',
						'eael_google_map_polyline_lng'   => '-77.02154422636042'
					],
				],
				'fields'      => $polylines_repeater->get_controls(),
				'title_field' => '{{eael_google_map_polyline_title}}',
			]
		);
		$this->end_controls_section();

		/**
		 * Routes Coordinates Settings (Routes)
		 */
		$this->start_controls_section(
			'eael_section_google_map_routes_settings',
			[
				'label' => esc_html__('Routes Coordinate Settings', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_type' => ['routes']
				]
			]
		);
		$this->add_control(
			'eael_google_map_routes_origin',
			[
				'label' => esc_html__('Origin', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_control(
			'eael_google_map_routes_origin_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-12.044012922866312', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_routes_origin_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-77.02470665341184', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_routes_dest',
			[
				'label' => esc_html__('Destination', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_control(
			'eael_google_map_routes_dest_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-12.090814532191756', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_routes_dest_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('-77.02271108990476', 'essential-addons-elementor'),
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_routes_travel_mode',
			[
				'label'       	=> esc_html__('Travel Mode', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'walking',
				'label_block' 	=> false,
				'options' 		=> [
					'walking'  	=> esc_html__('Walking', 'essential-addons-elementor'),
					'bicycling' => esc_html__('Bicycling', 'essential-addons-elementor'),
					'driving' 	=> esc_html__('Driving', 'essential-addons-elementor'),
				]
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_map_controls',
			[
				'label'	=> esc_html__('Map Controls', 'essential-addons-elementor')
			]
		);
		$this->add_control(
			'eael_google_map_zoom',
			[
				'label' => esc_html__('Zoom Level', 'essential-addons-elementor'),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => esc_html__('14', 'essential-addons-elementor'),
			]
		);
		$this->add_control(
			'eael_map_streeview_control',
			[
				'label'                 => esc_html__('Street View Controls', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'true',
				'label_on'              => __('On', 'essential-addons-elementor'),
				'label_off'             => __('Off', 'essential-addons-elementor'),
				'return_value'          => 'true',
			]
		);
		$this->add_control(
			'eael_map_type_control',
			[
				'label'                 => esc_html__('Map Type Control', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __('On', 'essential-addons-elementor'),
				'label_off'             => __('Off', 'essential-addons-elementor'),
				'return_value'          => 'yes',
			]
		);
		$this->add_control(
			'eael_map_zoom_control',
			[
				'label'                 => esc_html__('Zoom Control', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __('On', 'essential-addons-elementor'),
				'label_off'             => __('Off', 'essential-addons-elementor'),
				'return_value'          => 'yes',
			]
		);
		$this->add_control(
			'eael_map_fullscreen_control',
			[
				'label'                 => esc_html__('Fullscreen Control', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __('On', 'essential-addons-elementor'),
				'label_off'             => __('Off', 'essential-addons-elementor'),
				'return_value'          => 'yes',
			]
		);
		$this->add_control(
			'eael_map_scroll_zoom',
			[
				'label'                 => esc_html__('Scroll Wheel Zoom', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __('On', 'essential-addons-elementor'),
				'label_off'             => __('Off', 'essential-addons-elementor'),
				'return_value'          => 'yes',
			]
		);
		$this->add_control(
			'eael_google_map_center_point',
			[
				'label'     => esc_html__( 'Center Point', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''       => __( 'Default', 'essential-addons-elementor' ),
					'auto'   => __( 'Auto Center', 'essential-addons-elementor' ),
					'custom' => __( 'Custom Point', 'essential-addons-elementor' ),
				],
				'condition' => [
					'eael_google_map_type' => [ 'marker', 'polyline', 'polygon' ]
				]
			]
		);
		$this->add_control(
			'eael_google_map_center_point_lat',
			[
				'label' => esc_html__('Latitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('28.948790', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_center_point' => 'custom',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_google_map_center_point_lng',
			[
				'label' => esc_html__('Longitude', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('90.403947', 'essential-addons-elementor'),
				'condition' => [
					'eael_google_map_center_point' => 'custom',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->end_controls_section();

		/**
		 * Map Theme Settings
		 */
		$this->start_controls_section(
			'eael_section_google_map_theme_settings',
			[
				'label'		=> esc_html__('Map Theme', 'essential-addons-elementor'),
				'condition' => [
                    'eael_google_map_type!'	=> ['static', 'panorama', 'polygon', 'routes']
				]
			]
		);
		$this->add_control(
			'eael_google_map_theme_source',
			[
				'label'		=> __('Theme Source', 'essential-addons-elementor'),
				'type'		=> Controls_Manager::CHOOSE,
				'options' => [
					'gstandard' => [
						'title' => __('Google Standard', 'essential-addons-elementor'),
						'icon' => 'fa fa-map',
					],
					'snazzymaps' => [
						'title' => __('Snazzy Maps', 'essential-addons-elementor'),
						'icon' => 'fa fa-map-marker',
					],
					'custom' => [
						'title' => __('Custom', 'essential-addons-elementor'),
						'icon' => 'fa fa-edit',
					],
				],
				'default'	=> 'gstandard'
			]
		);
		$this->add_control(
			'eael_google_map_gstandards',
			[
				'label'                 => esc_html__('Google Themes', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'standard',
				'options'               => [
					'standard'     => __('Standard', 'essential-addons-elementor'),
					'silver'       => __('Silver', 'essential-addons-elementor'),
					'retro'        => __('Retro', 'essential-addons-elementor'),
					'dark'         => __('Dark', 'essential-addons-elementor'),
					'night'        => __('Night', 'essential-addons-elementor'),
					'aubergine'    => __('Aubergine', 'essential-addons-elementor')
				],
				'description'           => sprintf('<a href="https://mapstyle.withgoogle.com/" target="_blank">%1$s</a> %2$s', __('Click here', 'essential-addons-elementor'), __('to generate your own theme and use JSON within Custom style field.', 'essential-addons-elementor')),
				'condition'	=> [
					'eael_google_map_theme_source'	=> 'gstandard'
				]
			]
		);
		$this->add_control(
			'eael_google_map_snazzymaps',
			[
				'label'                 => esc_html__('SnazzyMaps Themes', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'label_block'			=> true,
				'default'               => 'colorful',
				'options'               => [
					'default'		=> __('Default', 'essential-addons-elementor'),
					'simple'		=> __('Simple', 'essential-addons-elementor'),
					'colorful'		=> __('Colorful', 'essential-addons-elementor'),
					'complex'		=> __('Complex', 'essential-addons-elementor'),
					'dark'			=> __('Dark', 'essential-addons-elementor'),
					'greyscale'		=> __('Greyscale', 'essential-addons-elementor'),
					'light'			=> __('Light', 'essential-addons-elementor'),
					'monochrome'	=> __('Monochrome', 'essential-addons-elementor'),
					'nolabels'		=> __('No Labels', 'essential-addons-elementor'),
					'twotone'		=> __('Two Tone', 'essential-addons-elementor')
				],
				'description'           => sprintf('<a href="https://snazzymaps.com/explore" target="_blank">%1$s</a> %2$s', __('Click here', 'essential-addons-elementor'), __('to explore more themes and use JSON within custom style field.', 'essential-addons-elementor')),
				'condition'	=> [
					'eael_google_map_theme_source'	=> 'snazzymaps'
				]
			]
		);
		$this->add_control(
			'eael_google_map_custom_style',
			[
				'label'                 => __('Custom Style', 'essential-addons-elementor'),
				'description'           => sprintf('<a href="https://mapstyle.withgoogle.com/" target="_blank">%1$s</a> %2$s', __('Click here', 'essential-addons-elementor'), __('to get JSON style code to style your map', 'essential-addons-elementor')),
				'type'                  => Controls_Manager::TEXTAREA,
				'condition'             => [
					'eael_google_map_theme_source'     => 'custom',
				],
			]
		);
		$this->end_controls_section();
		/**
		 * -------------------------------------------
		 * Tab Style Google Map Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_google_map_style_settings',
			[
				'label' => esc_html__('General Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'eael_google_map_max_width',
			[
				'label' => __('Max Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1140,
					'unit' => 'px',
				],
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1400,
						'step' => 10,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .eael-google-map' => 'max-width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'eael_google_map_max_height',
			[
				'label' => __('Max Height', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1400,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .eael-google-map' => 'height: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'eael_google_map_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-google-map' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style Google Map Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_google_map_overlay_style_settings',
			[
				'label' => esc_html__('Overlay Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_google_map_type' => ['overlay']
				]
			]
		);
		$this->add_responsive_control(
			'eael_google_map_overlay_width',
			[
				'label' => __('Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1100,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .eael-gmap-overlay' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control(
			'eael_google_map_overlay_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-gmap-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'eael_google_mapoverlay_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-gmap-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'eael_google_map_overlay_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-gmap-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_google_map_overlay_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-gmap-overlay',
			]
		);
		$this->add_responsive_control(
			'eael_google_map_overlay_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .eael-gmap-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_google_map_overlay_box_shadow',
				'selector' => '{{WRAPPER}} .eael-gmap-overlay',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_google_map_overlay_typography',
				'selector' => '{{WRAPPER}} .eael-gmap-overlay',
			]
		);
		$this->add_control(
			'eael_google_map_overlay_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#222',
				'selectors' => [
					'{{WRAPPER}} .eael-gmap-overlay' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style Google Map Stroke Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_google_map_stroke_style_settings',
			[
				'label' => esc_html__('Stroke Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_google_map_type' => ['polyline', 'polygon', 'routes']
				]
			]
		);
		$this->add_control(
			'eael_google_map_stroke_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#e23a47',
			]
		);
		$this->add_responsive_control(
			'eael_google_map_stroke_opacity',
			[
				'label' => __('Opacity', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.8,
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0.2,
						'max' => 1,
						'step' => 0.1,
					]
				],
			]
		);
		$this->add_responsive_control(
			'eael_google_map_stroke_weight',
			[
				'label' => __('Weight', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					]
				],
			]
		);
		$this->add_control(
			'eael_google_map_stroke_fill_color',
			[
				'label' => esc_html__('Fill Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#e23a47',
				'condition' => [
					'eael_google_map_type' => ['polygon']
				]
			]
		);
		$this->add_responsive_control(
			'eael_google_map_stroke_fill_opacity',
			[
				'label' => __('Fill Opacity', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.4,
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0.2,
						'max' => 1,
						'step' => 0.1,
					]
				],
				'condition' => [
					'eael_google_map_type' => ['polygon']
				]
			]
		);



		$this->end_controls_section();
	}

	protected function get_map_theme($settings)
	{

		if ($settings['eael_google_map_theme_source'] == 'custom') {
			return strip_tags($settings['eael_google_map_custom_style']);
		} else {
			$themes = include('advance-gmap-themes.php');
			if (isset($themes[$settings['eael_google_map_theme_source']][$settings['eael_google_map_gstandards']])) {
				return $themes[$settings['eael_google_map_theme_source']][$settings['eael_google_map_gstandards']];
			} elseif (isset($themes[$settings['eael_google_map_theme_source']][$settings['eael_google_map_snazzymaps']])) {
				return $themes[$settings['eael_google_map_theme_source']][$settings['eael_google_map_snazzymaps']];
			} else {
				return '';
			}
		}
	}

	protected function map_render_data_attributes( $settings ) {
        $new_settings = $this->get_settings();
		return [
			'data-map_type'				=> esc_attr($settings['eael_google_map_type']),
			'data-map_address_type'		=> esc_attr($settings['eael_google_map_address_type']),
			'data-map_lat'				=> esc_attr($this->get_settings('eael_google_map_lat')),
			'data-map_lng'				=> esc_attr($this->get_settings('eael_google_map_lng')),
			'data-map_addr'				=> esc_attr($settings['eael_google_map_addr']),
			'data-map_basic_marker_title'		=> esc_attr($settings['eael_google_map_basic_marker_title']),
			'data-map_basic_marker_content'		=> esc_attr($settings['eael_google_map_basic_marker_content']),
			'data-map_basic_marker_icon_enable'	=> esc_attr($new_settings['eael_google_map_basic_marker_icon_enable']),
			'data-map_basic_marker_icon'		=> esc_attr($new_settings['eael_google_map_basic_marker_icon']['url']),
			'data-map_basic_marker_icon_width'	=> esc_attr($new_settings['eael_google_map_basic_marker_icon_width']['size']),
			'data-map_basic_marker_icon_height'	=> esc_attr($new_settings['eael_google_map_basic_marker_icon_height']['size']),
			'data-map_zoom'				=> esc_attr($settings['eael_google_map_zoom']),
			'data-map_marker_content'	=> isset($settings['eael_google_map_marker_content']) ? esc_attr($settings['eael_google_map_marker_content']) : '',
			'data-map_markers'				=> urlencode(json_encode($new_settings['eael_google_map_markers'])),
			'data-map_static_width'			=> esc_attr($new_settings['eael_google_map_static_width']['size']),
			'data-map_static_height'		=> esc_attr($new_settings['eael_google_map_static_height']['size']),
			'data-map_static_lat'			=> esc_attr($settings['eael_google_map_static_lat']),
			'data-map_static_lng'			=> esc_attr($settings['eael_google_map_static_lng']),
			'data-map_polylines'			=> urlencode(json_encode($settings['eael_google_map_polylines'])),
			'data-map_stroke_color'			=> esc_attr($new_settings['eael_google_map_stroke_color']),
			'data-map_stroke_opacity'		=> esc_attr($new_settings['eael_google_map_stroke_opacity']['size']),
			'data-map_stroke_weight'		=> esc_attr($new_settings['eael_google_map_stroke_weight']['size']),
			'data-map_stroke_fill_color'	=> esc_attr($new_settings['eael_google_map_stroke_fill_color']),
			'data-map_stroke_fill_opacity'	=> esc_attr($new_settings['eael_google_map_stroke_fill_opacity']['size']),
			'data-map_overlay_content'		=> esc_attr($settings['eael_google_map_overlay_content']),
			'data-map_routes_origin_lat'	=> esc_attr($settings['eael_google_map_routes_origin_lat']),
			'data-map_routes_origin_lng'	=> esc_attr($settings['eael_google_map_routes_origin_lng']),
			'data-map_routes_dest_lat'		=> esc_attr($settings['eael_google_map_routes_dest_lat']),
			'data-map_routes_dest_lng'		=> esc_attr($settings['eael_google_map_routes_dest_lng']),
			'data-map_routes_travel_mode'	=> esc_attr($settings['eael_google_map_routes_travel_mode']),
			'data-map_panorama_lat'			=> esc_attr($settings['eael_google_map_panorama_lat']),
			'data-map_panorama_lng'			=> esc_attr($settings['eael_google_map_panorama_lng']),
			'data-map_theme'				=> urlencode(json_encode($this->get_map_theme($settings))),
			'data-map_streeview_control'	=> ($settings['eael_map_streeview_control'] ? 'true' : 'false'),
			'data-map_type_control'			=> ($settings['eael_map_type_control'] ? 'true' : 'false'),
			'data-map_zoom_control'			=> ($settings['eael_map_zoom_control'] ? 'true' : 'false'),
			'data-map_fullscreen_control'	=> ($settings['eael_map_fullscreen_control'] ? 'true' : 'false'),
			'data-map_scroll_zoom'			=> ($settings['eael_map_scroll_zoom'] ? 'true' : 'false')
		];
	}

	protected function get_map_render_data_attribute_string($settings)
	{

		$data_attributes = $this->map_render_data_attributes($settings);
		$data_string = '';

		foreach ($data_attributes as $key => $value) {
			if (isset($key) && !empty($value)) {
				$data_string .= ' ' . $key . '="' . $value . '"';
			}
		}

		return $data_string;
	}

	protected function get_polyline_center_point( $obj ) {
		$center_type = $obj->get_settings_for_display( 'eael_google_map_center_point' );

		if ( $center_type === 'auto' ) {
			$poly_lines = $obj->get_settings_for_display( 'eael_google_map_polylines' );

			if ( ! empty( $poly_lines ) ) {
				$lat_array = wp_list_pluck( $poly_lines, 'eael_google_map_polyline_lat' );
				$lng_array = wp_list_pluck( $poly_lines, 'eael_google_map_polyline_lng' );
			} else {
				$multy_lines = $obj->get_settings_for_display( 'eael_google_map_markers' );
				$lat_array   = wp_list_pluck( $multy_lines, 'eael_google_map_marker_lat' );
				$lng_array   = wp_list_pluck( $multy_lines, 'eael_google_map_marker_lng' );
			}

			$center_lat = ( max( $lat_array ) + min( $lat_array ) ) / 2;
			$center_lng = ( max( $lng_array ) + min( $lng_array ) ) / 2;
		} elseif ( $center_type === 'custom' ) {
			$center_lat = $obj->get_settings_for_display( 'eael_google_map_center_point_lat' );
			$center_lng = $obj->get_settings_for_display( 'eael_google_map_center_point_lng' );
		} else {
			$center_lat = $center_lng = '';
		}

		return [ 'center_lat' => $center_lat, 'center_lng' => $center_lng ];
	}

	protected function render()
	{

        $settings = $this->get_settings_for_display();
        $poly_line_center = $this->get_polyline_center_point($this);
		$this->add_render_attribute( 'eael_google_map_wrap', [
			'class'               => [ 'eael-google-map', 'eael-google-map-' . $settings['eael_google_map_type'] ],
			'id'                  => 'eael-google-map-' . esc_attr( $this->get_id() ),
			'data-id'             => esc_attr( $this->get_id() ),
			'data-map-center-lat' => $poly_line_center['center_lat'],
			'data-map-center-lng' => $poly_line_center['center_lng'],
		] );
?>

		<?php if (!empty($settings['eael_google_map_type'])) : ?>
			<div <?php echo $this->get_render_attribute_string('eael_google_map_wrap'), $this->get_map_render_data_attribute_string($settings); ?>></div>
		<?php endif; ?>
		<div class="google-map-notice"></div>
<?php
	}

	protected function content_template()
	{
	}
}
