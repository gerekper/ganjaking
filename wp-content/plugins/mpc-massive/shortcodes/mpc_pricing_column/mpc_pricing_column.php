<?php
/*----------------------------------------------------------------------------*\
	PRICING COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/

if ( !class_exists( 'MPC_Pricing_Column' ) ) {
	class MPC_Pricing_Column {
		public $shortcode = 'mpc_pricing_column';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_pricing_column', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_pricing_column-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pricing_column/css/mpc_pricing_column.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_pricing_column-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pricing_column/js/mpc_pricing_column' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Shortcode, $MPC_Button, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                 => '',
				'preset'                => '',

				/* General */
				'background_type'                 => 'color',
				'background_color'                => '',
				'background_image'                => '',
				'background_image_size'           => 'large',
				'background_repeat'               => 'no-repeat',
				'background_size'                 => 'initial',
				'background_position'             => 'middle-center',
				'background_gradient'             => '#83bae3||#80e0d4||0;100||180||linear',

				/* Title */
				'title'                           => '',
				'title_color'                     => '',

				/* Price */
				'price'                           => '',
				'price_color'                     => '',

				/* Button */
				'mpc_button__title'               => '',
				'mpc_button__url'                 => '',

				/* Properties */
				'properties'                      => '',
				'properties_color'                => '',
				'prop_even_background_type'       => 'color',
				'prop_even_background_color'      => '',
				'prop_even_background_image'      => '',
				'prop_even_background_image_size' => 'large',
				'prop_even_background_repeat'     => 'no-repeat',
				'prop_even_background_size'       => 'initial',
				'prop_even_background_position'   => 'middle-center',
				'prop_even_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				/* Featured */
				'featured'                        => __( 'Best Seller', 'mpc' ),
				'featured_padding_css'            => '',
				'featured_enable'                 => '',
				'featured_font_preset'            => '',
				'featured_font_color'             => '',
				'featured_font_size'              => '',
				'featured_font_line_height'       => '',
				'featured_font_align'             => '',
				'featured_font_transform'         => '',

				'featured_background_type'       => 'color',
				'featured_background_color'      => '',
				'featured_background_image'      => '',
				'featured_background_image_size' => 'large',
				'featured_background_repeat'     => 'no-repeat',
				'featured_background_size'       => 'initial',
				'featured_background_position'   => 'middle-center',
				'featured_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			/* Prepare */
			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];
			$MPC_Shortcode[ 'pricing' ][ 'style' ] .= $styles[ 'css' ];

			$animation = MPC_Parser::animation( $atts );
			$properties = explode( '|||', $atts[ 'properties' ] );

			/* Fonts Presets */
			$title_classes    = $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'title' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'title' ] : '';
			$price_classes    = $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'price' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'price' ] : '';
			$props_classes    = $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'prop' ] != '' ? ' mpc-typography--' . $MPC_Shortcode[ 'pricing' ][ 'presets' ][ 'prop' ] : '';
			$featured_classes = $atts[ 'featured_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'featured_font_preset' ] ) : '';

			/* Prepare Parts */
			$title    = $MPC_Shortcode[ 'pricing' ][ 'disable' ][ 'title' ] == '' ? '<div class="mpc-pricing__title' . $title_classes . '">' . $atts[ 'title' ] . '</div>' : '';
			$price    = $MPC_Shortcode[ 'pricing' ][ 'disable' ][ 'price' ] == '' ? '<div class="mpc-pricing__price' . $price_classes . '">' . $atts[ 'price' ] . '</div>' : '';
			$featured = $atts[ 'featured_enable' ] != '' ? '<div class="mpc-pricing__featured' . $featured_classes . '">' . $atts[ 'featured' ] . '</div>' : '';

			/* Prepare Button */
			$button_atts = MPC_Parser::shortcode( $atts, 'mpc_button_' );
			$button_atts = array_merge( $button_atts, $MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'button' ] );
			$button      = $button_atts[ 'disable' ] == '' ? '<div class="mpc-pricing__button">' . $MPC_Button->shortcode_template( $button_atts ) . '</div>' : '';

			$props = '<div class="mpc-pricing__properties' . $props_classes . '">';
				foreach( $properties as $property ) {
					$props .= '<div class="mpc-pricing__property">' . $property . '</div>';
				}
			$props .= '</div>';

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition'; // mpc-transition
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-pricing-column' . $classes . '" ' . $animation . '>';
				$return .= $featured . $title . $price . $props . $button;
			$return .= '</div>';

			$MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'columns' ] .= $return;

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$css = '<style>' . $styles[ 'css' ] . '</style>';

				return $css;
			}

			return '';
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc-pricing-column-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'featured_font_size' ] = $styles[ 'featured_font_size' ] != '' ? $styles[ 'featured_font_size' ] . ( is_numeric( $styles[ 'featured_font_size' ] ) ? 'px' : '' ) : '';

			if ( $temp_style = MPC_CSS::background( $styles ) ) {
				$style .= '.mpc-pricing-column[id="' . $css_id . '"] {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles, 'prop_even' ) ) {
				$style .= '.mpc-pricing-box .mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__property:nth-child(even) {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'title_color' ] ) {
				$style .= '.mpc-pricing-box .mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__title {';
					$style .= 'color:' . $styles[ 'title_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'price_color' ] ) {
				$style .= '.mpc-pricing-box .mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__price {';
					$style .= 'color:' . $styles[ 'price_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'properties_color' ] ) {
				$style .= '.mpc-pricing-box .mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__property {';
					$style .= 'color:' . $styles[ 'properties_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'featured_padding_css' ] ) { $inner_styles[] = $styles[ 'featured_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'featured' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles, 'featured' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-pricing-column[id="' . $css_id . '"] .mpc-pricing__featured {';
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

			/* Title Section */
			$title = array(
				array(
					'type'             => 'textfield',
					'param_name'       => 'title_disable',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-hidden',
				),
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Title', 'mpc' ),
					'param_name' => 'titles_divider',
					'dependency' => array( 'element' => 'title_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Title Text', 'mpc' ),
					'param_name'       => 'title',
					'admin_label'      => true,
					'tooltip'          => __( 'Define title text.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'       => array( 'element' => 'title_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'title_color',
					'tooltip'          => __( 'Choose title color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'title_disable', 'value_not_equal_to' => 'true' ),
				),
			);

			/* Price Section */
			$price = array(
				array(
					'type'             => 'textfield',
					'param_name'       => 'price_disable',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-hidden',
				),
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Price', 'mpc' ),
					'param_name' => 'price_divider',
					'dependency' => array( 'element' => 'price_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Price Value', 'mpc' ),
					'tooltip'          => __( 'Define price value.', 'mpc' ),
					'param_name'       => 'price',
					'admin_label'      => true,
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'       => array( 'element' => 'price_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'price_color',
					'tooltip'          => __( 'Choose price color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'price_disable', 'value_not_equal_to' => 'true' ),
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
					'tooltip'     => __( 'Define properties values. Each new line will be a separate property.', 'mpc' ),
					'value'       => '',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'properties_color',
					'tooltip'          => __( 'Define properties color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$properties_even_background = MPC_Snippets::vc_background( array( 'prefix' => 'prop_even', 'subtitle' => __( 'Even Properties', 'mpc' ), /*'group' => __( 'Elements', 'mpc' ),*/ ) );

			/* Button */
			$button = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Button', 'mpc' ),
					'param_name' => 'button_divider',
                ),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Button Text', 'mpc' ),
					'param_name'       => 'mpc_button__title',
					'admin_label'      => true,
					'tooltip'          => __( 'Define button text.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'Link', 'mpc' ),
					'param_name'       => 'mpc_button__url',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose target link for button.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
			);

			/* Featured Column  */
			$enable_featured = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Featured', 'mpc' ),
					'param_name'       => 'featured_enable',
					'tooltip'          => __( 'Check to display this column as featured. Featured column will have additional label making it more visible.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field mpc-no-wrap',
				),
			);
			$featured = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Label', 'mpc' ),
					'param_name'       => 'featured',
					'tooltip'          => __( 'Define featured label text.', 'mpc' ),
					'value'            => '',
					'placeholder'      => __( 'Best Seller', 'mpc' ),
					'dependency'       => array( 'element' => 'featured_enable', 'value' => 'true' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$featured_font       = MPC_Snippets::vc_font( array( 'prefix' => 'featured', 'subtitle' => __( 'Featured', 'mpc' ), 'dependency' => array( 'element' => 'featured_enable', 'value' => 'true' ), ) );
			$featured_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'featured', 'subtitle' => __( 'Featured', 'mpc' ), 'dependency' => array( 'element' => 'featured_enable', 'value' => 'true' ), ) );
			$featured_background = MPC_Snippets::vc_background( array( 'prefix' => 'featured', 'subtitle' => __( 'Featured', 'mpc' ), 'dependency' => array( 'element' => 'featured_enable', 'value' => 'true' ), ) );

			$background = MPC_Snippets::vc_background();

			$class = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,

				$enable_featured,
				$featured_font,
				$featured,
				$featured_background,
				$featured_padding,

				$title,
				$price,
				$button,

				$properties,
				$properties_even_background,

				$background,
				$class
			);

			return array(
				'name'            => __( 'Pricing Column', 'mpc' ),
				'description'     => __( 'Pricing table single column', 'mpc' ),
				'base'            => 'mpc_pricing_column',
				'as_child'        => array( 'only' => 'mpc_pricing_box' ),
				'content_element' => true,
//				'icon'            => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-pricing-box.png',
				'icon'            => 'mpc-shicon-pricing-column',
				'category'        => __( 'Massive', 'mpc' ),
				'params'          => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Pricing_Column' ) ) {
	$MPC_Pricing_Column = new MPC_Pricing_Column;
}