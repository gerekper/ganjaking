<?php
/**
 * Add-on Name: Just Icon for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 * @package Just Icon
 */

if ( ! class_exists( 'AIO_Just_Icon' ) ) {
	/**
	 * Function that initializes Just Icon Module
	 *
	 * @class AIO_Just_Icon
	 */
	class AIO_Just_Icon {
		/**
		 * Constructor function that constructs default values for the Just Icon module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'just_icon_init' ) );
			}
			add_shortcode( 'just_icon', array( $this, 'just_icon_shortcode' ) );
		}
		/**
		 * Function that initializes settings of Just Icon Module.
		 *
		 * @method just_icon_init
		 */
		public function just_icon_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Just Icon', 'ultimate_vc' ),
						'base'        => 'just_icon',
						'class'       => 'vc_simple_icon',
						'icon'        => 'vc_just_icon',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Add a simple icon and give some custom style.', 'ultimate_vc' ),
						'params'      => array(
							// Play with icon selector.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
									__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
								),
								'description' => __( 'Use existing font icon or upload a custom image.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name'  => 'icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
								'param_name'  => 'icon_img',
								'admin_label' => true,
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Width', 'ultimate_vc' ),
								'param_name'  => 'img_width',
								'value'       => 48,
								'min'         => 16,
								'max'         => 512,
								'suffix'      => 'px',
								'description' => __( 'Provide image width', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Size of Icon', 'ultimate_vc' ),
								'param_name'  => 'icon_size',
								'value'       => 32,
								'min'         => 12,
								'max'         => 72,
								'suffix'      => 'px',
								'description' => __( 'How big would you like it?', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '#333333',
								'description' => __( 'Give it a nice paint!', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon or Image Style', 'ultimate_vc' ),
								'param_name'  => 'icon_style',
								'value'       => array(
									__( 'Simple', 'ultimate_vc' ) => 'none',
									__( 'Circle Background', 'ultimate_vc' ) => 'circle',
									__( 'Square Background', 'ultimate_vc' ) => 'square',
									__( 'Hexagon Background', 'ultimate_vc' ) => 'hexagon',
									__( 'Design your own', 'ultimate_vc' ) => 'advanced',
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_bg',
								'value'       => '#ffffff',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced', 'hexagon' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon Border Style', 'ultimate_vc' ),
								'param_name'  => 'icon_border_style',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'description' => __( 'Select the border style for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'advanced' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_border',
								'value'       => '#333333',
								'description' => __( 'Select border color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Width', 'ultimate_vc' ),
								'param_name'  => 'icon_border_size',
								'value'       => 1,
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Radius', 'ultimate_vc' ),
								'param_name'  => 'icon_border_radius',
								'value'       => 500,
								'min'         => 1,
								'max'         => 500,
								'suffix'      => 'px',
								'description' => __( '0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (e.g 500 pixels).', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Background Size', 'ultimate_vc' ),
								'param_name'  => 'icon_border_spacing',
								'value'       => 50,
								'min'         => 30,
								'max'         => 500,
								'suffix'      => 'px',
								'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'smile' ),
								'param_name'  => 'icon_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Animation', 'ultimate_vc' ),
								'param_name'  => 'icon_animation',
								'value'       => array(
									__( 'No Animation', 'ultimate_vc' ) => '',
									__( 'Swing', 'ultimate_vc' ) => 'swing',
									__( 'Pulse', 'ultimate_vc' ) => 'pulse',
									__( 'Fade In', 'ultimate_vc' ) => 'fadeIn',
									__( 'Fade In Up', 'ultimate_vc' ) => 'fadeInUp',
									__( 'Fade In Down', 'ultimate_vc' ) => 'fadeInDown',
									__( 'Fade In Left', 'ultimate_vc' ) => 'fadeInLeft',
									__( 'Fade In Right', 'ultimate_vc' ) => 'fadeInRight',
									__( 'Fade In Up Long', 'ultimate_vc' ) => 'fadeInUpBig',
									__( 'Fade In Down Long', 'ultimate_vc' ) => 'fadeInDownBig',
									__( 'Fade In Left Long', 'ultimate_vc' ) => 'fadeInLeftBig',
									__( 'Fade In Right Long', 'ultimate_vc' ) => 'fadeInRightBig',
									__( 'Slide In Down', 'ultimate_vc' ) => 'slideInDown',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Bounce In', 'ultimate_vc' ) => 'bounceIn',
									__( 'Bounce In Up', 'ultimate_vc' ) => 'bounceInUp',
									__( 'Bounce In Down', 'ultimate_vc' ) => 'bounceInDown',
									__( 'Bounce In Left', 'ultimate_vc' ) => 'bounceInLeft',
									__( 'Bounce In Right', 'ultimate_vc' ) => 'bounceInRight',
									__( 'Rotate In', 'ultimate_vc' ) => 'rotateIn',
									__( 'Light Speed In', 'ultimate_vc' ) => 'lightSpeedIn',
									__( 'Roll In', 'ultimate_vc' ) => 'rollIn',
								),
								'description' => __( 'Like CSS3 Animations? We have several options for you!', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Tooltip', 'ultimate_vc' ),
								'param_name'  => 'tooltip_disp',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Tooltip from Left', 'ultimate_vc' ) => 'left',
									__( 'Tooltip from Right', 'ultimate_vc' ) => 'right',
									__( 'Tooltip from Top', 'ultimate_vc' ) => 'top',
									__( 'Tooltip from Bottom', 'ultimate_vc' ) => 'bottom',
								),
								'description' => __( 'Select the tooltip position', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Tooltip Text', 'ultimate_vc' ),
								'param_name'  => 'tooltip_text',
								'value'       => '',
								'description' => __( 'Enter your tooltip text here.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'tooltip_disp',
									'not_empty' => true,
								),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'icon_align',
								'value'      => array(
									__( 'Center', 'ultimate_vc' )  => 'center',
									__( 'Left', 'ultimate_vc' )        => 'left',
									__( 'Right', 'ultimate_vc' )       => 'right',
								),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Custom CSS Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Ran out of options? Need more styles? Write your own CSS and mention the class name here.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_just_icon',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}
		/**
		 * Render function for Just Icon Module.
		 *
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function just_icon_shortcode( $atts ) {
				$ult_just_settings = shortcode_atts(
					array(
						'icon_type'           => 'selector',
						'icon'                => 'none',
						'icon_img'            => '',
						'img_width'           => '48',
						'icon_size'           => '32',
						'icon_color'          => '#333',
						'icon_style'          => 'none',
						'icon_color_bg'       => '#ffffff',
						'icon_color_border'   => '#333333',
						'icon_border_style'   => '',
						'icon_border_size'    => '1',
						'icon_border_radius'  => '500',
						'icon_border_spacing' => '50',
						'icon_link'           => '',
						'icon_animation'      => 'none',
						'tooltip_disp'        => '',
						'tooltip_text'        => '',
						'el_class'            => '',
						'icon_align'          => 'center',
						'css_just_icon'       => '',
					),
					$atts
				);
			$is_preset             = false;
			if ( isset( $_GET['preset'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
				$is_preset = true;
			}
			$ult_just_settings['css_just_icon'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_just_settings['css_just_icon'], ' ' ), 'just_icon', $atts );
			$ult_just_settings['css_just_icon'] = esc_attr( $ult_just_settings['css_just_icon'] );
			$ultimate_js                        = get_option( 'ultimate_js' );
			if ( '' !== $ult_just_settings['tooltip_text'] && 'enable' !== $ultimate_js ) {
				wp_enqueue_script( 'ultimate-tooltip' );
			}

			$output           = '';
			$style            = '';
			$link_sufix       = '';
			$link_prefix      = '';
			$target           = '';
			$href             = '';
			$icon_align_style = '';
			$css_trans        = '';
			$target           = '';
			$link_title       = '';
			$rel              = '';

			if ( trim( $ult_just_settings['icon_animation'] ) === '' ) {
				$ult_just_settings['icon_animation'] = 'none';
			}

			if ( 'none' !== $ult_just_settings['icon_animation'] ) {
				$css_trans = 'data-animation="' . esc_attr( $ult_just_settings['icon_animation'] ) . '" data-animation-delay="03"';
			}

			$uniqid = uniqid();
			$href   = vc_build_link( $ult_just_settings['icon_link'] );

			if ( '' !== $ult_just_settings['icon_link'] ) {
				if ( null != $href['url'] ) {
					$url          = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target       = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$link_title   = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel          = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
					$link_prefix .= '<a class="aio-tooltip ' . esc_attr( $uniqid ) . '" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' data-toggle="tooltip" data-placement="' . esc_attr( $ult_just_settings['tooltip_disp'] ) . '">';
					$link_sufix  .= '</a>';
				}
			}

			if ( '' !== $ult_just_settings['tooltip_disp'] && '' == $link_title ) {
				$link_prefix .= '<div class="aio-tooltip ' . esc_attr( $uniqid ) . '" data-toggle="tooltip" data-placement="' . esc_attr( $ult_just_settings['tooltip_disp'] ) . '" title="' . esc_attr( $ult_just_settings['tooltip_text'] ) . '">';
				$link_sufix  .= '</div>';
			}

			$elx_class = '';

			/* position fix */
			if ( 'right' == $ult_just_settings['icon_align'] ) {
				$icon_align_style .= 'text-align:right;';
			} elseif ( 'center' == $ult_just_settings['icon_align'] ) {
				$icon_align_style .= 'text-align:center;';
			} elseif ( 'left' == $ult_just_settings['icon_align'] ) {
				$icon_align_style .= 'text-align:left;';
			}

			if ( 'custom' == $ult_just_settings['icon_type'] ) {

				$img = apply_filters( 'ult_get_img_single', $ult_just_settings['icon_img'], 'url' );
				$alt = apply_filters( 'ult_get_img_single', $ult_just_settings['icon_img'], 'alt' );

				if ( 'none' !== $ult_just_settings['icon_style'] ) {
					if ( '' !== $ult_just_settings['icon_color_bg'] ) {
						$style .= 'background:' . $ult_just_settings['icon_color_bg'] . ';';
					}
				}
				if ( 'circle' == $ult_just_settings['icon_style'] ) {
					$elx_class .= ' uavc-circle ';
				}
				if ( 'square' == $ult_just_settings['icon_style'] ) {
					$elx_class .= ' uavc-square ';
				}
				if ( 'hexagon' == $ult_just_settings['icon_style'] ) {
					$elx_class .= ' uavc-hexagon ';
					$style     .= 'border-color:' . $ult_just_settings['icon_color_bg'] . ';';
				}
				if ( 'advanced' == $ult_just_settings['icon_style'] && '' !== $ult_just_settings['icon_border_style'] ) {
					$style .= 'border-style:' . $ult_just_settings['icon_border_style'] . ';';
					$style .= 'border-color:' . $ult_just_settings['icon_color_border'] . ';';
					$style .= 'border-width:' . $ult_just_settings['icon_border_size'] . 'px;';
					$style .= 'padding:' . $ult_just_settings['icon_border_spacing'] . 'px;';
					$style .= 'border-radius:' . $ult_just_settings['icon_border_radius'] . 'px;';
				}

				if ( ! empty( $img ) ) {
					if ( '' == $ult_just_settings['icon_link'] || 'center' == $ult_just_settings['icon_align'] ) {
						$style .= 'display:inline-block;';
					}
					$output .= "\n" . $link_prefix . '<div class="aio-icon-img ' . esc_attr( $elx_class ) . '" style="font-size:' . esc_attr( $ult_just_settings['img_width'] ) . 'px;' . esc_attr( $style ) . '" ' . $css_trans . '>';
					$output .= "\n\t" . '<img class="img-icon" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '"/>';
					$output .= "\n" . '</div>' . $link_sufix;
				}
				$output = $output;
			} else {
				if ( '' !== $ult_just_settings['icon_color'] ) {
					$style .= 'color:' . $ult_just_settings['icon_color'] . ';';
				}
				if ( 'none' !== $ult_just_settings['icon_style'] ) {
					if ( '' !== $ult_just_settings['icon_color_bg'] ) {
						$style .= 'background:' . $ult_just_settings['icon_color_bg'] . ';';
					}
				}
				if ( 'hexagon' == $ult_just_settings['icon_style'] ) {
					$style .= 'border-color:' . $ult_just_settings['icon_color_bg'] . ';';
				}
				if ( 'advanced' == $ult_just_settings['icon_style'] ) {
					$style .= 'border-style:' . $ult_just_settings['icon_border_style'] . ';';
					$style .= 'border-color:' . $ult_just_settings['icon_color_border'] . ';';
					$style .= 'border-width:' . $ult_just_settings['icon_border_size'] . 'px;';
					$style .= 'width:' . $ult_just_settings['icon_border_spacing'] . 'px;';
					$style .= 'height:' . $ult_just_settings['icon_border_spacing'] . 'px;';
					$style .= 'line-height:' . $ult_just_settings['icon_border_spacing'] . 'px;';
					$style .= 'border-radius:' . $ult_just_settings['icon_border_radius'] . 'px;';
				}
				if ( '' !== $ult_just_settings['icon_size'] ) {
					$style .= 'font-size:' . $ult_just_settings['icon_size'] . 'px;';
				}
				if ( 'left' !== $ult_just_settings['icon_align'] ) {
					$style .= 'display:inline-block;';
				}
				if ( '' !== $ult_just_settings['icon'] ) {
					$output .= "\n" . $link_prefix . '<div class="aio-icon ' . esc_attr( $ult_just_settings['icon_style'] ) . ' ' . esc_attr( $elx_class ) . '" ' . $css_trans . ' style="' . esc_attr( $style ) . '">';
					$output .= "\n\t" . '<i class="' . esc_attr( $ult_just_settings['icon'] ) . '"></i>';
					$output .= "\n" . '</div>' . $link_sufix;
				}
				$output = $output;
			}
			if ( '' !== $ult_just_settings['tooltip_disp'] && '' !== $ult_just_settings['tooltip_text'] ) {
				$output .= '<script>
					jQuery(function () {
						jQuery(".' . esc_attr( $uniqid ) . '").bsf_tooltip("hide");
					})
				</script>';
			}
			/* alignment fix */
			if ( '' !== $icon_align_style ) {
				$output = '<div class="align-icon" style="' . esc_attr( $icon_align_style ) . '">' . $output . '</div>';
			}

			$output = '<div class="ult-just-icon-wrapper ' . esc_attr( $ult_just_settings['el_class'] ) . ' ' . esc_attr( $ult_just_settings['css_just_icon'] ) . '">' . $output . '</div>';

			if ( $is_preset ) {
				$text = 'array ( ';
				foreach ( $atts as $key => $att ) {
					$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
				}
				if ( '' != $content ) {
					$text .= '<br/>	\'content\' => \'' . $content . '\',';
				}
				$text   .= '<br/>)';
				$output .= '<pre>';
				$output .= $text;
				$output .= '</pre>';
			}

			return $output;
		}
	}
}
if ( class_exists( 'AIO_Just_Icon' ) ) {
	$aio_just_icon = new AIO_Just_Icon();
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Just_Icon' ) ) {
	/**
	 * Function that checks if the class is exists or not.
	 */
	class WPBakeryShortCode_Just_Icon extends WPBakeryShortCode {
	}
}
