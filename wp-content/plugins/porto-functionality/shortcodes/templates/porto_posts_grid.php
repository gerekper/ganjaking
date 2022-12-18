<?php

if ( ! defined( 'PORTO_VERSION' ) ) {
	return;
}

$default_atts = array(
	'shortcode_type'     => '',
	'builder_id'         => '',
	'list_builder_id'    => '',
	'source'             => '',
	'post_type'          => '',
	'product_status'     => '',
	'post_tax'           => '',
	'post_terms'         => '',
	'tax'                => '',
	'terms'              => '',
	'count'              => '',
	'hide_empty'         => '',
	'orderby'            => '',
	'orderby_term'       => '',
	'order'              => '',

	'view'               => '',
	'grid_layout'        => '1',
	'grid_height'        => 600,
	'spacing'            => '',
	'columns'            => 4,
	'columns_tablet'     => '',
	'columns_mobile'     => '',
	'list_col'           => 1,
	'pagination_style'   => '',
	'category_filter'    => '',
	'filter_cat_tax'     => '',
	'image_size'         => '',

	'navigation'         => 1,
	'nav_pos'            => '',
	'nav_pos2'           => '',
	'nav_type'           => '',
	'show_nav_hover'     => false,
	'pagination'         => 0,
	'dots_pos'           => '',
	'dots_style'         => '',
	'autoplay'           => '',
	'autoplay_timeout'   => 5000,
	'stage_padding'      => '',

	'animation_type'     => '',
	'animation_duration' => 1000,
	'animation_delay'    => 0,
	'el_class'           => '',
	'posts_wrap_cls'     => '',

	'related_post'       => '',
	// Linked Produc Widget - related, up-sell, cross-sell
	'linked_product'     => '',

	'post_found_nothing' => '',
);
extract( // @codingStandardsIgnoreLine
	shortcode_atts(
		$default_atts,
		$atts
	)
);

global $porto_settings;

if ( is_array( $columns_mobile ) && isset( $columns_mobile['size'] ) ) {
	$columns_mobile = $columns_mobile['size'];
}
if ( empty( $columns_mobile ) ) {
	$columns_mobile = 1;
	if ( ( 'product' == $post_type || 'shop' == $shortcode_type ) && ! empty( $porto_settings['shop-product-cols-mobile'] ) ) {
		$columns_mobile = (int) $porto_settings['shop-product-cols-mobile'];
	}
} else {
	$columns_mobile = (int) $columns_mobile;
}

if ( 'shop' == $shortcode_type ) {
	$post_type     = 'product';
	$other_col_cls = '';
	if ( isset( $_COOKIE['gridcookie'] ) && 'list' == $_COOKIE['gridcookie'] && ! empty( $list_builder_id ) ) {
		$builder_id_backup = $builder_id;
		$builder_id        = (int) $list_builder_id;
		if ( empty( $list_col ) ) {
			$list_col = 1;
		}

		$other_col_cls  = porto_grid_column_class( $columns, $columns_mobile, $columns_tablet );
		$view           = 'grid';
		$columns        = $list_col;
		$columns_tablet = '';
		$columns_mobile = 1;
		$list_layout    = true;
	} elseif ( $list_builder_id ) {
		$other_col_cls = porto_grid_column_class( $list_col ? (int) $list_col : 1, 1 );
	}

	if ( ! empty( $builder_id_backup ) ) {
		$list_builder_id = $builder_id_backup;
	}
}
if ( 'shop' == $shortcode_type || 'archive' == $shortcode_type ) {
	if ( empty( $pagination_style ) ) {
		$pagination_style = '1';
	} elseif ( 'none' == $pagination_style ) {
		$pagination_style = '';
	}
}

$builder_post = false;
if ( $builder_id ) {
	$builder_post = get_post( (int) $builder_id );
}

if ( ! isset( $porto_settings['rendered_builders'] ) ) {
	$porto_settings['rendered_builders'] = array();
}

if ( 'masonry' == $view || 'creative' == $view ) {
	wp_enqueue_script( 'isotope' );
}

if ( is_array( $spacing ) && isset( $spacing['size'] ) ) {
	$spacing = $spacing['size'];
}
$posts            = array();
$skeleton_enabled = $shortcode_type && apply_filters( 'porto_skeleton_lazyload', ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( $shortcode_type, $porto_settings['show-skeleton-screen'] ) && ! porto_is_ajax(), $shortcode_type );
if ( $skeleton_enabled && function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	$skeleton_enabled = false;
}

if ( 'creative' == $view && $grid_layout && ! $count && '0' != $count ) {
	$grid_layout_arr = porto_creative_grid_layout( $grid_layout );
	if ( ! empty( $grid_layout_arr ) ) {
		$count = count( $grid_layout_arr );
	}
}
if ( empty( $shortcode_type ) || ( ! empty( $atts['shortcode'] ) && 'archive' == $shortcode_type ) ) {
	if ( 'terms' == $source ) { // terms
		if ( $tax ) {
			$args = array(
				'taxonomy'   => sanitize_text_field( $tax ),
				'hide_empty' => empty( $hide_empty ) ? false : true,
			);
			if ( $count || '0' == $count ) {
				$args['number'] = (int) $count;
			}
			if ( $orderby_term ) {
				$args['orderby'] = sanitize_text_field( $orderby_term );
			}
			if ( $order ) {
				$args['order'] = sanitize_text_field( $order );
			}
			if ( ! empty( $terms ) ) {
				if ( ! is_array( $terms ) ) {
					$terms = explode( ',', $terms );
				}
				$args['orderby'] = 'include';
				$args['include'] = array_map( 'absint', $terms );
			}
			$posts = get_terms( $args );
		}
	} elseif ( empty( $source ) ) { // posts
		$args = array(
			'post_status' => 'publish',
		);

		if ( $post_type ) {
			$args['post_type'] = sanitize_text_field( $post_type );
		}

		if ( $pagination_style ) {
			if ( is_front_page() ) {
				$paged = get_query_var( 'page' );
			} else {
				$paged = get_query_var( 'paged' );
			}
			if ( $paged ) {
				$args['paged'] = (int) $paged;
			}
		}
		if ( $count || '0' == $count ) {
			$args['posts_per_page'] = (int) $count;
		}

		if ( ! $post_tax && ! empty( $post_terms ) ) {
			if ( ! is_array( $post_terms ) ) {
				$post_terms = explode( ',', $post_terms );
			}
			$term = get_term( trim( $post_terms[0] ) );
			if ( $term && ! is_wp_error( $term ) ) {
				$post_tax = $term->taxonomy;
			} else {
				$post_terms = '';
			}
		}
		if ( ! empty( $atts['cats'] ) ) {
			$post_terms = $atts['cats'];
		}
		if ( ! empty( $post_terms ) ) {
			if ( ! is_array( $post_terms ) ) {
				$post_terms = explode( ',', $post_terms );
			}
			$tax_name = $post_tax;
			if ( ! empty( $atts['cats'] ) ) {
				if ( in_array( $post_type, apply_filters( 'available_posts_grid_post_types', porto_supported_post_types() ) ) ) {
					$tax_name   = $atts['cats'];
					$taxonomies = get_object_taxonomies( $post_type );
					if ( ( ! empty( $taxonomies ) && ! in_array( $tax_name, $taxonomies ) ) ) {
						if ( 'product' == $post_type ) {
							$tax_name = 'product_cat';
						} else {
							$tax_name = $taxonomies[0];
						}
					}
				}
			}
			if ( $tax_name ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => sanitize_text_field( $tax_name ),
						'field'    => is_numeric( $post_terms[0] ) ? 'term_id' : 'slug',
						'terms'    => array_map( 'sanitize_text_field', $post_terms ),
					),
				);
			}
		}

		// update orderby and order for products
		if ( 'product' == $post_type && class_exists( 'WooCommerce' ) ) {
			$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );
			$orderby       = $ordering_args['orderby'];
			$order         = $ordering_args['order'];

			if ( 'viewed' == $product_status ) {
				$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array(); // @codingStandardsIgnoreLine
				$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
				if ( empty( $viewed_products ) ) {
					return;
				}
				if ( is_array( $viewed_products ) ) {
					$atts['ids'] = implode( ',', $viewed_products );
				}
			} elseif ( 'on_sale' == $product_status ) {
				$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			} elseif ( 'featured' == $product_status ) {
				if ( ! isset( $args['tax_query'] ) ) {
					$args['tax_query'] = array();
				}
				$args['tax_query'] = array_merge( $args['tax_query'], WC()->query->get_tax_query() ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

				$args['tax_query'][] = array(
					'taxonomy'         => 'product_visibility',
					'terms'            => 'featured',
					'field'            => 'name',
					'operator'         => 'IN',
					'include_children' => false,
				);
			} elseif ( 'pre_order' == $product_status ) {
				if ( ! isset( $args['meta_query'] ) ) {
					$args['meta_query'] = array();
				}
				$args['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key'   => '_porto_pre_order',
						'value' => 'yes',
					),
					array(
						'key'   => '_porto_variation_pre_order',
						'value' => 'yes',
					),
				);
			}
		}

		if ( $orderby ) {
			$args['orderby'] = sanitize_text_field( $orderby );
		}
		if ( $order ) {
			$args['order'] = sanitize_text_field( $order );
		}
		if ( ! empty( $atts['ids'] ) && is_array( $atts['ids'] ) ) {
			$args['post__in'] = array_map( 'sanitize_text_field', $atts['ids'] );
			$args['orderby']  = 'post__in';
		}
		// related posts for single builder
		if ( ! empty( $related_post ) ) {
			$args['post__not_in']  = array( $related_post );
			$args['category__in']  = wp_get_post_categories( $related_post );
			$args['no_found_rows'] = true;
		}

		// linked product for single product builder
		if ( ! empty( $linked_product ) ) {
			global $product;
			if ( 'product' == $post_type && ! empty( $product ) ) {
				$product_ids = array();
				if ( 'related' == $linked_product ) { // Related products
					$product_ids = wc_get_related_products( $product->get_id(), ( ! empty( $count ) ? $count : '4' ), $product->get_upsell_ids() );
				} elseif ( 'upsell' == $linked_product ) { // Upsell products
					$product_ids = $product->get_upsell_ids();
				}
				if ( ! empty( $product_ids ) ) {
					$args['post__in'] = $product_ids;
				}
			}
			//cart product
		}
		$posts_query = new WP_Query( $args );

		if ( 'product' == $post_type && class_exists( 'WooCommerce' ) ) {
			WC()->query->remove_ordering_args();
		}
	}
} else { // shop, archive builder
	global $wp_query;
	$posts_query = $wp_query;
}

$should_render_wrapper = 'archive' == $shortcode_type || 'shop' == $shortcode_type || ( empty( $source ) && $posts_query->have_posts() ) || ( 'terms' == $source && ! empty( $posts ) );

if ( $skeleton_enabled && ( ( empty( $source ) && ! $posts_query->have_posts() ) || ( 'terms' == $source && empty( $posts ) ) ) ) {
	$skeleton_enabled = false;
}

if ( $should_render_wrapper ) {
	add_filter( 'porto_is_tb_rendering', '__return_true' );

	$wrapper_id      = 'porto-posts-grid-' . porto_generate_rand( 4 );
	$shortcode_id    = porto_generate_rand( 4 );
	$wrap_class      = 'porto-posts-grid';
	$container_class = 'posts-wrap';
	if ( empty( $post_type ) ) {
		$post_type = 'post';
	}
	$container_class .= ' ' . $post_type . 's-container';
	$column_cls       = porto_grid_column_class( $columns, $columns_mobile, $columns_tablet );
	$wrap_attrs       = '';
	$container_attrs  = '';

	if ( 'shop' == $shortcode_type ) { // shop builder
		$wrap_class .= ' archive-products archive-posts';
		if ( false === strpos( $container_class, ' products ' ) ) {
			$container_class .= ' products';
		}
		$container_class .= isset( $list_layout ) ? ' list' : '';

		if ( ! empty( $other_col_cls ) ) { // list
			$container_attrs .= ' data-' . ( isset( $list_layout ) ? 'grid' : 'list' ) . '_col_cls="' . esc_attr( $other_col_cls ) . '"';
		}

		add_filter( 'porto_sb_products_rendered', '__return_true' );
	} elseif ( 'archive' == $shortcode_type ) {
		$wrap_class .= ' archive-posts-infinite';
	}
	if ( $el_class ) {
		$wrap_class .= ' ' . trim( $el_class );
	}
	if ( ! empty( $shortcode_class ) ) {
		$wrap_class .= ' ' . trim( $shortcode_class );
	}
	if ( $posts_wrap_cls ) {
		$container_class .= ' ' . trim( $posts_wrap_cls );
	}

	if ( ! $source && $pagination_style && '1' !== $pagination_style ) {
		$wrap_class .= ' porto-ajax-load';
		$wrap_attrs .= ' data-post_type="' . esc_attr( $post_type ) . '"';

		if ( 'ajax' == $pagination_style ) {
			$wrap_class .= ' load-ajax';
		} elseif ( 'load_more' == $pagination_style ) {
			$wrap_class .= ' load-more';
			wp_enqueue_script( 'porto-jquery-infinite-scroll' );
		} elseif ( 'infinite' == $pagination_style ) {
			$wrap_class .= ' load-infinite';
			wp_enqueue_script( 'porto-jquery-infinite-scroll' );
		}
	}

	$wrap_class .= ' porto-' . ( 'post' == $post_type ? 'blog' : $post_type . 's' ) . $shortcode_id;
	if ( ! $source && ( $category_filter || $pagination_style ) ) {
		$options = array( 'shortcode' => 'porto_posts_grid' );
		foreach ( $default_atts as $key => $val ) {
			if ( ! empty( $atts[ $key ] ) ) {
				$options[ $key ] = $atts[ $key ];
			}
		}
		$wrap_attrs .= ' data-ajax_load_options="' . esc_attr( json_encode( $options ) ) . '"';
		wp_enqueue_script( 'porto-infinite-scroll' );
	}

	$container_tag = 'div';
	$item_base_cls = 'porto-tb-item';

	// init product default type vars
	$is_product_default_layout = ( ! $builder_post && ( ( empty( $source ) && 'product' == $post_type ) || ( 'terms' == $source && 'product_cat' == $tax ) ) );
	if ( $is_product_default_layout ) {
		$container_tag = 'ul';

		global $woocommerce_loop, $porto_woocommerce_loop;
		if ( empty( $woocommerce_loop ) ) {
			$woocommerce_loop = array();
		}
		if ( empty( $porto_woocommerce_loop ) ) {
			$porto_woocommerce_loop = array();
		}
		$woocommerce_loop['product_loop'] = 0;
		$woocommerce_loop['cat_loop']     = 0;
		$porto_woocommerce_loop['view']   = $view;

		if ( empty( $woocommerce_loop['addlinks_pos'] ) ) {
			$woocommerce_loop['addlinks_pos'] = isset( $porto_settings['category-addlinks-pos'] ) ? $porto_settings['category-addlinks-pos'] : 'outimage_aq_onimage';
		}
		if ( 'creative' == $view ) {
			$woocommerce_loop['addlinks_pos'] = 'onimage';
		}

		if ( $image_size ) {
			$porto_woocommerce_loop['image_size'] = $image_size;
		}

		if ( 'slider' == $view ) {
			$wrap_class      .= ' slider-wrapper';
			$container_class .= ' products-slider';
		}

		// list type
		if ( 'shop' == $shortcode_type && empty( $source ) && isset( $_COOKIE['gridcookie'] ) && 'list' == $_COOKIE['gridcookie'] ) {
			$container_class                 .= ' list';
			$woocommerce_loop['addlinks_pos'] = '';
			if ( ! has_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt' ) ) {
				$should_remove_excerpt_action = true;
				add_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt', 9 );
			}
		}

		$porto_woocommerce_loop['addlinks_pos'] = $woocommerce_loop['addlinks_pos'];

		$legacy_mode = apply_filters( 'porto_legacy_mode', true );
		$legacy_mode = ( $legacy_mode && ! empty( $porto_settings['product-quickview'] ) ) || ! $legacy_mode;
		if ( $legacy_mode || ! empty( $porto_settings['show_swatch'] ) ) {
			// load wc variation script
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		if ( 'creative' == $view ) {
			$column_cls = str_replace( 'has-ccols', 'products has-ccols', $column_cls );
		} else {
			if ( $columns ) {
				$cols_arr    = porto_generate_shop_columns( $columns );
				$cols_arr[0] = $columns_mobile;
				$column_cls  = 'products';

				if ( count( $cols_arr ) > 4 ) {
					$column_cls .= ' pcols-xl-' . $cols_arr[4];
				}
				$column_cls .= ' pcols-lg-' . $cols_arr[3];
				$column_cls .= ' pcols-md-' . $cols_arr[2];
				if ( $columns_tablet ) {
					$column_cls .= ' pcols-sm-' . $columns_tablet;
					if ( $cols_arr[1] > $columns_tablet ) {
						$cols_arr[1] = $columns_tablet;
					}
				}
				$column_cls .= ' pcols-xs-' . $cols_arr[1];
				$column_cls .= ' pcols-ls-' . $cols_arr[0];
			}
		}
	} elseif ( ! $builder_post ) {
		if ( empty( $source ) ) {
			if ( 'portfolio' == $post_type ) {
				global $porto_portfolio_columns;
				$porto_portfolio_columns = (int) $columns;
			} elseif ( 'member' == $post_type && 'creative' != $view ) {
				global $porto_member_overview, $porto_member_socials;
				$porto_member_overview = 'yes';
				$porto_member_socials  = 'yes';
			}
		} elseif ( 'terms' == $source ) {

		}
	}

	$container_class .= ' ' . trim( $column_cls );

	if ( $builder_id && ( ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) ) {
		$wrap_attrs .= ' data-tb-id=' . $builder_id;
	}
	// print wrapper div
	echo '<div id="' . esc_attr( $wrapper_id ) . '" class="' . esc_attr( $wrap_class ) . '"' . $wrap_attrs . '>';

	ob_start();
	echo '<style scope="scope">';
	if ( $builder_id && ! in_array( $builder_id, $porto_settings['rendered_builders'] ) ) {
		$porto_settings['rendered_builders'][] = $builder_id;

		$css = get_post_meta( $builder_id, 'porto_builder_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
		$css = get_post_meta( $builder_id, 'porto_blocks_style_options_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
		$css = get_post_meta( $builder_id, 'custom_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
	}
	if ( 'shop' == $shortcode_type && $list_builder_id && ! in_array( $list_builder_id, $porto_settings['rendered_builders'] ) ) {
		$porto_settings['rendered_builders'][] = $list_builder_id;

		$css = get_post_meta( $list_builder_id, 'porto_builder_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
		$css = get_post_meta( $list_builder_id, 'porto_blocks_style_options_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
		$css = get_post_meta( $list_builder_id, 'custom_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
	}

	// view
	if ( 'creative' == $view ) {
		$porto_grid_layout  = porto_creative_grid_layout( $grid_layout );
		$container_class   .= ' grid-creative';
		$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
		$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
		porto_creative_grid_style( $porto_grid_layout, $grid_height_number, $wrapper_id, $spacing, false, $unit, '.porto-tb-item' );

		$post_count = 0;
		if ( $pagination_style && ! empty( $paged ) && (int) $paged > 1 && ( 'infinite' == $pagination_style || 'load_more' == $pagination_style ) ) {
			$number     = $count ? $count : get_option( 'posts_per_page' );
			$post_count = (int) $number * ( (int) $paged - 1 ) % count( $porto_grid_layout );
		}

		$iso_options                    = array();
		$iso_options['layoutMode']      = 'masonry';
		$iso_options['itemSelector']    = '.porto-tb-item';
		$iso_options['masonry']         = array( 'columnWidth' => '.grid-col-sizer' );
		$iso_options['animationEngine'] = 'best-available';
		$iso_options['resizable']       = false;
		$container_attrs               .= ' data-plugin-masonry data-plugin-options="' . esc_attr( json_encode( $iso_options ) ) . '"';
	} elseif ( 'slider' == $view ) {
		$container_class .= ' owl-carousel porto-carousel';
		if ( $navigation ) {
			if ( $nav_pos ) {
				$container_class .= ' ' . $nav_pos;
			}
			if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
				$container_class .= ' ' . $nav_pos2;
			}
			if ( $nav_type ) {
				$container_class .= ' ' . $nav_type;
			} else {
				$container_class .= ' show-nav-middle';
			}
			if ( $show_nav_hover ) {
				$container_class .= ' show-nav-hover';
			}
		}

		if ( $pagination ) {
			if ( $dots_pos ) {
				$container_class .= ' ' . $dots_pos;
			}
			if ( $dots_style ) {
				$container_class .= ' ' . $dots_style;
			}
		}

		$options = array(
			'themeConfig' => true,
			'items'       => (int) $columns,
			'lg'          => (int) $columns,
			'xs'          => (int) $columns_mobile,
		);
		if ( $builder_post || ! $is_product_default_layout ) {
			if ( '0' == $spacing || ! empty( $spacing ) ) {
				$options['margin'] = (int) $spacing;
			} else {
				$options['margin'] = (int) $porto_settings['grid-gutter-width'];
			}
		}

		if ( $autoplay ) {
			$options['autoplay'] = ( 'yes' == $autoplay ? true : false );
			if ( 5000 !== intval( $autoplay_timeout ) && $autoplay_timeout ) {
				$options['autoplayTimeout'] = (int) $autoplay_timeout;
			}
		}

		switch ( $columns ) {
			case '2':
				$options['md'] = 2;
				break;
			case '3':
				$options['md'] = 2;
				break;
			case '4':
				$options['md'] = 3;
				$options['sm'] = 2;
				break;
			case '5':
			case '6':
				$options['md'] = 4;
				$options['sm'] = 3;
				break;
			case '7':
			case '8':
				$options['xl'] = $columns;
				$options['lg'] = 6;
				$options['md'] = 5;
				$options['sm'] = 4;
				break;
		}

		if ( $columns_tablet ) {
			if ( ! isset( $options['sm'] ) || $options['sm'] > (int) $columns_tablet ) {
				$options['sm'] = (int) $columns_tablet;
				unset( $options['md'] );
			} else {
				$options['md'] = (int) $columns_tablet;
			}
		}

		if ( $stage_padding ) {
			$options['stagePadding'] = (int) $stage_padding;
			$container_class        .= ' stage-margin';
		}

		$options['nav']   = $navigation ? true : false;
		$options['dots']  = $pagination ? true : false;
		$container_attrs .= ' data-plugin-options="' . esc_attr( json_encode( $options ) ) . '"';
	} elseif ( 'masonry' == $view ) {
		$iso_options                    = array();
		$iso_options['layoutMode']      = 'masonry';
		$iso_options['itemSelector']    = '.porto-tb-item';
		$iso_options['masonry']         = array( 'columnWidth' => '.porto-tb-item' );
		$iso_options['animationEngine'] = 'best-available';
		$iso_options['resizable']       = false;

		$container_attrs .= ' data-plugin-masonry data-plugin-options="' . esc_attr( json_encode( $iso_options ) ) . '"';
	}

	$container_class .= ' has-ccols-spacing';

	$args = array();
	if ( $image_size ) {
		$GLOBALS['porto_post_image_size'] = $image_size;
	}
	echo '</style>';
	porto_filter_inline_css( ob_get_clean() );


	/**
	 * Fires before archive posts is rendered
	 *
	 * @since 2.3.0
	 */
	do_action( 'porto_before_tb_loop' );

	// display category filter
	if ( $category_filter && ! $filter_cat_tax && 'product' == $post_type ) {
		$filter_cat_tax = 'product_cat';
	}
	if ( $category_filter && in_array( $post_type, apply_filters( 'available_posts_grid_post_types', porto_supported_post_types() ) ) ) {
		$taxonomies = get_object_taxonomies( $post_type );
		if ( ! empty( $taxonomies ) && ( empty( $filter_cat_tax ) || ! in_array( $filter_cat_tax, $taxonomies ) ) ) {
			$filter_cat_tax = $taxonomies[0];
		}
	}
	if ( ! $source && $category_filter && $filter_cat_tax && ( $filter_tax_obj = get_taxonomy( $filter_cat_tax ) ) && ! empty( $filter_tax_obj->object_type ) && in_array( $post_type, $filter_tax_obj->object_type ) ) {

		$tax_args = array(
			'taxonomy'   => $filter_cat_tax,
			'hide_empty' => true,
		);
		if ( $post_tax && ! empty( $post_terms ) ) {
			if ( count( $post_terms ) > 1 ) {
				$tax_args['include'] = $post_terms;
				$tax_args['orderby'] = 'include';
			} else {
				$tax_args['parent'] = count( $post_terms ) ? $post_terms[0] : 0;
			}
		}
		$taxs = get_terms( $tax_args );
		if ( ! empty( $taxs ) ) :
			$active_cat = '';
			if ( ! empty( $atts['cats'] ) ) {
				$active_cat = sanitize_text_field( $atts['cats'] );
			} elseif ( ! empty( $_REQUEST['product_cat'] ) ) {
				$active_cat = sanitize_text_field( $_REQUEST['product_cat'] );
			} elseif ( is_archive() ) {
				$current_term = get_queried_object();
				if ( $current_term && isset( $current_term->slug ) ) {
					$active_cat = $current_term->slug;
				}
			}

			$all_link = get_post_type_archive_link( $post_type );
			if ( ! $all_link && $post_type ) {
				$all_link = site_url() . '?post_type=' . $post_type;
			}
			?>
			<ul class="<?php echo esc_attr( $post_type ); ?>-filter nav sort-source nav-pills porto-ajax-filter" data-filter-type="<?php echo esc_attr( $post_type ); ?>">
				<li data-filter="*"<?php echo ! $active_cat ? ' class="active"' : ''; ?>><a href="<?php echo esc_url( $all_link ); ?>"><?php esc_html_e( 'Show All', 'porto-functionality' ); ?></a></li>
			<?php foreach ( $taxs as $idx => $tax ) : ?>
				<li data-filter="<?php echo esc_attr( $tax->slug ); ?>"<?php echo ! $active_cat || $active_cat != $tax->slug ? '' : ' class="active"'; ?>><a href="<?php echo esc_url( get_term_link( $tax ) ); ?>"><?php echo esc_html( $tax->name ); ?></a></li>
			<?php endforeach; ?>
			</ul>
			<?php
		endif;
	}

	// infinite scrolling
	if ( ! $source && ( 'infinite' == $pagination_style || 'load_more' == $pagination_style ) && $posts_query->max_num_pages ) {
		$container_attrs .= ' data-cur_page="' . ( ! empty( $paged ) ? (int) $paged : 1 ) . '" data-max_page="' . intval( $posts_query->max_num_pages ) . '"';
	}

	// add animation
	if ( $animation_type ) {
		$container_attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$container_attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$container_attrs .= ' data-appear-animation-duration="' . absint( $animation_duration ) . '"';
		}
	}

	// Add edit link for admins.
	$edit_link_html = '';
	if ( $builder_id && current_user_can( 'edit_pages' ) && ! is_customize_preview() && (
			( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) &&
			( ! function_exists( 'porto_is_elementor_preview' ) || ! porto_is_elementor_preview() ) &&
			( ! function_exists( 'porto_is_vc_preview' ) || ! porto_is_vc_preview() )
			) ) {
		if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $builder_id, '_elementor_edit_mode', true ) ) {
			$edit_link = admin_url( 'post.php?post=' . $builder_id . '&action=elementor' );
		} else {
			$edit_link = admin_url( 'post.php?post=' . $builder_id . '&action=edit' );
		}
		$builder_type = get_post_meta( $builder_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
		if ( ! $builder_type ) {
			$builder_type = __( 'Template', 'porto-functionality' );
		}
		/* translators: template name */
		$edit_link_html = '<div class="pb-edit-link d-none" data-title="' . sprintf( esc_html__( 'Edit %1$s: %2$s', 'porto-functionality' ), esc_attr( $builder_type ), esc_attr( get_the_title( $builder_id ) ) ) . '" data-link="' . esc_url( $edit_link ) . '"></div>';
	}

	// skeleton
	if ( $skeleton_enabled ) {
		$porto_settings['skeleton_lazyload'] = true;
		$container_class                    .= ' skeleton-loading';
	}

	// container html
	echo '<' . $container_tag . ' class="' . esc_attr( $container_class ) . '"' . $container_attrs . '>';

	// nothing found
	if ( isset( $posts_query ) && ! $posts_query->have_posts() && ( 'archive' == $shortcode_type || 'shop' == $shortcode_type ) ) {
		if ( 'shop' == $shortcode_type && ! $post_found_nothing ) {
			echo '<div class="nothing-found-message"><p class="woocommerce-info">' . esc_html__( 'No products were found matching your selection.', 'woocommerce' ) . '</p></div>';
		} else {
			echo '<div class="nothing-found-message' . ( empty( $shortcode_class ) ? '' : ' ' . trim( $shortcode_class ) ) . '">' . ( ! empty( $post_found_nothing ) ? do_shortcode( $post_found_nothing ) : '' ) . '</div>';
		}
	}

	if ( $skeleton_enabled ) {
		ob_start();
	}

	// default product template
	if ( $is_product_default_layout ) {
		do_action( 'porto_woocommerce_shop_loop_start' );
	}

	$original_query          = $GLOBALS['wp_query'];
	$original_queried_object = $GLOBALS['wp_query']->queried_object;

	if ( empty( $source ) && $posts_query->have_posts() ) { // posts

		$original_post = $GLOBALS['post'];
		if ( 'product' == $post_type ) {
			if ( ! class_exists( 'Woocommerce' ) ) {
				$post_type = 'post';
			}

			if ( isset( $GLOBALS['product'] ) ) {
				$original_product = $GLOBALS['product'];
			}

			$GLOBALS['porto_tb_catalog_mode'] = false;
			if ( $porto_settings['catalog-enable'] ) {
				if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
					if ( ! $porto_settings['catalog-cart'] ) {
						$GLOBALS['porto_tb_catalog_mode'] = true;
					}
				}
			}

			if ( $builder_post ) {
				if ( isset( $porto_settings['show_swatch'] ) ) {
					$original_swatch = $porto_settings['show_swatch'];
				}
				$porto_settings['show_swatch'] = true;
			}

			if ( $porto_settings['add-to-cart-notification'] && ! has_action( 'porto_after_wrapper', 'porto_woocommerce_add_to_cart_notification_html' ) ) {
				add_action( 'porto_after_wrapper', 'porto_woocommerce_add_to_cart_notification_html' );
			}

			// display product categories
			if ( 'shop' == $shortcode_type ) {
				wc_set_loop_prop( 'cat_loop', 0 );
				$categories_html = woocommerce_maybe_show_product_subcategories();
				if ( $categories_html ) {
					echo apply_filters( 'porto_posts_grid_product_subcategories_html', str_replace( array( '<li class="product-category product-col', '</li>' ), array( '<div class="product-category product-col', '</div>' ), $categories_html ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				global $wp_query;
				$posts_query = $wp_query;
			}
		}
		while ( $posts_query->have_posts() ) {
			$posts_query->the_post();
			global $post;
			$GLOBALS['wp_query']->queried_object = $post;

			$item_cls = $item_base_cls;
			if ( $post_type ) {
				$item_cls .= ' ' . $post_type;
			}

			if ( 'product' == $post_type ) {
				$GLOBALS['product'] = wc_get_product( $post->ID );
				if ( ! $GLOBALS['product'] || ! $GLOBALS['product']->is_visible() ) {
					continue;
				}
				$item_cls .= ' product-col';
			}

			if ( 'creative' == $view && isset( $post_count ) ) {
				global $porto_post_image_size;
				$grid_layout           = $porto_grid_layout[ $post_count % count( $porto_grid_layout ) ];
				$porto_post_image_size = $grid_layout['size'];
				$post_count++;
				$item_cls .= ' grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ( isset( $grid_layout['width_lg'] ) ? ' grid-col-lg-' . $grid_layout['width_lg'] : '' ) . ( isset( $grid_layout['height'] ) ? ' grid-height-' . $grid_layout['height'] : '' );
			}

			// display edit link
			if ( $edit_link_html && $builder_post ) {
				echo porto_filter_output( $edit_link_html );
				$item_cls      .= ' porto-block';
				$edit_link_html = '';
			}

			if ( $builder_post ) {
				echo '<div ';
				post_class( $item_cls );
				echo '>';
				echo do_blocks( $builder_post->post_content );
				do_action( 'porto_posts_grid_item_rendered', $builder_post, $atts );
				echo '</div>';
			} elseif ( 'product' == $post_type ) {
				wc_get_template(
					'content-product.php',
					array(
						'product_classes' => str_replace( ' product-col', '', $item_cls ),
					)
				);
			} elseif ( 'portfolio' == $post_type ) {
				porto_get_template_part(
					'content-archive-portfolio',
					'grid',
					array(
						'post_classes' => str_replace( ' portfolio', '', $item_cls ),
					)
				);
			} elseif ( 'member' == $post_type ) {
				porto_get_template_part(
					'content-archive',
					'member',
					array(
						'post_classes' => str_replace( ' member', '', $item_cls ),
					)
				);
			} elseif ( 'event' == $post_type ) {
				porto_get_template_part(
					'content-archive-event',
					'grid',
					array(
						'post_classes' => str_replace( ' event', '', $item_cls ),
						'post'         => $post,
					)
				);
			} elseif ( 'faq' == $post_type ) {
				porto_get_template_part(
					'content-archive-faq',
					'grid',
					array(
						'post_classes' => str_replace( ' faq', '', $item_cls ),
					)
				);
			} else {
				$args = array(
					'post_view'    => 'style-5',
					'post_classes' => $item_cls,
				);
				if ( 1 === (int) $columns ) {
					$args['image_size'] = 'full';
				}
				porto_get_template_part(
					'content-post',
					'item',
					$args
				);
			}
		}

		wp_reset_postdata();

		// Restore global data.
		$GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( 'product' == $post_type ) {
			if ( isset( $original_product ) ) {
				$GLOBALS['product'] = $original_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
			if ( isset( $original_swatch ) ) {
				$porto_settings['show_swatch'] = $original_swatch;
			}
		}
	} elseif ( 'terms' == $source && ! empty( $posts ) ) { // terms
		$original_is_tax     = $GLOBALS['wp_query']->is_tax;
		$original_is_archive = $GLOBALS['wp_query']->is_archive;

		$GLOBALS['wp_query']->is_tax     = true;
		$GLOBALS['wp_query']->is_archive = true;

		foreach ( $posts as $term ) {
			$GLOBALS['wp_query']->queried_object = $term;

			$item_cls = $item_base_cls;
			if ( 'creative' == $view && isset( $post_count ) ) {
				global $porto_post_image_size;
				$grid_layout           = $porto_grid_layout[ $post_count % count( $porto_grid_layout ) ];
				$porto_post_image_size = $grid_layout['size'];
				$post_count++;
				$item_cls .= ' grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ( isset( $grid_layout['width_lg'] ) ? ' grid-col-lg-' . $grid_layout['width_lg'] : '' ) . ( isset( $grid_layout['height'] ) ? ' grid-height-' . $grid_layout['height'] : '' );
			}

			// display edit link
			if ( $edit_link_html && $builder_post ) {
				echo porto_filter_output( $edit_link_html );
				$item_cls      .= ' porto-block';
				$edit_link_html = '';
			}

			if ( $builder_post ) {
				echo '<div class="' . esc_attr( $item_cls ) . '">';
				echo do_blocks( $builder_post->post_content );
				do_action( 'porto_posts_grid_item_rendered', $builder_post, $atts );
				echo '</div>';
			} elseif ( 'product_cat' == $tax ) {
				if ( function_exists( 'wc_get_template' ) ) {
					wc_get_template(
						'content-product-cat.php',
						array(
							'product_classes' => $item_cls,
							'category'        => $term,
						)
					);
				}
			} else {
				porto_get_template_part(
					'views/category/default',
					null,
					array(
						'post_classes' => $item_cls,
					)
				);
			}
		}

		$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_query']->is_tax         = $original_is_tax; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_query']->is_archive     = $original_is_archive; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
	$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	unset( $GLOBALS['porto_post_image_size'] );
	if ( ! $builder_post ) {
		unset( $GLOBALS['porto_portfolio_columns'], $GLOBALS['porto_member_overview'], $GLOBALS['porto_member_socials'] );
	}

	if ( 'creative' == $view ) {
		echo '<div class="grid-col-sizer"></div>';
	}

	if ( $skeleton_enabled ) {
		$posts_content_escaped = ob_get_clean();
		echo '<script type="text/template">';
		echo json_encode( $posts_content_escaped );
		echo '</script>';
	}

	// default product template
	if ( $is_product_default_layout ) {
		do_action( 'porto_woocommerce_shop_loop_end' );
	}

	// end container
	echo '</' . $container_tag . '>';

	if ( $skeleton_enabled ) {
		$posts_count = (int) $count;
		if ( empty( $posts_count ) ) {
			if ( 'terms' == $source ) {
				$posts_count = count( $posts );
			} else {
				$posts_count = $posts_query->post_count;
			}
		}
		echo '<' . $container_tag . ' class="' . esc_attr( str_replace( 'skeleton-loading', 'skeleton-body', $container_class ) ) . '"' . $container_attrs . '>';
		for ( $i = 0; $i < $posts_count; $i++ ) {
			echo '<' . ( 'ul' == $container_tag ? 'li' : 'div' ) . ' class="porto-tb-item post' . ( ! $source ? ' ' . esc_attr( $post_type ) : '' ) . ( ! $source && 'product' == $post_type ? ' product-col' : '' ) . '"></' . ( 'ul' == $container_tag ? 'li' : 'div' ) . '>';
		}
		echo '</' . $container_tag . '>';
	}

	if ( empty( $source ) && $pagination_style && function_exists( 'porto_pagination' ) ) {
		?>
		<input type="hidden" class="shortcode-id" value="<?php echo esc_attr( $shortcode_id ); ?>"/>
		<?php
		porto_pagination( $posts_query->max_num_pages, 'load_more' == $pagination_style );
	}

	// reset product default type vars
	if ( $is_product_default_layout ) {
		unset( $GLOBALS['woocommerce_loop'], $GLOBALS['porto_woocommerce_loop'] );
	}

	// custom js
	if ( $builder_id ) {
		$js_code = get_post_meta( $builder_id, 'custom_js_body', true );
		if ( $js_code ) {
			echo '<script>';
			echo porto_strip_script_tags( $js_code );
			echo '</script>';
		}
	}

	/**
	 * Fires after archive posts is rendered
	 *
	 * @since 2.3.0
	 */
	do_action( 'porto_after_tb_loop' );

	echo '</div>';

	remove_filter( 'porto_is_tb_rendering', '__return_true' );

	if ( ! empty( $should_remove_excerpt_action ) ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt', 9 );
	}
}
