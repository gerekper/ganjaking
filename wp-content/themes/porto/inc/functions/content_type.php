<?php
require_once( PORTO_FUNCTIONS . '/content_type/portfolio_like.php' );
require_once( PORTO_FUNCTIONS . '/content_type/blog_like.php' );
require_once( PORTO_FUNCTIONS . '/content_type/meta_values.php' );

function porto_get_id_type() {
	if ( is_archive() ) {
		if ( function_exists( 'is_shop' ) && is_shop() && ! is_product_category() ) {
			return array( wc_get_page_id( 'shop' ), 'post' );
		} elseif ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() && ( $archive_page = porto_portfolios_page_id() ) ) {
			return array( $archive_page, 'post' );
		} elseif ( function_exists( 'is_porto_members_page' ) && is_porto_members_page() && ( $archive_page = porto_members_page_id() ) ) {
			return array( $archive_page, 'post' );
		} elseif ( function_exists( 'is_porto_faqs_page' ) && is_porto_faqs_page() && ( $archive_page = porto_faqs_page_id() ) ) {
			return array( $archive_page, 'post' );
		} elseif ( function_exists( 'is_porto_events_page' ) && is_porto_events_page() && ( $archive_page = porto_events_page_id() ) ) {
			return array( $archive_page, 'post' );
		} else {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				return array( $term->term_id, 'term' );
			}
		}
	} elseif ( is_singular() ) {
		return array( get_the_ID(), 'post' );
	} elseif ( is_home() ) {
		$blog_id = get_option( 'page_for_posts' );
		if ( $blog_id ) {
			return array( $blog_id, 'post' );
		}
	}
	return false;
}

function porto_get_meta_value( $meta_key, $boolean = false ) {
	global $wp_query, $porto_settings;
	$value = '';
	if ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		if ( $cat ) {
			$value = get_metadata( 'category', $cat->term_id, $meta_key, true );
		}
	} elseif ( is_archive() ) {
		if ( function_exists( 'porto_is_shop' ) && porto_is_shop() && ! is_product_category() ) {
			$value = get_post_meta( wc_get_page_id( 'shop' ), $meta_key, true );
		} elseif ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() && ( $archive_page = porto_portfolios_page_id() ) ) {
			$value = get_post_meta( $archive_page, $meta_key, true );
		} elseif ( function_exists( 'is_porto_members_page' ) && is_porto_members_page() && ( $archive_page = porto_members_page_id() ) ) {
			$value = get_post_meta( $archive_page, $meta_key, true );
		} elseif ( function_exists( 'is_porto_faqs_page' ) && is_porto_faqs_page() && ( $archive_page = porto_faqs_page_id() ) ) {
			$value = get_post_meta( $archive_page, $meta_key, true );
		} elseif ( function_exists( 'is_porto_events_page' ) && is_porto_events_page() && ( $archive_page = porto_events_page_id() ) ) {
			$value = get_post_meta( $archive_page, $meta_key, true );
		} else {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				$value = get_metadata( $term->taxonomy, $term->term_id, $meta_key, true );
			}
		}
	} else {
		if ( is_singular() ) {
			$value = get_post_meta( get_the_id(), $meta_key, true );
		} else {
			if ( ! is_home() && is_front_page() ) {
				if ( isset( $porto_settings[ $meta_key ] ) ) {
					$value = $porto_settings[ $meta_key ];
				}
			} elseif ( is_home() && ! is_front_page() ) {
				$blog_id = get_option( 'page_for_posts' );
				if ( $blog_id ) {
					$value = get_post_meta( $blog_id, $meta_key, true );
				}
				if ( ( ! $blog_id || ! $value ) && isset( $porto_settings[ 'blog-' . $meta_key ] ) ) {
					$value = $porto_settings[ 'blog-' . $meta_key ];
				}
			} elseif ( is_home() || is_front_page() ) {
				if ( isset( $porto_settings[ $meta_key ] ) ) {
					$value = $porto_settings[ $meta_key ];
				}
			}
		}
	}
	if ( $boolean ) {
		$value = ( $value != $meta_key ) ? true : false;
	}
	return apply_filters( 'porto_get_meta_value_' . $meta_key, $value );
}
function porto_meta_use_default() {
	global $wp_query;
	$value = '';
	if ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		if ( $cat ) {
			$value = get_metadata( 'category', $cat->term_id, 'default', true );
		}
	} elseif ( is_archive() ) {
		if ( function_exists( 'is_shop' ) && is_shop() && ! is_product_category() ) {
			$value = get_post_meta( wc_get_page_id( 'shop' ), 'default', true );
		} elseif ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() && ( $archive_page = porto_portfolios_page_id() ) ) {
			$value = get_post_meta( $archive_page, 'default', true );
		} elseif ( function_exists( 'is_porto_members_page' ) && is_porto_members_page() && ( $archive_page = porto_members_page_id() ) ) {
			$value = get_post_meta( $archive_page, 'default', true );
		} elseif ( function_exists( 'is_porto_faqs_page' ) && is_porto_faqs_page() && ( $archive_page = porto_faqs_page_id() ) ) {
			$value = get_post_meta( $archive_page, 'default', true );
		} elseif ( function_exists( 'is_porto_events_page' ) && is_porto_events_page() && ( $archive_page = porto_events_page_id() ) ) {
			$value = get_post_meta( $archive_page, 'default', true );
		} else {
			$term = get_queried_object();
			if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
				$value = get_metadata( $term->taxonomy, $term->term_id, 'default', true );
			}
		}
	} else {
		if ( is_singular() ) {
			$value = get_post_meta( get_the_id(), 'default', true );
		}
	}
	return apply_filters( 'porto_meta_use_default', ( 'default' != $value ) ? true : false );
}
function porto_meta_layout() {
	global $wp_query, $porto_settings;
	$value    = isset( $porto_settings['layout'] ) ? $porto_settings['layout'] : $porto_settings['layout'];
	$sidebar  = $porto_settings['sidebar'];
	$sidebar2 = '';
	$default  = porto_meta_use_default();
	if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) {
		$value    = $porto_settings['bb-layout'];
		$sidebar  = $porto_settings['bb-sidebar'];
		$sidebar2 = isset( $porto_settings['bb-sidebar2'] ) ? $porto_settings['bb-sidebar2'] : '';
	} elseif ( is_404() ) {
		$value = 'fullwidth';
	} elseif ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		if ( $default ) {
			$value   = $porto_settings['post-archive-layout'];
			$sidebar = 'blog-sidebar';
		} else {
			if ( $cat ) {
				$value    = get_metadata( 'category', $cat->term_id, 'layout', true );
				$sidebar  = get_metadata( 'category', $cat->term_id, 'sidebar', true );
				$sidebar2 = get_metadata( 'category', $cat->term_id, 'sidebar2', true );
			}
		}
	} elseif ( is_archive() || is_search() ) {
		if ( function_exists( 'porto_is_shop' ) && porto_is_shop() && ! is_product_category() ) {
			if ( $default ) {
				$value    = $porto_settings['product-archive-layout'];
				$sidebar  = 'woo-category-sidebar';
				$sidebar2 = isset( $porto_settings['product-archive-sidebar2'] ) ? $porto_settings['product-archive-sidebar2'] : '';
			} else {
				$value    = get_post_meta( wc_get_page_id( 'shop' ), 'layout', true );
				$sidebar  = get_post_meta( wc_get_page_id( 'shop' ), 'sidebar', true );
				$sidebar2 = get_post_meta( wc_get_page_id( 'shop' ), 'sidebar2', true );
			}
		} elseif ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() && ( $archive_page = porto_portfolios_page_id() ) ) {
			if ( $default ) {
				$value = $porto_settings['portfolio-archive-layout'];
			} else {
				$value = get_post_meta( $archive_page, 'layout', true );
			}
			$sidebar = $porto_settings['portfolio-archive-sidebar'];
		} elseif ( function_exists( 'is_porto_members_page' ) && is_porto_members_page() && ( $archive_page = porto_members_page_id() ) ) {
			if ( $default ) {
				$value = $porto_settings['member-archive-layout'];
			} else {
				$value = get_post_meta( $archive_page, 'layout', true );
			}
			$sidebar  = $porto_settings['member-archive-sidebar'];
			$sidebar2 = isset( $porto_settings['member-archive-sidebar2'] ) ? $porto_settings['member-archive-sidebar2'] : '';
		} elseif ( function_exists( 'is_porto_faqs_page' ) && is_porto_faqs_page() && ( $archive_page = porto_faqs_page_id() ) ) {
			if ( $default ) {
				$value = $porto_settings['faq-archive-layout'];
			} else {
				$value = get_post_meta( $archive_page, 'layout', true );
			}
			$sidebar  = $porto_settings['faq-archive-sidebar'];
			$sidebar2 = isset( $porto_settings['faq-archive-sidebar2'] ) ? $porto_settings['faq-archive-sidebar2'] : '';
		} elseif ( function_exists( 'is_porto_events_page' ) && is_porto_events_page() && ( $archive_page = is_porto_events_page() ) ) {
			if ( $default ) {
				$value = isset( $porto_settings['event-archive-layout'] ) ? $porto_settings['event-archive-layout'] : 'list';
			} else {
				$value = get_post_meta( $archive_page, 'layout', true );
			}
		} elseif ( $ptu_post_type = porto_has_ptu_archive_layout() ) {
			$value    = $porto_settings[ $ptu_post_type . '-ptu-archive-layout' ];
			$sidebar  = isset( $porto_settings[ $ptu_post_type . '-ptu-archive-sidebar' ] ) ? $porto_settings[ $ptu_post_type . '-ptu-archive-sidebar' ] : '';
			$sidebar2 = isset( $porto_settings[ $ptu_post_type . '-ptu-archive-sidebar2' ] ) ? $porto_settings[ $ptu_post_type . '-ptu-archive-sidebar2' ] : '';
		} else {
			if ( is_post_type_archive( 'portfolio' ) ) {
				$value    = $porto_settings['portfolio-archive-layout'];
				$sidebar  = $porto_settings['portfolio-archive-sidebar'];
				$sidebar2 = isset( $porto_settings['portfolio-archive-sidebar2'] ) ? $porto_settings['portfolio-archive-sidebar2'] : '';
			} elseif ( is_post_type_archive( 'member' ) ) {
				$value    = $porto_settings['member-archive-layout'];
				$sidebar  = $porto_settings['member-archive-sidebar'];
				$sidebar2 = isset( $porto_settings['member-archive-sidebar2'] ) ? $porto_settings['member-archive-sidebar2'] : '';
			} elseif ( is_post_type_archive( 'faq' ) ) {
				$value    = $porto_settings['faq-archive-layout'];
				$sidebar  = $porto_settings['faq-archive-sidebar'];
				$sidebar2 = isset( $porto_settings['faq-archive-sidebar2'] ) ? $porto_settings['faq-archive-sidebar2'] : '';
			} elseif ( is_post_type_archive( 'event' ) ) {
				$value = isset( $porto_settings['event-archive-layout'] ) ? $porto_settings['event-archive-layout'] : 'list';
			} else {
				$term = get_queried_object();
				if ( $term && isset( $term->taxonomy ) && isset( $term->term_id ) ) {
					if ( $default ) {
						switch ( $term->taxonomy ) {
							case in_array( $term->taxonomy, porto_get_taxonomies( 'portfolio' ) ):
								$value    = $porto_settings['portfolio-archive-layout'];
								$sidebar  = $porto_settings['portfolio-archive-sidebar'];
								$sidebar2 = isset( $porto_settings['portfolio-archive-sidebar2'] ) ? $porto_settings['portfolio-archive-sidebar2'] : '';
								break;
							case in_array( $term->taxonomy, porto_get_taxonomies( 'product' ) ):
							case 'product_cat':
								$value    = $porto_settings['product-archive-layout'];
								$sidebar  = 'woo-category-sidebar';
								$sidebar2 = isset( $porto_settings['product-archive-sidebar2'] ) ? $porto_settings['product-archive-sidebar2'] : '';
								break;
							case in_array( $term->taxonomy, porto_get_taxonomies( 'member' ) ):
								$value    = $porto_settings['member-archive-layout'];
								$sidebar  = $porto_settings['member-archive-sidebar'];
								$sidebar2 = isset( $porto_settings['member-archive-sidebar2'] ) ? $porto_settings['member-archive-sidebar2'] : '';
								break;
							case in_array( $term->taxonomy, porto_get_taxonomies( 'faq' ) ):
								$value    = $porto_settings['faq-archive-layout'];
								$sidebar  = $porto_settings['faq-archive-sidebar'];
								$sidebar2 = isset( $porto_settings['faq-archive-sidebar2'] ) ? $porto_settings['faq-archive-sidebar2'] : '';
								break;
							case in_array( $term->taxonomy, porto_get_taxonomies( 'post' ) ):
								$value   = $porto_settings['post-archive-layout'];
								$sidebar = 'blog-sidebar';
								break;
							default:
								$value    = $porto_settings['layout'];
								$sidebar  = $porto_settings['sidebar'];
								$sidebar2 = '';
						}
					} else {
						$value    = get_metadata( $term->taxonomy, $term->term_id, 'layout', true );
						$sidebar  = get_metadata( $term->taxonomy, $term->term_id, 'sidebar', true );
						$sidebar2 = get_metadata( $term->taxonomy, $term->term_id, 'sidebar2', true );
					}
				} else /*if (is_tag())*/ {
					if ( ! ( function_exists( 'is_shop' ) && is_shop() ) ) {
						$value   = $porto_settings['post-archive-layout'];
						$sidebar = 'blog-sidebar';
					}
				}
			}
		}
	} else {
		if ( is_singular() ) {
			if ( $default ) {
				switch ( get_post_type() ) {
					case 'product':
						$value    = $porto_settings['product-single-layout'];
						$sidebar  = 'woo-product-sidebar';
						$sidebar2 = isset( $porto_settings['product-single-sidebar2'] ) ? $porto_settings['product-single-sidebar2'] : '';
						break;
					case 'portfolio':
						$value    = $porto_settings['portfolio-single-layout'];
						$sidebar  = $porto_settings['portfolio-single-sidebar'];
						$sidebar2 = isset( $porto_settings['portfolio-single-sidebar2'] ) ? $porto_settings['portfolio-single-sidebar2'] : '';
						break;
					case 'member':
						$value    = $porto_settings['member-single-layout'];
						$sidebar  = $porto_settings['member-single-sidebar'];
						$sidebar2 = isset( $porto_settings['member-single-sidebar2'] ) ? $porto_settings['member-single-sidebar2'] : '';

						break;
					case 'post':
						$value   = $porto_settings['post-single-layout'];
						$sidebar = 'blog-sidebar';
						break;
					default:
						if ( $ptu_post_type = porto_has_ptu_single_layout() ) {
							$value    = $porto_settings[ $ptu_post_type . '-ptu-single-layout' ];
							$sidebar  = isset( $porto_settings[ $ptu_post_type . '-ptu-single-sidebar' ] ) ? $porto_settings[ $ptu_post_type . '-ptu-single-sidebar' ] : '';
							$sidebar2 = isset( $porto_settings[ $ptu_post_type . '-ptu-single-sidebar2' ] ) ? $porto_settings[ $ptu_post_type . '-ptu-single-sidebar2' ] : '';
						} else {
							$value    = $porto_settings['layout'];
							$sidebar  = $porto_settings['sidebar'];
							$sidebar2 = '';
						}
				}
			} else {
				$value    = get_post_meta( get_the_ID(), 'layout', true );
				$sidebar  = get_post_meta( get_the_ID(), 'sidebar', true );
				$sidebar2 = get_post_meta( get_the_ID(), 'sidebar2', true );
			}
		} else {
			if ( is_home() ) {
				$value = $porto_settings['post-archive-layout'];
			} elseif ( is_front_page() ) {
				$value = $porto_settings['layout'];
			}
			$sidebar = 'blog-sidebar';
		}
	}

	if ( empty( $sidebar2 ) ) {
		$sidebar2 = empty( $porto_settings['sidebar2'] ) ? 'secondary-sidebar' : $porto_settings['sidebar2'];
	}
	if ( ! in_array( $value, porto_options_sidebars() ) ) {
		$sidebar  = '';
		$sidebar2 = '';
	} elseif ( ! in_array( $value, porto_options_both_sidebars() ) ) {
		$sidebar2 = '';
	}

	$have_sidebar_menu = porto_have_sidebar_menu();
	if ( 'both-sidebar' == $value || 'wide-both-sidebar' == $value ) {
		if ( ! ( ( $sidebar && is_registered_sidebar( $sidebar ) && is_active_sidebar( $sidebar ) ) || $have_sidebar_menu ) ) {
			$value   = str_replace( 'both-sidebar', 'right-sidebar', $value );
			$sidebar = $sidebar2;
		}
		if ( ! ( ( $sidebar2 && is_registered_sidebar( $sidebar2 ) && is_active_sidebar( $sidebar2 ) ) || $have_sidebar_menu ) ) {
			$value = str_replace( 'both-sidebar', 'left-sidebar', $value );
		}
	}
	if ( ( 'left-sidebar' == $value || 'right-sidebar' == $value ) && ! ( ( $sidebar && is_registered_sidebar( $sidebar ) && is_active_sidebar( $sidebar ) ) || $have_sidebar_menu ) ) {
		$value = 'fullwidth';
	}
	if ( ( 'wide-left-sidebar' == $value || 'wide-right-sidebar' == $value ) && ! ( ( $sidebar && is_registered_sidebar( $sidebar ) && is_active_sidebar( $sidebar ) ) || $have_sidebar_menu ) ) {
		$value = 'widewidth';
	}
	return apply_filters( 'porto_meta_layout', array( $value, $sidebar, $sidebar2 ) );
}
function porto_meta_default_layout() {
	global $porto_settings;
	$value = isset( $porto_settings['layout'] ) ? $porto_settings['layout'] : $porto_settings['layout'];
	if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) {
		$value = $porto_settings['bb-layout'];
	} elseif ( is_404() ) {
		$value = 'fullwidth';
	} elseif ( is_category() ) {
		$value = $porto_settings['post-archive-layout'];
	} elseif ( is_archive() ) {
		if ( function_exists( 'is_shop' ) && is_shop() && ! is_product_category() ) {
			$value = $porto_settings['product-archive-layout'];
		} elseif ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() ) {
			$value = $porto_settings['portfolio-archive-layout'];
		} elseif ( function_exists( 'is_porto_members_page' ) && is_porto_members_page() ) {
			$value = $porto_settings['member-archive-layout'];
		} elseif ( function_exists( 'is_porto_faqs_page' ) && is_porto_faqs_page() ) {
			$value = $porto_settings['faq-archive-layout'];
		} elseif ( function_exists( 'is_porto_events_page' ) && is_porto_events_page() ) {
			$value = isset( $porto_settings['event-archive-layout'] ) ? $porto_settings['event-archive-layout'] : 'list';
		} else {
			if ( is_post_type_archive( 'portfolio' ) ) {
				$value = $porto_settings['portfolio-archive-layout'];
			} elseif ( is_post_type_archive( 'member' ) ) {
				$value = $porto_settings['member-archive-layout'];
			} elseif ( is_post_type_archive( 'faq' ) ) {
				$value = $porto_settings['faq-archive-layout'];
			} elseif ( is_post_type_archive( 'event' ) ) {
				$value = isset( $porto_settings['event-archive-layout'] ) ? $porto_settings['event-archive-layout'] : 'list';
			} else {
				$term = get_queried_object();
				if ( $term && isset( $term->taxonomy ) ) {
					switch ( $term->taxonomy ) {
						case in_array( $term->taxonomy, porto_get_taxonomies( 'portfolio' ) ):
							$value = $porto_settings['portfolio-archive-layout'];
							break;
						case in_array( $term->taxonomy, porto_get_taxonomies( 'product' ) ):
							$value = $porto_settings['product-archive-layout'];
							break;
						case 'product_cat':
							$value = $porto_settings['product-archive-layout'];
							break;
						case in_array( $term->taxonomy, porto_get_taxonomies( 'member' ) ):
							$value = $porto_settings['member-archive-layout'];
							break;
						case in_array( $term->taxonomy, porto_get_taxonomies( 'faq' ) ):
							$value = $porto_settings['faq-archive-layout'];
							break;
						case in_array( $term->taxonomy, porto_get_taxonomies( 'post' ) ):
							$value = $porto_settings['post-archive-layout'];
							break;
						default:
							$value = $porto_settings['layout'];
					}
				} else /*if (is_tag())*/ {
					$value = $porto_settings['post-archive-layout'];
				}
			}
		}
	} else {
		if ( is_singular() ) {
			switch ( get_post_type() ) {
				case 'product':
					$value = $porto_settings['product-single-layout'];
					break;
				case 'portfolio':
					$value = $porto_settings['portfolio-single-layout'];
					break;
				case 'member':
					$value = $porto_settings['member-single-layout'];
					break;
				case 'post':
					$value = $porto_settings['post-single-layout'];
					break;
				default:
					$value = $porto_settings['layout'];
			}
		} else {
			if ( is_home() ) {
				$value = $porto_settings['post-archive-layout'];
			} elseif ( is_front_page() ) {
				$value = $porto_settings['layout'];
			}
		}
	}
	return apply_filters( 'porto_meta_default_layout', $value );
}

function porto_meta_sticky_sidebar() {
	global $porto_settings;
	$value   = $porto_settings['sticky-sidebar'];
	$default = porto_get_meta_value( 'sticky_sidebar' );
	if ( 'yes' == $default ) {
		return true;
	}
	if ( 'no' == $default ) {
		return false;
	}
	if ( is_404() ) {
		$value = false;
	} elseif ( $value && is_singular( 'product' ) ) {
		$builder_id = porto_check_builder_condition( 'product' );
		if ( $builder_id && get_post_meta( $builder_id, 'disable_sticky_sidebar', true ) ) {
			$value = false;
		}
	}
	return apply_filters( 'porto_meta_sticky_sidebar', $value );
}
function porto_get_taxonomies( $content_type ) {
	$args       = array(
		'object_type' => array( $content_type ),
	);
	$output     = 'names'; // or objects
	$operator   = 'and'; // 'and' or 'or'
	$taxonomies = get_taxonomies( $args, $output, $operator );
	return $taxonomies;
}
function porto_portfolio_sub_title( $post = null ) {
	if ( ! $post ) {
		$post = $GLOBALS['post'];
	}
	$output = '';
	global $porto_settings;
	if ( $post && isset( $porto_settings['portfolio-subtitle'] ) ) {
		switch ( $porto_settings['portfolio-subtitle'] ) {
			case 'like':
				$output .= porto_portfolio_like();
				break;
			case 'date':
				$output .= get_the_date( '', $post );
				break;
			case 'cats':
				$terms = get_the_terms( $post->ID, 'portfolio_cat' );
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$links = array();
					foreach ( $terms as $term ) {
						$links[] = $term->name;
					}
					$output .= join( ', ', $links );
				}
				break;
			case 'skills':
				$terms = get_the_terms( $post->ID, 'portfolio_skills' );
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$links = array();
					foreach ( $terms as $term ) {
						$links[] = $term->name;
					}
					$output .= join( ', ', $links );
				}
				break;
			case 'location':
				$output .= get_post_meta( $post->ID, 'portfolio_location', true );
				break;
			case 'client_name':
				$output .= get_post_meta( $post->ID, 'portfolio_client', true );
				break;
			case 'client_link':
				$output .= get_post_meta( $post->ID, 'portfolio_client_link', true );
				break;
			case 'author_name':
				$output .= get_post_meta( $post->ID, 'portfolio_author_name', true );
				break;
			case 'author_role':
				$output .= get_post_meta( $post->ID, 'portfolio_author_role', true );
				break;
			case 'excerpt':
				if ( has_excerpt( $post->ID ) ) {
					$output .= get_the_excerpt( $post->ID );
				}
				break;
		}
	}
	return apply_filters( 'porto_portfolio_sub_title', $output );
}
