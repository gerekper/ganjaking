<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Google Map Widget
 *
 * Porto Elementor widget to display a advanced google map.
 *
 * @since 5.4.4
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Google_Map_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_google_map';
	}

	public function get_title() {
		return __( 'Porto Google Map', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'google', 'map', 'location' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'googleapis', 'porto_shortcodes_map_loader_js' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {

		$floating_options = porto_update_vc_options_to_elementor( porto_shortcode_floating_fields() );

		$this->start_controls_section(
			'section_google_map',
			array(
				'label' => __( 'Google Map', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'width',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Width (in %)', 'porto-functionality' ),
				'min'     => 0,
				'max'     => 100,
				'default' => 100,
			)
		);

		$this->add_control(
			'height',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Height (in px)', 'porto-functionality' ),
				'min'     => 0,
				'max'     => 1000,
				'default' => 300,
			)
		);

		$this->add_control(
			'map_type',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Map type', 'porto-functionality' ),
				'options' => array(
					'ROADMAP'   => __( 'Roadmap', 'porto-functionality' ),
					'SATELLITE' => __( 'Satellite', 'porto-functionality' ),
					'HYBRID'    => __( 'Hybrid', 'porto-functionality' ),
					'TERRAIN'   => __( 'Terrain', 'porto-functionality' ),
				),
				'default' => 'ROADMAP',
			)
		);

		$this->add_control(
			'lat',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Latitude', 'porto-functionality' ),
				'default'     => '40.7528214',
				'description' => '<a href="http://universimmedia.pagesperso-orange.fr/geo/loc.htm" target="_blank">' . __( 'Here is a tool', 'porto-functionality' ) . '</a> ' . __( 'where you can find Latitude & Longitude of your location', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'lng',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Longitude', 'porto-functionality' ),
				'default'     => '-73.9580603',
				'description' => '<a href="http://universimmedia.pagesperso-orange.fr/geo/loc.htm" target="_blank">' . __( 'Here is a tool', 'porto-functionality' ) . '</a> ' . __( 'where you can find Latitude & Longitude of your location', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'zoom',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Map Zoom', 'porto-functionality' ),
				'options' => array(
					1 => 1,
					2,
					3,
					4,
					5,
					6,
					7,
					8,
					9,
					10,
					11,
					12,
					13,
					14,
					15,
					16,
					17,
					18,
					19,
					20,
				),
				'default' => 12,
			)
		);

		$this->add_control(
			'scrollwheel',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Disable map zoom on mouse wheel scroll', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'type'  => Controls_Manager::WYSIWYG,
				'label' => __( 'Info Window Text', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'infowindow_open',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Open on Marker Click', 'porto-functionality' ),
				'options' => array(
					'on'  => __( 'Yes', 'porto-functionality' ),
					'off' => __( 'No', 'porto-functionality' ),
				),
				'default' => 'on',
			)
		);

		$this->add_control(
			'marker_icon',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Marker/Point icon', 'porto-functionality' ),
				'options' => array(
					'default' => __( 'Use Google Default', 'porto-functionality' ),
					'custom'  => __( 'Upload Custom', 'porto-functionality' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'icon_img',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Upload Image Icon:', 'porto-functionality' ),
				'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'marker_icon' => array( 'custom' ),
				),
			)
		);

		$this->add_control(
			'streetviewcontrol',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Street view control', 'porto-functionality' ),
				'options' => array(
					'false' => __( 'Disable', 'porto-functionality' ),
					'true'  => __( 'Enable', 'porto-functionality' ),
				),
				'default' => 'false',
			)
		);

		$this->add_control(
			'maptypecontrol',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Map type control', 'porto-functionality' ),
				'options' => array(
					'false' => __( 'Disable', 'porto-functionality' ),
					'true'  => __( 'Enable', 'porto-functionality' ),
				),
				'default' => 'false',
			)
		);

		$this->add_control(
			'zoomcontrol',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Zoom control', 'porto-functionality' ),
				'options' => array(
					'false' => __( 'Disable', 'porto-functionality' ),
					'true'  => __( 'Enable', 'porto-functionality' ),
				),
				'default' => 'false',
			)
		);

		$this->add_control(
			'zoomcontrolposition',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Zoom Control position', 'porto-functionality' ),
				'options'   => array(
					'RIGHT_BOTTOM' => __( 'Right Bottom', 'porto-functionality' ),
					'RIGHT_TOP'    => __( 'Right Top', 'porto-functionality' ),
					'RIGHT_CENTER' => __( 'Right Center', 'porto-functionality' ),
					'LEFT_TOP'     => __( 'Left Top', 'porto-functionality' ),
					'LEFT_CENTER'  => __( 'Left Center', 'porto-functionality' ),
					'LEFT_BOTTOM'  => __( 'Left Bottom', 'porto-functionality' ),
				),
				'condition' => array(
					'zoomcontrol' => 'true',
				),
				'default'   => 'RIGHT_BOTTOM',
			)
		);

		$this->add_control(
			'dragging',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Dragging on Mobile', 'porto-functionality' ),
				'options' => array(
					'false' => __( 'Disable', 'porto-functionality' ),
					'true'  => __( 'Enable', 'porto-functionality' ),
				),
				'default' => 'false',
			)
		);

		$this->add_control(
			'top_margin',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Top margin', 'porto-functionality' ),
				'options' => array(
					'page_margin_top'         => __( 'Page (small)', 'porto-functionality' ),
					'page_margin_top_section' => __( 'Section (large)', 'porto-functionality' ),
					'none'                    => __( 'None', 'porto-functionality' ),
				),
				'default' => 'page_margin_top',
			)
		);

		$this->add_control(
			'map_override',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Map Width Override', 'porto-functionality' ),
				'options'     => array(
					'0'       => 'Default Width',
					'1'       => "Apply 1st parent element's width",
					'2'       => "Apply 2nd parent element's width",
					'3'       => "Apply 3rd parent element's width",
					'4'       => "Apply 4th parent element's width",
					'5'       => "Apply 5th parent element's width",
					'6'       => "Apply 6th parent element's width",
					'7'       => "Apply 7th parent element's width",
					'8'       => "Apply 8th parent element's width",
					'9'       => "Apply 9th parent element's width",
					'full'    => 'Full Width',
					'ex-full' => 'Maximum Full Width',
				),
				'default'     => '0',
				'description' => __( "By default, the map will be given to the Visual Composer row. However, in some cases depending on your theme's CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output.", 'porto-functionality' ),
			)
		);

		$this->add_control(
			'map_style',
			array(
				'type'        => Controls_Manager::WYSIWYG,
				'label'       => __( 'Google Styled Map JSON', 'porto-functionality' ),
				'description' => "<a target='_blank' href='http://googlemaps.github.io/js-samples/styledmaps/wizard/index.html'>" . __( 'Click here', 'porto-functionality' ) . '</a> ' . __( 'to get the style JSON code for styling your map.', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_google_map_style_options',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_google_map' ) ) {
			$content = $atts['content'];
			if ( ! empty( $atts['width'] ) ) {
				$atts['width'] .= '%';
			}
			if ( is_array( $atts['icon_img'] ) && ! empty( $atts['icon_img']['id'] ) ) {
				$atts['icon_img']  = (int) $atts['icon_img']['id'];
			}
			include $template;
		}
	}
}
