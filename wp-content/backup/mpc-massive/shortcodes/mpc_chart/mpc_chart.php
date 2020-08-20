<?php
/*----------------------------------------------------------------------------*\
	CHART SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Chart' ) ) {
	class MPC_Chart {
		public $shortcode = 'mpc_chart';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_chart', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'section_begin', 'section_end' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return;

			foreach( $layouts[ $style ] as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}


		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_chart-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_chart/css/mpc_chart.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_chart-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_chart/js/mpc_chart' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',

				'value' => 80,

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'border_css'  => '',
				'padding_css' => '',
				'margin_css'  => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				// Title
				'disable_title'           => '',
				'title'                   => '',

				'title_margin_css' => '',

				'title_font_preset'      => '',
				'title_font_color'       => '',
				'title_font_size'        => '',
				'title_font_line_height' => '',
				'title_font_align'       => '',
				'title_font_transform'   => '',

				// Description
				'disable_description'    => '',
				'description'            => '',

				'description_margin_css' => '',

				'description_font_preset'      => '',
				'description_font_color'       => '',
				'description_font_size'        => '',
				'description_font_line_height' => '',
				'description_font_align'       => '',
				'description_font_transform'   => '',

				// Chart
				'chart_radius'                 => '100',
				'chart_width'                  => '10',

				'chart_front_background_type'       => 'color',
				'chart_front_background_color'      => '',
				'chart_front_background_image'      => '',
				'chart_front_background_image_size' => 'large',
				'chart_front_background_repeat'     => 'no-repeat',
				'chart_front_background_size'       => 'initial',
				'chart_front_background_position'   => 'middle-center',
				'chart_front_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'chart_back_background_type'       => 'color',
				'chart_back_background_color'      => '',
				'chart_back_background_image'      => '',
				'chart_back_background_image_size' => 'large',
				'chart_back_background_repeat'     => 'no-repeat',
				'chart_back_background_size'       => 'initial',
				'chart_backg_background_position'  => 'middle-center',
				'chart_back_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				// Icon
				'disable_icon'                     => '',

				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_color'      => '#333333',
				'icon_size'       => '',

				// Value
				'disable_value'   => '',

				'value_text' => '',
				'value_unit' => '',

				'value_font_preset'      => '',
				'value_font_color'       => '',
				'value_font_size'        => '',
				'value_font_line_height' => '',
				'value_font_align'       => '',
				'value_font_transform'   => '',

				// Inner Circle
				'disable_inner_circle'   => '',
				'inner_circle_radius'    => '',

				'inner_circle_background_type'       => 'color',
				'inner_circle_background_color'      => '',
				'inner_circle_background_image'      => '',
				'inner_circle_background_image_size' => 'large',
				'inner_circle_background_repeat'     => 'no-repeat',
				'inner_circle_background_size'       => 'initial',
				'inner_circle_background_position'   => 'middle-center',
				'inner_circle_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'inner_circle_border_css' => '',

				// Outer Circle
				'disable_outer_circle'    => '',
				'outer_circle_radius'     => '',

				'outer_circle_background_type'       => 'color',
				'outer_circle_background_color'      => '',
				'outer_circle_background_image'      => '',
				'outer_circle_background_image_size' => 'large',
				'outer_circle_background_repeat'     => 'no-repeat',
				'outer_circle_background_size'       => 'initial',
				'outer_circle_background_position'   => 'middle-center',
				'outer_circle_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'outer_circle_border_css' => '',

				// Marker
				'disable_marker'          => '',
				'marker_radius'           => '',

				'marker_background_type'       => 'color',
				'marker_background_color'      => '',
				'marker_background_image'      => '',
				'marker_background_image_size' => 'large',
				'marker_background_repeat'     => 'no-repeat',
				'marker_background_size'       => 'initial',
				'marker_background_position'   => 'middle-center',
				'marker_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'marker_border_css' => '',
			), $atts );

			$atts[ 'description' ] = $content;

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			if ( $atts[ 'disable_icon' ] == '' ) {
				$icon = MPC_Parser::icon( $atts );
			}

			$options = array(
				'value'  => $atts[ 'value' ],
				'radius' => $atts[ 'chart_radius' ],
				'width'  => $atts[ 'chart_width' ],

				'inner_radius' => $atts[ 'inner_circle_radius' ],
				'outer_radius' => $atts[ 'outer_circle_radius' ],
			);
			$this->add_background_options( $options, $atts, 'chart_front' );
			$this->add_background_options( $options, $atts, 'chart_back' );

			$animation = MPC_Parser::animation( $atts );
			$classes = ' mpc-waypoint mpc-init mpc-parent--init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$title_classes = $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'title_font_preset' ] ) : '';

			$description_classes = $atts[ 'description_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'description_font_preset' ] ) : '';

			$value_classes = $atts[ 'value_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'value_font_preset' ] ) : '';
			$value = $atts[ 'value_text' ] != '' ? $atts[ 'value_text' ] : $atts[ 'value' ];
			$value_unit = $atts[ 'value_unit' ] != '' ? $atts[ 'value_unit' ] : '';

			$return = '<div data-id="' . $css_id . '" class="mpc-chart-wrap' . $classes . '" data-options="' . htmlentities( json_encode( $options ), ENT_QUOTES, 'UTF-8' ) . '"' . $animation . '>';
				$return .= '<div class="mpc-chart__box">';
					$return .= '<canvas class="mpc-chart" width="200" height="200"></canvas>';
					if ( $atts[ 'disable_outer_circle' ] == '' && $atts[ 'outer_circle_radius' ] != '' ) {
						$return .= '<div class="mpc-chart__outer_circle"></div>';
					}
					if ( $atts[ 'disable_inner_circle' ] == '' && $atts[ 'inner_circle_radius' ] != '' ) {
						$return .= '<div class="mpc-chart__inner_circle"></div>';
					}
					if ( $atts[ 'disable_marker' ] == '' && $atts[ 'marker_radius' ] != '' ) {
						$return .= '<div class="mpc-chart__marker"></div>';
					}
					if ( isset( $icon ) || ( $atts[ 'disable_value' ] == '' && $value != '' ) ) {
						$return .= '<div class="mpc-chart__text' . $value_classes . '">';
						if ( isset( $icon ) ) {
							$return .= '<i class="mpc-chart__icon ' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i>';
						}
						if ( $atts[ 'disable_value' ] == '' && $value != '' ) {
							$return .= '<div class="mpc-chart__value" data-value="' . esc_attr( $value ) . '" data-unit="' . esc_attr( $value_unit ) . '">' . $value . $value_unit . '</div>';
						}
						$return .= '</div>';
					}
				$return .= '</div>';
				if ( $atts[ 'disable_title' ] == '' && $atts[ 'title' ] != '' ) {
					$return .= '<div class="mpc-chart__title' . $title_classes . '">' . $atts[ 'title' ] . '</div>';
				}
				if ( $atts[ 'disable_description' ] == '' && $content != '' ) {
					$return .= '<div class="mpc-chart__description' . $description_classes . '">' . do_shortcode( $atts[ 'description' ] ) . '</div>';
				}
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_chart-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'value_font_size' ] = $styles[ 'value_font_size' ] != '' ? $styles[ 'value_font_size' ] . ( is_numeric( $styles[ 'value_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'description_font_size' ] = $styles[ 'description_font_size' ] != '' ? $styles[ 'description_font_size' ] . ( is_numeric( $styles[ 'description_font_size' ] ) ? 'px' : '' ) : '';

			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Box
			if ( $styles[ 'chart_radius' ] ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__box {';
					$style .= 'width: ' . ( (int)$styles[ 'chart_radius' ] * 2 ) . 'px;';
				$style .= '}';
			}

			// Icon
			if ( $temp_style = MPC_CSS::icon( $styles ) ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__icon {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Outer Circle
			$inner_styles = array();
			if ( $styles[ 'outer_circle_border_css' ] ) { $inner_styles[] = $styles[ 'outer_circle_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'outer_circle' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'disable_outer_circle' ] == '' && $styles[ 'outer_circle_radius' ] != '' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__outer_circle {';
					$style .= 'width: ' . ( (int)$styles[ 'outer_circle_radius' ] * 2 ) . 'px;';
					$style .= 'height: ' . ( (int)$styles[ 'outer_circle_radius' ] * 2 ) . 'px;';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Inner Circle
			$inner_styles = array();
			if ( $styles[ 'inner_circle_border_css' ] ) { $inner_styles[] = $styles[ 'inner_circle_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'inner_circle' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'disable_inner_circle' ] == '' && $styles[ 'inner_circle_radius' ] != '' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__inner_circle {';
					$style .= 'width: ' . ( (int)$styles[ 'inner_circle_radius' ] * 2 ) . 'px;';
					$style .= 'height: ' . ( (int)$styles[ 'inner_circle_radius' ] * 2 ) . 'px;';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Marker
			$inner_styles = array();
			if ( $styles[ 'marker_border_css' ] ) { $inner_styles[] = $styles[ 'marker_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'marker' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'disable_marker' ] == '' && $styles[ 'marker_radius' ] != '' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__marker {';
					$style .= 'top: ' . (int)$styles[ 'marker_radius' ] . 'px;';
					$style .= 'width: ' . ( (int)$styles[ 'marker_radius' ] * 2 ) . 'px;';
					$style .= 'height: ' . ( (int)$styles[ 'marker_radius' ] * 2 ) . 'px;';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Value
			if ( ( $temp_style = MPC_CSS::font( $styles, 'value' ) ) && $styles[ 'disable_value' ] == '' && ( $styles[ 'value_text' ] != '' || $styles[ 'value' ] != '' ) ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__text {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Title
			$inner_styles = array();
			if ( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'disable_title' ] == '' && $styles[ 'title' ] != '' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__title {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Description
			$inner_styles = array();
			if ( $styles[ 'description_margin_css' ] ) { $inner_styles[] = $styles[ 'description_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'description' ) ) { $inner_styles[] = $temp_style; }

			if ( $styles[ 'disable_description' ] == '' && $styles[ 'description' ] != '' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-chart-wrap[data-id="' . $css_id . '"] .mpc-chart__description {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Main Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'        => 'mpc_slider',
					'heading'     => __( 'Value', 'mpc' ),
					'param_name'  => 'value',
					'admin_label' => true,
					'tooltip'     => __( 'Choose chart fill value. From 0% as empty chart to 100% as closed circle.', 'mpc' ),
					'value'       => '80',
					'unit'        => '%',
				),
			);
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();
			$animation  = MPC_Snippets::vc_animation();

			/* SECTION TITLE */
			$title = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Title', 'mpc' ),
					'param_name'       => 'disable_title',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Description', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
			);
			$title_font = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Description', 'mpc' ), 'dependency'  => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ) ) );
			$title_text = array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'mpc' ),
					'param_name'  => 'title',
					'admin_label' => true,
					'tooltip'     => __( 'Define title.', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ),
					'group'       => __( 'Description', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),);
			$title_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Description', 'mpc' ), 'dependency'  => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ) ) );

			/* SECTION DESCRIPTION */
			$description = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Description', 'mpc' ),
					'param_name'       => 'disable_description',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Description', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field mpc-no-wrap',
				),
			);
			$description_font = MPC_Snippets::vc_font( array( 'prefix' => 'description', 'subtitle' => __( 'Description', 'mpc' ), 'group' => __( 'Description', 'mpc' ), 'dependency'  => array( 'element' => 'disable_description', 'value_not_equal_to' => 'true' ) ) );
			$description_text = array(
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Description', 'mpc' ),
					'param_name'  => 'content',
					'holder'      => 'div',
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'disable_description', 'value_not_equal_to' => 'true' ),
					'group'       => __( 'Description', 'mpc' ),
				),
			);
			$description_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'description', 'subtitle' => __( 'Description', 'mpc' ), 'group' => __( 'Description', 'mpc' ), 'dependency'  => array( 'element' => 'disable_description', 'value_not_equal_to' => 'true' ) ) );

			/* SECTION CHART */
			// Chart
			$chart = array(
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Width', 'mpc' ),
					'param_name'       => 'chart_width',
					'tooltip'          => __( 'Define chart circle width. You can create two types of charts:<br><b>Donut</b>: if the <em>width</em> value is smaller then <em>radius</em>;<br><b>Pie</b>: if the <em>width</em> and <em>radius</em> values are the same.', 'mpc' ),
					'value'            => '10',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-marker',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'group'            => __( 'Chart', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Radius', 'mpc' ),
					'param_name'       => 'chart_radius',
					'tooltip'          => __( 'Define chart circle radius.', 'mpc' ),
					'value'            => '100',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'group'            => __( 'Chart', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-first-row mpc-advanced-field',
				),
			);
			$chart_front = MPC_Snippets::vc_background( array( 'prefix' => 'chart_front', 'title' => __( 'Foreground', 'mpc' ), 'group' => __( 'Chart', 'mpc' ) ) );
			$chart_back  = MPC_Snippets::vc_background( array( 'prefix' => 'chart_back', 'group' => __( 'Chart', 'mpc' ) ) );

			// Icon
			$disable_icon = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Icon', 'mpc' ),
					'param_name'       => 'disable_icon',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Chart', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-no-wrap mpc-advanced-field',
				),
			);
			$icon = MPC_Snippets::vc_icon( array( 'title' => __( 'Icon - Settings', 'mpc' ), 'dependency'  => array( 'element' => 'disable_icon', 'value_not_equal_to' => 'true' ), 'group' => __( 'Chart', 'mpc' ) ) );

			// Value
			$value = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Value', 'mpc' ),
					'param_name'       => 'disable_value',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Chart', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-no-wrap',
				),
			);
			$value_font = MPC_Snippets::vc_font( array( 'prefix' => 'value', 'subtitle' => __( 'Value', 'mpc' ), 'dependency'  => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ), 'group' => __( 'Chart', 'mpc' ), 'with_align' => false ) );
			$value_text = array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Display Value', 'mpc' ),
					'param_name'  => 'value_text',
					'tooltip'     => __( 'Define displayed value. When you want to display value different then <b>Value</b> field (0-100).', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ),
					'group'       => __( 'Chart', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Unit', 'mpc' ),
					'param_name'  => 'value_unit',
					'tooltip'     => __( 'Define value unit. You can specify a custom unit displayed after the <b>Value</b> number (e.g. <em>%</em>).', 'mpc' ),
					'value'       => '',
					'dependency'  => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ),
					'group'       => __( 'Chart', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			// Marker
			$marker = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Marker', 'mpc' ),
					'param_name'       => 'disable_marker',
					'tooltip'          => __( 'Check to disable marker. This will disable marker at the end of your chart progress circle.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Marker - Radius', 'mpc' ),
					'param_name'       => 'marker_radius',
					'tooltip'          => __( 'Define marker circle radius.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-marker',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'dependency'       => array( 'element' => 'disable_marker', 'value_not_equal_to' => 'true' ),
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
				),
			);
			$marker_background = MPC_Snippets::vc_background( array( 'prefix' => 'marker', 'subtitle' => __( 'Marker', 'mpc' ), 'dependency'  => array( 'element' => 'disable_marker', 'value_not_equal_to' => 'true' ), 'group' => __( 'Extras', 'mpc' ) ) );
			$marker_border = MPC_Snippets::vc_border( array( 'prefix' => 'marker', 'subtitle' => __( 'Marker', 'mpc' ), 'dependency'  => array( 'element' => 'disable_marker', 'value_not_equal_to' => 'true' ), 'group' => __( 'Extras', 'mpc' ), 'with_radius' => false ) );

			// Inner Circle
			$inner_circle = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'First Circle', 'mpc' ),
					'param_name'       => 'disable_inner_circle',
					'tooltip'          => __( 'Check to disable first circle. This will disable first circle background around chart value. You can create a multi-layered charts.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-no-wrap mpc-clear--both',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'First Circle - Radius', 'mpc' ),
					'param_name'       => 'inner_circle_radius',
					'tooltip'          => __( 'Define first circle radius.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-marker',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'dependency'       => array( 'element' => 'disable_inner_circle', 'value_not_equal_to' => 'true' ),
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);
			$inner_circle_background = MPC_Snippets::vc_background( array( 'prefix' => 'inner_circle', 'subtitle' => __( 'First Circle', 'mpc' ), 'dependency'  => array( 'element' => 'disable_inner_circle', 'value_not_equal_to' => 'true' ), 'group' => __( 'Extras', 'mpc' ) ) );
			$inner_circle_border = MPC_Snippets::vc_border( array( 'prefix' => 'inner_circle', 'subtitle' => __( 'First Circle', 'mpc' ), 'dependency'  => array( 'element' => 'disable_inner_circle', 'value_not_equal_to' => 'true' ), 'group' => __( 'Extras', 'mpc' ), 'with_radius' => false ) );

			// Outer Circle
			$outer_circle = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Second Circle', 'mpc' ),
					'param_name'       => 'disable_outer_circle',
					'tooltip'          => __( 'Check to disable second circle. This will disable second circle background around chart value. You can create a multi-layered charts.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-no-wrap mpc-clear--both',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Second Circle - Radius', 'mpc' ),
					'param_name'       => 'outer_circle_radius',
					'tooltip'          => __( 'Define second circle radius.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-marker',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'dependency'       => array( 'element' => 'disable_outer_circle', 'value_not_equal_to' => 'true' ),
					'group'            => __( 'Extras', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);
			$outer_circle_background = MPC_Snippets::vc_background( array( 'prefix' => 'outer_circle', 'subtitle' => __( 'Second Circle', 'mpc' ), 'dependency'  => array( 'element' => 'disable_outer_circle', 'value_not_equal_to' => 'true' ), 'group' => __( 'Extras', 'mpc' ) ) );
			$outer_circle_border = MPC_Snippets::vc_border( array( 'prefix' => 'outer_circle', 'subtitle' => __( 'Second Circle', 'mpc' ), 'dependency'  => array( 'element' => 'disable_outer_circle', 'value_not_equal_to' => 'true' ), 'group' => __( 'Extras', 'mpc' ), 'with_radius' => false ) );

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $background, $border, $padding, $margin, $title, $title_font, $title_text, $title_margin, $description, $description_font, $description_text, $description_margin, $chart, $chart_front, $chart_back, $disable_icon, $icon, $value, $value_font, $value_text, $marker, $marker_background, $marker_border, $inner_circle, $inner_circle_background, $inner_circle_border, $outer_circle, $outer_circle_background, $outer_circle_border, $animation, $class );

			return array(
				'name'        => __( 'Chart', 'mpc' ),
				'description' => __( 'Extended pie chart styles', 'mpc' ),
				'base'        => 'mpc_chart',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-charts.png',
				'icon'        => 'mpc-shicon-chart',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}

		function add_background_options( &$options, $atts, $prefix ) {
			if ( ! isset( $prefix ) ) {
				return $options;
			}

			$options[ $prefix ] = array();

			if ( $atts[ $prefix . '_background_type' ] == 'color' && $atts[ $prefix . '_background_color' ] != '' ) {
				$options[ $prefix ][ 'type' ]  = 'color';
				$options[ $prefix ][ 'color' ] = $atts[ $prefix . '_background_color' ];
			} elseif ( $atts[ $prefix . '_background_type' ] == 'image' && $atts[ $prefix . '_background_image' ] != '' ) {
				$background_image = wp_get_attachment_image_src( $atts[ $prefix . '_background_image' ], 'full' );

				$options[ $prefix ][ 'type' ]   = 'image';
				$options[ $prefix ][ 'repeat' ] = $atts[ $prefix . '_background_repeat' ];
				$options[ $prefix ][ 'size' ]   = $atts[ $prefix . '_background_size' ];

				if ( isset( $background_image[ 0 ] ) ) {
					$options[ $prefix ][ 'image' ] = $background_image[ 0 ];
				} else {
					$options[ $prefix ][ 'image' ] = mpc_get_plugin_path( __FILE__ ) . '/assets/images/mpc-image-placeholder.png';
				}
			} elseif ( $atts[ $prefix . '_background_type' ] == 'gradient' ) {
				$options[ $prefix ][ 'type' ]     = 'gradient';
				$options[ $prefix ][ 'gradient' ] = $atts[ $prefix . '_background_gradient' ];
			}

			return $options;
		}
	}
}
if ( class_exists( 'MPC_Chart' ) ) {
	global $MPC_Chart;
	$MPC_Chart = new MPC_Chart;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_chart' ) ) {
	class WPBakeryShortCode_mpc_chart extends MPCShortCode_Base {}
}
