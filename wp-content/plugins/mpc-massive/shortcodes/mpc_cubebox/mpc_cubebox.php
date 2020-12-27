<?php
/*----------------------------------------------------------------------------*\
	CUBEBOX SHORTCODE
\*----------------------------------------------------------------------------*/
global $mpc_cubebox_side;

if ( ! class_exists( 'MPC_Cubebox_Side' ) ) {
	class MPC_Cubebox_Side {
		public $shortcode = 'mpc_cubebox_side';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_cubebox_side', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		function shortcode_template( $atts, $description = null ) {
			global $MPC_Shortcode;

			$cubebox_side = $MPC_Shortcode[ 'cubebox' ][ 'side' ];

			$return = '<div class="mpc-cubebox__' . $cubebox_side . '">';
				$return .= '<div class="mpc-cubebox-side"><div class="mpc-cubebox-side__content">';
					$return .= do_shortcode( $description );
			    $return .= '</div></div>';
			$return .= '</div>';

			if ( $cubebox_side == 'front' ) {
				$MPC_Shortcode[ 'cubebox' ][ 'side' ] = 'side mpc-container';
			} else {
				$MPC_Shortcode[ 'cubebox' ][ 'side' ] = 'front';
			}

			return $return;
		}

		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$params_side        = array();
			$allowed_shortcodes = array( 'only' => 'mpc_alert,mpc_dropcap,mpc_icon,mpc_icon_column,mpc_button,mpc_divider,mpc_icon,mpc_progress,mpc_counter,mpc_chart,vc_column_text,vc_custom_heading,mpc_image' );

			return array(
				'name'                      => __( 'Cubebox Side', 'mpc' ),
				'base'                      => 'mpc_cubebox_side',
//				'icon'                      => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-cubebox.png',
				'icon'                      => 'mpc-shicon-cubebox',
				'allowed_container_element' => 'vc_row',
				'is_container'              => true,
				'as_parent'                 => $allowed_shortcodes,
				'content_element'           => false,
				'params'                    => $params_side,
				'js_view'                   => 'VcTabView',
			);
		}
	}
}
if ( class_exists( 'MPC_Cubebox_Side' ) ) {
	$MPC_Cubebox_Side = new MPC_Cubebox_Side;
}

if ( ! class_exists( 'MPC_Cubebox' ) ) {
	class MPC_Cubebox {
		public $shortcode = 'mpc_cubebox';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_cubebox', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'content'       => '',
			);

			$this->parts = $parts;
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_cubebox-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_cubebox/css/mpc_cubebox.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_cubebox-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_cubebox/js/mpc_cubebox' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $mpc_ma_options, $MPC_Shortcode, $MPC_Ribbon;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                             => '',
				'preset'                            => '',
				'content_preset'                    => '',
				'url'                               => '',
				'max_width'                         => '',
				'max_height'                        => '',
				'primary_side'                      => 'none',
				'click'                             => 'no',

				'front_alignment'                   => 'middle-center',
				'front_background_type'             => 'color',
				'front_background_color'            => '',
				'front_background_image'            => '',
				'front_background_image_size'       => 'large',
				'front_background_repeat'           => 'no-repeat',
				'front_background_size'             => 'initial',
				'front_background_position'         => 'middle-center',
				'front_background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'front_padding_css'                 => '',
				'front_border_css'                  => '',

				'side_alignment'                    => 'middle-center',
				'side_background_type'              => 'color',
				'side_background_color'             => '',
				'side_background_image'             => '',
				'side_background_image_size'        => 'large',
				'side_background_repeat'            => 'no-repeat',
				'side_background_size'              => 'initial',
				'side_background_position'          => 'middle-center',
				'side_background_gradient'          => '#83bae3||#80e0d4||0;100||180||linear',

				'side_padding_css'                  => '',
				'side_border_css'                   => '',

				'margin_css'                        => '',

				'transition_direction'              => 'flip-top',
				'transition_style'                  => 'flip-top',
				'transition_duration'               => '500',

				'animation_in_type'                 => 'none',
				'animation_in_duration'             => '300',
				'animation_in_delay'                => '0',
				'animation_in_offset'               => '100',

				/* Ribbon */
				'mpc_ribbon__disable'               => '',
				'mpc_ribbon__preset'                => '',
				'mpc_ribbon__text'                  => '',
				'mpc_ribbon__style'                 => 'classic',
				'mpc_ribbon__disable_corners'       => '',
				'mpc_ribbon__alignment'             => 'top-left',
				'mpc_ribbon__corners_color'         => '',
				'mpc_ribbon__size'                  => 'medium',

				'mpc_ribbon__font_preset'           => '',
				'mpc_ribbon__font_color'            => '',
				'mpc_ribbon__font_size'             => '',
				'mpc_ribbon__font_line_height'      => '',
				'mpc_ribbon__font_align'            => '',
				'mpc_ribbon__font_transform'        => '',

				'mpc_ribbon__icon_type'             => 'icon',
				'mpc_ribbon__icon'                  => '',
				'mpc_ribbon__icon_character'        => '',
				'mpc_ribbon__icon_image'            => '',
				'mpc_ribbon__icon_image_size'       => 'thumbnail',
				'mpc_ribbon__icon_preset'           => '',
				'mpc_ribbon__icon_size'             => '',
				'mpc_ribbon__icon_color'            => '#333333',

				'mpc_ribbon__margin_css'            => '',
				'mpc_ribbon__padding_css'           => '',
				'mpc_ribbon__border_css'            => '',

				'mpc_ribbon__background_type'       => 'color',
				'mpc_ribbon__background_color'      => '',
				'mpc_ribbon__background_image'      => '',
				'mpc_ribbon__background_image_size' => 'large',
				'mpc_ribbon__background_repeat'     => 'no-repeat',
				'mpc_ribbon__background_size'       => 'initial',
				'mpc_ribbon__background_position'   => 'middle-center',
				'mpc_ribbon__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			/* Prepare */
			$MPC_Shortcode[ 'cubebox' ][ 'side' ] = 'front';
			$url_settings = MPC_Parser::url( $atts[ 'url' ] );
			$wrapper      = $url_settings != '' ? 'a' : 'div';

			$atts_ribbon  = MPC_Parser::shortcode( $atts, 'mpc_ribbon_' );
			$ribbon       = $atts[ 'mpc_ribbon__disable' ] == '' ? $MPC_Ribbon->shortcode_template( $atts_ribbon ) : '';

			$animation    = MPC_Parser::animation( $atts );

			$css_id = $this->shortcode_styles( $atts );

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'transition_direction' ] != '' ? ' mpc-cubebox--' . esc_attr( $atts[ 'transition_direction' ] ) : '';
			$classes .= $atts[ 'click' ] === 'yes' ? ' mpc-cubebox--click' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$align = $atts[ 'front_alignment' ] != '' ? ' data-front-align="' . esc_attr( $atts[ 'front_alignment' ] ) . '"' : '';
			$align .= $atts[ 'side_alignment' ] != '' ? ' data-side-align="' . esc_attr( $atts[ 'side_alignment' ] ) . '"' : '';

			/* Layout parts */
			$this->parts[ 'section_begin' ] = '<div class="mpc-cubebox__content"' . $align . '>';
			$this->parts[ 'section_end' ]   = '</div>';

			$this->parts[ 'content' ] = do_shortcode( $description );

			/* Max height */
			$height = $atts[ 'max_height' ] ? ' data-max-height="' . esc_attr( $atts[ 'max_height' ] ) . '"' : '';

			/* Primary Side */
			$primary_side = $atts[ 'primary_side' ] ? ' data-primary-side="' . esc_attr( $atts[ 'primary_side' ] ) . '"' : '';

			/* Shortcode Output */
			$return = $ribbon != '' ? '<div class="mpc-ribbon-wrap">' : '';
				$return .= '<' . $wrapper . $url_settings . ' data-id="' . $css_id . '" class="mpc-cubebox' . $classes . '" ' . $animation . $height . $primary_side . '>';
					$return .= $this->parts[ 'section_begin' ] . $this->parts[ 'content' ] . $this->parts[ 'section_end' ];
				$return .= '</' . $wrapper . '>';
			$return .= $ribbon;
			$return .= $ribbon != '' ? '</div>' : '';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Cubebox', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_cubebox-' . rand( 1, 100 ) );
			$style = '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] =  $styles[ 'margin_css' ]; }
			if ( $styles[ 'max_width' ] ) { $inner_styles[] = 'max-width:' .  $styles[ 'max_width' ] . 'px'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-cubebox[data-id="' . $css_id . '"] {';
				$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Front
			$inner_styles = array();
			if ( $styles[ 'front_border_css' ] ) { $inner_styles[] = $styles[ 'front_border_css' ]; }
			if ( $styles[ 'front_padding_css' ] ) { $inner_styles[] = $styles[ 'front_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'front' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-cubebox[data-id="' . $css_id . '"] .mpc-cubebox__front .mpc-cubebox-side {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Side
			$inner_styles = array();
			if ( $styles[ 'side_border_css' ] ) { $inner_styles[] = $styles[ 'side_border_css' ]; }
			if ( $styles[ 'side_padding_css' ] ) { $inner_styles[] = $styles[ 'side_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'side' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-cubebox[data-id="' . $css_id . '"] .mpc-cubebox__side .mpc-cubebox-side {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Duration
			if ( $styles[ 'transition_duration' ] ) {
				$style .= '.mpc-cubebox[data-id="' . $css_id . '"] .mpc-cubebox__content {';
					$style .= '-webkit-transition-duration: ' . (int) $styles[ 'transition_duration' ] . 'ms;';
					$style .= '-moz-transition-duration: ' . (int) $styles[ 'transition_duration' ] . 'ms;';
					$style .= '-ms-transition-duration: ' . (int) $styles[ 'transition_duration' ] . 'ms;';
                    $style .= 'transition-duration: ' . (int) $styles[ 'transition_duration' ] . 'ms;';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return $css_id;
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Style Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'        => 'mpc_content',
					'heading'     => __( 'Content Preset', 'mpc' ),
					'param_name'  => 'content_preset',
					'tooltip'     => MPC_Helper::content_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Flip Direction', 'mpc' ),
					'param_name'       => 'transition_direction',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose flip animation direction. It will rotate the cube in specified direction and display its other side.', 'mpc' ),
					'value'            => array(
						__( 'Top' )    => 'flip-top',
						__( 'Bottom' ) => 'flip-bottom',
						__( 'Left' )   => 'flip-left',
						__( 'Right' )  => 'flip-right',
					),
					'std'              => 'flip-top',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Max Width', 'mpc' ),
					'param_name'       => 'max_width',
					'tooltip'          => __( 'Define max width for Cubebox or leave empty for fullwidth.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Max Height', 'mpc' ),
					'param_name'       => 'max_height',
					'tooltip'          => __( 'Define max height for Cubebox or leave empty for highest side value apply.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Primary Side', 'mpc' ),
					'param_name'       => 'primary_side',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose a box primary side. An other side will adopt height to it. This works only with "Max Height" option empty.', 'mpc' ),
					'value'            => array(
						__( 'None' )  => 'none',
						__( 'Front' ) => 'front',
						__( 'Side' )  => 'side',
					),
					'std'              => 'none',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'URL', 'mpc' ),
					'param_name'       => 'url',
					'tooltip'          => __( 'Choose target link for the whole cubebox.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-5 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Flip On Click', 'mpc' ),
					'param_name'       => 'click',
					'tooltip'          => __( 'Check to flip element on click.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'yes' ),
					'std'              => 'no',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Flip Duration', 'mpc' ),
					'param_name'       => 'transition_duration',
					'tooltip'          => __( 'Choose flip animation duration.', 'mpc' ),
					'value'            => 500,
					'min'              => 100,
					'max'              => 5000,
					'step'             => 50,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$front_atts = array( 'prefix' => 'front', 'group' => __( 'Front', 'mpc' ) );

			$front = array(
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Content Position', 'mpc' ),
					'param_name'       => 'front_alignment',
					'tooltip'          => __( 'Choose front content position.', 'mpc' ),
					'value'            => '',
					'std'              => 'middle-center',
					'grid_size'        => 'large',
					'group'            => __( 'Front', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);
			$front_background = MPC_Snippets::vc_background( $front_atts );
			$front_border     = MPC_Snippets::vc_border( $front_atts );
			$front_padding    = MPC_Snippets::vc_padding( $front_atts );

			$side_atts = array( 'prefix' => 'side', 'group' => __( 'Side', 'mpc' ) );

			$side = array(
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Content Position', 'mpc' ),
					'param_name'       => 'side_alignment',
					'tooltip'          => __( 'Choose side content position.', 'mpc' ),
					'value'            => '',
					'std'              => 'middle-center',
					'grid_size'        => 'large',
					'group'            => __( 'Side', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);
			$side_background = MPC_Snippets::vc_background( $side_atts );
			$side_border     = MPC_Snippets::vc_border( $side_atts );
			$side_padding    = MPC_Snippets::vc_padding( $side_atts );

			$margin = MPC_Snippets::vc_margin();

			$integrate_ribbon = vc_map_integrate_shortcode( 'mpc_ribbon', 'mpc_ribbon__', __( 'Ribbon', 'mpc' ) );
			$disable_ribbon   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Ribbon', 'mpc' ),
					'param_name'       => 'mpc_ribbon__disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to disable ribbon display.', 'mpc' ),
					'group'            => __( 'Ribbon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_ribbon = array_merge( $disable_ribbon, $integrate_ribbon );

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$margin,

				$front,
				$front_background,
				$front_border,
				$front_padding,

				$side,
				$side_background,
				$side_border,
				$side_padding,

				$integrate_ribbon,

				$animation,
				$class
			);

			$default_content = '[mpc_cubebox_side title="' . __( 'Front', 'mpc' ) . '"][/mpc_cubebox_side]';
			$default_content .= '[mpc_cubebox_side title="' . __( 'Side', 'mpc' ) . '"][/mpc_cubebox_side]';

			$custom_markup = PHP_EOL . '<div class="wpb_tabs_holder wpb_holder vc_container_for_children">';
			$custom_markup .= PHP_EOL . '<ul class="tabs_controls">' . PHP_EOL .  '</ul>';
			$custom_markup .= PHP_EOL . '%content%';
			$custom_markup .= PHP_EOL . '</div>';

			return array(
				'name'                    => __( 'Cubebox', 'mpc' ),
				'description'             => __( 'Rotating cube with content', 'mpc' ),
				'base'                    => 'mpc_cubebox',
				'show_settings_on_create' => false,
				'is_container'            => true,
				'container_not_allowed'   => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-cubebox.png',
				'icon'                    => 'mpc-shicon-cubebox',
				'category'                => __( 'Massive', 'mpc' ),
				'wrapper_class'           => 'vc_clearfix',
				'params'                  => $params,
				'default_content'         => $default_content,
				'custom_markup'           => $custom_markup,
				'js_view'                 => 'mpcTabsView',
			);
		}
	}
}
if ( class_exists( 'MPC_Cubebox' ) ) {
	global $MPC_Cubebox;
	$MPC_Cubebox = new MPC_Cubebox;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_cubebox' ) ) {
	class WPBakeryShortCode_mpc_cubebox extends MPCShortCode_Base {
		static    $filter_added          = false;
		protected $controls_css_settings = 'out-tc vc_controls-content-widget';
		protected $controls_list         = array( 'edit', 'clone', 'delete' );

		public function __construct( $settings ) {
			parent::__construct( $settings );
		}

		public function contentAdmin( $atts, $content = null ) {
			$width                = $custom_markup = '';
			$shortcode_attributes = array( 'width' => '1/1' );
			foreach ( $this->settings[ 'params' ] as $param ) {
				if ( $param[ 'param_name' ] != 'content' ) {
					//$shortcode_attributes[$param['param_name']] = $param['value'];
					if ( isset( $param[ 'value' ] ) && is_string( $param[ 'value' ] ) ) {
						$shortcode_attributes[ $param[ 'param_name' ] ] = __( $param[ 'value' ], "mpc" );
					} elseif ( isset( $param[ 'value' ] ) ) {
						$shortcode_attributes[ $param[ 'param_name' ] ] = $param[ 'value' ];
					}
				} else if ( $param[ 'param_name' ] == 'content' && $content == null ) {
					//$content = $param['value'];
					$content = __( $param[ 'value' ], "mpc" );
				}
			}
			extract( shortcode_atts( $shortcode_attributes, $atts ) );

			preg_match_all( '/mpc_cubebox_side title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );

			$output     = '';
			$tab_titles = array();

			if ( isset( $matches[ 0 ] ) ) {
				$tab_titles = $matches[ 0 ];
			}
			$tmp = '';
			if ( count( $tab_titles ) ) {
				$tmp .= '<ul class="clearfix tabs_controls">';
				foreach ( $tab_titles as $tab ) {
					preg_match( '/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[ 0 ], $tab_matches, PREG_OFFSET_CAPTURE );
					if ( isset( $tab_matches[ 1 ][ 0 ] ) ) {
						$tmp .= '<li><a href="#tab-' . ( isset( $tab_matches[ 3 ][ 0 ] ) ? $tab_matches[ 3 ][ 0 ] : sanitize_title( $tab_matches[ 1 ][ 0 ] ) ) . '">' . $tab_matches[ 1 ][ 0 ] . '</a></li>';

					}
				}
				$tmp .= '</ul>' . "\n";
			} else {
				$output .= do_shortcode( $content );
			}

			$elem = $this->getElementHolder( $width );

			$iner = '';
			foreach ( $this->settings[ 'params' ] as $param ) {
				$custom_markup = '';
				$param_name = $param[ 'param_name' ];
				$param_value   = isset( $$param_name ) ? $$param_name : '';
				if ( is_array( $param_value ) ) {
					// Get first element from the array
					reset( $param_value );
					$first_key   = key( $param_value );
					$param_value = $param_value[ $first_key ];
				}
				$iner .= $this->singleParamHtmlHolder( $param, $param_value );
			}

			if ( isset( $this->settings[ "custom_markup" ] ) && $this->settings[ "custom_markup" ] != '' ) {
				if ( $content != '' ) {
					$custom_markup = str_ireplace( "%content%", $tmp . $content, $this->settings[ "custom_markup" ] );
				} else if ( $content == '' && isset( $this->settings[ "default_content_in_template" ] ) && $this->settings[ "default_content_in_template" ] != '' ) {
					$custom_markup = str_ireplace( "%content%", $this->settings[ "default_content_in_template" ], $this->settings[ "custom_markup" ] );
				} else {
					$custom_markup = str_ireplace( "%content%", '', $this->settings[ "custom_markup" ] );
				}
				//$output .= do_shortcode($this->settings["custom_markup"]);
				$iner .= do_shortcode( $custom_markup );
			}
			$elem   = str_ireplace( '%wpb_element_content%', $iner, $elem );
			$output = $elem;

			return $output;
		}
	}
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-column.php' );
if ( class_exists( 'WPBakeryShortCode_VC_Column' ) && ! class_exists( 'WPBakeryShortCode_mpc_cubebox_side' ) ) {
	class WPBakeryShortCode_mpc_cubebox_side extends WPBakeryShortCode_VC_Column {
		protected $controls_css_settings = 'tc vc_control-container';
		protected $controls_list = array( 'add' );
		protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl.php';

		public function __construct( $settings ) {
			parent::__construct( $settings );
		}

		public function mainHtmlBlockParams( $width, $i ) {
			return 'data-element_type="' . $this->settings["base"] . '" class="wpb_' . $this->settings['base'] . ' wpb_sortable wpb_content_holder"' . $this->customAdminBlockParams();
		}

		public function containerHtmlBlockParams( $width, $i ) {
			return 'class="wpb_column_container vc_container_for_children"';
		}

		public function getColumnControls( $controls, $extended_css = '' ) {
			return $this->getColumnControlsModular( $extended_css );
		}
	}
}
