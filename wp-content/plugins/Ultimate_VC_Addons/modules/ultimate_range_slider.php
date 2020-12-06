<?php
/**
 * Add-on Name: Range Slider
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Range Slider
 */

if ( ! class_exists( 'Ultimate_Range_Slider' ) ) {
	/**
	 * Function that initializes Range Slider Module
	 *
	 * @class Ultimate_Range_Slider
	 */
	class Ultimate_Range_Slider {
		/**
		 * Constructor function that constructs default values for the Range Slider module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'init_range_slider' ) );
			}
			add_shortcode( 'ult_range_slider', array( $this, 'ult_range_slider_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'range_slider_scripts' ), 1 );
		}
		/**
		 * Render function for Range Slider Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ult_range_slider_shortcode( $atts, $content = null ) {
				$ult_range_settings = shortcode_atts(
					array(
						'slider_color'      => '',
						'title_box_color'   => '',
						'title_border'      => 'border-style:solid;|border-width:2px;border-radius:0px;|border-color:#30eae9;',
						'title_box'         => '',
						'title_box_width'   => '',
						'title_box_height'  => '',
						'title_padding'     => '',
						'el_class'          => '',
						'slider_data'       => '',
						'slider_bar_size'   => '',
						'title_font'        => '',
						'title_font_size'   => '',
						'title_line_height' => '',
						'title_color'       => '',
						'title_font_style'  => '',
						'desc_font'         => '',
						'desc_font_size'    => '',
						'desc_line_height'  => '',
						'desc_color'        => '',
						'desc_font_style'   => '',
						'desc_width'        => '',
						'adaptive_height'   => '',
						'desc_padding'      => '',
						'desc_margin'       => '',
					),
					$atts
				);

			$steps_count = 0;
			$title_count = 1;
			$desc_count  = 1;
			$output      = '';

			// slider color setting setting.
			$slider_color_data  = '';
			$slider_color_data .= '' != $ult_range_settings['slider_color'] ? " data-slider_color = '" . esc_attr( $ult_range_settings['slider_color'] ) . "'" : '';

			// Title box style.
			$title_box_data  = '' != $ult_range_settings['title_box_color'] ? 'data-title-background = ' . esc_attr( $ult_range_settings['title_box_color'] ) . ' ' : '';
			$title_box_data .= '' != $ult_range_settings['title_box'] ? ' data-title-box = ' . esc_attr( $ult_range_settings['title_box'] ) . ' ' : ' data-title-box = auto ';
			$title_style     = " style = '"; // title box style var.
			$arrow_style     = '';

			$none_style = 'ult-tooltip-border ult-arrow';

			if ( strpos( $ult_range_settings['title_border'], 'none' ) !== false ) {
				$none_style = '';
			} else {
				$arrow_style = " data-arrow = '";

				if ( strpos( $ult_range_settings['title_border'], 'border-width:0px' ) !== false ) {
					$none_style = '';
				}

				$temp_array   = array();
				$temp_border  = str_replace( '|', '', $ult_range_settings['title_border'] );
				$title_style .= $temp_border;
				$title_style .= '' == $ult_range_settings['title_box_color'] ? '' : 'background:' . $ult_range_settings['title_box_color'] . '; ';
				$temp_array   = explode( ';', $temp_border );

				if ( is_array( $temp_array ) ) {
					foreach ( $temp_array as $key => $value ) {
						if ( strpos( $value, 'border-width:' ) !== false ) {
							$value        = str_replace( 'border-width:', '', $value );
							$value        = str_replace( 'px', '', $value );
							$value        = $value + 7;
							$arrow_style .= ' border-width:' . $value . 'px; ';
							$arrow_style .= 'margin-left:-' . $value . 'px; ';
						} elseif ( strpos( $value, 'border-color:' ) !== false ) {
							$value        = str_replace( 'border-color', 'border-top-color', $value );
							$value        = $value . '; ';
							$arrow_style .= $value;
						}
					}
				}
				$arrow_style .= "'";
			}

			$title_style .= '' !== $ult_range_settings['title_padding'] ? ' ' . $ult_range_settings['title_padding'] . ';' : '';
			// title box custom width.
			$center_class       = '';
			$title_box_width_t  = '';
			$title_box_height_t = '';
			$slider_padding     = '';
			if ( 'custom' == $ult_range_settings['title_box'] ) {
				$center_class = ' ult-tooltip-center';

				$title_box_width_t  = '' !== $ult_range_settings['title_box_width'] ? $ult_range_settings['title_box_width'] : '115';
				$title_box_height_t = '' !== $ult_range_settings['title_box_height'] ? $ult_range_settings['title_box_height'] : '115';

				$title_style .= ' width:' . $title_box_width_t . 'px;';
				$title_style .= ' height:' . $title_box_height_t . 'px;';

				// apply this time padding to slider for tooltip adjustment.
				$slider_padding = ' style = "padding:' . $title_box_height_t . 'px ' . ( ( $title_box_width_t / 2 ) + 10 ) . 'px 35px;"';

			}

			// slider size data.
			$ult_range_settings['slider_bar_size'] = '' != $ult_range_settings['slider_bar_size'] ? ' ' . $ult_range_settings['slider_bar_size'] . 'px;' : '';
			$slider_size_data                      = '' != $ult_range_settings['slider_bar_size'] ? ' data-slider_size = "' . esc_attr( $ult_range_settings['slider_bar_size'] ) . 'px"' : '';

			// title data.
			$ult_range_settings['slider_data'] = json_decode( urldecode( $ult_range_settings['slider_data'] ), true );

			// min max value data.
			if ( isset( $ult_range_settings['slider_data'] ) ) {
				foreach ( $ult_range_settings['slider_data'] as $slider_datas ) {
					++$steps_count;
				}
			}

			$steps_data = ' data-slider_steps = "' . esc_attr( $steps_count ) . '"';

			// typography data.

			// title.
			if ( '' !== $ult_range_settings['title_font'] ) {
				$title_font_family = function_exists( 'get_ultimate_font_family' ) ? get_ultimate_font_family( $ult_range_settings['title_font'] ) : '';
				$title_style      .= 'font-family:' . $title_font_family . ';';
			}
			if ( '' !== $ult_range_settings['title_font_style'] ) {
				$title_style .= $ult_range_settings['title_font_style'];
			}

			$micro = wp_rand( 0000, 9999 );
			$id    = uniqid( 'ultimate-range-slider' . $micro );
			$uid   = 'urs-' . wp_rand( 0000, 9999 );

			// FIX: set old font size before implementing responsive param.
			if ( is_numeric( $ult_range_settings['title_font_size'] ) ) {
				$ult_range_settings['title_font_size'] = 'desktop:' . $ult_range_settings['title_font_size'] . 'px;';       }
			if ( is_numeric( $ult_range_settings['title_line_height'] ) ) {
				$ult_range_settings['title_line_height'] = 'desktop:' . $ult_range_settings['title_line_height'] . 'px;';       }
			// responsive {main} heading styles.
			$args             = array(
				'target'      => '#' . $id . ' .ult-content',
				'media_sizes' => array(
					'font-size'   => $ult_range_settings['title_font_size'],
					'line-height' => $ult_range_settings['title_line_height'],
				),
			);
			$title_responsive = get_ultimate_vc_responsive_media_css( $args );
			$title_style     .= '' !== $ult_range_settings['title_color'] ? 'color:' . $ult_range_settings['title_color'] . ';' : '';
			$title_style     .= "'";

			$desc_style = " style = '";

			if ( '' !== $ult_range_settings['desc_font'] ) {
				$desc_font_family = function_exists( 'get_ultimate_font_family' ) ? get_ultimate_font_family( $ult_range_settings['desc_font'] ) : '';
				$desc_style      .= 'font-family:' . $desc_font_family . ';';
			}
			if ( '' !== $ult_range_settings['desc_font_style'] ) {
				$desc_style .= $ult_range_settings['desc_font_style'];
			}

			if ( is_numeric( $ult_range_settings['desc_font_size'] ) ) {
				$ult_range_settings['desc_font_size'] = 'desktop:' . $ult_range_settings['desc_font_size'] . 'px;';     }
			if ( is_numeric( $ult_range_settings['desc_line_height'] ) ) {
				$ult_range_settings['desc_line_height'] = 'desktop:' . $ult_range_settings['desc_line_height'] . 'px;';     }
			// responsive {main} heading styles.
			$args = array(
				'target'      => '#' . $id . ' .ult-description',
				'media_sizes' => array(
					'font-size'   => $ult_range_settings['desc_font_size'],
					'line-height' => $ult_range_settings['desc_line_height'],
				),
			);

			$desc_responsive = get_ultimate_vc_responsive_media_css( $args );
			$desc_style     .= '' !== $ult_range_settings['desc_color'] ? 'color:' . $ult_range_settings['desc_color'] . ';' : '';

			// design data.
			$desc_style .= '' !== $ult_range_settings['desc_padding'] ? ' ' . $ult_range_settings['desc_padding'] . ';' : '';
			$desc_style .= '' !== $ult_range_settings['desc_margin'] ? ' ' . $ult_range_settings['desc_margin'] . ';' : '';
			$desc_style .= '' !== $ult_range_settings['desc_width'] ? ' width:' . $ult_range_settings['desc_width'] . 'px;' : '';
			$desc_style .= "'";

			$desc_data = '' !== $ult_range_settings['adaptive_height'] ? ' data-adaptive_height = ' . esc_attr( $ult_range_settings['adaptive_height'] ) . ' ' : '';

			// typogrphy data end.
			$output .= '<div id="' . esc_attr( $id ) . '" class="ult-rs-wrapper"><div id="ult-range-slider " ' . $slider_padding . ' class="ult-rslider-container ult-responsive ' . esc_attr( $ult_range_settings['el_class'] ) . '" ' . $steps_data . $slider_color_data . $slider_size_data . $arrow_style . $title_box_data . $desc_data . ' ' . $title_responsive . '>';
			if ( isset( $ult_range_settings['slider_data'] ) ) {
				foreach ( $ult_range_settings['slider_data'] as $slider_datas ) {

					if ( isset( $slider_datas['slider_title'] ) ) {

						// $output .= '<div class = "ult-tooltip ult-title'.$title_count.'" ><div class = "ult-tooltip-inner"'.$title_style.'>'.$slider_datas["slider_title"]; " '.$main_heading_responsive
						// $output .= '</div></div>';

						$output .= '<div class = "ult-tooltip ' . esc_attr( $none_style ) . ' ult-title' . esc_attr( $title_count ) . '" ' . $title_style . '><span class="ult-content ' . esc_attr( $center_class ) . '">' . $slider_datas['slider_title'];
						$output .= '</span></div>';
					}
					++$title_count;
				}
			}

			$output .= '<div class = "ult-rslider" style = "width:' . esc_attr( $ult_range_settings['slider_bar_size'] ) . '" ></div>';
			$output .= '</div>';
				// description data.

			if ( isset( $ult_range_settings['slider_data'] ) ) {
				$output .= '<div class="ult-desc-wrap ult-responsive "' . $desc_responsive . '>';
				foreach ( $ult_range_settings['slider_data'] as $slider_datas ) {

					if ( isset( $slider_datas['slider_desc'] ) ) {

						$output .= '<div class = "ult-description ult-desc' . esc_attr( $desc_count ) . '"' . $desc_style . ' >' . $slider_datas['slider_desc'] . '</div>';
					}
					++$desc_count;
				}
				$output .= '</div>';
			}

			$output .= '</div>'; // wrapper div close.
			return $output;
		}
		/**
		 * Function that initializes settings of Range Slider Module.
		 *
		 * @method init_range_slider
		 */
		public function init_range_slider() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'            => __( 'Range Slider', 'ultimate_vc' ),
						'base'            => 'ult_range_slider',
						'icon'            => 'vc_icon_range_slider',
						'class'           => '',
						'content_element' => true,
						'controls'        => 'full',
						'category'        => 'Ultimate VC Addons',
						'description'     => __( 'Create creative range sliders.', 'ultimate_vc' ),
						'params'          => array(
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>Slider Content</h4>', 'ultimate_vc' ),
								'value'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'param_group',
								'heading'     => __( 'Slider Text Setting', 'ultimate_vc' ),
								'param_name'  => 'slider_data',
								'description' => __( 'Add content here steps will generate based on content', 'ultimate_vc' ),
								'value'       => rawurlencode(
									wp_json_encode(
										array(
											array(
												'slider_title' => '',
												'slider_desc'  => '',

											),
										)
									)
								),
								'params'      => array(

									array(
										'type'        => 'textfield',
										'heading'     => __( 'Slider Title', 'ultimate_vc' ),
										'param_name'  => 'slider_title',
										'description' => '',
										'admin_label' => true,
									),
									array(
										'type'        => 'textarea',
										'heading'     => __( 'Slider Description', 'ultimate_vc' ),
										'param_name'  => 'slider_desc',
										'value'       => '',
										'description' => '',

									),
								),
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>Slider Bar Color</h4>', 'ultimate_vc' ),
								'value'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Slider Bar Color', 'ultimate_vc' ),
								'param_name'  => 'slider_color',
								'value'       => '#3BF7D1',
								'description' => '',
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>Slider Bar Width</h4>', 'ultimate_vc' ),
								'value'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),

							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Slider Width', 'ultimate_vc' ),
								'param_name'  => 'slider_bar_size',
								'value'       => '',
								'suffix'      => 'px',
								'description' => __( 'If title box text or width is too long then slider width will reduce according to title box width', 'ultimate_vc' ),
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>Extra Class</h4>', 'ultimate_vc' ),
								'value'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>Title Box</h4>', 'ultimate_vc' ),
								'value'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Title Box',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Title Color', 'ultimate_vc' ),
								'param_name'  => 'title_color',
								'value'       => '#444444',
								'description' => '',
								'group'       => 'Title Box',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'title_box_color',
								'value'       => '#fff',
								'description' => '',
								'group'       => 'Title Box',
							),
							array(
								'type'       => 'ultimate_border',
								'heading'    => __( 'Title Box Border', 'ultimate_vc' ),
								'param_name' => 'title_border',
								'unit'       => 'px',
								'value'      => 'border-style:solid;|border-width:2px;border-radius:0px;|border-color:#30eae9;',                                 // [required] px,em,%,all     Default all
								'group'      => 'Title Box',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )     => '',
									__( 'Right', 'ultimate_vc' )   => '',
									__( 'Bottom', 'ultimate_vc' )  => '',
									__( 'Left', 'ultimate_vc' )    => '',
								),
								'radius'     => array(
									__( 'Top Left', 'ultimate_vc' ) => '',
									__( 'Top Right', 'ultimate_vc' )    => '',
									__( 'Bottom Right', 'ultimate_vc' ) => '',
									__( 'Bottom Left', 'ultimate_vc' )  => '',
								),
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Title Box Padding', 'ultimate_vc' ),
								'param_name' => 'title_padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )  => '15',
									__( 'Right', 'ultimate_vc' ) => '15',
									__( 'Bottom', 'ultimate_vc' ) => '15',
									__( 'Left', 'ultimate_vc' ) => '15',
								),
								'group'      => 'Title Box',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Title Box Size', 'ultimate_vc' ),
								'param_name'  => 'title_box',
								'value'       => array(
									'Auto'   => 'auto',
									'Custom' => 'custom',
								),
								'description' => __( 'Set Title Box Size', 'ultimate_vc' ),
								'group'       => 'Title Box',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Width', 'ultimate_vc' ),
								'param_name'  => 'title_box_width',
								'value'       => '115',
								'suffix'      => 'px',
								'description' => __( 'Ex: 20px', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'title_box',
									'value'   => 'custom',
								),
								'group'       => 'Title Box',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Height', 'ultimate_vc' ),
								'param_name'  => 'title_box_height',
								'value'       => '115',
								'suffix'      => 'px',
								'description' => __( 'Ex: 20px ', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'title_box',
									'value'   => 'custom',
								),
								'group'       => 'Title Box',
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>Description Design Setting</h4>', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Description',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),

							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Description Color', 'ultimate_vc' ),
								'param_name'  => 'desc_color',
								'value'       => '#444',
								'description' => '',
								'group'       => 'Description',
							),
							array(
								'type'       => 'number',
								'param_name' => 'desc_width',
								'heading'    => __( 'Width', 'ultimate_vc' ),
								'value'      => '',
								'suffix'     => 'px',
								'group'      => 'Description',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Adaptive Height', 'ultimate_vc' ),
								'param_name'  => 'adaptive_height',
								'value'       => '',
								'options'     => array(
									'on' => array(
										'label' => __( 'Turn on Adaptive Height', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => __( 'If you have different height of descriptions. It will automatically adapt the maximum height.  ', 'smile' ),
								'dependency'  => '',
								'group'       => 'Description',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'desc_padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )  => '35',
									__( 'Right', 'ultimate_vc' ) => '35',
									__( 'Bottom', 'ultimate_vc' ) => '35',
									__( 'Left', 'ultimate_vc' ) => '35',
								),
								'group'      => 'Description',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Margin', 'ultimate_vc' ),
								'param_name' => 'desc_margin',
								'mode'       => 'margin',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
								),
								'group'      => 'Description',
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>For Title</h4>', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'title_font',
								'value'       => '',
								'description' => __( "Click and select font of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-google-font-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'title_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'title_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '16',
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
								'param_name' => 'title_line_height',
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
								'type'             => 'text',
								'param_name'       => 'title_typography',
								'heading'          => __( '<h4>For Description</h4>', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'desc_font',
								'value'       => '',
								'description' => __( "Click and select font of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-google-font-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'desc_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'desc_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '16',
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
								'param_name' => 'desc_line_height',
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
						),
					)
				); // vc_map() end.
			} //vc_map function check.
		} // init_range_slider() end.
		/**
		 * Function that register styles and scripts for Range Slider Module.
		 *
		 * @method range_slider_scripts
		 */
		public function range_slider_scripts() {

			Ultimate_VC_Addons::ultimate_register_style( 'ult_range_slider_css', 'range-slider' );

			Ultimate_VC_Addons::ultimate_register_script( 'ult_range_slider_js', 'range-slider', false, array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-slider' ), ULTIMATE_VERSION, true );

			Ultimate_VC_Addons::ultimate_register_script( 'ult_ui_touch_punch', 'range-slider-touch-punch', false, array( 'jquery', 'jquery-ui-widget', 'jquery-ui-mouse' ), ULTIMATE_VERSION, true );

			// jquery.ui.labeledslider.
			Ultimate_VC_Addons::ultimate_register_script( 'ult_range_tick', 'jquery-ui-labeledslider', false, array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-slider' ), ULTIMATE_VERSION, false );
		}
	}
	new Ultimate_Range_Slider();

	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ult_Range_Slider' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Range_Slider extends WPBakeryShortCode {
		}
	}
}
