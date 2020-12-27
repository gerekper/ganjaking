<?php
/*----------------------------------------------------------------------------*\
	PRODUCTS CATEGORY SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_WC_Category' ) ) {
	class MPC_WC_Category {
		private $is_wrapped = false;
		public $shortcode = 'mpc_wc_category';
		public $cat;
		public $style;
		public $parts = array();
		public $defaults = array();
		public $classes = '';
		public $attributes = '';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Autocomplete */
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_item_id_callback', 'MPC_Autocompleter::suggest_wc_category', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_item_id_render',  'MPC_Autocompleter::render_wc_category', 10, 1 );

			/* Layout parts */
			$this->default_parts();

			$this->defaults = array(
				/* Category */
				'preset'                => '',
				'item_id'               => '',
				'height'                => '300',
				'tiles_size'            => 'fixed',
				'gap'                   => '',
				'thumb_disable'         => '',
				'animation_type'        => 'replace',
				'inline_content'        => '',
				'count_phrase'          => __( 'Product,Products', 'mpc' ),
				'count_phrase_position' => 'suffix',
				'border_css'            => '',
				'margin_css'            => '',

				/* Fx Effects */
				'fx_effect'    => '',
				'fx_scale'     => 115,
				'fx_rotate'    => 15,
				'fx_direction' => 'left',
				'fx_margin'    => 15,
				'fx_color'     => '#FFFFFF',

				/* Regular */
				'regular_disable' => '',
				'layout'          => 'title',
				'position'        => 'bottom',
				'alignment'       => 'left',
				'effect'          => 'fade',

				'title_overflow'         => '',
				'title_font_preset'      => '',
				'title_font_color'       => '',
				'title_font_size'        => '',
				'title_font_line_height' => '',
				'title_font_align'       => '',
				'title_font_transform'   => '',
				'title_margin_css'       => '',

				'count_font_preset'      => '',
				'count_font_color'       => '',
				'count_font_size'        => '',
				'count_font_line_height' => '',
				'count_font_align'       => '',
				'count_font_transform'   => '',
				'count_margin_css'       => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'background_fullfill'   => '',

				'regular_padding_css' => '',

				/* Hover */
				'hover_disable'   => '',
				'hover_layout'    => 'title',
				'hover_position'  => 'bottom',
				'hover_alignment' => 'left',
				'hover_effect'    => 'fade',

				'hover_title_overflow'         => '',
				'hover_title_font_preset'      => '',
				'hover_title_font_color'       => '',
				'hover_title_font_size'        => '',
				'hover_title_font_line_height' => '',
				'hover_title_font_align'       => '',
				'hover_title_font_transform'   => '',
				'hover_title_margin_css'       => '',

				'hover_count_font_preset'      => '',
				'hover_count_font_color'       => '',
				'hover_count_font_size'        => '',
				'hover_count_font_line_height' => '',
				'hover_count_font_align'       => '',
				'hover_count_font_transform'   => '',
				'hover_count_margin_css'       => '',

				'overlay_background_type'       => 'color',
				'overlay_background_color'      => '',
				'overlay_background_image'      => '',
				'overlay_background_image_size' => 'large',
				'overlay_background_repeat'     => 'no-repeat',
				'overlay_background_size'       => 'initial',
				'overlay_background_position'   => 'middle-center',
				'overlay_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'hover_background_fullfill'   => '',

				'overlay_padding_css' => '',

				/* Animations */
				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Reset parts */
		function reset() {
			$this->default_parts();
			$this->cat = null;
		}
		function default_parts() {
			$this->parts = array(
				'regular_begin' => '<div class="mpc-category__content mpc-transition">',
				'regular_end'   => '</div>',
				'wrapper_begin' => '<div class="mpc-wrapper">',
				'wrapper_end'   => '</div>',
				'hover_begin'   => '<div class="mpc-category__overlay mpc-transition">',
				'hover_end'     => '</div>',
				'regular_title' => '',
				'regular_count' => '',
				'hover_title'   => '',
				'hover_count'   => '',
				'thumbnail'     => '',
			);

			$this->classes = '';
			$this->attributes = '';
		}

		/* Get Products */
		public function get_items( $item_id ) {
			if( !isset( $this->cat ) || !is_object( $this->cat ) ) {
				$this->is_wrapped = false;

				$this->cat = get_term( (int) $item_id, 'product_cat' );
			} else {
				$this->is_wrapped = true;
			}
		}

		/* Set Category */
		public function set_cat( $cat ) {
			if( !is_wp_error( $cat ) ) {
				$this->cat = $cat;
			}
		}

		/* Get Thumbnail */
		function get_placeholder( $item, $atts ) {
			$per_thumb_css = apply_filters( 'ma/products_category/thumb_inline_css', '', $atts, $item->term_id );
			$placeholder = '<div class="mpc-category__thumbnail mpc-image-placeholder" style="' . $per_thumb_css . '"></div>';

			return $placeholder;
		}
		function get_thumbnail( $item, $atts ) {
			$thumbnail_id = absint( get_term_meta( $item->term_id, 'thumbnail_id', true ) );
			$thumbnail    = wp_get_attachment_image_src( $thumbnail_id, 'full' );

			if( $thumbnail ) {
				$per_thumb_css = apply_filters( 'ma/products_category/thumb_inline_css', '', $atts, $item->term_id );
				$image_atts = ' data-mpc_src="' . $thumbnail[ 0 ] . '"';
				$image_atts .= ( $per_thumb_css !== '') ? ' style="'. $per_thumb_css .'"' : '';
			} else {
				$image_atts = false;
			}

			return apply_filters( 'ma/products_category/get/thumbnail', $image_atts, 'get_thumbnail' );
		}

		/* Build Thumbnail */
		function build_thumbnail( $item, $disable, $atts ) {
			if ( $disable == '' ) {
				$thumbnail = $this->get_thumbnail( $item, $atts );

				$return = $thumbnail != false ? '<div class="mpc-category__thumbnail mpc-effect--target"' . $thumbnail . '></div>' : $this->get_placeholder( $item, $atts );

				return apply_filters( 'ma/products_category/build/thumbnail', $return, 'build_thumbnail' );
			}

			return '';
		}

		/* Get Title */
		function get_title( $item ) {
			return apply_filters( 'ma/products_category/get/title', $item->name, 'get_title' );
		}

		/* Get Count */
		function get_count( $item, $atts ) {
			$item_count = $item->count;
			$phrase     = $atts[ 'count_phrase' ] != '' ? $atts[ 'count_phrase' ] : '';

			$phrases = explode( ',', $phrase );

			if ( isset( $phrases[ 1 ] ) && $item_count !== 1 ) {
				$phrase = $phrases[ 1 ];
			} elseif( isset( $phrases[ 0 ] ) ) {
				$phrase = $phrases[ 0 ];
			}

			$count = $phrase != '' && $atts[ 'count_phrase_position' ] == 'prefix' ? $phrase . ' ' : '';
			$count .= $item_count;
			$count .= $phrase != '' && $atts[ 'count_phrase_position' ] == 'suffix' ?  ' ' . $phrase : '';

			return apply_filters( 'ma/products_category/get/count', $count, 'get_count' );
		}

		/* Build layout */
		function layout( $layout, $prefix ) {
			$content = '';

			if( $layout == '' )
				return '';

			$parts = explode( ',', $layout );

			if( ! isset( $parts ) )
				return '';

			foreach( $parts as $part ) {
				$content .= $this->parts[ $prefix . '_' . $part ];
			}

			$content = $this->parts[ 'wrapper_begin' ] . $content . $this->parts[ 'wrapper_end' ];
			$content = $this->parts[ $prefix . '_begin' ] . $content . $this->parts[ $prefix . '_end' ];

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null, $shortcode = null, $parent_css = null ) {
			if( !class_exists( 'WooCommerce' ) ) {
				return '';
			}

			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( $this->defaults, $atts );

			/* Build Query */
			if ( $atts[ 'item_id' ] == '' && !is_object( $this->cat ) ) {
				return '';
			}

			$this->style = '';
			$this->get_items( $atts[ 'item_id' ] );

			if( is_wp_error( $this->cat ) ) {
				return '';
			}

			/* Prepare */
			$styles = $this->shortcode_styles( $atts, $parent_css );
			$css_id = $styles[ 'id' ];
			$css_id = !empty( $parent_css ) ? '' : ' data-id="' . $css_id . '"';

			$animation = MPC_Parser::animation( $atts );

			/* Shortcode classes | Animation | Layout */
			$this->classes = ' mpc-init mpc-transition'; // mpc-transition
			$this->classes .= $animation != '' ? ' mpc-animation' : '';
			$this->classes .= $atts[ 'thumb_disable' ] != '' ? ' mpc--no-thumb' : '';
			$this->classes .= $atts[ 'animation_type' ] == 'move' ? ' mpc--no-replace' : ' mpc--force-replace';
			$this->classes .= $atts[ 'inline_content' ] != '' ? ' mpc--floating-box' : '';
			$classes_effect = $atts[ 'fx_effect' ] != '' ? ' mpc-effect--' . $atts[ 'fx_effect' ] : '';

			/* Effects & Positions */
			if( $atts[ 'animation_type' ] == 'replace' ) {
				$effects[] = $atts[ 'effect' ] != '' && $atts[ 'regular_disable' ] == '' ? $atts[ 'effect' ] : 'none';
				$effects[] = $atts[ 'hover_effect' ] != '' && $atts[ 'hover_disable' ] == '' ? $atts[ 'hover_effect' ] : 'none';
				$this->attributes .=  ' data-effects="' . join( '|', $effects ) . '"';
			}

			$positions[] = $atts[ 'position' ] . ':' . $atts[ 'alignment' ];
			$positions[] = $atts[ 'hover_disable' ] == '' ? $atts[ 'hover_position' ] . ':' . $atts[ 'hover_alignment' ] : '';
			$this->attributes .= ' data-positions="' . join( '|', $positions ) . '"';

			$regular_title = $regular_count = $hover_title = $hover_count = '';
			$regular_count .= $atts[ 'count_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'count_font_preset' ] : '';
			$hover_count   .= $atts[ 'hover_count_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'hover_count_font_preset' ] : '';

			$regular_title .= $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'title_font_preset' ] : '';
			$regular_title .= $atts[ 'title_overflow' ] != '' ? '' : ' mpc-text-overflow';
			$hover_title   .= $atts[ 'hover_title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'hover_title_font_preset' ] : '';
			$hover_title   .= $atts[ 'hover_title_overflow' ] != '' ? '' : ' mpc-text-overflow';

			/* Shortcode Output */
			$link  = get_term_link( $this->cat->term_id, 'product_cat' );
			$title = $this->get_title( $this->cat );
			$count = $this->get_count( $this->cat, $atts );
			$this->parts[ 'regular_title' ]   = '<h3 class="mpc-category__heading' . $regular_title . '">' . $title . '</h3>';
			$this->parts[ 'regular_count' ]   = '<div class="mpc-category__count' . $regular_count . '">' . $count . '</div>';
			$this->parts[ 'hover_title' ]     = '<h3 class="mpc-category__heading' . $hover_title . '">' . $title . '</h3>';
			$this->parts[ 'hover_count' ]     = '<div class="mpc-category__count' . $hover_count . '">' . $count . '</div>';

			$this->parts[ 'thumbnail' ] = $this->build_thumbnail( $this->cat, $atts[ 'thumb_disable' ], $atts );

			if( !$this->is_wrapped ) {
				$return = '<div' . $css_id . ' class="mpc-wc-category' . $this->classes . $classes_effect . '"' . $this->attributes . $animation . '>';
				$return .= '<a href="' . $link . '" class="mpc-wc-category__wrap">';
			} else {
				$per_cat_atts = apply_filters( 'ma/products_category/category_atts', '', $atts, 'product_cat_' . $this->cat->term_id );

				$return = '<div class="mpc-wc-category"' . $per_cat_atts . '>';
				$return .= '<a href="' . $link . '" class="mpc-wc-category__wrap' . $classes_effect . '">';
			}

			$per_thumb_css = apply_filters( 'ma/products_category/thumb_inline_css', '', $atts, 'product_cat_' . $this->cat->term_id );
			$return .= $this->parts[ 'thumbnail' ];
			$return .= '<div class="mpc-category__wrapper"' . ( $per_thumb_css != '' ? ' style="' . $per_thumb_css . '"' : '' ) . '>';
				$return .= $atts[ 'regular_disable' ] == '' ? $this->layout( $atts[ 'layout' ], 'regular' ) : '';
				$return .= $atts[ 'hover_disable' ] == '' && $atts[ 'animation_type' ] == 'replace' ? $this->layout( $atts[ 'hover_layout' ], 'hover' ) : '';
			$return .= '</div>';

			$return .= '</a>';
			$return .= '</div>';

			/* Restore original Post Data */
			if( !$this->is_wrapped ) {
				$this->reset();
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				if( !$this->is_wrapped ) {
					$return .= '<style>' . $styles[ 'css' ] . '</style>';
				} else {
					$this->style = $styles[ 'css' ];
				}
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $parent_css ) {
			global $mpc_massive_styles;
			$css_id       = uniqid( $this->shortcode . '-' . rand( 1, 100 ) );
			$css_selector = '.mpc-wc-category[data-id="' . $css_id . '"]';
			$style = '';

			if( $parent_css != '' && is_array( $parent_css ) ) {
				$css_id       = $parent_css[ 'id' ];
				$css_selector = $parent_css[ 'selector' ];
			}

			if( $styles[ 'animation_type' ] == 'move' ) {
				$styles[ 'hover_title_font_transform' ] = $styles[ 'hover_title_font_align' ] = $styles[ 'hover_title_font_preset'  ] = '';
				$styles[ 'hover_count_font_transform' ] = $styles[ 'hover_count_font_align' ] = $styles[ 'hover_count_font_preset'  ] = '';
			}

			// Add 'px'
			$styles[ 'height' ] = $styles[ 'height' ] != '' ? $styles[ 'height' ] . ( is_numeric( $styles[ 'height' ] ) ? 'px' : '' ) : '';
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'count_font_size' ] = $styles[ 'count_font_size' ] != '' ? $styles[ 'count_font_size' ] . ( is_numeric( $styles[ 'count_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_title_font_size' ] = $styles[ 'hover_title_font_size' ] != '' ? $styles[ 'hover_title_font_size' ] . ( is_numeric( $styles[ 'hover_title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_count_font_size' ] = $styles[ 'hover_count_font_size' ] != '' ? $styles[ 'hover_count_font_size' ] . ( is_numeric( $styles[ 'hover_count_font_size' ] ) ? 'px' : '' ) : '';

			// Thumb Height
			$inner_styles = array();
			if ( $styles[ 'margin_css' ] != '' && $styles[ 'inline_content' ] != '' ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $styles[ 'height' ] && $styles[ 'thumb_disable' ] == '' ) { $inner_styles[] = 'height: ' . $styles[ 'height' ] . ';'; }
			if( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-category__wrapper {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'border_css' ] != '' ) {
				$style .= $css_selector . ' .mpc-wc-category__wrap {';
					$style .= $styles[ 'border_css' ];
				$style .= '}';
			}

			$style .= MPC_CSS::effect( $css_selector, $styles );

			// Regular
			if( $styles[ 'regular_disable' ] == '') {
				if ( $temp_style = MPC_CSS::background( $styles ) ) {
					$element = $styles[ 'background_fullfill' ] != '' ? '' : ' .mpc-wrapper';
					$style .= $css_selector . ' .mpc-category__content' . $element . ' {';
						$style .= $temp_style;
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'regular_padding_css' ] ) { $inner_styles[] = $styles[ 'regular_padding_css' ]; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $css_selector . ' .mpc-category__content .mpc-wrapper {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				// Typography
				$inner_styles = array();
				if ( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
				if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $css_selector . ' .mpc-category__heading {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'count_margin_css' ] ) { $inner_styles[] = $styles[ 'count_margin_css' ]; }
				if ( $temp_style = MPC_CSS::font( $styles, 'count' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $css_selector . ' .mpc-category__count {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}
			}

			// Hover
			if( $styles[ 'hover_disable' ] == '' ) {
				$css_selector .= $styles[ 'animation_type' ] == 'replace' ? ' .mpc-category__overlay' : ':hover .mpc-category__content';

				if ( $temp_style = MPC_CSS::background( $styles, 'overlay' ) ) {
					$element = $styles[ 'hover_background_fullfill' ] != '' ? '' : ' .mpc-wrapper';
					$style .= $css_selector . $element . ' {';
						$style .= $temp_style;
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'overlay_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_padding_css' ]; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $css_selector . ' .mpc-wrapper {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				// Typography
				$inner_styles = array();
				if ( $styles[ 'hover_title_margin_css' ] ) { $inner_styles[] = $styles[ 'hover_title_margin_css' ]; }
				if ( $temp_style = MPC_CSS::font( $styles, 'hover_title' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $css_selector . ' .mpc-category__heading {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				$inner_styles = array();
				if ( $styles[ 'hover_count_margin_css' ] ) { $inner_styles[] = $styles[ 'hover_count_margin_css' ]; }
				if ( $temp_style = MPC_CSS::font( $styles, 'hover_count' ) ) { $inner_styles[] = $temp_style; }
				if ( count( $inner_styles ) > 0 ) {
					$style .= $css_selector . ' .mpc-category__count {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}
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
				return false;
			}

			$animation_replace = array( 'element' => 'animation_type', 'value' => 'replace' );
			$inline_content    = array( 'element' => 'inline_content', 'not_empty' => true );
			$fullwidth_content = array( 'element' => 'inline_content', 'not_empty' => false );

			/* Count Section */
			$count = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Products Count', 'mpc' ),
					'subtitle'   => __( 'Specify items count settings.', 'mpc' ),
					'param_name' => 'count_section_divider',
					'group'      => __( 'Category', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Count Phrase Position', 'mpc' ),
					'param_name'       => 'count_phrase_position',
					'description'      => __( 'Specify the position of count phrase', 'mpc' ),
					'value'            => array(
						__( 'Prefix', 'mpc' ) => 'prefix',
						__( 'Suffix', 'mpc' ) => 'suffix',
					),
					'std'              => 'suffix',
					'group'      => __( 'Category', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Prefix/Suffix Phrase', 'mpc' ),
					'param_name'       => 'count_phrase',
					'description'      => __( 'You can add both singular & plural forms separated by "," ( comma ).', 'mpc' ),
					'value'            => __( 'Product,Products', 'mpc' ),
					'group'      => __( 'Category', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Item Preset', 'mpc' ),
					'param_name'  => 'preset',
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Specify item preset.', 'mpc' ),
					'group'       => __( 'Category', 'mpc' ),
				),
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Source', 'mpc' ),
					'subtitle'   => __( 'Specify the source.', 'mpc' ),
					'param_name' => 'source_section_divider',
					'group'      => __( 'Category', 'mpc' ),
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => __( 'Category', 'mpc' ),
					'param_name'  => 'item_id',
					'settings'    => array(
						'multiple'      => false,
						'sortable'      => false,
						'unique_values' => true,
					),
					'description' => __( 'Select category to display.', 'mpc' ),
					'group'      => __( 'Category', 'mpc' ),
				),
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'General', 'mpc' ),
					'subtitle'   => __( 'Specify settings for thumbnail and item height.', 'mpc' ),
					'param_name' => 'thumbnail_section_divider',
					'group'      => __( 'Category', 'mpc' ),
				),
				array(
					'type'        => 'mpc_slider',
					'heading'     => __( 'Height', 'mpc' ),
					'param_name'  => 'height',
					'description' => __( 'Specify height.', 'mpc' ),
					'min'         => 0,
					'max'         => 1000,
					'step'        => 1,
					'value'       => 300,
					'unit'        => 'px',
					'group'       => __( 'Category', 'mpc' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Animation Type', 'mpc' ),
					'param_name'  => 'animation_type',
					'value'       => array(
						__( 'Replace regular content', 'mpc' ) => 'replace',
						__( 'Move regular content', 'mpc' )    => 'move',
					),
					'std'         => 'replace',
					'description' => __( 'Select the animation type.', 'mpc' ),
					'group'            => __( 'Category', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Inline Content', 'mpc' ),
					'param_name'       => 'inline_content',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to disable fullwidth content (will create a floating box).', 'mpc' ),
					'group'            => __( 'Category', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Regular State', 'mpc' ),
					'param_name'       => 'regular_disable',
					'tooltip'          => __( 'Check to disable regular state (only thumbnail image will be displayed until hover).', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Regular Layout', 'mpc' ),
					'subtitle'   => __( 'Specify settings for regular state layout.', 'mpc' ),
					'param_name' => 'regular_section_divider',
					'group'      => __( 'Regular', 'mpc' ),
				),
				array(
					'type'        => 'mpc_list',
					'heading'     => __( 'Count layout', 'mpc' ),
					'param_name'  => 'layout',
					'description' => __( 'Enable blocks and place them in desired order.<br/><br/>', 'mpc' ),
					'value'       => 'title',
					'options'     => array(
						'title'   => __( 'Title', 'mpc' ),
						'count'   => __( 'Products Count', 'mpc' ),
					),
					'group'      => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Position', 'mpc' ),
					'param_name'  => 'position',
					'value'       => array(
						__( 'Top', 'mpc' )     => 'top',
						__( 'Middle', 'mpc' )  => 'middle',
						__( 'Bottom', 'mpc' )  => 'bottom',
					),
					'std'         => 'bottom',
					'description' => __( 'Select the content position.', 'mpc' ),
					'group'       => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'alignment',
					'value'            => array(
						__( 'Left', 'mpc' )   => 'left',
						__( 'Center', 'mpc' ) => 'center',
						__( 'Right', 'mpc' )  => 'right',
					),
					'std'              => 'left',
					'description'      => __( 'Select the content position.', 'mpc' ),
					'group'            => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $inline_content,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Content Out Effect', 'mpc' ),
					'param_name'       => 'effect',
					'description'      => __( 'Select effect for interactive content element', 'mpc' ),
					'value'            => array(
						__( 'Stay', 'mpc' )            => 'stay',
						__( 'Fade', 'mpc' )            => 'fade',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade',
					'group'            => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => $animation_replace,
				),
			);

			/* Title Section */
			$title_overflow = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Title Overflow', 'mpc' ),
					'param_name'       => 'title_overflow',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to option to show the full title', 'mpc' ),
					'group'            => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-8 vc_column',
					'dependency'       => $fullwidth_content,
				),
			);

			$regular_background_fullfill = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Fullfill Area', 'mpc' ),
					'param_name'       => 'background_fullfill',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to fill whole area.', 'mpc' ),
					'group'            => __( 'Regular', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $animation_replace,
				),
			);

			/* Item */
			$item_border  = MPC_Snippets::vc_border( array( 'group' => __( 'Category', 'mpc' ) ) );
			$item_margin  = MPC_Snippets::vc_margin( array( 'group' => __( 'Category', 'mpc' ), 'subtitle' => __( 'Content', 'mpc' ), 'dependency' => $inline_content ) );

			$regular_background = MPC_Snippets::vc_background( array( 'subtitle' => __( 'Regular', 'mpc' ), 'group' => __( 'Regular', 'mpc' ) ) );
			$regular_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'regular', 'subtitle' => __( 'Regular', 'mpc' ),'group' => __( 'Regular', 'mpc' ) ) );

			/* Elements */
			$title_font   = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Regular', 'mpc' ) ) );
			$title_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Regular', 'mpc' ) ) );

			/* Count */
			$count_font   = MPC_Snippets::vc_font( array( 'prefix' => 'count', 'subtitle' => __( 'Products Count', 'mpc' ), 'group' => __( 'Regular', 'mpc' ) ) );
			$count_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'count', 'subtitle' => __( 'Products Count', 'mpc' ), 'group' => __( 'Regular', 'mpc' ) ) );

			/* Hover State */
			$hover = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Hover State', 'mpc' ),
					'param_name'       => 'hover_disable',
					'tooltip'          => __( 'Check to disable hover state (nothing will change after hover).', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover State', 'mpc' ),
					'subtitle'   => __( 'Specify overlay settings for items.', 'mpc' ),
					'param_name' => 'hover_section_divider',
					'group'      => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'        => 'mpc_list',
					'heading'     => __( 'Layout', 'mpc' ),
					'param_name'  => 'hover_layout',
					'description' => __( 'Enable blocks and place them in desired order.<br/><br/>', 'mpc' ),
					'value'       => 'title',
					'options'     => array(
						'title' => __( 'Title', 'mpc' ),
						'count' => __( 'Products Count', 'mpc' ),
					),
					'group'      => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => $animation_replace,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Position', 'mpc' ),
					'param_name'  => 'hover_position',
					'value'       => array(
						__( 'Top', 'mpc' )     => 'top',
						__( 'Middle', 'mpc' )  => 'middle',
						__( 'Bottom', 'mpc' )  => 'bottom',
					),
					'std'         => 'bottom',
					'description' => __( 'Select the content position.', 'mpc' ),
					'group'       => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'hover_alignment',
					'value'            => array(
						__( 'Left', 'mpc' )   => 'left',
						__( 'Center', 'mpc' ) => 'center',
						__( 'Right', 'mpc' )  => 'right',
					),
					'std'              => 'left',
					'description'      => __( 'Select the content position.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $inline_content,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Overlay In Effect', 'mpc' ),
					'param_name'       => 'hover_effect',
					'value'            => array(
						__( 'Fade', 'mpc' )            => 'fade',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade',
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => $animation_replace,
				),
			);

			/* Hover Title Section */
			$hover_title_overflow = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Title Overflow', 'mpc' ),
					'param_name'       => 'hover_title_overflow',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to option to show the full title', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
			);

			/* Hover Title */
			$hover_title_font   = MPC_Snippets::vc_font( array( 'prefix' => 'hover_title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );
			$hover_title_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'hover_title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );

			/* Hover Count */
			$hover_count_font   = MPC_Snippets::vc_font( array( 'prefix' => 'hover_count', 'subtitle' => __( 'Products Count', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );
			$hover_count_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'hover_count', 'subtitle' => __( 'Products Count', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );

			$hover_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'overlay', 'subtitle' => __( 'Hover State', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );
			$hover_background = MPC_Snippets::vc_background( array( 'prefix' => 'overlay', 'subtitle' => __( 'Hover State', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );

			$hover_background_fullfill = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Fullfill Area', 'mpc' ),
					'param_name'       => 'hover_background_fullfill',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to fill whole area.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => $animation_replace,
				),
			);

			/* General */
			$animation = MPC_Snippets::vc_animation_basic();
			$effects   = MPC_Snippets::vc_effects_filters( array( 'subtitle' => __( 'Thumbnail' ), 'group' => __( 'Category', 'mpc' ) ) );

			$params = array_merge(
				$base,
				$item_margin,
				$count,
				$effects,
				$item_border,

				$title_font,
				$title_overflow,
				$title_margin,

				$count_font,
				$count_margin,

				$regular_background,
				$regular_background_fullfill,
				$regular_padding,

				$hover,
				$hover_title_font,
				$hover_title_overflow,
				$hover_title_margin,

				$hover_count_font,
				$hover_count_margin,

				$hover_background,
				$hover_background_fullfill,
				$hover_padding,

				$animation
			);

			if( !class_exists( 'WooCommerce' ) ) {
				$params = array(
					array(
						'type'             => 'custom_markup',
						'param_name'       => 'woocommerce_notice',
						'value'            => '<p class="mpc-warning mpc-active"><i class="dashicons dashicons-warning"></i>' . __( 'Please install and activate <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> plugin in order to use this shortcode! :)', 'mpc' ) . '</p>',
						'edit_field_class' => 'vc_col-sm-12 vc_column mpc-woocommerce-field',
					),
				);
			}

			return array(
				'name'        => __( 'Products Category', 'mpc' ),
				'description' => __( 'Display single products category', 'mpc' ),
				'base'        => $this->shortcode,
				'icon'        => 'mpc-shicon-wc-category',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_WC_Category' ) ) {
	global $MPC_WC_Category;
	$MPC_WC_Category = new MPC_WC_Category;
}
