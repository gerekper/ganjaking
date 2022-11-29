<?php
/**
 *  UAVC Ultimate Content Box module file
 *
 *  @package Ultimate Content Box
 */

if ( ! class_exists( 'Ultimate_VC_Addons_Content_Box' ) ) {
		/**
		 * Function that initializes Ultimate Content Box Module
		 *
		 * @class Ultimate_VC_Addons_Content_Box
		 */
	class Ultimate_VC_Addons_Content_Box {
		/**
		 * Constructor function that constructs default values for the  Ultimate Content Box module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ult_content_box_init' ) );
			}
			add_shortcode( 'ult_content_box', array( $this, 'ult_content_box_callback' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ult_content_box_scripts' ), 1 );
		}
				/**
				 * For the animation in the module
				 *
				 * @since ----
				 * @param array  $atts represts module attribuits.
				 * @param string $content value has been set to null.
				 * @access public
				 */
		public function ult_content_box_callback( $atts, $content = null ) {

				$ult_cb_settings = shortcode_atts(
					array(
						'bg_type'                => 'bg_color',
						'bg_image'               => '',
						'bg_color'               => '',
						'bg_repeat'              => 'repeat',
						'bg_size'                => 'cover',
						'bg_position'            => 'center center',
						'border'                 => '',
						'box_shadow'             => '',
						'box_shadow_color'       => '',
						'padding'                => '',
						'margin'                 => '',
						'link'                   => '',
						'hover_bg_color'         => '',
						'hover_border_color'     => '',
						'hover_box_shadow'       => '',
						'box_hover_shadow_color' => '',
						'min_height'             => '',
						'el_class'               => '',
						'trans_property'         => 'all',
						'trans_duration'         => '700',
						'trans_function'         => 'ease',
						'responsive_margin'      => '',

					),
					$atts
				);

			/* 	init var's 	*/
			$style              = '';
			$url                = '';
			$link_title         = '';
			$target             = '';
			$responsive_margins = '';
			$normal_margins     = '';
			$hover              = '';
			$shadow             = '';
			$data_attr          = '';
			$target             = '';
			$link_title         = '';
			$rel                = '';

			if ( '' != $ult_cb_settings['bg_type'] ) {
				switch ( $ult_cb_settings['bg_type'] ) {
					case 'bg_color':    /* 	background color 	*/
						if ( '' != $ult_cb_settings['bg_color'] ) {
							$style     .= 'background-color:' . $ult_cb_settings['bg_color'] . ';';
							$data_attr .= ' data-bg="' . esc_attr( $ult_cb_settings['bg_color'] ) . '" ';
						}
						if ( '' != $ult_cb_settings['hover_bg_color'] ) {
							$hover .= ' data-hover_bg_color="' . esc_attr( $ult_cb_settings['hover_bg_color'] ) . '" ';
						}
						break;
					case 'bg_image':
						if ( '' != $ult_cb_settings['bg_image'] ) {
											$img    = wp_get_attachment_image_src( $ult_cb_settings['bg_image'], 'large' );
											$style .= "background-image:url('" . esc_url( $img[0] ) . "');";
											$style .= 'background-size: ' . esc_attr( $ult_cb_settings['bg_size'] ) . ';';
											$style .= 'background-repeat: ' . esc_attr( $ult_cb_settings['bg_repeat'] ) . ';';
											$style .= 'background-position: ' . esc_attr( $ult_cb_settings['bg_position'] ) . ';';
											$style .= 'background-color: rgba(0, 0, 0, 0);';
						}
						break;
				}
			}

			/* 	box shadow 	*/
			if ( '' != $ult_cb_settings['box_shadow'] ) {
				$style .= apply_filters( 'ultimate_getboxshadow', $ult_cb_settings['box_shadow'], 'css' );
			}

			/* 	box shadow - {HOVER} 	*/
			if ( '' != $ult_cb_settings['hover_box_shadow'] ) {

				$data = apply_filters( 'ultimate_getboxshadow', $ult_cb_settings['hover_box_shadow'], 'data' );

				if ( false !== strpos( $data, 'none' ) ) {
					$data = 'none';
				}
				// Apply default box shadow.
				if ( false !== strpos( $data, 'inherit' ) ) {
					if ( '' != $ult_cb_settings['box_shadow'] ) {
						$data = apply_filters( 'ultimate_getboxshadow', $ult_cb_settings['box_shadow'], 'data' );
					}
				}

				$hover .= ' data-hover_box_shadow="' . esc_attr( $data ) . '" ';
			}

			/* 	border 	*/
			if ( '' != $ult_cb_settings['border'] ) {
				$border_array = explode( '|', $ult_cb_settings['border'] );
				$border_color = '';
				foreach ( $border_array as $border_val ) {
					$border_value_array = explode( ':', $border_val );
					if ( isset( $border_value_array[0] ) && 'border-color' === $border_value_array[0] ) {
						$border_color = ( isset( $border_value_array[1] ) ) ? rtrim( $border_value_array[1], ';' ) : '';
					}
				}
				$temp_border = str_replace( '|', '', $ult_cb_settings['border'] );
				$style      .= $temp_border;
				$data_attr  .= ' data-border_color="' . esc_attr( $border_color ) . '" ';
			}

			/* 	link 	*/
			if ( '' != $ult_cb_settings['link'] ) {
				$href       = vc_build_link( $ult_cb_settings['link'] );
				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
			}

			/* 	padding  	*/
			if ( '' != $ult_cb_settings['padding'] ) {
				$style .= $ult_cb_settings['padding'];     }

			/* 	margin 		*/
			if ( '' != $ult_cb_settings['margin'] ) {
				$style .= $ult_cb_settings['margin'];      }

			// HOVER.
			if ( '' != $ult_cb_settings['hover_border_color'] ) {
				$hover .= ' data-hover_border_color="' . esc_attr( $ult_cb_settings['hover_border_color'] ) . '" ';
			}
			if ( '' != $ult_cb_settings['min_height'] ) {
				$style .= 'min-height:' . esc_attr( $ult_cb_settings['min_height'] ) . 'px;'; }

			// Transition Effect.
			if ( '' != $ult_cb_settings['trans_property'] && '' != $ult_cb_settings['trans_duration'] && '' != $ult_cb_settings['trans_function'] ) {
				$style .= '-webkit-transition: ' . $ult_cb_settings['trans_property'] . ' ' . $ult_cb_settings['trans_duration'] . 'ms ' . $ult_cb_settings['trans_function'] . ';';
				$style .= '-moz-transition: ' . $ult_cb_settings['trans_property'] . ' ' . $ult_cb_settings['trans_duration'] . 'ms ' . $ult_cb_settings['trans_function'] . ';';
				$style .= '-ms-transition: ' . $ult_cb_settings['trans_property'] . ' ' . $ult_cb_settings['trans_duration'] . 'ms ' . $ult_cb_settings['trans_function'] . ';';
				$style .= '-o-transition: ' . $ult_cb_settings['trans_property'] . ' ' . $ult_cb_settings['trans_duration'] . 'ms ' . $ult_cb_settings['trans_function'] . ';';
				$style .= 'transition: ' . $ult_cb_settings['trans_property'] . ' ' . $ult_cb_settings['trans_duration'] . 'ms ' . $ult_cb_settings['trans_function'] . ';';
			}

			/* 	Margins - Responsive 	*/
			if ( '' != $ult_cb_settings['responsive_margin'] ) {
				$responsive_margins .= ' data-responsive_margins="' . esc_attr( $ult_cb_settings['responsive_margin'] ) . '" ';
			}
			/* 	Margins - Normal  */
			if ( '' != $ult_cb_settings['margin'] ) {
				$normal_margins .= ' data-normal_margins="' . esc_attr( $ult_cb_settings['margin'] ) . '" ';
			}

			$output = '<div class="ult-content-box-container ' . esc_attr( $ult_cb_settings['el_class'] ) . '" >';
			if ( '' != $ult_cb_settings['link'] ) {
				$output .= '	<a class="ult-content-box-anchor" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '>';
			}
			$output .= '		<div class="ult-content-box" style="' . esc_attr( $style ) . '" ' . $hover . ' ' . $responsive_margins . ' ' . $normal_margins . ' ' . $data_attr . '>';
			$output .= do_shortcode( $content );
			$output .= '		</div>';
			if ( '' != $ult_cb_settings['link'] ) {
				$output .= '	</a>';
			}
			$output .= '</div>';

			return $output;
		}
		/**
		 * For vc map check
		 *
		 * @since ----
		 * @access public
		 */
		public function ult_content_box_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Content Box', 'ultimate_vc' ),
						'base'                    => 'ult_content_box',
						'icon'                    => 'vc_icon_content_box',
						'class'                   => 'vc_icon_content_box',
						'as_parent'               => array( 'except' => 'ult_content_box' ),

						'controls'                => 'full',
						'show_settings_on_create' => true,

						'category'                => 'Ultimate VC Addons',
						'description'             => __( 'Content Box.', 'ultimate_vc' ),
						'js_view'                 => 'VcColumnView',
						'params'                  => array(
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Background Type', 'ultimate_vc' ),
								'param_name' => 'bg_type',
								'value'      => array(
									__( 'Background Color', 'ultimate_vc' ) => 'bg_color',
									__( 'Background Image', 'ultimate_vc' ) => 'bg_image',
								),
							),

							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'param_name' => 'bg_color',
								'dependency' => array(
									'element' => 'bg_type',
									'value'   => 'bg_color',
								),
							),
							array(
								'type'        => 'attach_image',
								'heading'     => __( 'Background Image', 'ultimate_vc' ),
								'param_name'  => 'bg_image',
								'description' => __( 'Set background image for content box.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'bg_type',
									'value'   => 'bg_image',
								),
							),
							array(
								'type'       => 'ultimate_border',
								'heading'    => __( 'Border', 'ultimate_vc' ),
								'param_name' => 'border',
								'unit'       => 'px',                                             // [required] px,em,%,all     Default all.
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )     => '',
									__( 'Right', 'ultimate_vc' )   => '',
									__( 'Bottom', 'ultimate_vc' )  => '',
									__( 'Left', 'ultimate_vc' )    => '',
								),

								'radius'     => array(
									__( 'Top Left', 'ultimate_vc' )     => '',
									__( 'Top Right', 'ultimate_vc' )    => '',
									__( 'Bottom Right', 'ultimate_vc' ) => '',
									__( 'Bottom Left', 'ultimate_vc' )  => '',
								),

							),
							array(
								'type'       => 'ultimate_boxshadow',
								'heading'    => __( 'Box Shadow', 'ultimate_vc' ),
								'param_name' => 'box_shadow',
								'unit'       => 'px',                        // [required] px,em,%,all     Default all.
								'positions'  => array(
									__( 'Horizontal', 'ultimate_vc' )     => '',
									__( 'Vertical', 'ultimate_vc' )   => '',
									__( 'Blur', 'ultimate_vc' )  => '',
									__( 'Spread', 'ultimate_vc' )    => '',
								),

							),

							// Spacing.
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'content_spacing',
								'text'             => __( 'Spacing', 'ultimate_vc' ),
								'value'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'padding',
								'mode'       => 'padding',                       // margin/padding.
								'unit'       => 'px',                            // [required] px,em,%,all     Default all.
								'positions'  => array(                       // Also set 'defaults'.
									__( 'Top', 'ultimate_vc' ) => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Margin', 'ultimate_vc' ),
								'param_name' => 'margin',
								'mode'       => 'margin',                    // margin/padding.
								'unit'       => 'px',                            // [required] px,em,%,all     Default all.
								'positions'  => array(                       // Also set 'defaults'.
									__( 'Top', 'ultimate_vc' ) => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
							),
							array(
								'type'       => 'vc_link',
								'heading'    => __( 'Content Box Link', 'ultimate_vc' ),
								'param_name' => 'link',

							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Min Height', 'ultimate_vc' ),
								'param_name' => 'min_height',
								'suffix'     => 'px',
								'min'        => '0',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),

							// Background.
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Background Image Repeat', 'ultimate_vc' ),
								'param_name' => 'bg_repeat',
								'value'      => array(
									__( 'Repeat', 'ultimate_vc' ) => 'repeat',
									__( 'Repeat X', 'ultimate_vc' ) => 'repeat-x',
									__( 'Repeat Y', 'ultimate_vc' ) => 'repeat-y',
									__( 'No Repeat', 'ultimate_vc' ) => 'no-repeat',
								),
								'group'      => 'Background',
								'dependency' => array(
									'element' => 'bg_type',
									'value'   => 'bg_image',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Background Image Size', 'ultimate_vc' ),
								'param_name' => 'bg_size',
								'value'      => array(
									__( 'Cover - Image to be as large as possible', 'ultimate_vc' ) => 'cover',
									__( 'Contain - Image will try to fit inside the container area', 'ultimate_vc' ) => 'contain',
									__( 'Initial', 'ultimate_vc' ) => 'initial',
								),
								'group'      => 'Background',
								'dependency' => array(
									'element' => 'bg_type',
									'value'   => 'bg_image',
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Background Image Position', 'ultimate_vc' ),
								'param_name'  => 'bg_position',
								'description' => __( 'You can use any number with px, em, %, etc. Example- 100px 100px.', 'ultimate_vc' ),
								'group'       => 'Background',
								'dependency'  => array(
									'element' => 'bg_type',
									'value'   => 'bg_image',
								),
							),

							// Hover.
							array(
								'type'       => 'colorpicker',

								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'param_name' => 'hover_bg_color',
								'dependency' => array(
									'element' => 'bg_type',
									'value'   => 'bg_color',
								),
								'group'      => 'Hover',
							),
							array(
								'type'             => 'colorpicker',
								'heading'          => __( 'Border Color', 'ultimate_vc' ),
								'param_name'       => 'hover_border_color',
								'edit_field_class' => 'vc_col-sm-12 vc_column border_ultimate_border',  // Custom dependency.
								'group'            => 'Hover',
							),
							array(
								'type'        => 'ultimate_boxshadow',
								'heading'     => __( 'Box Shadow', 'ultimate_vc' ),
								'param_name'  => 'hover_box_shadow',
								'unit'        => 'px',                        // [required] px,em,%,all     Default all.
								'positions'   => array(
									__( 'Horizontal', 'ultimate_vc' )     => '',
									__( 'Vertical', 'ultimate_vc' )   => '',
									__( 'Blur', 'ultimate_vc' )  => '',
									__( 'Spread', 'ultimate_vc' )    => '',
								),
								'label_color' => __( 'Shadow Color', 'ultimate_vc' ),

								'group'       => 'Hover',
							),

							// Effect.
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'content_transition',
								'text'             => __( 'Transition Options', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Hover',
							),
							array(
								'type'       => 'dropdown',

								'heading'    => __( 'Transition Property', 'ultimate_vc' ),
								'param_name' => 'trans_property',
								'value'      => array(
									__( 'All', 'ultimate_vc' ) => 'all',
									__( 'Background', 'ultimate_vc' ) => 'background',
									__( 'Color', 'ultimate_vc' ) => 'color',
									__( 'Height', 'ultimate_vc' ) => 'height',
									__( 'Width', 'ultimate_vc' ) => 'width',
									__( 'Outline', 'ultimate_vc' ) => 'outline',
								),
								'group'      => 'Hover',
							),
							array(
								'type'       => 'number',

								'heading'    => __( 'Duration', 'ultimate_vc' ),
								'param_name' => 'trans_duration',
								'suffix'     => 'ms',
								'min'        => '0',
								'value'      => '',
								'group'      => 'Hover',
							),
							array(
								'type'       => 'dropdown',

								'heading'    => __( 'Transition Effect', 'ultimate_vc' ),
								'param_name' => 'trans_function',
								'value'      => array(
									__( 'Ease', 'ultimate_vc' ) => 'ease',
									__( 'Linear', 'ultimate_vc' ) => 'linear',
									__( 'Ease-In', 'ultimate_vc' ) => 'ease-in',
									__( 'Ease-Out', 'ultimate_vc' ) => 'ease-out',
									__( 'Ease-In-Out', 'ultimate_vc' ) => 'ease-in-out',
								),
								'group'      => 'Hover',
							),

							// Responsive.
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => __( 'Margin', 'ultimate_vc' ),
								'param_name'  => 'responsive_margin',
								'mode'        => 'margin',                        // margin/padding.
								'unit'        => 'px',                            // [required] px,em,%,all     Default all.
								'positions'   => array(                       // Also set 'defaults'.
									__( 'Top', 'ultimate_vc' ) => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
								'group'       => __( 'Responsive', 'ultimate_vc' ),
								'description' => __( 'This margin will apply below screen 768px.', 'ultimate_vc' ),
							),
						),
					)
				);
			}
		}
				/**
				 * Function to regester scripts
				 *
				 * @since ----
				 * @access public
				 */
		public function ult_content_box_scripts() {
			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons_content_box_css', 'content-box' );

			Ultimate_VC_Addons::ultimate_register_script( 'ultimate-vc-addons_content_box_js', 'content-box', false, array( 'jquery' ) );
		}
	}
	// Finally initialize code.
	new Ultimate_VC_Addons_Content_Box();
	if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ult_Content_Box' ) ) {
				/**
				 * Function that initializes Ultimate Content Box Module
				 *
				 * @class WPBakeryShortCode_Ult_Animation_Block
				 */
		class WPBakeryShortCode_Ult_Content_Box extends WPBakeryShortCodesContainer {
		}
	}
}
