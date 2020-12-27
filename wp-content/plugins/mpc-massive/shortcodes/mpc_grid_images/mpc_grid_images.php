<?php
/*----------------------------------------------------------------------------*\
	GRID IMAGES SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Grid_Images' ) ) {
	class MPC_Grid_Images {
		public $shortcode = 'mpc_grid_images';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_grid_images', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_grid_images-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_grid_images/css/mpc_grid_images.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_grid_images-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_grid_images/js/mpc_grid_images' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-isotope-js' );

			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'       => '',
				'preset'      => '',
				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'effect'          => 'none',
				'effect_reverse'  => '',
				'cols'            => '4',
				'gap'             => '0',
				'images'          => '',
				'images_links'    => '',
				'enable_lightbox' => '',

				'image_size'          => 'medium',
				'image_opacity'       => '100',
				'image_hover_opacity' => '100',

				'image_border_css'             => '',
				'image_hover_border_css'       => '',
				'image_inner_border_css'       => '',
				'image_hover_inner_border_css' => '',

				'overlay_enable_lightbox' => '',
				'overlay_enable_url'      => '',
				'overlay_padding_css'     => '',
				'overlay_overlay_effect'  => 'fade',
				'overlay_background'      => '',
				'overlay_icon_align'      => 'middle-center',

				'overlay_icon_background'  => '',
				'overlay_icon_margin_css'  => '',
				'overlay_icon_padding_css' => '',
				'overlay_icon_border_css'  => '',

				'overlay_icon_type'       => 'icon',
				'overlay_icon'            => '',
				'overlay_icon_preset'     => '',
				'overlay_icon_character'  => '',
				'overlay_icon_color'      => '#333333',
				'overlay_icon_size'       => '',
				'overlay_icon_image'      => '',
				'overlay_icon_image_size' => 'thumbnail',
				'overlay_icon_mirror'     => '',

				'overlay_url_icon_type'       => 'icon',
				'overlay_url_icon'            => '',
				'overlay_url_icon_preset'     => '',
				'overlay_url_icon_character'  => '',
				'overlay_url_icon_color'      => '#333333',
				'overlay_url_icon_size'       => '',
				'overlay_url_icon_image'      => '',
				'overlay_url_icon_image_size' => 'thumbnail',
				'overlay_url_icon_mirror'     => '',

				'overlay_hover_color'           => '',
				'overlay_hover_border'          => '',
				'overlay_hover_icon_background' => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',
			), $atts );

			/* Validate data */
			if( empty( $atts[ 'images' ] ) )
				return '';

			/* Prepare */
			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $atts );

			$images       = explode( ',', $atts[ 'images' ] );
			$images_links = $atts[ 'images_links' ] != '' ? explode( ',', $atts[ 'images_links' ] ) : '';
			$image_size   = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'medium';
			$index        = 0;

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'effect' ] != '' ? ' mpc-effect--' . esc_attr( $atts[ 'effect' ] ) : '';
			$classes .= $atts[ 'effect_reverse' ] != '' ? ' mpc-effect--reverse' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes_item = 'mpc-grid__image mpc-transition';

			$data_atts = $animation;
			$data_atts .= $atts[ 'cols' ] != '' ? ' data-grid-cols="' . (int) esc_attr( $atts[ 'cols' ] ) . '"' : '';

			/* Lightbox & Overlay */
			$overlay_begin = $overlay_end = '';
			if( $atts[ 'overlay_enable_lightbox' ] != '' || $atts[ 'overlay_enable_url' ] != '' ? true : false ) {
				$overlay_begin = '<div class="mpc-item-overlay mpc-transition"><div class="mpc-overlay--vertical-wrap"><div class="mpc-overlay--vertical">';
				$overlay_end   = '</div></div></div>';

				$data_atts .= $atts[ 'overlay_icon_align' ] != '' ? ' data-align="' . esc_attr( $atts[ 'overlay_icon_align' ] ) . '"' : '';
				$classes .= $atts[ 'overlay_overlay_effect' ] != '' ? ' mpc-overlay--' . esc_attr( $atts[ 'overlay_overlay_effect' ] ) : ' mpc-overlay--fade';
			}

			$overlay_icon = $overlay_atts = $lightbox = '';
			if( $atts[ 'overlay_enable_lightbox' ] != '' ) {
				$lightbox  = MPC_Helper::lightbox_vendor();

				$overlay_atts = ' rel="mpc[' . $css_id . ']"';
				$classes_icon = 'mpc-item-overlay__icon mpc-type--lightbox';
				$classes_icon .= $atts[ 'overlay_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

				$overlay_icon = MPC_Parser::icon( $atts, 'overlay' );
				$overlay_icon[ 'class' ] .= $atts[ 'overlay_icon_type' ] != '' ? ' mpc-icon--' . esc_attr( $atts[ 'overlay_icon_type' ] ) : '';

				$overlay_icon = '<i class="' . $classes_icon . $overlay_icon[ 'class' ] . '">' . $overlay_icon[ 'content' ] . '</i>';
			}

			$url_icon = '';
			if( $atts[ 'overlay_enable_url' ] != '' ) {
				$classes_icon = 'mpc-item-overlay__icon mpc-type--external';
				$classes_icon .= $atts[ 'overlay_url_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

				$url_icon = MPC_Parser::icon( $atts, 'overlay_url' );
				$url_icon[ 'class' ] .= $atts[ 'overlay_url_icon_type' ] != '' ? ' mpc-icon--' . esc_attr( $atts[ 'overlay_url_icon_type' ] ) : '';

				$url_icon = '<i class="' . $classes_icon  . $url_icon[ 'class' ] . '">' . $url_icon[ 'content' ] . '</i>';
			}

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-grid-images' . $classes . '" ' . $data_atts . '>';

			foreach( $images as $image_id ) {
				$image = wpb_getImageBySize( array(
                      'attach_id'  => $image_id,
                      'thumb_size' => $image_size,
                      'class'      => 'mpc-transition',
				) );

				if( !$image ) {
					$index++; // Attachment has been deleted but we need to assign the correct links for other items
					continue;
				}

				$overlay = '';
				if( $atts[ 'overlay_enable_url' ] != '' ) {
					$url_link = isset( $images_links[ $index ] ) && $images_links[ $index ] != '' ? esc_url( $images_links[ $index ] ) : '';
					$overlay .= '<a href="' . $url_link . '" class="mpc-icon-anchor">' . $url_icon . '</a>';
				}

				if( $atts[ 'overlay_enable_lightbox' ] != '' ) {
					$full_image = wp_get_attachment_image_src( $image_id, 'full' );
					$image_link = is_array( $full_image ) ? $full_image[ 0 ] : '';
					$overlay    .= '<a href="' . $image_link . '"' . $overlay_atts . ' class="mpc-icon-anchor' . $lightbox . '">' . $overlay_icon . '</a>';
				}

				$overlay = $overlay != '' ? $overlay_begin . $overlay . $overlay_end : '';
				$overlay = apply_filters( 'ma_grid_images_overlay', $overlay, $image_id, $image_size );

				$wrapper_link = $overlay == '' && isset( $images_links[ $index ] ) && $images_links[ $index ] != '' ? true : false;

				$wrapper     = $wrapper_link != '' ? 'a href="' . $images_links[ $index ] . '"' : 'div';
				$wrapper_end = $wrapper_link != '' ? 'a' : 'div';

				$thumbnail = apply_filters( 'ma_grid_images_thumbnail', $image[ 'thumbnail' ], $image_id, $image_size );

				$item = '<div class="mpc-item">';
					$item .= '<' . $wrapper . ' onclick="" class="' . $classes_item . '">' . $thumbnail . $overlay . '</' . $wrapper_end . '>';
				$item .= '</div>';

				$return .= $item;
				$index++;
			}

			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_grid_images-' . rand( 1, 100 ) );
			$style  = '';

			// Add 'px'
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_icon_size' ] = $styles[ 'overlay_icon_size' ] != '' ? $styles[ 'overlay_icon_size' ] . ( is_numeric( $styles[ 'overlay_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_url_icon_size' ] = $styles[ 'overlay_url_icon_size' ] != '' ? $styles[ 'overlay_url_icon_size' ] . ( is_numeric( $styles[ 'overlay_url_icon_size' ] ) ? 'px' : '' ) : '';

			// Gap
			if( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px') {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] {';
					$style .= 'margin-left: -' . $styles[ 'gap' ] . ';';
					$style .= 'margin-bottom: -' . $styles[ 'gap' ] . ';';
				$style .= '}';
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-grid__image  {';
					$style .= 'margin-left: ' . $styles[ 'gap' ] . ';';
					$style .= 'margin-bottom: ' . $styles[ 'gap' ] . ';';
				$style .= '}';
			}

			// Regular
			if ( $styles[ 'image_border_css' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-grid__image {';
					$style .= $styles[ 'image_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'image_inner_border_css' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-grid__image::before {';
					$style .= $styles[ 'image_inner_border_css' ];
				$style .= '}';
			}

			// Hover
			if ( $styles[ 'image_opacity' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-item {';
					$style .= 'opacity: ' . ( $styles[ 'image_opacity' ] / 100 ) . '; filter: alpha( opacity = ' . $styles[ 'image_opacity' ] . ' );';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'image_hover_border_css' ] ) { $inner_styles[] = $styles[ 'image_hover_border_css' ]; }
			if ( $styles[ 'image_hover_opacity' ] ) { $inner_styles[] = 'opacity: ' . ( $styles[ 'image_hover_opacity' ] / 100 ) . '; filter: alpha( opacity = ' . $styles[ 'image_hover_opacity' ] . ' );'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-grid__image:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'image_hover_inner_border_css' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-grid__image:hover::before {';
					$style .= $styles[ 'image_hover_inner_border_css' ];
				$style .= '}';
			}

			// Overlay & Lightbox
			if ( $styles[ 'overlay_background' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-item-overlay {';
					$style .= 'background: ' . $styles[ 'overlay_background' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'overlay_padding_css' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-overlay--vertical {';
					$style .= $styles[ 'overlay_padding_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_icon_border_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_border_css' ]; }
			if ( $styles[ 'overlay_icon_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_padding_css' ]; }
			if ( $styles[ 'overlay_icon_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_margin_css' ]; }
			if ( $styles[ 'overlay_icon_background' ] ) { $inner_styles[] = 'background: ' . $styles[ 'overlay_icon_background' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-icon-anchor {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay_url' ) ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-type--external {';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay' ) ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-type--lightbox {';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_hover_border' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'overlay_hover_border' ] . ';'; }
			if ( $styles[ 'overlay_hover_icon_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'overlay_hover_icon_background' ]. ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-icon-anchor:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'overlay_hover_color' ] ) {
				$style .= '.mpc-grid-images[id="' . $css_id . '"] .mpc-icon-anchor:hover i {';
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
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'        => 'attach_images',
					'heading'     => __( 'Images', 'mpc' ),
					'param_name'  => 'images',
					'tooltip'     => __( 'Choose images for this grid.', 'mpc' ),
					'value'       => '',
					'admin_label' => true,
				),
				array(
					'type'        => 'exploded_textarea',
					'heading'     => __( 'Images Links', 'mpc' ),
					'param_name'  => 'images_links',
					'tooltip'     => __( 'Define custom links for grid images. Each new line will be a separate link. Leave empty line to skip an image.', 'mpc' ),
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose gap between grid items.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$image = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Images', 'mpc' ),
					'param_name'       => 'image_section_divider',
					'group'            => __( 'Images', 'mpc' ),
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
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-input--large',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'image_opacity',
					'tooltip'          => __( 'Choose opacity for images.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 100,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Images', 'mpc' ),
				),
			);

			$image_hover = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Image Hover', 'mpc' ),
					'param_name'       => 'image_hover_section_divider',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Effect', 'mpc' ),
					'param_name'       => 'effect',
					'tooltip'          => __( 'Select hover effect for carousel images.<br><br><b>Please notice that some of them will work only in modern browsers</b>.', 'mpc' ),
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
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Reverse', 'mpc' ),
					'param_name'       => 'effect_reverse',
					'tooltip'          => __( 'Check to reverse the effect.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
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
					'group'            => __( 'Images', 'mpc' ),
				),
			);

			$overlay  = MPC_Snippets::vc_overlay();
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 8, 'default' => 4 ), 'rows' => false ) );

			$image_atts = array( 'prefix' => 'image', 'subtitle' => __( 'Image', 'mpc' ), 'group' => __( 'Images', 'mpc' ));
			$image_hover_atts = array( 'prefix' => 'image_hover', 'subtitle' => __( 'Image Hover', 'mpc' ), 'group' => __( 'Images', 'mpc' ));

			$image_border          = MPC_Snippets::vc_border( $image_atts );
			$image_inner_border    = MPC_Snippets::vc_inner_border( $image_atts );

			$image_hover_border       = MPC_Snippets::vc_border( $image_hover_atts );
			$image_hover_inner_border = MPC_Snippets::vc_inner_border( $image_hover_atts );

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$rows_cols,
				$base_ext,
				$image,
				$image_border,
				$image_inner_border,
				$image_hover,
				$image_hover_border,
				$image_hover_inner_border,
				$overlay,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Grid Images', 'mpc' ),
				'description' => __( 'Grid with images', 'mpc' ),
				'base'        => 'mpc_grid_images',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-grid-images.png',
				'icon'        => 'mpc-shicon-grid-images',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Grid_Images' ) ) {
	global $MPC_Grid_Images;
	$MPC_Grid_Images = new MPC_Grid_Images;
}

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_grid_images' ) ) {
	class WPBakeryShortCode_mpc_grid_images extends WPBakeryShortCode {
		function __construct( $settings ) {
			parent::__construct( $settings );

			$this->shortcodeScripts();
		}

		public function shortcodeScripts() {
			wp_enqueue_script( 'vc_grid-js-imagesloaded', vc_asset_url( 'lib/bower/imagesloaded/imagesloaded.pkgd.min.js' ) );
		}

		public function singleParamHtmlHolder( $param, $value ) {
			$output = '';

			$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
			$type = isset( $param['type'] ) ? $param['type'] : '';
			$class = isset( $param['class'] ) ? $param['class'] : '';

			if ( isset( $param['holder'] ) == true && $param['holder'] !== 'hidden' ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
			if ( $param_name == 'images' ) {
				$images_ids = empty( $value ) ? array() : explode( ',', trim( $value ) );
				$output .= '<ul class="attachment-thumbnails' . ( empty( $images_ids ) ? ' image-exists' : '' ) . '" data-name="' . $param_name . '">';
				foreach ( $images_ids as $image ) {
					$img = wpb_getImageBySize( array( 'attach_id' => (int) $image, 'thumb_size' => 'thumbnail' ) );
					$output .= ( $img ? '<li>' . $img['thumbnail'] . '</li>' : '<li><img width="150" height="150" test="' . $image . '" src="' . vc_asset_url( 'vc/blank.gif' ) . '" class="attachment-thumbnail" alt="" title="" /></li>' );
				}
				$output .= '</ul>';
				$output .= '<a href="#" class="column_edit_trigger' . ( ! empty( $images_ids ) ? ' image-exists' : '' ) . '">' . __( 'Add images', 'js_composer' ) . '</a>';

			}

			return $output;
		}
	}
}
