<?php
/*----------------------------------------------------------------------------*\
	COUNTER SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Counter' ) ) {
	class MPC_Counter {
		public $shortcode = 'mpc_counter';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_counter', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'icon'          => '',
				'divider'       => '',
				'title'         => '',
				'counter'       => '',
			);

			$this->parts = $parts;
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_counter-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_counter/css/mpc_counter.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_counter-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_counter/js/mpc_counter' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'icon', 'counter', 'divider', 'title' ),
				'style_2' => array( 'counter', 'icon', 'divider', 'title' ),
				'style_3' => array( 'title', 'divider', 'icon', 'counter' ),
				'style_4' => array( 'title', 'icon', 'divider', 'counter' ),
				'style_5' => array( 'icon', 'section_begin', 'counter', 'title', 'section_end' ),
				'style_6' => array( 'section_begin', 'counter', 'title', 'section_end', 'icon' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $parts[ $part ];
			}

			return $content;
		}

		/* Generate final value for spacing */
		function get_final_value( $atts, $grouping ) {
			$final = $atts[ 'value' ];

			if( $grouping ) {
				$final = number_format( $final, $atts[ 'decimals' ], $atts[ 'decimal' ], $atts[ 'separator' ] );
			}

			return $final;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-countup-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/countUp.min.js', array(), '', true );

			global $MPC_Icon, $MPC_Divider, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                     => '',
				'preset'                    => '',
				'layout'                    => 'style_1',

				'alignment'                 => 'center',
				'icon_vertical'             => 'top',
				'value'                     => '',
				'type'                      => 'from-zero',
				'duration'                  => '100',
				'delay'                     => '0',
				'enable_grouping'           => '',
				'disable_easing'            => '',
				'separator'                 => ',',
				'decimal'                   => '.',
				'decimals'                  => '2',

				'title_font_preset'         => '',
				'title_font_color'          => '',
				'title_font_size'           => '',
				'title_font_line_height'    => '',
				'title_font_align'          => '',
				'title_font_transform'      => '',
				'title'                     => '',

				'counter_font_preset'       => '',
				'counter_font_color'        => '',
				'counter_font_size'         => '',
				'counter_font_line_height'  => '',
				'counter_font_align'        => '',
				'counter_font_transform'    => '',

				'prefix'                 => '',
				'suffix'                 => '',
				'addon_gap'              => 'bottom',
				'addon_v_align'          => '',
				'addon_font_preset'      => '',
				'addon_font_color'       => '',
				'addon_font_size'        => '',
				'addon_font_line_height' => '',
				'addon_font_transform'   => '',

				'background_type'           => 'color',
				'background_color'          => '',
				'background_image'          => '',
				'background_image_size'     => 'large',
				'background_repeat'         => 'no-repeat',
				'background_size'           => 'initial',
				'background_position'       => 'middle-center',
				'background_gradient'       => '#83bae3||#80e0d4||0;100||180||linear',

				'padding_css'               => '',
				'margin_css'                => '',
				'border_css'                => '',

				'animation_in_type'         => 'none',
				'animation_in_duration'     => '300',
				'animation_in_delay'        => '0',
				'animation_in_offset'       => '100',

				'animation_loop_type'       => 'none',
				'animation_loop_duration'   => '1000',
				'animation_loop_delay'      => '1000',
				'animation_loop_hover'      => '',

				/* Icon */
				'mpc_icon__disable'         => '',
				'mpc_icon__preset'          => '',
				'mpc_icon__url'             => '',
				'mpc_icon__transition'      => 'none',

				'mpc_icon__padding_css'     => '',
				'mpc_icon__margin_css'      => '',

				'mpc_icon__border_css'      => '',

				'mpc_icon__icon_type'       => 'icon',
				'mpc_icon__icon'            => '',
				'mpc_icon__icon_character'  => '',
				'mpc_icon__icon_image'      => '',
				'mpc_icon__icon_image_size' => 'thumbnail',
				'mpc_icon__icon_preset'     => '',
				'mpc_icon__icon_size'       => '',
				'mpc_icon__icon_color'      => '',

				'mpc_icon__background_type'         => 'color',
				'mpc_icon__background_color'        => '',
				'mpc_icon__background_image'        => '',
				'mpc_icon__background_image_size'   => 'large',
				'mpc_icon__background_repeat'       => 'no-repeat',
				'mpc_icon__background_size'         => 'initial',
				'mpc_icon__background_position'     => 'middle-center',
				'mpc_icon__background_gradient'     => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_icon__animation_in_type'       => 'none',
				'mpc_icon__animation_in_duration'   => '300',
				'mpc_icon__animation_in_delay'      => '0',
				'mpc_icon__animation_in_offset'     => '100',

				'mpc_icon__animation_loop_type'     => 'none',
				'mpc_icon__animation_loop_duration' => '1000',
				'mpc_icon__animation_loop_delay'    => '1000',
				'mpc_icon__animation_loop_hover'    => '',

				/* Divider */
				'mpc_divider__disable'          => '',
				'mpc_divider__align'            => 'center',
				'mpc_divider__width'            => '100',

				'mpc_divider__content_type'     => 'none',
				'mpc_divider__content_position' => '50',

				'mpc_divider__lines_number'     => '1',
				'mpc_divider__lines_style'      => 'solid',
				'mpc_divider__lines_color'      => '',
				'mpc_divider__lines_gap'        => '1',
				'mpc_divider__lines_weight'     => '1',

				'mpc_divider__title'            => '',
				'mpc_divider__font_preset'      => '',
				'mpc_divider__font_color'       => '#333333',
				'mpc_divider__font_size'        => '18',
				'mpc_divider__font_line_height' => '',
				'mpc_divider__font_align'       => '',
				'mpc_divider__font_transform'   => '',

				'mpc_divider__icon_type'        => 'icon',
				'mpc_divider__icon'             => '',
				'mpc_divider__icon_character'   => '',
				'mpc_divider__icon_image'       => '',
				'mpc_divider__icon_image_size'  => 'thumbnail',
				'mpc_divider__icon_preset'      => '',
				'mpc_divider__icon_size'        => '',
				'mpc_divider__icon_color'       => '#333333',

				'mpc_divider__padding_css'      => '',
				'mpc_divider__margin_css'       => '',
			), $atts );

			/* Prepare */
			$animation    = MPC_Parser::animation( $atts );
			$atts_icon    = MPC_Parser::shortcode( $atts, 'mpc_icon_' );
			$atts_divider = MPC_Parser::shortcode( $atts, 'mpc_divider_' );

			$easing        = $atts[ 'disable_easing' ] != '' ? false : true;
			$value         = $atts[ 'value' ] != '' ? (float) esc_attr( $atts[ 'value' ] ) : 1;
			$duration      = $atts[ 'duration' ] != '' ? (int) esc_attr( $atts[ 'duration' ] ) : 100;
			$delay         = $atts[ 'delay' ] != '' ? (int) esc_attr( $atts[ 'delay' ] ) : 0;
			$initial_value = $atts[ 'type' ] == 'random' ? mt_rand( 0, $value ) : 0;

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
//			$classes = ' mpc-init mpc-parent--init mpc-transition mpc-waypoint';
			$classes = ' mpc-init mpc-transition mpc-waypoint';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-counter--' . esc_attr( $atts[ 'layout' ] ) : '';
			$classes .= $atts[ 'alignment' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'alignment' ] ) : ' mpc-align--center';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes_title   = $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'title_font_preset' ] ) : '';
			$classes_counter = $atts[ 'counter_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'counter_font_preset' ] ) : '';
			$classes_addons  = $atts[ 'addon_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'addon_font_preset' ] ) : '';
			$addon_atts = $atts[ 'addon_v_align' ] != '' ? ' data-v-align="' . esc_attr( $atts[ 'addon_v_align' ] ) . '"' : '';

			$counter_atts = array(
				'useEasing' => $easing,
				'value'     => $value,
				'initial'   => $initial_value,
				'duration'  => $duration * 0.001,
				'delay'     => $delay,
			);

			if( $atts[ 'enable_grouping' ] != '' ) {
				$options = array(
					'separator' => $atts[ 'separator' ],
					'decimals'  => (int) $atts[ 'decimals' ],
					'decimal'   => $atts[ 'decimal' ],
				);
			} else {
				$options = array(
					'decimals'  => 0,
					'separator' => '',
					'decimal'   => '',
				);
			}

			$counter_atts = array_merge( $counter_atts, $options );
			$final_value  = $this->get_final_value( $counter_atts, $atts[ 'enable_grouping' ] );
			$counter_atts = ' data-options="' . esc_attr( json_encode( $counter_atts ) ) . '"';


			/* Layout parts */
			$this->parts[ 'section_begin' ] = '<div class="mpc-counter__content">';
			$this->parts[ 'section_end' ]   = '</div>';
			$this->parts[ 'icon' ]          = $atts[ 'mpc_icon__disable' ] == '' ? $MPC_Icon->shortcode_template( $atts_icon ) : '';
			$this->parts[ 'title' ]         = $atts[ 'title' ] != '' ? '<h3 class="mpc-counter__heading' . $classes_title . '">' . $atts[ 'title' ] . '</h3>' : '';
			$this->parts[ 'divider' ]       = $atts[ 'mpc_divider__disable' ] == '' ? $MPC_Divider->shortcode_template( $atts_divider ) : '';
			$this->parts[ 'counter' ]       = '<div class="mpc-counter__counter' . $classes_counter . '"><div class="mpc-counter--target"' . $counter_atts . ' data-to="' . esc_attr( $value ) . '">' . $initial_value . '</div><div class="mpc-counter--sizer">' . $final_value . '</div></div>';

			$prefix = $atts[ 'prefix' ] != '' ? '<span class="mpc-counter__prefix' . $classes_addons . '">' . $atts[ 'prefix' ] . '</span>' : '';
			$suffix = $atts[ 'suffix' ] != '' ? '<span class="mpc-counter__suffix' . $classes_addons . '">' . $atts[ 'suffix' ] . '</span>' : '';

			$wrap = $prefix != '' || $suffix != '' ? '<div class="mpc-counter__wrap"' . $addon_atts . '>': '';
			$wrap_end = $wrap != '' ? '</div>' : '';
			$this->parts[ 'counter' ] = $wrap . $prefix . $this->parts[ 'counter' ] . $suffix . $wrap_end;

			if( $atts[ 'layout' ] == 'style_5' || $atts[ 'layout' ] == 'style_6' ) {
				$this->parts[ 'icon' ] = '<div class="mpc-icon__wrap mpc-icon--' . esc_attr( $atts[ 'icon_vertical' ] ) . '">' . $this->parts[ 'icon' ] . '</div>';
			}

			/* Shortcode Output */
			$return = '<div data-id="' . $css_id . '" class="mpc-counter' . $classes . '" ' . $animation . '>';
				$return .= $this->shortcode_layout( $atts[ 'layout' ], $this->parts );
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
			$css_id = uniqid( 'mpc_counter-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'title_font_size' ]   = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'counter_font_size' ] = $styles[ 'counter_font_size' ] != '' ? $styles[ 'counter_font_size' ] . ( is_numeric( $styles[ 'counter_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'addon_font_size' ] = $styles[ 'addon_font_size' ] != '' ? $styles[ 'addon_font_size' ] . ( is_numeric( $styles[ 'addon_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'addon_gap' ] = $styles[ 'addon_gap' ] != '' ? $styles[ 'addon_gap' ] . ( is_numeric( $styles[ 'addon_gap' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-counter[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Title
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) {
				$style .= '.mpc-counter[data-id="' . $css_id . '"] .mpc-counter__heading {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Content
			if ( $temp_style = MPC_CSS::font( $styles, 'counter' ) ) {
				$style .= '.mpc-counter[data-id="' . $css_id . '"] .mpc-counter__counter {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Prefix/Suffix
			if ( $temp_style = MPC_CSS::font( $styles, 'addon' ) ) {
				$style .= '.mpc-counter[data-id="' . $css_id . '"] .mpc-counter__prefix,';
				$style .= '.mpc-counter[data-id="' . $css_id . '"] .mpc-counter__suffix {';
					$style .= $temp_style;
				$style .= '}';
			}

			if( $styles[ 'addon_gap' ] != '' && $styles[ 'prefix' ] != '' ) {
				$style .= '.mpc-counter[data-id="' . $css_id . '"] .mpc-counter__prefix {';
					$style .= 'margin-right:' . $styles[ 'addon_gap' ];
				$style .= '}';
			}
			if( $styles[ 'addon_gap' ] != '' && $styles[ 'suffix' ] != '' ) {
				$style .= '.mpc-counter[data-id="' . $css_id . '"] .mpc-counter__suffix {';
					$style .= 'margin-left:' . $styles[ 'addon_gap' ];
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
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'param_name'  => 'preset',
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => 'style_1',
					'columns'          => '6',
					'layouts'          => array(
						'style_1' => '3',
						'style_2' => '3',
						'style_3' => '3',
						'style_4' => '3',
						'style_5' => '1',
						'style_6' => '1',
					),
					'std'              => 'style_1',
					'shortcode'        => $this->shortcode,
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Content Alignment', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Choose content alignment.', 'mpc' ),
					'value'            => '',
					'std'              => 'center',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Icon Alignment', 'mpc' ),
					'param_name'       => 'icon_vertical',
					'tooltip'          => __( 'Select icon vertical alignment to the counter.', 'mpc' ),
					'value'            => array(
						__( 'Top', 'mpc' )    => 'top',
						__( 'Middle', 'mpc' ) => 'middle',
						__( 'Bottom', 'mpc' ) => 'bottom',
					),
					'std'              => 'top',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_6' ) ),
				),
			);

			/* SECTION TITLE */
			$title = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Title', 'mpc' ),
					'tooltip'          => __( 'Define title.', 'mpc' ),
					'param_name'       => 'title',
					'admin_label'      => true,
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			/* SECTION COUNTER */
			$counter = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Type', 'mpc' ),
					'param_name'       => 'type',
					'tooltip'          => __( 'Select counter type:<br><b>Classic</b>: counts from zero to specified value;<br><b>Random</b>: counts from random number to specified value.', 'mpc' ),
					'value'            => array(
						__( 'Classic', 'mpc' ) => 'from-zero',
						__( 'Random', 'mpc' ) => 'random',
					),
					'std'              => 'from-zero',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Count To', 'mpc' ),
					'param_name'  => 'value',
					'admin_label' => true,
					'tooltip'     => __( 'Define counter value.', 'mpc' ),
					'value'       => '',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-arrow-down-alt',
						'align' => 'prepend',
					),
					'label'       => '',
					'validate'    => 'float',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-input--large',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Count Duration', 'mpc' ),
					'param_name'       => 'duration',
					'tooltip'          => __( 'Choose counting duration. For large numbers it\'s better to specify longer duration for smoother transition.', 'mpc' ),
					'min'              => 100,
					'max'              => 5000,
					'step'             => 50,
					'value'            => 100,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Start Delay', 'mpc' ),
					'param_name'       => 'delay',
					'tooltip'          => __( 'Choose a delay before the counting.', 'mpc' ),
					'min'              => 0,
					'max'              => 5000,
					'step'             => 50,
					'value'            => 0,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Use grouping', 'mpc' ),
					'param_name'       => 'enable_grouping',
					'tooltip'          => __( 'Check to enable thousand and decimal separators.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable easing', 'mpc' ),
					'param_name'       => 'disable_easing',
					'tooltip'          => __( 'Disable the counting easing (slow down at the end of counting).', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Separator', 'mpc' ),
					'param_name'  => 'separator',
					'tooltip'     => __( 'Define counter separator.', 'mpc' ),
					'value'       => ',',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-editor-textcolor',
						'align' => 'prepend',
					),
					'label'       => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both',
					'dependency'  => array( 'element' => 'enable_grouping', 'not_empty' => true ),
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Decimals', 'mpc' ),
					'param_name'  => 'decimals',
					'tooltip'     => __( 'Define number of decimals.', 'mpc' ),
					'value'       => '2',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-editor-textcolor',
						'align' => 'prepend',
					),
					'label'       => '',
					'validation'  => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'  => array( 'element' => 'enable_grouping', 'not_empty' => true ),
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Decimal Separator', 'mpc' ),
					'param_name'  => 'decimal',
					'tooltip'     => __( 'Define counter decimal separator.', 'mpc' ),
					'value'       => '.',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-editor-textcolor',
						'align' => 'prepend',
					),
					'label'       => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'  => array( 'element' => 'decimals', 'not_empty' => true ),
				),
			);

			/* Suffic/Prefix */
			$addons = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Vertical Align', 'mpc' ),
					'param_name'       => 'addon_v_align',
					'tooltip'          => __( 'Specify vertical position of prefix and suffix.', 'mpc' ),
					'value'            => array(
						__( 'Top', 'mpc' )    => 'top',
						__( 'Middle', 'mpc' ) => 'middle',
						__( 'Bottom', 'mpc' ) => 'bottom',
					),
					'std'              => 'bottom',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Prefix', 'mpc' ),
					'param_name'  => 'prefix',
					'admin_label' => true,
					'tooltip'     => __( 'Define counter prefix - a text displayed before the counter.', 'mpc' ),
					'value'       => '',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-editor-outdent',
						'align' => 'prepend',
					),
					'label'       => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-input--large',
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Suffix', 'mpc' ),
					'param_name'  => 'suffix',
					'admin_label' => true,
					'tooltip'     => __( 'Define counter prefix - a test displayed after the counter.', 'mpc' ),
					'value'       => '',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-editor-indent',
						'align' => 'prepend',
					),
					'label'       => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-input--large',
				),
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Gap', 'mpc' ),
					'param_name'  => 'addon_gap',
					'tooltip'     => __( 'Define the gap between Counter and prefix/suffix.', 'mpc' ),
					'value'       => '',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'       => '',
					'validate'    => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-input--large mpc-advanced-field',
				),
			);

			/* Integrate Icon */
			$icon_exclude   = array( 'exclude_regex' => '/hover_(.*)|transition|tooltip_(.*)/', 'exclude' => array( 'url' ) );
			$integrate_icon = vc_map_integrate_shortcode( 'mpc_icon', 'mpc_icon__', __( 'Icon', 'mpc' ), $icon_exclude );

			$disable_icon = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Icon', 'mpc' ),
					'param_name'       => 'mpc_icon__disable',
					'tooltip'          => __( 'Check to disable icon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_icon = array_merge( $disable_icon, $integrate_icon );

			/* Integrate Divider */
			$divider_exclude    = array( 'exclude_regex' => '/animation_(.*)/', );
			$divider_dependency = array( 'element' => 'layout', 'value' => array( 'style_1', 'style_2', 'style_3', 'style_4' ) );
			$integrate_divider  = vc_map_integrate_shortcode( 'mpc_divider', 'mpc_divider__', __( 'Divider', 'mpc' ), $divider_exclude, $divider_dependency );

			$disable_divider = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Divider', 'mpc' ),
					'param_name'       => 'mpc_divider__disable',
					'tooltip'          => __( 'Check to disable divider.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Divider', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_divider = array_merge( $disable_divider, $integrate_divider );

			$title_font   = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'with_align' => false ) );
			$counter_font = MPC_Snippets::vc_font( array( 'prefix' => 'counter', 'subtitle' => __( 'Counter', 'mpc' ), 'with_align' => false ) );
			$addon_font   = MPC_Snippets::vc_font( array( 'prefix' => 'addon', 'subtitle' => __( 'Suffix/Prefix', 'mpc' ), 'with_align' => false ) );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$title_font,
				$title,
				$counter_font,
				$counter,
				$addon_font,
				$addons,
				$background,
				$border,
				$padding,
				$margin,
				$integrate_icon,
				$integrate_divider,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Counter', 'mpc' ),
				'description' => __( 'Count to specified number', 'mpc' ),
				'base'        => 'mpc_counter',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-counter.png',
				'icon'        => 'mpc-shicon-counter',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Counter' ) ) {
	global $MPC_Counter;
	$MPC_Counter = new MPC_Counter;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_counter' ) ) {
	class WPBakeryShortCode_mpc_counter extends MPCShortCode_Base {}
}
