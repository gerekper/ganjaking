<?php
/**
 * Add-on Name: Ultimate Ribbon
 * Add-on URI: http://dev.brainstormforce.com
 *
 * @package Ultimate Ribbon
 */

if ( ! class_exists( 'Ultimate_Ribbons' ) ) {
	/**
	 * Function that initializes Ultimate Ribbon Module
	 *
	 * @class Ultimate_Headings
	 */
	class Ultimate_Ribbons {
		/**
		 * Constructor function that constructs default values for the Ultimate Ribbon module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_ribbons_module_init' ) );
			}
			add_shortcode( 'ultimate_ribbon', array( $this, 'ultimate_ribbons_module_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_ribbons_module_assets' ), 1 );
		}//end __construct()

		/**
		 * Function that register styles and scripts for Ultimate Ribbon Module.
		 *
		 * @method register_ribbons_module_assets
		 */
		public function register_ribbons_module_assets() {

			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-ribbons-style', 'ribbon_module' );
		}//end register_ribbons_module_assets()

		/**
		 * Function that initializes settings of Ultimate Ribbon Module.
		 *
		 * @method ultimate_ribbons_module_init
		 */
		public function ultimate_ribbons_module_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Ribbon', 'ultimate_vc' ),
						'base'        => 'ultimate_ribbon',
						'class'       => 'vc_ultimate_ribbon',
						'icon'        => 'vc_ultimate_ribbon',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Design awesome Ribbon styles', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Ribbon Message', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'ribbon_msg',
								'value'       => 'SPECIAL OFFER',
								'group'       => 'Layout',
							),
							array(
								'type'       => 'icon_manager',
								'class'      => '',
								'heading'    => __( 'Left Icon ', 'ultimate_vc' ),
								'param_name' => 'left_icon',
								'value'      => '',
								'group'      => 'Layout',
							),
							array(
								'type'       => 'icon_manager',
								'class'      => '',
								'heading'    => __( 'Right Icon ', 'ultimate_vc' ),
								'param_name' => 'right_icon',
								'value'      => '',
								'group'      => 'Layout',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hide Ribbon Wings', 'ultimate_vc' ),
								'param_name'  => 'ribbon_wings',
								'value'       =>
								array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Small Devices', 'ultimate_vc' ) => 'small',
									__( 'Medium & Small Devices', 'ultimate_vc' ) => 'medium',
								),
								'description' => 'To hide Ribbon Wings on Small or Medium device use this option.',
								'group'       => 'Layout',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Style', 'ultimate_vc' ),
								'param_name'       => 'style_option',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper top-margin vc_column vc_col-sm-12',
								'group'            => 'Layout',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Ribbon Width', 'ultimate_vc' ),
								'param_name' => 'ribbon_width',
								'value'      =>
								array(
									__( 'Auto', 'ultimate_vc' ) => 'auto',
									__( 'Full', 'ultimate_vc' ) => 'full',
									__( 'Custom', 'ultimate_vc' ) => 'custom',
								),
								'group'      => 'Layout',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Custom Width', 'ultimate_vc' ),
								'param_name' => 'custom_width',
								'value'      => '',
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'ribbon_width',
									'value'   => array( 'custom' ),
								),
								'group'      => 'Layout',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'ribbon_alignment',
								'value'      =>
								array(
									__( 'Center', 'ultimate_vc' ) => 'center',
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
								'group'      => 'Layout',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Stitching', 'ultimate_vc' ),
								'param_name'  => 'ribbon_stitching',
								'value'       =>
								array(
									__( 'Yes', 'ultimate_vc' ) => 'yes',
									__( 'No', 'ultimate_vc' ) => 'no',
								),
								'description' => 'To give Stitch effect on Ribbon.',
								'group'       => 'Layout',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Shadow', 'ultimate_vc' ),
								'param_name' => 'rib_shadow',
								'value'      =>
								array(
									__( 'Yes', 'ultimate_vc' ) => 'yes',
									__( 'No', 'ultimate_vc' ) => 'no',
								),
								'group'      => 'Layout',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Ribbon Colors', 'ultimate_vc' ),
								'param_name'       => 'ribbon_option',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper top-margin vc_column vc_col-sm-12',
								'group'            => 'Layout',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Ribbon Color', 'ultimate_vc' ),
								'param_name' => 'ribbon_color',
								'value'      => '',
								'group'      => 'Layout',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon Color', 'ultimate_vc' ),
								'param_name' => 'icon_color',
								'value'      => '',
								'group'      => 'Layout',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Ribbon Fold Color', 'ultimate_vc' ),
								'param_name' => 'rib_fold_color',
								'value'      => '',
								'group'      => 'Layout',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Ribbon Wings Color', 'ultimate_vc' ),
								'param_name' => 'rib_wing_color',
								'value'      => '',
								'group'      => 'Layout',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
								'group'       => 'Layout',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Ribbon Text Settings', 'ultimate_vc' ),
								'param_name'       => 'ribbon_text_typograpy',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
								'group'            => 'Typography',
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Tag', 'ultimate_vc' ),
								'param_name'  => 'heading_tag',
								'value'       => array(
									__( 'Default', 'ultimate_vc' ) => 'h3',
									__( 'H1', 'ultimate_vc' ) => 'h1',
									__( 'H3', 'ultimate_vc' ) => 'h2',
									__( 'H4', 'ultimate_vc' ) => 'h4',
									__( 'H5', 'ultimate_vc' ) => 'h5',
									__( 'H6', 'ultimate_vc' ) => 'h6',
									__( 'Div', 'ultimate_vc' ) => 'div',
									__( 'p', 'ultimate_vc' )  => 'p',
									__( 'span', 'ultimate_vc' ) => 'span',
								),
								'description' => __( 'Default is H3', 'ultimate_vc' ),
								'group'       => 'Typography',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'ribbon_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'ribbon_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'main_ribbon_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'main_ribbon_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Font Color', 'ultimate_vc' ),
								'param_name' => 'ribbon_text_color',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Transform', 'ultimate_vc' ),
								'param_name' => 'ribbontext_trans',
								'value'      => array(
									__( 'Default', 'ultimate_vc' ) => 'unset',
									__( 'UPPERCASE', 'ultimate_vc' ) => 'uppercase',
									__( 'lowercase', 'ultimate_vc' ) => 'lowercase',
									__( 'Capitalize', 'ultimate_vc' ) => 'capitalize',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Letter Spacing', 'ultimate_vc' ),
								'param_name' => 'letter_space',
								'value'      => '',
								'min'        => 1,
								'max'        => 15,
								'suffix'     => 'px',
								'group'      => 'Typography',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_ribbon_design',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}//end ultimate_ribbons_module_init()

		/**
		 * Render function for Ultimate Ribbon Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ultimate_ribbons_module_shortcode( $atts, $content = null ) {
			$rib_width                = '';
			$rib_align                = '';
			$rib_left_color           = '';
			$rib_right_color          = '';
			$ribbon_style_inline      = '';
			$main_ribbon_responsive   = '';
			$main_ribbon_style_inline = '';
			$ribbont_trans            = '';
			$ribbon_spacer            = '';
			$ribbon_design_style_css  = '';
			$ribc_width               = '';
			$rib_media                = '';
			$output                   = '';
				$ult_rib_settings     = shortcode_atts(
					array(
						'ribbon_msg'              => 'SPECIAL OFFER',
						'left_icon'               => '',
						'right_icon'              => '',
						'ribbon_stitching'        => 'yes',
						'ribbon_width'            => 'auto',
						'ribbon_alignment'        => 'center',
						'custom_width'            => '',
						'rib_shadow'              => 'yes',
						'ribbon_color'            => '',
						'icon_color'              => '',
						'rib_wing_color'          => '',
						'ribbon_font_family'      => '',
						'ribbon_style'            => '',
						'rib_fold_color'          => '',
						'main_ribbon_font_size'   => '',
						'main_ribbon_line_height' => '',
						'ribbon_text_color'       => '',
						'ribbontext_trans'        => 'unset',
						'letter_space'            => '',
						'ribbon_wings'            => 'none',
						'el_class'                => '',
						'css_ribbon_design'       => '',
						'heading_tag'             => '',
					),
					$atts
				);
			$vc_version               = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus            = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			// Default Design Editor.
			$ribbon_design_style_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_rib_settings['css_ribbon_design'], ' ' ), 'ultimate_ribbons', $atts );

			$ribbon_design_style_css = esc_attr( $ribbon_design_style_css );

			$micro = wp_rand( 0000, 9999 );
			$id    = uniqid( 'ultimate-ribbon-' . $micro );

			// Style option for Ribbon Module.
			if ( '' == $ult_rib_settings['heading_tag'] ) {
				$ult_rib_settings['heading_tag'] = 'h3';
			}

			if ( '' != $ult_rib_settings['ribbon_alignment'] ) {
				$rib_align = 'text-align:' . esc_attr( $ult_rib_settings['ribbon_alignment'] ) . ';';
			}
			if ( '' != $ult_rib_settings['ribbon_width'] ) {
				if ( 'auto' == $ult_rib_settings['ribbon_width'] ) {
					$rib_width = 'auto';
				} elseif ( 'full' == $ult_rib_settings['ribbon_width'] ) {
					$rib_width .= 'full';
				} elseif ( 'custom' == $ult_rib_settings['ribbon_width'] ) {
					$rib_width .= 'custom';
					$ribc_width = 'width:calc(' . esc_attr( $ult_rib_settings['custom_width'] ) . 'px - 7em)';
				}
			}

			if ( 'none' != $ult_rib_settings['ribbon_wings'] ) {
				$rib_media = 'media-width';
			}

			// Color option for Ribbon Module.
			if ( '' != $ult_rib_settings['ribbon_color'] ) {
				$ult_rib_settings['ribbon_color'] = 'background:' . esc_attr( $ult_rib_settings['ribbon_color'] ) . ';';
			}
			if ( '' != $ult_rib_settings['icon_color'] ) {
				$ult_rib_settings['icon_color'] = 'color:' . esc_attr( $ult_rib_settings['icon_color'] ) . ';';
			}
			if ( '' != $ult_rib_settings['rib_wing_color'] ) {
				$rib_left_color  = 'border-top-color:' . esc_attr( $ult_rib_settings['rib_wing_color'] ) . ';';
				$rib_left_color .= 'border-bottom-color:' . esc_attr( $ult_rib_settings['rib_wing_color'] ) . ';';
				$rib_left_color .= 'border-right-color:' . esc_attr( $ult_rib_settings['rib_wing_color'] ) . ';';

				$rib_right_color  = 'border-top-color:' . esc_attr( $ult_rib_settings['rib_wing_color'] ) . ';';
				$rib_right_color .= 'border-bottom-color:' . esc_attr( $ult_rib_settings['rib_wing_color'] ) . ';';
				$rib_right_color .= 'border-left-color:' . esc_attr( $ult_rib_settings['rib_wing_color'] ) . ';';
			}
			if ( '' != $ult_rib_settings['rib_fold_color'] ) {
				$output .= '<style>
					.' . esc_attr( $id ) . ' .ult-ribbon-text:before, .' . esc_attr( $id ) . ' .ult-ribbon-text:after {
						border-top-color: ' . esc_attr( $ult_rib_settings['rib_fold_color'] ) . ';
						border-right-color: transparent;
						border-bottom-color: transparent;
						border-left-color: transparent;
					}
					</style>';
			}

			/* ---- main heading styles ---- */
			if ( '' != $ult_rib_settings['ribbon_font_family'] ) {
				$mrfont_family = get_ultimate_font_family( $ult_rib_settings['ribbon_font_family'] );
				if ( $mrfont_family ) {
					$ribbon_style_inline .= 'font-family:\'' . $mrfont_family . '\';';
				}
			}
			// main ribbon font style.
			$ribbon_style_inline .= get_ultimate_font_style( $ult_rib_settings['ribbon_style'] );

			// FIX: set old font size before implementing responsive param.
			if ( is_numeric( $ult_rib_settings['main_ribbon_font_size'] ) ) {
				$ult_rib_settings['main_ribbon_font_size'] = 'desktop:' . $ult_rib_settings['main_ribbon_font_size'] . 'px;';       }
			if ( is_numeric( $ult_rib_settings['main_ribbon_line_height'] ) ) {
				$ult_rib_settings['main_ribbon_line_height'] = 'desktop:' . $ult_rib_settings['main_ribbon_line_height'] . 'px;';       }

			// responsive {main} ribbon styles.
			$args                   = array(
				'target'      => '.' . $id . ' .ult-ribbon-text-title',
				'media_sizes' => array(
					'font-size'   => $ult_rib_settings['main_ribbon_font_size'],
					'line-height' => $ult_rib_settings['main_ribbon_line_height'],
				),
			);
			$main_ribbon_responsive = get_ultimate_vc_responsive_media_css( $args );

			// attach font color if set.
			if ( '' != $ult_rib_settings['ribbon_text_color'] ) {
				$main_ribbon_style_inline .= 'color:' . $ult_rib_settings['ribbon_text_color'] . ';';
			}

			// Text -Transform Property for Ribbon Text.
			if ( '' != $ult_rib_settings['ribbontext_trans'] ) {
				$ribbont_trans = 'text-transform: ' . $ult_rib_settings['ribbontext_trans'] . ';';
			}
			// Letter spacing for Ribbon Text.
			if ( '' !== $ult_rib_settings['letter_space'] ) {
					$ribbon_spacer = 'letter-spacing:' . $ult_rib_settings['letter_space'] . 'px';
			}

			$output     .= '<div id="' . esc_attr( $id ) . '" class="ultr-ribbon ' . esc_attr( $ribbon_design_style_css ) . ' ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $id ) . ' ' . esc_attr( $ult_rib_settings['el_class'] ) . '">';
				$output .= '<div class="ult-ribbon-wrap" style= "' . esc_attr( $rib_align ) . '">
					<' . $ult_rib_settings['heading_tag'] . ' class="ult-ribbon ' . esc_attr( $rib_width ) . ' ' . esc_attr( $rib_media ) . '" style="' . esc_attr( $ribc_width ) . '">
						<span class="ult-left-ribb ' . esc_attr( $ult_rib_settings['ribbon_wings'] ) . ' ' . esc_attr( $ult_rib_settings['rib_shadow'] ) . '" style= "' . esc_attr( $rib_left_color ) . '"><i class="' . $ult_rib_settings['left_icon'] . '" style="' . esc_attr( $ult_rib_settings['icon_color'] ) . '"></i></span>
						<span class="ult-ribbon-text ' . esc_attr( $ult_rib_settings['ribbon_wings'] ) . '" style= "' . esc_attr( $ult_rib_settings['ribbon_color'] ) . '">';
			if ( 'yes' == $ult_rib_settings['ribbon_stitching'] ) {
				$output .= '<div class="ult-ribbon-stitches-top"></div>'; }

								$output .= '<span class="ult-ribbon-text-title ult-responsive" ' . $main_ribbon_responsive . ' style="' . esc_attr( $ribbon_style_inline ) . ' ' . esc_attr( $main_ribbon_style_inline ) . ' ' . esc_attr( $ribbont_trans ) . ' ' . esc_attr( $ribbon_spacer ) . '">' . esc_attr( $ult_rib_settings['ribbon_msg'] ) . '</span>';
			if ( 'yes' == $ult_rib_settings['ribbon_stitching'] ) {
				$output .= '<div class="ult-ribbon-stitches-bottom"></div>';}
						$output .= '</span>';
					$output     .= '<span class="ult-right-ribb  ' . esc_attr( $ult_rib_settings['ribbon_wings'] ) . ' ' . esc_attr( $ult_rib_settings['rib_shadow'] ) . '" style= "' . esc_attr( $rib_right_color ) . '"><i class="' . esc_attr( $ult_rib_settings['right_icon'] ) . '" style="' . esc_attr( $ult_rib_settings['icon_color'] ) . '"></i></span>
					</' . $ult_rib_settings['heading_tag'] . '>
				</div>';
			$output             .= '</div>';

			return $output;
		}
	}//end class
	new Ultimate_Ribbons();
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Ribbon' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ultimate_Ribbon extends WPBakeryShortCode {
		}
	}
}
