<?php

function porto_page_title() {

	global $porto_settings;

	$output = '';

	if ( ! is_front_page() ) {

	} elseif ( is_home() && isset( $porto_settings['blog-title'] ) ) {
		$output .= $porto_settings['blog-title'];
	}

	if ( is_singular() ) {

		$post       = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$page_title = '';
		if ( is_page() && $porto_settings['pagetitle-parent'] ) {
			$page_title = empty( $post->post_parent ) ? '' : get_the_title( $post->post_parent );
		} elseif ( ! is_page() && $porto_settings['pagetitle-archives'] ) {
			if ( isset( $post->post_type ) && 'post' == $post->post_type && $porto_settings['pagetitle-archives'] ) {
				if ( get_option( 'show_on_front' ) == 'page' ) {
					$page_title = get_the_title( get_option( 'page_for_posts', true ) );
				} else {
					$page_title = porto_page_title_archive( $post->post_type );
				}
			} elseif ( isset( $post->post_type ) && 'product' == $post->post_type && $porto_settings['pagetitle-archives'] ) {
				$post_type        = 'product';
				$post_type_object = get_post_type_object( $post_type );
				if ( is_object( $post_type_object ) && function_exists( 'wc_get_page_id' ) ) {
					$shop_page_id = wc_get_page_id( 'shop' );
					$page_title   = $shop_page_id ? get_the_title( $shop_page_id ) : '';
					if ( ! $page_title ) {
						$page_title = $post_type_object->labels->name;
					} else {
						$page_title .= ' - ' . get_the_title();
					}
				}
			} else {
				$page_title  = porto_page_title_archive( $post->post_type );
				$page_title .= ' - ' . get_the_title();
			}
		}

		if ( $page_title ) {
			$output .= $page_title;
		} else {
			$output .= porto_page_title_leaf();
		}
	} else {
		if ( is_post_type_archive() ) {
			if ( is_search() ) {
				$output .= porto_page_title_leaf( 'search' );
			} else {
				$output .= porto_page_title_archive();
			}
		} elseif ( is_tax() || is_tag() || is_category() ) {
			$html = porto_page_title_leaf( 'term' );

			if ( is_tag() ) {
				/* translators: %s: Page title */
				$output .= sprintf( esc_html__( 'Tag - %s', 'porto' ), $html );
			} elseif ( is_tax( 'product_tag' ) ) {
				/* translators: %s: Page title */
				$output .= sprintf( esc_html__( 'Product Tag - %s', 'porto' ), $html );
			} else {
				$output .= $html;
			}
		} elseif ( is_date() ) {
			if ( is_year() ) {
				$output .= porto_page_title_leaf( 'year' );
			} elseif ( is_month() ) {
				$output .= porto_page_title_leaf( 'month' );
			} elseif ( is_day() ) {
				$output .= porto_page_title_leaf( 'day' );
			}
		} elseif ( is_author() ) {
			$output .= porto_page_title_leaf( 'author' );
		} elseif ( is_search() ) {
			$output .= porto_page_title_leaf( 'search' );
		} elseif ( is_404() ) {
			$output .= porto_page_title_leaf( '404' );
		} elseif ( class_exists( 'bbPress' ) && is_bbpress() ) {
			if ( bbp_is_search() ) {
				$output .= porto_page_title_leaf( 'bbpress_search' );
			} elseif ( bbp_is_single_user() ) {
				$output .= porto_page_title_leaf( 'bbpress_user' );
			} else {
				$output .= porto_page_title_leaf();
			}
		} else {
			if ( is_home() && ! is_front_page() ) {
				if ( get_option( 'show_on_front' ) == 'page' ) {
					$output .= get_the_title( get_option( 'page_for_posts', true ) );
				} else {
					if ( isset( $porto_settings['blog-title'] ) ) {
						$output .= $porto_settings['blog-title'];
					}
				}
			}
		}
	}

	return apply_filters( 'porto_page_title', $output );
}

function porto_page_sub_title() {
	global $porto_settings, $wp_query;

	$output = porto_get_meta_value( 'page_sub_title' );

	if ( $output ) {
		return apply_filters( 'porto_page_sub_title', $output );
	}

	if ( is_singular() ) {

		$post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		if ( is_page() && $porto_settings['pagetitle-parent'] && ! empty( $post->post_parent ) ) {
			$output = get_post_meta( $post->post_parent, 'page_sub_title', true );
		} elseif ( ! is_page() && $porto_settings['pagetitle-archives'] ) {
			if ( isset( $post->post_type ) && 'post' == $post->post_type && $porto_settings['pagetitle-archives'] ) {
				if ( get_option( 'show_on_front' ) == 'page' ) {
					$output = get_post_meta( get_option( 'page_for_posts', true ), 'page_sub_title', true );
				}
			} elseif ( isset( $post->post_type ) && 'product' == $post->post_type && $porto_settings['pagetitle-archives'] ) {
				$post_type        = 'product';
				$post_type_object = get_post_type_object( $post_type );
				if ( is_object( $post_type_object ) && function_exists( 'wc_get_page_id' ) && $shop_page_id = wc_get_page_id( 'shop' ) ) {
					$output = get_post_meta( $shop_page_id, 'page_sub_title', true );
				}
			} else {
				$post_type = $wp_query->query_vars['post_type'];
				$page_id   = 0;
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
				if ( $page_id && ( $post = get_post( $page_id ) ) ) {
					$output = get_post_meta( $page_id, 'page_sub_title', true );
				}
			}
		}
	} else {
		if ( is_home() && ! is_front_page() ) {
			if ( get_option( 'show_on_front' ) == 'page' ) {
				$output = get_post_meta( get_option( 'page_for_posts', true ), 'page_sub_title', true );
			}
		}
	}

	return apply_filters( 'porto_page_sub_title', $output );
}

function porto_page_title_leaf( $object_type = '' ) {
	global $wp_query;

	$post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

	switch ( $object_type ) {
		case 'term':
			$term  = $wp_query->get_queried_object();
			$title = $term->name;
			break;
		case 'year':
			/* translators: %s: Year */
			$title = sprintf( __( 'Yearly Archives - %s', 'porto' ), get_the_date( _x( 'Y', 'yearly archives date format', 'porto' ) ) );
			break;
		case 'month':
			/* translators: %s: Month */
			$title = sprintf( __( 'Monthly Archives - %s', 'porto' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'porto' ) ) );
			break;
		case 'day':
			/* translators: %s: Day */
			$title = sprintf( __( 'Daily Archives - %s', 'porto' ), get_the_date() );
			break;
		case 'author':
			$user = $wp_query->get_queried_object();
			/* translators: %s: Author name */
			$title = sprintf( __( 'Author - %s', 'porto' ), $user->display_name );
			break;
		case 'search':
			/* translators: %s: Search query */
			$title = sprintf( __( 'Search Results - %s', 'porto' ), esc_html( get_search_query() ) );
			break;
		case '404':
			$title = __( '404 - Page Not Found', 'porto' );
			break;
		case 'bbpress_search':
			/* translators: %s: Search query */
			$title = sprintf( __( 'Search Results - %s', 'porto' ), esc_html( get_query_var( 'bbp_search' ) ) );
			break;
		case 'bbpress_user':
			$current_user = wp_get_current_user();
			$title        = $current_user->user_nicename;
			break;
		default:
			$title = get_the_title( $post->ID );
			break;
	}

	return $title;
}

function porto_page_title_shop() {
	$post_type        = 'product';
	$post_type_object = get_post_type_object( $post_type );

	$output = '';
	if ( is_object( $post_type_object ) && class_exists( 'WooCommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$shop_page_id   = wc_get_page_id( 'shop' );
		$shop_page_name = $shop_page_id ? get_the_title( $shop_page_id ) : '';

		if ( ! $shop_page_name ) {
			$shop_page_name = $post_type_object->labels->name;
		}
		$output .= $shop_page_name;
	}

	return $output;
}

function porto_page_title_archive( $post_type = null ) {
	global $wp_query;

	if ( ! $post_type ) {
		$post_type = $wp_query->query_vars['post_type'];
	}
	$post_type_object = get_post_type_object( $post_type );
	$archive_title    = '';

	if ( is_object( $post_type_object ) ) {

		// woocommerce
		if ( 'product' == $post_type && $shop_title = porto_page_title_shop() ) {
			return $shop_title;
		}

		// bbpress
		if ( class_exists( 'bbPress' ) && 'topic' == $post_type ) {
			$archive_title = bbp_get_topic_archive_title();

			return $archive_title;
		}

		// default
		$archive_title = porto_title_archive_name( $post_type );
	}

	return $archive_title;
}

function porto_title_archive_name( $post_type = null ) {
	global $porto_settings, $wp_query;

	if ( ! $post_type ) {
		$post_type = $wp_query->query_vars['post_type'];
	}

	$page_id = 0;
	switch ( $post_type ) {
		case 'post':
			if ( get_option( 'show_on_front' ) == 'page' ) {
				$page_id = (int) ( get_option( 'page_for_posts', true ) );
			}
			break;
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
