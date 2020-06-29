<?php
/*----------------------------------------------------------------------------*\
	TESTIMONIAL SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Testimonial' ) ) {
	class MPC_Testimonial {
		public $shortcode = 'mpc_testimonial';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_testimonial', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'image'         => '',
				'signature'     => '',
				'description'   => '',
			);

			$this->parts = $parts;
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_testimonial-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_testimonial/css/mpc_testimonial.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_testimonial-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_testimonial/js/mpc_testimonial' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'image', 'section_begin', 'signature', 'description', 'section_end' ),
				'style_2' => array( 'image', 'section_begin', 'description', 'signature', 'section_end' ),
				'style_3' => array( 'image', 'section_begin', 'description', 'signature', 'section_end' ),
				'style_4' => array( 'section_begin', 'description', 'signature', 'section_end', 'image' ),
				'style_5' => array( 'section_begin', 'signature', 'image', 'description', 'section_end' ),
				'style_6' => array( 'image', 'section_begin', 'signature', 'description', 'section_end' ),
				'style_7' => array( 'image', 'section_begin', 'description', 'signature', 'section_end' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $parts[ $part ];
			}

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                        => '',
				'preset'                       => '',
                'layout'                       => 'style_1',

                'testimonial_font_preset'      => '',
                'testimonial_font_color'       => '',
                'testimonial_font_size'        => '',
                'testimonial_font_line_height' => '',
                'testimonial_font_align'       => '',
                'testimonial_font_transform'   => '',

                'author_font_preset'           => '',
                'author_font_color'            => '',
                'author_font_size'             => '',
                'author_font_line_height'      => '',
                'author_font_align'            => '',
                'author_font_transform'        => '',
                'author'                       => '',

                'url'                          => '',
                'url_font_preset'              => '',
                'url_font_color'               => '',
                'url_font_size'                => '',
                'url_font_transform'           => '',
                'url_font_line_height'         => '',
                'hover_url_color'              => '',

                'background_type'              => 'color',
                'background_color'             => '',
                'background_repeat'            => 'no-repeat',
                'background_size'              => 'initial',
                'background_position'          => 'middle-center',
                'background_gradient'          => '#83bae3||#80e0d4||0;100||180||linear',
                'background_image'             => '',
                'background_image_size'        => 'large',

                'thumbnail'                    => '',
                'thumbnail_size'               => 'thumbnail',
                'thumbnail_border_css'         => '',
                'thumbnail_padding_css'        => '',

                'padding_css'                  => '',
                'margin_css'                   => '',
                'border_css'                   => '',

                'animation_in_type'            => 'none',
                'animation_in_duration'        => '300',
				'animation_in_delay'           => '0',
                'animation_in_offset'          => '100',

                'animation_loop_type'          => 'none',
                'animation_loop_duration'      => '1000',
                'animation_loop_delay'         => '1000',
                'animation_loop_hover'         => '',
			), $atts );

			global $mpc_frontend, $mpc_can_link;
			if ( $mpc_frontend ) {
				$mpc_can_link = true;
			}

			/* Prepare */
			$animation = MPC_Parser::animation( $atts );
			$url_title = '';
			$url       = MPC_Parser::url( $atts[ 'url' ], $url_title );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-testimonial--' . esc_attr( $atts[ 'layout' ] ) : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Font presets */
			$testimonial_classes = $atts[ 'testimonial_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'testimonial_font_preset' ] ) : '';
			$author_classes      = $atts[ 'author_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'author_font_preset' ] ) : '';
			$url_classes         = $atts[ 'url_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'url_font_preset' ] ) : '';
			$url_classes         .= ' mpc-transition';
			$thumbnail_classes   = ' mpc-testimonial__thumbnail';

			/* Layout parts */
			$image = '';
			if ( isset( $atts[ 'thumbnail' ] ) ) {
				$image_size = wpb_getImageBySize( array( 'attach_id' => $atts[ 'thumbnail'], 'thumb_size' => $atts[ 'thumbnail_size' ] ) );
				$image = $image_size[ 'thumbnail' ];
			}

			$signature = '<div class="mpc-testimonial__signature' . $author_classes . '">';
				$signature .= '<span>' . $atts[ 'author' ] . '</span>';
				$signature .= $url ? ' <a class="mpc-testimonial__link' . $url_classes . '" ' . $url . '>' . $url_title . '</a>' : '';
			$signature .= '</div>';

			$this->parts[ 'section_begin' ] = '<div class="mpc-testimonial__content' . $testimonial_classes . '">';
			$this->parts[ 'section_end' ]   = '</div>';
			$this->parts[ 'description' ]   = '<div class="mpc-testimonial__description">' . wpb_js_remove_wpautop( $description, true ) . '</div>';
			$this->parts[ 'signature' ]     = $signature;
			$this->parts[ 'image' ]         = '<div class="' . $thumbnail_classes . '">' . $image . '</div>';

			/* Shortcode Output */
			$return = '<div data-id="' . $css_id . '" class="mpc-testimonial' . $classes . '" ' . $animation . '>';
				$return .= $this->shortcode_layout( $atts[ 'layout' ], $this->parts );
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
			$css_id = uniqid( 'mpc_testimonial-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'url_font_size' ] = $styles[ 'url_font_size' ] != '' ? $styles[ 'url_font_size' ] . ( is_numeric( $styles[ 'url_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'author_font_size' ] = $styles[ 'author_font_size' ] != '' ? $styles[ 'author_font_size' ] . ( is_numeric( $styles[ 'author_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'testimonial_font_size' ] = $styles[ 'testimonial_font_size' ] != '' ? $styles[ 'testimonial_font_size' ] . ( is_numeric( $styles[ 'testimonial_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Thumbnail
			if ( $styles[ 'thumbnail_padding_css' ] ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] .mpc-testimonial__thumbnail {';
					$style .= $styles[ 'thumbnail_padding_css' ];
				$style .= '}';
			}

			if ( $styles[ 'thumbnail_border_css' ] ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] .mpc-testimonial__thumbnail img {';
					$style .= $styles[ 'thumbnail_border_css' ];
				$style .= '}';
			}

			// Description
			if ( $temp_style = MPC_CSS::font( $styles, 'testimonial' ) ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] .mpc-testimonial__description {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Signature
			if ( $temp_style = MPC_CSS::font( $styles, 'author' ) ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] .mpc-testimonial__signature {';
					$style .= $temp_style;
				$style .= '}';
			}

			// URL
			if ( $temp_style = MPC_CSS::font( $styles, 'url' ) ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] .mpc-testimonial__link {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'hover_url_color' ] ) {
				$style .= '.mpc-testimonial[data-id="' . $css_id . '"] .mpc-testimonial__link:hover {';
					$style .= 'color: ' . $styles[ 'hover_url_color' ] . ';';
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
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => 'style_1',
					'columns'          => '5',
					'layouts'          => array(
						'style_1' => '1',
						'style_2' => '1',
						'style_3' => '3',
						'style_4' => '3',
						'style_5' => '3',
						'style_6' => '1',
						'style_7' => '1',
					),
					'std'              => 'style_1',
					'shortcode'        => $this->shortcode,
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			/* SECTION THUMBNAIL */
			$thumbnail = array(
				array(
					'type'             => 'attach_image',
					'heading'          => __( 'Image', 'mpc' ),
					'param_name'       => 'thumbnail',
					'holder'           => 'img',
					'tooltip'          => __( 'Choose author image.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Thumbnail', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'thumbnail_size',
					'tooltip'          => __( 'Define images size. You can use default WordPress sizes (<em>thumbnail</em>, <em>medium</em>, <em>large</em>, <em>full</em>) or pass exact size by width and height in this format: 100x200.', 'mpc' ),
					'value'            => 'thumbnail',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => false,
					'group'            => __( 'Thumbnail', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field mpc-input--large mpc-first-row',
				),
			);

			/* SECTION AUTHOR */
			$author = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Author', 'mpc' ),
					'param_name'       => 'author',
					'admin_label'      => true,
					'tooltip'          => __( 'Define author.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			/* SECTION URL */
			$url = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'hover_url_color',
					'tooltip'          => __( 'If you want to change the link color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'URL', 'mpc' ),
					'param_name'  => 'url',
					'tooltip'     => __( 'Choose target link for author.', 'mpc' ),
					'value'       => '',
					'group'       => __( 'Content', 'mpc' ),
				),
			);

			/* SECTION TESTIMONIAL */
			$testimonial = array(
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Testimonial - Content', 'mpc' ),
					'param_name'  => 'content',
					'holder'      => 'div',
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'value'       => '',
					'group'       => __( 'Content', 'mpc' ),
				),
			);

			$thumbnail_atts = array( 'prefix' => 'thumbnail', 'group' => __( 'Thumbnail', 'mpc' ) );

			$thumbnail_border  = MPC_Snippets::vc_border( $thumbnail_atts );
			$thumbnail_padding = MPC_Snippets::vc_padding( $thumbnail_atts );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$url_font         = MPC_Snippets::vc_font( array( 'prefix' => 'url', 'subtitle' => __( 'Link', 'mpc' ), 'with_align' => false, 'group' => __( 'Content', 'mpc' ) ) );
			$author_font      = MPC_Snippets::vc_font( array( 'prefix' => 'author', 'subtitle' => __( 'Author', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );
			$testimonial_font = MPC_Snippets::vc_font( array( 'prefix' => 'testimonial', 'subtitle' => __( 'Testimonial', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$background,
				$author_font,
				$author,
				$url_font,
				$url,
				$testimonial_font,
				$testimonial,
				$thumbnail,
				$thumbnail_border,
				$thumbnail_padding,
				$border,
				$padding,
				$margin,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Testimonial', 'mpc' ),
				'description' => __( 'Stylish recommendation box', 'mpc' ),
				'base'        => 'mpc_testimonial',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-testimonials.png',
				'icon'        => 'mpc-shicon-testimonial',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Testimonial' ) ) {
	global $MPC_Testimonial;
	$MPC_Testimonial = new MPC_Testimonial;
}
