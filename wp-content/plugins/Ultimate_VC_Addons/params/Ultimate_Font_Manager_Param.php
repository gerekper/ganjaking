<?php
/**
 * Class Ultimate_Font_Manager_Param
 *
 * @package Ultimate_Font_Manager_Param.
 */

if ( ! class_exists( 'Ultimate_Font_Manager_Param' ) ) {
	/**
	 * Class Ultimate_Font_Manager_Param
	 *
	 * @class Ultimate_Font_Manager_Param.
	 */
	class Ultimate_Font_Manager_Param {
		/**
		 * Initiator.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_google_fonts', array( $this, 'ultimate_google_fonts_settings' ), UAVC_URL . 'admin/vc_extend/js/vc-google-fonts-param.js' );
					vc_add_shortcode_param( 'ultimate_google_fonts_style', array( $this, 'ultimate_google_fonts_style_settings' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_google_fonts', array( $this, 'ultimate_google_fonts_settings' ), UAVC_URL . 'admin/vc_extend/js/vc-google-fonts-param.js' );
					add_shortcode_param( 'ultimate_google_fonts_style', array( $this, 'ultimate_google_fonts_style_settings' ) );
				}
			}
		}
		/**
		 * Ultimate_google_fonts_settings.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ultimate_google_fonts_settings( $settings, $value ) {
			$dependency = '';
			$fonts      = get_option( 'ultimate_selected_google_fonts' );
			$html       = '<div class="ultimate_google_font_param_block">';
				$html  .= '<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value vc-ultimate-google-font ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . '/>';
				$html  .= '<select name="font_family" class="google-font-list">';
				$html  .= '<option value="">' . __( 'Default', 'ultimate_vc' ) . '</option>';
			if ( ! empty( $fonts ) ) :
				foreach ( $fonts as $key => $font ) {
					$selected = '';
					if ( $font['font_name'] == $value ) {
						$selected = 'selected';
					}
					$html .= '<option value="' . esc_attr( $font['font_name'] ) . '" ' . $selected . '>' . esc_html__( $font['font_name'], 'ultimate_vc' ) . '</option>';
				}
				endif;
				$html .= '</select>';
			$html     .= '</div>';
			return $html;
		}
		/**
		 * Ultimate_google_fonts_style_settings.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ultimate_google_fonts_style_settings( $settings, $value ) {
			$dependency = '';
			$html       = '<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ugfont-style-value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . '/>';
			$html      .= '<div class="ultimate_fstyle"></div>';
			return $html;
		}

	}
}

if ( class_exists( 'Ultimate_Font_Manager_Param' ) ) {
	$ultimate_font_manager_param = new Ultimate_Font_Manager_Param();
}
