<?php
/*----------------------------------------------------------------------------*\
	TABS SHORTCODE
\*----------------------------------------------------------------------------*/
if ( ! class_exists( 'MPC_Tab' ) ) {
	class MPC_Tab {
		public $shortcode = 'mpc_tab';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_tab', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		function shortcode_template( $atts, $description = null ) {
			global $MPC_Shortcode, $MPC_Button;

			$atts = shortcode_atts( array(
				'class'                       => '',
				'title'                       => '',
				'tab_id'                      => '',
				'mpc_button__icon_type'       => 'icon',
				'mpc_button__icon'            => '',
				'mpc_button__icon_character'  => '',
				'mpc_button__icon_image'      => '',
				'mpc_button__icon_image_size' => 'thumbnail',
				'mpc_button__icon_preset'     => '',
				'mpc_button__icon_color'      => '#333333',
				'mpc_button__icon_size'       => '',
				'mpc_button__icon_effect'     => 'none-none',
			), $atts );

			$classes = ' mpc-container mpc-transition';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$active_tab = ( $MPC_Shortcode[ 'tabs' ][ 'active' ] == 0 ) ? 1 : $MPC_Shortcode[ 'tabs' ][ 'active' ]; // Active Tab Issue Backward Compatibility

			$is_active = count( $MPC_Shortcode[ 'tabs' ][ 'nav' ] ) == ( $active_tab - 1 ); // Active Tab Fix

			$tab_atts = $atts[ 'tab_id' ] != '' ? ' id="' . esc_attr( $atts[ 'tab_id' ] ) . '"' : '';
			$tab_atts .= $is_active ? ' data-active="true"' : ' data-active="false"';

			$nav_classes = $is_active ? ' mpc-active' : '';
			$nav_atts = $atts[ 'tab_id' ] != '' ? ' data-tab_id="' . esc_attr( $atts[ 'tab_id' ] ) . '"' : '';

			/* Merge Buttons options */
			$button_atts = MPC_Parser::shortcode( $atts, 'mpc_button_' );
			$button_atts[ 'title' ] = $atts[ 'title' ];
			$button_atts = array_merge( $button_atts, $MPC_Shortcode[ 'tabs' ][ 'button' ] );

			/* Append new Tab Button */
			$tab_button = '<li class="mpc-tabs__nav-item mpc-parent-hover' . $nav_classes . '"' . $nav_atts . '>';
				$tab_button .= $MPC_Button->shortcode_template( $button_atts );
			$tab_button .= '</li>';

			$MPC_Shortcode[ 'tabs' ][ 'nav' ][] = $tab_button;

			/* Return Tab Content */
			$return = '<div class="mpc-tab' . $classes . '"' . $tab_atts . '><div class="mpc-tab__content">';
				$return .= do_shortcode( $description );
		    $return .= '</div></div>';

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
					'tooltip'          => __( 'Define title for this tab section.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Tab ID', 'mpc' ),
					'param_name'       => 'tab_id',
					'tooltip'          => __( 'Define unique ID for this tab section. It\'s best to leave default value.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-first-row',
				),
			);

			$button_icon_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Display Effect', 'mpc' ),
					'param_name'       => 'mpc_button__icon_effect',
					'tooltip'          => __( 'Select icon display style:<br><b>None</b>: hide the icon;<br><b>Stay</b>: display icon on selected side;<br><b>Slide In</b>: slide icon in from selected side;<br><b>Push Out</b>: push out button text with icon from selected side.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )                   => 'none-none',
						__( 'Stay - Left', 'mpc' )            => 'stay-left',
						__( 'Stay - Right', 'mpc' )           => 'stay-right',
						__( 'Slide In - from Left', 'mpc' )   => 'slide-left',
						__( 'Slide In - from Right', 'mpc' )  => 'slide-right',
						__( 'Push Out - from Top', 'mpc' )    => 'push_out-top',
						__( 'Push Out - from Right', 'mpc' )  => 'push_out-right',
						__( 'Push Out - from Bottom', 'mpc' ) => 'push_out-bottom',
						__( 'Push Out - from Left', 'mpc' )   => 'push_out-left',
					),
					'std'           => 'none-none',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Gap', 'mpc' ),
					'param_name'       => 'mpc_button__icon_gap',
					'tooltip'          => __( 'Define gap between icon and text.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'icon_effect', 'value' => array( 'stay-left', 'stay-right' ) ),
				),
			);
			$button_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'mpc_button_' ) );

			$class = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$button_icon,
				$button_icon_effect,
				$class
			);

			return array(
				'name'            => __( 'Tab', 'mpc' ),
				'base'            => 'mpc_tab',
				'icon'            => 'mpc-shicon-tab',
				'is_container'    => true,
				'content_element' => false,
				'params'          => $params,
				'js_view'         => 'mpcTabView',
			);
		}
	}
}
if ( class_exists( 'MPC_Tab' ) ) {
	$MPC_Tab = new MPC_Tab;
}

if ( ! class_exists( 'MPC_Tabs' ) ) {
	global $MPC_Shortcode;
	$MPC_Shortcode[ 'tabs' ] = array();

	class MPC_Tabs {
		public $shortcode = 'mpc_tabs';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_tabs', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$parts = array(
				'navigation' => '',
				'content'    => '',
			);

			$this->parts = $parts;
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_tabs-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_tabs/css/mpc_tabs.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_tabs-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_tabs/js/mpc_tabs' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'top'    => array( 'navigation', 'content' ),
				'bottom' => array( 'content', 'navigation' ),
				'left'   => array( 'navigation', 'content' ),
				'right'  => array( 'navigation', 'content' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $parts[ $part ];
			}

			return $content;
		}


		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                 => '',
				'preset'                => '',
				'content_preset'        => '',
				'tabs_position'         => 'top',
				'tabs_v_align'          => 'top',
				'tabs_h_align'          => 'left',
				'active_tab'            => '1',
				'decor_line'            => '',
				'decor_color'           => '',
				'decor_active'          => '',
				'decor_size'            => '2',
				'decor_gap'             => '5',

				'font_preset'           => '',
				'font_color'            => '',
				'font_size'             => '',
				'font_line_height'      => '',
				'font_align'            => '',
				'font_transform'        => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'padding_css'           => '',
				'margin_css'            => '',
				'content_padding_css'   => '',

				/* Button */
				'button_margin_css'                       => '',
				'mpc_button__disable'                     => '',

				'mpc_button__font_preset'                 => '',
				'mpc_button__font_color'                  => '',
				'mpc_button__font_size'                   => '',
				'mpc_button__font_line_height'            => '',
				'mpc_button__font_align'                  => '',
				'mpc_button__font_transform'              => '',

				'mpc_button__padding_css'                 => '',
				'mpc_button__border_css'                  => '',

				'mpc_button__background_type'             => 'color',
				'mpc_button__background_color'            => '',
				'mpc_button__background_image'            => '',
				'mpc_button__background_image_size'       => 'large',
				'mpc_button__background_repeat'           => 'no-repeat',
				'mpc_button__background_size'             => 'initial',
				'mpc_button__background_position'         => 'middle-center',
				'mpc_button__background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_button__hover_border_css'            => '',

				'mpc_button__hover_font_color'            => '',
				'mpc_button__hover_icon_color'            => '',

				'mpc_button__hover_background_type'       => 'color',
				'mpc_button__hover_background_color'      => '',
				'mpc_button__hover_background_image'      => '',
				'mpc_button__hover_background_image_size' => 'large',
				'mpc_button__hover_background_repeat'     => 'no-repeat',
				'mpc_button__hover_background_size'       => 'initial',
				'mpc_button__hover_background_position'   => 'middle-center',
				'mpc_button__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'mpc_button__hover_background_effect'     => 'fade-in',
				'mpc_button__hover_background_offset'     => '',

				'animation_in_type'                       => 'none',
				'animation_in_duration'                   => '300',
				'animation_in_delay'                      => '0',
				'animation_in_offset'                     => '100',
			), $atts );

			/* Prepare */
			$MPC_Shortcode[ 'tabs' ][ 'nav' ] = '';
			$active = ' data-active="' . esc_attr( $atts[ 'active_tab' ] ) . '"';
			$MPC_Shortcode[ 'tabs' ][ 'nav' ] = array();
			$MPC_Shortcode[ 'tabs' ][ 'active' ] = (int) $atts[ 'active_tab' ];

			MPC_Helper::add_typography( $atts[ 'font_preset' ] );

			$animation = MPC_Parser::animation( $atts );
			$MPC_Shortcode[ 'tabs' ][ 'button' ] = MPC_Parser::shortcode( $atts, 'mpc_button_' );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';//' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'tabs_position' ] != '' ? ' mpc-tabs--' . esc_attr( $atts[ 'tabs_position' ] ) : ' mpc-tabs--top';
			$classes .= $atts[ 'decor_line' ] == 'true' ? ' mpc-tabs--decor-line' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			if( in_array( $atts[ 'tabs_position' ], array( 'left', 'right' ) ) ) {
				$classes_nav = $atts[ 'tabs_v_align' ] != '' ? ' mpc-align--' . $atts[ 'tabs_v_align' ] : '';
			} else {
				$classes_nav = $atts[ 'tabs_h_align' ] != '' ? ' mpc-align--' . $atts[ 'tabs_h_align' ] : '';
			}

			$content_classes = ' mpc-transition';
			$content_classes .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';

			/* Layout parts */
			$this->parts[ 'content' ]   = '<div class="mpc-tabs__content' . $content_classes . '">' . do_shortcode( $description ) . '</div>';
			$this->parts[ 'navigation'] = '<div class="mpc-tabs__nav' . $classes_nav . '"><ul>' . join( '', $MPC_Shortcode[ 'tabs' ][ 'nav' ] ) . '</ul></div>';

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-tabs' . $classes . '" ' . $animation . $active . '>';
				$return .= $this->shortcode_layout( $atts[ 'tabs_position' ], $this->parts );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Tabs', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			unset( $MPC_Shortcode[ 'tabs' ] );

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_tabs-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'decor_size' ] = $styles[ 'decor_size' ] != '' ? $styles[ 'decor_size' ] . ( is_numeric( $styles[ 'decor_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'decor_gap' ] = $styles[ 'decor_gap' ] != '' ? $styles[ 'decor_gap' ] . ( is_numeric( $styles[ 'decor_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-tabs[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'content_padding_css' ] ) { $inner_styles[] = $styles[ 'content_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-tabs[id="' . $css_id . '"] .mpc-tab {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Button
			if ( $styles[ 'button_margin_css' ] ) {
				$style .= '.mpc-tabs[id="' . $css_id . '"] .mpc-tabs__nav-item {';
					$style .= $styles[ 'button_margin_css' ];
				$style .= '}';
			}

			// Decor Line
			if( $styles[ 'decor_line' ] == 'true' ) {
				$inner_styles = array();
				if ( $styles[ 'decor_color' ] ) { $inner_styles[] = 'border-color: ' . $styles[ 'decor_color' ] . ';'; }
				if ( $styles[ 'decor_size' ] ) { $inner_styles[] = 'border-width: ' . $styles[ 'decor_size' ] . ';'; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-tabs[id="' . $css_id . '"].mpc-tabs--decor-line .mpc-tabs__content, .mpc-tabs[id="' . $css_id . '"] .mpc-tabs__nav-item {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				if ( $styles[ 'decor_size' ] ) {
					$style .= '.mpc-tabs[id="' . $css_id . '"].mpc-tabs--decor-line .mpc-tabs__nav {';
						$style .= 'margin: -' . $styles[ 'decor_size' ] . ';';
					$style .= '}';
				}

				if ( $styles[ 'decor_gap' ] ) {
					$style .= '.mpc-tabs[id="' . $css_id . '"] .mpc-tabs__nav-item {';
						$style .= 'padding: ' . $styles[ 'decor_gap' ] . ';';
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'decor_size' ] ) { $inner_styles[] = 'width: ' . $styles[ 'decor_size' ] . ';height: ' . $styles[ 'decor_size' ] . ';margin: -' . $styles[ 'decor_size' ] . ';'; }
				if ( $styles[ 'decor_active' ] ) { $inner_styles[] = 'background-color: ' . $styles[ 'decor_active' ] . ';'; }

				if ( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-tabs[id="' . $css_id . '"].mpc-tabs--decor-line .mpc-tabs__nav-item:after {';
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
					'heading'          => __( 'Tabs position', 'mpc' ),
					'param_name'       => 'tabs_position',
					'tooltip'          => __( 'Select tabs position.', 'mpc' ),
					'value'            => array(
						__( 'Top' )    => 'top',
						__( 'Bottom' ) => 'bottom',
						__( 'Left' )   => 'left',
						__( 'Right' )  => 'right',
					),
					'std'              => 'top',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Tabs Alignment', 'mpc' ),
					'param_name'       => 'tabs_v_align',
					'tooltip'          => __( 'Select tabs alignment.', 'mpc' ),
					'value'            => array(
						__( 'Top' )    => 'top',
						__( 'Middle' ) => 'middle',
						__( 'Bottom' ) => 'bottom',
					),
					'std'              => 'top',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'tabs_position', 'value' => array( 'left', 'right' ) ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Tabs Alignment', 'mpc' ),
					'param_name'       => 'tabs_h_align',
					'tooltip'          => __( 'Select tabs alignment.', 'mpc' ),
					'value'            => array(
						__( 'Left' )   => 'left',
						__( 'Center' ) => 'center',
						__( 'Right' )  => 'right',
					),
					'std'              => 'left',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'tabs_position', 'value' => array( 'top', 'bottom' ) ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Active Item', 'mpc' ),
					'param_name'       => 'active_tab',
					'tooltip'          => __( 'Define which section should be opened by default.', 'mpc' ),
					'value'            => '1',
					'label'            => '',
					'addon'            => array(
						'icon'  => 'dashicons-clipboard',
						'align' => 'prepend'
					),
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Decor Line', 'mpc' ),
					'param_name'       => 'decor_line',
					'tooltip'          => __( 'Check to enable deco line. Enabling it will display a line between tabs buttons and content.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Decor Line', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-clear--both',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'decor_color',
					'tooltip'          => __( 'Choose default line color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-first-row',
					'group'            => __( 'Decor Line', 'mpc' ),
					'dependency'       => array( 'element' => 'decor_line', 'value' => 'true' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Active Color', 'mpc' ),
					'param_name'       => 'decor_active',
					'tooltip'          => __( 'Choose active tab line color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-first-row',
					'group'            => __( 'Decor Line', 'mpc' ),
					'dependency'       => array( 'element' => 'decor_line', 'value' => 'true' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Line Width', 'mpc' ),
					'param_name'       => 'decor_size',
					'tooltip'          => __( 'Choose line thickness.', 'mpc' ),
					'value'            => 2,
					'step'             => 1,
					'min'              => 1,
					'max'              => 20,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Decor Line', 'mpc' ),
					'dependency'       => array( 'element' => 'decor_line', 'value' => 'true' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Line Gap', 'mpc' ),
					'param_name'       => 'decor_gap',
					'tooltip'          => __( 'Choose gap between line and tabs buttons.', 'mpc' ),
					'value'            => 5,
					'step'             => 1,
					'min'              => 0,
					'max'              => 40,
					'unit'             => 'px',
					'group'            => __( 'Decor Line', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-before-section mpc-advanced-field',
					'dependency'       => array( 'element' => 'decor_line', 'value' => 'true' ),
				),
			);

			/* Integrate Button */
			$button_exclude    = array( 'exclude_regex' => '/animation_(.*)|^icon(.*)|margin_(.*)|mpc_tooltip(.*)|url|block|title/' );
			$integrate_button  = vc_map_integrate_shortcode( 'mpc_button', 'mpc_button__', __( 'Tab Button', 'mpc' ), $button_exclude );
			$button_margin     = MPC_Snippets::vc_margin( array( 'prefix' => 'button', 'group' => __( 'Tab Button', 'mpc' ) ) );

			$content_font    = MPC_Snippets::vc_font( array( 'prefix' => '', 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );
            $content_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );

			$margin     = MPC_Snippets::vc_margin();
			$padding    = MPC_Snippets::vc_padding();
			$background = MPC_Snippets::vc_background();

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,

				$content_font,
				$content_padding,

				$background,
				$padding,
				$margin,

				$integrate_button,
				$button_margin,

				$animation,
				$class
			);

			$tab_1_id = time() . '-1-' . rand( 0, 100 );
			$tab_2_id = time() . '-2-' . rand( 0, 100 );

			$default_content = '[mpc_tab title="' . __( 'Tab 1', 'mpc' ) . '" tab_id="' . $tab_1_id . '"][/mpc_tab]';
			$default_content .= '[mpc_tab title="' . __( 'Tab 2', 'mpc' ) . '" tab_id="' . $tab_2_id . '"][/mpc_tab]';

			$custom_markup = PHP_EOL . '<div class="wpb_tabs_holder wpb_holder vc_container_for_children">';
			$custom_markup .= PHP_EOL . '<ul class="tabs_controls">' . PHP_EOL .  '</ul>';
			$custom_markup .= PHP_EOL . '%content%';
			$custom_markup .= PHP_EOL . '</div>';

			return array(
				'name'                    => __( 'Tabs', 'mpc' ),
				'description'             => __( 'Tabbed content blocks', 'mpc' ),
				'base'                    => 'mpc_tabs',
				'show_settings_on_create' => false,
				'is_container'            => true,
				'container_not_allowed'   => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-tabs.png',
				'icon'                    => 'mpc-shicon-tabs',
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
if ( class_exists( 'MPC_Tabs' ) ) {
	global $MPC_Tabs;
	$MPC_Tabs = new MPC_Tabs;
}

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_tabs' ) ) {
	class WPBakeryShortCode_mpc_tabs extends WPBakeryShortCode {
		static    $filter_added          = false;
		protected $controls_css_settings = 'out-tc vc_controls-content-widget';
		protected $controls_list         = array( 'edit', 'clone', 'delete' );

		public function __construct( $settings ) {
			parent::__construct( $settings );
		}
		public function contentAdmin( $atts, $content = null ) {
			$width = $custom_markup = '';
			$shortcode_attributes = array( 'width' => '1/1' );
			foreach ( $this->settings['params'] as $param ) {
				if ( 'content' !== $param['param_name'] ) {
					$shortcode_attributes[ $param['param_name'] ] = isset( $param['value'] ) ? $param['value'] : null;
				} elseif ( 'content' === $param['param_name'] && null === $content ) {
					$content = $param['value'];
				}
			}
			extract( shortcode_atts( $shortcode_attributes, $atts ) );

			preg_match_all( '/mpc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );

			$output = '';
			$tab_titles = array();

			if ( isset( $matches[0] ) ) {
				$tab_titles = $matches[0];
			}
			$tmp = '';
			if ( count( $tab_titles ) ) {
				$tmp .= '<ul class="clearfix tabs_controls">';
				foreach ( $tab_titles as $tab ) {
					preg_match( '/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE );
					if ( isset( $tab_matches[1][0] ) ) {
						$tmp .= '<li><a href="#tab-' . ( isset( $tab_matches[3][0] ) ? $tab_matches[3][0] : sanitize_title( $tab_matches[1][0] ) ) . '">' . $tab_matches[1][0] . '</a></li>';
					}
				}
				$tmp .= '</ul>' . "\n";
			} else {
				$output .= do_shortcode( $content );
			}

			$elem = $this->getElementHolder( $width );

			$iner = '';
			foreach ( $this->settings['params'] as $param ) {
				$param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
				if ( is_array( $param_value ) ) {
					// Get first element from the array
					reset( $param_value );
					$first_key = key( $param_value );
					$param_value = $param_value[ $first_key ];
				}
				$iner .= $this->singleParamHtmlHolder( $param, $param_value );
			}

			if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
				if ( '' !== $content ) {
					$custom_markup = str_ireplace( '%content%', $tmp . $content, $this->settings['custom_markup'] );
				} elseif ( '' === $content && isset( $this->settings['default_content_in_template'] ) && '' !== $this->settings['default_content_in_template'] ) {
					$custom_markup = str_ireplace( '%content%', $this->settings['default_content_in_template'], $this->settings['custom_markup'] );
				} else {
					$custom_markup = str_ireplace( '%content%', '', $this->settings['custom_markup'] );
				}
				$iner .= do_shortcode( $custom_markup );
			}
			$elem = str_ireplace( '%wpb_element_content%', $iner, $elem );
			$output = $elem;

			return $output;
		}

		public function getTabTemplate() {
			return '<div class="wpb_template">' . do_shortcode( '[mpc_tab title="Tab" tab_id="tab"][/mpc_tab]' ) . '</div>';
		}

		public function setCustomTabId( $content ) {
			return preg_replace( '/tab\_id\=\"([^\"]+)\"/', 'tab_id="$1-' . time() . '"', $content );
		}

		public function singleParamHtmlHolder( $param, $value ) {
			$output = '';

			$param_name = isset( $param[ 'param_name' ] ) ? $param[ 'param_name' ] : '';
			$group      = isset( $param[ 'group' ] ) ? '[' . $param[ 'group' ] . '] ' : '';
			$heading    = isset( $param[ 'heading' ] ) ? $param[ 'heading' ] : '';
			$type       = isset( $param[ 'type' ] ) ? $param[ 'type' ] : '';
			$class      = isset( $param[ 'class' ] ) ? $param[ 'class' ] : '';

			if ( isset( $param['holder'] ) && $param['holder'] !== 'hidden' ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}

			if ( isset( $param['admin_label'] ) && $param['admin_label'] === true ) {
				$output .= '<span class="vc_admin_label admin_label_' . $param_name . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . $group . $heading . '</label>: ' . $value . '</span>';
			}

			return $output;
		}
	}
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-column.php' );
if ( class_exists( 'WPBakeryShortCode_VC_Column' ) && ! class_exists( 'WPBakeryShortCode_mpc_tab' ) ) {
	define( 'MPC_TAB_TITLE', __( 'Tab', 'mpc' ) );

	class WPBakeryShortCode_mpc_tab extends WPBakeryShortCode_VC_Column {
		protected $controls_css_settings  = 'tc vc_control-container';
		protected $controls_list          = array( 'add', 'edit', 'clone', 'delete' );
		protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl.php';
		protected $predefined_atts = array(
			'tab_id' => MPC_TAB_TITLE,
			'title' => '',
		);

		public function __construct( $settings ) {
			parent::__construct( $settings );
		}

		public function customAdminBlockParams() {
			return ' id="tab-' . $this->atts['tab_id'] . '"';
		}

		public function mainHtmlBlockParams( $width, $i ) {
			if ( function_exists( 'vc_user_access_check_shortcode_all' ) ) {
				$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );
			} else {
				$sortable = 'wpb_sortable';
			}

			return 'data-element_type="' . $this->settings['base'] . '" class="wpb_' . $this->settings['base'] . ' ' . $sortable . ' wpb_content_holder"' . $this->customAdminBlockParams();
		}

		public function containerHtmlBlockParams( $width, $i ) {
			return 'class="wpb_column_container vc_container_for_children"';
		}

		public function getColumnControls( $controls, $extended_css = '' ) {
			return $this->getColumnControlsModular( $extended_css );
		}
	}
}
