<?php
/*----------------------------------------------------------------------------*\
	IHOVER SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_iHover' ) ) {
	class MPC_iHover {
		public $shortcode = 'mpc_ihover';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_ihover', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_ihover-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_ihover/css/mpc_ihover.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_ihover-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_ihover/js/mpc_ihover' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-ihover-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/ihover.min.css' );

			global $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                         => '',
                'preset'                        => '',
				'content_preset'                => '',
                'item_width'                    => '200',
                'gap'                           => '10',
                'alignment'                     => 'center',

                'shape'                         => 'circle',
                'effect'                        => 'effect1',
                'style'                         => '',

                'spinner_top_color'             => '',
                'spinner_bottom_color'          => '',

                'divider_color'                 => '#f3f3f3',
                'divider_height'                => '1',
                'divider_width'                 => '75',
                'divider_alignment'             => 'center',
                'divider_margin_css'            => '',

                'image_border_css'              => '',

                'image_background_type'         => 'color',
                'image_background_color'        => '',
                'image_background_repeat'       => 'no-repeat',
                'image_background_size'         => 'initial',
                'image_background_position'     => 'middle-center',
                'image_background_gradient'     => '#83bae3||#80e0d4||0;100||180||linear',
                'image_background_image'        => '',
                'image_background_image_size'   => 'large',

                'content_border_css'            => '',

                'content_background_type'       => 'color',
                'content_background_color'      => '',
                'content_background_repeat'     => 'no-repeat',
                'content_background_size'       => 'initial',
                'content_background_position'   => 'middle-center',
                'content_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
                'content_background_image'      => '',
                'content_background_image_size' => 'large',

                'title_font_preset'             => '',
                'title_font_color'              => '',
                'title_font_size'               => '',
                'title_font_line_height'        => '',
                'title_font_align'              => '',
                'title_font_transform'          => '',

                'content_font_preset'           => '',
                'content_font_color'            => '',
                'content_font_size'             => '',
                'content_font_line_height'      => '',
                'content_font_align'            => '',
                'content_font_transform'        => '',
			), $atts );

			$MPC_Shortcode[ 'ihover' ] = array(
				'title_font_preset'   => esc_attr( $atts[ 'title_font_preset' ] ),
				'content_font_preset' => esc_attr( $atts[ 'content_font_preset' ] ),
				'shape'               => esc_attr( $atts[ 'shape' ] ),
				'effect'              => esc_attr( $atts[ 'effect' ] ),
				'style'               => esc_attr( $atts[ 'style' ] ),
			);

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$classes = ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div data-id="' . $css_id . '" class="mpc-ihover-wrapper mpc-init' . $classes . '">';
				$return .= do_shortcode( $content );
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
			$css_id = uniqid( 'mpc_ihover-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'content_font_size' ] = $styles[ 'content_font_size' ] != '' ? $styles[ 'content_font_size' ] . ( is_numeric( $styles[ 'content_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'item_width' ] = $styles[ 'item_width' ] != '' ? $styles[ 'item_width' ] . ( is_numeric( $styles[ 'item_width' ] ) ? 'px' : '' ) : '';
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'divider_height' ] = $styles[ 'divider_height' ] != '' ? $styles[ 'divider_height' ] . ( is_numeric( $styles[ 'divider_height' ] ) ? 'px' : '' ) : '';

			// Add '%'
			$styles[ 'divider_width' ] = $styles[ 'divider_width' ] != '' ? $styles[ 'divider_width' ] . ( is_numeric( $styles[ 'divider_width' ] ) ? '%' : '' ) : '';

			// Wrapper
			if ( $styles[ 'alignment' ] ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] {';
					$style .= 'text-align: ' . $styles[ 'alignment' ]. ';';
				$style .= '}';
			}

			// Image
			$inner_styles = array();
			if ( $styles[ 'image_border_css' ] ) { $inner_styles[] = $styles[ 'image_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'image' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .img {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Info
			if ( $styles[ 'effect' ] == 'effect20' && $styles[ 'shape' ] == 'circle' ) {
				$target = 'info-back';
			} else {
				$target = 'info';
			}

			$inner_styles = array();
			if ( $styles[ 'content_border_css' ] ) { $inner_styles[] = $styles[ 'content_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'content' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item.ih-item .' . $target . ' {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Title
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item.ih-item .mpc-ihover-title {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Content
			if ( $temp_style = MPC_CSS::font( $styles, 'content' ) ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item.ih-item .mpc-ihover-content {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Spinner
			$inner_styles = array();
			if ( $styles[ 'spinner_top_color' ] ) { $inner_styles[] = 'border-top-color: ' . $styles[ 'spinner_top_color' ] . ';border-left-color: ' . $styles[ 'spinner_top_color' ] . ';'; }
			if ( $styles[ 'spinner_bottom_color' ] ) { $inner_styles[] = 'border-bottom-color: ' . $styles[ 'spinner_bottom_color' ] . ';border-right-color: ' . $styles[ 'spinner_bottom_color' ] . ';'; }

			if ( $styles[ 'effect' ] == 'effect1' && $styles[ 'shape' ] == 'circle' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item.ih-item .spinner {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Masks
			if ( $styles[ 'effect' ] == 'effect4' && $styles[ 'shape' ] == 'square' && $temp_style = MPC_CSS::background( $styles, 'content' ) ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item.ih-item .mask1,';
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item.ih-item .mask2 {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Divider
			$inner_styles = array();
			if ( $styles[ 'divider_margin_css' ] ) { $inner_styles[] = $styles[ 'divider_margin_css' ]; }
			if ( $styles[ 'divider_alignment' ] ) { $inner_styles[] = 'text-align: ' . $styles[ 'divider_alignment' ] . ';'; }
			if ( $styles[ 'divider_height' ] ) { $inner_styles[] = 'height: ' . $styles[ 'divider_height' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-divider-wrap {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'divider_color' ] ) { $inner_styles[] = 'background: ' . $styles[ 'divider_color' ] . ';'; }
			if ( $styles[ 'divider_width' ] ) { $inner_styles[] = 'width: ' . $styles[ 'divider_width' ] . ';'; }
			if ( $styles[ 'divider_height' ] ) { $inner_styles[] = 'height: ' . $styles[ 'divider_height' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-divider {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Size
			if ( $styles[ 'item_width' ] != '' ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item {';
					$style .= 'width:' . $styles[ 'item_width' ] . ';';
					$style .= 'height:' . $styles[ 'item_width' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'gap' ] != '' ) {
				$style .= '.mpc-ihover-wrapper[data-id="' . $css_id . '"] .mpc-ihover-item {';
					$style .= 'margin:' . $styles[ 'gap' ] . ';';
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
					'type'             => 'mpc_text',
					'heading'          => __( 'Width', 'mpc' ),
					'param_name'       => 'item_width',
					'tooltip'          => __( 'Define item width.', 'mpc' ),
					'value'            => '200',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Define gap between items.', 'mpc' ),
					'value'            => '10',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-screenoptions',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Items Alignment', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Choose items alignment.', 'mpc' ),
					'value'            => 'center',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Items Shape', 'mpc' ),
					'param_name'       => 'shape',
					'admin_label'      => true,
					'tooltip'          => __( 'Select items shape. Shape defines available hover effects.', 'mpc' ),
					'value'            => array(
						__( 'Circle', 'mpc' ) => 'circle',
						__( 'Square', 'mpc' ) => 'square',
					),
					'std'              => 'circle',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-ihover-shape mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'effect',
					'admin_label'      => true,
					'tooltip'          => __( 'Select hover effect. You can preview all effects <a href="http://gudh.github.io/ihover/dist/" target="_blank">here</a>.', 'mpc' ),
					'value'            => array(
						__( 'Effect 1', 'mpc' )  => 'effect1',
						__( 'Effect 2', 'mpc' )  => 'effect2',
						__( 'Effect 3', 'mpc' )  => 'effect3',
						__( 'Effect 4', 'mpc' )  => 'effect4',
						__( 'Effect 5', 'mpc' )  => 'effect5',
						__( 'Effect 6', 'mpc' )  => 'effect6',
						__( 'Effect 7', 'mpc' )  => 'effect7',
						__( 'Effect 8', 'mpc' )  => 'effect8',
						__( 'Effect 9', 'mpc' )  => 'effect9',
						__( 'Effect 10', 'mpc' ) => 'effect10',
						__( 'Effect 11', 'mpc' ) => 'effect11',
						__( 'Effect 12', 'mpc' ) => 'effect12',
						__( 'Effect 13', 'mpc' ) => 'effect13',
						__( 'Effect 14', 'mpc' ) => 'effect14',
						__( 'Effect 15', 'mpc' ) => 'effect15',
						__( 'Effect 16', 'mpc' ) => 'effect16',
						__( 'Effect 17', 'mpc' ) => 'effect17',
						__( 'Effect 18', 'mpc' ) => 'effect18',
						__( 'Effect 19', 'mpc' ) => 'effect19',
						__( 'Effect 20', 'mpc' ) => 'effect20',
					),
					'std'              => 'effect1',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-ihover-effect mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect Style', 'mpc' ),
					'param_name'       => 'style',
					'admin_label'      => true,
					'tooltip'          => __( 'Select hover effect style.', 'mpc' ),
					'value'            => array(
						''                                 => 'none',
						__( 'Left to right', 'mpc' )       => 'left_to_right',
						__( 'Right to left', 'mpc' )       => 'right_to_left',
						__( 'Top to bottom', 'mpc' )       => 'top_to_bottom',
						__( 'Bottom to top', 'mpc' )       => 'bottom_to_top',
						__( 'Left and right', 'mpc' )      => 'left_and_right',
						__( 'From left and right', 'mpc' ) => 'from_left_and_right',
						__( 'From top and bottom', 'mpc' ) => 'from_top_and_bottom',
						__( 'Scale up', 'mpc' )            => 'scale_up',
						__( 'Scale down', 'mpc' )          => 'scale_down',
						__( 'Scale down and up', 'mpc' )   => 'scale_down_up',
					),
					'std'              => 'none',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-ihover-style mpc-advanced-field',
				),
			);

			$extras = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Extras', 'mpc' ),
					'tooltip'    => __( 'Extra settings for <b>Circle - Effect 1</b>.', 'mpc' ),
					'param_name' => 'extra_divider',
					'group'      => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Spinner - Top-Left', 'mpc' ),
					'param_name'       => 'spinner_top_color',
					'tooltip'          => __( 'Choose spinner top-left color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Spinner - Bottom-Right', 'mpc' ),
					'param_name'       => 'spinner_bottom_color',
					'tooltip'          => __( 'Choose spinner bottom-right color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Extras', 'mpc' ),
				),
			);

			$divider = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Divider', 'mpc' ),
					'param_name' => 'divider_divider',
					'group'      => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Divider', 'mpc' ),
					'param_name'       => 'divider_color',
					'tooltip'          => __( 'Choose divider color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'divider_alignment',
					'tooltip'          => __( 'Choose divider alignment.', 'mpc' ),
					'value'            => 'center',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Height', 'mpc' ),
					'param_name'       => 'divider_height',
					'tooltip'          => __( 'Define divider height.', 'mpc' ),
					'value'            => '1',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-sort',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'description'      => __( 'Specify divider height.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'group'            => __( 'Extras', 'mpc' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Width', 'mpc' ),
					'param_name'       => 'divider_width',
					'tooltip'          => __( 'Choose divider width. It will take the specified value of its container width.', 'mpc' ),
					'value'            => '100',
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Extras', 'mpc' ),
				),
			);

			$divider_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'divider', 'subtitle' => __( 'Divider', 'mpc' ), 'group' => __( 'Extras', 'mpc' ) ) );

			$image_background   = MPC_Snippets::vc_background( array( 'prefix' => 'image', 'group' => __( 'Thumbnail', 'mpc' ) ) );
			$image_border       = MPC_Snippets::vc_border( array( 'prefix' => 'image', 'group' => __( 'Thumbnail', 'mpc' ), 'with_radius' => false ) );
			$content_background = MPC_Snippets::vc_background( array( 'prefix' => 'content', 'group' => __( 'Content', 'mpc' ) ) );
			$content_border     = MPC_Snippets::vc_border( array( 'prefix' => 'content', 'group' => __( 'Content', 'mpc' ), 'with_radius' => false ) );
			$title_font         = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'title' => __( 'Title', 'mpc' ), 'group' => __( 'Typography', 'mpc' ) ) );
			$content_font       = MPC_Snippets::vc_font( array( 'prefix' => 'content', 'title' => __( 'Content', 'mpc' ), 'group' => __( 'Typography', 'mpc' ) ) );

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $image_background, $image_border, $content_background, $content_border, $title_font, $content_font, $extras, $divider, $divider_margin, $class );

			return array(
				'name'                    => __( 'iHover', 'mpc' ),
				'description'             => __( 'iHover with different animations', 'mpc' ),
				'base'                    => 'mpc_ihover',
				'class'                   => '',
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-ihover.png',
				'icon'                    => 'mpc-shicon-ihover',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_ihover_item' ),
				'content_element'         => true,
				'js_view'                 => 'VcColumnView',
				'show_settings_on_create' => true,
			);
		}
	}
}
if ( class_exists( 'MPC_iHover' ) ) {
	global $MPC_iHover;
	$MPC_iHover = new MPC_iHover;
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_ihover' ) ) {
	class WPBakeryShortCode_mpc_ihover extends WPBakeryShortCodesContainer {}
}
