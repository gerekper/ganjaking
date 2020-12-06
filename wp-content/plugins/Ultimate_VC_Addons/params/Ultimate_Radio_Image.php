<?php
/**
 *  Array(
 *      'type' => 'radio_image_box',
 *      'options' => array(
 *          'image-1' => plugins_url('../assets/images/patterns/01.png',__FILE__),
 *          'image-2' => plugins_url('../assets/images/patterns/12.png',__FILE__),
 *      ),
 *      'useextension' => false, // if false it will use key as value instead file name. Eg - "image-1" instead "01.png"
 *      'css' => array(
 *          'width' => '40px',
 *          'height' => '35px',
 *          'background-repeat' => 'repeat',
 *          'background-size' => 'cover'
 *      ),
 *  )
 *
 * @package Ultimate_Radio_Image_Param.
 */

if ( ! class_exists( 'Ultimate_Radio_Image_Param' ) ) {
	/**
	 * Class Ultimate_Radio_Image_Param
	 *
	 * @class Ultimate_Radio_Image_Param.
	 */
	class Ultimate_Radio_Image_Param {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'radio_image_box', array( &$this, 'radio_image_settings_field' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'radio_image_box', array( &$this, 'radio_image_settings_field' ) );
				}
			}
		}
		/**
		 * Radio_image_settings_field.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function radio_image_settings_field( $settings, $value ) {
			$default_css  = array(
				'width'             => '25px',
				'height'            => '25px',
				'background-repeat' => 'repeat',
				'background-size'   => 'cover',
			);
			$dependency   = '';
			$param_name   = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type         = isset( $settings['type'] ) ? $settings['type'] : '';
			$options      = isset( $settings['options'] ) ? $settings['options'] : '';
			$css          = isset( $settings['css'] ) ? $settings['css'] : $default_css;
			$class        = isset( $settings['class'] ) ? $settings['class'] : '';
			$useextension = ( isset( $settings['useextension'] ) && '' != $settings['useextension'] ) ? $settings['useextension'] : 'true';
			$default      = isset( $settings['default'] ) ? $settings['default'] : 'transperant';

			$uni = uniqid();

			$output  = '';
			$output  = '<input id="radio_image_setting_val_' . esc_attr( $uni ) . '" class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . ' ' . esc_attr( $value ) . ' vc_ug_gradient" name="' . esc_attr( $param_name ) . '"  style="display:none"  value="' . esc_attr( $value ) . '" ' . $dependency . '/>';
			$output .= '<div class="ult-radio-image-box" data-uniqid="' . esc_attr( $uni ) . '">';
			if ( 'transperant' == $value ) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
				$output .= '<label>
					<input type="radio" name="radio_image_' . esc_attr( $uni ) . '" ' . $checked . ' class="radio_pattern_image" value="' . esc_attr( $default ) . '" />
					<span class="pattern-background no-bg" style="background:transperant;"></span>
				</label>';
			foreach ( $options as $key => $img_url ) {
				if ( $value == $key ) {
					$checked = 'checked';
				} else {
					$checked = '';
				}
				if ( 'true' != $useextension ) {
					$temp          = pathinfo( $key );
					$temp_filename = $temp['filename'];
					$key           = $temp_filename;
				}
				$output .= '<label>
						<input type="radio" name="radio_image_' . esc_attr( $uni ) . '" ' . $checked . ' class="radio_pattern_image" value="' . esc_attr( $key ) . '" />
						<span class="pattern-background" style="background:url(' . esc_url( $img_url ) . ')"></span>
					</label>';
			}
			$output .= '</div>';
			$output .= '<style>
				.ult-radio-image-box label > input{ /* HIDE RADIO */
					display:none;
				}
				.ult-radio-image-box label > input + img{ /* IMAGE STYLES */
					cursor:pointer;
				  	border:2px solid transparent;
				}
				.ult-radio-image-box .no-bg {
					border:2px solid #ccc;
				}
				.ult-radio-image-box label > input:checked + img, .ult-radio-image-box label > input:checked + .pattern-background{ /* (CHECKED) IMAGE STYLES */
				  	border:2px solid #f00;
				}
				.pattern-background {';
			foreach ( $css as $attr => $inine_style ) {
				$output .= $attr . ':' . $inine_style . ';';
			}
					$output .= 'display: inline-block;
					border:2px solid transparent;
				}
			</style>';
			$output         .= '<script type="text/javascript">
				jQuery(".radio_pattern_image").change(function(){
					var radio_id = jQuery(this).parent().parent().data("uniqid");
					var val = jQuery(this).val();
					jQuery("#radio_image_setting_val_"+radio_id).val(val);
				});
			</script>';
			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_Radio_Image_Param' ) ) {
	$ultimate_radio_image_param = new Ultimate_Radio_Image_Param();
}
