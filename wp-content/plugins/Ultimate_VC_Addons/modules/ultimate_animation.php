<?php
/**
 *  UAVC Ultimate Animation module file
 *
 *  @package Ultimate Animation
 */

if ( ! class_exists( 'Ultimate_Animation' ) ) {
	/**
	 * Function that initializes Ultimate Animation Module
	 *
	 * @class Ultimate_Animation
	 */
	class Ultimate_Animation {
		/**
		 * Constructor function that constructs default values for the Ultimate Animation module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'animate_shortcode_mapper' ) );
			}
			add_shortcode( 'ult_animation_block', array( $this, 'animate_shortcode' ) );
		}
		/**
		 * For the animation in the module
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function animate_shortcode( $atts, $content = null ) {

			$output                    = '';
			$opacity_start_effect_data = '';

				$ult_ua_settings = shortcode_atts(
					array(
						'animation'                 => 'none',
						'opacity'                   => 'set',
						'opacity_start_effect'      => '',
						'animation_duration'        => '3',
						'animation_delay'           => '0',
						'animation_iteration_count' => '1',
						'inline_disp'               => '',
						'css'                       => '',
						'el_class'                  => '',
					),
					$atts
				);

			$style              = '';
			$infi               = '';
			$mobile_opt         = '';
			$css_class          = '';
			$css_class          = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_ua_settings['css'], ' ' ), 'ult_createlink', $atts );
			$css_class          = esc_attr( $css_class );
			$ultimate_animation = get_option( 'ultimate_animation' );
			if ( 'disable' == $ultimate_animation ) {
				$mobile_opt = 'ult-no-mobile';
			}
			if ( '' !== $ult_ua_settings['inline_disp'] ) {
				$style .= 'display:inline-block;';
			}
			if ( 'set' == $ult_ua_settings['opacity'] ) {
				$style                       .= 'opacity:0;';
				$ult_ua_settings['el_class'] .= ' ult-animate-viewport ';
				$opacity_start_effect_data    = 'data-opacity_start_effect="' . esc_attr( $ult_ua_settings['opacity_start_effect'] ) . '"';
			}
			$inifinite_arr = array( 'InfiniteRotate', 'InfiniteDangle', 'InfiniteSwing', 'InfinitePulse', 'InfiniteHorizontalShake', 'InfiniteBounce', 'InfiniteFlash', 'InfiniteTADA' );
			if ( 0 == $ult_ua_settings['animation_iteration_count'] || in_array( $ult_ua_settings['animation'], $inifinite_arr, true ) ) {
				$ult_ua_settings['animation_iteration_count'] = 'infinite';
				$ult_ua_settings['animation']                 = 'infinite ' . $ult_ua_settings['animation'];
			}
			$output .= '<div class="ult-animation ' . esc_attr( $ult_ua_settings['el_class'] ) . ' ' . esc_attr( $mobile_opt ) . ' ' . esc_attr( $css_class ) . '" data-animate="' . esc_attr( $ult_ua_settings['animation'] ) . '" data-animation-delay="' . esc_attr( $ult_ua_settings['animation_delay'] ) . '" data-animation-duration="' . esc_attr( $ult_ua_settings['animation_duration'] ) . '" data-animation-iteration="' . esc_attr( $ult_ua_settings['animation_iteration_count'] ) . '" style="' . esc_attr( $style ) . '" ' . $opacity_start_effect_data . '>';
			$output .= do_shortcode( $content );
			$output .= '</div>';
			return $output;
		} /* end animate_shortcode()*/

		/**
		 * For vc map check
		 *
		 * @since ----
		 * @access public
		 */
		public function animate_shortcode_mapper() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Animation Block', 'ultimate_vc' ),
						'base'                    => 'ult_animation_block',
						'icon'                    => 'animation_block',
						'class'                   => 'animation_block',
						'as_parent'               => array( 'except' => 'ult_animation_block' ),
						'content_element'         => true,
						'controls'                => 'full',
						'show_settings_on_create' => true,
						'category'                => 'Ultimate VC Addons',
						'description'             => __( 'Apply animations everywhere.', 'ultimate_vc' ),
						'params'                  => array(
							// add params same as with any other content element.
							array(
								'type'       => 'animator',
								'class'      => '',
								'heading'    => __( 'Animation', 'ultimate_vc' ),
								'param_name' => 'animation',
								'value'      => '',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Duration', 'ultimate_vc' ),
								'param_name'  => 'animation_duration',
								'value'       => 3,
								'min'         => 1,
								'max'         => 100,
								'suffix'      => 's',
								'description' => __( 'How long the animation effect should last. Decides the speed of effect.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Delay', 'ultimate_vc' ),
								'param_name'  => 'animation_delay',
								'value'       => 0,
								'min'         => 1,
								'max'         => 100,
								'suffix'      => 's',
								'description' => __( 'Delays the animation effect for seconds you enter above.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Repeat Count', 'ultimate_vc' ),
								'param_name'  => 'animation_iteration_count',
								'value'       => 1,
								'min'         => 0,
								'max'         => 100,
								'suffix'      => '',
								'description' => __( 'The animation effect will repeat to the count you enter above. Enter 0 if you want to repeat it infinitely.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Hide Elements Until Delay', 'ultimate_vc' ),
								'param_name'  => 'opacity',
								'admin_label' => true,
								'value'       => 'set',
								'default_set' => true,
								'options'     => array(
									'set' => array(
										'label' => __( 'If set to yes, the elements inside block will stay hidden until animation starts (depends on delay settings above).', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Viewport Position', 'ultimate_vc' ),
								'param_name'  => 'opacity_start_effect',
								'suffix'      => '%',
								'value'       => '90',
								'description' => __( 'The area of screen from top where animation effects will start working.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/pd-ct' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);/* end vc_map*/
			} /* end vc_map check*/
		}//end animate_shortcode_mapper()

	}
	// Instantiate the class.
	new Ultimate_Animation();
	if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ult_Animation_Block' ) ) {
		/**
		 * Function that initializes Ultimate Animation Module
		 *
		 * @class WPBakeryShortCode_Ult_Animation_Block
		 */
		class WPBakeryShortCode_Ult_Animation_Block extends WPBakeryShortCodesContainer {
		}
	}
}
