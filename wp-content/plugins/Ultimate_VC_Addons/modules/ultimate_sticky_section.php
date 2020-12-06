<?php
/**
 * Add-on Name: Sticky Section
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Sticky Section
 */

if ( ! class_exists( 'Ultimate_Sticky_Section' ) ) {
	/**
	 * Function that initializes Sticky Section Module.
	 *
	 * @class Ultimate_Sticky_Section
	 */
	class Ultimate_Sticky_Section {
		/**
		 * Constructor function that constructs default values for the Sticky Section module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'sticky_shortcode_mapper' ) );
			}
			add_shortcode( 'ult_sticky_section', array( $this, 'sticky_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ult_sticky_section_scripts' ), 1 );
		} /* Contructor End*/

		/**
		 * Render function for Sticky Section Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function sticky_shortcode( $atts, $content = null ) {
				$ult_sticky_settings = shortcode_atts(
					array(
						'el_class'            => '',
						'sticky_gutter'       => '',
						'sticky_offset_class' => '',

						'sticky_width'        => '',
						'sticky_custom_width' => '',

						'stick_behaviour'     => '',
						'sticky_position'     => '',
						'sticky_position_lr'  => 'left',
						'permanent_lr'        => '',
						'btn_mobile'          => '',
						'btn_support'         => '',

					),
					$atts
				);

			$sticky_classes_data  = array();
			$sticky_gutter_class  = " data-sticky_gutter_class= '";
			$sticky_gutter_id     = " data-sticky_gutter_id= '";
			$class_flag           = -1;
			$id_flag              = -1;
			$stick_behaviour_data = '';
			$left_right_postion   = '';
			$data_mobile          = '';
			$data_support         = '';
			if ( 'enable' == $ult_sticky_settings['btn_mobile'] ) {
				$data_mobile = " data-mobile='yes'";
			} else {
				$data_mobile = " data-mobile='no'";
			}

			if ( 'enable' == $ult_sticky_settings['btn_support'] ) {
				$data_support = " data-support='yes'";
			} else {
				$data_support = " data-support='no'";
			}

			// gutter classes explode.
			if ( '' != $ult_sticky_settings['sticky_offset_class'] ) {
				if ( strpos( $ult_sticky_settings['sticky_offset_class'], ',' ) !== false ) {
					$sticky_classes_data = explode( ',', $ult_sticky_settings['sticky_offset_class'] );
					foreach ( $sticky_classes_data as $data ) {
						if ( strpos( $data, '.' ) !== false ) {
							$class_flag = 0;

							$sticky_gutter_class .= trim( $data );
							$sticky_gutter_class .= ' ';
						} elseif ( strpos( $data, '#' ) !== false ) {
							$id_flag = 0;

							$sticky_gutter_id .= trim( $data );
							$sticky_gutter_id .= ' ';
						}
					}
				} else {
					if ( strpos( $ult_sticky_settings['sticky_offset_class'], '.' ) !== false ) {
						$class_flag = 0;

						$sticky_gutter_class .= trim( $ult_sticky_settings['sticky_offset_class'] );

					} elseif ( strpos( $ult_sticky_settings['sticky_offset_class'], '#' ) !== false ) {
						$id_flag           = 0;
						$sticky_gutter_id .= trim( $ult_sticky_settings['sticky_offset_class'] );

					}
				}//checked ',' in string end else.

				if ( 0 != $class_flag ) {
					$sticky_gutter_class = '';
				} else {
					$sticky_gutter_class = rtrim( $sticky_gutter_class ) . "'";
				}

				if ( 0 != $id_flag ) {
					$sticky_gutter_id = '';
				} else {
					$sticky_gutter_id = rtrim( $sticky_gutter_id ) . "'";
				}
			} else {
				$sticky_gutter_class = '';
				$sticky_gutter_id    = '';
			} //check sticky_offset_class end else.

			// width data.
			if ( 'customwidth' == $ult_sticky_settings['sticky_width'] ) {

				if ( '' != $ult_sticky_settings['sticky_custom_width'] ) {
					if ( strpos( $ult_sticky_settings['sticky_custom_width'], 'px' ) !== false || strpos( $ult_sticky_settings['sticky_custom_width'], 'em' ) !== false || strpos( $ult_sticky_settings['sticky_custom_width'], '%' ) !== false ) {
						$ult_sticky_settings['sticky_custom_width'] .= " data-sticky_customwidth= '" . esc_attr( $ult_sticky_settings['sticky_custom_width'] );
					} else {
						$ult_sticky_settings['sticky_custom_width'] .= " data-sticky_customwidth= '" . esc_attr( $ult_sticky_settings['sticky_custom_width'] ) . 'px';
					}
				} else {
					$ult_sticky_settings['sticky_custom_width'] .= " data-sticky_customwidth= '60%";

				}
				$ult_sticky_settings['sticky_custom_width'] .= "'";
			}

			// sticky bahviour.
			$stick_behaviour_data = '' != $ult_sticky_settings['stick_behaviour'] ? " data-stick_behaviour= '" . esc_attr( $ult_sticky_settings['stick_behaviour'] ) . "'" : " data-stick_behaviour= 'stick_with_scroll_row'";

			// permanent data.
			if ( 'stick_permanent' == $ult_sticky_settings['stick_behaviour'] ) {
				$left_right_postion = " data-lr_position= '" . esc_attr( $ult_sticky_settings['sticky_position_lr'] ) . "' ";
				if ( '' != $ult_sticky_settings['permanent_lr'] ) {
					if ( strpos( $ult_sticky_settings['permanent_lr'], 'px' ) !== false || strpos( $ult_sticky_settings['permanent_lr'], 'em' ) !== false || strpos( $ult_sticky_settings['permanent_lr'], '%' ) !== false ) {
						$left_right_postion .= " data-lr_value= '" . esc_attr( $ult_sticky_settings['permanent_lr'] );
					} else {
						$left_right_postion .= " data-lr_value= '" . esc_attr( $ult_sticky_settings['permanent_lr'] ) . 'px';
					}
				} else {
					$left_right_postion .= "data-lr_value= '0";

				}
				$left_right_postion .= "'";
			}

			$custom_data  = '' != $ult_sticky_settings['sticky_gutter'] ? " data-gutter= '" . esc_attr( $ult_sticky_settings['sticky_gutter'] ) . "'" : '';
			$custom_data .= $stick_behaviour_data;
			$custom_data .= $left_right_postion;
			$custom_data .= $sticky_gutter_class . ' ' . $sticky_gutter_id;
			$custom_data .= '' != $ult_sticky_settings['sticky_width'] ? " data-sticky_width= '" . esc_attr( $ult_sticky_settings['sticky_width'] ) . "'" : '';
			$custom_data .= $ult_sticky_settings['sticky_custom_width'];
			$custom_data .= '' != $ult_sticky_settings['sticky_position'] ? " data-sticky_position= '" . esc_attr( $ult_sticky_settings['sticky_position'] ) . "'" : " data-sticky_position= 'top'";
			$custom_data .= $data_mobile;
			$custom_data .= $data_support;

			$output  = '<div class="ult_row_spacer">';
			$output .= '<div class="ult-sticky-anchor">';
			$output .= '<div class="ult-sticky-section ult-sticky ' . esc_attr( $ult_sticky_settings['el_class'] ) . '" ' . $custom_data . '>';
			$output .= do_shortcode( $content );
			$output .= '</div>';
			$output .= '<div class="ult-space"></div>';
			$output .= '</div></div>';
			return $output;

		}//end sticky_shortcode()

		/**
		 * Mapper function for Sticky Section Module.
		 *
		 * @method sticky_shortcode_mapper
		 * @access public
		 */
		public function sticky_shortcode_mapper() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Sticky Section', 'ultimate_vc' ),
						'base'                    => 'ult_sticky_section',
						'icon'                    => 'vc_icon_sticky_section',
						'class'                   => '',
						'as_parent'               => array( 'except' => 'ult_sticky_section' ),
						'content_element'         => true,
						'controls'                => 'full',
						'show_settings_on_create' => true,
						'category'                => 'Ultimate VC Addons',
						'description'             => __( 'Make any elements sticky anywhere.', 'ultimate_vc' ),
						'params'                  => array(

							// sticky behaviour.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Stick Behaviour', 'ultimate_vc' ),
								'param_name'  => 'stick_behaviour',
								'value'       => array(
									'Stick with Row'    => 'stick_with_scroll_row',
									'Stick with Window' => 'stick_with_scroll',
									'Stick Permanent'   => 'stick_permanent',
								),
								'description' => __( 'Set behaviour of sticky section', 'ultimate_vc' ),
							),
							// sticky section width.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Sticky Section Width', 'ultimate_vc' ),
								'param_name'  => 'sticky_width',
								'value'       => array(
									'Default'      => '',
									'Full Width'   => 'fullwidth',
									'Custom Width' => 'customwidth',
								),
								'description' => __( 'Set the width of container', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'stick_behaviour',
									'value'   => array( 'stick_with_scroll', 'stick_permanent' ),
								),
							),

							// sticy section custom width.
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Custom Width', 'ultimate_vc' ),
								'param_name'  => 'sticky_custom_width',
								'description' => __( 'Ex : 20px, 20%, 20em', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'sticky_width',
									'value'   => 'customwidth',
								),
							),

							// sticky section position.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Sticky Section Postion Top / Bottom', 'ultimate_vc' ),
								'param_name'  => 'sticky_position',
								'value'       => array(
									'Top'    => '',
									'Bottom' => 'bottom',
								),
								'description' => __( 'Set the postion of container', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'stick_behaviour',
									'value'   => array( 'stick_permanent' ),
								),
							),

							// permanent position.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Sticky Section Postion Left / Right', 'ultimate_vc' ),
								'param_name'  => 'sticky_position_lr',
								'value'       => array(
									'Left'  => 'left',
									'Right' => 'right',
								),
								'description' => __( 'Default is Left : 0px ', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'stick_behaviour',
									'value'   => 'stick_permanent',
								),
							),

							array(
								'type'        => 'textfield',
								'heading'     => __( 'Value', 'ultimate_vc' ),
								'param_name'  => 'permanent_lr',
								'description' => __( 'Ex : 20px, 20%, 20em', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'sticky_position_lr',
									'value'   => array( 'left', 'right' ),
								),
							),

							// num value.
							array(
								'type'        => 'number',
								'heading'     => __( 'Gutter Space', 'ultimate_vc' ),
								'param_name'  => 'sticky_gutter',
								'suffix'      => 'px',
								'description' => __( 'Ex : 20', 'ultimate_vc' ),

							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Enable on Mobile', 'ultimate_vc' ),
								'param_name'  => 'btn_mobile',
								'value'       => '',
								'options'     => array(
									'enable' => array(
										'label' => '',
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => __( 'Enable Sticky Element on Smartphone.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'stick_behaviour',
									'value'   => array( 'stick_with_scroll_row', 'stick_with_scroll', 'stick_permanent' ),
								),
							),

							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Enable support', 'ultimate_vc' ),
								'param_name'  => 'btn_support',
								'value'       => '',
								'options'     => array(
									'enable' => array(
										'label' => '',
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => __( 'Enable this incase Sticky Element not working.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'stick_behaviour',
									'value'   => array( 'stick_with_scroll' ),
								),
							),

							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);// end vc_map.
			}//end vc_map checker.
		}//end sticky_shortcode_mapper()

		/**
		 * Function that register styles and scripts for Sticky Section Module.
		 *
		 * @method ult_sticky_section_scripts
		 */
		public function ult_sticky_section_scripts() {

			Ultimate_VC_Addons::ultimate_register_script( 'ult_sticky_js', 'fixto', false, array( 'jquery' ), ULTIMATE_VERSION, true );

			Ultimate_VC_Addons::ultimate_register_script( 'ult_sticky_section_js', 'sticky-section', false, array( 'ult_sticky_js' ), ULTIMATE_VERSION, true );

			Ultimate_VC_Addons::ultimate_register_style( 'ult_sticky_section_css', 'sticky-section' );

		}//end ult_sticky_section_scripts()


	}//end class


	// Instantiate the class.
	new Ultimate_Sticky_Section();

	if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ult_Sticky_Section' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Sticky_Section extends WPBakeryShortCodesContainer {
		}
	}
}
