<?php

/**
 * SEO plugins integration
 */
class Permalink_Manager_SEO_Plugins {

	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ), 99 );
	}

	/**
	 * Add support for SEO plugins using their hooks
	 */
	function init_hooks() {
		// Yoast SEO
		add_filter( 'wpseo_xml_sitemap_post_url', array( $this, 'yoast_fix_sitemap_urls' ), 9 );
		if ( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '14.0', '>=' ) ) {
			add_action( 'permalink_manager_updated_post_uri', array( $this, 'yoast_update_indexable_permalink' ), 10, 3 );
			add_action( 'permalink_manager_updated_term_uri', array( $this, 'yoast_update_indexable_permalink' ), 10, 3 );
			add_filter( 'wpseo_canonical', array( $this, 'yoast_fix_canonical' ), 10 );
			add_filter( 'wpseo_opengraph_url', array( $this, 'yoast_fix_canonical' ), 10 );
			add_filter( 'wpseo_dynamic_permalinks_enabled', '__return_true', 5 );
		}

		// Breadcrumbs
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'rank_math/frontend/breadcrumb/items', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'seopress_pro_breadcrumbs_crumbs', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'slim_seo_breadcrumbs_links', array( $this, 'filter_breadcrumbs' ), 9 );
		// add_filter( 'aioseo_breadcrumbs_trail', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'avia_breadcrumbs_trail', array( $this, 'filter_breadcrumbs' ), 100 );
	}

	/**
	 * Get the HTTP protocol of the home URL and use it in Yoast SEO sitemap permalinks
	 *
	 * @param string $permalink The permalink in the sitemap
	 *
	 * @return string The sitemap's permalink
	 */
	function yoast_fix_sitemap_urls( $permalink ) {
		if ( class_exists( 'WPSEO_Utils' ) ) {
			$home_url      = WPSEO_Utils::home_url();
			$home_protocol = parse_url( $home_url, PHP_URL_SCHEME );

			$permalink = preg_replace( "/^http(s)?/", $home_protocol, $permalink );
		}

		return $permalink;
	}

	/**
	 * Update the permalink in the Yoast SEO indexable table when the permalink is changed
	 *
	 * @param int $element_id The ID of the post/term element that was updated.
	 * @param string $new_uri The new URI of the element.
	 * @param string $old_uri The old URI of the element.
	 */
	function yoast_update_indexable_permalink( $element_id, $new_uri, $old_uri ) {
		global $wpdb;

		if ( ! empty( $new_uri ) && ! empty( $old_uri ) && $new_uri !== $old_uri ) {
			if ( current_filter() == 'permalink_manager_updated_term_uri' ) {
				$permalink   = get_term_link( (int) $element_id );
				$object_type = 'term';
			} else {
				$permalink   = get_permalink( $element_id );
				$object_type = 'post';
			}

			if ( ! empty( $permalink ) ) {
				$permalink_hash = strlen( $permalink ) . ':' . md5( $permalink );
				$wpdb->update( "{$wpdb->prefix}yoast_indexable", array( 'permalink' => $permalink, 'permalink_hash' => $permalink_hash ), array( 'object_id' => $element_id, 'object_type' => $object_type ), array( '%s', '%s' ), array( '%d', '%s' ) );
			}
		}
	}

	/**
	 * Filter the canonical permalink used by SEO using 'wpseo_canonical' & 'wpseo_opengraph_url' hooks
	 *
	 * @param string $url The canonical URL that Yoast SEO has generated.
	 *
	 * @return string the URL.
	 */
	function yoast_fix_canonical( $url ) {
		global $pm_query, $wp_rewrite;

		if ( ! empty( $pm_query['id'] ) ) {
			$element = get_queried_object();

			if ( ! empty( $element->ID ) && ! empty( $element->post_type ) ) {
				$new_url = get_permalink( $element->ID );

				// Do not filter if custom canonical URL is set
				$yoast_canonical_url = get_post_meta( $element->ID, '_yoast_wpseo_canonical', true );
				if ( ! empty( $yoast_canonical_url ) ) {
					return $url;
				}

				if ( is_home() ) {
					$paged   = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
					$new_url = ( $paged > 1 ) ? sprintf( '%s/%s/%d', trim( $new_url, '/' ), $wp_rewrite->pagination_base, $paged ) : $new_url;
				} else {
					$paged   = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
					$new_url = ( $paged > 1 ) ? sprintf( '%s/%d', trim( $new_url, '/' ), $paged ) : $new_url;
				}
			} else if ( ! empty( $element->taxonomy ) && ! empty( $element->term_id ) ) {
				$new_url = get_term_link( $element, $element->taxonomy );

				// Do not filter if custom canonical URL is set
				if ( class_exists( 'WPSEO_Taxonomy_Meta' ) ) {
					$yoast_canonical_url = WPSEO_Taxonomy_Meta::get_term_meta( $element, $element->taxonomy, 'canonical' );
					if ( ! empty( $yoast_canonical_url ) ) {
						return $url;
					}
				}

				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				if ( $paged > 1 ) {
					$new_url = sprintf( '%s/%s/%d', trim( $new_url, '/' ), $wp_rewrite->pagination_base, $paged );
				}
			}

			$url = ( ! empty( $new_url ) ) ? $new_url : $url;
			$url = Permalink_Manager_Core_Functions::control_trailing_slashes( $url );
		}

		return $url;
	}

	/**
	 * Filter the breadcrumbs array to match the structure of currently requested URL
	 *
	 * @param array $links The current breadcrumb links.
	 *
	 * @return array The $links array.
	 */
	function filter_breadcrumbs( $links ) {
		// Get post type permastructure settings
		global $permalink_manager_uris, $permalink_manager_options, $post, $wpdb, $wp, $wp_current_filter;

		// Check if the filter should be activated
		if ( empty( $permalink_manager_options['general']['yoast_breadcrumbs'] ) || empty( $permalink_manager_uris ) ) {
			return $links;
		}

		// Get current post/page/term (if available)
		$queried_element = get_queried_object();
		if ( ! empty( $queried_element->ID ) ) {
			$element_id = $queried_element->ID;
		} else if ( ! empty( $queried_element->term_id ) ) {
			$element_id = "tax-{$queried_element->term_id}";
		} else if ( defined( 'REST_REQUEST' ) && ! empty( $post->ID ) ) {
			$element_id = $post->ID;
		}

		// Get the custom permalink (if available) or the current request URL (if unavailable)
		if ( ! empty( $element_id ) && ! empty( $permalink_manager_uris[ $element_id ] ) ) {
			$custom_uri = preg_replace( "/([^\/]+)$/", '', $permalink_manager_uris[ $element_id ] );
		} else {
			$custom_uri = trim( preg_replace( "/([^\/]+)$/", '', $wp->request ), "/" );
		}

		$all_uris                     = array_flip( $permalink_manager_uris );
		$custom_uri_parts             = explode( '/', trim( $custom_uri ) );
		$breadcrumbs                  = array();
		$snowball                     = '';
		$available_taxonomies         = Permalink_Manager_Helper_Functions::get_taxonomies_array( null, null, true );
		$available_post_types         = Permalink_Manager_Helper_Functions::get_post_types_array( null, null, true );
		$available_post_types_archive = Permalink_Manager_Helper_Functions::get_post_types_array( 'archive_slug', null, true );
		$current_filter               = end( $wp_current_filter );

		// Get Yoast Meta (the breadcrumbs titles can be changed in Yoast metabox)
		$yoast_meta_terms = get_option( 'wpseo_taxonomy_meta' );

		// Check what array keys should be used for breadcrumbs ("All In One SEO" uses a more complicated schema)
		if ( $current_filter == 'aioseo_breadcrumbs_trail' ) {
			$breadcrumb_key_text = 'label';
			$breadcrumb_key_url  = 'link';
			$is_aioseo           = true;
		} else if ( in_array( $current_filter, array( 'wpseo_breadcrumb_links', 'slim_seo_breadcrumbs_links' ) ) ) {
			$breadcrumb_key_text = 'text';
			$breadcrumb_key_url  = 'url';
			$is_aioseo           = false;
		} else {
			$breadcrumb_key_text = 0;
			$breadcrumb_key_url  = 1;
			$is_aioseo           = false;
		}

		// Get internal breadcrumb elements
		foreach ( $custom_uri_parts as $slug ) {
			if ( empty( $slug ) ) {
				continue;
			}

			$snowball = ( empty( $snowball ) ) ? $slug : "{$snowball}/{$slug}";

			// 1A. Try to match any custom URI
			$uri     = trim( $snowball, "/" );
			$element = ( ! empty( $all_uris[ $uri ] ) ) ? $all_uris[ $uri ] : false;

			if ( ! empty( $element ) && strpos( $element, 'tax-' ) !== false ) {
				$element_id = intval( preg_replace( "/[^0-9]/", "", $element ) );
				$element    = get_term( $element_id );
			} else if ( is_numeric( $element ) ) {
				$element = get_post( $element );
			}

			// 1B. Try to get term
			if ( empty( $element ) && ! empty( $available_taxonomies ) ) {
				$sql = sprintf( "SELECT t.term_id, t.name, tt.taxonomy FROM {$wpdb->terms} AS t LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE slug = '%s' AND tt.taxonomy IN ('%s') LIMIT 1", esc_sql( $slug ), implode( "','", array_keys( $available_taxonomies ) ) );

				$element = $wpdb->get_row( $sql );
			}

			// 1C. Try to get page/post
			if ( empty( $element ) && ! empty( $available_post_types ) ) {
				$sql = sprintf( "SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_name = '%s' AND post_status = 'publish' AND post_type IN ('%s') AND post_type != 'attachment' LIMIT 1", esc_sql( $slug ), implode( "','", array_keys( $available_post_types ) ) );

				$element = $wpdb->get_row( $sql );
			}

			// 1D. Try to get post type archive
			if ( empty( $element ) && ! empty( $available_post_types_archive ) && in_array( $snowball, $available_post_types_archive ) ) {
				$post_type_slug = array_search( $snowball, $available_post_types_archive );
				$element        = get_post_type_object( $post_type_slug );
			}

			// 2A. When the term is found, we can add it to the breadcrumbs
			if ( ! empty( $element->term_id ) ) {
				$term_id = apply_filters( 'wpml_object_id', $element->term_id, $element->taxonomy, true );
				$term    = ( ( $element->term_id !== $term_id ) || $is_aioseo ) ? get_term( $term_id ) : $element;

				// Alternative title
				if ( $current_filter == 'wpseo_breadcrumb_links' ) {
					$alt_title = ( ! empty( $yoast_meta_terms[ $term->taxonomy ][ $term->term_id ]['wpseo_bctitle'] ) ) ? $yoast_meta_terms[ $term->taxonomy ][ $term->term_id ]['wpseo_bctitle'] : '';
				} else if ( $current_filter == 'seopress_pro_breadcrumbs_crumbs' ) {
					$alt_title = get_term_meta( $term->term_id, '_seopress_robots_breadcrumbs', true );
				} else if ( $current_filter == 'rank_math/frontend/breadcrumb/items' ) {
					$alt_title = get_term_meta( $term->term_id, 'rank_math_breadcrumb_title', true );
				}

				$title = ( ! empty( $alt_title ) ) ? $alt_title : $term->name;

				if ( $is_aioseo ) {
					$breadcrumbs[] = array(
						$breadcrumb_key_text => wp_strip_all_tags( $title ),
						$breadcrumb_key_url  => get_term_link( (int) $term->term_id, $term->taxonomy ),
						'type'               => 'taxonomy',
						'subType'            => 'parent',
						'reference'          => $term,
					);
				} else {
					$breadcrumbs[] = array(
						$breadcrumb_key_text => wp_strip_all_tags( $title ),
						$breadcrumb_key_url  => get_term_link( (int) $term->term_id, $term->taxonomy )
					);
				}
			} // 2B. When the post/page is found, we can add it to the breadcrumbs
			else if ( ! empty( $element->ID ) ) {
				$page_id = apply_filters( 'wpml_object_id', $element->ID, $element->post_type, true );
				$page    = ( ( $element->ID !== $page_id ) || $is_aioseo ) ? get_post( $page_id ) : $element;

				// Alternative title
				if ( $current_filter == 'wpseo_breadcrumb_links' ) {
					$alt_title = get_post_meta( $page->ID, '_yoast_wpseo_bctitle', true );
				} else if ( $current_filter == 'seopress_pro_breadcrumbs_crumbs' ) {
					$alt_title = get_post_meta( $page->ID, '_seopress_robots_breadcrumbs', true );
				} else if ( $current_filter == 'rank_math/frontend/breadcrumb/items' ) {
					$alt_title = get_post_meta( $page->ID, 'rank_math_breadcrumb_title', true );
				}

				$title = ( ! empty( $alt_title ) ) ? $alt_title : $page->post_title;

				if ( $is_aioseo ) {
					$breadcrumbs[] = array(
						$breadcrumb_key_text => wp_strip_all_tags( $title ),
						$breadcrumb_key_url  => get_permalink( $page->ID ),
						'type'               => 'single',
						'subType'            => '',
						'reference'          => $page
					);
				} else {
					$breadcrumbs[] = array(
						$breadcrumb_key_text => wp_strip_all_tags( $title ),
						$breadcrumb_key_url  => get_permalink( $page->ID )
					);
				}
			} // 2C. When the post archive is found, we can add it to the breadcrumbs
			else if ( ! empty( $element->rewrite ) && ( ! empty( $element->labels->name ) ) ) {
				if ( $is_aioseo ) {
					$breadcrumbs[] = array(
						$breadcrumb_key_text => apply_filters( 'post_type_archive_title', $element->labels->name, $element->name ),
						$breadcrumb_key_url  => get_post_type_archive_link( $element->name ),
						'type'               => 'postTypeArchive',
						'subType'            => '',
						'reference'          => $element
					);
				} else {
					$breadcrumbs[] = array(
						$breadcrumb_key_text => apply_filters( 'post_type_archive_title', $element->labels->name, $element->name ),
						$breadcrumb_key_url  => get_post_type_archive_link( $element->name )
					);
				}
			}
		}

		// Add new links to current breadcrumbs array
		if ( ! empty( $links ) && is_array( $links ) ) {
			$first_element  = reset( $links );
			$last_element   = end( $links );
			$b_last_element = prev( $links );
			$breadcrumbs    = ( ! empty( $breadcrumbs ) ) ? $breadcrumbs : array();

			// Support RankMath/SEOPress/WooCommerce/Slim SEO/AIOSEO breadcrumbs
			if ( in_array( $current_filter, array( 'wpseo_breadcrumb_links', 'rank_math/frontend/breadcrumb/items', 'seopress_pro_breadcrumbs_crumbs', 'woocommerce_get_breadcrumb', 'slim_seo_breadcrumbs_links', 'aioseo_breadcrumbs_trail' ) ) ) {
				if ( $current_filter == 'slim_seo_breadcrumbs_links' ) {
					$links = array_merge( array( $first_element ), $breadcrumbs );
				} // Append the element before the last element if the last breadcrumb does not have a URL set (e.g. if the /page/ endpoint is used)
				else if ( ! in_array( $current_filter, array( 'aioseo_breadcrumbs_trail', 'slim_seo_breadcrumbs_links' ) ) && ! empty( $wp->query_vars['paged'] ) && $wp->query_vars['paged'] > 1 && ! empty( $b_last_element[ $breadcrumb_key_url ] ) ) {
					$links = array_merge( array( $first_element ), $breadcrumbs, array( $b_last_element ), array( $last_element ) );
				} else {
					$links = array_merge( array( $first_element ), $breadcrumbs, array( $last_element ) );
				}
			} // Support Avia/Enfold breadcrumbs
			else if ( $current_filter == 'avia_breadcrumbs_trail' ) {
				foreach ( $breadcrumbs as &$breadcrumb ) {
					if ( isset( $breadcrumb[ $breadcrumb_key_text ] ) ) {
						$breadcrumb = sprintf( '<a href="%s" title="%2$s">%2$s</a>', esc_attr( $breadcrumb[ $breadcrumb_key_url ] ), esc_attr( $breadcrumb[ $breadcrumb_key_text ] ) );
					}
				}

				$links = array_merge( array( $first_element ), $breadcrumbs, array( 'trail_end' => $last_element ) );
			}
		}

		return array_filter( $links );
	}
}