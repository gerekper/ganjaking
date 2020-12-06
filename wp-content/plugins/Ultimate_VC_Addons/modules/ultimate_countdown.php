<?php
/**
 *  UAVC Ultimate CountDown module file
 *
 *  @package Ultimate CountDown
 */

if ( ! class_exists( 'Ultimate_CountDown' ) ) {
		/**
		 * Function that initializes Ultimate CountDown Module
		 *
		 * @class Ultimate_CountDown
		 */
	class Ultimate_CountDown {
				/**
				 * Constructor function that constructs default values for the Ultimate CountDown module.
				 *
				 * @method __construct
				 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'countdown_init' ) );
			}
			add_shortcode( 'ult_countdown', array( $this, 'countdown_shortcode' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'count_down_scripts' ), 1 );
		}
		/**
		 * Function to regester style and scripts
		 *
		 * @since ----
		 * @access public
		 */
		public function count_down_scripts() {

			Ultimate_VC_Addons::ultimate_register_script( 'jquery.timecircle', 'countdown', false, array( 'jquery' ), ULTIMATE_VERSION, false );
			Ultimate_VC_Addons::ultimate_register_script( 'jquery.countdown', 'count-timer', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_style( 'ult-countdown', 'countdown' );
		}
				/**
				 * Function for button admin script
				 *
				 * @since ----
				 * @param mixed $hook for the script.
				 * @access public
				 */
		public function admin_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {

					Ultimate_VC_Addons::ultimate_register_style( 'ult-colorpicker-style', UAVC_URL . 'admin/css/bootstrap-datetimepicker-admin.css', true );

					wp_enqueue_style( 'ult-colorpicker-style' );
				}
			}
		}
		/**
		 * Function to intialize the button module
		 *
		 * @since ----
		 * @access public
		 */
		public function countdown_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Countdown', 'ultimate_vc' ),
						'base'        => 'ult_countdown',
						'class'       => 'vc_countdown',
						'icon'        => 'vc_countdown',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Countdown Timer.', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Countdown Timer Style', 'ultimate_vc' ),
								'param_name' => 'count_style',
								'value'      => array(
									__( 'Digit and Unit Side by Side', 'smile' ) => 'ult-cd-s1',
									__( 'Digit and Unit Up and Down', 'smile' ) => 'ult-cd-s2',
								),
								'group'      => 'General Settings',

							),
							array(
								'type'        => 'datetimepicker',
								'class'       => '',
								'heading'     => __( 'Target Time For Countdown', 'ultimate_vc' ),
								'param_name'  => 'datetime',
								'value'       => '',
								'description' => __( 'Date and time format (yyyy/mm/dd hh:mm:ss).', 'ultimate_vc' ),
								'group'       => 'General Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Countdown Timer Depends on', 'ultimate_vc' ),
								'param_name' => 'ult_tz',
								'value'      => array(
									__( 'WordPress Defined Timezone', 'ultimate_vc' ) => 'ult-wptz',
									__( "User's System Timezone", 'ultimate_vc' ) => 'ult-usrtz',
								),

								'group'      => 'General Settings',
							),
							array(
								'type'       => 'checkbox',
								'class'      => '',
								'heading'    => __( 'Select Time Units To Display In Countdown Timer', 'ultimate_vc' ),
								'param_name' => 'countdown_opts',
								'value'      => array(
									__( 'Years', 'ultimate_vc' ) => 'syear',
									__( 'Months', 'ultimate_vc' ) => 'smonth',
									__( 'Weeks', 'ultimate_vc' ) => 'sweek',
									__( 'Days', 'ultimate_vc' ) => 'sday',
									__( 'Hours', 'ultimate_vc' ) => 'shr',
									__( 'Minutes', 'ultimate_vc' ) => 'smin',
									__( 'Seconds', 'ultimate_vc' ) => 'ssec',
								),

								'group'      => 'General Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Timer Digit Border Style', 'ultimate_vc' ),
								'param_name' => 'br_style',
								'value'      => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),

								'group'      => 'General Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Timer Digit Border Size', 'ultimate_vc' ),
								'param_name' => 'br_size',
								'value'      => '',
								'min'        => '0',
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'br_style',
									'value'   => array( 'solid', 'dotted', 'dashed', 'double', 'inset', 'outset' ),
								),
								'group'      => 'General Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Timer Digit Border Color', 'ultimate_vc' ),
								'param_name' => 'br_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'br_style',
									'value'   => array( 'solid', 'dotted', 'dashed', 'double', 'inset', 'outset' ),
								),
								'group'      => 'General Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Timer Digit Border Radius', 'ultimate_vc' ),
								'param_name' => 'br_radius',
								'value'      => '',
								'min'        => '0',
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'br_style',
									'value'   => array( 'solid', 'dotted', 'dashed', 'double', 'inset', 'outset' ),
								),
								'group'      => 'General Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Timer Digit Background Color', 'ultimate_vc' ),
								'param_name' => 'timer_bg_color',
								'value'      => '',
								'group'      => 'General Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Timer Digit Background Size', 'ultimate_vc' ),
								'param_name' => 'br_time_space',
								'min'        => '0',
								'value'      => '0',
								'suffix'     => 'px',
								'group'      => 'General Settings',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Extra Class for the Wrapper.', 'ultimate_vc' ),
								'group'       => 'General Settings',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Day (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_days',
								'value'      => 'Day',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Days (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_days2',
								'value'      => 'Days',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Week (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_weeks',
								'value'      => 'Week',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Weeks (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_weeks2',
								'value'      => 'Weeks',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Month (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_months',
								'value'      => 'Month',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Months (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_months2',
								'value'      => 'Months',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Year (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_years',
								'value'      => 'Year',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Years (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_years2',
								'value'      => 'Years',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Hour (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_hours',
								'value'      => 'Hour',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Hours (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_hours2',
								'value'      => 'Hours',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Minute (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_minutes',
								'value'      => 'Minute',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Minutes (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_minutes2',
								'value'      => 'Minutes',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Second (Singular)', 'ultimate_vc' ),
								'param_name' => 'string_seconds',
								'value'      => 'Second',
								'group'      => 'Strings Translation',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Seconds (Plural)', 'ultimate_vc' ),
								'param_name' => 'string_seconds2',
								'value'      => 'Seconds',
								'group'      => 'Strings Translation',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/szdd2' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
								'group'            => 'General Settings',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Timer Digit Settings', 'ultimate_vc' ),
								'param_name'       => 'countdown_typograpy',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'timer_digit_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'tick_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Timer Digit Text Color', 'ultimate_vc' ),
								'param_name' => 'tick_col',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Timer Digit Text Size', 'ultimate_vc' ),
								'param_name' => 'tick_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Timer Digit Text Line height', 'ultimate_vc' ),
								'param_name' => 'tick_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Timer Unit Settings', 'ultimate_vc' ),
								'param_name'       => 'countdown_typograpy',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'timer_unit_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'tick_unit_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Timer Unit Text Color', 'ultimate_vc' ),
								'param_name' => 'tick_sep_col',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Timer Unit Text Size', 'ultimate_vc' ),
								'param_name' => 'tick_sep_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Timer Unit Line Height', 'ultimate_vc' ),
								'param_name' => 'tick_sep_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_countdown',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}
		/**
		 * Shortcode handler function for  icon block.
		 *
		 * @since ----
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function countdown_shortcode( $atts ) {

				$ult_uco_settings = shortcode_atts(
					array(
						'count_style'             => 'ult-cd-s1',
						'datetime'                => '',
						'ult_tz'                  => 'ult-wptz',
						'countdown_opts'          => '',
						'tick_col'                => '',
						'tick_size'               => '36',
						'tick_line_height'        => '',
						'tick_style'              => '',
						'tick_unit_style'         => '',
						'timer_digit_font_family' => '',
						'timer_unit_font_family'  => '',
						'tick_sep_col'            => '',
						'tick_sep_size'           => '13',
						'tick_sep_line_height'    => '',
						'tick_sep_style'          => '',
						'br_color'                => '',
						'br_style'                => '',
						'br_size'                 => '',
						'timer_bg_color'          => '',
						'br_radius'               => '',
						'br_time_space'           => '0',
						'el_class'                => '',
						'string_days'             => 'Day',
						'string_days2'            => 'Days',
						'string_weeks'            => 'Week',
						'string_weeks2'           => 'Weeks',
						'string_months'           => 'Month',
						'string_months2'          => 'Months',
						'string_years'            => 'Year',
						'string_years2'           => 'Years',
						'string_hours'            => 'Hour',
						'string_hours2'           => 'Hours',
						'string_minutes'          => 'Minute',
						'string_minutes2'         => 'Minutes',
						'string_seconds'          => 'Second',
						'string_seconds2'         => 'Seconds',
						'css_countdown'           => '',
					),
					$atts
				);

			$count_frmt             = '';
			$labels                 = '';
			$countdown_design_style = '';
			$tdfamily               = '';
			$tunifamily             = '';
			$labels                 = $ult_uco_settings['string_years2'] . ',' . $ult_uco_settings['string_months2'] . ',' . $ult_uco_settings['string_weeks2'] . ',' . $ult_uco_settings['string_days2'] . ',' . $ult_uco_settings['string_hours2'] . ',' . $ult_uco_settings['string_minutes2'] . ',' . $ult_uco_settings['string_seconds2'];
			$labels2                = $ult_uco_settings['string_years'] . ',' . $ult_uco_settings['string_months'] . ',' . $ult_uco_settings['string_weeks'] . ',' . $ult_uco_settings['string_days'] . ',' . $ult_uco_settings['string_hours'] . ',' . $ult_uco_settings['string_minutes'] . ',' . $ult_uco_settings['string_seconds'];
			$countdown_opt          = explode( ',', $ult_uco_settings['countdown_opts'] );
			if ( is_array( $countdown_opt ) ) {
				foreach ( $countdown_opt as $opt ) {
					if ( 'syear' == $opt ) {
						$count_frmt .= 'Y';
					}
					if ( 'smonth' == $opt ) {
						$count_frmt .= 'O';
					}
					if ( 'sweek' == $opt ) {
						$count_frmt .= 'W';
					}
					if ( 'sday' == $opt ) {
						$count_frmt .= 'D';
					}
					if ( 'shr' == $opt ) {
						$count_frmt .= 'H';
					}
					if ( 'smin' == $opt ) {
						$count_frmt .= 'M';
					}
					if ( 'ssec' == $opt ) {
						$count_frmt .= 'S';
					}
				}
			}
			if ( is_numeric( $ult_uco_settings['tick_size'] ) ) {
				$ult_uco_settings['tick_size'] = 'desktop:' . $ult_uco_settings['tick_size'] . 'px;';
			}
			$countdown_design_style = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_uco_settings['css_countdown'], ' ' ), 'ult_countdown', $atts );
			$countdown_design_style = esc_attr( $countdown_design_style );
			$countdown_id           = 'countdown-wrap-' . wp_rand( 1000, 9999 );

			$data_attr = '';
			if ( '' == $count_frmt ) {
				$count_frmt = 'DHMS';
			}
			if ( '' == $ult_uco_settings['br_size'] || '' == $ult_uco_settings['br_color'] || '' == $ult_uco_settings['br_style'] ) {
				if ( '' == $ult_uco_settings['timer_bg_color'] ) {
					$ult_uco_settings['el_class'] .= ' ult-cd-no-border';
				}
			} else {
				$data_attr .= 'data-br-color="' . esc_attr( $ult_uco_settings['br_color'] ) . '" data-br-style="' . esc_attr( $ult_uco_settings['br_style'] ) . '" data-br-size="' . esc_attr( $ult_uco_settings['br_size'] ) . '" ';
			}
			// Responsive param.

			if ( is_numeric( $ult_uco_settings['tick_sep_size'] ) ) {
				$ult_uco_settings['tick_sep_size'] = 'desktop:' . $ult_uco_settings['tick_sep_size'] . 'px;';       }
			if ( is_numeric( $ult_uco_settings['tick_sep_line_height'] ) ) {
				$ult_uco_settings['tick_sep_line_height'] = 'desktop:' . $ult_uco_settings['tick_sep_line_height'] . 'px;';     }

			$count_down_id            = 'count-down-wrap-' . wp_rand( 1000, 9999 );
			$count_down_sep_args      = array(
				'target'      => '#' . $count_down_id . ' .ult_countdown-period',
				'media_sizes' => array(
					'font-size'   => $ult_uco_settings['tick_sep_size'],
					'line-height' => $ult_uco_settings['tick_sep_line_height'],
				),
			);
			$count_down_sep_data_list = get_ultimate_vc_responsive_media_css( $count_down_sep_args );
			if ( '' != $ult_uco_settings['timer_digit_font_family'] ) {
				$tdfamily            = get_ultimate_font_family( $ult_uco_settings['timer_digit_font_family'] );
				$timer_d_font_family = 'font-family:\'' . $tdfamily . '\';';
			}
			if ( '' != $ult_uco_settings['timer_unit_font_family'] ) {
				$tunifamily     = get_ultimate_font_family( $ult_uco_settings['timer_unit_font_family'] );
					$data_attr .= ' data-tuni-font-family ="' . esc_attr( $tunifamily ) . '"';
			}
			$stick_style      = get_ultimate_font_style( $ult_uco_settings['tick_style'] );
			$stick_unit_style = get_ultimate_font_style( $ult_uco_settings['tick_unit_style'] );
			$data_attr       .= ' data-tick-style="' . esc_attr( $stick_style ) . '" ';
			$data_attr       .= ' data-tick-p-style="' . esc_attr( $ult_uco_settings['tick_sep_style'] ) . '" ';
			$data_attr       .= ' data-bg-color="' . esc_attr( $ult_uco_settings['timer_bg_color'] ) . '" data-br-radius="' . esc_attr( $ult_uco_settings['br_radius'] ) . '" data-padd="' . esc_attr( $ult_uco_settings['br_time_space'] ) . '" ';

			switch ( $ult_uco_settings['tick_style'] ) {
				case 'bold':
								$tick_style_css = 'font-weight:bold;';
					break;
				case 'italic':
								$tick_style_css = 'font-style:italic;';
					break;
				case 'boldnitalic':
								$tick_style_css  = 'font-weight:bold;';
								$tick_style_css .= 'font-style:italic;';
					break;
				default:
								$tick_style_css = $ult_uco_settings['tick_style'];
					break;
			}
			switch ( $ult_uco_settings['tick_sep_style'] ) {
				case 'bold':
								$tick_sep_style_css = 'font-weight:bold;';
					break;
				case 'italic':
								$tick_sep_style_css = 'font-style:italic;';
					break;
				case 'boldnitalic':
						$tick_sep_style_css = 'font-style:italic;';
						$tick_sep_style_css = 'font-weight:bold;';
					break;
				default:
						$tick_sep_style_css = $ult_uco_settings['tick_sep_style'];
					break;
			}
			$output  = '<style>';
			$output .= '#' . $count_down_id . ' .ult_countdown-amount { ';
			$output .= $stick_style;
			$output .= $tick_style_css;
			$output .= '  font-family : ' . $tdfamily . ';';
			$output .= '	color: ' . $ult_uco_settings['tick_col'] . ';';
			$output .= ' } ';
			$output .= '#' . $count_down_id . ' .ult_countdown-period{';
			$output .= $stick_unit_style;
			$output .= '	color: ' . $ult_uco_settings['tick_sep_col'] . ';';
			$output .= '	font-family: ' . $tunifamily . ';';
			$output .= $tick_sep_style_css;
			$output .= $ult_uco_settings['tick_sep_style'];
			$output .= '}';

			$output .= '</style>';
			$output .= '<div "' . $count_down_sep_data_list . '" class="ult-responsive ult_countdown ' . esc_attr( $countdown_design_style ) . ' ' . esc_attr( $ult_uco_settings['el_class'] ) . ' ' . esc_attr( $ult_uco_settings['count_style'] ) . '">';

			// Responsive param.

			if ( is_numeric( $ult_uco_settings['tick_size'] ) ) {
				$ult_uco_settings['tick_size'] = 'desktop:' . $ult_uco_settings['tick_size'] . 'px;';       }
			if ( is_numeric( $ult_uco_settings['tick_line_height'] ) ) {
				$ult_uco_settings['tick_line_height'] = 'desktop:' . $ult_uco_settings['tick_line_height'] . 'px;';     }

			$count_down_args      = array(
				'target'      => '#' . $count_down_id . ' .ult_countdown-amount',
				'media_sizes' => array(
					'font-size'   => $ult_uco_settings['tick_size'],
					'line-height' => $ult_uco_settings['tick_line_height'],
				),
			);
			$count_down_data_list = get_ultimate_vc_responsive_media_css( $count_down_args );

			if ( '' != $ult_uco_settings['datetime'] ) {
				$output .= '<div id="' . esc_attr( $count_down_id ) . '"  class="ult-responsive ult_countdown-div ult_countdown-dateAndTime ' . esc_attr( $ult_uco_settings['ult_tz'] ) . '" data-labels="' . esc_attr( $labels ) . '" data-labels2="' . esc_attr( $labels2 ) . '"  data-terminal-date="' . esc_attr( $ult_uco_settings['datetime'] ) . '" data-countformat="' . esc_attr( $count_frmt ) . '" data-time-zone="' . esc_attr( get_option( 'gmt_offset' ) ) . '" data-time-now="' . esc_attr( str_replace( '-', '/', current_time( 'mysql' ) ) ) . '"  data-tick-col="' . esc_attr( $ult_uco_settings['tick_col'] ) . '"  ' . $count_down_data_list . ' data-tick-p-col="' . esc_attr( $ult_uco_settings['tick_sep_col'] ) . '" ' . $data_attr . '>' . $ult_uco_settings['datetime'] . '</div>';
			}
			$output   .= '</div>';
			$is_preset = false;
			if ( isset( $_GET['preset'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$is_preset = true;
			}
			if ( $is_preset ) {
				$text = 'array ( ';
				foreach ( $atts as $key => $att ) {
					$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
				}
				if ( '' != $content ) {
					$text .= '<br/>	\'content\' => \'' . $content . '\',';
				}
				$text   .= '<br/>)';
				$output .= '<pre>';
				$output .= $text;
				$output .= '</pre>'; // remove backslash once copied.
			}
			return $output;
		}
	}
	// instantiate the class.
	$ult_countdown = new Ultimate_CountDown();
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ult_Countdown' ) ) {
				/**
				 * Function that initializes Ultimate Countdown Module
				 *
				 * @class WPBakeryShortCode_Ult_Countdown
				 */
		class WPBakeryShortCode_Ult_Countdown extends WPBakeryShortCode {
		}
	}
}
