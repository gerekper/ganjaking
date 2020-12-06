<?php
/**
 * Class Ult_Spacing
 *
 * @package Ult_Spacing.
 */

if ( ! class_exists( 'Ult_Spacing' ) ) {
	/**
	 * Class Ult_Spacing
	 *
	 * @class Ult_Spacing.
	 */
	class Ult_Spacing {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_spacing_param_scripts' ) );

			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_spacing', array( $this, 'ultimate_spacing_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-spacing.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_spacing', array( $this, 'ultimate_spacing_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-spacing.js' );
				}
			}
		}
		/**
		 * Ultimate_spacing_callback.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ultimate_spacing_callback( $settings, $value ) {
			$dependency = '';
			$positions  = $settings['positions'];
			$mode       = $settings['mode'];

			$uid = 'ultimate-spacing-' . wp_rand( 1000, 9999 );
			if ( isset( $settings['unit'] ) ) {
				$unit = $settings['unit'];
			} else {
				$unit = ''; }

			$html = '<div class="ultimate-spacing" id="' . esc_attr( $uid ) . '" data-unit="' . esc_attr( $unit ) . '" >';

			// Expand / Collapse.
			$html .= '<div class="ult-spacing-expand">';
			$html .= '  <span class="ult-tooltip">Expand / Collapse</span>';
			$html .= '  <i class="dashicons dashicons-minus"></i>';
			$html .= '</div>';

			$html .= '<div class="ultimate-four-input-section" >';
			foreach ( $positions as $key => $default_value ) {
				switch ( $key ) {
					case 'Top':
							// add '-width' if mode equals 'spacing'.
							$dashicon = 'dashicons dashicons-arrow-up-alt';
							$html    .= $this->ultimate_spacing_param_item( $dashicon, $mode, $unit, /*$default_value,*/$default_value, $key );
						break;
					case 'Right':
							$dashicon = 'dashicons dashicons-arrow-right-alt';
							$html    .= $this->ultimate_spacing_param_item( $dashicon, $mode, $unit, /*$default_value,*/$default_value, $key );
						break;
					case 'Bottom':
							$dashicon = 'dashicons dashicons-arrow-down-alt';
							$html    .= $this->ultimate_spacing_param_item( $dashicon, $mode, $unit, /*$default_value,*/$default_value, $key );
						break;
					case 'Left':
							$dashicon = 'dashicons dashicons-arrow-left-alt';
							$html    .= $this->ultimate_spacing_param_item( $dashicon, $mode, $unit, /*$default_value,*/$default_value, $key );
						break;
				}
			}

			$html .= '<div class="ultimate-spacing-input-block ult-spacing-all " data-status="hide-all">
                      <span class="ultimate-spacing-icon"><i class="dashicons dashicons-editor-expand"></i></span>
                      <input type="text" class="ultimate-spacing-inputs ultimate-spacing-input" data-unit="' . esc_attr( $unit ) . '" data-default="" data-id="' . esc_attr( $mode ) . '" placeholder="All">
                    </div>';

			$html .= $this->get_units( $unit );
			$html .= '</div><!-- .ultimate-four-input-section -->';

			$html .= '  <input type="hidden" data-unit="' . esc_attr( $unit ) . '" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ultimate-spacing-value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . ' />';
			$html .= '</div>';
			return $html;
		}
		/**
		 * Ultimate_spacing_param_item.
		 *
		 * @param string $dashicon Dashicon.
		 * @param string $mode Mode.
		 * @param string $unit Unit.
		 * @param string $default_value Default_value.
		 * @param string $key Key.
		 */
		public function ultimate_spacing_param_item( $dashicon, $mode, $unit, /* $default_value,*/ $default_value, $key ) {
			$html  = '  <div class="ultimate-spacing-input-block ult-spacing-single">';
			$html .= '    <span class="ultimate-spacing-icon">';
			$html .= '      <i class="' . esc_attr( $dashicon ) . '"></i>';
			$html .= '    </span>';
			$html .= '    <input type="text" class="ultimate-spacing-inputs ultimate-spacing-input" data-unit="' . esc_attr( $unit ) . '" data-default="' . esc_attr( $default_value ) . '" data-id="' . esc_attr( $mode . '-' . strtolower( $key ) ) . '" placeholder="' . esc_attr( $key ) . '" />';
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
			$html  = '<div class="ultimate-unit-section">';
			$html .= '  <select data-placeholder="Select Unit" class="ult-unit-spacing" >';
			switch ( $unit ) {
				case 'px':
					$html .= '  <option value="px" selected>px</option>';
					$html .= '  <option value="em">em</option>';
					$html .= '  <option value="%">%</option>';
					break;
				case 'em':
					$html .= '  <option value="em" selected>em</option>';
					$html .= '  <option value="px">px</option>';
					$html .= '  <option value="%">%</option>';
					break;
				case '%':
					$html .= '  <option value="%" selected>%</option>';
					$html .= '  <option value="px">px</option>';
					$html .= '  <option value="em">em</option>';
					break;
			}
			$html .= '  </select>';
			$html .= '</div>';

			return $html;
		}
		/**
		 * Ultimate_spacing_param_scripts.
		 *
		 * @param string $hook Hook.
		 */
		public function ultimate_spacing_param_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_register_style( 'ultimate_spacing_css', UAVC_URL . 'admin/vc_extend/css/ultimate_spacing.css', null, ULTIMATE_VERSION );
					wp_enqueue_style( 'ultimate_spacing_css' );
				}
			}
		}
	}
}
if ( class_exists( 'Ult_Spacing' ) ) {
	$ult_spacing = new Ult_Spacing();
}
