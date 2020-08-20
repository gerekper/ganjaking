<?php
/*----------------------------------------------------------------------------*\
	ACCORDION SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Accordion_Tab' ) ) {
	class MPC_Accordion_Tab {
		public $shortcode = 'mpc_accordion_tab';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_accordion_tab', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		function shortcode_template( $atts, $description = null ) {
			global $MPC_Shortcode;

			$atts = shortcode_atts( array(
					'class' => '',
					'title' => '',
			), $atts );

			/* Prepare */
			$MPC_Shortcode[ 'accordion' ][ 'current' ] = isset( $MPC_Shortcode[ 'accordion' ][ 'current' ]  ) ? $MPC_Shortcode[ 'accordion' ][ 'current' ]  : 1;
			$MPC_Shortcode[ 'accordion' ][ 'opened' ]  = isset( $MPC_Shortcode[ 'accordion' ][ 'opened' ] ) ? $MPC_Shortcode[ 'accordion' ][ 'opened' ] : 1;

			$classes = ' ' . $atts[ 'class' ];

			$active = $MPC_Shortcode[ 'accordion' ][ 'opened' ] == $MPC_Shortcode[ 'accordion' ][ 'current' ] ? ' data-active="true"' : '';

			$title_classes  =  ' mpc-transition';
			$title_classes  .= $MPC_Shortcode[ 'accordion' ][ 'opened' ] == $MPC_Shortcode[ 'accordion' ][ 'current' ] ? ' mpc-active' : '';
			$title_classes  .= $MPC_Shortcode[ 'accordion' ][ 'presets' ][ 'title' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'accordion' ][ 'presets' ][ 'title' ] : '';
			$content_classes = $MPC_Shortcode[ 'accordion' ][ 'presets' ][ 'content' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'accordion' ][ 'presets' ][ 'content' ] : '';

			/* Return content */
			$title_markup = $MPC_Shortcode[ 'accordion' ][ 'icon' ] . '<h3>' . $atts[ 'title' ] . '</h3>';

			$return = '<li class="mpc-accordion__item' . esc_attr( $classes ) . '">';
				$return .= '<div class="mpc-accordion-item__heading' . esc_attr( $title_classes ) . '">' . $title_markup . '</div>';
				$return .= '<div class="mpc-accordion-item__content mpc-container" ' . $active . '>';
					if( isset( $MPC_Shortcode[ 'accordion' ][ 'indent' ] ) && $MPC_Shortcode[ 'accordion' ][ 'indent' ] != '' ) {
						$return .= '<div class="mpc-accordion-item__indent">' . $MPC_Shortcode[ 'accordion' ][ 'icon' ] . '</div>';
					}
					$return .= '<div class="mpc-accordion-item__wrapper' . esc_attr( $content_classes ) . '">';
						$return .= do_shortcode( $description );
			        $return .= '</div>';
			    $return .= '</div>';
			$return .= '</li>';

			$MPC_Shortcode[ 'accordion' ][ 'current' ]++;

			return $return;
		}

		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Title', 'mpc' ),
					'param_name'       => 'title',
					'tooltip'          => __( 'Define title for this accordion section.', 'mpc' ),
					'value'            => '',
				),
			);

			$class = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$class
			);

			return array(
				'name'            => __( 'Accordion Tab', 'mpc' ),
				'base'            => 'mpc_accordion_tab',
//				'icon'            => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-accordion.png',
				'icon'            => 'mpc-shicon-accordion-tab',
				'is_container'    => true,
				'content_element' => false,
				'params'          => $params,
				'js_view'         => 'mpcAccordionTabView',
			);
		}
	}
}
if ( class_exists( 'MPC_Accordion_Tab' ) ) {
	$MPC_Accordion_Tab = new MPC_Accordion_Tab;
}

if ( ! class_exists( 'MPC_Accordion' ) ) {
	global $mpc_accordion;

	class MPC_Accordion {
		public $shortcode = 'mpc_accordion';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_accordion', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_accordion-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_accordion/css/mpc_accordion.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_accordion-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_accordion/js/mpc_accordion'  . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $MPC_Icon, $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                                       => '',
				'preset'                                      => '',
				'content_preset'                              => '',
				'auto_close'                                  => 'false',
				'auto_indent'                                 => 'true',
				'opened'                                      => 1,

				'border_css'                                  => '',
				'margin_css'                                  => '',

				'hover_color'                                 => '',
				'hover_border'                                => '',

				'animation_in_type'                           => 'none',
				'animation_in_duration'                       => '300',
				'animation_in_delay'                          => '0',
				'animation_in_offset'                         => '100',

				/* Content */
				'content_font_preset'                         => '',
				'content_font_color'                          => '',
				'content_font_size'                           => '',
				'content_font_line_height'                    => '',
				'content_font_align'                          => '',
				'content_font_transform'                      => '',

				'content_background_type'                     => 'color',
				'content_background_color'                    => '',
				'content_background_image'                    => '',
				'content_background_image_size'               => 'large',
				'content_background_repeat'                   => 'no-repeat',
				'content_background_size'                     => 'initial',
				'content_background_position'                 => 'middle-center',
				'content_background_gradient'                 => '#83bae3||#80e0d4||0;100||180||linear',

				'content_margin_css'                          => '',
				'content_border_css'                          => '',
				'content_padding_css'                         => '',

				'hover_content_border'                        => '',

				/* Title */
				'title_font_preset'                           => '',
				'title_font_color'                            => '',
				'title_font_size'                             => '',
				'title_font_line_height'                      => '',
				'title_font_align'                            => '',
				'title_font_transform'                        => '',

				'title_background_type'                       => 'color',
				'title_background_color'                      => '',
				'title_background_image'                      => '',
				'title_background_image_size'                 => 'large',
				'title_background_repeat'                     => 'no-repeat',
				'title_background_size'                       => 'initial',
				'title_background_position'                   => 'middle-center',
				'title_background_gradient'                   => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_title_background_type'                 => 'color',
				'hover_title_background_color'                => '',
				'hover_title_background_image'                => '',
				'hover_title_background_image_size'           => 'large',
				'hover_title_background_repeat'               => 'no-repeat',
				'hover_title_background_size'                 => 'initial',
				'hover_title_background_position'             => 'middle-center',
				'hover_title_background_gradient'             => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_title_color'                           => '',
				'hover_title_border'                          => '',

				'title_border_css'                            => '',
				'title_padding_css'                           => '',
				'title_margin_css'                            => '',

				/* Icon */
				'mpc_icon__transition'                        => 'none',

				'mpc_icon__border_css'                        => '',
				'mpc_icon__padding_css'                       => '',
				'mpc_icon__hover_border_css'                  => '',

				'mpc_icon__icon_type'                         => 'icon',
				'mpc_icon__icon'                              => '',
				'mpc_icon__icon_character'                    => '',
				'mpc_icon__icon_image'                        => '',
				'mpc_icon__icon_image_size'                   => 'thumbnail',
				'mpc_icon__icon_preset'                       => '',
				'mpc_icon__icon_size'                         => '',
				'mpc_icon__icon_color'                        => '',

				'mpc_icon__title_background_type'             => 'color',
				'mpc_icon__title_background_color'            => '',
				'mpc_icon__title_background_image'            => '',
				'mpc_icon__title_background_image_size'       => 'large',
				'mpc_icon__title_background_repeat'           => 'no-repeat',
				'mpc_icon__title_background_size'             => 'initial',
				'mpc_icon__title_background_position'         => 'middle-center',
				'mpc_icon__title_background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_icon__hover_icon_type'                   => 'icon',
				'mpc_icon__hover_icon'                        => '',
				'mpc_icon__hover_icon_character'              => '',
				'mpc_icon__hover_icon_image'                  => '',
				'mpc_icon__hover_icon_image_size'             => 'thumbnail',
				'mpc_icon__hover_icon_preset'                 => '',
				'mpc_icon__hover_icon_color'                  => '',

				'mpc_icon__hover_title_background_type'       => 'color',
				'mpc_icon__hover_title_background_color'      => '',
				'mpc_icon__hover_title_background_image'      => '',
				'mpc_icon__hover_title_background_image_size' => 'large',
				'mpc_icon__hover_title_background_repeat'     => 'no-repeat',
				'mpc_icon__hover_title_background_size'       => 'initial',
				'mpc_icon__hover_title_background_position'   => 'middle-center',
				'mpc_icon__hover_title_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			/* Prepare */
			$animation = MPC_Parser::animation( $atts );
			$icon_atts = MPC_Parser::shortcode( $atts, 'mpc_icon_' );

			$icon = $MPC_Icon->shortcode_template( $icon_atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'auto_close' ] == 'true' ? ' mpc-accordion--toggle' : '';
			$classes .= $atts[ 'auto_indent' ] == 'true' ? ' mpc-accordion--indent' : '';
			$classes .= $atts[ 'auto_indent' ] == 'true' ? ' mpc-accordion--indent' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Setup globals */
			$MPC_Shortcode[ 'accordion' ] = array(
				'opened'     => $atts[ 'opened' ],
				'indent'     => $atts[ 'auto_indent' ],
				'current'    => 1,
				'icon'       => $icon,
				'presets'    => array(
					'title'   => $atts[ 'title_font_preset' ],
					'content' => $atts[ 'content_font_preset' ],
				),
			);

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-accordion' . esc_attr( $classes ) . '" ' . $animation . '>';
				$return .= '<ul class="mpc-accordion__content">' . do_shortcode( $description ) . '</ul>';
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Accordion', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_accordion-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'content_font_size' ] = $styles[ 'content_font_size' ] != '' ? $styles[ 'content_font_size' ] . ( is_numeric( $styles[ 'content_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Heading
			$inner_styles = array();
			if ( $styles[ 'title_border_css' ] ) { $inner_styles[] = $styles[ 'title_border_css' ]; }
			if ( $styles[ 'title_padding_css' ] ) { $inner_styles[] = $styles[ 'title_padding_css' ]; }
			if ( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__heading {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Content
			$inner_styles = array();
			if ( $styles[ 'content_border_css' ] ) { $inner_styles[] = $styles[ 'content_border_css' ]; }
			if ( $styles[ 'content_padding_css' ] ) { $inner_styles[] = $styles[ 'content_padding_css' ]; }
			if ( $styles[ 'content_margin_css' ] ) { $inner_styles[] = $styles[ 'content_margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'content' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles, 'content' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__wrapper {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			if ( $styles[ 'hover_border' ] ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"]:hover {';
					$style .= 'border-color:' . $styles[ 'hover_border' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_title_border' ] ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__heading:hover {';
					$style .= 'border-color:' . $styles[ 'hover_title_border' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_content_border' ] ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__wrapper:hover {';
					$style .= 'border-color:' . $styles[ 'hover_content_border' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_title_color' ] ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__heading:hover .mpc-icon i {';
					$style .= 'color:' . $styles[ 'hover_title_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'hover_title_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'hover_title_color' ] . ';'; }
			if ( $temp_style = MPC_CSS::background( $styles, 'hover_title' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__heading:hover,';
				$style .= '.mpc-accordion[id="' . $css_id . '"] .mpc-accordion-item__heading.mpc-active {';
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
					'type'             => 'checkbox',
					'heading'          => __( 'Close Others', 'mpc' ),
					'param_name'       => 'auto_close',
					'tooltip'          => __( 'Check to enable automatic closing other sections when opening a new one.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Auto Indent', 'mpc' ),
					'param_name'       => 'auto_indent',
					'tooltip'          => __( 'Check to enable automatic indent to sections content.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Active Item', 'mpc' ),
					'param_name'       => 'opened',
					'tooltip'          => __( 'Define which section should be opened by default.', 'mpc' ),
					'value'            => '',
					'label'            => '',
					'addon'            => array(
						'icon'  => 'dashicons-clipboard',
						'align' => 'prepend'
					),
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-before-section mpc-advanced-field',
				),
			);

			$hover_title = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover - Title', 'mpc' ),
					'tooltip'    => __( 'If you want to change the title color after hover choose a different one from the color picker below.', 'mpc' ),
					'param_name' => 'hover_title_divider',
					'group'      => __( 'Title', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Text', 'mpc' ),
					'param_name'       => 'hover_title_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Title', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border', 'mpc' ),
					'param_name'       => 'hover_title_border',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Title', 'mpc' ),
				),
			);

			$hover_content = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover - Border', 'mpc' ),
					'param_name'       => 'hover_content_border',
					'tooltip'          => __( 'If you want to change the border color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-no-wrap',
					'group'            => __( 'Content', 'mpc' ),
				),
			);

			$hover = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover - Border', 'mpc' ),
					'param_name'       => 'hover_border',
					'tooltip'          => __( 'If you want to change the border color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-no-wrap',
				),
			);

			$title_atts = array( 'prefix' => 'title', 'group' => __( 'Title', 'mpc' ) );

			/* Integrate Icon */
			$icon_exclude = array( 'exclude_regex' => '/animation_(.*)|margin(.*)|url|preset|tooltip_(.*)/' );
			$integrate_icon = vc_map_integrate_shortcode( 'mpc_icon', 'mpc_icon__', __( 'Title Icon', 'mpc' ), $icon_exclude );

			$title_font       = MPC_Snippets::vc_font( $title_atts );
			$title_padding    = MPC_Snippets::vc_padding( $title_atts );
			$title_margin     = MPC_Snippets::vc_margin( $title_atts );
			$title_background = MPC_Snippets::vc_background( $title_atts );
			$title_border     = MPC_Snippets::vc_border( $title_atts );

			$hover_title_background = MPC_Snippets::vc_background( array( 'prefix' => 'hover_title', 'subtitle' => __( 'Hover', 'mpc' ), 'tooltip' => __( 'If you want to change the background after hover set it up below.', 'mpc' ), 'group' => __( 'Title', 'mpc' ) ) );

			$content_atts = array( 'prefix' => 'content', 'group' => __( 'Content', 'mpc' ) );

			$content_font       = MPC_Snippets::vc_font( $content_atts );
			$content_padding    = MPC_Snippets::vc_padding( $content_atts );
			$content_margin     = MPC_Snippets::vc_margin( $content_atts );
			$content_background = MPC_Snippets::vc_background( $content_atts );
			$content_border     = MPC_Snippets::vc_border( $content_atts );

			$margin    = MPC_Snippets::vc_margin();
			$border    = MPC_Snippets::vc_border();
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,

				$title_font,
				$title_background,
				$title_border,
				$title_padding,
				$title_margin,

				$hover_title,
				$hover_title_background,

				$content_font,
				$content_background,
				$content_border,
				$content_padding,
				$content_margin,
				$hover_content,

				$border,
				$margin,
				$hover,

				$integrate_icon,

				$animation,
				$class
			);

			$default_content = '[mpc_accordion_tab title="' . __( 'Tab 1', 'mpc' ) . '"][/mpc_accordion_tab]';
			$default_content .= '[mpc_accordion_tab title="' . __( 'Tab 2', 'mpc' ) . '"][/mpc_accordion_tab]';

			$custom_markup = PHP_EOL . '<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">';
			$custom_markup .= PHP_EOL . '%content%';
			$custom_markup .= PHP_EOL . '</div>';
			$custom_markup .= PHP_EOL . '<div class="tab_controls">';
			$custom_markup .= PHP_EOL . '<a class="add_tab" title="' . __( 'Add section', 'mpc' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . __( 'Add section', 'mpc' ) . '</span></a>';
			$custom_markup .= PHP_EOL . '</div>';

			return array(
				'name'                    => __( 'Accordion', 'mpc' ),
				'description'             => __( 'Collapsible content blocks', 'mpc' ),
				'base'                    => 'mpc_accordion',
				'show_settings_on_create' => false,
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_accordion_tab', ),
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-accordion.png',
				'icon'                    => 'mpc-shicon-accordion',
				'category'                => __( 'Massive', 'mpc' ),
				'wrapper_class'           => 'vc_clearfix',
				'params'                  => $params,
				'default_content'         => $default_content,
				'custom_markup'           => $custom_markup,
				'js_view'                 => 'mpcAccordionView',
			);
		}
	}
}
if ( class_exists( 'MPC_Accordion' ) ) {
	global $MPC_Accordion;
	$MPC_Accordion = new MPC_Accordion;
}

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_accordion' ) ) {
	class WPBakeryShortCode_mpc_accordion extends WPBakeryShortCode {
		protected $controls_css_settings = 'out-tc vc_controls-content-widget';

		public function __construct( $settings ) {
			parent::__construct( $settings );
		}

		public function contentAdmin( $atts, $content = NULL ) {
			$width = $custom_markup = '';
			$shortcode_attributes = array( 'width' => '1/1' );
			foreach ( $this->settings['params'] as $param ) {
				if ( $param['param_name'] != 'content' ) {
					if ( isset( $param['value'] ) && is_string( $param['value'] ) ) {
						$shortcode_attributes[ $param['param_name'] ] = __( $param['value'], "mpc" );
					} elseif ( isset( $param['value'] ) ) {
						$shortcode_attributes[ $param['param_name'] ] = $param['value'];
					}
				} else if ( $param['param_name'] == 'content' && $content == null ) {
					$content = __( $param['value'], "mpc" );
				}
			}
			extract( shortcode_atts( $shortcode_attributes, $atts ) );

			$elem = $this->getElementHolder( $width );

			$inner = '';
			foreach ( $this->settings['params'] as $param ) {
				$param_name = $param[ 'param_name' ];
				$param_value   = isset( $$param_name ) ? $$param_name : '';
				if ( is_array( $param_value ) ) {
					// Get first element from the array
					reset( $param_value );
					$first_key = key( $param_value );
					$param_value = $param_value[ $first_key ];
				}
				$inner .= $this->singleParamHtmlHolder( $param, $param_value );
			}

			if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
				if ( $content != '' ) {
					$custom_markup = str_ireplace( "%content%", $content, $this->settings["custom_markup"] );
				} else if ( $content == '' && isset( $this->settings["default_content_in_template"] ) && $this->settings["default_content_in_template"] != '' ) {
					$custom_markup = str_ireplace( "%content%", $this->settings["default_content_in_template"], $this->settings["custom_markup"] );
				} else {
					$custom_markup = str_ireplace( "%content%", '', $this->settings["custom_markup"] );
				}

				$inner .= do_shortcode( $custom_markup );
			}
			$elem = str_ireplace( '%wpb_element_content%', $inner, $elem );
			$output = $elem;

			return $output;
		}
	}
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-column.php' );
if ( class_exists( 'WPBakeryShortCode_VC_Column' ) && ! class_exists( 'WPBakeryShortCode_mpc_accordion_tab' ) ) {
	define( 'MPC_ACCORDION_TITLE', __( 'Accordion Tab', 'mpc' ) );

	class WPBakeryShortCode_mpc_accordion_tab extends WPBakeryShortCode_VC_Column {
		protected $controls_css_settings  = 'tc vc_control-container';
		protected $controls_list          = array( 'add', 'edit', 'clone', 'delete' );
		protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl.php';
		protected $predefined_atts        = array(
			'el_class' => '',
			'width'    => '',
			'title'    => MPC_ACCORDION_TITLE
		);

		public function __construct( $settings ) {
			parent::__construct( $settings );
		}

		public function contentAdmin( $atts, $content = null ) {
			$width = $el_class = $title = '';
			extract( shortcode_atts( $this->predefined_atts, $atts ) );
			$output = '';

			$column_controls        = $this->getColumnControls( $this->settings( 'controls' ) );
			$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );

			if ( $width == 'column_14' || $width == '1/4' ) {
				$width = array( 'vc_col-sm-3' );
			} else if ( $width == 'column_14-14-14-14' ) {
				$width = array( 'vc_col-sm-3', 'vc_col-sm-3', 'vc_col-sm-3', 'vc_col-sm-3' );
			} else if ( $width == 'column_13' || $width == '1/3' ) {
				$width = array( 'vc_col-sm-4' );
			} else if ( $width == 'column_13-23' ) {
				$width = array( 'vc_col-sm-4', 'vc_col-sm-8' );
			} else if ( $width == 'column_13-13-13' ) {
				$width = array( 'vc_col-sm-4', 'vc_col-sm-4', 'vc_col-sm-4' );
			} else if ( $width == 'column_12' || $width == '1/2' ) {
				$width = array( 'vc_col-sm-6' );
			} else if ( $width == 'column_12-12' ) {
				$width = array( 'vc_col-sm-6', 'vc_col-sm-6' );
			} else if ( $width == 'column_23' || $width == '2/3' ) {
				$width = array( 'vc_col-sm-8' );
			} else if ( $width == 'column_34' || $width == '3/4' ) {
				$width = array( 'vc_col-sm-9' );
			} else if ( $width == 'column_16' || $width == '1/6' ) {
				$width = array( 'vc_col-sm-2' );
			} else {
				$width = array( '' );
			}
			for ( $i = 0; $i < count( $width ); $i++ ) {
				$output .= '<div class="group wpb_sortable">';
				$output .= '<h3><span class="tab-label"><%= params.title %></span></h3>';
				$output .= '<div ' . $this->mainHtmlBlockParams( $width, $i ) . '>';
				$output .= str_replace( "%column_size%", wpb_translateColumnWidthToFractional( $width[ $i ] ), $column_controls );
				$output .= '<div class="wpb_element_wrapper">';
				$output .= '<div ' . $this->containerHtmlBlockParams( $width, $i ) . '>';
				$output .= do_shortcode( shortcode_unautop( $content ) );
				$output .= '</div>';
				if ( isset( $this->settings[ 'params' ] ) ) {
					$inner = '';
					foreach ( $this->settings[ 'params' ] as $param ) {
						$param_name = $param[ 'param_name' ];
						$param_value   = isset( $$param_name ) ? $$param_name : '';
						if ( is_array( $param_value ) ) {
							// Get first element from the array
							reset( $param_value );
							$first_key   = key( $param_value );
							$param_value = $param_value[ $first_key ];
						}
						$inner .= $this->singleParamHtmlHolder( $param, $param_value );
					}
					$output .= $inner;
				}
				$output .= '</div>';
				$output .= str_replace( "%column_size%", wpb_translateColumnWidthToFractional( $width[ $i ] ), $column_controls_bottom );
				$output .= '</div>';
				$output .= '</div>';
			}

			return $output;
		}

		public function mainHtmlBlockParams( $width, $i ) {
			return 'data-element_type="' . $this->settings[ "base" ] . '" class=" wpb_' . $this->settings[ 'base' ] . '"' . $this->customAdminBlockParams();
		}

		public function containerHtmlBlockParams( $width, $i ) {
			return 'class="wpb_column_container vc_container_for_children"';
		}

		public function getColumnControls( $controls, $extended_css = '' ) {
			return $this->getColumnControlsModular( $extended_css );
		}

		protected function outputTitle( $title ) {
			return '';
		}

		public function customAdminBlockParams() {
			return '';
		}
	}
}
