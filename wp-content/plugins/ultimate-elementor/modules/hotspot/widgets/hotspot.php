<?php
/**
 * UAEL Hotspot.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Hotspot\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Hotspot.
 */
class Hotspot extends Common_Widget {

	/**
	 * Retrieve Hotspot Widget name.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Hotspot' );
	}

	/**
	 * Retrieve Hotspot Widget title.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Hotspot' );
	}

	/**
	 * Retrieve Hotspot Widget icon.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Hotspot' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.21.1
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Hotspot' );
	}

	/**
	 * Retrieve the list of styles needed for Hotspot.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends() {
		return array( 'uael-hotspot' );
	}

	/**
	 * Retrieve the list of scripts the Hotspot widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script', 'uael-hotspot' );
	}

	/**
	 * Register Hotspot controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_image_content_controls();
		$this->register_hotspot_content_controls();
		$this->register_tooltip_content_controls();
		$this->register_hotspot_tour_controls();
		$this->register_helpful_information();

		$this->register_image_style_controls();
		$this->register_hotspot_style_controls();
		$this->register_tooltip_style_controls();
		$this->register_hotspot_overlay_controls();
	}

	/**
	 * Register Hotspot Image Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_image_content_controls() {
		$this->start_controls_section(
			'section_image',
			array(
				'label' => __( 'Image', 'uael' ),
			)
		);

			$this->add_control(
				'image',
				array(
					'label'   => __( 'Choose Image', 'uael' ),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => array(
						'active' => true,
					),
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'image',
					'label'   => __( 'Image Size', 'uael' ),
					'default' => 'large',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Hotspot content Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_hotspot_content_controls() {
		$this->start_controls_section(
			'section_hotspot',
			array(
				'label'     => __( 'Markers', 'uael' ),
				'condition' => array(
					'image[url]!' => '',
				),
			)
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'marker_repeater' );

				$repeater->start_controls_tab( 'general_tab', array( 'label' => __( 'General', 'uael' ) ) );

					$repeater->add_control(
						'hotspot',
						array(
							'label'   => __( 'Marker Type', 'uael' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'icon',
							'options' => array(
								'text'  => __( 'Text', 'uael' ),
								'icon'  => __( 'Icon', 'uael' ),
								'image' => __( 'Image', 'uael' ),
							),
						)
					);

					$repeater->add_control(
						'text',
						array(
							'default'   => __( 'Marker Text', 'uael' ),
							'type'      => Controls_Manager::TEXT,
							'label'     => __( 'Title', 'uael' ),
							'separator' => 'none',
							'dynamic'   => array(
								'active' => true,
							),
						)
					);

		if ( UAEL_Helper::is_elementor_updated() ) {
			$repeater->add_control(
				'new_icon',
				array(
					'label'              => __( 'Marker Icon', 'uael' ),
					'label_block'        => true,
					'type'               => Controls_Manager::ICONS,
					'fa4compatibility'   => 'icon',
					'default'            => array(
						'value'   => 'fa fa-dot-circle-o',
						'library' => 'fa-solid',
					),
					'frontend_available' => true,
				)
			);
		} else {
			$repeater->add_control(
				'icon',
				array(
					'label'       => __( 'Marker Icon', 'uael' ),
					'label_block' => true,
					'type'        => Controls_Manager::ICON,
					'default'     => 'fa fa-dot-circle-o',
				)
			);
		}

					$repeater->add_control(
						'repeater_image',
						array(
							'label'     => __( 'Choose Image', 'uael' ),
							'type'      => Controls_Manager::MEDIA,
							'default'   => array(
								'url' => Utils::get_placeholder_image_src(),
							),
							'dynamic'   => array(
								'active' => true,
							),
							'condition' => array(
								'hotspot' => 'image',
							),
						)
					);

					$repeater->add_control(
						'hotspot_position_heading',
						array(
							'label' => __( 'Position', 'uael' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$repeater->add_responsive_control(
						'tooltip_pos_horizontal',
						array(
							'label'     => __( 'Horizontal position (%)', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'range'     => array(
								'px' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 0.1,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%; transform: translate(-{{SIZE}}%, 0);',
								'.rtl {{WRAPPER}} {{CURRENT_ITEM}}' => 'right: {{SIZE}}%; transform: translate(0, -{{SIZE}}%); left: unset;',
							),
						)
					);

					$repeater->add_responsive_control(
						'tooltip_pos_vertical',
						array(
							'label'     => __( 'Vertical position (%)', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'range'     => array(
								'px' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 0.1,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%; transform: translate(0, -{{SIZE}}%);',
							),
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab( 'content_tab', array( 'label' => __( 'Content', 'uael' ) ) );

					$repeater->add_control(
						'content',
						array(
							'label'   => __( 'Tooltip Content', 'uael' ),
							'type'    => Controls_Manager::WYSIWYG,
							'default' => __( 'This is a tooltip', 'uael' ),
							'dynamic' => array(
								'active' => true,
							),
						)
					);

					$repeater->add_control(
						'marker_link',
						array(
							'label'       => __( 'Marker Link', 'uael' ),
							'description' => __( 'Note: Applicable only when tooltips trigger is set to Hover. Also, when Hotspot Tour option is enabled link will not work', 'uael' ),
							'type'        => Controls_Manager::URL,
							'placeholder' => __( 'https://your-link.com', 'uael' ),
							'default'     => array(
								'url' => '',
							),
							'dynamic'     => array(
								'active' => true,
							),
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab( 'style_tab', array( 'label' => __( 'Style', 'uael' ) ) );

					$repeater->add_responsive_control(
						'hotspot_img_size',
						array(
							'label'      => __( 'Image Size', 'uael' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', 'em', 'rem' ),
							'range'      => array(
								'px' => array(
									'min' => 1,
									'max' => 100,
								),
							),
							'default'    => array(
								'size' => 30,
								'unit' => 'px',
							),
							'condition'  => array(
								'hotspot' => 'image',
							),
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'width: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$repeater->add_control(
						'hotspot_marker_options',
						array(
							'label'        => __( 'Override Global Settings', 'uael' ),
							'type'         => Controls_Manager::SWITCHER,
							'default'      => 'no',
							'label_on'     => __( 'Yes', 'uael' ),
							'label_off'    => __( 'No', 'uael' ),
							'return_value' => 'yes',
						)
					);

					$repeater->add_responsive_control(
						'hotspot_icon_size',
						array(
							'label'      => __( 'Icon Size', 'uael' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', 'em', 'rem' ),
							'range'      => array(
								'px' => array(
									'min' => 1,
									'max' => 100,
								),
							),
							'condition'  => array(
								'hotspot_marker_options' => 'yes',
								'hotspot'                => 'icon',
							),
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$repeater->add_control(
						'hotspot_icons_color',
						array(
							'label'     => __( 'Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content,
								{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content.uael-hotspot-anim:before' => 'color: {{VALUE}};',
								'{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content svg' => 'fill: {{VALUE}};',
							),
							'condition' => array(
								'hotspot_marker_options' => 'yes',
								'hotspot'                => array( 'text', 'icon' ),
							),
						)
					);

					$repeater->add_control(
						'hotspot_marker_bgcolor',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'condition' => array(
								'hotspot_marker_options' => 'yes',
							),
							'selectors' => array(
								'{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content,
								{{WRAPPER}} {{CURRENT_ITEM}} .uael-hotspot-content.uael-hotspot-anim:before' => 'background-color: {{VALUE}};',
							),
						)
					);

				$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'hotspots_list',
				array(
					'label'       => __( 'Hotspot', 'uael' ),
					'show_label'  => false,
					'type'        => Controls_Manager::REPEATER,
					'default'     => array(
						array(
							'text'     => 'Marker 1',
							'new_icon' => array(
								'value'   => 'fa fa-dot-circle-o',
								'library' => 'fa-solid',
							),
						),
					),
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{ text }}',
					'condition'   => array(
						'image[url]!' => '',
					),
					'separator'   => 'none',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Hotspot Tour Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_hotspot_tour_controls() {
		$this->start_controls_section(
			'section_tour',
			array(
				'label'     => __( 'Hotspot Tour', 'uael' ),
				'condition' => array(
					'hotspot_tooltip_data' => 'yes',
				),
			)
		);

		$this->add_control(
			'hotspot_tour',
			array(
				'label'        => __( 'Enable Tour', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'hotspot_tour_repeat',
			array(
				'label'        => __( 'Repeat Tour', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					'image[url]!'  => '',
					'hotspot_tour' => 'yes',
				),
			)
		);

		$this->add_control(
			'hotspot_tour_autoplay',
			array(
				'label'        => __( 'Autoplay', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					'image[url]!'  => '',
					'hotspot_tour' => 'yes',
				),
			)
		);

		if ( parent::is_internal_links() ) {

			$this->add_control(
				'help_doc_tour_repeat',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( 'Note: Tour autoplay option will only work on the frontend.', 'uael' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'image[url]!'           => '',
						'hotspot_tour'          => 'yes',
						'hotspot_tour_autoplay' => 'yes',

					),
				)
			);
		}

		$this->add_control(
			'tour_interval',
			array(
				'label'              => __( 'Interval between Tooltips (sec)', 'uael' ),
				'description'        => __( 'Next tooltip will be displayed after this time interval', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'sec' => array(
						'min' => 1,
						'max' => 9,
					),
				),
				'default'            => array(
					'size' => 4,
					'unit' => 'sec',
				),
				'condition'          => array(
					'image[url]!'           => '',
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_options',
			array(
				'label'     => __( 'Launch Tour', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'click',
				'options'   => array(
					'click' => __( 'On Button Click', 'uael' ),
					'auto'  => __( 'When Widget is in Viewport', 'uael' ),
				),
				'condition' => array(
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'hotspot_nonactive_markers',
			array(
				'label'        => __( 'Hide Non-Active Markers', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'condition'    => array(
					'image[url]!'  => '',
					'hotspot_tour' => 'yes',
				),
			)
		);

		$this->add_control(
			'overlay_button_heading',
			array(
				'label'     => __( 'Overlay Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
					'autoplay_options'      => 'click',
				),
			)
		);

		$this->add_control(
			'overlay_button_text',
			array(
				'label'     => __( 'Button Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Start Tour', 'uael' ),
				'condition' => array(
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
					'autoplay_options'      => 'click',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'overlay_button_size',
			array(
				'label'     => __( 'Size', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'sm',
				'options'   => array(
					'xs' => __( 'Extra Small', 'uael' ),
					'sm' => __( 'Small', 'uael' ),
					'md' => __( 'Medium', 'uael' ),
					'lg' => __( 'Large', 'uael' ),
					'xl' => __( 'Extra Large', 'uael' ),
				),
				'condition' => array(
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
					'autoplay_options'      => 'click',
				),
			)
		);

		$this->add_control(
			'overlay_pos_horizontal',
			array(
				'label'     => __( 'Horizontal position (%)', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
					'autoplay_options'      => 'click',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-overlay-button' => 'left: {{SIZE}}%; transform: translate(-{{SIZE}}%, 0);',
				),
			)
		);

		$this->add_control(
			'overlay_pos_vertical',
			array(
				'label'     => __( 'Vertical position (%)', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
					'autoplay_options'      => 'click',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-overlay-button' => 'top: {{SIZE}}%; transform: translate(0, -{{SIZE}}%);',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_helpful_information() {

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
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/hotspot-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_6',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s How Hotspot Tour works? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-hotspot-tour-works/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_2',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Styling a Marker in Hotspot widget » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/styling-hotspot-marker/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_3',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Unable to access the URL assigned to marker? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/unable-to-access-the-url-assigned-to-marker/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_4',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s How Max-height option works? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/how-max-height-option-works/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_5',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Unable to click on markers when Hotspot Tour option is enabled? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/unable-to-click-on-markers/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_7',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s doc link */
						'raw'             => sprintf( __( '%1$s Filters/Actions » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/filters-actions-for-hotspot-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

			$this->end_controls_section();
		}
	}

	/**
	 * Register Hotspot Tooltip Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_tooltip_content_controls() {
		$this->start_controls_section(
			'section_tooltip',
			array(
				'label' => __( 'Tooltip', 'uael' ),

			)
		);

		$this->add_control(
			'hotspot_tooltip_data',
			array(
				'label'        => __( 'Enable Tooltip', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
			)
		);

			$this->add_control(
				'position',
				array(
					'label'              => __( 'Position', 'uael' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => 'top',
					'options'            => array(
						'top'    => __( 'Top', 'uael' ),
						'bottom' => __( 'Bottom', 'uael' ),
						'left'   => __( 'Left', 'uael' ),
						'right'  => __( 'Right', 'uael' ),
					),
					'condition'          => array(
						'image[url]!'          => '',
						'hotspot_tooltip_data' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'trigger',
				array(
					'label'              => __( 'Display on', 'uael' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => 'hover',
					'options'            => array(
						'hover' => __( 'Hover', 'uael' ),
						'click' => __( 'Click', 'uael' ),
					),
					'condition'          => array(
						'image[url]!'          => '',
						'hotspot_tour!'        => 'yes',
						'hotspot_tooltip_data' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'arrow',
				array(
					'label'     => __( 'Arrow', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'true',
					'options'   => array(
						'true'  => __( 'Show', 'uael' ),
						'false' => __( 'Hide', 'uael' ),
					),
					'condition' => array(
						'image[url]!'          => '',
						'hotspot_tooltip_data' => 'yes',
					),
				)
			);

			$this->add_control(
				'distance',
				array(
					'label'       => __( 'Distance', 'uael' ),
					'description' => __( 'The distance between the marker and the tooltip.', 'uael' ),
					'type'        => Controls_Manager::SLIDER,
					'default'     => array(
						'size' => 6,
						'unit' => 'px',
					),
					'range'       => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'condition'   => array(
						'image[url]!'          => '',
						'hotspot_tooltip_data' => 'yes',
					),
				)
			);

			$this->add_control(
				'tooltip_anim',
				array(
					'label'     => __( 'Animation Type', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'fade',
					'options'   => array(
						'fade'  => __( 'Default', 'uael' ),
						'grow'  => __( 'Grow', 'uael' ),
						'swing' => __( 'Swing', 'uael' ),
						'slide' => __( 'Slide', 'uael' ),
						'fall'  => __( 'Fall', 'uael' ),
					),
					'condition' => array(
						'hotspot_tooltip_data' => 'yes',
					),
				)
			);

			$this->add_control(
				'hotspot_tooltip_adv',
				array(
					'label'        => __( 'Advanced Settings', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'condition'    => array(
						'hotspot_tooltip_data' => 'yes',
					),
				)
			);

			$this->add_control(
				'anim_duration',
				array(
					'label'              => __( 'Animation Duration', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'default'            => array(
						'size' => 350,
						'unit' => 'px',
					),
					'condition'          => array(
						'image[url]!'          => '',
						'hotspot_tooltip_adv'  => 'yes',
						'hotspot_tooltip_data' => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'tooltip_height',
				array(
					'label'              => __( 'Max Height', 'uael' ),
					'description'        => __( 'Note: If Tooltip Content is large, a vertical scroll will appear. Set Max Height to manage the content window height.', 'uael' ),
					'type'               => Controls_Manager::SLIDER,
					'range'              => array(
						'px' => array(
							'min' => 0,
							'max' => 1000,
						),
					),
					'condition'          => array(
						'image[url]!'          => '',
						'hotspot_tooltip_adv'  => 'yes',
						'hotspot_tooltip_data' => 'yes',
					),
					'selectors'          => array(
						'.tooltipster-base.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip .tooltipster-content' => 'max-height: {{SIZE}}px;',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'zindex',
				array(
					'label'       => __( 'Z-Index', 'uael' ),
					'description' => __( 'Note: Increase the z-index value if you are unable to see the tooltip. For example - 99, 999, 9999 ', 'uael' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => '99',
					'min'         => -9999999,
					'step'        => 1,
					'condition'   => array(
						'image[url]!'          => '',
						'hotspot_tooltip_adv'  => 'yes',
						'hotspot_tooltip_data' => 'yes',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Hotspot Image Style Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_image_style_controls() {
		$this->start_controls_section(
			'section_img_style',
			array(
				'label' => __( 'Image', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'opacity',
				array(
					'label'     => __( 'Opacity (%)', 'uael' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 1,
					),
					'range'     => array(
						'px' => array(
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .uael-hotspot img' => 'opacity: {{SIZE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Register Hotspot Style Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_hotspot_style_controls() {
		$this->start_controls_section(
			'section_hotspot_style',
			array(
				'label' => __( 'Marker', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'hotspot_anim',
				array(
					'label'        => __( 'Glow Effect', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'no',
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
				)
			);

			$this->add_responsive_control(
				'hotspot_bg_size',
				array(
					'label'      => __( 'Background Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
						'em' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'default'    => array(
						'size' => '2',
						'unit' => 'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} .uael-hotspot-content,
						{{WRAPPER}} .uael-hotspot-content.uael-hotspot-anim:before' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'hotspot_typography',
					'selector' => '{{WRAPPER}} .uael-hotspot-content',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
				)
			);

			$this->start_controls_tabs( 'hotspot_tabs_style' );

				$this->start_controls_tab(
					'hotspot_normal',
					array(
						'label' => __( 'Normal', 'uael' ),
					)
				);
					$this->add_control(
						'hotspot_color',
						array(
							'label'     => __( 'Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content,
								{{WRAPPER}} .uael-hotspot-content.uael-hotspot-anim:before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .uael-hotspot-content svg' => 'fill: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'hotspot_background_color',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_PRIMARY,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content,
								{{WRAPPER}} .uael-hotspot-content.uael-hotspot-anim:before' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'hotspot_border',
						array(
							'label'       => __( 'Border Style', 'uael' ),
							'type'        => Controls_Manager::SELECT,
							'default'     => 'none',
							'label_block' => false,
							'options'     => array(
								'none'   => __( 'None', 'uael' ),
								'solid'  => __( 'Solid', 'uael' ),
								'double' => __( 'Double', 'uael' ),
								'dotted' => __( 'Dotted', 'uael' ),
								'dashed' => __( 'Dashed', 'uael' ),
							),
							'selectors'   => array(
								'{{WRAPPER}} .uael-hotspot-content' => 'border-style: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'hotspot_border_size',
						array(
							'label'      => __( 'Border Width', 'uael' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px' ),
							'default'    => array(
								'top'    => '1',
								'bottom' => '1',
								'left'   => '1',
								'right'  => '1',
								'unit'   => 'px',
							),
							'condition'  => array(
								'hotspot_border!' => 'none',
							),
							'selectors'  => array(
								'{{WRAPPER}} .uael-hotspot-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_control(
						'hotspot_border_color',
						array(
							'label'     => __( 'Border Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_PRIMARY,
							),
							'condition' => array(
								'hotspot_border!' => 'none',
							),
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content' => 'border-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'hotspot_border_radius',
						array(
							'label'     => __( 'Rounded Corners', 'uael' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array(
								'size' => 100,
							),
							'range'     => array(
								'px' => array(
									'max' => 100,
									'min' => 0,
								),
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content,
								{{WRAPPER}} .uael-hotspot-content.uael-hotspot-anim:before' => 'border-radius: {{SIZE}}px;',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'      => 'hotspot_box_shadow',
							'selector'  => '{{WRAPPER}} .uael-hotspot-content',
							'separator' => '',
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'hotspot_hover',
					array(
						'label' => __( 'Hover / Active', 'uael' ),
					)
				);
					$this->add_control(
						'hotspot_hover_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content:hover, {{WRAPPER}} .uael-hotspot-tour .uael-hotspot-content.open' => 'color: {{VALUE}};',
								'{{WRAPPER}} .uael-hotspot-content:hover svg' => 'fill: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'hotspot_hover_bgcolor',
						array(
							'label'     => __( 'Background Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array(
								'default' => Global_Colors::COLOR_PRIMARY,
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content:hover,
								{{WRAPPER}} .uael-hotspot-content.uael-hotspot-anim:hover:before,
								{{WRAPPER}} .uael-hotspot-tour .uael-hotspot-content.open,
								{{WRAPPER}} .uael-hotspot-tour .open.uael-hotspot-anim:before' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'hotspot_hover_border_color',
						array(
							'label'     => __( 'Border Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'condition' => array(
								'hotspot_border!' => 'none',
							),
							'selectors' => array(
								'{{WRAPPER}} .uael-hotspot-content:hover, {{WRAPPER}} .uael-hotspot-tour .uael-hotspot-content.open' => 'border-color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'      => 'hotspot_hover_box_shadow',
							'selector'  => '{{WRAPPER}} .uael-hotspot-content:hover, {{WRAPPER}} .uael-hotspot-tour .uael-hotspot-content.open',
							'separator' => '',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Hotspot General Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_tooltip_style_controls() {

		$this->start_controls_section(
			'section_tooltip_style',
			array(
				'label'     => __( 'Tooltip', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'hotspot_tooltip_data' => 'yes',
				),
			)
		);

			$this->add_control(
				'uael_tooltip_align',
				array(
					'label'     => __( 'Text Alignment', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => __( 'Left', 'uael' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'uael' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Right', 'uael' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'selectors' => array(
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip .tooltipster-content' => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'uael_tooltip_typography',
					'label'    => __( 'Typography', 'uael' ),
					'selector' => '.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip .tooltipster-content, .tooltipster-sidetip.uael-tooltip-wrap-{{ID}} .uael-tour li a',
				)
			);

			$this->add_control(
				'uael_tooltip_color',
				array(
					'label'     => __( 'Text Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-tooltipster-active.uael-hotspot-tooltip .tooltipster-content, .tooltipster-sidetip.uael-tooltip-wrap-{{ID}} .uael-tour li a, .tooltipster-sidetip.uael-tooltip-wrap-{{ID}} .uael-tour-active .uael-hotspot-end a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'uael_tooltip_bgcolor',
				array(
					'label'     => __( 'Background Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-tooltipster-active.uael-hotspot-tooltip .tooltipster-box' => 'background-color: {{VALUE}};',
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip.tooltipster-noir.tooltipster-bottom .tooltipster-arrow-background' => 'border-bottom-color: {{VALUE}};',
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip.tooltipster-noir.tooltipster-left .tooltipster-arrow-background' => 'border-left-color: {{VALUE}};',
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip.tooltipster-noir.tooltipster-right .tooltipster-arrow-background' => 'border-right-color: {{VALUE}};',
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip.tooltipster-noir.tooltipster-top .tooltipster-arrow-background' => 'border-top-color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'uael_tooltip_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'default'    => array(
						'top'    => '20',
						'bottom' => '20',
						'left'   => '20',
						'right'  => '20',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip .tooltipster-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'uael_tooltip_radius',
				array(
					'label'      => __( 'Rounded Corners', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'default'    => array(
						'top'    => '10',
						'bottom' => '10',
						'left'   => '10',
						'right'  => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip .tooltipster-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'uael_tooltip_shadow',
					'selector'  => '.tooltipster-sidetip.uael-tooltip-wrap-{{ID}}.uael-hotspot-tooltip .tooltipster-box',
					'separator' => '',
				)
			);

		$this->end_controls_section();

	}

	/**
	 * Register Hotspot Tour Style Controls.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function register_hotspot_overlay_controls() {

		$this->start_controls_section(
			'section_overlay',
			array(
				'label'     => __( 'Hotspot Tour', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'hotspot_tooltip_data'  => 'yes',
					'hotspot_tour'          => 'yes',
					'hotspot_tour_autoplay' => 'yes',
					'autoplay_options'      => 'click',
				),
			)
		);

			$this->add_control(
				'overlay_bg_color',
				array(
					'label'     => __( 'Overlay Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => 'rgba(0, 0, 0, 0.57)',
					'selectors' => array(
						'{{WRAPPER}} .uael-hotspot-overlay' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'hotspot_tour'          => 'yes',
						'hotspot_tour_autoplay' => 'yes',
						'autoplay_options'      => 'click',
					),
				)
			);

			$this->add_control(
				'overlay_button_style',
				array(
					'label'     => __( 'Overlay Button', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'hotspot_tour'          => 'yes',
						'hotspot_tour_autoplay' => 'yes',
						'autoplay_options'      => 'click',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'overlay_button_typography',
					'label'     => __( 'Typography', 'uael' ),
					'selector'  => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
					'condition' => array(
						'hotspot_tour'          => 'yes',
						'hotspot_tour_autoplay' => 'yes',
						'autoplay_options'      => 'click',
					),
				)
			);

			$this->add_responsive_control(
				'overlay_button_custom_padding',
				array(
					'label'      => __( 'Padding', 'uael' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'hotspot_tour'          => 'yes',
						'hotspot_tour_autoplay' => 'yes',
						'autoplay_options'      => 'click',
					),
				)
			);

			$this->start_controls_tabs( 'overlay_tabs_button_style' );

				$this->start_controls_tab(
					'overlay_button_normal',
					array(
						'label'     => __( 'Normal', 'uael' ),
						'condition' => array(
							'hotspot_tour'          => 'yes',
							'hotspot_tour_autoplay' => 'yes',
							'autoplay_options'      => 'click',
						),
					)
				);
					$this->add_control(
						'overlay_button_text_color',
						array(
							'label'     => __( 'Text Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'condition' => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'           => 'overlay_button_bgcolor',
							'label'          => __( 'Background Color', 'uael' ),
							'types'          => array( 'classic', 'gradient' ),
							'selector'       => '{{WRAPPER}} .elementor-button',
							'condition'      => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
							'fields_options' => array(
								'color' => array(
									'global' => array(
										'default' => Global_Colors::COLOR_ACCENT,
									),
								),
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'      => 'overlay_button_border',
							'selector'  => '{{WRAPPER}} .elementor-button',
							'condition' => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
						)
					);

					$this->add_control(
						'overlay_button_radius',
						array(
							'label'      => __( 'Rounded Corners', 'uael' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%' ),
							'default'    => array(
								'top'    => '0',
								'bottom' => '0',
								'left'   => '0',
								'right'  => '0',
								'unit'   => 'px',
							),
							'selectors'  => array(
								'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
							'condition'  => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'      => 'overlay_button_box_shadow',
							'selector'  => '{{WRAPPER}} .elementor-button',
							'condition' => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'overlay_button_hover',
					array(
						'label'     => __( 'Hover', 'uael' ),
						'condition' => array(
							'hotspot_tour'          => 'yes',
							'hotspot_tour_autoplay' => 'yes',
							'autoplay_options'      => 'click',
						),
					)
				);
					$this->add_control(
						'overlay_button_hover_color',
						array(
							'label'     => __( 'Text Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'condition' => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'           => 'overlay_button_hover_bgcolor',
							'label'          => __( 'Background Hover Color', 'uael' ),
							'types'          => array( 'classic', 'gradient' ),
							'selector'       => '{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover',
							'condition'      => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
							'fields_options' => array(
								'color' => array(
									'global' => array(
										'default' => Global_Colors::COLOR_ACCENT,
									),
								),
							),
						)
					);

					$this->add_control(
						'overlay_button_border_hover_color',
						array(
							'label'     => __( 'Border Hover Color', 'uael' ),
							'type'      => Controls_Manager::COLOR,
							'condition' => array(
								'hotspot_tour'          => 'yes',
								'hotspot_tour_autoplay' => 'yes',
								'autoplay_options'      => 'click',
							),
							'selectors' => array(
								'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover' => 'border-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get Data Attributes.
	 *
	 * @since 1.9.0
	 * @param array   $settings The settings array.
	 * @param boolean $device specifies mobile devices.
	 * @return string Data Attributes
	 * @access public
	 */
	public function get_data_attrs( $settings, $device ) {

		$marker_length = count( $settings['hotspots_list'] );

		$side             = $settings['position'];
		$trigger          = '';
		$tour_autoplay    = '';
		$tour_repeat      = '';
		$tour_overlay     = '';
		$arrow            = $settings['arrow'];
		$animation        = $settings['tooltip_anim'];
		$zindex           = ( 'yes' === $settings['hotspot_tooltip_adv'] ) ? $settings['zindex'] : 99;
		$interval_val     = empty( $settings['tour_interval']['size'] ) ? '' : $settings['tour_interval']['size'];
		$delay            = 300;
		$anim_duration    = ( 'yes' === $settings['hotspot_tooltip_adv'] ) ? $settings['anim_duration']['size'] : 350;
		$distance         = ( '' !== $settings['distance']['size'] ) ? $settings['distance']['size'] : 6;
		$action_auto      = ( 'auto' === $settings['autoplay_options'] ) ? 'auto' : '';
		$hotspot_viewport = 90;
		$maxwidth         = 250;
		$minwidth         = 0;

		if ( '' === $interval_val ) {
			$tour_interval = 4000;
		} else {
			$tour_interval = $interval_val * 1000;
		}

		if ( 'yes' === $settings['hotspot_tour'] && 'yes' === $settings['hotspot_tooltip_data'] ) {
			$trigger = 'custom';

			if ( 'yes' === $settings['hotspot_tour_autoplay'] ) {
				$tour_autoplay = 'yes';
				$tour_overlay  = ( 'click' === $settings['autoplay_options'] ) ? 'yes' : 'no';
				if ( 'yes' === $settings['hotspot_tour_repeat'] ) {
					$tour_repeat = 'yes';
				} else {
					$tour_repeat = 'no';
				}
			} else {
				$tour_autoplay = 'no';
				$tour_overlay  = 'no';
				if ( 'yes' === $settings['hotspot_tour_repeat'] ) {
					$tour_repeat = 'yes';
				} else {
					$tour_repeat = 'no';
				}
			}
		} else {
			if ( true === $device ) {
				$trigger = 'click';
			} else {
				$trigger = $settings['trigger'];
			}
		}

		$hotspot_viewport = apply_filters( 'uael_hotspot_viewport', $hotspot_viewport );

		$maxwidth = apply_filters( 'uael_tooltip_maxwidth', $maxwidth, $settings );
		$minwidth = apply_filters( 'uael_tooltip_minwidth', $minwidth, $settings );

		$data_attr  = 'data-side="' . $side . '" ';
		$data_attr .= 'data-hotspottrigger="' . $trigger . '" ';
		$data_attr .= 'data-arrow="' . $arrow . '" ';
		$data_attr .= 'data-distance="' . $distance . '" ';
		$data_attr .= 'data-delay="' . $delay . '" ';
		$data_attr .= 'data-animation="' . $animation . '" ';
		$data_attr .= 'data-animduration="' . $anim_duration . '" ';
		$data_attr .= 'data-zindex="' . $zindex . '" ';
		$data_attr .= 'data-length="' . $marker_length . '" ';
		$data_attr .= 'data-autoplay="' . $tour_autoplay . '" ';
		$data_attr .= 'data-repeat="' . $tour_repeat . '" ';
		$data_attr .= 'data-tourinterval="' . $tour_interval . '" ';
		$data_attr .= 'data-overlay="' . $tour_overlay . '" ';
		$data_attr .= 'data-autoaction="' . $action_auto . '" ';
		$data_attr .= 'data-hotspotviewport="' . $hotspot_viewport . '" ';
		$data_attr .= 'data-tooltip-maxwidth="' . $maxwidth . '" ';
		$data_attr .= 'data-tooltip-minwidth="' . $minwidth . '" ';

		return $data_attr;

	}

	/**
	 * Render Hotspot output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.9.0
	 * @access protected
	 */
	protected function render() {
		$html     = '';
		$settings = $this->get_settings_for_display();
		$node_id  = $this->get_id();
		$device   = false;

		$iphone  = ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== ( stripos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'iPhone' ) ) ? true : false ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__
		$ipad    = ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== ( stripos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'iPad' ) ) ? true : false ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__
		$android = ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== ( stripos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'Android' ) ) ? true : false ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__

		if ( $iphone || $ipad || $android ) {
			$device = true;
		}

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}

		$hotspot_tour   = '';
		$tour_enable    = '';
		$hide_nonactive = '';

		$tooltip_enable = ( 'yes' === $settings['hotspot_tooltip_data'] ) ? 'uael-hotspot-tooltip-yes' : '';

		if ( 'yes' === $settings['hotspot_tour'] ) {
			$hotspot_tour = 'uael-tour-active';
			$tour_enable  = ' uael-hotspot-tour';
			if ( 'yes' === $settings['hotspot_nonactive_markers'] ) {
				$hide_nonactive = 'uael-hotspot-marker-nonactive';
			}
		} else {
			$hotspot_tour = 'uael-tour-inactive';
		}
		?>

		<div class="uael-hotspot <?php echo esc_attr( $tour_enable ) . ' ' . esc_attr( $tooltip_enable ); ?> ">
			<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings ) ); ?>
			<?php
			if ( $settings['hotspots_list'] ) :
				$counter = 1;
				?>

			<div class="uael-hotspot-container" <?php echo $this->get_data_attrs( $settings, $device ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

				<?php
				foreach ( $settings['hotspots_list'] as $index => $item ) :

					$hotspot_img  = false;
					$tooltip_data = $this->get_repeater_setting_key( 'content', 'hotspots_list', $index );
					$content_id   = $this->get_id() . '-' . $item['_id'];
					$hotspot_glow = '';

					if ( ! empty( $item['marker_link']['url'] ) ) {

						$this->add_link_attributes( 'url-' . $item['_id'], $item['marker_link'] );

						$link = $this->get_render_attribute_string( 'url-' . $item['_id'] );
					}

					$this->add_render_attribute(
						$tooltip_data,
						array(
							'class' => 'uael-tooltip-text ' . $hotspot_tour,
							'id'    => 'uael-tooltip-content-' . $content_id,
						)
					);

					if ( 'image' === $item['hotspot'] && ! empty( $item['repeater_image'] ) ) {
						$hotspot_img = true;

						$this->add_render_attribute( 'hotspot_image' . $index, 'src', $item['repeater_image']['url'] );
						$this->add_render_attribute( 'hotspot_image' . $index, 'class', 'uael-hotspot-img image-' . $index );
						$image_html = '<img ' . $this->get_render_attribute_string( 'hotspot_image' . $index ) . '>';
					}

					if ( 'yes' === $settings['hotspot_anim'] ) {
						$hotspot_glow = ' uael-hotspot-anim';
					}
					?>
					<?php if ( ! empty( $item['marker_link']['url'] ) ) { ?>
						<?php if ( 'yes' === $settings['hotspot_tooltip_data'] ) { ?>
							<?php if ( 'yes' !== $settings['hotspot_tour'] && 'hover' === $settings['trigger'] ) { ?>
								<a <?php echo $link; ?> > <?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php } ?>
						<?php } else { ?>
							<a <?php echo $link; ?> ><?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php } ?>
					<?php } ?>
					<span class="uael-tooltip elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
						<span class="uael-hotspot-main-<?php echo esc_attr( $node_id ); ?> uael-hotspot-content<?php echo esc_attr( $hotspot_glow ); ?> <?php echo esc_attr( $hide_nonactive ); ?>" id="uael-tooltip-id-<?php echo esc_attr( $node_id ) . '-' . esc_attr( $counter ); ?>" data-uaeltour="<?php echo esc_attr( $counter ); ?>" data-tooltip-content="<?php echo '#uael-tooltip-content-' . esc_attr( $content_id ); ?>">
							<?php
							if ( 'icon' === $item['hotspot'] ) {
								if ( UAEL_Helper::is_elementor_updated() ) {
									$marker_migrated = isset( $item['__fa4_migrated']['new_icon'] );

									$marker_is_new = ! isset( $item['icon'] );

									if ( $marker_migrated || $marker_is_new ) {
										\Elementor\Icons_Manager::render_icon( $item['new_icon'], array( 'aria-hidden' => 'true' ) );
									} elseif ( ! empty( $item['icon'] ) ) {
										$icon_data = 'icon-' . $index;
										$this->add_render_attribute( $icon_data, 'class', esc_attr( $item['icon'] ) );
										?>
										<i <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_data ) ); ?> aria-hidden="true"></i>
										<?php
									}
								} elseif ( ! empty( $item['icon'] ) ) {
									$icon_data = 'icon-' . $index;
									$this->add_render_attribute( $icon_data, 'class', esc_attr( $item['icon'] ) );
									?>
									<i <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_data ) ); ?> aria-hidden="true"></i>
									<?php
								}
							} elseif ( $hotspot_img ) {
								echo wp_kses_post( $image_html );
							} else {
								echo '<span class="uael-hotspot-text">' . wp_kses_post( $item['text'] ) . '</span>';
							}
							?>
						</span>
					</span>
					<?php if ( ! empty( $item['marker_link']['url'] ) ) { ?>
						<?php if ( 'yes' === $settings['hotspot_tooltip_data'] ) { ?>
							<?php if ( 'yes' !== $settings['hotspot_tour'] && 'hover' === $settings['trigger'] ) { ?>
								</a>
							<?php } ?>
						<?php } else { ?>
							</a>
						<?php } ?>
					<?php } ?>

					<?php
						$next_label     = __( 'Next', 'uael' );
						$previous_label = __( 'Previous', 'uael' );
						$endtour_label  = __( 'End Tour', 'uael' );
						$next           = apply_filters( 'uael_hotspot_next_label', $next_label, $settings );
						$previous       = apply_filters( 'uael_hotspot_previous_label', $previous_label, $settings );
						$end            = apply_filters( 'uael_hotspot_endtour_label', $endtour_label, $settings );
					?>
					<?php if ( 'yes' === $settings['hotspot_tooltip_data'] ) { ?>
						<span class="uael-tooltip-container">
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( $tooltip_data ) ); ?>><?php echo wp_kses_post( $this->parse_text_editor( $item['content'] ) ); ?>
								<span class="uael-tour"><span class="uael-actual-step"><?php echo esc_html( $counter ); ?> <?php echo esc_attr_e( 'of', 'uael' ); ?> <?php echo count( $settings['hotspots_list'] ); ?></span><ul><li><a href="#0" class="uael-prev-<?php echo esc_attr( $node_id ); ?>" data-tooltipid="<?php echo esc_attr( $counter ); ?>">&#171; <?php echo esc_html( $previous ); ?></a></li><li><a href="#0" class="uael-next-<?php echo esc_attr( $node_id ); ?>" data-tooltipid="<?php echo esc_attr( $counter ); ?>"><?php echo esc_html( $next ); ?> &#187;</a></li></ul></span>
								<?php
								if ( 'yes' === $settings['hotspot_tour_autoplay'] && 'yes' === $settings['hotspot_tour_repeat'] ) {
									?>
									<span class="uael-hotspot-end"><a href="#" class="uael-tour-end-<?php echo esc_attr( $node_id ); ?>"><?php echo esc_html( $end ); ?></a></span><?php } ?>
							</span>
						</span>
					<?php } ?>
					<?php
					$counter++;
					endforeach;
				?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['hotspot_tour'] && 'yes' === $settings['hotspot_tour_autoplay'] && 'yes' === $settings['hotspot_tooltip_data'] && 'click' === $settings['autoplay_options'] ) { ?>
				<?php
					$this->add_render_attribute( 'button', 'class', 'elementor-button' );
				if ( ! empty( $settings['overlay_button_size'] ) ) {
					$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['overlay_button_size'] );
				}
				?>
				<div class="uael-hotspot-overlay">
					<div class="uael-overlay-button">
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
							<span class="elementor-button-text elementor-inline-editing" data-elementor-setting-key="overlay_button_text" data-elementor-inline-editing-toolbar="none"><?php echo wp_kses_post( $settings['overlay_button_text'] ); ?></span>
						</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Hotspot widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.1
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var length = settings.hotspots_list.length;

			function data_attributes() {
				var side			= settings.position;
				var trigger			= '';
				var tour_autoplay 	= '';
				var tour_repeat  	= '';
				var tour_overlay  	= '';
				var arrow			= settings.arrow;
				var animation		= settings.tooltip_anim;
				var zindex			= ( 'yes' == settings.hotspot_tooltip_adv ) ? settings.zindex : 99;
				var interval_val 	= settings.tour_interval.size;
				var action_auto   	= ( 'auto' == settings.autoplay_options ) ? 'auto' : '';

				var delay			= 300;

				var anim_duration			= ( 'yes' == settings.hotspot_tooltip_adv ) ? settings.anim_duration.size : 350;

				var distance			= ( '' != settings.distance.size ) ? settings.distance.size : 6;

				if( '' == interval_val ) {
					tour_interval = 4000;
				} else {
					tour_interval = interval_val * 1000;
				}

				if ( 'yes' == settings.hotspot_tour && 'yes' == settings.hotspot_tooltip_data ) {
					trigger = 'custom';

					if ( 'yes' == settings.hotspot_tour_autoplay ) {
						tour_autoplay = 'yes';
						tour_overlay = ( 'click' == settings.autoplay_options ) ? 'yes' : 'no';

						if ( 'yes' == settings.hotspot_tour_repeat ) {
							tour_repeat = 'yes';
						} else {
							tour_repeat = 'no';
						}
					} else {
						tour_autoplay = 'no';
						tour_overlay = 'no';

						if ( 'yes' == settings.hotspot_tour_repeat ) {
							tour_repeat = 'yes';
						} else {
							tour_repeat = 'no';
						}
					}
				} else {
					trigger = settings.trigger;
				}

				var data_attr  = 'data-side="' + side + '" ';
					data_attr += 'data-hotspottrigger="' + trigger + '" ';
					data_attr += 'data-arrow="' + arrow + '" ';
					data_attr += 'data-distance="' + distance + '" ';
					data_attr += 'data-delay="' + delay + '" ';
					data_attr += 'data-animation="' + animation + '" ';
					data_attr += 'data-animduration="' + anim_duration + '" ';
					data_attr += 'data-zindex="' + zindex + '" ';
					data_attr += 'data-length="' + length + '" ';
					data_attr += 'data-autoplay="' + tour_autoplay + '" ';
					data_attr += 'data-repeat="' + tour_repeat + '" ';
					data_attr += 'data-tourinterval="' + tour_interval + '" ';
					data_attr += 'data-overlay="' + tour_overlay + '" ';
					data_attr += 'data-autoaction="' + action_auto + '" ';

				return data_attr;
			}
		#>
		<# if ( '' !== settings.image.url ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};
			var image_url = elementor.imagesManager.getImageUrl( image );
			var node_id = view.$el.data('id');
			var hotspot_tour = '';
			var tour_enable = '';
			var hide_nonactive = '';

			var tooltip_enable = ( 'yes' == settings.hotspot_tooltip_data ) ? ' uael-hotspot-tooltip-yes' : '';

			if ( 'yes' == settings.hotspot_tour ) {
				hotspot_tour = 'uael-tour-active';
				tour_enable  = ' uael-hotspot-tour';
			} else {
				hotspot_tour = 'uael-tour-inactive';
			}

			if( 'yes' == settings.hotspot_tour && 'yes' == settings.hotspot_nonactive_markers ) {
				hide_nonactive = 'uael-hotspot-marker-nonactive';
			}

			var iconsHTML = {};
			#>
			<# var param = data_attributes(); #>

			<div class="uael-hotspot{{ tour_enable }}{{ tooltip_enable }}">
				<img src="{{ image_url }}"/>
				<#
				if ( settings.hotspots_list ) {
					var counter = 1; #>
					<div class="uael-hotspot-container" {{{ param }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# _.each( settings.hotspots_list, function( item, index ) {
							var hotspot_glow = '';
							if ( 'yes' == settings.hotspot_anim ) {
								hotspot_glow = ' uael-hotspot-anim';
							}

							if ( '' != item.marker_link.url ) {
								view.addRenderAttribute( 'url-' + item._id, 'href', item.marker_link.url );
							}
							#>

							<# if ( '' != item.marker_link.url ) { #>
								<# if ( 'yes' == settings.hotspot_tooltip_data ) { #>
									<# if ( 'yes' != settings.hotspot_tour && 'hover' == settings.trigger ) { #>
										<a {{{ view.getRenderAttributeString( 'url-' + item._id ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
									<# } #>
								<# } else { #>
									<a {{{ view.getRenderAttributeString( 'url-' + item._id ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
								<# } #>
							<# } #>

							<span class="uael-tooltip elementor-repeater-item-{{ item._id }}">
								<span class="uael-hotspot-main-{{ node_id }} uael-hotspot-content{{ hotspot_glow }} {{ hide_nonactive }}" id="uael-tooltip-id-{{ node_id }}-{{ counter }}" data-uaeltour="{{ counter }}" data-tooltip-content="#uael-tooltip-content-{{ item._id }}">
									<# if ( 'icon' === item.hotspot ) { #>
										<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>
											<#
											iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.new_icon, { 'aria-hidden': true }, 'i' , 'object' );
											migrated = elementor.helpers.isIconMigrated( item, 'new_icon' ); #>
											<# if ( ( ! item.icon || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
												{{{ iconsHTML[ index ].value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
											<# } else { #>
												<i class="{{ item.icon }}" aria-hidden="true"></i>
											<# } #>
										<?php } else { ?>
											<i class="{{ item.icon }}" aria-hidden="true"></i>
										<?php } ?>

									<# } else if ( 'image' === item.hotspot ) { #>
										<img src="{{ item.repeater_image.url }}" class="uael-hotspot-img" />
									<# } else { #>
											<span class="uael-hotspot-text">{{ item.text }}</span>
									<# } #>
								</span>
							</span>
							<# if ( '' != item.marker_link.url ) { #>
								<# if ( 'yes' == settings.hotspot_tooltip_data ) { #>
									<# if ( 'yes' != settings.hotspot_tour && 'hover' == settings.trigger ) { #>
										</a>
									<# } #>
								<# } else { #>
									</a>
								<# } #>
							<# } #>
							<# if ( 'yes' == settings.hotspot_tooltip_data ) { #>
								<span class="uael-tooltip-container">
									<span class="uael-tooltip-text {{ hotspot_tour }}" id="uael-tooltip-content-{{ item._id }}">{{ item.content }}
										<span class="uael-tour"><span class="uael-actual-step">{{ counter }} of {{ length }}</span><ul><li><a href="#0" class="uael-prev-{{ node_id }}" data-tooltipid="{{ counter }}">&#171; <?php esc_html_e( 'Previous', 'uael' ); ?></a></li><li><a href="#0" class="uael-next-{{ node_id }}" data-tooltipid="{{ counter }}"><?php esc_html_e( 'Next', 'uael' ); ?> &#187;</a></li></ul></span>
									</span>
								</span>
							<# } #>
							<# counter++;
						}); #>
					</div>
				<# } #>
				<# if( 'yes' == settings.hotspot_tour && 'yes' == settings.hotspot_tour_autoplay && 'yes' == settings.hotspot_tooltip_data && 'click' == settings.autoplay_options ) { #>
					<#
						view.addRenderAttribute( 'button', 'class', 'elementor-button' );
						if ( '' != settings.overlay_button_size ) {
							view.addRenderAttribute( 'button', 'class', 'elementor-size-' + settings.overlay_button_size );
						}
					#>
					<div class="uael-hotspot-overlay">
						<div class="uael-overlay-button">
							<a {{{ view.getRenderAttributeString( 'button' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
								<span class="elementor-button-text elementor-inline-editing" data-elementor-setting-key="overlay_button_text" data-elementor-inline-editing-toolbar="none">{{ settings.overlay_button_text }}</span>
							</a>
						</div>
					</div>
				<# } #>
			</div>
		<# } #>
		<# elementorFrontend.hooks.doAction( 'frontend/element_ready/uael-hotspot.default' ); #>
		<?php
	}
}
