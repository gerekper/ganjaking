<?php
/**
 * UAEL GoogleMap.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\GoogleMap\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class GoogleMap.
 */
class GoogleMap extends Common_Widget {

	/**
	 * Retrieve GoogleMap Widget name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'GoogleMap' );
	}

	/**
	 * Retrieve GoogleMap Widget title.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'GoogleMap' );
	}

	/**
	 * Retrieve GoogleMap Widget icon.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'GoogleMap' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'GoogleMap' );
	}

	/**
	 * Retrieve the list of scripts the image carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-google-maps', 'uael-google-maps-api', 'uael-google-maps-cluster' );
	}

	/**
	 * Returns an array of position options for map controls.
	 *
	 * @since 1.33.2
	 * @access protected
	 * @return array
	 */
	protected function get_control_position_options() {
		return array(
			''              => __( 'Default', 'uael' ),
			'TOP_CENTER'    => __( 'Top Center', 'uael' ),
			'TOP_LEFT'      => __( 'Top Left', 'uael' ),
			'TOP_RIGHT'     => __( 'Top Right', 'uael' ),
			'LEFT_TOP'      => __( 'Left Top', 'uael' ),
			'RIGHT_TOP'     => __( 'Right Top', 'uael' ),
			'LEFT_CENTER'   => __( 'Left Center', 'uael' ),
			'RIGHT_CENTER'  => __( 'Right Center', 'uael' ),
			'LEFT_BOTTOM'   => __( 'Left Bottom', 'uael' ),
			'RIGHT_BOTTOM'  => __( 'Right Bottom', 'uael' ),
			'BOTTOM_CENTER' => __( 'Bottom Center', 'uael' ),
			'BOTTOM_LEFT'   => __( 'Bottom Left', 'uael' ),
			'BOTTOM_RIGHT'  => __( 'Bottom Right', 'uael' ),
		);
	}

	/**
	 * Register GoogleMap controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_addresses_controls();
		$this->register_layout_controls();
		$this->register_controls_controls();
		$this->register_info_window_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register GoogleMap Addresses Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_addresses_controls() {

		$map_options = UAEL_Helper::get_integrations_options();

		$this->start_controls_section(
			'section_map_addresses',
			array(
				'label' => __( 'Addresses', 'uael' ),
			)
		);

		if ( parent::is_internal_links() && ( ! isset( $map_options['google_api'] ) || '' === $map_options['google_api'] ) ) {

			$widget_list = UAEL_Helper::get_widget_list();

			$admin_link = $widget_list['GoogleMap']['setting_url'];

			$this->add_control(
				'err_msg',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'To display customized Google Map without an issue, you need to configure Google Map API key. Please configure API key from <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'latitude',
			array(
				'label'       => __( 'Latitude', 'uael' ),
				'description' => sprintf( '<a href="https://www.latlong.net/" target="_blank">%1$s</a> %2$s', __( 'Click here', 'uael' ), __( 'to find Latitude of your location', 'uael' ) ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'longitude',
			array(
				'label'       => __( 'Longitude', 'uael' ),
				'description' => sprintf( '<a href="https://www.latlong.net/" target="_blank">%1$s</a> %2$s', __( 'Click here', 'uael' ), __( 'to find Longitude of your location', 'uael' ) ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'map_title',
			array(
				'label'       => __( 'Address Title', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'marker_infowindow',
			array(
				'label'       => __( 'Display Info Window', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'label_block' => true,
				'options'     => array(
					'none'  => __( 'None', 'uael' ),
					'click' => __( 'On Mouse Click', 'uael' ),
					'load'  => __( 'On Page Load', 'uael' ),
				),
			)
		);

		$repeater->add_control(
			'map_description',
			array(
				'label'       => __( 'Address Information', 'uael' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'marker_infowindow',
							'operator' => '!=',
							'value'    => 'none',
						),
					),
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'marker_icon_type',
			array(
				'label'   => __( 'Marker Icon', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'uael' ),
					'custom'  => __( 'Custom', 'uael' ),
				),
			)
		);

		$repeater->add_control(
			'marker_icon',
			array(
				'label'      => __( 'Select Marker', 'uael' ),
				'type'       => Controls_Manager::MEDIA,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'marker_icon_type',
							'operator' => '==',
							'value'    => 'custom',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'custom_marker_size',
			array(
				'label'       => __( 'Marker Size', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'description' => __( 'Note: If you want to retain the image original size, then set the Marker Size as blank.', 'uael' ),
				'default'     => array(
					'size' => 30,
					'unit' => 'px',
				),
				'range'       => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'marker_icon_type',
							'operator' => '==',
							'value'    => 'custom',
						),
					),
				),
			)
		);

			$this->add_control(
				'addresses',
				array(
					'label'       => '',
					'type'        => Controls_Manager::REPEATER,
					'default'     => array(
						array(
							'latitude'        => 51.503333,
							'longitude'       => -0.119562,
							'map_title'       => __( 'Coca-Cola London Eye', 'uael' ),
							'map_description' => '',
						),
					),
					'fields'      => $repeater->get_controls(),
					'title_field' => '<i class="fa fa-map-marker"></i> {{ map_title }}',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register GoogleMap Layout Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_layout_controls() {

		$this->start_controls_section(
			'section_map_settings',
			array(
				'label' => __( 'Layout', 'uael' ),
			)
		);

			$this->add_control(
				'type',
				array(
					'label'   => __( 'Map Type', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'roadmap',
					'options' => array(
						'roadmap'   => __( 'Road Map', 'uael' ),
						'satellite' => __( 'Satellite', 'uael' ),
						'hybrid'    => __( 'Hybrid', 'uael' ),
						'terrain'   => __( 'Terrain', 'uael' ),
					),
				)
			);

			$this->add_control(
				'skin',
				array(
					'label'     => __( 'Map Skin', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'standard',
					'options'   => array(
						'standard'     => __( 'Standard', 'uael' ),
						'silver'       => __( 'Silver', 'uael' ),
						'retro'        => __( 'Retro', 'uael' ),
						'dark'         => __( 'Dark', 'uael' ),
						'night'        => __( 'Night', 'uael' ),
						'aubergine'    => __( 'Aubergine', 'uael' ),
						'aqua'         => __( 'Aqua', 'uael' ),
						'classic_blue' => __( 'Classic Blue', 'uael' ),
						'earth'        => __( 'Earth', 'uael' ),
						'magnesium'    => __( 'Magnesium', 'uael' ),
						'custom'       => __( 'Custom', 'uael' ),
					),
					'condition' => array(
						'type!' => 'satellite',
					),
				)
			);

			$this->add_control(
				'map_custom_style',
				array(
					'label'       => __( 'Custom Style', 'uael' ),
					'description' => sprintf( '<a href="https://mapstyle.withgoogle.com/" target="_blank">%1$s</a> %2$s', __( 'Click here', 'uael' ), __( 'to get JSON style code to style your map', 'uael' ) ),
					'type'        => Controls_Manager::TEXTAREA,
					'condition'   => array(
						'skin'  => 'custom',
						'type!' => 'satellite',
					),
				)
			);

			$this->add_control(
				'animate',
				array(
					'label'   => __( 'Marker Animation', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						''       => __( 'None', 'uael' ),
						'drop'   => __( 'On Load', 'uael' ),
						'bounce' => __( 'Continuous', 'uael' ),
					),
				)
			);

			$this->add_control(
				'zoom',
				array(
					'label'   => __( 'Map Zoom', 'uael' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => array(
						'size' => 12,
					),
					'range'   => array(
						'px' => array(
							'min' => 1,
							'max' => 22,
						),
					),
				)
			);

			$this->add_responsive_control(
				'height',
				array(
					'label'      => __( 'Height', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 500,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min' => 80,
							'max' => 1200,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-google-map' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register GoogleMap Control Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_controls_controls() {

		$this->start_controls_section(
			'section_map_controls',
			array(
				'label' => __( 'Controls', 'uael' ),
			)
		);

			$this->add_control(
				'option_streeview',
				array(
					'label'              => __( 'Street View Controls', 'uael' ),
					'type'               => Controls_Manager::SWITCHER,
					'default'            => 'yes',
					'label_on'           => __( 'On', 'uael' ),
					'label_off'          => __( 'Off', 'uael' ),
					'return_value'       => 'yes',
					'frontend_available' => true,
				)
			);

		$this->add_control(
			'street_view_pos',
			array(
				'label'              => __( 'Street View Position', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => $this->get_control_position_options(),
				'render_type'        => 'template',
				'condition'          => array(
					'option_streeview' => 'yes',
				),
				'frontend_available' => true,
			)
		);

			$this->add_control(
				'type_control',
				array(
					'label'              => __( 'Map Type Control', 'uael' ),
					'type'               => Controls_Manager::SWITCHER,
					'default'            => 'yes',
					'label_on'           => __( 'On', 'uael' ),
					'label_off'          => __( 'Off', 'uael' ),
					'return_value'       => 'yes',
					'frontend_available' => true,
				)
			);

		$this->add_control(
			'map_type_pos',
			array(
				'label'              => __( 'Map Type Position', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => $this->get_control_position_options(),
				'render_type'        => 'template',
				'condition'          => array(
					'type_control' => 'yes',
				),
				'frontend_available' => true,
			)
		);

			$this->add_control(
				'zoom_control',
				array(
					'label'              => __( 'Zoom Control', 'uael' ),
					'type'               => Controls_Manager::SWITCHER,
					'default'            => 'yes',
					'label_on'           => __( 'On', 'uael' ),
					'label_off'          => __( 'Off', 'uael' ),
					'return_value'       => 'yes',
					'frontend_available' => true,
				)
			);

		$this->add_control(
			'zoom_control_pos',
			array(
				'label'              => __( 'Zoom Control Position', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => $this->get_control_position_options(),
				'render_type'        => 'template',
				'condition'          => array(
					'zoom_control' => 'yes',
				),
				'frontend_available' => true,
			)
		);

			$this->add_control(
				'fullscreen_control',
				array(
					'label'              => __( 'Fullscreen Control', 'uael' ),
					'type'               => Controls_Manager::SWITCHER,
					'default'            => 'yes',
					'label_on'           => __( 'On', 'uael' ),
					'label_off'          => __( 'Off', 'uael' ),
					'return_value'       => 'yes',
					'frontend_available' => true,
				)
			);

		$this->add_control(
			'fullscreen_pos',
			array(
				'label'              => __( 'Fullscreen Position', 'uael' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => $this->get_control_position_options(),
				'render_type'        => 'template',
				'condition'          => array(
					'fullscreen_control' => 'yes',
				),
				'frontend_available' => true,
			)
		);

			$this->add_control(
				'scroll_zoom',
				array(
					'label'        => __( 'Zoom on Scroll', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'yes',
					'label_on'     => __( 'On', 'uael' ),
					'label_off'    => __( 'Off', 'uael' ),
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'auto_center',
				array(
					'label'       => __( 'Map Alignment', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'center',
					'options'     => array(
						'center'   => __( 'Center', 'uael' ),
						'moderate' => __( 'Moderate', 'uael' ),
					),
					'description' => __( 'Generally, the map is center aligned. If you have multiple locations & wish to make your first location as a center point, then switch to moderate mode.', 'uael' ),
				)
			);

			$this->add_control(
				'cluster',
				array(
					'label'        => __( 'Cluster the Markers', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => __( 'On', 'uael' ),
					'label_off'    => __( 'Off', 'uael' ),
					'return_value' => 'yes',
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'cluster_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s admin link */
					'raw'             => sprintf( __( 'Enable this to group your markers together if you have many in a close proximity to only display one larger marker on your map.<br> Read %1$s this article %2$s for more information.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/what-are-cluster-markers-in-uael/" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array( 'cluster' => 'yes' ),
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Register GoogleMap Info Window Controls.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_info_window_controls() {

		$this->start_controls_section(
			'section_info_window_style',
			array(
				'label' => __( 'Info Window', 'uael' ),
			)
		);

			$this->add_control(
				'info_window_size',
				array(
					'label'       => __( 'Max Width for Info Window', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'default'     => array(
						'size' => 250,
						'unit' => 'px',
					),
					'range'       => array(
						'px' => array(
							'min'  => 50,
							'max'  => 1000,
							'step' => 1,
						),
					),
					'size_units'  => array( 'px' ),
					'label_block' => true,
				)
			);

			$this->add_responsive_control(
				'info_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .gm-style .uael-infowindow-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'title_spacing',
				array(
					'label'      => __( 'Spacing Between Title & Info.', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'default'    => array(
						'size' => 5,
						'unit' => 'px',
					),
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .gm-style .uael-infowindow-description' => 'margin-top: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .gm-style .uael-infowindow-title' => 'font-weight: bold;',
					),
				)
			);

			$this->add_control(
				'title_heading',
				array(
					'label' => __( 'Address Title', 'uael' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'title_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .gm-style .uael-infowindow-title' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_typography',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
					'selector' => '{{WRAPPER}} .gm-style .uael-infowindow-title',
				)
			);

			$this->add_control(
				'description_heading',
				array(
					'label' => __( 'Address Information', 'uael' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'description_color',
				array(
					'label'     => __( 'Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .gm-style .uael-infowindow-description' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'description_typography',
					'selector' => '{{WRAPPER}} .gm-style .uael-infowindow-description',
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Helpful Information.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_2 = UAEL_DOMAIN . 'docs/how-to-display-uaels-google-maps-widget-in-your-local-language/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started video » %2$s', 'uael' ), '<a href="https://www.youtube.com/watch?v=jWzW_oT1iSQ&index=6&list=PL1kzJGWGPrW_7HabOZHb6z88t_S8r-xAc" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Google Map localization » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}


	/**
	 * Renders Locations JSON array.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function get_locations() {

		$settings = $this->get_settings_for_display();

		$locations = array();

		foreach ( $settings['addresses'] as $index => $item ) {

			$latitude  = apply_filters( 'uael_google_map_latitude', $item['latitude'] );
			$longitude = apply_filters( 'uael_google_map_longitude', $item['longitude'] );

			$location_object = array(
				$latitude,
				$longitude,
			);

			$location_object[] = ( 'none' !== $item['marker_infowindow'] ) ? true : false;
			$location_object[] = apply_filters( 'uael_google_map_title', $item['map_title'] );
			$location_object[] = apply_filters( 'uael_google_map_description', $item['map_description'] );

			if (
				'custom' === $item['marker_icon_type'] && is_array( $item['marker_icon'] ) &&
				'' !== $item['marker_icon']['url']
			) {
				$location_object[] = 'custom';
				$location_object[] = $item['marker_icon']['url'];
				$location_object[] = $item['custom_marker_size']['size'];
			} else {
				$location_object[] = '';
				$location_object[] = '';
				$location_object[] = '';
			}

			$location_object[] = ( 'load' === $item['marker_infowindow'] ) ? 'iw_open' : '';

			$locations[] = $location_object;
		}

		return $locations;
	}

	/**
	 * Renders Map Control option JSON array.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function get_map_options() {

		$settings = $this->get_settings_for_display();

		return array(
			'zoom'              => ( ! empty( $settings['zoom']['size'] ) ) ? $settings['zoom']['size'] : 4,
			'mapTypeId'         => ( ! empty( $settings['type'] ) ) ? $settings['type'] : 'roadmap',
			'mapTypeControl'    => ( 'yes' === $settings['type_control'] ) ? true : false,
			'streetViewControl' => ( 'yes' === $settings['option_streeview'] ) ? true : false,
			'zoomControl'       => ( 'yes' === $settings['zoom_control'] ) ? true : false,
			'fullscreenControl' => ( 'yes' === $settings['fullscreen_control'] ) ? true : false,
			'gestureHandling'   => ( 'yes' === $settings['scroll_zoom'] ) ? true : false,
		);
	}

	/**
	 * Render Google Map output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		ob_start();

		$map_options     = $this->get_map_options();
		$map_options     = apply_filters( 'uael_map_options', $map_options );
		$locations       = $this->get_locations();
		$cluster_options = array();
		$cluster_attrs   = apply_filters( 'uael_cluster_options', $cluster_options );

		$this->add_render_attribute(
			'google-map',
			array(
				'id'                => 'uael-google-map-' . esc_attr( $this->get_id() ),
				'class'             => 'uael-google-map',
				'data-map_options'  => wp_json_encode( $map_options ),
				'data-cluster'      => $settings['cluster'],
				'data-cluster-attr' => wp_json_encode( $cluster_attrs ),
				'data-max-width'    => $settings['info_window_size']['size'],
				'data-locations'    => wp_json_encode( $locations ),
				'data-animate'      => $settings['animate'],
				'data-auto-center'  => $settings['auto_center'],
			)
		);

		if ( 'standard' !== $settings['skin'] ) {
			if ( 'custom' !== $settings['skin'] ) {
				$this->add_render_attribute( 'google-map', 'data-predefined-style', $settings['skin'] );
			} elseif ( ! empty( $settings['map_custom_style'] ) ) {
				$this->add_render_attribute( 'google-map', 'data-custom-style', $settings['map_custom_style'] );
			}
		}

		?>
		<div class="uael-google-map-wrap">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'google-map' ) ); ?>></div>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render GoogleMap widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

		function get_map_options( settings ) {

			return {
				'zoom' : ( '' != settings.zoom.size ) ? settings.zoom.size : 4,
				'mapTypeId' : ( '' != settings.type ) ? settings.type : 'roadmap',
				'mapTypeControl' : ( 'yes' == settings.type_control ) ? true : false,
				'streetViewControl' : ( 'yes' == settings.option_streeview ) ? true : false,
				'zoomControl' : ( 'yes' == settings.zoom_control ) ? true : false,
				'fullscreenControl' : ( 'yes' == settings.fullscreen_control ) ? true : false,
				'gestureHandling' : ( 'yes' == settings.scroll_zoom ) ? true : false
			};
		}

		function get_locations( settings ) {

			var all_locations = [];

			_.each( settings.addresses, function( item ) {

				var this_location = [ item.latitude, item.longitude ];

				if ( 'none' != item.marker_infowindow ) {
					this_location.push( true );
				} else {
					this_location.push( false );
				}
				this_location.push( item.map_title );
				this_location.push( item.map_description );

				if (
					'custom' == item.marker_icon_type &&
					'undefined' != typeof item.marker_icon &&
					'' != item.marker_icon.url
				) {
					this_location.push( 'custom' );
					this_location.push( item.marker_icon.url );
					this_location.push( item.custom_marker_size.size );
				} else {
					this_location.push( "" );
					this_location.push( "" );
					this_location.push( "" );
				}

				if ( 'load' == item.marker_infowindow ) {
					this_location.push( 'iw_open' );
				} else {
					this_location.push( "" );
				}

				all_locations.push( this_location );

			});

			return all_locations;
		}

		var map_options = get_map_options( settings );
		var locations 	= get_locations( settings );
		var cluster_parameter = [];
		var cluster_attrs = cluster_parameter;

		view.addRenderAttribute(
			'google-map',
			{
				'class' : 'uael-google-map',
				'data-map_options' : JSON.stringify( map_options ),
				'data-cluster' : settings.cluster,
				'data-cluster-attr': JSON.stringify( cluster_attrs ),
				'data-max-width' : settings.info_window_size.size,
				'data-locations' : JSON.stringify( locations ),
				'data-animate'   : settings.animate,
				'data-auto-center'   : settings.auto_center,
			}
		);

		if ( 'standard' != settings.skin ) {

			if ( 'custom' != settings.skin ) {

				view.addRenderAttribute( 'google-map', 'data-predefined-style', settings.skin );

			} else if ( '' != settings.map_custom_style ) {

				view.addRenderAttribute( 'google-map', 'data-custom-style', settings.map_custom_style );
			}
		}

		#>
		<div class="uael-google-map-wrap">
			<div {{{ view.getRenderAttributeString( 'google-map' ) }}}></div> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
		</div>

		<# elementorFrontend.hooks.doAction( 'frontend/element_ready/uael-google-map.default' ); #>
		<?php
	}
}
