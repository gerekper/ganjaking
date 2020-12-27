<?php
/*----------------------------------------------------------------------------*\
	IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Image' ) ) {
	class MPC_Image {
		public $shortcode = 'mpc_image';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_image', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_image-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_image/css/mpc_image.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_image-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_image/js/mpc_image' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Ribbon, $mpc_can_link, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                         => '',
				'preset'                        => '',
				'padding_css'                   => '',
				'margin_css'                    => '',

				'effect'                        => 'none',
				'effect_reverse'                => '',
				'image'                         => '',
				'image_link'                    => '',
				'force_fullwidth'               => '',

				'image_size'                    => 'medium',
				'image_opacity'                 => '100',
				'image_hover_opacity'           => '100',

				'image_border_css'              => '',
				'image_hover_border_css'        => '',
				'image_inner_border_css'        => '',
				'image_inner_border_gap'        => '0',
				'image_hover_inner_border_css'  => '',

				'overlay_enable_lightbox'       => '',
				'overlay_enable_url'            => '',
				'overlay_padding_css'           => '',
				'overlay_overlay_effect'        => 'fade',
				'overlay_background'            => '',
				'overlay_icon_align'            => 'middle-center',

				'overlay_icon_background'       => '',
				'overlay_icon_margin_css'       => '',
				'overlay_icon_padding_css'      => '',
				'overlay_icon_border_css'       => '',

				'overlay_icon_type'             => 'icon',
				'overlay_icon'                  => '',
				'overlay_icon_preset'           => '',
				'overlay_icon_character'        => '',
				'overlay_icon_color'            => '#333333',
				'overlay_icon_size'             => '',
				'overlay_icon_image'            => '',
				'overlay_icon_image_size'       => 'thumbnail',
				'overlay_icon_mirror'           => '',

				'overlay_url_icon_type'         => 'icon',
				'overlay_url_icon'              => '',
				'overlay_url_icon_preset'       => '',
				'overlay_url_icon_character'    => '',
				'overlay_url_icon_color'        => '#333333',
				'overlay_url_icon_size'         => '',
				'overlay_url_icon_image'        => '',
				'overlay_url_icon_image_size'   => 'thumbnail',
				'overlay_url_icon_mirror'       => '',

				'overlay_hover_color'           => '',
				'overlay_hover_border'          => '',
				'overlay_hover_icon_background' => '',

				'animation_in_type'             => 'none',
				'animation_in_duration'         => '300',
				'animation_in_delay'            => '0',
				'animation_in_offset'           => '100',

				/* Ribbon */
				'mpc_ribbon__disable'               => '',
				'mpc_ribbon__class'                 => '',
				'mpc_ribbon__preset'                => '',
				'mpc_ribbon__text'                  => '',
				'mpc_ribbon__style'                 => 'classic',
				'mpc_ribbon__disable_corners'       => '',
				'mpc_ribbon__alignment'             => 'top-left',
				'mpc_ribbon__corners_color'         => '',
				'mpc_ribbon__size'                  => 'medium',

				'mpc_ribbon__font_preset'           => '',
				'mpc_ribbon__font_color'            => '',
				'mpc_ribbon__font_size'             => '',
				'mpc_ribbon__font_line_height'      => '',
				'mpc_ribbon__font_align'            => '',
				'mpc_ribbon__font_transform'        => '',

				'mpc_ribbon__icon_type'             => 'icon',
				'mpc_ribbon__icon'                  => '',
				'mpc_ribbon__icon_character'        => '',
				'mpc_ribbon__icon_image'            => '',
				'mpc_ribbon__icon_image_size'       => 'thumbnail',
				'mpc_ribbon__icon_preset'           => '',
				'mpc_ribbon__icon_size'             => '',
				'mpc_ribbon__icon_color'            => '#333333',

				'mpc_ribbon__margin_css'            => '',
				'mpc_ribbon__padding_css'           => '',
				'mpc_ribbon__border_css'            => '',

				'mpc_ribbon__background_type'       => 'color',
				'mpc_ribbon__background_color'      => '',
				'mpc_ribbon__background_image'      => '',
				'mpc_ribbon__background_image_size' => 'large',
				'mpc_ribbon__background_repeat'     => 'no-repeat',
				'mpc_ribbon__background_size'       => 'initial',
				'mpc_ribbon__background_position'   => 'middle-center',
				'mpc_ribbon__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			/* Validate data */
			$image_size = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'medium';

			$image = wpb_getImageBySize( array(
				'attach_id'  => $atts[ 'image' ],
				'thumb_size' => $image_size,
				'class'      => 'mpc-transition',
			) );
			$image_full = wp_get_attachment_image_src( $atts[ 'image' ], 'full' );
			$image_full = isset( $image_full[ 0 ] ) ? $image_full[ 0 ] : '';

			if ( ! $image ) {
				return '';
			}

			/* Prepare */
			$styles      = $this->shortcode_styles( $atts );
			$css_id      = $styles[ 'id' ];
			$animation   = MPC_Parser::animation( $atts );
			$atts_ribbon = MPC_Parser::shortcode( $atts, 'mpc_ribbon_' );
			$ribbon      = $atts[ 'mpc_ribbon__disable' ] == '' ? $MPC_Ribbon->shortcode_template( $atts_ribbon ) : '';

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'effect' ] != 'none' ? ' mpc-effect--' . esc_attr( $atts[ 'effect' ] ) : '';
			$classes .= $atts[ 'effect_reverse' ] != '' ? ' mpc-effect--reverse' : '';
			$classes .= $atts[ 'force_fullwidth' ] != '' ? ' mpc-fullwidth' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$data_atts = $animation;
			$classes_item = 'mpc-item mpc-transition';

			$overlay   = MPC_Helper::render_overlay( $atts, $image_full, $atts[ 'image_link' ] );
			$classes   .= $overlay[ 'class' ];
			$data_atts .= $overlay[ 'atts' ];

			/* Image Stuff */
			$url_settings = $mpc_can_link && $overlay[ 'content' ] == '' ? MPC_Parser::url( $atts[ 'image_link' ] ) : '';
			$wrapper = $url_settings != '' ? 'a' : 'div';

			/* Shortcode Output */
			$return = $ribbon != '' ? '<div class="mpc-ribbon-wrap">' : '';
				$return .= '<div data-id="' . $css_id . '" onclick="" class="mpc-image' . $classes . '" ' . $data_atts . '>';
					$return .= '<' . $wrapper . $url_settings . ' class="' . $classes_item . '">';
						$return .= $image[ 'thumbnail' ] . $overlay[ 'content' ];
					$return .= '</' . $wrapper . '>';
				$return .= '</div>';
				$return .= $ribbon;
			$return .= $ribbon != '' ? '</div>' : '';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_image-' . rand( 1, 100 ) );
			$style  = '';

			// Add 'px'
			$styles[ 'overlay_icon_size' ] = $styles[ 'overlay_icon_size' ] != '' ? $styles[ 'overlay_icon_size' ] . ( is_numeric( $styles[ 'overlay_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_url_icon_size' ] = $styles[ 'overlay_url_icon_size' ] != '' ? $styles[ 'overlay_url_icon_size' ] . ( is_numeric( $styles[ 'overlay_url_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'image_inner_border_gap' ] = $styles[ 'image_inner_border_gap' ] != '' ? $styles[ 'image_inner_border_gap' ] . ( is_numeric( $styles[ 'image_inner_border_gap' ] ) ? 'px' : '' ) : '';

			$inner_styles = array();
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'image_border_css' ] ) { $inner_styles[] = $styles[ 'image_border_css' ]; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'image_opacity' ] ) { $inner_styles[] = 'opacity: ' . ( $styles[ 'image_opacity' ] / 100 ) . ';filter: alpha( opacity = ' . $styles[ 'image_opacity' ] . ' );'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item img {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'image_inner_border_css' ] ) { $inner_styles[] = $styles[ 'image_inner_border_css' ]; }
			if ( $styles[ 'image_inner_border_css' ] && $styles[ 'image_inner_border_gap' ] != '' && $styles[ 'image_inner_border_gap' ] != '0px' ) {
				$inner_styles[] = 'left:' . $styles[ 'image_inner_border_gap' ] . ';right:' . $styles[ 'image_inner_border_gap' ] . ';';
				$inner_styles[] = 'bottom:' . $styles[ 'image_inner_border_gap' ] . ';top:' . $styles[ 'image_inner_border_gap' ] . ';';
			}
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item:before {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'image_hover_border_css' ] ) { $inner_styles[] = $styles[ 'image_hover_border_css' ]; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'image_hover_opacity' ] ) { $inner_styles[] = 'opacity: ' . ( $styles[ 'image_hover_opacity' ] / 100 ) . ';filter: alpha( opacity = ' . $styles[ 'image_hover_opacity' ] . ' );'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item:hover img {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'image_hover_inner_border_css' ] ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item:hover:before {';
					$style .= $styles[ 'image_hover_inner_border_css' ];
				$style .= '}';
			}

			// Overlay & Lightbox
			if ( $styles[ 'overlay_background' ] ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-item-overlay {';
					$style .= 'background: ' . $styles[ 'overlay_background' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'overlay_padding_css' ] ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-overlay--vertical {';
					$style .= $styles[ 'overlay_padding_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_icon_border_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_border_css' ]; }
			if ( $styles[ 'overlay_icon_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_padding_css' ]; }
			if ( $styles[ 'overlay_icon_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_margin_css' ]; }
			if ( $styles[ 'overlay_icon_background' ] ) { $inner_styles[] = 'background: ' . $styles[ 'overlay_icon_background' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-icon-anchor {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay_url' ) ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-type--external {';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay' ) ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-type--lightbox {';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_hover_border' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'overlay_hover_border' ] . ';'; }
			if ( $styles[ 'overlay_hover_icon_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'overlay_hover_icon_background' ]. ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-icon-anchor:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'overlay_hover_color' ] ) {
				$style .= '.mpc-image[data-id="' . $css_id . '"] .mpc-icon-anchor:hover i {';
					$style .= 'color:' . $styles[ 'overlay_hover_color' ] . ';';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Main Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
			);

			$image = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Image', 'mpc' ),
					'param_name'       => 'image_section_divider',
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'image_size',
					'tooltip'          => __( 'Define images size. You can use default WordPress sizes (<em>thumbnail</em>, <em>medium</em>, <em>large</em>, <em>full</em>) or pass exact size by width and height in this format: 100x200.', 'mpc' ),
					'value'            => 'medium',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => false,
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-input--large',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Force Fullwidth', 'mpc' ),
					'param_name'       => 'force_fullwidth',
					'tooltip'          => __( 'Check to stretch image to fullfill the container area.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-input--large',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'image_opacity',
					'tooltip'          => __( 'Choose opacity for image.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 100,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Image', 'mpc' ),
				),
			);

			$image_hover = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Image Hover', 'mpc' ),
					'param_name'       => 'image_hover_section_divider',
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Effect', 'mpc' ),
					'param_name'       => 'effect',
					'tooltip'          => __( 'Select hover effect for image.<br><br><b>Please notice that some of them will work only in modern browsers</b>.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )       => 'none',
						__( 'Brightness', 'mpc' ) => 'brightness',
						__( 'Contrast', 'mpc' )   => 'contrast',
						__( 'Grey Scale', 'mpc' ) => 'grey-scale',
						__( 'Hue', 'mpc' )        => 'hue',
						__( 'Invert', 'mpc' )     => 'invert',
						__( 'Saturate', 'mpc' )   => 'saturate',
						__( 'Sepia', 'mpc' )      => 'sepia',
					),
					'std'              => 'fade',
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Reverse', 'mpc' ),
					'param_name'       => 'effect_reverse',
					'tooltip'          => __( 'Check to reverse the effect.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'image_hover_opacity',
					'tooltip'          => __( 'If you want to change the image opacity after hover choose a different value from the slider below.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 100,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Image', 'mpc' ),
				),
			);

			$image_gallery = array(
				array(
					'type'             => 'attach_image',
					'heading'          => __( 'Image', 'mpc' ),
					'param_name'       => 'image',
					'holder'           => 'img',
					'tooltip'          => __( 'Choose image.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'External Link', 'mpc' ),
					'param_name'       => 'image_link',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose target link for image or External URL at Overlay.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
			);

			$padding = MPC_Snippets::vc_padding();
			$margin  = MPC_Snippets::vc_margin();
			$overlay  = MPC_Snippets::vc_overlay();

			$image_atts = array( 'prefix' => 'image', 'subtitle' => __( 'Image', 'mpc' ), 'group' => __( 'Image', 'mpc' ));
			$image_hover_atts = array( 'prefix' => 'image_hover', 'subtitle' => __( 'Image Hover', 'mpc' ), 'group' => __( 'Image', 'mpc' ));

			$image_border          = MPC_Snippets::vc_border( $image_atts );
			$image_inner_border    = MPC_Snippets::vc_inner_border( $image_atts );
			$image_inner_border_gap = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Image - Inner Border Gap', 'mpc' ),
					'param_name'       => 'image_hover_section_divider',
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'image_inner_border_gap',
					'tooltip'          => __( 'Specify gap between inner border and image edges.', 'mpc' ),
					'value'            => 0,
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'group'            => __( 'Image', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
			);

			$image_hover_border       = MPC_Snippets::vc_border( $image_hover_atts );
			$image_hover_inner_border = MPC_Snippets::vc_inner_border( $image_hover_atts );

			$integrate_ribbon = vc_map_integrate_shortcode( 'mpc_ribbon', 'mpc_ribbon__', __( 'Ribbon', 'mpc' ) );
			$disable_ribbon   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Ribbon', 'mpc' ),
					'param_name'       => 'mpc_ribbon__disable',
					'tooltip'          => __( 'Check to disable ribbon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Ribbon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_ribbon = array_merge( $disable_ribbon, $integrate_ribbon );

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$image_gallery,
				$padding,
				$margin,
				$image,
				$image_border,
				$image_inner_border,
				$image_inner_border_gap,

				$image_hover,
				$image_hover_border,
				$image_hover_inner_border,

				$overlay,
                $integrate_ribbon,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Image', 'mpc' ),
				'description' => __( 'Advanced image with overlay', 'mpc' ),
				'base'        => 'mpc_image',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-single-image.png',
				'icon'        => 'mpc-shicon-image',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Image' ) ) {
	global $MPC_Image;
	$MPC_Image = new MPC_Image;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_image' ) ) {
	class WPBakeryShortCode_mpc_image extends MPCShortCode_Base {}
}
