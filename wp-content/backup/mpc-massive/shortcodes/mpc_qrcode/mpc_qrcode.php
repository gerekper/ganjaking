<?php
/*----------------------------------------------------------------------------*\
	QR CODE SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_QRCode' ) ) {
	class MPC_QRCode {
		public $shortcode = 'mpc_qrcode';

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

		/* Decode URL string */
		static function get_url( $url ) {
			$url = $url == '' ?: explode( '||', $url );
			$url = isset( $url[ 0 ] ) ? explode( 'url:', $url[ 0 ] ) : '';
			return isset( $url[ 1 ] ) ? urldecode( $url[ 1 ] ) : get_the_permalink();
		}

		/* Generate QR atts */
		static function get_qr_atts( $atts ) {
			$qr_atts = array(
				'text' => self::get_url( $atts[ 'url' ] ),
				'width' => $atts[ 'size' ],
				'height' => $atts[ 'size' ],
				'colorDark' => $atts[ 'dark' ],
				'colorLight' => $atts[ 'light' ],
			);

			return " data-qr='" . json_encode( $qr_atts ) . "'";
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-qrcode-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/qrcode.min.js', array(), '', true );

			global $MPC_Tooltip, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',

				'url'   => '',
				'size'  => '128',
				'dark'  => '#000000',
				'light' => '#ffffff',

				'border_css'  => '',
				'padding_css' => '',
				'margin_css'  => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				/* Tooltip */
				'mpc_tooltip__disable'               => '',

				'mpc_tooltip__preset'                => '',
				'mpc_tooltip__text'                  => '',
				'mpc_tooltip__trigger'               => 'hover',
				'mpc_tooltip__position'              => 'top',
				'mpc_tooltip__show_effect'           => '',
				'mpc_tooltip__disable_arrow'         => '',
				'mpc_tooltip__disable_hover'         => '',
				'mpc_tooltip__always_visible'         => '',
				'mpc_tooltip__enable_wide'           => '',

				'mpc_tooltip__font_preset'           => '',
				'mpc_tooltip__font_color'            => '',
				'mpc_tooltip__font_size'             => '',
				'mpc_tooltip__font_line_height'      => '',
				'mpc_tooltip__font_align'            => '',
				'mpc_tooltip__font_transform'        => '',

				'mpc_tooltip__padding_css'           => '',
				'mpc_tooltip__border_css'            => '',

				'mpc_tooltip__background_type'       => 'color',
				'mpc_tooltip__background_color'      => '',
				'mpc_tooltip__background_image'      => '',
				'mpc_tooltip__background_image_size' => 'large',
				'mpc_tooltip__background_repeat'     => 'no-repeat',
				'mpc_tooltip__background_size'       => 'initial',
				'mpc_tooltip__background_position'   => 'middle-center',
				'mpc_tooltip__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$tooltip      = $atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			$animation = MPC_Parser::animation( $atts );
			$qr_atts = self::get_qr_atts( $atts );

			$classes   = ' mpc-init';
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= $tooltip != '' ? ' mpc-tooltip-target' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = $tooltip != '' ? '<div class="mpc-tooltip-wrap" data-id="' . $css_id . '">' : '';
				$return .= '<div id="' . $css_id . '" class="mpc-qrcode' . $classes . '"' . $animation . $qr_atts . '></div>';
				$return .= $tooltip;
			$return .= $tooltip != '' ? '</div>' : '';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_qrcode-' . rand( 1, 100 ) );
			$style = '';

			$disabled_tooltip = $styles[ 'mpc_tooltip__disable' ] != '' || ( $styles[ 'mpc_tooltip__disable' ] == '' && $styles[ 'mpc_tooltip__text' ] == '' );

			// Add 'px'
			$styles[ 'size' ] = $styles[ 'size' ] != '' ? $styles[ 'size' ] . ( is_numeric( $styles[ 'size' ] ) ? 'px' : '' ) : '';

			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] && $disabled_tooltip ) { $inner_styles[] = $styles[ 'margin_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-qrcode[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'margin_css' ] && ! $disabled_tooltip ) {
				$style .= '.mpc-tooltip-wrap[data-id="' . $css_id . '"] {';
				$style .= $styles[ 'margin_css' ];
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
					'type'             => 'vc_link',
					'heading'          => __( 'Link', 'mpc' ),
					'param_name'       => 'url',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose a link to encode into QR code.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
//				array(
//					'type'             => 'colorpicker',
//					'heading'          => __( 'Dark Color', 'mpc' ),
//					'param_name'       => 'dark',
//					'tooltip'          => __( 'Select a color for dark elements of QR.', 'mpc' ),
//					'value'            => '#000000',
//					'edit_field_class' => 'vc_col-sm-4 vc_column',
//				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Light Color', 'mpc' ),
					'param_name'       => 'light',
					'tooltip'          => __( 'Select a color for light elements of QR.', 'mpc' ),
					'value'            => '#ffffff',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'size',
					'tooltip'          => __( 'Overwrite QR size in pixels.', 'mpc' ),
					'value'            => '',
					'label'            => 'px',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$integrate_tooltip = vc_map_integrate_shortcode( 'mpc_tooltip', 'mpc_tooltip__', __( 'Tooltip', 'mpc' ) );
			$disable_tooltip   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Tooltip', 'mpc' ),
					'param_name'       => 'mpc_tooltip__disable',
					'tooltip'          => __( 'Check to disable tooltip.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Tooltip', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_tooltip = array_merge( $disable_tooltip, $integrate_tooltip );

			return array(
				'name'        => __( 'QR Code', 'mpc' ),
				'description' => __( 'Display a code for QR Code Readers', 'mpc' ),
				'base'        => 'mpc_qrcode',
				'class'       => '',
				'icon'        => 'mpc-shicon-qrcode',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => array_merge( $base, $border, $padding, $margin, $animation, $class, $integrate_tooltip ),
			);
		}
	}
}
if ( class_exists( 'MPC_QRCode' ) ) {
	global $MPC_QRCode;
	$MPC_QRCode = new MPC_QRCode;
}
