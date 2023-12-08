<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder shortcodes
 *
 * @package WPBakeryPageBuilder
 * @since 7.0
 */

/**
 * Class WPBakeryShortCode_Vc_Pricing_Table
 * @since 7.0
 */
class WPBakeryShortCode_Vc_Pricing_Table extends WPBakeryShortCode {
	/**
	 * Template variables
	 * @var array
	 * @since 7.0
	 */
	protected $template_vars = array();

	/**
	 * Build templates where we are keeping element optionality
	 * @since 7.0
	 *
	 * @param array $atts
	 * @param string $content
	 * @throws Exception
	 */
	public function buildTemplate( $atts, $content ) {
		$output = array();
		$inline_css = array();

		$main_wrapper_classes = array( 'wpb-pricing-table' );

		if ( ! empty( $atts['el_class'] ) ) {
			$main_wrapper_classes[] = $atts['el_class'];
		}

		if ( ! empty( $atts['css_animation'] ) ) {
			$main_wrapper_classes[] = $this->getCSSAnimation( $atts['css_animation'] );
		}

		if ( ! empty( $atts['css'] ) ) {
			$main_wrapper_classes[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
			if ( ! empty( $atts['custom_background'] ) ) {
				$inline_css[] = vc_get_css_color( 'background-color', $atts['custom_background'] );
			}
		}

		$output['inline-css'] = $inline_css;

		$output['css-class'] = $main_wrapper_classes;

		$output['content'] = wpb_js_remove_wpautop( $content, true );

		$is_custom_heading = isset( $atts['use_custom_fonts_heading'] ) && 'true' === $atts['use_custom_fonts_heading'];
		$default_heading = '<h3 class="wpb-plan-title">' . esc_html( $atts['heading'] ) . '</h2>';
		$output['heading'] = $is_custom_heading ? $this->getHeading( 'heading', $atts ) : $default_heading;

		$is_custom_subheading = isset( $atts['use_custom_fonts_subheading'] ) && 'true' === $atts['use_custom_fonts_subheading'];
		$default_subheading = '<p class="wpb-plan-description">' . esc_html( $atts['subheading'] ) . '</p>';
		$output['subheading'] = $is_custom_subheading ? $this->getHeading( 'subheading', $atts ) : $default_subheading;

		$is_button_active = isset( $atts['add_button'] ) && 'yes' === $atts['add_button'];
		if ( $is_button_active ) {
			$output['button'] = $this->getButton( $atts );
		}

		$this->template_vars = $output;
	}

	/**
	 * Get custom element heading.
	 * @since 7.0
	 *
	 * @param string $tag
	 * @param array $atts
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getHeading( $tag, $atts ) {
		if ( isset( $atts[ $tag ] ) && '' !== trim( $atts[ $tag ] ) ) {
			$custom_heading = wpbakery()->getShortCode( 'vc_custom_heading' );
			$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, $tag . '_' );
			$data['font_container'] = implode( '|', array_filter( array(
				'tag:' . $tag,
				$data['font_container'],
			) ) );
			$data['text'] = $atts[ $tag ]; // provide text to shortcode

			return $custom_heading->render( array_filter( $data ) );
		}

		return '';
	}

	/**
	 * Get custom element button.
	 * @since 7.0
	 *
	 * @param array $atts
	 * @return string
	 * @throws Exception
	 */
	public function getButton( $atts ) {
		$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_btn', $atts, 'btn_' );
		if ( $data ) {
			$btn = wpbakery()->getShortCode( 'vc_btn' );
			if ( is_object( $btn ) ) {
				return  $btn->render( array_filter( $data ) );
			}
		}

		return '';
	}

	/**
	 * We keep some element setting here.
	 * @since 7.0
	 *
	 * @param string $string
	 * @return mixed|string
	 */
	public function getTemplateVariable( $string ) {
		if ( is_array( $this->template_vars ) && isset( $this->template_vars[ $string ] ) ) {

			return $this->template_vars[ $string ];
		}

		return '';
	}

	/**
	 * Some styles that depend on element setting we out them as inline.
	 * @since 7.0
	 *
	 * @param array $atts
	 * @param string $element_id
	 *
	 * @return string
	 */
	public function getInlineStyle( $atts, $element_id ) {
		$color = empty( $atts['markers_color'] ) ? '#5188F1' : str_replace( '#', '', $atts['markers_color'] );

		$style = '<style>';
		$style .= '#' . esc_attr( $element_id ) .  ' .wpb-plan-features li::before { ';
		$style .= ' content: ""; ';
		$style .= ' display: inline-block; ';
		$style .= ' margin: 0 10px 0 0;';
		$style .= ' width: 18px; ';
		$style .= ' height: 18px; ';
		$style .= ' vertical-align: middle; ';
		$style .= ' background: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'22px\' height=\'22px\' viewBox=\'0 0 22 22\' version=\'1.1\'%3E%3Cg id=\'Page-1\' stroke=\'none\' stroke-width=\'1\' fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg id=\'Artboard\' transform=\'translate(-472.000000, -546.000000)\' fill-rule=\'nonzero\'%3E%3Cg id=\'wpb-pricing-table-element\' transform=\'translate(445.000000, 222.000000)\'%3E%3Cg id=\'bullet\' transform=\'translate(27.000000, 324.000000)\'%3E%3Cpath d=\'M22,10.9999756 C22,17.0751668 17.0751778,22 11,22 C4.92487111,22 0,17.0751668 0,10.9999756 C0,4.92488206 4.92487111,0 11,0 C17.0751778,0 22,4.92488206 22,10.9999756 Z\' id=\'Path\' fill=\'%23'
			. $color .
			'\'/%3E%3Cpolygon id=\'Path\' fill=\'%23FFFFFF\' transform=\'translate(11.011123, 9.631788) rotate(-45.000000) translate(-11.011123, -9.631788) \' points=\'8.39375516 6.63178844 8.39375516 10.0231025 16.2371538 10.0231255 16.2371538 12.6317884 5.78509217 12.6317133 5.78509217 6.63178844\'/%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center no-repeat; ';
		$style .= ' background-size: 18px; ';
		$style .= '}</style>';

		return $style;
	}
}
