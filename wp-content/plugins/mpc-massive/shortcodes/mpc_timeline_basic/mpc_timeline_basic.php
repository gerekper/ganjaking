<?php
/*----------------------------------------------------------------------------*\
	TIMELINE BASIC SHORTCODE
\*----------------------------------------------------------------------------*/

/*
 * ToDo: Backend View drag&drop
 */

if ( ! class_exists( 'MPC_Timeline_Basic' ) ) {
	class MPC_Timeline_Basic {
		public $shortcode = 'mpc_timeline_basic';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-isotope-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/isotope.min.js', array( 'jquery' ), '', true );

			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'               => '',
				'position'            => 'both',
				'gap_top'             => '',
				'gap_bottom'          => '',
				'force_fullwidth'     => '',
				'line_width'          => '5',
				'line_gap'            => '15',
				'mobile_line_gap'     => '',
				'line_color'          => '',
				'vertical_gap'        => '15',
				'pointer_size'        => '10',
				'pointer_gap'         => '10',
				'first_item_gap'      => '',
				'pointer_type'        => 'triangle',
				'pointer_alignment'   => 'top',
				'pointer_color'       => '',
				'line_vinette_top'    => '0',
				'line_vinette_bottom' => '0',

				'margin_css'           => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				// Ornament
				'icon_disable'    => '',
				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_size'       => '',
				'icon_color'      => '',

				'padding_css' => '',
				'border_css'  => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'background_overlay'    => '',
			), $atts );

			/* Prepare */
			$styles    = $this->shortcode_styles( $atts );
			$css_id    = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $atts );
			$icon = MPC_Parser::icon( $atts );

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'position' ] != '' ? ' mpc-layout--' . $atts[ 'position' ] : '';
			$classes .= $atts[ 'force_fullwidth' ] != '' && $atts[ 'position' ] != 'both' ? ' mpc--item-fullwidth' : '';
			$classes .= $atts[ 'pointer_type' ] != '' ? ' mpc-pointer--' . $atts[ 'pointer_type' ] : '';
			$classes .= $atts[ 'pointer_alignment' ] != '' ? ' mpc-pointer--' . $atts[ 'pointer_alignment' ] : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$classes_icon = $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';

			/* Shortcode Output */
			$ornament = $atts[ 'icon_disable' ] == '' ? '<i class="mpc-transition ' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i>' : '';
			$return = '<div id="' . $css_id . '" class="mpc-timeline-basic' . $classes . '" ' . $animation . '>';
				$return .= '<div class="mpc-timeline__track"></div>';
				$return .= '<div class="mpc-track__icon' . $classes_icon . '">' . $ornament . '</div>';
				$return .= do_shortcode( $content );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Timeline Basic', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( $this->shortcode . '-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' && $styles[ 'icon_size' ] != '0' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'vertical_gap' ] = $styles[ 'vertical_gap' ] != '' && $styles[ 'vertical_gap' ] != '0' ? $styles[ 'vertical_gap' ] . ( is_numeric( $styles[ 'vertical_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'first_item_gap' ] = $styles[ 'first_item_gap' ] != '' && $styles[ 'first_item_gap' ] != '0' ? $styles[ 'first_item_gap' ] . ( is_numeric( $styles[ 'first_item_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'pointer_gap' ] = $styles[ 'pointer_gap' ] != '' && $styles[ 'pointer_gap' ] != '0' ? $styles[ 'pointer_gap' ] . ( is_numeric( $styles[ 'pointer_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'pointer_size' ] = $styles[ 'pointer_size' ] != '' && $styles[ 'pointer_size' ] != '0' ? $styles[ 'pointer_size' ] . ( is_numeric( $styles[ 'pointer_size' ] ) ? 'px' : '' ) : '';

			// Gap
			if( $styles[ 'vertical_gap' ] != '' ) {
				$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline-item__wrap {';
					$style .= 'margin-bottom: ' . $styles[ 'vertical_gap' ] . ';';
				$style .= '}';
			}

			if( $styles[ 'first_item_gap' ] != '' && $styles[ 'position' ] == 'both' ) {
				$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline-item__wrap:nth-child(4) {';
					$style .= 'margin-top: ' . $styles[ 'first_item_gap' ] . ';';
				$style .= '}';
			}

			// Line width & Gap & Color
			if( $styles[ 'line_width' ] && $styles[ 'line_gap' ] ) {
				$gap_size = ( $styles[ 'line_width' ] * 0.5 ) + $styles[ 'line_gap' ];

				$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline-item__wrap {';
					$style .= 'padding-left: ' . $gap_size . 'px;';
					$style .= 'padding-right: ' . $gap_size . 'px;';
				$style .= '}';

				$inner_styles = array();
				$triangle_size = $styles[ 'pointer_size' ] != '' ? $styles[ 'pointer_size' ] : $gap_size . 'px';
				if( $styles[ 'pointer_type' ] == 'right-triangle' ) {
					$triangle_size = ( (int) $triangle_size * .75 ) . 'px ' . $triangle_size;
				}

				if( $styles[ 'pointer_gap' ] != '' ) {
					$inner_styles[] = $styles[ 'pointer_alignment' ] == 'bottom' ? 'margin-bottom: ' . $styles[ 'pointer_gap' ] . ';' : 'margin-top: ' . $styles[ 'pointer_gap' ] . ';';
				}
				if( $triangle_size != '' ) { $inner_styles[] = 'border-width: ' . $triangle_size . ';'; }
				if( $styles[ 'pointer_type' ] == 'line' ) { $inner_styles[] = 'width: ' . $gap_size . 'px;'; }
				if( $styles[ 'pointer_color' ] != '' ) { $inner_styles[] = 'border-color: ' . $styles[ 'pointer_color' ] . ';'; }

				if( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-tl-before {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				if( $styles[ 'mobile_line_gap' ] != '' ) {
					$mobile_gap_size = ( $styles[ 'line_width' ] * 0.5 ) + $styles[ 'mobile_line_gap' ];

					$style .= '@media screen and (max-width: 768px) {';
						$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline-item__wrap {';
							$style .= 'padding-left: ' . $mobile_gap_size . 'px;';
							$style .= 'padding-right: ' . $mobile_gap_size . 'px;';
						$style .= '}';
						$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-tl-before {';
							$style .= 'width: ' . $mobile_gap_size . 'px;';
						$style .= '}';
					$style .= '}';
				}
			}

			// Vinette's
			$parent_style = '';
			$inner_styles = array();
			if ( $styles[ 'line_width' ] != '' ) {
				$inner_styles[] = 'width: ' . $styles[ 'line_width' ] . 'px;';
				$inner_styles[] = 'margin-left: ' . ( - round( $styles[ 'line_width' ] * .5, 1 ) ) . 'px;';
			}
			if ( $styles[ 'line_color' ] != '' ) { $inner_styles[] = 'background: ' . $styles[ 'line_color' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline__track {';
					$style .= join( '', $inner_styles );
				$style .= '}';

				$styles[ 'line_vinette_top' ]    = $styles[ 'line_vinette_top' ] != '' && $styles[ 'line_vinette_top' ] != '0' ? $styles[ 'line_vinette_top' ] . ( is_numeric( $styles[ 'line_vinette_top' ] ) ? 'px' : '' ) : '';
				$styles[ 'line_vinette_bottom' ] = $styles[ 'line_vinette_bottom' ] != '' && $styles[ 'line_vinette_bottom' ] != '0' ? $styles[ 'line_vinette_bottom' ] . ( is_numeric( $styles[ 'line_vinette_bottom' ] ) ? 'px' : '' ) : '';

				if( $styles[ 'line_vinette_top' ] != '0px' ) {
					$parent_style .= 'margin-top: ' . $styles[ 'line_vinette_top' ] . ';';
					$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline__track:before {';
						$style .= 'top: -' . $styles[ 'line_vinette_top' ] . ';';
						$style .= 'height: ' . $styles[ 'line_vinette_top' ] . ';';
						$style .= 'background-image: linear-gradient( rgba( 0,0,0,0 ) 10%, ' . $styles[ 'line_color' ] . ');';
					$style .= '}';
				}
				if( $styles[ 'line_vinette_bottom' ] != '' ) {
					$parent_style .= 'margin-bottom: ' . $styles[ 'line_vinette_top' ] . ';';
					$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-timeline__track:after {';
						$style .= 'bottom: -' . $styles[ 'line_vinette_bottom' ] . ';';
						$style .= 'height: ' . $styles[ 'line_vinette_bottom' ] . ';';
						$style .= 'background-image: linear-gradient( ' . $styles[ 'line_color' ] . ', rgba( 0,0,0,0 ) 90% );';
					$style .= '}';
				}
			}

			// Ornament Icon
			$inner_styles = array();
			if( $temp = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp; }
			if( $temp = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp; }
			if( $styles[ 'border_css' ] != '' ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if( $styles[ 'padding_css' ] != '' ) { $inner_styles[] = $styles[ 'padding_css' ]; }

			if( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-timeline-basic[id="' . $css_id . '"] .mpc-track__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Paddings
			$inner_styles = array();
			if( $parent_style != '' ) { $inner_styles[] = $parent_style; }
			if( $styles[ 'margin_css' ] != '' ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if( is_numeric( $styles[ 'gap_top' ] ) && $styles[ 'gap_top' ] > 0 ) { $inner_styles[] = 'padding-top:' . $styles[ 'gap_top' ] . 'px;'; }
			if( is_numeric( $styles[ 'gap_bottom' ] ) && $styles[ 'gap_bottom' ] > 0 ) { $inner_styles[] = 'padding-bottom:' . $styles[ 'gap_bottom' ] . 'px;'; }

			if( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-timeline-basic[id="' . $css_id . '"] {';
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
					'type'             => 'mpc_text',
					'heading'          => __( 'Top Padding', 'mpc' ),
					'param_name'       => 'gap_top',
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-up-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Bottom Padding', 'mpc' ),
					'param_name'       => 'gap_bottom',
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-down-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Items Position', 'mpc' ),
					'param_name'       => 'position',
					'tooltip'          => __( 'Select items position on timeline.', 'mpc' ),
					'value'            => array(
						__( 'Left & Right', 'mpc' ) => 'both',
						__( 'Only Right', 'mpc' )   => 'left',
						__( 'Only Left', 'mpc' )    => 'right',
					),
					'std'              => 'both',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Force Fullwidth', 'mpc' ),
					'param_name'       => 'force_fullwidth',
					'tooltip'          => __( 'Check to force timeline item width to 100%.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'dependency'       => array( 'element' => 'position', 'value_not_equal_to' => 'both' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Vertical Items Gap', 'mpc' ),
					'param_name'       => 'vertical_gap',
					'tooltip'          => __( 'Choose gap between grid items.', 'mpc' ),
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-up-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'First Right Item Gap', 'mpc' ),
					'tooltip'          => __( 'Adjust the top gap of first right item.', 'mpc' ),
					'param_name'       => 'first_item_gap',
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-up-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'dependency'       => array( 'element' => 'position', 'value' => 'both' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Track Line & Spacing', 'mpc' ),
//					'subtitle'         => __( 'Track Line.', 'mpc' ),
					'param_name'       => 'track_line_divider',
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Line Width', 'mpc' ),
					'param_name'       => 'line_width',
					'tooltip'          => __( 'Select width of track line.', 'mpc' ),
					'min'              => 1,
					'max'              => 50,
					'step'             => 1,
					'value'            => 5,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Line & Item Spacing', 'mpc' ),
					'param_name'       => 'line_gap',
					'tooltip'          => __( 'Choose gap between track line and items.', 'mpc' ),
					'value'            => 15,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Mobile Line & Item Spacing', 'mpc' ),
					'param_name'       => 'mobile_line_gap',
					'tooltip'          => __( 'Choose gap between track line and items for mobile devices.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Line Color', 'mpc' ),
					'param_name'       => 'line_color',
					'tooltip'          => __( 'Select color for track line.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Vinette Top', 'mpc' ),
					'param_name'       => 'line_vinette_top',
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-up-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Vinette Bottom', 'mpc' ),
					'param_name'       => 'line_vinette_bottom',
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-arrow-down-alt',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Pointer', 'mpc' ),
					'param_name'       => 'pointer_divider',
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Type', 'mpc' ),
					'param_name'       => 'pointer_type',
					'tooltip'          => __( 'Select the type of pointer.', 'mpc' ),
					'value'            => array(
						__( 'Triangle', 'mpc' ) => 'triangle',
						__( 'Kicked Triangle', 'mpc' ) => 'right-triangle',
						__( 'Line', 'mpc' ) => 'line',
					),
					'std'              => 'triangle',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'pointer_alignment',
					'tooltip'          => __( 'Select the alignment of pointer.', 'mpc' ),
					'value'            => array(
						__( 'Top', 'mpc' )    => 'top',
						__( 'Middle', 'mpc' ) => 'middle',
						__( 'Bottom', 'mpc' ) => 'bottom',
					),
					'std'              => 'top',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'pointer_size',
					'tooltip'          => __( 'Select size of pointer.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 10,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Edge Gap', 'mpc' ),
					'param_name'       => 'pointer_gap',
					'tooltip'          => __( 'Specify the gap between pointer and item edges.', 'mpc' ),
					'min'              => 0,
					'max'              => 25,
					'step'             => 1,
					'value'            => 10,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'pointer_color',
					'tooltip'          => __( 'Specify the default pointers color which can be overwritten for each item.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$group_atts = array( 'group' => __( 'Ornament', 'mpc' ) );
			$icon       = MPC_Snippets::vc_icon( $group_atts );
			$border     = MPC_Snippets::vc_border( $group_atts );
			$padding    = MPC_Snippets::vc_padding( $group_atts );
			$background = MPC_Snippets::vc_background( $group_atts );

			$margin    = MPC_Snippets::vc_margin();
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,

				$icon,
				$background,
				$border,
				$padding,

				$margin,
				$animation,
				$class
			);

			return array(
				'name'                    => __( 'Timeline Basic', 'mpc' ),
				'description'             => __( 'Simple timeline shortcode', 'mpc' ),
				'base'                    => $this->shortcode,
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_timeline_item' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
				'icon'                    => 'mpc-shicon-timeline-basic',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'js_view'                 => 'VcColumnView',
			);
		}
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_timeline_basic' ) ) {
	class WPBakeryShortCode_mpc_timeline_basic extends WPBakeryShortCodesContainer {
	}
}

if ( class_exists( 'MPC_Timeline_Basic' ) ) {
	global $MPC_Timeline_Basic;
	$MPC_Timeline_Basic = new MPC_Timeline_Basic;
}
