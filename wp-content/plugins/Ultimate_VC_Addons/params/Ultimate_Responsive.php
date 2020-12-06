<?php
/**
 * // Param Use -
 *   array(
 *         "type" => "ultimate_responsive",
 *         "class" => "",
 *         "heading" => __("Font size", 'ultimate_vc'),
 *         "param_name" => "YOUR_PARAM_NAME_FONT_SIZE",
 *         "unit"  => "px",                              // use '%' or 'px'
 *         "media" => array(
 *             // "Large Screen"      => '',
 *             "Desktop"           => '28',              // Here '28' is default value set for 'Desktop'
 *             "Tablet"            => '',
 *             "Tablet Portrait"   => '',
 *             "Mobile Landscape"  => '',
 *             "Mobile"            => '',
 *         ),
 *         "group" => "Typography"
 *   ),
 *
 * Module implementation -
 *
 *   1]  Create Data List -
 *       $args = array(
 *           'target'      =>  '#ID .TARGET_element_CLASS, #ID #TARGET_element_ID',  // set targeted element e.g. unique *class/id etc.
 *              'media_sizes' => array(
 *               font-size' => $YOUR_PARAM_NAME_FONT_SIZE,       //  Your PARAM_NAME which you set in array
 *              'line-height' => $YOUR_PARAM_NAME_LINE_HEIGHT    //  Your PARAM_NAME which you set in array
 *           ),
 *          );
 *       $data_list = get_ultimate_vc_responsive_media_css($args);
 *
 *   2] Add ID and class 'ult-responsive' and set data attribute - $data_list to targeted element
 *       <div id="#ID" class='ult-responsive' '.$data_list.' >
 *           <div class="TARGET_element_ID">
 *               ...
 *           </div>
 *           <div class="TARGET_element_CLASS">
 *               ...
 *           </div>
 *           ....
 *         </div>
 *
 * Note - Without .ult-responsive class on target resposive param will not work
 *
 * @package Ultimate_Responsive.
 */

if ( ! class_exists( 'Ultimate_Responsive' ) ) {
	/**
	 * Class Ultimate_Responsive
	 *
	 * @class Ultimate_Responsive.
	 */
	class Ultimate_Responsive {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_admin_responsive_param_scripts' ) );

			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_responsive', array( $this, 'ultimate_responsive_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-responsive.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_responsive', array( $this, 'ultimate_responsive_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-responsive.js' );
				}
			}
		}
		/**
		 * Ultimate_responsive_callback.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ultimate_responsive_callback( $settings, $value ) {
			$dependency = '';
			$unit       = $settings['unit'];
			$medias     = $settings['media'];

			if ( is_numeric( $value ) ) {
				$value = 'desktop:' . $value . 'px;';
			}

			$uid = 'ultimate-responsive-' . wp_rand( 1000, 9999 );

			$html      = '<div class="ultimate-responsive-wrapper" id="' . $uid . '" >';
				$html .= '  <div class="ultimate-responsive-items" >';

			foreach ( $medias as $key => $default_value ) {
				switch ( $key ) {
					case 'Desktop':
						$class    = 'required';
						$data_id  = strtolower( ( preg_replace( '/\s+/', '_', $key ) ) );
						$dashicon = "<i class='dashicons dashicons-desktop'></i>";
						$icons    = 'simplify-icon dashicons dashicons-arrow-right-alt2';
						if ( is_rtl() ) {
							$icons = 'simplify-icon dashicons dashicons-arrow-left-alt2';
						}
						$html .= $this->ultimate_responsive_param_media( $class, $dashicon, $key, $default_value, $unit, $data_id );
						$html .= "<div class='simplify'>
										<div class='ult-tooltip simplify-options'>" . __( 'Responsive Options', 'ultimate_vc' ) . "</div>
										<i class='$icons'></i>
									  </div>";
						break;
					case 'Tablet':
						$class    = 'optional';
						$data_id  = strtolower( ( preg_replace( '/\s+/', '_', $key ) ) );
						$dashicon = "<i class='dashicons dashicons-tablet' style='transform: rotate(90deg);'></i>";
						$html    .= $this->ultimate_responsive_param_media( $class, $dashicon, $key, $default_value, $unit, $data_id );
						break;
					case 'Tablet Portrait':
						$class    = 'optional';
						$data_id  = strtolower( ( preg_replace( '/\s+/', '_', $key ) ) );
						$dashicon = "<i class='dashicons dashicons-tablet'></i>";
						$html    .= $this->ultimate_responsive_param_media( $class, $dashicon, $key, $default_value, $unit, $data_id );
						break;
					case 'Mobile Landscape':
						$class    = 'optional';
						$data_id  = strtolower( ( preg_replace( '/\s+/', '_', $key ) ) );
						$dashicon = "<i class='dashicons dashicons-smartphone' style='transform: rotate(90deg);'></i>";
						$html    .= $this->ultimate_responsive_param_media( $class, $dashicon, $key, $default_value, $unit, $data_id );
						break;
					case 'Mobile':
						$class    = 'optional';
						$data_id  = strtolower( ( preg_replace( '/\s+/', '_', $key ) ) );
						$dashicon = "<i class='dashicons dashicons-smartphone'></i>";
						$html    .= $this->ultimate_responsive_param_media( $class, $dashicon, $key, $default_value, $unit, $data_id );
						break;
				}
			}
			$html .= '  </div>';
			$html .= $this->get_units( $unit );
			$html .= '  <input type="hidden" data-unit="' . esc_attr( $unit ) . '"  name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ultimate-responsive-value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . ' />';

			$html .= '</div>';

			return $html;
		}
		/**
		 * Ultimate_responsive_param_media.
		 *
		 * @param string $class Class.
		 * @param string $dashicon Dashicon.
		 * @param string $key Key.
		 * @param string $default_value Default_value.
		 * @param string $unit Unit.
		 * @param string $data_id Data ID.
		 */
		public function ultimate_responsive_param_media( $class, $dashicon, $key, $default_value, $unit, $data_id ) {
			$tooltipval = str_replace( '_', ' ', $data_id );
			$html       = '  <div class="ult-responsive-item ' . esc_attr( $class ) . ' ' . esc_attr( $data_id ) . ' ">';
			$html      .= '    <span class="ult-icon">';
			$html      .= '    	<div class="ult-tooltip ' . esc_attr( $class ) . ' ' . esc_attr( $data_id ) . '">' . ucwords( $tooltipval ) . '</div>';
			$html      .= $dashicon;
			$html      .= '     </span>';
			$html      .= '    <input type="text" class="ult-responsive-input" data-default="' . esc_attr( $default_value ) . '" data-unit="' . esc_attr( $unit ) . '" data-id="' . esc_attr( $data_id ) . '" />';
			$html      .= '  </div>';
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
			$html .= '  <label>' . esc_html( $unit ) . '</label>';
			$html .= '</div>';
			return $html;
		}
		/**
		 * Ultimate_admin_responsive_param_scripts.
		 *
		 * @param string $hook Hook.
		 */
		public function ultimate_admin_responsive_param_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				wp_enqueue_style( 'wp-color-picker' );
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {

					$css_ext = '.css';
					if ( is_rtl() ) {
						$css_ext = '-rtl.css';
					}
					Ultimate_VC_Addons::ultimate_register_style( 'ultimate_responsive_param_css', UAVC_URL . 'admin/vc_extend/css/ultimate_responsive' . $css_ext, true );
					wp_enqueue_style( 'ultimate_responsive_param_css' );
				}
			}
		}
	}
}

if ( class_exists( 'Ultimate_Responsive' ) ) {
	$ultimate_responsive = new Ultimate_Responsive();
}
