<?php
/*----------------------------------------------------------------------------*\
	PRICING LEGEND SHORTCODE
\*----------------------------------------------------------------------------*/

if ( !class_exists( 'MPC_Pricing_Legend' ) ) {
	class MPC_Pricing_Legend {
		public $shortcode = 'mpc_pricing_legend';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_pricing_legend', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_pricing_legend-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pricing_legend/css/mpc_pricing_legend.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_pricing_legend-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pricing_legend/js/mpc_pricing_legend' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                     => '',
				'preset'                    => '',

				/* General */
				'properties'                => '',
				'properties_font_color'     => '',
				'properties_font_align'     => '',
				'properties_font_transform' => '',
				'prop_padding_css'          => '',

				'prop_even_background_type'       => 'color',
				'prop_even_background_color'      => '',
				'prop_even_background_image'      => '',
				'prop_even_background_image_size' => 'large',
				'prop_even_background_repeat'     => 'no-repeat',
				'prop_even_background_size'       => 'initial',
				'prop_even_background_position'   => 'middle-center',
				'prop_even_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
            ), $atts );

			/* Prepare */
			$styles   = $this->shortcode_styles( $atts );
			$css_id   = $styles[ 'id' ];
			$MPC_Shortcode[ 'pricing' ][ 'style' ] .= $styles[ 'css' ];

			$animation = MPC_Parser::animation( $atts );
			$properties = explode( '|||', $atts[ 'properties' ] );

			/* Fonts Presets */
			$title_classes = $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'title' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'title' ] : '';
			$price_classes = $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'price' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'price' ] : '';
			$props_classes = $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'prop' ] != '' ? ' mpc-typography--' .  $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'prop' ]  : '';

			/* Prepare Parts */
			$title  = $MPC_Shortcode[ 'pricing' ][ 'disable' ][ 'title' ] == '' ? '<div class="mpc-pricing__title' . $title_classes . '">&nbsp;</div>' : '';
			$price  = $MPC_Shortcode[ 'pricing' ][ 'disable' ][ 'price' ] == '' ? '<div class="mpc-pricing__price' . $price_classes . '">&nbsp;</div>' : '';

			$props = '<div class="mpc-pricing__properties' . $props_classes . '">';
			foreach( $properties as $property ) {
				$props .= '<div class="mpc-pricing__property">' . $property . '</div>';
			}
			$props .= '</div>';

			/* Shortcode classes | Animation | Layout */
			$classes = $animation != '' ? ' mpc-animation' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-pricing-column mpc-pricing-legend' . $classes . '" ' . $animation . '>';
				$return .= $title . $price . $props;
			$return .= '</div>';

			$MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'legend' ] = $return;

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$css = '<style>' . $styles[ 'css' ] . '</style>';

				return $css;
			}

			return;
		}


		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc-pricing-legend-' . rand( 1, 100 ) );
			$style = '';

			if ( $temp_style = MPC_CSS::background( $styles, 'prop_even' ) ) {
				$style .= '.mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__property:nth-child(even) {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::font( $styles, 'properties' ) ) {
				$style .= '.mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__property {';
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
			if ( !function_exists( 'vc_map' ) ) {
				return;
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
			);

			/* Properties List */
			$properties = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'List of Properties', 'mpc' ),
					'param_name' => 'properties_divider',
				),
				array(
					'type'        => 'mpc_split',
					'heading'     => __( 'Properties', 'mpc' ),
					'param_name'  => 'properties',
					'admin_label' => true,
					'tooltip'     => __( 'Define properties values. Each new line will be a separate property.', 'mpc' ),
					'value'       => '',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Transform', 'mpc' ),
					'param_name'       => 'properties_font_transform',
					'tooltip'          => __( 'Select properties transform style.', 'mpc' ),
					'value'            => array(
						''                        => '',
						__( 'Capitalize', 'mpc' ) => 'capitalize',
						__( 'Small Caps', 'mpc' ) => 'small-caps',
						__( 'Uppercase', 'mpc' )  => 'uppercase',
						__( 'Lowercase', 'mpc' )  => 'lowercase',
						__( 'None', 'mpc' )       => 'none',
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'properties_font_align',
					'tooltip'          => __( 'Select properties alignment.', 'mpc' ),
					'value'            => array(
						''                     => '',
						__( 'Left', 'mpc' )    => 'left',
						__( 'Right', 'mpc' )   => 'right',
						__( 'Center', 'mpc' )  => 'center',
						__( 'Justify', 'mpc' ) => 'justify',
						__( 'Default', 'mpc' ) => 'inherit',
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'properties_font_color',
					'tooltip'          => __( 'Define properties color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$properties_even_background = MPC_Snippets::vc_background( array( 'prefix' => 'prop_even', 'subtitle' => __( 'Even Properties', 'mpc' ) ) );

			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,

				$properties,
				$properties_even_background,
				$class
			);

			return array(
				'name'            => __( 'Pricing Legend', 'mpc' ),
				'description'     => __( 'Pricing table legend', 'mpc' ),
				'base'            => 'mpc_pricing_legend',
				'as_child'        => array( 'only' => 'mpc_pricing_box' ),
				'content_element' => true,
//				'icon'            => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-pricing-box.png',
				'icon'            => 'mpc-shicon-pricing-legend',
				'category'        => __( 'Massive', 'mpc' ),
				'params'          => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Pricing_Legend' ) ) {
	global $MPC_Pricing_Legend;
	$MPC_Pricing_Legend = new MPC_Pricing_Legend;
}
