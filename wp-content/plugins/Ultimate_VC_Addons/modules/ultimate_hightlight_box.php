<?php
/**
 * Add-on Name: Highlight Box.
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Highlight Box
 */

if ( ! class_exists( 'Ultimate_Highlight_Box' ) ) {
	/**
	 * Function that initializes Highlight Box Module
	 *
	 * @class Ultimate_Highlight_Box
	 */
	class Ultimate_Highlight_Box {
		/**
		 * Constructor function that constructs default values for the Highlight Box module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ctaction_init' ) );
			}
			add_shortcode( 'ultimate_ctation', array( $this, 'call_to_action_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_cta_assets' ), 1 );
		}
		/**
		 * Function that register styles and scripts for Highlight Box Module.
		 *
		 * @method register_cta_assets
		 */
		public function register_cta_assets() {
			Ultimate_VC_Addons::ultimate_register_style( 'utl-ctaction-style', 'highlight-box' );

			Ultimate_VC_Addons::ultimate_register_script( 'utl-ctaction-script', 'highlight-box', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}
		/**
		 * Function that initializes settings of Highlight Box Module.
		 *
		 * @method ctaction_init
		 */
		public function ctaction_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'     => __( 'Hightlight Box', 'ultimate_vc' ),
						'base'     => 'ultimate_ctation',
						'class'    => 'vc_ctaction_icon',
						'icon'     => 'vc_icon_ctaction',
						'category' => 'Ultimate VC Addons',
						'params'   => array(
							array(
								'type'             => 'textarea_html',
								'class'            => '',
								'heading'          => __( 'Text ', 'ultimate_vc' ),
								'param_name'       => 'content',
								'admin_label'      => true,
								'value'            => '',
								'edit_field_class' => 'ult_hide_editor_fullscreen vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'content_alignment',
								'value'      => array(
									__( 'Center', 'ultimate_vc' ) => 'ctaction-text-center',
									__( 'Left', 'ultimate_vc' ) => 'ctaction-text-left',
									__( 'Right', 'ultimate_vc' ) => 'ctaction-text-right',
								),
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Background', 'ultimate_vc' ),
								'param_name' => 'ctaction_background',
								'value'      => '#e74c3c',
								'group'      => 'Background',
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Background Hover', 'ultimate_vc' ),
								'param_name' => 'ctaction_background_hover',
								'value'      => '#c0392b',
								'group'      => 'Background',
							),
							array(
								'type'       => 'vc_link',
								'param_name' => 'ctaction_link',
								'heading'    => __( 'Link', 'ultimate_vc' ),
							),
							array(
								'type'       => 'ult_switch',
								'heading'    => __( 'Enable Icon', 'ultimate_vc' ),
								'param_name' => 'enable_icon',
								'value'      => '',
								'options'    => array(
									'enable_icon_value' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'group'      => 'Icon',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Effect', 'ultimate_vc' ),
								'param_name'  => 'effect',
								'value'       => array(
									__( 'Slide Left', 'ultimate_vc' ) => 'right-push',
									__( 'Slide Right', 'ultimate_vc' ) => 'left-push',
									__( 'Slide Top', 'ultimate_vc' ) => 'bottom-push',
									__( 'Slide Bottom', 'ultimate_vc' ) => 'top-push',
								),
								'description' => __( 'Select an effect for highlight box.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'enable_icon',
									'value'   => array( 'enable_icon_value' ),
								),
								'group'       => 'Icon',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
									__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
								),
								'description' => __( 'Use an existing font icon or upload a custom image.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'enable_icon',
									'value'   => array( 'enable_icon_value' ),
								),
								'group'       => 'Icon',
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
								'group'       => 'Icon',
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
								'group'       => 'Icon',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '',
								'description' => __( 'Give it a nice paint!', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Icon',
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
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Icon',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_bg',
								'value'       => '',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'       => 'Icon',
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
								'group'       => 'Icon',
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
								'group'       => 'Icon',
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
								'group'       => 'Icon',
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
								'group'       => 'Icon',
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
									'element' => 'icon_style',
									'value'   => array( 'advanced' ),
								),
								'group'       => 'Icon',
							),
							array(
								'type'        => 'attach_image',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'smile' ),
								'param_name'  => 'icon_img',
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => 'Icon',
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
								'group'       => 'Icon',
							),

							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'text_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'text_font_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'text_font_size',
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
								'param_name' => 'text_color',
								'value'      => '#ffffff',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'text_line_height',
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
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Width Override', 'ultimate_vc' ),
								'param_name'  => 'ctaction_override',
								'value'       => array(
									__( 'Default Width', 'ultimate_vc' ) => '0',
									__( "Apply 1st parent element's width", 'ultimate_vc' ) => '1',
									__( "Apply 2nd parent element's width", 'ultimate_vc' ) => '2',
									__( "Apply 3rd parent element's width", 'ultimate_vc' ) => '3',
									__( "Apply 4th parent element's width", 'ultimate_vc' ) => '4',
									__( "Apply 5th parent element's width", 'ultimate_vc' ) => '5',
									__( "Apply 6th parent element's width", 'ultimate_vc' ) => '6',
									__( "Apply 7th parent element's width", 'ultimate_vc' ) => '7',
									__( "Apply 8th parent element's width", 'ultimate_vc' ) => '8',
									__( "Apply 9th parent element's width", 'ultimate_vc' ) => '9',
									__( 'Full Width', 'ultimate_vc' ) => 'full',
									__( 'Maximum Full Width', 'ultimate_vc' ) => 'ex-full',
								),
								'description' => __( "By default, the map will be given to the WPBakery Page Builder row. However, in some cases depending on your theme's CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output..", 'ultimate_vc' ),
							),
							array(
								'type'             => 'number',
								'heading'          => __( 'Top Padding', 'ultimate_vc' ),
								'param_name'       => 'ctaction_padding_top',
								'edit_field_class' => 'vc_column vc_col-sm-3',
								'value'            => '20',
								'suffix'           => 'px',
								'group'            => 'Background',
							),
							array(
								'type'             => 'number',
								'heading'          => __( 'Bottom Padding', 'ultimate_vc' ),
								'param_name'       => 'ctaction_padding_bottom',
								'edit_field_class' => 'vc_column vc_col-sm-3',
								'value'            => '20',
								'suffix'           => 'px',
								'group'            => 'Background',
							),
							array(
								'type'             => 'number',
								'heading'          => __( 'Left Padding', 'ultimate_vc' ),
								'param_name'       => 'ctaction_padding_left',
								'edit_field_class' => 'vc_column vc_col-sm-3',
								'value'            => '',
								'suffix'           => 'px',
								'group'            => 'Background',
							),
							array(
								'type'             => 'number',
								'heading'          => __( 'Right Padding', 'ultimate_vc' ),
								'param_name'       => 'ctaction_padding_right',
								'edit_field_class' => 'vc_column vc_col-sm-3',
								'value'            => '',
								'suffix'           => 'px',
								'group'            => 'Background',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => 'Margin ',
								'param_name'  => 'highlight_margin',
								'mode'        => 'margin',
								'unit'        => 'px',
								'positions'   => array(
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing to FlipBox.', 'ultimate_vc' ),
							),
						),
					)
				);
			}
		}
		/**
		 * Render function for Highlight Box Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function call_to_action_shortcode( $atts, $content ) {
			$output                = '';
			$style                 = '';
			$data                  = '';
			$text_style_inline     = '';
			$ctaction_link_html    = '';
			$icon_inline           = '';
			$target                = '';
			$link_title            = '';
			$rel                   = '';
				$ult_high_settings = shortcode_atts(
					array(
						'content_alignment'         => 'ctaction-text-center',
						'ctaction_background'       => '#e74c3c',
						'ctaction_background_hover' => '#c0392b',
						'text_font_family'          => '',
						'text_font_style'           => '',
						'text_font_size'            => '32',
						'text_color'                => '#ffffff',
						'text_line_height'          => '',
						'ctaction_link'             => '',
						'ctaction_override'         => '0',
						'ctaction_padding_top'      => '20',
						'ctaction_padding_bottom'   => '20',
						'ctaction_padding_left'     => '',
						'ctaction_padding_right'    => '',
						'enable_icon'               => '',
						'icon_type'                 => 'selector',
						'icon'                      => '',
						'icon_color'                => '',
						'icon_style'                => 'none',
						'icon_color_bg'             => '',
						'icon_border_style'         => '',
						'icon_color_border'         => '#333333',
						'icon_border_size'          => '1',
						'icon_border_radius'        => '500',
						'icon_border_spacing'       => '50',
						'icon_img'                  => '',
						'img_width'                 => '48',
						'icon_size'                 => '32',
						'effect'                    => 'right-push',
						'el_class'                  => '',
						'highlight_margin'          => '',
					),
					$atts
				);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$ult_high_settings['el_class'] .= ' ' . $ult_high_settings['content_alignment'];

			/* typography */

			if ( '' != $ult_high_settings['text_font_family'] ) {
				$temp               = get_ultimate_font_family( $ult_high_settings['text_font_family'] );
				$text_style_inline .= 'font-family:' . $temp . ';';
			}

			$text_style_inline .= get_ultimate_font_style( $ult_high_settings['text_font_style'] );

			// responsive param.
			if ( is_numeric( $ult_high_settings['text_font_size'] ) ) {
				$ult_high_settings['text_font_size'] = 'desktop:' . $ult_high_settings['text_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_high_settings['text_line_height'] ) ) {
				$ult_high_settings['text_line_height'] = 'desktop:' . $ult_high_settings['text_line_height'] . 'px;';
			}
			$highlight_box_id   = 'highlight-box-wrap-' . wp_rand( 1000, 9999 );
			$highlight_box_args = array(
				'target'      => '#' . $highlight_box_id .
				'', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_high_settings['text_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_high_settings['text_line_height'],
				),
			);
			$data_list          = get_ultimate_vc_responsive_media_css( $highlight_box_args );

			if ( '' != $ult_high_settings['text_color'] ) {
				$text_style_inline .= 'color:' . $ult_high_settings['text_color'] . ';';
			}

			if ( '' != $ult_high_settings['ctaction_background'] ) {
				$data              .= ' data-background="' . esc_attr( $ult_high_settings['ctaction_background'] ) . '" ';
				$text_style_inline .= 'background:' . $ult_high_settings['ctaction_background'] . ';';
			}
			if ( '' != $ult_high_settings['ctaction_background_hover'] ) {
				$data .= ' data-background-hover="' . esc_attr( $ult_high_settings['ctaction_background_hover'] ) . '" ';
			}

			$data .= ' data-override="' . esc_attr( $ult_high_settings['ctaction_override'] ) . '" ';

			if ( '' != $ult_high_settings['ctaction_padding_top'] ) {
				$text_style_inline .= 'padding-top:' . $ult_high_settings['ctaction_padding_top'] . 'px;';
			}
			if ( '' != $ult_high_settings['ctaction_padding_bottom'] ) {
				$text_style_inline .= 'padding-bottom:' . $ult_high_settings['ctaction_padding_bottom'] . 'px;';
			}
			if ( '' != $ult_high_settings['ctaction_padding_left'] ) {
				$text_style_inline .= 'padding-left:' . $ult_high_settings['ctaction_padding_left'] . 'px;';
			}
			if ( '' != $ult_high_settings['ctaction_padding_right'] ) {
				$text_style_inline .= 'padding-right:' . $ult_high_settings['ctaction_padding_right'] . 'px;';
			}

			$text_style_inline .= $ult_high_settings['highlight_margin'];

			if ( '' != $ult_high_settings['ctaction_link'] ) {
				$href = vc_build_link( $ult_high_settings['ctaction_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
				if ( '' != $url ) {
					$ctaction_link_html = '<a class="ulimate-call-to-action-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '></a>';
				}
			}

			if ( 'enable_icon_value' == $ult_high_settings['enable_icon'] ) {
				$icon_inline = do_shortcode( '[just_icon icon_align="center" icon_type="' . esc_attr( $ult_high_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_high_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_high_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_high_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_high_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_high_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_high_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_high_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_high_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_high_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_high_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_high_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_high_settings['icon_border_spacing'] ) . '"]' );
			} else {
				$ult_high_settings['effect'] = 'no-effect';
			}

			$output .= '<div id="' . esc_attr( $highlight_box_id ) . '" ' . $data_list . ' class="ultimate-call-to-action ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $ult_high_settings['el_class'] ) . ' ult-responsive" style="' . esc_attr( $text_style_inline ) . '" ' . $data . '>';

			if ( '' != $icon_inline ) {
				$output .= '<div class="ultimate-ctaction-icon ctaction-icon-' . esc_attr( $ult_high_settings['effect'] ) . '">' . $icon_inline . '</div>';
			}
				$output .= '<div class="uvc-ctaction-data uvc-ctaction-data-' . esc_attr( $ult_high_settings['effect'] ) . ' ult-responsive">' . do_shortcode( $content ) . '</div>';
			$output     .= $ctaction_link_html . '</div>';
			$is_preset   = false; // Display settings for Preset.
			if ( isset( $_GET['preset'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
				$is_preset = true;
			}
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
if ( class_exists( 'Ultimate_Highlight_Box' ) ) {
	$ultimate_highlight_box = new Ultimate_Highlight_Box();
}

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Ctation' ) ) {
	/**
	 * Function that checks if the class is exists or not.
	 */
	class WPBakeryShortCode_Ultimate_Ctation extends WPBakeryShortCode {
	}
}


