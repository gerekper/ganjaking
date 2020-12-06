<?php
/**
 * Add-on Name: Ultimate Hotspot
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Ultimate Hotspot
 */

if ( ! class_exists( 'ULT_HotSpot' ) ) {
	/**
	 * Function that initializes Ultimate Hotspot Module
	 *
	 * @class ULT_HotSpot
	 */
	class ULT_HotSpot {
		/**
		 * Constructor function that constructs default values for the Ultimate Hotspot module.
		 *
		 * @method __construct
		 */
		public function __construct() {

			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				// We safely integrate with VC with this hook.
				add_action( 'init', array( $this, 'ult_hotspot_init' ), 99 );
			}

			// Use this when creating a shortcode addon.
			add_shortcode( 'ult_hotspot', array( $this, 'ult_hotspot_callback' ) );
			add_shortcode( 'ult_hotspot_items', array( $this, 'ult_hotspot_items_callback' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ), 999 );

			// Register CSS and JS.
			add_action( 'wp_enqueue_scripts', array( $this, 'ult_hotspot_scripts' ), 1 );

			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ultimate_hotspot_param', array( $this, 'ultimate_hotspot_param_callback' ), UAVC_URL . 'admin/vc_extend/js/vc-hotspot-param.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ultimate_hotspot_param', array( $this, 'ultimate_hotspot_param_callback' ), UAVC_URL . 'admin/vc_extend/js/vc-hotspot-param.js' );
				}
			}
		}
		/**
		 * Render function for Ultimate Hotspot Param callback.
		 *
		 * @param array  $settings represts module attribuits.
		 * @param string $value value has been set to null.
		 * @access public
		 */
		public function ultimate_hotspot_param_callback( $settings, $value ) {
			$dependency  = '';
			$class       = isset( $settings['class'] ) ? $settings['class'] : '';
			$output      = '<div class="ult-hotspot-image-wrapper ' . esc_attr( $class ) . '">';
				$output .= '<img src="" class="ult-hotspot-image" alt="image"/>';
				$output .= '<div class="ult-hotspot-draggable"></div>';
				$output .= '<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" value="' . esc_attr( $value ) . '" class="ult-hotspot-positions wpb_vc_param_value" ' . $dependency . '/>';
			$output     .= '</div>';
			return $output;
		}
		/**
		 * Function that register styles and scripts for Ultimate Hotspot Module.
		 *
		 * @param array $hook has a file type.
		 * @method enqueue_admin_assets
		 */
		public function enqueue_admin_assets( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook || 'edit.php' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_register_script( 'hotspt-admin-js', UAVC_URL . 'admin/vc_extend/js/admin_enqueue_js.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), ULTIMATE_VERSION, true );
					wp_enqueue_script( 'hotspt-admin-js' );
				}
			}
		}
		/**
		 * Render function for Ultimate Hotspot Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ult_hotspot_callback( $atts, $content = null ) {

			global $tooltip_continuous_animation;

				$ult_hots_setting = shortcode_atts(
					array(
						'main_img'       => '',
						'main_img_size'  => '',
						'main_img_width' => '',
						'el_class'       => '',
					),
					$atts
				);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$content = wpb_js_remove_wpautop( $content, true ); // fix unclosed/unwanted paragraph tags in $content.

			$mnimg = '';
			$alt   = '';
			if ( '' !== $ult_hots_setting['main_img'] ) {
				$mnimg = apply_filters( 'ult_get_img_single', $ult_hots_setting['main_img'], 'url' );
				$alt   = apply_filters( 'ult_get_img_single', $ult_hots_setting['main_img'], 'alt' );
			}
			$cust_size = '';
			if ( 'main_img_custom' == $ult_hots_setting['main_img_size'] ) {
				if ( '' != $ult_hots_setting['main_img_width'] ) {
					$cust_size .= 'width:' . $ult_hots_setting['main_img_width'] . 'px;';
				}
			}
			$output  = "<div class='ult_hotspot_container " . esc_attr( $is_vc_49_plus ) . ' ult-hotspot-tooltip-wrapper ' . esc_attr( $ult_hots_setting['el_class'] ) . "' style=" . esc_attr( $cust_size ) . '>';
			$output .= "  <img class='ult_hotspot_image' src=" . esc_url( apply_filters( 'ultimate_images', $mnimg ) ) . " alt='" . esc_attr( $alt ) . "'/>";
			$output .= "     <div class='utl-hotspot-items ult-hotspot-item'>" . do_shortcode( $content ) . '</div>';
			$output .= "     <div style='color:#000;' data-image='" . esc_attr( $GLOBALS['hotspot_icon'] ) . ' ' . esc_attr( $GLOBALS['hotspot_icon_bg_color'] ) . ' ' . esc_attr( $GLOBALS['hotspot_icon_color'] ) . ' ' . esc_attr( $GLOBALS['hotspot_icon_size'] ) . ' ' . esc_attr( $GLOBALS['tooltip_continuous_animation'] ) . "'></div>";
			$output .= '</div>';
			return $output;
		}
		/**
		 * Function that initializes settings of Ultimate Hotspot Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @method ult_hotspot_items_callback
		 */
		public function ult_hotspot_items_callback( $atts, $content = null ) {
			global $hotspot_icon, $hotspot_icon_bg_color, $hotspot_icon_color, $hotspot_icon_size;

				$ult_hots_settings = shortcode_atts(
					array(
						'hotspot_content'              => '',
						'hotspot_label'                => '',
						'hotspot_position'             => '0,0',
						'tooltip_content'              => '',
						'tooltip_width'                => '300',
						'tooltip_padding'              => '',
						'tooltip_position'             => '',
						'icon_type'                    => '',
						'icon'                         => 'Defaults-map-marker',
						'icon_color'                   => '',
						'icon_style'                   => '',
						'icon_color_bg'                => '',
						'icon_border_style'            => '',
						'icon_color_border'            => '',
						'icon_border_size'             => '',
						'icon_border_radius'           => '',
						'icon_border_spacing'          => '',
						'icon_img'                     => '',
						'img_width'                    => '60',
						'link_style'                   => '',
						'icon_link'                    => '',
						'icon_size'                    => '',
						'alignment'                    => 'center',
						'tooltip_trigger'              => '',
						'tooltip_animation'            => '',
						'tooltip_continuous_animation' => '',
						'glow_color'                   => '',
						'enable_bubble_arrow'          => 'on',
						'tooltip_custom_bg_color'      => '#fff',
						'tooltip_custom_color'         => '#4c4c4c',
						'tooltip_font'                 => '',
						'tooltip_font_style'           => '',
						'tooltip_font_size'            => '',
						'tooltip_font_line_height'     => '',
						'tooltip_custom_border_size'   => '',
						'tooltip_align'                => '',
						'el_sub_class'                 => '',
					),
					$atts
				);

			// Animation effects.
			$glow  = '';
			$pulse = '';
			if ( '' != $ult_hots_settings['tooltip_continuous_animation'] ) {

				switch ( $ult_hots_settings['tooltip_continuous_animation'] ) {
					case 'on':
								$pulse = 'ult-pulse';
						break;
					case 'glow':
						if ( '' !== $ult_hots_settings['glow_color'] ) {
							$ult_hots_settings['glow_color'] = 'style=background-color:' . $ult_hots_settings['glow_color'] . ';';
						} else {
							$ult_hots_settings['glow_color'] = '';
						}
								$glow = " <div class='ult-glow' " . esc_attr( $ult_hots_settings['glow_color'] ) . '></div>';
						break;
				}
			}

			if ( trim( $content ) !== '' ) {
				$ult_hots_settings['hotspot_content'] = $content;
			}

			/**    Tooltip [Content] Styling
			 *--------------------------------------*/
			$font_args             = array();
			$tooltip_content_style = '';
			$tooltip_base_style    = '';
			$hotspot_tooltip_id    = '';
			if ( '' != $ult_hots_settings['tooltip_font'] ) {
				$font_family            = get_ultimate_font_family( $ult_hots_settings['tooltip_font'] );
				$tooltip_content_style .= 'font-family:' . $font_family . ';';
				array_push( $font_args, $ult_hots_settings['tooltip_font'] );
			}
			if ( '' != $ult_hots_settings['tooltip_font_style'] ) {
				$tooltip_content_style .= get_ultimate_font_style( $ult_hots_settings['tooltip_font_style'] );
			}

			if ( is_numeric( $ult_hots_settings['tooltip_font_size'] ) ) {
				$ult_hots_settings['tooltip_font_size'] = 'desktop:' . $ult_hots_settings['tooltip_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_hots_settings['tooltip_font_line_height'] ) ) {
				$ult_hots_settings['tooltip_font_line_height'] = 'desktop:' . $ult_hots_settings['tooltip_font_line_height'] . 'px;';
			}

			$hotspot_tooltip_id = 'hotspot-tooltip-' . wp_rand( 1000, 9999 );

			$hotspot_tooltip_args = array(
				'target'      => '#' . $hotspot_tooltip_id . ' .ult-tooltipster-content', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_hots_settings['tooltip_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_hots_settings['tooltip_font_line_height'],
				),
			);

			$hotspot_tooltip_data_list = get_ultimate_vc_responsive_media_css( $hotspot_tooltip_args );

			// Width.
			if ( '' != $ult_hots_settings['tooltip_width'] ) {
				$tooltip_content_style .= 'width:' . $ult_hots_settings['tooltip_width'] . 'px;'; }

			// Padding.
			if ( '' != $ult_hots_settings['tooltip_padding'] ) {
				$tooltip_content_style .= $ult_hots_settings['tooltip_padding']; }

			/**
			 *    Tooltip [Base] Styling options
			 */
			// Background.
			if ( '' != $ult_hots_settings['tooltip_custom_bg_color'] ) {
				$tooltip_base_style .= 'background-color:' . $ult_hots_settings['tooltip_custom_bg_color'] . ';';
			}
			if ( '' != $ult_hots_settings['tooltip_custom_color'] ) {
				$tooltip_base_style .= 'color:' . $ult_hots_settings['tooltip_custom_color'] . ';'; }

			// Border Styling.
			if ( '' != $ult_hots_settings['tooltip_custom_border_size'] ) {
				$bstyle              = str_replace( '|', '', $ult_hots_settings['tooltip_custom_border_size'] );
				$tooltip_base_style .= $bstyle;
			}
			if ( '' != $ult_hots_settings['tooltip_align'] ) {
				$tooltip_base_style .= 'text-align:' . $ult_hots_settings['tooltip_align'] . ';';
			}

			$data = '';

			if ( '' != $hotspot_tooltip_id ) {
				$data .= 'data-mycust-id="' . esc_attr( $hotspot_tooltip_id ) . '" ';}
			if ( '' != $hotspot_tooltip_data_list ) {
				$data .= $hotspot_tooltip_data_list;}
			if ( '' != $tooltip_content_style ) {
				$data .= 'data-tooltip-content-style="' . esc_attr( $tooltip_content_style ) . '"'; }
			if ( '' != $tooltip_base_style ) {
				$data .= 'data-tooltip-base-style="' . esc_attr( $tooltip_base_style ) . '"'; }

			if ( '' != $ult_hots_settings['enable_bubble_arrow'] && 'on' == $ult_hots_settings['enable_bubble_arrow'] ) {
				$data .= ' data-bubble-arrow="true" ';
			} else {
				$data .= ' data-bubble-arrow="false" ';
			}

			$ult_hots_settings['hotspot_position'] = explode( ',', $ult_hots_settings['hotspot_position'] );
			if ( 'custom' == $ult_hots_settings['icon_type'] ) {
				$temp_icon_size = ( $ult_hots_settings['img_width'] / 2 );
			} else {
				$temp_icon_size = ( $ult_hots_settings['icon_size'] / 2 );
			}

			$hotspot_x_position = $ult_hots_settings['hotspot_position'][0];
			$hotspot_y_position = ( isset( $ult_hots_settings['hotspot_position'][1] ) ) ? $ult_hots_settings['hotspot_position'][1] : '0';
			$tooltip_offsety    = '';

				// set offsetY for tooltip.
				$tooltip_offsety = $temp_icon_size;

			if ( '' != $ult_hots_settings['tooltip_animation'] ) {
				$data .= 'data-tooltipanimation="' . esc_attr( $ult_hots_settings['tooltip_animation'] ) . '"';}
			if ( '' != $ult_hots_settings['tooltip_trigger'] ) {
				$data .= 'data-trigger="' . esc_attr( $ult_hots_settings['tooltip_trigger'] ) . '"';}
			if ( '' != $tooltip_offsety ) {
				$data .= 'data-tooltip-offsety="' . esc_attr( $tooltip_offsety ) . '"';}
			if ( '' != $ult_hots_settings['tooltip_position'] ) {
				$data .= 'data-arrowposition="' . esc_attr( $ult_hots_settings['tooltip_position'] ) . '"';}

			$icon_animation = '';
			$icon_inline    = do_shortcode( '[just_icon icon_align="' . esc_attr( $ult_hots_settings['alignment'] ) . '" icon_type="' . esc_attr( $ult_hots_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_hots_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_hots_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_hots_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_hots_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_hots_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_hots_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_hots_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_hots_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_hots_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_hots_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_hots_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_hots_settings['icon_border_spacing'] ) . '" icon_animation="' . esc_attr( $icon_animation ) . '"]' );

			$url        = '';
			$link_title = '';
			$target     = '';
			$rel        = '';

			// Hotspot has simple link.
			if ( 'link' == $ult_hots_settings['link_style'] && '' != $ult_hots_settings['icon_link'] ) {
				$href = vc_build_link( $ult_hots_settings['icon_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
			}

			$output  = "<div class='ult-hotspot-item " . esc_attr( $pulse ) . ' ' . esc_attr( $ult_hots_settings['el_sub_class'] ) . "' style='top:-webkit-calc(" . esc_attr( $hotspot_x_position ) . '% - ' . esc_attr( $temp_icon_size ) . 'px);top:-moz-calc(' . esc_attr( $hotspot_x_position ) . '% - ' . esc_attr( $temp_icon_size ) . 'px);top:calc(' . esc_attr( $hotspot_x_position ) . '% - ' . esc_attr( $temp_icon_size ) . 'px);left: -webkit-calc(' . esc_attr( $hotspot_y_position ) . '% - ' . esc_attr( $temp_icon_size ) . 'px);left: -moz-calc(' . esc_attr( $hotspot_y_position ) . '% - ' . esc_attr( $temp_icon_size ) . 'px);left: calc(' . esc_attr( $hotspot_y_position ) . '% - ' . esc_attr( $temp_icon_size ) . "px);' >";
			$output .= "  <div style='z-index: 39;position: relative;'>";

			if ( 'link' == $ult_hots_settings['link_style'] ) {
				$output .= "   <a data-link_style='simple' class='ult-tooltipstered ult-hotspot-tooltip' " . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . " data-status='hide'>";
				$output .= $icon_inline;
				$output .= '  </a>';
			} else {
				$output     .= "   <a data-link_style='tootip' " . $data . " class='ult-tooltipstered ult-hotspot-tooltip' href='#' data-status='show'>";
					$output .= $icon_inline;
					$output .= "<span class='hotspot-tooltip-content'>" . esc_html( str_replace( '"', '\'', $ult_hots_settings['hotspot_content'] ) ) . '</span>';
				$output     .= '  </a>';
			}

			$output .= ' </div><!-- ICON WRAP -->';

			$output .= $glow;

			$output .= '</div>';
			return $output;
		}
		/**
		 * Function that initializes settings of Ultimate Hotspot Module.
		 *
		 * @method ult_hotspot_init
		 */
		public function ult_hotspot_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Hotspot', 'ultimate_vc' ),
						'base'                    => 'ult_hotspot',
						'as_parent'               => array( 'only' => 'ult_hotspot_items' ),
						'content_element'         => true,
						'show_settings_on_create' => true,
						'category'                => 'Ultimate VC Addons',
						'icon'                    => 'ult_hotspot',
						'class'                   => 'ult_hotspot',
						'description'             => __( 'Display Hotspot on Image.', 'ultimate_vc' ),
						'params'                  => array(
							array(
								'type'       => 'ult_img_single',
								'class'      => '',
								'heading'    => __( 'Select Hotspot Image', 'ultimate_vc' ),
								'param_name' => 'main_img',
								'value'      => '',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Image Size', 'ultimate_vc' ),
								'param_name' => 'main_img_size',
								'value'      => array(
									'Default / Full Size' => 'main_img_original',
									'Custom'              => 'main_img_custom',
								),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Image Width', 'ultimate_vc' ),
								'class'      => '',
								'value'      => '',
								'suffix'     => 'px',
								'param_name' => 'main_img_width',
								'dependency' => array(
									'element' => 'main_img_size',
									'value'   => 'main_img_custom',
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra Class Name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'Ran out of options? Need more styles? Write your own CSS and mention the class name here.', 'ultimate_vc' ),
							),
						),
						'js_view'                 => 'ULTHotspotContainerView',
					)
				);

				global $ultimate_hostspot_image;
				vc_map(
					array(
						'name'            => __( 'Hotspot Item', 'ultimate_vc' ),
						'base'            => 'ult_hotspot_items',
						'content_element' => true,
						'as_child'        => array( 'only' => 'ult_hotspot' ),
						'icon'            => 'ult_hotspot',
						'class'           => 'ult_hotspot',
						'js_view'         => 'ULTHotspotSingleView',
						'is_container'    => false,
						'params'          => array(
							array(
								'type'       => 'ultimate_hotspot_param',
								'heading'    => 'Position',
								'param_name' => 'hotspot_position',
							),
							// Hotspot Icon.
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
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
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

							// link style.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'On Click HotSpot Item:', 'ultimate_vc' ),
								'param_name'  => 'link_style',
								'value'       => array(
									__( 'Tooltip', 'ultimate_vc' ) => 'tooltip',
									__( 'Simple Link', 'ultimate_vc' ) => 'link',
								),
								'description' => __( 'Display tooltip or just link for the hotspot icon.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'icon_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'link_style',
									'value'   => 'link',
								),
							),
							// Link style.
							// TOOLTIP.
							array(
								'type'             => 'textarea_html',
								'class'            => '',
								'value'            => 'Tooltip content goes here!',
								'heading'          => __( 'Hotspot Tooltip Content', 'ultimate_vc' ),
								'param_name'       => 'content',
								'dependency'       => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
								'admin_label'      => true,
								'edit_field_class' => 'ult_hide_editor_fullscreen vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra Class Name', 'ultimate_vc' ),
								'param_name'  => 'el_sub_class',
								'description' => __( 'Ran out of options? Need more styles? Write your own CSS and mention the class name here.', 'ultimate_vc' ),
							),
							// Tooltip.
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Tooltip Text Color', 'ultimate_vc' ),
								'param_name' => 'tooltip_custom_color',
								'value'      => '#4c4c4c',
								'group'      => 'Tooltip',
								'dependency' => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'param_name' => 'tooltip_custom_bg_color',
								'value'      => '#fff',
								'dependency' => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
								'group'      => 'Tooltip',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'value'       => '300',
								'heading'     => __( 'Width', 'ultimate_vc' ),
								'param_name'  => 'tooltip_width',
								'group'       => 'Tooltip',
								'suffix'      => 'px',
								'description' => __( 'Tooltip Default width: auto.', 'ultimate' ),
								'dependency'  => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Trigger On', 'ultimate_vc' ),
								'param_name' => 'tooltip_trigger',
								'value'      => array(
									'Hover' => 'hover',
									'Click' => 'click',
								),
								'group'      => 'Tooltip',
								'dependency' => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Position', 'ultimate_vc' ),
								'param_name' => 'tooltip_position',
								'value'      => array(
									__( 'Top', 'ultimate_vc' ) => 'top',
									__( 'Bottom', 'ultimate_vc' ) => 'bottom',
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
								'group'      => 'Tooltip',
								'dependency' => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),

							array(
								'type'         => 'ultimate_border',
								'heading'      => __( 'Border', 'ultimate_vc' ),
								'param_name'   => 'tooltip_custom_border_size',
								'unit'         => 'px',
								'positions'    => array(
									__( 'Top', 'ultimate_vc' )     => '1',
									__( 'Right', 'ultimate_vc' )   => '1',
									__( 'Bottom', 'ultimate_vc' )  => '1',
									__( 'Left', 'ultimate_vc' )    => '1',
								),
								'radius'       => array(
									__( 'Top Left', 'ultimate_vc' ) => '3',
									__( 'Top Right', 'ultimate_vc' )    => '3',
									__( 'Bottom Right', 'ultimate_vc' ) => '3',
									__( 'Bottom Left', 'ultimate_vc' )  => '3',
								),
								'label_color'  => __( 'Border Color', 'ultimate_vc' ),
								'label_radius' => __( 'Border Radius', 'ultimate_vc' ),
								'group'        => 'Tooltip',
								'dependency'   => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Arrow', 'ultimate_vc' ),
								'param_name'  => 'enable_bubble_arrow',
								'value'       => 'on',
								'default_set' => true,
								'options'     => array(
									'on' => array(
										'label' => __( 'Enable Tooltip Arrow?', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'group'       => 'Tooltip',
								'dependency'  => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),

							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Appear Animation', 'ultimate_vc' ),
								'param_name' => 'tooltip_animation',
								'value'      => array(
									__( 'Fade', 'ultimate_vc' ) => 'fade',
									__( 'Grow', 'ultimate_vc' ) => 'glow',
									__( 'Swing', 'ultimate_vc' ) => 'swing',
									__( 'Slide', 'ultimate_vc' ) => 'slide',
									__( 'Fall', 'ultimate_vc' ) => 'fall',
									__( 'Euclid', 'ultimate_vc' ) => 'euclid',
								),
								'group'      => 'Tooltip',
								'dependency' => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'tooltip_padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' ) => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
								'group'      => 'Tooltip',
								'dependency' => array(
									'element' => 'link_style',
									'value'   => 'tooltip',
								),
							),
							// Typography.
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'tooltip_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'tooltip_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'tooltip_font_size',
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
								'param_name' => 'tooltip_font_line_height',
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
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Text Align', 'ultimate_vc' ),
								'param_name' => 'tooltip_align',
								'value'      => array(
									__( 'Left', 'ultimate_vc' )    => 'left',
									__( 'Center', 'ultimate_vc' )  => 'center',
									__( 'Right', 'ultimate_vc' )   => 'right',
									__( 'Justify', 'ultimate_vc' )     => 'justify',
								),
								'group'      => 'Typography',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Animation For Hotspot', 'ultimate_vc' ),
								'param_name'  => 'tooltip_continuous_animation',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Pulse', 'ultimate_vc' ) => 'on',
									__( 'Glow', 'ultimate_vc' ) => 'glow',
								),
								'description' => __( 'Select animation effect for hotspot icon/image.', 'ultimate_vc' ),
								'group'       => 'Animation',
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Glow Color', 'ultimate_vc' ),
								'param_name' => 'glow_color',
								'value'      => '',
								'group'      => 'Animation',
								'dependency' => array(
									'element' => 'tooltip_continuous_animation',
									'value'   => 'glow',
								),
							),
						),
					)
				);
			}
		}
		/**
		 * Function that register styles and scripts for Ultimate Hotspot Module.
		 *
		 * @method ult_hotspot_scripts
		 */
		public function ult_hotspot_scripts() {
			$bsf_dev_mode = bsf_get_option( 'dev_mode' );
			if ( 'enable' === $bsf_dev_mode ) {
				$js_path  = '../assets/js/';
				$css_path = '../assets/css/';
				$ext      = '';
			} else {
				$js_path  = '../assets/min-js/';
				$css_path = '../assets/min-css/';
				$ext      = '.min';
			}
			// css.

			Ultimate_VC_Addons::ultimate_register_style( 'ult_hotspot_css', 'hotspot' );
			Ultimate_VC_Addons::ultimate_register_style( 'ult_hotspot_tooltipster_css', 'hotspot-tooltipster' );

			// js.
			Ultimate_VC_Addons::ultimate_register_script( 'ult_hotspot_js', 'hotspot', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_script( 'ult_hotspot_tooltipster_js', 'hotspot-tooltipster', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}
	}

	new ULT_HotSpot();

	if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ult_Hotspot' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Hotspot extends WPBakeryShortCodesContainer {
		}
	}
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ult_Hotspot_Items' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Hotspot_Items extends WPBakeryShortCode {
		}
	}
}


