<?php
/**
 * Class Ultimate_Animator_Param
 *
 * @package Ultimate_Animator_Param.
 */

if ( ! class_exists( 'Ultimate_Animator_Param' ) ) {
	/**
	 * Class Ultimate_Animator_Param
	 *
	 * @class Ultimate_Animator_Param.
	 */
	class Ultimate_Animator_Param {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'animator', array( $this, 'animator_param' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'animator', array( $this, 'animator_param' ) );
				}
			}
		}
		/**
		 * Animator_param.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function animator_param( $settings, $value ) {
			$param_name   = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type         = isset( $settings['type'] ) ? $settings['type'] : '';
			$class        = isset( $settings['class'] ) ? $settings['class'] : '';
			$json         = ultimate_get_animation_json();
			$jsoniterator = json_decode( $json, true );

			$animators = '<select name="' . esc_attr( $param_name ) . '" class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . '">';

			foreach ( $jsoniterator as $key => $val ) {
				if ( is_array( $val ) ) {
					$labels     = str_replace( '_', ' ', $key );
					$animators .= '<optgroup label="' . ucwords( esc_attr__( $labels, 'ultimate_vc' ) ) . '">';
					foreach ( $val as $label => $style ) {
						$label = str_replace( '_', ' ', $label );
						if ( $label == $value ) {
							$animators .= '<option selected value="' . esc_attr( $label ) . '">' . esc_html__( $label, 'ultimate_vc' ) . '</option>';
						} else {
							$animators .= '<option value="' . esc_attr( $label ) . '">' . esc_html__( $label, 'ultimate_vc' ) . '</option>';
						}
					}
				} else {
					if ( $key == $value ) {
						$animators .= '<option selected value=' . esc_attr( $key ) . '>' . esc_html__( $key, 'ultimate_vc' ) . '</option>';
					} else {
						$animators .= '<option value=' . esc_attr( $key ) . '>' . esc_html__( $key, 'ultimate_vc' ) . '</option>';
					}
				}
			}
			$animators .= '<select>';

			$output  = '';
			$output .= '<div class="select_anim" style="width: 45%; float: left;">';
			$output .= $animators;
			$output .= '</div>';
			$output .= '<div class="anim_prev" style=" padding: 8px; width: 45%; float: left; text-align: center; margin-left: 15px;"> <span id="animate-me" style="padding: 15px; background: #1C8FCF; color: #FFF;">Animation Preview</span></div>';
			$output .= '<script type="text/javascript">
					jQuery(document).ready(function(){
						var animator = jQuery(".' . esc_attr( $param_name ) . '");
						var anim_target = jQuery("#animate-me");
						animator.on("change",function(){
							var anim = jQuery(this).val();
							anim_target.removeClass().addClass(anim + " animated").one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function(){jQuery(this).removeClass();
							});
						});
					});
				</script>';
			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_Animator_Param' ) ) {
	$ultimate_animator_param = new Ultimate_Animator_Param();
}
