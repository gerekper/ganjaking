<?php
/**
 * Class Ultimate_DateTime_Picker_Param
 *
 * @package Ultimate_DateTime_Picker_Param.
 */

if ( ! class_exists( 'Ultimate_DateTime_Picker_Param' ) ) {
	/**
	 * Class Ultimate_DateTime_Picker_Param
	 *
	 * @class Ultimate_DateTime_Picker_Param.
	 */
	class Ultimate_DateTime_Picker_Param {
		/**
		 * Initiator.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'datetimepicker', array( $this, 'datetimepicker' ), UAVC_URL . 'admin/js/bootstrap-datetimepicker.min.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'datetimepicker', array( $this, 'datetimepicker' ), UAVC_URL . 'admin/js/bootstrap-datetimepicker.min.js' );
				}
			}
		}
		/**
		 * Date time picker.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function datetimepicker( $settings, $value ) {
			$dependency = '';
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type       = isset( $settings['type'] ) ? $settings['type'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			$uni        = uniqid( 'datetimepicker-' . wp_rand() );
			$output     = '<div id="ult-date-time' . esc_attr( $uni ) . '" class="ult-datetime"><input data-format="yyyy/MM/dd hh:mm:ss" readonly class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . '" name="' . esc_attr( $param_name ) . '" style="width:258px;" value="' . esc_attr( $value ) . '" ' . $dependency . '/><div class="add-on" >  <i data-time-icon="Defaults-calendar-o" data-date-icon="Defaults-calendar-o"></i></div></div>';
			$output    .= '<script type="text/javascript">

				</script>';
			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_DateTime_Picker_Param' ) ) {
	$ultimate_datetime_picker_param = new Ultimate_DateTime_Picker_Param();
}
