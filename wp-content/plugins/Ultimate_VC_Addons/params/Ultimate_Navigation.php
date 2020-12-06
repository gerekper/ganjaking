<?php
/**
 * Use
 *
 *  1] Previous Icon
 *      Use param_name = prev_icon
 *
 *  2] Next Icon
 *      Use param_name = next_icon
 *
 *  3] Dots
 *  Use param_name = dots_icon
 *
 * @package Ultimate_Navigation.
 * */

if ( ! class_exists( 'Ultimate_Navigation' ) ) {
	/**
	 * Class Ultimate_Navigation
	 *
	 * @class Ultimate_Navigation.
	 */
	class Ultimate_Navigation {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_navigation', array( &$this, 'icon_settings_field' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_navigation', array( &$this, 'icon_settings_field' ) );
				}
			}
		}
		/**
		 * Icon_settings_field.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function icon_settings_field( $settings, $value ) {
			$dependency = '';
			$uid        = uniqid();
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type       = isset( $settings['type'] ) ? $settings['type'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			if ( 'next_icon' == $param_name ) {
				$icons = array( 'ultsl-arrow-right', 'ultsl-arrow-right2', 'ultsl-arrow-right3', 'ultsl-arrow-right4', 'ultsl-arrow-right6' );
			}
			if ( 'prev_icon' == $param_name ) {
				$icons = array( 'ultsl-arrow-left', 'ultsl-arrow-left2', 'ultsl-arrow-left3', 'ultsl-arrow-left4', 'ultsl-arrow-left6' );
			}

			if ( 'dots_icon' == $param_name ) {
				$icons = array( 'ultsl-checkbox-unchecked', 'ultsl-checkbox-partial', 'ultsl-stop', 'ultsl-radio-checked', 'ultsl-radio-unchecked', 'ultsl-record' );
			}
			$output  = '<input type="hidden" name="' . esc_attr( $param_name ) . '" class="wpb_vc_param_value ' . esc_attr( $param_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . '" value="' . esc_attr( $value ) . '" id="trace-' . esc_attr( $uid ) . '"/>';
			$output .= '<div id="icon-dropdown-' . esc_attr( $uid ) . '" >';
			$output .= '<ul class="icon-list">';
			$n       = 1;
			foreach ( $icons as $icon ) {
				$selected = ( $icon == $value ) ? 'class="selected"' : '';
				$id       = 'icon-' . $n;
				$output  .= '<li ' . $selected . ' data-ac-icon="' . esc_attr( $icon ) . '"><i class="ult-icon ' . esc_attr( $icon ) . '"></i><label class="ult-icon">' . esc_html( $icon ) . '</label></li>';
				$n++;
			}
			$output .= '</ul>';
			$output .= '</div>';
			$output .= '<script type="text/javascript">
					jQuery("#icon-dropdown-' . esc_attr( $uid ) . ' li").click(function() {
						jQuery(this).attr("class","selected").siblings().removeAttr("class");
						var icon = jQuery(this).attr("data-ac-icon");
						jQuery("#trace-' . esc_attr( $uid ) . '").val(icon);
						jQuery(".icon-preview-' . esc_attr( $uid ) . '").html("<i class=\'ult-icon "+icon+"\'></i>");
					});
			</script>';
			$output .= '<style type="text/css">';
			$output .= 'ul.icon-list li {
							display: inline-block;
							float: left;
							padding: 5px;
							border: 1px solid #ddd;
							font-size: 18px;
							width: 18px;
							height: 18px;
							line-height: 18px;
							margin: 0 auto;
						}
						ul.icon-list li label.ult-icon {
							display: none;
						}
						.ult-icon-preview {
							padding: 5px;
							font-size: 24px;
							border: 1px solid #ddd;
							display: inline-block;
						}
						ul.icon-list li.selected {
							background: #3486D1;
							padding: 10px;
							margin: 0 -1px;
							margin-top: -7px;
							color: #fff;
							font-size: 24px;
							width: 24px;
							height: 24px;
						}';
			$output .= '</style>';
			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_Navigation' ) ) {
	$ultimate_navigation = new Ultimate_Navigation();
}
