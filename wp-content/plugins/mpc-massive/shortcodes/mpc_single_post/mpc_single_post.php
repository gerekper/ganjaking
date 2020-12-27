<?php
/*----------------------------------------------------------------------------*\
	SINGLE POST SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Single_Post' ) ) {
	class MPC_Single_Post {
		public $shortcode = 'mpc_single_post';
		private $is_wrapped = false;
		private $post;
		public $style = '';
		public $parts = array();
		public $classes = '';
		public $defaults = array();
		public $before = '';
		public $after  = '';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Read More button */
			add_filter( 'excerpt_more', array( $this, 'remove_excerpt_more' ), 999 );

			/* Autocomplete */
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_item_id_callback', 'vc_include_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_item_id_render', 'vc_include_field_render', 10, 1 );

			$this->parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'overlay_begin' => '',
				'overlay_end'   => '',
				'meta'          => '',
				'readmore'      => '',
				'title'         => '',
				'date'          => '',
				'author'        => '',
				'description'   => '',
				'thumbnail'     => '',
			);

			$this->defaults = array(
				'class'                           => '',
				'preset'                          => '',
				'layout'                          => 'style_1',
				'gap'                             => '',
				'content_over_thumb'              => '',
				'disable_thumbnail'               => '',
				'content_position'                => 'bottom-left',
				'content_effect'                  => 'slide-bottom',

				'item_id'                         => '',
				'item_effect'                     => 'fade',

				'title_disable'                   => '',
				'title_overflow'                  => '',
				'title_font_preset'               => '',
				'title_font_color'                => '',
				'title_font_size'                 => '',
				'title_font_line_height'          => '',
				'title_font_align'                => '',
				'title_font_transform'            => '',
				'title_margin_css'                => '',
				'hover_title_color'               => '',

				'meta_disable'                    => '',
				'meta_layout'                     => 'date,taxonomies',
				'meta_font_preset'                => '',
				'meta_font_color'                 => '',
				'meta_font_size'                  => '',
				'meta_font_line_height'           => '',
				'meta_font_align'                 => '',
				'meta_font_transform'             => '',
				'meta_separator'                  => ', ',
				'meta_tax_separator'              => ', ',
				'meta_link_color'                 => '',
				'meta_margin_css'                 => '',
				'hover_meta_link_color'           => '',

				'date_font_preset'                => '',
				'date_font_color'                 => '',
				'date_font_size'                  => '',
				'date_font_line_height'           => '',
				'date_font_align'                 => '',
				'date_font_transform'             => '',
				'date_padding_css'                => '',
				'date_margin_css'                 => '',
				'date_border_css'                 => '',
				'date_background'                 => '',

				'readmore_disable'                => '',
				'lightbox_disable'                => '',

				'description_disable'             => '',
				'description_font_preset'         => '',
				'description_font_color'          => '',
				'description_font_size'           => '',
				'description_font_line_height'    => '',
				'description_font_align'          => '',
				'description_font_transform'      => '',
				'description_padding_css'         => '',
				'description_margin_css'          => '',

				'background_type'                 => 'color',
				'background_color'                => '',
				'background_image'                => '',
				'background_image_size'           => 'large',
				'background_repeat'               => 'no-repeat',
				'background_size'                 => 'initial',
				'background_position'             => 'middle-center',
				'background_gradient'             => '#83bae3||#80e0d4||0;100||180||linear',
				'overlay_effect'                  => 'fade',
				'overlay_color'                   => 'rgba(255,255,255,0.5)',
				'disable_vinette'                  => '',

				'overlay_title_align'             => '',
				'overlay_title_color'             => '',
				'overlay_hover_title_color'       => '',

				'overlay_meta_align'              => '',
				'overlay_meta_color'              => '',
				'overlay_meta_link_color'         => '',
				'overlay_hover_meta_color'        => '',

				'overlay_date_color'              => '',
				'overlay_date_border'             => '',
				'overlay_date_background'         => '',
				'overlay_date_padding_css'        => '',
				'overlay_date_margin_css'         => '',

				'overlay_description_align'       => '',
				'overlay_description_color'       => '',

				'overlay_icons_padding_css'       => '',
				'overlay_icons_margin_css'        => '',
				'overlay_padding_css'             => '',

				'overlay_title_margin_css'        => '',
				'overlay_meta_margin_css'         => '',
				'overlay_description_margin_css'  => '',
				'overlay_description_padding_css' => '',

				'tiles_size'                      => 'fixed',
				'thumb_height'                    => '300',
				'image_size'                      => 'large',

				'border_css'                      => '',
				'padding_css'                     => '',
				'margin_css'                      => '',

				'animation_in_type'               => 'none',
				'animation_in_duration'           => '300',
				'animation_in_delay'              => '0',
				'animation_in_offset'             => '100',

				'mpc_navigation__preset'          => '',

				/* Overlay Icons */
				'lightbox_icon_type'              => 'icon',
				'lightbox_icon'                   => '',
				'lightbox_icon_preset'            => '',
				'lightbox_icon_character'         => '',
				'lightbox_icon_color'             => '#333333',
				'lightbox_icon_size'              => '',
				'lightbox_icon_image'             => '',
				'lightbox_icon_image_size'        => 'thumbnail',
				'lightbox_icon_mirror'            => '',

				'hover_lightbox_color'            => '',

				'readmore_icon_type'              => 'icon',
				'readmore_icon'                   => '',
				'readmore_icon_preset'            => '',
				'readmore_icon_character'         => '',
				'readmore_icon_color'             => '#333333',
				'readmore_icon_size'              => '',
				'readmore_icon_image'             => '',
				'readmore_icon_image_size'        => 'thumbnail',
				'readmore_icon_mirror'            => '',

				'hover_readmore_color'            => '',

				/* Button */
				'mpc_button__disable'                     => '',
                'mpc_button__title'                       => '',
				'mpc_button__block'                       => '',

				'mpc_button__font_preset'                 => '',
				'mpc_button__font_color'                  => '',
				'mpc_button__font_size'                   => '',
				'mpc_button__font_line_height'            => '',
				'mpc_button__font_align'                  => '',
				'mpc_button__font_transform'              => '',

				'mpc_button__padding_css'                 => '',
				'mpc_button__margin_css'                  => '',
				'mpc_button__border_css'                  => '',
				'mpc_button__background_type'             => 'color',
				'mpc_button__background_color'            => '',
				'mpc_button__background_image'            => '',
				'mpc_button__background_image_size'       => 'large',
				'mpc_button__background_repeat'           => 'no-repeat',
				'mpc_button__background_size'             => 'initial',
				'mpc_button__background_position'         => 'middle-center',
				'mpc_button__background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_button__icon_type'                   => 'icon',
				'mpc_button__icon'                        => '',
				'mpc_button__icon_character'              => '',
				'mpc_button__icon_image'                  => '',
				'mpc_button__icon_image_size'             => 'thumbnail',
				'mpc_button__icon_preset'                 => '',
				'mpc_button__icon_color'                  => '#333333',
				'mpc_button__icon_size'                   => '',

				'mpc_button__icon_effect'                 => 'none-none',
				'mpc_button__icon_gap'                    => '',

				'mpc_button__hover_border_css'            => '',

				'mpc_button__hover_font_color'            => '',
				'mpc_button__hover_icon_color'            => '',

				'mpc_button__hover_background_type'       => 'color',
				'mpc_button__hover_background_color'      => '',
				'mpc_button__hover_background_image'      => '',
				'mpc_button__hover_background_image_size' => 'large',
				'mpc_button__hover_background_repeat'     => 'no-repeat',
				'mpc_button__hover_background_size'       => 'initial',
				'mpc_button__hover_background_position'   => 'middle-center',
				'mpc_button__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'mpc_button__hover_background_effect'     => 'fade-in',
				'mpc_button__hover_background_offset'     => '',
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

		/* Reset */
		function reset() {
			$this->is_wrapped = false;
			$this->style      = '';
			$this->parts      = array();
			$this->classes    = '';
			$this->before     = '';
			$this->after      = '';

			$this->post       = null;
		}

		/* Remove Excerpt More */
		function remove_excerpt_more() {
			return '';
		}

		/* Allow to set post from wrappers */
		function set_post( WP_Post $post ) {
			$this->post = $post;
			$this->is_wrapped = true;
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

		/* Get Post Date */
		function get_date( $layout ) {
			$prefix  = '';
			$date_link = apply_filters( 'ma/single_post/permalink/date', get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) , 'permalink/date' );
			$date = '<a href="' . esc_attr( esc_url( $date_link ) ) . '">';

			if( $layout == 'style_3' || $layout == 'style_6' ) {
			   $date .= '<div class="mpc-date__days">' . get_the_time( 'd' ) . '</div><div class="mpc-date__month">' . get_the_time( 'M' ) . '</div>';
			} else if( $layout == 'style_5' ) {
				$date .= '<div class="mpc-date__days">' . get_the_time( 'd' ) . '</div><div class="mpc-date-wrap"><div class="mpc-date__month">' . get_the_time( 'M' ) . '</div><div class="mpc-date__year">' . get_the_time( 'Y' ) . '</div></div>';
			} else {
				$prefix =  __( 'on ', 'mpc' );
				$date   .= '<span class="mpc-date__inline">' . get_the_time( 'd F Y' ) . '</span>';
			}

			$date .= '</a>';

			return apply_filters( 'ma/single_post/get/date', $prefix . $date, 'get_date' );
		}

		/* Get Tax list */
		function get_taxonomies( $separator ) {
			$post_taxonomies = get_the_category_list( $separator );

			if( $post_taxonomies != '' ) {
				$post_taxonomies = __( 'in ', 'mpc') . $post_taxonomies;
			}

			return apply_filters( 'ma/single_post/get/taxonomies', $post_taxonomies, 'get_taxonomies' );
		}

		/* Get Author */
		function get_author() {
			$author_link = apply_filters( 'ma/single_post/permalink/author', get_author_posts_url( get_the_author_meta( 'ID' )  ) , 'permalink/author' );
			$author = __( 'by ', 'mpc' ) . '<a href="' . esc_attr( esc_url( $author_link ) ) . '" title="' . esc_attr( get_the_author() ) . '">' .get_the_author() . '</a>';

			return apply_filters( 'ma/single_post/get/author', $author, 'get_author' );
		}

		/* Get Comments */
		function get_comments() {
			$comments_link = apply_filters( 'ma/single_post/permalink/comments', get_comments_link() , 'permalink/comments' );
			$comments = '<a href="' . esc_attr( esc_url( $comments_link ) ) . '" title="' . esc_attr( __( 'Comments for ', 'mpc' ) . get_the_title() ) . '">' . get_comments_number_text() . '</a>';

			return apply_filters( 'ma/single_post/get/comments', $comments, 'get_comments' );
		}

		/* Get Thumbnail */
		function get_placeholder( $thumbnail_layout ) {
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$js_link   = $this->get_js_link( $permalink );

			$placeholder = '<div class="mpc-post__thumbnail mpc-image-placeholder" '. $js_link . '>' . $thumbnail_layout . '</div>';

			return $placeholder;

		}
		function get_thumbnail( $image_size ) {
			$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
			$thumbnail = wp_get_attachment_image_src( $thumbnail_id, $image_size );
			$thumbnail = isset( $thumbnail[ 0 ] ) ? $thumbnail[ 0 ] : false;

			return apply_filters( 'ma/single_post/get/thumbnail', $thumbnail, 'get_thumbnail' );
		}

		function get_js_link( $permalink ) {
			return 'data-mpc_link="' . $permalink . '"';
		}

		/* Build Thumbnail */
		function build_thumbnail( $layout, $disable, $tiles_size, $image_size, $atts ) {
			if ( $disable == '' ) {
				$thumbnail        = $this->get_thumbnail( $image_size );
				$thumbnail_layout = $this->thumbnail_layout( $layout );

				if( $thumbnail !== false ) {
					$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
					$js_link   = $this->get_js_link( $permalink );

					if( $tiles_size !== 'full' ) {
						$per_thumb_css = apply_filters( 'ma/single_post/thumb_inline_css', '', $atts, $this->post->ID );
						$thumbnail = 'data-mpc_src="' . $thumbnail . '"';
						$thumbnail .= ( $per_thumb_css !== '') ? ' style="'. $per_thumb_css .'"' : '';
						$return = '<div class="mpc-post__thumbnail" ' . $thumbnail . ' ' . $js_link . '>' . $thumbnail_layout . '</div>';
					} else {
						$thumbnail = '<img src="' . $thumbnail . '" alt="" />';
						$return = '<div class="mpc-post__thumbnail mpc-post__thumbnail-full" ' . $js_link . '>' . $thumbnail . $thumbnail_layout . '</div>';
					}
				} else {
					$return = $this->get_placeholder( $thumbnail_layout );
				}

				return apply_filters( 'ma/single_post/build/thumbnail', $return, 'build_thumbnail' );
			}

			return '';
		}

		/* Get Title */
		function get_title() {
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );

			$title = '<a href="' . esc_attr( esc_url( $permalink ) ) . '" title="' . esc_attr( get_the_title() ) . '">' . get_the_title() . '</a>';

			return apply_filters( 'ma/single_post/get/title', $title, 'get_title' );
		}

		/* Get Content/Excerpt */
		function get_content( $full = false ) {
			if( $full ) {
				$content = get_the_content();
			} else {
				$content = get_the_excerpt();
			}

			return apply_filters( 'ma/single_post/get/content', $content, 'get_content' );
		}

		/* Get Icon */
		function get_lightbox_src() {
			$thumbnail_id  = get_post_thumbnail_id( get_the_ID() );
			$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			$thumbnail     = apply_filters( 'ma/single_post/lightbox_src', $thumbnail_src[ 0 ], 'lightbox_src' );

			return $thumbnail;
		}
		function get_icon( $atts, $type = 'lightbox' ) {
			$icon = '';

			$title     = apply_filters( 'ma/single_post/title', get_the_title(), 'title' );
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );

			if( $atts[ 'content' ] != '' || $atts[ 'class' ] != '' ) {
				$icon = '<i class="mpc-post-overlay__icon mpc-type--' . esc_attr( $type . $atts[ 'class' ] ) . '">' . $atts[ 'content' ] . '</i>';
			}

			if( $type == 'lightbox' ) {
				$thumbnail = $this->get_lightbox_src();
				$lightbox = MPC_Helper::lightbox_vendor();

				$icon = '<a href="' . esc_attr( $thumbnail ) . '" title="' . esc_attr( $title ) . '" class="mpc-lightbox mpc-icon-anchor' . $lightbox . '">' . $icon . '</a>';
			} else if( $type == 'readmore' ) {
				$icon = '<a href="' . esc_attr( $permalink ) . '" title="' . esc_attr( __( 'Read more about ', 'mpc' ) . $title ) . '" class="mpc-icon-anchor">' . $icon . '</a>';
			}

			return apply_filters( 'ma/single_post/get/icon', $icon, 'get_icon' );
		}

		/* Build Meta display */
		function meta_layout( $style, $layout, $sep = ', ', $prefix = false ) {
			if( !is_array( $layout ) ) {
				return '';
			}

			$disable_date = array(
				'style_3',
				'style_5',
				'style_6',
			);

			$elements = array();
			$comments = null;

			foreach( $layout as $part => $index ) {
				if( $part == 'comments' ) {
					$comments = $this->parts[ $part ];
				} else if( $part == 'date' ) {
					if( !in_array( $style, $disable_date ) ) {
						$elements[] = $this->parts[ $part ];
					}
				} else {
					$elements[] = $this->parts[ $part ];
				}
			}

			$content = join( $sep, $elements );
			$content = $comments != '' ? join( ' | ', array( $content, $comments ) ) : $content;

			return $content;
		}

		/* Build overlay layout */
		function overlay_layout( $style ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'overlay_begin', 'vertical_begin', 'readmore_icon', 'lightbox_icon', 'section_begin', 'title', 'meta', 'section_end', 'vertical_end', 'overlay_end' ),
				'style_2' => array( 'overlay_begin', 'vertical_begin', 'lightbox_icon', 'readmore_icon', 'vertical_end', 'overlay_end' ),
				'style_3' => array( 'overlay_begin', 'vertical_begin', 'lightbox_icon', 'readmore_icon', 'vertical_end', 'overlay_end' ),
				'style_4' => array( 'overlay_begin', 'wrapper_begin', 'vertical_begin', 'readmore_icon', 'lightbox_icon', 'vertical_end', 'wrapper_end',  'section_begin', 'title', 'meta', 'section_end', 'overlay_end' ),
				'style_5' => array( 'overlay_begin', 'vertical_begin', 'lightbox_icon', 'readmore_icon', 'vertical_end', 'overlay_end' ),
				'style_6' => array( 'overlay_begin', 'section_begin', 'date', 'wrapper_begin', 'title', 'meta',  'description', 'wrapper_end', 'section_end', 'overlay_end' ),
				'style_7' => array( 'overlay_begin', 'vertical_begin', 'lightbox_icon', 'readmore_icon', 'vertical_end', 'overlay_end' ),
				'style_8' => array( 'overlay_begin', 'vertical_begin', 'lightbox_icon', 'readmore_icon', 'title', 'meta', 'vertical_end', 'overlay_end' ),
				'style_9' => array(  ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}

		/* Build thumbnail layout */
		function thumbnail_layout( $style ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'overlay' ),
				'style_2' => array( 'overlay' ),
				'style_3' => array( 'overlay' ),
				'style_5' => array( 'overlay', 'date'  ),
				'style_6' => array( 'overlay' ),
				'style_8' => array( 'overlay' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}

		/* Build shortcode layout */
		function shortcode_layout( $style ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'thumbnail', 'section_begin', 'title', 'meta', 'section_end' ),
				'style_2' => array( 'thumbnail', 'section_begin', 'title', 'description', 'readmore', 'meta', 'section_end' ),
				'style_3' => array( 'thumbnail', 'section_begin', 'date', 'wrapper_begin', 'title', 'meta', 'wrapper_end', 'description', 'readmore', 'section_end' ),
				'style_4' => array( 'thumbnail', 'section_begin', 'wrapper_begin', 'title', 'meta', 'wrapper_end', 'overlay', 'section_end' ),
				'style_5' => array( 'thumbnail', 'section_begin', 'title', 'description', 'meta', 'readmore', 'section_end' ),
				'style_6' => array( 'thumbnail', 'section_begin', 'wrapper_begin', 'date', 'wrapper_end', 'title', 'meta', 'section_end' ),
				'style_7' => array( 'thumbnail', 'section_begin', 'title', 'meta', 'overlay', 'section_end' ),
				'style_8' => array( 'thumbnail' ),
				'style_9' => array( 'section_begin', 'title', 'meta', 'description', 'readmore', 'section_end' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}

		/* Prepare Pagination Content */
		function pagination_content( $atts ) {
			global $MPC_Button;

			$atts = shortcode_atts( $this->defaults, $atts );

			/* Get Post */
			if ( ! is_object( $this->post ) && isset( $atts[ 'item_id' ] ) ) {
				$this->get_post( (int) $atts[ 'item_id' ] );
			}

			if( $this->post === null ) {
				return '';
			}

			/* Setup post data */
			global $post; $post = $this->post;
			setup_postdata( $post );

			/* Prepare */
			$meta_layout = $atts[ 'meta_layout' ] != '' ? array_flip( explode( ',', $atts[ 'meta_layout' ] ) ) : '';
			$atts_button  = MPC_Parser::shortcode( $atts, 'mpc_button_' );

			$atts_lightbox = MPC_Parser::icon( $atts, 'lightbox' );
			$atts_readmore = MPC_Parser::icon( $atts, 'readmore' );

			$atts_lightbox[ 'class' ] .= $atts_lightbox[ 'class' ] != '' && $atts[ 'lightbox_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';
			$atts_readmore[ 'class' ] .= $atts_readmore[ 'class' ] != '' && $atts[ 'readmore_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

			$classes_meta    = ' mpc-transition';
			$classes_meta    .= $atts[ 'meta_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'meta_font_preset' ] : '';
			$classes_content = $atts[ 'description_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'description_font_preset' ] : '';

			$classes_title = ' mpc-transition';
			$classes_title .= $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'title_font_preset' ] : '';
			$classes_title .= $atts[ 'title_overflow' ] != '' ? '' : ' mpc-text-overflow';

			$classes_date = in_array( $atts[ 'layout' ], array( 'style_3', 'style_5', 'style_6' ) ) ? ' mpc-date__wrapper': '';
			$classes_date .= in_array( $atts[ 'layout' ], array( 'style_3', 'style_5', 'style_6' ) ) && $atts[ 'date_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'date_font_preset' ] : '';

			/* Layout parts */
			$return = '';

			/* Overlay click */
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$js_link   = $this->get_js_link( $permalink );

			/* Layout parts */
			$this->parts[ 'section_begin' ]  = '<div class="mpc-post__content mpc-transition">';
			$this->parts[ 'section_end' ]    = '</div>';
			$this->parts[ 'overlay_begin' ]  = '<div class="mpc-post__overlay mpc-transition">';
			$this->parts[ 'overlay_end' ]    = '</div>';
			$this->parts[ 'wrapper_begin' ]  = '<div class="mpc-wrapper">';
			$this->parts[ 'wrapper_end' ]    = '</div>';
			$this->parts[ 'vertical_begin' ] = '<div class="mpc-post--vertical-wrap"><div class="mpc-post--vertical" ' . $js_link .'>';
			$this->parts[ 'vertical_end' ]   = '</div></div>';
			$atts_button[ 'title' ]          = $atts_button[ 'title' ] != '' ? $atts_button[ 'title' ] : __( 'Read More', 'mpc' );

			/* Shortcode Output */
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$atts_button[ 'url' ]   = 'url:' . urlencode( $permalink ) . '|title:' . get_the_title() . '|';

			$image_size = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'large';

			$this->parts[ 'author' ]      = isset( $meta_layout[ 'author' ] ) ? '<span class="mpc-post__author">' . $this->get_author() . '</span>' : '';
			$this->parts[ 'comments' ]    = isset( $meta_layout[ 'comments' ] ) ? '<span class="mpc-post__comments">' . $this->get_comments() . '</span>' : '';
			$this->parts[ 'date' ]        = isset( $meta_layout[ 'date' ] ) ? '<span class="mpc-post__date' . esc_attr( $classes_date ) . '">' . $this->get_date( $atts[ 'layout' ] ) . '</span>' : '';
			$this->parts[ 'taxonomies' ]  = isset( $meta_layout[ 'taxonomies' ] ) ? '<span class="mpc-post__tax">' . $this->get_taxonomies( $atts[ 'meta_tax_separator' ] ) . '</span>' : '';

			$this->parts[ 'title' ]       = $atts[ 'title_disable' ] == '' ? '<h3 class="mpc-post__heading' . esc_attr( $classes_title ) . '">' . $this->get_title() . '</h3>' : '';
			$this->parts[ 'readmore' ]    = $atts[ 'mpc_button__disable' ] == '' ? $MPC_Button->shortcode_template( $atts_button ) : '';
			$this->parts[ 'description' ] = $atts[ 'description_disable' ] == '' ? '<div class="mpc-post__description' . esc_attr( $classes_content ) . '">' . $this->get_content() . '</div>' : '';

			$this->parts[ 'lightbox_icon' ] = $atts[ 'lightbox_disable' ] == '' ? $this->get_icon( $atts_lightbox, 'lightbox' ) : '';
			$this->parts[ 'readmore_icon' ] = $atts[ 'readmore_disable' ] == '' ? $this->get_icon( $atts_readmore, 'readmore' ) : '';

			$meta = $this->meta_layout( $atts[ 'layout' ], $meta_layout, $atts[ 'meta_separator' ] );
			$this->parts[ 'meta' ]    = $meta != '' ? '<div class="mpc-post__meta' . esc_attr( $classes_meta ) . '">' . $meta . '</div>' : '';
			$this->parts[ 'overlay' ] = $atts[ 'disable_thumbnail' ] == '' ? $this->overlay_layout( $atts[ 'layout' ] ) : '';

			$this->parts[ 'thumbnail' ] = $this->build_thumbnail( $atts[ 'layout' ], $atts[ 'disable_thumbnail' ], $atts[ 'tiles_size' ], $image_size,$atts );

			$per_post_atts = apply_filters( 'ma/single_post/post_atts', '', $atts, $this->post->ID );

			$return .= '<div onclick="" class="mpc-post"' . $per_post_atts . '>';
				$return .= '<div class="mpc-post__wrapper">';
					$return .= $this->shortcode_layout( $atts[ 'layout' ] );
				$return .= '</div>';
			$return .= '</div>';

			$return = $this->before . $return . $this->after;

			$this->reset();
			wp_reset_postdata();

			return $return;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null, $shortcode = null, $parent_css = null ) {
			global $MPC_Button, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( $this->defaults, $atts );

			/* Get Post */
			if ( ! is_object( $this->post ) && isset( $atts[ 'item_id' ] ) ) {
				$this->get_post( (int) $atts[ 'item_id' ] );
			}

			if( $this->post === null ) {
				return '';
			}

			/* Setup post data */
			global $post; $post = $this->post;
			setup_postdata( $post );

			/* Prepare */
			$styles = $this->shortcode_styles( $atts, $parent_css );
			$css_id = $styles[ 'id' ];
			$css_id = !empty( $parent_css ) ? '' : ' id="' . esc_attr( $css_id ) . '"';

			if( !isset( $parent_css[ 'selector' ] ) ) {
				$parent_css = array(
					'id' => $styles[ 'id' ],
					'selector' => '.mpc-single-post[id="' . $styles[ 'id' ] . '"] .mpc-button'
				);
			} else {
				$parent_css[ 'selector' ] .= ' .mpc-button';
			}

			$meta_layout = $atts[ 'meta_layout' ] != '' ? array_flip( explode( ',', $atts[ 'meta_layout' ] ) ) : '';

			$animation    = MPC_Parser::animation( $atts );
			$atts_button  = MPC_Parser::shortcode( $atts, 'mpc_button_' );

			$atts_lightbox = MPC_Parser::icon( $atts, 'lightbox' );
			$atts_readmore = MPC_Parser::icon( $atts, 'readmore' );

			$atts_lightbox[ 'class' ] .= $atts_lightbox[ 'class' ] != '' && $atts[ 'lightbox_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';
			$atts_readmore[ 'class' ] .= $atts_readmore[ 'class' ] != '' && $atts[ 'readmore_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

			/* Shortcode classes | Animation | Layout */
			$this->classes = ' mpc-init mpc-transition'; // mpc-transition
			$this->classes .= $animation != '' ? ' mpc-animation' : '';
			$this->classes .= $atts[ 'overlay_effect' ] != '' ? ' mpc-overlay--' . $atts[ 'overlay_effect' ] : ' mpc-overlay--fade';
			$this->classes .= $atts[ 'item_effect' ] != '' ? ' mpc-item--' . $atts[ 'item_effect' ] : ' mpc-item--fade';
			$this->classes .= $atts[ 'layout' ] != '' ? ' mpc-layout--' . $atts[ 'layout' ] : '';
			$this->classes .= $atts[ 'layout' ] == 'style_1' && $atts[ 'content_over_thumb' ] == 'true' ? ' mpc-content--overlay' : '';
			$this->classes .= $atts[ 'layout' ] == 'style_7' &&  $atts[ 'content_position' ] != '' ? ' mpc-align--' . $atts[ 'content_position' ] : '';
			$this->classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$classes_meta    = ' mpc-transition';
			$classes_meta    .= $atts[ 'meta_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'meta_font_preset' ] : '';
			$classes_content = $atts[ 'description_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'description_font_preset' ] : '';

			$classes_title = ' mpc-transition';
			$classes_title .= $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'title_font_preset' ] : '';
			$classes_title .= $atts[ 'title_overflow' ] != '' ? '' : ' mpc-text-overflow';

			$classes_date = in_array( $atts[ 'layout' ], array( 'style_3', 'style_5', 'style_6' ) ) ? ' mpc-date__wrapper': '';
			$classes_date .= in_array( $atts[ 'layout' ], array( 'style_3', 'style_5', 'style_6' ) ) && $atts[ 'date_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'date_font_preset' ] : '';

			/* Overlay click */
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$js_link   = $this->get_js_link( $permalink );

			/* Layout parts */
			$this->parts[ 'section_begin' ]  = '<div class="mpc-post__content mpc-transition">';
			$this->parts[ 'section_end' ]    = '</div>';
			$this->parts[ 'overlay_begin' ]  = '<div class="mpc-post__overlay mpc-transition">';
			$this->parts[ 'overlay_end' ]    = '</div>';
			$this->parts[ 'wrapper_begin' ]  = '<div class="mpc-wrapper">';
			$this->parts[ 'wrapper_end' ]    = '</div>';
			$this->parts[ 'vertical_begin' ] = '<div class="mpc-post--vertical-wrap"><div class="mpc-post--vertical" ' . $js_link . '>';
			$this->parts[ 'vertical_end' ]   = '</div></div>';
			$atts_button[ 'title' ] = $atts_button[ 'title' ] != '' ? $atts_button[ 'title' ] : __( 'Read More', 'mpc' );

			/* Shortcode Output */
			$permalink = apply_filters( 'ma/single_post/permalink', get_the_permalink(), 'permalink' );
			$atts_button[ 'url' ]   = 'url:' . urlencode( $permalink ) . '|title:' . get_the_title() . '|';

			$image_size = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'large';

			$this->parts[ 'author' ]      = isset( $meta_layout[ 'author' ] ) ? '<span class="mpc-post__author">' . $this->get_author() . '</span>' : '';
			$this->parts[ 'comments' ]    = isset( $meta_layout[ 'comments' ] ) ? '<span class="mpc-post__comments">' . $this->get_comments() . '</span>' : '';
			$this->parts[ 'date' ]        = isset( $meta_layout[ 'date' ] ) ? '<span class="mpc-post__date' . esc_attr( $classes_date ) . '">' . $this->get_date( $atts[ 'layout' ] ) . '</span>' : '';
			$this->parts[ 'taxonomies' ]  = isset( $meta_layout[ 'taxonomies' ] ) ? '<span class="mpc-post__tax">' . $this->get_taxonomies( $atts[ 'meta_tax_separator' ] ) . '</span>' : '';

			$this->parts[ 'title' ]       = $atts[ 'title_disable' ] == '' ? '<h3 class="mpc-post__heading' . esc_attr( $classes_title ) . '">' . $this->get_title() . '</h3>' : '';
			$this->parts[ 'readmore' ]    = $atts[ 'mpc_button__disable' ] == '' ? $MPC_Button->shortcode_template( $atts_button, null, null, $parent_css ) : '';
			$this->parts[ 'description' ] = $atts[ 'description_disable' ] == '' ? '<div class="mpc-post__description' . esc_attr( $classes_content ) . '">' . $this->get_content() . '</div>' : '';

			$this->parts[ 'lightbox_icon' ] = $atts[ 'lightbox_disable' ] == '' ? $this->get_icon( $atts_lightbox, 'lightbox' ) : '';
			$this->parts[ 'readmore_icon' ] = $atts[ 'readmore_disable' ] == '' ? $this->get_icon( $atts_readmore, 'readmore' ) : '';

			$meta = $this->meta_layout( $atts[ 'layout' ], $meta_layout, $atts[ 'meta_separator' ] );
			$this->parts[ 'meta' ]    = $meta != '' ? '<div class="mpc-post__meta' . esc_attr( $classes_meta ) . '">' . $meta . '</div>' : '';
			$this->parts[ 'overlay' ] = $atts[ 'disable_thumbnail' ] == '' ? $this->overlay_layout( $atts[ 'layout' ] ) : '';

			$this->parts[ 'thumbnail' ] = $this->build_thumbnail( $atts[ 'layout' ], $atts[ 'disable_thumbnail' ], $atts[ 'tiles_size' ], $image_size,$atts );

			$per_post_atts = apply_filters( 'ma/single_post/post_atts', '', $atts, $this->post->ID );

			$return = '<div onclick="" class="mpc-post"' . $per_post_atts . '>';
				$return .= '<div class="mpc-post__wrapper">';
					$return .= $this->shortcode_layout( $atts[ 'layout' ] );
				$return .= '</div>';
			$return .= '</div>';

			/* Wrapper check */
			if( ! $this->is_wrapped ) {
				$return = '<div' . $css_id . ' class="mpc-single-post' . esc_attr( $this->classes ) .'">' . $return . '</div>';
				$this->reset();
			} else {
				$this->post = null;
			}

			$return = $this->before . $return . $this->after;

			wp_reset_postdata();

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $parent_css ) {
			global $mpc_massive_styles;
			$css_id       = uniqid( 'mpc_post-' . rand( 1, 100 ) );
			$css_selector = '.mpc-single-post[id="' . $css_id . '"]';
			$style        = '';

			if ( is_array( $parent_css ) ) {
				$css_id       = $parent_css[ 'id' ];
				$css_selector = $parent_css[ 'selector' ];
			}

			if( $this->style === $css_id ) {
				return array(
					'id'  => $css_id,
					'css' => '',
				);
			}

			// Add 'px'
			$styles[ 'thumb_height' ] = $styles[ 'thumb_height' ] != '' ? $styles[ 'thumb_height' ] . ( is_numeric( $styles[ 'thumb_height' ] ) ? 'px' : '' ) : '';
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'meta_font_size' ] = $styles[ 'meta_font_size' ] != '' ? $styles[ 'meta_font_size' ] . ( is_numeric( $styles[ 'meta_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'date_font_size' ] = $styles[ 'date_font_size' ] != '' ? $styles[ 'date_font_size' ] . ( is_numeric( $styles[ 'date_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'description_font_size' ] = $styles[ 'description_font_size' ] != '' ? $styles[ 'description_font_size' ] . ( is_numeric( $styles[ 'description_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'lightbox_icon_size' ] = $styles[ 'lightbox_icon_size' ] != '' ? $styles[ 'lightbox_icon_size' ] . ( is_numeric( $styles[ 'lightbox_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'readmore_icon_size' ] = $styles[ 'readmore_icon_size' ] != '' ? $styles[ 'readmore_icon_size' ] . ( is_numeric( $styles[ 'readmore_icon_size' ] ) ? 'px' : '' ) : '';

			// Thumb Height
			if( $styles[ 'thumb_height' ] && $styles[ 'tiles_size' ] !== 'full' ) {
				$style .= $css_selector . ' .mpc-post__thumbnail {';
					$style .=  'height: ' . $styles[ 'thumb_height' ] . ';';
				$style .= '}';
			}

			// Regular
			if ( $temp_style = MPC_CSS::background( $styles ) ) {
				$style .= $css_selector . ' .mpc-post__content {';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] && in_array( $styles[ 'layout' ], array( 'style_7' ) ) ) { $inner_styles[] = $styles[ 'margin_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__content {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'date_padding_css' ] ) { $inner_styles[] = $styles[ 'date_padding_css' ]; }
			if ( $styles[ 'date_margin_css' ] ) { $inner_styles[] = $styles[ 'date_margin_css' ]; }
			if ( $styles[ 'date_border_css' ] ) { $inner_styles[] = $styles[ 'date_border_css' ]; }
			if ( $styles[ 'date_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'date_background' ] . ';'; }
			if ( $temp_style = MPC_CSS::font( $styles, 'date' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 && in_array( $styles[ 'layout' ], array( 'style_3', 'style_5', 'style_6' ) ) ) {
				$style .= $css_selector . ' .mpc-date__wrapper {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'border_css' ] ) {
				$style .= $css_selector . ' .mpc-post__wrapper {';
					$style .= $styles[ 'border_css' ];
				$style .= '}';
			}

			// Typography
			$inner_styles = array();
			if( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__heading {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'hover_title_color' ] ) {
				$style .= $css_selector . ' .mpc-post__heading a:hover {';
					$style .= 'color: ' . $styles[ 'hover_title_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'description_padding_css' ] ) { $inner_styles[] = $styles[ 'description_padding_css' ]; }
			if ( $styles[ 'description_margin_css' ] ) { $inner_styles[] = $styles[ 'description_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'description' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__description {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'meta_margin_css' ] ) { $inner_styles[] = $styles[ 'meta_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'meta' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__meta {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'meta_link_color' ] ) {
				$style .= $css_selector . ' .mpc-post__meta a {';
					$style .= 'color: ' . $styles[ 'meta_link_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_meta_link_color' ] ) {
				$style .= $css_selector . ' .mpc-post__meta a:hover {';
					$style .= 'color: ' . $styles[ 'hover_meta_link_color' ] . ';';
				$style .= '}';
			}

			// Overlay
			if ( $styles[ 'overlay_color' ] ) {
				$style .= $css_selector . ' .mpc-post__overlay {';
					$style .= 'background: ' . $styles[ 'overlay_color' ] . ';';
				$style .= '}';

				if( $styles[ 'layout' ] == 'style_6' && $styles[ 'disable_vinette' ] == '' ) {
					$style .= $css_selector . ' .mpc-post__overlay .mpc-post__content:after {';
					if( strpos( $styles[ 'overlay_color' ], 'rgba' ) !== false ) {
						$main_color = explode( ',', $styles[ 'overlay_color' ] );
						array_pop( $main_color );
						$main_color = join( ',', $main_color );
						$style .= 'background: linear-gradient(to bottom, ' . $main_color . ',0) 0%, ' . $main_color . ',1) 80%);';
					} else {
						$main_color = 'rgba(' . join( ',', vc_hex2rgb( $styles[ 'overlay_color' ] ) );
						$style .= 'background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, ' . $main_color . ',1) 80%);';
					}
					$style .= '}';
				}
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_title_color' ] ) { $inner_styles[] = 'color: ' . $styles[ 'overlay_title_color' ] . ';'; }
			if ( $styles[ 'overlay_title_align' ] ) { $inner_styles[] = 'text-align: ' . $styles[ 'overlay_title_align' ] . ';'; }
			if ( $styles[ 'overlay_title_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_title_margin_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__heading {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if( $styles[ 'overlay_padding_css' ] && $styles[ 'layout' ] == 'style_6' ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__content {';
					$style .= $styles[ 'overlay_padding_css' ];
				$style .= '}';
			}

			if( $styles[ 'overlay_hover_title_color' ] ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__heading a:hover {';
					$style .= 'color: ' . $styles[ 'overlay_hover_title_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_meta_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_meta_margin_css' ]; }
			if ( $styles[ 'overlay_meta_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'overlay_meta_color' ] . ';'; }
			if ( $styles[ 'overlay_meta_align' ] ) { $inner_styles[] = 'text-align:' . $styles[ 'overlay_meta_align' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__meta {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if( $styles[ 'overlay_meta_link_color' ] ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__meta a {';
					$style .= 'color: ' . $styles[ 'overlay_meta_link_color' ] . ';';
				$style .= '}';
			}

			if( $styles[ 'overlay_hover_meta_color' ] ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__meta a:hover {';
					$style .= 'color: ' . $styles[ 'overlay_hover_meta_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_date_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_date_padding_css' ]; }
			if ( $styles[ 'overlay_date_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_date_margin_css' ]; }
			if ( $styles[ 'overlay_date_border' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'overlay_date_border' ] . ';'; }
			if ( $styles[ 'overlay_date_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'overlay_date_background' ] . ';'; }
			if ( $styles[ 'overlay_date_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'overlay_date_color' ] . ';'; }

			if ( count( $inner_styles ) > 0 && in_array( $styles[ 'layout' ], array( 'style_6' ) ) ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-date__wrapper {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_description_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_description_padding_css' ]; }
			if ( $styles[ 'overlay_description_margin_css' ] ) {  $inner_styles[] = $styles[ 'overlay_description_margin_css' ]; }
			if ( $styles[ 'overlay_description_color' ] ) {  $inner_styles[] = 'color:' . $styles[ 'overlay_description_color' ] . ';'; }
			if ( $styles[ 'overlay_description_align' ] ) {  $inner_styles[] = 'color:' . $styles[ 'overlay_description_align' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-post__description {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'overlay_icons_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_icons_padding_css' ]; }
			if ( $styles[ 'overlay_icons_margin_css' ] ) {  $inner_styles[] = $styles[ 'overlay_icons_margin_css' ]; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-post__overlay .mpc-icon-anchor {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'lightbox' ) ) {
				$style .= $css_selector . ' .mpc-type--lightbox {';
					$style .= $temp_style;
				$style .= '}';
			}

			if( $styles[ 'hover_lightbox_color' ] ) {
				$style .= $css_selector . ' .mpc-type--lightbox:hover {';
					$style .= 'color: ' . $styles[ 'hover_lightbox_color' ] . ';';
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'readmore' ) ) {
				$style .= $css_selector . ' .mpc-type--readmore {';
					$style .= $temp_style;
				$style .= '}';
			}

			if( $styles[ 'hover_readmore_color' ] ) {
				$style .= $css_selector . ' .mpc-type--readmore:hover {';
					$style .= 'color: ' . $styles[ 'hover_readmore_color' ] . ';';
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
				return '';
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Item Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
					'group'       => __( 'Post', 'mpc' ),
				),
				array(
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'param_name'       => 'layout',
					'value'            => '',
					'columns'          => '3',
					'shortcode'        => $this->shortcode,
					'layouts'          => array(
							'style_1' => '4',
							'style_2' => '6',
							'style_3' => '6',
							'style_4' => '4',
							'style_5' => '6',
							'style_6' => '4',
							'style_7' => '3',
							'style_8' => '3',
							'style_9' => '4',
					),
					'std'              => 'style_1',
					'group'            => __( 'Post', 'mpc' ),
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$base_ext = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Thumbnail & Overlay', 'mpc' ),
					'param_name'       => 'disable_thumbnail',
					'tooltip'          => __( 'Check to disable thumbnail and its overlay in selected layout.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_2', 'style_3', 'style_5' ) ),
					'group'            => __( 'Post', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Content In Overlay', 'mpc' ),
					'param_name'       => 'content_over_thumb',
					'tooltip'          => __( 'Check to display post content inside overlay.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => 'style_1' ),
					'group'            => __( 'Post', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Content Position', 'mpc' ),
					'param_name'       => 'content_position',
					'tooltip'          => __( 'Select position of post content on thumbnail.', 'mpc' ),
					'value'            => array(
						__( 'Top Left', 'mpc' )     => 'top-left',
						__( 'Top Right', 'mpc' )    => 'top-right',
						__( 'Bottom Left', 'mpc' )  => 'bottom-left',
						__( 'Bottom Right', 'mpc' ) => 'bottom-right',
					),
					'std'              => 'bottom-left',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => 'style_7' ),
					'group'            => __( 'Post', 'mpc' ),
				),);

			$source = array(
				array(
					'type'             => 'autocomplete',
					'heading'          => __( 'Post', 'mpc' ),
					'param_name'       => 'item_id',
					'tooltip'          => __( 'Define post to display. After you type few characters you can choose desired post from drop down.', 'mpc' ),
					'settings'         => array(
						'multiple'      => false,
						'sortable'      => false,
						'unique_values' => true,
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-no-wrap',
					'group'            => __( 'Post', 'mpc' ),
				),
			);

			$tiles_size = array(
				__( 'Fixed Height', 'mpc' ) => 'fixed',
				__( 'Masonry', 'mpc' ) => 'full',
			);
			if( current_theme_supports( 'massive-addons' ) ) {
				$tiles_size[ __( 'Tiles Size from post options', 'mpc' ) ] = 'sizes';
			}

			$item = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Item', 'mpc' ),
					'param_name'       => 'items_section_divider',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-post-effect mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_9' ) ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Item/Date Out Effect', 'mpc' ),
					'param_name'       => 'item_effect',
					'tooltip'          => __( 'Select exit animation effect for post content or date box (style 5) after hover.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )            => 'fade',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-post-effect mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_1', 'style_4', 'style_5', 'style_6', 'style_7' ) ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Tiles size', 'mpc' ),
					'param_name'       => 'tiles_size',
					'tooltip'          => __( 'Select the size of image.', 'mpc' ),
					'value'            => $tiles_size,
					'std'              => 'fixed',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_9' ) ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Thumbnail Height', 'mpc' ),
					'param_name'       => 'thumb_height',
					'tooltip'          => __( 'Specify height of post thumbnail.', 'mpc' ),
					'min'              => 0,
					'max'              => 1000,
					'step'             => 1,
					'value'            => 300,
					'unit'             => 'px',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-post-effect mpc-advanced-field',
					'dependency'       => array( 'element' => 'tiles_size', 'value_not_equal_to' => array( 'full' ) ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Image Quality', 'mpc' ),
					'param_name'       => 'image_size',
					'tooltip'          => __( 'Define image quality by it\'s size. You can use default WordPress sizes (<em>thumbnail</em>, <em>medium</em>, <em>large</em>, <em>full</em>) or pass exact size by width and height in this format: 100x200.', 'mpc' ),
					'value'            => 'large',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => false,
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-input--large',
				),
			);

			/* Title Section */
			$title = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Title', 'mpc' ),
					'param_name'       => 'title_section_divider',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-post-effect mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Title', 'mpc' ),
					'param_name'       => 'title_disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to disable title display.', 'mpc' ),
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Title Overflow', 'mpc' ),
					'param_name'       => 'title_overflow',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to enable multiple lines display for long title. Uncheck to trim title to available space.', 'mpc' ),
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'title_disable', 'value_not_equal_to' => 'true' ),
				),
			);
			$title_hover_color = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'hover_title_color',
					'value'            => '',
					'tooltip'          => __( 'Specify hover color for title.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Item', 'mpc' ),
					'dependency'       => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_4', 'style_7', 'style_8' ) ),
				),
			);

			/* Meta Section */
			$meta = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Meta', 'mpc' ),
					'param_name'       => 'meta_section_divider',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_list',
					'heading'          => __( 'Meta layout', 'mpc' ),
					'param_name'       => 'meta_layout',
					'description'      => __( 'Enable meta elements and place them in desired order. Click and drag element to change its order.', 'mpc' ),
					'value'            => 'date,taxonomies',
					'options'          => array(
						'date'       => __( 'Date', 'mpc' ),
						'taxonomies' => __( 'Taxonomies', 'mpc' ),
						'author'     => __( 'Author', 'mpc' ),
						'comments'   => __( 'Comments', 'mpc' ),
					),
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);
			$meta_link_color = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Link Color', 'mpc' ),
					'param_name'       => 'meta_link_color',
					'value'            => '',
					'tooltip'          => __( 'Specify color of links inside meta (for example links to taxonomies, archives).', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Item', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Link Color', 'mpc' ),
					'param_name'       => 'hover_meta_link_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'tooltip'          => __( 'Specify color of hovered links inside meta.', 'mpc' ),
					'group'            => __( 'Item', 'mpc' ),
                    'dependency'       => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_4', 'style_7', 'style_8' ) ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Separator', 'mpc' ),
					'param_name'       => 'meta_separator',
					'value'            => ', ',
					'tooltip'          => __( 'Specify a character to separate each of meta elements.', 'mpc' ),
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-clear--both',
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Taxonomies Separator', 'mpc' ),
					'param_name'       => 'meta_tax_separator',
					'value'            => ', ',
					'tooltip'          => __( 'Specify a character to separate each of taxonomies.', 'mpc' ),
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
			);
			$date_background = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Date Background Color', 'mpc' ),
					'param_name'       => 'date_background',
					'value'            => '',
					'tooltip'          => __( 'Select a background color for date box.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker',
					'group'            => __( 'Item', 'mpc' ),
                    'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_3', 'style_5', 'style_6' ) ),
				),
			);

			/* Description Section */
			$description = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Excerpt', 'mpc' ),
					'param_name'       => 'description_section_divider',
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Excerpt', 'mpc' ),
					'param_name'       => 'description_disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to disable excerpt display.', 'mpc' ),
					'group'            => __( 'Item', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			/* Overlay */
			$overlay = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover State', 'mpc' ),
					'tooltip'    => __( 'Specify settings for hover state of post. Some settings depends on selected layout and disabled elements at Item tab.', 'mpc' ),
					'param_name' => 'overlay_section_divider',
					'group'      => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Overlay in Effect', 'mpc' ),
					'param_name'       => 'overlay_effect',
					'value'            => array(
						__( 'Fade', 'mpc' )            => 'fade',
						__( 'Slide To Top', 'mpc' )    => 'slide-up',
						__( 'Slide To Bottom', 'mpc' ) => 'slide-down',
						__( 'Slide To Left', 'mpc' )   => 'slide-left',
						__( 'Slide To Right', 'mpc' )  => 'slide-right',
					),
					'std'              => 'fade',
					'tooltip'          => __( 'Select enter animation for hover state of post.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-post-effect mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background Color', 'mpc' ),
					'param_name'       => 'overlay_color',
					'value'            => 'rgba(255,255,255,0.5)',
					'tooltip'          => __( 'Select background color for hover state.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Vinette', 'mpc' ),
					'param_name'       => 'disable_vinette',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to disable vinette.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_6' ) ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
			);

			/* Item */
			$item_background = MPC_Snippets::vc_background( array( 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$item_border  = MPC_Snippets::vc_border( array( 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$item_padding = MPC_Snippets::vc_padding( array( 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$item_margin  = MPC_Snippets::vc_margin( array( 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Item', 'mpc' ), 'dependency' => array( 'element' => 'layout', 'value' => array( 'style_7' ) ) ) );

			/* Elements */
			$title_font = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Item', 'mpc' ), 'dependency' => array( 'element' => 'title_disable', 'value_not_equal_to' => 'true' ) ) );
			$title_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );

			/* Date */
			$date_font = MPC_Snippets::vc_font( array( 'prefix' => 'date', 'subtitle' => __( 'Date', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$date_border = MPC_Snippets::vc_border( array( 'prefix' => 'date', 'subtitle' => __( 'Date', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$date_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'date', 'subtitle' => __( 'Date', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$date_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'date', 'subtitle' => __( 'Date', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );

			/* Meta */
			$meta_font = MPC_Snippets::vc_font( array( 'prefix' => 'meta', 'subtitle' => __( 'Meta', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$meta_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'meta', 'subtitle' => __( 'Meta', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );

			/* Description */
			$description_font = MPC_Snippets::vc_font( array( 'prefix' => 'description', 'subtitle' => __( 'Excerpt', 'mpc' ), 'group' => __( 'Item', 'mpc' ), 'dependency' => array( 'element' => 'description_disable', 'value_not_equal_to' => 'true' ) ) );
			$description_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'description', 'subtitle' => __( 'Excerpt', 'mpc' ), 'group' => __( 'Item', 'mpc' ), 'dependency' => array( 'element' => 'description_disable', 'value_not_equal_to' => 'true' ) ) );
			$description_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'description', 'subtitle' => __( 'Excerpt', 'mpc' ), 'group' => __( 'Item', 'mpc' ), 'dependency' => array( 'element' => 'description_disable', 'value_not_equal_to' => 'true' ) ) );

			/* Overlay Icons */
			/* Lightbox Section */
			$lightbox = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Lightbox Icon', 'mpc' ),
					'param_name'       => 'lightbox_section_divider',
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'layout',
						'value_not_equal_to' => array( 'style_6' )
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Lightbox', 'mpc' ),
					'param_name'       => 'lightbox_disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to disable lightbox icon at thumbnail overlay.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'layout',
						'value_not_equal_to' => array( 'style_6' )
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Icon Horizontal Mirror', 'mpc' ),
					'param_name'       => 'lightbox_icon_mirror',
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to enable horizontal flip for icon.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'lightbox_disable',
						'value_not_equal_to' => 'true'
					),
				),
			);
			$lightbox_hover = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'hover_lightbox_color',
					'value'            => '',
					'tooltip'          => __( 'Select hover color for lightbox icon.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
					'dependency'       => array(
						'element'            => 'lightbox_icon_type',
						'value_not_equal_to' => 'image'
					),
				),
			);

			/* Read More Section (Overlay) */
			$readmore = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Read More Icon', 'mpc' ),
					'param_name'       => 'readmore_section_divider',
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'layout',
						'value_not_equal_to' => array( 'style_6' )
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Read More', 'mpc' ),
					'param_name'       => 'readmore_disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to disable read more icon at thumbnail overlay.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'layout',
						'value_not_equal_to' => array( 'style_6' )
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Icon Horizontal Mirror', 'mpc' ),
					'param_name'       => 'readmore_icon_mirror',
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to enable horizontal flip for icon.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'readmore_disable',
						'value_not_equal_to' => 'true'
					),
				),
			);
			$readmore_hover = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'hover_readmore_color',
					'value'            => '',
					'tooltip'          => __( 'Select hover color for readmore icon.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
					'dependency'       => array(
						'element'            => 'readmore_icon_type',
						'value_not_equal_to' => 'image'
					),
				),
			);
			$readmore_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'readmore', 'subtitle' => __( 'Read More', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ), 'dependency' => array( 'element' => 'readmore_disable', 'value_not_equal_to' => 'true' ) ) );
			$lightbox_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'lightbox', 'subtitle' => __( 'Lightbox', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ), 'dependency' => array( 'element' => 'lightbox_disable', 'value_not_equal_to' => 'true' ) ) );

			$overlay_icons_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'overlay_icons', 'subtitle' => __( 'Icons', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ), 'dependency' => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_6' ) ) ) );
			$overlay_icons_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'overlay_icons', 'subtitle' => __( 'Icons', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ), 'dependency' => array( 'element' => 'layout', 'value_not_equal_to' => array( 'style_6' ) ) ) );

			/* Overlay Title */
			$overlay_title_colors = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Title - Typography', 'mpc' ),
					'tooltip'    => __( 'Specify title typography for Hover State.', 'mpc' ),
					'param_name' => 'overlay_title_section_divider',
					'group'      => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'overlay_title_align',
					'value'            => array(
						''                     => '',
						__( 'Left', 'mpc' )    => 'left',
						__( 'Right', 'mpc' )   => 'right',
						__( 'Center', 'mpc' )  => 'center',
						__( 'Justify', 'mpc' ) => 'justify',
						__( 'Default', 'mpc' ) => 'inherit',
					),
					'std'              => '',
					'tooltip'          => __( 'Select text align for title at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'overlay_title_color',
					'value'            => '',
					'tooltip'          => __( 'Select color for title at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'overlay_hover_title_color',
					'value'            => '',
					'tooltip'          => __( 'Select hover color for title at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
			);
			$overlay_title_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'overlay_title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );

			/* Overlay Meta */
			$overlay_meta_colors = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Meta - Typography', 'mpc' ),
					'tooltip'    => __( 'Specify meta typography for Hover State.', 'mpc' ),
					'param_name' => 'overlay_meta_section_divider',
					'group'      => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'overlay_meta_align',
					'value'            => array(
						''                     => '',
						__( 'Left', 'mpc' )    => 'left',
						__( 'Right', 'mpc' )   => 'right',
						__( 'Center', 'mpc' )  => 'center',
						__( 'Justify', 'mpc' ) => 'justify',
						__( 'Default', 'mpc' ) => 'inherit',
					),
					'std'              => '',
					'tooltip'          => __( 'Select text align for meta at post overlay.', 'mpc' ),
					'group'            => __( 'Hover State', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'overlay_meta_color',
					'value'            => '',
					'tooltip'          => __( 'Select text color for meta at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Link Color', 'mpc' ),
					'param_name'       => 'overlay_meta_link_color',
					'value'            => '',
					'tooltip'          => __( 'Select link color for meta at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Color', 'mpc' ),
					'param_name'       => 'overlay_hover_meta_color',
					'value'            => '',
					'tooltip'          => __( 'Select hover link color for meta at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
			);
			$overlay_meta_margin  = MPC_Snippets::vc_margin( array( 'prefix' => 'overlay_meta', 'subtitle' => __( 'Meta', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );

			/* Overlay Date - style 6 only */
			$overlay_date_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'overlay_date', 'subtitle' => __( 'Date', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );
			$overlay_date_margin  = MPC_Snippets::vc_margin( array( 'prefix' => 'overlay_date', 'subtitle' => __( 'Date', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );
			$overlay_date_font = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Date', 'mpc' ),
					'tooltip'   => __( 'Specify date box typography for Hover State.', 'mpc' ),
					'param_name' => 'overlay_date_section_divider',
					'group'      => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'overlay_date_color',
					'value'            => '',
					'tooltip'          => __( 'Select color for date box at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border Color', 'mpc' ),
					'param_name'       => 'overlay_date_border',
					'value'            => '',
					'tooltip'          => __( 'Select border color for date box at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background Color', 'mpc' ),
					'param_name'       => 'overlay_date_background',
					'value'            => '',
					'tooltip'          => __( 'Select background color for meta at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
			);

			/* Overlay Description */
			$overlay_description_colors = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Excerpt - Typography', 'mpc' ),
					'tooltip'    => __( 'Specify excerpt typography for Hover State.', 'mpc' ),
					'param_name' => 'overlay_description_section_divider',
					'group'      => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'overlay_description_align',
					'value'            => array(
						''                     => '',
						__( 'Left', 'mpc' )    => 'left',
						__( 'Right', 'mpc' )   => 'right',
						__( 'Center', 'mpc' )  => 'center',
						__( 'Justify', 'mpc' ) => 'justify',
						__( 'Default', 'mpc' ) => 'inherit',
					),
					'std'              => '',
					'tooltip'          => __( 'Select text align for excerpt at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'group'            => __( 'Hover State', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'overlay_description_color',
					'value'            => '',
					'tooltip'          => __( 'Select color for excerpt at post overlay.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Hover State', 'mpc' ),
				),
			);
			$overlay_description_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'overlay_description', 'subtitle' => __( 'Excerpt', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );
			$overlay_description_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'overlay_description', 'subtitle' => __( 'Excerpt', 'mpc' ), 'group' => __( 'Hover State', 'mpc' ) ) );

			/* General */
			$animation = MPC_Snippets::vc_animation_basic();

			/* Integrate Button */
			$button_exclude   = array( 'exclude_regex' => '/animation_(.*)|tooltip(.*)|url/', );
			$integrate_button = vc_map_integrate_shortcode( 'mpc_button', 'mpc_button__', __( 'Read More', 'mpc' ), $button_exclude );
			$disable_button   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Read More button', 'mpc' ),
					'param_name'       => 'mpc_button__disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'tooltip'          => __( 'Check to disable Read More button displayed after expert section.', 'mpc' ),
					'group'            => __( 'Read More', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_button = array_merge( $disable_button, $integrate_button );

			$class = MPC_Snippets::vc_class( array( 'group' => __( 'Post', 'mpc' ) ) );

			$params = array_merge(
				$base,
				$source,
				$base_ext,

				$item,

				$title,
				$title_font,
				$title_hover_color,
				$title_margin,

				$meta,
				$meta_font,
				$meta_link_color,
				$meta_margin,

				$date_font,
				$date_background,
				$date_border,
				$date_padding,
				$date_margin,

				$description,
				$description_font,
				$description_padding,
				$description_margin,

				$item_background,
				$item_border,
				$item_padding,
				$item_margin,

				$overlay,
				$overlay_title_colors,
				$overlay_title_margin,
				$overlay_meta_colors,
				$overlay_meta_margin,
				$overlay_date_font,
				$overlay_date_padding,
				$overlay_date_margin,
				$overlay_description_colors,
				$overlay_description_padding,
				$overlay_description_margin,
				$lightbox,
				$lightbox_icon,
				$lightbox_hover,
				$readmore,
				$readmore_icon,
				$readmore_hover,
				$overlay_icons_padding,
				$overlay_icons_margin,

				$integrate_button,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Single Post', 'mpc' ),
				'description' => __( 'Customizable post with many styles', 'mpc' ),
				'base'        => 'mpc_single_post',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-single-display.png',
				'icon'        => 'mpc-shicon-single-post',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Single_Post' ) ) {
	global $MPC_Single_Post;
	$MPC_Single_Post = new MPC_Single_Post;
}
