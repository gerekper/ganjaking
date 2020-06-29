<?php
/*----------------------------------------------------------------------------*\
	PROGRESS SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Progress' ) ) {
	class MPC_Progress {
		public $shortcode = 'mpc_progress';
		private $parts    = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_progress', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$this->parts = array(
				'title'          => '',
				'value'          => '',
				'info_begin'     => '<div class="mpc-progress__info">',
				'info_end'       => '</div>',
				'box_begin'      => '<div class="mpc-progress__wrap"><div class="mpc-progress__box">',
				'box_end'        => '</div></div>',
				'progress_begin' => '<div class="mpc-progress__bar">',
				'progress_end'   => '</div>',
				'icons'          => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_progress-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_progress/css/mpc_progress.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_progress-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_progress/js/mpc_progress' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $layout ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'info_begin', 'title', 'value', 'info_end', 'box_begin', 'progress_begin', 'progress_end', 'box_end' ),
				'style_2' => array( 'box_begin', 'progress_begin', 'progress_end', 'info_begin', 'title', 'value', 'info_end', 'box_end' ),
				'style_3' => array( 'box_begin', 'progress_begin', 'progress_end', 'box_end', 'info_begin', 'title', 'value', 'info_end' ),
				'style_4' => array( 'box_begin', 'progress_begin', 'progress_end', 'box_end', 'info_begin', 'title', 'value', 'info_end' ),
				'style_5' => array( 'box_begin', 'icons', 'box_end', 'info_begin', 'title', 'value', 'info_end' ),
				'style_6' => array( 'box_begin', 'progress_begin', 'progress_end', 'box_end', 'info_begin', 'title', 'value', 'info_end' ),
				'style_7' => array( 'box_begin', 'progress_begin', 'value', 'progress_end', 'box_end', 'info_begin', 'title', 'info_end' ),
				'style_8' => array( 'info_begin', 'title', 'value', 'info_end', 'box_begin', 'icons', 'box_end' ),
			);

			if ( ! isset( $layouts[ $layout ] ) ) {
				return $content;
			}

			foreach( $layouts[ $layout ] as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                 => '',
				'preset'                 => '',
				'layout'                 => 'style_1',
				'value'                  => '75',
				'thickness'              => '30',

				'border_css'             => '',
				'padding_css'            => '',
				'margin_css'             => '',

				'background_type'        => 'color',
				'background_color'       => '',
				'background_image'       => '',
				'background_image_size'  => 'large',
				'background_repeat'      => 'no-repeat',
				'background_size'        => 'initial',
				'background_position'    => 'middle-center',
				'background_gradient'    => '#83bae3||#80e0d4||0;100||180||linear',

				// Title
				'disable_title'          => '',
				'title'                  => '',
				'title_padding_css'      => '',

				'title_font_preset'      => '',
				'title_font_color'       => '',
				'title_font_size'        => '',
				'title_font_line_height' => '',
				'title_font_align'       => '',
				'title_font_transform'   => '',

				// Value
				'disable_value'          => '',
				'value_number'           => '',
				'value_unit'             => '%',
				'value_sticky'           => '',
				'value_padding_css'      => '',

				'value_font_preset'      => '',
				'value_font_color'       => '',
				'value_font_size'        => '',
				'value_font_line_height' => '',
				'value_font_align'       => '',
				'value_font_transform'   => '',

				// Icon
				'icon_type'                  => 'icon',
				'icon'                       => '',
				'icon_character'             => '',
				'icon_image'                 => '',
				'icon_image_size'            => 'thumbnail',
				'icon_preset'                => '',
				'icon_color'                 => '#333333',
				'icon_size'                  => '',

				'icon_gap'                   => '',
				'icon_border_css'            => '',

				'icon_background_type'       => 'color',
				'icon_background_color'      => '',
				'icon_background_image'      => '',
				'icon_background_image_size' => 'large',
				'icon_background_repeat'     => 'no-repeat',
				'icon_background_size'       => 'initial',
				'icon_background_position'   => 'middle-center',
				'icon_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				// Progress
				'progress_border_css'                 => '',

				'progress_background_type'            => 'color',
				'progress_background_color'           => '',
				'progress_background_image'           => '',
				'progress_background_image_size'      => 'large',
				'progress_background_repeat'          => 'no-repeat',
				'progress_background_size'            => 'initial',
				'progress_background_position'        => 'middle-center',
				'progress_background_gradient'        => '#83bae3||#80e0d4||0;100||180||linear',

				'progress_icon_type'                  => 'icon',
				'progress_icon'                       => '',
				'progress_icon_character'             => '',
				'progress_icon_image'                 => '',
				'progress_icon_image_size'            => 'thumbnail',
				'progress_icon_preset'                => '',
				'progress_icon_color'                 => '#333333',
				'progress_icon_size'                  => '',

				'progress_icon_border_css'            => '',

				'progress_icon_background_type'       => 'color',
				'progress_icon_background_color'      => '',
				'progress_icon_background_image'      => '',
				'progress_icon_background_image_size' => 'large',
				'progress_icon_background_repeat'     => 'no-repeat',
				'progress_icon_background_size'       => 'initial',
				'progress_icon_background_position'   => 'middle-center',
				'progress_icon_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				// Animation
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

			if ( $atts[ 'layout' ] == 'style_5' || $atts[ 'layout' ] == 'style_8' ) {
				$icon          = MPC_Parser::icon( $atts );
				$progress_icon = MPC_Parser::icon( $atts, 'progress' );

				$icon_box = '<div class="mpc-progress__icon-box">';
					$icon_box .= '<i class="mpc-progress__icon mpc-layer--back' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i>';
					$icon_box .= '<i class="mpc-progress__icon mpc-layer--front' . $progress_icon[ 'class' ] . '">' . $progress_icon[ 'content' ] . '</i>';
				$icon_box .= '</div>';

				$this->parts[ 'icons' ] = str_repeat( $icon_box, 10 );
			} else {
				$this->parts[ 'icons' ] = '';
			}

			if ( $atts[ 'disable_title' ] == '' && $atts[ 'title' ] != '' ) {
				$title = $atts[ 'title' ];
			} else {
				$title = '&nbsp;';
			}

			$this->parts[ 'title' ] = '<h4 class="mpc-progress__title' . ( $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'title_font_preset' ] ) : '' ) . '">' . $title . '</h4>';

			if ( $atts[ 'disable_value' ] == '' ) {
				$this->parts[ 'value' ] = '<div class="mpc-progress__value' . ( $atts[ 'value_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'value_font_preset' ] ) : '' ) . '"></div>';
			} else {
				$this->parts[ 'value' ] = '';

				if ( $title == '&nbsp;' ) {
					$this->parts[ 'title' ] = '';
				}
			}

			$animation = MPC_Parser::animation( $atts );
			$classes   = ' mpc-init mpc-parent--init mpc-waypoint';
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= ' mpc-style--' . esc_attr( $atts[ 'layout' ] );
			$classes   .= $atts[ 'layout' ] != 'style_4' && $atts[ 'layout' ] != 'style_7' ? ' mpc-vertical--center' : '';
			$classes   .= $atts[ 'layout' ] != 'style_3' && $atts[ 'value_sticky' ] != '' ? ' mpc-sticky-value' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div data-id="' . $css_id . '" class="mpc-progress' . $classes . '" ' . $animation . ' data-value="' . esc_attr( $atts[ 'value' ] ) . '" data-value-text="' . esc_attr( $atts[ 'value_number' ] ) . '" data-unit="' . esc_html( $atts[ 'value_unit' ] ) . '">';
				$return .= $this->shortcode_layout( $atts[ 'layout' ] );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_progress-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'value_font_size' ] = $styles[ 'value_font_size' ] != '' ? $styles[ 'value_font_size' ] . ( is_numeric( $styles[ 'value_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'progress_icon_size' ] = $styles[ 'progress_icon_size' ] != '' ? $styles[ 'progress_icon_size' ] . ( is_numeric( $styles[ 'progress_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'thickness' ] = $styles[ 'thickness' ] != '' ? $styles[ 'thickness' ] . ( is_numeric( $styles[ 'thickness' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_gap' ] = $styles[ 'icon_gap' ] != '' ? $styles[ 'icon_gap' ] . ( is_numeric( $styles[ 'icon_gap' ] ) ? 'px' : '' ) : '';

			// Add '%'
			$styles[ 'value' ] = $styles[ 'value' ] != '' ? $styles[ 'value' ] . ( is_numeric( $styles[ 'value' ] ) ? '%' : '' ) : '';

			// Wrap
			if ( $styles[ 'margin_css' ] ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"] {';
					$style .= $styles[ 'margin_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'thickness' ] && $styles[ 'layout' ] != 'style_2' && $styles[ 'layout' ] != 'style_5' && $styles[ 'layout' ] != 'style_8' ) { $inner_styles[] = 'height:' . $styles[ 'thickness' ] . ';'; }
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__wrap {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Title
			$inner_styles = array();
			if ( $styles[ 'title_padding_css' ] ) { $inner_styles[] = $styles[ 'title_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__title {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Value
			$inner_styles = array();
			if ( $styles[ 'value_padding_css' ] ) { $inner_styles[] = $styles[ 'value_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'value' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__value {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Bar
			$inner_styles = array();
			if ( $styles[ 'progress_border_css' ] ) { $inner_styles[] = $styles[ 'progress_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'progress' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__bar {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'value' ] && $styles[ 'layout' ] != 'style_5' && $styles[ 'layout' ] != 'style_8' ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"].mpc-anim--init .mpc-progress__bar {';
					if ( $styles[ 'layout' ] == 'style_3' || $styles[ 'layout' ] == 'style_4' || $styles[ 'layout' ] == 'style_7' ) {
						$style .= 'height:' . $styles[ 'value' ] . ';';
					} else {
						$style .= 'width:' . $styles[ 'value' ] . ';';
					}
				$style .= '}';
			}

			// Init size
			if ( $styles[ 'value_sticky' ] != '' && $styles[ 'value' ] && !in_array( $styles[ 'layout' ], array( 'style_3', 'style_4', 'style_7' ) ) ) {
				$style .= '.mpc-progress[data-id="' . $css_id . '"].mpc-anim--init .mpc-progress__info {';
					$style .= 'min-width:' . $styles[ 'value' ] . ';';
					$style .= 'max-width:' . $styles[ 'value' ] . ';';
				$style .= '}';
			}

			// Icons
			if ( $styles[ 'layout' ] == 'style_5' || $styles[ 'layout' ] == 'style_8' ) {
				if ( $styles[ 'icon_gap' ] != '' ) {
					$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__icon-box {';
						$style .= 'border-left-width:' . $styles[ 'icon_gap' ] . ';';
						$style .= 'border-right-width:' . $styles[ 'icon_gap' ] . ';';
					$style .= '}';

					$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__wrap {';
						$style .= 'margin-left:-' . $styles[ 'icon_gap' ] . ';';
						$style .= 'margin-right:-' . $styles[ 'icon_gap' ] . ';';
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'icon_border_css' ] ) { $inner_styles[] = $styles[ 'icon_border_css' ]; }
				if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }
				if ( $temp_style = MPC_CSS::background( $styles, 'icon' ) ) { $inner_styles[] = $temp_style; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__icon.mpc-layer--back {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'progress_icon_border_css' ] ) { $inner_styles[] = $styles[ 'progress_icon_border_css' ]; }
				if ( $temp_style = MPC_CSS::icon( $styles, 'progress' ) ) { $inner_styles[] = $temp_style; }
				if ( $temp_style = MPC_CSS::background( $styles, 'progress_icon' ) ) { $inner_styles[] = $temp_style; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-progress[data-id="' . $css_id . '"] .mpc-progress__icon.mpc-layer--front {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}
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
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => 'style_1',
					'columns'          => '5',
					'layouts'          => array(
						'style_1' => '2',
						'style_2' => '1',
						'style_3' => '5',
						'style_4' => '5',
						'style_5' => '2',
						'style_6' => '2',
						'style_7' => '5',
						'style_8' => '2',
					),
					'std'              => 'style_1',
					'shortcode'        => $this->shortcode,
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Value', 'mpc' ),
					'param_name'       => 'value',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose progress value.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 75,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Thickness', 'mpc' ),
					'param_name'       => 'thickness',
					'tooltip'          => __( 'Define progress bar thickness.', 'mpc' ),
					'value'            => '30',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-sort',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'dependency'       => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_2', 'style_5', 'style_8' ) ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
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
					'tooltip'          => __( 'Check to disable title.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Title', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);
			$title_font = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'dependency'  => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ), 'group' => __( 'Title', 'mpc' ) ) );
			$title_text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Text', 'mpc' ),
					'param_name'       => 'title',
					'admin_label'      => true,
					'tooltip'          => __( 'Define title text.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Title', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ),
				),
			);
			$title_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'title', 'dependency'  => array( 'element' => 'disable_title', 'value_not_equal_to' => 'true' ), 'group' => __( 'Title', 'mpc' ) ) );

			/* SECTION VALUE */
			$value = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Value', 'mpc' ),
					'param_name'       => 'disable_value',
					'tooltip'          => __( 'Check to disable value.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Value', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Sticky Value', 'mpc' ),
					'param_name'       => 'value_sticky',
					'tooltip'          => __( 'Check to enable sticky value. By default value is displayed at the right side. Enabling it will display value at the end of progress bar.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Value', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
					'dependency'       => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ),
				),
			);
			$value_font = MPC_Snippets::vc_font( array( 'prefix' => 'value', 'dependency'  => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ), 'group' => __( 'Value', 'mpc' ) ) );
			$value_text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Display Value Number', 'mpc' ),
					'param_name'       => 'value_number',
					'tooltip'          => __( 'Define displayed value number. When you want to display value different then <b>Value</b> field (0-100).', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Value', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-validate-int',
					'dependency'       => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Unit', 'mpc' ),
					'param_name'       => 'value_unit',
					'tooltip'          => __( 'Define value unit. You can specify a custom unit displayed after the <b>Value</b> number (e.g. <em>%</em>).', 'mpc' ),
					'value'            => '%',
					'group'            => __( 'Value', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ),
				),);
			$value_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'value', 'dependency'  => array( 'element' => 'disable_value', 'value_not_equal_to' => 'true' ), 'group' => __( 'Value', 'mpc' ) ) );

			/* SECTION ICON */
			$icon_section = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Icon Progress Bar - Settings', 'mpc' ),
					'subtitle'   => __( 'Icon settings for icon progress bar layout.', 'mpc' ),
					'param_name' => 'icon_section_divider',
					'group'      => __( 'Icon', 'mpc' ),
					'dependency' => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_5', 'style_8' ) ),
				),
			);
			$icon            = MPC_Snippets::vc_icon( array( 'dependency'  => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ), 'group' => __( 'Icon', 'mpc' ), 'custom_class' => 'mpc-offset-row' ) );
			$icon_gap = array(
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'icon_gap',
					'tooltip'          => __( 'Define gap between icons.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Icon', 'mpc' ),
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ),
				),
			);
			$icon_background = MPC_Snippets::vc_background( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'dependency'  => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ), 'group' => __( 'Icon', 'mpc' ) ) );
			$icon_border     = MPC_Snippets::vc_border( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'dependency'  => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ), 'group' => __( 'Icon', 'mpc' ) ) );

			/* SECTION PROGRESS */
			$progress_background = MPC_Snippets::vc_background( array( 'prefix' => 'progress', 'group' => __( 'Progress', 'mpc' ) ) );
			$progress_border     = MPC_Snippets::vc_border( array( 'prefix' => 'progress', 'group' => __( 'Progress', 'mpc' ) ) );

			$progress_icon            = MPC_Snippets::vc_icon( array( 'prefix' => 'progress', 'dependency'  => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ), 'group' => __( 'Progress', 'mpc' ) ) );
			$progress_icon_background = MPC_Snippets::vc_background( array( 'prefix' => 'progress_icon', 'subtitle' => __( 'Icon', 'mpc' ), 'dependency'  => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ), 'group' => __( 'Progress', 'mpc' ) ) );
			$progress_icon_border     = MPC_Snippets::vc_border( array( 'prefix' => 'progress_icon', 'subtitle' => __( 'Icon', 'mpc' ), 'dependency'  => array( 'element' => 'layout', 'value' => array( 'style_5', 'style_8' ) ), 'group' => __( 'Progress', 'mpc' ) ) );

			$class = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$background,
				$border,
				$padding,
				$margin,
				$title,
				$title_font,
				$title_text,
				$title_padding,
				$value,
				$value_font,
				$value_text,
				$value_padding,
				$icon_section,
				$icon,
				$icon_gap,
				$icon_background,
				$icon_border,
				$progress_background,
				$progress_border,
				$progress_icon,
				$progress_icon_background,
				$progress_icon_border,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Progress Bar', 'mpc' ),
				'description' => __( 'Animated progress bar', 'mpc' ),
				'base'        => 'mpc_progress',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-progress-bar.png',
				'icon'        => 'mpc-shicon-progress-bar',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Progress' ) ) {
	global $MPC_Progress;
	$MPC_Progress = new MPC_Progress;
}
