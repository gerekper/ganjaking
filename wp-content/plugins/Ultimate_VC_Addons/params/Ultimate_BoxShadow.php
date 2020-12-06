<?php
/**  # W3Schools
 *               - box-shadow: none|h-shadow v-shadow blur spread color |inset|initial|inherit;
 *
 *   How To?
 *
 *     array(
 *           "type" => "ultimate_boxshadow",
 *           "heading" => __("Box Shadow", "ultimate_vc"),
 *           "param_name" => "img_box_shadow",
 *           "unit"     => "px",                        //  [required] px,em,%,all     Default all
 *           "positions" => array(
 *             __("Horizontal","ultimate_vc")     => "",
 *             __("Vertical","ultimate_vc")   => "",
 *             __("Blur","ultimate_vc")  => "",
 *             __("Spread","ultimate_vc")    => ""
 *           ),
 *           "label_color"   => __("Shadow Color","ultimate_vc"),
 *           //"label_style" => __("Style","ultimate_vc"),
 *           "dependency" => Array("element" => "img_box_shadow_type", "value" => "on" ),
 *     ),
 *
 * @package Ultimate_BoxShadow.
 */

if ( ! class_exists( 'Ultimate_BoxShadow' ) ) {
	/**
	 * Ultimate_BoxShadow.
	 *
	 * @class Ultimate_BoxShadow.
	 */
	class Ultimate_BoxShadow {
		/**
		 *  Initiator.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_boxshadow', array( $this, 'ultimate_boxshadow_callback' ), UAVC_URL . 'admin/vc_extend/js/vc-box-shadow-param.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_boxshadow', array( $this, 'ultimate_boxshadow_callback' ), UAVC_URL . 'admin/vc_extend/js/vc-box-shadow-param.js' );
				}
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_boxshadow_param_scripts' ) );
		}
		/**
		 *  Box Shadow callback.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ultimate_boxshadow_callback( $settings, $value ) {

			$dependency   = '';
			$positions    = $settings['positions'];
			$enable_color = isset( $settings['enable_color'] ) ? $settings['enable_color'] : true;
			$unit         = isset( $settings['unit'] ) ? $settings['unit'] : 'px';

			$uid = 'ultimate-boxshadow-' . wp_rand( 1000, 9999 );

			$html = '<div class="ultimate-boxshadow" id="' . esc_attr( $uid ) . '" data-unit="' . esc_attr( $unit ) . '" >';

			// Box Shadow - Style.
			$label = 'Shadow Style';
			if ( isset( $settings['label_style'] ) && '' != $settings['label_style'] ) {
				$label = $settings['label_style']; }
			$html .= '<div class="ultbs-select-block">';
			$html .= '    <div class="ultbs-select-wrap">';
			$html .= '        <select class="ultbs-select" >';
			$html .= '            <option value="none">' . __( 'None', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="inherit">' . __( 'Inherit', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="inset">' . __( 'Inset', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="outset">' . __( 'Outset', 'ultimate_vc' ) . '</option>';
			$html .= '        </select>';
			$html .= '    </div>';
			$html .= '</div>';

			// BORDER - WIDTH.
			$html .= '<div class="ultbs-input-block" >';
			foreach ( $positions as $key => $default_value ) {
				switch ( $key ) {
					case 'Horizontal':
						$dashicon = 'dashicons dashicons-leftright';
						$html    .= $this->ultimate_boxshadow_param_item( $dashicon, $unit, $default_value, $key );
						break;
					case 'Vertical':
						$dashicon = 'dashicons dashicons-sort';
						$html    .= $this->ultimate_boxshadow_param_item( $dashicon, $unit, $default_value, $key );
						break;
					case 'Blur':
						$dashicon = 'dashicons dashicons-visibility';
						$html    .= $this->ultimate_boxshadow_param_item( $dashicon, $unit, $default_value, $key );
						break;
					case 'Spread':
						$dashicon = 'dashicons dashicons-location';
						$html    .= $this->ultimate_boxshadow_param_item( $dashicon, $unit, $default_value, $key );
						break;
				}
			}
			$html .= $this->get_units( $unit );
			$html .= '</div>';

			// Box Shadow - Color.
			if ( $enable_color ) {
				$label = 'Box Shadow Color';
				if ( isset( $settings['label_color'] ) && '' != $settings['label_color'] ) {
					$label = $settings['label_color']; }
				$html .= '  <div class="ultbs-colorpicker-block">';
				$html .= '    <div class="label wpb_element_label">';
				$html .= esc_html( $label );
				$html .= '    </div>';
				$html .= '    <div class="ultbs-colorpicker-wrap">';
				$html .= '      <input name="" class="ultbs-colorpicker cs-wp-color-picker" type="text" value="" />';
				$html .= '    </div>';
				$html .= '  </div>';
			}

			$html .= '  <input type="hidden" data-unit="' . esc_attr( $unit ) . '" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ultbs-result-value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . ' />';
			$html .= '</div>';
			return $html;
		}
		/**
		 * Ultimate_boxshadow_param_item.
		 *
		 * @param string $dashicon Dashicon.
		 * @param string $unit Unit.
		 * @param string $default_value Default_value.
		 * @param string $key Key.
		 */
		public function ultimate_boxshadow_param_item( $dashicon, /*$mode,*/ $unit, /* $default_value,*/ $default_value, $key ) {
			$html  = '  <div class="ultbs-input-wrap">';
			$html .= '    <span class="ultbs-icon">';
			$html .= '      <span class="ultbs-tooltip">' . esc_html( $key ) . '</span>';
			$html .= '      <i class="' . esc_attr( $dashicon ) . '"></i>';
			$html .= '    </span>';
			$html .= '    <input type="number" class="ultbs-input" data-unit="' . esc_attr( $unit ) . '" data-id="' . strtolower( esc_attr( $key ) ) . '" data-default="' . esc_attr( $default_value ) . '" placeholder="' . esc_attr( $key ) . '" />';
			$html .= '  </div>';
			return $html;
		}
		/**
		 * Get_units.
		 *
		 * @param string $unit Unit.
		 */
		public function get_units( $unit ) {
			// set units - px, em, %.
			$html  = '<div class="ultbs-unit">';
			$html .= '  <label>' . esc_html( $unit ) . '</label>';
			$html .= '</div>';
			return $html;
		}
		/**
		 * Ultimate_boxshadow_param_scripts.
		 *
		 * @param string $hook Hook.
		 */
		public function ultimate_boxshadow_param_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_enqueue_style( 'wp-color-picker' );

					wp_register_style( 'ultimate_boxshadow_param_css', UAVC_URL . 'admin/vc_extend/css/vc_param_boxshadow.css', null, ULTIMATE_VERSION );
					wp_enqueue_style( 'ultimate_boxshadow_param_css' );
				}
			}
		}
	}
}
if ( class_exists( 'Ultimate_BoxShadow' ) ) {
	$ultimate_boxshadow = new Ultimate_BoxShadow();
}
