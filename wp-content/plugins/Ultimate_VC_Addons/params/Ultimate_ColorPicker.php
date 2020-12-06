<?php
/**
 * Class Ultimate_ColorPicker_Param
 *
 * @package Ultimate_ColorPicker_Param.
 */

if ( ! class_exists( 'Ultimate_ColorPicker_Param' ) ) {
	/**
	 * Class Ultimate_ColorPicker_Param
	 *
	 * @class Ultimate_ColorPicker_Param.
	 */
	class Ultimate_ColorPicker_Param {
		/**
		 * Initiator.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'colorpicker_alpha', array( $this, 'colorpicker_alpha_gen' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'colorpicker_alpha', array( $this, 'colorpicker_alpha_gen' ) );
				}
			}
		}
		/**
		 * Color Picker Alpha Generate.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function colorpicker_alpha_gen( $settings, $value ) {
			$base       = '';
			$opacity    = '';
			$output     = '';
			$dependency = '';
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type       = isset( $settings['type'] ) ? $settings['type'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			$uni        = uniqid( 'colorpicker-' . wp_rand() );
			if ( '' != $value ) {
				$arr_v = explode( ',', $value );
				if ( is_array( $arr_v ) ) {
					if ( isset( $arr_v[1] ) ) {
						$opacity = $arr_v[1];
					}
					if ( isset( $arr_v[0] ) ) {
						$base = $arr_v[0];
					}
				}
			}
			$output  = '
                <input id="alpha_val' . esc_attr( $uni ) . '" class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . ' vc_column_alpha" value="' . esc_attr( $value ) . '" ' . $dependency . ' data-uniqid="' . esc_attr( $uni ) . '" data-opacity="' . esc_attr( $opacity ) . '" data-hex-code="' . esc_attr( $base ) . '"/>
';
			$output .= '
<input class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . '" ' . $dependency . ' name="' . esc_attr( $param_name ) . '" value="' . esc_attr( $value ) . '" style="display:none"/>
<button class="alpha_clear" type="button">' . __( 'Clear', 'ultimate_vc' ) . '</button>
';
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					function colorpicker_alpha(selector,id_prefix){
						jQuery(selector).each(function(){
							var aid = jQuery(this).data('uniqid');
							jQuery(id_prefix+aid).minicolors({
								change: function(hex, opacity) {
									console.log(hex+','+opacity);
									jQuery(this).parent().next().val(hex+','+opacity);
									console.log(jQuery(this).parent().next().attr('class'))
								},
								opacity: true,
								defaultValue: jQuery(this).data('hex-code'),
								position: 'default',
							});
							jQuery('.alpha_clear').click(function(){
								jQuery(this).parent().find('input').val('');
								jQuery(this).parent().find('.minicolors-swatch-color').css('background-color','');
								//$select.val('');
								//jQuery(id_prefix+aid).val('');
								//jQuery(id_prefix+aid).next().find('.minicolors-swatch-color').css('background-color','');
							})
						});
					}
					colorpicker_alpha('.vc_column_alpha','#alpha_val');
				})
				</script>
			<?php
			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_ColorPicker_Param' ) ) {
	$ultimate_colorpicker_param = new Ultimate_ColorPicker_Param();
}
