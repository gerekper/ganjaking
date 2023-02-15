<?php

/**
 * Third parties integration
 */
class Permalink_Manager_Third_Parties {

	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ), 99 );
		add_action( 'plugins_loaded', array( $this, 'init_early_hooks' ), 99 );
	}

	/**
	 * Add support for 3rd party plugins using their hooks
	 */
	function init_hooks() {
		global $permalink_manager_options;

		// 0. Stop redirect
		add_action( 'wp', array( $this, 'stop_redirect' ), 0 );

		// 1. AMP
		if ( defined( 'AMP_QUERY_VAR' ) ) {
			add_filter( 'permalink_manager_detect_uri', array( $this, 'detect_amp' ), 10, 2 );
			add_filter( 'request', array( $this, 'enable_amp' ), 10, 1 );
		}

		// 2. AMP for WP
		if ( defined( 'AMPFORWP_AMP_QUERY_VAR' ) ) {
			add_filter( 'permalink_manager_filter_query', array( $this, 'detect_amp_for_wp' ), 5 );
		}

		// 4. WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'permalink_manager_filter_query', array( $this, 'woocommerce_detect' ), 9, 5 );
			add_filter( 'template_redirect', array( $this, 'woocommerce_checkout_fix' ), 9 );

			if ( class_exists( 'Permalink_Manager_Pro_Functions' ) ) {
				if ( empty( $permalink_manager_options['general']['partial_disable']['post_types'] ) || ! in_array( 'shop_coupon', $permalink_manager_options['general']['partial_disable']['post_types'] ) ) {
					if ( is_admin() ) {
						add_filter( 'woocommerce_coupon_data_tabs', 'Permalink_Manager_Pro_Functions::woocommerce_coupon_tabs' );
						add_action( 'woocommerce_coupon_data_panels', 'Permalink_Manager_Pro_Functions::woocommerce_coupon_panel' );
						add_action( 'woocommerce_coupon_options_save', 'Permalink_Manager_Pro_Functions::woocommerce_save_coupon_uri', 9, 2 );
					}

					add_filter( 'request', 'Permalink_Manager_Pro_Functions::woocommerce_detect_coupon_code', 1, 1 );
					add_filter( 'permalink_manager_disabled_post_types', 'Permalink_Manager_Pro_Functions::woocommerce_coupon_uris', 9, 1 );
				}
			}

			// WooCommerce Import/Export
			add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'woocommerce_csv_custom_uri_column' ), 9 );
			add_filter( 'woocommerce_product_export_product_column_custom_uri', array( $this, 'woocommerce_export_custom_uri_value' ), 9, 3 );

			add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'woocommerce_csv_custom_uri_column' ), 9 );
			add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'woocommerce_csv_custom_uri_column' ), 9 );
			add_action( 'woocommerce_product_import_inserted_product_object', array( $this, 'woocommerce_csv_import_custom_uri' ), 9, 2 );

			add_action( 'woocommerce_product_duplicate', array( $this, 'woocommerce_generate_permalinks_after_duplicate' ), 9, 2 );
			add_filter( 'permalink_manager_filter_default_post_uri', array( $this, 'woocommerce_product_attributes' ), 5, 5 );

			if ( wp_doing_ajax() && class_exists( 'SitePress' ) ) {
				add_filter( 'permalink_manager_filter_final_post_permalink', array( $this, 'woocommerce_translate_ajax_fragments_urls' ), 9999, 3 );
			}
		}

		// 5. Theme My Login
		if ( class_exists( 'Theme_My_Login' ) ) {
			add_action( 'wp', array( $this, 'tml_ignore_custom_permalinks'), 10 );
		}

		// 6. Yoast SEO
		add_filter( 'wpseo_xml_sitemap_post_url', array( $this, 'yoast_fix_sitemap_urls' ), 9 );
		if ( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '14.0', '>=' ) ) {
			add_action( 'permalink_manager_updated_post_uri', array( $this, 'yoast_update_indexable_permalink' ), 10, 3 );
			add_action( 'permalink_manager_updated_term_uri', array( $this, 'yoast_update_indexable_permalink' ), 10, 3 );
			add_filter( 'wpseo_canonical', array( $this, 'yoast_fix_canonical' ), 10 );
			add_filter( 'wpseo_opengraph_url', array( $this, 'yoast_fix_canonical' ), 10 );
		}

		// 7. Breadcrumbs
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'rank_math/frontend/breadcrumb/items', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'seopress_pro_breadcrumbs_crumbs', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'slim_seo_breadcrumbs_links', array( $this, 'filter_breadcrumbs' ), 9 );
		// add_filter( 'aioseo_breadcrumbs_trail', array( $this, 'filter_breadcrumbs' ), 9 );
		add_filter( 'avia_breadcrumbs_trail', array( $this, 'filter_breadcrumbs' ), 100 );

		// 8. WooCommerce Wishlist Plugin
		if ( function_exists( 'tinv_get_option' ) ) {
			add_filter( 'permalink_manager_detect_uri', array( $this, 'ti_woocommerce_wishlist_uris' ), 15, 3 );
		}

		// 9. Revisionize
		if ( defined( 'REVISIONIZE_ROOT' ) ) {
			add_action( 'revisionize_after_create_revision', array( $this, 'revisionize_keep_post_uri' ), 9, 2 );
			add_action( 'revisionize_before_publish', array( $this, 'revisionize_clone_uri' ), 9, 2 );
		}

		// 10. WP All Import
		if ( class_exists( 'PMXI_Plugin' ) && ( ! empty( $permalink_manager_options['general']['pmxi_support'] ) ) ) {
			add_action( 'pmxi_extend_options_featured', array( $this, 'wpaiextra_uri_display' ), 9, 2 );
			add_filter( 'pmxi_options_options', array( $this, 'wpai_api_options' ) );
			add_filter( 'pmxi_addons', array( $this, 'wpai_api_register' ) );
			add_filter( 'wp_all_import_addon_parse', array( $this, 'wpai_api_parse' ) );
			add_filter( 'wp_all_import_addon_import', array( $this, 'wpai_api_import' ) );

			add_action( 'pmxi_saved_post', array( $this, 'wpai_save_redirects' ) );

			add_action( 'pmxi_after_xml_import', array( $this, 'wpai_schedule_regenerate_uris_after_xml_import' ), 10, 1 );
			add_action( 'wpai_regenerate_uris_after_import_event', array( $this, 'wpai_regenerate_uris_after_import' ), 10, 1 );
		}

		// 11. WP All Export
		if ( class_exists( 'PMXE_Plugin' ) && ( ! empty( $permalink_manager_options['general']['pmxi_support'] ) ) ) {
			add_filter( 'wp_all_export_available_sections', array( $this, 'wpae_custom_uri_section' ), 9 );
			add_filter( 'wp_all_export_available_data', array( $this, 'wpae_custom_uri_section_fields' ), 9 );
			add_filter( 'wp_all_export_csv_rows', array( $this, 'wpae_export_custom_uri' ), 10, 2 );
		}

		// 12. Duplicate Post
		if ( defined( 'DUPLICATE_POST_CURRENT_VERSION' ) ) {
			add_action( 'dp_duplicate_post', array( $this, 'duplicate_custom_uri' ), 100, 2 );
			add_action( 'dp_duplicate_page', array( $this, 'duplicate_custom_uri' ), 100, 2 );
		}

		// 13. My Listing by 27collective
		if ( class_exists( '\MyListing\Post_Types' ) ) {
			add_filter( 'permalink_manager_filter_default_post_uri', array( $this, 'ml_listing_custom_fields' ), 5, 5 );
			add_action( 'mylisting/submission/save-listing-data', array( $this, 'ml_set_listing_uri' ), 100 );
			add_filter( 'permalink_manager_filter_query', array( $this, 'ml_detect_archives' ), 1, 2 );
		}

		// 14. bbPress
		if ( class_exists( 'bbPress' ) && function_exists( 'bbp_get_edit_slug' ) ) {
			add_filter( 'permalink_manager_endpoints', array( $this, 'bbpress_endpoints' ), 9 );
			add_action( 'wp', array( $this, 'bbpress_detect_endpoints' ), 0 );
		}

		// 15. Dokan
		if ( class_exists( 'WeDevs_Dokan' ) ) {
			add_action( 'wp', array( $this, 'dokan_detect_endpoints' ), 999 );
			add_filter( 'permalink_manager_endpoints', array( $this, 'dokan_endpoints' ) );
		}

		// 16. GeoDirectory
		if ( class_exists( 'GeoDirectory' ) ) {
			add_filter( 'permalink_manager_filter_default_post_uri', array( $this, 'geodir_custom_fields' ), 5, 5 );
		}

		// 17. BasePress
		if ( class_exists( 'Basepress' ) ) {
			add_filter( 'permalink_manager_filter_query', array( $this, 'kb_adjust_query' ), 5, 5 );
		}

		// 18. Ultimate Member
		if ( class_exists( 'UM' ) && ! ( empty( $permalink_manager_options['general']['um_support'] ) ) ) {
			add_filter( 'permalink_manager_detect_uri', array( $this, 'um_detect_extra_pages' ), 20 );
		}

		// 19. WooCommerce Subscriptions
		if ( class_exists( 'WC_Subscriptions' ) ) {
			add_filter( 'permalink_manager_filter_final_post_permalink', array( $this, 'wcs_fix_subscription_links' ), 10, 3 );
		}

		// 20. LearnPress
		if ( class_exists( 'LearnPress' ) ) {
			add_filter( 'permalink_manager_excluded_post_ids', array( $this, 'learnpress_exclude_pages' ) );
		}
	}

	/**
	 * Some hooks must be called shortly after all the plugins are loaded
	 */
	public function init_early_hooks() {
		// WP Store Locator
		if ( class_exists( 'WPSL_CSV' ) ) {
			add_action( 'added_post_meta', array( $this, 'wpsl_regenerate_after_import' ), 10, 4 );
			add_action( 'updated_post_meta', array( $this, 'wpsl_regenerate_after_import' ), 10, 4 );
		}
		// Woocommerce
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_get_endpoint_url', array( 'Permalink_Manager_Core_Functions', 'control_trailing_slashes' ), 9 );
		}
	}

	/**
	 * 0. Stop canonical redirect if specific query variables are set
	 */
	public static function stop_redirect() {
		global $wp_query, $post;

		if ( ! empty( $wp_query->query ) ) {
			$query_vars = $wp_query->query;

			// WordPress Photo Seller Plugin
			if ( ! empty( $query_vars['image_id'] ) && ! empty( $query_vars['gallery_id'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // Ultimate Member
			else if ( ! empty( $query_vars['um_user'] ) || ! empty( $query_vars['um_tab'] ) || ( ! empty( $query_vars['provider'] ) && ! empty( $query_vars['state'] ) ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // Mailster
			else if ( ! empty( $query_vars['_mailster_page'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // WP Route
			else if ( ! empty( $query_vars['WP_Route'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // WooCommerce Wishlist
			else if ( ! empty( $query_vars['wishlist-action'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // UserPro
			else if ( ! empty( $query_vars['up_username'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // The Events Calendar
			else if ( ! empty( $query_vars['eventDisplay'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // Groundhogg
			else if ( class_exists( '\Groundhogg\Plugin' ) && ! empty( $query_vars['subpage'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // MyListing theme
			else if ( ! empty( $query_vars['explore_tab'] ) || ! empty( $query_vars['explore_region'] ) || ! empty( $_POST['submit_job'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // GeoDirectory
			else if ( function_exists( 'geodir_location_page_id' ) && ! empty( $post->ID ) && geodir_location_page_id() == $post->ID ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // RankMath Pro
			else if ( isset( $query_vars['schema-preview'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // Theme.co - Pro Theme
			else if ( ! empty( $_POST['_cs_nonce'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // Tutor LMS
			else if ( ! empty( $query_vars['tutor_dashboard_page'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // AMP
			else if ( function_exists( 'amp_get_slug' ) && array_key_exists( amp_get_slug(), $query_vars ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			} // LearnPress
			if ( ! empty( $query_vars['view'] ) && ! empty( $query_vars['page_id'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			}
		}

		// WPForo
		if ( defined( 'WPFORO_VERSION' ) ) {
			$forum_page_id = get_option( 'wpforo_pageid' );

			if ( ! empty( $forum_page_id ) && ! empty( $post->ID ) && $forum_page_id == $post->ID ) {
				$wp_query->query_vars['do_not_redirect'] = 1;
			}
		}
	}

	/**
	 * 1A. AMP hooks (support for older versions)
	 *
	 * @param array $uri_parts
	 * @param string $request_url
	 *
	 * @return array
	 */
	function detect_amp( $uri_parts, $request_url ) {
		global $amp_enabled;

		if ( defined( 'AMP_QUERY_VAR' ) ) {
			$amp_query_var = AMP_QUERY_VAR;

			// Check if AMP should be triggered
			preg_match( "/^(.+?)\/({$amp_query_var})?\/?$/i", $uri_parts['uri'], $regex_parts );
			if ( ! empty( $regex_parts[2] ) ) {
				$uri_parts['uri'] = $regex_parts[1];
				$amp_enabled      = true;
			}
		}

		return $uri_parts;
	}

	/**
	 * 1B. AMP hooks
	 *
	 * @param array $query
	 *
	 * @return array
	 */
	function enable_amp( $query ) {
		global $amp_enabled;

		if ( ! empty( $amp_enabled ) && defined( 'AMP_QUERY_VAR' ) ) {
			$query[ AMP_QUERY_VAR ] = 1;
		}

		return $query;
	}

	/**
	 * 2. AMP for WP hooks (support for older versions)
	 *
	 * @param array $query
	 *
	 * @return array
	 */
	function detect_amp_for_wp( $query ) {
		global $wp_rewrite, $pm_query;

		if ( defined( 'AMPFORWP_AMP_QUERY_VAR' ) ) {
			$amp_endpoint   = AMPFORWP_AMP_QUERY_VAR;
			$paged_endpoint = $wp_rewrite->pagination_base;

			if ( ! empty( $pm_query['endpoint'] ) && strpos( $pm_query['endpoint_value'], "{$paged_endpoint}/" ) !== false ) {
				$paged_val = preg_replace( "/({$paged_endpoint}\/)([\d]+)/", '$2', $pm_query['endpoint_value'] );

				if ( ! empty( $paged_val ) ) {
					$query[ $amp_endpoint ] = 1;
					$query['paged']         = $paged_val;
				}
			}
		}

		return $query;
	}

	/**
	 * 3A. Parse Custom Permalinks import
	 */
	public static function custom_permalinks_uris() {
		global $wpdb;

		$custom_permalinks_uris = array();

		// 1. List tags/categories
		$table = get_option( 'custom_permalink_table' );
		if ( $table && is_array( $table ) ) {
			foreach ( $table as $permalink => $info ) {
				$custom_permalinks_uris[] = array(
					'id'  => "tax-" . $info['id'],
					'uri' => trim( $permalink, "/" )
				);
			}
		}

		// 2. List posts/pages
		$query = "SELECT p.ID, m.meta_value FROM $wpdb->posts AS p LEFT JOIN $wpdb->postmeta AS m ON (p.ID = m.post_id)  WHERE m.meta_key = 'custom_permalink' AND m.meta_value != '';";
		$posts = $wpdb->get_results( $query );
		foreach ( $posts as $post ) {
			$custom_permalinks_uris[] = array(
				'id'  => $post->ID,
				'uri' => trim( $post->meta_value, "/" ),
			);
		}

		return $custom_permalinks_uris;
	}

	/**
	 * 3B. Import the URIs from the Custom Permalinks plugin.
	 */
	static public function import_custom_permalinks_uris() {
		global $permalink_manager_uris, $permalink_manager_before_sections_html;

		$custom_permalinks_plugin = 'custom-permalinks/custom-permalinks.php';

		if ( is_plugin_active( $custom_permalinks_plugin ) && ! empty( $_POST['disable_custom_permalinks'] ) ) {
			deactivate_plugins( $custom_permalinks_plugin );
		}

		// Get a list of imported URIs
		$custom_permalinks_uris = self::custom_permalinks_uris();

		if ( ! empty( $custom_permalinks_uris ) && count( $custom_permalinks_uris ) > 0 ) {
			foreach ( $custom_permalinks_uris as $item ) {
				$permalink_manager_uris[ $item['id'] ] = Permalink_Manager_Helper_Functions::sanitize_title( $item['uri'] );
			}

			$permalink_manager_before_sections_html .= Permalink_Manager_Admin_Functions::get_alert_message( __( '"Custom Permalinks" URIs were imported!', 'permalink-manager' ), 'updated' );
			update_option( 'permalink-manager-uris', $permalink_manager_uris );
		} else {
			$permalink_manager_before_sections_html .= Permalink_Manager_Admin_Functions::get_alert_message( __( 'No "Custom Permalinks" URIs were imported!', 'permalink-manager' ), 'error' );
		}
	}

	/**
	 * 4A. Fix query on WooCommerce shop page & disable the canonical redirect if WooCommerce query variables are set
	 */
	function woocommerce_detect( $query, $old_query, $uri_parts, $pm_query, $content_type ) {
		global $woocommerce, $pm_query;

		$shop_page_id = get_option( 'woocommerce_shop_page_id' );

		// WPML - translate shop page id
		$shop_page_id = apply_filters( 'wpml_object_id', $shop_page_id, 'page', true );

		// Fix shop page
		if ( get_theme_support( 'woocommerce' ) && ! empty( $pm_query['id'] ) && is_numeric( $pm_query['id'] ) && $shop_page_id == $pm_query['id'] ) {
			$query['post_type'] = 'product';
			unset( $query['pagename'] );
		}

		// Fix WooCommerce pages
		if ( ! empty( $woocommerce->query->query_vars ) ) {
			$query_vars = $woocommerce->query->query_vars;

			foreach ( $query_vars as $key => $val ) {
				if ( isset( $query[ $key ] ) ) {
					$query['do_not_redirect'] = 1;
					break;
				}
			}
		}

		return $query;
	}

	/**
	 * 4B. Redirects the user from the Shop archive to the Shop page if the user is not searching for anything
	 * Disable canonical redirect on "thank you" & another WooCommerce pages
	 */
	function woocommerce_checkout_fix() {
		global $wp_query, $pm_query, $permalink_manager_options;

		// Redirect from Shop archive to selected page
		if ( is_shop() && empty( $pm_query['id'] ) ) {
			$redirect_mode = ( ! empty( $permalink_manager_options['general']['redirect'] ) ) ? $permalink_manager_options['general']['redirect'] : false;
			$redirect_shop = apply_filters( 'permalink_manager_redirect_shop_archive', false );
			$shop_page     = get_option( 'woocommerce_shop_page_id' );

			if ( $redirect_mode && $redirect_shop && $shop_page && empty( $wp_query->query_vars['s'] ) ) {
				$shop_url = get_permalink( $shop_page );
				wp_safe_redirect( $shop_url, $redirect_mode );
				exit();
			}
		}

		if ( is_checkout() || ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) ) {
			$wp_query->query_vars['do_not_redirect'] = 1;
		}
	}

	/**
	 * 4C. Generate a new custom permalink for duplicated product
	 *
	 * @param WC_Product $new_product The new product object.
	 * @param WC_Product $old_product The product that was duplicated.
	 */
	function woocommerce_generate_permalinks_after_duplicate( $new_product, $old_product ) {
		if ( ! empty( $new_product ) ) {
			$product_id = $new_product->get_id();

			// Ignore variations
			if ( $new_product->get_type() !== 'variation' ) {
				$custom_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $product_id, false, true );

				Permalink_Manager_URI_Functions::save_single_uri( $product_id, $custom_uri, false, true );
			}
		}
	}

	/**
	 * 4D. If the URI contains %pa_attribute_name% tag, replace it with the value of the attribute
	 *
	 * @param string $default_uri The default custom permalink that WordPress would use for the post.
	 * @param string $slug The post slug.
	 * @param WP_Post $post The post object.
	 * @param string $post_name The post slug.
	 * @param bool $native_uri true if the URI is a native URI, false if it's a custom URI
	 *
	 * @return string The default custom permalink
	 */
	function woocommerce_product_attributes( $default_uri, $slug, $post, $post_name, $native_uri ) {
		// Do not affect native URIs
		if ( $native_uri ) {
			return $default_uri;
		}

		// Use only for products
		if ( empty( $post->post_type ) || $post->post_type !== 'product' ) {
			return $default_uri;
		}

		preg_match_all( "/%pa_(.[^\%]+)%/", $default_uri, $custom_fields );

		if ( ! empty( $custom_fields[1] ) ) {
			$product = wc_get_product( $post->ID );

			foreach ( $custom_fields[1] as $i => $custom_field ) {
				$attribute_name  = sanitize_title( $custom_field );
				$attribute_value = $product->get_attribute( $attribute_name );

				$default_uri = str_replace( $custom_fields[0][ $i ], Permalink_Manager_Helper_Functions::sanitize_title( $attribute_value ), $default_uri );
			}
		}

		return $default_uri;
	}

	/**
	 * 4E. Check the current request is a WooCommerce AJAX request. If it is, check the translated page's URL should be returned
	 *
	 * @param string $permalink The full URL of the post
	 * @param WP_Post $post The post object
	 * @param string $old_permalink The original URL of the post.
	 *
	 * @return string The permalink is being returned.
	 */
	function woocommerce_translate_ajax_fragments_urls( $permalink, $post, $old_permalink ) {
		// Use it only if the permalinks are different
		if ( $permalink == $old_permalink || $post->post_type !== 'page' ) {
			return $permalink;
		}

		// A. Native WooCommerce AJAX events
		if ( ! empty( $_REQUEST['wc-ajax'] ) ) {
			$action = sanitize_title( $_REQUEST['wc-ajax'] );
		} // B. Shoptimizer theme
		else if ( ! empty( $_REQUEST['action'] ) ) {
			$action = sanitize_title( $_REQUEST['action'] );
		}

		// Allowed action names
		$allowed_actions = array( 'shoptimizer_pdp_ajax_atc', 'get_refreshed_fragments' );

		if ( ! empty( $action ) && in_array( $action, $allowed_actions ) ) {
			$translated_post_id = apply_filters( 'wpml_object_id', $post->ID, 'page' );
			$permalink          = ( $translated_post_id !== $post->ID ) ? get_permalink( $translated_post_id ) : $permalink;
		}

		return $permalink;
	}

	/**
	 * 4FA. Add a new column to the WooCommerce CSV Import/Export tool
	 *
	 * @param array $columns The array of columns to be displayed.
	 *
	 * @return array The $columns array.
	 */
	function woocommerce_csv_custom_uri_column( $columns ) {
		if ( ! is_array( $columns ) ) {
			return $columns;
		}

		$label = __( 'Custom URI', 'permalink-manager' );
		$key   = 'custom_uri';

		if ( current_filter() == 'woocommerce_csv_product_import_mapping_default_columns' ) {
			$columns[ $label ] = $key;
		} else {
			$columns[ $key ] = $label;
		}

		return $columns;
	}

	/**
	 * 4FB. Return the custom permalink of the product if it exists, otherwise return the default URI
	 *
	 * @param string $value The value of the column.
	 * @param WC_Product $product The product object.
	 * @param mixed $column_id The column ID.
	 *
	 * @return string The custom permalink or default permalink
	 */
	function woocommerce_export_custom_uri_value( $value, $product, $column_id ) {
		if ( empty( $value ) && ! empty( $product ) ) {
			$product_id = $product->get_id();

			// Get custom permalink or default permalink
			$value = Permalink_Manager_URI_Functions_Post::get_post_uri( $product_id );
		}

		return $value;
	}

	/**
	 * 4FC. Set the custom URI for the product using the value from CSV file, if not set use the default permalink
	 *
	 * @param WC_Product $product The product object.
	 * @param array $data The data array for the current row being imported.
	 */
	function woocommerce_csv_import_custom_uri( $product, $data ) {
		global $permalink_manager_uris;

		if ( ! empty( $product ) ) {
			$product_id = $product->get_id();

			// Ignore variations
			if ( $product->get_type() == 'variation' ) {
				return;
			}

			// A. Use default permalink if "Custom URI" is not set and did not exist before
			if ( empty( $permalink_manager_uris[ $product_id ] ) && empty( $data['custom_uri'] ) ) {
				$custom_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $product_id, false, true );
			} else if ( ! empty( $data['custom_uri'] ) ) {
				$custom_uri = Permalink_Manager_Helper_Functions::sanitize_title( $data['custom_uri'] );
			} else {
				return;
			}

			Permalink_Manager_URI_Functions::save_single_uri( $product_id, $custom_uri, false, true );
		}
	}

	/**
	 * 5. Do not use custom permalinks if any action is triggered inside Theme My Login plugin
	 */
	function tml_ignore_custom_permalinks() {
		global $wp, $permalink_manager_ignore_permalink_filters;

		if ( isset( $wp->query_vars['action'] ) || ! empty( $_GET['redirect_to'] ) ) {
			$permalink_manager_ignore_permalink_filters = true;

			// Allow the canonical redirect (if blocked earlier by Permalink Manager)
			if ( ! empty( $wp_query->query_vars['do_not_redirect'] ) ) {
				$wp_query->query_vars['do_not_redirect'] = 0;
			}
		}
	}

	/**
	 * 6A. Get the HTTP protocol of the home URL and use it in Yoast SEO sitemap permalinks
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
	 * 6B. Update the permalink in the Yoast SEO indexable table when the permalink is changed
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
	 * 6C. Filter the canonical permalink used by SEO using 'wpseo_canonical' & 'wpseo_opengraph_url' hooks
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
	 * 7. Filter the breadcrumbs array to match the structure of currently requested URL
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

		// Check if the hook is called by AIOSEO plugin
		$is_aioseo = ( $current_filter == 'aioseo_breadcrumbs_trail' ) ? true : false;

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
				$term    = ( ( $element->term_id !== $term_id) || $is_aioseo ) ? get_term( $term_id ) : $element;

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
						'label'     => wp_strip_all_tags( $title ),
						'link'      => get_term_link( (int) $term->term_id, $term->taxonomy ),
						'type'      => 'taxonomy',
						'subType'   => 'parent',
						'reference' => $term,
					);
				} else {
					$breadcrumbs[] = array(
						'text' => wp_strip_all_tags( $title ),
						'url'  => get_term_link( (int) $term->term_id, $term->taxonomy )
					);
				}
			} // 2B. When the post/page is found, we can add it to the breadcrumbs
			else if ( ! empty( $element->ID ) ) {
				$page_id = apply_filters( 'wpml_object_id', $element->ID, $element->post_type, true );
				$page    = ( ($element->ID !== $page_id) || $is_aioseo ) ? get_post( $page_id ) : $element;

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
						'label'     => wp_strip_all_tags( $title ),
						'link'      => get_permalink( $page->ID ),
						'type'      => 'single',
						'subType'   => '',
						'reference' => $page
					);
				} else {
					$breadcrumbs[] = array(
						'text' => wp_strip_all_tags( $title ),
						'url'  => get_permalink( $page->ID )
					);
				}
			} // 2C. When the post archive is found, we can add it to the breadcrumbs
			else if ( ! empty( $element->rewrite ) && ( ! empty( $element->labels->name ) ) ) {
				if ( $is_aioseo ) {
					$breadcrumbs[] = array(
						'label'     => apply_filters( 'post_type_archive_title', $element->labels->name, $element->name ),
						'link'      => get_post_type_archive_link( $element->name ),
						'type'      => 'postTypeArchive',
						'subType'   => '',
						'reference' => $element
					);
				} else {
					$breadcrumbs[] = array(
						'text' => apply_filters( 'post_type_archive_title', $element->labels->name, $element->name ),
						'url'  => get_post_type_archive_link( $element->name )
					);
				}
			}
		}

		// Add new links to current breadcrumbs array
		if ( ! empty( $links ) && is_array( $links ) ) {
			$first_element = reset( $links );
			$last_element  = end( $links );
			$breadcrumbs   = ( ! empty( $breadcrumbs ) ) ? $breadcrumbs : array();

			// Support RankMath/SEOPress/WooCommerce/Slim SEO/AIOSEO breadcrumbs
			if ( in_array( $current_filter, array( 'wpseo_breadcrumb_links', 'rank_math/frontend/breadcrumb/items', 'seopress_pro_breadcrumbs_crumbs', 'woocommerce_get_breadcrumb', 'slim_seo_breadcrumbs_links', 'aioseo_breadcrumbs_trail' ) ) ) {
				foreach ( $breadcrumbs as &$breadcrumb ) {
					if ( isset( $breadcrumb['text'] ) ) {
						$breadcrumb[0] = $breadcrumb['text'];
						$breadcrumb[1] = $breadcrumb['url'];
					}
				}

				if ( $current_filter == 'slim_seo_breadcrumbs_links' ) {
					$links = array_merge( array( $first_element ), $breadcrumbs );
				} else {
					$links = array_merge( array( $first_element ), $breadcrumbs, array( $last_element ) );
				}
			} // Support Avia/Enfold breadcrumbs
			else if ( $current_filter == 'avia_breadcrumbs_trail' ) {
				foreach ( $breadcrumbs as &$breadcrumb ) {
					if ( isset( $breadcrumb['text'] ) ) {
						$breadcrumb = sprintf( '<a href="%s" title="%2$s">%2$s</a>', esc_attr( $breadcrumb['url'] ), esc_attr( $breadcrumb['text'] ) );
					}
				}

				if ( ! empty( $breadcrumbs ) ) {
					$links              = $breadcrumbs;
					$links['trail_end'] = $last_element;
				}
			}
		}

		return array_filter( $links );
	}

	/**
	 * 8. Extracts the Wishlist ID from the URI and add it to the $uri_parts array (WooCommerce Wishlist Plugin)
	 *
	 * @param array $uri_parts An array of the URI parts.
	 * @param string $request_url The URL that was requested.
	 * @param array $endpoints An array of all the endpoints that are currently registered.
	 *
	 * @return array The URI parts.
	 */
	function ti_woocommerce_wishlist_uris( $uri_parts, $request_url, $endpoints ) {
		global $permalink_manager_uris;

		$wishlist_pid = tinv_get_option( 'general', 'page_wishlist' );

		// Find the Wishlist page URI
		if ( is_numeric( $wishlist_pid ) && ! empty( $permalink_manager_uris[ $wishlist_pid ] ) ) {
			$wishlist_uri = preg_quote( $permalink_manager_uris[ $wishlist_pid ], '/' );

			// Extract the Wishlist ID
			preg_match( "/^({$wishlist_uri})\/([^\/]+)\/?$/", $uri_parts['uri'], $output_array );

			if ( ! empty( $output_array[2] ) ) {
				$uri_parts['uri']            = $output_array[1];
				$uri_parts['endpoint']       = 'tinvwlID';
				$uri_parts['endpoint_value'] = $output_array[2];
			}
		}

		return $uri_parts;
	}

	/**
	 * 9A. Copy the custom URI from original post and apply it to the new temp. revision post (Revisionize)
	 *
	 * @param int $old_id
	 * @param int $new_id
	 */
	function revisionize_keep_post_uri( $old_id, $new_id ) {
		global $permalink_manager_uris;

		if ( ! empty( $permalink_manager_uris[ $old_id ] ) ) {
			$permalink_manager_uris[ $new_id ] = $permalink_manager_uris[ $old_id ];

			update_option( 'permalink-manager-uris', $permalink_manager_uris );
		}
	}

	/**
	 * 9B. Copy the custom URI from revision post and apply it to the original post
	 *
	 * @param int $old_id
	 * @param int $new_id
	 */
	function revisionize_clone_uri( $old_id, $new_id ) {
		global $permalink_manager_uris;

		if ( ! empty( $permalink_manager_uris[ $new_id ] ) ) {
			$permalink_manager_uris[ $old_id ] = $permalink_manager_uris[ $new_id ];
			unset( $permalink_manager_uris[ $new_id ] );

			update_option( 'permalink-manager-uris', $permalink_manager_uris );
		}
	}

	/**
	 * 10A. Add a new section to the WP All Import interface
	 *
	 * @param string $content_type The type of content being imported
	 * @param array $current_values An array of the current values for the post
	 */
	function wpaiextra_uri_display( $content_type, $current_values ) {
		// Check if post type is supported
		if ( $content_type !== 'taxonomies' && Permalink_Manager_Helper_Functions::is_post_type_disabled( $content_type ) ) {
			return;
		}

		// Get custom URI format
		$custom_uri = ( ! empty( $current_values['custom_uri'] ) ) ? sanitize_text_field( $current_values['custom_uri'] ) : "";

		$html = '<div class="wpallimport-collapsed closed wpallimport-section">';
		$html .= '<div class="wpallimport-content-section">';
		$html .= sprintf( '<div class="wpallimport-collapsed-header"><h3>%s</h3></div>', __( 'Permalink Manager', 'permalink-manager' ) );
		$html .= '<div class="wpallimport-collapsed-content">';

		$html .= '<div class="template_input">';
		$html .= Permalink_Manager_Admin_Functions::generate_option_field( 'custom_uri', array( 'extra_atts' => 'style="width:100%; line-height: 25px;"', 'placeholder' => __( 'Custom URI', 'permalink-manager' ), 'value' => $custom_uri ) );
		$html .= wpautop( sprintf( __( 'If empty, a default permalink based on your current <a href="%s" target="_blank">permastructure settings</a> will be used.', 'permalink-manager' ), Permalink_Manager_Admin_Functions::get_admin_url( '&section=permastructs' ) ) );
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	/**
	 * 10BA. Add a new field to the list of WP All Import options
	 *
	 * @param array $all_options The array of all options that are currently set
	 *
	 * @return array The array of all options plus the custom_uri option
	 */
	function wpai_api_options( $all_options ) {
		return $all_options + array( 'custom_uri' => null );
	}

	/**
	 * 10BB. Add Permalink Manager plugin to the WP All Import API
	 *
	 * @param array $addons
	 *
	 * @return array
	 */
	function wpai_api_register( $addons ) {
		if ( empty( $addons[ PERMALINK_MANAGER_PLUGIN_SLUG ] ) ) {
			$addons[ PERMALINK_MANAGER_PLUGIN_SLUG ] = 1;
		}

		return $addons;
	}

	/**
	 * 10BC. Register function that parses Permalink Manager plugin data in WP All Import API data feed
	 *
	 * @param array $functions
	 *
	 * @return array
	 */
	function wpai_api_parse( $functions ) {
		$functions[ PERMALINK_MANAGER_PLUGIN_SLUG ] = array( $this, 'wpai_api_parse_function' );

		return $functions;
	}

	/**
	 * 10BD. Register function that saves Permalink Manager plugin data extracted from WP All Import API data feed
	 *
	 * @param array $functions
	 *
	 * @return array
	 */
	function wpai_api_import( $functions ) {
		$functions[ PERMALINK_MANAGER_PLUGIN_SLUG ] = array( $this, 'wpai_api_import_function' );

		return $functions;
	}

	/**
	 * 10C. Parse Permalink Manager plugin data in WP All Import API data feed
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	function wpai_api_parse_function( $data ) {
		extract( $data );

		$data        = array(); // parsed data
		$option_name = 'custom_uri';

		if ( ! empty( $import->options[ $option_name ] ) && class_exists( 'XmlImportParser' ) ) {
			$cxpath    = $xpath_prefix . $import->xpath;
			$tmp_files = array();

			if ( isset( $import->options[ $option_name ] ) && $import->options[ $option_name ] != '' ) {
				if ( $import->options[ $option_name ] == "xpath" ) {
					$data[ $option_name ] = XmlImportParser::factory( $xml, $cxpath, (string) $import->options['xpaths'][ $option_name ], $file )->parse();
				} else {
					$data[ $option_name ] = XmlImportParser::factory( $xml, $cxpath, (string) $import->options[ $option_name ], $file )->parse();
				}

				$tmp_files[] = $file;
			} else {
				$data[ $option_name ] = array_fill( 0, $count, "" );
			}

			foreach ( $tmp_files as $file ) {
				unlink( $file );
			}
		}

		return $data;
	}

	/**
	 * 10D. Save the Permalink Manager plugin data extracted from WP All Import API data feed
	 *
	 * @param array $importData
	 * @param array $parsedData
	 */
	function wpai_api_import_function( $importData, $parsedData ) {
		// Check if the array with $parsedData is not empty
		if ( empty( $parsedData ) || empty( $importData['post_type'] ) ) {
			return;
		}

		// Check if the imported elements are terms
		if ( $importData['post_type'] == 'taxonomies' ) {
			$is_term = true;
		} else if ( Permalink_Manager_Helper_Functions::is_post_type_disabled( $importData['post_type'] ) ) {
			return;
		}

		// Get the parsed custom URI
		$index = ( isset( $importData['i'] ) ) ? $importData['i'] : false;
		$pid   = ( ! empty( $importData['pid'] ) ) ? (int) $importData['pid'] : false;

		if ( isset( $index ) && ! empty( $pid ) && ! empty( $parsedData['custom_uri'][ $index ] ) ) {
			$new_uri = Permalink_Manager_Helper_Functions::sanitize_title( $parsedData['custom_uri'][ $index ] );

			if ( ! empty( $new_uri ) ) {
				if ( ! empty( $is_term ) ) {
					$default_uri = Permalink_Manager_URI_Functions_Tax::get_default_term_uri( $pid );
					$native_uri  = Permalink_Manager_URI_Functions_Tax::get_default_term_uri( $pid, true );
					$custom_uri  = Permalink_Manager_URI_Functions_Tax::get_term_uri( $pid, false, true );
					$old_uri     = ( ! empty( $custom_uri ) ) ? $custom_uri : $native_uri;

					if ( $new_uri !== $old_uri ) {
						Permalink_Manager_URI_Functions::save_single_uri( $pid, $new_uri, true, true );
						do_action( 'permalink_manager_updated_term_uri', $pid, $new_uri, $old_uri, $native_uri, $default_uri, $single_update = true, $uri_saved = true );
					}
				} else {
					$default_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $pid );
					$native_uri  = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $pid, true );
					$custom_uri  = Permalink_Manager_URI_Functions_Post::get_post_uri( $pid, false, true );
					$old_uri     = ( ! empty( $custom_uri ) ) ? $custom_uri : $native_uri;

					if ( $new_uri !== $old_uri ) {
						Permalink_Manager_URI_Functions::save_single_uri( $pid, $new_uri, false, true );
						do_action( 'permalink_manager_updated_post_uri', $pid, $new_uri, $old_uri, $native_uri, $default_uri, $single_update = true, $uri_saved = true );
					}
				}
			}
		}
	}

	/**
	 * 10E. Copy the external redirect from the "external_redirect" custom field to the data model used by Permalink Manager Pro
	 *
	 * @param int $pid The post ID of the post being imported.
	 */
	function wpai_save_redirects( $pid ) {
		$external_url = get_post_meta( $pid, '_external_redirect', true );
		$external_url = ( empty( $external_url ) ) ? get_post_meta( $pid, 'external_redirect', true ) : $external_url;

		if ( $external_url && class_exists( 'Permalink_Manager_Pro_Functions' ) ) {
			Permalink_Manager_Pro_Functions::save_external_redirect( $external_url, $pid );
		}
	}

	/**
	 * 10FA. Use the import ID to extract all the post IDs that were imported, then splits them into chunks and schedule a single regenerate permalink event for each chunk
	 *
	 * @param int $import_id The ID of the import.
	 */
	function wpai_schedule_regenerate_uris_after_xml_import( $import_id ) {
		global $wpdb;

		$post_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->prefix}pmxi_posts WHERE import_id = {$import_id}" );
		$chunks   = array_chunk( $post_ids, 200 );

		// Schedule URI regenerate and split into bulks
		foreach ( $chunks as $i => $chunk ) {
			wp_schedule_single_event( time() + ( $i * 30 ), 'wpai_regenerate_uris_after_import_event', array( $chunk ) );
		}
	}

	/**
	 * 10FB. Regenerate the custom permalinks for all imported posts
	 *
	 * @param array $post_ids An array of post IDs that were just imported.
	 */
	function wpai_regenerate_uris_after_import( $post_ids ) {
		global $permalink_manager_uris;

		if ( ! is_array( $post_ids ) ) {
			return;
		}

		foreach ( $post_ids as $id ) {
			if ( ! empty( $permalink_manager_uris[ $id ] ) ) {
				continue;
			}

			$post_object = get_post( $id );

			// Check if post is allowed
			if ( empty( $post_object->post_type ) || Permalink_Manager_Helper_Functions::is_post_excluded( $post_object, true ) ) {
				continue;
			}

			$default_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $id );
			Permalink_Manager_URI_Functions::save_single_uri( $id, $default_uri, false, false );
		}

		Permalink_Manager_URI_Functions::save_all_uris();
	}

	/**
	 * 11A. Add a new section to the WP All Export interface
	 *
	 * @param array $sections
	 *
	 * @return array
	 */
	function wpae_custom_uri_section( $sections ) {
		if ( is_array( $sections ) ) {
			$sections['permalink_manager'] = array(
				'title'   => __( 'Permalink Manager', 'permalink-manager' ),
				'content' => 'permalink_manager_fields'
			);
		}

		return $sections;
	}

	/**
	 * 11B. Add a new field to the "Permalink Manager" section of the WP All Export interface
	 *
	 * @param array $fields The fields to be displayed in the section.
	 *
	 * @return array
	 */
	function wpae_custom_uri_section_fields( $fields ) {
		if ( is_array( $fields ) ) {
			$fields['permalink_manager_fields'] = array(
				array(
					'label' => 'custom_uri',
					'name'  => 'Custom URI',
					'type'  => 'custom_uri'
				)
			);
		}

		return $fields;
	}

	/**
	 * 11C. Add a new column to the export file with the custom permalink for each post/term
	 *
	 * @param array $articles The array of articles to be exported.
	 * @param array $options an array of options for the export.
	 *
	 * @return array
	 */
	function wpae_export_custom_uri( $articles, $options ) {
		if ( ( ! empty( $options['selected_post_type'] ) && $options['selected_post_type'] == 'taxonomies' ) || ! empty( $options['is_taxonomy_export'] ) ) {
			$is_term = true;
		} else {
			$is_term = false;
		}

		foreach ( $articles as &$article ) {
			if ( ! empty( $article['id'] ) ) {
				$item_id = $article['id'];
			} else if ( ! empty( $article['ID'] ) ) {
				$item_id = $article['ID'];
			} else if ( ! empty( $article['Term ID'] ) ) {
				$item_id = $article['Term ID'];
			} else {
				continue;
			}

			if ( ! empty( $is_term ) ) {
				$article['Custom URI'] = Permalink_Manager_URI_Functions_Tax::get_term_uri( $item_id );
			} else {
				$article['Custom URI'] = Permalink_Manager_URI_Functions_Post::get_post_uri( $item_id );
			}
		}

		return $articles;
	}

	/**
	 * 12. Check if the "custom_uri" is not blacklisted in the "duplicate_post_blacklist" option and if it's not, clone the custom permalink of the original post to the new one
	 *
	 * @param int $new_post_id The ID of the newly created post.
	 * @param WP_Post $old_post The post object of the original post.
	 */
	function duplicate_custom_uri( $new_post_id, $old_post ) {
		global $permalink_manager_uris;

		$duplicate_post_blacklist  = get_option( 'duplicate_post_blacklist', false );
		$duplicate_custom_uri_bool = ( ! empty( $duplicate_post_blacklist ) && strpos( $duplicate_post_blacklist, 'custom_uri' ) !== false ) ? false : true;

		if ( ! empty( $old_post->ID ) && $duplicate_custom_uri_bool ) {
			$old_post_id = $old_post->ID;

			// Clone custom permalink (if set for cloned post/page)
			if ( ! empty( $permalink_manager_uris[ $old_post_id ] ) ) {
				$old_post_uri = $permalink_manager_uris[ $old_post_id ];
				$new_post_uri = preg_replace( '/(.+?)(\.[^\.]+$|$)/', '$1-2$2', $old_post_uri );

				$permalink_manager_uris[ $new_post_id ] = $new_post_uri;
				update_option( 'permalink-manager-uris', $permalink_manager_uris );
			}
		}
	}

	/**
	 * 13A. Replace in the default permalink format the unique permastructure tags available for My Listing theme
	 *
	 * @param string $default_uri The default permalink for the element.
	 * @param string $native_slug The native slug of the post type.
	 * @param WP_Post $element The post object.
	 * @param string $slug The slug of the post type.
	 * @param bool $native_uri
	 *
	 * @return string
	 */
	public function ml_listing_custom_fields( $default_uri, $native_slug, $element, $slug, $native_uri ) {
		// Use only for "listing" post type & custom permalink
		if ( empty( $element->post_type ) || $element->post_type !== 'job_listing' ) {
			return $default_uri;
		}

		// A1. Listing type
		if ( strpos( $default_uri, '%listing-type%' ) !== false || strpos( $default_uri, '%listing_type%' ) !== false ) {
			if ( class_exists( 'MyListing\Src\Listing' ) ) {
				$listing_type_post = MyListing\Src\Listing::get( $element );
				$listing_type      = ( is_object( $listing_type_post ) && ! empty( $listing_type_post->type ) ) ? $listing_type_post->type->get_permalink_name() : '';
			} else {
				$listing_type_slug = get_post_meta( $element->ID, '_case27_listing_type', true );
				$listing_type_post = get_page_by_path( $listing_type_slug, OBJECT, 'case27_listing_type' );

				if ( ! empty( $listing_type_post ) ) {
					$listing_type_post_settings = get_post_meta( $listing_type_post->ID, 'case27_listing_type_settings_page', true );
					$listing_type_post_settings = ( is_serialized( $listing_type_post_settings ) ) ? unserialize( $listing_type_post_settings ) : array();

					$listing_type = ( ! empty( $listing_type_post_settings['permalink'] ) ) ? $listing_type_post_settings['permalink'] : $listing_type_post->post_name;
				}
			}

			if ( ! empty( $listing_type ) ) {
				$default_uri = str_replace( array( '%listing-type%', '%listing_type%' ), Permalink_Manager_Helper_Functions::sanitize_title( $listing_type, true ), $default_uri );
			}
		}

		// A2. Listing type (slug)
		if ( strpos( $default_uri, '%listing-type-slug%' ) !== false || strpos( $default_uri, '%listing_type_slug%' ) !== false || strpos( $default_uri, '%case27_listing_type%' ) !== false ) {
			$listing_type = get_post_meta( $element->ID, '_case27_listing_type', true );

			if ( ! empty( $listing_type ) ) {
				$listing_type = Permalink_Manager_Helper_Functions::sanitize_title( $listing_type, true );
				$default_uri  = str_replace( array( '%listing-type-slug%', '%listing_type_slug%', '%case27_listing_type%' ), $listing_type, $default_uri );
			}
		}

		// B. Listing location
		if ( strpos( $default_uri, '%listing-location%' ) !== false || strpos( $default_uri, '%listing_location%' ) !== false ) {
			$listing_location = get_post_meta( $element->ID, '_job_location', true );

			if ( ! empty( $listing_location ) ) {
				$listing_location = Permalink_Manager_Helper_Functions::sanitize_title( $listing_location, true );
				$default_uri      = str_replace( array( '%listing-location%', '%listing_location%' ), $listing_location, $default_uri );
			}
		}

		// C. Listing region
		if ( strpos( $default_uri, '%listing-region%' ) !== false || strpos( $default_uri, '%listing_region%' ) !== false ) {
			$listing_region_terms = wp_get_object_terms( $element->ID, 'region' );
			$listing_region_term  = ( ! is_wp_error( $listing_region_terms ) && ! empty( $listing_region_terms ) && is_object( $listing_region_terms[0] ) ) ? Permalink_Manager_Helper_Functions::get_lowest_element( $listing_region_terms[0], $listing_region_terms ) : "";

			if ( ! empty( $listing_region_term ) ) {
				$listing_region = Permalink_Manager_Helper_Functions::get_term_full_slug( $listing_region_term, $listing_region_terms, 2 );
				$listing_region = Permalink_Manager_Helper_Functions::sanitize_title( $listing_region, true );

				$default_uri = str_replace( array( '%listing-region%', '%listing_region%' ), $listing_region, $default_uri );
			}
		}

		// D. Listing category
		if ( strpos( $default_uri, '%listing-category%' ) !== false || strpos( $default_uri, '%listing_category%' ) !== false ) {
			$listing_category_terms = wp_get_object_terms( $element->ID, 'job_listing_category' );
			$listing_category_term  = ( ! is_wp_error( $listing_category_terms ) && ! empty( $listing_category_terms ) && is_object( $listing_category_terms[0] ) ) ? Permalink_Manager_Helper_Functions::get_lowest_element( $listing_category_terms[0], $listing_category_terms ) : "";

			if ( ! empty( $listing_category_term ) ) {
				$listing_category = Permalink_Manager_Helper_Functions::get_term_full_slug( $listing_category_term, $listing_category_terms, 2 );
				$listing_category = Permalink_Manager_Helper_Functions::sanitize_title( $listing_category, true );

				$default_uri = str_replace( array( '%listing-category%', '%listing_category%' ), $listing_category, $default_uri );
			}
		}

		return $default_uri;
	}

	/**
	 * 13B. Set the default custom permalink for the listing item when it is created
	 *
	 * @param int $post_id
	 */
	function ml_set_listing_uri( $post_id ) {
		global $permalink_manager_uris;

		if ( ! empty( $permalink_manager_uris ) ) {
			$default_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $post_id );

			if ( $default_uri ) {
				$permalink_manager_uris[ $post_id ] = $default_uri;
				update_option( 'permalink-manager-uris', $permalink_manager_uris );
			}
		}
	}

	/**
	 * 13C. If the user is on a MyListing archive page submitting a job, redirect the user to the Explore Listings page
	 *
	 * @param array $query The query object.
	 * @param array $old_query The original query array.
	 *
	 * @return array The new query array is being returned if the explore_tab property is present. Otherwise, the original query is returned.
	 */
	function ml_detect_archives( $query, $old_query ) {
		if ( function_exists( 'mylisting_custom_taxonomies' ) && empty( $_POST['submit_job'] ) ) {
			$explore_page_id = get_option( 'options_general_explore_listings_page', false );
			if ( empty( $explore_page_id ) ) {
				return $query;
			}

			// 1. Set-up new query array variable
			$new_query = array(
				"page_id" => $explore_page_id
			);

			// 2A. Check if any custom MyListing taxonomy was detected
			$ml_taxonomies = mylisting_custom_taxonomies();

			if ( ! empty( $ml_taxonomies ) && is_array( $ml_taxonomies ) ) {
				$ml_taxonomies = array_keys( $ml_taxonomies );

				foreach ( $ml_taxonomies as $taxonomy ) {
					if ( ! empty( $query[ $taxonomy ] ) && empty( $_GET[ $taxonomy ] ) ) {
						$new_query["explore_tab"]         = $taxonomy;
						$new_query["explore_{$taxonomy}"] = $query['term'];
					}
				}
			}

			// 2b. Check if any MyListing query var was detected
			$ml_query_vars = array(
				'explore_tag'      => 'tags',
				'explore_region'   => 'regions',
				'explore_category' => 'categories'
			);

			foreach ( $ml_query_vars as $query_var => $explore_tab ) {
				if ( ! empty( $old_query[ $query_var ] ) && empty( $_GET[ $query_var ] ) ) {
					$new_query[ $query_var ]  = $old_query[ $query_var ];
					$new_query["explore_tab"] = $explore_tab;
				}
			}
		}

		return ( ! empty( $new_query["explore_tab"] ) ) ? $new_query : $query;
	}

	/**
	 * 14A. Add the bbPress endpoints to the list of endpoints that are supported by the plugin
	 *
	 * @param string $endpoints
	 * @param bool $all Whether to return all endpoints or just the bbPress ones
	 *
	 * @return string|array
	 */
	function bbpress_endpoints( $endpoints, $all = true ) {
		$bbpress_endpoints   = array();
		$bbpress_endpoints[] = bbp_get_edit_slug();

		return ( $all ) ? $endpoints . "|" . implode( "|", $bbpress_endpoints ) : $bbpress_endpoints;
	}

	/**
	 * 14B. If the query contains the edit endpoint, then set the appropriate bbPress query variable
	 */
	function bbpress_detect_endpoints() {
		global $wp_query;

		if ( ! empty( $wp_query->query ) ) {
			$edit_endpoint = bbp_get_edit_slug();

			if ( isset( $wp_query->query[ $edit_endpoint ] ) ) {
				if ( isset( $wp_query->query['forum'] ) ) {
					$wp_query->bbp_is_forum_edit = true;
				} else if ( isset( $wp_query->query['topic'] ) ) {
					$wp_query->bbp_is_topic_edit = true;
				} else if ( isset( $wp_query->query['reply'] ) ) {
					$wp_query->bbp_is_reply_edit = true;
				}
			}
		}
	}

	/**
	 * 15A. Add the endpoint "edit" used by Dokan to the endpoints array supported by Permalink Manager
	 *
	 * @param string $endpoints
	 *
	 * @return string
	 */
	function dokan_endpoints( $endpoints ) {
		return "{$endpoints}|edit|edit-account";
	}

	/**
	 * 15B. Check if the current page is a Dokan page, and if so, adjust the query variables to disable the canonical redirect
	 */
	function dokan_detect_endpoints() {
		global $post, $wp_query, $wp, $pm_query;

		// Check if Dokan is activated
		if ( ! function_exists( 'dokan_get_option' ) || is_admin() || empty( $pm_query['id'] ) ) {
			return;
		}

		// Get Dokan dashboard page id
		$dashboard_page = dokan_get_option( 'dashboard', 'dokan_pages' );

		// Stop the redirect
		if ( ! empty( $dashboard_page ) && ! empty( $post->ID ) && ( $post->ID == $dashboard_page ) ) {
			$wp->query_vars['do_not_redirect'] = 1;

			// Detect Dokan shortcode
			if ( empty( $pm_query['endpoint'] ) ) {
				$wp->query_vars['page'] = 1;
			} else if ( isset( $wp->query_vars['page'] ) ) {
				unset( $wp->query_vars['page'] );
			}
		}

		// 2. Support "Edit Product" pages
		if ( isset( $wp_query->query_vars['edit'] ) ) {
			$wp_query->query_vars['edit']            = 1;
			$wp_query->query_vars['do_not_redirect'] = 1;
		}
	}

	/**
	 * 16. Replace in the default permalink format the unique permastructure tags available for GeoDirectory plugin
	 *
	 * @param string $default_uri The default permalink for the element.
	 * @param string $native_slug The native slug of the post type.
	 * @param WP_Post $element The post object.
	 * @param string $slug The slug of the post type.
	 * @param bool $native_uri
	 *
	 * @return string
	 */
	public function geodir_custom_fields( $default_uri, $native_slug, $element, $slug, $native_uri ) {
		// Use only for GeoDirectory post types & custom permalinks
		if ( empty( $element->post_type ) || ( strpos( $element->post_type, 'gd_' ) === false ) || $native_uri || ! function_exists( 'geodir_get_post_info' ) ) {
			return $default_uri;
		}

		// Get place info
		$place_data = geodir_get_post_info( $element->ID );

		// A. Category
		if ( strpos( $default_uri, '%category%' ) !== false ) {
			$place_category_terms = wp_get_object_terms( $element->ID, 'gd_placecategory' );
			$place_category_term  = ( ! is_wp_error( $place_category_terms ) && ! empty( $place_category_terms ) && is_object( $place_category_terms[0] ) ) ? Permalink_Manager_Helper_Functions::get_lowest_element( $place_category_terms[0], $place_category_terms ) : "";

			if ( ! empty( $place_category_term ) && is_a( $place_category_term, 'WP_Term' ) ) {
				$place_category = Permalink_Manager_Helper_Functions::get_term_full_slug( $place_category_term, '', 2 );
				$place_category = Permalink_Manager_Helper_Functions::sanitize_title( $place_category, true );

				$default_uri = str_replace( '%category%', $place_category, $default_uri );
			}
		}

		// B. Country
		if ( strpos( $default_uri, '%country%' ) !== false && ! empty( $place_data->country ) ) {
			$place_country = Permalink_Manager_Helper_Functions::sanitize_title( $place_data->country, true );
			$default_uri   = str_replace( '%country%', $place_country, $default_uri );
		}

		// C. Region
		if ( strpos( $default_uri, '%region%' ) !== false && ! empty( $place_data->region ) ) {
			$place_region = Permalink_Manager_Helper_Functions::sanitize_title( $place_data->region, true );
			$default_uri  = str_replace( '%region%', $place_region, $default_uri );
		}

		// D. City
		if ( strpos( $default_uri, '%city%' ) !== false && ! empty( $place_data->city ) ) {
			$place_city  = Permalink_Manager_Helper_Functions::sanitize_title( $place_data->city, true );
			$default_uri = str_replace( '%city%', $place_city, $default_uri );
		}

		return $default_uri;
	}

	/**
	 * 17. Adjust the query if BasePress page is detected
	 *
	 * @param array $query The query object.
	 * @param array $old_query The original query array.
	 * @param array $uri_parts An array of the URI parts.
	 * @param array $pm_query
	 * @param string $content_type
	 *
	 * @return array
	 */
	function kb_adjust_query( $query, $old_query, $uri_parts, $pm_query, $content_type ) {
		$knowledgebase_options = get_option( 'basepress_settings' );
		$knowledgebase_page    = ( ! empty( $knowledgebase_options['entry_page'] ) ) ? $knowledgebase_options['entry_page'] : '';

		// A. Knowledgebase category
		if ( isset( $query['knowledgebase_cat'] ) && ! empty( $pm_query['id'] ) ) {
			$kb_category = $query['knowledgebase_cat'];

			$query = array(
				'post_type'           => 'knowledgebase',
				'knowledgebase_items' => $kb_category
			);

			// Disable the canonical redirect function included in BasePress
			add_filter( 'basepress_canonical_redirect', '__return_false' );
		} // B. Knowledgebase main page
		else if ( ! empty( $knowledgebase_page ) && ! empty( $pm_query['id'] ) && $pm_query['id'] == $knowledgebase_page ) {
			$query = array(
				'page_id' => $knowledgebase_page
			);
		}

		return $query;
	}

	/**
	 * 18. Detect the extra pages created by Ultimate Member plugin
	 *
	 * @param array $uri_parts
	 *
	 * @return array
	 */
	public function um_detect_extra_pages( $uri_parts ) {
		global $permalink_manager_uris;

		$request_url = trim( "{$uri_parts['uri']}/{$uri_parts['endpoint_value']}", "/" );
		$um_pages    = array(
			'user'    => 'um_user',
			'account' => 'um_tab',
		);

		// Detect UM permalinks
		foreach ( $um_pages as $um_page => $query_var ) {
			$um_page_id = UM()->config()->permalinks[ $um_page ];
			// Support for WPML/Polylang
			$um_page_id = ( ! empty( $uri_parts['lang'] ) ) ? apply_filters( 'wpml_object_id', $um_page_id, 'page', true, $uri_parts['lang'] ) : $um_page_id;

			if ( ! empty( $um_page_id ) && ! empty( $permalink_manager_uris[ $um_page_id ] ) ) {
				$user_page_uri = preg_quote( $permalink_manager_uris[ $um_page_id ], '/' );
				preg_match( "/^({$user_page_uri})\/([^\/]+)?$/", $request_url, $parts );

				if ( ! empty( $parts[2] ) ) {
					$uri_parts['uri']            = $parts[1];
					$uri_parts['endpoint']       = $query_var;
					$uri_parts['endpoint_value'] = Permalink_Manager_Helper_Functions::sanitize_title( $parts[2], null, null, false );
				}
			}
		}

		return $uri_parts;
	}

	/**
	 * 19. Keep the query strings appended to the product permalinks by WooCommerce Subscriptions
	 *
	 * @param string $permalink
	 * @param WP_Post $post
	 * @param string $old_permalink
	 *
	 * @return string
	 */
	function wcs_fix_subscription_links( $permalink, $post, $old_permalink ) {
		if ( ! empty( $post->post_type ) && $post->post_type == 'product' && strpos( $old_permalink, 'switch-subscription=' ) !== false ) {
			$query_arg = parse_url( $old_permalink, PHP_URL_QUERY );
			$permalink = "{$permalink}?{$query_arg}";
		}

		return $permalink;
	}

	/**
	 * 20. Excluding the LearnPress pages from Permalink Manager
	 *
	 * @param array $excluded_ids
	 *
	 * @return array
	 */
	function learnpress_exclude_pages( $excluded_ids ) {
		if ( is_array( $excluded_ids ) && function_exists( 'learn_press_get_page_id' ) ) {
			$learnpress_pages = array( 'profile', 'courses', 'checkout', 'become_a_teacher' );

			foreach ( $learnpress_pages as $page ) {
				$learnpress_page_id = learn_press_get_page_id( $page );

				if ( empty( $learnpress_page_id ) || in_array( $learnpress_page_id, $excluded_ids ) ) {
					continue;
				}

				$excluded_ids[] = $learnpress_page_id;
			}
		}

		return $excluded_ids;
	}

	/**
	 * 21. Regenerate the default permalink for the post after the custom permalink is imported by Store Locator - CSV Manager
	 *
	 * @param int $meta_id The ID of the metadata entry.
	 * @param int $post_id The ID of the post that the metadata is for.
	 * @param string $meta_key The meta key of the metadata being updated.
	 * @param mixed $meta_value The value of the meta key.
	 */
	public function wpsl_regenerate_after_import( $meta_id, $post_id, $meta_key, $meta_value ) {
		global $permalink_manager_uris;

		if ( strpos( $meta_key, 'wpsl_' ) !== false && isset( $_POST['wpsl_csv_import_nonce'] ) ) {
			$default_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri( $post_id );

			if ( $default_uri ) {
				$permalink_manager_uris[ $post_id ] = $default_uri;
				update_option( 'permalink-manager-uris', $permalink_manager_uris );
			}
		}
	}

}
