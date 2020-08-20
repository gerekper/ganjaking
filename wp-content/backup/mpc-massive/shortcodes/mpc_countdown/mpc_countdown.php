<?php
/*----------------------------------------------------------------------------*\
	COUNTDOWN SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Countdown' ) ) {
	class MPC_Countdown {
		public $shortcode = 'mpc_countdown';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_countdown', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_countdown-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_countdown/css/mpc_countdown.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_countdown-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_countdown/js/mpc_countdown' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-countdown-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/jquery.countdown.min.css' );
			wp_enqueue_script( 'mpc-massive-countdown-base-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/jquery.countdown.base.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'mpc-massive-countdown-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/jquery.countdown.min.js', array( 'jquery', 'mpc-massive-countdown-base-js' ), '', true );

			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                   => '',
				'preset'                  => '',
				'alignment'               => 'bottom',
				'date'                    => '',

				'years'                   => '',
				'months'                  => '',
				'days'                    => 'true',
				'hours'                   => 'true',
				'minutes'                 => 'true',
				'seconds'                 => 'true',

				'force_square'            => '',

				'years_label'             => __( 'Years', 'mpc' ),
				'months_label'            => __( 'Months', 'mpc' ),
				'days_label'              => __( 'Days', 'mpc' ),
				'hours_label'             => __( 'Hours', 'mpc' ),
				'minutes_label'           => __( 'Minutes', 'mpc' ),
				'seconds_label'           => __( 'Seconds', 'mpc' ),

				'item_font_preset'        => '',
				'item_font_color'         => '',
				'item_font_size'          => '',
				'item_font_line_height'   => '',
				'item_font_align'         => '',
				'item_font_transform'     => '',

				'label_font_preset'       => '',
				'label_font_color'        => '',
				'label_font_size'         => '',
				'label_font_line_height'  => '',
				'label_font_align'        => '',
				'label_font_transform'    => '',

				'background_type'         => 'color',
				'background_color'        => '',
				'background_image'        => '',
				'background_image_size'   => 'large',
				'background_repeat'       => 'no-repeat',
				'background_size'         => 'initial',
				'background_position'     => 'middle-center',
				'background_gradient'     => '#83bae3||#80e0d4||0;100||180||linear',

				'padding_css'             => '',
				'margin_css'              => '',
				'border_css'              => '',
				'elem_margin_css'         => '',

				'animation_in_type'       => 'none',
				'animation_in_duration'   => '300',
				'animation_in_delay'      => '0',
				'animation_in_offset'     => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $atts );

			/* Prepare date */
			$date_time = explode( ' ', $atts[ 'date' ] );
			$DateTime  = new DateTime;

			if( is_array( $date_time ) && sizeof( $date_time ) == 2 ) {
				$date = explode( '/', $date_time[ 0 ] );
				$time = explode( ':', $date_time[ 1 ] );

				$DateTime->setDate( $date[ 2 ], $date[ 1 ], $date[ 0 ] );
				$DateTime->setTime( $time[ 0 ], $time[ 1 ] );
			}

			/* Prepare format */
			$date_format = '';

			$date_format .= $atts[ 'years' ] != '' ? 'Y' : '';
			$date_format .= $atts[ 'months' ] != '' ? 'O' : '';
			$date_format .= $atts[ 'days' ] != '' ? 'D' : '';
			$date_format .= $atts[ 'hours' ] != '' ? 'H' : '';
			$date_format .= $atts[ 'minutes' ] != '' ? 'M' : '';
			$date_format .= $atts[ 'seconds' ] != '' ? 'S' : '';

			/* Prepare labels */
			$date_labels = array(
				$atts[ 'years_label' ],
				$atts[ 'months_label' ],
				'',
				$atts[ 'days_label' ],
				$atts[ 'hours_label' ],
				$atts[ 'minutes_label' ],
				$atts[ 'seconds_label' ],
			);

			/* Prepare layout */
			$date_layout = $atts[ 'alignment' ] == 'top' ? 'top' : 'bottom';
			$data_force_square = $atts[ 'force_square' ] != '' ? ' data-square="1"' : '';

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$typography = $atts[ 'item_font_preset' ] != '' ? ' data-item-typo="' . esc_attr( $atts[ 'item_font_preset' ] ) . '"' : '';
			$typography .= $atts[ 'label_font_preset' ] != '' ? ' data-label-typo="' . esc_attr( $atts[ 'label_font_preset' ] ) . '"' : '';

            /* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-countdown' . $classes . '" ' . $animation . $data_force_square . $typography . '>';
				$return .= '<div class="mpc-countdown__content" data-until="' . $DateTime->format("F j, Y H:i:s") . '" data-format="' . $date_format . '" data-layout="' . $date_layout . '" data-labels="' . esc_attr( implode( '/', $date_labels ) ) . '"></div>';
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
			$css_id = uniqid( 'mpc_countdown-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'item_font_size' ]  = $styles[ 'item_font_size' ] != '' ? $styles[ 'item_font_size' ] . ( is_numeric( $styles[ 'item_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'label_font_size' ] = $styles[ 'label_font_size' ] != '' ? $styles[ 'label_font_size' ] . ( is_numeric( $styles[ 'label_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			if ( $styles[ 'elem_margin_css' ] ) {
				$style .= '.mpc-countdown[id="' . $css_id . '"] {';
					$style .= $styles[ 'elem_margin_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles, 'item' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-countdown[id="' . $css_id . '"] .mpc-countdown__section .mpc-main {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::font( $styles, 'label' ) ) {
				$style .= '.mpc-countdown[id="' . $css_id . '"] .mpc-countdown__section h4 {';
					$style .= $temp_style;
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
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Description Position', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Select description position.', 'mpc' ),
					'value'            => array(
						__( 'Top', 'mpc' )    => 'top',
						__( 'Bottom', 'mpc' ) => 'bottom',
					),
					'std'              => 'bottom',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_datetime',
					'heading'          => __( 'End Date', 'mpc' ),
					'param_name'       => 'date',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose end date for countdown.', 'mpc' ),
					'value'            => '',
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Force Square', 'mpc' ),
					'param_name'       => 'force_square',
					'tooltip'          => __( 'Display each countdown section as square.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			/* SECTIONS */
			$sections = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Countdown Sections', 'mpc' ),
					'param_name'       => 'sections_divider',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Years', 'mpc' ),
					'param_name'       => 'years',
					'tooltip'          => __( 'Display years section in countdown.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Years Label', 'mpc' ),
					'param_name'       => 'year_label',
					'tooltip'          => __( 'Define years section label.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-calendar-alt',
						'align' => 'prepend'
					),
					'validate'         => 'false',
					'placeholder'      => __( 'Years', 'mpc' ),
					'dependency'       => array(
						'element' => 'years',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),

				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Months', 'mpc' ),
					'param_name'       => 'months',
					'tooltip'          => __( 'Display months section in countdown.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Months Label', 'mpc' ),
					'param_name'       => 'months_label',
					'tooltip'          => __( 'Define months section label.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-calendar-alt',
						'align' => 'prepend'
					),
					'validate'         => 'false',
					'placeholder'      => __( 'Months', 'mpc' ),
					'dependency'       => array(
						'element' => 'months',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),

				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Days', 'mpc' ),
					'param_name'       => 'days',
					'tooltip'          => __( 'Display days section in countdown.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Days Label', 'mpc' ),
					'param_name'       => 'days_label',
					'tooltip'          => __( 'Define days section label.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-calendar-alt',
						'align' => 'prepend'
					),
					'validate'         => 'false',
					'placeholder'      => __( 'Days', 'mpc' ),
					'dependency'       => array(
						'element' => 'days',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),

				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Hours', 'mpc' ),
					'param_name'       => 'hours',
					'tooltip'          => __( 'Display hours section in countdown.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Hours Label', 'mpc' ),
					'param_name'       => 'hours_label',
					'tooltip'          => __( 'Define hours section label.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-calendar-alt',
						'align' => 'prepend'
					),
					'validate'         => 'false',
					'placeholder'      => __( 'Hours', 'mpc' ),
					'dependency'       => array(
						'element' => 'hours',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),

				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Minutes', 'mpc' ),
					'param_name'       => 'minutes',
					'tooltip'          => __( 'Display minutes section in countdown.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Minutes Label', 'mpc' ),
					'param_name'       => 'minutes_label',
					'tooltip'          => __( 'Define minutes section label.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-calendar-alt',
						'align' => 'prepend'
					),
					'validate'         => 'false',
					'placeholder'      => __( 'Minutes', 'mpc' ),
					'dependency'       => array(
						'element' => 'minutes',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),

				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Seconds', 'mpc' ),
					'param_name'       => 'seconds',
					'tooltip'          => __( 'Display seconds section in countdown.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Seconds Label', 'mpc' ),
					'param_name'       => 'seconds_label',
					'tooltip'          => __( 'Define seconds section label.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-calendar-alt',
						'align' => 'prepend'
					),
					'validate'         => 'false',
					'placeholder'      => __( 'Seconds', 'mpc' ),
					'dependency'       => array(
						'element' => 'seconds',
						'value'   => 'true',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$base_atts = array( 'subtitle' => __( 'Section', 'mpc' ) );

			$font  = MPC_Snippets::vc_font( array( 'prefix' => 'item', 'subtitle' => __( 'Section', 'mpc' ), 'with_align' => true ) );
			$label = MPC_Snippets::vc_font( array( 'prefix' => 'label', 'subtitle' => __( 'Label', 'mpc' ), 'with_align' => true ) );

			$background = MPC_Snippets::vc_background( $base_atts );
			$border     = MPC_Snippets::vc_border( $base_atts );
			$padding    = MPC_Snippets::vc_padding( $base_atts );
			$margin     = MPC_Snippets::vc_margin( $base_atts );

			$elem_margin     = MPC_Snippets::vc_margin( array( 'prefix' => 'elem' ) );

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$sections,
				$font,
				$label,
				$background,
				$border,
				$padding,
				$margin,
				$elem_margin,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Countdown', 'mpc' ),
				'description' => __( 'Count to specified date', 'mpc' ),
				'base'        => 'mpc_countdown',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-countdown.png',
				'icon'        => 'mpc-shicon-countdown',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Countdown' ) ) {
	global $MPC_Countdown;
	$MPC_Countdown = new MPC_Countdown;
}
