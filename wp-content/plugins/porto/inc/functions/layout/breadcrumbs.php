<?php

add_filter( 'wpseo_breadcrumb_output_class', 'porto_wpseo_breadcrumb_output_class' );

function porto_wpseo_breadcrumb_output_class( $class ) {
	if ( $class ) {
		return $class . ' yoast-breadcrumbs';
	} else {
		return 'yoast-breadcrumbs';
	}
}

function porto_breadcrumbs() {

	// use yoast breadcrumbs if enabled
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		$yoast_breadcrumbs = yoast_breadcrumb( '', '', false );
		if ( $yoast_breadcrumbs ) {
			return $yoast_breadcrumbs;
		}
	}

	global $porto_settings;

	$post   = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	$output = '';

	// add breadcrumbs prefix
	if ( ! is_front_page() ) {
		if ( isset( $porto_settings['breadcrumbs-prefix'] ) && $porto_settings['breadcrumbs-prefix'] ) {
			$output .= '<span class="breadcrumbs-prefix">' . esc_html( $porto_settings['breadcrumbs-prefix'] ) . '</span>';
		}
	}

	// breadcrumbs start wrap
	$output .= '<ul class="breadcrumb"' . ( isset( $porto_settings['rich-snippets'] ) && $porto_settings['rich-snippets'] ? ' itemscope itemtype="https://schema.org/BreadcrumbList"' : '' ) . '>';

	// add home link
	if ( ! is_front_page() ) {
		$output .= porto_breadcrumbs_link( __( 'Home', 'porto' ), apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) );
	} elseif ( is_home() ) {
		$output .= porto_breadcrumbs_link( isset( $porto_settings['blog-title'] ) ? $porto_settings['blog-title'] : esc_html__( 'Blog', 'porto' ) );
	}

	// add woocommerce shop page link
	if ( class_exists( 'WooCommerce' ) && ( ! isset( $porto_settings['breadcrumbs-shop-link'] ) || $porto_settings['breadcrumbs-shop-link'] ) && ( ( is_woocommerce() && is_archive() && ! is_shop() ) || is_product() || is_cart() || is_checkout() || is_account_page() ) ) {
		$output .= porto_breadcrumbs_shop_link();
	}

	// add bbpress forums link
	if ( class_exists( 'bbPress' ) && is_bbpress() && ( bbp_is_topic_archive() || bbp_is_single_user() || bbp_is_search() || bbp_is_topic_tag() || bbp_is_edit() ) ) {
		$output .= porto_breadcrumbs_link( bbp_get_forum_archive_title(), get_post_type_archive_link( 'forum' ) );
	}

	if ( is_singular() ) {
		if ( isset( $post->post_type ) && 'post' == $post->post_type && get_option( 'show_on_front' ) == 'page' && ( isset( $porto_settings['breadcrumbs-blog-link'] ) && $porto_settings['breadcrumbs-blog-link'] ) ) {
			$output .= porto_breadcrumbs_link( get_the_title( get_option( 'page_for_posts', true ) ), get_permalink( get_option( 'page_for_posts' ) ) );
		} elseif ( isset( $post->post_type ) && 'product' !== $post->post_type && get_post_type_archive_link( $post->post_type ) && ( isset( $porto_settings['breadcrumbs-archives-link'] ) && $porto_settings['breadcrumbs-archives-link'] ) ) {
			$output .= porto_breadcrumbs_archive_link();
		}

		if ( isset( $post->post_parent ) && 0 == $post->post_parent ) {
			$output .= porto_breadcrumbs_terms_link();
		} else {
			$output .= porto_breadcrumbs_ancestors_link();
		}

		$output .= porto_breadcrumb_leaf();
	} else {
		if ( is_post_type_archive() ) {
			if ( is_search() ) {
				$output .= porto_breadcrumbs_archive_link();
				$output .= porto_breadcrumb_leaf( 'search' );
			} else {
				$output .= porto_breadcrumbs_archive_link( false );
			}
		} elseif ( is_tax() || is_tag() || is_category() ) {
			$html  = porto_breadcrumbs_taxonomies_link();
			$html .= porto_breadcrumb_leaf( 'term' );

			if ( is_tag() ) {
				if ( get_option( 'show_on_front' ) == 'page' && ( isset( $porto_settings['breadcrumbs-blog-link'] ) && $porto_settings['breadcrumbs-blog-link'] ) ) {
					$output .= porto_breadcrumbs_link( get_the_title( get_option( 'page_for_posts', true ) ), get_permalink( get_option( 'page_for_posts' ) ) );
				}
				/* translators: %s: Tag name */
				$output .= sprintf( '<li>' . esc_html__( 'Tag', 'porto' ) . '&nbsp;-&nbsp;%s</li>', $html );
			} elseif ( is_tax( 'product_tag' ) ) {
				/* translators: %s: Tag name */
				$output .= sprintf( '<li>' . esc_html__( 'Product Tag', 'porto' ) . '&nbsp;-&nbsp;%s</li>', $html );
			} else {
				if ( is_category() && get_option( 'show_on_front' ) == 'page' && ( isset( $porto_settings['breadcrumbs-blog-link'] ) && $porto_settings['breadcrumbs-blog-link'] ) ) {
					$output .= porto_breadcrumbs_link( get_the_title( get_option( 'page_for_posts', true ) ), get_permalink( get_option( 'page_for_posts' ) ) );
				}
				if ( is_tax( 'portfolio_cat' ) || is_tax( 'portfolio_skills' ) ) {
					$output .= porto_breadcrumbs_link( porto_breadcrumbs_archive_name( 'portfolio' ), get_post_type_archive_link( 'portfolio' ) );
				}
				if ( is_tax( 'member_cat' ) ) {
					$output .= porto_breadcrumbs_link( porto_breadcrumbs_archive_name( 'member' ), get_post_type_archive_link( 'member' ) );
				}
				if ( is_tax( 'faq_cat' ) ) {
					$output .= porto_breadcrumbs_link( porto_breadcrumbs_archive_name( 'faq' ), get_post_type_archive_link( 'faq' ) );
				}
				$output .= $html;
			}
		} elseif ( is_date() ) {
			global $wp_locale;

			if ( get_option( 'show_on_front' ) == 'page' && ( isset( $porto_settings['breadcrumbs-blog-link'] ) && $porto_settings['breadcrumbs-blog-link'] ) ) {
				$output .= porto_breadcrumbs_link( get_the_title( get_option( 'page_for_posts', true ) ), get_permalink( get_option( 'page_for_posts' ) ) );
			}

			$year = esc_html( get_query_var( 'year' ) );
			if ( is_month() || is_day() ) {
				$month      = get_query_var( 'monthnum' );
				$month_name = $wp_locale->get_month( $month );
			}

			if ( is_year() ) {
				$output .= porto_breadcrumb_leaf( 'year' );
			} elseif ( is_month() ) {
				$output .= porto_breadcrumbs_link( $year, get_year_link( $year ) );
				$output .= porto_breadcrumb_leaf( 'month' );
			} elseif ( is_day() ) {
				$output .= porto_breadcrumbs_link( $year, get_year_link( $year ) );
				$output .= porto_breadcrumbs_link( $month_name, get_month_link( $year, $month ) );
				$output .= porto_breadcrumb_leaf( 'day' );
			}
		} elseif ( is_author() ) {
			$output .= porto_breadcrumb_leaf( 'author' );
		} elseif ( is_search() ) {
			$output .= porto_breadcrumb_leaf( 'search' );
		} elseif ( is_404() ) {
			$output .= porto_breadcrumb_leaf( '404' );
		} elseif ( class_exists( 'bbPress' ) && is_bbpress() ) {
			if ( bbp_is_search() ) {
				$output .= porto_breadcrumb_leaf( 'bbpress_search' );
			} elseif ( bbp_is_single_user() ) {
				$output .= porto_breadcrumb_leaf( 'bbpress_user' );
			} else {
				$output .= porto_breadcrumb_leaf();
			}
		} elseif ( class_exists( 'WeDevs_Dokan' ) ) {
			$arr   = apply_filters( 'woocommerce_get_breadcrumb', array() );
			$index = 0;
			foreach ( $arr as $crumb ) {
				if ( $index == count( $arr ) - 1 ) {
					$output .= esc_html( $crumb[0] );
				} else {
					$output .= porto_breadcrumbs_link( $crumb[0], $crumb[1] );
				}
				$index++;
			}
		} else {
			if ( is_home() && ! is_front_page() ) {
				if ( get_option( 'show_on_front' ) == 'page' ) {
					$output .= porto_breadcrumbs_link( get_the_title( get_option( 'page_for_posts', true ) ) );
				} else {
					$output .= porto_breadcrumbs_link( isset( $porto_settings['blog-title'] ) ? $porto_settings['blog-title'] : esc_html__( 'Blog', 'porto' ) );
				}
			}
		}
	}

	// breadcrumbs end wrap
	$output .= '</ul>';

	return apply_filters( 'porto_breadcrumbs', $output );
}

class Porto_Breadcrumbs_Link_Position {
	public static $position = 1;
}
function porto_breadcrumbs_link( $title, $link = '' ) {

	global $porto_settings;

	$microdata = ( isset( $porto_settings['rich-snippets'] ) && $porto_settings['rich-snippets'] ) ? true : false;

	$microdata_itemscope = $microdata_url = $microdata_title = $microdata_position = $separator_markup = '';
	$microdata_itemscope = ( ! is_front_page() && 'Home' == $title ) ? ' class="home"' : '';
	if ( $microdata ) {
		$microdata_itemscope .= ' itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"';
		$microdata_url        = 'itemscope itemtype="https://schema.org/Thing" itemprop="item"';
		$microdata_title      = 'itemprop="name"';
		$microdata_position   = '<meta itemprop="position" content="' . Porto_Breadcrumbs_Link_Position::$position . '" />';
		Porto_Breadcrumbs_Link_Position::$position++;
	}

	$output    = sprintf( '<span %s>%s</span>', $microdata_title, $title );
	$delimiter = '';
	if ( $link ) {
		if ( $microdata ) {
			$microdata_url = 'itemprop="item"';
		}
		$output    = sprintf( '<a %s href="%s"%s>%s</a>%s', $microdata_url, esc_url( $link ), ( 'Home' == $title ) ? ' title="' . esc_attr__( 'Go to Home Page', 'porto' ) . '"' : '', $output, $microdata_position );
		$delimiter = '<i class="delimiter' . ( $porto_settings['breadcrumbs-delimiter'] ? ' ' . esc_attr( $porto_settings['breadcrumbs-delimiter'] ) : '' ) . '"></i>';
		$before    = sprintf( '<li%s>', $microdata_itemscope );
	} else {
		//$before = '<li>';
		$output     .= $microdata_position;
		$current_url = esc_url( home_url( add_query_arg( array() ) ) );
		$output     .= sprintf( '<meta %s content="%s">', $microdata_url, $current_url );
		$before      = sprintf( '<li%s>', $microdata_itemscope );
	}
	$after = '</li>';

	return $before . $output . $delimiter . $after;
}

function porto_breadcrumbs_simple_link( $title, $link = '' ) {
	global $porto_settings;

	$microdata = ( isset( $porto_settings['rich-snippets'] ) && $porto_settings['rich-snippets'] ) ? true : false;

	$microdata_itemscope = $microdata_url = $microdata_title = $microdata_position = $separator_markup = '';

	if ( $microdata ) {
		$microdata_itemscope               = 'itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"';
		$microdata_url                     = 'itemprop="item"';
		$microdata_title                   = 'itemprop="name"';
		static $porto_breadcrumbs_position = 1;
		$microdata_position                = '<meta itemprop="position" content="' . Porto_Breadcrumbs_Link_Position::$position . '" />';
		Porto_Breadcrumbs_Link_Position::$position++;
	}

	$output = sprintf( '<span %s>%s</span>', $microdata_title, $title );

	if ( $link ) {
		$output = sprintf( '<a %s href="%s">%s</a>%s', $microdata_url, $link, $output, $microdata_position );
	} else {
		$output .= $microdata_position;
	}

	$before = sprintf( '<span %s>', $microdata_itemscope );
	$after  = '</span>';

	return $before . $output . $after;
}

function porto_breadcrumb_leaf( $object_type = '' ) {
	global $wp_query, $wp_locale;

	$post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

	switch ( $object_type ) {
		case 'term':
			$term  = $wp_query->get_queried_object();
			$title = $term->name;
			break;
		case 'year':
			$title = esc_html( get_query_var( 'year' ) );
			break;
		case 'month':
			$title = $wp_locale->get_month( get_query_var( 'monthnum' ) );
			break;
		case 'day':
			$title = get_query_var( 'day' );
			break;
		case 'author':
			$user  = $wp_query->get_queried_object();
			$title = $user->display_name;
			break;
		case 'search':
			$search = esc_html( get_search_query() );
			if ( $product_cat = get_query_var( 'product_cat' ) ) {
				$product_cat = get_term_by( 'slug', $product_cat, 'product_cat' );
				$search      = '<a href="' . esc_url( get_term_link( $product_cat, 'product_cat' ) ) . '">' . esc_html( $product_cat->name ) . '</a>' . ( $search ? ' / ' : '' ) . $search;
			}
			/* translators: %s: Search query */
			$title = sprintf( __( 'Search - %s', 'porto' ), $search );
			break;
		case '404':
			$title = __( '404', 'porto' );
			break;
		case 'bbpress_search':
			/* translators: %s: Search query */
			$title = sprintf( __( 'Search - %s', 'porto' ), esc_html( get_query_var( 'bbp_search' ) ) );
			break;
		case 'bbpress_user':
			$current_user = wp_get_current_user();
			$title        = $current_user->user_nicename;
			break;
		default:
			$title = get_the_title( $post->ID );
			break;
	}

	$before = '<li>';
	$after  = '</li>';

	return $before . $title . $after;
}

function porto_breadcrumbs_links( $output ) {
	global $porto_settings;
	$delimiter = '<i class="delimiter' . ( $porto_settings['breadcrumbs-delimiter'] ? ' ' . esc_attr( $porto_settings['breadcrumbs-delimiter'] ) : '' ) . '"></i>';
	$before    = '<li>';
	$after     = '</li>';
	return $before . $output . $delimiter . $after;
}

function porto_breadcrumbs_shop_link( $linked = true ) {
	$post_type        = 'product';
	$post_type_object = get_post_type_object( $post_type );
	$link             = '';

	$output = '';
	if ( is_object( $post_type_object ) && class_exists( 'WooCommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$shop_page_id   = wc_get_page_id( 'shop' );
		$shop_page_name = $shop_page_id ? get_the_title( $shop_page_id ) : '';

		if ( ! $shop_page_name ) {
			$shop_page_name = $post_type_object->labels->name;
		}
		if ( $linked ) {
			$link = -1 !== $shop_page_id ? get_permalink( $shop_page_id ) : get_post_type_archive_link( $post_type );
		}
		$output .= porto_breadcrumbs_link( $shop_page_name, esc_url( $link ) );
	}

	return $output;
}

function porto_breadcrumbs_archive_link( $linked = true ) {
	global $wp_query;

	$post_type        = $wp_query->query_vars['post_type'];
	$post_type_object = get_post_type_object( $post_type );
	$link             = '';
	$archive_title    = '';

	if ( is_object( $post_type_object ) ) {

		// woocommerce
		if ( 'product' == $post_type && $shop_link = porto_breadcrumbs_shop_link( $linked ) ) {
			return $shop_link;
		}

		// bbpress
		if ( class_exists( 'bbPress' ) && 'topic' == $post_type ) {
			if ( $linked ) {
				$archive_title = bbp_get_forum_archive_title();
				$link          = get_post_type_archive_link( bbp_get_forum_post_type() );
			} else {
				$archive_title = bbp_get_topic_archive_title();
			}

			return porto_breadcrumbs_link( $archive_title, $link );
		}

		// default
		$archive_title = porto_breadcrumbs_archive_name( $post_type );
	}

	if ( $linked ) {
		$link = get_post_type_archive_link( $post_type );
	}

	if ( $archive_title ) {
		return porto_breadcrumbs_link( $archive_title, $link );
	}

	return '';
}

function porto_breadcrumbs_archive_name( $post_type ) {
	global $porto_settings;

	$page_id = 0;
	switch ( $post_type ) {
		case 'portfolio':
			$page_id = (int) ( ( isset( $porto_settings ) && isset( $porto_settings['portfolio-archive-page'] ) && $porto_settings['portfolio-archive-page'] ) ? $porto_settings['portfolio-archive-page'] : 0 );
			break;
		case 'member':
			$page_id = (int) ( ( isset( $porto_settings ) && isset( $porto_settings['member-archive-page'] ) && $porto_settings['member-archive-page'] ) ? $porto_settings['member-archive-page'] : 0 );
			break;
		case 'faq':
			$page_id = (int) ( ( isset( $porto_settings ) && isset( $porto_settings['faq-archive-page'] ) && $porto_settings['faq-archive-page'] ) ? $porto_settings['faq-archive-page'] : 0 );
			break;
	}

	$archive_title = '';

	if ( $page_id && ( $post = get_post( $page_id ) ) ) {
		$archive_title = $post->post_title;
	} else {
		$post_type_object = get_post_type_object( $post_type );

		if ( is_object( $post_type_object ) ) {

			if ( isset( $post_type_object->label ) && '' !== $post_type_object->label ) {
				$archive_title = $post_type_object->label;
			} elseif ( isset( $post_type_object->labels->menu_name ) && '' !== $post_type_object->labels->menu_name ) {
				$archive_title = $post_type_object->labels->menu_name;
			} else {
				$archive_title = $post_type_object->name;
			}
		}
	}

	return $archive_title;
}

function porto_breadcrumbs_terms_link() {

	global $porto_settings;

	$output = '';
	$post   = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

	if ( ! ( isset( $porto_settings['breadcrumbs-categories'] ) && $porto_settings['breadcrumbs-categories'] ) ) {
		return $output;
	}

	if ( 'post' == $post->post_type ) {
		$taxonomy = 'category';
	} elseif ( 'portfolio' == $post->post_type ) {
		$taxonomy = 'portfolio_cat';
	} elseif ( 'member' == $post->post_type ) {
		$taxonomy = 'member_cat';
	} elseif ( 'faq' == $post->post_type ) {
		$taxonomy = 'faq_cat';
	} elseif ( 'product' == $post->post_type && class_exists( 'WooCommerce' ) && is_woocommerce() ) {
		$taxonomy = 'product_cat';
	} else {
		return $output;
	}

	$terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'orderby' => 'term_id' ) );

	if ( empty( $terms ) ) {
		return $output;
	}

	$terms_by_id = array();
	foreach ( $terms as $term ) {
		$terms_by_id[ $term->term_id ] = $term;
	}

	foreach ( $terms as $term ) {
		unset( $terms_by_id[ $term->parent ] );
	}

	if ( count( $terms_by_id ) == 1 ) {
		unset( $terms );
		$terms[0] = array_shift( $terms_by_id );
	}

	if ( count( $terms ) == 1 ) {
		$term_parent = $terms[0]->parent;

		if ( $term_parent ) {
			$term_tree   = get_ancestors( $terms[0]->term_id, $taxonomy );
			$term_tree   = array_reverse( $term_tree );
			$term_tree[] = get_term( $terms[0]->term_id, $taxonomy );

			$i = 0;
			foreach ( $term_tree as $term_id ) {
				$term_object = get_term( $term_id, $taxonomy );
				if ( 0 == $i++ ) {
					$output .= porto_breadcrumbs_simple_link( $term_object->name, get_term_link( $term_object ) );
				} else {
					$output .= ', ' . porto_breadcrumbs_simple_link( $term_object->name, get_term_link( $term_object ) );
				}
			}
			$output = porto_breadcrumbs_links( $output );
		} else {
			$output = porto_breadcrumbs_link( $terms[0]->name, get_term_link( $terms[0] ) );
		}
	} else {
		$output = porto_breadcrumbs_simple_link( $terms[0]->name, get_term_link( $terms[0] ) );
		array_shift( $terms );
		foreach ( $terms as $term ) {
			$output .= ', ' . porto_breadcrumbs_simple_link( $term->name, get_term_link( $term ) );
		}
		$output = porto_breadcrumbs_links( $output );
	}

	return $output;
}

function porto_breadcrumbs_ancestors_link() {
	$output = '';

	$post              = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	$post_ancestor_ids = array_reverse( get_post_ancestors( $post ) );

	foreach ( $post_ancestor_ids as $post_ancestor_id ) {
		$post_ancestor = get_post( $post_ancestor_id );
		$output       .= porto_breadcrumbs_link( $post_ancestor->post_title, get_permalink( $post_ancestor->ID ) );
	}

	return $output;
}

function porto_breadcrumbs_taxonomies_link() {
	global $wp_query;
	$term   = $wp_query->get_queried_object();
	$output = '';

	if ( $term && 0 != $term->parent && isset( $term->taxonomy ) && isset( $term->term_id ) && is_taxonomy_hierarchical( $term->taxonomy ) ) {
		$term_ancestors = get_ancestors( $term->term_id, $term->taxonomy );
		$term_ancestors = array_reverse( $term_ancestors );

		foreach ( $term_ancestors as $term_ancestor ) {
			$term_object = get_term( $term_ancestor, $term->taxonomy );
			$output     .= porto_breadcrumbs_link( $term_object->name, get_term_link( $term_object->term_id, $term->taxonomy ) );
		}
	}

	return $output;
}


add_action(
	'init',
	function() {
		global $porto_settings;
		$action_name = 'porto_after_banner';
		if ( isset( $porto_settings['breadcrumbs-pos'] ) && 'inner_top' == $porto_settings['breadcrumbs-pos'] ) {
			$action_name = 'porto_before_content_inner_top';
		}
		add_action(
			$action_name,
			function() {
				do_action( 'porto_before_breadcrumbs' );
				get_template_part( 'breadcrumbs' );
				do_action( 'porto_before_main' );
			}
		);
	}
);
