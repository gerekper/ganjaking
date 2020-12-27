<?php
/*----------------------------------------------------------------------------*\
	WC PRODUCT SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_WC_Product' ) ) {
	class MPC_WC_Product {
		public $shortcode = 'mpc_wc_product';
		private $is_wrapped = false;
		private $post = null;
		public $style = '';
		public $parts = array();
		public $classes = '';
		public $attributes = '';
		public $defaults = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Autocomplete */
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_id_callback', 'MPC_Autocompleter::suggest_wc_product', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_id_render', 'MPC_Autocompleter::render_wc_product', 10, 1 );

			$this->reset();

			$this->defaults = array(
				'preset'     => '',
				'gap'        => '',
				'tiles_size' => 'fixed',
				'height'     => '300',
				'image_size' => 'large',

				'id' => '',

				/* Fx Effects */
				'fx_effect'    => '',
				'fx_scale'     => 115,
				'fx_rotate'    => 15,
				'fx_direction' => 'left',
				'fx_margin'    => 15,
				'fx_color'     => '#FFFFFF',

				'main_elements'                       => 'title,price',
				'hover_elements'                      => 'atc_button',
				'thumb_elements'                      => '',
				'thumb_hover_elements'                => '',
				'border_css'                          => '',

				'main_background_type'                => 'color',
				'main_background_color'               => '',
				'main_background_image'               => '',
				'main_background_image_size'          => 'large',
				'main_background_repeat'              => 'no-repeat',
				'main_background_size'                => 'initial',
				'main_background_position'            => 'middle-center',
				'main_background_gradient'            => '#83bae3||#80e0d4||0;100||180||linear',
				'main_border_css'                     => '',
				'main_padding_css'                    => '',

				'hover_background_type'               => 'color',
				'hover_background_color'              => '',
				'hover_background_image'              => '',
				'hover_background_image_size'         => 'large',
				'hover_background_repeat'             => 'no-repeat',
				'hover_background_size'               => 'initial',
				'hover_background_position'           => 'middle-center',
				'hover_background_gradient'           => '#83bae3||#80e0d4||0;100||180||linear',
				'hover_border_css'                    => '',
				'hover_padding_css'                   => '',
				'main_effect'                         => 'fade-in',

				'thumb_background_type'       => 'color',
				'thumb_background_color'      => '',
				'thumb_background_image'      => '',
				'thumb_background_image_size' => 'large',
				'thumb_background_repeat'     => 'no-repeat',
				'thumb_background_size'       => 'initial',
				'thumb_background_position'   => 'middle-center',
				'thumb_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'thumb_border_css'            => '',
				'thumb_padding_css'           => '',
				'thumb_fullfill'              => '',

				'thumb_hover_background_type'       => 'color',
				'thumb_hover_background_color'      => '',
				'thumb_hover_background_image'      => '',
				'thumb_hover_background_image_size' => 'large',
				'thumb_hover_background_repeat'     => 'no-repeat',
				'thumb_hover_background_size'       => 'initial',
				'thumb_hover_background_position'   => 'middle-center',
				'thumb_hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'thumb_hover_border_css'            => '',
				'thumb_hover_padding_css'           => '',
				'thumb_hover_fullfill'              => '',
				'thumb_box_prefix'                  => '',

				'content_position'                    => 'bottom-left',
				'content_effect'                      => 'slide-bottom',

				'thumb_animation_type'  => 'replace',
				'thumb_effect'          => 'fade',
				'thumb_inline'          => '',
				'thumb_position'        => 'bottom',
				'thumb_alignment'       => 'left',
				'thumb_hover_position'  => 'bottom',
				'thumb_hover_effect'    => 'fade',
				'thumb_hover_alignment' => 'left',

				/* Title */
				'title_overflow'                      => '',
				'title_font_preset'                   => '',
				'title_font_color'                    => '',
				'title_font_size'                     => '',
				'title_font_line_height'              => '',
				'title_font_align'                    => '',
				'title_font_transform'                => '',
				'title_margin_css'                    => '',
				'title_hover_color'                   => '',

				/* Price */
				'price_font_preset'                   => '',
				'price_font_color'                    => '',
				'price_font_size'                     => '',
				'price_font_line_height'              => '',
				'price_font_align'                    => '',
				'price_font_transform'                => '',
				'price_margin_css'                    => '',
				'price_sale_color'                    => '',

				/* Categories */
				'tax_font_preset'                   => '',
				'tax_font_color'                    => '',
				'tax_font_size'                     => '',
				'tax_font_line_height'              => '',
				'tax_font_align'                    => '',
				'tax_font_transform'                => '',
				'tax_margin_css'                    => '',
				'tax_link_color'                    => '',
				'tax_hover_color'                   => '',
				'tax_separator'                     => ', ',

				/* Rating */
				'rating_align'            => 'inherit',
				'rating_gap'              => 1,
				'rating_icon_type'        => 'icon',
				'rating_icon'             => '',
				'rating_icon_color'       => '',
				'rating_icon_size'        => '',
				'rating_score_icon_type'  => 'icon',
				'rating_score_icon'       => '',
				'rating_score_icon_color' => '',
				'rating_margin_css'       => '',

				'animation_in_type'                   => 'none',
				'animation_in_duration'               => '300',
				'animation_in_delay'                  => '0',
				'animation_in_offset'                 => '100',

				/* Buttons */
				'buttons_list'                        => 'lightbox',
				'buttons_align'                       => 'top-left',
				'buttons_direction'                   => 'vertical',
				'buttons_gap'                         => 1,
				'buttons_on_hover'                    => 'true',
				'buttons_effect'                      => 'fade-in',

				'buttons_size'                        => '',

				'buttons_bg'                          => '',
				'buttons_color'                       => '#333333',
				'buttons_hover_bg'                    => '',
				'buttons_hover_color'                 => '',
				'buttons_hover_border'                => '',

				'buttons_wcwl_icon_type'              => 'icon',
				'buttons_wcwl_icon'                   => '',
				'buttons_wcwl_icon_preset'            => '',
				'buttons_wcwl_icon_character'         => '',
				'buttons_wcwl_icon_image'             => '',
				'buttons_wcwl_icon_image_size'        => 'thumbnail',
				'buttons_wcwl_mirror'                 => '',

				'buttons_lb_icon_type'                => 'icon',
				'buttons_lb_icon'                     => '',
				'buttons_lb_icon_preset'              => '',
				'buttons_lb_icon_character'           => '',
				'buttons_lb_icon_image'               => '',
				'buttons_lb_icon_image_size'          => 'thumbnail',
				'buttons_lb_mirror'                   => '',

				'buttons_url_icon_type'               => 'icon',
				'buttons_url_icon'                    => '',
				'buttons_url_icon_preset'             => '',
				'buttons_url_icon_character'          => '',
				'buttons_url_icon_image'              => '',
				'buttons_url_icon_image_size'         => 'thumbnail',
				'buttons_url_mirror'                  => '',

				'buttons_border_css'                  => '',
				'buttons_padding_css'                 => '',
				'buttons_margin_css'                  => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Reset */
		function reset() {
			$this->is_wrapped = false;
			$this->style      = '';
			$this->parts      = array();
			$this->classes    = '';
			$this->attributes = '';
			$this->post       = null;

			$this->parts = array(
				'flex_begin'    => '<div class="mpc-flex">',
				'inline_begin'  => '<div class="mpc-inline-box">',
				'block_begin'   => '<div class="mpc-block-box">',
				'buttons_begin' => '<div class="mpc-thumb__buttons mpc-transition">',
				'thumb_begin'   => '<div class="mpc-thumb__content-wrap">',

				'atc_button'      => '',
				'title'           => '',
				'rating'          => '',
				'price'           => '',
				'categories'      => '',
				'thumbnail'       => '',
				'buttons'         => '',
				'button_lightbox' => '',
				'button_url'      => '',
				'button_wishlist' => '',

				'content'             => '',
				'hover_content'       => '',
				'thumb_content'       => '',
				'thumb_hover_content' => '',

				'section_end' => '</div>',
			);
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style(  $this->shortcode .  '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode .  '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Allow to set post from wrappers */
		function set_post( WP_Post $post ) {
			$this->post = $post;
			$this->is_wrapped = true;
		}

		/* Build query */
		function build_query( $atts ) {
			$args = array(
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page'      => 1,
				'post_type'           => 'product',
			);

			$args[ 'post__in' ] = explode( ', ', $atts[ 'id' ] );

			return $args;
		}

		/* Get Posts */
		function get_post( $post_id ) {
			if( !is_object( $this->post ) ) {
				$this->is_wrapped = false;

				$this->post = get_post( $post_id );
			} else {
				$this->is_wrapped = true;
			}
		}

		/* Get Product */
		function get_product() {
			$product = wc_setup_product_data( get_the_ID() );

			return apply_filters( 'ma/product/get/product', $product, 'get_product' );
		}

		/* Get Tax list */
		function get_taxonomies( $separator ) {
			$product_taxonomies = get_the_term_list( get_the_ID(), 'product_cat', '', $separator );

			return apply_filters( 'ma/product/get/taxonomies', $product_taxonomies, 'get_taxonomies' );
		}

		/* Get Rating */
		function get_rating( $product = '' ) {
			$rating = $product != '' ? round( $product->get_average_rating(), 0 ) : '';

			return apply_filters( 'ma/product/rating', $rating, 'rating' );
		}

		/* Get Thumbnail */
		function get_thumbnail( $post_id = null, $image_size ) {
			$post_id = $post_id ?: get_the_ID();

			$thumbnail_id = get_post_thumbnail_id( $post_id );
			$thumbnail = wp_get_attachment_image_src( $thumbnail_id, $image_size );
			$thumbnail = isset( $thumbnail[ 0 ] ) ? $thumbnail[ 0 ] : false;

			return apply_filters( 'ma/product/get/thumbnail', $thumbnail, 'get_thumbnail' );
		}

		/* Get JS link */
		function get_js_link( $permalink ) {
			return 'data-mpc_link="' . $permalink . '"';
		}

		/* Build Thumbnail */
		function build_thumbnail( $tiles_size, $image_size, $atts ) {
			$thumbnail        = $this->get_thumbnail( null, $image_size );
			$thumbnail_layout = $this->thumbnail_layout();

			if ( !$thumbnail ) {
				$product_object = wc_get_product( get_the_ID() );
				$product_type   = $product_object->get_type();

				if ( $product_type === 'variation' ) {
					$post_id   = $product_object->get_parent_id();
					$thumbnail = $this->get_thumbnail( $post_id, $image_size );
				}
			}

			$is_placeholder = !$thumbnail ? ' mpc-image-placeholder' : '';

			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$js_link   = $this->get_js_link( $permalink );

			if( $tiles_size !== 'full'  ) {
				$per_thumb_css = apply_filters( 'ma/product/thumb_inline_css', '', $atts, $this->post->ID );

				$thumbnail = 'data-mpc_src="' . $thumbnail . '"';

				$return = '<div class="mpc-product__thumb-wrap" style="' . $per_thumb_css . '">';
				$return .= '<div ' . $js_link . ' class="mpc-product__thumb mpc-effect--target' . $is_placeholder . '" ' . ( $is_placeholder == '' ? $thumbnail : '' ) . '></div>';
			} else {
				$thumbnail_css = 'data-mpc_src="' . $thumbnail . '"';
				$return = '<div class="mpc-product__thumb-wrap">';
				$return .= '<div ' . $js_link . ' class="mpc-product__thumb mpc-effect--target"' . $thumbnail_css . '></div>';
				$return .= '<img src="' . $thumbnail . '" alt="" />';
			}

			$return .= $thumbnail_layout;
			$return .= '</div>';

			return apply_filters( 'ma/product/build/thumbnail', $return, 'build_thumbnail' );
		}

		/* Get Title */
		function get_title() {
			$title = '<a href="' . get_the_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a>';

			return apply_filters( 'ma/product/get/title', $title, 'get_title' );
		}

		/* Get Price */
		function get_price( $product = '' ) {
			$price = $product != '' ? $product->get_price_html() : '';

			return apply_filters( 'ma/product/get/price', $price, 'get_price' );
		}

		/* Get Icon */
		function get_lightbox_src() {
			$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
			$thumbnail    = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			$thumbnail    = apply_filters( 'ma/product/lightbox_src', $thumbnail[ 0 ], 'lightbox_src' );

			return $thumbnail;
		}

		function get_icon( $atts, $type = 'lightbox' ) {
			$icon = '';

			$title     = apply_filters( 'ma/product/title', get_the_title(), 'title' );
			$permalink = apply_filters( 'ma/product/permalink', get_the_permalink(), 'permalink' );

			if ( $atts[ 'content' ] != '' || $atts[ 'class' ] != '' ) {
				$icon = '<i class="mpc-product__icon mpc-type--' . $type . $atts[ 'class' ] . '">' . $atts[ 'content' ] . '</i>';
			}

			if ( $type == 'lightbox' ) {
				$thumbnail = $this->get_lightbox_src();
				$lightbox = MPC_Helper::lightbox_vendor();

				$icon = '<a href="' . esc_attr( $thumbnail ) . '" title="' . esc_attr( $title ) . '" class="mpc-lb mpc-icon-anchor' . $lightbox . '">' . $icon . '</a>';
			} else if ( $type == 'url' ) {
				$icon = '<a href="' . $permalink . '" title="' . __( 'Purchase: ', 'mpc' ) . $title . '" class="mpc-url mpc-icon-anchor">' . $icon . '</a>';
			} else if ( $type == 'wcwl' ) {
				$icon = '<a href="' . $permalink . '" title="' . __( 'Add to Wishlist: ', 'mpc' ) . $title . '" class="mpc-wcwl mpc-icon-anchor">' . $icon . '</a>';
			}

			return apply_filters( 'ma/product/get/icon', $icon, 'get_icon' );
		}

		/* Build rating */
		function build_rating( $rating, $icon, $score_icon ) {
			$wrap_icon = $wrap_score = $icon_markup = $score_icon_markup = '';

			if ( $icon[ 'content' ] != '' || $icon[ 'class' ] != '' ) {
				$icon_markup = '<i class="mpc-rating__icon' . $icon[ 'class' ] . '"></i>';
			}
			if ( $score_icon[ 'content' ] != '' || $score_icon[ 'class' ] != '' ) {
				$score_icon_markup = '<i class="mpc-rating__score-icon' . $score_icon[ 'class' ] . '"></i>';
			}

			if( $icon_markup == '' || $score_icon_markup == '' ) {
				return '';
			}

			for( $i=0; $i < $rating; $i++ ) {
				$wrap_score .= $score_icon_markup;
			}
			for( $j=$rating; $j < 5; $j++ ) {
				$wrap_icon .= $icon_markup;
			}

			$return = '<div class="mpc-rating">';
				$return .= $wrap_score .$wrap_icon;
			$return .= '</div>';

			return apply_filters( 'ma/product/get/rating', $return, 'get_rating' );
		}

		/* Build Section */
		function build_from_list( $layout, $prefix = '', $wrapper = 'flex' ) {
			if ( $layout == '' ) {
				return '';
			}

			$elements = explode( ',', $layout );

			if ( ! is_array( $elements ) ) {
				return '';
			}

			$return = '';
			$prefix = $prefix != '' ? $prefix . '_' : '';

			foreach ( $elements as $index => $element ) {
				$return .= $this->parts[ $prefix . $element ];
			}

			if ( $return != '' && $wrapper !== false ) {
				$return = $this->parts[ $wrapper . '_begin' ] . $return . $this->parts[ 'section_end' ];
			}

			return $return;
		}

		/* Build thumbnail layout */
		function thumbnail_layout() {
			$content = '';

			if( $this->parts[ 'buttons' ] == ''
			    && $this->parts[ 'thumb_content' ] == ''
				&& $this->parts[ 'thumb_hover_content' ] == '' ) {
				return '';
			}

			$layouts = array(  'buttons', 'thumb_begin', 'thumb_content', 'thumb_hover_content', 'section_end' );

			foreach ( $layouts as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}

		/* Build shortcode layout */
		function shortcode_layout() {
			$return  = '';
			$layouts = array( 'thumbnail' );
			$content = $this->parts[ 'content' ] != '' ? array( 'flex_begin', 'content', 'hover_content', 'section_end' ) : array();

			$layouts = array_merge( $layouts, $content );

			foreach ( $layouts as $part ) {
				$return .= $this->parts[ $part ];
			}

			return $return;
		}

		/* Prepare Pagination Content */
		function pagination_content( $atts ) {
			global $MPC_WC_Add_To_Cart;

			$atts_button = MPC_Parser::shortcode( $atts, 'mpc_wc_add_to_cart_' );
			$atts = shortcode_atts( $this->defaults, $atts );

			/* Get Post */
			if ( ! is_object( $this->post ) && isset( $atts[ 'id' ] ) ) {
				$this->get_post( (int) $atts[ 'id' ] );
			}

			if( $this->post === null ) {
				return '';
			}

			/* Setup post data */
			global $post; $post = $this->post;
			$product = wc_setup_product_data( $post );

			/* Prepare */
			$atts[ 'thumb_box_prefix' ] = $atts[ 'thumb_inline' ] != '' ? 'inline' : 'block';
			$atts[ 'thumb_elements' ] = $atts[ 'thumb_elements' ] != '' ? $atts[ 'thumb_box_prefix' ] . '_begin,' . $atts[ 'thumb_elements' ] . ',section_end' : '';
			$atts[ 'thumb_hover_elements' ] = $atts[ 'thumb_hover_elements' ] != '' ? $atts[ 'thumb_box_prefix' ] . '_begin,' . $atts[ 'thumb_hover_elements' ] . ',section_end' : '';

			if( $atts[ 'buttons_list' ] != '' ) {
				$atts_lightbox = MPC_Parser::icon( $atts, 'buttons_lb' );
				$atts_url      = MPC_Parser::icon( $atts, 'buttons_url' );
				$atts_lightbox[ 'class' ] .= $atts_lightbox[ 'class' ] != '' && $atts[ 'buttons_lb_mirror' ] != '' ? ' mpc-icon--mirror' : '';
				$atts_url[ 'class' ]      .= $atts_url[ 'class' ] != '' && $atts[ 'buttons_url_mirror' ] != '' ? ' mpc-icon--mirror' : '';
			}
			$atts_rating       = MPC_Parser::icon( $atts, 'rating' );
			$atts_rating_score = MPC_Parser::icon( $atts, 'rating_score' );

			/* Shortcode classes | Animation | Layout */
			$classes_effect = $atts[ 'fx_effect' ] != '' ? ' mpc-effect--' . $atts[ 'fx_effect' ] : '';
			$classes_title  = ' mpc-transition';
			$classes_title .= $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'title_font_preset' ] : '';
			$classes_title .= $atts[ 'title_overflow' ] != '' ? '' : ' mpc-text-overflow';

			$classes_price = $atts[ 'price_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'price_font_preset' ] : '';
			$classes_tax = $atts[ 'tax_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'tax_font_preset' ] : '';

			/* Layout parts */
			$content = '';

			/* Shortcode Output */
			$atts[ 'rating_score' ] = $this->get_rating( $product );
			$atts_button[ 'item_id' ] = get_the_ID();

			/* Buttons */
			if( $atts[ 'buttons_list' ] != '' ) {
				$this->parts[ 'button_lightbox' ] = $this->get_icon( $atts_lightbox, 'lightbox' );
				$this->parts[ 'button_url' ]      = $this->get_icon( $atts_url, 'url' );
				$this->parts[ 'buttons' ]         = $this->build_from_list( $atts[ 'buttons_list' ], 'button', 'buttons' );
			}

			/* JS link */
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$js_link   = $this->get_js_link( $permalink );

			/* Image Quality */
			$image_size = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'large';

			$this->parts[ 'title' ]  = '<span class="mpc-product__heading' . $classes_title . '">' . $this->get_title() . '</span>';
			$this->parts[ 'price' ]  = '<span class="mpc-product__price' . $classes_price . '">' . $this->get_price( $product ) . '</span>';
			$this->parts[ 'rating' ] = '<span class="mpc-product__rating">' . $this->build_rating( $atts[ 'rating_score' ], $atts_rating, $atts_rating_score ). '</span>';
			$this->parts[ 'categories' ] = '<span class="mpc-product__tax' . $classes_tax . '">' . $this->get_taxonomies( $atts[ 'tax_separator' ] ) . '</span>';
			$this->parts[ 'atc_button' ] = '<span class="mpc-product__atc">' . $MPC_WC_Add_To_Cart->shortcode_template( $atts_button ) . '</span>';

			$this->parts[ 'content' ]   = $atts[ 'main_elements' ] != '' ? '<div class="mpc-product__content mpc-transition">' . $this->build_from_list( $atts[ 'main_elements' ], '', false ) . '</div>' : '';
			$this->parts[ 'hover_content' ] = $atts[ 'hover_elements' ] != '' ?'<div class="mpc-product__content-hover mpc-transition">' . $this->build_from_list( $atts[ 'hover_elements' ], '', false ). '</div>' : '';
			$this->parts[ 'thumb_content' ] = $atts[ 'thumb_elements' ] != '' ? '<div class="mpc-thumb__content mpc-transition">' . $this->build_from_list( $atts[ 'thumb_elements' ], '', false ). '</div>' : '';
			$this->parts[ 'thumb_hover_content' ] = $atts[ 'thumb_hover_elements' ] != '' ? '<div ' . $js_link . ' class="mpc-thumb__content-hover mpc-transition">' . $this->build_from_list( $atts[ 'thumb_hover_elements' ], '', false ). '</div>' : '';
			$this->parts[ 'thumbnail' ] = $this->build_thumbnail( $atts[ 'tiles_size' ], $image_size, $atts );

			$per_product_atts = apply_filters( 'ma/product/product_atts', '', $atts, $this->post->ID );

			$content .= '<div class="mpc-wc-product' . $classes_effect . '"' . $per_product_atts . '>';
				$content .= '<div class="mpc-product__wrapper">';
					$content .= $this->shortcode_layout();
				$content .= '</div>';
			$content .= '</div>';

			wp_reset_postdata();
			$this->post = null;

			return $content;
		}


		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null, $shortcode = null, $parent_css = null ) {
			if( !class_exists( 'WooCommerce' ) ) {
				return '';
			}

			global $MPC_WC_Add_To_Cart, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts_button = MPC_Parser::shortcode( $atts, 'mpc_wc_add_to_cart_' );
			$atts = shortcode_atts( $this->defaults, $atts );

			/* Get Post */
			if ( ! is_object( $this->post ) && isset( $atts[ 'id' ] ) ) {
				$this->get_post( (int) $atts[ 'id' ] );
			}

			if( $this->post === null ) {
				return '';
			}

			/* Setup post data */
			global $post; $post = $this->post;
			$product = wc_setup_product_data( $post );

			/* Prepare */
			$atts[ 'thumb_box_prefix' ] = $atts[ 'thumb_inline' ] != '' ? 'inline' : 'block';

			$styles = $this->shortcode_styles( $atts, $parent_css );
			$css_id = $styles[ 'id' ];
			$css_id = ! empty( $parent_css ) ? '' : ' id="' . $css_id . '"';

			if( !isset( $parent_css[ 'selector' ] ) ) {
				$parent_css = array(
					'id' => $styles[ 'id' ],
					'selector' => '.mpc-wc-product[id="' . $styles[ 'id' ] . '"] .mpc-wc-add_to_cart-wrap'
				);
			} else {
				$parent_css[ 'selector' ] .= ' .mpc-wc-add_to_cart-wrap';
			}

			$effects = array(
				'buttons' => $atts[ 'buttons_list' ] != '' ? $atts[ 'buttons_effect' ] : '',
				'content' => $atts[ 'main_effect' ],
			);
			$atts[ 'thumb_elements' ] = $atts[ 'thumb_elements' ] != '' ? $atts[ 'thumb_box_prefix' ] . '_begin,' . $atts[ 'thumb_elements' ] . ',section_end' : '';
			$atts[ 'thumb_hover_elements' ] = $atts[ 'thumb_hover_elements' ] != '' ? $atts[ 'thumb_box_prefix' ] . '_begin,' . $atts[ 'thumb_hover_elements' ] . ',section_end' : '';

			$animation = MPC_Parser::animation( $atts );

			/* Buttons */
			if( $atts[ 'buttons_list' ] != '' ) {
				$atts_lightbox = MPC_Parser::icon( $atts, 'buttons_lb' );
				$atts_url      = MPC_Parser::icon( $atts, 'buttons_url' );
				$atts_lightbox[ 'class' ] .= $atts_lightbox[ 'class' ] != '' && $atts[ 'buttons_lb_mirror' ] != '' ? ' mpc-icon--mirror' : '';
				$atts_url[ 'class' ] .= $atts_url[ 'class' ] != '' && $atts[ 'buttons_url_mirror' ] != '' ? ' mpc-icon--mirror' : '';
			}
			$atts_rating       = MPC_Parser::icon( $atts, 'rating' );
			$atts_rating_score = MPC_Parser::icon( $atts, 'rating_score' );

			/* Shortcode classes | Animation | Layout */
			$this->classes = ' mpc-init mpc-transition'; // mpc-transition
			$this->classes .= $animation != '' ? ' mpc-animation' : '';
			$this->classes .= $atts[ 'buttons_list' ] != '' && $atts[ 'buttons_on_hover' ] != '' ? ' mpc-buttons--on-hover' : '';
			$this->classes .= $atts[ 'thumb_animation_type' ] == 'move' ? ' mpc--no-replace' : ' mpc--force-replace';
			$this->classes .= $atts[ 'thumb_inline' ] != '' ? ' mpc--floating-box' : '';
			$classes_effect = $atts[ 'fx_effect' ] != '' ? ' mpc-effect--' . $atts[ 'fx_effect' ] : '';

			$this->attributes = $animation;
			$this->attributes .= $this->get_attr( 'effects', $effects );
			$this->attributes .= $atts[ 'buttons_list' ] != '' ? $this->get_attr( 'buttons', array( $atts[ 'buttons_align' ], $atts[ 'buttons_direction' ] ) ) : '';

			if( $atts[ 'thumb_animation_type' ] == 'replace' ) {
				$thumb_effects[] = $atts[ 'thumb_effect' ] != '' && $atts[ 'thumb_elements' ] != '' ? $atts[ 'thumb_effect' ] : 'none';
				$thumb_effects[] = $atts[ 'thumb_hover_effect' ] != '' && $atts[ 'thumb_hover_elements' ] != '' ? $atts[ 'thumb_hover_effect' ] : 'none';
				$this->attributes .=  ' data-thumb-effects="' . join( '|', $thumb_effects ) . '"';
			}

			$positions[] = $atts[ 'thumb_position' ] . ':' . $atts[ 'thumb_alignment' ];
			$positions[] = $atts[ 'thumb_hover_position' ] . ':' . $atts[ 'thumb_hover_alignment' ];
			$this->attributes .= ' data-thumb-positions="' . join( '|', $positions ) . '"';

			$classes_title = ' mpc-transition';
			$classes_title .= $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'title_font_preset' ] : '';
			$classes_title .= $atts[ 'title_overflow' ] != '' ? '' : ' mpc-text-overflow';

			$classes_price = $atts[ 'price_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'price_font_preset' ] : '';
			$classes_tax = $atts[ 'tax_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'tax_font_preset' ] : '';

			/* Shortcode Output */
			$atts[ 'rating_score' ]   = $this->get_rating( $product );
			$atts_button[ 'item_id' ] = get_the_ID();

			/* Buttons */
			if( $atts[ 'buttons_list' ] != '' ) {
				$this->parts[ 'button_lightbox' ] = $this->get_icon( $atts_lightbox, 'lightbox' );
				$this->parts[ 'button_url' ]     = $this->get_icon( $atts_url, 'url' );
				$this->parts[ 'buttons' ]         = $this->build_from_list( $atts[ 'buttons_list' ], 'button', 'buttons' );
			}

			/* JS link */
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$js_link   = $this->get_js_link( $permalink );

			/* Image Quality */
			$image_size = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'large';

			$this->parts[ 'title' ]  = '<span class="mpc-product__heading' . $classes_title . '">' . $this->get_title() . '</span>';
			$this->parts[ 'price' ]  = '<span class="mpc-product__price' . $classes_price . '">' . $this->get_price( $product ) . '</span>';
			$this->parts[ 'rating' ] = '<span class="mpc-product__rating">' . $this->build_rating( $atts[ 'rating_score' ], $atts_rating, $atts_rating_score ). '</span>';
			$this->parts[ 'categories' ] = '<span class="mpc-product__tax' . $classes_tax . '">' . $this->get_taxonomies( $atts[ 'tax_separator' ] ) . '</span>';
			$this->parts[ 'atc_button' ] = '<span class="mpc-product__atc">' . $MPC_WC_Add_To_Cart->shortcode_template( $atts_button, null, null, $parent_css ) . '</span>';

			$this->parts[ 'content' ]   = $atts[ 'main_elements' ] != '' ? '<div class="mpc-product__content mpc-transition">' . $this->build_from_list( $atts[ 'main_elements' ], '', false ) . '</div>' : '';
			$this->parts[ 'hover_content' ] = $atts[ 'hover_elements' ] != '' ?'<div class="mpc-product__content-hover mpc-transition">' . $this->build_from_list( $atts[ 'hover_elements' ], '', false ). '</div>' : '';
			$this->parts[ 'thumb_content' ] = $atts[ 'thumb_elements' ] != '' ? '<div class="mpc-thumb__content mpc-transition">' . $this->build_from_list( $atts[ 'thumb_elements' ], '', false ). '</div>' : '';
			$this->parts[ 'thumb_hover_content' ] = $atts[ 'thumb_hover_elements' ] != '' ? '<div ' . $js_link . ' class="mpc-thumb__content-hover mpc-transition">' . $this->build_from_list( $atts[ 'thumb_hover_elements' ], '', false ). '</div>' : '';
			$this->parts[ 'thumbnail' ] = $this->build_thumbnail( $atts[ 'tiles_size' ], $image_size, $atts );

			$return = '<div class="mpc-product__wrapper">';
				$return .= $this->shortcode_layout();
			$return .= '</div>';

			/* Wrapper check */
			if( ! $this->is_wrapped ) {
				$return = '<div' . $css_id . ' class="mpc-wc-product' . $this->classes . $classes_effect . '"' . $this->attributes . '>' . $return . '</div>';
				$this->reset();
			} else {
				$per_product_atts = apply_filters( 'ma/product/product_atts', '', $atts, $this->post->ID );
				$return = '<div class="mpc-wc-product' . $classes_effect . '"' . $per_product_atts . '>' . $return . '</div>';
				$this->post = null;
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				if ( ! $this->is_wrapped ) {
					$return .= '<style>' . $styles[ 'css' ] . '</style>';
				}
			}

			/* Restore original Post Data */
			wp_reset_postdata();
			unset( $product );

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $parent_css ) {
			global $mpc_massive_styles;
			$css_id       = uniqid( $this->shortcode . '-' . rand( 1, 100 ) );
			$css_selector = '.mpc-wc-product[id="' . $css_id . '"]';
			$style        = '';

			if ( is_array( $parent_css ) ) {
				$css_id       = $parent_css[ 'id' ];
				$css_selector = $parent_css[ 'selector' ];
			}

			if( $this->style === $css_id ) {
				return array(
					'id'  => $css_id,
					'css' => $this->style,
				);
			}

			// Add 'px'
			$styles[ 'height' ]    = $styles[ 'height' ] != '' ? $styles[ 'height' ] . ( is_numeric( $styles[ 'height' ] ) ? 'px' : '' ) : '';
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'price_font_size' ] = $styles[ 'price_font_size' ] != '' ? $styles[ 'price_font_size' ] . ( is_numeric( $styles[ 'price_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'tax_font_size' ] = $styles[ 'tax_font_size' ] != '' ? $styles[ 'tax_font_size' ] . ( is_numeric( $styles[ 'tax_font_size' ] ) ? 'px' : '' ) : '';
			$icon_size = $styles[ 'buttons_size' ] != '' ? $styles[ 'buttons_size' ] . ( is_numeric( $styles[ 'buttons_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'buttons_lb_icon_size' ] = $styles[ 'buttons_url_icon_size' ] = $icon_size;
			$styles[ 'buttons_lb_icon_color' ] = $styles[ 'buttons_url_icon_color' ] = $styles[ 'buttons_color' ];
			$styles[ 'rating_icon_size' ] = $styles[ 'rating_icon_size' ] != '' ? $styles[ 'rating_icon_size' ] . ( is_numeric( $styles[ 'rating_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'rating_score_icon_size' ] = $styles[ 'rating_icon_size' ];

			// Border
			if ( $styles[ 'border_css' ] != '' ) {
				$style .= $css_selector . ' .mpc-product__wrapper {';
					$style .= $styles[ 'border_css' ];
				$style .= '}';
			}

			// Thumb Height
			if ( $styles[ 'height' ] != '' && $styles[ 'tiles_size' ] !== 'full' ) {
				$style .= $css_selector . ' .mpc-product__thumb-wrap {';
					$style .= 'height: ' . $styles[ 'height' ] . ';';
				$style .= '}';
			}
			$style .= MPC_CSS::effect( $css_selector, $styles );

			// Regular
			$inner_styles = array();
			if ( $styles[ 'main_border_css' ] != '' ) { $inner_styles[] = $styles[ 'main_border_css' ]; }
			if ( $styles[ 'main_padding_css' ] != '' ) { $inner_styles[] = $styles[ 'main_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'main' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-product__content {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'hover_border_css' ] != '' ) { $inner_styles[] = $styles[ 'hover_border_css' ]; }
			if ( $styles[ 'hover_padding_css' ] != '' ) { $inner_styles[] = $styles[ 'hover_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-product__content-hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Thumb Overlay
			$inner_styles = array();
			if ( $styles[ 'thumb_border_css' ] != '' ) { $inner_styles[] = $styles[ 'thumb_border_css' ]; }
			if ( $styles[ 'thumb_padding_css' ] != '' ) { $inner_styles[] = $styles[ 'thumb_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'thumb' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$selector = $styles[ 'thumb_fullfill' ] == '' ? $css_selector . ' .mpc-thumb__content .mpc-' . $styles[ 'thumb_box_prefix' ] . '-box' :  $css_selector . ' .mpc-thumb__content';
				$style .= $selector . ' {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			$inner_styles = array();
			if ( $styles[ 'thumb_hover_border_css' ] != '' ) { $inner_styles[] = $styles[ 'thumb_hover_border_css' ]; }
			if ( $styles[ 'thumb_hover_padding_css' ] != '' ) { $inner_styles[] = $styles[ 'thumb_hover_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'thumb_hover' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$selector = $styles[ 'thumb_hover_fullfill' ] == '' ? $css_selector . ' .mpc-thumb__content-hover .mpc-' . $styles[ 'thumb_box_prefix' ] . '-box' : $css_selector . ' .mpc-thumb__content-hover';
				$style .= $selector . ' {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Typography
			$inner_styles = array();
			if ( $styles[ 'title_margin_css' ] != '' ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-product__heading {';
				$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'title_hover_color' ] != '' ) {
				$style .= $css_selector . ' .mpc-product__heading a:hover {';
					$style .= 'color: ' . $styles[ 'title_hover_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'price_margin_css' ] != '' ) { $inner_styles[] = $styles[ 'price_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'price' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-product__price {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'price_sale_color' ] != '' ) {
				$style .= $css_selector . ' .mpc-product__price ins {';
					$style .= 'color: ' . $styles[ 'price_sale_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'tax_margin_css' ] != '' ) { $inner_styles[] = $styles[ 'tax_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'tax' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-product__tax {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'tax_link_color' ] != '' ) {
				$style .= $css_selector . ' .mpc-product__tax a {';
					$style .= 'color: ' . $styles[ 'tax_link_color' ] . ';';
				$style .= '}';
			}
			if ( $styles[ 'tax_hover_color' ] ) {
				$style .= $css_selector . ' .mpc-product__tax a:hover {';
					$style .= 'color: ' . $styles[ 'tax_hover_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'rating_margin_css' ] != '' ) { $inner_styles[] = $styles[ 'rating_margin_css' ]; }
			if ( $styles[ 'rating_align' ] != '' ) { $inner_styles[] = 'text-align:' . $styles[ 'rating_align' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-product__rating {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if( $temp_style = MPC_CSS::icon( $styles, 'rating' ) ) {
				$style .= $css_selector . ' .mpc-rating__icon {';
					$style .= $temp_style;
				$style .= '}';
			}
			if( $temp_style = MPC_CSS::icon( $styles, 'rating_score' ) ) {
				$style .= $css_selector . ' .mpc-rating__score-icon {';
					$style .= $temp_style;
				$style .= '}';
			}
			if( $styles[ 'rating_gap' ] != '' ) {
				$style .= $css_selector . ' .mpc-rating i {';
					$style .= 'margin-right:' . $styles[ 'rating_gap' ] . 'px;';
				$style .= '}';
			}

			/* Buttons */
			$inner_styles = array();
			if ( $styles[ 'buttons_padding_css' ] != '' ) { $inner_styles[] = $styles[ 'buttons_padding_css' ]; }
			if ( $styles[ 'buttons_border_css' ] != '' ) { $inner_styles[] = $styles[ 'buttons_border_css' ]; }
			if ( $styles[ 'buttons_bg' ] != '' ) { $inner_styles[] = 'background-color:' . $styles[ 'buttons_bg' ] . ';'; }
			if ( $styles[ 'buttons_gap' ] != '' && $styles[ 'buttons_gap' ] > 0 ) { $inner_styles[] = $styles[ 'buttons_direction' ] == 'vertical' ? 'margin-bottom: ' . $styles[ 'buttons_gap' ] . 'px;' : 'margin-right: ' . $styles[ 'buttons_gap' ] . 'px;';
			}
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-thumb__buttons .mpc-icon-anchor {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'buttons_margin_css' ] ) {
				$style .= $css_selector . ' .mpc-thumb__buttons {';
					$style .= str_replace( 'margin', 'padding', $styles[ 'buttons_margin_css' ] );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'buttons_lb' ) ) {
				$style .= $css_selector . ' .mpc-type--lightbox {';
				$style .= $temp_style;
				$style .= '}';
			}
			if ( $temp_style = MPC_CSS::icon( $styles, 'buttons_url' ) ) {
				$style .= $css_selector . ' .mpc-type--url {';
				$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'buttons_hover_bg' ] != '' ) { $inner_styles[] = 'background-color:' . $styles[ 'buttons_hover_bg' ] . ';'; }
			if ( $styles[ 'buttons_hover_border' ] != '' ) { $inner_styles[] = 'border-color:' . $styles[ 'buttons_hover_border' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-thumb__buttons .mpc-icon-anchor:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'buttons_hover_color' ] != '' ) {
				$style .= $css_selector . ' .mpc-thumb__buttons .mpc-icon-anchor:hover i {';
					$style .= 'color: ' . $styles[ 'buttons_hover_color' ] . ';';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;
			$this->style = $css_id;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Item Preset', 'mpc' ),
					'param_name'  => 'preset',
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Specify item preset.', 'mpc' ),
					'group'       => __( 'Product', 'mpc' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Thumbnail Height', 'mpc' ),
					'param_name'       => 'height',
					'description'      => __( 'Specify height for thumbnails.', 'mpc' ),
					'min'              => 0,
					'max'              => 1000,
					'step'             => 1,
					'value'            => 300,
					'unit'             => 'px',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Image Quality', 'mpc' ),
					'param_name'       => 'image_size',
					'tooltip'          => __( 'Define image quality by it\'s size. You can use default WordPress & WooCommerce sizes (<em>thumbnail</em>, <em>medium</em>, <em>large</em>, <em>full</em>, <em>woocommerce_thumbnail</em>, <em>woocommerce_single</em>, <em>woocommerce_gallery_thumbnail</em> ) or pass exact size by width and height in this format: 100x200.', 'mpc' ),
					'value'            => 'large',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => false,
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-input--large',
				),

			);

			$source = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Source', 'mpc' ),
					'subtitle'   => __( 'Specify the source.', 'mpc' ),
					'param_name' => 'source_section_divider',
					'group'      => __( 'Product', 'mpc' ),
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => __( 'Product', 'mpc' ),
					'param_name'  => 'id',
					'settings'    => array(
						'multiple'      => false,
						'sortable'      => false,
						'unique_values' => true,
					),
					'description' => __( 'Select post to display.', 'mpc' ),
					'group'       => __( 'Product', 'mpc' ),
				),
			);

			$thumb_buttons_available = array(
				'lightbox' => __( 'Lightbox', 'mpc' ),
				'url'     => __( 'Permalink', 'mpc' ),
			);
			$thumb_buttons_dependency = array(
				'element'   => 'buttons_list',
				'not_empty' => true
			);

			$thumb_buttons         = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Icon Buttons', 'mpc' ),
					'subtitle'         => __( 'Specify settings for buttons on thumbnail.', 'mpc' ),
					'param_name'       => 'buttons_section_divider',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_list',
					'heading'          => __( 'Buttons List', 'mpc' ),
					'param_name'       => 'buttons_list',
					'description'      => __( 'Enable blocks and place them in desired order.', 'mpc' ),
					'value'            => 'lightbox',
					'options'          => $thumb_buttons_available,
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Buttons Alignment', 'mpc' ),
					'param_name'       => 'buttons_align',
					'value'            => '',
					'std'              => 'top-left',
					'grid_size'        => 'large',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Buttons Direction', 'mpc' ),
					'param_name'       => 'buttons_direction',
					'description'      => __( 'Select buttons direction', 'mpc' ),
					'value'            => array(
						__( 'Vertical', 'mpc' )   => 'vertical',
						__( 'Horizontal', 'mpc' ) => 'horizontal',
					),
					'std'              => 'vertical',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Buttons Gap', 'mpc' ),
					'param_name'       => 'buttons_gap',
					'description'      => __( 'Specify gap between buttons.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 1,
					'unit'             => 'px',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-product-effect mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Buttons Appear Effect', 'mpc' ),
					'subtitle'         => __( 'Specify settings for buttons appear effect thumbnail.', 'mpc' ),
					'param_name'       => 'buttons_appear_section_divider',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Show on hover', 'mpc' ),
					'param_name'       => 'buttons_on_hover',
					'description'      => __( 'Enable this option to show buttons on thumbnail hover.', 'mpc' ),
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Buttons Effect', 'mpc' ),
					'param_name'       => 'buttons_effect',
					'description'      => __( 'Select effect for buttons', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )            => 'fade-in',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade-in',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'   => 'buttons_on_hover',
						'not_empty' => true
					),
				),
			);
			$thumb_buttons_colors  = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Buttons - Colors & Size', 'mpc' ),
					'subtitle'         => __( 'Specify settings for buttons colors.', 'mpc' ),
					'param_name'       => 'buttons_colors_section_divider',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'buttons_size',
					'tooltip'          => __( 'Define icon size for Icon Font and Character types.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-textcolor',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'buttons_color',
					'value'            => '#333333',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background', 'mpc' ),
					'param_name'       => 'buttons_bg',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'dependency'       => $thumb_buttons_dependency,
				),
			);
			$thumb_buttons_hover   = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Buttons - Hover Colors', 'mpc' ),
					'subtitle'         => __( 'Specify colors for buttons color on hover state.', 'mpc' ),
					'param_name'       => 'buttons_hover_section_divider',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'buttons_hover_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background', 'mpc' ),
					'param_name'       => 'buttons_hover_bg',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'dependency'       => $thumb_buttons_dependency,
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border', 'mpc' ),
					'param_name'       => 'buttons_hover_border',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Icon Buttons', 'mpc' ),
					'dependency'       => $thumb_buttons_dependency,
				),
			);
			$thumb_buttons_url     = MPC_Snippets::vc_icon( array(
				'prefix'     => 'buttons_url',
				'subtitle'   => __( 'Permalink', 'mpc' ),
				'group'      => __( 'Icon Buttons', 'mpc' ),
				'with_size'  => false,
				'with_color' => false
			) );
			$thumb_buttons_lb      = MPC_Snippets::vc_icon( array(
				'prefix'     => 'buttons_lb',
				'subtitle'   => __( 'Lightbox', 'mpc' ),
				'group'      => __( 'Icon Buttons', 'mpc' ),
				'with_size'  => false,
				'with_color' => false
			) );
			$thumb_buttons_border  = MPC_Snippets::vc_border( array(
				'prefix'     => 'buttons',
				'subtitle'   => __( 'Buttons', 'mpc' ),
				'group'      => __( 'Icon Buttons', 'mpc' ),
				'dependency' => $thumb_buttons_dependency
			) );
			$thumb_buttons_margin  = MPC_Snippets::vc_margin( array(
				'prefix'     => 'buttons',
				'subtitle'   => __( 'Buttons', 'mpc' ),
				'group'      => __( 'Icon Buttons', 'mpc' ),
				'dependency' => $thumb_buttons_dependency
			) );
			$thumb_buttons_padding = MPC_Snippets::vc_padding( array(
				'prefix'     => 'buttons',
				'subtitle'   => __( 'Buttons', 'mpc' ),
				'group'      => __( 'Icon Buttons', 'mpc' ),
				'dependency' => $thumb_buttons_dependency
			) );

			/* Sections */
			$elements_available       = array(
				'title'      => __( 'Title', 'mpc' ),
				'price'      => __( 'Price', 'mpc' ),
				'rating'     => __( 'Rating', 'mpc' ),
				'categories' => __( 'Categories', 'mpc' ),
				'atc_button' => __( 'Add To Cart', 'mpc' ),
			);
			$main_section_dependency  = array(
				'element'   => 'main_elements',
				'not_empty' => true
			);
			$hover_section_dependency = array(
				'element'   => 'hover_elements',
				'not_empty' => true
			);
			$thumb_section_dependency = array(
				'element'   => 'thumb_elements',
				'not_empty' => true
			);
			$thumb_hover_section_dependency = array(
				'element'   => 'thumb_hover_elements',
				'not_empty' => true
			);

			$main_section            = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Main Section', 'mpc' ),
					'subtitle'         => __( 'Specify settings for slider items.', 'mpc' ),
					'param_name'       => 'main_section_divider',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_list',
					'heading'          => __( 'Lower Bar', 'mpc' ),
					'param_name'       => 'main_elements',
					'description'      => __( 'Enable blocks and place them in desired order.', 'mpc' ),
					'value'            => 'title,price',
					'options'          => $elements_available,
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_list',
					'heading'          => __( 'After Hover', 'mpc' ),
					'param_name'       => 'hover_elements',
					'description'      => __( 'Enable blocks and place them in desired order.', 'mpc' ),
					'value'            => 'atc_button',
					'options'          => $elements_available,
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);
			$main_section_border     = MPC_Snippets::vc_border( array(
				'prefix'     => 'main',
				'subtitle'   => __( 'Main Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $main_section_dependency,
				'with_radius' => false,
			) );
			$main_section_padding    = MPC_Snippets::vc_padding( array(
				'prefix'     => 'main',
				'subtitle'   => __( 'Main Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $main_section_dependency,
			) );
			$main_section_background = MPC_Snippets::vc_background( array(
				'prefix'     => 'main',
				'subtitle'   => __( 'Main Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $main_section_dependency,
			) );

			$hover_section            = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Hover Section', 'mpc' ),
					'subtitle'         => __( 'Specify settings for slider items.', 'mpc' ),
					'param_name'       => 'hover_section_divider',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => $hover_section_dependency,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Appear Effect', 'mpc' ),
					'param_name'       => 'main_effect',
					'description'      => __( 'Select effect for interactive content element', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )            => 'fade-in',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
						__( 'Push To Top', 'mpc' )     => 'push-up',
						__( 'Push To Bottom', 'mpc' )  => 'push-down',
						__( 'Push To Left', 'mpc' )    => 'push-left',
						__( 'Push To Right', 'mpc' )   => 'push-right',
					),
					'std'              => 'fade-in',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => $hover_section_dependency,
				),
			);
			$hover_section_border     = MPC_Snippets::vc_border( array(
				'prefix'     => 'hover',
				'subtitle'   => __( 'Hover Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $hover_section_dependency,
				'with_radius' => false,
			) );
			$hover_section_padding    = MPC_Snippets::vc_padding( array(
				'prefix'     => 'hover',
				'subtitle'   => __( 'Hover Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $hover_section_dependency,
			) );
			$hover_section_background = MPC_Snippets::vc_background( array(
				'prefix'     => 'hover',
				'subtitle'   => __( 'Hover Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $hover_section_dependency,
			) );

			$animation_replace = array( 'element' => 'thumb_animation_type', 'value' => 'replace' );
			$inline_content    = array( 'element' => 'thumb_inline', 'not_empty' => true );
			$thumb_section            = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Thumb Section', 'mpc' ),
					'subtitle'         => __( 'Specify settings for slider items.', 'mpc' ),
					'param_name'       => 'thumb_section_divider',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Animation Type', 'mpc' ),
					'param_name'  => 'thumb_animation_type',
					'value'       => array(
						__( 'Replace regular content', 'mpc' ) => 'replace',
						__( 'Move regular content', 'mpc' )    => 'move',
					),
					'std'         => 'replace',
					'description' => __( 'Select the animation type.', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Inline Content', 'mpc' ),
					'param_name'       => 'thumb_inline',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to disable fullwidth content (will create a floating box).', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_list',
					'heading'          => __( 'Elements & Order', 'mpc' ),
					'param_name'       => 'thumb_elements',
					'description'      => __( 'Enable blocks and place them in desired order.', 'mpc' ),
					'value'            => '',
					'options'          => $elements_available,
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-clear--both',
				),
				array(
					'type'             => 'mpc_list',
					'heading'          => __( 'After Hover', 'mpc' ),
					'param_name'       => 'thumb_hover_elements',
					'description'      => __( 'Enable blocks and place them in desired order.', 'mpc' ),
					'value'            => '',
					'options'          => $elements_available,
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-clear-both',
					'dependency'       => $animation_replace,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Out Effect', 'mpc' ),
					'param_name'       => 'thumb_effect',
					'description'      => __( 'Select effect for regular content over thumb.', 'mpc' ),
					'value'            => array(
						__( 'Stay', 'mpc' )            => 'stay',
						__( 'Fade', 'mpc' )            => 'fade-in',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade-in',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => $animation_replace,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Position', 'mpc' ),
					'param_name'  => 'thumb_position',
					'value'       => array(
						__( 'Top', 'mpc' )     => 'top',
						__( 'Middle', 'mpc' )  => 'middle',
						__( 'Bottom', 'mpc' )  => 'bottom',
					),
					'std'         => 'bottom',
					'description' => __( 'Select the content position.', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear--both',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'thumb_alignment',
					'value'            => array(
						__( 'Left', 'mpc' )   => 'left',
						__( 'Center', 'mpc' ) => 'center',
						__( 'Right', 'mpc' )  => 'right',
					),
					'std'              => 'left',
					'description'      => __( 'Select the content position.', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $inline_content,
				),
			);
			$thumb_section_border     = MPC_Snippets::vc_border( array(
				'prefix'     => 'thumb',
				'subtitle'   => __( 'Thumb Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $thumb_section_dependency,
			) );
			$thumb_section_padding    = MPC_Snippets::vc_padding( array(
				'prefix'     => 'thumb',
				'subtitle'   => __( 'Thumb Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $thumb_section_dependency,
			) );
			$thumb_section_background = MPC_Snippets::vc_background( array(
				'prefix'     => 'thumb',
				'subtitle'   => __( 'Thumb Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $thumb_section_dependency,
			) );
			$thumb_fullfill = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Fullfill Area', 'mpc' ),
					'param_name'       => 'thumb_fullfill',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to fill whole area.', 'mpc' ),
					'group'      => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => $inline_content,
				),
			);

			$thumb_hover_section = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Hover Thumb Section', 'mpc' ),
					'subtitle'         => __( 'Specify settings for slider items.', 'mpc' ),
					'param_name'       => 'thumb_hover_section_divider',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'In Effect', 'mpc' ),
					'param_name'       => 'thumb_hover_effect',
					'description'      => __( 'Select effect for hover content over thumb.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )            => 'fade-in',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade-in',
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-clear--both',
					'dependency'       => $animation_replace,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Position', 'mpc' ),
					'param_name'  => 'thumb_hover_position',
					'value'       => array(
						__( 'Top', 'mpc' )     => 'top',
						__( 'Middle', 'mpc' )  => 'middle',
						__( 'Bottom', 'mpc' )  => 'bottom',
					),
					'std'         => 'bottom',
					'description' => __( 'Select the content position.', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear--both',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'thumb_hover_alignment',
					'value'            => array(
						__( 'Left', 'mpc' )   => 'left',
						__( 'Center', 'mpc' ) => 'center',
						__( 'Right', 'mpc' )  => 'right',
					),
					'std'              => 'left',
					'description'      => __( 'Select the content position.', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $inline_content,
				),
			);
			$thumb_hover_section_border     = MPC_Snippets::vc_border( array(
				'prefix'     => 'thumb_hover',
				'subtitle'   => __( 'Hover Thumb Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $thumb_hover_section_dependency,
			) );
			$thumb_hover_section_padding    = MPC_Snippets::vc_padding( array(
				'prefix'     => 'thumb_hover',
				'subtitle'   => __( 'Hover Thumb Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $thumb_hover_section_dependency,
			) );
			$thumb_hover_section_background = MPC_Snippets::vc_background( array(
				'prefix'     => 'thumb_hover',
				'subtitle'   => __( 'Hover Thumb Section', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
				'dependency' => $thumb_hover_section_dependency,
			) );
			$thumb_hover_fullfill = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Fullfill Area', 'mpc' ),
					'param_name'       => 'thumb_hover_fullfill',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to fill whole area.', 'mpc' ),
					'group'            => __( 'Product', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => $inline_content,
				),
			);

			/* Title Block */
			$title_overflow = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Title Overflow', 'mpc' ),
					'param_name'       => 'title_overflow',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to option to show the full title.', 'mpc' ),
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field',
				),
			);
			$title_hover_color = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'title_hover_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
			);
			$title_font        = MPC_Snippets::vc_font( array(
				'prefix'   => 'title',
				'subtitle' => __( 'Title', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );
			$title_margin      = MPC_Snippets::vc_margin( array(
				'prefix'   => 'title',
				'subtitle' => __( 'Title', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );

			/* Price Block */
			$price_sale_color = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Sale Price Color', 'mpc' ),
					'param_name'       => 'price_sale_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
			);
			$price_font       = MPC_Snippets::vc_font( array(
				'prefix'   => 'price',
				'subtitle' => __( 'Price', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );
			$price_margin     = MPC_Snippets::vc_margin( array(
				'prefix'   => 'price',
				'subtitle' => __( 'Price', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );

			/* Taxonomies Block */
			$tax_hover_color = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Link Color', 'mpc' ),
					'param_name'       => 'tax_link_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'tax_hover_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Taxonomies Separator', 'mpc' ),
					'param_name'       => 'tax_separator',
					'value'            => ', ',
					'tooltip'          => __( 'Specify a character to separate each of taxonomies.', 'mpc' ),
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);
			$tax_font = MPC_Snippets::vc_font( array(
				'prefix'   => 'tax',
				'subtitle' => __( 'Categories', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );
			$tax_margin = MPC_Snippets::vc_margin( array(
				'prefix'   => 'tax',
				'subtitle' => __( 'Categories', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );

			// Ratings
			$rating = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Rating', 'mpc' ),
					'subtitle'         => __( 'Specify settings for rating.', 'mpc' ),
					'param_name'       => 'rating_section_divider',
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'rating_align',
					'description'      => __( 'Select alignemnt for rating.', 'mpc' ),
					'value'            => array(
						__( 'Inherit', 'mpc' ) => 'inherit',
						__( 'Left', 'mpc' )    => 'left',
						__( 'Center', 'mpc' )  => 'center',
						__( 'Right', 'mpc' )   => 'right',
					),
					'std'              => 'inherit',
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Icons Gap', 'mpc' ),
					'param_name'       => 'rating_gap',
					'description'      => __( 'Specify gap between icons.', 'mpc' ),
					'min'              => 0,
					'max'              => 10,
					'step'             => 1,
					'value'            => 1,
					'unit'             => 'px',
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field',
				),
			);
			$rating_icon = array(
				array(
					'type'             => 'mpc_icon',
					'heading'          => __( 'Select icon', 'mpc' ),
					'param_name'       => 'rating_icon',
					'tooltip'          => __( 'Choose icon that you like. You can change the icons library at the top. You can also search the icons by keywords. Remember to use as few different icons libraries across your page as possible.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both',
					'group'            => __( 'Elements', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'rating_icon_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'rating_icon_size',
					'tooltip'          => __( 'Define icon size.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-textcolor',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'group'            => __( 'Elements', 'mpc' ),
				)
			);
			$rating_icon_score = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Rating Value Style', 'mpc' ),
					'subtitle'         => __( 'Set rating value style.', 'mpc' ),
					'param_name'       => 'rating_value_section_divider',
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'mpc_icon',
					'heading'          => __( 'Select icon', 'mpc' ),
					'param_name'       => 'rating_score_icon',
					'tooltip'          => __( 'Choose icon that you like. You can change the icons library at the top. You can also search the icons by keywords. Remember to use as few different icons libraries across your page as possible.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'rating_score_icon_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Elements', 'mpc' ),
				),
			);
			$rating_margin = MPC_Snippets::vc_margin( array(
				'prefix'   => 'rating',
				'subtitle' => __( 'Rating', 'mpc' ),
				'group'    => __( 'Elements', 'mpc' ),
			) );

			/* General */
			$border = MPC_Snippets::vc_border( array(
				'subtitle'   => __( 'Product', 'mpc' ),
				'group'      => __( 'Product', 'mpc' ),
			) );
			$animation = MPC_Snippets::vc_animation_basic();
			$effects   = MPC_Snippets::vc_effects_filters( array( 'subtitle' => __( 'Thumbnail' ), 'group' => __( 'Product', 'mpc' ) ) );

			/* Integrate Button */
			$atc_exclude   = array( 'exclude_regex' => '/animation_(.*)|tooltip(.*)|item_id/', );
			$integrate_atc = vc_map_integrate_shortcode( 'mpc_wc_add_to_cart', 'mpc_wc_add_to_cart__', __( 'Add To Cart', 'mpc' ), $atc_exclude );

			$params = array_merge(
				$base,
				$source,

				$main_section,
				$main_section_background,
				$main_section_border,
				$main_section_padding,

				$hover_section,
				$hover_section_background,
				$hover_section_border,
				$hover_section_padding,

				$thumb_section,
				$effects,
				$thumb_section_background,
				$thumb_fullfill,
				$thumb_section_border,
				$thumb_section_padding,
				$thumb_hover_section,
				$thumb_hover_section_background,
				$thumb_hover_fullfill,
				$thumb_hover_section_border,
				$thumb_hover_section_padding,

				$border,

				$title_font,
				$title_overflow,
				$title_hover_color,
				$title_margin,

				$price_font,
				$price_sale_color,
				$price_margin,

				$tax_font,
				$tax_hover_color,
				$tax_margin,

				$rating,
				$rating_icon,
				$rating_icon_score,
				$rating_margin,

				$thumb_buttons,
				$thumb_buttons_url,
				$thumb_buttons_lb,
				$thumb_buttons_colors,
				$thumb_buttons_border,
				$thumb_buttons_hover,
				$thumb_buttons_padding,
				$thumb_buttons_margin,

				$integrate_atc,
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
				'name'        => __( 'Product', 'mpc' ),
				'description' => __( 'Display single product', 'mpc' ),
				'base'        => $this->shortcode,
				'icon'        => 'mpc-shicon-wc-product',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}

		/* Get Attr */
		function get_attr( $name, $values = array(), $separator = '|' ) {
			if ( empty( $values ) ) {
				return '';
			}

			if( $name == 'effects' ) {
				$return = '';
				foreach( $values as $name => $effect ) {
					$return .= ' data-' . $name . '-effect="' . $effect . '"';
				}

				return $return;
			}

			return ' data-' . $name . '="' . join( $separator, $values ) . '"';
		}
	}
}

if ( class_exists( 'MPC_WC_Product' ) ) {
	global $MPC_WC_Product;
	$MPC_WC_Product = new MPC_WC_Product;
}
