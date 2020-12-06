<?php
/**
 * Add-on Name: Icons Block for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Icons Block
 */

if ( ! class_exists( 'Ultimate_Icons' ) ) {
	/**
	 * Function that initializes Icons Block Module
	 *
	 * @class Ultimate_Icons
	 */
	class Ultimate_Icons {
		/**
		 * Constructor function that constructs default values for the Icons Block module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_icon_init' ) );
			}
			add_shortcode( 'ultimate_icons', array( $this, 'ultimate_icons_shortcode' ) );
			add_shortcode( 'single_icon', array( $this, 'single_icon_shortcode' ) );
		}
		/**
		 * Function that initializes settings of Icons Block Module.
		 *
		 * @method ultimate_icon_init
		 */
		public function ultimate_icon_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Icons', 'ultimate_vc' ),
						'base'                    => 'ultimate_icons',
						'class'                   => 'ultimate_icons',
						'icon'                    => 'ultimate_icons',
						'category'                => 'Ultimate VC Addons',
						'description'             => __( 'Add a set of multiple icons and give some custom style.', 'ultimate_vc' ),
						'as_parent'               => array( 'only' => 'single_icon' ), // Use only|except attributes to limit child shortcodes (separate multiple values with comma).
						'content_element'         => true,
						'show_settings_on_create' => true,
						'js_view'                 => 'VcColumnView',
						'params'                  => array(
							// Play with icon selector.
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'align',
								'value'      => array(
									__( 'Left Align', 'ultimate_vc' ) => 'uavc-icons-left',
									__( 'Right Align', 'ultimate_vc' ) => 'uavc-icons-right',
									__( 'Center Align', 'ultimate_vc' ) => 'uavc-icons-center',
								),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Write your own CSS and mention the class name here.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_icon',
								'group'            => __( 'Design', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
				vc_map(
					array(
						'name'                    => __( 'Icon Item' ),
						'base'                    => 'single_icon',
						'class'                   => 'vc_simple_icon',
						'icon'                    => 'vc_just_icon',
						'category'                => __( 'Ultimate VC Addons', 'ultimate_vc' ),
						'description'             => __( 'Add a set of multiple icons and give some custom style.', 'ultimate_vc' ),
						'as_child'                => array( 'only' => 'ultimate_icons' ), // Use only|except attributes to limit child shortcodes (separate multiple values with comma).
						'show_settings_on_create' => true,
						'is_container'            => false,
						'params'                  => array(
							// Play with icon selector.
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name'  => 'icon',
								'value'       => '',
								'admin_label' => true,
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Select Icon',
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
								'group'       => 'Select Icon',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Space after Icon', 'ultimate_vc' ),
								'param_name'  => 'icon_margin',
								'value'       => 5,
								'min'         => 0,
								'max'         => 100,
								'suffix'      => 'px',
								'description' => __( 'How much distance would you like in two icons?', 'ultimate_vc' ),
								'group'       => 'Other Settings',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '#333333',
								'description' => __( 'Give it a nice paint!', 'ultimate_vc' ),
								'group'       => 'Select Icon',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon Style', 'ultimate_vc' ),
								'param_name'  => 'icon_style',
								'value'       => array(
									__( 'Simple', 'ultimate_vc' ) => 'none',
									__( 'Circle Background', 'ultimate_vc' ) => 'circle',
									__( 'Square Background', 'ultimate_vc' ) => 'square',
									__( 'Design your own', 'ultimate_vc' ) => 'advanced',
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
								'group'       => 'Select Icon',
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
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'       => 'Select Icon',
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
								'group'       => 'Select Icon',
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
								'group'       => 'Select Icon',
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
								'group'       => 'Select Icon',
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
								'group'       => 'Select Icon',
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
								'group'       => 'Select Icon',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'icon_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),
								'group'       => 'Other Settings',
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
								'group'       => 'Other Settings',
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
								'group'       => 'Other Settings',
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
								'group'       => 'Other Settings',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Custom CSS Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Ran out of options? Need more styles? Write your own CSS and mention the class name here.', 'ultimate_vc' ),
								'group'       => 'Select Icon',
							),
						),
					)
				);
			}
		}
		/**
		 * Render function for Icons Block Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ultimate_icons_shortcode( $atts, $content = null ) {
				$ult_icons_setting = shortcode_atts(
					array(
						'align'    => '',
						'el_class' => '',
						'css_icon' => '',
					),
					$atts
				);
			$icon_design_css       = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_icons_setting['css_icon'], ' ' ), 'ultimate_icons', $atts );

			$output  = '<div class="' . esc_attr( $icon_design_css ) . ' ' . esc_attr( $ult_icons_setting['align'] ) . ' uavc-icons ' . esc_attr( $ult_icons_setting['el_class'] ) . '">';
			$output .= do_shortcode( $content );
			$output .= '</div>';

			return $output;
		}
		/**
		 * Render function for Single Icon shortcode Module.
		 *
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function single_icon_shortcode( $atts ) {

			$icon_type              = '';
			$icon_img               = '';
			$img_width              = '';
			$target                 = '';
			$link_title             = '';
			$rel                    = '';
				$ult_icons_settings = shortcode_atts(
					array(
						'icon'                => '',
						'icon_size'           => '',
						'icon_color'          => '',
						'icon_style'          => '',
						'icon_color_bg'       => '',
						'icon_color_border'   => '',
						'icon_border_style'   => '',
						'icon_border_size'    => '',
						'icon_border_radius'  => '',
						'icon_border_spacing' => '',
						'icon_link'           => '',
						'icon_margin'         => '',
						'icon_animation'      => '',
						'tooltip_disp'        => '',
						'tooltip_text'        => '',
						'el_class'            => '',
					),
					$atts
				);
			$ultimate_js            = get_option( 'ultimate_js' );
			if ( isset( $ult_icons_settings['tooltip_disp'] ) && '' != $ult_icons_settings['tooltip_disp'] && 'enable' != $ultimate_js ) {
				wp_enqueue_script( 'ultimate-tooltip' );
			}

			if ( 'none' !== $ult_icons_settings['icon_animation'] ) {
				$css_trans = 'data-animation="' . esc_attr( $ult_icons_settings['icon_animation'] ) . '" data-animation-delay="03"';
			}
			$output           = '';
			$style            = '';
			$link_sufix       = '';
			$link_prefix      = '';
			$target           = '';
			$href             = '';
			$icon_align_style = '';
			$uniqid           = uniqid();
			$href             = vc_build_link( $ult_icons_settings['icon_link'] );
			if ( '' !== $ult_icons_settings['icon_link'] ) {
				if ( null != $href['url'] ) {
					$url          = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target       = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$link_title   = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel          = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
					$link_prefix .= '<a class="aio-tooltip ' . esc_attr( $uniqid ) . '" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' data-toggle="tooltip" data-placement="' . esc_attr( $ult_icons_settings['tooltip_disp'] ) . '" title="' . esc_attr( $ult_icons_settings['tooltip_text'] ) . '">';
					$link_sufix  .= '</a>';
				}
			}
			if ( '' !== $ult_icons_settings['tooltip_disp'] && null == $href['url'] ) {
				$link_prefix .= '<span class="aio-tooltip ' . esc_attr( $uniqid ) . '" data-toggle="tooltip" data-placement="' . esc_attr( $ult_icons_settings['tooltip_disp'] ) . '" title="' . esc_attr( $ult_icons_settings['tooltip_text'] ) . '">';
				$link_sufix  .= '</span>';
			}

			if ( '' !== $ult_icons_settings['icon_color'] ) {
				$style .= 'color:' . $ult_icons_settings['icon_color'] . ';';
			}
			if ( 'none' !== $ult_icons_settings['icon_style'] ) {
				if ( '' !== $ult_icons_settings['icon_color_bg'] ) {
					$style .= 'background:' . $ult_icons_settings['icon_color_bg'] . ';';
				}
			}
			if ( 'advanced' == $ult_icons_settings['icon_style'] ) {
				$style .= 'border-style:' . $ult_icons_settings['icon_border_style'] . ';';
				$style .= 'border-color:' . $ult_icons_settings['icon_color_border'] . ';';
				$style .= 'border-width:' . $ult_icons_settings['icon_border_size'] . 'px;';
				$style .= 'width:' . $ult_icons_settings['icon_border_spacing'] . 'px;';
				$style .= 'height:' . $ult_icons_settings['icon_border_spacing'] . 'px;';
				$style .= 'line-height:' . $ult_icons_settings['icon_border_spacing'] . 'px;';
				$style .= 'border-radius:' . $ult_icons_settings['icon_border_radius'] . 'px;';
			}
			if ( '' !== $ult_icons_settings['icon_size'] ) {
				$style .= 'font-size:' . $ult_icons_settings['icon_size'] . 'px;';
			}

			if ( '' !== $ult_icons_settings['icon_margin'] ) {
				$style .= 'margin-right:' . $ult_icons_settings['icon_margin'] . 'px;';
			}

			if ( '' !== $ult_icons_settings['icon'] ) {
				$output .= "\n" . $link_prefix . '<div class="aio-icon ' . esc_attr( $ult_icons_settings['icon_style'] ) . ' ' . esc_attr( $ult_icons_settings['el_class'] ) . '" ' . $css_trans . ' style="' . esc_attr( $style ) . '">';
				$output .= "\n\t" . '<i class="' . esc_attr( $ult_icons_settings['icon'] ) . '"></i>';
				$output .= "\n" . '</div>' . $link_sufix;
			}
			if ( '' !== $ult_icons_settings['tooltip_disp'] ) {
				$output .= '<script>
					jQuery(function () {
						jQuery(".' . esc_attr( $uniqid ) . '").bsf_tooltip("hide");
					})
				</script>';
			}
			return $output;
		}
	}
}
if ( class_exists( 'Ultimate_Icons' ) ) {
	$ultimate_icons = new Ultimate_Icons();
}
// Extend WPBakeryShortCodesContainer class to inherit all required functionality.
if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Icons' ) ) {
	/**
	 * Function that checks if the class is exists or not.
	 */
	class WPBakeryShortCode_Ultimate_Icons extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Single_Icon' ) ) {
	/**
	 * Function that checks if the class is exists or not.
	 */
	class WPBakeryShortCode_Single_Icon extends WPBakeryShortCode {
	}
}
