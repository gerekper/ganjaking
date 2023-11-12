<?php
/**
 * UAEL Party Propz Extension feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\PartyPropzExtension;

use Elementor\Controls_Manager;
use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;
use Elementor\Utils;
use Elementor\Group_Control_Css_Filter;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Module should load or not.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'PartyPropzExtension' );
	}

	/**
	 * Check if this is a widget.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public function is_widget() {
		return false;
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'PartyPropzExtension',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		if ( UAEL_Helper::is_widget_active( 'PartyPropzExtension' ) ) {
			$this->add_actions();

		}
	}

	/**
	 * Add actions and set scripts dependencies required to run the widget.
	 *
	 * @since 1.35.0
	 * @access protected
	 */
	protected function add_actions() {

		add_action( 'wp_footer', array( $this, 'enqueue_scripts' ) );
		// Activate for column.
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( __CLASS__, 'register_section_control' ), 1, 2 );
		// Activate for section.
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( __CLASS__, 'register_section_control' ), 1, 2 );
		// for editor.
		add_action( 'elementor/column/print_template', array( $this, 'print_template_for_section' ), 10, 2 );
		add_action( 'elementor/section/print_template', array( $this, 'print_template_for_section' ), 10, 2 );
		// for frontend.
		add_action( 'elementor/frontend/column/after_render', array( $this, '_before_render_for_section' ), 10, 1 );
		add_action( 'elementor/frontend/section/after_render', array( $this, '_before_render_for_section' ), 10, 1 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( __CLASS__, 'register_widget_control' ), 1, 2 );
		// for editor.
		add_action( 'elementor/widget/print_template', array( $this, '_print_template_for_widget' ), 10, 2 );
		// for frontend.
		add_action( 'elementor/widget/before_render_content', array( $this, '_before_render_for_widget' ), 10, 1 );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the scripts defined as extension dependencies and enqueues them.
	 *
	 * @since 1.35.0
	 * @access public
	 */
	public function enqueue_scripts() {
		$is_elementor = false;

		if ( false !== get_the_ID() ) {
			$ele_document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );

			if ( ! is_bool( $ele_document ) ) {
				$is_elementor = $ele_document->is_built_with_elementor();
			}
		}
		if ( ( true === \Elementor\Plugin::$instance->frontend->has_elementor_in_page() ) || ( true === $is_elementor ) || ( function_exists( 'elementor_location_exits' ) && ( elementor_location_exits( 'archive', true ) || elementor_location_exits( 'single', true ) ) ) ) {
			wp_add_inline_script(
				'elementor-frontend',
				'jQuery( window ).on( "elementor/frontend/init", function() {
					elementorFrontend.hooks.addAction( "frontend/element_ready/global", function( $scope, $ ){
						if ( "undefined" == typeof $scope ) {
							return;
						}

						if ( $scope.hasClass( "uael-party-propz-yes" ) ) {
							element_type = $scope.data( "element_type" );
							extension_html = $scope.next();
							if( $scope.next().hasClass( "uael-party-propz-wrap" ) ) {
								if( element_type == "section" ) {

									section_wrap = $scope.find( ".elementor-container" );

									section_wrap.before( extension_html );

								} else if( element_type == "column" ) {

									if( $scope.find( ".elementor-column-wrap" ).length == 0 ) {

										$scope.append( extension_html );

									} else if( $scope.find( ".elementor-column-wrap" ).length != 0 ) {

											$column = $scope.find( ".elementor-column-wrap" );
											$column.after( extension_html );

									}
								}
							}
						}
					});
				}); '
			);
		}
	}

	/**
	 * Added Party Propz Extension controls for section/column.
	 *
	 * @since 1.35.0
	 *
	 * @param array $element returns controls array.
	 * @param array $args return arguments.
	 * @access public
	 */
	public static function register_section_control( $element, $args ) {

			$element->start_controls_section(
				'party_propz_section',
				array(
					'tab'   => Controls_Manager::TAB_ADVANCED,
					/* translators: %s Admin name */
					'label' => sprintf( __( '%1s - Party Propz', 'uael' ), UAEL_PLUGIN_SHORT_NAME ),
				)
			);

			$element->add_control(
				'enable_party_propz',
				array(
					'label'        => __( 'Enable', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'On', 'uael' ),
					'label_off'    => __( 'Off', 'uael' ),
					'return_value' => 'yes',
					'default'      => '',
					'prefix_class' => 'uael-party-propz-',
					'render_type'  => 'template',
				)
			);

			$element->add_control(
				'party_propz_image_type',
				array(
					'label'     => __( 'Image Type', 'uael' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'image' => array(
							'title' => __( 'Image', 'uael' ),
							'icon'  => 'fa fa-image',
						),
						'icon'  => array(
							'title' => __( 'Font Icon', 'uael' ),
							'icon'  => 'fa fa-info-circle',
						),
					),
					'default'   => 'image',
					'toggle'    => false,
					'condition' => array(
						'enable_party_propz' => 'yes',
					),
				)
			);

		if ( UAEL_Helper::is_elementor_updated() ) {
			$element->add_control(
				'new_party_propz_select_icon',
				array(
					'label'            => __( 'Select Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'party_propz_select_icon',
					'default'          => array(
						'value'   => 'fa fa-star',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'party_propz_image_type' => 'icon',
						'enable_party_propz'     => 'yes',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$element->add_control(
				'party_propz_select_icon',
				array(
					'label'       => __( 'Select Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'default'     => 'fa fa-star',
					'render_type' => 'template',
					'condition'   => array(
						'party_propz_image_type' => 'icon',
						'enable_party_propz'     => 'yes',
					),
				)
			);
		}

			$element->add_control(
				'party_propz_image_source',
				array(
					'label'     => __( 'Image Source', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'santa',
					'options'   => array(
						'tree'            => __( 'Christmas Tree', 'uael' ),
						'santa_with_deer' => __( 'Reindeer', 'uael' ),
						'hanukkah'        => __( 'Hanukkah Candles', 'uael' ),
						'hang_decor'      => __( 'Hanging Decoration', 'uael' ),
						'default'         => __( 'Santa Cap', 'uael' ),
						'santa'           => __( 'Santa Claus', 'uael' ),
						'snow'            => __( 'Snow', 'uael' ),
						'snow_man'        => __( 'Snow Man', 'uael' ),
						'custom'          => __( 'Custom', 'uael' ),
					),
					'condition' => array(
						'party_propz_image_type!' => 'icon',
						'enable_party_propz'      => 'yes',
					),
				)
			);

			$element->add_control(
				'party_propz_image',
				array(
					'label'     => __( 'Select Image', 'uael' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'party_propz_image_source' => 'custom',
						'party_propz_image_type'   => 'image',
						'enable_party_propz'       => 'yes',
					),
				)
			);

			$element->add_control(
				'party_propz_image_position',
				array(
					'label'        => __( 'Alignment', 'uael' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'bottom_right',
					'options'      => array(
						'top_left'     => __( 'Top Left', 'uael' ),
						'top_right'    => __( 'Top Right', 'uael' ),
						'bottom_left'  => __( 'Bottom Left', 'uael' ),
						'bottom_right' => __( 'Bottom Right', 'uael' ),
						'center_left'  => __( 'Center Left', 'uael' ),
						'center_right' => __( 'Center Right', 'uael' ),
						'custom'       => __( 'Custom', 'uael' ),
					),
					'condition'    => array(
						'enable_party_propz' => 'yes',
					),
					'prefix_class' => 'uael-party-propz-align-',
					'render_type'  => 'template',
				)
			);

			$element->add_responsive_control(
				'party_propz_vertical_position',
				array(
					'label'      => __( 'Vertical Position', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'size' => '',
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'condition'  => array(
						'party_propz_image_position' => 'custom',
						'enable_party_propz'         => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} > .uael-party-propz-wrap, {{WRAPPER}} > .uael-party-propz-wrap i' => 'top: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$element->add_responsive_control(
				'party_propz_horizontal_position',
				array(
					'label'      => __( 'Horizontal Position', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'size' => '',
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'condition'  => array(
						'party_propz_image_position' => 'custom',
						'enable_party_propz'         => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}}.uael-party-propz-align-top_right > .uael-party-propz-wrap, {{WRAPPER}}.uael-party-propz-align-bottom_right > .uael-party-propz-wrap, {{WRAPPER}}.uael-party-propz-align-center_right > .uael-party-propz-wrap' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
						'{{WRAPPER}}.uael-party-propz-align-top_left > .uael-party-propz-wrap, {{WRAPPER}}.uael-party-propz-align-bottom_left > .uael-party-propz-wrap, {{WRAPPER}}.uael-party-propz-align-center_left > .uael-party-propz-wrap' => 'right: auto;left: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}}.uael-party-propz-align-custom > .uael-party-propz-wrap' => 'left: {{SIZE}}{{UNIT}};right: unset;',
					),
				)
			);

			$element->add_responsive_control(
				'party_propz_sticky_image_margin',
				array(
					'label'              => __( 'Spacing from Edges', 'uael' ),
					'description'        => __( 'Note: This is spacing around the image with respect to the Alignment chosen.', 'uael' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px' ),
					'selectors'          => array(
						'{{WRAPPER}}.uael-party-propz-align-top_right > .uael-party-propz-wrap' => 'top: {{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}};',
						'{{WRAPPER}}.uael-party-propz-align-top_left > .uael-party-propz-wrap' => 'top: {{TOP}}{{UNIT}}; left: {{LEFT}}{{UNIT}};',
						'.admin-bar {{WRAPPER}}.uael-party-propz-align-top_left > .uael-party-propz-wrap,
						.admin-bar {{WRAPPER}}.uael-party-propz-align-top_right > .uael-party-propz-wrap' => 'top: calc( {{TOP}}px + 10px );',
						'{{WRAPPER}}.uael-party-propz-align-bottom_right > .uael-party-propz-wrap' => 'bottom: {{BOTTOM}}{{UNIT}}; right: {{RIGHT}}{{UNIT}};',
						'{{WRAPPER}}.uael-party-propz-align-bottom_left > .uael-party-propz-wrap' => 'bottom: {{BOTTOM}}{{UNIT}}; left: {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-party-propz-align-center_left > .uael-party-propz-wrap' => 'left: {{LEFT}}{{UNIT}};',
						'{{WRAPPER}}.uael-party-propz-align-center_right > .uael-party-propz-wrap' => 'right: {{RIGHT}}{{UNIT}};',
					),
					'condition'          => array(
						'party_propz_image_position!' => 'custom',
						'enable_party_propz'          => 'yes',
					),
					'frontend_available' => true,
				)
			);

			$element->add_control(
				'party_propz_link_switch',
				array(
					'label'        => __( 'Link', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'enable_party_propz' => 'yes',
					),
				)
			);

			$element->add_control(
				'party_propz_link',
				array(
					'label'         => __( 'Enter URL', 'uael' ),
					'type'          => Controls_Manager::URL,
					'default'       => array(
						'url' => '',
					),
					'placeholder'   => __( 'https://your-link.com', 'uael' ),
					'condition'     => array(
						'party_propz_link_switch' => 'yes',
					),
					'show_external' => false, // Show the 'open in new tab' button.
					'condition'     => array(
						'enable_party_propz'      => 'yes',
						'party_propz_link_switch' => 'yes',
					),
				)
			);

			$element->add_control(
				'party_propz_style',
				array(
					'label'     => __( 'Style', 'uael' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'enable_party_propz' => 'yes',
					),
				)
			);

			$element->add_control(
				'party_propz_filp',
				array(
					'label'        => __( 'Flip Image', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'uael' ),
					'label_off'    => __( 'No', 'uael' ),
					'return_value' => 'yes',
					'default'      => 'no',
					'prefix_class' => 'uael-flip-img-',
					'condition'    => array(
						'party_propz_image_type' => 'image',
						'enable_party_propz'     => 'yes',
					),
				)
			);

			$element->add_responsive_control(
				'party_propz_image_size',
				array(
					'label'      => __( 'Image Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em' ),
					'range'      => array(
						'em' => array(
							'min' => 1,
							'max' => 30,
						),
						'px' => array(
							'min' => 1,
							'max' => 300,
						),
					),
					'condition'  => array(
						'party_propz_image_type' => 'image',
						'enable_party_propz'     => 'yes',
					),
					'default'    => array(
						'size' => 10,
						'unit' => 'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} > .uael-party-propz-wrap .uael-party-propz-image' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$element->add_responsive_control(
				'party_propz_icon_size',
				array(
					'label'      => __( 'Icon Size', 'uael' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 1,
							'max' => 200,
						),
					),
					'default'    => array(
						'size' => 40,
						'unit' => 'px',
					),
					'condition'  => array(
						'party_propz_image_type' => 'icon',
						'enable_party_propz'     => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} > .uael-party-propz-wrap i:before' => 'font-size: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}} > .uael-party-propz-wrap svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
					),
				)
			);

			$element->add_responsive_control(
				'party_propz_rotate',
				array(
					'label'          => __( 'Rotate', 'uael' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'deg' ),
					'default'        => array(
						'size' => 0,
						'unit' => 'deg',
					),
					'tablet_default' => array(
						'unit' => 'deg',
					),
					'mobile_default' => array(
						'unit' => 'deg',
					),
					'selectors'      => array(
						'{{WRAPPER}} > .uael-party-propz-wrap .uael-party-propz-image,
						{{WRAPPER}} > .uael-party-propz-wrap i, {{WRAPPER}} > .uael-party-propz-wrap svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
					),
					'condition'      => array(
						'enable_party_propz' => 'yes',
					),
				)
			);

			$element->add_group_control(
				Group_Control_Css_Filter::get_type(),
				array(
					'name'      => 'css_filters_party_propz',
					'selector'  => '{{WRAPPER}} > .uael-party-propz-wrap img, {{WRAPPER}} > .uael-party-propz-wrap i,{{WRAPPER}} > .uael-party-propz-wrap svg',
					'condition' => array(
						'enable_party_propz' => 'yes',
					),
				)
			);

			$element->add_control(
				'party_propz_icon_color',
				array(
					'label'     => __( 'Icon Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'condition' => array(
						'party_propz_image_type' => 'icon',
						'enable_party_propz'     => 'yes',
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} > .uael-party-propz-wrap i' => 'color: {{VALUE}};',
						'{{WRAPPER}} > .uael-party-propz-wrap svg' => 'fill: {{VALUE}};',
					),
				)
			);
			$element->add_control(
				'party_propz_icons_hover_color',
				array(
					'label'     => __( 'Icon Hover Color', 'uael' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => array(
						'party_propz_image_type' => 'icon',
						'enable_party_propz'     => 'yes',
					),
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} > .uael-party-propz-wrap:hover > i' => 'color: {{VALUE}};',
						'{{WRAPPER}} > .uael-party-propz-wrap:hover > svg' => 'fill: {{VALUE}};',
					),
				)
			);

			$element->add_control(
				'party_propz_animation',
				array(
					'label'     => __( 'Hover Animation', 'uael' ),
					'type'      => Controls_Manager::HOVER_ANIMATION,
					'condition' => array(
						'enable_party_propz' => 'yes',
					),
				)
			);

			$element->end_controls_section();
	}

	/**
	 * Added Party Propz extension controls for widget.
	 *
	 * @since 1.35.0
	 *
	 * @param array $element returns controls array.
	 * @param array $args return arguments.
	 * @access public
	 */
	public static function register_widget_control( $element, $args ) {

		$element->start_controls_section(
			'party_propz_widget_section',
			array(
				'tab'   => Controls_Manager::TAB_ADVANCED,
				/* translators: %s admin link */
				'label' => sprintf( __( '%1s - Party Propz', 'uael' ), UAEL_PLUGIN_SHORT_NAME ),
			)
		);

		$element->add_control(
			'enable_party_propz_widget',
			array(
				'label'              => __( 'Enable', 'uael' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'On', 'uael' ),
				'label_off'          => __( 'Off', 'uael' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'party_propz_widget_image_type',
			array(
				'label'       => __( 'Image Type', 'uael' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'image' => array(
						'title' => __( 'Image', 'uael' ),
						'icon'  => 'fa fa-image',
					),
					'icon'  => array(
						'title' => __( 'Font Icon', 'uael' ),
						'icon'  => 'fa fa-info-circle',
					),
				),
				'default'     => 'image',
				'toggle'      => false,
				'render_type' => 'template',
				'condition'   => array(
					'enable_party_propz_widget' => 'yes',
				),
			)
		);

		if ( UAEL_Helper::is_elementor_updated() ) {
			$element->add_control(
				'new_party_propz_widget_select_icon',
				array(
					'label'            => __( 'Select Icon', 'uael' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'party_propz_select_icon',
					'default'          => array(
						'value'   => 'fa fa-star',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'party_propz_widget_image_type' => 'icon',
						'enable_party_propz_widget'     => 'yes',
					),
					'render_type'      => 'template',
				)
			);
		} else {
			$element->add_control(
				'party_propz_widget_select_icon',
				array(
					'label'       => __( 'Select Icon', 'uael' ),
					'type'        => Controls_Manager::ICON,
					'default'     => 'fa fa-star',
					'render_type' => 'template',
					'condition'   => array(
						'party_propz_widget_image_type' => 'icon',
						'enable_party_propz_widget'     => 'yes',
					),
				)
			);
		}

		$element->add_control(
			'party_propz_widget_style',
			array(
				'label'     => __( 'Image Source', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'tree'            => __( 'Christmas Tree', 'uael' ),
					'santa_with_deer' => __( 'Reindeer', 'uael' ),
					'hanukkah'        => __( 'Hanukkah Candles', 'uael' ),
					'hang-decor'      => __( 'Hanging Decoration', 'uael' ),
					'default'         => __( 'Santa Cap', 'uael' ),
					'santa-claus'     => __( 'Santa Claus', 'uael' ),
					'snow'            => __( 'Snow', 'uael' ),
					'snow_man'        => __( 'Snow Man', 'uael' ),
					'custom'          => __( 'Custom', 'uael' ),
				),
				'condition' => array(
					'party_propz_widget_image_type!' => 'icon',
					'enable_party_propz_widget'      => 'yes',
				),
			)
		);

		$element->add_control(
			'party_propz_widget_img',
			array(
				'label'     => __( 'Select Image', 'uael' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'enable_party_propz_widget'     => 'yes',
					'party_propz_widget_style'      => 'custom',
					'party_propz_widget_image_type' => 'image',
				),
			)
		);

		$element->add_control(
			'party_propz_widget_alignment',
			array(
				'label'       => __( 'Alignment', 'uael' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle'      => false,
				'options'     => array(
					'left'  => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'     => 'left',

				'condition'   => array(
					'enable_party_propz_widget' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'party_propz_widget_ver_pos',
			array(
				'label'      => __( 'Vertical Position', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '%',
				'default'    => array(
					'size' => '0',
					'unit' => '%',
				),
				'range'      => array(
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap' => 'top: {{SIZE}}{{UNIT}};',
				),

				'condition'  => array(
					'enable_party_propz_widget' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'party_propz_widget_hor_pos',
			array(
				'label'      => __( 'Horizontal Position', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '%',
				'default'    => array(
					'size' => '1',
					'unit' => '%',
				),
				'range'      => array(
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap.party-propz-widget-alignment-right' => 'right: {{SIZE}}{{UNIT}}; left: unset;',
					'{{WRAPPER}} .uael-party-propz-widget-wrap.party-propz-widget-alignment-left' => 'left: {{SIZE}}{{UNIT}}; right: unset;',
				),
				'condition'  => array(
					'enable_party_propz_widget' => 'yes',
				),

			)
		);

		$element->add_responsive_control(
			'party_propz_widget_rotate',
			array(
				'label'          => __( 'Rotate', 'uael' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'deg' ),
				'default'        => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'tablet_default' => array(
					'unit' => 'deg',
				),
				'mobile_default' => array(
					'unit' => 'deg',
				),
				'selectors'      => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap img,
						{{WRAPPER}} .uael-party-propz-widget-wrap i,
						{{WRAPPER}} .uael-party-propz-widget-wrap svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
				),
				'condition'      => array(
					'enable_party_propz_widget' => 'yes',
				),

			)
		);

		$element->add_responsive_control(
			'party_propz_widget_image_size',
			array(
				'label'     => __( 'Image Size', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'default'   => array(
					'size' => 4,
					'unit' => 'em',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap img.uael-party-propz-img-cls' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'party_propz_widget_image_type' => 'image',
					'enable_party_propz_widget'     => 'yes',
				),

			)
		);

		$element->add_responsive_control(
			'party_propz_widget_icon_size',
			array(
				'label'      => __( 'Icon Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 40,
					'unit' => 'px',
				),
				'condition'  => array(
					'party_propz_widget_image_type' => 'icon',
					'enable_party_propz_widget'     => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap i:before' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .uael-party-propz-widget-wrap svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$element->add_control(
			'party_propz_widget_icon_color',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'party_propz_widget_image_type' => 'icon',
					'enable_party_propz_widget'     => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-party-propz-widget-wrap svg' => 'fill: {{VALUE}};',
				),
			)
		);
		$element->add_control(
			'party_propz_widget_icons_hover_color',
			array(
				'label'     => __( 'Icon Hover Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'party_propz_widget_image_type' => 'icon',
					'enable_party_propz_widget'     => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .uael-party-propz-widget-wrap:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$element->add_control(
			'party_propz_widget_zindex',
			array(
				'label'       => __( 'Z-Index', 'uael' ),
				'description' => __( 'Adjust the z-index of the image if it is not visibile. Defaults is set to 999', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '999',
				'min'         => 0,
				'step'        => 1,
				'condition'   => array(
					'enable_party_propz_widget' => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap' => 'z-index: {{SIZE}};',
				),

			)
		);

		$element->add_control(
			'party_propz_widget_img_sty',
			array(
				'label'              => __( 'Image/Icon Style', 'uael' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => __( 'Show', 'uael' ),
				'label_off'          => __( 'Hide', 'uael' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
				'condition'          => array(
					'enable_party_propz_widget' => 'yes',
				),

			)
		);

		$element->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'party_propz_widget_image_filter',
				'selector'  => '{{WRAPPER}} .uael-party-propz-widget-wrap img,
					{{WRAPPER}} .uael-party-propz-widget-wrap i,
					{{WRAPPER}} .uael-party-propz-widget-wrap svg',
				'condition' => array(
					'party_propz_widget_img_sty' => 'yes',
					'enable_party_propz_widget'  => 'yes',
				),
			)
		);

		$element->add_control(
			'party_propz_widget_image_opacity',
			array(
				'label'     => __( 'Image Opacity', 'uael' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-party-propz-widget-wrap img' => 'opacity: {{SIZE}};',
				),
				'condition' => array(
					'party_propz_widget_img_sty' => 'yes',
					'enable_party_propz_widget'  => 'yes',
				),

			)
		);

		$element->end_controls_section();
	}

	/**
	 * Render Party Propz extension output on the frontend for section/column.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.35.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function _before_render_for_section( $element ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$settings = $element->get_settings();

		$class = array(
			'elementor-animation-' . $settings['party_propz_animation'],
			'uael-party-propz-wrap',
		);

		$element->add_render_attribute( 'party_propz_wrap', 'class', $class );

		$element->add_render_attribute( 'party_propz_image_class', 'class', 'uael-party-propz-image' );

		if ( ! empty( $settings['party_propz_link']['url'] ) ) {
			$element->add_link_attributes( 'image_link', $settings['party_propz_link'] );
		}

		switch ( $settings['party_propz_image_source'] ) {
			case 'custom':
				if ( ! empty( $settings['party_propz_image']['url'] ) ) {
					$element->add_render_attribute( 'party_propz_image_class', 'src', $settings['party_propz_image']['url'] );
					$element->add_render_attribute( 'party_propz_image_class', 'alt', Control_Media::get_image_alt( $settings['party_propz_image'] ) );
				}
				break;

			case 'default':
				$img = UAEL_URL . 'assets/img/uae-santa-cap.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $img );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Christmas Santa Cap' );
				break;

			case 'snow':
				$img = UAEL_URL . 'assets/img/snow.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $img );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Snow Gathered' );
				break;

			case 'tree':
				$source = UAEL_URL . 'assets/img/extension-tree.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $source );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Christmas Tree' );
				break;

			case 'santa':
				$source = UAEL_URL . 'assets/img/santa-claus.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $source );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Santa Claus' );
				break;

			case 'hanukkah':
				$img = UAEL_URL . 'assets/img/hanukkah.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $img );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Hanukkah Candles' );
				break;

			case 'hang_decor':
				$source = UAEL_URL . 'assets/img/extension-hang-decor.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $source );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Hanging Decoration' );
				break;

			case 'santa_with_deer':
				$source = UAEL_URL . 'assets/img/extension-deer.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $source );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Reindeer' );
				break;

			case 'snow_man':
				$source = UAEL_URL . 'assets/img/extension-snowman.png';
				$element->add_render_attribute( 'party_propz_image_class', 'src', $source );
				$element->add_render_attribute( 'party_propz_image_class', 'alt', 'Snow Man' );
				break;

			default:
				break;
		}
		if ( 'yes' === $settings['enable_party_propz'] ) { ?>
			<div <?php echo wp_kses_post( $element->get_render_attribute_string( 'party_propz_wrap' ) ); ?>>
				<?php if ( ! empty( $settings['party_propz_link']['url'] ) ) { ?>
					<a <?php echo wp_kses_post( $element->get_render_attribute_string( 'image_link' ) ); ?>>
					<?php
				}
				if ( 'image' === $settings['party_propz_image_type'] ) {
					?>
					<img <?php echo wp_kses_post( $element->get_render_attribute_string( 'party_propz_image_class' ) ); ?> />
					<?php
				} elseif ( 'icon' === $settings['party_propz_image_type'] ) {
					if ( UAEL_Helper::is_elementor_updated() ) {
						if ( ! isset( $settings['party_propz_select_icon'] ) && ! \Elementor\Icons_Manager::is_migration_allowed() ) {
							// add old default.
							$settings['party_propz_select_icon'] = 'fa fa-close';
						}
						$has_icon = ! empty( $settings['party_propz_select_icon'] );

						if ( ! $has_icon && ! empty( $settings['new_party_propz_select_icon']['value'] ) ) {
							$has_icon = true;
						}

						$close_migrated = isset( $settings['__fa4_migrated']['new_party_propz_select_icon'] );
						$close_is_new   = ! isset( $settings['party_propz_select_icon'] ) && \Elementor\Icons_Manager::is_migration_allowed();

						if ( $has_icon ) {
							?>
							<?php if ( $close_migrated || $close_is_new ) { ?>
									<?php \Elementor\Icons_Manager::render_icon( $settings['new_party_propz_select_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php } elseif ( ! empty( $settings['party_propz_select_icon'] ) ) { ?>
								<i class="<?php echo esc_attr( $settings['party_propz_select_icon'] ); ?>"></i>
							<?php } ?>
							<?php
						}
					} elseif ( ! empty( $settings['party_propz_select_icon'] ) ) {
						?>
						<i class="<?php echo esc_attr( $settings['party_propz_select_icon'] ); ?>"></i>
						<?php
					}
				}
				if ( ! empty( $settings['party_propz_link']['url'] ) ) {
					?>
					</a>
				<?php } ?>
			</div>
			<?php
		}
	}

	/**
	 * Render Party Propz extension output on the frontend for widget.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.35.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function _before_render_for_widget( $element ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$settings = $element->get_settings();

		$element->add_render_attribute( 'uael_party_propz_widget_wrap', 'class', 'uael-party-propz-widget-wrap' );
		$element->add_render_attribute( 'uael_party_propz_widget_wrap', 'class', 'party-propz-widget-alignment-' . $settings['party_propz_widget_alignment'] );

		$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'class', 'uael-party-propz-img-cls' );

		$party_propz_widget_style = $settings['party_propz_widget_style'];
		switch ( $party_propz_widget_style ) {
			case 'custom':
				if ( ! empty( $settings['party_propz_widget_img']['url'] ) ) {
					$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $settings['party_propz_widget_img']['url'] );
					$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', Control_Media::get_image_alt( $settings['party_propz_widget_img'] ) );
				}
				break;

			case 'default':
				$img = UAEL_URL . 'assets/img/uae-santa-cap.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Christmas Santa Cap' );
				break;

			case 'santa-claus':
				$img = UAEL_URL . 'assets/img/santa-claus.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Santa Claus' );
				break;

			case 'hang-decor':
				$img = UAEL_URL . 'assets/img/extension-hang-decor.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Hanging Decoration' );
				break;

			case 'snow':
				$img = UAEL_URL . 'assets/img/snow.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Snow Gathered' );
				break;

			case 'snow_man':
				$img = UAEL_URL . 'assets/img/extension-snowman.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Snow Man' );
				break;

			case 'hanukkah':
				$img = UAEL_URL . 'assets/img/hanukkah.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Hanukkah Candles' );
				break;

			case 'tree':
				$img = UAEL_URL . 'assets/img/extension-tree.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Christmas Tree' );
				break;

			case 'santa_with_deer':
				$img = UAEL_URL . 'assets/img/extension-deer.png';
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'src', $img );
				$element->add_render_attribute( 'uael_party_propz_widget_img_cls', 'alt', 'Reindeer' );
				break;

			default:
				break;

		}

		?>
		<?php if ( 'yes' === $settings['enable_party_propz_widget'] ) { ?>
			<div <?php echo wp_kses_post( $element->get_render_attribute_string( 'uael_party_propz_widget_wrap' ) ); ?>>
				<?php if ( 'image' === $settings['party_propz_widget_image_type'] ) { ?>
					<img <?php echo wp_kses_post( $element->get_render_attribute_string( 'uael_party_propz_widget_img_cls' ) ); ?> />
					<?php
				} elseif ( 'icon' === $settings['party_propz_widget_image_type'] ) {
					if ( UAEL_Helper::is_elementor_updated() ) {
						if ( ! isset( $settings['party_propz_widget_select_icon'] ) && ! \Elementor\Icons_Manager::is_migration_allowed() ) {
							// add old default.
							$settings['party_propz_widget_select_icon'] = 'fa fa-close';
						}
						$has_icon = ! empty( $settings['party_propz_widget_select_icon'] );

						if ( ! $has_icon && ! empty( $settings['new_party_propz_widget_select_icon']['value'] ) ) {
							$has_icon = true;
						}

						$close_migrated = isset( $settings['__fa4_migrated']['new_party_propz_widget_select_icon'] );
						$close_is_new   = ! isset( $settings['party_propz_widget_select_icon'] ) && \Elementor\Icons_Manager::is_migration_allowed();

						if ( $has_icon ) {
							?>
							<?php if ( $close_migrated || $close_is_new ) { ?>
								<?php \Elementor\Icons_Manager::render_icon( $settings['new_party_propz_widget_select_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php } elseif ( ! empty( $settings['party_propz_widget_select_icon'] ) ) { ?>
								<i class="<?php echo esc_attr( $settings['party_propz_widget_select_icon'] ); ?>"></i>
							<?php } ?>
							<?php
						}
					} elseif ( ! empty( $settings['party_propz_widget_select_icon'] ) ) {
						?>
						<i class="<?php echo esc_attr( $settings['party_propz_widget_select_icon'] ); ?>"></i>
						<?php
					}
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * Render Party Propz Background output in the editor for section/column.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.35.0
	 * @access public
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public function print_template_for_section( $template, $widget ) {

		$old_template = $template;
		ob_start();
		?>
			<# view.addRenderAttribute( 'party_propz_wrap', 'class', ' elementor-animation-' + settings.party_propz_animation );
				view.addRenderAttribute( 'party_propz_wrap', 'class', ' uael-party-propz-wrap' );

				view.addRenderAttribute( 'party_propz_image_class', 'class', ' uael-party-propz-image' );
				if( '' != settings.party_propz_link.url ) {
					view.addRenderAttribute( 'image_link', 'href', settings.party_propz_link.url );
				}

				switch( settings.party_propz_image_source ) {
					case 'custom' :
						if ( '' != settings.party_propz_image.url ) {
							source = settings.party_propz_image.url;
							view.addRenderAttribute( 'party_propz_image_class', 'src', source );
						}
					break;

					case 'default' :
						source = UAEWidgetsData.santa_cap;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'tree' :
						source = UAEWidgetsData.extension_tree;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'santa' :
						source = UAEWidgetsData.santa_claus;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'hanukkah':
						source = UAEWidgetsData.hanukkah;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'snow':
						source = UAEWidgetsData.snow;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'hang_decor' :
						source = UAEWidgetsData.extension_hang_decor;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'santa_with_deer':
						source = UAEWidgetsData.extension_deer;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					case 'snow_man':
						source = UAEWidgetsData.extension_snowman;
						view.addRenderAttribute( 'party_propz_image_class', 'src', source );
					break;

					default:
						break;
				}

				if( 'yes' == settings.enable_party_propz ) { #>
					<div {{{ view.getRenderAttributeString( 'party_propz_wrap' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# if( '' != settings.party_propz_link.url ) { #>
							<a {{{ view.getRenderAttributeString( 'image_link' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# }

						if( 'image' === settings.party_propz_image_type ) { #>
							<img {{{ view.getRenderAttributeString( 'party_propz_image_class' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
						<# } else if ( 'icon' === settings.party_propz_image_type ) { #>
							<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>

								<# if ( settings.party_propz_select_icon || settings.new_party_propz_select_icon ) {

									var selectedIconHTML = elementor.helpers.renderIcon( view, settings.new_party_propz_select_icon, { 'aria-hidden': true }, 'i' , 'object' );

									var iconMigrated = elementor.helpers.isIconMigrated( settings, 'new_party_propz_select_icon' );
									if ( selectedIconHTML && selectedIconHTML.rendered && ( ! settings.party_propz_select_icon || iconMigrated ) ) { #>
										{{{ selectedIconHTML.value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
									<# } else { #>
										<i class= "{{ settings.party_propz_select_icon }}" aria-hidden="true"></i>
									<# }
								} #>
							<?php } else { ?>
								<i class="{{ settings.party_propz_select_icon }}"/>
							<?php } ?>
						<# } #>

						<# if( '' != settings.party_propz_link.url ) { #>
							</a>
						<# } #>
					</div>
				<# } #>
		<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}

	/**
	 * Render Party Propz extension output in the editor for widget.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.35.0
	 * @access public
	 * @param object $template for current template.
	 * @param object $widget for current widget.
	 */
	public function _print_template_for_widget( $template, $widget ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		if ( ! $template ) {
			return;
		}

		$old_template = $template;
		ob_start();
		?>
		<#
		view.addRenderAttribute( 'uael_party_propz_widget_wrap', 'class', 'uael-party-propz-widget-wrap' );
		view.addRenderAttribute( 'uael_party_propz_widget_wrap', 'class', ' party-propz-widget-alignment-' + settings.party_propz_widget_alignment );
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'class', 'uael-party-propz-img-cls' );
		party_propz_widget_style = settings.party_propz_widget_style;

		switch( party_propz_widget_style ) {
		case 'custom' :
		if ( '' != settings.party_propz_widget_img.url ) {
		source = settings.party_propz_widget_img.url;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		}
		break;

		case 'default' :
		source = UAEWidgetsData.santa_cap;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'santa-claus' :
		source = UAEWidgetsData.santa_claus;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'hang-decor' :
		source = UAEWidgetsData.extension_hang_decor;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'snow':
		source = UAEWidgetsData.snow;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'snow_man':
		source = UAEWidgetsData.extension_snowman;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'hanukkah':
		source = UAEWidgetsData.hanukkah;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'tree' :
		source = UAEWidgetsData.extension_tree;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;

		case 'santa_with_deer':
		source = UAEWidgetsData.extension_deer;
		view.addRenderAttribute( 'uael_party_propz_widget_img_cls', 'src', source );
		break;
		default:
		break;
		}

		if( 'yes' == settings.enable_party_propz_widget ) { #>
		<div {{{ view.getRenderAttributeString( 'uael_party_propz_widget_wrap' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
		<# if( 'image' === settings.party_propz_widget_image_type ) { #>
		<img {{{ view.getRenderAttributeString( 'uael_party_propz_widget_img_cls' ) }}}> <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
		<# } else if ( 'icon' === settings.party_propz_widget_image_type ) { #>
		<?php if ( UAEL_Helper::is_elementor_updated() ) { ?>

			<# if ( settings.party_propz_widget_select_icon || settings.new_party_propz_widget_select_icon ) {

			var selectedIconHTML = elementor.helpers.renderIcon( view, settings.new_party_propz_widget_select_icon, { 'aria-hidden': true }, 'i' , 'object' );

			var iconMigrated = elementor.helpers.isIconMigrated( settings, 'new_party_propz_widget_select_icon' );
			if ( selectedIconHTML && selectedIconHTML.rendered && ( ! settings.party_propz_widget_select_icon || iconMigrated ) ) { #>
			{{{ selectedIconHTML.value }}} <?php //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation ?>
			<# } else { #>
			<i class= "{{ settings.party_propz_widget_select_icon }}" aria-hidden="true"></i>
			<# }
			} #>
		<?php } else { ?>
			<i class="{{ settings.party_propz_widget_select_icon }}"/>
		<?php } ?>
		<# } #>
		</div>
		<# } #>
		<?php

		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}
}
