<?php
/**
 * Class Ultimate_Switch_Param
 *
 * @package Ultimate_Switch_Param.
 */

if ( ! class_exists( 'Ultimate_Switch_Param' ) ) {
	/**
	 * Class Ultimate_Switch_Param
	 *
	 * @class Ultimate_Switch_Param.
	 */
	class Ultimate_Switch_Param {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ult_switch', array( $this, 'checkbox_param' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ult_switch', array( $this, 'checkbox_param' ) );
				}
			}
		}
		/**
		 * Checkbox_param.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function checkbox_param( $settings, $value ) {
			$dependency  = '';
			$param_name  = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type        = isset( $settings['type'] ) ? $settings['type'] : '';
			$options     = isset( $settings['options'] ) ? $settings['options'] : '';
			$class       = isset( $settings['class'] ) ? $settings['class'] : '';
			$default_set = isset( $settings['default_set'] ) ? $settings['default_set'] : false;
			$output      = '';
			$checked     = '';
			$un          = uniqid( 'ultswitch-' . wp_rand( 1000, 9999 ) );
			if ( is_array( $options ) && ! empty( $options ) ) {
				foreach ( $options as $key => $opts ) {
					if ( $value == $key ) {
						$checked = 'checked';
					} else {
						$checked = '';
					}
					$uid     = uniqid( 'ultswitchparam-' . wp_rand( 1000, 9999 ) );
					$output .= '<div class="ult-onoffswitch">
							<input type="checkbox" name="' . esc_attr( $param_name ) . '" value="' . esc_attr( $value ) . '" ' . $dependency . ' class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . ' ' . esc_attr( $dependency ) . ' ult-onoffswitch-checkbox chk-switch-' . esc_attr( $un ) . '" id="switch' . esc_attr( $uid ) . '" ' . $checked . '>
							<label class="ult-onoffswitch-label" for="switch' . esc_attr( $uid ) . '">
								<div class="ult-onoffswitch-inner">
									<div class="ult-onoffswitch-active">
										<div class="ult-onoffswitch-switch">' . esc_html( $opts['on'] ) . '</div>
									</div>
									<div class="ult-onoffswitch-inactive">
										<div class="ult-onoffswitch-switch">' . esc_html( $opts['off'] ) . '</div>
									</div>
								</div>
							</label>
						</div>';
					if ( isset( $opts['label'] ) ) {
						$lbl = $opts['label'];
					} else {
						$lbl = '';
					}
					$output .= '<div class="chk-label">' . $lbl . '</div><br/>';
				}
			}

			if ( $default_set ) {
				$set_value = 'off';
			} else {
				$set_value = '';
			}

			$output .= '<script type="text/javascript">
				jQuery("#switch' . esc_attr( $uid ) . '").change(function(){

					 if(jQuery("#switch' . esc_attr( $uid ) . '").is(":checked")){
						jQuery("#switch' . esc_attr( $uid ) . '").val("' . esc_attr( $key ) . '");
						jQuery("#switch' . esc_attr( $uid ) . '").attr("checked","checked");
					 } else {
						jQuery("#switch' . esc_attr( $uid ) . '").val("' . esc_attr( $set_value ) . '");
						jQuery("#switch' . esc_attr( $uid ) . '").removeAttr("checked");
					 }

				});
			</script>';

			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_Switch_Param' ) ) {
	$ultimate_switch_param = new Ultimate_Switch_Param();
}
