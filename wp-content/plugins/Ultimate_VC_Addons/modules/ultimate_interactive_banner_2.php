<?php
/**
 * Add-on Name: Interactive Banner - 2
 *
 * @package Interactive Banner - 2
 */

if ( ! class_exists( 'Ultimate_Interactive_Banner' ) ) {
	/**
	 * Class Ultimate_Interactive_Banner.
	 *
	 * @class Ultimate_Interactive_Banner
	 */
	class Ultimate_Interactive_Banner {
		/**
		 * Constructor function that constructs default values for the Ultimate_Info_Table.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'banner_init' ) );
			}
			add_shortcode( 'interactive_banner_2', array( $this, 'banner_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_ib2_banner_assets' ), 1 );
		}
		/**
		 *  Function Interactive Banner 2 assets.
		 *
		 * @method register_ib_banner_assets
		 */
		public function register_ib2_banner_assets() {
			Ultimate_VC_Addons::ultimate_register_style( 'ult-ib2-style', 'ib2-style' );
		}
		/**
		 *  Init function.
		 *
		 * @method banner_init
		 */
		public function banner_init() {
			if ( function_exists( 'vc_map' ) ) {
				$json = ultimate_get_banner2_json();
				vc_map(
					array(
						'name'        => __( 'Interactive Banner 2', 'ultimate_vc' ),
						'base'        => 'interactive_banner_2',
						'class'       => 'vc_interactive_icon',
						'icon'        => 'vc_icon_interactive',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Displays the banner image with Information.', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'             => 'textfield',
								'class'            => '',
								'heading'          => __( 'Title ', 'ultimate_vc' ),
								'param_name'       => 'banner_title',
								'admin_label'      => true,
								'value'            => '',
								'description'      => __( 'Give a title to this banner.', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-8',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => __( 'Tag', 'ultimate_vc' ),
								'param_name'       => 'heading_tag',
								'value'            => array(
									__( 'Default', 'ultimate_vc' ) => 'h2',
									__( 'H1', 'ultimate_vc' ) => 'h1',
									__( 'H3', 'ultimate_vc' ) => 'h3',
									__( 'H4', 'ultimate_vc' ) => 'h4',
									__( 'H5', 'ultimate_vc' ) => 'h5',
									__( 'H6', 'ultimate_vc' ) => 'h6',
									__( 'Div', 'ultimate_vc' ) => 'div',
									__( 'p', 'ultimate_vc' )  => 'p',
									__( 'span', 'ultimate_vc' ) => 'span',
								),
								'description'      => __( 'Default is H2.', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-padding-remove vc_col-sm-4',
							),
							array(
								'type'        => 'textarea',
								'class'       => '',
								'heading'     => __( 'Description', 'ultimate_vc' ),
								'param_name'  => 'banner_desc',
								'value'       => '',
								'description' => __( 'Text that comes on mouse hover.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Banner Image', 'ultimate_vc' ),
								'param_name'  => 'banner_image',
								'value'       => '',
								'description' => __( 'Upload the image for this banner.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'banner_link',
								'value'       => '',
								'description' => __( 'Add link / select existing page to link to this banner.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Link Delay', 'ultimate_vc' ),
								'param_name'  => 'delay_seconds',
								'value'       => '200',
								'suffix'      => 'ms',
								'description' => __( 'Add time delay for redirecting the links on touch devices.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_select2',
								'class'       => '',
								'heading'     => __( 'Styles ', 'ultimate_vc' ),
								'param_name'  => 'banner_style',
								'value'       => '',
								'json'        => $json,
								'description' => '',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Banner Height', 'ultimate_vc' ),
								'param_name'  => 'banner_min_height_op',
								'description' => '',
								'value'       => array(
									__( 'Default', 'ultimate_vc' ) => 'default',
									__( 'Custom', 'ultimate_vc' ) => 'custom',
								),
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Min Height', 'ultimate_vc' ),
								'param_name' => 'banner_min',
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'banner_min_height_op',
									'value'   => array( 'custom' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Title Background Color', 'ultimate_vc' ),
								'param_name'  => 'banner_title_bg',
								'value'       => '',
								'description' => '',
								'dependency'  => array(
									'element' => 'banner_style',
									'value'   => array( 'style5' ),
								),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the icon process, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'heading'          => __( 'Title Settings', 'ultimate_vc' ),
								'param_name'       => 'banner_title_typograpy',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'smile' ),
								'param_name'  => 'banner_title_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'banner_title_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'banner_title_font_size',
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
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'banner_title_line_height',
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
								'type'             => 'ult_param_heading',
								'heading'          => __( 'Description Settings', 'ultimate_vc' ),
								'param_name'       => 'banner_desc_typograpy',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'smile' ),
								'param_name'  => 'banner_desc_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'banner_desc_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'banner_desc_font_size',
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
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'banner_desc_line_height',
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
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Title Color', 'ultimate_vc' ),
								'param_name'  => 'banner_color_title',
								'value'       => '',
								'description' => '',
								'group'       => 'Color Settings',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Description Color', 'ultimate_vc' ),
								'param_name'  => 'banner_color_desc',
								'value'       => '',
								'description' => '',
								'group'       => 'Color Settings',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'banner_color_bg',
								'value'       => '',
								'description' => '',
								'group'       => 'Color Settings',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Opacity', 'ultimate_vc' ),
								'param_name'  => 'image_opacity',
								'value'       => 1,
								'min'         => 0.0,
								'max'         => 1.0,
								'step'        => 0.1,
								'suffix'      => '',
								'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'ultimate_vc' ),
								'group'       => 'Color Settings',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Opacity on Hover', 'ultimate_vc' ),
								'param_name'  => 'image_opacity_on_hover',
								'value'       => 1,
								'min'         => 0.0,
								'max'         => 1.0,
								'step'        => 0.1,
								'suffix'      => '',
								'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'ultimate_vc' ),
								'group'       => 'Color Settings',
							),
							array(
								'type'        => 'checkbox',
								'class'       => '',
								'heading'     => __( 'Responsive Nature', 'ultimate_vc' ),
								'param_name'  => 'enable_responsive',
								'value'       => array( 'Enable Responsive Behaviour' => 'yes' ),
								'description' => __( 'If the description text is not suiting well on specific screen sizes, you may enable this option - which will hide the description text.', 'ultimate_vc' ),
								'group'       => 'Responsive',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Minimum Screen Size', 'ultimate_vc' ),
								'param_name'  => 'responsive_min',
								'value'       => 768,
								'min'         => 100,
								'max'         => 1000,
								'suffix'      => 'px',
								'dependency'  => array(
									'element' => 'enable_responsive',
									'value'   => 'yes',
								),
								'description' => __( 'Provide the range of screen size where you would like to hide the description text.', 'ultimate_vc' ),
								'group'       => 'Responsive',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Maximum Screen Size', 'ultimate_vc' ),
								'param_name'  => 'responsive_max',
								'value'       => 900,
								'min'         => 100,
								'max'         => 1000,
								'suffix'      => 'px',
								'dependency'  => array(
									'element' => 'enable_responsive',
									'value'   => 'yes',
								),
								'description' => __( 'Provide the range of screen size where you would like to hide the description text.', 'ultimate_vc' ),
								'group'       => 'Responsive',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/n8o33' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_ib2',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}
		/**
		 * Shortcode handler function for stats banner.
		 *
		 * @param array $atts Attributes.
		 */
		public function banner_shortcode( $atts ) {
			$banner_title                      = '';
			$banner_desc                       = '';
			$banner_style                      = '';
			$banner_title_style                = '';
			$banner_desc_style                 = '';
			$banner_title_style_inline         = '';
			$banner_desc_style_inline          = '';
			$banner_min                        = '';
			$target                            = '';
			$link_title                        = '';
			$rel                               = '';
			$ult_interactive_banner_2_settings = shortcode_atts(
				array(
					'banner_title'             => '',
					'banner_desc'              => '',
					'banner_title_location'    => '',
					'banner_image'             => '',
					'image_opacity'            => '1',
					'image_opacity_on_hover'   => '1',
					'banner_height'            => '',
					'banner_height_val'        => '',
					'banner_link'              => '',
					'delay_seconds'            => '200',
					'banner_style'             => '',
					'banner_title_font_family' => '',
					'banner_title_style'       => '',
					'banner_title_font_size'   => '',
					'banner_title_line_height' => '',
					'banner_desc_font_family'  => '',
					'banner_desc_style'        => '',
					'banner_desc_font_size'    => '',
					'banner_desc_line_height'  => '',
					'banner_color_bg'          => '',
					'banner_color_title'       => '',
					'banner_color_desc'        => '',
					'banner_title_bg'          => 'banner_style',
					'enable_responsive'        => 'yes',
					'responsive_min'           => '768',
					'responsive_max'           => '900',
					'banner_min'               => '',
					'banner_min_height_op'     => 'default',
					'el_class'                 => '',
					'heading_tag'              => '',
					'css_ib2'                  => '',
				),
				$atts
			);

			$css_ib2_styles = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_interactive_banner_2_settings['css_ib2'], ' ' ), 'interactive_banner_2', $atts );
			$css_ib2_styles = esc_attr( $css_ib2_styles );

			$output              = '';
			$style               = '';
			$target              = '';
			$link                = '';
			$url                 = '';
			$banner_style_inline = '';
			$title_bg            = '';
			$img_style           = '';
			$responsive          = '';
			$target              = '';

			if ( 'yes' == $ult_interactive_banner_2_settings['enable_responsive'] ) {
				$responsive                                    .= 'data-min-width="' . esc_attr( $ult_interactive_banner_2_settings['responsive_min'] ) . '" data-max-width="' . esc_attr( $ult_interactive_banner_2_settings['responsive_max'] ) . '"';
				$ult_interactive_banner_2_settings['el_class'] .= ' ult-ib-resp';
			}

			if ( '' !== $ult_interactive_banner_2_settings['banner_title_bg'] && 'style5' == $ult_interactive_banner_2_settings['banner_style'] ) {
				$title_bg .= 'background:' . $ult_interactive_banner_2_settings['banner_title_bg'] . ';';
			}

			$img = apply_filters( 'ult_get_img_single', $ult_interactive_banner_2_settings['banner_image'], 'url' );
			$alt = apply_filters( 'ult_get_img_single', $ult_interactive_banner_2_settings['banner_image'], 'alt' );
			if ( '' !== $ult_interactive_banner_2_settings['banner_link'] ) {
				$href = vc_build_link( $ult_interactive_banner_2_settings['banner_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
			} else {
				$link = '#';
			}

			if ( '' != $ult_interactive_banner_2_settings['banner_title_font_family'] ) {
				$bfamily = get_ultimate_font_family( $ult_interactive_banner_2_settings['banner_title_font_family'] );
				if ( '' != $bfamily ) {
					$banner_title_style_inline = 'font-family:\'' . $bfamily . '\';';
				}
			}
			$banner_title_style_inline .= get_ultimate_font_style( $ult_interactive_banner_2_settings['banner_title_style'] );

			if ( is_numeric( $ult_interactive_banner_2_settings['banner_title_font_size'] ) ) {
				$ult_interactive_banner_2_settings['banner_title_font_size'] = 'desktop:' . $ult_interactive_banner_2_settings['banner_title_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_interactive_banner_2_settings['banner_title_line_height'] ) ) {
				$ult_interactive_banner_2_settings['banner_title_line_height'] = 'desktop:' . $ult_interactive_banner_2_settings['banner_title_line_height'] . 'px;';
			}

			$interactive_banner_id = 'interactive-banner-wrap-' . wp_rand( 1000, 9999 );

			$interactive_banner_args = array(
				'target'      => '#' . $interactive_banner_id . ' .ult-new-ib-title', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_interactive_banner_2_settings['banner_title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_interactive_banner_2_settings['banner_title_line_height'],
				),
			);

			$interactive_banner_data_list = get_ultimate_vc_responsive_media_css( $interactive_banner_args );

			if ( '' != $ult_interactive_banner_2_settings['banner_desc_font_family'] ) {
				$bdfamily = get_ultimate_font_family( $ult_interactive_banner_2_settings['banner_desc_font_family'] );
				if ( '' != $bdfamily ) {
					$banner_desc_style_inline = 'font-family:\'' . $bdfamily . '\';';
				}
			}
			$banner_desc_style_inline .= get_ultimate_font_style( $ult_interactive_banner_2_settings['banner_desc_style'] );

			if ( is_numeric( $ult_interactive_banner_2_settings['banner_desc_font_size'] ) ) {
				$ult_interactive_banner_2_settings['banner_desc_font_size'] = 'desktop:' . $ult_interactive_banner_2_settings['banner_desc_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_interactive_banner_2_settings['banner_desc_line_height'] ) ) {
				$ult_interactive_banner_2_settings['banner_desc_line_height'] = 'desktop:' . $ult_interactive_banner_2_settings['banner_desc_line_height'] . 'px;';
			}
			$interactive_banner_desc_args = array(
				'target'      => '#' . $interactive_banner_id . ' .ult-new-ib-content', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_interactive_banner_2_settings['banner_desc_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_interactive_banner_2_settings['banner_desc_line_height'],
				),
			);

			$interactive_banner_desc_data_list = get_ultimate_vc_responsive_media_css( $interactive_banner_desc_args );

			if ( '' != $ult_interactive_banner_2_settings['banner_color_bg'] ) {
				$banner_style_inline .= 'background:' . $ult_interactive_banner_2_settings['banner_color_bg'] . ';';
			}

			$banner_min_height = '';
			$img_min_height    = '';
			$img_max_height    = '';
			$min_height_class  = '';
			if ( '' != $ult_interactive_banner_2_settings['banner_min_height_op'] && 'custom' == $ult_interactive_banner_2_settings['banner_min_height_op'] ) {
				if ( '' != $ult_interactive_banner_2_settings['banner_min'] ) {
					$banner_min_height = ' data-min-height="' . esc_attr( $ult_interactive_banner_2_settings['banner_min'] ) . '" ';
					$img_min_height    = ' data-min-height="' . esc_attr( $ult_interactive_banner_2_settings['banner_min'] ) . '" ';

					$min_height_class     = 'ult-ib2-min-height';
					$banner_style_inline .= ' opacity:0; ';
				}
			}

			$delay_on_touch_devices = '';
			if ( '' !== $ult_interactive_banner_2_settings['delay_seconds'] && '200' !== $ult_interactive_banner_2_settings['delay_seconds'] ) {
				$delay_on_touch_devices = 'data-touch-delay="' . $ult_interactive_banner_2_settings['delay_seconds'] . '"';
			}

			if ( '' != $ult_interactive_banner_2_settings['banner_color_title'] ) {
				$banner_title_style_inline .= 'color:' . $ult_interactive_banner_2_settings['banner_color_title'] . ';';
			}

			if ( 'span' == $ult_interactive_banner_2_settings['heading_tag'] ) {
				$banner_title_style_inline .= 'display: block';
			}

			if ( '' != $ult_interactive_banner_2_settings['banner_color_desc'] ) {
				$banner_desc_style_inline .= 'color:' . $ult_interactive_banner_2_settings['banner_color_desc'] . ';';
			}

			if ( '' !== $ult_interactive_banner_2_settings['image_opacity'] ) {
				$img_style .= 'opacity:' . $ult_interactive_banner_2_settings['image_opacity'] . ';';
			}
			if ( '#' !== $link ) {
				$href = 'href="' . esc_url( $link ) . '"';
			} else {
				$href = '';
			}

			$ult_interactive_banner_2_settings['heading_tag'] = ( isset( $ult_interactive_banner_2_settings['heading_tag'] ) && trim( $ult_interactive_banner_2_settings['heading_tag'] ) != '' ) ? $ult_interactive_banner_2_settings['heading_tag'] : 'h2';

			$output .= '<div class="ult-new-ib ult-ib-effect-' . esc_attr( $ult_interactive_banner_2_settings['banner_style'] ) . ' ' . esc_attr( $ult_interactive_banner_2_settings['el_class'] ) . ' ' . esc_attr( $min_height_class ) . ' ' . esc_attr( $css_ib2_styles ) . '" ' . $responsive . ' style="' . esc_attr( $banner_style_inline ) . '" data-opacity="' . esc_attr( $ult_interactive_banner_2_settings['image_opacity'] ) . '" data-hover-opacity="' . esc_attr( $ult_interactive_banner_2_settings['image_opacity_on_hover'] ) . '" ' . $banner_min_height . '>';
			if ( '' !== $img ) {
				$output .= '<img class="ult-new-ib-img" style="' . esc_attr( $img_style ) . '" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" ' . $img_min_height . ' ' . $img_max_height . ' />';
			}
			$output .= '<div id="' . esc_attr( $interactive_banner_id ) . '" class="ult-new-ib-desc" style="' . esc_attr( $title_bg ) . '">';
			$output .= '<' . $ult_interactive_banner_2_settings['heading_tag'] . ' class="ult-new-ib-title ult-responsive" ' . $interactive_banner_data_list . ' style="' . esc_attr( $banner_title_style_inline ) . '">' . $ult_interactive_banner_2_settings['banner_title'] . '</' . $ult_interactive_banner_2_settings['heading_tag'] . '>';
			if ( '' != $ult_interactive_banner_2_settings['banner_desc'] ) {
				$output .= '<div class="ult-new-ib-content ult-responsive" ' . $interactive_banner_desc_data_list . ' style="' . esc_attr( $banner_desc_style_inline ) . '"><p>' . $ult_interactive_banner_2_settings['banner_desc'] . '</p></div>';
			}
			$output .= '</div>';
			if ( '' != $href ) {
				$output .= '<a class="ult-new-ib-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' ' . $delay_on_touch_devices . '></a>';
			}
			$output   .= '</div>';
			$is_preset = false; // Display settings for Preset.
			if ( isset( $_GET['preset'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
			$output .= '<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					$(".ult-new-ib").css("opacity","1");
				});
			})(jQuery);
			</script>';
			return $output;
		}
	}
}
if ( class_exists( 'Ultimate_Interactive_Banner' ) ) {
	$ultimate_interactive_banner = new Ultimate_Interactive_Banner();
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Interactive_Banner_2' ) ) {
	/**
	 * Class WPBakeryShortCode_Interactive_Banner_2
	 */
	class WPBakeryShortCode_Interactive_Banner_2 extends WPBakeryShortCode {
	}
}
