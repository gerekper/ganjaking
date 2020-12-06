<?php
/**
 * Class Ultimate_Border
 *
 * @package Ultimate_Border.
 */

if ( ! class_exists( 'Ultimate_Border' ) ) {
	/**
	 * Class Ultimate_Border
	 *
	 * @class Ultimate_Border.
	 */
	class Ultimate_Border {

		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_border_param_scripts' ) );

			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_border', array( $this, 'ultimate_border_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-border.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_border', array( $this, 'ultimate_border_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-border.js' );
				}
			}
		}

		/**
		 * Ultimate_border_callback.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ultimate_border_callback( $settings, $value ) {
			$dependency    = '';
			$positions     = $settings['positions'];
			$enable_radius = isset( $settings['enable_radius'] ) ? $settings['enable_radius'] : true;
			$label         = isset( $settings['label_border'] ) ? $settings['label_border'] : 'Border Style';
			$unit          = isset( $settings['unit'] ) ? $settings['unit'] : 'px';

			$uid = 'ultimate-border-' . wp_rand( 1000, 9999 );

			$html  = '<div class="ultimate-border" id="' . esc_attr( $uid ) . '" data-unit="' . esc_attr( $unit ) . '" >';
			$html .= '<div class="ultimate-border-style-section">';
			$html .= '    <div class="ultimate-border-select-block">';
			$html .= '        <select data-placeholder="Border Style" class="ultimate-border-style-selector" >';
			$html .= '            <option value="none">' . __( 'None', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="solid">' . __( 'Solid', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="dotted">' . __( 'Dotted', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="dashed">' . __( 'Dashed', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="hidden">' . __( 'Hidden', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="double">' . __( 'Double', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="groove">' . __( 'Groove', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="ridge">' . __( 'Ridge', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="inset">' . __( 'Inset', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="outset">' . __( 'Outset', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="initial">' . __( 'Initial', 'ultimate_vc' ) . '</option>';
			$html .= '            <option value="inherit">' . __( 'Inherit', 'ultimate_vc' ) . '</option>';
			$html .= '        </select>';
			$html .= '    </div>';
			$html .= '</div>';

			/**    BORDER - {WIDTH}
			 *---------------------------------------------------*/
			$label = 'Border Width';
			if ( isset( $settings['label_width'] ) && '' != $settings['label_width'] ) {
				$label = $settings['label_width']; }
			$html .= '<div class="ultimate-four-input-section ultb-width-section" >';
			$html .= '    <div class="label">';
			$html .= esc_html( $label );
			$html .= '    </div>';
			$html .= '<div class="ult-expand ">  <span class="ult-tooltip">Expand / Collapse</span>  <i class="dashicons dashicons-minus"></i></div>';
			foreach ( $positions as $key => $default_value ) {
				switch ( $key ) {
					case 'Top':
							$id       = 'border-' . strtolower( $key ) . '-width';
							$dashicon = 'dashicons dashicons-arrow-up-alt';
							$html    .= $this->ultimate_border_param_item( $dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id );
						break;
					case 'Right':
							$id       = 'border-' . strtolower( $key ) . '-width';
							$dashicon = 'dashicons dashicons-arrow-right-alt';
							$html    .= $this->ultimate_border_param_item( $dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id );
						break;
					case 'Bottom':
							$id       = 'border-' . strtolower( $key ) . '-width';
							$dashicon = 'dashicons dashicons-arrow-down-alt';
							$html    .= $this->ultimate_border_param_item( $dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id );
						break;
					case 'Left':
							$id       = 'border-' . strtolower( $key ) . '-width';
							$dashicon = 'dashicons dashicons-arrow-left-alt';
							$html    .= $this->ultimate_border_param_item( $dashicon, /*$mode,*/ $unit, /*$default_value,*/$default_value, $key, $id );
						break;
				}
			}

			// {all} - border width.
			$html .= '  <div class="ultimate-border-input-block ultb-width-all">';
			$html .= '    <span class="ultimate-border-icon">';
			$html .= '      <i class="dashicons dashicons-editor-expand"></i>';
			$html .= '    </span>';
			$html .= '    <input type="text" class="ultimate-border-inputs ultimate-border-input" data-unit="' . esc_attr( $unit ) . '" data-default="' . esc_attr( $default_value ) . '" data-id="border-width" placeholder="all" />';
			$html .= '  </div>';

			$html .= '<div class="ultimate-unit-section">';

			$html .= '  <select class="ult-unit-border-width" >';
			switch ( $unit ) {
				case 'px':
					$html     .= '  <option value="px" selected>px</option>';
						$html .= '  <option value="em">em</option>';
					break;
				case 'em':
					$html     .= '  <option value="em" selected>em</option>';
						$html .= '  <option value="px">px</option>';
					break;
			}
			$html .= '  </select>';
			$html .= '</div>';
			$html .= '</div><!-- .ultimate-four-input-section -->';

			if ( $enable_radius ) :
				$label = 'Border Radius';
				if ( isset( $settings['label_radius'] ) && '' != $settings['label_radius'] ) {
					$label = $settings['label_radius']; }
				$html .= '<div class="ultimate-border-radius-block ultb-radius-section" >';
				$html .= '    <div class="label">';
				$html .= esc_html( $label );
				$html .= '    </div>';
				$html .= '  <div class="ult-expand ">  <span class="ult-tooltip">Expand / Collapse</span>  <i class="dashicons dashicons-minus"></i></div>';

				$radius = $settings['radius'];
				foreach ( $radius as $key => $default_value ) {

					switch ( $key ) {
						case 'Top Left':
							$key             = 'top-left-radius';
								$dashicon    = 'dashicons dashicons-arrow-up-alt';
								$placeholder = 'T. Left';
								$html       .= $this->ultimate_border_radius_item( $dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder );
							break;
						case 'Top Right':
							$key             = 'top-right-radius';
								$dashicon    = 'dashicons dashicons-arrow-right-alt';
								$placeholder = 'T. Right';
								$html       .= $this->ultimate_border_radius_item( $dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder );
							break;
						case 'Bottom Right':
							$key             = 'bottom-right-radius';
								$dashicon    = 'dashicons dashicons-arrow-down-alt';
								$placeholder = 'B. Right';
								$html       .= $this->ultimate_border_radius_item( $dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder );
							break;
						case 'Bottom Left':
							$key             = 'bottom-left-radius';
								$dashicon    = 'dashicons dashicons-arrow-left-alt';
								$placeholder = 'B. Left';
								$html       .= $this->ultimate_border_radius_item( $dashicon, /*$mode,*/ $unit, $default_value, /*$default_value,*//*$default_value,*/ $key, $placeholder );
							break;
					}
				}

				// {all} - border radius.
				$html .= '  <div class="ultimate-border-input-block ultb-radius-all">';
				$html .= '    <span class="ultimate-border-icon">';
				$html .= '      <i class="dashicons dashicons-editor-expand"></i>';
				$html .= '    </span>';
				$html .= '    <input type="text" class="ultimate-border-inputs ultimate-border-input" data-unit="' . esc_attr( $unit ) . '" data-default="' . esc_attr( $default_value ) . '" data-id="border-radius" placeholder="all" />';
				$html .= '  </div>';

				$html .= '<div class="ultimate-unit-section">';

				$html .= '  <select class="ult-unit-border-radius" >';
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

				$html .= '</div>';
			endif;

			// add color picker.
			$label = 'Border Color';
			if ( isset( $settings['label_color'] ) && '' != $settings['label_color'] ) {
				$label = $settings['label_color']; }
			$html .= '  <div class="ultimate-colorpicker-section">';
			$html .= '    <div class="label">';
			$html .= esc_html( $label );
			$html .= '    </div>';
			$html .= '    <div class="ultimate-colorpicker-block">';
			$html .= '      <input name="" class="ultimate-colorpicker cs-wp-color-picker" data-id="border-color" type="text" value="" />';
			$html .= '    </div>';
			$html .= '  </div>';

			$html .= '  <input type="hidden" data-unit="' . esc_attr( $unit ) . '" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ultimate-border-value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . ' />';
			$html .= '</div>';
			return $html;
		}
		/**
		 * Ultimate_border_radius_item.
		 *
		 * @param string $dashicon Dashicon.
		 * @param string $unit Unit.
		 * @param string $default_value Default_value.
		 * @param string $key Key.
		 * @param string $placeholder Placeholder.
		 */
		public function ultimate_border_radius_item( $dashicon, /*$mode,*/ $unit, /* $default_value,*/ $default_value, $key, $placeholder ) {
			$html  = '  <div class="ultimate-border-radius ultb-radius-single">';
			$html .= '    <span class="ultimate-border-icon">';
			$html .= '      <i class="' . $dashicon . '"></i>';
			$html .= '    </span>';
			$html .= '    <input type="text" class="ultimate-border-inputs ultimate-border-input" data-unit="' . esc_attr( $unit ) . '" data-default="' . esc_attr( $default_value ) . '" data-id="border-' . strtolower( esc_attr( $key ) ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
			$html .= '  </div>';
			return $html;
		}
		/**
		 * Ultimate_border_param_item.
		 *
		 * @param string $dashicon Dashicon.
		 * @param string $unit Unit.
		 * @param string $default_value Default_value.
		 * @param string $key Key.
		 * @param string $id ID.
		 */
		public function ultimate_border_param_item( $dashicon, /*$mode,*/ $unit, /* $default_value,*/ $default_value, $key, $id ) {
			$html  = '  <div class="ultimate-border-input-block ultb-width-single">';
			$html .= '    <span class="ultimate-border-icon">';
			$html .= '      <i class="' . esc_attr( $dashicon ) . '"></i>';
			$html .= '    </span>';
			$html .= '    <input type="text" class="ultimate-border-inputs ultimate-border-input" data-unit="' . esc_attr( $unit ) . '" data-default="' . esc_attr( $default_value ) . '" data-id="' . esc_attr( $id ) . '" placeholder="' . esc_attr( $key ) . '" />';
			$html .= '  </div>';
			return $html;
		}

		/**
		 * Ultimate_border_param_scripts.
		 *
		 * @param string $hook Hook.
		 */
		public function ultimate_border_param_scripts( $hook ) {
			wp_register_style( 'ultimate-border-style', UAVC_URL . 'admin/vc_extend/css/ultimate_border.css', null, ULTIMATE_VERSION );

			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_style( 'ultimate-border-style' );
					wp_enqueue_style( 'ultimate-chosen-style' );

					wp_enqueue_script( 'ultimate-chosen-script' );
				}
			}
		}
	}
}
if ( class_exists( 'Ultimate_Border' ) ) {
	$ultimate_border = new Ultimate_Border();
}
